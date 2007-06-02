addLoadEvent(function(){linkcatList=new listMan('linkcategorychecklist');linkcatList.ajaxRespEl='jaxcat';linkcatList.topAdder=1;linkcatList.alt=0;linkcatList.showLink=0;});
addLoadEvent(newLinkCatAddIn);
function newLinkCatAddIn() {
	var jaxcat = $('jaxcat');
	if ( !jaxcat )
		return false;
	Element.update(jaxcat,'<span id="ajaxcat"><input type="text" name="newcat" id="newcat" size="16" autocomplete="off"/><input type="button" name="Button" id="catadd" value="' + linkcatL10n.add + '"/><input type="hidden"/><span id="howto">' + linkcatL10n.how + '</span></span>');
	$('newcat').onkeypress = function(e) { return killSubmit("linkcatList.ajaxAdder('link-category','jaxcat');", e); };
	$('catadd').onclick = function() { linkcatList.ajaxAdder('link-category', 'jaxcat'); };
}
