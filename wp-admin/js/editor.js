
var switchEditors = {

	switchto: function(el) {
		var aid = el.id, l = aid.length, id = aid.substr(0, l - 5), mode = aid.substr(l - 4);

		this.go(id, mode);
	},

	go: function(id, mode) { // mode can be 'html', 'tmce', or 'toggle'; 'html' is used for the "Text" editor tab.
		id = id || 'content';
		mode = mode || 'toggle';

		var t = this, ed = tinyMCE.get(id), wrap_id, txtarea_el, dom = tinymce.DOM;

		wrap_id = 'wp-'+id+'-wrap';
		txtarea_el = dom.get(id);

		if ( 'toggle' == mode ) {
			if ( ed && !ed.isHidden() )
				mode = 'html';
			else
				mode = 'tmce';
		}

		if ( 'tmce' == mode || 'tinymce' == mode ) {
			if ( ed && ! ed.isHidden() )
				return false;

			if ( typeof(QTags) != 'undefined' )
				QTags.closeAllTags(id);

			if ( tinyMCEPreInit.mceInit[id] && tinyMCEPreInit.mceInit[id].wpautop )
				txtarea_el.value = t.wpautop( txtarea_el.value );

			if ( ed ) {
				ed.show();
			} else {
				ed = new tinymce.Editor(id, tinyMCEPreInit.mceInit[id]);
				ed.render();
			}

			dom.removeClass(wrap_id, 'html-active');
			dom.addClass(wrap_id, 'tmce-active');
			setUserSetting('editor', 'tinymce');

		} else if ( 'html' == mode ) {

			if ( ed && ed.isHidden() )
				return false;

			if ( ed ) {
				ed.hide();
			} else {
				// The TinyMCE instance doesn't exist, run the content through "pre_wpautop()" and show the textarea
				if ( tinyMCEPreInit.mceInit[id] && tinyMCEPreInit.mceInit[id].wpautop )
					txtarea_el.value = t.pre_wpautop( txtarea_el.value );

				dom.setStyles(txtarea_el, {'display': '', 'visibility': ''});
			}

			dom.removeClass(wrap_id, 'tmce-active');
			dom.addClass(wrap_id, 'html-active');
			setUserSetting('editor', 'html');
		}
		return false;
	},

	_wp_Nop : function(content) {
		var blocklist1, blocklist2, preserve_linebreaks = false, preserve_br = false;

		// Protect pre|script tags
		if ( content.indexOf('<pre') != -1 || content.indexOf('<script') != -1 ) {
			preserve_linebreaks = true;
			content = content.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
				a = a.replace(/<br ?\/?>(\r\n|\n)?/g, '<wp-temp-lb>');
				return a.replace(/<\/?p( [^>]*)?>(\r\n|\n)?/g, '<wp-temp-lb>');
			});
		}

		// keep <br> tags inside captions and remove line breaks
		if ( content.indexOf('[caption') != -1 ) {
			preserve_br = true;
			content = content.replace(/\[caption[\s\S]+?\[\/caption\]/g, function(a) {
				return a.replace(/<br([^>]*)>/g, '<wp-temp-br$1>').replace(/[\r\n\t]+/, '');
			});
		}

		// Pretty it up for the source editor
		blocklist1 = 'blockquote|ul|ol|li|table|thead|tbody|tfoot|tr|th|td|div|h[1-6]|p|fieldset';
		content = content.replace(new RegExp('\\s*</('+blocklist1+')>\\s*', 'g'), '</$1>\n');
		content = content.replace(new RegExp('\\s*<((?:'+blocklist1+')(?: [^>]*)?)>', 'g'), '\n<$1>');

		// Mark </p> if it has any attributes.
		content = content.replace(/(<p [^>]+>.*?)<\/p>/g, '$1</p#>');

		// Separate <div> containing <p>
		content = content.replace(/<div( [^>]*)?>\s*<p>/gi, '<div$1>\n\n');

		// Remove <p> and <br />
		content = content.replace(/\s*<p>/gi, '');
		content = content.replace(/\s*<\/p>\s*/gi, '\n\n');
		content = content.replace(/\n[\s\u00a0]+\n/g, '\n\n');
		content = content.replace(/\s*<br ?\/?>\s*/gi, '\n');

		// Fix some block element newline issues
		content = content.replace(/\s*<div/g, '\n<div');
		content = content.replace(/<\/div>\s*/g, '</div>\n');
		content = content.replace(/\s*\[caption([^\[]+)\[\/caption\]\s*/gi, '\n\n[caption$1[/caption]\n\n');
		content = content.replace(/caption\]\n\n+\[caption/g, 'caption]\n\n[caption');

		blocklist2 = 'blockquote|ul|ol|li|table|thead|tbody|tfoot|tr|th|td|h[1-6]|pre|fieldset';
		content = content.replace(new RegExp('\\s*<((?:'+blocklist2+')(?: [^>]*)?)\\s*>', 'g'), '\n<$1>');
		content = content.replace(new RegExp('\\s*</('+blocklist2+')>\\s*', 'g'), '</$1>\n');
		content = content.replace(/<li([^>]*)>/g, '\t<li$1>');

		if ( content.indexOf('<hr') != -1 ) {
			content = content.replace(/\s*<hr( [^>]*)?>\s*/g, '\n\n<hr$1>\n\n');
		}

		if ( content.indexOf('<object') != -1 ) {
			content = content.replace(/<object[\s\S]+?<\/object>/g, function(a){
				return a.replace(/[\r\n]+/g, '');
			});
		}

		// Unmark special paragraph closing tags
		content = content.replace(/<\/p#>/g, '</p>\n');
		content = content.replace(/\s*(<p [^>]+>[\s\S]*?<\/p>)/g, '\n$1');

		// Trim whitespace
		content = content.replace(/^\s+/, '');
		content = content.replace(/[\s\u00a0]+$/, '');

		// put back the line breaks in pre|script
		if ( preserve_linebreaks )
			content = content.replace(/<wp-temp-lb>/g, '\n');

		// and the <br> tags in captions
		if ( preserve_br )
			content = content.replace(/<wp-temp-br([^>]*)>/g, '<br$1>');

		return content;
	},

	_wp_Autop : function(pee) {
		var preserve_linebreaks = false, preserve_br = false,
			blocklist = 'table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|p|h[1-6]|hr|fieldset|noscript|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary';

		if ( pee.indexOf('<object') != -1 ) {
			pee = pee.replace(/<object[\s\S]+?<\/object>/g, function(a){
				return a.replace(/[\r\n]+/g, '');
			});
		}

		pee = pee.replace(/<[^<>]+>/g, function(a){
			return a.replace(/[\r\n]+/g, ' ');
		});

		// Protect pre|script tags
		if ( pee.indexOf('<pre') != -1 || pee.indexOf('<script') != -1 ) {
			preserve_linebreaks = true;
			pee = pee.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
				return a.replace(/(\r\n|\n)/g, '<wp-temp-lb>');
			});
		}

		// keep <br> tags inside captions and convert line breaks
		if ( pee.indexOf('[caption') != -1 ) {
			preserve_br = true;
			pee = pee.replace(/\[caption[\s\S]+?\[\/caption\]/g, function(a) {
				// keep existing <br>
				a = a.replace(/<br([^>]*)>/g, '<wp-temp-br$1>');
				// no line breaks inside HTML tags
				a = a.replace(/<[a-zA-Z0-9]+( [^<>]+)?>/g, function(b){
					return b.replace(/[\r\n\t]+/, ' ');
				});
				// convert remaining line breaks to <br>
				return a.replace(/\s*\n\s*/g, '<wp-temp-br />');
			});
		}

		pee = pee + '\n\n';
		pee = pee.replace(/<br \/>\s*<br \/>/gi, '\n\n');
		pee = pee.replace(new RegExp('(<(?:'+blocklist+')(?: [^>]*)?>)', 'gi'), '\n$1');
		pee = pee.replace(new RegExp('(</(?:'+blocklist+')>)', 'gi'), '$1\n\n');
		pee = pee.replace(/<hr( [^>]*)?>/gi, '<hr$1>\n\n'); // hr is self closing block element
		pee = pee.replace(/\r\n|\r/g, '\n');
		pee = pee.replace(/\n\s*\n+/g, '\n\n');
		pee = pee.replace(/([\s\S]+?)\n\n/g, '<p>$1</p>\n');
		pee = pee.replace(/<p>\s*?<\/p>/gi, '');
		pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')(?: [^>]*)?>)\\s*</p>', 'gi'), "$1");
		pee = pee.replace(/<p>(<li.+?)<\/p>/gi, '$1');
		pee = pee.replace(/<p>\s*<blockquote([^>]*)>/gi, '<blockquote$1><p>');
		pee = pee.replace(/<\/blockquote>\s*<\/p>/gi, '</p></blockquote>');
		pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')(?: [^>]*)?>)', 'gi'), "$1");
		pee = pee.replace(new RegExp('(</?(?:'+blocklist+')(?: [^>]*)?>)\\s*</p>', 'gi'), "$1");
		pee = pee.replace(/\s*\n/gi, '<br />\n');
		pee = pee.replace(new RegExp('(</?(?:'+blocklist+')[^>]*>)\\s*<br />', 'gi'), "$1");
		pee = pee.replace(/<br \/>(\s*<\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)/gi, '$1');
		pee = pee.replace(/(?:<p>|<br ?\/?>)*\s*\[caption([^\[]+)\[\/caption\]\s*(?:<\/p>|<br ?\/?>)*/gi, '[caption$1[/caption]');

		pee = pee.replace(/(<(?:div|th|td|form|fieldset|dd)[^>]*>)(.*?)<\/p>/g, function(a, b, c) {
			if ( c.match(/<p( [^>]*)?>/) )
				return a;

			return b + '<p>' + c + '</p>';
		});

		// put back the line breaks in pre|script
		if ( preserve_linebreaks )
			pee = pee.replace(/<wp-temp-lb>/g, '\n');

		if ( preserve_br )
			pee = pee.replace(/<wp-temp-br([^>]*)>/g, '<br$1>');

		return pee;
	},

	pre_wpautop : function(content) {
		var t = this, o = { o: t, data: content, unfiltered: content },
			q = typeof(jQuery) != 'undefined';

		if ( q )
			jQuery('body').trigger('beforePreWpautop', [o]);
		o.data = t._wp_Nop(o.data);
		if ( q )
			jQuery('body').trigger('afterPreWpautop', [o]);

		return o.data;
	},

	wpautop : function(pee) {
		var t = this, o = { o: t, data: pee, unfiltered: pee },
			q = typeof(jQuery) != 'undefined';

		if ( q )
			jQuery('body').trigger('beforeWpautop', [o]);
		o.data = t._wp_Autop(o.data);
		if ( q )
			jQuery('body').trigger('afterWpautop', [o]);

		return o.data;
	}
}
