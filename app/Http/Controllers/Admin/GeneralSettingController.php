<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\SocialMedia;
use App\Models\Contact;
use App\Models\CreatePage;
use App\Models\Media;
use App\Models\ShippingCharge;
use App\Models\OrderStatus;
use App\Services\AppService\FileUploadService;
use Illuminate\Http\Request;
use Toastr;
use Illuminate\Support\Facades\Cache;

class GeneralSettingController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
        $this->middleware('permission:setting-list|setting-create|setting-edit|setting-delete', ['only' => ['index']]);
        $this->middleware('permission:setting-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:setting-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:setting-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $show_data = GeneralSetting::orderBy('id', 'DESC')->get();
        $socialmedias = SocialMedia::orderBy('id', 'DESC')->get();
        $contacts = Contact::orderBy('id', 'DESC')->get();
        $pages = CreatePage::orderBy('id', 'DESC')->get();
        $shippingcharges = ShippingCharge::orderBy('id', 'DESC')->get();
        $orderstatuses = OrderStatus::orderBy('id', 'DESC')->get();

        return view('backEnd.settings.index', compact(
            'show_data',
            'socialmedias',
            'contacts',
            'pages',
            'shippingcharges',
            'orderstatuses'
        ));
    }
    public function create()
    {
        return view('backEnd.settings.create');
    }

    protected function clearSettingCaches(): void
    {
        Cache::forget('shared_view_data_v3');
        Cache::forget('upload_settings_v1');
    }

    public function store(Request $request)
    {

        // dd($request->all());
        $this->validate($request, [
            'name' => 'required',
            'white_logo' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,svg|max:4096|required_without:white_logo_media_id',
            'white_logo_media_id' => 'nullable|integer|exists:media,id|required_without:white_logo',
            'dark_logo' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,svg|max:4096',
            'dark_logo_media_id' => 'nullable|integer|exists:media,id',
            'favicon' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,svg,ico|max:4096|required_without:favicon_media_id',
            'favicon_media_id' => 'nullable|integer|exists:media,id|required_without:favicon',
            'status' => 'required',
        ]);

        $input = $request->all();
        unset($input['white_logo_media_id'], $input['dark_logo_media_id'], $input['favicon_media_id']);
        $input['vault_upload_enabled'] = $request->has('vault_upload_enabled') ? 1 : 0;
        $input['white_logo'] = $request->hasFile('white_logo')
            ? $this->fileUploadService->processAndUploadImage(
                $request->file('white_logo'),
                'uploads/settings',
                ['prefix' => 'white-logo']
            )
            : Media::query()->whereKey($request->input('white_logo_media_id'))->value('path');
        $input['dark_logo'] = $request->hasFile('dark_logo')
            ? $this->fileUploadService->processAndUploadImage(
                $request->file('dark_logo'),
                'uploads/settings',
                ['prefix' => 'dark-logo']
            )
            : (Media::query()->whereKey($request->input('dark_logo_media_id'))->value('path') ?: $input['white_logo']);
        $input['favicon'] = $request->hasFile('favicon')
            ? $this->fileUploadService->processAndUploadImage(
                $request->file('favicon'),
                'uploads/settings',
                ['width' => 32, 'height' => 32, 'prefix' => 'favicon']
            )
            : Media::query()->whereKey($request->input('favicon_media_id'))->value('path');
        $input['default_robots'] = $request->input('default_robots', 'index, follow');
        GeneralSetting::create($input);
        $this->clearSettingCaches();
        Toastr::success('Success', 'Data insert successfully');
        return redirect()->route('settings.index');
    }

    public function edit($id)
    {
        $edit_data = GeneralSetting::find($id);
        $selectedWhiteLogoMediaId = $edit_data?->white_logo
            ? Media::query()->where('path', $edit_data->white_logo)->value('id')
            : null;
        $selectedDarkLogoMediaId = $edit_data?->dark_logo
            ? Media::query()->where('path', $edit_data->dark_logo)->value('id')
            : null;
        $selectedFaviconMediaId = $edit_data?->favicon
            ? Media::query()->where('path', $edit_data->favicon)->value('id')
            : null;

        return view('backEnd.settings.edit', compact(
            'edit_data',
            'selectedWhiteLogoMediaId',
            'selectedDarkLogoMediaId',
            'selectedFaviconMediaId'
        ));
    }

    public function seo()
    {
        $seo_data = GeneralSetting::where('status', 1)->latest('id')->first() ?? GeneralSetting::latest('id')->first();

        if (! $seo_data) {
            Toastr::error('Error', 'Create general settings first before configuring SEO.');
            return redirect()->route('settings.create');
        }

        return view('backEnd.settings.seo', compact('seo_data'));
    }

    // public function update(Request $request)
    // {
    //     $this->validate($request, [
    //         'name' => 'required'
    //     ]);
    //     $update_data = GeneralSetting::find($request->id);
    //     $input = $request->all();
    //     // new white logo
    //     $image = $request->file('white_logo');
    //     if($image){
    //         // image with intervention
    //         $image = $request->file('white_logo');
    //         $name =  time().'-'.$image->getClientOriginalName();
    //         $name = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name);
    //         $name = strtolower(preg_replace('/\s+/', '-', $name));
    //         $uploadpath = 'uploads/settings/';
    //         $imageUrl = $uploadpath.$name;
    //         $img=Image::make($image->getRealPath());
    //         $img->encode('webp', 90);
    //         $width = '';
    //         $height = '';
    //         $img->height() > $img->width() ? $width=null : $height=null;
    //         $img->resize($width, $height);
    //         $img->save($imageUrl);
    //         $input['white_logo'] = $imageUrl;
    //     }else{
    //         $input['white_logo'] = $update_data->white_logo;
    //     }
    //     // new dark logo
    //     $image2 = $request->file('dark_logo');
    //     if($image2){
    //         // image with intervention
    //         $image2 = $request->file('dark_logo');
    //         $name2 =  time().'-'.$image2->getClientOriginalName();
    //         $name2 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name2);
    //         $name2 = strtolower(preg_replace('/\s+/', '-', $name2));
    //         $uploadpath2 = 'uploads/settings/';
    //         $image2Url = $uploadpath2.$name2;
    //         $img2=Image::make($image2->getRealPath());
    //         $img2->encode('webp', 90);
    //         $width2 = '';
    //         $height2 = '';
    //         $img2->height() > $img2->width() ? $width2=null : $height2=null;
    //         $img2->resize($width2, $height2);
    //         $img2->save($image2Url);
    //         $input['dark_logo'] = $image2Url;
    //     }else{
    //         $input['dark_logo'] = $update_data->dark_logo;
    //     }

    //     // new favicon image
    //     $image3 = $request->file('favicon');
    //     if($image3){
    //         $image3 = $request->file('favicon');
    //         $name3 =  time().'-'.$image3->getClientOriginalName();
    //         $name3 = preg_replace('"\.(jpg|jpeg|png|webp)$"', '.webp',$name3);
    //         $name3 = strtolower(preg_replace('/\s+/', '-', $name3));
    //         $uploadpath3 = 'uploads/settings/';
    //         $image3Url = $uploadpath3.$name3;
    //         $img3=Image::make($image3->getRealPath());
    //         $img3->encode('webp', 90);
    //         $width3 = 32;
    //         $height3 = 32;
    //         $img3->height() > $img3->width() ? $width3=null : $height3=null;
    //         $img3->resize($width3, $height3);
    //         $img3->save($image3Url);
    //         $input['favicon'] = $image3Url;
    //     }else{
    //         $input['favicon'] = $update_data->favicon;
    //     }
    //     $input['status'] = $request->status?1:0;
    //     $update_data->update($input);

    //     Toastr::success('Success','Data update successfully');
    //     return redirect()->route('settings.index');
    // }

    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255',
            'white_logo' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,svg|max:4096',
            'white_logo_media_id' => 'nullable|integer|exists:media,id',
            'dark_logo' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,svg|max:4096',
            'dark_logo_media_id' => 'nullable|integer|exists:media,id',
            'favicon' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif,svg,ico|max:4096',
            'favicon_media_id' => 'nullable|integer|exists:media,id',
        ]);

        try {
            $update_data = GeneralSetting::findOrFail($request->id);
            $input = $request->except([
                'white_logo',
                'dark_logo',
                'favicon',
                'white_logo_media_id',
                'dark_logo_media_id',
                'favicon_media_id',
            ]);

            // White Logo Upload
            if ($request->hasFile('white_logo')) {
                $this->fileUploadService->deleteLocalIfNeeded($update_data->white_logo);
                $input['white_logo'] = $this->fileUploadService->processAndUploadImage(
                    $request->file('white_logo'),
                    'uploads/settings',
                    ['prefix' => 'white-logo']
                );
            } elseif (filled($request->input('white_logo_media_id'))) {
                $imagePath = Media::query()->whereKey($request->input('white_logo_media_id'))->value('path');
                if ($imagePath && $imagePath !== $update_data->white_logo) {
                    $this->fileUploadService->deleteLocalIfNeeded($update_data->white_logo);
                    $input['white_logo'] = $imagePath;
                } else {
                    $input['white_logo'] = $update_data->white_logo;
                }
            } else {
                $input['white_logo'] = $update_data->white_logo;
            }

            // Dark Logo Upload
            if ($request->hasFile('dark_logo')) {
                $this->fileUploadService->deleteLocalIfNeeded($update_data->dark_logo);
                $input['dark_logo'] = $this->fileUploadService->processAndUploadImage(
                    $request->file('dark_logo'),
                    'uploads/settings',
                    ['prefix' => 'dark-logo']
                );
            } elseif (filled($request->input('dark_logo_media_id'))) {
                $imagePath = Media::query()->whereKey($request->input('dark_logo_media_id'))->value('path');
                if ($imagePath && $imagePath !== $update_data->dark_logo) {
                    $this->fileUploadService->deleteLocalIfNeeded($update_data->dark_logo);
                    $input['dark_logo'] = $imagePath;
                } else {
                    $input['dark_logo'] = $update_data->dark_logo;
                }
            } else {
                $input['dark_logo'] = $update_data->dark_logo;
            }

            // Favicon Upload
            if ($request->hasFile('favicon')) {
                $this->fileUploadService->deleteLocalIfNeeded($update_data->favicon);
                $input['favicon'] = $this->fileUploadService->processAndUploadImage(
                    $request->file('favicon'),
                    'uploads/settings',
                    ['width' => 32, 'height' => 32, 'prefix' => 'favicon']
                );
            } elseif (filled($request->input('favicon_media_id'))) {
                $imagePath = Media::query()->whereKey($request->input('favicon_media_id'))->value('path');
                if ($imagePath && $imagePath !== $update_data->favicon) {
                    $this->fileUploadService->deleteLocalIfNeeded($update_data->favicon);
                    $input['favicon'] = $imagePath;
                } else {
                    $input['favicon'] = $update_data->favicon;
                }
            } else {
                $input['favicon'] = $update_data->favicon;
            }

            // Set status
            $input['status'] = $request->has('status') ? 1 : 0;
            $input['vault_upload_enabled'] = $request->has('vault_upload_enabled') ? 1 : 0;
            $input['default_robots'] = $request->input('default_robots', 'index, follow');

            $update_data->update($input);
            $this->clearSettingCaches();

            Toastr::success('Success', 'Data updated successfully');
            return redirect()->back();
        } catch (\Exception $e) {
            \Log::error('Settings Update Failed: ' . $e->getMessage());
            Toastr::error('Error', 'Something went wrong while updating settings.');
            return redirect()->back()->withInput();
        }
    }

    public function seoUpdate(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|exists:general_settings,id',
            'meta_title' => 'nullable|string|max:255',
            'meta_tag' => 'nullable|string',
            'meta_description' => 'nullable|string',
            'google_site_verification' => 'nullable|string|max:255',
            'default_robots' => 'nullable|string|max:255',
        ]);

        $seoData = GeneralSetting::findOrFail($request->id);
        $seoData->update([
            'meta_title' => $request->meta_title,
            'meta_tag' => $request->meta_tag,
            'meta_description' => $request->meta_description,
            'google_site_verification' => $request->google_site_verification,
            'default_robots' => $request->input('default_robots', 'index, follow'),
        ]);

        $this->clearSettingCaches();

        Toastr::success('Success', 'SEO configuration updated successfully');
        return redirect()->route('seo.config.index');
    }




    public function inactive(Request $request)
    {
        $inactive = GeneralSetting::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        $this->clearSettingCaches();
        Toastr::success('Success', 'Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = GeneralSetting::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        $this->clearSettingCaches();
        Toastr::success('Success', 'Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data = GeneralSetting::find($request->hidden_id);
        $this->fileUploadService->deleteLocalIfNeeded($delete_data?->white_logo);
        $this->fileUploadService->deleteLocalIfNeeded($delete_data?->dark_logo);
        $this->fileUploadService->deleteLocalIfNeeded($delete_data?->favicon);
        $delete_data->delete();
        $this->clearSettingCaches();
        Toastr::success('Success', 'Data delete successfully');
        return redirect()->back();
    }
}
