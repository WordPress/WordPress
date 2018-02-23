<?php get_header(); ?>
<section class="main-box video-details">
    <section class="contain-box">
        <div class="contain-body">
            <div class="main-contain">
                <a href="<?php echo get_option('home'); ?>/<?php echo pll_current_language() ?>/category/blogs"><?php pll_e('返回博客中心'); ?></a>
                    <hr/>
                <?php if (have_posts()) : the_post(); update_post_caches($posts); ?>
                    <div class="list-contain">
                        <div class="post-header">
                            <h3 class="title"><?php the_title(); ?></h3>
                            <p><?php the_time('Y-m-d') ?></p>
                        </div>
                        <div>
                            <?php the_content(); ?>
                        </div>
                    </div>
                <?php else : ?>
                    <p>error</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</section>
<?php get_footer(); ?>