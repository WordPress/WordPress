
var switchEditors = {
	
	go: function(a) {
		var t = this, aid = a.id, l = aid.length, id = aid.substr(0, l - 5), mode = aid.substr(l - 4),
			ed = tinyMCE.get(id), wrap_id = 'wp-'+id+'-wrap', dom = tinymce.DOM, txtarea_el = dom.get(id);
	
		if ( 'tmce' == mode ) {
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
				txtarea_el.style.height = ed.getContentAreaContainer().offsetHeight + 20 + 'px';
				ed.hide();
			}

			dom.removeClass(wrap_id, 'tmce-active');
			dom.addClass(wrap_id, 'html-active');
			setUserSetting('editor', 'html');
		}
		return false;
	},
	
	_wp_Nop : function(content) {
		var blocklist1, blocklist2;

		// Protect pre|script tags
		if ( content.indexOf('<pre') != -1 || content.indexOf('<script') != -1 ) {
			content = content.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
				a = a.replace(/<br ?\/?>(\r\n|\n)?/g, '<wp_temp>');
				return a.replace(/<\/?p( [^>]*)?>(\r\n|\n)?/g, '<wp_temp>');
			});
		}

		// Pretty it up for the source editor
		blocklist1 = 'blockquote|ul|ol|li|table|thead|tbody|tfoot|tr|th|td|div|h[1-6]|p|fieldset';
		content = content.replace(new RegExp('\\s*</('+blocklist1+')>\\s*', 'g'), '</$1>\n');
		content = content.replace(new RegExp('\\s*<((?:'+blocklist1+')(?: [^>]*)?)>', 'g'), '\n<$1>');

		// Mark </p> if it has any attributes.
		content = content.replace(/(<p [^>]+>.*?)<\/p>/g, '$1</p#>');

		// Sepatate <div> containing <p>
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
		content = content.replace(/<wp_temp>/g, '\n');

		return content;
	},

	_wp_Autop : function(pee) {
		var blocklist = 'table|thead|tfoot|tbody|tr|td|th|caption|col|colgroup|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6]|fieldset|legend|hr|noscript|menu|samp|header|footer|article|section|hgroup|nav|aside|details|summary';

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
			pee = pee.replace(/<(pre|script)[^>]*>[\s\S]+?<\/\1>/g, function(a) {
				return a.replace(/(\r\n|\n)/g, '<wp_temp_br>');
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
		pee = pee.replace(/<wp_temp_br>/g, '\n');

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

