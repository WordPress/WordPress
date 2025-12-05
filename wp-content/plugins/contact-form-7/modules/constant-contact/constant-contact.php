<?php
/**
 * Constant Contact module main file
 *
 * @link https://contactform7.com/constant-contact-integration/
 */

add_action(
	'wpcf7_init',
	'wpcf7_constant_contact_register_service',
	120, 0
);

/**
 * Registers the Constant Contact service.
 */
function wpcf7_constant_contact_register_service() {
	$integration = WPCF7_Integration::get_instance();

	$integration->add_service( 'constant_contact',
		WPCF7_ConstantContact::get_instance()
	);
}


/**
 * The WPCF7_Service subclass for Constant Contact.
 */
class WPCF7_ConstantContact extends WPCF7_Service {
	const service_name = 'constant_contact';

	private static $instance;

	protected $client_id = '';
	protected $client_secret = '';

	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$option = (array) WPCF7::get_option( self::service_name );
		$this->client_id = $option['client_id'] ?? '';
		$this->client_secret = $option['client_secret'] ?? '';
	}

	protected function reset_data() {
		WPCF7::update_option( self::service_name, array() );
	}

	public function get_title() {
		return __( 'Constant Contact', 'contact-form-7' );
	}

	public function is_active() {
		return $this->client_id || $this->client_secret;
	}

	public function get_categories() {
		return array( 'email_marketing' );
	}

	public function icon() {
	}

	public function link() {
	}

	protected function menu_page_url( $args = '' ) {
		$args = wp_parse_args( $args, array() );

		$url = add_query_arg(
			array( 'service' => self::service_name ),
			menu_page_url( 'wpcf7-integration', false )
		);

		if ( ! empty( $args ) ) {
			$url = add_query_arg( $args, $url );
		}

		return $url;
	}

	public function load( $action = '' ) {
		if (
			'setup' === $action and
			'POST' === wpcf7_superglobal_server( 'REQUEST_METHOD' )
		) {
			check_admin_referer( 'wpcf7-constant-contact-setup' );

			if ( wpcf7_superglobal_post( 'reset' ) ) {
				$this->reset_data();
				$message = 'reset';
			}

			wp_safe_redirect( $this->menu_page_url( array(
				'action' => 'setup',
				'message' => $message ?? '',
			) ) );

			exit();
		}
	}

	public function admin_notice( $message = '' ) {
		switch ( $message ) {
			case 'reset':
				wp_admin_notice(
					__( 'API configuration cleared.', 'contact-form-7' ),
					array( 'type' => 'success' )
				);

				break;
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
			wpcf7_link(
				__( 'https://contactform7.com/2024/02/02/we-end-the-constant-contact-integration/', 'contact-form-7' ),
				__( 'The Constant Contact integration has been removed.', 'contact-form-7' )
			)
		);

		$formatter->end_tag( 'p' );

		if ( $this->is_active() ) {
			$formatter->append_start_tag( 'form', array(
				'method' => 'post',
				'action' => esc_url( $this->menu_page_url( 'action=setup' ) ),
			) );

			$formatter->call_user_func( static function () {
				wp_nonce_field( 'wpcf7-constant-contact-setup' );
			} );

			$formatter->append_start_tag( 'table', array(
				'class' => 'form-table',
			) );

			$formatter->append_start_tag( 'tbody' );

			$formatter->append_start_tag( 'tr' );

			$formatter->append_start_tag( 'th', array(
				'scope' => 'row',
			) );

			$formatter->append_start_tag( 'label', array(
				'for' => 'client_id',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'API Key', 'contact-form-7' ) )
			);

			$formatter->end_tag( 'th' );

			$formatter->append_start_tag( 'td' );

			$formatter->append_preformatted( esc_html( $this->client_id ) );

			$formatter->end_tag( 'tr' );

			$formatter->append_start_tag( 'tr' );

			$formatter->append_start_tag( 'th', array(
				'scope' => 'row',
			) );

			$formatter->append_start_tag( 'label', array(
				'for' => 'client_secret',
			) );

			$formatter->append_preformatted(
				esc_html( __( 'App Secret', 'contact-form-7' ) )
			);

			$formatter->end_tag( 'th' );

			$formatter->append_start_tag( 'td' );

			$formatter->append_preformatted(
				esc_html( wpcf7_mask_password( $this->client_secret, 4, 4 ) )
			);

			$formatter->end_tag( 'table' );

			$formatter->call_user_func( function () {
				submit_button(
					_x( 'Remove Keys', 'API keys', 'contact-form-7' ),
					'small', 'reset'
				);
			} );
		}

		$formatter->print();
	}

}
