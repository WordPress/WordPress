<?php

if ( ! class_exists( 'WPCF7_Service' ) ) {
	return;
}

class WPCF7_RECAPTCHA extends WPCF7_Service {

	private static $instance;
	private $sitekeys;
	private $last_score;


	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	private function __construct() {
		$this->sitekeys = WPCF7::get_option( 'recaptcha' );
	}


	public function get_title() {
		return __( 'reCAPTCHA', 'contact-form-7' );
	}


	public function is_active() {
		$sitekey = $this->get_sitekey();
		$secret = $this->get_secret( $sitekey );
		return $sitekey && $secret;
	}


	public function get_categories() {
		return array( 'spam_protection' );
	}


	public function icon() {
	}


	public function link() {
		echo wp_kses_data( wpcf7_link(
			'https://www.google.com/recaptcha/intro/index.html',
			'google.com/recaptcha'
		) );
	}


	public function get_global_sitekey() {
		static $sitekey = '';

		if ( $sitekey ) {
			return $sitekey;
		}

		if ( defined( 'WPCF7_RECAPTCHA_SITEKEY' ) ) {
			$sitekey = WPCF7_RECAPTCHA_SITEKEY;
		}

		$sitekey = apply_filters( 'wpcf7_recaptcha_sitekey', $sitekey );

		return $sitekey;
	}


	public function get_global_secret() {
		static $secret = '';

		if ( $secret ) {
			return $secret;
		}

		if ( defined( 'WPCF7_RECAPTCHA_SECRET' ) ) {
			$secret = WPCF7_RECAPTCHA_SECRET;
		}

		$secret = apply_filters( 'wpcf7_recaptcha_secret', $secret );

		return $secret;
	}


	public function get_sitekey() {
		if ( $this->get_global_sitekey() and $this->get_global_secret() ) {
			return $this->get_global_sitekey();
		}

		if ( empty( $this->sitekeys )
		or ! is_array( $this->sitekeys ) ) {
			return false;
		}

		$sitekeys = array_keys( $this->sitekeys );

		return $sitekeys[0];
	}


	public function get_secret( $sitekey ) {
		if ( $this->get_global_sitekey() and $this->get_global_secret() ) {
			return $this->get_global_secret();
		}

		$sitekeys = (array) $this->sitekeys;

		if ( isset( $sitekeys[$sitekey] ) ) {
			return $sitekeys[$sitekey];
		} else {
			return false;
		}
	}


	protected function log( $url, $request, $response ) {
		wpcf7_log_remote_request( $url, $request, $response );
	}


	public function verify( $token ) {
		$is_human = false;

		if ( empty( $token ) or ! $this->is_active() ) {
			return $is_human;
		}

		$endpoint = 'https://www.google.com/recaptcha/api/siteverify';

		if ( apply_filters( 'wpcf7_use_recaptcha_net', false ) ) {
			$endpoint = 'https://www.recaptcha.net/recaptcha/api/siteverify';
		}

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

		$this->last_score = $score = isset( $response_body['score'] )
			? $response_body['score']
			: 0;

		$threshold = $this->get_threshold();
		$is_human = $threshold < $score;

		$is_human = apply_filters( 'wpcf7_recaptcha_verify_response',
			$is_human, $response_body );

		if ( $submission = WPCF7_Submission::get_instance() ) {
			$submission->push( 'recaptcha', array(
				'version' => '3.0',
				'threshold' => $threshold,
				'response' => $response_body,
			) );
		}

		return $is_human;
	}


	public function get_threshold() {
		return apply_filters( 'wpcf7_recaptcha_threshold', 0.50 );
	}


	public function get_last_score() {
		return $this->last_score;
	}


	protected function menu_page_url( $args = '' ) {
		$args = wp_parse_args( $args, array() );

		$url = menu_page_url( 'wpcf7-integration', false );
		$url = add_query_arg( array( 'service' => 'recaptcha' ), $url );

		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}


	protected function save_data() {
		WPCF7::update_option( 'recaptcha', $this->sitekeys );
	}


	protected function reset_data() {
		$this->sitekeys = null;
		$this->save_data();
	}


	public function load( $action = '' ) {
		if (
			'setup' === $action and
			'POST' === wpcf7_superglobal_server( 'REQUEST_METHOD' )
		) {
			check_admin_referer( 'wpcf7-recaptcha-setup' );

			if ( ! empty( $_POST['reset'] ) ) {
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

			if ( WPCF7::get_option( 'recaptcha_v2_v3_warning' ) ) {
				WPCF7::update_option( 'recaptcha_v2_v3_warning', false );
			}

			wp_safe_redirect( $redirect_to );
			exit();
		}
	}


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
			esc_html( __( 'reCAPTCHA protects you against spam and other types of automated abuse. With Contact Form 7&#8217;s reCAPTCHA integration module, you can block abusive form submissions by spam bots.', 'contact-form-7' ) )
		);

		$formatter->end_tag( 'p' );

		$formatter->append_start_tag( 'p' );
		$formatter->append_start_tag( 'strong' );

		$formatter->append_preformatted(
			wpcf7_link(
				__( 'https://contactform7.com/recaptcha/', 'contact-form-7' ),
				__( 'reCAPTCHA (v3)', 'contact-form-7' )
			)
		);

		$formatter->end_tag( 'p' );

		if ( $this->is_active() ) {
			$formatter->append_start_tag( 'p', array(
				'class' => 'dashicons-before dashicons-yes',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'reCAPTCHA is active on this site.', 'contact-form-7' ) )
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
		$sitekey = $this->is_active() ? $this->get_sitekey() : '';
		$secret = $this->is_active() ? $this->get_secret( $sitekey ) : '';

?>
<form method="post" action="<?php echo esc_url( $this->menu_page_url( 'action=setup' ) ); ?>">
<?php wp_nonce_field( 'wpcf7-recaptcha-setup' ); ?>
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
			if ( $this->get_global_sitekey() and $this->get_global_secret() ) {
				// nothing
			} else {
				submit_button(
					_x( 'Remove Keys', 'API keys', 'contact-form-7' ),
					'small', 'reset'
				);
			}
		} else {
			submit_button( __( 'Save Changes', 'contact-form-7' ) );
		}
?>
</form>
<?php
	}
}
