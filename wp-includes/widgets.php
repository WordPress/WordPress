<?php
/**
 * Core Widgets API
 *
 * This API is used for creating dynamic sidebar without hardcoding functionality into
 * themes
 *
 * Includes both internal WordPress routines and theme-use routines.
 *
 * This functionality was found in a plugin before the WordPress 2.2 release, which
 * included it in the core from that point on.
 *
 * @link https://wordpress.org/support/article/wordpress-widgets/
 * @link https://developer.wordpress.org/themes/functionality/widgets/
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 2.2.0
 */

//
// Global Variables.
//

/** @ignore */
global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_updates;

/**
 * Stores the sidebars, since many themes can have more than one.
 *
 * @global array $wp_registered_sidebars Registered sidebars.
 * @since 2.2.0
 */
$wp_registered_sidebars = array();

/**
 * Stores the registered widgets.
 *
 * @global array $wp_registered_widgets
 * @since 2.2.0
 */
$wp_registered_widgets = array();

/**
 * Stores the registered widget controls (options).
 *
 * @global array $wp_registered_widget_controls
 * @since 2.2.0
 */
$wp_registered_widget_controls = array();
/**
 * @global array $wp_registered_widget_updates
 */
$wp_registered_widget_updates = array();

/**
 * Private
 *
 * @global array $_wp_sidebars_widgets
 */
$_wp_sidebars_widgets = array();

/**
 * Private
 *
 * @global array $_wp_deprecated_widgets_callbacks
 */
$GLOBALS['_wp_deprecated_widgets_callbacks'] = array(
	'wp_widget_pages',
	'wp_widget_pages_control',
	'wp_widget_calendar',
	'wp_widget_calendar_control',
	'wp_widget_archives',
	'wp_widget_archives_control',
	'wp_widget_links',
	'wp_widget_meta',
	'wp_widget_meta_control',
	'wp_widget_search',
	'wp_widget_recent_entries',
	'wp_widget_recent_entries_control',
	'wp_widget_tag_cloud',
	'wp_widget_tag_cloud_control',
	'wp_widget_categories',
	'wp_widget_categories_control',
	'wp_widget_text',
	'wp_widget_text_control',
	'wp_widget_rss',
	'wp_widget_rss_control',
	'wp_widget_recent_comments',
	'wp_widget_recent_comments_control',
);

//
// Template tags & API functions.
//

/**
 * Register a widget
 *
 * Registers a WP_Widget widget
 *
 * @since 2.8.0
 * @since 4.6.0 Updated the `$widget` parameter to also accept a WP_Widget instance object
 *              instead of simply a `WP_Widget` subclass name.
 *
 * @see WP_Widget
 *
 * @global WP_Widget_Factory $wp_widget_factory
 *
 * @param string|WP_Widget $widget Either the name of a `WP_Widget` subclass or an instance of a `WP_Widget` subclass.
 */
function register_widget( $widget ) {
	global $wp_widget_factory;

	$wp_widget_factory->register( $widget );
}

/**
 * Unregisters a widget.
 *
 * Unregisters a WP_Widget widget. Useful for un-registering default widgets.
 * Run within a function hooked to the {@see 'widgets_init'} action.
 *
 * @since 2.8.0
 * @since 4.6.0 Updated the `$widget` parameter to also accept a WP_Widget instance object
 *              instead of simply a `WP_Widget` subclass name.
 *
 * @see WP_Widget
 *
 * @global WP_Widget_Factory $wp_widget_factory
 *
 * @param string|WP_Widget $widget Either the name of a `WP_Widget` subclass or an instance of a `WP_Widget` subclass.
 */
function unregister_widget( $widget ) {
	global $wp_widget_factory;

	$wp_widget_factory->unregister( $widget );
}

/**
 * Creates multiple sidebars.
 *
 * If you wanted to quickly create multiple sidebars for a theme or internally.
 * This function will allow you to do so. If you don't pass the 'name' and/or
 * 'id' in `$args`, then they will be built for you.
 *
 * @since 2.2.0
 *
 * @see register_sidebar() The second parameter is documented by register_sidebar() and is the same here.
 *
 * @global array $wp_registered_sidebars The new sidebars are stored in this array by sidebar ID.
 *
 * @param int          $number Optional. Number of sidebars to create. Default 1.
 * @param array|string $args {
 *     Optional. Array or string of arguments for building a sidebar.
 *
 *     @type string $id   The base string of the unique identifier for each sidebar. If provided, and multiple
 *                        sidebars are being defined, the ID will have "-2" appended, and so on.
 *                        Default 'sidebar-' followed by the number the sidebar creation is currently at.
 *     @type string $name The name or title for the sidebars displayed in the admin dashboard. If registering
 *                        more than one sidebar, include '%d' in the string as a placeholder for the uniquely
 *                        assigned number for each sidebar.
 *                        Default 'Sidebar' for the first sidebar, otherwise 'Sidebar %d'.
 * }
 */
function register_sidebars( $number = 1, $args = array() ) {
	global $wp_registered_sidebars;
	$number = (int) $number;

	if ( is_string( $args ) ) {
		parse_str( $args, $args );
	}

	for ( $i = 1; $i <= $number; $i++ ) {
		$_args = $args;

		if ( $number > 1 ) {
			if ( isset( $args['name'] ) ) {
				$_args['name'] = sprintf( $args['name'], $i );
			} else {
				/* translators: %d: Sidebar number. */
				$_args['name'] = sprintf( __( 'Sidebar %d' ), $i );
			}
		} else {
			$_args['name'] = isset( $args['name'] ) ? $args['name'] : __( 'Sidebar' );
		}

		// Custom specified ID's are suffixed if they exist already.
		// Automatically generated sidebar names need to be suffixed regardless starting at -0.
		if ( isset( $args['id'] ) ) {
			$_args['id'] = $args['id'];
			$n           = 2; // Start at -2 for conflicting custom IDs.
			while ( is_registered_sidebar( $_args['id'] ) ) {
				$_args['id'] = $args['id'] . '-' . $n++;
			}
		} else {
			$n = count( $wp_registered_sidebars );
			do {
				$_args['id'] = 'sidebar-' . ++$n;
			} while ( is_registered_sidebar( $_args['id'] ) );
		}
		register_sidebar( $_args );
	}
}

/**
 * Builds the definition for a single sidebar and returns the ID.
 *
 * Accepts either a string or an array and then parses that against a set
 * of default arguments for the new sidebar. WordPress will automatically
 * generate a sidebar ID and name based on the current number of registered
 * sidebars if those arguments are not included.
 *
 * When allowing for automatic generation of the name and ID parameters, keep
 * in mind that the incrementor for your sidebar can change over time depending
 * on what other plugins and themes are installed.
 *
 * If theme support for 'widgets' has not yet been added when this function is
 * called, it will be automatically enabled through the use of add_theme_support()
 *
 * @since 2.2.0
 * @since 5.6.0 Added the `before_sidebar` and `after_sidebar` arguments.
 *
 * @global array $wp_registered_sidebars Registered sidebars.
 *
 * @param array|string $args {
 *     Optional. Array or string of arguments for the sidebar being registered.
 *
 *     @type string $name           The name or title of the sidebar displayed in the Widgets
 *                                  interface. Default 'Sidebar $instance'.
 *     @type string $id             The unique identifier by which the sidebar will be called.
 *                                  Default 'sidebar-$instance'.
 *     @type string $description    Description of the sidebar, displayed in the Widgets interface.
 *                                  Default empty string.
 *     @type string $class          Extra CSS class to assign to the sidebar in the Widgets interface.
 *                                  Default empty.
 *     @type string $before_widget  HTML content to prepend to each widget's HTML output when assigned
 *                                  to this sidebar. Receives the widget's ID attribute as `%1$s`
 *                                  and class name as `%2$s`. Default is an opening list item element.
 *     @type string $after_widget   HTML content to append to each widget's HTML output when assigned
 *                                  to this sidebar. Default is a closing list item element.
 *     @type string $before_title   HTML content to prepend to the sidebar title when displayed.
 *                                  Default is an opening h2 element.
 *     @type string $after_title    HTML content to append to the sidebar title when displayed.
 *                                  Default is a closing h2 element.
 *     @type string $before_sidebar HTML content to prepend to the sidebar when displayed.
 *                                  Receives the `$id` argument as `%1$s` and `$class` as `%2$s`.
 *                                  Outputs after the {@see 'dynamic_sidebar_before'} action.
 *                                  Default empty string.
 *     @type string $after_sidebar  HTML content to append to the sidebar when displayed.
 *                                  Outputs before the {@see 'dynamic_sidebar_after'} action.
 *                                  Default empty string.
 * }
 * @return string Sidebar ID added to $wp_registered_sidebars global.
 */
