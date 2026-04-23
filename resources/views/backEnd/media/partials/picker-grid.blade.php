@php
    $selectedIds = collect($selectedMediaIds ?? [$selectedMediaId ?? null])
        ->flatten()
        ->filter(fn ($value) => filled($value))
        ->map(fn ($value) => (string) $value)
        ->unique()
        ->values()
        ->all();
@endphp
@if($mediaItems->count())
    @foreach($mediaItems as $media)
        @continue(!$media)
        @php $isSelected = in_array((string) $media->id, $selectedIds, true); @endphp
        @php($mediaUrl = filled($media->path ?? '') ? (\Illuminate\Support\Str::startsWith($media->path, ['http://', 'https://']) ? $media->path : asset($media->path)) : '')
        <div class="media-picker-card @if($isSelected) is-selected @endif" data-media-card data-media-id="{{ $media->id }}">
            <div class="media-picker-thumb">
                <img src="{{ $mediaUrl }}" alt="{{ $media->alt ?? 'Media image' }}">
            </div>
            <div class="media-picker-body">
                <div class="media-picker-name" title="{{ $media->name ?? 'Untitled media' }}">{{ $media->name ?? 'Untitled media' }}</div>
                <div class="media-picker-meta">
                    <span>{{ strtoupper(pathinfo($media->path ?? '', PATHINFO_EXTENSION)) ?: 'FILE' }}</span>
                    <span>{{ $media->created_at?->format('d M Y') }}</span>
                </div>
                <button
                    type="button"
                    class="media-picker-select-btn"
                    data-media-select
                    data-media-id="{{ $media->id }}"
                    data-media-path="{{ $mediaUrl }}"
                    data-media-name="{{ $media->name ?? 'Untitled media' }}"
                    data-media-alt="{{ $media->alt ?? 'Media image' }}"
                >
                    {{ $isSelected ? 'Selected' : 'Select' }}
                </button>
            </div>
        </div>
    @endforeach
@else
    <div class="media-picker-empty">
        <h5>No media uploaded yet</h5>
        <p>Prothom image upload korlei ekhane clean gallery view dekhabe.</p>
    </div>
@endif
