<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Value;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ValueController extends Controller
{
    public function index(Request $request)
    {
        $data = Value::orderBy('id', 'DESC')->get();
        return view('backEnd.value.index', compact('data'));
    }
    public function create()
    {
        $data['data'] = Attribute::all();
        return view('backEnd.value.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'attribute_id' => 'required',
        ]);

        try {
            if ($request->input('id')) {
                $data = Value::find($request->input('id'));
                if (!$data) {
                    throw new \Exception('Data not found.');
                }
            } else {
                $data = new Value();
            }
            $data->attribute_id = $request->input('attribute_id');
            $data->title = $request->input('title');
            $data->status = $request->input('status') ?? 0;

            if ($data->save()) {
                Toastr::success('Data ' . ($request->input('id') ? 'updated' : 'created') . ' successfully.', 'Success');
                return redirect()->route('value.index');
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
        $edit_data = Value::find($id);
        return view('backEnd.value.edit', compact('edit_data'));
    }

    public function inactive(Request $request)
    {
        $inactive = Value::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Value::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $data = Value::find($request->hidden_id);
        $data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'value_ids' => 'required|array|min:1',
            'value_ids.*' => 'integer|exists:values,id',
        ]);

        Value::whereIn('id', $request->value_ids)->delete();

        Toastr::success('Success', 'Selected values deleted successfully');
        return redirect()->back();
    }

}
