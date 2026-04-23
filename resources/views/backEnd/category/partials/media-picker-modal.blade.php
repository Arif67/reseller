@once
<style>
    .category-media-card {
        margin-bottom: 20px;
        padding: 18px;
        border: 1px solid #dbe4f0;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .category-media-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .category-media-title {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
    }

    .category-media-note {
        display: block;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .category-media-trigger {
        white-space: nowrap;
        align-self: flex-start;
    }

    .category-media-dropzone {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 124px;
        padding: 16px;
        border: 1px dashed #94a3b8;
        border-radius: 16px;
        background: #f8fafc;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s ease, background-color 0.2s ease, transform 0.2s ease;
    }

    .category-media-dropzone.is-dragover {
        border-color: #2563eb;
        background: #eff6ff;
        transform: translateY(-1px);
    }

    .category-media-dropzone input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
    }

    .category-media-dropzone-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        margin-bottom: 10px;
        border-radius: 999px;
        background: #dbeafe;
        color: #1d4ed8;
        font-size: 22px;
        font-weight: 700;
    }

    .category-media-dropzone strong {
        display: block;
        color: #0f172a;
        font-size: 14px;
        margin-bottom: 4px;
    }

    .category-media-dropzone span {
        color: #64748b;
        font-size: 12px;
    }

    .category-media-current {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 12px;
        padding: 10px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
    }

    .category-media-current-preview {
        width: 64px;
        height: 64px;
        border-radius: 12px;
        object-fit: cover;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .category-media-current-preview.is-icon {
        width: 48px;
        height: 48px;
        padding: 6px;
        object-fit: contain;
    }

    .category-media-current-copy {
        color: #475569;
        font-size: 12px;
        line-height: 1.5;
    }

    .category-media-status {
        margin-top: 10px;
        color: #64748b;
        font-size: 12px;
    }

    .category-media-modal .modal-content {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.16);
    }

    .category-media-modal .modal-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #0f172a, #1d4ed8);
        color: #fff;
        border-bottom: 0;
    }

    .category-media-modal .modal-title {
        font-size: 20px;
        font-weight: 800;
    }

    .category-media-modal .btn-close {
        filter: invert(1);
        opacity: 0.9;
    }

    .category-media-modal .modal-body {
        padding: 20px 24px 24px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }

    .category-media-modal-toolbar {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .category-media-modal-search {
        min-width: min(100%, 360px);
    }

    .category-media-modal-search input {
        border-radius: 14px;
        border-color: #cbd5e1;
        box-shadow: none;
    }

    .category-media-modal-hint {
        color: #64748b;
        font-size: 13px;
    }

    .media-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 14px;
    }

    .media-picker-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.06);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .media-picker-card:hover,
    .media-picker-card.is-selected {
        transform: translateY(-2px);
        border-color: #2563eb;
        box-shadow: 0 18px 32px rgba(37, 99, 235, 0.12);
    }

    .media-picker-thumb {
        aspect-ratio: 1 / 1;
        background: #f8fafc;
        overflow: hidden;
    }

    .media-picker-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .media-picker-body {
        padding: 12px;
    }

    .media-picker-name {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin-bottom: 8px;
    }

    .media-picker-meta {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        color: #64748b;
        font-size: 12px;
        margin-bottom: 12px;
    }

    .media-picker-select-btn {
        width: 100%;
        border: 0;
        border-radius: 12px;
        padding: 10px 14px;
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
    }

    .media-picker-empty {
        grid-column: 1 / -1;
        padding: 46px 24px;
        text-align: center;
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
    }

    .media-picker-empty h5 {
        color: #0f172a;
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 8px;
    }

    .media-picker-empty p {
        margin: 0;
        color: #64748b;
    }

    .category-media-modal-loader {
        display: none;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
        color: #475569;
        font-size: 14px;
        font-weight: 600;
    }

    .category-media-modal-loader.is-visible {
        display: flex;
    }

    .category-media-modal-spinner {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #bfdbfe;
        border-top-color: #2563eb;
        animation: media-spin 0.8s linear infinite;
    }

    .category-media-modal-footer {
        padding: 16px 24px 22px;
        border-top: 1px solid #e2e8f0;
        background: #fff;
    }

    @keyframes media-spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 767px) {
        .category-media-head,
        .category-media-modal-toolbar {
            align-items: stretch;
        }

        .category-media-trigger,
        .category-media-modal-search,
        .category-media-modal .btn {
            width: 100%;
        }

        .media-picker-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        }

        .category-media-modal .modal-body,
        .category-media-modal .modal-header,
        .category-media-modal-footer {
            padding-left: 16px;
            padding-right: 16px;
        }
    }
