<?php
/**
 * @package matomo
 */
namespace WpMatomo\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // if accessed directly
}

class Chart {
	public function register_hooks() {
		add_action( 'matomo_load_chartjs', [ $this, 'load_chartjs' ] );
	}

	public function load_chartjs() {
		wp_enqueue_script( 'chart.js', plugins_url( 'node_modules/chart.js/dist/chart.min.js', MATOMO_ANALYTICS_FILE ), [], '1.0.0', true );
		wp_enqueue_script( 'matomo_chart.js', plugins_url( 'assets/chart.js', MATOMO_ANALYTICS_FILE ), [], '1.0.0', true );
	}
}
