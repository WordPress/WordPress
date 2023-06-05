<?php
/**
 * Marketplace suggestions
 *
 * Behaviour for displaying in-context suggestions for marketplace extensions.
 *
 * @package WooCommerce\Classes
 * @since   3.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Marketplace suggestions core behaviour.
 */
class WC_Marketplace_Suggestions {

	/**
	 * Initialise.
	 */
	public static function init() {
		if ( ! self::allow_suggestions() ) {
			return;
		}

		// Add suggestions to the product tabs.
		add_action( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_data_tabs' ) );
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_data_panels' ) );

		// Register ajax api handlers.
		add_action( 'wp_ajax_woocommerce_add_dismissed_marketplace_suggestion', array( __CLASS__, 'post_add_dismissed_suggestion_handler' ) );

		// Register hooks for rendering suggestions container markup.
		add_action( 'wc_marketplace_suggestions_products_empty_state', array( __CLASS__, 'render_products_list_empty_state' ) );
		add_action( 'wc_marketplace_suggestions_orders_empty_state', array( __CLASS__, 'render_orders_list_empty_state' ) );
	}

	/**
	 * Product data tabs filter
	 *
	 * Adds a new Extensions tab to the product data meta box.
	 *
	 * @param array $tabs Existing tabs.
	 *
	 * @return array
	 */
	public static function product_data_tabs( $tabs ) {
		$tabs['marketplace-suggestions'] = array(
			'label'    => _x( 'Get more options', 'Marketplace suggestions', 'woocommerce' ),
			'target'   => 'marketplace_suggestions',
			'class'    => array(),
			'priority' => 1000,
		);

		return $tabs;
	}

	/**
	 * Render additional panels in the product data metabox.
	 */
	public static function product_data_panels() {
		include dirname( __FILE__ ) . '/templates/html-product-data-extensions.php';
	}

	/**
	 * Return an array of suggestions the user has dismissed.
	 */
	public static function get_dismissed_suggestions() {
		$dismissed_suggestions = array();

		$dismissed_suggestions_data = get_user_meta( get_current_user_id(), 'wc_marketplace_suggestions_dismissed_suggestions', true );
		if ( $dismissed_suggestions_data ) {
			$dismissed_suggestions = $dismissed_suggestions_data;
			if ( ! is_array( $dismissed_suggestions ) ) {
				$dismissed_suggestions = array();
			}
		}

		return $dismissed_suggestions;
	}

	/**
	 * POST handler for adding a dismissed suggestion.
	 */
	public static function post_add_dismissed_suggestion_handler() {
		if ( ! check_ajax_referer( 'add_dismissed_marketplace_suggestion' ) ) {
			wp_die();
		}

		$post_data       = wp_unslash( $_POST );
		$suggestion_slug = sanitize_text_field( $post_data['slug'] );
		if ( ! $suggestion_slug ) {
			wp_die();
		}

		$dismissed_suggestions = self::get_dismissed_suggestions();

		if ( in_array( $suggestion_slug, $dismissed_suggestions, true ) ) {
			wp_die();
		}

		$dismissed_suggestions[] = $suggestion_slug;
		update_user_meta(
			get_current_user_id(),
			'wc_marketplace_suggestions_dismissed_suggestions',
			$dismissed_suggestions
		);

		wp_die();
	}

	/**
	 * Render suggestions containers in products list empty state.
	 */
	public static function render_products_list_empty_state() {
		self::render_suggestions_container( 'products-list-empty-header' );
		self::render_suggestions_container( 'products-list-empty-body' );
		self::render_suggestions_container( 'products-list-empty-footer' );
	}

	/**
	 * Render suggestions containers in orders list empty state.
	 */
	public static function render_orders_list_empty_state() {
		self::render_suggestions_container( 'orders-list-empty-header' );
		self::render_suggestions_container( 'orders-list-empty-body' );
		self::render_suggestions_container( 'orders-list-empty-footer' );
	}

	/**
	 * Render a suggestions container element, with the specified context.
	 *
	 * @param string $context Suggestion context name (rendered as a css class).
	 */
	public static function render_suggestions_container( $context ) {
		include dirname( __FILE__ ) . '/views/container.php';
	}

	/**
	 * Should suggestions be displayed?
	 *
	 * @param string $screen_id The current admin screen.
	 *
	 * @return bool
	 */
	public static function show_suggestions_for_screen( $screen_id ) {
		// We only show suggestions on certain admin screens.
		if ( ! in_array( $screen_id, array( 'edit-product', 'edit-shop_order', 'product', wc_get_page_screen_id( 'shop-order' ) ), true ) ) {
			return false;
		}

		return self::allow_suggestions();
	}


	/**
	 * Should suggestions be displayed?
	 *
	 * @return bool
	 */
	public static function allow_suggestions() {
		// We currently only support English suggestions.
		$locale             = get_locale();
		$suggestion_locales = array(
			'en_AU',
			'en_CA',
			'en_GB',
			'en_NZ',
			'en_US',
			'en_ZA',
		);
		if ( ! in_array( $locale, $suggestion_locales, true ) ) {
			return false;
		}

		// Suggestions are only displayed if user can install plugins.
		if ( ! current_user_can( 'install_plugins' ) ) {
			return false;
		}

		// Suggestions may be disabled via a setting under Accounts & Privacy.
		if ( 'no' === get_option( 'woocommerce_show_marketplace_suggestions', 'yes' ) ) {
			return false;
		}

		// User can disabled all suggestions via filter.
		return apply_filters( 'woocommerce_allow_marketplace_suggestions', true );
	}

	/**
	 * Pull suggestion data from options. This is retrieved from a remote endpoint.
	 *
	 * @return array of json API data
	 */
	public static function get_suggestions_api_data() {
		$data = get_option( 'woocommerce_marketplace_suggestions', array() );

		// If the options have never been updated, or were updated over a week ago, queue update.
		if ( empty( $data['updated'] ) || ( time() - WEEK_IN_SECONDS ) > $data['updated'] ) {
			$next = WC()->queue()->get_next( 'woocommerce_update_marketplace_suggestions' );
			if ( ! $next ) {
				WC()->queue()->cancel_all( 'woocommerce_update_marketplace_suggestions' );
				WC()->queue()->schedule_single( time(), 'woocommerce_update_marketplace_suggestions' );
			}
		}

		return ! empty( $data['suggestions'] ) ? $data['suggestions'] : array();
	}
}

WC_Marketplace_Suggestions::init();

