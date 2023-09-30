<?php

/*  Система ------------------------------------------------*/
include_once('config/debug.php');

/*  Вспомогательные функции --------------------------------*/
include_once('includes/mysql.php');

include_once('includes/ico_gen.php');

include_once('includes/devmode.php');

include_once('includes/ratelimit.php');
include_once('includes/helpers.php');
include_once('includes/scripts.php');
include_once('includes/images.php');
include_once('includes/utils.php');
include_once('includes/seo.php');

include_once('includes/page.php');
include_once('includes/term.php');
include_once('includes/comment.php');
include_once('includes/comments.php');
include_once('includes/category.php');


include_once('includes/nav.php');
include_once('includes/path.php');
include_once('includes/reports.php');
include_once('includes/profile-update.php');

/*  Установка --------------------------------*/
include_once('includes/install.php');

/*  Вордпресс ----------------------------------------------*/
include_once('config/protection.php');

include_once('config/lang.php');

include_once('config/template.php');
include_once('config/theme.php');
include_once('config/embed.php');
include_once('config/mail.php');
include_once('config/pwa.php');
include_once('config/seo.php');

/*  Настройки темы ------------------------------------------*/
include_once('settings/setup.php');

/*  Настройка ассетов ---------------------------------------*/
include_once('assets/setup.php');

/*  Загрузка модулей --------------------------------------------*/
include_once('modules/slugify/main.php');
include_once('modules/darkify/main.php');
include_once('modules/upscale/main.php');

/*  AJAX --------------------------------------------------------*/

if (wp_doing_ajax()) {
    include_once('requests/user_message.php');
	include_once('requests/filter-blog.php');
};

// Перенаправление со страниц входа


// Перенаправление на страницу регистрации

function register_link_url( $url ) {
    if ( ! is_user_logged_in() ) {
        if ( get_option('users_can_register') )
            $url = '<li><a href="' . get_bloginfo( 'url' ) . "/register" . '">' . __('Register', 'yourtheme') . '</a></li>';
        else  $url = '';
    } else {
        $url = '<li><a href="' . admin_url() . '">' . __('Site Admin', 'yourtheme') . '</a></li>';
    }
    return $url;
}
add_filter( 'register', 'register_link_url', 10, 2 );


/*  Elementor HEADER - Blog ----------------------------------------------*/
/*
if (!function_exists('hello_elementor_body_open')) {
    function hello_elementor_body_open()
    {
        wp_body_open();
    }
}
*/