<?php

$args = [
    'post_type' => 'post',
    'orderby' => 'date',
    'order' => 'DESC',
    'post_status' => 'publish',
    'paged' => get_query_var('paged'),
    'posts_per_page' => 2,
    //'paged' => $current_page,
];

$post_query = new WP_Query($args);

if ($post_query->have_posts()) :
    while ($post_query->have_posts()) : $post_query->the_post();
        if (file_exists(get_template_directory() . '/layouts/card/main_page_blog.php')) {
            get_template_part('layouts/card/main_page_blog');
        } else {
            get_template_part('layouts/card/default');
        }

    endwhile;

else :
    get_template_part('layouts/card/no-result');
endif;


