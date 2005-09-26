function init() {
	tinyMCEPopup.resizeToInnerSize();

	// Give FF some time
	window.setTimeout('insertHelpIFrame();', 10);

	var tcont = document.getElementById('plugintablecontainer');
	var plugins = tinyMCE.getParam('plugins', '', true, ',');
	if (plugins.length == 0)
		document.getElementById('plugins_tab').style.display = 'none';

	var html = "";
	html += '<table id="plugintable">';
	html += '<thead>';
	html += '<tr>';
	html += '<td>' + tinyMCE.getLang('lang_plugin') + '</td>';
	html += '<td>' + tinyMCE.getLang('lang_author') + '</td>';
	html += '<td>' + tinyMCE.getLang('lang_version') + '</td>';
	html += '</tr>';
	html += '</thead>';
	html += '<tbody>';

	for (var i=0; i<plugins.length; i++) {
		var info = getPluginInfo(plugins[i]);

		html += '<tr>';

		if (info.infourl != null && info.infourl != '')
			html += '<td width="50%" title="' + plugins[i] + '"><a href="' + info.infourl + '" target="_blank">' + info.longname + '</a></td>';
		else
			html += '<td width="50%" title="' + plugins[i] + '">' + info.longname + '</td>';

		if (info.authorurl != null && info.authorurl != '')
			html += '<td width="35%"><a href="' + info.authorurl + '" target="_blank">' + info.author + '</a></td>';
		else
			html += '<td width="35%">' + info.author + '</td>';

		html += '<td width="15%">' + info.version + '</td>';
		html += '</tr>';
	}

	html += '</tbody>';
	html += '</table>';

	tcont.innerHTML = html;
}

function getPluginInfo(name) {
	var fn = eval('tinyMCEPopup.windowOpener.TinyMCE_' + name + '_getInfo');

	if (typeof(fn) != 'undefined')
		return fn();

	return {
		longname : name,
		authorurl : '',
		infourl : '',
		author : '--',
		version : '--'
	};
}

function insertHelpIFrame() {
	var html = '<iframe width="100%" height="300" src="' + tinyMCE.themeURL + "/docs/" + tinyMCE.settings['docs_language'] + "/index.htm" + '"></iframe>';

	document.getElementById('iframecontainer').innerHTML = html;

	html = '';
	html += '<a href="http://www.moxiecode.com" target="_blank"><img src="http://tinymce.moxiecode.com/images/gotmoxie.png" alt="Got Moxie?" border="0" /></a> ';
	html += '<a href="http://sourceforge.net/projects/tinymce/" target="_blank"><img src="http://sourceforge.net/sflogo.php?group_id=103281" alt="Hosted By Sourceforge" border="0" /></a> ';
	html += '<a href="http://www.freshmeat.net/projects/tinymce" target="_blank"><img src="http://tinymce.moxiecode.com/images/fm.gif" alt="Also on freshmeat" border="0" /></a> ';

	document.getElementById('buttoncontainer').innerHTML = html;
}
