var showNotice, adminMenu, columns, validateForm, screenMeta, autofold_menu;
(function($){
// sidebar admin menu
adminMenu = {
	init : function() {
		var menu = $('#adminmenu');

		this.favorites();

		$('#collapse-menu', menu).click(function(){
			if ( $('body').hasClass('folded') ) {
				adminMenu.fold(1);
				deleteUserSetting( 'mfold' );
			} else {
				adminMenu.fold();
				setUserSetting( 'mfold', 'f' );
			}
			return false;
		});

		this.flyout( $('#adminmenu li.wp-has-submenu') );

		this.fold( ! $('body').hasClass('folded') );
	},

	restoreMenuState : function() {
		// (perhaps) needed for back-compat
	},

	flyout: function( el, unbind ) {
		if ( unbind ) {
			el.unbind(); // Unbind flyout
			return;
		}

		el.hoverIntent({
			over: function(e){
				var m, b, h, o, f;
				m = $(this).find('.wp-submenu');
				b = $(this).offset().top + m.height() + 1; // Bottom offset of the menu
				h = $('#wpwrap').height(); // Height of the entire page
				o = 60 + b - h;
				f = $(window).height() + $(window).scrollTop() - 15; // The fold
				if ( f < (b - o) ) {
					o = b - f;
				}
				if ( o > 1 ) {
					m.css({'marginTop':'-'+o+'px'});
				} else if ( m.css('marginTop') ) {
					m.css({'marginTop':''});
				}
				m.addClass('sub-open');
			},
			out: function(){
				$(this).find('.wp-submenu').removeClass('sub-open');
			},
			timeout: 220,
			sensitivity: 8,
			interval: 100
		});
	},

	toggle : function() {
		// Removed in 3.3.
		// (perhaps) needed for back-compat
	},

	fold : function( off ) {
		var current = $('#adminmenu li.wp-has-current-submenu');

		$('body').toggleClass( 'folded', ! off );
		$('body').toggleClass( 'expanded', off );
		this.flyout( current, off );

		// Remove any potentially remaining hoverIntent positioning.
		if ( off )
			current.find('.wp-submenu').css( 'marginTop', '0' );
	},

	favorites : function() {
		$('#favorite-inside').width( $('#favorite-actions').width() - 4 );
		$('#favorite-toggle, #favorite-inside').bind('mouseenter', function() {
			$('#favorite-inside').removeClass('slideUp').addClass('slideDown');
			setTimeout(function() {
				if ( $('#favorite-inside').hasClass('slideDown') ) {
					$('#favorite-inside').slideDown(100);
					$('#favorite-first').addClass('slide-down');
				}
			}, 200);
		}).bind('mouseleave', function() {
			$('#favorite-inside').removeClass('slideDown').addClass('slideUp');
			setTimeout(function() {
				if ( $('#favorite-inside').hasClass('slideUp') ) {
					$('#favorite-inside').slideUp(100, function() {
						$('#favorite-first').removeClass('slide-down');
					});
				}
			}, 300);
		});
	}
};

$(document).ready(function(){ adminMenu.init(); });

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
}

$(document).ready(function(){columns.init();});

validateForm = function( form ) {
	return !$( form ).find('.form-required').filter( function() { return $('input:visible', this).val() == ''; } ).addClass( 'form-invalid' ).find('input:visible').change( function() { $(this).closest('.form-invalid').removeClass( 'form-invalid' ); } ).size();
}

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
	padding: null, // the closed page padding-top property
	top:     null, // the closed element top property
	map: {
		'wp-admin-bar-screen-options': 'screen-options-wrap',
		'wp-admin-bar-help': 'contextual-help-wrap'
	},

	init: function() {
		screenMeta.element = $('#screen-meta');
		screenMeta.toggles = $('.screen-meta-toggle');
		screenMeta.page    = $('#wpcontent');

		screenMeta.toggles.click( screenMeta.toggleEvent );
	},

	toggleEvent: function( e ) {
		var panel;
		e.preventDefault();

		// Check to see if we found a panel.
		if ( ! screenMeta.map[ this.id ] )
			return;

		panel = $('#' + screenMeta.map[ this.id ]);

		if ( panel.is(':visible') )
			screenMeta.close( panel, $(this) );
		else
			screenMeta.open( panel, $(this) );
	},
	open: function( panel, link ) {
		// Close open panel
		screenMeta.toggles.filter('.selected').click();

		// Open selected panel
		link.addClass('selected');

		screenMeta.padding = parseInt( screenMeta.page.css('paddingTop'), 10 );
		screenMeta.top     = parseInt( screenMeta.element.css('top'), 10 );

		panel.show();

		screenMeta.element.css({ top: 0 });
		screenMeta.page.css({ paddingTop: screenMeta.padding + screenMeta.element.outerHeight() });
	},
	close: function( panel, link ) {
		screenMeta.element.css({ top: screenMeta.top });
		screenMeta.page.css({ paddingTop: screenMeta.padding });
		panel.hide();
		link.removeClass('selected');
	}
};


