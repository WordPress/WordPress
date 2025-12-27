<?php

use Sentry\Event;
use Sentry\Severity;
use Sentry\EventHint;
use Sentry\SentrySdk;
use Sentry\State\Hub;
use Sentry\State\Scope;
use Sentry\ClientBuilder;
use Sentry\State\HubInterface;
use Sentry\Integration\ModulesIntegration;
use function Sentry\logger;

/**
 * Sentry for WordPress PHP Tracker.
 */
final class WP_Sentry_Php_Tracker {
	use WP_Sentry_Resolve_User, WP_Sentry_Resolve_Environment;

	/**
	 * Holds an instance to the Sentry client.
	 *
	 * @var \Sentry\ClientInterface
	 */
	protected $client;

	/**
	 * Holds the last DSN which was used to initialize the Sentry client.
	 *
	 * @var string
	 */
	protected $dsn;

	/**
	 * Holds the class instance.
	 *
	 * @var WP_Sentry_Php_Tracker
	 */
	private static $instance;

	/**
	 * Get the sentry tracker instance.
	 *
	 * @return \WP_Sentry_Php_Tracker
	 */
	public static function get_instance(): WP_Sentry_Php_Tracker {
		return self::$instance ?: self::$instance = new self;
	}

	/**
	 * WP_Sentry_Php_Tracker constructor.
	 */
	protected function __construct() {
		// Force the initialization of the client immediately
		$this->get_client();

		// Register with WordPress hooks
		add_action( 'init', [ $this, 'on_init' ] );
		add_action( 'set_current_user', [ $this, 'on_set_current_user' ] );
		add_action( 'after_setup_theme', [ $this, 'on_after_setup_theme' ] );

		// Register our helper actions
		add_action( 'sentry/captureMessage', [ $this, 'on_capture_message_action' ], 10, 3 );
		add_action( 'sentry/captureException', [ $this, 'on_capture_exception_action' ], 10, 2 );

		if ( self::get_logs_enabled() ) {
			add_action( 'shutdown', function () {
				logger()->flush();
			} );
		}
	}

	public function on_init(): void {
		if ( $this->client === null ) {
			return;
		}

		$hub = SentrySdk::getCurrentHub();

		$hub->configureScope( function ( Scope $scope ) {
			foreach ( $this->get_default_tags() as $tag => $value ) {
				$scope->setTag( $tag, $value );
			}
		} );

		SentrySdk::setCurrentHub( $hub );
	}

	/**
	 * Handle the `set_current_user` WP action.
	 */
	public function on_set_current_user(): void {
		$this->get_client()->configureScope( function ( Scope $scope ) {
			$user = $this->get_current_user_info();

			if ( $user !== null ) {
				$scope->setUser( $user );
			}
		} );
	}

	/**
	 * Handle the `after_setup_theme` WP action.
	 */
	public function on_after_setup_theme(): void {
		// If the DSN potentially has changed, re-initialize the client
		if ( has_filter( 'wp_sentry_dsn' ) ) {
			$this->initializeClient();
		}

		// Apply the filter to config the scope
		if ( has_filter( 'wp_sentry_scope' ) ) {
			$this->get_client()->configureScope( function ( Scope $scope ) {
				apply_filters( 'wp_sentry_scope', $scope );
			} );
		}

		// Apply the filter to configure any options
		if ( has_filter( 'wp_sentry_options' ) ) {
			$sentryClient = $this->get_client()->getClient();

			if ( $sentryClient !== null ) {
				apply_filters( 'wp_sentry_options', $sentryClient->getOptions() );
			}
		}
	}

	/**
	 * Handle the `sentry/captureMessage` helper action.
	 */
	public function on_capture_message_action( string $message, ?Severity $level = null, ?EventHint $hint = null ): void {
		$this->get_client()->captureMessage( $message, $level, $hint );
	}

