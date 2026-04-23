@extends('backEnd.layouts.master')
@section('title','Visitor Analytics')

@section('content')
@php
    $mapRows = $districtSummary
        ->where('district_name', '!=', 'Unknown')
        ->map(fn ($row) => [$row->district_name, (int) $row->visitors])
        ->values();
@endphp
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Visitor Analytics</h4>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">From</label>
                    <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">District</label>
                    <select class="form-control" name="district">
                        <option value="">All Districts</option>
                        <option value="Unknown" @selected($selectedDistrict === 'Unknown')>Unknown</option>
                        @foreach($districtSummary->where('district_name', '!=', 'Unknown') as $row)
                            <option value="{{ $row->district_name }}" @selected($selectedDistrict === $row->district_name)>{{ $row->district_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary w-100">Filter</button>
                    <a href="{{ route('reports.visitor_analytics') }}" class="btn btn-light w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Total Visitors</div>
                    <h3 class="mb-0">{{ number_format($totals['visitors']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Total Page Views</div>
                    <h3 class="mb-0">{{ number_format($totals['page_views']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Districts Tracked</div>
                    <h3 class="mb-0">{{ number_format($totals['districts']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Bangladesh Visitor Map</h5>
                            <div class="text-muted small">Jekono district e click korle oi district-er visitor list filter hobe.</div>
                        </div>
                        @if($selectedDistrict)
                            <span class="badge bg-info text-dark">Selected: {{ $selectedDistrict }}</span>
                        @endif
                    </div>
                    <div id="visitor-analytics-map" style="height: 520px;">
                        @if($mapRows->isEmpty())
                            <div class="alert alert-light border mb-0">District-wise visitor data ekhono enough nai. Visitor tracking start hole map fill hobe.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="mb-3">District List</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>District</th>
                                    <th>Visitors</th>
                                    <th>Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($districtSummary as $row)
                                    <tr @if($selectedDistrict === $row->district_name) class="table-info" @endif>
                                        <td>
                                            <a href="{{ route('reports.visitor_analytics', array_filter(['district' => $row->district_name, 'date_from' => request('date_from'), 'date_to' => request('date_to')])) }}">
                                                {{ $row->district_name }}
                                            </a>
                                        </td>
                                        <td>{{ number_format($row->visitors) }}</td>
                                        <td>{{ number_format($row->page_views) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No visitor data found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h5 class="mb-1">Recent Visits</h5>
                    <div class="text-muted small">Selected district: {{ $selectedDistrict ?: 'All' }}</div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>District</th>
                            <th>Country</th>
                            <th>Device</th>
                            <th>Page</th>
                            <th>Views</th>
                            <th>Last Seen</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentVisits as $visit)
                            <tr>
                                <td>{{ $visit->district ?: 'Unknown' }}</td>
                                <td>{{ $visit->country ?: '-' }}</td>
                                <td>{{ ucfirst($visit->device_type ?: '-') }}</td>
                                <td style="min-width: 260px;">
                                    <div>{{ $visit->page_path ?: '-' }}</div>
                                    @if($visit->page_url)
                                        <div class="small text-muted text-break">{{ $visit->page_url }}</div>
                                    @endif
                                </td>
                                <td>{{ number_format($visit->view_count) }}</td>
                                <td>{{ optional($visit->last_seen_at)->format('d M Y h:i A') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No visitor data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($recentVisits, 'links'))
                <div class="mt-3">
                    {{ $recentVisits->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
@parent
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    (function () {
        var mapRows = @json($mapRows->values());
        var selectedDistrict = @json($selectedDistrict);
        var dateFrom = @json(request('date_from'));
        var dateTo = @json(request('date_to'));

        if (!mapRows.length || typeof google === 'undefined') {
            return;
        }

        google.charts.load('current', {
            packages: ['geochart']
        });

        google.charts.setOnLoadCallback(drawVisitorMap);

        function drawVisitorMap() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'District');
            data.addColumn('number', 'Visitors');
            data.addRows(mapRows);

            var chart = new google.visualization.GeoChart(document.getElementById('visitor-analytics-map'));
            var options = {
                region: 'BD',
                resolution: 'provinces',
                displayMode: 'markers',
                colorAxis: {
                    colors: ['#dbeafe', '#2563eb']
                },
                datalessRegionColor: '#eef2f7',
                defaultColor: '#dbeafe',
                backgroundColor: '#ffffff',
                legend: {
                    textStyle: {
                        color: '#475569'
                    }
                }
            };

            google.visualization.events.addListener(chart, 'select', function () {
                var selection = chart.getSelection();

                if (!selection.length) {
                    return;
                }

                var district = data.getValue(selection[0].row, 0);
                var params = new URLSearchParams(window.location.search);
                params.set('district', district);

                if (dateFrom) {
                    params.set('date_from', dateFrom);
                }

                if (dateTo) {
                    params.set('date_to', dateTo);
                }

                window.location.href = '{{ route('reports.visitor_analytics') }}?' + params.toString();
            });

            chart.draw(data, options);
        }
    })();
</script>
@endsection
