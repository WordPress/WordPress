<?php
/**
 * Load up extra automatic mappings for the CSV importer.
 *
 * @package WooCommerce\Admin\Importers
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require dirname( __FILE__ ) . '/default.php';
require dirname( __FILE__ ) . '/generic.php';
require dirname( __FILE__ ) . '/shopify.php';
require dirname( __FILE__ ) . '/wordpress.php';
