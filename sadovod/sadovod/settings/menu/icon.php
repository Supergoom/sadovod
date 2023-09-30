<?php

function menu_item_icon_settings($item_id, $item)
{
    if (!apply_filters('sadovod_show_menu_item_icon_settings', true, $item)) return;

    $menu_item_icon = get_post_meta($item_id, '_menu_item_icon', true);
    $menu_show_title = get_post_meta($item_id, '_menu_show_title', true);
?>
    <p class="field-icon description description-wide">
        <label for="edit-menu-item-title-<?php echo $item_id; ?>">
            <?php _e("Item Icon", "sadovod"); ?><br>
            <input type="text" id="edit-menu-item-icon-<?php echo $item_id; ?>" class="widefat edit-menu-item-icon" name="menu-item-icon[<?php echo $item_id; ?>]" value="<?php echo esc_attr($menu_item_icon); ?>">
        </label>
    </p>
    <p class="field-icon description description-wide">
        <input type="checkbox" id="edit-menu-item-show-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-show-title" name="menu-item-show-title[<?php echo $item_id; ?>]" <?php echo $menu_show_title ? 'checked' : ''; ?>>
        <label for="edit-menu-item-show-title-<?php echo $item_id; ?>">
            <?php _e("Don't Show Title", "sadovod"); ?><br>
        </label>
    </p>
<?php
}
add_action('wp_nav_menu_item_custom_fields', 'menu_item_icon_settings', 10, 2);

function save_menu_item_icon($menu_id, $menu_item_db_id)
{
    if (isset($_POST['menu-item-icon'][$menu_item_db_id])) {
        $sanitized_data = sanitize_text_field($_POST['menu-item-icon'][$menu_item_db_id]);
        update_post_meta($menu_item_db_id, '_menu_item_icon', $sanitized_data);
    } else {
        delete_post_meta($menu_item_db_id, '_menu_item_icon');
    }

    if (isset($_POST['menu-item-show-title'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_show_title', 1);
    } else {
        delete_post_meta($menu_item_db_id, '_menu_show_title');
    }
}
add_action('wp_update_nav_menu_item', 'save_menu_item_icon', 10, 2);

function show_menu_item_icon($title, $item)
{
    if (is_object($item) && isset($item->ID)) {
        $menu_item_icon = get_post_meta($item->ID, '_menu_item_icon', true);
        $menu_show_title = get_post_meta($item->ID, '_menu_show_title', true);
        if (!empty($menu_item_icon)) {
            $icon = '<i class="i-' . $menu_item_icon . '"></i>';
            if (empty($menu_show_title)) {
                $title = $icon . '<small>' . $title . '</small>';
            } else {
                $title = $icon . '<small hidden="hidden">' . $title . '</small>';
            }
        }
    }
    return $title;
}
add_filter('nav_menu_item_title', 'show_menu_item_icon', 10, 2);

function add_icon_class($atts, $item)
{
    if (is_object($item) && isset($item->ID)) {
        $menu_item_icon = get_post_meta($item->ID, '_menu_item_icon', true);
        if (!empty($menu_item_icon)) {
            $class = 'menu-item-icon';

            $atts['title'] = $item->title;
            $atts['class'] = isset($atts['class']) ? $atts['class'] . ' ' . $class : $class;
        }
    }
    return $atts;
}

add_action('nav_menu_link_attributes', 'add_icon_class', 10, 2);
