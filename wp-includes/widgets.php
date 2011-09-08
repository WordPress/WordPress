<?php
/**
 * API for creating dynamic sidebar without hardcoding functionality into
 * themes. Includes both internal WordPress routines and theme use routines.
 *
 * This functionality was found in a plugin before WordPress 2.2 release which
 * included it in the core from that point on.
 *
 * @link http://codex.wordpress.org/Plugins/WordPress_Widgets WordPress Widgets
 * @link http://codex.wordpress.org/Plugins/WordPress_Widgets_Api Widgets API
 *
 * @package WordPress
 * @subpackage Widgets
 */

/**
 * This class must be extended for each widget and WP_Widget::widget(), WP_Widget::update()
 * and WP_Widget::form() need to be over-ridden.
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 2.8
 */
class WP_Widget {

	var $id_base;			// Root id for all widgets of this type.
	var $name;				// Name for this widget type.
	var $widget_options;	// Option array passed to wp_register_sidebar_widget()
	var $control_options;	// Option array passed to wp_register_widget_control()

	var $number = false;	// Unique ID number of the current instance.
	var $id = false;		// Unique ID string of the current instance (id_base-number)
	var $updated = false;	// Set true when we update the data after a POST submit - makes sure we don't do it twice.

	// Member functions that you must over-ride.

	/** Echo the widget content.
	 *
	 * Subclasses should over-ride this function to generate their widget code.
	 *
	 * @param array $args Display arguments including before_title, after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the widget
	 */
	function widget($args, $instance) {
		die('function WP_Widget::widget() must be over-ridden in a sub-class.');
	}

	/** Update a particular instance.
	 *
	 * This function should check that $new_instance is set correctly.
	 * The newly calculated value of $instance should be returned.
	 * If "false" is returned, the instance won't be saved/updated.
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form()
	 * @param array $old_instance Old settings for this instance
	 * @return array Settings to save or bool false to cancel saving
	 */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** Echo the settings update form
	 *
	 * @param array $instance Current settings
	 */
	function form($instance) {
		echo '<p class="no-options-widget">' . __('There are no options for this widget.') . '</p>';
		return 'noform';
	}

	// Functions you'll need to call.

	/**
	 * PHP4 constructor
	 */
	function WP_Widget( $id_base = false, $name, $widget_options = array(), $control_options = array() ) {
		WP_Widget::__construct( $id_base, $name, $widget_options, $control_options );
	}

	/**
	 * PHP5 constructor
	 *
	 * @param string $id_base Optional Base ID for the widget, lower case,
	 * if left empty a portion of the widget's class name will be used. Has to be unique.
	 * @param string $name Name for the widget displayed on the configuration page.
	 * @param array $widget_options Optional Passed to wp_register_sidebar_widget()
	 *	 - description: shown on the configuration page
	 *	 - classname
	 * @param array $control_options Optional Passed to wp_register_widget_control()
	 *	 - width: required if more than 250px
	 *	 - height: currently not used but may be needed in the future
	 */
	function __construct( $id_base = false, $name, $widget_options = array(), $control_options = array() ) {
		$this->id_base = empty($id_base) ? preg_replace( '/(wp_)?widget_/', '', strtolower(get_class($this)) ) : strtolower($id_base);
		$this->name = $name;
		$this->option_name = 'widget_' . $this->id_base;
		$this->widget_options = wp_parse_args( $widget_options, array('classname' => $this->option_name) );
		$this->control_options = wp_parse_args( $control_options, array('id_base' => $this->id_base) );
	}

	/**
	 * Constructs name attributes for use in form() fields
	 *
	 * This function should be used in form() methods to create name attributes for fields to be saved by update()
	 *
	 * @param string $field_name Field name
	 * @return string Name attribute for $field_name
	 */
	function get_field_name($field_name) {
		return 'widget-' . $this->id_base . '[' . $this->number . '][' . $field_name . ']';
	}

	/**
	 * Constructs id attributes for use in form() fields
	 *
	 * This function should be used in form() methods to create id attributes for fields to be saved by update()
	 *
	 * @param string $field_name Field name
	 * @return string ID attribute for $field_name
	 */
	function get_field_id($field_name) {
		return 'widget-' . $this->id_base . '-' . $this->number . '-' . $field_name;
	}

	// Private Functions. Don't worry about these.

	function _register() {
		$settings = $this->get_settings();
		$empty = true;

		if ( is_array($settings) ) {
			foreach ( array_keys($settings) as $number ) {
				if ( is_numeric($number) ) {
					$this->_set($number);
					$this->_register_one($number);
					$empty = false;
				}
			}
		}

		if ( $empty ) {
			// If there are none, we register the widget's existence with a
			// generic template
			$this->_set(1);
			$this->_register_one();
		}
	}

	function _set($number) {
		$this->number = $number;
		$this->id = $this->id_base . '-' . $number;
	}

	function _get_display_callback() {
		return array(&$this, 'display_callback');
	}

