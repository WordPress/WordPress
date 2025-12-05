<?php
namespace Elementor\App\Modules\Onboarding;

use Automatic_Upgrader_Skin;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Common\Modules\Ajax\Module as Ajax;
use Elementor\Core\Common\Modules\Connect\Apps\Library;
use Elementor\Core\Files\Uploads_Manager;
use Elementor\Includes\EditorAssetsAPI;
use Elementor\Plugin;
use Elementor\Utils;
use Plugin_Upgrader;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Onboarding Module
 *
 * Responsible for initializing Elementor App functionality
 *
 * @since 3.6.0
 */
class Module extends BaseModule {

	const VERSION = '1.0.0';
	const ONBOARDING_OPTION = 'elementor_onboarded';

	private ?API $editor_assets_api = null;

	/**
	 * Get name.
	 *
	 * @since 3.6.0
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'onboarding';
	}

	private function is_theme_selection_experiment_enabled() {
		$editor_assets_api = $this->get_editor_assets_api();

		if ( null === $editor_assets_api ) {
			return false;
		}

		return $editor_assets_api->is_theme_selection_experiment_enabled();
	}

	private function is_good_to_go_experiment_enabled() {
		$editor_assets_api = $this->get_editor_assets_api();

		if ( null === $editor_assets_api ) {
			return false;
		}

		return $editor_assets_api->is_good_to_go_experiment_enabled();
	}

	private function get_editor_assets_api(): ?API {
		if ( null !== $this->editor_assets_api ) {
			return $this->editor_assets_api;
		}

		$editor_assets_api_instance = new EditorAssetsAPI( $this->get_editor_assets_api_config() );
		$this->editor_assets_api = new API( $editor_assets_api_instance );

		return $this->editor_assets_api;
	}

	private function get_editor_assets_api_config(): array {
		return [
			EditorAssetsAPI::ASSETS_DATA_URL => 'https://assets.elementor.com/ab-testing/v1/ab-testing.json',
			EditorAssetsAPI::ASSETS_DATA_TRANSIENT_KEY => '_elementor_ab_testing_data',
			EditorAssetsAPI::ASSETS_DATA_KEY => 'ab-testing',
		];
	}

	/**
	 * Set Onboarding Settings
	 *
	 * Creates an array of module settings that is localized into the JS App config.
	 *
	 * @since 3.6.0
	 */
	private function set_onboarding_settings() {
		if ( ! Plugin::$instance->common ) {
			return;
		}

		// Get the published pages and posts
		$pages_and_posts = new \WP_Query( [
			'post_type' => [ 'page', 'post' ],
			'post_status' => 'publish',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows' => true,
		] );

		$custom_site_logo_id = get_theme_mod( 'custom_logo' );
		$custom_logo_src = wp_get_attachment_image_src( $custom_site_logo_id, 'full' );
		$site_name = get_option( 'blogname', '' );

		$hello_theme = wp_get_theme( 'hello-elementor' );
		$hello_theme_errors = is_object( $hello_theme->errors() ) ? $hello_theme->errors()->errors : [];

		/** @var Library $library */
		$library = Plugin::$instance->common->get_component( 'connect' )->get_app( 'library' );

		Plugin::$instance->app->set_settings( 'onboarding', [
			'eventPlacement' => 'Onboarding wizard',
			'onboardingAlreadyRan' => get_option( self::ONBOARDING_OPTION ),
			'onboardingVersion' => self::VERSION,
			'isLibraryConnected' => $library->is_connected(),
			// Used to check if the Hello Elementor theme is installed but not activated.
			'helloInstalled' => empty( $hello_theme_errors['theme_not_found'] ),
			'helloActivated' => 'hello-elementor' === get_option( 'template' ),
			// The "Use Hello theme on my site" checkbox should be checked by default only if this condition is met.
			'helloOptOut' => count( $pages_and_posts->posts ) < 5,
			'siteName' => esc_html( $site_name ),
			'isUnfilteredFilesEnabled' => Uploads_Manager::are_unfiltered_uploads_enabled(),
			'urls' => [
				'kitLibrary' => Plugin::$instance->app->get_base_url() . '&source=onboarding#/kit-library?order[direction]=desc&order[by]=featuredIndex',
				'sitePlanner' => add_query_arg( [
					'type' => 'editor',
					'siteUrl' => esc_url( home_url() ),
					'siteName' => esc_html( $site_name ),
					'siteDescription' => esc_html( get_bloginfo( 'description' ) ),
					'siteLanguage' => get_locale(),
				], 'https://planner.elementor.com/onboarding.html' ),
				'createNewPage' => Plugin::$instance->documents->get_create_new_post_url(),
				'connect' => $library->get_admin_url( 'authorize', [
					'utm_source' => 'onboarding-wizard',
					'utm_campaign' => 'connect-account',
					'utm_medium' => 'wp-dash',
					'utm_term' => self::VERSION,
					'source' => 'generic',
				] ),
				'upgrade' => 'https://go.elementor.com/go-pro-onboarding-wizard-upgrade/',
				'signUp' => $library->get_admin_url( 'authorize', [
					'utm_source' => 'onboarding-wizard',
					'utm_campaign' => 'connect-account',
					'utm_medium' => 'wp-dash',
					'utm_term' => self::VERSION,
					'source' => 'generic',
					'screen_hint' => 'signup',
				] ),
				'uploadPro' => Plugin::$instance->app->get_base_url() . '#/onboarding/uploadAndInstallPro?mode=popup',
			],
			'siteLogo' => [
				'id' => $custom_site_logo_id,
				'url' => $custom_logo_src ? $custom_logo_src[0] : '',
			],
			'utms' => [
				'connectTopBar' => '&utm_content=top-bar',
				'connectCta' => '&utm_content=cta-button',
				'connectCtaLink' => '&utm_content=cta-link',
				'downloadPro' => '?utm_source=onboarding-wizard&utm_campaign=my-account-subscriptions&utm_medium=wp-dash&utm_content=import-pro-plugin&utm_term=' . self::VERSION,
			],
			'nonce' => wp_create_nonce( 'onboarding' ),
			'experiment' => true,
			'themeSelectionExperimentEnabled' => $this->is_theme_selection_experiment_enabled(),
			'goodToGoExperimentEnabled' => $this->is_good_to_go_experiment_enabled(),
		] );
	}

