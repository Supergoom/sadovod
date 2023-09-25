<?php

/*  ----------------- Авторизация ------------------------------*/

add_action('wp_ajax_user_message', 'user_message');

function user_message()
{
    check_ajax_referer('contact-action', 'contact-nonce');

    $out = array();

    $user_name = sanitize_text_field($_POST['name']);
    $user_email = sanitize_email($_POST['email']);
    $user_message = sanitize_text_field($_POST['message']);

    if (empty($user_name)) {
        $out['error'] = array(
            'field' => 'name',
            'text' => __('Name is incorrect.', 'sadovod-dialogs')
        );
    } else if (empty($user_email)) {
        $out['error'] = array(
            'field' => 'email',
            'text' => __('E-mail incorrect.', 'sadovod-dialogs')
        );
    } else if (empty($user_message)) {
        $out['error'] = array(
            'field' => 'message',
            'text' => __('Message incorrect.', 'sadovod-dialogs')
        );
    } else {

        $captcha = $_POST['recaptcha'] ?? '';
        $response = json_decode(file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret="
                . get_theme_mod('captcha_secret_key')
                . "&response=" . $captcha
                . "&remoteip=" . $_SERVER['REMOTE_ADDR']
        ), true);

        if ($response['success'] == true && $response['action'] == ($_POST['action'] ?? '')) {
            if ($response['score'] > 0.3) {

                //------------------------------------------------

                require_once ABSPATH . 'wp-admin/includes/image.php';
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';

                $uploads = array();
                $files = $_FILES['files'] ?? false;
                if ($files) {

                    $allowed_file_types = array(
                        '.txt',
                        '.pdf',
                        '.xlsx',
                        '.xls',
                        '.doc',
                        '.docx',
                        '.zip',
                        '.rar',
                        'video/*',
                        'image/*',
                        'audio/*'
                    );

                    foreach ($files['name'] as $index => $filename) {
                        $match = true;

                        $tmp = $files['tmp_name'][$index];
                        $size = $files['size'][$index];
                        $ext  = pathinfo($filename, PATHINFO_EXTENSION);
                        $mime = mime_content_type($tmp);

                        if (!in_array('.' . $ext, $allowed_file_types))
                            $match = false;

                        if (!$match) {
                            foreach ($allowed_file_types as $type) {
                                $pattern = str_replace('\*', '.*?', preg_quote($type, '/'));
                                echo "\n";
                                if (preg_match('/' . $pattern . '$/', $mime)) {
                                    $match = true;
                                    break;
                                }
                            }
                        }


                        if (!$match) {
                            $out['error'] = array(
                                'block' => '.form-attachment-wrap[data-file="' . $filename . '"]',
                                'text' => sprintf(
                                    __('File %s not allowed to upload.', 'sadovod-dialogs'),
                                    $filename
                                )
                            );
                        } else {
                            $type = current(explode('/', $mime, 1));
                            $sizes = get_file_size_limits();
                            $max_size = isset($sizes[$type]) ? $sizes[$type] : ($sizes['document'] ?? 5);

                            $max_size = $max_size * 1024 * 1024;
                            if ($size > $max_size) {
                                $out['error'] = array(
                                    'block' => '.form-attachment-wrap[data-file="' . $filename . '"]',
                                    'text' => sprintf(
                                        __('File %s has exceeded allowed file size of %s.', 'sadovod-dialogs'),
                                        $filename,
                                        size_format($max_size)
                                    )
                                );
                            }
                        }
                    }

                    if (empty($out)) {
                        $overrides = array(
                            'test_form' => false
                        );

                        foreach ($files['name'] as $key => $value) {
                            $file = array(
                                'name'     => $value,
                                'type'     => $files['type'][$key],
                                'tmp_name' => $files['tmp_name'][$key],
                                'error'    => $files['error'][$key],
                                'size'     => $files['size'][$key]
                            );

                            $uploaded = wp_handle_upload($file, $overrides);
                            if (!$uploaded || isset($uploaded['error'])) {
                                $out['error'] = array(
                                    'note' => true,
                                    'text' => '<h4>' . __('Error uploading files!', 'sadovod-dialogs') . '</h4>' .
                                        '<span>' . __('Please, try later.', 'sadovod-dialogs') . '</span>',
                                );
                                break;
                            } else {
                                $uploads[] = $uploaded;
                            }
                        }
                    }
                }

                if (empty($out)) {
                    //------------------------------------------------
                    $site_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
                    if (is_multisite())
                        $site_name = get_network()->site_name;

                    $replacments = array(
                        '/{user_name}/'         => $user_name,
                        '/{user_email}/'        => $user_email,
                        '/{user_message}/'      => $user_message,
                        '/{site_name}/'         => $site_name,
                        '/{attached_files}/'    => __('No attached files', 'sadovod-mail')
                    );

                    if (!empty($uploads)) {
                        $attachemnts = &$replacments['/{attached_files}/'];
                        $attachemnts = '';

                        foreach ($uploads as $file) {
                            $attachemnts .= '<br><a href="' . $file['url'] . '" target="_blank">' . basename($file['url']) . '</a>';
                        }
                    }

                    $message = get_mail_styles();

                    $message .= __(
                        '<h1>Hello</h1>
<p>New user feedback on your site "{site_name}".</p>

<p>User Name: {user_name}<br>
Email: {user_email}<br>
Message: {user_message}</p>
<p>Attached files:{attached_files}</p>

<p>This letter is generated automatically and does not imply a response.<br>
Please, don’t answer it.</p>',
                        'sadovod-mail'
                    );

                    $subject = sprintf(
                        __('[%s] New Feedback', 'sadovod-mail'),
                        $site_name
                    );

                    $message = preg_replace(array_keys($replacments), array_values($replacments), $message);

                    add_filter('wp_mail_from_name', function ($from_name) use ($user_name) {
                        return $user_name;
                    }, 20);

                    add_filter('wp_mail_from', function ($from_email) use ($user_email) {
                        return $user_email;
                    }, 20);

                    if (!wp_mail(get_option('admin_email'), $subject, $message, array('content-type: text/html'))) {
                        $out['error'] = array(
                            'note' => true,
                            'text' => __('The email could not be sent. Please try later.', 'sadovod-dialogs')
                        );
                    } else {
                        $delay = time() + HOUR_IN_SECONDS * rand(0, 3) + MINUTE_IN_SECONDS * rand(15, 59);
                        wp_schedule_single_event($delay, 'notify_user_feedback_message_received', array($user_name, $user_email));

                        $out['success'] = array(
                            'text' => '<h4>' . __('Your message succesfuly send!', 'sadovod-dialogs') . '</h4>' .
                                '<span>' . __('We will review it, as soon as we can.', 'sadovod-dialogs') . '</span>',
                        );
                    }
                }
            } else {
                $_SESSION['last_request'] = microtime(true) + get_theme_mod('penalty_time');

                $out['error'] = array(
                    'note' => true,
                    'text' => '<h4>' . __('We sorry, but you looks like a robot!', 'sadovod-dialogs') . '</h4>' .
                        '<span>' . __('Please, try again later.', 'sadovod-dialogs') . '</span>'
                );
            }
        } else {
            $out['error'] = array(
                'note' => true,
                'text' => '<h4>' . __('Error verifying captcha response!', 'sadovod-dialogs') . '</h4>' .
                    '<span>' . __('Please, try again.', 'sadovod-dialogs') . '</span>'
            );
        }
    }


    echo (json_encode($out));
    wp_die();
}

