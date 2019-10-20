import '../css/app.scss';
import $ from 'jquery';

global.$ = global.jQuery = $;

require('./jquery-migrate');
require('./chosen.jquery');
require('./scripts');
require('./jquery.fancybox.pack');
require('./jquery.fancybox-thumbs');
require('./jquery.pep');
require('./jquery.rd-parallax');
require('./swiper');
require('./menu');
require('./rd-navbar');

(function ($) {
    $(document).ready(function () {
        var o = $("#back-top");
        o.hide();
        $(window).scroll(function () {
            if ($(this).scrollTop() > 100) {
                o.fadeIn()
            } else {
                o.fadeOut()
            }
        });
        $("#back-top-link").click(function () {
            $('html,body').animate({ scrollTop: 0 }, 'slow');
            return false;
        })
    })
})(jQuery);

(function ($) {
    var click = true;
    $('a[data-fancybox="fancybox"]').fancybox({
        padding: 0,
        margin: 0,
        loop: true,
        openSpeed: 500,
        closeSpeed: 500,
        nextSpeed: 500,
        prevSpeed: 500,
        afterLoad: function () {
            $('.fancybox-inner').click(function () {
                if (click == true) {
                    $('body').toggleClass('fancybox-full')
                }
            })
        },
        beforeShow: function () {
            $('body').addClass('fancybox-lock')
        },
        afterClose: function () {
            $('body').removeClass('fancybox-lock')
        },
        tpl: {
            image: '<div class="fancybox-image" style="background-image: url(\'{href}\')"></div>',
            iframe: '<span class="iframe-before"/><iframe id="fancybox-frame{rnd}" width="60%" height="60%" name="fancybox-frame{rnd}" class="fancybox-iframe" frameborder="0" vspace="0" hspace="0"' + ($.browser.msie ? ' allowtransparency="true"' : '') + '/>'
        },
        helpers: {title: null, thumbs: {height: 50, width: 80}, overlay: {css: {'background': '#191919'}}}
    })
})(jQuery);
;(function ($, undefined) {
    $(document).ready(function () {
        function isIE() {
            var myNav = navigator.userAgent.toLowerCase();
            return (myNav.indexOf('msie') != -1) ? parseInt(myNav.split('msie')[1]) : false;
        };
        var o = $("#swiper-slider");
        if (o.length) {
            function getSwiperHeight(object, attr) {
                var val = object.attr("data-" + attr), dim;
                if (!val) {
                    return undefined;
                }
                dim = val.match(/(px)|(%)|(vh)$/i);
                if (dim.length) {
                    switch (dim[0]) {
                        case"px":
                            return parseFloat(val);
                        case"vh":
                            return $(window).height() * (parseFloat(val) / 100);
                        case"%":
                            return object.width() * (parseFloat(val) / 100);
                    }
                } else {
                    return undefined;
                }
            }

            function toggleSwiperCaptionAnimation(swiper) {
                if (isIE() && isIE() < 10) {
                    return;
                }
                var prevSlide = $(swiper.container), nextSlide = $(swiper.slides[swiper.activeIndex]);
                prevSlide.find("[data-caption-animate]").each(function () {
                    var $this = $(this);
                    $this.removeClass("animated").removeClass($this.attr("data-caption-animate")).addClass("not-animated");
                });
                nextSlide.find("[data-caption-animate]").each(function () {
                    var $this = $(this), delay = $this.attr("data-caption-delay");
                    setTimeout(function () {
                        $this.removeClass("not-animated").addClass($this.attr("data-caption-animate")).addClass("animated");
                    }, delay ? parseInt(delay) : 0);
                });
            }

            $(document).ready(function () {
                o.each(function () {
                    var s = $(this);
                    var pag = s.find(".swiper-pagination"), next = s.find(".swiper-button-next"),
                        prev = s.find(".swiper-button-prev"), bar = s.find(".swiper-scrollbar"),
                        h = getSwiperHeight(o, "height"), mh = getSwiperHeight(o, "min-height");
                    s.find(".swiper-slide").each(function () {
                        var $this = $(this), url;
                        if (url = $this.attr("data-slide-bg")) {
                            $this.css({"background-image": "url(" + url + ")", "background-size": "cover"})
                        }
                    }).end().find("[data-caption-animate]").addClass("not-animated").end();

                    var slider = new Swiper(s, {
                        observer: true,
                        observeParents: true,
                        autoplay: s.attr('data-autoplay') ? s.attr('data-autoplay') === "false" ? undefined : s.attr('data-autoplay') : 5000,
                        direction: s.attr('data-direction') ? s.attr('data-direction') : "horizontal",
                        effect: s.attr('data-slide-effect') ? s.attr('data-slide-effect') : "slide",
                        speed: s.attr('data-slide-speed') ? s.attr('data-slide-speed') : 600,
                        keyboardControl: s.attr('data-keyboard') === "true",
                        mousewheelControl: s.attr('data-mousewheel') === "true",
                        mousewheelReleaseOnEdges: s.attr('data-mousewheel-release') === "true",
                        nextButton: next.length ? next.get(0) : null,
                        prevButton: prev.length ? prev.get(0) : null,
                        pagination: pag.length ? pag.get(0) : null,
                        paginationClickable: pag.length ? pag.attr("data-clickable") !== "false" : false,
                        paginationBulletRender: pag.length ? pag.attr("data-index-bullet") === "true" ? function (index, className) {
                            return '<span class="' + className + '">' + (index + 1) + '</span>';
                        } : null : null,
                        scrollbar: bar.length ? bar.get(0) : null,
                        scrollbarDraggable: bar.length ? bar.attr("data-draggable") !== "false" : true,
                        scrollbarHide: bar.length ? bar.attr("data-draggable") === "false" : false,
                        loop: s.attr('data-loop') !== "false",
                        loopedSlides: 3,
                        autoplayDisableOnInteraction: false,
                        onTransitionEnd: function (swiper) {
                            toggleSwiperCaptionAnimation(swiper);
                        },
                        onInit: function (swiper) {
                            swiper.update();
                            toggleSwiperCaptionAnimation(swiper);
                            var o = $(swiper.container).find('.rd-parallax');
                            if (o.length && window.RDParallax) {
                                o.RDParallax({layerDirection: ($('html').hasClass("smoothscroll") || $('html').hasClass("smoothscroll-all")) && !isIE() ? "normal" : "inverse"});
                            }
                        }
                    });
                    $(window).on("resize", function () {
                        var mh = getSwiperHeight(s, "min-height"),
                            h = getSwiperHeight(s, "height");
                        if (h) {
                            s.css("height", mh ? mh > h ? mh : h : h);
                        }
                    }).load(function () {
                        s.find("video").each(function () {
                            if (!$(this).parents(".swiper-slide-active").length) {
                                this.pause();
                            }
                        });
                    }).trigger("resize");
                });
            });
        }
    });
})(jQuery);

jQuery(function ($) {
    var e = $(window).width();
    $("#icemegamenu").find(".icesubMenu").each(function (a) {
        var b = $(this).offset();
        var c = b.left + $(this).width();
        if (c >= e) {
            $(this).addClass("ice_righttoleft")
        }
    });
    $(window).resize(function () {
        var d = $(window).width();
        $("#icemegamenu").find(".icesubMenu").removeClass("ice_righttoleft").each(function (a) {
            var b = $(this).offset();
            var c = b.left + $(this).width();
            if (c >= d) {
                $(this).addClass("ice_righttoleft")
            }
        })
    })
});

