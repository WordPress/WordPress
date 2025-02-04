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
#[AllowDynamicProperties]
final class WP_Block_Styles_Registry {
	/**
	 * Registered block styles, as `$block_name => $block_style_name => $block_style_properties` multidimensional arrays.
	 *
	 * @since 5.3.0
	 *
	 * @var array[]
	 */
	private $registered_block_styles = array();

	/**
	 * Container for the main instance of the class.
	 *
	 * @since 5.3.0
	 *
	 * @var WP_Block_Styles_Registry|null
	 */
	private static $instance = null;

	/**
	 * Registers a block style for the given block type.
	 *
	 * If the block styles are present in a standalone stylesheet, register it and pass
	 * its handle as the `style_handle` argument. If the block styles should be inline,
	 * use the `inline_style` argument. Usually, one of them would be used to pass CSS
	 * styles. However, you could also skip them and provide CSS styles in any stylesheet
	 * or with an inline tag.
	 *
	 * @since 5.3.0
	 * @since 6.6.0 Added ability to register style across multiple block types along with theme.json-like style data.
	 *
	 * @link https://developer.wordpress.org/block-editor/reference-guides/block-api/block-styles/
	 *
	 * @param string|string[] $block_name       Block type name including namespace or array of namespaced block type names.
	 * @param array           $style_properties {
	 *     Array containing the properties of the style.
	 *
	 *     @type string $name         The identifier of the style used to compute a CSS class.
	 *     @type string $label        A human-readable label for the style.
	 *     @type string $inline_style Inline CSS code that registers the CSS class required
	 *                                for the style.
	 *     @type string $style_handle The handle to an already registered style that should be
	 *                                enqueued in places where block styles are needed.
	 *     @type bool   $is_default   Whether this is the default style for the block type.
	 *     @type array  $style_data   Theme.json-like object to generate CSS from.
	 * }
	 * @return bool True if the block style was registered with success and false otherwise.
	 */
	public function register( $block_name, $style_properties ) {

		if ( ! is_string( $block_name ) && ! is_array( $block_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Block name must be a string or array.' ),
				'6.6.0'
			);
			return false;
		}

		if ( ! isset( $style_properties['name'] ) || ! is_string( $style_properties['name'] ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Block style name must be a string.' ),
				'5.3.0'
			);
			return false;
		}

		if ( str_contains( $style_properties['name'], ' ' ) ) {
			_doing_it_wrong(
				__METHOD__,
				__( 'Block style name must not contain any spaces.' ),
				'5.9.0'
			);
			return false;
		}

		$block_style_name = $style_properties['name'];
		$block_names      = is_string( $block_name ) ? array( $block_name ) : $block_name;

		// Ensure there is a label defined.
		if ( empty( $style_properties['label'] ) ) {
			$style_properties['label'] = $block_style_name;
		}

		foreach ( $block_names as $name ) {
			if ( ! isset( $this->registered_block_styles[ $name ] ) ) {
				$this->registered_block_styles[ $name ] = array();
			}
			$this->registered_block_styles[ $name ][ $block_style_name ] = $style_properties;
		}

		return true;
	}

	/**
	 * Unregisters a block style of the given block type.
	 *
	 * @since 5.3.0
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param string $block_style_name Block style name.
	 * @return bool True if the block style was unregistered with success and false otherwise.
	 */
	public function unregister( $block_name, $block_style_name ) {
		if ( ! $this->is_registered( $block_name, $block_style_name ) ) {
			_doing_it_wrong(
				__METHOD__,
				/* translators: 1: Block name, 2: Block style name. */
				sprintf( __( 'Block "%1$s" does not contain a style named "%2$s".' ), $block_name, $block_style_name ),
				'5.3.0'
			);
			return false;
		}

		unset( $this->registered_block_styles[ $block_name ][ $block_style_name ] );

		return true;
	}

	/**
	 * Retrieves the properties of a registered block style for the given block type.
	 *
	 * @since 5.3.0
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param string $block_style_name Block style name.
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
	 * @return array[] Array of arrays containing the registered block styles properties grouped by block type.
	 */
	public function get_all_registered() {
		return $this->registered_block_styles;
	}

	/**
	 * Retrieves registered block styles for a specific block type.
	 *
	 * @since 5.3.0
	 *
	 * @param string $block_name Block type name including namespace.
	 * @return array[] Array whose keys are block style names and whose values are block style properties.
	 */
	public function get_registered_styles_for_block( $block_name ) {
		if ( isset( $this->registered_block_styles[ $block_name ] ) ) {
			return $this->registered_block_styles[ $block_name ];
		}
		return array();
	}

	/**
	 * Checks if a block style is registered for the given block type.
	 *
	 * @since 5.3.0
	 *
	 * @param string $block_name       Block type name including namespace.
	 * @param string $block_style_name Block style name.
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
