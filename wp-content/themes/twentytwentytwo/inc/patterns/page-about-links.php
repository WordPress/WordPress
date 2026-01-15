<?php
/**
 * About page links
 */
return array(
	'title'      => __( 'About page links', 'twentytwentytwo' ),
	'categories' => array( 'twentytwentytwo_pages', 'buttons' ),
	'content'    => '<!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"10rem","bottom":"10rem"}}},"layout":{"inherit":false,"contentSize":"400px"}} -->
					<div class="wp-block-group alignfull" style="padding-top:10rem;padding-bottom:10rem;"><!-- wp:image {"align":"center","width":100,"height":100,"sizeSlug":"full","linkDestination":"none","className":"is-style-rounded"} -->
					<div class="wp-block-image is-style-rounded"><figure class="aligncenter size-full is-resized"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/icon-bird.jpg" alt="' . esc_attr__( 'Logo featuring a flying bird', 'twentytwentytwo' ) . '" width="100" height="100"/></figure></div>
					<!-- /wp:image -->

					<!-- wp:group -->
					<div class="wp-block-group">

					<!-- wp:heading {"textAlign":"center","style":{"typography":{"fontSize":"var(--wp--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))"}}} -->
					<h2 class="has-text-align-center" style="font-size:var(--wp--custom--typography--font-size--huge, clamp(2.25rem, 4vw, 2.75rem))">' . esc_html__( 'Swoop', 'twentytwentytwo' ) . '</h2>
					<!-- /wp:heading -->

					<!-- wp:paragraph {"align":"center"} -->
					<p class="has-text-align-center">' . esc_html__( 'A podcast about birds', 'twentytwentytwo' ) . '</p>
					<!-- /wp:paragraph -->

					<!-- wp:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:buttons {"contentJustification":"left"} -->
					<div class="wp-block-buttons is-content-justification-left"><!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill"><a class="wp-block-button__link" style="border-radius:6px">' . esc_html__( 'Watch our videos', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill"><a class="wp-block-button__link" style="border-radius:6px">' . esc_html__( 'Listen on iTunes Podcasts', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill"><a class="wp-block-button__link" style="border-radius:6px">' . esc_html__( 'Listen on Spotify', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill"><a class="wp-block-button__link" style="border-radius:6px">' . esc_html__( 'Support the show', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button -->

					<!-- wp:button {"width":100,"style":{"border":{"radius":"6px"}},"className":"is-style-fill"} -->
					<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-fill"><a class="wp-block-button__link" style="border-radius:6px">' . esc_html__( 'About the hosts', 'twentytwentytwo' ) . '</a></div>
					<!-- /wp:button --></div>
					<!-- /wp:buttons --></div>
					<!-- /wp:group -->

					<!-- wp:spacer {"height":40} -->
					<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:social-links {"iconColor":"primary","iconColorValue":"var(--wp--preset--color--primary)","className":"is-style-logos-only","layout":{"type":"flex","justifyContent":"center"}} -->
					<ul class="wp-block-social-links has-icon-color is-style-logos-only"><!-- wp:social-link {"url":"#","service":"wordpress"} /-->

					<!-- wp:social-link {"url":"#","service":"facebook"} /-->

					<!-- wp:social-link {"url":"#","service":"twitter"} /-->

					<!-- wp:social-link {"url":"#","service":"instagram"} /--></ul>
					<!-- /wp:social-links --></div>
					<!-- /wp:group -->',
);
