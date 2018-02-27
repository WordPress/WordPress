<?php
/**
 * Customize API: WP_Customize_Custom_CSS_Setting class
 *
 * This handles validation, sanitization and saving of the value.
 *
 * @package WordPress
 * @subpackage Customize
 * @since 4.7.0
 */

/**
 * Custom Setting to handle WP Custom CSS.
 *
 * @since 4.7.0
 *
 * @see WP_Customize_Setting
 */
final class WP_Customize_Custom_CSS_Setting extends WP_Customize_Setting {

	/**
	 * The setting type.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	public $type = 'custom_css';

	/**
	 * Setting Transport
	 *
	 * @since 4.7.0
	 * @var string
	 */
	public $transport = 'postMessage';

	/**
	 * Capability required to edit this setting.
	 *
	 * @since 4.7.0
	 * @var string
	 */
	public $capability = 'edit_css';

	/**
	 * Stylesheet
	 *
	 * @since 4.7.0
	 * @var string
	 */
	public $stylesheet = '';

	/**
	 * WP_Customize_Custom_CSS_Setting constructor.
	 *
	 * @since 4.7.0
	 *
	 * @throws Exception If the setting ID does not match the pattern `custom_css[$stylesheet]`.
	 *
	 * @param WP_Customize_Manager $manager The Customize Manager class.
	 * @param string               $id      An specific ID of the setting. Can be a
	 *                                      theme mod or option name.
	 * @param array                $args    Setting arguments.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		if ( 'custom_css' !== $this->id_data['base'] ) {
			throw new Exception( 'Expected custom_css id_base.' );
		}
		if ( 1 !== count( $this->id_data['keys'] ) || empty( $this->id_data['keys'][0] ) ) {
			throw new Exception( 'Expected single stylesheet key.' );
		}
		$this->stylesheet = $this->id_data['keys'][0];
	}

	/**
	 * Add filter to preview post value.
	 *
	 * @since 4.7.9
	 *
	 * @return bool False when preview short-circuits due no change needing to be previewed.
	 */
	public function preview() {
		if ( $this->is_previewed ) {
			return false;
		}
		$this->is_previewed = true;
		add_filter( 'wp_get_custom_css', array( $this, 'filter_previewed_wp_get_custom_css' ), 9, 2 );
		return true;
	}

	/**
	 * Filter `wp_get_custom_css` for applying the customized value.
	 *
	 * This is used in the preview when `wp_get_custom_css()` is called for rendering the styles.
	 *
	 * @since 4.7.0
	 * @see wp_get_custom_css()
	 *
	 * @param string $css        Original CSS.
	 * @param string $stylesheet Current stylesheet.
	 * @return string CSS.
	 */
	public function filter_previewed_wp_get_custom_css( $css, $stylesheet ) {
		if ( $stylesheet === $this->stylesheet ) {
			$customized_value = $this->post_value( null );
			if ( ! is_null( $customized_value ) ) {
				$css = $customized_value;
			}
		}
		return $css;
	}

	/**
	 * Fetch the value of the setting. Will return the previewed value when `preview()` is called.
	 *
	 * @since 4.7.0
	 * @see WP_Customize_Setting::value()
	 *
	 * @return string
	 */
	public function value() {
		if ( $this->is_previewed ) {
			$post_value = $this->post_value( null );
			if ( null !== $post_value ) {
				return $post_value;
			}
		}
		$id_base = $this->id_data['base'];
		$value   = '';
		$post    = wp_get_custom_css_post( $this->stylesheet );
		if ( $post ) {
			$value = $post->post_content;
		}
		if ( empty( $value ) ) {
			$value = $this->default;
		}

		/** This filter is documented in wp-includes/class-wp-customize-setting.php */
		$value = apply_filters( "customize_value_{$id_base}", $value, $this );

		return $value;
	}

	/**
	 * Validate CSS.
	 *
	 * Checks for imbalanced braces, brackets, and comments.
	 * Notifications are rendered when the customizer state is saved.
	 *
	 * @since 4.7.0
	 * @since 4.9.0 Checking for balanced characters has been moved client-side via linting in code editor.
	 *
	 * @param string $css The input string.
	 * @return true|WP_Error True if the input was validated, otherwise WP_Error.
	 */
	public function validate( $css ) {
		$validity = new WP_Error();

		if ( preg_match( '#</?\w+#', $css ) ) {
			$validity->add( 'illegal_markup', __( 'Markup is not allowed in CSS.' ) );
		}

		if ( ! $validity->has_errors() ) {
			$validity = parent::validate( $css );
		}
		return $validity;
	}

	/**
	 * Store the CSS setting value in the custom_css custom post type for the stylesheet.
	 *
	 * @since 4.7.0
	 *
	 * @param string $css The input value.
	 * @return int|false The post ID or false if the value could not be saved.
	 */
	public function update( $css ) {
		if ( empty( $css ) ) {
			$css = '';
		}

		$r = wp_update_custom_css_post(
			$css, array(
				'stylesheet' => $this->stylesheet,
			)
		);

		if ( $r instanceof WP_Error ) {
			return false;
		}
		$post_id = $r->ID;

		// Cache post ID in theme mod for performance to avoid additional DB query.
		if ( $this->manager->get_stylesheet() === $this->stylesheet ) {
			set_theme_mod( 'custom_css_post_id', $post_id );
		}

		return $post_id;
	}
}
