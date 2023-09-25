<?php

/*  Список меню -----------------------------------------------*/

function setup_dropdown_menus()
{
    global $dropdown_menus;

    $dropdown_menus = array(
        'dropdown' => __('Dropdown', 'sadovod'),
    );
}
add_action('after_setup_theme', 'setup_dropdown_menus');

/*  Добавление поля -----------------------------------------------*/
function add_nav_menu_dropdown_metabox()
{
    add_meta_box('dropdown', __('Dropdown', 'sadovod'), 'nav_menu_dropdown_metabox', 'nav-menus', 'side', 'default');
}
add_action('admin_head-nav-menus.php', 'add_nav_menu_dropdown_metabox');

function nav_menu_dropdown_metabox($object)
{
    global $nav_menu_selected_id;
    global $dropdown_menus;

    class DropdownItem
    {
        public $title;
        public $db_id = 0;
        public $object_id = 'dropdown';
        public $menu_item_parent = 0;
        public $type = 'custom';
        public $object = 'custom';
        public $target = 'dropdown';
        public $url;
        public $attr_title = '';
        public $classes = array();
        public $xfn = '';
    }

    $elems_obj = array();

    foreach ($dropdown_menus as $slug => $name) {
        $elems_obj[$slug] = new DropdownItem();
        $elems_obj[$slug]->target = $slug;
        $elems_obj[$slug]->title = $name;
    }

    $walker = new Walker_Nav_Menu_Checklist(array());

?>
    <div id="dropdown" class="dropdowndiv">
        <div id="tabs-panel-dropdown-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
            <ul id="dropdownchecklist" class="list:dropdown categorychecklist form-no-clear">
                <?php echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $elems_obj), 0, (object)array('walker' => $walker)); ?>
            </ul>
        </div>
        <p class="button-controls">
            <span class="add-to-menu">
                <input type="submit" <?php disabled($nav_menu_selected_id, 0); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-dropdown-menu-item" id="submit-dropdown">
                <span class="spinner"></span>
            </span>
        </p>
    </div>
<?php
}

function menu_item_dropdown_settings($item_id, $menu_item)
{
    global $dropdown_menus;
    $targets = array_keys($dropdown_menus);

    $show = false;
    if (isset($menu_item->type) && in_array($menu_item->type, $targets)) {
        $show = true;
    }

    if (!apply_filters('sadovod_show_menu_dropdown_settings', $show, $menu_item)) return;

    $menu_item_dropdown_url = get_post_meta($item_id, '_menu_item_dropdown_url', true);
    $menu_item_dropdown_centered = get_post_meta($item_id, '_menu_item_dropdown_centered', true);
    $menu_item_dropdown_horizontal = get_post_meta($item_id, '_menu_item_dropdown_horizontal', true);
?>
    <p class="field-dropdown-centered description description-wide">
        <label for="edit-menu-item-dropdown-centered-<?php echo $item_id; ?>">
            <?php _e('URL'); ?><br>
        </label>
        <input id="custom-menu-item-url" name="menu-item-dropdown-url[<?php echo $item_id; ?>]" type="text" class="widefat edit-menu-item-url" placeholder="https://" value="<?= $menu_item_dropdown_url ?>" />
    </p>

    <p class="field-dropdown-centered description description-wide">
        <input type="checkbox" id="edit-menu-item-dropdown-centered-<?php echo $item_id; ?>" class="widefat edit-menu-item-dropdown-centered" name="menu-item-dropdown-centered[<?php echo $item_id; ?>]" <?php echo $menu_item_dropdown_centered ? 'checked' : ''; ?>>
        <label for="edit-menu-item-dropdown-centered-<?php echo $item_id; ?>">
            <?php _e("Center dropdown", "sadovod"); ?><br>
        </label>
    </p>
    <p class="field-dropdown-horizontal description description-wide">
        <input type="checkbox" id="edit-menu-item-dropdown-horizontal-<?php echo $item_id; ?>" class="widefat edit-menu-item-dropdown-horizontal" name="menu-item-dropdown-horizontal[<?php echo $item_id; ?>]" <?php echo $menu_item_dropdown_horizontal ? 'checked' : ''; ?>>
        <label for="edit-menu-item-dropdown-horizontal-<?php echo $item_id; ?>">
            <?php _e("Make dropdown horizontal", "sadovod"); ?><br>
        </label>
    </p>
<?php
}
add_action('wp_nav_menu_item_custom_fields', 'menu_item_dropdown_settings', 10, 2);

