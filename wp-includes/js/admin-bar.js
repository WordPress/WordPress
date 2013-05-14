// use jQuery and hoverIntent if loaded
if ( typeof(jQuery) != 'undefined' ) {
	if ( typeof(jQuery.fn.hoverIntent) == 'undefined' ) {
		// hoverIntent r6 - Copy of wp-includes/js/hoverIntent.min.js
		(function(a){a.fn.hoverIntent=function(m,d,h){var j={interval:100,sensitivity:7,timeout:0};if(typeof m==="object"){j=a.extend(j,m)}else{if(a.isFunction(d)){j=a.extend(j,{over:m,out:d,selector:h})}else{j=a.extend(j,{over:m,out:m,selector:d})}}var l,k,g,f;var e=function(n){l=n.pageX;k=n.pageY};var c=function(o,n){n.hoverIntent_t=clearTimeout(n.hoverIntent_t);if((Math.abs(g-l)+Math.abs(f-k))<j.sensitivity){a(n).off("mousemove.hoverIntent",e);n.hoverIntent_s=1;return j.over.apply(n,[o])}else{g=l;f=k;n.hoverIntent_t=setTimeout(function(){c(o,n)},j.interval)}};var i=function(o,n){n.hoverIntent_t=clearTimeout(n.hoverIntent_t);n.hoverIntent_s=0;return j.out.apply(n,[o])};var b=function(p){var o=jQuery.extend({},p);var n=this;if(n.hoverIntent_t){n.hoverIntent_t=clearTimeout(n.hoverIntent_t)}if(p.type=="mouseenter"){g=o.pageX;f=o.pageY;a(n).on("mousemove.hoverIntent",e);if(n.hoverIntent_s!=1){n.hoverIntent_t=setTimeout(function(){c(o,n)},j.interval)}}else{a(n).off("mousemove.hoverIntent",e);if(n.hoverIntent_s==1){n.hoverIntent_t=setTimeout(function(){i(o,n)},j.timeout)}}};return this.on({"mouseenter.hoverIntent":b,"mouseleave.hoverIntent":b},j.selector)}})(jQuery);
	}
	jQuery(document).ready(function($){
		var adminbar = $('#wpadminbar'), refresh, touchOpen, touchClose, disableHoverIntent = false;

		refresh = function(i, el){ // force the browser to refresh the tabbing index
			var node = $(el), tab = node.attr('tabindex');
			if ( tab )
				node.attr('tabindex', '0').attr('tabindex', tab);
		};

		touchOpen = function(unbind) {
			adminbar.find('li.menupop').on('click.wp-mobile-hover', function(e) {
				var el = $(this);

				if ( !el.hasClass('hover') ) {
					e.preventDefault();
					adminbar.find('li.menupop.hover').removeClass('hover');
					el.addClass('hover');
				}

				if ( unbind ) {
					$('li.menupop').off('click.wp-mobile-hover');
					disableHoverIntent = false;
				}
			});
		};

		touchClose = function() {
			var mobileEvent = /Mobile\/.+Safari/.test(navigator.userAgent) ? 'touchstart' : 'click';
			// close any open drop-downs when the click/touch is not on the toolbar
			$(document.body).on( mobileEvent+'.wp-mobile-hover', function(e) {
				if ( !$(e.target).closest('#wpadminbar').length )
					adminbar.find('li.menupop.hover').removeClass('hover');
			});
		};

		adminbar.removeClass('nojq').removeClass('nojs');

		if ( 'ontouchstart' in window ) {
			adminbar.on('touchstart', function(){
				touchOpen(true);
				disableHoverIntent = true;
			});
			touchClose();
		} else if ( /IEMobile\/[1-9]/.test(navigator.userAgent) ) {
			touchOpen();
			touchClose();
		}

		adminbar.find('li.menupop').hoverIntent({
			over: function(e){
				if ( disableHoverIntent )
					return;

				$(this).addClass('hover');
			},
			out: function(e){
				if ( disableHoverIntent )
					return;

				$(this).removeClass('hover');
			},
			timeout: 180,
			sensitivity: 7,
			interval: 100
		});

		if ( window.location.hash )
			window.scrollBy( 0, -32 );

		$('#wp-admin-bar-get-shortlink').click(function(e){
			e.preventDefault();
			$(this).addClass('selected').children('.shortlink-input').blur(function(){
				$(this).parents('#wp-admin-bar-get-shortlink').removeClass('selected');
			}).focus().select();
		});

		$('#wpadminbar li.menupop > .ab-item').bind('keydown.adminbar', function(e){
			if ( e.which != 13 )
				return;

			var target = $(e.target), wrap = target.closest('ab-sub-wrapper');

			e.stopPropagation();
			e.preventDefault();

			if ( !wrap.length )
				wrap = $('#wpadminbar .quicklinks');

			wrap.find('.menupop').removeClass('hover');
			target.parent().toggleClass('hover');
			target.siblings('.ab-sub-wrapper').find('.ab-item').each(refresh);
		}).each(refresh);

		$('#wpadminbar .ab-item').bind('keydown.adminbar', function(e){
			if ( e.which != 27 )
				return;

			var target = $(e.target);

			e.stopPropagation();
			e.preventDefault();

			target.closest('.hover').removeClass('hover').children('.ab-item').focus();
			target.siblings('.ab-sub-wrapper').find('.ab-item').each(refresh);
		});

		$('#wpadminbar').click( function(e) {
			if ( e.target.id != 'wpadminbar' && e.target.id != 'wp-admin-bar-top-secondary' )
				return;

			e.preventDefault();
			$('html, body').animate({ scrollTop: 0 }, 'fast');
		});

		// fix focus bug in WebKit
		$('.screen-reader-shortcut').keydown( function(e) {
			if ( 13 != e.which )
				return;

			var id = $(this).attr('href');

			var ua = navigator.userAgent.toLowerCase();
			if ( ua.indexOf('applewebkit') != -1 && id && id.charAt(0) == '#' ) {
				setTimeout(function () {
					$(id).focus();
				}, 100);
			}
		});

		// Empty sessionStorage on logging out
		if ( 'sessionStorage' in window ) {
			$('#wp-admin-bar-logout a').click( function() {
				try {
					for ( var key in sessionStorage ) {
						if ( key.indexOf('wp-autosave-') != -1 )
							sessionStorage.removeItem(key);
					}
				} catch(e) {}
			});
		}
	});
} else {
	(function(d, w) {
		var addEvent = function( obj, type, fn ) {
			if ( obj.addEventListener )
				obj.addEventListener(type, fn, false);
			else if ( obj.attachEvent )
				obj.attachEvent('on' + type, function() { return fn.call(obj, window.event);});
		},

		aB, hc = new RegExp('\\bhover\\b', 'g'), q = [],
		rselected = new RegExp('\\bselected\\b', 'g'),

		/**
		 * Get the timeout ID of the given element
		 */
		getTOID = function(el) {
			var i = q.length;
			while ( i-- ) {
				if ( q[i] && el == q[i][1] )
					return q[i][0];
			}
			return false;
		},

		addHoverClass = function(t) {
			var i, id, inA, hovering, ul, li,
				ancestors = [],
				ancestorLength = 0;

			while ( t && t != aB && t != d ) {
				if ( 'LI' == t.nodeName.toUpperCase() ) {
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
				if ( 'LI' == t.nodeName.toUpperCase() ) {
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
		},

		scrollToTop = function(t) {
			var distance, speed, step, steps, timer, speed_step;

			// Ensure that the #wpadminbar was the target of the click.
			if ( t.id != 'wpadminbar' && t.id != 'wp-admin-bar-top-secondary' )
				return;

			distance    = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;

			if ( distance < 1 )
				return;

			speed_step = distance > 800 ? 130 : 100;
			speed     = Math.min( 12, Math.round( distance / speed_step ) );
			step      = distance > 800 ? Math.round( distance / 30  ) : Math.round( distance / 20  );
			steps     = [];
			timer     = 0;

			// Animate scrolling to the top of the page by generating steps to
			// the top of the page and shifting to each step at a set interval.
			while ( distance ) {
				distance -= step;
				if ( distance < 0 )
					distance = 0;
				steps.push( distance );

				setTimeout( function() {
					window.scrollTo( 0, steps.shift() );
				}, timer * speed );

				timer++;
			}
		};

		addEvent(w, 'load', function() {
			aB = d.getElementById('wpadminbar');

			if ( d.body && aB ) {
				d.body.appendChild( aB );

				if ( aB.className )
					aB.className = aB.className.replace(/nojs/, '');

				addEvent(aB, 'mouseover', function(e) {
					addHoverClass( e.target || e.srcElement );
				});

				addEvent(aB, 'mouseout', function(e) {
					removeHoverClass( e.target || e.srcElement );
				});

				addEvent(aB, 'click', clickShortlink );

				addEvent(aB, 'click', function(e) {
					scrollToTop( e.target || e.srcElement );
				});

				addEvent( document.getElementById('wp-admin-bar-logout'), 'click', function() {
					if ( 'sessionStorage' in window ) {
						try {
							for ( var key in sessionStorage ) {
								if ( key.indexOf('wp-autosave-') != -1 )
									sessionStorage.removeItem(key);
							}
						} catch(e) {}
					}
				});
			}

			if ( w.location.hash )
				w.scrollBy(0,-32);
		});
	})(document, window);

}
