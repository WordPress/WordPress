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
class WP_Widget_Factory {

	/**
	 * Widgets array.
	 *
	 * @since 2.8.0
	 * @var array
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
	 *
	 * @param string|WP_Widget $widget Either the name of a `WP_Widget` subclass or an instance of a `WP_Widget` subclass.
	 */
	public function register( $widget ) {
		if ( $widget instanceof WP_Widget ) {
			$this->widgets[ spl_object_hash( $widget ) ] = $widget;
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
	 *
	 * @param string|WP_Widget $widget Either the name of a `WP_Widget` subclass or an instance of a `WP_Widget` subclass.
	 */
	public function unregister( $widget ) {
		if ( $widget instanceof WP_Widget ) {
			unset( $this->widgets[ spl_object_hash( $widget ) ] );
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
}