function save_menu_item_dropdown_settings($menu_id, $menu_item_db_id)
{
    if (isset($_POST['menu-item-dropdown-url'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_item_dropdown_url', sanitize_url($_POST['menu-item-dropdown-url'][$menu_item_db_id]));
    } else {
        delete_post_meta($menu_item_db_id, '_menu_item_dropdown_url');
    }

    if (isset($_POST['menu-item-dropdown-centered'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_item_dropdown_centered', 1);
    } else {
        delete_post_meta($menu_item_db_id, '_menu_item_dropdown_centered');
    }

    if (isset($_POST['menu-item-dropdown-horizontal'][$menu_item_db_id])) {
        update_post_meta($menu_item_db_id, '_menu_item_dropdown_horizontal', 1);
    } else {
        delete_post_meta($menu_item_db_id, '_menu_item_dropdown_horizontal');
    }
}
add_action('wp_update_nav_menu_item', 'save_menu_item_dropdown_settings', 10, 2);

function nav_menu_type_label_for_dropdown($menu_item)
{
    global $dropdown_menus;
    $targets = array_keys($dropdown_menus);

    if (isset($menu_item->type) && in_array($menu_item->type, $targets)) {
        $menu_item->type_label = __('Dropdown', 'sadovod');
    }

    return $menu_item;
}
add_filter('wp_setup_nav_menu_item', 'nav_menu_type_label_for_dropdown', 5);

function nav_menu_type_setup_for_dropdown($menu_item)
{
    global $dropdown_menus;
    $targets = array_keys($dropdown_menus);

    if (
        isset($menu_item->type, $menu_item->target) && $menu_item->type == 'custom' && !empty($menu_item->db_id)
        && in_array($menu_item->target, $targets)
    ) {
        $menu_item->type = $menu_item->target;
        $menu_item->type_label = __('Dropdown', 'sadovod');
    }

    return $menu_item;
}
add_filter('wp_setup_nav_menu_item', 'nav_menu_type_setup_for_dropdown', 0);

/*  Фронтенд -----------------------------------------------*/
function setup_menu_item_dropdown_class($classes, $menu_item)
{
    global $dropdown_menus;
    $targets = array_keys($dropdown_menus);

    if (isset($menu_item->type) && in_array($menu_item->type, $targets)) {
        if (get_post_meta($menu_item->ID, '_menu_item_dropdown_centered', true) === '1')
            $classes[] = 'dropdown-center';

        if (get_post_meta($menu_item->ID, '_menu_item_dropdown_horizontal', true) === '1')
            $classes[] = 'dropdown-horizontal';
    }

    return $classes;
}
add_filter('nav_menu_css_class', 'setup_menu_item_dropdown_class', 10, 2);

function show_menu_dropdown_title($attr, $menu_item)
{
    global $dropdown_menus;
    $targets = array_keys($dropdown_menus);

    if (isset($menu_item->type) && in_array($menu_item->type, $targets)) {

        $url = get_post_meta($menu_item->ID, '_menu_item_dropdown_url', true);
        if (!empty($url)) { 
            $attr['href'] = $url;
        }else{
            $attr['data-bs-toggle'] = 'dropdown';
            $attr['data-bs-offset'] = '0,7';
            //$attr['data-bs-auto-close'] = 'outside';

            $attr['class'] = ($attr['class'] ?? '') . ' dropdown-toggle';
            $attr['href'] = '#';
        }
    }
    return $attr;
}
add_filter('nav_menu_link_attributes', 'show_menu_dropdown_title', 10, 2);

function add_menu_dropdown($item_output, $menu_item)
{
    $url = get_post_meta($menu_item->ID, '_menu_item_dropdown_url', true);
    if (!empty($url)) { 
        return $item_output .
            '<a class="menu-item-link dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" data-bs-reference="parent" data-bs-offset="10,7"></a>';
    }

    return $item_output;
}
add_action('walker_nav_menu_start_el', 'add_menu_dropdown', 10, 2);

function add_dropdown_nav_class($classes, $args)
{
    $classes[] = 'dropdown-menu';
    return $classes;
}
add_action('nav_menu_submenu_css_class', 'add_dropdown_nav_class', 10, 2);
