<?php
namespace Elementor;

use Elementor\Core\Base\App;
use Elementor\Core\Base\Elements_Iteration_Actions\Assets;
use Elementor\Core\Files\Fonts\Google_Font;
use Elementor\Core\Frontend\Render_Mode_Manager;
use Elementor\Core\Responsive\Files\Frontend as FrontendFile;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Core\Files\CSS\Post_Preview;
use Elementor\Core\Responsive\Responsive;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Core\Breakpoints\Manager as Breakpoints_Manager;
use Elementor\Modules\FloatingButtons\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor frontend.
 *
 * Elementor frontend handler class is responsible for initializing Elementor in
 * the frontend.
 *
 * @since 1.0.0
 */
class Frontend extends App {

	/**
	 * The priority of the content filter.
	 */
	const THE_CONTENT_FILTER_PRIORITY = 9;

	/**
	 * The priority of the frontend enqueued styles.
	 */
	const ENQUEUED_STYLES_PRIORITY = 20;

	/**
	 * Post ID.
	 *
	 * Holds the ID of the current post.
	 *
	 * @access private
	 *
	 * @var int Post ID.
	 */
	private $post_id;

	/**
	 * Fonts to enqueue
	 *
	 * Holds the list of fonts that are being used in the current page.
	 *
	 * @since 1.9.4
	 * @access public
	 *
	 * @var array Used fonts. Default is an empty array.
	 */
	public $fonts_to_enqueue = [];

	/**
	 * Holds the class that respond to manage the render mode.
	 *
	 * @var Render_Mode_Manager
	 */
	public $render_mode_manager;

	/**
	 * Registered fonts.
	 *
	 * Holds the list of enqueued fonts in the current page.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array Registered fonts. Default is an empty array.
	 */
	private $registered_fonts = [];

	/**
	 * Icon Fonts to enqueue
	 *
	 * Holds the list of Icon fonts that are being used in the current page.
	 *
	 * @since 2.4.0
	 * @access private
	 *
	 * @var array Used icon fonts. Default is an empty array.
	 */
	private $icon_fonts_to_enqueue = [];

	/**
	 * Enqueue Icon Fonts
	 *
	 * Holds the list of Icon fonts already enqueued  in the current page.
	 *
	 * @since 2.4.0
	 * @access private
	 *
	 * @var array enqueued icon fonts. Default is an empty array.
	 */
	private $enqueued_icon_fonts = [];

	/**
	 * Whether the page is using Elementor.
	 *
	 * Used to determine whether the current page is using Elementor.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var bool Whether Elementor is being used. Default is false.
	 */
	private $_has_elementor_in_page = false;

	/**
	 * Whether the excerpt is being called.
	 *
	 * Used to determine whether the call to `the_content()` came from `get_the_excerpt()`.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var bool Whether the excerpt is being used. Default is false.
	 */
	private $_is_excerpt = false;

	/**
	 * Filters removed from the content.
	 *
	 * Hold the list of filters removed from `the_content()`. Used to hold the filters that
	 * conflicted with Elementor while Elementor process the content.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var array Filters removed from the content. Default is an empty array.
	 */
	private $content_removed_filters = [];

	/**
	 * @var string[]
	 */
	private $body_classes = [
		'elementor-default',
	];

