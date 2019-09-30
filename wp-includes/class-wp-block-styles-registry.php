<?php
/**
 * Blocks API: WP_Block_Styles_Registry class
 *
 * @package WordPress
 * @subpackage Blocks
 * @since 5.3.0
 */

/**
 * Class used for interacting with block styles.
 *
 * @since 5.3.0
 */
final class WP_Block_Styles_Registry {
	/**
	 * Registered block styles, as `$block_name => $block_style_name => $block_style_properties` multidimensional arrays.
	 *
	 * @since 5.3.0
	 * @var array
	 */
	private $registered_block_styles = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @since 5.3.0
	 * @var WP_Block_Styles_Registry|null
	 */
	private static $instance = null;

	/**
	 * Registers a block style.
	 *
	 * @since 5.3.0
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param array  $style_properties Array containing the properties of the style name, label, style (name of the stylesheet to be enqueued), inline_style (string containing the CSS to be added).
	 *
	 * @return boolean True if the block style was registered with success and false otherwise.
	 */
	public function register( $block_name, $style_properties ) {

		if ( ! isset( $block_name ) || ! is_string( $block_name ) ) {
			$message = __( 'Block name name must be a string.' );
			_doing_it_wrong( __METHOD__, $message, '5.3.0' );
			return false;
		}

		if ( ! isset( $style_properties['name'] ) || ! is_string( $style_properties['name'] ) ) {
			$message = __( 'Block style name must be a string.' );
			_doing_it_wrong( __METHOD__, $message, '5.3.0' );
			return false;
		}

		$block_style_name = $style_properties['name'];

		if ( ! isset( $this->registered_block_styles[ $block_name ] ) ) {
			$this->registered_block_styles[ $block_name ] = array();
		}
		$this->registered_block_styles[ $block_name ][ $block_style_name ] = $style_properties;

		return true;
	}

	/**
	 * Unregisters a block style.
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param array  $block_style_name Block style name.
	 *
	 * @return boolean True if the block style was unregistered with success and false otherwise.
	 */
	public function unregister( $block_name, $block_style_name ) {
		if ( ! $this->is_registered( $block_name, $block_style_name ) ) {
			/* translators: 1: block name, 2: block style name */
			$message = sprintf( __( 'Block "%1$s" does not contain a style named "%2$s.".' ), $block_name, $block_style_name );
			_doing_it_wrong( __METHOD__, $message, '5.3.0' );
			return false;
		}

		unset( $this->registered_block_styles[ $block_name ][ $block_style_name ] );

		return true;
	}

	/**
	 * Retrieves an array containing the properties of a registered block style.
	 *
	 * @since 5.3.0
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param array  $block_style_name Block style name.
	 *
	 * @return array Registered block style properties.
	 */
	public function get_registered( $block_name, $block_style_name ) {
		if ( ! $this->is_registered( $block_name, $block_style_name ) ) {
			return null;
		}

		return $this->registered_block_styles[ $block_name ][ $block_style_name ];
	}

	/**
	 * Retrieves all registered block styles.
	 *
	 * @since 5.3.0
	 *
	 * @return array Array of arrays containing the registered block styles properties grouped per block, and per style.
	 */
	public function get_all_registered() {
		return $this->registered_block_styles;
	}

	/**
	 * Retrieves registered block styles for a specific block.
	 *
	 * @since 5.3.0
	 *
	 * @param string $block_name Block type name including namespace.
	 *
	 * @return array Array whose keys are block style names and whose value are block style properties.
	 */
	public function get_registered_styles_for_block( $block_name ) {
		if ( isset( $this->registered_block_styles[ $block_name ] ) ) {
			return $this->registered_block_styles[ $block_name ];
		}
		return array();
	}

	/**
	 * Checks if a block style is registered.
	 *
	 * @since 5.3.0
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param array  $block_style_name Block style name.
	 *
	 * @return bool True if the block style is registered, false otherwise.
	 */
	public function is_registered( $block_name, $block_style_name ) {
		return isset( $this->registered_block_styles[ $block_name ][ $block_style_name ] );
	}

	/**
	 * Utility method to retrieve the main instance of the class.
	 *
	 * The instance will be created if it does not exist yet.
	 *
	 * @since 5.3.0
	 *
	 * @return WP_Block_Styles_Registry The main instance.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}
