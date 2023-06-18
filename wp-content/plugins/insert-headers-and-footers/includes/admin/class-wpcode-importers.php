<?php
/**
 * Load classes used for importing data from other plugins.
 *
 * @package WPCode
 */

/**
 * WPCode_Importers class.
 */
class WPCode_Importers {

	/**
	 * Available importers.
	 *
	 * @var WPCode_Importer_Type[]
	 */
	public $importers = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->require_files();
		$this->load_importers();
	}

	/**
	 * Require the importer classes.
	 *
	 * @return void
	 */
	private function require_files() {
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/importers/class-wpcode-importer-type.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/importers/class-wpcode-importer-code-snippets.php';
		require_once WPCODE_PLUGIN_PATH . 'includes/admin/importers/class-wpcode-importer-woody.php';
	}

	/**
	 * Load the available importers instances.
	 *
	 * @return void
	 */
	private function load_importers() {
		if ( empty( $this->importers ) ) {
			$this->importers = array(
				'code-snippets' => new WPCode_Importer_Code_Snippets(),
				'woody'         => new WPCode_Importer_Woody(),
			);
		}
	}

	/**
	 * Get the importers with registered data.
	 *
	 * @return array
	 */
	public function get_importers() {

		$importers = array();

		foreach ( $this->importers as $importer ) {
			$importers = $importer->register( $importers );
		}

		return $importers;
	}
}
