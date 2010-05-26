jQuery(document).ready( function($) {

	var newCat, noSyncChecks = false, syncChecks, catAddAfter;

	$('#link_name').focus();
	// postboxes
	postboxes.add_postbox_toggles('link');

	// category tabs
	$('#category-tabs a').click(function(){
		var t = $(this).attr('href');
		$(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
		$('.tabs-panel').hide();
		$(t).show();
		if ( '#categories-all' == t )
			deleteUserSetting('cats');
		else
			setUserSetting('cats','pop');
		return false;
	});
	if ( getUserSetting('cats') )
		$('#category-tabs a[href="#categories-pop"]').click();

	// Ajax Cat
	newCat = $('#newcat').one( 'focus', function() { $(this).val( '' ).removeClass( 'form-input-tip' ) } );
	$('#category-add-submit').click( function() { newCat.focus(); } );
	syncChecks = function() {
		if ( noSyncChecks )
			return;
		noSyncChecks = true;
		var th = $(this), c = th.is(':checked'), id = th.val().toString();
		$('#in-link-category-' + id + ', #in-popular-category-' + id).attr( 'checked', c );
		noSyncChecks = false;
	};

	catAddAfter = function( r, s ) {
		$(s.what + ' response_data', r).each( function() {
			var t = $($(this).text());
			t.find( 'label' ).each( function() {
				var th = $(this), val = th.find('input').val(), id = th.find('input')[0].id, name = $.trim( th.text() ), o;
				$('#' + id).change( syncChecks );
				o = $( '<option value="' +  parseInt( val, 10 ) + '"></option>' ).text( name );
			} );
		} );
	};

	$('#categorychecklist').wpList( {
		alt: '',
		what: 'link-category',
		response: 'category-ajax-response',
		addAfter: catAddAfter
	} );

	$('a[href="#categories-all"]').click(function(){deleteUserSetting('cats');});
	$('a[href="#categories-pop"]').click(function(){setUserSetting('cats','pop');});
	if ( 'pop' == getUserSetting('cats') )
		$('a[href="#categories-pop"]').click();

	$('#category-add-toggle').click( function() {		
		$(this).parents('div:first').toggleClass( 'wp-hidden-children' );
		$('#category-tabs a[href="#categories-all"]').click();
		$('#newcategory').focus();
		return false;
	} );

	$('.categorychecklist :checkbox').change( syncChecks ).filter( ':checked' ).change();
});
