<?php

/*  Правка iframe
-----------------------------------------------*/
function add_allow_same_origin($html)
{
    $html = preg_replace(array('/width=".*?"/', '/height=".*?"/'), array('width="100%"', 'height="230px"'), $html);
    $html = str_replace('display="none"', 'display="block"', $html);
    return str_replace('iframe', 'iframe allow-same-origin="true"', $html);
}
add_filter('embed_html', 'add_allow_same_origin');

/*  Правка размеров iframe
-----------------------------------------------*/
function change_iframe_size($cur)
{
    return array(
        'min' => 200,
        'max' => 900,
    );
}
add_filter('oembed_min_max_width', 'change_iframe_size');

/*  Доп стили
-----------------------------------------------*/
function print_additional_embed_styles()
{
    $type_attr = current_theme_supports('html5', 'style') ? '' : ' type="text/css"';
?>
    <style<?php echo $type_attr; ?>>
        .wp-embed{padding: 15px 20px 10px}
        .wp-embed-card{max-width: 650px; margin: auto;}
        .wp-embed-body{display:flex; align-items: center;}
        .wp-embed-body .wp-embed-featured-image{max-width: 30%; margin-right: 30px; margin-bottom: 0;}
        .wp-embed-footer{margin-top: 25px}

        @media screen and (max-width:600px) {
        .wp-embed-featured-image img{max-width: 100px;}
        }
        @media screen and (max-width:400px) {
        .wp-embed-body .wp-embed-heading{margin-bottom: 5px}
        .wp-embed-body .wp-embed-featured-image{display:none}
        }
        </style>
    <?php
}
add_action('embed_head', 'print_additional_embed_styles', 30);
