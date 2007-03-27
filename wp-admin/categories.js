addLoadEvent(function() {
	if (!theList.theList) return false;
	document.forms.addcat.submit.onclick = function(e) {return killSubmit('theList.ajaxAdder("cat", "addcat");', e); };
	theList.addComplete = function(what, where, update, transport) {
		var name = getNodeValue(transport.responseXML, 'name').unescapeHTML();
		var id = transport.responseXML.getElementsByTagName(what)[0].getAttribute('id');
		var options = document.forms['addcat'].category_parent.options;
		options[options.length] = new Option(name, id);
	};
	theList.delComplete = function(what, id) {
		var options = document.forms['addcat'].category_parent.options;
		for ( var o = 0; o < options.length; o++ )
			if ( id == options[o].value )
				options[o] = null;
	};
});
