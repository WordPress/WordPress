<?php get_header(); ?>
<section class="main-box news-list">
    <div class="banner">
        <h2 class="text-center">NEWS CENTER</h2>
    </div>
    <section class="contain-box">
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div class="new-item">
            <p><?php the_time('Y-m-d') ?></p>
            <hr>
            <a href="<?php the_permalink(); ?>">
                <h3 class="title"><?php the_title(); ?></h3>
            </a>
            <div class="content">
                <?php the_content(); ?>
            </div>
        </div>
        <?php endwhile; ?>
        <?php else : ?>
            <h3 class="title"><a href="#" rel="bookmark">NOT FOUND</a></h3>
        <?php endif; ?>
    </section>
</section>
<?php get_footer(); ?>