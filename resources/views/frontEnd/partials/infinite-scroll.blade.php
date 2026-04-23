@push('css')
<style>
    .infinite-scroll-loader {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-height: 34px;
        margin-top: 20px;
        color: #475569;
        font-size: 14px;
        font-weight: 600;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s ease;
    }

    .infinite-scroll-loader.is-visible {
        opacity: 1;
    }

    .infinite-scroll-loader::before {
        content: '';
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid rgba(15, 23, 42, 0.18);
        border-top-color: #f97316;
        animation: infiniteScrollSpin 0.8s linear infinite;
    }

    .custom_paginate[data-infinite-pagination] {
        display: none;
    }

    @keyframes infiniteScrollSpin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
@endpush

@push('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var list = document.querySelector('[data-infinite-list]');
        var pagination = document.querySelector('[data-infinite-pagination]');
        var loader = document.querySelector('[data-infinite-loader]');

        if (!list || !pagination || !loader) {
            return;
        }

        var nextLink = pagination.querySelector('.pagination .page-item.active + .page-item a, .pagination a[rel="next"]');
        var isLoading = false;
        var hasUserScrolled = false;
        var nextLoadAvailableAt = 0;

        function setLoaderState(visible) {
            loader.classList.toggle('is-visible', visible);
        }

        function resolveNextLink(doc) {
            var nextPagination = doc.querySelector('[data-infinite-pagination]');
            if (!nextPagination) {
                return null;
            }

            return nextPagination.querySelector('.pagination .page-item.active + .page-item a, .pagination a[rel="next"]');
        }

        function loadNextPage() {
            if (isLoading || !nextLink) {
                return;
            }

            if (Date.now() < nextLoadAvailableAt) {
                return;
            }

            isLoading = true;
            setLoaderState(true);

            fetch(nextLink.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Failed to load products');
                    }

                    return response.text();
                })
                .then(function (html) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(html, 'text/html');
                    var incomingList = doc.querySelector('[data-infinite-list]');

                    if (!incomingList) {
                        nextLink = null;
                        return;
                    }

                    Array.from(incomingList.children).forEach(function (item) {
                        list.appendChild(item);
                    });

                    nextLink = resolveNextLink(doc);

                    if (!nextLink) {
                        observer.unobserve(loader);
                    }
                })
                .catch(function (error) {
                    console.error(error);
                })
                .finally(function () {
                    isLoading = false;
                    nextLoadAvailableAt = Date.now() + 1200;
                    setLoaderState(false);
                });
        }

        function maybeLoadNextPage() {
            if (!hasUserScrolled || !nextLink || isLoading) {
                return;
            }

            var loaderTop = loader.getBoundingClientRect().top;
            if (loaderTop <= window.innerHeight + 240) {
                loadNextPage();
            }
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    loadNextPage();
                }
            });
        }, {
            rootMargin: '0px 0px 240px 0px'
        });

        if (nextLink) {
            observer.observe(loader);
            window.addEventListener('scroll', function () {
                hasUserScrolled = true;
                maybeLoadNextPage();
            }, { passive: true });
        }
    });
</script>
@endpush