function register_sidebar( $args = array() ) {
	global $wp_registered_sidebars;

	$i = count( $wp_registered_sidebars ) + 1;

	$id_is_empty = empty( $args['id'] );

	$defaults = array(
		/* translators: %d: Sidebar number. */
		'name'           => sprintf( __( 'Sidebar %d' ), $i ),
		'id'             => "sidebar-$i",
		'description'    => '',
		'class'          => '',
		'before_widget'  => '<li id="%1$s" class="widget %2$s">',
		'after_widget'   => "</li>\n",
		'before_title'   => '<h2 class="widgettitle">',
		'after_title'    => "</h2>\n",
		'before_sidebar' => '',
		'after_sidebar'  => '',
	);

	/**
	 * Filters the sidebar default arguments.
	 *
	 * @since 5.3.0
	 *
	 * @see register_sidebar()
	 *
	 * @param array $defaults The default sidebar arguments.
	 */
	$sidebar = wp_parse_args( $args, apply_filters( 'register_sidebar_defaults', $defaults ) );

	if ( $id_is_empty ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: 1: The 'id' argument, 2: Sidebar name, 3: Recommended 'id' value. */
				__( 'No %1$s was set in the arguments array for the "%2$s" sidebar. Defaulting to "%3$s". Manually set the %1$s to "%3$s" to silence this notice and keep existing sidebar content.' ),
				'<code>id</code>',
				$sidebar['name'],
				$sidebar['id']
			),
			'4.2.0'
		);
	}

	$wp_registered_sidebars[ $sidebar['id'] ] = $sidebar;

	add_theme_support( 'widgets' );

	/**
	 * Fires once a sidebar has been registered.
	 *
	 * @since 3.0.0
	 *
	 * @param array $sidebar Parsed arguments for the registered sidebar.
	 */
	do_action( 'register_sidebar', $sidebar );

	return $sidebar['id'];
}

/**
 * Removes a sidebar from the list.
 *
 * @since 2.2.0
 *
 * @global array $wp_registered_sidebars Registered sidebars.
 *
 * @param string|int $sidebar_id The ID of the sidebar when it was registered.
 */
function unregister_sidebar( $sidebar_id ) {
	global $wp_registered_sidebars;

	unset( $wp_registered_sidebars[ $sidebar_id ] );
}

/**
 * Checks if a sidebar is registered.
 *
 * @since 4.4.0
 *
 * @global array $wp_registered_sidebars Registered sidebars.
 *
 * @param string|int $sidebar_id The ID of the sidebar when it was registered.
 * @return bool True if the sidebar is registered, false otherwise.
 */
function is_registered_sidebar( $sidebar_id ) {
	global $wp_registered_sidebars;

	return isset( $wp_registered_sidebars[ $sidebar_id ] );
}

/**
 * Register an instance of a widget.
 *
 * The default widget option is 'classname' that can be overridden.
 *
 * The function can also be used to un-register widgets when `$output_callback`
 * parameter is an empty string.
 *
 * @since 2.2.0
 * @since 5.3.0 Formalized the existing and already documented `...$params` parameter
 *              by adding it to the function signature.
 * @since 5.8.0 Added show_instance_in_rest option.
 *
 * @global array $wp_registered_widgets            Uses stored registered widgets.
 * @global array $wp_registered_widget_controls    Stores the registered widget controls (options).
 * @global array $wp_registered_widget_updates
 * @global array $_wp_deprecated_widgets_callbacks
 *
 * @param int|string $id              Widget ID.
 * @param string     $name            Widget display title.
 * @param callable   $output_callback Run when widget is called.
 * @param array      $options {
 *     Optional. An array of supplementary widget options for the instance.
 *
 *     @type string $classname             Class name for the widget's HTML container. Default is a shortened
 *                                         version of the output callback name.
 *     @type string $description           Widget description for display in the widget administration
 *                                         panel and/or theme.
 *     @type bool   $show_instance_in_rest Whether to show the widget's instance settings in the REST API.
 *                                         Only available for WP_Widget based widgets.
 * }
 * @param mixed      ...$params       Optional additional parameters to pass to the callback function when it's called.
 */
function wp_register_sidebar_widget( $id, $name, $output_callback, $options = array(), ...$params ) {
	global $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_updates, $_wp_deprecated_widgets_callbacks;

	$id = strtolower( $id );

	if ( empty( $output_callback ) ) {
		unset( $wp_registered_widgets[ $id ] );
		return;
	}

	$id_base = _get_widget_id_base( $id );
	if ( in_array( $output_callback, $_wp_deprecated_widgets_callbacks, true ) && ! is_callable( $output_callback ) ) {
		unset( $wp_registered_widget_controls[ $id ] );
		unset( $wp_registered_widget_updates[ $id_base ] );
		return;
	}

	$defaults = array( 'classname' => $output_callback );
	$options  = wp_parse_args( $options, $defaults );
	$widget   = array(
		'name'     => $name,
		'id'       => $id,
		'callback' => $output_callback,
		'params'   => $params,
	);
	$widget   = array_merge( $widget, $options );

	if ( is_callable( $output_callback ) && ( ! isset( $wp_registered_widgets[ $id ] ) || did_action( 'widgets_init' ) ) ) {

		/**
		 * Fires once for each registered widget.
		 *
		 * @since 3.0.0
		 *
		 * @param array $widget An array of default widget arguments.
		 */
		do_action( 'wp_register_sidebar_widget', $widget );
		$wp_registered_widgets[ $id ] = $widget;
	}
}

/**
 * Retrieve description for widget.
 *
 * When registering widgets, the options can also include 'description' that
 * describes the widget for display on the widget administration panel or
 * in the theme.
 *
 * @since 2.5.0
 *
 * @global array $wp_registered_widgets
 *
 * @param int|string $id Widget ID.
 * @return string|void Widget description, if available.
 */
function wp_widget_description( $id ) {
	if ( ! is_scalar( $id ) ) {
		return;
	}

	global $wp_registered_widgets;

	if ( isset( $wp_registered_widgets[ $id ]['description'] ) ) {
		return esc_html( $wp_registered_widgets[ $id ]['description'] );
	}
}

/**
 * Retrieve description for a sidebar.
 *
 * When registering sidebars a 'description' parameter can be included that
 * describes the sidebar for display on the widget administration panel.
 *
 * @since 2.9.0
 *
 * @global array $wp_registered_sidebars Registered sidebars.
 *
 * @param string $id sidebar ID.
 * @return string|void Sidebar description, if available.
 */
function wp_sidebar_description( $id ) {
	if ( ! is_scalar( $id ) ) {
		return;
	}

	global $wp_registered_sidebars;

	if ( isset( $wp_registered_sidebars[ $id ]['description'] ) ) {
		return wp_kses( $wp_registered_sidebars[ $id ]['description'], 'sidebar_description' );
	}
}

/**
 * Remove widget from sidebar.
 *
 * @since 2.2.0
 *
 * @param int|string $id Widget ID.
 */
function wp_unregister_sidebar_widget( $id ) {

	/**
	 * Fires just before a widget is removed from a sidebar.
	 *
	 * @since 3.0.0
	 *
	 * @param int $id The widget ID.
	 */
	do_action( 'wp_unregister_sidebar_widget', $id );

	wp_register_sidebar_widget( $id, '', '' );
	wp_unregister_widget_control( $id );
}

/**
 * Registers widget control callback for customizing options.
 *
 * @since 2.2.0
 * @since 5.3.0 Formalized the existing and already documented `...$params` parameter
 *              by adding it to the function signature.
 *
 * @global array $wp_registered_widget_controls
 * @global array $wp_registered_widget_updates
 * @global array $wp_registered_widgets
 * @global array $_wp_deprecated_widgets_callbacks
 *
 * @param int|string $id               Sidebar ID.
 * @param string     $name             Sidebar display name.
 * @param callable   $control_callback Run when sidebar is displayed.
 * @param array      $options {
 *     Optional. Array or string of control options. Default empty array.
 *
 *     @type int        $height  Never used. Default 200.
 *     @type int        $width   Width of the fully expanded control form (but try hard to use the default width).
 *                               Default 250.
 *     @type int|string $id_base Required for multi-widgets, i.e widgets that allow multiple instances such as the
 *                               text widget. The widget id will end up looking like `{$id_base}-{$unique_number}`.
 * }
 * @param mixed      ...$params        Optional additional parameters to pass to the callback function when it's called.
 */
function wp_register_widget_control( $id, $name, $control_callback, $options = array(), ...$params ) {
	global $wp_registered_widget_controls, $wp_registered_widget_updates, $wp_registered_widgets, $_wp_deprecated_widgets_callbacks;

	$id      = strtolower( $id );
	$id_base = _get_widget_id_base( $id );

	if ( empty( $control_callback ) ) {
		unset( $wp_registered_widget_controls[ $id ] );
		unset( $wp_registered_widget_updates[ $id_base ] );
		return;
	}

	if ( in_array( $control_callback, $_wp_deprecated_widgets_callbacks, true ) && ! is_callable( $control_callback ) ) {
		unset( $wp_registered_widgets[ $id ] );
		return;
	}

	if ( isset( $wp_registered_widget_controls[ $id ] ) && ! did_action( 'widgets_init' ) ) {
		return;
	}

	$defaults          = array(
		'width'  => 250,
		'height' => 200,
	); // Height is never used.
	$options           = wp_parse_args( $options, $defaults );
	$options['width']  = (int) $options['width'];
	$options['height'] = (int) $options['height'];

	$widget = array(
		'name'     => $name,
		'id'       => $id,
		'callback' => $control_callback,
		'params'   => $params,
	);
	$widget = array_merge( $widget, $options );

	$wp_registered_widget_controls[ $id ] = $widget;

	if ( isset( $wp_registered_widget_updates[ $id_base ] ) ) {
		return;
	}

	if ( isset( $widget['params'][0]['number'] ) ) {
		$widget['params'][0]['number'] = -1;
	}

	unset( $widget['width'], $widget['height'], $widget['name'], $widget['id'] );
	$wp_registered_widget_updates[ $id_base ] = $widget;
}