	/**
	 * Handle the `sentry/captureException` helper action.
	 */
	public function on_capture_exception_action( Throwable $e, ?EventHint $hint = null ): void {
		$this->get_client()->captureException( $e, $hint );
	}

	/**
	 * Retrieve the DSN.
	 */
	public function get_dsn(): ?string {
		$dsn = defined( 'WP_SENTRY_PHP_DSN' ) ? WP_SENTRY_PHP_DSN : null;

		if ( $dsn === null ) {
			$dsn = defined( 'WP_SENTRY_DSN' ) ? WP_SENTRY_DSN : null;
		}

		if ( has_filter( 'wp_sentry_dsn' ) ) {
			$dsn = (string) apply_filters( 'wp_sentry_dsn', $dsn );
		}

		return $dsn;
	}

	/**
	 * Retrieve the logs enabled status.
	 */
	public static function get_logs_enabled(): bool {
		return defined( 'WP_SENTRY_ENABLE_LOGS' ) && WP_SENTRY_ENABLE_LOGS === true;
	}

	/**
	 * Retrieve the spotlight enabled status.
	 */
	public static function get_spotlight_enabled(): bool {
		return defined( 'WP_SENTRY_SPOTLIGHT' ) && WP_SENTRY_SPOTLIGHT === true;
	}

	/**
	 * Get the sentry client.
	 */
	public function get_client(): HubInterface {
		if ( $this->client === null && ( $this->get_dsn() !== null || self::get_spotlight_enabled() ) ) {
			$this->initializeClient();
		}

		return SentrySdk::getCurrentHub();
	}

	/**
	 * Get the default tags.
	 */
	public function get_default_tags(): array {
		require WP_SENTRY_WPINC . '/version.php';

		/** @noinspection IssetArgumentExistenceInspection */
		$tags = [
			'wordpress' => $wp_version ?? 'unknown',
		];

		if ( function_exists( 'get_bloginfo' ) ) {
			$tags['language'] = get_bloginfo( 'language' );
		}

		return $tags;
	}

	/**
	 * Get the default options.
	 */
	public function get_default_options(): array {
		$options = [
			'dsn'                  => $this->get_dsn(),
			'tags'                 => $this->get_default_tags(),
			'prefixes'             => [ ABSPATH ],
			'spotlight'            => self::get_spotlight_enabled(),
			'environment'          => $this->get_environment(),
			'enable_logs'          => self::get_logs_enabled(),
			'before_send'          => function ( Event $event, ?EventHint $hint ): ?Event {
				// Sync the transaction name with the current transaction if we have detected one
				if ( $event->getTransaction() === null ) {
					$transaction_name = WP_Sentry_Php_Tracing::get_instance()->get_transaction_name();

					if ( $transaction_name !== null ) {
						$event->setTransaction( $transaction_name );
					}
				}

				if ( function_exists( 'apply_filters' ) ) {
					try {
						/**
						 * Filter to decide not to send the event to Sentry or to edit it.
						 *
						 * @link https://docs.sentry.io/platforms/php/configuration/filtering/#filtering-error-events
						 *
						 * @param \Sentry\Event          $event
						 * @param \Sentry\EventHint|null $hint
						 */
						return apply_filters( 'wp_sentry_before_send', $event, $hint );
					} catch ( Throwable $e ) {
						// If the filter throws an exception, ignore it and fall through to sending the event
					}
				}

				return $event;
			},
			'integrations'         => static function ( array $integrations ) {
				return array_filter( $integrations, static function ( $integration ) {
					// Disable the modules integration as it only lists the internal packages from this plugin instead of the packages of the full project
					if ( $integration instanceof ModulesIntegration ) {
						return false;
					}

					return true;
				} );
			},
			'send_default_pii'     => defined( 'WP_SENTRY_SEND_DEFAULT_PII' ) && WP_SENTRY_SEND_DEFAULT_PII,
			'traces_sample_rate'   => defined( 'WP_SENTRY_TRACES_SAMPLE_RATE' ) ? WP_SENTRY_TRACES_SAMPLE_RATE : null,
			'profiles_sample_rate' => defined( 'WP_SENTRY_PROFILES_SAMPLE_RATE' ) ? WP_SENTRY_PROFILES_SAMPLE_RATE : null,
		];

		if ( defined( 'WP_SENTRY_VERSION' ) ) {
			$options['release'] = WP_SENTRY_VERSION;
		}

		if ( defined( 'WP_SENTRY_ERROR_TYPES' ) ) {
			$options['error_types'] = WP_SENTRY_ERROR_TYPES;
		}

		$options['in_app_exclude'] = [
			WP_SENTRY_WPADMIN, // <base>/wp-admin
			WP_SENTRY_WPINC,   // <base>/wp-includes
		];

		if ( $this->is_wp_proxy_enabled() && $this->wp_proxy_enabled_for_us() ) {
			$options['http_proxy'] = sprintf( "%s:%s", $this->wp_proxy_host(), $this->wp_proxy_port() );

			if ( $this->is_wp_proxy_using_authentication() ) {
				$options['http_proxy_authentication'] = $this->wp_proxy_authentication();
			}
		}

		return $options;
	}

