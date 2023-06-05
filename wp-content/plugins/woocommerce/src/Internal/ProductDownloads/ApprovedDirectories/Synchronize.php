<?php

namespace Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories;

use Exception;
use Automattic\WooCommerce\Internal\Utilities\URL;
use WC_Admin_Notices;
use WC_Product;
use WC_Queue_Interface;

/**
 * Ensures that any downloadable files have a corresponding entry in the Approved Product
 * Download Directories list.
 */
class Synchronize {
	/**
	 * Scheduled action hook used to facilitate scanning the product catalog for downloadable products.
	 */
	public const SYNC_TASK = 'woocommerce_download_dir_sync';

	/**
	 * The group under which synchronization tasks run (our standard 'woocommerce-db-updates' group).
	 */
	public const SYNC_TASK_GROUP = 'woocommerce-db-updates';

	/**
	 * Used to track progress throughout the sync process.
	 */
	public const SYNC_TASK_PAGE = 'wc_product_download_dir_sync_page';

	/**
	 * Used to record an estimation of progress on the current synchronization process. 0 means 0%,
	 * 100 means 100%.
	 *
	 * @param int
	 */
	public const SYNC_TASK_PROGRESS = 'wc_product_download_dir_sync_progress';

	/**
	 * Number of downloadable products to be processed in each atomic sync task.
	 */
	public const SYNC_TASK_BATCH_SIZE = 20;

	/**
	 * WC Queue.
	 *
	 * @var WC_Queue_Interface
	 */
	private $queue;

	/**
	 * Register of approved directories.
	 *
	 * @var Register
	 */
	private $register;

	/**
	 * Sets up our checks and controls for downloadable asset URLs, as appropriate for
	 * the current approved download directory mode.
	 *
	 * @internal
	 * @throws Exception If the WC_Queue instance cannot be obtained.
	 *
	 * @param Register $register The active approved download directories instance in use.
	 */
	final public function init( Register $register ) {
		$this->queue    = WC()->get_instance_of( WC_Queue_Interface::class );
		$this->register = $register;

	}

	/**
	 * Performs any work needed to add hooks and otherwise integrate with the wider system.
	 */
	final public function init_hooks() {
		add_action( self::SYNC_TASK, array( $this, 'run' ) );
	}

	/**
	 * Initializes the Approved Download Directories feature, typically following an update or
	 * during initial installation.
	 *
	 * @param bool $synchronize    Synchronize with existing product downloads. Not needed in a fresh installation.
	 * @param bool $enable_feature Enable (default) or disable the feature.
	 */
	public function init_feature( bool $synchronize = true, bool $enable_feature = true ) {
		try {
			$this->add_default_directories();

			if ( $synchronize ) {
				$this->start();
			}
		} catch ( Exception $e ) {
			wc_get_logger()->log( 'warning', __( 'It was not possible to synchronize download directories following the most recent update.', 'woocommerce' ) );
		}

		$this->register->set_mode(
			$enable_feature ? Register::MODE_ENABLED : Register::MODE_DISABLED
		);
	}

	/**
	 * By default we add the woocommerce_uploads directory (file path plus web URL) to the list
	 * of approved download directories.
	 *
	 * @throws Exception If the default directories cannot be added to the Approved List.
	 */
	public function add_default_directories() {
		$upload_dir = wp_get_upload_dir();
		$this->register->add_approved_directory( $upload_dir['basedir'] . '/woocommerce_uploads' );
		$this->register->add_approved_directory( $upload_dir['baseurl'] . '/woocommerce_uploads' );
	}

	/**
	 * Starts the synchronization process.
	 *
	 * @return bool
	 */
	public function start(): bool {
		if ( null !== $this->queue->get_next( self::SYNC_TASK ) ) {
			wc_get_logger()->log( 'warning', __( 'Synchronization of approved product download directories is already in progress.', 'woocommerce' ) );
			return false;
		}

		update_option( self::SYNC_TASK_PAGE, 1 );
		$this->queue->schedule_single( time(), self::SYNC_TASK, array(), self::SYNC_TASK_GROUP );
		wc_get_logger()->log( 'info', __( 'Approved Download Directories sync: new scan scheduled.', 'woocommerce' ) );
		return true;
	}

