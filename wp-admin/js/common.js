
wpCookies = {
// The following functions are from Cookie.js class in TinyMCE, Moxiecode, used under LGPL.

	each : function(o, cb, s) {
		var n, l;

		if (!o)
			return 0;

		s = s || o;

		if (typeof(o.length) != 'undefined') {
			for (n=0, l = o.length; n<l; n++) {
				if (cb.call(s, o[n], n, o) === false)
					return 0;
			}
		} else {
			for (n in o) {
				if (o.hasOwnProperty(n)) {
					if (cb.call(s, o[n], n, o) === false)
						return 0;
				}
			}
		}
		return 1;
	},

	getHash : function(n) {
		var v = this.get(n), h;

		if (v) {
			this.each(v.split('&'), function(v) {
				v = v.split('=');
				h = h || {};
				h[v[0]] = v[1];
			});
		}
		return h;
	},

	setHash : function(n, v, e, p, d, s) {
		var o = '';

		this.each(v, function(v, k) {
			o += (!o ? '' : '&') + k + '=' + v;
		});

		this.set(n, o, e, p, d, s);
	},

	get : function(n) {
		var c = document.cookie, e, p = n + "=", b;

		if (!c)
			return;

		b = c.indexOf("; " + p);

		if (b == -1) {
			b = c.indexOf(p);

			if (b != 0)
				return null;
		} else
			b += 2;

		e = c.indexOf(";", b);

		if (e == -1)
			e = c.length;

		return decodeURIComponent(c.substring(b + p.length, e));
	},

	set : function(n, v, e, p, d, s) {
		document.cookie = n + "=" + encodeURIComponent(v) +
			((e) ? "; expires=" + e.toGMTString() : "") +
			((p) ? "; path=" + p : "") +
			((d) ? "; domain=" + d : "") +
			((s) ? "; secure" : "");
	},

	remove : function(n, p) {
		var d = new Date();

		d.setTime(d.getTime() - 1000);

		this.set(n, '', d, p, d);
	}
};

// Returns the value as string. Second arg or empty string is returned when value is not set.
function getUserSetting( name, def ) {
	var o = getAllUserSettings();

	if ( o.hasOwnProperty(name) )
		return o[name];

	if ( typeof def != 'undefined' )
		return def;

	return '';
}

// Both name and value must be only ASCII letters, numbers or underscore
// and the shorter, the better (cookies can store maximum 4KB). Not suitable to store text.
function setUserSetting( name, value, del ) {
	var c = 'wp-settings-'+userSettings.uid, o = wpCookies.getHash(c) || {}, d = new Date();
	var n = name.toString().replace(/[^A-Za-z0-9_]/, ''), v = value.toString().replace(/[^A-Za-z0-9_]/, '');

	if ( del ) delete o[n];
	else o[n] = v;

	d.setTime( d.getTime() + 31536000000 );
	p = userSettings.url;

	wpCookies.setHash(c, o, d, p );
	wpCookies.set('wp-settings-time-'+userSettings.uid, userSettings.time, d, p );
}

function deleteUserSetting( name ) {
	setUserSetting( name, '', 1 );
}

// Returns all settings as js object.
function getAllUserSettings() {
	return wpCookies.getHash('wp-settings-'+userSettings.uid) || {};
}


