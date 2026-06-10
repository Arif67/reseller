<!-- ===== Products / Categories ===== -->
<section class="section" id="products">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">প্রোডাক্ট ক্যাটাগরি</span>
            <h2>আমাদের ক্যাটাগরিসমূহ</h2>
            <p>পছন্দের ক্যাটাগরি থেকে হাজারো ট্রেন্ডিং প্রোডাক্ট রিসেল করুন।</p>
        </div>
        @php $landingCats = collect($sidecategories ?? []); @endphp
        @if ($landingCats->isNotEmpty())
            <div class="grid grid-6">
                @foreach ($landingCats as $cat)
                    <a href="{{ route('landing.products', ['category' => $cat->slug]) }}" class="card cat">
                        @if ($cat->image)
                            <div class="thumb"><img src="{{ $cat->image_url }}" alt="{{ $cat->name }}" loading="lazy"></div>
                        @else
                            <div class="ic"><i class="fa-solid fa-tag"></i></div>
                        @endif
                        <span>{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>
        @else
            <p style="text-align:center;color:var(--muted)">ক্যাটাগরি শীঘ্রই যোগ করা হবে।</p>
        @endif
    </div>
</section>
