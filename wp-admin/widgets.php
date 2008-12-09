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

wp_enqueue_script( array( 'wp-lists', 'admin-widgets' ) );
wp_admin_css( 'widgets' );

do_action( 'sidebar_admin_setup' );

$title = __( 'Widgets' );
$parent_file = 'themes.php';

// $sidebar = What sidebar are we editing?
if ( isset($_GET['sidebar']) && isset($wp_registered_sidebars[$_GET['sidebar']]) ) {
	$sidebar = attribute_escape( $_GET['sidebar'] );
} elseif ( is_array($wp_registered_sidebars) && !empty($wp_registered_sidebars) ) {
	// By default we look at the first defined sidebar
	$sidebar = array_shift( $keys = array_keys($wp_registered_sidebars) );
} else {
	// If no sidebars, die.
	require_once( 'admin-header.php' );
?>

	<div class="wrap">
	<?php screen_icon(); ?>
	<h2><?php echo wp_specialchars( $title ); ?></h2>
		<div class="error">
			<p><?php _e( 'No Sidebars Defined' ); ?></p>
		</div>
		<p><?php _e( 'The theme you are currently using isn&#8217;t widget-aware, meaning that it has no sidebars that you are able to change. For information on making your theme widget-aware, please <a href="http://codex.wordpress.org/Widgetizing_Themes">follow these instructions</a>.' ); ?></p>
	</div>

<?php
	require_once( 'admin-footer.php' );
	exit;
}

// These are the widgets grouped by sidebar
$sidebars_widgets = wp_get_sidebars_widgets();
if ( empty( $sidebars_widgets ) )
	$sidebars_widgets = wp_get_widget_defaults();

// for the sake of PHP warnings
if ( empty( $sidebars_widgets[$sidebar] ) )
	$sidebars_widgets[$sidebar] = array();

$http_post = 'post' == strtolower($_SERVER['REQUEST_METHOD']);

// We're updating a sidebar
if ( $http_post && isset($sidebars_widgets[$_POST['sidebar']]) ) {
	check_admin_referer( 'edit-sidebar_' . $_POST['sidebar'] );

	/* Hack #1
	 * The widget_control is overloaded.  It updates the widget's options AND echoes out the widget's HTML form.
	 * Since we want to update before sending out any headers, we have to catch it with an output buffer,
	 */
	ob_start();
		/* There can be multiple widgets of the same type, but the widget_control for that
		 * widget type needs only be called once if it's a multi-widget.
		 */
		$already_done = array();

		foreach ( $wp_registered_widget_controls as $name => $control ) {
			if ( in_array( $control['callback'], $already_done ) )
				continue;

			if ( is_callable( $control['callback'] ) ) {
				call_user_func_array( $control['callback'], $control['params'] );
				$control_output = ob_get_contents();
				if ( false !== strpos( $control_output, '%i%' ) ) // if it's a multi-widget, only call control function once.
					$already_done[] = $control['callback'];
			}

			ob_clean();
		}
	ob_end_clean();

	// Prophylactic.  Take out empty ids.
	foreach ( (array) $_POST['widget-id'] as $key => $val )
		if ( !$val )
			unset($_POST['widget-id'][$key]);

	// Reset the key numbering and store
	$new_sidebar = isset( $_POST['widget-id'] ) && is_array( $_POST['widget-id'] ) ? array_values( $_POST['widget-id'] ) : array();
	$sidebars_widgets[$_POST['sidebar']] = $new_sidebar;
	wp_set_sidebars_widgets( $sidebars_widgets );

	wp_redirect( add_query_arg( 'message', 'updated' ) );
	exit;
}

// What widget (if any) are we editing
$edit_widget = -1;

$query_args = array('add', 'remove', 'key', 'edit', '_wpnonce', 'message', 'base' );

