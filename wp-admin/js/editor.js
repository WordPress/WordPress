wpEditorInit = function() {
    // Activate tinyMCE if it's the user's default editor
    if ( ( 'undefined' == typeof wpTinyMCEConfig ) || 'tinymce' == wpTinyMCEConfig.defaultEditor ) {
        document.getElementById('editorcontainer').style.padding = '0px';
        tinyMCE.execCommand("mceAddControl", false, "content");
	} else {
        var H;
        if ( H = tinymce.util.Cookie.getHash("TinyMCE_content_size") )
            document.getElementById('content').style.height = H.ch - 30 + 'px';
    }
};

switchEditors = {

    saveCallback : function(el, content, body) {
    
        document.getElementById(el).style.color = '#fff';
        if ( tinyMCE.activeEditor.isHidden() ) 
            content = document.getElementById(el).value;
        else
            content = this.pre_wpautop(content);

        return content;
    },

    pre_wpautop : function(content) {
        // We have a TON of cleanup to do. Line breaks are already stripped.

        // Pretty it up for the source editor
        var blocklist1 = 'blockquote|ul|ol|li|table|thead|tbody|tr|th|td|div|h[1-6]|pre|p';
        content = content.replace(new RegExp('\\s*</('+blocklist1+')>\\s*', 'mg'), '</$1>\n');
        content = content.replace(new RegExp('\\s*<(('+blocklist1+')[^>]*)>', 'mg'), '\n<$1>');

        // Mark </p> if it has any attributes.
        content = content.replace(new RegExp('(<p [^>]+>.*?)</p>', 'mg'), '$1</p#>');

        // Sepatate <div> containing <p>
        content = content.replace(new RegExp('<div([^>]*)>\\s*<p>', 'mgi'), '<div$1>\n\n');

        // Remove <p> and <br />
        content = content.replace(new RegExp('\\s*<p>', 'mgi'), '');
        content = content.replace(new RegExp('\\s*</p>\\s*', 'mgi'), '\n\n');
        content = content.replace(new RegExp('\\n\\s*\\n', 'mgi'), '\n\n');
        content = content.replace(new RegExp('\\s*<br ?/?>\\s*', 'gi'), '\n');

        // Fix some block element newline issues
        content = content.replace(new RegExp('\\s*<div', 'mg'), '\n<div');
        content = content.replace(new RegExp('</div>\\s*', 'mg'), '</div>\n');
        
        var blocklist2 = 'blockquote|ul|ol|li|table|thead|tr|th|td|h[1-6]|pre';
        content = content.replace(new RegExp('\\s*<(('+blocklist2+') ?[^>]*)\\s*>', 'mg'), '\n<$1>');
        content = content.replace(new RegExp('\\s*</('+blocklist2+')>\\s*', 'mg'), '</$1>\n');
        content = content.replace(new RegExp('<li([^>]*)>', 'g'), '\t<li$1>');

        if ( content.indexOf('<object') != -1 ) {
            content = content.replace(new RegExp('\\s*<param([^>]*)>\\s*', 'mg'), "<param$1>");
            content = content.replace(new RegExp('\\s*</embed>\\s*', 'mg'), '</embed>');
        }

        // Unmark special paragraph closing tags
        content = content.replace(new RegExp('</p#>', 'g'), '</p>\n');
        content = content.replace(new RegExp('\\s*(<p [^>]+>.*</p>)', 'mg'), '\n$1');

        // Trim whitespace
        content = content.replace(new RegExp('^\\s*', ''), '');
        content = content.replace(new RegExp('\\s*$', ''), '');

        // Hope.
        return content;
    },

    go : function(id) {
        var ed = tinyMCE.get(id);
        var qt = document.getElementById('quicktags');
        var H = document.getElementById('edButtonHTML');
        var P = document.getElementById('edButtonPreview');
        var ta = document.getElementById(id);
        var ec = document.getElementById('editorcontainer');

        if ( ! ed || ed.isHidden() ) {
            ta.style.color = '#fff';

            this.edToggle(P, H);
            edCloseAllTags(); // :-(

            qt.style.display = 'none';
            ec.style.padding = '0px';
            ta.style.padding = '0px';

            ta.value = this.wpautop(ta.value);

            if ( ed ) ed.show();
            else tinyMCE.execCommand("mceAddControl", false, id);

            this.wpSetDefaultEditor('tinymce');
        } else {
            this.edToggle(H, P);
            ta.style.height = ed.getContentAreaContainer().offsetHeight + 6 + 'px';

            ed.hide();
            qt.style.display = 'block';

            if ( tinymce.isIE6 ) {
				ta.style.width = '98%';
				ec.style.padding = '0px';
				ta.style.padding = '6px';
			} else {
				ta.style.width = '100%';
				ec.style.padding = '6px';
            }

			ta.style.color = '';
            this.wpSetDefaultEditor('html');
        }
    },

    edToggle : function(A, B) {
        A.className = 'active';
        B.className = '';

        B.onclick = A.onclick;
        A.onclick = null;
    },

    wpSetDefaultEditor : function(editor) {
        try {
            editor = escape( editor.toString() );
        } catch(err) {
            editor = 'tinymce';
        }

        var userID = document.getElementById('user-id');
        var date = new Date();
        date.setTime(date.getTime()+(10*365*24*60*60*1000));
        document.cookie = "wordpress_editor_" + userID.value + "=" + editor + "; expires=" + date.toGMTString();
    },

    wpautop : function(pee) {
        var blocklist = 'table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6]';

        pee = pee + "\n\n";
        pee = pee.replace(new RegExp('<br />\\s*<br />', 'gi'), "\n\n");
        pee = pee.replace(new RegExp('(<(?:'+blocklist+')[^>]*>)', 'gi'), "\n$1");
        pee = pee.replace(new RegExp('(</(?:'+blocklist+')>)', 'gi'), "$1\n\n");
        pee = pee.replace(new RegExp("\\r\\n|\\r", 'g'), "\n");
        pee = pee.replace(new RegExp("\\n\\s*\\n+", 'g'), "\n\n");
        pee = pee.replace(new RegExp('([\\s\\S]+?)\\n\\n', 'mg'), "<p>$1</p>\n");
        pee = pee.replace(new RegExp('<p>\\s*?</p>', 'gi'), '');
        pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')[^>]*>)\\s*</p>', 'gi'), "$1");
        pee = pee.replace(new RegExp("<p>(<li.+?)</p>", 'gi'), "$1");
        pee = pee.replace(new RegExp('<p><blockquote([^>]*)>', 'gi'), "<blockquote$1><p>");
        pee = pee.replace(new RegExp('</blockquote></p>', 'gi'), '</p></blockquote>');
        pee = pee.replace(new RegExp('<p>\\s*(</?(?:'+blocklist+')[^>]*>)', 'gi'), "$1");
        pee = pee.replace(new RegExp('(</?(?:'+blocklist+')[^>]*>)\\s*</p>', 'gi'), "$1");
        pee = pee.replace(new RegExp('\\s*\\n', 'gi'), "<br />\n");
        pee = pee.replace(new RegExp('(</?(?:'+blocklist+')[^>]*>)\\s*<br />', 'gi'), "$1");
        pee = pee.replace(new RegExp('<br />(\\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)', 'gi'), '$1');
        pee = pee.replace(new RegExp('^((?:&nbsp;)*)\\s', 'mg'), '$1&nbsp;');
        //pee = pee.replace(new RegExp('(<pre.*?>)(.*?)</pre>!ise', " stripslashes('$1') .  stripslashes(clean_pre('$2'))  . '</pre>' "); // Hmm...
        return pee;
    }
}
