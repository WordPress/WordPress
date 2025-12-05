<?php

// phpcs:disable Yoast.NamingConventions.NamespaceName.TooLong -- Discussed in Tech Council, a better solution is being worked on.

namespace Yoast\WP\SEO\Integrations\Admin\Addon_Installation;

use WPSEO_Addon_Manager;
use Yoast\WP\SEO\Actions\Addon_Installation\Addon_Activate_Action;
use Yoast\WP\SEO\Actions\Addon_Installation\Addon_Install_Action;
use Yoast\WP\SEO\Conditionals\Addon_Installation_Conditional;
use Yoast\WP\SEO\Conditionals\Admin\Licenses_Page_Conditional;
use Yoast\WP\SEO\Conditionals\Admin_Conditional;
use Yoast\WP\SEO\Exceptions\Addon_Installation\Addon_Activation_Error_Exception;
use Yoast\WP\SEO\Exceptions\Addon_Installation\Addon_Already_Installed_Exception;
use Yoast\WP\SEO\Exceptions\Addon_Installation\Addon_Installation_Error_Exception;
use Yoast\WP\SEO\Exceptions\Addon_Installation\User_Cannot_Activate_Plugins_Exception;
use Yoast\WP\SEO\Exceptions\Addon_Installation\User_Cannot_Install_Plugins_Exception;
use Yoast\WP\SEO\Integrations\Integration_Interface;
use Yoast\WP\SEO\Plans\User_Interface\Plans_Page_Integration;

/**
 * Represents the Addon installation feature.
 */
class Installation_Integration implements Integration_Interface {

	/**
	 * The installation action.
	 *
	 * @var Addon_Install_Action
	 */
	protected $addon_install_action;

	/**
	 * The activation action.
	 *
	 * @var Addon_Activate_Action
	 */
	protected $addon_activate_action;

	/**
	 * The addon manager.
	 *
	 * @var WPSEO_Addon_Manager
	 */
	protected $addon_manager;

	/**
	 * {@inheritDoc}
	 */
	public static function get_conditionals() {
		return [
			Admin_Conditional::class,
			Licenses_Page_Conditional::class,
			Addon_Installation_Conditional::class,
		];
	}

	/**
	 * Addon_Installation constructor.
	 *
	 * @param WPSEO_Addon_Manager   $addon_manager         The addon manager.
	 * @param Addon_Activate_Action $addon_activate_action The addon activate action.
	 * @param Addon_Install_Action  $addon_install_action  The addon install action.
	 */
	public function __construct(
		WPSEO_Addon_Manager $addon_manager,
		Addon_Activate_Action $addon_activate_action,
		Addon_Install_Action $addon_install_action
	) {
		$this->addon_manager         = $addon_manager;
		$this->addon_activate_action = $addon_activate_action;
		$this->addon_install_action  = $addon_install_action;
	}

	/**
	 * Registers all hooks to WordPress.
	 *
	 * @return void
	 */
	public function register_hooks() {
		\add_action( 'wpseo_install_and_activate_addons', [ $this, 'install_and_activate_addons' ] );
	}

	/**
	 * Installs and activates missing addons.
	 *
	 * @return void
	 */
	public function install_and_activate_addons() {
		if ( ! isset( $_GET['action'] ) || ! \is_string( $_GET['action'] ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are only strictly comparing action below.
		$action = \wp_unslash( $_GET['action'] );
		if ( $action !== 'install' ) {
			return;
		}

		\check_admin_referer( 'wpseo_addon_installation', 'nonce' );

		echo '<div class="wrap yoast wpseo_table_page">';

		\printf(
			'<h1 id="wpseo-title" class="yoast-h1">%s</h1>',
			\esc_html__( 'Installing and activating addons', 'wordpress-seo' )
		);

		$licensed_addons = $this->addon_manager->get_myyoast_site_information()->subscriptions;

		foreach ( $licensed_addons as $addon ) {
			\printf( '<p><strong>%s</strong></p>', \esc_html( $addon->product->name ) );

			[ $installed, $output ] = $this->install_addon( $addon->product->slug, $addon->product->download );

			if ( $installed ) {
				$activation_output = $this->activate_addon( $addon->product->slug );

				$output = \array_merge( $output, $activation_output );
			}

			echo '<p>';
			echo \implode( '<br />', \array_map( 'esc_html', $output ) );
			echo '</p>';
		}

		\printf(
			/* translators: %1$s expands to an anchor tag to the admin premium page, %2$s expands to Yoast SEO Premium, %3$s expands to a closing anchor tag */
			\esc_html__( '%1$s Continue to %2$s%3$s', 'wordpress-seo' ),
			'<a href="' . \esc_url( \admin_url( 'admin.php?page=' . Plans_Page_Integration::PAGE ) ) . '">',
			'Yoast SEO Premium',
			'</a>'
		);

		echo '</div>';

		exit;
	}

	/**
	 * Activates an addon.
	 *
	 * @param string $addon_slug The addon to activate.
	 *
	 * @return array The output of the activation.
	 */
	public function activate_addon( $addon_slug ) {
		$output = [];

		try {
			$this->addon_activate_action->activate_addon( $addon_slug );

			/* Translators: %s expands to the name of the addon. */
			$output[] = \__( 'Addon activated.', 'wordpress-seo' );
		} catch ( User_Cannot_Activate_Plugins_Exception $exception ) {
			$output[] = \__( 'You are not allowed to activate plugins.', 'wordpress-seo' );
		} catch ( Addon_Activation_Error_Exception $exception ) {
			$output[] = \sprintf(
				/* Translators:%s expands to the error message. */
				\__( 'Addon activation failed because of an error: %s.', 'wordpress-seo' ),
				$exception->getMessage()
			);
		}

		return $output;
	}

	/**
	 * Installs an addon.
	 *
	 * @param string $addon_slug     The slug of the addon to install.
	 * @param string $addon_download The download URL of the addon.
	 *
	 * @return array The installation success state and the output of the installation.
	 */
	public function install_addon( $addon_slug, $addon_download ) {
		$installed = false;
		$output    = [];

		try {
			$installed = $this->addon_install_action->install_addon( $addon_slug, $addon_download );
		} catch ( Addon_Already_Installed_Exception $exception ) {
			/* Translators: %s expands to the name of the addon. */
			$output[] = \__( 'Addon installed.', 'wordpress-seo' );

			$installed = true;
		} catch ( User_Cannot_Install_Plugins_Exception $exception ) {
			$output[] = \__( 'You are not allowed to install plugins.', 'wordpress-seo' );
		} catch ( Addon_Installation_Error_Exception $exception ) {
			$output[] = \sprintf(
				/* Translators: %s expands to the error message. */
				\__( 'Addon installation failed because of an error: %s.', 'wordpress-seo' ),
				$exception->getMessage()
			);
		}

		return [ $installed, $output ];
	}
}
