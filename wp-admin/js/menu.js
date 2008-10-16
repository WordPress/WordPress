(function($){

adminMenu = {
		
	init : function() {
		$('#adminmenu a').attr('tabindex', '10');
		$('#adminmenu a.wp-has-submenu').click( function() { return adminMenu.toggle( $(this).siblings('ul') ); } );
		$('#adminmenu li.wp-has-submenu img.wp-menu-image').dblclick( function() { window.location = $(this).siblings('a.wp-has-submenu')[0].href; } );
		
		var li = document.createElement('li'); // temp
		$(li).attr('id', 'menu-toggle').html('&laquo;&laquo;').click(function(){
			if ( 'o' == getUserSetting( 'mfold' ) ) {
				adminMenu.fold();
				setUserSetting( 'mfold', 'f' );
				$(this).html('&raquo;&raquo;');
			} else {
				adminMenu.fold(1);
				setUserSetting( 'mfold', 'o' );
				$(this).html('&laquo;&laquo;');
			}
		});
		$('#adminmenu').prepend(li);

		if ( 'o' == getUserSetting( 'mfold' ) ) {
			$('#adminmenu li.wp-has-submenu').each(function(i, e) {
				var v = getUserSetting( 'm'+i );
				if ( $(e).hasClass('wp-has-current-submenu') ) return true; // leave the current parent open
	
				if ( 'o' == v ) $(e).addClass('wp-menu-open');
				else if ( 'c' == v ) $(e).removeClass('wp-menu-open');	
			});
		} else {
			this.fold();
			$('#menu-toggle').html('&raquo;&raquo;');
		}
	},

	toggle : function(ul, effect) {
		if ( !effect )
			effect = 'slideToggle';

		ul[effect](150).parent().toggleClass( 'wp-menu-open' );

		$('#adminmenu li.wp-has-submenu').each(function(i, e) {
			var v = $(e).hasClass('wp-menu-open') ? 'o' : 'c';
			setUserSetting( 'm'+i, v );
		});

		return false;
	},
	
	fold : function(off) {
		if (off) {
			$('#wpbody-content').css('marginLeft', '140px');
			$('#adminmenu').removeClass('folded');
			$('#adminmenu li.wp-submenu-head').hide();
			$('#adminmenu a.wp-has-submenu').show();
			$('#adminmenu li.wp-has-submenu').unbind().css('width', '');
		} else {
			$('#adminmenu').addClass('folded');
			$('#adminmenu a.wp-has-submenu, #adminmenu .wp-submenu').hide();
			$('#adminmenu li.wp-submenu-head').show();
			$('#wpbody-content').css('marginLeft', '38px');
			$('#adminmenu li.wp-has-submenu').css({'width':'24px'}).hoverIntent({
				over: function(){ $(this).find('.wp-submenu').show(); },
				out: function(){ $(this).find('.wp-submenu').hide(); },
				timeout: 220,
				sensitivity: 8,
				interval: 100
			});
		}
	}
};

$(document).ready(function(){adminMenu.init();});
})(jQuery);

jQuery( function($) {	
	$('#favorite-actions').bind( 'mouseenter', function(){$('#favorite-action').removeClass('slideUp').addClass('slideDown'); setTimeout(function(){if ( $('#favorite-action').hasClass('slideDown') ) { $('#favorite-action').slideDown('fast') }}, 300) } );
	$('#favorite-actions').bind( 'mouseleave', function(){$('#favorite-action').removeClass('slideDown').addClass('slideUp'); setTimeout(function(){if ( $('#favorite-action').hasClass('slideUp') ) { $('#favorite-action').slideUp('fast') }}, 500) } );
} );
