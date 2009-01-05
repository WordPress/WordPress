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
global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls;

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
	global $wp_registered_widget_controls;

	$id = strtolower($id);

	if ( empty($control_callback) ) {
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
		'callback' => $control_callback,
		'params' => array_slice(func_get_args(), 4)
	);
	$widget = array_merge($widget, $options);

	$wp_registered_widget_controls[$id] = $widget;
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
 * Will only check if both parameters are used. Used to find which sidebar the
 * widget is located in, but requires that both the callback and the widget ID
 * be known.
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

/* Default Widgets */

/**
 * Display pages widget.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 */
function wp_widget_pages( $args ) {
	extract( $args );
	$options = get_option( 'widget_pages' );

	$title = empty( $options['title'] ) ? __( 'Pages' ) : apply_filters('widget_title', $options['title']);
	$sortby = empty( $options['sortby'] ) ? 'menu_order' : $options['sortby'];
	$exclude = empty( $options['exclude'] ) ? '' : $options['exclude'];

	if ( $sortby == 'menu_order' ) {
		$sortby = 'menu_order, post_title';
	}

	$out = wp_list_pages( array('title_li' => '', 'echo' => 0, 'sort_column' => $sortby, 'exclude' => $exclude) );

	if ( !empty( $out ) ) {
?>
	<?php echo $before_widget; ?>
		<?php echo $before_title . $title . $after_title; ?>
		<ul>
			<?php echo $out; ?>
		</ul>
	<?php echo $after_widget; ?>
<?php
	}
}

/**
 * Display and process pages widget options form.
 *
 * @since 2.2.0
 */
function wp_widget_pages_control() {
	$options = $newoptions = get_option('widget_pages');
	if ( isset($_POST['pages-submit']) ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['pages-title']));

		$sortby = stripslashes( $_POST['pages-sortby'] );

		if ( in_array( $sortby, array( 'post_title', 'menu_order', 'ID' ) ) ) {
			$newoptions['sortby'] = $sortby;
		} else {
			$newoptions['sortby'] = 'menu_order';
		}

		$newoptions['exclude'] = strip_tags( stripslashes( $_POST['pages-exclude'] ) );
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_pages', $options);
	}
	$title = attribute_escape($options['title']);
	$exclude = attribute_escape( $options['exclude'] );
?>
		<p><label for="pages-title"><?php _e('Title:'); ?> <input class="widefat" id="pages-title" name="pages-title" type="text" value="<?php echo $title; ?>" /></label></p>
		<p>
			<label for="pages-sortby"><?php _e( 'Sort by:' ); ?>
				<select name="pages-sortby" id="pages-sortby" class="widefat">
					<option value="post_title"<?php selected( $options['sortby'], 'post_title' ); ?>><?php _e('Page title'); ?></option>
					<option value="menu_order"<?php selected( $options['sortby'], 'menu_order' ); ?>><?php _e('Page order'); ?></option>
					<option value="ID"<?php selected( $options['sortby'], 'ID' ); ?>><?php _e( 'Page ID' ); ?></option>
				</select>
			</label>
		</p>
		<p>
			<label for="pages-exclude"><?php _e( 'Exclude:' ); ?> <input type="text" value="<?php echo $exclude; ?>" name="pages-exclude" id="pages-exclude" class="widefat" /></label>
			<br />
			<small><?php _e( 'Page IDs, separated by commas.' ); ?></small>
		</p>
		<input type="hidden" id="pages-submit" name="pages-submit" value="1" />
<?php
}

/**
 * Display links widget.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 */
function wp_widget_links($args) {
	extract($args, EXTR_SKIP);

	$before_widget = preg_replace('/id="[^"]*"/','id="%id"', $before_widget);
	wp_list_bookmarks(apply_filters('widget_links_args', array(
		'title_before' => $before_title, 'title_after' => $after_title,
		'category_before' => $before_widget, 'category_after' => $after_widget,
		'show_images' => true, 'class' => 'linkcat widget'
	)));
}

/**
 * Display search widget.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 */
function wp_widget_search($args) {
	extract($args);
	echo $before_widget;

	// Use current theme search form if it exists
	get_search_form();

	echo $after_widget;
}

/**
 * Display archives widget.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 */
function wp_widget_archives($args) {
	extract($args);
	$options = get_option('widget_archives');
	$c = $options['count'] ? '1' : '0';
	$d = $options['dropdown'] ? '1' : '0';
	$title = empty($options['title']) ? __('Archives') : apply_filters('widget_title', $options['title']);

	echo $before_widget;
	echo $before_title . $title . $after_title;

	if($d) {
?>
		<select name="archive-dropdown" onchange='document.location.href=this.options[this.selectedIndex].value;'> <option value=""><?php echo attribute_escape(__('Select Month')); ?></option> <?php wp_get_archives("type=monthly&format=option&show_post_count=$c"); ?> </select>
<?php
	} else {
?>
		<ul>
		<?php wp_get_archives("type=monthly&show_post_count=$c"); ?>
		</ul>
<?php
	}

	echo $after_widget;
}

/**
 * Display and process archives widget options form.
 *
 * @since 2.2.0
 */
