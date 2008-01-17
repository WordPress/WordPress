
/* file:jscripts/tiny_mce/classes/tinymce.js */

var tinymce = {
	majorVersion : '3',
	minorVersion : '0rc2',
	releaseDate : '2008-01-xx',

	_init : function() {
		var t = this, ua = navigator.userAgent, i, nl, n, base;

		// Browser checks
		t.isOpera = window.opera && opera.buildNumber;
		t.isWebKit = /WebKit/.test(ua);
		t.isOldWebKit = t.isWebKit && !window.getSelection().getRangeAt;
		t.isIE = !t.isWebKit && !t.isOpera && (/MSIE/gi).test(ua) && (/Explorer/gi).test(navigator.appName);
		t.isIE6 = t.isIE && /MSIE [56]/.test(ua);
		t.isGecko = !t.isWebKit && /Gecko/.test(ua);
//		t.isGecko3 = t.isGecko && /(Firefox|Minefield)\/[3-9]/.test(ua);
		t.isMac = ua.indexOf('Mac') != -1;

		// TinyMCE .NET webcontrol might be setting the values for TinyMCE
		if (window.tinyMCEPreInit) {
			t.suffix = tinyMCEPreInit.suffix;
			t.baseURL = tinyMCEPreInit.base;
			return;
		}

		// Get suffix and base
		t.suffix = '';

		// If base element found, add that infront of baseURL
		nl = document.getElementsByTagName('base');
		for (i=0; i<nl.length; i++) {
			if (nl[i].href)
				base = nl[i].href;
		}

		function getBase(n) {
			if (n.src && /tiny_mce(|_dev|_src|_gzip|_jquery|_prototype).js/.test(n.src)) {
				if (/_(src|dev)\.js/g.test(n.src))
					t.suffix = '_src';

				t.baseURL = n.src.substring(0, n.src.lastIndexOf('/'));

				// If path to script is relative and a base href was found add that one infront
				if (base && t.baseURL.indexOf('://') == -1)
					t.baseURL = base + t.baseURL;

				return t.baseURL;
			}

			return null;
		};

		// Check document
		nl = document.getElementsByTagName('script');
		for (i=0; i<nl.length; i++) {
			if (getBase(nl[i]))
				return;
		}

		// Check head
		n = document.getElementsByTagName('head')[0];
		if (n) {
			nl = n.getElementsByTagName('script');
			for (i=0; i<nl.length; i++) {
				if (getBase(nl[i]))
					return;
			}
		}

		return;
	},

	is : function(o, t) {
		var n = typeof(o);

		if (!t)
			return n != 'undefined';

		if (t == 'array' && (o instanceof Array))
			return true;

		return n == t;
	},

	// #if !jquery

	each : function(o, cb, s) {
		var n, l;

		if (!o)
			return 0;

		s = s || o;

		if (typeof(o.length) != 'undefined') {
			// Indexed arrays, needed for Safari
			for (n=0, l = o.length; n<l; n++) {
				if (cb.call(s, o[n], n, o) === false)
					return 0;
			}
		} else {
			// Hashtables
			for (n in o) {
				if (o.hasOwnProperty(n)) {
					if (cb.call(s, o[n], n, o) === false)
						return 0;
				}
			}
		}

		return 1;
	},

	map : function(a, f) {
		var o = [];

		tinymce.each(a, function(v) {
			o.push(f(v));
		});

		return o;
	},

	grep : function(a, f) {
		var o = [];

		tinymce.each(a, function(v) {
			if (!f || f(v))
				o.push(v);
		});

		return o;
	},

	inArray : function(a, v) {
		var i, l;

		if (a) {
			for (i = 0, l = a.length; i < l; i++) {
				if (a[i] === v)
					return i;
			}
		}

		return -1;
	},

	extend : function(o, e) {
		var i, a = arguments;

		for (i=1; i<a.length; i++) {
			e = a[i];

			tinymce.each(e, function(v, n) {
				if (typeof(v) !== 'undefined')
					o[n] = v;
			});
		}

		return o;
	},

	trim : function(s) {
		return (s ? '' + s : '').replace(/^\s*|\s*$/g, '');
	},

	// #endif

	create : function(s, p) {
		var t = this, sp, ns, cn, scn, c, de = 0;

		// Parse : <prefix> <class>:<super class>
		s = /^((static) )?([\w.]+)(:([\w.]+))?/.exec(s);
		cn = s[3].match(/(^|\.)(\w+)$/i)[2]; // Class name

		// Create namespace for new class
		ns = t.createNS(s[3].replace(/\.\w+$/, ''));

		// Class already exists
		if (ns[cn])
			return;

		// Make pure static class
		if (s[2] == 'static') {
			ns[cn] = p;

			if (this.onCreate)
				this.onCreate(s[2], s[3], ns[cn]);

			return;
		}

		// Create default constructor
		if (!p[cn]) {
			p[cn] = function() {};
			de = 1;
		}

		// Add constructor and methods
		ns[cn] = p[cn];
		t.extend(ns[cn].prototype, p);

		// Extend
		if (s[5]) {
			sp = t.resolve(s[5]).prototype;
			scn = s[5].match(/\.(\w+)$/i)[1]; // Class name

			// Extend constructor
			c = ns[cn];
			if (de) {
				// Add passthrough constructor
				ns[cn] = function() {
					return sp[scn].apply(this, arguments);
				};
			} else {
				// Add inherit constructor
				ns[cn] = function() {
					this.parent = sp[scn];
					return c.apply(this, arguments);
				};
			}
			ns[cn].prototype[cn] = ns[cn];

			// Add super methods
			t.each(sp, function(f, n) {
				if (n != scn)
					ns[cn].prototype[n] = sp[n];
			});

			// Add overridden methods
			t.each(p, function(f, n) {
				// Extend methods if needed
				if (sp[n]) {
					ns[cn].prototype[n] = function() {
						this.parent = sp[n];
						return f.apply(this, arguments);
					};
				} else {
					if (n != cn)
						ns[cn].prototype[n] = f;
				}
			});
		}

		// Add static methods
		t.each(p['static'], function(f, n) {
			ns[cn][n] = f;
		});

		if (this.onCreate)
			this.onCreate(s[2], s[3], ns[cn].prototype);
	},

	walk : function(o, f, n, s) {
		s = s || this;

		if (o) {
			if (n)
				o = o[n];

			tinymce.each(o, function(o, i) {
				if (f.call(s, o, i, n) === false)
					return false;

				tinymce.walk(o, f, n, s);
			});
		}
	},

	createNS : function(n, o) {
		var i, v;

		o = o || window;

		n = n.split('.');
		for (i=0; i<n.length; i++) {
			v = n[i];

			if (!o[v])
				o[v] = {};

			o = o[v];
		}

		return o;
	},

	resolve : function(n, o) {
		var i, l;

		o = o || window;

		n = n.split('.');
		for (i=0, l = n.length; i<l; i++) {
			o = o[n[i]];

			if (!o)
				break;
		}

		return o;
	},

	addUnload : function(f, s) {
		var t = this, w = window, unload;

		f = {func : f, scope : s || this};

		if (!t.unloads) {
			unload = function() {
				var li = t.unloads, o, n;

				// Call unload handlers
				for (n in li) {
					o = li[n];

					if (o && o.func)
						o.func.call(o.scope);
				}

				// Detach unload function
				if (w.detachEvent)
					w.detachEvent('onunload', unload);
				else if (w.removeEventListener)
					w.removeEventListener('unload', unload, false);

				// Destroy references
				o = li = w = unload = null;

				// Run garbarge collector on IE
				if (window.CollectGarbage)
					window.CollectGarbage();
			};

			// Attach unload handler
			if (w.attachEvent)
				w.attachEvent('onunload', unload);
			else if (w.addEventListener)
				w.addEventListener('unload', unload, false);

			// Setup initial unload handler array
			t.unloads = [f];
		} else
			t.unloads.push(f);

		return f;
	},

	removeUnload : function(f) {
		var u = this.unloads, r = null;

		tinymce.each(u, function(o, i) {
			if (o && o.func == f) {
				u.splice(i, 1);
				r = f;
				return false;
			}
		});

		return r;
	}

	};

// Required for GZip AJAX loading
window.tinymce = tinymce;

// Initialize the API
tinymce._init();

/* file:jscripts/tiny_mce/classes/adapter/jquery/adapter.js */


/* file:jscripts/tiny_mce/classes/adapter/prototype/adapter.js */


/* file:jscripts/tiny_mce/classes/util/Dispatcher.js */

tinymce.create('tinymce.util.Dispatcher', {
	scope : null,
	listeners : null,

	Dispatcher : function(s) {
		this.scope = s || this;
		this.listeners = [];
	},

	add : function(cb, s) {
		this.listeners.push({cb : cb, scope : s || this.scope});

		return cb;
	},

	addToTop : function(cb, s) {
		this.listeners.unshift({cb : cb, scope : s || this.scope});

		return cb;
	},

	remove : function(cb) {
		var l = this.listeners, o = null;

		tinymce.each(l, function(c, i) {
			if (cb == c.cb) {
				o = cb;
				l.splice(i, 1);
				return false;
			}
		});

		return o;
	},

	dispatch : function() {
		var s, a = arguments;

		tinymce.each(this.listeners, function(c) {
			return s = c.cb.apply(c.scope, a);
		});

		return s;
	}

	});

/* file:jscripts/tiny_mce/classes/util/URI.js */

(function() {
	var each = tinymce.each;

	tinymce.create('tinymce.util.URI', {
		URI : function(u, s) {
			var t = this, o, a, b;

			// Default settings
			s = t.settings = s || {};

			// Strange app protocol or local anchor
			if (/^(mailto|news|javascript|about):/i.test(u) || /^\s*#/.test(u)) {
				t.source = u;
				return;
			}

			// Absolute path with no host, fake host and protocol
			if (u.indexOf('/') === 0 && u.indexOf('//') !== 0)
				u = (s.base_uri ? s.base_uri.protocol || 'http' : 'http') + '://mce_host' + u;

			// Relative path
			if (u.indexOf('://') === -1 && u.indexOf('//') !== 0)
				u = (s.base_uri.protocol || 'http') + '://mce_host' + t.toAbsPath(s.base_uri.path, u);

			// Parse URL (Credits goes to Steave, http://blog.stevenlevithan.com/archives/parseuri)
			u = u.replace(/@@/g, '(mce_at)'); // Zope 3 workaround, they use @@something
			u = /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/.exec(u);
			each(["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"], function(v, i) {
				var s = u[i];

				// Zope 3 workaround, they use @@something
				if (s)
					s = s.replace(/\(mce_at\)/g, '@@');

				t[v] = s;
			});

			if (b = s.base_uri) {
				if (!t.protocol)
					t.protocol = b.protocol;

				if (!t.userInfo)
					t.userInfo = b.userInfo;

				if (!t.port && t.host == 'mce_host')
					t.port = b.port;

				if (!t.host || t.host == 'mce_host')
					t.host = b.host;

				t.source = '';
			}

			//t.path = t.path || '/';
		},

		setPath : function(p) {
			var t = this;

			p = /^(.*?)\/?(\w+)?$/.exec(p);

			// Update path parts
			t.path = p[0];
			t.directory = p[1];
			t.file = p[2];

			// Rebuild source
			t.source = '';
			t.getURI();
		},

		toRelative : function(u) {
			var t = this, o;

			u = new tinymce.util.URI(u, {base_uri : t});

			// Not on same domain/port or protocol
			if ((u.host != 'mce_host' && t.host != u.host && u.host) || t.port != u.port || t.protocol != u.protocol)
				return u.getURI();

			o = t.toRelPath(t.path, u.path);

			// Add query
			if (u.query)
				o += '?' + u.query;

			// Add anchor
			if (u.anchor)
				o += '#' + u.anchor;

			return o;
		},
	
		toAbsolute : function(u, nh) {
			var u = new tinymce.util.URI(u, {base_uri : this});

			return u.getURI(this.host == u.host ? nh : 0);
		},

		toRelPath : function(base, path) {
			var items, bp = 0, out = '', i;

			// Split the paths
			base = base.substring(0, base.lastIndexOf('/'));
			base = base.split('/');
			items = path.split('/');

			if (base.length >= items.length) {
				for (i = 0; i < base.length; i++) {
					if (i >= items.length || base[i] != items[i]) {
						bp = i + 1;
						break;
					}
				}
			}

			if (base.length < items.length) {
				for (i = 0; i < items.length; i++) {
					if (i >= base.length || base[i] != items[i]) {
						bp = i + 1;
						break;
					}
				}
			}

			if (bp == 1)
				return path;

			for (i = 0; i < base.length - (bp - 1); i++)
				out += "../";

			for (i = bp - 1; i < items.length; i++) {
				if (i != bp - 1)
					out += "/" + items[i];
				else
					out += items[i];
			}

			return out;
		},

		toAbsPath : function(base, path) {
			var i, nb = 0, o = [];

			// Split paths
			base = base.split('/');
			path = path.split('/');

			// Remove empty chunks
			each(base, function(k) {
				if (k)
					o.push(k);
			});

			base = o;

			// Merge relURLParts chunks
			for (i = path.length - 1, o = []; i >= 0; i--) {
				// Ignore empty or .
				if (path[i].length == 0 || path[i] == ".")
					continue;

				// Is parent
				if (path[i] == '..') {
					nb++;
					continue;
				}

				// Move up
				if (nb > 0) {
					nb--;
					continue;
				}

				o.push(path[i]);
			}

			i = base.length - nb;

			// If /a/b/c or /
			if (i <= 0)
				return '/' + o.reverse().join('/');

			return '/' + base.slice(0, i).join('/') + '/' + o.reverse().join('/');
		},

		getURI : function(nh) {
			var s, t = this;

			// Rebuild source
			if (!t.source || nh) {
				s = '';

				if (!nh) {
					if (t.protocol)
						s += t.protocol + '://';

					if (t.userInfo)
						s += t.userInfo + '@';

					if (t.host)
						s += t.host;

					if (t.port)
						s += ':' + t.port;
				}

				if (t.path)
					s += t.path;

				if (t.query)
					s += '?' + t.query;

				if (t.anchor)
					s += '#' + t.anchor;

				t.source = s;
			}

			return t.source;
		}

		});
})();

/* file:jscripts/tiny_mce/classes/util/Cookie.js */

(function() {
	var each = tinymce.each;

	tinymce.create('static tinymce.util.Cookie', {
		getHash : function(n) {
			var v = this.get(n), h;

			if (v) {
				each(v.split('&'), function(v) {
					v = v.split('=');
					h = h || {};
					h[unescape(v[0])] = unescape(v[1]);
				});
			}

			return h;
		},

		setHash : function(n, v, e, p, d, s) {
			var o = '';

			each(v, function(v, k) {
				o += (!o ? '' : '&') + escape(k) + '=' + escape(v);
			});

			this.set(n, o, e, p, d, s);
		},

		get : function(n) {
			var c = document.cookie, e, p = n + "=", b;

			// Strict mode
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

			return unescape(c.substring(b + p.length, e));
		},

		set : function(n, v, e, p, d, s) {
			document.cookie = n + "=" + escape(v) +
				((e) ? "; expires=" + e.toGMTString() : "") +
				((p) ? "; path=" + escape(p) : "") +
				((d) ? "; domain=" + d : "") +
				((s) ? "; secure" : "");
		},

		remove : function(n, p) {
			var d = new Date();

			d.setTime(d.getTime() - 1000);

			this.set(n, '', d, p, d);
		}

		});
})();

/* file:jscripts/tiny_mce/classes/util/JSON.js */

