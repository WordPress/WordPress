<?php

namespace Yoast\WP\SEO\Actions;

use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Class Integrations_Action.
 */
class Integrations_Action {

	/**
	 * The Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * Integrations_Action constructor.
	 *
	 * @param Options_Helper $options_helper The WPSEO options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Sets an integration state.
	 *
	 * @param string $integration_name The name of the integration to activate/deactivate.
	 * @param bool   $value            The value to store.
	 *
	 * @return object The response object.
	 */
	public function set_integration_active( $integration_name, $value ) {
		$option_name  = $integration_name . '_integration_active';
		$success      = true;
		$option_value = $this->options_helper->get( $option_name );

		if ( $option_value !== $value ) {
			$success = $this->options_helper->set( $option_name, $value );
		}

		if ( $success ) {
			return (object) [
				'success' => true,
				'status'  => 200,
			];
		}
		return (object) [
			'success' => false,
			'status'  => 500,
			'error'   => 'Could not save the option in the database',
		];
	}
}