function wp_widget_archives_control() {
	$options = $newoptions = get_option('widget_archives');
	if ( isset($_POST["archives-submit"]) ) {
		$newoptions['count'] = isset($_POST['archives-count']);
		$newoptions['dropdown'] = isset($_POST['archives-dropdown']);
		$newoptions['title'] = strip_tags(stripslashes($_POST["archives-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_archives', $options);
	}
	$count = $options['count'] ? 'checked="checked"' : '';
	$dropdown = $options['dropdown'] ? 'checked="checked"' : '';
	$title = attribute_escape($options['title']);
?>
			<p><label for="archives-title"><?php _e('Title:'); ?> <input class="widefat" id="archives-title" name="archives-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p>
				<label for="archives-count"><input class="checkbox" type="checkbox" <?php echo $count; ?> id="archives-count" name="archives-count" /> <?php _e('Show post counts'); ?></label>
				<br />
				<label for="archives-dropdown"><input class="checkbox" type="checkbox" <?php echo $dropdown; ?> id="archives-dropdown" name="archives-dropdown" /> <?php _e('Display as a drop down'); ?></label>
			</p>
			<input type="hidden" id="archives-submit" name="archives-submit" value="1" />
<?php
}

/**
 * Display meta widget.
 *
 * Displays log in/out, RSS feed links, etc.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 */
function wp_widget_meta($args) {
	extract($args);
	$options = get_option('widget_meta');
	$title = empty($options['title']) ? __('Meta') : apply_filters('widget_title', $options['title']);
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php echo attribute_escape(__('Syndicate this site using RSS 2.0')); ?>"><?php _e('Entries <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php echo attribute_escape(__('The latest comments to all posts in RSS')); ?>"><?php _e('Comments <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
			<li><a href="http://wordpress.org/" title="<?php echo attribute_escape(__('Powered by WordPress, state-of-the-art semantic personal publishing platform.')); ?>">WordPress.org</a></li>
			<?php wp_meta(); ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
}

/**
 * Display and process meta widget options form.
 *
 * @since 2.2.0
 */
function wp_widget_meta_control() {
	$options = $newoptions = get_option('widget_meta');
	if ( isset($_POST["meta-submit"]) ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["meta-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_meta', $options);
	}
	$title = attribute_escape($options['title']);
?>
			<p><label for="meta-title"><?php _e('Title:'); ?> <input class="widefat" id="meta-title" name="meta-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="meta-submit" name="meta-submit" value="1" />
<?php
}

/**
 * Display calendar widget.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 */
function wp_widget_calendar($args) {
	extract($args);
	$options = get_option('widget_calendar');
	$title = apply_filters('widget_title', $options['title']);
	if ( empty($title) )
		$title = '&nbsp;';
	echo $before_widget . $before_title . $title . $after_title;
	echo '<div id="calendar_wrap">';
	get_calendar();
	echo '</div>';
	echo $after_widget;
}

/**
 * Display and process calendar widget options form.
 *
 * @since 2.2.0
 */
function wp_widget_calendar_control() {
	$options = $newoptions = get_option('widget_calendar');
	if ( isset($_POST["calendar-submit"]) ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["calendar-title"]));
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_calendar', $options);
	}
	$title = attribute_escape($options['title']);
?>
			<p><label for="calendar-title"><?php _e('Title:'); ?> <input class="widefat" id="calendar-title" name="calendar-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="calendar-submit" name="calendar-submit" value="1" />
<?php
}

/**
 * Display the Text widget, depending on the widget number.
 *
 * Supports multiple text widgets and keeps track of the widget number by using
 * the $widget_args parameter. The option 'widget_text' is used to store the
 * content for the widgets. The content and title are passed through the
 * 'widget_text' and 'widget_title' filters respectively.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 * @param int $number Widget number.
 */
function wp_widget_text($args, $widget_args = 1) {
	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('widget_text');
	if ( !isset($options[$number]) )
		return;

	$title = apply_filters('widget_title', $options[$number]['title']);
	$text = apply_filters( 'widget_text', $options[$number]['text'] );
?>
		<?php echo $before_widget; ?>
			<?php if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><?php echo $text; ?></div>
		<?php echo $after_widget; ?>
<?php
}

/**
 * Display and process text widget options form.
 *
 * @since 2.2.0
 *
 * @param int $widget_args Widget number.
 */
function wp_widget_text_control($widget_args) {
	global $wp_registered_widgets;
	static $updated = false;

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('widget_text');
	if ( !is_array($options) )
		$options = array();

	if ( !$updated && !empty($_POST['sidebar']) ) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( (array) $this_sidebar as $_widget_id ) {
			if ( 'wp_widget_text' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "text-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed.
					unset($options[$widget_number]);
			}
		}

		foreach ( (array) $_POST['widget-text'] as $widget_number => $widget_text ) {
			if ( !isset($widget_text['text']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;
			$title = strip_tags(stripslashes($widget_text['title']));
			if ( current_user_can('unfiltered_html') )
				$text = stripslashes( $widget_text['text'] );
			else
				$text = stripslashes(wp_filter_post_kses( $widget_text['text'] ));
			$options[$widget_number] = compact( 'title', 'text' );
		}

		update_option('widget_text', $options);
		$updated = true;
	}

	if ( -1 == $number ) {
		$title = '';
		$text = '';
		$number = '%i%';
	} else {
		$title = attribute_escape($options[$number]['title']);
		$text = format_to_edit($options[$number]['text']);
	}
?>
		<p>
			<input class="widefat" id="text-title-<?php echo $number; ?>" name="widget-text[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
			<textarea class="widefat" rows="16" cols="20" id="text-text-<?php echo $number; ?>" name="widget-text[<?php echo $number; ?>][text]"><?php echo $text; ?></textarea>
			<input type="hidden" name="widget-text[<?php echo $number; ?>][submit]" value="1" />
		</p>
<?php
}

/**
 * Register text widget on startup.
 *
 * @since 2.2.0
 */
function wp_widget_text_register() {
	if ( !$options = get_option('widget_text') )
		$options = array();
	$widget_ops = array('classname' => 'widget_text', 'description' => __('Arbitrary text or HTML'));
	$control_ops = array('width' => 400, 'height' => 350, 'id_base' => 'text');
	$name = __('Text');

	$id = false;
	foreach ( (array) array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['title']) || !isset($options[$o]['text']) )
			continue;
		$id = "text-$o"; // Never never never translate an id
		wp_register_sidebar_widget($id, $name, 'wp_widget_text', $widget_ops, array( 'number' => $o ));
		wp_register_widget_control($id, $name, 'wp_widget_text_control', $control_ops, array( 'number' => $o ));
	}

	// If there are none, we register the widget's existance with a generic template
	if ( !$id ) {
		wp_register_sidebar_widget( 'text-1', $name, 'wp_widget_text', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'text-1', $name, 'wp_widget_text_control', $control_ops, array( 'number' => -1 ) );
	}
}

/**
 * Display categories widget.
 *
 * Allows multiple category widgets.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 * @param int $number Widget number.
 */
function wp_widget_categories($args, $widget_args = 1) {
	extract($args, EXTR_SKIP);
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract($widget_args, EXTR_SKIP);

	$options = get_option('widget_categories');
	if ( !isset($options[$number]) )
		return;

	$c = $options[$number]['count'] ? '1' : '0';
	$h = $options[$number]['hierarchical'] ? '1' : '0';
	$d = $options[$number]['dropdown'] ? '1' : '0';

	$title = empty($options[$number]['title']) ? __('Categories') : apply_filters('widget_title', $options[$number]['title']);

	echo $before_widget;
	echo $before_title . $title . $after_title;

	$cat_args = array('orderby' => 'name', 'show_count' => $c, 'hierarchical' => $h);

	if ( $d ) {
		$cat_args['show_option_none'] = __('Select Category');
		wp_dropdown_categories($cat_args);
?>

<script type='text/javascript'>
/* <![CDATA[ */
	var dropdown = document.getElementById("cat");
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value > 0 ) {
			location.href = "<?php echo get_option('home'); ?>/?cat="+dropdown.options[dropdown.selectedIndex].value;
		}
	}
	dropdown.onchange = onCatChange;
/* ]]> */
</script>

<?php
	} else {
?>
		<ul>
		<?php
			$cat_args['title_li'] = '';
			wp_list_categories($cat_args);
		?>
		</ul>
<?php
	}

	echo $after_widget;
}

