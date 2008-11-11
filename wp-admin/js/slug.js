function edit_permalink(post_id) {
	var i, c = 0;
	var e = jQuery('#editable-post-name');
	var revert_e = e.html();
	var real_slug = jQuery('#post_name');
	var revert_slug = real_slug.html();
	var b = jQuery('#edit-slug-buttons');
	var revert_b = b.html();
	var full = jQuery('#editable-post-name-full').html();

	b.html('<a href="" class="save button">'+slugL10n.save+'</a> <a class="cancel" href="">'+slugL10n.cancel+'</a>');
	b.children('.save').click(function() {
		var new_slug = e.children('input').val();
		jQuery.post(slugL10n.requestFile, {
			action: 'sample-permalink',
			post_id: post_id,
			new_slug: new_slug,
			new_title: jQuery('#title').val(),
			samplepermalinknonce: jQuery('#samplepermalinknonce').val()}, function(data) {
				jQuery('#edit-slug-box').html(data);
				b.html(revert_b);
				real_slug.attr('value', new_slug);
				make_slugedit_clickable();
			});
		return false;
	});
	jQuery('#edit-slug-buttons .cancel').click(function() {
		e.html(revert_e);
		b.html(revert_b);
		real_slug.attr('value', revert_slug);
		return false;
	});
	for(i=0; i < full.length; ++i) {
		if ('%' == full.charAt(i)) c++;
	}
	slug_value = (c > full.length/4)? '' : full;
	e.html('<input type="text" id="new-post-slug" value="'+slug_value+'" />').children('input').keypress(function(e){
		var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
		// on enter, just save the new slug, don't save the post
		if (13 == key) {b.children('.save').click();return false;}
		if (27 == key) {b.children('.cancel').click();return false;}
		real_slug.attr('value', this.value)}).focus();
}

function make_slugedit_clickable() {
	jQuery('#editable-post-name').click(function() {jQuery('#edit-slug-buttons').children('.edit-slug').click()});
}

