@extends('backEnd.layouts.master')
@section('title',$order_status->name.' Order')
@section('css')
<style>
    .order-toolbar {
        padding: 16px 18px;
        border: 1px solid #dbe4f0;
        border-radius: 18px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 55%, #f1f5f9 100%);
        box-shadow: 0 16px 36px rgba(15, 23, 42, 0.07);
        margin-bottom: 16px;
    }

    .order-toolbar-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .order-toolbar-actions {
        flex: 0 0 auto;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .order-toolbar-filters {
        flex: 1 1 auto;
        min-width: 0;
    }

    .order-toolbar .action2-btn {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .order-toolbar .action2-btn li {
        margin: 0;
    }

    .order-toolbar .action2-btn .btn {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 15px;
        border-width: 1px;
        box-shadow: none;
    }

    .order-filter-panel {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 0;
        border: 0;
        border-radius: 0;
        background: transparent;
    }

    .order-filter-panel .form-control {
        width: 100% !important;
        min-height: 42px;
        border: 1px solid #dbe4f0;
        border-radius: 999px;
        box-shadow: none;
        background: #fff;
        padding: 0 14px;
    }

    .order-filter-panel .form-control:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.08);
    }

    .order-search-box {
        position: relative;
        flex: 0 0 260px;
        min-width: 260px;
    }

    .order-search-box i {
        position: absolute;
        top: 50%;
        left: 14px;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 15px;
    }

    .order-search-box .form-control {
        padding-left: 40px;
    }

    .order-preset-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        flex: 0 1 auto;
    }

    .order-preset-group .btn {
        white-space: nowrap;
        min-height: 40px;
        min-width: 132px;
        justify-content: center;
        padding: 8px 16px;
        border-radius: 999px;
        font-weight: 700;
    }

    .order-date-fields {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 0 1 auto;
    }

    .order-date-fields .form-control {
        min-width: 150px;
    }

    .order-filter-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 0 0 auto;
    }

    .order-filter-actions .btn {
        min-height: 42px;
        border-radius: 999px;
        padding: 0 18px;
        font-weight: 700;
    }

    .order-filter-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 999px;
        background: #f8fafc;
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    @media (max-width: 991.98px) {
        .order-toolbar {
            padding: 14px;
        }

        .order-toolbar-row {
            flex-direction: column;
            align-items: stretch;
        }

        .order-toolbar-actions,
        .order-toolbar-filters {
            width: 100%;
        }

        .order-preset-group .btn {
            min-width: 124px;
        }

        .order-filter-panel {
            flex-wrap: wrap;
            justify-content: flex-start;
        }
    }

    @media (max-width: 575.98px) {
        .order-filter-panel {
            padding: 12px;
        }

        .order-search-box,
        .order-date-fields,
        .order-filter-actions {
            width: 100%;
        }

        .order-date-fields {
            flex-direction: column;
        }

        .order-date-fields .form-control,
        .order-filter-select {
            width: 100%;
        }

        .order-filter-actions {
            flex-direction: column;
        }

        .order-preset-group .btn {
            width: 100%;
            min-width: 0;
        }

        .order-toolbar .action2-btn .btn {
            width: 100%;
        }
    }

    /* Order Image Styles */
    .order-image-wrapper {
        position: relative;
        width: 45px;
        height: 45px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .order-image-wrapper:hover {
        transform: scale(1.1);
    }
    .order-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }
    .order-count-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        min-width: 18px;
        height: 18px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        border: 2px solid #fff;
        padding: 0 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .modal-image-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    @media (min-width: 992px) {
        .modal-image-grid {
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        }
    }

    .modal-image-item {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #fff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .modal-image-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .modal-image-item img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        display: block;
        cursor: pointer;
    }

    @media (min-width: 992px) {
        .modal-image-item img {
            height: 300px;
        }
    }
    .modal-image-item .item-qty {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(15, 23, 42, 0.9);
        color: #fff;
        font-size: 14px;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 800;
        backdrop-filter: blur(4px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .modal-image-item .item-name {
        padding: 14px;
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        background: #fff;
        border-top: 1px solid #f1f5f9;
    }
    .order-items-modal .modal-content {
        border-radius: 24px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .order-items-modal .modal-header {
        border-bottom: 1px solid #f1f5f9;
        padding: 24px 30px;
        background: #fff;
        border-radius: 24px 24px 0 0;
    }
    .order-items-modal .modal-title {
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.025em;
        font-size: 20px;
    }
    .modal-shipping-info {
        padding: 20px 30px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        font-size: 16px;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 15px;
        font-weight: 600;
        line-height: 1.5;
    }
    .modal-shipping-info i {
        font-size: 22px;
        color: #3b82f6;
    }
    .modal-pricing-summary {
        padding: 30px;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        border-radius: 0 0 24px 24px;
        display: flex;
        justify-content: flex-end;
    }
    .pricing-table {
        min-width: 350px;
    }
    .pricing-row {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 16px;
        color: #475569;
        font-weight: 500;
    }
    .pricing-row.total {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 2px solid #f1f5f9;
        font-weight: 900;
        color: #2563eb;
        font-size: 24px;
    }
    .item-meta {
        position: absolute;
        bottom: 50px;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        padding: 8px;
        font-size: 13px;
        font-weight: 800;
        color: #1e293b;
        display: flex;
        justify-content: center;
        gap: 12px;
        border-top: 1px solid #f1f5f9;
    }
</style>
@endsection
@section('content')
@php
    $currentOrderSlug = $order_status->slug ?? request()->route('slug');
    $datePresets = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'last_7_days' => 'Last 7 Days',
        'this_month' => 'This Month',
    ];
@endphp
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{route('admin.order.create')}}" class="btn btn-danger rounded-pill"><i class="fe-shopping-cart"></i> Add New</a>
                </div>
                <h4 class="page-title">{{$order_status->name}} Order ({{$order_status->orders_count}})</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row order_page">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- <div class="alert alert-light border mb-3" role="alert">
                        <strong>Source Guide:</strong>
                        <span class="ms-2"><span class="badge bg-primary">Facebook</span> Orders from Facebook, Instagram, or Meta.</span>
                        <span class="ms-2"><span class="badge bg-dark">TikTok</span> Orders from TikTok traffic.</span>
                        <span class="ms-2"><span class="badge bg-success">Google</span> Orders from Google Ads or paid Google campaigns.</span>
                        <span class="ms-2"><span class="badge bg-info text-dark">Organic</span> Orders from search engines, direct visits, or external referrals.</span>
                        <span class="ms-2"><span class="badge bg-danger">Admin Panel</span> Orders created manually from the admin panel.</span>
                    </div> --}}
                    <div class="order-toolbar">
                        <div class="order-toolbar-row">
                        <div class="order-toolbar-actions">
                            <ul class="action2-btn">
                                <li><a data-bs-toggle="modal" data-bs-target="#asignUser" class="btn rounded-pill btn-success"><i class="fe-plus"></i> Assign User</a></li>
                                <li><a data-bs-toggle="modal" data-bs-target="#changeStatus" class="btn rounded-pill btn-primary"><i class="fe-plus"></i> Change Status</a></li>
                                <li><a href="{{route('admin.order.bulk_destroy')}}" class="btn rounded-pill btn-danger order_delete"><i class="fe-plus"></i> Delete All</a></li>
                                <li><a href="{{route('admin.order.order_print')}}" class="btn rounded-pill btn-info multi_order_print"><i class="fe-printer"></i> Print</a></li>
                                <li><a href="{{route('admin.bulk_courier', 'steadfast')}}" class="btn rounded-pill btn-warning multi_order_courier"><i class="fe-truck"></i> State Courier</a></li>
                                <li><a data-bs-toggle="modal" data-bs-target="#bulkPathao" class="btn rounded-pill btn-success"><i class="fe-truck"></i> Pathao Courier</a></li>
                            </ul>
                            <div class="order-preset-group">
                                @foreach ($datePresets as $presetValue => $presetLabel)
                                    <a href="{{ route('admin.orders', $currentOrderSlug) }}?{{ http_build_query(array_filter([
                                        'keyword' => $activeFilters['keyword'] ?? null,
                                        'date' => $presetValue,
                                    ])) }}"
                                        class="btn btn-sm rounded-pill {{ ($activeFilters['date'] ?? '') === $presetValue ? 'btn-primary' : 'btn-outline-primary' }}">
                                        {{ $presetLabel }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="order-toolbar-filters">
                            <div class="order-filter-panel">
                            <form class="custom_form" method="GET" action="{{ url()->current() }}">
                                <div class="d-flex align-items-center flex-wrap justify-content-end gap-2">
                                    <div class="order-search-box">
                                            <i class="fe-search"></i>
                                            <input id="order-keyword" type="text" name="keyword" class="form-control" placeholder="Search by invoice, phone, name" value="{{ $activeFilters['keyword'] ?? '' }}">
                                    </div>
                                    <div class="order-date-fields">
                                            <input id="order-start-date" type="date" name="start_date" class="form-control" value="{{ $activeFilters['start_date'] ?? '' }}">
                                            <input id="order-end-date" type="date" name="end_date" class="form-control" value="{{ $activeFilters['end_date'] ?? '' }}">
                                    </div>
                                    <div class="order-filter-actions">
                                                <button class="btn rounded-pill btn-info">Filter</button>
                                                <a href="{{ route('admin.orders', $currentOrderSlug) }}" class="btn rounded-pill btn-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                        </div>
                    </div>
                    @if(($activeFilters['start_date'] ?? null) || ($activeFilters['end_date'] ?? null) || ($activeFilters['date'] ?? null))
                    <div class="order-filter-badge">
                        <i class="fe-calendar"></i>
                        <span>
                            Date Filter:
                            {{ ($activeFilters['date_label'] ?? null)
                                ? $activeFilters['date_label']
                                : (($activeFilters['start_date'] ?? '...') . ' to ' . ($activeFilters['end_date'] ?? '...')) }}
                        </span>
                    </div>
                    @endif
                    <div class="table-responsive ">
                        <table id="datatable-buttons" class="table table-striped   w-100">
                            <thead>
                                <tr>
                                    <th style="width:2%">
                                        <div class="form-check">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input checkall" value="">
                                            </label>
                                        </div>
                                    </th>
                                    <th style="width:2%">SL</th>
                                    <th style="width:5%">Image</th>
                                    <th style="width:8%">Action</th>
                    <th style="width:8%">Invoice</th>
                    <th style="width:8%">Ip Address</th>
                    <th style="width:10%">Date</th>
                    <th style="width:10%">Name</th>
                    <th style="width:10%">Phone</th>
                    <th style="width:10%">Assign</th>
                    <th style="width:10%">Amount</th>
                    <th style="width:10%">Source</th>
                    <th style="width:10%">Trak Order  </th>
                    <th style="width:10%">Status</th>
                    <th> Froud Checker </th>
                    </tr>
                    </thead>


                    <tbody>
                        @foreach($show_data as $key=>$value)

                        <tr>
                            <td><input type="checkbox" class="checkbox" value="{{$value->id}}"></td>
                            <td>{{ ($show_data->currentPage() - 1) * $show_data->perPage() + $loop->iteration }}</td>
                            <td>
                                @php($totalQty = $value->orderdetails->sum('qty'))
                                @php($firstDetail = $value->orderdetails->first())
                                @php($firstImage = $firstDetail ? ($firstDetail->productVariable?->primary_media_image ?? $firstDetail->product?->primary_media_image ?? $firstDetail->image?->image ?? 'uploads/logo.png') : 'uploads/logo.png')
                                <div class="order-image-wrapper order-image-trigger" 
                                     data-invoice="{{$value->invoice_id}}"
                                     data-details="{{ json_encode($value->orderdetails->map(function($detail) {
                                         return [
                                             'image' => asset($detail->productVariable?->primary_media_image ?? $detail->product?->primary_media_image ?? $detail->image?->image ?? 'uploads/logo.png'),
                                             'name' => $detail->product_name,
                                             'qty' => $detail->qty,
                                             'size' => $detail->product_size,
                                             'color' => $detail->product_color,
                                             'price' => $detail->sale_price
                                         ];
                                     })) }}"
                                     data-summary="{{ json_encode([
                                         'address' => $value->shipping ? ($value->shipping->name . ' | ' . $value->shipping->phone . ' | ' . $value->shipping->address . ' | ' . $value->shipping->area) : 'N/A',
                                         'subtotal' => $value->orderdetails->sum(function($d) { return $d->sale_price * $d->qty; }),
                                         'shipping' => $value->shipping_charge,
                                         'discount' => $value->discount,
                                         'total' => $value->amount
                                     ]) }}">
                                    <img src="{{ asset($firstImage) }}" alt="">
                                    @if($totalQty > 1)
                                        <span class="order-count-badge">{{ $totalQty }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="button-list custom-btn-list">
                                    <a href="{{route('admin.order.workspace',['invoice_id'=>$value->invoice_id, 'tab' => 'invoice'])}}" title="Invoice"><i class="fe-eye"></i></a>
                                    <a href="{{route('admin.order.workspace',['invoice_id'=>$value->invoice_id, 'tab' => 'manage'])}}" title="Process"><i class="fe-settings"></i></a>
                                    <a href="{{route('admin.order.workspace',['invoice_id'=>$value->invoice_id, 'tab' => 'manage'])}}" title="Edit"><i class="fe-edit"></i></a>
                                    <form method="post" action="{{route('admin.order.destroy')}}" class="d-inline">
                                        @csrf
                                        <input type="hidden" value="{{$value->id}}" name="id">
                                        <button type="submit" title="Delete" class="delete-confirm"><i class="fe-trash-2"></i></button>
                                    </form>
                                    <a data-bs-toggle="modal" data-bs-target="#pathao{{$value->id}}" class="btn btn-success">pathao</a>
                                        <div class="container mb-5">

                                    </div>
                                </div>
                            </td>

                            <td>{{$value->invoice_id}}</td>

                            <td>{{$value->ip_address}}</td>
                            <td>{{date('d-m-Y', strtotime($value->updated_at))}}<br> {{date('h:i:s a', strtotime($value->updated_at))}}</td>
                            <td><strong>{{$value->shipping?$value->shipping->name:''}}</strong>
                                <p>{{$value->shipping?$value->shipping->address:''}}</p>
                            </td>
                            <td>{{$value->shipping?$value->shipping->phone:''}}
                                <div class="mt-1">
                                    <a href="https://api.whatsapp.com/send?phone=88{{$value->shipping?$value->shipping->phone:''}}" target="_blank" class="text-success" style="font-size: 18px;" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                                    <a href="tel:{{$value->shipping?$value->shipping->phone:''}}" class="text-primary" style="font-size: 16px; margin-left: 5px;" title="Call"><i class="fe-phone"></i></a>
                                </div>
                            </td>
                            <td>{{$value->user?$value->user->name:''}}</td>
                            <td>৳{{$value->amount}}</td>
                            <td>
                                <span class="badge {{ $value->marketing_source_badge_class }}">
                                    {{ $value->marketing_source_label }}
                                </span>
                                @if($value->utm_campaign)
                                    <div class="small text-muted mt-1">{{ $value->utm_campaign }}</div>
                                @endif
                            </td>
                            <td>
                             @if($value->courier == 'pathao' && $value->status->name =='Shipped')
                                <a href="https://merchant.pathao.com/tracking?consignment_id={{ $value->tracking_id }}&phone={{ $value->shipping->phone }}"
                                target="_blank"
                                class="btn btn-sm btn-primary">
                                Track Order
                                </a>
                            @endif
                            </td>

                            </td>
                            <td>{{$value->status?$value->status->name:''}}</td>
                            <td>
                                <button class="fraud-checker btn btn-info" data-phone="{{$value->shipping->phone}}">Check Fraud</button>
                            </td>

                        </tr>
                        @endforeach
                    </tbody>
                    </table>
                </div>
                <div class="custom-paginate">
                    {{$show_data->links('pagination::bootstrap-4')}}
                </div>
            </div> <!-- end card body-->

        </div> <!-- end card -->
    </div><!-- end col-->
</div>
</div>
<!-- Assign User End -->
<div class="modal fade" id="asignUser" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Assign User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.order.assign')}}" id="order_assign" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Select..</option>
                            @foreach($users as $key=>$value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Assign User End-->

<!-- Assign User End -->
<div class="modal fade" id="changeStatus" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.order.status')}}" id="order_status_form">
                <div class="modal-body">
                    <div class="form-group">
                        <select name="order_status" id="order_status" class="form-control">
                            <option value="">Select..</option>
                            @foreach($orderstatus as $key=>$value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Assign User End-->

<!-- Bulk Pathao Start -->
<div class="modal fade" id="bulkPathao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Pathao Courier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.bulk_courier', 'pathao')}}" id="bulk_pathao_form" method="GET">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="bulk_pathaostore" class="form-label">Select Pathao Store</label>
                        <select name="store_id" id="bulk_pathaostore" class="form-control" required>
                            <option value="">Select Store...</option>
                            @if(isset($pathaostore['data']['data']))
                                @foreach($pathaostore['data']['data'] as $store)
                                    <option value="{{$store['store_id']}}">{{$store['store_name']}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <p class="mt-2 text-muted">Selected orders will be sent to Pathao. Make sure they have valid addresses and phone numbers.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit Bulk Orders</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Bulk Pathao End -->

<!-- pathao coureir start -->
@foreach($show_data as $key=>$value)
<div class="modal fade" id="pathao{{$value->id}}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Pathao Courier - {{$value->invoice_id}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('admin.order.pathao')}}" method="POST" id="order_sendto_pathao">
                @csrf

                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="id" value="{{$value->id}}">
                        <label for="pathaostore" class="form-label">Store</label>
                        <select name="pathaostore" id="pathaostore" class="pathaostore form-control">
                            <option value="">Select Store...</option>

                            @if(isset($pathaostore['data']['data']))
                            @foreach($pathaostore['data']['data'] as $key=>$store)
                            <option value="{{$store['store_id']}}">{{$store['store_name']}}</option>
                            @endforeach
                            @else
                            @endif
                        </select>
                        @if ($errors->has('pathaostore'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('pathaostore') }}</strong>
                        </span>
                        @endif
                    </div>
                    <!-- form group end -->
                    <div class="form-group mt-3">
                        <label for="pathaocity" class="form-label">City</label>
                        <select name="pathaocity" id="pathaocity" class="chosen-select pathaocity form-control" style="width:100%">
                            <option value="">Select City...</option>
                            @if(isset($pathaocities['data']['data']))
                            @foreach($pathaocities['data']['data'] as $key=>$city)
                            <option value="{{$city['city_id']}}">{{$city['city_name']}}</option>
                            @endforeach
                            @else
                            @endif
                        </select>
                        @if ($errors->has('pathaocity'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('pathaocity') }}</strong>
                        </span>
                        @endif
                    </div>
                    <!-- form group end -->
                    <div class="form-group mt-3">
                        <label for="" class="form-label">Zone</label>
                        <select name="pathaozone" id="pathaozone" class="pathaozone chosen-select form-control  {{ $errors->has('pathaozone') ? ' is-invalid' : '' }}" value="{{ old('pathaozone') }}" style="width:100%">
                        </select>
                        @if ($errors->has('pathaozone'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('pathaozone') }}</strong>
                        </span>
                        @endif
                    </div>
                    <!-- form group end -->
                    <div class="form-group mt-3">
                        <label for="" class="form-label">Area</label>
                        <select name="pathaoarea" id="pathaoarea" class="pathaoarea chosen-select form-control  {{ $errors->has('pathaoarea') ? ' is-invalid' : '' }}" value="{{ old('pathaoarea') }}" style="width:100%">
                        </select>
                        @if ($errors->has('pathaoarea'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('pathaoarea') }}</strong>
                        </span>
                        @endif
                    </div>
                    <!-- form group end -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
<!-- Order Images Modal Start -->
<div class="modal fade order-items-modal" id="orderImagesModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fe-shopping-bag me-2 text-primary"></i>
                    Order Items - <span class="text-primary">#<span id="modalInvoiceId"></span></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="modalShippingInfo" class="modal-shipping-info">
                <i class="fe-map-pin"></i>
                <span id="modalAddressText"></span>
            </div>
            <div class="modal-body p-0" style="max-height: 60vh; overflow-y: auto;">
                <div id="modalImageGrid" class="modal-image-grid"></div>
            </div>
            <div class="modal-pricing-summary">
                <div class="pricing-table">
                    <div class="pricing-row">
                        <span>Subtotal</span>
                        <span id="modalSubtotal">৳0</span>
                    </div>
                    <div class="pricing-row">
                        <span>Shipping</span>
                        <span id="modalShipping">৳0</span>
                    </div>
                    <div class="pricing-row">
                        <span>Discount</span>
                        <span id="modalDiscount">৳0</span>
                    </div>
                    <div class="pricing-row total">
                        <span>Grand Total</span>
                        <span id="modalTotal">৳0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Zoom Modal -->
<div class="modal fade" id="imageZoomModal" tabindex="-1" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center">
                <img id="zoomedImage" src="" alt="" style="max-width: 100%; max-height: 90vh; border-radius: 8px; box-shadow: 0 0 20px rgba(0,0,0,0.5);">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>
<!-- Order Images Modal End -->

<!-- Fraud Checker Modal Start -->
<div id="fraudModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background: rgba(0,0,0,0.6); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:8px; max-width:760px; width:92%; padding:20px; position:relative; box-shadow:0 4px 15px rgba(0,0,0,0.3);">
        <button id="fraudModalClose" style="position:absolute; top:12px; right:12px; background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        <div style="display:flex; align-items:center; gap:12px; margin-top:0; margin-bottom:12px;">
            <h2 style="margin:0; color:#0f172a;">Fraud Check Result</h2>
            <span id="fraudCacheBadge" style="display:none; background:#dcfce7; color:#166534; border:1px solid #86efac; border-radius:999px; padding:4px 10px; font-size:12px; font-weight:700;">Cached Result</span>
        </div>
        <div id="fraudModalContent" style="max-height:420px; overflow-y:auto; font-family: Arial, sans-serif;"></div>
    </div>
</div>
<style>
    #fraudModal table {
        width: 100%;
        border-collapse: collapse;
    }

    #fraudModal th,
    #fraudModal td {
        border: 1px solid #ddd;
        padding: 8px 12px;
        text-align: left;
    }

    #fraudModal th {
        background-color: #0f172a;
        color: white;
        font-weight: bold;
    }
</style>
<!-- Fraud Checker Modal End -->

<!-- pathao courier  End-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $(".checkall").on('change', function() {
            $(".checkbox").prop('checked', $(this).is(":checked"));
        });

        // Order Image Modal Logic
        $('.order-image-trigger').on('click', function() {
            const invoiceId = $(this).data('invoice');
            const details = $(this).data('details');
            const summary = $(this).data('summary');
            const grid = $('#modalImageGrid');
            
            $('#modalInvoiceId').text(invoiceId);
            $('#modalAddressText').text(summary.address);
            $('#modalSubtotal').text('৳' + summary.subtotal);
            $('#modalShipping').text('৳' + summary.shipping);
            $('#modalDiscount').text('৳' + summary.discount);
            $('#modalTotal').text('৳' + summary.total);
            
            grid.empty();

            details.forEach(item => {
                const sizeInfo = item.size ? `<span>Size: ${item.size}</span>` : '';
                const colorInfo = item.color ? `<span>Color: ${item.color}</span>` : '';
                const metaHtml = (sizeInfo || colorInfo) ? `<div class="item-meta">${sizeInfo}${colorInfo}</div>` : '';

                grid.append(`
                    <div class="modal-image-item">
                        <img src="${item.image}" alt="${item.name}" class="zoom-trigger" title="${item.name}">
                        <div class="item-qty">Qty: ${item.qty}</div>
                        ${metaHtml}
                        <div class="item-name">${item.name}</div>
                    </div>
                `);
            });

            $('#orderImagesModal').modal('show');
        });

        // Zoom Logic
        $(document).on('click', '.zoom-trigger', function() {
            const src = $(this).attr('src');
            $('#zoomedImage').attr('src', src);
            $('#imageZoomModal').modal('show');
        });

        const fraudCheckerConfig = @json([
            'enabled' => (bool) $fraudCheckerConfig,
            'endpoint' => route('admin.order.fraud_check'),
        ]);
        const fraudModal = document.getElementById('fraudModal');
        const fraudModalContent = document.getElementById('fraudModalContent');
        const fraudModalClose = document.getElementById('fraudModalClose');
        const fraudCacheBadge = document.getElementById('fraudCacheBadge');

        if (fraudModal && fraudModalClose && fraudModalContent) {
            fraudModalClose.addEventListener('click', function() {
                fraudModal.style.display = 'none';
                fraudModalContent.innerHTML = '';
                if (fraudCacheBadge) {
                    fraudCacheBadge.style.display = 'none';
                }
            });

            window.addEventListener('click', function(e) {
                if (e.target === fraudModal) {
                    fraudModal.style.display = 'none';
                    fraudModalContent.innerHTML = '';
                }
            });

            $(document).on('click', '.fraud-checker', function() {
                const phone = $(this).data('phone');

                if (!phone) {
                    toastr.error('Phone number not found');
                    return;
                }

                if (!fraudCheckerConfig.enabled) {
                    toastr.error('Fraud checker API key not configured');
                    return;
                }

                fraudModalContent.innerHTML = '<p style="padding:20px;">Loading fraud data...</p>';
                fraudModal.style.display = 'flex';

                fetch(fraudCheckerConfig.endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        phone: String(phone),
                    }),
                })
                    .then(function(res) {
                        return res.json().then(function(data) {
                            return {
                                ok: res.ok,
                                data: data,
                            };
                        });
                    })
                    .then(function(result) {
                        const data = result.data;

                        if (!result.ok) {
                            if (fraudCacheBadge) {
                                fraudCacheBadge.style.display = 'none';
                            }
                            fraudModalContent.innerHTML = '<p style="padding:20px;">' + (data.message || 'Failed to fetch data.') + '</p>';
                            return;
                        }

                        if (fraudCacheBadge) {
                            fraudCacheBadge.style.display = data.cached ? 'inline-flex' : 'none';
                        }
                        fraudModalContent.innerHTML = '';

                        const courierPayload = data?.courierData || data?.data?.courierData || data?.data || data?.result || null;

                        if (!courierPayload || typeof courierPayload !== 'object') {
                            fraudModalContent.innerHTML = '<p style="padding:20px;">' + (data?.message || 'No data found.') + '</p>';
                            return;
                        }

                        const courierData = courierPayload;
                        const summary = courierData.summary || courierData.Summary || data?.summary || data?.data?.summary || {};
                        const table = document.createElement('table');
                        table.innerHTML = `
                            <tr>
                                <th>Logo</th>
                                <th>Courier Name</th>
                                <th>Total Parcel</th>
                                <th>Successful</th>
                                <th>Cancelled</th>
                                <th>Success Rate</th>
                            </tr>
                        `;

                        for (const key in courierData) {
                            if (key === 'summary') continue;

                            const courier = courierData[key];
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><img src="${courier.logo}" alt="${courier.name}" width="40" /></td>
                                <td>${courier.name}</td>
                                <td>${courier.total_parcel}</td>
                                <td>${courier.success_parcel}</td>
                                <td>${courier.cancelled_parcel}</td>
                                <td>${courier.success_ratio}%</td>
                            `;
                            table.appendChild(tr);
                        }

                        const summaryRow = document.createElement('tr');
                        summaryRow.style.backgroundColor = '#f3f3f3';
                        summaryRow.innerHTML = `
                            <td colspan="2"><strong>Total Summary</strong></td>
                            <td><strong>${summary.total_parcel || 0}</strong></td>
                            <td><strong>${summary.success_parcel || 0}</strong></td>
                            <td><strong>${summary.cancelled_parcel || 0}</strong></td>
                            <td><strong>${summary.success_ratio || 0}%</strong></td>
                        `;
                        table.appendChild(summaryRow);

                        fraudModalContent.appendChild(table);
                    })
                    .catch(function(err) {
                        fraudModalContent.innerHTML = '<p style="padding:20px;">Failed to fetch data.</p>';
                        console.error(err);
                    });
            });
        }

        // order assign
        $(document).on('submit', 'form#order_assign', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var method = form.attr('method') || 'POST';
            let user_id = $(document).find('select#user_id').val();

            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (!user_id) {
                toastr.error('Please select a user first!');
                return;
            }

            if (order_ids.length == 0) {
                toastr.error('Please Select An Order First !');
                return;
            }

            $.ajax({
                type: method,
                url: url,
                data: {
                    _token: form.find('input[name="_token"]').val(),
                    user_id,
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        toastr.success(res.message);
                        window.location.reload();

                    } else {
                        toastr.error(res.message || 'Order assign failed');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Order assign failed');
                }
            });

        });

        // order status change
        $(document).on('submit', 'form#order_status_form', function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            var method = $(this).attr('method');
            let order_status = $(document).find('select#order_status').val();

            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (order_ids.length == 0) {
                toastr.error('Please Select An Order First !');
                return;
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    order_status,
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        toastr.success(res.message);
                        window.location.reload();

                    } else {
                        toastr.error('Failed something wrong');
                    }
                }
            });

        });
        // order delete
        $(document).on('click', '.order_delete', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (order_ids.length == 0) {
                toastr.error('Please Select An Order First !');
                return;
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        toastr.success(res.message);
                        window.location.reload();

                    } else {
                        toastr.error('Failed something wrong');
                    }
                }
            });

        });

        // multiple print
        $(document).on('click', '.multi_order_print', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (order_ids.length == 0) {
                toastr.error('Please Select Atleast One Order!');
                return;
            }
            $.ajax({
                type: 'GET',
                url,
                data: {
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        console.log(res.items, res.info);
                        var myWindow = window.open("", "_blank");
                        myWindow.document.write(res.view);
                    } else {
                        toastr.error('Failed something wrong');
                    }
                }
            });
        });
        // multiple courier
        $(document).on('click', '.multi_order_courier', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (order_ids.length == 0) {
                toastr.error('Please Select An Order First !');
                return;
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        toastr.success(res.message);
                        window.location.reload();

                    } else {
                        toastr.error('Failed something wrong');
                    }
                }
            });

        });

        // bulk pathao
        $(document).on('submit', 'form#bulk_pathao_form', function(e) {
            e.preventDefault();
            var url = $(this).attr('action');
            let store_id = $(this).find('select[name="store_id"]').val();

            var order = $('input.checkbox:checked').map(function() {
                return $(this).val();
            });
            var order_ids = order.get();

            if (order_ids.length == 0) {
                toastr.error('Please Select An Order First !');
                return;
            }

            if (!store_id) {
                toastr.error('Please Select A Store!');
                return;
            }

            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    store_id,
                    order_ids
                },
                success: function(res) {
                    if (res.status == 'success') {
                        toastr.success(res.message);
                        window.location.reload();
                    } else {
                        toastr.error(res.message || 'Failed something wrong');
                    }
                },
                error: function(xhr) {
                    toastr.error(xhr.responseJSON?.message || 'Bulk Pathao failed');
                }
            });
        });
    })
</script>
@endsection