/**
 * Display and process categories widget options form.
 *
 * @since 2.2.0
 *
 * @param int $widget_args Widget number.
 */
function wp_widget_categories_control( $widget_args ) {
	global $wp_registered_widgets;
	static $updated = false;

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract($widget_args, EXTR_SKIP);

	$options = get_option('widget_categories');

	if ( !is_array( $options ) )
		$options = array();

	if ( !$updated && !empty($_POST['sidebar']) ) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( (array) $this_sidebar as $_widget_id ) {
			if ( 'wp_widget_categories' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "categories-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed.
					unset($options[$widget_number]);
			}
		}

		foreach ( (array) $_POST['widget-categories'] as $widget_number => $widget_cat ) {
			if ( !isset($widget_cat['title']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;
			$title = trim(strip_tags(stripslashes($widget_cat['title'])));
			$count = isset($widget_cat['count']);
			$hierarchical = isset($widget_cat['hierarchical']);
			$dropdown = isset($widget_cat['dropdown']);
			$options[$widget_number] = compact( 'title', 'count', 'hierarchical', 'dropdown' );
		}

		update_option('widget_categories', $options);
		$updated = true;
	}

	if ( -1 == $number ) {
		$title = '';
		$count = false;
		$hierarchical = false;
		$dropdown = false;
		$number = '%i%';
	} else {
		$title = attribute_escape( $options[$number]['title'] );
		$count = (bool) $options[$number]['count'];
		$hierarchical = (bool) $options[$number]['hierarchical'];
		$dropdown = (bool) $options[$number]['dropdown'];
	}
?>
			<p>
				<label for="categories-title-<?php echo $number; ?>">
					<?php _e( 'Title:' ); ?>
					<input class="widefat" id="categories-title-<?php echo $number; ?>" name="widget-categories[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
				</label>
			</p>

			<p>
				<label for="categories-dropdown-<?php echo $number; ?>">
					<input type="checkbox" class="checkbox" id="categories-dropdown-<?php echo $number; ?>" name="widget-categories[<?php echo $number; ?>][dropdown]"<?php checked( $dropdown, true ); ?> />
					<?php _e( 'Show as dropdown' ); ?>
				</label>
				<br />
				<label for="categories-count-<?php echo $number; ?>">
					<input type="checkbox" class="checkbox" id="categories-count-<?php echo $number; ?>" name="widget-categories[<?php echo $number; ?>][count]"<?php checked( $count, true ); ?> />
					<?php _e( 'Show post counts' ); ?>
				</label>
				<br />
				<label for="categories-hierarchical-<?php echo $number; ?>">
					<input type="checkbox" class="checkbox" id="categories-hierarchical-<?php echo $number; ?>" name="widget-categories[<?php echo $number; ?>][hierarchical]"<?php checked( $hierarchical, true ); ?> />
					<?php _e( 'Show hierarchy' ); ?>
				</label>
			</p>

			<input type="hidden" name="widget-categories[<?php echo $number; ?>][submit]" value="1" />
<?php
}

/**
 * Register categories widget on startup.
 *
 * @since 2.3.0
 */
function wp_widget_categories_register() {
	if ( !$options = get_option( 'widget_categories' ) )
		$options = array();

	if ( isset($options['title']) )
		$options = wp_widget_categories_upgrade();

	$widget_ops = array( 'classname' => 'widget_categories', 'description' => __( "A list or dropdown of categories" ) );

	$name = __( 'Categories' );

	$id = false;
	foreach ( (array) array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['title']) )
			continue;
		$id = "categories-$o";
		wp_register_sidebar_widget( $id, $name, 'wp_widget_categories', $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, 'wp_widget_categories_control', array( 'id_base' => 'categories' ), array( 'number' => $o ) );
	}

	// If there are none, we register the widget's existance with a generic template
	if ( !$id ) {
		wp_register_sidebar_widget( 'categories-1', $name, 'wp_widget_categories', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'categories-1', $name, 'wp_widget_categories_control', array( 'id_base' => 'categories' ), array( 'number' => -1 ) );
	}
}

