<?php ?>

<div class="last-blog__title" style="margin-bottom:20px; margin-top:20px">Новое в блоге :</div>
<div class="row last-blog__row">
    <?php

    $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
    $args = array(
        'post_type' => 'post',
        'orderby' => 'date',
        'order' => 'DESC',
        'post_status' => 'publish',
        'posts_per_page' => 4,
        'paged' => $paged
    );

    $post_query = new WP_Query($args);

    if ($post_query->have_posts()) :
        while ($post_query->have_posts()) : $post_query->the_post();
            if (file_exists(get_template_directory() . '/layouts/card/last-blog.php')) {
                get_template_part('layouts/card/last-blog');
            } else {
                get_template_part('layouts/card/default');
            }
        endwhile;
        ?><div class="container"><?php

        echo paginate_links( array(
        'base' => site_url() . '/news%_%',
        'format' => '?paged=%#%',
        'total' => $post_query->max_num_pages,
        'current' => $paged,
    ) );
        wp_reset_postdata();
        ?></div><?php
    else :
        get_template_part('layouts/card/no-result');
    endif;
    ?>
</div>

