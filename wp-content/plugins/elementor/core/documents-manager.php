<?php
namespace Elementor\Core;

use Elementor\Core\Base\Document;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Core\DocumentTypes\Page;
use Elementor\Core\DocumentTypes\Post;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor documents manager.
 *
 * Elementor documents manager handler class is responsible for registering and
 * managing Elementor documents.
 *
 * @since 2.0.0
 */
class Documents_Manager {

	/**
	 * Registered types.
	 *
	 * Holds the list of all the registered types.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @var Document[]
	 */
	protected $types = [];

	/**
	 * Registered documents.
	 *
	 * Holds the list of all the registered documents.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @var Document[]
	 */
	protected $documents = [];

	/**
	 * Current document.
	 *
	 * Holds the current document.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @var Document
	 */
	protected $current_doc;

	/**
	 * Switched data.
	 *
	 * Holds the current document when changing to the requested post.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $switched_data = [];

	protected $cpt = [];

	/**
	 * Documents manager constructor.
	 *
	 * Initializing the Elementor documents manager.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'elementor/documents/register', [ $this, 'register_default_types' ], 0 );
		add_action( 'elementor/ajax/register_actions', [ $this, 'register_ajax_actions' ] );
		add_filter( 'post_row_actions', [ $this, 'filter_post_row_actions' ], 11, 2 );
		add_filter( 'page_row_actions', [ $this, 'filter_post_row_actions' ], 11, 2 );
		add_filter( 'user_has_cap', [ $this, 'remove_user_edit_cap' ], 10, 3 );
		add_filter( 'elementor/editor/localize_settings', [ $this, 'localize_settings' ] );
		add_action( 'rest_api_init', [ $this, 'register_rest_routes' ] );
	}

	/**
	 * Register ajax actions.
	 *
	 * Process ajax action handles when saving data and discarding changes.
	 *
	 * Fired by `elementor/ajax/register_actions` action.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param Ajax $ajax_manager An instance of the ajax manager.
	 */
	public function register_ajax_actions( $ajax_manager ) {
		$ajax_manager->register_ajax_action( 'save_builder', [ $this, 'ajax_save' ] );
		$ajax_manager->register_ajax_action( 'discard_changes', [ $this, 'ajax_discard_changes' ] );
		$ajax_manager->register_ajax_action( 'get_document_config', [ $this, 'ajax_get_document_config' ] );
	}

	/**
	 * Register default types.
	 *
	 * Registers the default document types.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function register_default_types() {
		$default_types = [
			'post' => Post::get_class_full_name(), // BC.
			'wp-post' => Post::get_class_full_name(),
			'wp-page' => Page::get_class_full_name(),
		];

		foreach ( $default_types as $type => $class ) {
			$this->register_document_type( $type, $class );
		}
	}

	/**
	 * Register document type.
	 *
	 * Registers a single document.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $type       Document type name.
	 * @param string $class_name The name of the class that registers the document type.
	 *                           Full name with the namespace.
	 *
	 * @return Documents_Manager The updated document manager instance.
	 */
	public function register_document_type( $type, $class_name ) {
		$this->types[ $type ] = $class_name;

		$cpt = $class_name::get_property( 'cpt' );

		if ( $cpt ) {
			foreach ( $cpt as $post_type ) {
				$this->cpt[ $post_type ] = $type;
			}
		}

		if ( $class_name::get_property( 'register_type' ) ) {
			Source_Local::add_template_type( $type );
		}

		return $this;
	}

	/**
	 * Get document.
	 *
	 * Retrieve the document data based on a post ID.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param int  $post_id    Post ID.
	 * @param bool $from_cache Optional. Whether to retrieve cached data. Default is true.
	 *
	 * @return false|Document Document data or false if post ID was not entered.
	 */
	public function get( $post_id, $from_cache = true ) {
		$this->register_types();

		$post_id = absint( $post_id );

		if ( ! $post_id || ! get_post( $post_id ) ) {
			return false;
		}

		/**
		 * Retrieve document post ID.
		 *
		 * Filters the document post ID.
		 *
		 * @since 2.0.7
		 *
		 * @param int $post_id The post ID of the document.
		 */
		$post_id = apply_filters( 'elementor/documents/get/post_id', $post_id );

		if ( ! $from_cache || ! isset( $this->documents[ $post_id ] ) ) {
			$doc_type = $this->get_doc_type_by_id( $post_id );
			$doc_type_class = $this->get_document_type( $doc_type );

			$this->documents[ $post_id ] = new $doc_type_class( [
				'post_id' => $post_id,
			] );
		}

		return $this->documents[ $post_id ];
	}

