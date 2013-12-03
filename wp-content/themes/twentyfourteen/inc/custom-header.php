<?php
/**
 * Implement Custom Header functionality for Twenty Fourteen
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */

/**
 * Set up the WordPress core custom header settings.
 *
 * @since Twenty Fourteen 1.0
 *
 * @uses twentyfourteen_header_style()
 * @uses twentyfourteen_admin_header_style()
 * @uses twentyfourteen_admin_header_image()
 */
function twentyfourteen_custom_header_setup() {
	/**
	 * Filter Twenty Fourteen custom-header support arguments.
	 *
	 * @since Twenty Fourteen 1.0
	 *
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *
	 *     @type bool   $header_text            Whether to display custom header text. Default false.
	 *     @type int    $width                  Width in pixels of the custom header image. Default 1260.
	 *     @type int    $height                 Height in pixels of the custom header image. Default 240.
	 *     @type bool   $flex_height            Whether to allow flexible-height header images. Default true.
	 *     @type string $admin_head_callback    Callback function used to style the image displayed in
	 *                                          the Appearance > Header screen.
	 *     @type string $admin_preview_callback Callback function used to create the custom header markup in
	 *                                          the Appearance > Header screen.
	 * }
	 */
	add_theme_support( 'custom-header', apply_filters( 'twentyfourteen_custom_header_args', array(
		'header-text'            => false,
		'width'                  => 1260,
		'height'                 => 240,
		'flex-height'            => true,
		'admin-head-callback'    => 'twentyfourteen_admin_header_style',
		'admin-preview-callback' => 'twentyfourteen_admin_header_image',
	) ) );
}
add_action( 'after_setup_theme', 'twentyfourteen_custom_header_setup' );

if ( ! function_exists( 'twentyfourteen_admin_header_style' ) ) :
/**
 * Style the header image displayed on the Appearance > Header screen.
 *
 * @see twentyfourteen_custom_header_setup()
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		background-color: #000;
		border: none;
		max-width: 1230px;
		min-height: 48px;
	}
	#headimg h1 {
		font-family: lato, sans-serif;
		font-size: 18px;
		line-height: 1.3333333333;
		margin: 12px 0 12px 27px;
	}
	#headimg h1 a {
		color: #fff;
		text-decoration: none;
	}
	#headimg img {
		vertical-align: middle;
	}
	</style>
<?php
}
endif; // twentyfourteen_admin_header_style

if ( ! function_exists( 'twentyfourteen_admin_header_image' ) ) :
/**
 * Create the custom header image markup displayed on the Appearance > Header screen.
 *
 * @see twentyfourteen_custom_header_setup()
 *
 * @since Twenty Fourteen 1.0
 */
function twentyfourteen_admin_header_image() {
?>
	<div id="headimg">
		<?php if ( get_header_image() ) : ?>
		<img src="<?php header_image(); ?>" alt="">
		<?php endif; ?>
		<h1><a id="name" onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
	</div>
<?php
}
endif; // twentyfourteen_admin_header_image
