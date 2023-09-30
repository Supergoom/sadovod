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
    include_once('requests/tooltip.php');
    include_once('requests/municipalities-list.php');
};

/*   Дополнительные поля пользователя ------------------------------*/
include_once('settings/fields/user_fields.php');
include_once('settings/fields/admin_fields.php');


/*   Регистрация пользователя ------------------------------*/
include_once('registration/user.php');

/*   Получаем данные с кастомных полей ------------------------------*/
include_once('layouts/widgets/custom_fields.php');

//Аккардионы в админке
function admin_js()
{
?>
    <script>
        jQuery(document).ready(function($) {
            $('tr:not(.acf-clone) .acf-table [data-key="field_6515696bdbf81"]').each(function(index) {
                let val = $(this).find('.acf-accordion-content [data-name="name_municipality"] .acf-input input').val();
                if (val) {
                    $(this).find('.acf-accordion-title label').html(val);
                }
            });
        });
    </script>
<?php }
add_action('admin_head', 'admin_js');

// Перенаправление со страниц входа

//add_action( 'init', 'level_check' );
//
//function level_check() {
//    // is_admin() will let us know if we're in admin pages
//    // only admins can 'update_core' and 'list_users'
//    if ( is_admin() && !current_user_can( 'update_core' ) && !current_user_can( 'list_users' ) ) {
//        // redirect or whatever here
//        echo "not permitted";
//        die();
//    }
//}

//function custom_login() {
//    echo header("Location: " . get_bloginfo( 'url' ) . "/login");
//}
//
//add_action('login_head', 'custom_login');
//
//function login_link_url( $url ) {
//    $url = get_bloginfo( 'url' ) . "/login";
//    return $url;
//}
//add_filter( 'login_url', 'login_link_url', 10, 2 );

// Перенаправление на страницу регистрации

function register_link_url($url)
{
    if (!is_user_logged_in()) {
        if (get_option('users_can_register'))
            $url = '<li><a href="' . get_bloginfo('url') . "/register" . '">' . __('Register', 'yourtheme') . '</a></li>';
        else  $url = '';
    } else {
        $url = '<li><a href="' . admin_url() . '">' . __('Site Admin', 'yourtheme') . '</a></li>';
    }
    return $url;
}

add_filter('register', 'register_link_url', 10, 2);

add_action('wp_enqueue_scripts', 'add_jquery');

function add_jquery()
{
    wp_enqueue_script('jquery');
}
