<?php

namespace Elementor\App\Modules\ImportExportCustomization\Processes;

use Elementor\App\Modules\ImportExportCustomization\Compatibility\Base_Adapter;
use Elementor\App\Modules\ImportExportCustomization\Compatibility\Envato;
use Elementor\App\Modules\ImportExportCustomization\Compatibility\Kit_Library;
use Elementor\App\Modules\ImportExportCustomization\Compatibility\Customization;
use Elementor\App\Modules\ImportExportCustomization\Utils;
use Elementor\Core\Base\Document;
use Elementor\Core\Kits\Documents\Kit;
use Elementor\Plugin;

use Elementor\App\Modules\ImportExportCustomization\Runners\Import\Elementor_Content;
use Elementor\App\Modules\ImportExportCustomization\Runners\Import\Import_Runner_Base;
use Elementor\App\Modules\ImportExportCustomization\Runners\Import\Plugins;
use Elementor\App\Modules\ImportExportCustomization\Runners\Import\Site_Settings;
use Elementor\App\Modules\ImportExportCustomization\Runners\Import\Taxonomies;
use Elementor\App\Modules\ImportExportCustomization\Runners\Import\Templates;
use Elementor\App\Modules\ImportExportCustomization\Runners\Import\Wp_Content;
use Elementor\App\Modules\ImportExportCustomization\Module;

class Import {
	const MANIFEST_ERROR_KEY = 'manifest-error';

	const ZIP_FILE_ERROR_KEY = 'invalid-zip-file';

	const ZIP_ARCHIVE_ERROR_KEY = 'zip-archive-module-missing';

	/**
	 * @var Import_Runner_Base[]
	 */
	protected $runners = [];

	/**
	 * The session ID of the import process.
	 * This ID is uniquely generated for each import process (by the temp folder which contains the extracted kit files).
	 *
	 * @var string
	 */
	private $session_id;

	/**
	 * The Kit ID.
	 *
	 * @var string
	 */
	private $kit_id;

	/**
	 * Adapter for the kit compatibility.
	 *
	 * @var Base_Adapter[]
	 */
	private $adapters;

	/**
	 * Document's data (elements and settings) that was imported during the process.
	 *
	 * @var array { [document_id] => { "elements": array , "settings": array } }
	 */
	private $documents_data = [];

	/**
	 * Path to the extracted kit files.
	 *
	 * @var string
	 */
	private $extracted_directory_path;

	/**
	 * Imported kit manifest.
	 *
	 * @var array
	 */
	private $manifest;

	/**
	 * Imported kit site settings. (e.g: custom_colors, custom_typography, etc.)
	 *
	 * @var array
	 */
	private $site_settings;

	/**
	 * Selected content types to import.
	 *
	 * @var array
	 */
	private $settings_include;

	/**
	 * Referer of the import. (e.g: kit-library, local, etc.)
	 *
	 * @var string
	 */
	private $settings_referrer;

	/**
	 * All the conflict between the exited templates and the kit templates.
	 *
	 * @var array
	 */
	private $settings_conflicts;

	/**
	 * Selected elementor templates conditions to override.
	 *
	 * @var array
	 */
	private $settings_selected_override_conditions;

	/**
	 * Selected custom post types to import.
	 *
	 * @var array
	 */
	private $settings_selected_custom_post_types;

	/**
	 * Selected plugins to import.
	 *
	 * @var array
	 */
	private $settings_selected_plugins;

	/**
	 * Customization settings for selective import.
	 *
	 * @var array
	 */
	private $settings_customization;

	/**
	 * The imported data output.
	 *
	 * @var array
	 */
	private $imported_data = [];

	/**
	 * The metadata output of the import runners.
	 * Will be saved in the import_session and will be used to revert the import process.
	 *
	 * @var array
	 */
	private $runners_import_metadata = [];