	/**
	 * Retrieve a document after checking it exist and allowed to edit.
	 *
	 * @param string $id
	 * @return Document
	 * @throws \Exception If the document is not found or the current user is not allowed to edit it.
	 * @since 3.13.0
	 */
	public function get_with_permissions( $id ): Document {
		$document = $this->get( $id );

		if ( ! $document ) {
			throw new \Exception( 'Not found.' );
		}

		if ( ! $document->is_editable_by_current_user() ) {
			throw new \Exception( 'Access denied.' );
		}

		return $document;
	}

	/**
	 * A `void` version for `get_with_permissions`.
	 *
	 * @param string $id
	 * @return void
	 * @throws \Exception If the document is not found or the current user is not allowed to edit it.
	 */
	public function check_permissions( $id ) {
		$this->get_with_permissions( $id );
	}

	/**
	 * Get document or autosave.
	 *
	 * Retrieve either the document or the autosave.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param int $id      Optional. Post ID. Default is `0`.
	 * @param int $user_id Optional. User ID. Default is `0`.
	 *
	 * @return false|Document The document if it exist, False otherwise.
	 */
	public function get_doc_or_auto_save( $id, $user_id = 0 ) {
		$document = $this->get( $id );
		if ( $document && $document->get_autosave_id( $user_id ) ) {
			$document = $document->get_autosave( $user_id );
		}

		return $document;
	}

	/**
	 * Get document for frontend.
	 *
	 * Retrieve the document for frontend use.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param int $post_id Optional. Post ID. Default is `0`.
	 *
	 * @return false|Document The document if it exist, False otherwise.
	 */
	public function get_doc_for_frontend( $post_id ) {
		$preview_id = (int) Utils::get_super_global_value( $_GET, 'preview_id' );
		$is_preview = is_preview() && $post_id === $preview_id;
		$is_nonce_verify = wp_verify_nonce( Utils::get_super_global_value( $_GET, 'preview_nonce' ), 'post_preview_' . $preview_id );

		if ( ( $is_preview && $is_nonce_verify ) || Plugin::$instance->preview->is_preview_mode() ) {
			$document = $this->get_doc_or_auto_save( $post_id, get_current_user_id() );
		} else {
			$document = $this->get( $post_id );
		}

		return $document;
	}

	/**
	 * Get document type.
	 *
	 * Retrieve the type of any given document.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param string $type
	 *
	 * @param string $fallback
	 *
	 * @return Document|bool The type of the document.
	 */
	public function get_document_type( $type, $fallback = 'post' ) {
		$types = $this->get_document_types();

		if ( isset( $types[ $type ] ) ) {
			return $types[ $type ];
		}

		if ( isset( $types[ $fallback ] ) ) {
			return $types[ $fallback ];
		}

		return false;
	}

	/**
	 * Get document types.
	 *
	 * Retrieve the all the registered document types.
	 *
	 * @since  2.0.0
	 * @access public
	 *
	 * @param array  $args      Optional. An array of key => value arguments to match against
	 *                                the properties. Default is empty array.
	 * @param string $operator Optional. The logical operation to perform. 'or' means only one
	 *                               element from the array needs to match; 'and' means all elements
	 *                               must match; 'not' means no elements may match. Default 'and'.
	 *
	 * @return Document[] All the registered document types.
	 */
	public function get_document_types( $args = [], $operator = 'and' ) {
		$this->register_types();

		if ( ! empty( $args ) ) {
			$types_properties = $this->get_types_properties();

			$filtered = wp_filter_object_list( $types_properties, $args, $operator );

			return array_intersect_key( $this->types, $filtered );
		}

		return $this->types;
	}

