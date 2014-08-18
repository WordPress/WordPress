<?php
/**
 * Setup importers for WC data.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Admin_Importers' ) ) :

/**
 * WC_Admin_Importers Class
 */
class WC_Admin_Importers {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_importers' ) );
		add_action( 'import_start', array( $this, 'post_importer_compatibility' ) );
	}

	/**
	 * Add menu items
	 */
	public function register_importers() {
		register_importer( 'woocommerce_tax_rate_csv', __( 'WooCommerce Tax Rates (CSV)', 'woocommerce' ), __( 'Import <strong>tax rates</strong> to your store via a csv file.', 'woocommerce'), array( $this, 'tax_rates_importer' ) );
	}

	/**
	 * Add menu item
	 */
	public function tax_rates_importer() {
		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';

		if ( ! class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) )
				require $class_wp_importer;
		}

		// includes
		require 'importers/class-wc-tax-rate-importer.php';

		// Dispatch
		$importer = new WC_Tax_Rate_Importer();
		$importer->dispatch();
	}

	/**
	 * When running the WP importer, ensure attributes exist.
	 *
	 * WordPress import should work - however, it fails to import custom product attribute taxonomies.
	 * This code grabs the file before it is imported and ensures the taxonomies are created.
	 *
	 * @access public
	 * @return void
	 */
	public function post_importer_compatibility() {
		global $wpdb;

		if ( empty( $_POST['import_id'] ) || ! class_exists( 'WXR_Parser' ) )
			return;

		$id          = (int) $_POST['import_id'];
		$file        = get_attached_file( $id );
		$parser      = new WXR_Parser();
		$import_data = $parser->parse( $file );

		if ( isset( $import_data['posts'] ) ) {
			$posts = $import_data['posts'];

			if ( $posts && sizeof( $posts ) > 0 ) foreach ( $posts as $post ) {

				if ( $post['post_type'] == 'product' ) {

					if ( $post['terms'] && sizeof( $post['terms'] ) > 0 ) {

						foreach ( $post['terms'] as $term ) {

							$domain = $term['domain'];

							if ( strstr( $domain, 'pa_' ) ) {

								// Make sure it exists!
								if ( ! taxonomy_exists( $domain ) ) {

									$nicename = strtolower( sanitize_title( str_replace( 'pa_', '', $domain ) ) );

									$exists_in_db = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_id FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = %s;", $nicename ) );

									// Create the taxonomy
									if ( ! $exists_in_db )
										$wpdb->insert( $wpdb->prefix . "woocommerce_attribute_taxonomies", array( 'attribute_name' => $nicename, 'attribute_type' => 'select', 'attribute_orderby' => 'menu_order' ), array( '%s', '%s', '%s' ) );

									// Register the taxonomy now so that the import works!
									register_taxonomy( $domain,
								        apply_filters( 'woocommerce_taxonomy_objects_' . $domain, array('product') ),
								        apply_filters( 'woocommerce_taxonomy_args_' . $domain, array(
								            'hierarchical' => true,
								            'show_ui' => false,
								            'query_var' => true,
								            'rewrite' => false,
								        ) )
								    );
								}
							}
						}
					}
				}
			}
		}
	}
}

endif;

return new WC_Admin_Importers();