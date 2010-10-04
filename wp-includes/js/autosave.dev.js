var autosave, autosaveLast = '', autosavePeriodical, autosaveOldMessage = '', autosaveDelayPreview = false, notSaved = true, blockSave = false, interimLogin = false;

jQuery(document).ready( function($) {
	var dotabkey = true;

	autosaveLast = $('#post #title').val() + $('#post #content').val();
	autosavePeriodical = $.schedule({time: autosaveL10n.autosaveInterval * 1000, func: function() { autosave(); }, repeat: true, protect: true});

	//Disable autosave after the form has been submitted
	$("#post").submit(function() {
		$.cancel(autosavePeriodical);
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
			$('#ajax-loading').css('visibility', 'visible');
		else
			$('#draft-ajax-loading').css('visibility', 'visible');
	});

	window.onbeforeunload = function(){
		var mce = typeof(tinyMCE) != 'undefined' ? tinyMCE.activeEditor : false, title, content;

		if ( mce && !mce.isHidden() ) {
			if ( mce.isDirty() )
				return autosaveL10n.saveAlert;
		} else {
			title = $('#post #title').val(), content = $('#post #content').val();
			if ( ( title || content ) && title + content != autosaveLast )
				return autosaveL10n.saveAlert;
		}
	};

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
		$('input#wp-preview').val('');
	}

	//  This code is meant to allow tabbing from Title to Post if tinyMCE is defined.
	if ( typeof tinyMCE != 'undefined' ) {
		$('#title')[$.browser.opera ? 'keypress' : 'keydown'](function (e) {
			if ( e.which == 9 && !e.shiftKey && !e.controlKey && !e.altKey ) {
				if ( ($('#auto_draft').val() == '1') && ($("#title").val().length > 0) ) { autosave(); }
				if ( tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden() && dotabkey ) {
					e.preventDefault();
					dotabkey = false;
					tinyMCE.activeEditor.focus();
					return false;
				}
			}
		});
	}

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
	var res = wpAjax.parseAjaxResponse(response, 'autosave'), message = '', postID, sup, url;

	if ( res && res.responses && res.responses.length ) {
		message = res.responses[0].data; // The saved message or error.
		// someone else is editing: disable autosave, set errors
		if ( res.responses[0].supplemental ) {
			sup = res.responses[0].supplemental;
			if ( 'disable' == sup['disable_autosave'] ) {
				autosave = function() {};
				res = { errors: true };
			}
			if ( sup['session_expired'] && (url = sup['session_expired']) ) {
				if ( !interimLogin || interimLogin.closed ) {
					interimLogin = window.open(url, 'login', 'width=600,height=450,resizable=yes,scrollbars=yes,status=yes');
					interimLogin.focus();
				}
				delete sup['session_expired'];
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
	if ( message ) { jQuery('#autosave').html(message); } // update autosave message
	else if ( autosaveOldMessage && res ) { jQuery('#autosave').html( autosaveOldMessage ); }
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
	var res = autosave_parse_response(response), tempID, postID;
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
		jQuery.post(
			ajaxurl,
			{
				action: 'sample-permalink',
				post_id: post_id,
				new_title: jQuery('#title').val(),
				samplepermalinknonce: jQuery('#samplepermalinknonce').val()
			},
			function(data) {
				jQuery('#edit-slug-box').html(data);
				makeSlugeditClickable();
			}
		);
	}
}

function autosave_loading() {
	jQuery('#autosave').html(autosaveL10n.savingText);
}

function autosave_enable_buttons() {
	// delay that a bit to avoid some rare collisions while the DOM is being updated.
	setTimeout(function(){
		jQuery(':button, :submit', '#submitpost').removeAttr('disabled');
		jQuery('.ajax-loading').css('visibility', 'hidden');
	}, 500);
}

function autosave_disable_buttons() {
	jQuery(':button, :submit', '#submitpost').attr('disabled', 'disabled');
	// Re-enable 5 sec later.  Just gives autosave a head start to avoid collisions.
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
	var rich = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden(), post_data, doAutoSave, ed, origStatus, successCallback;

	autosave_disable_buttons();

	post_data = {
		action: "autosave",
		post_ID:  jQuery("#post_ID").val() || 0,
		post_title: jQuery("#title").val() || "",
		autosavenonce: jQuery('#autosavenonce').val(),
		post_type: jQuery('#post_type').val() || "",
		autosave: 1
	};

	jQuery('.tags-input').each( function() {
		post_data[this.name] = this.value;
	} );

	// We always send the ajax request in order to keep the post lock fresh.
	// This (bool) tells whether or not to write the post to the DB during the ajax request.
	doAutoSave = true;

	// No autosave while thickbox is open (media buttons)
	if ( jQuery("#TB_window").css('display') == 'block' )
		doAutoSave = false;

	/* Gotta do this up here so we can check the length when tinyMCE is in use */
	if ( rich && doAutoSave ) {
		ed = tinyMCE.activeEditor;
		// Don't run while the TinyMCE spellcheck is on. It resets all found words.
		if ( ed.plugins.spellchecker && ed.plugins.spellchecker.active ) {
			doAutoSave = false;
		} else {
			if ( 'mce_fullscreen' == ed.id )
				tinyMCE.get('content').setContent(ed.getContent({format : 'raw'}), {format : 'raw'});
			tinyMCE.get('content').save();
		}
	}

	post_data["content"] = jQuery("#content").val();
	if ( jQuery('#post_name').val() )
		post_data["post_name"] = jQuery('#post_name').val();

	// Nothing to save or no change.
	if ( ( post_data["post_title"].length == 0 && post_data["content"].length == 0 ) || post_data["post_title"] + post_data["content"] == autosaveLast ) {
		doAutoSave = false;
	}

	origStatus = jQuery('#original_post_status').val();

	goodcats = ([]);
	jQuery("[name='post_category[]']:checked").each( function(i) {
		goodcats.push(this.value);
	} );
	post_data["catslist"] = goodcats.join(",");

	if ( jQuery("#comment_status").attr("checked") )
		post_data["comment_status"] = 'open';
	if ( jQuery("#ping_status").attr("checked") )
		post_data["ping_status"] = 'open';
	if ( jQuery("#excerpt").size() )
		post_data["excerpt"] = jQuery("#excerpt").val();
	if ( jQuery("#post_author").size() )
		post_data["post_author"] = jQuery("#post_author").val();
	if ( jQuery("#parent_id").val() ) 
		post_data["parent_id"] = jQuery("#parent_id").val(); 
	post_data["user_ID"] = jQuery("#user-id").val();
	if ( jQuery('#auto_draft').val() == '1' )
		post_data["auto_draft"] = '1';

	if ( doAutoSave ) {
		autosaveLast = jQuery("#title").val() + jQuery("#content").val();
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
		url: autosaveL10n.requestFile,
		success: successCallback
	});
}
