<?php
	@require( '../wp-config.php' );
	cache_javascript_headers();
	
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
	for ( var n in Draggables.drags ) {
		if ( Draggables.drags[n].element.id == 'lastmodule' ) {
			Draggables.drags[n].destroy();
			break;
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
	var left = $('shadow').offsetWidth - (el.offsetWidth + left);
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
			Position.absolutize(o+'placematt');
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