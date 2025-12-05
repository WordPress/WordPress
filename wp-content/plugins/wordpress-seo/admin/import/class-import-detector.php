<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class WPSEO_Import_Plugins_Detector.
 *
 * Class with functionality to detect whether we should import from another SEO plugin.
 */
class WPSEO_Import_Plugins_Detector {

	/**
	 * Plugins we need to import from.
	 *
	 * @var array
	 */
	public $needs_import = [];

	/**
	 * Detects whether we need to import anything.
	 *
	 * @return void
	 */
	public function detect() {
		foreach ( WPSEO_Plugin_Importers::get() as $importer_class ) {
			$importer = new $importer_class();
			$detect   = new WPSEO_Import_Plugin( $importer, 'detect' );
			if ( $detect->status->status ) {
				$this->needs_import[ $importer_class ] = $importer->get_plugin_name();
			}
		}
	}
}
