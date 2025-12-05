<?php
namespace Elementor\App\Modules\ImportExportCustomization;

use Elementor\App\Modules\ImportExportCustomization\Processes\Export;
use Elementor\App\Modules\ImportExportCustomization\Processes\Import;
use Elementor\App\Modules\ImportExportCustomization\Processes\Revert;
use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Files\Uploads_Manager;
use Elementor\Modules\CloudKitLibrary\Module as CloudKitLibrary;
use Elementor\Modules\System_Info\Reporters\Server;
use Elementor\Plugin;
use Elementor\Tools;
use Elementor\Utils as ElementorUtils;
use Elementor\App\Modules\ImportExportCustomization\Utils as ImportExportUtils;
use Elementor\App\Modules\ImportExportCustomization\Data\Controller;
use Elementor\Core\Settings\Manager as SettingsManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Import Export Module
 *
 * Responsible for initializing Elementor App functionality
 */
class Module extends BaseModule {
	const FORMAT_VERSION = '3.0';

	const REFERRER_KIT_LIBRARY = 'kit-library';

	const REFERRER_LOCAL = 'local';

	const REFERRER_CLOUD = 'cloud';

	const PLUGIN_PERMISSIONS_ERROR_KEY = 'plugin-installation-permissions-error';

	const KIT_LIBRARY_ERROR_KEY = 'invalid-kit-library-zip-error';

	const CLOUD_KIT_LIBRARY_ERROR_LOADING_RESOURCE = 'error-loading-resource';

	const NO_WRITE_PERMISSIONS_KEY = 'no-write-permissions';

	const THIRD_PARTY_ERROR = 'third-party-error';

	const DOMDOCUMENT_MISSING = 'domdocument-missing';

	const MEDIA_PROCESSING_ERROR = 'media-processing-error';

	const OPTION_KEY_ELEMENTOR_IMPORT_SESSIONS = 'elementor_import_sessions';

	const OPTION_KEY_ELEMENTOR_REVERT_SESSIONS = 'elementor_revert_sessions';

	const META_KEY_ELEMENTOR_IMPORT_SESSION_ID = '_elementor_import_session_id';

	const META_KEY_ELEMENTOR_EDIT_MODE = '_elementor_edit_mode';
	const IMPORT_PLUGINS_ACTION = 'import-plugins';
	const EXPORT_SOURCE_CLOUD = 'cloud';
	const EXPORT_SOURCE_FILE = 'file';

	/**
	 * Assigning the export process to a property, so we can use the process from outside the class.
	 *
	 * @var Export
	 */
	public $export;

	/**
	 * Assigning the import process to a property, so we can use the process from outside the class.
	 *
	 * @var Import
	 */
	public $import;

	/**
	 * Assigning the revert process to a property, so we can use the process from outside the class.
	 *
	 * @var Revert
	 */
	public $revert;

	/**
	 * Get name.
	 *
	 * @access public
	 *
	 * @return string
	 */
	public function get_name() {
		return 'import-export-customization';
	}

	public function __construct() {
		$this->register_actions();

		Controller::register_hooks();

		if ( ElementorUtils::is_wp_cli() ) {
			\WP_CLI::add_command( 'elementor kit', WP_CLI::class );
		}

		( new Usage() )->register();

		$this->revert = new Revert();
	}

	public function get_init_settings() {
		if ( ! Plugin::$instance->app->is_current() ) {
			return [];
		}

		return $this->get_config_data();
	}

	/**
	 * Register the import/export tab in elementor tools.
	 */
	public function register_settings_tab( Tools $tools ) {
		$tools->add_tab( 'import-export-kit', [
			'label' => esc_html__( 'Website Templates', 'elementor' ),
			'sections' => [
				'intro' => [
					'label' => esc_html__( 'Website Templates', 'elementor' ),
					'callback' => function() {
						$this->render_import_export_tab_content();
					},
					'fields' => [],
				],
			],
		] );
	}

