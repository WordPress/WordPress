var showNotice, adminMenu, columns;
(function($){
// sidebar admin menu
adminMenu = {

	init : function() {
		$('#adminmenu div.wp-menu-toggle').each( function() {
			if ( $(this).siblings('.wp-submenu').length )
				$(this).click(function(){ adminMenu.toggle( $(this).siblings('.wp-submenu') ); });
			else
				$(this).hide();
		});
		$('#adminmenu li.menu-top .wp-menu-image').click( function() { window.location = $(this).siblings('a.menu-top')[0].href; } );
		this.favorites();

		$('a.separator').click(function(){
			if ( $('body').hasClass('folded') ) {
				adminMenu.fold(1);
				deleteUserSetting( 'mfold' );
			} else {
				adminMenu.fold();
				setUserSetting( 'mfold', 'f' );
			}
			return false;
		});

		if ( $('body').hasClass('folded') ) {
			this.fold();
		} else {
			this.restoreMenuState();
		}
	},

	restoreMenuState : function() {
		$('#adminmenu li.wp-has-submenu').each(function(i, e) {
			var v = getUserSetting( 'm'+i );
			if ( $(e).hasClass('wp-has-current-submenu') ) return true; // leave the current parent open

			if ( 'o' == v ) $(e).addClass('wp-menu-open');
			else if ( 'c' == v ) $(e).removeClass('wp-menu-open');
		});
	},

	toggle : function(el) {

		el['slideToggle'](150, function(){el.css('display','');}).parent().toggleClass( 'wp-menu-open' );

		$('#adminmenu li.wp-has-submenu').each(function(i, e) {
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
					var m = $(this).find('.wp-submenu'), t = e.clientY, H = $(window).height(), h = m.height(), o;

					if ( (t+h+10) > H ) {
						o = (t+h+10) - H;
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
		$('#favorite-inside').width($('#favorite-actions').width()-4);
		$('#favorite-toggle, #favorite-inside').bind( 'mouseenter', function(){$('#favorite-inside').removeClass('slideUp').addClass('slideDown'); setTimeout(function(){if ( $('#favorite-inside').hasClass('slideDown') ) { $('#favorite-inside').slideDown(100); $('#favorite-first').addClass('slide-down'); }}, 200) } );

		$('#favorite-toggle, #favorite-inside').bind( 'mouseleave', function(){$('#favorite-inside').removeClass('slideDown').addClass('slideUp'); setTimeout(function(){if ( $('#favorite-inside').hasClass('slideUp') ) { $('#favorite-inside').slideUp(100, function(){ $('#favorite-first').removeClass('slide-down'); } ); }}, 300) } );
	}
};

$(document).ready(function(){adminMenu.init();});

// show/hide/save table columns
columns = {
	init : function() {
		$('.hide-column-tog').click( function() {
			var column = $(this).val(), show = $(this).attr('checked');
			if ( show ) {
				$('.column-' + column).show();
			} else {
				$('.column-' + column).hide();
			}
			columns.save_manage_columns_state();
		} );
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
	$('.fade').animate( { backgroundColor: '#ffffe0' }, 300).animate( { backgroundColor: '#fffbcc' }, 300).animate( { backgroundColor: '#ffffe0' }, 300).animate( { backgroundColor: '#fffbcc' }, 300);

	// Move .updated and .error alert boxes
	$('div.wrap h2 ~ div.updated, div.wrap h2 ~ div.error').addClass('below-h2');
	$('div.updated, div.error').not('.below-h2').insertAfter('div.wrap h2:first');

	// show warnings
	$('#doaction, #doaction2').click(function(){
		if ( $('select[name="action"]').val() == 'delete' || $('select[name="action2"]').val() == 'delete' ) {
			return showNotice.warn();
		}
	});

	// screen settings tab
	$('#show-settings-link').click(function () {
		if ( ! $('#screen-options-wrap').hasClass('screen-options-open') ) {
			$('#contextual-help-link-wrap').css('visibility', 'hidden');
		}
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
		if ( ! $('#contextual-help-wrap').hasClass('contextual-help-open') ) {
			$('#screen-options-link-wrap').css('visibility', 'hidden');
		}
		$('#contextual-help-wrap').slideToggle('fast', function(){
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
	$('#contextual-help-link-wrap, #screen-options-link-wrap').show();

	// check all checkboxes
	$( 'table:visible tbody .check-column :checkbox' ).click( function(e) {
		if ( 'undefined' == e.shiftKey ) { return true; }
		if ( e.shiftKey ) {
			if ( !lastClicked ) { return true; }
			checks = $( lastClicked ).parents( 'form:first' ).find( ':checkbox' );
			first = checks.index( lastClicked );
			last = checks.index( this );
			checked = $(this).attr('checked');
			if ( 0 < first && 0 < last && first != last ) {
				checks.slice( first, last ).attr( 'checked', function(){
					if ( $(this).parents('tr').is(':visible') )
						return checked ? 'checked' : '';

					return '';
				});
			}
		}
		lastClicked = this;
		return true;
	} );
	$( 'thead :checkbox, tfoot :checkbox' ).click( function(e) {
		var c = $(this).attr('checked'), kbtoggle = 'undefined' == typeof toggleWithKeyboard ? false : toggleWithKeyboard, toggle = e.shiftKey || kbtoggle;


		$(this).parents( 'form:first' ).find( 'table tbody:visible' ).find( '.check-column :checkbox' ).attr( 'checked', function() {
			if ( $(this).parents('tr').is(':hidden') )
				return '';
			if ( toggle )
				return $(this).attr( 'checked' ) ? '' : 'checked';
			else if (c)
				return 'checked';
			return '';
		});
		$(this).parents( 'form:first' ).find( 'table thead:visible, table tfoot:visible').find( '.check-column :checkbox' ).attr( 'checked', function() {
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

(function(){
	if ( 'undefined' != typeof google && google.gears ) return;

	var gf = false;
	if ( 'undefined' != typeof GearsFactory ) {
		gf = new GearsFactory();
	} else {
		try {
			if ( window.ActiveXObject ) {
				gf = new ActiveXObject('Gears.Factory');
				if ( gf && gf.getBuildInfo().indexOf('ie_mobile') != -1 )
					gf.privateSetGlobalObject(this);
			} else if ( ( 'undefined' != typeof navigator.mimeTypes ) && navigator.mimeTypes['application/x-googlegears'] ) {
				gf = document.createElement("object");
				gf.style.display = "none";
				gf.width = 0;
				gf.height = 0;
				gf.type = "application/x-googlegears";
				document.documentElement.appendChild(gf);
			}
		} catch(e){}
	}
	if ( gf && gf.hasPermission )
		return;

	jQuery('.turbo-nag').show();
})();