/**
 * Upgrade previous category widget to current version.
 *
 * @since 2.3.0
 *
 * @return array
 */
function wp_widget_categories_upgrade() {
	$options = get_option( 'widget_categories' );

	if ( !isset( $options['title'] ) )
		return $options;

	$newoptions = array( 1 => $options );

	update_option( 'widget_categories', $newoptions );

	$sidebars_widgets = get_option( 'sidebars_widgets' );
	if ( is_array( $sidebars_widgets ) ) {
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( is_array( $widgets ) ) {
				foreach ( $widgets as $widget )
					$new_widgets[$sidebar][] = ( $widget == 'categories' ) ? 'categories-1' : $widget;
			} else {
				$new_widgets[$sidebar] = $widgets;
			}
		}
		if ( $new_widgets != $sidebars_widgets )
			update_option( 'sidebars_widgets', $new_widgets );
	}

	return $newoptions;
}

/**
 * Display recent entries widget.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 * @return int Displayed cache.
 */
function wp_widget_recent_entries($args) {
	if ( '%BEG_OF_TITLE%' != $args['before_title'] ) {
		if ( $output = wp_cache_get('widget_recent_entries', 'widget') )
			return print($output);
		ob_start();
	}

	extract($args);
	$options = get_option('widget_recent_entries');
	$title = empty($options['title']) ? __('Recent Posts') : apply_filters('widget_title', $options['title']);
	if ( !$number = (int) $options['number'] )
		$number = 10;
	else if ( $number < 1 )
		$number = 1;
	else if ( $number > 15 )
		$number = 15;

	$r = new WP_Query(array('showposts' => $number, 'what_to_show' => 'posts', 'nopaging' => 0, 'post_status' => 'publish', 'caller_get_posts' => 1));
	if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul>
			<?php  while ($r->have_posts()) : $r->the_post(); ?>
			<li><a href="<?php the_permalink() ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></li>
			<?php endwhile; ?>
			</ul>
		<?php echo $after_widget; ?>
<?php
		wp_reset_query();  // Restore global post data stomped by the_post().
	endif;

	if ( '%BEG_OF_TITLE%' != $args['before_title'] )
		wp_cache_add('widget_recent_entries', ob_get_flush(), 'widget');
}

/**
 * Remove recent entries widget items cache.
 *
 * @since 2.2.0
 */
function wp_flush_widget_recent_entries() {
	wp_cache_delete('widget_recent_entries', 'widget');
}

add_action('save_post', 'wp_flush_widget_recent_entries');
add_action('deleted_post', 'wp_flush_widget_recent_entries');
add_action('switch_theme', 'wp_flush_widget_recent_entries');

/**
 * Display and process recent entries widget options form.
 *
 * @since 2.2.0
 */
function wp_widget_recent_entries_control() {
	$options = $newoptions = get_option('widget_recent_entries');
	if ( isset($_POST["recent-entries-submit"]) ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["recent-entries-title"]));
		$newoptions['number'] = (int) $_POST["recent-entries-number"];
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_recent_entries', $options);
		wp_flush_widget_recent_entries();
	}
	$title = attribute_escape($options['title']);
	if ( !$number = (int) $options['number'] )
		$number = 5;
?>

			<p><label for="recent-entries-title"><?php _e('Title:'); ?> <input class="widefat" id="recent-entries-title" name="recent-entries-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p>
				<label for="recent-entries-number"><?php _e('Number of posts to show:'); ?> <input style="width: 25px; text-align: center;" id="recent-entries-number" name="recent-entries-number" type="text" value="<?php echo $number; ?>" /></label>
				<br />
				<small><?php _e('(at most 15)'); ?></small>
			</p>
			<input type="hidden" id="recent-entries-submit" name="recent-entries-submit" value="1" />
<?php
}

/**
 * Display recent comments widget.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 */
function wp_widget_recent_comments($args) {
	global $wpdb, $comments, $comment;
	extract($args, EXTR_SKIP);
	$options = get_option('widget_recent_comments');
	$title = empty($options['title']) ? __('Recent Comments') : apply_filters('widget_title', $options['title']);
	if ( !$number = (int) $options['number'] )
		$number = 5;
	else if ( $number < 1 )
		$number = 1;
	else if ( $number > 15 )
		$number = 15;

	if ( !$comments = wp_cache_get( 'recent_comments', 'widget' ) ) {
		$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT $number");
		wp_cache_add( 'recent_comments', $comments, 'widget' );
	}
?>

		<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
			<ul id="recentcomments"><?php
			if ( $comments ) : foreach ( (array) $comments as $comment) :
			echo  '<li class="recentcomments">' . sprintf(__('%1$s on %2$s'), get_comment_author_link(), '<a href="' . clean_url( get_comment_link($comment->comment_ID) ) . '">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
			endforeach; endif;?></ul>
		<?php echo $after_widget; ?>
<?php
}

/**
 * Remove the cache for recent comments widget.
 *
 * @since 2.2.0
 */
function wp_delete_recent_comments_cache() {
	wp_cache_delete( 'recent_comments', 'widget' );
}
add_action( 'comment_post', 'wp_delete_recent_comments_cache' );
add_action( 'wp_set_comment_status', 'wp_delete_recent_comments_cache' );

/**
 * Display and process recent comments widget options form.
 *
 * @since 2.2.0
 */
function wp_widget_recent_comments_control() {
	$options = $newoptions = get_option('widget_recent_comments');
	if ( isset($_POST["recent-comments-submit"]) ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["recent-comments-title"]));
		$newoptions['number'] = (int) $_POST["recent-comments-number"];
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_recent_comments', $options);
		wp_delete_recent_comments_cache();
	}
	$title = attribute_escape($options['title']);
	if ( !$number = (int) $options['number'] )
		$number = 5;
