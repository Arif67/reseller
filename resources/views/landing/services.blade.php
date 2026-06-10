@extends('landing.master')

@section('title', 'সার্ভিস | ' . ($generalsetting?->name ?? 'ShopBase BD'))

@section('content')
    @include('landing.partials._banner', [
        'crumb' => 'Services',
        'title' => 'আমাদের সার্ভিস',
        'text' => 'রিসেলিং থেকে শুরু করে হোলসেল, কাস্টম প্রিন্টিং, ফ্রিল্যান্সিং — সব আয়ের উৎস এক জায়গায়।',
    ])

    @include('landing.partials._services')
    @include('landing.partials._features')
    @include('landing.partials._cta')
@endsection
