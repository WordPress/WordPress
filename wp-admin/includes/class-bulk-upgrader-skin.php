<?php
/**
 * Upgrader API: Bulk_Upgrader_Skin class
 *
 * @package WordPress
 * @subpackage Upgrader
 * @since 4.6.0
 */

/**
 * Generic Bulk Upgrader Skin for WordPress Upgrades.
 *
 * @since 3.0.0
 * @since 4.6.0 Moved to its own file from wp-admin/includes/class-wp-upgrader-skins.php.
 *
 * @see WP_Upgrader_Skin
 */
class Bulk_Upgrader_Skin extends WP_Upgrader_Skin {
	public $in_loop = false;
	/**
	 * @var string|false
	 */
	public $error = false;

	/**
	 * @param array $args
	 */
	public function __construct( $args = array() ) {
		$defaults = array(
			'url'   => '',
			'nonce' => '',
		);
		$args     = wp_parse_args( $args, $defaults );

		parent::__construct( $args );
	}

	/**
	 */
	public function add_strings() {
		$this->upgrader->strings['skin_upgrade_start'] = __( 'The update process is starting. This process may take a while on some hosts, so please be patient.' );
		/* translators: 1: Title of an update, 2: Error message. */
		$this->upgrader->strings['skin_update_failed_error'] = __( 'An error occurred while updating %1$s: %2$s' );
		/* translators: %s: Title of an update. */
		$this->upgrader->strings['skin_update_failed'] = __( 'The update of %s failed.' );
		/* translators: %s: Title of an update. */
		$this->upgrader->strings['skin_update_successful'] = __( '%s updated successfully.' );
		$this->upgrader->strings['skin_upgrade_end']       = __( 'All updates have been completed.' );
	}

	/**
	 * @param string $string
	 * @param mixed  ...$args Optional text replacements.
	 */
	public function feedback( $string, ...$args ) {
		if ( isset( $this->upgrader->strings[ $string ] ) ) {
			$string = $this->upgrader->strings[ $string ];
		}

		if ( strpos( $string, '%' ) !== false ) {
			if ( $args ) {
				$args   = array_map( 'strip_tags', $args );
				$args   = array_map( 'esc_html', $args );
				$string = vsprintf( $string, $args );
			}
		}
		if ( empty( $string ) ) {
			return;
		}
		if ( $this->in_loop ) {
			echo "$string<br />\n";
		} else {
			echo "<p>$string</p>\n";
		}
	}

	/**
	 */
	public function header() {
		// Nothing, This will be displayed within a iframe.
	}

	/**
	 */
	public function footer() {
		// Nothing, This will be displayed within a iframe.
	}

	/**
	 * @param string|WP_Error $error
	 */
	public function error( $error ) {
		if ( is_string( $error ) && isset( $this->upgrader->strings[ $error ] ) ) {
			$this->error = $this->upgrader->strings[ $error ];
		}

		if ( is_wp_error( $error ) ) {
			$messages = array();
			foreach ( $error->get_error_messages() as $emessage ) {
				if ( $error->get_error_data() && is_string( $error->get_error_data() ) ) {
					$messages[] = $emessage . ' ' . esc_html( strip_tags( $error->get_error_data() ) );
				} else {
					$messages[] = $emessage;
				}
			}
			$this->error = implode( ', ', $messages );
		}
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').hide();</script>';
	}

	/**
	 */
	public function bulk_header() {
		$this->feedback( 'skin_upgrade_start' );
	}

	/**
	 */
	public function bulk_footer() {
		$this->feedback( 'skin_upgrade_end' );
	}

	/**
	 * @param string $title
	 */
	public function before( $title = '' ) {
		$this->in_loop = true;
		printf( '<h2>' . $this->upgrader->strings['skin_before_update_header'] . ' <span class="spinner waiting-' . $this->upgrader->update_current . '"></span></h2>', $title, $this->upgrader->update_current, $this->upgrader->update_count );
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').css("display", "inline-block");</script>';
		// This progress messages div gets moved via JavaScript when clicking on "Show details.".
		echo '<div class="update-messages hide-if-js" id="progress-' . esc_attr( $this->upgrader->update_current ) . '"><p>';
		$this->flush_output();
	}

	/**
	 * @param string $title
	 */
	public function after( $title = '' ) {
		echo '</p></div>';
		if ( $this->error || ! $this->result ) {
			if ( $this->error ) {
				echo '<div class="error"><p>' . sprintf( $this->upgrader->strings['skin_update_failed_error'], $title, '<strong>' . $this->error . '</strong>' ) . '</p></div>';
			} else {
				echo '<div class="error"><p>' . sprintf( $this->upgrader->strings['skin_update_failed'], $title ) . '</p></div>';
			}

			echo '<script type="text/javascript">jQuery(\'#progress-' . esc_js( $this->upgrader->update_current ) . '\').show();</script>';
		}
		if ( $this->result && ! is_wp_error( $this->result ) ) {
			if ( ! $this->error ) {
				echo '<div class="updated js-update-details" data-update-details="progress-' . esc_attr( $this->upgrader->update_current ) . '">' .
					'<p>' . sprintf( $this->upgrader->strings['skin_update_successful'], $title ) .
					' <button type="button" class="hide-if-no-js button-link js-update-details-toggle" aria-expanded="false">' . __( 'Show details.' ) . '</button>' .
					'</p></div>';
			}

			echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').hide();</script>';
		}

		$this->reset();
		$this->flush_output();
	}

	/**
	 */
	public function reset() {
		$this->in_loop = false;
		$this->error   = false;
	}

	/**
	 */
	public function flush_output() {
		wp_ob_end_flush_all();
		flush();
	}
}