?>
			<p><label for="recent-comments-title"><?php _e('Title:'); ?> <input class="widefat" id="recent-comments-title" name="recent-comments-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p>
				<label for="recent-comments-number"><?php _e('Number of comments to show:'); ?> <input style="width: 25px; text-align: center;" id="recent-comments-number" name="recent-comments-number" type="text" value="<?php echo $number; ?>" /></label>
				<br />
				<small><?php _e('(at most 15)'); ?></small>
			</p>
			<input type="hidden" id="recent-comments-submit" name="recent-comments-submit" value="1" />
<?php
}

/**
 * Display the style for recent comments widget.
 *
 * @since 2.2.0
 */
function wp_widget_recent_comments_style() {
?>
<style type="text/css">.recentcomments a{display:inline !important;padding: 0 !important;margin: 0 !important;}</style>
<?php
}

/**
 * Register recent comments with control and hook for 'wp_head' action.
 *
 * @since 2.2.0
 */
function wp_widget_recent_comments_register() {
	$widget_ops = array('classname' => 'widget_recent_comments', 'description' => __( 'The most recent comments' ) );
	wp_register_sidebar_widget('recent-comments', __('Recent Comments'), 'wp_widget_recent_comments', $widget_ops);
	wp_register_widget_control('recent-comments', __('Recent Comments'), 'wp_widget_recent_comments_control');

	if ( is_active_widget('wp_widget_recent_comments') )
		add_action('wp_head', 'wp_widget_recent_comments_style');
}

/**
 * Display RSS widget.
 *
 * Allows for multiple widgets to be displayed.
 *
 * @since 2.2.0
 *
 * @param array $args Widget arguments.
 * @param int $number Widget number.
 */
function wp_widget_rss($args, $widget_args = 1) {
	extract($args, EXTR_SKIP);
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract($widget_args, EXTR_SKIP);

	$options = get_option('widget_rss');

	if ( !isset($options[$number]) )
		return;

	if ( isset($options[$number]['error']) && $options[$number]['error'] )
		return;

	$url = $options[$number]['url'];
	while ( strstr($url, 'http') != $url )
		$url = substr($url, 1);
	if ( empty($url) )
		return;

	require_once(ABSPATH . WPINC . '/rss.php');

	$rss = fetch_rss($url);
	$link = clean_url(strip_tags($rss->channel['link']));
	while ( strstr($link, 'http') != $link )
		$link = substr($link, 1);
	$desc = attribute_escape(strip_tags(html_entity_decode($rss->channel['description'], ENT_QUOTES)));
	$title = $options[$number]['title'];
	if ( empty($title) )
		$title = htmlentities(strip_tags($rss->channel['title']));
	if ( empty($title) )
		$title = $desc;
	if ( empty($title) )
		$title = __('Unknown Feed');
	$title = apply_filters('widget_title', $title );
	$url = clean_url(strip_tags($url));
	if ( file_exists(dirname(__FILE__) . '/rss.png') )
		$icon = str_replace(ABSPATH, site_url() . '/', dirname(__FILE__)) . '/rss.png';
	else
		$icon = includes_url('images/rss.png');
	$title = "<a class='rsswidget' href='$url' title='" . attribute_escape(__('Syndicate this content')) ."'><img style='background:orange;color:white;border:none;' width='14' height='14' src='$icon' alt='RSS' /></a> <a class='rsswidget' href='$link' title='$desc'>$title</a>";

	echo $before_widget;
	echo $before_title . $title . $after_title;

	wp_widget_rss_output( $rss, $options[$number] );

	echo $after_widget;
}

/**
 * Display the RSS entries in a list.
 *
 * @since 2.5.0
 *
 * @param string|array|object $rss RSS url.
 * @param array $args Widget arguments.
 */
function wp_widget_rss_output( $rss, $args = array() ) {
	if ( is_string( $rss ) ) {
		require_once(ABSPATH . WPINC . '/rss.php');
		if ( !$rss = fetch_rss($rss) )
			return;
	} elseif ( is_array($rss) && isset($rss['url']) ) {
		require_once(ABSPATH . WPINC . '/rss.php');
		$args = $rss;
		if ( !$rss = fetch_rss($rss['url']) )
			return;
	} elseif ( !is_object($rss) ) {
		return;
	}

	$default_args = array( 'show_author' => 0, 'show_date' => 0, 'show_summary' => 0 );
	$args = wp_parse_args( $args, $default_args );
	extract( $args, EXTR_SKIP );

	$items = (int) $items;
	if ( $items < 1 || 20 < $items )
		$items = 10;
	$show_summary  = (int) $show_summary;
	$show_author   = (int) $show_author;
	$show_date     = (int) $show_date;

	if ( is_array( $rss->items ) && !empty( $rss->items ) ) {
		$rss->items = array_slice($rss->items, 0, $items);
		echo '<ul>';
		foreach ( (array) $rss->items as $item ) {
			while ( strstr($item['link'], 'http') != $item['link'] )
				$item['link'] = substr($item['link'], 1);
			$link = clean_url(strip_tags($item['link']));
			$title = attribute_escape(strip_tags($item['title']));
			if ( empty($title) )
				$title = __('Untitled');
			$desc = '';
			if ( isset( $item['description'] ) && is_string( $item['description'] ) )
				$desc = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($item['description'], ENT_QUOTES))));
			elseif ( isset( $item['summary'] ) && is_string( $item['summary'] ) )
				$desc = str_replace(array("\n", "\r"), ' ', attribute_escape(strip_tags(html_entity_decode($item['summary'], ENT_QUOTES))));
			if ( 360 < strlen( $desc ) )
				$desc = wp_html_excerpt( $desc, 360 ) . ' [&hellip;]';
			$summary = $desc;

			if ( $show_summary ) {
				$desc = '';
				$summary = wp_specialchars( $summary );
				$summary = "<div class='rssSummary'>$summary</div>";
			} else {
				$summary = '';
			}

			$date = '';
			if ( $show_date ) {
				if ( isset($item['pubdate']) )
					$date = $item['pubdate'];
				elseif ( isset($item['published']) )
					$date = $item['published'];

				if ( $date ) {
					if ( $date_stamp = strtotime( $date ) )
						$date = ' <span class="rss-date">' . date_i18n( get_option( 'date_format' ), $date_stamp ) . '</span>';
					else
						$date = '';
				}
			}

			$author = '';
			if ( $show_author ) {
				if ( isset($item['dc']['creator']) )
					$author = ' <cite>' . wp_specialchars( strip_tags( $item['dc']['creator'] ) ) . '</cite>';
				elseif ( isset($item['author_name']) )
					$author = ' <cite>' . wp_specialchars( strip_tags( $item['author_name'] ) ) . '</cite>';
			}

			if ( $link == '' ) {
				echo "<li>$title{$date}{$summary}{$author}</li>";
			} else {
				echo "<li><a class='rsswidget' href='$link' title='$desc'>$title</a>{$date}{$summary}{$author}</li>";
			}
}
		echo '</ul>';
	} else {
		echo '<ul><li>' . __( 'An error has occurred; the feed is probably down. Try again later.' ) . '</li></ul>';
	}
}

