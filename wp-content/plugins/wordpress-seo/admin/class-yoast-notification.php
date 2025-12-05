<?php
/**
 * WPSEO plugin file.
 *
 * @package WPSEO\Admin\Notifications
 * @since   1.5.3
 */

/**
 * Implements individual notification.
 */
class Yoast_Notification {

	/**
	 * Type of capability check.
	 *
	 * @var string
	 */
	public const MATCH_ALL = 'all';

	/**
	 * Type of capability check.
	 *
	 * @var string
	 */
	public const MATCH_ANY = 'any';

	/**
	 * Notification type.
	 *
	 * @var string
	 */
	public const ERROR = 'error';

	/**
	 * Notification type.
	 *
	 * @var string
	 */
	public const WARNING = 'warning';

	/**
	 * Notification type.
	 *
	 * @var string
	 */
	public const UPDATED = 'updated';

	/**
	 * Options of this Notification.
	 *
	 * Contains optional arguments:
	 *
	 * -             type: The notification type, i.e. 'updated' or 'error'
	 * -               id: The ID of the notification
	 * -            nonce: Security nonce to use in case of dismissible notice.
	 * -         priority: From 0 to 1, determines the order of Notifications.
	 * -    dismissal_key: Option name to save dismissal information in, ID will be used if not supplied.
	 * -     capabilities: Capabilities that a user must have for this Notification to show.
	 * - capability_check: How to check capability pass: all or any.
	 * -  wpseo_page_only: Only display on wpseo page or on every page.
	 * -   yoast_branding: Whether to show the Yoast SEO branding in the notification.
	 * -    resolve_nonce: Security nonce to use in case of resolving the notification.
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Contains default values for the optional arguments.
	 *
	 * @var array
	 */
	private $defaults = [
		'type'             => self::UPDATED,
		'id'               => '',
		'user_id'          => null,
		'nonce'            => null,
		'priority'         => 0.5,
		'data_json'        => [],
		'dismissal_key'    => null,
		'capabilities'     => [],
		'capability_check' => self::MATCH_ALL,
		'yoast_branding'   => false,
		'resolve_nonce'    => '',
	];

	/**
	 * The message for the notification.
	 *
	 * @var string
	 */
	private $message;

	/**
	 * Notification class constructor.
	 *
	 * @param string $message Message string.
	 * @param array  $options Set of options.
	 */
	public function __construct( $message, $options = [] ) {
		$this->message = $message;
		$this->options = $this->normalize_options( $options );
	}

	/**
	 * Retrieve notification ID string.
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->options['id'];
	}

	/**
	 * Retrieve the user to show the notification for.
	 *
	 * @deprecated 21.6
	 * @codeCoverageIgnore
	 *
	 * @return WP_User|null The user to show this notification for.
	 */
	public function get_user() {
		_deprecated_function( __METHOD__, 'Yoast SEO 21.6' );
		return null;
	}

	/**
	 * Retrieve the id of the user to show the notification for.
	 *
	 * Returns the id of the current user if not user has been sent.
	 *
	 * @return int The user id
	 */
	public function get_user_id() {
		return ( $this->options['user_id'] ?? get_current_user_id() );
	}

	/**
	 * Retrieve nonce identifier.
	 *
	 * @return string|null Nonce for this Notification.
	 */
	public function get_nonce() {
		if ( $this->options['id'] && empty( $this->options['nonce'] ) ) {
			$this->options['nonce'] = wp_create_nonce( $this->options['id'] );
		}

		return $this->options['nonce'];
	}

	/**
	 * Make sure the nonce is up to date.
	 *
	 * @return void
	 */
	public function refresh_nonce() {
		if ( $this->options['id'] ) {
			$this->options['nonce'] = wp_create_nonce( $this->options['id'] );
		}
	}

	/**
	 * Get the type of the notification.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->options['type'];
	}

	/**
	 * Priority of the notification.
	 *
	 * Relative to the type.
	 *
	 * @return float Returns the priority between 0 and 1.
	 */
	public function get_priority() {
		return $this->options['priority'];
	}

	/**
	 * Get the nonce to resolve the alert.
	 *
	 * @return string
	 */
	public function get_resolve_nonce() {
		return $this->options['resolve_nonce'];
	}

	/**
	 * Get the User Meta key to check for dismissal of notification.
	 *
	 * @return string User Meta Option key that registers dismissal.
	 */
	public function get_dismissal_key() {
		if ( empty( $this->options['dismissal_key'] ) ) {
			return $this->options['id'];
		}

		return $this->options['dismissal_key'];
	}

	/**
	 * Is this Notification persistent.
	 *
	 * @return bool True if persistent, False if fire and forget.
	 */
	public function is_persistent() {
		$id = $this->get_id();

		return ! empty( $id );
	}

	/**
	 * Check if the notification is relevant for the current user.
	 *
	 * @return bool True if a user needs to see this notification, false if not.
	 */
	public function display_for_current_user() {
		// If the notification is for the current page only, always show.
		if ( ! $this->is_persistent() ) {
			return true;
		}

		// If the current user doesn't match capabilities.
		return $this->match_capabilities();
	}

