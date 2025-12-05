<?php
namespace Elementor\Core\Logger;

use Elementor\Modules\System_Info\Reporters\Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor Log reporter.
 *
 * Elementor log reporter handler class is responsible for generating the
 * debug reports.
 *
 * @since 2.4.0
 */
class Log_Reporter extends Base {

	const MAX_ENTRIES = 20;
	const CLEAR_LOG_ACTION = 'elementor-clear-log';

	public function get_title() {
		return esc_html__( 'Log', 'elementor' );
	}

	public function get_fields() {
		return [
			'log_entries' => '',
		];
	}

	public function print_html_label( $log_label ) {
		$title = $this->get_title();

		if ( empty( $_GET[ self::CLEAR_LOG_ACTION ] ) ) { // phpcs:ignore -- nonce validation is not require here.
			$nonce = wp_create_nonce( self::CLEAR_LOG_ACTION );
			$url = add_query_arg( [
				self::CLEAR_LOG_ACTION => 1,
				'_wpnonce' => $nonce,
			] );

			$title .= '<a href="' . esc_url( $url ) . '#elementor-clear-log" class="box-title-tool">' . esc_html__( 'Clear Log', 'elementor' ) . '</a>';
			$title .= '<span id="elementor-clear-log"></span>';
		}

		parent::print_html_label( $title );
	}

	public function get_log_entries() {
		/** @var \Elementor\Core\Logger\Manager $manager */
		$manager = Manager::instance();

		/** @var \Elementor\Core\Logger\Loggers\Db $logger */
		$logger = $manager->get_logger( 'db' );

		if ( ! empty( $_GET[ self::CLEAR_LOG_ACTION ] ) ) {
			$nonce = Utils::get_super_global_value( $_GET, '_wpnonce' );

			if ( ! wp_verify_nonce( $nonce, self::CLEAR_LOG_ACTION ) ) {
				wp_die( 'Invalid Nonce', 'Invalid Nonce', [
					'back_link' => true,
				] );
			}

			$logger->clear();
		}

		$log_string = 'No entries to display';
		$log_entries = $logger->get_formatted_log_entries( self::MAX_ENTRIES, false );

		if ( ! empty( $log_entries ) ) {
			$entries_string = '';
			foreach ( $log_entries as $key => $log_entry ) {
				if ( $log_entry['count'] ) {
					$entries_string .= '<h3>' . sprintf( '%s: showing %s of %s', $key, $log_entry['count'], $log_entry['total_count'] ) . '</h3>';
					$entries_string .= '<div class="elementor-log-entries">' . $log_entry['entries'] . '</div>';
				}
			}

			if ( ! empty( $entries_string ) ) {
				$log_string = $entries_string;
			}
		}

		return [
			'value' => $log_string,
		];
	}

	public function get_raw_log_entries() {
		$log_string = 'No entries to display';

		/** @var \Elementor\Core\Logger\Manager $manager */
		$manager = Manager::instance();
		$logger = $manager->get_logger();
		$log_entries = $logger->get_formatted_log_entries( self::MAX_ENTRIES, false );

		if ( ! empty( $log_entries ) ) {
			$entries_string = PHP_EOL;
			foreach ( $log_entries as $key => $log_entry ) {
				if ( $log_entry['count'] ) {
					$entries_string .= sprintf( '%s: showing %s of %s', $key, $log_entry['count'], $log_entry['total_count'] ) . $log_entry['entries'] . PHP_EOL;
				}
			}

			if ( ! empty( $entries_string ) ) {
				$log_string = $entries_string;
			}
		}

		return [
			'value' => $log_string,
		];
	}
}
