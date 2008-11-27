jQuery(function($) {
	var gallerySortable;
	var gallerySortableInit = function() {
		gallerySortable = $('#media-items').sortable( {
			items: '.media-item',
			placeholder: 'sorthelper',
			axis: 'y',
			distance: 2,
			update: galleryReorder
		} );
	}

	// When an update has occurred, adjust the order for each item
	var galleryReorder = function(e, sort) {
		jQuery.each(sort['element'].sortable('toArray'), function(i, id) {
			jQuery('#' + id + ' .menu_order input')[0].value = (1+i);
		});
	}

	// initialize sortable
	gallerySortableInit();
});

jQuery(document).ready(function($){
	$('.menu_order_input').each(function(){
		if ( this.value == '0' ) this.value = '';
	});

	if ( $('#media-items>*').length > 1 ) {
		var w = wpgallery.getWin();

		$('#save-all, #gallery-settings').show();
		if ( typeof w.tinyMCE != 'undefined' && w.tinyMCE.activeEditor && ! w.tinyMCE.activeEditor.isHidden() ) {
			wpgallery.mcemode = true;
			wpgallery.init();
		} else {
			$('#insert-gallery').show();
		}
	}
});

jQuery(window).unload( function () { tinymce = tinyMCE = wpgallery = null; } ); // Cleanup

/* gallery settings */
var tinymce = null, tinyMCE;

var wpgallery = {
	mcemode : false,
	editor : {},
	dom : {},
	is_update : false,
	el : {},

	I : function(e) {
		return document.getElementById(e);
	},

	init: function() {
		var t = this, li, q, i, it, w = t.getWin();

		if ( ! t.mcemode ) return;

		li = ('' + document.location.search).replace(/^\?/, '').split('&');
		q = {};
		for (i=0; i<li.length; i++) {
			it = li[i].split('=');
			q[unescape(it[0])] = unescape(it[1]);
		}

		if (q.mce_rdomain)
			document.domain = q.mce_rdomain;

		// Find window & API
		tinymce = w.tinymce;
		tinyMCE = w.tinyMCE;
		t.editor = tinymce.EditorManager.activeEditor;

		t.setup();
	},

	getWin : function() {
		return window.dialogArguments || opener || parent || top;
	},

	restoreSelection : function() {
		var t = this;

		if (tinymce.isIE)
			t.editor.selection.moveToBookmark(t.editor.windowManager.bookmark);
	},

	setup : function() {
		var t = this, a, f = document.forms[0], ed = t.editor, el, g;
		if ( ! t.mcemode ) return;

		t.restoreSelection();
		t.el = ed.selection.getNode();

		if ( t.el.nodeName != 'IMG' || ! ed.dom.hasClass(t.el, 'wpGallery') ) {
			if ( (g = ed.dom.select('img.wpGallery')) && g[0] ) {
				t.el = g[0];
			} else {
				if ( getUserSetting('galfile') == '1' ) t.I('linkto-file').checked = "checked";
				if ( getUserSetting('galdesc') == '1' ) t.I('order-desc').checked = "checked";
				if ( getUserSetting('galcols') ) t.I('columns').value = getUserSetting('galcols');
				if ( getUserSetting('galord') ) t.I('orderby').value = getUserSetting('galord');
				jQuery('#insert-gallery').show();
				return;
			}
		}

		a = ed.dom.getAttrib(t.el, 'title');
		a = ed.dom.decode(a);

		if ( a ) {
			jQuery('#update-gallery').show();
			t.is_update = true;

			var columns = a.match(/columns=['"]([0-9]+)['"]/), link = a.match(/link=['"]([^'"]+)['"]/i);
			var order = a.match(/order=['"]([^'"]+)['"]/i), orderby = a.match(/orderby=['"]([^'"]+)['"]/i), all = '';

			if ( link && link[1] ) t.I('linkto-file').checked = "checked";
			if ( order && order[1] ) t.I('order-desc').checked = "checked";
			if ( columns && columns[1] ) t.I('columns').value = ''+columns[1];
			if ( orderby && orderby[1] ) t.I('orderby').value = orderby[1];
		} else {
			jQuery('#insert-gallery').show();
		}
	},

	update : function() {
		var t = this, ed = t.editor, el, all = '';

		if ( ! t.mcemode || ! t.is_update ) {
			var s = '[gallery'+t.getSettings()+']';
			t.getWin().send_to_editor(s);
			return;
		}

		if (t.el.nodeName != 'IMG') return;

		all = ed.dom.decode(ed.dom.getAttrib(t.el, 'title'));
		all = all.replace(/\s*(order|link|columns|orderby)=['"]([^'"]+)['"]/gi, '');
		all += t.getSettings();

		ed.dom.setAttrib(t.el, 'title', all);
		t.getWin().tb_remove();
	},

	getSettings : function() {
		var I = this.I, s = '';

		if ( I('linkto-file').checked ) {
			s += ' link="file"';
			setUserSetting('galfile', '1');
		}

		if ( I('order-desc').checked ) {
			s += ' order="DESC"';
			setUserSetting('galdesc', '1');
		}

		if ( I('columns').value != 3 ) {
			s += ' columns="'+I('columns').value+'"';
			setUserSetting('galcols', I('columns').value);
		}

		if ( I('orderby').value != 'menu_order' ) {
			s += ' orderby="'+I('orderby').value+'"';
			setUserSetting('galord', I('orderby').value);
		}

		return s;
	}
};
