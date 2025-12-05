<?php
/**
 * Integration API
 *
 * @link https://contactform7.com/integration-with-external-apis/
 */

class WPCF7_Integration {

	private static $instance;

	private $services = array();
	private $categories = array();

	private function __construct() {}


	/**
	 * Returns initially supported service categories.
	 *
	 * @return array Service categories.
	 */
	public static function get_builtin_categories() {
		return array(
			'spam_protection' => __( 'Spam protection', 'contact-form-7' ),
			'email_marketing' => __( 'Email marketing', 'contact-form-7' ),
			'payments' => __( 'Payments', 'contact-form-7' ),
		);
	}


	/**
	 * Returns the singleton instance of this class.
	 *
	 * @return WPCF7_Integration The instance.
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->categories = self::get_builtin_categories();
		}

		return self::$instance;
	}


	/**
	 * Adds a service to the services list.
	 */
	public function add_service( $name, WPCF7_Service $service ) {
		$name = sanitize_key( $name );

		if ( empty( $name ) or isset( $this->services[$name] ) ) {
			return false;
		}

		$this->services[$name] = $service;
	}


	/**
	 * Adds a service category to the categories list.
	 */
	public function add_category( $name, $title ) {
		$name = sanitize_key( $name );

		if ( empty( $name ) or isset( $this->categories[$name] ) ) {
			return false;
		}

		$this->categories[$name] = $title;
	}


	/**
	 * Returns true if a service with the name exists in the services list.
	 *
	 * @param string $name The name of service to search.
	 */
	public function service_exists( $name = '' ) {
		if ( '' === $name ) {
			return (bool) count( $this->services );
		} else {
			return isset( $this->services[$name] );
		}
	}


	/**
	 * Returns a service object with the name.
	 *
	 * @param string $name The name of service.
	 * @return WPCF7_Service|bool The service object if it exists,
	 *                            false otherwise.
	 */
	public function get_service( $name ) {
		if ( $this->service_exists( $name ) ) {
			return $this->services[$name];
		} else {
			return false;
		}
	}


	/**
	 * Prints services list.
	 */
	public function list_services( $args = '' ) {
		$args = wp_parse_args( $args, array(
			'include' => array(),
		) );

		$singular = false;
		$services = (array) $this->services;

		if ( ! empty( $args['include'] ) ) {
			$services = array_intersect_key( $services,
				array_flip( (array) $args['include'] )
			);

			if ( 1 === count( $services ) ) {
				$singular = true;
			}
		}

		if ( empty( $services ) ) {
			return;
		}

		$action = wpcf7_current_action();

		foreach ( $services as $name => $service ) {
			$cats = array_intersect_key( $this->categories,
				array_flip( $service->get_categories() )
			);
?>
<div class="card<?php echo $service->is_active() ? ' active' : ''; ?>" id="<?php echo esc_attr( $name ); ?>">
<?php $service->icon(); ?>
<h2 class="title"><?php echo esc_html( $service->get_title() ); ?></h2>
<div class="infobox">
<?php echo esc_html( implode( ', ', $cats ) ); ?>
<br />
<?php $service->link(); ?>
</div>
<br class="clear" />

<div class="inside">
<?php
			if ( $singular ) {
				$service->display( $action );
			} else {
				$service->display();
			}
?>
</div>
</div>
<?php
		}
	}

}


/**
 * Abstract class for services.
 *
 * Only instances of this class's subclasses are allowed to be
 * listed on the Integration page.
 */
abstract class WPCF7_Service {

	abstract public function get_title();
	abstract public function is_active();


	public function get_categories() {
		return array();
	}


	public function icon() {
		return '';
	}


	public function link() {
		return '';
	}


	public function load( $action = '' ) {
	}


	public function display( $action = '' ) {
	}


	public function admin_notice( $message = '' ) {
	}

}


/**
 * Class for services that use OAuth.
 *
 * While this is not an abstract class, subclassing this class for
 * your aim is advised.
 */
class WPCF7_Service_OAuth2 extends WPCF7_Service {

	protected $client_id = '';
	protected $client_secret = '';
	protected $access_token = '';
	protected $refresh_token = '';
	protected $authorization_endpoint = 'https://example.com/authorization';
	protected $token_endpoint = 'https://example.com/token';


	public function get_title() {
		return '';
	}


	public function is_active() {
		return ! empty( $this->refresh_token );
	}


	protected function save_data() {
	}


	protected function reset_data() {
	}


