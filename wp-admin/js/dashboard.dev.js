
jQuery(document).ready( function($) {
	var ajaxWidgets, ajaxPopulateWidgets, quickPressLoad;
	// These widgets are sometimes populated via ajax
	ajaxWidgets = [
		'dashboard_incoming_links',
		'dashboard_primary',
		'dashboard_secondary',
		'dashboard_plugins'
	];

	ajaxPopulateWidgets = function() {
		$.each( ajaxWidgets, function() {
			var e = jQuery('#' + this + ':visible div.inside').find('.widget-loading');
			if ( e.size() ) { e.parent().load('index-extra.php?jax=' + this); }
		} );
	};
	ajaxPopulateWidgets();

	postboxes.add_postbox_toggles('dashboard', { onShow: ajaxPopulateWidgets } );

	/* QuickPress */
	quickPressLoad = function() {
		var act = $('#quickpost-action'), t;
		t = $('#quick-press').submit( function() {
			$('#dashboard_quick_press h3').append( '<img src="images/wpspin_light.gif" style="margin: 0 6px 0 0; vertical-align: middle" />' );
			$('#quick-press .submit input[type="submit"], #quick-press .submit input[type="reset"]').attr('disabled','disabled');

			if ( 'post' == act.val() ) {
				act.val( 'post-quickpress-publish' );
			}

			$('#dashboard_quick_press div.inside').load( t.attr( 'action' ), t.serializeArray(), function() {
				$('#dashboard_quick_press h3 img').remove();
				$('#quick-press .submit input[type="submit"], #quick-press .submit input[type="reset"]').attr('disabled','');
				$('#dashboard_quick_press ul').find('li').each( function() {
					$('#dashboard_recent_drafts ul').prepend( this );
				} ).end().remove();
				tb_init('a.thickbox');
				quickPressLoad();
			} );
			return false;
		} );

		$('#publish').click( function() { act.val( 'post-quickpress-publish' ); } );

	};
	quickPressLoad();

} );
