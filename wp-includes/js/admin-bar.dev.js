(function(d, w) {
	var addEvent = function( obj, type, fn ) {
		if (obj.addEventListener)
			obj.addEventListener(type, fn, false);
		else if (obj.attachEvent)
			obj.attachEvent('on' + type, function() { return fn.call(obj, window.event);});
	},

	aB, hc = new RegExp('\\bhover\\b', 'g'), q = [],

	/**
	 * Get the timeout ID of the given element
	 */
	getTOID = function(el) {
		var i = q.length;
		while( i-- )
			if ( q[i] && el == q[i][1] )
				return q[i][0];
		return false;
	},

	addClass = function(t) {
		var ancestors = [],
		ancestorLength = 0,
		id,
		i = q.length,
		inA;
		while ( t && t != aB && t != d ) {
			if( 'LI' == t.nodeName.toUpperCase() ) {
				ancestors[ ancestors.length ] = t;
				id = getTOID(t);	
				if ( id )
					clearTimeout( id );
				t.className = t.className ? ( t.className.replace(hc, '') + ' hover' ) : 'hover';
			}
			t = t.parentNode;
		}
		
		/* remove the hover class for any objects not in the immediate element's ancestry */
		while ( i-- ) {
			inA = false;	
			ancestorLength = ancestors.length;
			while( ancestorLength-- ) {
				if ( ancestors[ ancestorLength ] == q[i][1] ) 
					inA = true;	
			}

			if ( ! inA )
				q[i][1].className = q[i][1].className ? q[i][1].className.replace(hc, '') : '';
		}
	},

	removeClass = function(t) {
		while ( t && t != aB && t != d ) {
			if( 'LI' == t.nodeName.toUpperCase() ) {
				(function(t) {
					var to = setTimeout(function() {
						t.className = t.className ? t.className.replace(hc, '') : '';
					}, 500);
					q[q.length] = [to, t];
				})(t);
			}
			t = t.parentNode;
		}
	},

	clickShortlink = function(e) {
		var t = e.target || e.srcElement, links, i;
		

		if ( 'undefined' == typeof adminBarL10n )
			return;

		while( t && t != aB && t != d && (
			! t.className ||
			-1 == t.className.indexOf('ab-get-shortlink')
		) )
			t = t.parentNode;

		if ( t && t.className && -1 != t.className.indexOf('ab-get-shortlink') ) {
			links = d.getElementsByTagName('link');
			if ( ! links.length )
				links = d.links;

			i = links.length;

			if ( e.preventDefault )
				e.preventDefault();
			e.returnValue = false;

			while( i-- ) {
				if ( links[i] && 'shortlink' == links[i].getAttribute('rel') ) {
					prompt( adminBarL10n.url, links[i].href );
					return false;
				}
			}
			
			alert( adminBarL10n.noShortlink );
			return false;
		}
	}

	addEvent(w, 'load', function() {
		var b = d.getElementsByTagName('body')[0],
		s = d.getElementById('adminbar-search');
		
		aB = d.getElementById('wpadminbar');

		if ( b && aB ) {
			b.appendChild( aB );

			addEvent(aB, 'mouseover', function(e) {
				addClass( e.target || e.srcElement );
			});

			addEvent(aB, 'mouseout', function(e) {
				removeClass( e.target || e.srcElement );	
			});

			addEvent(aB, 'click', clickShortlink );
		}

		if ( s ) {
			if ( '' == s.value )
				s.value = s.getAttribute('title');

			s.onblur = function() {
				this.value = '' == this.value ? this.getAttribute('title') : this.value;
			}
			s.onfocus = function() {
				this.value = this.getAttribute('title') == this.value ? '' : this.value;
			}
		}
		
		if ( w.location.hash )
			w.scrollBy(0,-32);
	});
})(document, window);
