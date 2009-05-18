<?php
/**
 * Widgets administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( 'admin.php' );

/** WordPress Administration Widgets API */
require_once(ABSPATH . 'wp-admin/includes/widgets.php');

if ( ! current_user_can('switch_themes') )
	wp_die( __( 'Cheatin&#8217; uh?' ));

wp_enqueue_script('admin-widgets');
wp_admin_css( 'widgets' );

do_action( 'sidebar_admin_setup' );

$title = __( 'Widgets' );
$parent_file = 'themes.php';

// register the inactive_widgets area as sidebar
register_sidebar(array(
	'name' => __('Inactive Widgets'),
	'id' => 'wp_inactive_widgets',
	'before_widget' => '',
	'after_widget' => '',
	'before_title' => '',
	'after_title' => '',
));

// These are the widgets grouped by sidebar
$sidebars_widgets = wp_get_sidebars_widgets();
if ( empty( $sidebars_widgets ) )
	$sidebars_widgets = wp_get_widget_defaults();

// look for "lost" widgets, this has to run at least on each theme change
function retrieve_widgets() {
	global $wp_registered_widget_updates, $wp_registered_sidebars, $sidebars_widgets, $wp_registered_widgets;

	$_sidebars_widgets = array();
	$sidebars = array_keys($wp_registered_sidebars);

	unset( $sidebars_widgets['array_version'] );

	$diff = array_diff( array_keys($sidebars_widgets), $sidebars );
	if ( empty($diff) )
		return;

	// Move the known-good ones first
	foreach ( $sidebars as $id ) {
		if ( array_key_exists( $id, $sidebars_widgets ) ) {
			$_sidebars_widgets[$id] = $sidebars_widgets[$id];
			unset($sidebars_widgets[$id], $sidebars[$id]);
		}
	}

	// if new theme has less sidebars than the old theme
	if ( !empty($sidebars_widgets) ) {
		foreach ( $sidebars_widgets as $lost => $val ) {
			if ( is_array($val) )
				$_sidebars_widgets['wp_inactive_widgets'] = array_merge( (array) $_sidebars_widgets['wp_inactive_widgets'], $val );
		}
	}
	
	// discard invalid, theme-specific widgets from sidebars
	foreach ( $_sidebars_widgets as $sidebar => $widgets ) {
		if ( !is_array($widgets) )
			continue;
		
		$_widgets = array();
		foreach ( $widgets as $widget ) {
			if ( isset($wp_registered_widgets[$widget]) )
				$_widgets[] = $widget;
		}
		$_sidebars_widgets[$sidebar] = $_widgets;
	}

	$sidebars_widgets = $_sidebars_widgets;
	unset($_sidebars_widgets, $_widgets);

	// find hidden/lost multi-widget instances
	$shown_widgets = array();
	foreach ( $sidebars_widgets as $sidebar ) {
		if ( is_array($sidebar) )
			$shown_widgets = array_merge($shown_widgets, $sidebar);
	}

	$all_widgets = array();
	foreach ( $wp_registered_widget_updates as $key => $val ) {
		if ( isset($val['id_base']) )
			$all_widgets[] = $val['id_base'];
		else
			$all_widgets[] = $key;
	}

	$all_widgets = array_unique($all_widgets);

	$lost_widgets = array();
	foreach ( $all_widgets as $name ) {
		$data = get_option( str_replace('-', '_', "widget_$name") );
		if ( is_array($data) ) {
			foreach ( $data as $num => $value ) {
				if ( !is_numeric($num) ) // skip single widgets, some don't delete their settings
					continue;
				if ( is_array($value) && !in_array("$name-$num", $shown_widgets, true) )
					$lost_widgets[] = "$name-$num";
			}
		}
	}

	$sidebars_widgets['wp_inactive_widgets'] = array_merge($lost_widgets, (array) $sidebars_widgets['wp_inactive_widgets']);
	wp_set_sidebars_widgets($sidebars_widgets);
}
retrieve_widgets();

if ( count($wp_registered_sidebars) == 1 ) {
	// If only "wp_inactive_widgets" is defined the theme has no sidebars, die.
	require_once( 'admin-header.php' );
?>

	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo esc_html( $title ); ?></h2>
		<div class="error">
			<p><?php _e( 'No Sidebars Defined' ); ?></p>
		</div>
		<p><?php _e( 'The theme you are currently using isn&#8217;t widget-aware, meaning that it has no sidebars that you are able to change. For information on making your theme widget-aware, please <a href="http://codex.wordpress.org/Widgetizing_Themes">follow these instructions</a>.' ); ?></p>
	</div>

<?php
	require_once( 'admin-footer.php' );
	exit;
}

