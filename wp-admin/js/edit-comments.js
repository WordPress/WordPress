var theList; var theExtraList;
jQuery(function($) {

var dimAfter = function( r, settings ) {
	$('li span.comment-count').each( function() {
		var a = $(this);
		var n = parseInt(a.html(),10);
		n = n + ( $('#' + settings.element).is('.' + settings.dimClass) ? 1 : -1 );
		if ( n < 0 ) { n = 0; }
		a.html( n.toString() );
		$('#awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
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
		if ( n < 0 ) { n = 0; }
		if ( t < 0 ) { t = 0; }
		if ( t >= 0 ) { a.parent().attr('title', adminCommentsL10n.pending.replace( /%i%/, t.toString() ) ); }
		if ( 0 === t ) { a.parents('strong:first').replaceWith( a.parents('strong:first').html() ); }
		a.html( n.toString() );
	});
}

var delAfter = function( r, settings ) {
	$('li span.comment-count').each( function() {
		var a = $(this);
		var n = parseInt(a.html(),10);
		if ( $('#' + settings.element).is('.unapproved') ) { // we deleted a formerly unapproved comment
			n = n - 1;
		} else if ( $(settings.target).parents( 'span.unapprove' ).size() ) { // we "deleted" an approved comment from the approved list by clicking "Unapprove"
			n = n + 1;
		}
		if ( n < 0 ) { n = 0; }
		a.html( n.toString() );
		$('#awaiting-mod')[ 0 == n ? 'addClass' : 'removeClass' ]('count-0');
	});
	$('.post-com-count span.comment-count').each( function() {
		var a = $(this);
		if ( $('#' + settings.element).is('.unapproved') ) { // we deleted a formerly unapproved comment
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
