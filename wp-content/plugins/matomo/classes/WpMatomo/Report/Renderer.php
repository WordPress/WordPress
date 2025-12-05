<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

namespace WpMatomo\Report;

use Piwik\DataTable;
use Piwik\DataTable\DataTableInterface;
use WpMatomo\Capabilities;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Renderer {
	const CUSTOM_UNIQUE_ID_VISITS_OVER_TIME = 'visits_over_time';

	public function register_hooks() {
		add_shortcode( 'matomo_report', [ $this, 'show_report' ] );
	}

	public function show_visits_over_time( $limit, $period ) {
		$cannot_view = $this->check_cannot_view();
		if ( $cannot_view ) {
			return $cannot_view;
		}

		if ( is_numeric( $limit ) ) {
			$limit = (int) $limit;
		} else {
			$limit = 14;
		}

		$report_meta = [
			'module' => 'VisitsSummary',
			'action' => 'get',
		];

		$data              = new Data();
		$report            = $data->fetch_report( $report_meta, $period, 'last' . $limit, 'label', $limit );
		$first_metric_name = 'nb_visits';
		$matomo_graph_data = ' data-chart="VisitsSumary"';
		ob_start();

		include 'views/table_map_no_dimension.php';

		return ob_get_clean();
	}

	private function check_cannot_view() {
		if ( ! current_user_can( Capabilities::KEY_VIEW ) ) {
			// not needed as processRequest checks permission anyway but it's faster this way and double ensures to not
			// letting users view it when they have no access.
			return esc_html__( 'Sorry, you are not allowed to view this report.', 'matomo' );
		}
	}

	public function show_report( $atts ) {
		$a = shortcode_atts(
			[
				'unique_id'   => '',
				'report_date' => Dates::YESTERDAY,
				'limit'       => 10,
			],
			$atts
		);

		$cannot_view = $this->check_cannot_view();
		if ( $cannot_view ) {
			return $cannot_view;
		}

		$dates                 = new Dates();
		list( $period, $date ) = $dates->detect_period_and_date( $a['report_date'] );

		if ( 'visits_over_time' === $a['unique_id'] ) {
			$is_default_limit = 10 === $a['limit'];
			if ( $is_default_limit ) {
				$a['limit'] = 14;
			}

			return $this->show_visits_over_time( $a['limit'], $period );
		}

		$metadata    = new Metadata();
		$report_meta = $metadata->find_report_by_unique_id( $a['unique_id'] );

		if ( empty( $report_meta ) ) {
			return sprintf( esc_html__( 'Report %s not found', 'matomo' ), esc_html( $a['unique_id'] ) );
		}

		$metric_keys               = array_keys( $report_meta['metrics'] );
		$first_metric_name         = reset( $metric_keys );
		$first_metric_display_name = reset( $report_meta['metrics'] );

		$report_data     = new Data();
		$report          = $report_data->fetch_report( $report_meta, $period, $date, $first_metric_name, $a['limit'] );
		$has_report_data = ! empty( $report['reportData'] ) && $this->has_rows_with_data( $report['reportData'], $first_metric_name );

		ob_start();

		if ( ! $has_report_data ) {
			include 'views/table_no_data.php';
		} elseif ( empty( $report_meta['dimension'] ) ) {
			include 'views/table_no_dimension.php';
		} else {
			include 'views/table.php';
		}

		return ob_get_clean();
	}

	private function has_rows_with_data( DataTableInterface $table, $first_metric_name ) {
		$has_data = false;
		$table->filter(
			function ( DataTable $table ) use ( &$has_data, $first_metric_name ) {
				if ( $has_data ) {
					return;
				}

				foreach ( $table->getRows() as $row ) {
					if ( ! empty( $row[ $first_metric_name ] ) ) {
						$has_data = true;
						break;
					}
				}
			}
		);
		return $has_data;
	}
}
