@extends('backEnd.layouts.master')
@section('title', 'Landing Hero')

@section('css')
<style>
    .hero-editor-page { max-width: 1000px; margin: 0 auto; padding-top: 30px; }
    .hero-editor-page .card { border: 0; border-radius: 16px; box-shadow: 0 10px 30px rgba(15,23,42,.08); margin-bottom: 22px; }
    .hero-editor-page .card-header { background: #fff; border-bottom: 1px solid #eef0f3; font-weight: 600; border-radius: 16px 16px 0 0; }
    .hero-editor-page .card-header small { display:block; color:#8a94a6; font-weight:400; }
    .hero-editor-head {
        border-radius: 18px; padding: 24px 26px; margin-bottom: 22px; color: #fff;
        background: linear-gradient(135deg, #FF6A00 0%, #E85D00 100%);
        box-shadow: 0 16px 40px rgba(232,93,0,.22);
    }
    .hero-editor-head h4 { margin: 0 0 4px; }
    .hero-editor-head p { margin: 0; opacity: .92; }
    .hero-color-row { display:flex; align-items:center; gap:12px; }
    .hero-color-row input[type=color] { width:54px; height:42px; padding:2px; border:1px solid #d8dce3; border-radius:8px; }
</style>
@endsection

@section('content')
@php
    $fields = [
        'content' => [
            'title' => 'Content',
            'note'  => 'Hero section er main text gula.',
            'items' => [
                ['hero_badge', 'Badge Text', '🚀 জিরো ইনভেস্টমেন্ট বিজনেস'],
                ['hero_title', 'Headline', 'দেশের সর্ববৃহৎ ড্রপশিপিং ও রিসেলিং প্ল্যাটফর্ম'],
                ['hero_highlight', 'Highlighted Word(s)', 'ড্রপশিপিং ও রিসেলিং', 'Headline er moddhe ei word gula orange highlight hobe.'],
                ['hero_subtitle', 'Sub Text', 'বিনা পুঁজিতে ঘরে বসে অনলাইন ব্যবসা শুরু করুন। ফ্রি রেজিস্ট্রেশন করে ১০,০০০+ ট্রেন্ডিং প্রোডাক্ট রিসেল করুন এবং ইনস্ট্যান্ট পেমেন্টে আয় করুন।', '', true],
            ],
        ],
        'buttons' => [
            'title' => 'Buttons',
            'note'  => 'Link khali rakhle primary button auto reseller register page e jabe.',
            'items' => [
                ['hero_primary_text', 'Primary Button Text', 'এখনই রেজিস্ট্রেশন করুন'],
                ['hero_primary_link', 'Primary Button Link', '', 'Khali rakhle reseller register page.'],
                ['hero_secondary_text', 'Secondary Button Text', 'আরও জানুন'],
                ['hero_secondary_link', 'Secondary Button Link', '#how'],
            ],
        ],
        'visual' => [
            'title' => 'Visual Card',
            'note'  => 'Hero er pasher card.',
            'items' => [
                ['hero_visual_title', 'Card Title', 'আপনার ব্যবসা, আপনার নিয়ন্ত্রণে'],
                ['hero_visual_text', 'Card Text', 'পুঁজি নেই? সমস্যা নেই। আমরা প্রোডাক্ট, ডেলিভারি ও পেমেন্ট সব সামলাই — আপনি শুধু সেল করুন।', '', true],
            ],
        ],
    ];

    $stats = [
        ['hero_stat1_value', 'hero_stat1_label', '০ টাকা', 'ইনভেস্টমেন্ট'],
        ['hero_stat2_value', 'hero_stat2_label', 'ইনস্ট্যান্ট', 'পেমেন্ট'],
        ['hero_stat3_value', 'hero_stat3_label', 'ক্যাশ অন', 'ডেলিভারি'],
        ['hero_stat4_value', 'hero_stat4_label', '২৪/৭', 'সাপোর্ট'],
    ];

    $bgColor = old('hero_bg_color', $themeCustomization->hero_bg_color ?? '#FFE790');
@endphp

<div class="hero-editor-page">
    <div class="hero-editor-head">
        <h4>Landing Page Hero</h4>
        <p>Reseller landing page er hero/banner section ekhan theke edit korun. Save korle sathe sathe site e update hobe.</p>
    </div>

    <form action="{{ route('theme.hero.update') }}" method="POST">
        @csrf

        @foreach ($fields as $group)
            <div class="card">
                <div class="card-header">
                    {{ $group['title'] }}
                    <small>{{ $group['note'] }}</small>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach ($group['items'] as $item)
                            @php
                                $name = $item[0];
                                $label = $item[1];
                                $default = $item[2] ?? '';
                                $help = $item[3] ?? '';
                                $isTextarea = $item[4] ?? false;
                                $value = old($name, $themeCustomization->{$name} ?? $default);
                            @endphp
                            <div class="{{ $isTextarea ? 'col-12' : 'col-md-6' }}">
                                <label for="{{ $name }}" class="form-label">{{ $label }}</label>
                                @if ($isTextarea)
                                    <textarea class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" rows="2">{{ $value }}</textarea>
                                @else
                                    <input type="text" class="form-control @error($name) is-invalid @enderror" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}">
                                @endif
                                @if ($help)<small class="text-muted">{{ $help }}</small>@endif
                                @error($name)<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-white text-end" style="border-top:1px solid #eef0f3;border-radius:0 0 16px 16px;">
                    <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
                </div>
            </div>
        @endforeach

        <div class="card">
            <div class="card-header">
                Background &amp; Mini Stats
                <small>Hero background color ar visual card er 4 ta choto stat.</small>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <label class="form-label">Background Color</label>
                    <div class="hero-color-row">
                        <input type="color" id="hero_bg_color_picker" value="{{ $bgColor }}">
                        <input type="text" class="form-control @error('hero_bg_color') is-invalid @enderror"
                            id="hero_bg_color" name="hero_bg_color" value="{{ $bgColor }}" style="max-width:180px">
                    </div>
                    @error('hero_bg_color')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                </div>

                <div class="row g-3">
                    @foreach ($stats as $i => $stat)
                        @php
                            $valueName = $stat[0]; $labelName = $stat[1];
                            $valVal = old($valueName, $themeCustomization->{$valueName} ?? $stat[2]);
                            $labVal = old($labelName, $themeCustomization->{$labelName} ?? $stat[3]);
                        @endphp
                        <div class="col-md-6">
                            <label class="form-label">Stat {{ $i + 1 }} (Value / Label)</label>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-control" name="{{ $valueName }}" value="{{ $valVal }}" placeholder="Value">
                                <input type="text" class="form-control" name="{{ $labelName }}" value="{{ $labVal }}" placeholder="Label">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="card-footer bg-white text-end" style="border-top:1px solid #eef0f3;border-radius:0 0 16px 16px;">
                <button type="submit" class="btn btn-success btn-sm">Save Changes</button>
            </div>
        </div>

        <div class="d-flex justify-content-start mb-5">
            <a href="{{ route('theme.customization.index') }}" class="btn btn-light">Back to Components</a>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
    (function () {
        const picker = document.getElementById('hero_bg_color_picker');
        const text = document.getElementById('hero_bg_color');
        if (picker && text) {
            picker.addEventListener('input', () => text.value = picker.value.toUpperCase());
            text.addEventListener('input', () => {
                if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(text.value)) picker.value = text.value;
            });
        }
    })();
</script>
@endsection
