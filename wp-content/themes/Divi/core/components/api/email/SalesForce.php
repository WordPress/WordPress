<?php

/**
 * Wrapper for SalesForce's API.
 *
 * @since   1.1.0
 *
 * @package ET\Core\API\Email
 */
class ET_Core_API_Email_SalesForce extends ET_Core_API_Email_Provider {

	/**
	 * @inheritDoc
	 */
	public $ACCESS_TOKEN_URL = 'https://login.salesforce.com/services/oauth2/token';

	/**
	 * @inheritDoc
	 */
	public $AUTHORIZATION_URL = 'https://login.salesforce.com/services/oauth2/authorize';

	/**
	 * @inheritDoc
	 */
	public $BASE_URL = '';

	/**
	 * @inheritDoc
	 */
	public $name = 'SalesForce';

	/**
	 * @inheritDoc
	 */
	public $slug = 'salesforce';

	/**
	 * @inheritDoc
	 */
	public $oauth_version = '2.0';

	/**
	 * @inheritDoc
	 */
	public $uses_oauth = true;

	/**
	 * ET_Core_API_SalesForce constructor.
	 *
	 * @inheritDoc
	 */
	public function __construct( $owner, $account_name = '' ) {
		parent::__construct( $owner, $account_name );

		$this->REDIRECT_URL = admin_url( 'admin.php?page=et_bloom_options', 'https' );

		$this->_set_base_url();
	}

	/**
	 * @return string
	 */
	protected function _fetch_subscriber_lists() {
		$query = urlencode( 'SELECT Id, Name, NumberOfLeads from Campaign LIMIT 100' );
		$url   = "{$this->BASE_URL}/services/data/v39.0/query?q={$query}";

		$this->response_data_key = 'records';

		$this->prepare_request( $url );

		return parent::fetch_subscriber_lists();
	}

	public function _set_base_url() {
		$this->BASE_URL = empty( $this->data['login_url'] ) ? '' : $this->data['login_url'];
	}

	public function authenticate() {
		$this->data['consumer_secret'] = $this->data['client_secret'];
		$this->data['consumer_key']    = $this->data['api_key'];

		return parent::authenticate();
	}

	/**
	 * @inheritDoc
	 */
	public function get_account_fields() {
		// SalesForce supports OAuth for SSL websites so generate different fields in this case
		if ( is_ssl() ) {
			return array(
				'organization_id' => array(
					'label'        => esc_html__( 'Organization ID', 'et_core' ),
					'not_required' => true,
				),
				'login_url'       => array(
					'label' => esc_html__( 'Instance URL', 'et_core' ),
				),
				'api_key'         => array(
					'label' => esc_html__( 'Consumer Key', 'et_core' ),
				),
				'client_secret'   => array(
					'label' => esc_html__( 'Consumer Secret', 'et_core' ),
				),
			);

		} else {
			return array(
				'organization_id' => array(
					'label' => esc_html__( 'Organization ID', 'et_core' ),
				),
			);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function fetch_subscriber_lists() {
		$this->_set_base_url();
		// SalesForce supports 2 types of authentication: Simple and OAuth2
		if ( isset( $this->data['api_key'] ) && isset( $this->data['client_secret'] ) ) {
			// OAuth2
			return $this->is_authenticated() ? $this->_fetch_subscriber_lists() : $this->authenticate();
		} else if ( isset( $this->data['organization_id'] ) && '' !== $this->data['organization_id'] ) {
			// Simple
			$this->data['is_authorized'] = 'true';

			$this->save_data();

			// return 'success' immediately in case of simple authentication. Lists cannot be retrieved with this type.
			return 'success';
		} else {
			return esc_html__( 'Organization ID cannot be empty', 'et_core' );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function get_data_keymap( $keymap = array(), $custom_fields_key = '' ) {
		$custom_fields_key = 'custom_fields';

		$keymap = array(
			'list'       => array(
				'list_id'           => 'Id',
				'name'              => 'Name',
				'subscribers_count' => 'NumberOfLeads',
			),
			'subscriber' => array(
				'name'      => 'FirstName',
				'last_name' => 'LastName',
				'email'     => 'Email',
			),
		);

		return parent::get_data_keymap( $keymap, $custom_fields_key );
	}

	public function get_subscriber( $email ) {
		$query = urlencode( "SELECT Id from Lead where Email='{$email}' LIMIT 100" );
		$url   = "{$this->BASE_URL}/services/data/v39.0/query?q={$query}";

		$this->response_data_key = 'records';

		$this->prepare_request( $url );
		$this->make_remote_request();

		$response = $this->response;

		if ( $response->ERROR || empty( $response->DATA['records'] ) ) {
			return false;
		}

		return isset( $response->DATA['records'][0]['Id'] ) ? $response->DATA['records'][0]['Id'] : false;
	}

	/**
	 * @inheritDoc
	 */
	public function subscribe( $args, $url = '' ) {
		if ( empty( $this->data['access_secret'] ) ) {
			// Try to use simple web form
			return $this->subscribe_salesforce_web( $args );
		}

		$error_message = esc_html__( 'An error occurred. Please try again.', 'et_core' );
		$subscriber_id = $this->get_subscriber( $args['email'] );

		if ( ! $subscriber_id ) {
			$url                = "{$this->BASE_URL}/services/data/v39.0/sobjects/Lead";
			$content            = $this->transform_data_to_provider_format( $args, 'subscriber' );
			$content['Company'] = 'Bloom';

			$this->prepare_request( $url, 'POST', false, json_encode( $content ), true );

			$this->response_data_key = false;

			$result = parent::subscribe( $content, $url );

			if ( 'success' !== $result || empty( $this->response->DATA['id'] ) ) {
				return $error_message;
			}

			$subscriber_id = $this->response->DATA['id'];
		}

		$url     = "{$this->BASE_URL}/services/data/v39.0/sobjects/CampaignMember";
		$content = array(
			'LeadId'     => $subscriber_id,
			'CampaignId' => $args['list_id'],
		);

		$this->prepare_request( $url, 'POST', false, json_encode( $content ), true );

		$result = parent::subscribe( $content, $url );

		if ( 'success' !== $result && ! empty( $this->response->DATA['errors'] ) ) {
			return $this->response->DATA['errors'][0];
		} else if ( 'success' !== $result ) {
			return $error_message;
		}

		return 'success';
	}

	/**
	 * Post web-to-lead request to SalesForce
	 *
	 * @return string
	 */
	public function subscribe_salesforce_web( $args ) {
		if ( ! isset( $this->data['organization_id'] ) || '' === $this->data['organization_id'] ) {
			return esc_html__( 'Unknown Organization ID', 'et_core' );
		}

		// Define SalesForce web-to-lead endpoint
		$url = "https://www.salesforce.com/servlet/servlet.WebToLead?encoding=UTF-8";

		// Prepare arguments for web-to-lead POST
		$form_args = array(
			'body' => array(
				'oid'    => sanitize_text_field( $this->data['organization_id'] ),
				'retURL' => esc_url( home_url( '/' ) ),
				'email'  => sanitize_email( $args['email'] ),
			),
		);

		if ( '' !== $args['name'] ) {
			$form_args['body']['first_name'] = sanitize_text_field( $args['name'] );
		}

		if ( '' !== $args['last_name'] ) {
			$form_args['body']['last_name'] = sanitize_text_field( $args['last_name'] );
		}

		// Post to SalesForce web-to-lead endpoint
		$post = wp_remote_post( $url, $form_args );

		if ( ! is_wp_error( $post ) ) {
			return 'success';
		}

		return esc_html__( 'An error occurred. Please try again.', 'et_core' );
	}
}
