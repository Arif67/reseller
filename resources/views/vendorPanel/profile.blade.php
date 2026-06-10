@extends('vendorPanel.layouts.master')
@section('title', 'My Shop')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box"><h4 class="page-title">My Shop</h4></div>
        </div>
    </div>

    <div class="row">
        {{-- Edit profile --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Edit Shop Info</h4>
                    <form action="{{ route('vendor.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center mb-3">
                            <img src="{{ asset($vendor->image) }}" alt="" height="80" class="rounded-circle">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Shop Logo / Image</label>
                            <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                            @error('image')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Owner Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $vendor->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Shop Name <span class="text-danger">*</span></label>
                            <input type="text" name="shop_name" value="{{ old('shop_name', $vendor->shop_name) }}" class="form-control @error('shop_name') is-invalid @enderror" required>
                            @error('shop_name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $vendor->phone) }}" class="form-control @error('phone') is-invalid @enderror" required>
                                @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" value="{{ old('email', $vendor->email) }}" class="form-control @error('email') is-invalid @enderror">
                                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="2" class="form-control">{{ old('address', $vendor->address) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>

            {{-- Change password --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Change Password</h4>
                    <form action="{{ route('vendor.password.update') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark">Update Password</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Read-only admin-controlled --}}
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Account Status</h4>
                    <table class="table table-borderless mb-0">
                        <tr><th style="width:45%">Shop Slug</th><td>{{ $vendor->shop_slug }}</td></tr>
                        <tr><th>Commission Rate</th><td>{{ $vendor->commission_rate }}%</td></tr>
                        <tr><th>Status</th>
                            <td>
                                @if($vendor->status=='active')<span class="badge bg-success">Active</span>
                                @elseif($vendor->status=='pending')<span class="badge bg-warning">Pending</span>
                                @else<span class="badge bg-danger">Suspended</span>@endif
                            </td>
                        </tr>
                        <tr><th>Balance</th><td>৳ {{ number_format($vendor->balance, 2) }}</td></tr>
                    </table>
                    <p class="text-muted mt-2 mb-0"><small>Commission, status ar balance admin niyontron kore.</small></p>
                </div>
            </div>
        </div>
    </div>
@endsection
