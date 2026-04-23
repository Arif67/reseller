@extends('frontEnd.layouts.master')
@section('title',$childcategory->meta_title)
@push('css')
@include('frontEnd.partials.product-card-theme')
<link rel="stylesheet" href="{{asset('public/frontEnd/css/jquery-ui.css')}}" />
@endpush
@push('seo')
<meta name="app-url" content="{{route('products',$childcategory->slug)}}" />
<meta name="robots" content="index, follow" />
<meta name="description" content="{{ $childcategory->meta_description}}" />
<meta name="keywords" content="{{ $childcategory->slug }}" />

<!-- Twitter Card data -->
<meta name="twitter:card" content="product" />
<meta name="twitter:site" content="{{$childcategory->childcategoryName}}" />
<meta name="twitter:title" content="{{$childcategory->childcategoryName}}" />
<meta name="twitter:description" content="{{ $childcategory->meta_description}}" />
<meta name="twitter:creator" content="gomobd.com" />
<meta property="og:url" content="{{route('products',$childcategory->slug)}}" />
<meta name="twitter:image" content="{{asset($childcategory->image)}}" />

<!-- Open Graph data -->
<meta property="og:title" content="{{$childcategory->childcategoryName}}" />
<meta property="og:type" content="product" />
<meta property="og:url" content="{{route('products',$childcategory->slug)}}" />
<meta property="og:image" content="{{asset($childcategory->image)}}" />
<meta property="og:description" content="{{ $childcategory->meta_description}}" />
<meta property="og:site_name" content="{{$childcategory->childcategoryName}}" />
@endpush
@section('content')
 <section class="homeproduct product-section">
        <div class="container">
            <div class="sorting-section">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="category-breadcrumb d-flex align-items-center">
                            <a href="{{ route('home') }}">Home</a>
                            <span>/</span>
                            <strong>{{ $childcategory->childcategoryName }}</strong>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="showing-data">
                                    <span>Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of
                                        {{ $products->total() }} Results</span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="filter_sort">
                                    <div class="filter_btn">
                                        <i class="fa fa-list-ul"></i>
                                    </div>
                                    <div class="page-sort">
                                        <form action="" class="sort-form">
                                            <select name="sort" class="form-control form-select sort">
                                                <option value="1" @if(request()->get('sort')==1)selected @endif>Product: Latest</option>
                                                <option value="2" @if(request()->get('sort')==2)selected @endif>Product: Oldest</option>
                                                <option value="3" @if(request()->get('sort')==3)selected @endif>Price: High To Low</option>
                                                <option value="4" @if(request()->get('sort')==4)selected @endif>Price: Low To High</option>
                                                <option value="5" @if(request()->get('sort')==5)selected @endif>Name: A-Z</option>
                                                <option value="6" @if(request()->get('sort')==6)selected @endif>Name: Z-A</option>
                                            </select>
                                            <input type="hidden" name="min_price" value="{{request()->get('min_price')}}" />
                                            <input type="hidden" name="max_price" value="{{request()->get('max_price')}}" />
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="product_sliders2 catalog-product-grid" data-infinite-list>
                        @foreach ($products as $key => $value)
                        @include('frontEnd.partials.product-card', ['product' => $value, 'titleLimit' => 56])
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="custom_paginate" data-infinite-pagination>
                        {{ $products->links('pagination::bootstrap-4') }}

                    </div>
                </div>
                <div class="infinite-scroll-loader" data-infinite-loader>Loading more products...</div>
            </div>
        </div>
    </section>
@include('frontEnd.partials.infinite-scroll')
@endsection

@push('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script>
        $("#price-range").click(function() {
            $(".price-submit").submit();
        })
        $(".form-attribute").on('change click',function(){
            $(".attribute-submit").submit();
        })
        $(".sort").change(function() {
            $(".sort-form").submit();
        })
        $(".form-checkbox").change(function() {
            $(".subcategory-submit").submit();
        })
    </script>
    <script>
        $(function() {
            $("#price-range").slider({
                step: 5,
                range: true,
                min: {{ $min_price }},
                max: {{ $max_price }},
                values: [
                    {{ request()->get('min_price') ? request()->get('min_price') : $min_price }},
                    {{ request()->get('max_price') ? request()->get('max_price') : $max_price }}
                ],
                slide: function(event, ui) {
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                }
            });
            $("#min_price").val({{ request()->get('min_price') ? request()->get('min_price') : $min_price }});
            $("#max_price").val({{ request()->get('max_price') ? request()->get('max_price') : $max_price }});
            $("#priceRange").val($("#price-range").slider("values", 0) + " - " + $("#price-range").slider("values",
                1));

            $("#mobile-price-range").slider({
                step: 5,
                range: true,
                min: {{ $min_price }},
                max: {{ $max_price }},
                values: [
                    {{ request()->get('min_price') ? request()->get('min_price') : $min_price }},
                    {{ request()->get('max_price') ? request()->get('max_price') : $max_price }}
                ],
                slide: function(event, ui) {
                    $("#min_price").val(ui.values[0]);
                    $("#max_price").val(ui.values[1]);
                }
            });
            $("#min_price").val({{ request()->get('min_price') ? request()->get('min_price') : $min_price }});
            $("#max_price").val({{ request()->get('max_price') ? request()->get('max_price') : $max_price }});
            $("#priceRange").val($("#price-range").slider("values", 0) + " - " + $("#price-range").slider("values",
                1));

        });
        const productSlider12Items = (typeof window.getSliderItems === 'function')
            ? window.getSliderItems('product_slider12', { mobile: 2, tablet: 4, desktop: 6 })
            : { mobile: 2, tablet: 4, desktop: 6 };
        $(".product_slider12").owlCarousel({
            margin: 15,
            items: productSlider12Items.desktop,
            loop: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 6000,
            autoplayHoverPause: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: productSlider12Items.mobile,
                    nav: false,
                },
                600: {
                    items: productSlider12Items.tablet,
                    nav: false,
                },
                1000: {
                    items: productSlider12Items.desktop,
                    nav: false,
                },
            },
        });
    </script>
@endpush
