<?php

require_once 'admin.php';

if ( ! current_user_can('switch_themes') )
	wp_die( __( 'Cheatin&#8217; uh?' ));

wp_enqueue_script('interface');

function wp_widgets_admin_head() {
	global $wp_registered_sidebars, $wp_registered_widgets, $wp_registered_widget_controls;
?>
	<?php wp_admin_css( 'css/widgets' ); ?>
	<!--[if IE 7]>
	<style type="text/css">
		#palette { float: <?php echo ( get_bloginfo( 'text_direction' ) == 'rtl' ) ? 'right' : 'left'; ?>; }
	</style>
	<![endif]-->
<?php

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
		controldims['#<?php echo $widget['id']; ?>control'] = new Array;
		controldims['#<?php echo $widget['id']; ?>control']['width'] = <?php echo (int) $widget['width']; ?>;
		controldims['#<?php echo $widget['id']; ?>control']['height'] = <?php echo (int) $widget['height']; ?>;
	<?php endforeach; ?>
	function initWidgets() {
	<?php foreach ( $wp_registered_widget_controls as $name => $widget ) : ?>
		jQuery('#<?php echo $widget['id']; ?>popper').click(function() {popControl('#<?php echo $widget['id']; ?>control');});
		jQuery('#<?php echo $widget['id']; ?>closer').click(function() {unpopControl('#<?php echo $widget['id']; ?>control');});
		jQuery('#<?php echo $widget['id']; ?>control').Draggable({handle: '.controlhandle', zIndex: 1000});
		if ( true && window.opera )
			jQuery('#<?php echo $widget['id']; ?>control').css('border','1px solid #bbb');
	<?php endforeach; ?>
		jQuery('#shadow').css('opacity','0');
		jQuery(widgets).each(function(o) {o='#widgetprefix-'+o; jQuery(o).css('position','relative');} );
	}
	function resetDroppableHeights() {
		var max = 6;
		jQuery.map(cols, function(o) {
			var c = jQuery('#' + o + ' li').length;
			if ( c > max ) max = c;
		});
		var maxheight = 35 * ( max + 1);
		jQuery.map(cols, function(o) {
			height = 0 == jQuery('#' + o + ' li').length ? maxheight - jQuery('#' + o + 'placemat').height() : maxheight;
			jQuery('#' + o).height(height);
		});
	}
	function maxHeight(elm) {
		htmlheight = document.body.parentNode.clientHeight;
		bodyheight = document.body.clientHeight;
		var height = htmlheight > bodyheight ? htmlheight : bodyheight;
		jQuery(elm).height(height);
	}
	function getViewportDims() {
		var x,y;
		if (self.innerHeight) { // all except Explorer
			x = self.innerWidth;
			y = self.innerHeight;
		} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
			x = document.documentElement.clientWidth;
			y = document.documentElement.clientHeight;
		} else if (document.body) { // other Explorers
			x = document.body.clientWidth;
			y = document.body.clientHeight;
		}
		return new Array(x,y);
	}
	function dragChange(o) {
		var p = getViewportDims();
		var screenWidth = p[0];
		var screenHeight = p[1];
		var elWidth = parseInt( jQuery(o).css('width') );
		var elHeight = parseInt( jQuery(o).css('height') );
		var elLeft = parseInt( jQuery(o).css('left') );
		var elTop = parseInt( jQuery(o).css('top') );
		if ( screenWidth < ( parseInt(elLeft) + parseInt(elWidth) ) )
			jQuery(o).css('left', ( screenWidth - elWidth ) + 'px' );
		if ( screenHeight < ( parseInt(elTop) + parseInt(elHeight) ) )
			jQuery(o).css('top', ( screenHeight - elHeight ) + 'px' );
		if ( elLeft < 1 )
			jQuery(o).css('left', '1px');
		if ( elTop < 1 )
			jQuery(o).css('top', '1px');
	}
	function popControl(elm) {
		var x = ( document.body.clientWidth - controldims[elm]['width'] ) / 2;
		var y = ( document.body.parentNode.clientHeight - controldims[elm]['height'] ) / 2;
		jQuery(elm).css({display: 'block', width: controldims[elm]['width'] + 'px', height: controldims[elm]['height'] + 'px', position: 'absolute', right: x + 'px', top: y + 'px', zIndex: '1000' });
		jQuery(elm).attr('class','control');
		jQuery('#shadow').click(function() {unpopControl(elm);});
		window.onresize = function(){maxHeight('#shadow');dragChange(elm);};
		popShadow();
	}
	function popShadow() {
		maxHeight('#shadow');
		jQuery('#shadow').css({zIndex: '999', display: 'block'});
		jQuery('#shadow').fadeTo('fast', 0.2);
	}
	function unpopShadow() {
		jQuery('#shadow').fadeOut('fast', function() {jQuery('#shadow').hide()});
	}
	function unpopControl(el) {
		jQuery(el).attr('class','hidden');
		jQuery(el).hide();
		unpopShadow();
	}
	function serializeAll() {
	<?php $i = 0; foreach ( $wp_registered_sidebars as $index => $sidebar ) : $i++; ?>
		var serial<?php echo $i ?> = jQuery.SortSerialize('<?php echo $index ?>');
		jQuery('#<?php echo $index ?>order').attr('value',serial<?php echo $i ?>.hash.replace(/widgetprefix-/g, ''));
	<?php endforeach; ?>
	}
	function updateAll() {
		jQuery.map(cols, function(o) {
			if ( jQuery('#' + o + ' li').length )
				jQuery('#'+o+'placemat span.handle').hide();
			else
				jQuery('#'+o+'placemat span.handle').show();
		});
		resetDroppableHeights();
	}
	jQuery(document).ready( function() {
		updateAll();
		initWidgets();
	});
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

					<div id="<?php echo $index; ?>placemat" class="placemat">
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

			</div>

			<div id="palettediv">
				<h3><?php _e( 'Available Widgets' ); ?></h3>

				<ul id="palette">
				<?php
					foreach ( $inactive_widgets as $name ) {
						wp_widget_draggable( $name );
					}
				?>
				</ul>
			</div>

			<script type="text/javascript">
			// <![CDATA[
				jQuery(document).ready(function(){
			<?php foreach ( $containers as $container ) { ?>
					jQuery('ul#<?php echo $container; ?>').Sortable({
						accept: 'module', activeclass: 'activeDraggable', opacity: 0.8, revert: true, onStop: updateAll
					});
			<?php } ?>
				});
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
