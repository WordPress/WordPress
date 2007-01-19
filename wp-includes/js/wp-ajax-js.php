<?php @require_once('../../wp-config.php'); cache_javascript_headers(); ?>
var WPAjax = Class.create();
Object.extend(WPAjax.prototype, Ajax.Request.prototype);
Object.extend(WPAjax.prototype, {
	WPComplete: false, // onComplete function
	WPError: false, // onWPError function
	initialize: function(url, responseEl) {
		var tempObj = this;
		this.transport = Ajax.getTransport();
		if ( !this.transport )
			return false;
		this.setOptions( {
			parameters: 'cookie=' + encodeURIComponent(document.cookie),
			onComplete: function(transport) { // transport = XMLHttpRequest object
				if ( tempObj.parseAjaxResponse() ) {
					if ( 'function' == typeof tempObj.WPComplete )
						tempObj.WPComplete(transport);
				} else if ( 'function' == typeof tempObj.WPError ) // if response corresponds to an error (bad data, say, not 404)
					tempObj.WPError(transport);
			}
		});
		this.url = url ? url : '<?php bloginfo( 'wpurl' ); ?>/wp-admin/admin-ajax.php';
		this.getResponseElement(responseEl);
	},
	addArg: function(key, value) {
		var a = $H(this.options.parameters.parseQuery());
		a[encodeURIComponent(key)] = encodeURIComponent(value);
		this.options.parameters = a.map(function(pair) {
			return pair.join('=');
		}).join('&');
	},
	getResponseElement: function(r) {
		var p = $(r + '-p');
		if ( !p ) {
			new Insertion.Bottom(r, "<span id='" + r + "-p'></span>");
			var p = $(r + '-p');
		}
		this.myResponseElement = p;
	},
	parseAjaxResponse: function() { // 1 = good, 0 = strange (bad data?), -1 = you lack permission
		if ( this.transport.responseXML && typeof this.transport.responseXML == 'object' && ( this.transport.responseXML.xml || 'undefined' == typeof this.transport.responseXML.xml ) ) {
			var err = this.transport.responseXML.getElementsByTagName('wp_error');
			if ( err[0] ) {
				var msg = $A(err).inject( '', function(a, b) { return a + '<p>' + b.firstChild.nodeValue + '</p>'; } );
				Element.update(this.myResponseElement,'<div class="error">' + msg + '</div>');
				return false;
			}
			return true;
		}
		var r = this.transport.responseText;
		if ( isNaN(r) ) {
			Element.update(this.myResponseElement,'<div class="error"><p>' + r + '</p></div>');
			return false;
		}
		var r = parseInt(r,10);
		if ( -1 == r ) {
			Element.update(this.myResponseElement,"<div class='error'><p><?php _e("You don't have permission to do that."); ?></p></div>");
			return false;
		} else if ( 0 == r ) {
			Element.update(this.myResponseElement,"<div class='error'><p><?php _e("Something strange happened.  Try refreshing the page."); ?></p></div>");
			return false;
		}
		return true;
	},
	addOnComplete: function(f) {
		if ( 'function' == typeof f ) { var of = this.WPComplete; this.WPComplete = function(t) { if ( of ) of(t); f(t); } }
	},
	addOnWPError: function(f) {
		if ( 'function' == typeof f ) { var of = this.WPError; this.WPError = function(t) { if ( of ) of(t); f(t); } }
	},
	notInitialized: function() {
		return this.transport ? false : true;
	}
});

Ajax.activeSendCount = 0;
Ajax.Responders.register( {
	onCreate: function() {
		Ajax.activeSendCount++;
		if ( 1 != Ajax.activeSendCount )
			return;
		wpBeforeUnload = window.onbeforeunload;
		window.onbeforeunload = function() {
			return "<?php js_escape(__("Slow down, I'm still sending your data!")); ?>";
		}
	},
	onLoading: function() { // Can switch to onLoaded if we lose data
		Ajax.activeSendCount--;
		if ( 0 != Ajax.activeSendCount )
			return;
		window.onbeforeunload = wpBeforeUnload;
	}
});

//Pretty func adapted from ALA http://www.alistapart.com/articles/gettingstartedwithajax
function getNodeValue(tree,el){try { var r = tree.getElementsByTagName(el)[0].firstChild.nodeValue; } catch(err) { var r = null; } return r; }
