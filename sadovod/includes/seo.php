<?php

function get_current_title()
{
    $title = wp_title('|', false);
    if (get_query_var('taxonomy')) {
        $taxonomy = get_query_var('taxonomy');
        $term = get_query_var('term');
        if (!empty($taxonomy) && !empty($term)) {
            $term_data = get_term($term, $taxonomy);
            if ($term_data) {
                $custom_title = get_term_meta($term_data->ID, '_seo_title', true);
                if (!empty($custom_keywords))
                    $title = $custom_title;
            }
        }
    } else {
        $custom_title = get_post_meta(get_queried_object_id(), '_seo_title', true);
        if (!empty($custom_title))
            $title = $custom_title;
    }

    $personal = '';
    if (is_author(get_current_user_id())) {
        $personal = __('Personal Profile', 'sadovod') .  ' | ';
    }

    return $personal . trim($title, ' |')  . (!empty($title) ? ' | ' : '') . get_bloginfo('name') . ' — ' . get_bloginfo('description');
}

function get_current_description()
{
    global $post;

    $description = get_bloginfo('description');
    if (is_singular(array('post', 'projects', 'resources', 'jobs')) && isset($post)) {
        $description = get_the_excerpt($post->ID);
    }

    if (get_query_var('taxonomy')) {
        $taxonomy = get_query_var('taxonomy');
        $term = get_query_var('term');
        if (!empty($taxonomy) && !empty($term)) {
            $term_data = get_term($term, $taxonomy);
            if ($term_data) {
                $custom_description = get_term_meta($term_data->ID, '_seo_description', true);
                if (!empty($custom_keywords))
                    $description = $custom_description;
            }
        }
    } else {
        $custom_description = get_post_meta(get_queried_object_id(), '_seo_description', true);
        if (!empty($custom_description))
            $description = $custom_description;
    }

    return $description;
}

function get_current_keywords()
{
    $keywords = get_theme_mod('blogkeywords');
    if (get_query_var('taxonomy')) {
        $taxonomy = get_query_var('taxonomy');
        $term = get_query_var('term');
        if (!empty($taxonomy) && !empty($term)) {
            $term_data = get_term($term, $taxonomy);
            if ($term_data) {
                $custom_keywords = get_term_meta($term_data->ID, '_seo_keys', true);
                if (!empty($custom_keywords))
                    $keywords .= ', ' . $custom_keywords;
            }
        }
    } else {
        $custom_keywords = get_post_meta(get_queried_object_id(), '_seo_keys', true);
        if (!empty($custom_keywords))
            $keywords .= ', ' . $custom_keywords;
    }

    return $keywords;
}
