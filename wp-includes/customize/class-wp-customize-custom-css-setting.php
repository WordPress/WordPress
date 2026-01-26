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
	 * @param WP_Customize_Manager $manager Customizer bootstrap instance.
	 * @param string               $id      A specific ID of the setting.
	 *                                      Can be a theme mod or option name.
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
	 * Filters `wp_get_custom_css` for applying the customized value.
	 *
	 * This is used in the preview when `wp_get_custom_css()` is called for rendering the styles.
	 *
	 * @since 4.7.0
	 *
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
	 *
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
	 * Validate a received value for being valid CSS.
	 *
	 * Checks for imbalanced braces, brackets, and comments.
	 * Notifications are rendered when the customizer state is saved.
	 *
	 * @since 4.7.0
	 * @since 4.9.0 Checking for balanced characters has been moved client-side via linting in code editor.
	 * @since 5.9.0 Renamed `$css` to `$value` for PHP 8 named parameter support.
	 * @since 7.0.0 Only restricts contents which risk prematurely closing the STYLE element,
	 *              either through a STYLE end tag or a prefix of one which might become a
	 *              full end tag when combined with the contents of other styles.
	 *
	 * @see WP_REST_Global_Styles_Controller::validate_custom_css()
	 *
	 * @param string $value CSS to validate.
	 * @return true|WP_Error True if the input was validated, otherwise WP_Error.
	 */
	public function validate( $value ) {
		// Restores the more descriptive, specific name for use within this method.
		$css = $value;

		$validity = new WP_Error();

		$length = strlen( $css );
		for (
			$at = strcspn( $css, '<' );
			$at < $length;
			$at += strcspn( $css, '<', ++$at )
		) {
			$remaining_strlen = $length - $at;
			/**
			 * Custom CSS text is expected to render inside an HTML STYLE element.
			 * A STYLE closing tag must not appear within the CSS text because it
			 * would close the element prematurely.
			 *
			 * The text must also *not* end with a partial closing tag (e.g., `<`,
			 * `</`, … `</style`) because subsequent styles which are concatenated
			 * could complete it, forming a valid `</style>` tag.
			 *
			 * Example:
			 *
			 *     $style_a = 'p { font-weight: bold; </sty';
			 *     $style_b = 'le> gotcha!';
			 *     $combined = "{$style_a}{$style_b}";
			 *
			 *     $style_a = 'p { font-weight: bold; </style';
			 *     $style_b = 'p > b { color: red; }';
			 *     $combined = "{$style_a}\n{$style_b}";
			 *
			 * Note how in the second example, both of the style contents are benign
			 * when analyzed on their own. The first style was likely the result of
			 * improper truncation, while the second is perfectly sound. It was only
			 * through concatenation that these two styles combined to form content
			 * that would have broken out of the containing STYLE element, thus
			 * corrupting the page and potentially introducing security issues.
			 *
			 * @see https://html.spec.whatwg.org/multipage/parsing.html#rawtext-end-tag-name-state
			 */
			$possible_style_close_tag = 0 === substr_compare(
				$css,
				'</style',
				$at,
				min( 7, $remaining_strlen ),
				true
			);
			if ( $possible_style_close_tag ) {
				if ( $remaining_strlen < 8 ) {
					$validity->add(
						'illegal_markup',
						sprintf(
							/* translators: %s is the CSS that was provided. */
							__( 'The CSS must not end in "%s".' ),
							esc_html( substr( $css, $at ) )
						)
					);
					break;
				}

				if ( 1 === strspn( $css, " \t\f\r\n/>", $at + 7, 1 ) ) {
					$validity->add(
						'illegal_markup',
						sprintf(
							/* translators: %s is the CSS that was provided. */
							__( 'The CSS must not contain "%s".' ),
							esc_html( substr( $css, $at, 8 ) )
						)
					);
					break;
				}
			}
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
	 * @since 5.9.0 Renamed `$css` to `$value` for PHP 8 named parameter support.
	 *
	 * @param string $value CSS to update.
	 * @return int|false The post ID or false if the value could not be saved.
	 */
	public function update( $value ) {
		// Restores the more descriptive, specific name for use within this method.
		$css = $value;

		if ( empty( $css ) ) {
			$css = '';
		}

		$r = wp_update_custom_css_post(
			$css,
			array(
				'stylesheet' => $this->stylesheet,
			)
		);

		if ( is_wp_error( $r ) ) {
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
