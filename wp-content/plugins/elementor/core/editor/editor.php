<?php
namespace Elementor\Core\Editor;

use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Core\Common\Modules\Ajax\Module;
use Elementor\Core\Debug\Loading_Inspection_Manager;
use Elementor\Core\Editor\Loader\Editor_Loader_Factory;
use Elementor\Core\Editor\Loader\Editor_Loader_Interface;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Plugin;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils;
use Elementor\Core\Editor\Data;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor editor.
 *
 * Elementor editor handler class is responsible for initializing Elementor
 * editor and register all the actions needed to display the editor.
 *
 * @since 1.0.0
 */
class Editor {

	/**
	 * User capability required to access Elementor editor.
	 */
	const EDITING_CAPABILITY = 'edit_posts';

	/**
	 * Post ID.
	 *
	 * Holds the ID of the current post being edited.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var int Post ID.
	 */
	private $post_id;

	/**
	 * Whether the edit mode is active.
	 *
	 * Used to determine whether we are in edit mode.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var bool Whether the edit mode is active.
	 */
	private $is_edit_mode;

	/**
	 * @var Notice_Bar
	 */
	public $notice_bar;

	/**
	 * @var Promotion
	 */
	public $promotion;

	/**
	 * @var Editor_Loader_Interface
	 */
	private $loader;

