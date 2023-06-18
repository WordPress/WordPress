<?php

namespace Automattic\WooCommerce\Admin\PluginsInstallLoggers;

/**
 * A logger to log plugin installation progress in real time to an option.
 */
class AsyncPluginsInstallLogger implements PluginsInstallLogger {

	/**
	 * Variable to store logs.
	 *
	 * @var string $option_name option name to store logs.
	 */
	private $option_name;

	/**
	 * Constructor.
	 *
	 * @param string $option_name option name.
	 */
	public function __construct( string $option_name ) {
		$this->option_name = $option_name;
		add_option(
			$this->option_name,
			array(
				'created_time' => time(),
				'status'       => 'pending',
				'plugins'      => array(),
			),
			'',
			'no'
		);

		// Set status as failed in case we run out of exectuion time.
		register_shutdown_function(
			function () {
				$error = error_get_last();
				if ( isset( $error['type'] ) && E_ERROR === $error['type'] ) {
					$option           = $this->get();
					$option['status'] = 'failed';
					$this->update( $option );
				}
			}
		);
	}

	/**
	 * Update the option.
	 *
	 * @param array $data New data.
	 *
	 * @return bool
	 */
	private function update( array $data ) {
		return update_option( $this->option_name, $data );
	}

	/**
	 * Retreive the option.
	 *
	 * @return false|mixed|void
	 */
	private function get() {
		return get_option( $this->option_name );
	}

	/**
	 * Add requested plugin.
	 *
	 * @param string $plugin_name plugin name.
	 *
	 * @return void
	 */
	public function install_requested( string $plugin_name ) {
		$option = $this->get();
		if ( ! isset( $option['plugins'][ $plugin_name ] ) ) {
			$option['plugins'][ $plugin_name ] = array(
				'status'           => 'installing',
				'errors'           => array(),
				'install_duration' => 0,
			);
		}
		$this->update( $option );
	}

	/**
	 * Add installed plugin.
	 *
	 * @param string $plugin_name plugin name.
	 * @param int    $duration time took to install plugin.
	 *
	 * @return void
	 */
	public function installed( string $plugin_name, int $duration ) {
		$option = $this->get();

		$option['plugins'][ $plugin_name ]['status']           = 'installed';
		$option['plugins'][ $plugin_name ]['install_duration'] = $duration;
		$this->update( $option );
	}

	/**
	 * Add an error.
	 *
	 * @param string      $plugin_name plugin name.
	 * @param string|null $error_message error message.
	 *
	 * @return void
	 */
	public function add_error( string $plugin_name, string $error_message = null ) {
		$option = $this->get();

		$option['plugins'][ $plugin_name ]['errors'][] = $error_message;
		$option['plugins'][ $plugin_name ]['status']   = 'failed';
		$option['status']                              = 'failed';

		$this->update( $option );
	}

	/**
	 * Record completed_time.
	 *
	 * @return void
	 */
	public function complete() {
		$option = $this->get();

		$option['complete_time'] = time();
		$option['status']        = 'complete';

		$this->update( $option );
	}
}
