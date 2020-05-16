<?php
/**
 * WordPress Widgets Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Display list of the available widgets.
 *
 * @since 2.5.0
 *
 * @global array $wp_registered_widgets
 * @global array $wp_registered_widget_controls
 */
function wp_list_widgets() {
	global $wp_registered_widgets, $wp_registered_widget_controls;

	$sort = $wp_registered_widgets;
	usort( $sort, '_sort_name_callback' );
	$done = array();

	foreach ( $sort as $widget ) {
		if ( in_array( $widget['callback'], $done, true ) ) { // We already showed this multi-widget.
			continue;
		}

		$sidebar = is_active_widget( $widget['callback'], $widget['id'], false, false );
		$done[]  = $widget['callback'];

		if ( ! isset( $widget['params'][0] ) ) {
			$widget['params'][0] = array();
		}

		$args = array(
			'widget_id'   => $widget['id'],
			'widget_name' => $widget['name'],
			'_display'    => 'template',
		);

		if ( isset( $wp_registered_widget_controls[ $widget['id'] ]['id_base'] ) && isset( $widget['params'][0]['number'] ) ) {
			$id_base            = $wp_registered_widget_controls[ $widget['id'] ]['id_base'];
			$args['_temp_id']   = "$id_base-__i__";
			$args['_multi_num'] = next_widget_id_number( $id_base );
			$args['_add']       = 'multi';
		} else {
			$args['_add'] = 'single';
			if ( $sidebar ) {
				$args['_hide'] = '1';
			}
		}

		$control_args = array(
			0 => $args,
			1 => $widget['params'][0],
		);
		$sidebar_args = wp_list_widget_controls_dynamic_sidebar( $control_args );

		wp_widget_control( ...$sidebar_args );
	}
}

/**
 * Callback to sort array by a 'name' key.
 *
 * @since 3.1.0
 * @access private
 *
 * @return int
 */
function _sort_name_callback( $a, $b ) {
	return strnatcasecmp( $a['name'], $b['name'] );
}

/**
 * Show the widgets and their settings for a sidebar.
 * Used in the admin widget config screen.
 *
 * @since 2.5.0
 *
 * @param string $sidebar      Sidebar ID.
 * @param string $sidebar_name Optional. Sidebar name. Default empty.
 */
function wp_list_widget_controls( $sidebar, $sidebar_name = '' ) {
	add_filter( 'dynamic_sidebar_params', 'wp_list_widget_controls_dynamic_sidebar' );

	$description = wp_sidebar_description( $sidebar );

	echo '<div id="' . esc_attr( $sidebar ) . '" class="widgets-sortables">';

	if ( $sidebar_name ) {
		$add_to = sprintf(
			/* translators: %s: Widgets sidebar name. */
			__( 'Add to: %s' ),
			$sidebar_name
		);
		?>
		<div class="sidebar-name" data-add-to="<?php echo esc_attr( $add_to ); ?>">
			<button type="button" class="handlediv hide-if-no-js" aria-expanded="true">
				<span class="screen-reader-text"><?php echo esc_html( $sidebar_name ); ?></span>
				<span class="toggle-indicator" aria-hidden="true"></span>
			</button>
			<h2><?php echo esc_html( $sidebar_name ); ?> <span class="spinner"></span></h2>
		</div>
		<?php
	}

	if ( ! empty( $description ) ) {
		?>
		<div class="sidebar-description">
			<p class="description"><?php echo $description; ?></p>
		</div>
		<?php
	}

	dynamic_sidebar( $sidebar );

	echo '</div>';
}

/**
 * Retrieves the widget control arguments.
 *
 * @since 2.5.0
 *
 * @global array $wp_registered_widgets
 *
 * @staticvar int $i
 *
 * @param array $params
 * @return array
 */
