<?php get_header('video'); ?>
<section class="main-box video-details">
    <section class="contain-box">
        <div class="contain-body">
            <div class="main-contain">
                <a href="<?php echo get_option('home'); ?>/<?php echo pll_current_language() ?>/category/video"><?php pll_e('返回视频中心'); ?></a>
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
            <div class="comment-list" id="user-comment">
                <?php
                    if ( comments_open() || get_comments_number() ) {
                        comments_template();
                } ?>
			</div>
        </div>
        <div class="slide-bar">
            <h3><?php pll_e('同类教程'); ?></h3>
            <?php
                $cat=get_the_category();
                $cat_id=$cat[0]->cat_ID;
                query_posts('order=asc&cat='.$cat_id);
                while (have_posts()):the_post();
            ?>
            <div class="video-item">
                <a href="<?php the_permalink(); ?>">
                    <img src="<?php post_thumbnail_src('thumbnail'); ?>" alt="">
                    <span><?php the_title(); ?></span>
                    <p><?php the_time('Y-m-d') ?></p>
                </a>
            </div>
            <?php  endwhile;wp_reset_query(); ?>
        </div>
    </section>
</section>
<?php get_footer(); ?>