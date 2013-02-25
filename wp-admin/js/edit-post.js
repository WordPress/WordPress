(function($){
	$( document ).on( 'heartbeat-tick.wp-check-locked', function( e, data ) {
		var locked = data['wp-check-locked'] || {};
		
		$('#the-list tr').each( function(i, el) {
			var key = el.id, row = $(el);
			
			if ( locked.hasOwnProperty( key ) ) {
				if ( ! row.hasClass('wp-locked') )
					row.addClass('wp-locked').find('.column-title strong').after( $('<span class="lock-holder" />').text(locked[key]) );
					row.find('.check-column checkbox').prop('checked', false);
			} else if ( row.hasClass('wp-locked') ) {
				row.removeClass('wp-locked').find('.column-title span.lock-holder').remove();
			}
		});
	}).on( 'heartbeat-send.wp-check-locked', function( e, data ) {
		var check = [];
		
		$('#the-list tr').each( function(i, el) {
			check.push( el.id );
		});

		data['wp-check-locked'] = check;
	});
}(jQuery));