	/**
	 * Get document types with their properties.
	 *
	 * @return array A list of properties arrays indexed by the type.
	 */
	public function get_types_properties() {
		$types_properties = [];

		foreach ( $this->get_document_types() as $type => $class ) {
			$types_properties[ $type ] = $class::get_properties();
		}
		return $types_properties;
	}

	/**
	 * Create a document.
	 *
	 * Create a new document using any given parameters.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $type      Document type.
	 * @param array  $post_data An array containing the post data.
	 * @param array  $meta_data An array containing the post meta data.
	 *
	 * @return Document The type of the document.
	 */
	public function create( $type, $post_data = [], $meta_data = [] ) {
		$class = $this->get_document_type( $type, false );

		if ( ! $class ) {
			return new \WP_Error( 500, sprintf( 'Type %s does not exist.', $type ) );
		}

		if ( empty( $post_data['post_title'] ) ) {
			$post_data['post_title'] = esc_html__( 'Elementor', 'elementor' );
			if ( 'post' !== $type ) {
				$post_data['post_title'] = sprintf(
					/* translators: %s: Document title. */
					__( 'Elementor %s', 'elementor' ),
					call_user_func( [ $class, 'get_title' ] )
				);
			}
			$update_title = true;
		}

		$meta_data['_elementor_edit_mode'] = 'builder';

		// Save the type as-is for plugins that hooked at `wp_insert_post`.
		$meta_data[ Document::TYPE_META_KEY ] = $type;

		$post_data['meta_input'] = $meta_data;

		$post_types = $class::get_property( 'cpt' );

		if ( ! empty( $post_types[0] ) && empty( $post_data['post_type'] ) ) {
			$post_data['post_type'] = $post_types[0];
		}

		$post_id = wp_insert_post( $post_data );

		if ( ! empty( $update_title ) ) {
			$post_data['ID'] = $post_id;
			$post_data['post_title'] .= ' #' . $post_id;

			// The meta doesn't need update.
			unset( $post_data['meta_input'] );

			wp_update_post( $post_data );
		}

		/** @var Document $document */
		$document = new $class( [
			'post_id' => $post_id,
		] );

		// Let the $document to re-save the template type by his way + version.
		$document->save( [] );

		return $document;
	}

	/**
	 * Remove user edit capabilities if document is not editable.
	 *
	 * Filters the user capabilities to disable editing in admin.
	 *
	 * @param array $allcaps An array of all the user's capabilities.
	 * @param array $caps    Actual capabilities for meta capability.
	 * @param array $args    Optional parameters passed to has_cap(), typically object ID.
	 *
	 * @return array
	 */
	public function remove_user_edit_cap( $allcaps, $caps, $args ) {
		global $pagenow;

		if ( ! in_array( $pagenow, [ 'post.php', 'edit.php' ], true ) ) {
			return $allcaps;
		}

		// Don't touch not existing or not allowed caps.
		if ( empty( $caps[0] ) || empty( $allcaps[ $caps[0] ] ) ) {
			return $allcaps;
		}

		$capability = $args[0];

		if ( 'edit_post' !== $capability ) {
			return $allcaps;
		}

		if ( empty( $args[2] ) ) {
			return $allcaps;
		}

		$post_id = $args[2];

		$document = Plugin::$instance->documents->get( $post_id );

		if ( ! $document ) {
			return $allcaps;
		}

		$allcaps[ $caps[0] ] = $document::get_property( 'is_editable' );

		return $allcaps;
	}

	/**
	 * Filter Post Row Actions.
	 *
	 * Let the Document to filter the array of row action links on the Posts list table.
	 *
	 * @param array    $actions
	 * @param \WP_Post $post
	 *
	 * @return array
	 */
	public function filter_post_row_actions( $actions, $post ) {
		$document = $this->get( $post->ID );

		if ( $document ) {
			$actions = $document->filter_admin_row_actions( $actions );
		}

		return $actions;
	}

