var autosaveLast = '';
var autosavePeriodical;

jQuery(function($) {
	autosaveLast = $('#post #title').val()+$('#post #content').val();
	autosavePeriodical = $.schedule({time: autosaveL10n.autosaveInterval * 1000, func: function() { autosave(); }, repeat: true, protect: true});

	//Disable autosave after the form has been submitted
	$("#post").submit(function() { $.cancel(autosavePeriodical); });

	// Autosave early on for a new post.  Why?  Should this only be run once?
	$("#content").keypress(function() {
		if ( 1 === ( $(this).val().length % 15 ) && 1 > parseInt($("#post_ID").val(),10) )
			setTimeout(autosave, 5000);
	});
});

// called when autosaving pre-existing post
function autosave_saved(response) {
	var oldMessage = jQuery('#autosave').html();
	var res = wpAjax.parseAjaxResponse(response, 'autosave'); // parse the ajax response
	var message = '';

	if ( res && res.responses && res.responses.length ) {
		message = res.responses[0].data; // The saved message or error.
		// someone else is editing: disable autosave, set errors
		if ( res.responses[0].supplemental && 'disable' == res.responses[0].supplemental['disable_autosave'] ) {
			autosave = function() {};
			res = { errors: true };
		}

		// if no errors: add preview link and slug UI
		if ( !res.errors ) {
			var postID = parseInt( res.responses[0].id );
			if ( !isNaN(postID) && postID > 0 ) {
				autosave_update_preview_link(postID);
				autosave_update_slug(postID);
			}
		}
	}
	if ( message ) { jQuery('#autosave').html(message); } // update autosave message
	else if ( oldMessage && res ) { jQuery('#autosave').html( oldMessage ); }
	autosave_enable_buttons(); // re-enable disabled form buttons
	return res;
}

// called when autosaving new post
function autosave_update_post_ID(response) {
	var res = autosave_saved(response); // parse the ajax response do the above

	// if no errors: update post_ID from the temporary value, grab new save-nonce for that new ID
	if ( res && res.responses.length && !res.errors ) {
		var postID = parseInt( res.responses[0].id );
		if ( !isNaN(postID) && postID > 0 ) {
			if ( postID == parseInt(jQuery('#post_ID').val()) ) { return; } // no need to do this more than once
			jQuery('#post_ID').attr({name: "post_ID"});
			jQuery('#post_ID').val(postID);
			// We need new nonces
			jQuery.post(autosaveL10n.requestFile, {
				action: "autosave-generate-nonces",
				post_ID: postID,
				autosavenonce: jQuery('#autosavenonce').val(),
				post_type: jQuery('#post_type').val()
			}, function(html) {
				jQuery('#_wpnonce').val(html);
			});
			jQuery('#hiddenaction').val('editpost');
		}
	}
}

function autosave_update_preview_link(post_id) {
	// Add preview button if not already there
	if ( !jQuery('#previewview > *').size() ) {
		var post_type = jQuery('#post_type').val();
		var previewText = 'page' == post_type ? autosaveL10n.previewPageText : autosaveL10n.previewPostText;
		jQuery.post(autosaveL10n.requestFile, {
			action: "get-permalink",
			post_id: post_id,
			getpermalinknonce: jQuery('#getpermalinknonce').val()
		}, function(permalink) {
			jQuery('#previewview').html('<a target="_blank" href="'+permalink+'">'+previewText+'</a>');
		});
	}
}

function autosave_update_slug(post_id) {
	// create slug area only if not already there
	if ( jQuery.isFunction(make_slugedit_clickable) && !jQuery('#edit-slug-box > *').size() ) {
		jQuery.post(
			slugL10n.requestFile,
			{
				action: 'sample-permalink',
				post_id: post_id,
				samplepermalinknonce: jQuery('#samplepermalinknonce').val()
			},
			function(data) {
				jQuery('#edit-slug-box').html(data);
				make_slugedit_clickable();
			}
		);
	}
}

function autosave_loading() {
	jQuery('#autosave').html('<div class="updated"><p>' + autosaveL10n.savingText + '</p></div>');
}

function autosave_enable_buttons() {
	jQuery("#submitpost :button:disabled, #submitpost :submit:disabled").attr('disabled', '');
}

function autosave_disable_buttons() {
	jQuery("#submitpost :button:enabled, #submitpost :submit:enabled").attr('disabled', 'disabled');
	setTimeout(autosave_enable_buttons, 1000); // Re-enable 1 sec later.  Just gives autosave a head start to avoid collisions.
}

var autosave = function() {
	// (bool) is rich editor enabled and active
	var rich = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
	var post_data = {
		action: "autosave",
		post_ID:  jQuery("#post_ID").val() || 0,
		post_title: jQuery("#title").val() || "",
		autosavenonce: jQuery('#autosavenonce').val(),
		tags_input: jQuery("#tags-input").val() || "",
		post_type: jQuery('#post_type').val() || "",
		autosave: 1
	};

	// We always send the ajax request in order to keep the post lock fresh.
	// This (bool) tells whether or not to write the post to the DB during the ajax request.
	var doAutoSave = true;

	/* Gotta do this up here so we can check the length when tinyMCE is in use */
	if ( rich ) { tinyMCE.triggerSave(); }

	post_data["content"] = jQuery("#content").val();
	if ( jQuery('#post_name').val() )
		post_data["post_name"] = jQuery('#post_name').val();

	// Nothing to save or no change.
	if(post_data["post_title"].length==0 || post_data["content"].length==0 || post_data["post_title"] + post_data["content"] == autosaveLast) {
		doAutoSave = false
	}

	autosave_disable_buttons();

	var origStatus = jQuery('#original_post_status').val();
	if ( 'draft' != origStatus ) // autosave currently only turned on for drafts
		doAutoSave = false;

	autosaveLast = jQuery("#title").val()+jQuery("#content").val();
	goodcats = ([]);
	jQuery("[@name='post_category[]']:checked").each( function(i) {
		goodcats.push(this.value);
	} );
	post_data["catslist"] = goodcats.join(",");

	if ( jQuery("#comment_status").attr("checked") )
		post_data["comment_status"] = 'open';
	if ( jQuery("#ping_status").attr("checked") )
		post_data["ping_status"] = 'open';
	if( jQuery("#excerpt"))
		post_data["excerpt"] = jQuery("#excerpt").val();

	// Don't run while the TinyMCE spellcheck is on.  Why?  Who knows.
	if ( rich && tinyMCE.activeEditor.plugins.spellchecker && tinyMCE.activeEditor.plugins.spellchecker.active ) {
		doAutoSave = false;
	}

	if(parseInt(post_data["post_ID"]) < 1) {
		post_data["temp_ID"] = post_data["post_ID"];
		var successCallback = autosave_update_post_ID; // new post
	} else {
		var successCallback = autosave_saved; // pre-existing post
	}

	if ( !doAutoSave ) {
		post_data['autosave'] = 0;
	}

	jQuery.ajax({
		data: post_data,
		beforeSend: doAutoSave ? autosave_loading : null,
		type: "POST",
		url: autosaveL10n.requestFile,
		success: successCallback
	});
}
