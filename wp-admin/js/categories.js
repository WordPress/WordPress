jQuery(function($) {
	var options = false
	if ( document.forms['addcat'].category_parent )
		options = document.forms['addcat'].category_parent.options;

	var addAfter = function( r, settings ) {
		var name = $("<span>" + $('name', r).text() + "</span>").html();
		var id = $('cat', r).attr('id');
		options[options.length] = new Option(name, id);
	}

	var delAfter = function( r, settings ) {
		var id = $('cat', r).attr('id');
		for ( var o = 0; o < options.length; o++ )
			if ( id == options[o].value )
				options[o] = null;
	}

	if ( options )
		$('#the-list').wpList( { addAfter: addAfter, delAfter: delAfter } );
	else
		$('#the-list').wpList();

	if ( jQuery('#link-category-search').size() ) {
		columns.init('link-category');
	} else {
		columns.init('category');
	}

	// Edit Settings
	$('#show-settings-link').click(function () {
		$('#edit-settings').slideDown('normal', function(){
			$('#show-settings-link').hide();
			$('#hide-settings-link').show();
			
		});
		$('#show-settings').addClass('show-settings-opened');
		return false;
	});
	
	$('#hide-settings-link').click(function () {
		$('#edit-settings').slideUp('normal', function(){
			$('#hide-settings-link').hide();
			$('#show-settings-link').show();
			$('#show-settings').removeClass('show-settings-opened');
		});
		
		return false;
	});
});
