<?php

function register_user_sidebar()
{
    register_sidebar(array(
        'id' => 'user-list-sidebar',
        'name' => __("Users Sidebar", "sadovod"),
        'description' => __("Shown on users page", "sadovod"),
        'before_title' => '<h4 class="sidebar-block-title">',
        'after_title' => '</h4>',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
    ));
}
add_action('widgets_init', 'register_user_sidebar');
