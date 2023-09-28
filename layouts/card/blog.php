<div class="col-sm-12 col-md-6 col-lg-4" style="margin-bottom: 20px;">
	<div class="blog-information-card blog-custom-margin" itemscope itemscope itemtype="http://schema.org/BlogPosting">
		<link itemprop="publisher" href="sadovod">
		<meta itemprop="dateModified" content="<?php the_modified_date('c') ?>">
		<meta itemprop="datePublished" content="<?php the_date('c') ?>">
		<link itemprop="mainEntityOfPage" href="<?= get_post_permalink(); ?>">

		<meta itemprop="name" content="<?php the_title(); ?>">
		<meta itemprop="headline" content="<?php the_title(); ?>">

		<a itemprop="url" href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail(array(415, 256), array('class' => 'img-blog-card lazyload', 'itemprop' => 'image')); ?>
		</a>
		<div class="blog-information-container">
			<h4 class="blog-subtitle-info">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h4>
			<div class="blog-text-info" itemprop="description">
				<?php the_excerpt(); ?>
			</div>
			<div class="blog-links-box">
				<a class="btn btn-link" href="<?php the_permalink(); ?>" class="blog-link-info">Читать далее <i class="i-chevron-down"></i></a>
			</div>
		</div>
	</div>
</div>