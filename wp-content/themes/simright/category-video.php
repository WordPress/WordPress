<?php get_header(); ?>
<section class="main-box video-list">
    <div class="banner">
        <h2 class="text-center">ABOUT US</h2>
        <P>Simulation in your browser</P>
        <P>Accessible,Cost-efficient,For everyone</P>
    </div>
    <section class="contain-box">
        <div class="slide-bar">
            <?php get_search_form(); ?>
            <?php get_the_category( $id ) ?>
            <?php

                function get_category_root_id($cat){ 
                    $this_category = get_category($cat);   // 取得当前分类 
                    while($this_category->category_parent){ 
                        $this_category = get_category($this_category->category_parent); // 将当前分类设为上级分类（往上爬） 
                    } 
                    return $this_category->term_id; // 返回根分类的id号 
                } 
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
                        'child_of'           => get_category_root_id(the_category_ID(false)),
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
                <h2>LATEST</h2>
                <hr/>
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
        <div>
            <?php link_pages('<p><code>Pages:</strong> ', '</p>', 'number'); ?>
        </div>
    </section>
</section>
<?php get_footer(); ?>