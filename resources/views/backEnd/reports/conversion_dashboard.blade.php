@extends('backEnd.layouts.master')
@section('title','Conversion Dashboard')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Conversion Dashboard</h4>
            </div>
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
    </div>

    <div class="row">
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
    </div>
</div>
@endsection
