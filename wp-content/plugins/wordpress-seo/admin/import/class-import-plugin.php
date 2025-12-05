<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Import\Plugins
 */

/**
 * Class WPSEO_Import_Plugin.
 *
 * Class with functionality to import Yoast SEO settings from other plugins.
 */
class WPSEO_Import_Plugin {

	/**
	 * Holds the status of and message about imports.
	 *
	 * @var WPSEO_Import_Status
	 */
	public $status;

	/**
	 * Class with functionality to import meta data from other plugins.
	 *
	 * @var WPSEO_Plugin_Importer
	 */
	protected $importer;

	/**
	 * Import class constructor.
	 *
	 * @param WPSEO_Plugin_Importer $importer The importer that needs to perform this action.
	 * @param string                $action   The action to perform.
	 */
	public function __construct( WPSEO_Plugin_Importer $importer, $action ) {
		$this->importer = $importer;

		switch ( $action ) {
			case 'cleanup':
				$this->status = $this->importer->run_cleanup();
				break;
			case 'import':
				$this->status = $this->importer->run_import();
				break;
			case 'detect':
			default:
				$this->status = $this->importer->run_detect();
		}

		$this->status->set_msg( $this->complete_msg( $this->status->get_msg() ) );
	}

	/**
	 * Convenience function to replace %s with plugin name in import message.
	 *
	 * @param string $msg Message string.
	 *
	 * @return string Returns message with plugin name instead of replacement variables.
	 */
	protected function complete_msg( $msg ) {
		return sprintf( $msg, $this->importer->get_plugin_name() );
	}
}
