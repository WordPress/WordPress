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

	/**
	 * Whether the bulk update process has started.
	 *
	 * @since 3.0.0
	 * @var bool
	 */
	public $in_loop = false;

	/**
	 * Stores an error message about the update.
	 *
	 * @since 3.0.0
	 * @var string|false
	 */
	public $error = false;

	/**
	 * Constructor.
	 *
	 * Sets up the generic skin for the Bulk Upgrader classes.
	 *
	 * @since 3.0.0
	 *
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
	 * Sets up the strings used in the update process.
	 *
	 * @since 3.0.0
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
	 * Displays a message about the update.
	 *
	 * @since 3.0.0
	 * @since 5.9.0 Renamed `$string` (a PHP reserved keyword) to `$feedback` for PHP 8 named parameter support.
	 *
	 * @param string $feedback Message data.
	 * @param mixed  ...$args  Optional text replacements.
	 */
	public function feedback( $feedback, ...$args ) {
		if ( isset( $this->upgrader->strings[ $feedback ] ) ) {
			$feedback = $this->upgrader->strings[ $feedback ];
		}

		if ( str_contains( $feedback, '%' ) ) {
			if ( $args ) {
				$args     = array_map( 'strip_tags', $args );
				$args     = array_map( 'esc_html', $args );
				$feedback = vsprintf( $feedback, $args );
			}
		}
		if ( empty( $feedback ) ) {
			return;
		}
		if ( $this->in_loop ) {
			echo "$feedback<br />\n";
		} else {
			echo "<p>$feedback</p>\n";
		}
	}

	/**
	 * Displays the header before the update process.
	 *
	 * @since 3.0.0
	 */
	public function header() {
		// Nothing. This will be displayed within an iframe.
	}

	/**
	 * Displays the footer following the update process.
	 *
	 * @since 3.0.0
	 */
	public function footer() {
		// Nothing. This will be displayed within an iframe.
	}

	/**
	 * Displays an error message about the update.
	 *
	 * @since 3.0.0
	 * @since 5.9.0 Renamed `$error` to `$errors` for PHP 8 named parameter support.
	 *
	 * @param string|WP_Error $errors Errors.
	 */
	public function error( $errors ) {
		if ( is_string( $errors ) && isset( $this->upgrader->strings[ $errors ] ) ) {
			$this->error = $this->upgrader->strings[ $errors ];
		}

		if ( is_wp_error( $errors ) ) {
			$messages = array();
			foreach ( $errors->get_error_messages() as $emessage ) {
				if ( $errors->get_error_data() && is_string( $errors->get_error_data() ) ) {
					$messages[] = $emessage . ' ' . esc_html( strip_tags( $errors->get_error_data() ) );
				} else {
					$messages[] = $emessage;
				}
			}
			$this->error = implode( ', ', $messages );
		}
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').hide();</script>';
	}

	/**
	 * Displays the header before the bulk update process.
	 *
	 * @since 3.0.0
	 */
	public function bulk_header() {
		$this->feedback( 'skin_upgrade_start' );
	}

	/**
	 * Displays the footer following the bulk update process.
	 *
	 * @since 3.0.0
	 */
	public function bulk_footer() {
		$this->feedback( 'skin_upgrade_end' );
	}

	/**
	 * Performs an action before a bulk update.
	 *
	 * @since 3.0.0
	 *
	 * @param string $title
	 */
	public function before( $title = '' ) {
		$this->in_loop = true;
		printf( '<h2>' . $this->upgrader->strings['skin_before_update_header'] . ' <span class="spinner waiting-' . $this->upgrader->update_current . '"></span></h2>', $title, $this->upgrader->update_current, $this->upgrader->update_count );
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').css("display", "inline-block");</script>';
		// This progress messages div gets moved via JavaScript when clicking on "More details.".
		echo '<div class="update-messages hide-if-js" id="progress-' . esc_attr( $this->upgrader->update_current ) . '"><p>';
		$this->flush_output();
	}

	/**
	 * Performs an action following a bulk update.
	 *
	 * @since 3.0.0
	 *
	 * @param string $title
	 */
	public function after( $title = '' ) {
		echo '</p></div>';
		if ( $this->error || ! $this->result ) {
			if ( $this->error ) {
				$after_error_message = sprintf( $this->upgrader->strings['skin_update_failed_error'], $title, '<strong>' . $this->error . '</strong>' );
			} else {
				$after_error_message = sprintf( $this->upgrader->strings['skin_update_failed'], $title );
			}
			wp_admin_notice(
				$after_error_message,
				array(
					'additional_classes' => array( 'error' ),
				)
			);

			echo '<script type="text/javascript">jQuery(\'#progress-' . esc_js( $this->upgrader->update_current ) . '\').show();</script>';
		}
		if ( $this->result && ! is_wp_error( $this->result ) ) {
			if ( ! $this->error ) {
				echo '<div class="updated js-update-details" data-update-details="progress-' . esc_attr( $this->upgrader->update_current ) . '">' .
					'<p>' . sprintf( $this->upgrader->strings['skin_update_successful'], $title ) .
					' <button type="button" class="hide-if-no-js button-link js-update-details-toggle" aria-expanded="false">' . __( 'More details.' ) . '<span class="dashicons dashicons-arrow-down" aria-hidden="true"></span></button>' .
					'</p></div>';
			}

			echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js( $this->upgrader->update_current ) . '\').hide();</script>';
		}

		$this->reset();
		$this->flush_output();
	}

	/**
	 * Resets the properties used in the update process.
	 *
	 * @since 3.0.0
	 */
	public function reset() {
		$this->in_loop = false;
		$this->error   = false;
	}

	/**
	 * Flushes all output buffers.
	 *
	 * @since 3.0.0
	 */
	public function flush_output() {
		wp_ob_end_flush_all();
		flush();
	}
}
