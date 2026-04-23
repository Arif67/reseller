<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\AppService\FileUploadService;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Toastr;
class UserController extends Controller
{
    public function __construct(
        private readonly FileUploadService $fileUploadService
    ) {
        $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index']]);
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update', 'active', 'inactive']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $data = User::with(['roles', 'permissions'])->orderBy('id','DESC')->get();
        return view('backEnd.users.index',compact('data'));
    }
    
    public function create()
    {
        $roles = Role::select('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('backEnd.users.create',compact('roles', 'permissions'));
    }
    
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif|max:2048|required_without:image_media_id',
            'image_media_id' => 'nullable|integer|exists:media,id|required_without:image',
        ]);
        $input = $request->all();
        unset($input['image_media_id']);
        $input['password'] = Hash::make($input['password']);
        $input['image'] = $request->hasFile('image')
            ? $this->fileUploadService->processAndUploadImage(
                $request->file('image'),
                'uploads/users',
                ['width' => 100, 'height' => 100, 'prefix' => 'user']
            )
            : Media::query()->whereKey($request->input('image_media_id'))->value('path');
        
        $user = User::create($input);
        $user->syncRoles($request->input('roles'));
        $user->syncPermissions($request->input('permissions', []));
        Toastr::success('Success','Data insert successfully');
        return redirect()->route('users.index');
    }
    
    public function edit($id)
    {
        $edit_data = User::find($id);
        $roles = Role::get();
        $permissions = Permission::orderBy('name')->get();
        $selectedImageMediaId = $edit_data?->image
            ? Media::query()->where('path', $edit_data->image)->value('id')
            : null;

        return view('backEnd.users.edit',compact('edit_data','roles', 'permissions', 'selectedImageMediaId'));
    }
    
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$request->hidden_id,
            'password' => 'same:confirm-password',
            'roles' => 'required',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif|max:2048',
            'image_media_id' => 'nullable|integer|exists:media,id',
        ]);
        
        $update_data = User::find($request->hidden_id);

        // new password
        $input = $request->all();
        unset($input['image_media_id']);
        if(!empty($input['password'])){ 
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));    
        }

        // new image
        $image = $request->file('image');
        if($image){
            $input['image'] = $this->fileUploadService->processAndUploadImage(
                $image,
                'uploads/users',
                ['width' => 100, 'height' => 100, 'prefix' => 'user']
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

        $update_data->syncRoles($request->input('roles'));
        $update_data->syncPermissions($request->input('permissions', []));
        Toastr::success('Success','Data update successfully');
        return redirect()->route('users.index');
    }
 
    public function inactive(Request $request)
    {
        $inactive = User::find($request->hidden_id);
        $inactive->status = 0;
        $inactive->save();
        Toastr::success('Success','Data inactive successfully');
        return redirect()->back();
    }
    public function active(Request $request)
    {
        $active = User::find($request->hidden_id);
        $active->status = 1;
        $active->save();
        Toastr::success('Success','Data active successfully');
        return redirect()->back();
    }
    public function destroy(Request $request)
    {

        $delete_data = User::find($request->hidden_id);
        $this->fileUploadService->deleteLocalIfNeeded($delete_data?->image);
        $delete_data->delete();
        Toastr::success('Success','Data delete successfully');
        return redirect()->back();
    }
}
