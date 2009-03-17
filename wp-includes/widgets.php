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
 * This class must be extended for each widget and WP_Widget::widget(), WP_Widget::update()
 * and WP_Widget::form() need to be over-ridden.
 * 
 * @package WordPress
 * @subpackage Widgets
 * @since 2.8
 */
class WP_Widget {

	var $id_base;         	// Root id for all widgets of this type.
	var $name;				// Name for this widget type.
	var $widget_options;	// Option array passed to wp_register_sidebar_widget()
	var $control_options;	// Option array passed to wp_register_widget_control()

	var $number = false;	// Unique ID number of the current instance.
	var $id = false;		// Unique ID string of the current instance (id_base-number)
	var $updated = false;	// Set true when we update the data after a POST submit - makes sure we don't do it twice.

	// Member functions that you must over-ride.

	/** Echo the actual widget content. Subclasses should over-ride this function
	 *	to generate their widget code. */
	function widget($args, $instance) {
		die('function WP_Widget::widget() must be over-ridden in a sub-class.');
	}

	/** Update a particular instance.
	 *	This function should check that $new_instance is set correctly.
	 *	The newly calculated value of $instance should be returned. */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** Echo a control form for the current instance. */
	function form($instance) {
		echo '<p>' . __('There are no options for this widget.') . '</p>';
	}

	// Functions you'll need to call.

	/**
	 * PHP4 constructor
	 */
	function WP_Widget( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		$this->__construct( $id_base, $name, $widget_options, $control_options );
	}

	/**
	 * PHP5 constructor
	 *	 widget_options: passed to wp_register_sidebar_widget()
	 *	 - description
	 *	 - classname
	 *	 control_options: passed to wp_register_widget_control()
	 *	 - width
	 *	 - height
	 */
	function __construct( $id_base, $name, $widget_options = array(), $control_options = array() ) {
		$this->id_base = $id_base;
		$this->name = $name;
		$this->option_name = 'widget_' . $id_base;
		$this->widget_options = wp_parse_args( $widget_options, array('classname' => $this->option_name) );
		$this->control_options = wp_parse_args( $control_options, array('id_base' => $this->id_base) );

		add_action( 'widgets_init', array( &$this, 'register' ) );
	}

	/** Helper function to be called by form().
	 *	Returns an HTML name for the field. */
	function get_field_name($field_name) {
		return 'widget-' . $this->id_base . '[' . $this->number . '][' . $field_name . ']';
	}

	/** Helper function to be called by form().
	 *	Returns an HTML id for the field. */
	function get_field_id($field_name) {
		return 'widget-' . $this->id_base . '-' . $this->number . '-' . $field_name;
	}

	/** Registers this widget-type.
	 *	Called during the 'widgets_init' action. */
	function register() {
		$settings = $this->get_settings();

		if ( empty($settings) ) {
			// If there are none, we register the widget's existance with a
			// generic template
			$this->_set(1);
			$this->_register_one();
		} elseif ( is_array($settings) ) {
			foreach ( array_keys($settings) as $number ) {
				if ( is_numeric($number) ) {
					$this->_set($number);
					$this->_register_one($number);
				}
			}
		}
	}

	// Private Functions. Don't worry about these.

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
		$settings = $this->get_settings();

