jQuery( function($) {
	var userLists; var list; var addBefore; var addAfter;

	addBefore = function( s ) {
		if ( $( '#role-' + $('#role').val() ).size() )
			return s;
		return false;
	};

	addAfter = function( r, s ) {
		var roleTable = $( '#role-' + $('role', r).text() );

		var e = $('#user-' + $('user', r).attr('id') );
		if ( !roleTable.size() ) { return; }
		if ( !e.size() ) { return; }

		roleTable[0].wpList.add(e.remove().clone());
	};	

	userLists = $('.user-list').wpList();
	list = $('#user-list').wpList( { addBefore: addBefore, addAfter: addAfter } );
} );
