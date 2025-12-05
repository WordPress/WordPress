<?php
namespace Elementor\TemplateLibrary;

use Elementor\Api;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Core\Isolation\Wordpress_Adapter;
use Elementor\Core\Isolation\Wordpress_Adapter_Interface;
use Elementor\Core\Isolation\Elementor_Adapter;
use Elementor\Core\Isolation\Elementor_Adapter_Interface;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Includes\TemplateLibrary\Data\Controller;
use Elementor\TemplateLibrary\Classes\Import_Images;
use Elementor\Plugin;
use Elementor\User;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor template library manager.
 *
 * Elementor template library manager handler class is responsible for
 * initializing the template library.
 *
 * @since 1.0.0
 */
class Manager {

	/**
	 * Registered template sources.
	 *
	 * Holds a list of all the supported sources with their instances.
	 *
	 * @access protected
	 *
	 * @var Source_Base[]
	 */
	protected $_registered_sources = []; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * Imported template images.
	 *
	 * Holds an instance of `Import_Images` class.
	 *
	 * @access private
	 *
	 * @var Import_Images
	 */
	private $_import_images = null; // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore

	/**
	 * @var Wordpress_Adapter_Interface
	 */
	protected $wordpress_adapter = null;

	/**
	 * @var Elementor_Adapter_Interface
	 */
	protected $elementor_adapter = null;

	/**
	 * Template library manager constructor.
	 *
	 * Initializing the template library manager by registering default template
	 * sources and initializing ajax calls.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		Plugin::$instance->data_manager_v2->register_controller( new Controller() );

		$this->register_default_sources();

		$this->add_actions();
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function add_actions() {
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
		add_action( 'wp_ajax_elementor_library_direct_actions', [ $this, 'handle_direct_actions' ] );
	}

	/**
	 * Get `Import_Images` instance.
	 *
	 * Retrieve the instance of the `Import_Images` class.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Import_Images Imported images instance.
	 */
	public function get_import_images_instance() {
		if ( null === $this->_import_images ) {
			$this->_import_images = new Import_Images();
		}

		return $this->_import_images;
	}

	public function set_wordpress_adapter( Wordpress_Adapter_Interface $wordpress_adapter ) {
		$this->wordpress_adapter = $wordpress_adapter;
	}

	public function set_elementor_adapter( Elementor_Adapter_Interface $elementor_adapter ): void {
		$this->elementor_adapter = $elementor_adapter;
	}

	/**
	 * Register template source.
	 *
	 * Used to register new template sources displayed in the template library.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $source_class The name of source class.
	 * @param array  $args         Optional. Class arguments. Default is an
	 *                             empty array.
	 *
	 * @return \WP_Error|true True if the source was registered, `WP_Error`
	 *                        otherwise.
	 */
	public function register_source( $source_class, $args = [] ) {
		if ( ! class_exists( $source_class ) ) {
			return new \WP_Error( 'source_class_name_not_exists' );
		}

		$source_instance = new $source_class( $args );

		if ( ! $source_instance instanceof Source_Base ) {
			return new \WP_Error( 'wrong_instance_source' );
		}

		$source_id = $source_instance->get_id();

		if ( isset( $this->_registered_sources[ $source_id ] ) ) {
			return new \WP_Error( 'source_exists' );
		}

		$this->_registered_sources[ $source_id ] = $source_instance;

		return true;
	}

	/**
	 * Unregister template source.
	 *
	 * Remove an existing template sources from the list of registered template
	 * sources.
	 *
	 * @since 1.0.0
	 * @deprecated 2.7.0
	 * @access public
	 *
	 * @param string $id The source ID.
	 *
	 * @return bool Whether the source was unregistered.
	 */
	public function unregister_source( $id ) {
		return true;
	}

	/**
	 * Get registered template sources.
	 *
	 * Retrieve registered template sources.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return Source_Base[] Registered template sources.
	 */
	public function get_registered_sources() {
		return $this->_registered_sources;
	}

