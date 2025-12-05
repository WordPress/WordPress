<?php

if ( ! class_exists( 'WPCF7_Service' ) ) {
	return;
}

class WPCF7_Sendinblue extends WPCF7_Service {
	use WPCF7_Sendinblue_API;

	private static $instance;
	private $api_key;

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$option = WPCF7::get_option( 'sendinblue' );

		if ( isset( $option['api_key'] ) ) {
			$this->api_key = $option['api_key'];
		}
	}

	public function get_title() {
		return __( 'Brevo', 'contact-form-7' );
	}

	public function is_active() {
		return (bool) $this->get_api_key();
	}

	public function get_api_key() {
		return $this->api_key;
	}

	public function get_categories() {
		return array( 'email_marketing' );
	}

	public function icon() {
	}

	public function link() {
		echo wp_kses_data( wpcf7_link(
			'https://get.brevo.com/wpcf7-integration',
			'brevo.com'
		) );
	}

	protected function log( $url, $request, $response ) {
		wpcf7_log_remote_request( $url, $request, $response );
	}

	protected function menu_page_url( $args = '' ) {
		$args = wp_parse_args( $args, array() );

		$url = menu_page_url( 'wpcf7-integration', false );
		$url = add_query_arg( array( 'service' => 'sendinblue' ), $url );

		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}

	protected function save_data() {
		WPCF7::update_option( 'sendinblue', array(
			'api_key' => $this->api_key,
		) );
	}

	protected function reset_data() {
		$this->api_key = null;
		$this->save_data();
	}

	public function load( $action = '' ) {
		if (
			'setup' === $action and
			'POST' === wpcf7_superglobal_server( 'REQUEST_METHOD' )
		) {
			check_admin_referer( 'wpcf7-sendinblue-setup' );

			if ( wpcf7_superglobal_post( 'reset' ) ) {
				$this->reset_data();
				$redirect_to = $this->menu_page_url( 'action=setup' );
			} else {
				$this->api_key = wpcf7_superglobal_post( 'api_key' );

				$confirmed = $this->confirm_key();

				if ( true === $confirmed ) {
					$redirect_to = $this->menu_page_url( array(
						'message' => 'success',
					) );

					$this->save_data();
				} elseif ( false === $confirmed ) {
					$redirect_to = $this->menu_page_url( array(
						'action' => 'setup',
						'message' => 'unauthorized',
					) );
				} else {
					$redirect_to = $this->menu_page_url( array(
						'action' => 'setup',
						'message' => 'invalid',
					) );
				}
			}

			wp_safe_redirect( $redirect_to );
			exit();
		}
	}

	public function admin_notice( $message = '' ) {
		if ( 'unauthorized' === $message ) {
			wp_admin_notice(
				sprintf(
					'<strong>%1$s</strong>: %2$s',
					__( 'Error', 'contact-form-7' ),
					__( 'You have not been authenticated. Make sure the provided API key is correct.', 'contact-form-7' )
				),
				array( 'type' => 'error' )
			);
		}

		if ( 'invalid' === $message ) {
			wp_admin_notice(
				sprintf(
					'<strong>%1$s</strong>: %2$s',
					__( 'Error', 'contact-form-7' ),
					__( 'Invalid key values.', 'contact-form-7' )
				),
				array( 'type' => 'error' )
			);
		}

		if ( 'success' === $message ) {
			wp_admin_notice(
				__( 'Settings saved.', 'contact-form-7' ),
				array( 'type' => 'success' )
			);
		}
	}

	public function display( $action = '' ) {
		$formatter = new WPCF7_HTMLFormatter( array(
			'allowed_html' => array_merge( wpcf7_kses_allowed_html(), array(
				'form' => array(
					'action' => true,
					'method' => true,
				),
			) ),
		) );

		$formatter->append_start_tag( 'p' );

		$formatter->append_preformatted(
			esc_html( __( 'Store and organize your contacts while protecting user privacy on Brevo, the leading CRM & email marketing platform in Europe. Brevo offers unlimited contacts and advanced marketing features.', 'contact-form-7' ) )
		);

		$formatter->end_tag( 'p' );

		$formatter->append_start_tag( 'p' );
		$formatter->append_start_tag( 'strong' );

		$formatter->append_preformatted(
			wpcf7_link(
				__( 'https://contactform7.com/sendinblue-integration/', 'contact-form-7' ),
				__( 'Brevo integration', 'contact-form-7' )
			)
		);

		$formatter->end_tag( 'p' );

		if ( $this->is_active() ) {
			$formatter->append_start_tag( 'p', array(
				'class' => 'dashicons-before dashicons-yes',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'Brevo is active on this site.', 'contact-form-7' ) )
			);

			$formatter->end_tag( 'p' );
		}

		if ( 'setup' === $action ) {
			$formatter->call_user_func( function () {
				$this->display_setup();
			} );
		} else {
			$formatter->append_start_tag( 'p' );

			$formatter->append_start_tag( 'a', array(
				'href' => esc_url( $this->menu_page_url( 'action=setup' ) ),
				'class' => 'button',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'Setup integration', 'contact-form-7' ) )
			);

			$formatter->end_tag( 'p' );
		}

		$formatter->print();
	}

	private function display_setup() {
		$api_key = $this->get_api_key();

?>
<form method="post" action="<?php echo esc_url( $this->menu_page_url( 'action=setup' ) ); ?>">
<?php wp_nonce_field( 'wpcf7-sendinblue-setup' ); ?>
<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="publishable"><?php echo esc_html( __( 'API key', 'contact-form-7' ) ); ?></label></th>
	<td><?php
		if ( $this->is_active() ) {
			echo esc_html( wpcf7_mask_password( $api_key, 4, 8 ) );
			echo sprintf(
				'<input type="hidden" value="%s" id="api_key" name="api_key" />',
				esc_attr( $api_key )
			);
		} else {
			echo sprintf(
				'<input type="text" aria-required="true" value="%s" id="api_key" name="api_key" class="regular-text code" />',
				esc_attr( $api_key )
			);
		}
	?></td>
</tr>
</tbody>
</table>
<?php
		if ( $this->is_active() ) {
			submit_button(
				_x( 'Remove key', 'API keys', 'contact-form-7' ),
				'small', 'reset'
			);
		} else {
			submit_button( __( 'Save changes', 'contact-form-7' ) );
		}
?>
</form>
<?php
	}
}


