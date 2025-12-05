<?php

namespace Elementor\App\Modules\ImportExportCustomization\Data;

use Elementor\App\Modules\ImportExportCustomization\Data\Routes\Export;
use Elementor\App\Modules\ImportExportCustomization\Data\Routes\Upload;
use Elementor\App\Modules\ImportExportCustomization\Data\Routes\Import;
use Elementor\App\Modules\ImportExportCustomization\Data\Routes\Import_Runner;
use Elementor\App\Modules\ImportExportCustomization\Data\Routes\Process_Media;
use Elementor\App\Modules\ImportExportCustomization\Data\Routes\Revert;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Controller {
	const API_NAMESPACE = 'elementor/v1';
	const API_BASE = 'import-export-customization';

	public static function register_hooks() {
		add_action( 'rest_api_init', fn() => self::register_routes() );
	}

	public static function get_base_url() {
		return get_rest_url() . self::API_NAMESPACE . '/' . self::API_BASE;
	}

	private static function register_routes() {
		( new Export() )->register_route( self::API_NAMESPACE, self::API_BASE );
		( new Upload() )->register_route( self::API_NAMESPACE, self::API_BASE );
		( new Import() )->register_route( self::API_NAMESPACE, self::API_BASE );
		( new Import_Runner() )->register_route( self::API_NAMESPACE, self::API_BASE );
		( new Process_Media() )->register_route( self::API_NAMESPACE, self::API_BASE );
		( new Revert() )->register_route( self::API_NAMESPACE, self::API_BASE );
	}
}
