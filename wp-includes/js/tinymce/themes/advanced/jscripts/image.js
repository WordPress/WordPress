var url = tinyMCE.getParam("external_image_list_url");
if (url != null) {
	// Fix relative
	if (url.charAt(0) != '/' && url.indexOf('://') == -1)
		url = tinyMCE.documentBasePath + "/" + url;

	document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + url + '"></sc'+'ript>');
}

function insertImage() {
	if (window.opener) {
		var src = document.forms[0].src.value;
		var alt = document.forms[0].alt.value;
		var border = '';//document.forms[0].border.value;
		var vspace = '';//document.forms[0].vspace.value;
		var hspace = '';//document.forms[0].hspace.value;
		var width = '';//document.forms[0].width.value;
		var height = '';//document.forms[0].height.value;
		var align = document.forms[0].align.options[document.forms[0].align.selectedIndex].value;

		window.opener.tinyMCE.insertImage(src, alt, border, hspace, vspace, width, height, align);
		top.close();
	}
}

function init() {
	tinyMCEPopup.resizeToInnerSize();

	document.getElementById('srcbrowsercontainer').innerHTML = getBrowserHTML('srcbrowser','src','image','theme_advanced_image');

	var formObj = document.forms[0];

	for (var i=0; i<document.forms[0].align.options.length; i++) {
		if (document.forms[0].align.options[i].value == tinyMCE.getWindowArg('align'))
			document.forms[0].align.options.selectedIndex = i;
	}

	formObj.src.value = tinyMCE.getWindowArg('src');
	formObj.alt.value = tinyMCE.getWindowArg('alt');
//	formObj.border.value = tinyMCE.getWindowArg('border');
//	formObj.vspace.value = tinyMCE.getWindowArg('vspace');
//	formObj.hspace.value = tinyMCE.getWindowArg('hspace');
//	formObj.width.value = tinyMCE.getWindowArg('width');
//	formObj.height.value = tinyMCE.getWindowArg('height');
	formObj.insert.value = tinyMCE.getLang('lang_' + tinyMCE.getWindowArg('action'), 'Insert', true); 

	// Handle file browser
	if (isVisible('srcbrowser'))
		document.getElementById('src').style.width = '180px';

	// Auto select image in list
	if (typeof(tinyMCEImageList) != "undefined" && tinyMCEImageList.length > 0) {
		for (var i=0; i<formObj.image_list.length; i++) {
			if (formObj.image_list.options[i].value == tinyMCE.getWindowArg('src'))
				formObj.image_list.options[i].selected = true;
		}
	}
}

var preloadImg = new Image();

function resetImageData() {
	var formObj = document.forms[0];
	formObj.width.value = formObj.height.value = "";
}

function updateImageData() {
	var formObj = document.forms[0];

//	if (formObj.width.value == "")
//		formObj.width.value = preloadImg.width;

//	if (formObj.height.value == "")
//		formObj.height.value = preloadImg.height;
}

function getImageData() {
	preloadImg = new Image();
	tinyMCE.addEvent(preloadImg, "load", updateImageData);
	tinyMCE.addEvent(preloadImg, "error", function () {var formObj = document.forms[0];formObj.width.value = formObj.height.value = "";});
	preloadImg.src = tinyMCE.convertRelativeToAbsoluteURL(tinyMCE.settings['base_href'], document.forms[0].src.value);
}
