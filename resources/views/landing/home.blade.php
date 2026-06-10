@extends('landing.master')

@section('content')
    @php
        $registerUrl = Route::has('reseller.register') ? route('reseller.register') : '#';

        // Admin-editable hero content (theme_customizations), with safe fallbacks.
        $tc = $themeCustomization ?? null;
        $heroBadge = $tc?->hero_badge ?: '🚀 জিরো ইনভেস্টমেন্ট বিজনেস';
        $heroTitle = $tc?->hero_title ?: 'দেশের সর্ববৃহৎ ড্রপশিপিং ও রিসেলিং প্ল্যাটফর্ম';
        $heroHighlight = $tc?->hero_highlight ?: 'ড্রপশিপিং ও রিসেলিং';
        $heroSubtitle = $tc?->hero_subtitle ?: 'বিনা পুঁজিতে ঘরে বসে অনলাইন ব্যবসা শুরু করুন। ফ্রি রেজিস্ট্রেশন করে ১০,০০০+ ট্রেন্ডিং প্রোডাক্ট রিসেল করুন এবং ইনস্ট্যান্ট পেমেন্টে আয় করুন।';
        $heroBg = $tc?->hero_bg_color ?: '#FFE790';
        $heroPrimaryText = $tc?->hero_primary_text ?: 'এখনই রেজিস্ট্রেশন করুন';
        $heroPrimaryLink = $tc?->hero_primary_link ?: $registerUrl;
        $heroSecondaryText = $tc?->hero_secondary_text ?: 'আরও জানুন';
        $heroSecondaryLink = $tc?->hero_secondary_link ?: '#how';
        $heroVisualTitle = $tc?->hero_visual_title ?: 'আপনার ব্যবসা, আপনার নিয়ন্ত্রণে';
        $heroVisualText = $tc?->hero_visual_text ?: 'পুঁজি নেই? সমস্যা নেই। আমরা প্রোডাক্ট, ডেলিভারি ও পেমেন্ট সব সামলাই — আপনি শুধু সেল করুন।';
        $heroStats = [
            [$tc?->hero_stat1_value ?: '০ টাকা', $tc?->hero_stat1_label ?: 'ইনভেস্টমেন্ট'],
            [$tc?->hero_stat2_value ?: 'ইনস্ট্যান্ট', $tc?->hero_stat2_label ?: 'পেমেন্ট'],
            [$tc?->hero_stat3_value ?: 'ক্যাশ অন', $tc?->hero_stat3_label ?: 'ডেলিভারি'],
            [$tc?->hero_stat4_value ?: '২৪/৭', $tc?->hero_stat4_label ?: 'সাপোর্ট'],
        ];

        // Wrap the highlighted phrase in a coloured span if present in the title.
        $heroTitleHtml = e($heroTitle);
        if ($heroHighlight !== '' && str_contains($heroTitle, $heroHighlight)) {
            $heroTitleHtml = str_replace(e($heroHighlight), '<span>' . e($heroHighlight) . '</span>', e($heroTitle));
        }
    @endphp

    <!-- ===== Hero ===== -->
    <section class="hero" style="background: {{ $heroBg }};">
        <div class="container">
            <div class="hero-copy">
                <span class="badge">{{ $heroBadge }}</span>
                <h1>{!! $heroTitleHtml !!}</h1>
                <p>{{ $heroSubtitle }}</p>
                <div class="hero-actions">
                    <a href="{{ $heroPrimaryLink }}" class="btn btn-primary"><i class="fa-solid fa-user-plus"></i>
                        {{ $heroPrimaryText }}</a>
                    <a href="{{ $heroSecondaryLink }}" class="btn btn-ghost">{{ $heroSecondaryText }}</a>
                </div>
            </div>

            <div class="hero-visual">
                <div class="hv-icon"><i class="fa-solid fa-box-open"></i></div>
                <h3 style="font-size:22px;margin-bottom:6px">{{ $heroVisualTitle }}</h3>
                <p style="opacity:.92;font-size:15px">{{ $heroVisualText }}</p>
                <div class="hv-grid">
                    @foreach ($heroStats as $stat)
                        <div><b>{{ $stat[0] }}</b><small>{{ $stat[1] }}</small></div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @include('landing.partials._stats')
    @include('landing.partials._about')
    @include('landing.partials._services')
    @include('landing.partials._features')
    @include('landing.partials._products')
    @include('landing.partials._how')
    @include('landing.partials._testimonials')
    @include('landing.partials._experience')
    @include('landing.partials._cta')
@endsection
