<?php

class WPCode_Notice {

	/**
	 * Not dismissible.
	 *
	 * Constant attended to use as the value of the $args['dismiss'] argument.
	 * DISMISS_NONE means that the notice is not dismissible.
	 *
	 */
	const DISMISS_NONE = 0;

	/**
	 * Dismissible global.
	 *
	 * Constant attended to use as the value of the $args['dismiss'] argument.
	 * DISMISS_GLOBAL means that the notice will have the dismiss button, and after clicking this button, the notice will be dismissed for all users.
	 *
	 */
	const DISMISS_GLOBAL = 1;

	/**
	 * Dismissible per user.
	 *
	 * Constant attended to use as the value of the $args['dismiss'] argument.
	 * DISMISS_USER means that the notice will have the dismiss button, and after clicking this button, the notice will be dismissed only for the current user..
	 *
	 */
	const DISMISS_USER = 2;

	/**
	 * Added notices.
	 *
	 * @var array
	 */
	public $notices = array();

	/**
	 * Top notices, displayed separately.
	 *
	 * @var array
	 */
	public $notices_top = array();

	/**
	 * Init.
	 *
	 */
	public function __construct() {

		$this->hooks();
	}

	/**
	 * Hooks.
	 *
	 */
	public function hooks() {
		add_action( 'admin_notices', array( $this, 'display' ), 999000 );
		// Hook for our specific pages where we hide all other admin notices.
		add_action( 'wpcode_admin_notices', array( $this, 'display' ), 10 );
		add_action( 'wp_ajax_wpcode_notice_dismiss', array( $this, 'dismiss_ajax' ) );

		// Display notices above the header.
		add_action( 'wpcode_admin_page', array( $this, 'display_top' ), 5 );
	}

