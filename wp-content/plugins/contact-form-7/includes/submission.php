<?php

/**
 * Class representing contact form submission.
 */
class WPCF7_Submission {

	use WPCF7_PocketHolder;

	private static $instance;

	private $contact_form;
	private $status = 'init';
	private $posted_data = array();
	private $posted_data_hash = null;
	private $skip_spam_check = false;
	private $uploaded_files = array();
	private $extra_attachments = array();
	private $skip_mail = false;
	private $response = '';
	private $invalid_fields = array();
	private $meta = array();
	private $consent = array();
	private $spam_log = array();
	private $result_props = array();


	/**
	 * Returns the singleton instance of this class.
	 */
	public static function get_instance( $contact_form = null, $options = '' ) {
		if ( $contact_form instanceof WPCF7_ContactForm ) {
			if ( empty( self::$instance ) ) {
				self::$instance = new self( $contact_form, $options );
				self::$instance->proceed();
				return self::$instance;
			} else {
				return null;
			}
		} else {
			if ( empty( self::$instance ) ) {
				return null;
			} else {
				return self::$instance;
			}
		}
	}


	/**
	 * Returns true if this submission is created via WP REST API.
	 */
	public static function is_restful() {
		return wp_is_serving_rest_request();
	}


	/**
	 * Constructor.
	 */
	private function __construct( WPCF7_ContactForm $contact_form, $options = '' ) {
		$options = wp_parse_args( $options, array(
			'skip_mail' => false,
		) );

		$this->contact_form = $contact_form;
		$this->skip_mail = (bool) $options['skip_mail'];
	}


	/**
	 * Destructor.
	 */
	public function __destruct() {
		$this->remove_uploaded_files();
	}


	/**
	 * The main logic of submission.
	 */
	private function proceed() {

		$callback = function () {
			$contact_form = $this->contact_form;

			$this->setup_meta_data();
			$this->setup_posted_data();

			if ( $this->is( 'init' ) and ! $this->validate() ) {
				$this->set_status( 'validation_failed' );
				$this->set_response( $contact_form->message( 'validation_error' ) );
			}

			if ( $this->is( 'init' ) and ! $this->accepted() ) {
				$this->set_status( 'acceptance_missing' );
				$this->set_response( $contact_form->message( 'accept_terms' ) );
			}

			if ( $this->is( 'init' ) and $this->spam() ) {
				$this->set_status( 'spam' );
				$this->set_response( $contact_form->message( 'spam' ) );
			}

			if ( $this->is( 'init' ) and ! $this->unship_uploaded_files() ) {
				$this->set_status( 'validation_failed' );
				$this->set_response( $contact_form->message( 'validation_error' ) );
			}

			if ( $this->is( 'init' ) ) {
				$abort = ! $this->before_send_mail();

				if ( $abort ) {
					if ( $this->is( 'init' ) ) {
						$this->set_status( 'aborted' );
					}

					if ( '' === $this->get_response() ) {
						$this->set_response( $contact_form->filter_message(
							__( 'Sending mail has been aborted.', 'contact-form-7' ) )
						);
					}
				} elseif ( $this->mail() ) {
					$this->set_status( 'mail_sent' );
					$this->set_response( $contact_form->message( 'mail_sent_ok' ) );

					do_action( 'wpcf7_mail_sent', $contact_form );
				} else {
					$this->set_status( 'mail_failed' );
					$this->set_response( $contact_form->message( 'mail_sent_ng' ) );

					do_action( 'wpcf7_mail_failed', $contact_form );
				}
			}
		};

		wpcf7_switch_locale( $this->contact_form->locale(), $callback );
	}


	/**
	 * Returns the current status property.
	 */
	public function get_status() {
		return $this->status;
	}


	/**
	 * Sets the status property.
	 *
	 * @param string $status The status.
	 */
	public function set_status( $status ) {
		if ( preg_match( '/^[a-z][0-9a-z_]+$/', $status ) ) {
			$this->status = $status;
			return true;
		}

		return false;
	}


	/**
	 * Returns true if the specified status is identical to the current
	 * status property.
	 *
	 * @param string $status The status to compare.
	 */
	public function is( $status ) {
		return $this->status === $status;
	}


