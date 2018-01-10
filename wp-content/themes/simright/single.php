<?php get_header(); ?>
<section class="main-box video-details">
    <section class="contain-box">
        <div class="contain-body">
            <div class="main-contain">
                <?php if (have_posts()) : the_post(); update_post_caches($posts); ?>
                    <a href="<?php echo get_option('home'); ?>"<返回视频中心</a>
                    <hr/>
                    <div class="list-contain">
                        <h3 class="title"><?php the_title(); ?></h3>
                        <p><?php the_time('Y-m-d') ?></p>
                        <div>
                            <?php the_content(); ?>
                        </div>
                    </div>
                <?php else : ?>
                    <p>error</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="slide-bar">
            <h3>同类教程</h3>
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <div class="video-item">
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php bloginfo('template_url'); ?>" alt="">
                        <span><?php the_title(); ?></span>
                        <p><?php the_time('Y-m-d') ?></p>
                    </a>
                </div>
            <?php endwhile; ?>
            <?php else : ?>
                <h3 class="title"><a href="#" rel="bookmark">NOT FOUND</a></h3>
            <?php endif; ?>
        </div>
    </section>
</section>
<?php get_footer(); ?>