/**
 * Display and process RSS widget control form.
 *
 * @since 2.2.0
 *
 * @param int $widget_args Widget number.
 */
function wp_widget_rss_control($widget_args) {
	global $wp_registered_widgets;
	static $updated = false;

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract($widget_args, EXTR_SKIP);

	$options = get_option('widget_rss');
	if ( !is_array($options) )
		$options = array();

	$urls = array();
	foreach ( (array) $options as $option )
		if ( isset($option['url']) )
			$urls[$option['url']] = true;

	if ( !$updated && 'POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['sidebar']) ) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( (array) $this_sidebar as $_widget_id ) {
			if ( 'wp_widget_rss' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "rss-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed.
					unset($options[$widget_number]);
			}
		}

		foreach( (array) $_POST['widget-rss'] as $widget_number => $widget_rss ) {
			if ( !isset($widget_rss['url']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;
			$widget_rss = stripslashes_deep( $widget_rss );
			$url = sanitize_url(strip_tags($widget_rss['url']));
			$options[$widget_number] = wp_widget_rss_process( $widget_rss, !isset($urls[$url]) );
		}

		update_option('widget_rss', $options);
		$updated = true;
	}

	if ( -1 == $number ) {
		$title = '';
		$url = '';
		$items = 10;
		$error = false;
		$number = '%i%';
		$show_summary = 0;
		$show_author = 0;
		$show_date = 0;
	} else {
		extract( (array) $options[$number] );
	}

	wp_widget_rss_form( compact( 'number', 'title', 'url', 'items', 'error', 'show_summary', 'show_author', 'show_date' ) );
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
 * @param array|string $args Values for input fields.
 * @param array $inputs Override default display options.
 */
function wp_widget_rss_form( $args, $inputs = null ) {

	$default_inputs = array( 'url' => true, 'title' => true, 'items' => true, 'show_summary' => true, 'show_author' => true, 'show_date' => true );
	$inputs = wp_parse_args( $inputs, $default_inputs );
	extract( $args );
	extract( $inputs, EXTR_SKIP);

	$number = attribute_escape( $number );
	$title  = attribute_escape( $title );
	$url    = attribute_escape( $url );
	$items  = (int) $items;
	if ( $items < 1 || 20 < $items )
		$items  = 10;
	$show_summary   = (int) $show_summary;
	$show_author    = (int) $show_author;
	$show_date      = (int) $show_date;

	if ( $inputs['url'] ) :
?>
	<p>
		<label for="rss-url-<?php echo $number; ?>"><?php _e('Enter the RSS feed URL here:'); ?>
			<input class="widefat" id="rss-url-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][url]" type="text" value="<?php echo $url; ?>" />
		</label>
	</p>
<?php endif; if ( $inputs['title'] ) : ?>
	<p>
		<label for="rss-title-<?php echo $number; ?>"><?php _e('Give the feed a title (optional):'); ?>
			<input class="widefat" id="rss-title-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][title]" type="text" value="<?php echo $title; ?>" />
		</label>
	</p>
<?php endif; if ( $inputs['items'] ) : ?>
	<p>
		<label for="rss-items-<?php echo $number; ?>"><?php _e('How many items would you like to display?'); ?>
			<select id="rss-items-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][items]">
				<?php
					for ( $i = 1; $i <= 20; ++$i )
						echo "<option value='$i' " . ( $items == $i ? "selected='selected'" : '' ) . ">$i</option>";
				?>
			</select>
		</label>
	</p>
<?php endif; if ( $inputs['show_summary'] ) : ?>
	<p>
		<label for="rss-show-summary-<?php echo $number; ?>">
			<input id="rss-show-summary-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][show_summary]" type="checkbox" value="1" <?php if ( $show_summary ) echo 'checked="checked"'; ?>/>
			<?php _e('Display item content?'); ?>
		</label>
	</p>
<?php endif; if ( $inputs['show_author'] ) : ?>
	<p>
		<label for="rss-show-author-<?php echo $number; ?>">
			<input id="rss-show-author-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][show_author]" type="checkbox" value="1" <?php if ( $show_author ) echo 'checked="checked"'; ?>/>
			<?php _e('Display item author if available?'); ?>
		</label>
	</p>
<?php endif; if ( $inputs['show_date'] ) : ?>
	<p>
		<label for="rss-show-date-<?php echo $number; ?>">
			<input id="rss-show-date-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][show_date]" type="checkbox" value="1" <?php if ( $show_date ) echo 'checked="checked"'; ?>/>
			<?php _e('Display item date?'); ?>
		</label>
	</p>
	<input type="hidden" name="widget-rss[<?php echo $number; ?>][submit]" value="1" />
<?php
	endif;
	foreach ( array_keys($default_inputs) as $input ) :
		if ( 'hidden' === $inputs[$input] ) :
			$id = str_replace( '_', '-', $input );
?>
	<input type="hidden" id="rss-<?php echo $id; ?>-<?php echo $number; ?>" name="widget-rss[<?php echo $number; ?>][<?php echo $input; ?>]" value="<?php echo $$input; ?>" />
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
 * @param bool $check_feed Optional, default is true. Whether to check feed for errors.
 * @return array
 */
function wp_widget_rss_process( $widget_rss, $check_feed = true ) {
	$items = (int) $widget_rss['items'];
	if ( $items < 1 || 20 < $items )
		$items = 10;
	$url           = sanitize_url(strip_tags( $widget_rss['url'] ));
	$title         = trim(strip_tags( $widget_rss['title'] ));
	$show_summary  = (int) $widget_rss['show_summary'];
	$show_author   = (int) $widget_rss['show_author'];
	$show_date     = (int) $widget_rss['show_date'];

	if ( $check_feed ) {
		require_once(ABSPATH . WPINC . '/rss.php');
		$rss = fetch_rss($url);
		$error = false;
		$link = '';
		if ( !is_object($rss) ) {
			$url = wp_specialchars(__('Error: could not find an RSS or ATOM feed at that URL.'), 1);
			$error = sprintf(__('Error in RSS %1$d'), $widget_number );
		} else {
			$link = clean_url(strip_tags($rss->channel['link']));
			while ( strstr($link, 'http') != $link )
				$link = substr($link, 1);
		}
	}

	return compact( 'title', 'url', 'link', 'items', 'error', 'show_summary', 'show_author', 'show_date' );
}

/**
 * Register RSS widget to allow multiple RSS widgets on startup.
 *
 * @since 2.2.0
 */
function wp_widget_rss_register() {
	if ( !$options = get_option('widget_rss') )
		$options = array();
	$widget_ops = array('classname' => 'widget_rss', 'description' => __( 'Entries from any RSS or Atom feed' ));
	$control_ops = array('width' => 400, 'height' => 200, 'id_base' => 'rss');
	$name = __('RSS');

	$id = false;
	foreach ( (array) array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['url']) || !isset($options[$o]['title']) || !isset($options[$o]['items']) )
			continue;
		$id = "rss-$o"; // Never never never translate an id
		wp_register_sidebar_widget($id, $name, 'wp_widget_rss', $widget_ops, array( 'number' => $o ));
		wp_register_widget_control($id, $name, 'wp_widget_rss_control', $control_ops, array( 'number' => $o ));
	}

	// If there are none, we register the widget's existance with a generic template
	if ( !$id ) {
		wp_register_sidebar_widget( 'rss-1', $name, 'wp_widget_rss', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'rss-1', $name, 'wp_widget_rss_control', $control_ops, array( 'number' => -1 ) );
	}
}

/**
 * Display tag cloud widget.
 *
 * @since 2.3.0
 *
 * @param array $args Widget arguments.
 */
function wp_widget_tag_cloud($args) {
	extract($args);
	$options = get_option('widget_tag_cloud');
	$title = empty($options['title']) ? __('Tags') : apply_filters('widget_title', $options['title']);

	echo $before_widget;
	echo $before_title . $title . $after_title;
	wp_tag_cloud();
	echo $after_widget;
}

/**
 * Manage WordPress Tag Cloud widget options.
 *
 * Displays management form for changing the tag cloud widget title.
 *
 * @since 2.3.0
 */
function wp_widget_tag_cloud_control() {
	$options = $newoptions = get_option('widget_tag_cloud');

	if ( isset($_POST['tag-cloud-submit']) ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['tag-cloud-title']));
	}

	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_tag_cloud', $options);
	}

	$title = attribute_escape( $options['title'] );
