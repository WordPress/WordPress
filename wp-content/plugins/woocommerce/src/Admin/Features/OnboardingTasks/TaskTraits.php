<?php
/**
 * Task and TaskList Traits
 */

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks;

defined( 'ABSPATH' ) || exit;

/**
 * TaskTraits class.
 */
trait TaskTraits {
	/**
	 * Record a tracks event with the prefixed event name.
	 *
	 * @param string $event_name Event name.
	 * @param array  $args Array of tracks arguments.
	 * @return string Prefixed event name.
	 */
	public function record_tracks_event( $event_name, $args = array() ) {
		if ( ! $this->get_list_id() ) {
			return;
		}

		$prefixed_event_name = $this->prefix_event( $event_name );

		wc_admin_record_tracks_event(
			$prefixed_event_name,
			$args
		);

		return $prefixed_event_name;
	}

	/**
	 * Get the task list ID.
	 *
	 * @return string
	 */
	public function get_list_id() {
		$namespaced_class = get_class( $this );
		return is_subclass_of( $namespaced_class, 'Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task' )
			? $this->get_parent_id()
			: $this->id;
	}
}
