@extends('backEnd.layouts.master')
@section('title','Incomplete Orders')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Incomplete Orders</h4>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Cart Items</th>
                            <th>Cart Total</th>
                            <th>Source</th>
                            <th>Last Activity</th>
                            <th>Products</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incompleteOrders as $lead)
                            @php
                                $items = collect(json_decode($lead->cart_payload ?: '[]', true));
                            @endphp
                            <tr>
                                <td>{{ $lead->name ?: '-' }}</td>
                                <td>{{ $lead->phone ?: '-' }}</td>
                                <td>{{ $lead->cart_count }}</td>
                                <td>৳ {{ number_format((float) $lead->cart_total, 2) }}</td>
                                <td>
                                    {{ $lead->utm_source ?: ($lead->status ?: '-') }}
                                </td>
                                <td>{{ optional($lead->last_activity_at)->format('d M Y h:i A') ?: '-' }}</td>
                                <td style="min-width: 260px;">
                                    @if($items->isNotEmpty())
                                        @foreach($items as $item)
                                            <div class="mb-1">
                                                {{ $item['name'] ?? 'Product' }}
                                                <small class="text-muted">x{{ $item['qty'] ?? 1 }}</small>
                                            </div>
                                        @endforeach
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No incomplete orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($incompleteOrders, 'links'))
                <div class="mt-3">
                    {{ $incompleteOrders->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
