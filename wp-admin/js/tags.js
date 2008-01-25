jQuery(function($) {
	var options = false

	var addAfter = function( r, settings ) {
		var name = $("<span>" + $('name', r).text() + "</span>").html();
		var id = $('tag', r).attr('id');
		options[options.length] = new Option(name, id);
	}

	var delAfter = function( r, settings ) {
		var id = $('tag', r).attr('id');
		for ( var o = 0; o < options.length; o++ )
			if ( id == options[o].value )
				options[o] = null;
	}

	if ( options )
		$('#the-list').wpList( { addAfter: addAfter, delAfter: delAfter } );
	else
		$('#the-list').wpList();
});
