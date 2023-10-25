<?php ?>
<div class="last-blog">
    <div class="container">
        <div class="last-blog__title">Новое в блоге:</div>
        <div class="row last-blog__row">
            <?php
            $args = [
                'post_type' => 'post',
                //'paged'             => get_query_var('paged'),
                'posts_per_page'    => 4
            ];

            $post_query = new WP_Query($args);

            if ($post_query->have_posts()) :
                while ($post_query->have_posts()) : $post_query->the_post();
                    if (file_exists(get_template_directory() . '/layouts/card/last-blog.php')) {
                        get_template_part('layouts/card/last-blog');
                    } else {
                        get_template_part('layouts/card/default');
                    }
                endwhile;
            else :
                get_template_part('layouts/card/no-result');
            endif;
            ?>
        </div>
    </div>
</div>