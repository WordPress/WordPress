
var tinymce = null, tinyMCEPopup, tinyMCE;

tinyMCEPopup = {
	init: function() {
		var t = this, w, ti, li, q, i, it;

		li = ('' + document.location.search).replace(/^\?/, '').split('&');
		q = {};
		for (i=0; i<li.length; i++) {
			it = li[i].split('=');
			q[unescape(it[0])] = unescape(it[1]);
		}

		if (q.mce_rdomain)
			document.domain = q.mce_rdomain;

		// Find window & API
		w = t.getWin();
		tinymce = w.tinymce;
		tinyMCE = w.tinyMCE;
		t.editor = tinymce.EditorManager.activeEditor;
		t.params = t.editor.windowManager.params;

		// Setup local DOM
		t.dom = t.editor.windowManager.createInstance('tinymce.dom.DOMUtils', document);
		t.editor.windowManager.onOpen.dispatch(t.editor.windowManager, window);
	},

	getWin : function() {
		return window.dialogArguments || opener || parent || top;
	},

	getParam : function(n, dv) {
		return this.editor.getParam(n, dv);
	},

	close : function() {
		var t = this, win = t.getWin();

		// To avoid domain relaxing issue in Opera
		function close() {
			win.tb_remove();
			tinymce = tinyMCE = t.editor = t.dom = t.dom.doc = null; // Cleanup
		};

		if (tinymce.isOpera)
			win.setTimeout(close, 0);
		else
			close();
	},

	execCommand : function(cmd, ui, val, a) {
		a = a || {};
		a.skip_focus = 1;

		this.restoreSelection();
		return this.editor.execCommand(cmd, ui, val, a);
	},

	storeSelection : function() {
		this.editor.windowManager.bookmark = tinyMCEPopup.editor.selection.getBookmark('simple');
	},

	restoreSelection : function() {
		var t = tinyMCEPopup;

		if (tinymce.isIE)
			t.editor.selection.moveToBookmark(t.editor.windowManager.bookmark);
	}
}
tinyMCEPopup.init();

var wpgallery = {
	preInit : function() {
		// import colors stylesheet from parent
		var win = tinyMCEPopup.getWin();
		var styles = win.document.styleSheets;

		for ( i = 0; i < styles.length; i++ ) {
			var url = styles.item(i).href;
			if ( url && url.indexOf('colors') != -1 )
				document.write( '<link rel="stylesheet" href="'+url+'" type="text/css" media="all" />' );
		}
	},

	I : function(e) {
		return document.getElementById(e);
	},

	init : function() {
		var ed = tinyMCEPopup.editor, h;

		h = document.body.innerHTML;

		// Replace a=x with a="x" in IE
		if (tinymce.isIE)
			h = h.replace(/ (value|title|alt)=([^"][^\s>]+)/gi, ' $1="$2"')

		document.body.innerHTML = ed.translate(h);
		window.setTimeout( function(){wpgallery.setup();}, 100 );
	},

	setup : function() {
		var t = this, a, f = document.forms[0], ed = tinyMCEPopup.editor, dom = tinyMCEPopup.dom;
		document.dir = tinyMCEPopup.editor.getParam('directionality','');

		tinyMCEPopup.restoreSelection();
		el = ed.selection.getNode();
		if (el.nodeName != 'IMG') return;

		a = ed.dom.getAttrib(el, 'title');
		a = ed.dom.decode(a);
		
		if ( a ) {
			var columns = a.match(/columns=['"]([0-9]+)['"]/), link = a.match(/link=['"]([^'"]+)['"]/i);
			var imgwidth = a.match(/imgwidth=['"]([0-9]+)['"]/), order = a.match(/order=['"]([^'"]+)['"]/i);
			var orderby = a.match(/orderby=['"]([^'"]+)['"]/i), all = '';
			
			if ( link && link[1] ) t.I('linkto-file').checked = "checked";
			if ( order && order[1] ) t.I('order-desc').checked = "checked";
			if ( columns && columns[1] ) t.I('columns').value = ''+columns[1];
			if ( orderby && orderby[1] ) t.I('orderby').value = orderby[1];
			if ( imgwidth && imgwidth[1] ) t.I('imgwidth').value = imgwidth[1];
		}

		document.body.style.display = '';
	},

	update : function() {
		var t = this, ed = tinyMCEPopup.editor, el, all;

		tinyMCEPopup.restoreSelection();
		el = ed.selection.getNode();

		if (el.nodeName != 'IMG') return;

		all = ed.dom.decode(ed.dom.getAttrib(el, 'title'));
		all = all.substr(0, all.lastIndexOf(']'));
		all = all.replace(/\s*(order|link|columns|orderby|imgwidth)=['"]([^'"]+)['"]/gi, '');

		if ( t.I('linkto-file').checked )
			all += ' link="file"';

		if ( t.I('order-desc').checked )
			all += ' order="DESC"';

		if ( t.I('columns').value != 3 )
			all += ' columns="'+t.I('columns').value+'"';

		if ( t.I('orderby').value != 'menu_order' )
			all += ' orderby="'+t.I('orderby').value+'"';

		if ( t.I('imgwidth').value )
			all += ' imgwidth="'+t.I('imgwidth').value+'"';

		all += ']';

		ed.dom.setAttrib(el, 'title', all);
		tinyMCEPopup.close();
	}
};

window.onload = function(){wpgallery.init();}
wpgallery.preInit();
