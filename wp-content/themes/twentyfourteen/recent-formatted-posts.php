<?php
/**
 * A template to display recent post formatted posts.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
?>

<div class="post-formatted-posts">
<?php
	do_action( 'before_sidebar' );
	do_action( 'twentyfourteen_formatted_posts_before' );
	$recent_videos = twentyfourteen_get_recent( 'post-format-video' );
	if ( $recent_videos->have_posts() ) :
?>
	<section id="recent-videos" class="recent-videos">
		<h1 class="format-title genericon">
			<a class="entry-format" href="<?php echo esc_url( get_post_format_link( 'video' ) ); ?>" title="<?php esc_attr_e( 'All Video Posts', 'twentyfourteen' ); ?>"><?php _e( 'Videos', 'twentyfourteen' ); ?></a>
		</h1>
		<?php
			while ( $recent_videos->have_posts() ) : $recent_videos->the_post();
				get_template_part( 'content', 'recent-formatted-post' );
			endwhile;
		?>
		<a class="more-formatted-posts-link" href="<?php echo esc_url( get_post_format_link( 'video' ) ); ?>" title="<?php esc_attr_e( 'More Videos', 'twentyfourteen' ); ?>"><?php _e( 'More Videos <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ); ?></a>
	</section>
<?php endif; ?>

<?php
	$recent_images = twentyfourteen_get_recent( 'post-format-image' );
	if ( $recent_images->have_posts() ) :
?>
	<section id="recent-images" class="recent-images">
		<h1 class="format-title genericon">
			<a class="entry-format" href="<?php echo esc_url( get_post_format_link( 'image' ) ); ?>" title="<?php esc_attr_e( 'All Image Posts', 'twentyfourteen' ); ?>"><?php _e( 'Images', 'twentyfourteen' ); ?></a>
		</h1>
		<?php
			while ( $recent_images->have_posts() ) : $recent_images->the_post();
				get_template_part( 'content', 'recent-formatted-post' );
			endwhile;
		?>
		<a class="more-formatted-posts-link" href="<?php echo esc_url( get_post_format_link( 'image' ) ); ?>" title="<?php esc_attr_e( 'More images', 'twentyfourteen' ); ?>"><?php _e( 'More images <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ); ?></a>
	</section>
<?php endif; ?>

<?php
	$recent_galleries = twentyfourteen_get_recent( 'post-format-gallery' );
	if ( $recent_galleries->have_posts() ) :
?>
	<section id="recent-galleries" class="recent-galleries">
		<h1 class="format-title genericon">
			<a class="entry-format" href="<?php echo esc_url( get_post_format_link( 'gallery' ) ); ?>" title="<?php esc_attr_e( 'All Gallery Posts', 'twentyfourteen' ); ?>"><?php _e( 'Galleries', 'twentyfourteen' ); ?></a>
		</h1>
		<?php
			while ( $recent_galleries->have_posts() ) : $recent_galleries->the_post();
				get_template_part( 'content', 'recent-formatted-post' );
			endwhile;
		?>
		<a class="more-formatted-posts-link" href="<?php echo esc_url( get_post_format_link( 'gallery' ) ); ?>" title="<?php esc_attr_e( 'More Galleries', 'twentyfourteen' ); ?>"><?php _e( 'More galleries <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ); ?></a>
	</section>
<?php endif; ?>

<?php
	$recent_asides = twentyfourteen_get_recent( 'post-format-aside' );
	if ( $recent_asides->have_posts() ) :
?>
	<section id="recent-asides" class="recent-asides">
		<h1 class="format-title genericon">
			<a class="entry-format" href="<?php echo esc_url( get_post_format_link( 'aside' ) ); ?>" title="<?php esc_attr_e( 'All Aside Posts', 'twentyfourteen' ); ?>"><?php _e( 'Asides', 'twentyfourteen' ); ?></a>
		</h1>
		<?php
			while ( $recent_asides->have_posts() ) : $recent_asides->the_post();
				get_template_part( 'content', 'recent-formatted-post' );
			endwhile;
		?>
		<a class="more-formatted-posts-link" href="<?php echo esc_url( get_post_format_link( 'aside' ) ); ?>" title="<?php esc_attr_e( 'More Asides', 'twentyfourteen' ); ?>"><?php _e( 'More asides <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ); ?></a>
	</section>
<?php endif; ?>

<?php
	$recent_links = twentyfourteen_get_recent( 'post-format-link' );
	if ( $recent_links->have_posts() ) :
?>
	<section id="recent-links" class="recent-links">
		<h1 class="format-title genericon">
			<a class="entry-format" href="<?php echo esc_url( get_post_format_link( 'link' ) ); ?>" title="<?php esc_attr_e( 'All Link Posts', 'twentyfourteen' ); ?>"><?php _e( 'Links', 'twentyfourteen' ); ?></a>
		</h1>
		<?php
			while ( $recent_links->have_posts() ) : $recent_links->the_post();
				get_template_part( 'content', 'recent-formatted-post' );
			endwhile;
		?>
		<a class="more-formatted-posts-link" href="<?php echo esc_url( get_post_format_link( 'link' ) ); ?>" title="<?php esc_attr_e( 'More Links', 'twentyfourteen' ); ?>"><?php _e( 'More links <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ); ?></a>
	</section>
<?php endif; ?>

<?php
	$recent_quotes = twentyfourteen_get_recent( 'post-format-quote' );
	if ( $recent_quotes->have_posts() ) :
?>
	<section id="recent-quotes" class="recent-quotes">
		<h1 class="format-title genericon">
			<a class="entry-format" href="<?php echo esc_url( get_post_format_link( 'quote' ) ); ?>" title="<?php esc_attr_e( 'All Quote Posts', 'twentyfourteen' ); ?>"><?php _e( 'Quotes', 'twentyfourteen' ); ?></a>
		</h1>
		<?php
			while ( $recent_quotes->have_posts() ) : $recent_quotes->the_post();
				get_template_part( 'content', 'recent-formatted-post' );
			endwhile;
		?>
		<a class="more-formatted-posts-link" href="<?php echo esc_url( get_post_format_link( 'quote' ) ); ?>" title="<?php esc_attr_e( 'More Quotes', 'twentyfourteen' ); ?>"><?php _e( 'More quotes <span class="meta-nav">&rarr;</span>', 'twentyfourteen' ); ?></a>
	</section>
<?php endif; ?>

<?php
	wp_reset_postdata();
	do_action( 'twentyfourteen_formatted_posts_after' );
?>

</div>