	/**
	 * Get Permission Error Response
	 *
	 * Returns the response that is returned when the user's capabilities are not sufficient for performing an action.
	 *
	 * @since 3.6.4
	 *
	 * @return array
	 */
	private function get_permission_error_response() {
		return [
			'status' => 'error',
			'payload' => [
				'error_message' => esc_html__( 'You do not have permission to perform this action.', 'elementor' ),
			],
		];
	}

	/**
	 * Maybe Update Site Logo
	 *
	 * If a new name is provided, it will be updated as the Site Name.
	 *
	 * @since 3.6.0
	 *
	 * @return array
	 */
	private function maybe_update_site_name() {
		$problem_error = [
			'status' => 'error',
			'payload' => [
				'error_message' => esc_html__( 'There was a problem setting your site name.', 'elementor' ),
			],
		];

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( empty( $_POST['data'] ) ) {
			return $problem_error;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$data = json_decode( Utils::get_super_global_value( $_POST, 'data' ), true );

		if ( ! isset( $data['siteName'] ) ) {
			return $problem_error;
		}

		/**
		 * Onboarding Site Name
		 *
		 * Filters the new site name passed by the user to update in Elementor's onboarding process.
		 * Elementor runs `esc_html()` on the Site Name passed by the user for security reasons. If a user wants to
		 * include special characters in their site name, they can use this filter to override it.
		 *
		 * @since 3.6.0
		 *
		 * @param string Escaped new site name
		 */
		$new_site_name = apply_filters( 'elementor/onboarding/site-name', $data['siteName'] );

		// The site name is sanitized in `update_options()`
		update_option( 'blogname', $new_site_name );

		return [
			'status' => 'success',
			'payload' => [
				'siteNameUpdated' => true,
			],
		];
	}

	/**
	 * Maybe Update Site Logo
	 *
	 * If an image attachment ID is provided, it will be updated as the Site Logo Theme Mod.
	 *
	 * @since 3.6.0
	 *
	 * @return array
	 */
	private function maybe_update_site_logo() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return $this->get_permission_error_response();
		}

