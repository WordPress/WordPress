<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @var array $reports
 */
foreach ( $reports as $report_name => $report ) : ?>
	<div class="elementor-system-info-section elementor-system-info-<?php echo esc_attr( $report_name ); ?>">
		<table class="widefat">
			<thead>
			<tr>
				<th><?php $report['report']->print_html_label( ( $report['label'] ) ); ?></th>
				<th></th>
				<th></th>
			</tr>
			</thead>
			<tbody>
			<?php
				$report['report']->print_html();
			?>
			</tbody>
		</table>
	</div>
	<?php
endforeach;
