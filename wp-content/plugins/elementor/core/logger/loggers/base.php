<?php
namespace Elementor\Core\Logger\Loggers;

use Elementor\Core\Logger\Items\Base as Log_Item;
use Elementor\Core\Logger\Items\Log_Item_Interface;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

abstract class Base implements Logger_Interface {

	abstract protected function save_log( Log_Item_Interface $item );

	/**
	 * @return Log_Item_Interface[]
	 */
	abstract public function get_log();

	public function log( $item, $type = self::LEVEL_INFO, $args = [] ) {
		if ( ! $item instanceof Log_Item ) {
			$item = $this->create_item( $item, $type, $args );
		}
		$this->save_log( $item );
	}

	public function info( $message, $args = [] ) {
		$this->log( $message, self::LEVEL_INFO, $args );
	}

	public function notice( $message, $args = [] ) {
		$this->log( $message, self::LEVEL_NOTICE, $args );
	}

	public function warning( $message, $args = [] ) {
		$this->log( $message, self::LEVEL_WARNING, $args );
	}

	public function error( $message, $args = [] ) {
		$this->log( $message, self::LEVEL_ERROR, $args );
	}

	/**
	 * @param string $message
	 * @param string $type
	 * @param array  $args
	 *
	 * @return Log_Item_Interface
	 */
	private function create_item( $message, $type, $args = [] ) {
		$args['message'] = $message;
		$args['type'] = $type;

		$item = new Log_Item( $args );

		return $item;
	}

	public function get_formatted_log_entries( $max_entries, $table = true ) {
		$entries = $this->get_log();

		if ( empty( $entries ) ) {
			return [
				'All' => [
					'total_count' => 0,
					'count' => 0,
					'entries' => '',
				],
			];
		}

		$sorted_entries = [];
		$open_tag = $table ? '<tr><td>' : '';
		$close_tab = $table ? '</td></tr>' : PHP_EOL;

		$format = $table ? 'html' : 'raw';

		foreach ( $entries as $entry ) {
			/** @var Log_Item $entry */
			$sorted_entries[ $entry->get_name() ][] = $open_tag . $entry->format( $format ) . $close_tab;
		}

		$formatted_entries = [];
		foreach ( $sorted_entries as $key => $sorted_entry ) {
			$formatted_entries[ $key ]['total_count'] = count( $sorted_entry );
			$formatted_entries[ $key ]['count'] = count( $sorted_entry );
			$sorted_entry = array_slice( $sorted_entry, -$max_entries );
			$formatted_entries[ $key ]['count'] = count( $sorted_entry );
			$formatted_entries[ $key ]['entries'] = implode( $sorted_entry );
		}
		return $formatted_entries;
	}
}
