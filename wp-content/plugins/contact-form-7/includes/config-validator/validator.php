<?php

require_once path_join( __DIR__, 'form.php' );
require_once path_join( __DIR__, 'mail.php' );
require_once path_join( __DIR__, 'messages.php' );
require_once path_join( __DIR__, 'additional-settings.php' );
require_once path_join( __DIR__, 'actions.php' );


/**
 * Configuration validator.
 *
 * @link https://contactform7.com/configuration-errors/
 */
class WPCF7_ConfigValidator {

	/**
	 * The plugin version in which important updates happened last time.
	 */
	const last_important_update = '5.8.1';

	const error_codes = array(
		'maybe_empty',
		'invalid_mailbox_syntax',
		'email_not_in_site_domain',
		'html_in_message',
		'multiple_controls_in_label',
		'file_not_found',
		'unavailable_names',
		'invalid_mail_header',
		'deprecated_settings',
		'file_not_in_content_dir',
		'unavailable_html_elements',
		'attachments_overweight',
		'dots_in_names',
		'colons_in_names',
		'upload_filesize_overlimit',
		'unsafe_email_without_protection',
	);

	use WPCF7_ConfigValidator_Form;
	use WPCF7_ConfigValidator_Mail;
	use WPCF7_ConfigValidator_Messages;
	use WPCF7_ConfigValidator_AdditionalSettings;

	private $contact_form;
	private $errors = array();
	private $include;
	private $exclude;


	/**
	 * Returns a URL linking to the documentation page for the error type.
	 */
	public static function get_doc_link( $child_page = '' ) {
		$url = __( 'https://contactform7.com/configuration-errors/',
			'contact-form-7'
		);

		if ( '' !== $child_page ) {
			$child_page = strtr( $child_page, '_', '-' );

			$url = sprintf( '%s/%s', untrailingslashit( $url ), $child_page );
		}

		return esc_url( $url );
	}


	/**
	 * Constructor.
	 */
	public function __construct( WPCF7_ContactForm $contact_form, $options = '' ) {
		$options = wp_parse_args( $options, array(
			'include' => null,
			'exclude' => null,
		) );

		$this->contact_form = $contact_form;

		if ( isset( $options['include'] ) ) {
			$this->include = (array) $options['include'];
		}

		if ( isset( $options['exclude'] ) ) {
			$this->exclude = (array) $options['exclude'];
		}
	}


	/**
	 * Returns the contact form object that is tied to this validator.
	 */
	public function contact_form() {
		return $this->contact_form;
	}


	/**
	 * Returns true if no error has been detected.
	 */
	public function is_valid() {
		return ! $this->count_errors();
	}


	/**
	 * Returns true if the given error code is supported by this instance.
	 */
	public function supports( $error_code ) {
		if ( isset( $this->include ) ) {
			$supported_codes = array_intersect( self::error_codes, $this->include );
		} else {
			$supported_codes = self::error_codes;
		}

		if ( isset( $this->exclude ) ) {
			$supported_codes = array_diff( $supported_codes, $this->exclude );
		}

		return in_array( $error_code, $supported_codes, true );
	}


	/**
	 * Counts detected errors.
	 */
	public function count_errors( $options = '' ) {
		$options = wp_parse_args( $options, array(
			'section' => '',
			'code' => '',
		) );

		$count = 0;

		foreach ( $this->errors as $key => $errors ) {
			if ( preg_match( '/^mail_[0-9]+\.(.*)$/', $key, $matches ) ) {
				$key = sprintf( 'mail.%s', $matches[1] );
			}

			if (
				$options['section'] and
				$key !== $options['section'] and
				preg_replace( '/\..*$/', '', $key, 1 ) !== $options['section']
			) {
				continue;
			}

			foreach ( $errors as $error ) {
				if ( empty( $error ) ) {
					continue;
				}

				if ( $options['code'] and $error['code'] !== $options['code'] ) {
					continue;
				}

				$count += 1;
			}
		}

		return $count;
	}


	/**
	 * Collects messages for detected errors.
	 */
	public function collect_error_messages( $options = '' ) {
		$options = wp_parse_args( $options, array(
			'decodes_html_entities' => false,
		) );

		$error_messages = array();

		foreach ( $this->errors as $section => $errors ) {
			$error_messages[$section] = array();

			foreach ( $errors as $error ) {
				if ( empty( $error['args']['message'] ) ) {
					$message = $this->get_default_message( $error['code'] );
				} elseif ( empty( $error['args']['params'] ) ) {
					$message = $error['args']['message'];
				} else {
					$message = $this->build_message(
						$error['args']['message'],
						$error['args']['params']
					);
				}

				if ( $options['decodes_html_entities'] ) {
					$message = html_entity_decode( $message, ENT_HTML5 );
				}

				$link = '';

				if ( ! empty( $error['args']['link'] ) ) {
					$link = $error['args']['link'];
				}

				$error_messages[$section][] = array(
					'message' => $message,
					'link' => esc_url( $link ),
				);
			}
		}

		return $error_messages;
	}


