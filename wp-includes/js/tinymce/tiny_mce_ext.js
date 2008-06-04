tinyMCEPreInit.start = function() {
	var t = this, each = tinymce.each, s = t.settings, sl = tinymce.ScriptLoader, ln = s.languages, th = s.themes;

	function load(u, sp) {
		var o;

		if (!sp)
			u = t.base + u;

		o = {url : u, state : 2};
		sl.queue.push(o);
		sl.lookup[o.url] = o;
	};

	sl.markDone(t.base + '/langs/' + ln + '.js');

	load('/themes/' + th + '/editor_template' + t.suffix + '.js');
	sl.markDone(t.base + '/themes/' + th + '/langs/' + ln + '.js');
	sl.markDone(t.base + '/themes/' + th + '/langs/' + ln + '_dlg.js');

	each(s.plugins.split(','), function(n) {
		if (n && n.charAt(0) != '-') {
			load('/plugins/' + n + '/editor_plugin' + t.suffix + '.js');

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
