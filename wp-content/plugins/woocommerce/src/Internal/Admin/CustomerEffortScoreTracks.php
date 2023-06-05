<?php
/**
 * WooCommerce Customer effort score tracks
 *
 * @package WooCommerce\Admin\Features
 */

namespace Automattic\WooCommerce\Internal\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * Triggers customer effort score on several different actions.
 */
class CustomerEffortScoreTracks {
	/**
	 * Option name for the CES Tracks queue.
	 */
	const CES_TRACKS_QUEUE_OPTION_NAME = 'woocommerce_ces_tracks_queue';

	/**
	 * Option name for the clear CES Tracks queue for page.
	 */
	const CLEAR_CES_TRACKS_QUEUE_FOR_PAGE_OPTION_NAME =
		'woocommerce_clear_ces_tracks_queue_for_page';

	/**
	 * Option name for the set of actions that have been shown.
	 */
	const SHOWN_FOR_ACTIONS_OPTION_NAME = 'woocommerce_ces_shown_for_actions';

	/**
	 * Action name for product add/publish.
	 */
	const PRODUCT_ADD_PUBLISH_ACTION_NAME = 'product_add_publish';

	/**
	 * Action name for product update.
	 */
	const PRODUCT_UPDATE_ACTION_NAME = 'product_update';

	/**
	 * Action name for shop order update.
	 */
	const SHOP_ORDER_UPDATE_ACTION_NAME = 'shop_order_update';

	/**
	 * Action name for settings change.
	 */
	const SETTINGS_CHANGE_ACTION_NAME = 'settings_change';

	/**
	 * Action name for add product categories.
	 */
	const ADD_PRODUCT_CATEGORIES_ACTION_NAME = 'add_product_categories';

	/**
	 * Action name for add product tags.
	 */
	const ADD_PRODUCT_TAGS_ACTION_NAME = 'add_product_tags';

	/*
	 * Action name for add product attributes.
	 */
	const ADD_PRODUCT_ATTRIBUTES_ACTION_NAME = 'add_product_attributes';

	/**
	 * Action name for import products.
	 */
	const IMPORT_PRODUCTS_ACTION_NAME = 'import_products';

	/**
	 * Action name for search.
	 */
	const SEARCH_ACTION_NAME = 'ces_search';

	/**
	 * Label for the snackbar that appears when a user submits the survey.
	 *
	 * @var string
	 */
	private $onsubmit_label;

	/**
	 * Constructor. Sets up filters to hook into WooCommerce.
	 */
	public function __construct() {
		$this->enable_survey_enqueing_if_tracking_is_enabled();
	}

