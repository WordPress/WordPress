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
	'name' => '',
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

// look for "lost" widgets, perhaps run only after changin themes
function retrieve_widgets() {
	global $wp_registered_widget_updates, $wp_registered_sidebars, $sidebars_widgets;

	$key_sidebars = array_keys( (array) $wp_registered_sidebars);
	$key_widgets = array_keys($sidebars_widgets);
	if ( count($key_widgets) > count($key_sidebars) ) {
		$changed_sidebars = array_diff( $key_widgets, $key_sidebars );
		foreach ( $changed_sidebars as $lost ) {
			if ( is_array($sidebars_widgets[$lost]) )
				$sidebars_widgets['wp_inactive_widgets'] = array_merge( (array) $sidebars_widgets['wp_inactive_widgets'], $sidebars_widgets[$lost] );
			unset($sidebars_widgets[$lost]);
		}
	}

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
	// If only the "wp_inactive_widgets" is defined the theme has no sidebars, die.
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

/*
// Unsanitized!
$widget_search = isset($_GET['s']) ? $_GET['s'] : false;

// Not entirely sure what all should be here
$show_values = array(
	''       => $widget_search ? __( 'Show any widgets' ) : __( 'Show all widgets' ),
	'unused' => __( 'Show unused widgets' ),
	'used'   => __( 'Show used widgets' )
);
*/

$show = isset($_GET['show']) && isset($show_values[$_GET['show']]) ? attribute_escape( $_GET['show'] ) : false;

$messages = array(
	'updated' => __('Changes saved.')
);

require_once( 'admin-header.php' ); ?>

<?php if ( isset($_GET['message']) && isset($messages[$_GET['message']]) ) : ?>
<div id="message" class="updated fade"><p><?php echo $messages[$_GET['message']]; ?></p></div>
<?php endif; ?>


<div id="message" class="updated"><p>This page is not 100% ready, please use only on test installations.</p></div>


<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo wp_specialchars( $title ); ?></h2>


<!--
	<form id="widgets-filter" action="" method="get">

	<div class="widget-liquid-left-holder">
	<div id="available-widgets-filter" class="widget-liquid-left">
		<h3><label for="show"><?php _e('Available Widgets'); ?></label></h3>
		<div class="nav">
			<select name="show" id="show">
<?php //foreach ( $show_values as $show_value => $show_text ) : $show_value = attribute_escape( $show_value ); ?>
				<option value='<?php //echo $show_value; ?>'<?php //selected( $show_value, $show ); ?>><?php //echo wp_specialchars( $show_text ); ?></option>
<?php //endforeach; ?>
			</select>
			<input type="submit" value="<?php _e('Show' ); ?>" class="button-secondary" />
			<p class="pagenav">
				<?php // echo $page_links; ?>
			</p>
		</div>
	</div>
	</div>

	<div id="available-sidebars" class="widget-liquid-right">
		<h3><label for="sidebar-selector"><?php _e('Current Widgets'); ?></label></h3>

		<div class="nav">
			<select id="sidebar-selector" name="sidebar">
<?php //foreach ( $wp_registered_sidebars as $sidebar_id => $registered_sidebar ) : $sidebar_id = attribute_escape( $sidebar_id ); ?>
				<option value='<?php //echo $sidebar_id; ?>'<?php selected( $sidebar_id, $open_sidebar ); ?>><?php //echo wp_specialchars( $registered_sidebar['name'] ); ?></option>
<?php //endforeach; ?>
			</select>
			<input type="submit" value="<?php _e('Show' ); ?>" class="button-secondary" />
		</div>

	</div>

	</form>
-->

	<div class="widget-liquid-left">
	<div id="widgets-left">
		<div id="available-widgets" class="widgets-holder-wrap">
			<h3 class="sidebar-name"><?php _e('Available Widgets'); ?></h3>
            <?php wp_list_widgets(); ?>
			<br class="clear" />
		</div>

		<div id="wp_inactive_widgets" class="widgets-holder-wrap">
			<h3 class="sidebar-name"><?php _e('Inactive Widgets'); ?>
			<span><img src="images/loading-publish.gif" class="ajax-feedback" title="" alt="" /></span></h3>
            <?php wp_list_widget_controls('wp_inactive_widgets'); ?>
			<br class="clear" />
		</div>
	</div>
	</div>

<!--
	<div id="current-widgets-head" class="widget-liquid-right">

		<div id="sidebar-info">
			<p><?php //echo $sidebar_info_text; ?></p>
			<p><?php _e( 'Add more from the Available Widgets section.' ); ?></p>
		</div>

	</div>
-->

	<div class="widget-liquid-right">
<?php
    $i = 0;
	foreach ( $wp_registered_sidebars as $sidebar => $registered_sidebar ) {
        if ( 'wp_inactive_widgets' == $sidebar )
            continue;
        ?>
		<div id="<?php echo attribute_escape( $sidebar ); ?>" class="widgets-holder-wrap">
		<h3 class="sidebar-name"><?php echo wp_specialchars( $registered_sidebar['name'] ); ?>
		<span><img src="images/loading-publish.gif" class="ajax-feedback" title="" alt="" /></span></h3>
		<?php wp_list_widget_controls( $sidebar, $i ); // Show the control forms for each of the widgets in this sidebar ?>
		</div>
<?php
	   $i++;
    } ?>
	</div>
	<form action="" method="post">
	<?php wp_nonce_field( 'save-sidebar-widgets', '_wpnonce_widgets', false ); ?>
	</form>
	<br class="clear" />
</div>

<?php
do_action( 'sidebar_admin_page' );
require_once( 'admin-footer.php' );