/**
 * Registers the update callback for a widget.
 *
 * @since 2.8.0
 * @since 5.3.0 Formalized the existing and already documented `...$params` parameter
 *              by adding it to the function signature.
 *
 * @global array $wp_registered_widget_updates
 *
 * @param string   $id_base         The base ID of a widget created by extending WP_Widget.
 * @param callable $update_callback Update callback method for the widget.
 * @param array    $options         Optional. Widget control options. See wp_register_widget_control().
 *                                  Default empty array.
 * @param mixed    ...$params       Optional additional parameters to pass to the callback function when it's called.
 */
function _register_widget_update_callback( $id_base, $update_callback, $options = array(), ...$params ) {
	global $wp_registered_widget_updates;

	if ( isset( $wp_registered_widget_updates[ $id_base ] ) ) {
		if ( empty( $update_callback ) ) {
			unset( $wp_registered_widget_updates[ $id_base ] );
		}
		return;
	}

	$widget = array(
		'callback' => $update_callback,
		'params'   => $params,
	);

	$widget                                   = array_merge( $widget, $options );
	$wp_registered_widget_updates[ $id_base ] = $widget;
}

/**
 * Registers the form callback for a widget.
 *
 * @since 2.8.0
 * @since 5.3.0 Formalized the existing and already documented `...$params` parameter
 *              by adding it to the function signature.
 *
 * @global array $wp_registered_widget_controls
 *
 * @param int|string $id            Widget ID.
 * @param string     $name          Name attribute for the widget.
 * @param callable   $form_callback Form callback.
 * @param array      $options       Optional. Widget control options. See wp_register_widget_control().
 *                                  Default empty array.
 * @param mixed      ...$params     Optional additional parameters to pass to the callback function when it's called.
 */

function _register_widget_form_callback( $id, $name, $form_callback, $options = array(), ...$params ) {
	global $wp_registered_widget_controls;

	$id = strtolower( $id );

	if ( empty( $form_callback ) ) {
		unset( $wp_registered_widget_controls[ $id ] );
		return;
	}

	if ( isset( $wp_registered_widget_controls[ $id ] ) && ! did_action( 'widgets_init' ) ) {
		return;
	}

	$defaults          = array(
		'width'  => 250,
		'height' => 200,
	);
	$options           = wp_parse_args( $options, $defaults );
	$options['width']  = (int) $options['width'];
	$options['height'] = (int) $options['height'];

	$widget = array(
		'name'     => $name,
		'id'       => $id,
		'callback' => $form_callback,
		'params'   => $params,
	);
	$widget = array_merge( $widget, $options );

	$wp_registered_widget_controls[ $id ] = $widget;
}

/**
 * Remove control callback for widget.
 *
 * @since 2.2.0
 *
 * @param int|string $id Widget ID.
 */
function wp_unregister_widget_control( $id ) {
	wp_register_widget_control( $id, '', '' );
}

/**
 * Display dynamic sidebar.
 *
 * By default this displays the default sidebar or 'sidebar-1'. If your theme specifies the 'id' or
 * 'name' parameter for its registered sidebars you can pass an ID or name as the $index parameter.
 * Otherwise, you can pass in a numerical index to display the sidebar at that index.
 *
 * @since 2.2.0
 *
 * @global array $wp_registered_sidebars Registered sidebars.
 * @global array $wp_registered_widgets  Registered widgets.
 *
 * @param int|string $index Optional. Index, name or ID of dynamic sidebar. Default 1.
 * @return bool True, if widget sidebar was found and called. False if not found or not called.
 */
function dynamic_sidebar( $index = 1 ) {
	global $wp_registered_sidebars, $wp_registered_widgets;

	if ( is_int( $index ) ) {
		$index = "sidebar-$index";
	} else {
		$index = sanitize_title( $index );
		foreach ( (array) $wp_registered_sidebars as $key => $value ) {
			if ( sanitize_title( $value['name'] ) === $index ) {
				$index = $key;
				break;
			}
		}
	}

	$sidebars_widgets = wp_get_sidebars_widgets();
	if ( empty( $wp_registered_sidebars[ $index ] ) || empty( $sidebars_widgets[ $index ] ) || ! is_array( $sidebars_widgets[ $index ] ) ) {
		/** This action is documented in wp-includes/widget.php */
		do_action( 'dynamic_sidebar_before', $index, false );
		/** This action is documented in wp-includes/widget.php */
		do_action( 'dynamic_sidebar_after', $index, false );
		/** This filter is documented in wp-includes/widget.php */
		return apply_filters( 'dynamic_sidebar_has_widgets', false, $index );
	}

	$sidebar = $wp_registered_sidebars[ $index ];

	$sidebar['before_sidebar'] = sprintf( $sidebar['before_sidebar'], $sidebar['id'], $sidebar['class'] );

	/**
	 * Fires before widgets are rendered in a dynamic sidebar.
	 *
	 * Note: The action also fires for empty sidebars, and on both the front end
	 * and back end, including the Inactive Widgets sidebar on the Widgets screen.
	 *
	 * @since 3.9.0
	 *
	 * @param int|string $index       Index, name, or ID of the dynamic sidebar.
	 * @param bool       $has_widgets Whether the sidebar is populated with widgets.
	 *                                Default true.
	 */
	do_action( 'dynamic_sidebar_before', $index, true );

	if ( ! is_admin() && ! empty( $sidebar['before_sidebar'] ) ) {
		echo $sidebar['before_sidebar'];
	}

	$did_one = false;
	foreach ( (array) $sidebars_widgets[ $index ] as $id ) {

		if ( ! isset( $wp_registered_widgets[ $id ] ) ) {
			continue;
		}

		$params = array_merge(
			array(
				array_merge(
					$sidebar,
					array(
						'widget_id'   => $id,
						'widget_name' => $wp_registered_widgets[ $id ]['name'],
					)
				),
			),
			(array) $wp_registered_widgets[ $id ]['params']
		);

		// Substitute HTML `id` and `class` attributes into `before_widget`.
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[ $id ]['classname'] as $cn ) {
			if ( is_string( $cn ) ) {
				$classname_ .= '_' . $cn;
			} elseif ( is_object( $cn ) ) {
				$classname_ .= '_' . get_class( $cn );
			}
		}
		$classname_ = ltrim( $classname_, '_' );

		$params[0]['before_widget'] = sprintf(
			$params[0]['before_widget'],
			str_replace( '\\', '_', $id ),
			$classname_
		);

		/**
		 * Filters the parameters passed to a widget's display callback.
		 *
		 * Note: The filter is evaluated on both the front end and back end,
		 * including for the Inactive Widgets sidebar on the Widgets screen.
		 *
		 * @since 2.5.0
		 *
		 * @see register_sidebar()
		 *
		 * @param array $params {
		 *     @type array $args  {
		 *         An array of widget display arguments.
		 *
		 *         @type string $name          Name of the sidebar the widget is assigned to.
		 *         @type string $id            ID of the sidebar the widget is assigned to.
		 *         @type string $description   The sidebar description.
		 *         @type string $class         CSS class applied to the sidebar container.
		 *         @type string $before_widget HTML markup to prepend to each widget in the sidebar.
		 *         @type string $after_widget  HTML markup to append to each widget in the sidebar.
		 *         @type string $before_title  HTML markup to prepend to the widget title when displayed.
		 *         @type string $after_title   HTML markup to append to the widget title when displayed.
		 *         @type string $widget_id     ID of the widget.
		 *         @type string $widget_name   Name of the widget.
		 *     }
		 *     @type array $widget_args {
		 *         An array of multi-widget arguments.
		 *
		 *         @type int $number Number increment used for multiples of the same widget.
		 *     }
		 * }
		 */
		$params = apply_filters( 'dynamic_sidebar_params', $params );

		$callback = $wp_registered_widgets[ $id ]['callback'];

		/**
		 * Fires before a widget's display callback is called.
		 *
		 * Note: The action fires on both the front end and back end, including
		 * for widgets in the Inactive Widgets sidebar on the Widgets screen.
		 *
		 * The action is not fired for empty sidebars.
		 *
		 * @since 3.0.0
		 *
		 * @param array $widget {
		 *     An associative array of widget arguments.
		 *
		 *     @type string   $name        Name of the widget.
		 *     @type string   $id          Widget ID.
		 *     @type callable $callback    When the hook is fired on the front end, `$callback` is an array
		 *                                 containing the widget object. Fired on the back end, `$callback`
		 *                                 is 'wp_widget_control', see `$_callback`.
		 *     @type array    $params      An associative array of multi-widget arguments.
		 *     @type string   $classname   CSS class applied to the widget container.
		 *     @type string   $description The widget description.
		 *     @type array    $_callback   When the hook is fired on the back end, `$_callback` is populated
		 *                                 with an array containing the widget object, see `$callback`.
		 * }
		 */
		do_action( 'dynamic_sidebar', $wp_registered_widgets[ $id ] );

		if ( is_callable( $callback ) ) {
			call_user_func_array( $callback, $params );
			$did_one = true;
		}
	}

	if ( ! is_admin() && ! empty( $sidebar['after_sidebar'] ) ) {
		echo $sidebar['after_sidebar'];
	}

	/**
	 * Fires after widgets are rendered in a dynamic sidebar.
	 *
	 * Note: The action also fires for empty sidebars, and on both the front end
	 * and back end, including the Inactive Widgets sidebar on the Widgets screen.
	 *
	 * @since 3.9.0
	 *
	 * @param int|string $index       Index, name, or ID of the dynamic sidebar.
	 * @param bool       $has_widgets Whether the sidebar is populated with widgets.
	 *                                Default true.
	 */
	do_action( 'dynamic_sidebar_after', $index, true );

	/**
	 * Filters whether a sidebar has widgets.
	 *
	 * Note: The filter is also evaluated for empty sidebars, and on both the front end
	 * and back end, including the Inactive Widgets sidebar on the Widgets screen.
	 *
	 * @since 3.9.0
	 *
	 * @param bool       $did_one Whether at least one widget was rendered in the sidebar.
	 *                            Default false.
	 * @param int|string $index   Index, name, or ID of the dynamic sidebar.
	 */
	return apply_filters( 'dynamic_sidebar_has_widgets', $did_one, $index );
}

