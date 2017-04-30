<?php
/**
 * Installation related functions and actions.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Classes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Install' ) ) :

/**
 * WC_Install Class
 */
class WC_Install {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		register_activation_hook( WC_PLUGIN_FILE, array( $this, 'install' ) );

		add_action( 'admin_init', array( $this, 'install_actions' ) );
		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
		add_action( 'in_plugin_update_message-woocommerce/woocommerce.php', array( $this, 'in_plugin_update_message' ) );
	}

	/**
	 * check_version function.
	 *
	 * @access public
	 * @return void
	 */
	public function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'woocommerce_version' ) != WC()->version || get_option( 'woocommerce_db_version' ) != WC()->version ) ) {
			$this->install();

			do_action( 'woocommerce_updated' );
		}
	}

	/**
	 * Install actions such as installing pages when a button is clicked.
	 */
	public function install_actions() {
		// Install - Add pages button
		if ( ! empty( $_GET['install_woocommerce_pages'] ) ) {

			self::create_pages();

			// We no longer need to install pages
			delete_option( '_wc_needs_pages' );
			delete_transient( '_wc_activation_redirect' );

			// What's new redirect
			wp_redirect( admin_url( 'index.php?page=wc-about&wc-installed=true' ) );
			exit;

		// Skip button
		} elseif ( ! empty( $_GET['skip_install_woocommerce_pages'] ) ) {

			// We no longer need to install pages
			delete_option( '_wc_needs_pages' );
			delete_transient( '_wc_activation_redirect' );

			// What's new redirect
			wp_redirect( admin_url( 'index.php?page=wc-about' ) );
			exit;

		// Update button
		} elseif ( ! empty( $_GET['do_update_woocommerce'] ) ) {

			$this->update();

			// Update complete
			delete_option( '_wc_needs_pages' );
			delete_option( '_wc_needs_update' );
			delete_transient( '_wc_activation_redirect' );

			// What's new redirect
			wp_redirect( admin_url( 'index.php?page=wc-about&wc-updated=true' ) );
			exit;
		}
	}

	/**
	 * Install WC
	 */
	public function install() {
		$this->create_options();
		$this->create_tables();
		$this->create_roles();

		// Register post types
		include_once( 'class-wc-post-types.php' );
		WC_Post_types::register_post_types();
		WC_Post_types::register_taxonomies();

		// Also register endpoints - this needs to be done prior to rewrite rule flush
		WC()->query->init_query_vars();
		WC()->query->add_endpoints();

		$this->create_terms();
		$this->create_cron_jobs();
		$this->create_files();
		$this->create_css_from_less();

		// Clear transient cache
		wc_delete_product_transients();

		// Queue upgrades
		$current_version = get_option( 'woocommerce_version', null );
		$current_db_version = get_option( 'woocommerce_db_version', null );

		if ( version_compare( $current_db_version, '2.1.0', '<' ) && null !== $current_db_version ) {
			update_option( '_wc_needs_update', 1 );
		} else {
			update_option( 'woocommerce_db_version', WC()->version );
		}

		// Update version
		update_option( 'woocommerce_version', WC()->version );

		// Check if pages are needed
		if ( wc_get_page_id( 'shop' ) < 1 ) {
			update_option( '_wc_needs_pages', 1 );
		}

		// Flush rules after install
		flush_rewrite_rules();

		// Redirect to welcome screen
		set_transient( '_wc_activation_redirect', 1, 60 * 60 );
	}

	/**
	 * Handle updates
	 */
	public function update() {
		// Do updates
		$current_db_version = get_option( 'woocommerce_db_version' );

		if ( version_compare( $current_db_version, '1.4', '<' ) ) {
			include( 'updates/woocommerce-update-1.4.php' );
			update_option( 'woocommerce_db_version', '1.4' );
		}

		if ( version_compare( $current_db_version, '1.5', '<' ) ) {
			include( 'updates/woocommerce-update-1.5.php' );
			update_option( 'woocommerce_db_version', '1.5' );
		}

		if ( version_compare( $current_db_version, '2.0', '<' ) ) {
			include( 'updates/woocommerce-update-2.0.php' );
			update_option( 'woocommerce_db_version', '2.0' );
		}

		if ( version_compare( $current_db_version, '2.0.9', '<' ) ) {
			include( 'updates/woocommerce-update-2.0.9.php' );
			update_option( 'woocommerce_db_version', '2.0.9' );
		}

		if ( version_compare( $current_db_version, '2.0.14', '<' ) ) {
			if ( 'HU' == get_option( 'woocommerce_default_country' ) ) {
				update_option( 'woocommerce_default_country', 'HU:BU' );
			}

			update_option( 'woocommerce_db_version', '2.0.14' );
		}

		if ( version_compare( $current_db_version, '2.1.0', '<' ) || WC_VERSION == '2.1-bleeding' ) {
			include( 'updates/woocommerce-update-2.1.php' );
			update_option( 'woocommerce_db_version', '2.1.0' );
		}

		update_option( 'woocommerce_db_version', WC()->version );
	}

	/**
	 * Create cron jobs (clear them first)
	 */
	private function create_cron_jobs() {
		// Cron jobs
		wp_clear_scheduled_hook( 'woocommerce_scheduled_sales' );
		wp_clear_scheduled_hook( 'woocommerce_cancel_unpaid_orders' );
		wp_clear_scheduled_hook( 'woocommerce_cleanup_sessions' );

		$ve = get_option( 'gmt_offset' ) > 0 ? '+' : '-';

		wp_schedule_event( strtotime( '00:00 tomorrow ' . $ve . get_option( 'gmt_offset' ) . ' HOURS' ), 'daily', 'woocommerce_scheduled_sales' );

		$held_duration = get_option( 'woocommerce_hold_stock_minutes', null );

		if ( is_null( $held_duration ) ) {
			$held_duration = '60';
		}

		if ( $held_duration != '' ) {
			wp_schedule_single_event( time() + ( absint( $held_duration ) * 60 ), 'woocommerce_cancel_unpaid_orders' );
		}

		wp_schedule_event( time(), 'twicedaily', 'woocommerce_cleanup_sessions' );
	}

	/**
	 * Create pages that the plugin relies on, storing page id's in variables.
	 *
	 * @access public
	 * @return void
	 */
	public static function create_pages() {
		$pages = apply_filters( 'woocommerce_create_pages', array(
			'shop' => array(
				'name'    => _x( 'shop', 'Page slug', 'woocommerce' ),
				'title'   => _x( 'Shop', 'Page title', 'woocommerce' ),
				'content' => ''
			),
			'cart' => array(
				'name'    => _x( 'cart', 'Page slug', 'woocommerce' ),
				'title'   => _x( 'Cart', 'Page title', 'woocommerce' ),
				'content' => '[' . apply_filters( 'woocommerce_cart_shortcode_tag', 'woocommerce_cart' ) . ']'
			),
			'checkout' => array(
				'name'    => _x( 'checkout', 'Paeg slug', 'woocommerce' ),
				'title'   => _x( 'Checkout', 'Page title', 'woocommerce' ),
				'content' => '[' . apply_filters( 'woocommerce_checkout_shortcode_tag', 'woocommerce_checkout' ) . ']'
			),
			'myaccount' => array(
				'name'    => _x( 'my-account', 'Page slug', 'woocommerce' ),
				'title'   => _x( 'My Account', 'Page title', 'woocommerce' ),
				'content' => '[' . apply_filters( 'woocommerce_my_account_shortcode_tag', 'woocommerce_my_account' ) . ']'
			)
		) );

		foreach ( $pages as $key => $page ) {
			wc_create_page( esc_sql( $page['name'] ), 'woocommerce_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? wc_get_page_id( $page['parent'] ) : '' );
		}
	}

	/**
	 * Add the default terms for WC taxonomies - product types and order statuses. Modify this at your own risk.
	 *
	 * @access public
	 * @return void
	 */
	private function create_terms() {

		$taxonomies = array(
			'product_type' => array(
				'simple',
				'grouped',
				'variable',
				'external'
			),
			'shop_order_status' => array(
				'pending',
				'failed',
				'on-hold',
				'processing',
				'completed',
				'refunded',
				'cancelled'
			)
		);

		foreach ( $taxonomies as $taxonomy => $terms ) {
			foreach ( $terms as $term ) {
				if ( ! get_term_by( 'slug', sanitize_title( $term ), $taxonomy ) ) {
					wp_insert_term( $term, $taxonomy );
				}
			}
		}
	}

	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 *
	 * @access public
	 */
	function create_options() {
		// Include settings so that we can run through defaults
		include_once( 'admin/class-wc-admin-settings.php' );

		$settings = WC_Admin_Settings::get_settings_pages();

		foreach ( $settings as $section ) {
			foreach ( $section->get_settings() as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
					add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
				}
			}

			// Special case to install the inventory settings.
			if ( $section instanceof WC_Settings_Products ) {
				foreach ( $section->get_settings( 'inventory' ) as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}
	}

	/**
	 * Set up the database tables which the plugin needs to function.
	 *
	 * Tables:
	 *		woocommerce_attribute_taxonomies - Table for storing attribute taxonomies - these are user defined
	 *		woocommerce_termmeta - Term meta table - sadly WordPress does not have termmeta so we need our own
	 *		woocommerce_downloadable_product_permissions - Table for storing user and guest download permissions.
	 *			KEY(order_id, product_id, download_id) used for organizing downloads on the My Account page
	 *		woocommerce_order_items - Order line items are stored in a table to make them easily queryable for reports
	 *		woocommerce_order_itemmeta - Order line item meta is stored in a table for storing extra data.
	 *		woocommerce_tax_rates - Tax Rates are stored inside 2 tables making tax queries simple and efficient.
	 *		woocommerce_tax_rate_locations - Each rate can be applied to more than one postcode/city hence the second table.
	 *
	 * @access public
	 * @return void
	 */
	private function create_tables() {
		global $wpdb, $woocommerce;

		$wpdb->hide_errors();

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty($wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty($wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		/**
		 * Update schemas before DBDELTA
		 *
		 * Before updating, remove any primary keys which could be modified due to schema updates
		 */
		if ( $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}woocommerce_downloadable_product_permissions';" ) ) {
			if ( ! $wpdb->get_var( "SHOW COLUMNS FROM `{$wpdb->prefix}woocommerce_downloadable_product_permissions` LIKE 'permission_id';" ) ) {
				$wpdb->query( "ALTER TABLE {$wpdb->prefix}woocommerce_downloadable_product_permissions DROP PRIMARY KEY, ADD `permission_id` bigint(20) NOT NULL PRIMARY KEY AUTO_INCREMENT;" );
			}
		}

		// WooCommerce Tables
		$woocommerce_tables = "
	CREATE TABLE {$wpdb->prefix}woocommerce_attribute_taxonomies (
	  attribute_id bigint(20) NOT NULL auto_increment,
	  attribute_name varchar(200) NOT NULL,
	  attribute_label longtext NULL,
	  attribute_type varchar(200) NOT NULL,
	  attribute_orderby varchar(200) NOT NULL,
	  PRIMARY KEY  (attribute_id),
	  KEY attribute_name (attribute_name)
	) $collate;
	CREATE TABLE {$wpdb->prefix}woocommerce_termmeta (
	  meta_id bigint(20) NOT NULL auto_increment,
	  woocommerce_term_id bigint(20) NOT NULL,
	  meta_key varchar(255) NULL,
	  meta_value longtext NULL,
	  PRIMARY KEY  (meta_id),
	  KEY woocommerce_term_id (woocommerce_term_id),
	  KEY meta_key (meta_key)
	) $collate;
	CREATE TABLE {$wpdb->prefix}woocommerce_downloadable_product_permissions (
	  permission_id bigint(20) NOT NULL auto_increment,
	  download_id varchar(32) NOT NULL,
	  product_id bigint(20) NOT NULL,
	  order_id bigint(20) NOT NULL DEFAULT 0,
	  order_key varchar(200) NOT NULL,
	  user_email varchar(200) NOT NULL,
	  user_id bigint(20) NULL,
	  downloads_remaining varchar(9) NULL,
	  access_granted datetime NOT NULL default '0000-00-00 00:00:00',
	  access_expires datetime NULL default null,
	  download_count bigint(20) NOT NULL DEFAULT 0,
	  PRIMARY KEY  (permission_id),
	  KEY download_order_key_product (product_id,order_id,order_key,download_id),
	  KEY download_order_product (download_id,order_id,product_id)
	) $collate;
	CREATE TABLE {$wpdb->prefix}woocommerce_order_items (
	  order_item_id bigint(20) NOT NULL auto_increment,
	  order_item_name longtext NOT NULL,
	  order_item_type varchar(200) NOT NULL DEFAULT '',
	  order_id bigint(20) NOT NULL,
	  PRIMARY KEY  (order_item_id),
	  KEY order_id (order_id)
	) $collate;
	CREATE TABLE {$wpdb->prefix}woocommerce_order_itemmeta (
	  meta_id bigint(20) NOT NULL auto_increment,
	  order_item_id bigint(20) NOT NULL,
	  meta_key varchar(255) NULL,
	  meta_value longtext NULL,
	  PRIMARY KEY  (meta_id),
	  KEY order_item_id (order_item_id),
	  KEY meta_key (meta_key)
	) $collate;
	CREATE TABLE {$wpdb->prefix}woocommerce_tax_rates (
	  tax_rate_id bigint(20) NOT NULL auto_increment,
	  tax_rate_country varchar(200) NOT NULL DEFAULT '',
	  tax_rate_state varchar(200) NOT NULL DEFAULT '',
	  tax_rate varchar(200) NOT NULL DEFAULT '',
	  tax_rate_name varchar(200) NOT NULL DEFAULT '',
	  tax_rate_priority bigint(20) NOT NULL,
	  tax_rate_compound int(1) NOT NULL DEFAULT 0,
	  tax_rate_shipping int(1) NOT NULL DEFAULT 1,
	  tax_rate_order bigint(20) NOT NULL,
	  tax_rate_class varchar(200) NOT NULL DEFAULT '',
	  PRIMARY KEY  (tax_rate_id),
	  KEY tax_rate_country (tax_rate_country),
	  KEY tax_rate_state (tax_rate_state),
	  KEY tax_rate_class (tax_rate_class),
	  KEY tax_rate_priority (tax_rate_priority)
	) $collate;
	CREATE TABLE {$wpdb->prefix}woocommerce_tax_rate_locations (
	  location_id bigint(20) NOT NULL auto_increment,
	  location_code varchar(255) NOT NULL,
	  tax_rate_id bigint(20) NOT NULL,
	  location_type varchar(40) NOT NULL,
	  PRIMARY KEY  (location_id),
	  KEY tax_rate_id (tax_rate_id),
	  KEY location_type (location_type),
	  KEY location_type_code (location_type,location_code)
	) $collate;
	";
		dbDelta( $woocommerce_tables );
	}

	/**
	 * Create roles and capabilities
	 */
	public function create_roles() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {

			// Customer role
			add_role( 'customer', __( 'Customer', 'woocommerce' ), array(
				'read' 						=> true,
				'edit_posts' 				=> false,
				'delete_posts' 				=> false
			) );

			// Shop manager role
			add_role( 'shop_manager', __( 'Shop Manager', 'woocommerce' ), array(
				'level_9'                => true,
				'level_8'                => true,
				'level_7'                => true,
				'level_6'                => true,
				'level_5'                => true,
				'level_4'                => true,
				'level_3'                => true,
				'level_2'                => true,
				'level_1'                => true,
				'level_0'                => true,
				'read'                   => true,
				'read_private_pages'     => true,
				'read_private_posts'     => true,
				'edit_users'             => true,
				'edit_posts'             => true,
				'edit_pages'             => true,
				'edit_published_posts'   => true,
				'edit_published_pages'   => true,
				'edit_private_pages'     => true,
				'edit_private_posts'     => true,
				'edit_others_posts'      => true,
				'edit_others_pages'      => true,
				'publish_posts'          => true,
				'publish_pages'          => true,
				'delete_posts'           => true,
				'delete_pages'           => true,
				'delete_private_pages'   => true,
				'delete_private_posts'   => true,
				'delete_published_pages' => true,
				'delete_published_posts' => true,
				'delete_others_posts'    => true,
				'delete_others_pages'    => true,
				'manage_categories'      => true,
				'manage_links'           => true,
				'moderate_comments'      => true,
				'unfiltered_html'        => true,
				'upload_files'           => true,
				'export'                 => true,
				'import'                 => true,
				'list_users'             => true
			) );

			$capabilities = $this->get_core_capabilities();

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->add_cap( 'shop_manager', $cap );
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}
		}
	}

	/**
	 * Get capabilities for WooCommerce - these are assigned to admin/shop manager during installation or reset
	 *
	 * @access public
	 * @return array
	 */
	public function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_woocommerce',
			'view_woocommerce_reports'
		);

		$capability_types = array( 'product', 'shop_order', 'shop_coupon' );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms"
			);
		}

		return $capabilities;
	}

	/**
	 * woocommerce_remove_roles function.
	 *
	 * @access public
	 * @return void
	 */
	public function remove_roles() {
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {

			$capabilities = $this->get_core_capabilities();

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->remove_cap( 'shop_manager', $cap );
					$wp_roles->remove_cap( 'administrator', $cap );
				}
			}

			remove_role( 'customer' );
			remove_role( 'shop_manager' );
		}
	}

	/**
	 * Create files/directories
	 */
	private function create_files() {
		// Install files and folders for uploading files and prevent hotlinking
		$upload_dir =  wp_upload_dir();

		$files = array(
			array(
				'base' 		=> $upload_dir['basedir'] . '/woocommerce_uploads',
				'file' 		=> '.htaccess',
				'content' 	=> 'deny from all'
			),
			array(
				'base' 		=> $upload_dir['basedir'] . '/woocommerce_uploads',
				'file' 		=> 'index.html',
				'content' 	=> ''
			),
			array(
				'base' 		=> WP_PLUGIN_DIR . "/" . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/logs',
				'file' 		=> '.htaccess',
				'content' 	=> 'deny from all'
			),
			array(
				'base' 		=> WP_PLUGIN_DIR . "/" . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/logs',
				'file' 		=> 'index.html',
				'content' 	=> ''
			)
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}

	/**
	 * Create CSS from LESS file
	 */
	private function create_css_from_less() {
		// Recompile LESS styles if they are custom
		$colors = get_option( 'woocommerce_frontend_css_colors' );

		if ( ( ! empty( $colors['primary'] ) && ! empty( $colors['secondary'] ) && ! empty( $colors['highlight'] ) && ! empty( $colors['content_bg'] ) && ! empty( $colors['subtext'] ) ) && ( $colors['primary'] != '#ad74a2' || $colors['secondary'] != '#f7f6f7' || $colors['highlight'] != '#85ad74' || $colors['content_bg'] != '#ffffff' || $colors['subtext'] != '#777777' ) ) {
			if ( ! function_exists( 'woocommerce_compile_less_styles' ) ) {
				include_once( 'admin/wc-admin-functions.php' );
			}
			woocommerce_compile_less_styles();
		}
	}

	/**
	 * Active plugins pre update option filter
	 *
	 * @param string $new_value
	 * @return string
	 */
	function pre_update_option_active_plugins( $new_value ) {
		$old_value = (array) get_option( 'active_plugins' );

		if ( $new_value !== $old_value && in_array( W3TC_FILE, (array) $new_value ) && in_array( W3TC_FILE, (array) $old_value ) ) {
			$this->_config->set( 'notes.plugins_updated', true );
			try {
				$this->_config->save();
			} catch( Exception $ex ) {}
		}

		return $new_value;
	}

	/**
	 * Show plugin changes. Code adapted from W3 Total Cache.
	 *
	 * @return void
	 */
	function in_plugin_update_message( $args ) {
		$transient_name = 'wc_upgrade_notice_' . $args['Version'];

		if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {

			$response = wp_remote_get( 'https://plugins.svn.wordpress.org/woocommerce/trunk/readme.txt' );

			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {

				// Output Upgrade Notice
				$matches        = null;
				$regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( WC_VERSION ) . '\s*=|$)~Uis';
				$upgrade_notice = '';

				if ( preg_match( $regexp, $response['body'], $matches ) ) {
					$version        = trim( $matches[1] );
					$notices        = (array) preg_split('~[\r\n]+~', trim( $matches[2] ) );
					
					if ( version_compare( WC_VERSION, $version, '<' ) ) {

						$upgrade_notice .= '<div class="wc_plugin_upgrade_notice">';

						foreach ( $notices as $index => $line ) {
							$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line ) );
						}

						$upgrade_notice .= '</div> ';
					}
				}

				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}

		echo wp_kses_post( $upgrade_notice );
	}
}

endif;

return new WC_Install();
