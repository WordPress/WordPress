(function($){

adminMenu = {
		
	init : function() {
		$('#adminmenu a').attr('tabindex', '10');
		$('#adminmenu div.wp-menu-toggle').each( function() {
			if ( $(this).siblings('.wp-submenu').length ) 
				$(this).click(function(){ adminMenu.toggle( $(this).siblings('.wp-submenu') ); });
			else
				$(this).hide();
		});
		$('#adminmenu li.menu-top .wp-menu-image').click( function() { window.location = $(this).siblings('a.menu-top')[0].href; } );
		this.favorites();

		$('.wp-menu-separator').click(function(){
			if ( $('#adminmenu').hasClass('folded') ) {
				adminMenu.fold(1);
				setUserSetting( 'mfold', 'o' );
			} else {
				adminMenu.fold();
				setUserSetting( 'mfold', 'f' );
			}
		});

		if ( 'f' != getUserSetting( 'mfold' ) ) {
			this.restoreMenuState();
		} else {
			this.fold();
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
			$('#adminmenu').removeClass('folded');
			$('#adminmenu li.wp-has-submenu').unbind();
		} else {
			$('#adminmenu').addClass('folded');
			$('#adminmenu li.wp-has-submenu').hoverIntent({
				over: function(e){
					var m = $(this).find('.wp-submenu'), t = e.clientY, H = $(window).height(), h = m.height(), o;

					if ( (t+h+10) > H ) {
						o = (t+h+10) - H;
						m.css({'marginTop':'-'+o+'px'});
					} else if ( m.css('marginTop') ) {
						m.css({'marginTop':''})
					}
					m.addClass('sub-open');
				},
				out: function(){ $(this).find('.wp-submenu').removeClass('sub-open'); },
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

})(jQuery);
