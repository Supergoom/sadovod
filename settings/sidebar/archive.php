<?php

function register_archive_sidebar()
{
    register_sidebar(array(
        'id' => 'archive-sidebar',
        'name' => __("Archive Sidebar", "sadovod"),
        'description' => __("Shown on archive page", "sadovod"),
        'before_title' => '<h4 class="sidebar-block-title">',
        'after_title' => '</h4>',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
    ));
}
add_action('widgets_init', 'register_archive_sidebar');