/**
 * Determines whether a given widget is displayed on the front end.
 *
 * Either $callback or $id_base can be used
 * $id_base is the first argument when extending WP_Widget class
 * Without the optional $widget_id parameter, returns the ID of the first sidebar
 * in which the first instance of the widget with the given callback or $id_base is found.
 * With the $widget_id parameter, returns the ID of the sidebar where
 * the widget with that callback/$id_base AND that ID is found.
 *
 * NOTE: $widget_id and $id_base are the same for single widgets. To be effective
 * this function has to run after widgets have initialized, at action {@see 'init'} or later.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.2.0
 *
 * @global array $wp_registered_widgets
 *
 * @param callable|false $callback      Optional. Widget callback to check. Default false.
 * @param string|false   $widget_id     Optional. Widget ID. Optional, but needed for checking.
 *                                      Default false.
 * @param string|false   $id_base       Optional. The base ID of a widget created by extending WP_Widget.
 *                                      Default false.
 * @param bool           $skip_inactive Optional. Whether to check in 'wp_inactive_widgets'.
 *                                      Default true.
 * @return string|false ID of the sidebar in which the widget is active,
 *                      false if the widget is not active.
 */
function is_active_widget( $callback = false, $widget_id = false, $id_base = false, $skip_inactive = true ) {
	global $wp_registered_widgets;

	$sidebars_widgets = wp_get_sidebars_widgets();

	if ( is_array( $sidebars_widgets ) ) {
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( $skip_inactive && ( 'wp_inactive_widgets' === $sidebar || 'orphaned_widgets' === substr( $sidebar, 0, 16 ) ) ) {
				continue;
			}

			if ( is_array( $widgets ) ) {
				foreach ( $widgets as $widget ) {
					if ( ( $callback && isset( $wp_registered_widgets[ $widget ]['callback'] ) && $wp_registered_widgets[ $widget ]['callback'] === $callback ) || ( $id_base && _get_widget_id_base( $widget ) === $id_base ) ) {
						if ( ! $widget_id || $widget_id === $wp_registered_widgets[ $widget ]['id'] ) {
							return $sidebar;
						}
					}
				}
			}
		}
	}
	return false;
}

/**
 * Determines whether the dynamic sidebar is enabled and used by the theme.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.2.0
 *
 * @global array $wp_registered_widgets  Registered widgets.
 * @global array $wp_registered_sidebars Registered sidebars.
 *
 * @return bool True if using widgets, false otherwise.
 */
function is_dynamic_sidebar() {
	global $wp_registered_widgets, $wp_registered_sidebars;

	$sidebars_widgets = get_option( 'sidebars_widgets' );

	foreach ( (array) $wp_registered_sidebars as $index => $sidebar ) {
		if ( ! empty( $sidebars_widgets[ $index ] ) ) {
			foreach ( (array) $sidebars_widgets[ $index ] as $widget ) {
				if ( array_key_exists( $widget, $wp_registered_widgets ) ) {
					return true;
				}
			}
		}
	}

	return false;
}

/**
 * Determines whether a sidebar contains widgets.
 *
 * For more information on this and similar theme functions, check out
 * the {@link https://developer.wordpress.org/themes/basics/conditional-tags/
 * Conditional Tags} article in the Theme Developer Handbook.
 *
 * @since 2.8.0
 *
 * @param string|int $index Sidebar name, id or number to check.
 * @return bool True if the sidebar has widgets, false otherwise.
 */
function is_active_sidebar( $index ) {
	$index             = ( is_int( $index ) ) ? "sidebar-$index" : sanitize_title( $index );
	$sidebars_widgets  = wp_get_sidebars_widgets();
	$is_active_sidebar = ! empty( $sidebars_widgets[ $index ] );

	/**
	 * Filters whether a dynamic sidebar is considered "active".
	 *
	 * @since 3.9.0
	 *
	 * @param bool       $is_active_sidebar Whether or not the sidebar should be considered "active".
	 *                                      In other words, whether the sidebar contains any widgets.
	 * @param int|string $index             Index, name, or ID of the dynamic sidebar.
	 */
	return apply_filters( 'is_active_sidebar', $is_active_sidebar, $index );
}

//
// Internal Functions.
//

/**
 * Retrieve full list of sidebars and their widget instance IDs.
 *
 * Will upgrade sidebar widget list, if needed. Will also save updated list, if
 * needed.
 *
 * @since 2.2.0
 * @access private
 *
 * @global array $_wp_sidebars_widgets
 * @global array $sidebars_widgets
 *
 * @param bool $deprecated Not used (argument deprecated).
 * @return array Upgraded list of widgets to version 3 array format when called from the admin.
 */
function wp_get_sidebars_widgets( $deprecated = true ) {
	if ( true !== $deprecated ) {
		_deprecated_argument( __FUNCTION__, '2.8.1' );
	}

	global $_wp_sidebars_widgets, $sidebars_widgets;

	// If loading from front page, consult $_wp_sidebars_widgets rather than options
	// to see if wp_convert_widget_settings() has made manipulations in memory.
	if ( ! is_admin() ) {
		if ( empty( $_wp_sidebars_widgets ) ) {
			$_wp_sidebars_widgets = get_option( 'sidebars_widgets', array() );
		}

		$sidebars_widgets = $_wp_sidebars_widgets;
	} else {
		$sidebars_widgets = get_option( 'sidebars_widgets', array() );
	}

	if ( is_array( $sidebars_widgets ) && isset( $sidebars_widgets['array_version'] ) ) {
		unset( $sidebars_widgets['array_version'] );
	}

	/**
	 * Filters the list of sidebars and their widgets.
	 *
	 * @since 2.7.0
	 *
	 * @param array $sidebars_widgets An associative array of sidebars and their widgets.
	 */
	return apply_filters( 'sidebars_widgets', $sidebars_widgets );
}

/**
 * Set the sidebar widget option to update sidebars.
 *
 * @since 2.2.0
 * @access private
 *
 * @global array $_wp_sidebars_widgets
 * @param array $sidebars_widgets Sidebar widgets and their settings.
 */
function wp_set_sidebars_widgets( $sidebars_widgets ) {
	global $_wp_sidebars_widgets;

	// Clear cached value used in wp_get_sidebars_widgets().
	$_wp_sidebars_widgets = null;

	if ( ! isset( $sidebars_widgets['array_version'] ) ) {
		$sidebars_widgets['array_version'] = 3;
	}

	update_option( 'sidebars_widgets', $sidebars_widgets );
}

/**
 * Retrieve default registered sidebars list.
 *
 * @since 2.2.0
 * @access private
 *
 * @global array $wp_registered_sidebars Registered sidebars.
 *
 * @return array
 */
function wp_get_widget_defaults() {
	global $wp_registered_sidebars;

	$defaults = array();

	foreach ( (array) $wp_registered_sidebars as $index => $sidebar ) {
		$defaults[ $index ] = array();
	}

	return $defaults;
}

/**
 * Converts the widget settings from single to multi-widget format.
 *
 * @since 2.8.0
 *
 * @global array $_wp_sidebars_widgets
 *
 * @param string $base_name   Root ID for all widgets of this type.
 * @param string $option_name Option name for this widget type.
 * @param array  $settings    The array of widget instance settings.
 * @return array The array of widget settings converted to multi-widget format.
 */