// We're saving a widget without js
if ( isset($_POST['savewidget']) || isset($_POST['removewidget']) ) {
	$widget_id = $_POST['widget-id'];
	check_admin_referer("save-delete-widget-$widget_id");

	$sidebar_id = $_POST['insidebar'];
	$position = isset($_POST[$sidebar_id . '_position']) ? (int) $_POST[$sidebar_id . '_position'] - 1 : 0;
	$_POST['sidebar'] = $sidebar_id;

	$id_base = $_POST['id_base'];
	$number = isset($_POST['multi_number']) ? $_POST['multi_number'] : '';
	$sidebar = isset($sidebars_widgets[$sidebar_id]) ? $sidebars_widgets[$sidebar_id] : array();

	// delete
	if ( isset($_POST['removewidget']) && $_POST['removewidget'] ) {
		$widget = isset($wp_registered_widgets[$widget_id]) ? $wp_registered_widgets[$widget_id] : false;

		if ( !in_array($widget_id, $sidebar, true) || !$widget ) {
			wp_redirect('widgets.php?error=0');
			exit;
		}

		$option = str_replace( '-', '_', 'widget_' . $id_base );
		$data = get_option($option);

		if ( isset($widget['params'][0]['number']) ) {
			$number = $widget['params'][0]['number'];
			if ( is_array($data) && isset($data[$number]) ) {
				unset( $data[$number] );
				update_option($option, $data);
			}
		} else {
			if ( $data ) {
				$data = array();
				update_option($option, $data);
			}
		}

		$sidebar = array_diff( $sidebar, array($widget_id) );
	} else {
		// save
		foreach ( (array) $wp_registered_widget_updates as $name => $control ) {
			if ( $name != $id_base || !is_callable($control['callback']) )
				continue;

			if ( $number ) {
				// don't delete other instances of the same multi-widget
				foreach ( $sidebar as $_widget_id ) {
					if ( isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) )
						unset($wp_registered_widgets[$_widget_id]['params'][0]['number']);
				}
				$widget_id = "$id_base-$number";
			}

			ob_start();
				call_user_func_array( $control['callback'], $control['params'] );
			ob_end_clean();

			// remove old position
			$sidebar = array_diff( $sidebar, array($widget_id) );
			foreach ( $sidebars_widgets as $key => $sb ) {
				if ( is_array($sb) && in_array($widget_id, $sb, true) )
					$sidebars_widgets[$key] = array_diff( $sb, array($widget_id) );
			}

			array_splice( $sidebar, $position, 0, $widget_id );
			break;
		}
	}

	$sidebars_widgets[$sidebar_id] = $sidebar;
	wp_set_sidebars_widgets($sidebars_widgets);

	wp_redirect('widgets.php?message=0');
	exit;
}

// Output the widget form without js
if ( isset($_GET['editwidget']) && $_GET['editwidget'] ) {
	$widget_id = $_GET['editwidget'];

	if ( isset($_GET['addnew']) ) {
		// Default to the first sidebar
		$sidebar = array_shift( $keys = array_keys($wp_registered_sidebars) );

		if ( isset($_GET['base']) && isset($_GET['num']) ) { // multi-widget
			// Copy minimal info from an existing instance of this widget to a new instance
			foreach ( $wp_registered_widget_controls as $control ) {
				if ( $_GET['base'] === $control['id_base'] ) {
					$control_callback = $control['callback'];
					$multi_number = (int) $_GET['num'];
					$control['params'][0]['number'] = $multi_number;
					$control['id'] = $control['id_base'] . '-' . $multi_number;
					$wp_registered_widget_controls[$control['id']] = $control;
					break;
				}
			}
		}
	}

	if ( isset($wp_registered_widget_controls[$widget_id]) && !isset($control) ) {
		$control = $wp_registered_widget_controls[$widget_id];
		$control_callback = $control['callback'];
	}

	if ( !isset($sidebar) )
		$sidebar = isset($_GET['sidebar']) ? $_GET['sidebar'] : 'wp_inactive_widgets';

	if ( !isset($multi_number) )
		$multi_number = isset($control['params'][0]['number']) ? $control['params'][0]['number'] : '';

	$id_base = isset($control['id_base']) ? $control['id_base'] : $control['id'];

	// show the widget form
	if ( is_callable( $control_callback ) ) {
		$width = ' style="width:' . max($control['width'], 350) . 'px"';
		$key = isset($_GET['key']) ? (int) $_GET['key'] : 0;

		require_once( 'admin-header.php' ); ?>
		<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php echo esc_html( $title ); ?></h2>
		<div class="editwidget"<?php echo $width; ?>>
		<h3><?php printf( __( 'Widget %s' ), esc_html( strip_tags($control['name']) ) ); ?></h3>

		<form action="widgets.php" method="post">
		<div class="widget-inside">
<?php	call_user_func_array( $control_callback, $control['params'] ); ?>
		</div>

		<p class="describe"><?php _e('Select both the sidebar for this widget and the position of the widget in that sidebar.'); ?></p>
		<div class="widget-position">
		<table class="widefat"><thead><tr><th><?php _e('Sidebar'); ?></th><th><?php _e('Position'); ?></th></tr></thead><tbody>
<?php	foreach ( $wp_registered_sidebars as $sbname => $sbvalue ) {
			echo "\t\t<tr><td><label><input type='radio' name='insidebar' value='" . esc_attr($sbname) . "'" . checked( $sbname, $sidebar, false ) . " /> $sbvalue[name]</label></td><td>";
			if ( 'wp_inactive_widgets' == $sbname ) {
				echo '&nbsp;';
			} else {
				if ( !isset($sidebars_widgets[$sbname]) || !is_array($sidebars_widgets[$sbname]) ) {
					$j = 1;
				} else {
					$j = count($sidebars_widgets[$sbname]);
					if ( isset($_GET['addnew']) || !in_array($widget_id, $sidebars_widgets[$sbname], true) )
						$j++;
				}
				$selected = '';
				echo "\t\t<select name='{$sbname}_position'>\n";
				echo "\t\t<option value=''>" . __('-- select --') . "</option>\n";
				for ( $i = 1; $i <= $j; $i++ ) {
					if ( in_array($widget_id, $sidebars_widgets[$sbname], true) )
						$selected = selected( $i, $key + 1, false );
					echo "\t\t<option value='$i'$selected> $i </option>\n";
				}
				echo "\t\t</select>\n";
			}
			echo "</td></tr>\n";
		} ?>
		</tbody></table>
		</div>

		<div class="widget-control-actions">
<?php	if ( isset($_GET['addnew']) ) { ?>
		<a href="widgets.php" class="button alignleft"><?php _e('Cancel'); ?></a>
<?php	} else { ?>
		<input type="submit" name="removewidget" class="button alignleft" value="<?php esc_attr_e('Remove'); ?>" />
<?php	} ?>
		<input type="submit" name="savewidget" class="button-primary alignright" value="<?php esc_attr_e('Save Widget'); ?>" />
		<input type="hidden" name="widget-id" class="widget-id" value="<?php echo esc_attr($widget_id); ?>" />
		<input type="hidden" name="id_base" class="id_base" value="<?php echo esc_attr($id_base); ?>" />
		<input type="hidden" name="multi_number" class="multi_number" value="<?php echo esc_attr($multi_number); ?>" />
<?php	wp_nonce_field("save-delete-widget-$widget_id"); ?>
		<br class="clear" />
		</div>
		</form>
		</div>
		</div>
<?php
		require_once( 'admin-footer.php' );
		exit;
	}
	wp_redirect('widgets.php?error=1');
	exit;
}

