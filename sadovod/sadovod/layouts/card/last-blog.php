<div class="col-sm-12 col-md-6 col-lg-3 last-blog__item">
    <div class="blog-information-card blog-custom-margin" itemscope itemscope itemtype="http://schema.org/BlogPosting">
        <link itemprop="publisher" href="sadovod">
        <meta itemprop="dateModified" content="<?php the_modified_date('c') ?>">
        <meta itemprop="datePublished" content="<?php the_date('c') ?>">
        <link itemprop="mainEntityOfPage" href="<?= get_post_permalink(); ?>">

        <meta itemprop="name" content="<?php the_title(); ?>">
        <meta itemprop="headline" content="<?php the_title(); ?>">

        <a itemprop="url" href="<?php the_permalink(); ?>" class="last-blog__block">
            <div class="last-blog__img">
				<?php the_post_thumbnail(array(300, 256), array('class' => 'img-blog-card lazyload', 'itemprop' => 'image')); ?>
            </div>
            <div class="last-blog__card-title">
                <?php the_title(); ?>
            </div>
        </a>
    </div>
</div>