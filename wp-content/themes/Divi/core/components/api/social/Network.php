<?php


abstract class ET_Core_API_SocialNetwork extends ET_Core_API_Service  {

	public function __construct( $owner = '', $account_name = '' ) {
		$this->service_type = 'social';

		parent::__construct( $owner, $account_name );
	}

	/**
	 * @inheritDoc
	 */
	protected function _get_data_keys() {
		// Implement _get_data_keys() method.
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		// Implement get_fields() method.
	}

	/**
	 * @inheritDoc
	 */
	protected function _get_accounts_data() {
		// Implement _get_accounts_data() method.
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		// Implement get_data_keymap() method.
	}

	/**
	 * @inheritDoc
	 */
	public function save_data() {
		// Implement save_data() method.
	}
}