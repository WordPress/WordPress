addLoadEvent(function() {theList.dimComplete = function(what,id,dimClass) {
	var m = document.getElementById('awaitmod');
	if ( document.getElementById(what + '-' + id).className.match(dimClass) ) m.innerHTML = parseInt(m.innerHTML,10) + 1;
	else m.innerHTML = parseInt(m.innerHTML,10) - 1;
}});
