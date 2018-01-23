<?php get_header(); ?>
<section class="main-box video-list">
    <section class="contain-box">
        <div class="slide-bar">
            <form class="neck-bar-search">
                <div class="form-group input-group">
                    <input type="text" name="s" id="search" value="<?php echo the_search_query(); ?>" placeholder="Search" class="form-control" />
                    <input type="hidden" name="cat" value="<?php echo get_query_var( 'cat' ) ?>" />
                    <button type="submit" class="input-group-addon"><i class="glyphicon glyphicon-search"></i></button>
                </div>
            </form>
            <?php 
             if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Viedeo_list_classification') ) : ?>
                <ul>
                    <?php $args = array(
                        'show_option_all'    => '',
                        'orderby'            => 'name',
                        'order'              => 'ASC',
                        'style'              => 'list',
                        'show_count'         => 1,
                        'hide_empty'         => 0,
                        'use_desc_for_title' => 1,
                        'child_of'           => get_query_var('cat'),
                        'feed'               => '',
                        'feed_type'          => '',
                        'feed_image'         => '',
                        'exclude'            => '',
                        'exclude_tree'       => '',
                        'include'            => '',
                        'hierarchical'       => 1,
                        'title_li'           => '',
                        'show_option_none'   => '',
                        'number'             => null,
                        'echo'               => 1,
                        'depth'              => 0,
                        'current_category'   => 0,
                        'pad_counts'         => 0,
                        'taxonomy'           => 'category',
                        'walker'             => null
                    ); 
                    wp_list_categories($args)
                    ?>
                </ul>
            <?php endif; ?>
        </div>
        <div class="contain-body">
            <section class="list-item">
                <!-- <h2>LATEST</h2>
                <hr/> -->
                <div class="list-contain">
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="video-item">
                        <a href="<?php the_permalink(); ?>">
                            <img src="<?php post_thumbnail_src('thumbnail'); ?>" alt="">
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
    </section>
</section>
<?php get_footer(); ?>