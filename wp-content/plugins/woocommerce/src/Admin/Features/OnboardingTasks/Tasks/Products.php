<?php

namespace Automattic\WooCommerce\Admin\Features\OnboardingTasks\Tasks;

use Automattic\WooCommerce\Admin\Features\OnboardingTasks\Task;
use Automattic\WooCommerce\Internal\Admin\WCAdminAssets;

/**
 * Products Task
 */
class Products extends Task {

	/**
	 * Constructor
	 *
	 * @param TaskList $task_list Parent task list.
	 */
	public function __construct( $task_list ) {
		parent::__construct( $task_list );
		add_action( 'admin_enqueue_scripts', array( $this, 'possibly_add_manual_return_notice_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'possibly_add_import_return_notice_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'possibly_add_load_sample_return_notice_script' ) );
	}

	/**
	 * ID.
	 *
	 * @return string
	 */
	public function get_id() {
		return 'products';
	}

	/**
	 * Title.
	 *
	 * @return string
	 */
	public function get_title() {
		if ( $this->get_parent_option( 'use_completed_title' ) === true ) {
			if ( $this->is_complete() ) {
				return __( 'You added products', 'woocommerce' );
			}
			return __( 'Add products', 'woocommerce' );
		}
		return __( 'Add my products', 'woocommerce' );
	}

	/**
	 * Content.
	 *
	 * @return string
	 */
	public function get_content() {
		return __(
			'Start by adding the first product to your store. You can add your products manually, via CSV, or import them from another service.',
			'woocommerce'
		);
	}

	/**
	 * Time.
	 *
	 * @return string
	 */
	public function get_time() {
		return __( '1 minute per product', 'woocommerce' );
	}

	/**
	 * Task completion.
	 *
	 * @return bool
	 */
	public function is_complete() {
		return self::has_products();
	}

	/**
	 * Addtional data.
	 *
	 * @return array
	 */
	public function get_additional_data() {
		return array(
			'has_products' => self::has_products(),
		);
	}


	/**
	 * Adds a return to task list notice when completing the manual product task.
	 *
	 * @param string $hook Page hook.
	 */
	public function possibly_add_manual_return_notice_script( $hook ) {
		global $post;
		if ( $hook !== 'post.php' || $post->post_type !== 'product' ) {
			return;
		}

		if ( ! $this->is_active() || ! $this->is_complete() ) {
			return;
		}

		WCAdminAssets::register_script( 'wp-admin-scripts', 'onboarding-product-notice', true );

		// Clear the active task transient to only show notice once per active session.
		delete_transient( self::ACTIVE_TASK_TRANSIENT );
	}

	/**
	 * Adds a return to task list notice when completing the import product task.
	 *
	 * @param string $hook Page hook.
	 */
	public function possibly_add_import_return_notice_script( $hook ) {
		$step = isset( $_GET['step'] ) ? $_GET['step'] : ''; // phpcs:ignore csrf ok, sanitization ok.

		if ( $hook !== 'product_page_product_importer' || $step !== 'done' ) {
			return;
		}

		if ( ! $this->is_active() || $this->is_complete() ) {
			return;
		}

		WCAdminAssets::register_script( 'wp-admin-scripts', 'onboarding-product-import-notice', true );
	}

	/**
	 * Adds a return to task list notice when completing the loading sample products action.
	 *
	 * @param string $hook Page hook.
	 */
	public function possibly_add_load_sample_return_notice_script( $hook ) {
		if ( $hook !== 'edit.php' || get_query_var( 'post_type' ) !== 'product' ) {
			return;
		}

		$referer = wp_get_referer();
		if ( ! $referer || strpos( $referer, wc_admin_url() ) !== 0 ) {
			return;
		}

		if ( ! isset( $_GET[ Task::ACTIVE_TASK_TRANSIENT ] ) ) {
			return;
		}

		$task_id = sanitize_title_with_dashes( wp_unslash( $_GET[ Task::ACTIVE_TASK_TRANSIENT ] ) );
		if ( $task_id !== $this->get_id() || ! $this->is_complete() ) {
			return;
		}

		WCAdminAssets::register_script( 'wp-admin-scripts', 'onboarding-load-sample-products-notice', true );
	}

	/**
	 * Check if the store has any published products.
	 *
	 * @return bool
	 */
	public static function has_products() {
		$counts = wp_count_posts('product');
		return isset( $counts->publish ) && $counts->publish > 0;
	}
}
