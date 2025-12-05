<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * @var array $reports
 * @var int   $tabs_count
 */
$tabs_count++;

foreach ( $reports as $report_name => $report ) :
	$report['report']->print_raw( $tabs_count );

	if ( ! empty( $report['sub'] ) ) :
		$this->print_report( $report['sub'], $template, true );
	endif;
endforeach;

$tabs_count--;