?>
	<p><label for="tag-cloud-title">
	<?php _e('Title:') ?> <input type="text" class="widefat" id="tag-cloud-title" name="tag-cloud-title" value="<?php echo $title ?>" /></label>
	</p>
	<input type="hidden" name="tag-cloud-submit" id="tag-cloud-submit" value="1" />
<?php
}

/**
 * Register all of the default WordPress widgets on startup.
 *
 * Calls 'widgets_init' action after all of the WordPress widgets have been
 * registered.
 *
 * @since 2.2.0
 */
function wp_widgets_init() {
	if ( !is_blog_installed() )
		return;

	$widget_ops = array('classname' => 'widget_pages', 'description' => __( "Your blog's WordPress Pages") );
	wp_register_sidebar_widget('pages', __('Pages'), 'wp_widget_pages', $widget_ops);
	wp_register_widget_control('pages', __('Pages'), 'wp_widget_pages_control' );

	$widget_ops = array('classname' => 'widget_calendar', 'description' => __( "A calendar of your blog's posts") );
	wp_register_sidebar_widget('calendar', __('Calendar'), 'wp_widget_calendar', $widget_ops);
	wp_register_widget_control('calendar', __('Calendar'), 'wp_widget_calendar_control' );

	$widget_ops = array('classname' => 'widget_archive', 'description' => __( "A monthly archive of your blog's posts") );
	wp_register_sidebar_widget('archives', __('Archives'), 'wp_widget_archives', $widget_ops);
	wp_register_widget_control('archives', __('Archives'), 'wp_widget_archives_control' );

	$widget_ops = array('classname' => 'widget_links', 'description' => __( "Your blogroll") );
	wp_register_sidebar_widget('links', __('Links'), 'wp_widget_links', $widget_ops);

	$widget_ops = array('classname' => 'widget_meta', 'description' => __( "Log in/out, admin, feed and WordPress links") );
	wp_register_sidebar_widget('meta', __('Meta'), 'wp_widget_meta', $widget_ops);
	wp_register_widget_control('meta', __('Meta'), 'wp_widget_meta_control' );

	$widget_ops = array('classname' => 'widget_search', 'description' => __( "A search form for your blog") );
	wp_register_sidebar_widget('search', __('Search'), 'wp_widget_search', $widget_ops);

	$widget_ops = array('classname' => 'widget_recent_entries', 'description' => __( "The most recent posts on your blog") );
	wp_register_sidebar_widget('recent-posts', __('Recent Posts'), 'wp_widget_recent_entries', $widget_ops);
	wp_register_widget_control('recent-posts', __('Recent Posts'), 'wp_widget_recent_entries_control' );

	$widget_ops = array('classname' => 'widget_tag_cloud', 'description' => __( "Your most used tags in cloud format") );
	wp_register_sidebar_widget('tag_cloud', __('Tag Cloud'), 'wp_widget_tag_cloud', $widget_ops);
	wp_register_widget_control('tag_cloud', __('Tag Cloud'), 'wp_widget_tag_cloud_control' );

	wp_widget_categories_register();
	wp_widget_text_register();
	wp_widget_rss_register();
	wp_widget_recent_comments_register();

	do_action('widgets_init');
}

