var autosaveLast = '';
var autosavePeriodical;

function autosave_start_timer() {
	autosaveLast = jQuery('#post #title').val()+jQuery('#post #content').val();
	// Keep autosave_interval in sync with edit_post().
	autosavePeriodical = jQuery.schedule({time: autosaveL10n.autosaveInterval * 1000, func: autosave, repeat: true, protect: true});

	//Disable autosave after the form has been submitted
	jQuery("#post #submit").submit(function() { jQuery.cancel(autosavePeriodical); });
	jQuery("#post #save").click(function() { jQuery.cancel(autosavePeriodical); });
	jQuery("#post #submit").click(function() { jQuery.cancel(autosavePeriodical); });
	jQuery("#post #publish").click(function() { jQuery.cancel(autosavePeriodical); });
	jQuery("#post #deletepost").click(function() { jQuery.cancel(autosavePeriodical); });

	// Autosave early on for a new post
	jQuery("#content").keypress(function() {
		if ( 1 === ( jQuery(this).val().length % 15 ) && 1 > parseInt(jQuery("#post_ID").val(),10) )
			setTimeout(autosave, 5000);
	});
}
addLoadEvent(autosave_start_timer)

function autosave_cur_time() {
	var now = new Date();
	return "" + ((now.getHours() >12) ? now.getHours() -12 : now.getHours()) + 
	((now.getMinutes() < 10) ? ":0" : ":") + now.getMinutes() +
	((now.getSeconds() < 10) ? ":0" : ":") + now.getSeconds();
}

function autosave_update_post_ID(response) {
	var res = parseInt(response);
	var message;

	if(isNaN(res)) {
		message = autosaveL10n.errorText.replace(/%response%/g, response);
	} else if( res > 0 ) {
		message = autosaveL10n.saveText.replace(/%time%/g, autosave_cur_time());
		jQuery('#post_ID').attr({name: "post_ID"});
		jQuery('#post_ID').val(res);
		// We need new nonces
		jQuery.post(autosaveL10n.requestFile, {
			action: "autosave-generate-nonces",
			post_ID: res,
			autosavenonce: jQuery('#autosavenonce').val(),
			post_type: jQuery('#post_type').val()
		}, function(html) {
			jQuery('#_wpnonce').val(html);
		});
		jQuery('#hiddenaction').val('editpost');
	} else {
		message = autosaveL10n.failText;
	}
	jQuery('#autosave').html(message);
	autosave_update_preview_link(res);
	autosave_update_slug(res);
	autosave_enable_buttons();
}

function autosave_update_preview_link(post_id) {
	// Add preview button if not already there
	if ( ! jQuery('#previewview > *').get()[0] ) {
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
	if ( 'undefined' != typeof make_slugedit_clickable && ! jQuery('#edit-slug-box > *').get()[0] ) {
		jQuery.post(slugL10n.requestFile, {
			action: 'sample-permalink',
			post_id: post_id,
			samplepermalinknonce: jQuery('#samplepermalinknonce').val()}, function(data) {
				jQuery('#edit-slug-box').html(data);
				make_slugedit_clickable();
			});
	}
}

function autosave_loading() {
	jQuery('#autosave').html(autosaveL10n.savingText);
}

function autosave_saved(response) {
	var res = parseInt(response);
	var message;

	if(isNaN(res)) {
		message = autosaveL10n.errorText.replace(/%response%/g, response);
	} else {
		message = autosaveL10n.saveText.replace(/%time%/g, autosave_cur_time());
	}
	jQuery('#autosave').html(message);
	autosave_update_preview_link(res);
	autosave_update_slug(res);
	autosave_enable_buttons();
}

function autosave_disable_buttons() {
	jQuery("#post #save:enabled").attr('disabled', 'disabled');
	jQuery("#post #submit:enabled").attr('disabled', 'disabled');
	jQuery("#post #publish:enabled").attr('disabled', 'disabled');
	jQuery("#post #deletepost:enabled").attr('disabled', 'disabled');
	setTimeout('autosave_enable_buttons();', 1000); // Re-enable 1 sec later.  Just gives autosave a head start to avoid collisions.
}

function autosave_enable_buttons() {
	jQuery("#post #save:disabled").attr('disabled', '');
	jQuery("#post #submit:disabled").attr('disabled', '');
	jQuery("#post #publish:disabled").attr('disabled', '');
	jQuery("#post #deletepost:disabled").attr('disabled', '');
}

function autosave() {
	var rich = ( (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden() ) ? true : false;
	var post_data = {
			action: "autosave",
			post_ID:  jQuery("#post_ID").val() || 0,
			post_title: jQuery("#title").val() || "",
			autosavenonce: jQuery('#autosavenonce').val(),
			tags_input: jQuery("#tags-input").val() || "",
			post_type: jQuery('#post_type').val() || ""
		};

	/* Gotta do this up here so we can check the length when tinyMCE is in use */
	if ( rich ) {
		// Don't run while the TinyMCE spellcheck is on.
		if ( tinyMCE.activeEditor.plugins.spellchecker && tinyMCE.activeEditor.plugins.spellchecker.active ) return;
		tinyMCE.triggerSave();
	} 
	
	post_data["content"] = jQuery("#content").val();
	if ( jQuery('#post_name').val() )
		post_data["post_name"] = jQuery('#post_name').val();

	if(post_data["post_title"].length==0 || post_data["content"].length==0 || post_data["post_title"] + post_data["content"] == autosaveLast) {
		return;
	}

	autosave_disable_buttons();

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

	if ( rich ) 
        tinyMCE.triggerSave();
    
	post_data["content"] = jQuery("#content").val();

	if(parseInt(post_data["post_ID"]) < 1) {
		post_data["temp_ID"] = post_data["post_ID"];
		jQuery.ajaxSetup({
			success: function(html) { autosave_update_post_ID(html); }
		});
	} else {
		jQuery.ajaxSetup({
			success: function(html) { autosave_saved(html); }
		});
	}
	jQuery.ajax({
		data: post_data,
		beforeSend: function() { autosave_loading() },
		type: "POST",
		url: autosaveL10n.requestFile
	});
}
