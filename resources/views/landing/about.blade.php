@extends('landing.master')

@section('title', 'আমাদের সম্পর্কে | ' . ($generalsetting?->name ?? 'ShopBase BD'))

@section('content')
    @include('landing.partials._banner', [
        'crumb' => 'About Us',
        'title' => 'আমাদের সম্পর্কে',
        'text' => 'বিনা পুঁজিতে ঘরে বসে অনলাইন ব্যবসার দেশের সর্ববৃহৎ ড্রপশিপিং ও রিসেলিং প্ল্যাটফর্ম।',
    ])

    @include('landing.partials._about')
    @include('landing.partials._features')
    @include('landing.partials._experience')
    @include('landing.partials._cta')
@endsection