add_action('init', 'wp_widgets_init', 1);

/*
 * Pattern for multi-widget (allows multiple instances such as the text widget).
 *
 * Make sure to close the comments after copying.

/**
 * Displays widget.
 *
 * Supports multiple widgets.
 *
 * @param array $args Widget arguments.
 * @param array|int $widget_args Widget number. Which of the several widgets of this type do we mean.
 * /
function widget_many( $args, $widget_args = 1 ) {
	extract( $args, EXTR_SKIP );
	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	// Data should be stored as array:  array( number => data for that instance of the widget, ... )
	$options = get_option('widget_many');
	if ( !isset($options[$number]) )
		return;

	echo $before_widget;

	// Do stuff for this widget, drawing data from $options[$number]

	echo $after_widget;
}

/**
 * Displays form for a particular instance of the widget.
 *
 * Also updates the data after a POST submit.
 *
 * @param array|int $widget_args Widget number. Which of the several widgets of this type do we mean.
 * /
function widget_many_control( $widget_args = 1 ) {
	global $wp_registered_widgets;
	static $updated = false; // Whether or not we have already updated the data after a POST submit

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	// Data should be stored as array:  array( number => data for that instance of the widget, ... )
	$options = get_option('widget_many');
	if ( !is_array($options) )
		$options = array();

	// We need to update the data
	if ( !$updated && !empty($_POST['sidebar']) ) {
		// Tells us what sidebar to put the data in
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			// Remove all widgets of this type from the sidebar.  We'll add the new data in a second.  This makes sure we don't get any duplicate data
			// since widget ids aren't necessarily persistent across multiple updates
			if ( 'widget_many' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "many-$widget_number", $_POST['widget-id'] ) ) // the widget has been removed. "many-$widget_number" is "{id_base}-{widget_number}
					unset($options[$widget_number]);
			}
		}

		foreach ( (array) $_POST['widget-many'] as $widget_number => $widget_many_instance ) {
			// compile data from $widget_many_instance
			if ( !isset($widget_many_instance['something']) && isset($options[$widget_number]) ) // user clicked cancel
				continue;
			$something = wp_specialchars( $widget_many_instance['something'] );
			$options[$widget_number] = array( 'something' => $something );  // Even simple widgets should store stuff in array, rather than in scalar
		}

		update_option('widget_many', $options);

		$updated = true; // So that we don't go through this more than once
	}


	// Here we echo out the form
	if ( -1 == $number ) { // We echo out a template for a form which can be converted to a specific form later via JS
		$something = '';
		$number = '%i%';
	} else {
		$something = attribute_escape($options[$number]['something']);
	}

	// The form has inputs with names like widget-many[$number][something] so that all data for that instance of
	// the widget are stored in one $_POST variable: $_POST['widget-many'][$number]
?>
		<p>
			<input class="widefat" id="widget-many-something-<?php echo $number; ?>" name="widget-many[<?php echo $number; ?>][something]" type="text" value="<?php echo $data; ?>" />
			<input type="hidden" id="widget-many-submit-<?php echo $number; ?>" name="widget-many[<?php echo $number; ?>][submit]" value="1" />
		</p>
<?php
}

/**
 * Registers each instance of our widget on startup.
 * /
function widget_many_register() {
	if ( !$options = get_option('widget_many') )
		$options = array();

	$widget_ops = array('classname' => 'widget_many', 'description' => __('Widget which allows multiple instances'));
	$control_ops = array('width' => 400, 'height' => 350, 'id_base' => 'many');
	$name = __('Many');

	$registered = false;
	foreach ( array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['something']) ) // we used 'something' above in our exampple.  Replace with with whatever your real data are.
			continue;

		// $id should look like {$id_base}-{$o}
		$id = "many-$o"; // Never never never translate an id
		$registered = true;
		wp_register_sidebar_widget( $id, $name, 'widget_many', $widget_ops, array( 'number' => $o ) );
		wp_register_widget_control( $id, $name, 'widget_many_control', $control_ops, array( 'number' => $o ) );
	}

	// If there are none, we register the widget's existance with a generic template
	if ( !$registered ) {
		wp_register_sidebar_widget( 'many-1', $name, 'widget_many', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'many-1', $name, 'widget_many_control', $control_ops, array( 'number' => -1 ) );
	}
}

// This is important
add_action( 'widgets_init', 'widget_many_register' );

*/

?>