	/**
	 * Render the import/export tab content.
	 */
	private function render_import_export_tab_content() {
		$is_cloud_kits_available = CloudKitLibrary::get_app()->check_eligibility()['is_eligible'];

		$content_data = [
			'export' => [
				'title' => esc_html__( 'Export this website', 'elementor' ),
				'button' => [
					'url' => Plugin::$instance->app->get_base_url() . '#/export-customization',
					'text' => esc_html__( 'Export', 'elementor' ),
					'id' => 'elementor-import-export__export',
				],
				'description' => esc_html__( 'You can download this website as a .zip file, or upload it to the library.', 'elementor' ),
			],
			'import' => [
				'title' => esc_html__( 'Apply a Website Template', 'elementor' ),
				'button' => [
					'url' => Plugin::$instance->app->get_base_url() . '#/import-customization',
					'text' => $is_cloud_kits_available ? esc_html__( 'Upload .zip file', 'elementor' ) : esc_html__( 'Import', 'elementor' ),
					'id' => 'elementor-import-export__import',
				],
				'description' => esc_html__( 'You can import design and settings from a .zip file or choose from the library.', 'elementor' ),
			],
		];

		if ( $is_cloud_kits_available ) {
			$content_data['import']['button_secondary'] = [
				'url' => Plugin::$instance->app->get_base_url() . '#/kit-library/cloud',
				'text' => esc_html__( 'Import from library', 'elementor' ),
				'id' => 'elementor-import-export__import_from_library',
			];
		}

		$last_imported_kit = $this->revert->get_last_import_session();
		$penultimate_imported_kit = $this->revert->get_penultimate_import_session();

		$user_date_format = get_option( 'date_format' );
		$user_time_format = get_option( 'time_format' );
		$date_format = $user_date_format . ' ' . $user_time_format;

		$should_show_revert_section = ! empty( $last_imported_kit );

		if ( $should_show_revert_section ) {
			if ( ! empty( $penultimate_imported_kit ) ) {
				$revert_text = sprintf(
					/* translators: 1: kit title, 2: date, 3: line break, 4: kit title, 5: date. */
					esc_html__( 'Remove all the content and site settings that came with "%1$s" on %2$s %3$s and revert to the site setting that came with "%4$s" on %5$s.', 'elementor' ),
					! empty( $last_imported_kit['kit_title'] ) ? $last_imported_kit['kit_title'] : esc_html__( 'imported kit', 'elementor' ),
					gmdate( $date_format, $last_imported_kit['start_timestamp'] ),
					'<br>',
					! empty( $penultimate_imported_kit['kit_title'] ) ? $penultimate_imported_kit['kit_title'] : esc_html__( 'imported kit', 'elementor' ),
					gmdate( $date_format, $penultimate_imported_kit['start_timestamp'] )
				);
			} else {
				$revert_text = sprintf(
					/* translators: 1: kit title, 2: date, 3: line break */
					esc_html__( 'Remove all the content and site settings that came with "%1$s" on %2$s.%3$s Your original site settings will be restored.', 'elementor' ),
					! empty( $last_imported_kit['kit_title'] ) ? $last_imported_kit['kit_title'] : esc_html__( 'imported kit', 'elementor' ),
					gmdate( $date_format, $last_imported_kit['start_timestamp'] ),
					'<br>'
				);
			}
		}
		?>

		<div class="tab-import-export-kit__content">
			<p class="tab-import-export-kit__info">
				<?php
				printf(
					'%1$s <a href="https://go.elementor.com/wp-dash-import-export-general/" target="_blank">%2$s</a>',
					esc_html__( 'Here’s where you can export this website as a .zip file, upload it to the cloud, or start the process of applying an existing template to your site.', 'elementor' ),
					esc_html__( 'Learn more', 'elementor' ),
				);
				?>
			</p>

			<div class="tab-import-export-kit__wrapper">
				<?php foreach ( $content_data as $data ) {
					$this->print_item_content( $data );
				} ?>
			</div>

			<?php
			if ( $should_show_revert_section ) {

				$link_attributes = [
					'href' => $this->get_revert_href(),
					'id' => 'elementor-import-export__revert_kit',
					'class' => 'button',
				];
				?>
				<div class="tab-import-export-kit__revert">
					<h2>
						<?php echo esc_html__( 'Remove the most recent Website Template', 'elementor' ); ?>
					</h2>
					<p class="tab-import-export-kit__info">
						<?php ElementorUtils::print_unescaped_internal_string( $revert_text ); ?>
					</p>
					<?php $this->render_last_kit_thumbnail( $last_imported_kit ); ?>
					<a <?php ElementorUtils::print_html_attributes( $link_attributes ); ?> >
						<?php echo esc_html__( 'Remove Website Template', 'elementor' ); ?>
					</a>
				</div>
			<?php } ?>
		</div>
		<?php
	}

