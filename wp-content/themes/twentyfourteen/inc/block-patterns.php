<?php
/**
 * Block Patterns
 *
 * @link https://developer.wordpress.org/reference/functions/register_block_pattern/
 * @link https://developer.wordpress.org/reference/functions/register_block_pattern_category/
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 3.2
 */

/**
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {

	register_block_pattern_category(
		'twentyfourteen',
		array( 'label' => esc_html__( 'Twenty Fourteen', 'twentyfourteen' ) )
	);
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {

	// Description
	register_block_pattern(
		'twentyfourteen/about',
		array(
			'title'         => esc_html__( 'About', 'twentyfourteen' ),
			'categories'    => array( 'twentyfourteen' ),
			'viewportWidth' => 1000,
			'content'       => '<!-- wp:image {"id":null,"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/images/person.jpg" alt="' . esc_attr__( 'A person standing in front of a lake', 'twentyfourteen' ) . '"/></figure><!-- /wp:image --><!-- wp:heading {"fontSize":"large","style":{"typography":{"lineHeight":"1.4"}}} --><h2 class="has-large-font-size" style="line-height:1.4">' . esc_html__( 'Hello, my name is Joan. I am passionate about writing, travel, and photography.', 'twentyfourteen' ) . '</h2><!-- /wp:heading --><!-- wp:paragraph --><p>' . esc_html__( 'I’ve traveled to over 60 countries, and have made many friends along the way. I created this website to keep track of the memories I’ve made in my years of traveling.', 'twentyfourteen' ) . '</p><!-- /wp:paragraph -->',
		)
	);

	// Description
	register_block_pattern(
		'twentyfourteen/list',
		array(
			'title'         => esc_html__( 'List', 'twentyfourteen' ),
			'categories'    => array( 'twentyfourteen' ),
			'viewportWidth' => 1000,
			'content'       => '<!-- wp:columns --><div class="wp-block-columns"><!-- wp:column {"width":"40%"} --><div class="wp-block-column" style="flex-basis:40%"><!-- wp:heading {"style":{"typography":{"fontSize":45}}} --><h2 style="font-size:45px">' . esc_html__( '2001', 'twentyfourteen' ) . '</h2><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p><em>' . esc_html__( 'My first year of traveling. I visited China, Thailand, Japan, and India.', 'twentyfourteen' ) . '</em></p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:separator {"color":"black","className":"is-style-wide"} --><hr class="wp-block-separator has-text-color has-background has-black-background-color has-black-color is-style-wide"/><!-- /wp:separator --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column {"width":"40%"} --><div class="wp-block-column" style="flex-basis:40%"><!-- wp:heading {"style":{"typography":{"fontSize":50}}} --><h2 style="font-size:50px">' . esc_html__( '2012', 'twentyfourteen' ) . '</h2><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p><em>' . esc_html__( 'In 2012, I expanded my travels to Europe. I spent time in Poland, Germany, Italy, France, and Spain.  ', 'twentyfourteen' ) . '</em></p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:separator {"color":"black","className":"is-style-wide"} --><hr class="wp-block-separator has-text-color has-background has-black-background-color has-black-color is-style-wide"/><!-- /wp:separator --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column {"width":"40%"} --><div class="wp-block-column" style="flex-basis:40%"><!-- wp:heading {"style":{"typography":{"fontSize":50}}} --><h2 style="font-size:50px">' . esc_html__( '2016', 'twentyfourteen' ) . '</h2><!-- /wp:heading --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:paragraph --><p><em>' . esc_html__( 'This year was devoted entirely to the western hemisphere. I went on a month-long road trip through the U.S.A. and Canada, and also visited Mexico, Brazil, and Colombia.', 'twentyfourteen' ) . '</em></p><!-- /wp:paragraph --></div><!-- /wp:column --></div><!-- /wp:columns -->',
		)
	);

	// Heading and paragraph with four images.
	register_block_pattern(
		'twentyfourteen/summary',
		array(
			'title'         => esc_html__( 'Summary', 'twentyfourteen' ),
			'categories'    => array( 'twentyfourteen' ),
			'viewportWidth' => 1000,
			'content'       => '<!-- wp:group {"backgroundColor":"light-gray"} --><div class="wp-block-group has-light-gray-background-color has-background"><!-- wp:spacer {"height":50} --><div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer --><!-- wp:heading {"level":3,"style":{"typography":{"lineHeight":"1.5"}}} --><h3 style="line-height:1.5">' . esc_html__( 'Traveling Nostalgia', 'twentyfourteen' ) . '</h3><!-- /wp:heading --><!-- wp:paragraph --><p><em>' . esc_html__( 'Here are some photos from my all-time favorite destinations.', 'twentyfourteen' ) . '</em></p><!-- /wp:paragraph --><!-- wp:columns --><div class="wp-block-columns"><!-- wp:column --><div class="wp-block-column"><!-- wp:image {"id":null,"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/images/sunset.jpg" alt="' . esc_html__( 'Photo of a sunset', 'twentyfourteen' ) . '"/><figcaption><span class="has-inline-color has-dark-gray-color">' . esc_html__( 'Sunset', 'twentyfourteen' ) . '</span></figcaption></figure><!-- /wp:image --><!-- wp:image {"id":null,"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/images/bridge.jpg" alt="' . esc_attr__( 'Photo of a bridge', 'twentyfourteen' ) . '"/><figcaption><span class="has-inline-color has-dark-gray-color">' . esc_html__( 'Bridge', 'twentyfourteen' ) . '</span></figcaption></figure><!-- /wp:image --></div><!-- /wp:column --><!-- wp:column --><div class="wp-block-column"><!-- wp:image {"id":null,"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/images/street.jpg" alt="' . esc_attr__( 'Photo of a streetscape', 'twentyfourteen' ) . '"/><figcaption><span class="has-inline-color has-dark-gray-color">' . esc_html__( 'Streetscape', 'twentyfourteen' ) . '</span></figcaption></figure><!-- /wp:image --><!-- wp:image {"id":null,"sizeSlug":"large","linkDestination":"none"} --><figure class="wp-block-image size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/images/clouds.jpg" alt="' . esc_attr__( 'Photo of a cloudy mountain', 'twentyfourteen' ) . '"/><figcaption><span class="has-inline-color has-dark-gray-color">' . esc_html__( 'Clouds', 'twentyfourteen' ) . '</span></figcaption></figure><!-- /wp:image --></div><!-- /wp:column --></div><!-- /wp:columns --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"style":{"border":{"radius":0}},"backgroundColor":"dark-gray"} --><div class="wp-block-button"><a class="wp-block-button__link has-dark-gray-background-color has-background no-border-radius">' . esc_html__( 'Read More', 'twentyfourteen' ) . '</a></div><!-- /wp:button --></div><!-- /wp:buttons --><!-- wp:spacer {"height":50} --><div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer --></div><!-- /wp:group -->',
		)
	);

	// Cover block with contact message.
	register_block_pattern(
		'twentyfourteen/contact',
		array(
			'title'         => esc_html__( 'Contact', 'twentyfourteen' ),
			'categories'    => array( 'twentyfourteen' ),
			'viewportWidth' => 1000,
			'content'       => '<!-- wp:cover {"customOverlayColor":"#e6f0e4","minHeight":450,"contentPosition":"center center","className":"is-style-default"} --><div class="wp-block-cover has-background-dim is-style-default" style="background-color:#e6f0e4;min-height:450px"><div class="wp-block-cover__inner-container"><!-- wp:spacer {"height":10} --><div style="height:10px" aria-hidden="true" class="wp-block-spacer"></div><!-- /wp:spacer --><!-- wp:paragraph {"align":"left","placeholder":"' . esc_attr__( 'Write title…', 'twentyfourteen' ) . '","textColor":"black","fontSize":"large"} --><p class="has-text-align-left has-black-color has-text-color has-large-font-size">' . esc_html__( 'GOT A QUESTION?', 'twentyfourteen' ) . '</p><!-- /wp:paragraph --><!-- wp:paragraph {"align":"left","textColor":"black","style":{"typography":{"fontSize":22}}} --><p class="has-text-align-left has-black-color has-text-color" style="font-size:22px">' . esc_html__( 'Don’t hesitate to reach out.', 'twentyfourteen' ) . '</p><!-- /wp:paragraph --><!-- wp:buttons --><div class="wp-block-buttons"><!-- wp:button {"style":{"border":{"radius":0}}} --><div class="wp-block-button"><a class="wp-block-button__link no-border-radius">' . esc_html__( 'GET IN TOUCH', 'twentyfourteen' ) . '</a></div><!-- /wp:button --></div><!-- /wp:buttons --></div></div><!-- /wp:cover -->',
		)
	);

}