	/**
	 * @param string     $path session_id | zip_file_path
	 * @param array      $settings Use to determine which content to import.
	 *           (e.g: include, selected_plugins, selected_cpt, selected_override_conditions, etc.)
	 * @param array|null $old_instance An array of old instance parameters that will be used for creating new instance.
	 *      We are using it for quick creation of the instance when the import process is being split into chunks.
	 * @throws \Exception If the import session does not exist.
	 */
	public function __construct( string $path, array $settings = [], array $old_instance = null ) {
		if ( ! empty( $old_instance ) ) {
			$this->set_import_object( $old_instance );
		} else {
			if ( is_file( $path ) ) {
				$this->extracted_directory_path = $this->extract_zip( $path );
			} else {
				$elementor_tmp_directory = Plugin::$instance->uploads_manager->get_temp_dir();
				$path = $elementor_tmp_directory . basename( $path );

				if ( ! is_dir( $path ) ) {
					throw new \Exception( 'Couldn’t execute the import process because the import session does not exist.' );
				}

				$this->extracted_directory_path = $path . '/';
			}

			$this->session_id = basename( $this->extracted_directory_path );
			$this->kit_id = $settings['id'] ?? '';
			$this->settings_referrer = ! empty( $settings['referrer'] ) ? $settings['referrer'] : 'local';
			$this->settings_include = ! empty( $settings['include'] ) ? $settings['include'] : null;

			// Using isset and not empty is important since empty array is valid option.
			$this->settings_selected_override_conditions = $settings['customization']['templates']['themeBuilder']['overrideConditions'] ?? null;
			$this->settings_selected_custom_post_types = $settings['customization']['content']['customPostTypes'] ?? null;
			$this->settings_selected_plugins = $settings['plugins'] ?? null;
			$this->settings_customization = $settings['customization'] ?? null;

			$this->manifest = $this->read_manifest_json();
			$this->site_settings = $this->read_site_settings_json();

			$this->set_default_settings();
		}

		add_filter( 'wp_php_error_args', function ( $args, $error ) {
			return $this->filter_php_error_args( $args, $error );
		}, 10, 2 );
	}

	/**
	 * Set the import object parameters.
	 *
	 * @param array $instance
	 * @return void
	 */
	private function set_import_object( array $instance ) {
		$this->session_id = $instance['session_id'];

		$instance_data = $instance['instance_data'];

		$this->extracted_directory_path = $instance_data['extracted_directory_path'];
		$this->runners = $instance_data['runners'];
		$this->adapters = $instance_data['adapters'];

		$this->manifest = $instance_data['manifest'];
		$this->site_settings = $instance_data['site_settings'];

		$this->kit_id = $instance_data['kit_id'] ?? '';
		$this->settings_include = $instance_data['settings_include'];
		$this->settings_referrer = $instance_data['settings_referrer'];
		$this->settings_conflicts = $instance_data['settings_conflicts'];
		$this->settings_selected_override_conditions = $instance_data['settings_selected_override_conditions'];
		$this->settings_selected_custom_post_types = $instance_data['settings_selected_custom_post_types'];
		$this->settings_selected_plugins = $instance_data['settings_selected_plugins'];
		$this->settings_customization = $instance_data['settings_customization'];

		$this->documents_data = $instance_data['documents_data'];
		$this->imported_data = $instance_data['imported_data'];
		$this->runners_import_metadata = $instance_data['runners_import_metadata'];
	}

	/**
	 * Creating a new instance of the import process by the id of the old import session.
	 *
	 * @param string $session_id
	 *
	 * @return Import
	 * @throws \Exception If the import session does not exist.
	 */
	public static function from_session( string $session_id ): Import {
		$import_sessions = Utils::get_import_sessions();

		if ( ! $import_sessions || ! isset( $import_sessions[ $session_id ] ) ) {
			throw new \Exception( 'Couldn’t execute the import process because the import session does not exist.' );
		}

		$import_session = $import_sessions[ $session_id ];

		return new self( $session_id, [], $import_session );
	}

