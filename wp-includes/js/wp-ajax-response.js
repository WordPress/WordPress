var wpAjax = jQuery.extend( {
	unserialize: function( s ) {
		var r = {}; if ( !s ) { return r; }
		var q = s.split('?'); if ( q[1] ) { s = q[1]; }
		var pp = s.split('&');
		for ( var i in pp ) {
			if ( jQuery.isFunction(pp.hasOwnProperty) && !pp.hasOwnProperty(i) ) { continue; }
			var p = pp[i].split('=');
			r[p[0]] = p[1];
		}
		return r;
	},
	parseAjaxResponse: function( x, r, e ) { // 1 = good, 0 = strange (bad data?), -1 = you lack permission
		var parsed = {};
		var re = jQuery('#' + r).html('');
		if ( x && typeof x == 'object' && x.getElementsByTagName('wp_ajax') ) {
			parsed.responses = [];
			parsed.errors = false;
			var err = '';
			jQuery('response', x).each( function() {
				var th = jQuery(this);
				var child = jQuery(this.firstChild);
				var response = { action: th.attr('action'), what: child.get(0).nodeName, id: child.attr('id'), oldId: child.attr('old_id'), position: child.attr('position') };
				response.data = jQuery( 'response_data', child ).text();
				response.supplemental = {};
				if ( !jQuery( 'supplemental', child ).children().each( function() {
					response.supplemental[this.nodeName] = jQuery(this).text();
				} ).size() ) { response.supplemental = false }
				response.errors = [];
				if ( !jQuery('wp_error', child).each( function() {
					var code = jQuery(this).attr('code');
					var anError = { code: code, message: this.firstChild.nodeValue, data: false };
					var errorData = jQuery('wp_error_data[code="' + code + '"]', x);
					if ( errorData ) { anError.data = errorData.get(); }
					var formField = jQuery( 'form-field', errorData ).text();
					if ( formField ) { code = formField; }
					if ( e ) { wpAjax.invalidateForm( jQuery('#' + e + ' :input[name="' + code + '"]' ).parents('.form-field:first') ); }
					err += '<p>' + anError.message + '</p>';
					response.errors.push( anError );
					parsed.errors = true;
				} ).size() ) { response.errors = false; }
				parsed.responses.push( response );
			} );
			if ( err.length ) { re.html( '<div class="error">' + err + '</div>' ); }
			return parsed;
		}
		if ( isNaN(x) ) { return !re.html('<div class="error"><p>' + x + '</p></div>'); }
		x = parseInt(x,10);
		if ( -1 == x ) { return !re.html('<div class="error"><p>' + wpAjax.noPerm + '</p></div>'); }
		else if ( 0 === x ) { return !re.html('<div class="error"><p>' + wpAjax.broken  + '</p></div>'); }
		return true;
	},
	invalidateForm: function ( selector ) {
		return jQuery( selector ).addClass( 'form-invalid' ).change( function() { jQuery(this).removeClass( 'form-invalid' ); } );
	},
	validateForm: function( selector ) {
		selector = jQuery( selector );
		return !wpAjax.invalidateForm( selector.find('.form-required').andSelf().filter('.form-required:has(:input[value=""]), .form-required:input[value=""]') ).size();
	}
}, wpAjax || { noPerm: 'You do not have permission to do that.', broken: 'An unidentified error has occurred.' } );
