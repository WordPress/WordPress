<?php

/**
 * Class ActionScheduler_wpPostStore_PostTypeRegistrar
 * @codeCoverageIgnore
 */
class ActionScheduler_wpPostStore_PostTypeRegistrar {
	public function register() {
		register_post_type( ActionScheduler_wpPostStore::POST_TYPE, $this->post_type_args() );
	}

	/**
	 * Build the args array for the post type definition
	 *
	 * @return array
	 */
	protected function post_type_args() {
		$args = array(
			'label' => __( 'Scheduled Actions', 'woocommerce' ),
			'description' => __( 'Scheduled actions are hooks triggered on a cetain date and time.', 'woocommerce' ),
			'public' => false,
			'map_meta_cap' => true,
			'hierarchical' => false,
			'supports' => array('title', 'editor','comments'),
			'rewrite' => false,
			'query_var' => false,
			'can_export' => true,
			'ep_mask' => EP_NONE,
			'labels' => array(
				'name' => __( 'Scheduled Actions', 'woocommerce' ),
				'singular_name' => __( 'Scheduled Action', 'woocommerce' ),
				'menu_name' => _x( 'Scheduled Actions', 'Admin menu name', 'woocommerce' ),
				'add_new' => __( 'Add', 'woocommerce' ),
				'add_new_item' => __( 'Add New Scheduled Action', 'woocommerce' ),
				'edit' => __( 'Edit', 'woocommerce' ),
				'edit_item' => __( 'Edit Scheduled Action', 'woocommerce' ),
				'new_item' => __( 'New Scheduled Action', 'woocommerce' ),
				'view' => __( 'View Action', 'woocommerce' ),
				'view_item' => __( 'View Action', 'woocommerce' ),
				'search_items' => __( 'Search Scheduled Actions', 'woocommerce' ),
				'not_found' => __( 'No actions found', 'woocommerce' ),
				'not_found_in_trash' => __( 'No actions found in trash', 'woocommerce' ),
			),
		);

		$args = apply_filters('action_scheduler_post_type_args', $args);
		return $args;
	}
}
 