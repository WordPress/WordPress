var autosave, autosaveLast = '', autosavePeriodical, autosaveDelayPreview = false, notSaved = true, blockSave = false, fullscreen, autosaveLockRelease = true;

jQuery(document).ready( function($) {

	if ( $('#wp-content-wrap').hasClass('tmce-active') && typeof switchEditors != 'undefined' ) {
		autosaveLast = wp.autosave.getCompareString({
			post_title : $('#title').val() || '',
			content : switchEditors.pre_wpautop( $('#content').val() ) || '',
			excerpt : $('#excerpt').val() || ''
		});
	} else {
		autosaveLast = wp.autosave.getCompareString();
	}

	autosavePeriodical = $.schedule({time: autosaveL10n.autosaveInterval * 1000, func: function() { autosave(); }, repeat: true, protect: true});

	//Disable autosave after the form has been submitted
	$("#post").submit(function() {
		$.cancel(autosavePeriodical);
		autosaveLockRelease = false;
	});

	$('input[type="submit"], a.submitdelete', '#submitpost').click(function(){
		blockSave = true;
		window.onbeforeunload = null;
		$(':button, :submit', '#submitpost').each(function(){
			var t = $(this);
			if ( t.hasClass('button-primary') )
				t.addClass('button-primary-disabled');
			else
				t.addClass('button-disabled');
		});
		if ( $(this).attr('id') == 'publish' )
			$('#major-publishing-actions .spinner').show();
		else
			$('#minor-publishing .spinner').show();
	});

	window.onbeforeunload = function(){
		var editor = typeof(tinymce) != 'undefined' ? tinymce.activeEditor : false, compareString;

		if ( editor && ! editor.isHidden() ) {
			if ( editor.isDirty() )
				return autosaveL10n.saveAlert;
		} else {
			if ( fullscreen && fullscreen.settings.visible ) {
				compareString = wp.autosave.getCompareString({
					post_title: $('#wp-fullscreen-title').val() || '',
					content: $('#wp_mce_fullscreen').val() || '',
					excerpt: $('#excerpt').val() || ''
				});
			} else {
				compareString = wp.autosave.getCompareString();
			}

			if ( compareString != autosaveLast )
				return autosaveL10n.saveAlert;
		}
	};

	$(window).unload( function(e) {
		if ( ! autosaveLockRelease )
			return;

		// unload fires (twice) on removing the Thickbox iframe. Make sure we process only the main document unload.
		if ( e.target && e.target.nodeName != '#document' )
			return;

		$.ajax({
			type: 'POST',
			url: ajaxurl,
			async: false,
			data: {
				action: 'wp-remove-post-lock',
				_wpnonce: $('#_wpnonce').val(),
				post_ID: $('#post_ID').val(),
				active_post_lock: $('#active_post_lock').val()
			}
		});
	} );

	// preview
	$('#post-preview').click(function(){
		if ( $('#auto_draft').val() == '1' && notSaved ) {
			autosaveDelayPreview = true;
			autosave();
			return false;
		}
		doPreview();
		return false;
	});

	doPreview = function() {
		$('input#wp-preview').val('dopreview');
		$('form#post').attr('target', 'wp-preview').submit().attr('target', '');

		/*
		 * Workaround for WebKit bug preventing a form submitting twice to the same action.
		 * https://bugs.webkit.org/show_bug.cgi?id=28633
		 */
		var ua = navigator.userAgent.toLowerCase();
		if ( ua.indexOf('safari') != -1 && ua.indexOf('chrome') == -1 ) {
			$('form#post').attr('action', function(index, value) {
				return value + '?t=' + new Date().getTime();
			});
		}

		$('input#wp-preview').val('');
	}

	// This code is meant to allow tabbing from Title to Post content.
	$('#title').on('keydown.editor-focus', function(e) {
		var ed;

		if ( e.which != 9 )
			return;

		if ( !e.ctrlKey && !e.altKey && !e.shiftKey ) {
			if ( typeof(tinymce) != 'undefined' )
				ed = tinymce.get('content');

			if ( ed && !ed.isHidden() ) {
				$(this).one('keyup', function(e){
					$('#content_tbl td.mceToolbar > a').focus();
				});
			} else {
				$('#content').focus();
			}

			e.preventDefault();
		}
	});

	// autosave new posts after a title is typed but not if Publish or Save Draft is clicked
	if ( '1' == $('#auto_draft').val() ) {
		$('#title').blur( function() {
			if ( !this.value || $('#auto_draft').val() != '1' )
				return;
			delayed_autosave();
		});
	}

	// When connection is lost, keep user from submitting changes.
	$(document).on('heartbeat-connection-lost.autosave', function( e, error ) {
		if ( 'timeout' === error ) {
			var notice = $('#lost-connection-notice');
			if ( ! wp.autosave.local.hasStorage ) {
				notice.find('.hide-if-no-sessionstorage').hide();
			}
			notice.show();
			autosave_disable_buttons();
		}
	}).on('heartbeat-connection-restored.autosave', function() {
		$('#lost-connection-notice').hide();
		autosave_enable_buttons();
	});
});