	private function print_item_content( $data ) {
		?>
		<div class="tab-import-export-kit__container">
			<div class="tab-import-export-kit__box">
				<h2><?php ElementorUtils::print_unescaped_internal_string( $data['title'] ); ?></h2>
			</div>
			<p class="description"><?php ElementorUtils::print_unescaped_internal_string( $data['description'] ); ?></p>

			<?php if ( ! empty( $data['link'] ) ) : ?>
				<a href="<?php ElementorUtils::print_unescaped_internal_string( $data['link']['url'] ); ?>" target="_blank"><?php ElementorUtils::print_unescaped_internal_string( $data['link']['text'] ); ?></a>
			<?php endif; ?>
			<div class="tab-import-export-kit__box action-buttons">
				<?php if ( ! empty( $data['button_secondary'] ) ) : ?>
					<a id="<?php ElementorUtils::print_unescaped_internal_string( $data['button_secondary']['id'] ); ?>" href="<?php ElementorUtils::print_unescaped_internal_string( $data['button_secondary']['url'] ); ?>" class="elementor-button e-btn-txt e-btn-txt-border">
						<?php ElementorUtils::print_unescaped_internal_string( $data['button_secondary']['text'] ); ?>
					</a>
				<?php endif; ?>
				<a <?php ElementorUtils::print_html_attributes( [ 'id' => $data['button']['id'] ] ); ?> href="<?php ElementorUtils::print_unescaped_internal_string( $data['button']['url'] ); ?>" class="elementor-button e-primary">
					<?php ElementorUtils::print_unescaped_internal_string( $data['button']['text'] ); ?>
				</a>
			</div>
		</div>
		<?php
	}

	private function get_revert_href(): string {
		$current_url = add_query_arg( null, null );
		return $this->maybe_add_referrer_param( $current_url );
	}

	/**
	 * Checks if referred by a kit and adds the referrer ID to the href
	 *
	 * @param string $href
	 *
	 * @return string
	 */
	private function maybe_add_referrer_param( string $href ): string {
		$param_name = 'referrer_kit';

		if ( empty( $_GET[ $param_name ] ) ) {
			return $href;
		}

		return add_query_arg( $param_name, sanitize_key( $_GET[ $param_name ] ), $href );
	}

	/**
	 * Get referrer kit ID from current request
	 *
	 * @return string
	 */
	private function get_referrer_kit_id_from_request(): string {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Called via REST API with its own authentication
		return sanitize_key( $_GET['referrer_kit'] ?? '' );
	}

	/**
	 * Render the last kit thumbnail if exists
	 *
	 * @param $last_imported_kit
	 *
	 * @return void
	 */
	private function render_last_kit_thumbnail( $last_imported_kit ) {
		if ( empty( $last_imported_kit['kit_thumbnail'] ) ) {
			return;
		}

		?>
		<div class="tab-import-export-kit__kit-item-row">
			<article class="tab-import-export-kit__kit-item">
				<header>
					<h3>
						<?php echo esc_html( $last_imported_kit['kit_title'] ); ?>
					</h3>
				</header>
				<img
					src="<?php echo esc_url( $last_imported_kit['kit_thumbnail'] ); ?>"
					alt="<?php echo esc_attr( $last_imported_kit['kit_title'] ); ?>"
					loading="lazy"
				>
			</article>
		</div>
		<?php
	}

