var autosave, autosaveLast = '', autosavePeriodical, autosaveOldMessage = '', autosaveDelayPreview = false, notSaved = true, blockSave = false, fullscreen, autosaveLockRelease = true;

jQuery(document).ready( function($) {

	autosaveLast = ( $('#post #title').val() || '' ) + ( $('#post #content').val() || '' );
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
		var mce = typeof(tinymce) != 'undefined' ? tinymce.activeEditor : false, title, content;

		if ( mce && !mce.isHidden() ) {
			if ( mce.isDirty() )
				return autosaveL10n.saveAlert;
		} else {
			if ( fullscreen && fullscreen.settings.visible ) {
				title = $('#wp-fullscreen-title').val() || '';
				content = $("#wp_mce_fullscreen").val() || '';
			} else {
				title = $('#post #title').val() || '';
				content = $('#post #content').val() || '';
			}

			if ( ( title || content ) && title + content != autosaveLast )
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
});

function autosave_parse_response(response) {
	var res = wpAjax.parseAjaxResponse(response, 'autosave'), message = '', postID, sup;

	if ( res && res.responses && res.responses.length ) {
		message = res.responses[0].data; // The saved message or error.
		// someone else is editing: disable autosave, set errors
		if ( res.responses[0].supplemental ) {
			sup = res.responses[0].supplemental;
			if ( 'disable' == sup['disable_autosave'] ) {
				autosave = function() {};
				autosaveLockRelease = false;
				res = { errors: true };
			}

			if ( sup['active-post-lock'] ) {
				jQuery('#active_post_lock').val( sup['active-post-lock'] );
			}

			if ( sup['alert'] ) {
				jQuery('#autosave-alert').remove();
				jQuery('#titlediv').after('<div id="autosave-alert" class="error below-h2"><p>' + sup['alert'] + '</p></div>');
			}

			jQuery.each(sup, function(selector, value) {
				if ( selector.match(/^replace-/) ) {
					jQuery('#'+selector.replace('replace-', '')).val(value);
				}
			});
		}

		// if no errors: add slug UI
		if ( !res.errors ) {
			postID = parseInt( res.responses[0].id, 10 );
			if ( !isNaN(postID) && postID > 0 ) {
				autosave_update_slug(postID);
			}
		}
	}
	if ( message ) { // update autosave message
		jQuery('.autosave-message').html(message);
	} else if ( autosaveOldMessage && res ) {
		jQuery('.autosave-message').html( autosaveOldMessage );
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
	var res = autosave_parse_response(response), postID;

	if ( res && res.responses.length && !res.errors ) {
		// An ID is sent only for real auto-saves, not for autosave=0 "keepalive" saves
		postID = parseInt( res.responses[0].id, 10 );
		if ( !isNaN(postID) && postID > 0 ) {
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
					jQuery('#edit-slug-box').html(data);
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
	// delay that a bit to avoid some rare collisions while the DOM is being updated.
	setTimeout(function(){
		jQuery(':button, :submit', '#submitpost').removeAttr('disabled');
		jQuery('.spinner', '#submitpost').hide();
	}, 500);
}

function autosave_disable_buttons() {
	jQuery(':button, :submit', '#submitpost').prop('disabled', true);
	// Re-enable 5 sec later. Just gives autosave a head start to avoid collisions.
	setTimeout(autosave_enable_buttons, 5000);
}

function delayed_autosave() {
	setTimeout(function(){
		if ( blockSave )
			return;
		autosave();
	}, 200);
}

autosave = function() {
	// (bool) is rich editor enabled and active
	blockSave = true;
	var rich = (typeof tinymce != "undefined") && tinymce.activeEditor && !tinymce.activeEditor.isHidden(),
		post_data, doAutoSave, ed, origStatus, successCallback;

	autosave_disable_buttons();

	post_data = wp.autosave.getPostData();

	// We always send the ajax request in order to keep the post lock fresh.
	// This (bool) tells whether or not to write the post to the DB during the ajax request.
	doAutoSave = post_data.autosave;

	// No autosave while thickbox is open (media buttons)
	if ( jQuery("#TB_window").css('display') == 'block' )
		doAutoSave = false;

	// Nothing to save or no change.
	if ( ( post_data["post_title"].length == 0 && post_data["content"].length == 0 ) || post_data["post_title"] + post_data["content"] == autosaveLast ) {
		doAutoSave = false;
	}

	if ( doAutoSave ) {
		autosaveLast = post_data["post_title"] + post_data["content"];
		jQuery(document).triggerHandler('wpcountwords', [ post_data["content"] ]);
	} else {
		post_data['autosave'] = 0;
	}

	if ( post_data["auto_draft"] == '1' ) {
		successCallback = autosave_saved_new; // new post
	} else {
		successCallback = autosave_saved; // pre-existing post
	}

	autosaveOldMessage = jQuery('#autosave').html();
	jQuery.ajax({
		data: post_data,
		beforeSend: doAutoSave ? autosave_loading : null,
		type: "POST",
		url: ajaxurl,
		success: successCallback
	});
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
}

wp.autosave.local = {

	lastsaveddata: '',
	blog_id: 0,
	ajaxurl: window.ajaxurl || 'wp-admin/admin-ajax.php',
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
	 * Set (save) post data in the storage
	 *
	 * @return bool
	 */
	setData: function( stored_data ) {
		var stored = this.getStorage(), post_id = $('#post_ID').val();

		if ( !stored || !post_id )
			return false;

		stored[ 'post_' + post_id ] = stored_data;

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
		var result = false;

		if ( ! data ) {
			post_data = wp.autosave.getPostData();
		} else {
			post_data = this.getData() || {};
			$.extend( post_data, data );
		}

		// If the content and title are empty or did not change since the last save, don't save again
		if ( post_data.post_title + ': ' + post_data.content == this.lastsaveddata )
			return false;

		// Cannot get the post data at the moment
		if ( !post_data.autosave )
			return false;

		post_data['save_time'] = (new Date()).getTime();
		post_data['status'] = $('#post_status').val() || '';
		result = this.setData( post_data );

		if ( result )
			this.lastsaveddata = post_data.post_title + ': ' + post_data.content;

		return result;
	},

	// Initialize and run checkPost() on loading the script (before TinyMCE init)
	init: function( settings ) {
		var self = this;

		// Run only on the Add/Edit Post screens and in browsers that have sessionStorage
		if ( 'post' != window.pagenow || ! this.checkStorage() )
			return;
		// editor.js has to be loaded before autosave.js
		if ( typeof switchEditors == 'undefined' )
			return;

		if ( settings )
			$.extend( this, settings );

		if ( !this.blog_id )
			this.blog_id = typeof window.autosaveL10n != 'undefined' ? window.autosaveL10n.blog_id : 0;

		this.checkPost();
		$(document).ready( self.run );
	},

	// Run on DOM ready
	run: function() {
		var self = this, post_data;

		// Set the comparison string
		if ( !this.lastsaveddata ) {
			post_data = wp.autosave.getPostData();

			if ( post_data.content && $('#wp-content-wrap').hasClass('tmce-active') )
				this.lastsaveddata = post_data.post_title + ': ' + switchEditors.pre_wpautop( post_data.content );
			else
				this.lastsaveddata = post_data.post_title + ': ' + post_data.content;
		}

		// Set the schedule
		this.schedule = $.schedule({
			time: 15 * 1000,
			func: function() { wp.autosave.local.save(); },
			repeat: true,
			protect: true
		});

		$('form#post').on('submit.autosave-local', function() {
			var editor = typeof tinymce != 'undefined' && tinymce.get('content');

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
		});
	},

	// Strip whitespace and compare two strings
	compare: function( str1, str2, strip_tags ) {
		function remove( string, strip_tags ) {
			string = string.toString();

			if ( strip_tags )
				string = string.replace(/<[^<>]+>/g, '');

			return string.replace(/[\x20\t\r\n\f]+/g, '');
		}

		return ( remove( str1 || '', strip_tags ) == remove( str2 || '', strip_tags ) );
	},

	/**
	 * Check if the saved data for the current post (if any) is different than the loaded post data on the screen
	 *
	 * Shows a standard message letting the user restore the post data if different.
	 *
	 * @return void
	 */
	checkPost: function() {
		var self = this, post_data = this.getData(), content, check_data, strip_tags = false, notice;

		if ( ! post_data )
			return;

		// There is a newer autosave. Don't show two "restore" notices at the same time.
		if ( $('#has-newer-autosave').length )
			return;

		content = $('#content').val();
		check_data = $.extend( {}, post_data );

		if ( $('#wp-content-wrap').hasClass('tmce-active') )
			content = switchEditors.pre_wpautop( content );

		// The post has just been published, only compare text
		if ( $('#post_status').val() == 'publish' && check_data.status != 'publish' )
			strip_tags = true;

		if ( this.compare( content, check_data.content, strip_tags ) && this.compare( $('#title').val(), check_data.post_title, strip_tags ) && this.compare( $('#excerpt').val(), check_data.excerpt, strip_tags ) )
			return;

		// We have three choices here:
		// - Do an autosave and then show the standard notice "There is an autosave newer than...".
		// - Offer to load/restore the backed up post data.
		// - Restore the post_data without asking, then show a notice with an Undo link/button.
		// Doing an autosave will take few seconds and may take up to 30 and fail if network connectivity is bad
		// Restoring the post will leave the user with the proper content, but it won't be saved to the server until the next autosave.

		this.restore_post_data = post_data;
		this.undo_post_data = wp.autosave.getPostData();

		/*
		if ( $('#post_status').val() == 'publish' ) {
			// Different message when a post is published?
			// Comparing the current and saved post data may fail (false positive) when the post is published
			// as in some cases there are changes to post_content on publishing and updating before saving to the DB.
		}
		*/

		notice = $('#local-storage-notice');
		$('form#post').before( notice.addClass('updated').show() );

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
			this.lastsaveddata = post_data.post_title + ': ' + post_data.content;

			if ( $('#title').val() != post_data.post_title )
				$('#title').focus().val( post_data.post_title || '' );

			$('#excerpt').val( post_data.excerpt || '' );
			editor = typeof tinymce != 'undefined' && tinymce.get('content');

			if ( editor && ! editor.isHidden() ) {
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
}

wp.autosave.local.init();

}(jQuery));