	/**
	 * Returns an associative array of submission result properties.
	 *
	 * @return array Submission result properties.
	 */
	public function get_result() {
		$result = array_merge( $this->result_props, array(
			'status' => $this->get_status(),
			'message' => $this->get_response(),
		) );

		if ( $this->is( 'validation_failed' ) ) {
			$result['invalid_fields'] = $this->get_invalid_fields();
		}

		switch ( $this->get_status() ) {
			case 'init':
			case 'validation_failed':
			case 'acceptance_missing':
			case 'spam':
				$result['posted_data_hash'] = '';
				break;
			default:
				$result['posted_data_hash'] = $this->get_posted_data_hash();
				break;
		}

		$result = apply_filters( 'wpcf7_submission_result', $result, $this );

		return $result;
	}


	/**
	 * Adds items to the array of submission result properties.
	 *
	 * @param string|array|object $data Value to add to result properties.
	 * @return array Added result properties.
	 */
	public function add_result_props( $data = '' ) {
		$data = wp_parse_args( $data, array() );

		$this->result_props = array_merge( $this->result_props, $data );

		return $data;
	}


	/**
	 * Retrieves the response property.
	 *
	 * @return string The current response property value.
	 */
	public function get_response() {
		return $this->response;
	}


	/**
	 * Sets the response property.
	 *
	 * @param string $response New response property value.
	 */
	public function set_response( $response ) {
		$this->response = $response;
		return true;
	}


	/**
	 * Retrieves the contact form property.
	 *
	 * @return WPCF7_ContactForm A contact form object.
	 */
	public function get_contact_form() {
		return $this->contact_form;
	}


	/**
	 * Search an invalid field by field name.
	 *
	 * @param string $name The field name.
	 * @return array|bool An associative array of validation error
	 *                    or false when no invalid field.
	 */
	public function get_invalid_field( $name ) {
		return $this->invalid_fields[$name] ?? false;
	}


	/**
	 * Retrieves all invalid fields.
	 *
	 * @return array Invalid fields.
	 */
	public function get_invalid_fields() {
		return $this->invalid_fields;
	}


	/**
	 * Retrieves meta information.
	 *
	 * @param string $name Name of the meta information.
	 * @return string|null The meta information of the given name if it exists,
	 *                     null otherwise.
	 */
	public function get_meta( $name ) {
		return $this->meta[$name] ?? null;
	}


	/**
	 * Collects meta information about this submission.
	 */
	private function setup_meta_data() {
		$this->meta = array(
			'timestamp' => time(),
			'remote_ip' => $this->get_remote_ip_addr(),
			'remote_port' => wpcf7_superglobal_server( 'REMOTE_PORT' ),
			'user_agent' => wpcf7_superglobal_server( 'HTTP_USER_AGENT' ),
			'url' => $this->get_request_url(),
			'unit_tag' => wpcf7_sanitize_unit_tag(
				wpcf7_superglobal_post( '_wpcf7_unit_tag' )
			),
			'container_post_id' => absint(
				wpcf7_superglobal_post( '_wpcf7_container_post' )
			),
			'current_user_id' => get_current_user_id(),
			'do_not_store' => $this->contact_form->is_true( 'do_not_store' ),
		);

		return $this->meta;
	}


	/**
	 * Retrieves user input data through this submission.
	 *
	 * @param string $name Optional field name.
	 * @return string|array|null The user input of the field, or array of all
	 *                           fields values if no field name specified.
	 */
	public function get_posted_data( $name = '' ) {
		if ( ! empty( $name ) ) {
			return $this->posted_data[$name] ?? null;
		}

		return $this->posted_data;
	}


	/**
	 * Retrieves a user input string value through the specified field.
	 *
	 * @param string $name Field name.
	 * @return string The user input. If the input is an array,
	 *                the first item in the array.
	 */
	public function get_posted_string( $name ) {
		$data = $this->get_posted_data( $name );
		$data = wpcf7_array_flatten( $data );

		if ( empty( $data ) ) {
			return '';
		}

		// Returns the first array item.
		return trim( reset( $data ) );
	}


