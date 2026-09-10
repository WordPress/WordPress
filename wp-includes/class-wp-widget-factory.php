<?php
/**
 * Widget API: WP_Widget_Factory class
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 4.4.0
 */

/**
 * Singleton that registers and instantiates WP_Widget classes.
 *
 * @since 2.8.0
 * @since 4.4.0 Moved to its own file from wp-includes/widgets.php
 */
#[AllowDynamicProperties]
class WP_Widget_Factory {

	/**
	 * Prefix for the key under which a widget registered as an instance is stored.
	 *
	 * Without it the key would be the decimal representation of an integer, which PHP casts from
	 * string to int when it is used as an array key.
	 *
	 * @since 7.1.1
	 */
	private const INSTANCE_KEY_PREFIX = 'spl_object_id:';

	/**
	 * Widgets array.
	 *
	 * Keyed by class name for a widget registered by name, and by a prefixed object ID for a widget
	 * registered as an instance.
	 *
	 * @since 2.8.0
	 * @var array<string, WP_Widget>
	 * @phpstan-var array<non-decimal-int-string, WP_Widget>
	 */
	public $widgets = array();

	/**
	 * PHP5 constructor.
	 *
	 * @since 4.3.0
	 */
	public function __construct() {
		add_action( 'widgets_init', array( $this, '_register_widgets' ), 100 );
	}

	/**
	 * PHP4 constructor.
	 *
	 * @since 2.8.0
	 * @deprecated 4.3.0 Use __construct() instead.
	 *
	 * @see WP_Widget_Factory::__construct()
	 */
	public function WP_Widget_Factory() {
		_deprecated_constructor( 'WP_Widget_Factory', '4.3.0' );
		self::__construct();
	}

	/**
	 * Registers a widget subclass.
	 *
	 * @since 2.8.0
	 * @since 4.6.0 Updated the `$widget` parameter to also accept a WP_Widget instance object
	 *              instead of simply a `WP_Widget` subclass name.
	 * @since 7.1.1 The key for an instance is prefixed so that it is never cast to an integer.
	 *
	 * @param string|WP_Widget $widget Either the name of a `WP_Widget` subclass or an instance of a `WP_Widget` subclass.
	 */
	public function register( $widget ) {
		if ( $widget instanceof WP_Widget ) {
			$this->widgets[ self::INSTANCE_KEY_PREFIX . spl_object_id( $widget ) ] = $widget;
		} else {
			$this->widgets[ $widget ] = new $widget();
		}
	}

	/**
	 * Un-registers a widget subclass.
	 *
	 * @since 2.8.0
	 * @since 4.6.0 Updated the `$widget` parameter to also accept a WP_Widget instance object
	 *              instead of simply a `WP_Widget` subclass name.
	 * @since 7.1.1 The key for an instance is prefixed so that it is never cast to an integer.
	 *
	 * @param string|WP_Widget $widget Either the name of a `WP_Widget` subclass or an instance of a `WP_Widget` subclass.
	 */
	public function unregister( $widget ) {
		if ( $widget instanceof WP_Widget ) {
			unset( $this->widgets[ self::INSTANCE_KEY_PREFIX . spl_object_id( $widget ) ] );
		} else {
			unset( $this->widgets[ $widget ] );
		}
	}

	/**
	 * Serves as a utility method for adding widgets to the registered widgets global.
	 *
	 * @since 2.8.0
	 *
	 * @global array $wp_registered_widgets
	 */
	public function _register_widgets() {
		global $wp_registered_widgets;
		$keys       = array_keys( $this->widgets );
		$registered = array_keys( $wp_registered_widgets );
		$registered = array_map( '_get_widget_id_base', $registered );

		foreach ( $keys as $key ) {
			// Don't register new widget if old widget with the same id is already registered.
			if ( in_array( $this->widgets[ $key ]->id_base, $registered, true ) ) {
				unset( $this->widgets[ $key ] );
				continue;
			}

			$this->widgets[ $key ]->_register();
		}
	}

	/**
	 * Returns the registered WP_Widget object for the given widget type.
	 *
	 * @since 5.8.0
	 *
	 * @param string $id_base Widget type ID.
	 * @return WP_Widget|null
	 */
	public function get_widget_object( $id_base ) {
		$key = $this->get_widget_key( $id_base );
		if ( '' === $key ) {
			return null;
		}

		return $this->widgets[ $key ];
	}

	/**
	 * Returns the registered key for the given widget type.
	 *
	 * @since 5.8.0
	 *
	 * @param string $id_base Widget type ID.
	 * @return string
	 */
	public function get_widget_key( $id_base ) {
		foreach ( $this->widgets as $key => $widget_object ) {
			if ( $widget_object->id_base === $id_base ) {
				return $key;
			}
		}

		return '';
	}
}
