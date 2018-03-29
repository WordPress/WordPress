<?php

/**
 * Wrapper for ProviderName's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_ProviderName extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = '';

	/**
	 * @inheritDoc
	 */
	public $name = 'ProviderName';

	/**
	 * @inheritDoc
	 */
	public $slug = 'providername';

	/**
	 * @inheritDoc
	 * @internal If true, oauth endpoints properties must also be defined.
	 */
	public $uses_oauth = false;

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		// Implement get_account_fields() method.
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		// Implement get_data_keys() method.
		$keymap = array(
			'list'       => array(
				'list_id'           => '',
				'name'              => '',
				'subscribers_count' => '',
			),
			'subscriber' => array(
				'name'       => '',
				'email'      => '',
				'list_id'    => '',
				'ip_address' => '',
			),
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		// Implement get_subscriber_lists() method.
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		// Implement subscribe() method.
	}
}