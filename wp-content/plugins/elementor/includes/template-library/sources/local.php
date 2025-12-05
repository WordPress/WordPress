<?php
namespace Elementor\TemplateLibrary;

use Elementor\Core\Admin\Menu\Admin_Menu_Manager;
use Elementor\Core\Base\Document;
use Elementor\Core\Editor\Editor;
use Elementor\Core\Utils\Collection;
use Elementor\DB;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Core\Settings\Page\Model;
use Elementor\Includes\TemplateLibrary\Sources\AdminMenuItems\Add_New_Template_Menu_Item;
use Elementor\Includes\TemplateLibrary\Sources\AdminMenuItems\Saved_Templates_Menu_Item;
use Elementor\Includes\TemplateLibrary\Sources\AdminMenuItems\Templates_Categories_Menu_Item;
use Elementor\Modules\Library\Documents\Library_Document;
use Elementor\Plugin;
use Elementor\Utils;
use Elementor\User;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor template library local source.
 *
 * Elementor template library local source handler class is responsible for
 * handling local Elementor templates saved by the user locally on his site.
 *
 * @since 1.0.0
 */
class Source_Local extends Source_Base {

	/**
	 * Elementor template-library post-type slug.
	 */
	const CPT = 'elementor_library';

	/**
	 * Elementor template-library taxonomy slug.
	 */
	const TAXONOMY_TYPE_SLUG = 'elementor_library_type';

	/**
	 * Elementor template-library category slug.
	 */
	const TAXONOMY_CATEGORY_SLUG = 'elementor_library_category';

	/**
	 * Elementor template-library meta key.
	 *
	 * @deprecated 2.3.0 Use `Elementor\Core\Base\Document::TYPE_META_KEY` const instead.
	 */
	const TYPE_META_KEY = '_elementor_template_type';

	/**
	 * Elementor template-library temporary files folder.
	 */
	const TEMP_FILES_DIR = 'elementor/tmp';

	/**
	 * Elementor template-library bulk export action name.
	 */
	const BULK_EXPORT_ACTION = 'elementor_export_multiple_templates';

	const ADMIN_MENU_SLUG = 'edit.php?post_type=elementor_library';

	const ADMIN_MENU_PRIORITY = 10;

	const ADMIN_SCREEN_ID = 'edit-elementor_library';

	/**
	 * Template types.
	 *
	 * Holds the list of supported template types that can be displayed.
	 *
	 * @access private
	 * @static
	 *
	 * @var array
	 */
	private static $template_types = [];

	/**
	 * Post type object.
	 *
	 * Holds the post type object of the current post.
	 *
	 * @access private
	 *
	 * @var \WP_Post_Type
	 */
	private $post_type_object;

	/**
	 * @since 2.3.0
	 * @access public
	 * @static
	 * @return array
	 */
	public static function get_template_types() {
		return self::$template_types;
	}

	/**
	 * Get local template type.
	 *
	 * Retrieve the template type from the post meta.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return mixed The value of meta data field.
	 */
	public static function get_template_type( $template_id ) {
		return get_post_meta( $template_id, Document::TYPE_META_KEY, true );
	}

	/**
	 * Is base templates screen.
	 *
	 * Whether the current screen base is edit and the post type is template.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 *
	 * @return bool True on base templates screen, False otherwise.
	 */
	public static function is_base_templates_screen() {
		global $current_screen;

		if ( ! $current_screen ) {
			return false;
		}

		return 'edit' === $current_screen->base && self::CPT === $current_screen->post_type;
	}

	/**
	 * Add template type.
	 *
	 * Register new template type to the list of supported local template types.
	 *
	 * @since 1.0.3
	 * @access public
	 * @static
	 *
	 * @param string $type Template type.
	 */
	public static function add_template_type( $type ) {
		self::$template_types[ $type ] = $type;
	}

	/**
	 * Remove template type.
	 *
	 * Remove existing template type from the list of supported local template
	 * types.
	 *
	 * @since 1.8.0
	 * @access public
	 * @static
	 *
	 * @param string $type Template type.
	 */
	public static function remove_template_type( $type ) {
		if ( isset( self::$template_types[ $type ] ) ) {
			unset( self::$template_types[ $type ] );
		}
	}

	public static function get_admin_url( $relative = false ) {
		$base_url = self::ADMIN_MENU_SLUG;
		if ( ! $relative ) {
			$base_url = admin_url( $base_url );
		}

		return add_query_arg( 'tabs_group', 'library', $base_url );
	}

	/**
	 * Get local template ID.
	 *
	 * Retrieve the local template ID.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The local template ID.
	 */
	public function get_id() {
		return 'local';
	}

	/**
	 * Get local template title.
	 *
	 * Retrieve the local template title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string The local template title.
	 */
	public function get_title() {
		return esc_html__( 'Local', 'elementor' );
	}

	/**
	 * Register local template data.
	 *
	 * Used to register custom template data like a post type, a taxonomy or any
	 * other data.
	 *
	 * The local template class registers a new `elementor_library` post type
	 * and an `elementor_library_type` taxonomy. They are used to store data for
	 * local templates saved by the user on his site.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function register_data() {
		$labels = [
			'name' => esc_html_x( 'My Templates', 'Template Library', 'elementor' ),
			'singular_name' => esc_html_x( 'Template', 'Template Library', 'elementor' ),
			'add_new' => esc_html__( 'Add New Template', 'elementor' ),
			'add_new_item' => esc_html__( 'Add New Template', 'elementor' ),
			'edit_item' => esc_html__( 'Edit Template', 'elementor' ),
			'new_item' => esc_html__( 'New Template', 'elementor' ),
			'all_items' => esc_html__( 'All Templates', 'elementor' ),
			'view_item' => esc_html__( 'View Template', 'elementor' ),
			'search_items' => esc_html__( 'Search Template', 'elementor' ),
			'not_found' => esc_html__( 'No Templates found', 'elementor' ),
			'not_found_in_trash' => esc_html__( 'No Templates found in Trash', 'elementor' ),
			'parent_item_colon' => esc_html__( 'Parent Template:', 'elementor' ),
			'menu_name' => esc_html_x( 'Templates', 'Template Library', 'elementor' ),
		];

		$args = [
			'labels' => $labels,
			'public' => true,
			'rewrite' => false,
			'menu_icon' => 'dashicons-admin-page',
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'supports' => [ 'title', 'thumbnail', 'author', 'elementor', 'custom-fields' ],
			'show_in_rest' => true,
		];

		$this->avoid_rest_access_for_non_admins();

		/**
		 * Register template library post type args.
		 *
		 * Filters the post type arguments when registering elementor template library post type.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args Arguments for registering a post type.
		 */
		$args = apply_filters( 'elementor/template_library/sources/local/register_post_type_args', $args );

