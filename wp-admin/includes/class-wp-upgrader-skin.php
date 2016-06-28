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
class WP_Upgrader_Skin {

	public $upgrader;
	public $done_header = false;
	public $done_footer = false;

	/**
	 *
	 * @var string|false|WP_Error
	 */
	public $result = false;
	public $options = array();

	/**
	 *
	 * @param array $args
	 */
	public function __construct($args = array()) {
		$defaults = array( 'url' => '', 'nonce' => '', 'title' => '', 'context' => false );
		$this->options = wp_parse_args($args, $defaults);
	}

	/**
	 * @param WP_Upgrader $upgrader
	 */
	public function set_upgrader(&$upgrader) {
		if ( is_object($upgrader) )
			$this->upgrader =& $upgrader;
		$this->add_strings();
	}

	/**
	 * @access public
	 */
	public function add_strings() {
	}

	/**
	 *
	 * @param string|false|WP_Error $result
	 */
	public function set_result($result) {
		$this->result = $result;
	}

	/**
	 *
	 * @param bool   $error
	 * @param string $context
	 * @param bool   $allow_relaxed_file_ownership
	 * @return type
	 */
	public function request_filesystem_credentials( $error = false, $context = false, $allow_relaxed_file_ownership = false ) {
		$url = $this->options['url'];
		if ( ! $context ) {
			$context = $this->options['context'];
		}
		if ( !empty($this->options['nonce']) ) {
			$url = wp_nonce_url($url, $this->options['nonce']);
		}

		$extra_fields = array();

		return request_filesystem_credentials( $url, '', $error, $context, $extra_fields, $allow_relaxed_file_ownership );
	}

	/**
	 * @access public
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
	 * @access public
	 */
	public function footer() {
		if ( $this->done_footer ) {
			return;
		}
		$this->done_footer = true;
		echo '</div>';
	}

	/**
	 *
	 * @param string|WP_Error $errors
	 */
	public function error($errors) {
		if ( ! $this->done_header )
			$this->header();
		if ( is_string($errors) ) {
			$this->feedback($errors);
		} elseif ( is_wp_error($errors) && $errors->get_error_code() ) {
			foreach ( $errors->get_error_messages() as $message ) {
				if ( $errors->get_error_data() && is_string( $errors->get_error_data() ) )
					$this->feedback($message . ' ' . esc_html( strip_tags( $errors->get_error_data() ) ) );
				else
					$this->feedback($message);
			}
		}
	}

	/**
	 *
	 * @param string $string
	 */
	public function feedback($string) {
		if ( isset( $this->upgrader->strings[$string] ) )
			$string = $this->upgrader->strings[$string];

		if ( strpos($string, '%') !== false ) {
			$args = func_get_args();
			$args = array_splice($args, 1);
			if ( $args ) {
				$args = array_map( 'strip_tags', $args );
				$args = array_map( 'esc_html', $args );
				$string = vsprintf($string, $args);
			}
		}
		if ( empty($string) )
			return;
		show_message($string);
	}

	/**
	 * @access public
	 */
	public function before() {}

	/**
	 * @access public
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
						if ( wp && wp.updates.decrementCount ) {
							wp.updates.decrementCount( "' . $type . '" );
						}
					})( window.wp );
				</script>';
		}
	}

	/**
	 * @access public
	 */
	public function bulk_header() {}

	/**
	 * @access public
	 */
	public function bulk_footer() {}
}
