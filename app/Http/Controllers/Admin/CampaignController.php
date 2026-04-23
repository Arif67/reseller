<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignReview;
use App\Models\Product;
use App\Services\AppService\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Toastr;

class CampaignController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
    }

    public function index(Request $request)
    {
        $show_data = Campaign::orderBy('id','DESC')->get();
        return view('backEnd.campaign.index',compact('show_data'));
    }
    public function create()
    {
        $products = Product::where(['status'=>1])->select('id','name','status')->get();
        return view('backEnd.campaign.create',compact('products'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|integer|exists:products,id',
            'banner' => 'required|file|mimes:jpg,jpeg,png,webp,avif|max:4096',
            'short_description' => 'required',
            'description' => 'required',
            'why_chooseus' => 'required',
            'description_title' => 'required',
            'name' => 'required',
            'status' => 'required',
        ], [
            'product_id.required' => 'Please select a product for this campaign.',
            'product_id.exists' => 'The selected product was not found.',
            'banner.required' => 'Please upload a banner image.',
        ]);

        $input = $request->only([
            'product_id',
            'name',
            'short_description',
            'video',
            'description_title',
            'description',
            'why_chooseus',
            'status',
        ]);
        
        $input['slug'] = strtolower(Str::slug($request->name));
        if ($request->hasFile('banner')) {
            $input['banner'] = $this->fileUploadService->processAndUploadImage(
                $request->file('banner'),
                'uploads/campaign',
                ['prefix' => 'campaign-banner']
            );
        }
        $campaign = Campaign::create($input);

        $images = $request->file('image');
        if($images){
            foreach ($images as $image) {
                $pimage             = new CampaignReview();
                $pimage->campaign_id = $campaign->id;
                $pimage->image       = $this->fileUploadService->processAndUploadImage(
                    $image,
                    'uploads/campaign',
                    ['prefix' => 'campaign-review']
                );
                $pimage->save();
            }
            
        }       
        
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('campaign.index');
    }
    
    public function edit($id)
    {
        $edit_data = Campaign::with('images')->find($id);
        $products = Product::where(['status'=>1])->select('id','name','status')->get();
        return view('backEnd.campaign.edit',compact('edit_data','products'));
    }
    
    public function update(Request $request)
    { 
        $this->validate($request, [
            'product_id' => 'required|integer|exists:products,id',
            'banner' => 'nullable|file|mimes:jpg,jpeg,png,webp,avif|max:4096',
            'name' => 'required',
            'short_description' => 'required',
            'description' => 'required',
            'why_chooseus' => 'required',
            'description_title' => 'required',
            'status' => 'required',
        ], [
            'product_id.required' => 'Please select a product for this campaign.',
            'product_id.exists' => 'The selected product was not found.',
        ]);
        // image one
        $update_data = Campaign::find($request->hidden_id);
        $input = $request->only([
            'product_id',
            'name',
            'short_description',
            'video',
            'description_title',
            'description',
            'why_chooseus',
            'status',
        ]);
        
        $image1 = $request->file('banner');
        if($image1){
            $this->fileUploadService->deleteLocalIfNeeded($update_data->banner);
            $input['banner'] = $this->fileUploadService->processAndUploadImage(
                $image1,
                'uploads/campaign',
                ['prefix' => 'campaign-banner']
            );
        }else{
            $input['banner'] = $update_data->banner;
        }


        $input['slug'] = strtolower(Str::slug($request->name));
        $update_data = Campaign::find($request->hidden_id);
        $update_data->update($input);

        $images = $request->file('image');  
        if($images){
            foreach ($images as $image) {
                $pimage             = new CampaignReview();
                $pimage->campaign_id = $update_data->id;
                $pimage->image      = $this->fileUploadService->processAndUploadImage(
                    $image,
                    'uploads/campaign',
                    ['prefix' => 'campaign-review']
                );
                $pimage->save();
            }
        }

        Toastr::success('Success','Data update successfully');
        return redirect()->route('campaign.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = Campaign::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = Campaign::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {
       
        $delete_data = Campaign::with('images')->find($request->hidden_id);
        if ($delete_data) {
            $this->fileUploadService->deleteLocalIfNeeded($delete_data->banner);
            foreach ($delete_data->images as $image) {
                $this->fileUploadService->deleteLocalIfNeeded($image->image);
            }
        }
        $delete_data->delete();

        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
    public function imgdestroy(Request $request)
    { 
        $delete_data = CampaignReview::find($request->id);
        $this->fileUploadService->deleteLocalIfNeeded($delete_data->image);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
