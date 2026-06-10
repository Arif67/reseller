@extends('resellerPanel.layouts.master')
@section('title', $product->name)

@section('css')
<style>
    .rpd-wrap { padding: 8px 6px 40px; }
    .rpd-card { border-radius: 10px; }
    .rpd-card .card-body { padding: 22px; }
    .rpd-main-img { height: 360px; object-fit: contain; border-radius: 8px; background:#f7f7f7; padding: 14px; width:100%; }
    .rpd-title { font-size: 20px; font-weight: 600; margin-bottom: 14px; line-height: 1.4; }
    .rpd-meta .btn { font-size: 13px; padding: 8px 6px; }
    .rpd-form-row { padding: 14px 0; border-bottom: 1px solid #f0f0f0; }
    .rpd-form-row:last-child { border-bottom: 0; }
    .rpd-label { font-weight: 600; margin-bottom: 8px; }
    .desc-title { font-weight:600; font-size:16px; border-bottom:1px solid #eee; padding-bottom:10px; margin-bottom:14px; }
    .desc-data { font-size:14px; color:#444; line-height:1.7; white-space:pre-line; }
    .rpd-thumb { width:100%; height:170px; object-fit:contain; background:#f7f7f7; border-radius:6px; padding:6px; }
    .rpd-gallery-item { padding:10px; transition:box-shadow .2s; height:100%; }
    .rpd-gallery-item:hover { box-shadow:0 0 14px rgba(0,0,0,.1); }
    .size-radio input { margin-right:4px; }
</style>
@endsection

@section('content')
<div class="rpd-wrap">
    <div class="row align-items-center mb-2">
        <div class="col">
            <a href="{{ route('reseller.products.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="mdi mdi-arrow-left"></i> Back to Products
            </a>
        </div>
    </div>

    <div class="row">
        {{-- LEFT: image --}}
        <div class="col-12 col-md-5 mb-3">
            <div class="card rpd-card h-100">
                <div class="card-body text-center">
                    @php
                        $mainImg = $product->primary_media_image ?? ($images->first() ?? 'public/uploads/default/user.png');
                        $mainIdx = $images->search($mainImg);
                    @endphp
                    <img src="{{ \Illuminate\Support\Str::startsWith($mainImg, ['http://','https://']) ? $mainImg : asset($mainImg) }}"
                        alt="{{ $product->name }}" class="img-fluid rpd-main-img">
                    <div class="pt-3">
                        @if($mainIdx !== false)
                            <a href="{{ route('reseller.products.download', [$product->id, $mainIdx]) }}" class="btn btn-outline-success btn-sm">
                                <i class="mdi mdi-download"></i> ছবি ডাউনলোড
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: info + order form --}}
        <div class="col-12 col-md-7 mb-3">
            <div class="card rpd-card h-100">
                <div class="card-body">
                    <h4 class="rpd-title">{{ $product->name }}</h4>

                    <button class="btn btn-sm btn-light text-success mb-3" type="button">
                        <i class="mdi mdi-check-circle"></i> ভেরিফাইড প্রোডাক্ট
                    </button>

                    <button type="button" data-fav-toggle
                        class="btn btn-sm mb-3 ms-1 {{ $isFavourite ? 'btn-danger is-fav' : 'btn-outline-danger' }}"
                        data-product-id="{{ $product->id }}"
                        data-favourited="{{ $isFavourite ? '1' : '0' }}">
                        <i class="mdi {{ $isFavourite ? 'mdi-heart' : 'mdi-heart-outline' }}"></i>
                        <span>ফেভারিট</span>
                    </button>

                    <div class="row g-2 mb-3 rpd-meta">
                        <div class="col-4">
                            <div class="btn btn-light w-100">
                                প্রাইস: <span class="text-danger fw-bold">৳{{ number_format($product->wholesale_price, 0) }}</span>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="btn btn-light w-100">
                                স্টক:
                                @if($inStock)<span class="text-success">আছে</span>@else<span class="text-danger">নাই</span>@endif
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="btn btn-light w-100">
                                SKU: {{ $product->product_code }}
                            </div>
                        </div>
                    </div>

                    @if($product->new_price > 0)
                        <p style="font-size:13px;" class="mb-2">
                            সাজেস্টেড বিক্রয় মূল্য সর্বোচ্চ
                            <span class="text-danger fw-bold">{{ number_format($product->new_price, 0) }}</span> টাকা।
                        </p>
                    @endif

                    <form action="{{ route('reseller.order.add') }}" method="POST" class="mt-2">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        {{-- Dynamic attributes (Size, Color, Material... ja-i thakuk) --}}
                        @foreach($attributeGroups as $group)
                            <div class="rpd-form-row">
                                <label class="rpd-label d-block">{{ $group['title'] }}:</label>
                                @foreach($group['options'] as $option)
                                    @php $optId = $option['id'] ?? ('legacy-'.$loop->index); @endphp
                                    <div class="form-check form-check-inline size-radio">
                                        <input type="radio"
                                            name="attribute_values[{{ $group['attribute_id'] }}]"
                                            value="{{ $option['id'] }}"
                                            id="attr_{{ $group['attribute_id'] }}_{{ $loop->index }}"
                                            class="form-check-input" required>
                                        <label class="form-check-label" for="attr_{{ $group['attribute_id'] }}_{{ $loop->index }}">{{ $option['title'] }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        <div class="rpd-form-row">
                            <label class="rpd-label d-block">পরিমান / পিস</label>
                            <input type="number" name="qty" value="1" min="1" class="form-control"
                                style="max-width:160px; text-align:center;" required>
                        </div>

                        <div class="rpd-form-row">
                            <label class="rpd-label d-block">বিক্রয়-মূল্য</label>
                            <input type="number" name="amt" class="form-control" placeholder="৳"
                                style="max-width:160px; text-align:center;" required>
                            <p class="text-danger mb-0 mt-2" style="font-size:13px;">
                                বিক্রয় মূল্যের জায়গায় শুধু প্রোডাক্ট এর প্রাইজ লিখুন, কুরিয়ার চার্জ পরবর্তী পেইজে পাবেন।
                            </p>
                        </div>

                        <button type="submit" class="btn btn-outline-success btn-lg mt-3 px-4" @disabled(!$inStock)>
                            <i class="mdi mdi-cart-plus"></i> অর্ডার তালিকায় অ্যাড করুন
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Description --}}
        <div class="col-12 mb-3">
            <div class="card rpd-card">
                <div class="card-body">
                    <p class="desc-title">পণ্যের বিবরণ</p>
                    <div id="descriptionCopy">
                        <div class="desc-data">{!! $product->description !!}</div>
                    </div>
                    <button id="copyButton" onclick="rpCopyDesc()" class="btn btn-outline-success btn-sm mt-2" style="width:120px;">
                        <i class="mdi mdi-content-copy"></i> <span id="copyButtonText">কপি করুন</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Ei product-er sob image — 1-click download --}}
        @if($images->count())
        <div class="col-12">
            <div class="card rpd-card">
                <div class="card-body">
                    <p class="desc-title">প্রোডাক্টের ছবিসমূহ</p>
                    <div class="row g-3">
                        @foreach($images as $i => $img)
                            <div class="col-6 col-md-3">
                                <div class="border rounded rpd-gallery-item text-center">
                                    <img src="{{ \Illuminate\Support\Str::startsWith($img, ['http://','https://']) ? $img : asset($img) }}"
                                        alt="" class="rpd-thumb" loading="lazy">
                                    <div class="pt-2">
                                        <a href="{{ route('reseller.products.download', [$product->id, $i]) }}"
                                            class="btn btn-outline-success btn-sm">
                                            <i class="mdi mdi-download"></i> ছবি ডাউনলোড
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script>
function rpCopyDesc() {
    const text = document.getElementById('descriptionCopy').innerText;
    navigator.clipboard.writeText(text).then(function () {
        document.getElementById('copyButtonText').innerText = 'কপি হয়েছে';
        setTimeout(() => document.getElementById('copyButtonText').innerText = 'কপি করুন', 1500);
    });
}
</script>
@endsection