	/**
	 * Get template source.
	 *
	 * Retrieve single template sources for a given template ID.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $id The source ID.
	 *
	 * @return false|Source_Base Template sources if one exist, False otherwise.
	 */
	public function get_source( $id ) {
		$sources = $this->get_registered_sources();

		if ( ! isset( $sources[ $id ] ) ) {
			return false;
		}

		return $sources[ $id ];
	}

	/**
	 * Get templates.
	 *
	 * Retrieve all the templates from all the registered sources.
	 *
	 * @param array $filter_sources
	 * @param bool  $force_update
	 * @return array
	 */
	public function get_templates( array $filter_sources = [], bool $force_update = false ): array {
		$templates = [];

		foreach ( $this->get_registered_sources() as $source ) {
			if ( ! empty( $filter_sources ) && ! in_array( $source->get_id(), $filter_sources, true ) ) {
				continue;
			}

			$templates = array_merge( $templates, $source->get_items( [ 'force_update' => $force_update ] ) );
		}

		return $templates;
	}

	/**
	 * Get library data.
	 *
	 * Retrieve the library data.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @param array $args Library arguments.
	 *
	 * @return array Library data.
	 */
	public function get_library_data( array $args ) {
		$force_update = ! empty( $args['sync'] );

		$library_data = Api::get_library_data( $force_update );

		if ( empty( $library_data ) ) {
			return $library_data;
		}

		// Ensure all document are registered.
		Plugin::$instance->documents->get_document_types();

		$filter_sources = ! empty( $args['filter_sources'] ) ? $args['filter_sources'] : [];

		$full_library_data = [
			'templates' => $this->get_templates( $filter_sources, $force_update ),
			'config' => $library_data['types_data'],
		];

		/**
		 * Filter the full library data.
		 *
		 * @since 3.32.2
		 * @param-out $full_library_data - 'templates' and 'config' data ('config' holds the list of categories).
		 */
		return apply_filters( 'elementor/library/full-data', $full_library_data );
	}

