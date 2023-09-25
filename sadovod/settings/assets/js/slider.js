/**
 * Slider
 */
jQuery(function ($) {
    var frame;

    function change_slider(elem) {
        var arr = [];
        var el = elem.children('.slides-box');
        var fields = jQuery.map(el.children('.slide-box').children('.slide-content'), function (e) {
            var _this = jQuery(e).children('.slide-info');
            arr.push({
                img: jQuery(e).children('.slide-img').find('img').attr('data-id'),
                ico: _this.children('.slide-ico').find('input').val(),
                title: _this.find('.slide-title').find('input').val(),
                url: _this.find('.slide-link').find('input').val(),
                text: _this.find('.slide-text').find('textarea').val(),
                subtext: _this.find('.slide-subtext').find('textarea').val(),
                type: _this.find('.slide-type').find('select').val(),
                check: _this.find('.slide-check').find('input').val()
            });

        }).join(',');
        arr = JSON.stringify(arr);
        elem.find('label #slider-arr').val(arr).change();
        elem.find('label #goog').val(arr).change();
    }

    jQuery('body').on('click', '.slide-img img', function (ev) {
        ev.preventDefault();

        var $button = $(this);

        if (frame) {
            frame.close();
        }

        // задаем media frame
        frame = wp.media.frames.questImgAdd = wp.media({
            states: [
                new wp.media.controller.Library({
                    title: wp.i18n.__('Slider Image', 'sadovod-scripts'),
                    library: wp.media.query({ type: 'image' }),
                    multiple: false
                })
            ],
            button: {
                text: wp.i18n.__('Set slider image', 'sadovod-scripts') // Set the text of the button.
            }
        });

        // выбор
        frame.on('select', function () {
            var selected = frame.state().get('selection').first().toJSON();
            if (selected) {

                $button.attr('src', selected.url);
                $button.attr('data-id', selected.id);

                var elem = $button.parents('.slider-arr');
                change_slider(elem);
            }
        });

        // открываем
        frame.on('open', function () {
            if ($button.attr('data-id')) frame.state().get('selection').add(wp.media.attachment($button.attr('data-id')));
        });

        frame.open();

        return false;
    });

    /*
     * удаляем значение произвольного поля
     * если быть точным, то мы просто удаляем value у input type="hidden"
     */
    $('.remove_image_button').on('click', function () {
        var src = $(this).parent().prev().attr('data-src');
        $(this).parent().prev().attr('src', src);
        $(this).prev().prev().val('');
        return false;
    });

    jQuery('body').on('click', '.slide-expand', function () {
        $(this).siblings('.slide-content').toggleClass('active');
        $(this).children('span').eq(1).toggleClass('dashicons-arrow-down dashicons-arrow-up');
    });

    jQuery('body').on('click', '.slide-adding', function () {
        var elem = $(this).parents('.slider-arr');
        var el = elem.find('.slides-box').children('.slide-default:eq(0)');
        var cl = elem.find('.slides-box');
        var cre = el.clone().appendTo(cl);
        cre.removeClass('slide-default').addClass('slide-box');
        change_slider(elem);
    });

    jQuery('body').on('click', '.slide-remove', function () {
        var elem = $(this).parents('.slider-arr');
        var box = $(this).parents('.slide-box');
        box.remove();
        change_slider(elem);
    });

    //-------------------------------------------------------

    jQuery('body').on('keyup paste', '.slide-box .slide-title input[type="text"]', function () {
        var str = $(this).val();
        $(this).parents('.slide-box').children('.slide-expand').children('.slide-name').html(str);
    });

    jQuery('body').on('change', '.slide-box .slide-title input[type="text"]', function () {
        var elem = jQuery(this).parents('.slider-arr');
        change_slider(elem);
    });

    jQuery('body').on('change', '.slide-box input', function () {
        var elem = jQuery(this).parents('.slider-arr');
        change_slider(elem);
    });

    jQuery('body').on('change', '.slide-box .slide-ico input', function () {
        var iconClass = $(this).val();
        if (iconClass)
            $(this).prev('i').attr('class', 'i-' + iconClass);
        else
            $(this).prev('i').attr('class', '');
    });

    jQuery('body').on('change', '.slide-box textarea', function () {
        var elem = jQuery(this).parents('.slider-arr');
        change_slider(elem);
    });
});