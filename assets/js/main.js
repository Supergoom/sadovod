jQuery(function ($) {
    $(function() {
        let carrentStep = 'start';

        //текст hover в навигации
        $('[data-step-next]').mouseenter(function() {   
            $('.district__hover').html($(this).attr('data-title'));
            $('.district__hover').show();
        });
        $('[data-step-next]').mouseleave(function() {
            $('.district__hover').hide();
        });

        //клик по участку в карте
        $('[data-step-next]').on('click', function() {
            let thisNextStep = $(this).data('step-next');
            let isActive = $(this).data('active');

            //скрываем тултип
            $('.cnt-tooltip').hide()

            //отмена для disabled
            if( isActive == 'disabled' ) return;

            //добавить в навигацию
            $('.district__nav').append(`<span data-nav-step="${carrentStep}">` + $(this).attr('data-title') + '</span>');

            //обновить кнопку назад
            $('.close-district').attr('data-prewiev', carrentStep);

            
            //условия на кнопку нажатия муниципалитета
            if( $(this).data('step') == 'municipalities' ) {
                $('[data-step="municipalities-list"]').attr('data-step-id', thisNextStep);
                $('[data-step="municipalities-list"]').attr( 'data-step-preview', $(this).closest('.map-step').data('step-id') )

                $.ajax({
                    url: '/wp-admin/admin-ajax.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'municipalities_list',
                        municipality: $(this).data('step-next')
                    },
                    beforeSend: function( xhr ) {
                        console.log('beforeSend');
                    },
                    success: function( data ) {
                        $('.map-step__last-text-row').html( data.html );
                    }
                });
            }

            //меняем карту
            $(`[data-step-id="${carrentStep}"]`).hide();
            $(`[data-step-id="${thisNextStep}"]`).fadeIn(200);

            //обновить статус
            $('.rf-map').attr('data-step', thisNextStep);
            $('.rf-map').attr('data-prewiev', carrentStep);
            carrentStep = thisNextStep;
            
        });

        //копка возврата карты
        $('.close-district').on('click', function() {
            let prewiev = $('.map-step:visible').data('step-preview');
               
            $('[data-step-id]').hide();
            $(`[data-step-id="${prewiev}"]`).fadeIn(200);

            //удалить последни el из навигации
            $('.district__nav span:last').remove()

             //обновить статус
             $('.rf-map').attr('data-step', prewiev );
             carrentStep = prewiev;
        });

        //два раена подсвечиваем
        $('.nahim').hover(
            function() {
                $('.nahim').addClass('active')
              }, function() {
                $('.nahim').removeClass('active')
              }
        );

        //подсказки при ховере
        $(document).on("mouseenter", '[data-step="municipalities"]:not([data-active="disabled"])', function() {
            $(this).mousemove(function (eventObject) {
                $data_tooltip = $(this).attr("data-step-next");
    
                $('.cnt-tooltip').css({ 
                    "top" : eventObject.pageY + 15,
                    "left" : eventObject.pageX + 15
                  })
                  .show();
                }).mouseout(function () {
                    $('.cnt-tooltip').hide()
                    .css({
                        "top" : 0,
                        "left" : 0
                    });
                });

            $.ajax({
                url: '/wp-admin/admin-ajax.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'tooltip',
                    municipality: $(this).data('step-next')
                },
                beforeSend: function( xhr ) {
                    $( '.cnt-tooltip__main' ).addClass('skeleton-box');
                },
                success: function( data ) {
                    $('.cnt-tooltip__square').html(data.square);
                    $('.cnt-tooltip__population').html(data.population);
                    $('.cnt-tooltip__number_snt').html(data.number_snt);
                    $('.cnt-tooltip__land_plots').html(data.land_plots);
                    
                    $( '.cnt-tooltip__main' ).removeClass('skeleton-box');
                }
            });
        });
    
        //при нажатии на раен в шаге 2
        $('.rf-map').on('click', '[data-tooltip]', function(){    
            let id = $(this).attr('data-tooltip');

           $('#' + id).addClass('active');
        });

        //слайдер
        $('.partner__slider').slick({
            slidesToShow: 5,
            slidesToScroll: 5,
            arrows: true,
            prevArrow: '<button class="slick-prev pull-left"><svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 52 52" fill="none"><path d="M25.9831 47.6688C37.9492 47.6688 47.6497 37.9683 47.6497 26.0021C47.6497 14.0359 37.9492 4.33545 25.9831 4.33545C14.0169 4.33545 4.31641 14.0359 4.31641 26.0021C4.31641 37.9683 14.0169 47.6688 25.9831 47.6688Z" stroke="#B3B3B3" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M25.9831 17.3354L17.3164 26.0021L25.9831 34.6688" stroke="#B3B3B3" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M34.6497 26H17.3164" stroke="#B3B3B3" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
            nextArrow: '<button class="slick-next pull-right"><svg xmlns="http://www.w3.org/2000/svg" width="52" height="52" viewBox="0 0 52 52" fill="none"><path d="M25.9831 47.6688C37.9492 47.6688 47.6497 37.9683 47.6497 26.0021C47.6497 14.0359 37.9492 4.33545 25.9831 4.33545C14.0169 4.33545 4.31641 14.0359 4.31641 26.0021C4.31641 37.9683 14.0169 47.6688 25.9831 47.6688Z" stroke="#B3B3B3" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M25.9831 17.3354L17.3164 26.0021L25.9831 34.6688" stroke="#B3B3B3" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/><path d="M34.6497 26H17.3164" stroke="#B3B3B3" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
          });


          //регистрация смены форм
          $('.register-tabs-wrapper').each(function() {
            let ths = $(this);
            ths.find('.form-tab').not(':first').hide();
            ths.find('.register-input-rol').click(function() {
                ths.find('.register-input-rol').removeClass('active').eq($(this).index()).addClass('active');
                ths.find('.form-tab').hide().eq($(this).index()).fadeIn()
            }).eq(0).addClass('active');
        });
    });    

});
