<?php
/**
 * PageController
 */

namespace Automattic\WooCommerce\Admin;

use Automattic\WooCommerce\Admin\Features\Navigation\Screen;
use Automattic\WooCommerce\Internal\Admin\Loader;

defined( 'ABSPATH' ) || exit;

/**
 * PageController
 */
class PageController {
	/**
	 * App entry point.
	 */
	const APP_ENTRY_POINT = 'wc-admin';

	// JS-powered page root.
	const PAGE_ROOT = 'wc-admin';

	/**
	 * Singleton instance of self.
	 *
	 * @var PageController
	 */
	private static $instance = false;

	/**
	 * Current page ID (or false if not registered with this controller).
	 *
	 * @var string
	 */
	private $current_page = null;

	/**
	 * Registered pages
	 * Contains information (breadcrumbs, menu info) about JS powered pages and classic WooCommerce pages.
	 *
	 * @var array
	 */
	private $pages = array();

	/**
	 * We want a single instance of this class so we can accurately track registered menus and pages.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 * Hooks added here should be removed in `wc_admin_initialize` via the feature plugin.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page_handler' ) );
		add_action( 'admin_menu', array( $this, 'register_store_details_page' ) );

		// priority is 20 to run after https://github.com/woocommerce/woocommerce/blob/a55ae325306fc2179149ba9b97e66f32f84fdd9c/includes/admin/class-wc-admin-menus.php#L165.
		add_action( 'admin_head', array( $this, 'remove_app_entry_page_menu_item' ), 20 );
	}

	/**
	 * Connect an existing page to wc-admin.
	 *
	 * @param array $options {
	 *   Array describing the page.
	 *
	 *   @type string       id           Id to reference the page.
	 *   @type string|array title        Page title. Used in menus and breadcrumbs.
	 *   @type string|null  parent       Parent ID. Null for new top level page.
	 *   @type string       path         Path for this page. E.g. admin.php?page=wc-settings&tab=checkout
	 *   @type string       capability   Capability needed to access the page.
	 *   @type string       icon         Icon. Dashicons helper class, base64-encoded SVG, or 'none'.
	 *   @type int          position     Menu item position.
	 *   @type boolean      js_page      If this is a JS-powered page.
	 * }
	 */
	public function connect_page( $options ) {
		if ( ! is_array( $options['title'] ) ) {
			$options['title'] = array( $options['title'] );
		}

		/**
		 * Filter the options when connecting or registering a page.
		 *
		 * Use the `js_page` option to determine if registering.
		 *
		 * @param array $options {
		 *   Array describing the page.
		 *
		 *   @type string       id           Id to reference the page.
		 *   @type string|array title        Page title. Used in menus and breadcrumbs.
		 *   @type string|null  parent       Parent ID. Null for new top level page.
		 *   @type string       screen_id    The screen ID that represents the connected page. (Not required for registering).
		 *   @type string       path         Path for this page. E.g. admin.php?page=wc-settings&tab=checkout
		 *   @type string       capability   Capability needed to access the page.
		 *   @type string       icon         Icon. Dashicons helper class, base64-encoded SVG, or 'none'.
		 *   @type int          position     Menu item position.
		 *   @type boolean      js_page      If this is a JS-powered page.
		 * }
		 */
		$options = apply_filters( 'woocommerce_navigation_connect_page_options', $options );

		// @todo check for null ID, or collision.
		$this->pages[ $options['id'] ] = $options;
	}

	/**
	 * Determine the current page ID, if it was registered with this controller.
	 */
	public function determine_current_page() {
		$current_url       = '';
		$current_screen_id = $this->get_current_screen_id();

		if ( isset( $_SERVER['REQUEST_URI'] ) ) {
			$current_url = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
		}

		$current_query = wp_parse_url( $current_url, PHP_URL_QUERY );
		parse_str( (string) $current_query, $current_pieces );
		$current_path  = empty( $current_pieces['page'] ) ? '' : $current_pieces['page'];
		$current_path .= empty( $current_pieces['path'] ) ? '' : '&path=' . $current_pieces['path'];

		foreach ( $this->pages as $page ) {
			if ( isset( $page['js_page'] ) && $page['js_page'] ) {
				// Check registered admin pages.
				if (
					$page['path'] === $current_path
				) {
					$this->current_page = $page;
					return;
				}
			} else {
				// Check connected admin pages.
				if (
					isset( $page['screen_id'] ) &&
					$page['screen_id'] === $current_screen_id
				) {
					$this->current_page = $page;
					return;
				}
			}
		}
		$this->current_page = false;
	}


