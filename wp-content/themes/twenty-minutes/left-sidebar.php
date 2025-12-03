<?php
/**
 * The Template Name: Left Sidebar
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Twenty Minutes
 */

get_header(); ?>

<div class="container">
    <div id="content" class="contentsecwrap">
        <div class="row">
            <div class="col-lg-3 col-md-3">
                <?php get_sidebar();?>
            </div>
            <div class="col-lg-9 col-md-9">
                <section class="site-main">
                    <?php while( have_posts() ) : the_post(); ?>
                        <?php get_template_part( 'content', 'page' ); ?>
                        <?php
                            //If comments are open or we have at least one comment, load up the comment template
                            if ( comments_open() || '0' != get_comments_number() )
                                comments_template();
                            ?>
                    <?php endwhile; ?>
                </section>
            </div>            
        </div>
        <div class="clear"></div>
    </div>
 </div>

<?php get_footer(); ?>