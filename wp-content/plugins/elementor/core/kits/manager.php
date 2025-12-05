<?php
namespace Elementor\Core\Kits;

use Elementor\Core\Base\Document;
use Elementor\Core\Kits\Controls\Repeater;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Plugin;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Core\Files\CSS\Post_Preview;
use Elementor\Core\Documents_Manager;
use Elementor\Core\Kits\Documents\Kit;
use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Manager {

	const OPTION_ACTIVE = 'elementor_active_kit';

	const OPTION_PREVIOUS = 'elementor_previous_kit';

	const E_HASH_COMMAND_OPEN_SITE_SETTINGS = 'e:run:panel/global/open';

	private $should_skip_trash_kit_confirmation = false;

	public function get_active_id() {
		return get_option( self::OPTION_ACTIVE );
	}

	public function get_previous_id() {
		return get_option( self::OPTION_PREVIOUS );
	}

	public function get_kit( $kit_id ) {
		$kit = Plugin::$instance->documents->get( $kit_id );

		if ( ! $this->is_valid_kit( $kit ) ) {
			return $this->get_empty_kit_instance();
		}

		return $kit;
	}

	public function get_active_kit() {
		return $this->get_kit( $this->get_active_id() );
	}

	public function get_active_kit_for_frontend() {
		$kit = Plugin::$instance->documents->get_doc_for_frontend( $this->get_active_id() );

		if ( ! $this->is_valid_kit( $kit ) ) {
			return $this->get_empty_kit_instance();
		}

		return $kit;
	}

	/**
	 * @param $kit
	 *
	 * @return bool
	 */
	private function is_valid_kit( $kit ) {
		return $kit && $kit instanceof Kit && 'trash' !== $kit->get_main_post()->post_status;
	}

	/**
	 * Returns an empty kit for situation when there is no kit in the site.
	 *
	 * @return Kit
	 * @throws \Exception If the kit instance cannot be created.
	 */
	private function get_empty_kit_instance() {
		return new Kit( [
			'settings' => [],
			'post_id' => 0,
		] );
	}

	/**
	 * Checks if specific post is a kit.
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function is_kit( $post_id ) {
		$document = Plugin::$instance->documents->get( $post_id );

		return $document && $document instanceof Kit && ! $document->is_revision();
	}


	/**
	 * Init kit controls.
	 *
	 * A temp solution in order to avoid init kit group control from within another group control.
	 *
	 * After moving the `default_font` to the kit, the Typography group control cause initialize the kit controls at: https://github.com/elementor/elementor/blob/e6e1db9eddef7e3c1a5b2ba0c2338e2af2a3bfe3/includes/controls/groups/typography.php#L91
	 * and because the group control is a singleton, its args are changed to the last kit group control.
	 */
	public function init_kit_controls() {
		$this->get_active_kit_for_frontend()->get_settings();
	}

	public function get_current_settings( $setting = null ) {
		$kit = $this->get_active_kit_for_frontend();

		if ( ! $kit ) {
			return '';
		}

		return $kit->get_settings( $setting );
	}

	public function create( array $kit_data = [], array $kit_meta_data = [] ) {
		$default_kit_data = [
			'post_status' => 'publish',
		];

		$kit_data = array_merge( $default_kit_data, $kit_data );

		$kit_data['post_type'] = Source_Local::CPT;

		$kit = Plugin::$instance->documents->create( 'kit', $kit_data, $kit_meta_data );

		if ( isset( $kit_data['settings'] ) ) {
			$kit->save( [ 'settings' => $kit_data['settings'] ] );
		}

		return $kit->get_id();
	}

	public function create_new_kit( $kit_name = '', $settings = [], $active = true ) {
		$kit_name = $kit_name ? $kit_name : esc_html__( 'Custom', 'elementor' );

		$id = $this->create( [
			'post_title' => $kit_name,
			'settings' => $settings,
		] );

		if ( $active ) {
			update_option( self::OPTION_PREVIOUS, $this->get_active_id() );
			update_option( self::OPTION_ACTIVE, $id );
		}

		return $id;
	}

	public function create_default() {
		return $this->create( [
			'post_title' => esc_html__( 'Default Kit', 'elementor' ),
		] );
	}

	/**
	 * Create a default kit if needed.
	 *
	 * This action runs on activation hook, all the Plugin components do not exists and
	 * the Document manager and Kits manager instances cannot be used.
	 *
	 * @return int|void|\WP_Error
	 */
	public static function create_default_kit() {
		if ( get_option( self::OPTION_ACTIVE ) ) {
			return;
		}

		$id = wp_insert_post( [
			'post_title' => esc_html__( 'Default Kit', 'elementor' ),
			'post_type' => Source_Local::CPT,
			'post_status' => 'publish',
			'meta_input' => [
				'_elementor_edit_mode' => 'builder',
				Document::TYPE_META_KEY => 'kit',
			],
		] );

		update_option( self::OPTION_ACTIVE, $id );

		return $id;
	}

	/**
	 * @param $imported_kit_id int The id of the imported kit that should be deleted.
	 * @param $active_kit_id int The id of the kit that should set as 'active_kit' after the deletion.
	 * @param $previous_kit_id int The id of the kit that should set as 'previous_kit' after the deletion.
	 * @return void
	 */
	public function revert( int $imported_kit_id, int $active_kit_id, int $previous_kit_id ) {
		// If the kit that should set as active is not a valid kit then abort the revert.
		if ( ! $this->is_kit( $active_kit_id ) ) {
			return;
		}

		// This a hacky solution to avoid from the revert process to be interrupted by the `trash_kit_confirmation`.
		$this->should_skip_trash_kit_confirmation = true;

		$kit = $this->get_kit( $imported_kit_id );
		$kit->force_delete();

		$this->should_skip_trash_kit_confirmation = false;

		update_option( self::OPTION_ACTIVE, $active_kit_id );

		if ( $this->is_kit( $previous_kit_id ) ) {
			update_option( self::OPTION_PREVIOUS, $previous_kit_id );
		}
	}

	/**
	 * @param Documents_Manager $documents_manager
	 */
	public function register_document( $documents_manager ) {
		$documents_manager->register_document_type( 'kit', Kit::get_class_full_name() );
	}

	public function localize_settings( $settings ) {
		$kit = $this->get_active_kit();
		$kit_controls = $kit->get_controls();
		$design_system_controls = [
			'colors' => $kit_controls['system_colors']['fields'],
			'typography' => $kit_controls['system_typography']['fields'],
		];

		$settings = array_replace_recursive( $settings, [
			'kit_id' => $kit->get_main_id(),
			'kit_config' => [
				'typography_prefix' => Global_Typography::TYPOGRAPHY_GROUP_PREFIX,
				'design_system_controls' => $design_system_controls,
			],
			'user' => [
				'can_edit_kit' => $kit->is_editable_by_current_user(),
			],
		] );

		return $settings;
	}

	public function preview_enqueue_styles() {
		$kit = $this->get_kit_for_frontend();

		if ( $kit ) {
			// On preview, the global style is not enqueued.
			$this->frontend_before_enqueue_styles();

			Plugin::$instance->frontend->print_fonts_links();
		}
	}

	public function frontend_before_enqueue_styles() {
		$kit = $this->get_kit_for_frontend();

		if ( $kit ) {
			if ( $kit->is_autosave() ) {
				$css_file = Post_Preview::create( $kit->get_id() );
			} else {
				$css_file = Post_CSS::create( $kit->get_id() );
			}

			$css_file->enqueue();
		}
	}

	public function render_panel_html() {
		require __DIR__ . '/views/panel.php';
	}

	public function get_kit_for_frontend() {
		$kit = false;
		$active_kit = $this->get_active_kit();
		$is_kit_preview = is_preview() && isset( $_GET['preview_id'] ) && $active_kit->get_main_id() === (int) $_GET['preview_id'];

		if ( $is_kit_preview ) {
			$kit = Plugin::$instance->documents->get_doc_or_auto_save( $active_kit->get_main_id(), get_current_user_id() );
		} elseif ( null !== $active_kit->get_main_post() && 'publish' === $active_kit->get_main_post()->post_status ) {
			$kit = $active_kit;
		}

		return $kit;
	}

	public function update_kit_settings_based_on_option( $key, $value ) {
		/** @var Kit $active_kit */
		$active_kit = $this->get_active_kit();

		if ( $active_kit->is_saving() ) {
			return;
		}

		$active_kit->update_settings( [ $key => $value ] );
	}

	/**
	 * Map Scheme To Global
	 *
	 * Convert a given scheme value to its corresponding default global value
	 *
	 * @param string $type 'color'/'typography'.
	 * @param $value
	 * @return mixed
	 */
	private function map_scheme_to_global( $type, $value ) {
		$schemes_to_globals_map = [
			'color' => [
				'1' => Global_Colors::COLOR_PRIMARY,
				'2' => Global_Colors::COLOR_SECONDARY,
				'3' => Global_Colors::COLOR_TEXT,
				'4' => Global_Colors::COLOR_ACCENT,
			],
			'typography' => [
				'1' => Global_Typography::TYPOGRAPHY_PRIMARY,
				'2' => Global_Typography::TYPOGRAPHY_SECONDARY,
				'3' => Global_Typography::TYPOGRAPHY_TEXT,
				'4' => Global_Typography::TYPOGRAPHY_ACCENT,
			],
		];

		return $schemes_to_globals_map[ $type ][ $value ];
	}

	/**
	 * Convert Scheme to Default Global
	 *
	 * If a control has a scheme property, convert it to a default Global.
	 *
	 * @param $scheme - Control scheme property
	 * @return array - Control/group control args
	 * @since 3.0.0
	 * @access public
	 */
	public function convert_scheme_to_global( $scheme ) {
		if ( isset( $scheme['type'] ) && isset( $scheme['value'] ) ) {
			// _deprecated_argument( $args['scheme'], '3.0.0', 'Schemes are now deprecated - use $args[\'global\'] instead.' );
			return $this->map_scheme_to_global( $scheme['type'], $scheme['value'] );
		}

		// Typography control 'scheme' properties usually only include the string with the typography value ('1'-'4').
		return $this->map_scheme_to_global( 'typography', $scheme );
	}

	public function register_controls() {
		$controls_manager = Plugin::$instance->controls_manager;

		$controls_manager->register( new Repeater() );
	}

	public function is_custom_colors_enabled() {
		return ! get_option( 'elementor_disable_color_schemes' );
	}

	public function is_custom_typography_enabled() {
		return ! get_option( 'elementor_disable_typography_schemes' );
	}

	/**
	 * Add kit wrapper body class.
	 *
	 * It should be added even for non Elementor pages,
	 * in order to support embedded templates.
	 */
	private function add_body_class() {
		$kit = $this->get_kit_for_frontend();

		if ( $kit ) {
			Plugin::$instance->frontend->add_body_class( 'elementor-kit-' . $kit->get_main_id() );
		}
	}

	/**
	 * Send a confirm message before move a kit to trash, or if delete permanently not for trash.
	 *
	 * @param $post_id
	 * @param bool $is_permanently_delete
	 */
	private function before_delete_kit( $post_id, $is_permanently_delete = false ) {
		if ( $this->should_skip_trash_kit_confirmation ) {
			return;
		}

		$document = Plugin::$instance->documents->get( $post_id );

		if (
			! $document ||
			! $this->is_kit( $post_id ) ||
			isset( $_GET['force_delete_kit'] ) ||  // phpcs:ignore -- nonce validation is not require here.
			( $is_permanently_delete && $document->is_trash() )
		) {
			return;
		}

		ob_start();
		require __DIR__ . '/views/trash-kit-confirmation.php';

		$confirmation_content = ob_get_clean();

		// PHPCS - the content does not contain user input value.
		wp_die( new \WP_Error( 'cant_delete_kit', $confirmation_content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Add 'Edit with elementor -> Site Settings' in admin bar.
	 *
	 * @param [] $admin_bar_config
	 *
	 * @return array $admin_bar_config
	 */
	private function add_menu_in_admin_bar( $admin_bar_config ) {
		$document = Plugin::$instance->documents->get( get_the_ID() );

		if ( ! $document || ! $document->is_built_with_elementor() ) {
			$recent_edited_post = Utils::get_recently_edited_posts_query( [
				'posts_per_page' => 1,
			] );

			if ( $recent_edited_post->post_count ) {
				$posts = $recent_edited_post->get_posts();
				$document = Plugin::$instance->documents->get( reset( $posts )->ID );
			}
		}

		if ( $document ) {
			$document_edit_url = add_query_arg(
				[
					'active-document' => $this->get_active_id(),
				],
				$document->get_edit_url()
			);

			$admin_bar_config['elementor_edit_page']['children'][] = [
				'id' => 'elementor_site_settings',
				'title' => esc_html__( 'Site Settings', 'elementor' ),
				'sub_title' => esc_html__( 'Site', 'elementor' ),
				'href' => $document_edit_url,
				'class' => 'elementor-site-settings',
				'parent_class' => 'elementor-second-section',
			];
		}

		return $admin_bar_config;
	}

	public function __construct() {
		add_action( 'elementor/documents/register', [ $this, 'register_document' ] );
		add_filter( 'elementor/editor/localize_settings', [ $this, 'localize_settings' ] );
		add_filter( 'elementor/editor/footer', [ $this, 'render_panel_html' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'frontend_before_enqueue_styles' ], 0 );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'preview_enqueue_styles' ], 0 );
		add_action( 'elementor/controls/register', [ $this, 'register_controls' ] );

		add_action( 'wp_trash_post', function ( $post_id ) {
			$this->before_delete_kit( $post_id );
		} );

		add_action( 'before_delete_post', function ( $post_id ) {
			$this->before_delete_kit( $post_id, true );
		} );

		add_action( 'update_option_blogname', function ( $old_value, $value ) {
			$this->update_kit_settings_based_on_option( 'site_name', $value );
		}, 10, 2 );

		add_action( 'update_option_blogdescription', function ( $old_value, $value ) {
			$this->update_kit_settings_based_on_option( 'site_description', $value );
		}, 10, 2 );

		add_action( 'wp_head', function() {
			$this->add_body_class();
		} );

		add_filter( 'elementor/frontend/admin_bar/settings', function ( $admin_bar_config ) {
			return $this->add_menu_in_admin_bar( $admin_bar_config );
		}, 9 /* Before site-editor (theme-builder) */ );
	}
}