	/**
	 * Get breadcrumbs for WooCommerce Admin Page navigation.
	 *
	 * @return array Navigation pieces (breadcrumbs).
	 */
	public function get_breadcrumbs() {
		$current_page = $this->get_current_page();

		// Bail if this isn't a page registered with this controller.
		if ( false === $current_page ) {
			// Filter documentation below.
			return apply_filters( 'woocommerce_navigation_get_breadcrumbs', array( '' ), $current_page );
		}

		if ( 1 === count( $current_page['title'] ) ) {
			$breadcrumbs = $current_page['title'];
		} else {
			// If this page has multiple title pieces, only link the first one.
			$breadcrumbs = array_merge(
				array(
					array( $current_page['path'], reset( $current_page['title'] ) ),
				),
				array_slice( $current_page['title'], 1 )
			);
		}

		if ( isset( $current_page['parent'] ) ) {
			$parent_id = $current_page['parent'];

			while ( $parent_id ) {
				if ( isset( $this->pages[ $parent_id ] ) ) {
					$parent = $this->pages[ $parent_id ];

					if ( 0 === strpos( $parent['path'], self::PAGE_ROOT ) ) {
						$parent['path'] = 'admin.php?page=' . $parent['path'];
					}

					array_unshift( $breadcrumbs, array( $parent['path'], reset( $parent['title'] ) ) );
					$parent_id = isset( $parent['parent'] ) ? $parent['parent'] : false;
				} else {
					$parent_id = false;
				}
			}
		}

		$woocommerce_breadcrumb = array( 'admin.php?page=' . self::PAGE_ROOT, __( 'WooCommerce', 'woocommerce' ) );

		array_unshift( $breadcrumbs, $woocommerce_breadcrumb );

		/**
		 * The navigation breadcrumbs for the current page.
		 *
		 * @param array         $breadcrumbs Navigation pieces (breadcrumbs).
		 * @param array|boolean $current_page The connected page data or false if not identified.
		 */
		return apply_filters( 'woocommerce_navigation_get_breadcrumbs', $breadcrumbs, $current_page );
	}

	/**
	 * Get the current page.
	 *
	 * @return array|boolean Current page or false if not registered with this controller.
	 */
	public function get_current_page() {
		// If 'current_screen' hasn't fired yet, the current page calculation
		// will fail which causes `false` to be returned for all subsquent calls.
		if ( ! did_action( 'current_screen' ) ) {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Current page retrieval should be called on or after the `current_screen` hook.', 'woocommerce' ), '0.16.0' );
		}

		if ( is_null( $this->current_page ) ) {
			$this->determine_current_page();
		}

		return $this->current_page;
	}


