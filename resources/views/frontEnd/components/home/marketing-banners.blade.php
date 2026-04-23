<!-- marketing banner section  -->
<section class="marketing-banner-section">
    <div class="custom-container">
        <div class="row g-3 g-lg-4 justify-content-center">
            @foreach ($marketing_banner->take(4) as $banner)
            <div class="col-6 col-lg-3">
                <a href="{{ $banner->link }}" class="marketing-banner-card" style="--banner-index: {{ $loop->index }};">
                    <img src="{{ asset($banner->image) }}" class="img-fluid" alt="Banner {{ $banner->id }}">
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
