@extends('backEnd.layouts.master')
@section('title','Media Library')
@section('content')
<style>
    .media-library-hero {
        background: linear-gradient(135deg, #fff6e8 0%, #ffffff 55%, #eef6ff 100%);
        border: 1px solid #f1e2c8;
        border-radius: 24px;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }
    .media-library-hero .card-body {
        padding: 28px;
    }
    .media-library-kicker {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 14px;
        border-radius: 999px;
        background: rgba(191, 219, 254, 0.5);
        color: #1d4ed8;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .media-library-title {
        font-size: 30px;
        line-height: 1.1;
        font-weight: 800;
        color: #0f172a;
        margin: 16px 0 10px;
    }
    .media-library-copy {
        max-width: 680px;
        color: #475569;
        font-size: 14px;
        margin-bottom: 0;
    }
    .media-upload-panel {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
    }
    .media-upload-panel .card-body {
        padding: 24px;
    }
    .media-upload-top {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .media-upload-heading {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
    }
    .media-upload-note {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
    }
    .media-upload-badge {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        padding: 8px 14px;
        white-space: nowrap;
    }
    .media-dropzone {
        position: relative;
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        background: linear-gradient(180deg, #fcfdff 0%, #f8fafc 100%);
        min-height: 210px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .media-dropzone:hover,
    .media-dropzone.is-dragover {
        border-color: #2563eb;
        background: linear-gradient(180deg, #f8fbff 0%, #eef6ff 100%);
        box-shadow: inset 0 0 0 4px rgba(37, 99, 235, 0.06);
    }
    .media-dropzone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }
    .media-dropzone-icon {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        color: #fff;
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 16px;
        box-shadow: 0 18px 28px rgba(37, 99, 235, 0.22);
    }
    .media-dropzone strong {
        display: block;
        font-size: 20px;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .media-dropzone span {
        display: block;
        color: #64748b;
        font-size: 14px;
        max-width: 420px;
    }
    .media-upload-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-top: 18px;
        flex-wrap: wrap;
    }
    .media-file-count {
        font-size: 14px;
        color: #475569;
        font-weight: 600;
    }
    .media-submit-btn {
        border: 0;
        border-radius: 14px;
        padding: 14px 22px;
        min-width: 190px;
        font-size: 15px;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #0f172a, #1d4ed8);
        box-shadow: 0 18px 30px rgba(29, 78, 216, 0.22);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .media-submit-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 22px 34px rgba(29, 78, 216, 0.28);
    }
    .media-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .media-toolbar h5 {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
    }
    .media-toolbar p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
    }
    .media-count-pill {
        display: inline-flex;
        align-items: center;
        padding: 9px 14px;
        border-radius: 999px;
        background: #fff7ed;
        color: #c2410c;
        font-size: 13px;
        font-weight: 700;
        border: 1px solid #fed7aa;
    }
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 18px;
    }
    .media-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .media-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.1);
    }
    .media-thumb {
        position: relative;
        aspect-ratio: 1 / 1;
        background: #f8fafc;
        overflow: hidden;
    }
    .media-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .media-card-body {
        padding: 14px 14px 16px;
    }
    .media-name {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-bottom: 8px;
    }
    .media-meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        color: #64748b;
        font-size: 12px;
        margin-bottom: 12px;
    }
    .media-delete-btn {
        width: 100%;
        border: 0;
        border-radius: 12px;
        padding: 10px 14px;
        background: #fee2e2;
        color: #b91c1c;
        font-size: 13px;
        font-weight: 700;
        transition: background 0.2s ease, color 0.2s ease;
    }
    .media-delete-btn:hover {
        background: #fecaca;
        color: #991b1b;
    }
    .media-empty {
        padding: 50px 24px;
        text-align: center;
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 22px;
    }
    .media-empty h5 {
        color: #0f172a;
        font-size: 20px;
        font-weight: 800;
        margin-bottom: 10px;
    }
    .media-empty p {
        margin: 0;
        color: #64748b;
    }
    .media-loader {
        display: none;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin-top: 24px;
        color: #475569;
        font-size: 14px;
        font-weight: 600;
    }
    .media-loader.is-visible {
        display: flex;
    }
    .media-loader-spinner {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #bfdbfe;
        border-top-color: #2563eb;
        animation: media-spin 0.8s linear infinite;
    }
    @keyframes media-spin {
        to { transform: rotate(360deg); }
    }
    @media (max-width: 767px) {
        .media-library-title {
            font-size: 24px;
        }
        .media-upload-panel .card-body,
        .media-library-hero .card-body {
            padding: 20px;
        }
        .media-submit-btn {
            width: 100%;
        }
        .media-upload-footer {
            align-items: stretch;
        }
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Media Library</h4>
            </div>
        </div>
    </div>


    <div class="card media-upload-panel mb-4">
        <div class="card-body">
            <form action="{{ route('media.store') }}" method="POST" enctype="multipart/form-data" id="mediaUploadForm">
                @csrf
                <div class="media-upload-top">
                    <div>
                        <h3 class="media-upload-heading">Upload New Media</h3>
                        <p class="media-upload-note">JPG, PNG, WEBP, AVIF support ache. Ek sathe multiple image upload korte parben.</p>
                    </div>
                    <span class="media-upload-badge">Multi Upload Enabled</span>
                </div>

                <label class="media-dropzone" for="media-files">
                    <div class="media-dropzone-icon">+</div>
                    <strong>Drop images here or click to browse</strong>
                    <span>Clean file input-er jaygay ekta focused upload area deya holo, jate onek image ek sathe select kora easy hoy.</span>
                    <input type="file" id="media-files" name="files[]" multiple accept="image/*" class="form-control @error('files') is-invalid @enderror @error('files.*') is-invalid @enderror">
                </label>

                @error('files')
                <span class="invalid-feedback d-block mt-2" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
                @error('files.*')
                <span class="invalid-feedback d-block mt-2" role="alert"><strong>{{ $message }}</strong></span>
                @enderror

                <div class="media-upload-footer">
                    <div class="media-file-count" id="mediaFileCount">No files selected yet</div>
                    <button type="submit" class="media-submit-btn">Upload To Library</button>
                </div>
            </form>
        </div>
    </div>

    <div class="media-toolbar">
        <div>
            <h5>Library Images</h5>
            <p>Scroll korle next images auto load hobe. Delete button sudhu unused media-te kaj korbe.</p>
        </div>
        <span class="media-count-pill">{{ $mediaItems->total() }} items</span>
    </div>

    <div id="mediaGrid" class="media-grid" data-next-page="{{ $mediaItems->hasMorePages() ? $mediaItems->currentPage() + 1 : '' }}">
        @include('backEnd.media.partials.grid', ['mediaItems' => $mediaItems])
    </div>

    <div id="mediaLoader" class="media-loader">
        <span class="media-loader-spinner"></span>
        <span>Loading more media...</span>
    </div>
</div>
@endsection
@section('script')
<script>
    (function() {
        var fileInput = document.getElementById('media-files');
        var fileCount = document.getElementById('mediaFileCount');
        var dropzone = document.querySelector('.media-dropzone');
        var mediaGrid = document.getElementById('mediaGrid');
        var mediaLoader = document.getElementById('mediaLoader');
        var mediaCountPill = document.querySelector('.media-count-pill');
        var csrfToken = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
        var isLoading = false;

        function updateFileCount(files) {
            if (!files || !files.length) {
                fileCount.textContent = 'No files selected yet';
                return;
            }

            fileCount.textContent = files.length + (files.length === 1 ? ' image selected' : ' images selected');
        }

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                updateFileCount(this.files);
            });
        }

        if (dropzone && fileInput) {
            ['dragenter', 'dragover'].forEach(function(eventName) {
                dropzone.addEventListener(eventName, function(event) {
                    event.preventDefault();
                    dropzone.classList.add('is-dragover');
                });
            });

            ['dragleave', 'dragend', 'drop'].forEach(function(eventName) {
                dropzone.addEventListener(eventName, function(event) {
                    event.preventDefault();
                    dropzone.classList.remove('is-dragover');
                });
            });

            dropzone.addEventListener('drop', function(event) {
                if (!event.dataTransfer || !event.dataTransfer.files.length) {
                    return;
                }

                fileInput.files = event.dataTransfer.files;
                updateFileCount(event.dataTransfer.files);
            });
        }


        function updateMediaCount(delta) {
            if (!mediaCountPill) {
                return;
            }

            var currentCount = parseInt(mediaCountPill.textContent, 10);
            if (isNaN(currentCount)) {
                return;
            }

            var nextCount = Math.max(0, currentCount + delta);
            mediaCountPill.textContent = nextCount + ' items';
        }

        function ensureEmptyState() {
            if (!mediaGrid || mediaGrid.querySelector('.media-card')) {
                return;
            }

            mediaGrid.innerHTML = '<div class="media-empty"><h5>No media uploaded yet</h5><p>Prothom image upload korlei ekhane clean gallery view dekhabe.</p></div>';
        }

        document.addEventListener('submit', async function(event) {
            var form = event.target;
            if (!form.matches('.js-media-delete-form')) {
                return;
            }

            event.preventDefault();

            if (!confirm('Do you want to delete this media?')) {
                return;
            }

            var formData = new FormData(form);
            var card = form.closest('.media-card');
            var button = form.querySelector('button[type="submit"]');
            var originalButtonText = button ? button.textContent : '';

            if (button) {
                button.disabled = true;
                button.textContent = 'Deleting...';
            }

            try {
                var response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                var data = await response.json();

                if (!response.ok || data.status !== 'success') {
                    throw new Error(data.message || 'Delete failed.');
                }

                if (card) {
                    card.remove();
                }

                updateMediaCount(-1);
                ensureEmptyState();
            } catch (error) {
                alert(error.message || 'Delete failed.');
                if (button) {
                    button.disabled = false;
                    button.textContent = originalButtonText;
                }
            }
        });

        async function loadMoreMedia() {
            var nextPage = mediaGrid.dataset.nextPage;
            if (!nextPage || isLoading) {
                return;
            }

            isLoading = true;
            mediaLoader.classList.add('is-visible');

            try {
                var response = await fetch('{{ route('media.index') }}?page=' + nextPage, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load media');
                }

                var data = await response.json();
                mediaGrid.insertAdjacentHTML('beforeend', data.html);
                mediaGrid.dataset.nextPage = data.next_page || '';
            } catch (error) {
                mediaGrid.dataset.nextPage = '';
            } finally {
                isLoading = false;
                mediaLoader.classList.remove('is-visible');
            }
        }

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        loadMoreMedia();
                    }
                });
            }, {
                rootMargin: '300px 0px'
            });

            observer.observe(mediaLoader);
        }
    })();
</script>
@endsection