	/**
	 * Save document data using ajax.
	 *
	 * Save the document on the builder using ajax, when saving the changes, and refresh the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $request Post ID.
	 *
	 * @throws \Exception If current user don't have permissions to edit the post or the post is not using Elementor.
	 *
	 * @return array The document data after saving.
	 */
	public function ajax_save( $request ) {
		$document = $this->get( $request['editor_post_id'] );

		if ( ! $document->is_built_with_elementor() || ! $document->is_editable_by_current_user() ) {
			throw new \Exception( 'Access denied.' );
		}

		$this->switch_to_document( $document );

		// Set the post as global post.
		Plugin::$instance->db->switch_to_post( $document->get_post()->ID );

		$status = Document::STATUS_DRAFT;

		if ( isset( $request['status'] ) && in_array( $request['status'], [ Document::STATUS_PUBLISH, Document::STATUS_PRIVATE, Document::STATUS_PENDING, Document::STATUS_AUTOSAVE ], true ) ) {
			$status = $request['status'];
		}

		if ( Document::STATUS_AUTOSAVE === $status ) {
			// If the post is a draft - save the `autosave` to the original draft.
			// Allow a revision only if the original post is already published.
			if ( in_array( $document->get_post()->post_status, [ Document::STATUS_PUBLISH, Document::STATUS_PRIVATE ], true ) ) {
				$document = $document->get_autosave( 0, true );
			}
		}

		// Set default page template because the footer-saver doesn't send default values,
		// But if the template was changed from canvas to default - it needed to save.
		if ( Utils::is_cpt_custom_templates_supported() && ! isset( $request['settings']['template'] ) ) {
			$request['settings']['template'] = 'default';
		}

		$data = [
			'elements' => $request['elements'],
			'settings' => $request['settings'],
		];

		$document->save( $data );

		$post = $document->get_post();
		$main_post = $document->get_main_post();

		// Refresh after save.
		$document = $this->get( $post->ID, false );

		$return_data = [
			'status' => $post->post_status,
			'config' => [
				'document' => [
					'last_edited' => $document->get_last_edited(),
					'urls' => [
						'wp_preview' => $document->get_wp_preview_url(),
					],
				],
			],
		];

		$post_status_object = get_post_status_object( $main_post->post_status );

		if ( $post_status_object ) {
			$return_data['config']['document']['status'] = [
				'value' => $post_status_object->name,
				'label' => $post_status_object->label,
			];
		}

		/**
		 * Returned documents ajax saved data.
		 *
		 * Filters the ajax data returned when saving the post on the builder.
		 *
		 * @since 2.0.0
		 *
		 * @param array    $return_data The returned data.
		 * @param Document $document    The document instance.
		 */
		$return_data = apply_filters( 'elementor/documents/ajax_save/return_data', $return_data, $document );

		return $return_data;
	}

	/**
	 * Ajax discard changes.
	 *
	 * Load the document data from an autosave, deleting unsaved changes.
	 *
	 * @param array $request
	 *
	 * @return bool True if changes discarded, False otherwise.
	 * @throws \Exception If current user don't have permissions to edit the post or the post is not using Elementor.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function ajax_discard_changes( $request ) {
		$document = $this->get_with_permissions( $request['editor_post_id'] );

		$autosave = $document->get_autosave();

		if ( $autosave ) {
			$success = $autosave->delete();
		} else {
			$success = true;
		}

		return $success;
	}

	public function ajax_get_document_config( $request ) {
		$post_id = absint( $request['id'] );

		Plugin::$instance->editor->set_post_id( $post_id );

		$document = $this->get_doc_or_auto_save( $post_id );

		if ( ! $document ) {
			throw new \Exception( 'Not found.' );
		}

		if ( ! $document->is_editable_by_current_user() ) {
			throw new \Exception( 'Access denied.' );
		}

		// Set the global data like $post, $authordata and etc
		Plugin::$instance->db->switch_to_post( $post_id );

		$this->switch_to_document( $document );

		// Change mode to Builder
		$document->set_is_built_with_elementor( true );

		$doc_config = $document->get_config();

		return $doc_config;
	}

	/**
	 * Switch to document.
	 *
	 * Change the document to any new given document type.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param Document $document The document to switch to.
	 */
	public function switch_to_document( $document ) {
		// If is already switched, or is the same post, return.
		if ( $this->current_doc === $document ) {
			$this->switched_data[] = false;
			return;
		}

		$this->switched_data[] = [
			'switched_doc' => $document,
			'original_doc' => $this->current_doc, // Note, it can be null if the global isn't set
		];

		$this->current_doc = $document;
	}

