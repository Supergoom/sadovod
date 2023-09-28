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

// Функция транслитерации
function transliterate($string) {
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'i',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'kh',   'ц' => 'tc',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'shch',
        'ь' => '',  'ы' => 'y',   'ъ' => '',
        'э' => 'e',   'ю' => 'iu',  'я' => 'ia',
        '’' => ' ',  '.' => '',

        'А' => 'A',   'Б' => 'B',   'В' => 'V',
        'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
        'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
        'И' => 'I',   'Й' => 'I',   'К' => 'K',
        'Л' => 'L',   'М' => 'M',   'Н' => 'N',
        'О' => 'O',   'П' => 'P',   'Р' => 'R',
        'С' => 'S',   'Т' => 'T',   'У' => 'U',
        'Ф' => 'F',   'Х' => 'Kh',   'Ц' => 'Tc',
        'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Shch',
        'Ь' => '',  'Ы' => 'Y',   'Ъ' => '',
        'Э' => 'E',   'Ю' => 'Iu',  'Я' => 'Ia',
    );
    return strtr($string, $converter);
}


// Дополнительные поля пользователя

// когда пользователь сам редактирует свой профиль
add_action( 'show_user_profile', 'true_show_profile_fields' );

// когда чей-то профиль редактируется админом
add_action( 'edit_user_profile', 'true_show_profile_fields' );

function true_show_profile_fields( $user ) {

    echo '<h1 style="color:red">Дополнительные данные пользователя</h1>';
    echo '<table class="form-table" role="presentation">';

    // добавляем поле Фамилия
    $last_name = get_the_author_meta( 'last_name', $user->ID );
    echo '<tr><th><label for="last_name">Фамилия</label></th>
 	<td><input type="text" name="last_name" id="last_name" value="' . esc_attr($last_name) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Имя
    $first_name = get_the_author_meta( 'first_name', $user->ID );
    echo '<tr><th><label for="first_name">Имя</label></th>
 	<td><input type="text" name="first_name" id="first_name" value="' . esc_attr($first_name) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Отчество
    $patronymic = get_the_author_meta( 'patronymic', $user->ID );
    echo '<tr><th><label for="patronymic">Отчество</label></th>
 	<td><input type="text" name="patronymic" id="patronymic" value="' . esc_attr($patronymic) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Телефон
    $tel = get_the_author_meta( 'tel', $user->ID );
    echo '<tr><th><label for="tel">Телефон</label></th>
 	<td><input type="text" name="tel" id="tel" value="' . esc_attr($tel) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Email
    $email = get_the_author_meta( 'email', $user->ID );
    echo '<tr><th><label for="email">Email</label></th>
 	<td><input type="text" name="email" id="email" value="' . esc_attr($email) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Название СНТ
    $namesnt = get_the_author_meta( 'namesnt', $user->ID );
    echo '<tr><th><label for="namesnt">Название СНТ</label></th>
 	<td><input type="text" name="namesnt" id="snt_name" value="' . esc_attr($namesnt) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Квдвстровый номер
    $cadastral_num = get_the_author_meta( 'cadastral_num', $user->ID );
    echo '<tr><th><label for="cadastral_num">Кадастровый номер</label></th>
 	<td><input type="text" name="cadastral_num" id="cadastral_num" value="' . esc_attr($cadastral_num) . '" class="regular-text" /></td>
	</tr>';
    // добавляем поле Адрес
    $address = get_the_author_meta( 'address', $user->ID );
    echo '<tr><th><label for="address">Адрес</label></th>
 	<td><input type="text" name="address" id="address" value="' . esc_attr($address) . '" class="regular-text" /></td>
	</tr>';

    echo '</table>';

}

// когда пользователь сам редактирует свой профиль
add_action( 'personal_options_update', 'true_save_profile_fields' );
// когда чей-то профиль редактируется админом например
add_action( 'edit_user_profile_update', 'true_save_profile_fields' );

