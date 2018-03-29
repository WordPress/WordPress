<?php

/**
 * Wrapper for MailerLite's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_MailerLite extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = 'https://api.mailerlite.com/api/v2';

	/**
	 * @inheritDoc
	 */
	public $LISTS_URL = 'https://api.mailerlite.com/api/v2/groups';

	/**
	 * @inheritDoc
	 */
	public $name = 'MailerLite';

	/**
	 * @inheritDoc
	 */
	public $slug = 'mailerlite';

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
		if ( empty( $this->custom_headers ) && isset( $this->data['api_key'] ) ) {
			$this->custom_headers = array( 'X-MailerLite-ApiKey' => "{$this->data['api_key']}" );
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
		$custom_fields_key = 'fields';

		$keymap = array(
			'list'       => array(
				'list_id'           => 'id',
				'name'              => 'name',
				'subscribers_count' => 'active',
			),
			'subscriber' => array(
				'name'      => 'fields.name',
				'last_name' => 'fields.last_name',
				'email'     => 'email',
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
		if ( empty( $this->data['api_key'] ) ) {
			return $this->API_KEY_REQUIRED;
		}

		$this->_maybe_set_custom_headers();

		$this->response_data_key = false;

		return parent::fetch_subscriber_lists();
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$url = "{$this->LISTS_URL}/{$args['list_id']}/subscribers";

		return parent::subscribe( $args, $url );
	}
}
