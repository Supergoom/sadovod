<?php

/*  Настройки меню -----------------------------------------------*/
include_once('menu/dropdown.php');
include_once('menu/classes.php');
include_once('menu/icon.php');
include_once('menu/logo.php');

/*  Подключение контролов -----------------------------------------------*/
include_once('control/slider.php');
include_once('control/month_day.php');
include_once('control/group.php');
include_once('control/group_item.php');

/*  Настройки темы -----------------------------------------------*/
global $wp_customize;

add_action('customize_register', 'customizer_init', 3000);
add_action('customize_preview_init', 'customizer_js_file');

remove_action('customize_controls_init', array($wp_customize, 'prepare_controls'));
add_action('customize_controls_print_footer_scripts', array($wp_customize, 'prepare_controls'), 1001);

function customizer_init(WP_Customize_Manager $wp_customize)
{
    $wp_customize->add_setting('blogkeywords', [
        'default' => str_replace(' ', ', ', get_bloginfo('name') . ' ' . get_bloginfo('description')),
    ]);

    $wp_customize->add_control('blogkeywords', [
        'section' => 'title_tagline',
        'label' => __('Keywords', 'sadovod'),
        'type' => 'text'
    ]);

    $wp_customize->add_setting('site_icon_svg');
    $wp_customize->add_control(
        new WP_Customize_Media_Control(
            $wp_customize,
            'site_icon_svg',
            array(
                'label' => __('Site icon in SVG format', 'sadovod'),
                'section' => 'title_tagline',
                'priority' => 200,
                'settings' => 'site_icon_svg',
                'flex_height' => false,
                'flex_width' => false,
                'button_labels' => array(
                    'select' => __('Select image'),
                    'change' => __('Change image'),
                    'remove' => __('Remove'),
                    'default' => __('Default'),
                    'placeholder' => __('No image selected'),
                    'frame_title' => __('Select image'),
                    'frame_button' => __('Choose image'),
                ),
            )
        )
    );

    $wp_customize->add_setting('page_placeholder');
    $wp_customize->add_control(
        new WP_Customize_Cropped_Image_Control(
            $wp_customize,
            'page_placeholder',
            array(
                'label' => __('Page placeholder', 'sadovod'),
                'section' => 'title_tagline',
                'priority' => 210,
                'settings' => 'page_placeholder',
                'height' => 630,
                'width' => 1200,
                'flex_height' => false,
                'flex_width' => false,
                'button_labels' => array(
                    'select' => __('Select image'),
                    'change' => __('Change image'),
                    'remove' => __('Remove'),
                    'default' => __('Default'),
                    'placeholder' => __('No image selected'),
                    'frame_title' => __('Select image'),
                    'frame_button' => __('Choose image'),
                ),
            )
        )
    );

    //--------------------------------------------------------
    // Настройки меню
    $wp_customize->get_panel('nav_menus')->title = __('Menu Settings', 'sadovod');

    $wp_customize->get_section('title_tagline')->title = __('Title Settings', 'sadovod');

    $wp_customize->get_section('static_front_page')->title = __('Home Settings', 'sadovod');
    $wp_customize->get_section('static_front_page')->priority = 90;

    //--------------------------------------------------------
    // Настройки отображения
    $wp_customize->add_panel('display_panel', array(
        'priority' => 101,
        'title' => __('Display Settings', 'sadovod'),
        'description' => '',
    ));

    //--------------------------------------------------------
    // Настройки загрлушек для записей
    $wp_customize->add_section('placeholder_section', [
        'title' => __('Record placeholder', 'sadovod'),
        'panel' => 'display_panel',
    ]);

    $wp_customize->add_setting('record_placeholder', [
        'default' => get_theme_mod('custom_logo'),
    ]);

    $wp_customize->add_control(
        new WP_Customize_Media_Control(
            $wp_customize,
            'record_placeholder',
            array(
                'label' => __('Record placeholder', 'sadovod'),
                'section' => 'placeholder_section',
                'priority' => 200,
                'settings' => 'record_placeholder',
                'flex_height' => false,
                'flex_width' => false,
                'button_labels' => array(
                    'select' => __('Select image'),
                    'change' => __('Change image'),
                    'remove' => __('Remove'),
                    'default' => __('Default'),
                    'placeholder' => __('No image selected'),
                    'frame_title' => __('Select image'),
                    'frame_button' => __('Choose image'),
                ),
            )
        )
    );

    //--------------------------------------------------------
    // Настройки яндекс метрики
    $wp_customize->add_section('metrika_section', [
        'title' => __('Yandex.Metrika Settings', 'sadovod'),
        'panel' => 'display_panel',
    ]);

    $wp_customize->add_setting('metrika_id', [
        'default' => '',
    ]);

    $wp_customize->add_control('metrika_id', [
        'section' => 'metrika_section',
        'label' => __('Metrika ID', 'sadovod'),
        'type' => 'text'
    ]);

    //--------------------------------------------------------
    // Настройки яндекс карты
    $wp_customize->add_section('yamap_section', [
        'title' => __('Yandex.Map Settings', 'sadovod'),
        'panel' => 'display_panel',
    ]);

    $wp_customize->add_setting('yamap_key', [
        'default' => '',
    ]);

    $wp_customize->add_control('yamap_key', [
        'section' => 'yamap_section',
        'label' => __('API Key', 'sadovod'),
        'type' => 'text'
    ]);

    //--------------------------------------------------------
    // Настройки Подвала
    $wp_customize->add_section('footer_section', [
        'title' => __('Footer Settings', 'sadovod'),
        'panel' => 'display_panel',
    ]);


    $wp_customize->add_setting('footer_copyright_text', [
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'postMessage'
    ]);

    $wp_customize->add_control('footer_copyright_text', [
        'section' => 'footer_section',
        'label' => __('Copyright', 'sadovod'),
        'type' => 'text'
    ]);


    if (isset($wp_customize->selective_refresh)) {

        $wp_customize->selective_refresh->add_partial('footer_copyright_text', [
            'selector' => 'footer .copyright-text',
            'container_inclusive' => false,
            'render_callback' => function () {
                get_option('footer_copyright_text');
            },
            'fallback_refresh' => false,
        ]);
    }

    //--------------------------------------------------------
    // Настройки переменных
    $wp_customize->add_panel('variable_settings', array(
        'priority' => 105,
        'title' => __('Variable Settings', 'sadovod'),
        'description' => '',
    ));

    $wp_customize->add_section('file_upload_options', [
        'title' => __('File upload Settings', 'sadovod'),
        'panel' => 'variable_settings',
    ]);

    $wp_customize->add_setting('max_file_count', [
        'default' => 10
    ]);

    $wp_customize->add_control('max_file_count', [
        'section' => 'file_upload_options',
        'label' => __('Max file count', 'sadovod'),
        'type' => 'number'
    ]);

    $wp_customize->add_setting('max_image_size', [
        'default' => 5
    ]);

    $wp_customize->add_control('max_image_size', [
        'section' => 'file_upload_options',
        'label' => __('Max image size', 'sadovod'),
        'type' => 'number'
    ]);

    $wp_customize->add_setting('max_video_size', [
        'default' => 30
    ]);

    $wp_customize->add_control('max_video_size', [
        'section' => 'file_upload_options',
        'label' => __('Max video size', 'sadovod'),
        'type' => 'number'
    ]);

    $wp_customize->add_setting('max_audio_size', [
        'default' => 5
    ]);

    $wp_customize->add_control('max_audio_size', [
        'section' => 'file_upload_options',
        'label' => __('Max audio size', 'sadovod'),
        'type' => 'number'
    ]);

    $wp_customize->add_setting('max_document_size', [
        'default' => 10
    ]);

    $wp_customize->add_control('max_document_size', [
        'section' => 'file_upload_options',
        'label' => __('Max document size', 'sadovod'),
        'type' => 'number'
    ]);

    // Настройки режима разработки
    $wp_customize->add_section('devmode_options', [
        'title' => __('Devmode Settings', 'sadovod'),
        'priority' => 110
    ]);

    $wp_customize->add_setting('devmode_enabled', []);

    $wp_customize->add_control('devmode_enabled', [
        'section' => 'devmode_options',
        'label' => __('Enable devmode', 'sadovod'),
        'type' => 'checkbox'
    ]);

    $wp_customize->add_setting('devmode_code', []);

    $wp_customize->add_control('devmode_code', [
        'section' => 'devmode_options',
        'label' => __('Devmode return code', 'sadovod'),
        'type' => 'number'
    ]);

    // Настройки Безопасности
    $wp_customize->add_panel('security_panel', array(
        'priority' => 115,
        'title' => __('Security Settings', 'sadovod'),
        'description' => '',
    ));

    $wp_customize->add_section('rate_limit_options', [
        'title' => __('Rate Limit Settings', 'sadovod'),
        'panel' => 'security_panel',
    ]);

    $wp_customize->add_setting('rate_limit', []);

    $wp_customize->add_control('rate_limit', [
        'section' => 'rate_limit_options',
        'label' => __('Rate limit', 'sadovod'),
        'description' => __('Count of requests sent and received in second.', 'sadovod'),
        'type' => 'number'
    ]);

    $wp_customize->add_setting('penalty_time', []);

    $wp_customize->add_control('penalty_time', [
        'section' => 'rate_limit_options',
        'label' => __('Penalty time', 'sadovod'),
        'description' => __('Seconds to wait if rate limit exceeded.', 'sadovod'),
        'type' => 'number'
    ]);

    // Настройки Безопасности
    $wp_customize->add_section('captcha_options', [
        'title' => __('Captcha Settings', 'sadovod'),
        'panel' => 'security_panel',
    ]);

    $wp_customize->add_setting('captcha_site_key', [
        'default' => '6LezAFknAAAAANz0AxyjOaBxApeG5-X3kWo-oyh1'
    ]);

    $wp_customize->add_control('captcha_site_key', [
        'section' => 'captcha_options',
        'label' => __('Site key', 'sadovod'),
        'description' => __('Site transmits this key in the HTML code to users devices.', 'sadovod'),
        'type' => 'text'
    ]);

    $wp_customize->add_setting('captcha_secret_key', [
        'default' => '6LezAFknAAAAAHas0H2_Sjh0-9TJNiZRNVIH0Qaz'
    ]);

    $wp_customize->add_control('captcha_secret_key', [
        'section' => 'captcha_options',
        'label' => __('Secret key', 'sadovod'),
        'description' => __('Site sends and gets data from the reCAPTCHA service using this secret key.', 'sadovod'),
        'type' => 'text'
    ]);

    foreach ($wp_customize->panels() as $panel_id => $panel) {
        if ($panel_id == 'widgets')
            $wp_customize->get_panel('widgets')->title = __('Widgets Settings', 'sadovod');
    }

    // Настройки Виджетов
    if ($panel = $wp_customize->get_panel('widgets'))
        $panel->title = __('Widgets Settings', 'sadovod');
}


function customizer_js_file()
{
    wp_enqueue_script('theme-customizer', get_stylesheet_directory_uri() . '/settings/assets/js/theme.js', ['jquery', 'customize-preview'], null, true);
}