	/**
	 * Enqueue assets.
	 */
	private function enqueues() {

		wp_enqueue_script(
			'wpcode-admin-notices',
			WPCODE_PLUGIN_URL . 'build/notices.js',
			array( 'jquery' ),
			WPCODE_VERSION,
			true
		);

		wp_localize_script(
			'wpcode-admin-notices',
			'wpcode_admin_notices',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'wpcode-admin' ),
			)
		);
	}

	/**
	 * Display the notices.
	 */
	public function display() {

		$dismissed_notices = get_user_meta( get_current_user_id(), 'wpcode_admin_notices', true );
		$dismissed_notices = is_array( $dismissed_notices ) ? $dismissed_notices : array();
		$dismissed_notices = array_merge( $dismissed_notices, (array) get_option( 'wpcode_admin_notices', array() ) );

		foreach ( $this->notices as $slug => $notice ) {
			if ( isset( $dismissed_notices[ $slug ] ) && ! empty( $dismissed_notices[ $slug ]['dismissed'] ) ) {
				unset( $this->notices[ $slug ] );
			}
		}

		$output = implode( '', $this->notices );

		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// Enqueue script only when it's needed.
		if ( strpos( $output, 'is-dismissible' ) !== false ) {
			$this->enqueues();
		}
	}

	/**
	 * Display the notices at the top of the WPC pages.
	 */
	public function display_top() {
		$dismissed_notices = get_user_meta( get_current_user_id(), 'wpcode_admin_notices', true );
		$dismissed_notices = is_array( $dismissed_notices ) ? $dismissed_notices : array();
		$dismissed_notices = array_merge( $dismissed_notices, (array) get_option( 'wpcode_admin_notices', array() ) );

		foreach ( $this->notices_top as $slug => $notice ) {
			if ( isset( $dismissed_notices[ $slug ] ) && ! empty( $dismissed_notices[ $slug ]['dismissed'] ) ) {
				unset( $this->notices_top[ $slug ] );
			}
		}

		$output = implode( '', $this->notices_top );

		if ( ! empty( $output ) ) {
			echo '<div class="wpcode-notice-top-area">';
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '</div>';
		}

		// Enqueue script only when it's needed.
		if ( strpos( $output, 'is-dismissible' ) !== false ) {
			$this->enqueues();
		}
	}

	/**
	 * Add notice to the registry.
	 *
	 *
	 * @param string $message Message to display.
	 * @param string $type Type of the notice. Can be [ '' (default) | 'info' | 'error' | 'success' | 'warning' ].
	 * @param array  $args The array of additional arguments. Please see the $defaults array below.
	 */
	public static function add( $message, $type = '', $args = [] ) {

		static $uniq_id = 0;

		$defaults = [
			'dismiss' => self::DISMISS_NONE,
			// Dismissible level: one of the self::DISMISS_* const. By default notice is not dismissible.
			'slug'    => '',
			// Slug. Should be unique if dismissible is not equal self::DISMISS_NONE.
			'autop'   => true,
			// `false` if not needed to pass message through wpautop().
			'class'   => '',
			// Additional CSS class.
		];

		$args = wp_parse_args( $args, $defaults );

		$dismissible = (int) $args['dismiss'];
		$dismissible = $dismissible > self::DISMISS_USER ? self::DISMISS_USER : $dismissible;

		$class  = $dismissible > self::DISMISS_NONE ? ' is-dismissible' : '';
		$global = ( $dismissible === self::DISMISS_GLOBAL ) ? 'global-' : '';
		$slug   = sanitize_key( $args['slug'] );

		++ $uniq_id;

		$uniq_id += ( $uniq_id === (int) $slug ) ? 1 : 0;

		$id = 'wpcode-notice-' . $global;

		$id .= empty( $slug ) ? $uniq_id : $slug;

		$type_class = ! empty( $type ) ? 'notice-' . esc_attr( sanitize_key( $type ) ) : '';
		$class      = empty( $args['class'] ) ? $class : $class . ' ' . esc_attr( sanitize_key( $args['class'] ) );
		$message    = $args['autop'] ? wpautop( $message ) : $message;
		$notice     = sprintf(
			'<div class="notice wpcode-notice %s%s" id="%s">%s</div>',
			esc_attr( $type_class ),
			esc_attr( $class ),
			esc_attr( $id ),
			$message
		);

		if ( 'top' === $type ) {
			if ( empty( $slug ) ) {
				wpcode()->notice->notices_top[] = $notice;
			} else {
				wpcode()->notice->notices_top[ $slug ] = $notice;
			}

			return; // Don't mix top notices.
		}

		if ( empty( $slug ) ) {
			wpcode()->notice->notices[] = $notice;
		} else {
			wpcode()->notice->notices[ $slug ] = $notice;
		}
	}

	/**
	 * Add info notice.
	 *
	 *
	 * @param string $message Message to display.
	 * @param array  $args Array of additional arguments. Details in the self::add() method.
	 */
	public static function info( $message, $args = [] ) {

		self::add( $message, 'info', $args );
	}

	/**
	 * Add top notice (displayed before the header on wpcode pages only).
	 *
	 *
	 * @param string $message Message to display.
	 * @param array  $args Array of additional arguments. Details in the self::add() method.
	 */
	public static function top( $message, $args = [] ) {

		self::add( $message, 'top', $args );
	}

	/**
	 * Add error notice.
	 *
	 *
	 * @param string $message Message to display.
	 * @param array  $args Array of additional arguments. Details in the self::add() method.
	 */
	public static function error( $message, $args = [] ) {

		self::add( $message, 'error', $args );
	}

	/**
	 * Add success notice.
	 *
	 *
	 * @param string $message Message to display.
	 * @param array  $args Array of additional arguments. Details in the self::add() method.
	 */
	public static function success( $message, $args = [] ) {

		self::add( $message, 'success', $args );
	}

	/**
	 * Add warning notice.
	 *
	 *
	 * @param string $message Message to display.
	 * @param array  $args Array of additional arguments. Details in the self::add() method.
	 */
	public static function warning( $message, $args = [] ) {

		self::add( $message, 'warning', $args );
	}

	/**
	 * AJAX routine that updates dismissed notices meta data.
	 *

	 */
	public function dismiss_ajax() {

		// Run a security check.
		check_ajax_referer( 'wpcode-admin' );

		// Sanitize POST data.
		$post = array_map( 'sanitize_key', wp_unslash( $_POST ) );

		// Update notices meta data.
		if ( strpos( $post['id'], 'global-' ) !== false ) {

			// Check for permissions.
			if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
				wp_send_json_error();
			}

			$notices = $this->dismiss_global( $post['id'] );
			$level   = self::DISMISS_GLOBAL;

		} else {

			$notices = $this->dismiss_user( $post['id'] );
			$level   = self::DISMISS_USER;
		}

		/**
		 * Allows developers to apply additional logic to the dismissing notice process.
		 * Executes after updating option or user meta (according to the notice level).
		 *
		 *
		 * @param string  $notice_id Notice ID (slug).
		 * @param integer $level Notice level.
		 * @param array   $notices Dismissed notices.
		 */
		do_action( 'wpcode_admin_notice_dismiss_ajax', $post['id'], $level, $notices );

		wp_send_json_success();
	}

	/**
	 * AJAX sub-routine that updates dismissed notices option.
	 *
	 *
	 * @param string $id Notice Id.
	 *
	 * @return array Notices.
	 */
	private function dismiss_global( $id ) {

		$id             = str_replace( 'global-', '', $id );
		$notices        = get_option( 'wpcode_admin_notices', array() );
		$notices[ $id ] = array(
			'time'      => time(),
			'dismissed' => true,
		);

		update_option( 'wpcode_admin_notices', $notices, true );

		return $notices;
	}

	/**
	 *  AJAX sub-routine that updates dismissed notices user meta.
	 *
	 *
	 * @param string $id Notice Id.
	 *
	 * @return array Notices.
	 */
	private function dismiss_user( $id ) {

		$user_id        = get_current_user_id();
		$notices        = get_user_meta( $user_id, 'wpcode_admin_notices', true );
		$notices        = ! is_array( $notices ) ? array() : $notices;
		$notices[ $id ] = array(
			'time'      => time(),
			'dismissed' => true,
		);

		update_user_meta( $user_id, 'wpcode_admin_notices', $notices );

		return $notices;
	}
}
