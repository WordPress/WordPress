<?php

require_once 'admin.php';

wp_enqueue_script( 'scriptaculous-effects' );
wp_enqueue_script( 'scriptaculous-dragdrop' );

wp_register_script( 'widgets-admin', '/wp-admin/widgets.js.php', array( 'scriptaculous-effects', 'scriptaculous-dragdrop' ), '1.0' );
wp_enqueue_script( 'widgets-admin' );

function wp_widgets_admin_head() {
	global $wp_registered_sidebars, $wp_registered_widgets;
	
	define( 'WP_WIDGETS_WIDTH', 1 + 262 * ( count( $wp_registered_sidebars ) ) );
	define( 'WP_WIDGETS_HEIGHT', 35 * ( count( $wp_registered_widgets ) ) );
?>
	<style type="text/css">
	<?php include dirname( __FILE__ ) . '/widgets.css'; ?>
	</style>
<?php
}

add_action( 'admin_head', 'wp_widgets_admin_head' );
do_action( 'sidebar_admin_setup' );

function wp_widget_draggable( $name ) {
	global $wp_registered_widgets, $wp_registered_widget_controls;
	
	if ( !isset( $wp_registered_widgets[$name] ) ) {
		return;
	}
	
	$sanitized_name = sanitize_title( $wp_registered_widgets[$name]['id'] );
	$link_title = __( 'Configure' );
	$popper = ( isset( $wp_registered_widget_controls[$name] ) ) 
		? ' <div class="popper" id="' . $sanitized_name . 'popper" title="' . $link_title . '">&#8801;</div>'
		: '';
	
	$output = '<li class="module" id="widgetprefix-%1$s"><span class="handle">%2$s</span></li>';
	
	printf( $output, $sanitized_name, $name . $popper );
}

$title = __( 'Widgets' );
$parent_file = 'themes.php';

require_once 'admin-header.php';

if ( count( $wp_registered_sidebars ) < 1 ) {
?>
	<div class="wrap">
		<h2><?php _e( 'No Sidebars Defined' ); ?></h2>
		
		<p><?php _e( 'You are seeing this message because the theme you are currently using isn&#8217;t widget-aware, meaning that it has no sidebars that you are able to change. For information on making your theme widget-aware, please <a href="http://andy.wordpress.com/widgets/get-ready">follow these instructions</a>.' ); /* TODO: article on codex */; ?></p>
	</div>
<?php
	
	require_once 'admin-footer.php';
	exit;
}

$sidebars_widgets = wp_get_sidebars_widgets();

if ( empty( $sidebars_widgets ) ) {
	$sidebars_widgets = wp_get_widget_defaults();
}

if ( isset( $_POST['action'] ) ) {
	check_admin_referer( 'widgets-save-widget-order' );
	
	switch ( $_POST['action'] ) {
		case 'default' :
			$sidebars_widgets = wp_get_widget_defaults();
			wp_set_sidebars_widgets( $sidebars_widgets );
		break;
		
		case 'save_widget_order' :
			$sidebars_widgets = array();
			
			foreach ( $wp_registered_sidebars as $index => $sidebar ) {
				$postindex = $index . 'order';
				
				parse_str( $_POST[$postindex], $order );
				
				$new_order = $order[$index];
				
				if ( is_array( $new_order ) ) {
					foreach ( $new_order as $sanitized_name ) {
						foreach ( $wp_registered_widgets as $name => $widget ) {
							if ( $sanitized_name == $widget['id'] ) {
								$sidebars_widgets[$index][] = $name;
							}
						}
					}
				}
			}
			
			wp_set_sidebars_widgets( $sidebars_widgets );
		break;
	}
}

ksort( $wp_registered_widgets );

$inactive_widgets = array();

foreach ( $wp_registered_widgets as $name => $widget ) {
	$is_active = false;
	
	foreach ( $wp_registered_sidebars as $index => $sidebar ) {
		if ( is_array( $sidebars_widgets[$index] ) && in_array( $name, $sidebars_widgets[$index] ) ) {
			$is_active = true;
			break;
		}
	}
	
	if ( !$is_active ) {
		$inactive_widgets[] = $name;
	}
}

