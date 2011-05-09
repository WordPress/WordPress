<?php
/**
 * Template Name: Showcase Template
 * Description: A Page Template that showcases Sticky Posts, Asides, and Blog Posts
 *
 * The showcase template in Twenty Eleven consists of a featured posts section using sticky posts,
 * another recent posts area (with the latest post shown in full and the rest as a list)
 * and a left sidebar holding aside posts.
 *
 * We are creating two queries to fetch the proper posts and a custom widget for the sidebar.
 *
 * @package WordPress
 * @subpackage Twenty Eleven
 * @since Twenty Eleven 1.0
 */

// Enqueue showcase script for the slider
wp_enqueue_script( 'twentyeleven-showcase', get_template_directory_uri() . '/js/showcase.js', array( 'jquery' ), '2011-04-28' );

get_header(); ?>

		<div id="primary" class="showcase">
			<div id="content" role="main">

				<?php the_post(); ?>

				<?php
					/**
					 * We are using a heading by rendering the_content
					 * If we have content for this page, let's display it.
					 */
					if ( '' != get_the_content() )
						get_template_part( 'content', 'intro' );
				?>

				<?php
					/**
					 * Begin the featured posts section.
					 *
					 * See if we have any sticky posts and use them to create our featured posts.
					 * We limit the featured posts at ten.
					 */
					$sticky = get_option( 'sticky_posts' );
					$featured_args = array(
						'post__in' => $sticky,
						'post_status' => 'publish',
						'posts_per_page' => 10,
					);

					// The Featured Posts query.
					$featured = new WP_Query( $featured_args );

					// Proceed only if sticky posts exist.
					if ( $featured->have_posts() ) :

					/**
					 * We will need to count featured posts starting from zero
					 * to create the slider navigation.
					 */
					$counter_slider = 0;

					?>

				<div class="featured-posts">
					<h1 class="showcase-heading"><?php _e( 'Featured Post', 'twentyeleven' ); ?></h1>

				<?php
					// Let's roll.
					while ( $featured->have_posts() ) : $featured->the_post();

					// Increase the counter.
					$counter_slider++;

					/**
					 * We're going to add a class to our featured post for featured images
					 * by default it'll have no class though.
					 */
					$feature_class = '';

					if ( has_post_thumbnail() ) {
						// ... but if it has a featured image let's add some class
						$feature_class = 'feature-image small';

						// Hang on. Let's check this here image out.
						$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), array( HEADER_IMAGE_WIDTH, HEADER_IMAGE_WIDTH ) );

						// Is it bigger than or equal to our header?
						if ( $image[1] >= HEADER_IMAGE_WIDTH ) {
							// If bigger, let's add a BIGGER class. It's EXTRA classy now.
							$feature_class = 'feature-image large';
						}
					}
				?>

				<?php if ( has_post_thumbnail() ) : ?>
				<section class="featured-post <?php echo $feature_class; ?>" id="featured-post-<?php echo $counter_slider; ?>">
				<?php else : ?>
				<section class="featured-post" id="featured-post-<?php echo $counter_slider; ?>">
				<?php endif; ?>

					<?php
						/**
						 * If the thumbnail is as big as the header image
						 * make it a large featured post, otherwise render it small
						 */
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
				<?php endwhile;	?>

				<?php
					// Show slider only if we have more than one featured post.
					if ( $featured->post_count > 1 ) :
				?>
				<nav class="feature-slider">
					<ul>
					<?php
						/**
						 * We need to query the same set of posts again
						 * to populate the navigation dots
						 */
				    	$featured->query( $featured_args );

						// Reset the counter so that we end up with matching elements
				    	$counter_slider = 0;

						// Begin from zero
				    	rewind_posts();

						// Let's roll again.
				    	while ( $featured->have_posts() ) : $featured->the_post();
				    		$counter_slider++;
				    ?>
						<li><a href="#featured-post-<?php echo $counter_slider; ?>" title="<?php printf( esc_attr__( 'Featuring: %s', 'twentyeleven' ), the_title_attribute( 'echo=0' ) ); ?>" <?php
						if ( 1 == $counter_slider ) :
							echo 'class="active"';
						endif;
						?>></a></li>
					<?php endwhile;	?>
					</ul>
				</nav>
				<?php endif; // End check for more than one sticky post. ?>
				</div><!-- .featured-posts -->
				<?php endif; // End check for sticky posts. ?>

				<section class="recent-posts">
					<h1 class="showcase-heading"><?php _e( 'Recent Posts', 'twentyeleven' ); ?></h1>

					<?php

					// Display our recent posts, showing full content for the very latest, ignoring Aside posts.
					$recent_args = array(
						'order' => 'DESC',
						'post__not_in' => get_option( 'sticky_posts' ),
						'tax_query' => array(
							array(
								'taxonomy' => 'post_format',
								'terms' => array( 'post-format-aside', 'post-format-link', 'post-format-quote', 'post-format-status' ),
								'field' => 'slug',
								'operator' => 'NOT IN',
							),
						),
					);
					// Our new query for the Recent Posts section.
					$recent = new WP_Query();
					$recent->query( $recent_args );
					$counter = 0;

					while ( $recent->have_posts() ) : $recent->the_post();
						// Set $more to 0 in order to only get the first part of the post.
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
				</section><!-- .recent-posts -->

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