	/**
	 * Front End constructor.
	 *
	 * Initializing Elementor front end. Make sure we are not in admin, not and
	 * redirect from old URL structure of Elementor editor.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		// We don't need this class in admin side, but in AJAX requests.
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		add_action( 'template_redirect', [ $this, 'init_render_mode' ], -1 /* Before admin bar. */ );
		add_action( 'template_redirect', [ $this, 'init' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ], 5 );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ], 5 );

		$this->add_content_filter();

		// Hack to avoid enqueue post CSS while it's a `the_excerpt` call.
		add_filter( 'get_the_excerpt', [ $this, 'start_excerpt_flag' ], 1 );
		add_filter( 'get_the_excerpt', [ $this, 'end_excerpt_flag' ], 20 );

		if ( version_compare( get_bloginfo( 'version' ), '6.9', '>=' ) ) {
			add_filter( 'wp_should_output_buffer_template_for_enhancement', '__return_false', 1 );
		}
	}

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'frontend';
	}

	/**
	 * Init render mode manager.
	 */
	public function init_render_mode() {
		if ( Plugin::$instance->editor->is_edit_mode() ) {
			return;
		}

		$this->render_mode_manager = new Render_Mode_Manager();
	}

	/**
	 * Init.
	 *
	 * Initialize Elementor front end. Hooks the needed actions to run Elementor
	 * in the front end, including script and style registration.
	 *
	 * Fired by `template_redirect` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		if ( Plugin::$instance->editor->is_edit_mode() ) {
			return;
		}

		add_filter( 'body_class', [ $this, 'body_class' ] );

		if ( Plugin::$instance->preview->is_preview_mode() ) {
			return;
		}

		if ( current_user_can( 'manage_options' ) ) {
			Plugin::$instance->init_common();
		}

		$this->post_id = get_the_ID();

		$document = Plugin::$instance->documents->get( $this->post_id );

		if ( is_singular() && $document && $document->is_built_with_elementor() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ], self::ENQUEUED_STYLES_PRIORITY );
		}

		// Priority 7 to allow google fonts in header template to load in <head> tag
		add_action( 'wp_head', [ $this, 'print_fonts_links' ], 7 );
		add_action( 'wp_head', [ $this, 'add_theme_color_meta_tag' ] );
		add_action( 'wp_footer', [ $this, 'wp_footer' ] );
	}

	/**
	 * @since 2.0.12
	 * @access public
	 * @param string|array $class_name
	 */
	public function add_body_class( $class_name ) {
		if ( is_array( $class_name ) ) {
			$this->body_classes = array_merge( $this->body_classes, $class_name );
		} else {
			$this->body_classes[] = $class_name;
		}
	}

	/**
	 * Add Theme Color Meta Tag
	 *
	 * @since 3.0.0
	 * @access public
	 */
	public function add_theme_color_meta_tag() {
		$kit = Plugin::$instance->kits_manager->get_active_kit_for_frontend();
		$mobile_theme_color = $kit->get_settings( 'mobile_browser_background' );

		if ( ! empty( $mobile_theme_color ) ) {
			?>
			<meta name="theme-color" content="<?php echo esc_attr( $mobile_theme_color ); ?>">
			<?php
		}
	}

	/**
	 * Body tag classes.
	 *
	 * Add new elementor classes to the body tag.
	 *
	 * Fired by `body_class` filter.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array $classes Optional. One or more classes to add to the body tag class list.
	 *                       Default is an empty array.
	 *
	 * @return array Body tag classes.
	 */
	public function body_class( $classes = [] ) {
		$classes = array_merge( $classes, $this->body_classes );

		$id = get_the_ID();

		$document = Plugin::$instance->documents->get( $id );

		if ( is_singular() && $document && $document->is_built_with_elementor() ) {
			$classes[] = 'elementor-page elementor-page-' . $id;
		}

		if ( Plugin::$instance->preview->is_preview_mode() ) {
			$editor_preferences = SettingsManager::get_settings_managers( 'editorPreferences' );

			$show_hidden_elements = $editor_preferences->get_model()->get_settings( 'show_hidden_elements' );

			if ( 'yes' === $show_hidden_elements ) {
				$classes[] = 'e-preview--show-hidden-elements';
			}
		}

		return $classes;
	}

	/**
	 * Add content filter.
	 *
	 * Remove plain content and render the content generated by Elementor.
	 *
	 * @since 1.8.0
	 * @access public
	 */
	public function add_content_filter() {
		add_filter( 'the_content', [ $this, 'apply_builder_in_content' ], self::THE_CONTENT_FILTER_PRIORITY );
	}

	/**
	 * Remove content filter.
	 *
	 * When the Elementor generated content rendered, we remove the filter to prevent multiple
	 * accuracies. This way we make sure Elementor renders the content only once.
	 *
	 * @since 1.8.0
	 * @access public
	 */
	public function remove_content_filter() {
		remove_filter( 'the_content', [ $this, 'apply_builder_in_content' ], self::THE_CONTENT_FILTER_PRIORITY );
	}

	/**
	 * Registers scripts.
	 *
	 * Registers all the frontend scripts.
	 *
	 * Fired by `wp_enqueue_scripts` action.
	 *
	 * @since 1.2.1
	 * @access public
	 */
	public function register_scripts() {
		/**
		 * Before frontend register scripts.
		 *
		 * Fires before Elementor frontend scripts are registered.
		 *
		 * @since 1.2.1
		 */
		do_action( 'elementor/frontend/before_register_scripts' );

		wp_register_script(
			'elementor-webpack-runtime',
			$this->get_js_assets_url( 'webpack.runtime', 'assets/js/' ),
			[],
			ELEMENTOR_VERSION,
			true
		);

		wp_register_script(
			'elementor-frontend-modules',
			$this->get_js_assets_url( 'frontend-modules' ),
			[
				'elementor-webpack-runtime',
				'jquery',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_register_script(
			'swiper',
			$this->get_js_assets_url( 'swiper', 'assets/lib/swiper/v8/' ),
			[],
			'8.4.5',
			true
		);

		wp_register_script(
			'flatpickr',
			$this->get_js_assets_url( 'flatpickr', 'assets/lib/flatpickr/' ),
			[
				'jquery',
			],
			'4.6.13',
			true
		);

		wp_register_script(
			'imagesloaded',
			$this->get_js_assets_url( 'imagesloaded', 'assets/lib/imagesloaded/' ),
			[
				'jquery',
			],
			'4.1.0',
			true
		);

		wp_register_script(
			'jquery-numerator',
			$this->get_js_assets_url( 'jquery-numerator', 'assets/lib/jquery-numerator/' ),
			[
				'jquery',
			],
			'0.2.1',
			true
		);

		wp_register_script(
			'elementor-dialog',
			$this->get_js_assets_url( 'dialog', 'assets/lib/dialog/' ),
			[
				'jquery-ui-position',
			],
			'4.9.4',
			true
		);

		wp_register_script(
			'elementor-gallery',
			$this->get_js_assets_url( 'e-gallery', 'assets/lib/e-gallery/js/' ),
			[
				'jquery',
			],
			'1.2.0',
			true
		);

		wp_register_script(
			'share-link',
			$this->get_js_assets_url( 'share-link', 'assets/lib/share-link/' ),
			[
				'jquery',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_register_script(
			'elementor-frontend',
			$this->get_js_assets_url( 'frontend' ),
			[
				'elementor-frontend-modules',
				'jquery-ui-position',
			],
			ELEMENTOR_VERSION,
			true
		);

		/**
		 * After frontend register scripts.
		 *
		 * Fires after Elementor frontend scripts are registered.
		 *
		 * @since 1.2.1
		 */
		do_action( 'elementor/frontend/after_register_scripts' );
	}

	/**
	 * Registers styles.
	 *
	 * Registers all the frontend styles.
	 *
	 * Fired by `wp_enqueue_scripts` action.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function register_styles() {
		$min_suffix = Utils::is_script_debug() ? '' : '.min';
		$direction_suffix = is_rtl() ? '-rtl' : '';
		$has_custom_breakpoints = Plugin::$instance->breakpoints->has_custom_breakpoints();

		/**
		 * Before frontend register styles.
		 *
		 * Fires before Elementor frontend styles are registered.
		 *
		 * @since 1.2.0
		 */
		do_action( 'elementor/frontend/before_register_styles' );

		wp_register_style(
			'font-awesome',
			$this->get_css_assets_url( 'font-awesome', 'assets/lib/font-awesome/css/' ),
			[],
			'4.7.0'
		);

		wp_register_style(
			'elementor-icons',
			$this->get_css_assets_url( 'elementor-icons', 'assets/lib/eicons/css/' ),
			[],
			Icons_Manager::ELEMENTOR_ICONS_VERSION
		);

		wp_register_style(
			'flatpickr',
			$this->get_css_assets_url( 'flatpickr', 'assets/lib/flatpickr/' ),
			[],
			'4.6.13'
		);

		wp_register_style(
			'elementor-gallery',
			$this->get_css_assets_url( 'e-gallery', 'assets/lib/e-gallery/css/' ),
			[],
			'1.2.0'
		);

		wp_register_style(
			'e-apple-webkit',
			$this->get_frontend_file_url( 'apple-webkit.min.css', $has_custom_breakpoints, 'conditionals/' ),
			[],
			$has_custom_breakpoints ? null : ELEMENTOR_VERSION
		);

		wp_register_style(
			'e-swiper',
			$this->get_css_assets_url( 'e-swiper', 'assets/css/conditionals/' ),
			[ 'swiper' ],
			ELEMENTOR_VERSION
		);

		wp_register_style(
			'swiper',
			$this->get_css_assets_url( 'swiper', 'assets/lib/swiper/v8/css/' ),
			[],
			'8.4.5'
		);

		wp_register_style(
			'elementor-wp-admin-bar',
			$this->get_css_assets_url( 'admin-bar', 'assets/css/' ),
			[],
			ELEMENTOR_VERSION
		);

		wp_register_style(
			'e-lightbox',
			$this->get_frontend_file_url( 'lightbox.min.css', $has_custom_breakpoints, 'conditionals/' ),
			[],
			$has_custom_breakpoints ? null : ELEMENTOR_VERSION
		);

		wp_register_style(
			'elementor-frontend',
			$this->get_frontend_file_url( "frontend{$direction_suffix}{$min_suffix}.css", $has_custom_breakpoints ),
			[],
			$has_custom_breakpoints ? null : ELEMENTOR_VERSION
		);

		$widgets_with_styles = Plugin::$instance->widgets_manager->widgets_with_styles();
		foreach ( $widgets_with_styles as $widget_name ) {
			wp_register_style(
				"widget-{$widget_name}",
				$this->get_css_assets_url( "widget-{$widget_name}", null, true, true ),
				[ 'elementor-frontend' ],
				ELEMENTOR_VERSION
			);
		}

		$widgets_with_responsive_styles = Plugin::$instance->widgets_manager->widgets_with_responsive_styles();
		foreach ( $widgets_with_responsive_styles as $widget_name ) {
			wp_register_style(
				"widget-{$widget_name}",
				$this->get_frontend_file_url( "widget-{$widget_name}{$direction_suffix}.min.css", $has_custom_breakpoints ),
				[ 'elementor-frontend' ],
				$has_custom_breakpoints ? null : ELEMENTOR_VERSION
			);
		}

		/**
		 * After frontend register styles.
		 *
		 * Fires after Elementor frontend styles are registered.
		 *
		 * @since 1.2.0
		 */
		do_action( 'elementor/frontend/after_register_styles' );
	}

	/**
	 * Enqueue scripts.
	 *
	 * Enqueue all the frontend scripts.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_scripts() {
		/**
		 * Before frontend enqueue scripts.
		 *
		 * Fires before Elementor frontend scripts are enqueued.
		 *
		 * @since 1.0.0
		 */
		do_action( 'elementor/frontend/before_enqueue_scripts' );

		$this->print_config();

		$this->enqueue_conditional_assets();

		/**
		 * After frontend enqueue scripts.
		 *
		 * Fires after Elementor frontend scripts are enqueued.
		 *
		 * @since 1.0.0
		 */
		do_action( 'elementor/frontend/after_enqueue_scripts' );
	}

	/**
	 * Enqueue styles.
	 *
	 * Enqueue all the frontend styles.
	 *
	 * Fired by `wp_enqueue_scripts` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_styles() {
		static $is_enqueue_styles_already_triggered;

		if ( ! $is_enqueue_styles_already_triggered ) {
			$is_enqueue_styles_already_triggered = true;

			/**
			 * Before frontend styles enqueued.
			 *
			 * Fires before Elementor frontend styles are enqueued.
			 *
			 * @since 1.0.0
			 */
			do_action( 'elementor/frontend/before_enqueue_styles' );

			// The e-icons are needed in preview mode for the editor icons (plus-icon for new section, folder-icon for the templates library etc.).
			if ( ! Plugin::$instance->experiments->is_feature_active( 'e_font_icon_svg' ) || Plugin::$instance->preview->is_preview_mode() ) {
				wp_enqueue_style( 'elementor-icons' );
			}

			wp_enqueue_style( 'elementor-frontend' );

			if ( is_admin_bar_showing() ) {
				wp_enqueue_style( 'elementor-wp-admin-bar' );
			}

			/**
			 * After frontend styles enqueued.
			 *
			 * Fires after Elementor frontend styles are enqueued.
			 *
			 * @since 1.0.0
			 */
			do_action( 'elementor/frontend/after_enqueue_styles' );

			if ( ! Plugin::$instance->preview->is_preview_mode() ) {
				$post_id = get_the_ID();
				// Check $post_id for virtual pages. check is singular because the $post_id is set to the first post on archive pages.
				if ( $post_id && is_singular() ) {
					do_action( 'elementor/post/render', $post_id );
					$this->handle_page_assets( $post_id );

					$css_file = Post_CSS::create( get_the_ID() );
					$css_file->enqueue();
				}
			}

			do_action( 'elementor/frontend/after_enqueue_post_styles' );
		}
	}

	private function handle_page_assets( $post_id ): void {
		$page_assets = get_post_meta( $post_id, Assets::ASSETS_META_KEY, true );
		if ( ! empty( $page_assets ) ) {
			Plugin::$instance->assets_loader->enable_assets( $page_assets );
			return;
		}

		$document = Plugin::$instance->documents->get( $post_id );

		if ( ! $document ) {
			return;
		}

		$document->update_runtime_elements();
	}

	/**
	 * Get Frontend File URL
	 *
	 * Returns the URL for the CSS file to be loaded in the front end. If requested via the second parameter, a custom
	 * file is generated based on a passed template file name. Otherwise, the URL for the default CSS file is returned.
	 *
	 * @since 3.4.5
	 *
	 * @access public
	 *
	 * @param string  $frontend_file_name
	 * @param boolean $custom_file
	 *
	 * @return string frontend file URL
	 */
	public function get_frontend_file_url( $frontend_file_name, $custom_file, $css_subfolder = '' ) {
		if ( $custom_file ) {
			$frontend_file = $this->get_frontend_file( $frontend_file_name );

			$frontend_file_url = $frontend_file->get_url();
		} else {
			$frontend_file_url = ELEMENTOR_ASSETS_URL . 'css/' . $css_subfolder . $frontend_file_name;
		}

		return $frontend_file_url;
	}

	/**
	 * Get Frontend File Path
	 *
	 * Returns the path for the CSS file to be loaded in the front end. If requested via the second parameter, a custom
	 * file is generated based on a passed template file name. Otherwise, the path for the default CSS file is returned.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string  $frontend_file_name
	 * @param boolean $custom_file
	 *
	 * @return string frontend file path
	 */
	public function get_frontend_file_path( $frontend_file_name, $custom_file ) {
		if ( $custom_file ) {
			$frontend_file = $this->get_frontend_file( $frontend_file_name );

			$frontend_file_path = $frontend_file->get_path();
		} else {
			$frontend_file_path = ELEMENTOR_ASSETS_PATH . 'css/' . $frontend_file_name;
		}

		return $frontend_file_path;
	}

	/**
	 * Get Frontend File
	 *
	 * Returns a frontend file instance.
	 *
	 * @since 3.5.0
	 * @access public
	 *
	 * @param string $frontend_file_name
	 * @param string $file_prefix
	 * @param string $template_file_path
	 *
	 * @return FrontendFile
	 */
	public function get_frontend_file( $frontend_file_name, $file_prefix = 'custom-', $template_file_path = '' ) {
		static $cached_frontend_files = [];

		$file_name = $file_prefix . $frontend_file_name;

		if ( isset( $cached_frontend_files[ $file_name ] ) ) {
			return $cached_frontend_files[ $file_name ];
		}

		if ( ! $template_file_path ) {
			$template_file_path = Breakpoints_Manager::get_stylesheet_templates_path() . $frontend_file_name;
		}

		$frontend_file = new FrontendFile( $file_name, $template_file_path );

		$time = $frontend_file->get_meta( 'time' );

		if ( ! $time ) {
			$frontend_file->update();
		}

		$cached_frontend_files[ $file_name ] = $frontend_file;

		return $frontend_file;
	}

	/**
	 * Enqueue assets conditionally.
	 *
	 * Enqueue all assets that were pre-enabled.
	 *
	 * @since 3.3.0
	 * @access private
	 */
	private function enqueue_conditional_assets() {
		Plugin::$instance->assets_loader->enqueue_assets();
	}

	/**
	 * Elementor footer scripts and styles.
	 *
	 * Handle styles and scripts that are not printed in the header.
	 *
	 * Fired by `wp_footer` action.
	 *
	 * @since 1.0.11
	 * @access public
	 */
	public function wp_footer() {
		if ( ! $this->_has_elementor_in_page ) {
			return;
		}

		$this->enqueue_styles();
		$this->enqueue_scripts();

		$this->print_fonts_links();
	}

	/**
	 * @return array|array[]
	 */
	public function get_list_of_google_fonts_by_type(): array {
		$google_fonts = [
			'google' => [],
			'early' => [],
		];

		foreach ( $this->fonts_to_enqueue as $key => $font ) {
			$font_type = Fonts::get_font_type( $font );

			switch ( $font_type ) {
				case Fonts::GOOGLE:
					$google_fonts['google'][] = $font;
					break;

				case Fonts::EARLYACCESS:
					$google_fonts['early'][] = $font;
					break;

				case false:
					$this->maybe_enqueue_icon_font( $font );
					break;
				default:
					/**
					 * Print font links.
					 *
					 * Fires when Elementor frontend fonts are printed on the HEAD tag.
					 *
					 * The dynamic portion of the hook name, `$font_type`, refers to the font type.
					 *
					 * @since 2.0.0
					 *
					 * @param string $font Font name.
					 */
					do_action( "elementor/fonts/print_font_links/{$font_type}", $font );
			}
		}
		$this->fonts_to_enqueue = [];

		return $google_fonts;
	}

	/**
	 * Print fonts links.
	 *
	 * Enqueue all the frontend fonts by url.
	 *
	 * Fired by `wp_head` action.
	 *
	 * @since 1.9.4
	 * @access public
	 */
	public function print_fonts_links() {
		$google_fonts = $this->get_list_of_google_fonts_by_type();

		$this->enqueue_google_fonts( $google_fonts );
		$this->enqueue_icon_fonts();
	}

	private function maybe_enqueue_icon_font( $icon_font_type ) {
		if ( ! Icons_Manager::is_migration_allowed() ) {
			return;
		}

		$icons_types = Icons_Manager::get_icon_manager_tabs();
		if ( ! isset( $icons_types[ $icon_font_type ] ) ) {
			return;
		}

		$icon_type = $icons_types[ $icon_font_type ];
		if ( isset( $icon_type['url'] ) ) {
			$this->icon_fonts_to_enqueue[ $icon_font_type ] = [ $icon_type['url'] ];
		}
	}

	private function enqueue_icon_fonts() {
		if ( empty( $this->icon_fonts_to_enqueue ) || ! Icons_Manager::is_migration_allowed() ) {
			return;
		}

		foreach ( $this->icon_fonts_to_enqueue as $icon_type => $css_url ) {
			wp_enqueue_style( 'elementor-icons-' . $icon_type );
			$this->enqueued_icon_fonts[] = $css_url;
		}

		// Clear enqueued icons.
		$this->icon_fonts_to_enqueue = [];
	}

	/**
	 * @param array $fonts Stable google fonts ($google_fonts['google']).
	 * @return string
	 */
	public function get_stable_google_fonts_url( array $fonts ): string {
		foreach ( $fonts as &$font ) {
			$font = str_replace( ' ', '+', $font ) . ':100,100italic,200,200italic,300,300italic,400,400italic,500,500italic,600,600italic,700,700italic,800,800italic,900,900italic';
		}

		// Defining a font-display type to google fonts.
		$font_display_url_str = '&display=' . Fonts::get_font_display_setting();

		$fonts_url = sprintf( 'https://fonts.googleapis.com/css?family=%1$s%2$s', implode( rawurlencode( '|' ), $fonts ), $font_display_url_str );

		$subsets = [
			'ru_RU' => 'cyrillic',
			'bg_BG' => 'cyrillic',
			'he_IL' => 'hebrew',
			'el' => 'greek',
			'vi' => 'vietnamese',
			'uk' => 'cyrillic',
			'cs_CZ' => 'latin-ext',
			'ro_RO' => 'latin-ext',
			'pl_PL' => 'latin-ext',
			'hr_HR' => 'latin-ext',
			'hu_HU' => 'latin-ext',
			'sk_SK' => 'latin-ext',
			'tr_TR' => 'latin-ext',
			'lt_LT' => 'latin-ext',
		];

		/**
		 * Google font subsets.
		 *
		 * Filters the list of Google font subsets from which locale will be enqueued in frontend.
		 *
		 * @since 1.0.0
		 *
		 * @param array $subsets A list of font subsets.
		 */
		$subsets = apply_filters( 'elementor/frontend/google_font_subsets', $subsets );

		$locale = get_locale();

		if ( isset( $subsets[ $locale ] ) ) {
			$fonts_url .= '&subset=' . $subsets[ $locale ];
		}

		return $fonts_url;
	}

	/**
	 * @param array $fonts Early Access google fonts ($google_fonts['early']).
	 * @return array
	 */
	public function get_early_access_google_font_urls( array $fonts ): array {
		$font_urls = [];

		foreach ( $fonts as $font ) {
			$font_urls[] = sprintf( 'https://fonts.googleapis.com/earlyaccess/%s.css', strtolower( str_replace( ' ', '', $font ) ) );
		}

		return $font_urls;
	}

	/**
	 * Print Google fonts.
	 *
	 * Enqueue all the frontend Google fonts.
	 *
	 * Fired by `wp_head` action.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $google_fonts Optional. Google fonts to print in the frontend.
	 *                            Default is an empty array.
	 */
	private function enqueue_google_fonts( $google_fonts = [] ) {
		$print_google_fonts = Fonts::is_google_fonts_enabled();

		/**
		 * Print frontend google fonts.
		 *
		 * Filters whether to enqueue Google fonts in the frontend.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $print_google_fonts Whether to enqueue Google fonts. Default is true.
		 */
		$print_google_fonts = apply_filters( 'elementor/frontend/print_google_fonts', $print_google_fonts );

		if ( ! $print_google_fonts ) {
			return;
		}

		$force_enqueue_from_cdn = Plugin::$instance->preview->is_preview_mode();

		if ( ! empty( $google_fonts['google'] ) ) {
			foreach ( $google_fonts['google'] as $current_font ) {
				Google_Font::enqueue( $current_font, Google_Font::TYPE_DEFAULT, $force_enqueue_from_cdn );
			}
		}

		if ( ! empty( $google_fonts['early'] ) ) {
			foreach ( $google_fonts['early'] as $current_font ) {
				Google_Font::enqueue( $current_font, Google_Font::TYPE_EARLYACCESS, $force_enqueue_from_cdn );
			}
		}
	}

	/**
	 * Enqueue fonts.
	 *
	 * Enqueue all the frontend fonts.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array $font Fonts to enqueue in the frontend.
	 */
	public function enqueue_font( $font ) {
		if ( in_array( $font, $this->registered_fonts ) ) {
			return;
		}

		$this->fonts_to_enqueue[] = $font;
		$this->registered_fonts[] = $font;
	}

	/**
	 * Apply builder in content.
	 *
	 * Used to apply the Elementor page editor on the post content.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $content The post content.
	 *
	 * @return string The post content.
	 */
	public function apply_builder_in_content( $content ) {
		$this->restore_content_filters();

		if ( Plugin::$instance->preview->is_preview_mode() || $this->_is_excerpt ) {
			return $content;
		}

		// Remove the filter itself in order to allow other `the_content` in the elements
		$this->remove_content_filter();

		$post_id = get_the_ID();
		$builder_content = $this->get_builder_content( $post_id );

		if ( ! empty( $builder_content ) ) {
			$content = $builder_content;
			$this->remove_content_filters();
		}

		// Add the filter again for other `the_content` calls
		$this->add_content_filter();

		return $content;
	}

	/**
	 * Retrieve builder content.
	 *
	 * Used to render and return the post content with all the Elementor elements.
	 *
	 * Note that this method is an internal method, please use `get_builder_content_for_display()`.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int  $post_id  The post ID.
	 * @param bool $with_css Optional. Whether to retrieve the content with CSS
	 *                       or not. Default is false.
	 *
	 * @return string The post content.
	 */
	public function get_builder_content( $post_id, $with_css = false ) {
		if ( post_password_required( $post_id ) ) {
			return '';
		}

		$document = Plugin::$instance->documents->get_doc_for_frontend( $post_id );

		if ( ! $document || ! $document->is_built_with_elementor() ) {
			return '';
		}

		// Change the current post, so widgets can use `documents->get_current`.
		Plugin::$instance->documents->switch_to_document( $document );

		$data = $document->get_elements_data();

		/**
		 * Frontend builder content data.
		 *
		 * Filters the builder content in the frontend.
		 *
		 * @since 1.0.0
		 *
		 * @param array $data    The builder content.
		 * @param int   $post_id The post ID.
		 */
		$data = apply_filters( 'elementor/frontend/builder_content_data', $data, $post_id );

		do_action( 'elementor/frontend/before_get_builder_content', $document, $this->_is_excerpt );

		if ( empty( $data ) ) {
			Plugin::$instance->documents->restore_document();

			return '';
		}

		if ( ! $this->_is_excerpt ) {
			if ( $document->is_autosave() ) {
				$css_file = Post_Preview::create( $document->get_post()->ID );
			} else {
				$css_file = Post_CSS::create( $post_id );
			}

			/**
			 * Builder Content - Before Enqueue CSS File
			 *
			 * Allows intervening with a document's CSS file before it is enqueued.
			 *
			 * @param $css_file Post_CSS|Post_Preview
			 */
			$css_file = apply_filters( 'elementor/frontend/builder_content/before_enqueue_css_file', $css_file );

			$css_file->enqueue();
		}

		ob_start();

		// Handle JS and Customizer requests, with CSS inline.
		if ( is_customize_preview() || wp_doing_ajax() ) {
			$with_css = true;
		}

		/**
		 * Builder Content - With CSS
		 *
		 * Allows overriding the `$with_css` parameter which is a factor in determining whether to print the document's
		 * CSS and font links inline in a `style` tag above the document's markup.
		 *
		 * @param $with_css boolean
		 */
		$with_css = apply_filters( 'elementor/frontend/builder_content/before_print_css', $with_css );

		if ( ! empty( $css_file ) && $with_css ) {
			$css_file->print_css();
		}

		$document->print_elements_with_wrapper( $data );

		$content = ob_get_clean();

		$content = $this->process_more_tag( $content );

		/**
		 * Frontend content.
		 *
		 * Filters the content in the frontend.
		 *
		 * @since 1.0.0
		 *
		 * @param string $content The content.
		 */
		$content = apply_filters( 'elementor/frontend/the_content', $content );

		if ( ! empty( $content ) ) {
			$this->_has_elementor_in_page = true;
		}

		Plugin::$instance->documents->restore_document();

		// BC
		// TODO: use Deprecation::do_deprecated_action() in 3.1.0
		do_action( 'elementor/frontend/get_builder_content', $document, $this->_is_excerpt, $with_css );

		return $content;
	}

	/**
	 * Retrieve builder content for display.
	 *
	 * Used to render and return the post content with all the Elementor elements.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int  $post_id The post ID.
	 *
	 * @param bool $with_css Optional. Whether to retrieve the content with CSS
	 *                       or not. Default is false.
	 *
	 * @return string The post content.
	 */
	public function get_builder_content_for_display( $post_id, $with_css = false ) {
		if ( ! get_post( $post_id ) ) {
			return '';
		}

		$editor = Plugin::$instance->editor;

		// Avoid recursion
		if ( get_the_ID() === (int) $post_id ) {
			$content = '';
			if ( $editor->is_edit_mode() ) {
				$content = '<div class="elementor-alert elementor-alert-danger">' . esc_html__( 'Invalid Data: The Template ID cannot be the same as the currently edited template. Please choose a different one.', 'elementor' ) . '</div>';
			}

			return $content;
		}

		// Set edit mode as false, so don't render settings and etc. use the $is_edit_mode to indicate if we need the CSS inline
		$is_edit_mode = $editor->is_edit_mode();
		$editor->set_edit_mode( false );

		$with_css = $with_css ? true : $is_edit_mode;

		$content = $this->get_builder_content( $post_id, $with_css );

		// Restore edit mode state
		Plugin::$instance->editor->set_edit_mode( $is_edit_mode );

		return $content;
	}

	/**
	 * Start excerpt flag.
	 *
	 * Flags when `the_excerpt` is called. Used to avoid enqueueing CSS in the excerpt.
	 *
	 * @since 1.4.3
	 * @access public
	 *
	 * @param string $excerpt The post excerpt.
	 *
	 * @return string The post excerpt.
	 */
	public function start_excerpt_flag( $excerpt ) {
		$this->_is_excerpt = true;
		return $excerpt;
	}

	/**
	 * End excerpt flag.
	 *
	 * Flags when `the_excerpt` call ended.
	 *
	 * @since 1.4.3
	 * @access public
	 *
	 * @param string $excerpt The post excerpt.
	 *
	 * @return string The post excerpt.
	 */
	public function end_excerpt_flag( $excerpt ) {
		$this->_is_excerpt = false;
		return $excerpt;
	}

	/**
	 * Remove content filters.
	 *
	 * Remove WordPress default filters that conflicted with Elementor.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function remove_content_filters() {
		$filters = [
			'wpautop',
			'shortcode_unautop',
			'wptexturize',
		];

		foreach ( $filters as $filter ) {
			// Check if another plugin/theme do not already removed the filter.
			if ( has_filter( 'the_content', $filter ) ) {
				remove_filter( 'the_content', $filter );
				$this->content_removed_filters[] = $filter;
			}
		}
	}

	/**
	 * Has Elementor In Page
	 *
	 * Determine whether the current page is using Elementor.
	 *
	 * @since 2.0.9
	 *
	 * @access public
	 * @return bool
	 */
	public function has_elementor_in_page() {
		return $this->_has_elementor_in_page;
	}

	public function create_action_hash( $action, array $settings = [] ) {
		return '#' . rawurlencode( sprintf( 'elementor-action:action=%1$s&settings=%2$s', $action, base64_encode( wp_json_encode( $settings ) ) ) );
	}

	/**
	 * Is the current render mode is static.
	 *
	 * @return bool
	 */
	public function is_static_render_mode() {
		// The render mode manager is exists only in frontend,
		// so by default if it is not exist the method will return false.
		if ( ! $this->render_mode_manager ) {
			return false;
		}

		return $this->render_mode_manager->get_current()->is_static();
	}

	/**
	 * Get Init Settings
	 *
	 * Used to define the default/initial settings of the object. Inheriting classes may implement this method to define
	 * their own default/initial settings.
	 *
	 * @since 2.3.0
	 *
	 * @access protected
	 * @return array
	 */
	protected function get_init_settings() {
		$is_preview_mode = Plugin::$instance->preview->is_preview_mode( Plugin::$instance->preview->get_post_id() );

		$active_experimental_features = Plugin::$instance->experiments->get_active_features();

		$active_experimental_features = array_fill_keys( array_keys( $active_experimental_features ), true );

		$assets_url = ELEMENTOR_ASSETS_URL;

		/**
		 * Frontend assets URL
		 *
		 * Filters Elementor frontend assets URL.
		 *
		 * @since 2.3.0
		 *
		 * @param string $assets_url The frontend assets URL. Default is ELEMENTOR_ASSETS_URL.
		 */
		$assets_url = apply_filters( 'elementor/frontend/assets_url', $assets_url );

		$settings = [
			'environmentMode' => [
				'edit' => $is_preview_mode,
				'wpPreview' => is_preview(),
				'isScriptDebug' => Utils::is_script_debug(),
			],
			'i18n' => [
				'shareOnFacebook' => esc_html__( 'Share on Facebook', 'elementor' ),
				'shareOnTwitter' => esc_html__( 'Share on Twitter', 'elementor' ),
				'pinIt' => esc_html__( 'Pin it', 'elementor' ),
				'download' => esc_html__( 'Download', 'elementor' ),
				'downloadImage' => esc_html__( 'Download image', 'elementor' ),
				'fullscreen' => esc_html__( 'Fullscreen', 'elementor' ),
				'zoom' => esc_html__( 'Zoom', 'elementor' ),
				'share' => esc_html__( 'Share', 'elementor' ),
				'playVideo' => esc_html__( 'Play Video', 'elementor' ),
				'previous' => esc_html__( 'Previous', 'elementor' ),
				'next' => esc_html__( 'Next', 'elementor' ),
				'close' => esc_html__( 'Close', 'elementor' ),
				'a11yCarouselPrevSlideMessage' => __( 'Previous slide', 'elementor' ),
				'a11yCarouselNextSlideMessage' => __( 'Next slide', 'elementor' ),
				'a11yCarouselFirstSlideMessage' => __( 'This is the first slide', 'elementor' ),
				'a11yCarouselLastSlideMessage' => __( 'This is the last slide', 'elementor' ),
				'a11yCarouselPaginationBulletMessage' => __( 'Go to slide', 'elementor' ),
			],
			'is_rtl' => is_rtl(),
			// 'breakpoints' object is kept for BC.
			'breakpoints' => Responsive::get_breakpoints(),
			// 'responsive' contains the custom breakpoints config introduced in Elementor v3.2.0
			'responsive' => [
				'breakpoints' => Plugin::$instance->breakpoints->get_breakpoints_config(),
				'hasCustomBreakpoints' => Plugin::$instance->breakpoints->has_custom_breakpoints(),
			],
			'version' => ELEMENTOR_VERSION,
			'is_static' => $this->is_static_render_mode(),
			'experimentalFeatures' => $active_experimental_features,
			'urls' => [
				'assets' => $assets_url,
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'uploadUrl' => wp_upload_dir()['baseurl'],
			],
			'nonces' => [
				'floatingButtonsClickTracking' => wp_create_nonce( Module::CLICK_TRACKING_NONCE ),
			],
			'swiperClass' => 'swiper',
		];

		$settings['settings'] = SettingsManager::get_settings_frontend_config();

		$kit = Plugin::$instance->kits_manager->get_active_kit_for_frontend();
		$settings['kit'] = $kit->get_frontend_settings();

		if ( is_singular() ) {
			$post = get_post();

			$title = Utils::urlencode_html_entities( wp_get_document_title() );

			// Try to use the 'large' WP image size because the Pinterest share API
			// has problems accepting shares with large images sometimes, and the WP 'large' thumbnail is
			// the largest default WP image size that will probably not be changed in most sites
			$featured_image_url = get_the_post_thumbnail_url( null, 'large' );

			// If the large size was nullified, use the full size which cannot be nullified/deleted
			if ( ! $featured_image_url ) {
				$featured_image_url = get_the_post_thumbnail_url( null, 'full' );
			}

			$settings['post'] = [
				'id' => $post->ID,
				'title' => $title,
				'excerpt' => $post->post_excerpt,
				'featuredImage' => $featured_image_url,
			];
		} else {
			$settings['post'] = [
				'id' => 0,
				'title' => wp_get_document_title(),
				'excerpt' => get_the_archive_description(),
			];
		}

		$empty_object = (object) [];

		if ( $is_preview_mode ) {
			$settings['elements'] = [
				'data' => $empty_object,
				'editSettings' => $empty_object,
				'keys' => $empty_object,
			];
		}

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();

			if ( ! empty( $user->roles ) ) {
				$settings['user'] = [
					'roles' => $user->roles,
				];
			}
		}

		return $settings;
	}

	/**
	 * Restore content filters.
	 *
	 * Restore removed WordPress filters that conflicted with Elementor.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function restore_content_filters() {
		foreach ( $this->content_removed_filters as $filter ) {
			add_filter( 'the_content', $filter );
		}

		$this->content_removed_filters = [];
	}

	/**
	 * Process More Tag
	 *
	 * Respect the native WP (<!--more-->) tag
	 *
	 * @access private
	 * @since 2.0.4
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	private function process_more_tag( $content ) {
		$post = get_post();
		$content = str_replace( '&lt;!--more--&gt;', '<!--more-->', $content );
		$parts = get_extended( $content );
		if ( empty( $parts['extended'] ) ) {
			return $content;
		}

		if ( is_singular() ) {
			return $parts['main'] . '<div id="more-' . $post->ID . '"></div>' . $parts['extended'];
		}

		if ( empty( $parts['more_text'] ) ) {
			$parts['more_text'] = esc_html__( '(more&hellip;)', 'elementor' );
		}

		$more_link_text = sprintf(
			'<span aria-label="%1$s">%2$s</span>',
			sprintf(
				/* translators: %s: Current post name. */
				__( 'Continue reading %s', 'elementor' ),
				the_title_attribute( [
					'echo' => false,
				] )
			),
			$parts['more_text']
		);

		$more_link = sprintf( ' <a href="%s#more-%s" class="more-link elementor-more-link">%s</a>', get_permalink(), $post->ID, $more_link_text );

		/**
		 * The content "more" link.
		 *
		 * Filters the "more" link displayed after the content.
		 *
		 * This hook can be used either to change the link syntax or to change the
		 * text inside the link.
		 *
		 * @since 2.0.4
		 *
		 * @param string $more_link      The more link.
		 * @param string $more_link_text The text inside the more link.
		 */
		$more_link = apply_filters( 'the_content_more_link', $more_link, $more_link_text );

		return force_balance_tags( $parts['main'] ) . $more_link;
	}
}
