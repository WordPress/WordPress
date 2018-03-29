<?php

/**
 * High-level wrapper for interacting with the external API's offered by 3rd-party mailing list providers.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API
 */
abstract class ET_Core_API_Email_Provider extends ET_Core_API_Service  {

	/**
	 * The URL from which subscriber lists for this account can be retrieved.
	 *
	 * @var string
	 */
	public $LISTS_URL;

	/**
	 * "Subscribed via..." translated string.
	 *
	 * @var string
	 */
	public $SUBSCRIBED_VIA;

	/**
	 * ET_Core_API_Email_Provider constructor.
	 *
	 * @inheritDoc
	 */
	public function __construct( $owner = '', $account_name = '', $api_key = '' ) {
		$this->service_type = 'email';

		parent::__construct( $owner, $account_name, $api_key );

		$this->SUBSCRIBED_VIA = sprintf( '%1$s %2$s.', esc_html__( 'Subscribed via', 'et_core' ), ucfirst( $this->owner ) );
	}

	/**
	 * @inheritDoc
	 */
	protected function _get_data() {
		$options = parent::_get_data();

		// return empty array in case of empty name
		if ( '' === $this->account_name ) {
			return array();
		}

		$provider = sanitize_text_field( $this->slug );
		$account  = sanitize_text_field( $this->account_name );

		if ( ! isset( $options['accounts'][ $provider ][ $account ] ) ) {
			$options['accounts'][ $provider ][ $account ] = array();
			update_option( "et_core_api_email_options", $options );
		}

		return $options['accounts'][ $provider ][ $account ];
	}

	/**
	 * Processes subscriber lists data from the provider's API and returns only the data we're interested in.
	 *
	 * @since 1.1.0
	 *
	 * @param array $lists Subscriber lists data to process.
	 *
	 * @return array
	 */
	protected function _process_subscriber_lists( $lists ) {
		$id_key = $this->data_keys['list']['list_id'];
		$result = array();

		foreach ( (array) $lists as $list ) {
			if ( ! is_array( $list ) ) {
				$list = (array) $list;
			}

			if ( ! isset( $list[ $id_key ] ) ) {
				continue;
			}

			$id            = $list[ $id_key ];
			$result[ $id ] = $this->transform_data_to_our_format( $list, 'list' );

			if ( ! array_key_exists( 'subscribers_count', $result[ $id ] ) ) {
				$result[ $id ]['subscribers_count'] = 0;
			}
		}

		return $result;
	}

	/**
	 * Returns whether or not an account exists in the database.
	 *
	 * @param string $provider
	 * @param string $account_name
	 *
	 * @return bool
	 */
	public static function account_exists( $provider, $account_name ) {
		$all_accounts = self::get_accounts();

		return isset( $all_accounts[ $provider ][ $account_name ] );
	}

	/**
	 * @inheritDoc
	 */
	public function delete() {
		self::remove_account( $this->slug, $this->account_name );

		$this->account_name = '';

		$this->_get_data();
	}

	/**
	 * Retrieves the email accounts data from the database.
	 *
	 * @return array
	 */
	public static function get_accounts() {
		$options = (array) get_option( 'et_core_api_email_options' );

		return isset( $options['accounts'] ) ? $options['accounts'] : array();
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		$keymap['custom_fields']     = $this->_get_custom_fields();
		$keymap['custom_fields_key'] = $custom_fields_key;
		return $keymap;
	}

	/**
	 * Retrieves the subscriber lists for the account assigned to the current instance.
	 *
	 * @return string 'success' if successful, an error message otherwise.
	 */
	public function fetch_subscriber_lists() {
		if ( null === $this->request || $this->request->COMPLETE ) {
			$this->prepare_request( $this->LISTS_URL );
		}

		$this->make_remote_request();
		$result = 'success';

		if ( false !== $this->response_data_key && empty( $this->response_data_key ) ) {
			// Let child class handle parsing the response data themselves.
			return '';
		}

		if ( $this->response->ERROR ) {
			return $this->get_error_message();
		}

		if ( false === $this->response_data_key ) {
			// The data returned by the service is not nested.
			$data = $this->response->DATA;
		} else {
			// The data returned by the service is nested under a single key.
			$data = $this->response->DATA[ $this->response_data_key ];
		}

		if ( ! empty( $data ) ) {
			$this->data['lists'] = $this->_process_subscriber_lists( $data );
			$this->data['is_authorized'] = true;
			$this->save_data();
		}

		return $result;
	}

	/**
	 * Remove an account
	 *
	 * @param $provider
	 * @param $account_name
	 */
	public static function remove_account( $provider, $account_name ) {
		$options = (array) get_option( 'et_core_api_email_options' );

		unset( $options['accounts'][ $provider ][ $account_name ] );

		update_option( 'et_core_api_email_options', $options );
	}

	/**
	 * @inheritDoc
	 */
	public function save_data() {
		self::update_account( $this->slug, $this->account_name, $this->data );
	}

	/**
	 * @inheritDoc
	 */
	public function set_account_name( $name ) {
		$this->account_name = $name;
		$this->data = $this->_get_data();
	}

	/**
	 * Makes an HTTP POST request to add a subscriber to a list.
	 *
	 * @param string[] $args Data for the POST request.
	 * @param string   $url  The URL for the POST request. Optional when called on child classes.
	 *
	 * @return string 'success' if successful, an error message otherwise.
	 */
	public function subscribe( $args, $url = '' ) {
		if ( null === $this->request || $this->request->COMPLETE ) {
			if ( ! in_array( 'ip_address', $args ) ) {
				$args['ip_address'] = et_core_get_ip_address();
			}

			$args = $this->transform_data_to_provider_format( $args, 'subscriber' );

			$this->prepare_request( $url, 'POST', false, $args );
		} else if ( is_array( $this->request->BODY ) ) {
			$this->request->BODY = array_merge( $this->request->BODY, $args );
		} else if ( ! $this->request->JSON_BODY ) {
			$this->request->BODY = $args;
		}

		$this->make_remote_request();

		return $this->response->ERROR ? $this->get_error_message() : 'success';
	}

	/**
	 * Updates the data for a provider account.
	 *
	 * @param string $provider The provider's slug.
	 * @param string $account  The account name.
	 * @param array  $data     The new data for the account.
	 */
	public static function update_account( $provider, $account, $data ) {
		$options       = (array) get_option( 'et_core_api_email_options' );
		$existing_data = array();

		if ( empty( $account ) || empty( $provider ) ) {
			return;
		}

		$provider = sanitize_text_field( $provider );
		$account  = sanitize_text_field( $account );

		if ( isset( $options['accounts'][ $provider ][ $account ] ) ) {
			$existing_data = $options['accounts'][ $provider ][ $account ];
		}

		$options['accounts'][ $provider ][ $account ] = array_merge( $existing_data, $data );

		update_option( 'et_core_api_email_options', $options );
	}
}
