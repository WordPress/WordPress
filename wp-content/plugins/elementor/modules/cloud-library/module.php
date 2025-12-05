<?php
namespace Elementor\Modules\CloudLibrary;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Common\Modules\Connect\Module as ConnectModule;
use Elementor\Core\Documents_Manager;
use Elementor\Core\Frontend\Render_Mode_Manager;
use Elementor\Modules\CloudLibrary\Connect\Cloud_Library;
use Elementor\Core\Common\Modules\Connect\Apps\Library;
use Elementor\Core\Experiments\Manager as ExperimentsManager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	/**
	 * @var callable
	 */
	protected $print_preview_callback;

	public function get_name(): string {
		return 'cloud-library';
	}

	public function __construct() {
		parent::__construct();

		$this->register_experiments();

		$this->register_app();

		add_action( 'elementor/init', function () {
			$this->set_cloud_library_settings();
		}, 12 /** After the initiation of the connect cloud library */ );

		add_filter( 'elementor/editor/localize_settings', function ( $settings ) {
			return $this->localize_settings( $settings );
		}, 11 /** After Elementor Core */ );

		add_filter( 'elementor/render_mode/module', function( $module_name ) {
			$render_mode_manager = \Elementor\Plugin::$instance->frontend->render_mode_manager;

			if ( $render_mode_manager ) {
				$current_render_mode = $render_mode_manager->get_current();

				if ( $current_render_mode instanceof \Elementor\Modules\CloudLibrary\Render_Mode_Preview ) {
					return 'cloud-library';
				}
			}

			return $module_name;
		}, 12);

		if ( $this->is_screenshot_proxy_mode( $_GET ) ) { // phpcs:ignore -- Checking nonce inside the method.
			echo $this->get_proxy_data( htmlspecialchars( $_GET['href'] ) ); // phpcs:ignore -- Nonce was checked on the above method
			die;
		}
	}

	public function get_proxy_data( $url ) {
		$response = wp_safe_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return '';
		}

		$content_type = wp_remote_retrieve_headers( $response )->offsetGet( 'content-type' );

		header( 'content-type: ' . $content_type );

		return wp_remote_retrieve_body( $response );
	}

	public function localize_settings( $settings ) {
		if ( isset( $settings['i18n'] ) ) {
			$settings['i18n']['folder'] = esc_html__( 'Folder', 'elementor' );
		}

		$settings['library']['doc_types'] = $this->get_document_types();

		return $settings;
	}

	private function register_experiments() {
		Plugin::$instance->experiments->add_feature( [
			'name' => $this->get_name(),
			'title' => esc_html__( 'Cloud Library', 'elementor' ),
			'release_status' => ExperimentsManager::RELEASE_STATUS_STABLE,
			'default' => ExperimentsManager::STATE_ACTIVE,
			'hidden' => true,
			'mutable' => false,
			'new_site' => [
				'always_active' => true,
				'minimum_installation_version' => '3.32.0',
			],
		] );
	}

	private function register_app() {
		add_action( 'elementor/connect/apps/register', function ( ConnectModule $connect_module ) {
			$connect_module->register_app( 'cloud-library', Cloud_Library::get_class_name() );
		} );

		add_action( 'elementor/frontend/render_mode/register', [ $this, 'register_render_mode' ] );

		add_action( 'elementor/documents/register', function ( Documents_Manager $documents_manager ) {
			$documents_manager->register_document_type(
				Documents\Cloud_Template_Preview::TYPE,
				Documents\Cloud_Template_Preview::get_class_full_name()
			);
		});
	}

	/**
	 * @param Render_Mode_Manager $manager
	 *
	 * @throws \Exception If render mode registration fails.
	 */
	public function register_render_mode( Render_Mode_Manager $manager ) {
		$manager->register_render_mode( Render_Mode_Preview::class );
	}

	private function set_cloud_library_settings() {
		if ( ! Plugin::$instance->common ) {
			return;
		}

		/** @var ConnectModule $connect */
		$connect = Plugin::$instance->common->get_component( 'connect' );

		/** @var Library $library */
		$library = $connect->get_app( 'library' );

		if ( ! $library ) {
			return;
		}

		Plugin::$instance->app->set_settings( 'cloud-library', [
			'library_connect_url'  => esc_url( $library->get_admin_url( 'authorize', [
				'utm_source' => 'template-library',
				'utm_medium' => 'wp-dash',
				'utm_campaign' => 'library-connect',
				'utm_content' => 'cloud-library',
				'source' => 'cloud-library',
			] ) ),
			'library_connect_title_copy' => esc_html__( 'Connect to your Elementor account', 'elementor' ),
			'library_connect_sub_title_copy' => esc_html__( 'Then you can find all your templates in one convenient library.', 'elementor' ),
			'library_connect_button_copy' => esc_html__( 'Connect', 'elementor' ),
		] );
	}

	private function get_document_types() {
		$document_types = Plugin::$instance->documents->get_document_types( [
			'show_in_library' => true,
		] );

		$data = [];

		foreach ( $document_types as $name => $document_type ) {
			$data[ $name ] = $document_type::get_title();
		}

		return $data;
	}

	public function print_content() {
		if ( ! $this->print_preview_callback ) {
			$this->print_preview_callback = [ $this, 'print_thumbnail_preview_callback' ];
		}

		call_user_func( $this->print_preview_callback );
	}

	private function print_thumbnail_preview_callback() {
		$doc = Plugin::$instance->documents->get_current();

		if ( ! $doc ) {
			$render_mode = Plugin::$instance->frontend->render_mode_manager->get_current();
			if ( $render_mode instanceof Render_Mode_Preview ) {
				$doc = $render_mode->get_document();
			}
		}

		if ( ! $doc ) {
			echo '<div class="elementor-alert elementor-alert-danger">' . esc_html__( 'Document not found for preview.', 'elementor' ) . '</div>';
			return;
		}

		Plugin::$instance->documents->switch_to_document( $doc );

		$content = $doc->get_content( true );

		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}


	protected function is_screenshot_proxy_mode( array $query_params ) {
		$is_proxy = isset( $query_params['screenshot_proxy'] );

		if ( $is_proxy ) {
			if ( ! wp_verify_nonce( $query_params['nonce'], 'screenshot-proxy' ) ) {
				// WP >= 6.2-alpha
				if ( class_exists( '\WpOrg\Requests\Exception\Http\Status403' ) ) {
					throw new \WpOrg\Requests\Exception\Http\Status403();
				} else {
					throw new \Requests_Exception_HTTP_403();
				}
			}

			if ( ! $query_params['href'] ) {
				// WP >= 6.2-alpha
				if ( class_exists( '\WpOrg\Requests\Exception\Http\Status400' ) ) {
					throw new \WpOrg\Requests\Exception\Http\Status400();
				} else {
					throw new \Requests_Exception_HTTP_400();
				}
			}
		}

		return $is_proxy;
	}
}
