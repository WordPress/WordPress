<?php

namespace Yoast\WP\SEO\Integrations\Watchers;

use WPSEO_Utils;
use Yoast\WP\SEO\Config\Indexing_Reasons;

/**
 * Watches the stripcategorybase key in wpseo_titles, in order to clear the permalink of the category indexables.
 */
class Indexable_Category_Permalink_Watcher extends Indexable_Permalink_Watcher {

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'update_option_wpseo_titles', [ $this, 'check_option' ], 10, 2 );
	}

	/**
	 * Checks if the stripcategorybase key in wpseo_titles has a change in value, and if so,
	 * clears the permalink for category indexables.
	 *
	 * @param array $old_value The old value of the wpseo_titles option.
	 * @param array $new_value The new value of the wpseo_titles option.
	 *
	 * @return void
	 */
	public function check_option( $old_value, $new_value ) {
		// If this is the first time saving the option, in which case its value would be false.
		if ( $old_value === false ) {
			$old_value = [];
		}

		// If either value is not an array, return.
		if ( ! \is_array( $old_value ) || ! \is_array( $new_value ) ) {
			return;
		}

		// If both values aren't set, they haven't changed.
		if ( ! isset( $old_value['stripcategorybase'] ) && ! isset( $new_value['stripcategorybase'] ) ) {
			return;
		}

		// If a new value has been set for 'stripcategorybase', clear the category permalinks.
		if ( $old_value['stripcategorybase'] !== $new_value['stripcategorybase'] ) {
			$this->indexable_helper->reset_permalink_indexables( 'term', 'category', Indexing_Reasons::REASON_CATEGORY_BASE_PREFIX );
			// Clear the rewrites, so the new permalink structure is used.
			WPSEO_Utils::clear_rewrites();
		}
	}
}
