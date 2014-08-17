/* global setUserSetting, ajaxurl, commonL10n, alert, confirm, pagenow */
var showNotice, adminMenu, columns, validateForm, screenMeta;
( function( $, window, undefined ) {
// Removed in 3.3.
// (perhaps) needed for back-compat
adminMenu = {
	init : function() {},
	fold : function() {},
	restoreMenuState : function() {},
	toggle : function() {},
	favorites : function() {}
};

// show/hide/save table columns
columns = {
	init : function() {
		var that = this;
		$('.hide-column-tog', '#adv-settings').click( function() {
			var $t = $(this), column = $t.val();
			if ( $t.prop('checked') )
				that.checked(column);
			else
				that.unchecked(column);

			columns.saveManageColumnsState();
		});
	},

	saveManageColumnsState : function() {
		var hidden = this.hidden();
		$.post(ajaxurl, {
			action: 'hidden-columns',
			hidden: hidden,
			screenoptionnonce: $('#screenoptionnonce').val(),
			page: pagenow
		});
	},

	checked : function(column) {
		$('.column-' + column).show();
		this.colSpanChange(+1);
	},

	unchecked : function(column) {
		$('.column-' + column).hide();
		this.colSpanChange(-1);
	},

	hidden : function() {
		return $('.manage-column').filter(':hidden').map(function() { return this.id; }).get().join(',');
	},

	useCheckboxesForHidden : function() {
		this.hidden = function(){
			return $('.hide-column-tog').not(':checked').map(function() {
				var id = this.id;
				return id.substring( id, id.length - 5 );
			}).get().join(',');
		};
	},

	colSpanChange : function(diff) {
		var $t = $('table').find('.colspanchange'), n;
		if ( !$t.length )
			return;
		n = parseInt( $t.attr('colspan'), 10 ) + diff;
		$t.attr('colspan', n.toString());
	}
};

$(document).ready(function(){columns.init();});

validateForm = function( form ) {
	return !$( form )
		.find( '.form-required' )
		.filter( function() { return $( 'input:visible', this ).val() === ''; } )
		.addClass( 'form-invalid' )
		.find( 'input:visible' )
		.change( function() { $( this ).closest( '.form-invalid' ).removeClass( 'form-invalid' ); } )
		.size();
};

// stub for doing better warnings
showNotice = {
	warn : function() {
		var msg = commonL10n.warnDelete || '';
		if ( confirm(msg) ) {
			return true;
		}

		return false;
	},

	note : function(text) {
		alert(text);
	}
};

screenMeta = {
	element: null, // #screen-meta
	toggles: null, // .screen-meta-toggle
	page:    null, // #wpcontent

	init: function() {
		this.element = $('#screen-meta');
		this.toggles = $('.screen-meta-toggle a');
		this.page    = $('#wpcontent');

		this.toggles.click( this.toggleEvent );
	},

	toggleEvent: function( e ) {
		var panel = $( this.href.replace(/.+#/, '#') );
		e.preventDefault();

		if ( !panel.length )
			return;

		if ( panel.is(':visible') )
			screenMeta.close( panel, $(this) );
		else
			screenMeta.open( panel, $(this) );
	},

	open: function( panel, link ) {

		$('.screen-meta-toggle').not( link.parent() ).css('visibility', 'hidden');

		panel.parent().show();
		panel.slideDown( 'fast', function() {
			panel.focus();
			link.addClass('screen-meta-active').attr('aria-expanded', true);
		});

		$( document ).trigger( 'screen:options:open' );
	},

	close: function( panel, link ) {
		panel.slideUp( 'fast', function() {
			link.removeClass('screen-meta-active').attr('aria-expanded', false);
			$('.screen-meta-toggle').css('visibility', '');
			panel.parent().hide();
		});

		$( document ).trigger( 'screen:options:close' );
	}
};

/**
 * Help tabs.
 */
$('.contextual-help-tabs').delegate('a', 'click', function(e) {
	var link = $(this),
		panel;

	e.preventDefault();

	// Don't do anything if the click is for the tab already showing.
	if ( link.is('.active a') )
		return false;

	// Links
	$('.contextual-help-tabs .active').removeClass('active');
	link.parent('li').addClass('active');

	panel = $( link.attr('href') );

	// Panels
	$('.help-tab-content').not( panel ).removeClass('active').hide();
	panel.addClass('active').show();
});

$(document).ready( function() {
	var checks, first, last, checked, sliced, mobileEvent, transitionTimeout, focusedRowActions,
		lastClicked = false,
		menu = $('#adminmenu'),
		pageInput = $('input.current-page'),
		currentPage = pageInput.val();

	// when the menu is folded, make the fly-out submenu header clickable
	menu.on('click.wp-submenu-head', '.wp-submenu-head', function(e){
		$(e.target).parent().siblings('a').get(0).click();
	});

	$('#collapse-menu').on('click.collapse-menu', function() {
		var body = $( document.body ), respWidth, state;

		// reset any compensation for submenus near the bottom of the screen
		$('#adminmenu div.wp-submenu').css('margin-top', '');

		if ( window.innerWidth ) {
			// window.innerWidth is affected by zooming on phones
			respWidth = Math.max( window.innerWidth, document.documentElement.clientWidth );
		} else {
			// IE < 9 doesn't support @media CSS rules
			respWidth = 961;
		}

		if ( respWidth && respWidth < 960 ) {
			if ( body.hasClass('auto-fold') ) {
				body.removeClass('auto-fold').removeClass('folded');
				setUserSetting('unfold', 1);
				setUserSetting('mfold', 'o');
				state = 'open';
			} else {
				body.addClass('auto-fold');
				setUserSetting('unfold', 0);
				state = 'folded';
			}
		} else {
			if ( body.hasClass('folded') ) {
				body.removeClass('folded');
				setUserSetting('mfold', 'o');
				state = 'open';
			} else {
				body.addClass('folded');
				setUserSetting('mfold', 'f');
				state = 'folded';
			}
		}

		$( document ).trigger( 'wp-collapse-menu', { state: state } );
	});

	if ( 'ontouchstart' in window || /IEMobile\/[1-9]/.test(navigator.userAgent) ) { // touch screen device
		// iOS Safari works with touchstart, the rest work with click
		mobileEvent = /Mobile\/.+Safari/.test(navigator.userAgent) ? 'touchstart' : 'click';

		// close any open submenus when touch/click is not on the menu
		$(document.body).on( mobileEvent+'.wp-mobile-hover', function(e) {
			if ( menu.data('wp-responsive') ) {
				return;
			}

			if ( ! $(e.target).closest('#adminmenu').length ) {
				menu.find('li.wp-has-submenu.opensub').removeClass('opensub');
			}
		});

		menu.find('a.wp-has-submenu').on( mobileEvent+'.wp-mobile-hover', function(e) {
			var b, h, o, f, menutop, wintop, maxtop,
				el = $(this),
				parent = el.parent(),
				m = parent.find('.wp-submenu');

			if ( menu.data('wp-responsive') ) {
				return;
			}

			// Show the sub instead of following the link if:
			//	- the submenu is not open
			//	- the submenu is not shown inline or the menu is not folded
			if ( !parent.hasClass('opensub') && ( !parent.hasClass('wp-menu-open') || parent.width() < 40 ) ) {
				e.preventDefault();

				menutop = parent.offset().top;
				wintop = $(window).scrollTop();
				maxtop = menutop - wintop - 30; // max = make the top of the sub almost touch admin bar

				b = menutop + m.height() + 1; // Bottom offset of the menu
				h = $('#wpwrap').height(); // Height of the entire page
				o = 60 + b - h;
				f = $(window).height() + wintop - 50; // The fold

				if ( f < (b - o) ) {
					o = b - f;
				}

				if ( o > maxtop ) {
					o = maxtop;
				}

				if ( o > 1 ) {
					m.css('margin-top', '-'+o+'px');
				} else {
					m.css('margin-top', '');
				}

				menu.find('li.opensub').removeClass('opensub');
				parent.addClass('opensub');
			}
		});
	}

	menu.find('li.wp-has-submenu').hoverIntent({
		over: function() {
			var b, h, o, f, m = $(this).find('.wp-submenu'), menutop, wintop, maxtop, top = parseInt( m.css('top'), 10 );

			if ( isNaN(top) || top > -5 ) { // meaning the submenu is visible
				return;
			}

			if ( menu.data('wp-responsive') ) {
				// The menu is in responsive mode, bail
				return;
			}

			menutop = $(this).offset().top;
			wintop = $(window).scrollTop();
			maxtop = menutop - wintop - 30; // max = make the top of the sub almost touch admin bar

			b = menutop + m.height() + 1; // Bottom offset of the menu
			h = $('#wpwrap').height(); // Height of the entire page
			o = 60 + b - h;
			f = $(window).height() + wintop - 15; // The fold

			if ( f < (b - o) ) {
				o = b - f;
			}

			if ( o > maxtop ) {
				o = maxtop;
			}

			if ( o > 1 ) {
				m.css('margin-top', '-'+o+'px');
			} else {
				m.css('margin-top', '');
			}

			menu.find('li.menu-top').removeClass('opensub');
			$(this).addClass('opensub');
		},
		out: function(){
			if ( menu.data('wp-responsive') ) {
				// The menu is in responsive mode, bail
				return;
			}

			$(this).removeClass('opensub').find('.wp-submenu').css('margin-top', '');
		},
		timeout: 200,
		sensitivity: 7,
		interval: 90
	});

	menu.on('focus.adminmenu', '.wp-submenu a', function(e){
		if ( menu.data('wp-responsive') ) {
			// The menu is in responsive mode, bail
			return;
		}

		$(e.target).closest('li.menu-top').addClass('opensub');
	}).on('blur.adminmenu', '.wp-submenu a', function(e){
		if ( menu.data('wp-responsive') ) {
			// The menu is in responsive mode, bail
			return;
		}

		$(e.target).closest('li.menu-top').removeClass('opensub');
	});

	// Move .updated and .error alert boxes. Don't move boxes designed to be inline.
	$('div.wrap h2:first').nextAll('div.updated, div.error').addClass('below-h2');
	$('div.updated, div.error').not('.below-h2, .inline').insertAfter( $('div.wrap h2:first') );

	// Init screen meta
	screenMeta.init();

	// check all checkboxes
	$('tbody').children().children('.check-column').find(':checkbox').click( function(e) {
		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			checks = $( lastClicked ).closest( 'form' ).find( ':checkbox' );
			first = checks.index( lastClicked );
			last = checks.index( this );
			checked = $(this).prop('checked');
			if ( 0 < first && 0 < last && first != last ) {
				sliced = ( last > first ) ? checks.slice( first, last ) : checks.slice( last, first );
				sliced.prop( 'checked', function() {
					if ( $(this).closest('tr').is(':visible') )
						return checked;

					return false;
				});
			}
		}
		lastClicked = this;

		// toggle "check all" checkboxes
		var unchecked = $(this).closest('tbody').find(':checkbox').filter(':visible').not(':checked');
		$(this).closest('table').children('thead, tfoot').find(':checkbox').prop('checked', function() {
			return ( 0 === unchecked.length );
		});

		return true;
	});

	$('thead, tfoot').find('.check-column :checkbox').on( 'click.wp-toggle-checkboxes', function( event ) {
		var $this = $(this),
			$table = $this.closest( 'table' ),
			controlChecked = $this.prop('checked'),
			toggle = event.shiftKey || $this.data('wp-toggle');

		$table.children( 'tbody' ).filter(':visible')
			.children().children('.check-column').find(':checkbox')
			.prop('checked', function() {
				if ( $(this).is(':hidden') ) {
					return false;
				}

				if ( toggle ) {
					return ! $(this).prop( 'checked' );
				} else if ( controlChecked ) {
					return true;
				}

				return false;
			});

		$table.children('thead,  tfoot').filter(':visible')
			.children().children('.check-column').find(':checkbox')
			.prop('checked', function() {
				if ( toggle ) {
					return false;
				} else if ( controlChecked ) {
					return true;
				}

				return false;
			});
	});

	// Show row actions on keyboard focus of its parent container element or any other elements contained within
	$( 'td.post-title, td.title, td.comment, .bookmarks td.column-name, td.blogname, td.username, .dashboard-comment-wrap' ).focusin(function(){
		clearTimeout( transitionTimeout );
		focusedRowActions = $(this).find( '.row-actions' );
		focusedRowActions.addClass( 'visible' );
	}).focusout(function(){
		// Tabbing between post title and .row-actions links needs a brief pause, otherwise
		// the .row-actions div gets hidden in transit in some browsers (ahem, Firefox).
		transitionTimeout = setTimeout(function(){
			focusedRowActions.removeClass( 'visible' );
		}, 30);
	});

	$('#default-password-nag-no').click( function() {
		setUserSetting('default_password_nag', 'hide');
		$('div.default-password-nag').hide();
		return false;
	});

	// tab in textareas
	$('#newcontent').bind('keydown.wpevent_InsertTab', function(e) {
		var el = e.target, selStart, selEnd, val, scroll, sel;

		if ( e.keyCode == 27 ) { // escape key
			$(el).data('tab-out', true);
			return;
		}

		if ( e.keyCode != 9 || e.ctrlKey || e.altKey || e.shiftKey ) // tab key
			return;

		if ( $(el).data('tab-out') ) {
			$(el).data('tab-out', false);
			return;
		}

		selStart = el.selectionStart;
		selEnd = el.selectionEnd;
		val = el.value;

		try {
			this.lastKey = 9; // not a standard DOM property, lastKey is to help stop Opera tab event. See blur handler below.
		} catch(err) {}

		if ( document.selection ) {
			el.focus();
			sel = document.selection.createRange();
			sel.text = '\t';
		} else if ( selStart >= 0 ) {
			scroll = this.scrollTop;
			el.value = val.substring(0, selStart).concat('\t', val.substring(selEnd) );
			el.selectionStart = el.selectionEnd = selStart + 1;
			this.scrollTop = scroll;
		}

		if ( e.stopPropagation )
			e.stopPropagation();
		if ( e.preventDefault )
			e.preventDefault();
	});

	$('#newcontent').bind('blur.wpevent_InsertTab', function() {
		if ( this.lastKey && 9 == this.lastKey )
			this.focus();
	});

	if ( pageInput.length ) {
		pageInput.closest('form').submit( function() {

			// Reset paging var for new filters/searches but not for bulk actions. See #17685.
			if ( $('select[name="action"]').val() == -1 && $('select[name="action2"]').val() == -1 && pageInput.val() == currentPage )
				pageInput.val('1');
		});
	}

	$('.search-box input[type="search"], .search-box input[type="submit"]').mousedown(function () {
		$('select[name^="action"]').val('-1');
	});

	// Scroll into view when focused
	$('#contextual-help-link, #show-settings-link').on( 'focus.scroll-into-view', function(e){
		if ( e.target.scrollIntoView )
			e.target.scrollIntoView(false);
	});

	// Disable upload buttons until files are selected
	(function(){
		var button, input, form = $('form.wp-upload-form');
		if ( ! form.length )
			return;
		button = form.find('input[type="submit"]');
		input = form.find('input[type="file"]');

		function toggleUploadButton() {
			button.prop('disabled', '' === input.map( function() {
				return $(this).val();
			}).get().join(''));
		}
		toggleUploadButton();
		input.on('change', toggleUploadButton);
	})();
});

// Fire a custom jQuery event at the end of window resize
( function() {
	var timeout;

	function triggerEvent() {
		$(document).trigger( 'wp-window-resized' );
	}

	function fireOnce() {
		window.clearTimeout( timeout );
		timeout = window.setTimeout( triggerEvent, 200 );
	}

	$(window).on( 'resize.wp-fire-once', fireOnce );
}());

$(document).ready( function() {
	var $document = $( document ),
		$window = $( window ),
		$body = $( document.body ),
		$adminMenuWrap = $( '#adminmenuwrap' ),
		$collapseMenu = $( '#collapse-menu' ),
		$wpwrap = $( '#wpwrap' ),
		$adminmenu = $( '#adminmenu' ),
		$overlay = $( '#wp-responsive-overlay' ),
		$toolbar = $( '#wp-toolbar' ),
		$toolbarPopups = $toolbar.find( 'a[aria-haspopup="true"]' ),
		$sortables = $('.meta-box-sortables'),
		stickyMenuActive = false,
		wpResponsiveActive = false;

	window.stickyMenu = {
		enable: function() {
			if ( ! stickyMenuActive ) {
				$document.on( 'wp-window-resized.sticky-menu', $.proxy( this.update, this ) );
				$collapseMenu.on( 'click.sticky-menu', $.proxy( this.update, this ) );
				this.update();
				stickyMenuActive = true;
			}
		},

		disable: function() {
			if ( stickyMenuActive ) {
				$window.off( 'resize.sticky-menu' );
				$collapseMenu.off( 'click.sticky-menu' );
				$body.removeClass( 'sticky-menu' );
				stickyMenuActive = false;
			}
		},

		update: function() {
			// Make the admin menu sticky if the viewport is taller than it
			if ( $window.height() > $adminMenuWrap.height() + 32 ) {
				if ( ! $body.hasClass( 'sticky-menu' ) ) {
					$body.addClass( 'sticky-menu' );
				}
			} else {
				if ( $body.hasClass( 'sticky-menu' ) ) {
					$body.removeClass( 'sticky-menu' );
				}
			}
		}
	};

	window.wpResponsive = {
		init: function() {
			var self = this;

			// Modify functionality based on custom activate/deactivate event
			$document.on( 'wp-responsive-activate.wp-responsive', function() {
				self.activate();
			}).on( 'wp-responsive-deactivate.wp-responsive', function() {
				self.deactivate();
			});

			$( '#wp-admin-bar-menu-toggle a' ).attr( 'aria-expanded', 'false' );

			// Toggle sidebar when toggle is clicked
			$( '#wp-admin-bar-menu-toggle' ).on( 'click.wp-responsive', function( event ) {
				event.preventDefault();
				$wpwrap.toggleClass( 'wp-responsive-open' );
				if ( $wpwrap.hasClass( 'wp-responsive-open' ) ) {
					$(this).find('a').attr( 'aria-expanded', 'true' );
					$( '#adminmenu a:first' ).focus();
				} else {
					$(this).find('a').attr( 'aria-expanded', 'false' );
				}
			} );

			// Add menu events
			$adminmenu.on( 'click.wp-responsive', 'li.wp-has-submenu > a', function( event ) {
				if ( ! $adminmenu.data('wp-responsive') ) {
					return;
				}

				$( this ).parent( 'li' ).toggleClass( 'selected' );
				event.preventDefault();
			});

			self.trigger();
			$document.on( 'wp-window-resized.wp-responsive', $.proxy( this.trigger, this ) );

			// This needs to run later as UI Sortable may be initialized later on $(document).ready()
			$window.on( 'load.wp-responsive', function() {
				var width = navigator.userAgent.indexOf('AppleWebKit/') > -1 ? $window.width() : window.innerWidth;

				if ( width <= 782 ) {
					self.disableSortables();
				}
			});
		},

		activate: function() {
			window.stickyMenu.disable();

			if ( ! $body.hasClass( 'auto-fold' ) ) {
				$body.addClass( 'auto-fold' );
			}

			$adminmenu.data( 'wp-responsive', 1 );
			this.disableSortables();
		},

		deactivate: function() {
			window.stickyMenu.enable();
			$adminmenu.removeData('wp-responsive');
			this.enableSortables();
		},

		trigger: function() {
			var width;

			if ( window.innerWidth ) {
				// window.innerWidth is affected by zooming on phones
				width = Math.max( window.innerWidth, document.documentElement.clientWidth );
			} else {
				// Exclude IE < 9, it doesn't support @media CSS rules
				return;
			}

			if ( width <= 782 ) {
				if ( ! wpResponsiveActive ) {
					$document.trigger( 'wp-responsive-activate' );
					wpResponsiveActive = true;
				}
			} else {
				if ( wpResponsiveActive ) {
					$document.trigger( 'wp-responsive-deactivate' );
					wpResponsiveActive = false;
				}
			}

			if ( width <= 480 ) {
				this.enableOverlay();
			} else {
				this.disableOverlay();
			}
		},

		enableOverlay: function() {
			if ( $overlay.length === 0 ) {
				$overlay = $( '<div id="wp-responsive-overlay"></div>' )
					.insertAfter( '#wpcontent' )
					.hide()
					.on( 'click.wp-responsive', function() {
						$toolbar.find( '.menupop.hover' ).removeClass( 'hover' );
						$( this ).hide();
					});
			}

			$toolbarPopups.on( 'click.wp-responsive', function() {
				$overlay.show();
			});
		},

		disableOverlay: function() {
			$toolbarPopups.off( 'click.wp-responsive' );
			$overlay.hide();
		},

		disableSortables: function() {
			if ( $sortables.length ) {
				try {
					$sortables.sortable('disable');
				} catch(e) {}
			}
		},

		enableSortables: function() {
			if ( $sortables.length ) {
				try {
					$sortables.sortable('enable');
				} catch(e) {}
			}
		}
	};

	window.stickyMenu.enable();
	window.wpResponsive.init();
});

// Make Windows 8 devices play along nicely.
(function(){
	if ( '-ms-user-select' in document.documentElement.style && navigator.userAgent.match(/IEMobile\/10\.0/) ) {
		var msViewportStyle = document.createElement( 'style' );
		msViewportStyle.appendChild(
			document.createTextNode( '@-ms-viewport{width:auto!important}' )
		);
		document.getElementsByTagName( 'head' )[0].appendChild( msViewportStyle );
	}
})();

}( jQuery, window ));
