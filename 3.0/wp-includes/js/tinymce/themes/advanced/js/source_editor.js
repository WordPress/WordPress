tinyMCEPopup.requireLangPack();
tinyMCEPopup.onInit.add(onLoadInit);

function saveContent() {
	tinyMCEPopup.editor.setContent(document.getElementById('htmlSource').value, {source_view : true});
	tinyMCEPopup.close();
}

function onLoadInit() {
	tinyMCEPopup.resizeToInnerSize();

	// Remove Gecko spellchecking
	if (tinymce.isGecko)
		document.body.spellcheck = tinyMCEPopup.editor.getParam("gecko_spellcheck");

	document.getElementById('htmlSource').value = tinyMCEPopup.editor.getContent({source_view : true});

	if (tinyMCEPopup.editor.getParam("theme_advanced_source_editor_wrap", true)) {
		setWrap('soft');
		document.getElementById('wraped').checked = true;
	}

	resizeInputs();
}

function setWrap(val) {
	var v, n, s = document.getElementById('htmlSource');

	s.wrap = val;

	if (!tinymce.isIE) {
		v = s.value;
		n = s.cloneNode(false);
		n.setAttribute("wrap", val);
		s.parentNode.replaceChild(n, s);
		n.value = v;
	}
}

function toggleWordWrap(elm) {
	if (elm.checked)
		setWrap('soft');
	else
		setWrap('off');
}

var wHeight=0, wWidth=0, owHeight=0, owWidth=0;

function resizeInputs() {
	var el = document.getElementById('htmlSource');

	if (!tinymce.isIE) {
		 wHeight = self.innerHeight - 65;
		 wWidth = self.innerWidth - 16;
	} else {
		 wHeight = document.body.clientHeight - 70;
		 wWidth = document.body.clientWidth - 16;
	}

	el.style.height = Math.abs(wHeight) + 'px';
	el.style.width  = Math.abs(wWidth) + 'px';
}