</style>

<div class="modal fade category-media-modal" id="categoryMediaPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" data-category-media-modal-title>Choose Media</h5>
                    <div class="category-media-modal-hint" data-category-media-modal-subtitle>Select an image for the active field.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="category-media-modal-toolbar">
                    <div class="category-media-modal-search">
                        <input type="search" class="form-control" placeholder="Search media by name, path or alt text" data-category-media-search>
                    </div>
                    <div class="category-media-modal-hint" data-category-media-result-hint>Latest media will load first.</div>
                </div>

                <div class="media-picker-grid" data-category-media-grid></div>

                <div class="alert alert-danger d-none mt-3" data-category-media-error></div>

                <div class="category-media-modal-loader" data-category-media-loader>
                    <span class="category-media-modal-spinner"></span>
                    <span>Loading more media...</span>
                </div>
            </div>

            <div class="category-media-modal-footer d-flex justify-content-between gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-category-media-load-more>Load more</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalElement = document.getElementById('categoryMediaPickerModal');

        if (!modalElement || typeof bootstrap === 'undefined') {
            return;
        }

        var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        var grid = modalElement.querySelector('[data-category-media-grid]');
        var searchInput = modalElement.querySelector('[data-category-media-search]');
        var loadMoreButton = modalElement.querySelector('[data-category-media-load-more]');
        var loader = modalElement.querySelector('[data-category-media-loader]');
        var errorBox = modalElement.querySelector('[data-category-media-error]');
        var titleNode = modalElement.querySelector('[data-category-media-modal-title]');
        var subtitleNode = modalElement.querySelector('[data-category-media-modal-subtitle]');
        var hintNode = modalElement.querySelector('[data-category-media-result-hint]');
        var activeField = null;
        var activeLabel = 'Choose Media';
        var nextPage = null;
        var loading = false;
        var searchTimer = null;
        var baseUrl = '{{ route('media.index') }}';

        function getPicker(field) {
            return document.querySelector('[data-category-media-picker][data-category-media-field="' + field + '"]');
        }

        function getSelectedMediaId(field) {
            var picker = getPicker(field);
            var hiddenInput = picker ? picker.querySelector('[data-category-media-selected-id]') : null;

            return hiddenInput ? hiddenInput.value : '';
        }

        function setError(message) {
            if (!errorBox) {
                return;
            }

            if (!message) {
                errorBox.textContent = '';
                errorBox.classList.add('d-none');
                return;
            }

            errorBox.textContent = message;
            errorBox.classList.remove('d-none');
        }

        function setGridLoading(isVisible) {
            if (loader) {
                loader.classList.toggle('is-visible', !!isVisible);
            }
        }

        function updateLoadMoreState() {
            if (!loadMoreButton) {
                return;
            }

            loadMoreButton.disabled = loading || !nextPage;
            loadMoreButton.style.display = nextPage ? 'inline-flex' : 'none';
        }

        function syncCardSelection(selectedId) {
            if (!grid) {
                return;
            }

            grid.querySelectorAll('[data-media-card]').forEach(function (card) {
                card.classList.toggle('is-selected', String(card.dataset.mediaId) === String(selectedId));
            });
        }

        function applySelectionToField(media) {
            if (!activeField) {
                return;
            }

            var picker = getPicker(activeField);
            if (!picker) {
                return;
            }

            var hiddenInput = picker.querySelector('[data-category-media-selected-id]');
            var uploadInput = picker.querySelector('[data-category-media-upload]');
            var fileLabel = picker.querySelector('[data-category-file-label]');
            var previewImage = picker.querySelector('[data-category-media-preview-image]');
            var previewTitle = picker.querySelector('[data-category-media-preview-title]');
            var previewPath = picker.querySelector('[data-category-media-preview-path]');
            var statusNode = picker.querySelector('[data-category-media-status]');

            if (hiddenInput) {
                hiddenInput.value = media.id || '';
            }

            if (uploadInput) {
                uploadInput.value = '';
            }

            if (fileLabel) {
                fileLabel.textContent = 'No file selected yet';
            }

            if (previewImage) {
                previewImage.src = media.path || '';
                previewImage.alt = media.alt || activeLabel;
                previewImage.style.display = media.path ? '' : 'none';
            }

            if (previewTitle) {
                previewTitle.textContent = 'Selected ' + activeLabel;
            }

            if (previewPath) {
                previewPath.textContent = media.name || media.path || 'Media selected from library.';
            }

            if (statusNode) {
                statusNode.textContent = 'Selected from media library.';
            }
        }

        function refreshUploadState(picker) {
            var hiddenInput = picker.querySelector('[data-category-media-selected-id]');
            var uploadInput = picker.querySelector('[data-category-media-upload]');
            var fileLabel = picker.querySelector('[data-category-file-label]');
            var previewImage = picker.querySelector('[data-category-media-preview-image]');
            var previewTitle = picker.querySelector('[data-category-media-preview-title]');
            var previewPath = picker.querySelector('[data-category-media-preview-path]');
            var statusNode = picker.querySelector('[data-category-media-status]');
            var currentUrl = picker.dataset.categoryCurrentUrl || '';
            var currentPath = picker.dataset.categoryCurrentPath || '';
            var currentLabel = picker.dataset.categoryCurrentLabel || 'Media';
            var hasUpload = uploadInput && uploadInput.files && uploadInput.files.length > 0;

            if (hasUpload) {
                if (hiddenInput) {
                    hiddenInput.value = '';
                }

                if (fileLabel) {
                    fileLabel.textContent = uploadInput.files[0].name;
                }

                if (previewImage) {
                    if (picker.dataset.previewObjectUrl) {
                        URL.revokeObjectURL(picker.dataset.previewObjectUrl);
                    }

                    picker.dataset.previewObjectUrl = URL.createObjectURL(uploadInput.files[0]);
                    previewImage.src = picker.dataset.previewObjectUrl;
                    previewImage.alt = uploadInput.files[0].name;
                    previewImage.style.display = '';
                }

                if (previewTitle) {
                    previewTitle.textContent = 'New upload selected';
                }

                if (previewPath) {
                    previewPath.textContent = uploadInput.files[0].name;
                }

                if (statusNode) {
                    statusNode.textContent = 'New upload selected.';
                }

                return;
            }

            if (picker.dataset.previewObjectUrl) {
                URL.revokeObjectURL(picker.dataset.previewObjectUrl);
                delete picker.dataset.previewObjectUrl;
            }

            if (fileLabel) {
                fileLabel.textContent = 'No file selected yet';
            }

            if (previewImage) {
                previewImage.src = currentUrl;
                previewImage.alt = currentLabel;
                previewImage.style.display = currentUrl ? '' : 'none';
            }

            if (previewTitle) {
                previewTitle.textContent = currentUrl ? 'Current ' + currentLabel : 'No media selected yet';
            }

            if (previewPath) {
                previewPath.textContent = currentPath || 'Choose a file or select from the media library.';
            }

            if (hiddenInput && hiddenInput.value) {
                if (statusNode) {
                    statusNode.textContent = 'Selected from media library.';
                }
                return;
            }

            if (statusNode) {
                statusNode.textContent = 'Use upload or choose from library.';
            }
        }

        function renderMedia(html, append) {
            if (!grid) {
                return;
            }

            if (!append) {
                grid.innerHTML = html;
            } else {
                grid.insertAdjacentHTML('beforeend', html);
            }
        }

        async function fetchMedia(page, append) {
            if (!activeField || loading) {
                return;
            }

            loading = true;
            setGridLoading(true);
            setError('');
            updateLoadMoreState();

            var params = new URLSearchParams();
            params.set('picker', '1');
            params.set('page', String(page || 1));
            params.set('selected_media_id', getSelectedMediaId(activeField));

            if (searchInput && searchInput.value.trim() !== '') {
                params.set('search', searchInput.value.trim());
            }

            try {
                var response = await fetch(baseUrl + '?' + params.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                var data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Failed to load media.');
                }

                renderMedia(data.html || '', append);
                nextPage = data.next_page || null;
                syncCardSelection(getSelectedMediaId(activeField));
                hintNode.textContent = nextPage ? 'More media available below.' : 'No more media to load.';
            } catch (error) {
                setError(error.message || 'Failed to load media.');
                if (!append) {
                    grid.innerHTML = '';
                }
                nextPage = null;
                hintNode.textContent = 'Unable to load media right now.';
            } finally {
                loading = false;
                setGridLoading(false);
                updateLoadMoreState();
            }
        }

        document.querySelectorAll('[data-open-category-media-picker]').forEach(function (button) {
            button.addEventListener('click', function () {
                activeField = button.dataset.mediaTarget || 'image';
                activeLabel = button.dataset.mediaLabel || 'Choose Media';

                var picker = getPicker(activeField);
                var hiddenInput = picker ? picker.querySelector('[data-category-media-selected-id]') : null;
                var selectedId = hiddenInput ? hiddenInput.value : '';

                titleNode.textContent = 'Choose ' + activeLabel;
                subtitleNode.textContent = 'Pick a media item from the library for ' + activeLabel.toLowerCase() + '.';
                hintNode.textContent = 'Latest media will load first.';
                searchInput.value = '';
                modal.show();
                syncCardSelection(selectedId);
                fetchMedia(1, false);
            });
        });

        if (grid) {
            grid.addEventListener('click', function (event) {
                var button = event.target.closest('[data-media-select]');
                if (!button) {
                    return;
                }

                var media = {
                    id: button.dataset.mediaId || '',
                    path: button.dataset.mediaPath || '',
                    name: button.dataset.mediaName || '',
                    alt: button.dataset.mediaAlt || ''
                };

                applySelectionToField(media);
                syncCardSelection(media.id);
                modal.hide();
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                if (!activeField) {
                    return;
                }

                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    fetchMedia(1, false);
                }, 250);
            });
        }

        if (loadMoreButton) {
            loadMoreButton.addEventListener('click', function () {
                if (nextPage) {
                    fetchMedia(nextPage, true);
                }
            });
        }

        document.querySelectorAll('[data-category-media-picker]').forEach(function (picker) {
            var uploadInput = picker.querySelector('[data-category-media-upload]');
            var dropzone = picker.querySelector('[data-category-dropzone]');

            if (uploadInput) {
                uploadInput.addEventListener('change', function () {
                    refreshUploadState(picker);
                });
            }

            if (dropzone && uploadInput) {
                ['dragenter', 'dragover'].forEach(function (eventName) {
                    dropzone.addEventListener(eventName, function (event) {
                        event.preventDefault();
                        dropzone.classList.add('is-dragover');
                    });
                });

                ['dragleave', 'dragend', 'drop'].forEach(function (eventName) {
                    dropzone.addEventListener(eventName, function (event) {
                        event.preventDefault();
                        dropzone.classList.remove('is-dragover');
                    });
                });

                dropzone.addEventListener('drop', function (event) {
                    if (!event.dataTransfer || !event.dataTransfer.files || event.dataTransfer.files.length === 0) {
                        return;
                    }

                    uploadInput.files = event.dataTransfer.files;
                    refreshUploadState(picker);
                });
            }

            refreshUploadState(picker);
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            activeField = null;
            nextPage = null;
            loading = false;
            setError('');
            setGridLoading(false);
            updateLoadMoreState();
            if (grid) {
                grid.innerHTML = '';
            }
            if (searchInput) {
                searchInput.value = '';
            }
            if (hintNode) {
                hintNode.textContent = 'Latest media will load first.';
            }
        });
    });
</script>
@endonce