	/**
	 * Builds an error message by replacing placeholders.
	 */
	public function build_message( $message, $params = '' ) {
		$params = wp_parse_args( $params, array() );

		foreach ( $params as $key => $val ) {
			if ( ! preg_match( '/^[0-9A-Za-z_]+$/', $key ) ) { // invalid key
				continue;
			}

			$placeholder = '%' . $key . '%';

			if ( false !== stripos( $message, $placeholder ) ) {
				$message = str_ireplace( $placeholder, $val, $message );
			}
		}

		return $message;
	}


	/**
	 * Returns a default message that is used when the message for the error
	 * is not specified.
	 */
	public function get_default_message( $code = '' ) {
		return __( 'Configuration error is detected.', 'contact-form-7' );
	}


	/**
	 * Returns true if the specified section has the specified error.
	 *
	 * @param string $section The section where the error detected.
	 * @param string $code The unique code of the error.
	 */
	public function has_error( $section, $code ) {
		if ( empty( $this->errors[$section] ) ) {
			return false;
		}

		foreach ( (array) $this->errors[$section] as $error ) {
			if ( isset( $error['code'] ) and $error['code'] === $code ) {
				return true;
			}
		}

		return false;
	}


	/**
	 * Adds a validation error.
	 *
	 * @param string $section The section where the error detected.
	 * @param string $code The unique code of the error.
	 * @param string|array $args Optional options for the error.
	 */
	public function add_error( $section, $code, $args = '' ) {
		$args = wp_parse_args( $args, array(
			'message' => '',
			'params' => array(),
		) );

		$available_error_codes = (array) apply_filters(
			'wpcf7_config_validator_available_error_codes',
			self::error_codes,
			$this->contact_form
		);

		if ( ! in_array( $code, $available_error_codes, true ) ) {
			return false;
		}

		if ( ! isset( $args['link'] ) ) {
			$args['link'] = self::get_doc_link( $code );
		}

		if ( ! isset( $this->errors[$section] ) ) {
			$this->errors[$section] = array();
		}

		$this->errors[$section][] = array(
			'code' => $code,
			'args' => $args,
		);

		return true;
	}


	/**
	 * Removes an error.
	 *
	 * @param string $section The section where the error detected.
	 * @param string $code The unique code of the error.
	 */
	public function remove_error( $section, $code ) {
		if ( empty( $this->errors[$section] ) ) {
			return;
		}

		foreach ( (array) $this->errors[$section] as $key => $error ) {
			if ( isset( $error['code'] ) and $error['code'] === $code ) {
				unset( $this->errors[$section][$key] );
			}
		}

		if ( empty( $this->errors[$section] ) ) {
			unset( $this->errors[$section] );
		}
	}


	/**
	 * The main validation runner.
	 *
	 * @return bool True if there is no error detected.
	 */
	public function validate() {
		$this->validate_form();
		$this->validate_mail( 'mail' );
		$this->validate_mail( 'mail_2' );
		$this->validate_messages();
		$this->validate_additional_settings();

		do_action( 'wpcf7_config_validator_validate', $this );

		return $this->is_valid();
	}


	/**
	 * Saves detected errors as a post meta data.
	 */
	public function save() {
		if ( $this->contact_form->initial() ) {
			return;
		}

		delete_post_meta( $this->contact_form->id(), '_config_validation' );

		if ( $this->errors ) {
			update_post_meta(
				$this->contact_form->id(), '_config_validation', $this->errors
			);
		}
	}


	/**
	 * Restore errors from the database.
	 */
	public function restore() {
		$config_errors = get_post_meta(
			$this->contact_form->id(), '_config_validation', true
		);

		foreach ( (array) $config_errors as $section => $errors ) {
			if ( empty( $errors ) ) {
				continue;
			}

			foreach ( (array) $errors as $error ) {
				if ( ! empty( $error['code'] ) ) {
					$code = $error['code'];
					$args = isset( $error['args'] ) ? $error['args'] : '';
					$this->add_error( $section, $code, $args );
				}
			}
		}
	}

}
