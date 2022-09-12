<?php
/**
 * Upgrader API: WP_Upgrader_Skin class
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 4.6.0
 */

/**
 * Generic Skin for the WordPress Upgrader classes. This skin is designed to be extended for specific purposes.
 *
 * @since 2.8.0
 * @since 4.6.0 Moved to its own file from wp-admin/includes/class-wp-upgrader-skins.php.
 */
#[AllowDynamicProperties]
class WP_Upgrader_Skin {

	/**
	 * Holds the upgrader data.
	 *
	 * @since 2.8.0
	 *
	 * @var WP_Upgrader
	 */
	public $upgrader;

	/**
	 * Whether header is done.
	 *
	 * @since 2.8.0
	 *
	 * @var bool
	 */
	public $done_header = false;

	/**
	 * Whether footer is done.
	 *
	 * @since 2.8.0
	 *
	 * @var bool
	 */
	public $done_footer = false;

	/**
	 * Holds the result of an upgrade.
	 *
	 * @since 2.8.0
	 *
	 * @var string|bool|WP_Error
	 */
	public $result = false;

	/**
	 * Holds the options of an upgrade.
	 *
	 * @since 2.8.0
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * Constructor.
	 *
	 * Sets up the generic skin for the WordPress Upgrader classes.
	 *
	 * @since 2.8.0
	 *
	 * @param array $args Optional. The WordPress upgrader skin arguments to
	 *                    override default options. Default empty array.
	 */
	public function __construct( $args = array() ) {
		$defaults      = array(
			'url'     => '',
			'nonce'   => '',
			'title'   => '',
			'context' => false,
		);
		$this->options = wp_parse_args( $args, $defaults );
	}

	/**
	 * @since 2.8.0
	 *
	 * @param WP_Upgrader $upgrader
	 */
	public function set_upgrader( &$upgrader ) {
		if ( is_object( $upgrader ) ) {
			$this->upgrader =& $upgrader;
		}
		$this->add_strings();
	}

	/**
	 * @since 3.0.0
	 */
	public function add_strings() {
	}

	/**
	 * Sets the result of an upgrade.
	 *
	 * @since 2.8.0
	 *
	 * @param string|bool|WP_Error $result The result of an upgrade.
	 */
	public function set_result( $result ) {
		$this->result = $result;
	}

	/**
	 * Displays a form to the user to request for their FTP/SSH details in order
	 * to connect to the filesystem.
	 *
	 * @since 2.8.0
	 * @since 4.6.0 The `$context` parameter default changed from `false` to an empty string.
	 *
	 * @see request_filesystem_credentials()
	 *
	 * @param bool|WP_Error $error                        Optional. Whether the current request has failed to connect,
	 *                                                    or an error object. Default false.
	 * @param string        $context                      Optional. Full path to the directory that is tested
	 *                                                    for being writable. Default empty.
	 * @param bool          $allow_relaxed_file_ownership Optional. Whether to allow Group/World writable. Default false.
	 * @return bool True on success, false on failure.
	 */
	public function request_filesystem_credentials( $error = false, $context = '', $allow_relaxed_file_ownership = false ) {
		$url = $this->options['url'];
		if ( ! $context ) {
			$context = $this->options['context'];
		}
		if ( ! empty( $this->options['nonce'] ) ) {
			$url = wp_nonce_url( $url, $this->options['nonce'] );
		}

		$extra_fields = array();

		return request_filesystem_credentials( $url, '', $error, $context, $extra_fields, $allow_relaxed_file_ownership );
	}

	/**
	 * @since 2.8.0
	 */
	public function header() {
		if ( $this->done_header ) {
			return;
		}
		$this->done_header = true;
		echo '<div class="wrap">';
		echo '<h1>' . $this->options['title'] . '</h1>';
	}

	/**
	 * @since 2.8.0
	 */
	public function footer() {
		if ( $this->done_footer ) {
			return;
		}
		$this->done_footer = true;
		echo '</div>';
	}

	/**
	 * @since 2.8.0
	 *
	 * @param string|WP_Error $errors Errors.
	 */
	public function error( $errors ) {
		if ( ! $this->done_header ) {
			$this->header();
		}
		if ( is_string( $errors ) ) {
			$this->feedback( $errors );
		} elseif ( is_wp_error( $errors ) && $errors->has_errors() ) {
			foreach ( $errors->get_error_messages() as $message ) {
				if ( $errors->get_error_data() && is_string( $errors->get_error_data() ) ) {
					$this->feedback( $message . ' ' . esc_html( strip_tags( $errors->get_error_data() ) ) );
				} else {
					$this->feedback( $message );
				}
			}
		}
	}

	/**
	 * @since 2.8.0
	 * @since 5.9.0 Renamed `$string` (a PHP reserved keyword) to `$feedback` for PHP 8 named parameter support.
	 *
	 * @param string $feedback Message data.
	 * @param mixed  ...$args  Optional text replacements.
	 */
	public function feedback( $feedback, ...$args ) {
		if ( isset( $this->upgrader->strings[ $feedback ] ) ) {
			$feedback = $this->upgrader->strings[ $feedback ];
		}

		if ( strpos( $feedback, '%' ) !== false ) {
			if ( $args ) {
				$args     = array_map( 'strip_tags', $args );
				$args     = array_map( 'esc_html', $args );
				$feedback = vsprintf( $feedback, $args );
			}
		}
		if ( empty( $feedback ) ) {
			return;
		}
		show_message( $feedback );
	}

	/**
	 * Action to perform before an update.
	 *
	 * @since 2.8.0
	 */
	public function before() {}

	/**
	 * Action to perform following an update.
	 *
	 * @since 2.8.0
	 */
	public function after() {}

	/**
	 * Output JavaScript that calls function to decrement the update counts.
	 *
	 * @since 3.9.0
	 *
	 * @param string $type Type of update count to decrement. Likely values include 'plugin',
	 *                     'theme', 'translation', etc.
	 */
	protected function decrement_update_count( $type ) {
		if ( ! $this->result || is_wp_error( $this->result ) || 'up_to_date' === $this->result ) {
			return;
		}

		if ( defined( 'IFRAME_REQUEST' ) ) {
			echo '<script type="text/javascript">
					if ( window.postMessage && JSON ) {
						window.parent.postMessage( JSON.stringify( { action: "decrementUpdateCount", upgradeType: "' . $type . '" } ), window.location.protocol + "//" + window.location.hostname );
					}
				</script>';
		} else {
			echo '<script type="text/javascript">
					(function( wp ) {
						if ( wp && wp.updates && wp.updates.decrementCount ) {
							wp.updates.decrementCount( "' . $type . '" );
						}
					})( window.wp );
				</script>';
		}
	}

	/**
	 * @since 3.0.0
	 */
	public function bulk_header() {}

	/**
	 * @since 3.0.0
	 */
	public function bulk_footer() {}

	/**
	 * Hides the `process_failed` error message when updating by uploading a zip file.
	 *
	 * @since 5.5.0
	 *
	 * @param WP_Error $wp_error WP_Error object.
	 * @return bool
	 */
	public function hide_process_failed( $wp_error ) {
		return false;
	}
}