	/**
	 * Constructs posted data property based on user input values.
	 */
	private function setup_posted_data() {
		$posted_data = array_filter(
			(array) $_POST,
			static function ( $key ) {
				return ! str_starts_with( $key, '_' );
			},
			ARRAY_FILTER_USE_KEY
		);

		$posted_data = wp_unslash( $posted_data );
		$posted_data = $this->sanitize_posted_data( $posted_data );

		$tags = $this->contact_form->scan_form_tags( array(
			'feature' => array(
				'name-attr',
				'! not-for-mail',
			),
		) );

		$tags = array_reduce( $tags, static function ( $carry, $tag ) {
			if ( $tag->name and ! isset( $carry[$tag->name] ) ) {
				$carry[$tag->name] = $tag;
			}

			return $carry;
		}, array() );

		foreach ( $tags as $tag ) {
			$value_orig = $value = $posted_data[$tag->name] ?? '';

			if ( wpcf7_form_tag_supports( $tag->type, 'selectable-values' ) ) {
				$value = ( '' === $value ) ? array() : (array) $value;

				if ( WPCF7_USE_PIPE ) {
					$pipes = $this->contact_form->get_pipes( $tag->name );

					$value = array_map( static function ( $value ) use ( $pipes ) {
						return $pipes->do_pipe( $value );
					}, $value );
				}
			}

			$value = apply_filters( "wpcf7_posted_data_{$tag->type}",
				$value,
				$value_orig,
				$tag
			);

			$posted_data[$tag->name] = $value;

			if ( $tag->has_option( 'consent_for:storage' ) and empty( $value ) ) {
				$this->meta['do_not_store'] = true;
			}
		}

		$this->posted_data = apply_filters( 'wpcf7_posted_data', $posted_data );

		$this->posted_data_hash = $this->create_posted_data_hash();

		return $this->posted_data;
	}


	/**
	 * Sanitizes user input data.
	 */
	private function sanitize_posted_data( $value ) {
		return map_deep( $value, static function ( $val ) {
			$val = (string) $val;
			$val = wp_check_invalid_utf8( $val );
			$val = wp_kses_no_null( $val );
			$val = wpcf7_strip_whitespaces( $val );
			return $val;
		} );
	}


	/**
	 * Returns the time-dependent variable for hash creation.
	 *
	 * @return float Float value rounded up to the next highest integer.
	 */
	private function posted_data_hash_tick() {
		return ceil( time() / ( HOUR_IN_SECONDS / 2 ) );
	}


	/**
	 * Creates a hash string based on posted data, the remote IP address,
	 * contact form location, and window of time.
	 *
	 * @param string $tick Optional. If not specified, result of
	 *               posted_data_hash_tick() will be used.
	 * @return string The hash.
	 */
	private function create_posted_data_hash( $tick = '' ) {
		if ( '' === $tick ) {
			$tick = $this->posted_data_hash_tick();
		}

		$hash = wp_hash(
			wpcf7_flat_join( array_merge(
				array(
					$tick,
					$this->get_meta( 'remote_ip' ),
					$this->get_meta( 'unit_tag' ),
				),
				$this->posted_data
			) ),
			'wpcf7_submission'
		);

		return $hash;
	}


	/**
	 * Returns the hash string created for this submission.
	 *
	 * @return string The current hash for the submission.
	 */
	public function get_posted_data_hash() {
		return $this->posted_data_hash;
	}


	/**
	 * Verifies that the given string is equivalent to the posted data hash.
	 *
	 * @param string $hash Optional. This value will be compared to the
	 *               current posted data hash for the submission. If not
	 *               specified, the value of $_POST['_wpcf7_posted_data_hash']
	 *               will be used.
	 * @return int|bool 1 if $hash is created 0-30 minutes ago,
	 *                  2 if $hash is created 30-60 minutes ago,
	 *                  false if $hash is invalid.
	 */
	public function verify_posted_data_hash( $hash = '' ) {
		if ( '' === $hash ) {
			$hash = wpcf7_superglobal_post( '_wpcf7_posted_data_hash' );
		}

		if ( '' === $hash ) {
			return false;
		}

		$tick = $this->posted_data_hash_tick();

		// Hash created 0-30 minutes ago.
		$expected_1 = $this->create_posted_data_hash( $tick );

		if ( hash_equals( $expected_1, $hash ) ) {
			return 1;
		}

		// Hash created 30-60 minutes ago.
		$expected_2 = $this->create_posted_data_hash( $tick - 1 );

		if ( hash_equals( $expected_2, $hash ) ) {
			return 2;
		}

		return false;
	}


	/**
	 * Retrieves the remote IP address of this submission.
	 */
	private function get_remote_ip_addr() {
		$ip_addr = wpcf7_superglobal_server( 'REMOTE_ADDR' );

		if ( ! WP_Http::is_ip_address( $ip_addr ) ) {
			$ip_addr = '';
		}

		return apply_filters( 'wpcf7_remote_ip_addr', $ip_addr );
	}


	/**
	 * Retrieves the request URL of this submission.
	 */
	private function get_request_url() {
		$home_url = untrailingslashit( home_url() );

		if ( self::is_restful() ) {
			$referer = wpcf7_superglobal_server( 'HTTP_REFERER' );

			if ( $referer and str_starts_with( $referer, $home_url ) ) {
				return sanitize_url( $referer );
			}
		}

		$url = preg_replace( '%(?<!:|/)/.*$%', '', $home_url )
			. wpcf7_get_request_uri();

		return $url;
	}


