jQuery(document).ready( function() {
	// pulse
	jQuery('.fade').animate( { backgroundColor: '#ffffe0' }, 300).animate( { backgroundColor: '#fffbcc' }, 300).animate( { backgroundColor: '#ffffe0' }, 300).animate( { backgroundColor: '#fffbcc' }, 300);

	// show things that should be visible, hide what should be hidden
	jQuery('.hide-if-no-js').removeClass('hide-if-no-js');
	jQuery('.hide-if-js').hide();

	// Basic form validation
	if ( ( 'undefined' != typeof wpAjax ) && jQuery.isFunction( wpAjax.validateForm ) ) {
		jQuery('form.validate').submit( function() { return wpAjax.validateForm( jQuery(this) ); } );
	}

	jQuery('a.no-crazy').click( function() {
		alert( "This feature isn't enabled in this prototype." );
		return false;
	} );
});
/*
(function($) {
	$.fn.tTips = function() {

		$('body').append('<div id="tTips"><p id="tTips_inside"></p></div>');
		var TT = $('#tTips');

		this.each(function() {
			var el = $(this), txt;

			if ( txt = el.attr('title') ) el.attr('tip', txt).removeAttr('title');
			else return;
			el.find('img').removeAttr('alt');

			el.mouseover(function(e) {
				txt = el.attr('tip'), o = el.offset();

				clearTimeout(TT.sD);
				TT.find('p').html(txt);

				TT.css({'top': o.top - 43, 'left': o.left - 5});
				TT.sD = setTimeout(function(){TT.fadeIn(150);}, 100);
			});

			el.mouseout(function() {
				clearTimeout(TT.sD);
				TT.css({display : 'none'});
			})
		});
	}
}(jQuery));
*/
jQuery( function($) {
	var menuToggle = function(ul, effect) {
		if ( !effect ) {
			effect = 'slideToggle';
		}
		ul[effect]().parent().toggleClass( 'wp-menu-open' );
		return false;
	};

	jQuery('#adminmenu li.wp-has-submenu > a').click( function() { return menuToggle( jQuery(this).siblings('ul') ); } );

	jQuery('#dashmenu li.wp-has-submenu').bind( 'mouseenter mouseleave', function() { return menuToggle( jQuery(this).children('ul'), 'toggle' ); } );

} );

//jQuery(function(){jQuery('#media-buttons a').tTips();});
