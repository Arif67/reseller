@extends('backEnd.layouts.master')
@section('title','Vendor Manage')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box"><h4 class="page-title">Vendor Manage</h4></div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-2 mb-3" method="GET">
                        <div class="col-sm-4">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Name / phone / shop">
                        </div>
                        <div class="col-sm-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach(['pending','active','suspended'] as $s)
                                    <option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button class="btn btn-info">Filter</button>
                            <a href="{{ route('admin.vendors.index') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-centered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th><th>Shop</th><th>Owner</th><th>Phone</th>
                                    <th>Products</th><th>Commission</th><th>Status</th><th style="width:230px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($show_data as $vendor)
                                    <tr>
                                        <td>{{ $vendor->id }}</td>
                                        <td><a href="{{ route('admin.vendors.show', $vendor->id) }}">{{ $vendor->shop_name }}</a></td>
                                        <td>{{ $vendor->name }}</td>
                                        <td>{{ $vendor->phone }}</td>
                                        <td>{{ $vendor->products_count }}</td>
                                        <td>
                                            <form action="{{ route('admin.vendors.commission') }}" method="POST" class="d-flex" style="max-width:140px">
                                                @csrf
                                                <input type="hidden" name="hidden_id" value="{{ $vendor->id }}">
                                                <input type="number" step="0.01" min="0" max="100" name="commission_rate" value="{{ $vendor->commission_rate }}" class="form-control form-control-sm">
                                                <button class="btn btn-sm btn-secondary ms-1">%</button>
                                            </form>
                                        </td>
                                        <td>
                                            @if($vendor->status=='active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($vendor->status=='pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Suspended</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($vendor->status !== 'active')
                                                <form action="{{ route('admin.vendors.approve') }}" method="POST" class="d-inline">
                                                    @csrf<input type="hidden" name="hidden_id" value="{{ $vendor->id }}">
                                                    <button class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.vendors.suspend') }}" method="POST" class="d-inline">
                                                    @csrf<input type="hidden" name="hidden_id" value="{{ $vendor->id }}">
                                                    <button class="btn btn-sm btn-warning">Suspend</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.vendors.show', $vendor->id) }}" class="btn btn-sm btn-info">View</a>
                                            <form action="{{ route('admin.vendors.destroy') }}" method="POST" class="d-inline" onsubmit="return confirm('Delete vendor?')">
                                                @csrf<input type="hidden" name="hidden_id" value="{{ $vendor->id }}">
                                                <button class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="text-center text-muted py-4">Kono vendor nei.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">{{ $show_data->withQueryString()->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
