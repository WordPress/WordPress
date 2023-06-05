<?php
/**
 * Reports Exportable Controller Interface
 */

namespace Automattic\WooCommerce\Admin\API\Reports;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce Reports exportable controller interface.
 *
 * @since 3.5.0
 */
interface ExportableInterface {

	/**
	 * Get the column names for export.
	 *
	 * @return array Key value pair of Column ID => Label.
	 */
	public function get_export_columns();

	/**
	 * Get the column values for export.
	 *
	 * @param array $item Single report item/row.
	 * @return array Key value pair of Column ID => Value.
	 */
	public function prepare_item_for_export( $item );
}
