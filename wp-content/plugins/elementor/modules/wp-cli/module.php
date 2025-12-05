<?php
namespace Elementor\Modules\WpCli;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Logger\Manager as Logger;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Module extends BaseModule {

	/**
	 * Get module name.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'wp-cli';
	}

	/**
	 * @since 2.1.0
	 * @access public
	 * @static
	 */
	public static function is_active() {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * @param Logger $logger
	 * @access public
	 */
	public function register_cli_logger( $logger ) {
		$logger->register_logger( 'cli', 'Elementor\Modules\WpCli\Cli_Logger' );
		$logger->set_default_logger( 'cli' );
	}

	public function init_common() {
		Plugin::$instance->init_common();
	}

	/**
	 *
	 * @since 2.1.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'cli_init', [ $this, 'init_common' ] );
		add_action( 'elementor/loggers/register', [ $this, 'register_cli_logger' ] );

		\WP_CLI::add_command( 'elementor', '\Elementor\Modules\WpCli\Command' );
		\WP_CLI::add_command( 'elementor update', '\Elementor\Modules\WpCli\Update' );
		\WP_CLI::add_command( 'elementor library', '\Elementor\Modules\WpCli\Library' );
	}
}
