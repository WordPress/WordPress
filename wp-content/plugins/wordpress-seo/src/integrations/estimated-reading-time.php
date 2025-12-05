<?php

namespace Yoast\WP\SEO\Integrations;

use Yoast\WP\SEO\Conditionals\Admin\Estimated_Reading_Time_Conditional;

/**
 * Estimated reading time class.
 */
class Estimated_Reading_Time implements Integration_Interface {

	/**
	 * Returns the conditionals based in which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [ Estimated_Reading_Time_Conditional::class ];
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_filter( 'wpseo_metabox_entries_general', [ $this, 'add_estimated_reading_time_hidden_fields' ] );
	}

	/**
	 * Adds an estimated-reading-time hidden field.
	 *
	 * @param array $field_defs The $fields_defs.
	 *
	 * @return array
	 */
	public function add_estimated_reading_time_hidden_fields( $field_defs ) {
		if ( \is_array( $field_defs ) ) {
			$field_defs['estimated-reading-time-minutes'] = [
				'type'  => 'hidden',
				'title' => 'estimated-reading-time-minutes',
			];
		}

		return $field_defs;
	}
}
