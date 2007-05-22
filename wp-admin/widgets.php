<?php

require_once 'admin.php';

if ( ! current_user_can('switch_themes') )
	wp_die( __( 'Cheatin&#8217; uh?' ));

wp_enqueue_script( 'scriptaculous-effects' );
wp_enqueue_script( 'scriptaculous-dragdrop' );

function wp_widgets_admin_head() {
	global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls;
	
	define( 'WP_WIDGETS_WIDTH', 1 + 262 * ( count( $wp_registered_sidebars ) ) );
	define( 'WP_WIDGETS_HEIGHT', 35 * ( count( $wp_registered_widgets ) ) );
?>
	<link rel="stylesheet" href="widgets.css?version=<?php bloginfo('version'); ?>" type="text/css" />
	<!--[if IE 7]>
	<style type="text/css">
	#palette {float:left;}
	</style>
	<![endif]-->
	<style type="text/css">
		.dropzone ul { height: <?php echo constant( 'WP_WIDGETS_HEIGHT' ); ?>px; }
		#sbadmin #zones { width: <?php echo constant( 'WP_WIDGETS_WIDTH' ); ?>px; }
	</style>
<?php
	if ( get_bloginfo( 'text_direction' ) == 'rtl' ) { 
?>
	<link rel="stylesheet" href="widgets-rtl.css?version=<?php bloginfo('version'); ?>" type="text/css" />
<?php
	}
	
	$cols = array();
	foreach ( $wp_registered_sidebars as $index => $sidebar ) {
		$cols[] = '\'' . $index . '\'';
	}
	$cols = implode( ', ', $cols );
	
	$widgets = array();
	foreach ( $wp_registered_widgets as $name => $widget ) {
		$widgets[] = '\'' . $widget['id'] . '\'';
	}
	$widgets = implode( ', ', $widgets );
?>
<script type="text/javascript">
// <![CDATA[
	var cols = [<?php echo $cols; ?>];
	var widgets = [<?php echo $widgets; ?>];
	var controldims = new Array;
	<?php foreach ( $wp_registered_widget_controls as $name => $widget ) : ?>
		controldims['<?php echo $widget['id']; ?>control'] = new Array;
		controldims['<?php echo $widget['id']; ?>control']['width'] = <?php echo (int) $widget['width']; ?>;
		controldims['<?php echo $widget['id']; ?>control']['height'] = <?php echo (int) $widget['height']; ?>;
	<?php endforeach; ?>
	function initWidgets() {
	<?php foreach ( $wp_registered_widget_controls as $name => $widget ) : ?>
		$('<?php echo $widget['id']; ?>popper').onclick = function() {popControl('<?php echo $widget['id']; ?>control');};
		$('<?php echo $widget['id']; ?>closer').onclick = function() {unpopControl('<?php echo $widget['id']; ?>control');};
		new Draggable('<?php echo $widget['id']; ?>control', {revert:false,handle:'controlhandle',starteffect:function(){},endeffect:function(){},change:function(o){dragChange(o);}});
		if ( true && window.opera )
			$('<?php echo $widget['id']; ?>control').style.border = '1px solid #bbb';
	<?php endforeach; ?>
		if ( true && window.opera )
			$('shadow').style.background = 'transparent';
		new Effect.Opacity('shadow', {to:0.0});
		widgets.map(function(o) {o='widgetprefix-'+o; Position.absolutize(o); Position.relativize(o);} );
		$A(Draggables.drags).map(function(o) {o.startDrag(null); o.finishDrag(null);});
		//for ( var n in Draggables.drags ) {
		for ( n=0; n<=Draggables.drags.length; n++ ) {
			if ( parseInt( n ) ) {
				if ( Draggables.drags[n].element.id == 'lastmodule' ) {
					Draggables.drags[n].destroy();
					break;
				}
			}
		}
		resetPaletteHeight();
	}
	function resetDroppableHeights() {
		var max = 6;
		cols.map(function(o) {var c = $(o).childNodes.length; if ( c > max ) max = c;} );
		var height = 35 * ( max + 1);
		cols.map(function(o) {h = (($(o).childNodes.length + 1) * 35); $(o).style.height = (h > 280 ? h : 280) + 'px';} );
	}
	function resetPaletteHeight() {
		var p = $('palette'), pd = $('palettediv'), last = $('lastmodule');
		p.appendChild(last);
		if ( Draggables.activeDraggable && last.id == Draggables.activeDraggable.element.id )
			last = last.previousSibling;
		var y1 = Position.cumulativeOffset(last)[1] + last.offsetHeight;
		var y2 = Position.cumulativeOffset(pd)[1] + pd.offsetHeight;
		var dy = y1 - y2;
		pd.style.height = (pd.offsetHeight + dy + 9) + "px";
	}
	function maxHeight(elm) {
		htmlheight = document.body.parentNode.clientHeight;
		bodyheight = document.body.clientHeight;
		var height = htmlheight > bodyheight ? htmlheight : bodyheight;
		$(elm).style.height = height + 'px';
	}
	function dragChange(o) {
		el = o.element ? o.element : $(o);
		var p = Position.page(el);
		var right = p[0];
		var top = p[1];
		var left = $('shadow').offsetWidth - (el.offsetWidth + right);
		var bottom = $('shadow').offsetHeight - (el.offsetHeight + top);
		if ( right < 1 ) el.style.left = 0;
		if ( top < 1 ) el.style.top = 0;
		if ( left < 1 ) el.style.left = (left + right) + 'px';
		if ( bottom < 1 ) el.style.top = (top + bottom) + 'px';
	}
	function popControl(elm) {
		el = $(elm);
		el.style.width = controldims[elm]['width'] + 'px';
		el.style.height = controldims[elm]['height'] + 'px';
		var x = ( document.body.clientWidth - controldims[elm]['width'] ) / 2;
		var y = ( document.body.parentNode.clientHeight - controldims[elm]['height'] ) / 2;
		el.style.position = 'absolute';
		el.style.right = '' + x + 'px';
		el.style.top = '' + y + 'px';
		el.style.zIndex = 1000;
		el.className='control';
		$('shadow').onclick = function() {unpopControl(elm);};
	    window.onresize = function(){maxHeight('shadow');dragChange(elm);};
		popShadow();
	}
	function popShadow() {
		maxHeight('shadow');
		var shadow = $('shadow');
		shadow.style.zIndex = 999;
		shadow.style.display = 'block';
	    new Effect.Opacity('shadow', {duration:0.5, from:0.0, to:0.2});
	}
	function unpopShadow() {
	    new Effect.Opacity('shadow', {to:0.0});
		$('shadow').style.display = 'none';
	}
	function unpopControl(el) {
		$(el).className='hidden';
		unpopShadow();
	}
	function serializeAll() {
	<?php foreach ( $wp_registered_sidebars as $index => $sidebar ) : ?>
		$('<?php echo $index; ?>order').value = Sortable.serialize('<?php echo $index; ?>');
	<?php endforeach; ?>
	}
	function updateAll() {
		resetDroppableHeights();
		resetPaletteHeight();
		cols.map(function(o){
			var pm = $(o+'placematt');
			if ( $(o).childNodes.length == 0 ) {
				pm.style.display = 'block';
				//Position.absolutize(o+'placematt');
			} else {
				pm.style.display = 'none';
			}
		});
	}
	function noSelection(event) {
		if ( document.selection ) {
			var range = document.selection.createRange();
			range.collapse(false);
			range.select();
			return false;
		}
	}
	addLoadEvent(updateAll);
	addLoadEvent(initWidgets);
	Event.observe(window, 'resize', resetPaletteHeight);
// ]]>
</script>
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
	
	printf( $output, $sanitized_name, $wp_registered_widgets[$name]['name'] . $popper );
}

