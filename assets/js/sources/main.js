jQuery(function ($) {

    // ##################################################################################################
    //    Маска полей
    // ##################################################################################################
    //Copyright (c) 2007-2015 Josh Bush (digitalbush.com)
    !function (e) { "function" == typeof define && define.amd ? define(["jquery"], e) : "object" == typeof exports ? e(require("jquery")) : e(jQuery) }((function (e) { var t, n = navigator.userAgent, a = /iphone/i.test(n), i = /chrome/i.test(n), r = /android/i.test(n); e.mask = { definitions: { 9: "[0-9]", a: "[A-Za-z]", "*": "[A-Za-z0-9]", "~": "[A-Za-z0-9-_/+=]" }, autoclear: !0, dataName: "rawMaskFn", placeholder: "_", escape: "`" }, e.fn.extend({ caret: function (e, t) { var n; if (0 !== this.length && !this.is(":hidden") && this.get(0) === document.activeElement) return "number" == typeof e ? (t = "number" == typeof t ? t : e, this.each((function () { this.setSelectionRange ? this.setSelectionRange(e, t) : this.createTextRange && ((n = this.createTextRange()).collapse(!0), n.moveEnd("character", t), n.moveStart("character", e), n.select()) }))) : (this[0].setSelectionRange ? (e = this[0].selectionStart, t = this[0].selectionEnd) : document.selection && document.selection.createRange && (n = document.selection.createRange(), e = 0 - n.duplicate().moveStart("character", -1e5), t = e + n.text.length), { begin: e, end: t }) }, unmask: function () { return this.trigger("unmask") }, mask: function (n, o) { var c, l, u, s, f, g, h, m; if (!n && this.length > 0) { var p = e(this[0]).data(e.mask.dataName); return p ? p() : void 0 } return o = e.extend({ autoclear: e.mask.autoclear, placeholder: e.mask.placeholder, escape: e.mask.escape, completed: null }, o), c = e.mask.definitions, l = [], u = g = n.length, s = null, n = String(n), m = o.escape.repeat(2), e.each(n.split(""), (function (e, t) { if (n[e - 1] + n[e - 2] === m) return g -= 2, l.splice(-2), void l.push(null); "?" == t ? (g--, u = l.length) : c[t] ? (l.push(new RegExp(c[t])), null === s && (s = l.length - 1), e < u && (f = l.length - 1)) : l.push(null) })), n = n.replace(new RegExp(m.replace(/[.*+?^${}()|[\]\\]/g, "\\$&"), "g"), ""), this.trigger("unmask").each((function () { var c = e(this), m = e.map(n.split(""), (function (e, t) { if (u != t) return l[t] ? b(t) : e })), p = m.join(""), d = c.val(); function v() { if (o.completed) { for (var e = s; e <= f; e++)if (l[e] && m[e] === b(e)) return; o.completed.call(c) } } function b(e) { return e < o.placeholder.length ? o.placeholder.charAt(e) : o.placeholder.charAt(0) } function k(e) { for (; ++e < g && !l[e];); return e } function y(e, t) { var n, a; if (!(e < 0)) { for (n = e, a = k(t); n < g; n++)if (l[n]) { if (!(a < g && l[n].test(m[a]))) break; m[n] = m[a], m[a] = b(a), a = k(a) } R(), c.caret(Math.max(s, e)) } } function x(e) { j(), c.val() != d && c.change() } function A(e, t) { var n; for (n = e; n < t && n < g; n++)l[n] && (m[n] = b(n)) } function R() { c.val(m.join("")) } function j(e) { var t, n, a, i = c.val(), r = -1; for (t = 0, a = 0; t < g; t++)if (l[t]) { for (m[t] = b(t); a++ < i.length;)if (n = i.charAt(a - 1), l[t].test(n)) { m[t] = n, r = t; break } if (a > i.length) { A(t + 1, g); break } } else m[t] === i.charAt(a) && a++, t < u && (r = t); return e ? R() : r < u ? o.autoclear || m.join("") === p ? (c.val() && c.val(""), A(0, g)) : R() : (R(), c.val(c.val().substring(0, r + 1))), u ? t : s } c.data(e.mask.dataName, (function () { return e.map(m, (function (e, t) { return l[t] && e != b(t) ? e : null })).join("") })), c.one("unmask", (function () { c.off(".mask").removeData(e.mask.dataName) })).on("focus.mask", (function () { var e; c.prop("readonly") || (clearTimeout(t), d = c.val(), e = j(), t = setTimeout((function () { c.get(0) === document.activeElement && (R(), e == n.replace("?", "").length ? c.caret(0, e) : c.caret(e)) }), 10)) })).on("blur.mask", x).on("keydown.mask", (function (e) { if (!c.prop("readonly")) { var t, n, i, r = e.which || e.keyCode; h = c.val(), 8 === r || 46 === r || a && 127 === r ? (n = (t = c.caret()).begin, (i = t.end) - n == 0 && (n = 46 !== r ? function (e) { for (; --e >= 0 && !l[e];); return e }(n) : i = k(n - 1), i = 46 === r ? k(i) : i), A(n, i), y(n, i - 1), e.preventDefault()) : 13 === r ? x.call(this, e) : 27 === r && (c.val(d), c.caret(0, j()), e.preventDefault()) } })).on("keypress.mask", (function (t) { if (!c.prop("readonly")) { var n, a, i, o = t.which || t.keyCode, u = c.caret(); if (!(t.ctrlKey || t.altKey || t.metaKey || o < 32) && o && 13 !== o) { if (u.end - u.begin != 0 && (A(u.begin, u.end), y(u.begin, u.end - 1)), (n = k(u.begin - 1)) < g && (a = String.fromCharCode(o), l[n].test(a))) { if (function (e) { var t, n, a, i; for (t = e, n = b(e); t < g; t++)if (l[t]) { if (a = k(t), i = m[t], m[t] = n, !(a < g && l[a].test(i))) break; n = i } }(n), m[n] = a, R(), i = k(n), r) { setTimeout((function () { e.proxy(e.fn.caret, c, i)() }), 0) } else c.caret(i); u.begin <= f && v() } t.preventDefault() } } })).on("input.mask paste.mask", (function () { c.prop("readonly") || setTimeout((function () { var e = j(!0); c.caret(e), v() }), 0) })), i && r && c.off("input.mask").on("input.mask", (function (e) { var t = c.val(), n = c.caret(); if (h && h.length && h.length > t.length) { for (j(!0); n.begin > 0 && !l[n.begin - 1];)n.begin--; if (0 === n.begin) for (; n.begin < s && !l[n.begin];)n.begin++; c.caret(n.begin, n.begin) } else { j(!0); var a = t.charAt(n.begin); n.begin < g && (l[n.begin] || n.begin++, l[n.begin].test(a) && n.begin++), c.caret(n.begin, n.begin) } v() })), j() })) } }) }));

    $('input[name="phone"]').mask("+9(999)999-99-99", { autoclear: false });
    $('[data-bs-toggle="tooltip"]').tooltip();

    $(document).on("input keyup mouseup select contextmenu drop", 'input.number', function () {
        if (/^-?\d*[.,]?\d*$/.test(this.value)) {
            this.oldValue = this.value;
            this.oldSelectionStart = this.selectionStart;
            this.oldSelectionEnd = this.selectionEnd;
        } else if (this.hasOwnProperty("oldValue")) {
            this.value = this.oldValue;
            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
        } else {
            this.value = "";
        }
    });

    $(document).on('formChanged', function () {
        $('input[required], select[required], textarea[required], input[data-required], select[data-required], textarea[data-required]')
            .closest('.form-group, .form-check').addClass('required');
    });
    $(document).trigger('formChanged');

    // ##################################################################################################
    //    Закрепление
    // ##################################################################################################

    var scrollPoint = 300;

    $(window).on('scroll', function () {
        if ($(this).scrollTop() > scrollPoint && $("#arrow-up").not(':visible')) {
            $("#arrow-up").fadeIn();
        } else if ($(this).scrollTop() < scrollPoint && $("#arrow-up").is(':visible')) {
            $("#arrow-up").fadeOut();
        }
    });

    $(document).on('click', '#arrow-up', function () {
        $('body,html').animate({
            scrollTop: 0
        }, 400);
        return false;
    });

    // ##################################################################################################
    //    Обратная связь
    // ##################################################################################################

    $(document).on('formsubmit', function (e, form, result) {
        var action = $(form).children('input[name="action"]').val();
        if (action == 'user_message') {
            if (result.success) {
                $(form).trigger('reset');
                $(form).find('form-attachments-list').empty();

                BlobStorage.revokeAllObjectsURL();
            }
        }
    });

    // ##################################################################################################
    //    Подсказки
    // ##################################################################################################

    $('[data-popover="tip"]').each(function (i) {
        var text = $(this).attr('data-text'),
            trigger = $(this).attr('data-trigger');

        $(this).popover({
            content: text,
            template:
                '<div class="popover popover-tip">' +
                    /**/'<div class="popover-arrow"></div>' +
                    /**/'<div class="popover-body"></div>' +
                '</div>',
            trigger: trigger
        })
    });

    $('[data-bs-toggle="popover"]').popover();


    // ##################################################################################################
    //    Метрика
    // ##################################################################################################

    //-------------------------------------------------
    // Главная

    $(document).on('click', '[data-bs-target="#registerForm"]', function (e) {
        reachGoal(metrikaID, 'registerButton', { 'userID': userID, 'UserPage': UserPage });
    });

    $(document).on('formsubmit', function (e, form, result) {
        var action = $(form).children('input[name="action"]').val();
        if (action == 'user_register') {
            if (result.success) {
                reachGoal(metrikaID, 'registered', { 'userID': userID, 'UserPage': UserPage });
            }
        }
    });

    //-------------------------------------------------
    // Навигация

    $(document).on('click', '#sidebar-menu a', function (e) {
        reachGoal(metrikaID, 'usedMainMenu', { 'userID': userID, 'UserPage': UserPage });
    });

    $(document).on('click', '#arrow-up', function (e) {
        reachGoal(metrikaID, 'usedScrollUp', { 'userID': userID, 'UserPage': UserPage });
    });

    $(document).on('click', '#sidebar', function (e) {
        reachGoal(metrikaID, 'usedRecordSidebar', { 'userID': userID, 'UserPage': UserPage });
    });

    // ##################################################################################################
    //    Подсказки
    // ##################################################################################################

    $('#sidebar-menu a').popover({
        trigger: 'hover',
        placement: 'right',
        container: '#sidebar-menu',
        delay: {
            show: 500,
            hide: 100
        },
        offset: [0, 10]
    });

    // ##################################################################################################
    //   Поля
    // ##################################################################################################

    var $emojiWindow = null;
    $('textarea.has-emoji').each(function (i, item) {
        var hideDelay = 100;

        $(this).after('<a class="btn btn-secondary btn-emoji">😊</a>')
            .next().popover({
                trigger: 'click hover',
                template:
                    '<div class="popover popover-emoji">' +
                    /**/'<div class="popover-arrow"></div>' +
                    /**/'<div class="popover-body"></div>' +
                    '</div>',
                html: true,
                delay: { hide: hideDelay },
                placement: 'top',
                container: $(this).parent().parent(),
                offset: [0, 9],
                title: wp.i18n.__('Pick emoji', 'sadovod-scripts'),
                content: '<div class="popover-header">' + wp.i18n.__('Loading...', 'sadovod-scripts') + '</div>',
                boundary: $(this).parent().get(0)
            }).on('show.bs.popover', function (event) {
                var that = this;
                if (!$emojiWindow) {
                    $emojiWindow = $('<div class="emoji-wrap">');
                    $.get(ajax.url + '?action=user_emoji&nonce=' + ajax.nonce, {})
                        .done(function (data) {
                            var user_emoji = JSON.parse(data);
                            $.each(user_emoji, function (group, emojis) {
                                $emojiWindow.append('<div class="emoji-group">' + group + '</div>');
                                $.each(emojis, function (k, emoji) {
                                    $emojiWindow.append('<span class="emoji-item" title="' + emoji[0] + '">' + emoji[1] + '</span>');
                                });
                            });

                            reloadEmojiList(that);
                        });
                } else {
                    setTimeout(function () {
                        reloadEmojiList(that);
                    }, 100);
                }
            }).on('shown.bs.popover', function (event) {
                var that = this;
                $('.popover').on('mouseenter', function () {
                    $(that).addClass('in');
                }).on('mouseleave', function () {
                    $(that).removeClass('in');
                    setTimeout(function () { $(that).popover('hide'); }, hideDelay);
                });
            }).on('hide.bs.popover', function (event) {
                if ($(this).hasClass('in')) {
                    event.preventDefault();
                }
            });
    });

    function reloadEmojiList(instance) {
        var popoverInstance = bootstrap.Popover.getInstance(instance),
            newContent = $emojiWindow.prop('outerHTML');

        if (popoverInstance._config.content != newContent) {
            popoverInstance._config.content = newContent;
            popoverInstance.setContent();
            popoverInstance.update();
        }
    }

    $(document).on('click', function (e) {
        $('[aria-describedby^="popover"]').each(function () {
            if (!$(this).attr('data-bs-content') && !$(this).is(e.target) && $('.popover').has(e.target).length === 0) {
                $(this).popover('hide');
            }
        });
    });

    $(document).on('click', '.emoji-item', function (e) {
        var input = $('[aria-describedby^="popover"]').prev().get(0);
        insertAtCursor(input, $(this).text());
    });

    // ##################################################################################################
    //   Главная
    // ##################################################################################################

    $('#interface').on('click', function (e) {
        if (scrollToAnchor('#' + $(this).next().attr('id'), true))
            e.preventDefault();
    });

    //-----------------------------------------------
    $(window).on('load', function () {
        setTimeout(function () {
            $('.slick-slider').each(function () {
                if ($(this).isInViewport()) {
                    $(this).trigger('mouseenter');
                }
            });
        }, 500);
    });

    var homeSlider = $('#home-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        //dots: true,
        autoplay: true,
        autoplaySpeed: 10000,
        draggable: true,
        pauseOnHover: true
    }).slick('getSlick');

    var homeHandlerTimeout = null;
    $('#home-slider').on('mouseenter', function (e) {
        homeHandlerTimeout = setTimeout(function () {
            $('#home-slider').on('scroll mousewheel DOMMouseScroll wheel', function (e) {
                updateSliderScroll(e, homeSlider, true);
                homeHandlerTimeout = null;
            });
        }, 200);
    });

    $('#home-slider').on('mouseleave', function (e) {
        if (homeHandlerTimeout) {
            clearTimeout(homeHandlerTimeout);
            homeHandlerTimeout = null;
        }

        $('#home-slider').off('scroll mousewheel DOMMouseScroll wheel');
    });

    //--------------------------------------------

    var sliderScrollBlocked = false;
    var lastSlideDelay = null;
    var lastScrollValue = 0;
    function updateSliderScroll(e, slider, infinite) {
        if (((e.originalEvent.deltaY && Number.isInteger(e.originalEvent.deltaY)) ||
            (e.originalEvent.deltaX && Math.abs(e.originalEvent.deltaY) < Math.abs(e.originalEvent.deltaX)))) {

            var isTouchPad = !Number.isInteger(e.originalEvent.deltaX);

            if (isTouchPad && lastScrollValue + 40 > e.originalEvent.deltaX) {
                lastScrollValue = e.originalEvent.deltaX;

                e.preventDefault();
                e.stopPropagation();

                return;
            }

            var slidesToShow = slider.slickGetOption('slidesToShow');
            var slidesToScroll = slider.slickGetOption('slidesToScroll');
            var maxSlide = (Math.floor(slider.slideCount / slidesToScroll) - Math.ceil(slidesToScroll / slidesToShow)) - 1;

            var deltaY = e.originalEvent.wheelDelta || -e.originalEvent.detail;
            var direction = deltaY < 0 ? 1 : (deltaY > 0 ? -1 : 0);

            if (direction) {
                if ((direction == -1 && (slider.currentSlide != 0)) ||
                    (direction == 1 && (slider.currentSlide != maxSlide)) ||
                    infinite || lastSlideDelay) {

                    e.preventDefault();
                    e.stopPropagation();

                    if (!sliderScrollBlocked) {
                        if (direction == -1) {
                            slider.slickPrev()
                        } else if (direction == 1) {
                            slider.slickNext()
                        }

                        sliderScrollBlocked = true;
                        setTimeout(function () { sliderScrollBlocked = false; }, 300);
                    }

                    if (!isTouchPad && !infinite && !lastSlideDelay &&
                        ((direction == -1 && slider.currentSlide == 0) ||
                            (direction == 1 && slider.currentSlide == maxSlide))) {

                        lastSlideDelay = setTimeout(function () { lastSlideDelay = null; }, 500);
                    }

                    lastScrollValue = e.originalEvent.deltaX;

                    document.scrollTop += deltaY * 30;
                    return false;
                }
            }
        }
    }

    //-----------------------------------------------

    $(window).on('hashchange', function () { scrollToAnchor(window.location.hash) });
    $(window).on('load', function () { scrollToAnchor(window.location.hash) });
    $(document).on('click', 'a', function (e) {
        if (scrollToAnchor($(this).attr('href'), true))
            e.preventDefault();
    });

    var HISTORY_SUPPORT = !!(history && history.pushState);
    function scrollToAnchor(href, pushToHistory) {
        var match = href && href.match(/^(#[^ ]+)$/);
        if (!match)
            return false;

        var $target = $(match[0]);
        if ($target && $target.offset()) {
            var offset = $target.attr('data-scroll-offset') || 0;

            $('html, body')
                .stop()
                .animate({
                    scrollTop: $target.offset().top - offset
                }, 200);

            // Add the state to history as-per normal anchor links
            if (HISTORY_SUPPORT && pushToHistory) {
                history.pushState({}, document.title, window.location.pathname + href);
            }
        }

        return !!$target;
    };

    // ##################################################################################################
    //    Gallery
    // ##################################################################################################

    !function(t){"use strict";"function"==typeof define&&define.amd?define(["./blueimp-helper"],t):(window.blueimp=window.blueimp||{},window.blueimp.Gallery=t(window.blueimp.helper||window.jQuery))}(function(t){"use strict";function e(t,i){return void 0===document.body.style.maxHeight?null:this&&this.options===e.prototype.options?void(t&&t.length?(this.list=t,this.num=t.length,this.initOptions(i),this.initialize()):this.console.log("blueimp Gallery: No or empty list provided as first argument.",t)):new e(t,i)}return t.extend(e.prototype,{options:{container:"#blueimp-gallery",slidesContainer:"div",titleElement:"h3",displayClass:"blueimp-gallery-display",controlsClass:"blueimp-gallery-controls",singleClass:"blueimp-gallery-single",leftEdgeClass:"blueimp-gallery-left",rightEdgeClass:"blueimp-gallery-right",playingClass:"blueimp-gallery-playing",svgasimgClass:"blueimp-gallery-svgasimg",smilClass:"blueimp-gallery-smil",slideClass:"slide",slideActiveClass:"slide-active",slidePrevClass:"slide-prev",slideNextClass:"slide-next",slideLoadingClass:"slide-loading",slideErrorClass:"slide-error",slideContentClass:"slide-content",toggleClass:"toggle",prevClass:"prev",nextClass:"next",closeClass:"close",playPauseClass:"play-pause",typeProperty:"type",titleProperty:"title",altTextProperty:"alt",urlProperty:"href",srcsetProperty:"srcset",sizesProperty:"sizes",sourcesProperty:"sources",displayTransition:!0,clearSlides:!0,toggleControlsOnEnter:!0,toggleControlsOnSlideClick:!0,toggleSlideshowOnSpace:!0,enableKeyboardNavigation:!0,closeOnEscape:!0,closeOnSlideClick:!0,closeOnSwipeUpOrDown:!0,closeOnHashChange:!0,emulateTouchEvents:!0,stopTouchEventsPropagation:!1,hidePageScrollbars:!0,disableScroll:!0,carousel:!1,continuous:!0,unloadElements:!0,startSlideshow:!1,slideshowInterval:5e3,slideshowDirection:"ltr",index:0,preloadRange:2,transitionDuration:300,slideshowTransitionDuration:500,event:void 0,onopen:void 0,onopened:void 0,onslide:void 0,onslideend:void 0,onslidecomplete:void 0,onclose:void 0,onclosed:void 0},carouselOptions:{hidePageScrollbars:!1,toggleControlsOnEnter:!1,toggleSlideshowOnSpace:!1,enableKeyboardNavigation:!1,closeOnEscape:!1,closeOnSlideClick:!1,closeOnSwipeUpOrDown:!1,closeOnHashChange:!1,disableScroll:!1,startSlideshow:!0},console:window.console&&"function"==typeof window.console.log?window.console:{log:function(){}},support:function(e){var i,s={source:!!window.HTMLSourceElement,picture:!!window.HTMLPictureElement,svgasimg:document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#Image","1.1"),smil:!!document.createElementNS&&/SVGAnimate/.test(document.createElementNS("http://www.w3.org/2000/svg","animate").toString()),touch:void 0!==window.ontouchstart||window.DocumentTouch&&document instanceof DocumentTouch},n={webkitTransition:{end:"webkitTransitionEnd",prefix:"-webkit-"},MozTransition:{end:"transitionend",prefix:"-moz-"},OTransition:{end:"otransitionend",prefix:"-o-"},transition:{end:"transitionend",prefix:""}};for(i in n)if(Object.prototype.hasOwnProperty.call(n,i)&&void 0!==e.style[i]){s.transition=n[i],s.transition.name=i;break}function o(){var t,i,n=s.transition;document.body.appendChild(e),n&&(t=n.name.slice(0,-9)+"ransform",void 0!==e.style[t]&&(e.style[t]="translateZ(0)",i=window.getComputedStyle(e).getPropertyValue(n.prefix+"transform"),s.transform={prefix:n.prefix,name:t,translate:!0,translateZ:!!i&&"none"!==i})),document.body.removeChild(e)}return document.body?o():t(document).on("DOMContentLoaded",o),s}(document.createElement("div")),requestAnimationFrame:window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame,cancelAnimationFrame:window.cancelAnimationFrame||window.webkitCancelRequestAnimationFrame||window.webkitCancelAnimationFrame||window.mozCancelAnimationFrame,initialize:function(){if(this.initStartIndex(),!1===this.initWidget())return!1;this.initEventListeners(),this.onslide(this.index),this.ontransitionend(),this.options.startSlideshow&&this.play()},slide:function(t,e){window.clearTimeout(this.timeout);var i,s,n,o=this.index;if(o!==t&&1!==this.num){if(e||(e=this.options.transitionDuration),this.support.transform){for(this.options.continuous||(t=this.circle(t)),i=Math.abs(o-t)/(o-t),this.options.continuous&&(s=i,(i=-this.positions[this.circle(t)]/this.slideWidth)!==s&&(t=-i*this.num+t)),n=Math.abs(o-t)-1;n;)n-=1,this.move(this.circle((t>o?t:o)-n-1),this.slideWidth*i,0);t=this.circle(t),this.move(o,this.slideWidth*i,e),this.move(t,0,e),this.options.continuous&&this.move(this.circle(t-i),-this.slideWidth*i,0)}else t=this.circle(t),this.animate(o*-this.slideWidth,t*-this.slideWidth,e);this.onslide(t)}},getIndex:function(){return this.index},getNumber:function(){return this.num},prev:function(){(this.options.continuous||this.index)&&this.slide(this.index-1)},next:function(){(this.options.continuous||this.index<this.num-1)&&this.slide(this.index+1)},play:function(t){var e=this,i=this.index+("rtl"===this.options.slideshowDirection?-1:1);window.clearTimeout(this.timeout),this.interval=t||this.options.slideshowInterval,this.elements[this.index]>1&&(this.timeout=this.setTimeout(!this.requestAnimationFrame&&this.slide||function(t,i){e.animationFrameId=e.requestAnimationFrame.call(window,function(){e.slide(t,i)})},[i,this.options.slideshowTransitionDuration],this.interval)),this.container.addClass(this.options.playingClass),this.slidesContainer[0].setAttribute("aria-live","off"),this.playPauseElement.length&&this.playPauseElement[0].setAttribute("aria-pressed","true")},pause:function(){window.clearTimeout(this.timeout),this.interval=null,this.cancelAnimationFrame&&(this.cancelAnimationFrame.call(window,this.animationFrameId),this.animationFrameId=null),this.container.removeClass(this.options.playingClass),this.slidesContainer[0].setAttribute("aria-live","polite"),this.playPauseElement.length&&this.playPauseElement[0].setAttribute("aria-pressed","false")},add:function(t){var e;for(t.concat||(t=Array.prototype.slice.call(t)),this.num=this.list.length,this.list.concat||(this.list=Array.prototype.slice.call(this.list)),this.list=this.list.concat(t),this.num=this.list.length,this.num>2&&null===this.options.continuous&&(this.options.continuous=!0,this.container.removeClass(this.options.leftEdgeClass)),this.container.removeClass(this.options.rightEdgeClass).removeClass(this.options.singleClass),e=this.num-t.length;e<this.num;e+=1)this.addSlide(e),this.positionSlide(e);this.positions.length=this.num,this.initSlides(!0)},resetSlides:function(){this.slidesContainer.empty(),this.unloadAllSlides(),this.slides=[]},handleClose:function(){var t=this.options;this.destroyEventListeners(),this.pause(),this.container[0].style.display="none",this.container.removeClass(t.displayClass).removeClass(t.singleClass).removeClass(t.leftEdgeClass).removeClass(t.rightEdgeClass),t.hidePageScrollbars&&(document.body.style.overflow=this.bodyOverflowStyle),this.options.clearSlides&&this.resetSlides(),this.options.onclosed&&this.options.onclosed.call(this)},close:function(){var t=this;this.options.onclose&&this.options.onclose.call(this),this.support.transition&&this.options.displayTransition?(this.container.on(this.support.transition.end,function e(i){i.target===t.container[0]&&(t.container.off(t.support.transition.end,e),t.handleClose())}),this.container.removeClass(this.options.displayClass)):this.handleClose()},circle:function(t){return(this.num+t%this.num)%this.num},move:function(t,e,i){this.translateX(t,e,i),this.positions[t]=e},translate:function(t,e,i,s){if(this.slides[t]){var n=this.slides[t].style,o=this.support.transition,a=this.support.transform;n[o.name+"Duration"]=s+"ms",n[a.name]="translate("+e+"px, "+i+"px)"+(a.translateZ?" translateZ(0)":"")}},translateX:function(t,e,i){this.translate(t,e,0,i)},translateY:function(t,e,i){this.translate(t,0,e,i)},animate:function(t,e,i){if(i)var s=this,n=(new Date).getTime(),o=window.setInterval(function(){var a=(new Date).getTime()-n;if(a>i)return s.slidesContainer[0].style.left=e+"px",s.ontransitionend(),void window.clearInterval(o);s.slidesContainer[0].style.left=(e-t)*(Math.floor(a/i*100)/100)+t+"px"},4);else this.slidesContainer[0].style.left=e+"px"},preventDefault:function(t){t.preventDefault?t.preventDefault():t.returnValue=!1},stopPropagation:function(t){t.stopPropagation?t.stopPropagation():t.cancelBubble=!0},onresize:function(){this.initSlides(!0)},onhashchange:function(){this.options.closeOnHashChange&&this.close()},onmousedown:function(t){t.which&&1===t.which&&"VIDEO"!==t.target.nodeName&&"AUDIO"!==t.target.nodeName&&(t.preventDefault(),(t.originalEvent||t).touches=[{pageX:t.pageX,pageY:t.pageY}],this.ontouchstart(t))},onmousemove:function(t){this.touchStart&&((t.originalEvent||t).touches=[{pageX:t.pageX,pageY:t.pageY}],this.ontouchmove(t))},onmouseup:function(t){this.touchStart&&(this.ontouchend(t),delete this.touchStart)},onmouseout:function(e){if(this.touchStart){var i=e.target,s=e.relatedTarget;s&&(s===i||t.contains(i,s))||this.onmouseup(e)}},ontouchstart:function(t){this.options.stopTouchEventsPropagation&&this.stopPropagation(t);var e=(t.originalEvent||t).touches[0];this.touchStart={x:e.pageX,y:e.pageY,time:Date.now()},this.isScrolling=void 0,this.touchDelta={}},ontouchmove:function(t){this.options.stopTouchEventsPropagation&&this.stopPropagation(t);var e,i,s=(t.originalEvent||t).touches,n=s[0],o=(t.originalEvent||t).scale,a=this.index;if(!(s.length>1||o&&1!==o))if(this.options.disableScroll&&t.preventDefault(),this.touchDelta={x:n.pageX-this.touchStart.x,y:n.pageY-this.touchStart.y},e=this.touchDelta.x,void 0===this.isScrolling&&(this.isScrolling=this.isScrolling||Math.abs(e)<Math.abs(this.touchDelta.y)),this.isScrolling)this.options.carousel||this.translateY(a,this.touchDelta.y+this.positions[a],0);else for(t.preventDefault(),window.clearTimeout(this.timeout),this.options.continuous?i=[this.circle(a+1),a,this.circle(a-1)]:(this.touchDelta.x=e/=!a&&e>0||a===this.num-1&&e<0?Math.abs(e)/this.slideWidth+1:1,i=[a],a&&i.push(a-1),a<this.num-1&&i.unshift(a+1));i.length;)a=i.pop(),this.translateX(a,e+this.positions[a],0)},ontouchend:function(t){this.options.stopTouchEventsPropagation&&this.stopPropagation(t);var e,i,s,n,o,a=this.index,l=Math.abs(this.touchDelta.x),r=this.slideWidth,h=Math.ceil(this.options.transitionDuration*(1-l/r)/2),d=l>20,c=!a&&this.touchDelta.x>0||a===this.num-1&&this.touchDelta.x<0,u=!d&&this.options.closeOnSwipeUpOrDown&&Math.abs(this.touchDelta.y)>20;this.options.continuous&&(c=!1),e=this.touchDelta.x<0?-1:1,this.isScrolling?u?this.close():this.translateY(a,0,h):d&&!c?(i=a+e,s=a-e,n=r*e,o=-r*e,this.options.continuous?(this.move(this.circle(i),n,0),this.move(this.circle(a-2*e),o,0)):i>=0&&i<this.num&&this.move(i,n,0),this.move(a,this.positions[a]+n,h),this.move(this.circle(s),this.positions[this.circle(s)]+n,h),a=this.circle(s),this.onslide(a)):this.options.continuous?(this.move(this.circle(a-1),-r,h),this.move(a,0,h),this.move(this.circle(a+1),r,h)):(a&&this.move(a-1,-r,h),this.move(a,0,h),a<this.num-1&&this.move(a+1,r,h))},ontouchcancel:function(t){this.touchStart&&(this.ontouchend(t),delete this.touchStart)},ontransitionend:function(t){var e=this.slides[this.index];t&&e!==t.target||(this.interval&&this.play(),this.setTimeout(this.options.onslideend,[this.index,e]))},oncomplete:function(e){var i,s=e.target||e.srcElement,n=s&&s.parentNode;s&&n&&(i=this.getNodeIndex(n),t(n).removeClass(this.options.slideLoadingClass),"error"===e.type?(t(n).addClass(this.options.slideErrorClass),this.elements[i]=3):this.elements[i]=2,s.clientHeight>this.container[0].clientHeight&&(s.style.maxHeight=this.container[0].clientHeight),this.interval&&this.slides[this.index]===n&&this.play(),this.setTimeout(this.options.onslidecomplete,[i,n]))},onload:function(t){this.oncomplete(t)},onerror:function(t){this.oncomplete(t)},onkeydown:function(t){switch(t.which||t.keyCode){case 13:this.options.toggleControlsOnEnter&&(this.preventDefault(t),this.toggleControls());break;case 27:this.options.closeOnEscape&&(this.close(),t.stopImmediatePropagation());break;case 32:this.options.toggleSlideshowOnSpace&&(this.preventDefault(t),this.toggleSlideshow());break;case 37:this.options.enableKeyboardNavigation&&(this.preventDefault(t),this.prev());break;case 39:this.options.enableKeyboardNavigation&&(this.preventDefault(t),this.next())}},handleClick:function(e){var i=this.options,s=e.target||e.srcElement,n=s.parentNode;function o(e){return t(s).hasClass(e)||t(n).hasClass(e)}o(i.toggleClass)?(this.preventDefault(e),this.toggleControls()):o(i.prevClass)?(this.preventDefault(e),this.prev()):o(i.nextClass)?(this.preventDefault(e),this.next()):o(i.closeClass)?(this.preventDefault(e),this.close()):o(i.playPauseClass)?(this.preventDefault(e),this.toggleSlideshow()):n===this.slidesContainer[0]?i.closeOnSlideClick?(this.preventDefault(e),this.close()):i.toggleControlsOnSlideClick&&(this.preventDefault(e),this.toggleControls()):n.parentNode&&n.parentNode===this.slidesContainer[0]&&i.toggleControlsOnSlideClick&&(this.preventDefault(e),this.toggleControls())},onclick:function(t){if(!(this.options.emulateTouchEvents&&this.touchDelta&&(Math.abs(this.touchDelta.x)>20||Math.abs(this.touchDelta.y)>20)))return this.handleClick(t);delete this.touchDelta},updateEdgeClasses:function(t){t?this.container.removeClass(this.options.leftEdgeClass):this.container.addClass(this.options.leftEdgeClass),t===this.num-1?this.container.addClass(this.options.rightEdgeClass):this.container.removeClass(this.options.rightEdgeClass)},updateActiveSlide:function(e,i){for(var s,n,o=this.slides,a=this.options,l=[{index:i,method:"addClass",hidden:!1},{index:e,method:"removeClass",hidden:!0}];l.length;)s=l.pop(),t(o[s.index])[s.method](a.slideActiveClass),n=this.circle(s.index-1),(a.continuous||n<s.index)&&t(o[n])[s.method](a.slidePrevClass),n=this.circle(s.index+1),(a.continuous||n>s.index)&&t(o[n])[s.method](a.slideNextClass);this.slides[e].setAttribute("aria-hidden","true"),this.slides[i].removeAttribute("aria-hidden")},handleSlide:function(t,e){this.options.continuous||this.updateEdgeClasses(e),this.updateActiveSlide(t,e),this.loadElements(e),this.options.unloadElements&&this.unloadElements(t,e),this.setTitle(e)},onslide:function(t){this.handleSlide(this.index,t),this.index=t,this.setTimeout(this.options.onslide,[t,this.slides[t]])},setTitle:function(t){var e=this.slides[t].firstChild,i=e.title||e.alt,s=this.titleElement;s.length&&(this.titleElement.empty(),i&&s[0].appendChild(document.createTextNode(i)))},setTimeout:function(t,e,i){var s=this;return t&&window.setTimeout(function(){t.apply(s,e||[])},i||0)},imageFactory:function(e,i){var s,n,o,a,l,r,h,d,c=this.options,u=this,p=e,m=this.imagePrototype.cloneNode(!1);if("string"!=typeof p&&(p=this.getItemProperty(e,c.urlProperty),o=this.support.picture&&this.support.source&&this.getItemProperty(e,c.sourcesProperty),a=this.getItemProperty(e,c.srcsetProperty),l=this.getItemProperty(e,c.sizesProperty),r=this.getItemProperty(e,c.titleProperty),h=this.getItemProperty(e,c.altTextProperty)||r),m.draggable=!1,r&&(m.title=r),h&&(m.alt=h),t(m).on("load error",function e(o){if(!n){if(!(o={type:o.type,target:s||m}).target.parentNode)return u.setTimeout(e,[o]);n=!0,t(m).off("load error",e),i(o)}}),o&&o.length){for(s=this.picturePrototype.cloneNode(!1),d=0;d<o.length;d+=1)s.appendChild(t.extend(this.sourcePrototype.cloneNode(!1),o[d]));s.appendChild(m),t(s).addClass(c.toggleClass)}return a&&(l&&(m.sizes=l),m.srcset=a),m.src=p,s||m},createElement:function(e,i){var s=e&&this.getItemProperty(e,this.options.typeProperty),n=s&&this[s.split("/")[0]+"Factory"]||this.imageFactory,o=e&&n.call(this,e,i);return o||(o=this.elementPrototype.cloneNode(!1),this.setTimeout(i,[{type:"error",target:o}])),t(o).addClass(this.options.slideContentClass),o},iteratePreloadRange:function(t,e){var i,s=this.num,n=this.options,o=Math.min(s,2*n.preloadRange+1),a=t;for(i=0;i<o;i+=1){if((a+=i*(i%2==0?-1:1))<0||a>=s){if(!n.continuous)continue;a=this.circle(a)}e.call(this,a)}},loadElement:function(e){this.elements[e]||(this.slides[e].firstChild?this.elements[e]=t(this.slides[e]).hasClass(this.options.slideErrorClass)?3:2:(this.elements[e]=1,t(this.slides[e]).addClass(this.options.slideLoadingClass),this.slides[e].appendChild(this.createElement(this.list[e],this.proxyListener))))},loadElements:function(t){this.iteratePreloadRange(t,this.loadElement)},unloadElements:function(t,e){var i=this.options.preloadRange;this.iteratePreloadRange(t,function(t){var s=Math.abs(t-e);s>i&&s+i<this.num&&(this.unloadSlide(t),delete this.elements[t])})},addSlide:function(t){var e=this.slidePrototype.cloneNode(!1);e.setAttribute("data-index",t),e.setAttribute("aria-hidden","true"),this.slidesContainer[0].appendChild(e),this.slides.push(e)},positionSlide:function(t){var e=this.slides[t];e.style.width=this.slideWidth+"px",this.support.transform&&(e.style.left=t*-this.slideWidth+"px",this.move(t,this.index>t?-this.slideWidth:this.index<t?this.slideWidth:0,0))},initSlides:function(e){var i,s;e||(this.positions=[],this.positions.length=this.num,this.elements={},this.picturePrototype=this.support.picture&&document.createElement("picture"),this.sourcePrototype=this.support.source&&document.createElement("source"),this.imagePrototype=document.createElement("img"),this.elementPrototype=document.createElement("div"),this.slidePrototype=this.elementPrototype.cloneNode(!1),t(this.slidePrototype).addClass(this.options.slideClass),this.slides=this.slidesContainer[0].children,i=this.options.clearSlides||this.slides.length!==this.num),this.slideWidth=this.container[0].clientWidth,this.slideHeight=this.container[0].clientHeight,this.slidesContainer[0].style.width=this.num*this.slideWidth+"px",i&&this.resetSlides();var n=[],o=[];for(s=0;s<this.num;s+=1){var a=this.list[s],l=this.getItemProperty(a,this.options.urlProperty);-1===n.indexOf(l)&&(o.push(a),n.push(l))}for(this.list=o,this.num=this.list.length,s=0;s<this.num;s+=1)i&&this.addSlide(s),this.positionSlide(s);this.options.continuous&&this.support.transform&&(this.move(this.circle(this.index-1),-this.slideWidth,0),this.move(this.circle(this.index+1),this.slideWidth,0)),this.support.transform||(this.slidesContainer[0].style.left=this.index*-this.slideWidth+"px")},unloadSlide:function(t){var e,i;null!==(i=(e=this.slides[t]).firstChild)&&e.removeChild(i)},unloadAllSlides:function(){var t,e;for(t=0,e=this.slides.length;t<e;t++)this.unloadSlide(t)},toggleControls:function(){var t=this.options.controlsClass;this.container.hasClass(t)?this.container.removeClass(t):this.container.addClass(t)},toggleSlideshow:function(){this.interval?this.pause():this.play()},getNodeIndex:function(t){return parseInt(t.getAttribute("data-index"),10)},getNestedProperty:function(t,e){return e.replace(/\[(?:'([^']+)'|"([^"]+)"|(\d+))\]|(?:(?:^|\.)([^\.\[]+))/g,function(e,i,s,n,o){var a=o||i||s||n&&parseInt(n,10);e&&t&&(t=t[a])}),t},getDataProperty:function(e,i){var s,n;if(e.dataset?(s=i.replace(/-([a-z])/g,function(t,e){return e.toUpperCase()}),n=e.dataset[s]):e.getAttribute&&(n=e.getAttribute("data-"+i.replace(/([A-Z])/g,"-$1").toLowerCase())),"string"==typeof n){if(/^(true|false|null|-?\d+(\.\d+)?|\{[\s\S]*\}|\[[\s\S]*\])$/.test(n))try{return t.parseJSON(n)}catch(t){}return n}},getItemProperty:function(t,e){var i=this.getDataProperty(t,e);return void 0===i&&(i=t[e]),void 0===i&&(i=this.getNestedProperty(t,e)),i},initStartIndex:function(){var t,e=this.options.index,i=this.options.urlProperty;if(e&&"number"!=typeof e)for(t=0;t<this.num;t+=1)if(this.list[t]===e||this.getItemProperty(this.list[t],i)===this.getItemProperty(e,i)){e=t;break}this.index=this.circle(parseInt(e,10)||0)},initEventListeners:function(){var e=this,i=this.slidesContainer;function s(t){var i=e.support.transition&&e.support.transition.end===t.type?"transitionend":t.type;e["on"+i](t)}t(window).on("resize",s),t(window).on("hashchange",s),t(document.body).on("keydown",s),this.container.on("click",s),this.support.touch?i.on("touchstart touchmove touchend touchcancel",s):this.options.emulateTouchEvents&&this.support.transition&&i.on("mousedown mousemove mouseup mouseout",s),this.support.transition&&i.on(this.support.transition.end,s),this.proxyListener=s},destroyEventListeners:function(){var e=this.slidesContainer,i=this.proxyListener;t(window).off("resize",i),t(document.body).off("keydown",i),this.container.off("click",i),this.support.touch?e.off("touchstart touchmove touchend touchcancel",i):this.options.emulateTouchEvents&&this.support.transition&&e.off("mousedown mousemove mouseup mouseout",i),this.support.transition&&e.off(this.support.transition.end,i)},handleOpen:function(){this.options.onopened&&this.options.onopened.call(this)},initWidget:function(){var e=this;return this.container=t(this.options.container),this.container.length?(this.slidesContainer=this.container.find(this.options.slidesContainer).first(),this.slidesContainer.length?(this.titleElement=this.container.find(this.options.titleElement).first(),this.playPauseElement=this.container.find("."+this.options.playPauseClass).first(),1===this.num&&this.container.addClass(this.options.singleClass),this.support.svgasimg&&this.container.addClass(this.options.svgasimgClass),this.support.smil&&this.container.addClass(this.options.smilClass),this.options.onopen&&this.options.onopen.call(this),this.support.transition&&this.options.displayTransition?this.container.on(this.support.transition.end,function t(i){i.target===e.container[0]&&(e.container.off(e.support.transition.end,t),e.handleOpen())}):this.handleOpen(),this.options.hidePageScrollbars&&(this.bodyOverflowStyle=document.body.style.overflow,document.body.style.overflow="hidden"),this.container[0].style.display="block",this.initSlides(),void this.container.addClass(this.options.displayClass)):(this.console.log("blueimp Gallery: Slides container not found.",this.options.slidesContainer),!1)):(this.console.log("blueimp Gallery: Widget container not found.",this.options.container),!1)},initOptions:function(e){this.options=t.extend({},this.options),(e&&e.carousel||this.options.carousel&&(!e||!1!==e.carousel))&&t.extend(this.options,this.carouselOptions),t.extend(this.options,e),this.num<3&&(this.options.continuous=!!this.options.continuous&&null),this.support.transition||(this.options.emulateTouchEvents=!1),this.options.event&&this.preventDefault(this.options.event)}}),e}),function(t){"use strict";"function"==typeof define&&define.amd?define(["./blueimp-helper","./blueimp-gallery"],t):t(window.blueimp.helper||window.jQuery,window.blueimp.Gallery)}(function(t,e){"use strict";var i=e.prototype;t.extend(i.options,{indicatorContainer:"ol",activeIndicatorClass:"active",thumbnailProperty:"thumbnail",thumbnailIndicators:!0});var s=i.initSlides,n=i.addSlide,o=i.resetSlides,a=i.handleClick,l=i.handleSlide,r=i.handleClose;return t.extend(i,{createIndicator:function(e){var i,s,n=this.indicatorPrototype.cloneNode(!1),o=this.getItemProperty(e,this.options.titleProperty),a=this.options.thumbnailProperty;return this.options.thumbnailIndicators&&(a&&(i=this.getItemProperty(e,a)),void 0===i&&(s=e.getElementsByTagName&&t(e).find("img")[0])&&((i=s.src)||void 0===s.dataset.src||(i=s.dataset.src)),i&&(n.style.backgroundImage='url("'+i+'")')),o&&(n.title=o),n.setAttribute("role","link"),n},addIndicator:function(t){if(this.indicatorContainer.length){var e=this.createIndicator(this.list[t]);e.setAttribute("data-index",t),this.indicatorContainer[0].appendChild(e),this.indicators.push(e)}},setActiveIndicator:function(e){this.indicators&&(this.activeIndicator&&this.activeIndicator.removeClass(this.options.activeIndicatorClass),this.activeIndicator=t(this.indicators[e]),this.activeIndicator.addClass(this.options.activeIndicatorClass))},initSlides:function(t){t||(this.indicatorContainer=this.container.find(this.options.indicatorContainer),this.indicatorContainer.length&&(this.indicatorPrototype=document.createElement("li"),this.indicators=this.indicatorContainer[0].children)),s.call(this,t)},addSlide:function(t){n.call(this,t),this.addIndicator(t)},resetSlides:function(){o.call(this),this.indicatorContainer.empty(),this.indicators=[]},handleClick:function(t){var e=t.target||t.srcElement,i=e.parentNode;if(i===this.indicatorContainer[0])this.preventDefault(t),this.slide(this.getNodeIndex(e));else{if(i.parentNode!==this.indicatorContainer[0])return a.call(this,t);this.preventDefault(t),this.slide(this.getNodeIndex(i))}},handleSlide:function(t,e){l.call(this,t,e),this.setActiveIndicator(e)},handleClose:function(){this.activeIndicator&&this.activeIndicator.removeClass(this.options.activeIndicatorClass),r.call(this)}}),e}),function(t){"use strict";"function"==typeof define&&define.amd?define(["./blueimp-helper","./blueimp-gallery"],t):t(window.blueimp.helper||window.jQuery,window.blueimp.Gallery)}(function(t,e){"use strict";var i=e.prototype;t.extend(i.options,{fullscreen:!1});var s=i.initialize,n=i.close;return t.extend(i,{getFullScreenElement:function(){return document.fullscreenElement||document.webkitFullscreenElement||document.mozFullScreenElement||document.msFullscreenElement},requestFullScreen:function(t){t.requestFullscreen?t.requestFullscreen():t.webkitRequestFullscreen?t.webkitRequestFullscreen():t.mozRequestFullScreen?t.mozRequestFullScreen():t.msRequestFullscreen&&t.msRequestFullscreen()},exitFullScreen:function(){document.exitFullscreen?document.exitFullscreen():document.webkitCancelFullScreen?document.webkitCancelFullScreen():document.mozCancelFullScreen?document.mozCancelFullScreen():document.msExitFullscreen&&document.msExitFullscreen()},initialize:function(){s.call(this),this.options.fullscreen&&!this.getFullScreenElement()&&this.requestFullScreen(this.container[0])},close:function(){this.getFullScreenElement()===this.container[0]&&this.exitFullScreen(),n.call(this)}}),e}),function(t){"use strict";"function"==typeof define&&define.amd?define(["./blueimp-helper","./blueimp-gallery"],t):t(window.blueimp.helper||window.jQuery,window.blueimp.Gallery)}(function(t,e){"use strict";var i=e.prototype;t.extend(i.options,{videoContentClass:"video-content",videoLoadingClass:"video-loading",videoPlayingClass:"video-playing",videoIframeClass:"video-iframe",videoCoverClass:"video-cover",videoPlayClass:"video-play",videoControlClass:"video-control",videoAutostart:!1,videoPlaysInline:!0,videoPreloadProperty:"preload",videoPosterProperty:"poster"});var s=i.handleSlide;return t.extend(i,{handleSlide:function(e,i){s.call(this,e,i),this.setTimeout(function(){this.activeVideo&&this.activeVideo.pause(),(this.options.videoAutostart||e===i)&&t(this.slides[i]).find("."+this.options.videoContentClass).trigger("selected")})},videoFactory:function(e,i,s){var n,o,a,l=this,r=this.options,h=this.elementPrototype.cloneNode(!1),d=t(h),c=[{type:"error",target:h}],u=s||document.createElement("video"),p=this.elementPrototype.cloneNode(!1),m=document.createElement("a"),y=this.getItemProperty(e,r.urlProperty),f=this.getItemProperty(e,r.sourcesProperty),v=this.getItemProperty(e,r.titleProperty),g=this.getItemProperty(e,r.videoPosterProperty),C=[m];if(d.addClass(r.videoContentClass),t(m).addClass(r.videoPlayClass),t(p).addClass(r.videoCoverClass).hasClass(r.toggleClass)||C.push(p),p.draggable=!1,v&&(h.title=v,m.setAttribute("aria-label",v)),g&&(p.style.backgroundImage='url("'+g+'")'),u.setAttribute?r.videoPlaysInline&&u.setAttribute("playsinline",""):d.addClass(r.videoIframeClass),u.preload=this.getItemProperty(e,r.videoPreloadProperty)||"none",this.support.source&&f)for(a=0;a<f.length;a+=1)u.appendChild(t.extend(this.sourcePrototype.cloneNode(!1),f[a]));return y&&(u.src=y),m.href=y||f&&f.length&&f[0].src,u.play&&u.pause&&((s||t(u)).on("error",function(){l.setTimeout(i,c)}).on("pause",function(){u.seeking||(o=!1,d.removeClass(l.options.videoLoadingClass).removeClass(l.options.videoPlayingClass),n&&l.container.addClass(l.options.controlsClass),u.controls=!1,u===l.activeVideo&&delete l.activeVideo,l.interval&&l.play())}).on("playing",function(){o=!1,p.removeAttribute("style"),d.removeClass(l.options.videoLoadingClass).addClass(l.options.videoPlayingClass)}).on("play",function(){window.clearTimeout(l.timeout),o=!0,d.addClass(l.options.videoLoadingClass),l.container.hasClass(l.options.controlsClass)?(n=!0,l.container.removeClass(l.options.controlsClass)):n=!1,u.controls=!0,l.activeVideo=u}),t(d).on("click",function(t){-1===(""+(t.target||t.srcElement).classList).indexOf(l.options.videoControlClass)&&(l.preventDefault(t),o||u===l.activeVideo?u.pause():u.play())}),h.appendChild(s&&s.element||u)),u.playOnReady&&t(h).on("selected",function(t){u.hasPlayed||u.play()}),h.appendChild(p),h.appendChild(m),this.setTimeout(i,[{type:"load",target:h}]),h}}),e}),function(t){"use strict";"function"==typeof define&&define.amd?define(["./blueimp-helper","./blueimp-gallery-video"],t):t(window.blueimp.helper||window.jQuery,window.blueimp.Gallery)}(function(t,e){"use strict";if(!window.postMessage)return e;var i=e.prototype;t.extend(i.options,{youTubeVideoIdProperty:"youtube",youTubeContentClass:"youtube-content",youTubeWrapClass:"youtube-wrap",youTubePlayerVars:{color:"white",wmode:"transparent",listType:"playlist",modestbranding:1,playsinline:1,controls:0,loop:1,rel:0},youTubeClickToPlay:!1,playOnReady:!0});var s=i.textFactory||i.imageFactory,n=function(e,i){this.options=t.extend({},i),this.videoId=e,this.playerVars=t.extend({playlist:e},i.youTubePlayerVars),this.playOnReady=i.playOnReady,this.clickToPlay=i.youTubeClickToPlay,this.element=document.createElement("div"),this.listeners={},this.retryCount=0};t.extend(n.prototype,{on:function(t,e){return this.listeners[t]=e,this},loadAPI:function(){var t,e=this,i=window.onYouTubeIframeAPIReady,s="https://www.youtube.com/iframe_api",n=document.getElementsByTagName("script"),o=n.length;for(window.onYouTubeIframeAPIReady=function(){i&&i.apply(this),e.playOnReady&&e.play()};o;)if(n[o-=1].src===s)return;(t=document.createElement("script")).src=s,n[0].parentNode.insertBefore(t,n[0])},onReady:function(){var t=this;this.ready=!0,this.videoNav.min=0,this.videoNav.max=this.player.getDuration(),this.videoNav.addEventListener("change",function(e){clearInterval(t.tick),t.tick=setInterval(t.onTick,1e3,t),t.player.seekTo(e.target.value)}),this.tick=setInterval(this.onTick,1e3,this),this.playOnReady&&this.play()},onTick:function(t){t.videoNav.value=t.player.getCurrentTime()},onPlaying:function(){this.playStatus<2&&(this.listeners.playing(),this.playStatus=2)},onPause:function(){this.listeners.pause(),delete this.playStatus},onStateChange:function(t){switch(window.clearTimeout(this.pauseTimeout),t.data){case YT.PlayerState.PLAYING:this.hasPlayed=!0,this.onPlaying();break;case YT.PlayerState.UNSTARTED:case YT.PlayerState.PAUSED:this.pauseTimeout=i.setTimeout.call(this,this.onPause,null,500);break;case YT.PlayerState.ENDED:this.onPause()}},onError:function(t){this.listeners.error(t)},play:function(){var t=this;if(this.playStatus||(this.listeners.play(),this.playStatus=1),this.ready)!this.hasPlayed&&(this.clickToPlay||window.navigator&&/iP(hone|od|ad)/.test(window.navigator.platform))?this.onPlaying():this.player.playVideo();else if(this.playOnReady=!0,window.YT&&YT.Player){if(!this.player){var e=document.createElement("div"),i=this.element.parentNode;e.appendChild(this.element),e.className=this.options.youTubeWrapClass,i.insertBefore(e,i.firstChild),this.player=new YT.Player(this.element,{videoId:this.videoId,playerVars:this.playerVars,events:{onReady:function(){setTimeout(function(){t.onReady()},300)},onStateChange:function(e){t.onStateChange(e)},onError:function(e){t.retryCount<3?(t.retryCount++,t.player.loadVideoById(t.videoId)):t.onError(e)}}}),this.videoNav=document.createElement("input"),this.videoNav.classList.add(this.options.videoControlClass),this.videoNav.type="range",this.videoNav.value=0,this.videoNav.draggable=!1,i.insertBefore(this.videoNav,i.firstChild.nextSibling)}}else this.loadAPI()},pause:function(){this.ready?this.player.pauseVideo():this.playStatus&&(delete this.playOnReady,this.listeners.pause(),delete this.playStatus)}});var o=i.handleClick,a=i.onmousedown;return t.extend(i,{YouTubePlayer:n,handleClick:function(t){if(-1===(""+(t.target||t.srcElement).classList).indexOf(this.options.videoControlClass))return o.call(this,t)},onmousedown:function(t){if(-1===(""+(t.target||t.srcElement).classList).indexOf(this.options.videoControlClass))return a.call(this,t)},textFactory:function(e,i){var o=this.options,a=this.getItemProperty(e,o.youTubeVideoIdProperty);if(a){void 0===this.getItemProperty(e,o.urlProperty)&&(e[o.urlProperty]="https://www.youtube.com/watch?v="+a),void 0===this.getItemProperty(e,o.videoPosterProperty)&&(e[o.videoPosterProperty]="https://img.youtube.com/vi/"+a+"/maxresdefault.jpg");var l=this.videoFactory(e,i,new n(a,o));l.classList.add(o.youTubeContentClass);var r=new Image;return r.onload=function(i){if(90==r.height){var s="https://img.youtube.com/vi/"+a+"/0.jpg";t(e).attr(o.videoPosterProperty,s),t(l).children(".video-cover").css("background-image","url("+s+")")}r=null},r.src=e[o.videoPosterProperty],l}return s.call(this,e,i)}}),e}),function(t){"use strict";"function"==typeof define&&define.amd?define(["./blueimp-helper","./blueimp-gallery-video"],t):t(window.blueimp.helper||window.jQuery,window.blueimp.Gallery)}(function(t,e){"use strict";if(!window.postMessage)return e;var i=e.prototype;t.extend(i.options,{vimeoVideoIdProperty:"vimeo",vimeoPlayerUrl:"https://player.vimeo.com/video/VIDEO_ID?api=1&player_id=PLAYER_ID",vimeoPlayerIdPrefix:"vimeo-player-",vimeoClickToPlay:!1});var s=i.textFactory||i.imageFactory,n=function(t,e,i,s){this.url=t,this.videoId=e,this.playerId=i,this.clickToPlay=s,this.element=document.createElement("div"),this.listeners={}},o=0;return t.extend(n.prototype,{on:function(t,e){return this.listeners[t]=e,this},loadAPI:function(){var e,i,s=this,n="https://f.vimeocdn.com/js/froogaloop2.min.js",o=document.getElementsByTagName("script"),a=o.length;function l(){!i&&s.playOnReady&&s.play(),i=!0}for(;a;)if(o[a-=1].src===n){e=o[a];break}e||((e=document.createElement("script")).src=n),t(e).on("load",l),o[0].parentNode.insertBefore(e,o[0]),/loaded|complete/.test(e.readyState)&&l()},onReady:function(){var t=this;this.ready=!0,this.player.addEvent("play",function(){t.hasPlayed=!0,t.onPlaying()}),this.player.addEvent("pause",function(){t.onPause()}),this.player.addEvent("finish",function(){t.onPause()}),this.playOnReady&&this.play()},onPlaying:function(){this.playStatus<2&&(this.listeners.playing(),this.playStatus=2)},onPause:function(){this.listeners.pause(),delete this.playStatus},insertIframe:function(){var t=document.createElement("iframe");t.src=this.url.replace("VIDEO_ID",this.videoId).replace("PLAYER_ID",this.playerId),t.id=this.playerId,t.allow="autoplay",this.element.parentNode.replaceChild(t,this.element),this.element=t},play:function(){var t=this;this.playStatus||(this.listeners.play(),this.playStatus=1),this.ready?!this.hasPlayed&&(this.clickToPlay||window.navigator&&/iP(hone|od|ad)/.test(window.navigator.platform))?this.onPlaying():this.player.api("play"):(this.playOnReady=!0,window.$f?this.player||(this.insertIframe(),this.player=$f(this.element),this.player.addEvent("ready",function(){t.onReady()})):this.loadAPI())},pause:function(){this.ready?this.player.api("pause"):this.playStatus&&(delete this.playOnReady,this.listeners.pause(),delete this.playStatus)}}),t.extend(i,{VimeoPlayer:n,textFactory:function(t,e){var i=this.options,a=this.getItemProperty(t,i.vimeoVideoIdProperty);return a?(void 0===this.getItemProperty(t,i.urlProperty)&&(t[i.urlProperty]="https://vimeo.com/"+a),o+=1,this.videoFactory(t,e,new n(i.vimeoPlayerUrl,a,i.vimeoPlayerIdPrefix+o,i.vimeoClickToPlay))):s.call(this,t,e)}}),e}),function(t){"use strict";"function"==typeof define&&define.amd?define(["jquery","./blueimp-gallery"],t):t(window.jQuery,window.blueimp.Gallery)}(function(t,e){"use strict";t(document).on("click","[data-gallery]",function(i){var s=t(this).data("gallery"),n=t(s),o=n.length&&n||t(e.prototype.options.container),a={onopen:function(){o.data("gallery",this).trigger("open")},onopened:function(){o.trigger("opened")},onslide:function(){o.trigger("slide",arguments)},onslideend:function(){o.trigger("slideend",arguments)},onslidecomplete:function(){o.trigger("slidecomplete",arguments)},onclose:function(){o.trigger("close")},onclosed:function(){o.trigger("closed").removeData("gallery")}},l=t.extend(o.data(),{container:o[0],index:this,event:i},a),r=t(this).closest("[data-gallery-group], body").find('[data-gallery="'+s+'"]');return l.filter&&(r=r.filter(l.filter)),new e(r,l)})});
    
    // ##################################################################################################
    //    Обработка форм
    // ##################################################################################################

    $('.modal').on('append', '#ajax-loader', function (e) {
        if ($(this).hasClass('static'))
            return;

        var $modal = $(e.delegateTarget), $modalDialog = $modal.children('.modal-dialog'),
            $modalContent = $modalDialog.children('.modal-content'),
            $loader = $(this), $loaderWrap = $loader.find('.wrap');

        if ($modalContent.outerHeight() < $(window).height())
            return;

        var modalHeight = $modalContent.outerHeight() / 2;
        var windowHeight = $(window).height() / 2;
        var loaderHeight = $loaderWrap.outerHeight() / 2;
        var offset = $modalDialog.css('margin-top').toNumber()
            + $modalContent.css('padding-top').toNumber();

        $modal.on('scroll.ajax.loader', function () {
            var modalScroll = $modal.scrollTop();

            var min = ((-modalHeight + windowHeight) - offset) - loaderHeight;
            var top = (min + modalScroll) + loaderHeight;
            var max = ((modalHeight - windowHeight) - offset) - loaderHeight;

            $loaderWrap.css('top', top.between(min, max) + 'px');
        });

        $(window).on('resize.ajax.loader', function () {
            modalHeight = $modalContent.outerHeight() / 2;
            windowHeight = $(window).height() / 2;
            loaderHeight = $loaderWrap.outerHeight() / 2;
            offset = $modalDialog.css('margin-top').toNumber()
                + $modalContent.css('padding-top').toNumber();

            $modal.trigger('scroll.ajax.loader');
        });

        $loader.one('remove', function () {
            $modal.off('scroll.ajax.loader');
            $modal.off('resize.ajax.loader');
        });

        $modal.trigger('scroll.ajax.loader');
    });

    function validateForm(form) {
        $(form).find('.invalid-feedback').remove();
        var invalid = $(form).find(":invalid");
        if (invalid.length) {
            var field = $(form).find(":invalid").get(0);

            var tab = $(field).closest('.tab-pane');
            if (tab.length && !$(tab).hasClass('show')) {
                var tabId = $(tab).attr('id');
                $('#' + tabId + '-tab').tab('show');
            }

            var accordion = $(field).closest('.accordion');
            if (accordion.length && !$(accordion).children('.collapse').hasClass('show')) {
                $(accordion).children('.collapse').collapse('show');
            }

            var quanity = $(field).closest('.input-group-quanity');
            if (quanity.length) {
                $(quanity).after('<div class="invalid-feedback">' + wp.i18n.__('Please, set quanity', 'sadovod-scripts') + '</div>');
                $(field).trigger("focus");
            } else {
                var type = $(field).attr("type");
                if (type != 'checkbox') {
                    if ($(field).is('select')) {
                        var appendAfter = $(field);
                        if ($(field).next('button').length) {
                            appendAfter = $(field).parent();
                        }
                        $(appendAfter).after('<div class="invalid-feedback">' + wp.i18n.__('Please, choose option', 'sadovod-scripts') + '</div>');
                    } else {
                        $(field).after('<div class="invalid-feedback">' + wp.i18n.__('Please, fill this field', 'sadovod-scripts') + '</div>');
                        $(field).trigger("focus");
                    }
                }
            }
            $(field).get(0).setCustomValidity('');
            return false;
        }
        return true;
    }

    $('form').on('click', '[type="submit"]', function (e) {
        var form = $(this).closest('form');
        if (!validateForm(form)) {
            e.preventDefault();
            return false;
        }
    });

    $('form').on('keyup keypress', function (e) {
        if (e.code === 'Enter') {
            e.preventDefault();
            return false;
        }
    });

    $(document).on('submit', 'form', async function (e) {
        e.preventDefault();

        if (!$(this).find('input[name="action"]').length)
            return false;

        if (typeof tinymce !== 'undefined')
            tinymce.triggerSave();

        var form = this,
            $form = $(form),
            formData = new FormData(),
            formParams = $form.serializeArray(),
            $modal = $form.closest('.modal-content'),
            $alert = $form.find('.alert');

        var loader = $form.attr('data-loader') ||
            wp.i18n.__('Sending form data.', 'sadovod-scripts') +
            '<br>' +
            wp.i18n.__('Please, wait...', 'sadovod-scripts');
        if ($modal.length) {
            createAjaxLoader($modal, loader);
        } else {
            createAjaxLoader($form, loader);
        }

        $.each($form.find('input[type="file"]'), function (i, tag) {
            $.each($(tag)[0].files, function (i, file) {
                formData.append(tag.name, file);
            });
        });

        $.each(formParams, function (i, val) {
            formData.append(val.name, val.value);
        });

        if ($form.hasClass('g-recaptcha')) {
            var action = $form.find('input[name="action"]').val(),
                token = await grecaptcha.execute(scripts.captcha, { action: action });

            formData.append('recaptcha', token);
        }

        var request = $.ajax({
            url: ajax.url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            start: function () {
                var timeout = setTimeout(function () {
                    $('a class="btn-link"').addClass('cancel-request').appendTo('#ajax-loader').text(
                        wp.i18n.__('Cancel request', 'sadovod-scripts')
                    ).one('click', function () {
                        request.abort();
                        $('#ajax-loader').remove();
                    });
                }, 2000);

                $('#ajax-loader').one('remove', function () {
                    clearTimeout(timeout);
                })
            },
            success: function (data) {
                $('#ajax-loader').remove();
                $form.find('.invalid-feedback').remove();
                $alert.fadeOut();
                var result = JSON.parse(data);
                if (!result)
                    return;

                if (result.error) {
                    if (result.error.note) {
                        $alert.find('.alert-content').html(result.error.text);
                        $alert.fadeIn();
                    } else {
                        $form.find('input[name="' + result.error.field + '"]').after('<div class="invalid-feedback">' + result.error.text + '</div>');
                    }

                    if (result.error.close) {
                        setTimeout(function (e) { $form.closest('.modal.show').modal('hide'); }, 2000);
                    }

                    if ($form.find('.g-recaptcha').length)
                        grecaptcha.reset($form.find('.g-recaptcha').data('id'));
                } else if (result.success) {
                    if (result.success.note) {
                        $form.closest('.modal.show').modal('hide');

                        $alert.find('.alert-content').html(result.success.text);
                        $alert.removeClass('alert-warning').addClass('alert-success').fadeIn();

                        setTimeout(function (e) {
                            $alert.fadeOut();
                            setTimeout(function (e) { $alert.addClass('alert-warning'); }, 400);
                        }, 1500);
                    } else {
                        if (result.success.text) {
                            $('#resultModal .success-content').html(result.success.text);
                            if ($form.closest('.modal.show').length) {
                                $form.closest('.modal.show').modal('hide');
                                setTimeout(function (e) { $('#resultModal').modal('show'); }, 400);
                                if (!result.success.noclose) {
                                    setTimeout(function (e) { $('#resultModal').modal('hide'); }, 1900);
                                }
                            } else {
                                $("#resultModal").modal('show');
                                if (!result.success.noclose) {
                                    setTimeout(function (e) { $('#resultModal').modal('hide'); }, 1500);
                                }
                            }
                        }
                    }

                    if (result.success.captcha) {
                        grecaptcha.reset(result.success.captcha);
                    }
                    if (result.success.reload) {
                        setTimeout(function (e) { location.reload(); }, 1500);
                    }
                    if (result.success.redirect) {
                        setTimeout(function (e) { location.href = result.success.redirect; }, 1500);
                    }
                }
                $(document).trigger("formsubmit", [form, result]);
            },
            error: function (data) {
                $('#ajax-loader').addClass('error').find('.message').html($form.attr('data-error') ||
                    wp.i18n.__('Some error happened.', 'sadovod-scripts') +
                    '<br>' +
                    wp.i18n.__('Please, reload the page!', 'sadovod-scripts')
                );
            }
        });

        return true;
    });

    $(document).on('hidden.bs.modal', '.modal', function () {
        $(this).find('.invalid-feedback').remove();
        $(this).find('.alert').fadeOut();
    });

    function createAjaxLoader(element, content, pinned = false) {
        return $(
            '<div id="ajax-loader" class="' + (pinned ? 'static' : '') + '">' +
            /**/'<div class="wrap">' +
            /**//**/'<span class="message">' +
            /**//**//**/content +
            /**//**/'</span>' +
            /**//**/'<i class="loader"></i>' +
            /**/'</div>' +
            '</div>'
        ).appendTo($(element));
    }

    window.createAjaxLoader = createAjaxLoader;

    // ##################################################################################################
    //    Загрузчик
    // ##################################################################################################

    $('.form-attachments-list').tooltip({
        selector: '.attachment-content',
        title: function () {
            return $(this).attr('title');
        }
    });

    $(document).on('dragover', '.upload-area', function (event) {
        event.preventDefault();
    });

    $(document).on('drop', '.upload-area', function (event) {
        event.preventDefault();

        const files = event.originalEvent.dataTransfer.files;
        processFiles(this, files);
    });

    $(document).on('change', '.file-uploader', function (event) {
        processFiles(this, event.target.files);
    });

    //TODO: Async loading of files with showing loader gif
    function processFiles(input, files) {
        var $uploader = $(input).closest('.form-file-upload'),
            $uploadArea = $uploader.find('.upload-area'),
            $fileList = $uploader.find('.form-attachments-list');

        var fileLen = $uploader.find('.form-attachment-wrap').length;
        if (fileLen > uploads.file_limit) {
            $uploadArea.hide();

            var fileNote = wp.i18n.__('Maximum file count exceeded. Allowed %d.', 'sadovod-scripts');
            createAlert('alert-file-limit', $uploadArea,
                wp.i18n.sprintf(fileNote,
                    uploads.file_limit
                ),
                'warning', false
            );
            return;
        }

        var userFileLen = fileLen + files.length,
            processLimit = files.length;
        if (userFileLen > uploads.file_limit) {
            $uploadArea.hide();

            processLimit = processLimit - (userFileLen - uploads.file_limit);

            var fileNote = wp.i18n.__('Maximum file count exceeded. Allowed %d. Uploaded only %d. Rest files will be skipped.', 'sadovod-scripts');
            createAlert('alert-user-file-limit', $uploadArea,
                wp.i18n.sprintf(fileNote,
                    uploads.file_limit,
                    processLimit
                ),
                'warning', false
            );
        }

        var sizeNote = wp.i18n.__('Maximum file size for %s %s file exceeded. Allowed %dMB, got %dMB.', 'sadovod-scripts');
        $.each(files, function (index, file) {
            if (index >= processLimit)
                return;

            if ($fileList.find('[data-file="' + file.name + '"]').length)
                return;

            var fileSize = file.size / 1024 / 1024;

            var type = 'file',
                preview = '<i class="i-document"></i>',
                previewLink = '',
                additionalData = '';

            if (file.type.match(/image.*/)) {
                if (fileSize > uploads.limits.image) {
                    createAlert('alert-' + file.name, $uploadArea,
                        wp.i18n.sprintf(sizeNote,
                            wp.i18n.__('image', 'sadovod-scripts'), file.name, uploads.limits.image, fileSize
                        ),
                        'warning', false
                    );
                    return;
                }

                var imageURL = BlobStorage.createObjectURL(file);

                type = 'image';
                preview = '<i class="i-image"></i>';
                preview += '<img alt="' + file.name + '" data-src="' + imageURL + '" title="' + file.name + '" class="lazyload">';
                previewLink = imageURL;

            } else if (file.type.match(/audio.*/)) {
                if (fileSize > uploads.limits.audio) {
                    createAlert('alert-' + file.name, $uploadArea,
                        wp.i18n.sprintf(sizeNote,
                            wp.i18n.__('audio', 'sadovod-scripts'), file.name, uploads.limits.audio, fileSize
                        ),
                        'warning', false
                    );
                    return;
                }

                var audioURL = BlobStorage.createObjectURL(file);
                generateAudioPreview($fileList, file, audioURL);

                type = 'audio';
                preview = '<i class="i-volume-up"></i>';
                preview += '<img alt="' + file.name + '" data-src="" title="' + file.name + '" class="lazyloading">';
                previewLink = audioURL;
                additionalData = 'data-poster=""';

            } else if (file.type.match(/video.*/)) {
                if (fileSize > uploads.limits.video) {
                    createAlert('alert-' + file.name, $uploadArea,
                        wp.i18n.sprintf(sizeNote,
                            wp.i18n.__('video', 'sadovod-scripts'), file.name, uploads.limits.video, fileSize
                        ),
                        'warning', false
                    );
                    return;
                }

                var videoURL = BlobStorage.createObjectURL(file);
                generateVideoPreview($fileList, file, videoURL);

                type = 'video';
                preview = '<i class="i-video"></i>';
                preview += '<img alt="' + file.name + '" data-src="" title="' + file.name + '" class="lazyloading">';
                previewLink = videoURL;
                additionalData = 'data-poster=""';

            } else {
                if (fileSize > uploads.limits.document) {
                    createAlert('alert-' + file.name, $uploadArea,
                        wp.i18n.sprintf(sizeNote,
                            wp.i18n.__('document', 'sadovod-scripts'), file.name, uploads.limits.document, fileSize
                        ),
                        'warning', false
                    );
                    return;
                }

                if (file.type === 'application/pdf') {
                    var pdfURL = BlobStorage.createObjectURL(file);
                    generatePDFPreview($fileList, file, pdfURL);

                    type = 'pdf';
                    preview += '<img alt="' + file.name + '" data-src="" title="' + file.name + '" class="lazyloading">';
                    previewLink = pdfURL;
                    additionalData = 'data-poster=""';

                } else if (file.type.match(/text.*/)) {
                    var fileURL = BlobStorage.createObjectURL(file);

                    type = 'text';
                    previewLink = fileURL;
                }
            }

            var $attachment = $(
                '<div class="form-attachment-wrap col" data-file="' + file.name + '">' +
                /**/'<input type="file" name="files[]" multiple hidden>' +
                /**/'<div class="form-attachment">' +
                /**//**/'<div class="attachment-actions">' +
                /**//**//**/'<i class="i-edit"></i>' +
                /**//**//**/'<i class="i-close"></i>' +
                /**//**/'</div>' +
                /**//**/'<a class="attachment-content attachment-content-' + type + '"' +
                /**//**//**/' title="' + file.name + '"' +
                /**//**//**/' href="' + previewLink + '"' +
                /**//**//**/' data-type="' + type + '"' +
                /**//**//**/' data-gallery="upload" ' +
                /**//**//**/' data-title="' + file.name + '"' +
                /**//**//**/' ' + additionalData + '> ' + preview + '</div>' +
                /**//**/'</a>' +
                /**/'</div>' +
                '</div>'
            ).appendTo($fileList);

            $attachment.find('input').prop('files', files);
            $('.form-attachments-list').trigger('fileAttached', file.name, $attachment)
        });
    }

    function generateVideoPreview(fileList, file, url) {
        var $video = $("<video>").prop('autoplay', true).prop('muted', true).attr('src', url); //TODO: Need to cleanup on form submit

        $video.one('loadeddata', function () {
            var $canvas = $("<canvas>"), videoWidth = $video.prop('videoWidth'), videoHeight = $video.prop('videoHeight'),
                context = $canvas.attr('width', videoWidth).attr('height', videoHeight).get(0).getContext("2d");

            context.drawImage($video.get(0), 0, 0, videoWidth, videoHeight);
            $video.trigger('pause');

            $canvas.get(0).toBlob(function (blob) {
                var thumbnail = BlobStorage.createObjectURL(blob);
                fileList.find('[data-file="' + file.name + '"] a')
                    .attr('data-poster', thumbnail)
                    .find('img').attr('data-src', thumbnail).toggleClass('lazyload lazyloading');

                $canvas.remove();
                $video.remove();
            });
        });
    }

    window.AudioContext = (window.AudioContext ||
        window.webkitAudioContext ||
        window.mozAudioContext ||
        window.oAudioContext ||
        window.msAudioContext);

    var audioLineCount = 100;
    function generateAudioPreview(fileList, file, url) {
        var audioContext = new AudioContext(),
            reader = new FileReader();

        $(reader).one('load', function () {
            var $canvas = $("<canvas>"), audioWidth = 300, audioHeight = 300,
                context = $canvas.attr('width', audioWidth).attr('height', audioHeight).get(0).getContext("2d");

            audioContext.decodeAudioData(reader.result, function (buffer) {
                var leftChannel = buffer.getChannelData(0),
                    totallength = leftChannel.length,
                    eachBlock = Math.floor(totallength / audioLineCount),
                    lineGap = (audioWidth / audioLineCount);

                context.save();

                context.lineWidth = 1;
                context.strokeStyle = '#46a0ba';
                context.globalCompositeOperation = 'lighter';
                context.translate(0, audioHeight / 2);

                context.beginPath();
                for (var i = 0; i <= audioLineCount; i++) {
                    var audioBuffKey = Math.floor(eachBlock * i);
                    var x = i * lineGap;
                    var y = leftChannel[audioBuffKey] * audioHeight / 2;
                    context.moveTo(x, y);
                    context.lineTo(x, (y * -1));
                }
                context.stroke();
                context.restore();

                audioContext.close();

                $canvas.get(0).toBlob(function (blob) {
                    var thumbnail = BlobStorage.createObjectURL(blob);
                    fileList.find('[data-file="' + file.name + '"] a')
                        .attr('data-poster', thumbnail)
                        .find('img').attr('data-src', thumbnail).toggleClass('lazyload lazyloading');

                    $canvas.remove();
                });
            });
        });

        reader.readAsArrayBuffer(file)
    }

    function generatePDFPreview(fileList, file, url) {
        var reader = new FileReader();

        $(reader).one('load', function () {
            var data = new Uint8Array(reader.result);

            var loadingTask = pdfjsLib.getDocument(data);
            loadingTask.promise.then(function (pdfDocument) {
                pdfDocument.getPage(1).then(function (page) {
                    var previewWidth = 800, previewHeight = 800, viewport = page.getViewport({ scale: 1 }),
                        scaleX = previewWidth / viewport.width, scaleY = previewHeight / viewport.height,
                        scale = Math.min(scaleX, scaleY), outputScale = window.devicePixelRatio || 1,
                        realViewport = page.getViewport({ scale: scale });

                    var $canvas = $("<canvas>"), context = $canvas.attr('width', previewWidth * outputScale)
                        .attr('height', previewHeight * outputScale).get(0).getContext("2d");

                    var offsetX = scale == scaleY ? (previewWidth - realViewport.width) / 2 : 0,
                        offsetY = scale == scaleX ? (previewHeight - realViewport.height) / 2 : 0;

                    var transform = outputScale !== 1
                        ? [outputScale, 0, 0, outputScale, offsetX * outputScale, offsetY * outputScale]
                        : null;

                    var renderContext = {
                        canvasContext: context,
                        transform: transform,
                        viewport: realViewport,
                        background: $.cookie.get('dt') === '1' ? 'rgba(255,255,255,0.5)' : 'rgba(0,0,0,0)'
                    };

                    var renderingTask = page.render(renderContext);
                    renderingTask.promise.then(function () {
                        $canvas.get(0).toBlob(function (blob) {
                            var thumbnail = BlobStorage.createObjectURL(blob);
                            fileList.find('[data-file="' + file.name + '"] a')
                                .attr('data-poster', thumbnail)
                                .find('img').attr('data-src', thumbnail).toggleClass('lazyload lazyloading');

                            $canvas.remove();
                        });

                        page.cleanup();

                        pdfDocument.cleanup();
                        pdfDocument.destroy();
                    });
                });
            });
        });

        reader.readAsArrayBuffer(file)
    }

    $('.form-attachments-list').on('click', '.i-close', function (e) {
        var $uploader = $(this).closest('.form-file-upload'),
            $uploadArea = $uploader.find('.upload-area');

        $(this).closest('.form-attachment-wrap').remove();
        $uploadArea.show();
    });

    // ##################################################################################################
    //    Утлиты
    // ##################################################################################################

    function delay(callback, ms) {
        var timer = 0;
        return function () {
            var context = this, args = arguments;
            clearTimeout(timer);
            timer = setTimeout(function () {
                callback.apply(context, args);
            }, ms || 0);
        };
    }

    function sanitizeID(s) {
        return ('' + s)
            .replace(/([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g, '');
    }

    function mimeType(headerString) {
        switch (headerString) {
            case "89504e47":
                type = "image/png";
                break;
            case "47494638":
                type = "image/gif";
                break;
            case "ffd8ffe0":
            case "ffd8ffe1":
            case "ffd8ffe2":
                type = "image/jpeg";
                break;
            default:
                type = "unknown";
                break;
        }
        return type;
    }

    // ##################################################################################################
    //    jQuery
    // ##################################################################################################

    $.fn.isInViewport = function () {
        if (!$(this).offset()) return false;

        var elementTop = $(this).offset().top;
        var elementBottom = elementTop + $(this).outerHeight();

        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();

        return elementBottom > viewportTop && elementTop < viewportBottom;
    };

    $.fn.slideUpAndRemove = function (speed = 400) {
        $(this).slideUp(speed, function () {
            $(this).remove();
        })
    }

    $.fn.fadeOutAndRemove = function (speed = 400) {
        $(this).fadeOut(speed, function () {
            $(this).remove();
        })
    }

    $.fn.hasClasses = function (selectors) {
        var self = this;
        for (var i in selectors) {
            if ($(self).hasClass(selectors[i]))
                return true;
        }
        return false;
    }

    $.fn.hasParents = function (selectors) {
        var self = this;
        for (var i in selectors) {
            if ($(self).parent(selectors[i]).length)
                return true;
        }
        return false;
    }

    // appendTo calling append
    var _append = $.fn.append;
    $.fn.append = function (elements) {
        var elements = $(elements);
        var targets = _append.apply(this, arguments);

        var appendEvent = $.Event('append');
        $.each(targets, function (i, target) {
            var $target = $(target);

            $.each(elements, function (i, el) {
                var $el = $target.find(el);

                appendEvent.relatedTarget = $target;
                $el.trigger(appendEvent);
            });
        });

        return targets;
    };

    $.fn.generateID = function (prefix = 'id_') {
        var id = 0;

        prefix = prefix.trim().toLowerCase().replace(/[^a-z0-9_]+/g, ' ').replace(/\s+/g, '-');

        this.toString = function () {
            return prefix + id++;
        };
    };

    // ##################################################################################################
    //    Блобы
    // ##################################################################################################

    class BlobStorage {
        static createObjectURL(blob) {
            const url = URL.createObjectURL(blob);
            BlobStorage.store = { ...(BlobStorage.store || {}), [url]: blob };
            return url;
        }

        static getFromObjectURL(url) {
            return (BlobStorage.store || {})[url] || null;
        }

        static revokeObjectURL(url) {
            URL.revokeObjectURL(url);
            if (
                new URL(url).protocol === "blob:" &&
                BlobStorage.store &&
                url in BlobStorage.store
            )
                delete BlobStorage.store[url];
        }

        static revokeAllObjectsURL() {
            for (var url in dict) {
                BlobStorage.revokeObjectURL(url);
            }
        }
    }

    if (!HTMLCanvasElement.prototype.toBlob) {
        Object.defineProperty(HTMLCanvasElement.prototype, 'toBlob', {
            value: function (callback, type, quality) {
                var dataURL = this.toDataURL(type, quality).split(',')[1];
                setTimeout(function () {

                    var binStr = atob(dataURL),
                        len = binStr.length,
                        arr = new Uint8Array(len);

                    for (var i = 0; i < len; i++) {
                        arr[i] = binStr.charCodeAt(i);
                    }

                    callback(new Blob([arr], { type: type || 'image/png' }));

                });
            }
        });
    }

    // ##################################################################################################
    //    Расширения
    // ##################################################################################################

    Object.defineProperty(String.prototype, 'capitalize', {
        value: function () {
            return this.charAt(0).toUpperCase() + this.slice(1);
        },
        enumerable: false
    });

    String.prototype.repeatN = function (times) {
        var result = '', pattern = this.valueOf();
        while (times) {
            if (times & 1) result += pattern;
            times >>= 1, pattern += pattern;
        }
        return result + pattern;
    };

    String.prototype.toNumber = function () {
        var val = this.valueOf();
        return val.match(/\d*\.\d*/) || 0;
    };

    Number.prototype.between = function (min, max) {
        var val = this.valueOf();

        if (isNaN(val) || isNaN(min) || isNaN(max))
            return val;

        return Math.min(Math.max(val, min), max)
    }

    window.requestAnimationFrame = function () {
        return (
            window.requestAnimationFrame ||
            window.webkitRequestAnimationFrame ||
            window.mozRequestAnimationFrame ||
            window.oRequestAnimationFrame ||
            window.msRequestAnimationFrame ||
            function (/* function */ callback) {
                window.setTimeout(callback, 1000 / 60);
            }
        );
    }();
});
