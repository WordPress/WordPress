jQuery(document).ready(function($) {
	var options = false, addAfter, addAfter2, delAfter;

	addAfter = function( r, settings ) {
		var name = $("<span>" + $('name', r).text() + "</span>").html(), id = $('tag', r).attr('id');
		options[options.length] = new Option(name, id);
	}

	addAfter2 = function( x, r ) {
		var t = $(r.parsed.responses[0].data);
		if ( t.length == 1 )
			inlineEditTax.addEvents($(t.id));
	}

	delAfter = function( r, settings ) {
		var id = $('tag', r).attr('id'), o;
		for ( o = 0; o < options.length; o++ )
			if ( id == options[o].value )
				options[o] = null;
	}

	if ( options )
		$('#the-list').wpList( { addAfter: addAfter, delAfter: delAfter } );
	else
		$('#the-list').wpList({ addAfter: addAfter2 });

	columns.init('edit-tags');
});