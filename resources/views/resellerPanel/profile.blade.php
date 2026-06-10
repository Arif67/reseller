@extends('resellerPanel.layouts.master')
@section('title', 'My Profile')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box"><h4 class="page-title">My Profile</h4></div>
        </div>
    </div>

    <div class="row">
        {{-- Edit profile --}}
        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Edit Profile</h4>
                    <form action="{{ route('reseller.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center mb-3">
                            <img src="{{ asset($reseller->image) }}" alt="" height="80" class="rounded-circle">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                            @error('image')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $reseller->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                            @error('name')<span class="invalid-feedback">{{ $message }}</span>@enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Business Name</label>
                            <input type="text" name="business_name" value="{{ old('business_name', $reseller->business_name) }}" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $reseller->phone) }}" class="form-control @error('phone') is-invalid @enderror" required>
                                @error('phone')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" value="{{ old('email', $reseller->email) }}" class="form-control @error('email') is-invalid @enderror">
                                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea name="address" rows="2" class="form-control">{{ old('address', $reseller->address) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </form>
                </div>
            </div>

            {{-- Change password --}}
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Change Password</h4>
                    <form action="{{ route('reseller.password.update') }}" method="POST">
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
                        <tr><th style="width:45%">Default Margin</th>
                            <td>
                                @if($reseller->default_margin_type == 'percent')
                                    {{ $reseller->default_margin_value }}%
                                @else
                                    ৳ {{ number_format($reseller->default_margin_value, 2) }}
                                @endif
                            </td>
                        </tr>
                        <tr><th>Status</th>
                            <td>
                                @if($reseller->status=='active')<span class="badge bg-success">Active</span>
                                @elseif($reseller->status=='pending')<span class="badge bg-warning">Pending</span>
                                @else<span class="badge bg-danger">Suspended</span>@endif
                            </td>
                        </tr>
                        <tr><th>Balance</th><td>৳ {{ number_format($reseller->balance, 2) }}</td></tr>
                    </table>
                    <p class="text-muted mt-2 mb-0"><small>Margin, status ar balance admin niyontron kore.</small></p>
                </div>
            </div>

            {{-- Payment Methods ekhon alada "Payment Methods" menu-te --}}
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="header-title mb-2">Payment Methods</h4>
                    <p class="text-muted">Payment method add/manage korte alada page-e jan.</p>
                    <a href="{{ route('reseller.payment_methods.index') }}" class="btn btn-outline-success btn-sm">
                        <i class="mdi mdi-credit-card"></i> Manage Payment Methods
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
