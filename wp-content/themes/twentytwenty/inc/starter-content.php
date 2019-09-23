<?php
/**
 * Twenty Twenty Starter Content
 *
 * @link https://make.wordpress.org/core/2016/11/30/starter-content-for-themes-in-4-7/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since 1.0.0
 */

/**
 * Function to return the array of starter content for the theme.
 *
 * Passes it through the `twentytwenty_starter_content` filter before returning.
 *
 * @since  Twenty Twenty 1.0.0
 * @return array a filtered array of args for the starter_content.
 */
function twentytwenty_get_starter_content() {

	// Define and register starter content to showcase the theme on new sites.
	$starter_content = array(
		'widgets'   => array(
			// Place one core-defined widgets in the first tooter widget area.
			'footer-one' => array(
				'text_about',
			),
			// Place one core-defined widgets in the second tooter widget area.
			'footer-two' => array(
				'text_business_info',
			),
		),

		// Specify the core-defined pages to create and add custom thumbnails to some of them.
		'posts'     => array(
			'home',
			'about',
			'contact',
			'blog',
		),

		// Default to a static front page and assign the front and posts pages.
		'options'   => array(
			'show_on_front'  => 'page',
			'page_on_front'  => '{{home}}',
			'page_for_posts' => '{{blog}}',
		),

		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus' => array(
			// Assign a menu to the "primary" location.
			'primary'  => array(
				'name'  => __( 'Primary', 'twentytwenty' ),
				'items' => array(
					'page_contact',
				),
			),
			// Assign a menu to the "expanded" (modal) menu location.
			'expanded' => array(
				'name'  => __( 'Primary', 'twentytwenty' ),
				'items' => array(
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
				),
			),
			// Assign a menu to the "social" location.
			'social'   => array(
				'name'  => __( 'Social Links Menu', 'twentytwenty' ),
				'items' => array(
					'link_yelp',
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_email',
				),
			),
		),
	);

	/**
	 * Filters Twenty Twenty array of starter content.
	 *
	 * @since Twenty Twenty 1.0.0
	 *
	 * @param array $starter_content Array of starter content.
	 */
	return apply_filters( 'twentytwenty_starter_content', $starter_content );

}
