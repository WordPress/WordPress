(function($){
	var $et_window = $(window);

	window.et_load_event_fired   = false;
	window.et_is_transparent_nav = $( 'body' ).hasClass( 'et_transparent_nav' );
	window.et_is_vertical_nav    = $( 'body' ).hasClass( 'et_vertical_nav' );
	window.et_is_fixed_nav       = $( 'body' ).hasClass( 'et_fixed_nav' );

	jQuery.fn.reverse = [].reverse;

	jQuery.fn.closest_descendent = function( selector ) {
		var $found,
			$current_children = this.children();

		while ( $current_children.length ) {
			$found = $current_children.filter( selector );
			if ( $found.length ) {
				break;
			}
			$current_children = $current_children.children();
		}

		return $found;
	};

	window.et_pb_init_modules = function() {
		$.et_pb_simple_slider = function(el, options) {
			var settings = $.extend( {
				slide         			: '.et-slide',				 	// slide class
				arrows					: '.et-pb-slider-arrows',		// arrows container class
				prev_arrow				: '.et-pb-arrow-prev',			// left arrow class
				next_arrow				: '.et-pb-arrow-next',			// right arrow class
				controls 				: '.et-pb-controllers a',		// control selector
				carousel_controls 		: '.et_pb_carousel_item',		// carousel control selector
				control_active_class	: 'et-pb-active-control',		// active control class name
				previous_text			: et_pb_custom.previous,			// previous arrow text
				next_text				: et_pb_custom.next,				// next arrow text
				fade_speed				: 500,							// fade effect speed
				use_arrows				: true,							// use arrows?
				use_controls			: true,							// use controls?
				manual_arrows			: '',							// html code for custom arrows
				append_controls_to		: '',							// controls are appended to the slider element by default, here you can specify the element it should append to
				controls_below			: false,
				controls_class			: 'et-pb-controllers',				// controls container class name
				slideshow				: false,						// automattic animation?
				slideshow_speed			: 7000,							// automattic animation speed
				show_progress_bar		: false,							// show progress bar if automattic animation is active
				tabs_animation			: false,
				use_carousel			: false
			}, options );

			var $et_slider 			= $(el),
				$et_slide			= $et_slider.closest_descendent( settings.slide ),
				et_slides_number	= $et_slide.length,
				et_fade_speed		= settings.fade_speed,
				et_active_slide		= 0,
				$et_slider_arrows,
				$et_slider_prev,
				$et_slider_next,
				$et_slider_controls,
				$et_slider_carousel_controls,
				et_slider_timer,
				controls_html = '',
				carousel_html = '',
				$progress_bar = null,
				progress_timer_count = 0,
				$et_pb_container = $et_slider.find( '.et_pb_container' ),
				et_pb_container_width = $et_pb_container.width(),
				is_post_slider = $et_slider.hasClass( 'et_pb_post_slider' );

				$et_slider.et_animation_running = false;

				$.data(el, "et_pb_simple_slider", $et_slider);

				$et_slide.eq(0).addClass( 'et-pb-active-slide' );

				if ( ! settings.tabs_animation ) {
					if ( !$et_slider.hasClass('et_pb_bg_layout_dark') && !$et_slider.hasClass('et_pb_bg_layout_light') ) {
						$et_slider.addClass( et_get_bg_layout_color( $et_slide.eq(0) ) );
					}
				}

				if ( settings.use_arrows && et_slides_number > 1 ) {
					if ( settings.manual_arrows == '' )
						$et_slider.append( '<div class="et-pb-slider-arrows"><a class="et-pb-arrow-prev" href="#">' + '<span>' +settings.previous_text + '</span>' + '</a><a class="et-pb-arrow-next" href="#">' + '<span>' + settings.next_text + '</span>' + '</a></div>' );
					else
						$et_slider.append( settings.manual_arrows );

					$et_slider_arrows 	= $et_slider.find( settings.arrows );
					$et_slider_prev 	= $et_slider.find( settings.prev_arrow );
					$et_slider_next 	= $et_slider.find( settings.next_arrow );

					$et_slider.on( 'click.et_pb_simple_slider', settings.next_arrow, function() {
						if ( $et_slider.et_animation_running )	return false;

						$et_slider.et_slider_move_to( 'next' );

						return false;
					} );

					$et_slider.on( 'click.et_pb_simple_slider', settings.prev_arrow, function() {
						if ( $et_slider.et_animation_running )	return false;

						$et_slider.et_slider_move_to( 'previous' );

						return false;
					} );

					// swipe support requires et-jquery-touch-mobile
					$et_slider.on( 'swipeleft.et_pb_simple_slider', settings.slide, function( event ) {
						// do not switch slide on selecting text in VB
						if ( $( event.target ).closest( '.et-fb-popover-tinymce' ).length || $( event.target ).closest( '.et-fb-editable-element' ).length ) {
							return;
						}

						$et_slider.et_slider_move_to( 'next' );
					});
					$et_slider.on( 'swiperight.et_pb_simple_slider', settings.slide, function( event ) {
						// do not switch slide on selecting text in VB
						if ( $( event.target ).closest( '.et-fb-popover-tinymce' ).length || $( event.target ).closest( '.et-fb-editable-element' ).length ) {
							return;
						}

						$et_slider.et_slider_move_to( 'previous' );
					});
				}

				if ( settings.use_controls && et_slides_number > 1 ) {
					for ( var i = 1; i <= et_slides_number; i++ ) {
						controls_html += '<a href="#"' + ( i == 1 ? ' class="' + settings.control_active_class + '"' : '' ) + '>' + i + '</a>';
					}

					if ($et_slider.find('video').length > 0) {
						settings.controls_class += ' et-pb-controllers-has-video-tag';
					}

					controls_html =
						'<div class="' + settings.controls_class + '">' +
							controls_html +
						'</div>';

					if ( settings.append_controls_to == '' )
						$et_slider.append( controls_html );
					else
						$( settings.append_controls_to ).append( controls_html );

					if ( settings.controls_below )
						$et_slider_controls	= $et_slider.parent().find( settings.controls );
					else
						$et_slider_controls	= $et_slider.find( settings.controls );

					et_maybe_set_controls_color( $et_slide.eq(0) );

					$et_slider.on( 'click.et_pb_simple_slider', settings.controls, function () {
						if ( $et_slider.et_animation_running )	return false;

						$et_slider.et_slider_move_to( $(this).index() );

						return false;
					} );
				}

				if ( settings.use_carousel && et_slides_number > 1 ) {
					for ( var i = 1; i <= et_slides_number; i++ ) {
						slide_id = i - 1;
						image_src = ( $et_slide.eq(slide_id).data('image') !== undefined ) ? 'url(' + $et_slide.eq(slide_id).data('image') + ')' : 'none';
						carousel_html += '<div class="et_pb_carousel_item ' + ( i == 1 ? settings.control_active_class : '' ) + '" data-slide-id="'+ slide_id +'">' +
							'<div class="et_pb_video_overlay" href="#" style="background-image: ' + image_src + ';">' +
								'<div class="et_pb_video_overlay_hover"><a href="#" class="et_pb_video_play"></a></div>' +
							'</div>' +
						'</div>';
					}

					carousel_html =
						'<div class="et_pb_carousel">' +
						'<div class="et_pb_carousel_items">' +
							carousel_html +
						'</div>' +
						'</div>';
					$et_slider.after( carousel_html );

					$et_slider_carousel_controls = $et_slider.siblings('.et_pb_carousel').find( settings.carousel_controls );
					$et_slider_carousel_controls.on( 'click.et_pb_simple_slider', function() {
						if ( $et_slider.et_animation_running )	return false;

						var $this = $(this);
						$et_slider.et_slider_move_to( $this.data('slide-id') );

						return false;
					} );
				}

				if ( settings.slideshow && et_slides_number > 1 ) {
					$et_slider.on( 'mouseenter.et_pb_simple_slider', function() {
						if ( $et_slider.hasClass( 'et_slider_auto_ignore_hover' ) ) {
							return;
						}

						$et_slider.addClass( 'et_slider_hovered' );

						if ( typeof et_slider_timer != 'undefined' ) {
							clearInterval( et_slider_timer );
						}
					}).on( 'mouseleave.et_pb_simple_slider', function() {
						if ( $et_slider.hasClass( 'et_slider_auto_ignore_hover' ) ) {
							return;
						}

						$et_slider.removeClass( 'et_slider_hovered' );

						et_slider_auto_rotate();
					} );
				}

				et_slider_auto_rotate();

				function et_slider_auto_rotate(){
					if ( settings.slideshow && et_slides_number > 1 && ! $et_slider.hasClass( 'et_slider_hovered' ) ) {
						et_slider_timer = setTimeout( function() {
							$et_slider.et_slider_move_to( 'next' );
						}, settings.slideshow_speed );
					}
				}

				$et_slider.et_slider_destroy = function() {
					// Clear existing timer / auto rotate
					if ( typeof et_slider_timer != 'undefined' ) {
						clearInterval( et_slider_timer );
					}

					// Deregister all own existing events
					$et_slider.off( '.et_pb_simple_slider' );

					// Removing existing style from slide(s)
					$et_slider.find('.et_pb_slide').css({
						'z-index': '',
						'display': '',
						'opacity': '',
					});

					// Removing existing classnames from slide(s)
					$et_slider.find('.et-pb-active-slide').removeClass('et-pb-active-slide');
					$et_slider.find('.et-pb-moved-slide').removeClass('et-pb-moved-slide');

					// Removing DOM that was added by slider
					$et_slider.find('.et-pb-slider-arrows, .et-pb-controllers').remove();
					$et_slider.siblings('.et_pb_carousel').remove();

					// Remove references
					$et_slider.removeData( 'et_pb_simple_slider' );
				};

				function et_stop_video( active_slide ) {
					var $et_video, et_video_src;

					// if there is a video in the slide, stop it when switching to another slide
					if ( active_slide.has( 'iframe' ).length ) {
						$et_video = active_slide.find( 'iframe' );
						et_video_src = $et_video.attr( 'src' );

						$et_video.attr( 'src', '' );
						$et_video.attr( 'src', et_video_src );

					} else if ( active_slide.has( 'video' ).length ) {
						if ( !active_slide.find('.et_pb_section_video_bg').length ) {
							$et_video = active_slide.find( 'video' );
							$et_video[0].pause();
						}
					}
				}

				$et_slider.et_fix_slider_content_images = et_fix_slider_content_images;

				function et_fix_slider_content_images() {
					var $this_slider                 = $et_slider,
						$slide_image_container       = $this_slider.find( '.et-pb-active-slide .et_pb_slide_image' ),
						$slide_video_container       = $this_slider.find( '.et-pb-active-slide .et_pb_slide_video' ),
						$slide                       = $slide_image_container.closest( '.et_pb_slide' ),
						$slider                      = $slide.closest( '.et_pb_slider' ),
						slide_height                 = parseFloat( $slider.innerHeight() ),
						image_height                 = parseFloat( slide_height * 0.8 ),
						slide_image_container_height = parseFloat( $slide_image_container.height() ),
						slide_video_container_height = parseFloat( $slide_video_container.height() );

					if ( ! isNaN( image_height ) ) {
						$slide_image_container.find( 'img' ).css( 'maxHeight', image_height + 'px' );

						slide_image_container_height = parseInt( $slide_image_container.height() )
					}

					if ( ! isNaN( slide_image_container_height ) && $slide.hasClass( 'et_pb_media_alignment_center' ) ) {
						$slide_image_container.css( 'marginTop', '-' + ( slide_image_container_height / 2 ) + 'px' );
					}

					if ( ! isNaN( slide_video_container_height ) ) {
						$slide_video_container.css( 'marginTop', '-' + ( slide_video_container_height / 2 ) + 'px' );
					}
				}

				function et_get_bg_layout_color( $slide ) {
					if ( $slide.hasClass( 'et_pb_bg_layout_light' ) ) {
						return 'et_pb_bg_layout_light';
					}

					return 'et_pb_bg_layout_dark';
				}

				function et_maybe_set_controls_color( $slide ) {
					var next_slide_dot_color,
						$arrows,
						arrows_color;

					if ( typeof $et_slider_controls !== 'undefined' && $et_slider_controls.length ) {
						next_slide_dot_color = $slide.attr( 'data-dots_color' ) || '';

						if ( next_slide_dot_color !== '' ) {
							$et_slider_controls.attr( 'style', 'background-color: ' + hex_to_rgba( next_slide_dot_color, '0.3' ) + ';' );
							$et_slider_controls.filter( '.et-pb-active-control' ).attr( 'style', 'background-color: ' + hex_to_rgba( next_slide_dot_color ) + '!important;' );
						} else {
							$et_slider_controls.removeAttr( 'style' );
						}
					}

					if ( typeof $et_slider_arrows !== 'undefined' && $et_slider_arrows.length ) {
						$arrows      = $et_slider_arrows.find( 'a' );
						arrows_color = $slide.attr( 'data-arrows_color' ) || '';

						if ( arrows_color !== '' ) {
							$arrows.css( 'color', arrows_color );
						} else {
							$arrows.css( 'color', 'inherit' );
						}
					}
				}

				// fix the appearance of some modules inside the post slider
				function et_fix_builder_content() {
					if ( is_post_slider ) {
						setTimeout( function() {
							var $et_pb_circle_counter = $( '.et_pb_circle_counter' ),
								$et_pb_number_counter = $( '.et_pb_number_counter' );

							window.et_fix_testimonial_inner_width();

							if ( $et_pb_circle_counter.length ) {
								window.et_pb_reinit_circle_counters( $et_pb_circle_counter );
							}

							if ( $et_pb_number_counter.length ) {
								window.et_pb_reinit_number_counters( $et_pb_number_counter );
							}
							window.et_reinit_waypoint_modules();
						}, 1000 );
					}
				}

				function hex_to_rgba( color, alpha ) {
					var color_16 = parseInt( color.replace( '#', '' ), 16 ),
						red      = ( color_16 >> 16 ) & 255,
						green    = ( color_16 >> 8 ) & 255,
						blue     = color_16 & 255,
						alpha    = alpha || 1,
						rgba;

					rgba = red + ',' + green + ',' + blue + ',' + alpha;
					rgba = 'rgba(' + rgba + ')';

					return rgba;
				}

				if ( window.et_load_event_fired ) {
					et_fix_slider_height( $et_slider );
				} else {
					$et_window.on( 'load', function() {
						et_fix_slider_height( $et_slider );
					} );
				}

				$et_window.on( 'resize.et_simple_slider', function() {
					et_fix_slider_height( $et_slider );
				} );

				$et_slider.et_slider_move_to = function ( direction ) {
					var $active_slide = $et_slide.eq( et_active_slide ),
						$next_slide;

					$et_slider.et_animation_running = true;

					$et_slider.removeClass('et_slide_transition_to_next et_slide_transition_to_previous').addClass('et_slide_transition_to_' + direction );

					$et_slider.find('.et-pb-moved-slide').removeClass('et-pb-moved-slide');

					if ( direction == 'next' || direction == 'previous' ){

						if ( direction == 'next' )
							et_active_slide = ( et_active_slide + 1 ) < et_slides_number ? et_active_slide + 1 : 0;
						else
							et_active_slide = ( et_active_slide - 1 ) >= 0 ? et_active_slide - 1 : et_slides_number - 1;

					} else {

						if ( et_active_slide == direction ) {
							$et_slider.et_animation_running = false;
							return;
						}

						et_active_slide = direction;

					}

					if ( typeof et_slider_timer != 'undefined' )
						clearInterval( et_slider_timer );

					$next_slide	= $et_slide.eq( et_active_slide );

					if ( typeof $active_slide.find('video')[0] !== 'undefined' && typeof $active_slide.find('video')[0]['player'] !== 'undefined' ) {
						$active_slide.find('video')[0].player.pause();
					}

					if ( typeof $next_slide.find('video')[0] !== 'undefined' && typeof $next_slide.find('video')[0]['player'] !== 'undefined' ) {
						$next_slide.find('video')[0].player.play();
					}

					var $active_slide_video = $active_slide.find('.et_pb_video_box iframe');

					if ( $active_slide_video.length ) {
						var active_slide_video_src = $active_slide_video.attr('src');

						// Removes the "autoplay=1" parameter when switching slides
						// by covering three possible cases:

						// "?autoplay=1" at the end of the URL
						active_slide_video_src = active_slide_video_src.replace(/\?autoplay=1$/, '');

						// "?autoplay=1" followed by another parameter
						active_slide_video_src = active_slide_video_src.replace(/\?autoplay=1&(amp;)?/, '?');

						// "&autoplay=1" anywhere in the URL
						active_slide_video_src = active_slide_video_src.replace(/&(amp;)?autoplay=1/, '');

						// Delays the URL update so that the cross-fade animation's smoothness is not affected
						setTimeout(function() {
							$active_slide_video.attr({
								'src': active_slide_video_src
							});
						}, settings.fade_speed);

						// Restores video overlay
						$active_slide_video.parents('.et_pb_video_box').next('.et_pb_video_overlay').css({
							'display' : 'block',
							'opacity' : 1
						});
					}

					$et_slider.trigger( 'simple_slider_before_move_to', { direction : direction, next_slide : $next_slide });

					$et_slide.each( function(){
						$(this).css( 'zIndex', 1 );
					} );
					// add 'slide-status' data attribute so it can be used to determine active slide in Visual Builder
					$active_slide.css( 'zIndex', 2 ).removeClass( 'et-pb-active-slide' ).addClass('et-pb-moved-slide').data('slide-status', 'inactive');
					$next_slide.css( { 'display' : 'block', opacity : 0 } ).addClass( 'et-pb-active-slide' ).data('slide-status', 'active');

					et_fix_slider_content_images();

					et_fix_builder_content();

					if ( settings.use_controls )
						$et_slider_controls.removeClass( settings.control_active_class ).eq( et_active_slide ).addClass( settings.control_active_class );

					if ( settings.use_carousel )
						$et_slider_carousel_controls.removeClass( settings.control_active_class ).eq( et_active_slide ).addClass( settings.control_active_class );

					if ( ! settings.tabs_animation ) {
						et_maybe_set_controls_color( $next_slide );

						$next_slide.animate( { opacity : 1 }, et_fade_speed );
						$active_slide.addClass( 'et_slide_transition' ).css( { 'display' : 'list-item', 'opacity' : 1 } ).animate( { opacity : 0 }, et_fade_speed, function(){
							var active_slide_layout_bg_color = et_get_bg_layout_color( $active_slide ),
								next_slide_layout_bg_color = et_get_bg_layout_color( $next_slide );

							$(this).css('display', 'none').removeClass( 'et_slide_transition' );

							et_stop_video( $active_slide );

							$et_slider
								.removeClass( active_slide_layout_bg_color )
								.addClass( next_slide_layout_bg_color );

							$et_slider.et_animation_running = false;

							$et_slider.trigger( 'simple_slider_after_move_to', { next_slide : $next_slide } );
						} );
					} else {
						$next_slide.css( { 'display' : 'none', opacity : 0 } );

						$active_slide.addClass( 'et_slide_transition' ).css( { 'display' : 'block', 'opacity' : 1 } ).animate( { opacity : 0 }, et_fade_speed, function(){
							$(this).css('display', 'none').removeClass( 'et_slide_transition' );

							$next_slide.css( { 'display' : 'block', 'opacity' : 0 } ).animate( { opacity : 1 }, et_fade_speed, function() {
								$et_slider.et_animation_running = false;

								$et_slider.trigger( 'simple_slider_after_move_to', { next_slide : $next_slide } );
							} );
						} );
					}

					if ( $next_slide.find( '.et_parallax_bg' ).length ) {
						// reinit parallax on slide change to make sure it displayed correctly
						window.et_pb_parallax_init( $next_slide.find( '.et_parallax_bg' ) );
					}

					et_slider_auto_rotate();
				}
		};

		$.fn.et_pb_simple_slider = function( options ) {
			return this.each(function() {
				var slider = $.data( this, 'et_pb_simple_slider' );
				return slider ? slider : new $.et_pb_simple_slider( this, options );
			});
		};

		var et_hash_module_seperator = '||',
			et_hash_module_param_seperator = '|';

		function process_et_hashchange( hash ) {
			if ( ( hash.indexOf( et_hash_module_seperator, 0 ) ) !== -1 ) {
				modules = hash.split( et_hash_module_seperator );
				for ( var i = 0; i < modules.length; i++ ) {
					var module_params = modules[i].split( et_hash_module_param_seperator );
					var element = module_params[0];
					module_params.shift();
					if ( $('#' + element ).length ) {
						$('#' + element ).trigger({
							type: "et_hashchange",
							params: module_params
						});
					}
				}
			} else {
				module_params = hash.split( et_hash_module_param_seperator );
				var element = module_params[0];
				module_params.shift();
				if ( $('#' + element ).length ) {
					$('#' + element ).trigger({
						type: "et_hashchange",
						params: module_params
					});
				}
			}
		}

		function et_set_hash( module_state_hash ) {
			module_id = module_state_hash.split( et_hash_module_param_seperator )[0];
			if ( !$('#' + module_id ).length ) {
				return;
			}

			if ( window.location.hash ) {
				var hash = window.location.hash.substring(1), //Puts hash in variable, and removes the # character
					new_hash = [];

				if ( ( hash.indexOf( et_hash_module_seperator, 0 ) ) !== -1 ) {
					modules = hash.split( et_hash_module_seperator );
					var in_hash = false;
					for ( var i = 0; i < modules.length; i++ ) {
						var element = modules[i].split( et_hash_module_param_seperator )[0];
						if ( element === module_id ) {
							new_hash.push( module_state_hash );
							in_hash = true;
						} else {
							new_hash.push( modules[i] );
						}
					}
					if ( !in_hash ) {
						new_hash.push( module_state_hash );
					}
				} else {
					module_params = hash.split( et_hash_module_param_seperator );
					var element = module_params[0];
					if ( element !== module_id ) {
						new_hash.push( hash );
					}
					new_hash.push( module_state_hash );
				}

				hash = new_hash.join( et_hash_module_seperator );
			} else {
				hash = module_state_hash;
			}

			var yScroll = document.body.scrollTop;
			window.location.hash = hash;
			document.body.scrollTop = yScroll;
		}

		$.et_pb_simple_carousel = function(el, options) {
			var settings = $.extend( {
				slide_duration	: 500,
			}, options );

			var $et_carousel 			= $(el),
				$carousel_items 		= $et_carousel.find('.et_pb_carousel_items'),
				$the_carousel_items 	= $carousel_items.find('.et_pb_carousel_item');

			$et_carousel.et_animation_running = false;

			$et_carousel.addClass('container-width-change-notify').on('containerWidthChanged', function( event ){
				set_carousel_columns( $et_carousel );
				set_carousel_height( $et_carousel );
			});

			$carousel_items.data('items', $the_carousel_items.toArray() );
			$et_carousel.data('columns_setting_up', false );

			$carousel_items.prepend('<div class="et-pb-slider-arrows"><a class="et-pb-slider-arrow et-pb-arrow-prev" href="#">' + '<span>' + et_pb_custom.previous + '</span>' + '</a><a class="et-pb-slider-arrow et-pb-arrow-next" href="#">' + '<span>' + et_pb_custom.next + '</span>' + '</a></div>');

			set_carousel_columns( $et_carousel );
			set_carousel_height( $et_carousel );

			$et_carousel_next 	= $et_carousel.find( '.et-pb-arrow-next' );
			$et_carousel_prev 	= $et_carousel.find( '.et-pb-arrow-prev'  );

			$et_carousel.on( 'click', '.et-pb-arrow-next', function(){
				if ( $et_carousel.et_animation_running ) return false;

				$et_carousel.et_carousel_move_to( 'next' );

				return false;
			} );

			$et_carousel.on( 'click', '.et-pb-arrow-prev', function(){
				if ( $et_carousel.et_animation_running ) return false;

				$et_carousel.et_carousel_move_to( 'previous' );

				return false;
			} );

			// swipe support requires et-jquery-touch-mobile
			$et_carousel.on( 'swipeleft', function() {
				$et_carousel.et_carousel_move_to( 'next' );
			});
			$et_carousel.on( 'swiperight', function() {
				$et_carousel.et_carousel_move_to( 'previous' );
			});

			function set_carousel_height( $the_carousel ) {
				var carousel_items_width = $the_carousel_items.width(),
					carousel_items_height = $the_carousel_items.height();

				$carousel_items.css('height', carousel_items_height + 'px' );
			}

			function set_carousel_columns( $the_carousel ) {
				var columns,
					$carousel_parent = $the_carousel.parents('.et_pb_column'),
					carousel_items_width = $carousel_items.width(),
					carousel_item_count = $the_carousel_items.length;

				if ( $carousel_parent.hasClass('et_pb_column_4_4') || $carousel_parent.hasClass('et_pb_column_3_4') || $carousel_parent.hasClass('et_pb_column_2_3') ) {
					if ( $et_window.width() < 768 ) {
						columns = 3;
					} else {
						columns = 4;
					}
				} else if ( $carousel_parent.hasClass('et_pb_column_1_2') || $carousel_parent.hasClass('et_pb_column_3_8') || $carousel_parent.hasClass('et_pb_column_1_3') ) {
					columns = 3;
				} else if ( $carousel_parent.hasClass('et_pb_column_1_4') ) {
					if ( $et_window.width() > 480 && $et_window.width() < 980 ) {
						columns = 3;
					} else {
						columns = 2;
					}
				}

				if ( columns === $carousel_items.data('portfolio-columns') ) {
					return;
				}

				if ( $the_carousel.data('columns_setting_up') ) {
					return;
				}

				$the_carousel.data('columns_setting_up', true );

				// store last setup column
				$carousel_items.removeClass('columns-' + $carousel_items.data('portfolio-columns') );
				$carousel_items.addClass('columns-' + columns );
				$carousel_items.data('portfolio-columns', columns );

				// kill all previous groups to get ready to re-group
				if ( $carousel_items.find('.et-carousel-group').length ) {
					$the_carousel_items.appendTo( $carousel_items );
					$carousel_items.find('.et-carousel-group').remove();
				}

				// setup the grouping
				var the_carousel_items = $carousel_items.data('items'),
					$carousel_group = $('<div class="et-carousel-group active">').appendTo( $carousel_items );

				$the_carousel_items.data('position', '');
				if ( the_carousel_items.length <= columns ) {
					$carousel_items.find('.et-pb-slider-arrows').hide();
				} else {
					$carousel_items.find('.et-pb-slider-arrows').show();
				}

				for ( position = 1, x=0 ;x < the_carousel_items.length; x++, position++ ) {
					if ( x < columns ) {
						$( the_carousel_items[x] ).show();
						$( the_carousel_items[x] ).appendTo( $carousel_group );
						$( the_carousel_items[x] ).data('position', position );
						$( the_carousel_items[x] ).addClass('position_' + position );
					} else {
						position = $( the_carousel_items[x] ).data('position');
						$( the_carousel_items[x] ).removeClass('position_' + position );
						$( the_carousel_items[x] ).data('position', '' );
						$( the_carousel_items[x] ).hide();
					}
				}

				$the_carousel.data('columns_setting_up', false );

			} /* end set_carousel_columns() */

			$et_carousel.et_carousel_move_to = function ( direction ) {
				var $active_carousel_group 	= $carousel_items.find('.et-carousel-group.active'),
					items 					= $carousel_items.data('items'),
					columns 				= $carousel_items.data('portfolio-columns');

				$et_carousel.et_animation_running = true;

				var left = 0;
				$active_carousel_group.children().each(function(){
					$(this).css({'position':'absolute', 'left': left });
					left = left + $(this).outerWidth(true);
				});

				if ( direction == 'next' ) {
					var $next_carousel_group,
						current_position = 1,
						next_position = 1,
						active_items_start = items.indexOf( $active_carousel_group.children().first()[0] ),
						active_items_end = active_items_start + columns,
						next_items_start = active_items_end,
						next_items_end = next_items_start + columns;

					$next_carousel_group = $('<div class="et-carousel-group next" style="display: none;left: 100%;position: absolute;top: 0;">').insertAfter( $active_carousel_group );
					$next_carousel_group.css({ 'width': $active_carousel_group.innerWidth() }).show();

					// this is an endless loop, so it can decide internally when to break out, so that next_position
					// can get filled up, even to the extent of an element having both and current_ and next_ position
					for( x = 0, total = 0 ; ; x++, total++ ) {
						if ( total >= active_items_start && total < active_items_end ) {
							$( items[x] ).addClass( 'changing_position current_position current_position_' + current_position );
							$( items[x] ).data('current_position', current_position );
							current_position++;
						}

						if ( total >= next_items_start && total < next_items_end ) {
							$( items[x] ).data('next_position', next_position );
							$( items[x] ).addClass('changing_position next_position next_position_' + next_position );

							if ( !$( items[x] ).hasClass( 'current_position' ) ) {
								$( items[x] ).addClass('container_append');
							} else {
								$( items[x] ).clone(true).appendTo( $active_carousel_group ).hide().addClass('delayed_container_append_dup').attr('id', $( items[x] ).attr('id') + '-dup' );
								$( items[x] ).addClass('delayed_container_append');
							}

							next_position++;
						}

						if ( next_position > columns ) {
							break;
						}

						if ( x >= ( items.length -1 )) {
							x = -1;
						}
					}

					var sorted = $carousel_items.find('.container_append, .delayed_container_append_dup').sort(function (a, b) {
						var el_a_position = parseInt( $(a).data('next_position') );
						var el_b_position = parseInt( $(b).data('next_position') );
						return ( el_a_position < el_b_position ) ? -1 : ( el_a_position > el_b_position ) ? 1 : 0;
					});

					$( sorted ).show().appendTo( $next_carousel_group );

					var left = 0;
					$next_carousel_group.children().each(function(){
						$(this).css({'position':'absolute', 'left': left });
						left = left + $(this).outerWidth(true);
					});

					$active_carousel_group.animate({
						left: '-100%'
					}, {
						duration: settings.slide_duration,
						complete: function() {
							$carousel_items.find('.delayed_container_append').each(function(){
								left = $( '#' + $(this).attr('id') + '-dup' ).css('left');
								$(this).css({'position':'absolute', 'left': left });
								$(this).appendTo( $next_carousel_group );
							});

							$active_carousel_group.removeClass('active');
							$active_carousel_group.children().each(function(){
								position = $(this).data('position');
								current_position = $(this).data('current_position');
								$(this).removeClass('position_' + position + ' ' + 'changing_position current_position current_position_' + current_position );
								$(this).data('position', '');
								$(this).data('current_position', '');
								$(this).hide();
								$(this).css({'position': '', 'left': ''});
								$(this).appendTo( $carousel_items );
							});

							$active_carousel_group.remove();

						}
					} );

					next_left = $active_carousel_group.width() + parseInt( $the_carousel_items.first().css('marginRight').slice(0, -2) );
					$next_carousel_group.addClass('active').css({'position':'absolute', 'top':0, left: next_left });
					$next_carousel_group.animate({
						left: '0%'
					}, {
						duration: settings.slide_duration,
						complete: function(){
							$next_carousel_group.removeClass('next').addClass('active').css({'position':'', 'width':'', 'top':'', 'left': ''});

							$next_carousel_group.find('.changing_position').each(function( index ){
								position = $(this).data('position');
								current_position = $(this).data('current_position');
								next_position = $(this).data('next_position');
								$(this).removeClass('container_append delayed_container_append position_' + position + ' ' + 'changing_position current_position current_position_' + current_position + ' next_position next_position_' + next_position );
								$(this).data('current_position', '');
								$(this).data('next_position', '');
								$(this).data('position', ( index + 1 ) );
							});

							$next_carousel_group.children().css({'position': '', 'left': ''});
							$next_carousel_group.find('.delayed_container_append_dup').remove();

							$et_carousel.et_animation_running = false;
						}
					} );

				} else if ( direction == 'previous' ) {
					var $prev_carousel_group,
						current_position = columns,
						prev_position = columns,
						columns_span = columns - 1,
						active_items_start = items.indexOf( $active_carousel_group.children().last()[0] ),
						active_items_end = active_items_start - columns_span,
						prev_items_start = active_items_end - 1,
						prev_items_end = prev_items_start - columns_span;

					$prev_carousel_group = $('<div class="et-carousel-group prev" style="display: none;left: 100%;position: absolute;top: 0;">').insertBefore( $active_carousel_group );
					$prev_carousel_group.css({ 'left': '-' + $active_carousel_group.innerWidth(), 'width': $active_carousel_group.innerWidth() }).show();

					// this is an endless loop, so it can decide internally when to break out, so that next_position
					// can get filled up, even to the extent of an element having both and current_ and next_ position
					for( x = ( items.length - 1 ), total = ( items.length - 1 ) ; ; x--, total-- ) {

						if ( total <= active_items_start && total >= active_items_end ) {
							$( items[x] ).addClass( 'changing_position current_position current_position_' + current_position );
							$( items[x] ).data('current_position', current_position );
							current_position--;
						}

						if ( total <= prev_items_start && total >= prev_items_end ) {
							$( items[x] ).data('prev_position', prev_position );
							$( items[x] ).addClass('changing_position prev_position prev_position_' + prev_position );

							if ( !$( items[x] ).hasClass( 'current_position' ) ) {
								$( items[x] ).addClass('container_append');
							} else {
								$( items[x] ).clone(true).appendTo( $active_carousel_group ).addClass('delayed_container_append_dup').attr('id', $( items[x] ).attr('id') + '-dup' );
								$( items[x] ).addClass('delayed_container_append');
							}

							prev_position--;
						}

						if ( prev_position <= 0 ) {
							break;
						}

						if ( x == 0 ) {
							x = items.length;
						}
					}

					var sorted = $carousel_items.find('.container_append, .delayed_container_append_dup').sort(function (a, b) {
						var el_a_position = parseInt( $(a).data('prev_position') );
						var el_b_position = parseInt( $(b).data('prev_position') );
						return ( el_a_position < el_b_position ) ? -1 : ( el_a_position > el_b_position ) ? 1 : 0;
					});

					$( sorted ).show().appendTo( $prev_carousel_group );

					var left = 0;
					$prev_carousel_group.children().each(function(){
						$(this).css({'position':'absolute', 'left': left });
						left = left + $(this).outerWidth(true);
					});

					$active_carousel_group.animate({
						left: '100%'
					}, {
						duration: settings.slide_duration,
						complete: function() {
							$carousel_items.find('.delayed_container_append').reverse().each(function(){
								left = $( '#' + $(this).attr('id') + '-dup' ).css('left');
								$(this).css({'position':'absolute', 'left': left });
								$(this).prependTo( $prev_carousel_group );
							});

							$active_carousel_group.removeClass('active');
							$active_carousel_group.children().each(function(){
								position = $(this).data('position');
								current_position = $(this).data('current_position');
								$(this).removeClass('position_' + position + ' ' + 'changing_position current_position current_position_' + current_position );
								$(this).data('position', '');
								$(this).data('current_position', '');
								$(this).hide();
								$(this).css({'position': '', 'left': ''});
								$(this).appendTo( $carousel_items );
							});

							$active_carousel_group.remove();
						}
					} );

					prev_left = (-1) * $active_carousel_group.width() - parseInt( $the_carousel_items.first().css('marginRight').slice(0, -2) );
					$prev_carousel_group.addClass('active').css({'position':'absolute', 'top':0, left: prev_left });
					$prev_carousel_group.animate({
						left: '0%'
					}, {
						duration: settings.slide_duration,
						complete: function(){
							$prev_carousel_group.removeClass('prev').addClass('active').css({'position':'', 'width':'', 'top':'', 'left': ''});

							$prev_carousel_group.find('.delayed_container_append_dup').remove();

							$prev_carousel_group.find('.changing_position').each(function( index ){
								position = $(this).data('position');
								current_position = $(this).data('current_position');
								prev_position = $(this).data('prev_position');
								$(this).removeClass('container_append delayed_container_append position_' + position + ' ' + 'changing_position current_position current_position_' + current_position + ' prev_position prev_position_' + prev_position );
								$(this).data('current_position', '');
								$(this).data('prev_position', '');
								position = index + 1;
								$(this).data('position', position );
								$(this).addClass('position_' + position );
							});

							$prev_carousel_group.children().css({'position': '', 'left': ''});
							$et_carousel.et_animation_running = false;
						}
					} );
				}
			}
		};

		$.fn.et_pb_simple_carousel = function( options ) {
			return this.each(function() {
				var carousel = $.data( this, 'et_pb_simple_carousel' );
				return carousel ? carousel : new $.et_pb_simple_carousel( this, options );
			});
		};

		$(document).ready( function(){
			/**
			 * Provide event listener for plugins to hook up to
			 */
			$(window).trigger('et_pb_before_init_modules');

			var $et_pb_slider  = $( '.et_pb_slider' ),
				$et_pb_tabs    = $( '.et_pb_tabs' ),
				$et_pb_video_section = $('.et_pb_section_video_bg'),
				$et_pb_newsletter_button = $( '.et_pb_newsletter_button' ),
				$et_pb_filterable_portfolio = $( '.et_pb_filterable_portfolio' ),
				$et_pb_fullwidth_portfolio = $( '.et_pb_fullwidth_portfolio' ),
				$et_pb_gallery = $( '.et_pb_gallery' ),
				$et_pb_countdown_timer = $( '.et_pb_countdown_timer' ),
				$et_post_gallery = $( '.et_post_gallery' ),
				$et_lightbox_image = $( '.et_pb_lightbox_image'),
				$et_pb_map    = $( '.et_pb_map_container' ),
				$et_pb_circle_counter = $( '.et_pb_circle_counter' ),
				$et_pb_number_counter = $( '.et_pb_number_counter' ),
				$et_pb_parallax = $( '.et_parallax_bg' ),
				$et_pb_shop = $( '.et_pb_shop' ),
				$et_pb_post_fullwidth = $( '.single.et_pb_pagebuilder_layout.et_full_width_page' ),
				et_is_mobile_device = navigator.userAgent.match( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/ ) !== null,
				et_is_ipad = navigator.userAgent.match( /iPad/ ),
				et_is_ie9 = navigator.userAgent.match( /MSIE 9.0/ ) !== null,
				$et_container = ! et_pb_custom.is_builder_plugin_used ? $( '.container' ) : $( '.et_pb_row' ),
				et_container_width = $et_container.width(),
				et_is_vertical_fixed_nav = $( 'body' ).hasClass( 'et_vertical_fixed' ),
				et_is_rtl = $( 'body' ).hasClass( 'rtl' ),
				et_hide_nav = $( 'body' ).hasClass( 'et_hide_nav' ),
				et_header_style_left = $( 'body' ).hasClass( 'et_header_style_left' ),
				$top_header = $('#top-header'),
				$main_header = $('#main-header'),
				$main_container_wrapper = $( '#page-container' ),
				$et_transparent_nav = $( '.et_transparent_nav' ),
				$et_pb_first_row = $( 'body.et_pb_pagebuilder_layout .et_pb_section:first-child' ),
				$et_main_content_first_row = $( '#main-content .container:first-child' ),
				$et_main_content_first_row_meta_wrapper = $et_main_content_first_row.find('.et_post_meta_wrapper:first'),
				$et_main_content_first_row_meta_wrapper_title = $et_main_content_first_row_meta_wrapper.find( 'h1' ),
				$et_main_content_first_row_content = $et_main_content_first_row.find('.entry-content:first'),
				$et_single_post = $( 'body.single-post' ),
				etRecalculateOffset = false,
				et_header_height,
				et_header_modifier,
				et_header_offset,
				et_primary_header_top,
				$et_header_style_split = $('.et_header_style_split'),
				$et_top_navigation = $('#et-top-navigation'),
				$logo = $('#logo'),
				$et_sticky_image = $('.et_pb_image_sticky'),
				$et_pb_counter_amount = $('.et_pb_counter_amount'),
				$et_pb_carousel = $( '.et_pb_carousel' ),
				$et_menu_selector = et_pb_custom.is_divi_theme_used ? $( 'ul.nav' ) : $( '.et_pb_fullwidth_menu ul.nav' ),
				et_pb_ab_bounce_rate = et_pb_custom.ab_bounce_rate * 1000,
				et_pb_ab_logged_status = {
					read_page: false,
					read_goal: false,
					view_goal: false,
					click_goal: false,
					con_goal: false,
					con_short: false,
				};

			window.et_pb_slider_init = function( $this_slider ) {
				var et_slider_settings = {
						fade_speed 		: 700,
						slide			: ! $this_slider.hasClass( 'et_pb_gallery' ) ? '.et_pb_slide' : '.et_pb_gallery_item'
					};

				if ( $this_slider.hasClass('et_pb_slider_no_arrows') )
					et_slider_settings.use_arrows = false;

				if ( $this_slider.hasClass('et_pb_slider_no_pagination') )
					et_slider_settings.use_controls = false;

				if ( $this_slider.hasClass('et_slider_auto') ) {
					var et_slider_autospeed_class_value = /et_slider_speed_(\d+)/g;

					et_slider_settings.slideshow = true;

					var et_slider_autospeed = et_slider_autospeed_class_value.exec( $this_slider.attr('class') );

					et_slider_settings.slideshow_speed = et_slider_autospeed === null ? 10 : et_slider_autospeed[1];
				}

				if ( $this_slider.parent().hasClass('et_pb_video_slider') ) {
					et_slider_settings.controls_below = true;
					et_slider_settings.append_controls_to = $this_slider.parent();

					setTimeout( function() {
						$( '.et_pb_preload' ).removeClass( 'et_pb_preload' );
					}, 500 );
				}

				if ( $this_slider.hasClass('et_pb_slider_carousel') )
					et_slider_settings.use_carousel = true;

				$this_slider.et_pb_simple_slider( et_slider_settings );
			}

			var $et_top_menu = $et_menu_selector,
				et_parent_menu_longpress_limit = 300,
				et_parent_menu_longpress_start,
				et_parent_menu_click = true,
				is_frontend_builder = $('body').hasClass('et-fb'),
				et_menu_hover_triggered = false;

			// log the conversion if visitor is on Thank You page and comes from the Shop module which is the Goal
			if ( $( '.et_pb_ab_shop_conversion' ).length && typeof et_pb_get_cookie_value( 'et_pb_ab_shop_log' ) !== 'undefined' && '' !== et_pb_get_cookie_value( 'et_pb_ab_shop_log' ) ) {
				var shop_log_data = et_pb_get_cookie_value( 'et_pb_ab_shop_log' ).split( '_' );
					page_id = shop_log_data[0],
					subject_id = shop_log_data[1],
					test_id = shop_log_data[2];

				et_pb_ab_update_stats( 'con_goal', page_id, subject_id, test_id );

				// remove the cookie after conversion is logged
				et_pb_set_cookie( 0, 'et_pb_ab_shop_log=true' );
			}

			// log the conversion if visitor is on page with tracking shortcode
			if ( $( '.et_pb_ab_split_track' ).length ) {
				$( '.et_pb_ab_split_track' ).each( function() {
					var tracking_test = $( this ).data( 'test_id' ),
						cookies_name = 'et_pb_ab_shortcode_track_' + tracking_test;

					if ( typeof et_pb_get_cookie_value( cookies_name ) !== 'undefined' && '' !== et_pb_get_cookie_value( cookies_name ) ) {
						var track_data = et_pb_get_cookie_value( cookies_name ).split( '_' );
							page_id = track_data[0],
							subject_id = track_data[1],
							test_id = track_data[2];

						et_pb_ab_update_stats( 'con_short', page_id, subject_id, test_id );

						// remove the cookie after conversion is logged
						et_pb_set_cookie( 0, cookies_name + '=true' );
					}
				});
			}

			// init split testing if enabled
			if ( et_pb_custom.is_ab_testing_active ) {
				et_pb_init_ab_test();
			}

			if ( $( '.et_pb_row' ).length ) {
				$( '.et_pb_row' ).each( function() {
					var $this_row = $( this ),
						row_class = '';

					row_class = et_get_column_types( $this_row.find( '>.et_pb_column' ) );

					if ( '' !== row_class && ( -1 !== row_class.indexOf( '1-4' ) || '_4col' === row_class ) ) {
						$this_row.addClass( 'et_pb_row' + row_class );
					}

					if ( $this_row.find( '.et_pb_row_inner' ).length ) {
						$this_row.find( '.et_pb_row_inner' ).each( function() {
							var $this_row_inner = $( this );
							row_class = et_get_column_types( $this_row_inner.find( '.et_pb_column' ) );

							if ( '' !== row_class && -1 !== row_class.indexOf( '1-4' ) ) {
								$this_row_inner.addClass( 'et_pb_row' + row_class );
							}
						});
					}
				});
			}

			function et_get_column_types( $columns ) {
				var row_class = '';

				if ( $columns.length ) {
					$columns.each( function() {
						var $this_column = $( this ),
							column_type = $this_column.attr( 'class' ).split( 'et_pb_column_' )[1],
							column_type_clean = typeof column_type !== 'undefined' ? column_type.split( ' ', 1 )[0] : '4_4',
							column_type_updated = column_type_clean.replace( '_', '-' ).trim();

						row_class += '_' + column_type_updated;
					});

					row_class = '_1-4_1-4_1-4_1-4' === row_class ? '_4col' : row_class;
				}

				return row_class;
			}

			$et_top_menu.find( 'li' ).hover( function() {
				if ( ! $(this).closest( 'li.mega-menu' ).length || $(this).hasClass( 'mega-menu' ) ) {
					$(this).addClass( 'et-show-dropdown' );
					$(this).removeClass( 'et-hover' ).addClass( 'et-hover' );
					et_menu_hover_triggered = true;
				}
			}, function() {
				var $this_el = $(this);

				$this_el.removeClass( 'et-show-dropdown' ).addClass( 'et-dropdown-removing' );

				et_menu_hover_triggered = false;

				setTimeout( function() {
					if ( ! $this_el.hasClass( 'et-show-dropdown' ) ) {
						$this_el.removeClass( 'et-hover' ).removeClass( 'et-dropdown-removing' );
					}
				}, 200 );
			} );

			// Dropdown menu adjustment for touch screen
			$et_top_menu.find('.menu-item-has-children > a').on( 'touchstart', function(){
				et_parent_menu_longpress_start = new Date().getTime();
			} ).on( 'touchend', function(){
				var et_parent_menu_longpress_end = new Date().getTime()
				if ( et_parent_menu_longpress_end  >= et_parent_menu_longpress_start + et_parent_menu_longpress_limit ) {
					et_parent_menu_click = true;
				} else {
					et_parent_menu_click = false;

					// Some devices emulate hover event on touch, so check that hover event was not triggered to avoid extra mouseleave event triggering
					if ( ! et_menu_hover_triggered ) {
						// Close sub-menu if toggled
						var $et_parent_menu = $(this).parent('li');
						if ( $et_parent_menu.hasClass( 'et-hover') ) {
							$et_parent_menu.trigger( 'mouseleave' );
						} else {
							$et_parent_menu.trigger( 'mouseenter' );
						}
					}
				}
				et_parent_menu_longpress_start = 0;
			} ).click(function() {
				if ( et_parent_menu_click ) {
					return true;
				}

				return false;
			} );

			$et_top_menu.find( 'li.mega-menu' ).each(function(){
				var $li_mega_menu           = $(this),
					$li_mega_menu_item      = $li_mega_menu.children( 'ul' ).children( 'li' ),
					li_mega_menu_item_count = $li_mega_menu_item.length;

				if ( li_mega_menu_item_count < 4 ) {
					$li_mega_menu.addClass( 'mega-menu-parent mega-menu-parent-' + li_mega_menu_item_count );
				}
			});

			$et_sticky_image.each( function() {
				var $this_el            = $(this),
					$row                = $this_el.closest('.et_pb_row'),
					$section            = $row.closest('.et_pb_section'),
					$column             = $this_el.closest( '.et_pb_column' ),
					sticky_class        = 'et_pb_section_sticky',
					sticky_mobile_class = 'et_pb_section_sticky_mobile';

				// If it is not in the last row, continue
				if ( ! $row.is( ':last-child' ) ) {
					return true;
				}

				// Make sure sticky image is the last element in the column
				if ( ! $this_el.is( ':last-child' ) ) {
					return true;
				}

				// If it is in the last row, find the parent section and attach new class to it
				if ( ! $section.hasClass( sticky_class ) ) {
					$section.addClass( sticky_class );
				}

				$column.addClass( 'et_pb_row_sticky' );

				if ( ! $section.hasClass( sticky_mobile_class ) && $column.is( ':last-child' ) ) {
					$section.addClass( sticky_mobile_class );
				}
			} );

			if ( et_is_mobile_device ) {
				$( '.et_pb_section_video_bg' ).each( function() {
					var $this_el = $(this);

					$this_el.closest( '.et_pb_preload' ).removeClass( 'et_pb_preload' )

					$this_el.remove();
				} );

				$( 'body' ).addClass( 'et_mobile_device' );

				if ( ! et_is_ipad ) {
					$( 'body' ).addClass( 'et_mobile_device_not_ipad' );
				}
			}

			if ( et_is_ie9 ) {
				$( 'body' ).addClass( 'et_ie9' );
			}

			if ( $et_pb_video_section.length || is_frontend_builder ) {
				window.et_pb_video_section_init = function( $et_pb_video_section ) {
					$et_pb_video_section.find( 'video' ).mediaelementplayer( {
						pauseOtherPlayers: false,
						success : function( mediaElement, domObject ) {
							mediaElement.addEventListener( 'loadeddata', function() {
								et_pb_resize_section_video_bg( $(domObject) );
								et_pb_center_video( $(domObject).closest( '.mejs-video' ) );
							}, false );

							mediaElement.addEventListener( 'canplay', function() {
								$(domObject).closest( '.et_pb_preload' ).removeClass( 'et_pb_preload' );
							}, false );
						}
					} );
				}
				et_pb_video_section_init( $et_pb_video_section );
			}

			if ( $et_post_gallery.length ) {
				// swipe support in magnific popup only if gallery exists
				var magnificPopup = $.magnificPopup.instance;

				$( 'body' ).on( 'swiperight', '.mfp-container', function() {
					magnificPopup.prev();
				} );
				$( 'body' ).on( 'swipeleft', '.mfp-container', function() {
					magnificPopup.next();
				} );

				$et_post_gallery.each(function() {
					$(this).magnificPopup( {
						delegate: '.et_pb_gallery_image a',
						type: 'image',
						removalDelay: 500,
						gallery: {
							enabled: true,
							navigateByImgClick: true
						},
						mainClass: 'mfp-fade',
						zoom: {
							enabled: ! et_pb_custom.is_builder_plugin_used,
							duration: 500,
							opener: function(element) {
								return element.find('img');
							}
						}
					} );
				} );
				// prevent attaching of any further actions on click
				$et_post_gallery.find( 'a' ).unbind( 'click' );
			}

			if ( $et_lightbox_image.length || is_frontend_builder ) {
				// prevent attaching of any further actions on click
				$et_lightbox_image.unbind( 'click' );
				$et_lightbox_image.bind( 'click' );

				window.et_pb_image_lightbox_init = function( $et_lightbox_image ) {
					$et_lightbox_image.magnificPopup( {
						type: 'image',
						removalDelay: 500,
						mainClass: 'mfp-fade',
						zoom: {
							enabled: ! et_pb_custom.is_builder_plugin_used,
							duration: 500,
							opener: function(element) {
								return element.find('img');
							}
						}
					} );
				}

				et_pb_image_lightbox_init( $et_lightbox_image );
			}

			if ( $et_pb_slider.length || is_frontend_builder ) {
				$et_pb_slider.each( function() {
					$this_slider = $(this);

					et_pb_slider_init( $this_slider );
				} );
			}

			$et_pb_carousel  = $( '.et_pb_carousel' );
			if ( $et_pb_carousel.length || is_frontend_builder ) {
				$et_pb_carousel.each( function() {
					var $this_carousel = $(this),
						et_carousel_settings = {
							slide_duration: 1000
						};

					$this_carousel.et_pb_simple_carousel( et_carousel_settings );
				} );
			}

			if ( $et_pb_fullwidth_portfolio.length || is_frontend_builder ) {

				window.et_fullwidth_portfolio_init = function( $the_portfolio ) {
					var $portfolio_items = $the_portfolio.find('.et_pb_portfolio_items');

						$portfolio_items.data('items', $portfolio_items.find('.et_pb_portfolio_item').toArray() );
						$the_portfolio.data('columns_setting_up', false );

					if ( $the_portfolio.hasClass('et_pb_fullwidth_portfolio_carousel') ) {
						// add left and right arrows
						$portfolio_items.prepend('<div class="et-pb-slider-arrows"><a class="et-pb-arrow-prev" href="#">' + '<span>' + et_pb_custom.previous + '</span>' + '</a><a class="et-pb-arrow-next" href="#">' + '<span>' + et_pb_custom.next + '</span>' + '</a></div>');

						set_fullwidth_portfolio_columns( $the_portfolio, true );

						et_carousel_auto_rotate( $the_portfolio );

						// swipe support
						$the_portfolio.on( 'swiperight', function() {
							$( this ).find( '.et-pb-arrow-prev' ).click();
						});
						$the_portfolio.on( 'swipeleft', function() {
							$( this ).find( '.et-pb-arrow-next' ).click();
						});

						$the_portfolio.hover(
							function(){
								$(this).addClass('et_carousel_hovered');
								if ( typeof $(this).data('et_carousel_timer') != 'undefined' ) {
									clearInterval( $(this).data('et_carousel_timer') );
								}
							},
							function(){
								$(this).removeClass('et_carousel_hovered');
								et_carousel_auto_rotate( $(this) );
							}
						);

						$the_portfolio.data('carouseling', false );

						$the_portfolio.on('click', '.et-pb-slider-arrows a', function(e){
							fullwidth_portfolio_carousel_slide( $( this) );
							e.preventDefault();
							return false;
						});

					} else {
						// setup fullwidth portfolio grid
						set_fullwidth_portfolio_columns( $the_portfolio, false );
					}
				}

				function fullwidth_portfolio_carousel_slide( $arrow ) {
					var $the_portfolio = $arrow.parents('.et_pb_fullwidth_portfolio'),
						$portfolio_items = $the_portfolio.find('.et_pb_portfolio_items'),
						$the_portfolio_items = $portfolio_items.find('.et_pb_portfolio_item'),
						$active_carousel_group = $portfolio_items.find('.et_pb_carousel_group.active'),
						slide_duration = 700,
						items = $portfolio_items.data('items'),
						columns = $portfolio_items.data('portfolio-columns'),
						item_width = $active_carousel_group.innerWidth() / columns, //$active_carousel_group.children().first().innerWidth(),
						original_item_width = ( 100 / columns ) + '%';

					if ( 'undefined' == typeof items ) {
						return;
					}

					if ( $the_portfolio.data('carouseling') ) {
						return;
					}

					$the_portfolio.data('carouseling', true);

					$active_carousel_group.children().each(function(){
						$(this).css({'width': $(this).innerWidth() + 1, 'position':'absolute', 'left': ( $(this).innerWidth() * ( $(this).data('position') - 1 ) ) });
					});

					if ( $arrow.hasClass('et-pb-arrow-next') ) {
						var $next_carousel_group,
							current_position = 1,
							next_position = 1,
							active_items_start = items.indexOf( $active_carousel_group.children().first()[0] ),
							active_items_end = active_items_start + columns,
							next_items_start = active_items_end,
							next_items_end = next_items_start + columns;

						$next_carousel_group = $('<div class="et_pb_carousel_group next" style="display: none;left: 100%;position: absolute;top: 0;">').insertAfter( $active_carousel_group );
						$next_carousel_group.css({ 'width': $active_carousel_group.innerWidth() }).show();

						// this is an endless loop, so it can decide internally when to break out, so that next_position
						// can get filled up, even to the extent of an element having both and current_ and next_ position
						for( x = 0, total = 0 ; ; x++, total++ ) {
							if ( total >= active_items_start && total < active_items_end ) {
								$( items[x] ).addClass( 'changing_position current_position current_position_' + current_position );
								$( items[x] ).data('current_position', current_position );
								current_position++;
							}

							if ( total >= next_items_start && total < next_items_end ) {
								$( items[x] ).data('next_position', next_position );
								$( items[x] ).addClass('changing_position next_position next_position_' + next_position );

								if ( !$( items[x] ).hasClass( 'current_position' ) ) {
									$( items[x] ).addClass('container_append');
								} else {
									$( items[x] ).clone(true).appendTo( $active_carousel_group ).hide().addClass('delayed_container_append_dup').attr('id', $( items[x] ).attr('id') + '-dup' );
									$( items[x] ).addClass('delayed_container_append');
								}

								next_position++;
							}

							if ( next_position > columns ) {
								break;
							}

							if ( x >= ( items.length -1 )) {
								x = -1;
							}
						}

						sorted = $portfolio_items.find('.container_append, .delayed_container_append_dup').sort(function (a, b) {
							var el_a_position = parseInt( $(a).data('next_position') );
							var el_b_position = parseInt( $(b).data('next_position') );
							return ( el_a_position < el_b_position ) ? -1 : ( el_a_position > el_b_position ) ? 1 : 0;
						});

						$( sorted ).show().appendTo( $next_carousel_group );

						$next_carousel_group.children().each(function(){
							$(this).css({'width': item_width, 'position':'absolute', 'left': ( item_width * ( $(this).data('next_position') - 1 ) ) });
						});

						$active_carousel_group.animate({
							left: '-100%'
						}, {
							duration: slide_duration,
							complete: function() {
								$portfolio_items.find('.delayed_container_append').each(function(){
									$(this).css({'width': item_width, 'position':'absolute', 'left': ( item_width * ( $(this).data('next_position') - 1 ) ) });
									$(this).appendTo( $next_carousel_group );
								});

								$active_carousel_group.removeClass('active');
								$active_carousel_group.children().each(function(){
									position = $(this).data('position');
									current_position = $(this).data('current_position');
									$(this).removeClass('position_' + position + ' ' + 'changing_position current_position current_position_' + current_position );
									$(this).data('position', '');
									$(this).data('current_position', '');
									$(this).hide();
									$(this).css({'position': '', 'width': '', 'left': ''});
									$(this).appendTo( $portfolio_items );
								});

								$active_carousel_group.remove();

								et_carousel_auto_rotate( $the_portfolio );

							}
						} );

						$next_carousel_group.addClass('active').css({'position':'absolute', 'top':0, left: '100%'});
						$next_carousel_group.animate({
							left: '0%'
						}, {
							duration: slide_duration,
							complete: function(){
								setTimeout(function(){
									$next_carousel_group.removeClass('next').addClass('active').css({'position':'', 'width':'', 'top':'', 'left': ''});

									$next_carousel_group.find('.delayed_container_append_dup').remove();

									$next_carousel_group.find('.changing_position').each(function( index ){
										position = $(this).data('position');
										current_position = $(this).data('current_position');
										next_position = $(this).data('next_position');
										$(this).removeClass('container_append delayed_container_append position_' + position + ' ' + 'changing_position current_position current_position_' + current_position + ' next_position next_position_' + next_position );
										$(this).data('current_position', '');
										$(this).data('next_position', '');
										$(this).data('position', ( index + 1 ) );
									});

									$next_carousel_group.children().css({'position': '', 'width': original_item_width, 'left': ''});

									$the_portfolio.data('carouseling', false);
								}, 100 );
							}
						} );

					} else {
						var $prev_carousel_group,
							current_position = columns,
							prev_position = columns,
							columns_span = columns - 1,
							active_items_start = items.indexOf( $active_carousel_group.children().last()[0] ),
							active_items_end = active_items_start - columns_span,
							prev_items_start = active_items_end - 1,
							prev_items_end = prev_items_start - columns_span;

						$prev_carousel_group = $('<div class="et_pb_carousel_group prev" style="display: none;left: 100%;position: absolute;top: 0;">').insertBefore( $active_carousel_group );
						$prev_carousel_group.css({ 'left': '-' + $active_carousel_group.innerWidth(), 'width': $active_carousel_group.innerWidth() }).show();

						// this is an endless loop, so it can decide internally when to break out, so that next_position
						// can get filled up, even to the extent of an element having both and current_ and next_ position
						for( x = ( items.length - 1 ), total = ( items.length - 1 ) ; ; x--, total-- ) {

							if ( total <= active_items_start && total >= active_items_end ) {
								$( items[x] ).addClass( 'changing_position current_position current_position_' + current_position );
								$( items[x] ).data('current_position', current_position );
								current_position--;
							}

							if ( total <= prev_items_start && total >= prev_items_end ) {
								$( items[x] ).data('prev_position', prev_position );
								$( items[x] ).addClass('changing_position prev_position prev_position_' + prev_position );

								if ( !$( items[x] ).hasClass( 'current_position' ) ) {
									$( items[x] ).addClass('container_append');
								} else {
									$( items[x] ).clone(true).appendTo( $active_carousel_group ).addClass('delayed_container_append_dup').attr('id', $( items[x] ).attr('id') + '-dup' );
									$( items[x] ).addClass('delayed_container_append');
								}

								prev_position--;
							}

							if ( prev_position <= 0 ) {
								break;
							}

							if ( x == 0 ) {
								x = items.length;
							}
						}

						sorted = $portfolio_items.find('.container_append, .delayed_container_append_dup').sort(function (a, b) {
							var el_a_position = parseInt( $(a).data('prev_position') );
							var el_b_position = parseInt( $(b).data('prev_position') );
							return ( el_a_position < el_b_position ) ? -1 : ( el_a_position > el_b_position ) ? 1 : 0;
						});

						$( sorted ).show().appendTo( $prev_carousel_group );

						$prev_carousel_group.children().each(function(){
							$(this).css({'width': item_width, 'position':'absolute', 'left': ( item_width * ( $(this).data('prev_position') - 1 ) ) });
						});

						$active_carousel_group.animate({
							left: '100%'
						}, {
							duration: slide_duration,
							complete: function() {
								$portfolio_items.find('.delayed_container_append').reverse().each(function(){
									$(this).css({'width': item_width, 'position':'absolute', 'left': ( item_width * ( $(this).data('prev_position') - 1 ) ) });
									$(this).prependTo( $prev_carousel_group );
								});

								$active_carousel_group.removeClass('active');
								$active_carousel_group.children().each(function(){
									position = $(this).data('position');
									current_position = $(this).data('current_position');
									$(this).removeClass('position_' + position + ' ' + 'changing_position current_position current_position_' + current_position );
									$(this).data('position', '');
									$(this).data('current_position', '');
									$(this).hide();
									$(this).css({'position': '', 'width': '', 'left': ''});
									$(this).appendTo( $portfolio_items );
								});

								$active_carousel_group.remove();
							}
						} );

						$prev_carousel_group.addClass('active').css({'position':'absolute', 'top':0, left: '-100%'});
						$prev_carousel_group.animate({
							left: '0%'
						}, {
							duration: slide_duration,
							complete: function(){
								setTimeout(function(){
									$prev_carousel_group.removeClass('prev').addClass('active').css({'position':'', 'width':'', 'top':'', 'left': ''});

									$prev_carousel_group.find('.delayed_container_append_dup').remove();

									$prev_carousel_group.find('.changing_position').each(function( index ){
										position = $(this).data('position');
										current_position = $(this).data('current_position');
										prev_position = $(this).data('prev_position');
										$(this).removeClass('container_append delayed_container_append position_' + position + ' ' + 'changing_position current_position current_position_' + current_position + ' prev_position prev_position_' + prev_position );
										$(this).data('current_position', '');
										$(this).data('prev_position', '');
										position = index + 1;
										$(this).data('position', position );
										$(this).addClass('position_' + position );
									});

									$prev_carousel_group.children().css({'position': '', 'width': original_item_width, 'left': ''});
									$the_portfolio.data('carouseling', false);
								}, 100 );
							}
						} );
					}
				}

				function set_fullwidth_portfolio_columns( $the_portfolio, carousel_mode ) {
					var columns,
						$portfolio_items = $the_portfolio.find('.et_pb_portfolio_items'),
						portfolio_items_width = $portfolio_items.width(),
						$the_portfolio_items = $portfolio_items.find('.et_pb_portfolio_item'),
						portfolio_item_count = $the_portfolio_items.length;

					if ('undefined' === typeof $the_portfolio_items) {
						return;
					}

					// calculate column breakpoints
					if ( portfolio_items_width >= 1600 ) {
						columns = 5;
					} else if ( portfolio_items_width >= 1024 ) {
						columns = 4;
					} else if ( portfolio_items_width >= 768 ) {
						columns = 3;
					} else if ( portfolio_items_width >= 480 ) {
						columns = 2;
					} else {
						columns = 1;
					}

					// set height of items
					portfolio_item_width = portfolio_items_width / columns;
					portfolio_item_height = portfolio_item_width * .75;

					if ( carousel_mode ) {
						$portfolio_items.css({ 'height' : portfolio_item_height });
					}

					$the_portfolio_items.css({ 'height' : portfolio_item_height });

					if ( columns === $portfolio_items.data('portfolio-columns') ) {
						return;
					}

					if ( $the_portfolio.data('columns_setting_up') ) {
						return;
					}

					$the_portfolio.data('columns_setting_up', true );

					var portfolio_item_width_percentage = ( 100 / columns ) + '%';
					$the_portfolio_items.css({ 'width' : portfolio_item_width_percentage });

					// store last setup column
					$portfolio_items.removeClass('columns-' + $portfolio_items.data('portfolio-columns') );
					$portfolio_items.addClass('columns-' + columns );
					$portfolio_items.data('portfolio-columns', columns );

					if ( !carousel_mode ) {
						return $the_portfolio.data('columns_setting_up', false );
					}

					// kill all previous groups to get ready to re-group
					if ( $portfolio_items.find('.et_pb_carousel_group').length ) {
						$the_portfolio_items.appendTo( $portfolio_items );
						$portfolio_items.find('.et_pb_carousel_group').remove();
					}

					// setup the grouping
					var the_portfolio_items = $portfolio_items.data('items' ),
						$carousel_group = $('<div class="et_pb_carousel_group active">').appendTo( $portfolio_items );

					if ('undefined' === typeof the_portfolio_items) {
						return;
					}

					$the_portfolio_items.data('position', '');
					if ( the_portfolio_items.length <= columns ) {
						$portfolio_items.find('.et-pb-slider-arrows').hide();
					} else {
						$portfolio_items.find('.et-pb-slider-arrows').show();
					}

					for ( position = 1, x=0 ;x < the_portfolio_items.length; x++, position++ ) {
						if ( x < columns ) {
							$( the_portfolio_items[x] ).show();
							$( the_portfolio_items[x] ).appendTo( $carousel_group );
							$( the_portfolio_items[x] ).data('position', position );
							$( the_portfolio_items[x] ).addClass('position_' + position );
						} else {
							position = $( the_portfolio_items[x] ).data('position');
							$( the_portfolio_items[x] ).removeClass('position_' + position );
							$( the_portfolio_items[x] ).data('position', '' );
							$( the_portfolio_items[x] ).hide();
						}
					}

					$the_portfolio.data('columns_setting_up', false );

				}

				function et_carousel_auto_rotate( $carousel ) {
					if ( 'on' === $carousel.data('auto-rotate') && $carousel.find('.et_pb_portfolio_item').length > $carousel.find('.et_pb_carousel_group .et_pb_portfolio_item').length && ! $carousel.hasClass( 'et_carousel_hovered' ) ) {

						et_carousel_timer = setTimeout( function() {
							fullwidth_portfolio_carousel_slide( $carousel.find('.et-pb-arrow-next') );
						}, $carousel.data('auto-rotate-speed') );

						$carousel.data('et_carousel_timer', et_carousel_timer);
					}
				}

				$et_pb_fullwidth_portfolio.each(function(){
					et_fullwidth_portfolio_init( $(this) );
				});
			}

			function et_audio_module_set() {
				if ( $( '.et_pb_audio_module .mejs-audio' ).length || $( '.et_audio_content .mejs-audio' ).length ) {
					$( '.et_audio_container' ).each( function(){
						et_pb_audio_module_init( $(this) );
					});
				}
			}

			window.et_pb_audio_module_init = function( $audio_container ) {
				var $this_player = $audio_container,
					$time_rail = $this_player.find( '.mejs-time-rail' ),
					$time_slider = $this_player.find( '.mejs-time-slider' );
				// remove previously added width and min-width attributes to calculate the new sizes accurately
				$time_rail.removeAttr( 'style' );
				$time_slider.removeAttr( 'style' );

				var $count_timer = $this_player.find( 'div.mejs-currenttime-container' ),
					$count_timer_width_container = $this_player.find( '.mejs-duration-container' ).length ? $this_player.find( '.mejs-duration-container' ) : $this_player.find( '.mejs-currenttime-container' ),
					player_width = $this_player.width(),
					controls_play_width = $this_player.find( '.mejs-play' ).outerWidth(),
					time_width = $count_timer_width_container.outerWidth(),
					volume_icon_width = $this_player.find( '.mejs-volume-button' ).outerWidth(),
					volume_bar_width = $this_player.find( '.mejs-horizontal-volume-slider' ).outerWidth(),
					new_time_rail_width;

				$count_timer.addClass( 'custom' );
				$this_player.find( '.mejs-controls div.mejs-duration-container' ).replaceWith( $count_timer );
				new_time_rail_width = player_width - ( controls_play_width + time_width + volume_icon_width + volume_bar_width + 65 );

				if ( 0 < new_time_rail_width ) {
					$time_rail.attr( 'style', 'min-width: ' + new_time_rail_width + 'px;' );
					$time_slider.attr( 'style', 'min-width: ' + new_time_rail_width + 'px;' );
				}
			}

			if ( $('.et_pb_section_video').length ) {
				window._wpmejsSettings.pauseOtherPlayers = false;
			}

			if ( $et_pb_filterable_portfolio.length || is_frontend_builder ) {

				window.et_pb_filterable_portfolio_init = function( $selector ) {
					if ( typeof $selector !== 'undefined' ){
						set_filterable_portfolio_init( $selector );
					} else {
						$et_pb_filterable_portfolio.each(function(){
							set_filterable_portfolio_init( $(this) )
						});
					}
				}

				window.set_filterable_portfolio_init = function( $the_portfolio ) {
					var $the_portfolio_items = $the_portfolio.find('.et_pb_portfolio_items'),
						$left_orientatation = true == $the_portfolio.data( 'rtl' ) ? false : true,
						all_portfolio_items = $the_portfolio_items.clone(); // cache for all the portfolio items

					$the_portfolio.show();

					set_filterable_grid_items( $the_portfolio );

					$the_portfolio.on('click', '.et_pb_portfolio_filter a', function(e){
						e.preventDefault();
						var category_slug = $(this).data('category-slug');
						$the_portfolio_items = $(this).parents('.et_pb_filterable_portfolio').find('.et_pb_portfolio_items');

						if ( 'all' == category_slug ) {
							$the_portfolio.find('.et_pb_portfolio_filter a').removeClass('active');
							$the_portfolio.find('.et_pb_portfolio_filter_all a').addClass('active');

							// remove all items from the portfolio items container
							$the_portfolio_items.empty();

							// fill the portfolio items container with cached items from memory
							$the_portfolio_items.append( all_portfolio_items.find( '.et_pb_portfolio_item' ).clone() );
							$the_portfolio.find('.et_pb_portfolio_item').addClass('active');
						} else {
							$the_portfolio.find('.et_pb_portfolio_filter_all').removeClass('active');
							$the_portfolio.find('.et_pb_portfolio_filter a').removeClass('active');
							$the_portfolio.find('.et_pb_portfolio_filter_all a').removeClass('active');
							$(this).addClass('active');

							// remove all items from the portfolio items container
							$the_portfolio_items.empty();

							// fill the portfolio items container with cached items from memory
							$the_portfolio_items.append( all_portfolio_items.find( '.et_pb_portfolio_item.project_category_' + $(this).data('category-slug') ).clone() );

							$the_portfolio_items.find('.et_pb_portfolio_item').removeClass('active');
							$the_portfolio_items.find('.et_pb_portfolio_item.project_category_' + $(this).data('category-slug') ).addClass('active').removeClass( 'inactive' );
						}

						set_filterable_grid_items( $the_portfolio );
						setTimeout(function(){
							set_filterable_portfolio_hash( $the_portfolio );
						}, 500 );
					});

					$the_portfolio.on('click', '.et_pb_portofolio_pagination a', function(e){
						e.preventDefault();

						var to_page = $(this).data('page'),
							$the_portfolio = $(this).parents('.et_pb_filterable_portfolio'),
							$the_portfolio_items = $the_portfolio.find('.et_pb_portfolio_items');

						et_pb_smooth_scroll( $the_portfolio, false, 800 );

						if ( $(this).hasClass('page-prev') ) {
							to_page = parseInt( $(this).parents('ul').find('a.active').data('page') ) - 1;
						} else if ( $(this).hasClass('page-next') ) {
							to_page = parseInt( $(this).parents('ul').find('a.active').data('page') ) + 1;
						}

						$(this).parents('ul').find('a').removeClass('active');
						$(this).parents('ul').find('a.page-' + to_page ).addClass('active');

						var current_index = $(this).parents('ul').find('a.page-' + to_page ).parent().index(),
							total_pages = $(this).parents('ul').find('li.page').length;

						$(this).parent().nextUntil('.page-' + ( current_index + 3 ) ).show();
						$(this).parent().prevUntil('.page-' + ( current_index - 3 ) ).show();

						$(this).parents('ul').find('li.page').each(function(i){
							if ( !$(this).hasClass('prev') && !$(this).hasClass('next') ) {
								if ( i < ( current_index - 3 ) ) {
									$(this).hide();
								} else if ( i > ( current_index + 1 ) ) {
									$(this).hide();
								} else {
									$(this).show();
								}

								if ( total_pages - current_index <= 2 && total_pages - i <= 5 ) {
									$(this).show();
								} else if ( current_index <= 3 && i <= 4 ) {
									$(this).show();
								}

							}
						});

						if ( to_page > 1 ) {
							$(this).parents('ul').find('li.prev').show();
						} else {
							$(this).parents('ul').find('li.prev').hide();
						}

						if ( $(this).parents('ul').find('a.active').hasClass('last-page') ) {
							$(this).parents('ul').find('li.next').hide();
						} else {
							$(this).parents('ul').find('li.next').show();
						}

						$the_portfolio.find('.et_pb_portfolio_item').hide();
						$the_portfolio.find('.et_pb_portfolio_item').filter(function( index ) {
							return $(this).data('page') === to_page;
						}).show();

						window.et_pb_set_responsive_grid( $the_portfolio.find( '.et_pb_portfolio_items' ), '.et_pb_portfolio_item' );

						setTimeout(function(){
							set_filterable_portfolio_hash( $the_portfolio );
						}, 500 );
					});

					$(this).on('et_hashchange', function( event ){
						var params = event.params;
						$the_portfolio = $( '#' + event.target.id );

						if ( !$the_portfolio.find('.et_pb_portfolio_filter a[data-category-slug="' + params[0] + '"]').hasClass('active') ) {
							$the_portfolio.find('.et_pb_portfolio_filter a[data-category-slug="' + params[0] + '"]').click();
						}

						if ( params[1] ) {
							setTimeout(function(){
								if ( !$the_portfolio.find('.et_pb_portofolio_pagination a.page-' + params[1]).hasClass('active') ) {
									$the_portfolio.find('.et_pb_portofolio_pagination a.page-' + params[1]).addClass('active').click();
								}
							}, 300 );
						}
					});
				}

				// init portfolio if .load event was fired already, wait for the window load otherwise.
				if ( window.et_load_event_fired ) {
					et_pb_filterable_portfolio_init();
				} else {
					$(window).load(function(){
						et_pb_filterable_portfolio_init();
					}); // End $(window).load()
				}

				function set_filterable_grid_items( $the_portfolio ) {
					var active_category = $the_portfolio.find('.et_pb_portfolio_filter > a.active').data('category-slug');

					window.et_pb_set_responsive_grid( $the_portfolio.find( '.et_pb_portfolio_items' ), '.et_pb_portfolio_item' );

					if ( 'all' === active_category ) {
						$the_portfolio_visible_items = $the_portfolio.find('.et_pb_portfolio_item');
					} else {
						$the_portfolio_visible_items = $the_portfolio.find('.et_pb_portfolio_item.project_category_' + active_category);
					}

					var visible_grid_items = $the_portfolio_visible_items.length,
						posts_number = $the_portfolio.data('posts-number'),
						pages = 0 === posts_number ? 1 : Math.ceil( visible_grid_items / posts_number );

					set_filterable_grid_pages( $the_portfolio, pages );

					var visible_grid_items = 0;
					var _page = 1;
					$the_portfolio.find('.et_pb_portfolio_item').data('page', '');
					$the_portfolio_visible_items.each(function(i){
						visible_grid_items++;
						if ( 0 === parseInt( visible_grid_items % posts_number ) ) {
							$(this).data('page', _page);
							_page++;
						} else {
							$(this).data('page', _page);
						}
					});

					$the_portfolio_visible_items.filter(function() {
						return $(this).data('page') == 1;
					}).show();

					$the_portfolio_visible_items.filter(function() {
						return $(this).data('page') != 1;
					}).hide();
				}

				function set_filterable_grid_pages( $the_portfolio, pages ) {
					$pagination = $the_portfolio.find('.et_pb_portofolio_pagination');

					if ( !$pagination.length ) {
						return;
					}

					$pagination.html('<ul></ul>');
					if ( pages <= 1 ) {
						return;
					}

					$pagination_list = $pagination.children('ul');
					$pagination_list.append('<li class="prev" style="display:none;"><a href="#" data-page="prev" class="page-prev">' + et_pb_custom.prev + '</a></li>');
					for( var page = 1; page <= pages; page++ ) {
						var first_page_class = page === 1 ? ' active' : '',
							last_page_class = page === pages ? ' last-page' : '',
							hidden_page_class = page >= 5 ? ' style="display:none;"' : '';
						$pagination_list.append('<li' + hidden_page_class + ' class="page page-' + page + '"><a href="#" data-page="' + page + '" class="page-' + page + first_page_class + last_page_class + '">' + page + '</a></li>');
					}
					$pagination_list.append('<li class="next"><a href="#" data-page="next" class="page-next">' + et_pb_custom.next + '</a></li>');
				}

				function set_filterable_portfolio_hash( $the_portfolio ) {

					if ( !$the_portfolio.attr('id') ) {
						return;
					}

					var this_portfolio_state = [];
					this_portfolio_state.push( $the_portfolio.attr('id') );
					this_portfolio_state.push( $the_portfolio.find('.et_pb_portfolio_filter > a.active').data('category-slug') );

					if ( $the_portfolio.find('.et_pb_portofolio_pagination a.active').length ) {
						this_portfolio_state.push( $the_portfolio.find('.et_pb_portofolio_pagination a.active').data('page') );
					} else {
						this_portfolio_state.push( 1 );
					}

					this_portfolio_state = this_portfolio_state.join( et_hash_module_param_seperator );

					et_set_hash( this_portfolio_state );
				}
			} /*  end if ( $et_pb_filterable_portfolio.length ) */

			if ( $et_pb_gallery.length || is_frontend_builder ) {

				window.set_gallery_grid_items = function( $the_gallery ) {
					var $the_gallery_items_container = $the_gallery.find('.et_pb_gallery_items'),
						$the_gallery_items = $the_gallery_items_container.find('.et_pb_gallery_item');

					var total_grid_items = $the_gallery_items.length,
						posts_number_original = parseInt( $the_gallery_items_container.attr('data-per_page') ),
						posts_number = isNaN( posts_number_original ) || 0 === posts_number_original ? 4 : posts_number_original,
						pages = Math.ceil( total_grid_items / posts_number );

					set_gallery_grid_pages( $the_gallery, pages );

					var total_grid_items = 0;
					var _page = 1;
					$the_gallery_items.data('page', '');
					$the_gallery_items.each(function(i){
						total_grid_items++;
						if ( 0 === parseInt( total_grid_items % posts_number ) ) {
							$(this).data('page', _page);
							_page++;
						} else {
							$(this).data('page', _page);
						}

					});

					var visible_items = $the_gallery_items.filter(function() {
						return $(this).data('page') == 1;
					}).show();

					$the_gallery_items.filter(function() {
						return $(this).data('page') != 1;
					}).hide();
				}

				window.set_gallery_grid_pages = function( $the_gallery, pages ) {
					$pagination = $the_gallery.find('.et_pb_gallery_pagination');

					if ( !$pagination.length ) {
						return;
					}

					$pagination.html('<ul></ul>');
					if ( pages <= 1 ) {
						$pagination.hide();
						return;
					}

					$pagination_list = $pagination.children('ul');
					$pagination_list.append('<li class="prev" style="display:none;"><a href="#" data-page="prev" class="page-prev">' + et_pb_custom.prev + '</a></li>');
					for( var page = 1; page <= pages; page++ ) {
						var first_page_class = page === 1 ? ' active' : '',
							last_page_class = page === pages ? ' last-page' : '',
							hidden_page_class = page >= 5 ? ' style="display:none;"' : '';
						$pagination_list.append('<li' + hidden_page_class + ' class="page page-' + page + '"><a href="#" data-page="' + page + '" class="page-' + page + first_page_class + last_page_class + '">' + page + '</a></li>');
					}
					$pagination_list.append('<li class="next"><a href="#" data-page="next" class="page-next">' + et_pb_custom.next + '</a></li>');
				}

				window.set_gallery_hash = function( $the_gallery ) {

					if ( !$the_gallery.attr('id') ) {
						return;
					}

					var this_gallery_state = [];
					this_gallery_state.push( $the_gallery.attr('id') );

					if ( $the_gallery.find('.et_pb_gallery_pagination a.active').length ) {
						this_gallery_state.push( $the_gallery.find('.et_pb_gallery_pagination a.active').data('page') );
					} else {
						this_gallery_state.push( 1 );
					}

					this_gallery_state = this_gallery_state.join( et_hash_module_param_seperator );

					et_set_hash( this_gallery_state );
				}

				window.et_pb_gallery_init = function( $the_gallery ) {
					if ( $the_gallery.hasClass( 'et_pb_gallery_grid' ) ) {

						$the_gallery.show();
						set_gallery_grid_items( $the_gallery );

						$the_gallery.on('et_hashchange', function( event ){
							var params = event.params;
							$the_gallery = $( '#' + event.target.id );

							if ( page_to = params[0] ) {
								if ( !$the_gallery.find('.et_pb_gallery_pagination a.page-' + page_to ).hasClass('active') ) {
									$the_gallery.find('.et_pb_gallery_pagination a.page-' + page_to ).addClass('active').click();
								}
							}
						});
					}
				}

				$et_pb_gallery.each(function(){
					var $the_gallery = $(this);

					et_pb_gallery_init( $the_gallery );
				});

				$et_pb_gallery.data('paginating', false );

				window.et_pb_gallery_pagination_nav = function( $the_gallery ) {
					$the_gallery.on('click', '.et_pb_gallery_pagination a', function(e){
						e.preventDefault();

						var to_page = $(this).data('page'),
							$the_gallery = $(this).parents('.et_pb_gallery'),
							$the_gallery_items_container = $the_gallery.find('.et_pb_gallery_items'),
							$the_gallery_items = $the_gallery_items_container.find('.et_pb_gallery_item');

						if ( $the_gallery.data('paginating') ) {
							return;
						}

						$the_gallery.data('paginating', true );

						if ( $(this).hasClass('page-prev') ) {
							to_page = parseInt( $(this).parents('ul').find('a.active').data('page') ) - 1;
						} else if ( $(this).hasClass('page-next') ) {
							to_page = parseInt( $(this).parents('ul').find('a.active').data('page') ) + 1;
						}

						$(this).parents('ul').find('a').removeClass('active');
						$(this).parents('ul').find('a.page-' + to_page ).addClass('active');

						var current_index = $(this).parents('ul').find('a.page-' + to_page ).parent().index(),
							total_pages = $(this).parents('ul').find('li.page').length;

						$(this).parent().nextUntil('.page-' + ( current_index + 3 ) ).show();
						$(this).parent().prevUntil('.page-' + ( current_index - 3 ) ).show();

						$(this).parents('ul').find('li.page').each(function(i){
							if ( !$(this).hasClass('prev') && !$(this).hasClass('next') ) {
								if ( i < ( current_index - 3 ) ) {
									$(this).hide();
								} else if ( i > ( current_index + 1 ) ) {
									$(this).hide();
								} else {
									$(this).show();
								}

								if ( total_pages - current_index <= 2 && total_pages - i <= 5 ) {
									$(this).show();
								} else if ( current_index <= 3 && i <= 4 ) {
									$(this).show();
								}

							}
						});

						if ( to_page > 1 ) {
							$(this).parents('ul').find('li.prev').show();
						} else {
							$(this).parents('ul').find('li.prev').hide();
						}

						if ( $(this).parents('ul').find('a.active').hasClass('last-page') ) {
							$(this).parents('ul').find('li.next').hide();
						} else {
							$(this).parents('ul').find('li.next').show();
						}

						$the_gallery_items.hide();
						var visible_items = $the_gallery_items.filter(function( index ) {
							return $(this).data('page') === to_page;
						}).show();

						$the_gallery.data('paginating', false );

						window.et_pb_set_responsive_grid( $the_gallery_items_container, '.et_pb_gallery_item' );

						setTimeout(function(){
							set_gallery_hash( $the_gallery );
						}, 100 );

						$( 'html, body' ).animate( { scrollTop : $the_gallery.offset().top - 200 }, 200 );
					});
				}
				et_pb_gallery_pagination_nav( $et_pb_gallery );

				// Frontend builder's interface wouldn't be able to use $et_pb_gallery as selector
				// due to its react component's nature. Using more global selector works.
				if ( is_frontend_builder ) {
					et_pb_gallery_pagination_nav( $('#et-fb-app') );
				}

			} /*  end if ( $et_pb_gallery.length ) */

			if ( $et_pb_counter_amount.length ) {
				$et_pb_counter_amount.each(function(){
					window.et_bar_counters_init( $( this ) );
				});
			} /* $et_pb_counter_amount.length */

			window.et_countdown_timer = function( timer ) {
				var end_date = parseInt( timer.attr( 'data-end-timestamp') ),
					current_date = new Date().getTime() / 1000,
					seconds_left = ( end_date - current_date );

				days = parseInt(seconds_left / 86400);
				days = days > 0 ? days : 0;
				seconds_left = seconds_left % 86400;

				hours = parseInt(seconds_left / 3600);
				hours = hours > 0 ? hours : 0;

				seconds_left = seconds_left % 3600;

				minutes = parseInt(seconds_left / 60);
				minutes = minutes > 0 ? minutes : 0;

				seconds = parseInt(seconds_left % 60);
				seconds = seconds > 0 ? seconds : 0;

				var $days_section = timer.find('.days > .value').parent('.section'),
					$hours_section = timer.find('.hours > .value').parent('.section'),
					$minutes_section = timer.find('.minutes > .value').parent('.section'),
					$seconds_section = timer.find('.seconds > .value').parent('.section');


				if ( days == 0 ) {
					if ( ! $days_section.hasClass('zero') ) {
						timer.find('.days > .value').html( '000' ).parent('.section').addClass('zero').next().addClass('zero');
					}
				} else {
					days_slice = days.toString().length >= 3 ? days.toString().length : 3;
					timer.find('.days > .value').html( ('000' + days).slice(-days_slice) );

					if ( $days_section.hasClass('zero') ) {
						$days_section.removeClass('zero').next().removeClass('zero');
					}
				}

				if ( days == 0 && hours == 0 ) {
					if ( ! $hours_section.hasClass('zero') ) {
						timer.find('.hours > .value').html('00').parent('.section').addClass('zero').next().addClass('zero');
					}
				} else {
					timer.find('.hours > .value').html( ( '0' + hours ).slice(-2) );

					if ( $hours_section.hasClass('zero') ) {
						$hours_section.removeClass('zero').next().removeClass('zero');
					}
				}

				if ( days == 0 && hours == 0 && minutes == 0 ) {
					if ( ! $minutes_section.hasClass('zero') ) {
						timer.find('.minutes > .value').html('00').parent('.section').addClass('zero').next().addClass('zero');
					}
				} else {
					timer.find('.minutes > .value').html( ( '0' + minutes ).slice(-2) );

					if ( $minutes_section.hasClass('zero') ) {
						$minutes_section.removeClass('zero').next().removeClass('zero');
					}
				}

				if ( days == 0 && hours == 0 && minutes == 0 && seconds == 0 ) {
					if ( ! $seconds_section.hasClass('zero') ) {
						timer.find('.seconds > .value').html('00').parent('.section').addClass('zero');
					}
				} else {
					timer.find('.seconds > .value').html( ( '0' + seconds ).slice(-2) );

					if ( $seconds_section.hasClass('zero') ) {
						$seconds_section.removeClass('zero').next().removeClass('zero');
					}
				}
			}

			window.et_countdown_timer_labels = function( timer ) {
				if ( timer.closest( '.et_pb_column_3_8' ).length || timer.closest( '.et_pb_column_1_4' ).length || timer.children('.et_pb_countdown_timer_container').width() <= 400 ) {
					timer.find('.days .label').html( timer.find('.days').data('short') );
					timer.find('.hours .label').html( timer.find('.hours').data('short') );
					timer.find('.minutes .label').html( timer.find('.minutes').data('short') );
					timer.find('.seconds .label').html( timer.find('.seconds').data('short') );
				} else {
					timer.find('.days .label').html( timer.find('.days').data('full') );
					timer.find('.hours .label').html( timer.find('.hours').data('full') );
					timer.find('.minutes .label').html( timer.find('.minutes').data('full') );
					timer.find('.seconds .label').html( timer.find('.seconds').data('full') );
				}
			}

			if ( $et_pb_countdown_timer.length || is_frontend_builder ) {
				window.et_pb_countdown_timer_init = function( $et_pb_countdown_timer ) {
					$et_pb_countdown_timer.each(function(){
						var timer = $(this);
						et_countdown_timer_labels( timer );
						et_countdown_timer( timer );
						setInterval(function(){
							et_countdown_timer( timer );
						}, 1000);
					});
				}
				et_pb_countdown_timer_init( $et_pb_countdown_timer );
			}

			if ( $et_pb_tabs.length || is_frontend_builder ) {
				window.et_pb_tabs_init = function( $et_pb_tabs ) {
					var $et_pb_tabs_li = $et_pb_tabs.find( '.et_pb_tabs_controls li' );

					$et_pb_tabs.et_pb_simple_slider( {
						use_controls   : false,
						use_arrows     : false,
						slide          : '.et_pb_all_tabs > div',
						tabs_animation : true
					} ).on('et_hashchange', function( event ){
						var params = event.params;
						var $the_tabs = $( '#' + event.target.id );
						var active_tab = params[0];
						if ( !$the_tabs.find( '.et_pb_tabs_controls li' ).eq( active_tab ).hasClass('et_pb_tab_active') ) {
							$the_tabs.find( '.et_pb_tabs_controls li' ).eq( active_tab ).click();
						}
					});

					$et_pb_tabs_li.click( function() {
						var $this_el        = $(this),
							$tabs_container = $this_el.closest( '.et_pb_tabs' ).data('et_pb_simple_slider');

						if ( $tabs_container.et_animation_running ) return false;

						$this_el.addClass( 'et_pb_tab_active' ).siblings().removeClass( 'et_pb_tab_active' );

						$tabs_container.data('et_pb_simple_slider').et_slider_move_to( $this_el.index() );

						if ( $this_el.closest( '.et_pb_tabs' ).attr('id') ) {
							var tab_state = [];
							tab_state.push( $this_el.closest( '.et_pb_tabs' ).attr('id') );
							tab_state.push( $this_el.index() );
							tab_state = tab_state.join( et_hash_module_param_seperator );
							et_set_hash( tab_state );
						}

						return false;
					} );

					window.et_pb_set_tabs_height();
				}
				window.et_pb_tabs_init( $et_pb_tabs );
			}

			if ( $et_pb_map.length || is_frontend_builder ) {
				function et_pb_init_maps() {
					$et_pb_map.each(function(){
						et_pb_map_init( $(this) );
					});
				}

				window.et_pb_map_init = function( $this_map_container ) {
					if (typeof google === 'undefined') {
						return;
					}

					var $this_map = $this_map_container.children('.et_pb_map'),
						this_map_grayscale = $this_map_container.attr( 'data-grayscale' ) || 0,
						is_draggable = ( et_is_mobile_device && $this_map.data('mobile-dragging') !== 'off' ) || ! et_is_mobile_device,
						infowindow_active;

					if ( this_map_grayscale !== 0 ) {
						this_map_grayscale = '-' + this_map_grayscale.toString();
					}

					$this_map_container.data('map', new google.maps.Map( $this_map[0], {
						zoom: parseInt( $this_map.attr('data-zoom') ),
						center: new google.maps.LatLng( parseFloat( $this_map.attr('data-center-lat') ) , parseFloat( $this_map.attr('data-center-lng') )),
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						scrollwheel: $this_map.attr('data-mouse-wheel') == 'on' ? true : false,
						draggable: is_draggable,
						panControlOptions: {
							position: $this_map_container.is( '.et_beneath_transparent_nav' ) ? google.maps.ControlPosition.LEFT_BOTTOM : google.maps.ControlPosition.LEFT_TOP
						},
						zoomControlOptions: {
							position: $this_map_container.is( '.et_beneath_transparent_nav' ) ? google.maps.ControlPosition.LEFT_BOTTOM : google.maps.ControlPosition.LEFT_TOP
						},
						styles: [ {
							stylers: [
								{ saturation: parseInt( this_map_grayscale ) }
							]
						} ]
					}));

					$this_map_container.find('.et_pb_map_pin').each(function(){
						var $this_marker = $(this);

						var marker = new google.maps.Marker({
							position: new google.maps.LatLng( parseFloat( $this_marker.attr('data-lat') ) , parseFloat( $this_marker.attr('data-lng') ) ),
							map: $this_map_container.data('map'),
							title: $this_marker.attr('data-title'),
							icon: { url: et_pb_custom.builder_images_uri + '/marker.png', size: new google.maps.Size( 46, 43 ), anchor: new google.maps.Point( 16, 43 ) },
							shape: { coord: [1, 1, 46, 43], type: 'rect' },
							anchorPoint: new google.maps.Point(0, -45)
						});

						if ( $this_marker.find('.infowindow').length ) {
							var infowindow = new google.maps.InfoWindow({
								content: $this_marker.html()
							});

							google.maps.event.addListener( $this_map_container.data('map'), 'click', function() {
								infowindow.close();
							});

							google.maps.event.addListener(marker, 'click', function() {
								if( infowindow_active ) {
									infowindow_active.close();
								}
								infowindow_active = infowindow;

								infowindow.open( $this_map_container.data('map'), marker );
							});
						}
					});
				}

				if ( window.et_load_event_fired ) {
					et_pb_init_maps();
				} else {
					if ( typeof google !== 'undefined' ) {
						google.maps.event.addDomListener(window, 'load', function() {
							et_pb_init_maps();
						} );
					}
				}
			}

			if ( $et_pb_shop.length ) {
				$et_pb_shop.each( function() {
					var $this_el = $(this),
						icon     = $this_el.data('icon') || '';

					if ( icon === '' ) {
						return true;
					}

					$this_el.find( '.et_overlay' )
						.attr( 'data-icon', icon )
						.addClass( 'et_pb_inline_icon' );
				} );
			}

			if ( $et_pb_circle_counter.length || is_frontend_builder ) {

				window.et_pb_circle_counter_init = function($the_counter, animate) {
					if ( 0 === $the_counter.width() ) {
						return;
					}

					$the_counter.easyPieChart({
						animate: {
							duration: 1800,
							enabled: true
						},
						size: 0 !== $the_counter.width() ? $the_counter.width() : 10, // set the width to 10 if actual width is 0 to avoid js errors
						barColor: $the_counter.data( 'bar-bg-color' ),
						trackColor: $the_counter.data( 'color' ) || '#000000',
						trackAlpha: $the_counter.data( 'alpha' ) || '0.1',
						scaleColor: false,
						lineWidth: 5,
						onStart: function() {
							$(this.el).find('.percent p').css({ 'visibility' : 'visible' });
						},
						onStep: function(from, to, percent) {
							$(this.el).find('.percent-value').text( Math.round( parseInt( percent ) ) );
						},
						onStop: function(from, to) {
							$(this.el).find('.percent-value').text( $(this.el).data('number-value') );
						}
					});
				}

				window.et_pb_reinit_circle_counters = function( $et_pb_circle_counter ) {
					$et_pb_circle_counter.each(function(){
						var $the_counter = $(this);
						window.et_pb_circle_counter_init($the_counter, false);

						$the_counter.on('containerWidthChanged', function( event ){
							$the_counter = $( event.target );
							$the_counter.find('canvas').remove();
							$the_counter.removeData('easyPieChart' );
							window.et_pb_circle_counter_init($the_counter, true);
						});

					});
				}
				window.et_pb_reinit_circle_counters( $et_pb_circle_counter );
			}

			if ( $et_pb_number_counter.length || is_frontend_builder ) {
				window.et_pb_reinit_number_counters = function( $et_pb_number_counter ) {

					function et_format_number( number_value, separator ) {
						return number_value.toString().replace( /\B(?=(\d{3})+(?!\d))/g, separator );
					}

					if ( $.fn.fitText ) {
						$et_pb_number_counter.find( '.percent p' ).fitText( 0.3 );
					}

					$et_pb_number_counter.each(function(){
						var $this_counter = $(this);
						var separator     = $this_counter.data('number-separator');

						$this_counter.easyPieChart({
							animate: {
								duration: 1800,
								enabled: true
							},
							size: 0,
							trackColor: false,
							scaleColor: false,
							lineWidth: 0,
							onStart: function() {
								$(this.el).find('.percent p').css({ 'visibility' : 'visible' });
							},
							onStep: function(from, to, percent) {
								if ( percent != to )
									$(this.el).find('.percent-value').text( et_format_number( Math.round( parseInt( percent ) ), separator ) );
							},
							onStop: function(from, to) {
								$(this.el).find('.percent-value').text( et_format_number( $(this.el).data('number-value'), separator ) );
							}
						});
					});
				}
				window.et_pb_reinit_number_counters( $et_pb_number_counter );
			}

			window.et_apply_parallax = function() {
				if ( ! $(this).length || typeof $(this) === 'undefined' || typeof $(this).offset() === 'undefined') {
					return;
				}

				var $this = $(this),
					element_top = $this.offset().top,
					window_top = $et_window.scrollTop(),
					y_pos = ( ( ( window_top + $et_window.height() ) - element_top ) * 0.3 ),
					main_position;

				main_position = 'translate(0, ' + y_pos + 'px)';

				$this.children('.et_parallax_bg').css( {
					'-webkit-transform' : main_position,
					'-moz-transform'    : main_position,
					'-ms-transform'     : main_position,
					'transform'         : main_position
				} );
			}

			window.et_parallax_set_height = function() {
				var $this = $(this),
					bg_height;

				bg_height = ( $et_window.height() * 0.3 + $this.innerHeight() );

				$this.find('.et_parallax_bg').css( { 'height' : bg_height } );
			}

			function et_toggle_animation_callback( initial_toggle_state, $module, $section ) {
				if ( 'closed' === initial_toggle_state ) {
					$module.removeClass('et_pb_toggle_close').addClass('et_pb_toggle_open');
				} else {
					$module.removeClass('et_pb_toggle_open').addClass('et_pb_toggle_close');
				}

				if ( $section.hasClass( 'et_pb_section_parallax' ) && !$section.children().hasClass( 'et_pb_parallax_css') ) {
					$.proxy( et_parallax_set_height, $section )();
				}
			}

			$( 'body' ).on( 'click', '.et_pb_toggle_title, .et_fb_toggle_overlay', function() {
				var $this_heading         = $(this),
					$module               = $this_heading.closest('.et_pb_toggle'),
					$section              = $module.parents( '.et_pb_section' ),
					$content              = $module.find('.et_pb_toggle_content'),
					$accordion            = $module.closest( '.et_pb_accordion' ),
					is_accordion          = $accordion.length,
					is_accordion_toggling = $accordion.hasClass( 'et_pb_accordion_toggling' ),
					window_offset_top     = $(window).scrollTop(),
					fixed_header_height   = 0,
					initial_toggle_state  = $module.hasClass( 'et_pb_toggle_close' ) ? 'closed' : 'opened',
					$accordion_active_toggle,
					module_offset;

				if ( is_accordion ) {
					if ( $module.hasClass('et_pb_toggle_open') || is_accordion_toggling ) {
						return false;
					}

					$accordion.addClass( 'et_pb_accordion_toggling' );
					$accordion_active_toggle = $module.siblings('.et_pb_toggle_open');
				}

				if ( $content.is( ':animated' ) ) {
					return;
				}

				if ( $('body').hasClass('safari') ) {
					$content.fadeToggle( 700, function() {
						et_toggle_animation_callback( initial_toggle_state, $module, $section );
					} );
				} else {
					$content.slideToggle( 700, function() {
						et_toggle_animation_callback( initial_toggle_state, $module, $section );
					} );
				}

				if ( is_accordion ) {
					$accordion_active_toggle.find('.et_pb_toggle_content').slideToggle( 700, function() {
						$accordion_active_toggle.removeClass( 'et_pb_toggle_open' ).addClass('et_pb_toggle_close');
						$accordion.removeClass( 'et_pb_accordion_toggling' );

						module_offset = $module.offset();

						// Calculate height of fixed nav
						if ( $('#wpadminbar').length ) {
							fixed_header_height += $('#wpadminbar').height();
						}

						if ( $('#top-header').length ) {
							fixed_header_height += $('#top-header').height();
						}

						if ( $('#main-header').length && ! window.et_is_vertical_nav ) {
							fixed_header_height += $('#main-header').height();
						}

						// Compare accordion offset against window's offset and adjust accordingly
						if ( ( window_offset_top + fixed_header_height ) > module_offset.top ) {
							$('html, body').animate({ scrollTop : ( module_offset.top - fixed_header_height - 50 ) });
						}
					} );
				}
			} );

			var $et_contact_container = $( '.et_pb_contact_form_container' );

			if ( $et_contact_container.length ) {
				$et_contact_container.each( function() {
					var $this_contact_container = $( this ),
						$et_contact_form = $this_contact_container.find( 'form' ),
						$et_contact_submit = $this_contact_container.find( 'input.et_pb_contact_submit' ),
						$et_inputs = $et_contact_form.find( 'input[type=text], input[type=radio]:checked, textarea, .et_pb_contact_select' ),
						et_email_reg = /^[\w-]+(\.[\w-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)*?\.[a-z]{2,}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/,
						redirect_url = typeof $this_contact_container.data( 'redirect_url' ) !== 'undefined' ? $this_contact_container.data( 'redirect_url' ) : '';

					$et_contact_form.find( 'input[type=checkbox]' ).on( 'change', function() {
						var $checkbox = $(this);
						var $checkbox_field = $checkbox.siblings( 'input[type=text]:first' );
						var is_checked = $checkbox.prop( 'checked' );

						$checkbox_field.val( is_checked ? $checkbox_field.data( 'checked' ) : $checkbox_field.data( 'unchecked' ) );
					} );

					$et_contact_form.on( 'submit', function( event ) {
						var $this_contact_form = $( this ),
							$this_inputs = $this_contact_form.find( 'input[type=text], .et_pb_contact_field[data-type="checkbox"], .et_pb_contact_field[data-type="radio"], textarea, select' ),
							this_et_contact_error = false,
							$et_contact_message = $this_contact_form.closest( '.et_pb_contact_form_container' ).find( '.et-pb-contact-message' ),
							et_message = '',
							et_fields_message = '',
							$this_contact_container = $this_contact_form.closest( '.et_pb_contact_form_container' ),
							$captcha_field = $this_contact_form.find( '.et_pb_contact_captcha' ),
							form_unique_id = typeof $this_contact_container.data( 'form_unique_num' ) !== 'undefined' ? $this_contact_container.data( 'form_unique_num' ) : 0,
							inputs_list = [];
						et_message = '<ul>';

						$this_inputs.removeClass( 'et_contact_error' );

						$this_inputs.each( function(){
							var $this_el      = $( this );
							var $this_wrapper = false;

							// Prevent field processing if that field is not visible (conditional logic)
							if ( ! $this_el.is(':visible') ) {
								return;
							}

							if ( 'checkbox' === $this_el.data('type') ) {
								$this_el      = $this_el.find('input[type="checkbox"]');
								$this_wrapper = $this_el.parents('.et_pb_contact_field');
							}

							if ( 'radio' === $this_el.data('type') ) {
								$this_el = $this_el.find('input[type="radio"]');
								$this_wrapper = $this_el.parents('.et_pb_contact_field');
							}

							var this_id       = $this_el.attr( 'id' );
							var this_val      = $this_el.val();
							var this_label    = $this_el.siblings( 'label:first' ).text();
							var field_type    = typeof $this_el.data( 'field_type' ) !== 'undefined' ? $this_el.data( 'field_type' ) : 'text';
							var required_mark = typeof $this_el.data( 'required_mark' ) !== 'undefined' ? $this_el.data( 'required_mark' ) : 'not_required';
							var original_id   = typeof $this_el.data( 'original_id' ) !== 'undefined' ? $this_el.data( 'original_id' ) : '';
							var unchecked     = false;
							var default_value;

							// Escape double quotes in label
							this_label = this_label.replace(/"/g, "&quot;");

							// radio field properties adjustment
							if ( 'radio' === field_type ) {
								if ( 0 !== $this_wrapper.find( 'input[type="radio"]').length ) {
									field_type = 'radio';

									var $firstRadio = $this_wrapper.find('input[type="radio"]:first');

									required_mark = typeof $firstRadio.data( 'required_mark' ) !== 'undefined' ? $firstRadio.data( 'required_mark' ) : 'not_required';
									original_id   = typeof $firstRadio.data( 'original_id' ) !== 'undefined' ? $firstRadio.data( 'original_id' ) : '';

									this_val = '';
									if ( $this_wrapper.find('input[type="radio"]:checked') ) {
										this_val = $this_wrapper.find('input[type="radio"]:checked').val();
									}
								}

								this_label = $this_wrapper.find('.et_pb_contact_form_label').text();
								this_id = $this_wrapper.find('input[type="radio"]:first').attr('name');

								if ( 0 === $this_wrapper.find('input[type="radio"]:checked').length ) {
									unchecked = true;
								}
							}

							// checkbox field value adjustment
							if ( 'checkbox' === field_type ) {
								var $checkbox = $this_el;
								var $handle   = $checkbox.siblings('[data-checked][data-unchecked]');

								this_id       = $handle.attr('id');
								this_val      = $checkbox.prop('checked') ? $handle.data('checked') : $handle.data('unchecked');
								unchecked     = ! $checkbox.prop('checked');

								$handle.val( this_val );
							}

							// add current field data into array of inputs
							if ( typeof this_id !== 'undefined' ) {
								inputs_list.push( { 'field_id' : this_id, 'original_id' : original_id, 'required_mark' : required_mark, 'field_type' : field_type, 'field_label' : this_label } );
							}

							// add error message for the field if it is required and empty
							if ( 'required' === required_mark && ( '' === this_val || true === unchecked ) ) {
								if ( false === $this_wrapper ) {
									$this_el.addClass( 'et_contact_error' );
								} else {
									$this_wrapper.addClass( 'et_contact_error' );
								}

								this_et_contact_error = true;

								default_value = this_label;

								if ( '' === default_value ) {
									default_value = et_pb_custom.captcha;
								}

								et_fields_message += '<li>' + default_value + '</li>';
							}

							// add error message if email field is not empty and fails the email validation
							if ( 'email' === field_type ) {
								// remove trailing/leading spaces and convert email to lowercase
								var processed_email = this_val.trim().toLowerCase();
								var is_valid_email = et_email_reg.test( processed_email );

								if ( '' !== processed_email && this_label !== processed_email && ! is_valid_email ) {
									$this_el.addClass( 'et_contact_error' );
									this_et_contact_error = true;

									if ( ! is_valid_email ) {
										et_message += '<li>' + et_pb_custom.invalid + '</li>';
									}
								}
							}
						});

						// check the captcha value if required for current form
						if ( $captcha_field.length && '' !== $captcha_field.val() ) {
							var first_digit = parseInt( $captcha_field.data( 'first_digit' ) ),
								second_digit = parseInt( $captcha_field.data( 'second_digit' ) );

							if ( parseInt( $captcha_field.val() ) !== first_digit + second_digit ) {

								et_message += '<li>' + et_pb_custom.wrong_captcha + '</li>';
								this_et_contact_error = true;

								// generate new digits for captcha
								first_digit = Math.floor( ( Math.random() * 15 ) + 1 );
								second_digit = Math.floor( ( Math.random() * 15 ) + 1 );

								// set new digits for captcha
								$captcha_field.data( 'first_digit', first_digit );
								$captcha_field.data( 'second_digit', second_digit );

								// regenerate captcha on page
								$this_contact_form.find( '.et_pb_contact_captcha_question' ).empty().append( first_digit  + ' + ' + second_digit );
							}

						}

						if ( ! this_et_contact_error ) {
							var $href = $( this ).attr( 'action' ),
								form_data = $( this ).serializeArray();

							form_data.push( { 'name': 'et_pb_contact_email_fields_' + form_unique_id, 'value' : JSON.stringify( inputs_list ) } );

							$this_contact_container.fadeTo( 'fast', 0.2 ).load( $href + ' #' + $this_contact_form.closest( '.et_pb_contact_form_container' ).attr( 'id' ), form_data, function( responseText ) {
								if ( ! $( responseText ).find( '.et_pb_contact_error_text').length ) {

									et_pb_maybe_log_event( $this_contact_container, 'con_goal' );

									// redirect if redirect URL is not empty and no errors in contact form
									if ( '' !== redirect_url ) {
										window.location.href = redirect_url;
									}
								}

								$this_contact_container.fadeTo( 'fast', 1 );
							} );
						}

						et_message += '</ul>';

						if ( '' !== et_fields_message ) {
							if ( et_message != '<ul></ul>' ) {
								et_message = '<p class="et_normal_padding">' + et_pb_custom.contact_error_message + '</p>' + et_message;
							}

							et_fields_message = '<ul>' + et_fields_message + '</ul>';

							et_fields_message = '<p>' + et_pb_custom.fill_message + '</p>' + et_fields_message;

							et_message = et_fields_message + et_message;
						}

						if ( et_message != '<ul></ul>' ) {
							$et_contact_message.html( et_message );

							// If parent of this contact form uses parallax
							if ( $this_contact_container.parents('.et_pb_section_parallax').length ) {
								$this_contact_container.parents('.et_pb_section_parallax').each(function() {
									var $parallax_element = $(this),
										$parallax         = $parallax_element.children('.et_parallax_bg'),
										is_true_parallax  = ( ! $parallax.hasClass( 'et_pb_parallax_css' ) );

									if ( is_true_parallax ) {
										$et_window.trigger( 'resize' );
									}
								});
							}
						}

						event.preventDefault();
					});
				});
			}

			window.et_pb_play_overlayed_video = function( $play_video ) {
				var $this        = $play_video,
					$video_image = $this.closest('.et_pb_video_overlay'),
					$wrapper     = $this.closest('.et_pb_video, .et_main_video_container, .et_pb_video_wrap'),
					$video_iframe = $wrapper.find('iframe'),
					is_embedded = $video_iframe.length ? true : false,
					video_iframe_src,
					video_iframe_src_splitted,
					video_iframe_src_autoplay;

				if (is_embedded) {
					// Add autoplay parameter to automatically play embedded content when overlay is clicked
					video_iframe_src = $video_iframe.attr('src');
					video_iframe_src_splitted = video_iframe_src.split("?");

					if (video_iframe_src.indexOf('autoplay=') !== -1) {
						return;
					}

					if (typeof video_iframe_src_splitted[1] !== 'undefined') {
						video_iframe_src_autoplay = video_iframe_src_splitted[0] + "?autoplay=1&amp;" + video_iframe_src_splitted[1];
					} else {
						video_iframe_src_autoplay = video_iframe_src_splitted[0] + "?autoplay=1";
					}

					$video_iframe.attr({
						'src': video_iframe_src_autoplay
					});
				} else {
					$wrapper.find('video').get(0).play();
				}


				$video_image.fadeTo( 500, 0, function() {
					var $image = $(this);

					$image.css( 'display', 'none' );
				} );
			};

			$( '.et_pb_post .et_pb_video_overlay, .et_pb_video .et_pb_video_overlay, .et_pb_video_wrap .et_pb_video_overlay' ).click( function() {
				var $this = $(this);

				et_pb_play_overlayed_video( $this );

				return false;
			} );

			window.et_pb_resize_section_video_bg = function( $video ) {
				$element = typeof $video !== 'undefined' ? $video.closest( '.et_pb_section_video_bg' ) : $( '.et_pb_section_video_bg' );

				$element.each( function() {
					var $this_el  = $(this);

					if ( is_frontend_builder ) {
						$this_el.removeAttr('data-ratio');
						$this_el.find('video').removeAttr('style');
					}

					var el_ratio  = parseFloat( $this_el.attr( 'data-ratio' ) );
					var el_width  = parseInt( $this_el.find( 'video' ).attr( 'width' ) || $this_el.find( 'video' ).width() );
					var el_height = parseInt( $this_el.find( 'video' ).attr( 'height' ) || $this_el.find( 'video' ).height() );

					var ratio = ( ! isNaN( el_ratio ) ) ? el_ratio : ( el_width / el_height );

					var $video_elements = $this_el.find( '.mejs-video, video, object' ).css( 'margin', 0 );

					var  $container = $this_el.closest( '.et_pb_section_video' ).length
							? $this_el.closest( '.et_pb_section_video' )
							: $this_el.closest( '.et_pb_slides' );

					var body_width = $container.innerWidth();

					var container_height = $container.innerHeight();

					var width, height;

					if ( typeof $this_el.attr( 'data-ratio' ) == 'undefined' && !isNaN(ratio) ) {
						$this_el.attr( 'data-ratio', ratio );
					}

					if ( body_width / container_height < ratio ) {
						width = container_height * ratio;
						height = container_height;
					} else {
						width = body_width;
						height = body_width / ratio;
					}

					$video_elements.width( width ).height( height );

					// need to re-set the values to make it work correctly in Frontend builder
					if ( is_frontend_builder ) {
						setTimeout( function() {
							$video_elements.width( width ).height( height );
						}, 0 );
					}
				} );
			};

			window.et_pb_center_video = function( $video ) {
				$element = typeof $video !== 'undefined' ? $video : $( '.et_pb_section_video_bg .mejs-video' );

				if ( ! $element.length ) {
					return;
				}

				$element.each( function() {
					var $this_el = $(this);

					et_pb_adjust_video_margin( $this_el );

					// need to re-calculate the values in Frontend builder
					if ( is_frontend_builder ) {
						setTimeout( function() {
							et_pb_adjust_video_margin( $this_el );
						}, 0 );
					}

					if ( typeof $video !== 'undefined' ) {
						if ( $video.closest( '.et_pb_slider' ).length && ! $video.closest( '.et_pb_first_video' ).length ) {
							return false;
						}
					}
				} );
			};

			window.et_pb_adjust_video_margin = function( $el ) {
				var $video_width          = $el.width() / 2;
				var $video_width_negative = 0 - $video_width;

				$el.css("margin-left", $video_width_negative );
			}

			window.et_fix_slider_height = function( $slider ) {
				var $this_slider = $slider || $et_pb_slider;

				if ( ! $this_slider || ! $this_slider.length ) {
					return;
				}

				$this_slider.each( function() {
					var $slide_section = $(this).parent( '.et_pb_section' ),
						$slides = $(this).find( '.et_pb_slide' ),
						$slide_containers = $slides.find( '.et_pb_container' ),
						max_height = 0,
						image_margin = 0,
						need_image_margin_top = $(this).hasClass( 'et_pb_post_slider_image_top' ),
						need_image_margin_bottom = $(this).hasClass( 'et_pb_post_slider_image_bottom' );

					// If this is appears at the first section beneath transparent nav, skip it
					// leave it to et_fix_page_container_position()
					if ( $slide_section.is( '.et_pb_section_first' ) ){
						return true;
					}

					$slide_containers.css( 'height', 0 );

					// make slides visible to calculate the height correctly
					$slides.addClass( 'et_pb_temp_slide' );

					if ( typeof $(this).data('et_pb_simple_slider') === 'object' ) {
						$(this).data('et_pb_simple_slider').et_fix_slider_content_images();
					}

					$slides.each( function() {
						var height = parseFloat( $(this).innerHeight() ),
							$slide_image = $(this).find( '.et_pb_slide_image' ),
							adjustedHeight = parseFloat( $(this).data( 'adjustedHeight' ) ),
							autoTopPadding = isNaN( adjustedHeight ) ? 0 : adjustedHeight;

						// reduce the height by autopadding value if slider height was adjusted. This is required in VB.
						height = ( autoTopPadding && autoTopPadding < height ) ? ( height - autoTopPadding ) : height;

						if ( need_image_margin_top || need_image_margin_bottom ) {
							if ( $slide_image.length ) {
								// get the margin from slides with image
								image_margin = need_image_margin_top ? parseFloat( $slide_image.css( 'margin-top' ) ) : parseFloat( $slide_image.css( 'margin-bottom' ) );
								image_margin += 10;
							} else {
								// add class to slides without image to adjust their height accordingly
								$(this).find( '.et_pb_container' ).addClass( 'et_pb_no_image' );
							}
						}

						// mark the slides without content
						if ( 0 === Math.abs( parseInt( $(this).find( '.et_pb_slide_description' ).height() ) ) ) {
							$(this).find( '.et_pb_container' ).addClass( 'et_pb_empty_slide' );
						}

						if ( max_height < height ) {
							max_height = height;
						}
					} );

					if ( ( max_height + image_margin ) < 1 ) {
						// No slides have any content. It's probably being used with background images only.
						// Reset the height so that it falls back to the default padding for the content.
						$slide_containers.css( 'height', '' );

					} else {
						$slide_containers.css( 'height', max_height + image_margin );
					}

					// remove temp class after getting the slider height
					$slides.removeClass( 'et_pb_temp_slide' );

					// Show the active slide's image (if exists)
					$slides.filter('.et-pb-active-slide')
						.find( '.et_pb_slide_image' )
						.children( 'img' )
						.addClass( 'active' );
				} );
			};

			/**
			 * Add conditional class to prevent unwanted dropdown nav
			 */
			function et_fix_nav_direction() {
				window_width = $(window).width();
				$('.nav li.et-reverse-direction-nav').removeClass( 'et-reverse-direction-nav' );
				$('.nav li li ul').each(function(){
					var $dropdown       = $(this),
						dropdown_width  = $dropdown.width(),
						dropdown_offset = $dropdown.offset(),
						$parents        = $dropdown.parents('.nav > li');

					if ( dropdown_offset.left > ( window_width - dropdown_width ) ) {
						$parents.addClass( 'et-reverse-direction-nav' );
					}
				});
			}
			et_fix_nav_direction();

			et_pb_form_placeholders_init( $( '.et_pb_newsletter_form, .et_pb_comments_module #commentform' ) );

			$('.et_pb_fullwidth_menu ul.nav').each(function(i) {
				i++;
				et_duplicate_menu( $(this), $(this).parents('.et_pb_row').find('div .mobile_nav'), 'mobile_menu' + i, 'et_mobile_menu' );
			});

			$('.et_pb_fullwidth_menu').each(function() {
				var this_menu = $( this ),
					bg_color = this_menu.data( 'bg_color' );
				if ( bg_color ) {
					this_menu.find( 'ul' ).css( { 'background-color' : bg_color } );
				}
			});

			$et_pb_newsletter_button.click( function( event ) {
				et_pb_submit_newsletter( $(this), event );
			} );

			window.et_pb_submit_newsletter = function( $submit, event ) {
				if ( $submit.closest( '.et_pb_login_form' ).length || $submit.closest( '.et_pb_feedburner_form' ).length ) {
					et_pb_maybe_log_event( $submit.closest( '.et_pb_newsletter' ), 'con_goal' );
					return;
				}

				if ( typeof event !== 'undefined' ) {
					event.preventDefault();
				}

				var $newsletter_container = $submit.closest( '.et_pb_newsletter' ),
					$firstname = $newsletter_container.find( 'input[name="et_pb_signup_firstname"]' ),
					$lastname = $newsletter_container.find( 'input[name="et_pb_signup_lastname"]' ),
					$email = $newsletter_container.find( 'input[name="et_pb_signup_email"]' ),
					list_id = $newsletter_container.find( 'input[name="et_pb_signup_list_id"]' ).val(),
					$result = $newsletter_container.find( '.et_pb_newsletter_result' ).hide(),
					service = $submit.closest( '.et_pb_newsletter_form' ).data( 'service' ) || 'mailchimp',
					account = $newsletter_container.find( 'input[name="et_pb_signup_account_name"]' ).val();

				$firstname.removeClass( 'et_pb_signup_error' );
				$lastname.removeClass( 'et_pb_signup_error' );
				$email.removeClass( 'et_pb_signup_error' );

				et_pb_remove_placeholder_text( $submit.closest( '.et_pb_newsletter_form' ) );

				if ( $firstname.val() == '' || $email.val() == '' || list_id === '' ) {
					if ( $firstname.val() == '' ) $firstname.addClass( 'et_pb_signup_error' );

					if ( $email.val() == '' ) $email.addClass( 'et_pb_signup_error' );

					if ( $firstname.val() == '' )
						$firstname.val( $firstname.siblings( '.et_pb_contact_form_label' ).text() );

					if ( $lastname.val() == '' )
						$lastname.val( $lastname.siblings( '.et_pb_contact_form_label' ).text() );

					if ( $email.val() == '' )
						$email.val( $email.siblings( '.et_pb_contact_form_label' ).text() );

					return;
				}

				$.ajax( {
					type: "POST",
					url: et_pb_custom.ajaxurl,
					dataType: "json",
					data:
					{
						action : 'et_pb_submit_subscribe_form',
						et_frontend_nonce : et_pb_custom.et_frontend_nonce,
						et_list_id : list_id,
						et_firstname : $firstname.val(),
						et_lastname : $lastname.val(),
						et_email : $email.val(),
						et_service : service,
						et_account: account
					},
					beforeSend: function() {
						$newsletter_container
							.find( '.et_pb_newsletter_button' )
							.addClass( 'et_pb_button_text_loading' )
							.find('.et_subscribe_loader')
							.show();
					},
					complete: function(){
						$newsletter_container
							.find( '.et_pb_newsletter_button' )
							.removeClass( 'et_pb_button_text_loading' )
							.find('.et_subscribe_loader')
							.hide();
					},
					success: function( data ){
						if ( data ) {
							if ( data.error ) {
								$result.html( data.error ).show();
							}
							if ( data.success ) {
								$newsletter_container.find( '.et_pb_newsletter_form > p' ).hide();
								$result.html( data.success ).show();

								et_pb_maybe_log_event( $newsletter_container, 'con_goal' );
							}
						} else {
							$result.html( et_pb_custom.subscription_failed ).show();
						}
					}
				} );
			};

			window.et_fix_testimonial_inner_width = function() {
				var window_width = $( window ).width();

				if ( window_width > 767 ) {
					$( '.et_pb_testimonial' ).each( function() {
						if ( ! $(this).is( ':visible' ) ) {
							return;
						}

						var $testimonial            = $(this);
						var testimonial_width       = $testimonial.width();
						var $portrait               = $testimonial.find( '.et_pb_testimonial_portrait' );
						var portrait_width          = $portrait.outerWidth( true );
						var $testimonial_inner      = $testimonial.find( '.et_pb_testimonial_description_inner' );
						var $outer_column           = $testimonial.closest( '.et_pb_column' );
						var testimonial_inner_width = testimonial_width;
						var subtract                = ! ( $outer_column.hasClass( 'et_pb_column_1_3' ) || $outer_column.hasClass( 'et_pb_column_1_4' ) || $outer_column.hasClass( 'et_pb_column_3_8' ) ) ? portrait_width : 0;

						$testimonial_inner.width( testimonial_inner_width - subtract );
					} );
				} else {
					$( '.et_pb_testimonial_description_inner' ).removeAttr( 'style' );
				}
			}
			window.et_fix_testimonial_inner_width();

			window.et_pb_video_background_init = function( $this_video_background, this_video_background ) {
				var $video_background_wrapper = $this_video_background.closest( '.et_pb_section_video_bg' );

				// Initializing video values
				var onplaying = false;
				var onpause   = true;

				// On video playing toggle values
				this_video_background.onplaying = function() {
					onplaying = true;
					onpause   = false;
				};

				// On video pause toggle values
				this_video_background.onpause = function() {
					onplaying = false;
					onpause   = true;
				};

				// Entering video's top viewport
				et_waypoint( $video_background_wrapper, {
					offset: '100%',
					handler : function( direction ) {
						if ( $this_video_background.is(':visible') && direction === 'down' ) {
							if ( this_video_background.paused && ! onplaying ) {
								this_video_background.play();
							}
						} else if ( $this_video_background.is(':visible') && direction === 'up' ) {
							if ( ! this_video_background.paused && ! onpause ) {
								this_video_background.pause();
							}
						}
					}
				});

				// Entering video's bottom viewport
				et_waypoint( $video_background_wrapper, {
					offset: function() {
						var video_height = this.element.clientHeight,
							toggle_offset = Math.ceil( window.innerHeight / 2);

						if ( video_height > toggle_offset ) {
							toggle_offset = video_height;
						}

						return toggle_offset * (-1);
					},
					handler : function( direction ) {
						if ( $this_video_background.is(':visible') && direction === 'up' ) {
							if ( this_video_background.paused && ! onplaying ) {
								this_video_background.play();
							}
						} else if ( $this_video_background.is(':visible') && direction === 'down' ) {
							if ( ! this_video_background.paused && ! onpause ) {
								this_video_background.pause();
							}
						}
					}
				});
			};

			function et_waypoint( $element, options ) {
				if ( ! $element.data( 'et_waypoint' ) ) {
					var instances = $element.waypoint( options );

					if ( instances && instances.length > 0 ) {
						$element.data( 'et_waypoint', instances[0] );
					}
				} else {
					// Reinit existing
					$element.data( 'et_waypoint' ).context.refresh();
				}
			}

			window.et_reinit_waypoint_modules = et_pb_debounce( function() {
					var $et_pb_circle_counter = $( '.et_pb_circle_counter' ),
						$et_pb_number_counter = $( '.et_pb_number_counter' ),
						$et_pb_video_background = $( '.et_pb_section_video_bg video' );

				if ( $.fn.waypoint && 'yes' !== et_pb_custom.ignore_waypoints ) {
					et_waypoint( $( '.et_pb_counter_container, .et-waypoint' ), {
						offset: '75%',
						handler: function() {
							$(this.element).addClass( 'et-animated' );
						}
					} );

					// fallback to 'bottom-in-view' offset, to make sure element become visible when it's on the bottom of page and other offsets are not triggered
					et_waypoint( $( '.et_pb_counter_container, .et-waypoint' ), {
						offset: 'bottom-in-view',
						handler: function() {
							$(this.element).addClass( 'et-animated' );
						}
					} );

					if ( $et_pb_circle_counter.length ) {
						$et_pb_circle_counter.each(function(){
							var $this_counter = $(this);
							if ( ! $this_counter.is( ':visible' ) ) {
								return;
							}
							et_waypoint( $this_counter, {
								offset: '65%',
								handler: function() {
									if ( $this_counter.data( 'PieChartHasLoaded' ) || typeof $this_counter.data('easyPieChart') === 'undefined' ) {
										return;
									}

									$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );

									$this_counter.data( 'PieChartHasLoaded', true );
								}
							});

							// fallback to 'bottom-in-view' offset, to make sure animation applied when element is on the bottom of page and other offsets are not triggered
							et_waypoint( $this_counter, {
								offset: 'bottom-in-view',
								handler: function() {
									if ( $this_counter.data( 'PieChartHasLoaded' ) || typeof $this_counter.data('easyPieChart') === 'undefined' ) {
										return;
									}

									$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );

									$this_counter.data( 'PieChartHasLoaded', true );
								}
							});
						});
					}

					if ( $et_pb_number_counter.length ) {
						$et_pb_number_counter.each(function(){
							var $this_counter = $(this);
							et_waypoint( $this_counter, {
								offset: '75%',
								handler: function() {
									$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );
								}
							});

							// fallback to 'bottom-in-view' offset, to make sure animation applied when element is on the bottom of page and other offsets are not triggered
							et_waypoint( $this_counter, {
								offset: 'bottom-in-view',
								handler: function() {
									$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );
								}
							});
						});
					}

					if ( $( '.et_pb_ab_goal' ).length ) {
						var $et_pb_ab_goal = $( '.et_pb_ab_goal' );

						et_waypoint( $et_pb_ab_goal, {
							offset: '80%',
							handler: function() {
								if ( et_pb_ab_logged_status['read_goal'] || ! $et_pb_ab_goal.length || ! $et_pb_ab_goal.visible( true ) ) {
									return;
								}

								// log the goal_read if goal is still visible after 3 seconds.
								setTimeout( function() {
									if ( $et_pb_ab_goal.length && $et_pb_ab_goal.visible( true ) && ! et_pb_ab_logged_status['read_goal'] ) {
										et_pb_ab_update_stats( 'read_goal' );
									}
								}, 3000 );

								et_pb_maybe_log_event( $et_pb_ab_goal, 'view_goal' );
							}
						});
					}
				} else {
					// if no waypoints supported then apply all the animations right away
					$( '.et_pb_counter_container, .et-waypoint' ).addClass( 'et-animated' );

					if ( $et_pb_circle_counter.length ) {
						$et_pb_circle_counter.each(function() {
							var $this_counter = $(this);

							if ( ! $this_counter.is( ':visible' ) ) {
								return;
							}

							if ( $this_counter.data( 'PieChartHasLoaded' ) ) {
								return;
							}

							$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );

							$this_counter.data( 'PieChartHasLoaded', true );
						} );
					}

					if ( $et_pb_number_counter.length ) {
						$et_pb_number_counter.each(function(){
							var $this_counter = $(this);

							$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );
						});
					}

					// log the stats without waypoints
					if ( $( '.et_pb_ab_goal' ).length ) {
						var $et_pb_ab_goal = $( '.et_pb_ab_goal' );

						if ( et_pb_ab_logged_status['read_goal'] || ! $et_pb_ab_goal.length || ! $et_pb_ab_goal.visible( true ) ) {
							return;
						}

						// log the goal_read if goal is still visible after 3 seconds.
						setTimeout( function() {
							if ( $et_pb_ab_goal.length && $et_pb_ab_goal.visible( true ) && ! et_pb_ab_logged_status['read_goal'] ) {
								et_pb_ab_update_stats( 'read_goal' );
							}
						}, 3000 );

						et_pb_maybe_log_event( $et_pb_ab_goal, 'view_goal' );
					}
				}

				if ( $et_pb_video_background.length ) {
					$et_pb_video_background.each( function(){
						var $this_video_background = $(this);

						et_pb_video_background_init( $this_video_background, this );
					});
				}
			}, 100 );

			function et_pb_init_ab_test() {
				var $et_pb_ab_goal = $( '.et_pb_ab_goal' ),
					et_ab_subject_id = et_pb_get_subject_id();

				$.each( et_pb_ab_logged_status, function( key, value ) {
					var cookie_subject = 'click_goal' === key || 'con_short' === key ? '' : et_ab_subject_id;

					et_pb_ab_logged_status[key] = et_pb_check_cookie_value( 'et_pb_ab_' + key + '_' + et_pb_custom.page_id + et_pb_custom.unique_test_id + cookie_subject, 'true' );

				});

				// log the page read event if user stays on page long enough and if not logged for current subject
				if ( ! et_pb_ab_logged_status['read_page'] ) {
					setTimeout( function() {
						et_pb_ab_update_stats( 'read_page' );
					}, et_pb_ab_bounce_rate );
				}

				// add the cookies for shortcode tracking, if enabled
				if ( 'on' === et_pb_custom.is_shortcode_tracking && ! et_pb_ab_logged_status['con_short'] ) {
					et_pb_set_cookie( 365, 'et_pb_ab_shortcode_track_' + et_pb_custom.page_id + '=' + et_pb_custom.page_id + '_' + et_pb_get_subject_id() + '_' + et_pb_custom.unique_test_id );
				}

				if ( $et_pb_ab_goal.length ) {
					// if goal is a module and has a button then track the conversions, otherwise track clicks
					if ( $et_pb_ab_goal.hasClass( 'et_pb_module' ) && ( $et_pb_ab_goal.hasClass( 'et_pb_button' ) || $et_pb_ab_goal.find( '.et_pb_button' ).length ) ) {
						// Log con_goal if current goal doesn't require any specific conversion calculation
						if ( ! $et_pb_ab_goal.hasClass( 'et_pb_contact_form_container' ) && ! $et_pb_ab_goal.hasClass( 'et_pb_newsletter' ) ) {
							var $goal_button = $et_pb_ab_goal.hasClass( 'et_pb_button' ) ? $et_pb_ab_goal : $et_pb_ab_goal.find( '.et_pb_button' );

							if ( $et_pb_ab_goal.hasClass( 'et_pb_comments_module' ) ) {
								var page_url = window.location.href,
									comment_submitted = -1 !== page_url.indexOf( '#comment-' ) ? true : false,
									log_conversion = et_pb_check_cookie_value( 'et_pb_ab_comment_log_' + et_pb_custom.page_id + et_pb_custom.unique_test_id, 'true' );

								if ( comment_submitted && log_conversion ) {
									et_pb_ab_update_stats( 'con_goal' );
									et_pb_set_cookie( 0, 'et_pb_ab_comment_log_' + et_pb_custom.page_id + et_pb_custom.unique_test_id + '=true' );
								}
							}

							$goal_button.click( function(){
								if ( $et_pb_ab_goal.hasClass( 'et_pb_comments_module' ) && ! et_pb_ab_logged_status['con_goal'] ) {
									et_pb_set_cookie( 365, 'et_pb_ab_comment_log_' + et_pb_custom.page_id + et_pb_custom.unique_test_id + '=true' );
									return;
								}

								et_pb_maybe_log_event( $et_pb_ab_goal, 'click_goal' );
							});
						}
					} else {
						$et_pb_ab_goal.click( function() {
							if ( $et_pb_ab_goal.hasClass( 'et_pb_shop' ) && ! et_pb_ab_logged_status['con_goal'] ) {
								et_pb_set_cookie( 365, 'et_pb_ab_shop_log=' + et_pb_custom.page_id + '_' + et_pb_get_subject_id() + '_' + et_pb_custom.unique_test_id );
							}

							et_pb_maybe_log_event( $et_pb_ab_goal, 'click_goal' );
						});
					}
				}
			}

			function et_pb_maybe_log_event( $goal_container, event ) {
				var log_event = typeof event === 'undefined' ? 'con_goal' : event;

				if ( ! $goal_container.hasClass( 'et_pb_ab_goal' ) || et_pb_ab_logged_status[ log_event ] ) {
					return;
				}

				// log the event if it's not logged for current user
				et_pb_ab_update_stats( log_event );
			}

			function et_pb_ab_update_stats( record_type, set_page_id, set_subject_id, set_test_id ) {
				var subject_id = typeof set_subject_id === 'undefined' ? et_pb_get_subject_id() : set_subject_id,
					page_id = typeof set_page_id === 'undefined' ? et_pb_custom.page_id : set_page_id,
					test_id = typeof set_test_id === 'undefined' ? et_pb_custom.unique_test_id : set_test_id,
					stats_data = JSON.stringify({ 'test_id' : page_id, 'subject_id' : subject_id, 'record_type' : record_type }),
					cookie_subject = 'click_goal' === record_type || 'con_short' === record_type ? '' : subject_id;

				et_pb_set_cookie( 365, 'et_pb_ab_' + record_type + '_' + page_id + test_id + cookie_subject + '=true' );

				et_pb_ab_logged_status[record_type] = true;

				$.ajax({
					type: 'POST',
					url: et_pb_custom.ajaxurl,
					data: {
						action : 'et_pb_update_stats_table',
						stats_data_array : stats_data,
						et_ab_log_nonce : et_pb_custom.et_ab_log_nonce
					}
				});
			}

			function et_pb_get_subject_id() {
				var $subject = $( '.et_pb_ab_subject' );

				// In case no subject found
				if ( $subject.length <= 0 ) {
					return false;
				}

				var subject_classname = $subject.attr( 'class' ),
					subject_id_raw = subject_classname.split( 'et_pb_ab_subject_id-' )[1],
					subject_id_clean = subject_id_raw.split( ' ' )[0],
					subject_id_separated = subject_id_clean.split( '_' ),
					subject_id = subject_id_separated[1];

				return subject_id;
			}

			function et_pb_set_cookie_expire( days ) {
				var ms = days*24*60*60*1000;

				var date = new Date();
				date.setTime( date.getTime() + ms );

				return "; expires=" + date.toUTCString();
			}

			function et_pb_check_cookie_value( cookie_name, value ) {
				return et_pb_get_cookie_value( cookie_name ) == value;
			}

			function et_pb_get_cookie_value( cookie_name ) {
				return et_pb_parse_cookies()[cookie_name];
			}

			function et_pb_parse_cookies() {
				var cookies = document.cookie.split( '; ' );

				var ret = {};
				for ( var i = cookies.length - 1; i >= 0; i-- ) {
				  var el = cookies[i].split( '=' );
				  ret[el[0]] = el[1];
				}
				return ret;
			}

			function et_pb_set_cookie( expire, cookie_content ) {
				cookie_expire = et_pb_set_cookie_expire( expire );
				document.cookie = cookie_content + cookie_expire + "; path=/";
			}

			function et_pb_get_fixed_main_header_height() {
				if ( ! window.et_is_fixed_nav ) {
					return 0;
				}

				var fixed_height_onload = typeof $('#main-header').attr('data-fixed-height-onload') === 'undefined' ? 0 : $('#main-header').attr('data-fixed-height-onload');

				return ! window.et_is_fixed_nav ? 0 : parseFloat( fixed_height_onload );
			}

			var fullscreen_section_width = {};
			var fullscreen_section_timeout = {};

			window.et_calc_fullscreen_section = function(event) {
				var isResizing = typeof event === 'object' && event.type === 'resize',
					$et_window = $(window),
					$this_section = $(this),
					section_index = $this_section.index('.et_pb_fullscreen'),
					timeout = isResizing && typeof fullscreen_section_width[section_index] !== 'undefined' && event.target.window_width > fullscreen_section_width[section_index] ? 800 : 0;

					fullscreen_section_width[section_index] = $et_window.width();

					if ( typeof fullscreen_section_timeout[section_index] !== 'undefined' ) {
						clearTimeout( fullscreen_section_timeout[section_index] );
					}

					fullscreen_section_timeout[section_index] = setTimeout( function() {
					var $body = $( 'body' ),
						this_section_index = $this_section.index('.et_pb_fullwidth_header'),
						this_section_offset = $this_section.offset(),
						$header = $this_section.children('.et_pb_fullwidth_header_container'),
						$header_content = $header.children('.header-content-container'),
						$header_image = $header.children('.header-image-container'),
						sectionHeight = window.innerHeight || $et_window.height(),
						$wpadminbar = $('#wpadminbar'),
						has_wpadminbar = $wpadminbar.length,
						wpadminbar_height = has_wpadminbar ? $wpadminbar.height() : 0,
						$top_header = $('#top-header'),
						has_top_header = $top_header.length,
						top_header_height = has_top_header ? $top_header.height() : 0,
						$main_header = $('#main-header'),
						has_main_header = $main_header.length,
						main_header_height = has_main_header ? $main_header.outerHeight() : 0,
						fixed_main_header_height = et_pb_get_fixed_main_header_height(),
						is_mobile_first_module = this_section_offset.top <= (main_header_height + wpadminbar_height),
						is_wp_relative_admin_bar = $et_window.width() < 782,
						is_desktop_view = $et_window.width() > 980,
						is_tablet_view = $et_window.width() <= 980 && $et_window.width() >= 479,
						is_phone_view = $et_window.width() < 479,
						overall_header_height = window.et_is_vertical_nav && is_desktop_view ? wpadminbar_height + top_header_height : wpadminbar_height + top_header_height + main_header_height,
						is_first_module = this_section_offset.top <= overall_header_height;

					// In case theme stored the onload main-header height as data-attribute
					if ( $main_header.attr('data-height-onload') ) {
						main_header_height = parseFloat( $main_header.attr('data-height-onload') );
					}

					/**
					 * WP Admin Bar:
					 *
					 * - Desktop fixed: standard
					 * - WP Mobile relative: less than 782px window
					**/
					if ( has_wpadminbar ) {
						if ( is_wp_relative_admin_bar ) {
							if ( is_first_module ) {
								sectionHeight -= wpadminbar_height;
							}
						} else {
							sectionHeight -= wpadminbar_height;
						}
					}

					/**
					 * Divi Top Header:
					 *
					 * - Desktop fixed: standard.
					 * - Desktop fixed BUT first header's height shouldn't be substracted: hide nav until scroll activated
					 * - Desktop relative: fixed nav bar disabled
					 * - Desktop relative: vertical nav activated
					 */
					if ( has_top_header ) {
						if ( is_desktop_view ) {
							if ( et_hide_nav && ! window.et_is_vertical_nav ) {
								if ( ! is_first_module ) {
									sectionHeight -= top_header_height;
								}
							} else if ( ! window.et_is_fixed_nav || window.et_is_vertical_nav ) {
								if ( is_first_module ) {
									sectionHeight -= top_header_height;
								}
							} else {
								sectionHeight -= top_header_height;
							}
						}
					}

					/**
					 * Divi Main Header:
					 *
					 * - Desktop fixed: standard. Initial and 'fixed' header might have different height
					 * - Desktop relative: fixed nav bar disabled
					 * - Desktop fixed BUT height should be ignored: vertical nav activated
					 * - Desktop fixed BUT height should be ignored for first header only: main header uses rgba
					 * - Desktop fixed BUT first header's height shouldn't be substracted: hide nav until scroll activated
					 * - Tablet relative: standard. Including vertical header style
					 * - Phone relative: standard. Including vertical header style
					 */
					if ( has_main_header ) {
						if ( is_desktop_view ) {
							if ( et_hide_nav && ! window.et_is_vertical_nav ) {
								if ( ! is_first_module ) {
									sectionHeight -= fixed_main_header_height;
								}
							} else if ( window.et_is_fixed_nav && ! window.et_is_vertical_nav ) {
								if ( is_first_module ) {
									sectionHeight -= main_header_height;
								} else {
									sectionHeight -= fixed_main_header_height;
								}
							} else if ( ! window.et_is_fixed_nav && ! window.et_is_vertical_nav ) {
								if ( is_first_module ) {
									sectionHeight -= main_header_height;
								}
							}
						} else {
							if ( is_first_module ) {
								sectionHeight -= main_header_height;
							}
						}
					}

					// If the transparent primary nav + hide nav until scroll is being used,
					// cancel automatic padding-top added by transparent nav mechanism
					if ( $body.hasClass('et_transparent_nav') && $body.hasClass( 'et_hide_nav' ) &&  0 === this_section_index ) {
						$this_section.css( 'padding-top', '' );
					}

					$this_section.css('min-height', sectionHeight + 'px' );
					$header.css('min-height', sectionHeight + 'px' );

					if ( $header.hasClass('center') && $header_content.hasClass('bottom') && $header_image.hasClass('bottom') ) {
						$header.addClass('bottom-bottom');
					}

					if ( $header.hasClass('center') && $header_content.hasClass('center') && $header_image.hasClass('center') ) {
						$header.addClass('center-center');
					}

					if ( $header.hasClass('center') && $header_content.hasClass('center') && $header_image.hasClass('bottom') ) {
						$header.addClass('center-bottom');

						var contentHeight = sectionHeight - $header_image.outerHeight( true );

						if ( contentHeight > 0 ) {
							$header_content.css('min-height', contentHeight + 'px' );
						}
					}

					if ( $header.hasClass('center') && $header_content.hasClass('bottom') && $header_image.hasClass('center') ) {
						$header.addClass('bottom-center');
					}

					if ( ( $header.hasClass('left') || $header.hasClass('right') ) && !$header_content.length && $header_image.length ) {
						$header.css('justify-content', 'flex-end');
					}

					if ( $header.hasClass('center') && $header_content.hasClass('bottom') && !$header_image.length ) {
						$header_content.find('.header-content').css( 'margin-bottom', 80 + 'px' );
					}

					if ( $header_content.hasClass('bottom') && $header_image.hasClass('center') ) {
						$header_image.find('.header-image').css( 'margin-bottom', 80 + 'px' );
						$header_image.css('align-self', 'flex-end');
					}

				}, timeout );
			}

			window.et_pb_parallax_init = function( $this_parallax ) {
				if ( $this_parallax.hasClass('et_pb_parallax_css') ) {
					return;
				}

				var $this_parent = $this_parallax.parent();

				$.proxy( et_parallax_set_height, $this_parent )();

				$.proxy( et_apply_parallax, $this_parent )();

				$et_window.on( 'scroll', $.proxy( et_apply_parallax, $this_parent ) );

				$et_window.on( 'resize', $.proxy( et_parallax_set_height, $this_parent ) );

				$et_window.on( 'resize', $.proxy( et_apply_parallax, $this_parent ) );

				$this_parent.find('.et-learn-more .heading-more').click( function() {
					setTimeout(function(){
						$.proxy( et_parallax_set_height, $this_parent )();
					}, 300 );
				});
			}

			$( window ).resize( function(){
				var window_width                = $et_window.width(),
					et_container_css_width      = $et_container.css( 'width' ),
					et_container_width_in_pixel = ( typeof et_container_css_width !== 'undefined' ) ? et_container_css_width.substr( -1, 1 ) !== '%' : '',
					et_container_actual_width   = ( et_container_width_in_pixel ) ? $et_container.width() : ( ( $et_container.width() / 100 ) * window_width ), // $et_container.width() doesn't recognize pixel or percentage unit. It's our duty to understand what it returns and convert it properly
					containerWidthChanged       = et_container_width !== et_container_actual_width;

				et_pb_resize_section_video_bg();
				et_pb_center_video();
				et_fix_slider_height();
				et_fix_nav_direction();

				$et_pb_fullwidth_portfolio.each(function(){
					set_container_height = $(this).hasClass('et_pb_fullwidth_portfolio_carousel') ? true : false;
					set_fullwidth_portfolio_columns( $(this), set_container_height );
				});

				if ( containerWidthChanged ) {
					$('.container-width-change-notify').trigger('containerWidthChanged');

					setTimeout( function() {
						$et_pb_filterable_portfolio.each(function(){
							set_filterable_grid_items( $(this) );
						});
						$et_pb_gallery.each(function(){
							if ( $(this).hasClass( 'et_pb_gallery_grid' ) ) {
								set_gallery_grid_items( $(this) );
							}
						});
					}, 100 );

					et_container_width = et_container_actual_width;

					etRecalculateOffset = true;

					if ( $et_pb_circle_counter.length ) {
						$et_pb_circle_counter.each(function(){
							var $this_counter = $(this);
							if ( ! $this_counter.is( ':visible' ) ) {
								return;
							}

							$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );
						});
					}
					if ( $et_pb_countdown_timer.length ) {
						$et_pb_countdown_timer.each(function(){
							var timer = $(this);
							et_countdown_timer_labels( timer );
						} );
					}
				}

				window.et_fix_testimonial_inner_width();

				et_audio_module_set();

				if ( $et_pb_counter_amount.length ) {
					$et_pb_counter_amount.each(function(){
						window.et_bar_counters_init( $( this ) );
					});
				} /* $et_pb_counter_amount.length */
			} );

			$( window ).ready( function(){
				if ( $.fn.fitVids ) {
					$( '.et_pb_slide_video' ).fitVids();
					$( '.et_pb_module' ).fitVids( { customSelector: "iframe[src^='http://www.hulu.com'], iframe[src^='http://www.dailymotion.com'], iframe[src^='http://www.funnyordie.com'], iframe[src^='https://embed-ssl.ted.com'], iframe[src^='http://embed.revision3.com'], iframe[src^='https://flickr.com'], iframe[src^='http://blip.tv'], iframe[src^='http://www.collegehumor.com']"} );
				}

				et_fix_video_wmode('.fluid-width-video-wrapper');

				et_fix_slider_height();

				// calculate fullscreen section sizes on $( window ).ready to avoid jumping in some cases
				$( 'section.et_pb_fullscreen' ).each( function(){
					var $this_section = $( this );

					$.proxy( et_calc_fullscreen_section, $this_section )();

					$et_window.on( 'resize', $.proxy( et_calc_fullscreen_section, $this_section ) );
				});
			} );

			window.et_pb_fullwidth_header_scroll = function( event ) {
				event.preventDefault();

				var window_width             = $et_window.width(),
					$body                    = $('body'),
					is_wp_relative_admin_bar = window_width < 782,
					is_transparent_main_header = $body.hasClass( 'et_transparent_nav' ),
					is_hide_nav              = $body.hasClass( 'et_hide_nav' ),
					is_desktop_view          = window_width > 980,
					is_tablet_view           = window_width <= 980 && window_width >= 479,
					is_phone_view            = window_width < 479,
					$this_section            = $(this).parents( 'section' ),
					this_section_offset      = $this_section.offset(),
					$wpadminbar              = $('#wpadminbar'),
					$main_header             = $('#main-header'),
					wpadminbar_height        = $wpadminbar.length && ! is_wp_relative_admin_bar ? $wpadminbar.height() : 0,
					top_header_height        = !window.et_is_fixed_nav || !is_desktop_view ? 0 : $top_header.height(),
					data_height_onload       = typeof $main_header.attr('data-height-onload') === 'undefined' ? 0 : $main_header.attr('data-height-onload');
					initial_fixed_difference = $main_header.height() === et_pb_get_fixed_main_header_height() || ! is_desktop_view || ! window.et_is_fixed_nav || is_transparent_main_header || is_hide_nav ? 0 : et_pb_get_fixed_main_header_height() - parseFloat( data_height_onload ),
					section_bottom           = ( this_section_offset.top + $this_section.outerHeight( true ) + initial_fixed_difference ) - ( wpadminbar_height + top_header_height + et_pb_get_fixed_main_header_height() ),
					animate_modified         = false;

				if ( $this_section.length ) {
					var fullscreen_scroll_duration = 800;

					$( 'html, body' ).animate( { scrollTop : section_bottom }, {
						duration: fullscreen_scroll_duration
					} );
				}
			}

			function et_pb_window_load_scripts() {
				et_fix_fullscreen_section();

				// recalculate fullscreen section sizes on load
				$( 'section.et_pb_fullscreen' ).each( function(){
					var $this_section = $( this );

					$.proxy( et_calc_fullscreen_section, $this_section )();
				});

				$( '.et_pb_fullwidth_header_scroll' ).on('click', 'a', et_pb_fullwidth_header_scroll );

				setTimeout( function() {
					$( '.et_pb_preload' ).removeClass( 'et_pb_preload' );
				}, 500 );

				if ( $.fn.hashchange ) {
					$(window).hashchange( function(){
						var hash = window.location.hash.substring(1);
						process_et_hashchange( hash );
					});
					$(window).hashchange();
				}

				if ( $et_pb_parallax.length && !et_is_mobile_device ) {
					$et_pb_parallax.each(function(){
						et_pb_parallax_init( $(this) );
					});
				}

				et_audio_module_set();

				window.et_reinit_waypoint_modules();

				if ( $( '.et_audio_content' ).length ) {
					$( window ).trigger( 'resize' );
				}
			}

			if ( window.et_load_event_fired ) {
				et_pb_window_load_scripts();
			} else {
				$( window ).load( function() {
					et_pb_window_load_scripts();
				} );
			}

			if ( $( '.et_section_specialty' ).length ) {
				$( '.et_section_specialty' ).each( function() {
					var this_row = $( this ).find( '.et_pb_row' );

					this_row.find( '>.et_pb_column:not(.et_pb_specialty_column)' ).addClass( 'et_pb_column_single' );
				});
			}

			/**
			* In particular browser, map + parallax doesn't play well due the use of CSS 3D transform
			*/
			if ( $('.et_pb_section_parallax').length && $('.et_pb_map').length ) {
				$('body').addClass( 'parallax-map-support' );
			}

			/**
			 * Add conditional class for search widget in sidebar module
			 */
			$('.et_pb_widget_area ' + et_pb_custom.widget_search_selector ).each( function() {
				var $search_wrap              = $(this),
					$search_input_submit      = $search_wrap.find('input[type="submit"]'),
					search_input_submit_text = $search_input_submit.attr( 'value' ),
					$search_button            = $search_wrap.find('button'),
					search_button_text       = $search_button.text(),
					has_submit_button         = $search_input_submit.length || $search_button.length ? true : false,
					min_column_width          = 150;

				if ( ! $search_wrap.find( 'input[type="text"]' ).length && ! $search_wrap.find( 'input[type="search"]' ).length ) {
					return;
				}

				// Mark no button state
				if ( ! has_submit_button ) {
					$search_wrap.addClass( 'et-no-submit-button' );
				}

				// Mark narrow state
				if ( $search_wrap.width() < 150 ) {
					$search_wrap.addClass( 'et-narrow-wrapper' );
				}

				// Fixes issue where theme's search button has no text: treat it as non-existent
				if ( $search_input_submit.length && ( typeof search_input_submit_text == 'undefined' || search_input_submit_text === '' ) ) {
					$search_input_submit.remove();
					$search_wrap.addClass( 'et-no-submit-button' );
				}

				if ( $search_button.length && ( typeof search_button_text == 'undefined' || search_button_text === '' ) ) {
					$search_button.remove();
					$search_wrap.addClass( 'et-no-submit-button' );
				}

			} );

			window.et_pb_search_init = function( $search ) {
				var $input_field = $search.find( '.et_pb_s' ),
					$button = $search.find( '.et_pb_searchsubmit' ),
					input_padding = $search.hasClass( 'et_pb_text_align_right' ) ? 'paddingLeft' : 'paddingRight',
					disabled_button = $search.hasClass( 'et_pb_hide_search_button' );

				// set the relative button position to get its height correctly
				$button.css( { 'position' : 'relative' } );

				if ( $button.innerHeight() > $input_field.innerHeight() ) {
					$input_field.height( $button.innerHeight() );
				}

				if ( ! disabled_button ) {
					$input_field.css( input_padding, $button.innerWidth() + 10 );
				}

				// reset the button position back to default
				$button.css( { 'position' : '' } );
			}

			/**
			 * Fix search module which has percentage based custom margin
			 */
			window.et_pb_search_percentage_custom_margin_fix = function( $search ) {
				var inputMargin = $search.find( '.et_pb_s' ).css( 'margin' ).split(' ');
				var inputMarginObj = {};

				switch(inputMargin.length) {
					case 4:
						inputMarginObj = {
							top: inputMargin[0],
							right: inputMargin[1],
							bottom: inputMargin[2],
							left: inputMargin[3],
						};
						break;
					case 2:
						inputMarginObj = {
							top: inputMargin[0],
							right: inputMargin[1],
							bottom: inputMargin[0],
							left: inputMargin[1],
						};
						break;
					default:
						inputMarginObj = {
							top: inputMargin[0],
							right: inputMargin[0],
							bottom: inputMargin[0],
							left: inputMargin[0],
						};
						break;
				}

				var inputRight = 0 - parseFloat(inputMarginObj.left) + 'px';

				$search.find('.et_pb_searchsubmit').css({
					top: inputMarginObj.top,
					right: inputRight,
					bottom: inputMarginObj.bottom,
				});
			}

			if ( $( '.et_pb_search' ).length ) {
				$( '.et_pb_search' ).each( function() {
					var $search = $(this);

					if ( $search.is( '.et_pb_search_percentage_custom_margin' ) ) {
						et_pb_search_percentage_custom_margin_fix( $search );
					}

					et_pb_search_init( $search );
				});
			}

			window.et_pb_comments_init = function( $comments_module ) {
				var $comments_module_button = $comments_module.find( '.comment-reply-link, .submit' );

				if ( $comments_module_button.length ) {
					$comments_module_button.addClass( 'et_pb_button' );

					if ( typeof $comments_module.attr( 'data-icon' ) !== 'undefined' && $comments_module.attr( 'data-icon' ) !== '' ) {
						$comments_module_button.attr( 'data-icon', $comments_module.attr( 'data-icon' ) );
						$comments_module_button.addClass( 'et_pb_custom_button_icon' );
					}
				}
			};

			// apply required classes for the Reply buttons in Comments Module
			if ( $( '.et_pb_comments_module' ).length ) {
				$( '.et_pb_comments_module' ).each( function() {
					var $comments_module = $( this );

					et_pb_comments_init( $comments_module );
				});
			}

			window.et_fix_pricing_currency_position();

			$('.et_pb_contact_form_container').each( function() {
				var $form = $(this);

				/* Listen for any field change */
				$form.on( 'change', 'input, textarea, select', function() {
					et_conditional_check( $form );
				} );

				// Conditions may be satisfied on default form state
				et_conditional_check( $form );
			} );

			function et_conditional_check( $form ) {
				var $conditionals = $form.find('[data-conditional-logic]');

				/* Upon change loop all the fields that have conditional logic */
				$conditionals
					.hide()
					.each( function() {
						var $conditional = $(this);

						/* jQuery automatically parses the JSON */
						var rules    = $conditional.data('conditional-logic');
						var relation = $conditional.data('conditional-relation');

						show_field = false;

						/* Loop all the conditional logic rules */
						var matched_rules = [];

						for ( var i = 0; i < rules.length; i++ ) {
							var ruleset     = rules[i];
							var check_id    = ruleset[0];
							var check_type  = ruleset[1];
							var check_value = ruleset[2];
							var $wrapper    = $form.find('.et_pb_contact_field[data-id="' + check_id + '"]');
							var field_id    = $wrapper.data('id');
							var field_type  = $wrapper.data('type');
							var field_value;

							/* Get the proper compare value based on the field type */
							switch( field_type ) {
								case 'input':
								case 'email':
									field_value = $wrapper.find('input').val();
									break;
								case 'text':
									field_value = $wrapper.find('textarea').val();
									break;
								case 'radio':
									field_value = $wrapper.find('input:checked').val() || '';
									break;
								case 'checkbox':
									var $checkbox      = $wrapper.find(':checkbox');
									var $checkbox_data = $wrapper.find('[data-checked][data-unchecked]');

									field_value = true === $checkbox.prop('checked') ? $checkbox_data.data('checked') : $checkbox_data.data('unchecked');
									break;
								case 'select':
									field_value = $wrapper.find('select').val();
									break;
							}

							/*
								'is empty' / 'is not empty' are comparing against an empty value so simply
								reset the `check_value` and update the condition to 'is' / 'is not'
							*/
							if ( 'is empty' === check_type || 'is not empty' === check_type ) {
								check_type  = 'is empty' === check_type ? 'is' : 'is not';
								check_value = '';
							}

							/* Check if the value IS matching (if it has to) */
							if ( 'is' === check_type && field_value !== check_value ) {
								continue;
							}

							/* Check if the value IS NOT matching (if it has to) */
							if ( 'is not' === check_type && field_value === check_value ) {
								continue;
							}

							/* Create the contains/not contains regular expresion */
							var containsRegExp = new RegExp( check_value, 'i' );

							/* Check if the value IS containing */
							if ( 'contains' === check_type && ! field_value.match( containsRegExp ) ) {
								continue;
							}

							/* Check if the value IS NOT containing */
							if ( 'does not contain' === check_type && field_value.match( containsRegExp ) ) {
								continue;
							}

							/* Prepare the values for the 'is greater than' / 'is less than' check */
							var maybeNumericValue       = parseInt( field_value );
							var maybeNumbericCheckValue = parseInt( check_value );

							if (
								( 'is greater' === check_type || 'is less' === check_type ) &&
								( isNaN( maybeNumericValue ) || isNaN( maybeNumbericCheckValue ) )
							) {
								continue;
							}

							/* Check if the value is greater than */
							if ( 'is greater' === check_type && maybeNumericValue <= maybeNumbericCheckValue) {
								continue;
							}

							/* Check if the value is less than */
							if ( 'is less' === check_type && maybeNumericValue >= maybeNumbericCheckValue) {
								continue;
							}

							matched_rules.push( true );
						}

						// Hide all the conditional fields initially
						$conditional.hide();

						/*
							Input fields may have HTML5 pattern validation which must be ignored
							if the field is not visible. In order for the pattern to not be
							taken into account the field must have novalidate property and
							to not be required (or to not have a pattern attribute)
						*/
						var $conditional_input  = $conditional.find('input[type="text"]');
						var conditional_pattern = $conditional_input.attr('pattern');

						$conditional_input.attr('novalidate', 'novalidate');
						$conditional_input.attr('data-pattern', conditional_pattern);
						$conditional_input.removeAttr('pattern');

						if ( 'all' === relation && rules.length === matched_rules.length ) {
							$conditional.show();
							$conditional_input.removeAttr('novalidate');
							$conditional_input.attr('pattern', $conditional_input.data('pattern'));
						}

						if ( 'any' === relation && 0 < matched_rules.length ) {
							$conditional.show();
							$conditional_input.removeAttr('novalidate');
							$conditional_input.attr('pattern', $conditional_input.data('pattern'));
						}
					} );
			}

			/**
			 * Provide event listener for plugins to hook up to
			 */
			$(document).trigger('et_pb_after_init_modules');
		});
	}

	// Modification of underscore's _.debounce()
	// Underscore.js 1.8.3
	// http://underscorejs.org
	// (c) 2009-2015 Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
	// Underscore may be freely distributed under the MIT license.
	window.et_pb_debounce = function(func, wait, immediate) {
		var timeout, args, context, timestamp, result;

		var now = Date.now || new Date().getTime();

		var later = function() {
			var last = now - timestamp;

			if (last < wait && last >= 0) {
				timeout = setTimeout(later, wait - last);
			} else {
				timeout = null;
				if (!immediate) {
					result = func.apply(context, args);
					if (!timeout) context = args = null;
				}
			}
		};

		return function() {
			context = this;
			args = arguments;
			timestamp = now;
			var callNow = immediate && !timeout;
			if (!timeout) timeout = setTimeout(later, wait);
			if (callNow) {
				result = func.apply(context, args);
				context = args = null;
			}

			return result;
		};
	};

	if ( et_pb_custom.is_ab_testing_active && 'yes' === et_pb_custom.is_cache_plugin_active ) {
		// update the window.et_load_event_fired variable to initiate the scripts properly
		$( window ).load( function() {
			window.et_load_event_fired = true;
		});

		// get the subject id for current visitor and display it
		// this ajax request performed only if split testing is enabled and cache plugin active
		$.ajax( {
			type: "POST",
			url: et_pb_custom.ajaxurl,
			dataType: "json",
			data:
			{
				action : 'et_pb_ab_get_subject_id',
				et_frontend_nonce : et_pb_custom.et_frontend_nonce,
				et_pb_ab_test_id : et_pb_custom.page_id
			},
			success: function( subject_data ) {
				if ( subject_data ) {
					// append the subject content to appropriate placeholder
					$( '.et_pb_subject_placeholder_id_' + subject_data.id ).after( subject_data.content );
					// remove all other placeholders from the DOM
					$( '.et_pb_subject_placeholder' ).remove();

					// init all scripts once the subject loaded
					window.et_pb_init_modules();
					$( 'body' ).trigger( 'et_pb_ab_subject_ready' );
				}
			}
		});
	} else {
		window.et_pb_init_modules();
	}
})(jQuery)