function wp_convert_widget_settings( $base_name, $option_name, $settings ) {
	// This test may need expanding.
	$single  = false;
	$changed = false;

	if ( empty( $settings ) ) {
		$single = true;
	} else {
		foreach ( array_keys( $settings ) as $number ) {
			if ( 'number' === $number ) {
				continue;
			}
			if ( ! is_numeric( $number ) ) {
				$single = true;
				break;
			}
		}
	}

	if ( $single ) {
		$settings = array( 2 => $settings );

		// If loading from the front page, update sidebar in memory but don't save to options.
		if ( is_admin() ) {
			$sidebars_widgets = get_option( 'sidebars_widgets' );
		} else {
			if ( empty( $GLOBALS['_wp_sidebars_widgets'] ) ) {
				$GLOBALS['_wp_sidebars_widgets'] = get_option( 'sidebars_widgets', array() );
			}
			$sidebars_widgets = &$GLOBALS['_wp_sidebars_widgets'];
		}

		foreach ( (array) $sidebars_widgets as $index => $sidebar ) {
			if ( is_array( $sidebar ) ) {
				foreach ( $sidebar as $i => $name ) {
					if ( $base_name === $name ) {
						$sidebars_widgets[ $index ][ $i ] = "$name-2";
						$changed                          = true;
						break 2;
					}
				}
			}
		}

		if ( is_admin() && $changed ) {
			update_option( 'sidebars_widgets', $sidebars_widgets );
		}
	}

	$settings['_multiwidget'] = 1;
	if ( is_admin() ) {
		update_option( $option_name, $settings );
	}

	return $settings;
}

/**
 * Output an arbitrary widget as a template tag.
 *
 * @since 2.8.0
 *
 * @global WP_Widget_Factory $wp_widget_factory
 *
 * @param string $widget   The widget's PHP class name (see class-wp-widget.php).
 * @param array  $instance Optional. The widget's instance settings. Default empty array.
 * @param array  $args {
 *     Optional. Array of arguments to configure the display of the widget.
 *
 *     @type string $before_widget HTML content that will be prepended to the widget's HTML output.
 *                                 Default `<div class="widget %s">`, where `%s` is the widget's class name.
 *     @type string $after_widget  HTML content that will be appended to the widget's HTML output.
 *                                 Default `</div>`.
 *     @type string $before_title  HTML content that will be prepended to the widget's title when displayed.
 *                                 Default `<h2 class="widgettitle">`.
 *     @type string $after_title   HTML content that will be appended to the widget's title when displayed.
 *                                 Default `</h2>`.
 * }
 */
function the_widget( $widget, $instance = array(), $args = array() ) {
	global $wp_widget_factory;

	if ( ! isset( $wp_widget_factory->widgets[ $widget ] ) ) {
		_doing_it_wrong(
			__FUNCTION__,
			sprintf(
				/* translators: %s: register_widget() */
				__( 'Widgets need to be registered using %s, before they can be displayed.' ),
				'<code>register_widget()</code>'
			),
			'4.9.0'
		);
		return;
	}

	$widget_obj = $wp_widget_factory->widgets[ $widget ];
	if ( ! ( $widget_obj instanceof WP_Widget ) ) {
		return;
	}

	$default_args          = array(
		'before_widget' => '<div class="widget %s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widgettitle">',
		'after_title'   => '</h2>',
	);
	$args                  = wp_parse_args( $args, $default_args );
	$args['before_widget'] = sprintf( $args['before_widget'], $widget_obj->widget_options['classname'] );

	$instance = wp_parse_args( $instance );

	/** This filter is documented in wp-includes/class-wp-widget.php */
	$instance = apply_filters( 'widget_display_callback', $instance, $widget_obj, $args );

	if ( false === $instance ) {
		return;
	}

	/**
	 * Fires before rendering the requested widget.
	 *
	 * @since 3.0.0
	 *
	 * @param string $widget   The widget's class name.
	 * @param array  $instance The current widget instance's settings.
	 * @param array  $args     An array of the widget's sidebar arguments.
	 */
	do_action( 'the_widget', $widget, $instance, $args );

	$widget_obj->_set( -1 );
	$widget_obj->widget( $args, $instance );
}

/**
 * Retrieves the widget ID base value.
 *
 * @since 2.8.0
 *
 * @param string $id Widget ID.
 * @return string Widget ID base.
 */
function _get_widget_id_base( $id ) {
	return preg_replace( '/-[0-9]+$/', '', $id );
}

/**
 * Handle sidebars config after theme change
 *
 * @access private
 * @since 3.3.0
 *
 * @global array $sidebars_widgets
 */
function _wp_sidebars_changed() {
	global $sidebars_widgets;

	if ( ! is_array( $sidebars_widgets ) ) {
		$sidebars_widgets = wp_get_sidebars_widgets();
	}

	retrieve_widgets( true );
}

/**
 * Validates and remaps any "orphaned" widgets to wp_inactive_widgets sidebar,
 * and saves the widget settings. This has to run at least on each theme change.
 *
 * For example, let's say theme A has a "footer" sidebar, and theme B doesn't have one.
 * After switching from theme A to theme B, all the widgets previously assigned
 * to the footer would be inaccessible. This function detects this scenario, and
 * moves all the widgets previously assigned to the footer under wp_inactive_widgets.
 *
 * Despite the word "retrieve" in the name, this function actually updates the database
 * and the global `$sidebars_widgets`. For that reason it should not be run on front end,
 * unless the `$theme_changed` value is 'customize' (to bypass the database write).
 *
 * @since 2.8.0
 *
 * @global array $wp_registered_sidebars Registered sidebars.
 * @global array $sidebars_widgets
 * @global array $wp_registered_widgets  Registered widgets.
 *
 * @param string|bool $theme_changed Whether the theme was changed as a boolean. A value
 *                                   of 'customize' defers updates for the Customizer.
 * @return array Updated sidebars widgets.
 */
function retrieve_widgets( $theme_changed = false ) {
	global $wp_registered_sidebars, $sidebars_widgets, $wp_registered_widgets;

	$registered_sidebars_keys = array_keys( $wp_registered_sidebars );
	$registered_widgets_ids   = array_keys( $wp_registered_widgets );

	if ( ! is_array( get_theme_mod( 'sidebars_widgets' ) ) ) {
		if ( empty( $sidebars_widgets ) ) {
			return array();
		}

		unset( $sidebars_widgets['array_version'] );

		$sidebars_widgets_keys = array_keys( $sidebars_widgets );
		sort( $sidebars_widgets_keys );
		sort( $registered_sidebars_keys );

		if ( $sidebars_widgets_keys === $registered_sidebars_keys ) {
			$sidebars_widgets = _wp_remove_unregistered_widgets( $sidebars_widgets, $registered_widgets_ids );

			return $sidebars_widgets;
		}
	}

	// Discard invalid, theme-specific widgets from sidebars.
	$sidebars_widgets = _wp_remove_unregistered_widgets( $sidebars_widgets, $registered_widgets_ids );
	$sidebars_widgets = wp_map_sidebars_widgets( $sidebars_widgets );

	// Find hidden/lost multi-widget instances.
	$shown_widgets = array_merge( ...array_values( $sidebars_widgets ) );
	$lost_widgets  = array_diff( $registered_widgets_ids, $shown_widgets );

	foreach ( $lost_widgets as $key => $widget_id ) {
		$number = preg_replace( '/.+?-([0-9]+)$/', '$1', $widget_id );

		// Only keep active and default widgets.
		if ( is_numeric( $number ) && (int) $number < 2 ) {
			unset( $lost_widgets[ $key ] );
		}
	}
	$sidebars_widgets['wp_inactive_widgets'] = array_merge( $lost_widgets, (array) $sidebars_widgets['wp_inactive_widgets'] );

	if ( 'customize' !== $theme_changed ) {
		// Update the widgets settings in the database.
		wp_set_sidebars_widgets( $sidebars_widgets );
	}

	return $sidebars_widgets;
}

/**
 * Compares a list of sidebars with their widgets against an allowed list.
 *
 * @since 4.9.0
 * @since 4.9.2 Always tries to restore widget assignments from previous data, not just if sidebars needed mapping.
 *
 * @param array $existing_sidebars_widgets List of sidebars and their widget instance IDs.
 * @return array Mapped sidebars widgets.
 */