	/**
	 * Initialize the Sentry client and register it with the Hub.
	 */
	private function initializeClient(): void {
		$dsn = $this->get_dsn();

		// Do not re-initialize the client when the DSN has not changed
		if ( $this->client !== null && $this->dsn === $dsn ) {
			return;
		}

		$this->dsn = $this->get_dsn();

		$clientBuilder = ClientBuilder::create( $this->get_default_options() );

		if ( defined( 'WP_SENTRY_CLIENTBUILDER_CALLBACK' ) && is_callable( WP_SENTRY_CLIENTBUILDER_CALLBACK ) ) {
			call_user_func( WP_SENTRY_CLIENTBUILDER_CALLBACK, $clientBuilder );
		}

		$clientBuilder->setSdkIdentifier( WP_Sentry_Version::SDK_IDENTIFIER );
		$clientBuilder->setSdkVersion( WP_Sentry_Version::SDK_VERSION );

		$hub = new Hub( $this->client = $clientBuilder->getClient() );

		SentrySdk::setCurrentHub( $hub );
	}

	public function enabled(): bool {
		return ! empty( $this->get_dsn() );
	}

	private function is_wp_proxy_enabled(): bool {
		return defined( 'WP_PROXY_HOST' ) && defined( 'WP_PROXY_PORT' );
	}

	private function is_wp_proxy_using_authentication(): bool {
		return defined( 'WP_PROXY_USERNAME' ) && defined( 'WP_PROXY_PASSWORD' );
	}

	private function wp_proxy_authentication(): string {
		return $this->wp_proxy_username() . ':' . $this->wp_proxy_password();
	}

	private function wp_proxy_host(): string {
		if ( defined( 'WP_PROXY_HOST' ) ) {
			return WP_PROXY_HOST;
		}

		return '';
	}

	private function wp_proxy_port(): string {
		if ( defined( 'WP_PROXY_PORT' ) ) {
			return WP_PROXY_PORT;
		}

		return '';
	}

	private function wp_proxy_username(): string {
		if ( defined( 'WP_PROXY_USERNAME' ) ) {
			return WP_PROXY_USERNAME;
		}

		return '';
	}

	private function wp_proxy_password(): string {
		if ( defined( 'WP_PROXY_PASSWORD' ) ) {
			return WP_PROXY_PASSWORD;
		}

		return '';
	}

	private function wp_proxy_enabled_for_us(): bool {
		return ! defined( 'WP_SENTRY_PROXY_ENABLED' ) || WP_SENTRY_PROXY_ENABLED;
	}

	public function get_sdk_version(): string {
		return Sentry\Client::SDK_VERSION;
	}
}
