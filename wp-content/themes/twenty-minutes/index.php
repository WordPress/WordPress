<?php
/**
 * The template for displaying home page.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Twenty Minutes
 */

get_header();
?>

<div class="container">
    <div id="content" class="contentsecwrap">
        <section class="site-main">

            <?php
            $twenty_minutes_sidebar_layout = get_theme_mod('twenty_minutes_sidebar_post_layout', 'right');

            if ($twenty_minutes_sidebar_layout == 'left') :
            ?>
                <div class="row m-0">
                    <div class="col-lg-4 col-md-4" id="sidebar">
                        <?php get_sidebar();?>
                    </div>
                    <div class="col-lg-8 col-md-8">
                        <div class="postsec-list">
                            <?php
                            if ( have_posts() ) :
                            // Start the Loop.
                            while ( have_posts() ) : the_post();
                                /*
                                 * Include the post format-specific template for the content. If you want to
                                 * use this in a child theme, then include a file called called content-___.php
                                 * (where ___ is the post format) and that will be used instead.
                                 */
                                get_template_part( 'template-parts/post/content' );
                        
                            endwhile;
                            // Previous/next post navigation.
                            the_posts_pagination();
                        
                             else :
                            // If no content, include the "No posts found" template.
                            get_template_part( 'no-results', 'index' );
                        
                            endif; ?>
                        </div>
                    </div>
                </div>
            <?php
            elseif ($twenty_minutes_sidebar_layout == 'right') :
            ?>
                <div class="row m-0">
                    <div class="col-lg-8 col-md-8">
                        <div class="postsec-list">
                            <?php
                            if ( have_posts() ) :
                            // Start the Loop.
                            while ( have_posts() ) : the_post();
                                /*
                                 * Include the post format-specific template for the content. If you want to
                                 * use this in a child theme, then include a file called called content-___.php
                                 * (where ___ is the post format) and that will be used instead.
                                 */
                                get_template_part( 'template-parts/post/content' );
                        
                            endwhile;
                            // Previous/next post navigation.
                            the_posts_pagination();
                        
                             else :
                            // If no content, include the "No posts found" template.
                            get_template_part( 'no-results', 'index' );
                        
                            endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4" id="sidebar">
                        <?php get_sidebar();?>
                    </div>
                </div>
            <?php
            elseif ($twenty_minutes_sidebar_layout == 'full') :
            ?>
                <div class="full">
                    <div class="postsec-list">
                        <?php
                            if ( have_posts() ) :
                            // Start the Loop.
                            while ( have_posts() ) : the_post();
                                /*
                                 * Include the post format-specific template for the content. If you want to
                                 * use this in a child theme, then include a file called called content-___.php
                                 * (where ___ is the post format) and that will be used instead.
                                 */
                                get_template_part( 'template-parts/post/content' );
                        
                            endwhile;
                            // Previous/next post navigation.
                            the_posts_pagination();
                        
                             else :
                            // If no content, include the "No posts found" template.
                            get_template_part( 'no-results', 'index' );
                        
                            endif; ?>
                    </div>
                </div>
            <?php
            elseif ($twenty_minutes_sidebar_layout == 'three-column') :
            ?>
                <div class="row m-0">
                    <div class="col-lg-3 col-md-3" id="sidebar">
                        <?php dynamic_sidebar('sidebar-1'); ?>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="postsec-list">
                            <?php
                            if ( have_posts() ) :
                            // Start the Loop.
                            while ( have_posts() ) : the_post();
                                /*
                                 * Include the post format-specific template for the content. If you want to
                                 * use this in a child theme, then include a file called called content-___.php
                                 * (where ___ is the post format) and that will be used instead.
                                 */
                                get_template_part( 'template-parts/post/content' );
                        
                            endwhile;
                            // Previous/next post navigation.
                            the_posts_pagination();
                        
                             else :
                            // If no content, include the "No posts found" template.
                            get_template_part( 'no-results', 'index' );
                        
                            endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3" id="sidebar">
                        <?php dynamic_sidebar('sidebar-2'); ?>
                    </div>
                </div>
            <?php
            elseif ($twenty_minutes_sidebar_layout == 'four-column') :
            ?>
                <div class="row m-0">
                    <div class="col-lg-3 col-md-3" id="sidebar">
                        <?php dynamic_sidebar('sidebar-1'); ?>
                    </div>
                    <div class="col-lg-3 col-md-3">
                        <div class="postsec-list four-col">
                            <?php
                            if ( have_posts() ) :
                            // Start the Loop.
                            while ( have_posts() ) : the_post();
                                /*
                                 * Include the post format-specific template for the content. If you want to
                                 * use this in a child theme, then include a file called called content-___.php
                                 * (where ___ is the post format) and that will be used instead.
                                 */
                                get_template_part( 'template-parts/post/content' );
                        
                            endwhile;
                            // Previous/next post navigation.
                            the_posts_pagination();
                        
                             else :
                            // If no content, include the "No posts found" template.
                            get_template_part( 'no-results', 'index' );
                        
                            endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3" id="sidebar">
                        <?php dynamic_sidebar('sidebar-2'); ?>
                    </div>
                    <div class="col-lg-3 col-md-3" id="sidebar">
                        <?php dynamic_sidebar('sidebar-3'); ?>
                    </div>
                </div>
            <?php
            elseif ($twenty_minutes_sidebar_layout == 'grid') :
            ?>
                <div class="row m-0">
                    <div class="col-lg-9 col-md-9">
                        <div class="row">
                            <?php
                            if ( have_posts() ) :
                            // Start the Loop.
                            while ( have_posts() ) : the_post();
                                /*
                                 * Include the post format-specific template for the content. If you want to
                                 * use this in a child theme, then include a file called called content-___.php
                                 * (where ___ is the post format) and that will be used instead.
                                 */
                                get_template_part( 'template-parts/post/content-grid' );
                        
                            endwhile;
                            // Previous/next post navigation.
                            the_posts_pagination();
                        
                             else :
                            // If no content, include the "No posts found" template.
                            get_template_part( 'no-results', 'index' );
                        
                            endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3" id="sidebar">
                        <?php get_sidebar();?>
                    </div>
                </div>
            <?php
            else :
            ?>
                <div class="row m-0">
                    <div class="col-lg-8 col-md-8">
                        <div class="postsec-list">
                            <?php
                            if ( have_posts() ) :
                            // Start the Loop.
                            while ( have_posts() ) : the_post();
                                /*
                                 * Include the post format-specific template for the content. If you want to
                                 * use this in a child theme, then include a file called called content-___.php
                                 * (where ___ is the post format) and that will be used instead.
                                 */
                                get_template_part( 'template-parts/post/content' );
                        
                            endwhile;
                            // Previous/next post navigation.
                            the_posts_pagination();
                        
                             else :
                            // If no content, include the "No posts found" template.
                            get_template_part( 'no-results', 'index' );
                        
                            endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4" id="sidebar">
                        <?php get_sidebar();?>
                    </div>
                </div>
            <?php
            endif;
            ?>

        </section>
    </div>
</div>

<?php get_footer(); ?>
