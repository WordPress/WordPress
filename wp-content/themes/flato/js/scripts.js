/*
	scripts.js
	
	License: GNU General Public License v2.0
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
	
	Copyright: (c) 2013 Theme Meme, http://themememe.com
*/

jQuery(document).ready(function($) {
	
/*  Dropdown menu animation
/* ------------------------------------ */
	$('.menu-bar ul ul.children').hide();
	$('.menu-bar ul li').hover( 
		function() {
			$(this).children('ul.children').slideDown('fast');
		}, 
		function() {
			$(this).children('ul.children').hide();
		}
	);
	
/*  Mobile menu smooth toggle height
/* ------------------------------------ */	
	$('.menu-toggle').on('click', function() {
		$('.menu-toggle').toggleClass('active');
		slide($('.menu-bar > ul', $(this).parent()));
	});
	 
	function slide(content) {
		var wrapper = content.parent();
		var contentHeight = content.outerHeight(true);
		var wrapperHeight = wrapper.height();
	 
		wrapper.toggleClass('menu-expand');
		if (wrapper.hasClass('menu-expand')) {
		setTimeout(function() {
			wrapper.addClass('transition').css('height', contentHeight);
		}, 10);
	}
	else {
		setTimeout(function() {
			wrapper.css('height', wrapperHeight);
			setTimeout(function() {
			wrapper.addClass('transition').css('height', 0);
			}, 10);
		}, 10);
	}
	 
	wrapper.one('transitionEnd webkitTransitionEnd transitionend oTransitionEnd msTransitionEnd', function() {
		if(wrapper.hasClass('open')) {
			wrapper.removeClass('transition').css('height', 'auto');
		}
	});
	}

/*  Header search
/* ------------------------------------ */
	$('.search-toggle').click(function(){
		$('.search-toggle').toggleClass('active');
		$('.search-expand').fadeToggle(250);
            setTimeout(function(){
                $('.search-expand input').focus();
            }, 300);
	});

/*  FlexSlider
/* ------------------------------------ */
	var firstImage = jQuery('.flexslider').find('img').filter(':first'),
		checkforloaded = setInterval(function() {
			var image = firstImage.get(0);
			if (image.complete || image.readyState == 'complete' || image.readyState == 4) {
				clearInterval(checkforloaded);
				jQuery('.flexslider').flexslider({
					animation: "slide",
					useCSS: false, // Fix iPad flickering issue
					slideshow: false,
					directionNav: true,
					controlNav: true,
					pauseOnHover: true,
					slideshowSpeed: 7000,
					animationSpeed: 400,
					smoothHeight: true,
					touch: false
				});
			}
		}, 20);

	$('select').dropkick();
});