function wp_list_widget_controls_dynamic_sidebar( $params ) {
	global $wp_registered_widgets;
	static $i = 0;
	$i++;

	$widget_id = $params[0]['widget_id'];
	$id        = isset( $params[0]['_temp_id'] ) ? $params[0]['_temp_id'] : $widget_id;
	$hidden    = isset( $params[0]['_hide'] ) ? ' style="display:none;"' : '';

	$params[0]['before_widget'] = "<div id='widget-{$i}_{$id}' class='widget'$hidden>";
	$params[0]['after_widget']  = '</div>';
	$params[0]['before_title']  = '%BEG_OF_TITLE%'; // Deprecated.
	$params[0]['after_title']   = '%END_OF_TITLE%'; // Deprecated.

	if ( is_callable( $wp_registered_widgets[ $widget_id ]['callback'] ) ) {
		$wp_registered_widgets[ $widget_id ]['_callback'] = $wp_registered_widgets[ $widget_id ]['callback'];
		$wp_registered_widgets[ $widget_id ]['callback']  = 'wp_widget_control';
	}

	return $params;
}

/**
 * @global array $wp_registered_widgets
 *
 * @param string $id_base
 * @return int
 */
function next_widget_id_number( $id_base ) {
	global $wp_registered_widgets;
	$number = 1;

	foreach ( $wp_registered_widgets as $widget_id => $widget ) {
		if ( preg_match( '/' . $id_base . '-([0-9]+)$/', $widget_id, $matches ) ) {
			$number = max( $number, $matches[1] );
		}
	}
	$number++;

	return $number;
}

/**
 * Meta widget used to display the control form for a widget.
 *
 * Called from dynamic_sidebar().
 *
 * @since 2.5.0
 *
 * @global array $wp_registered_widgets
 * @global array $wp_registered_widget_controls
 * @global array $sidebars_widgets
 *
 * @param array $sidebar_args
 * @return array
 */