function wp_map_sidebars_widgets( $existing_sidebars_widgets ) {
	global $wp_registered_sidebars;

	$new_sidebars_widgets = array(
		'wp_inactive_widgets' => array(),
	);

	// Short-circuit if there are no sidebars to map.
	if ( ! is_array( $existing_sidebars_widgets ) || empty( $existing_sidebars_widgets ) ) {
		return $new_sidebars_widgets;
	}

	foreach ( $existing_sidebars_widgets as $sidebar => $widgets ) {
		if ( 'wp_inactive_widgets' === $sidebar || 'orphaned_widgets' === substr( $sidebar, 0, 16 ) ) {
			$new_sidebars_widgets['wp_inactive_widgets'] = array_merge( $new_sidebars_widgets['wp_inactive_widgets'], (array) $widgets );
			unset( $existing_sidebars_widgets[ $sidebar ] );
		}
	}

	// If old and new theme have just one sidebar, map it and we're done.
	if ( 1 === count( $existing_sidebars_widgets ) && 1 === count( $wp_registered_sidebars ) ) {
		$new_sidebars_widgets[ key( $wp_registered_sidebars ) ] = array_pop( $existing_sidebars_widgets );

		return $new_sidebars_widgets;
	}

	// Map locations with the same slug.
	$existing_sidebars = array_keys( $existing_sidebars_widgets );

	foreach ( $wp_registered_sidebars as $sidebar => $name ) {
		if ( in_array( $sidebar, $existing_sidebars, true ) ) {
			$new_sidebars_widgets[ $sidebar ] = $existing_sidebars_widgets[ $sidebar ];
			unset( $existing_sidebars_widgets[ $sidebar ] );
		} elseif ( ! array_key_exists( $sidebar, $new_sidebars_widgets ) ) {
			$new_sidebars_widgets[ $sidebar ] = array();
		}
	}

	// If there are more sidebars, try to map them.
	if ( ! empty( $existing_sidebars_widgets ) ) {

		/*
		 * If old and new theme both have sidebars that contain phrases
		 * from within the same group, make an educated guess and map it.
		 */
		$common_slug_groups = array(
			array( 'sidebar', 'primary', 'main', 'right' ),
			array( 'second', 'left' ),
			array( 'sidebar-2', 'footer', 'bottom' ),
			array( 'header', 'top' ),
		);

		// Go through each group...
		foreach ( $common_slug_groups as $slug_group ) {

			// ...and see if any of these slugs...
			foreach ( $slug_group as $slug ) {

				// ...and any of the new sidebars...
				foreach ( $wp_registered_sidebars as $new_sidebar => $args ) {

					// ...actually match!
					if ( false === stripos( $new_sidebar, $slug ) && false === stripos( $slug, $new_sidebar ) ) {
						continue;
					}

					// Then see if any of the existing sidebars...
					foreach ( $existing_sidebars_widgets as $sidebar => $widgets ) {

						// ...and any slug in the same group...
						foreach ( $slug_group as $slug ) {

							// ... have a match as well.
							if ( false === stripos( $sidebar, $slug ) && false === stripos( $slug, $sidebar ) ) {
								continue;
							}

							// Make sure this sidebar wasn't mapped and removed previously.
							if ( ! empty( $existing_sidebars_widgets[ $sidebar ] ) ) {

								// We have a match that can be mapped!
								$new_sidebars_widgets[ $new_sidebar ] = array_merge( $new_sidebars_widgets[ $new_sidebar ], $existing_sidebars_widgets[ $sidebar ] );

								// Remove the mapped sidebar so it can't be mapped again.
								unset( $existing_sidebars_widgets[ $sidebar ] );

								// Go back and check the next new sidebar.
								continue 3;
							}
						} // End foreach ( $slug_group as $slug ).
					} // End foreach ( $existing_sidebars_widgets as $sidebar => $widgets ).
				} // End foreach ( $wp_registered_sidebars as $new_sidebar => $args ).
			} // End foreach ( $slug_group as $slug ).
		} // End foreach ( $common_slug_groups as $slug_group ).
	}

	// Move any left over widgets to inactive sidebar.
	foreach ( $existing_sidebars_widgets as $widgets ) {
		if ( is_array( $widgets ) && ! empty( $widgets ) ) {
			$new_sidebars_widgets['wp_inactive_widgets'] = array_merge( $new_sidebars_widgets['wp_inactive_widgets'], $widgets );
		}
	}

	// Sidebars_widgets settings from when this theme was previously active.
	$old_sidebars_widgets = get_theme_mod( 'sidebars_widgets' );
	$old_sidebars_widgets = isset( $old_sidebars_widgets['data'] ) ? $old_sidebars_widgets['data'] : false;

	if ( is_array( $old_sidebars_widgets ) ) {

		// Remove empty sidebars, no need to map those.
		$old_sidebars_widgets = array_filter( $old_sidebars_widgets );

		// Only check sidebars that are empty or have not been mapped to yet.
		foreach ( $new_sidebars_widgets as $new_sidebar => $new_widgets ) {
			if ( array_key_exists( $new_sidebar, $old_sidebars_widgets ) && ! empty( $new_widgets ) ) {
				unset( $old_sidebars_widgets[ $new_sidebar ] );
			}
		}

		// Remove orphaned widgets, we're only interested in previously active sidebars.
		foreach ( $old_sidebars_widgets as $sidebar => $widgets ) {
			if ( 'orphaned_widgets' === substr( $sidebar, 0, 16 ) ) {
				unset( $old_sidebars_widgets[ $sidebar ] );
			}
		}

		$old_sidebars_widgets = _wp_remove_unregistered_widgets( $old_sidebars_widgets );

		if ( ! empty( $old_sidebars_widgets ) ) {

			// Go through each remaining sidebar...
			foreach ( $old_sidebars_widgets as $old_sidebar => $old_widgets ) {

				// ...and check every new sidebar...
				foreach ( $new_sidebars_widgets as $new_sidebar => $new_widgets ) {

					// ...for every widget we're trying to revive.
					foreach ( $old_widgets as $key => $widget_id ) {
						$active_key = array_search( $widget_id, $new_widgets, true );

						// If the widget is used elsewhere...
						if ( false !== $active_key ) {

							// ...and that elsewhere is inactive widgets...
							if ( 'wp_inactive_widgets' === $new_sidebar ) {

								// ...remove it from there and keep the active version...
								unset( $new_sidebars_widgets['wp_inactive_widgets'][ $active_key ] );
							} else {

								// ...otherwise remove it from the old sidebar and keep it in the new one.
								unset( $old_sidebars_widgets[ $old_sidebar ][ $key ] );
							}
						} // End if ( $active_key ).
					} // End foreach ( $old_widgets as $key => $widget_id ).
				} // End foreach ( $new_sidebars_widgets as $new_sidebar => $new_widgets ).
			} // End foreach ( $old_sidebars_widgets as $old_sidebar => $old_widgets ).
		} // End if ( ! empty( $old_sidebars_widgets ) ).

		// Restore widget settings from when theme was previously active.
		$new_sidebars_widgets = array_merge( $new_sidebars_widgets, $old_sidebars_widgets );
	}

	return $new_sidebars_widgets;
}

/**
 * Compares a list of sidebars with their widgets against an allowed list.
 *
 * @since 4.9.0
 *
 * @param array $sidebars_widgets   List of sidebars and their widget instance IDs.
 * @param array $allowed_widget_ids Optional. List of widget IDs to compare against. Default: Registered widgets.
 * @return array Sidebars with allowed widgets.
 */
function _wp_remove_unregistered_widgets( $sidebars_widgets, $allowed_widget_ids = array() ) {
	if ( empty( $allowed_widget_ids ) ) {
		$allowed_widget_ids = array_keys( $GLOBALS['wp_registered_widgets'] );
	}

	foreach ( $sidebars_widgets as $sidebar => $widgets ) {
		if ( is_array( $widgets ) ) {
			$sidebars_widgets[ $sidebar ] = array_intersect( $widgets, $allowed_widget_ids );
		}
	}

	return $sidebars_widgets;
}

/**
 * Display the RSS entries in a list.
 *
 * @since 2.5.0
 *
 * @param string|array|object $rss  RSS url.
 * @param array               $args Widget arguments.
 */
