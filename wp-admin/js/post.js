// this file shoudl contain all the scripts used in the post/edit page

function new_tag_remove_tag() {
	var id = jQuery( this ).attr( 'id' );
	var num = id.substr( 10 );
	var current_tags = jQuery( '#tags-input' ).val().split(',');
	delete current_tags[num];
	var new_tags = [];
	jQuery.each( current_tags, function( key, val ) {
		if ( val && !val.match(/^\s+$/) && '' != val ) {
			new_tags = new_tags.concat( val );
		}
	});
	jQuery( '#tags-input' ).val( new_tags.join( ',' ).replace( /\s*,+\s*/, ',' ).replace( /,+/, ',' ).replace( /,+\s+,+/, ',' ).replace( /,+\s*$/, '' ).replace( /^\s*,+/, '' ) );
	tag_update_quickclicks();
	return false;
}

function tag_update_quickclicks() {
	var current_tags = jQuery( '#tags-input' ).val().split(',');
	jQuery( '#tagchecklist' ).empty();
	shown = false;
//	jQuery.merge( current_tags, current_tags ); // this doesn't work anymore, need something to array_unique
	jQuery.each( current_tags, function( key, val ) {
		val = val.replace( /^\s+/, '' ).replace( /\s+$/, '' ); // trim
		if ( !val.match(/^\s+$/) && '' != val ) { 
			txt = '<span><a id="tag-check-' + key + '" class="ntdelbutton">X</a>&nbsp;' + val + '</span> ';
			jQuery( '#tagchecklist' ).append( txt );
			jQuery( '#tag-check-' + key ).click( new_tag_remove_tag );
			shown = true;
		}
	});
	if ( shown )
		jQuery( '#tagchecklist' ).prepend( '<strong>'+postL10n.tagsUsed+'</strong><br />' );
}

function tag_flush_to_text() {
	var newtags = jQuery('#tags-input').val() + ',' + jQuery('#newtag').val();
	// massage
	newtags = newtags.replace( /\s+,+\s*/g, ',' ).replace( /,+/g, ',' ).replace( /,+\s+,+/g, ',' ).replace( /,+\s*$/g, '' ).replace( /^\s*,+/g, '' );
	jQuery('#tags-input').val( newtags );
	tag_update_quickclicks();
	jQuery('#newtag').val('');
	jQuery('#newtag').blur();
	return false;
}

function tag_press_key( e ) {
	if ( 13 == e.keyCode ) {
		tag_flush_to_text();
		return false;
	}
}

function add_postbox_toggles() {
	jQuery('.postbox h3').prepend('<a class="togbox">+</a> ');
	jQuery('.togbox').click( function() { jQuery(jQuery(this).parent().parent().get(0)).toggleClass('closed'); save_postboxes_state(); } );
}

function save_postboxes_state() {
	var closed = jQuery('.postbox').filter('.closed').map(function() { return this.id; }).get().join(',');
	jQuery.post(postL10n.requestFile, {
		action: 'closed-postboxes',
		closed: closed,
		cookie: document.cookie});
}

addLoadEvent( function() {
	jQuery('#tags-input').hide();
	tag_update_quickclicks();
	// add the quickadd form
	jQuery('#jaxtag').prepend('<span id="ajaxtag"><input type="text" name="newtag" id="newtag" size="16" autocomplete="off" value="'+postL10n.addTag+'" /><input type="button" class="button" id="tagadd" value="' + postL10n.add + '"/><input type="hidden"/><input type="hidden"/><span class="howto">'+postL10n.separate+'</span></span>');
	jQuery('#tagadd').click( tag_flush_to_text );
//	jQuery('#newtag').keydown( tag_press_key );
	jQuery('#newtag').focus(function() {
		if ( this.value == postL10n.addTag ) {
			this.value = '';
			this.style.color = '#333';
		}
	});
	jQuery('#newtag').blur(function() {
		if ( this.value == '' ) {
			this.value = postL10n.addTag;
			this.style.color = '#999'
		}
	});

	// auto-suggest stuff
	jQuery('#newtag').suggest( 'admin-ajax.php?action=ajax-tag-search', { onSelect: tag_flush_to_text, delay: 500, minchars: 2 } );

	// postboxes
	add_postbox_toggles();

	// category tabs
	var categoryTabs =jQuery('#category-tabs').tabs();

	// Ajax Cat
	var newCat = jQuery('#newcat').one( 'focus', function() { jQuery(this).val( '' ) } );
	jQuery('#category-add-sumbit').click( function() { newCat.focus(); } );
	var catAddAfter = function( r, s ) {
		jQuery(s.what + ' response_data', r).each( function() {
			var t = jQuery(jQuery(this).text());
			var o = jQuery( '<option value="' +  parseInt( t.find(':input').val(), 10 ) + '"></option>' );
			o.text( jQuery.trim( t.text() ) );
			jQuery('#newcat_parent').prepend( o );
		} );
	};
	jQuery('#categorychecklist').wpList( {
		alt: '',
		response: 'category-ajax-response',
		addAfter: catAddAfter
	} );
	jQuery('#category-add-toggle').click( function() {
		jQuery(this).parents('div:first').toggleClass( 'wp-hidden-children' );
		categoryTabs.tabsClick( 1 );
		return false;
	} );
});
