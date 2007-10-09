var list; var extra;
jQuery(function($) {

var dimAfter = function( r, settings ) {
	var a = $('#awaitmod');
	a.html( parseInt(a.html(),10) + ( $('#' + settings.element).is('.' + settings.dimClass) ? 1 : -1 ) );
}

var delAfter = function( r, settings ) {
	var a = $('#awaitmod');
	if ( $('#' + settings.element).is('.unapproved') && parseInt(a.html(),10) > 0 ) {
		a.html( parseInt(a.html(),10) - 1 );
	}

	if ( extra.size() == 0 || extra.children().size() == 0 ) {
		return;
	}

	list[0].wpList.add( extra.children(':eq(0)').remove().clone() );
	$('#get-extra-comments').submit();
}

extra = $('#the-extra-comment-list').wpList( { alt: '', delColor: 'none', addColor: 'none' } );
list = $('#the-comment-list').wpList( { dimAfter : dimAfter, delAfter : delAfter, addColor: 'none' } );

} );
