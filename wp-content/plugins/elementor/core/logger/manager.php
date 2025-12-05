<?php
namespace Elementor\Core\Logger;

use Elementor\Core\Base\Module as BaseModule;
use Elementor\Core\Common\Modules\Ajax\Module;
use Elementor\Core\Editor\Editor;
use Elementor\Core\Logger\Loggers\Logger_Interface;
use Elementor\Core\Logger\Items\PHP;
use Elementor\Core\Logger\Items\JS;
use Elementor\Plugin;
use Elementor\Modules\System_Info\Module as System_Info;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Manager extends BaseModule {

	protected $loggers = [];

	protected $default_logger = '';

	public function get_name() {
		return 'log';
	}

	public function shutdown( $last_error = null, $should_exit = false ) {
		if ( ! $last_error ) {
			$last_error = error_get_last();
		}

		if ( ! $last_error ) {
			return;
		}

		if ( empty( $last_error['file'] ) ) {
			return;
		}

		if ( ! Utils::is_elementor_path( $last_error['file'] ) ) {
			return;
		}

		$last_error['type'] = $this->get_log_type_from_php_error( $last_error['type'] );
		$last_error['trace'] = true;

		$item = new PHP( $last_error );

		$this->get_logger()->log( $item );

		if ( $should_exit ) {
			exit;
		}
	}

	public function rest_error_handler( $error_number, $error_message, $error_file, $error_line ) {
		// Temporary solution until all PHP notices will be fixed in the core and pro.
		if ( Utils::is_wp_cli() ) {
			return null;
		}

		$error = new \WP_Error( $error_number, $error_message, [
			'type' => $error_number,
			'message' => $error_message,
			'file' => $error_file,
			'line' => $error_line,
		] );

		if ( ! Utils::is_elementor_path( $error_file ) ) {
			// Do execute PHP internal error handler.
			return false;
		}

		$is_an_error = in_array( // It can be notice or warning
			$error_number,
			[ E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR ],
			true
		);

		$error_data = $error->get_error_data();

		// TODO: This part should be modular, temporary hard-coded.
		// Notify $e.data.
		if ( $is_an_error && ! headers_sent() ) {
			header( 'Content-Type: application/json; charset=UTF-8' );

			http_response_code( 500 );

			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				echo wp_json_encode( $error_data );
			} else {
				echo wp_json_encode( [
					'message' => 'Server error, see Elementor => System Info',
				] );
			}
		}

		$this->shutdown( $error_data, $is_an_error );
	}

	public function register_error_handler() {
		set_error_handler( [ $this, 'rest_error_handler' ], E_ALL );
	}

	public function add_system_info_report() {
		System_Info::add_report(
			'log', [
				'file_name' => __DIR__ . '/log-reporter.php',
				'class_name' => __NAMESPACE__ . '\Log_Reporter',
			]
		);
	}

	/**
	 * Javascript log.
	 *
	 * Log Elementor errors and save them in the database.
	 *
	 * Fired by `wp_ajax_elementor_js_log` action.
	 */
	public function js_log() {
		/** @var Module $ajax */
		$ajax = Plugin::$instance->common->get_component( 'ajax' );

		// PHPCS ignore is added throughout this method because nonce verification happens in the $ajax->verify_request_nonce() method.
		if ( ! $ajax->verify_request_nonce() || empty( $_POST['data'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_send_json_error();
		}

		if ( ! current_user_can( Editor::EDITING_CAPABILITY ) ) {
			wp_send_json_error( 'Permission denied' );
		}

		// PHPCS - See comment above.
		$data = Utils::get_super_global_value( $_POST, 'data' ) ?? []; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		array_walk_recursive( $data, function( &$value ) {
			$value = sanitize_text_field( $value );
		} );

		// PHPCS - See comment above.
		foreach ( $data as $error ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$error['type'] = Logger_Interface::LEVEL_ERROR;

			if ( ! empty( $error['customFields'] ) ) {
				$error['meta'] = $error['customFields'];
			}

			$item = new JS( $error );
			$this->get_logger()->log( $item );
		}

		wp_send_json_success();
	}

	public function register_logger( $name, $class_name ) {
		$this->loggers[ $name ] = $class_name;
	}

	public function set_default_logger( $name ) {
		if ( ! empty( $this->loggers[ $name ] ) ) {
			$this->default_logger = $name;
		}
	}

	public function register_default_loggers() {
		$this->register_logger( 'db', 'Elementor\Core\Logger\Loggers\Db' );
		$this->set_default_logger( 'db' );
	}

	/**
	 * @param string $name
	 *
	 * @return Logger_Interface
	 */
	public function get_logger( $name = '' ) {
		$this->register_loggers();

		if ( empty( $name ) || ! isset( $this->loggers[ $name ] ) ) {
			$name = $this->default_logger;
		}

		if ( ! $this->get_component( $name ) ) {
			$this->add_component( $name, new $this->loggers[ $name ]() );
		}

		return $this->get_component( $name );
	}

	/**
	 * @param string $message
	 * @param array  $args
	 *
	 * @return void
	 */
	public function log( $message, $args = [] ) {
		$this->get_logger()->log( $message, $args );
	}

	/**
	 * @param string $message
	 * @param array  $args
	 *
	 * @return void
	 */
	public function info( $message, $args = [] ) {
		$this->get_logger()->info( $message, $args );
	}

	/**
	 * @param string $message
	 * @param array  $args
	 *
	 * @return void
	 */
	public function notice( $message, $args = [] ) {
		$this->get_logger()->notice( $message, $args );
	}

	/**
	 * @param string $message
	 * @param array  $args
	 *
	 * @return void
	 */
	public function warning( $message, $args = [] ) {
		$this->get_logger()->warning( $message, $args );
	}

	/**
	 * @param string $message
	 * @param array  $args
	 *
	 * @return void
	 */
	public function error( $message, $args = [] ) {
		$this->get_logger()->error( $message, $args );
	}

	private function get_log_type_from_php_error( $type ) {
		$error_map = [
			E_CORE_ERROR => Logger_Interface::LEVEL_ERROR,
			E_ERROR => Logger_Interface::LEVEL_ERROR,
			E_USER_ERROR => Logger_Interface::LEVEL_ERROR,
			E_COMPILE_ERROR => Logger_Interface::LEVEL_ERROR,
			E_RECOVERABLE_ERROR => Logger_Interface::LEVEL_ERROR,
			E_PARSE => Logger_Interface::LEVEL_ERROR,
			E_STRICT => Logger_Interface::LEVEL_ERROR,

			E_WARNING => Logger_Interface::LEVEL_WARNING,
			E_USER_WARNING => Logger_Interface::LEVEL_WARNING,
			E_CORE_WARNING => Logger_Interface::LEVEL_WARNING,
			E_COMPILE_WARNING => Logger_Interface::LEVEL_WARNING,

			E_NOTICE => Logger_Interface::LEVEL_NOTICE,
			E_USER_NOTICE => Logger_Interface::LEVEL_NOTICE,
			E_DEPRECATED => Logger_Interface::LEVEL_NOTICE,
			E_USER_DEPRECATED => Logger_Interface::LEVEL_NOTICE,
		];

		return isset( $error_map[ $type ] ) ? $error_map[ $type ] : Logger_Interface::LEVEL_ERROR;
	}

	private function register_loggers() {
		if ( ! did_action( 'elementor/loggers/register' ) ) {
			do_action( 'elementor/loggers/register', $this );
		}
	}

	public function __construct() {
		register_shutdown_function( [ $this, 'shutdown' ] );

		add_action( 'admin_init', [ $this, 'add_system_info_report' ], 80 );

		add_action( 'wp_ajax_elementor_js_log', [ $this, 'js_log' ] );

		add_action( 'elementor/loggers/register', [ $this, 'register_default_loggers' ] );
	}
}
