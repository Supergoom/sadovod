<?php

function register_job_sidebar()
{
    register_sidebar(array(
        'id' => 'job-list-sidebar',
        'name' => __("Job Sidebar", "sadovod"),
        'description' => __("Shown on job page", "sadovod"),
        'before_title' => '<h4 class="sidebar-block-title">',
        'after_title' => '</h4>',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
    ));
}
add_action('widgets_init', 'register_job_sidebar');
