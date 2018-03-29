<?php

/**
 * Wrapper for Ontraport's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_Ontraport extends ET_Core_API_Email_Provider {
	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://api.ontraport.com/1/objects?objectID=5';

	/**
	 * @inheritDoc
	 */
	public $SUBSCRIBE_URL = 'https://api.ontraport.com/1/objects';

	/**
	 * @inheritDoc
	 */
	public $name = 'Ontraport';

	/**
	 * @inheritDoc
	 */
	public $slug = 'ontraport';

	/**
	 * @inheritDoc
	 * @internal If true, oauth endpoints properties must also be defined.
	 */
	public $uses_oauth = false;

	public function __construct( $owner = '', $account_name = '', $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		$this->_maybe_set_custom_headers();
	}


	protected function _maybe_set_custom_headers() {
		if ( empty( $this->custom_headers ) && isset( $this->data['api_key'] )  && isset( $this->data['client_id'] ) ) {
			$this->custom_headers = array(
				'Api-Appid' => sanitize_text_field( $this->data['client_id'] ),
				'Api-Key'   => sanitize_text_field( $this->data['api_key'] ),
			);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'api_key'   => array(
				'label' => esc_html__( 'API Key', 'et_core' ),
			),
			'client_id' => array(
				'label' => esc_html__( 'APP ID', 'et_core' ),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		$keymap = array(
			'list'       => array(
				'name'              => 'name',
				'list_id'           => 'drip_id',
				'subscribers_count' => 'subscriber_count',
			),
			'subscriber' => array(
				'name'      => 'firstname',
				'last_name' => 'lastname',
				'email'     => 'email',
				'list_id'   => 'updateSequence',
			),
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) || empty( $this->data['client_id'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_maybe_set_custom_headers();

		$this->response_data_key = 'data';

		return parent::fetch_subscriber_lists();
	}

	public function get_subscriber( $email ) {
		$query     = sprintf( '[{ "field":{"field":"email"}, "op":"=", "value":{"value":"%1$s"} }]', $email );
		$query_url = sprintf( '%1$s?objectID=0&condition=%2$s&listFields=id',
			$this->SUBSCRIBE_URL,
			rawurlencode( $query )
		);

		$this->prepare_request( $query_url, 'GET', false );
		$this->make_remote_request();

		$data_received = $this->response->DATA;

		if ( $data_received && ! empty( $data_received['data'] ) ) {
			return $data_received['data'][0]['id'];
		}

		return false;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$args                = $this->transform_data_to_provider_format( $args, 'subscriber' );
		$request_method      = 'POST';
		$existing_subscriber = $this->get_subscriber( $args['email'] );

		// update the `sequence` for existing subscriber using PUT method
		if ( false !== $existing_subscriber ) {
			$request_method = 'PUT';
			$sequence_id    = $args['updateSequence'];
			$args           = array(
				'id'             => $existing_subscriber,
				'updateSequence' => $sequence_id
			);
		}

		$args['objectID'] = 0;

		$this->prepare_request( $this->SUBSCRIBE_URL, $request_method, false, $args );
		$this->request->HEADERS['Api-Appid'] = sanitize_text_field( $this->data['client_id'] );
		$this->request->HEADERS['Api-Key'] = sanitize_text_field( $this->data['api_key'] );

		return parent::subscribe( $args, $this->SUBSCRIBE_URL );
	}
}
