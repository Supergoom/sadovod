<?php
add_action('wp_ajax_blog_cat_filter', 'blog_cat_filter');
add_action('wp_ajax_nopriv_blog_cat_filter', 'blog_cat_filter');

function blog_cat_filter()
{
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 9,
        'paged' => sanitize_text_field($_POST['current_page']),
    );


	$category = sanitize_text_field($_POST['category'] ?? '');
    if (!empty($category)) {
        $args['category_name'] = $category;
    }

	$search = sanitize_text_field($_POST['search'] ?? '');
    if (!empty($search)) {
        $args['s'] = $search;
    }

    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) :
        ob_start();
        while ($wp_query->have_posts()) : $wp_query->the_post();
            get_template_part('layouts/card/blog');
        endwhile;
        $response['html'] = ob_get_contents();
        ob_end_clean();
    else :
        ob_start();
        echo '<div class="blog-post-none">Записей не найдено</div>';
        $response['html'] = ob_get_contents();
        ob_end_clean();
    endif;

    $response['max_pages'] = $wp_query->max_num_pages;
    echo json_encode($response);

    wp_die();
}
