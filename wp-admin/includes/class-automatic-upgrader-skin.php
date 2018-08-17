<?php
/**
 * Upgrader API: Automatic_Upgrader_Skin class
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 4.6.0
 */

/**
 * Upgrader Skin for Automatic WordPress Upgrades
 *
 * This skin is designed to be used when no output is intended, all output
 * is captured and stored for the caller to process and log/email/discard.
 *
 * @since 3.7.0
 * @since 4.6.0 Moved to its own file from wp-admin/includes/class-wp-upgrader-skins.php.
 *
 * @see Bulk_Upgrader_Skin
 */
class Automatic_Upgrader_Skin extends WP_Upgrader_Skin {
	protected $messages = array();

	/**
	 * Determines whether the upgrader needs FTP/SSH details in order to connect
	 * to the filesystem.
	 *
	 * @since 3.7.0
	 * @since 4.6.0 The `$context` parameter default changed from `false` to an empty string.
	 *
	 * @see request_filesystem_credentials()
	 *
	 * @param bool   $error                        Optional. Whether the current request has failed to connect.
	 *                                             Default false.
	 * @param string $context                      Optional. Full path to the directory that is tested
	 *                                             for being writable. Default empty.
	 * @param bool   $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable. Default false.
	 * @return bool True on success, false on failure.
	 */
	public function request_filesystem_credentials( $error = false, $context = '', $allow_relaxed_file_ownership = false ) {
		if ( $context ) {
			$this->options['context'] = $context;
		}
		// TODO: fix up request_filesystem_credentials(), or split it, to allow us to request a no-output version
		// This will output a credentials form in event of failure, We don't want that, so just hide with a buffer
		ob_start();
		$result = parent::request_filesystem_credentials( $error, $context, $allow_relaxed_file_ownership );
		ob_end_clean();
		return $result;
	}

	/**
	 * @return array
	 */
	public function get_upgrade_messages() {
		return $this->messages;
	}

	/**
	 * @param string|array|WP_Error $data
	 */
	public function feedback( $data ) {
		if ( is_wp_error( $data ) ) {
			$string = $data->get_error_message();
		} elseif ( is_array( $data ) ) {
			return;
		} else {
			$string = $data;
		}
		if ( ! empty( $this->upgrader->strings[ $string ] ) ) {
			$string = $this->upgrader->strings[ $string ];
		}

		if ( strpos( $string, '%' ) !== false ) {
			$args = func_get_args();
			$args = array_splice( $args, 1 );
			if ( ! empty( $args ) ) {
				$string = vsprintf( $string, $args );
			}
		}

		$string = trim( $string );

		// Only allow basic HTML in the messages, as it'll be used in emails/logs rather than direct browser output.
		$string = wp_kses(
			$string,
			array(
				'a'      => array(
					'href' => true,
				),
				'br'     => true,
				'em'     => true,
				'strong' => true,
			)
		);

		if ( empty( $string ) ) {
			return;
		}

		$this->messages[] = $string;
	}

	/**
	 */
	public function header() {
		ob_start();
	}

	/**
	 */
	public function footer() {
		$output = ob_get_clean();
		if ( ! empty( $output ) ) {
			$this->feedback( $output );
		}
	}
}