		if ( array_key_exists( $this->number, $settings ) )
			$this->widget($args, $settings[$this->number]);
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
		if ( !$this->updated && !empty($_POST['sidebar']) ) {

			// Tells us what sidebar to put the data in
			$sidebar = (string) $_POST['sidebar'];

			$sidebars_widgets = wp_get_sidebars_widgets();
			if ( isset($sidebars_widgets[$sidebar]) )
				$this_sidebar =& $sidebars_widgets[$sidebar];
			else
				$this_sidebar = array();

			foreach ( $this_sidebar as $_widget_id )	{
				// Remove all widgets of this type from the sidebar. We'll add the
				// new data in a second. This makes sure we don't get any duplicate
				// data since widget ids aren't necessarily persistent across multiple
				// updates
				if ( $this->_get_display_callback() == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
					$number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
					if( !in_array( $this->id_base . '-' . $number, (array)$_POST['widget-id'] ) ) {
						// the widget has been removed.
						unset($all_instances[$number]);
					}
				}
			}

			foreach ( (array) $_POST['widget-' . $this->id_base] as $number => $new_instance ) {
				$new_instance = stripslashes_deep($new_instance);
				$this->_set($number);

				if ( !isset($new_instance['submit']) )
					continue;

				if ( isset($all_instances[$number]) )
					$instance = $this->update($new_instance, $all_instances[$number]);
				else
					$instance = $this->update($new_instance, array());

				if ( !empty($instance) )
					$all_instances[$number] = $instance;
			}

			$this->save_settings($all_instances);
			$this->updated = true;
		}
	}

	/** Generate the control form.
	 *	Do NOT over-ride this function. */
	function form_callback( $widget_args = 1 ) {
		if ( is_numeric($widget_args) )
			$widget_args = array( 'number' => $widget_args );

		$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
		$all_instances = $this->get_settings();

		if ( -1 == $widget_args['number'] ) {
			// We echo out a form where 'number' can be set later via JS
			$this->_set('%i%');
			$instance = array();
		} else {
			$this->_set($widget_args['number']);
			$instance = $all_instances[ $widget_args['number'] ];
		}

		$this->form($instance);
?>
		<input type="hidden" id="<?php echo $this->get_field_id('submit'); ?>" name="<?php echo $this->get_field_name('submit'); ?>" value="1" />
<?php
	}

	/** Helper function: Registers a single instance. */
	function _register_one($number = -1) {
		wp_register_sidebar_widget(	$this->id, $this->name,	$this->_get_display_callback(), $this->widget_options, array( 'number' => $number ) );
		_register_widget_update_callback(	$this->id, $this->name,	$this->_get_update_callback(), $this->control_options, array( 'number' => $number ) );
		_register_widget_form_callback(	$this->id, $this->name,	$this->_get_form_callback(), $this->control_options, array( 'number' => $number ) );
	}
	
	function save_settings($settings) {
		$settings['_multiwidget'] = 1;
		update_option( $this->option_name, $settings );
	}

	function get_settings() {
		$settings = get_option($this->option_name);	

		if ( !is_array($settings) )
			$settings = array();

		if ( !array_key_exists('_multiwidget', $settings) ) {
			// old format, conver if single widget
			$settings = wp_convert_widget_settings($this->id_base, $this->option_name, $settings);
		}

		unset($settings['_multiwidget']);
		return $settings;
	}
}