function true_save_profile_fields( $user_id ) {

    update_user_meta( $user_id, 'last_name', sanitize_text_field( $_POST[ 'last_name' ] ) );
    update_user_meta( $user_id, 'first_name', sanitize_text_field( $_POST[ 'first_name' ] ) );
    update_user_meta( $user_id, 'patronymic', sanitize_text_field( $_POST[ 'patronymic' ] ) );
    update_user_meta( $user_id, 'tel', sanitize_text_field( $_POST[ 'tel' ] ) );
    update_user_meta( $user_id, 'email', sanitize_text_field( $_POST[ 'email' ] ) );
    update_user_meta( $user_id, 'namesnt', sanitize_text_field( $_POST[ 'namesnt' ] ) );
    update_user_meta( $user_id, 'cadastral_num', sanitize_text_field( $_POST[ 'cadastral_num' ] ) );
    update_user_meta( $user_id, 'address', sanitize_text_field( $_POST[ 'address' ] ) );
}



// Contact Form 7 регистрация пользователя

function create_user_from_registration($cfdata) {
    if (!isset($cfdata->posted_data) && class_exists('WPCF7_Submission')) {

        $submission = WPCF7_Submission::get_instance();
        if ($submission) {
            $formdata = $submission->get_posted_data();
        }
    } elseif (isset($cfdata->posted_data)) {
        $formdata = $cfdata->posted_data;
    } else {
        return $cfdata;
    }

    // Check this is the user registration form
    if ( $cfdata->title() == 'Запрос на регистрацию собственника') {
        //$pass = wp_generate_password( 12, false );

        $last_name = sanitize_text_field($formdata['last_name']);
        $name = sanitize_text_field($formdata['first_name']);
        $patronymic = sanitize_text_field($formdata['patronymic']);
        $tel = sanitize_text_field($formdata['tel']);
        $email = sanitize_text_field($formdata['email']);
        $namesnt = sanitize_text_field($formdata['namesnt']);
        $cadastral_num = sanitize_text_field($formdata['cadastral_num']);
        $address = sanitize_text_field($formdata['address']);
        $pass = sanitize_text_field($formdata['pass']);

        // Construct a username from the user's name
        $username = strtolower(str_replace(' ', '', transliterate($name)));
        $name_parts = explode(' ', $name);
        if ( !email_exists( $email ) ) {
            // Find an unused username
            $username_tocheck = $username;
            $i = 1;
            while ( username_exists( $username_tocheck ) ) {
                $username_tocheck = $username . $i++;
            }
            $username = $username_tocheck;
            // Create the user
            $userdata = [
                'user_login' => $username,
                'user_pass' => $pass,
                'user_email' => $email,
                'nickname' => reset($name_parts),
                'display_name' => $name,
                'first_name' => $name,
                'last_name' => $last_name,
                'role' => 'subscriber'
            ];
            $userMetaData = [
                'patronymic' => $patronymic,
                'tel' => $tel,
                'namesnt'  => $namesnt,
                'cadastral_num' => $cadastral_num,
                'address' => $address,
            ];

            $user_id = wp_insert_user( $userdata );

            if ( !is_wp_error($user_id) ) {

                if ( isset($userMetaData['patronymic']) )
                    update_user_meta($user_id, 'patronymic', $userMetaData['patronymic']);
                if ( isset($userMetaData['tel']) )
                    update_user_meta($user_id, 'tel', $userMetaData['tel']);
                if ( isset($userMetaData['namesnt']) )
                    update_user_meta($user_id, 'namesnt', $userMetaData['namesnt']);
                if ( isset($userMetaData['cadastral_num']) )
                    update_user_meta($user_id, 'cadastral_num', $userMetaData['cadastral_num']);
                if ( isset($userMetaData['address']) )
                    update_user_meta($user_id, 'address', $userMetaData['address']);


                $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

                $message = "Добро пожаловать! Ваши данные для входа :" . "\r\n</br>";
                $message .= sprintf(__('Имя пользователя: %s'), $username) . "\r\n</br>";
                $message .= sprintf(__('Пароль : %s'), $pass) . "\r\n</br>";
                $message .=  "<a href='https://gissnt.ru/login/'>Перейти в личный кабинет</a>\r\n</br>";
                wp_mail($email, sprintf(__('[%s] Ваше имя пользователя и пароль'), $blogname), $message);
            }
        }
    }
    return $cfdata;
}
add_action('wpcf7_before_send_mail', 'create_user_from_registration', 1);


