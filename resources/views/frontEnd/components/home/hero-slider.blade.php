@php
    $sliderLayout = (int) ($themeCustomization?->slider_layout ?? 1);
@endphp

@if ($sliderLayout === 3)
    @include('frontEnd.components.home.partials.hero-slider-layout-3')
@elseif ($sliderLayout === 2)
    @include('frontEnd.components.home.partials.hero-slider-layout-2')
@else
    @include('frontEnd.components.home.partials.hero-slider-layout-1')
@endif
