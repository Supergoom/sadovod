<?php

get_header();

global $public_posts;

$args = array(
    'post_type'         => $wp_taxonomies[get_query_var('taxonomy')]->object_type,
    'paged'             => get_query_var('paged'),
    'posts_per_page'    => 12,
    'tax_query'         => array(
        array(
            'taxonomy'     => get_query_var('taxonomy'),
            'field'        => 'slug',
            'terms'     => get_query_var('term')
        )
    ),
);

$post_query = new WP_Query($args);

?>

<main id="category" class="site-category not-empty">
    <div id="category-list">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <?php outputBreadcrumbs(); ?>
                </div>
                <div class="col-sm-12">
                    <h1 class="section-title"><?php the_term_title(); ?>
                        <small>(<?= sprintf(_n('%s item', '%s items', $post_query->found_posts), $post_query->found_posts); ?>)</small>
                        <?php pagenumber(); ?>
                    </h1>
                </div>
                <div class="col-sm-12">
                    <div id="category-nav">
                        <?php get_template_part('layouts/widgets/filter');  ?>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div id="content-row" class="row">
                        <div class="col-lg-7 flex-grow-1">
                            <div class="main-content-wrap">
                                <?php do_action('main_content') ?>
                                <div class="row">
                                    <?php
                                    if ($post_query->have_posts()) :
                                        while ($post_query->have_posts()) : $post_query->the_post();
                                            $name = basename($public_posts[get_post_type()]->rewrite['slug']);
                                            if (file_exists(get_template_directory() . '/layouts/card/' . $name . '.php')) {
                                                get_template_part('layouts/card/' . $name);
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
                            <div class="row">
                                <div class="col-sm-12">
                                    <?php pagination(); ?>
                                </div>
                            </div>
                        </div>
                        <div id="sidebar" class="col-lg-4">
                            <div class="sidebar-inner">
                                <?php
                                if (function_exists('dynamic_sidebar'))
                                    dynamic_sidebar('taxonomy-sidebar');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main><!-- #main -->

<?php get_footer(); ?>