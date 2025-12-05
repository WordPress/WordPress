<?php
namespace Elementor\Core\Logger\Loggers;

use Elementor\Core\Logger\Items\Log_Item_Interface as Log_Item;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Db extends Base {

	public function save_log( Log_Item $item ) {
		$log = $this->maybe_truncate_log();

		$id = $item->get_fingerprint();

		if ( empty( $log[ $id ] ) ) {
			$log[ $id ] = $item;
		}

		$log[ $id ]->increase_times( $item );

		update_option( self::LOG_NAME, $log, 'no' );
	}

	public function clear() {
		delete_option( self::LOG_NAME );
	}

	private function maybe_truncate_log() {
		/** @var Log_Item[] $log */
		$log = $this->get_log();

		if ( Log_Item::MAX_LOG_ENTRIES < count( $log ) ) {
			$log = array_slice( $log, -Log_Item::MAX_LOG_ENTRIES );
		}

		return $log;
	}

	public function get_log() {
		// Clear cache.
		wp_cache_delete( self::LOG_NAME, 'options' );

		$log = get_option( self::LOG_NAME, [] );

		// In case the DB log is corrupted.
		if ( ! is_array( $log ) ) {
			$log = [];
		}

		return $log;
	}
}