/* Template tags & API functions */

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
 * number the sidebar creation is currently at.
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

	for ( $i=1; $i <= $number; $i++ ) {
		$_args = $args;

		if ( $number > 1 ) {
			$_args['name'] = isset($args['name']) ? sprintf($args['name'], $i) : sprintf(__('Sidebar %d'), $i);
		} else {
			$_args['name'] = isset($args['name']) ? $args['name'] : __('Sidebar');
		}

		if (isset($args['id'])) {
			$_args['id'] = $args['id'];
		} else {
			$n = count($wp_registered_sidebars);
			do {
				$n++;
				$_args['id'] = "sidebar-$n";
			} while (isset($wp_registered_sidebars[$_args['id']]));
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
 * @uses parse_str() Converts a string to an array to be used in the rest of the function.
 * @usedby register_sidebars()
 *
 * @param string|array $args Builds Sidebar based off of 'name' and 'id' values
 * @return string The sidebar id that was added.
 */
function register_sidebar($args = array()) {
	global $wp_registered_sidebars;

	if ( is_string($args) )
		parse_str($args, $args);

	$i = count($wp_registered_sidebars) + 1;

	$defaults = array(
		'name' => sprintf(__('Sidebar %d'), $i ),
		'id' => "sidebar-$i",
		'before_widget' => '<li id="%1$s" class="widget %2$s">',
		'after_widget' => "</li>\n",
		'before_title' => '<h2 class="widgettitle">',
		'after_title' => "</h2>\n",
	);

	$sidebar = array_merge($defaults, (array) $args);

	$wp_registered_sidebars[$sidebar['id']] = $sidebar;

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
 * @param array|string Optional. $options Widget Options.
 * @param mixed $params,... Widget parameters to add to widget.
 * @return null Will return if $output_callback is empty after removing widget.
 */
function wp_register_sidebar_widget($id, $name, $output_callback, $options = array()) {
	global $wp_registered_widgets;

	$id = strtolower($id);

	if ( empty($output_callback) ) {
		unset($wp_registered_widgets[$id]);
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

	if ( is_callable($output_callback) && ( !isset($wp_registered_widgets[$id]) || did_action( 'widgets_init' ) ) )
		$wp_registered_widgets[$id] = $widget;
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
		return wp_specialchars( $wp_registered_widgets[$id]['description'] );
}

/**
 * Remove widget from sidebar.
 *
 * @since 2.2.0
 *
 * @param int|string $id Widget ID.
 */
function wp_unregister_sidebar_widget($id) {
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
	global $wp_registered_widget_controls, $wp_registered_widget_updates;

	$id = strtolower($id);

	if ( empty($control_callback) ) {
		unset($wp_registered_widget_controls[$id]);
		unset($wp_registered_widget_updates[$id]);
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

	$wp_registered_widget_controls[$id] = $wp_registered_widget_updates[$id] = $widget;
}

function _register_widget_update_callback($id, $name, $update_callback, $options = array()) {
	global $wp_registered_widget_updates;

	$id = strtolower($id);

	if ( empty($update_callback) ) {
		unset($wp_registered_widget_updates[$id]);
		return;
	}

	if ( isset($wp_registered_widget_updates[$id]) && !did_action( 'widgets_init' ) )
		return;

	$defaults = array('width' => 250, 'height' => 200 ); // height is never used
	$options = wp_parse_args($options, $defaults);
	$options['width'] = (int) $options['width'];
	$options['height'] = (int) $options['height'];

	$widget = array(
		'name' => $name,
		'id' => $id,
		'callback' => $update_callback,
		'params' => array_slice(func_get_args(), 4)
	);
	$widget = array_merge($widget, $options);

	$wp_registered_widget_updates[$id] = $widget;
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

	$defaults = array('width' => 250, 'height' => 200 ); // height is never used
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

	if ( empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
		return false;

	$sidebar = $wp_registered_sidebars[$index];

	$did_one = false;
	foreach ( (array) $sidebars_widgets[$index] as $id ) {
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

		if ( is_callable($callback) ) {
			call_user_func_array($callback, $params);
			$did_one = true;
		}
	}

	return $did_one;
}

/**
 * Whether widget is registered using callback with widget ID.
 *
 * Without the optional $widget_id parameter, returns the ID of the first sidebar in which the first instance of the widget with the given callback is found.
 * With the $widget_id parameter, returns the ID of the sidebar in which the widget with that callback AND that ID is found.
 *
 * @since 2.2.0
 *
 * @param callback $callback Widget callback to check.
 * @param int $widget_id Optional, but needed for checking. Widget ID.
/* @return mixed false if widget is not active or id of sidebar in which the widget is active.
 */
function is_active_widget($callback, $widget_id = false) {
	global $wp_registered_widgets;

	$sidebars_widgets = wp_get_sidebars_widgets(false);

	if ( is_array($sidebars_widgets) ) foreach ( $sidebars_widgets as $sidebar => $widgets )
		if ( is_array($widgets) ) foreach ( $widgets as $widget )
			if ( isset($wp_registered_widgets[$widget]['callback']) && $wp_registered_widgets[$widget]['callback'] == $callback )
				if ( !$widget_id || $widget_id == $wp_registered_widgets[$widget]['id'] )
					return $sidebar;


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
 * @param bool $update Optional, default is true. Whether to save upgrade of widget array list.
 * @return array Upgraded list of widgets to version 2 array format.
 */
function wp_get_sidebars_widgets($update = true) {
	global $wp_registered_widgets, $wp_registered_sidebars;

	$sidebars_widgets = get_option('sidebars_widgets', array());
	$_sidebars_widgets = array();

	if ( !isset($sidebars_widgets['array_version']) )
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
			$sidebars = array_keys( $wp_registered_sidebars );
			if ( !empty( $sidebars ) ) {
				// Move the known-good ones first
				foreach ( (array) $sidebars as $id ) {
					if ( array_key_exists( $id, $sidebars_widgets ) ) {
						$_sidebars_widgets[$id] = $sidebars_widgets[$id];
						unset($sidebars_widgets[$id], $sidebars[$id]);
					}
				}

				// Assign to each unmatched registered sidebar the first available orphan
				unset( $sidebars_widgets[ 'array_version' ] );
				while ( ( $sidebar = array_shift( $sidebars ) ) && $widgets = array_shift( $sidebars_widgets ) )
					$_sidebars_widgets[ $sidebar ] = $widgets;

				$_sidebars_widgets['array_version'] = 3;
				$sidebars_widgets = $_sidebars_widgets;
				unset($_sidebars_widgets);
			}

			if ( $update )
				update_option('sidebars_widgets', $sidebars_widgets);
	}

	if ( isset($sidebars_widgets['array_version']) )
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
	$single = false;
	if ( empty($settings) ) {
		$single = true;
	} else {
		foreach ( array_keys($settings) as $number ) {
			if ( !is_numeric($number) ) {
				$single = true;
				break;
			}
		}
	}

	if ( $single ) {
		$settings = array( 2 => $settings );
		
		$sidebars_widgets = get_option('sidebars_widgets');
		foreach ( (array) $sidebars_widgets as $index => $sidebar ) {
			if ( is_array($sidebar) ) {
				foreach ( $sidebar as $i => $name ) {
					if ( $base_name == $name ) {
						$sidebars_widgets[$index][$i] = "$name-2";
						break 2;
					}
				}
			}
		}

		update_option('sidebars_widgets', $sidebars_widgets);
	}

	$settings['_multiwidget'] = 1;
	update_option( $option_name, $settings );
	
	return $settings;
}

/**
 * Deprecated API
 */
 
/**
 * Register widget for sidebar with backwards compatibility.
 *
 * Allows $name to be an array that accepts either three elements to grab the
 * first element and the third for the name or just uses the first element of
 * the array for the name.
 *
 * Passes to {@link wp_register_sidebar_widget()} after argument list and
 * backwards compatibility is complete.
 *
 * @since 2.2.0
 * @uses wp_register_sidebar_widget() Passes the compiled arguments.
 *
 * @param string|int $name Widget ID.
 * @param callback $output_callback Run when widget is called.
 * @param string $classname Classname widget option.
 * @param mixed $params,... Widget parameters.
 */
function register_sidebar_widget($name, $output_callback, $classname = '') {
	// Compat
	if ( is_array($name) ) {
		if ( count($name) == 3 )
			$name = sprintf($name[0], $name[2]);
		else
			$name = $name[0];
	}

	$id = sanitize_title($name);
	$options = array();
	if ( !empty($classname) && is_string($classname) )
		$options['classname'] = $classname;
	$params = array_slice(func_get_args(), 2);
	$args = array($id, $name, $output_callback, $options);
	if ( !empty($params) )
		$args = array_merge($args, $params);

	call_user_func_array('wp_register_sidebar_widget', $args);
}

/**
 * Alias of {@link wp_unregister_sidebar_widget()}.
 *
 * @see wp_unregister_sidebar_widget()
 *
 * @since 2.2.0
 *
 * @param int|string $id Widget ID.
 */
function unregister_sidebar_widget($id) {
	return wp_unregister_sidebar_widget($id);
}

/**
 * Registers widget control callback for customizing options.
 *
 * Allows $name to be an array that accepts either three elements to grab the
 * first element and the third for the name or just uses the first element of
 * the array for the name.
 *
 * Passes to {@link wp_register_widget_control()} after the argument list has
 * been compiled.
 *
 * @since 2.2.0
 *
 * @param int|string $name Sidebar ID.
 * @param callback $control_callback Widget control callback to display and process form.
 * @param int $width Widget width.
 * @param int $height Widget height.
 */
function register_widget_control($name, $control_callback, $width = '', $height = '') {
	// Compat
	if ( is_array($name) ) {
		if ( count($name) == 3 )
			$name = sprintf($name[0], $name[2]);
		else
			$name = $name[0];
	}

	$id = sanitize_title($name);
	$options = array();
	if ( !empty($width) )
		$options['width'] = $width;
	if ( !empty($height) )
		$options['height'] = $height;
	$params = array_slice(func_get_args(), 4);
	$args = array($id, $name, $control_callback, $options);
	if ( !empty($params) )
		$args = array_merge($args, $params);

	call_user_func_array('wp_register_widget_control', $args);
}

/**
 * Alias of {@link wp_unregister_widget_control()}.
 *
 * @since 2.2.0
 * @see wp_unregister_widget_control()
 *
 * @param int|string $id Widget ID.
 */
function unregister_widget_control($id) {
	return wp_unregister_widget_control($id);
}