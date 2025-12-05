<?php

namespace Elementor\Modules\GlobalClasses\ImportExport;

use Elementor\App\Modules\ImportExport\Runners\Export\Export_Runner_Base;
use Elementor\Modules\GlobalClasses\Global_Classes_Repository;
use Elementor\Modules\GlobalClasses\Global_Classes_Parser;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Export_Runner extends Export_Runner_Base {
	public static function get_name(): string {
		return 'global-classes';
	}

	public function should_export( array $data ) {
		// Same as the site-settings runner.
		return (
			isset( $data['include'] ) &&
			in_array( 'settings', $data['include'], true )
		);
	}

	public function export( array $data ) {
		$kit = Plugin::$instance->kits_manager->get_active_kit();

		if ( ! $kit ) {
			return [
				'manifest' => [],
				'files' => [],
			];
		}

		$global_classes = Global_Classes_Repository::make()->all()->get();

		$global_classes_result = Global_Classes_Parser::make()->parse( $global_classes );

		if ( ! $global_classes_result->is_valid() ) {
			return [
				'manifest' => [],
				'files' => [],
			];
		}

		return [
			'files' => [
				'path' => Import_Export::FILE_NAME,
				'data' => $global_classes_result->unwrap(),
			],
		];
	}
}
