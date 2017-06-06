// et_switcher plugin v2.2
(function($)
{
	$.fn.et_shortcodes_switcher = function(options)
	{
		var defaults =
		{
		   slides: '>div',
		   activeClass: 'active',
		   linksNav: '',
		   findParent: true, //use parent elements to define active states
		   lengthElement: 'li', //parent element, used only if findParent is set to true
		   useArrows: false,
		   arrowLeft: 'a#prev-arrow',
		   arrowRight: 'a#next-arrow',
		   auto: false,
		   autoSpeed: 5000,
		   slidePadding: '',
		   pauseOnHover: true,
		   fx: 'fade',
		   sliderType: ''
		}

		var options = $.extend(defaults, options);

		return this.each(function()
		{
			var slidesContainer = jQuery(this).parent().css('position','relative'),
				$slides = jQuery(this).css({'overflow':'hidden','position':'relative'}),
				$slides_wrapper_box = slidesContainer.find('.et-tabs-content-wrapper'),
				$slides_wrapper = $slides_wrapper_box.parent(),
				$slide = $slides.find( '.et-tabs-content-wrapper' + options.slides),
				slidesNum = $slide.length,
				zIndex = slidesNum,
				currentPosition = 1,
				slideHeight = 0,
				$activeSlide,
				$nextSlide,
				slides_wrapper_width = $slides_wrapper.width(),
				$et_shortcodes_mobile_nav,
				$et_shortcodes_mobile_controls;

			if (options.fx === 'slide') {
				$slides_wrapper_box.width( ( ( slidesNum + 2 ) * 200 ) + '%' );
				$slide.css({ 'width' : slides_wrapper_width, 'visibility' : 'visible' });

				$slides_wrapper_box.append( $slide.filter(':first').clone().removeClass().addClass('et_slidecontent_cloned') );
				$slides_wrapper_box.prepend( $slide.filter(':last').clone().removeClass().addClass('et_slidecontent_cloned') );

				$slides_wrapper_box.css( 'left', -slides_wrapper_width );
			}

			$slide.filter(':first').css({'display':'block'}).addClass('et_shortcode_slide_active');

			if (options.slidePadding != '') $slide.css('padding',options.slidePadding);

			if (options.linksNav != '') {
				var linkSwitcher = jQuery(options.linksNav);

				var linkSwitcherTab = '';
				if (options.findParent) linkSwitcherTab = linkSwitcher.parent();
				else linkSwitcherTab = linkSwitcher;

				if (!linkSwitcherTab.filter('.active').length) linkSwitcherTab.filter(':first').addClass('active');

				linkSwitcher.click(function(){

					var targetElement, orderNum;

					if (options.findParent) targetElement = jQuery(this).parent();
					else targetElement = jQuery(this);

					orderNum = targetElement.prevAll(options.lengthElement).length+1;

					if (orderNum > currentPosition) gotoSlide(orderNum, 1);
					else gotoSlide(orderNum, -1);

					return false;
				});
			}


			if (options.useArrows) {
				var $right_arrow = jQuery(options.arrowRight),
					$left_arrow = jQuery(options.arrowLeft);

				$right_arrow.click( function(){
					et_shortcodes_go_to_next_slide();
					return false;
				} );

				$left_arrow.click( function(){
					et_shortcodes_go_to_previous_slide();
					return false;
				} );

			}

			function changeTab(){
				if (linkSwitcherTab != '') {
					linkSwitcherTab.siblings().removeClass('active');
					linkSwitcherTab.filter(':eq('+(currentPosition-1)+')').addClass('active');
				}
			}

			function gotoSlide(slideNumber,dir){
				if ($slide.filter(':animated').length) return;

				$activeSlide = $slide.parent().find('.et_slidecontent').filter(':eq('+(currentPosition-1)+')');

				if (currentPosition === slideNumber) return;

				$activeSlide.removeClass('et_shortcode_slide_active');

				$nextSlide = $slide.parent().find('.et_slidecontent').filter(':eq('+(slideNumber-1)+')').addClass('et_shortcode_slide_active');

				if ((currentPosition > slideNumber || currentPosition === 1) && (dir === -1)) {
					if (options.fx === 'slide') slideBack(500);
					if (options.fx === 'fade') slideFade(500);
				} else {
					if (options.fx === 'slide') slideForward(500);
					if (options.fx === 'fade') slideFade(500);
				}

				currentPosition = $nextSlide.prevAll('.et_slidecontent').length + 1;

				if (options.linksNav != '') changeTab();

				if ( options.sliderType === 'images' || options.sliderType === 'simple' ){
					$et_shortcodes_mobile_controls.find('li').removeClass('et_shortcodes_active_control');
					$et_shortcodes_mobile_controls.find('li').eq(currentPosition-1).addClass('et_shortcodes_active_control');
				}

				return false;
			}


			if (options.auto) {
				auto_rotate();
				var pauseSlider = false;
			}

			if (options.pauseOnHover) {
				slidesContainer.hover(function(){
					pauseSlider = true;
				},function(){
					pauseSlider = false;
				});
			}

			function auto_rotate(){
				interval_shortcodes = setInterval(function(){
					if (!pauseSlider) {
						if (currentPosition === slidesNum)
							gotoSlide(1,1);
						else
							gotoSlide(currentPosition+1,1);

						if (options.linksNav != '') changeTab();
					}
				},options.autoSpeed);
			}

			function slideFade(speed){
				$activeSlide.css( { 'display' : 'none', 'opacity' : '0' } );

				$nextSlide.css({'opacity' : '0', 'display':'block'}).animate( { opacity : 1 }, 700 );
			}

			function slideForward(speed){
				var next_slide_order = $nextSlide.prevAll('.et_slidecontent').length+1,
					go_to_first_slide = false;

				if ( $activeSlide.next('.et_slidecontent_cloned').length ) {
					next_slide_order = $activeSlide.prevAll().length+1;
					go_to_first_slide = true;
				}

				$slides_wrapper_box.animate( { left : - ( $slides_wrapper.width() * next_slide_order ) }, 500, function(){
					if ( go_to_first_slide ) $slides_wrapper_box.css( 'left', - ( $slides_wrapper.width() ) );
				} );
			}

			function slideBack(speed){
				var next_slide_order = $nextSlide.prevAll('.et_slidecontent').length+1,
					go_to_last_slide = false;

				if ( $activeSlide.prev('.et_slidecontent_cloned').length ) {
					next_slide_order = 0;
					go_to_last_slide = true;
				}

				$slides_wrapper_box.animate( { left : - ( $slides_wrapper.width() * next_slide_order ) }, 500, function(){
					if ( go_to_last_slide ) $slides_wrapper_box.css( 'left', - ( $slides_wrapper.width() * slidesNum ) );
				} );
			}

			if (options.fx === 'slide') {
				$(window).resize( function(){
					$slides_wrapper_box.find('>div').css({ 'width' : $slides_wrapper.width() });
					$slides_wrapper_box.css( 'left', -( $slides_wrapper.width() * currentPosition ) );
				} );
			}

			et_generate_mobile_nav();

			function et_generate_mobile_nav(){
				var et_shortcodes_slides_num = slidesContainer.find( '.et_slidecontent' ).length,
					et_shortcodes_controllers_html = '';

				if ( et_shortcodes_slides_num > 1 && ( options.sliderType === 'images' || options.sliderType === 'simple' ) ){
					slidesContainer.append( '<div class="et_shortcodes_controller_nav">' + '<ul class="et_shortcodes_controls"></ul>' + '<ul class="et_shortcodes_controls_arrows"><li><a href="#" class="et_sc_nav_next">' + et_shortcodes_strings.next + '<span></span></a></li><li><a href="#" class="et_sc_nav_prev">' + et_shortcodes_strings.previous + '<span></span></a></li></ul>' + '</div>' );

					$et_shortcodes_mobile_controls = slidesContainer.find('.et_shortcodes_controls');
					for ( var i = 0; i < et_shortcodes_slides_num; i++ ) {
						et_shortcodes_controllers_html += '<li><a href="#"></a></li>';
					}
					$et_shortcodes_mobile_controls.prepend( et_shortcodes_controllers_html );
					$et_shortcodes_mobile_controls.find( 'li:first' ).addClass('et_shortcodes_active_control');
					$et_shortcodes_mobile_controls.find( 'a' ).click( function(){
						var $this_control = $(this),
							$this_control_li = $this_control.parent('li'),
							this_order = $this_control_li.prevAll().length+1;

						if ( this_order == currentPosition ) return false;

						if ( this_order > currentPosition ) gotoSlide(this_order, 1);
						else gotoSlide(this_order, -1);

						return false;
					} );

					$et_shortcodes_mobile_nav = slidesContainer.find('.et_shortcodes_controls_arrows');
					$et_shortcodes_mobile_nav.find('a').click( function(){
						var $this_link = jQuery(this),
							et_active_slide_order;

						if ( $this_link.hasClass( 'et_sc_nav_next' ) )
							et_shortcodes_go_to_next_slide();

						if ( $this_link.hasClass( 'et_sc_nav_prev' ) )
							et_shortcodes_go_to_previous_slide();

						$et_shortcodes_mobile_controls.find('li').removeClass('et_shortcodes_active_control');

						et_active_slide_order = currentPosition - 1;

						$et_shortcodes_mobile_controls.find('li').eq( et_active_slide_order ).addClass('et_shortcodes_active_control');

						return false;
					} );
				} else if ( options.sliderType !== 'images' && options.sliderType !== 'simple' ) {
					slidesContainer.prepend( '<ul class="et_shortcodes_mobile_nav"><li><a href="#" class="et_sc_nav_next">' + et_shortcodes_strings.next + '<span></span></a></li><li><a href="#" class="et_sc_nav_prev">' + et_shortcodes_strings.previous + '<span></span></a></li></ul>' );
					$et_shortcodes_mobile_nav = slidesContainer.find('.et_shortcodes_mobile_nav');
					$et_shortcodes_mobile_nav.find('a').click( function(){
						var $this_link = jQuery(this);

						if ( $this_link.hasClass( 'et_sc_nav_next' ) )
							et_shortcodes_go_to_next_slide();

						if ( $this_link.hasClass( 'et_sc_nav_prev' ) )
							et_shortcodes_go_to_previous_slide();

						return false;
					} );
				}
			}

			function et_shortcodes_go_to_next_slide(){
				if (currentPosition === slidesNum)
					gotoSlide(1,1);
				else
					gotoSlide(currentPosition+1),1;

				if (options.linksNav != '') changeTab();
			}

			function et_shortcodes_go_to_previous_slide(){
				if (currentPosition === 1)
					gotoSlide(slidesNum,-1);
				else
					gotoSlide(currentPosition-1,-1);

				if (options.linksNav != '') changeTab();
			}
		});
	}
})(jQuery);
// end et_switcher plugin v2

