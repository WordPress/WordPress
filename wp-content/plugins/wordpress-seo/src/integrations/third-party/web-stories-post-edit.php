<?php

namespace Yoast\WP\SEO\Integrations\Third_Party;

use Yoast\WP\SEO\Conditionals\Admin\Post_Conditional;
use Yoast\WP\SEO\Conditionals\Web_Stories_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;

/**
 * Web Stories integration.
 */
class Web_Stories_Post_Edit implements Integration_Interface {

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Web_Stories_Conditional::class, Post_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_admin_l10n', [ $this, 'add_admin_l10n' ] );
	}

	/**
	 * Adds a isWebStoriesIntegrationActive variable to the Adminl10n array.
	 *
	 * @param array $input The array to add the isWebStoriesIntegrationActive to.
	 *
	 * @return array The passed array with the additional isWebStoriesIntegrationActive variable set to 1 if we are editing a web story.
	 */
	public function add_admin_l10n( $input ) {
		if ( \get_post_type() === 'web-story' ) {
			$input['isWebStoriesIntegrationActive'] = 1;
		}
		return $input;
	}
}
