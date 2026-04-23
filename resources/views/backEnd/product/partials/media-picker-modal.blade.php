@once
<style>
    .product-media-modal .modal-content {
        border: 0;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 30px 60px rgba(15, 23, 42, 0.16);
    }

    .product-media-modal .modal-header {
        padding: 20px 24px;
        background: linear-gradient(135deg, #0f172a, #1d4ed8);
        color: #fff;
        border-bottom: 0;
    }

    .product-media-modal .modal-title {
        font-size: 20px;
        font-weight: 800;
    }

    .product-media-modal .btn-close {
        filter: invert(1);
        opacity: 0.9;
    }

    .product-media-modal .modal-body {
        padding: 20px 24px 24px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
    }

    .product-media-modal-toolbar {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .product-media-modal-search {
        min-width: min(100%, 360px);
    }

    .product-media-modal-search input {
        border-radius: 14px;
        border-color: #cbd5e1;
        box-shadow: none;
    }

    .product-media-modal-hint {
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

    .product-media-modal-loader {
        display: none;
        justify-content: center;
        align-items: center;
        gap: 12px;
        margin-top: 16px;
        color: #475569;
        font-size: 14px;
        font-weight: 600;
    }

    .product-media-modal-loader.is-visible {
        display: flex;
    }

    .product-media-modal-spinner {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 2px solid #bfdbfe;
        border-top-color: #2563eb;
        animation: product-media-spin 0.8s linear infinite;
    }

    .product-media-modal-footer {
        padding: 16px 24px 22px;
        border-top: 1px solid #e2e8f0;
        background: #fff;
    }

    .product-media-card {
        padding: 16px 18px;
        border: 1px solid #dbe4f0;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        margin-bottom: 16px;
    }

    .product-media-title {
        margin: 0;
        color: #0f172a;
        font-size: 16px;
        font-weight: 700;
    }

    .product-media-note {
        color: #64748b;
    }

    .product-media-summary {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
    }

    .product-media-summary-text strong {
        display: block;
        color: #0f172a;
        font-size: 14px;
        font-weight: 700;
    }

    .product-media-summary-text span {
        display: block;
        color: #64748b;
        font-size: 12px;
        line-height: 1.5;
    }

    .product-media-selected-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(84px, 84px));
        gap: 10px;
        align-items: start;
    }

    .product-media-selected-item {
        position: relative;
        width: 84px;
        height: 84px;
        overflow: hidden;
        border-radius: 14px;
        border: 1px solid #cbd5e1;
        background: #fff;
    }

    .product-media-selected-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .product-media-remove {
        position: absolute;
        top: 4px;
        right: 4px;
        width: 24px;
        height: 24px;
        border: 0;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.82);
        color: #fff;
        font-size: 18px;
        line-height: 1;
    }

    .product-media-empty {
        grid-column: 1 / -1;
        padding: 16px;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        background: #fff;
        color: #64748b;
        font-size: 13px;
    }

    @keyframes product-media-spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 767px) {
        .media-picker-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        }

        .product-media-modal .modal-body,
        .product-media-modal .modal-header,
        .product-media-modal-footer {
            padding-left: 16px;
            padding-right: 16px;
        }

        .product-media-modal .btn,
        .product-media-modal-search {
            width: 100%;
        }
    }
</style>

