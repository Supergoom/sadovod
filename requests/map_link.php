<?php

add_action('wp_ajax_map_link', 'get_resource');
add_action('wp_ajax_nopriv_map_link', 'get_resource');

function get_resource()
{
    $regiony = get_field('regiony', 'option');
    foreach ($regiony as $key => $value) {
        if ($key === $_REQUEST['region']) {
            $result = [
                'url' => $value,
            ];
        }
    }
    echo json_encode($result);

    // echo getResourceById($_REQUEST['region']);
    die;
}
