<?php
/**
 * WordPress Widgets Administration API
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * Display list of widgets, either all or matching search.
 *
 * The search parameter are search terms separated by spaces.
 *
 * @since unknown
 *
 * @param string $show Optional, default is all. What to display, can be 'all', 'unused', or 'used'.
 * @param string $_search Optional. Search for widgets. Should be unsanitized.
 */
function wp_list_widgets( $show = 'all', $_search = false ) {
	global $wp_registered_widgets, $sidebars_widgets, $wp_registered_widget_controls;
	if ( $_search ) {
		// sanitize
		$search = preg_replace( '/[^\w\s]/', '', $_search );
		// array of terms
		$search_terms = preg_split( '/[\s]/', $search, -1, PREG_SPLIT_NO_EMPTY );
	} else {
		$search_terms = array();
	}

	if ( !in_array( $show, array( 'all', 'unused', 'used' ) ) )
		$show = 'all';
?>

	<ul id='widget-list'>
		<?php
		$no_widgets_shown = true;
		$already_shown = array();
		foreach ( $wp_registered_widgets as $name => $widget ) :
			if ( 'all' == $show && in_array( $widget['callback'], $already_shown ) ) // We already showed this multi-widget
				continue;

			if ( $search_terms ) {
				$hit = false;
				// Simple case-insensitive search.  Boolean OR.
				$search_text = preg_replace( '/[^\w]/', '', $widget['name'] );
				if ( isset($widget['description']) )
					$search_text .= preg_replace( '/[^\w]/', '', $widget['description'] );

				foreach ( $search_terms as $search_term ) {
					if ( stristr( $search_text, $search_term ) ) {
						$hit = true;
						break;
					}
				}
				if ( !$hit )
					continue;
			}

			$sidebar = is_active_widget( $widget['callback'], $widget['id'] );

			if ( ( 'unused' == $show && $sidebar ) || ( 'used' == $show && !$sidebar ) )
				continue;

			if ( ! isset( $widget['params'][0] ) )
				$widget['params'][0] = array();
			ob_start();
			$args = wp_list_widget_controls_dynamic_sidebar( array( 0 => array( 'widget_id' => $widget['id'], 'widget_name' => $widget['name'], '_display' => 'template', '_show' => $show ), 1 => $widget['params'][0] ) );
			$sidebar_args = call_user_func_array( 'wp_widget_control', $args );
			$widget_control_template = ob_get_contents();
			ob_end_clean();

			$widget_id = $widget['id']; // save this for later in case we mess with $widget['id']

			$is_multi = false !== strpos( $widget_control_template, '%i%' );
			if ( !$sidebar || $is_multi ) {
				$add_query = array(
					'sidebar' => $sidebar,
					'key' => false,
					'edit' => false
				);
				if ( 'all' == $show && $is_multi ) {
					// it's a multi-widget.  We only need to show it in the list once.
					$already_shown[] = $widget['callback'];
					$num = (int) array_pop( $ids = explode( '-', $widget['id'] ) );
					$id_base = $wp_registered_widget_controls[$widget['id']]['id_base'];
					// so that we always add a new one when clicking "add"
					while ( isset($wp_registered_widgets["$id_base-$num"]) )
						$num++;
					$widget['id'] = "$id_base-$num";
					$add_query['base'] = $id_base;
					$add_query['key'] = $num;
					$add_query['sidebar'] = $GLOBALS['sidebar'];
				}
				$add_query['add'] = $widget['id'];
				$action = 'add';
				$add_url = clean_url( wp_nonce_url( add_query_arg( $add_query ), "add-widget_$widget[id]" ) );
			} else {
				$action = 'edit';
				$edit_url = clean_url( add_query_arg( array(
					'sidebar' => $sidebar,
					'edit' => $widget['id'],
					'key' => array_search( $widget['id'], $sidebars_widgets[$sidebar] ),
				) ) );

				$widget_control_template = '<textarea rows="1" cols="1">' . htmlspecialchars( $widget_control_template ) . '</textarea>';
			}

			$widget_control_template = $sidebar_args['before_widget'] . $widget_control_template . $sidebar_args['after_widget'];

			$no_widgets_shown = false;


			if ( 'all' != $show && $sidebar_args['_widget_title'] )
				$widget_title = $sidebar_args['_widget_title'];
			else
				$widget_title = $widget['name'];
		?>

		<li id="widget-list-item-<?php echo attribute_escape( $widget['id'] ); ?>" class="widget-list-item">
			<h4 class="widget-title widget-draggable">

				<span><?php echo $widget_title; ?></span>

				<?php if ( 'add' == $action ) : ?>

				<a class="widget-action widget-control-add" href="<?php echo $add_url; ?>"><?php _e( 'Add' ); ?></a>

				<?php elseif ( 'edit' == $action ) :
					// We echo a hidden edit link for the sake of the JS.  Edit links are shown (needlessly?) after a widget is added.
				?>

				<a class="widget-action widget-control-edit" href="<?php echo $edit_url; ?>" style="display: none;"><?php _e( 'Edit' ); ?></a>

				<?php endif; ?>

				<br class="clear" />

			</h4>


			<ul id="widget-control-info-<?php echo $widget['id']; ?>" class="widget-control-info">

				<?php echo $widget_control_template; ?>

			</ul>

			<?php if ( 'add' == $action ) : ?>
			<?php endif; ?>

			<div class="widget-description">
				<?php echo ( $widget_description = wp_widget_description( $widget_id ) ) ? $widget_description : '&nbsp;'; ?>
			</div>

			<br class="clear" />

		</li>

		<?php endforeach; if ( $no_widgets_shown ) : ?>

		<li><?php _e( 'No matching widgets' ); ?></li>

		<?php endif; ?>

	</ul>
<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param string $sidebar
 */
function wp_list_widget_controls( $sidebar ) {
	add_filter( 'dynamic_sidebar_params', 'wp_list_widget_controls_dynamic_sidebar' );
?>

	<ul class="widget-control-list">

		<?php if ( !dynamic_sidebar( $sidebar ) ) echo "<li />"; ?>

	</ul>

<?php
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param array $params
 * @return array
 */
function wp_list_widget_controls_dynamic_sidebar( $params ) {
	global $wp_registered_widgets;
	static $i = 0;
	$i++;

	$widget_id = $params[0]['widget_id'];

	$params[0]['before_widget'] = "<li id='widget-list-control-item-$i-$widget_id' class='widget-list-control-item widget-sortable'>\n";
	$params[0]['after_widget'] = "</li>";
	$params[0]['before_title'] = "%BEG_OF_TITLE%";
	$params[0]['after_title'] = "%END_OF_TITLE%";
	if ( is_callable( $wp_registered_widgets[$widget_id]['callback'] ) ) {
		$wp_registered_widgets[$widget_id]['_callback'] = $wp_registered_widgets[$widget_id]['callback'];
		$wp_registered_widgets[$widget_id]['callback'] = 'wp_widget_control';
	}
	return $params;
}

/**
 * Meta widget used to display the control form for a widget.
 *
 * Called from dynamic_sidebar().
 *
 * @since unknown
 *
 * @param array $sidebar_args
 * @return array
 */
function wp_widget_control( $sidebar_args ) {
	global $wp_registered_widgets, $wp_registered_widget_controls, $sidebars_widgets, $edit_widget;
	$widget_id = $sidebar_args['widget_id'];
	$sidebar_id = isset($sidebar_args['id']) ? $sidebar_args['id'] : false;

	$control = isset($wp_registered_widget_controls[$widget_id]) ? $wp_registered_widget_controls[$widget_id] : 0;
	$widget  = $wp_registered_widgets[$widget_id];

	$key = $sidebar_id ? array_search( $widget_id, $sidebars_widgets[$sidebar_id] ) : 'no-key'; // position of widget in sidebar

	$edit = -1 <  $edit_widget && is_numeric($key) && $edit_widget === $key; // (bool) are we currently editing this widget

	$id_format = $widget['id'];

	if ( ! isset( $sidebar_args['_show'] ) )
		$sidebar_args['_show'] = '';

	if ( ! isset( $sidebar_args['_display'] ) )
		$sidebar_args['_display'] = '';

	// We aren't showing a widget control, we're outputing a template for a mult-widget control
	if ( 'all' == $sidebar_args['_show'] && 'template' == $sidebar_args['_display'] && isset($control['params'][0]['number']) ) {
		// number == -1 implies a template where id numbers are replaced by a generic '%i%'
		$control['params'][0]['number'] = -1;
		// if given, id_base means widget id's should be constructed like {$id_base}-{$id_number}
		if ( isset($control['id_base']) )
			$id_format = $control['id_base'] . '-%i%';
	}

	$widget_title = '';
	// We grab the normal widget output to find the widget's title
	if ( ( 'all' != $sidebar_args['_show'] || 'template' != $sidebar_args['_display'] ) && is_callable( $widget['_callback'] ) ) {
		ob_start();
		$args = func_get_args();
		call_user_func_array( $widget['_callback'], $args );
		$widget_title = ob_get_clean();
		$widget_title = wp_widget_control_ob_filter( $widget_title );
	}
	$wp_registered_widgets[$widget_id]['callback'] = $wp_registered_widgets[$widget_id]['_callback'];
	unset($wp_registered_widgets[$widget_id]['_callback']);

	if ( $widget_title && $widget_title != $sidebar_args['widget_name'] )
		$widget_title = sprintf( _c('%1$s: %2$s|1: widget name, 2: widget title' ), $sidebar_args['widget_name'], $widget_title );
	else
		$widget_title = wp_specialchars( strip_tags( $sidebar_args['widget_name'] ) );

	$sidebar_args['_widget_title'] = $widget_title;

	if ( empty($sidebar_args['_display']) || 'template' != $sidebar_args['_display'] )
		echo $sidebar_args['before_widget'];
?>
		<div class="widget-top">
		<h4 class="widget-title"><span><?php echo $widget_title ?></span>

			<?php if ( $edit ) : ?>

			<a class="widget-action widget-control-edit" href="<?php echo clean_url( remove_query_arg( array( 'edit', 'key' ) ) ); ?>"><?php _e('Cancel'); ?></a>

			<?php else : ?>

			<a class="widget-action widget-control-edit" href="<?php echo clean_url( add_query_arg( array( 'edit' => $id_format, 'key' => $key ) ) ); ?>"><?php _e('Edit'); ?></a>

			<?php endif; ?>

			<br class="clear" />

		</h4></div>

		<div class="widget-control"<?php if ( $edit ) echo ' style="display: block;"'; ?>>

			<?php
			if ( $control )
				call_user_func_array( $control['callback'], $control['params'] );
			else
				echo '<p>' . __('There are no options for this widget.') . '</p>';
			?>

			<input type="hidden" name="widget-id[]" value="<?php echo $id_format; ?>" />
			<input type="hidden" class="widget-width" value="<?php echo $control['width']; ?>" />

			<div class="widget-control-actions">

				<?php if ( $control ) : ?>

				<a class="button widget-action widget-control-save hide-if-no-js edit alignleft" href="#save:<?php echo $id_format; ?>"><?php _e('Done'); ?></a>

				<?php endif; ?>

				<a class="button widget-action widget-control-remove alignright" href="<?php echo clean_url( wp_nonce_url( add_query_arg( array( 'remove' => $id_format, 'key' => $key ) ), "remove-widget_$widget[id]" ) ); ?>"><?php _e('Remove'); ?></a>
				<br class="clear" />
			</div>
		</div>
<?php
	if ( empty($sidebar_args['_display']) || 'template' != $sidebar_args['_display'] )
		echo $sidebar_args['after_widget'];
	return $sidebar_args;
}

/**
 * {@internal Missing Short Description}}
 *
 * @since unknown
 *
 * @param string $string
 * @return string
 */
function wp_widget_control_ob_filter( $string ) {
	if ( false === $beg = strpos( $string, '%BEG_OF_TITLE%' ) )
		return '';
	if ( false === $end = strpos( $string, '%END_OF_TITLE%' ) )
		return '';
	$string = substr( $string, $beg + 14 , $end - $beg - 14);
	$string = str_replace( '&nbsp;', ' ', $string );
	return trim( wp_specialchars( strip_tags( $string ) ) );
}

?>