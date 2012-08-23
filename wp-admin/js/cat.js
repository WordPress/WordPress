jQuery(document).ready( function($) {
	var myConfirm = function() { return '' !== $('#newcat').val(); };
	$('#jaxcat').prepend('<span id="ajaxcat"><input type="text" name="newcat" id="newcat" size="16" autocomplete="off"/><input type="button" name="Button" class="add:categorychecklist:jaxcat" id="catadd" value="' + catL10n.add + '"/><input type="hidden"/><input type="hidden"/><span id="howto">' + catL10n.how + '</span></span><span id="cat-ajax-response"></span>');
	$('#categorychecklist').wpList( { alt: '', response: 'cat-ajax-response', confirm: myConfirm } );
} );
