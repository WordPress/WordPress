<?php
namespace Elementor;

use Elementor\Core\Base\App;
use Elementor\Core\Settings\Manager as SettingsManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor preview.
 *
 * Elementor preview handler class is responsible for initializing Elementor in
 * preview mode.
 *
 * @since 1.0.0
 */
class Preview extends App {

	/**
	 * The priority of the preview enqueued styles.
	 */
	const ENQUEUED_STYLES_PRIORITY = 20;

	/**
	 * Is Preview.
	 *
	 * Holds a flag if current request is a preview.
	 * The flag is not related to a specific post or edit permissions.
	 *
	 * @since 2.9.5
	 * @access private
	 *
	 * @var bool Is Preview.
	 */

	private $is_preview;

	/**
	 * Post ID.
	 *
	 * Holds the ID of the current post being previewed.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @var int Post ID.
	 */
	private $post_id;

	/**
	 * Get module name.
	 *
	 * Retrieve the module name.
	 *
	 * @since 3.0.0
	 * @access public
	 * @abstract
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'preview';
	}

	/**
	 * Init.
	 *
	 * Initialize Elementor preview mode.
	 *
	 * Fired by `template_redirect` action.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {
		if ( is_admin() || ! $this->is_preview_mode() ) {
			return;
		}

		if ( isset( $_GET['preview-debug'] ) ) {
			register_shutdown_function( function () {
				$e = error_get_last();
				if ( $e ) {
					echo '<div id="elementor-preview-debug-error"><pre>';
					Utils::print_unescaped_internal_string( $e['message'] );
					echo '</pre></div>';
				}
			} );
		}

		$this->post_id = get_the_ID();
		$this->is_preview = true;

		// Don't redirect to permalink.
		remove_action( 'template_redirect', 'redirect_canonical' );

		// Compatibility with Yoast SEO plugin when 'Removes unneeded query variables from the URL' enabled.
		// TODO: Move this code to `includes/compatibility.php`.
		if ( class_exists( 'WPSEO_Frontend' ) ) {
			remove_action( 'template_redirect', [ \WPSEO_Frontend::get_instance(), 'clean_permalink' ], 1 );
		}

		// Disable the WP admin bar in preview mode.
		add_filter( 'show_admin_bar', '__return_false' );

		add_action( 'wp_enqueue_scripts', function() {
			$this->enqueue_styles();
			$this->enqueue_scripts();
		}, self::ENQUEUED_STYLES_PRIORITY );

		add_filter( 'the_content', [ $this, 'builder_wrapper' ], 999999 );

		add_action( 'wp_footer', [ $this, 'wp_footer' ] );

		// Avoid Cloudflare's Rocket Loader lazy load the editor iframe
		add_filter( 'script_loader_tag', [ $this, 'rocket_loader_filter' ], 10, 3 );

		// Tell to WP Cache plugins do not cache this request.
		Utils::do_not_cache();

		/**
		 * Preview init.
		 *
		 * Fires on Elementor preview init, after Elementor preview has finished
		 * loading but before any headers are sent.
		 *
		 * @since 1.0.0
		 *
		 * @param Preview $this The current preview.
		 */
		do_action( 'elementor/preview/init', $this );
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
	 * Is Preview.
	 *
	 * Whether current request is the elementor preview iframe.
	 * The flag is not related to a specific post or edit permissions.
	 *
	 * @since 2.9.5
	 * @access public
	 *
	 * @return bool
	 */
	public function is_preview() {
		return $this->is_preview;
	}