	/**
	 * Does the current user match required capabilities.
	 *
	 * @return bool
	 */
	public function match_capabilities() {
		// Super Admin can do anything.
		if ( is_multisite() && is_super_admin( $this->options['user_id'] ) ) {
			return true;
		}

		/**
		 * Filter capabilities that enable the displaying of this notification.
		 *
		 * @param array              $capabilities The capabilities that must be present for this notification.
		 * @param Yoast_Notification $notification The notification object.
		 *
		 * @return array Array of capabilities or empty for no restrictions.
		 *
		 * @since 3.2
		 */
		$capabilities = apply_filters( 'wpseo_notification_capabilities', $this->options['capabilities'], $this );

		// Should be an array.
		if ( ! is_array( $capabilities ) ) {
			$capabilities = (array) $capabilities;
		}

		/**
		 * Filter capability check to enable all or any capabilities.
		 *
		 * @param string             $capability_check The type of check that will be used to determine if an capability is present.
		 * @param Yoast_Notification $notification     The notification object.
		 *
		 * @return string self::MATCH_ALL or self::MATCH_ANY.
		 *
		 * @since 3.2
		 */
		$capability_check = apply_filters( 'wpseo_notification_capability_check', $this->options['capability_check'], $this );

		if ( ! in_array( $capability_check, [ self::MATCH_ALL, self::MATCH_ANY ], true ) ) {
			$capability_check = self::MATCH_ALL;
		}

		if ( ! empty( $capabilities ) ) {

			$has_capabilities = array_filter( $capabilities, [ $this, 'has_capability' ] );

			switch ( $capability_check ) {
				case self::MATCH_ALL:
					return $has_capabilities === $capabilities;
				case self::MATCH_ANY:
					return ! empty( $has_capabilities );
			}
		}

		return true;
	}

	/**
	 * Array filter function to find matched capabilities.
	 *
	 * @param string $capability Capability to test.
	 *
	 * @return bool
	 */
	private function has_capability( $capability ) {
		$user_id = $this->options['user_id'];
		if ( ! is_numeric( $user_id ) ) {
			return false;
		}
		$user = get_user_by( 'id', $user_id );
		if ( ! $user ) {
			return false;
		}

		return $user->has_cap( $capability );
	}

	/**
	 * Return the object properties as an array.
	 *
	 * @return array
	 */
	public function to_array() {
		return [
			'message' => $this->message,
			'options' => $this->options,
		];
	}

	/**
	 * Adds string (view) behaviour to the notification.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->render();
	}

	/**
	 * Renders the notification as a string.
	 *
	 * @return string The rendered notification.
	 */
	public function render() {
		$attributes = [];

		// Default notification classes.
		$classes = [
			'yoast-notification',
		];

		// Maintain WordPress visualisation of notifications when they are not persistent.
		if ( ! $this->is_persistent() ) {
			$classes[] = 'notice';
			$classes[] = $this->get_type();
		}

		if ( ! empty( $classes ) ) {
			$attributes['class'] = implode( ' ', $classes );
		}

		// Combined attribute key and value into a string.
		array_walk( $attributes, [ $this, 'parse_attributes' ] );

		$message = null;
		if ( $this->options['yoast_branding'] ) {
			$message = $this->wrap_yoast_seo_icon( $this->message );
		}

		if ( $message === null ) {
			$message = wpautop( $this->message );
		}

		// Build the output DIV.
		return '<div ' . implode( ' ', $attributes ) . '>' . $message . '</div>' . PHP_EOL;
	}

	/**
	 * Get the message for the notification.
	 *
	 * @return string The message.
	 */
	public function get_message() {
		return wpautop( $this->message );
	}

	/**
	 * Wraps the message with a Yoast SEO icon.
	 *
	 * @param string $message The message to wrap.
	 *
	 * @return string The wrapped message.
	 */
	private function wrap_yoast_seo_icon( $message ) {
		$out  = sprintf(
			'<img src="%1$s" height="%2$d" width="%3$d" class="yoast-seo-icon" />',
			esc_url( plugin_dir_url( WPSEO_FILE ) . 'packages/js/images/Yoast_SEO_Icon.svg' ),
			60,
			60
		);
		$out .= '<div class="yoast-seo-icon-wrap">';
		$out .= $message;
		$out .= '</div>';

		return $out;
	}

	/**
	 * Get the JSON if provided.
	 *
	 * @return string|false
	 */
	public function get_json() {
		if ( empty( $this->options['data_json'] ) ) {
			return '';
		}

		return WPSEO_Utils::format_json_encode( $this->options['data_json'] );
	}

	/**
	 * Make sure we only have values that we can work with.
	 *
	 * @param array $options Options to normalize.
	 *
	 * @return array
	 */
	private function normalize_options( $options ) {
		$options = wp_parse_args( $options, $this->defaults );

		// Should not exceed 0 or 1.
		$options['priority'] = min( 1, max( 0, $options['priority'] ) );

		// Set default capabilities when not supplied.
		if ( empty( $options['capabilities'] ) || $options['capabilities'] === [] ) {
			$options['capabilities'] = [ 'wpseo_manage_options' ];
		}

		// Set to the id of the current user if not supplied.
		if ( $options['user_id'] === null ) {
			$options['user_id'] = get_current_user_id();
		}

		return $options;
	}

	/**
	 * Format HTML element attributes.
	 *
	 * @param string $value Attribute value.
	 * @param string $key   Attribute name.
	 *
	 * @return void
	 */
	private function parse_attributes( &$value, $key ) {
		$value = sprintf( '%s="%s"', sanitize_key( $key ), esc_attr( $value ) );
	}
}