		$data_error = [
			'status' => 'error',
			'payload' => [
				'error_message' => esc_html__( 'There was a problem setting your site logo.', 'elementor' ),
			],
		];

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( empty( $_POST['data'] ) ) {
			return $data_error;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$data = json_decode( Utils::get_super_global_value( $_POST, 'data' ), true );

		// If there is no attachment ID passed or it is not a valid ID, exit here.
		if ( empty( $data['attachmentId'] ) ) {
			return $data_error;
		}

		$absint_attachment_id = absint( $data['attachmentId'] );

		if ( 0 === $absint_attachment_id ) {
			return $data_error;
		}

		$attachment_url = wp_get_attachment_url( $data['attachmentId'] );

		// Check if the attachment exists. If it does not, exit here.
		if ( ! $attachment_url ) {
			return $data_error;
		}

		set_theme_mod( 'custom_logo', $absint_attachment_id );

		return [
			'status' => 'success',
			'payload' => [
				'siteLogoUpdated' => true,
			],
		];
	}

	/**
	 * Maybe Upload Logo Image
	 *
	 * If an image file upload is provided, and it passes validation, it will be uploaded to the site's Media Library.
	 *
	 * @since 3.6.0
	 *
	 * @return array
	 */
	private function maybe_upload_logo_image() {
		$error_message = esc_html__( 'There was a problem uploading your file.', 'elementor' );

		$file = Utils::get_super_global_value( $_FILES, 'fileToUpload' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! is_array( $file ) || empty( $file['type'] ) ) {
			return [
				'status' => 'error',
				'payload' => [
					'error_message' => $error_message,
				],
			];
		}

		// If the user has allowed it, set the Request's state as an "Elementor Upload" request, in order to add
		// support for non-standard file uploads.
		if ( 'image/svg+xml' === $file['type'] ) {
			if ( Uploads_Manager::are_unfiltered_uploads_enabled() ) {
				Plugin::$instance->uploads_manager->set_elementor_upload_state( true );
			} else {
				wp_send_json_error( 'To upload SVG files, you must allow uploading unfiltered files.' );
			}
		}

		// If the image is an SVG file, sanitation is performed during the import (upload) process.
		$image_attachment = Plugin::$instance->templates_manager->get_import_images_instance()->import( $file );

		if ( 'image/svg+xml' === $file['type'] && Uploads_Manager::are_unfiltered_uploads_enabled() ) {
			// Reset Upload state.
			Plugin::$instance->uploads_manager->set_elementor_upload_state( false );
		}

		if ( $image_attachment && ! is_wp_error( $image_attachment ) ) {
			$result = [
				'status' => 'success',
				'payload' => [
					'imageAttachment' => $image_attachment,
				],
			];
		} else {
			$result = [
				'status' => 'error',
				'payload' => [
					'error_message' => $error_message,
				],
			];
		}

		return $result;
	}

	/**
	 * Activate Hello Theme
	 *
	 * @since 3.6.0
	 *
	 * @return array
	 */
	private function maybe_activate_hello_theme() {
		if ( ! current_user_can( 'switch_themes' ) ) {
			return $this->get_permission_error_response();
		}

		$theme_slug = Utils::get_super_global_value( $_POST, 'theme_slug' ) ?? 'hello-biz'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$allowed_themes = [ 'hello-elementor', 'hello-biz' ];
		if ( ! in_array( $theme_slug, $allowed_themes, true ) ) {
			$theme_slug = 'hello-biz';
		}

		switch_theme( $theme_slug );

		return [
			'status' => 'success',
			'payload' => [
				'helloThemeActivated' => true,
			],
		];
	}

