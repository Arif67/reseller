<section class="new-popular-section">
    <div class="custom-container">
        <div class="new-popular-shell">
            <div class="new-popular-head">
                <h3 class="new-popular-title">New And Popular</h3>
                <p class="new-popular-sub">
                    All latest products in one place. Switch categories to browse easily.
                </p>
            </div>

            <div class="new-popular-tabs-wrap">
                <ul class="nav nav-pills new-popular-tabs" id="pills-tab" role="tablist">
                    @foreach ($homecategory as $index => $cat)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link text-nowrap @if($index == 0) active @endif"
                            id="pills-{{ $cat->id }}-tab" data-bs-toggle="pill" data-bs-target="#pills-{{ $cat->id }}"
                            type="button" role="tab">
                            {{ $cat->name }}
                        </button>
                    </li>
                    @endforeach
                </ul>
            </div>

            <div class="tab-content new-popular-content" id="pills-tabContent">
                @foreach ($homecategory as $index => $cat)
                <div class="tab-pane fade new-popular-pane @if($index == 0) show active @endif"
                    id="pills-{{ $cat->id }}" role="tabpanel">
                    <div class="np-grid catalog-product-grid">
                        @foreach($cat->products as $value)
                        @include('frontEnd.partials.product-card', ['product' => $value, 'titleLimit' => 56])
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