	/**
	 * Upload a kit zip file and get the kit data.
	 *
	 * Assigning the Import process to the 'import' property,
	 * so it will be available to use in different places such as: WP_Cli, Pro, etc.
	 *
	 * @param string $file Path to the file.
	 * @param string $referrer Referrer of the file 'local' or 'kit-library'.
	 * @param string $kit_id
	 * @return array
	 * @throws \Exception If customization validation fails or processing errors occur.
	 */
	public function upload_kit( $file, $referrer, $kit_id = null ) {
		$this->ensure_writing_permissions();

		$this->import = new Import( $file, [
			'referrer' => $referrer,
			'id' => $kit_id,
		] );

		$this->save_upload_session_data();

		return [
			'session' => $this->import->get_session_id(),
			'manifest' => $this->import->get_manifest(),
			'conflicts' => $this->import->get_settings_conflicts(),
		];
	}

	/**
	 * Import a kit by session_id.
	 * Upload and import a kit by kit zip file.
	 *
	 * If the split_to_chunks flag is true, the process won't start
	 * It will initialize the import process and return the session_id and the runners.
	 *
	 * Assigning the Import process to the 'import' property,
	 * so it will be available to use in different places such as: WP_Cli, Pro, etc.
	 *
	 * @param string $path Path to the file or session_id.
	 * @param array  $settings Settings the import use to determine which content to import.
	 *               (e.g: include, selected_plugins, selected_cpt, selected_override_conditions, etc.)
	 * @param bool   $split_to_chunks Determine if the import process should be split into chunks.
	 * @return array
	 * @throws \Exception If export configuration is invalid or processing fails.
	 */
	public function import_kit( string $path, array $settings, bool $split_to_chunks = false ): array {
		$this->ensure_writing_permissions();
		$this->ensure_DOMDocument_exists();

		$this->import = new Import( $path, $settings );
		$this->import->register_default_runners();

		remove_filter( 'elementor/document/save/data', [ Plugin::$instance->modules_manager->get_modules( 'content-sanitizer' ), 'sanitize_content' ] );
		do_action( 'elementor/import-export-customization/import-kit', $this->import );

		if ( $split_to_chunks ) {
			$this->import->init_import_session( true );

			return [
				'session' => $this->import->get_session_id(),
				'runners' => $this->import->get_runners_name(),
			];
		}

		return $this->import->run();
	}

	private function save_upload_session_data(): void {
		$this->import->init_import_session();
	}

	/**
	 * Resuming import process by re-creating the import instance and running the specific runner.
	 *
	 * @param string $session_id The id off the import session.
	 * @param string $runner_name The specific runner that we want to run.
	 *
	 * @return array Two types of response.
	 *      1. The status and the runner name.
	 *      2. The imported data. (Only if the runner is the last one in the import process)
	 * @throws \Exception If export configuration is invalid or processing fails.
	 */
	public function import_kit_by_runner( string $session_id, string $runner_name ): array {
		// Check session_id
		$this->import = Import::from_session( $session_id );
		$runners = $this->import->get_runners_name();

		$run = $this->import->run_runner( $runner_name );

		if ( end( $runners ) === $run['runner'] ) {
			return $this->import->get_imported_data();
		}

		return $run;
	}

	/**
	 * Export a kit.
	 *
	 * Assigning the Export process to the 'export' property,
	 * so it will be available to use in different places such as: WP_Cli, Pro, etc.
	 *
	 * @param array $settings Settings the export use to determine which content to export.
	 *      (e.g: include, kit_info, selected_plugins, selected_cpt, etc.)
	 * @return array
	 * @throws \Exception If export configuration is invalid or processing fails.
	 */
	public function export_kit( array $settings ) {
		$this->ensure_writing_permissions();

		$this->export = new Export( $settings );
		$this->export->register_default_runners();

		do_action( 'elementor/import-export-customization/export-kit', $this->export );

		return $this->export->run();
	}

