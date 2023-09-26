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
            let thisPrewievStep = $(this).data('step-prewiev');
            let thisNextStep = $(this).data('step-next');
            let isAcrive = $(this).data('active');

            //отмена для disabled
            if( isAcrive == 'disabled' ) return;

            //добавить в навигацию
            $('.district__nav').append(`<span data-nav-step="${carrentStep}">` + $(this).attr('data-title') + '</span>');

            //обновить кнопку назад
            $('.close-district').attr('data-prewiev', carrentStep);

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
        // $("[data-step-next]").mousemove(function (eventObject) {
        //     $data_tooltip = $(this).attr("data-step-next");

        //     $('#' + $data_tooltip).css({ 
        //         "top" : eventObject.pageY + 15,
        //         "left" : eventObject.pageX + 15
        //       })
        //       .show();
        //     }).mouseout(function () {
        //         $('#' + $data_tooltip).hide()
        //         .css({
        //             "top" : 0,
        //             "left" : 0
        //         });
        // });

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

    });    

});
