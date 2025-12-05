<?php

namespace Elementor\App\Modules\ImportExport\Processes;

use Elementor\App\Modules\ImportExport\Module;
use Elementor\App\Modules\ImportExport\Utils;
use Elementor\Core\Utils\Str;
use Elementor\Plugin;

use Elementor\App\Modules\ImportExport\Runners\Export\Elementor_Content;
use Elementor\App\Modules\ImportExport\Runners\Export\Export_Runner_Base;
use Elementor\App\Modules\ImportExport\Runners\Export\Plugins;
use Elementor\App\Modules\ImportExport\Runners\Export\Site_Settings;
use Elementor\App\Modules\ImportExport\Runners\Export\Taxonomies;
use Elementor\App\Modules\ImportExport\Runners\Export\Templates;
use Elementor\App\Modules\ImportExport\Runners\Export\Wp_Content;

class Export {
	const ZIP_ARCHIVE_MODULE_MISSING = 'zip-archive-module-is-missing';

	/**
	 * @var Export_Runner_Base[]
	 */
	protected $runners = [];

	/**
	 * Selected content types to export.
	 *
	 * @var array
	 */
	private $settings_include;

	/**
	 * The kit information. (e.g: title, description)
	 *
	 * @var array $export_data
	 */
	private $settings_kit_info;

	/**
	 * Selected plugins to export.
	 * Contains the plugins essential data for export. (e.g: name, path, version, etc.)
	 *
	 * @var array
	 */
	private $settings_selected_plugins;

	/**
	 * Selected custom post types to export.
	 *
	 * @var array
	 */
	private $settings_selected_custom_post_types;

	/**
	 * The output data of the export process.
	 * Will be written into the manifest.json file.
	 *
	 * @var array
	 */
	private $manifest_data;

	/**
	 * The zip archive object.
	 *
	 * @var \ZipArchive
	 */
	private $zip;

	public function __construct( $settings = [] ) {
		$this->settings_include = ! empty( $settings['include'] ) ? $settings['include'] : null;
		$this->settings_kit_info = ! empty( $settings['kitInfo'] ) ? $settings['kitInfo'] : null;
		$this->settings_selected_plugins = isset( $settings['plugins'] ) ? $settings['plugins'] : null;
		$this->settings_selected_custom_post_types = isset( $settings['selectedCustomPostTypes'] ) ? $settings['selectedCustomPostTypes'] : null;
	}

	/**
	 * Register a runner.
	 *
	 * @param Export_Runner_Base $runner_instance
	 */
	public function register( Export_Runner_Base $runner_instance ) {
		$this->runners[ $runner_instance::get_name() ] = $runner_instance;
	}

	public function register_default_runners() {
		$this->register( new Site_Settings() );
		$this->register( new Plugins() );
		$this->register( new Templates() );
		$this->register( new Taxonomies() );
		$this->register( new Elementor_Content() );
		$this->register( new Wp_Content() );
	}

	/**
	 * Execute the export process.
	 *
	 * @return array The export data output.
	 *
	 * @throws \Exception If no export runners have been specified.
	 */
	public function run() {
		if ( empty( $this->runners ) ) {
			throw new \Exception( 'Couldn’t execute the export process because no export runners have been specified. Try again by specifying export runners.' );
		}

		$this->set_default_settings();

		$this->init_zip_archive();
		$this->init_manifest_data();

		$data = [
			'include' => $this->settings_include,
			'selected_plugins' => $this->settings_selected_plugins,
			'selected_custom_post_types' => $this->settings_selected_custom_post_types,
		];

		foreach ( $this->runners as $runner ) {
			if ( $runner->should_export( $data ) ) {
				$export_result = $runner->export( $data );
				$this->handle_export_result( $export_result );
			}
		}

		$this->add_json_file( 'manifest', $this->manifest_data );

		$zip_file_name = $this->zip->filename;
		$this->zip->close();

		return [
			'manifest' => $this->manifest_data,
			'file_name' => $zip_file_name,
		];
	}

	/**
	 * Set default settings for the export.
	 */
	private function set_default_settings() {
		if ( ! is_array( $this->get_settings_include() ) ) {
			$this->settings_include( $this->get_default_settings_include() );
		}

		if ( ! is_array( $this->get_settings_kit_info() ) ) {
			$this->settings_kit_info( $this->get_default_settings_kit_info() );
		}

		if ( ! is_array( $this->get_settings_selected_custom_post_types() ) && in_array( 'content', $this->settings_include, true ) ) {
			$this->settings_selected_custom_post_types( $this->get_default_settings_custom_post_types() );
		}

		if ( ! is_array( $this->get_settings_selected_plugins() ) && in_array( 'plugins', $this->settings_include, true ) ) {
			$this->settings_selected_plugins( $this->get_default_settings_selected_plugins() );
		}
	}

	public function settings_include( $included_settings ) {
		$this->settings_include = $included_settings;
	}

