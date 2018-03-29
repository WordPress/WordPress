<?php

/**
 * Wrapper for ActiveCampaign's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_ActiveCampaign extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $name = 'ActiveCampaign';

	/**
	 * @inheritDoc
	 */
	public $slug = 'activecampaign';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = false;

	/**
	 * Returns the requests URL for the account assigned to this class instance.
	 *
	 * @return string
	 */
	protected function _get_requests_url() {
		return "{$this->data['api_url']}/admin/api.php";
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array(
			'api_key' => array(
				'label' => esc_html__( 'API Key', 'et_core' ),
			),
			'api_url' => array(
				'label'               => esc_html__( 'API URL', 'et_core' ),
				'apply_password_mask' => false,
			),
			'form_id' => array(
				'label'               => esc_html__( 'Form ID', 'et_core' ),
				'not_required'        => true,
				'apply_password_mask' => false,
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
				'subscribers_count' => 'subscriber_count',
			),
			'subscriber' => array(
				'email'     => 'email',
				'last_name' => 'last_name',
				'name'      => 'first_name',
			),
			'error'      => array(
				'error_message' => 'result_message',
			)
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) || empty( $this->data['api_url'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$query_args = array(
			'api_key'    => $this->data['api_key'],
			'api_action' => 'list_list',
			'api_output' => 'json',
			'ids'        => 'all',
			'full'       => '1',
		);

		$request_url = add_query_arg( $query_args, $this->_get_requests_url() );
		$request_url = esc_url_raw( $request_url, array( 'https' ) );

		$this->prepare_request( $request_url );
		$this->request->HEADERS['Content-Type'] = 'application/x-www-form-urlencoded';

		parent::fetch_subscriber_lists();

		if ( $this->response->ERROR ) {
			return $this->get_error_message();
		}

		$lists = array();

		foreach ( $this->response->DATA as $key => $list_data ) {
			if ( ! is_numeric( $key ) ) {
				continue;
			}

			if ( ! empty( $list_data ) ) {
				$lists[] = $list_data;
			}
		}

		$this->data['lists']         = $this->_process_subscriber_lists( $lists );
		$this->data['is_authorized'] = 'true';

		$this->save_data();

		return 'success';
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url= '' ) {
		$list_id_key    = 'p[' . $args['list_id'] . ']';
		$status_key     = 'status[' . $args['list_id'] . ']';
		$responders_key = 'instantresponders[' . $args['list_id'] . ']';
		$list_id        = $args['list_id'];
		$args           = $this->transform_data_to_provider_format( $args, 'subscriber', array( 'list_id' ) );

		$query_args = array(
			'api_key'    => $this->data['api_key'],
			'api_action' => 'contact_sync',
			'api_output' => 'json',
		);

		$url = esc_url_raw( add_query_arg( $query_args, $this->_get_requests_url() ) );

		$args[ $list_id_key ]    = $list_id;
		$args[ $status_key ]     = 1;
		$args[ $responders_key ] = 1;

		if ( ! empty( $this->data['form_id'] ) ) {
			$args['form'] = (int) $this->data['form_id'];
		}

		$this->prepare_request( $url, 'POST', false, $args );
		$this->request->HEADERS['Content-Type'] = 'application/x-www-form-urlencoded';

		return parent::subscribe( $args, $url );
	}
}