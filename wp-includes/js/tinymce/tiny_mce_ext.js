
tinyMCE_GZ.start = function() {
	var t = this, each = tinymce.each, s = t.settings, sl = tinymce.ScriptLoader, ln = s.languages.split(',');

	function load(u, sp) {
		var o;

		if (!sp)
			u = t.baseURL + u;

		o = {url : u, state : 2};
		sl.queue.push(o);
		sl.lookup[o.url] = o;
	};

	// Add core languages
	each (ln, function(c) {
		if (c)
			load('/langs/' + c + '.js');
	});

	// Add themes with languages
	each(s.themes.split(','), function(n) {
		if (n) {
			load('/themes/' + n + '/editor_template' + s.suffix + '.js');

			each (ln, function(c) {
				if (c)
					load('/themes/' + n + '/langs/' + c + '.js');
			});
		}
	});

	// Add plugins with languages
	each(s.plugins.split(','), function(n) {
		if (n && n.charAt(0) != '-') {
			load('/plugins/' + n + '/editor_plugin' + s.suffix + '.js');

			each (ln, function(c) {
				if (c)
					load('/plugins/' + n + '/langs/' + c + '.js');
			});
		}
	});
};
