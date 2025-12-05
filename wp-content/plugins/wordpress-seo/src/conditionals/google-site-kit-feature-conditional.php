<?php

namespace Yoast\WP\SEO\Conditionals;

use Yoast\WP\SEO\Helpers\Options_Helper;

/**
 * Conditional for the Google Site Kit feature.
 */
class Google_Site_Kit_Feature_Conditional implements Conditional {

	/**
	 * The options helper.
	 *
	 * @var Options_Helper
	 */
	private $options;

	/**
	 * The constructor.
	 *
	 * @param Options_Helper $options The options helper.
	 */
	public function __construct( Options_Helper $options ) {
		$this->options = $options;
	}

	/**
	 * Returns `true` when the Site Kit feature is enabled.
	 *
	 * @return bool `true` when the Site Kit feature is enabled.
	 */
	public function is_met() {
		return $this->options->get( 'google_site_kit_feature_enabled' ) === true || \apply_filters( 'googlesitekit_is_feature_enabled', false, 'yoastIntegration' );
	}
}
