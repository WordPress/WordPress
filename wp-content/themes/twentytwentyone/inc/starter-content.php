<?php
/**
 * Twenty Twenty-One Starter Content
 *
 * @link https://make.wordpress.org/core/2016/11/30/starter-content-for-themes-in-4-7/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_One
 * @since 1.0.0
 */

/**
 * Function to return the array of starter content for the theme.
 *
 * Passes it through the `twentytwenty_starter_content` filter before returning.
 *
 * @since 1.0.0
 *
 * @return array A filtered array of args for the starter_content.
 */
function twenty_twenty_one_get_starter_content() {

	// Define and register starter content to showcase the theme on new sites.
	$starter_content = array(

		// Specify the core-defined pages to create and add custom thumbnails to some of them.
		'posts'     => array(
			'front' => array(
				'post_type'    => 'page',
				'post_title'   => esc_html_x( 'Create your website with blocks', 'Theme starter content', 'twentytwentyone' ),
				'post_content' => '
					<!-- wp:heading {"align":"wide","fontSize":"gigantic","style":{"typography":{"lineHeight":"1.1"}}} -->
					<h2 class="alignwide has-text-align-wide has-gigantic-font-size" style="line-height:1.1">' . esc_html_x( 'Create your website with blocks', 'Theme starter content', 'twentytwentyone' ) . '</h2>
					<!-- /wp:heading -->

					<!-- wp:spacer -->
					<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"is-style-twentytwentyone-columns-overlap"} -->
					<div class="wp-block-columns alignwide are-vertically-aligned-center is-style-twentytwentyone-columns-overlap"><!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:image {"align":"full","sizeSlug":"large"} -->
					<figure class="wp-block-image alignfull size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/roses-tremieres-hollyhocks.jpg" alt="' . esc_attr__( 'Roses Tremieres (Hollyhocks), by Berthe Morisot, 1884', 'twentytwentyone' ) . '"/></figure>
					<!-- /wp:image -->

					<!-- wp:spacer -->
					<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:image {"align":"full","sizeSlug":"large","className":"is-style-twentytwentyone-image-frame"} -->
					<figure class="wp-block-image alignfull size-large is-style-twentytwentyone-image-frame"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/in-the-bois-de-boulogne.jpg" alt="' . esc_attr__( 'In the Bois de Boulogne, by Berthe Morisot, 1879', 'twentytwentyone' ) . '"/></figure>
					<!-- /wp:image --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"center"} -->
					<div class="wp-block-column is-vertically-aligned-center"><!-- wp:spacer -->
					<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:image {"sizeSlug":"large","className":"alignfull size-full is-style-twentytwentyone-border"} -->
					<figure class="wp-block-image size-large alignfull size-full is-style-twentytwentyone-border"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/young-woman-in-mauve.jpg" alt="' . esc_attr__( 'Young Woman in Mauve, by Berthe Morisot, 1880', 'twentytwentyone' ) . '"/></figure>
					<!-- /wp:image --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:spacer {"height":50} -->
					<div style="height:50px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:columns {"verticalAlignment":"top","align":"wide"} -->
					<div class="wp-block-columns alignwide are-vertically-aligned-top"><!-- wp:column {"verticalAlignment":"top"} -->
					<div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"level":3} -->
					<h3>' . esc_html_x( 'Add block patterns', 'Theme starter content', 'twentytwentyone' ) . '</h3>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>' . esc_html_x( 'Block patterns are pre-designed groups of blocks. To add one, select the Add Block button [+] in the toolbar at the top of the editor. Switch to the Patterns tab underneath the search bar, and choose a pattern.', 'Theme starter content', 'twentytwentyone' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"top"} -->
					<div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"level":3} -->
					<h3>' . esc_html_x( 'Frame your images', 'Theme starter content', 'twentytwentyone' ) . '</h3>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>' . esc_html_x( 'Twenty Twenty-One includes stylish borders for your content. With an Image block selected, open the "Styles" panel within the Editor sidebar. Select the "Frame" block style to activate it.', 'Theme starter content', 'twentytwentyone' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column {"verticalAlignment":"top"} -->
					<div class="wp-block-column is-vertically-aligned-top"><!-- wp:heading {"level":3} -->
					<h3>' . esc_html_x( 'Overlap columns', 'Theme starter content', 'twentytwentyone' ) . '</h3>
					<!-- /wp:heading -->

					<!-- wp:paragraph -->
					<p>' . esc_html_x( 'Twenty Twenty-One also includes an overlap style for column blocks. With a Columns block selected, open the "Styles" panel within the Editor sidebar. Choose the "Overlap" block style to try it out.', 'Theme starter content', 'twentytwentyone' ) . '</p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:spacer -->
					<div style="height:100px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:cover {"overlayColor":"green","contentPosition":"center center","align":"wide","className":"is-style-twentytwentyone-border"} -->
					<div class="wp-block-cover alignwide has-green-background-color has-background-dim is-style-twentytwentyone-border"><div class="wp-block-cover__inner-container"><!-- wp:spacer {"height":20} -->
					<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:paragraph {"fontSize":"huge"} -->
					<p class="has-huge-font-size">' . esc_html_x( 'Need help?', 'Theme starter content', 'twentytwentyone' ) . '</p>
					<!-- /wp:paragraph -->

					<!-- wp:spacer {"height":75} -->
					<div style="height:75px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer -->

					<!-- wp:columns -->
					<div class="wp-block-columns"><!-- wp:column -->
					<div class="wp-block-column"><!-- wp:paragraph -->
					<p><a href="https://wordpress.org/support/article/twenty-twenty-one/">' . esc_html_x( 'Read the Theme Documentation', 'Theme starter content', 'twentytwentyone' ) . '</a></p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column -->

					<!-- wp:column -->
					<div class="wp-block-column"><!-- wp:paragraph -->
					<p><a href="https://wordpress.org/support/theme/twentytwentyone/">' . esc_html_x( 'Check out the Support Forums', 'Theme starter content', 'twentytwentyone' ) . '</a></p>
					<!-- /wp:paragraph --></div>
					<!-- /wp:column --></div>
					<!-- /wp:columns -->

					<!-- wp:spacer {"height":20} -->
					<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
					<!-- /wp:spacer --></div></div>
					<!-- /wp:cover -->',
			),
			'about',
			'contact',
			'blog',
		),

		// Default to a static front page and assign the front and posts pages.
		'options'   => array(
			'show_on_front'  => 'page',
			'page_on_front'  => '{{front}}',
			'page_for_posts' => '{{blog}}',
		),

		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus' => array(
			// Assign a menu to the "primary" location.
			'primary' => array(
				'name'  => esc_html__( 'Primary', 'twentytwentyone' ),
				'items' => array(
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
					'page_contact',
				),
			),

			// Assign a menu to the "footer" location.
			'footer'  => array(
				'name'  => esc_html__( 'Footer', 'twentytwentyone' ),
				'items' => array(
					'link_facebook',
					'link_twitter',
					'link_instagram',
					'link_email',
				),
			),
		),
	);

	/**
	 * Filters the array of starter content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $starter_content Array of starter content.
	 */
	return apply_filters( 'twenty_twenty_one_starter_content', $starter_content );
}
