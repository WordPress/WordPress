function moveAddCommentForm(theId,threadId,respondId){
	jQuery("#"+respondId).appendTo("#"+theId);
	jQuery("#comment-parent").val(threadId);
	jQuery("#cancel-comment-reply").show();
	jQuery("#comment").focus();
}
function cancelCommentReply(respondId,respondRoot){
	jQuery("#cancel-comment-reply").hide();
	jQuery("#"+respondId).appendTo("#"+respondRoot);
	document.location.href="#respond";
	jQuery("#comment").focus();
	jQuery("#comment-parent").val("0");
}
jQuery(document).ready(function($){
	$(".thread-odd").find("div.reply").show();
	$(".thread-even").find("div.reply").show();
});
