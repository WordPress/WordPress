// this file contains all the scripts used in the post/edit page

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

addLoadEvent( function() {
	// postboxes
	add_postbox_toggles('post');

	// If no tags on the page, skip the tag and category stuff.
	if ( !jQuery('#tags-input').size() ) {
		return;	
	}

	// Editable slugs
	make_slugedit_clickable();

	jQuery('#tags-input').hide();
	tag_update_quickclicks();
	// add the quickadd form
	jQuery('#jaxtag').prepend('<span id="ajaxtag"><input type="text" name="newtag" id="newtag" class="form-input-tip" size="16" autocomplete="off" value="'+postL10n.addTag+'" /><input type="button" class="button" id="tagadd" value="' + postL10n.add + '"/><input type="hidden"/><input type="hidden"/><span class="howto">'+postL10n.separate+'</span></span>');
	jQuery('#tagadd').click( tag_flush_to_text );
	jQuery('#newtag').focus(function() {
		if ( this.value == postL10n.addTag )
			jQuery(this).val( '' ).removeClass( 'form-input-tip' );
	});
	jQuery('#newtag').blur(function() {
		if ( this.value == '' )
			jQuery(this).val( postL10n.addTag ).addClass( 'form-input-tip' );
	});

	// auto-suggest stuff
	jQuery('#newtag').suggest( 'admin-ajax.php?action=ajax-tag-search', { delay: 500, minchars: 2 } );
	jQuery('#newtag').keypress( tag_press_key );

	// category tabs
	var categoryTabs =jQuery('#category-tabs').tabs();

	// Ajax Cat
	var newCat = jQuery('#newcat').one( 'focus', function() { jQuery(this).val( '' ).removeClass( 'form-input-tip' ) } );
	jQuery('#category-add-sumbit').click( function() { newCat.focus(); } );
	var newCatParent = false;
	var newCatParentOption = false;
	var noSyncChecks = false; // prophylactic. necessary?
	var syncChecks = function() {
		if ( noSyncChecks )
			return;
		noSyncChecks = true;
		var th = jQuery(this);
		var c = th.is(':checked');
		var id = th.val().toString();
		jQuery('#in-category-' + id + ', #in-popular-category-' + id).attr( 'checked', c );
		noSyncChecks = false;
	};
	var catAddAfter = function( r, s ) {
		if ( !newCatParent ) newCatParent = jQuery('#newcat_parent');
		if ( !newCatParentOption ) newCatParentOption = newCatParent.find( 'option[value=-1]' );
		jQuery(s.what + ' response_data', r).each( function() {
			var t = jQuery(jQuery(this).text());
			t.find( 'label' ).each( function() {
				var th = jQuery(this);
				var val = th.find('input').val();
				var id = th.find('input')[0].id
				jQuery('#' + id).change( syncChecks );
				if ( newCatParent.find( 'option[value=' + val + ']' ).size() )
					return;
				var name = jQuery.trim( th.text() );
				var o = jQuery( '<option value="' +  parseInt( val, 10 ) + '"></option>' ).text( name );
				newCatParent.prepend( o );
			} );
			newCatParentOption.attr( 'selected', true );
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
	jQuery('.categorychecklist :checkbox').change( syncChecks ).filter( ':checked' ).change();
});

wpEditorInit = function() {
    // Activate tinyMCE if it's the user's default editor
    if ( ( 'undefined' == typeof wpTinyMCEConfig ) || 'tinymce' == wpTinyMCEConfig.defaultEditor ) {
        document.getElementById('editorcontainer').style.padding = '0px';
        tinyMCE.execCommand("mceAddControl", true, "content");
	} else {
        var H;
        if ( H = tinymce.util.Cookie.getHash("TinyMCE_content_size") ) 
            document.getElementById('content').style.height = H.ch - 30 + 'px';
    }
};

switchEditors = {
    
    saveCallback : function(el, content, body) {
    
        document.getElementById(el).style.color = '#fff';
        if ( tinyMCE.activeEditor.isHidden() ) 
            content = document.getElementById(el).value;
        else
            content = this.pre_wpautop(content);

        return content;
    },

    pre_wpautop : function(content) {
	   // We have a TON of cleanup to do.

        // content = content.replace(/\n|\r/g, ' ');
        // Remove anonymous, empty paragraphs.
        content = content.replace(new RegExp('<p>(\\s|&nbsp;|<br>)*</p>', 'mg'), '');

        // Mark </p> if it has any attributes.
        content = content.replace(new RegExp('(<p[^>]+>.*?)</p>', 'mg'), '$1</p#>');

        // Get it ready for wpautop.
        content = content.replace(new RegExp('\\s*<p>', 'mgi'), '');
        content = content.replace(new RegExp('\\s*</p>\\s*', 'mgi'), '\n\n');
        content = content.replace(new RegExp('\\n\\s*\\n', 'mgi'), '\n\n');
        content = content.replace(new RegExp('\\s*<br ?/?>\\s*', 'gi'), '\n');

        // Fix some block element newline issues
        var blocklist = 'blockquote|ul|ol|li|table|thead|tr|th|td|div|h\\d|pre';
        content = content.replace(new RegExp('\\s*<(('+blocklist+') ?[^>]*)\\s*>', 'mg'), '\n<$1>');
        content = content.replace(new RegExp('\\s*</('+blocklist+')>\\s*', 'mg'), '</$1>\n');
        content = content.replace(new RegExp('<li>', 'g'), '\t<li>');
		
        if ( content.indexOf('<object') != -1 ) {
            content = content.replace(new RegExp('\\s*<param([^>]*)>\\s*', 'g'), "<param$1>"); // no pee inside object/embed
            content = content.replace(new RegExp('\\s*</embed>\\s*', 'g'), '</embed>');
        }
		
        // Unmark special paragraph closing tags
        content = content.replace(new RegExp('</p#>', 'g'), '</p>\n');
        content = content.replace(new RegExp('\\s*(<p[^>]+>.*</p>)', 'mg'), '\n$1');

        // Trim trailing whitespace
        content = content.replace(new RegExp('\\s*$', ''), '');

        // Hope.
        return content;
    },

    go : function(id) {
        var ed = tinyMCE.get(id);
        var qt = document.getElementById('quicktags');
        var H = document.getElementById('edButtonHTML');
        var P = document.getElementById('edButtonPreview');
        var ta = document.getElementById(id);
        var ec = document.getElementById('editorcontainer');

        if ( ! ed || ed.isHidden() ) {
            ta.style.color = '#fff';
        
            this.edToggle(P, H);
            edCloseAllTags(); // :-(

            qt.style.display = 'none';
            ec.style.padding = '0px';

            ta.value = this.wpautop(ta.value);

            if ( ed ) ed.show();
            else tinyMCE.execCommand("mceAddControl", false, id);
        
            this.wpSetDefaultEditor( 'tinymce' );
        } else {
            this.edToggle(H, P);
            tinyMCE.triggerSave();
            ta.style.height = tinyMCE.activeEditor.contentAreaContainer.offsetHeight + 6 + 'px';

            if ( tinymce.isIE6 ) 
                ta.style.width = tinyMCE.activeEditor.contentAreaContainer.offsetWidth - 12 + 'px';

            ed.hide();
            ta.value = this.pre_wpautop(ta.value);
        
            qt.style.display = 'block';
            ec.style.padding = '6px';
            ta.style.color = '';

            this.wpSetDefaultEditor( 'html' );
        }
    },

    edToggle : function(A, B) {
        A.className = 'active';
        B.className = '';

        B.onclick = A.onclick;
        A.onclick = null;
    },

    wpSetDefaultEditor : function( editor ) {
        try {
            editor = escape( editor.toString() );
        } catch(err) {
            editor = 'tinymce';
        }

        var userID = document.getElementById('user-id');
        var date = new Date();
        date.setTime(date.getTime()+(10*365*24*60*60*1000));
        document.cookie = "wordpress_editor_" + userID.value + "=" + editor + "; expires=" + date.toGMTString();
    },

    wpautop : function(pee) {
        var blocklist = 'table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6]';
    
        pee = pee + "\n\n";
        pee = pee.replace(new RegExp('<br />\\s*<br />', 'gi'), "\n\n");
        pee = pee.replace(new RegExp('(<(?:'+blocklist+')[^>]*>)', 'gi'), "\n$1"); 
        pee = pee.replace(new RegExp('(</(?:'+blocklist+')>)', 'gi'), "$1\n\n");
        pee = pee.replace(new RegExp("\\r\\n|\\r", 'g'), "\n");
        pee = pee.replace(new RegExp("\\n\\s*\\n+", 'g'), "\n\n");
        pee = pee.replace(new RegExp('([\\s\\S]+?)\\n\\n', 'mg'), "<p>$1</p>\n");
        pee = pee.replace(new RegExp('<p>\\s*?</p>', 'gi'), '');
        pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')[^>]*>)\\s*</p>', 'gi'), "$1");
        pee = pee.replace(new RegExp("<p>(<li.+?)</p>", 'gi'), "$1");
        pee = pee.replace(new RegExp('<p><blockquote([^>]*)>', 'gi'), "<blockquote$1><p>");
        pee = pee.replace(new RegExp('</blockquote></p>', 'gi'), '</p></blockquote>');
        pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')[^>]*>)', 'gi'), "$1");
        pee = pee.replace(new RegExp('(</?(?:'+blocklist+')[^>]*>)\\s*</p>', 'gi'), "$1"); 
        pee = pee.replace(new RegExp('\\s*\\n', 'gi'), "<br />\n");
        pee = pee.replace(new RegExp('(</?(?:'+blocklist+')[^>]*>)\\s*<br />', 'gi'), "$1");
        pee = pee.replace(new RegExp('<br />(\\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)', 'gi'), '$1');
        pee = pee.replace(new RegExp('^((?:&nbsp;)*)\\s', 'mg'), '$1&nbsp;');
        //pee = pee.replace(new RegExp('(<pre.*?>)(.*?)</pre>!ise', " stripslashes('$1') .  stripslashes(clean_pre('$2'))  . '</pre>' "); // Hmm...
        return pee;
    }
}