	/**
	 * Returns the current screen ID.
	 *
	 * This is slightly different from WP's get_current_screen, in that it attaches an action,
	 * so certain pages like 'add new' pages can have different breadcrumbs or handling.
	 * It also catches some more unique dynamic pages like taxonomy/attribute management.
	 *
	 * Format:
	 * - {$current_screen->action}-{$current_screen->action}-tab-section
	 * - {$current_screen->action}-{$current_screen->action}-tab
	 * - {$current_screen->action}-{$current_screen->action} if no tab is present
	 * - {$current_screen->action} if no action or tab is present
	 *
	 * @return string Current screen ID.
	 */
	public function get_current_screen_id() {
		$current_screen = get_current_screen();
		if ( ! $current_screen ) {
			// Filter documentation below.
			return apply_filters( 'woocommerce_navigation_current_screen_id', false, $current_screen );
		}

		$screen_pieces = array( $current_screen->id );

		if ( $current_screen->action ) {
			$screen_pieces[] = $current_screen->action;
		}

		if (
			! empty( $current_screen->taxonomy ) &&
			isset( $current_screen->post_type ) &&
			'product' === $current_screen->post_type
		) {
			// Editing a product attribute.
			if ( 0 === strpos( $current_screen->taxonomy, 'pa_' ) ) {
				$screen_pieces = array( 'product_page_product_attribute-edit' );
			}

			// Editing a product taxonomy term.
			if ( ! empty( $_GET['tag_ID'] ) ) {
				$screen_pieces = array( $current_screen->taxonomy );
			}
		}

		// Pages with default tab values.
		$pages_with_tabs = apply_filters(
			'woocommerce_navigation_pages_with_tabs',
			array(
				'wc-reports'  => 'orders',
				'wc-settings' => 'general',
				'wc-status'   => 'status',
				'wc-addons'   => 'browse-extensions',
			)
		);

		// Tabs that have sections as well.
		$wc_emails    = \WC_Emails::instance();
		$wc_email_ids = array_map( 'sanitize_title', array_keys( $wc_emails->get_emails() ) );

		$tabs_with_sections = apply_filters(
			'woocommerce_navigation_page_tab_sections',
			array(
				'products'          => array( '', 'inventory', 'downloadable' ),
				'shipping'          => array( '', 'options', 'classes' ),
				'checkout'          => array( 'bacs', 'cheque', 'cod', 'paypal' ),
				'email'             => $wc_email_ids,
				'advanced'          => array(
					'',
					'keys',
					'webhooks',
					'legacy_api',
					'woocommerce_com',
				),
				'browse-extensions' => array( 'helper' ),
			)
		);

		if ( ! empty( $_GET['page'] ) ) {
			$page = wc_clean( wp_unslash( $_GET['page'] ) );
			if ( in_array( $page, array_keys( $pages_with_tabs ) ) ) {
				if ( ! empty( $_GET['tab'] ) ) {
					$tab = wc_clean( wp_unslash( $_GET['tab'] ) );
				} else {
					$tab = $pages_with_tabs[ $page ];
				}

				$screen_pieces[] = $tab;

				if ( ! empty( $_GET['section'] ) ) {
					$section = wc_clean( wp_unslash( $_GET['section'] ) );
					if (
						isset( $tabs_with_sections[ $tab ] ) &&
						in_array( $section, array_keys( $tabs_with_sections[ $tab ] ) )
					) {
						$screen_pieces[] = $section;
					}
				}

				// Editing a shipping zone.
				if ( ( 'shipping' === $tab ) && isset( $_GET['zone_id'] ) ) {
					$screen_pieces[] = 'edit_zone';
				}
			}
		}

		/**
		 * The current screen id.
		 *
		 * Used for identifying pages to render the WooCommerce Admin header.
		 *
		 * @param string|boolean $screen_id The screen id or false if not identified.
		 * @param WP_Screen      $current_screen The current WP_Screen.
		 */
		return apply_filters( 'woocommerce_navigation_current_screen_id', implode( '-', $screen_pieces ), $current_screen );
	}

	/**
	 * Returns the path from an ID.
	 *
	 * @param  string $id  ID to get path for.
	 * @return string Path for the given ID, or the ID on lookup miss.
	 */
	public function get_path_from_id( $id ) {
		if ( isset( $this->pages[ $id ] ) && isset( $this->pages[ $id ]['path'] ) ) {
			return $this->pages[ $id ]['path'];
		}
		return $id;
	}

	/**
	 * Returns true if we are on a page connected to this controller.
	 *
	 * @return boolean
	 */
	public function is_connected_page() {
		$current_page = $this->get_current_page();

		if ( false === $current_page ) {
			$is_connected_page = false;
		} else {
			$is_connected_page = isset( $current_page['js_page'] ) ? ! $current_page['js_page'] : true;
		}

		// Disable embed on the block editor.
		$current_screen = did_action( 'current_screen' ) ? get_current_screen() : false;
		if ( ! empty( $current_screen ) && method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor() ) {
			$is_connected_page = false;
		}

		/**
		 * Whether or not the current page is an existing page connected to this controller.
		 *
		 * Used to determine if the WooCommerce Admin header should be rendered.
		 *
		 * @param boolean       $is_connected_page True if the current page is connected.
		 * @param array|boolean $current_page The connected page data or false if not identified.
		 */
		return apply_filters( 'woocommerce_navigation_is_connected_page', $is_connected_page, $current_page );
	}

	/**
	 * Returns true if we are on a page registed with this controller.
	 *
	 * @return boolean
	 */
	public function is_registered_page() {
		$current_page = $this->get_current_page();

		if ( false === $current_page ) {
			$is_registered_page = false;
		} else {
			$is_registered_page = isset( $current_page['js_page'] ) && $current_page['js_page'];
		}

		/**
		 * Whether or not the current page was registered with this controller.
		 *
		 * Used to determine if this is a JS-powered WooCommerce Admin page.
		 *
		 * @param boolean       $is_registered_page True if the current page was registered with this controller.
		 * @param array|boolean $current_page The registered page data or false if not identified.
		 */
		return apply_filters( 'woocommerce_navigation_is_registered_page', $is_registered_page, $current_page );
	}

