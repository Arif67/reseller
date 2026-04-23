(function (window, document, $) {
    'use strict';

    if (!$) {
        return;
    }

    const config = window.frontendConfig || {};
    const selectors = {
        cartQty: '#cart-qty',
        mobileCartQty: '.mobilecart-qty',
        cartList: '.cartlist',
        cartSummary: '.cart-summary',
        searchResult: '.search_result',
        customModal: '#custom-modal',
        pageOverlay: '#page-overlay',
        mobileMenu: '.mobile-menu',
        featureProducts: '.feature-products',
        contactButtons: '#contactButtons',
        scrollTop: '.scrolltop'
    };

    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(^|;\\s*)' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[2]) : '';
    }

    function getStoredUtm() {
        try {
            return JSON.parse(window.localStorage.getItem('utm_campaign_data') || '{}');
        } catch (error) {
            return {};
        }
    }

    function inferMarketingSource(utmSource, utmMedium, referrerUrl, clickIds) {
        const sourceText = ((utmSource || '') + ' ' + (utmMedium || '')).toLowerCase();
        const ids = clickIds || {};
        const referrerHost = (function () {
            try {
                return referrerUrl ? new URL(referrerUrl).hostname.toLowerCase() : '';
            } catch (error) {
                return '';
            }
        }());

        if (ids.fbclid) {
            return 'facebook';
        }

        if (ids.ttclid) {
            return 'tiktok';
        }

        if (ids.gclid || ids.wbraid || ids.gbraid) {
            return 'google';
        }

        if (/facebook|instagram|meta|messenger|\bfb\b|\big\b/.test(sourceText) || /facebook\.com|fb\.com|instagram\.com|messenger\.com/.test(referrerHost)) {
            return 'facebook';
        }

        if (/tiktok/.test(sourceText) || /tiktok\.com/.test(referrerHost)) {
            return 'tiktok';
        }

        if (/google|gads|adwords|youtube/.test(sourceText)) {
            return 'google';
        }

        if (/google\.|bing\.com|yahoo\.com|duckduckgo\.com/.test(referrerHost)) {
            return 'organic';
        }

        if (referrerHost) {
            return 'referral';
        }

        return 'direct';
    }

    function captureUtmParams() {
        if (!config.marketing || !config.marketing.utmTrackingEnabled) {
            return;
        }

        const storedData = getStoredUtm();
        const params = new URLSearchParams(window.location.search);
        const clickIds = {
            fbclid: params.get('fbclid') || storedData.fbclid || '',
            ttclid: params.get('ttclid') || storedData.ttclid || '',
            gclid: params.get('gclid') || storedData.gclid || '',
            wbraid: params.get('wbraid') || storedData.wbraid || '',
            gbraid: params.get('gbraid') || storedData.gbraid || ''
        };
        const hasCampaignParams = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].some(function (key) {
            return params.get(key);
        });
        const utmData = {
            utm_source: hasCampaignParams ? (params.get('utm_source') || '') : (storedData.utm_source || ''),
            utm_medium: hasCampaignParams ? (params.get('utm_medium') || '') : (storedData.utm_medium || ''),
            utm_campaign: hasCampaignParams ? (params.get('utm_campaign') || '') : (storedData.utm_campaign || ''),
            utm_term: hasCampaignParams ? (params.get('utm_term') || '') : (storedData.utm_term || ''),
            utm_content: hasCampaignParams ? (params.get('utm_content') || '') : (storedData.utm_content || ''),
            landing_url: storedData.landing_url || window.location.href,
            referrer_url: storedData.referrer_url || document.referrer || '',
            fbclid: clickIds.fbclid,
            ttclid: clickIds.ttclid,
            gclid: clickIds.gclid,
            wbraid: clickIds.wbraid,
            gbraid: clickIds.gbraid
        };

        if (hasCampaignParams) {
            utmData.landing_url = window.location.href;
            utmData.referrer_url = document.referrer || storedData.referrer_url || '';
        }

        utmData.marketing_source = inferMarketingSource(utmData.utm_source, utmData.utm_medium, utmData.referrer_url, clickIds);
        window.localStorage.setItem('utm_campaign_data', JSON.stringify(utmData));
    }

    function getQueryParam(name) {
        return new URLSearchParams(window.location.search).get(name) || '';
    }

    function syncAbandonedCart(extra) {
        if (!config.marketingRoutes || !config.marketingRoutes.abandonedCartSync || !config.marketing || !config.marketing.abandonedCartEnabled) {
            return;
        }

        const utm = getStoredUtm();
        const payload = Object.assign({
            landing_url: utm.landing_url || window.location.href,
            utm_source: utm.utm_source || '',
            utm_medium: utm.utm_medium || '',
            utm_campaign: utm.utm_campaign || '',
            utm_term: utm.utm_term || '',
            utm_content: utm.utm_content || ''
        }, extra || {});

        fetch(config.marketingRoutes.abandonedCartSync, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': config.csrfToken || ''
            },
            body: JSON.stringify(payload)
        }).catch(function () {
            return null;
        });
    }

    function showToastr(type, message, title) {
        if (window.toastr && typeof window.toastr[type] === 'function') {
            window.toastr[type](message, title);
        }
    }

    function replaceFeatherIcons() {
        if (window.feather && typeof window.feather.replace === 'function') {
            window.feather.replace();
        }
    }

    function initWow() {
        if (window.WOW) {
            new window.WOW().init();
        }
    }

    function updateCartCount() {
        if (!config.routes || !config.routes.cartCount) {
            return;
        }

        $.get(config.routes.cartCount, function (html) {
            $(selectors.cartQty).html(html || '');
            replaceFeatherIcons();
        });
    }

    function updateMobileCartCount() {
        if (!config.routes || !config.routes.mobileCartCount) {
            return;
        }

        $.get(config.routes.mobileCartCount, function (html) {
            $(selectors.mobileCartQty).html(html || '');
        });
    }

    function updateCartSummary() {
        if (!config.routes || !config.routes.shippingCharge) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: config.routes.shippingCharge,
            dataType: 'html',
            success: function (response) {
                $(selectors.cartSummary).html(response);
            }
        });
    }

    function openMobileMenu() {
        $(selectors.pageOverlay).show();
        $(selectors.mobileMenu).addClass('active');
    }

    function closeMobileMenu() {
        $(selectors.pageOverlay).hide();
        $(selectors.mobileMenu).removeClass('active');
        $(selectors.featureProducts).removeClass('active');
    }

    function bindSearchFormValidation() {
        $(document).on('submit', '#searchForm, #MainSearch', function (event) {
            const keyword = ($(this).find('input[name="keyword"]').val() || '').trim();

            if (keyword !== '') {
                return true;
            }

            event.preventDefault();
            showToastr('error', 'Please enter a search keyword.');
            return false;
        });
    }

    function bindLiveSearch() {
        function performSearch(inputSelector) {
            const keyword = $(inputSelector).val();

            $.ajax({
                type: 'GET',
                data: { keyword: keyword },
                url: config.routes.liveSearch,
                success: function (products) {
                    $(selectors.searchResult).html(products || '');
                }
            });
        }

        $(document).on('keyup change', '.search_click', function () {
            performSearch('.search_keyword');
        });

        $(document).on('keyup change', '.msearch_click', function () {
            performSearch('.msearch_keyword');
        });
    }

    function bindQuickView() {
        $(document).on('click', '.quick_view', function () {
            const id = $(this).data('id');

            if (!id || !config.routes.quickView) {
                return;
            }

            $('#loading').show();

            $.ajax({
                type: 'GET',
                data: { id: id },
                url: config.routes.quickView,
                success: function (html) {
                    if (!html) {
                        return;
                    }

                    $(selectors.customModal).html(html).show();
                    $('#loading').hide();
                    $(selectors.pageOverlay).show();
                    replaceFeatherIcons();
                }
            });
        });
    }

    function refreshCartUi() {
        updateCartCount();
        updateMobileCartCount();
        updateCartSummary();
    }

    function bindCartActions() {
        $(document).on('click', '.addcartbutton', function () {
            const id = $(this).data('id');
            const qty = 1;

            if (!id) {
                return;
            }

            $.ajax({
                cache: false,
                type: 'GET',
                url: config.routes.addToCartBase + '/' + id + '/' + qty,
                dataType: 'json',
                success: function (data) {
                    if (!data) {
                        return;
                    }

                    showToastr('success', 'Product add to cart successfully', 'Success');
                    updateCartCount();
                    updateMobileCartCount();
                    syncAbandonedCart();
                }
            });
        });

        $(document).on('click', '.cart_store', function () {
            const id = $(this).data('id');
            const qty = $(this).parent().find('input').val() || 1;

            if (!id) {
                return;
            }

            $.ajax({
                type: 'GET',
                data: { id: id, qty: qty },
                url: config.routes.cartStore,
                success: function (data) {
                    if (!data) {
                        return;
                    }

                    showToastr('success', 'Product add to cart succfully', 'Success');
                    updateCartCount();
                    updateMobileCartCount();
                    syncAbandonedCart();
                }
            });
        });

        $(document).on('click', '.cart_remove', function () {
            const id = $(this).data('id');

            if (!id) {
                return;
            }

            $.ajax({
                type: 'GET',
                data: { id: id },
                url: config.routes.cartRemove,
                success: function (html) {
                    if (!html) {
                        return;
                    }

                    $(selectors.cartList).html(html);
                    refreshCartUi();
                    replaceFeatherIcons();
                    syncAbandonedCart();
                }
            });
        });

        $(document).on('click', '.cart_increment', function () {
            const id = $(this).data('id');

            if (!id) {
                return;
            }

            $.ajax({
                type: 'GET',
                data: { id: id },
                url: config.routes.cartIncrement,
                success: function (html) {
                    if (!html) {
                        return;
                    }

                    $(selectors.cartList).html(html);
                    updateCartCount();
                    updateMobileCartCount();
                    replaceFeatherIcons();
                    syncAbandonedCart();
                }
            });
        });

        $(document).on('click', '.cart_decrement', function () {
            const id = $(this).data('id');

            if (!id) {
                return;
            }

            $.ajax({
                type: 'GET',
                data: { id: id },
                url: config.routes.cartDecrement,
                success: function (html) {
                    if (!html) {
                        return;
                    }

                    $(selectors.cartList).html(html);
                    updateCartCount();
                    updateMobileCartCount();
                    replaceFeatherIcons();
                    syncAbandonedCart();
                }
            });
        });
    }

    function bindDistrictLoader() {
        $(document).on('change', '.district', function () {
            $.ajax({
                type: 'GET',
                data: { id: $(this).val() },
                url: config.routes.districts,
                success: function (res) {
                    const $area = $('.area');
                    $area.empty();

                    if (!res) {
                        return;
                    }

                    $area.append('<option value="">Select..</option>');
                    $.each(res, function (key, value) {
                        $area.append('<option value="' + key + '">' + value + '</option>');
                    });
                }
            });
        });
    }

    function bindLayoutToggles() {
        $(document).on('click', '.toggle', openMobileMenu);
        $(document).on('click', selectors.pageOverlay, closeMobileMenu);
        $(document).on('click', '.mobile-menu-close', closeMobileMenu);

        $(document).on('click', '.mobile-filter-toggle', function () {
            $(selectors.pageOverlay).show();
            $(selectors.featureProducts).addClass('active');
        });

        $(document).on('click', '.filter_btn', function () {
            $('.filter_sidebar').addClass('active');
            $('body').css('overflow-y', 'hidden');
        });

        $(document).on('click', '.filter_close', function () {
            $('.filter_sidebar').removeClass('active');
            $('body').css('overflow-y', 'auto');
        });

        $(document).on('click', '[data-contact-toggle]', function () {
            $(selectors.contactButtons).toggleClass('is-visible');
        });

        $(document).on('click', '[data-submenu-toggle]', function () {
            const $icon = $(this).find('i');
            const $submenu = $(this).closest('div').next('ul');

            if ($submenu.length) {
                $submenu.slideToggle(150);
                $icon.toggleClass('rotate');
            }
        });
    }

    function bindMobileCategoryTree() {
        $('.parent-category').each(function () {
            const $parent = $(this);
            const $toggle = $parent.find('.menu-category-toggle').first();
            const $submenu = $parent.find('.second-nav').first();

            $toggle.on('click', function () {
                $toggle.toggleClass('active');
                $submenu.slideToggle('fast');
                $parent.toggleClass('active');
            });
        });

        $('.parent-subcategory').each(function () {
            const $parent = $(this);
            const $toggle = $parent.find('.menu-subcategory-toggle').first();
            const $submenu = $parent.find('.third-nav').first();

            $toggle.on('click', function () {
                $toggle.toggleClass('active');
                $submenu.slideToggle('fast');
                $parent.toggleClass('active');
            });
        });
    }

    function initMmenu() {
        const menuElement = document.querySelector('#menu');
        const trigger = document.querySelector('a[href="#menu"]');

        if (!menuElement || !trigger || !window.MmenuLight) {
            return;
        }

        const menu = new window.MmenuLight(menuElement, 'all');
        const navigation = menu.navigation({
            selectedClass: 'Selected',
            slidingSubmenus: true,
            title: 'ক্যাটাগরি'
        });
        const drawer = menu.offcanvas({});

        if (!navigation) {
            return;
        }

        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            drawer.open();
        });
    }

    function initMetaPixels() {
        if (!config.meta) {
            return;
        }

        if (config.meta.tiktokPixelId) {
            (function (w, d, t) {
                w.TiktokAnalyticsObject = t;
                const ttq = (w[t] = w[t] || []);
                ttq.methods = ['page', 'track', 'identify', 'instances', 'debug', 'on', 'off', 'once', 'ready', 'alias', 'group', 'enableCookie', 'disableCookie', 'holdConsent', 'revokeConsent', 'grantConsent'];
                ttq.setAndDefer = function (target, method) {
                    target[method] = function () {
                        target.push([method].concat(Array.prototype.slice.call(arguments, 0)));
                    };
                };
                for (let i = 0; i < ttq.methods.length; i += 1) {
                    ttq.setAndDefer(ttq, ttq.methods[i]);
                }
                ttq.instance = function (id) {
                    const instance = ttq._i[id] || [];
                    for (let i = 0; i < ttq.methods.length; i += 1) {
                        ttq.setAndDefer(instance, ttq.methods[i]);
                    }
                    return instance;
                };
                ttq.load = function (id, options) {
                    const src = 'https://analytics.tiktok.com/i18n/pixel/events.js';
                    ttq._i = ttq._i || {};
                    ttq._i[id] = [];
                    ttq._i[id]._u = src;
                    ttq._t = ttq._t || {};
                    ttq._t[id] = +new Date();
                    ttq._o = ttq._o || {};
                    ttq._o[id] = options || {};
                    const script = d.createElement('script');
                    script.type = 'text/javascript';
                    script.async = true;
                    script.src = src + '?sdkid=' + id + '&lib=' + t;
                    const firstScript = d.getElementsByTagName('script')[0];
                    firstScript.parentNode.insertBefore(script, firstScript);
                };
                ttq.load(config.meta.tiktokPixelId);
                ttq.page();
            }(window, document, 'ttq'));
        }

        if (config.meta && config.meta.facebookPixelId && !window.fbq) {
            (function (f, b, e, v, n, t, s) {
                if (f.fbq) {
                    return;
                }
                n = f.fbq = function () {
                    if (n.callMethod) {
                        n.callMethod.apply(n, arguments);
                    } else {
                        n.queue.push(arguments);
                    }
                };
                if (!f._fbq) {
                    f._fbq = n;
                }
                n.push = n;
                n.loaded = true;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = true;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s);
            }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js'));

            window.fbq('init', config.meta.facebookPixelId);
            window.fbq('track', 'PageView');
        }
    }

    function fireServerSidePageView() {
        if (!config.routes || !config.meta) {
            return;
        }

        const eventId = 'pageview_' + Date.now() + '_' + Math.random().toString(36).slice(2, 10);
        const payload = {
            event_id: eventId,
            event_source_url: window.location.href,
            client_ip_address: config.clientIp || '',
            client_user_agent: window.navigator.userAgent,
            fbp: getCookie('_fbp'),
            fbc: getCookie('_fbc'),
            ttp: getCookie('_ttp'),
            ttclid: getQueryParam('ttclid')
        };

        if (config.routes.facebookPageView && config.meta.facebookCapiEnabled) {
            fetch(config.routes.facebookPageView, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken || ''
                },
                body: JSON.stringify(payload)
            }).catch(function (error) {
                console.error('Facebook PageView CAPI error:', error);
            });
        }

        if (config.routes.tiktokPageView && config.meta.tiktokCapiEnabled) {
            fetch(config.routes.tiktokPageView, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken || ''
                },
                body: JSON.stringify(payload)
            }).catch(function (error) {
                console.error('TikTok PageView Events API error:', error);
            });
        }
    }

    function initScrollTop() {
        $(window).on('scroll', function () {
            if ($(this).scrollTop() > 50) {
                $(selectors.scrollTop + ':hidden').stop(true, true).fadeIn();
            } else {
                $(selectors.scrollTop).stop(true, true).fadeOut();
            }
        });

        $(document).on('click', '.scroll', function () {
            $('html, body').animate({ scrollTop: $('.gotop').offset().top }, 1000);
            return false;
        });
    }

    $(function () {
        initWow();
        replaceFeatherIcons();
        bindSearchFormValidation();
        bindLiveSearch();
        bindQuickView();
        bindCartActions();
        bindDistrictLoader();
        bindLayoutToggles();
        bindMobileCategoryTree();
        captureUtmParams();
        syncAbandonedCart();
        initMmenu();
        initMetaPixels();
        fireServerSidePageView();
        initScrollTop();
    });
}(window, document, window.jQuery));
