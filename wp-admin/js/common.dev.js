var showNotice, adminMenu, columns, validateForm, screenMeta;
(function($){
// sidebar admin menu
adminMenu = {
	init : function() {
		var menu = $('#adminmenu');

		$('.wp-menu-toggle', menu).each( function() {
			var t = $(this), sub = t.siblings('.wp-submenu');
			if ( sub.length )
				t.click(function(){ adminMenu.toggle( sub ); });
			else
				t.hide();
		});

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

		if ( $('body').hasClass('folded') )
			this.fold();
	},

	restoreMenuState : function() {
		// (perhaps) needed for back-compat
	},

	toggle : function(el) {
		el.slideToggle(150, function() {
			var id = el.removeAttr('style').parent().toggleClass( 'wp-menu-open' ).attr('id');
			if ( id ) {
				$('li.wp-has-submenu', '#adminmenu').each(function(i, e) {
					if ( id == e.id ) {
						var v = $(e).hasClass('wp-menu-open') ? 'o' : 'c';
						setUserSetting( 'm'+i, v );
					}
				});
			}
		});

		return false;
	},

	fold : function(off) {
		if (off) {
			$('body').removeClass('folded');
			$('#adminmenu li.wp-has-submenu').unbind();
		} else {
			$('body').addClass('folded');
			$('#adminmenu li.wp-has-submenu').hoverIntent({
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
				out: function(){ $(this).find('.wp-submenu').removeClass('sub-open').css({'marginTop':''}); },
				timeout: 220,
				sensitivity: 8,
				interval: 100
			});

		}
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
			if ( $t.attr('checked') )
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
	links: {
		'screen-options-link-wrap': 'screen-options-wrap',
		'contextual-help-link-wrap': 'contextual-help-wrap'
	},
	init: function() {
		$('.screen-meta-toggle').click( screenMeta.toggleEvent );
	},
	toggleEvent: function( e ) {
		var panel;
		e.preventDefault();

		// Check to see if we found a panel.
		if ( ! screenMeta.links[ this.id ] )
			return;

		panel = $('#' + screenMeta.links[ this.id ]);

		if ( panel.is(':visible') )
			screenMeta.close( panel, $(this) );
		else
			screenMeta.open( panel, $(this) );
	},
	open: function( panel, link ) {
		$('.screen-meta-toggle').not( link ).css('visibility', 'hidden');

		panel.slideDown( 'fast', function() {
			link.addClass('screen-meta-active');
		});
	},
	close: function( panel, link ) {
		panel.slideUp( 'fast', function() {
			link.removeClass('screen-meta-active');
			$('.screen-meta-toggle').css('visibility', '');
		});
	}
};

$(document).ready( function() {
	var lastClicked = false, checks, first, last, checked, dropdown;

	// Move .updated and .error alert boxes. Don't move boxes designed to be inline.
	$('div.wrap h2:first').nextAll('div.updated, div.error').addClass('below-h2');
	$('div.updated, div.error').not('.below-h2, .inline').insertAfter( $('div.wrap h2:first') );

	// Init screen meta
	screenMeta.init();

	// User info dropdown.
	dropdown = {
		doc: $(document),
		element: $('#user_info'),
		open: function() {
			if ( ! dropdown.element.hasClass('active') ) {
				dropdown.element.addClass('active');
				dropdown.doc.one( 'click', dropdown.close );
				return false;
			}
		},
		close: function() {
			dropdown.element.removeClass('active');
		}
	};

	dropdown.element.click( dropdown.open );

	// check all checkboxes
	$('tbody').children().children('.check-column').find(':checkbox').click( function(e) {
		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			checks = $( lastClicked ).closest( 'form' ).find( ':checkbox' );
			first = checks.index( lastClicked );
			last = checks.index( this );
			checked = $(this).attr('checked');
			if ( 0 < first && 0 < last && first != last ) {
				checks.slice( first, last ).attr( 'checked', function(){
					if ( $(this).closest('tr').is(':visible') )
						return checked ? 'checked' : '';

					return '';
				});
			}
		}
		lastClicked = this;
		return true;
	});

	$('thead, tfoot').find('.check-column :checkbox').click( function(e) {
		var c = $(this).attr('checked'),
			kbtoggle = 'undefined' == typeof toggleWithKeyboard ? false : toggleWithKeyboard,
			toggle = e.shiftKey || kbtoggle;

		$(this).closest( 'table' ).children( 'tbody' ).filter(':visible')
		.children().children('.check-column').find(':checkbox')
		.attr('checked', function() {
			if ( $(this).closest('tr').is(':hidden') )
				return '';
			if ( toggle )
				return $(this).attr( 'checked' ) ? '' : 'checked';
			else if (c)
				return 'checked';
			return '';
		});

		$(this).closest('table').children('thead,  tfoot').filter(':visible')
		.children().children('.check-column').find(':checkbox')
		.attr('checked', function() {
			if ( toggle )
				return '';
			else if (c)
				return 'checked';
			return '';
		});
	});

	$('#default-password-nag-no').click( function() {
		setUserSetting('default_password_nag', 'hide');
		$('div.default-password-nag').hide();
		return false;
	});

	// tab in textareas
	$('#newcontent').keydown(function(e) {
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

	$('#newcontent').blur(function(e) {
		if ( this.lastKey && 9 == this.lastKey )
			this.focus();
	});
});

})(jQuery);