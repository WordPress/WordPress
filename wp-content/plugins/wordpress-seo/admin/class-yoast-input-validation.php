<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin
 */

/**
 * Implements server-side user input validation.
 *
 * @since 12.0
 */
class Yoast_Input_Validation {

	/**
	 * The error descriptions.
	 *
	 * @since 12.1
	 *
	 * @var array<string, string>
	 */
	private static $error_descriptions = [];

	/**
	 * Check whether an option group is a Yoast SEO setting.
	 *
	 * The normal pattern is 'yoast' . $option_name . 'options'.
	 *
	 * @since 12.0
	 *
	 * @param string $group_name The option group name.
	 *
	 * @return bool Whether or not it's an Yoast SEO option group.
	 */
	public static function is_yoast_option_group_name( $group_name ) {
		return ( strpos( $group_name, 'yoast' ) !== false );
	}

	/**
	 * Adds an error message to the document title when submitting a settings
	 * form and errors are returned.
	 *
	 * Uses the WordPress `admin_title` filter in the WPSEO_Option subclasses.
	 *
	 * @since 12.0
	 *
	 * @param string $admin_title The page title, with extra context added.
	 *
	 * @return string The modified or original admin title.
	 */
	public static function add_yoast_admin_document_title_errors( $admin_title ) {
		$errors      = get_settings_errors();
		$error_count = 0;

		foreach ( $errors as $error ) {
			// For now, filter the admin title only in the Yoast SEO settings pages.
			if ( self::is_yoast_option_group_name( $error['setting'] ) && $error['code'] !== 'settings_updated' ) {
				++$error_count;
			}
		}

		if ( $error_count > 0 ) {
			return sprintf(
				/* translators: %1$s: amount of errors, %2$s: the admin page title */
				_n( 'The form contains %1$s error. %2$s', 'The form contains %1$s errors. %2$s', $error_count, 'wordpress-seo' ),
				number_format_i18n( $error_count ),
				$admin_title
			);
		}

		return $admin_title;
	}

	/**
	 * Checks whether a specific form input field was submitted with an invalid value.
	 *
	 * @since 12.1
	 *
	 * @param string $error_code Must be the same slug-name used for the field variable and for `add_settings_error()`.
	 *
	 * @return bool Whether or not the submitted input field contained an invalid value.
	 */
	public static function yoast_form_control_has_error( $error_code ) {
		$errors = get_settings_errors();

		foreach ( $errors as $error ) {
			if ( $error['code'] === $error_code ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Sets the error descriptions.
	 *
	 * @since      12.1
	 * @deprecated 23.3
	 * @codeCoverageIgnore
	 *
	 * @param array<string, string> $descriptions An associative array of error descriptions.
	 *                                            For each entry, the key must be the setting variable.
	 *
	 * @return void
	 */
	public static function set_error_descriptions( $descriptions = [] ) { // @phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable, Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Needed for BC.
		_deprecated_function( __METHOD__, 'Yoast SEO 23.3' );
	}

	/**
	 * Gets all the error descriptions.
	 *
	 * @since      12.1
	 * @deprecated 23.3
	 * @codeCoverageIgnore
	 *
	 * @return array<string, string> An associative array of error descriptions.
	 */
	public static function get_error_descriptions() {
		_deprecated_function( __METHOD__, 'Yoast SEO 23.3' );
		return [];
	}

	/**
	 * Gets a specific error description.
	 *
	 * @since 12.1
	 *
	 * @param string $error_code Code of the error set via `add_settings_error()`, normally the variable name.
	 *
	 * @return string|null The error description.
	 */
	public static function get_error_description( $error_code ) {
		if ( ! isset( self::$error_descriptions[ $error_code ] ) ) {
			return null;
		}

		return self::$error_descriptions[ $error_code ];
	}

	/**
	 * Gets the aria-invalid HTML attribute based on the submitted invalid value.
	 *
	 * @since 12.1
	 *
	 * @param string $error_code Code of the error set via `add_settings_error()`, normally the variable name.
	 *
	 * @return string The aria-invalid HTML attribute or empty string.
	 */
	public static function get_the_aria_invalid_attribute( $error_code ) {
		if ( self::yoast_form_control_has_error( $error_code ) ) {
			return ' aria-invalid="true"';
		}

		return '';
	}

	/**
	 * Gets the aria-describedby HTML attribute based on the submitted invalid value.
	 *
	 * @since 12.1
	 *
	 * @param string $error_code Code of the error set via `add_settings_error()`, normally the variable name.
	 *
	 * @return string The aria-describedby HTML attribute or empty string.
	 */
	public static function get_the_aria_describedby_attribute( $error_code ) {
		if ( self::yoast_form_control_has_error( $error_code ) && self::get_error_description( $error_code ) ) {
			return ' aria-describedby="' . esc_attr( $error_code ) . '-error-description"';
		}

		return '';
	}

	/**
	 * Gets the error description wrapped in a HTML paragraph.
	 *
	 * @since 12.1
	 *
	 * @param string $error_code Code of the error set via `add_settings_error()`, normally the variable name.
	 *
	 * @return string The error description HTML or empty string.
	 */
	public static function get_the_error_description( $error_code ) {
		$error_description = self::get_error_description( $error_code );

		if ( self::yoast_form_control_has_error( $error_code ) && $error_description ) {
			return '<p id="' . esc_attr( $error_code ) . '-error-description" class="yoast-input-validation__error-description">' . $error_description . '</p>';
		}

		return '';
	}

	/**
	 * Adds the submitted invalid value to the WordPress `$wp_settings_errors` global.
	 *
	 * @since 12.1
	 *
	 * @param string $error_code  Code of the error set via `add_settings_error()`, normally the variable name.
	 * @param string $dirty_value The submitted invalid value.
	 *
	 * @return void
	 */
	public static function add_dirty_value_to_settings_errors( $error_code, $dirty_value ) {
		global $wp_settings_errors;

		if ( ! is_array( $wp_settings_errors ) ) {
			return;
		}

		foreach ( $wp_settings_errors as $index => $error ) {
			if ( $error['code'] === $error_code ) {
				// phpcs:ignore WordPress.WP.GlobalVariablesOverride -- This is a deliberate action.
				$wp_settings_errors[ $index ]['yoast_dirty_value'] = $dirty_value;
			}
		}
	}

	/**
	 * Gets an invalid submitted value.
	 *
	 * @since      12.1
	 * @deprecated 23.3
	 * @codeCoverageIgnore
	 *
	 * @param string $error_code Code of the error set via `add_settings_error()`, normally the variable name.
	 *
	 * @return string The submitted invalid input field value.
	 */
	public static function get_dirty_value( $error_code ) {  // @phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable, Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Needed for BC.
		_deprecated_function( __METHOD__, 'Yoast SEO 23.3' );
		return '';
	}

	/**
	 * Gets a specific invalid value message.
	 *
	 * @since      12.1
	 * @deprecated 23.3
	 * @codeCoverageIgnore
	 *
	 * @param string $error_code Code of the error set via `add_settings_error()`, normally the variable name.
	 *
	 * @return string The error invalid value message or empty string.
	 */
	public static function get_dirty_value_message( $error_code ) { // @phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable, Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Needed for BC.
		_deprecated_function( __METHOD__, 'Yoast SEO 23.3' );

		return '';
	}
}
