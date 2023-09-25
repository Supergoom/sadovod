<?php

function register_project_sidebar()
{
    register_sidebar(array(
        'id' => 'project-list-sidebar',
        'name' => __("Project Sidebar", "sadovod"),
        'description' => __("Shown on project page", "sadovod"),
        'before_title' => '<h4 class="sidebar-block-title">',
        'after_title' => '</h4>',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
    ));
}
add_action('widgets_init', 'register_project_sidebar');
