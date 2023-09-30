<?php
add_action('wp_ajax_municipalities_list', 'municipalities_list_ajax'); // wp_ajax_{ЗНАЧЕНИЕ ПАРАМЕТРА ACTION!!}
add_action('wp_ajax_nopriv_municipalities_list', 'municipalities_list_ajax');  // wp_ajax_nopriv_{ЗНАЧЕНИЕ ACTION!!}
// первый хук для авторизованных, второй для не авторизованных пользователей

function municipalities_list_ajax()
{

    $municipality = $_POST['municipality'];

    ob_start();
    echo '
        <div class="map-step__last-text-item active" data-step-next="stariy-fioletn">Старый Фиолент</div>
        <div class="map-step__last-text-item active" data-bs-toggle="modal" data-bs-target="#authMap">Старый Фиолент</div>
        <div class="map-step__last-text-item">Луч, СТСН</div>
    ';

    $response['html'] = ob_get_contents();
    ob_end_clean();
    echo json_encode($response);
    die; // даём понять, что обработчик закончил выполнение
}
