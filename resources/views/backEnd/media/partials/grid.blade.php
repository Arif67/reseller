@if($mediaItems->count())
@foreach($mediaItems as $media)
@continue(!$media)
@php($mediaUrl = filled($media->path ?? '') ? (\Illuminate\Support\Str::startsWith($media->path, ['http://', 'https://']) ? $media->path : asset($media->path)) : '')
<div class="media-card">
    <div class="media-thumb">
        <img src="{{ $mediaUrl }}" alt="{{ $media->alt ?? 'Media image' }}">
    </div>
    <div class="media-card-body">
        <div class="media-name" title="{{ $media->name ?? 'Untitled media' }}">{{ $media->name ?? 'Untitled media' }}</div>
        <div class="media-meta">
            <span>{{ strtoupper(pathinfo($media->path ?? '', PATHINFO_EXTENSION)) ?: 'FILE' }}</span>
            <span>{{ $media->created_at?->format('d M Y') }}</span>
        </div>
        <form action="{{ route('media.destroy') }}" method="POST" class="js-media-delete-form">
            @csrf
            <input type="hidden" name="id" value="{{ $media->id }}">
            <button type="submit" class="media-delete-btn">Delete Media</button>
        </form>
    </div>
</div>
@endforeach
@else
<div class="media-empty">
    <h5>No media uploaded yet</h5>
    <p>Prothom image upload korlei ekhane clean gallery view dekhabe.</p>
</div>
@endif
