@extends('frontEnd.layouts.master')
@push('css')
    <link rel="stylesheet" href="{{ asset('frontEnd/css/jquery-ui.css') }}" />
@endpush
@push('seo')
    <style>
        .custom-container {
            max-width: var(--theme-container-width, 1520px);
            margin: 0 auto;
            padding-left: 15px;
            padding-right: 15px;
        }
    </style>
@endpush
@section('content')
    <section class="homeproduct product-section">
        <div class="custom-container">
            <div class="container container--narrow">
                <header class="page__header page__header--centered page__header--stack">
                    <h1 class="page__title heading h1">OUR STORES</h1>
                </header>

                <div class="page__content rte">

                    <div class="container">

                        <div class="row text-center">

                            @foreach ($data as $store)
                                <div class="col-md-4 mb-4">

                                    <div class="card shadow-sm h-100">
                                        <div class="card-body text-center">
                                            <h3 class="card-title mb-3">
                                                {{ $store->title }}
                                            </h3>
                                            <p class="card-text" style="white-space: pre-line;">
                                                {{ $store->description }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                    </div>


                </div>
            </div>



        </div>
    </section>
@endsection
@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
@endpush
