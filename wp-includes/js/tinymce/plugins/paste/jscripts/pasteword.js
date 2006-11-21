function saveContent() {
	var html = document.getElementById("frmData").contentWindow.document.body.innerHTML;

	if (html == ''){
		tinyMCEPopup.close();
		return false;
	}

	tinyMCEPopup.execCommand('mcePasteWord', false, html);
	tinyMCEPopup.close();
}

function onLoadInit() {
	tinyMCEPopup.resizeToInnerSize();

	// Fix for endless reloading in FF
	window.setTimeout('createIFrame();', 10);
}

function createIFrame() {
	document.getElementById('iframecontainer').innerHTML = '<iframe id="frmData" name="frmData" class="sourceIframe" src="blank.htm" height="280" width="400" frameborder="0" style="background-color:#FFFFFF; width:100%;" dir="ltr" wrap="soft"></iframe>';
}

var wHeight=0, wWidth=0, owHeight=0, owWidth=0;

function initIframe(doc) {
	var dir = tinyMCE.selectedInstance.settings['directionality'];

	doc.body.dir = dir;

	// Remove Gecko spellchecking
	if (tinyMCE.isGecko)
		doc.body.spellcheck = tinyMCE.getParam("gecko_spellcheck");

	resizeInputs();
}

function resizeInputs() {
	if (!tinyMCE.isMSIE) {
		wHeight = self.innerHeight - 80;
		wWidth = self.innerWidth - 18;
	} else {
		wHeight = document.body.clientHeight - 80;
		wWidth = document.body.clientWidth - 18;
	}

	var elm = document.getElementById('frmData');
	if (elm) {
		elm.style.height = Math.abs(wHeight) + 'px';
		elm.style.width  = Math.abs(wWidth) + 'px';
	}
}
