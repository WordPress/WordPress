<?php

// $_search is unsanitized
function wp_list_widgets( $show = 'all', $_search = false ) {
	global $wp_registered_widgets, $sidebars_widgets;
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
			if ( in_array( $widget['callback'], $already_shown ) ) // We already showed this multi-widget
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

			ob_start();
				wp_widget_control( 'no-key', $widget['id'], 'template' );
			$widget_control_template = ob_get_contents();
			ob_end_clean();

			if ( !$sidebar || $is_multi = false !== strpos( $widget_control_template, '%i%' ) ) {
				if ( $is_multi )
					$already_shown[] = $widget['callback']; // it's a multi-widget.  We only need to show it in the list once.
				$action = 'add';
				$add_url = wp_nonce_url( add_query_arg( array(
					'sidebar' => $sidebar,
					'add' => $widget['id'],
					'key' => false,
					'edit' => false
				) ), "add-widget_$widget[id]" );
			} else {
				$action = 'edit';
				$edit_url = clean_url( add_query_arg( array(
					'sidebar' => $sidebar,
					'edit' => $widget['id'],
					'key' => array_search( $widget['id'], $sidebars_widgets[$sidebar] ),
				) ) );
				$widget_control_template = '<li><textarea rows="1" cols="1">' . htmlspecialchars( $widget_control_template ) . '</textarea></li>';
			}

			$no_widgets_shown = false;

		?>

		<li id="widget-list-item-<?php echo attribute_escape( $widget['id'] ); ?>" class="widget-list-item">
			<h4 class="widget-title widget-draggable">

				<?php echo wp_specialchars( $widget['name'] ); ?>

				<?php if ( 'add' == $action ) : ?>

				<a class="widget-action widget-control-add" href="<?php echo $add_url; ?>"><?php _e( 'Add' ); ?></a>

				<?php elseif ( 'edit' == $action ) :
					// We echo a hidden edit link for the sake of the JS.  Edit links are shown (needlessly?) after a widget is added.
				?>

				<a class="widget-action widget-control-edit" href="<?php echo $edit_url; ?>" style="display: none;"><?php _e( 'Edit' ); ?></a>

				<?php endif; ?>

			</h4>


			<ul id="widget-control-info-<?php echo $widget['id']; ?>" class="widget-control-info">

				<?php echo $widget_control_template; ?>

			</ul>

			<?php if ( 'add' == $action ) : ?>
			<?php endif; ?>

			<div class="widget-description">
				<?php echo wp_widget_description( $widget['id'] ); ?>
			</div>

			<br class="clear" />

		</li>

		<?php endforeach; if ( $no_widgets_shown ) : ?>

		<li><?php _e( 'No matching widgets' ); ?></li>

		<?php endif; ?>

	</ul>
<?php
}

function wp_list_widget_controls( $widgets, $edit_widget = -1 ) {
?>

		<ul class="widget-control-list">
			<li />
<?php
	foreach ( $widgets as $key => $widget )
		wp_widget_control( $key, $widget, $key == $edit_widget ? 'edit' : 'display' );
?>

		</ul>

<?php
}


/*
 * Displays the control form for widget of type $widget at position $key.
 * $display
 *  == 'display': Normal, "closed" form.
 *  == 'edit': "open" form
 *  == 'template': generates a form template to be used by JS
 */
function wp_widget_control( $key, $widget, $display = 'display' ) {
	static $i = 0;
	global $wp_registered_widgets, $wp_registered_widget_controls;
	$control = $wp_registered_widget_controls[$widget];
	$widget  = $wp_registered_widgets[$widget];

	$id_format = $widget['id'];
	if ( 'template' == $display && isset($control['params'][0]['number']) ) {
		// number == -1 implies a template where id numbers are replaced by a generic '%i%'
		$control['params'][0]['number'] = -1;
		// if given, id_base means widget id's should be constructed like {$id_base}-{$id_number}
		if ( isset($control['id_base']) )
			$id_format = $control['id_base'] . '-%i%';
	}
?>

		<li id="widget-list-control-item-<?php echo ++$i; ?>-<?php echo $widget['id']; ?>" class="widget-list-control-item widget-sortable">
			<h4 class="widget-title">

				<?php echo $widget['name']; // TODO: Up/Down links for noJS reordering? ?>

				<?php if ( 'edit' == $display ) : ?>

				<a class="widget-action widget-control-edit" href="<?php echo remove_query_arg( array( 'edit', 'key' ) ); ?>"><?php _e('Cancel'); ?></a>

				<?php else : ?>

				<a class="widget-action widget-control-edit" href="<?php echo clean_url( add_query_arg( array( 'edit' => $id_format, 'key' => $key ) ) ); ?>"><?php _e('Edit'); ?></a>

				<?php endif; ?>

			</h4>

			<div class="widget-control"<?php if ( 'edit' == $display ) echo ' style="display: block;"'; ?>>

				<?php
				if ( $control )
					call_user_func_array( $control['callback'], $control['params'] );
				else
					echo '<p>' . __('There are no options for this widget.') . '</p>';
				?>

				<input type="hidden" name="widget-id[]" value="<?php echo $id_format; ?>" />
				<input type="hidden" class="widget-width" value="<?php echo $control['width']; ?>" />

				<div class="widget-control-actions">

					<?php if ( $control && 'edit' != $display ) : ?>

					<a class="widget-action widget-control-save edit alignleft" href="#save:<?php echo $id_format; ?>"><?php _e('Change'); ?></a>

					<?php endif; ?>

					<a class="widget-action widget-control-remove delete alignright" href="<?php echo clean_url( add_query_arg( array( 'remove' => $id_format, 'key' => $key ), wp_nonce_url( null, "remove-widget_$widget[id]" ) ) ); ?>"><?php _e('Remove'); ?></a>
					<br class="clear" />
				</div>
			</div>
		</li>

<?php
}

function widget_css() {
	wp_admin_css( 'css/widgets' );
}

add_action( 'admin_head', 'widget_css' );

?>
