<?php

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

