<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BannerCategory;
use App\Models\Banner;
use App\Models\Media;
use App\Services\Admin\BannerService\BannerService;
use App\Http\Requests\Admin\BannerRequest;
use Toastr;

class BannerController extends Controller
{
    public function __construct(
        private BannerService $bannerService
    ) {
    }

    public function index(Request $request)
    {
        $data = Banner::orderBy('id', 'DESC')->with('category')->get();
        return view('backEnd.banner.index', compact('data'));
    }

    public function create()
    {
        $categories = BannerCategory::orderBy('id', 'DESC')->select('id', 'name')->get();
        return view('backEnd.banner.create', compact('categories'));
    }

    public function store(BannerRequest $request)
    {
        try {
            $image = $request->file('image');
            $imageUrl = $image
                ? $this->bannerService->uploadBannerImage(new Banner(), $image)
                : Media::query()->whereKey($request->input('image_media_id'))->value('path');

            $input = $request->validated();
            unset($input['image_media_id']);
            $input['image'] = $imageUrl;
            $input['status'] = $request->status ? 1 : 0;
            
            Banner::create($input);
            
            Toastr::success('Success', 'Data insert successfully');
            return redirect()->route('banners.index');
        } catch (\Exception $e) {
            Toastr::error('Error', 'Something went wrong: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $edit_data = Banner::find($id);
        $categories = BannerCategory::select('id', 'name')->get();
        $selectedImageMediaId = $edit_data?->image
            ? Media::query()->where('path', $edit_data->image)->value('id')
            : null;

        return view('backEnd.banner.edit', compact('edit_data', 'categories', 'selectedImageMediaId'));
    }

    public function update(BannerRequest $request)
    {
        try {
            $banner = Banner::findOrFail($request->id);
            $input = $request->validated();
            unset($input['image_media_id']);
            $image = $request->file('image');

            if ($image) {
                $this->bannerService->deleteLocalIfNeeded($banner->image);
                $input['image'] = $this->bannerService->uploadBannerImage($banner, $image);
            } elseif (filled($request->input('image_media_id'))) {
                $imagePath = Media::query()->whereKey($request->input('image_media_id'))->value('path');

                if ($imagePath && $imagePath !== $banner->image) {
                    $this->bannerService->deleteLocalIfNeeded($banner->image);
                    $input['image'] = $imagePath;
                }
            }

            $input['status'] = $request->status ? 1 : 0;
            $banner->update($input);

            Toastr::success('Success', 'Data update successfully');
            return redirect()->route('banners.index');
        } catch (\Exception $e) {
            Toastr::error('Error', 'Update failed: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function inactive(Request $request)
    {
        $inactive = Banner::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }

    public function active(Request $request)
    {
        $active = Banner::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        try {
            $delete_data = Banner::findOrFail($request->hidden_id);
            $this->bannerService->deleteLocalIfNeeded($delete_data->image);
            $delete_data->delete();
            
            Toastr::success('Success', 'Data delete successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error('Error', 'Delete failed');
            return redirect()->back();
        }
    }
}
