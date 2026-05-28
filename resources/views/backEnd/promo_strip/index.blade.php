@extends('backEnd.layouts.master')
@section('title', 'Promo Strip')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="{{ route('promo_strip.create') }}" class="btn btn-primary rounded-pill">
                        <i class="mdi mdi-plus"></i> Add New
                    </a>
                </div>
                <h4 class="page-title">Promo Strip</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted mb-3" style="font-size:13px;">
                        <i class="mdi mdi-information-outline me-1"></i>
                        These banners appear as a full-width scrollable strip <strong>below the hero slider</strong> on the homepage.
                        Recommended image size: <strong>1188 × 140 px</strong> (wide) or <strong>600 × 140 px</strong> (card). Supports GIF, WebP, PNG, JPG.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Link</th>
                                    <th>Countdown End</th>
                                    <th>Sort</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if($item->image)
                                            <img src="{{ asset($item->image) }}" alt="" height="50"
                                                style="border-radius:6px;background:{{ $item->bg_color }};object-fit:cover;max-width:120px;">
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->title ?: '—' }}</td>
                                    <td><small class="text-muted" style="word-break:break-all;max-width:180px;display:block;">{{ $item->link }}</small></td>
                                    <td>
                                        @if($item->countdown_end)
                                            <span class="badge bg-warning text-dark">{{ $item->countdown_end->format('d M Y H:i') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->sort_order }}</td>
                                    <td>
                                        <form action="{{ route('promo_strip.toggle') }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                            <button type="submit" class="btn btn-sm {{ $item->status ? 'btn-success' : 'btn-secondary' }}">
                                                {{ $item->status ? 'Active' : 'Inactive' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="{{ route('promo_strip.edit', $item->id) }}" class="btn btn-sm btn-info">Edit</a>
                                        <form action="{{ route('promo_strip.destroy') }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this promo strip?')">
                                            @csrf
                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">No promo strips yet. <a href="{{ route('promo_strip.create') }}">Add one</a>.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
