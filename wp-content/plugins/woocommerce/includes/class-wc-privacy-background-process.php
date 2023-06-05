<?php
/**
 * Order cleanup background process.
 *
 * @package WooCommerce\Classes
 * @version 3.4.0
 * @since   3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Background_Process', false ) ) {
	include_once dirname( __FILE__ ) . '/abstracts/class-wc-background-process.php';
}

/**
 * WC_Privacy_Background_Process class.
 */
class WC_Privacy_Background_Process extends WC_Background_Process {

	/**
	 * Initiate new background process.
	 */
	public function __construct() {
		// Uses unique prefix per blog so each blog has separate queue.
		$this->prefix = 'wp_' . get_current_blog_id();
		$this->action = 'wc_privacy_cleanup';
		parent::__construct();
	}

	/**
	 * Code to execute for each item in the queue
	 *
	 * @param string $item Queue item to iterate over.
	 * @return bool
	 */
	protected function task( $item ) {
		if ( ! $item || empty( $item['task'] ) ) {
			return false;
		}

		$process_count = 0;
		$process_limit = 20;

		switch ( $item['task'] ) {
			case 'trash_pending_orders':
				$process_count = WC_Privacy::trash_pending_orders( $process_limit );
				break;
			case 'trash_failed_orders':
				$process_count = WC_Privacy::trash_failed_orders( $process_limit );
				break;
			case 'trash_cancelled_orders':
				$process_count = WC_Privacy::trash_cancelled_orders( $process_limit );
				break;
			case 'anonymize_completed_orders':
				$process_count = WC_Privacy::anonymize_completed_orders( $process_limit );
				break;
			case 'delete_inactive_accounts':
				$process_count = WC_Privacy::delete_inactive_accounts( $process_limit );
				break;
		}

		if ( $process_limit === $process_count ) {
			// Needs to run again.
			return $item;
		}

		return false;
	}
}