	/**
	 * Upload and Install Elementor Pro
	 *
	 * @since 3.6.0
	 *
	 * @return array
	 */
	private function upload_and_install_pro() {
		if ( ! current_user_can( 'install_plugins' ) || ! current_user_can( 'activate_plugins' ) ) {
			return $this->get_permission_error_response();
		}

		$error_message = esc_html__( 'There was a problem uploading your file.', 'elementor' );

		$file = Utils::get_super_global_value( $_FILES, 'fileToUpload' ) ?? []; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( ! is_array( $file ) || empty( $file['type'] ) ) {
			return [
				'status' => 'error',
				'payload' => [
					'error_message' => $error_message,
				],
			];
		}

		$result = [];

		if ( ! class_exists( 'Automatic_Upgrader_Skin' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$skin = new Automatic_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$upload_result = $upgrader->install( $file['tmp_name'], [ 'overwrite_package' => false ] );

		if ( ! $upload_result || is_wp_error( $upload_result ) ) {
			$result = [
				'status' => 'error',
				'payload' => [
					'error_message' => $error_message,
				],
			];
		} else {
			$activated = activate_plugin( WP_PLUGIN_DIR . '/elementor-pro/elementor-pro.php', false, false, true );

			if ( ! is_wp_error( $activated ) ) {
				$result = [
					'status' => 'success',
					'payload' => [
						'elementorProInstalled' => true,
					],
				];
			} else {
				$result = [
					'status' => 'error',
					'payload' => [
						'error_message' => $error_message,
						'elementorProInstalled' => false,
					],
				];
			}
		}

		return $result;
	}

	private function maybe_update_onboarding_db_option() {
		$db_option = get_option( self::ONBOARDING_OPTION );

		if ( ! $db_option ) {
			update_option( self::ONBOARDING_OPTION, true );
		}

		return [
			'status' => 'success',
			'payload' => 'onboarding DB',
		];
	}

	/**
	 * Maybe Handle Ajax
	 *
	 * This method checks if there are any AJAX actions being
	 *
	 * @since 3.6.0
	 */
	private function maybe_handle_ajax() {
		$result = [];

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		switch ( Utils::get_super_global_value( $_POST, 'action' ) ) {
			case 'elementor_update_site_name':
				// If no value is passed for any reason, no need to update the site name.
				$result = $this->maybe_update_site_name();
				break;
			case 'elementor_update_site_logo':
				$result = $this->maybe_update_site_logo();
				break;
			case 'elementor_upload_site_logo':
				$result = $this->maybe_upload_logo_image();
				break;
			case 'elementor_activate_hello_theme':
				$result = $this->maybe_activate_hello_theme();
				break;
			case 'elementor_upload_and_install_pro':
				$result = $this->upload_and_install_pro();
				break;
			case 'elementor_update_onboarding_option':
				$result = $this->maybe_update_onboarding_db_option();
				break;
			case 'elementor_save_onboarding_features':
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				$result = $this->get_component( 'features_usage' )->save_onboarding_features( Utils::get_super_global_value( $_POST, 'data' ) ?? [] );
		}

		if ( ! empty( $result ) ) {
			if ( 'success' === $result['status'] ) {
				wp_send_json_success( $result['payload'] );
			} else {
				wp_send_json_error( $result['payload'] );
			}
		}
	}

	public function __construct() {
		$this->add_component( 'features_usage', new Features_Usage() );

		add_action( 'elementor/init', function() {
			// Only load when viewing the onboarding app.
			if ( Plugin::$instance->app->is_current() ) {
				$this->set_onboarding_settings();
				// Needed for installing the Hello Elementor theme.
				wp_enqueue_script( 'updates' );
				// Needed for uploading Logo from WP Media Library.
				wp_enqueue_media();
			}
		}, 12 );

		// Needed for uploading Logo from WP Media Library. The 'admin_menu' hook is used because it runs before
		// 'admin_init', and the App triggers printing footer scripts on 'admin_init' at priority 0.
		add_action( 'admin_menu', function () {
			add_action( 'wp_print_footer_scripts', function () {
				if ( function_exists( 'wp_print_media_templates' ) ) {
					wp_print_media_templates();
				}
			} );
		} );

		add_action( 'admin_init', function() {
			if ( wp_doing_ajax() &&
				isset( $_POST['action'] ) &&
				isset( $_POST['_nonce'] ) &&
				wp_verify_nonce( Utils::get_super_global_value( $_POST, '_nonce' ), Ajax::NONCE_KEY ) &&
				current_user_can( 'manage_options' )
			) {
				$this->maybe_handle_ajax();
			}
		} );

		$this->get_component( 'features_usage' )->register();
	}
}
