@extends('backEnd.layouts.master')
@section('title','Dashboard')
@section('css')
@endsection
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Total Order -->
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-primary border-primary border">
                                <i class="fe-shopping-cart font-22 avatar-title text-primary"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('admin.orders',['slug'=>'all']) }}">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $total_order_count }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Total Order</p>
                                <small class="text-muted">Amount: {{ number_format($total_order_amount, 2) }}৳</small>
                            </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Order -->
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <a href="{{ route('admin.orders',['slug'=>'pending']) }}">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-warning border-warning border">
                                <i class="fe-clock font-22 avatar-title text-warning"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $pending_count }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Pending Order</p>
                                <small class="text-muted">Amount: {{ number_format($pending_amount, 2) }}৳</small>
                            </div>
                        </div>
                    </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Approved / Processing Order -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders',['slug'=>'processing']) }}">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                <i class="fe-check-circle font-22 avatar-title text-success"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $processing_count }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Approved Order</p>
                                <small class="text-muted">Amount: {{ number_format($processing_amount, 2) }}৳</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <!-- Packed / On The Way -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders',['slug'=>'on-the-way']) }}">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                <i class="fe-package font-22 avatar-title text-info"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $on_the_way_count }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Packed Order</p>
                                <small class="text-muted">Amount: {{ number_format($on_the_way_amount, 2) }}৳</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <!-- On Hold -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders',['slug'=>'on-hold']) }}">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-secondary border-secondary border">
                                <i class="fe-pause font-22 avatar-title text-secondary"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $on_hold_count }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">On Hold</p>
                                <small class="text-muted">Amount: {{ number_format($on_hold_amount, 2) }}৳</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <!-- Returned / Cancelled -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders',['slug'=>'cancelled']) }}">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-danger border-danger border">
                                <i class="fe-rotate-ccw font-22 avatar-title text-danger"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $cancelled_count }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Returned Order</p>
                                <small class="text-muted">Amount: {{ number_format($cancelled_amount, 2) }}৳</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <!-- Delivered -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders',['slug'=>'completed']) }}">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                <i class="fe-truck font-22 avatar-title text-success"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $completed_count }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Delivered Order</p>
                                <small class="text-muted">Amount: {{ number_format($completed_amount, 2) }}৳</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <!-- In Courier -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders',['slug'=>'in-courier']) }}">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                <i class="fe-map-pin font-22 avatar-title text-info"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $in_courier_count }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">In Courier</p>
                                <small class="text-muted">Amount: {{ number_format($in_courier_amount, 2) }}৳</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <!-- Didn't Receive Call -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders',['slug'=>'didnt-receive-call']) }}">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-danger border-danger border">
                                <i class="fe-phone-missed font-22 avatar-title text-danger"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $didnt_receive_count }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Didn't Receive Call</p>
                                <small class="text-muted">Amount: {{ number_format($didnt_receive_amount, 2) }}৳</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <!-- Today's Order -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('admin.orders', ['slug' => 'all', 'date' => 'today']) }}">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-success border-success border">
                                <i class="fe-shopping-bag font-22 avatar-title text-success"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $today_order_count }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Today's Order</p>
                                <small class="text-muted">Amount: {{ number_format($today_order_amount, 2) }}৳</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <!-- Products -->
        <div class="col-md-6 col-xl-3">
            <a href="{{ route('products.index') }}">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-info border-info border">
                                <i class="fe-database font-22 avatar-title text-info"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1">{{ $total_product }}</h3>
                                <p class="text-muted mb-1 text-truncate">Products</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <!-- Customers -->
        <div class="col-md-6 col-xl-3">
            <div class="widget-rounded-circle card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="avatar-lg rounded-circle bg-soft-warning border-warning border">
                                <i class="fe-user font-22 avatar-title text-warning"></i>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-end">
                                <h3 class="text-dark mt-1"><span data-plugin="counterup">{{ $total_customer }}</span></h3>
                                <p class="text-muted mb-1 text-truncate">Customer</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-primary border">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                        <div>
                            <h4 class="header-title mb-1">Top Best Selling Product</h4>
                            <p class="text-muted mb-0">Calculated from actual sales quantity of completed orders.</p>
                        </div>
                        @if($topBestSellingProduct)
                        <a href="{{ route('products.edit', $topBestSellingProduct->id) }}" class="btn btn-sm btn-outline-primary">Manage Product</a>
                        @endif
                    </div>

                    @if($topBestSellingProduct)
                    <div class="row align-items-center mt-3">
                        <div class="col-md-2 col-lg-2 mb-3 mb-md-0">
                            <img src="{{ asset($topBestSellingProduct->image ? $topBestSellingProduct->image->image : '') }}" alt="{{ $topBestSellingProduct->name }}" class="img-fluid rounded" style="max-height: 110px; object-fit: cover; width: 100%;">
                        </div>
                        <div class="col-md-5 col-lg-6">
                            <h4 class="mb-2">{{ $topBestSellingProduct->name }}</h4>
                            <div class="d-flex flex-wrap gap-3 text-muted">
                                <span><strong>Sold Qty:</strong> {{ $topBestSellingProduct->sold_quantity }}</span>
                                <span><strong>Orders:</strong> {{ $topBestSellingProduct->total_orders }}</span>
                                <span><strong>Revenue:</strong> {{ number_format($topBestSellingProduct->sold_amount, 2) }}৳</span>
                            </div>
                        </div>
                        <div class="col-md-5 col-lg-4 mt-3 mt-md-0">
                            <div class="row g-2 text-center">
                                <div class="col-6">
                                    <div class="p-2 rounded bg-soft-success border border-success-subtle">
                                        <small class="d-block text-muted">Current Price</small>
                                        <strong>৳ {{ number_format($topBestSellingProduct->new_price, 2) }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded bg-soft-info border border-info-subtle">
                                        <small class="d-block text-muted">Old Price</small>
                                        <strong>{{ $topBestSellingProduct->old_price ? '৳ ' . number_format($topBestSellingProduct->old_price, 2) : 'N/A' }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="alert alert-light border mt-3 mb-0">No completed order data was found yet, so the top best selling product is not available.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">

                    <h4 class="header-title mb-3">Latest 5 Orders</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-hover table-nowrap table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th colspan="2">Id</th>
                                    <th>Invoice</th>
                                    <th>Amount</th>
                                    <th>Customer</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latest_order as $order)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td style="width: 36px;">
                                        @if($order->product)
                                        <img src="{{asset($order->product->image?$order->product->image->image:'')}}" alt="contact-img" title="contact-img" class="rounded-circle avatar-sm" />
                                        @endif
                                    </td>

                                    <td>
                                        {{$order->invoice_id}}
                                    </td>

                                    <td>
                                        {{$order->amount}}
                                    </td>

                                    <td>
                                        {{$order->customer?$order->customer->name:''}}
                                    </td>
                                    <td>
                                        {{ $order->status?->name ?? 'Unknown' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-body">
                    <div class="dropdown float-end">
                        <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Edit Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Export Report</a>
                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item">Action</a>
                        </div>
                    </div>

                    <h4 class="header-title mb-3">Latest Customers</h4>

                    <div class="table-responsive">
                        <table class="table table-borderless table-nowrap table-hover table-centered m-0">

                            <thead class="table-light">
                                <tr>
                                    <th>Id</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latest_customer as $customer)
                                <tr>
                                    <td>
                                        <h5 class="m-0 fw-normal">{{$loop->iteration}}</h5>
                                    </td>

                                    <td>
                                        {{$customer->name}}
                                    </td>

                                    <td>
                                        {{$customer->phone}}
                                    </td>

                                    <td>
                                        {{$customer->created_at->format('d-m-Y')}}
                                    </td>

                                    <td>
                                        {{ (string) $customer->status === '1' ? 'Active' : 'Inactive' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end .table-responsive-->
                </div>
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div>
    <!-- end row -->
    
</div> 


    

</div> 
@endsection