$containers = array( 'palette' );

foreach ( $wp_registered_sidebars as $index => $sidebar ) {
	$containers[] = $index;
}

$c_string = '';

foreach ( $containers as $container ) {
	$c_string .= '"' . $container . '",';
}

$c_string = substr( $c_string, 0, -1 );

if ( isset( $_POST['action'] ) ) {
?>
	<div class="fade updated" id="message">
		<p><?php printf( __( 'Sidebar updated. <a href="%s">View site &raquo;</a>' ), get_bloginfo( 'url' ) . '/' ); ?></p>
	</div>
<?php
}
?>
	<div class="wrap">
		<h2><?php _e( 'Sidebar Arrangement' ); ?></h2>
		
		<p><?php _e( 'You can drag and drop widgets onto your sidebar below.' ); ?></p>
		
		<form id="sbadmin" method="post" onsubmit="serializeAll();">
			<div id="zones">
			<?php
				foreach ( $wp_registered_sidebars as $index => $sidebar ) {
			?>
				<input type="hidden" id="<?php echo $index; ?>order" name="<?php echo $index; ?>order" value="" />
				
				<div class="dropzone">
					<h3><?php echo $sidebar['name']; ?></h3>
					
					<div id="<?php echo $index; ?>placematt" class="module placematt">
						<span class="handle">
							<h4><?php _e( 'Default Sidebar' ); ?></h4>
							<?php _e( 'Your theme will display its usual sidebar when this box is empty. Dragging widgets into this box will replace the usual sidebar with your customized sidebar.' ); ?>
						</span>
					</div>
					
					<ul id="<?php echo $index; ?>">
					<?php
						if ( is_array( $sidebars_widgets[$index] ) ) {
							foreach ( $sidebars_widgets[$index] as $name ) {
								wp_widget_draggable( $name );
							}
						}
					?>
					</ul>
				</div>
			<?php
				}
			?>
			
			<br class="clear" />
			
			</div>
		
			<div id="palettediv">
				<h3><?php _e( 'Available Widgets' ); ?></h3>
			
				<ul id="palette">
				<?php
					foreach ( $inactive_widgets as $name ) {
						wp_widget_draggable( $name );
					}
				?>
					<li id="lastmodule"><span></span></li>
				</ul>
			</div>
		
			<script type="text/javascript">
			// <![CDATA[
			<?php foreach ( $containers as $container ) { ?>
				Sortable.create("<?php echo $container; ?>", {
					dropOnEmpty: true, containment: [<?php echo $c_string; ?>], 
					handle: 'handle', constraint: false, onUpdate: updateAll, 
					format: /^widgetprefix-(.*)$/
				});
			<?php } ?>
			// ]]>
			</script>
		
			<p class="submit">
			<?php
				if ( function_exists( 'wp_nonce_field' ) ) {
					wp_nonce_field( 'widgets-save-widget-order' );
				}
			?>
				<input type="hidden" name="action" id="action" value="save_widget_order" />
				<input type="submit" value="<?php _e( 'Save Changes &raquo;' ); ?>" />
			</p>
		
			<div id="controls">
			<?php foreach ( $wp_registered_widget_controls as $name => $widget ) { ?>
				<div class="hidden" id="<?php echo $widget['id']; ?>control">
					<span class="controlhandle"><?php echo $name; ?></span>
					<span id="<?php echo $widget['id']; ?>closer" class="controlcloser">&#215;</span>
					<div class="controlform">
					<?php call_user_func_array( $widget['callback'], $widget['params'] ); ?>
					</div>
				</div>
			<?php } ?>
			</div>
		</form>
		
		<br class="clear" />
	</div>
	
	<div id="shadow"> </div>
	
	<?php do_action( 'sidebar_admin_page' ); ?>

<?php require_once 'admin-footer.php'; ?>