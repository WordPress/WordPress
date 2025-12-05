<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

/** @var array $report */
/** @var array $report_meta */
/** @var string $first_metric_name */
/** @var string $first_metric_display_name */
?>

<div class="table">
	<table class="widefat matomo-table">
		<tbody>
		<?php
		$matomo_columns = ! empty( $report['columns'] ) ? $report['columns'] : [];
		foreach ( $report['reportData']->getRows() as $matomo_val => $matomo_row ) {
			foreach ( $matomo_row as $matomo_metric_name => $matomo_value ) {
				$matomo_display_name = ! empty( $matomo_columns[ $matomo_metric_name ] ) ? $matomo_columns[ $matomo_metric_name ] : $matomo_metric_name;
				echo '<tr><td width="75%">' . esc_html( $matomo_display_name ) . '</td><td width="25%">' . esc_html( $matomo_value ) . '</td></tr>';
			}
		}
		?>
		</tbody>

	</table>
</div>
