<!-- ===== Inner page banner ===== -->
<section class="page-banner">
    <div class="container">
        <div class="crumb">{{ $crumb ?? '' }}</div>
        <h1>{{ $title ?? '' }}</h1>
        @if (!empty($text))
            <p>{{ $text }}</p>
        @endif
    </div>
</section>
