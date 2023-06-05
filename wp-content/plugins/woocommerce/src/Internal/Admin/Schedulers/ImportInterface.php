<?php
/**
 * Import related abstract functions.
 */

namespace Automattic\WooCommerce\Internal\Admin\Schedulers;

interface ImportInterface {
	/**
	 * Get items based on query and return IDs along with total available.
	 *
	 * @internal
	 * @param int      $limit Number of records to retrieve.
	 * @param int      $page  Page number.
	 * @param int|bool $days Number of days prior to current date to limit search results.
	 * @param bool     $skip_existing Skip already imported items.
	 */
	public static function get_items( $limit, $page, $days, $skip_existing );

	/**
	 * Get total number of items already imported.
	 *
	 * @internal
	 * @return null
	 */
	public static function get_total_imported();

}
