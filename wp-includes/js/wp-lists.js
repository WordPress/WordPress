(function($) {
var currentFormEl = false;
var fs = {add:'ajaxAdd',del:'ajaxDel',dim:'ajaxDim',process:'process',recolor:'recolor'};

wpAjax = {
	unserialize: function( s ) {
		var r = {}; if ( !s ) { return r; }
		var q = s.split('?'); if ( q[1] ) { s = q[1]; }
		var pp = s.split('&');
		for ( var i in pp ) {
			var p = pp[i].split('=');
			r[p[0]] = p[1];
		}
		return r;
	},
	parseAjaxResponse: function( x, r ) { // 1 = good, 0 = strange (bad data?), -1 = you lack permission
		var re = $('#' + r).html('');
		if ( x && typeof x == 'object' && x.getElementsByTagName('wp_ajax') ) {
			if ( $('wp_error', x).each( function() { re.append('<p>' + this.firstChild.nodeValue + '</p>'); } ).size() ) {
				return !re.wrap( '<div class="error"></div>' );
			}
			return true;
		}
		if ( isNaN(x) ) { return !re.html('<div class="error"><p>' + x + '</p></div>'); }
		x = parseInt(x,10);
		if ( -1 == x ) { return !re.html('<div class="error"><p>You do not have permission to do that.</p></div>'); }
		else if ( 0 === x ) { return !re.html('<div class="error"><p>AJAX is teh b0rked.</p></div>'); }
		return true;
	}
};

var wpList = {
	settings: {
		url: wpListL10n.url, type: 'POST',
		response: 'ajax-response',

		what: '',
		alt: 'alternate', altOffset: 0,
		addColor: null, delColor: null, dimAddColor: null, dimDelColor: null,

		confirm: null,
		addBefore: null, addAfter: null,
		delBefore: null, delAfter: null,
		dimBefore: null, dimAfter: null
	},

	nonce: function(e,s) {
		var url = wpAjax.unserialize(e.attr('href'));
		return s.nonce || url._ajax_nonce || $('#' + s.element + ' input[@name=_ajax_nonce]').val() || url._wpnonce || $('#' + s.element + ' input[@name=_wpnonce]').val() || 0;
	},

	parseClass: function(e,t) {
		var c = []; try { c = $(e).attr('class').match(new RegExp(t+':[A-Za-z0-9:_=-]+'))[0].split(':'); } catch(r) {}
		return c;
	},

	pre: function(e,s,a) {
		var bg; var r;
		s = $.extend( {}, this.wpList.settings, {
			element: null,
			nonce: 0
		}, s || {} );
		if ( $.isFunction( s.confirm ) ) {
			if ( 'add' != a ) {
				bg = $('#' + s.element).css('background-color');
				$('#' + s.element).css('background-color', '#FF9966');
			}
			r = s.confirm.call(this,e,s,a,bg);
			if ( 'add' != a ) { $('#' + s.element).css('background-color', bg ); }
			if ( !r ) { return false; }
		}
		return s;
	},

	ajaxAdd: function( e, s ) {
		var list = this; e = $(e); s = s || {};
		var cls = wpList.parseClass(e,'add');
		s = $.extend(s, {
			element: s.element || cls[2] || e.attr( 'id' ) || null,
			addColor: s.addColor || '#' + ( cls[3] || 'FFFF33' )
		} );
		s = wpList.pre.call( list, e, s, 'add' );
		if ( !s ) { return false; }

		if ( !e.is("[@class^=add:" + list.id + ":]") ) { return !wpList.add.call( list, e, s ); }

		if ( !s.element ) { return true; }

		s.action = 'add-' + s.what;

		s.nonce = wpList.nonce(e,s);

		var es = $('#' + s.element + ' :input').not('[@name=_ajax_nonce], [@name=_wpnonce], [@name=action]');
		s.data = $.param( $.extend( { _ajax_nonce: s.nonce, action: s.action }, wpAjax.unserialize( cls[4] || '' ) ) );
		var formData = $.isFunction(es.fieldSerialize) ? es.fieldSerialize() : es.serialize();
		if ( formData ) { s.data += '&' + formData; }

		if ( $.isFunction(s.addBefore) ) {
			s = s.addBefore( s );
			if ( !s ) { return true; }
		}
		if ( !s.data.match(/_ajax_nonce=[a-f0-9]+/) ) { return true; }

		s.success = function(r) {
			if ( !wpAjax.parseAjaxResponse(r, s.response) ) { return false; }

			$(s.what + ' response_data', r).each( function() {
				var t = $(this);
				wpList.add.call( list, t.text(), $.extend( {}, s, { // this.firstChild.nodevalue
					pos: t.parent().attr( 'position' ) || 0,
					id: t.parent().attr( 'id' ) || 0,
					oldId: t.parent().attr( 'old_id' ) || null
				} ) );
			} );

			if ( $.isFunction(s.addAfter) ) {
				var o = this.complete;
				this.complete = function(x,st) {
					var _s = $.extend( { xml: x, status: st }, s );
					s.addAfter( r, _s );
					if ( $.isFunction(o) ) { o(x,st); }
				};
			}
			list.wpList.recolor();
			wpList.clear.call(list,'#' + s.element);
		};

		$.ajax( s );
		return false;
	},

	ajaxDel: function( e, s ) {
		var list = this; e = $(e); s = s || {};
		var cls = wpList.parseClass(e,'delete');
		s = $.extend(s, {
			element: s.element || cls[2] || null,
			delColor: s.delColor || '#' + ( cls[3] || 'FF3333' )
		} );
		s = wpList.pre.call( list, e, s, 'delete' );
		if ( !s || !s.element ) { return false; }

		s.action = 'delete-' + s.what;

		s.nonce = wpList.nonce(e,s);

		s.data = $.extend(
			{ action: s.action, id: s.element.split('-').pop(), _ajax_nonce: s.nonce },
			wpAjax.unserialize( cls[4] || '' )
		);

		if ( $.isFunction(s.delBefore) ) {
			s = s.delBefore( s );
			if ( !s ) { return true; }
		}
		if ( !s.data._ajax_nonce ) { return true; }

		var func = function() { $('#' + s.element).css( 'background-color', '' ).hide(); list.wpList.recolor(); };
		var hideTO = -1;
		if ( 'none' != s.delColor ) {
			Fat.fade_element(s.element,null,700,s.delColor);
			hideTO = setTimeout(func, 705);
		} else {
			func();
		}

		s.success = function(r) {
			if ( !wpAjax.parseAjaxResponse(r, s.response) ) {
				clearTimeout(hideTO);
				func = function() { $('#' + s.element).css( 'background-color', '#FF3333' ).show(); list.wpList.recolor(); };
				func(); setTimeout(func, 705); // In case it's still fading
				return false;
			}
			if ( $.isFunction(s.delAfter) ) {
				var o = this.complete;
				this.complete = function(x,st) {
					var _s = $.extend( { xml: x, status: st }, s );
					s.delAfter( r, _s );
					if ( $.isFunction(o) ) { o(x,st); }
				};
			}
		};
		$.ajax( s );
		return false;
	},

	ajaxDim: function( e, s ) {
		var list = this; e = $(e); s = s || {};
		var cls = wpList.parseClass(e,'dim');
		s = $.extend(s, {
			element: s.element || cls[2] || null,
			dimClass: s.dimClass || cls[3] || null,
			dimAddColor: s.dimAddColor || '#' + ( cls[4] || 'FFFF33' ),
			dimDelColor: s.dimDelColor || '#' + ( cls[5] || 'FF3333' )
		} );
		s = wpList.pre.call( list, e, s, 'dim' );
		if ( !s || !s.element || !s.dimClass ) { return true; }

		s.action = 'dim-' + s.what;

		s.nonce = wpList.nonce(e,s);

		s.data = $.extend(
			{ action: s.action, id: s.element.split('-').pop(), dimClass: s.dimClass, _ajax_nonce : s.nonce },
			wpAjax.unserialize( cls[6] || '' )
		);

		if ( $.isFunction(s.dimBefore) ) {
			s = s.dimBefore( s );
			if ( !s ) { return true; }
		}

		if ( !s.data._ajax_nonce ) { return true; }

		var isClass = $('#' + s.element).toggleClass(s.dimClass).is('.' + s.dimClass);
		if ( isClass && 'none' != s.dimAddColor ) { Fat.fade_element(s.element,null,700,s.dimAddColor); }
		else if ( !isClass && 'none' != s.dimDelColor ) { Fat.fade_element(s.element,null,700,s.dimDelColor); }

		var dimTO = setTimeout( function() { $('#' + s.element).css( 'background-color', '' ); }, 705 );

		s.success = function(r) {
			if ( !wpAjax.parseAjaxResponse(r, s.response) ) {
				clearTimeout(dimTO);
				func = function() { $('#' + s.element).css( 'background-color', '#FF3333' )[isClass?'removeClass':'addClass'](s.dimClass); };
				func(); setTimeout(func, 705);
				return false;
			}
			if ( $.isFunction(s.dimAfter) ) {
				var o = this.complete;
				this.complete = function(x,st) {
					var _s = $.extend( { xml: x, status: st }, s );
					s.dimAfter( r, _s );
					if ( $.isFunction(o) ) { o(x,st); }
				};
			}
		};

		$.ajax( s );
		return false;
	},

	add: function( e, s ) {
		list = $(this);
		e = $(e);

		var old = false; var next = false;
		var _s = { pos: 0, id: 0, oldId: null };
		if ( 'string' == typeof s ) { s = { what: s }; }
		s = $.extend(_s, this.wpList.settings, s);

		if ( !e.size() || !s.what ) { return false; }
		if ( s.oldId ) {
			old = $('#' + s.what + '-' + s.oldId);
			next = old.next();
			old.remove();
		}
		if ( s.id ) { $('#' + s.what + '-' + s.id).remove(); }

		if ( old && old.size() ) {
			if ( next && next.size() ) {
				next.before(e);
			} else {
				list.append(e);
			}
		} else if ( isNaN(s.pos) ) {
			var ba = 'after';
			if ( '-' == s.pos.substr(0,1) ) {
				s.pos = s.pos.substr(1);
				ba = 'before';
			}
			var ref = list.find( '#' + s.pos );
			if ( 1 === ref.size() ) { ref[ba](e); }
			else { list.append(e); }
		} else if ( s.pos < 0 ) {
			list.prepend(e);
		} else {
			list.append(e);
		}

		if ( s.alt ) {
			if ( ( list.children(':visible').index( e[0] ) + s.altOffset ) % 2 ) { e.removeClass( s.alt ); }
			else { e.addClass( s.alt ); }
		}

		if ( 'none' != s.addColor ) {
			Fat.fade_element(e.attr('id'),null,700,s.addColor);
			setTimeout( function() {
				var b = e.css( 'background-color' );
				var g = e.css( 'background-color', '' ).css( 'background-color' );
				if ( b != g ) { e.css( 'background-color', b ); }
			}, 705 );
		}
		list.each( function() { this.wpList.process( e ); } );
		return e;
	},

	clear: function(e) {
		var list = this;
		e = $(e);
		if ( list.wpList && e.parents( '#' + list.id ).size() ) { return; }
		e.find(':input').each( function() {
			var t = this.type.toLowerCase(); var tag = this.tagName.toLowerCase();
			if ( 'text' == t || 'password' == t || 'textarea' == tag ) { this.value = ''; }
			else if ( 'checkbox' == t || 'radio' == t ) { this.checked = false; }
			else if ( 'select' == tag ) { this.selectedIndex = null; }
		});
	},

	process: function(el) {
		var list = this;
		var bl = function() { currentFormEl = false; };
		var fo = function() { currentFormEl = this; };
		var a = $("[@class^=add:" + list.id + ":]", el || null)
			.filter('form').submit( function() { return list.wpList.add(this); } ).end()
			.not('form').click( function() { return list.wpList.add(this); } ).each( function() {
				var addEl = this;
				var c = wpList.parseClass(this,'add')[2] || addEl.id;
				if ( !c ) { return; }
				var forms = []; var ins = [];
				$('#' + c + ' :input').click( function() { $(this).unbind( 'blur', bl ).unbind( 'focus', fo ).blur( bl ).focus( fo ).focus(); } ).each( function() {
					ins.push(this);
					$.merge(forms,$(this).parents('form'));
					forms = $.unique(forms);
				} );
				$(forms).submit( function() {
					var e = currentFormEl;
					if ( 0 <= $.inArray(e,ins) ) {
						$(addEl).trigger( 'click' );
						$(e).focus();
						return false;
					}
				} );
			} );
		var d = $("[@class^=delete:" + list.id + ":]", el || null).click( function() { return list.wpList.del(this); } );
		var c = $("[@class^=dim:" + list.id + ":]", el || null).click( function() { return list.wpList.dim(this); } );
	},

	recolor: function() {
		var list = this;
		if ( !list.wpList.settings.alt ) { return; }
		var items = $('.list-item:visible', list);
		if ( !items.size() ) { items = $(list).children(':visible'); }
		var eo = [':even',':odd'];
		if ( list.wpList.settings.altOffset % 2 ) { eo.reverse(); }
		items.filter(eo[0]).addClass(list.wpList.settings.alt).end().filter(eo[1]).removeClass(list.wpList.settings.alt);
	},

	init: function() {
		var lists = this;
		lists.wpList.process = function(a) {
			lists.each( function() {
				this.wpList.process(a);
			} );
		};
		lists.wpList.recolor = function() {
			lists.each( function() {
				this.wpList.recolor();
			} );
		};
	}
};

$.fn.wpList = function( settings ) {
	this.each( function() {
		var _this = this;
		this.wpList = { settings: $.extend( {}, wpList.settings, { what: wpList.parseClass(this,'list')[1] || '' }, settings ) };
		$.each( fs, function(i,f) { _this.wpList[i] = function( e, s ) { return wpList[f].call( _this, e, s ); }; } );
	} );
	wpList.init.call(this);
	this.wpList.process();
	return this;
};

})(jQuery);
