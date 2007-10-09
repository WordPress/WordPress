jQuery(function($) {
var delAfter; var extra; var list;

if ( document.location.href.match(/(\?|&)c=/) )
	delAfter = function() { $('#comments, #the-comment-list').remove(); }
else
	delAfter = function() {
		list[0].wpList.add( extra.children(':eq(0)').remove().clone() );
		$('#get-extra-button').click();
	}

var addBefore = function ( settings ) {
	var q = document.location.href.split('?');
	if ( q[1] )
		settings.data += '&' + q[1];
	return settings;
}

extra = $('#the-extra-list').wpList( { alt: '', addBefore: addBefore, addColor: 'none', delColor: 'none' } );
list = $('#the-list').wpList( { delAfter: delAfter, addColor: 'none' } );

} );
