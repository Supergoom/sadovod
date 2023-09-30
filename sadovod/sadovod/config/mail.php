<?php

add_filter('sanitize_option_new_admin_email', 'sanitize_multiple_emails', 10, 3);
add_filter('sanitize_option_admin_email', 'sanitize_multiple_emails', 10, 3);

function sanitize_multiple_emails($value, $option, $original_value)
{
    $result = array();
    $emails = explode(',', $original_value);

    foreach ($emails as $email) {
        $email = trim($email);
        $email = sanitize_email($email);

        if (!is_email($email))
            return $value;

        $result[] = $email;
    }

    if (!count($result))
        return $value;

    return implode(', ', $result);
}

/*  Авторство -----------------------------------------------*/

add_filter('wp_mail_from_name', function ($from_name) {
    return get_bloginfo('name');
});

add_filter('wp_mail_from', function ($from_email) {
    return 'no-reply@' . get_site_root_domain();
});

add_filter('wp_mail', function ($args) {
    $domain = get_site_root_domain();

    if (!is_array($args['headers'])) {
        $args['headers'] = explode("\n", str_replace("\r\n", "\n", $args['headers']));
    } else {
        $args['headers'] = $args['headers'];
    }

    $args['headers'][] = 'Reply-To: support@' . $domain;

    return $args;
});

function add_supprot_email_receiver($value)
{
    $domain = get_site_root_domain();
    return $value . ', support@' . $domain;
}
add_filter('option_admin_email', 'add_supprot_email_receiver');


/*  Тип контента -----------------------------------------------*/

add_filter('wp_mail_content_type', function ($from_name) {
    return "text/html";
});

/*  Сообщение -----------------------------------------------*/

function get_mail_styles()
{
    return '<style>
		:root{--color-bright: #fff;--color-dark: #000; --color-foreground: #183037; --color-background: #e5f4f4; --color-primary: #244b5b; --color-secondary: #2fc2b5;
		--color-tertiary: #34ae9c; --color-alert: #d16867; --color-warning: #78d6ce; --color-notif: #244b5b; --color-info: #738891; --default-border-radius: 5px; --big-border-radius: 15px;
		--box-shadow-color: 0, 0, 0, .15; --box-shadow: 0px 0px 10px rgba(var(--box-shadow-color)); --box-shadow-hover-color: 0, 0, 0, .35;
		--box-shadow-hover: 0px 0px 15px rgba(var(--box-shadow-hover-color));}
	
		html, body{width: 100%; position: relative; background: var(--color-background)}
	
		h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6{ margin-top: 0; margin-bottom: .5rem; font-weight: 500; line-height: 1.2;}
		h1{margin-bottom: 15px;}
		
		p {margin-top: 0; margin-bottom: 1rem;}
	</style>';
}

/*  Уведомление о регистрации -----------------------------------------------*/
remove_action('register_new_user', 'wp_send_new_user_notifications');

/*  Уведомление о пароле -----------------------------------------------*/
function password_change_notification($user, $new_pass)
{
    if (false === strpos(get_option('admin_email'), $user->user_email)) {
        $site_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
        if (is_multisite())
            $site_name = get_network()->site_name;

        /*  ----------------- Замена ------------------------------*/
        $replacments = array(
            '/{user_name}/' => $user->display_name,
            '/{user_email}/' => $user->user_email,
            '/{user_login}/' => $user->nickname,
            '/{user_pass}/' => $new_pass,
            '/{site_name}/' => $site_name,
            '/{login_url}/' => home_url(),
        );

        /*  ----------------- Пользователю ------------------------------*/

        $message = get_mail_styles();

        $message .= __(
            '<h1>Hello, {user_name}.</h1>
<p>Your password on site "{site_name}" have been changed.<br>
If you not initialized this action, please, immediately change your password.</p>

<p>You can <a href="{login_url}" target="_blank">log in</a> using username and password specified below.<br>
Username: {user_login}<br>
Password: {user_pass}</p>

<p>This letter is generated automatically and does not imply a response.<br>
Please, don’t answer it.</p>',
            'sadovod-mail'
        );

        $subject = sprintf(
            __('[%s] Password Successfully Changed', 'sadovod-mail'),
            $site_name
        );


        $message = preg_replace(array_keys($replacments), array_values($replacments), $message);

        wp_mail($user->user_email, $subject, $message, array('content-type: text/html'));

        /*  ----------------- Админу ------------------------------*/

        $message = get_mail_styles();

        $message .= __(
            '<h1>Hello</h1>
<p>User changed password on your site "{site_name}".</p>

<p>Full Name: {user_name}<br>
Username: {user_login}<br>
Email: {user_email}</p>

<p>This letter is generated automatically and does not imply a response.<br>
Please don’t answer it.</p>',
            'sadovod-mail'
        );

        $subject = sprintf(
            __('[%s] Password Changed', 'sadovod-mail'),
            $site_name
        );

        $message = preg_replace(array_keys($replacments), array_values($replacments), $message);

        wp_mail(get_option('admin_email'), $subject, $message, array('content-type: text/html'));
    }
}

remove_action('after_password_reset', 'wp_password_change_notification');
add_action('after_password_reset', 'password_change_notification', 10, 2);
