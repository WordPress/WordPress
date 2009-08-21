var showNotice, adminMenu, columns, validateForm;
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
		
		$('.separator', menu).click(function(){
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
		
		this.restoreMenuState();
	},

	restoreMenuState : function() {
		$('li.wp-has-submenu', '#adminmenu').each(function(i, e) {
			var v = getUserSetting( 'm'+i );
			if ( $(e).hasClass('wp-has-current-submenu') )
				return true; // leave the current parent open

			if ( 'o' == v )
				$(e).addClass('wp-menu-open');
			else if ( 'c' == v )
				$(e).removeClass('wp-menu-open');
		});
	},

	toggle : function(el) {
		el['slideToggle'](150, function() {
			el.css('display','');
		}).parent().toggleClass( 'wp-menu-open' );

		$('.wp-has-submenu', '#adminmenu').each(function(i, e) {
			var v = $(e).hasClass('wp-menu-open') ? 'o' : 'c';
			setUserSetting( 'm'+i, v );
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
		$('.hide-column-tog', '#adv-settings').click( function() {
			var column = $(this).val();
			if ( $(this).attr('checked') )
				$('.column-' + column).show();
			else
				$('.column-' + column).hide();

			columns.save_manage_columns_state();
		});
	},

	save_manage_columns_state : function() {
		var hidden = $('.manage-column').filter(':hidden').map(function() { return this.id; }).get().join(',');
		$.post(ajaxurl, {
			action: 'hidden-columns',
			hidden: hidden,
			screenoptionnonce: $('#screenoptionnonce').val(),
			page: pagenow
		});
	}
}

$(document).ready(function(){columns.init();});

validateForm = function( form ) {
	return !$( form ).find('.form-required').filter( function() { return $('input:visible', this).val() == ''; } ).addClass( 'form-invalid' ).change( function() { $(this).removeClass( 'form-invalid' ); } ).size();
}

})(jQuery);

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

jQuery(document).ready( function($) {
	var lastClicked = false, checks, first, last, checked;

	// pulse
	$('div.fade').animate( { opacity: .5 }, 400)
	.animate( { opacity: 1 }, 400)
	.animate( { opacity: .5 }, 400)
	.animate( { opacity: 1 }, 400);

	// Move .updated and .error alert boxes
	$('div.wrap h2:first').nextAll('div.updated, div.error').addClass('below-h2');
	$('div.updated, div.error').not('.below-h2').insertAfter( $('div.wrap h2:first') );

	// screen settings tab
	$('#show-settings-link').click(function () {
		if ( ! $('#screen-options-wrap').hasClass('screen-options-open') )
			$('#contextual-help-link-wrap').css('visibility', 'hidden');

		$('#screen-options-wrap').slideToggle('fast', function(){
			if ( $(this).hasClass('screen-options-open') ) {
				$('#show-settings-link').css({'backgroundImage':'url("images/screen-options-right.gif")'});
				$('#contextual-help-link-wrap').css('visibility', '');
				$(this).removeClass('screen-options-open');
			} else {
				$('#show-settings-link').css({'backgroundImage':'url("images/screen-options-right-up.gif")'});
				$(this).addClass('screen-options-open');
			}
		});
		return false;
	});

	// help tab
	$('#contextual-help-link').click(function () {
		if ( ! $('#contextual-help-wrap').hasClass('contextual-help-open') )
			$('#screen-options-link-wrap').css('visibility', 'hidden');

		$('#contextual-help-wrap').slideToggle('fast', function() {
			if ( $(this).hasClass('contextual-help-open') ) {
				$('#contextual-help-link').css({'backgroundImage':'url("images/screen-options-right.gif")'});
				$('#screen-options-link-wrap').css('visibility', '');
				$(this).removeClass('contextual-help-open');
			} else {
				$('#contextual-help-link').css({'backgroundImage':'url("images/screen-options-right-up.gif")'});
				$(this).addClass('contextual-help-open');
			}
		});
		return false;
	});

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

	$('thead, tfoot').find(':checkbox').click( function(e) {
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
});

jQuery(document).ready( function($){
	var turboNag = $('span.turbo-nag', '#user_info');

	if ( !turboNag.length || ('undefined' != typeof(google) && google.gears) )
		return;

	if ( 'undefined' != typeof GearsFactory ) {
		return;
	} else {
		try {
			if ( ( 'undefined' != typeof window.ActiveXObject && ActiveXObject('Gears.Factory') ) ||
				( 'undefined' != typeof navigator.mimeTypes && navigator.mimeTypes['application/x-googlegears'] ) ) {
					return;
			}
		} catch(e){}
	}

	turboNag.show();
});
