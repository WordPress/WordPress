function moveAddCommentForm(theId, threadId, respondId) {
	var addComment = document.getElementById(respondId);
	var comment = document.getElementById(theId);
	addComment.parentNode.removeChild(addComment);

	comment.appendChild(addComment);
//	if(comment.className.indexOf("alt")>-1) {
//		addComment.className = addComment.className.replace(" alt", "");					
//	} else {
//		addComment.className += " alt";
//	}
	var replyId = document.getElementById("comment-parent");
	replyId.value = threadId;
	var reRootElement = document.getElementById("cancel-comment-reply");
	reRootElement.style.display = "block";
	var aTags = comment.getElementsByTagName("A");
	var anc = aTags.item(0).id;
	//document.location.href = "#"+anc;
	document.getElementById("comment").focus();
}

function cancelCommentReply() {
	var addComment = document.getElementById("respond");			
	var reRootElement = document.getElementById("cancel-comment-reply");
	reRootElement.style.display = "none";
	var content = document.getElementById("content-main");
	if( !content )
		content = document.getElementById("content");
	if( content ) {
		addComment.parentNode.removeChild(addComment);
		content.appendChild(addComment);
	}
//	addComment.className = addComment.className.replace(" alt", "");
	document.location.href = "#respond";
	document.getElementById("comment").focus();				
	document.getElementById("comment-parent").value = "0";
}