	public function get_settings_include() {
		return $this->settings_include;
	}

	private function settings_kit_info( $kit_info ) {
		$this->settings_kit_info = $kit_info;
	}

	private function get_settings_kit_info() {
		return $this->settings_kit_info;
	}

	public function settings_selected_custom_post_types( $selected_custom_post_types ) {
		$this->settings_selected_custom_post_types = $selected_custom_post_types;
	}

	public function get_settings_selected_custom_post_types() {
		return $this->settings_selected_custom_post_types;
	}

	public function settings_selected_plugins( $plugins ) {
		$this->settings_selected_plugins = $plugins;
	}

	public function get_settings_selected_plugins() {
		return $this->settings_selected_plugins;
	}

	/**
	 * Get the default settings of which content types should be exported.
	 *
	 * @return array
	 */
	private function get_default_settings_include() {
		return [ 'templates', 'content', 'settings', 'plugins' ];
	}

	/**
	 * Get the default settings of the kit info.
	 *
	 * @return array
	 */
	private function get_default_settings_kit_info() {
		return [
			'title' => 'kit',
			'description' => '',
		];
	}

	/**
	 * Get the default settings of the plugins that should be exported.
	 *
	 * @return array{name: string, plugin:string, pluginUri: string, version: string}
	 */
	private function get_default_settings_selected_plugins() {
		$installed_plugins = Plugin::$instance->wp->get_plugins();

		return $installed_plugins->map( function ( $item, $key ) {
			return [
				'name' => $item['Name'],
				'plugin' => $key,
				'pluginUri' => $item['PluginURI'],
				'version' => $item['Version'],
			];
		} )->all();
	}

	/**
	 * Get the default settings of all the custom post types that should be exported.
	 * Should be all the custom post types that are not built in to WordPress and not part of Elementor.
	 *
	 * @return array
	 */
	private function get_default_settings_custom_post_types() {
		return Utils::get_registered_cpt_names();
	}

	/**
	 * Init the zip archive.
	 *
	 * @throws \Error If export process fails, file creation errors occur, or data serialization fails.
	 */
	private function init_zip_archive() {
		if ( ! class_exists( '\ZipArchive' ) ) {
			throw new \Error( static::ZIP_ARCHIVE_MODULE_MISSING ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		$zip = new \ZipArchive();

		$temp_dir = Plugin::$instance->uploads_manager->create_unique_dir();

		$zip_file_name = $temp_dir . sanitize_title( $this->settings_kit_info['title'] ) . '.zip';

		$zip->open( $zip_file_name, \ZipArchive::CREATE | \ZipArchive::OVERWRITE );

		$this->zip = $zip;
	}

	/**
	 * Init the manifest data and add some basic info to it.
	 */
	private function init_manifest_data() {
		$kit_post = Plugin::$instance->kits_manager->get_active_kit()->get_post();

		$manifest_data = [
			'name' => sanitize_title( $this->settings_kit_info['title'] ),
			'title' => $this->settings_kit_info['title'],
			'description' => $this->settings_kit_info['description'],
			'author' => get_the_author_meta( 'display_name', $kit_post->post_author ),
			'version' => Module::FORMAT_VERSION,
			'elementor_version' => ELEMENTOR_VERSION,
			'created' => gmdate( 'Y-m-d H:i:s' ),
			'thumbnail' => get_the_post_thumbnail_url( $kit_post ),
			'site' => get_site_url(),
		];

		$this->manifest_data = $manifest_data;
	}

	/**
	 * Handle the export process output.
	 * Add the manifest data from the runner to the manifest.json file.
	 * Create files according to the files array that should be exported by the runner.
	 *
	 * @param array $export_result
	 */
	private function handle_export_result( $export_result ) {
		foreach ( $export_result['manifest'] as $data ) {
			$this->manifest_data += $data;
		}

		if ( isset( $export_result['files']['path'] ) ) {
			$export_result['files'] = [ $export_result['files'] ];
		}

		foreach ( $export_result['files'] as $file ) {
			$file_extension = pathinfo( $file['path'], PATHINFO_EXTENSION );
			if ( empty( $file_extension ) ) {
				$this->add_json_file(
					$file['path'],
					$file['data']
				);
			} else {
				$this->add_file(
					$file['path'],
					$file['data']
				);
			}
		}
	}

	/**
	 * Add json file to the zip archive.
	 *
	 * @param string $path The relative path to the file.
	 * @param array  $content The content of the file.
	 * @param int    $json_flags
	 */
	private function add_json_file( $path, array $content, $json_flags = 0 ) {
		if ( ! Str::ends_with( $path, '.json' ) ) {
			$path .= '.json';
		}

		$this->add_file( $path, wp_json_encode( $content, $json_flags ) );
	}

	/**
	 * Add file to the zip archive.
	 *
	 * @param string $file
	 * @param string $content The content of the file.
	 */
	private function add_file( $file, $content ) {
		$this->zip->addFromString( $file, $content );
	}
}
