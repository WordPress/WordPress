<?php
/**
 * Image with caption block pattern
 */
return array(
	'title'      => __( 'Image with caption', 'twentytwentytwo' ),
	'categories' => array( 'featured', 'columns', 'gallery' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"6rem","bottom":"6rem"}},"elements":{"link":{"color":{"text":"var:preset|color|background"}}}},"backgroundColor":"primary","textColor":"background","layout":{"inherit":true}} -->
					<div class="wp-block-group alignfull has-background-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:6rem;padding-bottom:6rem"><!-- wp:media-text {"mediaId":202,"mediaLink":"' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-gray.jpg","mediaType":"image","verticalAlignment":"bottom","imageFill":false} -->
					<div class="wp-block-media-text alignwide is-stacked-on-mobile is-vertically-aligned-bottom"><figure class="wp-block-media-text__media"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/bird-on-gray.jpg" alt="' . esc_attr__( 'Hummingbird illustration', 'twentytwentytwo' ) . '" class="wp-image-202 size-full"/></figure><div class="wp-block-media-text__content"><!-- wp:paragraph -->
					<p><strong>' . esc_html__( 'Hummingbird', 'twentytwentytwo' ) . '</strong></p>
					<!-- /wp:paragraph -->

					<!-- wp:paragraph -->
					<p>' . esc_html__( 'A beautiful bird featuring a surprising set of color feathers.', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph --></div></div>
					<!-- /wp:media-text --></div>
					<!-- /wp:group -->',
);
