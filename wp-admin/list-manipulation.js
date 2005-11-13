var listItems;
var reg_color = '#FFFFFF';
var alt_color = '#F1F1F1';

addLoadEvent(getListItems);

function deleteSomething( what, id, message ) {
	what.replace('-', ' ');
	if (!message) message = 'Are you sure you want to delete this ' + what + '?';
	if ( confirm(message) ) {
		return ajaxDelete( what.replace(' ', '-'), id );
	} else {
		return false;
	}
}

function getResponseElement() {
	var p = document.getElementById('ajax-response-p');
	if (!p) {
		p = document.createElement('p');
		p.id = 'ajax-response-p';
		document.getElementById('ajax-response').appendChild(p);
		return p;
	}
}

function ajaxDelete(what, id) {
	ajaxDel = new sack('list-manipulation.php');
	if ( ajaxDel.failed ) return true;
	ajaxDel.myResponseElement = getResponseElement();
	ajaxDel.method = 'POST';
	ajaxDel.onLoading = function() { ajaxDel.myResponseElement.innerHTML = 'Sending Data...'; };
	ajaxDel.onLoaded = function() { ajaxDel.myResponseElement.innerHTML = 'Data Sent...'; };
	ajaxDel.onInteractive = function() { ajaxDel.myResponseElement.innerHTML = 'Processing Data...'; };
	ajaxDel.onCompletion = function() { removeThisItem( what + '-' + id ); };
	ajaxDel.runAJAX('action=delete-' + what + '&id=' + id);
	return false;
}

function removeThisItem(id) {
	var response = ajaxDel.response;
	if ( isNaN(response) ) { alert(response); }
	response = parseInt(response, 10);
	if ( -1 == response ) { ajaxDel.myResponseElement.innerHTML = "You don't have permission to do that."; }
	else if ( 0 == response ) { ajaxDel.myResponseElement.interHTML = "Something odd happened.  Try refreshing the page? Either that or what you tried to delete never existed in the first place."; }
	else if ( 1 == response ) {
		theItem = document.getElementById(id);
		Fat.fade_element(id,null,700,'#FF3333');
		setTimeout('theItem.parentNode.removeChild(theItem)', 705);
		var pos = getListPos(id);
		listItems.splice(pos,1);
		recolorList(pos);
		ajaxDel.myResponseElement.parentNode.removeChild(ajaxDel.myResponseElement);
		
	}
}

function getListPos(id) {
	for (var i = 0; i < listItems.length; i++) {
		if (id == listItems[i]) {
			var pos = i;
			break;
		}
	}
	return pos;
}	

function getListItems() {
	if (list) return;
	listItems = new Array();
	var extra = false;
	var list = document.getElementById('the-list');
	if (!list) { var list = document.getElementById('the-list-x'); extra = true; }
	if (list) {
		var items = list.getElementsByTagName('tr');
		if (!items[0]) { items = list.getElementsByTagName('li'); }
		for (var i=0; i<items.length; i++) { listItems.push(items[i].id); }
		if (extra) { listItems.splice(0,1); }
	}
}

function recolorList(pos,dur,from) {
	if (!pos) pos = 0;

	if (!from) {
		reg_from = alt_color;
		alt_from = reg_color;
	} else {
		reg_from = from;
		alt_from = from;
	}
	for (var i = pos; i < listItems.length; i++) {
		if (i % 2 == 1) Fat.fade_element(listItems[i],null,dur,reg_from,reg_color);
		else Fat.fade_element(listItems[i],null,dur,alt_from,alt_color);
	}
}