$messages = array(
	__('Changes saved.')
);

$errors = array(
	__('Error while saving.'),
	__('Error in displaying the widget settings form.')
);

require_once( 'admin-header.php' ); ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<?php if ( isset($_GET['message']) && isset($messages[$_GET['message']]) ) { ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php } ?>
<?php if ( isset($_GET['error']) && isset($errors[$_GET['error']]) ) { ?>
<div id="message" class="error"><p><?php echo $errors[$_GET['error']]; ?></p></div>
<?php } ?>

<div class="widget-liquid-left">
<div id="widgets-left">
	<div id="available-widgets" class="widgets-holder-wrap">
		<div class="sidebar-name">
		<div class="sidebar-name-arrow"><br /></div>
		<h3><?php _e('Available Widgets'); ?></h3></div>
		<?php wp_list_widgets(); ?>
		<br class="clear" />
	</div>

	<div class="widgets-holder-wrap">
		<div class="sidebar-name">
		<div class="sidebar-name-arrow"><br /></div>
		<h3><?php _e('Inactive Widgets'); ?>
		<span><img src="images/wpspin_light.gif" class="ajax-feedback" title="" alt="" /></span></h3></div>
		<div class="widget-holder inactive">
		<p class="description"><?php _e('Drag widgets here to remove them from the sidebar but keep their settings.'); ?></p>
		<?php wp_list_widget_controls('wp_inactive_widgets'); ?>
		<br class="clear" />
		</div>
	</div>
</div>
</div>

<div class="widget-liquid-right">
<div id="widgets-right">
<?php
$i = 0;
foreach ( $wp_registered_sidebars as $sidebar => $registered_sidebar ) {
	if ( 'wp_inactive_widgets' == $sidebar )
		continue;
	$closed = $i ? ' closed' : ''; ?>
	<div class="widgets-holder-wrap<?php echo $closed; ?>">
	<div class="sidebar-name">
	<div class="sidebar-name-arrow"><br /></div>
	<h3><?php echo esc_html( $registered_sidebar['name'] ); ?>
	<span><img src="images/wpspin_dark.gif" class="ajax-feedback" title="" alt="" /></span></h3></div>
	<?php wp_list_widget_controls( $sidebar ); // Show the control forms for each of the widgets in this sidebar ?>
	</div>
<?php
	$i++;
} ?>
</div>
</div>
<form action="" method="post">
<?php wp_nonce_field( 'save-sidebar-widgets', '_wpnonce_widgets', false ); ?>
</form>
<br class="clear" />
</div>

<?php
do_action( 'sidebar_admin_page' );
require_once( 'admin-footer.php' );
