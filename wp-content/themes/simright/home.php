<?php get_header(); ?>
<section class="main-box video-list">
    <div class="banner">
        <h2 class="text-center">HOME</h2>
    </div>
    <section class="contain-box">
        <div class="slide-bar">
            <?php get_search_form(); ?>
            <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Viedeo_list_classification') ) : ?>
                <ul>
                    <?php wp_list_categories('depth=1&title_li=&orderby=id&show_count=1&hide_empty=1&child_of=0'); ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="contain-body">
            <section class="list-item">
                <div class="list-contain">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="video-item">
                        <a href="<?php the_permalink(); ?>">
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
        </div>
        <div>
            <?php link_pages('<p><code>Pages:</strong> ', '</p>', 'number'); ?>
        </div>
    </section>
</section>
<?php get_footer(); ?>