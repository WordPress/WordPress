<?php
/**
 * Loads WooCommerce packages from the /packages directory. These are packages developed outside of core.
 */

namespace Automattic\WooCommerce;

defined( 'ABSPATH' ) || exit;

/**
 * Packages class.
 *
 * @since 3.7.0
 */
class Packages {

	/**
	 * Static-only class.
	 */
	private function __construct() {}

	/**
	 * Array of package names and their main package classes.
	 *
	 * @var array Key is the package name/directory, value is the main package class which handles init.
	 */
	protected static $packages = array(
		'woocommerce-blocks'   => '\\Automattic\\WooCommerce\\Blocks\\Package'
	);

	/**
	 * Init the package loader.
	 *
	 * @since 3.7.0
	 */
	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'on_init' ) );
	}

	/**
	 * Callback for WordPress init hook.
	 */
	public static function on_init() {
		self::load_packages();
	}

	/**
	 * Checks a package exists by looking for it's directory.
	 *
	 * @param string $package Package name.
	 * @return boolean
	 */
	public static function package_exists( $package ) {
		return file_exists( dirname( __DIR__ ) . '/packages/' . $package );
	}

	/**
	 * Loads packages after plugins_loaded hook.
	 *
	 * Each package should include an init file which loads the package so it can be used by core.
	 */
	protected static function load_packages() {
		// Initialize WooCommerce Admin.
		\Automattic\WooCommerce\Admin\Composer\Package::init();

		foreach ( self::$packages as $package_name => $package_class ) {
			if ( ! self::package_exists( $package_name ) ) {
				self::missing_package( $package_name );
				continue;
			}
			call_user_func( array( $package_class, 'init' ) );
		}

		// Proxies "activated_plugin" hook for embedded packages listen on WC plugin activation
		// https://github.com/woocommerce/woocommerce/issues/28697.
		if ( is_admin() ) {
			$activated_plugin = get_transient( 'woocommerce_activated_plugin' );
			if ( $activated_plugin ) {
				delete_transient( 'woocommerce_activated_plugin' );

				/**
				 * WooCommerce is activated hook.
				 *
				 * @since 5.0.0
				 * @param bool $activated_plugin Activated plugin path,
				 *                               generally woocommerce/woocommerce.php.
				 */
				do_action( 'woocommerce_activated_plugin', $activated_plugin );
			}
		}
	}

	/**
	 * If a package is missing, add an admin notice.
	 *
	 * @param string $package Package name.
	 */
	protected static function missing_package( $package ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log(  // phpcs:ignore
				sprintf(
					/* Translators: %s package name. */
					esc_html__( 'Missing the WooCommerce %s package', 'woocommerce' ),
					'<code>' . esc_html( $package ) . '</code>'
				) . ' - ' . esc_html__( 'Your installation of WooCommerce is incomplete. If you installed WooCommerce from GitHub, please refer to this document to set up your development environment: https://github.com/woocommerce/woocommerce/wiki/How-to-set-up-WooCommerce-development-environment', 'woocommerce' )
			);
		}
		add_action(
			'admin_notices',
			function() use ( $package ) {
				?>
				<div class="notice notice-error">
					<p>
						<strong>
							<?php
							printf(
								/* Translators: %s package name. */
								esc_html__( 'Missing the WooCommerce %s package', 'woocommerce' ),
								'<code>' . esc_html( $package ) . '</code>'
							);
							?>
						</strong>
						<br>
						<?php
						printf(
							/* translators: 1: is a link to a support document. 2: closing link */
							esc_html__( 'Your installation of WooCommerce is incomplete. If you installed WooCommerce from GitHub, %1$splease refer to this document%2$s to set up your development environment.', 'woocommerce' ),
							'<a href="' . esc_url( 'https://github.com/woocommerce/woocommerce/wiki/How-to-set-up-WooCommerce-development-environment' ) . '" target="_blank" rel="noopener noreferrer">',
							'</a>'
						);
						?>
					</p>
				</div>
				<?php
			}
		);
	}
}