function autosave_parse_response( response ) {
	var res = wpAjax.parseAjaxResponse(response, 'autosave'), post_id, sup;

	if ( res && res.responses && res.responses.length ) {
		if ( res.responses[0].supplemental ) {
			sup = res.responses[0].supplemental;

			jQuery.each( sup, function( selector, value ) {
				if ( selector.match(/^replace-/) )
					jQuery( '#' + selector.replace('replace-', '') ).val( value );
			});
		}

		// if no errors: add slug UI and update autosave-message
		if ( !res.errors ) {
			if ( post_id = parseInt( res.responses[0].id, 10 ) )
				autosave_update_slug( post_id );

			if ( res.responses[0].data ) // update autosave message
				jQuery('.autosave-message').text( res.responses[0].data );
		}
	}

	return res;
}

// called when autosaving pre-existing post
function autosave_saved(response) {
	blockSave = false;
	autosave_parse_response(response); // parse the ajax response
	autosave_enable_buttons(); // re-enable disabled form buttons
}

// called when autosaving new post
function autosave_saved_new(response) {
	blockSave = false;
	var res = autosave_parse_response(response), post_id;

	if ( res && res.responses.length && !res.errors ) {
		// An ID is sent only for real auto-saves, not for autosave=0 "keepalive" saves
		post_id = parseInt( res.responses[0].id, 10 );

		if ( post_id ) {
			notSaved = false;
			jQuery('#auto_draft').val('0'); // No longer an auto-draft
		}

		autosave_enable_buttons();

		if ( autosaveDelayPreview ) {
			autosaveDelayPreview = false;
			doPreview();
		}
	} else {
		autosave_enable_buttons(); // re-enable disabled form buttons
	}
}

function autosave_update_slug(post_id) {
	// create slug area only if not already there
	if ( 'undefined' != makeSlugeditClickable && jQuery.isFunction(makeSlugeditClickable) && !jQuery('#edit-slug-box > *').size() ) {
		jQuery.post( ajaxurl, {
				action: 'sample-permalink',
				post_id: post_id,
				new_title: fullscreen && fullscreen.settings.visible ? jQuery('#wp-fullscreen-title').val() : jQuery('#title').val(),
				samplepermalinknonce: jQuery('#samplepermalinknonce').val()
			},
			function(data) {
				if ( data !== '-1' ) {
					var box = jQuery('#edit-slug-box');
					box.html(data);
					if (box.hasClass('hidden')) {
						box.fadeIn('fast', function () {
							box.removeClass('hidden');
						});
					}
					makeSlugeditClickable();
				}
			}
		);
	}
}

function autosave_loading() {
	jQuery('.autosave-message').html(autosaveL10n.savingText);
}

function autosave_enable_buttons() {
	jQuery(document).trigger('autosave-enable-buttons');
	if ( ! wp.heartbeat || ! wp.heartbeat.hasConnectionError() ) {
		// delay that a bit to avoid some rare collisions while the DOM is being updated.
		setTimeout(function(){
			var parent = jQuery('#submitpost');
			parent.find(':button, :submit').removeAttr('disabled');
			parent.find('.spinner').hide();
		}, 500);
	}
}

function autosave_disable_buttons() {
	jQuery(document).trigger('autosave-disable-buttons');
	jQuery('#submitpost').find(':button, :submit').prop('disabled', true);
	// Re-enable 5 sec later. Just gives autosave a head start to avoid collisions.
	setTimeout( autosave_enable_buttons, 5000 );
}

