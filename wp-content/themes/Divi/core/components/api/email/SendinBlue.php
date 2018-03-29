<?php

/**
 * Wrapper for SendinBlue's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_SendinBlue extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.sendinblue.com/v2.0';

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://api.sendinblue.com/v2.0/list';

	public $SUBSCRIBE_URL = 'https://api.sendinblue.com/v2.0/user/createdituser';

	public $USERS_URL = 'https://api.sendinblue.com/v2.0/user';

	/**
	 * @inheritDoc
	 */
	public $name = 'SendinBlue';

	/**
	 * @inheritDoc
	 */
	public $slug = 'sendinblue';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = false;

	public function __construct( $owner, $account_name, $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		$this->_maybe_set_custom_headers();
	}

	protected function _maybe_set_custom_headers() {
		if ( empty( $this->custom_headers ) && isset( $this->data['api_key'] ) ) {
			$this->custom_headers = array( 'api-key' => $this->data['api_key'] );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'api_key' => array(
				'label' => esc_html__( 'API Key', 'et_core' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		$keymap = array(
			'list'       => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => 'total_subscribers',
			),
			'subscriber' => array(
				'email'     => 'email',
				'name'      => 'attributes.NAME',
				'last_name' => 'attributes.SURNAME',
				'list_id'   => '@_listid',
			),
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	public function get_subscriber( $email ) {
		$this->prepare_request( "{$this->USERS_URL}/{$email}", 'GET' );
		$this->make_remote_request();

		if ( $this->response->ERROR || ! isset( $this->response->DATA['listid'] ) ) {
			return false;
		}

		if ( isset( $this->response->DATA['code'] ) && 'success' !== $this->response->DATA['code'] ) {
			return false;
		}

		return $this->response->DATA;
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		if ( empty( $this->custom_headers ) ) {
			$this->_maybe_set_custom_headers();
		}

		$params = array(
			"page"       => 1,
			"page_limit" => 2,
		);

		$this->prepare_request( $this->LISTS_URL, 'GET', false, $params );

		$this->request->data_format = 'body';
		$this->response_data_key    = 'data';

		parent::fetch_subscriber_lists();

		if ( $this->response->ERROR ) {
			return $this->response->ERROR_MESSAGE;
		}

		if ( isset( $this->response->DATA['code'] ) && 'success' !== $this->response->DATA['code'] ) {
			return $this->response->DATA['message'];
		}

		$result                      = 'success';
		$this->data['is_authorized'] = 'true';

		if ( ! empty( $this->response->DATA['data']['lists'] ) ) {
			$this->data['lists'] = $this->_process_subscriber_lists( $this->response->DATA['data']['lists'] );

			$this->save_data();
		}

		return $result;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$args['list_id'] = array( $args['list_id'] );
		$existing_user   = $this->get_subscriber( $args['email'] );

		if ( false !== $existing_user ) {
			$args['list_id'] = array_unique( array_merge( $args['list_id'], $existing_user['listid'] ) );
		}

		return parent::subscribe( $args, $this->SUBSCRIBE_URL );
	}
}
