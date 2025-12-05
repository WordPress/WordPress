<?php

if ( ! class_exists( 'WPCF7_Service' ) ) {
	return;
}

class WPCF7_Stripe extends WPCF7_Service {

	private static $instance;
	private $api_keys;


	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	private function __construct() {
		$option = WPCF7::get_option( 'stripe' );

		if (
			isset( $option['api_keys']['publishable'] ) and
			isset( $option['api_keys']['secret'] )
		) {
			$this->api_keys = array(
				'publishable' => $option['api_keys']['publishable'],
				'secret' => $option['api_keys']['secret'],
			);
		}
	}


	public function get_title() {
		return __( 'Stripe', 'contact-form-7' );
	}


	public function is_active() {
		return (bool) $this->get_api_keys();
	}


	public function api() {
		if ( $this->is_active() ) {
			$api = new WPCF7_Stripe_API( $this->api_keys['secret'] );
			return $api;
		}
	}


	public function get_api_keys() {
		return $this->api_keys;
	}


	public function get_categories() {
		return array( 'payments' );
	}


	public function icon() {
	}


	public function link() {
		echo wp_kses_data( wpcf7_link(
			'https://stripe.com/',
			'stripe.com'
		) );
	}


	protected function menu_page_url( $args = '' ) {
		$args = wp_parse_args( $args, array() );

		$url = menu_page_url( 'wpcf7-integration', false );
		$url = add_query_arg( array( 'service' => 'stripe' ), $url );

		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}


	protected function save_data() {
		WPCF7::update_option( 'stripe', array(
			'api_keys' => $this->api_keys,
		) );
	}


	protected function reset_data() {
		$this->api_keys = null;
		$this->save_data();
	}


	public function load( $action = '' ) {
		if (
			'setup' === $action and
			'POST' === wpcf7_superglobal_server( 'REQUEST_METHOD' )
		) {
			check_admin_referer( 'wpcf7-stripe-setup' );

			if ( ! empty( $_POST['reset'] ) ) {
				$this->reset_data();
				$redirect_to = $this->menu_page_url( 'action=setup' );
			} else {
				$publishable = wpcf7_superglobal_post( 'publishable' );
				$secret = wpcf7_superglobal_post( 'secret' );

				if ( $publishable and $secret ) {
					$this->api_keys = array(
						'publishable' => $publishable,
						'secret' => $secret,
					);

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
			esc_html( __( 'Stripe is a simple and powerful way to accept payments online. Stripe has no setup fees, no monthly fees, and no hidden costs. Millions of businesses rely on Stripe&#8217;s software tools to accept payments securely and expand globally.', 'contact-form-7' ) )
		);

		$formatter->end_tag( 'p' );

		$formatter->append_start_tag( 'p' );
		$formatter->append_start_tag( 'strong' );

		$formatter->append_preformatted(
			wpcf7_link(
				__( 'https://contactform7.com/stripe-integration/', 'contact-form-7' ),
				__( 'Stripe integration', 'contact-form-7' )
			)
		);

		$formatter->end_tag( 'p' );

		if ( $this->is_active() ) {
			$formatter->append_start_tag( 'p', array(
				'class' => 'dashicons-before dashicons-yes',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'Stripe is active on this site.', 'contact-form-7' ) )
			);

			$formatter->end_tag( 'p' );
		}

		if ( 'setup' === $action ) {
			$formatter->call_user_func( function () {
				$this->display_setup();
			} );
		} elseif ( is_ssl() or WP_DEBUG ) {
			$formatter->append_start_tag( 'p' );

			$formatter->append_start_tag( 'a', array(
				'href' => esc_url( $this->menu_page_url( 'action=setup' ) ),
				'class' => 'button',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'Setup integration', 'contact-form-7' ) )
			);

			$formatter->end_tag( 'p' );
		} else {
			$formatter->append_start_tag( 'p', array(
				'class' => 'dashicons-before dashicons-warning',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'Stripe is not available on this site. It requires an HTTPS-enabled site.', 'contact-form-7' ) )
			);

			$formatter->end_tag( 'p' );
		}

		$formatter->print();
	}


	private function display_setup() {
		$api_keys = $this->get_api_keys();

		if ( $api_keys ) {
			$publishable = $api_keys['publishable'];
			$secret = $api_keys['secret'];
		} else {
			$publishable = '';
			$secret = '';
		}

?>
<form method="post" action="<?php echo esc_url( $this->menu_page_url( 'action=setup' ) ); ?>">
<?php wp_nonce_field( 'wpcf7-stripe-setup' ); ?>
<table class="form-table">
<tbody>
<tr>
	<th scope="row"><label for="publishable"><?php echo esc_html( __( 'Publishable Key', 'contact-form-7' ) ); ?></label></th>
	<td><?php
		if ( $this->is_active() ) {
			echo esc_html( $publishable );
			echo sprintf(
				'<input type="hidden" value="%s" id="publishable" name="publishable" />',
				esc_attr( $publishable )
			);
		} else {
			echo sprintf(
				'<input type="text" aria-required="true" value="%s" id="publishable" name="publishable" class="regular-text code" />',
				esc_attr( $publishable )
			);
		}
	?></td>
</tr>
<tr>
	<th scope="row"><label for="secret"><?php echo esc_html( __( 'Secret Key', 'contact-form-7' ) ); ?></label></th>
	<td><?php
		if ( $this->is_active() ) {
			echo esc_html( wpcf7_mask_password( $secret ) );
			echo sprintf(
				'<input type="hidden" value="%s" id="secret" name="secret" />',
				esc_attr( $secret )
			);
		} else {
			echo sprintf(
				'<input type="text" aria-required="true" value="%s" id="secret" name="secret" class="regular-text code" />',
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
