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
 */
class Bulk_Upgrader_Skin extends WP_Upgrader_Skin {
	public $in_loop = false;
	/**
	 * @var string|false
	 */
	public $error = false;

	/**
	 *
	 * @param array $args
	 */
	public function __construct($args = array()) {
		$defaults = array( 'url' => '', 'nonce' => '' );
		$args = wp_parse_args($args, $defaults);

		parent::__construct($args);
	}

	/**
	 * @access public
	 */
	public function add_strings() {
		$this->upgrader->strings['skin_upgrade_start'] = __('The update process is starting. This process may take a while on some hosts, so please be patient.');
		/* translators: 1: Title of an update, 2: Error message */
		$this->upgrader->strings['skin_update_failed_error'] = __('An error occurred while updating %1$s: %2$s');
		/* translators: 1: Title of an update */
		$this->upgrader->strings['skin_update_failed'] = __('The update of %1$s failed.');
		/* translators: 1: Title of an update */
		$this->upgrader->strings['skin_update_successful'] = __( '%1$s updated successfully.' ) . ' <a onclick="%2$s" href="#" class="hide-if-no-js"><span>' . __( 'Show Details' ) . '</span><span class="hidden">' . __( 'Hide Details' ) . '</span></a>';
		$this->upgrader->strings['skin_upgrade_end'] = __('All updates have been completed.');
	}

	/**
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
		if ( $this->in_loop )
			echo "$string<br />\n";
		else
			echo "<p>$string</p>\n";
	}

	/**
	 * @access public
	 */
	public function header() {
		// Nothing, This will be displayed within a iframe.
	}

	/**
	 * @access public
	 */
	public function footer() {
		// Nothing, This will be displayed within a iframe.
	}

	/**
	 *
	 * @param string|WP_Error $error
	 */
	public function error($error) {
		if ( is_string($error) && isset( $this->upgrader->strings[$error] ) )
			$this->error = $this->upgrader->strings[$error];

		if ( is_wp_error($error) ) {
			$messages = array();
			foreach ( $error->get_error_messages() as $emessage ) {
				if ( $error->get_error_data() && is_string( $error->get_error_data() ) )
					$messages[] = $emessage . ' ' . esc_html( strip_tags( $error->get_error_data() ) );
				else
					$messages[] = $emessage;
			}
			$this->error = implode(', ', $messages);
		}
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js($this->upgrader->update_current) . '\').hide();</script>';
	}

	/**
	 * @access public
	 */
	public function bulk_header() {
		$this->feedback('skin_upgrade_start');
	}

	/**
	 * @access public
	 */
	public function bulk_footer() {
		$this->feedback('skin_upgrade_end');
	}

	/**
	 *
	 * @param string $title
	 */
	public function before($title = '') {
		$this->in_loop = true;
		printf( '<h2>' . $this->upgrader->strings['skin_before_update_header'] . ' <span class="spinner waiting-' . $this->upgrader->update_current . '"></span></h2>', $title, $this->upgrader->update_current, $this->upgrader->update_count );
		echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js($this->upgrader->update_current) . '\').css("display", "inline-block");</script>';
		echo '<div class="update-messages hide-if-js" id="progress-' . esc_attr($this->upgrader->update_current) . '"><p>';
		$this->flush_output();
	}

	/**
	 *
	 * @param string $title
	 */
	public function after($title = '') {
		echo '</p></div>';
		if ( $this->error || ! $this->result ) {
			if ( $this->error ) {
				echo '<div class="error"><p>' . sprintf($this->upgrader->strings['skin_update_failed_error'], $title, '<strong>' . $this->error . '</strong>' ) . '</p></div>';
			} else {
				echo '<div class="error"><p>' . sprintf($this->upgrader->strings['skin_update_failed'], $title) . '</p></div>';
			}

			echo '<script type="text/javascript">jQuery(\'#progress-' . esc_js($this->upgrader->update_current) . '\').show();</script>';
		}
		if ( $this->result && ! is_wp_error( $this->result ) ) {
			if ( ! $this->error )
				echo '<div class="updated"><p>' . sprintf($this->upgrader->strings['skin_update_successful'], $title, 'jQuery(\'#progress-' . esc_js($this->upgrader->update_current) . '\').toggle();jQuery(\'span\', this).toggle(); return false;') . '</p></div>';
			echo '<script type="text/javascript">jQuery(\'.waiting-' . esc_js($this->upgrader->update_current) . '\').hide();</script>';
		}

		$this->reset();
		$this->flush_output();
	}

	/**
	 * @access public
	 */
	public function reset() {
		$this->in_loop = false;
		$this->error = false;
	}

	/**
	 * @access public
	 */
	public function flush_output() {
		wp_ob_end_flush_all();
		flush();
	}
}
