@extends('backEnd.layouts.master')
@section('title', 'Order Workspace')
@section('css')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Playfair+Display:wght@700;800&display=swap');

        .container-fluid {
            font-family: "Poppins", "Cerebri Sans", sans-serif;
        }

        .workspace-tabs .nav-link {
            border-radius: 999px;
            font-family: "Poppins", "Cerebri Sans", sans-serif;
            font-weight: 700;
        }

        .workspace-panel {
            border: 1px solid #dbe6f3;
            border-radius: 24px;
            background:
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.12), transparent 26%),
                linear-gradient(135deg, #ffffff 0%, #f7fbff 100%);
            padding: 28px;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        }

        .workspace-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .workspace-meta .badge {
            font-size: 14px;
            padding: 10px 14px;
        }

        .workspace-hero-title {
            font-family: "Playfair Display", Georgia, serif;
            font-size: 30px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.02em;
        }

        .workspace-hero-text {
            color: #64748b;
            max-width: 620px;
            margin-bottom: 0;
        }

        .workspace-stat-strip {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 24px;
        }

        .workspace-stat-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid #e4edf8;
            border-radius: 18px;
            padding: 16px 18px;
        }

        .workspace-stat-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            margin-bottom: 6px;
        }

        .workspace-stat-value {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            word-break: break-word;
        }

        .workspace-action-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .invoice-preview-frame {
            width: 100%;
            min-height: 980px;
            border: 1px solid #dee2e6;
            border-radius: 14px;
            background: #fff;
        }

        .workspace-invoice-preview {
            background: #f8f9fa;
            border-radius: 16px;
            padding: 24px;
            overflow-x: auto;
        }

        .workspace-card {
            border: 1px solid #e9eef5;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }

        .workspace-card .card-body {
            padding: 24px;
        }

        .workspace-section-title {
            font-family: "Playfair Display", Georgia, serif;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: -0.01em;
        }

        .workspace-section-subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 0;
        }

        .workspace-sticky {
            position: sticky;
            top: 24px;
        }

        .workspace-summary-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .workspace-summary-wrap {
            background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
            border: 1px solid #dbe7f6;
            border-radius: 20px;
            padding: 16px;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.8);
        }

        .workspace-summary-table tr:last-child td {
            font-size: 18px;
            font-weight: 800;
            color: #0f172a;
            background: #f8fbff;
        }

        .workspace-control-card {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .workspace-compact-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .workspace-compact-badges .badge {
            padding: 9px 12px;
            font-size: 12px;
        }

        .workspace-table thead th {
            background: #f8fbff;
            color: #334155;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .04em;
            border-bottom-width: 1px;
        }

        .workspace-table-wrap {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 18px;
        }

        .workspace-table tbody tr td {
            vertical-align: middle;
        }

        .workspace-table tbody tr:hover {
            background: #fbfdff;
        }

        .workspace-product-thumb {
            width: 52px;
            height: 52px;
            object-fit: cover;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            background: #fff;
            padding: 4px;
        }

        .workspace-variant-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .workspace-variant-chip {
            display: inline-flex;
            align-items: center;
            padding: 4px 9px;
            border-radius: 999px;
            background: #eef4ff;
            color: #3655a3;
            font-size: 11px;
            font-weight: 600;
        }

        .workspace-form-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            font-weight: 700;
        }

        .qty-cart.vcart-qty .quantity {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #f8fbff;
            border: 1px solid #dce7f5;
            border-radius: 999px;
            padding: 6px;
        }

        .qty-cart.vcart-qty .quantity .minus,
        .qty-cart.vcart-qty .quantity .plus {
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 999px;
            background: #fff;
            color: #0f172a;
            font-weight: 800;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
        }

        .qty-cart.vcart-qty .quantity input {
            width: 36px;
            border: 0;
            background: transparent;
            text-align: center;
            font-weight: 700;
            color: #0f172a;
        }

        .discount input.product_discount {
            min-width: 88px;
            border-radius: 12px;
            border: 1px solid #d8e3f2;
            background: #fff;
            padding: 8px 10px;
            font-weight: 600;
        }

        .cart_remove {
            border-radius: 12px;
            padding: 8px 10px;
        }

        .tracking-card {
            background: linear-gradient(135deg, #f8fbff 0%, #eef7ff 100%);
            border: 1px solid #d7e8ff;
        }

        .tracking-shell {
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            border: 1px solid #d9e7fb;
            border-radius: 22px;
            padding: 24px;
        }

        .tracking-brand {
            font-family: "Poppins", "Cerebri Sans", sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .tracking-brand.pathao {
            background: #e8fff1;
            color: #0f8f53;
        }

        .tracking-brand.steadfast {
            background: #fff4dd;
            color: #a15c00;
        }

        .tracking-brand.pending {
            background: #eef2ff;
            color: #3949ab;
        }

        .tracking-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .tracking-stat {
            border: 1px solid #e4ebf5;
            border-radius: 16px;
            background: #fff;
            padding: 16px;
        }

        .tracking-stat-label {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .tracking-stat-value {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
        }

        .tracking-timeline {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        .tracking-step {
            position: relative;
            border-radius: 18px;
            padding: 18px;
            background: #fff;
            border: 1px solid #e5e7eb;
            min-height: 130px;
        }

        .tracking-step.done {
            border-color: #b8ebcb;
            background: linear-gradient(180deg, #f4fff8 0%, #ebfff3 100%);
        }

        .tracking-step.current {
            border-color: #86b7fe;
            background: linear-gradient(180deg, #f6faff 0%, #ecf4ff 100%);
            box-shadow: 0 10px 24px rgba(59, 130, 246, 0.10);
        }

        .tracking-step-index {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            background: #edf2f7;
            color: #475569;
            margin-bottom: 12px;
        }

        .tracking-step.done .tracking-step-index {
            background: #22c55e;
            color: #fff;
        }

        .tracking-step.current .tracking-step-index {
            background: #2563eb;
            color: #fff;
        }

        .tracking-step-title {
            font-family: "Playfair Display", Georgia, serif;
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .tracking-step-text {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
        }

        .tracking-note {
            border: 1px dashed #c8d6ea;
            border-radius: 16px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.75);
            margin-top: 18px;
        }

        .invoice-print-frame {
            position: absolute;
            width: 0;
            height: 0;
            border: 0;
            visibility: hidden;
        }

        @media (max-width: 1199px) {
            .workspace-stat-strip,
            .tracking-grid,
            .tracking-timeline {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767px) {
            .page-title-box {
                display: flex;
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }

            .page-title-box .page-title-right {
                width: 100%;
                margin: 0;
            }

            .page-title-box .page-title-right .btn {
                width: 100%;
            }

            .workspace-panel {
                padding: 16px;
                border-radius: 20px;
                background:
                    radial-gradient(circle at top right, rgba(59, 130, 246, 0.1), transparent 34%),
                    linear-gradient(180deg, #ffffff 0%, #f6fbff 100%);
            }

            .workspace-hero-title {
                font-size: 22px;
                line-height: 1.15;
            }

            .workspace-hero-text {
                font-size: 14px;
                line-height: 1.6;
                max-width: none;
            }

            .workspace-meta {
                gap: 8px;
                margin-bottom: 16px;
            }

            .workspace-meta .badge {
                width: 100%;
                text-align: left;
                display: flex;
                justify-content: space-between;
                padding: 10px 12px;
            }

            .workspace-action-bar .btn,
            .workspace-tabs .nav-link {
                width: 100%;
                justify-content: center;
                min-height: 46px;
            }

            .workspace-card .card-body,
            .tracking-shell {
                padding: 16px;
            }

            .workspace-card {
                border-radius: 20px;
            }

            .workspace-sticky {
                position: static;
            }

            .workspace-section-title {
                font-size: 20px;
            }

            .workspace-section-subtitle {
                font-size: 13px;
                line-height: 1.5;
            }

            .workspace-table-wrap {
                overflow: visible;
                border-radius: 0;
            }

            .workspace-table {
                min-width: 0;
                border: 0;
                margin-bottom: 0;
            }

            .workspace-table thead {
                display: none;
            }

            .workspace-table,
            .workspace-table tbody,
            .workspace-table tr,
            .workspace-table td {
                display: block;
                width: 100%;
            }

            .workspace-table tbody {
                display: grid;
                gap: 14px;
            }

            .workspace-table tbody tr {
                border: 1px solid #dce7f5;
                border-radius: 20px;
                background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
                padding: 14px;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            }

            .workspace-table tbody tr td {
                border: 0;
                padding: 0;
                background: transparent;
            }

            .workspace-table tbody tr td:nth-child(1) {
                margin-bottom: 12px;
            }

            .workspace-table tbody tr td:nth-child(2) {
                margin-bottom: 14px;
            }

            .workspace-table tbody tr td:nth-child(n+3) {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 10px 0;
                border-top: 1px dashed #dbe6f3;
            }

            .workspace-table tbody tr td:nth-child(n+3)::before {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: .08em;
                color: #64748b;
                font-weight: 700;
                flex: 0 0 auto;
            }

            .workspace-table tbody tr td:nth-child(3)::before {
                content: "Quantity";
            }

            .workspace-table tbody tr td:nth-child(4)::before {
                content: "Sell Price";
            }

            .workspace-table tbody tr td:nth-child(5)::before {
                content: "Discount";
            }

            .workspace-table tbody tr td:nth-child(6)::before {
                content: "Subtotal";
            }

            .workspace-table tbody tr td:nth-child(7)::before {
                content: "Action";
            }

            .workspace-table tbody tr td:nth-child(7) {
                justify-content: space-between;
            }

            .workspace-product-thumb {
                width: 64px;
                height: 64px;
                border-radius: 16px;
                margin-bottom: 6px;
            }

            .workspace-variant-list {
                gap: 5px;
                margin-top: 10px;
            }

            .workspace-summary-wrap {
                margin-top: 16px;
                padding: 14px;
                border-radius: 18px;
            }

            .workspace-summary-table td {
                font-size: 14px;
            }

            .qty-cart.vcart-qty .quantity {
                gap: 4px;
                padding: 4px;
                margin-left: auto;
            }

            .qty-cart.vcart-qty .quantity .minus,
            .qty-cart.vcart-qty .quantity .plus {
                width: 32px;
                height: 32px;
            }

            .qty-cart.vcart-qty .quantity input {
                width: 32px;
                font-size: 14px;
            }

            .discount input.product_discount {
                min-width: 84px;
                max-width: 110px;
                padding: 8px 10px;
                font-size: 14px;
                margin-left: auto;
                text-align: right;
            }

            .cart_remove {
                min-width: 42px;
                min-height: 42px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-left: auto;
            }

            .pos_search {
                display: flex;
                gap: 10px;
                align-items: center;
            }

            .pos_search input {
                min-height: 46px;
                border-radius: 14px;
                font-size: 14px;
            }

            .pos_search button {
                min-width: 46px;
                min-height: 46px;
                border-radius: 14px;
            }

            .tracking-brand {
                width: 100%;
                justify-content: center;
            }

            .tracking-stat {
                padding: 14px;
                border-radius: 14px;
            }

            .tracking-step {
                min-height: 0;
                padding: 16px;
                border-radius: 16px;
            }

            .tracking-note {
                font-size: 13px;
                padding: 14px;
                border-radius: 14px;
            }

            .modal-dialog {
                margin: 12px;
            }

            .modal-content {
                border-radius: 18px;
            }

            .workspace-stat-strip,
            .tracking-grid,
            .tracking-timeline {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <link href="{{ asset('backEnd') }}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backEnd') }}/assets/libs/summernote/summernote-lite.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    @php
        $activeTab = request('tab', 'manage');
    @endphp

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <button type="button" class="btn btn-outline-primary rounded-pill js-direct-invoice-print"
                            data-print-url="{{ route('admin.order.invoice_print', ['invoice_id' => $order->invoice_id]) }}">
                            <i class="fa fa-print"></i> Print Invoice
                        </button>
                    </div>
                    <h4 class="page-title">Order Workspace</h4>
                </div>
            </div>
        </div>

        <div class="workspace-panel mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <div class="workspace-hero-title">Invoice #{{ $order->invoice_id }}</div>
                    <p class="workspace-hero-text">Centralized order desk for pricing, delivery, courier actions, tracking, and invoice operations.</p>
                </div>
                <div class="workspace-meta">
                    <span class="badge bg-primary">Status: {{ optional($orderstatus->where('id', $order->order_status)->first())->name ?? 'N/A' }}</span>
                    <span class="badge bg-success">Amount: ৳{{ $order->amount }}</span>
                    <span class="badge {{ $order->marketing_source_badge_class }}">Source: {{ $order->marketing_source_label }}</span>
                    <span class="badge bg-secondary">Courier: {{ $order->courier ?: 'Not assigned' }}</span>
                    @if ($order->tracking_id)
                        <span class="badge bg-info text-dark">Tracking: {{ $order->tracking_id }}</span>
                    @endif
                </div>
            </div>

            <div class="workspace-stat-strip">
                <div class="workspace-stat-card">
                    <div class="workspace-stat-label">Customer</div>
                    <div class="workspace-stat-value">{{ $order->shipping?->name ?: 'Guest Customer' }}</div>
                </div>
                <div class="workspace-stat-card">
                    <div class="workspace-stat-label">Phone</div>
                    <div class="workspace-stat-value">
                        {{ $order->shipping?->phone ?: 'N/A' }}
                        @if($order->shipping?->phone)
                        <div class="mt-1">
                            <a href="https://api.whatsapp.com/send?phone=88{{ $order->shipping->phone }}" target="_blank" class="btn btn-xs btn-success" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                            <a href="tel:{{ $order->shipping->phone }}" class="btn btn-xs btn-primary" title="Call"><i class="fe-phone"></i></a>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="workspace-stat-card">
                    <div class="workspace-stat-label">Payment</div>
                    <div class="workspace-stat-value">{{ $order->payment?->payment_method ?: 'Cash On Delivery' }}</div>
                </div>
                @php
                    $workspacePaidAmount = (float) ($order?->payment?->amount ?? 0);
                    $workspaceDueAmount = max(0, (float) ($order?->amount ?? 0) - $workspacePaidAmount);
                @endphp
                <div class="workspace-stat-card">
                    <div class="workspace-stat-label">Paid</div>
                    <div class="workspace-stat-value">৳{{ number_format($workspacePaidAmount, 2, '.', '') }}</div>
                </div>
                <div class="workspace-stat-card">
                    <div class="workspace-stat-label">Due</div>
                    <div class="workspace-stat-value">৳{{ number_format($workspaceDueAmount, 2, '.', '') }}</div>
                </div>
                <div class="workspace-stat-card">
                    <div class="workspace-stat-label">Source</div>
                    <div class="workspace-stat-value">{{ $order->marketing_source_label }}</div>
                </div>
                <div class="workspace-stat-card">
                    <div class="workspace-stat-label">Updated</div>
                    <div class="workspace-stat-value">{{ $order->updated_at?->format('d M Y, h:i A') }}</div>
                </div>
            </div>

            <div class="workspace-action-bar">
                <button type="button" class="btn btn-outline-primary rounded-pill js-direct-invoice-print"
                    data-print-url="{{ route('admin.order.invoice_print', ['invoice_id' => $order->invoice_id]) }}">
                    <i class="fa fa-print"></i> Print Invoice
                </button>
                @if ($processOrder->order_status < 5)
                    <a href="{{ route('admin.order.steadfast', ['order_id' => $processOrder->id]) }}"
                        class="btn btn-warning rounded-pill">
                        <i class="fe-truck"></i> Send To Steadfast
                    </a>
                @endif
                <button type="button" class="btn btn-primary rounded-pill" data-bs-toggle="modal"
                    data-bs-target="#pathaoWorkspaceModal">
                    <i class="fe-truck"></i> Send To Pathao
                </button>
            </div>

            {{-- <div class="alert alert-light border mt-3 mb-0" role="alert">
                <strong>Source Guide:</strong>
                Facebook = Meta/Facebook/Instagram, TikTok = TikTok traffic, Google = paid Google campaigns, Organic = search engine traffic, direct visits, and external referrals, Admin Panel = manually created orders.
            </div> --}}

            <ul class="nav nav-pills workspace-tabs gap-2 mt-4">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'manage' ? 'active' : '' }}"
                        href="{{ route('admin.order.workspace', ['invoice_id' => $order->invoice_id, 'tab' => 'manage']) }}">
                        Manage Order
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'invoice' ? 'active' : '' }}"
                        href="{{ route('admin.order.workspace', ['invoice_id' => $order->invoice_id, 'tab' => 'invoice']) }}">
                        Invoice Preview
                    </a>
                </li>
            </ul>
        </div>

        @if ($activeTab === 'invoice')
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Invoice Preview</h5>
                        <button type="button" class="btn btn-success js-direct-invoice-print"
                            data-print-url="{{ route('admin.order.invoice_print', ['invoice_id' => $order->invoice_id]) }}">
                            <i class="fa fa-print"></i> Open Printable Invoice
                        </button>
                    </div>
                    <div class="workspace-invoice-preview">
                        @include('backEnd.order.partials.invoice-content', ['order' => $order])
                    </div>
                </div>
            </div>
        @else
            @php
                $currentStatus = optional($orderstatus->where('id', $order->order_status)->first())->name ?? 'Pending';
                $courierType = strtolower((string) ($order->courier ?: 'pending'));
                $isDelivered = (int) $order->order_status === 6 || str_contains(strtolower($currentStatus), 'deliver');
                $hasTracking = !empty($order->tracking_id);

                if ($courierType === 'pathao') {
                    $trackingSteps = [
                        ['title' => 'Order Confirmed', 'text' => 'Order has been prepared and is waiting for courier booking.'],
                        ['title' => 'Pathao Booked', 'text' => 'Consignment created in Pathao and awaiting pickup.'],
                        ['title' => 'In Transit', 'text' => 'Parcel is moving through the Pathao delivery network.'],
                        ['title' => 'Delivered', 'text' => 'Parcel has reached the customer successfully.'],
                    ];
                    $brandClass = 'pathao';
                    $brandLabel = 'Pathao Tracking';
                } elseif ($courierType === 'steadfast') {
                    $trackingSteps = [
                        ['title' => 'Order Ready', 'text' => 'Order is packed and ready for courier dispatch.'],
                        ['title' => 'Steadfast Pickup', 'text' => 'Steadfast consignment has been created and pickup is expected.'],
                        ['title' => 'On Delivery Route', 'text' => 'Steadfast is transporting the parcel to the destination area.'],
                        ['title' => 'Delivered', 'text' => 'Customer delivery has been completed.'],
                    ];
                    $brandClass = 'steadfast';
                    $brandLabel = 'Steadfast Tracking';
                } else {
                    $trackingSteps = [
                        ['title' => 'Order Received', 'text' => 'Order has been received in the admin panel.'],
                        ['title' => 'Preparing Shipment', 'text' => 'Courier entry is pending and packaging is in progress.'],
                        ['title' => 'Awaiting Dispatch', 'text' => 'Order will move to courier tracking once dispatched.'],
                        ['title' => 'Delivered', 'text' => 'Final delivery status will appear here.'],
                    ];
                    $brandClass = 'pending';
                    $brandLabel = 'Internal Tracking';
                }

                if ($isDelivered) {
                    $currentStepIndex = 3;
                } elseif ($hasTracking) {
                    $currentStepIndex = 2;
                } elseif ((int) $order->order_status >= 2) {
                    $currentStepIndex = 1;
                } else {
                    $currentStepIndex = 0;
                }
            @endphp

            <div class="row g-4">
                <div class="col-xl-4">
                    <div class="workspace-sticky">
                    <div class="card workspace-card workspace-control-card mb-4">
                        <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                    <div>
                                        <h5 class="workspace-section-title">Order Controls</h5>
                                        <p class="workspace-section-subtitle">Customer, delivery, status, and courier actions in one place.</p>
                                    </div>
                                </div>

                                <div class="workspace-compact-badges mb-4">
                                    <span class="badge bg-primary">Status: {{ optional($orderstatus->where('id', $order->order_status)->first())->name ?? 'N/A' }}</span>
                                    <span class="badge {{ $order->marketing_source_badge_class }}">Source: {{ $order->marketing_source_label }}</span>
                                    <span class="badge bg-secondary">Courier: {{ $order->courier ?: 'Not assigned' }}</span>
                                    @if ($order->tracking_id)
                                        <span class="badge bg-info text-dark">Tracking: {{ $order->tracking_id }}</span>
                                    @endif
                                </div>

                                <form action="{{ route('admin.order_change') }}" method="POST" class="row g-3"
                                    data-parsley-validate="" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $processOrder->id }}">
                                    <input type="hidden" name="workspace_invoice_id" value="{{ $order->invoice_id }}">

                                    <div class="col-12">
                                        <label for="process_name" class="form-label workspace-form-label">Customer Name</label>
                                        <input type="text" class="form-control order-sync-field" name="name" id="process_name"
                                            data-sync-target="#order_update_name" value="{{ $order->shipping?->name }}" placeholder="Name">
                                    </div>

                                    <div class="col-12">
                                        <label for="process_phone" class="form-label workspace-form-label">Customer Phone</label>
                                        <input type="text" class="form-control order-sync-field" name="phone" id="process_phone"
                                            data-sync-target="#order_update_phone" value="{{ $order->shipping?->phone }}" placeholder="Phone Number">
                                    </div>

                                    <div class="col-12">
                                        <label for="process_marketing_source" class="form-label workspace-form-label">Order Source</label>
                                        <select class="form-control" name="marketing_source" id="process_marketing_source">
                                            <option value="admin" @selected(($order->marketing_source ?: 'admin') === 'admin')>Admin Panel</option>
                                            <option value="facebook" @selected($order->marketing_source === 'facebook')>Facebook</option>
                                            <option value="messenger" @selected($order->marketing_source === 'messenger')>Messenger</option>
                                            <option value="whatsapp" @selected($order->marketing_source === 'whatsapp')>WhatsApp</option>
                                            <option value="tiktok" @selected($order->marketing_source === 'tiktok')>TikTok</option>
                                            <option value="google" @selected($order->marketing_source === 'google')>Google</option>
                                            <option value="organic" @selected(in_array($order->marketing_source, ['organic', 'direct', 'referral'], true))>Organic</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label for="process_address" class="form-label workspace-form-label">Customer Address</label>
                                        <textarea name="address" id="process_address" class="form-control order-sync-field"
                                            data-sync-target="#order_update_address">{{ $order->shipping?->address }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <label for="process_area" class="form-label workspace-form-label">Delivery Area</label>
                                        <select id="process_area" class="form-control order-sync-field" name="area"
                                            data-sync-target="#order_update_area" required>
                                            @foreach ($shippingcharge as $value)
                                                <option value="{{ $value->id }}"
                                                    @if (($order->shipping?->area ?? '') == $value->name) selected @endif>
                                                    {{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label workspace-form-label">Order Status</label>
                                        <select class="form-control select2" name="status" required>
                                            <option value="">Select..</option>
                                            @foreach ($orderstatus as $value)
                                                <option value="{{ $value->id }}" @if ($order->order_status == $value->id) selected @endif>
                                                    {{ $value->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success w-100">Update Status & Delivery</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card workspace-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <h5 class="workspace-section-title">Items & Pricing</h5>
                                    <p class="workspace-section-subtitle">Edit products, quantity, discounts, and final amount.</p>
                                </div>
                                @if ($processOrder->tracking_id)
                                    <span class="badge bg-info text-dark align-self-center">
                                        {{ $processOrder->courier == 'pathao' ? 'Pathao' : 'Steadfast' }} - {{ $processOrder->tracking_id }}
                                    </span>
                                @endif
                            </div>

                            <div class="form-group mb-4">
                                <label for="product_id" class="form-label workspace-form-label">Add Product</label>
                                <div class="pos_search">
                                    <input type="text" placeholder="Search Product or Scan Barcode ..." value=""
                                        class="search_click" name="keyword" autofocus />
                                    <button><i data-feather="search"></i></button>
                                </div>
                                <div class="search_result"></div>
                            </div>

                            <form action="{{ route('admin.order.update') }}" method="POST" class="row pos_form"
                                data-parsley-validate="" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" value="{{ $order->id }}" name="order_id">
                                <input type="hidden" name="context" value="workspace">
                                <input type="hidden" id="order_update_name" name="name" value="{{ $shippinginfo->name }}">
                                <input type="hidden" id="order_update_phone" name="phone" value="{{ $shippinginfo->phone }}">
                                <input type="hidden" id="order_update_address" name="address" value="{{ $shippinginfo->address }}">
                                <input type="hidden" id="order_update_area" name="area" value="{{ optional($shippingcharge->firstWhere('name', $shippinginfo->area))->id }}">

                                <div class="col-12">
                                    <div class="workspace-table-wrap">
                                    <table class="table table-bordered table-responsive-sm align-middle workspace-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 12%">Image</th>
                                                <th style="width: 28%">Name</th>
                                                <th style="width: 14%">Quantity</th>
                                                <th style="width: 14%">Sell Price</th>
                                                <th style="width: 12%">Discount</th>
                                                <th style="width: 12%">Sub Total</th>
                                                <th style="width: 8%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cartTable">
                                            @php $product_discount = 0; @endphp
                                            @foreach ($cartinfo as $value)
                                                <tr data-row-id="{{ $value->rowId }}">
                                                    <td>
                                                        <img class="workspace-product-thumb" src="{{ asset($value->options->image) }}">
                                                    </td>
                                                    <td>
                                                        <div class="fw-semibold">{{ $value->name }}</div>
                                                        @php $selectedAttributes = $value->options->selected_attributes ?? []; @endphp
                                                        @if (!empty($selectedAttributes))
                                                            <div class="workspace-variant-list">
                                                                @foreach ($selectedAttributes as $selectedAttribute)
                                                                    <span class="workspace-variant-chip">{{ $selectedAttribute['attribute'] ?? '' }}: {{ $selectedAttribute['value'] ?? '' }}</span>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="workspace-variant-list">
                                                                @if ($value->options->product_size)
                                                                    <span class="workspace-variant-chip">Size: {{ $value->options->product_size }}</span>
                                                                @endif
                                                                @if ($value->options->product_color)
                                                                    <span class="workspace-variant-chip">Color: {{ $value->options->product_color }}</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="qty-cart vcart-qty">
                                                            <div class="quantity">
                                                                <button type="button" class="minus cart_decrement" value="{{ $value->qty }}" data-id="{{ $value->rowId }}">-</button>
                                                                <input type="text" value="{{ $value->qty }}" readonly />
                                                                <button type="button" class="plus cart_increment" value="{{ $value->qty }}" data-id="{{ $value->rowId }}">+</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $value->price }}</td>
                                                    <td class="discount">
                                                        <input type="text" inputmode="decimal" class="product_discount" value="{{ $value->options->product_discount ?? 0 }}"
                                                            placeholder="0.00" data-id="{{ $value->rowId }}" />
                                                    </td>
                                                    <td>{{ ($value->price - ($value->options->product_discount ?? 0)) * $value->qty }}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger btn-xs cart_remove" data-id="{{ $value->rowId }}"><i class="fa fa-times"></i></button>
                                                    </td>
                                                </tr>
                                                @php
                                                    $product_discount += ($value->options->product_discount ?? 0) * $value->qty;
                                                @endphp
                                            @endforeach
                                        </tbody>
                                    </table>
                                    </div>
                                </div>

                                <div class="col-md-6 ms-auto">
                                    <div class="workspace-summary-wrap">
                                        <table class="table table-bordered workspace-summary-table mb-0">
                                            <tbody id="cart_details">
                                                @php
                                                    $subtotal = Cart::instance('workspace_shopping')->subtotal();
                                                    $subtotal = str_replace(',', '', $subtotal);
                                                    $subtotal = str_replace('.00', '', $subtotal);
                                                    $shipping = Session::get('workspace_pos_shipping');
                                                    $total_discount = Session::get('workspace_pos_discount', 0) + Session::get('workspace_product_discount', 0);
                                                @endphp
                                                <tr>
                                                    <td>Sub Total</td>
                                                    <td>{{ $subtotal }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Shipping Fee</td>
                                                    <td>{{ $shipping }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Discount</td>
                                                    <td>{{ $total_discount }}</td>
                                                </tr>
                                                <tr>
                                                    <td>Total</td>
                                                    <td>{{ $subtotal + $shipping - $total_discount }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">Update Items & Pricing</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="pathaoWorkspaceModal" tabindex="-1" aria-labelledby="pathaoWorkspaceModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="pathaoWorkspaceModalLabel">Pathao Courier - {{ $order->invoice_id }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.order.pathao') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" value="{{ $order->id }}">

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="workspace_pathaostore" class="form-label">Store</label>
                                        <select name="pathaostore" id="workspace_pathaostore" class="pathaostore form-control" required>
                                            <option value="">Select Store...</option>
                                            @if (isset($pathaostore['data']['data']))
                                                @foreach ($pathaostore['data']['data'] as $store)
                                                    <option value="{{ $store['store_id'] }}">{{ $store['store_name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label for="workspace_pathaocity" class="form-label">City</label>
                                        <select name="pathaocity" id="workspace_pathaocity" class="pathaocity form-control" required>
                                            <option value="">Select City...</option>
                                            @if (isset($pathaocities['data']['data']))
                                                @foreach ($pathaocities['data']['data'] as $city)
                                                    <option value="{{ $city['city_id'] }}">{{ $city['city_name'] }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label for="workspace_pathaozone" class="form-label">Zone</label>
                                        <select name="pathaozone" id="workspace_pathaozone" class="pathaozone form-control" required>
                                            <option value="">Select Zone...</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label for="workspace_pathaoarea" class="form-label">Area</label>
                                        <select name="pathaoarea" id="workspace_pathaoarea" class="pathaoarea form-control" required>
                                            <option value="">Select Area...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fe-truck"></i> Send To Pathao
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card workspace-card tracking-card mt-4">
                <div class="card-body">
                    <div class="tracking-shell">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div>
                                <span class="tracking-brand {{ $brandClass }}">{{ $brandLabel }}</span>
                                <h5 class="workspace-section-title mt-3 mb-1">Tracking Overview</h5>
                                <p class="workspace-section-subtitle">
                                    @if ($order->tracking_id)
                                        Courier-aware tracking UI for this order.
                                    @else
                                        Courier entry hole ekhane live-like tracking stages dekha jabe.
                                    @endif
                                </p>
                            </div>

                            @if ($order->courier === 'pathao' && $order->tracking_id && $order->shipping?->phone)
                                <a href="https://merchant.pathao.com/tracking?consignment_id={{ $order->tracking_id }}&phone={{ $order->shipping->phone }}"
                                    target="_blank" class="btn btn-primary rounded-pill">
                                    <i class="fe-external-link"></i> Open Official Tracking
                                </a>
                            @endif
                        </div>

                        <div class="tracking-grid mt-4">
                            <div class="tracking-stat">
                                <div class="tracking-stat-label">Courier</div>
                                <div class="tracking-stat-value text-capitalize">{{ $order->courier ?: 'Pending Assignment' }}</div>
                            </div>
                            <div class="tracking-stat">
                                <div class="tracking-stat-label">Tracking ID</div>
                                <div class="tracking-stat-value">{{ $order->tracking_id ?: 'Not Generated Yet' }}</div>
                            </div>
                            <div class="tracking-stat">
                                <div class="tracking-stat-label">Current Status</div>
                                <div class="tracking-stat-value">{{ $currentStatus }}</div>
                            </div>
                            <div class="tracking-stat">
                                <div class="tracking-stat-label">Source</div>
                                <div class="tracking-stat-value">{{ $order->marketing_source_label }}</div>
                            </div>
                            <div class="tracking-stat">
                                <div class="tracking-stat-label">Customer Phone</div>
                                <div class="tracking-stat-value">{{ $order->shipping?->phone ?: 'Unavailable' }}</div>
                            </div>
                        </div>

                        <div class="tracking-timeline">
                            @foreach ($trackingSteps as $index => $step)
                                @php
                                    $stepClass = '';
                                    if ($index < $currentStepIndex) {
                                        $stepClass = 'done';
                                    } elseif ($index === $currentStepIndex) {
                                        $stepClass = 'current';
                                    }
                                @endphp
                                <div class="tracking-step {{ $stepClass }}">
                                    <div class="tracking-step-index">{{ $index + 1 }}</div>
                                    <div class="tracking-step-title">{{ $step['title'] }}</div>
                                    <div class="tracking-step-text">{{ $step['text'] }}</div>
                                </div>
                            @endforeach
                        </div>

                        <div class="tracking-note">
                            @if ($order->courier === 'pathao')
                                <strong>Pathao mode:</strong> Courier booked hole ei section Pathao-specific stages dekhabe. Official tracking link upore thakbe.
                            @elseif ($order->courier === 'steadfast')
                                <strong>Steadfast mode:</strong> Steadfast courier flow onujayi pickup, route, ar delivery stages dekhano hocche.
                            @else
                                <strong>Pending mode:</strong> Ekhono courier assign hoy nai. Pathao ba Steadfast-e pathale tracking UI automatically courier-specific feel nibe.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <iframe id="invoicePrintFrame" class="invoice-print-frame" title="Invoice Print Frame"></iframe>
@endsection

@section('script')
    <script src="{{ asset('backEnd/') }}/assets/libs/parsleyjs/parsley.min.js"></script>
    <script src="{{ asset('backEnd/') }}/assets/js/pages/form-validation.init.js"></script>
    <script src="{{ asset('backEnd/') }}/assets/libs/select2/js/select2.min.js"></script>
    <script src="{{ asset('backEnd/') }}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{ asset('backEnd/') }}/assets/libs//summernote/summernote-lite.min.js"></script>
    <script>
        $(".summernote").summernote({
            placeholder: "Enter Your Text Here",
        });

        $(document).ready(function() {
            $(".select2").select2();
            $(".search_click").focus();
            $(".order-sync-field").each(function() {
                var target = $(this).data("sync-target");
                if (target) {
                    $(target).val($(this).val());
                }
            });

            $(document).on("click", ".js-direct-invoice-print", function() {
                var printUrl = $(this).data("print-url");
                var printFrame = document.getElementById("invoicePrintFrame");

                if (!printUrl || !printFrame) {
                    return;
                }

                printFrame.onload = function() {
                    setTimeout(function() {
                        printFrame.contentWindow.focus();
                        printFrame.contentWindow.print();
                    }, 250);
                };

                printFrame.src = printUrl;
            });
        });
    </script>

    @if ($activeTab === 'manage')
        <script>
            function cart_content(done) {
                return $.ajax({
                    type: "GET",
                    url: "{{ route('admin.order.cart_content', ['context' => 'workspace']) }}",
                    dataType: "html",
                    success: function(cartinfo) {
                        $("#cartTable").html(cartinfo);

                        if (typeof done === "function") {
                            done();
                        }
                    },
                });
            }

            function cart_details() {
                return $.ajax({
                    type: "GET",
                    url: "{{ route('admin.order.cart_details', ['context' => 'workspace']) }}",
                    dataType: "html",
                    success: function(cartinfo) {
                        $("#cart_details").html(cartinfo);
                    },
                });
            }

            function restoreDiscountFocus(state) {
                if (!state || !state.rowId) {
                    return;
                }

                var $input = $('.product_discount[data-id="' + state.rowId + '"]');

                if (!$input.length) {
                    return;
                }

                var scrollTop = window.pageYOffset || document.documentElement.scrollTop || 0;
                $input.val(state.value);

                if (typeof $input[0].focus === "function") {
                    try {
                        $input[0].focus({
                            preventScroll: true
                        });
                    } catch (e) {
                        $input[0].focus();
                    }
                }

                if ($input[0].setSelectionRange) {
                    var end = String(state.value ?? "").length;
                    setTimeout(function() {
                        $input[0].setSelectionRange(end, end);
                        window.scrollTo(0, scrollTop);
                    }, 0);
                } else {
                    window.scrollTo(0, scrollTop);
                }
            }

            function refreshCartUI(focusState) {
                return $.when(cart_content(function() {
                    restoreDiscountFocus(focusState);
                }), cart_details());
            }

            function search_clear() {
                $.ajax({
                    type: "GET",
                    data: {
                        keyword: ''
                    },
                    url: "{{ route('admin.livesearch') }}",
                    success: function(products) {
                        if (products) {
                            $(".search_result").html(products);
                        } else {
                            $(".search_result").empty();
                        }
                    },
                });
            }

            $(document).on("input change", ".order-sync-field", function() {
                var target = $(this).data("sync-target");

                if (target) {
                    $(target).val($(this).val());
                }
            });

            $(".cart_add").on("click", function() {
                var id = $(this).data("id");

                if (id) {
                    $.ajax({
                        cache: false,
                        type: "GET",
                        data: {
                            id: id,
                            context: 'workspace'
                        },
                        url: "{{ route('admin.order.cart_add') }}",
                        dataType: "json",
                        success: function() {
                            refreshCartUI();
                            search_clear();
                        }
                    });
                }
            });

            $(document).on("click", ".cart_increment", function(e) {
                e.preventDefault();
                var id = $(this).data("id");
                var qty = parseInt($(this).closest(".quantity").find("input").val(), 10) || 1;

                if (id) {
                    $.ajax({
                        cache: false,
                        data: {
                            id: id,
                            qty: qty,
                            context: 'workspace'
                        },
                        type: "GET",
                        url: "{{ route('admin.order.cart_increment') }}",
                        dataType: "json",
                        success: function() {
                            refreshCartUI();
                        }
                    });
                }
            });

            $(document).on("click", ".cart_decrement", function(e) {
                e.preventDefault();
                var id = $(this).data("id");
                var qty = parseInt($(this).closest(".quantity").find("input").val(), 10) || 1;

                if (id) {
                    $.ajax({
                        cache: false,
                        type: "GET",
                        data: {
                            id: id,
                            qty: qty,
                            context: 'workspace'
                        },
                        url: "{{ route('admin.order.cart_decrement') }}",
                        dataType: "json",
                        success: function() {
                            refreshCartUI();
                        }
                    });
                }
            });

            $(document).on("click", ".cart_remove", function(e) {
                e.preventDefault();
                var id = $(this).data("id");

                if (id) {
                    $.ajax({
                        cache: false,
                        type: "GET",
                        data: {
                            id: id,
                            context: 'workspace'
                        },
                        url: "{{ route('admin.order.cart_remove') }}",
                        dataType: "json",
                        success: function() {
                            refreshCartUI();
                        }
                    });
                }
            });

            const productDiscountTimers = {};
            const syncedProductDiscounts = {};
            const productDiscountDelay = 3000;

            function syncProductDiscount(input, forceImmediate) {
                var $input = $(input);
                var rowId = $input.data("id");
                var discount = $input.val();
                var caret = input.selectionStart;
                var normalizedDiscount = String(discount ?? "");
                var focusState = {
                    rowId: rowId,
                    value: discount,
                    caret: caret
                };

                clearTimeout(productDiscountTimers[rowId]);

                if (forceImmediate && syncedProductDiscounts[rowId] === normalizedDiscount) {
                    return;
                }

                var request = function() {
                    $.ajax({
                        cache: false,
                        type: "GET",
                        data: {
                            id: rowId,
                            discount: discount,
                            context: 'workspace'
                        },
                        url: "{{ route('admin.order.product_discount') }}",
                        dataType: "json",
                        success: function(response) {
                            var nextRowId = response.rowId || rowId;
                            delete syncedProductDiscounts[rowId];
                            syncedProductDiscounts[nextRowId] = normalizedDiscount;
                            focusState.rowId = nextRowId;
                            refreshCartUI(focusState);
                        }
                    });
                };

                if (forceImmediate) {
                    request();
                    return;
                }

                productDiscountTimers[rowId] = setTimeout(request, productDiscountDelay);
            }

            $(document).on("input", ".product_discount", function() {
                syncProductDiscount(this, false);
            });

            $(document).on("keydown", ".product_discount", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    syncProductDiscount(this, true);
                }
            });

            $(document).on("blur", ".product_discount", function() {
                syncProductDiscount(this, true);
            });

            $("#process_area").on("change", function() {
                var id = $(this).val();

                $.ajax({
                    type: "GET",
                        data: {
                        id: id,
                        context: 'workspace'
                    },
                    url: "{{ route('admin.order.cart_shipping') }}",
                    dataType: "html",
                    success: function() {
                        refreshCartUI();
                    }
                });
            });
        </script>
    @endif
@endsection
