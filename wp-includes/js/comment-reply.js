
function moveAddCommentForm(commId, parentId, respondId) {
	var div = document.createElement('div');
	jQuery("#"+respondId).before( jQuery(div).attr('id', 'wp-temp-form-div').hide() ).appendTo("#"+commId);
	jQuery("#comment-parent").val(parentId);
	jQuery("#cancel-comment-reply-link").show().click(function(){
		jQuery("#comment-parent").val("0");
		jQuery('#wp-temp-form-div').after( jQuery("#"+respondId) ).remove();
		jQuery(this).hide();
		return false;
	});
	jQuery("#comment").focus();
}