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
		<thead>
		<tr>
			<th width="75%"><?php echo esc_html( $report_meta['dimension'] ); ?></th>
			<th class="right"><?php echo esc_html( $first_metric_display_name ); ?></th>
		</tr>
		</thead>

		<tbody>
		<?php
		$matomo_report_metadata = $report['reportMetadata'];
		foreach ( $report['reportData']->getRows() as $matomo_report_id => $matomo_report_row ) {
			if ( ! empty( $matomo_report_row[ $first_metric_name ] ) ) {
				$matomo_logo_image = '';
				$matomo_meta_row   = $matomo_report_metadata->getRowFromId( $matomo_report_id );
				if ( ! empty( $matomo_meta_row ) ) {
					$matomo_logo = $matomo_meta_row->getColumn( 'logo' );
					if ( ! empty( $matomo_logo ) ) {
						$matomo_logo_image = '<img height="16" src="' . plugins_url( 'app/' . $matomo_logo, MATOMO_ANALYTICS_FILE ) . '"> ';
					}
				}
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo '<tr><td width="75%">' . $matomo_logo_image . esc_html( $matomo_report_row['label'] ) . '</td><td width="25%">' . esc_html( $matomo_report_row[ $first_metric_name ] ) . '</td></tr>';
			}
		}
		?>
		</tbody>
	</table>
</div>
