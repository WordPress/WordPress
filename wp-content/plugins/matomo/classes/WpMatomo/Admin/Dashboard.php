<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Admin;

use Exception;
use WpMatomo\Capabilities;
use WpMatomo\Logger;
use WpMatomo\Report\Dates;
use WpMatomo\Report\Metadata;
use WpMatomo\Report\Renderer;
use WpMatomo\Uninstaller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Dashboard {
	const DASHBOARD_USER_OPTION = 'matomo_dashboard_widgets';

	public function register_hooks() {
		add_action( 'wp_dashboard_setup', [ $this, 'add_dashboard_widgets' ] );
	}

	public function add_dashboard_widgets() {
		$widgets = $this->get_widgets();
		if ( ! empty( $widgets ) && is_array( $widgets ) && current_user_can( Capabilities::KEY_VIEW ) ) {
			do_action( 'matomo_load_chartjs' );
			foreach ( $widgets as $widget ) {
				try {
					$widget_meta = $this->is_valid_widget( $widget['unique_id'], $widget['date'] );
					if ( ! empty( $widget_meta['report']['name'] ) ) {
						$id = 'matomo_dashboard_widget_' . $widget['unique_id'] . '_' . $widget['date'];

						$title = $widget_meta['report']['name'] . ' - ' . $widget_meta['date'] . ' - Matomo';

						wp_add_dashboard_widget(
							$id,
							esc_html( $title ),
							function () use ( $widget ) {
								$renderer = new Renderer();
								// do not escape the content, we want the HTML
								// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								echo $renderer->show_report(
									[
										'unique_id'   => $widget['unique_id'],
										// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'report_date' => $widget['date'],
										'limit'       => 10,
									]
								);
							}
						);
					}
				} catch ( Exception $e ) {
					// dont want to break dashboard if there is any issue with matomo ... eg in case bootstrap fails
					// or is reinstalled but matomo not yet fully installed etc
					$logger = new Logger();
					$logger->log( sprintf( 'Failed to add Matomo widget %s to dashboard: %s', wp_json_encode( $widget ), $e->getMessage() ) );
				}
			}
		}
	}

	public function get_widgets() {
		$meta = get_user_meta( get_current_user_id(), self::DASHBOARD_USER_OPTION, true );
		if ( empty( $meta ) ) {
			$meta = [];
		}

		return $meta;
	}

	public function is_valid_widget( $unique_id, $date ) {
		if ( empty( $unique_id ) || empty( $date ) ) {
			return false;
		}

		$metadata = new Metadata();
		$report   = $metadata->find_report_by_unique_id( $unique_id );

		if ( empty( $report ) ) {
			return false;
		}

		$report_dates_obj = new Dates();
		$report_dates     = $report_dates_obj->get_supported_dates();

		if ( empty( $report_dates[ $date ] ) ) {
			return false;
		}

		return [
			'report' => $report,
			'date'   => $report_dates[ $date ],
		];
	}

	public function has_widget( $report_unique_id, $report_date ) {
		$widgets = $this->get_widgets();
		foreach ( $widgets as $index => $widget ) {
			if ( $widget['unique_id'] === $report_unique_id && $widget['date'] === $report_date ) {
				return true;
			}
		}

		return false;
	}

	public function toggle_widget( $report_unique_id, $report_date ) {
		$widgets = $this->get_widgets();
		foreach ( $widgets as $index => $widget ) {
			if ( $widget['unique_id'] === $report_unique_id && $widget['date'] === $report_date ) {
				unset( $widgets[ $index ] );
				$this->set_widgets( array_values( $widgets ) );

				return;
			}
		}
		$widgets[] = [
			'unique_id' => $report_unique_id,
			'date'      => $report_date,
		];

		$this->set_widgets( $widgets );
	}

	private function set_widgets( $widgets ) {
		update_user_meta( get_current_user_id(), self::DASHBOARD_USER_OPTION, $widgets );
	}

	public function uninstall() {
		Uninstaller::uninstall_user_meta( self::DASHBOARD_USER_OPTION );
	}
}
