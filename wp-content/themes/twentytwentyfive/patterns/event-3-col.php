<?php
/**
 * Title: Events, 3 columns with event images and titles
 * Slug: twentytwentyfive/event-3-col
 * Categories: banner
 * Description: A header with title and text and three columns that show 3 events with their images and titles.
 * Keywords: events, columns, images
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

?>
<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"},"blockGap":"0","margin":{"top":"0","bottom":"0"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull" style="margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50)">
	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:heading {"fontSize":"x-large"} -->
		<h2 class="wp-block-heading has-x-large-font-size"><?php esc_html_e( 'Events', 'twentytwentyfive' ); ?></h2>
		<!-- /wp:heading -->

		<!-- wp:paragraph -->
		<p><?php esc_html_e( 'These are some of the upcoming events.', 'twentytwentyfive' ); ?></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"top":"0","left":"var:preset|spacing|50"},"padding":{"top":"0","bottom":"0"}}}} -->
	<div class="wp-block-columns alignwide" style="padding-top:0;padding-bottom:0">
		<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70"},"blockGap":"0"}}} -->
		<div class="wp-block-column" style="padding-top:var(--wp--preset--spacing--70)">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
			<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/ruins-image.webp' ); ?>" alt="<?php esc_attr_e( 'Event image', 'twentytwentyfive' ); ?>"/></figure>
			<!-- /wp:image -->

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30)">
				<!-- wp:heading {"level":3,"style":{"spacing":{"padding":{"top":"var:preset|spacing|20"}}}} -->
				<h3 class="wp-block-heading" style="padding-top:var(--wp--preset--spacing--20)"><?php esc_html_e( 'Tell your story', 'twentytwentyfive' ); ?></h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|accent-4"}}}},"textColor":"accent-4","fontSize":"medium"} -->
				<p class="has-accent-4-color has-text-color has-link-color has-medium-font-size"><?php echo esc_html_x( 'Mon, Jan 1', 'Example event date in pattern.', 'twentytwentyfive' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:paragraph {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40"}}}} -->
			<p style="padding-top:var(--wp--preset--spacing--40)"><a href="#"><?php esc_html_e( 'Event details', 'twentytwentyfive' ); ?></a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70"},"blockGap":"0"}}} -->
		<div class="wp-block-column" style="padding-top:var(--wp--preset--spacing--70)">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
			<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/ruins-image.webp' ); ?>" alt="<?php esc_attr_e( 'Event image', 'twentytwentyfive' ); ?>"/></figure>
			<!-- /wp:image -->

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30)">
				<!-- wp:heading {"level":3,"style":{"spacing":{"padding":{"top":"var:preset|spacing|20"}}}} -->
				<h3 class="wp-block-heading" style="padding-top:var(--wp--preset--spacing--20)"><?php esc_html_e( 'Tell your story', 'twentytwentyfive' ); ?></h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|accent-4"}}}},"textColor":"accent-4","fontSize":"medium"} -->
				<p class="has-accent-4-color has-text-color has-link-color has-medium-font-size"><?php echo esc_html_x( 'Mon, Jan 1', 'Example event date in pattern.', 'twentytwentyfive' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:paragraph {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40"}}}} -->
			<p style="padding-top:var(--wp--preset--spacing--40)"><a href="#"><?php esc_html_e( 'Event details', 'twentytwentyfive' ); ?></a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"style":{"spacing":{"padding":{"top":"var:preset|spacing|70"},"blockGap":"0"}}} -->
		<div class="wp-block-column" style="padding-top:var(--wp--preset--spacing--70)">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
			<figure class="wp-block-image size-full"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/ruins-image.webp' ); ?>" alt="<?php esc_attr_e( 'Event image', 'twentytwentyfive' ); ?>"/></figure>
			<!-- /wp:image -->

			<!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|20","padding":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex","orientation":"vertical"}} -->
			<div class="wp-block-group" style="padding-top:var(--wp--preset--spacing--30)">
				<!-- wp:heading {"level":3,"style":{"spacing":{"padding":{"top":"var:preset|spacing|20"}}}} -->
				<h3 class="wp-block-heading" style="padding-top:var(--wp--preset--spacing--20)"><?php esc_html_e( 'Tell your story', 'twentytwentyfive' ); ?></h3>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"style":{"elements":{"link":{"color":{"text":"var:preset|color|accent-4"}}}},"textColor":"accent-4","fontSize":"medium"} -->
				<p class="has-accent-4-color has-text-color has-link-color has-medium-font-size"><?php echo esc_html_x( 'Mon, Jan 1', 'Example event date in pattern.', 'twentytwentyfive' ); ?></p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:paragraph {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40"}}}} -->
			<p style="padding-top:var(--wp--preset--spacing--40)"><a href="#"><?php esc_html_e( 'Event details', 'twentytwentyfive' ); ?></a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