		$this->post_type_object = register_post_type( self::CPT, $args );

		$args = [
			'hierarchical' => false,
			'show_ui' => false,
			'show_in_nav_menus' => false,
			'show_admin_column' => true,
			'query_var' => is_admin(),
			'rewrite' => false,
			'public' => false,
			'label' => esc_html_x( 'Type', 'Template Library', 'elementor' ),
		];

		/**
		 * Register template library taxonomy args.
		 *
		 * Filters the taxonomy arguments when registering elementor template library taxonomy.
		 *
		 * @since 1.0.0
		 *
		 * @param array $args Arguments for registering a taxonomy.
		 */
		$args = apply_filters( 'elementor/template_library/sources/local/register_taxonomy_args', $args );

		$cpts_to_associate = [ self::CPT ];

		/**
		 * Custom post types to associate.
		 *
		 * Filters the list of custom post types when registering elementor template library taxonomy.
		 *
		 * @since 1.0.0
		 *
		 * @param array $cpts_to_associate Custom post types. Default is `elementor_library` post type.
		 */
		$cpts_to_associate = apply_filters( 'elementor/template_library/sources/local/register_taxonomy_cpts', $cpts_to_associate );

		register_taxonomy( self::TAXONOMY_TYPE_SLUG, $cpts_to_associate, $args );

		/**
		 * Categories
		 */
		$args = [
			'hierarchical' => true,
			'show_ui' => true,
			'show_in_nav_menus' => false,
			'show_admin_column' => true,
			'query_var' => is_admin(),
			'rewrite' => false,
			'public' => false,
			'labels' => [
				'name' => esc_html_x( 'Categories', 'Template Library', 'elementor' ),
				'singular_name' => esc_html_x( 'Category', 'Template Library', 'elementor' ),
				'all_items' => esc_html_x( 'All Categories', 'Template Library', 'elementor' ),
			],
		];

		/**
		 * Register template library category args.
		 *
		 * Filters the category arguments when registering elementor template library category.
		 *
		 * @since 2.4.0
		 *
		 * @param array $args Arguments for registering a category.
		 */
		$args = apply_filters( 'elementor/template_library/sources/local/register_category_args', $args );