jQuery(document).ready( function($) {
	// pulse
	$('.fade').animate( { backgroundColor: '#ffffe0' }, 300).animate( { backgroundColor: '#fffbcc' }, 300).animate( { backgroundColor: '#ffffe0' }, 300).animate( { backgroundColor: '#fffbcc' }, 300);

	// show things that should be visible, hide what should be hidden
	$('.hide-if-no-js').removeClass('hide-if-no-js');
	$('.hide-if-js').hide();

	// Basic form validation
	if ( ( 'undefined' != typeof wpAjax ) && $.isFunction( wpAjax.validateForm ) ) {
		$('form.validate').submit( function() { return wpAjax.validateForm( $(this) ); } );
	}

	// Move .updated and .error alert boxes
	$('div.wrap h2 ~ div.updated, div.wrap h2 ~ div.error').addClass('below-h2');
	$('div.updated, div.error').not('.below-h2').insertAfter('div.wrap h2:first');

	// screen settings tab
	$('#show-settings-link').click(function () {
		if ( ! $('#screen-options-wrap').hasClass('screen-options-open') ) {
			$('#contextual-help-link-wrap').addClass('invisible');
		}
		$('#screen-options-wrap').slideToggle('fast', function(){
			if ( $(this).hasClass('screen-options-open') ) {
				$('#show-settings-link').css({'backgroundImage':'url("images/screen-options-right.gif")'});
				$('#contextual-help-link-wrap').removeClass('invisible');
				$(this).removeClass('screen-options-open');

			} else {
				$('#show-settings-link').css({'backgroundImage':'url("images/screen-options-right-up.gif")'});
				$(this).addClass('screen-options-open');
			}
		});
		return false;
	});

	// help tab
	$('#contextual-help-link').click(function () {
		if ( ! $('#contextual-help-wrap').hasClass('contextual-help-open') ) {
			$('#screen-options-link-wrap').addClass('invisible');
		}
		$('#contextual-help-wrap').slideToggle('fast', function(){
			if ( $(this).hasClass('contextual-help-open') ) {
				$('#contextual-help-link').css({'backgroundImage':'url("images/screen-options-right.gif")'});
				$('#screen-options-link-wrap').removeClass('invisible');
				$(this).removeClass('contextual-help-open');
			} else {
				$('#contextual-help-link').css({'backgroundImage':'url("images/screen-options-right-up.gif")'});
				$(this).addClass('contextual-help-open');
			}
		});
		return false;
	});

	// check all checkboxes
	var lastClicked = false;
	$( 'table:visible tbody .check-column :checkbox' ).click( function(e) {
		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			var checks = $( lastClicked ).parents( 'form:first' ).find( ':checkbox' );
			var first = checks.index( lastClicked );
			var last = checks.index( this );
			var checked = $(this).attr('checked');
			if ( 0 < first && 0 < last && first != last ) {
				checks.slice( first, last ).attr( 'checked', function(){
					if ( $(this).parents('tr').is(':visible') )
						return checked ? 'checked' : '';

					return '';
				});
			}
		}
		lastClicked = this;
		return true;
	} );
	$( 'thead :checkbox, tfoot :checkbox' ).click( function(e) {
		var c = $(this).attr('checked');
		if ( 'undefined' == typeof  toggleWithKeyboard)
			toggleWithKeyboard = false;
		var toggle = e.shiftKey || toggleWithKeyboard;
		$(this).parents( 'form:first' ).find( 'table tbody:visible').find( '.check-column :checkbox' ).attr( 'checked', function() {
			if ( $(this).parents('tr').is(':hidden') )
				return '';
			if ( toggle )
				return $(this).attr( 'checked' ) ? '' : 'checked';
			else if (c)
				return 'checked';
			return '';
		});
		$(this).parents( 'form:first' ).find( 'table thead:visible, table tfoot:visible').find( '.check-column :checkbox' ).attr( 'checked', function() {
			if ( toggle )
				return '';
			else if (c)
				return 'checked';
			return '';
		});
	});
});

var showNotice, adminMenu, columns;

// stub for doing better warnings
showNotice = {
	warn : function(text) {
		if ( confirm(text) )
			return true;

		return false;
	},

	note : function(text) {
		alert(text);
	}
};

