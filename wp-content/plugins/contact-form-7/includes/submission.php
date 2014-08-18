<?php

class WPCF7_Submission {

	private static $instance;

	private $contact_form;
	private $status = 'init';
	private $posted_data = array();
	private $uploaded_files = array();
	private $skip_mail = false;
	private $response = '';
	private $invalid_fields = array();
	private $meta = array();

	private function __construct() {}

	public static function get_instance( WPCF7_ContactForm $contact_form = null ) {
		if ( empty( self::$instance ) ) {
			if ( null == $contact_form ) {
				return null;
			}

			self::$instance = new self;
			self::$instance->contact_form = $contact_form;
			self::$instance->skip_mail = $contact_form->in_demo_mode();
			self::$instance->setup_posted_data();
			self::$instance->submit();
		} elseif ( null != $contact_form ) {
			return null;
		}

		return self::$instance;
	}

	public function get_status() {
		return $this->status;
	}

	public function is( $status ) {
		return $this->status == $status;
	}

	public function get_response() {
		return $this->response;
	}

	public function get_invalid_field( $name ) {
		if ( isset( $this->invalid_fields[$name] ) ) {
			return $this->invalid_fields[$name];
		} else {
			return false;
		}
	}

	public function get_invalid_fields() {
		return $this->invalid_fields;
	}

	public function get_posted_data( $name = '' ) {
		if ( ! empty( $name ) ) {
			if ( isset( $this->posted_data[$name] ) ) {
				return $this->posted_data[$name];
			} else {
				return null;
			}
		}

		return $this->posted_data;
	}

	private function setup_posted_data() {
		$posted_data = (array) $_POST;

		$tags = $this->contact_form->form_scan_shortcode();

		foreach ( (array) $tags as $tag ) {
			if ( empty( $tag['name'] ) ) {
				continue;
			}

			$name = $tag['name'];
			$value = '';

			if ( isset( $posted_data[$name] ) ) {
				$value = $posted_data[$name];
			}

			$pipes = $tag['pipes'];

			if ( WPCF7_USE_PIPE
			&& is_a( $pipes, 'WPCF7_Pipes' )
			&& ! $pipes->zero() ) {
				if ( is_array( $value) ) {
					$new_value = array();

					foreach ( $value as $v ) {
						$new_value[] = $pipes->do_pipe( wp_unslash( $v ) );
					}

					$value = $new_value;
				} else {
					$value = $pipes->do_pipe( wp_unslash( $value ) );
				}
			}

			$posted_data[$name] = $value;
		}

		$this->posted_data = apply_filters( 'wpcf7_posted_data', $posted_data );

		return $this->posted_data;
	}

	private function submit() {
		if ( ! $this->is( 'init' ) ) {
			return $this->status;
		}

		$this->meta = array(
			'remote_ip' => preg_replace( '/[^0-9a-f.:, ]/', '',
				$_SERVER['REMOTE_ADDR'] ),
			'user_agent' => substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ),
			'url' => preg_replace( '%(?<!:|/)/.*$%', '',
				untrailingslashit( home_url() ) ) . wpcf7_get_request_uri(),
			'timestamp' => current_time( 'timestamp' ),
			'unit_tag' => isset( $_POST['_wpcf7_unit_tag'] )
				? $_POST['_wpcf7_unit_tag'] : '' );

		$contact_form = $this->contact_form;

		if ( ! $this->validate() ) { // Validation error occured
			$this->status = 'validation_failed';
			$this->response = $contact_form->message( 'validation_error' );

		} elseif ( ! $this->accepted() ) { // Not accepted terms
			$this->status = 'acceptance_missing';
			$this->response = $contact_form->message( 'accept_terms' );

		} elseif ( $this->spam() ) { // Spam!
			$this->status = 'spam';
			$this->response = $contact_form->message( 'spam' );

		} elseif ( $this->mail() ) {
			$this->status = 'mail_sent';
			$this->response = $contact_form->message( 'mail_sent_ok' );

			do_action( 'wpcf7_mail_sent', $contact_form );

		} else {
			$this->status = 'mail_failed';
			$this->response = $contact_form->message( 'mail_sent_ng' );

			do_action( 'wpcf7_mail_failed', $contact_form );
		}

		$this->remove_uploaded_files();

		return $this->status;
	}

	private function validate() {
		if ( $this->invalid_fields ) {
			return false;
		}

		$result = array(
			'valid' => true,
			'reason' => array(),
			'idref' => array() );

		$tags = $this->contact_form->form_scan_shortcode();

		foreach ( $tags as $tag ) {
			$result = apply_filters( 'wpcf7_validate_' . $tag['type'],
				$result, $tag );
		}

		$result = apply_filters( 'wpcf7_validate', $result );

		if ( $result['valid'] ) {
			return true;
		} else {
			foreach ( (array) $result['reason'] as $name => $reason ) {
				$field = array( 'reason' => $reason );

				if ( isset( $result['idref'][$name] )
				&& wpcf7_is_name( $result['idref'][$name] ) ) {
					$field['idref'] = $result['idref'][$name];
				} else {
					$field['idref'] = null;
				}

				$this->invalid_fields[$name] = $field;
			}

			return false;
		}
	}

	private function accepted() {
		return apply_filters( 'wpcf7_acceptance', true );
	}

	private function spam() {
		$spam = false;

		if ( WPCF7_VERIFY_NONCE && ! $this->verify_nonce() ) {
			$spam = true;
		}

		if ( $this->blacklist_check() ) {
			$spam = true;
		}

		return apply_filters( 'wpcf7_spam', $spam );
	}

	private function verify_nonce() {
		return wpcf7_verify_nonce( $_POST['_wpnonce'], $this->contact_form->id() );
	}

	private function blacklist_check() {
		$target = wpcf7_array_flatten( $this->posted_data );
		$target[] = $_SERVER['REMOTE_ADDR'];
		$target[] = $_SERVER['HTTP_USER_AGENT'];

		$target = implode( "\n", $target );

		return wpcf7_blacklist_check( $target );
	}

	/* Mail */

	private function mail() {
		$contact_form = $this->contact_form;

		do_action( 'wpcf7_before_send_mail', $contact_form );

		if ( $this->skip_mail || ! empty( $contact_form->skip_mail ) ) {
			return true;
		}

		$result = WPCF7_Mail::send( $contact_form->prop( 'mail' ), 'mail' );

		if ( $result ) {
			$additional_mail = array();

			if ( ( $mail_2 = $contact_form->prop( 'mail_2' ) ) && $mail_2['active'] ) {
				$additional_mail['mail_2'] = $mail_2;
			}

			$additional_mail = apply_filters( 'wpcf7_additional_mail',
				$additional_mail, $contact_form );

			foreach ( $additional_mail as $name => $template ) {
				WPCF7_Mail::send( $template, $name );
			}

			return true;
		}

		return false;
	}

	public function uploaded_files() {
		return $this->uploaded_files;
	}

	public function add_uploaded_file( $name, $file_path ) {
		$this->uploaded_files[$name] = $file_path;

		if ( empty( $this->posted_data[$name] ) ) {
			$this->posted_data[$name] = basename( $file_path );
		}
	}

	public function remove_uploaded_files() {
		foreach ( (array) $this->uploaded_files as $name => $path ) {
			@unlink( $path );
		}
	}

	public function get_meta( $name ) {
		if ( isset( $this->meta[$name] ) ) {
			return $this->meta[$name];
		}
	}
}

?>