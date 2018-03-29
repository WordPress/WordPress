<?php

/**
 * Wrapper for integration with Mailster plugin.
 *
 * @license
 * Copyright © 2017 Elegant Themes, Inc.
 * Copyright © 2017 Xaver Birsak
 *
 * @since   1.1.0
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_Mailster extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $name = 'Mailster';

	/**
	 * @inheritDoc
	 */
	public $slug = 'mailster';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = false;

	/**
	 * Creates a referrer string that includes the name of the opt-in used to subscribe.
	 *
	 * @param array $args The args array that was passed to {@link self::subscribe()}
	 *
	 * @return string
	 */
	protected function _get_referrer( $args ) {
		$optin_name = '';
		$owner      = ucfirst( $this->owner );

		if ( 'bloom' === $this->owner && isset( $args['optin_id'] ) ) {
			$optin_form = ET_Bloom::get_this()->dashboard_options[ $args['optin_id'] ];
			$optin_name = $optin_form['optin_name'];
		}

		return sprintf( '%1$s %2$s "%3$s" on %4$s',
			esc_html( $owner ),
			esc_html__( 'Opt-in', 'et_core' ),
			esc_html( $optin_name ),
			wp_get_referer()
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		return array();
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		$keymap = array(
			'list'       => array(
				'list_id'           => 'ID',
				'name'              => 'name',
				'subscribers_count' => 'subscribers',
			),
			'subscriber' => array(
				'dbl_optin' => 'status',
				'email'     => 'email',
				'last_name' => 'lastname',
				'name'      => 'firstname',
			),
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	public function fetch_subscriber_lists() {
		if ( ! function_exists( 'mailster' ) ) {
			return esc_html__( 'Mailster Newsletter Plugin is not enabled!', 'et_core' );
		}

		$lists         = mailster( 'lists' )->get( null, null, true );
		$error_message = esc_html__( 'No lists were found. Please create a Mailster list first!', 'et_core' );

		if ( $lists ) {
			$error_message               = 'success';
			$this->data['lists']         = $this->_process_subscriber_lists( $lists );
			$this->data['is_authorized'] = true;
			$this->save_data();
		}

		return $error_message;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		$error = esc_html__( 'An error occurred. Please try again later.', 'et_core' );

		if ( ! function_exists( 'mailster' ) ) {
			return $error;
		}

		$params       = $this->transform_data_to_provider_format( $args, 'subscriber', array( 'dbl_optin' ) );
		$extra_params = array(
			'status'   => 'disable' === $args['dbl_optin'] ? 0 : 1,
			'referrer' => $this->_get_referrer( $args ),
		);

		$params        = array_merge( $params, $extra_params );
		$subscriber_id = mailster( 'subscribers' )->add( $params, false );

		if ( is_wp_error( $subscriber_id ) ) {
			$result = $subscriber_id->get_error_message();
		} else if ( mailster( 'subscribers' )->assign_lists( $subscriber_id, $args['list_id'], false ) ) {
			$result = 'success';
		} else {
			$result = $error;
		}

		return $result;
	}
}
