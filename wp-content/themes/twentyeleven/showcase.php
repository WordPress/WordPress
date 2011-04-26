<?php
/**
 * Template Name: Showcase Template
 * Description: A Page Template that showcases Sticky Posts, Asides, and Blog Posts
 *
 * @package WordPress
 * @subpackage Twenty Eleven
 */

get_header(); ?>

		<div id="primary" class="showcase">
			<div id="content" role="main">

				<?php the_post(); ?>

				<?php
					// If we have content for this page, let's display it.
					if ( '' != get_the_content() )
						get_template_part( 'content', 'intro' );
				?>

				<?php
					// See if we have any sticky posts and use the latest to create a featured post
					$sticky = get_option( 'sticky_posts' );
					$featured_args = array(
						'posts_per_page' => 1,
						'post__in' => $sticky,
					);

					$featured = new WP_Query();
					$featured->query( $featured_args );

					// Let's roll.
					if ( $sticky ) :

					$featured->the_post();

					// We're going to add a class to our featured post for featured images
					// by default it'll have no class though
					$feature_class = '';

					if ( has_post_thumbnail() ) {
						// … but if it has a featured image let's add some class
						$feature_class = 'feature-image small';

						// Hang on. Let's check this here image out.
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), array( HEADER_IMAGE_WIDTH, HEADER_IMAGE_WIDTH ) );

						// Is it bigger than or equal to our header?
						if ( $image[1] >= HEADER_IMAGE_WIDTH ) {
							// Let's add a BIGGER class. It's EXTRA classy now.
							$feature_class = 'feature-image large';
						}
					}
					?>

				<?php if ( has_post_thumbnail() ) : ?>
				<section class="featured-post <?php echo $feature_class; ?>">
				<?php else : ?>
				<section class="featured-post">
				<?php endif; ?>
					<h1 class="showcase-heading"><?php _e( 'Featured Post', 'twentyeleven' ); ?></h1>
					<?php
						// Dynamic thumbnails!
						if ( has_post_thumbnail() ) {
							if ( $image[1] >= HEADER_IMAGE_WIDTH ) { ?>
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"> <?php the_post_thumbnail( 'large-feature' ); ?></a>
							<?php } else { ?>
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_post_thumbnail( 'small-feature' ); ?></a>
							<?php }
						}
					?>
					<?php get_template_part( 'content', 'featured' ); ?>
				</section>
				<?php endif; ?>

				<section class="recent-posts">
					<h1 class="showcase-heading"><?php _e( 'Recent Posts', 'twentyeleven' ); ?></h1>

					<?php

					// Display our recent posts, showing full content for the very latest, ignoring Aside posts
					$recent_args = array(
						'order' => 'DESC',
						'post__not_in' => get_option( 'sticky_posts' ),
						'tax_query' => array(
							array(
								'taxonomy' => 'post_format',
								'terms' => array( 'post-format-aside', 'post-format-link' ),
								'field' => 'slug',
								'operator' => 'NOT IN',
							),
						),
					);
					$recent = new WP_Query();
					$recent->query( $recent_args );
					$counter = 0;

					while ( $recent->have_posts() ) : $recent->the_post();
						// set $more to 0 in order to only get the first part of the post
						global $more;
						$more = 0;
						$counter++;

						if ( 1 == $counter ) :
							get_template_part( 'content', get_post_format() );
							echo '<ol class="other-recent-posts">';

						else : ?>
							<li class="entry-title">
								<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
								<span class="comments-link">
									<?php comments_popup_link( __( '<span class="leave-reply">Leave a reply</span>', 'twentyeleven' ), __( '<b>1</b> Reply', 'twentyeleven' ), __( '<b>%</b> Replies', 'twentyeleven' ) ); ?>
								</span>
							</li>

						<?php endif;
					endwhile;
					?>

					</ol>
				</section>

				<div class="widget-area" role="complementary">
					<?php if ( ! dynamic_sidebar( 'sidebar-2' ) ) : ?>

						<?php
						the_widget( 'Twenty_Eleven_Ephemera_Widget', '', 'before_title=<h1 class="widget-title">&after_title=</h1>' );
						?>

					<?php endif; // end sidebar widget area ?>
				</div><!-- .widget-area -->

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_footer(); ?>