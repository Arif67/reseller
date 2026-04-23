<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Services\AppService\FileUploadService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AtrributeListController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
    }

    public function index(Request $request)
    {
        $data = Attribute::orderBy('id', 'DESC')->get();
        return view('backEnd.attribute.index', compact('data'));
    }
    public function create()
    {
        return view('backEnd.attribute.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        try {
            if ($request->input('id')) {
                $data = Attribute::find($request->input('id'));
                if (!$data) {
                    throw new \Exception('Data not found.');
                }
            } else {
                $data = new Attribute();
            }

            $data->title = $request->input('title');
            $data->status = $request->input('status') ?? 0;

            if ($data->save()) {
                Toastr::success('Data ' . ($request->input('id') ? 'updated' : 'created') . ' successfully.', 'Success');
                 return redirect()->route('attribute.index');
            } else {
                throw new \Exception('Failed to save data.');
            }
        } catch (\Exception $e) {
            Log::error('Data save failed: ' . $e->getMessage());
            Toastr::error($e->getMessage(), 'Error');
            return back();
        }
    }
    // public function store(Request $request)
    // {
    //    // dd($request->all());
    //     $this->validate($request, [
    //         'name' => 'required',
    //         'status' => 'required',
    //     ]);

    //     Attribute::create($input);
    //     Toastr::success('Success','Data insert successfully');
    //     return redirect()->route('attribute.index');
    // }

    public function edit($id)
    {
        $edit_data = Attribute::find($id);
        return view('backEnd.attribute.edit', compact('edit_data'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);
        $update_data = Attribute::find($request->id);
        $input = $request->all();
        $image = $request->file('image');
        if ($image) {
            $input['image'] = $this->fileUploadService->processAndUploadImage(
                $image,
                'uploads/attribute',
                ['width' => 210, 'height' => 210, 'prefix' => 'attribute']
            );
            $this->fileUploadService->deleteLocalIfNeeded($update_data->image);
        } else {
            $input['image'] = $update_data->image;
        }
        $input['status'] = $request->status ? 1 : 0;
        $update_data->update($input);

        Toastr::success('Success', 'Data update successfully');
        return redirect()->route('attribute.index');
    }

    public function inactive(Request $request)
    {
        $inactive = Attribute::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Attribute::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = Attribute::find($request->hidden_id);
        $delete_data->delete();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}
