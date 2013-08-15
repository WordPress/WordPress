<?php
/**
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @todo Rework this function to remove WordPress 3.4 support when WordPress 3.6 is released.
 *
 * @uses twentyfourteen_header_style()
 * @uses twentyfourteen_admin_header_style()
 * @uses twentyfourteen_admin_header_image()
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 */
function twentyfourteen_custom_header_setup() {
	add_theme_support( 'custom-header', apply_filters( 'twentyfourteen_custom_header_args', array(
		'default-text-color'     => 'fff',
		'width'                  => 1260,
		'height'                 => 240,
		'flex-height'            => true,
		'wp-head-callback'       => 'twentyfourteen_header_style',
		'admin-head-callback'    => 'twentyfourteen_admin_header_style',
		'admin-preview-callback' => 'twentyfourteen_admin_header_image',
	) ) );

}
add_action( 'after_setup_theme', 'twentyfourteen_custom_header_setup' );

if ( ! function_exists( 'twentyfourteen_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see twentyfourteen_custom_header_setup().
 *
 */
function twentyfourteen_header_style() {
	$header_text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail
	// $header_text_color options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == $header_text_color )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == $header_text_color ) :
	?>
		.site-title {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		.site-title a  {
			color: #<?php echo $header_text_color; ?> !important;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // twentyfourteen_header_style

if ( ! function_exists( 'twentyfourteen_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see twentyfourteen_custom_header_setup().
 *
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
 *
 */
function twentyfourteen_admin_header_image() {
	$header_image = get_header_image();
?>
	<div id="headimg">
		<?php if ( ! empty( $header_image ) ) : ?>
		<img src="<?php echo esc_url( $header_image ); ?>" alt="">
		<?php endif; ?>
		<h1 class="displaying-header-text"><a id="name"<?php echo sprintf( ' style="color:#%s;"', get_header_textcolor() ); ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
	</div>
<?php
}
endif; // twentyfourteen_admin_header_image
