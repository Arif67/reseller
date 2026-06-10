@extends('backEnd.layouts.master')
@section('title','Reseller Manage')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center">
        <div class="col">
            <div class="page-title-box"><h4 class="page-title">Reseller Manage</h4></div>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.resellers.withdrawals') }}" class="btn btn-primary">
                <i class="mdi mdi-cash-multiple"></i> Withdraw Requests
                @if($pendingWithdrawCount)<span class="badge bg-danger">{{ $pendingWithdrawCount }}</span>@endif
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="row g-2 mb-3" method="GET">
                        <div class="col-sm-4">
                            <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control" placeholder="Name / phone / business">
                        </div>
                        <div class="col-sm-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                @foreach(['pending','active','suspended'] as $s)
                                    <option value="{{ $s }}" @selected(request('status')==$s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <button class="btn btn-info">Filter</button>
                            <a href="{{ route('admin.resellers.index') }}" class="btn btn-light">Reset</a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-centered nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th><th>Name</th><th>Business</th><th>Phone</th>
                                    <th>Margin</th><th>Status</th><th style="width:230px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($show_data as $r)
                                    <tr>
                                        <td>{{ $r->id }}</td>
                                        <td><a href="{{ route('admin.resellers.show', $r->id) }}">{{ $r->name }}</a></td>
                                        <td>{{ $r->business_name ?? '—' }}</td>
                                        <td>{{ $r->phone }}</td>
                                        <td>
                                            <form action="{{ route('admin.resellers.margin') }}" method="POST" class="d-flex" style="max-width:200px">
                                                @csrf
                                                <input type="hidden" name="hidden_id" value="{{ $r->id }}">
                                                <input type="number" step="0.01" min="0" name="default_margin_value" value="{{ $r->default_margin_value }}" class="form-control form-control-sm">
                                                <select name="default_margin_type" class="form-control form-control-sm ms-1" style="width:80px">
                                                    <option value="percent" @selected($r->default_margin_type=='percent')>%</option>
                                                    <option value="flat" @selected($r->default_margin_type=='flat')>৳</option>
                                                </select>
                                                <button class="btn btn-sm btn-secondary ms-1"><i class="mdi mdi-content-save"></i></button>
                                            </form>
                                        </td>
                                        <td>
                                            @if($r->status=='active')<span class="badge bg-success">Active</span>
                                            @elseif($r->status=='pending')<span class="badge bg-warning">Pending</span>
                                            @else<span class="badge bg-danger">Suspended</span>@endif
                                        </td>
                                        <td>
                                            @if($r->status !== 'active')
                                                <form action="{{ route('admin.resellers.approve') }}" method="POST" class="d-inline">
                                                    @csrf<input type="hidden" name="hidden_id" value="{{ $r->id }}">
                                                    <button class="btn btn-sm btn-success">Approve</button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.resellers.suspend') }}" method="POST" class="d-inline">
                                                    @csrf<input type="hidden" name="hidden_id" value="{{ $r->id }}">
                                                    <button class="btn btn-sm btn-warning">Suspend</button>
                                                </form>
                                            @endif
                                            <a href="{{ route('admin.resellers.show', $r->id) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="text-center text-muted py-4">Kono reseller nei.</td></tr>
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
