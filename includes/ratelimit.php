<?php

//TODO: Add captcha wait option
function check_request()
{
    if (!wp_using_themes())
        return;

    if (is_customize_preview()) {
        return;
    }

    $now = microtime(true);
	$rate_limit = get_theme_mod('rate_limit', 5);
	$penalty_time = get_theme_mod('penalty_time', 30);

    $interval = 1 / $rate_limit;

    if (
        isset($_SESSION['last_request'])
        && ($_SESSION['last_request'] + $interval > $now)
    ) {
        $_SESSION['last_request'] = $now + $penalty_time;


        $error_text = '<h4>' . __('Maximum request count reached!', 'bingobox-dialogs') . '</h4>' .
            '<span>' . sprintf(__('Please, wait %d seconds.', 'bingobox-dialogs'), $penalty_time) . '<span>';

        if (defined('DOING_AJAX') && DOING_AJAX) {
            header('Content-type: application/json; charset=UTF-8');
            header('HTTP/2 429 ' . get_status_header_desc(429));

            $out['error'] = array(
                'note' => true,
                'text' => $error_text
            );

            echo (json_encode($out));
        } else {
            header('Refresh: ' . $penalty_time + 1);
            header('Content-type: text/html; charset=UTF-8');
            header('HTTP/2 429 ' . get_status_header_desc(429));

            echo $error_text;
        }

        exit();
    }

    $_SESSION['last_request'] = $now;
}

add_action('init', 'check_request', 1);
