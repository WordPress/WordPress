<?php
/**
 * Turnstile service main file
 */

if ( ! class_exists( 'WPCF7_Service' ) ) {
	return;
}

class WPCF7_Turnstile extends WPCF7_Service {

	private static $instance;
	private $sitekeys;


	/**
	 * Returns the singleton instance of the class.
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * The constructor.
	 */
	private function __construct() {
		$this->sitekeys = WPCF7::get_option( 'turnstile' );
	}


	/**
	 * Returns the service title.
	 */
	public function get_title() {
		return __( 'Turnstile', 'contact-form-7' );
	}


	/**
	 * Returns true if the service is active.
	 */
	public function is_active() {
		$sitekey = $this->get_sitekey();
		$secret = $this->get_secret( $sitekey );
		return $sitekey && $secret;
	}


	/**
	 * Returns an array of categories to which the service belongs to.
	 */
	public function get_categories() {
		return array( 'spam_protection' );
	}


	/**
	 * Returns the icon that represents the service.
	 */
	public function icon() {
	}


	/**
	 * Returns a link to the service provider.
	 */
	public function link() {
		echo wp_kses_data( wpcf7_link(
			'https://www.cloudflare.com/application-services/products/turnstile/',
			'cloudflare.com'
		) );
	}


	/**
	 * Returns a sitekey.
	 */
	public function get_sitekey() {
		$sitekeys = (array) $this->sitekeys;

		$sitekey = array_key_first( $sitekeys ) ?? '';

		return apply_filters( 'wpcf7_turnstile_sitekey', $sitekey );
	}


	/**
	 * Returns the secret key that is paired with the given sitekey.
	 */
	public function get_secret( $sitekey ) {
		$sitekeys = (array) $this->sitekeys;

		$secret = $sitekeys[$sitekey] ?? '';

		return apply_filters( 'wpcf7_turnstile_secret', $secret );
	}


	/**
	 * Logs an API response.
	 */
	protected function log( $url, $request, $response ) {
		wpcf7_log_remote_request( $url, $request, $response );
	}


	/**
	 * Verifies a response token.
	 */
	public function verify( $token ) {
		$is_human = false;

		if ( empty( $token ) or ! $this->is_active() ) {
			return $is_human;
		}

		$endpoint = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

		$sitekey = $this->get_sitekey();
		$secret = $this->get_secret( $sitekey );

		$request = array(
			'body' => array(
				'secret' => $secret,
				'response' => $token,
			),
		);

		$response = wp_remote_post( sanitize_url( $endpoint ), $request );

		if ( 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( WP_DEBUG ) {
				$this->log( $endpoint, $request, $response );
			}

			return $is_human;
		}

		$response_body = wp_remote_retrieve_body( $response );
		$response_body = json_decode( $response_body, true );

		if ( $response_body['success'] ) {
			$is_human = true;
		}

		if ( $submission = WPCF7_Submission::get_instance() ) {
			$submission->push( 'turnstile', array(
				'response' => $response_body,
			) );
		}

		return $is_human;
	}


	/**
	 * Returns the menu page URL for the service configuration.
	 */
	protected function menu_page_url( $args = '' ) {
		$args = wp_parse_args( $args, array() );

		$url = menu_page_url( 'wpcf7-integration', false );
		$url = add_query_arg( array( 'service' => 'turnstile' ), $url );

		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}


	/**
	 * Saves the service configuration data.
	 */
	protected function save_data() {
		WPCF7::update_option( 'turnstile', $this->sitekeys );
	}


	/**
	 * Resets the service configuration data.
	 */
	protected function reset_data() {
		$this->sitekeys = null;
		$this->save_data();
	}


	/**
	 * The loading process of the service configuration page.
	 */
	public function load( $action = '' ) {
		if (
			'setup' === $action and
			'POST' === wpcf7_superglobal_server( 'REQUEST_METHOD' )
		) {
			check_admin_referer( 'wpcf7-turnstile-setup' );

			if ( wpcf7_superglobal_post( 'reset' ) ) {
				$this->reset_data();
				$redirect_to = $this->menu_page_url( 'action=setup' );
			} else {
				$sitekey = wpcf7_superglobal_post( 'sitekey' );
				$secret = wpcf7_superglobal_post( 'secret' );

				if ( $sitekey and $secret ) {
					$this->sitekeys = array( $sitekey => $secret );
					$this->save_data();

					$redirect_to = $this->menu_page_url( array(
						'message' => 'success',
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


	/**
	 * Displays a notice on the integration page.
	 */
	public function admin_notice( $message = '' ) {
		if ( 'invalid' === $message ) {
			wp_admin_notice(
				__( '<strong>Error:</strong> Invalid key values.', 'contact-form-7' ),
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


	/**
	 * Displays the service configuration box.
	 */
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
			esc_html( __( 'Turnstile is Cloudflare&#8217;s smart CAPTCHA alternative, which confirms web visitors are real and blocks unwanted bots without slowing down web experiences for real users.', 'contact-form-7' ) )
		);

		$formatter->end_tag( 'p' );

		$formatter->append_start_tag( 'p' );
		$formatter->append_start_tag( 'strong' );

		$formatter->append_preformatted(
			wpcf7_link(
				__( 'https://contactform7.com/turnstile-integration/', 'contact-form-7' ),
				__( 'Cloudflare Turnstile integration', 'contact-form-7' )
			)
		);

		$formatter->end_tag( 'p' );

		if ( $this->is_active() ) {
			$formatter->append_start_tag( 'p', array(
				'class' => 'dashicons-before dashicons-yes',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'Turnstile is active on this site.', 'contact-form-7' ) )
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


	/**
	 * Displays the service setup form.
	 */
	private function display_setup() {
		$sitekey = $this->is_active() ? $this->get_sitekey() : '';
		$secret = $this->is_active() ? $this->get_secret( $sitekey ) : '';

?>
<form method="post" action="<?php echo esc_url( $this->menu_page_url( 'action=setup' ) ); ?>">
<?php wp_nonce_field( 'wpcf7-turnstile-setup' ); ?>
<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="sitekey"><?php echo esc_html( __( 'Site Key', 'contact-form-7' ) ); ?></label></th>
	<td><?php
		if ( $this->is_active() ) {
			echo esc_html( $sitekey );
			echo sprintf(
				'<input type="hidden" value="%1$s" id="sitekey" name="sitekey" />',
				esc_attr( $sitekey )
			);
		} else {
			echo sprintf(
				'<input type="text" aria-required="true" value="%1$s" id="sitekey" name="sitekey" class="regular-text code" />',
				esc_attr( $sitekey )
			);
		}
	?></td>
</tr>
<tr>
	<th scope="row"><label for="secret"><?php echo esc_html( __( 'Secret Key', 'contact-form-7' ) ); ?></label></th>
	<td><?php
		if ( $this->is_active() ) {
			echo esc_html( wpcf7_mask_password( $secret, 4, 4 ) );
			echo sprintf(
				'<input type="hidden" value="%1$s" id="secret" name="secret" />',
				esc_attr( $secret )
			);
		} else {
			echo sprintf(
				'<input type="text" aria-required="true" value="%1$s" id="secret" name="secret" class="regular-text code" />',
				esc_attr( $secret )
			);
		}
	?></td>
</tr>
</tbody>
</table>
<?php
		if ( $this->is_active() ) {
			submit_button(
				_x( 'Remove Keys', 'API keys', 'contact-form-7' ),
				'small', 'reset'
			);
		} else {
			submit_button( __( 'Save Changes', 'contact-form-7' ) );
		}
?>
</form>
<?php
	}
}
