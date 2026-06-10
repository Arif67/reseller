@extends('resellerPanel.layouts.master')
@section('title', 'Favourites')

@section('css')
<style>
    .rp-card { transition: box-shadow .2s; }
    .rp-card:hover { box-shadow: 0 0 18px rgba(0,0,0,.12); }
    .rp-thumb { width: 100%; height: 210px; object-fit: contain; object-position: center; background:#f7f7f7; padding:8px; }
    .rp-name { font-size: 14px; font-weight: 600; min-height: 38px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
</style>
@endsection

@section('content')
    <div class="row align-items-center">
        <div class="col-12">
            <div class="page-title-box"><h4 class="page-title">My Favourites</h4></div>
        </div>
    </div>

    @if($products->isEmpty())
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                <i class="mdi mdi-heart-outline" style="font-size:42px;"></i>
                <p class="mt-2 mb-0">Ekhono kono product favourite e nei.</p>
                <a href="{{ route('reseller.products.index') }}" class="btn btn-success btn-sm mt-3">
                    <i class="fe-grid"></i> Product browse korun
                </a>
            </div>
        </div>
    @else
        {{-- favPage = true: heart unfav korle card soriye jay --}}
        @php $favPage = true; @endphp
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-5">
            @include('resellerPanel.products._cards')
        </div>
    @endif
@endsection
