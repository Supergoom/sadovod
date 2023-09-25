<?php

/*  Режим разработки -----------------------------------------------*/

function devmode()
{
    if (get_theme_mod('devmode_enabled') && isset($_GET['mode']) && $_GET['mode'] == 'default') {
        unset($_SESSION["preview"]);
    }

    if (!get_theme_mod('devmode_enabled') || is_login_page() || isset($_SESSION["preview"])) {
        return false;
    }

    if (isset($_GET['mode']) && $_GET['mode'] == 'preview') {
        $_SESSION["preview"] = 1;
        return false;
    }

    if (!(current_user_can('edit_posts'))) {
        wp_die(get_devmode_content(), get_devmode_title(), ['response' => get_theme_mod('devmode_code')]);
    }
}

add_action('init', 'devmode');


function get_devmode_title()
{
    return get_bloginfo('name') . ' ' . wp_title('|', false) . ' — ' . get_bloginfo('description');
}

function get_devmode_content()
{
    $style = '<style>
		div{display: -webkit-box; display: -moz-box; display: -ms-flexbox; display: -webkit-flex; display: flex; -webkit-box-align: center;
		-webkit-flex-align: center; -ms-flex-align: center; -webkit-align-items: center; align-items: center; height: 500px; height: 100vh; max-height: 100%;
		text-align: center;}
	
	</style>';

    $content = '<div>
		<symbol id="icon-lock" viewBox="0 0 32 32">
			<path d="M16.5 2.669c3.893 0 7.064 3.171 7.064 7.068l-0 2.102c2.367 0.824 4.070 3.077 4.070 5.722v5.719c0 3.34-2.716 6.056-6.056 6.056h-10.188c-3.34 0-6.056-2.716-6.056-6.056v-5.719c0-2.644 1.702-4.897 4.068-5.721l0.001-2.103c0.008-1.92 0.751-3.692 2.091-5.020 1.341-1.329 3.112-2.099 5.007-2.048zM21.577 13.505h-10.188c-2.237 0-4.056 1.819-4.056 4.056v5.719c0 2.237 1.819 4.056 4.056 4.056h10.188c2.236 0 4.056-1.819 4.056-4.056v-5.719c0-2.237-1.82-4.056-4.056-4.056zM16.483 17.939c0.552 0 1 0.448 1 1v2.961c0 0.552-0.448 1-1 1s-1-0.448-1-1v-2.961c0-0.552 0.448-1 1-1zM16.496 4.669h-0.021c-1.351 0-2.616 0.52-3.572 1.468-0.963 0.952-1.495 2.223-1.5 3.577l-0.001 1.79h10.161l0.001-1.767c0-2.795-2.273-5.068-5.068-5.068z"></path>
		</symbol>
		<h1>Сайт в разработке</h1>
		<p>Пожалуйста, задите позже!</p>
	</div>';


    return $style . $content;
}

/*  Пояснения режима разработки -----------------------------------------------*/

function setup_devmode_details()
{
    global $devmode_details;

    if (isset($devmode_details) && is_array($devmode_details)) {
        $reset_args = apply_filters('devmode_clear_args', array());

        echo '<a class="devmode-details" href="' . get_permalink() . '?' . http_build_query($reset_args) . '">';
        foreach ($devmode_details as $detail) {
            echo '<div class="devmode-detail">';
            echo $detail;
            echo '</div>';
        }
        echo '</a>';
    }
}
add_action('wp_body_open', 'setup_devmode_details');

function add_devmode_detail($detail)
{
    global $devmode_details;

    if (!is_array($devmode_details))
        $devmode_details = array();

    if (is_numeric($detail) || is_string($detail))
        array_push($devmode_details, $detail);
}

/*  Очистка после выхода -----------------------------------------------*/
function clear_devmode_on_logout()
{
    unset($_SESSION['preview']);
}
add_action('wp_logout', 'clear_devmode_on_logout');
