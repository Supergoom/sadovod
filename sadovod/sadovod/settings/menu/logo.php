<?php

/*  Список меню -----------------------------------------------*/

function setup_logo_menus()
{
    global $logo_menus;

    $logo_menus = array(
        'logo' => __('Logo', 'sadovod')
    );
}
add_action('after_setup_theme', 'setup_logo_menus');

/*  Добавление поля -----------------------------------------------*/
function add_nav_menu_logo_metabox()
{
    add_meta_box('logo', __('Logo', 'sadovod'), 'nav_menu_logo_metabox', 'nav-menus', 'side', 'default');
}
add_action('admin_head-nav-menus.php', 'add_nav_menu_logo_metabox');

function nav_menu_logo_metabox($object)
{
    global $nav_menu_selected_id;
    global $logo_menus;

    class LogoItem
    {
        public $title;
        public $db_id = 0;
        public $object_id = 'logo';
        public $menu_item_parent = 0;
        public $type = 'custom';
        public $object = 'custom';
        public $target = 'logo';
        public $url;
        public $attr_title = '';
        public $classes = array();
        public $xfn = '';
    }

    $elems_obj = array();

    foreach ($logo_menus as $slug => $name) {
        $elems_obj[$slug] = new LogoItem();
        $elems_obj[$slug]->target = $slug;
        $elems_obj[$slug]->title = $name;
    }

    $walker = new Walker_Nav_Menu_Checklist(array());

?>
    <div id="logo" class="logodiv">
        <div id="tabs-panel-logo-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
            <ul id="logochecklist" class="list:logo categorychecklist form-no-clear">
                <?php echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $elems_obj), 0, (object)array('walker' => $walker)); ?>
            </ul>
        </div>
        <p class="button-controls">
            <span class="add-to-menu">
                <input type="submit" <?php disabled($nav_menu_selected_id, 0); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu'); ?>" name="add-logo-menu-item" id="submit-logo">
                <span class="spinner"></span>
            </span>
        </p>
    </div>
<?php
}

function nav_menu_type_label_for_logo($menu_item)
{
    global $logo_menus;
    $targets = array_keys($logo_menus);

    if (isset($menu_item->type) && in_array($menu_item->type, $targets)) {
        $menu_item->type_label = __('Logo', 'sadovod');
    }

    return $menu_item;
}
add_filter('wp_setup_nav_menu_item', 'nav_menu_type_label_for_logo', 5);

function nav_menu_type_setup_for_logo($menu_item)
{
    global $logo_menus;
    $targets = array_keys($logo_menus);

    if (
        isset($menu_item->type, $menu_item->target) && $menu_item->type == 'custom' && !empty($menu_item->db_id)
        && in_array($menu_item->target, $targets)
    ) {
        $menu_item->type = $menu_item->target;
        $menu_item->type_label = __('Logo', 'sadovod');
    }

    return $menu_item;
}
add_filter('wp_setup_nav_menu_item', 'nav_menu_type_setup_for_logo', 0);

/*  Фронтенд -----------------------------------------------*/
function show_menu_logo($title, $menu_item)
{
    global $logo_menus;
    $targets = array_keys($logo_menus);

    if (isset($menu_item->type) && in_array($menu_item->type, $targets)) {
        return '<img class="site-logo lazyload" title="' . get_bloginfo('name') . '" alt="' . get_bloginfo('name') . '" src="' . get_logo_placeholder('custom_logo') . '" data-src="' . wp_get_attachment_image_url(get_theme_mod('custom_logo'), 'tiny') . '">';
    }
    return $title;
}
add_filter('nav_menu_item_title', 'show_menu_logo', 10, 2);

function show_menu_logo_attr($attr, $menu_item)
{
    global $logo_menus;
    $targets = array_keys($logo_menus);

    if (isset($menu_item->type) && in_array($menu_item->type, $targets)) {
        $attr['class'] = 'site-link';
        $attr['href'] = home_url('/');
    }
    return $attr;
}
add_filter('nav_menu_link_attributes', 'show_menu_logo_attr', 10, 2);

function show_menu_item_icon_logo($show, $menu_item)
{
    global $logo_menus;
    $targets = array_keys($logo_menus);

    if (isset($menu_item->type) && in_array($menu_item->type, $targets)) {
        return false;
    }
    return $show;
}
add_filter('sadovod_show_menu_item_icon_settings', 'show_menu_item_icon_logo', 10, 2);
