<?php get_header(); ?>
<section class="main-box joinus-list">
    <div class="banner">
        <h2 class="text-center"><?php pll_e('join us'); ?></h2>
    </div>
    <section class="contain-box">
        <div class="email-to">
        <?php pll_e('应聘简历发送至'); ?>:<a href="mailto:hr@simright.com">hr@simright.com</a>
        </div>
        <div class="post-list">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <div class="joinus-item">
                <h3  class="title" ><?php the_title(); ?></h3>
                <div class="content">
                    <?php the_content(); ?>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else : ?>
                <h3 class="title"><a href="#" rel="bookmark">NOT FOUND</a></h3>
            <?php endif; ?>
        </div>
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