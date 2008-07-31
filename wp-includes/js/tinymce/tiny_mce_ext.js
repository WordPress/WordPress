
tinyMCEPreInit.start = function() {
	var t = this, sl = tinymce.ScriptLoader, ln = t.mceInit.language, th = t.mceInit.theme, pl = t.mceInit.plugins;

	sl.markDone(t.base + '/langs/' + ln + '.js');

	sl.markDone(t.base + '/themes/' + th + '/langs/' + ln + '.js');
	sl.markDone(t.base + '/themes/' + th + '/langs/' + ln + '_dlg.js');

	tinymce.each(pl.split(','), function(n) {
		if (n && n.charAt(0) != '-') {
			sl.markDone(t.base + '/plugins/' + n + '/langs/' + ln + '.js');
			sl.markDone(t.base + '/plugins/' + n + '/langs/' + ln + '_dlg.js');
		}
	});
};

tinyMCEPreInit.load_ext = function(url,lang) {
	var sl = tinymce.ScriptLoader;

	sl.markDone(url + '/langs/' + lang + '.js');
	sl.markDone(url + '/langs/' + lang + '_dlg.js');
};
