<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EcomPixel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Toastr;

class PixelsController extends Controller
{
    protected function deactivateOtherProviders(EcomPixel $pixel): void
    {
        if (! Schema::hasColumn('ecom_pixels', 'provider') || ! $pixel->status) {
            return;
        }

        EcomPixel::where('id', '!=', $pixel->id)
            ->where('provider', $pixel->provider)
            ->update(['status' => 0]);
    }

    protected function clearPixelCaches(): void
    {
        Cache::forget('shared_view_data_v3');
    }

    public function index(Request $request)
    {
        return redirect()->route('pixels.create');
    }
    public function create()
    {
        $facebookPixel = null;
        $tiktokPixel = null;

        if (Schema::hasColumn('ecom_pixels', 'provider')) {
            $facebookPixel = EcomPixel::where('provider', 'facebook')->latest('id')->first();
            $tiktokPixel = EcomPixel::where('provider', 'tiktok')->latest('id')->first();
        }

        return view('backEnd.pixels.create', compact('facebookPixel', 'tiktokPixel'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $provider = Schema::hasColumn('ecom_pixels', 'provider') ? ($request->input('provider') ?: 'facebook') : null;
        $input = $request->only(['name', 'provider', 'code', 'access_token', 'test_event_code']);
        $input['provider'] = $provider;
        $input['name'] = Schema::hasColumn('ecom_pixels', 'name') ? ($request->input('name') ?: 'Default Pixel') : null;
        $input['status'] = $request->boolean('status') ? 1 : 0;

        $pixel = null;

        if (Schema::hasColumn('ecom_pixels', 'provider')) {
            $pixel = EcomPixel::find($request->input('pixel_id'));

            if (! $pixel && $provider) {
                $pixel = EcomPixel::where('provider', $provider)->latest('id')->first();
            }
        }

        if ($pixel) {
            $pixel->update($input);
        } else {
            $pixel = EcomPixel::create($input);
        }

        $this->deactivateOtherProviders($pixel);
        $this->clearPixelCaches();

        Toastr::success('Success','Data insert successfully');
        return redirect()->route('pixels.create');
    }
    
    public function edit($id)
    {
        $edit_data =EcomPixel::find($id);
        return view('backEnd.pixels.edit',compact('edit_data'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);
        $update_data =EcomPixel::find($request->id);
        $input = $request->only(['name', 'provider', 'code', 'access_token', 'test_event_code']);
        if (! Schema::hasColumn('ecom_pixels', 'provider')) {
            unset($input['provider']);
        }
        if (! Schema::hasColumn('ecom_pixels', 'name')) {
            unset($input['name']);
        }
        $input['status'] = $request->boolean('status') ? 1 : 0;
        $update_data->update($input);
        $this->deactivateOtherProviders($update_data);
        $this->clearPixelCaches();

        Toastr::success('Success','Data update successfully');
        return redirect()->route('pixels.create');
    }
 
    public function inactive(Request $request)
    {
        $inactive =EcomPixel::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        $this->clearPixelCaches();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active =EcomPixel::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        $this->deactivateOtherProviders($active);
        $this->clearPixelCaches();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
        $delete_data =EcomPixel::find($request->hidden_id);
        $delete_data->delete();
        $this->clearPixelCaches();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
