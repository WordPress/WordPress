<?php

/**
 * Wrapper for MailChimp's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_MailChimp extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = '';

	/**
	 * @inheritDoc
	 * Use this variable to hold the pattern and update $BASE_URL dynamically when needed
	 */
	public $BASE_URL_PATTERN = 'https://@datacenter@.api.mailchimp.com/3.0';

	/**
	 * @inheritDoc
	 */
	public $http_auth = array(
		'username' => '-',
		'password' => 'api_key',
	);

	/**
	 * @inheritDoc
	 */
	public $name = 'MailChimp';

	/**
	 * @inheritDoc
	 */
	public $slug = 'mailchimp';

	public function __construct( $owner, $account_name, $api_key = '' ) {
		parent::__construct( $owner, $account_name, $api_key );

		if ( ! empty( $this->data['api_key'] ) ) {
			$this->_set_base_url();
		}

		$this->http_auth['username'] = $owner;
	}

	protected function _add_note_to_subscriber( $email, $url ) {
		$email = md5( $email );

		$this->prepare_request( "{$url}/$email/notes", 'POST' );

		$this->request->BODY = json_encode( array( 'note' => $this->SUBSCRIBED_VIA ) );

		$this->make_remote_request();
	}

	protected function _fetch_subscriber_groups() {
		$query = array( 'count' => 500, 'fields' => 'segments.id,segments.name,segments.member_count' );

		foreach ( array_keys( $this->data['lists'] ) as $list_id ) {
			$url = "{$this->BASE_URL}/lists/{$list_id}/segments";

			$this->prepare_request( $url, 'GET', false, $query );
			$this->make_remote_request();

			if ( ! $this->response->ERROR && ! empty( $this->response->DATA['segments'] ) ) {
				$groups = $this->response->DATA['segments'];
				$this->_process_subscriber_groups( $list_id, $groups );
			}
		}
	}

	protected function _process_subscriber_groups( $list_id, $groups ) {
		$subscriber_groups = array();

		foreach ( $groups as $group ) {
			$group_id                       = $group['id'];
			$subscriber_groups[ $group_id ] = $this->transform_data_to_our_format( $group, 'subscriber_group' );
		}

		$this->data['lists'][ $list_id ]['subscriber_groups'] = $subscriber_groups;
	}

	protected function _set_base_url() {
		$api_key_pieces = explode( '-', $this->data['api_key'] );
		$datacenter     = empty( $api_key_pieces[1] ) ? '' : $api_key_pieces[1];
		$this->BASE_URL = str_replace( '@datacenter@', $datacenter, $this->BASE_URL_PATTERN );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		if ( empty( $this->data['api_key'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_set_base_url();

		/**
		 * The maximum number of subscriber lists to request from Mailchimp's API.
		 *
		 * @since 2.0.0
		 *
		 * @param int $max_lists
		 */
		$max_lists = (int) apply_filters( 'et_core_api_email_mailchimp_max_lists', 250 );

		$url = "{$this->BASE_URL}/lists?count={$max_lists}&fields=lists.name,lists.id,lists.stats";

		$this->prepare_request( $url );

		$this->response_data_key = 'lists';

		$result = parent::fetch_subscriber_lists();

		return $result;
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
		$custom_fields_key = 'merge_fields';

		$keymap = array(
			'list'             => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => 'stats.member_count',
			),
			'subscriber'       => array(
				'email'     => 'email_address',
				'name'      => 'merge_fields.FNAME',
				'last_name' => 'merge_fields.LNAME',
			),
			'subscriber_group' => array(
				'group_id'          => 'id',
				'name'              => 'name',
				'subscribers_count' => 'member_count'
			),
			'error' => array(
				'error_message' => 'detail',
			),
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$dbl_optin = empty( $args['dbl_optin'] );
		$list_id   = $args['list_id'];
		$url       = "{$this->BASE_URL}/lists/{$list_id}/members";
		$args      = $this->transform_data_to_provider_format( $args, 'subscriber' );

		$args['ip_signup'] = et_core_get_ip_address();
		$args['status']    = $dbl_optin ? 'pending' : 'subscribed';

		$this->prepare_request( $url, 'POST', false, $args, true );

		$result = parent::subscribe( $args, $url );

		if ( 'success' === $result ) {
			$this->_add_note_to_subscriber( $args['email_address'], $url );
		}

		if ( false !== stripos( $result, 'already a list member' ) ) {
			$result = 'success';
		} else if ( false !== stripos( $result, 'has signed up to a lot of lists ' ) ) {
			// return message which can be translated. Generic Mailchimp messages are not translatable.
			$result = esc_html__( 'You have signed up to a lot of lists very recently, please try again later', 'et_core' );
		}

		return $result;
	}
}
