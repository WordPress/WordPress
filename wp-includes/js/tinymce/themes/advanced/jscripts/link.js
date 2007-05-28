var url = tinyMCE.getParam("external_link_list_url");
if (url != null) {
	// Fix relative
	if (url.charAt(0) != '/' && url.indexOf('://') == -1)
		url = tinyMCE.documentBasePath + "/" + url;

	document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></sc'+'ript>');
}

function init() {
	tinyMCEPopup.resizeToInnerSize();

	document.getElementById('hrefbrowsercontainer').innerHTML = getBrowserHTML('hrefbrowser','href','file','theme_advanced_link');

	// Handle file browser
	if (isVisible('hrefbrowser'))
		document.getElementById('href').style.width = '180px';

	var formObj = document.forms[0];

	for (var i=0; i<document.forms[0].target.options.length; i++) {
		var option = document.forms[0].target.options[i];

		if (option.value == tinyMCE.getWindowArg('target'))
			option.selected = true;
	}
	
	// WordPress -- next 3 lines
	document.forms[0].href.value = tinyMCE.getWindowArg('href') || 'http://';
	document.forms[0].href.select();
	document.forms[0].href.focus();
	
	document.forms[0].linktitle.value = tinyMCE.getWindowArg('title');
	document.forms[0].insert.value = tinyMCE.getLang('lang_' + tinyMCE.getWindowArg('action'), 'Insert', true); 

	addClassesToList('styleSelect', 'theme_advanced_link_styles');
	selectByValue(formObj, 'styleSelect', tinyMCE.getWindowArg('className'), true);

	// Hide css select row if no CSS classes
	if (formObj.styleSelect && formObj.styleSelect.options.length <= 1) {
		var sr = document.getElementById('styleSelectRow');
		sr.style.display = 'none';
		sr.parentNode.removeChild(sr);
	}

	// Auto select link in list
	if (typeof(tinyMCELinkList) != "undefined" && tinyMCELinkList.length > 0) {
		var formObj = document.forms[0];

		for (var i=0; i<formObj.link_list.length; i++) {
			if (formObj.link_list.options[i].value == tinyMCE.getWindowArg('href'))
				formObj.link_list.options[i].selected = true;
		}
	}
}

function checkPrefix(n) {
	if (Validator.isEmail(n) && !/^\s*mailto:/i.test(n.value) && confirm(tinyMCE.getLang('lang_is_email')))
		n.value = 'mailto:' + n.value;

	if (/^\s*www./i.test(n.value) && confirm(tinyMCE.getLang('lang_is_external')))
		n.value = 'http://' + n.value;
}

function insertLink() {
	var href = document.forms[0].href.value;
	var target = document.forms[0].target.options[document.forms[0].target.selectedIndex].value;
	var title = document.forms[0].linktitle.value;
	var style_class = document.forms[0].styleSelect ? document.forms[0].styleSelect.value : "";
	var dummy;
	
	// WordPress: Make anchors absolute;
	if (href.charAt(0) == '#')
		href = tinyMCE.settings.document_base_url + href;

	if (target == '_self')
		target = '';

	tinyMCEPopup.restoreSelection();
	tinyMCE.themes['advanced']._insertLink(href, target, title, dummy, style_class);
	tinyMCEPopup.close();
}