function delayed_autosave() {
	setTimeout(function(){
		if ( blockSave )
			return;
		autosave();
	}, 200);
}

autosave = function() {
	var post_data = wp.autosave.getPostData(),
		compareString,
		successCallback;

	blockSave = true;

	// post_data.content cannot be retrieved at the moment
	if ( ! post_data.autosave )
		return false;

	// No autosave while thickbox is open (media buttons)
	if ( jQuery("#TB_window").css('display') == 'block' )
		return false;

	compareString = wp.autosave.getCompareString( post_data );

	// Nothing to save or no change.
	if ( compareString == autosaveLast )
		return false;

	autosaveLast = compareString;
	jQuery(document).triggerHandler('wpcountwords', [ post_data["content"] ]);

	// Disable buttons until we know the save completed.
	autosave_disable_buttons();

	if ( post_data["auto_draft"] == '1' ) {
		successCallback = autosave_saved_new; // new post
	} else {
		successCallback = autosave_saved; // pre-existing post
	}

	jQuery.ajax({
		data: post_data,
		beforeSend: autosave_loading,
		type: "POST",
		url: ajaxurl,
		success: successCallback
	});

	return true;
}

// Autosave in localStorage
// set as simple object/mixin for now
window.wp = window.wp || {};
wp.autosave = wp.autosave || {};

