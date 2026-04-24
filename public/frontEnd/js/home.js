$(document).ready(function () {
    function sliderItems(key, fallback) {
        if (typeof window.getSliderItems === "function") {
            return window.getSliderItems(key, fallback);
        }

        return fallback;
    }

    $(".main_slider").owlCarousel({
        items: 1,
        loop: true,
        dots: true,
        autoplay: true,
        nav: true,
        navText: [
            '<i class="fa-solid fa-arrow-left"></i>',
            '<i class="fa-solid fa-arrow-right"></i>'
        ],
        autoplayHoverPause: true,
        margin: 0,
        mouseDrag: true,
        smartSpeed: 900,
        autoplayTimeout: 5000,
        animateOut: "fadeOut"
    });

    const hotDealsSliderItems = sliderItems("hotdeals_slider", { mobile: 3, tablet: 3, desktop: 6 });
    $(".hotdeals-slider").owlCarousel({
        margin: 15,
        loop: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: hotDealsSliderItems.mobile,
                nav: true
            },
            600: {
                items: hotDealsSliderItems.tablet,
                nav: false
            },
            1000: {
                items: hotDealsSliderItems.desktop,
                nav: true,
                loop: false
            }
        }
    });

    const hotDealsSlider1Items = sliderItems("hotdeals_slider1", { mobile: 2, tablet: 3, desktop: 6 });
    $(".hotdeals-slider1").owlCarousel({
        margin: 15,
        loop: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: hotDealsSlider1Items.mobile,
                nav: true
            },
            600: {
                items: hotDealsSlider1Items.tablet,
                nav: false
            },
            1000: {
                items: hotDealsSlider1Items.desktop,
                nav: true,
                loop: false
            }
        }
    });

    const hotDealsSlider111Items = sliderItems("hotdeals_slider111", { mobile: 2, tablet: 3, desktop: 4 });
    $(".hotdeals-slider111").owlCarousel({
        margin: 15,
        loop: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: hotDealsSlider111Items.mobile,
                nav: true
            },
            600: {
                items: hotDealsSlider111Items.tablet,
                nav: false
            },
            1000: {
                items: hotDealsSlider111Items.desktop,
                nav: true,
                loop: false
            }
        }
    });

    function initShowcaseProductSlider(selector, sliderKey) {
        const showcaseItems = sliderItems(sliderKey, { mobile: 2, tablet: 3, desktop: 5 });
        $(selector).owlCarousel({
            margin: 12,
            loop: true,
            dots: false,
            nav: true,
            navText: [
                '<i class="fa-solid fa-chevron-left"></i>',
                '<i class="fa-solid fa-chevron-right"></i>'
            ],
            autoplay: true,
            autoplayTimeout: 5200,
            autoplayHoverPause: true,
            smartSpeed: 650,
            touchDrag: true,
            mouseDrag: true,
            responsiveClass: true,
            responsive: {
                0: {
                    items: showcaseItems.mobile,
                    nav: false,
                    dots: true,
                    margin: 8
                },
                400: {
                    items: showcaseItems.mobile,
                    nav: false,
                    dots: true,
                    margin: 8
                },
                480: {
                    items: showcaseItems.mobile,
                    nav: false,
                    dots: true,
                    margin: 10
                },
                768: {
                    items: showcaseItems.tablet,
                    nav: false,
                    dots: false,
                    margin: 12
                },
                1024: {
                    items: Math.max(showcaseItems.tablet, showcaseItems.desktop - 1),
                    nav: true,
                    dots: false,
                    margin: 14
                },
                1280: {
                    items: showcaseItems.desktop,
                    nav: true,
                    dots: false,
                    margin: 15
                }
            }
        });
    }

    initShowcaseProductSlider(".best_seller_slider", "showcase_product_slider");
    initShowcaseProductSlider(".recently_viewed_slider", "showcase_product_slider");
    initShowcaseProductSlider(".featured_products_slider", "showcase_product_slider");
    initShowcaseProductSlider(".category-products-slider", "showcase_product_slider");

    const productSliderItems = sliderItems("product_slider", { mobile: 2, tablet: 3, desktop: 5 });
    $(".product_slider").owlCarousel({
        margin: 15,
        items: 4,
        loop: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: productSliderItems.mobile,
                nav: false
            },
            600: {
                items: productSliderItems.tablet,
                nav: false
            },
            1000: {
                items: productSliderItems.desktop,
                nav: false
            }
        }
    });

    const productSlider2Items = sliderItems("product_slider2", { mobile: 2, tablet: 4, desktop: 4 });
    $(".product_slider2").owlCarousel({
        margin: 15,
        items: 6,
        loop: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: productSlider2Items.mobile,
                nav: false
            },
            600: {
                items: productSlider2Items.tablet,
                nav: false
            },
            1000: {
                items: productSlider2Items.desktop,
                nav: false
            }
        }
    });

    const productSliders3Items = sliderItems("product_sliders3", { mobile: 2, tablet: 4, desktop: 6 });
    $(".product_sliders3").owlCarousel({
        margin: 15,
        items: 6,
        loop: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: productSliders3Items.mobile,
                nav: false
            },
            600: {
                items: productSliders3Items.tablet,
                nav: false
            },
            1000: {
                items: productSliders3Items.desktop,
                nav: false
            }
        }
    });

    const productSliderCategoryItems = sliderItems("product_slider_category", { mobile: 2, tablet: 3, desktop: 4 });
    $(".product_slider-category").owlCarousel({
        margin: 15,
        items: 4,
        loop: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 6000,
        autoplayHoverPause: true,
        responsiveClass: true,
        responsive: {
            0: {
                items: productSliderCategoryItems.mobile,
                nav: false
            },
            600: {
                items: productSliderCategoryItems.tablet,
                nav: false
            },
            1000: {
                items: productSliderCategoryItems.desktop,
                nav: false
            }
        }
    });

    const featuredCategoryLayout2Items = sliderItems("featured_category_layout2_slider", { mobile: 2, tablet: 4, desktop: 8 });
    $(".featured-category-layout2-slider").owlCarousel({
        margin: 12,
        loop: true,
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        smartSpeed: 650,
        nav: true,
        navText: [
            '<i class="fa-solid fa-chevron-left"></i>',
            '<i class="fa-solid fa-chevron-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0: {
                items: featuredCategoryLayout2Items.mobile,
                nav: false,
                dots: true,
                margin: 8
            },
            576: {
                items: Math.max(featuredCategoryLayout2Items.mobile, Math.min(featuredCategoryLayout2Items.tablet, 3)),
                nav: false,
                dots: true,
                margin: 10
            },
            768: {
                items: featuredCategoryLayout2Items.tablet,
                nav: false,
                dots: false,
                margin: 10
            },
            992: {
                items: Math.max(featuredCategoryLayout2Items.tablet, featuredCategoryLayout2Items.desktop - 2),
                nav: true,
                dots: false,
                margin: 12
            },
            1400: {
                items: featuredCategoryLayout2Items.desktop,
                nav: true,
                dots: false,
                margin: 12
            }
        }
    });

    $("#simple_timer").syotimer({
        date: new Date(2015, 0, 1),
        layout: "hms",
        doubleNumbers: false,
        effectType: "opacity",
        periodUnit: "d",
        periodic: true,
        periodInterval: 1
    });
});