function notify_user_feedback_message_received($user_name, $user_email)
{
    $site_domain = get_site_root_domain();
    $site_name = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    if (is_multisite())
        $site_name = get_network()->site_name;

    $replacments = array(
        '/{user_name}/'     => $user_name,
        '/{user_email}/'    => $user_email,
        '/{site_name}/'     => $site_name,
        '/{site_domain}/'   => $site_domain
    );

    $message = get_mail_styles();

    $message .= __(
        '<h1>Hello, {user_name}</h1>
<p>Thank you, for your feedback on site "{site_name}".</p>

<p>We will review your message as quick as possible.</p>
<p>Feel free to send additional info to <a href="mailto:support@{site_domain}">support@{site_domain}</a></p>

<p>This letter is generated automatically and does not imply a response.<br>
Please, don’t answer it.</p>',
        'sadovod-mail'
    );

    $subject = sprintf(
        __('[%s] Site Feedback', 'sadovod-mail'),
        $site_name
    );

    $message = preg_replace(array_keys($replacments), array_values($replacments), $message);

    if (!wp_mail($user_email, $subject, $message, array('content-type: text/html'))) {
        return false;
    } else {
        $delay = time() + HOUR_IN_SECONDS * rand(0, 1) + MINUTE_IN_SECONDS * rand(15, 30);
        wp_schedule_single_event($delay, 'notify_user_feedback_message_received', array($user_name, $user_email));
    }
}
