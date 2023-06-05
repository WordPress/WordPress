<?php
/**
 * Init WooCommerce data importers.
 *
 * @package WooCommerce\Admin
 */

use Automattic\Jetpack\Constants;

defined( 'ABSPATH' ) || exit;

/**
 * WC_Admin_Importers Class.
 */
class WC_Admin_Importers {

	/**
	 * Array of importer IDs.
	 *
	 * @var string[]
	 */
	protected $importers = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( ! $this->import_allowed() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'add_to_menus' ) );
		add_action( 'admin_init', array( $this, 'register_importers' ) );
		add_action( 'admin_head', array( $this, 'hide_from_menus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'wp_ajax_woocommerce_do_ajax_product_import', array( $this, 'do_ajax_product_import' ) );

		// Register WooCommerce importers.
		$this->importers['product_importer'] = array(
			'menu'       => 'edit.php?post_type=product',
			'name'       => __( 'Product Import', 'woocommerce' ),
			'capability' => 'import',
			'callback'   => array( $this, 'product_importer' ),
		);
	}

	/**
	 * Return true if WooCommerce imports are allowed for current user, false otherwise.
	 *
	 * @return bool Whether current user can perform imports.
	 */
	protected function import_allowed() {
		return current_user_can( 'edit_products' ) && current_user_can( 'import' );
	}

	/**
	 * Add menu items for our custom importers.
	 */
	public function add_to_menus() {
		foreach ( $this->importers as $id => $importer ) {
			add_submenu_page( $importer['menu'], $importer['name'], $importer['name'], $importer['capability'], $id, $importer['callback'] );
		}
	}

	/**
	 * Hide menu items from view so the pages exist, but the menu items do not.
	 */
	public function hide_from_menus() {
		global $submenu;

		foreach ( $this->importers as $id => $importer ) {
			if ( isset( $submenu[ $importer['menu'] ] ) ) {
				foreach ( $submenu[ $importer['menu'] ] as $key => $menu ) {
					if ( $id === $menu[2] ) {
						unset( $submenu[ $importer['menu'] ][ $key ] );
					}
				}
			}
		}
	}

	/**
	 * Register importer scripts.
	 */
	public function admin_scripts() {
		$suffix  = Constants::is_true( 'SCRIPT_DEBUG' ) ? '' : '.min';
		$version = Constants::get_constant( 'WC_VERSION' );
		wp_register_script( 'wc-product-import', WC()->plugin_url() . '/assets/js/admin/wc-product-import' . $suffix . '.js', array( 'jquery' ), $version, true );
	}

	/**
	 * The product importer.
	 *
	 * This has a custom screen - the Tools > Import item is a placeholder.
	 * If we're on that screen, redirect to the custom one.
	 */
	public function product_importer() {
		if ( Constants::is_defined( 'WP_LOAD_IMPORTERS' ) ) {
			wp_safe_redirect( admin_url( 'edit.php?post_type=product&page=product_importer' ) );
			exit;
		}

		include_once WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';
		include_once WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';

		$importer = new WC_Product_CSV_Importer_Controller();
		$importer->dispatch();
	}

	/**
	 * Register WordPress based importers.
	 */
	public function register_importers() {
		if ( Constants::is_defined( 'WP_LOAD_IMPORTERS' ) ) {
			add_action( 'import_start', array( $this, 'post_importer_compatibility' ) );
			register_importer( 'woocommerce_product_csv', __( 'WooCommerce products (CSV)', 'woocommerce' ), __( 'Import <strong>products</strong> to your store via a csv file.', 'woocommerce' ), array( $this, 'product_importer' ) );
			register_importer( 'woocommerce_tax_rate_csv', __( 'WooCommerce tax rates (CSV)', 'woocommerce' ), __( 'Import <strong>tax rates</strong> to your store via a csv file.', 'woocommerce' ), array( $this, 'tax_rates_importer' ) );
		}
	}

	/**
	 * The tax rate importer which extends WP_Importer.
	 */
	public function tax_rates_importer() {
		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

			if ( file_exists( $class_wp_importer ) ) {
				require $class_wp_importer;
			}
		}

		require dirname( __FILE__ ) . '/importers/class-wc-tax-rate-importer.php';

		$importer = new WC_Tax_Rate_Importer();
		$importer->dispatch();
	}

	/**
	 * When running the WP XML importer, ensure attributes exist.
	 *
	 * WordPress import should work - however, it fails to import custom product attribute taxonomies.
	 * This code grabs the file before it is imported and ensures the taxonomies are created.
	 */
	public function post_importer_compatibility() {
		global $wpdb;

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( empty( $_POST['import_id'] ) || ! class_exists( 'WXR_Parser' ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$id          = absint( $_POST['import_id'] );
		$file        = get_attached_file( $id );
		$parser      = new WXR_Parser();
		$import_data = $parser->parse( $file );

		if ( isset( $import_data['posts'] ) && ! empty( $import_data['posts'] ) ) {
			foreach ( $import_data['posts'] as $post ) {
				if ( 'product' === $post['post_type'] && ! empty( $post['terms'] ) ) {
					foreach ( $post['terms'] as $term ) {
						if ( strstr( $term['domain'], 'pa_' ) ) {
							if ( ! taxonomy_exists( $term['domain'] ) ) {
								$attribute_name = wc_attribute_taxonomy_slug( $term['domain'] );

								// Create the taxonomy.
								if ( ! in_array( $attribute_name, wc_get_attribute_taxonomies(), true ) ) {
									wc_create_attribute(
										array(
											'name'         => $attribute_name,
											'slug'         => $attribute_name,
											'type'         => 'select',
											'order_by'     => 'menu_order',
											'has_archives' => false,
										)
									);
								}

								// Register the taxonomy now so that the import works!
								register_taxonomy(
									$term['domain'],
									apply_filters( 'woocommerce_taxonomy_objects_' . $term['domain'], array( 'product' ) ),
									apply_filters(
										'woocommerce_taxonomy_args_' . $term['domain'],
										array(
											'hierarchical' => true,
											'show_ui'      => false,
											'query_var'    => true,
											'rewrite'      => false,
										)
									)
								);
							}
						}
					}
				}
			}
		}
	}

	/**
	 * Ajax callback for importing one batch of products from a CSV.
	 */
	public function do_ajax_product_import() {
		global $wpdb;

		check_ajax_referer( 'wc-product-import', 'security' );

		if ( ! $this->import_allowed() || ! isset( $_POST['file'] ) ) { // PHPCS: input var ok.
			wp_send_json_error( array( 'message' => __( 'Insufficient privileges to import products.', 'woocommerce' ) ) );
		}

		include_once WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';
		include_once WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';

		$file   = wc_clean( wp_unslash( $_POST['file'] ) ); // PHPCS: input var ok.
		$params = array(
			'delimiter'          => ! empty( $_POST['delimiter'] ) ? wc_clean( wp_unslash( $_POST['delimiter'] ) ) : ',', // PHPCS: input var ok.
			'start_pos'          => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0, // PHPCS: input var ok.
			'mapping'            => isset( $_POST['mapping'] ) ? (array) wc_clean( wp_unslash( $_POST['mapping'] ) ) : array(), // PHPCS: input var ok.
			'update_existing'    => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false, // PHPCS: input var ok.
			'character_encoding' => isset( $_POST['character_encoding'] ) ? wc_clean( wp_unslash( $_POST['character_encoding'] ) ) : '',

			/**
			 * Batch size for the product import process.
			 *
			 * @param int $size Batch size.
			 *
			 * @since
			 */
			'lines'              => apply_filters( 'woocommerce_product_import_batch_size', 30 ),
			'parse'              => true,
		);

		// Log failures.
		if ( 0 !== $params['start_pos'] ) {
			$error_log = array_filter( (array) get_user_option( 'product_import_error_log' ) );
		} else {
			$error_log = array();
		}

		$importer         = WC_Product_CSV_Importer_Controller::get_importer( $file, $params );
		$results          = $importer->import();
		$percent_complete = $importer->get_percent_complete();
		$error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );

		update_user_option( get_current_user_id(), 'product_import_error_log', $error_log );

		if ( 100 === $percent_complete ) {
			// @codingStandardsIgnoreStart.
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_original_id' ) );
			$wpdb->delete( $wpdb->posts, array(
				'post_type'   => 'product',
				'post_status' => 'importing',
			) );
			$wpdb->delete( $wpdb->posts, array(
				'post_type'   => 'product_variation',
				'post_status' => 'importing',
			) );
			// @codingStandardsIgnoreEnd.

			// Clean up orphaned data.
			$wpdb->query(
				"
				DELETE {$wpdb->posts}.* FROM {$wpdb->posts}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->posts}.post_parent
				WHERE wp.ID IS NULL AND {$wpdb->posts}.post_type = 'product_variation'
			"
			);
			$wpdb->query(
				"
				DELETE {$wpdb->postmeta}.* FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->postmeta}.post_id
				WHERE wp.ID IS NULL
			"
			);
			// @codingStandardsIgnoreStart.
			$wpdb->query( "
				DELETE tr.* FROM {$wpdb->term_relationships} tr
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
				LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE wp.ID IS NULL
				AND tt.taxonomy IN ( '" . implode( "','", array_map( 'esc_sql', get_object_taxonomies( 'product' ) ) ) . "' )
			" );
			// @codingStandardsIgnoreEnd.

			// Send success.
			wp_send_json_success(
				array(
					'position'   => 'done',
					'percentage' => 100,
					'url'        => add_query_arg( array( '_wpnonce' => wp_create_nonce( 'woocommerce-csv-importer' ) ), admin_url( 'edit.php?post_type=product&page=product_importer&step=done' ) ),
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'position'   => $importer->get_file_position(),
					'percentage' => $percent_complete,
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		}
	}
}

new WC_Admin_Importers();
