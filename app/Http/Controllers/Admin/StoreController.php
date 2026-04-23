<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Store;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
       public function index(Request $request)
    {
        $data = Store::orderBy('id', 'DESC')->get();
        return view('backEnd.store.index', compact('data'));
    }
    public function create()
    {

        return view('backEnd.store.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        
        try {
            if ($request->input('id')) {
                $data = Store::find($request->input('id'));
                if (!$data) {
                    throw new \Exception('Data not found.');
                }
            } else {
                $data = new Store();
            }

            $data->title = $request->input('title');
            $data->description = $request->input('description');
            $data->status = $request->input('status') ?? 0;

            if ($data->save()) {
                Toastr::success('Data ' . ($request->input('id') ? 'updated' : 'created') . ' successfully.', 'Success');
                return redirect()->route('store.index');
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
        $data = Store::find($id);
        return view('backEnd.store.edit', compact('data'));
    }

    public function inactive(Request $request)
    {
        $inactive = Store::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Store::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $data = Store::find($request->hidden_id);
        $data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}
