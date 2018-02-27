<?php get_header(); ?>
<section class="main-box video-list">
    <div class="banner">
        <?php $ptranslations = pll_the_languages( array( 'show_flags' => 0,'show_names' => 0 ,'hide_current'=> 0,'hide_if_no_translation' => 1,'raw' => 1 ) );
            foreach($ptranslations as $value){
            if($value['current_lang'] && $value['name'] === '中文'){
                echo'<div><div><h2>'. get_post(67)->post_title .'</h2><p>'. get_post(67)->post_excerpt .'</p><button><a href="'. get_permalink(67) .'">观看</a></button></div><div><img src="'. get_thumbnail_src(67) .'" alt=""></div></div>';
            }elseif($value['current_lang'] && $value['name'] === 'English'){
                echo'<div><div><h2>'. get_post(344)->post_title .'</h2><p>'. get_post(344)->post_excerpt .'</p><button><a href="'. get_permalink(344) .'">Watch Now</a></button></div><div><img src="'. get_thumbnail_src(344) .'" alt=""></div></div>';
            }
        } ?>
    </div>
    <section class="contain-box">
        <div class="slide-bar">
            <?php get_search_form(); ?>
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