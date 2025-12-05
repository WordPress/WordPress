<?php

namespace Yoast\WP\SEO\Actions\SEMrush;

use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Class SEMrush_Options_Action
 */
class SEMrush_Options_Action {

	/**
	 * The Options_Helper instance.
	 *
	 * @var Options_Helper
	 */
	protected $options_helper;

	/**
	 * SEMrush_Options_Action constructor.
	 *
	 * @param Options_Helper $options_helper The WPSEO options helper.
	 */
	public function __construct( Options_Helper $options_helper ) {
		$this->options_helper = $options_helper;
	}

	/**
	 * Stores SEMrush country code in the WPSEO options.
	 *
	 * @param string $country_code The country code to store.
	 *
	 * @return object The response object.
	 */
	public function set_country_code( $country_code ) {
		// The country code has already been validated at this point. No need to do that again.
		$success = $this->options_helper->set( 'semrush_country_code', $country_code );

		if ( $success ) {
			return (object) [
				'success' => true,
				'status'  => 200,
			];
		}
		return (object) [
			'success' => false,
			'status'  => 500,
			'error'   => 'Could not save option in the database',
		];
	}
}
