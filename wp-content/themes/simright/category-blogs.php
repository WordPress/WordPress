<?php get_header(); ?>
<section class="main-box blog-list">
    <div class="banner">
        <h2 class="text-center"> <?php pll_e('blog'); ?></h2>
    </div>
    <section class="contain-box">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="blog-item-wrap">
			<div class="blog-item">
            	<p><b>Simright</b><i>/</i><?php the_time('Y-m-d') ?></p>
				<hr>
				<a href="<?php the_permalink(); ?>">
					<h3 class="title"><?php the_title(); ?></h3>
				</a>
				<div class="content">
					<?php the_excerpt(); ?>
				</div>
			</div>
		</div>
        <?php endwhile; ?>
        <?php else : ?>
            <h3 class="title"><a href="#" rel="bookmark">NOT FOUND</a></h3>
        <?php endif; ?>
    </section>
</section>
<?php get_footer(); ?>