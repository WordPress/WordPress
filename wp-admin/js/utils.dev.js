// utility functions
function convertEntities(o) {
	var c, v;
	c = function(s) {
		if (/&[^;]+;/.test(s)) {
			var e = document.createElement("div");
			e.innerHTML = s;
			return !e.firstChild ? s : e.firstChild.nodeValue;
		}
		return s;
	}

	if ( typeof o === 'string' ) {
		return c(o);
	} else if ( typeof o === 'object' ) {
		for (v in o) {
			if ( typeof o[v] === 'string' ) {
				o[v] = c(o[v]);
			}
		}
	}
	return o;
}

var wpCookies = {
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
					if (cb.call(s, o[n], n, o) === false) {
						return 0;
					}
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

		} else {
			b += 2;
		}

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
	if ( 'object' !== typeof userSettings )
		return false;

	var c = 'wp-settings-' + userSettings.uid, o = wpCookies.getHash(c) || {}, d = new Date(), p,
	n = name.toString().replace(/[^A-Za-z0-9_]/, ''), v = value.toString().replace(/[^A-Za-z0-9_]/, '');

	if ( del ) {
		delete o[n];
	} else {
		o[n] = v;
	}

	d.setTime( d.getTime() + 31536000000 );
	p = userSettings.url;

	wpCookies.setHash(c, o, d, p);
	wpCookies.set('wp-settings-time-'+userSettings.uid, userSettings.time, d, p);

	return name;
}

function deleteUserSetting( name ) {
	return setUserSetting( name, '', 1 );
}

// Returns all settings as js object.
function getAllUserSettings() {
	if ( 'object' !== typeof userSettings )
		return {};

	return wpCookies.getHash('wp-settings-' + userSettings.uid) || {};
}