(function($){
// Returns the data for saving in both localStorage and autosaves to the server
wp.autosave.getPostData = function() {
	var ed = typeof tinymce != 'undefined' ? tinymce.activeEditor : null, post_name, parent_id, cats = [],
		data = {
			action: 'autosave',
			autosave: true,
			post_id: $('#post_ID').val() || 0,
			autosavenonce: $('#autosavenonce').val() || '',
			post_type: $('#post_type').val() || '',
			post_author: $('#post_author').val() || '',
			excerpt: $('#excerpt').val() || ''
		};

	if ( ed && !ed.isHidden() ) {
		// Don't run while the tinymce spellcheck is on. It resets all found words.
		if ( ed.plugins.spellchecker && ed.plugins.spellchecker.active ) {
			data.autosave = false;
			return data;
		} else {
			if ( 'mce_fullscreen' == ed.id )
				tinymce.get('content').setContent(ed.getContent({format : 'raw'}), {format : 'raw'});

			tinymce.triggerSave();
		}
	}

	if ( typeof fullscreen != 'undefined' && fullscreen.settings.visible ) {
		data['post_title'] = $('#wp-fullscreen-title').val() || '';
		data['content'] = $('#wp_mce_fullscreen').val() || '';
	} else {
		data['post_title'] = $('#title').val() || '';
		data['content'] = $('#content').val() || '';
	}

	/*
	// We haven't been saving tags with autosave since 2.8... Start again?
	$('.the-tags').each( function() {
		data[this.name] = this.value;
	});
	*/

	$('input[id^="in-category-"]:checked').each( function() {
		cats.push(this.value);
	});
	data['catslist'] = cats.join(',');

	if ( post_name = $('#post_name').val() )
		data['post_name'] = post_name;

	if ( parent_id = $('#parent_id').val() )
		data['parent_id'] = parent_id;

	if ( $('#comment_status').prop('checked') )
		data['comment_status'] = 'open';

	if ( $('#ping_status').prop('checked') )
		data['ping_status'] = 'open';

	if ( $('#auto_draft').val() == '1' )
		data['auto_draft'] = '1';

	return data;
};

// Concatenate title, content and excerpt. Used to track changes when auto-saving.
wp.autosave.getCompareString = function( post_data ) {
	if ( typeof post_data === 'object' ) {
		return ( post_data.post_title || '' ) + '::' + ( post_data.content || '' ) + '::' + ( post_data.excerpt || '' );
	}

	return ( $('#title').val() || '' ) + '::' + ( $('#content').val() || '' ) + '::' + ( $('#excerpt').val() || '' );
};

wp.autosave.local = {

	lastSavedData: '',
	blog_id: 0,
	hasStorage: false,

	// Check if the browser supports sessionStorage and it's not disabled
	checkStorage: function() {
		var test = Math.random(), result = false;

		try {
			sessionStorage.setItem('wp-test', test);
			result = sessionStorage.getItem('wp-test') == test;
			sessionStorage.removeItem('wp-test');
		} catch(e) {}

		this.hasStorage = result;
		return result;
    },

	/**
	 * Initialize the local storage
	 *
	 * @return mixed False if no sessionStorage in the browser or an Object containing all post_data for this blog
	 */
	getStorage: function() {
		var stored_obj = false;
		// Separate local storage containers for each blog_id
		if ( this.hasStorage && this.blog_id ) {
			stored_obj = sessionStorage.getItem( 'wp-autosave-' + this.blog_id );

			if ( stored_obj )
				stored_obj = JSON.parse( stored_obj );
			else
				stored_obj = {};
		}

		return stored_obj;
	},

	/**
	 * Set the storage for this blog
	 *
	 * Confirms that the data was saved successfully.
	 *
	 * @return bool
	 */
	setStorage: function( stored_obj ) {
		var key;

		if ( this.hasStorage && this.blog_id ) {
			key = 'wp-autosave-' + this.blog_id;
			sessionStorage.setItem( key, JSON.stringify( stored_obj ) );
			return sessionStorage.getItem( key ) !== null;
		}

		return false;
	},

	/**
	 * Get the saved post data for the current post
	 *
	 * @return mixed False if no storage or no data or the post_data as an Object
	 */
	getData: function() {
		var stored = this.getStorage(), post_id = $('#post_ID').val();

		if ( !stored || !post_id )
			return false;

		return stored[ 'post_' + post_id ] || false;
	},

	/**
	 * Set (save or delete) post data in the storage.
	 *
	 * If stored_data evaluates to 'false' the storage key for the current post will be removed
	 *
	 * $param stored_data The post data to store or null/false/empty to delete the key
	 * @return bool
	 */
	setData: function( stored_data ) {
		var stored = this.getStorage(), post_id = $('#post_ID').val();

		if ( !stored || !post_id )
			return false;

		if ( stored_data )
			stored[ 'post_' + post_id ] = stored_data;
		else if ( stored.hasOwnProperty( 'post_' + post_id ) )
			delete stored[ 'post_' + post_id ];
		else
			return false;

		return this.setStorage(stored);
	},

	/**
	 * Save post data for the current post
	 *
	 * Runs on a 15 sec. schedule, saves when there are differences in the post title or content.
	 * When the optional data is provided, updates the last saved post data.
	 *
	 * $param data optional Object The post data for saving, minimum 'post_title' and 'content'
	 * @return bool
	 */
	save: function( data ) {
		var result = false, post_data, compareString;

		if ( ! data ) {
			post_data = wp.autosave.getPostData();
		} else {
			post_data = this.getData() || {};
			$.extend( post_data, data );
			post_data.autosave = true;
		}

		// Cannot get the post data at the moment
		if ( ! post_data.autosave )
			return false;

		compareString = wp.autosave.getCompareString( post_data );

		// If the content, title and excerpt did not change since the last save, don't save again
		if ( compareString == this.lastSavedData )
			return false;

		post_data['save_time'] = (new Date()).getTime();
		post_data['status'] = $('#post_status').val() || '';
		result = this.setData( post_data );

		if ( result )
			this.lastSavedData = compareString;

		return result;
	},

	// Initialize and run checkPost() on loading the script (before TinyMCE init)
	init: function( settings ) {
		var self = this;

		// Check if the browser supports sessionStorage and it's not disabled
		if ( ! this.checkStorage() )
			return;

		// Don't run if the post type supports neither 'editor' (textarea#content) nor 'excerpt'.
		if ( ! $('#content').length && ! $('#excerpt').length )
			return;

		if ( settings )
			$.extend( this, settings );

		if ( !this.blog_id )
			this.blog_id = typeof window.autosaveL10n != 'undefined' ? window.autosaveL10n.blog_id : 0;

		$(document).ready( function(){ self.run(); } );
	},

	// Run on DOM ready
	run: function() {
		var self = this;

		// Check if the local post data is different than the loaded post data.
		this.checkPost();

		// Set the schedule
		this.schedule = $.schedule({
			time: 15 * 1000,
			func: function() { wp.autosave.local.save(); },
			repeat: true,
			protect: true
		});

		$('form#post').on('submit.autosave-local', function() {
			var editor = typeof tinymce != 'undefined' && tinymce.get('content'), post_id = $('#post_ID').val() || 0;

			if ( editor && ! editor.isHidden() ) {
				// Last onSubmit event in the editor, needs to run after the content has been moved to the textarea.
				editor.onSubmit.add( function() {
					wp.autosave.local.save({
						post_title: $('#title').val() || '',
						content: $('#content').val() || '',
						excerpt: $('#excerpt').val() || ''
					});
				});
			} else {
				self.save({
					post_title: $('#title').val() || '',
					content: $('#content').val() || '',
					excerpt: $('#excerpt').val() || ''
				});
			}

			wpCookies.set( 'wp-saving-post-' + post_id, 'check' );
		});
	},

	// Strip whitespace and compare two strings
	compare: function( str1, str2 ) {
		function remove( string ) {
			return string.toString().replace(/[\x20\t\r\n\f]+/g, '');
		}

		return ( remove( str1 || '' ) == remove( str2 || '' ) );
	},

	/**
	 * Check if the saved data for the current post (if any) is different than the loaded post data on the screen
	 *
	 * Shows a standard message letting the user restore the post data if different.
	 *
	 * @return void
	 */
	checkPost: function() {
		var self = this, post_data = this.getData(), content, post_title, excerpt, notice,
			post_id = $('#post_ID').val() || 0, cookie = wpCookies.get( 'wp-saving-post-' + post_id );

		if ( ! post_data )
			return;

		if ( cookie ) {
			wpCookies.remove( 'wp-saving-post-' + post_id );

			if ( cookie == 'saved' ) {
				// The post was saved properly, remove old data and bail
				this.setData( false );
				return;
			}
		}

		// There is a newer autosave. Don't show two "restore" notices at the same time.
		if ( $('#has-newer-autosave').length )
			return;

		content = $('#content').val() || '';
		post_title = $('#title').val() || '';
		excerpt = $('#excerpt').val() || '';

		if ( $('#wp-content-wrap').hasClass('tmce-active') && typeof switchEditors != 'undefined' )
			content = switchEditors.pre_wpautop( content );

		// cookie == 'check' means the post was not saved properly, always show #local-storage-notice
		if ( cookie != 'check' && this.compare( content, post_data.content ) && this.compare( post_title, post_data.post_title ) && this.compare( excerpt, post_data.excerpt ) ) {
			return;
		}

		this.restore_post_data = post_data;
		this.undo_post_data = {
			content: content,
			post_title: post_title,
			excerpt: excerpt
		};

		notice = $('#local-storage-notice');
		$('.wrap h2').first().after( notice.addClass('updated').show() );

		notice.on( 'click', function(e) {
			var target = $( e.target );

			if ( target.hasClass('restore-backup') ) {
				self.restorePost( self.restore_post_data );
				target.parent().hide();
				$(this).find('p.undo-restore').show();
			} else if ( target.hasClass('undo-restore-backup') ) {
				self.restorePost( self.undo_post_data );
				target.parent().hide();
				$(this).find('p.local-restore').show();
			}

			e.preventDefault();
		});
	},

	// Restore the current title, content and excerpt from post_data.
	restorePost: function( post_data ) {
		var editor;

		if ( post_data ) {
			// Set the last saved data
			this.lastSavedData = wp.autosave.getCompareString( post_data );

			if ( $('#title').val() != post_data.post_title )
				$('#title').focus().val( post_data.post_title || '' );

			$('#excerpt').val( post_data.excerpt || '' );
			editor = typeof tinymce != 'undefined' && tinymce.get('content');

			if ( editor && ! editor.isHidden() && typeof switchEditors != 'undefined' ) {
				// Make sure there's an undo level in the editor
				editor.undoManager.add();
				editor.setContent( post_data.content ? switchEditors.wpautop( post_data.content ) : '' );
			} else {
				// Make sure the Text editor is selected
				$('#content-html').click();
				$('#content').val( post_data.content );
			}

			return true;
		}

		return false;
	}
};

wp.autosave.local.init();

}(jQuery));