function wp_widget_rss_output( $rss, $args = array() ) {
	if ( is_string( $rss ) ) {
		$rss = fetch_feed( $rss );
	} elseif ( is_array( $rss ) && isset( $rss['url'] ) ) {
		$args = $rss;
		$rss  = fetch_feed( $rss['url'] );
	} elseif ( ! is_object( $rss ) ) {
		return;
	}

	if ( is_wp_error( $rss ) ) {
		if ( is_admin() || current_user_can( 'manage_options' ) ) {
			echo '<p><strong>' . __( 'RSS Error:' ) . '</strong> ' . $rss->get_error_message() . '</p>';
		}
		return;
	}

	$default_args = array(
		'show_author'  => 0,
		'show_date'    => 0,
		'show_summary' => 0,
		'items'        => 0,
	);
	$args         = wp_parse_args( $args, $default_args );

	$items = (int) $args['items'];
	if ( $items < 1 || 20 < $items ) {
		$items = 10;
	}
	$show_summary = (int) $args['show_summary'];
	$show_author  = (int) $args['show_author'];
	$show_date    = (int) $args['show_date'];

	if ( ! $rss->get_item_quantity() ) {
		echo '<ul><li>' . __( 'An error has occurred, which probably means the feed is down. Try again later.' ) . '</li></ul>';
		$rss->__destruct();
		unset( $rss );
		return;
	}

	echo '<ul>';
	foreach ( $rss->get_items( 0, $items ) as $item ) {
		$link = $item->get_link();
		while ( ! empty( $link ) && stristr( $link, 'http' ) !== $link ) {
			$link = substr( $link, 1 );
		}
		$link = esc_url( strip_tags( $link ) );

		$title = esc_html( trim( strip_tags( $item->get_title() ) ) );
		if ( empty( $title ) ) {
			$title = __( 'Untitled' );
		}

		$desc = html_entity_decode( $item->get_description(), ENT_QUOTES, get_option( 'blog_charset' ) );
		$desc = esc_attr( wp_trim_words( $desc, 55, ' [&hellip;]' ) );

		$summary = '';
		if ( $show_summary ) {
			$summary = $desc;

			// Change existing [...] to [&hellip;].
			if ( '[...]' === substr( $summary, -5 ) ) {
				$summary = substr( $summary, 0, -5 ) . '[&hellip;]';
			}

			$summary = '<div class="rssSummary">' . esc_html( $summary ) . '</div>';
		}

		$date = '';
		if ( $show_date ) {
			$date = $item->get_date( 'U' );

			if ( $date ) {
				$date = ' <span class="rss-date">' . date_i18n( get_option( 'date_format' ), $date ) . '</span>';
			}
		}

		$author = '';
		if ( $show_author ) {
			$author = $item->get_author();
			if ( is_object( $author ) ) {
				$author = $author->get_name();
				$author = ' <cite>' . esc_html( strip_tags( $author ) ) . '</cite>';
			}
		}

		if ( '' === $link ) {
			echo "<li>$title{$date}{$summary}{$author}</li>";
		} elseif ( $show_summary ) {
			echo "<li><a class='rsswidget' href='$link'>$title</a>{$date}{$summary}{$author}</li>";
		} else {
			echo "<li><a class='rsswidget' href='$link'>$title</a>{$date}{$author}</li>";
		}
	}
	echo '</ul>';
	$rss->__destruct();
	unset( $rss );
}

/**
 * Display RSS widget options form.
 *
 * The options for what fields are displayed for the RSS form are all booleans
 * and are as follows: 'url', 'title', 'items', 'show_summary', 'show_author',
 * 'show_date'.
 *
 * @since 2.5.0
 *
 * @param array|string $args   Values for input fields.
 * @param array        $inputs Override default display options.
 */
function wp_widget_rss_form( $args, $inputs = null ) {
	$default_inputs = array(
		'url'          => true,
		'title'        => true,
		'items'        => true,
		'show_summary' => true,
		'show_author'  => true,
		'show_date'    => true,
	);
	$inputs         = wp_parse_args( $inputs, $default_inputs );

	$args['title'] = isset( $args['title'] ) ? $args['title'] : '';
	$args['url']   = isset( $args['url'] ) ? $args['url'] : '';
	$args['items'] = isset( $args['items'] ) ? (int) $args['items'] : 0;

	if ( $args['items'] < 1 || 20 < $args['items'] ) {
		$args['items'] = 10;
	}

	$args['show_summary'] = isset( $args['show_summary'] ) ? (int) $args['show_summary'] : (int) $inputs['show_summary'];
	$args['show_author']  = isset( $args['show_author'] ) ? (int) $args['show_author'] : (int) $inputs['show_author'];
	$args['show_date']    = isset( $args['show_date'] ) ? (int) $args['show_date'] : (int) $inputs['show_date'];

	if ( ! empty( $args['error'] ) ) {
		echo '<p class="widget-error"><strong>' . __( 'RSS Error:' ) . '</strong> ' . $args['error'] . '</p>';
	}

	$esc_number = esc_attr( $args['number'] );
	if ( $inputs['url'] ) :
		?>
	<p><label for="rss-url-<?php echo $esc_number; ?>"><?php _e( 'Enter the RSS feed URL here:' ); ?></label>
	<input class="widefat" id="rss-url-<?php echo $esc_number; ?>" name="widget-rss[<?php echo $esc_number; ?>][url]" type="text" value="<?php echo esc_url( $args['url'] ); ?>" /></p>
<?php endif; if ( $inputs['title'] ) : ?>
	<p><label for="rss-title-<?php echo $esc_number; ?>"><?php _e( 'Give the feed a title (optional):' ); ?></label>
	<input class="widefat" id="rss-title-<?php echo $esc_number; ?>" name="widget-rss[<?php echo $esc_number; ?>][title]" type="text" value="<?php echo esc_attr( $args['title'] ); ?>" /></p>
<?php endif; if ( $inputs['items'] ) : ?>
	<p><label for="rss-items-<?php echo $esc_number; ?>"><?php _e( 'How many items would you like to display?' ); ?></label>
	<select id="rss-items-<?php echo $esc_number; ?>" name="widget-rss[<?php echo $esc_number; ?>][items]">
	<?php
	for ( $i = 1; $i <= 20; ++$i ) {
		echo "<option value='$i' " . selected( $args['items'], $i, false ) . ">$i</option>";
	}
	?>
	</select></p>
<?php endif; if ( $inputs['show_summary'] || $inputs['show_author'] || $inputs['show_date'] ) : ?>
	<p>
	<?php if ( $inputs['show_summary'] ) : ?>
		<input id="rss-show-summary-<?php echo $esc_number; ?>" name="widget-rss[<?php echo $esc_number; ?>][show_summary]" type="checkbox" value="1" <?php checked( $args['show_summary'] ); ?> />
		<label for="rss-show-summary-<?php echo $esc_number; ?>"><?php _e( 'Display item content?' ); ?></label><br />
	<?php endif; if ( $inputs['show_author'] ) : ?>
		<input id="rss-show-author-<?php echo $esc_number; ?>" name="widget-rss[<?php echo $esc_number; ?>][show_author]" type="checkbox" value="1" <?php checked( $args['show_author'] ); ?> />
		<label for="rss-show-author-<?php echo $esc_number; ?>"><?php _e( 'Display item author if available?' ); ?></label><br />
	<?php endif; if ( $inputs['show_date'] ) : ?>
		<input id="rss-show-date-<?php echo $esc_number; ?>" name="widget-rss[<?php echo $esc_number; ?>][show_date]" type="checkbox" value="1" <?php checked( $args['show_date'] ); ?>/>
		<label for="rss-show-date-<?php echo $esc_number; ?>"><?php _e( 'Display item date?' ); ?></label><br />
	<?php endif; ?>
	</p>
	<?php
	endif; // End of display options.
foreach ( array_keys( $default_inputs ) as $input ) :
	if ( 'hidden' === $inputs[ $input ] ) :
		$id = str_replace( '_', '-', $input );
		?>
<input type="hidden" id="rss-<?php echo esc_attr( $id ); ?>-<?php echo $esc_number; ?>" name="widget-rss[<?php echo $esc_number; ?>][<?php echo esc_attr( $input ); ?>]" value="<?php echo esc_attr( $args[ $input ] ); ?>" />
		<?php
	endif;
	endforeach;
}

/**
 * Process RSS feed widget data and optionally retrieve feed items.
 *
 * The feed widget can not have more than 20 items or it will reset back to the
 * default, which is 10.
 *
 * The resulting array has the feed title, feed url, feed link (from channel),
 * feed items, error (if any), and whether to show summary, author, and date.
 * All respectively in the order of the array elements.
 *
 * @since 2.5.0
 *
 * @param array $widget_rss RSS widget feed data. Expects unescaped data.
 * @param bool  $check_feed Optional. Whether to check feed for errors. Default true.
 * @return array
 */
function wp_widget_rss_process( $widget_rss, $check_feed = true ) {
	$items = (int) $widget_rss['items'];
	if ( $items < 1 || 20 < $items ) {
		$items = 10;
	}
	$url          = esc_url_raw( strip_tags( $widget_rss['url'] ) );
	$title        = isset( $widget_rss['title'] ) ? trim( strip_tags( $widget_rss['title'] ) ) : '';
	$show_summary = isset( $widget_rss['show_summary'] ) ? (int) $widget_rss['show_summary'] : 0;
	$show_author  = isset( $widget_rss['show_author'] ) ? (int) $widget_rss['show_author'] : 0;
	$show_date    = isset( $widget_rss['show_date'] ) ? (int) $widget_rss['show_date'] : 0;
	$error        = false;
	$link         = '';

	if ( $check_feed ) {
		$rss = fetch_feed( $url );

		if ( is_wp_error( $rss ) ) {
			$error = $rss->get_error_message();
		} else {
			$link = esc_url( strip_tags( $rss->get_permalink() ) );
			while ( stristr( $link, 'http' ) !== $link ) {
				$link = substr( $link, 1 );
			}

			$rss->__destruct();
			unset( $rss );
		}
	}

	return compact( 'title', 'url', 'link', 'items', 'error', 'show_summary', 'show_author', 'show_date' );
}

/**
 * Registers all of the default WordPress widgets on startup.
 *
 * Calls {@see 'widgets_init'} action after all of the WordPress widgets have been registered.
 *
 * @since 2.2.0
 */
