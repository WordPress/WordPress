(function(d, w) {
	var addEvent = function( obj, type, fn ) {
		if (obj.addEventListener)
			obj.addEventListener(type, fn, false);
		else if (obj.attachEvent)
			obj.attachEvent('on' + type, function() { return fn.call(obj, window.event);});
	},

	aB, hc = new RegExp('\\bhover\\b', 'g'), q = [],
	rselected = new RegExp('\\bselected\\b', 'g'),

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

	addHoverClass = function(t) {
		var i, id, inA, hovering, ul, li,
			ancestors = [],
			ancestorLength = 0;

		while ( t && t != aB && t != d ) {
			if( 'LI' == t.nodeName.toUpperCase() ) {
				ancestors[ ancestors.length ] = t;
				id = getTOID(t);
				if ( id )
					clearTimeout( id );
				t.className = t.className ? ( t.className.replace(hc, '') + ' hover' ) : 'hover';
				hovering = t;
			}
			t = t.parentNode;
		}

		// Remove any selected classes.
		if ( hovering && hovering.parentNode ) {
			ul = hovering.parentNode;
			if ( ul && 'UL' == ul.nodeName.toUpperCase() ) {
				i = ul.childNodes.length;
				while ( i-- ) {
					li = ul.childNodes[i];
					if ( li != hovering )
						li.className = li.className ? li.className.replace( rselected, '' ) : '';
				}
			}
		}

		/* remove the hover class for any objects not in the immediate element's ancestry */
		i = q.length;
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

	removeHoverClass = function(t) {
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
		var i, l, node,
			t = e.target || e.srcElement;

		// Make t the shortlink menu item, or return.
		while ( true ) {
			// Check if we've gone past the shortlink node,
			// or if the user is clicking on the input.
			if ( ! t || t == d || t == aB )
				return;
			// Check if we've found the shortlink node.
			if ( t.id && t.id == 'wp-admin-bar-get-shortlink' )
				break;
			t = t.parentNode;
		}

		// IE doesn't support preventDefault, and does support returnValue
		if ( e.preventDefault )
			e.preventDefault();
		e.returnValue = false;

		if ( -1 == t.className.indexOf('selected') )
			t.className += ' selected';

		for ( i = 0, l = t.childNodes.length; i < l; i++ ) {
			node = t.childNodes[i];
			if ( node.className && -1 != node.className.indexOf('shortlink-input') ) {
				node.focus();
				node.select();
				node.onblur = function() {
					t.className = t.className ? t.className.replace( rselected, '' ) : '';
				};
				break;
			}
		}
		return false;
	};

	addEvent(w, 'load', function() {
		aB = d.getElementById('wpadminbar');

		if ( d.body && aB ) {
			d.body.appendChild( aB );

			addEvent(aB, 'mouseover', function(e) {
				addHoverClass( e.target || e.srcElement );
			});

			addEvent(aB, 'mouseout', function(e) {
				removeHoverClass( e.target || e.srcElement );
			});

			addEvent(aB, 'click', clickShortlink );
		}

		if ( w.location.hash )
			w.scrollBy(0,-32);
	});
})(document, window);