$title = __( 'Widgets' );
$parent_file = 'themes.php';

require_once 'admin-header.php';

if ( count( $wp_registered_sidebars ) < 1 ) {
?>
	<div class="wrap">
		<h2><?php _e( 'No Sidebars Defined' ); ?></h2>
		
		<p><?php _e( 'You are seeing this message because the theme you are currently using isn&#8217;t widget-aware, meaning that it has no sidebars that you are able to change. For information on making your theme widget-aware, please <a href="http://automattic.com/code/widgets/themes/">follow these instructions</a>.' ); /* TODO: article on codex */; ?></p>
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
			<p class="submit">
				<input type="submit" value="<?php _e( 'Save Changes &raquo;' ); ?>" />
			</p>
			<div id="zones">
			<?php
				foreach ( $wp_registered_sidebars as $index => $sidebar ) {
			?>
				<input type="hidden" id="<?php echo $index; ?>order" name="<?php echo $index; ?>order" value="" />
				
				<div class="dropzone">
					<h3><?php echo $sidebar['name']; ?></h3>
					
					<div id="<?php echo $index; ?>placematt" class="module placemat">
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
			<?php wp_nonce_field( 'widgets-save-widget-order' ); ?>
				<input type="hidden" name="action" id="action" value="save_widget_order" />
				<input type="submit" value="<?php _e( 'Save Changes &raquo;' ); ?>" />
			</p>
		
			<div id="controls">
			<?php foreach ( $wp_registered_widget_controls as $name => $widget ) { ?>
				<div class="hidden" id="<?php echo $widget['id']; ?>control">
					<span class="controlhandle"><?php echo $widget['name']; ?></span>
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