if ( isset($_GET['add']) && $_GET['add'] ) {
	// Add to the end of the sidebar
	$control_callback;
	if ( isset($wp_registered_widgets[$_GET['add']]) ) {
		check_admin_referer( "add-widget_$_GET[add]" );
		$sidebars_widgets[$sidebar][] = $_GET['add'];
		wp_set_sidebars_widgets( $sidebars_widgets );
	} elseif ( isset($_GET['base']) && isset($_GET['key']) ) { // It's a multi-widget
		check_admin_referer( "add-widget_$_GET[add]" );
		// Copy minimal info from an existing instance of this widget to a new instance
		foreach ( $wp_registered_widget_controls as $control ) {
			if ( $_GET['base'] === $control['id_base'] ) {
				$control_callback = $control['callback'];
				$num = (int) $_GET['key'];
				$control['params'][0]['number'] = $num;
				$control['id'] = $control['id_base'] . '-' . $num;
				$wp_registered_widget_controls[$control['id']] = $control;
				$sidebars_widgets[$sidebar][] = $control['id'];
				break;
			}
		}
	}

	// it's a multi-widget.  The only way to add multi-widgets without JS is to actually submit POST content...
	// so here we go
	if ( is_callable( $control_callback ) ) {
		require_once( 'admin-header.php' );
	?>
		<div class="wrap">
		<h2><?php _e( 'Add Widget' ); ?></h2>
		<br />
		<form action="<?php echo clean_url( remove_query_arg( $query_args ) ); ?>" method="post">

			<ul class="widget-control-list">
				<li class="widget-list-control-item">
					<div class="widget-top">
					<h4 class="widget-title"><?php echo $control['name']; ?></h4>
					</div>
					<div class="widget-control" style="display: block;">
	<?php
						call_user_func_array( $control_callback, $control['params'] );
	?>
						<div class="widget-control-actions">
							<input type="submit" class="button" value="<?php _e( 'Add Widget' ); ?>" />
							<input type="hidden" id='sidebar' name='sidebar' value="<?php echo $sidebar; ?>" />
	<?php	wp_nonce_field ( 'edit-sidebar_' . $sidebar );
		foreach ( $sidebars_widgets[$sidebar] as $sidebar_widget_id ) : ?>
							<input type="hidden" name='widget-id[]' value="<?php echo $sidebar_widget_id; ?>" />
	<?php 	endforeach; ?>
						</div>
					</div>
				</li>
			</ul>
		</form>
		</div>
	<?php

		require_once( 'admin-footer.php' );
		exit;
	}
	wp_redirect( remove_query_arg( $query_args ) );
	exit;
} elseif ( isset($_GET['remove']) && $_GET['remove'] && isset($_GET['key']) && is_numeric($_GET['key']) ) {
	// Remove from sidebar the widget of type $_GET['remove'] and in position $_GET['key']
	$key = (int) $_GET['key'];
	if ( -1 < $key && ( $keys = array_keys($sidebars_widgets[$sidebar], $_GET['remove']) ) && in_array($key, $keys) ) {
		check_admin_referer( "remove-widget_$_GET[remove]" );
		unset($sidebars_widgets[$sidebar][$key]);
		$sidebars_widgets[$sidebar] = array_values($sidebars_widgets[$sidebar]);
		wp_set_sidebars_widgets( $sidebars_widgets );
	}
	wp_redirect( remove_query_arg( $query_args ) );
	exit;
} elseif ( isset($_GET['edit']) && $_GET['edit'] && isset($_GET['key']) && is_numeric($_GET['key']) ) {
	// Edit widget of type $_GET['edit'] and position $_GET['key']
	$key = (int) $_GET['key'];
	if ( -1 < $key && ( $keys = array_keys($sidebars_widgets[$sidebar], $_GET['edit']) ) && in_array($key, $keys) )
		$edit_widget = $key;
}

// Total number of registered sidebars
$sidebar_widget_count = count($sidebars_widgets[$sidebar]);

// This is sort of lame since "widget" won't be converted to "widgets" in the JS
if ( 1 < $sidebars_count = count($wp_registered_sidebars) )
	$sidebar_info_text = __ngettext( 'You are using %1$s widget in the "%2$s" sidebar.', 'You are using %1$s widgets in the "%2$s" sidebar.', $sidebar_widget_count );
else
	$sidebar_info_text = __ngettext( 'You are using %1$s widget in the sidebar.', 'You are using %1$s widgets in the sidebar.', $sidebar_widget_count );


$sidebar_info_text = sprintf( wp_specialchars( $sidebar_info_text ), "<span id='widget-count'>$sidebar_widget_count</span>", $wp_registered_sidebars[$sidebar]['name'] );

