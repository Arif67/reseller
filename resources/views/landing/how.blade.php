@extends('landing.master')

@section('title', 'যেভাবে কাজ করবেন | ' . ($generalsetting?->name ?? 'ShopBase BD'))

@section('content')
    @include('landing.partials._banner', [
        'crumb' => 'How It Works',
        'title' => 'যেভাবে কাজ করবেন',
        'text' => 'মাত্র ৬টি সহজ ধাপে বিনা পুঁজিতে আপনার অনলাইন ব্যবসা শুরু করুন।',
    ])

    @include('landing.partials._how')
    @include('landing.partials._testimonials')
    @include('landing.partials._cta')
@endsection
