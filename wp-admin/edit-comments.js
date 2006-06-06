addLoadEvent(function() {
	theCommentList = new listMan('the-comment-list');
	if ( !theCommentList )
		return false;
	theCommentList.dimComplete = function(what,id,dimClass) {
		var m = document.getElementById('awaitmod');
		if ( document.getElementById(what + '-' + id).className.match(dimClass) ) m.innerHTML = parseInt(m.innerHTML,10) + 1;
		else m.innerHTML = parseInt(m.innerHTML,10) - 1;
	}
	theCommentList.delComplete = function(what,id) {
		var m = document.getElementById('awaitmod');
		if ( document.getElementById(what + '-' + id).className.match('unapproved') ) m.innerHTML = parseInt(m.innerHTML,10) - 1;
	}
	if ( theList ) // the post list: edit.php
		theList.delComplete = function() {
			var comments = document.getElementById('comments');
			var commdel = encloseFunc(function(a){a.parentNode.removeChild(a);},comments);
			var listdel = encloseFunc(function(a){a.parentNode.removeChild(a);},theCommentList.theList);
			setTimeout(commdel,705);
			setTimeout(listdel,705);
		}
});

