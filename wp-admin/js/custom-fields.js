jQuery( function($) {
	var before = function() {
		var nonce = $('#newmeta [@name=_ajax_nonce]').val();
		var postId = $('#post_ID').val();
		if ( !nonce || !postId ) { return false; }
		return [nonce,postId];
	}

	var addBefore = function( s ) {
		var b = before();
		if ( !b ) { return false; }
		s.data = s.data.replace(/_ajax_nonce=[a-f0-9]+/, '_ajax_nonce=' + b[0]) + '&post_id=' + b[1];
		return s;
	};

	var addAfter = function( r, s ) {
		var postId = $('postid', r).text();
		if ( !postId ) { return; }
		$('#post_ID').attr( 'name', 'post_ID' ).val( postId );
		var h = $('#hiddenaction');
		if ( 'post' == h.val() ) { h.val( 'postajaxpost' ); }
	};

	var delBefore = function( s ) {
		var b = before(); if ( !b ) return false;
		s.data._ajax_nonce = b[0]; s.data.post_id = b[1];
		return s;
	}

	$('#the-list')
		.wpList( { addBefore: addBefore, addAfter: addAfter, delBefore: delBefore } )
		.find('.updatemeta, .deletemeta').attr( 'type', 'button' );
} );
