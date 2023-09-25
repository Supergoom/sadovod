<?php


function add_social_data()
{
    $url = get_site_url();
    $img = get_page_image();
    $jimg = get_page_image('jpg');

    $title = get_page_title();
    $description = get_page_description();

    echo '<meta name="twitter:card" content="summary_large_image">';
    echo '<meta name="twitter:image" content="' . $jimg . '">';
    echo '<meta name="twitter:url" content="' . $url . '">';
    echo '<meta name="twitter:title" content="' . $title . '">';
    echo '<meta name="twitter:description" content="' . $description . '">';
    echo '<meta name="twitter:site" content="@sadovod">';
    echo '<meta name="twitter:creator" content="@sadovod">';

    echo '<meta property="og:type" content="website">';
    echo '<meta property="og:image" content="' . $img . '">';
    echo '<meta property="og:url" content=" ' . $url . '">';
    echo '<meta property="og:title" content="' . $title . '">';
    echo '<meta property="og:description" content="' . $description . '">';
}
add_filter('wp_head', 'add_social_data');
add_filter('admin_head', 'add_social_data');

function add_additional_icons($meta_tags)
{
    $meta_tags = array();

    $svg_icon = wp_get_attachment_image_url(get_theme_mod('site_icon_svg', get_theme_mod('custom_logo')), 'full');

    $meta_tags[] = '<link rel="icon" href="' . $svg_icon . '" type="image/svg+xml">';
    $meta_tags[] = '<link rel="icon shortcut" href="' . $svg_icon . '" type="image/svg+xml">';
    $meta_tags[] = '<link rel="mask-icon" href="' . $svg_icon . '" color="#78d6ce">';

    $meta_tags[] = '<link rel="icon" href="' . get_site_icon(256, 'ico') . '" type="image/x-icon">';
    $meta_tags[] = '<link rel="icon alternate" href="' . get_site_icon(512, 'ico') . '" type="image/x-icon">';

    $meta_tags[] = '<link rel="apple-touch-icon-precomposed" sizes="256x256" href="' . get_site_icon_url(256) . '">';
    $meta_tags[] = '<link rel="apple-touch-icon-precomposed" sizes="152x152" href="' . get_site_icon_url(152) . '" >';
    $meta_tags[] = '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="' . get_site_icon_url(144) . '">';
    $meta_tags[] = '<link rel="apple-touch-icon-precomposed" sizes="120x120" href="' . get_site_icon_url(120) . '">';
    $meta_tags[] = '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="' . get_site_icon_url(114) . '">';
    $meta_tags[] = '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="' . get_site_icon_url(72) . '">';
    $meta_tags[] = '<link rel="apple-touch-icon-precomposed" sizes="64x64" href="' . get_site_icon_url(64) . '">';
    $meta_tags[] = '<link rel="apple-touch-icon-precomposed" sizes="57x57" href="' . get_site_icon_url(57) . '">';
    $meta_tags[] = '<link rel="apple-touch-icon-precomposed" sizes="32x32" href="' . get_site_icon_url(32) . '">';
    $meta_tags[] = '<link rel="apple-touch-icon-precomposed" sizes="16x16" href="' . get_site_icon_url(16) . '">';

    return $meta_tags;
}
add_filter('site_icon_meta_tags', 'add_additional_icons');


function get_site_icon_url_filter($url, $size)
{
    $icon = get_current_logo_type('site_icon');
    return wp_get_attachment_image_url(get_option($icon), $size);
}
add_filter('get_site_icon_url', 'get_site_icon_url_filter', 10, 3);
