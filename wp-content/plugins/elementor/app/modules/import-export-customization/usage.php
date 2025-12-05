<?php

namespace Elementor\App\Modules\ImportExportCustomization;

use Elementor\App\Modules\ImportExportCustomization\Processes\Revert;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Usage {

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_filter( 'elementor/tracker/send_tracking_data_params', function ( array $params ) {
			$params['usages']['import_export']['revert'] = $this->get_revert_usage_data();

			return $params;
		} );
	}

	/**
	 * Get the Revert usage data.
	 *
	 * @return array
	 */
	private function get_revert_usage_data() {
		$revert_sessions = ( new Revert() )->get_revert_sessions();

		$data = [];

		foreach ( $revert_sessions as $revert_session ) {
			$data[] = [
				'kit_name' => $revert_session['kit_name'],
				'source' => $revert_session['source'],
				'revert_timestamp' => (int) $revert_session['revert_timestamp'],
				'total_time' => ( (int) $revert_session['revert_timestamp'] - (int) $revert_session['import_timestamp'] ),
			];
		}

		return $data;
	}
}
