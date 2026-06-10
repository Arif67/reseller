@extends('backEnd.layouts.master')
@section('title','Reseller Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col"><div class="page-title-box"><h4 class="page-title">{{ $reseller->name }}</h4></div></div>
        <div class="col-auto pt-2"><a href="{{ route('admin.resellers.index') }}" class="btn btn-light btn-sm">← Back</a></div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Reseller Info</h4>
                    <table class="table table-borderless mb-0">
                        <tr><th>Name</th><td>{{ $reseller->name }}</td></tr>
                        <tr><th>Business</th><td>{{ $reseller->business_name ?? '—' }}</td></tr>
                        <tr><th>Phone</th><td>{{ $reseller->phone }}</td></tr>
                        <tr><th>Email</th><td>{{ $reseller->email ?? '—' }}</td></tr>
                        <tr><th>Default Margin</th>
                            <td>@if($reseller->default_margin_type=='percent'){{ $reseller->default_margin_value }}%@else৳{{ $reseller->default_margin_value }}@endif</td></tr>
                        <tr><th>Status</th><td>
                            @if($reseller->status=='active')<span class="badge bg-success">Active</span>
                            @elseif($reseller->status=='pending')<span class="badge bg-warning">Pending</span>
                            @else<span class="badge bg-danger">Suspended</span>@endif
                        </td></tr>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Earnings</h4>
                    <table class="table table-borderless mb-0">
                        <tr><th>Total Orders</th><td>{{ $stats['total_orders'] }}</td></tr>
                        <tr><th>Pending Margin</th><td class="text-warning">৳ {{ number_format($stats['pending_margin'],0) }}</td></tr>
                        <tr><th>Earned Margin</th><td class="text-success">৳ {{ number_format($stats['delivered_margin'],0) }}</td></tr>
                        <tr><th>Withdrawn</th><td>৳ {{ number_format($stats['withdrawn'],0) }}</td></tr>
                        <tr><th>Balance</th><td class="fw-bold">৳ {{ number_format($stats['balance'],0) }}</td></tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Orders</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th>Invoice</th><th>Customer Pays</th><th>Margin</th><th>Status</th><th>Date</th></tr></thead>
                            <tbody>
                                @forelse($orders as $o)
                                    <tr>
                                        <td><a href="{{ route('admin.order.workspace', $o->invoice_id) }}">#{{ $o->invoice_id }}</a></td>
                                        <td>৳ {{ number_format($o->amount,0) }}</td>
                                        <td class="text-success">৳ {{ number_format($o->reseller_margin,0) }}</td>
                                        <td>{{ $o->order_status == 7 ? 'Delivered' : 'Status '.$o->order_status }}</td>
                                        <td>{{ $o->created_at->format('d M Y') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="text-center text-muted py-3">Kono order nei.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-2">{{ $orders->links('pagination::bootstrap-4') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