	protected function get_redirect_uri() {
		return admin_url();
	}


	protected function menu_page_url( $args = '' ) {
		return menu_page_url( 'wpcf7-integration', false );
	}


	public function load( $action = '' ) {
		if ( 'auth_redirect' === $action ) {
			$code = wpcf7_superglobal_get( 'code' );

			if ( $code ) {
				$this->request_token( $code );
			}

			if ( ! empty( $this->access_token ) ) {
				$message = 'success';
			} else {
				$message = 'failed';
			}

			wp_safe_redirect( $this->menu_page_url(
				array(
					'action' => 'setup',
					'message' => $message,
				)
			) );

			exit();
		}
	}


	protected function authorize( $scope = '' ) {
		$endpoint = add_query_arg(
			array(
				'response_type' => 'code',
				'client_id' => $this->client_id,
				'redirect_uri' => urlencode( $this->get_redirect_uri() ),
				'scope' => $scope,
			),
			$this->authorization_endpoint
		);

		if ( wp_redirect( sanitize_url( $endpoint ) ) ) {
			exit();
		}
	}


	protected function get_http_authorization_header( $scheme = 'basic' ) {
		$scheme = strtolower( trim( $scheme ) );

		switch ( $scheme ) {
			case 'bearer':
				return sprintf( 'Bearer %s', $this->access_token );
			case 'basic':
			default:
				return sprintf( 'Basic %s',
					base64_encode( $this->client_id . ':' . $this->client_secret )
				);
		}
	}


	protected function request_token( $authorization_code ) {
		$endpoint = add_query_arg(
			array(
				'code' => $authorization_code,
				'redirect_uri' => urlencode( $this->get_redirect_uri() ),
				'grant_type' => 'authorization_code',
			),
			$this->token_endpoint
		);

		$request = array(
			'headers' => array(
				'Authorization' => $this->get_http_authorization_header( 'basic' ),
			),
		);

		$response = wp_remote_post( sanitize_url( $endpoint ), $request );
		$response_code = (int) wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$response_body = json_decode( $response_body, true );

		if ( WP_DEBUG and 400 <= $response_code ) {
			$this->log( $endpoint, $request, $response );
		}

		if ( 401 === $response_code ) { // Unauthorized
			$this->access_token = null;
			$this->refresh_token = null;
		} else {
			if ( isset( $response_body['access_token'] ) ) {
				$this->access_token = $response_body['access_token'];
			} else {
				$this->access_token = null;
			}

			if ( isset( $response_body['refresh_token'] ) ) {
				$this->refresh_token = $response_body['refresh_token'];
			} else {
				$this->refresh_token = null;
			}
		}

		$this->save_data();

		return $response;
	}


	protected function refresh_token() {
		$endpoint = add_query_arg(
			array(
				'refresh_token' => $this->refresh_token,
				'grant_type' => 'refresh_token',
			),
			$this->token_endpoint
		);

		$request = array(
			'headers' => array(
				'Authorization' => $this->get_http_authorization_header( 'basic' ),
			),
		);

		$response = wp_remote_post( sanitize_url( $endpoint ), $request );
		$response_code = (int) wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		$response_body = json_decode( $response_body, true );

		if ( WP_DEBUG and 400 <= $response_code ) {
			$this->log( $endpoint, $request, $response );
		}

		if ( 401 === $response_code ) { // Unauthorized
			$this->access_token = null;
			$this->refresh_token = null;
		} else {
			if ( isset( $response_body['access_token'] ) ) {
				$this->access_token = $response_body['access_token'];
			} else {
				$this->access_token = null;
			}

			if ( isset( $response_body['refresh_token'] ) ) {
				$this->refresh_token = $response_body['refresh_token'];
			}
		}

		$this->save_data();

		return $response;
	}


	protected function remote_request( $url, $request = array() ) {
		static $refreshed = false;

		$request = wp_parse_args( $request, array() );

		$request['headers'] = array_merge(
			$request['headers'],
			array(
				'Authorization' => $this->get_http_authorization_header( 'bearer' ),
			)
		);

		$response = wp_remote_request( sanitize_url( $url ), $request );

		if ( 401 === wp_remote_retrieve_response_code( $response )
		and ! $refreshed ) {
			$this->refresh_token();
			$refreshed = true;

			$response = $this->remote_request( $url, $request );
		}

		return $response;
	}


	protected function log( $url, $request, $response ) {
		wpcf7_log_remote_request( $url, $request, $response );
	}

}