	function _get_update_callback() {
		return array(&$this, 'update_callback');
	}

	function _get_form_callback() {
		return array(&$this, 'form_callback');
	}

	/** Generate the actual widget content.
	 *	Just finds the instance and calls widget().
	 *	Do NOT over-ride this function. */
	function display_callback( $args, $widget_args = 1 ) {
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		$this->_set( $widget_args['number'] );
		$instance = $this->get_settings();

		if ( array_key_exists( $this->number, $instance ) ) {
			$instance = $instance[$this->number];
			// filters the widget's settings, return false to stop displaying the widget
			$instance = apply_filters('widget_display_callback', $instance, $this, $args);
			if ( false !== $instance )
				$this->widget($args, $instance);
		}
	}

	/** Deal with changed settings.
	 *	Do NOT over-ride this function. */
	function update_callback( $widget_args = 1 ) {
		global $wp_registered_widgets;

		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		$all_instances = $this->get_settings();

		// We need to update the data
		if ( $this->updated )
			return;

		$sidebars_widgets = wp_get_sidebars_widgets();

		if ( isset($_POST['delete_widget']) && $_POST['delete_widget'] ) {
			// Delete the settings for this instance of the widget
			if ( isset($_POST['the-widget-id']) )
				$del_id = $_POST['the-widget-id'];
			else
				return;

			if ( isset($wp_registered_widgets[$del_id]['params'][0]['number']) ) {
				$number = $wp_registered_widgets[$del_id]['params'][0]['number'];

				if ( $this->id_base . '-' . $number == $del_id )
					unset($all_instances[$number]);
			}
		} else {
			if ( isset($_POST['widget-' . $this->id_base]) && is_array($_POST['widget-' . $this->id_base]) ) {
				$settings = $_POST['widget-' . $this->id_base];
			} elseif ( isset($_POST['id_base']) && $_POST['id_base'] == $this->id_base ) {
				$num = $_POST['multi_number'] ? (int) $_POST['multi_number'] : (int) $_POST['widget_number'];
				$settings = array( $num => array() );
			} else {
				return;
			}

			foreach ( $settings as $number => $new_instance ) {
				$new_instance = stripslashes_deep($new_instance);
				$this->_set($number);

				$old_instance = isset($all_instances[$number]) ? $all_instances[$number] : array();

				$instance = $this->update($new_instance, $old_instance);

				// filters the widget's settings before saving, return false to cancel saving (keep the old settings if updating)
				$instance = apply_filters('widget_update_callback', $instance, $new_instance, $old_instance, $this);
				if ( false !== $instance )
					$all_instances[$number] = $instance;

				break; // run only once
			}
		}

		$this->save_settings($all_instances);
		$this->updated = true;
	}

	/** Generate the control form.
	 *	Do NOT over-ride this function. */
	function form_callback( $widget_args = 1 ) {
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		$all_instances = $this->get_settings();

		if ( -1 == $widget_args['number'] ) {
			// We echo out a form where 'number' can be set later
			$this->_set('__i__');
			$instance = array();
		} else {
			$this->_set($widget_args['number']);
			$instance = $all_instances[ $widget_args['number'] ];
		}

		// filters the widget admin form before displaying, return false to stop displaying it
		$instance = apply_filters('widget_form_callback', $instance, $this);

		$return = null;
		if ( false !== $instance ) {
			$return = $this->form($instance);
			// add extra fields in the widget form - be sure to set $return to null if you add any
			// if the widget has no form the text echoed from the default form method can be hidden using css
			do_action_ref_array( 'in_widget_form', array(&$this, &$return, $instance) );
		}
		return $return;
	}

	/** Helper function: Registers a single instance. */
	function _register_one($number = -1) {
		wp_register_sidebar_widget(	$this->id, $this->name,	$this->_get_display_callback(), $this->widget_options, array( 'number' => $number ) );
		_register_widget_update_callback( $this->id_base, $this->_get_update_callback(), $this->control_options, array( 'number' => -1 ) );
		_register_widget_form_callback(	$this->id, $this->name,	$this->_get_form_callback(), $this->control_options, array( 'number' => $number ) );
	}

	function save_settings($settings) {
		$settings['_multiwidget'] = 1;
		update_option( $this->option_name, $settings );
	}

	function get_settings() {
		$settings = get_option($this->option_name);

		if ( false === $settings && isset($this->alt_option_name) )
			$settings = get_option($this->alt_option_name);

		if ( !is_array($settings) )
			$settings = array();

		if ( !array_key_exists('_multiwidget', $settings) ) {
			// old format, convert if single widget
			$settings = wp_convert_widget_settings($this->id_base, $this->option_name, $settings);
		}

		unset($settings['_multiwidget'], $settings['__i__']);
		return $settings;
	}
}

/**
 * Singleton that registers and instantiates WP_Widget classes.
 *
 * @package WordPress
 * @subpackage Widgets
 * @since 2.8
 */
