var imageEdit;

(function($) {
imageEdit = {
	iasapi : {},
	hold : {},
	postid : '',

	intval : function(f) {
		return f | 0;
	},

	setDisabled : function(el, s) {
		if ( s ) {
			el.removeClass('disabled');
			$('input', el).removeAttr('disabled');
		} else {
			el.addClass('disabled');
			$('input', el).prop('disabled', true);
		}
	},

	init : function(postid, nonce) {
		var t = this, old = $('#image-editor-' + t.postid),
			x = t.intval( $('#imgedit-x-' + postid).val() ),
			y = t.intval( $('#imgedit-y-' + postid).val() );

		if ( t.postid != postid && old.length )
			t.close(t.postid);

		t.hold['w'] = t.hold['ow'] = x;
		t.hold['h'] = t.hold['oh'] = y;
		t.hold['xy_ratio'] = x / y;
		t.hold['sizer'] = parseFloat( $('#imgedit-sizer-' + postid).val() );
		t.postid = postid;
		$('#imgedit-response-' + postid).empty();

		$('input[type="text"]', '#imgedit-panel-' + postid).keypress(function(e) {
			var k = e.keyCode;

			if ( 36 < k && k < 41 )
				$(this).blur()

			if ( 13 == k ) {
				e.preventDefault();
				e.stopPropagation();
				return false;
			}
		});
	},

	toggleEditor : function(postid, toggle) {
		var wait = $('#imgedit-wait-' + postid);

		if ( toggle )
			wait.height( $('#imgedit-panel-' + postid).height() ).fadeIn('fast');
		else
			wait.fadeOut('fast');
	},

	toggleHelp : function(el) {
		$(el).siblings('.imgedit-help').slideToggle('fast');
		return false;
	},

	getTarget : function(postid) {
		return $('input[name="imgedit-target-' + postid + '"]:checked', '#imgedit-save-target-' + postid).val() || 'full';
	},

	scaleChanged : function(postid, x) {
		var w = $('#imgedit-scale-width-' + postid), h = $('#imgedit-scale-height-' + postid),
		warn = $('#imgedit-scale-warn-' + postid), w1 = '', h1 = '';

		if ( x ) {
			h1 = (w.val() != '') ? Math.round( w.val() / this.hold['xy_ratio'] ) : '';
			h.val( h1 );
		} else {
			w1 = (h.val() != '') ? Math.round( h.val() * this.hold['xy_ratio'] ) : '';
			w.val( w1 );
		}

		if ( ( h1 && h1 > this.hold['oh'] ) || ( w1 && w1 > this.hold['ow'] ) )
			warn.css('visibility', 'visible');
		else
			warn.css('visibility', 'hidden');
	},

	getSelRatio : function(postid) {
		var x = this.hold['w'], y = this.hold['h'],
			X = this.intval( $('#imgedit-crop-width-' + postid).val() ),
			Y = this.intval( $('#imgedit-crop-height-' + postid).val() );

		if ( X && Y )
			return X + ':' + Y;

		if ( x && y )
			return x + ':' + y;

		return '1:1';
	},

	filterHistory : function(postid, setSize) {
		// apply undo state to history
		var history = $('#imgedit-history-' + postid).val(), pop, n, o, i, op = [];

		if ( history != '' ) {
			history = JSON.parse(history);
			pop = this.intval( $('#imgedit-undone-' + postid).val() );
			if ( pop > 0 ) {
				while ( pop > 0 ) {
					history.pop();
					pop--;
				}
			}

			if ( setSize ) {
				if ( !history.length ) {
					this.hold['w'] = this.hold['ow'];
					this.hold['h'] = this.hold['oh'];
					return '';
				}

				// restore
				o = history[history.length - 1];
				o = o.c || o.r || o.f || false;

				if ( o ) {
					this.hold['w'] = o.fw;
					this.hold['h'] = o.fh;
				}
			}

			// filter the values
			for ( n in history ) {
				i = history[n];
				if ( i.hasOwnProperty('c') ) {
					op[n] = { 'c': { 'x': i.c.x, 'y': i.c.y, 'w': i.c.w, 'h': i.c.h } };
				} else if ( i.hasOwnProperty('r') ) {
					op[n] = { 'r': i.r.r };
				} else if ( i.hasOwnProperty('f') ) {
					op[n] = { 'f': i.f.f };
				}
			}
			return JSON.stringify(op);
		}
		return '';
	},

	refreshEditor : function(postid, nonce, callback) {
		var t = this, data, img;

		t.toggleEditor(postid, 1);
		data = {
			'action': 'imgedit-preview',
			'_ajax_nonce': nonce,
			'postid': postid,
			'history': t.filterHistory(postid, 1),
			'rand': t.intval(Math.random() * 1000000)
		};

		img = $('<img id="image-preview-' + postid + '" />');
		img.load( function() {
			var max1, max2, parent = $('#imgedit-crop-' + postid), t = imageEdit;

			parent.empty().append(img);

			// w, h are the new full size dims
			max1 = Math.max( t.hold.w, t.hold.h );
			max2 = Math.max( $(img).width(), $(img).height() );
			t.hold['sizer'] = max1 > max2 ? max2 / max1 : 1;

			t.initCrop(postid, img, parent);
			t.setCropSelection(postid, 0);

			if ( (typeof callback != "unknown") && callback != null )
				callback();

			if ( $('#imgedit-history-' + postid).val() && $('#imgedit-undone-' + postid).val() == 0 )
				$('input.imgedit-submit-btn', '#imgedit-panel-' + postid).removeAttr('disabled');
			else
				$('input.imgedit-submit-btn', '#imgedit-panel-' + postid).prop('disabled', true);

			t.toggleEditor(postid, 0);
		}).error(function(){
			$('#imgedit-crop-' + postid).empty().append('<div class="error"><p>' + imageEditL10n.error + '</p></div>');
			t.toggleEditor(postid, 0);
		}).attr('src', ajaxurl + '?' + $.param(data));
	},

	action : function(postid, nonce, action) {
		var t = this, data, w, h, fw, fh;

		if ( t.notsaved(postid) )
			return false;

		data = {
			'action': 'image-editor',
			'_ajax_nonce': nonce,
			'postid': postid
		};

		if ( 'scale' == action ) {
			w = $('#imgedit-scale-width-' + postid),
			h = $('#imgedit-scale-height-' + postid),
			fw = t.intval(w.val()),
			fh = t.intval(h.val());

			if ( fw < 1 ) {
				w.focus();
				return false;
			} else if ( fh < 1 ) {
				h.focus();
				return false;
			}

			if ( fw == t.hold.ow || fh == t.hold.oh )
				return false;

			data['do'] = 'scale';
			data['fwidth'] = fw;
			data['fheight'] = fh;
		} else if ( 'restore' == action ) {
			data['do'] = 'restore';
		} else {
			return false;
		}

		t.toggleEditor(postid, 1);
		$.post(ajaxurl, data, function(r) {
			$('#image-editor-' + postid).empty().append(r);
			t.toggleEditor(postid, 0);
		});
	},

	save : function(postid, nonce) {
		var data, target = this.getTarget(postid), history = this.filterHistory(postid, 0);

		if ( '' == history )
			return false;

		this.toggleEditor(postid, 1);
		data = {
			'action': 'image-editor',
			'_ajax_nonce': nonce,
			'postid': postid,
			'history': history,
			'target': target,
			'context': $('#image-edit-context').length ? $('#image-edit-context').val() : null,
			'do': 'save'
		};

		$.post(ajaxurl, data, function(r) {
			var ret = JSON.parse(r);

			if ( ret.error ) {
				$('#imgedit-response-' + postid).html('<div class="error"><p>' + ret.error + '</p><div>');
				imageEdit.close(postid);
				return;
			}

			if ( ret.fw && ret.fh )
				$('#media-dims-' + postid).html( ret.fw + ' &times; ' + ret.fh );

			if ( ret.thumbnail )
				$('.thumbnail', '#thumbnail-head-' + postid).attr('src', ''+ret.thumbnail);

			if ( ret.msg )
				$('#imgedit-response-' + postid).html('<div class="updated"><p>' + ret.msg + '</p></div>');

			imageEdit.close(postid);
		});
	},

	open : function(postid, nonce) {
		var data, elem = $('#image-editor-' + postid), head = $('#media-head-' + postid),
			btn = $('#imgedit-open-btn-' + postid), spin = btn.siblings('.spinner');

		btn.prop('disabled', true);
		spin.show();

		data = {
			'action': 'image-editor',
			'_ajax_nonce': nonce,
			'postid': postid,
			'do': 'open'
		};

		elem.load(ajaxurl, data, function() {
			elem.fadeIn('fast');
			head.fadeOut('fast', function(){
				btn.removeAttr('disabled');
				spin.hide();
			});
		});
	},

	imgLoaded : function(postid) {
		var img = $('#image-preview-' + postid), parent = $('#imgedit-crop-' + postid);

		this.initCrop(postid, img, parent);
		this.setCropSelection(postid, 0);
		this.toggleEditor(postid, 0);
	},

	initCrop : function(postid, image, parent) {
		var t = this, selW = $('#imgedit-sel-width-' + postid),
			selH = $('#imgedit-sel-height-' + postid);

		t.iasapi = $(image).imgAreaSelect({
			parent: parent,
			instance: true,
			handles: true,
			keys: true,
			minWidth: 3,
			minHeight: 3,

			onInit: function(img, c) {
				parent.children().mousedown(function(e){
					var ratio = false, sel, defRatio;

					if ( e.shiftKey ) {
						sel = t.iasapi.getSelection();
						defRatio = t.getSelRatio(postid);
						ratio = ( sel && sel.width && sel.height ) ? sel.width + ':' + sel.height : defRatio;
					}

					t.iasapi.setOptions({
						aspectRatio: ratio
					});
				});
			},

			onSelectStart: function(img, c) {
				imageEdit.setDisabled($('#imgedit-crop-sel-' + postid), 1);
			},

			onSelectEnd: function(img, c) {
				imageEdit.setCropSelection(postid, c);
			},

			onSelectChange: function(img, c) {
				var sizer = imageEdit.hold.sizer;
				selW.val( imageEdit.round(c.width / sizer) );
				selH.val( imageEdit.round(c.height / sizer) );
			}
		});
	},

	setCropSelection : function(postid, c) {
		var sel, min = $('#imgedit-minthumb-' + postid).val() || '128:128',
			sizer = this.hold['sizer'];
			min = min.split(':');
			c = c || 0;

		if ( !c || ( c.width < 3 && c.height < 3 ) ) {
			this.setDisabled($('.imgedit-crop', '#imgedit-panel-' + postid), 0);
			this.setDisabled($('#imgedit-crop-sel-' + postid), 0);
			$('#imgedit-sel-width-' + postid).val('');
			$('#imgedit-sel-height-' + postid).val('');
			$('#imgedit-selection-' + postid).val('');
			return false;
		}

		if ( c.width < (min[0] * sizer) && c.height < (min[1] * sizer) ) {
			this.setDisabled($('.imgedit-crop', '#imgedit-panel-' + postid), 0);
			$('#imgedit-selection-' + postid).val('');
			return false;
		}

		sel = { 'x': c.x1, 'y': c.y1, 'w': c.width, 'h': c.height };
		this.setDisabled($('.imgedit-crop', '#imgedit-panel-' + postid), 1);
		$('#imgedit-selection-' + postid).val( JSON.stringify(sel) );
	},

	close : function(postid, warn) {
		warn = warn || false;

		if ( warn && this.notsaved(postid) )
			return false;

		this.iasapi = {};
		this.hold = {};
		$('#image-editor-' + postid).fadeOut('fast', function() {
			$('#media-head-' + postid).fadeIn('fast');
			$(this).empty();
		});
	},

	notsaved : function(postid) {
		var h = $('#imgedit-history-' + postid).val(),
			history = (h != '') ? JSON.parse(h) : new Array(),
			pop = this.intval( $('#imgedit-undone-' + postid).val() );

		if ( pop < history.length ) {
			if ( confirm( $('#imgedit-leaving-' + postid).html() ) )
				return false;
			return true;
		}
		return false;
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
			t.setDisabled($('#image-undo-' + postid), true);
			t.setDisabled($('#image-redo-' + postid), false);
		});
	},

	rotate : function(angle, postid, nonce, t) {
		if ( $(t).hasClass('disabled') )
			return false;

		this.addStep({ 'r': { 'r': angle, 'fw': this.hold['h'], 'fh': this.hold['w'] }}, postid, nonce);
	},

	flip : function (axis, postid, nonce, t) {
		if ( $(t).hasClass('disabled') )
			return false;

		this.addStep({ 'f': { 'f': axis, 'fw': this.hold['w'], 'fh': this.hold['h'] }}, postid, nonce);
	},

	crop : function (postid, nonce, t) {
		var sel = $('#imgedit-selection-' + postid).val(),
			w = this.intval( $('#imgedit-sel-width-' + postid).val() ),
			h = this.intval( $('#imgedit-sel-height-' + postid).val() );

		if ( $(t).hasClass('disabled') || sel == '' )
			return false;

		sel = JSON.parse(sel);
		if ( sel.w > 0 && sel.h > 0 && w > 0 && h > 0 ) {
			sel['fw'] = w;
			sel['fh'] = h;
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

			t.setDisabled($('#image-redo-' + postid), true);
			t.setDisabled(button, pop < history.length);
		});
	},

	redo : function(postid, nonce) {
		var t = this, button = $('#image-redo-' + postid), elem = $('#imgedit-undone-' + postid),
			pop = t.intval( elem.val() ) - 1;

		if ( button.hasClass('disabled') )
			return;

		elem.val(pop);
		t.refreshEditor(postid, nonce, function() {
			t.setDisabled($('#image-undo-' + postid), true);
			t.setDisabled(button, pop > 0);
		});
	},

	setNumSelection : function(postid) {
		var sel, elX = $('#imgedit-sel-width-' + postid), elY = $('#imgedit-sel-height-' + postid),
			x = this.intval( elX.val() ), y = this.intval( elY.val() ),
			img = $('#image-preview-' + postid), imgh = img.height(), imgw = img.width(),
			sizer = this.hold['sizer'], x1, y1, x2, y2, ias = this.iasapi;

		if ( x < 1 ) {
			elX.val('');
			return false;
		}

		if ( y < 1 ) {
			elY.val('');
			return false;
		}

		if ( x && y && ( sel = ias.getSelection() ) ) {
			x2 = sel.x1 + Math.round( x * sizer );
			y2 = sel.y1 + Math.round( y * sizer );
			x1 = sel.x1;
			y1 = sel.y1;

			if ( x2 > imgw ) {
				x1 = 0;
				x2 = imgw;
				elX.val( Math.round( x2 / sizer ) );
			}

			if ( y2 > imgh ) {
				y1 = 0;
				y2 = imgh;
				elY.val( Math.round( y2 / sizer ) );
			}

			ias.setSelection( x1, y1, x2, y2 );
			ias.update();
			this.setCropSelection(postid, ias.getSelection());
		}
	},

	round : function(num) {
		var s;
		num = Math.round(num);

		if ( this.hold.sizer > 0.6 )
			return num;

		s = num.toString().slice(-1);

		if ( '1' == s )
			return num - 1;
		else if ( '9' == s )
			return num + 1;

		return num;
	},

	setRatioSelection : function(postid, n, el) {
		var sel, r, x = this.intval( $('#imgedit-crop-width-' + postid).val() ),
			y = this.intval( $('#imgedit-crop-height-' + postid).val() ),
			h = $('#image-preview-' + postid).height();

		if ( !this.intval( $(el).val() ) ) {
			$(el).val('');
			return;
		}

		if ( x && y ) {
			this.iasapi.setOptions({
				aspectRatio: x + ':' + y
			});

			if ( sel = this.iasapi.getSelection(true) ) {
				r = Math.ceil( sel.y1 + ((sel.x2 - sel.x1) / (x / y)) );

				if ( r > h ) {
					r = h;
					if ( n )
						$('#imgedit-crop-height-' + postid).val('');
					else
						$('#imgedit-crop-width-' + postid).val('');
				}

				this.iasapi.setSelection( sel.x1, sel.y1, sel.x2, r );
				this.iasapi.update();
			}
		}
	}
}
})(jQuery);
