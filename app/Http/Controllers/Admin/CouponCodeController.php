<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CouponCode;
use App\Models\Media;
use App\Services\AppService\FileUploadService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class CouponCodeController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
        $this->middleware('permission:couponcode-list|couponcode-create|couponcode-edit|couponcode-delete', ['only' => ['index']]);
        $this->middleware('permission:couponcode-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:couponcode-edit', ['only' => ['edit', 'update', 'active', 'inactive']]);
        $this->middleware('permission:couponcode-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = CouponCode::orderBy('id','DESC')->get();
        return view('backEnd.couponcode.index',compact('data'));
    }
    public function create()
    {
        return view('backEnd.couponcode.create');
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'coupon_code' => 'required',
            'expiry_date' => 'required',
            'offer_type' => 'required',
            'amount' => 'required',
            'buy_amount' => 'required',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif|max:2048|required_without:image_media_id',
            'image_media_id' => 'nullable|integer|exists:media,id|required_without:image',
            'status' => 'required',
        ]);
        $input = $request->all();
        unset($input['image_media_id']);
        if ($request->hasFile('image')) {
            $input['image'] = $this->fileUploadService->processAndUploadImage(
                $request->file('image'),
                'uploads/couponcode',
                ['width' => 210, 'height' => 210, 'prefix' => 'coupon']
            );
        } else {
            $input['image'] = Media::query()->whereKey($request->input('image_media_id'))->value('path');
        }
        CouponCode::create($input);
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('couponcodes.index');
    }

    public function edit($id)
    {
        $edit_data = CouponCode::find($id);
        $selectedImageMediaId = $edit_data?->image
            ? Media::query()->where('path', $edit_data->image)->value('id')
            : null;

        return view('backEnd.couponcode.edit',compact('edit_data', 'selectedImageMediaId'));
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'coupon_code' => 'required',
            'expiry_date' => 'required',
            'offer_type' => 'required',
            'amount' => 'required',
            'buy_amount' => 'required',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif|max:2048',
            'image_media_id' => 'nullable|integer|exists:media,id',
        ]);
        $update_data = CouponCode::find($request->id);
        $input = $request->all();
        unset($input['image_media_id']);
        $image = $request->file('image');
        if($image){
            $input['image'] = $this->fileUploadService->processAndUploadImage(
                $image,
                'uploads/couponcode',
                ['width' => 210, 'height' => 210, 'prefix' => 'coupon']
            );
            $this->fileUploadService->deleteLocalIfNeeded($update_data->image);
        } elseif (filled($request->input('image_media_id'))) {
            $imagePath = Media::query()->whereKey($request->input('image_media_id'))->value('path');
            if ($imagePath && $imagePath !== $update_data->image) {
                $input['image'] = $imagePath;
                $this->fileUploadService->deleteLocalIfNeeded($update_data->image);
            } else {
                $input['image'] = $update_data->image;
            }
        }else{
            $input['image'] = $update_data->image;
        }
        $input['status'] = $request->status?1:0;
        $update_data->update($input);

        Toastr::success('Success','Data update successfully');
        return redirect()->route('couponcodes.index');
    }

    public function inactive(Request $request)
    {
        $inactive = CouponCode::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = CouponCode::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = CouponCode::find($request->hidden_id);
        $this->fileUploadService->deleteLocalIfNeeded($delete_data?->image);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
