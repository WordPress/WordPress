<?php

/**
 * Wrapper for Campaign Monitor's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_CampaignMonitor extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.createsend.com/api/v3.1';

	/**
	 * @inheritDoc
	 */
	public $http_auth = array(
		'username' => 'api_key',
		'password' => '-',
	);

	/**
	 * @inheritDoc
	 */
	public $name = 'CampaignMonitor';

	/**
	 * @inheritDoc
	 */
	public $slug = 'campaign_monitor';

	/**
	 * @inheritDoc
	 * @internal If true, oauth endpoints properties must also be defined.
	 */
	public $uses_oauth = false;

	public function __construct( $owner, $account_name, $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		$this->http_auth['password'] = $owner;
	}

	protected function _get_clients() {
		$url = "{$this->BASE_URL}/clients.json";

		$this->prepare_request( $url );
		$this->make_remote_request();

		if ( $this->response->ERROR ) {
			return $this->get_error_message();
		}

		return (array) $this->response->DATA;
	}

	protected function _get_subscriber_counts() {
		$subscriber_lists = $this->_process_subscriber_lists( $this->response->DATA );
		$with_counts      = array();

		foreach ( $subscriber_lists as $subscriber_list ) {
			$list_id                 = $subscriber_list['list_id'];
			$with_counts[ $list_id ] = $subscriber_list;
			$url                     = "{$this->BASE_URL}/lists/{$list_id}/stats.json";

			$this->prepare_request( $url );
			$this->make_remote_request();

			if ( $this->response->ERROR  ) {
				continue;
			}

			if ( isset( $this->response->DATA['TotalActiveSubscribers'] ) ) {
				$with_counts[ $list_id ]['subscribers_count'] = $this->response->DATA['TotalActiveSubscribers'];
			} else {
				$with_counts[ $list_id ]['subscribers_count'] = 0;
			}

			usleep( 500000 ); // 0.5 seconds
		}

		return $with_counts;
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
		$custom_fields_key = 'CustomFields';

		$keymap = array(
			'list'       => array(
				'list_id'           => 'ListID',
				'name'              => 'Name',
				'subscribers_count' => 'TotalActiveSubscribers',
			),
			'subscriber' => array(
				'name'  => 'Name',
				'email' => 'EmailAddress',
			),
			'error'      => array(
				'error_message' => 'Message',
			),
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

		$clients = $this->_get_clients();
		$lists   = array();

		if ( ! is_array( $clients ) ) {
			// Request failed with an error, return the error message.
			return $clients;
		}

		foreach ( $clients as $client_info ) {
			if ( empty( $client_info['ClientID'] ) ) {
				continue;
			}

			$url = "{$this->BASE_URL}/clients/{$client_info['ClientID']}/lists.json";

			$this->prepare_request( $url );

			parent::fetch_subscriber_lists();

			if ( $this->response->ERROR ) {
				return $this->get_error_message();
			}

			if ( isset( $this->response->DATA ) ) {
				$with_counts                 = $this->_get_subscriber_counts();
				$lists                       = $lists + $with_counts;
				$this->data['is_authorized'] = true;

				$this->save_data();
			}
		}

		if ( empty( $this->data['lists'] ) || ! empty( $lists ) ) {
			$this->data['lists'] = $lists;
			$this->save_data();
		}

		return $this->is_authenticated() ? 'success' : $this->FAILURE_MESSAGE;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$url    = "{$this->BASE_URL}/subscribers/{$args['list_id']}.json";
		$params = $this->transform_data_to_provider_format( $args, 'subscriber' );

		$params['CustomFields'][] = array( 'Key' => 'Note', 'Value' => $this->SUBSCRIBED_VIA );

		$this->prepare_request( $url, 'POST', false, $params, true );

		return parent::subscribe( $params, $url );
	}
}
