@extends('backEnd.layouts.master')
@section('title','Order Invoice')
@section('content')
<style>
    .customer-invoice {
        margin: 25px 0;
    }

    .invoice_btn {
        margin-bottom: 15px;
    }

    p {
        margin: 0;
    }

    td {
        font-size: 16px;
    }

    @page {
        margin: 0px;
    }

    @media print {
        .invoice-innter {
            margin-left: -120px !important;
        }

        .invoice_btn {
            margin-bottom: 0 !important;
        }

        td {
            font-size: 18px;
        }

        p {
            margin: 0;
        }

        header,
        footer,
        .no-print,
        .left-side-menu,
        .navbar-custom {
            display: none !important;
        }
    }
</style>
<section class="customer-invoice ">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <a href="" class="no-print"><strong><i class="fe-arrow-left"></i> Back To Order</strong></a>
            </div>
            <div class="col-sm-6">
                <button onclick="printFunction()" class="no-print btn btn-xs btn-success waves-effect waves-light"><i class="fa fa-print"></i></button>
            </div>
            <div class="col-sm-12 mt-3">
                @include('backEnd.order.partials.invoice-content', ['order' => $order])
            </div>
        </div>
    </div>
</section>
<script>
    function printFunction() {
        window.print();
    }
</script>
@endsection
