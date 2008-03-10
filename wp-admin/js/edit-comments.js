var theList; var theExtraList;
jQuery(function($) {

var dimAfter = function( r, settings ) {
	$('li span.comment-count').each( function() {
		var a = $(this);
		var n = parseInt(a.html(),10) + ( $('#' + settings.element).is('.' + settings.dimClass) ? 1 : -1 );
		a.html( n.toString() );
	});
	$('.post-com-count span.comment-count').each( function() {
		var a = $(this);
		var n = parseInt(a.html(),10);
		var t = parseInt(a.parent().attr('title'), 10);
		if ( $('#' + settings.element).is('.unapproved') ) { // we unapproved a formerly approved comment
			n = n - 1;
			t = t + 1;
		} else { // we approved a formerly unapproved comment
			n = n + 1;
			t = t - 1;
		}
		if ( t >= 0 ) { a.parent().attr('title', adminCommentsL10n.pending.replace( /%i%/, t.toString() ) ); }
		if ( 0 === t ) { a.parents('strong:first').replaceWith( a.parents('strong:first').html() ); }
		a.html( n.toString() );
	});
}

var delAfter = function( r, settings ) {
	$('li span.comment-count').each( function() {
		var a = $(this);
		if ( parseInt(a.html(),10) < 1 ) { return; }
		// on ?edit-comments.php?comment_status=moderated tab
		// or the comment is unapproved
		if ( a.parent('.current').size() || $('#' + settings.element).is('.unapproved') ) {
			var n = parseInt(a.html(),10) - 1;
			a.html( n.toString() );
			( 0 < n ) ? $('#awaiting-mod').each(function() { $(this).show(); $(this).removeClass('count-0') }) : $('#awaiting-mod').hide();
		}
	});
	$('.post-com-count span.comment-count').each( function() {
		var a = $(this);
		if ( $('#' + settings.element).is('.unapproved') ) { // we deleted an unapproved comment, decrement pending title
			var t = parseInt(a.parent().attr('title'), 10);
			if ( t < 1 ) { return; }
			t = t - 1;
			a.parent().attr('title', adminCommentsL10n.pending.replace( /%i%/, t.toString() ) );
			if ( 0 === t ) { a.parents('strong:first').replaceWith( a.parents('strong:first').html() ); }
			return;
		}
		var n = parseInt(a.html(),10) - 1;
		a.html( n.toString() );
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
