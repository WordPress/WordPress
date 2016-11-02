/**
 * UpSolution Widget: w-search
 */
(function ($) {
	"use strict";

	$.fn.wSearch = function(){
		return this.each(function(){
			var $this = $(this),
				$input = $this.find('input[name="s"]'),
				focusTimer = null;

			var show = function(){
				$this.addClass('active');
				focusTimer = setTimeout(function(){
					$input.focus();
				}, 300);
			};

			var hide = function(){
				clearTimeout(focusTimer);
				$this.removeClass('active');
				$input.blur();
			};

			$this.find('.w-search-open').click(show);
			$this.find('.w-search-close').click(hide);
			$input.keyup(function(e) {
				if (e.keyCode == 27) hide();
			});

		});
	};

	$(function(){
		jQuery('.w-search').wSearch();
	});
})(jQuery);

/**
 * UpSolution Widget: w-tabs
 */
jQuery('.w-tabs').wTabs();

/**
 * UpSolution Widget: w-blog
 */
jQuery(function($){
	$('.w-blog').wBlog();
});

/**
 * UpSolution Widget: w-portfolio
 */
jQuery(function($){
	$('.w-portfolio').wPortfolio();
});

/**
 * RevSlider support for our tabs
 */

jQuery(function($){
	$('.w-tabs .rev_slider').each(function(){
		var $slider = $(this);
		$slider.bind("revolution.slide.onloaded",function (e) {
			$us.canvas.$container.on('contentChange', function(){
				$slider.revredraw();
			});
		});
	});
});
