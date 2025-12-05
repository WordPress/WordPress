<?php
// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Needed in the folder structure.
namespace Yoast\WP\SEO\Dashboard\Infrastructure\Integrations;

use Google\Site_Kit\Core\REST_API\REST_Routes;
use Yoast\WP\SEO\Conditionals\Google_Site_Kit_Feature_Conditional;
use Yoast\WP\SEO\Conditionals\Third_Party\Site_Kit_Conditional;
use Yoast\WP\SEO\Dashboard\Infrastructure\Configuration\Permanently_Dismissed_Site_Kit_Configuration_Repository_Interface as Configuration_Repository;
use Yoast\WP\SEO\Dashboard\Infrastructure\Configuration\Site_Kit_Consent_Repository_Interface;
use Yoast\WP\SEO\Dashboard\Infrastructure\Connection\Site_Kit_Is_Connected_Call;
use Yoast\WP\SEO\Dashboard\User_Interface\Setup\Setup_Url_Interceptor;

/**
 * Describes if the Site kit integration is enabled and configured.
 */
class Site_Kit {

	private const SITE_KIT_FILE = 'google-site-kit/google-site-kit.php';

	/**
	 * The Site Kit feature conditional.
	 *
	 * @var Google_Site_Kit_Feature_Conditional
	 */
	protected $site_kit_feature_conditional;

	/**
	 * The Site Kit conditional.
	 *
	 * @var Site_Kit_Conditional
	 */
	private $site_kit_conditional;

	/**
	 * The Site Kit consent repository.
	 *
	 * @var Site_Kit_Consent_Repository_Interface
	 */
	private $site_kit_consent_repository;

	/**
	 * The Site Kit consent repository.
	 *
	 * @var Configuration_Repository
	 */
	private $permanently_dismissed_site_kit_configuration_repository;

	/**
	 * The call wrapper.
	 *
	 * @var Site_Kit_Is_Connected_Call $site_kit_is_connected_call
	 */
	private $site_kit_is_connected_call;

	/**
	 * The search console module data.
	 *
	 * @var array<string, bool> $search_console_module
	 */
	private $search_console_module = [
		'can_view' => null,
	];

	/**
	 * The analytics module data.
	 *
	 * @var array<string, bool> $ga_module
	 */
	private $ga_module = [
		'can_view'  => null,
		'connected' => null,
	];

	/**
	 * The constructor.
	 *
	 * @param Site_Kit_Consent_Repository_Interface $site_kit_consent_repository  The Site Kit consent repository.
	 * @param Configuration_Repository              $configuration_repository     The Site Kit permanently dismissed
	 *                                                                            configuration repository.
	 * @param Site_Kit_Is_Connected_Call            $site_kit_is_connected_call   The api call to check if the site is
	 *                                                                            connected.
	 * @param Google_Site_Kit_Feature_Conditional   $site_kit_feature_conditional The Site Kit feature conditional.
	 * @param Site_Kit_Conditional                  $site_kit_conditional         The Site Kit conditional.
	 */
	public function __construct(
		Site_Kit_Consent_Repository_Interface $site_kit_consent_repository,
		Configuration_Repository $configuration_repository,
		Site_Kit_Is_Connected_Call $site_kit_is_connected_call,
		Google_Site_Kit_Feature_Conditional $site_kit_feature_conditional,
		Site_Kit_Conditional $site_kit_conditional
	) {
		$this->site_kit_consent_repository                             = $site_kit_consent_repository;
		$this->permanently_dismissed_site_kit_configuration_repository = $configuration_repository;
		$this->site_kit_is_connected_call                              = $site_kit_is_connected_call;
		$this->site_kit_feature_conditional                            = $site_kit_feature_conditional;
		$this->site_kit_conditional                                    = $site_kit_conditional;
	}

	/**
	 * If the Site Kit plugin is active.
	 *
	 * @return bool If the integration is activated.
	 */
	public function is_enabled(): bool {
		return $this->site_kit_conditional->is_met();
	}

	/**
	 * If the Google site kit setup has been completed.
	 *
	 * @return bool If the Google site kit setup has been completed.
	 */
	private function is_setup_completed(): bool {
		return $this->site_kit_is_connected_call->is_setup_completed();
	}