	/**
	 * Add actions that require woocommerce_allow_tracking.
	 */
	private function enable_survey_enqueing_if_tracking_is_enabled() {
		// Only hook up the action handlers if in wp-admin.
		if ( ! is_admin() ) {
			return;
		}

		// Do not hook up the action handlers if a mobile device is used.
		if ( wp_is_mobile() ) {
			return;
		}

		// Only enqueue a survey if tracking is allowed.
		$allow_tracking = 'yes' === get_option( 'woocommerce_allow_tracking', 'no' );
		if ( ! $allow_tracking ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'maybe_clear_ces_tracks_queue' ) );
		add_action( 'woocommerce_update_options', array( $this, 'run_on_update_options' ), 10, 3 );
		add_action( 'product_cat_add_form', array( $this, 'add_script_track_product_categories' ), 10, 3 );
		add_action( 'product_tag_add_form', array( $this, 'add_script_track_product_tags' ), 10, 3 );
		add_action( 'woocommerce_attribute_added', array( $this, 'run_on_add_product_attributes' ), 10, 3 );
		add_action( 'load-edit.php', array( $this, 'run_on_load_edit_php' ), 10, 3 );
		add_action( 'product_page_product_importer', array( $this, 'run_on_product_import' ), 10, 3 );
		// Only hook up the transition_post_status action handler
		// if on the edit page.
		global $pagenow;
		if ( 'post.php' === $pagenow ) {
			add_action(
				'transition_post_status',
				array(
					$this,
					'run_on_transition_post_status',
				),
				10,
				3
			);
		}
		$this->onsubmit_label = __( 'Thank you for your feedback!', 'woocommerce' );
	}

	/**
	 * Returns a generated script for tracking tags added on edit-tags.php page.
	 * CES survey is triggered via direct access to wc/customer-effort-score store
	 * via wp.data.dispatch method.
	 *
	 * Due to lack of options to directly hook ourselves into the ajax post request
	 * initiated by edit-tags.php page, we infer a successful request by observing
	 * an increase of the number of rows in tags table
	 *
	 * @param string $action Action name for the survey.
	 * @param string $title Title for the snackbar.
	 * @param string $first_question The text for the first question.
	 * @param string $second_question The text for the second question.
	 *
	 * @return string Generated JavaScript to append to page.
	 */
	private function get_script_track_edit_php( $action, $title, $first_question, $second_question ) {
		return sprintf(
			"(function( $ ) {
				'use strict';
				// Hook on submit button and sets a 500ms interval function
				// to determine successful add tag or otherwise.
				$('#addtag #submit').on( 'click', function() {
					const initialCount = $('.tags tbody > tr').length;
					const interval = setInterval( function() {
						if ( $('.tags tbody > tr').length > initialCount ) {
							// New tag detected.
							clearInterval( interval );
							wp.data.dispatch('wc/customer-effort-score').addCesSurvey({ action: '%s', title: '%s', firstQuestion: '%s', secondQuestion: '%s', onsubmitLabel: '%s' });
						} else {
							// Form is no longer loading, most likely failed.
							if ( $( '#addtag .submit .spinner.is-active' ).length < 1 ) {
								clearInterval( interval );
							}
						}
					}, 500 );
				});
			})( jQuery );",
			esc_js( $action ),
			esc_js( $title ),
			esc_js( $first_question ),
			esc_js( $second_question ),
			esc_js( $this->onsubmit_label )
		);
	}

	/**
	 * Get the current published product count.
	 *
	 * @return integer The current published product count.
	 */
	private function get_product_count() {
		$query         = new \WC_Product_Query(
			array(
				'limit'    => 1,
				'paginate' => true,
				'return'   => 'ids',
				'status'   => array( 'publish' ),
			)
		);
		$products      = $query->get_products();
		$product_count = intval( $products->total );

		return $product_count;
	}

	/**
	 * Get the current shop order count.
	 *
	 * @return integer The current shop order count.
	 */
	private function get_shop_order_count() {
		$query            = new \WC_Order_Query(
			array(
				'limit'    => 1,
				'paginate' => true,
				'return'   => 'ids',
			)
		);
		$shop_orders      = $query->get_orders();
		$shop_order_count = intval( $shop_orders->total );

		return $shop_order_count;
	}

	/**
	 * Return whether the action has already been shown.
	 *
	 * @param string $action The action to check.
	 *
	 * @return bool Whether the action has already been shown.
	 */
	private function has_been_shown( $action ) {
		$shown_for_features = get_option( self::SHOWN_FOR_ACTIONS_OPTION_NAME, array() );
		$has_been_shown     = in_array( $action, $shown_for_features, true );

		return $has_been_shown;
	}

	/**
	 * Enqueue the item to the CES tracks queue.
	 *
	 * @param array $item The item to enqueue.
	 */
	private function enqueue_to_ces_tracks( $item ) {
		$queue = get_option(
			self::CES_TRACKS_QUEUE_OPTION_NAME,
			array()
		);

		$has_duplicate = array_filter(
			$queue,
			function ( $queue_item ) use ( $item ) {
				return $queue_item['action'] === $item['action'];
			}
		);
		if ( $has_duplicate ) {
			return;
		}

		$queue[] = $item;

		update_option(
			self::CES_TRACKS_QUEUE_OPTION_NAME,
			$queue
		);
	}

	/**
	 * Enqueue the CES survey on using search dynamically.
	 *
	 * @param string $search_area Search area such as "product" or "shop_order".
	 * @param string $page_now Value of window.pagenow.
	 * @param string $admin_page Value of window.adminpage.
	 */
	public function enqueue_ces_survey_for_search( $search_area, $page_now, $admin_page ) {
		if ( $this->has_been_shown( self::SEARCH_ACTION_NAME ) ) {
			return;
		}

		$this->enqueue_to_ces_tracks(
			array(
				'action'         => self::SEARCH_ACTION_NAME,
				'title'          => __(
					'How easy was it to use search?',
					'woocommerce'
				),
				'firstQuestion'  => __(
					'The search feature in WooCommerce is easy to use.',
					'woocommerce'
				),
				'secondQuestion' => __(
					'The search\'s functionality meets my needs.',
					'woocommerce'
				),
				'onsubmit_label' => $this->onsubmit_label,
				'pagenow'        => $page_now,
				'adminpage'      => $admin_page,
				'props'          => (object) array(
					'search_area' => $search_area,
				),
			)
		);
	}

	/**
	 * Hook into the post status lifecycle, to detect relevant user actions
	 * that we want to survey about.
	 *
	 * @param string $new_status The new status.
	 * @param string $old_status The old status.
	 * @param Post   $post The post.
	 */
	public function run_on_transition_post_status(
		$new_status,
		$old_status,
		$post
	) {
		if ( 'product' === $post->post_type ) {
			$this->maybe_enqueue_ces_survey_for_product( $new_status, $old_status );
		} elseif ( 'shop_order' === $post->post_type ) {
			$this->enqueue_ces_survey_for_edited_shop_order();
		}
	}

	/**
	 * Maybe enqueue the CES survey, if product is being added or edited.
	 *
	 * @param string $new_status The new status.
	 * @param string $old_status The old status.
	 */
	private function maybe_enqueue_ces_survey_for_product(
		$new_status,
		$old_status
	) {
		if ( 'publish' !== $new_status ) {
			return;
		}

		if ( 'publish' !== $old_status ) {
			$this->enqueue_ces_survey_for_new_product();
		} else {
			$this->enqueue_ces_survey_for_edited_product();
		}
	}

	/**
	 * Enqueue the CES survey trigger for a new product.
	 */
	private function enqueue_ces_survey_for_new_product() {
		if ( $this->has_been_shown( self::PRODUCT_ADD_PUBLISH_ACTION_NAME ) ) {
			return;
		}

		$this->enqueue_to_ces_tracks(
			array(
				'action'         => self::PRODUCT_ADD_PUBLISH_ACTION_NAME,
				'title'          => __(
					'How easy was it to add a product?',
					'woocommerce'
				),
				'firstQuestion'  => __(
					'The product creation screen is easy to use.',
					'woocommerce'
				),
				'secondQuestion' => __(
					'The product creation screen\'s functionality meets my needs.',
					'woocommerce'
				),
				'onsubmit_label' => $this->onsubmit_label,
				'pagenow'        => 'product',
				'adminpage'      => 'post-php',
				'props'          => array(
					'product_count' => $this->get_product_count(),
				),
			)
		);
	}

	/**
	 * Enqueue the CES survey trigger for an existing product.
	 */
	private function enqueue_ces_survey_for_edited_product() {
		if ( $this->has_been_shown( self::PRODUCT_UPDATE_ACTION_NAME ) ) {
			return;
		}

		$this->enqueue_to_ces_tracks(
			array(
				'action'         => self::PRODUCT_UPDATE_ACTION_NAME,
				'title'          => __(
					'How easy was it to edit your product?',
					'woocommerce'
				),
				'firstQuestion'  => __(
					'The product update process is easy to complete.',
					'woocommerce'
				),
				'secondQuestion' => __(
					'The product update process meets my needs.',
					'woocommerce'
				),
				'onsubmit_label' => $this->onsubmit_label,
				'pagenow'        => 'product',
				'adminpage'      => 'post-php',
				'props'          => array(
					'product_count' => $this->get_product_count(),
				),
			)
		);
	}

	/**
	 * Enqueue the CES survey trigger for an existing shop order.
	 */
	private function enqueue_ces_survey_for_edited_shop_order() {
		if ( $this->has_been_shown( self::SHOP_ORDER_UPDATE_ACTION_NAME ) ) {
			return;
		}

		$this->enqueue_to_ces_tracks(
			array(
				'action'         => self::SHOP_ORDER_UPDATE_ACTION_NAME,
				'title'          => __(
					'How easy was it to update an order?',
					'woocommerce'
				),
				'firstQuestion'  => __(
					'The order details screen is easy to use.',
					'woocommerce'
				),
				'secondQuestion' => __(
					'The order details screen\'s functionality meets my needs.',
					'woocommerce'
				),
				'onsubmit_label' => $this->onsubmit_label,
				'pagenow'        => 'shop_order',
				'adminpage'      => 'post-php',
				'props'          => array(
					'order_count' => $this->get_shop_order_count(),
				),
			)
		);
	}

	/**
	 * Maybe clear the CES tracks queue, executed on every page load. If the
	 * clear option is set it clears the queue. In practice, this executes a
	 * page load after the queued CES tracks are displayed on the client, which
	 * sets the clear option.
	 */
	public function maybe_clear_ces_tracks_queue() {
		$clear_ces_tracks_queue_for_page = get_option(
			self::CLEAR_CES_TRACKS_QUEUE_FOR_PAGE_OPTION_NAME,
			false
		);

		if ( ! $clear_ces_tracks_queue_for_page ) {
			return;
		}

		$queue           = get_option(
			self::CES_TRACKS_QUEUE_OPTION_NAME,
			array()
		);
		$remaining_items = array_filter(
			$queue,
			function ( $item ) use ( $clear_ces_tracks_queue_for_page ) {
				return $clear_ces_tracks_queue_for_page['pagenow'] !== $item['pagenow']
				|| $clear_ces_tracks_queue_for_page['adminpage'] !== $item['adminpage'];
			}
		);

		update_option(
			self::CES_TRACKS_QUEUE_OPTION_NAME,
			array_values( $remaining_items )
		);
		update_option( self::CLEAR_CES_TRACKS_QUEUE_FOR_PAGE_OPTION_NAME, false );
	}

	/**
	 * Appends a script to footer to trigger CES on adding product categories.
	 */
	public function add_script_track_product_categories() {
		if ( $this->has_been_shown( self::ADD_PRODUCT_CATEGORIES_ACTION_NAME ) ) {
			return;
		}

		wc_enqueue_js(
			$this->get_script_track_edit_php(
				self::ADD_PRODUCT_CATEGORIES_ACTION_NAME,
				__( 'How easy was it to add product category?', 'woocommerce' ),
				__( 'The product category details screen is easy to use.', 'woocommerce' ),
				__( "The product category details screen's functionality meets my needs.", 'woocommerce' )
			)
		);
	}

	/**
	 * Appends a script to footer to trigger CES on adding product tags.
	 */
	public function add_script_track_product_tags() {
		if ( $this->has_been_shown( self::ADD_PRODUCT_TAGS_ACTION_NAME ) ) {
			return;
		}

		wc_enqueue_js(
			$this->get_script_track_edit_php(
				self::ADD_PRODUCT_TAGS_ACTION_NAME,
				__( 'How easy was it to add a product tag?', 'woocommerce' ),
				__( 'The product tag details screen is easy to use.', 'woocommerce' ),
				__( "The product tag details screen's functionality meets my needs.", 'woocommerce' )
			)
		);
	}

	/**
	 * Maybe enqueue the CES survey on product import, if step is done.
	 */
	public function run_on_product_import() {
		// We're only interested in when the importer completes.
		if ( empty( $_GET['step'] ) || 'done' !== $_GET['step'] ) { // phpcs:ignore CSRF ok.
			return;
		}

		if ( $this->has_been_shown( self::IMPORT_PRODUCTS_ACTION_NAME ) ) {
			return;
		}

		$this->enqueue_to_ces_tracks(
			array(
				'action'         => self::IMPORT_PRODUCTS_ACTION_NAME,
				'title'          => __(
					'How easy was it to import products?',
					'woocommerce'
				),
				'firstQuestion'  => __(
					'The product import process is easy to complete.',
					'woocommerce'
				),
				'secondQuestion' => __(
					'The product import process meets my needs.',
					'woocommerce'
				),
				'onsubmit_label' => $this->onsubmit_label,
				'pagenow'        => 'product_page_product_importer',
				'adminpage'      => 'product_page_product_importer',
				'props'          => (object) array(),
			)
		);
	}

	/**
	 * Enqueue the CES survey trigger for setting changes.
	 */
	public function run_on_update_options() {
		// $current_tab is set when WC_Admin_Settings::save_settings is called.
		global $current_tab;
		global $current_section;

		if ( $this->has_been_shown( self::SETTINGS_CHANGE_ACTION_NAME ) ) {
			return;
		}

		$props = array(
			'settings_area' => $current_tab,
		);

		if ( $current_section ) {
			$props['settings_section'] = $current_section;
		}

		$this->enqueue_to_ces_tracks(
			array(
				'action'         => self::SETTINGS_CHANGE_ACTION_NAME,
				'title'          => __(
					'How easy was it to update your settings?',
					'woocommerce'
				),
				'firstQuestion'  => __(
					'The settings screen is easy to use.',
					'woocommerce'
				),
				'secondQuestion' => __(
					'The settings screen\'s functionality meets my needs.',
					'woocommerce'
				),
				'onsubmit_label' => $this->onsubmit_label,
				'pagenow'        => 'woocommerce_page_wc-settings',
				'adminpage'      => 'woocommerce_page_wc-settings',
				'props'          => (object) $props,
			)
		);
	}

	/**
	 * Enqueue the CES survey on adding new product attributes.
	 */
	public function run_on_add_product_attributes() {
		if ( $this->has_been_shown( self::ADD_PRODUCT_ATTRIBUTES_ACTION_NAME ) ) {
			return;
		}

		$this->enqueue_to_ces_tracks(
			array(
				'action'         => self::ADD_PRODUCT_ATTRIBUTES_ACTION_NAME,
				'title'          => __(
					'How easy was it to add a product attribute?',
					'woocommerce'
				),
				'firstQuestion'  => __(
					'Product attributes are easy to use.',
					'woocommerce'
				),
				'secondQuestion' => __(
					'Product attributes\' functionality meets my needs.',
					'woocommerce'
				),
				'onsubmit_label' => $this->onsubmit_label,
				'pagenow'        => 'product_page_product_attributes',
				'adminpage'      => 'product_page_product_attributes',
				'props'          => (object) array(),
			)
		);
	}

	/**
	 * Determine on initiating CES survey on searching for product or orders.
	 */
	public function run_on_load_edit_php() {
		$allowed_types = array( 'product', 'shop_order' );
		$post_type     = get_current_screen()->post_type;

		// We're only interested for certain post types.
		if ( ! in_array( $post_type, $allowed_types, true ) ) {
			return;
		}

		// Determine whether request is search by "s" GET parameter.
		if ( empty( $_GET['s'] ) ) { // phpcs:disable WordPress.Security.NonceVerification.Recommended
			return;
		}

		$page_now = 'edit-' . $post_type;
		$this->enqueue_ces_survey_for_search( $post_type, $page_now, 'edit-php' );
	}
}
