<?php
/**
 * Abstract class for importing data from other plugins.
 *
 * @package WPCode
 */

/**
 * Abstract class WPCode_Importer_Type.
 */
abstract class WPCode_Importer_Type {

	/**
	 * The plugin name.
	 *
	 * @var string
	 */
	public $name = '';
	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	public $slug = '';
	/**
	 * The plugin path.
	 *
	 * @var string
	 */
	public $path = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( "wp_ajax_wpcode_import_snippet_{$this->slug}", array( $this, 'import_snippet' ) );
	}

	/**
	 * Add to list of registered importers.
	 *
	 * @param array $importers List of supported importers.
	 *
	 * @return array
	 */
	public function register( $importers = array() ) {

		$importers[ $this->slug ] = array(
			'name'      => $this->name,
			'slug'      => $this->slug,
			'path'      => $this->path,
			'installed' => file_exists( trailingslashit( WP_PLUGIN_DIR ) . $this->path ),
			'active'    => $this->is_active(),
		);

		return $importers;
	}

	/**
	 * Get all the snippets for this plugin.
	 *
	 * @return array
	 */
	abstract public function get_snippets();

	/**
	 * Check if the plugin is active.
	 *
	 * @return bool
	 */
	public function is_active() {
		return is_plugin_active( $this->path );
	}

	/**
	 * After a snippet has been successfully imported we track it, so that in the
	 * future we can alert users if they try to import a snippet that has already
	 * been imported.
	 *
	 * @param int $source_id Imported plugin snippet ID.
	 * @param int $wpcode_id WPCode snippet ID.
	 */
	public function track_import( $source_id, $wpcode_id ) {

		$imported = get_option( 'wpcode_imported', array() );

		$imported[ $this->slug ][ $wpcode_id ] = $source_id;

		update_option( 'wpcode_imported', $imported, false );
	}

	/**
	 * Import a single snippet using AJAX.
	 *
	 * @return void
	 */
	abstract public function import_snippet();
}
