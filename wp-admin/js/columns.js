(function($) {
	columns = {
		init : function(page) {
			$('.hide-column-tog').click( function() {
				var column = jQuery(this).val();
				var show = jQuery(this).attr('checked');
				if ( show ) {
					jQuery('.column-' + column).show();
				} else {
					jQuery('.column-' + column).hide();
				}
				save_manage_columns_state(page);
			} );

			if ( $.browser.msie ) {
				$('#side-sortables').append( '<div id="make-it-tall"></div>' );
			} else {
				$('#side-sortables').append( '<div id="make-it-tall" style="margin-bottom: -2000px; padding-bottom: 2001px"></div>' );
			}
			$('#wpbody-content').css( 'overflow', 'hidden' );
		}
	}
}(jQuery));

function save_manage_columns_state(page) {
	var hidden = jQuery('.manage-column').filter(':hidden').map(function() { return this.id; }).get().join(',');
	jQuery.post(columnsL10n.requestFile, {
		action: 'hidden-columns',
		hidden: hidden,
		hiddencolumnsnonce: jQuery('#hiddencolumnsnonce').val(),
		page: page
	});
}