@extends('backEnd.layouts.master')
@section('title','Zero Cost Audit')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Zero Cost Audit</h4>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Keyword</label>
                        <input type="text" class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="Invoice / product / name / phone">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Order Status</label>
                        <select class="form-control" name="status">
                            <option value="">All</option>
                            @foreach($orderStatuses as $status)
                                <option value="{{ $status->id }}" @selected((string) request('status') === (string) $status->id)>{{ $status->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Affected Lines</div><h4 class="mb-0">{{ $summary['line_count'] }}</h4></div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Affected Orders</div><h4 class="mb-0">{{ $summary['order_count'] }}</h4></div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Affected Qty</div><h4 class="mb-0">{{ $summary['qty_total'] }}</h4></div></div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm"><div class="card-body"><div class="text-muted small">Sales Value On Zero Cost Lines</div><h4 class="mb-0">{{ number_format((float) $summary['sales_total'], 2, '.', '') }}</h4></div></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="alert alert-warning">
                Ei report e je line gula dekha jacche, oi line gulor <code>purchase_price</code> 0 ba missing. Profit report e distortion hote pare.
            </div>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Source</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Sale Price</th>
                            <th>Purchase Price</th>
                            <th>Line Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($details as $detail)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.order.workspace', ['invoice_id' => $detail->order?->invoice_id, 'tab' => 'manage']) }}">
                                        #{{ $detail->order?->invoice_id }}
                                    </a>
                                </td>
                                <td>{{ $detail->created_at?->format('d M Y') }}</td>
                                <td>
                                    {{ $detail->order?->shipping?->name ?: 'Guest' }}
                                    <div class="small text-muted">{{ $detail->order?->shipping?->phone }}</div>
                                </td>
                                <td>
                                    @if($detail->order)
                                        <span class="badge {{ $detail->order->marketing_source_badge_class }}">{{ $detail->order->marketing_source_label }}</span>
                                    @endif
                                </td>
                                <td>{{ $detail->product_name }}</td>
                                <td>{{ $detail->qty }}</td>
                                <td>{{ number_format((float) $detail->sale_price, 2, '.', '') }}</td>
                                <td><span class="badge bg-danger">{{ number_format((float) $detail->purchase_price, 2, '.', '') }}</span></td>
                                <td>{{ number_format((float) $detail->sale_price * (int) $detail->qty, 2, '.', '') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No zero-cost line found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $details->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
