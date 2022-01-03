<?php
/**
 * About page links (dark)
 */
return array(
	'title'      => __( 'About page links (dark)', 'twentytwentytwo' ),
	'categories' => array( 'pages', 'buttons' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"elements":{"link":{"color":{"text":"var:preset|color|background"}}},"spacing":{"padding":{"top":"10rem","bottom":"10rem"}}},"backgroundColor":"primary","textColor":"background","layout":{"inherit":false,"contentSize":"400px"}} -->
					<div class="wp-block-group alignfull has-background-color has-primary-background-color has-text-color has-background has-link-color" style="padding-top:10rem;padding-bottom:10rem;"><!-- wp:group -->
					<div class="wp-block-group">

					<!-- wp:image {"width":100,"height":100,"sizeSlug":"full","linkDestination":"none","className":"is-style-rounded"} -->
					<figure class="wp-block-image size-full is-resized is-style-rounded"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/icon-bird.jpg" alt="' . esc_attr__( 'Logo featuring a flying bird', 'twentytwentytwo' ) . '" width="100" height="100"/></figure>
					<!-- /wp:image -->

					<!-- wp:heading {"textAlign":"left","style":{"typography":{"fontSize":"var(--wp--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))"}}} -->
					<h2 class="has-text-align-left" style="font-size:var(--wp--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))">' . esc_html__( 'A trouble of hummingbirds', 'twentytwentytwo' ) . '</h2>
					<!-- /wp:heading -->

					<!-- wp:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:buttons {"contentJustification":"left"} -->
					<div class="wp-block-buttons is-content-justification-left"><!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-outline"} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline"><a class="wp-block-button__link" style="border-radius:6px">' . esc_html__( 'Watch our videos', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-outline"} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline"><a class="wp-block-button__link" style="border-radius:6px">' . esc_html__( 'Listen on iTunes Podcasts', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-outline"} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline"><a class="wp-block-button__link" style="border-radius:6px">' . esc_html__( 'Listen on Spotify', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-outline"} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline"><a class="wp-block-button__link" style="border-radius:6px">' . esc_html__( 'Support the show', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-outline"} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline"><a class="wp-block-button__link" style="border-radius:6px">' . esc_html__( 'About the hosts', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons --></div>
					<!-- /wp:group --></div>
					<!-- /wp:group -->',
);
