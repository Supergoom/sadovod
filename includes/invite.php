<?php

function check_invite()
{
    if (!wp_using_themes())
        return;

    if (is_customize_preview()) {
        return;
    }

    $invite_base = '/invite/';
    if (!empty($_SERVER["REQUEST_URI"]) && strpos($_SERVER["REQUEST_URI"], $invite_base) === 0) {
        $invite = str_replace($invite_base, '', $_SERVER["REQUEST_URI"]);

        $len = strlen($invite);
        if ($len >= 6 && $len <= 32) {
            $channel = get_channel_by_invite($invite);
            if ($channel) {
                if (is_user_logged_in()) {
                    $user = get_current_user_id();

                    $_SESSION['invite_channel'] = $channel;
                    add_channel_members($channel->channel_id, array($user), false);

                    $url = get_author_posts_url($user);
                    wp_redirect($url, 301);
                    exit();
                } else {
                    $_SESSION['invite_channel_unauthed'] = $channel;

                    $url = get_home_url(null, null, 'https');
                    wp_redirect($url, 301);
                }
                return;
            }
        }
    }
}
add_action('parse_request', 'check_invite', 1);

function responce_invite()
{
    if (!wp_using_themes())
        return;

    if (is_customize_preview()) {
        return;
    }

    if (is_user_logged_in() && isset($_SESSION['invite_channel']) && !isset($_SESSION['invite_channel_unauthed'])) {
        $channel = $_SESSION['invite_channel'];

        wp_add_inline_script('main', '
            jQuery(function ($) {
                $(document).on("channelsLoaded", function (e, channels) {
                    $("#messageModal").modal("show");
                    $(".channel-block[data-id=\"' . $channel->channel_id . '\"]").trigger("click");
                });
            });
        ', 'after');

        unset($_SESSION['invite_channel']);
    }

    if (isset($_SESSION['invite_channel_unauthed'])) {
        $channel = $_SESSION['invite_channel'];

        $note = sprintf(
            __('To join channel `%s` you need to log in to your account!', 'sadovod-dialogs'),
            $channel->channel_name
        );

        wp_add_inline_script('main', '
            jQuery(function ($) {
                $("#loginForm").html("<h4>' . $note . '</h4>").fadeIn()
                $("#loginForm").modal("show");
            });
        ', 'after');

        $_SESSION['invite_channel'] = $channel;
        unset($_SESSION['invite_channel_unauthed']);
    }
}
add_action('wp_enqueue_scripts', 'responce_invite', 20);