	/**
	 * Runs user input validation.
	 *
	 * @return bool True if no invalid field is found.
	 */
	private function validate() {
		if ( $this->invalid_fields ) {
			return false;
		}

		$result = new WPCF7_Validation();

		$this->contact_form->validate_schema(
			array(
				'text' => true,
				'file' => false,
				'field' => array(),
			),
			$result
		);

		$tags = $this->contact_form->scan_form_tags( array(
		  'feature' => '! file-uploading',
		) );

		foreach ( $tags as $tag ) {
			$type = $tag->type;
			$result = apply_filters( "wpcf7_validate_{$type}", $result, $tag );
		}

		$result = apply_filters( 'wpcf7_validate', $result, $tags );

		$this->invalid_fields = $result->get_invalid_fields();

		return $result->is_valid();
	}


	/**
	 * Returns true if user consent is obtained.
	 */
	private function accepted() {
		return apply_filters( 'wpcf7_acceptance', true, $this );
	}


	/**
	 * Adds user consent data to this submission.
	 *
	 * @param string $name Field name.
	 * @param string $conditions Conditions of consent.
	 */
	public function add_consent( $name, $conditions ) {
		$this->consent[$name] = $conditions;
		return true;
	}


	/**
	 * Collects user consent data.
	 *
	 * @return array User consent data.
	 */
	public function collect_consent() {
		return (array) $this->consent;
	}


	/**
	 * Executes spam protections.
	 *
	 * @return bool True if spam captured.
	 */
	private function spam() {
		$spam = false;

		$skip_spam_check = apply_filters( 'wpcf7_skip_spam_check',
			$this->skip_spam_check,
			$this
		);

		if ( $skip_spam_check ) {
			return $spam;
		}

		if (
			$this->contact_form->is_true( 'subscribers_only' ) and
			current_user_can( 'wpcf7_submit', $this->contact_form->id() )
		) {
			return $spam;
		}

		$user_agent = (string) $this->get_meta( 'user_agent' );

		if ( strlen( $user_agent ) < 2 ) {
			$spam = true;

			$this->add_spam_log( array(
				'agent' => 'wpcf7',
				'reason' => __( 'User-Agent string is unnaturally short.', 'contact-form-7' ),
			) );
		}

		if ( ! $this->verify_nonce() ) {
			$spam = true;

			$this->add_spam_log( array(
				'agent' => 'wpcf7',
				'reason' => __( 'Submitted nonce is invalid.', 'contact-form-7' ),
			) );
		}

		return apply_filters( 'wpcf7_spam', $spam, $this );
	}


	/**
	 * Adds a spam log.
	 *
	 * @link https://contactform7.com/2019/05/31/why-is-this-message-marked-spam/
	 */
	public function add_spam_log( $data = '' ) {
		$data = wp_parse_args( $data, array(
			'agent' => '',
			'reason' => '',
		) );

		$this->spam_log[] = $data;
	}


	/**
	 * Retrieves the spam logging data.
	 *
	 * @return array Spam logging data.
	 */
	public function get_spam_log() {
		return $this->spam_log;
	}


	/**
	 * Verifies that a correct security nonce was used.
	 */
	private function verify_nonce() {
		if ( ! $this->contact_form->nonce_is_active() or ! is_user_logged_in() ) {
			return true;
		}

		$nonce = wpcf7_superglobal_post( '_wpnonce' );

		return wpcf7_verify_nonce( $nonce );
	}


	/**
	 * Function called just before sending email.
	 */
	private function before_send_mail() {
		$abort = false;

		do_action_ref_array( 'wpcf7_before_send_mail', array(
			$this->contact_form,
			&$abort,
			$this,
		) );

		return ! $abort;
	}


	/**
	 * Sends emails based on user input values and contact form email templates.
	 */
	private function mail() {
		$contact_form = $this->contact_form;

		$skip_mail = apply_filters( 'wpcf7_skip_mail',
			$this->skip_mail, $contact_form
		);

		if ( $skip_mail ) {
			return true;
		}

		$result = WPCF7_Mail::send( $contact_form->prop( 'mail' ), 'mail' );

		if ( $result ) {
			$additional_mail = array();

			if (
				$mail_2 = $contact_form->prop( 'mail_2' ) and
				$mail_2['active']
			) {
				$additional_mail['mail_2'] = $mail_2;
			}

			$additional_mail = apply_filters( 'wpcf7_additional_mail',
				$additional_mail, $contact_form
			);

			foreach ( $additional_mail as $name => $template ) {
				WPCF7_Mail::send( $template, $name );
			}

			return true;
		}

		return false;
	}