(function($){
// sidebar admin menu
adminMenu = {

	init : function() {
		$('#adminmenu div.wp-menu-toggle').each( function() {
			if ( $(this).siblings('.wp-submenu').length )
				$(this).click(function(){ adminMenu.toggle( $(this).siblings('.wp-submenu') ); });
			else
				$(this).hide();
		});
		$('#adminmenu li.menu-top .wp-menu-image').click( function() { window.location = $(this).siblings('a.menu-top')[0].href; } );
		this.favorites();

		$('.wp-menu-separator').click(function(){
			if ( $('#wpcontent').hasClass('folded') ) {
				adminMenu.fold(1);
				setUserSetting( 'mfold', 'o' );
			} else {
				adminMenu.fold();
				setUserSetting( 'mfold', 'f' );
			}
		});

		if ( 'f' != getUserSetting( 'mfold' ) ) {
			this.restoreMenuState();
		} else {
			this.fold();
		}
	},

	restoreMenuState : function() {
		$('#adminmenu li.wp-has-submenu').each(function(i, e) {
			var v = getUserSetting( 'm'+i );
			if ( $(e).hasClass('wp-has-current-submenu') ) return true; // leave the current parent open

			if ( 'o' == v ) $(e).addClass('wp-menu-open');
			else if ( 'c' == v ) $(e).removeClass('wp-menu-open');
		});
	},

	toggle : function(el) {

		el['slideToggle'](150, function(){el.css('display','');}).parent().toggleClass( 'wp-menu-open' );

		$('#adminmenu li.wp-has-submenu').each(function(i, e) {
			var v = $(e).hasClass('wp-menu-open') ? 'o' : 'c';
			setUserSetting( 'm'+i, v );
		});

		return false;
	},

	fold : function(off) {
		if (off) {
			$('#wpcontent').removeClass('folded');
			$('#adminmenu li.wp-has-submenu').unbind();
		} else {
			$('#wpcontent').addClass('folded');
			$('#adminmenu li.wp-has-submenu').hoverIntent({
				over: function(e){
					var m = $(this).find('.wp-submenu'), t = e.clientY, H = $(window).height(), h = m.height(), o;

					if ( (t+h+10) > H ) {
						o = (t+h+10) - H;
						m.css({'marginTop':'-'+o+'px'});
					} else if ( m.css('marginTop') ) {
						m.css({'marginTop':''})
					}
					m.addClass('sub-open');
				},
				out: function(){ $(this).find('.wp-submenu').removeClass('sub-open').css({'marginTop':''}); },
				timeout: 220,
				sensitivity: 8,
				interval: 100
			});

		}
	},

	favorites : function() {
		$('#favorite-inside').width($('#favorite-actions').width()-4);
		$('#favorite-toggle, #favorite-inside').bind( 'mouseenter', function(){$('#favorite-inside').removeClass('slideUp').addClass('slideDown'); setTimeout(function(){if ( $('#favorite-inside').hasClass('slideDown') ) { $('#favorite-inside').slideDown(100); $('#favorite-first').addClass('slide-down'); }}, 200) } );

		$('#favorite-toggle, #favorite-inside').bind( 'mouseleave', function(){$('#favorite-inside').removeClass('slideDown').addClass('slideUp'); setTimeout(function(){if ( $('#favorite-inside').hasClass('slideUp') ) { $('#favorite-inside').slideUp(100, function(){ $('#favorite-first').removeClass('slide-down'); } ); }}, 300) } );
	}
};

$(document).ready(function(){adminMenu.init();});
})(jQuery);

(function($){
// show/hide/save table columns
columns = {
	init : function(page) {
		$('.hide-column-tog').click( function() {
			var column = $(this).val();
			var show = $(this).attr('checked');
			if ( show ) {
				$('.column-' + column).show();
			} else {
				$('.column-' + column).hide();
			}
			columns.save_manage_columns_state(page);
		} );
	},

	save_manage_columns_state : function(page) {
		var hidden = $('.manage-column').filter(':hidden').map(function() { return this.id; }).get().join(',');
		$.post('admin-ajax.php', {
			action: 'hidden-columns',
			hidden: hidden,
			hiddencolumnsnonce: $('#hiddencolumnsnonce').val(),
			page: page
		});
	}
}

})(jQuery);


jQuery(document).ready(function($){
	if ( 'undefined' != typeof google && google.gears ) return;

	var gf = false;
	if ( 'undefined' != typeof GearsFactory ) {
		gf = new GearsFactory();
	} else {
		try {
			gf = new ActiveXObject('Gears.Factory');
			if ( factory.getBuildInfo().indexOf('ie_mobile') != -1 )
				gf.privateSetGlobalObject(this);
		} catch (e) {
			if ( ( 'undefined' != typeof navigator.mimeTypes ) && navigator.mimeTypes['application/x-googlegears'] ) {
				gf = document.createElement("object");
				gf.style.display = "none";
				gf.width = 0;
				gf.height = 0;
				gf.type = "application/x-googlegears";
				document.documentElement.appendChild(gf);
			}
		}
	}
	if ( gf && gf.hasPermission )
		return;

	$('.turbo-nag').show();
});