function wp_widgets_init() {
	if ( ! is_blog_installed() ) {
		return;
	}

	register_widget( 'WP_Widget_Pages' );

	register_widget( 'WP_Widget_Calendar' );

	register_widget( 'WP_Widget_Archives' );

	if ( get_option( 'link_manager_enabled' ) ) {
		register_widget( 'WP_Widget_Links' );
	}

	register_widget( 'WP_Widget_Media_Audio' );

	register_widget( 'WP_Widget_Media_Image' );

	register_widget( 'WP_Widget_Media_Gallery' );

	register_widget( 'WP_Widget_Media_Video' );

	register_widget( 'WP_Widget_Meta' );

	register_widget( 'WP_Widget_Search' );

	register_widget( 'WP_Widget_Text' );

	register_widget( 'WP_Widget_Categories' );

	register_widget( 'WP_Widget_Recent_Posts' );

	register_widget( 'WP_Widget_Recent_Comments' );

	register_widget( 'WP_Widget_RSS' );

	register_widget( 'WP_Widget_Tag_Cloud' );

	register_widget( 'WP_Nav_Menu_Widget' );

	register_widget( 'WP_Widget_Custom_HTML' );

	register_widget( 'WP_Widget_Block' );

	/**
	 * Fires after all default WordPress widgets have been registered.
	 *
	 * @since 2.2.0
	 */
	do_action( 'widgets_init' );
}

/**
 * Enables the widgets block editor. This is hooked into 'after_setup_theme' so
 * that the block editor is enabled by default but can be disabled by themes.
 *
 * @since 5.8.0
 *
 * @access private
 */
function wp_setup_widgets_block_editor() {
	add_theme_support( 'widgets-block-editor' );
}

/**
 * Whether or not to use the block editor to manage widgets. Defaults to true
 * unless a theme has removed support for widgets-block-editor or a plugin has
 * filtered the return value of this function.
 *
 * @since 5.8.0
 *
 * @return bool Whether to use the block editor to manage widgets.
 */
function wp_use_widgets_block_editor() {
	/**
	 * Filters whether to use the block editor to manage widgets.
	 *
	 * @since 5.8.0
	 *
	 * @param bool $use_widgets_block_editor Whether to use the block editor to manage widgets.
	 */
	return apply_filters(
		'use_widgets_block_editor',
		get_theme_support( 'widgets-block-editor' )
	);
}

/**
 * Converts a widget ID into its id_base and number components.
 *
 * @since 5.8.0
 *
 * @param string $id Widget ID.
 * @return array Array containing a widget's id_base and number components.
 */
function wp_parse_widget_id( $id ) {
	$parsed = array();

	if ( preg_match( '/^(.+)-(\d+)$/', $id, $matches ) ) {
		$parsed['id_base'] = $matches[1];
		$parsed['number']  = (int) $matches[2];
	} else {
		// Likely an old single widget.
		$parsed['id_base'] = $id;
	}

	return $parsed;
}

/**
 * Finds the sidebar that a given widget belongs to.
 *
 * @since 5.8.0
 *
 * @param string $widget_id The widget id to look for.
 * @return string|null The found sidebar's id, or null if it was not found.
 */
function wp_find_widgets_sidebar( $widget_id ) {
	foreach ( wp_get_sidebars_widgets() as $sidebar_id => $widget_ids ) {
		foreach ( $widget_ids as $maybe_widget_id ) {
			if ( $maybe_widget_id === $widget_id ) {
				return (string) $sidebar_id;
			}
		}
	}

	return null;
}

/**
 * Assigns a widget to the given sidebar.
 *
 * @since 5.8.0
 *
 * @param string $widget_id  The widget id to assign.
 * @param string $sidebar_id The sidebar id to assign to. If empty, the widget won't be added to any sidebar.
 */
function wp_assign_widget_to_sidebar( $widget_id, $sidebar_id ) {
	$sidebars = wp_get_sidebars_widgets();

	foreach ( $sidebars as $maybe_sidebar_id => $widgets ) {
		foreach ( $widgets as $i => $maybe_widget_id ) {
			if ( $widget_id === $maybe_widget_id && $sidebar_id !== $maybe_sidebar_id ) {
				unset( $sidebars[ $maybe_sidebar_id ][ $i ] );
				// We could technically break 2 here, but continue looping in case the id is duplicated.
				continue 2;
			}
		}
	}

	if ( $sidebar_id ) {
		$sidebars[ $sidebar_id ][] = $widget_id;
	}

	wp_set_sidebars_widgets( $sidebars );
}

/**
 * Calls the render callback of a widget and returns the output.
 *
 * @since 5.8.0
 *
 * @param string $widget_id Widget ID.
 * @param string $sidebar_id Sidebar ID.
 * @return string
 */
function wp_render_widget( $widget_id, $sidebar_id ) {
	global $wp_registered_widgets, $wp_registered_sidebars;

	if ( ! isset( $wp_registered_widgets[ $widget_id ] ) ) {
		return '';
	}

	if ( isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
		$sidebar = $wp_registered_sidebars[ $sidebar_id ];
	} elseif ( 'wp_inactive_widgets' === $sidebar_id ) {
		$sidebar = array();
	} else {
		return '';
	}

	$params = array_merge(
		array(
			array_merge(
				$sidebar,
				array(
					'widget_id'   => $widget_id,
					'widget_name' => $wp_registered_widgets[ $widget_id ]['name'],
				)
			),
		),
		(array) $wp_registered_widgets[ $widget_id ]['params']
	);

	// Substitute HTML `id` and `class` attributes into `before_widget`.
	$classname_ = '';
	foreach ( (array) $wp_registered_widgets[ $widget_id ]['classname'] as $cn ) {
		if ( is_string( $cn ) ) {
			$classname_ .= '_' . $cn;
		} elseif ( is_object( $cn ) ) {
			$classname_ .= '_' . get_class( $cn );
		}
	}
	$classname_                 = ltrim( $classname_, '_' );
	$params[0]['before_widget'] = sprintf( $params[0]['before_widget'], $widget_id, $classname_ );

	/** This filter is documented in wp-includes/widgets.php */
	$params = apply_filters( 'dynamic_sidebar_params', $params );

	$callback = $wp_registered_widgets[ $widget_id ]['callback'];

	ob_start();

	/** This filter is documented in wp-includes/widgets.php */
	do_action( 'dynamic_sidebar', $wp_registered_widgets[ $widget_id ] );

	if ( is_callable( $callback ) ) {
		call_user_func_array( $callback, $params );
	}

	return ob_get_clean();
}

/**
 * Calls the control callback of a widget and returns the output.
 *
 * @since 5.8.0
 *
 * @param string $id Widget ID.
 * @return string|null
 */
function wp_render_widget_control( $id ) {
	global $wp_registered_widget_controls;

	if ( ! isset( $wp_registered_widget_controls[ $id ]['callback'] ) ) {
		return null;
	}

	$callback = $wp_registered_widget_controls[ $id ]['callback'];
	$params   = $wp_registered_widget_controls[ $id ]['params'];

	ob_start();

	if ( is_callable( $callback ) ) {
		call_user_func_array( $callback, $params );
	}

	return ob_get_clean();
}

/**
 * Displays a _doing_it_wrong() message for conflicting widget editor scripts.
 *
 * The 'wp-editor' script module is exposed as window.wp.editor. This overrides
 * the legacy TinyMCE editor module which is required by the widgets editor.
 * Because of that conflict, these two shouldn't be enqueued together.
 * See https://core.trac.wordpress.org/ticket/53569.
 *
 * There is also another conflict related to styles where the block widgets
 * editor is hidden if a block enqueues 'wp-edit-post' stylesheet.
 * See https://core.trac.wordpress.org/ticket/53569.
 *
 * @since 5.8.0
 * @access private
 *
 * @global WP_Scripts $wp_scripts
 * @global WP_Styles  $wp_styles
 */
function wp_check_widget_editor_deps() {
	global $wp_scripts, $wp_styles;

	if (
		$wp_scripts->query( 'wp-edit-widgets', 'enqueued' ) ||
		$wp_scripts->query( 'wp-customize-widgets', 'enqueued' )
	) {
		if ( $wp_scripts->query( 'wp-editor', 'enqueued' ) ) {
			_doing_it_wrong(
				'wp_enqueue_script()',
				sprintf(
					/* translators: 1: 'wp-editor', 2: 'wp-edit-widgets', 3: 'wp-customize-widgets'. */
					__( '"%1$s" script should not be enqueued together with the new widgets editor (%2$s or %3$s).' ),
					'wp-editor',
					'wp-edit-widgets',
					'wp-customize-widgets'
				),
				'5.8.0'
			);
		}
		if ( $wp_styles->query( 'wp-edit-post', 'enqueued' ) ) {
			_doing_it_wrong(
				'wp_enqueue_style()',
				sprintf(
					/* translators: 1: 'wp-edit-post', 2: 'wp-edit-widgets', 3: 'wp-customize-widgets'. */
					__( '"%1$s" style should not be enqueued together with the new widgets editor (%2$s or %3$s).' ),
					'wp-edit-post',
					'wp-edit-widgets',
					'wp-customize-widgets'
				),
				'5.8.0'
			);
		}
	}
}
