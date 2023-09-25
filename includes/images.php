<?php


/*  Настройки лого -----------------------------------------------*/
add_theme_support('custom-logo', [
    'height' => 63,
    'width' => 145,
    'flex-width' => false,
    'flex-height' => false,
    'header-text' => '',
    'unlink-homepage-logo' => true,
]);

/*  Настройки картинок -----------------------------------------------*/
add_theme_support('post-thumbnails');
add_image_size('tiny', 145, 63, true);
add_image_size('pwa', 96, 96, true);
add_image_size('mpwa', 256, 256, true);
add_image_size('medium', 300, 215, true);
add_image_size('big', 350, 250, true);
add_image_size('wide', 600, 320, true);
add_image_size('bigger', 775, 500, true);
add_image_size('large', 1024, 768, true);
add_image_size('snippet', 1200, 630, true);

/*  Подменить вывод фавикона -----------------------------------------------*/
function favicon_request_owerride()
{
    if ($_SERVER["REQUEST_URI"] === '/favicon.ico') {
        header('Content-Type: image/vnd.microsoft.icon');
        $icon = wp_get_attachment_image_url(get_option('site_icon'), 'mpwa');
        echo file_get_contents($icon);
        exit;
    }
}
remove_action('do_favicon', 'do_favicon');
add_action('do_favicon', 'favicon_request_owerride');

/*  Добавить возможность загружать сфг -----------------------------------------------*/
function svg_upload_allow($mimes)
{
    $mimes['svg']  = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'svg_upload_allow');