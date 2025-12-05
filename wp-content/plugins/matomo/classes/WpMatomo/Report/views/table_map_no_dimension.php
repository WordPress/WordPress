<?php
/**
 * Matomo - free/libre analytics platform
 *
 * @link https://matomo.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @package matomo
 */

use Piwik\DataTable\Simple;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

/** @var array $report */
/** @var array $report_meta */
/** @var string $first_metric_name */
/** @var string $matomo_graph_data */
if ( ! isset( $matomo_graph_data ) ) :
	$matomo_graph_data = '';
endif;
?>
<div class="table">
	<table class="widefat matomo-table" <?php echo esc_html( $matomo_graph_data ); ?>>

		<tbody>
		<?php
		$matomo_report_metadata = $report['reportMetadata'];
		$matomo_tables          = $report['reportData']->getDataTables();
		foreach ( array_reverse( $matomo_tables, true ) as $matomo_report_date => $matomo_report_table ) {
			/** @var Simple $matomo_report_table */
			echo '<tr><td width="75%">' . esc_html( $matomo_report_date ) . '</td><td width="25%">';
			if ( $matomo_report_table->getFirstRow() ) {
				echo esc_html( $matomo_report_table->getFirstRow()->getColumn( $first_metric_name ) );
			} else {
				echo '-';
			}
			echo '</td></tr>';
		}
		?>
		</tbody>
	</table>
</div>
