<?php
/**
 * Setup menus in WP admin.
 *
 * @package WooCommerce\Admin
 * @version 2.5.0
 */

use Automattic\WooCommerce\Internal\Admin\Orders\COTRedirectionController;
use Automattic\WooCommerce\Internal\Admin\Orders\PageController as Custom_Orders_PageController;
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
use Automattic\WooCommerce\Admin\Features\Features;

defined( 'ABSPATH' ) || exit;

if ( class_exists( 'WC_Admin_Menus', false ) ) {
	return new WC_Admin_Menus();
}

/**
 * WC_Admin_Menus Class.
 */
class WC_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus.
		add_action( 'admin_menu', array( $this, 'menu_highlight' ) );
		add_action( 'admin_menu', array( $this, 'menu_order_count' ) );
		add_action( 'admin_menu', array( $this, 'maybe_add_new_product_management_experience' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'orders_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'reports_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );
		add_action( 'admin_menu', array( $this, 'status_menu' ), 60 );

		if ( apply_filters( 'woocommerce_show_addons_page', true ) ) {
			add_action( 'admin_menu', array( $this, 'addons_menu' ), 70 );
		}

		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
		add_filter( 'set-screen-option', array( $this, 'set_screen_option' ), 10, 3 );

		// Add endpoints custom URLs in Appearance > Menus > Pages.
		add_action( 'admin_head-nav-menus.php', array( $this, 'add_nav_menu_meta_boxes' ) );

		// Admin bar menus.
		if ( apply_filters( 'woocommerce_show_admin_bar_visit_store', true ) ) {
			add_action( 'admin_bar_menu', array( $this, 'admin_bar_menus' ), 31 );
		}

		// Handle saving settings earlier than load-{page} hook to avoid race conditions in conditional menus.
		add_action( 'wp_loaded', array( $this, 'save_settings' ) );
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() {
		global $menu, $admin_page_hooks;

		$woocommerce_icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDI0IDEwMjQiPjxwYXRoIGZpbGw9IiNhMmFhYjIiIGQ9Ik02MTIuMTkyIDQyNi4zMzZjMC02Ljg5Ni0zLjEzNi01MS42LTI4LTUxLjYtMzcuMzYgMC00Ni43MDQgNzIuMjU2LTQ2LjcwNCA4Mi42MjQgMCAzLjQwOCAzLjE1MiA1OC40OTYgMjguMDMyIDU4LjQ5NiAzNC4xOTItLjAzMiA0Ni42NzItNzIuMjg4IDQ2LjY3Mi04OS41MnptMjAyLjE5MiAwYzAtNi44OTYtMy4xNTItNTEuNi0yOC4wMzItNTEuNi0zNy4yOCAwLTQ2LjYwOCA3Mi4yNTYtNDYuNjA4IDgyLjYyNCAwIDMuNDA4IDMuMDcyIDU4LjQ5NiAyNy45NTIgNTguNDk2IDM0LjE5Mi0uMDMyIDQ2LjY4OC03Mi4yODggNDYuNjg4LTg5LjUyek0xNDEuMjk2Ljc2OGMtNjguMjI0IDAtMTIzLjUwNCA1NS40ODgtMTIzLjUwNCAxMjMuOTJ2NjUwLjcyYzAgNjguNDMyIDU1LjI5NiAxMjMuOTIgMTIzLjUwNCAxMjMuOTJoMzM5LjgwOGwxMjMuNTA0IDEyMy45MzZWODk5LjMyOGgyNzguMDQ4YzY4LjIyNCAwIDEyMy41Mi01NS40NzIgMTIzLjUyLTEyMy45MnYtNjUwLjcyYzAtNjguNDMyLTU1LjI5Ni0xMjMuOTItMTIzLjUyLTEyMy45MmgtNzQxLjM2em01MjYuODY0IDQyMi4xNmMwIDU1LjA4OC0zMS4wODggMTU0Ljg4LTEwMi42NCAxNTQuODgtNi4yMDggMC0xOC40OTYtMy42MTYtMjUuNDI0LTYuMDE2LTMyLjUxMi0xMS4xNjgtNTAuMTkyLTQ5LjY5Ni01Mi4zNTItNjYuMjU2IDAgMC0zLjA3Mi0xNy43OTItMy4wNzItNDAuNzUyIDAtMjIuOTkyIDMuMDcyLTQ1LjMyOCAzLjA3Mi00NS4zMjggMTUuNTUyLTc1LjcyOCA0My41NTItMTA2LjczNiA5Ni40NDgtMTA2LjczNiA1OS4wNzItLjAzMiA4My45NjggNTguNTI4IDgzLjk2OCAxMTAuMjA4ek00ODYuNDk2IDMwMi40YzAgMy4zOTItNDMuNTUyIDE0MS4xNjgtNDMuNTUyIDIxMy40MjR2NzUuNzEyYy0yLjU5MiAxMi4wOC00LjE2IDI0LjE0NC0yMS44MjQgMjQuMTQ0LTQ2LjYwOCAwLTg4Ljg4LTE1MS40NzItOTIuMDE2LTE2MS44NC02LjIwOCA2Ljg5Ni02Mi4yNCAxNjEuODQtOTYuNDQ4IDE2MS44NC0yNC44NjQgMC00My41NTItMTEzLjY0OC00Ni42MDgtMTIzLjkzNkMxNzYuNzA0IDQzNi42NzIgMTYwIDMzNC4yMjQgMTYwIDMyNy4zMjhjMC0yMC42NzIgMS4xNTItMzguNzM2IDI2LjA0OC0zOC43MzYgNi4yMDggMCAyMS42IDYuMDY0IDIzLjcxMiAxNy4xNjggMTEuNjQ4IDYyLjAzMiAxNi42ODggMTIwLjUxMiAyOS4xNjggMTg1Ljk2OCAxLjg1NiAyLjkyOCAxLjUwNCA3LjAwOCA0LjU2IDEwLjQzMiAzLjE1Mi0xMC4yODggNjYuOTI4LTE2OC43ODQgOTQuOTYtMTY4Ljc4NCAyMi41NDQgMCAzMC40IDQ0LjU5MiAzMy41MzYgNjEuODI0IDYuMjA4IDIwLjY1NiAxMy4wODggNTUuMjE2IDIyLjQxNiA4Mi43NTIgMC0xMy43NzYgMTIuNDgtMjAzLjEyIDY1LjM5Mi0yMDMuMTIgMTguNTkyLjAzMiAyNi43MDQgNi45MjggMjYuNzA0IDI3LjU2OHpNODcwLjMyIDQyMi45MjhjMCA1NS4wODgtMzEuMDg4IDE1NC44OC0xMDIuNjQgMTU0Ljg4LTYuMTkyIDAtMTguNDQ4LTMuNjE2LTI1LjQyNC02LjAxNi0zMi40MzItMTEuMTY4LTUwLjE3Ni00OS42OTYtNTIuMjg4LTY2LjI1NiAwIDAtMy44ODgtMTcuOTItMy44ODgtNDAuODk2czMuODg4LTQ1LjE4NCAzLjg4OC00NS4xODRjMTUuNTUyLTc1LjcyOCA0My40ODgtMTA2LjczNiA5Ni4zODQtMTA2LjczNiA1OS4xMDQtLjAzMiA4My45NjggNTguNTI4IDgzLjk2OCAxMTAuMjA4eiIvPjwvc3ZnPg==';

		if ( self::can_view_woocommerce_menu_item() ) {
			$menu[] = array( '', 'read', 'separator-woocommerce', '', 'wp-menu-separator woocommerce' ); // WPCS: override ok.
		}

		add_menu_page( __( 'WooCommerce', 'woocommerce' ), __( 'WooCommerce', 'woocommerce' ), 'edit_others_shop_orders', 'woocommerce', null, $woocommerce_icon, '55.5' );

		// Work around https://github.com/woocommerce/woocommerce/issues/35677 (and related https://core.trac.wordpress.org/ticket/18857).
		// Translating the menu item breaks screen IDs and page hooks, so we force the hookname to be untranslated.
		$admin_page_hooks['woocommerce'] = 'woocommerce'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		add_submenu_page( 'edit.php?post_type=product', __( 'Attributes', 'woocommerce' ), __( 'Attributes', 'woocommerce' ), 'manage_product_terms', 'product_attributes', array( $this, 'attributes_page' ) );
	}

	/**
	 * Add menu item.
	 */
	public function reports_menu() {
		if ( self::can_view_woocommerce_menu_item() ) {
			add_submenu_page( 'woocommerce', __( 'Reports', 'woocommerce' ), __( 'Reports', 'woocommerce' ), 'view_woocommerce_reports', 'wc-reports', array( $this, 'reports_page' ) );
		} else {
			add_menu_page( __( 'Sales reports', 'woocommerce' ), __( 'Sales reports', 'woocommerce' ), 'view_woocommerce_reports', 'wc-reports', array( $this, 'reports_page' ), 'dashicons-chart-bar', '55.6' );
		}
	}

	/**
	 * Add menu item.
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'woocommerce', __( 'WooCommerce settings', 'woocommerce' ), __( 'Settings', 'woocommerce' ), 'manage_woocommerce', 'wc-settings', array( $this, 'settings_page' ) );

		add_action( 'load-' . $settings_page, array( $this, 'settings_page_init' ) );
	}

	/**
	 * Check if the user can access the top-level WooCommerce item.
	 */
	public static function can_view_woocommerce_menu_item() {
		return current_user_can( 'edit_others_shop_orders' );
	}

	/**
	 * Loads gateways and shipping methods into memory for use within settings.
	 */
	public function settings_page_init() {
		WC()->payment_gateways();
		WC()->shipping();

		// Include settings pages.
		WC_Admin_Settings::get_settings_pages();

		// Add any posted messages.
		if ( ! empty( $_GET['wc_error'] ) ) { // WPCS: input var okay, CSRF ok.
			WC_Admin_Settings::add_error( wp_kses_post( wp_unslash( $_GET['wc_error'] ) ) ); // WPCS: input var okay, CSRF ok.
		}

		if ( ! empty( $_GET['wc_message'] ) ) { // WPCS: input var okay, CSRF ok.
			WC_Admin_Settings::add_message( wp_kses_post( wp_unslash( $_GET['wc_message'] ) ) ); // WPCS: input var okay, CSRF ok.
		}

		do_action( 'woocommerce_settings_page_init' );
	}

	/**
	 * Handle saving of settings.
	 *
	 * @return void
	 */
	public function save_settings() {
		global $current_tab, $current_section;

		// We should only save on the settings page.
		if ( ! is_admin() || ! isset( $_GET['page'] ) || 'wc-settings' !== $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		// Include settings pages.
		WC_Admin_Settings::get_settings_pages();

		// Get current tab/section.
		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( wp_unslash( $_GET['tab'] ) ); // WPCS: input var okay, CSRF ok.
		$current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( wp_unslash( $_REQUEST['section'] ) ); // WPCS: input var okay, CSRF ok.

		// Save settings if data has been posted.
		if ( '' !== $current_section && apply_filters( "woocommerce_save_settings_{$current_tab}_{$current_section}", ! empty( $_POST['save'] ) ) ) { // WPCS: input var okay, CSRF ok.
			WC_Admin_Settings::save();
		} elseif ( '' === $current_section && apply_filters( "woocommerce_save_settings_{$current_tab}", ! empty( $_POST['save'] ) ) ) { // WPCS: input var okay, CSRF ok.
			WC_Admin_Settings::save();
		}
	}

	/**
	 * Add menu item.
	 */
	public function status_menu() {
		add_submenu_page( 'woocommerce', __( 'WooCommerce status', 'woocommerce' ), __( 'Status', 'woocommerce' ), 'manage_woocommerce', 'wc-status', array( $this, 'status_page' ) );
	}

	/**
	 * Addons menu item.
	 */
	public function addons_menu() {
		$count_html = WC_Helper_Updater::get_updates_count_html();
		/* translators: %s: extensions count */
		$menu_title = sprintf( __( 'Extensions %s', 'woocommerce' ), $count_html );
		add_submenu_page( 'woocommerce', __( 'WooCommerce extensions', 'woocommerce' ), $menu_title, 'manage_woocommerce', 'wc-addons', array( $this, 'addons_page' ) );
	}

	/**
	 * Highlights the correct top level admin menu item for post type add screens.
	 */
	public function menu_highlight() {
		global $parent_file, $submenu_file, $post_type;

		switch ( $post_type ) {
			case 'shop_order':
			case 'shop_coupon':
				$parent_file = 'woocommerce'; // WPCS: override ok.
				break;
			case 'product':
				$screen = get_current_screen();
				if ( $screen && taxonomy_is_product_attribute( $screen->taxonomy ) ) {
					$submenu_file = 'product_attributes'; // WPCS: override ok.
					$parent_file  = 'edit.php?post_type=product'; // WPCS: override ok.
				}
				break;
		}
	}

	/**
	 * Adds the order processing count to the menu.
	 */
	public function menu_order_count() {
		global $submenu;

		if ( isset( $submenu['woocommerce'] ) ) {
			// Remove 'WooCommerce' sub menu item.
			unset( $submenu['woocommerce'][0] );

			// Add count if user has access.
			if ( apply_filters( 'woocommerce_include_processing_order_count_in_menu', true ) && current_user_can( 'edit_others_shop_orders' ) ) {
				$order_count = apply_filters( 'woocommerce_menu_order_count', wc_processing_order_count() );

				if ( $order_count ) {
					foreach ( $submenu['woocommerce'] as $key => $menu_item ) {
						if ( 0 === strpos( $menu_item[0], _x( 'Orders', 'Admin menu name', 'woocommerce' ) ) ) {
							$submenu['woocommerce'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . esc_attr( $order_count ) . '"><span class="processing-count">' . number_format_i18n( $order_count ) . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
							break;
						}
					}
				}
			}
		}
	}

	/**
	 * Reorder the WC menu items in admin.
	 *
	 * @param int $menu_order Menu order.
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array.
		$woocommerce_menu_order = array();

		// Get the index of our custom separator.
		$woocommerce_separator = array_search( 'separator-woocommerce', $menu_order, true );

		// Get index of product menu.
		$woocommerce_product = array_search( 'edit.php?post_type=product', $menu_order, true );

		// Loop through menu order and do some rearranging.
		foreach ( $menu_order as $index => $item ) {

			if ( 'woocommerce' === $item ) {
				$woocommerce_menu_order[] = 'separator-woocommerce';
				$woocommerce_menu_order[] = $item;
				$woocommerce_menu_order[] = 'edit.php?post_type=product';
				unset( $menu_order[ $woocommerce_separator ] );
				unset( $menu_order[ $woocommerce_product ] );
			} elseif ( ! in_array( $item, array( 'separator-woocommerce' ), true ) ) {
				$woocommerce_menu_order[] = $item;
			}
		}

		// Return order.
		return $woocommerce_menu_order;
	}

	/**
	 * Custom menu order.
	 *
	 * @param bool $enabled Whether custom menu ordering is already enabled.
	 * @return bool
	 */
	public function custom_menu_order( $enabled ) {
		return $enabled || self::can_view_woocommerce_menu_item();
	}

	/**
	 * Validate screen options on update.
	 *
	 * @param bool|int $status Screen option value. Default false to skip.
	 * @param string   $option The option name.
	 * @param int      $value  The number of rows to use.
	 */
	public function set_screen_option( $status, $option, $value ) {
		if ( in_array( $option, array( 'woocommerce_keys_per_page', 'woocommerce_webhooks_per_page' ), true ) ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Init the reports page.
	 */
	public function reports_page() {
		WC_Admin_Reports::output();
	}

	/**
	 * Init the settings page.
	 */
	public function settings_page() {
		WC_Admin_Settings::output();
	}

	/**
	 * Init the attributes page.
	 */
	public function attributes_page() {
		WC_Admin_Attributes::output();
	}

	/**
	 * Init the status page.
	 */
	public function status_page() {
		WC_Admin_Status::output();
	}

	/**
	 * Init the addons page.
	 */
	public function addons_page() {
		WC_Admin_Addons::output();
	}

	/**
	 * Link to the order admin list table from the main WooCommerce menu.
	 *
	 * @return void
	 */
	public function orders_menu(): void {
		if ( wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ) {
			$this->orders_page_controller = new Custom_Orders_PageController();
			$this->orders_page_controller->setup();
		} else {
			wc_get_container()->get( COTRedirectionController::class )->setup();
		}
	}

	/**
	 * Add custom nav meta box.
	 *
	 * Adapted from http://www.johnmorrisonline.com/how-to-add-a-fully-functional-custom-meta-box-to-wordpress-navigation-menus/.
	 */
	public function add_nav_menu_meta_boxes() {
		add_meta_box( 'woocommerce_endpoints_nav_link', __( 'WooCommerce endpoints', 'woocommerce' ), array( $this, 'nav_menu_links' ), 'nav-menus', 'side', 'low' );
	}

	/**
	 * Output menu links.
	 */
	public function nav_menu_links() {
		// Get items from account menu.
		$endpoints = wc_get_account_menu_items();

		// Remove dashboard item.
		if ( isset( $endpoints['dashboard'] ) ) {
			unset( $endpoints['dashboard'] );
		}

		// Include missing lost password.
		$endpoints['lost-password'] = __( 'Lost password', 'woocommerce' );

		$endpoints = apply_filters( 'woocommerce_custom_nav_menu_items', $endpoints );

		?>
		<div id="posttype-woocommerce-endpoints" class="posttypediv">
			<div id="tabs-panel-woocommerce-endpoints" class="tabs-panel tabs-panel-active">
				<ul id="woocommerce-endpoints-checklist" class="categorychecklist form-no-clear">
					<?php
					$i = -1;
					foreach ( $endpoints as $key => $value ) :
						?>
						<li>
							<label class="menu-item-title">
								<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $i ); ?>" /> <?php echo esc_html( $value ); ?>
							</label>
							<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="custom" />
							<input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="<?php echo esc_attr( $value ); ?>" />
							<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" value="<?php echo esc_url( wc_get_account_endpoint_url( $key ) ); ?>" />
							<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" />
						</li>
						<?php
						$i--;
					endforeach;
					?>
				</ul>
			</div>
			<p class="button-controls" data-items-type="posttype-woocommerce-endpoints">
				<span class="list-controls">
					<label>
						<input type="checkbox" class="select-all" />
						<?php esc_html_e( 'Select all', 'woocommerce' ); ?>
					</label>
				</span>
				<span class="add-to-menu">
					<button type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to menu', 'woocommerce' ); ?>" name="add-post-type-menu-item" id="submit-posttype-woocommerce-endpoints"><?php esc_html_e( 'Add to menu', 'woocommerce' ); ?></button>
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Add the "Visit Store" link in admin bar main menu.
	 *
	 * @since 2.4.0
	 * @param WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 */
	public function admin_bar_menus( $wp_admin_bar ) {
		if ( ! is_admin() || ! is_admin_bar_showing() ) {
			return;
		}

		// Show only when the user is a member of this site, or they're a super admin.
		if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
			return;
		}

		// Don't display when shop page is the same of the page on front.
		if ( intval( get_option( 'page_on_front' ) ) === wc_get_page_id( 'shop' ) ) {
			return;
		}

		// Add an option to visit the store.
		$wp_admin_bar->add_node(
			array(
				'parent' => 'site-name',
				'id'     => 'view-store',
				'title'  => __( 'Visit Store', 'woocommerce' ),
				'href'   => wc_get_page_permalink( 'shop' ),
			)
		);
	}

	/**
	 * Maybe add new management product experience.
	 */
	public function maybe_add_new_product_management_experience() {
		if ( Features::is_enabled( 'new-product-management-experience' ) || Features::is_enabled( 'product-block-editor' ) ) {
			global $submenu;
			if ( isset( $submenu['edit.php?post_type=product'][10] ) ) {
				// Disable phpcs since we need to override submenu classes.
				// Note that `phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited` does not work to disable this check.
				// phpcs:disable
				$submenu['edit.php?post_type=product'][10][2] = 'admin.php?page=wc-admin&path=/add-product';
				// phps:enableWordPress.Variables.GlobalVariables.OverrideProhibited
			}
		}
	}
}

return new WC_Admin_Menus();