$(document).ready( function() {
	var lastClicked = false, checks, first, last, checked,
		pageInput = $('input.current-page'), currentPage = pageInput.val();

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
				checks.slice( first, last ).prop( 'checked', function(){
					if ( $(this).closest('tr').is(':visible') )
						return checked;

					return false;
				});
			}
		}
		lastClicked = this;
		return true;
	});

	$('thead, tfoot').find('.check-column :checkbox').click( function(e) {
		var c = $(this).prop('checked'),
			kbtoggle = 'undefined' == typeof toggleWithKeyboard ? false : toggleWithKeyboard,
			toggle = e.shiftKey || kbtoggle;

		$(this).closest( 'table' ).children( 'tbody' ).filter(':visible')
		.children().children('.check-column').find(':checkbox')
		.prop('checked', function() {
			if ( $(this).closest('tr').is(':hidden') )
				return false;
			if ( toggle )
				return $(this).prop( 'checked' );
			else if (c)
				return true;
			return false;
		});

		$(this).closest('table').children('thead,  tfoot').filter(':visible')
		.children().children('.check-column').find(':checkbox')
		.prop('checked', function() {
			if ( toggle )
				return false;
			else if (c)
				return true;
			return false;
		});
	});

	$('#default-password-nag-no').click( function() {
		setUserSetting('default_password_nag', 'hide');
		$('div.default-password-nag').hide();
		return false;
	});

	// tab in textareas
	$('#newcontent').bind('keydown.wpevent_InsertTab', function(e) {
		if ( e.keyCode != 9 )
			return true;

		var el = e.target, selStart = el.selectionStart, selEnd = el.selectionEnd, val = el.value, scroll, sel;

		try {
			this.lastKey = 9; // not a standard DOM property, lastKey is to help stop Opera tab event.  See blur handler below.
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

	$('#newcontent').bind('blur.wpevent_InsertTab', function(e) {
		if ( this.lastKey && 9 == this.lastKey )
			this.focus();
	});

	if ( pageInput.length ) {
		pageInput.closest('form').submit( function(e){

			// Reset paging var for new filters/searches but not for bulk actions. See #17685.
			if ( $('select[name="action"]').val() == -1 && $('select[name="action2"]').val() == -1 && pageInput.val() == currentPage )
				pageInput.val('1');
		});
	}

	// auto-fold the menu when screen is under 800px
	$(window).bind('resize.autofold', function(){
		if ( getUserSetting('mfold') == 'f' )
			return;

		var w = $(window).width();

		if ( w <= 800 ) // fold admin menu
			$(document.body).addClass('folded');
		else
			$(document.body).removeClass('folded');

	}).triggerHandler('resize');
});

// internal use
$(document).bind( 'wp_CloseOnEscape', function( e, data ) {
	if ( typeof(data.cb) != 'function' )
		return;

	if ( typeof(data.condition) != 'function' || data.condition() )
		data.cb();

	return true;
});

})(jQuery);
