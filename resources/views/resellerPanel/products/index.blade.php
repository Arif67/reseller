@extends('resellerPanel.layouts.master')
@section('title', 'All Products')

@section('css')
<style>
    .rp-card { transition: box-shadow .2s; }
    .rp-card:hover { box-shadow: 0 0 18px rgba(0,0,0,.12); }
    .rp-thumb { width: 100%; height: 210px; object-fit: contain; object-position: center; background:#f7f7f7; padding:8px; }
    .rp-name { font-size: 14px; font-weight: 600; min-height: 38px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .rp-cats { display:flex; gap:8px; overflow-x:auto; padding-bottom:8px; }
    .rp-cat { white-space:nowrap; }
    #rp-loader { display:none; }
</style>
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-12">
            <div class="page-title-box"><h4 class="page-title">All Products</h4></div>
        </div>
    </div>

    {{-- Product search (full width) --}}
    <div class="row">
        <div class="col-12">
            <form action="{{ route('reseller.products.index') }}" method="GET" class="mb-2">
                @if($activeCategory)
                    <input type="hidden" name="category" value="{{ $activeCategory }}">
                @endif
                <div class="input-group">
                    <input type="text" name="keyword" value="{{ request('keyword') }}"
                        class="form-control" placeholder="Product khujun..." autocomplete="off">
                    <button type="submit" class="btn btn-success"><i class="fe-search"></i></button>
                    @if(request('keyword'))
                        <a href="{{ route('reseller.products.index', $activeCategory ? ['category' => $activeCategory] : []) }}"
                            class="btn btn-outline-secondary"><i class="fe-x"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Category filter (top) --}}
    <div class="card">
        <div class="card-body py-2">
            <div class="rp-cats">
                <a href="{{ route('reseller.products.index') }}"
                    class="btn btn-sm rp-cat {{ !$activeCategory ? 'btn-success' : 'btn-outline-secondary' }}">All</a>
                @foreach($categories as $cat)
                    <a href="{{ route('reseller.products.index', ['category' => $cat->id]) }}"
                        class="btn btn-sm rp-cat {{ $activeCategory == $cat->id ? 'btn-success' : 'btn-outline-secondary' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Product grid: mobile 2, tablet 3, desktop 5 per row --}}
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5" id="rp-product-list">
        @include('resellerPanel.products._cards')
    </div>

    @if($products->isEmpty())
        <div class="card"><div class="card-body text-center text-muted py-4">Kono product nei.</div></div>
    @endif

    <div class="text-center my-3" id="rp-loader">
        <div class="spinner-border text-success" role="status"></div>
    </div>
    <div class="text-center text-muted my-3" id="rp-end" style="display:none;">— Sob product load hoyeche —</div>
@endsection

@section('script')
<script>
(function () {
    let nextPage = {{ $products->currentPage() + 1 }};
    let hasMore = {{ $products->hasMorePages() ? 'true' : 'false' }};
    let loading = false;

    const category = @json($activeCategory);
    const keyword  = @json(request('keyword'));
    const list   = document.getElementById('rp-product-list');
    const loader = document.getElementById('rp-loader');
    const endTxt = document.getElementById('rp-end');
    const baseUrl = "{{ route('reseller.products.index') }}";

    function loadMore() {
        if (loading || !hasMore) return;
        loading = true;
        loader.style.display = 'block';

        const params = new URLSearchParams({ page: nextPage });
        if (category) params.append('category', category);
        if (keyword)  params.append('keyword', keyword);

        fetch(baseUrl + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            list.insertAdjacentHTML('beforeend', data.html);
            hasMore = data.has_more;
            nextPage = data.next;
            loading = false;
            loader.style.display = 'none';
            if (!hasMore) endTxt.style.display = 'block';
        })
        .catch(() => { loading = false; loader.style.display = 'none'; });
    }

    window.addEventListener('scroll', function () {
        if ((window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 400)) {
            loadMore();
        }
    });

    if (!hasMore) endTxt.style.display = 'block';
})();
</script>
@endsection
