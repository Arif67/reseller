<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Topheader;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Log;


class TopHeaderController extends Controller
{


    public function index(Request $request)
    {
        $data = Topheader::orderBy('id', 'DESC')->get();
        return view('backEnd.header.index', compact('data'));
    }
    public function create()
    {

        return view('backEnd.header.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);
        try {
            if ($request->input('id')) {
                $data = Topheader::find($request->input('id'));
                if (!$data) {
                    throw new \Exception('Data not found.');
                }
            } else {
                $data = new Topheader();
            }

            $data->title = $request->input('title');
            $data->link = $request->input('link');
            $data->status = $request->input('status') ?? 0;

            if ($data->save()) {
                Toastr::success('Data ' . ($request->input('id') ? 'updated' : 'created') . ' successfully.', 'Success');
                return redirect()->route('header.index');
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
        $data = Topheader::find($id);
        return view('backEnd.header.edit', compact('data'));
    }

    public function inactive(Request $request)
    {
        $inactive = Topheader::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Topheader::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $data = Topheader::find($request->hidden_id);
        $data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}