function wp_widget_control( $sidebar_args ) {
	global $wp_registered_widgets, $wp_registered_widget_controls, $sidebars_widgets;

	$widget_id  = $sidebar_args['widget_id'];
	$sidebar_id = isset( $sidebar_args['id'] ) ? $sidebar_args['id'] : false;
	$key        = $sidebar_id ? array_search( $widget_id, $sidebars_widgets[ $sidebar_id ], true ) : '-1'; // Position of widget in sidebar.
	$control    = isset( $wp_registered_widget_controls[ $widget_id ] ) ? $wp_registered_widget_controls[ $widget_id ] : array();
	$widget     = $wp_registered_widgets[ $widget_id ];

	$id_format     = $widget['id'];
	$widget_number = isset( $control['params'][0]['number'] ) ? $control['params'][0]['number'] : '';
	$id_base       = isset( $control['id_base'] ) ? $control['id_base'] : $widget_id;
	$width         = isset( $control['width'] ) ? $control['width'] : '';
	$height        = isset( $control['height'] ) ? $control['height'] : '';
	$multi_number  = isset( $sidebar_args['_multi_num'] ) ? $sidebar_args['_multi_num'] : '';
	$add_new       = isset( $sidebar_args['_add'] ) ? $sidebar_args['_add'] : '';

	$before_form           = isset( $sidebar_args['before_form'] ) ? $sidebar_args['before_form'] : '<form method="post">';
	$after_form            = isset( $sidebar_args['after_form'] ) ? $sidebar_args['after_form'] : '</form>';
	$before_widget_content = isset( $sidebar_args['before_widget_content'] ) ? $sidebar_args['before_widget_content'] : '<div class="widget-content">';
	$after_widget_content  = isset( $sidebar_args['after_widget_content'] ) ? $sidebar_args['after_widget_content'] : '</div>';

	$query_arg = array( 'editwidget' => $widget['id'] );
	if ( $add_new ) {
		$query_arg['addnew'] = 1;
		if ( $multi_number ) {
			$query_arg['num']  = $multi_number;
			$query_arg['base'] = $id_base;
		}
	} else {
		$query_arg['sidebar'] = $sidebar_id;
		$query_arg['key']     = $key;
	}

	/*
	 * We aren't showing a widget control, we're outputting a template
	 * for a multi-widget control.
	 */
	if ( isset( $sidebar_args['_display'] ) && 'template' === $sidebar_args['_display'] && $widget_number ) {
		// number == -1 implies a template where id numbers are replaced by a generic '__i__'.
		$control['params'][0]['number'] = -1;
		// With id_base widget id's are constructed like {$id_base}-{$id_number}.
		if ( isset( $control['id_base'] ) ) {
			$id_format = $control['id_base'] . '-__i__';
		}
	}

	$wp_registered_widgets[ $widget_id ]['callback'] = $wp_registered_widgets[ $widget_id ]['_callback'];
	unset( $wp_registered_widgets[ $widget_id ]['_callback'] );

	$widget_title = esc_html( strip_tags( $sidebar_args['widget_name'] ) );
	$has_form     = 'noform';

	echo $sidebar_args['before_widget'];
	?>
	<div class="widget-top">
	<div class="widget-title-action">
		<button type="button" class="widget-action hide-if-no-js" aria-expanded="false">
			<span class="screen-reader-text edit">
				<?php
				/* translators: %s: Widget title. */
				printf( __( 'Edit widget: %s' ), $widget_title );
				?>
			</span>
			<span class="screen-reader-text add">
				<?php
				/* translators: %s: Widget title. */
				printf( __( 'Add widget: %s' ), $widget_title );
				?>
			</span>
			<span class="toggle-indicator" aria-hidden="true"></span>
		</button>
		<a class="widget-control-edit hide-if-js" href="<?php echo esc_url( add_query_arg( $query_arg ) ); ?>">
			<span class="edit"><?php _ex( 'Edit', 'widget' ); ?></span>
			<span class="add"><?php _ex( 'Add', 'widget' ); ?></span>
			<span class="screen-reader-text"><?php echo $widget_title; ?></span>
		</a>
	</div>
	<div class="widget-title"><h3><?php echo $widget_title; ?><span class="in-widget-title"></span></h3></div>
	</div>

	<div class="widget-inside">
	<?php echo $before_form; ?>
	<?php echo $before_widget_content; ?>
	<?php
	if ( isset( $control['callback'] ) ) {
		$has_form = call_user_func_array( $control['callback'], $control['params'] );
	} else {
		echo "\t\t<p>" . __( 'There are no options for this widget.' ) . "</p>\n";
	}

	$noform_class = '';
	if ( 'noform' === $has_form ) {
		$noform_class = ' widget-control-noform';
	}
	?>
	<?php echo $after_widget_content; ?>
	<input type="hidden" name="widget-id" class="widget-id" value="<?php echo esc_attr( $id_format ); ?>" />
	<input type="hidden" name="id_base" class="id_base" value="<?php echo esc_attr( $id_base ); ?>" />
	<input type="hidden" name="widget-width" class="widget-width" value="<?php echo esc_attr( $width ); ?>" />
	<input type="hidden" name="widget-height" class="widget-height" value="<?php echo esc_attr( $height ); ?>" />
	<input type="hidden" name="widget_number" class="widget_number" value="<?php echo esc_attr( $widget_number ); ?>" />
	<input type="hidden" name="multi_number" class="multi_number" value="<?php echo esc_attr( $multi_number ); ?>" />
	<input type="hidden" name="add_new" class="add_new" value="<?php echo esc_attr( $add_new ); ?>" />

	<div class="widget-control-actions">
		<div class="alignleft">
			<button type="button" class="button-link button-link-delete widget-control-remove"><?php _e( 'Delete' ); ?></button>
			<span class="widget-control-close-wrapper">
				|
				<button type="button" class="button-link widget-control-close"><?php _e( 'Done' ); ?></button>
			</span>
		</div>
		<div class="alignright<?php echo $noform_class; ?>">
			<?php submit_button( __( 'Save' ), 'primary widget-control-save right', 'savewidget', false, array( 'id' => 'widget-' . esc_attr( $id_format ) . '-savewidget' ) ); ?>
			<span class="spinner"></span>
		</div>
		<br class="clear" />
	</div>
	<?php echo $after_form; ?>
	</div>

	<div class="widget-description">
	<?php
	$widget_description = wp_widget_description( $widget_id );
	echo ( $widget_description ) ? "$widget_description\n" : "$widget_title\n";
	?>
	</div>
	<?php
	echo $sidebar_args['after_widget'];

	return $sidebar_args;
}

/**
 * @param string $classes
 * @return string
 */
function wp_widgets_access_body_class( $classes ) {
	return "$classes widgets_access ";
}