	/**
	 * Adds a JS powered page to wc-admin.
	 *
	 * @param array $options {
	 *   Array describing the page.
	 *
	 *   @type string      id           Id to reference the page.
	 *   @type string      title        Page title. Used in menus and breadcrumbs.
	 *   @type string|null parent       Parent ID. Null for new top level page.
	 *   @type string      path         Path for this page, full path in app context; ex /analytics/report
	 *   @type string      capability   Capability needed to access the page.
	 *   @type string      icon         Icon. Dashicons helper class, base64-encoded SVG, or 'none'.
	 *   @type int         position     Menu item position.
	 *   @type int         order        Navigation item order.
	 * }
	 */
	public function register_page( $options ) {
		$defaults = array(
			'id'         => null,
			'parent'     => null,
			'title'      => '',
			'capability' => 'view_woocommerce_reports',
			'path'       => '',
			'icon'       => '',
			'position'   => null,
			'js_page'    => true,
		);

		$options = wp_parse_args( $options, $defaults );

		if ( 0 !== strpos( $options['path'], self::PAGE_ROOT ) ) {
			$options['path'] = self::PAGE_ROOT . '&path=' . $options['path'];
		}

		if ( is_null( $options['parent'] ) ) {
			add_menu_page(
				$options['title'],
				$options['title'],
				$options['capability'],
				$options['path'],
				array( __CLASS__, 'page_wrapper' ),
				$options['icon'],
				intval( round( $options['position'] ) )
			);
		} else {
			$parent_path = $this->get_path_from_id( $options['parent'] );
			// @todo check for null path.
			add_submenu_page(
				$parent_path,
				$options['title'],
				$options['title'],
				$options['capability'],
				$options['path'],
				array( __CLASS__, 'page_wrapper' )
			);
		}

		$this->connect_page( $options );
	}

	/**
	 * Get registered pages.
	 *
	 * @return array
	 */
	public function get_pages() {
		return $this->pages;
	}

	/**
	 * Set up a div for the app to render into.
	 */
	public static function page_wrapper() {
		Loader::page_wrapper();
	}

	/**
	 * Connects existing WooCommerce pages.
	 *
	 * @todo The entry point for the embed needs moved to this class as well.
	 */
	public function register_page_handler() {
		require_once WC_ADMIN_ABSPATH . 'includes/react-admin/connect-existing-pages.php';
	}

	/**
	 * Registers the store details (profiler) page.
	 */
	public function register_store_details_page() {
		wc_admin_register_page(
			array(
				'title'  => __( 'Setup Wizard', 'woocommerce' ),
				'parent' => '',
				'path'   => '/setup-wizard',
			)
		);
	}

	/**
	 * Remove the menu item for the app entry point page.
	 */
	public function remove_app_entry_page_menu_item() {
		global $submenu;
		// User does not have capabilites to see the submenu.
		if ( ! current_user_can( 'manage_woocommerce' ) || empty( $submenu['woocommerce'] ) ) {
			return;
		}

		$wc_admin_key = null;
		foreach ( $submenu['woocommerce'] as $submenu_key => $submenu_item ) {
			// Our app entry page menu item has no title.
			if ( is_null( $submenu_item[0] ) && self::APP_ENTRY_POINT === $submenu_item[2] ) {
				$wc_admin_key = $submenu_key;
				break;
			}
		}

		if ( ! $wc_admin_key ) {
			return;
		}

		unset( $submenu['woocommerce'][ $wc_admin_key ] );
	}

	/**
	 * Returns true if we are on a JS powered admin page or
	 * a "classic" (non JS app) powered admin page (an embedded page).
	 */
	public static function is_admin_or_embed_page() {
		return self::is_admin_page() || self::is_embed_page();
	}

	/**
	 * Returns true if we are on a JS powered admin page.
	 */
	public static function is_admin_page() {
		// phpcs:disable WordPress.Security.NonceVerification
		return isset( $_GET['page'] ) && 'wc-admin' === $_GET['page'];
		// phpcs:enable WordPress.Security.NonceVerification
	}

	/**
	 *  Returns true if we are on a "classic" (non JS app) powered admin page.
	 *
	 * TODO: See usage in `admin.php`. This needs refactored and implemented properly in core.
	 */
	public static function is_embed_page() {
		return wc_admin_is_connected_page() || ( ! self::is_admin_page() && class_exists( 'Automattic\WooCommerce\Admin\Features\Navigation\Screen' ) && Screen::is_woocommerce_page() );
	}
}
