<?php
/**
 * Bird image
 *
 * This pattern is used only to reference a dynamic image URL.
 * It does not appear in the inserter.
 */
return array(
	'title'    => __( 'Heading and bird image', 'twentytwentytwo' ),
	'inserter' => false,
	'content'  => '<!-- wp:image {"align":"wide","width":2000,"height":474,"sizeSlug":"full","linkDestination":"none"} -->
					<figure class="wp-block-image alignwide size-full is-resized"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/flight-path-on-transparent-d.png" alt="' . esc_attr__( 'Illustration of a bird flying.', 'twentytwentytwo' ) . '" width="2000" height="474"/></figure>
					<!-- /wp:image -->',
);