	/**
	 * Handle revert kit request.
	 */
	public function revert_last_imported_kit(): array {
		$this->revert = new Revert();
		$this->revert->register_default_runners();

		$import_sessions = Revert::get_import_sessions();

		if ( empty( $import_sessions ) ) {
			return [
				'revert_completed' => false,
				'message' => __( 'No import sessions available to revert.', 'elementor' ),
				'referrer_kit_id' => $this->get_referrer_kit_id_from_request(),
				'show_referrer_dialog' => false,
			];
		}

		do_action( 'elementor/import-export-customization/revert-kit', $this->revert );

		$this->revert->run();

		$referrer_kit_id = $this->get_referrer_kit_id_from_request();

		return [
			'revert_completed' => true,
			'referrer_kit_id' => $referrer_kit_id,
			'show_referrer_dialog' => ! empty( $referrer_kit_id ),
		];
	}

	/**
	 * Register appropriate actions.
	 */
	private function register_actions() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );

		$page_id = Tools::PAGE_ID;

		add_action( "elementor/admin/after_create_settings/{$page_id}", [ $this, 'register_settings_tab' ] );

		// TODO 18/04/2023 : This needs to be moved to the runner itself after https://elementor.atlassian.net/browse/HTS-434 is done.
		if ( self::IMPORT_PLUGINS_ACTION === ElementorUtils::get_super_global_value( $_SERVER, 'HTTP_X_ELEMENTOR_ACTION' ) ) {
			add_filter( 'woocommerce_create_pages', [ $this, 'empty_pages' ], 10, 0 );
		}
		// TODO ^^^

		add_filter( 'elementor/import/kit/result', function( $result ) {
			if ( ! empty( $result['file_url'] ) ) {
				return [
					'file_name' => $this->get_remote_kit_zip( $result['file_url'] ),
					'referrer' => static::REFERRER_KIT_LIBRARY,
					'file_url' => $result['file_url'],
				];
			}

			return $result;
		} );
	}

	/**
	 * Prevent the creation of the default WooCommerce pages (Cart, Checkout, etc.)
	 *
	 * TODO 18/04/2023 : This needs to be moved to the runner itself after https://elementor.atlassian.net/browse/HTS-434 is done.
	 *
	 * @return array
	 */
	public function empty_pages(): array {
		return [];
	}

	private function ensure_writing_permissions() {
		$server = new Server();

		$paths_to_check = [
			Server::KEY_PATH_WP_CONTENT_DIR => $server->get_system_path( Server::KEY_PATH_WP_CONTENT_DIR ),
			Server::KEY_PATH_UPLOADS_DIR => $server->get_system_path( Server::KEY_PATH_UPLOADS_DIR ),
			Server::KEY_PATH_ELEMENTOR_UPLOADS_DIR => $server->get_system_path( Server::KEY_PATH_ELEMENTOR_UPLOADS_DIR ),
		];

		$permissions = $server->get_paths_permissions( $paths_to_check );

		// WP Content dir has to be exists and writable.
		if ( ! $permissions[ Server::KEY_PATH_WP_CONTENT_DIR ]['write'] ) {
			throw new \Error( self::NO_WRITE_PERMISSIONS_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		// WP Uploads dir has to be exists and writable.
		if ( ! $permissions[ Server::KEY_PATH_UPLOADS_DIR ]['write'] ) {
			throw new \Error( self::NO_WRITE_PERMISSIONS_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		// Elementor uploads dir permissions is divided to 2 cases:
		// 1. If the dir exists, it has to be writable.
		// 2. If the dir doesn't exist, the parent dir has to be writable (wp uploads dir), so we can create it.
		if ( $permissions[ Server::KEY_PATH_ELEMENTOR_UPLOADS_DIR ]['exists'] && ! $permissions[ Server::KEY_PATH_ELEMENTOR_UPLOADS_DIR ]['write'] ) {
			throw new \Error( self::NO_WRITE_PERMISSIONS_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}
	}

	private function ensure_DOMDocument_exists() {
		if ( ! class_exists( 'DOMDocument' ) ) {
			throw new \Error( self::DOMDOCUMENT_MISSING ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}
	}

	/**
	 * Enqueue admin scripts
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			'elementor-import-export-admin',
			$this->get_js_assets_url( 'import-export-admin' ),
			[ 'elementor-common' ],
			ELEMENTOR_VERSION,
			true
		);

		wp_localize_script(
			'elementor-import-export-admin',
			'elementorImportExport',
			[
				'lastImportedSession' => $this->revert->get_last_import_session(),
				'appUrl' => Plugin::$instance->app->get_base_url() . '#/kit-library',
			]
		);

		wp_enqueue_script(
			'import-export-customization-admin',
			$this->get_js_assets_url( 'import-export-customization-admin' ),
			[ 'elementor-common', 'wp-api-fetch' ],
			ELEMENTOR_VERSION,
			true
		);
	}

	protected function get_remote_kit_zip( $url ) {
		$remote_zip_request = wp_safe_remote_get( $url );

		if ( is_wp_error( $remote_zip_request ) ) {
			Plugin::$instance->logger->get_logger()->error( $remote_zip_request->get_error_message() );
			throw new \Error( static::KIT_LIBRARY_ERROR_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		if ( 200 !== $remote_zip_request['response']['code'] ) {
			Plugin::$instance->logger->get_logger()->error( $remote_zip_request['response']['message'] );
			throw new \Error( static::KIT_LIBRARY_ERROR_KEY ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}

		return Plugin::$instance->uploads_manager->create_temp_file( $remote_zip_request['body'], 'kit.zip' );
	}

	/**
	 * Get config data that will be exposed to the frontend.
	 */
	private function get_config_data() {
		$export_nonce = wp_create_nonce( 'elementor_export' );
		$export_url = add_query_arg( [ '_nonce' => $export_nonce ], Plugin::$instance->app->get_base_url() );

		return [
			'exportURL' => $export_url,
			'summaryTitles' => $this->get_summary_titles(),
			'builtinWpPostTypes' => ImportExportUtils::get_builtin_wp_post_types(),
			'elementorPostTypes' => ImportExportUtils::get_elementor_post_types(),
			'isUnfilteredFilesEnabled' => Uploads_Manager::are_unfiltered_uploads_enabled(),
			'elementorHomePageUrl' => $this->get_elementor_home_page_url(),
			'recentlyEditedElementorPageUrl' => $this->get_recently_edited_elementor_page_url(),
			'tools_url' => Tools::get_url(),
			'importSessions' => Revert::get_import_sessions(),
			'lastImportedSession' => $this->revert->get_last_import_session(),
			'kitPreviewNonce' => wp_create_nonce( 'kit_thumbnail' ),
			'restApiBaseUrl' => Controller::get_base_url(),
			'restNonce' => wp_create_nonce( 'wp_rest' ),
			'restUrl' => rest_url(),
			'uiTheme' => $this->get_elementor_ui_theme_preference(),
			'exportGroups' => $this->get_export_groups(),
			'manifestVersion' => self::FORMAT_VERSION,
			'elementorVersion' => ELEMENTOR_VERSION,
			'upgradeVersionUrl' => admin_url( 'plugins.php' ),
		];
	}

	private function get_elementor_ui_theme_preference() {
		$editor_preferences = SettingsManager::get_settings_managers( 'editorPreferences' );

		return $editor_preferences->get_model()->get_settings( 'ui_theme' );
	}

	private function get_export_groups() {
		$export_groups = [];
		$document_types = Plugin::$instance->documents->get_document_types();

		foreach ( $document_types as $name => $document_type ) {
			$export_groups[ $name ] = defined( $document_type . '::EXPORT_GROUP' ) ? $document_type::EXPORT_GROUP : '';
		}

		return $export_groups;
	}

	/**
	 * Get labels of Elementor document types, Elementor Post types, WordPress Post types and Custom Post types.
	 */
	private function get_summary_titles() {
		$summary_titles = [];

		$document_types = Plugin::$instance->documents->get_document_types();

		foreach ( $document_types as $name => $document_type ) {
			$summary_titles['templates'][ $name ] = [
				'single' => $document_type::get_title(),
				'plural' => $document_type::get_plural_title(),
			];
		}

		$elementor_post_types = ImportExportUtils::get_elementor_post_types();
		$wp_builtin_post_types = ImportExportUtils::get_builtin_wp_post_types();
		$post_types = array_merge( $elementor_post_types, $wp_builtin_post_types );

		foreach ( $post_types as $post_type ) {
			$post_type_object = get_post_type_object( $post_type );

			$summary_titles['content'][ $post_type ] = [
				'single' => $post_type_object->labels->singular_name ?? '',
				'plural' => $post_type_object->label ?? '',
			];
		}

		$custom_post_types = ImportExportUtils::get_registered_cpt_names();
		if ( ! empty( $custom_post_types ) ) {
			foreach ( $custom_post_types as $custom_post_type ) {

				$custom_post_types_object = get_post_type_object( $custom_post_type );
				// CPT data appears in two arrays:
				// 1. content object: in order to show the export summary when completed in getLabel function
				$summary_titles['content'][ $custom_post_type ] = [
					'single' => $custom_post_types_object->labels->singular_name ?? '',
					'plural' => $custom_post_types_object->label ?? '',
				];

				// 2. customPostTypes object: in order to actually export the data
				$summary_titles['content']['customPostTypes'][ $custom_post_type ] = [
					'single' => $custom_post_types_object->labels->singular_name ?? '',
					'plural' => $custom_post_types_object->label ?? '',
				];
			}
		}

		return $summary_titles;
	}

	private function get_elementor_editor_home_page_url() {
		if ( 'page' !== get_option( 'show_on_front' ) ) {
			return '';
		}

		$frontpage_id = get_option( 'page_on_front' );

		return $this->get_elementor_editor_page_url( $frontpage_id );
	}

	private function get_elementor_home_page_url() {
		if ( 'page' !== get_option( 'show_on_front' ) ) {
			return '';
		}

		$frontpage_id = get_option( 'page_on_front' );

		return $this->get_elementor_page_url( $frontpage_id );
	}

	private function get_recently_edited_elementor_page_url() {
		$query = ElementorUtils::get_recently_edited_posts_query( [ 'posts_per_page' => 1 ] );

		if ( ! isset( $query->post ) ) {
			return '';
		}

		return $this->get_elementor_page_url( $query->post->ID );
	}

	private function get_recently_edited_elementor_editor_page_url() {
		$query = ElementorUtils::get_recently_edited_posts_query( [ 'posts_per_page' => 1 ] );

		if ( ! isset( $query->post ) ) {
			return '';
		}

		return $this->get_elementor_editor_page_url( $query->post->ID );
	}

	private function get_elementor_document( $page_id ) {
		$document = Plugin::$instance->documents->get( $page_id );

		if ( ! $document || ! $document->is_built_with_elementor() ) {
			return false;
		}

		return $document;
	}

	private function get_elementor_page_url( $page_id ) {
		$document = $this->get_elementor_document( $page_id );

		return $document ? $document->get_preview_url() : '';
	}

	private function get_elementor_editor_page_url( $page_id ) {
		$document = $this->get_elementor_document( $page_id );

		return $document ? $document->get_edit_url() : '';
	}

	/**
	 * @param string $class_name
	 *
	 * @return bool
	 */
	public function is_third_party_class( $class_name ) {
		$allowed_classes = [
			'Elementor\\',
			'ElementorPro\\',
			'WP_',
			'wp_',
		];

		foreach ( $allowed_classes as $allowed_class ) {
			if ( str_starts_with( $class_name, $allowed_class ) ) {
				return false;
			}
		}

		return true;
	}
}
