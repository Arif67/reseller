@php
    $promoStrips = \App\Models\PromoStrip::active()->get();
@endphp

@if($promoStrips->isNotEmpty())
@push('css')
<style>
/* ── Promo Strip ── */
.ps-section {
    background: #fff;
    padding: 0;
    margin: 0;
    overflow: hidden;
}
.ps-inner {
    max-width: 1520px;
    margin: 0 auto;
    padding: 6px 15px;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
}
.ps-inner::-webkit-scrollbar { display: none; }

.ps-card {
    flex: 1 1 0;
    min-width: 0;
    border-radius: 8px;
    overflow: hidden;
    position: relative;
    display: block;
    text-decoration: none;
    cursor: pointer;
    transition: transform .15s, box-shadow .15s;
}
.ps-card:hover { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,0.12); text-decoration: none; }
.ps-card img {
    display: block; width: 100%; height: 100%;
    object-fit: cover;
}
/* single card = full width */
.ps-inner.ps-count-1 .ps-card { min-width: 100%; }
/* all cards: fixed height */
.ps-card { height: 180px; }

@media (max-width: 991.98px) { .ps-card { height: 130px; } }
@media (max-width: 767.98px) { .ps-card { height: 90px; min-width: 220px; flex: 0 0 auto; } }

/* countdown overlay */
.ps-countdown {
    position: absolute;
    bottom: 0; right: 0;
    background: rgba(0,0,0,0.65);
    backdrop-filter: blur(2px);
    color: #fff;
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 8px 0 0 0;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.5px;
}
.ps-countdown-label { font-size: 9px; font-weight: 400; opacity: .8; margin-right: 2px; }
.ps-countdown-block {
    background: #F85606;
    border-radius: 4px;
    padding: 2px 5px;
    font-size: 12px;
    font-weight: 800;
    min-width: 26px;
    text-align: center;
}
.ps-countdown-sep { font-size: 14px; opacity: .8; margin: 0 1px; }

/* no-image fallback */
.ps-card-noimg {
    display: flex; align-items: center; justify-content: center;
    height: 80px; width: 100%;
    font-size: 14px; font-weight: 700; color: #333;
    padding: 0 16px; text-decoration: none;
    border-radius: 8px;
    transition: filter .15s;
}
.ps-card-noimg:hover { filter: brightness(0.95); text-decoration: none; }

@media (max-width: 767.98px) {
    .ps-inner { gap: 6px; padding: 4px 8px; }
}
</style>
@endpush

<section class="ps-section">
    <div class="ps-inner ps-count-{{ min($promoStrips->count(), 4) }}">
        @foreach($promoStrips->take(4) as $strip)
            @if($strip->image)
                <a href="{{ $strip->link ?? '#' }}" class="ps-card"
                   style="background:{{ $strip->bg_color }};"
                   @if($strip->countdown_end) data-countdown="{{ $strip->countdown_end->toIso8601String() }}" @endif>
                    <img src="{{ asset($strip->image) }}" alt="{{ $strip->title }}">
                    @if($strip->countdown_end && $strip->countdown_end->isFuture())
                        <div class="ps-countdown">
                            <span class="ps-countdown-label">Ends in</span>
                            <span class="ps-countdown-block" data-part="d">00</span>
                            <span class="ps-countdown-sep">:</span>
                            <span class="ps-countdown-block" data-part="h">00</span>
                            <span class="ps-countdown-sep">:</span>
                            <span class="ps-countdown-block" data-part="m">00</span>
                            <span class="ps-countdown-sep">:</span>
                            <span class="ps-countdown-block" data-part="s">00</span>
                        </div>
                    @endif
                </a>
            @else
                <a href="{{ $strip->link ?? '#' }}" class="ps-card-noimg"
                   style="background:{{ $strip->bg_color }};">
                    {{ $strip->title }}
                </a>
            @endif
        @endforeach
    </div>
</section>

@push('script')
<script>
(function () {
    document.querySelectorAll('.ps-card[data-countdown]').forEach(function (card) {
        var end = new Date(card.dataset.countdown).getTime();
        var d = card.querySelector('[data-part="d"]');
        var h = card.querySelector('[data-part="h"]');
        var m = card.querySelector('[data-part="m"]');
        var s = card.querySelector('[data-part="s"]');
        if (!d || !h || !m || !s) return;

        function pad(n) { return String(n).padStart(2, '0'); }
        function tick() {
            var diff = end - Date.now();
            if (diff <= 0) {
                card.querySelector('.ps-countdown')?.remove();
                return;
            }
            var days    = Math.floor(diff / 86400000);
            var hours   = Math.floor((diff % 86400000) / 3600000);
            var minutes = Math.floor((diff % 3600000) / 60000);
            var seconds = Math.floor((diff % 60000) / 1000);
            d.textContent = pad(days);
            h.textContent = pad(hours);
            m.textContent = pad(minutes);
            s.textContent = pad(seconds);
        }
        tick();
        setInterval(tick, 1000);
    });
})();
</script>
@endpush
@endif