tinymce.create('static tinymce.util.JSON', {
	serialize : function(o) {
		var i, v, s = tinymce.util.JSON.serialize, t;

		if (o == null)
			return 'null';

		t = typeof o;

		if (t == 'string') {
			v = '\bb\tt\nn\ff\rr\""\'\'\\\\';

			return '"' + o.replace(/([\u0080-\uFFFF\x00-\x1f\"\'])/g, function(a, b) {
				i = v.indexOf(b);

				if (i + 1)
					return '\\' + v.charAt(i + 1);

				a = b.charCodeAt().toString(16);

				return '\\u' + '0000'.substring(a.length) + a;
			}) + '"';
		}

		if (t == 'object') {
			if (o instanceof Array) {
					for (i=0, v = '['; i<o.length; i++)
						v += (i > 0 ? ',' : '') + s(o[i]);

					return v + ']';
				}

				v = '{';

				for (i in o)
					v += typeof o[i] != 'function' ? (v.length > 1 ? ',"' : '"') + i + '":' + s(o[i]) : '';

				return v + '}';
		}

		return '' + o;
	},

	parse : function(s) {
		try {
			return eval('(' + s + ')');
		} catch (ex) {
			// Ignore
		}
	}

	});

/* file:jscripts/tiny_mce/classes/util/XHR.js */

tinymce.create('static tinymce.util.XHR', {
	send : function(o) {
		var x, t, w = window, c = 0;

		// Default settings
		o.scope = o.scope || this;
		o.success_scope = o.success_scope || o.scope;
		o.error_scope = o.error_scope || o.scope;
		o.async = o.async === false ? false : true;
		o.data = o.data || '';

		function get(s) {
			x = 0;

			try {
				x = new ActiveXObject(s);
			} catch (ex) {
			}

			return x;
		};

		x = w.XMLHttpRequest ? new XMLHttpRequest() : get('Microsoft.XMLHTTP') || get('Msxml2.XMLHTTP');

		if (x) {
			if (x.overrideMimeType)
				x.overrideMimeType(o.content_type);

			x.open(o.type || (o.data ? 'POST' : 'GET'), o.url, o.async);

			if (o.content_type)
				x.setRequestHeader('Content-Type', o.content_type);

			x.send(o.data);

			// Wait for response, onReadyStateChange can not be used since it leaks memory in IE
			t = w.setInterval(function() {
				if (x.readyState == 4 || c++ > 10000) {
					w.clearInterval(t);

					if (o.success && c < 10000 && x.status == 200)
						o.success.call(o.success_scope, '' + x.responseText, x, o);
					else if (o.error)
						o.error.call(o.error_scope, c > 10000 ? 'TIMED_OUT' : 'GENERAL', x, o);

					x = null;
				}
			}, 10);
		}

		}
});

/* file:jscripts/tiny_mce/classes/util/JSONRequest.js */

(function() {
	var extend = tinymce.extend, JSON = tinymce.util.JSON, XHR = tinymce.util.XHR;

	tinymce.create('tinymce.util.JSONRequest', {
		JSONRequest : function(s) {
			this.settings = extend({
			}, s);
			this.count = 0;
		},

		send : function(o) {
			var ecb = o.error, scb = o.success;

			o = extend(this.settings, o);

			o.success = function(c, x) {
				c = JSON.parse(c);

				if (typeof(c) == 'undefined') {
					c = {
						error : 'JSON Parse error.'
					};
				}

				if (c.error)
					ecb.call(o.error_scope || o.scope, c.error, x);
				else
					scb.call(o.success_scope || o.scope, c.result);
			};

			o.error = function(ty, x) {
				ecb.call(o.error_scope || o.scope, ty, x);
			};

			o.data = JSON.serialize({
				id : o.id || 'c' + (this.count++),
				method : o.method,
				params : o.params
			});

			XHR.send(o);
		},

		'static' : {
			sendRPC : function(o) {
				return new tinymce.util.JSONRequest().send(o);
			}
		}

		});
}());
/* file:jscripts/tiny_mce/classes/dom/DOMUtils.js */

(function() {
	// Shorten names
	var each = tinymce.each, is = tinymce.is;
	var isWebKit = tinymce.isWebKit, isIE = tinymce.isIE;

	tinymce.create('tinymce.dom.DOMUtils', {
		doc : null,
		root : null,
		files : null,
		listeners : {},
		pixelStyles : /^(top|left|bottom|right|width|height|borderWidth)$/,
		cache : {},
		idPattern : /^#[\w]+$/,
		elmPattern : /^[\w_*]+$/,
		elmClassPattern : /^([\w_]*)\.([\w_]+)$/,

		DOMUtils : function(d, s) {
			var t = this;

			t.doc = d;
			t.files = {};
			t.cssFlicker = false;
			t.counter = 0;
			t.boxModel = !tinymce.isIE || d.compatMode == "CSS1Compat"; 

			this.settings = s = tinymce.extend({
				keep_values : false,
				hex_colors : 1
			}, s);

			// Fix IE6SP2 flicker and check it failed for pre SP2
			if (tinymce.isIE6) {
				try {
					d.execCommand('BackgroundImageCache', false, true);
				} catch (e) {
					t.cssFlicker = true;
				}
			}

			tinymce.addUnload(function() {
				t.doc = t.root = null;
			});
		},

		getRoot : function() {
			var t = this, s = t.settings;

			return (s && t.get(s.root_element)) || t.doc.body;
		},

		getViewPort : function(w) {
			var d, b;

			w = !w ? window : w;
			d = w.document;
			b = this.boxModel ? d.documentElement : d.body;

			// Returns viewport size excluding scrollbars
			return {
				x : w.pageXOffset || b.scrollLeft,
				y : w.pageYOffset || b.scrollTop,
				w : w.innerWidth || b.clientWidth,
				h : w.innerHeight || b.clientHeight
			};
		},

		getRect : function(e) {
			var p, t = this, w, h;

			e = t.get(e);
			p = t.getPos(e);
			w = t.getStyle(e, 'width');
			h = t.getStyle(e, 'height');

			// Non pixel value, then force offset/clientWidth
			if (w.indexOf('px') === -1)
				w = 0;

			// Non pixel value, then force offset/clientWidth
			if (h.indexOf('px') === -1)
				h = 0;

			return {
				x : p.x,
				y : p.y,
				w : parseInt(w) || e.offsetWidth || e.clientWidth,
				h : parseInt(h) || e.offsetHeight || e.clientHeight
			};
		},

		getParent : function(n, f, r) {
			var na, se = this.settings;

			n = this.get(n);

			if (se.strict_root)
				r = r || this.getRoot();

			// Wrap node name as func
			if (is(f, 'string')) {
				na = f.toUpperCase();

				f = function(n) {
					var s = false;

					// Any element
					if (n.nodeType == 1 && na === '*') {
						s = true;
						return false;
					}

					each(na.split(','), function(v) {
						if (n.nodeType == 1 && ((se.strict && n.nodeName.toUpperCase() == v) || n.nodeName == v)) {
							s = true;
							return false; // Break loop
						}
					});

					return s;
				};
			}

			while (n) {
				if (n == r)
					return null;

				if (f(n))
					return n;

				n = n.parentNode;
			}

			return null;
		},

		get : function(e) {
			if (typeof(e) == 'string')
				return this.doc.getElementById(e);

			return e;
		},

		// #if !jquery

		select : function(pa, s) {
			var t = this, cs, c, pl, o = [], x, i, l, n;

			s = t.get(s) || t.doc;

			if (t.settings.strict) {
				function get(s, n) {
					return s.getElementsByTagName(n.toLowerCase());
				};
			} else {
				function get(s, n) {
					return s.getElementsByTagName(n);
				};
			}

			// Simple element pattern. For example: "p" or "*"
			if (t.elmPattern.test(pa)) {
				x = get(s, pa);

				for (i = 0, l = x.length; i<l; i++)
					o.push(x[i]);

				return o;
			}

			// Simple class pattern. For example: "p.class" or ".class"
			if (t.elmClassPattern.test(pa)) {
				pl = t.elmClassPattern.exec(pa);
				x = get(s, pl[1] || '*');
				c = ' ' + pl[2] + ' ';

				for (i = 0, l = x.length; i<l; i++) {
					n = x[i];

					if (n.className && (' ' + n.className + ' ').indexOf(c) !== -1)
						o.push(n);
				}

				return o;
			}

			function collect(n) {
				if (!n.mce_save) {
					n.mce_save = 1;
					o.push(n);
				}
			};

			function collectIE(n) {
				if (!n.getAttribute('mce_save')) {
					n.setAttribute('mce_save', '1');
					o.push(n);
				}
			};

			function find(n, f, r) {
				var i, l, nl = get(r, n);

				for (i = 0, l = nl.length; i < l; i++)
					f(nl[i]);
			};

			each(pa.split(','), function(v, i) {
				v = tinymce.trim(v);

				// Simple element pattern, most common in TinyMCE
				if (t.elmPattern.test(v)) {
					each(get(s, v), function(n) {
						collect(n);
					});

					return;
				}

				// Simple element pattern with class, fairly common in TinyMCE
				if (t.elmClassPattern.test(v)) {
					x = t.elmClassPattern.exec(v);

					each(get(s, x[1]), function(n) {
						if (t.hasClass(n, x[2]))
							collect(n);
					});

					return;
				}

				if (!(cs = t.cache[pa])) {
					cs = 'x=(function(cf, s) {';
					pl = v.split(' ');

					each(pl, function(v) {
						var p = /^([\w\\*]+)?(?:#([\w\\]+))?(?:\.([\w\\\.]+))?(?:\[\@([\w\\]+)([\^\$\*!]?=)([\w\\]+)\])?(?:\:([\w\\]+))?/i.exec(v);

						// Find elements
						p[1] = p[1] || '*';
						cs += 'find("' + p[1] + '", function(n) {';

						// Check id
						if (p[2])
							cs += 'if (n.id !== "' + p[2] + '") return;';

						// Check classes
						if (p[3]) {
							cs += 'var c = " " + n.className + " ";';
							cs += 'if (';
							c = '';
							each(p[3].split('.'), function(v) {
								if (v)
									c += (c ? '||' : '') + 'c.indexOf(" ' + v + ' ") === -1';
							});
							cs += c + ') return;';
						}
					});

					cs += 'cf(n);';

					for (i = pl.length - 1; i >= 0; i--)
						cs += '}, ' + (i ? 'n' : 's') + ');';

					cs += '})';

					// Compile CSS pattern function
					t.cache[pa] = cs = eval(cs);
				}

				// Run selector function
				cs(isIE ? collectIE : collect, s);
			});

			// Cleanup
			each(o, function(n) {
				if (isIE)
					n.removeAttribute('mce_save');
				else
					delete n.mce_save;
			});

			return o;
		},

		// #endif

		add : function(p, n, a, h, c) {
			var t = this;

			return this.run(p, function(p) {
				var e, k;

				e = is(n, 'string') ? t.doc.createElement(n) : n;

				if (a) {
					for (k in a) {
						if (a.hasOwnProperty(k) && !is(a[k], 'object'))
							t.setAttrib(e, k, '' + a[k]);
					}

					if (a.style && !is(a.style, 'string')) {
						each(a.style, function(v, n) {
							t.setStyle(e, n, v);
						});
					}
				}

				if (h) {
					if (h.nodeType)
						e.appendChild(h);
					else
						e.innerHTML = h;
				}

				return !c ? p.appendChild(e) : e;
			});
		},

		create : function(n, a, h) {
			return this.add(this.doc.createElement(n), n, a, h, 1);
		},

		createHTML : function(n, a, h) {
			var o = '', t = this, k;

			o += '<' + n;

			for (k in a) {
				if (a.hasOwnProperty(k))
					o += ' ' + k + '="' + t.encode(a[k]) + '"';
			}

			if (tinymce.is(h))
				return o + '>' + h + '</' + n + '>';

			return o + ' />';
		},

		remove : function(n, k) {
			return this.run(n, function(n) {
				var p;

				p = n.parentNode;

				if (!p)
					return null;

				if (k) {
					each (n.childNodes, function(c) {
						p.insertBefore(c.cloneNode(true), n);
					});
				}

				return p.removeChild(n);
			});
		},

		// #if !jquery

		setStyle : function(n, na, v) {
			var t = this;

			return t.run(n, function(e) {
				var s, i;

				s = e.style;

				// Camelcase it, if needed
				na = na.replace(/-(\D)/g, function(a, b){
					return b.toUpperCase();
				});

				// Default px suffix on these
				if (t.pixelStyles.test(na) && (tinymce.is(v, 'number') || /^[\-0-9\.]+$/.test(v)))
					v += 'px';

				switch (na) {
					case 'opacity':
						// IE specific opacity
						if (isIE) {
							s.filter = v === '' ? '' : "alpha(opacity=" + (v * 100) + ")";

							if (!n.currentStyle || !n.currentStyle.hasLayout)
								s.display = 'inline-block';
						}

						// Fix for older browsers
						s['-moz-opacity'] = s['-khtml-opacity'] = v;
						break;

					case 'float':
						isIE ? s.styleFloat = v : s.cssFloat = v;
						break;
				}

				s[na] = v || '';

				// Force update of the style data
				if (t.settings.update_styles)
					t.setAttrib(e, 'mce_style');
			});
		},

		getStyle : function(n, na, c) {
			n = this.get(n);

			if (!n)
				return false;

			// Gecko
			if (this.doc.defaultView && c) {
				// Remove camelcase
				na = na.replace(/[A-Z]/g, function(a){
					return '-' + a;
				});

				try {
					return this.doc.defaultView.getComputedStyle(n, null).getPropertyValue(na);
				} catch (ex) {
					// Old safari might fail
					return null;
				}
			}

			// Camelcase it, if needed
			na = na.replace(/-(\D)/g, function(a, b){
				return b.toUpperCase();
			});

			if (na == 'float')
				na = isIE ? 'styleFloat' : 'cssFloat';

			// IE & Opera
			if (n.currentStyle && c)
				return n.currentStyle[na];

			return n.style[na];
		},

		setStyles : function(e, o) {
			var t = this, s = t.settings, ol;

			ol = s.update_styles;
			s.update_styles = 0;

			each(o, function(v, n) {
				t.setStyle(e, n, v);
			});

			// Update style info
			s.update_styles = ol;
			if (s.update_styles)
				t.setAttrib(e, s.cssText);
		},

		setAttrib : function(e, n, v) {
			var t = this;

			// Strict XML mode
			if (t.settings.strict)
				n = n.toLowerCase();

			return this.run(e, function(e) {
				var s = t.settings;

				switch (n) {
					case "style":
						if (s.keep_values) {
							if (v)
								e.setAttribute('mce_style', v, 2);
							else
								e.removeAttribute('mce_style', 2);
						}

						e.style.cssText = v;
						break;

					case "class":
						e.className = v;
						break;

					case "src":
					case "href":
						if (s.keep_values) {
							if (s.url_converter)
								v = s.url_converter.call(s.url_converter_scope || t, v, n, e);

							t.setAttrib(e, 'mce_' + n, v, 2);
						}

						break;
				}

				if (is(v) && v !== null && v.length !== 0)
					e.setAttribute(n, '' + v, 2);
				else
					e.removeAttribute(n, 2);
			});
		},

		setAttribs : function(e, o) {
			var t = this;

			return this.run(e, function(e) {
				each(o, function(v, n) {
					t.setAttrib(e, n, v);
				});
			});
		},

		// #endif

		getAttrib : function(e, n, dv) {
			var v, t = this;

			e = t.get(e);

			if (!e)
				return false;

			if (!is(dv))
				dv = "";

			// Try the mce variant for these
			if (/^(src|href|style)$/.test(n)) {
				v = e.getAttribute("mce_" + n);

				if (v)
					return v;
			}

			v = e.getAttribute(n, 2);

			if (!v) {
				switch (n) {
					case 'class':
						v = e.className;
						break;

					default:
						v = e.attributes[n];
						v = v && is(v.nodeValue) ? v.nodeValue : v;
				}
			}

			switch (n) {
				case 'style':
					v = v || e.style.cssText;

					if (v) {
						v = t.serializeStyle(t.parseStyle(v));

						if (t.settings.keep_values)
							e.setAttribute('mce_style', v);
					}

					break;
			}

			// Remove Apple and WebKit stuff
			if (isWebKit && n === "class" && v)
				v = v.replace(/(apple|webkit)\-[a-z\-]+/gi, '');

			// Handle IE issues
			if (isIE) {
				switch (n) {
					case 'rowspan':
					case 'colspan':
						// IE returns 1 as default value
						if (v === 1)
							v = '';

						break;

					case 'size':
						// IE returns +0 as default value for size
						if (v === '+0')
							v = '';

						break;

					case 'hspace':
						// IE returns -1 as default value
						if (v === -1)
							v = '';

						break;

					case 'tabindex':
						// IE returns 32768 as default value
						if (v === 32768)
							v = '';

						break;

					default:
						// IE has odd anonymous function for event attributes
						if (n.indexOf('on') === 0 && v)
							v = ('' + v).replace(/^function\s+anonymous\(\)\s+\{\s+(.*)\s+\}$/, '$1');
				}
			}

			return (v && v != '') ? '' + v : dv;
		},

		getPos : function(n) {
			var t = this, x = 0, y = 0, e, d = t.doc;

			n = t.get(n);

			// Use getBoundingClientRect on IE, Opera has it but it's not perfect
			if (n && isIE) {
				n = n.getBoundingClientRect();
				e = t.boxModel ? d.documentElement : d.body;
				x = t.getStyle(t.select('html')[0], 'borderWidth'); // Remove border
				x = (x == 'medium' || t.boxModel && !t.isIE6) && 2 || x;
				n.top += window.self != window.top ? 2 : 0; // IE adds some strange extra cord if used in a frameset

				return {x : n.left + e.scrollLeft - x, y : n.top + e.scrollTop - x};
			}

			while (n) {
				x += n.offsetLeft || 0;
				y += n.offsetTop || 0;
				x -= n.scrollLeft || 0;
				y -= n.scrollTop || 0;
				n = n.offsetParent;
			}

			return {x : x, y : y};
		},

		parseStyle : function(st) {
			var t = this, s = t.settings, o = {};

			if (!st)
				return o;

			function compress(p, s, ot) {
				var t, r, b, l;

				// Get values and check it it needs compressing
				t = o[p + '-top' + s];
				if (!t)
					return;

				r = o[p + '-right' + s];
				if (t != r)
					return;

				b = o[p + '-bottom' + s];
				if (r != b)
					return;

				l = o[p + '-left' + s];
				if (b != l)
					return;

				// Compress
				o[ot] = l;
				delete o[p + '-top' + s];
				delete o[p + '-right' + s];
				delete o[p + '-bottom' + s];
				delete o[p + '-left' + s];
			};

			function compress2(ta, a, b, c) {
				var t;

				t = o[a];
				if (!t)
					return;

				t = o[b];
				if (!t)
					return;

				t = o[c];
				if (!t)
					return;

				// Compress
				o[ta] = o[a] + ' ' + o[b] + ' ' + o[c];
				delete o[a];
				delete o[b];
				delete o[c];
			};

			each(st.split(';'), function(v) {
				var sv, ur = [];

				if (v) {
					v = v.replace(/url\([^\)]+\)/g, function(v) {ur.push(v);return 'url(' + ur.length + ')';});
					v = v.split(':');
					sv = tinymce.trim(v[1]);
					sv = sv.replace(/url\(([^\)]+)\)/g, function(a, b) {return ur[parseInt(b) - 1];});

					sv = sv.replace(/rgb\([^\)]+\)/g, function(v) {
						return t.toHex(v);
					});

					if (s.url_converter) {
						sv = sv.replace(/url\([\'\"]?([^\)\'\"]+)[\'\"]?\)/g, function(x, c) {
							return 'url(' + t.encode(s.url_converter.call(s.url_converter_scope || t, t.decode(c), 'style', null)) + ')';
						});
					}

					o[tinymce.trim(v[0]).toLowerCase()] = sv;
				}
			});

			compress("border", "", "border");
			compress("border", "-width", "border-width");
			compress("border", "-color", "border-color");
			compress("border", "-style", "border-style");
			compress("padding", "", "padding");
			compress("margin", "", "margin");
			compress2('border', 'border-width', 'border-style', 'border-color');

			return o;
		},

		serializeStyle : function(o) {
			var s = '';

			each(o, function(v, k) {
				if (k && v) {
					switch (k) {
						case 'color':
						case 'background-color':
							v = v.toLowerCase();
							break;
					}

					s += (s ? ' ' : '') + k + ': ' + v + ';';
				}
			});

			return s;
		},

		loadCSS : function(u) {
			var t = this, d = this.doc;

			if (!u)
				u = '';

			each(u.split(','), function(u) {
				if (t.files[u])
					return;

				t.files[u] = true;

				if (!d.createStyleSheet)
					t.add(t.select('head')[0], 'link', {rel : 'stylesheet', href : u});
				else
					d.createStyleSheet(u);
			});
		},

		// #if !jquery

		addClass : function(e, c) {
			return this.run(e, function(e) {
				var o;

				if (!c)
					return 0;

				if (this.hasClass(e, c))
					return e.className;

				o = this.removeClass(e, c);

				return e.className = (o != '' ? (o + ' ') : '') + c;
			});
		},

		removeClass : function(e, c) {
			var t = this, re;

			return t.run(e, function(e) {
				var v;

				if (t.hasClass(e, c)) {
					if (!re)
						re = new RegExp("(^|\\s+)" + c + "(\\s+|$)", "g");

					v = e.className.replace(re, ' ');

					return e.className = tinymce.trim(v != ' ' ? v : '');
				}

				return e.className;
			});
		},

		hasClass : function(n, c) {
			n = this.get(n);

			if (!n || !c)
				return false;

			return (' ' + n.className + ' ').indexOf(' ' + c + ' ') !== -1;
		},

		show : function(e) {
			return this.setStyle(e, 'display', 'block');
		},

		hide : function(e) {
			return this.setStyle(e, 'display', 'none');
		},

		isHidden : function(e) {
			e = this.get(e);

			return e.style.display == 'none' || this.getStyle(e, 'display') == 'none';
		},

		// #endif

		uniqueId : function(p) {
			return (!p ? 'mce_' : p) + (this.counter++);
		},

		setHTML : function(e, h) {
			var t = this;

			return this.run(e, function(e) {
				var r;

				h = t.processHTML(h);

				if (isIE) {
					e.innerHTML = '<br />' + h;
					e.removeChild(e.firstChild);
				} else
					e.innerHTML = h;

				return h;
			});
		},

		processHTML : function(h) {
			var t = this, s = t.settings;

			// Convert strong and em to b and i in FF since it can't handle them
			if (tinymce.isGecko) {
				h = h.replace(/<(\/?)strong>|<strong( [^>]+)>/gi, '<$1b$2>');
				h = h.replace(/<(\/?)em>|<em( [^>]+)>/gi, '<$1i$2>');
			}

			// Fix some issues
			h = h.replace(/<(a)([^>]*)\/>/gi, '<$1$2></$1>'); // Force open

			// Store away src and href in mce_src and mce_href since browsers mess them up
			if (s.keep_values) {
				// Wrap scripts in comments for serialization purposes
				if (h.indexOf('<script') !== -1) {
					h = h.replace(/<script>/g, '<script type="text/javascript">');
					h = h.replace(/<script(|[^>]+)>(\s*<!--|\/\/\s*<\[CDATA\[)?[\r\n]*/g, '<mce:script$1><!--\n');
					h = h.replace(/\s*(\/\/\s*-->|\/\/\s*]]>)?<\/script>/g, '\n// --></mce:script>');
					h = h.replace(/<mce:script(|[^>]+)><!--\n\/\/ --><\/mce:script>/g, '<mce:script$1></mce:script>');
				}

				// Process all tags with src, href or style
				h = h.replace(/<([\w:]+) [^>]*(src|href|style)[^>]*>/gi, function(a, n) {
					function handle(m, b, c) {
						var u = c;

						// Tag already got a mce_ version
						if (a.indexOf('mce_' + b) != -1)
							return m;

						if (b == 'style') {
							// Why did I need this one?
							//if (isIE)
							//	u = t.serializeStyle(t.parseStyle(u));

							if (s.hex_colors) {
								u = u.replace(/rgb\([^\)]+\)/g, function(v) {
									return t.toHex(v);
								});
							}

							if (s.url_converter) {
								u = u.replace(/url\([\'\"]?([^\)\'\"]+)\)/g, function(x, c) {
									return 'url(' + t.encode(s.url_converter.call(s.url_converter_scope || t, t.decode(c), b, n)) + ')';
								});
							}
						} else {
							if (s.url_converter)
								u = t.encode(s.url_converter.call(s.url_converter_scope || t, t.decode(c), b, n));
						}

						return ' ' + b + '="' + c + '" mce_' + b + '="' + u + '"';
					};

					a = a.replace(/ (src|href|style)=[\"]([^\"]+)[\"]/gi, handle); // W3C
					a = a.replace(/ (src|href|style)=[\']([^\']+)[\']/gi, handle); // W3C

					return a.replace(/ (src|href|style)=([^\s\"\'>]+)/gi, handle); // IE
				});
			}

			return h;
		},

		getOuterHTML : function(e) {
			var d;

			e = this.get(e);

			if (!e)
				return null;

			if (isIE)
				return e.outerHTML;

			d = (e.ownerDocument || this.doc).createElement("body");
			d.appendChild(e.cloneNode(true));

			return d.innerHTML;
		},

		setOuterHTML : function(e, h, d) {
			var t = this;

			return this.run(e, function(e) {
				var n, tp;

				e = t.get(e);
				d = d || e.ownerDocument || t.doc;

				if (isIE && e.nodeType == 1)
					e.outerHTML = h;
				else {
					tp = d.createElement("body");
					tp.innerHTML = h;

					n = tp.lastChild;
					while (n) {
						t.insertAfter(n.cloneNode(true), e);
						n = n.previousSibling;
					}

					t.remove(e);
				}
			});
		},

		decode : function(s) {
			var e = document.createElement("div");

			e.innerHTML = s;

			return !e.firstChild ? s : e.firstChild.nodeValue;
		},

		encode : function(s) {
			return s ? ('' + s).replace(/[<>&\"]/g, function (c, b) {
				switch (c) {
					case '&':
						return '&amp;';

					case '"':
						return '&quot;';

					case '<':
						return '&lt;';

					case '>':
						return '&gt;';
				}

				return c;
			}) : s;
		},

		// #if !jquery

		insertAfter : function(n, r) {
			var t = this;

			r = t.get(r);

			return this.run(n, function(n) {
				var p, ns;

				p = r.parentNode;
				ns = r.nextSibling;

				if (ns)
					p.insertBefore(n, ns);
				else
					p.appendChild(n);

				return n;
			});
		},

		// #endif

		isBlock : function(n) {
			if (n.nodeType && n.nodeType !== 1)
				return false;

			n = n.nodeName || n;

			return /^(H[1-6]|HR|P|DIV|ADDRESS|PRE|FORM|TABLE|LI|OL|UL|TD|CAPTION|BLOCKQUOTE|CENTER|DL|DT|DD|DIR|FIELDSET|FORM|NOSCRIPT|NOFRAMES|MENU|ISINDEX|SAMP)$/.test(n);
		},

		// #if !jquery

		replace : function(n, o, k) {
			if (is(o, 'array'))
				n = n.cloneNode(true);

			return this.run(o, function(o) {
				if (k) {
					each(o.childNodes, function(c) {
						n.appendChild(c.cloneNode(true));
					});
				}

				return o.parentNode.replaceChild(n, o);
			});
		},

		// #endif

		toHex : function(s) {
			var c = /^\s*rgb\s*?\(\s*?([0-9]+)\s*?,\s*?([0-9]+)\s*?,\s*?([0-9]+)\s*?\)\s*$/i.exec(s);

			function hex(s) {
				s = parseInt(s).toString(16);

				return s.length > 1 ? s : '0' + s; // 0 -> 00
			};

			if (c) {
				s = '#' + hex(c[1]) + hex(c[2]) + hex(c[3]);

				return s;
			}

			return s;
		},

		getClasses : function() {
			var t = this, cl = [], i, lo = {}, f = t.settings.class_filter;

			if (t.classes)
				return t.classes;

			function addClasses(s) {
				// IE style imports
				each(s.imports, function(r) {
					addClasses(r);
				});

				each(s.cssRules || s.rules, function(r) {
					// Real type or fake it on IE
					switch (r.type || 1) {
						// Rule
						case 1:
							if (r.selectorText) {
								each(r.selectorText.split(','), function(v) {
									v = v.replace(/^\s*|\s*$|^\s\./g, "");

									if (f && !(v = f(v)))
										return;

									if (/^\.mce/.test(v) || !/^\.[\w\-]+$/.test(v))
										return;

									v = v.substring(1);
									if (!lo[v]) {
										cl.push({'class' : v});
										lo[v] = 1;
									}
								});
							}
							break;

						// Import
						case 3:
							addClasses(r.styleSheet);
							break;
					}
				});
			};

			try {
				each(t.doc.styleSheets, addClasses);
			} catch (ex) {
				// Ignore
			}

			if (cl.length > 0)
				t.classes = cl;

			return cl;
		},

		run : function(e, f, s) {
			var t = this, o;

			if (typeof(e) === 'string')
				e = t.doc.getElementById(e);

			if (!e)
				return false;

			s = s || this;
			if (!e.nodeType && (e.length || e.length === 0)) {
				o = [];

				each(e, function(e, i) {
					if (e) {
						if (typeof(e) == 'string')
							e = t.doc.getElementById(e);

						o.push(f.call(s, e, i));
					}
				});

				return o;
			}

			return f.call(s, e);
		}

		/*
		walk : function(n, f, s) {
			var d = this.doc, w;

			if (d.createTreeWalker) {
				w = d.createTreeWalker(n, NodeFilter.SHOW_TEXT, null, false);

				while ((n = w.nextNode()) != null)
					f.call(s || this, n);
			} else
				tinymce.walk(n, f, 'childNodes', s);
		}
		*/

		/*
		toRGB : function(s) {
			var c = /^\s*?#([0-9A-F]{2})([0-9A-F]{1,2})([0-9A-F]{2})?\s*?$/.exec(s);

			if (c) {
				// #FFF -> #FFFFFF
				if (!is(c[3]))
					c[3] = c[2] = c[1];

				return "rgb(" + parseInt(c[1], 16) + "," + parseInt(c[2], 16) + "," + parseInt(c[3], 16) + ")";
			}

			return s;
		}
		*/

		});

	// Setup page DOM
	tinymce.DOM = new tinymce.dom.DOMUtils(document);
})();

/* file:jscripts/tiny_mce/classes/dom/Event.js */

(function() {
	// Shorten names
	var each = tinymce.each, DOM = tinymce.DOM, isIE = tinymce.isIE, isWebKit = tinymce.isWebKit, Event;

	tinymce.create('static tinymce.dom.Event', {
		inits : [],
		events : [],

		// #if !jquery

		add : function(o, n, f, s) {
			var cb, t = this, el = t.events, r;

			// Handle array
			if (o && o instanceof Array) {
				r = [];

				each(o, function(o) {
					o = DOM.get(o);
					r.push(t.add(o, n, f, s));
				});

				return r;
			}

			o = DOM.get(o);

			if (!o)
				return;

			// Setup event callback
			cb = function(e) {
				e = e || window.event;

				// Patch in target in IE it's W3C valid
				if (e && !e.target && isIE)
					e.target = e.srcElement;

				if (!s)
					return f(e);

				return f.call(s, e);
			};

			if (n == 'unload') {
				tinymce.unloads.unshift({func : cb});
				return cb;
			}

			if (n == 'init') {
				if (t.domLoaded)
					cb();
				else
					t.inits.push(cb);

				return cb;
			}

			// Store away listener reference
			el.push({
				obj : o,
				name : n,
				func : f,
				cfunc : cb,
				scope : s
			});

			t._add(o, n, cb);

			return f;
		},

		remove : function(o, n, f) {
			var t = this, a = t.events, s = false, r;

			// Handle array
			if (o && o instanceof Array) {
				r = [];

				each(o, function(o) {
					o = DOM.get(o);
					r.push(t.remove(o, n, f));
				});

				return r;
			}

			o = DOM.get(o);

			each(a, function(e, i) {
				if (e.obj == o && e.name == n && (!f || (e.func == f || e.cfunc == f))) {
					a.splice(i, 1);
					t._remove(o, n, e.cfunc);
					s = true;
					return false;
				}
			});

			return s;
		},

		// #endif

		cancel : function(e) {
			if (!e)
				return false;

			this.stop(e);
			return this.prevent(e);
		},

		stop : function(e) {
			if (e.stopPropagation)
				e.stopPropagation();
			else
				e.cancelBubble = true;

			return false;
		},

		prevent : function(e) {
			if (e.preventDefault)
				e.preventDefault();
			else
				e.returnValue = false;

			return false;
		},

		_unload : function() {
			var t = Event;

			each(t.events, function(e, i) {
				t._remove(e.obj, e.name, e.cfunc);
				e.obj = e.cfunc = null;
			});

			t.events = [];
			t = null;
		},

		_add : function(o, n, f) {
			if (o.attachEvent)
				o.attachEvent('on' + n, f);
			else if (o.addEventListener)
				o.addEventListener(n, f, false);
			else
				o['on' + n] = f;
		},

		_remove : function(o, n, f) {
			if (o.detachEvent)
				o.detachEvent('on' + n, f);
			else if (o.removeEventListener)
				o.removeEventListener(n, f, false);
			else
				o['on' + n] = null;
		},

		_pageInit : function() {
			var e = Event;

			e._remove(window, 'DOMContentLoaded', e._pageInit);
			e.domLoaded = true;

			each(e.inits, function(c) {
				c();
			});

			e.inits = [];
		},

		_wait : function() {
			var t;

			// No need since the document is already loaded
			if (window.tinyMCE_GZ && tinyMCE_GZ.loaded)
				return;

			if (isIE && document.location.protocol != 'https:') {
				// Fake DOMContentLoaded on IE
				document.write('<script id=__ie_onload defer src=\'javascript:""\';><\/script>');
				DOM.get("__ie_onload").onreadystatechange = function() {
					if (this.readyState == "complete") {
						Event._pageInit();
						DOM.get("__ie_onload").onreadystatechange = null; // Prevent leak
					}
				};
			} else {
				Event._add(window, 'DOMContentLoaded', Event._pageInit, Event);

				if (isIE || isWebKit) {
					t = setInterval(function() {
						if (/loaded|complete/.test(document.readyState)) {
							clearInterval(t);
							Event._pageInit();
						}
					}, 10);
				}
			}
		}

		});

	// Shorten name
	Event = tinymce.dom.Event;

	// Dispatch DOM content loaded event for IE and Safari
	Event._wait();
	tinymce.addUnload(Event._unload);
})();

/* file:jscripts/tiny_mce/classes/dom/Element.js */

(function() {
	var each = tinymce.each;

	tinymce.create('tinymce.dom.Element', {
		Element : function(id, s) {
			var t = this, dom, el;

			s = s || {};
			t.id = id;
			t.dom = dom = s.dom || tinymce.DOM;
			t.settings = s;

			// Only IE leaks DOM references, this is a lot faster
			if (!tinymce.isIE)
				el = t.dom.get(t.id);

			each([
				'getPos',
				'getRect',
				'getParent',
				'add',
				'setStyle',
				'getStyle',
				'setStyles',
				'setAttrib',
				'setAttribs',
				'getAttrib',
				'addClass',
				'removeClass',
				'hasClass',
				'getOuterHTML',
				'setOuterHTML',
				'remove',
				'show',
				'hide',
				'isHidden',
				'setHTML',
				'get'
			], function(k) {
				t[k] = function() {
					var a = arguments, o;

					// Opera fails
					if (tinymce.isOpera) {
						a = [id];

						each(arguments, function(v) {
							a.push(v);
						});
					} else
						Array.prototype.unshift.call(a, el || id);

					o = dom[k].apply(dom, a);
					t.update(k);

					return o;
				};
			});
		},

		on : function(n, f, s) {
			return tinymce.dom.Event.add(this.id, n, f, s);
		},

		getXY : function() {
			return {
				x : parseInt(this.getStyle('left')),
				y : parseInt(this.getStyle('top'))
			};
		},

		getSize : function() {
			var n = this.dom.get(this.id);

			return {
				w : parseInt(this.getStyle('width') || n.clientWidth),
				h : parseInt(this.getStyle('height') || n.clientHeight)
			};
		},

		moveTo : function(x, y) {
			this.setStyles({left : x, top : y});
		},

		moveBy : function(x, y) {
			var p = this.getXY();

			this.moveTo(p.x + x, p.y + y);
		},

		resizeTo : function(w, h) {
			this.setStyles({width : w, height : h});
		},

		resizeBy : function(w, h) {
			var s = this.getSize();

			this.resizeTo(s.w + w, s.h + h);
		},

		update : function(k) {
			var t = this, b, dom = t.dom;

			if (tinymce.isIE6 && t.settings.blocker) {
				k = k || '';

				// Ignore getters
				if (k.indexOf('get') === 0 || k.indexOf('has') === 0 || k.indexOf('is') === 0)
					return;

				// Remove blocker on remove
				if (k == 'remove') {
					dom.remove(t.blocker);
					return;
				}

				if (!t.blocker) {
					t.blocker = dom.uniqueId();
					b = dom.add(t.settings.container || dom.getRoot(), 'iframe', {id : t.blocker, style : 'position:absolute;', frameBorder : 0, src : 'javascript:""'});
					dom.setStyle(b, 'opacity', 0);
				} else
					b = dom.get(t.blocker);

				dom.setStyle(b, 'left', t.getStyle('left', 1));
				dom.setStyle(b, 'top', t.getStyle('top', 1));
				dom.setStyle(b, 'width', t.getStyle('width', 1));
				dom.setStyle(b, 'height', t.getStyle('height', 1));
				dom.setStyle(b, 'display', t.getStyle('display', 1));
				dom.setStyle(b, 'zIndex', parseInt(t.getStyle('zIndex', 1) || 0) - 1);
			}
		}

		});
})();

/* file:jscripts/tiny_mce/classes/dom/Selection.js */

(function() {
	// Shorten names
	var is = tinymce.is, isIE = tinymce.isIE, each = tinymce.each;

	tinymce.create('tinymce.dom.Selection', {
		Selection : function(dom, win, serializer) {
			var t = this;

			t.dom = dom;
			t.win = win;
			t.serializer = serializer;

			// Prevent leaks
			tinymce.addUnload(function() {
				t.win = null;
			});
		},

		getContent : function(s) {
			var t = this, r = t.getRng(), e = t.dom.create("body"), se = t.getSel(), wb, wa, n;

			s = s || {};
			wb = wa = '';
			s.get = true;
			s.format = s.format || 'html';

			if (s.format == 'text')
				return t.isCollapsed() ? '' : (r.text || (se.toString ? se.toString() : ''));

			if (r.cloneContents) {
				n = r.cloneContents();

				if (n)
					e.appendChild(n);
			} else if (is(r.item) || is(r.htmlText))
				e.innerHTML = r.item ? r.item(0).outerHTML : r.htmlText;
			else
				e.innerHTML = r.toString();

			// Keep whitespace before and after
			if (/^\s/.test(e.innerHTML))
				wb = ' ';

			if (/\s+$/.test(e.innerHTML))
				wa = ' ';

			return t.isCollapsed() ? '' : wb + t.serializer.serialize(e, s) + wa;
		},

		setContent : function(h, s) {
			var t = this, r = t.getRng(), d;

			s = s || {format : 'html'};
			s.set = true;
			h = t.dom.processHTML(h);

			if (r.insertNode) {
				d = t.win.document;

				// Use insert HTML if it exists (places cursor after content)
				if (d.queryCommandEnabled('InsertHTML'))
					return d.execCommand('InsertHTML', false, h);

				r.deleteContents();
				r.insertNode(t.getRng().createContextualFragment(h));
			} else {
				if (r.item)
					r.item(0).outerHTML = h;
				else
					r.pasteHTML(h);
			}
		},

		getStart : function() {
			var t = this, r = t.getRng(), e;

			if (isIE) {
				if (r.item)
					return r.item(0);

				r = r.duplicate();
				r.collapse(1);
				e = r.parentElement();

				if (e.nodeName == 'BODY')
					return e.firstChild;

				return e;
			} else {
				e = r.startContainer;

				if (e.nodeName == 'BODY')
					return e.firstChild;

				return t.dom.getParent(e, function(n) {return n.nodeType == 1;});
			}
		},

		getEnd : function() {
			var t = this, r = t.getRng(), e;

			if (isIE) {
				if (r.item)
					return r.item(0);

				r = r.duplicate();
				r.collapse(0);
				e = r.parentElement();

				if (e.nodeName == 'BODY')
					return e.lastChild;

				return e;
			} else {
				e = r.endContainer;

				if (e.nodeName == 'BODY')
					return e.lastChild;

				return t.dom.getParent(e, function(n) {return n.nodeType == 1;});
			}
		},

		getBookmark : function(si) {
			var t = this, r = t.getRng(), tr, sx, sy, vp = t.dom.getViewPort(t.win), e, sp, bp, le, c = -0xFFFFFF, s, ro = t.dom.getRoot();

			sx = vp.x;
			sy = vp.y;

			// Simple bookmark fast but not as persistent
			if (si == 'simple')
				return {rng : r, scrollX : sx, scrollY : sy};

			// Handle IE
			if (isIE) {
				// Control selection
				if (r.item) {
					e = r.item(0);

					each(t.dom.select(e.nodeName), function(n, i) {
						if (e == n) {
							sp = i;
							return false;
						}
					});

					return {
						tag : e.nodeName,
						index : sp,
						scrollX : sx,
						scrollY : sy
					};
				}

				// Text selection
				tr = t.dom.doc.body.createTextRange();
				tr.moveToElementText(ro);
				tr.collapse(true);
				bp = Math.abs(tr.move('character', c));

				tr = r.duplicate();
				tr.collapse(true);
				sp = Math.abs(tr.move('character', c));

				tr = r.duplicate();
				tr.collapse(false);
				le = Math.abs(tr.move('character', c)) - sp;

				return {
					start : sp - bp,
					length : le,
					scrollX : sx,
					scrollY : sy
				};
			}

			// Handle W3C
			e = t.getNode();
			s = t.getSel();

			if (!s)
				return null;

			// Image selection
			if (e && e.nodeName == 'IMG') {
				return {
					scrollX : sx,
					scrollY : sy
				};
			}

			// Text selection

			function getPos(r, sn, en) {
				var w = document.createTreeWalker(r, NodeFilter.SHOW_TEXT, null, false), n, p = 0, d = {};

				while ((n = w.nextNode()) != null) {
					if (n == sn)
						d.start = p;

					if (n == en) {
						d.end = p;
						return d;
					}

					p += n.nodeValue ? n.nodeValue.length : 0;
				}

				return null;
			};

			// Caret or selection
			if (s.anchorNode == s.focusNode && s.anchorOffset == s.focusOffset) {
				e = getPos(ro, s.anchorNode, s.focusNode);

				if (!e)
					return {scrollX : sx, scrollY : sy};

				return {
					start : e.start + s.anchorOffset,
					end : e.end + s.focusOffset,
					scrollX : sx,
					scrollY : sy
				};
			} else {
				e = getPos(ro, r.startContainer, r.endContainer);

				if (!e)
					return {scrollX : sx, scrollY : sy};

				return {
					start : e.start + r.startOffset,
					end : e.end + r.endOffset,
					scrollX : sx,
					scrollY : sy
				};
			}
		},

		moveToBookmark : function(b) {
			var t = this, r = t.getRng(), s = t.getSel(), ro = t.dom.getRoot(), sd;

			function getPos(r, sp, ep) {
				var w = document.createTreeWalker(r, NodeFilter.SHOW_TEXT, null, false), n, p = 0, d = {};

				while ((n = w.nextNode()) != null) {
					p += n.nodeValue ? n.nodeValue.length : 0;

					if (p >= sp && !d.startNode) {
						d.startNode = n;
						d.startOffset = sp - (p - n.nodeValue.length);
					}

					if (p >= ep) {
						d.endNode = n;
						d.endOffset = ep - (p - n.nodeValue.length);

						return d;
					}
				}

				return null;
			};

			if (!b)
				return false;

			t.win.scrollTo(b.scrollX, b.scrollY);

			// Handle explorer
			if (isIE) {
				// Handle simple
				if (r = b.rng) {
					try {
						r.select();
					} catch (ex) {
						// Ignore
					}

					return true;
				}

				t.win.focus();

				// Handle control bookmark
				if (b.tag) {
					r = ro.createControlRange();

					each(t.dom.select(b.tag), function(n, i) {
						if (i == b.index)
							r.addElement(n);
					});
				} else {
					// Try/catch needed since this operation breaks when TinyMCE is placed in hidden divs/tabs
					try {
						// Incorrect bookmark
						if (b.start < 0)
							return true;

						r = s.createRange();
						r.moveToElementText(ro);
						r.collapse(true);
						r.moveStart('character', b.start);
						r.moveEnd('character', b.length);
					} catch (ex2) {
						return true;
					}
				}

				r.select();

				return true;
			}

			// Handle W3C
			if (!s)
				return false;

			// Handle simple
			if (b.rng) {
				s.removeAllRanges();
				s.addRange(b.rng);
			} else {
				if (is(b.start) && is(b.end)) {
					try {
						sd = getPos(ro, b.start, b.end);
						if (sd) {
							r = t.dom.doc.createRange();
							r.setStart(sd.startNode, sd.startOffset);
							r.setEnd(sd.endNode, sd.endOffset);
							s.removeAllRanges();
							s.addRange(r);
						}

						if (!tinymce.isOpera)
							t.win.focus();
					} catch (ex) {
						// Ignore
					}
				}
			}
		},

		select : function(n, c) {
			var t = this, r = t.getRng(), s = t.getSel(), b, fn, ln, d = t.win.document;

			function first(n) {
				return n ? d.createTreeWalker(n, NodeFilter.SHOW_TEXT, null, false).nextNode() : null;
			};

			function last(n) {
				var c, o, w;

				if (!n)
					return null;

				w = d.createTreeWalker(n, NodeFilter.SHOW_TEXT, null, false);
				while (c = w.nextNode())
					o = c;

				return o;
			};

			if (isIE) {
				try {
					b = d.body;

					if (/^(IMG|TABLE)$/.test(n.nodeName)) {
						r = b.createControlRange();
						r.addElement(n);
					} else {
						r = b.createTextRange();
						r.moveToElementText(n);
					}

					r.select();
				} catch (ex) {
					// Throws illigal agrument in IE some times
				}
			} else {
				if (c) {
					fn = first(n);
					ln = last(n);

					if (fn && ln) {
						//console.debug(fn, ln);
						r = d.createRange();
						r.setStart(fn, 0);
						r.setEnd(ln, ln.nodeValue.length);
					} else
						r.selectNode(n);
				} else
					r.selectNode(n);

				t.setRng(r);
			}

			return n;
		},

		isCollapsed : function() {
			var t = this, r = t.getRng(), s = t.getSel();

			if (!r || r.item)
				return false;

			return !s || r.boundingWidth == 0 || s.isCollapsed;
		},

		collapse : function(b) {
			var t = this, r = t.getRng(), n;

			// Control range on IE
			if (r.item) {
				n = r.item(0);
				r = this.win.document.body.createTextRange();
				r.moveToElementText(n);
			}

			r.collapse(!!b);
			t.setRng(r);
		},

		getSel : function() {
			var t = this, w = this.win;

			return w.getSelection ? w.getSelection() : w.document.selection;
		},

		getRng : function() {
			var t = this, s = t.getSel(), r;

			try {
				if (s)
					r = s.rangeCount > 0 ? s.getRangeAt(0) : (s.createRange ? s.createRange() : t.win.document.createRange());
			} catch (ex) {
				// IE throws unspecified error here if TinyMCE is placed in a frame/iframe
			}

			// No range found then create an empty one
			// This can occur when the editor is placed in a hidden container element on Gecko
			// Or on IE when there was an exception
			if (!r)
				r = isIE ? t.win.document.body.createTextRange() : t.win.document.createRange();

			return r;
		},

		setRng : function(r) {
			var s;

			if (!isIE) {
				s = this.getSel();

				if (s) {
					s.removeAllRanges();
					s.addRange(r);
				}
			} else
				r.select();
		},

		setNode : function(n) {
			var t = this;

			t.setContent(t.dom.getOuterHTML(n));

			return n;
		},

		getNode : function() {
			var t = this, r = t.getRng(), s = t.getSel(), e;

			if (!isIE) {
				// Range maybe lost after the editor is made visible again
				if (!r)
					return t.dom.getRoot();

				e = r.commonAncestorContainer;

				// Handle selection a image or other control like element such as anchors
				if (!r.collapsed) {
					if (r.startContainer == r.endContainer || (tinymce.isWebKit && r.startContainer == r.endContainer.parentNode)) {
						if (r.startOffset - r.endOffset < 2 || tinymce.isWebKit) {
							if (r.startContainer.hasChildNodes())
								e = r.startContainer.childNodes[r.startOffset];
						}
					}
				}

				return t.dom.getParent(e, function(n) {
					return n.nodeType == 1;
				});
			}

			return r.item ? r.item(0) : r.parentElement();
		}

		});
})();

/* file:jscripts/tiny_mce/classes/dom/XMLWriter.js */

(function() {
	tinymce.create('tinymce.dom.XMLWriter', {
		node : null,

		XMLWriter : function(s) {
			// Get XML document
			function getXML() {
				var i = document.implementation;

				if (!i || !i.createDocument) {
					// Try IE objects
					try {return new ActiveXObject('MSXML2.DOMDocument');} catch (ex) {}
					try {return new ActiveXObject('Microsoft.XmlDom');} catch (ex) {}
				} else
					return i.createDocument('', '', null);
			};

			this.doc = getXML();
			this.reset();
		},

		reset : function() {
			var t = this, d = t.doc;

			if (d.firstChild)
				d.removeChild(d.firstChild);

			t.node = d.appendChild(d.createElement("html"));
		},

		writeStartElement : function(n) {
			var t = this;

			t.node = t.node.appendChild(t.doc.createElement(n));
		},

		writeAttribute : function(n, v) {
			// Since Opera doesn't escape > into &gt; we need to do it our self
			if (tinymce.isOpera)
				v = v.replace(/>/g, '|>');

			this.node.setAttribute(n, v);
		},

		writeEndElement : function() {
			this.node = this.node.parentNode;
		},

		writeFullEndElement : function() {
			var t = this, n = t.node;

			n.appendChild(t.doc.createTextNode(""));
			t.node = n.parentNode;
		},

		writeText : function(v) {
			// Since Opera doesn't escape > into &gt; we need to do it our self
			if (tinymce.isOpera)
				v = v.replace(/>/g, '|>');

			this.node.appendChild(this.doc.createTextNode(v));
		},

		writeCDATA : function(v) {
			this.node.appendChild(this.doc.createCDATA(v));
		},

		writeComment : function(v) {
			this.node.appendChild(this.doc.createComment(v));
		},

		getContent : function() {
			var h;

			h = this.doc.xml || new XMLSerializer().serializeToString(this.doc);
			h = h.replace(/<\?[^?]+\?>|<html>|<\/html>|<html\/>|<!DOCTYPE[^>]+>/g, '');
			h = h.replace(/ ?\/>/g, ' />');

			// Since Opera doesn't escape > into &gt; we need to do it our self to normalize the output for all browsers
			if (tinymce.isOpera)
				h = h.replace(/\|>/g, '&gt;');

			return h;
		}

		});
})();

/* file:jscripts/tiny_mce/classes/dom/Serializer.js */

(function() {
	// Shorten names
	var extend = tinymce.extend, each = tinymce.each, Dispatcher = tinymce.util.Dispatcher, isIE = tinymce.isIE;

	// Returns only attribites that have values not all attributes in IE
	function getIEAtts(n) {
		var o = [];

		// Object will throw exception in IE
		if (n.nodeName == 'OBJECT')
			return n.attributes;

		n.cloneNode(false).outerHTML.replace(/([a-z0-9\-_]+)=/gi, function(a, b) {
			o.push({specified : 1, nodeName : b});
		});

		return o;
	};

	function wildcardToRE(s) {
		return s.replace(/([?+*])/g, '.$1');
	};

	tinymce.create('tinymce.dom.Serializer', {
		Serializer : function(s) {
			var t = this;

			t.key = 0;
			t.onPreProcess = new Dispatcher(t);
			t.onPostProcess = new Dispatcher(t);
			t.writer = new tinymce.dom.XMLWriter();

			// Default settings
			t.settings = s = extend({
				dom : tinymce.DOM,
				valid_nodes : 0,
				node_filter : 0,
				attr_filter : 0,
				invalid_attrs : /^(mce_|_moz_$)/,
				closed : /(br|hr|input|meta|img|link|param)/,
				entity_encoding : 'named',
				entities : '160,nbsp,161,iexcl,162,cent,163,pound,164,curren,165,yen,166,brvbar,167,sect,168,uml,169,copy,170,ordf,171,laquo,172,not,173,shy,174,reg,175,macr,176,deg,177,plusmn,178,sup2,179,sup3,180,acute,181,micro,182,para,183,middot,184,cedil,185,sup1,186,ordm,187,raquo,188,frac14,189,frac12,190,frac34,191,iquest,192,Agrave,193,Aacute,194,Acirc,195,Atilde,196,Auml,197,Aring,198,AElig,199,Ccedil,200,Egrave,201,Eacute,202,Ecirc,203,Euml,204,Igrave,205,Iacute,206,Icirc,207,Iuml,208,ETH,209,Ntilde,210,Ograve,211,Oacute,212,Ocirc,213,Otilde,214,Ouml,215,times,216,Oslash,217,Ugrave,218,Uacute,219,Ucirc,220,Uuml,221,Yacute,222,THORN,223,szlig,224,agrave,225,aacute,226,acirc,227,atilde,228,auml,229,aring,230,aelig,231,ccedil,232,egrave,233,eacute,234,ecirc,235,euml,236,igrave,237,iacute,238,icirc,239,iuml,240,eth,241,ntilde,242,ograve,243,oacute,244,ocirc,245,otilde,246,ouml,247,divide,248,oslash,249,ugrave,250,uacute,251,ucirc,252,uuml,253,yacute,254,thorn,255,yuml,402,fnof,913,Alpha,914,Beta,915,Gamma,916,Delta,917,Epsilon,918,Zeta,919,Eta,920,Theta,921,Iota,922,Kappa,923,Lambda,924,Mu,925,Nu,926,Xi,927,Omicron,928,Pi,929,Rho,931,Sigma,932,Tau,933,Upsilon,934,Phi,935,Chi,936,Psi,937,Omega,945,alpha,946,beta,947,gamma,948,delta,949,epsilon,950,zeta,951,eta,952,theta,953,iota,954,kappa,955,lambda,956,mu,957,nu,958,xi,959,omicron,960,pi,961,rho,962,sigmaf,963,sigma,964,tau,965,upsilon,966,phi,967,chi,968,psi,969,omega,977,thetasym,978,upsih,982,piv,8226,bull,8230,hellip,8242,prime,8243,Prime,8254,oline,8260,frasl,8472,weierp,8465,image,8476,real,8482,trade,8501,alefsym,8592,larr,8593,uarr,8594,rarr,8595,darr,8596,harr,8629,crarr,8656,lArr,8657,uArr,8658,rArr,8659,dArr,8660,hArr,8704,forall,8706,part,8707,exist,8709,empty,8711,nabla,8712,isin,8713,notin,8715,ni,8719,prod,8721,sum,8722,minus,8727,lowast,8730,radic,8733,prop,8734,infin,8736,ang,8743,and,8744,or,8745,cap,8746,cup,8747,int,8756,there4,8764,sim,8773,cong,8776,asymp,8800,ne,8801,equiv,8804,le,8805,ge,8834,sub,8835,sup,8836,nsub,8838,sube,8839,supe,8853,oplus,8855,otimes,8869,perp,8901,sdot,8968,lceil,8969,rceil,8970,lfloor,8971,rfloor,9001,lang,9002,rang,9674,loz,9824,spades,9827,clubs,9829,hearts,9830,diams,338,OElig,339,oelig,352,Scaron,353,scaron,376,Yuml,710,circ,732,tilde,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm,8211,ndash,8212,mdash,8216,lsquo,8217,rsquo,8218,sbquo,8220,ldquo,8221,rdquo,8222,bdquo,8224,dagger,8225,Dagger,8240,permil,8249,lsaquo,8250,rsaquo,8364,euro',
				valid_elements : '*[*]',
				extended_valid_elements : 0,
				valid_child_elements : 0,
				invalid_elements : 0,
				fix_table_elements : 0,
				fix_list_elements : true,
				fix_content_duplication : true,
				convert_fonts_to_spans : false,
				font_size_classes : 0,
				font_size_style_values : 0,
				apply_source_formatting : 0,
				indent_mode : 'simple',
				indent_char : '\t',
				indent_levels : 1,
				remove_linebreaks : 1
			}, s);

			t.dom = s.dom;

			if (s.fix_list_elements) {
				t.onPreProcess.add(function(se, o) {
					var nl, x, a = ['ol', 'ul'], i, n, p, r = /^(OL|UL)$/, np;

					function prevNode(e, n) {
						var a = n.split(','), i;

						while ((e = e.previousSibling) != null) {
							for (i=0; i<a.length; i++) {
								if (e.nodeName == a[i])
									return e;
							}
						}

						return null;
					};

					for (x=0; x<a.length; x++) {
						nl = t.dom.select(a[x], o.node);

						for (i=0; i<nl.length; i++) {
							n = nl[i];
							p = n.parentNode;

							if (r.test(p.nodeName)) {
								np = prevNode(n, 'LI');

								if (!np) {
									np = t.dom.create('li');
									np.innerHTML = '&nbsp;';
									np.appendChild(n);
									p.insertBefore(np, p.firstChild);
								} else
									np.appendChild(n);
							}
						}
					}
				});
			}

			if (s.fix_table_elements) {
				t.onPreProcess.add(function(se, o) {
					var ta = [], d = t.dom.doc;

					// Build list of HTML chunks and replace tables with comment placeholders
					each(t.dom.select('table', o.node), function(e) {
						var pa = t.dom.getParent(e, 'H1,H2,H3,H4,H5,H6,P'), p = [], i, h;

						if (pa) {
							t.dom.getParent(e, function(n) {
								if (n != e)
									p.push(n.nodeName);
							});

							h = '';

							for (i = 0; i < p.length; i++)
								h += '</' + p[i]+ '>';

							h += t.dom.getOuterHTML(e);

							for (i = p.length - 1; i >= 0; i--)
								h += '<' + p[i]+ '>';

							ta.push(h);
							e.parentNode.replaceChild(d.createComment('mcetable:' + (ta.length - 1)), e);
						}
					});

					// Replace table placeholders with end parents + table + start parents HTML code
					t.dom.setHTML(o.node, o.node.innerHTML.replace(/<!--mcetable:([0-9]+)-->/g, function(a, b) {
						return ta[parseInt(b)];
					}));
				});
			}
		},

		setEntities : function(s) {
			var a, i, l = {}, re = '', v;

			// No need to setup more than once
			if (this.entityLookup)
				return;

			// Build regex and lookup array
			a = s.split(',');
			for (i = 0; i < a.length; i += 2) {
				v = a[i];

				// Don't add default &amp; &quot; etc.
				if (v == 34 || v == 38 || v == 60 || v == 62)
					continue;

				l[String.fromCharCode(a[i])] = a[i + 1];

				v = parseInt(a[i]).toString(16);
				re += '\\u' + '0000'.substring(v.length) + v;
			}

			this.entitiesRE = new RegExp('[' + re + ']', 'g');
			this.entityLookup = l;
		},

		setValidChildRules : function(s) {
			this.childRules = null;
			this.addValidChildRules(s);
		},

		addValidChildRules : function(s) {
			var t = this, inst, intr, bloc;

			if (!s)
				return;

			inst = 'A|BR|SPAN|BDO|MAP|OBJECT|IMG|TT|I|B|BIG|SMALL|EM|STRONG|DFN|CODE|Q|SAMP|KBD|VAR|CITE|ABBR|ACRONYM|SUB|SUP|#text|#comment';
			intr = 'A|BR|SPAN|BDO|OBJECT|APPLET|IMG|MAP|IFRAME|TT|I|B|U|S|STRIKE|BIG|SMALL|FONT|BASEFONT|EM|STRONG|DFN|CODE|Q|SAMP|KBD|VAR|CITE|ABBR|ACRONYM|SUB|SUP|INPUT|SELECT|TEXTAREA|LABEL|BUTTON|#text|#comment';
			bloc = 'H[1-6]|P|DIV|ADDRESS|PRE|FORM|TABLE|LI|OL|UL|TD|CAPTION|BLOCKQUOTE|CENTER|DL|DT|DD|DIR|FIELDSET|FORM|NOSCRIPT|NOFRAMES|MENU|ISINDEX|SAMP';

			each(s.split(','), function(s) {
				var p = s.split(/\[|\]/), re;

				s = '';
				each(p[1].split('|'), function(v) {
					if (s)
						s += '|';

					switch (v) {
						case '%itrans':
							v = intr;
							break;

						case '%itrans_na':
							v = intr.substring(2);
							break;

						case '%istrict':
							v = inst;
							break;

						case '%istrict_na':
							v = inst.substring(2);
							break;

						case '%btrans':
							v = bloc;
							break;

						case '%bstrict':
							v = bloc;
							break;
					}

					s += v;
				});
				re = new RegExp('^(' + s.toLowerCase() + ')$', 'i');

				each(p[0].split('/'), function(s) {
					t.childRules = t.childRules || {};
					t.childRules[s] = re;
				});
			});

			// Build regex
			s = '';
			each(t.childRules, function(v, k) {
				if (s)
					s += '|';

				s += k;
			});

			t.parentElementsRE = new RegExp('^(' + s.toLowerCase() + ')$', 'i');

			/*console.debug(t.parentElementsRE.toString());
			each(t.childRules, function(v) {
				console.debug(v.toString());
			});*/
		},

		setRules : function(s) {
			var t = this;

			t._setup();
			t.rules = {};
			t.wildRules = [];
			t.validElements = {};

			return t.addRules(s);
		},

		addRules : function(s) {
			var t = this, dr;

			if (!s)
				return;

			t._setup();

			each(s.split(','), function(s) {
				var p = s.split(/\[|\]/), tn = p[0].split('/'), ra, at, wat, va = [];

				// Extend with default rules
				if (dr)
					at = tinymce.extend([], dr.attribs);

				// Parse attributes
				if (p.length > 1) {
					each(p[1].split('|'), function(s) {
						var ar = {}, i;

						at = at || [];

						// Parse attribute rule
						s = s.replace(/::/g, '~');
						s = /^([!\-])?([\w*.?~]+|)([=:<])?(.+)?$/.exec(s);
						s[2] = s[2].replace(/~/g, ':');

						// Add required attributes
						if (s[1] == '!') {
							ra = ra || [];
							ra.push(s[2]);
						}

						// Remove inherited attributes
						if (s[1] == '-') {
							for (i = 0; i <at.length; i++) {
								if (at[i].name == s[2]) {
									at.splice(i, 1);
									return;
								}
							}
						}

						switch (s[3]) {
							// Add default attrib values
							case '=':
								ar.defaultVal = s[4] || '';
								break;

							// Add forced attrib values
							case ':':
								ar.forcedVal = s[4];
								break;

							// Add validation values
							case '<':
								ar.validVals = s[4].split('?');
								break;
						}

						if (/[*.?]/.test(s[2])) {
							wat = wat || [];
							ar.nameRE = new RegExp('^' + wildcardToRE(s[2]) + '$');
							wat.push(ar);
						} else {
							ar.name = s[2];
							at.push(ar);
						}

						va.push(s[2]);
					});
				}

				// Handle element names
				each(tn, function(s, i) {
					var pr = s.charAt(0), x = 1, ru = {};

					// Extend with default rule data
					if (dr) {
						if (dr.noEmpty)
							ru.noEmpty = dr.noEmpty;

						if (dr.fullEnd)
							ru.fullEnd = dr.fullEnd;

						if (dr.padd)
							ru.padd = dr.padd;
					}

					// Handle prefixes
					switch (pr) {
						case '-':
							ru.noEmpty = true;
							break;

						case '+':
							ru.fullEnd = true;
							break;

						case '#':
							ru.padd = true;
							break;

						default:
							x = 0;
					}

					tn[i] = s = s.substring(x);
					t.validElements[s] = 1;

					// Add element name or element regex
					if (/[*.?]/.test(tn[0])) {
						ru.nameRE = new RegExp('^' + wildcardToRE(tn[0]) + '$');
						t.wildRules = t.wildRules || {};
						t.wildRules.push(ru);
					} else {
						ru.name = tn[0];

						// Store away default rule
						if (tn[0] == '@')
							dr = ru;

						t.rules[s] = ru;
					}

					ru.attribs = at;

					if (ra)
						ru.requiredAttribs = ra;

					if (wat) {
						// Build valid attributes regexp
						s = '';
						each(va, function(v) {
							if (s)
								s += '|';

							s += '(' + wildcardToRE(v) + ')';
						});
						ru.validAttribsRE = new RegExp('^' + s.toLowerCase() + '$');
						ru.wildAttribs = wat;
					}
				});
			});

			// Build valid elements regexp
			s = '';
			each(t.validElements, function(v, k) {
				if (s)
					s += '|';

				if (k != '@')
				s += k;
			});
			t.validElementsRE = new RegExp('^(' + wildcardToRE(s.toLowerCase()) + ')$');

			//console.debug(t.validElementsRE.toString());
			//console.dir(t.rules);
			//console.dir(t.wildRules);
		},

		findRule : function(n) {
			var t = this, rl = t.rules, i, r;

			t._setup();

			// Exact match
			r = rl[n];
			if (r)
				return r;

			// Try wildcards
			rl = t.wildRules;
			for (i = 0; i < rl.length; i++) {
				if (rl[i].nameRE.test(n))
					return rl[i];
			}

			return null;
		},

		findAttribRule : function(ru, n) {
			var i, wa = ru.wildAttribs;

			for (i = 0; i < wa.length; i++) {
				if (wa[i].nameRE.test(n))
					return wa[i];
			}

			return null;
		},

		serialize : function(n, o) {
			var h, t = this;

			t._setup();
			o = o || {};
			o.format = o.format || 'html';
			t.processObj = o;
			n = n.cloneNode(true);
			t.key = '' + (parseInt(t.key) + 1);

			// Pre process
			if (!o.no_events) {
				o.node = n;
				t.onPreProcess.dispatch(t, o);
			}

			// Serialize HTML DOM into a string
			t.writer.reset();
			t._serializeNode(n, o.getInner);

			// Post process
			o.content = t.writer.getContent();

			if (!o.no_events)
				t.onPostProcess.dispatch(t, o);

			t._postProcess(o);
			o.node = null;

			return tinymce.trim(o.content);
		},

		// Internal functions

		_postProcess : function(o) {
			var t = this, s = t.settings, h = o.content, sc = [], p, l;

			if (o.format == 'html') {
				// Protect some elements
				p = t._protect({
					content : h,
					patterns : [
						/(<script[^>]*>)(.*?)(<\/script>)/g,
						/(<style[^>]*>)(.*?)(<\/style>)/g,
						/(<pre[^>]*>)(.*?)(<\/pre>)/g
					]
				});

				h = p.content;

				// Entity encode
				if (s.entity_encoding !== 'raw') {
					if (s.entity_encoding.indexOf('named') != -1) {
						t.setEntities(s.entities);
						l = t.entityLookup;

						h = h.replace(t.entitiesRE, function(a) {
							var v;

							if (v = l[a])
								a = '&' + v + ';';

							return a;
						});
					}

					if (s.entity_encoding.indexOf('numeric') != -1) {
						h = h.replace(/[\u007E-\uFFFF]/g, function(a) {
							return '&#' + a.charCodeAt(0) + ';';
						});
					}
				}

				// Use BR instead of &nbsp; padded P elements inside editor and use <p>&nbsp;</p> outside editor
				if (o.set)
					h = h.replace(/<p>\s+(&nbsp;|&#160;|\u00a0|<br \/>)\s+<\/p>/g, '<p><br /></p>');
				else
					h = h.replace(/<p>\s+(&nbsp;|&#160;|\u00a0|<br \/>)\s+<\/p>/g, '<p>$1</p>');

				// Since Gecko and Safari keeps whitespace in the DOM we need to
				// remove it inorder to match other browsers. But I think Gecko and Safari is right.
				// This process is only done when getting contents out from the editor.
				if (!o.set) {
					if (s.remove_linebreaks) {
						h = h.replace(/(<[^>]+>)\s+/g, '$1 ');
						h = h.replace(/\s+(<\/[^>]+>)/g, ' $1');
						h = h.replace(/<(p|h[1-6]|hr|div|table|tbody|tr|td|body|head|html|title|meta|style|pre|script|link|object) ([^>]+)>\s+/g, '<$1 $2>'); // Trim block start
						h = h.replace(/<(p|h[1-6]|hr|div|table|tbody|tr|td|body|head|html|title|meta|style|pre|script|link|object)>\s+/g, '<$1>'); // Trim block start
						h = h.replace(/\s+<\/(p|h[1-6]|hr|div|table|tbody|tr|td|body|head|html|title|meta|style|pre|script|link|object)>/g, '</$1>'); // Trim block end
					}

					// Simple indentation
					if (s.apply_source_formatting && s.indent_mode == 'simple') {
						// Add line breaks before and after block elements
						h = h.replace(/<(\/?)(ul|hr|table|meta|link|tbody|tr|object|body|head|html)(|[^>]+)>\s*/g, '\n<$1$2$3>\n');
						h = h.replace(/\s*<(p|h[1-6]|div|title|style|pre|script|td|li)(|[^>]+)>/g, '\n<$1$2>');
						h = h.replace(/<\/(p|h[1-6]|div|title|style|pre|script|td|li)>\s*/g, '</$1>\n');
						h = h.replace(/\n\n/g, '\n');
					}
				}

				h = t._unprotect(h, p);
			}

			o.content = h;
		},

		_serializeNode : function(n, inn) {
			var t = this, s = t.settings, w = t.writer, hc, el, cn, i, l, a, at, no, v, nn, ru, ar, iv;

			if (!s.node_filter || s.node_filter(n)) {
				switch (n.nodeType) {
					case 1: // Element
						if (n.hasAttribute ? n.hasAttribute('mce_bogus') : n.getAttribute('mce_bogus'))
							return;

						iv = false;
						hc = n.hasChildNodes();
						nn = n.getAttribute('mce_name') || n.nodeName.toLowerCase();

						// Add correct prefix on IE
						if (isIE) {
							if (n.scopeName !== 'HTML' && n.scopeName !== 'html')
								nn = n.scopeName + ':' + nn;
						}

						// Remove mce prefix on IE needed for the abbr element
						if (nn.indexOf('mce:') === 0)
							nn = nn.substring(4);

						// Check if valid
						if (!t.validElementsRE.test(nn) || (t.invalidElementsRE && t.invalidElementsRE.test(nn)) || inn) {
							iv = true;
							break;
						}

						if (isIE) {
							// Fix IE content duplication (DOM can have multiple copies of the same node)
							if (s.fix_content_duplication) {
								if (n.mce_serialized == t.key)
									return;

								n.mce_serialized = t.key;
							}

							// IE sometimes adds a / infront of the node name
							if (nn.charAt(0) == '/')
								nn = nn.substring(1);
						}

						// Check if valid child
						if (t.childRules) {
							if (t.parentElementsRE.test(t.elementName)) {
								if (!t.childRules[t.elementName].test(nn)) {
									iv = true;
									break;
								}
							}

							t.elementName = nn;
						}

						ru = t.findRule(nn);
						nn = ru.name || nn;

						// Skip empty nodes or empty node name in IE
						if ((!hc && ru.noEmpty) || (isIE && !nn)) {
							iv = true;
							break;
						}

						// Check required
						if (ru.requiredAttribs) {
							a = ru.requiredAttribs;

							for (i = a.length - 1; i >= 0; i--) {
								if (this.dom.getAttrib(n, a[i]) !== '')
									break;
							}

							// None of the required was there
							if (i == -1) {
								iv = true;
								break;
							}
						}

						w.writeStartElement(nn);

						// Add ordered attributes
						if (ru.attribs) {
							for (i=0, at = ru.attribs, l = at.length; i<l; i++) {
								a = at[i];
								v = t._getAttrib(n, a);

								if (v !== null)
									w.writeAttribute(a.name, v);
							}
						}

						// Add wild attributes
						if (ru.validAttribsRE) {
							at = isIE ? getIEAtts(n) : n.attributes;
							for (i=at.length-1; i>-1; i--) {
								no = at[i];

								if (no.specified) {
									a = no.nodeName.toLowerCase();

									if (s.invalid_attrs.test(a) || !ru.validAttribsRE.test(a))
										continue;

									ar = t.findAttribRule(ru, a);
									v = t._getAttrib(n, ar, a);

									if (v !== null)
										w.writeAttribute(a, v);
								}
							}
						}

						// Padd empty nodes with a &nbsp;
						if (!hc && ru.padd)
							w.writeText('\u00a0');

						break;

					case 3: // Text
						// Check if valid child
						if (t.childRules && t.parentElementsRE.test(t.elementName)) {
							if (!t.childRules[t.elementName].test(n.nodeName))
								return;
						}

						return w.writeText(n.nodeValue);

					case 4: // CDATA
						return w.writeCDATA(n.nodeValue);

					case 8: // Comment
						return w.writeComment(n.nodeValue);
				}
			} else if (n.nodeType == 1)
				hc = n.hasChildNodes();

			if (hc) {
				cn = n.firstChild;

				while (cn) {
					t._serializeNode(cn);
					t.elementName = nn;
					cn = cn.nextSibling;
				}
			}

			// Write element end
			if (!iv) {
				if (hc || !s.closed.test(nn))
					w.writeFullEndElement();
				else
					w.writeEndElement();
			}
		},

		_protect : function(o) {
			o.items = o.items || [];

			function enc(s) {
				return s.replace(/[\r\n]/g, function(c) {
					if (c === '\n')
						return '\\n';

					return '\\r';
				});
			};

			function dec(s) {
				return s.replace(/\\[rn]/g, function(c) {
					if (c === '\\n')
						return '\n';

					return '\r';
				});
			};

			each(o.patterns, function(p) {
				o.content = dec(enc(o.content).replace(p, function(x, a, b, c) {
					o.items.push(dec(b));
					return a + '<!--mce:' + (o.items.length - 1) + '-->' + c;
				}));
			});

			return o;
		},

		_unprotect : function(h, o) {
			h = h.replace(/\<!--mce:([0-9]+)--\>/g, function(a, b) {
				return o.items[parseInt(b)];
			});

			o.items = [];

			return h;
		},

		_setup : function() {
			var t = this, s = this.settings;

			if (t.done)
				return;

			t.done = 1;

			t.setRules(s.valid_elements);
			t.addRules(s.extended_valid_elements);
			t.addValidChildRules(s.valid_child_elements);

			if (s.invalid_elements)
				t.invalidElementsRE = new RegExp('^(' + wildcardToRE(s.invalid_elements.replace(',', '|').toLowerCase()) + ')$');

			if (s.attrib_value_filter)
				t.attribValueFilter = s.attribValueFilter;
		},

		_getAttrib : function(n, a, na) {
			var i, v;

			na = na || a.name;

			if (a.forcedVal && (v = a.forcedVal)) {
				if (v === '{$uid}')
					return this.dom.uniqueId();

				return v;
			}

			v = this.dom.getAttrib(n, na);

			switch (na) {
				case 'rowspan':
				case 'colspan':
					// Whats the point? Remove usless attribute value
					if (v == '1')
						v = '';

					break;
			}

			if (this.attribValueFilter)
				v = this.attribValueFilter(na, v, n);

			if (a.validVals) {
				for (i = a.validVals.length - 1; i >= 0; i--) {
					if (v == a.validVals[i])
						break;
				}

				if (i == -1)
					return null;
			}

			if (v === '' && typeof(a.defaultVal) != 'undefined') {
				v = a.defaultVal;

				if (v === '{$uid}')
					return this.dom.uniqueId();

				return v;
			} else {
				// Remove internal mceItemXX classes when content is extracted from editor
				if (na == 'class' && this.processObj.get)
					v = v.replace(/\bmceItem\w+\b/g, '');
			}

			if (v === '')
				return null;


			return v;
		}

		});
})();

/* file:jscripts/tiny_mce/classes/dom/ScriptLoader.js */

(function() {
	var each = tinymce.each;

	tinymce.create('tinymce.dom.ScriptLoader', {
		ScriptLoader : function(s) {
			this.settings = s || {};
			this.queue = [];
			this.lookup = {};
		},

		markDone : function(u) {
			this.lookup[u] = {state : 2, url : u};
		},

		add : function(u, cb, s, pr) {
			var t = this, lo = t.lookup, o;

			if (o = lo[u]) {
				// Is loaded fire callback
				if (cb && o.state == 2)
					cb.call(s || this);

				return o;
			}

			o = {state : 0, url : u, func : cb, scope : s || this};

			if (pr)
				t.queue.unshift(o);
			else
				t.queue.push(o);

			lo[u] = o;

			return o;
		},

		load : function(u, cb, s) {
			var o;

			if (!tinymce.is(u, 'string')) {
				o = [];

				each(u, function(u) {
					o.push({state : 0, url : u});
				});

				this.loadScripts(o, cb, s);
			} else
				this.loadScripts([{state : 0, url : u}], cb, s);
		},

		loadQueue : function(cb, s) {
			var t = this;

			if (!t.queueLoading) {
				t.queueLoading = 1;
				t.queueCallbacks = [];

				t.loadScripts(t.queue, function() {
					t.queueLoading = 0;

					if (cb)
						cb.call(s || t);

					each(t.queueCallbacks, function(o) {
						o.func.call(o.scope);
					});
				});
			} else if (cb)
				t.queueCallbacks.push({func : cb, scope : s || t});
		},

		eval : function(co) {
			var w = window;

			// Evaluate script
			if (!w.execScript) {
				try {
					eval.call(w, co);
				} catch (ex) {
					eval(co, w); // Firefox 3.0a8
				}
			} else
				w.execScript(co); // IE
		},

		loadScripts : function(sc, cb, s) {
			var t = this, lo = t.lookup;

			function done(o) {
				o.state = 2; // Has been loaded

				// Run callback
				if (o.func)
					o.func.call(o.scope || t);
			};

			function allDone() {
				var l;

				// Check if all files are loaded
				l = sc.length;
				each(sc, function(o) {
					o = lo[o.url];

					if (o.state === 2) {// It has finished loading
						done(o);
						l--;
					} else
						load(o);
				});

				// They are all loaded
				if (l === 0 && cb) {
					cb.call(s || t);
					cb = 0;
				}
			};

			function load(o) {
				if (o.state > 0)
					return;

				o.state = 1; // Is loading

				tinymce.util.XHR.send({
					url : o.url,
					error : t.settings.error,
					success : function(co) {
						t.eval(co);
						done(o);
						allDone();
					}
				});
			};

			each(sc, function(o) {
				var u = o.url;

				// Add to queue if needed
				if (!lo[u]) {
					lo[u] = o;
					t.queue.push(o);
				} else
					o = lo[u];

				// Is already loading or has been loaded
				if (o.state > 0)
					return;

				if (!tinymce.dom.Event.domLoaded && !t.settings.strict_mode) {
					var ix, ol = '';

					// Add onload events
					if (cb || o.func) {
						o.state = 1; // Is loading

						ix = tinymce.dom.ScriptLoader._addOnLoad(function() {
							done(o);
							allDone();
						});

						if (tinymce.isIE)
							ol = ' onreadystatechange="';
						else
							ol = ' onload="';

						ol += 'tinymce.dom.ScriptLoader._onLoad(this,\'' + u + '\',' + ix + ');"';
					}

					document.write('<script type="text/javascript" src="' + u + '"' + ol + '></script>');

					if (!o.func)
						done(o);
				} else
					load(o);
			});

			allDone();
		},

		// Static methods
		'static' : {
			_addOnLoad : function(f) {
				var t = this;

				t._funcs = t._funcs || [];
				t._funcs.push(f);

				return t._funcs.length - 1;
			},

			_onLoad : function(e, u, ix) {
				if (!tinymce.isIE || e.readyState == 'complete')
					this._funcs[ix].call(this);
			}
		}

		});

	// Global script loader
	tinymce.ScriptLoader = new tinymce.dom.ScriptLoader();
})();

/* file:jscripts/tiny_mce/classes/ui/Control.js */

(function() {
	// Shorten class names
	var DOM = tinymce.DOM, is = tinymce.is;

	tinymce.create('tinymce.ui.Control', {
		Control : function(id, s) {
			this.id = id;
			this.settings = s = s || {};
			this.rendered = false;
			this.onRender = new tinymce.util.Dispatcher(this);
			this.classPrefix = '';
			this.scope = s.scope || this;
			this.disabled = 0;
			this.active = 0;
		},

		setDisabled : function(s) {
			var e;

			if (s != this.disabled) {
				e = DOM.get(this.id);

				// Add accessibility title for unavailable actions
				if (e && this.settings.unavailable_prefix) {
					if (s) {
						this.prevTitle = e.title;
						e.title = this.settings.unavailable_prefix + ": " + e.title;
					} else
						e.title = this.prevTitle;
				}

				this.setState('Disabled', s);
				this.setState('Enabled', !s);
				this.disabled = s;
			}
		},

		isDisabled : function() {
			return this.disabled;
		},

		setActive : function(s) {
			if (s != this.active) {
				this.setState('Active', s);
				this.active = s;
			}
		},

		isActive : function() {
			return this.active;
		},

		setState : function(c, s) {
			var n = DOM.get(this.id);

			c = this.classPrefix + c;

			if (s)
				DOM.addClass(n, c);
			else
				DOM.removeClass(n, c);
		},

		isRendered : function() {
			return this.rendered;
		},

		renderHTML : function() {
		},

		renderTo : function(n) {
			n.innerHTML = this.renderHTML();
		},

		postRender : function() {
			var t = this, b;

			// Set pending states
			if (is(t.disabled)) {
				b = t.disabled;
				t.disabled = -1;
				t.setDisabled(b);
			}

			if (is(t.active)) {
				b = t.active;
				t.active = -1;
				t.setActive(b);
			}
		},

		destroy : function() {
			DOM.remove(this.id);
		}

		});
})();
/* file:jscripts/tiny_mce/classes/ui/Container.js */

tinymce.create('tinymce.ui.Container:tinymce.ui.Control', {
	Container : function(id, s) {
		this.parent(id, s);
		this.controls = [];
		this.lookup = {};
	},

	add : function(c) {
		this.lookup[c.id] = c;
		this.controls.push(c);

		return c;
	},

	get : function(n) {
		return this.lookup[n];
	}

	});


/* file:jscripts/tiny_mce/classes/ui/Separator.js */

tinymce.create('tinymce.ui.Separator:tinymce.ui.Control', {
	renderHTML : function() {
		return tinymce.DOM.createHTML('span', {'class' : 'mceSeparator'});
	}

	});

/* file:jscripts/tiny_mce/classes/ui/MenuItem.js */

(function() {
	var is = tinymce.is, DOM = tinymce.DOM, each = tinymce.each, walk = tinymce.walk;

	tinymce.create('tinymce.ui.MenuItem:tinymce.ui.Control', {
		MenuItem : function(id, s) {
			this.parent(id, s);
			this.classPrefix = 'mceMenuItem';
		},

		setSelected : function(s) {
			this.setState('Selected', s);
			this.selected = s;
		},

		isSelected : function() {
			return this.selected;
		},

		postRender : function() {
			var t = this;
			
			t.parent();

			// Set pending state
			if (is(t.selected))
				t.setSelected(t.selected);
		}

		});
})();

/* file:jscripts/tiny_mce/classes/ui/Menu.js */

(function() {
	var is = tinymce.is, DOM = tinymce.DOM, each = tinymce.each, walk = tinymce.walk;

	tinymce.create('tinymce.ui.Menu:tinymce.ui.MenuItem', {
		Menu : function(id, s) {
			var t = this;

			t.parent(id, s);
			t.items = {};
			t.collapsed = false;
			t.menuCount = 0;
			t.onAddItem = new tinymce.util.Dispatcher(this);
		},

		expand : function(d) {
			var t = this;

			if (d) {
				walk(t, function(o) {
					if (o.expand)
						o.expand();
				}, 'items', t);
			}

			t.collapsed = false;
		},

		collapse : function(d) {
			var t = this;

			if (d) {
				walk(t, function(o) {
					if (o.collapse)
						o.collapse();
				}, 'items', t);
			}

			t.collapsed = true;
		},

		isCollapsed : function() {
			return this.collapsed;
		},

		add : function(o) {
			if (!o.settings)
				o = new tinymce.ui.MenuItem(o.id || DOM.uniqueId(), o);

			this.onAddItem.dispatch(this, o);

			return this.items[o.id] = o;
		},

		addSeparator : function() {
			return this.add({separator : true});
		},

		addMenu : function(o) {
			if (!o.collapse)
				o = this.createMenu(o);

			this.menuCount++;

			return this.add(o);
		},

		hasMenus : function() {
			return this.menuCount !== 0;
		},

		remove : function(o) {
			delete this.items[o.id];
		},

		removeAll : function() {
			var t = this;

			walk(t, function(o) {
				if (o.removeAll)
					o.removeAll();

				o.destroy();
			}, 'items', t);

			t.items = {};
		},

		createMenu : function(o) {
			var m = new tinymce.ui.Menu(o.id || DOM.uniqueId(), o);

			m.onAddItem.add(this.onAddItem.dispatch, this.onAddItem);

			return m;
		}

		});
})();
/* file:jscripts/tiny_mce/classes/ui/DropMenu.js */

(function() {
	var is = tinymce.is, DOM = tinymce.DOM, each = tinymce.each, Event = tinymce.dom.Event, Element = tinymce.dom.Element;

	tinymce.create('tinymce.ui.DropMenu:tinymce.ui.Menu', {
		DropMenu : function(id, s) {
			s = s || {};
			s.container = s.container || document.body;
			s.offset_x = s.offset_x || 0;
			s.offset_y = s.offset_y || 0;
			s.vp_offset_x = s.vp_offset_x || 0;
			s.vp_offset_y = s.vp_offset_y || 0;
			this.parent(id, s);
			this.onHideMenu = new tinymce.util.Dispatcher(this);
			this.classPrefix = 'mceMenu';
		},

		createMenu : function(s) {
			var t = this, cs = t.settings, m;

			s.container = s.container || cs.container;
			s.parent = t;
			s.constrain = s.constrain || cs.constrain;
			s['class'] = s['class'] || cs['class'];
			s.vp_offset_x = s.vp_offset_x || cs.vp_offset_x;
			s.vp_offset_y = s.vp_offset_y || cs.vp_offset_y;
			m = new tinymce.ui.DropMenu(s.id || DOM.uniqueId(), s);

			m.onAddItem.add(t.onAddItem.dispatch, t.onAddItem);

			return m;
		},

		update : function() {
			var t = this, s = t.settings, tb = DOM.get('menu_' + t.id + '_tbl'), co = DOM.get('menu_' + t.id + '_co'), tw, th;

			tw = s.max_width ? Math.min(tb.clientWidth, s.max_width) : tb.clientWidth;
			th = s.max_height ? Math.min(tb.clientHeight, s.max_height) : tb.clientHeight;

			if (!DOM.boxModel)
				t.element.setStyles({width : tw + 2, height : th + 2});
			else
				t.element.setStyles({width : tw, height : th});

			if (s.max_width)
				DOM.setStyle(co, 'width', tw);

			if (s.max_height) {
				DOM.setStyle(co, 'height', th);

				if (tb.clientHeight < s.max_height)
					DOM.setStyle(co, 'overflow', 'hidden');
			}
		},

		showMenu : function(x, y, px) {
			var t = this, s = t.settings, co, vp = DOM.getViewPort(), w, h, mx, my, ot = 2, dm, tb;

			t.collapse(1);

			if (t.isMenuVisible)
				return;

			if (!t.rendered) {
				co = DOM.add(t.settings.container, t.renderNode());

				each(t.items, function(o) {
					o.postRender();
				});

				t.element = new Element('menu_' + t.id, {blocker : 1, container : s.container});
			} else
				co = DOM.get('menu_' + t.id);

			DOM.setStyles(co, {left : -0xFFFF , top : -0xFFFF});
			DOM.show(co);
			t.update();

			x += s.offset_x || 0;
			y += s.offset_y || 0;
			vp.w -= 4;
			vp.h -= 4;

			// Move inside viewport if not submenu
			if (s.constrain) {
				w = co.clientWidth - ot;
				h = co.clientHeight - ot;
				mx = vp.x + vp.w;
				my = vp.y + vp.h;

				if ((x + s.vp_offset_x + w) > mx)
					x = px ? px - w : Math.max(0, (mx - s.vp_offset_x) - w);

				if ((y + s.vp_offset_y + h) > my)
					y = Math.max(0, (my - s.vp_offset_y) - h);
			}

			DOM.setStyles(co, {left : x , top : y});
			t.element.update();

			t.isMenuVisible = 1;
			t.mouseClickFunc = Event.add(co, 'click', function(e) {
				var m;

				e = e.target;

				if (e && (e = DOM.getParent(e, 'TR')) && !DOM.hasClass(e, 'mceMenuItemSub')) {
					m = t.items[e.id];

					if (m.isDisabled())
						return;

					if (m.settings.onclick)
						m.settings.onclick(e);

					dm = t;

					while (dm) {
						if (dm.hideMenu)
							dm.hideMenu();

						dm = dm.settings.parent;
					}

					return Event.cancel(e); // Cancel to fix onbeforeunload problem
				}
			});

			if (t.hasMenus()) {
				t.mouseOverFunc = Event.add(co, 'mouseover', function(e) {
					var m, r, mi;

					e = e.target;
					if (e && (e = DOM.getParent(e, 'TR'))) {
						m = t.items[e.id];

						if (t.lastMenu)
							t.lastMenu.collapse(1);

						if (m.isDisabled())
							return;

						if (e && DOM.hasClass(e, 'mceMenuItemSub')) {
							//p = DOM.getPos(s.container);
							r = DOM.getRect(e);
							m.showMenu((r.x + r.w - ot), r.y - ot, r.x);
							t.lastMenu = m;
							DOM.addClass(DOM.get(m.id).firstChild, 'mceMenuItemActive');
						}
					}
				});
			}
		},

		hideMenu : function() {
			var t = this, co = DOM.get('menu_' + t.id), e;

			if (!t.isMenuVisible)
				return;

			Event.remove(co, 'mouseover', t.mouseOverFunc);
			Event.remove(co, 'click', t.mouseClickFunc);
			DOM.hide(co);
			t.isMenuVisible = 0;

			if (t.element)
				t.element.hide();

			if (e = DOM.get(t.id))
				DOM.removeClass(e.firstChild, 'mceMenuItemActive');

			t.onHideMenu.dispatch(t);
		},

		add : function(o) {
			var t = this, co;

			o = t.parent(o);

			if (t.isRendered && (co = DOM.get('menu_' + t.id)))
				t._add(DOM.select('tbody', co)[0], o);

			return o;
		},

		collapse : function(d) {
			this.parent(d);
			this.hideMenu();
		},

		remove : function(o) {
			DOM.remove(o.id);

			return this.parent(o);
		},

		destroy : function() {
			var t = this, co = DOM.get('menu_' + t.id);

			Event.remove(co, 'mouseover', t.mouseOverFunc);
			Event.remove(co, 'click', t.mouseClickFunc);

			if (t.element)
				t.element.remove();

			DOM.remove(co);
		},

		renderNode : function() {
			var t = this, s = t.settings, n, tb, co, w;

			w = DOM.create('div', {id : 'menu_' + t.id, 'class' : s['class'], 'style' : 'position:absolute;left:0;top:0;z-index:150'});
			co = DOM.add(w, 'div', {id : 'menu_' + t.id + '_co', 'class' : 'mceMenu' + (s['class'] ? ' ' + s['class'] : '')});
			t.element = new Element('menu_' + t.id, {blocker : 1, container : s.container});

			if (s.menu_line)
				DOM.add(co, 'span', {'class' : 'mceMenuLine'});

//			n = DOM.add(co, 'div', {id : 'menu_' + t.id + '_co', 'class' : 'mceMenuContainer'});
			n = DOM.add(co, 'table', {id : 'menu_' + t.id + '_tbl', border : 0, cellPadding : 0, cellSpacing : 0});
			tb = DOM.add(n, 'tbody');

			each(t.items, function(o) {
				t._add(tb, o);
			});

			t.rendered = true;

			return w;
		},

		// Internal functions

		_add : function(tb, o) {
			var n, s = o.settings, a, ro, it;

			if (s.separator) {
				ro = DOM.add(tb, 'tr', {id : o.id, 'class' : 'mceMenuItemSeparator'});
				DOM.add(ro, 'td', {'class' : 'mceMenuItemSeparator'});

				if (n = ro.previousSibling)
					DOM.addClass(n, 'last');

				return;
			}

			n = ro = DOM.add(tb, 'tr', {id : o.id, 'class' : 'mceMenuItem mceMenuItemEnabled'});
			n = it = DOM.add(n, 'td');
			n = a = DOM.add(n, 'a', {href : 'javascript:;', onclick : "return false;", onmousedown : 'return false;'});

			DOM.addClass(it, s['class']);
//			n = DOM.add(n, 'span', {'class' : 'item'});
			DOM.add(n, 'span', {'class' : 'icon' + (s.icon ? ' ' + s.icon : '')});
			n = DOM.add(n, s.element || 'span', {'class' : 'text', title : o.settings.title}, o.settings.title);

			if (o.settings.style)
				DOM.setAttrib(n, 'style', o.settings.style);

			if (tb.childNodes.length == 1)
				DOM.addClass(ro, 'first');

			if ((n = ro.previousSibling) && DOM.hasClass(n, 'mceMenuItemSeparator'))
				DOM.addClass(ro, 'first');

			if (o.collapse)
				DOM.addClass(ro, 'mceMenuItemSub');

			if (n = ro.previousSibling)
				DOM.removeClass(n, 'last');

			DOM.addClass(ro, 'last');
		}

		});
})();
/* file:jscripts/tiny_mce/classes/ui/Button.js */

(function() {
	var DOM = tinymce.DOM;

	tinymce.create('tinymce.ui.Button:tinymce.ui.Control', {
		Button : function(id, s) {
			this.parent(id, s);
			this.classPrefix = 'mceButton';
		},

		renderHTML : function() {
			var s = this.settings, h = '<a id="' + this.id + '" href="javascript:;" class="mceButton mceButtonEnabled ' + s['class'] + '" onmousedown="return false;" onclick="return false;" title="' + DOM.encode(s.title) + '">';

			if (s.image)
				h += '<img class="icon" src="' + s.image + '" /></a>';
			else
				h += '<span class="icon ' + s['class'] + '"></span></a>';

			return h;
		},

		postRender : function() {
			var t = this, s = t.settings;

			tinymce.dom.Event.add(t.id, 'click', function(e) {
				if (!t.isDisabled())
					return s.onclick.call(s.scope, e);
			});
		}

		});
})();

/* file:jscripts/tiny_mce/classes/ui/ListBox.js */

(function() {
	var DOM = tinymce.DOM, Event = tinymce.dom.Event, each = tinymce.each, Dispatcher = tinymce.util.Dispatcher;

	tinymce.create('tinymce.ui.ListBox:tinymce.ui.Control', {
		ListBox : function(id, s) {
			var t = this;

			t.parent(id, s);
			t.items = [];
			t.onChange = new Dispatcher(t);
			t.onPostRender = new Dispatcher(t);
			t.onAdd = new Dispatcher(t);
			t.onRenderMenu = new tinymce.util.Dispatcher(this);
			t.classPrefix = 'mceListBox';
		},

		select : function(v) {
			var t = this, e, fv;

			// Do we need to do something?
			if (v != t.selectedValue) {
				e = DOM.get(t.id + '_text');
				t.selectedValue = v;

				// Find item
				each(t.items, function(o) {
					if (o.value == v) {
						DOM.setHTML(e, DOM.encode(o.title));
						fv = 1;
						return false;
					}
				});

				// If no item was found then present title
				if (!fv) {
					DOM.setHTML(e, DOM.encode(t.settings.title));
					DOM.addClass(e, 'title');
					e = 0;
					return;
				} else
					DOM.removeClass(e, 'title');
			}

			e = 0;
		},

		add : function(n, v, o) {
			var t = this;

			o = o || {};
			o = tinymce.extend(o, {
				title : n,
				value : v
			});

			t.items.push(o);
			t.onAdd.dispatch(t, o);
		},

		getLength : function() {
			return this.items.length;
		},

		renderHTML : function() {
			var h = '', t = this, s = t.settings;

			h = '<table id="' + t.id + '" cellpadding="0" cellspacing="0" class="mceListBox mceListBoxEnabled' + (s['class'] ? (' ' + s['class']) : '') + '"><tbody><tr>';
			h += '<td>' + DOM.createHTML('a', {id : t.id + '_text', href : 'javascript:;', 'class' : 'text', onclick : "return false;", onmousedown : 'return false;'}, DOM.encode(t.settings.title)) + '</td>';
			h += '<td>' + DOM.createHTML('a', {id : t.id + '_open', href : 'javascript:;', 'class' : 'open', onclick : "return false;", onmousedown : 'return false;'}, '<span></span>') + '</td>';
			h += '</tr></tbody></table>';

			return h;
		},

		showMenu : function() {
			var t = this, p1, p2, e = DOM.get(this.id), m;

			if (t.isDisabled() || t.items.length == 0)
				return;

			if (!t.isMenuRendered) {
				t.renderMenu();
				t.isMenuRendered = true;
			}

			p1 = DOM.getPos(this.settings.menu_container);
			p2 = DOM.getPos(e);

			m = t.menu;
			m.settings.offset_x = p2.x;
			m.settings.offset_y = p2.y;

			// Select in menu
			if (t.oldID)
				m.items[t.oldID].setSelected(0);

			each(t.items, function(o) {
				if (o.value === t.selectedValue) {
					m.items[o.id].setSelected(1);
					t.oldID = o.id;
				}
			});

			m.showMenu(0, e.clientHeight);

			Event.add(document, 'mousedown', t.hideMenu, t);
			DOM.addClass(t.id, 'mceListBoxSelected');
		},

		hideMenu : function(e) {
			var t = this;

			if (!e || !DOM.getParent(e.target, function(n) {return DOM.hasClass(n, 'mceMenu');})) {
				DOM.removeClass(t.id, 'mceListBoxSelected');
				Event.remove(document, 'mousedown', t.hideMenu, t);

				if (t.menu)
					t.menu.hideMenu();
			}
		},

		renderMenu : function() {
			var t = this, m;

			m = t.settings.control_manager.createDropMenu(t.id + '_menu', {
				menu_line : 1,
				'class' : 'mceListBoxMenu noIcons',
				max_width : 150,
				max_height : 150
			});

			m.onHideMenu.add(t.hideMenu, t);

			m.add({
				title : t.settings.title,
				'class' : 'mceMenuItemTitle'
			}).setDisabled(1);

			each(t.items, function(o) {
				o.id = DOM.uniqueId();
				o.onclick = function() {
					if (t.settings.onselect(o.value) !== false)
						t.select(o.value); // Must be runned after
				};

				m.add(o);
			});

			t.onRenderMenu.dispatch(t, m);
			t.menu = m;
		},

		postRender : function() {
			var t = this;

			Event.add(t.id, 'click', t.showMenu, t);

			// Old IE doesn't have hover on all elements
			if (tinymce.isIE6 || !DOM.boxModel) {
				Event.add(t.id, 'mouseover', function() {
					if (!DOM.hasClass(t.id, 'mceListBoxDisabled'))
						DOM.addClass(t.id, 'mceListBoxHover');
				});

				Event.add(t.id, 'mouseout', function() {
					if (!DOM.hasClass(t.id, 'mceListBoxDisabled'))
						DOM.removeClass(t.id, 'mceListBoxHover');
				});
			}

			t.onPostRender.dispatch(t, DOM.get(t.id));
		}

		});
})();
/* file:jscripts/tiny_mce/classes/ui/NativeListBox.js */

(function() {
	var DOM = tinymce.DOM, Event = tinymce.dom.Event, each = tinymce.each, Dispatcher = tinymce.util.Dispatcher;

	tinymce.create('tinymce.ui.NativeListBox:tinymce.ui.ListBox', {
		NativeListBox : function(id, s) {
			this.parent(id, s);
			this.classPrefix = 'mceNativeListBox';
		},

		setDisabled : function(s) {
			DOM.get(this.id).disabled = s;
		},

		isDisabled : function() {
			return DOM.get(this.id).disabled;
		},

		select : function(v) {
			var e = DOM.get(this.id), ol = e.options;

			v = '' + (v || '');

			e.selectedIndex = 0;
			each(ol, function(o, i) {
				if (o.value == v) {
					e.selectedIndex = i;
					return false;
				}
			});
		},

		add : function(n, v, a) {
			var o, t = this;

			a = a || {};
			a.value = v;

			if (t.isRendered())
				DOM.add(DOM.get(this.id), 'option', a, n);

			o = {
				title : n,
				value : v,
				attribs : a
			};

			t.items.push(o);
			t.onAdd.dispatch(t, o);
		},

		getLength : function() {
			return DOM.get(this.id).options.length - 1;
		},

		renderHTML : function() {
			var h, t = this;

			h = DOM.createHTML('option', {value : ''}, '-- ' + t.settings.title + ' --');

			each(t.items, function(it) {
				h += DOM.createHTML('option', {value : it.value}, it.title);
			});

			h = DOM.createHTML('select', {id : t.id, 'class' : 'mceNativeListBox'}, h);

			return h;
		},

		postRender : function() {
			var t = this, ch;

			t.rendered = true;

			function onChange(e) {
				var v = e.target.options[e.target.selectedIndex].value;

				t.onChange.dispatch(t, v);

				if (t.settings.onselect)
					t.settings.onselect(v);
			};

			Event.add(t.id, 'change', onChange);

			// Accessibility keyhandler
			Event.add(t.id, 'keydown', function(e) {
				var bf;

				Event.remove(t.id, 'change', ch);

				bf = Event.add(t.id, 'blur', function() {
					Event.add(t.id, 'change', onChange);
					Event.remove(t.id, 'blur', bf);
				});

				if (e.keyCode == 13 || e.keyCode == 32) {
					onChange(e);
					return Event.cancel(e);
				}
			});

			t.onPostRender.dispatch(t, DOM.get(t.id));
		}

		});
})();
/* file:jscripts/tiny_mce/classes/ui/MenuButton.js */

(function() {
	var DOM = tinymce.DOM, Event = tinymce.dom.Event, each = tinymce.each;

	tinymce.create('tinymce.ui.MenuButton:tinymce.ui.Button', {
		MenuButton : function(id, s) {
			this.parent(id, s);
			this.onRenderMenu = new tinymce.util.Dispatcher(this);
			s.menu_container = s.menu_container || document.body;
		},

		showMenu : function() {
			var t = this, p1, p2, e = DOM.get(t.id), m;

			if (t.isDisabled())
				return;

			if (!t.isMenuRendered) {
				t.renderMenu();
				t.isMenuRendered = true;
			}

			p1 = DOM.getPos(t.settings.menu_container);
			p2 = DOM.getPos(e);

			m = t.menu;
			m.settings.offset_x = p2.x;
			m.settings.offset_y = p2.y;
			m.settings.vp_offset_x = p2.x;
			m.settings.vp_offset_y = p2.y;
			m.showMenu(0, e.clientHeight);

			Event.add(document, 'mousedown', t.hideMenu, t);
			t.setState('Selected', 1);
		},

		renderMenu : function() {
			var t = this, m;

			m = t.settings.control_manager.createDropMenu(t.id + '_menu', {
				menu_line : 1,
				'class' : this.classPrefix + 'Menu'
			});

			m.onHideMenu.add(t.hideMenu, t);

			t.onRenderMenu.dispatch(t, m);
			t.menu = m;
		},

		hideMenu : function(e) {
			var t = this;

			if (!e || !DOM.getParent(e.target, function(n) {return DOM.hasClass(n, 'mceMenu');})) {
				t.setState('Selected', 0);
				Event.remove(document, 'mousedown', t.hideMenu, t);
				if (t.menu)
					t.menu.hideMenu();
			}
		},

		postRender : function() {
			var t = this, s = t.settings;

			Event.add(t.id, 'click', function() {
				if (!t.isDisabled()) {
					if (s.onclick)
						s.onclick(t.value);

					t.showMenu();
				}
			});
		}

		});
})();

/* file:jscripts/tiny_mce/classes/ui/SplitButton.js */

(function() {
	var DOM = tinymce.DOM, Event = tinymce.dom.Event, each = tinymce.each;

	tinymce.create('tinymce.ui.SplitButton:tinymce.ui.MenuButton', {
		SplitButton : function(id, s) {
			this.parent(id, s);
			this.classPrefix = 'mceSplitButton';
		},

		renderHTML : function() {
			var h, t = this, s = t.settings, h1;

			h = '<tbody><tr>';

			if (s.image)
				h1 = DOM.createHTML('img ', {src : s.image, 'class' : 'action ' + s['class']});
			else
				h1 = DOM.createHTML('span', {'class' : 'action ' + s['class']});

			h += '<td>' + DOM.createHTML('a', {id : t.id + '_action', href : 'javascript:;', 'class' : 'action ' + s['class'], onclick : "return false;", onmousedown : 'return false;', title : s.title}, h1) + '</td>';
	
			h1 = DOM.createHTML('span', {'class' : 'open ' + s['class']});
			h += '<td>' + DOM.createHTML('a', {id : t.id + '_open', href : 'javascript:;', 'class' : 'open ' + s['class'], onclick : "return false;", onmousedown : 'return false;', title : s.title}, h1) + '</td>';

			h += '</tr></tbody>';

			return DOM.createHTML('table', {id : t.id, 'class' : 'mceSplitButton mceSplitButtonEnabled ' + s['class'], cellpadding : '0', cellspacing : '0', onmousedown : 'return false;', title : s.title}, h);
		},

		postRender : function() {
			var t = this, s = t.settings;

			if (s.onclick) {
				Event.add(t.id + '_action', 'click', function() {
					if (!t.isDisabled())
						s.onclick(t.value);
				});
			}

			Event.add(t.id + '_open', 'click', t.showMenu, t);

			// Old IE doesn't have hover on all elements
			if (tinymce.isIE6 || !DOM.boxModel) {
				Event.add(t.id, 'mouseover', function() {
					if (!DOM.hasClass(t.id, 'mceSplitButtonDisabled'))
						DOM.addClass(t.id, 'mceSplitButtonHover');
				});

				Event.add(t.id, 'mouseout', function() {
					if (!DOM.hasClass(t.id, 'mceSplitButtonDisabled'))
						DOM.removeClass(t.id, 'mceSplitButtonHover');
				});
			}
		}

		});
})();

/* file:jscripts/tiny_mce/classes/ui/ColorSplitButton.js */

(function() {
	var DOM = tinymce.DOM, Event = tinymce.dom.Event, is = tinymce.is, each = tinymce.each;

	tinymce.create('tinymce.ui.ColorSplitButton:tinymce.ui.SplitButton', {
		ColorSplitButton : function(id, s) {
			var t = this;

			t.parent(id, s);

			t.settings = s = tinymce.extend({
				colors : '000000,993300,333300,003300,003366,000080,333399,333333,800000,FF6600,808000,008000,008080,0000FF,666699,808080,FF0000,FF9900,99CC00,339966,33CCCC,3366FF,800080,999999,FF00FF,FFCC00,FFFF00,00FF00,00FFFF,00CCFF,993366,C0C0C0,FF99CC,FFCC99,FFFF99,CCFFCC,CCFFFF,99CCFF,CC99FF,FFFFFF',
				grid_width : 8,
				default_color : '#888888'
			}, t.settings);

			t.value = s.default_color;
		},

		showMenu : function() {
			var t = this, r, p, e;

			if (t.isDisabled())
				return;

			if (!t.isMenuRendered) {
				t.renderMenu();
				t.isMenuRendered = true;
			}

			e = DOM.get(t.id);
			DOM.show(t.id + '_menu');
			DOM.addClass(e, 'mceSplitButtonSelected');
			p2 = DOM.getPos(e);
			DOM.setStyles(t.id + '_menu', {
				left : p2.x,
				top : p2.y + e.clientHeight,
				zIndex : 150
			});
			e = 0;

			Event.add(document, 'mousedown', t.hideMenu, t);
		},

		hideMenu : function(e) {
			var t = this;

			if (!e || !DOM.getParent(e.target, function(n) {return DOM.hasClass(n, 'mceSplitButtonMenu');})) {
				DOM.removeClass(t.id, 'mceSplitButtonSelected');
				Event.remove(document, 'mousedown', t.hideMenu, t);
				DOM.hide(t.id + '_menu');
			}
		},

		renderMenu : function() {
			var t = this, m, i = 0, s = t.settings, n, tb, tr, w;

			w = DOM.add(s.menu_container, 'div', {id : t.id + '_menu', 'class' : s['menu_class'] + ' ' + s['class'], style : 'position:absolute;left:0;top:-1000px;'});
			m = DOM.add(w, 'div', {'class' : s['class'] + ' mceSplitButtonMenu'});
			DOM.add(m, 'span', {'class' : 'mceMenuLine'});

			n = DOM.add(m, 'table', {'class' : 'mceColorSplitMenu'});
			tb = DOM.add(n, 'tbody');

			// Generate color grid
			i = 0;
			each(is(s.colors, 'array') ? s.colors : s.colors.split(','), function(c) {
				c = c.replace(/^#/, '');

				if (!i--) {
					tr = DOM.add(tb, 'tr');
					i = s.grid_width - 1;
				}

				n = DOM.add(tr, 'td');

				n = DOM.add(n, 'a', {
					href : 'javascript:;',
					style : {
						backgroundColor : '#' + c
					}
				});

				Event.add(n, 'mousedown', function() {
					t.setColor('#' + c);
				});
			});

			if (s.more_colors_func) {
				n = DOM.add(tb, 'tr');
				n = DOM.add(n, 'td', {colspan : s.grid_width, 'class' : 'morecolors'});
				n = DOM.add(n, 'a', {href : 'javascript:;', onclick : 'return false;', 'class' : 'morecolors'}, s.more_colors_title);

				Event.add(n, 'click', function(e) {
					s.more_colors_func.call(s.more_colors_scope || this);
					return Event.cancel(e); // Cancel to fix onbeforeunload problem
				});
			}

			DOM.addClass(m, 'mceColorSplitMenu');

			return w;
		},

		setColor : function(c) {
			var t = this, p, s = this.settings, co = s.menu_container, po, cp, id = t.id + '_preview';

			if (!(p = DOM.get(id))) {
				DOM.setStyle(t.id + '_action', 'position', 'relative');
				p = DOM.add(t.id + '_action', 'div', {id : id, 'class' : 'mceColorPreview'});
			}

			p.style.backgroundColor = c;

			t.value = c;
			t.hideMenu();
			s.onselect(c);
		}

		});
})();

/* file:jscripts/tiny_mce/classes/ui/Toolbar.js */

tinymce.create('tinymce.ui.Toolbar:tinymce.ui.Container', {
	renderHTML : function() {
		var t = this, h = '', c = 'mceToolbarEnd', co, dom = tinymce.DOM, s = t.settings;

		h += dom.createHTML('td', {'class' : 'mceToolbarStart'}, dom.createHTML('span', null, '<!-- IE -->'));

		tinymce.each(t.controls, function(c) {
			h += '<td>' + c.renderHTML() + '</td>';
		});

		co = t.controls[t.controls.length - 1].constructor;

		if (co === tinymce.ui.Button)
			c += ' mceToolbarEndButton';
		else if (co === tinymce.ui.SplitButton)
			c += ' mceToolbarEndSplitButton';
		else if (co === tinymce.ui.ListBox)
			c += ' mceToolbarEndListBox';

		h += dom.createHTML('td', {'class' : c}, dom.createHTML('span', null, '<!-- IE -->'));

		return dom.createHTML('table', {id : t.id, 'class' : 'mceToolbar' + (s['class'] ? ' ' + s['class'] : ''), cellpadding : '0', cellspacing : '0', align : t.settings.align || ''}, '<tbody><tr>' + h + '</tr></tbody>');
	}

	});

/* file:jscripts/tiny_mce/classes/AddOnManager.js */

(function() {
	var Dispatcher = tinymce.util.Dispatcher, each = tinymce.each;

	tinymce.create('tinymce.AddOnManager', {
		items : [],
		urls : {},
		lookup : {},
		onAdd : new Dispatcher(this),

		get : function(n) {
			return this.lookup[n];
		},

		requireLangPack : function(n) {
			var u, s;

			if (tinymce.EditorManager.settings) {
				u = this.urls[n] + '/langs/' + tinymce.EditorManager.settings.language + '.js';
				s = tinymce.EditorManager.settings;

				if (s) {
					if (!tinymce.dom.Event.domLoaded && !s.strict_mode)
						tinymce.ScriptLoader.load(u);
					else
						tinymce.ScriptLoader.add(u);
				}
			}
		},

		add : function(id, o) {
			this.items.push(o);
			this.lookup[id] = o;
			this.onAdd.dispatch(this, id, o);

			return o;
		},

		load : function(n, u, cb, s) {
			if (u.indexOf('/') != 0 && u.indexOf('://') == -1)
				u = tinymce.baseURL + '/' +  u;

			this.urls[n] = u.substring(0, u.lastIndexOf('/'));
			tinymce.ScriptLoader.add(u, cb, s);
		}

		});

	// Create plugin and theme managers
	tinymce.PluginManager = new tinymce.AddOnManager();
	tinymce.ThemeManager = new tinymce.AddOnManager();
}());
/* file:jscripts/tiny_mce/classes/EditorManager.js */

(function() {
	// Shorten names
	var each = tinymce.each, extend = tinymce.extend, DOM = tinymce.DOM, Event = tinymce.dom.Event, ThemeManager = tinymce.ThemeManager, PluginManager = tinymce.PluginManager;

	tinymce.create('static tinymce.EditorManager', {
		editors : {},
		i18n : {},
		activeEditor : null,

		init : function(s) {
			var t = this, pl, sl = tinymce.ScriptLoader;

			function execCallback(se, n, s) {
				var f = se[n];

				if (!f)
					return;

				if (tinymce.is(f, 'string')) {
					s = f.replace(/\.\w+$/, '');
					s = s ? tinymce.resolve(s) : 0;
					f = tinymce.resolve(f);
				}

				return f.apply(s || this, Array.prototype.slice.call(arguments, 2));
			};

			s = extend({
				theme : "simple",
				language : "en",
				strict_loading_mode : document.contentType == 'application/xhtml+xml'
			}, s);

			t.settings = s;

			// If page not loaded and strict mode isn't enabled then load them
			if (!Event.domLoaded && !s.strict_loading_mode) {
				// Load language
				if (s.language)
					sl.add(tinymce.baseURL + '/langs/' + s.language + '.js');

				// Load theme
				if (s.theme && s.theme.charAt(0) != '-')
					ThemeManager.load(s.theme, 'themes/' + s.theme + '/editor_template' + tinymce.suffix + '.js');

				// Load plugins
				if (s.plugins) {
					pl = s.plugins.split(',');

					// Load compat2x first
					if (tinymce.inArray(pl, 'compat2x') != -1)
						PluginManager.load('compat2x', 'plugins/compat2x/editor_plugin' + tinymce.suffix + '.js');

					// Load rest if plugins
					each(pl, function(v) {
						if (v && v.charAt(0) != '-') {
							// Skip safari plugin for other browsers
							if (!tinymce.isWebKit && v == 'safari')
								return;

							PluginManager.load(v, 'plugins/' + v + '/editor_plugin' + tinymce.suffix + '.js');
						}
					});
				}

				sl.loadQueue();
			}

			// Legacy call
			Event.add(document, 'init', function() {
				var l, co;

				execCallback(s, 'onpageload');

				// Verify that it's a valid browser
				if (s.browsers) {
					l = false;

					each(s.browsers.split(','), function(v) {
						switch (v) {
							case 'ie':
							case 'msie':
								if (tinymce.isIE)
									l = true;
								break;

							case 'gecko':
								if (tinymce.isGecko)
									l = true;
								break;

							case 'safari':
							case 'webkit':
								if (tinymce.isWebKit)
									l = true;
								break;

							case 'opera':
								if (tinymce.isOpera)
									l = true;

								break;
						}
					});

					// Not a valid one
					if (!l)
						return;
				}

				switch (s.mode) {
					case "exact":
						l = s.elements || '';
						each(l.split(','), function(v) {
							new tinymce.Editor(v, s).render();
						});
						break;

					case "textareas":
						function hasClass(n, c) {
							return new RegExp('\\b' + c + '\\b', 'g').test(n.className);
						};

						each(DOM.select('textarea'), function(v) {
							if (s.editor_deselector && hasClass(v, s.editor_deselector))
								return;

							if (!s.editor_selector || hasClass(v, s.editor_selector))
								new tinymce.Editor(v.id = (v.id || v.name || (v.id = DOM.uniqueId())), s).render();
						});
						break;
				}

				// Call onInit when all editors are initialized
				if (s.oninit) {
					l = co = 0;

					each (t.editors, function(ed) {
						co++;

						if (!ed.initialized) {
							// Wait for it
							ed.onInit.add(function() {
								l++;

								// All done
								if (l == co)
									execCallback(s, 'oninit');
							});
						} else
							l++;

						// All done
						if (l == co)
							execCallback(s, 'oninit');					
					});
				}
			});
		},

		get : function(id) {
			return this.editors[id];
		},

		getInstanceById : function(id) {
			return this.get(id);
		},

		add : function(e) {
			this.editors[e.id] = e;
			this._setActive(e);

			return e;
		},

		remove : function(e) {
			var t = this;

			// Not in the collection
			if (!t.editors[e.id])
				return null;

			delete t.editors[e.id];

			// Select another editor since the active one was removed
			if (t.activeEditor == e) {
				each(t.editors, function(e) {
					t._setActive(e);
					return false; // Break
				});
			}

			e._destroy();

			return e;
		},

		execCommand : function(c, u, v) {
			var t = this, ed = t.get(v);

			// Manager commands
			switch (c) {
				case "mceFocus":
					ed.focus();
					return true;

				case "mceAddEditor":
				case "mceAddControl":
					new tinymce.Editor(v, t.settings).render();
					return true;

				case "mceAddFrameControl":
					// TODO: Implement this
					return true;

				case "mceRemoveEditor":
				case "mceRemoveControl":
					ed.remove();
					return true;

				case 'mceToggleEditor':
					if (!ed) {
						t.execCommand('mceAddControl', 0, v);
						return true;
					}

					if (ed.isHidden())
						ed.show();
					else
						ed.hide();

					return true;
			}

			// Run command on active editor
			if (t.activeEditor)
				return t.activeEditor.execCommand(c, u, v);

			return false;
		},

		execInstanceCommand : function(id, c, u, v) {
			var ed = this.get(id);

			if (ed)
				return ed.execCommand(c, u, v);

			return false;
		},

		triggerSave : function() {
			each(this.editors, function(e) {
				e.save();
			});
		},

		addI18n : function(p, o) {
			var lo, i18n = this.i18n;

			if (!tinymce.is(p, 'string')) {
				each(p, function(o, lc) {
					each(o, function(o, g) {
						each(o, function(o, k) {
							if (g === 'common')
								i18n[lc + '.' + k] = o;
							else
								i18n[lc + '.' + g + '.' + k] = o;
						});
					});
				});
			} else {
				each(o, function(o, k) {
					i18n[p + '.' + k] = o;
				});
			}
		},

		// Private methods

		_setActive : function(e) {
			this.selectedInstance = this.activeEditor = e;
		}

		});

	// Setup some URLs where the editor API is located and where the document is
	tinymce.documentBaseURL = window.location.href.replace(/[\?#].*$/, '').replace(/[\/\\][^\/]+$/, '');
	if (!/[\/\\]$/.test(tinymce.documentBaseURL))
		tinymce.documentBaseURL += '/';

	tinymce.baseURL = new tinymce.util.URI(tinymce.documentBaseURL).toAbsolute(tinymce.baseURL);
	tinymce.EditorManager.baseURI = new tinymce.util.URI(tinymce.baseURL);
})();

// Short for editor manager window.tinyMCE is needed when TinyMCE gets loaded though a XHR call
var tinyMCE = window.tinyMCE = tinymce.EditorManager;

/* file:jscripts/tiny_mce/classes/Editor.js */

(function() {
	var DOM = tinymce.DOM, Event = tinymce.dom.Event, extend = tinymce.extend, Dispatcher = tinymce.util.Dispatcher;
	var each = tinymce.each, isGecko = tinymce.isGecko, isIE = tinymce.isIE, isWebKit = tinymce.isWebKit;
	var is = tinymce.is, ThemeManager = tinymce.ThemeManager, PluginManager = tinymce.PluginManager, EditorManager = tinymce.EditorManager;
	var inArray = tinymce.inArray, grep = tinymce.grep;

	tinymce.create('tinymce.Editor', {
		Editor : function(id, s) {
			var t = this;

			t.id = t.editorId = id;
			t.execCommands = {};
			t.queryStateCommands = {};
			t.queryValueCommands = {};
			t.plugins = {};

			// Add events to the editor
			each([
				'onPreInit',
				'onBeforeRenderUI',
				'onPostRender',
				'onInit',
				'onRemove',
				'onActivate',
				'onDeactivate',
				'onClick',
				'onEvent',
				'onMouseUp',
				'onMouseDown',
				'onDblClick',
				'onKeyDown',
				'onKeyUp',
				'onKeyPress',
				'onContextMenu',
				'onSubmit',
				'onReset',
				'onPaste',
				'onPreProcess',
				'onPostProcess',
				'onBeforeSetContent',
				'onBeforeGetContent',
				'onSetContent',
				'onGetContent',
				'onLoadContent',
				'onSaveContent',
				'onNodeChange',
				'onChange',
				'onBeforeExecCommand',
				'onExecCommand',
				'onUndo',
				'onRedo',
				'onVisualAid',
				'onSetProgressState'
			], function(e) {
				t[e] = new Dispatcher(t);
			});

			// Default editor config
			t.settings = s = extend({
				id : id,
				language : 'en',
				docs_language : 'en',
				theme : 'simple',
				skin : 'default',
				delta_width : 0,
				delta_height : 0,
				popup_css : '',
				plugins : '',
				document_base_url : tinymce.documentBaseURL,
				add_form_submit_trigger : 1,
				submit_patch : 1,
				add_unload_trigger : 1,
				convert_urls : 1,
				relative_urls : 1,
				remove_script_host : 1,
				table_inline_editing : 0,
				object_resizing : 1,
				cleanup : 1,
				accessibility_focus : 1,
				custom_shortcuts : 1,
				custom_undo_redo_keyboard_shortcuts : 1,
				custom_undo_redo_restore_selection : 1,
				custom_undo_redo : 1,
				doctype : '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">',
				visual_table_class : 'mceItemTable',
				visual : 1,
				inline_styles : true,
				convert_fonts_to_spans : true,
				font_size_style_values : 'xx-small,x-small,small,medium,large,x-large,xx-large',
				apply_source_formatting : 1,
				directionality : 'ltr',
				forced_root_block : 'p',
				valid_elements : '@[id|class|style|title|dir<ltr?rtl|lang|xml::lang|onclick|ondblclick|onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|onkeydown|onkeyup],a[rel|rev|charset|hreflang|tabindex|accesskey|type|name|href|target|title|class|onfocus|onblur],strong/b,em/i,strike,u,#p[align],-ol[type|compact],-ul[type|compact],-li,br,img[longdesc|usemap|src|border|alt=|title|hspace|vspace|width|height|align],-sub,-sup,-blockquote,-table[border=0|cellspacing|cellpadding|width|frame|rules|height|align|summary|bgcolor|background|bordercolor],-tr[rowspan|width|height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot,#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor|scope],#th[colspan|rowspan|width|height|align|valign|scope],caption,-div,-span,-pre,address,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],-font[face|size|color],dd,dl,dt,cite,abbr,acronym,del[datetime|cite],ins[datetime|cite],object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width|height|src|*],script[type]',
				hidden_input : 1,
				padd_empty_editor : 1,
				render_ui : 1,
				init_theme : 1,
				indentation : '30px'
			}, s);

			// Setup URIs
			t.documentBaseURI = new tinymce.util.URI(s.document_base_url || tinymce.documentBaseURL, {
				base_uri : tinyMCE.baseURI
			});
			t.baseURI = EditorManager.baseURI;

			// Call setup
			t.execCallback('setup', t);
		},

		render : function() {
			var t = this, s = t.settings, id = t.id, sl = tinymce.ScriptLoader;

			// Element not found, then skip initialization
			if (!t.getElement())
				return;

			if (s.strict_loading_mode) {
				sl.settings.strict_mode = s.strict_loading_mode;
				tinymce.DOM.settings.strict = 1;
			}

			// Add hidden input for non input elements inside form elements
			if (!/TEXTAREA|INPUT/i.test(t.getElement().nodeName) && s.hidden_input && DOM.getParent(id, 'form'))
				DOM.insertAfter(DOM.create('input', {type : 'hidden', name : id}), id);

			t.windowManager = new tinymce.WindowManager(t);

			if (s.encoding == 'xml') {
				t.onGetContent.add(function(ed, o) {
					if (o.get)
						o.content = DOM.encode(o.content);
				});
			}

			if (s.add_form_submit_trigger) {
				t.onSubmit.addToTop(function() {
					if (t.initialized) {
						t.save();
						t.isNotDirty = 1;
					}
				});
			}

			Event.add(document, 'unload', function() {
				if (t.initialized && !t.destroyed && s.add_unload_trigger)
					t.save({format : 'raw', no_events : true});
			});

			tinymce.addUnload(t._destroy, t);

			if (s.submit_patch) {
				t.onBeforeRenderUI.add(function() {
					var n = t.getElement().form;

					if (!n)
						return;

					// Already patched
					if (n._mceOldSubmit)
						return;

					// Check page uses id="submit" or name="submit" for it's submit button
					if (!n.submit.nodeType) {
						t.formElement = n;
						n._mceOldSubmit = n.submit;
						n.submit = function() {
							// Save all instances
							EditorManager.triggerSave();
							t.isNotDirty = 1;

							return this._mceOldSubmit(this);
						};
					}

					n = null;
				});
			}

			// Load scripts
			function loadScripts() {
				sl.add(tinymce.baseURL + '/langs/' + s.language + '.js');

				if (s.theme.charAt(0) != '-')
					ThemeManager.load(s.theme, 'themes/' + s.theme + '/editor_template' + tinymce.suffix + '.js');

				each(s.plugins.split(','), function(p) {
					if (p && p.charAt(0) != '-') {
						// Skip safari plugin for other browsers
						if (!isWebKit && p == 'safari')
							return;

						PluginManager.load(p, 'plugins/' + p + '/editor_plugin' + tinymce.suffix + '.js');
					}
				});

				// Init when que is loaded
				sl.loadQueue(function() {
					if (s.ask) {
						function ask() {
							t.windowManager.confirm(t.getLang('edit_confirm'), function(s) {
								if (s)
									t.init();
								else
									Event.remove(t.id, 'focus', ask);
							});
						};

						Event.add(t.id, 'focus', ask);
						return;
					}

					if (!t.removed)
						t.init();
				});
			};

			// Load compat2x first
			if (s.plugins.indexOf('compat2x') != -1) {
				PluginManager.load('compat2x', 'plugins/compat2x/editor_plugin' + tinymce.suffix + '.js');
				sl.loadQueue(loadScripts);
			} else
				loadScripts();
		},

		init : function() {
			var n, t = this, s = t.settings, w, h, e = t.getElement(), o, ti;

			EditorManager.add(t);

			// Create theme
			s.theme = s.theme.replace(/-/, '');
			o = ThemeManager.get(s.theme);
			t.theme = new o();

			if (t.theme.init && s.init_theme)
				t.theme.init(t, ThemeManager.urls[s.theme] || tinymce.documentBaseURL.replace(/\/$/, ''));

			// Create all plugins
			each(s.plugins.replace(/\-/g, '').split(','), function(p) {
				var c = PluginManager.get(p), u = PluginManager.urls[p] || tinymce.documentBaseURL.replace(/\/$/, ''), po;

				if (c) {
					po = new c(t, u);

					t.plugins[p] = po;

					if (po.init)
						po.init(t, u);
				}
			});

			// Setup popup CSS path(s)
			s.popup_css = t.baseURI.toAbsolute(s.popup_css || "themes/" + s.theme + "/skins/" + s.skin + "/dialog.css");

			if (s.popup_css_add)
				s.popup_css += ',' + s.popup_css_add;

			// Setup control factory
			t.controlManager = new tinymce.ControlManager(t);
			t.undoManager = new tinymce.UndoManager(t);

			// Pass through
			t.undoManager.onAdd.add(function(um, l) {
				return t.onChange.dispatch(t, l, um);
			});

			t.undoManager.onUndo.add(function(um, l) {
				return t.onUndo.dispatch(t, l, um);
			});

			t.undoManager.onRedo.add(function(um, l) {
				return t.onRedo.dispatch(t, l, um);
			});

			if (s.custom_undo_redo) {
				t.onExecCommand.add(function(ed, cmd) {
					if (cmd != 'Undo' && cmd != 'Redo' && cmd != 'mceRepaint')
						t.undoManager.add();
				});
			}

			t.onExecCommand.add(function(ed, c) {
				// Don't refresh the select lists until caret move
				if (!/^(FontName|FontSize)$/.test(c))
					t.nodeChanged();
			});

			// Remove ghost selections on images and tables in Gecko
			if (isGecko) {
				function repaint() {
					t.execCommand('mceRepaint');
				};

				t.onUndo.add(repaint);
				t.onRedo.add(repaint);
				t.onSetContent.add(repaint);
			}

			// Enables users to override the control factory
			t.onBeforeRenderUI.dispatch(t, t.controlManager);

			// Measure box
			if (s.render_ui) {
				w = s.width || e.style.width || e.clientWidth;
				h = s.height || e.style.height || e.clientHeight;
				t.orgDisplay = e.style.display;

				if (('' + w).indexOf('%') == -1)
					w = Math.max(parseInt(w) + (o.deltaWidth || 0), 100);

				if (('' + h).indexOf('%') == -1)
					h = Math.max(parseInt(h) + (o.deltaHeight || 0), 100);

				// Render UI
				o = t.theme.renderUI({
					targetNode : e,
					width : w,
					height : h,
					deltaWidth : s.delta_width,
					deltaHeight : s.delta_height
				});

				t.editorContainer = o.editorContainer;
			}

			
			// Resize editor
			DOM.setStyles(o.sizeContainer || o.editorContainer, {
				width : w,
				height : h
			});

			h = (o.iframeHeight || h) + ((h + '').indexOf('%') == -1 ? (o.deltaHeight || 0) : '');
			if (h < 100)
				h = 100;

			// Create iframe
			n = DOM.add(o.iframeContainer, 'iframe', {
				id : s.id + "_ifr",
				src : 'javascript:""', // Workaround for HTTPS warning in IE6/7
				frameBorder : '0',
				style : {
					width : '100%',
					height : h
				}
			});

			t.contentAreaContainer = o.iframeContainer;
			DOM.get(o.editorContainer).style.display = t.orgDisplay;
			DOM.get(s.id).style.display = 'none';

			// Safari 2.x requires us to wait for the load event and load a real HTML doc
			if (tinymce.isOldWebKit) {
				Event.add(n, 'load', t.setupIframe, t);
				n.src = tinymce.baseURL + '/plugins/safari/blank.htm';
			} else {
				t.setupIframe();
				e = n = o = null; // Cleanup
			}

/*			function createIfr() {
				h = (o.iframeHeight || h) + ((h + '').indexOf('%') == -1 ? (o.deltaHeight || 0) : '');
				if (h < 100)
					h = 100;

				// Create iframe
				n = DOM.add(o.iframeContainer, 'iframe', {
					id : s.id + "_ifr",
					src : 'javascript:""', // Workaround for HTTPS warning in IE6/7
					frameBorder : '0',
					style : {
						width : '100%',
						height : h
					}
				});

				t.contentAreaContainer = o.iframeContainer;
				DOM.get(o.editorContainer).style.display = t.orgDisplay;
				DOM.get(s.id).style.display = 'none';

				// Safari 2.x requires us to wait for the load event and load a real HTML doc
				if (tinymce.isOldWebKit) {
					Event.add(n, 'load', t.setupIframe, t);
					n.src = tinymce.baseURL + '/plugins/safari/blank.htm';
				} else {
					t.setupIframe();
					e = n = o = null; // Cleanup
				}
			};
*/
			// Waits for the editor to become visible, then it will run the init
			// This method is somewhat ugly but this is the best way to deal with it without
			// forcing the user to add some logic to their application.
/*			if (isGecko) {
				function check() {
					var hi;

					DOM.getParent(t.id, function(e) {
						if (DOM.getStyle(e, 'display', true) == 'none') {
							hi = 1;
							return true;
						}
					});

					return hi;
				};

				if (check()) {
					ti = window.setInterval(function() {
						if (!check()) {
							createIfr();
							window.clearInterval(ti);
						}
					}, 300);

					return;
				}
			}

			createIfr();
*/
		},

		setupIframe : function() {
			var t = this, s = t.settings, e = DOM.get(s.id), d = t.getDoc();

			// Setup body
			d.open();
			d.write(s.doctype + '<html><head xmlns="http://www.w3.org/1999/xhtml"><base href="' + t.documentBaseURI.getURI() + '" /><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /></head><body id="tinymce" class="mceContentBody"></body></html>');
			d.close();

			// Design mode needs to be added here Ctrl+A will fail otherwise
			if (!isIE) {
				try {
					d.designMode = 'On';
				} catch (ex) {
					// Will fail on Gecko if the editor is placed in an hidden container element
					// The design mode will be set ones the editor is focused
				}
			}

			// IE needs to use contentEditable or it will display non secure items for HTTPS
			if (isIE)
				t.getBody().contentEditable = true;

			// Setup objects
			t.dom = new tinymce.DOM.DOMUtils(t.getDoc(), {
				keep_values : true,
				url_converter : t.convertURL,
				url_converter_scope : t,
				hex_colors : s.force_hex_style_colors,
				class_filter : s.class_filter,
				update_styles : 1
			});

			t.serializer = new tinymce.dom.Serializer({
				entity_encoding : s.entity_encoding,
				entities : s.entities,
				valid_elements : s.verify_html === false ? '*[*]' : s.valid_elements,
				extended_valid_elements : s.extended_valid_elements,
				valid_child_elements : s.valid_child_elements,
				invalid_elements : s.invalid_elements,
				fix_table_elements : s.fix_table_elements,
				fix_list_elements : s.fix_list_elements,
				fix_content_duplication : s.fix_content_duplication,
				convert_fonts_to_spans : s.convert_fonts_to_spans,
				font_size_classes  : s.font_size_classes,
				font_size_style_values : s.font_size_style_values,
				apply_source_formatting : s.apply_source_formatting,
				remove_linebreaks : s.remove_linebreaks,
				dom : t.dom
			});

			t.selection = new tinymce.dom.Selection(t.dom, t.getWin(), t.serializer);
			t.forceBlocks = new tinymce.ForceBlocks(t, {
				forced_root_block : s.forced_root_block
			});
			t.editorCommands = new tinymce.EditorCommands(t);

			// Pass through
			t.serializer.onPreProcess.add(function(se, o) {
				return t.onPreProcess.dispatch(t, o, se);
			});

			t.serializer.onPostProcess.add(function(se, o) {
				return t.onPostProcess.dispatch(t, o, se);
			});

			t.onPreInit.dispatch(t);

			if (!s.gecko_spellcheck)
				t.getBody().spellcheck = 0;

			t._addEvents();

			t.controlManager.onPostRender.dispatch(t, t.controlManager);
			t.onPostRender.dispatch(t);

			if (s.directionality)
				t.getBody().dir = s.directionality;

			if (s.nowrap)
				t.getBody().style.whiteSpace = "nowrap";

			if (s.auto_resize)
				t.onNodeChange.add(t.resizeToContent, t);

			if (s.handle_node_change_callback) {
				t.onNodeChange.add(function(ed, cm, n) {
					t.execCallback('handle_node_change_callback', t.id, n, -1, -1, true, t.selection.isCollapsed());
				});
			}

			if (s.save_callback) {
				t.onSaveContent.add(function(ed, o) {
					var h = t.execCallback('save_callback', t.id, o.content, t.getBody());

					if (h)
						o.content = h;
				});
			}

			if (s.onchange_callback) {
				t.onChange.add(function(ed, l) {
					t.execCallback('onchange_callback', t, l);
				});
			}

			if (s.convert_newlines_to_brs) {
				t.onBeforeSetContent.add(function(ed, o) {
					if (o.initial)
						o.content = o.content.replace(/\r?\n/g, '<br />');
				});
			}

			if (s.fix_nesting && isIE) {
				t.onBeforeSetContent.add(function(ed, o) {
					o.content = t._fixNesting(o.content);
				});
			}

			if (s.preformatted) {
				t.onPostProcess.add(function(ed, o) {
					o.content = o.content.replace(/^\s*<pre.*?>/, '');
					o.content = o.content.replace(/<\/pre>\s*$/, '');

					if (o.set)
						o.content = '<pre class="mceItemHidden">' + o.content + '</pre>';
				});
			}

			if (s.verify_css_classes) {
				t.serializer.attribValueFilter = function(n, v) {
					var s, cl;

					if (n == 'class') {
						// Build regexp for classes
						if (!t.classesRE) {
							cl = t.dom.getClasses();

							if (cl.length > 0) {
								s = '';

								each (cl, function(o) {
									s += (s ? '|' : '') + o['class'];
								});

								t.classesRE = new RegExp('(' + s + ')', 'gi');
							}
						}

						return !t.classesRE || /(\bmceItem\w+\b|\bmceTemp\w+\b)/g.test(v) || t.classesRE.test(v) ? v : '';
					}

					return v;
				};
			}

			if (s.convert_fonts_to_spans)
				t._convertFonts();

			if (s.inline_styles)
				t._convertInlineElements();

			if (s.cleanup_callback) {
				t.onSetContent.add(function(ed, o) {
					if (o.initial) {
						t.setContent(t.execCallback('cleanup_callback', 'insert_to_editor', o.content, o), {format : 'raw', no_events : true});
						t.execCallback('cleanup_callback', 'insert_to_editor_dom', t.getDoc(), o);
					}
				});

				t.onPreProcess.add(function(ed, o) {
					if (o.set)
						t.execCallback('cleanup_callback', 'insert_to_editor_dom', o.node, o);

					if (o.get)
						t.execCallback('cleanup_callback', 'get_from_editor_dom', o.node, o);
				});

				t.onPostProcess.add(function(ed, o) {
					if (o.set)
						o.content = t.execCallback('cleanup_callback', 'insert_to_editor', o.content, o);

					if (o.get)						
						o.content = t.execCallback('cleanup_callback', 'get_from_editor', o.content, o);
				});
			}

			if (s.save_callback) {
				t.onGetContent.add(function(ed, o) {
					if (o.save)
						o.content = t.execCallback('save_callback', t.id, o.content, t.getBody());
				});
			}

			if (s.handle_event_callback) {
				t.onEvent.add(function(ed, e, o) {
					if (t.execCallback('handle_event_callback', e, ed, o) === false)
						Event.cancel(e);
				});
			}

			t.onSetContent.add(function() {
				// Safari needs some time, it will crash the browser when a link is created otherwise
				// I think this crash issue is resolved in the latest 3.0.4
				//window.setTimeout(function() {
					t.addVisual(t.getBody());
				//}, 1);
			});

			// Remove empty contents
			if (s.padd_empty_editor) {
				t.onPostProcess.add(function(ed, o) {
					o.content = o.content.replace(/^<p>(&nbsp;|#160;|\s)<\/p>$/, '');
				});
			}

			// A small timeout was needed since firefox will remove. Bug: #1838304
			setTimeout(function () {
				if (t.removed)
					return;

				t.load({initial : true, format : (s.cleanup_on_startup ? 'html' : 'raw')});
				t.startContent = t.getContent({format : 'raw'});
				t.undoManager.add({initial : true});
				t.initialized = true;

				t.onInit.dispatch(t);
				t.execCallback('setupcontent_callback', t.id, t.getBody(), t.getDoc());
				t.execCallback('init_instance_callback', t);
				t.focus(true);
				t.nodeChanged({initial : 1});

				// Load specified content CSS last
				if (s.content_css) {
					tinymce.each(s.content_css.split(','), function(u) {
						t.dom.loadCSS(t.documentBaseURI.toAbsolute(u));
					});
				}

				// Handle auto focus
				if (s.auto_focus) {
					setTimeout(function () {
						var ed = EditorManager.get(s.auto_focus);

						ed.selection.select(ed.getBody(), 1);
						ed.selection.collapse(1);
						ed.getWin().focus();
					}, 100);
				}
			}, 1);

			e = null;
		},

		
		focus : function(sf) {
			var oed, t = this;

			if (!sf) {
				t.getWin().focus();

							}

			if (EditorManager.activeEditor != t) {
				if ((oed = EditorManager.activeEditor) != null)
					oed.onDeactivate.dispatch(oed, t);

				t.onActivate.dispatch(t, oed);
			}

			EditorManager._setActive(t);
		},

		execCallback : function(n) {
			var t = this, f = t.settings[n], s;

			if (!f)
				return;

			// Look through lookup
			if (t.callbackLookup && (s = t.callbackLookup[n])) {
				f = s.func;
				s = s.scope;
			}

			if (is(f, 'string')) {
				s = f.replace(/\.\w+$/, '');
				s = s ? tinymce.resolve(s) : 0;
				f = tinymce.resolve(f);
				t.callbackLookup = t.callbackLookup || {};
				t.callbackLookup[n] = {func : f, scope : s};
			}

			return f.apply(s || t, Array.prototype.slice.call(arguments, 1));
		},

		translate : function(s) {
			var c = this.settings.language, i18n = EditorManager.i18n;

			if (!s)
				return '';

			return i18n[c + '.' + s] || s.replace(/{\#([^}]+)\}/g, function(a, b) {
				return i18n[c + '.' + b] || '{#' + b + '}';
			});
		},

		getLang : function(n, dv) {
			return EditorManager.i18n[this.settings.language + '.' + n] || (is(dv) ? dv : '{#' + n + '}');
		},

		getParam : function(n, dv) {
			return is(this.settings[n]) ? this.settings[n] : dv;
		},

		nodeChanged : function(o) {
			var t = this, s = t.selection, n = s.getNode() || this.getBody();

			this.onNodeChange.dispatch(
				t,
				o ? o.controlManager || t.controlManager : t.controlManager,
				isIE && n.ownerDocument != t.getDoc() ? this.getBody() : n, // Fix for IE initial state
				s.isCollapsed(),
				o
			);
		},

		addButton : function(n, s) {
			var t = this;

			t.buttons = t.buttons || {};
			t.buttons[n] = s;
		},

		addCommand : function(n, f, s) {
			this.execCommands[n] = {func : f, scope : s || this};
		},

		addQueryStateHandler : function(n, f, s) {
			this.queryStateCommands[n] = {func : f, scope : s || this};
		},

		addQueryValueHandler : function(n, f, s) {
			this.queryValueCommands[n] = {func : f, scope : s || this};
		},

		addShortcut : function(pa, desc, cmd_func, sc) {
			var t = this, c;

			if (!t.settings.custom_shortcuts)
				return false;

			t.shortcuts = t.shortcuts || {};

			if (is(cmd_func, 'string')) {
				c = cmd_func;

				cmd_func = function() {
					t.execCommand(c, false, null);
				};
			}

			if (is(cmd_func, 'object')) {
				c = cmd_func;

				cmd_func = function() {
					t.execCommand(c[0], c[1], c[2]);
				};
			}

			each(pa.split(','), function(pa) {
				var o = {
					func : cmd_func,
					scope : sc || this,
					desc : desc,
					alt : false,
					ctrl : false,
					shift : false
				};

				each(pa.split('+'), function(v) {
					switch (v) {
						case 'alt':
						case 'ctrl':
						case 'shift':
							o[v] = true;
							break;

						default:
							o.charCode = v.charCodeAt(0);
							o.keyCode = v.toUpperCase().charCodeAt(0);
					}
				});

				t.shortcuts[(o.ctrl ? 'ctrl' : '') + ',' + (o.alt ? 'alt' : '') + ',' + (o.shift ? 'shift' : '') + ',' + o.keyCode] = o;
			});

			return true;
		},

		execCommand : function(cmd, ui, val, a) {
			var t = this, s = 0, o;

			if (!/^(mceAddUndoLevel|mceEndUndoLevel|mceBeginUndoLevel|mceRepaint|SelectAll)$/.test(cmd) && (!a || !a.skip_focus))
				t.focus();

			o = {};
			t.onBeforeExecCommand.dispatch(t, cmd, ui, val, o);
			if (o.terminate)
				return false;

			// Comamnd callback
			if (t.execCallback('execcommand_callback', null, t.id, t.selection.getNode(), cmd, ui, val)) {
				t.onExecCommand.dispatch(t, cmd, ui, val);
				return true;
			}

			// Registred commands
			if (o = t.execCommands[cmd]) {
				s = o.func.call(o.scope, ui, val);
				t.onExecCommand.dispatch(t, cmd, ui, val);
				return s;
			}

			// Plugin commands
			each(t.plugins, function(p) {
				if (p.execCommand && p.execCommand(cmd, ui, val)) {
					t.onExecCommand.dispatch(t, cmd, ui, val);
					s = 1;
					return false;
				}
			});

			if (s)
				return true;

			// Theme commands
			if (t.theme.execCommand && t.theme.execCommand(cmd, ui, val)) {
				t.onExecCommand.dispatch(t, cmd, ui, val);
				return true;
			}

			// Editor commands
			if (t.editorCommands.execCommand(cmd, ui, val)) {
				t.onExecCommand.dispatch(t, cmd, ui, val);
				return true;
			}

			// Browser commands
			t.getDoc().execCommand(cmd, ui, val);
			t.onExecCommand.dispatch(t, cmd, ui, val);
		},

		queryCommandState : function(c) {
			var t = this, o;

			// Is hidden then return undefined
			if (t._isHidden())
				return;

			// Registred commands
			if (o = t.queryStateCommands[c])
				return o.func.call(o.scope);

			// Registred commands
			o = t.editorCommands.queryCommandState(c);
			if (o !== -1)
				return o;

			// Browser commands
			return this.getDoc().queryCommandState(c);
		},

		queryCommandValue : function(c) {
			var t = this, o;

			// Is hidden then return undefined
			if (t._isHidden())
				return;

			// Registred commands
			if (o = t.queryValueCommands[c])
				return o.func.call(o.scope);

			// Registred commands
			o = t.editorCommands.queryCommandValue(c);
			if (is(o))
				return o;

			// Browser commands
			return this.getDoc().queryCommandValue(c);
		},

		show : function() {
			var t = this;

			DOM.show(t.getContainer());
			DOM.hide(t.id);
			t.load();
		},

		hide : function() {
			var t = this, s = t.settings, d = t.getDoc();

			// Fixed bug where IE has a blinking cursor left from the editor
			if (isIE && d)
				d.execCommand('SelectAll');

			DOM.hide(t.getContainer());
			DOM.setStyle(s.id, 'display', t.orgDisplay);
			t.save();
		},

		isHidden : function() {
			return !DOM.isHidden(this.id);
		},

		setProgressState : function(b, ti, o) {
			this.onSetProgressState.dispatch(this, b, ti, o);

			return b;
		},

		remove : function() {
			var t = this;

			t.removed = 1; // Cancels post remove event execution
			t.hide();
			DOM.remove(t.getContainer());

			t.execCallback('remove_instance_callback', t);
			t.onRemove.dispatch(t);

			EditorManager.remove(t);
		},

		resizeToContent : function() {
			var t = this;

			DOM.setStyle(t.id + "_ifr", 'height', t.getBody().scrollHeight);
		},

		load : function(o) {
			var t = this, e = t.getElement(), h;

			o = o || {};
			o.load = true;

			h = t.setContent(is(e.value) ? e.value : e.innerHTML, o);
			o.element = e;

			if (!o.no_events)
				t.onLoadContent.dispatch(t, o);

			o.element = e = null;

			return h;
		},

		save : function(o) {
			var t = this, e = t.getElement(), h, f;

			if (!t.initialized)
				return;

			o = o || {};
			o.save = true;

			o.element = e;
			h = o.content = t.getContent(o);

			if (!o.no_events)
				t.onSaveContent.dispatch(t, o);

			h = o.content;

			if (!/TEXTAREA|INPUT/i.test(e.nodeName)) {
				e.innerHTML = h;

				// Update hidden form element
				if (f = DOM.getParent(t.id, 'form')) {
					each(f.elements, function(e) {
						if (e.name == t.id) {
							e.value = h;
							return false;
						}
					});
				}
			} else
				e.value = h;

			o.element = e = null;

			return h;
		},

		setContent : function(h, o) {
			var t = this;

			o = o || {};
			o.format = o.format || 'html';
			o.set = true;
			o.content = h;

			if (!o.no_events)
				t.onBeforeSetContent.dispatch(t, o);

			// Padd empty content in Gecko and Safari. Commands will otherwise fail on the content
			// It will also be impossible to place the caret in the editor unless there is a BR element present
			if (!tinymce.isIE && (h.length === 0 || /^\s+$/.test(h))) {
				o.content = t.dom.setHTML(t.getBody(), '<br mce_bogus="1" />', 1);
				o.format = 'raw';
			}

			o.content = t.dom.setHTML(t.getBody(), o.content);

			if (o.format != 'raw' && t.settings.cleanup) {
				o.getInner = true;
				o.content = t.dom.setHTML(t.getBody(), t.serializer.serialize(t.getBody(), o));
			}

			if (!o.no_events)
				t.onSetContent.dispatch(t, o);

			return o.content;
		},

		getContent : function(o) {
			var t = this, h;

			o = o || {};
			o.format = o.format || 'html';
			o.get = true;

			if (!o.no_events)
				t.onBeforeGetContent.dispatch(t, o);

			if (o.format != 'raw' && t.settings.cleanup) {
				o.getInner = true;
				h = t.serializer.serialize(t.getBody(), o);
			} else
				h = t.getBody().innerHTML;

			h = h.replace(/^\s*|\s*$/g, '');
			o = {content : h};
			t.onGetContent.dispatch(t, o);

			return o.content;
		},

		isDirty : function() {
			var t = this;

			return tinymce.trim(t.startContent) != tinymce.trim(t.getContent({format : 'raw', no_events : 1})) && !t.isNotDirty;
		},

		getContainer : function() {
			var t = this;

			if (!t.container)
				t.container = DOM.get(t.editorContainer || t.id + '_parent');

			return t.container;
		},

		getContentAreaContainer : function() {
			return this.contentAreaContainer;
		},

		getElement : function() {
			return DOM.get(this.settings.content_element || this.id);
		},

		getWin : function() {
			var t = this, e;

			if (!t.contentWindow) {
				e = DOM.get(t.id + "_ifr");

				if (e)
					t.contentWindow = e.contentWindow;
			}

			return t.contentWindow;
		},

		getDoc : function() {
			var t = this, w;

			if (!t.contentDocument) {
				w = this.getWin();

				if (w)
					t.contentDocument = w.document;
			}

			return t.contentDocument;
		},

		getBody : function() {
			return this.bodyElement || this.getDoc().body;
		},

		convertURL : function(u, n, e) {
			var t = this, s = t.settings;

			// Use callback instead
			if (s.urlconverter_callback)
				return t.execCallback('urlconverter_callback', u, e, true, n);

			// Don't convert link href since thats the CSS files that gets loaded into the editor
			if (!s.convert_urls || (e && e.nodeName == 'LINK'))
				return u;

			// Convert to relative
			if (s.relative_urls)
				return t.documentBaseURI.toRelative(u);

			// Convert to absolute
			u = t.documentBaseURI.toAbsolute(u, s.remove_script_host);

			return u;
		},

		addVisual : function(e) {
			var t = this, s = t.settings;

			e = e || t.getBody();

			if (!is(t.hasVisual))
				t.hasVisual = s.visual;

			each(t.dom.select('table,a', e), function(e) {
				var v;

				switch (e.nodeName) {
					case 'TABLE':
						v = t.dom.getAttrib(e, 'border');

						if (!v || v == '0') {
							if (t.hasVisual)
								t.dom.addClass(e, s.visual_table_class);
							else
								t.dom.removeClass(e, s.visual_table_class);
						}

						return;

					case 'A':
						v = t.dom.getAttrib(e, 'name');

						if (v) {
							if (t.hasVisual)
								t.dom.addClass(e, 'mceItemAnchor');
							else
								t.dom.removeClass(e, 'mceItemAnchor');
						}

						return;
				}
			});

			t.onVisualAid.dispatch(t, e, t.hasVisual);
		},

		// Internal functions

		_addEvents : function() {
			// 'focus', 'blur', 'dblclick', 'beforedeactivate', submit, reset
			var t = this, i, s = t.settings, lo = {
				mouseup : 'onMouseUp',
				mousedown : 'onMouseDown',
				click : 'onClick',
				keyup : 'onKeyUp',
				keydown : 'onKeyDown',
				keypress : 'onKeyPress',
				submit : 'onSubmit',
				reset : 'onReset',
				contextmenu : 'onContextMenu',
				dblclick : 'onDblClick',
				paste : 'onPaste' // Doesn't work in all browsers yet
			};

			function eventHandler(e, o) {
				var ty = e.type;

				// Don't fire events when it's removed
				if (t.removed)
					return;

				// Generic event handler
				if (t.onEvent.dispatch(t, e, o) !== false) {
					// Specific event handler
					t[lo[e.fakeType || e.type]].dispatch(t, e, o);
				}
			};

			// Add DOM events
			each(lo, function(v, k) {
				switch (k) {
					case 'contextmenu':
						if (tinymce.isOpera) {
							// Fake contextmenu on Opera
							Event.add(t.getDoc(), 'mousedown', function(e) {
								if (e.ctrlKey) {
									e.fakeType = 'contextmenu';
									eventHandler(e);
								}
							});
						} else
							Event.add(t.getDoc(), k, eventHandler);
						break;

					case 'paste':
						Event.add(t.getBody(), k, function(e) {
							var tx, h, el, r;

							// Get plain text data
							if (e.clipboardData)
								tx = e.clipboardData.getData('text/plain');
							else if (tinymce.isIE)
								tx = t.getWin().clipboardData.getData('Text');

							// Get HTML data
							/*if (tinymce.isIE) {
								el = DOM.add(document.body, 'div', {style : 'visibility:hidden;overflow:hidden;position:absolute;width:1px;height:1px'});
								r = document.body.createTextRange();
								r.moveToElementText(el);
								r.execCommand('Paste');
								h = el.innerHTML;
								DOM.remove(el);
							}*/

							eventHandler(e, {text : tx, html : h});
						});
						break;

					case 'submit':
					case 'reset':
						Event.add(t.getElement().form || DOM.getParent(t.id, 'form'), k, eventHandler);
						break;

					default:
						Event.add(s.content_editable ? t.getBody() : t.getDoc(), k, eventHandler);
				}
			});

			Event.add(s.content_editable ? t.getBody() : (isGecko ? t.getDoc() : t.getWin()), 'focus', function(e) {
				t.focus(true);
			});

			// Fixes bug where a specified document_base_uri could result in broken images
			// This will also fix drag drop of images in Gecko
			if (tinymce.isGecko) {
				// Convert all images to absolute URLs
/*				t.onSetContent.add(function(ed, o) {
					each(ed.dom.select('img'), function(e) {
						var v;

						if (v = e.getAttribute('mce_src'))
							e.src = t.documentBaseURI.toAbsolute(v);
					})
				});*/

				Event.add(t.getDoc(), 'DOMNodeInserted', function(e) {
					var v;

					e = e.target;

					if (e.nodeType === 1 && e.nodeName === 'IMG' && (v = e.getAttribute('mce_src')))
						e.src = t.documentBaseURI.toAbsolute(v);
				});
			}

			// Set various midas options in Gecko
			if (isGecko) {
				function setOpts() {
					var t = this, d = t.getDoc(), s = t.settings;

					if (isGecko) {
						if (t._isHidden()) {
							try {
								d.designMode = 'On';
							} catch (ex) {
								// Fails if it's hidden
							}
						}

						try {
							// Try new Gecko method
							d.execCommand("styleWithCSS", 0, false);
						} catch (ex) {
							// Use old method
							if (!t._isHidden())
								d.execCommand("useCSS", 0, true);
						}

						if (!s.table_inline_editing)
							try {d.execCommand('enableInlineTableEditing', false, false);} catch (ex) {}

						if (!s.object_resizing)
							try {d.execCommand('enableObjectResizing', false, false);} catch (ex) {}
					}
				};

				t.onBeforeExecCommand.add(setOpts);
				t.onMouseDown.add(setOpts);
			}

			// Add node change handlers
			t.onMouseUp.add(t.nodeChanged);
			t.onClick.add(t.nodeChanged);
			t.onKeyUp.add(function(ed, e) {
				if ((e.keyCode >= 33 && e.keyCode <= 36) || (e.keyCode >= 37 && e.keyCode <= 40) || e.keyCode == 13 || e.keyCode == 45 || e.keyCode == 46 || e.keyCode == 8 || e.ctrlKey)
					t.nodeChanged();
			});

			// Add reset handler
			t.onReset.add(function() {
				t.setContent(t.startContent, {format : 'raw'});
			});

			if (t.getParam('tab_focus')) {
				function tabCancel(ed, e) {
					if (e.keyCode === 9)
						return Event.cancel(e);
				};

				function tabHandler(ed, e) {
					var v, f, el;

					if (e.keyCode === 9) {
						v = ed.getParam('tab_focus');

						if (v == ':next') {
							f = DOM.getParent(ed.id, 'form');

							if (f) {
								each(f.elements, function(e, i) {
									if (e.id == ed.id)
										el = f.elements[i + 1];
								});
							}
						} else
							el = DOM.get(v);

						if (el) {
							window.setTimeout(function() {window.focus();el.focus();}, 10);
							return Event.cancel(e);
						}
					}
				};

				t.onKeyUp.add(tabCancel);

				if (isGecko) {
					t.onKeyPress.add(tabHandler);
					t.onKeyDown.add(tabCancel);
				} else
					t.onKeyDown.add(tabHandler);
			}

			// Add shortcuts
			if (s.custom_shortcuts) {
				if (s.custom_undo_redo_keyboard_shortcuts) {
					t.addShortcut('ctrl+z', t.getLang('undo_desc'), 'Undo');
					t.addShortcut('ctrl+y', t.getLang('redo_desc'), 'Redo');
				}

				// Add default shortcuts for gecko
				if (isGecko) {
					t.addShortcut('ctrl+b', t.getLang('bold_desc'), 'Bold');
					t.addShortcut('ctrl+i', t.getLang('italic_desc'), 'Italic');
					t.addShortcut('ctrl+u', t.getLang('underline_desc'), 'Underline');
				}

				// BlockFormat shortcuts keys
				for (i=1; i<=6; i++)
					t.addShortcut('ctrl+' + i, '', ['FormatBlock', false, '<h' + i + '>']);

				t.addShortcut('ctrl+7', '', ['FormatBlock', false, '<p>']);
				t.addShortcut('ctrl+8', '', ['FormatBlock', false, '<div>']);
				t.addShortcut('ctrl+9', '', ['FormatBlock', false, '<address>']);

				function find(e) {
					var v = null;

					if (!e.altKey && !e.ctrlKey && !e.metaKey)
						return v;

					each(t.shortcuts, function(o) {
						if (o.ctrl != e.ctrlKey && (!tinymce.isMac || o.ctrl == e.metaKey))
							return;

						if (o.alt != e.altKey)
							return;

						if (o.shift != e.shiftKey)
							return;

						if (e.keyCode == o.keyCode || (e.charCode && e.charCode == o.charCode)) {
							v = o;
							return false;
						}
					});

					return v;
				};

				t.onKeyUp.add(function(ed, e) {
					var o = find(e);

					if (o)
						return Event.cancel(e);
				});

				t.onKeyPress.add(function(ed, e) {
					var o = find(e);

					if (o)
						return Event.cancel(e);
				});

				t.onKeyDown.add(function(ed, e) {
					var o = find(e);

					if (o) {
						o.func.call(o.scope);
						return Event.cancel(e);
					}
				});
			}

			if (tinymce.isIE) {
				// Fix so resize will only update the width and height attributes not the styles of an image
				// It will also block mceItemNoResize items
				Event.add(t.getDoc(), 'controlselect', function(e) {
					var re = t.resizeInfo, cb;

					e = e.target;

					if (re)
						Event.remove(re.node, re.ev, re.cb);

					if (!t.dom.hasClass(e, 'mceItemNoResize')) {
						ev = 'resizeend';
						cb = Event.add(e, ev, function(e) {
							var v;

							e = e.target;

							if (v = t.dom.getStyle(e, 'width')) {
								t.dom.setAttrib(e, 'width', v.replace(/[^0-9%]+/g, ''));
								t.dom.setStyle(e, 'width', '');
							}

							if (v = t.dom.getStyle(e, 'height')) {
								t.dom.setAttrib(e, 'height', v.replace(/[^0-9%]+/g, ''));
								t.dom.setStyle(e, 'height', '');
							}
						});
					} else {
						ev = 'resizestart';
						cb = Event.add(e, 'resizestart', Event.cancel, Event);
					}

					re = t.resizeInfo = {
						node : e,
						ev : ev,
						cb : cb
					};
				});

				t.onKeyDown.add(function(ed, e) {
					switch (e.keyCode) {
						case 8:
							// Fix IE control + backspace browser bug
							if (t.selection.getRng().item) {
								t.selection.getRng().item(0).removeNode();
								return Event.cancel(e);
							}
					}
				});
			}

			if (tinymce.isOpera) {
				t.onClick.add(function(ed, e) {
					Event.prevent(e);
				});
			}

			// Add custom undo/redo handlers
			if (s.custom_undo_redo) {
				function addUndo() {
					t.undoManager.typing = 0;
					t.undoManager.add();
				};

				// Add undo level on editor blur
				if (tinymce.isIE) {
					Event.add(t.getWin(), 'blur', function(e) {
						var n;

						// Check added for fullscreen bug
						if (t.selection) {
							n = t.selection.getNode();

							// Add undo level is selection was lost to another document
							if (!t.removed && n.ownerDocument && n.ownerDocument != t.getDoc())
								addUndo();
						}
					});
				} else {
					Event.add(t.getDoc(), 'blur', function() {
						if (t.selection && !t.removed)
							addUndo();
					});
				}

				t.onMouseDown.add(addUndo);

				t.onKeyUp.add(function(ed, e) {
					if ((e.keyCode >= 33 && e.keyCode <= 36) || (e.keyCode >= 37 && e.keyCode <= 40) || e.keyCode == 13 || e.keyCode == 45 || e.ctrlKey) {
						t.undoManager.typing = 0;
						t.undoManager.add();
					}
				});

				t.onKeyDown.add(function(ed, e) {
					// Is caracter positon keys
					if ((e.keyCode >= 33 && e.keyCode <= 36) || (e.keyCode >= 37 && e.keyCode <= 40) || e.keyCode == 13 || e.keyCode == 45) {
						if (t.undoManager.typing) {
							t.undoManager.add();
							t.undoManager.typing = 0;
						}

						return;
					}

					if (!t.undoManager.typing) {
						t.undoManager.add();
						t.undoManager.typing = 1;
					}
				});
			}
		},

		_destroy : function() {
			var t = this;

			if (t.formElement) {
				t.formElement.submit = t.formElement._mceOldSubmit;
				t.formElement._mceOldSubmit = null;
			}

			t.contentAreaContainer = t.formElement = t.container = t.contentDocument = t.contentWindow = null;

			if (t.selection)
				t.selection = t.selection.win = t.selection.dom = t.selection.dom.doc = null;

			t.destroyed = 1;
		},

		_convertInlineElements : function() {
			var t = this, s = t.settings, dom = t.dom;

			function convert(ed, o) {
				if (!s.inline_styles)
					return;

				if (o.get) {
					each(t.dom.select('table,u,strike', o.node), function(n) {
						switch (n.nodeName) {
							case 'TABLE':
								if (v = dom.getAttrib(n, 'height')) {
									dom.setStyle(n, 'height', v);
									dom.setAttrib(n, 'height', '');
								}
								break;

							case 'U':
								dom.replace(dom.create('span', {style : 'text-decoration: underline;'}), n, 1);
								break;

							case 'STRIKE':
								dom.replace(dom.create('span', {style : 'text-decoration: line-through;'}), n, 1);
								break;
						}
					});
				} else if (o.set) {
					each(t.dom.select('table,span', o.node), function(n) {
						if (n.nodeName == 'TABLE') {
							if (v = dom.getStyle(n, 'height'))
								dom.setAttrib(n, 'height', v.replace(/[^0-9%]+/g, ''));
						} else {
							// Convert spans to elements
							if (n.style.textDecoration == 'underline')
								dom.replace(dom.create('u'), n, 1);
							else if (n.style.textDecoration == 'line-through')
								dom.replace(dom.create('strike'), n, 1);
						}
					});
				}
			};

			t.onPreProcess.add(convert);

			if (!s.cleanup_on_startup) {
				t.onInit.add(function() {
					convert(t, {node : t.getBody(), set : 1});
				});
			}
		},

		_convertFonts : function() {
			var t = this, s = t.settings, dom = t.dom, sl, cl, fz, fzn, v, i;

			// Font pt values and font size names
			fz = [8, 10, 12, 14, 18, 24, 36];
			fzn = ['xx-small', 'x-small','small','medium','large','x-large', 'xx-large'];

			if (sl = s.font_size_style_values)
				sl = sl.split(',');

			if (cl = s.font_size_classes)
				cl = cl.split(',');

			t.onPreProcess.add(function(ed, o) {
				if (!s.inline_styles)
					return;

				if (o.set) {
					// Convert spans to fonts on non WebKit browsers
					if (tinymce.isWebKit)
						return;

					each(t.dom.select('span', o.node), function(n) {
						var f = dom.create('font', {
							color : dom.toHex(dom.getStyle(n, 'color')),
							face : dom.getStyle(n, 'fontFamily')
						});

						if (sl) {
							i = inArray(sl, dom.getStyle(n, 'fontSize'));

							if (i != -1)
								dom.setAttrib(f, 'size', '' + (i + 1 || 1));
						} else if (cl) {
							i = inArray(cl, dom.getAttrib(n, 'class'));

							v = dom.getStyle(n, 'fontSize');

							if (i == -1 && v.indexOf('pt') > 0)
								i = inArray(fz, parseInt(v));

							if (i == -1)
								i = inArray(fzn, v);

							if (i != -1)
								dom.setAttrib(f, 'size', '' + (i + 1 || 1));
						}

						if (f.color || f.face || f.size)
							dom.replace(f, n, 1);
					});
				} else if (o.get) {
					each(t.dom.select('font', o.node), function(n) {
						var sp = dom.create('span', {
							style : {
								fontFamily : dom.getAttrib(n, 'face'),
								color : dom.getAttrib(n, 'color'),
								backgroundColor : n.style.backgroundColor
							}
						});

						if (n.size) {
							if (sl)
								dom.setStyle(sp, 'fontSize', sl[parseInt(n.size) - 1]);
							else
								dom.setAttrib(sp, 'class', cl[parseInt(n.size) - 1]);
						}

						dom.replace(sp, n, 1);
					});
				}
			});
		},

		_isHidden : function() {
			var s;

			if (!isGecko)
				return 0;

			// Weird, wheres that cursor selection?
			s = this.selection.getSel();
			return (!s || !s.rangeCount || s.rangeCount == 0);
		},

		_fixNesting : function(s) {
			var d = [], i;

			s = s.replace(/<(\/)?([^\s>]+)[^>]*?>/g, function(a, b, c) {
				var e;

				// Handle end element
				if (b === '/') {
					if (!d.length)
						return '';

					if (c !== d[d.length - 1].tag) {
						for (i=d.length - 1; i>=0; i--) {
							if (d[i].tag === c) {
								d[i].close = 1;
								break;
							}
						}

						return '';
					} else {
						d.pop();

						if (d.length && d[d.length - 1].close) {
							a = a + '</' + d[d.length - 1].tag + '>';
							d.pop();
						}
					}
				} else {
					// Ignore these
					if (/^(br|hr|input|meta|img|link|param)$/i.test(c))
						return a;

					// Ignore closed ones
					if (/\/>$/.test(a))
						return a;

					d.push({tag : c}); // Push start element
				}

				return a;
			});

			// End all open tags
			for (i=d.length - 1; i>=0; i--)
				s += '</' + d[i].tag + '>';

			return s;
		}

		});
})();

/* file:jscripts/tiny_mce/classes/EditorCommands.js */

(function() {
	var each = tinymce.each, isIE = tinymce.isIE, isGecko = tinymce.isGecko, isOpera = tinymce.isOpera, isWebKit = tinymce.isWebKit;

	tinymce.create('tinymce.EditorCommands', {
		EditorCommands : function(ed) {
			this.editor = ed;
		},

		execCommand : function(cmd, ui, val) {
			var t = this, ed = t.editor, f;

			switch (cmd) {
				case 'Cut':
				case 'Copy':
				case 'Paste':
					try {
						ed.getDoc().execCommand(cmd, ui, val);
					} catch (ex) {
						if (isGecko) {
							ed.windowManager.confirm(ed.getLang('clipboard_msg'), function(s) {
								if (s)
									window.open('http://www.mozilla.org/editor/midasdemo/securityprefs.html', 'mceExternal');
							});
						} else
							ed.windowManager.alert(ed.getLang('clipboard_no_support'));
					}

					return true;

				// Ignore these
				case 'mceResetDesignMode':
				case 'mceBeginUndoLevel':
					return true;

				// Ignore these
				case 'unlink':
					t.UnLink();
					return true;

				// Bundle these together
				case 'JustifyLeft':
				case 'JustifyCenter':
				case 'JustifyRight':
				case 'JustifyFull':
					t.mceJustify(cmd, cmd.substring(7).toLowerCase());
					return true;

				case 'mceEndUndoLevel':
				case 'mceAddUndoLevel':
					ed.undoManager.add();
					return true;

				default:
					f = this[cmd];

					if (f) {
						f.call(this, ui, val);
						return true;
					}
			}

			return false;
		},

		Indent : function() {
			var ed = this.editor, d = ed.dom, s = ed.selection, e, iv, iu;

			// Setup indent level
			iv = ed.settings.indentation;
			iu = /[a-z%]+$/i.exec(iv);
			iv = parseInt(iv);

			if (ed.settings.inline_styles && (!this.queryStateInsertUnorderedList() && !this.queryStateInsertOrderedList())) {
				each(this._getSelectedBlocks(), function(e) {
					d.setStyle(e, 'paddingLeft', (parseInt(e.style.paddingLeft || 0) + iv) + iu);
				});

				return;
			}

			ed.getDoc().execCommand('Indent', false, null);

			if (isIE) {
				d.getParent(s.getNode(), function(n) {
					if (n.nodeName == 'BLOCKQUOTE') {
						n.dir = n.style.cssText = '';
					}
				});
			}
		},

		Outdent : function() {
			var ed = this.editor, d = ed.dom, s = ed.selection, e, v, iv, iu;

			// Setup indent level
			iv = ed.settings.indentation;
			iu = /[a-z%]+$/i.exec(iv);
			iv = parseInt(iv);

			if (ed.settings.inline_styles && (!this.queryStateInsertUnorderedList() && !this.queryStateInsertOrderedList())) {
				each(this._getSelectedBlocks(), function(e) {
					v = Math.max(0, parseInt(e.style.paddingLeft || 0) - iv);
					d.setStyle(e, 'paddingLeft', v ? v + iu : '');
				});

				return;
			}

			ed.getDoc().execCommand('Outdent', false, null);
		},

		mceSetAttribute : function(u, v) {
			var ed = this.editor, d = ed.dom, e;

			if (e = d.getParent(ed.selection.getNode(), d.isBlock))
				d.setAttrib(e, v.name, v.value);
		},

		mceSetContent : function(u, v) {
			this.editor.setContent(v);
		},

		mceToggleVisualAid : function() {
			var ed = this.editor;

			ed.hasVisual = !ed.hasVisual;
			ed.addVisual();
		},

		mceReplaceContent : function(u, v) {
			var s = this.editor.selection;

			s.setContent(v.replace(/\{\$selection\}/g, s.getContent({format : 'text'})));
		},

		mceInsertLink : function(u, v) {
			var ed = this.editor, e = ed.dom.getParent(ed.selection.getNode(), 'A');

			if (tinymce.is(v, 'string'))
				v = {href : v};

			function set(e) {
				each(v, function(v, k) {
					ed.dom.setAttrib(e, k, v);
				});
			};

			if (!e) {
				ed.execCommand('CreateLink', false, 'javascript:mctmp(0);');
				each(ed.dom.select('a'), function(e) {
					if (e.href == 'javascript:mctmp(0);')
						set(e);
				});
			} else {
				if (v.href)
					set(e);
				else
					ed.dom.remove(e, 1);
			}
		},

		UnLink : function() {
			var ed = this.editor, s = ed.selection;

			if (s.isCollapsed())
				s.select(s.getNode());

			ed.getDoc().execCommand('unlink', false, null);
			s.collapse(0);
		},

		FontName : function(u, v) {
			var t = this, ed = t.editor, s = ed.selection, e;

			if (!v) {
				if (s.isCollapsed())
					s.select(s.getNode());

				t.RemoveFormat();
			} else
				ed.getDoc().execCommand('FontName', false, v);
		},

		queryCommandValue : function(c) {
			var f = this['queryValue' + c];

			if (f)
				return f.call(this, c);

			return false;
		},

		queryCommandState : function(cmd) {
			var f;

			switch (cmd) {
				// Bundle these together
				case 'JustifyLeft':
				case 'JustifyCenter':
				case 'JustifyRight':
				case 'JustifyFull':
					return this.queryStateJustify(cmd, cmd.substring(7).toLowerCase());

				default:
					if (f = this['queryState' + cmd])
						return f.call(this, cmd);
			}

			return -1;
		},

		queryValueFontSize : function() {
			var ed = this.editor, v = 0, p;

			if (isOpera || isWebKit) {
				if (p = ed.dom.getParent(ed.selection.getNode(), 'FONT'))
					v = p.size;

				return v;
			}

			return ed.getDoc().queryCommandValue('FontSize');
		},

		queryValueFontName : function() {
			var ed = this.editor, v = 0, p;

			if (p = ed.dom.getParent(ed.selection.getNode(), 'FONT'))
				v = p.face;

			if (!v)
				v = ed.getDoc().queryCommandValue('FontName');

			return v;
		},

		mceJustify : function(c, v) {
			var ed = this.editor, se = ed.selection, n = se.getNode(), nn = n.nodeName, bl, nb, dom = ed.dom, rm;

			if (ed.settings.inline_styles && this.queryStateJustify(c, v))
				rm = 1;

			bl = dom.getParent(n, ed.dom.isBlock);

			if (nn == 'IMG') {
				if (v == 'full')
					return;

				if (rm) {
					dom.setStyle(n, 'float', '');
					this.mceRepaint();
					return;
				}

				if (v == 'center') {
					if (!bl || bl.childNodes.length > 1) {
						nb = dom.create('p');
						nb.appendChild(n.cloneNode(false));

						if (bl)
							dom.insertAfter(nb, bl);
						else
							dom.insertAfter(nb, n);

						dom.remove(n);
						n = nb.firstChild;
						bl = nb;
					}

					dom.setStyle(bl, 'textAlign', v);
					dom.setStyle(n, 'float', '');
				} else
					dom.setStyle(n, 'float', v);

				this.mceRepaint();
				return;
			}

			// Handle the alignment outselfs, less quirks in all browsers
			if (ed.settings.inline_styles && ed.settings.forced_root_block) {
				if (rm)
					v = '';

				each(this._getSelectedBlocks(dom.getParent(se.getStart(), dom.isBlock), dom.getParent(se.getEnd(), dom.isBlock)), function(e) {
					dom.setAttrib(e, 'align', '');
					dom.setStyle(e, 'textAlign', v == 'full' ? 'justify' : v);
				});

				return;
			} else if (!rm)
				ed.getDoc().execCommand(c, false, null);

			if (ed.settings.inline_styles) {
				if (rm) {
					dom.getParent(ed.selection.getNode(), function(n) {
						if (n.style && n.style.textAlign)
							dom.setStyle(n, 'textAlign', '');
					});

					return;
				}

				each(dom.select('*'), function(n) {
					var v = n.align;

					if (v) {
						if (v == 'full')
							v = 'justify';

						dom.setStyle(n, 'textAlign', v);
						dom.setAttrib(n, 'align', '');
					}
				});
			}
		},

		mceSetCSSClass : function(u, v) {
			this.mceSetStyleInfo(0, {command : 'setattrib', name : 'class', value : v});
		},

		getSelectedElement : function() {
			var t = this, ed = t.editor, dom = ed.dom, se = ed.selection, r = se.getRng(), r1, r2, sc, ec, so, eo, e, sp, ep, re;

			if (se.isCollapsed() || r.item)
				return se.getNode();

			// Setup regexp
			re = ed.settings.merge_styles_invalid_parents;
			if (tinymce.is(re, 'string'))
				re = new RegExp(re, 'i');

			if (isIE) {
				r1 = r.duplicate();
				r1.collapse(true);
				sc = r1.parentElement();

				r2 = r.duplicate();
				r2.collapse(false);
				ec = r2.parentElement();

				if (sc != ec) {
					r1.move('character', 1);
					sc = r1.parentElement();
				}

				if (sc == ec) {
					r1 = r.duplicate();
					r1.moveToElementText(sc);

					if (r1.compareEndPoints('StartToStart', r) == 0 && r1.compareEndPoints('EndToEnd', r) == 0)
						return re && re.test(sc.nodeName) ? null : sc;
				}
			} else {
				function getParent(n) {
					return dom.getParent(n, function(n) {return n.nodeType == 1;});
				};

				sc = r.startContainer;
				ec = r.endContainer;
				so = r.startOffset;
				eo = r.endOffset;

				if (!r.collapsed) {
					if (sc == ec) {
						if (so - eo < 2) {
							if (sc.hasChildNodes()) {
								sp = sc.childNodes[so];
								return re && re.test(sp.nodeName) ? null : sp;
							}
						}
					}
				}

				if (sc.nodeType != 3 || ec.nodeType != 3)
					return null;

				if (so == 0) {
					sp = getParent(sc);

					if (sp && sp.firstChild != sc)
						sp = null;
				}

				if (so == sc.nodeValue.length) {
					e = sc.nextSibling;

					if (e && e.nodeType == 1)
						sp = sc.nextSibling;
				}

				if (eo == 0) {
					e = ec.previousSibling;

					if (e && e.nodeType == 1)
						ep = e;
				}

				if (eo == ec.nodeValue.length) {
					ep = getParent(ec);

					if (ep && ep.lastChild != ec)
						ep = null;
				}

				// Same element
				if (sp == ep)
					return re && sp && re.test(sp.nodeName) ? null : sp;
			}

			return null;
		},

		InsertHorizontalRule : function() {
			// Fix for Gecko <hr size="1" /> issue and IE bug rep(/<a.*?href=\"(.*?)\".*?>(.*?)<\/a>/gi,"[url=$1]$2[/url]");
			if (isGecko || isIE)
				this.editor.selection.setContent('<hr />');
			else
				this.editor.getDoc().execCommand('InsertHorizontalRule', false, '');
		},

		RemoveFormat : function() {
			var t = this, ed = t.editor, s = ed.selection, b;

			// Safari breaks tables
			if (isWebKit)
				s.setContent(s.getContent({format : 'raw'}).replace(/(<(span|b|i|strong|em|strike) [^>]+>|<(span|b|i|strong|em|strike)>|<\/(span|b|i|strong|em|strike)>|)/g, ''), {format : 'raw'});
			else
				ed.getDoc().execCommand('RemoveFormat', false, null);

			t.mceSetStyleInfo(0, {command : 'removeformat'});
			ed.addVisual();
		},

		mceSetStyleInfo : function(u, v) {
			var t = this, ed = t.editor, d = ed.getDoc(), dom = ed.dom, e, b, s = ed.selection, nn = v.wrapper || 'span', b = s.getBookmark(), re;

			function set(n, e) {
				if (n.nodeType == 1) {
					switch (v.command) {
						case 'setattrib':
							return dom.setAttrib(n, v.name, v.value);

						case 'setstyle':
							return dom.setStyle(n, v.name, v.value);

						case 'removeformat':
							return dom.setAttrib(n, 'class', '');
					}
				}
			};

			// Setup regexp
			re = ed.settings.merge_styles_invalid_parents;
			if (tinymce.is(re, 'string'))
				re = new RegExp(re, 'i');

			// Set style info on selected element
			if (e = t.getSelectedElement())
				set(e, 1);
			else {
				// Generate wrappers and set styles on them
				d.execCommand('FontName', false, '__');
				each(isWebKit ? dom.select('span') : dom.select('font'), function(n) {
					var sp, e;

					if (dom.getAttrib(n, 'face') == '__' || n.style.fontFamily === '__') {
						sp = dom.create(nn, {mce_new : '1'});

						set(sp);

						each (n.childNodes, function(n) {
							sp.appendChild(n.cloneNode(true));
						});

						dom.replace(sp, n);
					}
				});
			}

			// Remove wrappers inside new ones
			each(dom.select(nn).reverse(), function(n) {
				var p = n.parentNode;

				dom.setAttrib(n, 'mce_new', '');

				// Check if it's an old span in a new wrapper
				if (!dom.getAttrib(n, 'mce_new')) {
					// Find new wrapper
					p = dom.getParent(n, function(n) {
						return n.nodeType == 1 && dom.getAttrib(n, 'mce_new');
					});

					if (p)
						dom.remove(n, 1);
				}
			});

			// Merge wrappers with parent wrappers
			each(dom.select(nn).reverse(), function(n) {
				var p = n.parentNode;

				if (!p)
					return;

				// Has parent of the same type and only child
				if (p.nodeName == nn.toUpperCase() && p.childNodes.length == 1)
					return dom.remove(p, 1);

				// Has parent that is more suitable to have the class and only child
				if (n.nodeType == 1 && (!re || !re.test(p.nodeName)) && p.childNodes.length == 1) {
					set(p); // Set style info on parent instead
					dom.setAttrib(n, 'class', '');
				}
			});

			// Remove empty wrappers
			each(dom.select(nn).reverse(), function(n) {
				if (!dom.getAttrib(n, 'class') && !dom.getAttrib(n, 'style'))
					return dom.remove(n, 1);
			});

			s.moveToBookmark(b);
		},

		queryStateJustify : function(c, v) {
			var ed = this.editor, n = ed.selection.getNode(), dom = ed.dom;

			if (n && n.nodeName == 'IMG')
				return dom.getStyle(n, 'float') == v;

			n = dom.getParent(ed.selection.getStart(), function(n) {
				return n.nodeType == 1 && n.style.textAlign;
			});

			if (v == 'full')
				v = 'justify';

			if (ed.settings.inline_styles)
				return (n && n.style.textAlign == v);

			return ed.getDoc().queryCommandState(c);
		},

		HiliteColor : function(ui, val) {
			var t = this, ed = t.editor, d = ed.getDoc();

			function set(s) {
				if (!isGecko)
					return;

				try {
					// Try new Gecko method
					d.execCommand("styleWithCSS", 0, s);
				} catch (ex) {
					// Use old
					d.execCommand("useCSS", 0, !s);
				}
			};

			if (isGecko || isOpera) {
				set(true);
				d.execCommand('hilitecolor', false, val);
				set(false);
			} else
				d.execCommand('BackColor', false, val);
		},

		Undo : function() {
			var ed = this.editor;

			if (ed.settings.custom_undo_redo) {
				ed.undoManager.undo();
				ed.nodeChanged();
			} else
				ed.getDoc().execCommand('Undo', false, null);
		},

		Redo : function() {
			var ed = this.editor;

			if (ed.settings.custom_undo_redo) {
				ed.undoManager.redo();
				ed.nodeChanged();
			} else
				ed.getDoc().execCommand('Redo', false, null);
		},

		FormatBlock : function(ui, val) {
			var t = this, ed = t.editor;

			val = ed.settings.forced_root_block ? (val || '<p>') : val;
			t.mceRemoveNode();

			if (val.indexOf('<') == -1)
				val = '<' + val + '>';

			if (tinymce.isGecko)
				val = val.replace(/<(div|blockquote|code|dt|dd|dl|samp)>/gi, '$1');

			ed.getDoc().execCommand('FormatBlock', false, val);
		},

		mceCleanup : function() {
			var ed = this.editor, s = ed.selection, b = s.getBookmark();
			ed.setContent(ed.getContent());
			s.moveToBookmark(b);
		},

		mceRemoveNode : function(ui, val) {
			var ed = this.editor, s = ed.selection, b, n = val || s.getNode();

			// Make sure that the body node isn't removed
			if (n == ed.getBody())
				return;

			b = s.getBookmark();
			ed.dom.remove(n, 1);
			s.moveToBookmark(b);
			ed.nodeChanged();
		},

		mceSelectNodeDepth : function(ui, val) {
			var ed = this.editor, s = ed.selection, c = 0;

			ed.dom.getParent(s.getNode(), function(n) {
				if (n.nodeType == 1 && c++ == val) {
					s.select(n);
					ed.nodeChanged();
					return false;
				}
			}, ed.getBody());
		},

		mceSelectNode : function(u, v) {
			this.editor.selection.select(v);
		},

		mceInsertContent : function(ui, val) {
			this.editor.selection.setContent(val);
		},

		mceInsertRawHTML : function(ui, val) {
			var ed = this.editor;

			ed.execCommand('mceInsertContent', false, 'tiny_mce_marker');
			ed.setContent(ed.getContent().replace(/tiny_mce_marker/g, val));
		},

		mceRepaint : function() {
			var s, b, e = this.editor;

			if (tinymce.isGecko) {
				try {
					s = e.selection;
					b = s.getBookmark(true);

					if (s.getSel())
						s.getSel().selectAllChildren(e.getBody());

					s.collapse(true);
					s.moveToBookmark(b);
				} catch (ex) {
					// Ignore
				}
			}
		},

		queryStateUnderline : function() {
			var ed = this.editor, n;

			if (n && n.nodeName == 'A')
				return false;

			return ed.getDoc().queryCommandState('Underline');
		},

		queryStateOutdent : function() {
			var ed = this.editor, n;

			if (ed.settings.inline_styles) {
				if ((n = ed.dom.getParent(ed.selection.getStart(), ed.dom.isBlock)) && parseInt(n.style.paddingLeft) > 0)
					return true;

				if ((n = ed.dom.getParent(ed.selection.getEnd(), ed.dom.isBlock)) && parseInt(n.style.paddingLeft) > 0)
					return true;
			} else
				return !!ed.dom.getParent(ed.selection.getNode(), 'BLOCKQUOTE');

			return this.queryStateInsertUnorderedList() || this.queryStateInsertOrderedList();
		},

		queryStateInsertUnorderedList : function() {
			return this.editor.dom.getParent(this.editor.selection.getNode(), 'UL');
		},

		queryStateInsertOrderedList : function() {
			return this.editor.dom.getParent(this.editor.selection.getNode(), 'OL');
		},

		queryStatemceBlockQuote : function() {
			return !!this.editor.dom.getParent(this.editor.selection.getStart(), function(n) {return n.nodeName === 'BLOCKQUOTE';});
		},

		mceBlockQuote : function() {
			var t = this, s = t.editor.selection, b = s.getBookmark(), bq, dom = t.editor.dom;

			function findBQ(e) {
				return dom.getParent(e, function(n) {return n.nodeName === 'BLOCKQUOTE';});
			};

			// Remove blockquote(s)
			if (findBQ(s.getStart())) {
				each(t._getSelectedBlocks(findBQ(s.getStart()), findBQ(s.getEnd())), function(e) {
					// Found BQ lets remove it
					if (e.nodeName == 'BLOCKQUOTE')
						dom.remove(e, 1);
				});

				t.editor.selection.moveToBookmark(b);
				return;
			}

			each(t._getSelectedBlocks(findBQ(s.getStart()), findBQ(s.getEnd())), function(e) {
				var n;

				// Found existing BQ add to this one
				if (e.nodeName == 'BLOCKQUOTE' && !bq) {
					bq = e;
					return;
				}

				// No BQ found, create one
				if (!bq) {
					bq = dom.create('blockquote');
					e.parentNode.insertBefore(bq, e);
				}

				// Add children from existing BQ
				if (e.nodeName == 'BLOCKQUOTE' && bq) {
					n = e.firstChild;

					while (n) {
						bq.appendChild(n.cloneNode(true));
						n = n.nextSibling;
					}

					dom.remove(e);

					return;
				}

				// Add non BQ element to BQ
				bq.appendChild(dom.remove(e));
			});

			t.editor.selection.moveToBookmark(b);
		},

		_getSelectedBlocks : function(st, en) {
			var ed = this.editor, dom = ed.dom, s = ed.selection, sb, eb, n, bl = [];

			sb = dom.getParent(st || s.getStart(), dom.isBlock);
			eb = dom.getParent(en || s.getEnd(), dom.isBlock);

			if (sb)
				bl.push(sb);

			if (sb && eb && sb != eb) {
				n = sb;

				while ((n = n.nextSibling) && n != eb) {
					if (dom.isBlock(n))
						bl.push(n);
				}
			}

			if (eb && sb != eb)
				bl.push(eb);

			return bl;
		}
	});
})();


/* file:jscripts/tiny_mce/classes/UndoManager.js */

tinymce.create('tinymce.UndoManager', {
	index : 0,
	data : null,
	typing : 0,

	UndoManager : function(ed) {
		var t = this, Dispatcher = tinymce.util.Dispatcher;

		t.editor = ed;
		t.data = [];
		t.onAdd = new Dispatcher(this);
		t.onUndo = new Dispatcher(this);
		t.onRedo = new Dispatcher(this);
	},

	add : function(l) {
		var t = this, i, ed = t.editor, b, s = ed.settings, la;

		l = l || {};
		l.content = l.content || ed.getContent({format : 'raw', no_events : 1});

		// Add undo level if needed
		l.content = l.content.replace(/^\s*|\s*$/g, '');
		la = t.data[t.index > 0 ? t.index - 1 : 0];
		if (!l.initial && la && l.content == la.content)
			return null;

		// Time to compress
		if (s.custom_undo_redo_levels) {
			if (t.data.length > s.custom_undo_redo_levels) {
				for (i = 0; i < t.data.length - 1; i++)
					t.data[i] = t.data[i + 1];

				t.data.length--;
				t.index = t.data.length;
			}
		}

		if (s.custom_undo_redo_restore_selection)
			l.bookmark = b = l.bookmark || ed.selection.getBookmark();

		if (t.index < t.data.length && t.data[t.index].initial)
			t.index++;

		// Add level
		t.data.length = t.index + 1;
		t.data[t.index++] = l;

		if (l.initial)
			t.index = 0;

		// Set initial bookmark use first real undo level
		if (t.data.length == 2 && t.data[0].initial)
			t.data[0].bookmark = b;

		t.onAdd.dispatch(t, l);

		//console.dir(t.data);

		return l;
	},

	undo : function() {
		var t = this, ed = t.editor, l = l, i;

		if (t.typing) {
			t.add();
			t.typing = 0;
		}

		if (t.index > 0) {
			// If undo on last index then take snapshot
			if (t.index == t.data.length && t.index > 1) {
				i = t.index;
				t.typing = 0;

				if (!t.add())
					t.index = i;

				--t.index;
			}

			l = t.data[--t.index];
			ed.setContent(l.content, {format : 'raw'});
			ed.selection.moveToBookmark(l.bookmark);

			t.onUndo.dispatch(t, l);
		}

		return l;
	},

	redo : function() {
		var t = this, ed = t.editor, l = null;

		if (t.index < t.data.length - 1) {
			l = t.data[++t.index];
			ed.setContent(l.content, {format : 'raw'});
			ed.selection.moveToBookmark(l.bookmark);

			t.onRedo.dispatch(t, l);
		}

		return l;
	},

	clear : function() {
		var t = this;

		t.data = [];
		t.index = 0;
		t.typing = 0;
		t.add({initial : true});
	},

	hasUndo : function() {
		return this.index != 0 || this.typing;
	},

	hasRedo : function() {
		return this.index < this.data.length - 1;
	}

	});
/* file:jscripts/tiny_mce/classes/ForceBlocks.js */

(function() {
	// Shorten names
	var Event, isIE, isGecko, isOpera, each, extend;

	Event = tinymce.dom.Event;
	isIE = tinymce.isIE;
	isGecko = tinymce.isGecko;
	isOpera = tinymce.isOpera;
	each = tinymce.each;
	extend = tinymce.extend;

	tinymce.create('tinymce.ForceBlocks', {
		ForceBlocks : function(ed) {
			var t = this, s;

			t.editor = ed;
			t.dom = ed.dom;

			// Default settings
			t.settings = s = extend({
				element : 'P',
				forced_root_block : 'p',
				force_p_newlines : true
			}, ed.settings);

			ed.onPreInit.add(t.setup, t);

			if (!isIE) {
				ed.onSetContent.add(function(ed, o) {
					o.content = o.content.replace(/<p>[\s\u00a0]+<\/p>/g, '<p><br /></p>');
				});
			}

			ed.onPostProcess.add(function(ed, o) {
				if (isOpera)
					o.content = o.content.replace(/(\u00a0|&#160;|&nbsp;)<\/p>/g, '</p>');

				o.content = o.content.replace(/<p><\/p>/g, '<p>\u00a0</p>');

				// Use BR instead of &nbsp; padded paragraphs
				o.content = o.content.replace(/<p>\s*<br \/>\s*<\/p>/g, '<p>\u00a0</p>');
				o.content = o.content.replace(/\s*<br \/>\s*<\/p>/g, '</p>');
			});

			if (s.forced_root_block) {
				ed.onInit.add(t.forceRoots, t);
				ed.onSetContent.add(t.forceRoots, t);
				ed.onBeforeGetContent.add(t.forceRoots, t);
			}
		},

		setup : function() {
			var t = this, ed = t.editor, s = t.settings;

			// Force root blocks when typing and when getting output
			if (s.forced_root_block) {
				ed.onKeyUp.add(t.forceRoots, t);
				ed.onPreProcess.add(t.forceRoots, t);
			}

			if (s.force_br_newlines) {
				// Force IE to produce BRs on enter
				if (isIE) {
					ed.onKeyPress.add(function(ed, e) {
						var n, s = ed.selection;

						if (e.keyCode == 13) {
							s.setContent('<br id="__" /> ', {format : 'raw'});
							n = ed.dom.get('__');
							n.removeAttribute('id');
							s.select(n);
							s.collapse();
							return Event.cancel(e);
						}
					});
				}

				return;
			}

			if (!isIE && s.force_p_newlines) {
				ed.onPreProcess.add(function(ed, o) {
					each(ed.dom.select('br', o.node), function(n) {
						var p = n.parentNode;

						// Replace <p><br /></p> with <p>&nbsp;</p>
						if (p && p.nodeName == 'p' && (p.childNodes.length == 1 || p.lastChild == n))
							p.replaceChild(ed.getDoc().createTextNode('\u00a0'), n);
					});
				});

				ed.onKeyPress.add(function(ed, e) {
					if (e.keyCode == 13 && !e.shiftKey) {
						if (!t.insertPara(e))
							Event.cancel(e);
					}
				});

				if (isGecko) {
					ed.onKeyDown.add(function(ed, e) {
						if ((e.keyCode == 8 || e.keyCode == 46) && !e.shiftKey)
							t.backspaceDelete(e, e.keyCode == 8);
					});
				}
			}
		},

		find : function(n, t, s) {
			var ed = this.editor, w = ed.getDoc().createTreeWalker(n, 4, null, false), c = -1;

			while (n = w.nextNode()) {
				c++;

				// Index by node
				if (t == 0 && n == s)
					return c;

				// Node by index
				if (t == 1 && c == s)
					return n;
			}

			return -1;
		},

		forceRoots : function(ed, e) {
			var t = this, ed = t.editor, b = ed.getBody(), d = ed.getDoc(), se = ed.selection, s = se.getSel(), r = se.getRng(), si = -2, ei, so, eo, tr, c = -0xFFFFFF;
			var nx, bl, bp, sp, le, nl = b.childNodes, i;

			// Fix for bug #1863847
			if (e && e.keyCode == 13)
				return true;

			// Wrap non blocks into blocks
			for (i = nl.length - 1; i >= 0; i--) {
				nx = nl[i];

				// Is text or non block element
				if (nx.nodeType == 3 || !t.dom.isBlock(nx)) {
					if (!bl) {
						// Create new block but ignore whitespace
						if (nx.nodeType != 3 || /[^\s]/g.test(nx.nodeValue)) {
							// Store selection
							if (si == -2 && r) {
								if (!isIE) {
									so = r.startOffset;
									eo = r.endOffset;
									si = t.find(b, 0, r.startContainer);
									ei = t.find(b, 0, r.endContainer);
								} else {
									tr = d.body.createTextRange();
									tr.moveToElementText(b);
									tr.collapse(1);
									bp = tr.move('character', c) * -1;

									tr = r.duplicate();
									tr.collapse(1);
									sp = tr.move('character', c) * -1;

									tr = r.duplicate();
									tr.collapse(0);
									le = (tr.move('character', c) * -1) - sp;

									si = sp - bp;
									ei = le;
								}
							}

							bl = ed.dom.create(t.settings.forced_root_block);
							bl.appendChild(nx.cloneNode(1));
							b.replaceChild(bl, nx);
						}
					} else {
						if (bl.hasChildNodes())
							bl.insertBefore(nx, bl.firstChild);
						else
							bl.appendChild(nx);
					}
				} else
					bl = null; // Time to create new block
			}

			// Restore selection
			if (si != -2) {
				if (!isIE) {
					bl = d.getElementsByTagName(t.settings.element)[0];
					r = d.createRange();

					// Select last location or generated block
					if (si != -1)
						r.setStart(t.find(b, 1, si), so);
					else
						r.setStart(bl, 0);

					// Select last location or generated block
					if (ei != -1)
						r.setEnd(t.find(b, 1, ei), eo);
					else
						r.setEnd(bl, 0);

					if (s) {
						s.removeAllRanges();
						s.addRange(r);
					}
				} else {
					try {
						r = s.createRange();
						r.moveToElementText(b);
						r.collapse(1);
						r.moveStart('character', si);
						r.moveEnd('character', ei);
						r.select();
					} catch (ex) {
						// Ignore
					}
				}
			}
		},

		getParentBlock : function(n) {
			var d = this.dom;

			return d.getParent(n, d.isBlock);
		},

		insertPara : function(e) {
			var t = this, ed = t.editor, d = ed.getDoc(), se = t.settings, s = ed.selection.getSel(), r = s.getRangeAt(0), b = d.body;
			var rb, ra, dir, sn, so, en, eo, sb, eb, bn, bef, aft, sc, ec, n;

			function isEmpty(n) {
				n = n.innerHTML;
				n = n.replace(/<(img|hr|table)/gi, '-'); // Keep these convert them to - chars
				n = n.replace(/<[^>]+>/g, ''); // Remove all tags

				return n.replace(/[ \t\r\n]+/g, '') == '';
			};

			// If root blocks are forced then use Operas default behavior since it's really good
// Removed due to bug: #1853816
//			if (se.forced_root_block && isOpera)
//				return true;

			// Setup before range
			rb = d.createRange();

			// If is before the first block element and in body, then move it into first block element
			rb.setStart(s.anchorNode, s.anchorOffset);
			rb.collapse(true);

			// Setup after range
			ra = d.createRange();

			// If is before the first block element and in body, then move it into first block element
			ra.setStart(s.focusNode, s.focusOffset);
			ra.collapse(true);

			// Setup start/end points
			dir = rb.compareBoundaryPoints(rb.START_TO_END, ra) < 0;
			sn = dir ? s.anchorNode : s.focusNode;
			so = dir ? s.anchorOffset : s.focusOffset;
			en = dir ? s.focusNode : s.anchorNode;
			eo = dir ? s.focusOffset : s.anchorOffset;

			// If the caret is in an invalid location in FF we need to move it into the first block
			if (sn == b && en == b && b.firstChild && ed.dom.isBlock(b.firstChild)) {
				sn = en = sn.firstChild;
				so = eo = 0;
				rb = d.createRange();
				rb.setStart(sn, 0);
				ra = d.createRange();
				ra.setStart(en, 0);
			}

			// Never use body as start or end node
			sn = sn.nodeName == "BODY" ? sn.firstChild : sn;
			en = en.nodeName == "BODY" ? en.firstChild : en;

			// Get start and end blocks
			sb = t.getParentBlock(sn);
			eb = t.getParentBlock(en);
			bn = sb ? sb.nodeName : se.element; // Get block name to create

			// Return inside list use default browser behavior
			if (t.dom.getParent(sb, function(n) { return /OL|UL|PRE/.test(n.nodeName); }))
				return true;

			// If caption or absolute layers then always generate new blocks within
			if (sb && (sb.nodeName == 'CAPTION' || /absolute|relative|static/gi.test(sb.style.position))) {
				bn = se.element;
				sb = null;
			}

			// If caption or absolute layers then always generate new blocks within
			if (eb && (eb.nodeName == 'CAPTION' || /absolute|relative|static/gi.test(eb.style.position))) {
				bn = se.element;
				eb = null;
			}

			// Use P instead
			if (/(TD|TABLE|TH|CAPTION)/.test(bn) || (sb && bn == "DIV" && /left|right/gi.test(sb.style.cssFloat))) {
				bn = se.element;
				sb = eb = null;
			}

			// Setup new before and after blocks
			bef = (sb && sb.nodeName == bn) ? sb.cloneNode(0) : ed.dom.create(bn);
			aft = (eb && eb.nodeName == bn) ? eb.cloneNode(0) : ed.dom.create(bn);

			// Remove id from after clone
			aft.removeAttribute('id');

			// Is header and cursor is at the end, then force paragraph under
			if (/^(H[1-6])$/.test(bn) && sn.nodeValue && so == sn.nodeValue.length)
				aft = ed.dom.create(se.element);

			// Find start chop node
			n = sc = sn;
			do {
				if (n == b || n.nodeType == 9 || t.dom.isBlock(n) || /(TD|TABLE|TH|CAPTION)/.test(n.nodeName))
					break;

				sc = n;
			} while ((n = n.previousSibling ? n.previousSibling : n.parentNode));

			// Find end chop node
			n = ec = en;
			do {
				if (n == b || n.nodeType == 9 || t.dom.isBlock(n) || /(TD|TABLE|TH|CAPTION)/.test(n.nodeName))
					break;

				ec = n;
			} while ((n = n.nextSibling ? n.nextSibling : n.parentNode));

			// Place first chop part into before block element
			if (sc.nodeName == bn)
				rb.setStart(sc, 0);
			else
				rb.setStartBefore(sc);

			rb.setEnd(sn, so);
			bef.appendChild(rb.cloneContents() || d.createTextNode('')); // Empty text node needed for Safari

			// Place secnd chop part within new block element
			try {
				ra.setEndAfter(ec);
			} catch(ex) {
				//console.debug(s.focusNode, s.focusOffset);
			}

			ra.setStart(en, eo);
			aft.appendChild(ra.cloneContents() || d.createTextNode('')); // Empty text node needed for Safari

			// Create range around everything
			r = d.createRange();
			if (!sc.previousSibling && sc.parentNode.nodeName == bn) {
				r.setStartBefore(sc.parentNode);
			} else {
				if (rb.startContainer.nodeName == bn && rb.startOffset == 0)
					r.setStartBefore(rb.startContainer);
				else
					r.setStart(rb.startContainer, rb.startOffset);
			}

			if (!ec.nextSibling && ec.parentNode.nodeName == bn)
				r.setEndAfter(ec.parentNode);
			else
				r.setEnd(ra.endContainer, ra.endOffset);

			// Delete and replace it with new block elements
			r.deleteContents();

			// Never wrap blocks in blocks
			if (bef.firstChild && bef.firstChild.nodeName == bn)
				bef.innerHTML = bef.firstChild.innerHTML;

			if (aft.firstChild && aft.firstChild.nodeName == bn)
				aft.innerHTML = aft.firstChild.innerHTML;

			// Padd empty blocks
			if (isEmpty(bef))
				bef.innerHTML = '<br />';

			if (isEmpty(aft))
				aft.innerHTML = isOpera ? '&nbsp;' : '<br />'; // Extra space for Opera so that the caret can move there

			// Opera needs this one backwards
			if (isOpera) {
				r.insertNode(bef);
				r.insertNode(aft);
			} else {
				r.insertNode(aft);
				r.insertNode(bef);
			}

			// Normalize
			aft.normalize();
			bef.normalize();

			// Move cursor and scroll into view
			r = d.createRange();
			r.selectNodeContents(aft);
			r.collapse(1);
			s.removeAllRanges();
			s.addRange(r);
			aft.scrollIntoView(0);

			return false;
		},

		backspaceDelete : function(e, bs) {
			var t = this, ed = t.editor, b = ed.getBody(), n, se = ed.selection, r = se.getRng(), sc = r.startContainer, n;

			// The caret sometimes gets stuck in Gecko if you delete empty paragraphs
			// This workaround removes the element by hand and moves the caret to the previous element
			if (sc && ed.dom.isBlock(sc) && bs) {
				if (sc.childNodes.length == 1 && sc.firstChild.nodeName == 'BR') {
					n = sc.previousSibling;
					if (n) { 
						ed.dom.remove(sc);
						se.select(n, 1);
						se.collapse(0);
						return Event.cancel(e);
					}
				}
			}

			// Gecko generates BR elements here and there, we don't like those so lets remove them
			function handler(e) {
				e = e.target;

				// A new BR was created in a block element, remove it
				if (e && e.parentNode && e.nodeName == 'BR' && t.getParentBlock(e)) {
					ed.dom.remove(e);
					Event.remove(b, 'DOMNodeInserted', handler);
				}
			};

			// Listen for new nodes
			Event._add(b, 'DOMNodeInserted', handler);

			// Remove listener
			window.setTimeout(function() {
				Event._remove(b, 'DOMNodeInserted', handler);
			}, 1);
		}
	});
})();

/* file:jscripts/tiny_mce/classes/ControlManager.js */

(function() {
	// Shorten names
	var DOM = tinymce.DOM, Event = tinymce.dom.Event, each = tinymce.each, extend = tinymce.extend;

	tinymce.create('tinymce.ControlManager', {
		ControlManager : function(ed, s) {
			var t = this, i;

			s = s || {};
			t.editor = ed;
			t.controls = {};
			t.onAdd = new tinymce.util.Dispatcher(t);
			t.onPostRender = new tinymce.util.Dispatcher(t);
			t.prefix = s.prefix || ed.id + '_';

			t.onPostRender.add(function() {
				each(t.controls, function(c) {
					c.postRender();
				});
			});
		},

		get : function(id) {
			return this.controls[this.prefix + id] || this.controls[id];
		},

		setActive : function(id, s) {
			var c = null;

			if (c = this.get(id))
				c.setActive(s);

			return c;
		},

		setDisabled : function(id, s) {
			var c = null;

			if (c = this.get(id))
				c.setDisabled(s);

			return c;
		},

		add : function(c) {
			var t = this;

			if (c) {
				t.controls[c.id] = c;
				t.onAdd.dispatch(c, t);
			}

			return c;
		},

		createControl : function(n) {
			var c, t = this, ed = t.editor;

			each(ed.plugins, function(p) {
				if (p.createControl) {
					c = p.createControl(n, t);

					if (c)
						return false;
				}
			});

			switch (n) {
				case "|":
				case "separator":
					return t.createSeparator();
			}

			if (!c && ed.buttons && (c = ed.buttons[n]))
				return t.createButton(n, c);

			return t.add(c);
		},

		createDropMenu : function(id, s) {
			var t = this, ed = t.editor, c;

			s = extend({
				'class' : 'mceDropDown'
			}, s);

			s['class'] = s['class'] + ' ' + ed.getParam('skin') + 'Skin';

			id = t.prefix + id;
			c = t.controls[id] = new tinymce.ui.DropMenu(id, s);
			c.onAddItem.add(function(c, o) {
				var s = o.settings;

				s.title = ed.getLang(s.title, s.title);

				if (!s.onclick) {
					s.onclick = function(v) {
						ed.execCommand(s.cmd, s.ui || false, v || s.value);
					};
				}
			});

			ed.onRemove.add(function() {
				c.destroy();
			});

			return t.add(c);
		},

		createListBox : function(id, s) {
			var t = this, ed = t.editor, cmd, c;

			if (t.get(id))
				return null;

			s.title = ed.translate(s.title);
			s.scope = s.scope || ed;

			if (!s.onselect) {
				s.onselect = function(v) {
					ed.execCommand(s.cmd, s.ui || false, v || s.value);
				};
			}

			s = extend({
				title : s.title,
				'class' : id,
				scope : s.scope,
				control_manager : t
			}, s);

			id = t.prefix + id;

			if (ed.settings.use_native_selects)
				c = new tinymce.ui.NativeListBox(id, s);
			else
				c = new tinymce.ui.ListBox(id, s);

			t.controls[id] = c;

			// Fix focus problem in Safari
			if (tinymce.isWebKit) {
				c.onPostRender.add(function(c, n) {
					// Store bookmark on mousedown
					Event.add(n, 'mousedown', function() {
						ed.bookmark = ed.selection.getBookmark('simple');
					});

					// Restore on focus, since it might be lost
					Event.add(n, 'focus', function() {
						ed.selection.moveToBookmark(ed.bookmark);
						ed.bookmark = null;
					});
				});
			}

			if (c.hideMenu)
				ed.onMouseDown.add(c.hideMenu, c);

			return t.add(c);
		},

		createButton : function(id, s) {
			var t = this, ed = t.editor, o, c;

			if (t.get(id))
				return null;

			s.title = ed.translate(s.title);
			s.scope = s.scope || ed;

			if (!s.onclick && !s.menu_button) {
				s.onclick = function() {
					ed.execCommand(s.cmd, s.ui || false, s.value);
				};
			}

			s = extend({
				title : s.title,
				'class' : id,
				unavailable_prefix : ed.getLang('unavailable', ''),
				scope : s.scope,
				control_manager : t
			}, s);

			id = t.prefix + id;

			if (s.menu_button) {
				c = new tinymce.ui.MenuButton(id, s);
				ed.onMouseDown.add(c.hideMenu, c);
			} else
				c = new tinymce.ui.Button(id, s);

			return t.add(c);
		},

		createMenuButton : function(id, s) {
			s = s || {};
			s.menu_button = 1;

			return this.createButton(id, s);
		},

		createSplitButton : function(id, s) {
			var t = this, ed = t.editor, cmd, c;

			if (t.get(id))
				return null;

			s.title = ed.translate(s.title);
			s.scope = s.scope || ed;

			if (!s.onclick) {
				s.onclick = function(v) {
					ed.execCommand(s.cmd, s.ui || false, v || s.value);
				};
			}

			if (!s.onselect) {
				s.onselect = function(v) {
					ed.execCommand(s.cmd, s.ui || false, v || s.value);
				};
			}

			s = extend({
				title : s.title,
				'class' : id,
				scope : s.scope,
				control_manager : t
			}, s);

			id = t.prefix + id;
			c = t.add(new tinymce.ui.SplitButton(id, s));
			ed.onMouseDown.add(c.hideMenu, c);

			return c;
		},

		createColorSplitButton : function(id, s) {
			var t = this, ed = t.editor, cmd, c;

			if (t.get(id))
				return null;

			s.title = ed.translate(s.title);
			s.scope = s.scope || ed;

			if (!s.onclick) {
				s.onclick = function(v) {
					ed.execCommand(s.cmd, s.ui || false, v || s.value);
				};
			}

			if (!s.onselect) {
				s.onselect = function(v) {
					ed.execCommand(s.cmd, s.ui || false, v || s.value);
				};
			}

			s = extend({
				title : s.title,
				'class' : id,
				'menu_class' : ed.getParam('skin') + 'Skin',
				scope : s.scope,
				more_colors_title : ed.getLang('more_colors')
			}, s);

			id = t.prefix + id;
			c = new tinymce.ui.ColorSplitButton(id, s);
			ed.onMouseDown.add(c.hideMenu, c);

			return t.add(c);
		},

		createToolbar : function(id, s) {
			var c, t = this;

			id = t.prefix + id;
			c = new tinymce.ui.Toolbar(id, s);

			if (t.get(id))
				return null;

			return t.add(c);
		},

		createSeparator : function() {
			return new tinymce.ui.Separator();
		}

		});
})();

/* file:jscripts/tiny_mce/classes/WindowManager.js */

(function() {
	var Dispatcher = tinymce.util.Dispatcher, each = tinymce.each, isIE = tinymce.isIE, isOpera = tinymce.isOpera;

	tinymce.create('tinymce.WindowManager', {
		WindowManager : function(ed) {
			var t = this;

			t.editor = ed;
			t.onOpen = new Dispatcher(t);
			t.onClose = new Dispatcher(t);
			t.params = {};
			t.features = {};
		},

		open : function(s, p) {
			var t = this, f = '', x, y, mo = t.editor.settings.dialog_type == 'modal', w, sw, sh, vp = tinymce.DOM.getViewPort();

			// Default some options
			s = s || {};
			p = p || {};
			sw = isOpera ? vp.w : screen.width; // Opera uses windows inside the Opera window
			sh = isOpera ? vp.h : screen.height;
			s.name = s.name || 'mc_' + new Date().getTime();
			s.width = parseInt(s.width || 320);
			s.height = parseInt(s.height || 240);
			s.resizable = true;
			s.left = s.left || parseInt(sw / 2.0) - (s.width / 2.0);
			s.top = s.top || parseInt(sh / 2.0) - (s.height / 2.0);
			p.inline = false;
			p.mce_width = s.width;
			p.mce_height = s.height;

			if (mo) {
				if (isIE) {
					s.center = true;
					s.help = false;
					s.dialogWidth = s.width + 'px';
					s.dialogHeight = s.height + 'px';
					s.scroll = s.scrollbars || false;
				} else
					s.modal = s.alwaysRaised = s.dialog = s.centerscreen = s.dependent = true;
			}

			// Build features string
			each(s, function(v, k) {
				if (tinymce.is(v, 'boolean'))
					v = v ? 'yes' : 'no';

				if (!/^(name|url)$/.test(k)) {
					if (isIE && mo)
						f += (f ? ';' : '') + k + ':' + v;
					else
						f += (f ? ',' : '') + k + '=' + v;
				}
			});

			t.features = s;
			t.params = p;
			t.onOpen.dispatch(t, s, p);

			try {
				if (isIE && mo) {
					w = 1;
					window.showModalDialog(s.url || s.file, window, f);
				} else
					w = window.open(s.url || s.file, s.name, f);
			} catch (ex) {
				// Ignore
			}

			if (!w)
				alert(t.editor.getLang('popup_blocked'));
		},

		close : function(w) {
			w.close();
			this.onClose.dispatch(this);
		},

		createInstance : function(cl) {
			var a = arguments, i, f = tinymce.resolve(cl), s = '';

			// Is there a better way to dynamically create
			// a class with a dynamic number of arguments
			for (i=1; i<a.length; i++)
				s += (i > 1 ? ',' : '') + 'a[' + i + ']';

			return eval('(new f(' + s + '))');
		},

		confirm : function(t, cb, s) {
			cb.call(s || this, confirm(this._decode(this.editor.getLang(t, t))));
		},

		alert : function(t, cb, s) {
			alert(this._decode(t));

			if (cb)
				cb.call(s || this);
		},

		// Internal functions

		_decode : function(s) {
			return tinymce.DOM.decode(s).replace(/\\n/g, '\n');
		}

		});
}());