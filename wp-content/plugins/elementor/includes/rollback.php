<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor rollback.
 *
 * Elementor rollback handler class is responsible for rolling back Elementor to
 * previous version.
 *
 * @since 1.5.0
 */
class Rollback {

	/**
	 * Package URL.
	 *
	 * Holds the package URL.
	 *
	 * @since 1.5.0
	 * @access protected
	 *
	 * @var string Package URL.
	 */
	protected $package_url;

	/**
	 * Version.
	 *
	 * Holds the version.
	 *
	 * @since 1.5.0
	 * @access protected
	 *
	 * @var string Package URL.
	 */
	protected $version;

	/**
	 * Plugin name.
	 *
	 * Holds the plugin name.
	 *
	 * @since 1.5.0
	 * @access protected
	 *
	 * @var string Plugin name.
	 */
	protected $plugin_name;

	/**
	 * Plugin slug.
	 *
	 * Holds the plugin slug.
	 *
	 * @since 1.5.0
	 * @access protected
	 *
	 * @var string Plugin slug.
	 */
	protected $plugin_slug;

	/**
	 * Rollback constructor.
	 *
	 * Initializing Elementor rollback.
	 *
	 * @since 1.5.0
	 * @access public
	 *
	 * @param array $args Optional. Rollback arguments. Default is an empty array.
	 */
	public function __construct( $args = [] ) {
		foreach ( $args as $key => $value ) {
			$this->{$key} = $value;
		}
	}

	/**
	 * Print inline style.
	 *
	 * Add an inline CSS to the rollback page.
	 *
	 * @since 1.5.0
	 * @access private
	 */
	private function print_inline_style() {
		?>
		<style>
			.wrap {
				overflow: hidden;
				max-width: 850px;
				margin: auto;
				font-family: Courier, monospace;
			}

			h1 {
				background: #D30C5C;
				text-align: center;
				color: #fff !important;
				padding: 70px !important;
				text-transform: uppercase;
				letter-spacing: 1px;
			}

			h1 img {
				max-width: 300px;
				display: block;
				margin: auto auto 50px;
			}
		</style>
		<?php
	}

	/**
	 * Apply package.
	 *
	 * Change the plugin data when WordPress checks for updates. This method
	 * modifies package data to update the plugin from a specific URL containing
	 * the version package.
	 *
	 * @since 1.5.0
	 * @access protected
	 */
	protected function apply_package() {
		$update_plugins = get_site_transient( 'update_plugins' );
		if ( ! is_object( $update_plugins ) ) {
			$update_plugins = new \stdClass();
		}

		$plugin_info = new \stdClass();
		$plugin_info->new_version = $this->version;
		$plugin_info->slug = $this->plugin_slug;
		$plugin_info->package = $this->package_url;
		$plugin_info->url = 'https://elementor.com/';

		$update_plugins->response[ $this->plugin_name ] = $plugin_info;

		// Remove handle beta testers.
		remove_filter( 'pre_set_site_transient_update_plugins', [ Plugin::instance()->beta_testers, 'check_version' ] );

		set_site_transient( 'update_plugins', $update_plugins );
	}

	/**
	 * Upgrade.
	 *
	 * Run WordPress upgrade to rollback Elementor to previous version.
	 *
	 * @since 1.5.0
	 * @access protected
	 */
	protected function upgrade() {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$logo_url = ELEMENTOR_ASSETS_URL . 'images/logo-panel.svg';

		$upgrader_args = [
			'url' => 'update.php?action=upgrade-plugin&plugin=' . rawurlencode( $this->plugin_name ),
			'plugin' => $this->plugin_name,
			'nonce' => 'upgrade-plugin_' . $this->plugin_name,
			'title' => '<img src="' . $logo_url . '" alt="Elementor">' . esc_html__( 'Rollback to Previous Version', 'elementor' ),
		];

		$this->print_inline_style();

		$upgrader = new \Plugin_Upgrader( new \Plugin_Upgrader_Skin( $upgrader_args ) );
		$upgrader->upgrade( $this->plugin_name );
	}

	/**
	 * Run.
	 *
	 * Rollback Elementor to previous versions.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function run() {
		$this->apply_package();
		$this->upgrade();
	}
}
