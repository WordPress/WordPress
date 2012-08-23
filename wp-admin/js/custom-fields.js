jQuery(document).ready( function($) {
	var before, addBefore, addAfter, delBefore;

	before = function() {
		var nonce = $('#newmeta [name="_ajax_nonce"]').val(), postId = $('#post_ID').val();
		if ( !nonce || !postId ) { return false; }
		return [nonce,postId];
	}

	addBefore = function( s ) {
		var b = before();
		if ( !b ) { return false; }
		s.data = s.data.replace(/_ajax_nonce=[a-f0-9]+/, '_ajax_nonce=' + b[0]) + '&post_id=' + b[1];
		return s;
	};

	addAfter = function( r, s ) {
		var postId = $('postid', r).text(), h;
		if ( !postId ) { return; }
		$('#post_ID').attr( 'name', 'post_ID' ).val( postId );
		h = $('#hiddenaction');
		if ( 'post' == h.val() ) { h.val( 'postajaxpost' ); }
	};

	delBefore = function( s ) {
		var b = before(); if ( !b ) return false;
		s.data._ajax_nonce = b[0]; s.data.post_id = b[1];
		return s;
	}

	$('#the-list')
		.wpList( { addBefore: addBefore, addAfter: addAfter, delBefore: delBefore } )
		.find('.updatemeta, .deletemeta').attr( 'type', 'button' );
} );
