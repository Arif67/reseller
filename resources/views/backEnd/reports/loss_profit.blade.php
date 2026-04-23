@extends('backEnd.layouts.master')
@section('title','Profit & Loss')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <form action="{{ route('admin.loss_profit.recalculate') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill">Recalculate Old Orders</button>
                    </form>
                </div>
                <h4 class="page-title">Profit & Loss Report</h4>
            </div>
        </div>
    </div>

    @if(! $supportsProfitColumns)
        <div class="alert alert-warning">
            Order profit fields are not migrated yet. This page is showing calculated fallback data. Run <code>php artisan migrate --force</code> to store per-order profit snapshots.
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Keyword</label>
                        <input type="text" class="form-control" name="keyword" value="{{ request('keyword') }}" placeholder="Invoice / name / phone">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Source</label>
                        <select class="form-control" name="source">
                            <option value="">All</option>
                            <option value="facebook" @selected(request('source') === 'facebook')>Facebook</option>
                            <option value="messenger" @selected(request('source') === 'messenger')>Messenger</option>
                            <option value="whatsapp" @selected(request('source') === 'whatsapp')>WhatsApp</option>
                            <option value="tiktok" @selected(request('source') === 'tiktok')>TikTok</option>
                            <option value="google" @selected(request('source') === 'google')>Google</option>
                            <option value="organic" @selected(request('source') === 'organic')>Organic</option>
                            <option value="admin" @selected(request('source') === 'admin')>Admin Panel</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Profit Status</label>
                        <select class="form-control" name="profit_status">
                            <option value="">All</option>
                            <option value="profit" @selected(request('profit_status') === 'profit')>Profit</option>
                            <option value="loss" @selected(request('profit_status') === 'loss')>Loss</option>
                            <option value="breakeven" @selected(request('profit_status') === 'breakeven')>Breakeven</option>
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
                    <div class="col-md-1 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Sales Revenue</div>
                    <h4 class="mb-0">{{ number_format((float) $summary['sales_revenue'], 2, '.', '') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Product Cost</div>
                    <h4 class="mb-0">{{ number_format((float) $summary['product_cost'], 2, '.', '') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Gross Profit</div>
                    <h4 class="mb-0">{{ number_format((float) $summary['gross_profit'], 2, '.', '') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Operating Expense</div>
                    <h4 class="mb-0">{{ number_format((float) $summary['operating_expense'], 2, '.', '') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Additional Order Cost</div>
                    <h4 class="mb-0">{{ number_format((float) $summary['additional_cost'], 2, '.', '') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Net Before Operating Expense</div>
                    <h4 class="mb-0">{{ number_format((float) $summary['net_profit_before_operating_expense'], 2, '.', '') }}</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Final Net Profit</div>
                    <h4 class="mb-0 {{ $summary['net_profit_after_operating_expense'] < 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format((float) $summary['net_profit_after_operating_expense'], 2, '.', '') }}
                    </h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Completed Orders</div>
                    <h4 class="mb-0">{{ $summary['completed_order_count'] }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Order-wise Profit</h5>
                        <span class="text-muted small">Completed orders only</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Source</th>
                                    <th>Sale</th>
                                    <th>Cost</th>
                                    <th>Extra Cost</th>
                                    <th>Net</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.order.workspace', ['invoice_id' => $order->invoice_id, 'tab' => 'manage']) }}">
                                                #{{ $order->invoice_id }}
                                            </a>
                                        </td>
                                        <td>{{ $order->created_at?->format('d M Y') }}</td>
                                        <td>
                                            {{ $order->shipping?->name ?: 'Guest' }}
                                            <div class="small text-muted">{{ $order->shipping?->phone }}</div>
                                        </td>
                                        <td><span class="badge {{ $order->marketing_source_badge_class }}">{{ $order->marketing_source_label }}</span></td>
                                        <td>{{ number_format((float) $order->amount, 2, '.', '') }}</td>
                                        <td>{{ number_format((float) $order->report_product_cost, 2, '.', '') }}</td>
                                        <td>{{ number_format((float) $order->report_additional_cost, 2, '.', '') }}</td>
                                        <td class="{{ $order->report_net_profit < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format((float) $order->report_net_profit, 2, '.', '') }}
                                        </td>
                                        <td>
                                            <span class="badge {{ $order->report_profit_status === 'profit' ? 'bg-success' : ($order->report_profit_status === 'loss' ? 'bg-danger' : 'bg-secondary') }}">
                                                {{ ucfirst($order->report_profit_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No completed orders found for this filter.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div>
                        {{ $orders->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Expense Breakdown</h5>
                    @if($expenseBreakdown->isEmpty())
                        <p class="text-muted mb-0">No expense data found for this filter.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Count</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenseBreakdown as $expense)
                                        <tr>
                                            <td>{{ $expense->category_name }}</td>
                                            <td>{{ $expense->expense_count }}</td>
                                            <td class="text-end">{{ number_format((float) $expense->total_amount, 2, '.', '') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
