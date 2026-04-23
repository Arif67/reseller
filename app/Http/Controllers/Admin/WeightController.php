<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Weight;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WeightController extends Controller
{
   public function index(Request $request)
    {
        $data = Weight::orderBy('id', 'DESC')->get();
        return view('backEnd.weight.index', compact('data'));
    }
    public function create()
    {
        return view('backEnd.weight.create');
    }

    public function store(Request $request)
    {
       // dd($request->all());
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        try {
            if ($request->input('id')) {
                $data = Weight::find($request->input('id'));
                if (!$data) {
                    throw new \Exception('Data not found.');
                }
            } else {
                $data = new Weight();
            }
            $data->title = $request->input('title');
            $data->status = $request->input('status') ?? 0;

            if ($data->save()) {
                Toastr::success('Data ' . ($request->input('id') ? 'updated' : 'created') . ' successfully.', 'Success');
                return redirect()->route('weight.index');
            } else {
                throw new \Exception('Failed to save data.');
            }
        } catch (\Exception $e) {
            Log::error('Data save failed: ' . $e->getMessage());
            Toastr::error($e->getMessage(), 'Error');
            return back();
        }
    }


    public function edit($id)
    {
        $data['data'] = Weight::find($id);
        return view('backEnd.weight.edit', $data);
    }

    public function inactive(Request $request)
    {
        $inactive = Weight::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Weight::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $data = Weight::find($request->hidden_id);
        $data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}