/**
 * Trait for the Sendinblue API (v3).
 *
 * @link https://developers.sendinblue.com/reference
 */
trait WPCF7_Sendinblue_API {


	public function confirm_key() {
		$endpoint = 'https://api.sendinblue.com/v3/account';

		$request = array(
			'headers' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json; charset=utf-8',
				'API-Key' => $this->get_api_key(),
			),
		);

		$response = wp_remote_get( $endpoint, $request );
		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( 200 === $response_code ) { // 200 OK
			return true;
		} elseif ( 401 === $response_code ) { // 401 Unauthorized
			return false;
		} elseif ( 400 <= $response_code ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}
		}
	}


	public function get_lists( $options = '' ) {
		$options = wp_parse_args( $options, array(
			'limit' => 50,
			'offset' => 0,
		) );

		$endpoint = add_query_arg(
			$options,
			'https://api.sendinblue.com/v3/contacts/lists'
		);

		$request = array(
			'headers' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json; charset=utf-8',
				'API-Key' => $this->get_api_key(),
			),
		);

		$response = wp_remote_get( $endpoint, $request );
		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( 200 === $response_code ) { // 200 OK
			$response_body = wp_remote_retrieve_body( $response );
			$response_body = json_decode( $response_body, true );

			if ( empty( $response_body['lists'] ) ) {
				return array();
			} else {
				return (array) $response_body['lists'];
			}
		} elseif ( 400 <= $response_code ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}
		}
	}


	public function get_templates() {
		$endpoint = add_query_arg(
			array(
				'templateStatus' => 'true',
				'limit' => 100,
				'offset' => 0,
			),
			'https://api.sendinblue.com/v3/smtp/templates'
		);

		$request = array(
			'headers' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json; charset=utf-8',
				'API-Key' => $this->get_api_key(),
			),
		);

		$response = wp_remote_get( $endpoint, $request );
		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( 200 === $response_code ) { // 200 OK
			$response_body = wp_remote_retrieve_body( $response );
			$response_body = json_decode( $response_body, true );

			if ( empty( $response_body['templates'] ) ) {
				return array();
			} else {
				return (array) $response_body['templates'];
			}
		} elseif ( 400 <= $response_code ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}
		}
	}


	public function create_contact( $properties ) {
		$endpoint = 'https://api.sendinblue.com/v3/contacts';

		$request = array(
			'headers' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json; charset=utf-8',
				'API-Key' => $this->get_api_key(),
			),
			'body' => wp_json_encode( $properties ),
		);

		$response = wp_remote_post( $endpoint, $request );
		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( in_array( $response_code, array( 201, 204 ), true ) ) {
			$contact_id = wp_remote_retrieve_body( $response );
			return $contact_id;
		} elseif ( 400 <= $response_code ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}
		}

		return false;
	}


	public function send_email( $properties ) {
		$endpoint = 'https://api.sendinblue.com/v3/smtp/email';

		$request = array(
			'headers' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json; charset=utf-8',
				'API-Key' => $this->get_api_key(),
			),
			'body' => wp_json_encode( $properties ),
		);

		$response = wp_remote_post( $endpoint, $request );
		$response_code = (int) wp_remote_retrieve_response_code( $response );

		if ( 201 === $response_code ) { // 201 Transactional email sent
			$message_id = wp_remote_retrieve_body( $response );
			return $message_id;
		} elseif ( 400 <= $response_code ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}
		}

		return false;
	}


}
