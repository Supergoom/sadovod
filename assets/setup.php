<?php

/*  Загрузка ассетов -----------------------------------------------*/

function sadovod_enqueue()
{
    $theme_version = wp_get_theme()->get('Version');

    $version = is_string($theme_version) ? $theme_version : false;

    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');

    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-block-style');
    wp_dequeue_style('global-styles');

    //wp_dequeue_script('wp-a11y');
    //wp_deregister_script('wp-a11y');

    // styles
    wp_register_style('font', get_stylesheet_directory_uri() . '/assets/fonts/SFPro/SFPro.css', array(), $version, 'all');
    wp_enqueue_style('font');

    wp_register_style('i-font', get_stylesheet_directory_uri() . '/assets/fonts/sadovod/sadovod.css', array(), $version, 'all');
    wp_enqueue_style('i-font');

    wp_register_style('bootsrap', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css', array(), $version, 'all');
    wp_enqueue_style('bootsrap');

    wp_register_style('bootsrap-select', get_stylesheet_directory_uri() . '/assets/css/bootstrap.select.min.css', array('bootsrap'), $version, 'all');
    wp_enqueue_style('bootsrap-select');

    wp_register_style('style', get_stylesheet_directory_uri() . '/assets/css/main.css', array(), $version, 'all');
    wp_enqueue_style('style');

    wp_register_style('dr', get_stylesheet_directory_uri() . '/assets/css/dr.css', array('style'), $version, 'all');
    wp_enqueue_style('dr');

	wp_register_style('ns', get_stylesheet_directory_uri() . '/assets/css/ns.css', array('style', 'dr'), $version, 'all');
    wp_enqueue_style('ns');

    wp_register_style('slick', get_stylesheet_directory_uri() . '/assets/css/slick.css', array(), $version, 'all');
    wp_enqueue_style('slick');
    // scripts
    wp_enqueue_script('jquery');

    wp_register_script('bootsrap', get_stylesheet_directory_uri() . '/assets/js/bootstrap.min.js', array('jquery'), $version, true);
    wp_enqueue_script('bootsrap');

    wp_register_script('bootsrap-select', get_stylesheet_directory_uri() . '/assets/js/bootstrap.select.min.js#asyncload', array('jquery', 'bootsrap', 'wp-i18n'), $version, true);
    wp_set_script_translations('bootsrap-select', 'sadovod-scripts', get_template_directory() . '/languages');
    wp_enqueue_script('bootsrap-select');


    wp_register_script('pdf', get_stylesheet_directory_uri() . '/assets/js/pdf.min.js', array('jquery'), $version, true);
    wp_enqueue_script('pdf');

    wp_register_script('pdf-viewer', get_stylesheet_directory_uri() . '/assets/js/pdf.viewer.min.js', array('jquery', 'pdf'), $version, true);
    wp_enqueue_script('pdf-viewer');

    wp_add_inline_script(
        'pdf',
        'pdfjsLib.disableStream = true;'
            . 'pdfjsLib.disableAutoFetch = true;'
            . 'pdfjsLib.GlobalWorkerOptions.workerSrc = "' . get_stylesheet_directory_uri() . '/assets/js/pdf.worker.min.js";'
    );

    wp_register_script('slick', get_stylesheet_directory_uri() . '/assets/js/slick.min.js#asyncload', array('jquery'), $version, true);
    wp_enqueue_script('slick');

    wp_register_script('lazysizes', get_stylesheet_directory_uri() . '/assets/js/lazysizes.min.js#asyncload', array('jquery'), $version, true);
    wp_enqueue_script('lazysizes');

    wp_register_script('main', get_stylesheet_directory_uri() . '/assets/js/main.js#asyncload', array('jquery', 'bootsrap', 'wp-i18n', 'jquery-ui-slider', 'jquery-ui-autocomplete'), $version, true);
    wp_set_script_translations('main', 'sadovod-scripts', get_template_directory() . '/languages');
    wp_enqueue_script('main');

    wp_register_script('dr', get_stylesheet_directory_uri() . '/assets/js/dr.js#asyncload', array('jquery', 'bootsrap', 'wp-i18n', 'jquery-ui-slider', 'jquery-ui-autocomplete'), $version, true);
    wp_set_script_translations('dr', 'sadovod-scripts', get_template_directory() . '/languages');
    wp_enqueue_script('dr');

    // Раскоментировать когда будет известен metrikaID
    /* wp_register_script('yametrika', get_stylesheet_directory_uri() . '/assets/js/ya.metrika.js#asyncload', array('jquery'), $version, true);
    wp_enqueue_script('yametrika');
    wp_add_inline_script('yametrika', 'const metrikaID = '.get_theme_mod('metrika_id', '').', userID = 0, UserPage = 0;');

    if (in_array(wp_get_raw_referer(), array('https://metrika.yandex.ru/', 'https://metrica.yandex.com/'))) {
        wp_register_script('yametrika-rc', get_stylesheet_directory_uri() . '/assets/js/ya.metrika.rc.js#asyncload', array('jquery', 'yametrika'), $version, true);
        wp_enqueue_script('yametrika-rc');
    } */

    // AJAX
    wp_localize_script('main', 'ajax', array(
        'url' => admin_url('admin-ajax.php'),
        'surl' => admin_url('admin-ajax.php', 'service'),
        'nonce' => wp_create_nonce('nonce')
    ));

    // JQMIGRATE
    global $wp_scripts;
    $m = $wp_scripts->registered['jquery-migrate'];
    $m->extra['before'][] = 'temp_jm_logconsole = window.console.log; window.console.log=null;';
    $m->extra['after'][] = 'window.console.log=temp_jm_logconsole;';

    // Captcha
    //wp_register_script('recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . get_theme_mod('captcha_site_key'), array(), $version, true);
    //wp_enqueue_script('recaptcha');

    // Other data
    wp_localize_script('main', 'settings', array(
        'site_key' => get_theme_mod('captcha_site_key')
    ));

    wp_localize_script('main', 'placeholder', array(
        'small' => get_stylesheet_directory_uri() . '/assets/img/placeholder-small.jpg',
        'medium' => get_stylesheet_directory_uri() . '/assets/img/placeholder-medium.jpg',
    ));

    wp_localize_script('main', 'uploads', array(
        'file_limit' => get_theme_mod('max_file_count', 10),
        'limits' => get_file_size_limits()
    ));
}
add_action('wp_enqueue_scripts', 'sadovod_enqueue', 1);

/*  Асинхронная загрузка js -----------------------------------------------*/

add_filter('clean_url', function ($url) {
    if (strpos($url, '#asyncload') === false)
        return $url;
    else if (is_admin())
        return str_replace('#asyncload', '', $url);
    else
        return str_replace("#asyncload", '', $url) . "' async='async' defer='defer";
}, 11, 1);


/*  Предзагрузка шрифта -----------------------------------------------*/
if (!function_exists('sadovod_preload_webfonts')) :

    function sadovod_preload_webfonts()
    {
        global $font_csp_nonce;
        echo '<link rel="preload" nonce="' . $font_csp_nonce . '" href="' . get_stylesheet_directory_uri() . '/assets/fonts/SFPro/SFProDisplay-Regular.woff" as="font" crossorigin type="font/woff2">' . "\n";
        echo '<link rel="preload" nonce="' . $font_csp_nonce . '" href="' . get_stylesheet_directory_uri() . '/assets/fonts/SFPro/SFProText-Regular.woff" as="font" crossorigin type="font/woff2">' . "\n";
    }

endif;

add_action('wp_head', 'sadovod_preload_webfonts');

/* Старт сессии -----------------------------------------------*/
function start_user_session()
{
    if (!session_id()) {
        session_set_cookie_params([
            'lifetime' => WEEK_IN_SECONDS,
            'path' => '/',
            'domain' => '.' . get_site_root_domain(),
            'secure' => true,
            'httponly' => false,
            'samesite' => 'Strict'
        ]);

        session_start();
    }
}
add_filter('init', 'start_user_session', 1);

/* Нет кастомизации -----------------------------------------------*/
function setup_customizer_class($classes)
{
    if (!is_admin()) {
        $classes[] = 'no-customize-support';
    } elseif (is_customize_preview()) {
        $classes[] = 'customize-support';
    }

    return $classes;
}
add_filter('body_class', 'setup_customizer_class');