	/**
	 * If consent has been granted.
	 *
	 * @return bool If consent has been granted.
	 */
	private function is_connected(): bool {
		return $this->site_kit_consent_repository->is_consent_granted();
	}

	/**
	 * If Google Analytics is connected.
	 *
	 * @return bool If Google Analytics is connected.
	 */
	public function is_ga_connected(): bool {
		if ( $this->ga_module['connected'] !== null ) {
			return $this->ga_module['connected'];
		}

		return $this->site_kit_is_connected_call->is_ga_connected();
	}

	/**
	 * If the Site Kit plugin is installed. This is needed since we cannot check with `is_plugin_active` in rest
	 * requests. `Plugin.php` is only loaded on admin pages.
	 *
	 * @return bool If the Site Kit plugin is installed.
	 */
	private function is_site_kit_installed(): bool {
		return \class_exists( 'Google\Site_Kit\Plugin' );
	}

	/**
	 * If the entire onboarding has been completed.
	 *
	 * @return bool If the entire onboarding has been completed.
	 */
	public function is_onboarded(): bool {
		// @TODO: Consider replacing the `is_setup_completed()` check with a `can_read_data( $module )` check (and possibly rename the method to something more genric eg. is_ready() ).
		return ( $this->is_site_kit_installed() && $this->is_setup_completed() && $this->is_connected() );
	}

	/**
	 * Checks if current user can view dashboard data for a module
	 *
	 * @param array<array|null> $module The module.
	 *
	 * @return bool If the user can read the data.
	 */
	private function can_read_data( array $module ): bool {
		return ( ! \is_null( $module['can_view'] ) ? $module['can_view'] : false );
	}

	/**
	 * Return this object represented by a key value array.
	 *
	 * @return array<string, bool> Returns the name and if the feature is enabled.
	 */
	public function to_array(): array {
		if ( ! $this->site_kit_feature_conditional->is_met() ) {
			return [];
		}
		if ( $this->is_enabled() ) {
			$this->parse_site_kit_data();
		}
		return [
			'installUrl'               => \self_admin_url( 'update.php?page=' . Setup_Url_Interceptor::PAGE . '&redirect_setup_url=' ) . \rawurlencode( $this->get_install_url() ),
			'activateUrl'              => \self_admin_url( 'update.php?page=' . Setup_Url_Interceptor::PAGE . '&redirect_setup_url=' ) . \rawurlencode( $this->get_activate_url() ),
			'setupUrl'                 => \self_admin_url( 'update.php?page=' . Setup_Url_Interceptor::PAGE . '&redirect_setup_url=' ) . \rawurlencode( $this->get_setup_url() ),
			'updateUrl'                => \self_admin_url( 'update.php?page=' . Setup_Url_Interceptor::PAGE . '&redirect_setup_url=' ) . \rawurlencode( $this->get_update_url() ),
			'dashboardUrl'             => \self_admin_url( 'admin.php?page=googlesitekit-dashboard' ),
			'isAnalyticsConnected'     => $this->is_ga_connected(),
			'isFeatureEnabled'         => true,
			'isSetupWidgetDismissed'   => $this->permanently_dismissed_site_kit_configuration_repository->is_site_kit_configuration_dismissed(),
			'capabilities'             => [
				'installPlugins'        => \current_user_can( 'install_plugins' ),
				'viewSearchConsoleData' => $this->can_read_data( $this->search_console_module ),
				'viewAnalyticsData'     => $this->can_read_data( $this->ga_module ),
			],
			'connectionStepsStatuses'  => [
				'isInstalled'      => \file_exists( \WP_PLUGIN_DIR . '/' . self::SITE_KIT_FILE ),
				'isActive'         => $this->is_enabled(),
				'isSetupCompleted' => $this->can_read_data( $this->search_console_module ) || $this->can_read_data( $this->ga_module ),
				'isConsentGranted' => $this->is_connected(),
			],
			'isVersionSupported'       => \defined( 'GOOGLESITEKIT_VERSION' ) ? \version_compare( \GOOGLESITEKIT_VERSION, '1.148.0', '>=' ) : false,
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: We are not processing form information.
			'isRedirectedFromSiteKit'  => isset( $_GET['redirected_from_site_kit'] ),
		];
	}

	/**
	 * Return this object represented by a key value array. This is not used yet.
	 *
	 * @codeCoverageIgnore
	 *
	 * @return array<string, bool> Returns the name and if the feature is enabled.
	 */
	public function to_legacy_array(): array {
		return $this->to_array();
	}

