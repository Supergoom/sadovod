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
    include_once('requests/map_link.php');
};

/*   Дополнительные поля пользователя ------------------------------*/
include_once('settings/fields/user_fields.php');
include_once('settings/fields/admin_fields.php');


/*   Регистрация пользователя ------------------------------*/
include_once('registration/user.php');

/*   Получаем данные с кастомных полей ------------------------------*/
include_once('layouts/widgets/custom_fields.php');

add_action( 'acf/save_post', 'updatingFieldIdGroup' );

/*   Rest API ------------------------------*/
include_once('rest_api/RestApiGis.php');
include_once('rest_api/GetUser.php');

/*   ID пользователя в админке ------------------------------*/
/*
 * Добавление колонки
 */
function true_user_id_column( $columns ) {
    $columns['user_id'] = 'ID';
    return $columns;
}
add_filter('manage_users_columns', 'true_user_id_column');

/*
 * Заполнение колонки
 */
function true_user_id_column_content($value, $column_name, $user_id) {
    if ( 'user_id' == $column_name )
        return $user_id;
    return $value;
}
add_action('manage_users_custom_column',  'true_user_id_column_content', 10, 3);


function admin_js()
{
?><script>
        jQuery(document).ready(function($) {
            $('tr:not(.acf-clone) .acf-table [data-key="field_6515696bdbf81"]').each(function(index) {
                let val = $(this).find('.acf-accordion-content [data-name="name_municipality"] .acf-input input').val();
                if (val) {
                    $(this).find('.acf-accordion-title label').html(val);
                }
            });
        });
    </script><?php
}

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

/*   Обрезаем строку нужной длинны ------------------------------*/

function stringLength($string, $length) {

    $string = strip_tags($string);
    $string = substr($string, 0, $length);
    $string = rtrim($string, "!,.-");
    $string = substr($string, 0, strrpos($string, ' '));

    return $string . " ...";
}

/*   Убираем дату с парсеного поста ------------------------------*/

function text( $text ){
    return  preg_replace('/(\d{2}).(\d{2}).(\d{4})/', '', $text, 1);
}
add_filter('the_content', 'text');

/*   Проверка на уникальность email при регистрации ------------------------------*/

add_filter( 'wpcf7_validate', 'email_already_in_db_owner', 10, 2 );

function email_already_in_db_owner ( $result, $tags ) {
    // retrieve the posted email
    $form  = WPCF7_Submission::get_instance();
    $email = $form->get_posted_data('email');
    // if already in database, invalidate
    if( email_exists( $email ) ) // email_exists is a WP function
        $result->invalidate('email', 'Ваш адрес электронной почты существует в нашей базе данных');
    // return the filtered value
    return $result;
}

add_filter( 'wpcf7_validate', 'email_already_in_db', 10, 2 );

function email_already_in_db ( $result, $tags ) {
    // retrieve the posted email
    $form  = WPCF7_Submission::get_instance();
    $email = $form->get_posted_data('email_non_ownerr');
    // if already in database, invalidate
    if( email_exists( $email ) ) // email_exists is a WP function
        $result->invalidate('email_non_ownerr', 'Ваш адрес электронной почты существует в нашей базе данных');
    // return the filtered value
    return $result;
}

// Регистрируем AJAX-обработчик для выполнения поиска данных
add_action('wp_ajax_my_cf7_autocomplete_handler', 'my_cf7_autocomplete_handler');
add_action('wp_ajax_nopriv_my_cf7_autocomplete_handler', 'my_cf7_autocomplete_handler');

function my_cf7_autocomplete_handler()
{
    $search_query = sanitize_text_field($_POST['search_query']);

    if ($search_query !== '' && strlen($search_query) >= 3) {
        global $wpdb;
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM SAD_options WHERE option_value LIKE '%%%s%%' AND option_name LIKE '%%%_name_snt%%'", $search_query),
            ARRAY_A
        );

        $autocomplete_results = [];
        foreach ($results as $result) {
            $autocomplete_results[] = $result['option_value'];
        }
    }

    wp_send_json_success($autocomplete_results);
}

// Обработчик события ввода данных в поле ввода Contact Form 7

add_action('wp_footer', 'ajax_script_snt');
function ajax_script_snt()
{ ?>
    <script type="text/javascript">
		jQuery(document).ready(function ($) {

			var timeoutId;

			$('.wpcf7-form input[name="namesnt"]').on('input', function () {
				var searchQuery = $(this).val();

				if (searchQuery !== '' && searchQuery.length >= 3) {

					clearTimeout(timeoutId);
					timeoutId = setTimeout(function () {

						$.ajax({
							url: '<?php echo admin_url('admin-ajax.php'); ?>',
							type: 'POST',
							data: {
								action: 'my_cf7_autocomplete_handler',
								search_query: searchQuery
							},
							success: function (response) {
								if (response.success) {
									var results = response.data;

									var autocompleteList = $('.autocomplete-list');
									autocompleteList.empty();

									if (results.length > 0) {

										for (var i = 0; i < results.length; i++) {
											var listItem = $('<li class="list-group-item list-group-item-action list-group-item-primary">').text(results[i]);
											autocompleteList.append(listItem);
										}
										autocompleteList.show();
									} else {
										var listItem = $('<li class="list-group-item list-group-item-warning">').text('Такого СНТ в нашей базе данных нет');
										autocompleteList.append(listItem);
										// autocompleteList.hide();
									}
								} else {
									console.log('Ошибка при обработке AJAX-запроса');
								}
							},
							error: function () {
								console.log('Ошибка при выполнении AJAX-запроса');
							}
						});
					}, 100);
                }

			});

			$('.autocomplete-list').on('click', 'li', function () {
				var selectedValue = $(this).text();

				$('.wpcf7-form input[name="namesnt"]').val(selectedValue);

				$('.autocomplete-list').hide();
			});
		});
    </script>
<?php }