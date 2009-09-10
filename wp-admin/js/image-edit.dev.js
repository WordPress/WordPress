var imageEdit;

(function($) {
imageEdit = {
	iasapi : {},

	intval : function(f) {
		return f | 0;
	},

	setState : function(el, s) {
		if ( s )
			el.removeAttr('disabled');
		else
			el.attr('disabled', 'disabled');
	},

	setClass : function(el, c) {
		if ( c )
			el.removeClass('disabled');
		else
			el.addClass('disabled');
	},

	gcd : function(a, b) {
		var r;
		if ( a == 0 || b == 0 )
			return 0;
		else if ( a == b )
			return a;
		else {
			do {
				r = a % b;
				a = b; b = r;
			} while ( r != 0 );
			return a;
		}
	},

	toggleEditor : function(postid, toggle) {
		var wait = $('#imgedit-wait-' + postid);

		if ( toggle )
			wait.height( $('#imgedit-panel-' + postid).height() ).fadeIn('fast');
		else
			wait.height(500).fadeOut('fast');
	},

	isChecked : function(chkbox) {
		return ( !chkbox.attr('disabled') && chkbox[0].checked );
	},

	getAspect : function(postid) {
		var enable = this.isChecked( $('#imgedit-scale-switch-' + postid) ), X, Y;

		if ( enable ) {
			X = this.intval( $('#imgedit-aspect-x-' + postid).val() );
			Y = this.intval( $('#imgedit-aspect-y-' + postid).val() );
			return X / Y;
		} else {
			return 0;
		}
	},

	scaleWidthChanged : function(postid) {
		var src = $('#imgedit-scale-width-' + postid), aspect;

		if ( !src.attr('disabled') ) {
			aspect = this.getAspect(postid);

			if ( aspect != 0 )
				$('#imgedit-scale-height-' + postid).val( (src.val() != '') ? this.intval( src.val() / aspect ) : '' );
		}
	},

	scaleHeightChanged : function(postid) {
		var src = $('#imgedit-scale-height-' + postid), aspect;
		if ( !src.attr('disabled') ) {
			aspect = this.getAspect(postid);
			if ( aspect != 0 )
				$('#imgedit-scale-width-' + postid).val( (src.val() != '') ? this.intval(src.val() * aspect) : '' );
		}
	},

	setDefaultAspect : function(postid) {
		var t = this, g, host = $('#image-preview-' + postid),
			X = host.attr('width'), Y = host.attr('height');

		while( (g = t.gcd(X, Y) ) > 1) {
			X = t.intval( Math.ceil(X / g) );
			Y = t.intval( Math.ceil(Y / g) );
		}

		if ( X > 10 && Y > 10 ) {
			while ( X > 10 && Y > 10 ) {
				X = t.intval( Math.ceil(X / 10) );
				Y = t.intval( Math.ceil(Y / 10) );
			}
			while( ( g = t.gcd(X, Y) ) > 1) {
				X = t.intval( Math.ceil(X / g) );
				Y = t.intval( Math.ceil(Y / g) );
			}
		}
		$('#imgedit-aspect-x-' + postid).val(X);
		$('#imgedit-aspect-y-' + postid).val(Y);
	},

	filterHistory : function(postid) {
		// apply undo state to history
		var history = $('#imgedit-history-' + postid).val(), pop;
		if ( history != '' ) {
			pop = this.intval( $('#imgedit-undone-' + postid).val() );
			if ( pop > 0 ) {
				history = JSON.parse(history);
				while ( pop > 0 ) {
					history.pop();
					pop--;
				}
				history = JSON.stringify(history);
			}
		}
		return history;
	},

	refreshEditor : function(postid, nonce, callback) {
		var t = this, data, host;

		t.toggleEditor(postid, 1);

		data = {
			'action': 'load-preview-image',
			'_ajax_nonce': nonce,
			'postid': postid,
			'history': t.filterHistory(postid),
			'rand': t.intval(Math.random() * 1000000)
		};

		host = $('<img id="image-preview-' + postid + '" />');
		host.load( function() {
			var parent = $('#imgedit-crop-' + postid);

			parent.empty().append(host);
			t.initCrop(postid, host, parent);
			$('#imgedit-panel-' + postid).show();

			if ( (typeof callback != "unknown") && callback != null )
				callback();

			t.toggleEditor(postid, 0);

		}).attr('src', ajaxurl + '?' + $.param(data));
	},

	save : function(postid, nonce) {
		var t = this, fwidth = -1, fheight = -1, w, h, data,
			scaled = t.isChecked( $('#imgedit-scale-switch-' + postid) ),
			target = $('#imgedit-save-target-' + postid).val();

		if ( scaled ) {
			w = $('#imgedit-scale-width-' + postid);
			h = $('#imgedit-scale-height-' + postid);
			fwidth = t.intval(w.val());
			fheight = t.intval(h.val());

			if ( fwidth <= 0 ) {
				w.focus();
				return;
			} else if ( fheight <= 0 ) {
				h.focus();
				return;
			}
		}

		t.toggleEditor(postid, 1);

		data = {
			'action': 'image-edit-save',
			'_ajax_nonce': nonce,
			'postid': postid,
			'history': t.filterHistory(postid),
			'target': target,
			'fwidth': fwidth,
			'fheight': fheight
		};

		$.post(ajaxurl, data, function(r) {
			var fields = r.split('!'), pair, res, fw, fh, i, thumbnail;

			for ( i = 0; i < fields.length; i++ ) {
				pair = fields[i].split('=');
				if ( pair.length == 2 ) {
					switch ( pair[0] ) {
					case 'full':
						// update full size dimensions
						res = pair[1].split('x');
						if ( res.length == 2 ) {
							fw = res[0];
							fh = res[1];
							$('#image-dims-' + postid).html( fw + '&nbsp;&times;&nbsp;' + fh );
						}

						// clear undo history, it's no longer valid since we changed the original full size image
						$('#imgedit-history-' + postid).val('');
						$('#imgedit-undone-' + postid).val(0);
						t.setClass($('#image-undo-' + postid), false);
						t.setClass($('#image-redo-' + postid), false);
						break;
					case 'thumbnail':
						// force a reload of the thumbnail ??
						thumbnail = $('#media-item-' + postid);
						if ( thumbnail.length == 0 ) {
							// when the flash uploader is employed media items are named 'media-item-SWFUpload_n_n' with n >= 0
							// we therefore try to locate a known element and navigate up to the image-item-info div object
							thumbnail = $('#media-dims-' + postid).closest('.media-item-info');
						}
						thumbnail = thumbnail.find('.thumbnail');
						thumbnail.attr('src', pair[1]);
						break;
					case 'error':
						$('#imgedit-panel-' + postid).html(pair[1]);
					}
				}
			}
			t.toggleEditor(postid, 0);
		});
	},

	open : function(postid, nonce) {
		var t = this, data, elem = $('#image-editor-' + postid), head = $('#media-head-' + postid),
			btn = $('#imgedit-open-btn-' + postid), spin = btn.siblings('img');

		btn.attr('disabled', 'disabled');
		spin.css('visibility', 'visible');

		data = {
			'action': 'open-image-editor',
			'_ajax_nonce': nonce,
			'postid': postid
		};

		elem.load(ajaxurl, data, function() {

			elem.fadeIn('fast');

			head.fadeOut('fast', function(){
				btn.removeAttr('disabled');
				spin.css('visibility', 'hidden');
			});

			t.toggleEditor(postid, 1);

			$('#image-preview-' + postid).load(function(){
				var t = imageEdit, parent = $('#imgedit-crop-' + postid);

				t.initCrop(postid, this, parent);
				t.setDefaultAspect(postid);
				t.toggleEditor(postid, 0);
			});
		});
	},

	initCrop : function(postid, image, parent) {
		var t = this;

		t.iasapi = $(image).imgAreaSelect({
			parent: parent,
			instance: true,
			handles: true,
			keys: true,
			minHeight: 5,
			minWidth: 5,

			onInit: function(img, c) {
				parent.children().mousedown(function(e){
					var sel, ratio = false, X = t.intval( $('#imgedit-aspect-x-' + postid).val() ),
					Y = t.intval( $('#imgedit-aspect-y-' + postid).val() );

					defRatio = ( X && Y ) ? X + ':' + Y : '1:1';

					if ( e.shiftKey ) {
						sel = t.iasapi.getSelection();
						ratio = ( sel.width && sel.height ) ? sel.width + ':' + sel.height : defRatio;
					}

					t.iasapi.setOptions({
						aspectRatio: ratio
					});
				});
			},

			onSelectEnd: function(img, c) {
				var sel = { 'x': c.x1, 'y': c.y1, 'w': c.width, 'h': c.height };
				$('#imgedit-selection-' + postid).val( JSON.stringify(sel) );
			}
		});
	},

	close : function(postid) {
		$('#image-editor-' + postid).fadeOut('fast', function() {
			$('#media-head-' + postid).fadeIn('fast');
		});
	},

	addStep : function(op, postid, nonce) {
		var t = this, elem = $('#imgedit-history-' + postid),
		history = (elem.val() != '') ? JSON.parse(elem.val()) : new Array(),
		undone = $('#imgedit-undone-' + postid),
		pop = t.intval(undone.val());

		while ( pop > 0 ) {
			history.pop();
			pop--;
		}
		undone.val(0); // reset

		history.push(op);
		elem.val( JSON.stringify(history) );

		t.refreshEditor(postid, nonce, function() {
			t.setClass($('#image-undo-' + postid), true);
			t.setClass($('#image-redo-' + postid), false);
		});
	},

	rotate : function(angle, postid, nonce) {
		this.addStep({ 'r': angle }, postid, nonce);
	},

	flip : function (axis, postid, nonce) {
		this.addStep({ 'f': axis }, postid, nonce);
	},

	crop : function (postid, nonce) {
		var sel = $('#imgedit-selection-' + postid).val();

		if ( sel != '' ) {
			sel = JSON.parse(sel);
			if ( sel.w > 0 && sel.h > 0 )
				this.addStep({ 'c': sel }, postid, nonce);
		}
	},

	undo : function (postid, nonce) {
		var t = this, button = $('#image-undo-' + postid), elem = $('#imgedit-undone-' + postid),
			pop = t.intval( elem.val() ) + 1;

		if ( button.hasClass('disabled') )
			return;

		elem.val(pop);
		t.refreshEditor(postid, nonce, function() {
			var elem = $('#imgedit-history-' + postid),
			history = (elem.val() != '') ? JSON.parse(elem.val()) : new Array();

			t.setClass($('#image-redo-' + postid), true);
			t.setClass(button, pop < history.length);
		});
	},

	redo : function(postid, nonce) {
		var t = this, button = $('#image-redo-' + postid), elem = $('#imgedit-undone-' + postid),
			pop = t.intval( elem.val() ) - 1;

		if ( button.hasClass('disabled') )
			return;

		elem.val(pop);
		t.refreshEditor(postid, nonce, function() {
			t.setClass($('#image-undo-' + postid), true);
			t.setClass(button, pop > 0);
		});
	},

	scaleSwitched : function(postid) {
		var enable = this.isChecked( $('#imgedit-scale-switch-' + postid) );

		this.setState($('#imgedit-scale-width-' + postid), enable);
		this.setState($('#imgedit-scale-height-' + postid), enable);
	//	this.setClass($('#imgedit-scale-values-' + postid), !enable);
		this.scaleWidthChanged(postid);
	},

	targetChanged : function(postid) {
		var target = $('#imgedit-save-target-' + postid).val(),
			enable = (target == 'full' || target == 'all');

		this.setState($('#imgedit-scale-switch-' + postid), enable);
		this.setClass($('#imgedit-scale-' + postid), !enable);
		this.scaleSwitched(postid);
	}
}
})(jQuery);