	/**
	 * Runs the syncronization task.
	 */
	public function run() {
		$products = $this->get_next_set_of_downloadable_products();

		foreach ( $products as $product ) {
			$this->process_product( $product );
		}

		// Detect if we have reached the end of the task.
		if ( count( $products ) < self::SYNC_TASK_BATCH_SIZE ) {
			wc_get_logger()->log( 'info', __( 'Approved Download Directories sync: scan is complete!', 'woocommerce' ) );
			$this->stop();
		} else {
			wc_get_logger()->log(
				'info',
				sprintf(
				/* translators: %1$d is the current batch in the synchronization task, %2$d is the percent complete. */
					__( 'Approved Download Directories sync: completed batch %1$d (%2$d%% complete).', 'woocommerce' ),
					(int) get_option( self::SYNC_TASK_PAGE, 2 ) - 1,
					$this->get_progress()
				)
			);
			$this->queue->schedule_single( time() + 1, self::SYNC_TASK, array(), self::SYNC_TASK_GROUP );
		}
	}

	/**
	 * Stops/cancels the current synchronization task.
	 */
	public function stop() {
		WC_Admin_Notices::add_notice( 'download_directories_sync_complete', true );
		delete_option( self::SYNC_TASK_PAGE );
		delete_option( self::SYNC_TASK_PROGRESS );
		$this->queue->cancel( self::SYNC_TASK );
	}

	/**
	 * Queries for the next batch of downloadable products, applying logic to ensure we only fetch those that actually
	 * have downloadable files (a downloadable product can be created that does not have downloadable files and/or
	 * downloadable files can be removed from existing downloadable products).
	 *
	 * @return array
	 */
	private function get_next_set_of_downloadable_products(): array {
		$query_filter = function ( array $query ): array {
			$query['meta_query'][] = array(
				'key'     => '_downloadable_files',
				'compare' => 'EXISTS',
			);

			return $query;
		};

		$page = (int) get_option( self::SYNC_TASK_PAGE, 1 );
		add_filter( 'woocommerce_product_data_store_cpt_get_products_query', $query_filter );

		$products = wc_get_products(
			array(
				'limit'    => self::SYNC_TASK_BATCH_SIZE,
				'page'     => $page,
				'paginate' => true,
			)
		);

		remove_filter( 'woocommerce_product_data_store_cpt_get_products_query', $query_filter );
		$progress = $products->max_num_pages > 0 ? (int) ( ( $page / $products->max_num_pages ) * 100 ) : 1;
		update_option( self::SYNC_TASK_PAGE, $page + 1 );
		update_option( self::SYNC_TASK_PROGRESS, $progress );

		return $products->products;
	}

	/**
	 * Processes an individual downloadable product, adding the parent paths for any downloadable files to the
	 * Approved Download Directories list.
	 *
	 * Any such paths will be added with the disabled flag set, because we want a site administrator to review
	 * and approve first.
	 *
	 * @param WC_Product $product The product we wish to examine for downloadable file paths.
	 */
	private function process_product( WC_Product $product ) {
		$downloads = $product->get_downloads();

		foreach ( $downloads as $downloadable ) {
			$parent_url = _x( 'invalid URL', 'Approved product download URLs migration', 'woocommerce' );

			try {
				$download_file = $downloadable->get_file();

				/**
				 * Controls whether shortcodes should be resolved and validated using the Approved Download Directory feature.
				 *
				 * @param bool $should_validate
				 */
				if ( apply_filters( 'woocommerce_product_downloads_approved_directory_validation_for_shortcodes', true ) && 'shortcode' === $downloadable->get_type_of_file_path() ) {
					$download_file = do_shortcode( $download_file );
				}

				$parent_url = ( new URL( $download_file ) )->get_parent_url();
				$this->register->add_approved_directory( $parent_url, false );
			} catch ( Exception $e ) {
				wc_get_logger()->log(
					'error',
					sprintf(
					/* translators: %s is a URL, %d is a product ID. */
						__( 'Product download migration: %1$s (for product %1$d) could not be added to the list of approved download directories.', 'woocommerce' ),
						$parent_url,
						$product->get_id()
					)
				);
			}
		}
	}

	/**
	 * Indicates if a synchronization of product download directories is in progress.
	 *
	 * @return bool
	 */
	public function in_progress(): bool {
		return (bool) get_option( self::SYNC_TASK_PAGE, false );
	}

	/**
	 * Returns a value between 0 and 100 representing the percentage complete of the current sync.
	 *
	 * @return int
	 */
	public function get_progress(): int {
		return min( 100, max( 0, (int) get_option( self::SYNC_TASK_PROGRESS, 0 ) ) );
	}
}
