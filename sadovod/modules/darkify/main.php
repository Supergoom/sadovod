<?php

if (!defined('ABSPATH')) {
    exit;
}

define('DARKIFY_VERSION', '1.2.1');
define('DARKIFY_URL', get_stylesheet_directory_uri() . '/modules/darkify');

/**
 * Add styles to editor
 */
function darkify_editor_add_styles($mce_css)
{
    $theme_version = wp_get_theme()->get('Version');
    $version = is_string($theme_version) ? $theme_version : '1';

    $css = apply_filters('darkify_editor_css', DARKIFY_URL . '/assets/css/darkify-editor.css?v=' . $version);

    if (!empty($mce_css)) $css .= ',';
    $css .= $mce_css;

    return $css;
}
add_filter('mce_css', 'darkify_editor_add_styles');

/**
 * Add styles
 */
function darkify_dashboard_add_styles()
{
    /**
     * Check if dark mode is disable for the current user
     */
    if (wp_get_current_user()->darkify_dashboard != 1) {
        $darkify_dashboard_style = apply_filters('darkify_dashboard_css', DARKIFY_URL . '/assets/css/darkify-dashboard.css');
        wp_register_style('darkify-dashboard', $darkify_dashboard_style, array(), DARKIFY_VERSION);
        wp_enqueue_style('darkify-dashboard');
    }
}
add_action('admin_enqueue_scripts', 'darkify_dashboard_add_styles');

/**
 * Add field to user profile page
 */
function darkify_dashboard_user_profile_fields($user)
{ ?>
    <h3><?php _e("Dark Mode", "blank"); ?></h3>

    <table class="form-table">
        <tr>
            <th><label for="darkmode"><?php _e("Disable darkmode?"); ?></label></th>
            <td>
                <input type="checkbox" name="darkify_dashboard" id="darkmode" value="1" <?php checked($user->darkify_dashboard, true, true); ?>>
            </td>
        </tr>
    </table>
<?php }
add_action('show_user_profile', 'darkify_dashboard_user_profile_fields');
add_action('edit_user_profile', 'darkify_dashboard_user_profile_fields');

/**
 * Save data from user profile field to database
 */
function darkify_dashboard_save_user_profile_fields($user_id)
{
    if (empty($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $user_id)) {
        return;
    }

    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (empty($_POST['darkify_dashboard']))
        return false;

    update_user_meta($user_id, 'darkify_dashboard', $_POST['darkify_dashboard']);
}
add_action('personal_options_update', 'darkify_dashboard_save_user_profile_fields');
add_action('edit_user_profile_update', 'darkify_dashboard_save_user_profile_fields');
