<?php
namespace Elementor\Core\Common;

use Elementor\Core\Base\App as BaseApp;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Core\Common\Modules\Finder\Module as Finder;
use Elementor\Core\Common\Modules\Connect\Module as Connect;
use Elementor\Core\Common\Modules\EventTracker\Module as Event_Tracker;
use Elementor\Core\Common\Modules\EventsManager\Module as Events_Manager;
use Elementor\Core\Files\Uploads_Manager;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * App
 *
 * Elementor's common app that groups shared functionality, components and configuration
 *
 * @since 2.3.0
 */
class App extends BaseApp {

	private $templates = [];

	/**
	 * App constructor.
	 *
	 * @since 2.3.0
	 * @access public
	 */
	public function __construct() {
		$this->add_default_templates();

		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'register_scripts' ], 9 );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_scripts' ], 9 );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ], 9 );

		add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'register_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_styles' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_styles' ], 9 );

		add_action( 'elementor/editor/footer', [ $this, 'print_templates' ] );
		add_action( 'admin_footer', [ $this, 'print_templates' ] );
		add_action( 'wp_footer', [ $this, 'print_templates' ] );
	}

	/**
	 * Init components
	 *
	 * Initializing common components.
	 *
	 * @since 2.3.0
	 * @access public
	 */
	public function init_components() {
		$this->add_component( 'ajax', new Ajax() );

		if ( current_user_can( 'manage_options' ) ) {
			if ( ! is_customize_preview() ) {
				$this->add_component( 'finder', new Finder() );
			}
		}

		$this->add_component( 'connect', new Connect() );

		$this->add_component( 'event-tracker', new Event_Tracker() );

		Plugin::$instance->experiments->add_feature( Events_Manager::get_experimental_data() );

		if ( Plugin::$instance->experiments->is_feature_active( Events_Manager::EXPERIMENT_NAME ) ) {
			$this->add_component( 'events-manager', new Events_Manager() );
		}
	}

	/**
	 * Get name.
	 *
	 * Retrieve the app name.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @return string Common app name.
	 */
	public function get_name() {
		return 'common';
	}

	/**
	 * Register scripts.
	 *
	 * Register common scripts.
	 *
	 * @since 2.3.0
	 * @access public
	 */
	public function register_scripts() {
		wp_register_script(
			'elementor-common-modules',
			$this->get_js_assets_url( 'common-modules' ),
			[],
			ELEMENTOR_VERSION,
			true
		);

		wp_register_script(
			'backbone-marionette',
			$this->get_js_assets_url( 'backbone.marionette', 'assets/lib/backbone/' ),
			[
				'backbone',
			],
			'2.4.5.e1',
			true
		);

		wp_register_script(
			'backbone-radio',
			$this->get_js_assets_url( 'backbone.radio', 'assets/lib/backbone/' ),
			[
				'backbone',
			],
			'1.0.4',
			true
		);

		wp_register_script(
			'elementor-dialog',
			$this->get_js_assets_url( 'dialog', 'assets/lib/dialog/' ),
			[
				'jquery-ui-position',
			],
			'4.9.0',
			true
		);

		wp_enqueue_script(
			'elementor-common',
			$this->get_js_assets_url( 'common' ),
			[
				'jquery',
				'jquery-ui-draggable',
				'backbone-marionette',
				'backbone-radio',
				'elementor-common-modules',
				'elementor-web-cli',
				'elementor-dialog',
				'wp-api-request',
				'elementor-dev-tools',
			],
			ELEMENTOR_VERSION,
			true
		);

		wp_set_script_translations( 'elementor-common', 'elementor' );

		$this->print_config();

		// Used for external plugins.
		do_action( 'elementor/common/after_register_scripts', $this );
	}

	/**
	 * Register styles.
	 *
	 * Register common styles.
	 *
	 * @since 2.3.0
	 * @access public
	 */
	public function register_styles() {
		wp_register_style(
			'elementor-icons',
			$this->get_css_assets_url( 'elementor-icons', 'assets/lib/eicons/css/' ),
			[],
			Icons_Manager::ELEMENTOR_ICONS_VERSION
		);

		wp_enqueue_style(
			'elementor-common',
			$this->get_css_assets_url( 'common', null, 'default', true ),
			[
				'elementor-icons',
			],
			ELEMENTOR_VERSION
		);

		wp_enqueue_style(
			'e-theme-ui-light',
			$this->get_css_assets_url( 'theme-light' ),
			[],
			ELEMENTOR_VERSION
		);
	}

	/**
	 * Add template.
	 *
	 * @since 2.3.0
	 * @access public
	 *
	 * @param string $template Can be either a link to template file or template
	 *                         HTML content.
	 * @param string $type     Optional. Whether to handle the template as path
	 *                         or text. Default is `path`.
	 */
	public function add_template( $template, $type = 'path' ) {
		if ( 'path' === $type ) {
			ob_start();

			include $template;

			$template = ob_get_clean();
		}

		$this->templates[] = $template;
	}

	/**
	 * Print Templates
	 *
	 * Prints all registered templates.
	 *
	 * @since 2.3.0
	 * @access public
	 */
	public function print_templates() {
		foreach ( $this->templates as $template ) {
			echo $template; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Get init settings.
	 *
	 * Define the default/initial settings of the common app.
	 *
	 * @since 2.3.0
	 * @access protected
	 *
	 * @return array
	 */
	protected function get_init_settings() {
		$active_experimental_features = Plugin::$instance->experiments->get_active_features();
		$all_experimental_features = Plugin::$instance->experiments->get_features();

		$active_experimental_features = array_fill_keys( array_keys( $active_experimental_features ), true );
		$all_experimental_features = array_map(
			function( $feature ) {
				return Plugin::$instance->experiments->is_feature_active( $feature['name'] );
			},
			$all_experimental_features
		);

		$config = [
			'version' => ELEMENTOR_VERSION,
			'isRTL' => is_rtl(),
			'isDebug' => ( defined( 'WP_DEBUG' ) && WP_DEBUG ),
			'isElementorDebug' => Utils::is_elementor_debug(),
			'activeModules' => array_keys( $this->get_components() ),
			'experimentalFeatures' => $active_experimental_features,
			'allExperimentalFeatures' => $all_experimental_features,
			'urls' => [
				'assets' => ELEMENTOR_ASSETS_URL,
				'rest' => get_rest_url(),
			],
			'filesUpload' => [
				'unfilteredFiles' => Uploads_Manager::are_unfiltered_uploads_enabled(),
			],
			'editor_events' => Events_Manager::get_editor_events_config(),
		];

		/**
		 * Localize common settings.
		 *
		 * Filters the editor localized settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $config  Common configuration.
		 */
		return apply_filters( 'elementor/common/localize_settings', $config );
	}

	/**
	 * Add default templates.
	 *
	 * Register common app default templates.
	 *
	 * @since 2.3.0
	 * @access private
	 */
	private function add_default_templates() {
		$default_templates = [
			'includes/editor-templates/library-layout.php',
		];

		foreach ( $default_templates as $template ) {
			$this->add_template( ELEMENTOR_PATH . $template );
		}
	}
}
