<?php
add_action('wp_ajax_tooltip', 'tooltip_ajax'); // wp_ajax_{ЗНАЧЕНИЕ ПАРАМЕТРА ACTION!!}
add_action('wp_ajax_nopriv_tooltip', 'tooltip_ajax');  // wp_ajax_nopriv_{ЗНАЧЕНИЕ ACTION!!}
// первый хук для авторизованных, второй для не авторизованных пользователей

function tooltip_ajax()
{
    echo getDataMunicipalityByIdJson($_POST['municipality']);
    die; // даём понять, что обработчик закончил выполнение
}
