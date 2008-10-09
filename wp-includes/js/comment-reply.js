
addComment = {
	moveForm : function(commId, parentId, respondId) {
		var t = this, div, comm = t.I(commId), respond = t.I(respondId);
		
		t.respondId = respondId;
		
		if ( ! t.I('wp-temp-form-div') ) {
			div = document.createElement('div');
			div.id = 'wp-temp-form-div';
			div.style.display = 'none';
			respond.parentNode.insertBefore(div, respond);
		}

		comm.parentNode.insertBefore(respond, comm.nextSibling);
		
		t.I('comment_parent').value = parentId;
		
		t.I('cancel-comment-reply-link').style.display = '';
		t.I('cancel-comment-reply-link').onclick = function() {
			var t = addComment, temp = t.I('wp-temp-form-div'), respond = t.I(t.respondId);
			
			t.I('comment_parent').value = '0';
			temp.parentNode.insertBefore(respond, temp);
			temp.parentNode.removeChild(temp);
			t.I('cancel-comment-reply-link').style.display = 'none';
			t.I('cancel-comment-reply-link').onclick = null;
			return false;
		}
		t.I('comment').focus();
	},
	
	I : function(e) {
		return document.getElementById(e);
	}
}
