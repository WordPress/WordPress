<?php

/**
 * Wrapper for Feedblitz's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_Feedblitz extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://www.feedblitz.com';

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://www.feedblitz.com/f.api/syndications';

	/**
	 * @inheritDoc
	 */
	public $SUBSCRIBE_URL = 'https://www.feedblitz.com/f';

	/**
	 * @inheritDoc
	 */
	public $name = 'Feedblitz';

	/**
	 * @inheritDoc
	 */
	public $slug = 'feedblitz';

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
		$custom_fields_key = '';

		$keymap = array(
			'list'       => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => 'subscribersummary.subscribers',
			),
			'subscriber' => array(
				'list_id'   => 'listid',
				'email'     => 'email',
				'name'      => 'FirstName',
				'last_name' => 'LastName',
			),
			'error'      => array(
				'error_message' => 'rsp.err.@attributes.msg',
			)
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->http->expects_json = false;
		$this->response_data_key  = false;
		$this->LISTS_URL          = add_query_arg( 'key', $this->data['api_key'], $this->LISTS_URL );

		parent::fetch_subscriber_lists();

		$response = $this->data_utils->process_xmlrpc_response( $this->response->DATA, true );
		$response = $this->data_utils->xml_to_array( $response );

		if ( $this->response->ERROR || ! empty( $response['rsp']['err']['@attributes']['msg'] ) ) {
			return $this->get_error_message();
		}

		$this->data['lists']         = $this->_process_subscriber_lists( $response['syndications']['syndication'] );
		$this->data['is_authorized'] = true;

		$this->save_data();

		return 'success';
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$query_args = array(
			'email'      => rawurlencode( $args['email'] ),
			'first_name' => empty( $args['name'] ) ? '' : rawurlencode( $args['name'] ),
			'last_name'  => empty( $args['last_name'] ) ? '' : rawurlencode( $args['last_name'] ),
		);

		$query        = $this->transform_data_to_provider_format( $query_args, 'subscriber' );
		$query['key'] = $this->data['api_key'];
		$url          = add_query_arg( $query, "{$this->SUBSCRIBE_URL}?SimpleApiSubscribe" );

		$this->prepare_request( $url, 'GET', false, null, false, false );
		$this->make_remote_request();

		$response = $this->data_utils->process_xmlrpc_response( $this->response->DATA, true );
		$response = $this->data_utils->xml_to_array( $response );

		if ( $this->response->ERROR || ! empty( $response['rsp']['err']['@attributes']['msg'] ) ) {
			return $this->get_error_message();
		}

		if ( ! empty( $response['rsp']['success']['@attributes']['msg'] ) ) {
			return $response['rsp']['success']['@attributes']['msg'];
		}

		return 'success';
	}
}