	/**
	 * Whether preview mode is active.
	 *
	 * Used to determine whether we are in the preview mode (iframe).
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param int $post_id Optional. Post ID. Default is `0`.
	 *
	 * @return bool Whether preview mode is active.
	 */
	public function is_preview_mode( $post_id = 0 ) {
		if ( ! isset( $_GET['elementor-preview'] ) ) {
			return false;
		}

		if ( empty( $post_id ) ) {
			$post_id = get_the_ID();
		}

		if ( ! User::is_current_user_can_edit( $post_id ) ) {
			return false;
		}

		if ( $post_id !== (int) $_GET['elementor-preview'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Builder wrapper.
	 *
	 * Used to add an empty HTML wrapper for the builder, the javascript will add
	 * the content later.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $content The content of the builder.
	 *
	 * @return string HTML wrapper for the builder.
	 */
	public function builder_wrapper( $content ) {
		if ( get_the_ID() === $this->post_id ) {
			$document = Plugin::$instance->documents->get( $this->post_id );

			$attributes = $document->get_container_attributes();

			$content = '<div ' . Utils::render_html_attributes( $attributes ) . '></div>';
		}

		return $content;
	}

	/**
	 * Enqueue preview styles.
	 *
	 * Registers all the preview styles and enqueues them.
	 *
	 * Fired by `wp_enqueue_scripts` action.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function enqueue_styles() {
		// Hold-on all jQuery plugins after all HTML markup render.
		wp_add_inline_script( 'jquery-migrate', 'jQuery.holdReady( true );' );

		Plugin::$instance->frontend->enqueue_styles();

		Plugin::$instance->elements_manager->enqueue_elements_styles();

		Plugin::$instance->widgets_manager->enqueue_widgets_styles();

		$suffix = Utils::is_script_debug() ? '' : '.min';

		$direction_suffix = is_rtl() ? '-rtl' : '';

		wp_register_style(
			'elementor-select2',
			ELEMENTOR_ASSETS_URL . 'lib/e-select2/css/e-select2' . $suffix . '.css',
			[],
			'4.0.6-rc.1'
		);

		wp_register_style(
			'editor-preview',
			ELEMENTOR_ASSETS_URL . 'css/editor-preview' . $direction_suffix . $suffix . '.css',
			[
				'elementor-select2',
			],
			ELEMENTOR_VERSION
		);

		wp_enqueue_style(
			'e-theme-ui-light',
			$this->get_css_assets_url( 'theme-light' ),
			[],
			ELEMENTOR_VERSION
		);

		wp_enqueue_style( 'editor-preview' );

		// Handle the 'wp audio' in editor preview.
		wp_enqueue_style( 'wp-mediaelement' );

		/**
		 * Preview enqueue styles.
		 *
		 * Fires after Elementor preview styles are enqueued.
		 *
		 * @since 1.0.0
		 */
		do_action( 'elementor/preview/enqueue_styles' );
	}

	/**
	 * Enqueue preview scripts.
	 *
	 * Registers all the preview scripts and enqueues them.
	 *
	 * Fired by `wp_enqueue_scripts` action.
	 *
	 * @since 1.5.4
	 * @access private
	 */
	private function enqueue_scripts() {
		Plugin::$instance->frontend->register_scripts();

		Plugin::$instance->widgets_manager->enqueue_widgets_scripts();
		Plugin::$instance->elements_manager->enqueue_elements_scripts();

		$suffix = Utils::is_script_debug() ? '' : '.min';

		wp_enqueue_script(
			'elementor-inline-editor',
			ELEMENTOR_ASSETS_URL . 'lib/inline-editor/js/inline-editor' . $suffix . '.js',
			[],
			ELEMENTOR_VERSION,
			true
		);

		// Handle the 'wp audio' in editor preview.
		wp_enqueue_script( 'wp-mediaelement' );

		/**
		 * Preview enqueue scripts.
		 *
		 * Fires after Elementor preview scripts are enqueued.
		 *
		 * @since 1.5.4
		 */
		do_action( 'elementor/preview/enqueue_scripts' );
	}

	public function rocket_loader_filter( $tag, $handle, $src ) {
		return str_replace( '<script', '<script data-cfasync="false"', $tag );
	}

	/**
	 * Elementor Preview footer scripts and styles.
	 *
	 * Handle styles and scripts from frontend.
	 *
	 * Fired by `wp_footer` action.
	 *
	 * @since 2.0.9
	 * @access public
	 */
	public function wp_footer() {
		$frontend = Plugin::$instance->frontend;
		if ( $frontend->has_elementor_in_page() ) {
			// Has header/footer/widget-template - enqueue all style/scripts/fonts.
			$frontend->wp_footer();
		} else {
			// Enqueue only scripts.
			$frontend->enqueue_scripts();
		}
	}

	/**
	 * Preview constructor.
	 *
	 * Initializing Elementor preview.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'template_redirect', [ $this, 'init' ], 0 );
	}
}
