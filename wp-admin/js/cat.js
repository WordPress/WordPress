addLoadEvent(function(){catList=new listMan('categorychecklist');catList.ajaxRespEl='jaxcat';catList.topAdder=1;catList.alt=0;catList.showLink=0;});
addLoadEvent(newCatAddIn);
function newCatAddIn() {
	var jaxcat = $('jaxcat');
	if ( !jaxcat )
		return false;
	// These multiple blank hidden inputs are needed: https://bugzilla.mozilla.org/show_bug.cgi?id=377815 , #3895 , #4664
	Element.update(jaxcat,'<span id="ajaxcat"><input type="text" name="newcat" id="newcat" size="16" autocomplete="off"/><input type="button" name="Button" id="catadd" value="' + catL10n.add + '"/><input type="hidden"/><input type="hidden"/><span id="howto">' + catL10n.how + '</span></span>');
	$('newcat').onkeypress = function(e) { return killSubmit("catList.ajaxAdder('category','jaxcat');", e); };
	$('catadd').onclick = function() { catList.ajaxAdder('category', 'jaxcat'); };
}