<div class="modal fade product-media-modal" id="productMediaPickerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" data-product-media-modal-title>Choose Media</h5>
                    <div class="product-media-modal-hint" data-product-media-modal-subtitle>Select one or more items from the library.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="product-media-modal-toolbar">
                    <div class="product-media-modal-search">
                        <input type="search" class="form-control" placeholder="Search media by name, path or alt text" data-product-media-search>
                    </div>
                    <div class="product-media-modal-hint" data-product-media-result-hint>Latest media will load first.</div>
                </div>

                <div class="media-picker-grid" data-product-media-grid></div>
                <div class="alert alert-danger d-none mt-3" data-product-media-error></div>

                <div class="product-media-modal-loader" data-product-media-loader>
                    <span class="product-media-modal-spinner"></span>
                    <span>Loading more media...</span>
                </div>
            </div>

            <div class="product-media-modal-footer d-flex justify-content-between gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-product-media-load-more>Load more</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalElement = document.getElementById('productMediaPickerModal');

        if (!modalElement || typeof bootstrap === 'undefined') {
            return;
        }

        var modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        var grid = modalElement.querySelector('[data-product-media-grid]');
        var searchInput = modalElement.querySelector('[data-product-media-search]');
        var loadMoreButton = modalElement.querySelector('[data-product-media-load-more]');
        var loader = modalElement.querySelector('[data-product-media-loader]');
        var errorBox = modalElement.querySelector('[data-product-media-error]');
        var titleNode = modalElement.querySelector('[data-product-media-modal-title]');
        var subtitleNode = modalElement.querySelector('[data-product-media-modal-subtitle]');
        var hintNode = modalElement.querySelector('[data-product-media-result-hint]');
        var activeTarget = null;
        var activeLabel = 'Choose Media';
        var nextPage = null;
        var loading = false;
        var searchTimer = null;
        var mediaCache = new Map();
        var selectedIds = new Set();
        var baseUrl = '{{ route('media.index') }}';

        function getPicker(target) {
            return document.querySelector('[data-product-media-picker][data-product-media-target="' + target + '"]');
        }

        function getSelectedInputContainer(picker) {
            return picker ? picker.querySelector('[data-product-media-hidden-inputs]') : null;
        }

        function getSelectedGrid(picker) {
            return picker ? picker.querySelector('[data-product-media-selected-grid]') : null;
        }

        function readSelectedIds(picker) {
            if (!picker) {
                return [];
            }

            return Array.from(picker.querySelectorAll('[data-product-media-hidden-input]'))
                .map(function (input) { return String(input.value || '').trim(); })
                .filter(function (value) { return value !== ''; });
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

        function updateModalSelectionLabel(button, isSelected) {
            if (!button) {
                return;
            }

            button.textContent = isSelected ? 'Selected' : 'Select';
        }

        function syncGridSelection() {
            if (!grid) {
                return;
            }

            grid.querySelectorAll('[data-media-card]').forEach(function (card) {
                var isSelected = selectedIds.has(String(card.dataset.mediaId));
                card.classList.toggle('is-selected', isSelected);
                updateModalSelectionLabel(card.querySelector('[data-media-select]'), isSelected);
            });
        }

        function syncPickerSummary(picker) {
            if (!picker) {
                return;
            }

            var summaryTitle = picker.querySelector('[data-product-media-summary-title]');
            var summaryCopy = picker.querySelector('[data-product-media-summary-copy]');
            var selectedGrid = getSelectedGrid(picker);
            var hiddenInputs = getSelectedInputContainer(picker);
            var selectedItems = [];

            if (hiddenInputs) {
                hiddenInputs.innerHTML = '';
            }

            if (selectedGrid) {
                selectedGrid.innerHTML = '';
            }

            selectedIds.forEach(function (id) {
                var media = mediaCache.get(String(id));

                if (!media) {
                    return;
                }

                if (hiddenInputs) {
                    var hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = picker.dataset.productMediaTarget + '[]';
                    hidden.value = media.id;
                    hidden.setAttribute('data-product-media-hidden-input', '');
                    hiddenInputs.appendChild(hidden);
                }

                if (selectedGrid) {
                    var item = document.createElement('div');
                    item.className = 'product-media-selected-item';
                    item.setAttribute('data-product-media-selected-item', '');
                    item.setAttribute('data-media-id', media.id);
                    item.innerHTML = ''
                        + '<img src="' + media.path + '" alt="' + media.alt + '">'
                        + '<button type="button" class="product-media-remove" data-product-media-remove aria-label="Remove media">&times;</button>';
                    selectedGrid.appendChild(item);
                }

                selectedItems.push(media);
            });

            if (summaryTitle && summaryCopy) {
                if (selectedItems.length) {
                    summaryTitle.textContent = selectedItems.length + ' media selected';
                    summaryCopy.textContent = 'Selection will be saved with the product.';
                } else {
                    summaryTitle.textContent = 'No media selected yet';
                    summaryCopy.textContent = 'Choose one or more media items from the library.';
                }
            }

            var clearButton = picker.querySelector('[data-product-media-clear]');
            if (clearButton) {
                clearButton.disabled = selectedItems.length === 0;
            }
        }

        function applyPickerState(target) {
            var picker = getPicker(target);

            if (!picker) {
                return;
            }

            syncPickerSummary(picker);
            syncGridSelection();
        }

        function setActiveTarget(target, label) {
            activeTarget = target;
            activeLabel = label || 'Choose Media';
            selectedIds = new Set(readSelectedIds(getPicker(activeTarget)));
        }

        function collectCacheFromGrid() {
            if (!grid) {
                return;
            }

            grid.querySelectorAll('[data-media-select]').forEach(function (button) {
                var media = {
                    id: String(button.dataset.mediaId || ''),
                    path: button.dataset.mediaPath || '',
                    name: button.dataset.mediaName || '',
                    alt: button.dataset.mediaAlt || ''
                };

                if (media.id) {
                    mediaCache.set(media.id, media);
                }
            });
        }

        function collectInitialCache() {
            document.querySelectorAll('[data-product-media-selected-item]').forEach(function (item) {
                var mediaId = String(item.dataset.mediaId || '');

                if (!mediaId) {
                    return;
                }

                var image = item.querySelector('img');

                mediaCache.set(mediaId, {
                    id: mediaId,
                    path: item.dataset.mediaPath || (image ? image.src : ''),
                    name: item.dataset.mediaName || '',
                    alt: item.dataset.mediaAlt || item.dataset.mediaName || ''
                });
            });
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

            collectCacheFromGrid();
            syncGridSelection();
        }

        async function fetchMedia(page, append) {
            if (!activeTarget || loading) {
                return;
            }

            loading = true;
            setGridLoading(true);
            setError('');
            updateLoadMoreState();

            var params = new URLSearchParams();
            params.set('picker', '1');
            params.set('page', String(page || 1));
            selectedIds.forEach(function (id) {
                params.append('selected_media_ids[]', String(id));
            });

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
                hintNode.textContent = nextPage ? 'More media available below.' : 'No more media to load.';
            } catch (error) {
                setError(error.message || 'Failed to load media.');
                if (!append && grid) {
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

        document.querySelectorAll('[data-open-product-media-picker]').forEach(function (button) {
            button.addEventListener('click', function () {
                var target = button.dataset.mediaTarget || 'selected_media_ids';
                var label = button.dataset.mediaLabel || 'Choose Media';

                setActiveTarget(target, label);
                titleNode.textContent = 'Choose ' + activeLabel;
                subtitleNode.textContent = 'Pick one or more media items for ' + activeLabel.toLowerCase() + '.';
                hintNode.textContent = 'Latest media will load first.';
                searchInput.value = '';
                modal.show();
                applyPickerState(activeTarget);
                fetchMedia(1, false);
            });
        });

        if (grid) {
            grid.addEventListener('click', function (event) {
                var button = event.target.closest('[data-media-select]');
                if (!button) {
                    return;
                }

                var mediaId = String(button.dataset.mediaId || '');

                if (!mediaId) {
                    return;
                }

                if (selectedIds.has(mediaId)) {
                    selectedIds.delete(mediaId);
                } else {
                    selectedIds.add(mediaId);
                }

                collectCacheFromGrid();
                applyPickerState(activeTarget);
            });
        }

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                if (!activeTarget) {
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

        collectInitialCache();

        document.addEventListener('click', function (event) {
            var removeButton = event.target.closest('[data-product-media-remove]');

            if (removeButton) {
                var selectedItem = removeButton.closest('[data-product-media-selected-item]');
                var mediaId = String(removeButton.dataset.mediaId || selectedItem?.dataset.mediaId || '');
                var picker = removeButton.closest('[data-product-media-picker]');

                if (picker) {
                    activeTarget = picker.dataset.productMediaTarget || activeTarget;
                    selectedIds = new Set(readSelectedIds(picker));
                }

                if (mediaId && picker) {
                    selectedIds.delete(mediaId);
                    syncPickerSummary(picker);
                    syncGridSelection();
                }
            }

            var clearButton = event.target.closest('[data-product-media-clear]');
            if (clearButton) {
                var clearPicker = clearButton.closest('[data-product-media-picker]');

                selectedIds.clear();

                if (clearPicker) {
                    activeTarget = clearPicker.dataset.productMediaTarget || activeTarget;
                    syncPickerSummary(clearPicker);
                }

                syncGridSelection();
            }
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            activeTarget = null;
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
