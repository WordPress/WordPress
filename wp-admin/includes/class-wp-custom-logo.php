<?php
/**
 * Administration API: WP_Custom_Logo class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.5.0
 */

/**
 * Core class used to implement custom logo functionality.
 *
 * @since 4.5.0
 */
class WP_Custom_Logo {

	/**
	 * Get current logo settings stored in theme mod.
	 *
	 * @since 4.5.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'wp_head', array( $this, 'head_text_styles' ) );
		add_action( 'delete_attachment', array( $this, 'delete_attachment_data' ) );
	}

	/**
	 * Hides header text on the front end if necessary.
	 *
	 * @since 4.5.0
	 * @access public
	 */
	public function head_text_styles() {
		// Bail if our theme supports custom headers.
		if ( current_theme_supports( 'custom-header' ) || get_theme_mod( 'custom_logo_header_text', true ) ) {
			return;
		}

		// Is Display Header Text unchecked? If so, hide the header text.
		?>
		<!-- Custom Logo: hide header text -->
		<style type="text/css">
			<?php echo sanitize_html_class( $this->header_text_classes() ); ?>  {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
			}
		</style>
		<?php
	}

	/**
	 * Reset the custom logo if the current logo is deleted in the media manager.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param int $post_id Post ID.
	 */
	public function delete_attachment_data( $post_id ) {
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id && $custom_logo_id == $post_id ) {
			remove_theme_mod( 'custom_logo' );
		}
	}

	/**
	 * Retrieves the header text classes.
	 *
	 * If not defined in add_theme_support(), defaults from Underscores will be used.
	 *
	 * @since 4.5.0
	 * @access protected
	 *
	 * @return string String of classes to hide.
	 */
	protected function header_text_classes() {
		$args = get_theme_support( 'custom-logo' );

		if ( isset( $args[0]['header-text'] ) ) {
			// Use any classes defined in add_theme_support().
			$classes = $args[0]['header-text'];
		} else {
			// Otherwise, use these defaults, which will work with any Underscores-based theme.
			$classes = array(
				'site-title',
				'site-description',
			);
		}

		// If there's an array of classes, reduce them to a string for output.
		if ( is_array( $classes ) ) {
			$classes = array_map( 'sanitize_html_class', $classes );
			$classes = (string) '.' . implode( ', .', $classes );
		} else {
			$classes = (string) '.' . $classes;
		}

		return $classes;
	}
}

/**
 * WP_Custom_Logo instance.
 *
 * @global WP_Custom_Logo $wp_custom_logo
 */
$GLOBALS['wp_custom_logo'] = new WP_Custom_Logo;