class WP_Widget_Factory {
	var $widgets = array();

	function WP_Widget_Factory() {
		add_action( 'widgets_init', array( &$this, '_register_widgets' ), 100 );
	}

	function register($widget_class) {
		$this->widgets[$widget_class] = & new $widget_class();
	}

	function unregister($widget_class) {
		if ( isset($this->widgets[$widget_class]) )
			unset($this->widgets[$widget_class]);
	}

	function _register_widgets() {
		global $wp_registered_widgets;
		$keys = array_keys($this->widgets);
		$registered = array_keys($wp_registered_widgets);
		$registered = array_map('_get_widget_id_base', $registered);

		foreach ( $keys as $key ) {
			// don't register new widget if old widget with the same id is already registered
			if ( in_array($this->widgets[$key]->id_base, $registered, true) ) {
				unset($this->widgets[$key]);
				continue;
			}

			$this->widgets[$key]->_register();
		}
	}
}

/* Global Variables */

/** @ignore */
global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_updates;

/**
 * Stores the sidebars, since many themes can have more than one.
 *
 * @global array $wp_registered_sidebars
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
 * Stores the registered widget control (options).
 *
 * @global array $wp_registered_widget_controls
 * @since 2.2.0
 */
$wp_registered_widget_controls = array();
$wp_registered_widget_updates = array();

/**
 * Private
 */
$_wp_sidebars_widgets = array();

/**
 * Private
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
	'wp_widget_recent_comments_control'
 );

/* Template tags & API functions */

/**
 * Register a widget
 *
 * Registers a WP_Widget widget
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 * @see WP_Widget_Factory
 * @uses WP_Widget_Factory
 *
 * @param string $widget_class The name of a class that extends WP_Widget
 */
function register_widget($widget_class) {
	global $wp_widget_factory;

	$wp_widget_factory->register($widget_class);
}

/**
 * Unregister a widget
 *
 * Unregisters a WP_Widget widget. Useful for unregistering default widgets.
 * Run within a function hooked to the widgets_init action.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 * @see WP_Widget_Factory
 * @uses WP_Widget_Factory
 *
 * @param string $widget_class The name of a class that extends WP_Widget
 */
function unregister_widget($widget_class) {
	global $wp_widget_factory;

	$wp_widget_factory->unregister($widget_class);
}

/**
 * Creates multiple sidebars.
 *
 * If you wanted to quickly create multiple sidebars for a theme or internally.
 * This function will allow you to do so. If you don't pass the 'name' and/or
 * 'id' in $args, then they will be built for you.
 *
 * The default for the name is "Sidebar #", with '#' being replaced with the
 * number the sidebar is currently when greater than one. If first sidebar, the
 * name will be just "Sidebar". The default for id is "sidebar-" followed by the
 * number the sidebar creation is currently at. If the id is provided, and multiple
 * sidebars are being defined, the id will have "-2" appended, and so on.
 *
 * @since 2.2.0
 *
 * @see register_sidebar() The second parameter is documented by register_sidebar() and is the same here.
 * @uses parse_str() Converts a string to an array to be used in the rest of the function.
 * @uses register_sidebar() Sends single sidebar information [name, id] to this
 *	function to handle building the sidebar.
 *
 * @param int $number Number of sidebars to create.
 * @param string|array $args Builds Sidebar based off of 'name' and 'id' values.
 */
function register_sidebars($number = 1, $args = array()) {
	global $wp_registered_sidebars;
	$number = (int) $number;

	if ( is_string($args) )
		parse_str($args, $args);

	for ( $i = 1; $i <= $number; $i++ ) {
		$_args = $args;

		if ( $number > 1 )
			$_args['name'] = isset($args['name']) ? sprintf($args['name'], $i) : sprintf(__('Sidebar %d'), $i);
		else
			$_args['name'] = isset($args['name']) ? $args['name'] : __('Sidebar');

		// Custom specified ID's are suffixed if they exist already.
		// Automatically generated sidebar names need to be suffixed regardless starting at -0
		if ( isset($args['id']) ) {
			$_args['id'] = $args['id'];
			$n = 2; // Start at -2 for conflicting custom ID's
			while ( isset($wp_registered_sidebars[$_args['id']]) )
				$_args['id'] = $args['id'] . '-' . $n++;
		} else {
			$n = count($wp_registered_sidebars);
			do {
				$_args['id'] = 'sidebar-' . ++$n;
			} while ( isset($wp_registered_sidebars[$_args['id']]) );
		}
		register_sidebar($_args);
	}
}

