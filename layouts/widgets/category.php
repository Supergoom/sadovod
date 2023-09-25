<?php

global $post_query;

if (isset($post_query)) :

    $catlist = array();

    if (!is_tax()) {
        $tax = get_object_taxonomies($post_query->query['post_type'])[1];

        $args = array(
            'taxonomy' => $tax,
            'parent' => 0,
            'hide_empty' => 1,
            'number' => 12,
            'orderby' => 'name',
            'order' => 'DESC'
        );

        $catlist = get_terms($args);
    } else {
        $catlist = get_term_children(get_query_var('term'), get_query_var('taxonomy'));
    }

    if (!empty($catlist)) {
        $select = '<select class="form-select" name="category">';
        $select .= '<option value="0" disabled selected hidden>' . __('Choose category', 'oneunion') . '</option>';
        foreach ($catlist as $cat) :
            $image = get_term_image_url($cat);
            $link = get_term_link($cat->term_id, $tax); ?>

            <a class="btn btn-secondary cat-wrap" itemprop="url" href="<?= $link ?>">
                <img src="" alt="" title="" data-src="<?= $image ? $image : get_stylesheet_directory_uri() . '/assets/img/placeholder-small.jpg'; ?>" class="lazyload"><span itemprop="name"><?php echo $cat->name; ?></span>
            </a>

            <?php $select .= '<option value="' . $link . '">' . $cat->name . '</option>'; ?>

<?php endforeach;

        $select .= '</select>';

        echo '<div id="cat-select" class="select-wrapper">';
        echo $select;
        echo '</div>';
    }

endif; ?>