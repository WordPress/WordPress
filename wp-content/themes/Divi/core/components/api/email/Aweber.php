<?php

/**
 * Wrapper for Aweber's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_Aweber extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $ACCESS_TOKEN_URL = 'https://auth.aweber.com/1.0/oauth/access_token';

	/**
	 * @inheritDoc
	 */
	public $AUTHORIZATION_URL = 'https://auth.aweber.com/1.0/oauth/authorize';

	/**
	 * @inheritDoc
	 */
	public $REQUEST_TOKEN_URL = 'https://auth.aweber.com/1.0/oauth/request_token';

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.aweber.com/1.0';

	/**
	 * @var string
	 */
	public $accounts_url;

	/**
	 * @inheritDoc
	 */
	public $name = 'Aweber';

	/**
	 * @inheritDoc
	 */
	public $slug = 'aweber';

	/**
	 * @inheritDoc
	 */
	public $oauth_version = '1.0a';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = true;

	/**
	 * ET_Core_API_Aweber constructor.
	 *
	 * @inheritDoc
	 */
	public function __construct( $owner, $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );
		$this->accounts_url = "{$this->BASE_URL}/accounts";
	}

	protected function _get_lists_collection_url() {
		$this->prepare_request( $this->accounts_url );
		$this->make_remote_request();
		$url = '';

		if ( ! $this->response->ERROR && ! empty( $this->response->DATA['entries'][0]['lists_collection_link'] ) ) {
			$url = $this->response->DATA['entries'][0]['lists_collection_link'];
		}

		return $url;
	}

	protected static function _parse_ID( $ID ) {
		$values = explode( '|', $ID );

		return ( count( $values ) === 6 ) ? $values : null;
	}

	/**
	 * Uses the app's authorization code to get an access token
	 */
	public function authenticate() {
		$key_parts = self::_parse_ID( $this->data['api_key'] );

		if ( null === $key_parts ) {
			return false;
		}

		list( $consumer_key, $consumer_secret, $request_token, $request_secret, $verifier ) = $key_parts;

		if ( ! $verifier ) {
			return false;
		}

		$this->data['consumer_key']    = $consumer_key;
		$this->data['consumer_secret'] = $consumer_secret;
		$this->data['access_key']      = $request_token;
		$this->data['access_secret']   = $request_secret;
		$this->oauth_verifier          = $verifier;

		// AWeber returns oauth access key in url query format :face_with_rolling_eyes:
		$this->http->expects_json = false;

		return parent::authenticate();
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'api_key' => array(
				'label' => esc_html__( 'Authorization Code', 'et_core' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_error_message() {
		$error_message = parent::get_error_message();

		return preg_replace('/https.*/m', '', $error_message );
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		$custom_fields_key = 'custom_fields';

		$keymap = array(
			'list'       => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => 'total_subscribers',
			),
			'subscriber' => array(
				'name'        => 'name',
				'email'       => 'email',
				'ad_tracking' => 'ad_tracking',
			),
			'error'      => array(
				'error_message' => 'error.message',
			),
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		$needs_to_authenticate = ! $this->is_authenticated() || ! $this->_initialize_oauth_helper();

		if ( $needs_to_authenticate && ! $this->authenticate() ) {
			$this->response->DATA = json_decode( $this->response->DATA, true );
			return $this->get_error_message();
		}

		$this->http->expects_json = true;
		$this->LISTS_URL          = $this->_get_lists_collection_url();

		if ( empty( $this->LISTS_URL ) ) {
			return '';
		}

		$this->response_data_key = 'entries';

		return parent::fetch_subscriber_lists();
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$lists_url = $this->_get_lists_collection_url();
		$url       = "{$lists_url}/{$args['list_id']}/subscribers";

		$params = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$params = array_merge( $params, array(
			'ws.op'      => 'create',
			'ip_address' => et_core_get_ip_address(),
			'misc_notes' => $this->SUBSCRIBED_VIA,
		) );

		// There is a bug in AWeber some characters not encoded properly on AWeber side when sending data in x-www-form-urlencoded format so use json instead
		$this->prepare_request( $url, 'POST', false, $params, true );
		$this->request->HEADERS['Content-Type'] = 'application/json';

		return parent::subscribe( $params, $url );
	}
}
