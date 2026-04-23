@extends('backEnd.layouts.master')
@section('title','UTM Campaign Report')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">UTM Campaign Report</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Campaign Summary</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>UTM Source</th>
                                    <th>UTM Medium</th>
                                    <th>UTM Campaign</th>
                                    <th>Lead Count</th>
                                    <th>Total Cart Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                <tr>
                                    <td>{{ $campaign->utm_source ?: '-' }}</td>
                                    <td>{{ $campaign->utm_medium ?: '-' }}</td>
                                    <td>{{ $campaign->utm_campaign ?: '-' }}</td>
                                    <td>{{ $campaign->lead_count }}</td>
                                    <td>{{ number_format((float) $campaign->total_value, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No UTM campaign data found yet.</td>
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
                    <h5 class="mb-3">Recent UTM Leads</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Source</th>
                                    <th>Medium</th>
                                    <th>Campaign</th>
                                    <th>Status</th>
                                    <th>Cart Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentLeads as $lead)
                                <tr>
                                    <td>{{ $lead->name ?: '-' }}</td>
                                    <td>{{ $lead->phone ?: '-' }}</td>
                                    <td>{{ $lead->utm_source ?: '-' }}</td>
                                    <td>{{ $lead->utm_medium ?: '-' }}</td>
                                    <td>{{ $lead->utm_campaign ?: '-' }}</td>
                                    <td>{{ $lead->status }}</td>
                                    <td>{{ number_format((float) $lead->cart_total, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No lead data available.</td>
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
