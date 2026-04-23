@php
    $sliderLayout = (int) ($themeCustomization?->slider_layout ?? 1);
@endphp

@if ($sliderLayout === 2)
    @include('frontEnd.components.home.partials.hero-slider-layout-2')
@else
    @include('frontEnd.components.home.partials.hero-slider-layout-1')
@endif
