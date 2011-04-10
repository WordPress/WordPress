tinyMCEPopup.requireLangPack();

function init() {
	var ed, tcont;

	tinyMCEPopup.resizeToInnerSize();
	ed = tinyMCEPopup.editor;

	// Give FF some time
	window.setTimeout(insertHelpIFrame, 10);

	tcont = document.getElementById('plugintablecontainer');
	document.getElementById('plugins_tab').style.display = 'none';

	var html = "";
	html += '<table id="plugintable">';
	html += '<thead>';
	html += '<tr>';
	html += '<td>' + ed.getLang('advanced_dlg.about_plugin') + '</td>';
	html += '<td>' + ed.getLang('advanced_dlg.about_author') + '</td>';
	html += '<td>' + ed.getLang('advanced_dlg.about_version') + '</td>';
	html += '</tr>';
	html += '</thead>';
	html += '<tbody>';

	tinymce.each(ed.plugins, function(p, n) {
		var info;

		if (!p.getInfo)
			return;

		html += '<tr>';

		info = p.getInfo();

		if (info.infourl != null && info.infourl != '')
			html += '<td width="50%" title="' + n + '"><a href="' + info.infourl + '" target="_blank">' + info.longname + '</a></td>';
		else
			html += '<td width="50%" title="' + n + '">' + info.longname + '</td>';

		if (info.authorurl != null && info.authorurl != '')
			html += '<td width="35%"><a href="' + info.authorurl + '" target="_blank">' + info.author + '</a></td>';
		else
			html += '<td width="35%">' + info.author + '</td>';

		html += '<td width="15%">' + info.version + '</td>';
		html += '</tr>';

		document.getElementById('plugins_tab').style.display = '';

	});

	html += '</tbody>';
	html += '</table>';

	tcont.innerHTML = html;

	tinyMCEPopup.dom.get('version').innerHTML = tinymce.majorVersion + "." + tinymce.minorVersion;
	tinyMCEPopup.dom.get('date').innerHTML = tinymce.releaseDate;
}

function insertHelpIFrame() {
	var html;

	if (tinyMCEPopup.getParam('docs_url')) {
		html = '<iframe width="100%" height="300" src="' + tinyMCEPopup.editor.baseURI.toAbsolute(tinyMCEPopup.getParam('docs_url')) + '"></iframe>';
		document.getElementById('iframecontainer').innerHTML = html;
		document.getElementById('help_tab').style.display = 'block';
		document.getElementById('help_tab').setAttribute("aria-hidden", "false");
	}
}

tinyMCEPopup.onInit.add(init);
