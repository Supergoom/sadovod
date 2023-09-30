<?php

function menu_link_classes_settings($item_id, $item)
{
    if (!apply_filters('sadovod_show_menu_link_classes_settings', true, $item)) return;

    $menu_link_classes = get_post_meta($item_id, '_menu_link_classes', true);
?>
    <p class="field-link-classes description description-wide">
        <label for="edit-menu-item-title-<?php echo $item_id; ?>">
            <?php _e("Link classes", "sadovod"); ?><br>
            <input type="text" id="edit-menu-link-classes-<?php echo $item_id; ?>" class="widefat edit-menu-link-classes" name="menu-link-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr($menu_link_classes); ?>">
        </label>
    </p>
<?php
}
add_action('wp_nav_menu_item_custom_fields', 'menu_link_classes_settings', 10, 2);

function save_menu_link_classes($menu_id, $menu_item_db_id)
{
    if (isset($_POST['menu-link-classes'][$menu_item_db_id])) {
        $sanitized_data = sanitize_text_field($_POST['menu-link-classes'][$menu_item_db_id]);
        update_post_meta($menu_item_db_id, '_menu_link_classes', $sanitized_data);
    } else {
        delete_post_meta($menu_item_db_id, '_menu_link_classes');
    }
}
add_action('wp_update_nav_menu_item', 'save_menu_link_classes', 10, 2);

function add_link_classes($atts, $item)
{
    if (is_object($item) && isset($item->ID)) {
        $menu_link_classes = get_post_meta($item->ID, '_menu_link_classes', true);
        if (!empty($menu_link_classes)) {
            $atts['class'] = isset($atts['class']) ? $atts['class'] . ' ' . $menu_link_classes : $menu_link_classes;
        }
    }
    return $atts;
}
add_action('nav_menu_link_attributes', 'add_link_classes', 10, 2);