/////// Shortcodes Javascript ///////
jQuery(document).ready(function($){
	var $et_pricing_table_button = $('.pricing-table a.icon-button');

	$et_tooltip = $('.et-tooltip');
	$et_tooltip.live('mouseover mouseout', function(event){
		if (event.type == 'mouseover') {
			$(this).find('.et-tooltip-box').stop(true,true).animate({ opacity: 'show', bottom: '25px' }, 300);
		} else {
			$(this).find('.et-tooltip-box').delay(200).animate({ opacity: 'hide', bottom: '35px' }, 300);
		}
	});
	// learn more
	$et_learn_more = $('.et-learn-more .heading-more');
	$et_learn_more.live('click', function() {
		if ( $(this).hasClass('open') )
			$(this).removeClass('open');
		else
			$(this).addClass('open');

		$(this).parent('.et-learn-more').find('.learn-more-content').animate({ opacity: 'toggle', height: 'toggle' }, 300);
	});

	$('.et-learn-more').not('.et-open').find('.learn-more-content').css( { 'visibility' : 'visible', 'display' : 'none' } );

	$et_pricing_table_button.each( function(){
		var $this_button 			= $(this),
			this_button_width 		= $this_button.width(),
			this_button_innerwidth 	= $this_button.innerWidth();

		$this_button.css( { width : this_button_width, 'marginLeft' : '-' + ( this_button_innerwidth / 2 ) + 'px', 'visibility' : 'visible' } );
	} );

	var $et_shortcodes_tabs = $('.et-tabs-container, .tabs-left, .et-simple-slider, .et-image-slider');
	$et_shortcodes_tabs.each(function(i){
		var et_shortcodes_tab_class = $(this).attr('class'),
			et_shortcodes_tab_autospeed_class_value = /et_sliderauto_speed_(\d+)/g,
			et_shortcodes_tab_autospeed = et_shortcodes_tab_autospeed_class_value.exec( et_shortcodes_tab_class ),
			et_shortcodes_tab_auto_class_value = /et_sliderauto_(\w+)/g,
			et_shortcodes_tab_auto = et_shortcodes_tab_auto_class_value.exec( et_shortcodes_tab_class ),
			et_shortcodes_tab_type_class_value = /et_slidertype_(\w+)/g,
			et_shortcodes_tab_type = et_shortcodes_tab_type_class_value.exec( et_shortcodes_tab_class ),
			et_shortcodes_tab_fx_class_value = /et_sliderfx_(\w+)/g,
			et_shortcodes_tab_fx = et_shortcodes_tab_fx_class_value.exec( et_shortcodes_tab_class ),
			et_shortcodes_tab_apply_to_element = '.et-tabs-content',
			et_shortcodes_tab_settings = {};

		et_shortcodes_tab_settings.linksNav = $(this).find('.et-tabs-control li a');
		et_shortcodes_tab_settings.findParent = true;
		et_shortcodes_tab_settings.fx = et_shortcodes_tab_fx[1];
		et_shortcodes_tab_settings.auto = 'false' === et_shortcodes_tab_auto[1] ? false : true;
		et_shortcodes_tab_settings.autoSpeed = et_shortcodes_tab_autospeed[1];

		if ( 'simple' === et_shortcodes_tab_type[1] ){
			et_shortcodes_tab_settings = {};
			et_shortcodes_tab_settings.fx = et_shortcodes_tab_fx[1];
			et_shortcodes_tab_settings.auto = 'false' === et_shortcodes_tab_auto[1] ? false : true;
			et_shortcodes_tab_settings.autoSpeed = et_shortcodes_tab_autospeed[1];
			et_shortcodes_tab_settings.sliderType = 'simple';
			et_shortcodes_tab_apply_to_element = '.et-simple-slides';
		} else if ( 'images' === et_shortcodes_tab_type[1] ){
			et_shortcodes_tab_settings.sliderType = 'images';
			et_shortcodes_tab_settings.linksNav = '#' + $(this).attr('id') + ' .controllers a.switch';
			et_shortcodes_tab_settings.findParent = false;
			et_shortcodes_tab_settings.lengthElement = '#' + $(this).attr('id') + ' a.switch';
			et_shortcodes_tab_apply_to_element = '.et-image-slides';
		}

		$(this).find(et_shortcodes_tab_apply_to_element).et_shortcodes_switcher( et_shortcodes_tab_settings );
	});
});