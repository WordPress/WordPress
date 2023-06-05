<?php

/**
 * Class ActionScheduler_wpPostStore_PostStatusRegistrar
 * @codeCoverageIgnore
 */
class ActionScheduler_wpPostStore_PostStatusRegistrar {
	public function register() {
		register_post_status( ActionScheduler_Store::STATUS_RUNNING, array_merge( $this->post_status_args(), $this->post_status_running_labels() ) );
		register_post_status( ActionScheduler_Store::STATUS_FAILED, array_merge( $this->post_status_args(), $this->post_status_failed_labels() ) );
	}

	/**
	 * Build the args array for the post type definition
	 *
	 * @return array
	 */
	protected function post_status_args() {
		$args = array(
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
		);

		return apply_filters( 'action_scheduler_post_status_args', $args );
	}

	/**
	 * Build the args array for the post type definition
	 *
	 * @return array
	 */
	protected function post_status_failed_labels() {
		$labels = array(
			'label'       => _x( 'Failed', 'post', 'woocommerce' ),
			/* translators: %s: count */
			'label_count' => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'woocommerce' ),
		);

		return apply_filters( 'action_scheduler_post_status_failed_labels', $labels );
	}

	/**
	 * Build the args array for the post type definition
	 *
	 * @return array
	 */
	protected function post_status_running_labels() {
		$labels = array(
			'label'       => _x( 'In-Progress', 'post', 'woocommerce' ),
			/* translators: %s: count */
			'label_count' => _n_noop( 'In-Progress <span class="count">(%s)</span>', 'In-Progress <span class="count">(%s)</span>', 'woocommerce' ),
		);

		return apply_filters( 'action_scheduler_post_status_running_labels', $labels );
	}
}