	/**
	 * Register a runner.
	 * Be aware that the runner will be executed in the order of registration, the order is crucial for the import process.
	 *
	 * @param Import_Runner_Base $runner_instance
	 */
	public function register( Import_Runner_Base $runner_instance ) {
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
	 * Set default settings for the import.
	 */
	private function set_default_settings() {
		if ( ! is_array( $this->get_settings_include() ) ) {
			$this->settings_include( $this->get_default_settings_include() );
		}

		if ( ! is_array( $this->get_settings_conflicts() ) ) {
			$this->settings_conflicts( $this->get_default_settings_conflicts() );
		}

		if ( ! is_array( $this->get_settings_selected_override_conditions() ) ) {
			$this->settings_selected_override_conditions( $this->get_default_settings_override_conditions() );
		}

		if ( ! is_array( $this->get_settings_selected_custom_post_types() ) ) {
			$this->settings_selected_custom_post_types( $this->get_default_settings_custom_post_types() );
		}

		if ( ! is_array( $this->get_settings_selected_plugins() ) ) {
			$this->settings_selected_plugins( $this->get_default_settings_plugins() );
		}

		if ( ! is_array( $this->get_settings_customization() ) ) {
			$this->settings_customization( $this->get_default_settings_customization() );
		}
	}

	/**
	 * Execute the import process.
	 *
	 * @return array The imported data output.
	 *
	 * @throws \Exception If no import runners have been specified.
	 */
	public function run() {
		if ( empty( $this->runners ) ) {
			throw new \Exception( 'Couldn’t execute the import process because no import runners have been specified. Try again by specifying import runners.' );
		}

		$data = [
			'session_id' => $this->session_id,
			'include' => $this->settings_include,
			'manifest' => $this->manifest,
			'site_settings' => $this->site_settings,
			'selected_plugins' => $this->settings_selected_plugins,
			'customization' => $this->settings_customization,
			'extracted_directory_path' => $this->extracted_directory_path,
			'selected_custom_post_types' => $this->settings_selected_custom_post_types,
		];

		$this->init_import_session();

		remove_filter( 'elementor/document/save/data', [ Plugin::$instance->modules_manager->get_modules( 'content-sanitizer' ), 'sanitize_content' ] );
		add_filter( 'elementor/document/save/data', [ $this, 'prevent_saving_elements_on_post_creation' ], 10, 2 );

		// Set the Request's state as an Elementor upload request, in order to support unfiltered file uploads.
		Plugin::$instance->uploads_manager->set_elementor_upload_state( true );

		foreach ( $this->runners as $runner ) {
			if ( $runner->should_import( $data ) ) {
				$import = $runner->import( $data, $this->imported_data );
				$this->imported_data = array_merge_recursive( $this->imported_data, $import );

				$this->runners_import_metadata[ $runner::get_name() ] = $runner->get_import_session_metadata();
			}
		}

		// After the upload complete, set the elementor upload state back to false.
		Plugin::$instance->uploads_manager->set_elementor_upload_state( false );

		remove_filter( 'elementor/document/save/data', [ $this, 'prevent_saving_elements_on_post_creation' ], 10 );

		$this->finalize_import_session_option();

		$this->save_elements_of_imported_posts();

		Plugin::$instance->uploads_manager->remove_file_or_dir( $this->extracted_directory_path );
		return $this->imported_data;
	}

	/**
	 * Run specific runner by runner_name
	 *
	 * @param string $runner_name
	 *
	 * @return array
	 *
	 * @throws \Exception If no export runners have been specified.
	 */
	public function run_runner( string $runner_name ): array {
		if ( empty( $this->runners ) ) {
			throw new \Exception( 'Couldn’t execute the import process because no import runners have been specified. Try again by specifying import runners.' );
		}

		$data = [
			'session_id' => $this->session_id,
			'include' => $this->settings_include,
			'manifest' => $this->manifest,
			'site_settings' => $this->site_settings,
			'selected_plugins' => $this->settings_selected_plugins,
			'customization' => $this->settings_customization,
			'extracted_directory_path' => $this->extracted_directory_path,
			'selected_custom_post_types' => $this->settings_selected_custom_post_types,
		];

		add_filter( 'elementor/document/save/data', [ $this, 'prevent_saving_elements_on_post_creation' ], 10, 2 );

		// Set the Request's state as an Elementor upload request, in order to support unfiltered file uploads.
		Plugin::$instance->uploads_manager->set_elementor_upload_state( true );

		$runner = $this->runners[ $runner_name ];

		if ( empty( $runner ) ) {
			throw new \Exception( 'Couldn’t execute the import process because the import runner was not found. Try again by specifying an import runner.' );
		}

		if ( $runner->should_import( $data ) ) {
			$import = $runner->import( $data, $this->imported_data );
			$this->imported_data = array_merge_recursive( $this->imported_data, $import );

			$this->runners_import_metadata[ $runner::get_name() ] = $runner->get_import_session_metadata();
		}

		// After the upload complete, set the elementor upload state back to false.
		Plugin::$instance->uploads_manager->set_elementor_upload_state( false );

		remove_filter( 'elementor/document/save/data', [ $this, 'prevent_saving_elements_on_post_creation' ], 10 );

		$is_last_runner = key( array_slice( $this->runners, -1, 1, true ) ) === $runner_name;
		if ( $is_last_runner ) {
			$this->finalize_import_session_option();
			$this->save_elements_of_imported_posts();
		} else {
			$this->update_instance_data_in_import_session_option();
		}

		return [
			'status' => 'success',
			'runner' => $runner_name,
			'imported_data' => $this->imported_data,
		];
	}

	/**
	 * Create and save all the instance data to the import sessions option.
	 *
	 * @return void
	 */
	public function init_import_session( $save_instance_data = false ) {
		$import_sessions = Utils::get_import_sessions( true );
		$existing_session = $import_sessions[ $this->session_id ] ?? [];

		$import_sessions[ $this->session_id ] = [
			'session_id' => $this->session_id,
			'kit_title' => $this->manifest['title'] ?? '',
			'kit_name' => $this->manifest['name'] ?? '',
			'kit_thumbnail' => $existing_session['kit_thumbnail'] ?? $this->get_kit_thumbnail(),
			'kit_source' => $existing_session['kit_source'] ?? $this->settings_referrer,
			'user_id' => get_current_user_id(),
			'start_timestamp' => $existing_session['start_timestamp'] ?? current_time( 'timestamp' ),
		];

		if ( $save_instance_data ) {
			$import_sessions[ $this->session_id ]['instance_data'] = [
				'extracted_directory_path' => $this->extracted_directory_path,
				'runners' => $this->runners,
				'adapters' => $this->adapters,

				'manifest' => $this->manifest,
				'site_settings' => $this->site_settings,

				'kit_id' => $this->kit_id,
				'settings_include' => $this->settings_include,
				'settings_referrer' => $this->settings_referrer,
				'settings_conflicts' => $this->settings_conflicts,
				'settings_selected_override_conditions' => $this->settings_selected_override_conditions,
				'settings_selected_custom_post_types' => $this->settings_selected_custom_post_types,
				'settings_selected_plugins' => $this->settings_selected_plugins,
				'settings_customization' => $this->settings_customization,

				'documents_data' => $this->documents_data,
				'imported_data' => $this->imported_data,
				'runners_import_metadata' => $this->runners_import_metadata,
			];
		}

		update_option( Module::OPTION_KEY_ELEMENTOR_IMPORT_SESSIONS, $import_sessions, false );
	}

	/**
	 * Get the Kit thumbnail, goes to the home page thumbnail if main doesn't exist
	 *
	 * @return string
	 */
	private function get_kit_thumbnail(): string {
		if ( ! empty( $this->manifest['thumbnail'] ) ) {
			return $this->manifest['thumbnail'];
		}

		return apply_filters( 'elementor/import/kit_thumbnail', '', $this->kit_id, $this->settings_referrer );
	}

	public function get_runners_name(): array {
		return array_keys( $this->runners );
	}

	public function get_manifest() {
		return $this->manifest;
	}

	public function get_extracted_directory_path() {
		return $this->extracted_directory_path;
	}

	public function get_session_id() {
		return $this->session_id;
	}

	public function get_adapters() {
		return $this->adapters;
	}

	public function get_imported_data() {
		return $this->imported_data;
	}

	/**
	 * Get settings by key.
	 * Used for backward compatibility.
	 *
	 * @param string $key The key of the setting.
	 */
	public function get_settings( $key ) {
		switch ( $key ) {
			case 'include':
				return $this->get_settings_include();

			case 'overrideConditions':
				return $this->get_settings_selected_override_conditions();

			case 'selectedCustomPostTypes':
				return $this->get_settings_selected_custom_post_types();

			case 'plugins':
				return $this->get_settings_selected_plugins();

			case 'customization':
				return $this->get_settings_customization();

			default:
				return [];
		}
	}

	public function settings_include( array $settings_include ) {
		$this->settings_include = $settings_include;

		return $this;
	}

	public function get_settings_include() {
		return $this->settings_include;
	}

	public function settings_referrer( $settings_referrer ) {
		$this->settings_referrer = $settings_referrer;

		return $this;
	}

	public function get_settings_referrer() {
		return $this->settings_referrer;
	}

	public function settings_conflicts( array $settings_conflicts ) {
		$this->settings_conflicts = $settings_conflicts;

		return $this;
	}

	public function get_settings_conflicts() {
		return $this->settings_conflicts;
	}

	public function settings_selected_override_conditions( array $settings_selected_override_conditions ) {
		$this->settings_selected_override_conditions = $settings_selected_override_conditions;

		return $this;
	}

	public function get_settings_selected_override_conditions() {
		return $this->settings_selected_override_conditions;
	}

	public function settings_selected_custom_post_types( array $settings_selected_custom_post_types ) {
		$this->settings_selected_custom_post_types = $settings_selected_custom_post_types;

		return $this;
	}

	public function get_settings_selected_custom_post_types() {
		return $this->settings_selected_custom_post_types;
	}

	public function settings_selected_plugins( array $settings_selected_plugins ) {
		$this->settings_selected_plugins = $settings_selected_plugins;

		return $this;
	}

	public function get_settings_selected_plugins() {
		return $this->settings_selected_plugins;
	}

	/**
	 * Prevent saving elements on elementor post creation.
	 *
	 * @param array    $data
	 * @param Document $document
	 *
	 * @return array
	 */
	public function prevent_saving_elements_on_post_creation( array $data, Document $document ) {
		if ( isset( $data['elements'] ) ) {
			$this->documents_data[ $document->get_main_id() ] = [ 'elements' => $data['elements'] ];

			$data['elements'] = [];
		}

		if ( isset( $data['settings'] ) ) {
			$this->documents_data[ $document->get_main_id() ]['settings'] = $data['settings'];

		}

		return $data;
	}

	/**
	 * Extract the zip file.
	 *
	 * @param string $zip_path The path to the zip file.
	 * @return string The extracted directory path.
	 * @throws \Error If import process fails, file validation errors occur, or data corruption is detected.
	 */
	private function extract_zip( $zip_path ) {
		$extraction_result = Plugin::$instance->uploads_manager->extract_and_validate_zip( $zip_path, [ 'json', 'xml' ] );

		if ( is_wp_error( $extraction_result ) ) {
			if ( isset( $extraction_result->errors['zip_error'] ) ) {
				throw new \Error( static::ZIP_ARCHIVE_ERROR_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
			}

			throw new \Error( static::ZIP_FILE_ERROR_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		return $extraction_result['extraction_directory'];
	}

	/**
	 * Get the manifest file from the extracted directory and adapt it if needed.
	 *
	 * @return string The manifest file content.
	 * @throws \Error If import validation fails or processing errors occur.
	 */
	private function read_manifest_json() {
		$manifest = Utils::read_json_file( $this->extracted_directory_path . 'manifest' );

		if ( ! $manifest ) {
			Plugin::$instance->logger->get_logger()->error( static::MANIFEST_ERROR_KEY );
			throw new \Error( static::ZIP_FILE_ERROR_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		$this->init_adapters( $manifest );

		foreach ( $this->adapters as $adapter ) {
			$manifest = $adapter->adapt_manifest( $manifest );
		}

		return $manifest;
	}

	/**
	 * Init the adapters and determine which ones to use.
	 *
	 * @param array $manifest_data The manifest file content.
	 */
	private function init_adapters( array $manifest_data ) {
		$this->adapters = [];

		/** @var Base_Adapter[] $adapter_types */
		$adapter_types = [ Customization::class, Envato::class, Kit_Library::class ];

		foreach ( $adapter_types as $adapter_type ) {
			if ( $adapter_type::is_compatibility_needed( $manifest_data, [ 'referrer' => $this->get_settings_referrer() ] ) ) {
				$this->adapters[] = new $adapter_type( $this );
			}
		}
	}

	/**
	 * Get the site settings file from the extracted directory and adapt it if needed.
	 *
	 * @return string The site settings file content.
	 */
	private function read_site_settings_json() {
		$site_settings = Utils::read_json_file( $this->extracted_directory_path . 'site-settings' );

		foreach ( $this->adapters as $adapter ) {
			$site_settings = $adapter->adapt_site_settings( $site_settings, $this->manifest, $this->extracted_directory_path );
		}

		return $site_settings;
	}

	/**
	 * Get all the custom post types in the kit.
	 *
	 * @return array Custom post types names.
	 */
	private function get_default_settings_custom_post_types() {
		$excluded = [ 'page', 'nav_menu_item' ];

		if ( empty( $this->manifest['content']['post'] ?? [] ) && empty( $this->manifest['wp-content']['post'] ?? [] ) ) {
			$excluded[] = 'post';
		}

		$manifest_post_types = array_keys( $this->manifest['custom-post-type-title'] ?? [] );

		return array_merge( $manifest_post_types, Utils::get_builtin_wp_post_types( $excluded ) );
	}

	/**
	 * Get the default settings of elementor templates conditions to override.
	 *
	 * @return array
	 */
	private function get_default_settings_conflicts() {
		if ( empty( $this->manifest['templates'] ) ) {
			return [];
		}

		return apply_filters( 'elementor/import/get_default_settings_conflicts', [], $this->manifest['templates'] );
	}

	/**
	 * Get the default settings of elementor templates conditions to override.
	 *
	 * @return array
	 */
	private function get_default_settings_override_conditions() {
		if ( empty( $this->settings_conflicts ) ) {
			return [];
		}

		return array_keys( $this->settings_conflicts );
	}

	/**
	 * Get the default settings of the plugins that should be imported.
	 *
	 * @return array
	 */
	private function get_default_settings_plugins() {
		return ! empty( $this->manifest['plugins'] ) ? $this->manifest['plugins'] : [];
	}

	/**
	 * Get the default settings of which content types should be imported.
	 *
	 * @return array
	 */
	private function get_default_settings_include() {
		return [ 'templates', 'plugins', 'content', 'settings' ];
	}

	public function settings_customization( $customization ) {
		$this->settings_customization = $customization;
		return $this;
	}

	public function get_settings_customization() {
		return $this->settings_customization;
	}

	private function get_default_settings_customization() {
		return [
			'settings' => null,
			'templates' => null,
			'content' => null,
			'plugins' => null,
		];
	}

	/**
	 * Get the data that requires updating/replacement when imported.
	 *
	 * @return array{post_ids: array, term_ids: array}
	 */
	private function get_imported_data_replacements(): array {
		return [
			'post_ids' => Utils::map_old_new_post_ids( $this->imported_data ),
			'term_ids' => Utils::map_old_new_term_ids( $this->imported_data ),
		];
	}

	/**
	 * Save the prevented elements on elementor post creation elements.
	 * Handle the replacement of all the dynamic content of the elements that probably have been changed during the import.
	 */
	private function save_elements_of_imported_posts() {
		$imported_data_replacements = $this->get_imported_data_replacements();

		foreach ( $this->documents_data as $new_id => $data ) {
			$document = Plugin::$instance->documents->get( $new_id );

			if ( isset( $data['elements'] ) ) {
				$data['elements'] = $document->on_import_update_dynamic_content( $data['elements'], $imported_data_replacements );
			}

			if ( isset( $data['settings'] ) ) {

				if ( $document instanceof Kit ) {
					// Without post_status certain tabs in the Kit will not save properly.
					$data['settings']['post_status'] = get_post_status( $new_id );
				}

				$data['settings'] = $document->on_import_update_settings( $data['settings'], $imported_data_replacements );
			}

			$document->save( $data );
		}
	}

	private function update_instance_data_in_import_session_option() {
		$import_sessions = Utils::get_import_sessions();

		$import_sessions[ $this->session_id ]['instance_data']['documents_data'] = $this->documents_data;
		$import_sessions[ $this->session_id ]['instance_data']['imported_data'] = $this->imported_data;
		$import_sessions[ $this->session_id ]['instance_data']['runners_import_metadata'] = $this->runners_import_metadata;

		update_option( Module::OPTION_KEY_ELEMENTOR_IMPORT_SESSIONS, $import_sessions, false );
	}

	public function finalize_import_session_option() {
		$import_sessions = Utils::get_import_sessions();

		if ( ! isset( $import_sessions[ $this->session_id ] ) ) {
			return;
		}

		unset( $import_sessions[ $this->session_id ]['instance_data'] );

		$import_sessions[ $this->session_id ]['end_timestamp'] = current_time( 'timestamp' );
		$import_sessions[ $this->session_id ]['runners'] = $this->runners_import_metadata;

		update_option( Module::OPTION_KEY_ELEMENTOR_IMPORT_SESSIONS, $import_sessions, false );
	}

	/**
	 * Filter the php error args and return 408 status code if the error is a timeout.
	 *
	 * @param array $args
	 * @param array $error
	 * @return array
	 */
	private function filter_php_error_args( $args, $error ) {
		if ( strpos( $error['message'], 'Maximum execution time' ) !== false ) {
			$args['response'] = 408;
		}

		return $args;
	}
}