/**
 * Builds the definition for a single sidebar and returns the ID.
 *
 * The $args parameter takes either a string or an array with 'name' and 'id'
 * contained in either usage. It will be noted that the values will be applied
 * to all sidebars, so if creating more than one, it will be advised to allow
 * for WordPress to create the defaults for you.
 *
 * Example for string would be <code>'name=whatever;id=whatever1'</code> and for
 * the array it would be <code>array(
 *    'name' => 'whatever',
 *    'id' => 'whatever1')</code>.
 *
 * name - The name of the sidebar, which presumably the title which will be
 *     displayed.
 * id - The unique identifier by which the sidebar will be called by.
 * before_widget - The content that will prepended to the widgets when they are
 *     displayed.
 * after_widget - The content that will be appended to the widgets when they are
 *     displayed.
 * before_title - The content that will be prepended to the title when displayed.
 * after_title - the content that will be appended to the title when displayed.
 *
 * <em>Content</em> is assumed to be HTML and should be formatted as such, but
 * doesn't have to be.
 *
 * @since 2.2.0
 * @uses $wp_registered_sidebars Stores the new sidebar in this array by sidebar ID.
 *
 * @param string|array $args Builds Sidebar based off of 'name' and 'id' values
 * @return string The sidebar id that was added.
 */
function register_sidebar($args = array()) {
	global $wp_registered_sidebars;

	$i = count($wp_registered_sidebars) + 1;

	$defaults = array(
		'name' => sprintf(__('Sidebar %d'), $i ),
		'id' => "sidebar-$i",
		'description' => '',
		'class' => '',
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => "</li>\n",
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => "</h2>\n",
	);

	$sidebar = wp_parse_args( $args, $defaults );

	$wp_registered_sidebars[$sidebar['id']] = $sidebar;

	add_theme_support('widgets');

	do_action( 'register_sidebar', $sidebar );

	return $sidebar['id'];
}

/**
 * Removes a sidebar from the list.
 *
 * @since 2.2.0
 *
 * @uses $wp_registered_sidebars Stores the new sidebar in this array by sidebar ID.
 *
 * @param string $name The ID of the sidebar when it was added.
 */
function unregister_sidebar( $name ) {
	global $wp_registered_sidebars;

	if ( isset( $wp_registered_sidebars[$name] ) )
		unset( $wp_registered_sidebars[$name] );
}

/**
 * Register widget for use in sidebars.
 *
 * The default widget option is 'classname' that can be override.
 *
 * The function can also be used to unregister widgets when $output_callback
 * parameter is an empty string.
 *
 * @since 2.2.0
 *
 * @uses $wp_registered_widgets Uses stored registered widgets.
 * @uses $wp_register_widget_defaults Retrieves widget defaults.
 *
 * @param int|string $id Widget ID.
 * @param string $name Widget display title.
 * @param callback $output_callback Run when widget is called.
 * @param array|string $options Optional. Widget Options.
 * @param mixed $params,... Widget parameters to add to widget.
 * @return null Will return if $output_callback is empty after removing widget.
 */
