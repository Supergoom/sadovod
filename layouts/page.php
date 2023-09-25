<?php

get_header();


get_template_part('layouts/templates/shared/price-header', null, []);
?>
<main class="site-main main-blog">
    <div class="container">
        <?php the_content(); ?>
    </div>
</main>

<?php get_template_part('layouts/templates/shared/form-consultation'); ?>
<?php get_template_part('layouts/widgets/last-blog'); ?>

<?php get_footer(); ?>