	/**
	 * Save template.
	 *
	 * Save new or update existing template on the database.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Template arguments.
	 *
	 * @return \WP_Error|int The ID of the saved/updated template.
	 */
	public function save_template( array $args ) {
		$validate_args = $this->ensure_args( [ 'post_id', 'source', 'content', 'type' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$sources = (array) $args['source']; // BC
		$results = [];

		foreach ( $sources as $source ) {
			$args_copy = $args;
			$args_copy['source'] = $source;
			$results[] = $this->save_template_item( $args_copy );
		}

		return 1 === count( $results ) ? $results[0] : $results;
	}

	private function save_template_item( array $args ) {
		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		$args['content'] = json_decode( $args['content'], true );

		$page = SettingsManager::get_settings_managers( 'page' )->get_model( $args['post_id'] );

		$args['page_settings'] = $page->get_data( 'settings' );

		$template_id = $source->save_item( $args );

		if ( is_wp_error( $template_id ) ) {
			return $template_id;
		}

		return $source->get_item( $template_id );
	}

	public function move_template( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'from_source', 'from_template_id' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$args['source'] = $args['source'][0];

		$result = $this->move_template_item( $args );

		if ( ! $this->is_action_to_same_source( $args ) ) {
			$this->delete_template( [
				'source' => $args['from_source'],
				'template_id' => $args['from_template_id'],
			] );
		}

		return $result;
	}

	private function move_template_item( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'from_source', 'from_template_id' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		if ( $this->is_action_to_same_source( $args ) ) {
			return $source->move_template_to_folder( $args );
		}

		if ( 'local' === $args['from_source'] ) {
			$args = $this->format_args_for_single_action_from_local_to_cloud( $args );
		}

		if ( 'cloud' === $args['from_source'] ) {
			$args = $this->format_args_for_single_action_from_cloud_to_local( $args );
		}

		$template_id = $source->save_item( $args );

		if ( is_wp_error( $template_id ) ) {
			return $template_id;
		}

		return $source->get_item( $template_id );
	}

	public function copy_template( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'from_source', 'from_template_id' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'][0] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		if ( 'local' === $args['from_source'] ) {
			$args = $this->format_args_for_single_action_from_local_to_cloud( $args );
		}

		if ( 'cloud' === $args['from_source'] ) {
			$args = $this->format_args_for_single_action_from_cloud_to_local( $args );
		}

		$template_id = $source->save_item( $args );

		if ( is_wp_error( $template_id ) ) {
			return $template_id;
		}

		return $source->get_item( $template_id );
	}

	private function is_action_to_same_source( $args ) {
		return $args['source'] === $args['from_source'];
	}

	private function format_args_for_single_action_from_local_to_cloud( $args ) {
		if ( ! $this->is_allowed_to_read_template( [
			'source' => $args['from_source'],
			'template_id' => $args['from_template_id'],
		] ) ) {
			return new \WP_Error(
				'template_error',
				esc_html__( 'You do not have permission to access this template.', 'elementor' )
			);
		}

		$document = Plugin::$instance->documents->get( $args['from_template_id'] );

		if ( ! $document ) {
			return new \WP_Error( 'template_error', 'Document not found.' );
		}

		$args['content'] = $document->get_elements_data();

		$page = SettingsManager::get_settings_managers( 'page' )->get_model( $args['from_template_id'] );
		$args['page_settings'] = $page->get_data( 'settings' );

		return $args;
	}

	private function format_args_for_single_action_from_cloud_to_local( $args ) {
		$from_source = $this->get_source( $args['from_source'] );

		if ( ! $from_source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		$data = $from_source->get_item( $args['from_template_id'] );

		if ( is_wp_error( $data ) || empty( $data['content'] ) ) {
			return new \WP_Error( 'template_error', 'Unable to format template args.' );
		}

		$decoded_data = json_decode( $data['content'], true );
		$args['content'] = $decoded_data['content'];
		$args['page_settings'] = $decoded_data['page_settings'];

		return $args;
	}

	/**
	 * Update template.
	 *
	 * Update template on the database.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $template_data New template data.
	 *
	 * @return \WP_Error|Source_Base Template sources instance if the templates
	 *                               was updated, `WP_Error` otherwise.
	 */
	public function update_template( array $template_data ) {
		$validate_args = $this->ensure_args( [ 'source', 'content', 'type' ], $template_data );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $template_data['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		$template_data['content'] = json_decode( $template_data['content'], true );

		$update = $source->update_item( $template_data );

		if ( is_wp_error( $update ) ) {
			return $update;
		}

		return $source->get_item( $template_data['id'] );
	}

	public function rename_template( array $template_data ) {
		$validate_args = $this->ensure_args( [ 'source', 'title', 'id' ], $template_data );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $template_data['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		$update = $source->update_item( $template_data );

		if ( is_wp_error( $update ) ) {
			return $update;
		}

		return $source->get_item( $template_data['id'] );
	}

	/**
	 * Update templates.
	 *
	 * Update template on the database.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Template arguments.
	 *
	 * @return \WP_Error|true True if templates updated, `WP_Error` otherwise.
	 */
	public function update_templates( array $args ) {
		foreach ( $args['templates'] as $template_data ) {
			$result = $this->update_template( $template_data );

			if ( is_wp_error( $result ) ) {
				return $result;
			}
		}

		return true;
	}

	/**
	 * Get template data.
	 *
	 * Retrieve the template data.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $args Template arguments.
	 *
	 * @return \WP_Error|bool|array ??
	 */
	public function get_template_data( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'template_id' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		if ( ! $this->is_allowed_to_read_template( $args ) ) {
			return new \WP_Error(
				'template_error',
				esc_html__( 'You do not have permission to access this template.', 'elementor' )
			);
		}

		if ( isset( $args['edit_mode'] ) ) {
			Plugin::$instance->editor->set_edit_mode( $args['edit_mode'] );
		}

		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		do_action( 'elementor/template-library/before_get_source_data', $args, $source );

		$data = $source->get_data( $args );

		do_action( 'elementor/template-library/after_get_source_data', $args, $source );

		return $data;
	}

	/**
	 * Delete template.
	 *
	 * Delete template from the database.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Template arguments.
	 *
	 * @return \WP_Post|\WP_Error|false|null Post data on success, false or null
	 *                                       or 'WP_Error' on failure.
	 */
	public function delete_template( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'template_id' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		return $source->delete_template( $args['template_id'] );
	}

	/**
	 * Export template.
	 *
	 * Export template to a file after ensuring it is a valid Elementor template
	 * and checking user permissions for private posts.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Template arguments.
	 *
	 * @return mixed Whether the export succeeded or failed.
	 */
	public function export_template( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'template_id' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found' );
		}

		return $source->export_template( $args['template_id'] );
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function direct_import_template() {
		/** @var Source_Local $source */
		$source = $this->get_source( 'local' );
		$file = Utils::get_super_global_value( $_FILES, 'file' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return $source->import_template( $file['name'], $file['tmp_name'] );
	}

	/**
	 * Import template.
	 *
	 * Import template from a file.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $data
	 *
	 * @return mixed Whether the export succeeded or failed.
	 */
	public function import_template( array $data ) {
		// If the template is a JSON file, allow uploading it.
		add_filter( 'elementor/files/allow-file-type/json', [ $this, 'enable_json_template_upload' ] );
		add_filter( 'elementor/files/allow_unfiltered_upload', [ $this, 'enable_json_template_upload' ] );

		// Imported templates can be either JSON files, or Zip files containing multiple JSON files
		$upload_result = Plugin::$instance->uploads_manager->handle_elementor_upload( $data, [ 'zip', 'json' ] );

		remove_filter( 'elementor/files/allow-file-type/json', [ $this, 'enable_json_template_upload' ] );
		remove_filter( 'elementor/files/allow_unfiltered_upload', [ $this, 'enable_json_template_upload' ] );

		if ( is_wp_error( $upload_result ) ) {
			Plugin::$instance->uploads_manager->remove_file_or_dir( dirname( $upload_result['tmp_name'] ) );

			return $upload_result;
		}

		$source = $this->get_source( $data['source'] ?? 'local' );

		$import_result = $source->import_template( $upload_result['name'], $upload_result['tmp_name'] );

		// Remove the temporary directory generated for the stream-uploaded file.
		Plugin::$instance->uploads_manager->remove_file_or_dir( dirname( $upload_result['tmp_name'] ) );

		return $import_result;
	}

	/**
	 * Enable JSON Template Upload
	 *
	 * Runs on the 'elementor/files/allow-file-type/json' Uploads Manager filter.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * return bool
	 */
	public function enable_json_template_upload() {
		return true;
	}

	/**
	 * Mark template as favorite.
	 *
	 * Add the template to the user favorite templates.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @param array $args Template arguments.
	 *
	 * @return mixed Whether the template marked as favorite.
	 */
	public function mark_template_as_favorite( $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'template_id', 'favorite' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'] );

		return $source->mark_as_favorite( $args['template_id'], filter_var( $args['favorite'], FILTER_VALIDATE_BOOLEAN ) );
	}

	public function import_from_json( array $args ) {
		$validate_args = $this->ensure_args( [ 'editor_post_id', 'elements' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$elements = json_decode( $args['elements'], true );

		$document = Plugin::$instance->documents->get( $args['editor_post_id'] );
		if ( ! $document ) {
			return new \WP_Error( 'template_error', 'Document not found.' );
		}

		$import_data = $document->get_import_data( [ 'content' => $elements ] );

		return $import_data['content'];
	}

	public function get_item_children( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'template_id' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		return $source->get_item_children( $args );
	}

	public function search_templates( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'search' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		return $source->search_templates( $args );
	}

	public function load_more_templates( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'offset' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		return $source->get_items( $args );
	}

	public function create_folder( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'title' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		return $source->save_folder( $args );
	}

	public function get_folders( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'offset' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Folder source not found.' );
		}

		$args['templateType'] = 'folder';

		return $source->get_items( $args );
	}

	/**
	 * Register default template sources.
	 *
	 * Register the 'local' and 'remote' template sources that Elementor use by
	 * default.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function register_default_sources() {
		$sources = [
			'local',
			'remote',
			'cloud',
		];

		foreach ( $sources as $source_filename ) {
			$class_name = ucwords( $source_filename );
			$class_name = str_replace( '-', '_', $class_name );

			$this->register_source( __NAMESPACE__ . '\Source_' . $class_name );
		}
	}

	/**
	 * Handle ajax request.
	 *
	 * Fire authenticated ajax actions for any given ajax request.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param string $ajax_request Ajax request.
	 *
	 * @param array  $data
	 *
	 * @return mixed
	 * @throws \Exception If current user has no permission or the post is not found.
	 */
	private function handle_ajax_request( $ajax_request, array $data ) {
		if ( ! User::is_current_user_can_edit_post_type( Source_Local::CPT ) ) {
			throw new \Exception( 'Access denied.' );
		}

		if ( ! empty( $data['editor_post_id'] ) ) {
			$editor_post_id = absint( $data['editor_post_id'] );

			if ( ! get_post( $editor_post_id ) ) {
				throw new \Exception( 'Post not found.' );
			}

			Plugin::$instance->db->switch_to_post( $editor_post_id );
		}

		$result = call_user_func( [ $this, $ajax_request ], $data );

		if ( is_wp_error( $result ) ) {
			throw new \Exception( esc_html( $result->get_error_message() ) );
		}

		return $result;
	}

	/**
	 * @throws \Exception If template import fails, file validation errors occur, or processing encounters issues.
	 */
	public function save_template_screenshot( $data ): string {
		$validate_args = $this->ensure_args( [ 'template_id', 'screenshot' ], $data );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$raw_binary = base64_decode( substr( $data['screenshot'], strlen( 'data:image/png;base64,' ) ) );

		return $this->get_source( 'cloud' )->save_item_preview( $data['template_id'], $raw_binary );
	}

	/**
	 * @throws \Exception If template processing fails or data validation errors occur.
	 */
	public function template_screenshot_failed( $data ): string {
		$validate_args = $this->ensure_args( [ 'template_id' ], $data );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		return $this->get_source( 'cloud' )->mark_preview_as_failed( $data['template_id'], $data['error'] );
	}

	public function bulk_delete_templates( $data ) {
		$validate_args = $this->ensure_args( [ 'template_ids', 'source' ], $data );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $data['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		if ( empty( $data['template_ids'] ) || ! is_array( $data['template_ids'] ) ) {
			return new \WP_Error( 'template_error', 'Template IDs are missing.' );
		}

		return $source->bulk_delete_items( $data['template_ids'] );
	}

	public function bulk_undo_delete_items( $data ) {
		$validate_args = $this->ensure_args( [ 'template_ids', 'source' ], $data );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $data['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		if ( empty( $data['template_ids'] ) || ! is_array( $data['template_ids'] ) ) {
			return new \WP_Error( 'template_error', 'Template IDs are missing.' );
		}

		return $source->bulk_undo_delete_items( $data['template_ids'] );
	}

	/**
	 * Init ajax calls.
	 *
	 * Initialize template library ajax calls for allowed ajax requests.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param Ajax $ajax
	 */
	public function register_ajax_actions( Ajax $ajax ) {
		$library_ajax_requests = [
			'get_library_data',
			'get_template_data',
			'save_template',
			'update_templates',
			'delete_template',
			'import_template',
			'mark_template_as_favorite',
			'import_from_json',
			'get_item_children',
			'search_templates',
			'rename_template',
			'load_more_templates',
			'create_folder',
			'get_folders',
			'save_template_screenshot',
			'move_template',
			'copy_template',
			'bulk_move_templates',
			'bulk_delete_templates',
			'bulk_copy_templates',
			'bulk_undo_delete_items',
			'get_templates_quota',
			'template_screenshot_failed',
		];

		foreach ( $library_ajax_requests as $ajax_request ) {
			$ajax->register_ajax_action( $ajax_request, function( $data ) use ( $ajax_request ) {
				return $this->handle_ajax_request( $ajax_request, $data );
			} );
		}
	}

	/**
	 * @since 2.3.0
	 * @access public
	 */
	public function handle_direct_actions() {
		if ( ! User::is_current_user_can_edit_post_type( Source_Local::CPT ) ) {
			return;
		}

		/** @var Ajax $ajax */
		$ajax = Plugin::$instance->common->get_component( 'ajax' );

		if ( ! $ajax->verify_request_nonce() ) {
			$this->handle_direct_action_error( 'Access Denied' );
		}

		$action = Utils::get_super_global_value( $_REQUEST, 'library_action' ); // phpcs:ignore -- Nonce already verified.

		$whitelist_methods = [
			'export_template',
			'direct_import_template',
		];

		if ( 'direct_import_template' === $action && ! User::is_current_user_can_upload_json() ) {
			return;
		}

		if ( in_array( $action, $whitelist_methods, true ) ) {
			$result = $this->$action( $_REQUEST ); // phpcs:ignore -- Nonce already verified.
		} else {
			$result = new \WP_Error( 'method_not_exists', 'Method Not exists' );
		}

		if ( is_wp_error( $result ) ) {
			/** @var \WP_Error $result */
			$this->handle_direct_action_error( $result->get_error_message() . '.' );
		}

		$callback = "on_{$action}_success";

		if ( method_exists( $this, $callback ) ) {
			$this->$callback( $result );
		}

		die;
	}

	/**
	 * On successful template import.
	 *
	 * Redirect the user to the template library after template import was
	 * successful finished.
	 *
	 * @since 2.3.0
	 * @access private
	 */
	private function on_direct_import_template_success() {
		wp_safe_redirect( admin_url( Source_Local::ADMIN_MENU_SLUG ) );
	}

	/**
	 * @since 2.3.0
	 * @access private
	 */
	private function handle_direct_action_error( $message ) {
		_default_wp_die_handler( $message, 'Elementor Library' );
	}

	/**
	 * Ensure arguments exist.
	 *
	 * Checks whether the required arguments exist in the specified arguments.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $required_args  Required arguments to check whether they
	 *                              exist.
	 * @param array $specified_args The list of all the specified arguments to
	 *                              check against.
	 *
	 * @return \WP_Error|true True on success, 'WP_Error' otherwise.
	 */
	private function ensure_args( array $required_args, array $specified_args ) {
		$not_specified_args = array_diff( $required_args, array_keys( $specified_args ) );

		if ( $not_specified_args ) {
			return new \WP_Error( 'arguments_not_specified', sprintf( 'The required argument(s) "%s" not specified.', implode( ', ', $not_specified_args ) ) );
		}

		return true;
	}

	private function is_allowed_to_read_template( array $args ): bool {
		if ( 'remote' === $args['source'] || 'cloud' === $args['source'] ) {
			return true;
		}

		if ( null === $this->wordpress_adapter ) {
			$this->set_wordpress_adapter( new WordPress_Adapter() );
		}

		if ( ! $this->should_check_permissions( $args ) ) {
			return true;
		}

		$post_id = intval( $args['template_id'] );
		$post_status = $this->wordpress_adapter->get_post_status( $post_id );
		$is_private_or_non_published = ( 'private' === $post_status && ! $this->wordpress_adapter->current_user_can( 'read_private_posts', $post_id ) ) || ( 'publish' !== $post_status );

		$can_read_template = $is_private_or_non_published || $this->wordpress_adapter->current_user_can( 'edit_post', $post_id );

		return apply_filters( 'elementor/template-library/is_allowed_to_read_template', $can_read_template, $args );
	}

	private function should_check_permissions( array $args ): bool {
		if ( null === $this->elementor_adapter ) {
			$this->set_elementor_adapter( new Elementor_Adapter() );
		}

		// TODO: Remove $isWidgetTemplate in 3.28.0 as there is a Pro dependency
		$check_permissions = isset( $args['check_permissions'] ) && false === $args['check_permissions'];
		$is_widget_template = 'widget' === $this->elementor_adapter->get_template_type( $args['template_id'] );

		if ( $check_permissions || $is_widget_template ) {
			return false;
		}

		return true;
	}

	public function bulk_move_templates( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'from_source', 'from_template_id' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$args['source'] = $args['source'][0];

		return $this->bulk_move_template_items( $args );
	}

	private function bulk_move_template_items( array $args ) {
		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		if ( $this->is_action_to_same_source( $args ) ) {
			return $source->move_bulk_templates_to_folder( $args );
		}

		$bulk_args = 'local' === $args['from_source']
			? $this->format_args_for_bulk_action_from_local( $args )
			: $this->format_args_for_bulk_action_from_cloud( $args );

		if ( $source->supports_quota() && ! $this->is_action_to_same_source( $args ) ) {
			$is_quota_valid = $source->validate_quota( $bulk_args );

			if ( is_wp_error( $is_quota_valid ) ) {
				return $is_quota_valid;
			}

			if ( ! $is_quota_valid ) {
				return new \WP_Error( 'quota_error', 'The moving failed because it will pass the maximum templates you can save.' );
			}
		}

		$bulk_save = $source->save_bulk_items( $bulk_args );

		if ( ! empty( $bulk_save ) ) {
			$this->bulk_delete_templates( [
				'template_ids' => $args['from_template_id'],
				'source' => $args['from_source'],
			] );
		}

		return $bulk_save;
	}

	private function format_args_for_bulk_action_from_local( $args ) {
		$bulk_args = [];

		foreach ( $args['from_template_id'] as $from_template_id ) {
			if ( ! $this->is_allowed_to_read_template( [
				'source' => $args['from_source'],
				'template_id' => $from_template_id,
			] ) ) {
				continue;
			}

			$document = Plugin::$instance->documents->get( $from_template_id );

			if ( ! $document ) {
				continue;
			}

			$page = SettingsManager::get_settings_managers( 'page' )->get_model( $from_template_id );

			$bulk_args[] = array_merge(
				$args,
				[
					'title' => $document->get_post()->post_title,
					'type' => $document::get_type(),
					'content' => $document->get_elements_data(),
					'page_settings' => $page->get_data( 'settings' ),
				]
			);
		}

		return $bulk_args;
	}

	private function format_args_for_bulk_action_from_cloud( $args ) {
		$from_source = $this->get_source( $args['from_source'] );

		if ( ! $from_source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		$templates = $from_source->get_bulk_items( $args );
		$bulk_args = [];

		foreach ( $templates as $template ) {
			$content = json_decode( $template['content'], true );

			$bulk_args[] = array_merge(
				$args,
				[
					'title' => $template['title'],
					'type' => $template['type'],
					'content' => $content['content'],
					'page_settings' => $content['page_settings'],
				]
			);
		}

		return $bulk_args;
	}

	public function bulk_copy_templates( array $args ) {
		$validate_args = $this->ensure_args( [ 'source', 'from_source', 'from_template_id' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$args['source'] = $args['source'][0];

		return $this->bulk_copy_template_items( $args );
	}

	private function bulk_copy_template_items( array $args ) {
		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Template source not found.' );
		}

		$bulk_args = 'local' === $args['from_source']
			? $this->format_args_for_bulk_action_from_local( $args )
			: $this->format_args_for_bulk_action_from_cloud( $args );

		if ( $source->supports_quota() && ! $this->is_action_to_same_source( $args ) ) {
			$is_quota_valid = $source->validate_quota( $bulk_args );

			if ( is_wp_error( $is_quota_valid ) ) {
				return $is_quota_valid;
			}

			if ( ! $is_quota_valid ) {
				return new \WP_Error( 'quota_error', 'The copying failed because it will pass the maximum templates you can save.' );
			}
		}

		return $source->save_bulk_items( $bulk_args );
	}

	public function get_templates_quota( array $args ) {
		$validate_args = $this->ensure_args( [ 'source' ], $args );

		if ( is_wp_error( $validate_args ) ) {
			return $validate_args;
		}

		$source = $this->get_source( $args['source'] );

		if ( ! $source ) {
			return new \WP_Error( 'template_error', 'Source not found.' );
		}

		return $source->get_quota();
	}
}