function wp_register_sidebar_widget($id, $name, $output_callback, $options = array()) {
	global $wp_registered_widgets, $wp_registered_widget_controls, $wp_registered_widget_updates, $_wp_deprecated_widgets_callbacks;

	$id = strtolower($id);

	if ( empty($output_callback) ) {
		unset($wp_registered_widgets[$id]);
		return;
	}

	$id_base = _get_widget_id_base($id);
	if ( in_array($output_callback, $_wp_deprecated_widgets_callbacks, true) && !is_callable($output_callback) ) {
		if ( isset($wp_registered_widget_controls[$id]) )
			unset($wp_registered_widget_controls[$id]);

		if ( isset($wp_registered_widget_updates[$id_base]) )
			unset($wp_registered_widget_updates[$id_base]);

		return;
	}

	$defaults = array('classname' => $output_callback);
	$options = wp_parse_args($options, $defaults);
	$widget = array(
		'name' => $name,
		'id' => $id,
		'callback' => $output_callback,
		'params' => array_slice(func_get_args(), 4)
	);
	$widget = array_merge($widget, $options);

	if ( is_callable($output_callback) && ( !isset($wp_registered_widgets[$id]) || did_action( 'widgets_init' ) ) ) {
		do_action( 'wp_register_sidebar_widget', $widget );
		$wp_registered_widgets[$id] = $widget;
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
 * @param int|string $id Widget ID.
 * @return string Widget description, if available. Null on failure to retrieve description.
 */
function wp_widget_description( $id ) {
	if ( !is_scalar($id) )
		return;

	global $wp_registered_widgets;

	if ( isset($wp_registered_widgets[$id]['description']) )
		return esc_html( $wp_registered_widgets[$id]['description'] );
}

/**
 * Retrieve description for a sidebar.
 *
 * When registering sidebars a 'description' parameter can be included that
 * describes the sidebar for display on the widget administration panel.
 *
 * @since 2.9.0
 *
 * @param int|string $id sidebar ID.
 * @return string Sidebar description, if available. Null on failure to retrieve description.
 */
function wp_sidebar_description( $id ) {
	if ( !is_scalar($id) )
		return;

	global $wp_registered_sidebars;

	if ( isset($wp_registered_sidebars[$id]['description']) )
		return esc_html( $wp_registered_sidebars[$id]['description'] );
}


/**
 * Remove widget from sidebar.
 *
 * @since 2.2.0
 *
 * @param int|string $id Widget ID.
 */
function wp_unregister_sidebar_widget($id) {
	do_action( 'wp_unregister_sidebar_widget', $id );

	wp_register_sidebar_widget($id, '', '');
	wp_unregister_widget_control($id);
}

/**
 * Registers widget control callback for customizing options.
 *
 * The options contains the 'height', 'width', and 'id_base' keys. The 'height'
 * option is never used. The 'width' option is the width of the fully expanded
 * control form, but try hard to use the default width. The 'id_base' is for
 * multi-widgets (widgets which allow multiple instances such as the text
 * widget), an id_base must be provided. The widget id will end up looking like
 * {$id_base}-{$unique_number}.
 *
 * @since 2.2.0
 *
 * @param int|string $id Sidebar ID.
 * @param string $name Sidebar display name.
 * @param callback $control_callback Run when sidebar is displayed.
 * @param array|string $options Optional. Widget options. See above long description.
 * @param mixed $params,... Optional. Additional parameters to add to widget.
 */
function wp_register_widget_control($id, $name, $control_callback, $options = array()) {
	global $wp_registered_widget_controls, $wp_registered_widget_updates, $wp_registered_widgets, $_wp_deprecated_widgets_callbacks;

	$id = strtolower($id);
	$id_base = _get_widget_id_base($id);

	if ( empty($control_callback) ) {
		unset($wp_registered_widget_controls[$id]);
		unset($wp_registered_widget_updates[$id_base]);
		return;
	}

	if ( in_array($control_callback, $_wp_deprecated_widgets_callbacks, true) && !is_callable($control_callback) ) {
		if ( isset($wp_registered_widgets[$id]) )
			unset($wp_registered_widgets[$id]);

		return;
	}

	if ( isset($wp_registered_widget_controls[$id]) && !did_action( 'widgets_init' ) )
		return;

	$defaults = array('width' => 250, 'height' => 200 ); // height is never used
	$options = wp_parse_args($options, $defaults);
	$options['width'] = (int) $options['width'];
	$options['height'] = (int) $options['height'];

	$widget = array(
		'name' => $name,
		'id' => $id,
		'callback' => $control_callback,
		'params' => array_slice(func_get_args(), 4)
	);
	$widget = array_merge($widget, $options);

	$wp_registered_widget_controls[$id] = $widget;

	if ( isset($wp_registered_widget_updates[$id_base]) )
		return;

	if ( isset($widget['params'][0]['number']) )
		$widget['params'][0]['number'] = -1;

	unset($widget['width'], $widget['height'], $widget['name'], $widget['id']);
	$wp_registered_widget_updates[$id_base] = $widget;
}

function _register_widget_update_callback($id_base, $update_callback, $options = array()) {
	global $wp_registered_widget_updates;

	if ( isset($wp_registered_widget_updates[$id_base]) ) {
		if ( empty($update_callback) )
			unset($wp_registered_widget_updates[$id_base]);
		return;
	}

	$widget = array(
		'callback' => $update_callback,
		'params' => array_slice(func_get_args(), 3)
	);

	$widget = array_merge($widget, $options);
	$wp_registered_widget_updates[$id_base] = $widget;
}

function _register_widget_form_callback($id, $name, $form_callback, $options = array()) {
	global $wp_registered_widget_controls;

	$id = strtolower($id);

	if ( empty($form_callback) ) {
		unset($wp_registered_widget_controls[$id]);
		return;
	}

	if ( isset($wp_registered_widget_controls[$id]) && !did_action( 'widgets_init' ) )
		return;

	$defaults = array('width' => 250, 'height' => 200 );
	$options = wp_parse_args($options, $defaults);
	$options['width'] = (int) $options['width'];
	$options['height'] = (int) $options['height'];

	$widget = array(
		'name' => $name,
		'id' => $id,
		'callback' => $form_callback,
		'params' => array_slice(func_get_args(), 4)
	);
	$widget = array_merge($widget, $options);

	$wp_registered_widget_controls[$id] = $widget;
}

/**
 * Remove control callback for widget.
 *
 * @since 2.2.0
 * @uses wp_register_widget_control() Unregisters by using empty callback.
 *
 * @param int|string $id Widget ID.
 */
function wp_unregister_widget_control($id) {
	return wp_register_widget_control($id, '', '');
}

/**
 * Display dynamic sidebar.
 *
 * By default it displays the default sidebar or 'sidebar-1'. The 'sidebar-1' is
 * not named by the theme, the actual name is '1', but 'sidebar-' is added to
 * the registered sidebars for the name. If you named your sidebar 'after-post',
 * then the parameter $index will still be 'after-post', but the lookup will be
 * for 'sidebar-after-post'.
 *
 * It is confusing for the $index parameter, but just know that it should just
 * work. When you register the sidebar in the theme, you will use the same name
 * for this function or "Pay no heed to the man behind the curtain." Just accept
 * it as an oddity of WordPress sidebar register and display.
 *
 * @since 2.2.0
 *
 * @param int|string $index Optional, default is 1. Name or ID of dynamic sidebar.
 * @return bool True, if widget sidebar was found and called. False if not found or not called.
 */
function dynamic_sidebar($index = 1) {
	global $wp_registered_sidebars, $wp_registered_widgets;

	if ( is_int($index) ) {
		$index = "sidebar-$index";
	} else {
		$index = sanitize_title($index);
		foreach ( (array) $wp_registered_sidebars as $key => $value ) {
			if ( sanitize_title($value['name']) == $index ) {
				$index = $key;
				break;
			}
		}
	}

	$sidebars_widgets = wp_get_sidebars_widgets();
	if ( empty( $sidebars_widgets ) )
		return false;

	if ( empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
		return false;

	$sidebar = $wp_registered_sidebars[$index];

	$did_one = false;
	foreach ( (array) $sidebars_widgets[$index] as $id ) {

		if ( !isset($wp_registered_widgets[$id]) ) continue;

		$params = array_merge(
			array( array_merge( $sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
			(array) $wp_registered_widgets[$id]['params']
		);

		// Substitute HTML id and class attributes into before_widget
		$classname_ = '';
		foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
			if ( is_string($cn) )
				$classname_ .= '_' . $cn;
			elseif ( is_object($cn) )
				$classname_ .= '_' . get_class($cn);
		}
		$classname_ = ltrim($classname_, '_');
		$params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);

		$params = apply_filters( 'dynamic_sidebar_params', $params );

		$callback = $wp_registered_widgets[$id]['callback'];

		do_action( 'dynamic_sidebar', $wp_registered_widgets[$id] );

		if ( is_callable($callback) ) {
			call_user_func_array($callback, $params);
			$did_one = true;
		}
	}

	return $did_one;
}

/**
 * Whether widget is displayed on the front-end.
 *
 * Either $callback or $id_base can be used
 * $id_base is the first argument when extending WP_Widget class
 * Without the optional $widget_id parameter, returns the ID of the first sidebar
 * in which the first instance of the widget with the given callback or $id_base is found.
 * With the $widget_id parameter, returns the ID of the sidebar where
 * the widget with that callback/$id_base AND that ID is found.
 *
 * NOTE: $widget_id and $id_base are the same for single widgets. To be effective
 * this function has to run after widgets have initialized, at action 'init' or later.
 *
 * @since 2.2.0
 *
 * @param string $callback Optional, Widget callback to check.
 * @param int $widget_id Optional, but needed for checking. Widget ID.
 * @param string $id_base Optional, the base ID of a widget created by extending WP_Widget.
 * @param bool $skip_inactive Optional, whether to check in 'wp_inactive_widgets'.
 * @return mixed false if widget is not active or id of sidebar in which the widget is active.
 */
function is_active_widget($callback = false, $widget_id = false, $id_base = false, $skip_inactive = true) {
	global $wp_registered_widgets;

	$sidebars_widgets = wp_get_sidebars_widgets();

	if ( is_array($sidebars_widgets) ) {
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( $skip_inactive && 'wp_inactive_widgets' == $sidebar )
				continue;

			if ( is_array($widgets) ) {
				foreach ( $widgets as $widget ) {
					if ( ( $callback && isset($wp_registered_widgets[$widget]['callback']) && $wp_registered_widgets[$widget]['callback'] == $callback ) || ( $id_base && _get_widget_id_base($widget) == $id_base ) ) {
						if ( !$widget_id || $widget_id == $wp_registered_widgets[$widget]['id'] )
							return $sidebar;
					}
				}
			}
		}
	}
	return false;
}

/**
 * Whether the dynamic sidebar is enabled and used by theme.
 *
 * @since 2.2.0
 *
 * @return bool True, if using widgets. False, if not using widgets.
 */
function is_dynamic_sidebar() {
	global $wp_registered_widgets, $wp_registered_sidebars;
	$sidebars_widgets = get_option('sidebars_widgets');
	foreach ( (array) $wp_registered_sidebars as $index => $sidebar ) {
		if ( count($sidebars_widgets[$index]) ) {
			foreach ( (array) $sidebars_widgets[$index] as $widget )
				if ( array_key_exists($widget, $wp_registered_widgets) )
					return true;
		}
	}
	return false;
}

/**
 * Whether a sidebar is in use.
 *
 * @since 2.8
 *
 * @param mixed $index Sidebar name, id or number to check.
 * @return bool true if the sidebar is in use, false otherwise.
 */
function is_active_sidebar( $index ) {
	$index = ( is_int($index) ) ? "sidebar-$index" : sanitize_title($index);
	$sidebars_widgets = wp_get_sidebars_widgets();
	if ( !empty($sidebars_widgets[$index]) )
		return true;

	return false;
}

/* Internal Functions */

/**
 * Retrieve full list of sidebars and their widgets.
 *
 * Will upgrade sidebar widget list, if needed. Will also save updated list, if
 * needed.
 *
 * @since 2.2.0
 * @access private
 *
 * @param bool $deprecated Not used (deprecated).
 * @return array Upgraded list of widgets to version 3 array format when called from the admin.
 */
function wp_get_sidebars_widgets($deprecated = true) {
	if ( $deprecated !== true )
		_deprecated_argument( __FUNCTION__, '2.8.1' );

	global $wp_registered_widgets, $wp_registered_sidebars, $_wp_sidebars_widgets;

	// If loading from front page, consult $_wp_sidebars_widgets rather than options
	// to see if wp_convert_widget_settings() has made manipulations in memory.
	if ( !is_admin() ) {
		if ( empty($_wp_sidebars_widgets) )
			$_wp_sidebars_widgets = get_option('sidebars_widgets', array());

		$sidebars_widgets = $_wp_sidebars_widgets;
	} else {
		$sidebars_widgets = get_option('sidebars_widgets', array());
		$_sidebars_widgets = array();

		if ( isset($sidebars_widgets['wp_inactive_widgets']) || empty($sidebars_widgets) )
			$sidebars_widgets['array_version'] = 3;
		elseif ( !isset($sidebars_widgets['array_version']) )
			$sidebars_widgets['array_version'] = 1;

		switch ( $sidebars_widgets['array_version'] ) {
			case 1 :
				foreach ( (array) $sidebars_widgets as $index => $sidebar )
				if ( is_array($sidebar) )
				foreach ( (array) $sidebar as $i => $name ) {
					$id = strtolower($name);
					if ( isset($wp_registered_widgets[$id]) ) {
						$_sidebars_widgets[$index][$i] = $id;
						continue;
					}
					$id = sanitize_title($name);
					if ( isset($wp_registered_widgets[$id]) ) {
						$_sidebars_widgets[$index][$i] = $id;
						continue;
					}

					$found = false;

					foreach ( $wp_registered_widgets as $widget_id => $widget ) {
						if ( strtolower($widget['name']) == strtolower($name) ) {
							$_sidebars_widgets[$index][$i] = $widget['id'];
							$found = true;
							break;
						} elseif ( sanitize_title($widget['name']) == sanitize_title($name) ) {
							$_sidebars_widgets[$index][$i] = $widget['id'];
							$found = true;
							break;
						}
					}

					if ( $found )
						continue;

					unset($_sidebars_widgets[$index][$i]);
				}
				$_sidebars_widgets['array_version'] = 2;
				$sidebars_widgets = $_sidebars_widgets;
				unset($_sidebars_widgets);

			case 2 :
				$sidebars_widgets = retrieve_widgets();
		}
	}

	if ( is_array( $sidebars_widgets ) && isset($sidebars_widgets['array_version']) )
		unset($sidebars_widgets['array_version']);

	$sidebars_widgets = apply_filters('sidebars_widgets', $sidebars_widgets);
	return $sidebars_widgets;
}

/**
 * Set the sidebar widget option to update sidebars.
 *
 * @since 2.2.0
 * @access private
 *
 * @param array $sidebars_widgets Sidebar widgets and their settings.
 */
function wp_set_sidebars_widgets( $sidebars_widgets ) {
	if ( !isset( $sidebars_widgets['array_version'] ) )
		$sidebars_widgets['array_version'] = 3;
	update_option( 'sidebars_widgets', $sidebars_widgets );
}

/**
 * Retrieve default registered sidebars list.
 *
 * @since 2.2.0
 * @access private
 *
 * @return array
 */
function wp_get_widget_defaults() {
	global $wp_registered_sidebars;

	$defaults = array();

	foreach ( (array) $wp_registered_sidebars as $index => $sidebar )
		$defaults[$index] = array();

	return $defaults;
}

/**
 * Convert the widget settings from single to multi-widget format.
 *
 * @since 2.8.0
 *
 * @return array
 */
function wp_convert_widget_settings($base_name, $option_name, $settings) {
	// This test may need expanding.
	$single = $changed = false;
	if ( empty($settings) ) {
		$single = true;
	} else {
		foreach ( array_keys($settings) as $number ) {
			if ( 'number' == $number )
				continue;
			if ( !is_numeric($number) ) {
				$single = true;
				break;
			}
		}
	}

	if ( $single ) {
		$settings = array( 2 => $settings );

		// If loading from the front page, update sidebar in memory but don't save to options
		if ( is_admin() ) {
			$sidebars_widgets = get_option('sidebars_widgets');
		} else {
			if ( empty($GLOBALS['_wp_sidebars_widgets']) )
				$GLOBALS['_wp_sidebars_widgets'] = get_option('sidebars_widgets', array());
			$sidebars_widgets = &$GLOBALS['_wp_sidebars_widgets'];
		}

		foreach ( (array) $sidebars_widgets as $index => $sidebar ) {
			if ( is_array($sidebar) ) {
				foreach ( $sidebar as $i => $name ) {
					if ( $base_name == $name ) {
						$sidebars_widgets[$index][$i] = "$name-2";
						$changed = true;
						break 2;
					}
				}
			}
		}

		if ( is_admin() && $changed )
			update_option('sidebars_widgets', $sidebars_widgets);
	}

	$settings['_multiwidget'] = 1;
	if ( is_admin() )
		update_option( $option_name, $settings );

	return $settings;
}

/**
 * Output an arbitrary widget as a template tag
 *
 * @since 2.8
 *
 * @param string $widget the widget's PHP class name (see default-widgets.php)
 * @param array $instance the widget's instance settings
 * @param array $args the widget's sidebar args
 * @return void
 **/
function the_widget($widget, $instance = array(), $args = array()) {
	global $wp_widget_factory;

	$widget_obj = $wp_widget_factory->widgets[$widget];
	if ( !is_a($widget_obj, 'WP_Widget') )
		return;

	$before_widget = sprintf('<div class="widget %s">', $widget_obj->widget_options['classname']);
	$default_args = array('before_widget' => $before_widget, 'after_widget' => "</div>", 'before_title' => '<h2 class="widgettitle">', 'after_title' => '</h2>');

	$args = wp_parse_args($args, $default_args);
	$instance = wp_parse_args($instance);

	do_action( 'the_widget', $widget, $instance, $args );

	$widget_obj->_set(-1);
	$widget_obj->widget($args, $instance);
}

/**
 * Private
 */
function _get_widget_id_base($id) {
	return preg_replace( '/-[0-9]+$/', '', $id );
}

/**
 * Handle sidebars config after theme change
 *
 * @access private
 * @since 3.3
 */  
function _wp_sidebars_changed() {
	global $sidebars_widgets;

	if ( ! is_array( $sidebars_widgets ) )
		$sidebars_widgets = wp_get_sidebars_widgets();

	retrieve_widgets();
}

// look for "lost" widgets, this has to run at least on each theme change
function retrieve_widgets() {
	global $wp_registered_widget_updates, $wp_registered_sidebars, $sidebars_widgets, $wp_registered_widgets;

	$old_sidebars_widgets = get_theme_mod( 'sidebars_widgets' );
	if ( is_array( $old_sidebars_widgets ) ) {
		// time() that sidebars were stored is in $old_sidebars_widgets['time']
		$_sidebars_widgets = $old_sidebars_widgets['data'];
		remove_theme_mod( 'sidebars_widgets' );
	} else {
		if ( ! is_array( $sidebars_widgets ) )
			$sidebars_widgets = wp_get_sidebars_widgets();

		$sidebars = array_keys($wp_registered_sidebars);

		unset( $sidebars_widgets['array_version'] );

		$old = array_keys($sidebars_widgets);
		sort($old);
		sort($sidebars);

		if ( $old == $sidebars )
			return;

		$_sidebars_widgets = array(
			'wp_inactive_widgets' => $sidebars_widgets['wp_inactive_widgets']
		);

		unset( $sidebars_widgets['wp_inactive_widgets'] );

		foreach ( $wp_registered_sidebars as $id => $settings ) {
			if ( ! empty( $sidebars_widgets ) )
				$_sidebars_widgets[$id] = array_shift( $sidebars_widgets );
		}

		if ( !empty($sidebars_widgets) ) {
			$orphaned = 0;

			foreach ( $sidebars_widgets as $val ) {
				if ( is_array($val) && ! empty( $val ) )
					$_sidebars_widgets['orphaned_widgets_' . ++$orphaned] = $val;
			}
		}
	}

	// discard invalid, theme-specific widgets from sidebars
	$shown_widgets = array();

	foreach ( $_sidebars_widgets as $sidebar => $widgets ) {
		if ( !is_array($widgets) )
			continue;

		$_widgets = array();
		foreach ( $widgets as $widget ) {
			if ( isset($wp_registered_widgets[$widget]) )
				$_widgets[] = $widget;
		}

		$_sidebars_widgets[$sidebar] = $_widgets;
		$shown_widgets = array_merge($shown_widgets, $_widgets);
	}

	$sidebars_widgets = $_sidebars_widgets;
	unset($_sidebars_widgets, $_widgets);

	// find hidden/lost multi-widget instances
	$lost_widgets = array();
	foreach ( $wp_registered_widgets as $key => $val ) {
		if ( in_array($key, $shown_widgets, true) )
			continue;

		$number = preg_replace('/.+?-([0-9]+)$/', '$1', $key);

		if ( 2 > (int) $number )
			continue;

		$lost_widgets[] = $key;
	}

	$sidebars_widgets['wp_inactive_widgets'] = array_merge($lost_widgets, (array) $sidebars_widgets['wp_inactive_widgets']);
	wp_set_sidebars_widgets($sidebars_widgets);

	return $sidebars_widgets;
}
