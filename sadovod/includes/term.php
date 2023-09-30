<?php

/*  Получить ID прикрепленной картинки
-----------------------------------------------*/
function get_term_image_id($term)
{
    return (int) get_term_meta(is_object($term) ? $term->term_id : $term, '_thumbnail_id', true);
}

/*  Получить прикрепленную картинку
-----------------------------------------------*/
function get_term_image_url($term, $size = 'thumbnail')
{
    return wp_get_attachment_image_url(get_term_image_id($term), $size);
}

function get_term_seo($term)
{
    $term = is_object($term) ? $term->term_id : $term;

    return array(
        'title' => get_term_meta($term, '_seo_title', true),
        'keys' => get_term_meta($term, '_seo_keys', true),
        'description' => get_term_meta($term, '_seo_description', true)
    );
}
