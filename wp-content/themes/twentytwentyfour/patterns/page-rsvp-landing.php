<?php
/**
 * Title: RSVP landing
 * Slug: twentytwentyfour/page-rsvp-landing
 * Categories: twentytwentyfour_page
 * Keywords: starter
 * Block Types: core/post-content
 * Post Types: page, wp_template
 * Viewport width: 1100
 * Description: A large RSVP heading sideways, a description, and a CTA button.
 */
?>

<!-- wp:group {"metadata":{"name":"<?php echo esc_html_x( 'RSVP Landing Page', 'Name of RSVP landing page pattern', 'twentytwentyfour' ); ?>"},"align":"full","style":{"spacing":{"margin":{"top":"0","bottom":"0"},"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"dimensions":{"minHeight":"100vh"}},"backgroundColor":"accent-4","layout":{"type":"flex","orientation":"vertical","verticalAlignment":"center","justifyContent":"center","flexWrap":"nowrap"}} -->
<div class="wp-block-group alignfull has-accent-4-background-color has-background" style="min-height:100vh;margin-top:0;margin-bottom:0;padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
	<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%">
			<!-- wp:group {"style":{"dimensions":{"minHeight":"100%"},"spacing":{"blockGap":"var:preset|spacing|50","margin":{"top":"0","bottom":"0"},"padding":{"right":"0","left":"0"}}},"layout":{"type":"default"}} -->
			<div class="wp-block-group" style="min-height:100%;margin-top:0;margin-bottom:0;padding-right:0;padding-left:0">
				<!-- wp:heading {"textAlign":"right","level":1,"style":{"typography":{"fontSize":"12rem","writingMode":"vertical-rl","lineHeight":"1"},"spacing":{"margin":{"right":"0","left":"calc( var(--wp--preset--spacing--20) * -1)"}}}} -->
					<h1 class="wp-block-heading has-text-align-right" style="margin-right:0;margin-left:calc( var(--wp--preset--spacing--20) * -1);font-size:12rem;line-height:1;writing-mode:vertical-rl"><?php echo esc_html_x( 'RSVP', 'Initials for ´please respond´', 'twentytwentyfour' ); ?></h1>
				<!-- /wp:heading -->
				<!-- wp:group {"style":{"spacing":{"padding":{"right":"0","left":"0"}}},"layout":{"type":"constrained","contentSize":"300px","justifyContent":"left"}} -->
				<div class="wp-block-group" style="padding-right:0;padding-left:0">
					<!-- wp:paragraph {"style":{"layout":{"selfStretch":"fixed","flexSize":"50%"}}} -->
						<p><?php echo esc_html_x( 'Experience the fusion of imagination and expertise with Études Arch Summit, February 2025.', 'RSVP call to action description', 'twentytwentyfour' ); ?></p>
					<!-- /wp:paragraph -->
					<!-- wp:buttons -->
					<div class="wp-block-buttons">
						<!-- wp:button -->
						<div class="wp-block-button">
							<a class="wp-block-button__link wp-element-button"><?php echo esc_html_x( 'Reserve your spot', 'Call to action button text for the reservation button', 'twentytwentyfour' ); ?></a>
						</div>
						<!-- /wp:button -->
					</div>
					<!-- /wp:buttons -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"verticalAlignment":"top","width":"60%"} -->
		<div class="wp-block-column is-vertically-aligned-top" style="flex-basis:60%">
				<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large","linkDestination":"none","className":"is-style-rounded"} -->
					<figure class="wp-block-image size-large is-style-rounded">
						<img src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/images/green-staircase.webp" alt="<?php esc_attr_e( 'Green staircase at Western University, London, Canada', 'twentytwentyfour' ); ?>" style="aspect-ratio:3/4;object-fit:cover" />
					</figure>
				<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
