jQuery( document ).ready( function ( $ ) {

	function bbp_ajax_call( action, topic_id, nonce, update_selector ) {
		var $data = {
			action : action,
			id     : topic_id,
			nonce  : nonce
		};

		$.post( bbpTopicJS.bbp_ajaxurl, $data, function ( response ) {
			if ( response.success ) {
				$( update_selector ).html( response.content );
			} else {
				if ( !response.content ) {
					response.content = bbpTopicJS.generic_ajax_error;
				}
				alert( response.content );
			}
		} );
	}

	$( '#favorite-toggle' ).on( 'click', 'span a.favorite-toggle', function( e ) {
		e.preventDefault();
		bbp_ajax_call( 'favorite', $( this ).attr( 'data-topic' ), bbpTopicJS.fav_nonce, '#favorite-toggle' );
	} );

	$( '#subscription-toggle' ).on( 'click', 'span a.subscription-toggle', function( e ) {
		e.preventDefault();
		bbp_ajax_call( 'subscription', $( this ).attr( 'data-topic' ), bbpTopicJS.subs_nonce, '#subscription-toggle' );
	} );
} );
