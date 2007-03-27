addLoadEvent(function() {
	theCommentList = new listMan('the-comment-list');
	if ( !theCommentList )
		return false;

	theExtraCommentList = new listMan('the-extra-comment-list');
	if ( theExtraCommentList ) {
		theExtraCommentList.showLink = 0;
		theExtraCommentList.altOffset = 1;
		if ( theExtraCommentList.theList && theExtraCommentList.theList.childNodes )
			var commentNum = $A(theExtraCommentList.theList.childNodes).findAll( function(i) { return Element.visible(i) } ).length;
		else
			var commentNum = 0;
		var urlQ   = document.location.href.split('?');
		var params = urlQ[1] ? urlQ[1].toQueryParams() : [];
		var search = params['s'] ? params['s'] : '';
		var page   = params['apage'] ? params['apage'] : 1;
	}

	theCommentList.dimComplete = function(what,id,dimClass) {
		var m = document.getElementById('awaitmod');
		if ( document.getElementById(what + '-' + id).className.match(dimClass) )
			m.innerHTML = parseInt(m.innerHTML,10) + 1;
		else
			m.innerHTML = parseInt(m.innerHTML,10) - 1;
	}

	theCommentList.delComplete = function(what,id) {
		var m = document.getElementById('awaitmod');
		if ( document.getElementById(what + '-' + id).className.match('unapproved') )
			m.innerHTML = parseInt(m.innerHTML,10) - 1;
		if ( theExtraCommentList && commentNum ) {
			var theMover = theExtraCommentList.theList.childNodes[0];
			Element.removeClassName(theMover,'alternate');
			theCommentList.theList.appendChild(theMover);
			theExtraCommentList.inputData += '&page=' + page;
			if ( search )
				theExtraCommentList.inputData += '&s=' + search; // trust the URL not the search box
			theExtraCommentList.addComplete = function() {
				if ( theExtraCommentList.theList.childNodes )
					var commentNum = $A(theExtraCommentList.theList.childNodes).findAll( function(i) { return Element.visible(i) } ).length;
				else
					var commentNum = 0;
			}
			theExtraCommentList.ajaxAdder( 'comment', 'ajax-response' ); // Dummy Request
		}
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

