@php
    $productCategoryLayout = (int) ($themeCustomization?->product_category_layout ?? 1);
@endphp

@if ($productCategoryLayout === 2)
    @include('frontEnd.components.home.partials.featured-categories-layout-2')
@else
    @include('frontEnd.components.home.partials.featured-categories-layout-1')
@endif
