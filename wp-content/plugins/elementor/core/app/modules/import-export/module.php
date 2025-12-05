<?php
namespace Elementor\App\Modules\ImportExport;

use Elementor\App\Modules\ImportExport\Module as Import_Export_Module;
use Elementor\Core\Base\Module as BaseModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * This App class exists for backwards compatibility with 3rd parties.
 *
 * @deprecated 3.8.0
 */
class Module extends BaseModule {

	/**
	 * @deprecated 3.8.0
	 */
	const VERSION = '1.0.0';

	/**
	 * @var mixed
	 * @deprecated 3.8.0
	 */
	public $import;

	/**
	 * @deprecated 3.8.0
	 */
	public function get_name() {
		return 'import-export-bc';
	}
}
