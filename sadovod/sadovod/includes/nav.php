<?php

/*  Меню
-----------------------------------------------*/
add_theme_support('menus');

register_nav_menus(array(
    'header'              => __('Header menu', 'sadovod-records'),
    'footer'              => __('Footer menu', 'sadovod-records')
));

function outputMenu($name, $class = '')
{

    if (has_nav_menu($name)) {
        wp_nav_menu(array(
            'theme_location'    => $name,
            'menu'              => $name,
            'container'         => 'nav',
            'container_class'   => 'menu-wrap ' . $class,
            'container_id'      => $name . '-menu',
            'echo'              => true,
            'depth'             => 2,
            'items_wrap'        => '<ul id="%1$s" class="%2$s" itemscope="itemscope" itemtype="http://www.schema.org/SiteNavigationElement">' .
                /**/ '%3$s' .
                '</ul>',
            'link_before'       => '<span itemprop="name">',
            'link_after'        => '</span>'
        ));
    }
}

function reset_menu_position()
{
    global $wp_menu_index;
    $wp_menu_index = 0;
}
add_action('pre_wp_nav_menu', 'reset_menu_position', 5);

function add_menu_position($item_output)
{
    global $wp_menu_index;
    return str_replace('</a>', '<meta itemprop="position" content="' . ($wp_menu_index++) . '"></a>', $item_output);
}
add_action('walker_nav_menu_start_el', 'add_menu_position', 5);


function add_menu_item_shema_class($atts)
{
    if (empty($atts['href']))
        $atts['href'] = '/';

    $atts['itemprop'] = 'url';

    return $atts;
}
add_action('nav_menu_link_attributes', 'add_menu_item_shema_class', 5);

function add_menu_item_nav_class($atts)
{
    $class = 'menu-item-link';

    $atts['class'] = isset($atts['class']) ? $atts['class'] . ' ' . $class : $class;

    return $atts;
}
add_action('nav_menu_link_attributes', 'add_menu_item_nav_class', 5);

function add_current_nav_class($classes, $item)
{
    global $post;

    if (!$post or !isset($post->post_name)) return $classes;
    if (empty($item->url)) return $classes;

    $uri = parse_url(strval(strtolower(trim($item->url))));

    $url = $uri['host'] ?? '';
    if (isset($uri['path']))
        $url = trim($uri['path'], '/');

    if (strpos($url, strval($post->post_name)) !== false) {
        $classes[] = 'current-menu-item';
        return $classes;
    }
    if (strpos($url, strval($post->post_type)) !== false) {
        $classes[] = 'current-menu-item';
        return $classes;
    }

    global $all_posts;

    if ($object = get_queried_object()) {
        $post_type = $object->post_type;
        if (is_tax() or is_category() or is_tag()) {
            $post_type = get_taxonomy($object->taxonomy)->object_type[0];
        }

        if (
            isset($all_posts[$post_type]->rewrite) and isset($all_posts[$post_type]->rewrite['slug'])
            and strpos($all_posts[$post_type]->rewrite['slug'], $url) !== false
        ) {
            $classes[] = 'current-menu-item';
            return $classes;
        }
    }

    return $classes;
}

add_action('nav_menu_css_class', 'add_current_nav_class', 10, 2);
