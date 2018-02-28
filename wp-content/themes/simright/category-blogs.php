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
					<h3 class="title"><?php $title = get_the_title(); $release =substr($title,0,strrpos($title,':')+1); echo $release ; ?>
						<br/>
						<?php $title = get_the_title(); $release =substr($title,strrpos($title,':')+1); echo $release ; ?>
					</h3>
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
	<?php $current_lan = pll_current_language(); if($current_lan == 'en'): ?>
		<div id="pagination" class="noajx"><?php next_posts_link("Load more") ?></div>
		<div id="loadmore"><a href="javascript:;">Loading</a></div>
	<?php else : ?>
		<div id="pagination" class="noajx"><?php next_posts_link("加载更多...") ?></div>
		<div id="loadmore"><a href="javascript:;">正在加载 ...</a></div>
	<?php endif; ?>
</section>
<?php get_footer(); ?>