	/**
	 * Init.
	 *
	 * Initialize Elementor editor. Registers all needed actions to run Elementor,
	 * removes conflicting actions etc.
	 *
	 * Fired by `admin_action_elementor` action.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $to_die Optional. Whether to die at the end. Default is `true`.
	 */
	public function init( $to_die = true ) {
		if ( empty( $_REQUEST['post'] ) ) {
			return;
		}

		$this->set_post_id( absint( $_REQUEST['post'] ) );

		if ( ! $this->is_edit_mode( $this->post_id ) ) {
			return;
		}

		// BC: From 2.9.0, the editor shouldn't handle the global post / current document.
		// Use requested id and not the global in order to avoid conflicts with plugins that changes the global post.
		query_posts( [
			'p' => $this->post_id,
			'post_type' => get_post_type( $this->post_id ),
		] );

		Plugin::$instance->db->switch_to_post( $this->post_id );

		$document = Plugin::$instance->documents->get( $this->post_id );

		Plugin::$instance->documents->switch_to_document( $document );

		// Change mode to Builder
		$document->set_is_built_with_elementor( true );

		// End BC.

		Loading_Inspection_Manager::instance()->register_inspections();

		// Send MIME Type header like WP admin-header.
		@header( 'Content-Type: ' . get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' ) );

		add_filter( 'show_admin_bar', '__return_false' );

		// Remove all WordPress actions
		remove_all_actions( 'wp_head' );
		remove_all_actions( 'wp_print_styles' );
		remove_all_actions( 'wp_print_head_scripts' );
		remove_all_actions( 'wp_footer' );

		// Handle `wp_head`
		add_action( 'wp_head', 'wp_enqueue_scripts', 1 );
		add_action( 'wp_head', 'wp_print_styles', 8 );
		add_action( 'wp_head', 'wp_print_head_scripts', 9 );
		add_action( 'wp_head', 'wp_site_icon' );
		add_action( 'wp_head', [ $this, 'editor_head_trigger' ], 30 );

		// Handle `wp_footer`
		add_action( 'wp_footer', 'wp_print_footer_scripts', 20 );
		add_action( 'wp_footer', 'wp_auth_check_html', 30 );
		add_action( 'wp_footer', [ $this, 'wp_footer' ] );

		// Handle `wp_enqueue_scripts`
		remove_all_actions( 'wp_enqueue_scripts' );

		// Also remove all scripts hooked into after_wp_tiny_mce.
		remove_all_actions( 'after_wp_tiny_mce' );

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ], 999999 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], 999999 );

		// Setup default heartbeat options
		add_filter( 'heartbeat_settings', function( $settings ) {
			$settings['interval'] = 15;
			return $settings;
		} );

		// Tell to WP Cache plugins do not cache this request.
		Utils::do_not_cache();

		do_action( 'elementor/editor/init' );

		$this->get_loader()->print_root_template();

		// From the action it's an empty string, from tests its `false`
		if ( false !== $to_die ) {
			die;
		}
	}

	/**
	 * Retrieve post ID.
	 *
	 * Get the ID of the current post.
	 *
	 * @since 1.8.0
	 * @access public
	 *
	 * @return int Post ID.
	 */
	public function get_post_id() {
		return $this->post_id;
	}

	/**
	 * Redirect to new URL.
	 *
	 * Used as a fallback function for the old URL structure of Elementor page
	 * edit URL.
	 *
	 * Fired by `template_redirect` action.
	 *
	 * @since 1.6.0
	 * @access public
	 */
	public function redirect_to_new_url() {
		if ( ! isset( $_GET['elementor'] ) ) {
			return;
		}

		$document = Plugin::$instance->documents->get( get_the_ID() );

		if ( ! $document ) {
			wp_die( esc_html__( 'Document not found.', 'elementor' ) );
		}

		if ( ! $document->is_editable_by_current_user() || ! $document->is_built_with_elementor() ) {
			return;
		}

		wp_safe_redirect( $document->get_edit_url() );
		die;
	}

	/**
	 * Whether the edit mode is active.
	 *
	 * Used to determine whether we are in the edit mode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id Optional. Post ID. Default is `null`, the current
	 *                     post ID.
	 *
	 * @return bool Whether the edit mode is active.
	 */
	public function is_edit_mode( $post_id = null ) {
		if ( null !== $this->is_edit_mode ) {
			return $this->is_edit_mode;
		}

		if ( empty( $post_id ) ) {
			$post_id = $this->post_id;
		}

		$document = Plugin::$instance->documents->get( $post_id );

		if ( ! $document || ! $document->is_editable_by_current_user() ) {
			return false;
		}

		/** @var Module ajax */
		$ajax_data = Plugin::$instance->common->get_component( 'ajax' )->get_current_action_data();

		if ( ! empty( $ajax_data ) && 'get_document_config' === $ajax_data['action'] ) {
			return true;
		}

		// Ajax request as Editor mode
		$actions = [
			'elementor',

			// Templates
			'elementor_get_templates',
			'elementor_save_template',
			'elementor_get_template',
			'elementor_delete_template',
			'elementor_import_template',
			'elementor_library_direct_actions',
		];

		if ( isset( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], $actions ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Lock post.
	 *
	 * Mark the post as currently being edited by the current user.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id The ID of the post being edited.
	 */
	public function lock_post( $post_id ) {
		if ( ! function_exists( 'wp_set_post_lock' ) ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}

		wp_set_post_lock( $post_id );
	}

	/**
	 * Get locked user.
	 *
	 * Check what user is currently editing the post.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id The ID of the post being edited.
	 *
	 * @return \WP_User|false User information or false if the post is not locked.
	 */
	public function get_locked_user( $post_id ) {
		if ( ! function_exists( 'wp_check_post_lock' ) ) {
			require_once ABSPATH . 'wp-admin/includes/post.php';
		}

		$locked_user = wp_check_post_lock( $post_id );
		if ( ! $locked_user ) {
			return false;
		}

		return get_user_by( 'id', $locked_user );
	}

	/**
	 * NOTICE: This method not in use, it's here for backward compatibility.
	 *
	 * Print Editor Template.
	 *
	 * Include the wrapper template of the editor.
	 *
	 * @since 2.2.0
	 * @access public
	 */
	public function print_editor_template() {
		include ELEMENTOR_PATH . 'includes/editor-templates/editor-wrapper.php';
	}

	/**
	 * Enqueue scripts.
	 *
	 * Registers all the editor scripts and enqueues them.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_scripts() {
		remove_action( 'wp_enqueue_scripts', [ $this, __FUNCTION__ ], 999999 );

		global $wp_styles, $wp_scripts;

		// Reset global variable
		$wp_styles = new \WP_Styles(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$wp_scripts = new \WP_Scripts(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		$this->get_loader()->register_scripts();

		/**
		 * Before editor enqueue scripts.
		 *
		 * Fires before Elementor editor scripts are enqueued.
		 *
		 * @since 1.0.0
		 */
		do_action( 'elementor/editor/before_enqueue_scripts' );

		// Tweak for WP Admin menu icons
		wp_print_styles( 'editor-buttons' );

		$this->get_loader()->enqueue_scripts();

		Plugin::$instance->controls_manager->enqueue_control_scripts();

		/**
		 * After editor enqueue scripts.
		 *
		 * Fires after Elementor editor scripts are enqueued.
		 *
		 * @since 1.0.0
		 */
		do_action( 'elementor/editor/after_enqueue_scripts' );
	}

	/**
	 * Enqueue styles.
	 *
	 * Registers all the editor styles and enqueues them.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_styles() {
		/**
		 * Before editor enqueue styles.
		 *
		 * Fires before Elementor editor styles are enqueued.
		 *
		 * @since 1.0.0
		 */
		do_action( 'elementor/editor/before_enqueue_styles' );

		$this->get_loader()->register_styles();
		$this->get_loader()->enqueue_styles();

		$this->enqueue_theme_ui_styles();

		$breakpoints = Plugin::$instance->breakpoints->get_breakpoints();

		// The two breakpoints under 'tablet' need to be checked for values.
		if ( $breakpoints[ Breakpoints_Manager::BREAKPOINT_KEY_MOBILE ]->is_custom() || $breakpoints[ Breakpoints_Manager::BREAKPOINT_KEY_MOBILE_EXTRA ]->is_enabled() ) {
			wp_add_inline_style(
				'elementor-editor',
				'.elementor-device-tablet #elementor-preview-responsive-wrapper { width: ' . Plugin::$instance->breakpoints->get_device_min_breakpoint( Breakpoints_Manager::BREAKPOINT_KEY_TABLET ) . 'px; }'
			);
		}

		/**
		 * After editor enqueue styles.
		 *
		 * Fires after Elementor editor styles are enqueued.
		 *
		 * @since 1.0.0
		 */
		do_action( 'elementor/editor/after_enqueue_styles' );
	}

	private function enqueue_theme_ui_styles() {
		$ui_theme_selected = SettingsManager::get_settings_managers( 'editorPreferences' )->get_model()->get_settings( 'ui_theme' );

		$ui_themes = [
			'light',
			'dark',
		];

		if ( 'auto' === $ui_theme_selected || ! in_array( $ui_theme_selected, $ui_themes, true ) ) {
			$ui_light_theme_media_queries = '(prefers-color-scheme: light)';
			$ui_dark_theme_media_queries = '(prefers-color-scheme: dark)';
		} else {
			$ui_light_theme_media_queries = 'none';
			$ui_dark_theme_media_queries = 'none';

			if ( 'light' === $ui_theme_selected ) {
				$ui_light_theme_media_queries = 'all';
			} elseif ( 'dark' === $ui_theme_selected ) {
				$ui_dark_theme_media_queries = 'all';
			}
		}

		$this->enqueue_theme_ui( 'light', $ui_light_theme_media_queries );
		$this->enqueue_theme_ui( 'dark', $ui_dark_theme_media_queries );
	}

	private function enqueue_theme_ui( $ui_theme, $ui_theme_media_queries = 'all' ) {
		$suffix = Utils::is_script_debug() ? '' : '.min';

		wp_enqueue_style(
			'e-theme-ui-' . $ui_theme,
			ELEMENTOR_ASSETS_URL . 'css/theme-' . $ui_theme . $suffix . '.css',
			[],
			ELEMENTOR_VERSION,
			$ui_theme_media_queries
		);
	}

	/**
	 * Editor head trigger.
	 *
	 * Fires the 'elementor/editor/wp_head' action in the head tag in Elementor
	 * editor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function editor_head_trigger() {
		/**
		 * Elementor editor head.
		 *
		 * Fires on Elementor editor head tag.
		 *
		 * Used to prints scripts or any other data in the head tag.
		 *
		 * @since 1.0.0
		 */
		do_action( 'elementor/editor/wp_head' );
	}

	/**
	 * WP footer.
	 *
	 * Prints Elementor editor with all the editor templates, and render controls,
	 * widgets and content elements.
	 *
	 * Fired by `wp_footer` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function wp_footer() {
		$plugin = Plugin::$instance;

		$plugin->controls_manager->render_controls();
		$plugin->widgets_manager->render_widgets_content();
		$plugin->elements_manager->render_elements_content();

		$plugin->dynamic_tags->print_templates();

		$this->get_loader()->register_additional_templates();

		/**
		 * Elementor editor footer.
		 *
		 * Fires on Elementor editor before closing the body tag.
		 *
		 * Used to prints scripts or any other HTML before closing the body tag.
		 *
		 * @since 1.0.0
		 */
		do_action( 'elementor/editor/footer' );
	}

	/**
	 * Set edit mode.
	 *
	 * Used to update the edit mode.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param bool $edit_mode Whether the edit mode is active.
	 */
	public function set_edit_mode( $edit_mode ) {
		$this->is_edit_mode = $edit_mode;
	}

	/**
	 * Editor constructor.
	 *
	 * Initializing Elementor editor and redirect from old URL structure of
	 * Elementor editor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		Plugin::$instance->data_manager_v2->register_controller( new Data\Globals\Controller() );

		$this->notice_bar = new Notice_Bar();
		$this->promotion = new Promotion();

		add_action( 'admin_action_elementor', [ $this, 'init' ] );
		add_action( 'template_redirect', [ $this, 'redirect_to_new_url' ] );

		// Handle autocomplete feature for URL control.
		add_filter( 'wp_link_query_args', [ $this, 'filter_wp_link_query_args' ] );
		add_filter( 'wp_link_query', [ $this, 'filter_wp_link_query' ] );
	}

	/**
	 * @since 2.2.0
	 * @access public
	 */
	public function filter_wp_link_query_args( $query ) {
		$library_cpt_key = array_search( Source_Local::CPT, $query['post_type'], true );
		if ( false !== $library_cpt_key ) {
			unset( $query['post_type'][ $library_cpt_key ] );
		}

		return $query;
	}

	/**
	 * @since 2.2.0
	 * @access public
	 */
	public function filter_wp_link_query( $results ) {

		// PHPCS - The user data is not used.
		if ( isset( $_POST['editor'] ) && 'elementor' === $_POST['editor'] ) {  // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$post_type_object = get_post_type_object( 'post' );
			$post_label = $post_type_object->labels->singular_name;

			foreach ( $results as & $result ) {
				if ( 'post' === get_post_type( $result['ID'] ) ) {
					$result['info'] = $post_label;
				}
			}
		}

		return $results;
	}

	public function set_post_id( $post_id ) {
		$this->post_id = $post_id;
	}

	/**
	 * Get loader.
	 *
	 * @return Editor_Loader_Interface
	 */
	private function get_loader() {
		if ( ! $this->loader ) {
			$this->loader = Editor_Loader_Factory::create();

			$this->loader->init();
		}

		return $this->loader;
	}

	/**
	 * Get elements presets.
	 *
	 * @return array
	 */
	public function get_elements_presets() {
		$element_types = Plugin::$instance->elements_manager->get_element_types();
		$presets = [];

		foreach ( $element_types as $el_type => $element ) {
			$this->check_element_for_presets( $element, $el_type, $presets );
		}

		return $presets;
	}

	/**
	 * @return void
	 */
	private function check_element_for_presets( $element, $el_type, &$presets ) {
		$element_presets = $element->get_panel_presets();

		if ( empty( $element_presets ) ) {
			return;
		}

		foreach ( $element_presets as $key => $preset ) {
			$this->maybe_add_preset( $el_type, $preset, $key, $presets );
		}
	}

	/**
	 * @return void
	 */
	private function maybe_add_preset( $el_type, $preset, $key, &$presets ) {
		if ( $this->is_valid_preset( $el_type, $preset ) ) {
			$presets[ $key ] = $preset;
		}
	}

	/**
	 * @return boolean
	 */
	private function is_valid_preset( $el_type, $preset ) {
		return isset( $preset['replacements']['custom']['originalWidget'] )
			&& $el_type === $preset['replacements']['custom']['originalWidget'];
	}
}
