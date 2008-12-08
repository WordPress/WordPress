
jQuery( function($) {

// close postboxes that should be closed
jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');

// These widgets are sometimes populated via ajax
var ajaxWidgets = [
	'dashboard_incoming_links',
	'dashboard_primary',
	'dashboard_secondary',
	'dashboard_plugins'
];

var ajaxPopulateWidgets = function() {
	$.each( ajaxWidgets, function() {
		var e = jQuery('#' + this + ':visible div.inside').find('.widget-loading');
		if ( e.size() ) { e.parent().load('index-extra.php?jax=' + this); }
	} );
};
ajaxPopulateWidgets();

postboxes.add_postbox_toggles('dashboard', { onShow: ajaxPopulateWidgets } );

/* QuickPress */
var quickPressLoad = function() {
	var act = $('#quickpost-action');
	var t = $('#quick-press').submit( function() {
		$('#dashboard_quick_press h3').append( '<img src="images/loading.gif" style="margin: 0 6px 0 0; vertical-align: middle" />' );

		if ( 'post' == act.val() ) {
			act.val( 'post-quickpress-publish' );
		}

		$('#dashboard_quick_press div.inside').load( t.attr( 'action' ), t.serializeArray(), function() {
			$('#dashboard_quick_press h3 img').remove();
			$('#dashboard_quick_press ul').find('li').each( function() {
				$('#dashboard_recent_drafts ul').prepend( this );
			} ).end().remove();
			$(this).find('.hide-if-no-js').removeClass('hide-if-no-js');
			tb_init('a.thickbox');
			quickPressLoad();
		} );
		return false;
	} );

	$('#publish').click( function() { act.val( 'post-quickpress-publish' ); } );

};
quickPressLoad();

} );
