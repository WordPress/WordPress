var theList; var theExtraList;
jQuery(function($) {

var dimAfter = function( r, settings ) {
	$('li span.comment-count').each( function() {
		var a = $(this);
		var n = parseInt(a.html(),10) + ( $('#' + settings.element).is('.' + settings.dimClass) ? 1 : -1 );
		a.html( n.toString() );
	});
	// we need to do the opposite for this guy, TODO: update title
	$('.post-com-count span.comment-count').each( function() {
		var a = $(this);
		var n = parseInt(a.html(),10) + ( $('#' + settings.element).is('.' + settings.dimClass) ? -1 : 1 );
		a.html( n.toString() );
	});
}

var delAfter = function( r, settings ) {
	$('span.comment-count').each( function() {
		var a = $(this);
		if ( a.parent('.current').size() || $('#' + settings.element).is('.unapproved') && parseInt(a.html(),10) > 0 ) {
			var n = parseInt(a.html(),10) - 1;
			a.html( n.toString() );
		}
	});

	if ( theExtraList.size() == 0 || theExtraList.children().size() == 0 ) {
		return;
	}

	theList.get(0).wpList.add( theExtraList.children(':eq(0)').remove().clone() );
	$('#get-extra-comments').submit();
}

theExtraList = $('#the-extra-comment-list').wpList( { alt: '', delColor: 'none', addColor: 'none' } );
theList = $('#the-comment-list').wpList( { alt: '', dimAfter: dimAfter, delAfter: delAfter, addColor: 'none' } );

} );
