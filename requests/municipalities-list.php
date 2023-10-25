<?php
add_action('wp_ajax_municipalities_list', 'municipalities_list_ajax'); // wp_ajax_{ЗНАЧЕНИЕ ПАРАМЕТРА ACTION!!}
add_action('wp_ajax_nopriv_municipalities_list', 'municipalities_list_ajax');  // wp_ajax_nopriv_{ЗНАЧЕНИЕ ACTION!!}
// первый хук для авторизованных, второй для не авторизованных пользователей

function municipalities_list_ajax()
{
    echo getSntByIdMunicipalityJson($_POST['municipality']);

    die; // даём понять, что обработчик закончил выполнение
}
