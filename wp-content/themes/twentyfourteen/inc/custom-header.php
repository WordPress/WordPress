<?php
/**
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * @uses twentyfourteen_header_style()
 * @uses twentyfourteen_admin_header_style()
 * @uses twentyfourteen_admin_header_image()
 */
function twentyfourteen_custom_header_setup() {
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
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see twentyfourteen_custom_header_setup().
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
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see twentyfourteen_custom_header_setup().
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
