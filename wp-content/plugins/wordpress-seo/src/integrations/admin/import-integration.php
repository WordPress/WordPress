<?php

namespace Yoast\WP\SEO\Integrations\Admin;

use WPSEO_Admin_Asset_Manager;
use Yoast\WP\SEO\Conditionals\Import_Tool_Selected_Conditional;
use Yoast\WP\SEO\Conditionals\Yoast_Tools_Page_Conditional;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Presenters\Admin\Alert_Presenter;
use Yoast\WP\SEO\Routes\Importing_Route;
use Yoast\WP\SEO\Services\Importing\Importable_Detector_Service;

/**
 * Loads import script when on the Tool's page.
 */
class Import_Integration implements Integration_Interface {

	/**
	 * Contains the asset manager.
	 *
	 * @var WPSEO_Admin_Asset_Manager
	 */
	protected $asset_manager;

	/**
	 * The Importable Detector service.
	 *
	 * @var Importable_Detector_Service
	 */
	protected $importable_detector;

	/**
	 * The Importing Route class.
	 *
	 * @var Importing_Route
	 */
	protected $importing_route;

	/**
	 * Returns the conditionals based on which this loadable should be active.
	 *
	 * @return array
	 */
	public static function get_conditionals() {
		return [
			Import_Tool_Selected_Conditional::class,
			Yoast_Tools_Page_Conditional::class,
		];
	}

	/**
	 * Import Integration constructor.
	 *
	 * @param WPSEO_Admin_Asset_Manager   $asset_manager       The asset manager.
	 * @param Importable_Detector_Service $importable_detector The importable detector.
	 * @param Importing_Route             $importing_route     The importing route.
	 */
	public function __construct(
		WPSEO_Admin_Asset_Manager $asset_manager,
		Importable_Detector_Service $importable_detector,
		Importing_Route $importing_route
	) {
		$this->asset_manager       = $asset_manager;
		$this->importable_detector = $importable_detector;
		$this->importing_route     = $importing_route;
	}

	/**
	 * Initializes the integration.
	 *
	 * This is the place to register hooks and filters.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_import_script' ] );
	}

	/**
	 * Enqueues the Import script.
	 *
	 * @return void
	 */
	public function enqueue_import_script() {
		\wp_enqueue_style( 'dashicons' );
		$this->asset_manager->enqueue_script( 'import' );

		$data = [
			'restApi' => [
				'root'                => \esc_url_raw( \rest_url() ),
				'cleanup_endpoints'   => $this->get_cleanup_endpoints(),
				'importing_endpoints' => $this->get_importing_endpoints(),
				'nonce'               => \wp_create_nonce( 'wp_rest' ),
			],
			'assets'  => [
				'loading_msg_import'       => \esc_html__( 'The import can take a long time depending on your site\'s size.', 'wordpress-seo' ),
				'loading_msg_cleanup'      => \esc_html__( 'The cleanup can take a long time depending on your site\'s size.', 'wordpress-seo' ),
				'note'                     => \esc_html__( 'Note: ', 'wordpress-seo' ),
				'cleanup_after_import_msg' => \esc_html__( 'After you\'ve imported data from another SEO plugin, please make sure to clean up all the original data from that plugin. (step 5)', 'wordpress-seo' ),
				'select_placeholder'       => \esc_html__( 'Select SEO plugin', 'wordpress-seo' ),
				'no_data_msg'              => \esc_html__( 'No data found from other SEO plugins.', 'wordpress-seo' ),
				'validation_failure'       => $this->get_validation_failure_alert(),
				'import_failure'           => $this->get_import_failure_alert( true ),
				'cleanup_failure'          => $this->get_import_failure_alert( false ),
				'spinner'                  => \admin_url( 'images/loading.gif' ),
				'replacing_texts'          => [
					'cleanup_button'       => \esc_html__( 'Clean up', 'wordpress-seo' ),
					'import_explanation'   => \esc_html__( 'Please select an SEO plugin below to see what data can be imported.', 'wordpress-seo' ),
					'cleanup_explanation'  => \esc_html__( 'Once you\'re certain that your site is working properly with the imported data from another SEO plugin, you can clean up all the original data from that plugin.', 'wordpress-seo' ),
					/* translators: %s: expands to the name of the plugin that is selected to be imported */
					'select_header'        => \esc_html__( 'The import from %s includes:', 'wordpress-seo' ),
					'plugins'              => [
						'aioseo' => [
							[
								'data_name' => \esc_html__( 'Post metadata (SEO titles, descriptions, etc.)', 'wordpress-seo' ),
								'data_note' => \esc_html__( 'Note: This metadata will only be imported if there is no existing Yoast SEO metadata yet.', 'wordpress-seo' ),
							],
							[
								'data_name' => \esc_html__( 'Default settings', 'wordpress-seo' ),
								'data_note' => \esc_html__( 'Note: These settings will overwrite the default settings of Yoast SEO.', 'wordpress-seo' ),
							],
						],
						'other' => [
							[
								'data_name' => \esc_html__( 'Post metadata (SEO titles, descriptions, etc.)', 'wordpress-seo' ),
								'data_note' => \esc_html__( 'Note: This metadata will only be imported if there is no existing Yoast SEO metadata yet.', 'wordpress-seo' ),
							],
						],
					],
				],
			],
		];

		/**
		 * Filter: 'wpseo_importing_data' Filter to adapt the data used in the import process.
		 *
		 * @param array $data The import data to adapt.
		 */
		$data = \apply_filters( 'wpseo_importing_data', $data );

		$this->asset_manager->localize_script( 'import', 'yoastImportData', $data );
	}

