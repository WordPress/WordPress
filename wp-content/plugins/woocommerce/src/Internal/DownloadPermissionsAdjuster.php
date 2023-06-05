<?php
/**
 * DownloadPermissionsAdjuster class file.
 */

namespace Automattic\WooCommerce\Internal;

use Automattic\WooCommerce\Proxies\LegacyProxy;
use WC_Product;

defined( 'ABSPATH' ) || exit;

/**
 * Class to adjust download permissions on product save.
 */
class DownloadPermissionsAdjuster {

	/**
	 * The downloads data store to use.
	 *
	 * @var WC_Data_Store
	 */
	private $downloads_data_store;

	/**
	 * Class initialization, to be executed when the class is resolved by the container.
	 *
	 * @internal
	 */
	final public function init() {
		$this->downloads_data_store = wc_get_container()->get( LegacyProxy::class )->get_instance_of( \WC_Data_Store::class, 'customer-download' );
		add_action( 'adjust_download_permissions', array( $this, 'adjust_download_permissions' ), 10, 1 );
	}

	/**
	 * Schedule a download permissions adjustment for a product if necessary.
	 * This should be executed whenever a product is saved.
	 *
	 * @param \WC_Product $product The product to schedule a download permission adjustments for.
	 */
	public function maybe_schedule_adjust_download_permissions( \WC_Product $product ) {
		$children_ids = $product->get_children();
		if ( ! $children_ids ) {
			return;
		}

		$are_any_children_downloadable = false;
		foreach ( $children_ids as $child_id ) {
			$child = wc_get_product( $child_id );
			if ( $child && $child->is_downloadable() ) {
				$are_any_children_downloadable = true;
				break;
			}
		}

		if ( ! $product->is_downloadable() && ! $are_any_children_downloadable ) {
			return;
		}

		$scheduled_action_args = array( $product->get_id() );

		$already_scheduled_actions =
			WC()->call_function(
				'as_get_scheduled_actions',
				array(
					'hook'   => 'adjust_download_permissions',
					'args'   => $scheduled_action_args,
					'status' => \ActionScheduler_Store::STATUS_PENDING,
				),
				'ids'
			);

		if ( empty( $already_scheduled_actions ) ) {
			WC()->call_function(
				'as_schedule_single_action',
				WC()->call_function( 'time' ) + 1,
				'adjust_download_permissions',
				$scheduled_action_args
			);
		}
	}

	/**
	 * Create additional download permissions for variations if necessary.
	 *
	 * When a simple downloadable product is converted to a variable product,
	 * existing download permissions are still present in the database but they don't apply anymore.
	 * This method creates additional download permissions for the variations based on
	 * the old existing ones for the main product.
	 *
	 * The procedure is as follows. For each existing download permission for the parent product,
	 * check if there's any variation offering the same file for download (the file URL, not name, is checked).
	 * If that is found, check if an equivalent permission exists (equivalent means for the same file and with
	 * the same order id and customer id). If no equivalent permission exists, create it.
	 *
	 * @param int $product_id The id of the product to check permissions for.
	 */
	public function adjust_download_permissions( int $product_id ) {
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}

		$children_ids = $product->get_children();
		if ( ! $children_ids ) {
			return;
		}

		$parent_downloads = $this->get_download_files_and_permissions( $product );
		if ( ! $parent_downloads ) {
			return;
		}

		$children_with_downloads = array();
		foreach ( $children_ids as $child_id ) {
			$child = wc_get_product( $child_id );

			// Ensure we have a valid child product.
			if ( ! $child instanceof WC_Product ) {
				wc_get_logger()->warning(
					sprintf(
						/* translators: 1: child product ID 2: parent product ID. */
						__( 'Unable to load child product %1$d while adjusting download permissions for product %2$d.', 'woocommerce' ),
						$child_id,
						$product_id
					)
				);
				continue;
			}

			$children_with_downloads[ $child_id ] = $this->get_download_files_and_permissions( $child );
		}

		foreach ( $parent_downloads['permission_data_by_file_order_user'] as $parent_file_order_and_user => $parent_download_data ) {
			foreach ( $children_with_downloads as $child_id => $child_download_data ) {
				$file_url = $parent_download_data['file'];

				$must_create_permission =
					// The variation offers the same file as the parent for download...
					in_array( $file_url, array_keys( $child_download_data['download_ids_by_file_url'] ), true ) &&
					// ...but no equivalent download permission (same file URL, order id and user id) exists.
					! array_key_exists( $parent_file_order_and_user, $child_download_data['permission_data_by_file_order_user'] );

				if ( $must_create_permission ) {
					// The new child download permission is a copy of the parent's,
					// but with the product and download ids changed to match those of the variation.
					$new_download_data                = $parent_download_data['data'];
					$new_download_data['product_id']  = $child_id;
					$new_download_data['download_id'] = $child_download_data['download_ids_by_file_url'][ $file_url ];
					$this->downloads_data_store->create_from_data( $new_download_data );
				}
			}
		}
	}

	/**
	 * Get the existing downloadable files and download permissions for a given product.
	 * The returned value is an array with two keys:
	 *
	 * - download_ids_by_file_url: an associative array of file url => download_id.
	 * - permission_data_by_file_order_user: an associative array where key is "file_url:customer_id:order_id" and value is the full permission data set.
	 *
	 * @param \WC_Product $product The product to get the downloadable files and permissions for.
	 * @return array[] Information about the downloadable files and permissions for the product.
	 */
	private function get_download_files_and_permissions( \WC_Product $product ) {
		$result    = array(
			'permission_data_by_file_order_user' => array(),
			'download_ids_by_file_url'           => array(),
		);
		$downloads = $product->get_downloads();
		foreach ( $downloads as $download ) {
			$result['download_ids_by_file_url'][ $download->get_file() ] = $download->get_id();
		}

		$permissions = $this->downloads_data_store->get_downloads( array( 'product_id' => $product->get_id() ) );
		foreach ( $permissions as $permission ) {
			$permission_data = (array) $permission->data;
			if ( array_key_exists( $permission_data['download_id'], $downloads ) ) {
				$file = $downloads[ $permission_data['download_id'] ]->get_file();
				$data = array(
					'file' => $file,
					'data' => (array) $permission->data,
				);
				$result['permission_data_by_file_order_user'][ "{$file}:{$permission_data['user_id']}:{$permission_data['order_id']}" ] = $data;
			}
		}

		return $result;
	}
}