$page = isset($_GET['apage']) ? abs( (int) $_GET['apage'] ) : 1;

/* TODO: Paginate widgets list
$page_links = paginate_links( array(
	'base'    => add_query_arg( 'apage', '%#%' ),
	'format'  => '',
	'total'   => ceil(($total = 105 )/ 10),
	'current' => $page
));
*/
$page_links = '&nbsp;';

// Unsanitized!
$widget_search = isset($_GET['s']) ? $_GET['s'] : false;

// Not entirely sure what all should be here
$show_values = array(
	''       => $widget_search ? __( 'Show any widgets' ) : __( 'Show all widgets' ),
	'unused' => __( 'Show unused widgets' ),
	'used'   => __( 'Show used widgets' )
);

$show = isset($_GET['show']) && isset($show_values[$_GET['show']]) ? attribute_escape( $_GET['show'] ) : false;

$messages = array(
	'updated' => __('Changes saved.')
);

require_once( 'admin-header.php' ); ?>

<?php if ( isset($_GET['message']) && isset($messages[$_GET['message']]) ) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo wp_specialchars( $title ); ?></h2>

	<form id="widgets-filter" action="" method="get">

	<div class="widget-liquid-left-holder">
	<div id="available-widgets-filter" class="widget-liquid-left">
		<h3><label for="show"><?php _e('Available Widgets'); ?></label></h3>
		<div class="nav">
			<select name="show" id="show">
<?php foreach ( $show_values as $show_value => $show_text ) : $show_value = attribute_escape( $show_value ); ?>
				<option value='<?php echo $show_value; ?>'<?php selected( $show_value, $show ); ?>><?php echo wp_specialchars( $show_text ); ?></option>
<?php endforeach; ?>
			</select>
			<input type="submit" value="<?php _e('Show' ); ?>" class="button-secondary" />
			<p class="pagenav">
				<?php echo $page_links; ?>
			</p>
		</div>
	</div>
	</div>

	<div id="available-sidebars" class="widget-liquid-right">
		<h3><label for="sidebar-selector"><?php _e('Current Widgets'); ?></label></h3>

		<div class="nav">
			<select id="sidebar-selector" name="sidebar">
<?php foreach ( $wp_registered_sidebars as $sidebar_id => $registered_sidebar ) : $sidebar_id = attribute_escape( $sidebar_id ); ?>
				<option value='<?php echo $sidebar_id; ?>'<?php selected( $sidebar_id, $sidebar ); ?>><?php echo wp_specialchars( $registered_sidebar['name'] ); ?></option>
<?php endforeach; ?>
			</select>
			<input type="submit" value="<?php _e('Show' ); ?>" class="button-secondary" />
		</div>

	</div>

	</form>

	<div id="widget-content" class="widget-liquid-left-holder">

		<div id="available-widgets" class="widget-liquid-left">

			<?php wp_list_widgets( $show, $widget_search ); // This lists all the widgets for the query ( $show, $search ) ?>

			<div class="nav">
				<p class="pagenav">
					<?php echo $page_links; ?>
				</p>
			</div>
		</div>
	</div>

	<form id="widget-controls" action="" method="post">

	<div id="current-widgets-head" class="widget-liquid-right">

		<div id="sidebar-info">
			<p><?php echo $sidebar_info_text; ?></p>
			<p><?php _e( 'Add more from the Available Widgets section.' ); ?></p>
		</div>

	</div>

	<div id="current-widgets" class="widget-liquid-right">
		<div id="current-sidebar">

			<?php wp_list_widget_controls( $sidebar ); // Show the control forms for each of the widgets in this sidebar ?>

		</div>

		<p class="submit">
			<input type="hidden" id='sidebar' name='sidebar' value="<?php echo $sidebar; ?>" />
			<input type="hidden" id="generated-time" name="generated-time" value="<?php echo time() - 1199145600; // Jan 1, 2008 ?>" />
			<input type="submit" name="save-widgets" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" />
<?php
			wp_nonce_field( 'edit-sidebar_' . $sidebar );
?>
		</p>
	</div>

	</form>
	<br class="clear" />

</div>

<?php do_action( 'sidebar_admin_page' ); ?>

<?php require_once( 'admin-footer.php' ); ?>


