<?php

namespace Yoast\WP\SEO;

use Throwable;
use WP_CLI;
use YoastSEO_Vendor\Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class that manages loading integrations if and only if all their conditionals are met.
 */
class Loader {

	/**
	 * The registered integrations.
	 *
	 * @var string[]
	 */
	protected $integrations = [];

	/**
	 * The registered integrations.
	 *
	 * @var string[]
	 */
	protected $initializers = [];

	/**
	 * The registered routes.
	 *
	 * @var string[]
	 */
	protected $routes = [];

	/**
	 * The registered commands.
	 *
	 * @var string[]
	 */
	protected $commands = [];

	/**
	 * The registered migrations.
	 *
	 * @var string[]
	 */
	protected $migrations = [];

	/**
	 * The dependency injection container.
	 *
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * Loader constructor.
	 *
	 * @param ContainerInterface $container The dependency injection container.
	 */
	public function __construct( ContainerInterface $container ) {
		$this->container = $container;
	}

	/**
	 * Registers an integration.
	 *
	 * @param string $integration_class The class name of the integration to be loaded.
	 *
	 * @return void
	 */
	public function register_integration( $integration_class ) {
		$this->integrations[] = $integration_class;
	}

	/**
	 * Registers an initializer.
	 *
	 * @param string $initializer_class The class name of the initializer to be loaded.
	 *
	 * @return void
	 */
	public function register_initializer( $initializer_class ) {
		$this->initializers[] = $initializer_class;
	}

	/**
	 * Registers a route.
	 *
	 * @param string $route_class The class name of the route to be loaded.
	 *
	 * @return void
	 */
	public function register_route( $route_class ) {
		$this->routes[] = $route_class;
	}

	/**
	 * Registers a command.
	 *
	 * @param string $command_class The class name of the command to be loaded.
	 *
	 * @return void
	 */
	public function register_command( $command_class ) {
		$this->commands[] = $command_class;
	}

	/**
	 * Registers a migration.
	 *
	 * @param string $plugin          The plugin the migration belongs to.
	 * @param string $version         The version of the migration.
	 * @param string $migration_class The class name of the migration to be loaded.
	 *
	 * @return void
	 */
	public function register_migration( $plugin, $version, $migration_class ) {
		if ( ! \array_key_exists( $plugin, $this->migrations ) ) {
			$this->migrations[ $plugin ] = [];
		}

		$this->migrations[ $plugin ][ $version ] = $migration_class;
	}

	/**
	 * Loads all registered classes if their conditionals are met.
	 *
	 * @return void
	 */
	public function load() {
		$this->load_initializers();

		if ( ! \did_action( 'init' ) ) {
			\add_action( 'init', [ $this, 'load_integrations' ] );
		}
		else {
			$this->load_integrations();
		}

		\add_action( 'rest_api_init', [ $this, 'load_routes' ] );

		if ( \defined( 'WP_CLI' ) && \WP_CLI ) {
			$this->load_commands();
		}
	}

	/**
	 * Returns all registered migrations.
	 *
	 * @param string $plugin The plugin to get the migrations for.
	 *
	 * @return string[]|false The registered migrations. False if no migrations were registered.
	 */
	public function get_migrations( $plugin ) {
		if ( ! \array_key_exists( $plugin, $this->migrations ) ) {
			return false;
		}

		return $this->migrations[ $plugin ];
	}

	/**
	 * Loads all registered commands.
	 *
	 * @return void
	 */
	protected function load_commands() {
		foreach ( $this->commands as $class ) {
			$command = $this->get_class( $class );

			if ( $command === null ) {
				continue;
			}

			WP_CLI::add_command( $class::get_namespace(), $command );
		}
	}

	/**
	 * Loads all registered initializers if their conditionals are met.
	 *
	 * @return void
	 */
	protected function load_initializers() {
		foreach ( $this->initializers as $class ) {
			if ( ! $this->conditionals_are_met( $class ) ) {
				continue;
			}

			$initializer = $this->get_class( $class );

			if ( $initializer === null ) {
				continue;
			}

			$initializer->initialize();
		}
	}

	/**
	 * Loads all registered integrations if their conditionals are met.
	 *
	 * @return void
	 */
	public function load_integrations() {
		foreach ( $this->integrations as $class ) {
			if ( ! $this->conditionals_are_met( $class ) ) {
				continue;
			}

			$integration = $this->get_class( $class );

			if ( $integration === null ) {
				continue;
			}

			$integration->register_hooks();
		}
	}

	/**
	 * Loads all registered routes if their conditionals are met.
	 *
	 * @return void
	 */
	public function load_routes() {
		foreach ( $this->routes as $class ) {
			if ( ! $this->conditionals_are_met( $class ) ) {
				continue;
			}

			$route = $this->get_class( $class );

			if ( $route === null ) {
				continue;
			}

			$route->register_routes();
		}
	}

	/**
	 * Checks if all conditionals of a given loadable are met.
	 *
	 * @param string $loadable_class The class name of the loadable.
	 *
	 * @return bool Whether all conditionals of the loadable are met.
	 */
	protected function conditionals_are_met( $loadable_class ) {
		// In production environments do not fatal if the class does not exist but log and fail gracefully.
		if ( \YOAST_ENVIRONMENT === 'production' && ! \class_exists( $loadable_class ) ) {
			if ( \defined( 'WP_DEBUG' ) && \WP_DEBUG ) {
				// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				\error_log(
					\sprintf(
						/* translators: %1$s expands to Yoast SEO, %2$s expands to the name of the class that could not be found. */
						\__( '%1$s attempted to load the class %2$s but it could not be found.', 'wordpress-seo' ),
						'Yoast SEO',
						$loadable_class
					)
				);
			}
			return false;
		}

		$conditionals = $loadable_class::get_conditionals();
		foreach ( $conditionals as $class ) {
			$conditional = $this->get_class( $class );
			if ( $conditional === null || ! $conditional->is_met() ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Gets a class from the container.
	 *
	 * @param string $class_name The class name.
	 *
	 * @return object|null The class or, in production environments, null if it does not exist.
	 *
	 * @throws Throwable If the class does not exist in development environments.
	 */
	protected function get_class( $class_name ) {
		try {
			return $this->container->get( $class_name );
		} catch ( Throwable $e ) {
			// In production environments do not fatal if the class could not be constructed but log and fail gracefully.
			if ( \YOAST_ENVIRONMENT === 'production' ) {
				if ( \defined( 'WP_DEBUG' ) && \WP_DEBUG ) {
					// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
					\error_log( $e->getMessage() );
				}
				return null;
			}
			throw $e;
		}
	}
}
