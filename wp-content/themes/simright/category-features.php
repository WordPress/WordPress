<?php get_header(); ?>
<section class="main-box feature-list">
    <div class="banner">
        <h2 class="text-center"> <?php pll_e('FEATURES'); ?></h2>
    </div>
    <section class="about-feature">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <?php the_content(); ?>
    <?php endwhile; ?>
    <?php else : ?>
        <h3 class="title"><a href="#" rel="bookmark">NOT FOUND</a></h3>
    <?php endif; ?>
    </section>
</section>
<?php get_footer(); ?>