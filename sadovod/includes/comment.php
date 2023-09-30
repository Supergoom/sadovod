<?php


/*  Рейтинг
-----------------------------------------------*/

function get_comment_rating($comment = null)
{
    if (!is_numeric($comment)) {
        $comment = get_comment_ID();
    }

    $rating = get_comment_meta($comment, 'rating', true);

    return floatval($rating);
}

function the_comment_rating($comment = null, $return = false)
{
    $rating = get_comment_rating($comment);

    $full_stars = $rating;
    $empty_stars = 5 - $rating;

    $schema = '<div itemprop="reviewRating" itemtype="http://schema.org/Rating" itemscope>';
    $schema .= '<meta itemprop="ratingValue" content="' . $rating . '">';
    $schema .= '<meta itemprop="bestRating" content="5">';
    $schema .= '</div>';

    $output = '<span class="single-rating-stars">';
    $output .= str_repeat('<i class="i-star-4"></i>', $full_stars);
    $output .= str_repeat('<i class="i-star"></i>', $empty_stars);
    $output .= '</span>';

    $output = apply_filters('the_comment_rating', $schema . $output);

    if ($return) {
        return $output;
    } else {
        echo $output;
    }
}

function get_the_reviews($post = null)
{
    if (!isset($post)) {
        global $post;
    }

    $reviews = '<i class="i-chat"></i><span>' . get_formated_number(parse_int(get_comments_number($post->ID))) . '</span>';

    return apply_filters('get_the_reviews', $reviews, $post);
}

function the_reviews()
{
    echo apply_filters('the_reviews', get_the_reviews());
}
