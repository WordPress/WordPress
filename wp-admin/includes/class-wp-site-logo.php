<?php
/**
 * Administration API: WP_Site_Logo class
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.5.0
 */

/**
 * Core class used to implement site logo functionality.
 *
 * @since 4.5.0
 */
class WP_Site_Logo {

	/**
	 * Get current logo settings stored in theme mod.
	 *
	 * @since 4.5.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'wp_head', array( $this, 'head_text_styles' ) );
		add_action( 'delete_attachment', array( $this, 'delete_attachment_data' ) );
		add_filter( 'image_size_names_choose', array( $this, 'media_manager_image_sizes' ) );
	}

	/**
	 * Hides header text on the front end if necessary.
	 *
	 * @since 4.5.0
	 * @access public
	 */
	public function head_text_styles() {
		// Bail if our theme supports custom headers.
		if ( current_theme_supports( 'custom-header' ) || get_theme_mod( 'site_logo_header_text', true ) ) {
			return;
		}

		// Is Display Header Text unchecked? If so, hide the header text.
		?>
		<!-- Site Logo: hide header text -->
		<style type="text/css">
			<?php echo sanitize_html_class( $this->header_text_classes() ); ?>  {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
			}
		</style>
		<?php
	}

	/**
	 * Resets the site logo if the current logo is deleted in the media manager.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param int $post_id Post ID.
	 */
	public function delete_attachment_data( $post_id ) {
		$site_logo_id = get_theme_mod( 'site_logo' );

		if ( $site_logo_id && $site_logo_id == $post_id ) {
			remove_theme_mod( 'site_logo' );
		}
	}

	/**
	 * Makes custom image sizes available to the media manager.
	 *
	 * @since 4.5.0
	 * @access public
	 *
	 * @param array $sizes Image sizes.
	 * @return array All default and registered custom image sizes.
	 */
	public function media_manager_image_sizes( $sizes ) {

		// Get an array of all registered image sizes.
		$intermediate = get_intermediate_image_sizes();

		// Is there anything fun to work with?
		if ( is_array( $intermediate ) && ! empty( $intermediate ) ) {
			foreach ( $intermediate as $key => $size ) {

				// If the size isn't already in the $sizes array, add it.
				if ( ! array_key_exists( $size, $sizes ) ) {
					$sizes[ $size ] = $size;
				}
			}
		}

		return $sizes;
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
		$args = get_theme_support( 'site-logo' );

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
 * WP_Site_Logo instance.
 *
 * @global WP_Site_Logo $wp_site_logo
 */
$GLOBALS['wp_site_logo'] = new WP_Site_Logo;
