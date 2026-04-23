@extends('backEnd.layouts.master')
@section('title','Users Edit')
@section('css')
<link href="{{asset('public/backEnd')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<style>
    .access-panel {
        margin-bottom: 1rem;
        padding: 1rem 1rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .access-panel-header {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 0.75rem;
        flex-wrap: wrap;
    }

    .access-panel-title {
        margin: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
    }

    .access-panel-copy {
        margin: 0.25rem 0 0;
        color: #64748b;
        font-size: 0.875rem;
    }

    .access-chip {
        display: inline-flex;
        align-items: center;
        padding: 0.3rem 0.65rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .access-note {
        margin-top: 0.8rem;
        padding: 0.75rem 0.9rem;
        border: 1px solid #dbe4f0;
        border-radius: 12px;
        background: #fff;
        color: #64748b;
        font-size: 0.82rem;
        line-height: 1.55;
    }

    .permission-groups {
        display: grid;
        gap: 0.75rem;
        margin-top: 0.75rem;
    }

    .permission-group {
        padding: 0.85rem;
        border: 1px solid #dbe4f0;
        border-radius: 14px;
        background: #fff;
    }

    .permission-group-title {
        margin: 0 0 0.5rem;
        font-size: 0.9rem;
        font-weight: 700;
        color: #0f172a;
        text-transform: capitalize;
    }

    .permission-tag-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 0.55rem;
    }

    .permission-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0.6rem 0.75rem;
        border-radius: 12px;
        background: #f8fafc;
        border: 1px solid #dbe4f0;
        color: #334155;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
    }

    .permission-checkbox:hover {
        border-color: #93c5fd;
        background: #eff6ff;
    }

    .permission-checkbox input {
        width: 16px;
        height: 16px;
        margin: 0;
        accent-color: #2563eb;
    }

    .permission-checkbox span {
        line-height: 1.4;
    }

    .permission-actions {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-top: 0.75rem;
    }
</style>
@endsection
@section('content')
@php
    $permissionGroups = $permissions->groupBy(function ($permission) {
        return explode('-', $permission->name)[0];
    });
@endphp
<div class="container-fluid">
    
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('users.index')}}" class="btn btn-primary rounded-pill">Manage</a>
                </div>
                <h4 class="page-title">Users Edit</h4>
            </div>
    </div>       
    <!-- end page title --> 
   <div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <form action="{{route('users.update')}}" method="POST" class=row data-parsley-validate=""  enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" value="{{$edit_data->id}}" name="hidden_id">
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ $edit_data->name}}" id="name" required="">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $edit_data->email}}"  id="email" required="">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col-end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" value="" id="password" >
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-sm-6">
                        <div class="form-group mb-3">
                            <label for="confirm-password" class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control @error('confirm-password') is-invalid @enderror" name="confirm-password" value=""  id="confirm-password" >
                            @error('confirm-password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <!-- col end -->
                    <div class="col-lg-6">
                        <div class="d-grid gap-3">
                            <div class="access-panel">
                                <div class="access-panel-header">
                                    <div>
                                        <h5 class="access-panel-title">Role Assignment</h5>
                                        <p class="access-panel-copy">Assign one or more roles. Role-based permissions will be inherited automatically.</p>
                                    </div>
                                    <span class="access-chip">{{ $roles->count() }} roles</span>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="roles" class="form-label">Role *</label>
                                     <select class="form-control select2-multiple js-role-select" name="roles[]" data-toggle="select2"  multiple="multiple" data-placeholder="Choose roles ..." required>
                                        <optgroup label="Select Role">
                                            @foreach($roles as $role)
                                            <option value="{{$role->name}}" @foreach($edit_data->roles as $srole) {{$srole->id==$role->id?'selected':''}} @endforeach>{{$role->name}}</option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                    @error('roles')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="access-note">
                                    Use roles as the primary access layer. You can assign multiple roles if this user needs access across more than one responsibility.
                                </div>
                            </div>
                            <div class="access-panel">
                                <div class="access-panel-header">
                                    <div>
                                        <h5 class="access-panel-title">Direct Permissions</h5>
                                        <p class="access-panel-copy">Optional. Use direct permissions only for special exceptions beyond the assigned roles.</p>
                                    </div>
                                    <span class="access-chip">{{ $permissions->count() }} permissions</span>
                                </div>
                                <div class="form-group mb-3">
                                    <label class="form-label d-block">Permission Override</label>
                                    <div class="permission-actions">
                                        <button type="button" class="btn btn-soft-primary btn-sm js-select-all-permissions">Select All</button>
                                        <button type="button" class="btn btn-soft-secondary btn-sm js-clear-permissions">Clear</button>
                                    </div>
                                    <div class="permission-groups">
                                        @foreach($permissionGroups as $group => $groupPermissions)
                                            <div class="permission-group">
                                                <h6 class="permission-group-title">{{ str_replace('_', ' ', $group) }}</h6>
                                                <div class="permission-tag-list">
                                                    @foreach($groupPermissions as $permission)
                                                        <label class="permission-checkbox">
                                                            <input type="checkbox" class="js-permission-checkbox" name="permissions[]" value="{{$permission->name}}" @checked(in_array($permission->name, old('permissions', $edit_data->permissions->pluck('name')->toArray())))>
                                                            <span>{{ $permission->name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('permissions')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    @error('permissions.*')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                    <!-- col end -->
                    <div class="col-lg-6 mb-3">
                        <div class="d-grid gap-3">
                            <div class="user-form-card">
                                @include('backEnd.category.partials.media-field', [
                                    'field' => 'image',
                                    'label' => 'User Image',
                                    'selectedMediaId' => old('image_media_id', $selectedImageMediaId ?? null),
                                    'currentUrl' => $edit_data->image ? asset(ltrim($edit_data->image, '/')) : '',
                                    'currentPath' => $edit_data->image ?? '',
                                ])
                            </div>
                        <div class="form-group user-status-card">
                            <label for="status" class="d-block">Status</label>
                            <p class="user-status-copy">Keep this enabled if the user should be able to sign in right after the update.</p>
                            <label class="switch">
                              <input type="checkbox" value="1" name="status" @if($edit_data->status==1)checked @endif>
                              <span class="slider round"></span>
                            </label>
                            @error('status')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        </div>
                    </div>
                    <!-- col end -->
                    <div>
                        <input type="submit" class="btn btn-success" value="Submit">
                    </div>

                </form>

                @include('backEnd.category.partials.media-picker-modal')

            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->
   </div>
</div>
@endsection


@section('script')
<script src="{{asset('public/backEnd/')}}/assets/libs/parsleyjs/parsley.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-validation.init.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{asset('public/backEnd/')}}/assets/js/pages/form-advanced.init.js"></script>
<script>
    $(document).ready(function () {
        $('.js-select-all-permissions').on('click', function () {
            $('.js-permission-checkbox').prop('checked', true);
        });

        $('.js-clear-permissions').on('click', function () {
            $('.js-permission-checkbox').prop('checked', false);
        });
    });
</script>
@endsection