		register_taxonomy( self::TAXONOMY_CATEGORY_SLUG, self::CPT, $args );
	}

	/**
	 * Remove Add New item from admin menu.
	 *
	 * Fired by `admin_menu` action.
	 *
	 * @since 2.4.0
	 * @access public
	 */
	private function admin_menu_reorder( Admin_Menu_Manager $admin_menu ) {
		global $submenu;

		if ( ! isset( $submenu[ static::ADMIN_MENU_SLUG ] ) ) {
			return;
		}

		remove_submenu_page( static::ADMIN_MENU_SLUG, static::ADMIN_MENU_SLUG );

		$add_new_slug = 'post-new.php?post_type=' . static::CPT;
		$category_slug = 'edit-tags.php?taxonomy=' . static::TAXONOMY_CATEGORY_SLUG . '&amp;post_type=' . static::CPT;

		$library_submenu = new Collection( $submenu[ static::ADMIN_MENU_SLUG ] );

		$add_new_item = $library_submenu->find( function ( $item ) use ( $add_new_slug ) {
			return $add_new_slug === $item[2];
		} );

		$categories_item = $library_submenu->find( function ( $item ) use ( $category_slug ) {
			return $category_slug === $item[2];
		} );

		if ( $add_new_item ) {
			remove_submenu_page( static::ADMIN_MENU_SLUG, $add_new_slug );

			$admin_menu->register( admin_url( static::ADMIN_MENU_SLUG . '#add_new' ), new Add_New_Template_Menu_Item() );
		}

		if ( $categories_item ) {
			remove_submenu_page( static::ADMIN_MENU_SLUG, $category_slug );

			$admin_menu->register( $category_slug, new Templates_Categories_Menu_Item() );
		}
	}

	/**
	 * Add a `current` CSS class to the `Saved Templates` submenu page when it's active.
	 * It should work by default, but since we interfere with WordPress' builtin CPT menus it doesn't work properly.
	 *
	 * @return void
	 */
	private function admin_menu_set_current() {
		global $submenu;

		if ( $this->is_current_screen() ) {
			$library_submenu = &$submenu[ static::ADMIN_MENU_SLUG ];
			$library_title = $this->get_library_title();

			foreach ( $library_submenu as &$item ) {
				if ( $library_title === $item[0] ) {
					if ( ! isset( $item[4] ) ) {
						$item[4] = '';
					}
					$item[4] .= ' current';
				}
			}
		}
	}

	private function register_admin_menu( Admin_Menu_Manager $admin_menu ) {
		$admin_menu->register( static::get_admin_url( true ), new Saved_Templates_Menu_Item() );
	}

	public function admin_title( $admin_title, $title ) {
		$library_title = $this->get_library_title();

		if ( $library_title ) {
			$admin_title = str_replace( $title, $library_title, $admin_title );
		}

		return $admin_title;
	}

	public function replace_admin_heading() {
		$library_title = $this->get_library_title();

		if ( $library_title ) {
			global $post_type_object;

			$post_type_object->labels->name = $library_title;
		}
	}

	/**
	 * Get local templates.
	 *
	 * Retrieve local templates saved by the user on his site.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $args Optional. Filter templates based on a set of
	 *                    arguments. Default is an empty array.
	 *
	 * @return array Local templates.
	 */
	public function get_items( $args = [] ) {
		$template_types = array_values( self::$template_types );

		if ( ! empty( $args['type'] ) ) {
			$template_types = $args['type'];
			unset( $args['type'] );
		}

		$defaults = [
			'post_type' => self::CPT,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
			'meta_query' => [
				[
					'key' => Document::TYPE_META_KEY,
					'value' => $template_types,
				],
			],
		];

		$query_args = wp_parse_args( $args, $defaults );

		$templates_query = new \WP_Query( $query_args );

		$templates = [];

		if ( $templates_query->have_posts() ) {
			foreach ( $templates_query->get_posts() as $post ) {
				$templates[] = $this->get_item( $post->ID );
			}
		}

		return $templates;
	}

	/**
	 * Save local template.
	 *
	 * Save new or update existing template on the database.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $template_data Local template data.
	 *
	 * @return \WP_Error|int The ID of the saved/updated template, `WP_Error` otherwise.
	 */
	public function save_item( $template_data ) {
		if ( ! current_user_can( $this->post_type_object->cap->edit_posts ) ) {
			return new \WP_Error( 'save_error', esc_html__( 'Access denied.', 'elementor' ) );
		}

		$defaults = [
			'title' => esc_html__( '(no title)', 'elementor' ),
			'page_settings' => [],
		];

		$template_data = wp_parse_args( $template_data, $defaults );
		$template_data['status'] = current_user_can( 'publish_posts' ) ? 'publish' : 'pending';

		// BC: Allow importing any template type when using CLI
		// to support users that rely on this mechanism.
		$should_check_template_type = ! $this->is_wp_cli();

		if (
				$should_check_template_type &&
				! $this->is_valid_template_type( $template_data['type'] )
		) {
			return new \WP_Error( 'invalid_template_type', esc_html__( 'Invalid template type.', 'elementor' ) );
		}

		$document = Plugin::$instance->documents->create(
			$template_data['type'],
			[
				'post_title' => $template_data['title'],
				'post_status' => $template_data['status'],
			]
		);

		if ( is_wp_error( $document ) ) {
			/**
			 * @var \WP_Error $document
			 */
			return $document;
		}

		if ( ! empty( $template_data['content'] ) ) {
			$template_data['content'] = $this->replace_elements_ids( $template_data['content'] );
		}

		$document->save( [
			'elements' => $template_data['content'],
			'settings' => $template_data['page_settings'],
		] );

		$template_id = $document->get_main_id();

		/**
		 * After template library save.
		 *
		 * Fires after Elementor template library was saved.
		 *
		 * @since 1.0.1
		 *
		 * @param int   $template_id   The ID of the template.
		 * @param array $template_data The template data.
		 */
		do_action( 'elementor/template-library/after_save_template', $template_id, $template_data );

		/**
		 * After template library update.
		 *
		 * Fires after Elementor template library was updated.
		 *
		 * @since 1.0.1
		 *
		 * @param int   $template_id   The ID of the template.
		 * @param array $template_data The template data.
		 */
		do_action( 'elementor/template-library/after_update_template', $template_id, $template_data );

		return $template_id;
	}

	protected function is_valid_template_type( $type ) {
		$document_class = Plugin::$instance->documents->get_document_type( $type, false );

		if ( ! $document_class ) {
			return false;
		}

		$cpt = $document_class::get_property( 'cpt' );

		if ( ! $cpt || ! is_array( $cpt ) || 1 !== count( $cpt ) ) {
			return false;
		}

		$is_valid_template_type = in_array( static::CPT, $cpt, true );

		return apply_filters(
			'elementor/template_library/sources/local/is_valid_template_type',
			$is_valid_template_type,
			$cpt,
		);
	}

	/** For testing purposes only, in order to be able to mock the `WP_CLI` constant. */
	protected function is_wp_cli() {
		return Utils::is_wp_cli();
	}

	/**
	 * Update local template.
	 *
	 * Update template on the database.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $new_data New template data.
	 *
	 * @return \WP_Error|true True if template updated, `WP_Error` otherwise.
	 */
	public function update_item( $new_data ) {
		if ( ! current_user_can( $this->post_type_object->cap->edit_post, $new_data['id'] ) ) {
			return new \WP_Error( 'save_error', esc_html__( 'Access denied.', 'elementor' ) );
		}

		$document = Plugin::$instance->documents->get( $new_data['id'] );

		if ( ! $document ) {
			return new \WP_Error( 'save_error', esc_html__( 'Template not exist.', 'elementor' ) );
		}

		$save_data = [];

		if ( isset( $new_data['title'] ) ) {
			$save_data['post_title'] = $new_data['title'];
		}

		if ( isset( $new_data['content'] ) ) {
			$save_data['elements'] = $new_data['content'];
		}

		$document->save( $save_data );

		/**
		 * After template library update.
		 *
		 * Fires after Elementor template library was updated.
		 *
		 * @since 1.0.0
		 *
		 * @param int   $new_data_id The ID of the new template.
		 * @param array $new_data    The new template data.
		 */
		do_action( 'elementor/template-library/after_update_template', $new_data['id'], $new_data );

		return true;
	}

	/**
	 * Get local template.
	 *
	 * Retrieve a single local template saved by the user on his site.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return array Local template.
	 */
	public function get_item( $template_id ) {
		$post = get_post( $template_id );

		$user = get_user_by( 'id', $post->post_author );

		$page = SettingsManager::get_settings_managers( 'page' )->get_model( $template_id );

		$page_settings = $page->get_data( 'settings' );

		$date = strtotime( $post->post_date );

		$data = [
			'template_id' => $post->ID,
			'source' => $this->get_id(),
			'type' => self::get_template_type( $post->ID ),
			'title' => $post->post_title,
			'thumbnail' => get_the_post_thumbnail_url( $post ),
			'date' => $date,
			'human_date' => date_i18n( get_option( 'date_format' ), $date ),
			'human_modified_date' => date_i18n( get_option( 'date_format' ), strtotime( $post->post_modified ) ),
			'author' => $user->display_name,
			'status' => $post->post_status,
			'hasPageSettings' => ! empty( $page_settings ),
			'tags' => [],
			'export_link' => $this->get_export_link( $template_id ),
			'url' => get_permalink( $post->ID ),
		];

		/**
		 * Get template library template.
		 *
		 * Filters the template data when retrieving a single template from the
		 * template library.
		 *
		 * @since 1.0.0
		 *
		 * @param array $data Template data.
		 */
		$data = apply_filters( 'elementor/template-library/get_template', $data );

		return $data;
	}

	/**
	 * Get template data.
	 *
	 * Retrieve the data of a single local template saved by the user on his site.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $args Custom template arguments.
	 *
	 * @return array Local template data.
	 */
	public function get_data( array $args ) {
		$template_id = $args['template_id'];

		$document = Plugin::$instance->documents->get( $template_id );
		$content = [];

		if ( $document ) {
			// TODO: Validate the data (in JS too!).
			if ( ! empty( $args['display'] ) ) {
				$content = $document->get_elements_raw_data( null, true );
			} else {
				$content = $document->get_elements_data();
			}

			if ( ! empty( $content ) ) {
				$content = $this->replace_elements_ids( $content );
			}
		}

		$data = [
			'content' => $content,
		];

		if ( ! empty( $args['with_page_settings'] ) ) {
			$page = SettingsManager::get_settings_managers( 'page' )->get_model( $args['template_id'] );

			$data['page_settings'] = $page->get_data( 'settings' );
		}

		return $data;
	}

	/**
	 * Delete local template.
	 *
	 * Delete template from the database.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return \WP_Post|\WP_Error|false|null Post data on success, false or null
	 *                                       or 'WP_Error' on failure.
	 */
	public function delete_template( $template_id ) {
		if ( ! current_user_can( $this->post_type_object->cap->delete_post, $template_id ) ) {
			return new \WP_Error( 'template_error', esc_html__( 'Access denied.', 'elementor' ) );
		}

		return wp_delete_post( $template_id, true );
	}

	public function bulk_delete_items( array $template_ids ) {
		foreach ( $template_ids as $template_id ) {
			if ( ! current_user_can( $this->post_type_object->cap->delete_post, $template_id ) ) {
				return new \WP_Error( 'template_error', esc_html__( 'Access denied.', 'elementor' ) );
			}
		}

		foreach ( $template_ids as $template_id ) {
			wp_delete_post( $template_id, true );
		}

		return true;
	}

	/**
	 * Export local template.
	 *
	 * Export template to a file.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return \WP_Error WordPress error if template export failed.
	 */
	public function export_template( $template_id ) {
		$permissions_error = $this->validate_template_export_permissions( $template_id );

		if ( is_wp_error( $permissions_error ) ) {
			return $permissions_error;
		}

		$file_data = $this->prepare_template_export( $template_id );

		if ( is_wp_error( $file_data ) ) {
			return $file_data;
		}

		$this->send_file_headers( $file_data['name'], strlen( $file_data['content'] ) );

		$this->serve_file( $file_data['content'] );

		die;
	}

	private function validate_template_export_permissions( $template_id ) {
		$post_id = intval( $template_id );
		if ( get_post_type( $post_id ) !== self::CPT ) {
			return new \WP_Error( 'template_error', esc_html__( 'Invalid template type or template does not exist.', 'elementor' ) );
		}

		$post_status = get_post_status( $post_id );
		if ( 'private' === $post_status && ! current_user_can( 'read_private_posts', $post_id ) ) {
			return new \WP_Error( 'template_error', esc_html__( 'You do not have permission to access this template.', 'elementor' ) );
		}

		if ( 'publish' !== $post_status && ! current_user_can( 'edit_post', $post_id ) ) {
			return new \WP_Error( 'template_error', esc_html__( 'You do not have permission to export this template.', 'elementor' ) );
		}

		return null;
	}

	/**
	 * Export multiple local templates.
	 *
	 * Export multiple template to a ZIP file.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array $template_ids An array of template IDs.
	 *
	 * @return \WP_Error WordPress error if export failed.
	 */
	public function export_multiple_templates( array $template_ids ) {
		$files = [];

		$wp_upload_dir = wp_upload_dir();

		$temp_path = $wp_upload_dir['basedir'] . '/' . self::TEMP_FILES_DIR;

		// Create temp path if it doesn't exist
		wp_mkdir_p( $temp_path );

		// Create all json files
		foreach ( $template_ids as $template_id ) {
			$file_data = $this->prepare_template_export( $template_id );

			if ( is_wp_error( $file_data ) ) {
				continue;
			}

			$complete_path = $temp_path . '/' . $file_data['name'];

			$put_contents = file_put_contents( $complete_path, $file_data['content'] );

			if ( ! $put_contents ) {
				return new \WP_Error( '404', sprintf( 'Cannot create file "%s".', $file_data['name'] ) );
			}

			$files[] = [
				'path' => $complete_path,
				'name' => $file_data['name'],
			];
		}

		if ( ! $files ) {
			return new \WP_Error( 'empty_files', 'There is no files to export (probably all the requested templates are empty).' );
		}

		// Create temporary .zip file
		$zip_archive_filename = 'elementor-templates-' . gmdate( 'Y-m-d' ) . '.zip';

		$zip_archive = new \ZipArchive();

		$zip_complete_path = $temp_path . '/' . $zip_archive_filename;

		$zip_archive->open( $zip_complete_path, \ZipArchive::CREATE );

		foreach ( $files as $file ) {
			$zip_archive->addFile( $file['path'], $file['name'] );
		}

		$zip_archive->close();

		foreach ( $files as $file ) {
			unlink( $file['path'] );
		}

		$this->send_file_headers( $zip_archive_filename, $this->filesize( $zip_complete_path ) );

		$this->serve_zip( $zip_complete_path );

		unlink( $zip_complete_path );

		die;
	}

	/**
	 * Import local template.
	 *
	 * Import template from a file.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $name - The file name.
	 * @param string $path - The file path.
	 * @return \WP_Error|array An array of items on success, 'WP_Error' on failure.
	 */
	public function import_template( $name, $path ) {
		if ( empty( $path ) ) {
			return new \WP_Error( 'file_error', 'Please upload a file to import' );
		}

		// Set the Request's state as an Elementor upload request, in order to support unfiltered file uploads.
		Plugin::$instance->uploads_manager->set_elementor_upload_state( true );

		$items = [];

		// If the import file is a Zip file with potentially multiple JSON files
		if ( 'zip' === pathinfo( $name, PATHINFO_EXTENSION ) ) {
			$extracted_files = Plugin::$instance->uploads_manager->extract_and_validate_zip( $path, [ 'json' ] );

			if ( is_wp_error( $extracted_files ) ) {
				// Delete the temporary extraction directory, since it's now not necessary.
				Plugin::$instance->uploads_manager->remove_file_or_dir( $extracted_files['extraction_directory'] );

				return $extracted_files;
			}

			foreach ( $extracted_files['files'] as $file_path ) {
				// Skip macOS metadata files and folders
				if ( false !== strpos( $file_path, '__MACOSX' ) || '.' === basename( $file_path )[0] ) {
					continue;
				}

				$import_result = $this->import_single_template( $file_path );

				if ( is_wp_error( $import_result ) ) {
					// Skip failed files
					continue;
				}

				$items[] = $import_result;
			}

			// Delete the temporary extraction directory, since it's now not necessary.
			Plugin::$instance->uploads_manager->remove_file_or_dir( $extracted_files['extraction_directory'] );
		} else {
			// If the import file is a single JSON file
			$import_result = $this->import_single_template( $path );

			if ( is_wp_error( $import_result ) ) {
				return $import_result;
			}

			$items[] = $import_result;
		}

		return $items;
	}

	/**
	 * Post row actions.
	 *
	 * Add an export link to the template library action links table list.
	 *
	 * Fired by `post_row_actions` filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array    $actions An array of row action links.
	 * @param \WP_Post $post    The post object.
	 *
	 * @return array An updated array of row action links.
	 */
	public function post_row_actions( $actions, \WP_Post $post ) {
		if ( self::is_base_templates_screen() ) {
			if ( $this->is_template_supports_export( $post->ID ) ) {
				$actions['export-template'] = sprintf( '<a href="%1$s">%2$s</a>', $this->get_export_link( $post->ID ), esc_html__( 'Export Template', 'elementor' ) );
			}
		}

		return $actions;
	}

	/**
	 * Admin import template form.
	 *
	 * The import form displayed in "My Library" screen in WordPress dashboard.
	 *
	 * The form allows the user to import template in json/zip format to the site.
	 *
	 * Fired by `admin_footer` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function admin_import_template_form() {
		if ( ! self::is_base_templates_screen() || ! User::is_current_user_can_upload_json() ) {
			return;
		}

		/** @var \Elementor\Core\Common\Modules\Ajax\Module $ajax */
		$ajax = Plugin::$instance->common->get_component( 'ajax' );
		?>
		<div id="elementor-hidden-area">
			<a id="elementor-import-template-trigger" class="page-title-action"><?php echo esc_html__( 'Import Templates', 'elementor' ); ?></a>
			<div id="elementor-import-template-area">
				<div id="elementor-import-template-title"><?php echo esc_html__( 'Choose an Elementor template JSON file or a .zip archive of Elementor templates, and add them to the list of templates available in your library.', 'elementor' ); ?></div>
				<form id="elementor-import-template-form" method="post" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" enctype="multipart/form-data">
					<input type="hidden" name="action" value="elementor_library_direct_actions">
					<input type="hidden" name="library_action" value="direct_import_template">
					<input type="hidden" name="_nonce" value="<?php Utils::print_unescaped_internal_string( $ajax->create_nonce() ); ?>">
					<fieldset id="elementor-import-template-form-inputs">
						<input type="file" name="file" accept=".json,application/json,.zip,application/octet-stream,application/zip,application/x-zip,application/x-zip-compressed" required>
						<input id="e-import-template-action" type="submit" class="button" value="<?php echo esc_attr__( 'Import Now', 'elementor' ); ?>">
					</fieldset>
				</form>
			</div>
		</div>
		<?php
	}

	/**
	 * Block template frontend
	 *
	 * Don't display the single view of the template library post type in the
	 * frontend, for users that don't have the proper permissions.
	 *
	 * Fired by `template_redirect` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function block_template_frontend() {
		if ( is_singular( self::CPT ) && ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			wp_safe_redirect( site_url(), 301 );
			die;
		}
	}

	/**
	 * Is template library supports export.
	 *
	 * Whether the template library supports export.
	 *
	 * Template saved by the user locally on his site, support export by default
	 * but this can be changed using a filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return bool Whether the template library supports export.
	 */
	public function is_template_supports_export( $template_id ) {
		$export_support = true;

		/**
		 * Is template library supports export.
		 *
		 * Filters whether the template library supports export.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $export_support Whether the template library supports export.
		 *                             Default is true.
		 * @param int  $template_id    Post ID.
		 */
		$export_support = apply_filters( 'elementor/template_library/is_template_supports_export', $export_support, $template_id );

		return $export_support;
	}

	/**
	 * Remove Elementor post state.
	 *
	 * Remove the 'elementor' post state from the display states of the post.
	 *
	 * Used to remove the 'elementor' post state from the template library items.
	 *
	 * Fired by `display_post_states` filter.
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @param array    $post_states An array of post display states.
	 * @param \WP_Post $post        The current post object.
	 *
	 * @return array Updated array of post display states.
	 */
	public function remove_elementor_post_state_from_library( $post_states, $post ) {
		if ( self::CPT === $post->post_type && isset( $post_states['elementor'] ) ) {
			unset( $post_states['elementor'] );
		}
		return $post_states;
	}

	/**
	 * Get template export link.
	 *
	 * Retrieve the link used to export a single template based on the template
	 * ID.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return string Template export URL.
	 */
	private function get_export_link( $template_id ) {
		// TODO: BC since 2.3.0 - Use `$ajax->create_nonce()`
		/** @var \Elementor\Core\Common\Modules\Ajax\Module $ajax */
		// $ajax = Plugin::$instance->common->get_component( 'ajax' );

		return add_query_arg(
			[
				'action' => 'elementor_library_direct_actions',
				'library_action' => 'export_template',
				'source' => $this->get_id(),
				'_nonce' => wp_create_nonce( 'elementor_ajax' ),
				'template_id' => $template_id,
			],
			admin_url( 'admin-ajax.php' )
		);
	}

	/**
	 * On template save.
	 *
	 * Run this method when template is being saved.
	 *
	 * Fired by `save_post` action.
	 *
	 * @since 1.0.1
	 * @access public
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    The current post object.
	 */
	public function on_save_post( $post_id, \WP_Post $post ) {
		if ( self::CPT !== $post->post_type ) {
			return;
		}

		if ( self::get_template_type( $post_id ) ) { // It's already with a type
			return;
		}

		// Don't save type on import, the importer will do it.
		if ( did_action( 'import_start' ) ) {
			return;
		}

		$this->save_item_type( $post_id, 'page' );
	}

	/**
	 * Save item type.
	 *
	 * When saving/updating templates, this method is used to update the post
	 * meta data and the taxonomy.
	 *
	 * @since 1.0.1
	 * @access private
	 *
	 * @param int    $post_id Post ID.
	 * @param string $type    Item type.
	 */
	private function save_item_type( $post_id, $type ) {
		update_post_meta( $post_id, Document::TYPE_META_KEY, $type );

		wp_set_object_terms( $post_id, $type, self::TAXONOMY_TYPE_SLUG );
	}

	/**
	 * Bulk export action.
	 *
	 * Adds an 'Export' action to the Bulk Actions drop-down in the template
	 * library.
	 *
	 * Fired by `bulk_actions-edit-elementor_library` filter.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param array $actions An array of the available bulk actions.
	 *
	 * @return array An array of the available bulk actions.
	 */
	public function admin_add_bulk_export_action( $actions ) {
		$actions[ self::BULK_EXPORT_ACTION ] = esc_html__( 'Export', 'elementor' );

		return $actions;
	}

	/**
	 * Add bulk export action.
	 *
	 * Handles the template library bulk export action.
	 *
	 * Fired by `handle_bulk_actions-edit-elementor_library` filter.
	 *
	 * @since 1.6.0
	 * @access public
	 *
	 * @param string $redirect_to The redirect URL.
	 * @param string $action      The action being taken.
	 * @param array  $post_ids    The items to take the action on.
	 */
	public function admin_export_multiple_templates( $redirect_to, $action, $post_ids ) {
		if ( self::BULK_EXPORT_ACTION === $action ) {
			$result = $this->export_multiple_templates( $post_ids );

			// If you reach this line, the export failed
			// PHPCS - Not user input.
			wp_die( $result->get_error_message() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Print admin tabs.
	 *
	 * Used to output the template library tabs with their labels.
	 *
	 * Fired by `views_edit-elementor_library` filter.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param array $views An array of available list table views.
	 *
	 * @return array An updated array of available list table views.
	 */
	public function admin_print_tabs( $views ) {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce is not required to retrieve the value.
		$current_type = Utils::get_super_global_value( $_REQUEST, self::TAXONOMY_TYPE_SLUG ) ?? '';
		$active_class = $current_type ? '' : ' nav-tab-active';
		$current_tabs_group = $this->get_current_tab_group();

		$url_args = [
			'post_type' => self::CPT,
			'tabs_group' => $current_tabs_group,
		];

		$baseurl = add_query_arg( $url_args, admin_url( 'edit.php' ) );

		$filter = [
			'admin_tab_group' => $current_tabs_group,
		];
		$operator = 'and';

		if ( empty( $current_tabs_group ) ) {
			// Don't include 'not-supported' or other templates that don't set their `admin_tab_group`.
			$operator = 'NOT';
		}

		$doc_types = Plugin::$instance->documents->get_document_types( $filter, $operator );

		if ( 1 >= count( $doc_types ) ) {
			return $views;
		}

		?>
		<div id="elementor-template-library-tabs-wrapper" class="nav-tab-wrapper">
			<a class="nav-tab<?php echo esc_attr( $active_class ); ?>" href="<?php echo esc_url( $baseurl ); ?>">
				<?php
				$all_title = $this->get_library_title();
				if ( ! $all_title ) {
					$all_title = esc_html__( 'All', 'elementor' );
				}
				Utils::print_unescaped_internal_string( $all_title ); ?>
			</a>
			<?php
			foreach ( $doc_types as $type => $class_name ) :
				$active_class = '';

				if ( $current_type === $type ) {
					$active_class = ' nav-tab-active';
				}

				$type_url = esc_url( add_query_arg( self::TAXONOMY_TYPE_SLUG, $type, $baseurl ) );
				$type_label = $this->get_template_label_by_type( $type );
				Utils::print_unescaped_internal_string( "<a class='nav-tab{$active_class}' href='{$type_url}'>{$type_label}</a>" );
			endforeach;
			?>
		</div>
		<?php
		return $views;
	}

	/**
	 * Maybe render blank state.
	 *
	 * When the template library has no saved templates, display a blank admin page offering
	 * to create the very first template.
	 *
	 * Fired by `manage_posts_extra_tablenav` action.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @param string $which The location of the extra table nav markup: 'top' or 'bottom'.
	 * @param array  $args
	 */
	public function maybe_render_blank_state( $which, array $args = [] ) {
		global $post_type;

		$args = wp_parse_args( $args, [
			'cpt' => self::CPT,
			'post_type' => get_query_var( 'elementor_library_type' ),
		] );

		if ( $args['cpt'] !== $post_type || 'bottom' !== $which ) {
			return;
		}

		global $wp_list_table;

		$total_items = $wp_list_table->get_pagination_arg( 'total_items' );

		//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce is not required to retrieve the value.
		if ( ! empty( $total_items ) || ! empty( $_REQUEST['s'] ) ) {
			return;
		}

		$current_type = $args['post_type'];

		$document_types = Plugin::instance()->documents->get_document_types();

		if ( empty( $document_types[ $current_type ] ) ) {
			return;
		}

		// TODO: Better way to exclude widget type.
		if ( 'widget' === $current_type ) {
			return;
		}

		// TODO: This code maybe unreachable see if above `if ( empty( $document_types[ $current_type ] ) )`.
		if ( empty( $current_type ) ) {
			$counts = (array) wp_count_posts( self::CPT );
			unset( $counts['auto-draft'] );
			$count = array_sum( $counts );

			if ( 0 < $count ) {
				return;
			}

			$current_type = 'template';

			$args['additional_inline_style'] = '#elementor-template-library-tabs-wrapper {display: none;}';
		}

		$this->render_blank_state( $current_type, $args );
	}

	private function render_blank_state( $current_type, array $args = [] ) {
		$current_type_label = $this->get_template_label_by_type( $current_type );
		$inline_style = '#posts-filter .wp-list-table, #posts-filter .tablenav.top, .tablenav.bottom .actions, .wrap .subsubsub { display:none;}';

		$args = wp_parse_args( $args, [
			'additional_inline_style' => '',
			'href' => '',
			'description' => esc_html__( 'Add templates and reuse them across your website. Easily export and import them to any other project, for an optimized workflow.', 'elementor' ),
		] );
		$inline_style .= $args['additional_inline_style'];
		?>
		<style type="text/css"><?php Utils::print_unescaped_internal_string( $inline_style ); ?></style>
		<div class="elementor-template_library-blank_state">
			<?php $this->print_blank_state_template( $current_type_label, $args['href'], $args['description'] ); ?>
		</div>
		<?php
	}

	/**
	 * Print Blank State Template
	 *
	 * When the an entity (CPT, Taxonomy...etc) has no saved items, print a blank admin page offering
	 * to create the very first item.
	 *
	 * This method is public because it needs to be accessed from outside the Source_Local
	 *
	 * @since 3.1.0
	 * @access public
	 *
	 * @param string $current_type_label The Entity title.
	 * @param string $href The URL for the 'Add New' button.
	 * @param string $description The sub title describing the Entity (Post Type, Taxonomy, etc.).
	 */
	public function print_blank_state_template( $current_type_label, $href, $description ) {
		?>
			<div class="elementor-blank_state">
				<i class="eicon-folder"></i>
				<h3>
					<?php
					/* translators: %s: Template type label. */
					printf( esc_html__( 'Create Your First %s', 'elementor' ), esc_html( $current_type_label ) );
					?>
				</h3>
				<p><?php echo wp_kses_post( $description ); ?></p>
				<a id="elementor-template-library-add-new" class="elementor-button e-primary" href="<?php echo esc_url( $href ); ?>">
					<?php
					/* translators: %s: Template type label. */
					printf( esc_html__( 'Add New %s', 'elementor' ), esc_html( $current_type_label ) );
					?>
				</a>
			</div>
		<?php
	}

	/**
	 * Add filter by category.
	 *
	 * In the templates library, add a filter by Elementor library category.
	 *
	 * @access public
	 *
	 * @param string $post_type The post type slug.
	 */
	public function add_filter_by_category( $post_type ) {
		if ( self::CPT !== $post_type ) {
			return;
		}

		$all_items = get_taxonomy( self::TAXONOMY_CATEGORY_SLUG )->labels->all_items;
		$dropdown_options = [
			'show_option_all' => $all_items,
			'show_option_none' => $all_items,
			'hide_empty' => 0,
			'hierarchical' => 1,
			'show_count' => 0,
			'orderby' => 'name',
			'value_field' => 'slug',
			'taxonomy' => self::TAXONOMY_CATEGORY_SLUG,
			'name' => self::TAXONOMY_CATEGORY_SLUG,
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce is not required to retrieve the value.
			'selected' => Utils::get_super_global_value( $_GET, self::TAXONOMY_CATEGORY_SLUG ) ?? '',
		];

		printf(
			'<label class="screen-reader-text" for="%1$s">%2$s</label>',
			esc_attr( self::TAXONOMY_CATEGORY_SLUG ),
			esc_html_x( 'Filter by category', 'Template Library', 'elementor' )
		);
		wp_dropdown_categories( $dropdown_options );
	}

	/**
	 * Import single template.
	 *
	 * Import template from a file to the database.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param string $file_path File name.
	 *
	 * @return \WP_Error|int|array Local template array, or template ID, or
	 *                             `WP_Error`.
	 */
	private function import_single_template( $file_path ) {
		$data = $this->prepare_import_template_data( $file_path );

		if ( is_wp_error( $data ) ) {
			return $data;
		}

		if ( empty( $data ) ) {
			return new \WP_Error( 'file_error', 'Invalid File' );
		}

		$template_id = $this->save_item( [
			'content' => $data['content'],
			'title' => $data['title'],
			'type' => $data['type'],
			'page_settings' => $data['page_settings'],
		] );

		if ( is_wp_error( $template_id ) ) {
			return $template_id;
		}

		return $this->get_item( $template_id );
	}

	/**
	 * Prepare template to export.
	 *
	 * Retrieve the relevant template data and return them as an array.
	 *
	 * @since 1.6.0
	 * @access private
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return \WP_Error|array Exported template data.
	 */
	private function prepare_template_export( $template_id ) {
		$document = Plugin::$instance->documents->get( $template_id );

		$template_data = $document->get_export_data();

		if ( empty( $template_data['content'] ) ) {
			return new \WP_Error( 'empty_template', 'The template is empty' );
		}

		$content = apply_filters(
			'elementor/template_library/sources/local/export/elements',
			$template_data['content']
		);

		$export_data = [
			'content' => $content,
			'page_settings' => $template_data['settings'],
			'version' => DB::DB_VERSION,
			'title' => $document->get_main_post()->post_title,
			'type' => self::get_template_type( $template_id ),
		];

		return [
			'name' => 'elementor-' . $template_id . '-' . gmdate( 'Y-m-d' ) . '.json',
			'content' => wp_json_encode( $export_data ),
		];
	}

	/**
	 * Get template label by type.
	 *
	 * Retrieve the template label for any given template type.
	 *
	 * @since 2.0.0
	 * @access private
	 *
	 * @param string $template_type Template type.
	 *
	 * @return string Template label.
	 */
	private function get_template_label_by_type( $template_type ) {
		$document_types = Plugin::instance()->documents->get_document_types();

		if ( isset( $document_types[ $template_type ] ) ) {
			$template_label = call_user_func( [ $document_types[ $template_type ], 'get_title' ] );
		} else {
			$template_label = ucwords( str_replace( [ '_', '-' ], ' ', $template_type ) );
		}

		/**
		 * Template label by template type.
		 *
		 * Filters the template label by template type in the template library .
		 *
		 * @since 2.0.0
		 *
		 * @param string $template_label Template label.
		 * @param string $template_type  Template type.
		 */
		$template_label = apply_filters( 'elementor/template-library/get_template_label_by_type', $template_label, $template_type );

		return $template_label;
	}

	/**
	 * Filter template types in admin query.
	 *
	 * Update the template types in the main admin query.
	 *
	 * Fired by `parse_query` action.
	 *
	 * @since 2.4.0
	 * @access public
	 *
	 * @param \WP_Query $query The `WP_Query` instance.
	 */
	public function admin_query_filter_types( \WP_Query $query ) {
		if ( ! $this->is_current_screen() || ! empty( $query->query_vars['meta_key'] ) ) {
			return;
		}

		$current_tabs_group = $this->get_current_tab_group();

		if ( isset( $query->query_vars[ self::TAXONOMY_CATEGORY_SLUG ] ) && '-1' === $query->query_vars[ self::TAXONOMY_CATEGORY_SLUG ] ) {
			unset( $query->query_vars[ self::TAXONOMY_CATEGORY_SLUG ] );
		}

		if ( empty( $current_tabs_group ) ) {
			return;
		}

		$doc_types = Plugin::$instance->documents->get_document_types( [
			'admin_tab_group' => $current_tabs_group,
		] );

		$query->query_vars['meta_key'] = Document::TYPE_META_KEY;
		$query->query_vars['meta_value'] = array_keys( $doc_types );
	}

	/**
	 * Add template library actions.
	 *
	 * Register filters and actions for the template library.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function add_actions() {
		if ( is_admin() ) {
			add_action( 'elementor/admin/menu/register', function ( Admin_Menu_Manager $admin_menu ) {
				$this->register_admin_menu( $admin_menu );
			}, static::ADMIN_MENU_PRIORITY );

			add_action( 'elementor/admin/menu/register', function ( Admin_Menu_Manager $admin_menu ) {
				$this->admin_menu_reorder( $admin_menu );
			}, 800 );

			add_action( 'elementor/admin/menu/after_register', function () {
				$this->admin_menu_set_current();
			} );

			add_filter( 'admin_title', [ $this, 'admin_title' ], 10, 2 );
			add_action( 'all_admin_notices', [ $this, 'replace_admin_heading' ] );
			add_filter( 'post_row_actions', [ $this, 'post_row_actions' ], 10, 2 );
			add_action( 'admin_footer', [ $this, 'admin_import_template_form' ] );
			add_action( 'save_post', [ $this, 'on_save_post' ], 10, 2 );
			add_filter( 'display_post_states', [ $this, 'remove_elementor_post_state_from_library' ], 11, 2 );

			add_action( 'parse_query', [ $this, 'admin_query_filter_types' ] );

			// Template filter by category.
			add_action( 'restrict_manage_posts', [ $this, 'add_filter_by_category' ] );

			// Template type column.
			add_action( 'manage_' . self::CPT . '_posts_columns', [ $this, 'admin_columns_headers' ] );
			add_action( 'manage_' . self::CPT . '_posts_custom_column', [ $this, 'admin_columns_content' ], 10, 2 );

			// Template library bulk actions.
			add_filter( 'bulk_actions-edit-elementor_library', [ $this, 'admin_add_bulk_export_action' ] );
			add_filter( 'handle_bulk_actions-edit-elementor_library', [ $this, 'admin_export_multiple_templates' ], 10, 3 );

			// Print template library tabs.
			add_filter( 'views_edit-' . self::CPT, [ $this, 'admin_print_tabs' ] );

			// Show blank state.
			add_action( 'manage_posts_extra_tablenav', [ $this, 'maybe_render_blank_state' ] );
		}

		add_action( 'elementor/document/after_save', [ $this, 'on_template_update' ], 10, 2 );

		add_action( 'template_redirect', [ $this, 'block_template_frontend' ] );

		// Remove elementor library templates from WP Sitemap
		add_filter(
			'wp_sitemaps_post_types',
			function( $post_types ) {
				return $this->remove_elementor_cpt_from_sitemap( $post_types );
			}
		);
	}

	public function on_template_update( \Elementor\Core\Base\Document $document, array $data ) {
		if ( ! empty( $data['post_title'] ) ) {
			wp_update_post( [
				'ID' => $document->get_main_id(),
				'post_title' => $data['post_title'],
			] );
		}
	}

	/**
	 * @since 2.0.6
	 * @access public
	 */
	public function admin_columns_content( $column_name, $post_id ) {
		if ( 'elementor_library_type' === $column_name ) {
			/** @var Document $document */
			$document = Plugin::$instance->documents->get( $post_id );

			if ( $document && $document instanceof Library_Document ) {
				$document->print_admin_column_type();
			}
		}
	}

	/**
	 * @since 2.0.6
	 * @access public
	 */
	public function admin_columns_headers( $posts_columns ) {
		// Replace original column that bind to the taxonomy - with another column.
		unset( $posts_columns['taxonomy-elementor_library_type'] );

		$offset = 2;

		$posts_columns = array_slice( $posts_columns, 0, $offset, true ) + [
			'elementor_library_type' => esc_html__( 'Type', 'elementor' ),
		] + array_slice( $posts_columns, $offset, null, true );

		return $posts_columns;
	}

	public function get_current_tab_group() {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is not required here.
		$current_tabs_group = Utils::get_super_global_value( $_REQUEST, 'tabs_group' ) ?? '';
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce verification is not required here.
		$type_slug = Utils::get_super_global_value( $_REQUEST, self::TAXONOMY_TYPE_SLUG );

		if ( $type_slug ) {
			$doc_type = Plugin::$instance->documents->get_document_type( $type_slug, '' );
			if ( $doc_type ) {
				$current_tabs_group = $doc_type::get_property( 'admin_tab_group' );
			}
		}
		return $current_tabs_group;
	}

	private function get_library_title() {
		$title = '';

		if ( $this->is_current_screen() ) {
			$current_tab_group = $this->get_current_tab_group();

			if ( $current_tab_group ) {
				$titles = [
					'library' => esc_html__( 'Saved Templates', 'elementor' ),
					'theme' => esc_html__( 'Theme Builder', 'elementor' ),
					'popup' => esc_html__( 'Popups', 'elementor' ),
				];

				if ( ! empty( $titles[ $current_tab_group ] ) ) {
					$title = $titles[ $current_tab_group ];
				}
			}
		}

		return $title;
	}

	private function is_current_screen() {
		global $pagenow, $typenow;

		return 'edit.php' === $pagenow && self::CPT === $typenow;
	}

	/**
	 * @param array $post_types
	 *
	 * @return array
	 */
	private function remove_elementor_cpt_from_sitemap( array $post_types ) {
		unset( $post_types[ self::CPT ] );

		return $post_types;
	}

	private function avoid_rest_access_for_non_admins(): void {
		add_filter( 'rest_pre_dispatch', function ( $value, \WP_REST_Server $server, \WP_REST_Request $request ) {
			if ( strpos( $request->get_route(), self::CPT ) !== false ) {
				if ( ! current_user_can( 'manage_options' ) ) {
					return new \WP_Error( 'rest_forbidden', esc_html__( 'Sorry, you are not allowed to do that.', 'elementor' ), [ 'status' => rest_authorization_required_code() ] );
				}
			}

			return $value;
		}, 10, 3 );
	}

	public function save_bulk_items( array $args = [] ) {
		$items = [];

		foreach ( $args as $item ) {
			$items[] = $this->save_item( [
				'content' => $item['content'],
				'title' => $item['title'],
				'type' => $item['type'],
				'page_settings' => $item['page_settings'],
			] );
		}

		return $items;
	}

	/**
	 * Template library local source constructor.
	 *
	 * Initializing the template library local source base by registering custom
	 * template data and running custom actions.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		parent::__construct();

		$this->add_actions();
	}
}