	/**
	 * Restore document.
	 *
	 * Rollback to the original document.
	 *
	 * @since 2.0.0
	 * @access public
	 */
	public function restore_document() {
		$data = array_pop( $this->switched_data );

		// If not switched, return.
		if ( ! $data ) {
			return;
		}

		$this->current_doc = $data['original_doc'];
	}

	/**
	 * Get current document.
	 *
	 * Retrieve the current document.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return Document The current document.
	 */
	public function get_current() {
		return $this->current_doc;
	}

	public function localize_settings( $settings ) {
		$translations = [];

		foreach ( $this->get_document_types() as $type => $class ) {
			$translations[ $type ] = $class::get_title();
		}

		return array_replace_recursive( $settings, [
			'i18n' => $translations,
		] );
	}

	private function register_types() {
		if ( ! did_action( 'elementor/documents/register' ) ) {
			/**
			 * Register Elementor documents.
			 *
			 * @since 2.0.0
			 *
			 * @param Documents_Manager $this The document manager instance.
			 */
			do_action( 'elementor/documents/register', $this );
		}
	}

	/**
	 * Get create new post URL.
	 *
	 * Retrieve a custom URL for creating a new post/page using Elementor.
	 *
	 * @param string      $post_type Optional. Post type slug. Default is 'page'.
	 * @param string|null $template_type Optional. Query arg 'template_type'. Default is null.
	 *
	 * @return string A URL for creating new post using Elementor.
	 */
	public static function get_create_new_post_url( $post_type = 'page', $template_type = null ) {
		$query_args = [
			'action' => 'elementor_new_post',
			'post_type' => $post_type,
		];

		if ( $template_type ) {
			$query_args['template_type'] = $template_type;
		}

		$new_post_url = add_query_arg( $query_args, admin_url( 'edit.php' ) );

		$new_post_url = add_query_arg( '_wpnonce', wp_create_nonce( 'elementor_action_new_post' ), $new_post_url );

		return $new_post_url;
	}

	private function get_doc_type_by_id( $post_id ) {
		// Auto-save inherits from the original post.
		if ( wp_is_post_autosave( $post_id ) ) {
			$post_id = wp_get_post_parent_id( $post_id );
		}

		// Content built with Elementor.
		$template_type = get_post_meta( $post_id, Document::TYPE_META_KEY, true );

		if ( $template_type && isset( $this->types[ $template_type ] ) ) {
			return $template_type;
		}

		// Elementor installation on a site with existing content (which doesn't contain Elementor's meta).
		$post_type = get_post_type( $post_id );

		return $this->cpt[ $post_type ] ?? 'post';
	}

	public function register_rest_routes() {
		register_rest_route('elementor/v1/documents', '/(?P<id>\d+)/media/import', [
			'methods' => \WP_REST_Server::CREATABLE,
			'callback' => function( $request ) {
				$post_id = $request->get_param( 'id' );

				try {
					$document = $this->get_with_permissions( $post_id );

					$elements_data = $document->get_elements_data();

					$import_data = $document->get_import_data( [
						'content' => $elements_data,
					] );

					$document->save( [
						'elements' => $import_data['content'],
					] );

					return new \WP_REST_Response( [
						'success' => true,
						'document_saved' => true,
					], 200 );

				} catch ( \Exception $e ) {
					return new \WP_Error(
						'elementor_import_error',
						$e->getMessage(),
						[ 'status' => 500 ]
					);
				}
			},
			'permission_callback' => function() {
				return current_user_can( 'manage_options' );
			},
			'args' => [
				'id' => [
					'required' => true,
					'validate_callback' => function( $param ) {
						return is_numeric( $param );
					},
				],
			],
		]);
	}
}
