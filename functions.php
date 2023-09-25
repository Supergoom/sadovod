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

//function custom_login()
//{
//    echo header("Location: " . get_bloginfo('url') . "/login");
//}
//
//add_action('login_head', 'custom_login');

//function login_link_url($url)
//{
//    $url = get_bloginfo('url') . "/login";
//    return $url;
//}
//add_filter( 'login_url', 'login_link_url', 10, 2 );

//Redirect for login from wp-login.php to my-account if not admin
//add_action('init', 'prevent_wp_login');
//function prevent_wp_login() {
//    // WP tracks the current page - global the variable to access it
//    global $pagenow;
//    // Check if a $_GET['action'] is set, and if so, load it into $action variable
//    $action = (isset($_GET['action'])) ? $_GET['action'] : '';
//
//    //check if we came from the admin page or wp-admin
//    $refer = urlencode($_SERVER["REQUEST_URI"]);
//    if (strpos($refer, 'wp-admin') !== false) {
//        wp_redirect('/wp-login.php');
//    } else {
//        // Check if we're on the login page, and ensure the action is not 'logout'
//        if( $pagenow === 'wp-login.php' && ( ! $action || ( $action && ! in_array($action, array('logout', 'lostpassword', 'rp', 'resetpass'))))) {
//            // Load the home page url
//            // Redirect to the home page
//            wp_redirect('/login/');
//            // Stop execution to prevent the page loading for any reason
//            exit();
//        }
//    }
//}

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