	/**
	 * Retrieves a list of the importing endpoints to use.
	 *
	 * @return array The endpoints.
	 */
	protected function get_importing_endpoints() {
		$available_actions   = $this->importable_detector->detect_importers();
		$importing_endpoints = [];

		$available_sorted_actions = $this->sort_actions( $available_actions );

		foreach ( $available_sorted_actions as $plugin => $types ) {
			foreach ( $types as $type ) {
				$importing_endpoints[ $plugin ][] = $this->importing_route->get_endpoint( $plugin, $type );
			}
		}

		return $importing_endpoints;
	}

	/**
	 * Sorts the array of importing actions, by moving any validating actions to the start for every plugin.
	 *
	 * @param array $available_actions The array of actions that we want to sort.
	 *
	 * @return array The sorted array of actions.
	 */
	protected function sort_actions( $available_actions ) {
		$first_action             = 'validate_data';
		$available_sorted_actions = [];

		foreach ( $available_actions as $plugin => $plugin_available_actions ) {

			$validate_action_position = \array_search( $first_action, $plugin_available_actions, true );

			if ( ! empty( $validate_action_position ) ) {
				unset( $plugin_available_actions[ $validate_action_position ] );
				\array_unshift( $plugin_available_actions, $first_action );
			}

			$available_sorted_actions[ $plugin ] = $plugin_available_actions;
		}

		return $available_sorted_actions;
	}

	/**
	 * Retrieves a list of the importing endpoints to use.
	 *
	 * @return array The endpoints.
	 */
	protected function get_cleanup_endpoints() {
		$available_actions   = $this->importable_detector->detect_cleanups();
		$importing_endpoints = [];

		foreach ( $available_actions as $plugin => $types ) {
			foreach ( $types as $type ) {
				$importing_endpoints[ $plugin ][] = $this->importing_route->get_endpoint( $plugin, $type );
			}
		}

		return $importing_endpoints;
	}

	/**
	 * Gets the validation failure alert using the Alert_Presenter.
	 *
	 * @return string The validation failure alert.
	 */
	protected function get_validation_failure_alert() {
		$content  = \esc_html__( 'The AIOSEO import was cancelled because some AIOSEO data is missing. Please try and take the following steps to fix this:', 'wordpress-seo' );
		$content .= '<br/>';
		$content .= '<ol><li>';
		$content .= \esc_html__( 'If you have never saved any AIOSEO \'Search Appearance\' settings, please do that first and run the import again.', 'wordpress-seo' );
		$content .= '</li>';
		$content .= '<li>';
		$content .= \esc_html__( 'If you already have saved AIOSEO \'Search Appearance\' settings and the issue persists, please contact our support team so we can take a closer look.', 'wordpress-seo' );
		$content .= '</li></ol>';

		$validation_failure_alert = new Alert_Presenter( $content, 'error' );

		return $validation_failure_alert->present();
	}

	/**
	 * Gets the import failure alert using the Alert_Presenter.
	 *
	 * @param bool $is_import Wether it's an import or not.
	 *
	 * @return string The import failure alert.
	 */
	protected function get_import_failure_alert( $is_import ) {
		$content = \esc_html__( 'Cleanup failed with the following error:', 'wordpress-seo' );
		if ( $is_import ) {
			$content = \esc_html__( 'Import failed with the following error:', 'wordpress-seo' );
		}

		$content .= '<br/><br/>';
		$content .= \esc_html( '%s' );

		$import_failure_alert = new Alert_Presenter( $content, 'error' );

		return $import_failure_alert->present();
	}
}