	/**
	 * Retrieves files uploaded through this submission.
	 */
	public function uploaded_files() {
		return $this->uploaded_files;
	}


	/**
	 * Adds a file to the uploaded files array.
	 *
	 * @param string $name Field name.
	 * @param string|array $file_path File path or array of file paths.
	 */
	private function add_uploaded_file( $name, $file_path ) {
		if ( ! wpcf7_is_name( $name ) ) {
			return false;
		}

		$paths = (array) $file_path;
		$uploaded_files = array();
		$hash_strings = array();

		foreach ( $paths as $path ) {
			if ( @is_file( $path ) and @is_readable( $path ) ) {
				$uploaded_files[] = $path;
				$hash_strings[] = hash_file( 'sha256', $path );
			}
		}

		$this->uploaded_files[$name] = $uploaded_files;

		if ( empty( $this->posted_data[$name] ) ) {
			$this->posted_data[$name] = implode( ' ', $hash_strings );
		}
	}


	/**
	 * Removes uploaded files.
	 */
	private function remove_uploaded_files() {
		$filesystem = WPCF7_Filesystem::get_instance();

		foreach ( (array) $this->uploaded_files as $file_path ) {
			foreach ( (array) $file_path as $path ) {
				if ( wpcf7_is_file_path_in_content_dir( $path ) ) {
					wpcf7_rmdir_p( $path );

					// Remove parent dir if empty.
					$filesystem->delete( dirname( $path ), false );
				}
			}
		}
	}


	/**
	 * Moves uploaded files to the tmp directory and validates them.
	 *
	 * @return bool True if no invalid file is found.
	 */
	private function unship_uploaded_files() {
		$result = new WPCF7_Validation();

		$tags = $this->contact_form->scan_form_tags( array(
			'feature' => 'file-uploading',
		) );

		foreach ( $tags as $tag ) {
			if ( empty( $_FILES[$tag->name] ) ) {
				continue;
			}

			$file = $_FILES[$tag->name];

			$options = array(
				'tag' => $tag,
				'name' => $tag->name,
				'required' => $tag->is_required(),
				'filetypes' => $tag->get_option( 'filetypes' ),
				'limit' => $tag->get_limit_option(),
				'schema' => $this->contact_form->get_schema(),
			);

			$new_files = wpcf7_unship_uploaded_file( $file, $options );

			if ( is_wp_error( $new_files ) ) {
				$result->invalidate( $tag, $new_files );
			} else {
				$this->add_uploaded_file( $tag->name, $new_files );
			}

			$result = apply_filters(
				"wpcf7_validate_{$tag->type}",
				$result, $tag,
				array(
					'uploaded_files' => $new_files,
				)
			);
		}

		$this->invalid_fields = $result->get_invalid_fields();

		return $result->is_valid();
	}


	/**
	 * Adds extra email attachment files that are independent from form fields.
	 *
	 * @param string|array $file_path A file path or an array of file paths.
	 * @param string $template Optional. The name of the template to which
	 *                         the files are attached.
	 * @return bool True if it succeeds to attach a file at least,
	 *              or false otherwise.
	 */
	public function add_extra_attachments( $file_path, $template = 'mail' ) {
		if ( ! did_action( 'wpcf7_before_send_mail' ) ) {
			return false;
		}

		$extra_attachments = array();

		foreach ( (array) $file_path as $path ) {
			$path = path_join( WP_CONTENT_DIR, $path );

			if ( file_exists( $path ) ) {
				$extra_attachments[] = $path;
			}
		}

		if ( empty( $extra_attachments ) ) {
			return false;
		}

		if ( ! isset( $this->extra_attachments[$template] ) ) {
			$this->extra_attachments[$template] = array();
		}

		$this->extra_attachments[$template] = array_merge(
			$this->extra_attachments[$template],
			$extra_attachments
		);

		return true;
	}


	/**
	 * Returns extra email attachment files.
	 *
	 * @param string $template An email template name.
	 * @return array Array of file paths.
	 */
	public function extra_attachments( $template ) {
		if ( isset( $this->extra_attachments[$template] ) ) {
			return (array) $this->extra_attachments[$template];
		}

		return array();
	}

}
