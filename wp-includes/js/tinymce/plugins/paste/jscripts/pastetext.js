function saveContent() {
	if (document.forms[0].htmlSource.value == '') {
		tinyMCEPopup.close();
		return false;
	}

	tinyMCEPopup.execCommand('mcePasteText', false, {
		html : document.forms[0].htmlSource.value,
		linebreaks : document.forms[0].linebreaks.checked
	});

	tinyMCEPopup.close();
}

function onLoadInit() {
	tinyMCEPopup.resizeToInnerSize();

	// Remove Gecko spellchecking
	if (tinyMCE.isGecko)
		document.body.spellcheck = tinyMCE.getParam("gecko_spellcheck");

	resizeInputs();
}

var wHeight=0, wWidth=0, owHeight=0, owWidth=0;

function resizeInputs() {
	if (!tinyMCE.isMSIE) {
		wHeight = self.innerHeight-80;
		wWidth = self.innerWidth-17;
	} else {
		wHeight = document.body.clientHeight-80;
		wWidth = document.body.clientWidth-17;
	}

	document.forms[0].htmlSource.style.height = Math.abs(wHeight) + 'px';
	document.forms[0].htmlSource.style.width  = Math.abs(wWidth) + 'px';
}