	/**
	 * Parses the Site Kit configuration data.
	 *
	 * @return void
	 */
	public function parse_site_kit_data(): void {
		$paths     = $this->get_preload_paths();
		$preloaded = $this->get_preloaded_data( $paths );
		if ( empty( $preloaded ) ) {
			return;
		}

		$modules_data        = ! empty( $preloaded[ $paths['modules'] ]['body'] ) ? $preloaded[ $paths['modules'] ]['body'] : [];
		$modules_permissions = ! empty( $preloaded[ $paths['permissions'] ]['body'] ) ? $preloaded[ $paths['permissions'] ]['body'] : [];

		$can_view_dashboard = ( $modules_permissions['googlesitekit_view_authenticated_dashboard'] ?? false );

		foreach ( $modules_data as $module ) {
			$slug = $module['slug'];
			// We have to also check if the module is recoverable, because if we rely on the module being shared, we have to make also sure the module owner is still connected.
			$is_recoverable = ( $module['recoverable'] ?? null );

			if ( $slug === 'analytics-4' ) {
				$can_read_shared_module_data = ( $modules_permissions['googlesitekit_read_shared_module_data::["analytics-4"]'] ?? false );

				$this->ga_module['can_view']  = $can_view_dashboard || ( $can_read_shared_module_data && ! $is_recoverable );
				$this->ga_module['connected'] = ( $module['connected'] ?? false );
			}

			if ( $slug === 'search-console' ) {
				$can_read_shared_module_data = ( $modules_permissions['googlesitekit_read_shared_module_data::["search-console"]'] ?? false );

				$this->search_console_module['can_view'] = $can_view_dashboard || ( $can_read_shared_module_data && ! $is_recoverable );
			}
		}
	}

	/**
	 * Holds the parsed preload paths for preloading some Site Kit API data.
	 *
	 * @return string[]
	 */
	public function get_preload_paths(): array {

		$rest_root = ( \class_exists( REST_Routes::class ) ) ? REST_Routes::REST_ROOT : '';

		return [
			'permissions'    => '/' . $rest_root . '/core/user/data/permissions',
			'modules'        => '/' . $rest_root . '/core/modules/data/list',
		];
	}

	/**
	 * Runs the given paths through the `rest_preload_api_request` method.
	 *
	 * @param string[] $paths The paths to add to `rest_preload_api_request`.
	 *
	 * @return array<array|null> The array with all the now filled in preloaded data.
	 */
	public function get_preloaded_data( array $paths ): array {
		$preload_paths = \apply_filters( 'googlesitekit_apifetch_preload_paths', [] );
		$actual_paths  = \array_intersect( $paths, $preload_paths );

		return \array_reduce(
			\array_unique( $actual_paths ),
			'rest_preload_api_request',
			[]
		);
	}

	/**
	 * Creates a valid activation URL for the Site Kit plugin.
	 *
	 * @return string
	 */
	public function get_activate_url(): string {
		return \html_entity_decode(
			\wp_nonce_url(
				\self_admin_url( 'plugins.php?action=activate&plugin=' . self::SITE_KIT_FILE ),
				'activate-plugin_' . self::SITE_KIT_FILE
			)
		);
	}

	/**
	 *  Creates a valid install URL for the Site Kit plugin.
	 *
	 * @return string
	 */
	public function get_install_url(): string {
		return \html_entity_decode(
			\wp_nonce_url(
				\self_admin_url( 'update.php?action=install-plugin&plugin=google-site-kit' ),
				'install-plugin_google-site-kit'
			)
		);
	}

	/**
	 *  Creates a valid update URL for the Site Kit plugin.
	 *
	 * @return string
	 */
	public function get_update_url(): string {
		return \html_entity_decode(
			\wp_nonce_url(
				\self_admin_url( 'update.php?action=upgrade-plugin&plugin=' . self::SITE_KIT_FILE ),
				'upgrade-plugin_' . self::SITE_KIT_FILE
			)
		);
	}

	/**
	 *  Creates a valid setup URL for the Site Kit plugin.
	 *
	 * @return string
	 */
	public function get_setup_url(): string {
		return \self_admin_url( 'admin.php?page=googlesitekit-splash' );
	}
}
