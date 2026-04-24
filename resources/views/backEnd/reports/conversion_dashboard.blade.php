@extends('backEnd.layouts.master')
@section('title','Conversion Dashboard')
@section('css')
<style>
    .conversion-trend-chart {
        position: relative;
        min-height: 320px;
    }
</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Conversion Dashboard</h4>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">From</label>
                    <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">To</label>
                    <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('reports.conversion_dashboard') }}" class="btn btn-light w-100">Reset</a>
                    <a href="{{ route('reports.conversion_dashboard', array_filter(['date_from' => $dateFrom, 'date_to' => $dateTo, 'export' => 'csv'])) }}" class="btn btn-success w-100">CSV Export</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h5>Total Orders</h5>
                    <h3>{{ $orderMetrics['total_orders'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h5>Total Revenue</h5>
                    <h3>{{ number_format((float) $orderMetrics['total_revenue'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h5>Delivered Orders</h5>
                    <h3>{{ $orderMetrics['delivered_orders'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h5>Cancelled Orders</h5>
                    <h3>{{ $orderMetrics['cancelled_orders'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5>Delivered Rate</h5>
                    <h3>{{ number_format((float) $orderMetrics['delivered_rate'], 2) }}%</h3>
                    <div class="text-muted">Delivered / (Delivered + Cancelled)</div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5>Cancelled Rate</h5>
                    <h3>{{ number_format((float) $orderMetrics['cancelled_rate'], 2) }}%</h3>
                    <div class="text-muted">Cancelled / (Delivered + Cancelled)</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Status-wise Date Trend</h5>
                            <div class="text-muted small">Daily delivered vs cancelled count.</div>
                        </div>
                    </div>
                    <div class="conversion-trend-chart">
                        <canvas id="orderLifecycleTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Provider Purchase Summary</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Purchase Count</th>
                                    <th>Purchase Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseSummary as $summary)
                                <tr>
                                    <td>{{ ucfirst($summary->provider) }}</td>
                                    <td>{{ $summary->purchase_count }}</td>
                                    <td>{{ number_format((float) $summary->purchase_value, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">No purchase conversion log found yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Order Lifecycle Summary</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trackingSummary as $row)
                                <tr>
                                    <td>{{ str_replace('_', ' ', ucwords($row->event_name, '_')) }}</td>
                                    <td>{{ $row->total_events }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">No order lifecycle tracking data available yet.</td>
                                </tr>
                                @endforelse
                                @if($trackingSummary->isNotEmpty())
                                <tr class="table-light">
                                    <td>Delivered Rate</td>
                                    <td>{{ number_format((float) $orderMetrics['delivered_rate'], 2) }}%</td>
                                </tr>
                                <tr class="table-light">
                                    <td>Cancelled Rate</td>
                                    <td>{{ number_format((float) $orderMetrics['cancelled_rate'], 2) }}%</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Daily Status Trend Table</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Delivered</th>
                                    <th>Cancelled</th>
                                    <th>Total Outcome</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trackingTrend as $row)
                                <tr>
                                    <td>{{ \Illuminate\Support\Carbon::parse($row->event_date)->format('d M Y') }}</td>
                                    <td>{{ $row->delivered_count }}</td>
                                    <td>{{ $row->cancelled_count }}</td>
                                    <td>{{ (int) $row->delivered_count + (int) $row->cancelled_count }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">No date trend data found yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Event Summary</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Event</th>
                                    <th>Total Events</th>
                                    <th>Successful Events</th>
                                    <th>Total Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($providerSummary as $row)
                                <tr>
                                    <td>{{ ucfirst($row->provider) }}</td>
                                    <td>{{ $row->event_name }}</td>
                                    <td>{{ $row->total_events }}</td>
                                    <td>{{ $row->successful_events }}</td>
                                    <td>{{ number_format((float) $row->total_value, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No conversion data available yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 mt-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Recent Order Lifecycle Events</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Invoice</th>
                                    <th>Event</th>
                                    <th>Previous Status</th>
                                    <th>Current Status</th>
                                    <th>Source</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTrackingEvents as $event)
                                <tr>
                                    <td>{{ optional($event->created_at)->format('d M Y h:i A') }}</td>
                                    <td>{{ $event->invoice_id ?: optional($event->order)->invoice_id ?: '-' }}</td>
                                    <td>{{ str_replace('_', ' ', ucwords($event->event_name, '_')) }}</td>
                                    <td>{{ $event->previous_status_name ?: '-' }}</td>
                                    <td>{{ $event->current_status_name ?: '-' }}</td>
                                    <td>{{ $event->source ?: '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">No order lifecycle events found yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
@parent
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        var trendRows = @json($trackingTrend);
        var chartElement = document.getElementById('orderLifecycleTrendChart');

        if (!chartElement || !trendRows.length || typeof Chart === 'undefined') {
            return;
        }

        var labels = trendRows.map(function (row) {
            return row.event_date;
        });

        var deliveredData = trendRows.map(function (row) {
            return Number(row.delivered_count || 0);
        });

        var cancelledData = trendRows.map(function (row) {
            return Number(row.cancelled_count || 0);
        });

        new Chart(chartElement, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Delivered',
                        data: deliveredData,
                        borderColor: '#16a34a',
                        backgroundColor: 'rgba(22, 163, 74, 0.12)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Cancelled',
                        data: cancelledData,
                        borderColor: '#dc2626',
                        backgroundColor: 'rgba(220, 38, 38, 0.10)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    })();
</script>
@endsection
