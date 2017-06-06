(function($){
	window.et_load_event_fired = false;

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
					et_pb_init_modules();
					$( 'body' ).trigger( 'et_pb_ab_subject_ready' );
				}
			}
		});
	} else {
		et_pb_init_modules();
	}

	function et_pb_init_modules() {
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

					$et_slider_next.click( function(){
						if ( $et_slider.et_animation_running )	return false;

						$et_slider.et_slider_move_to( 'next' );

						return false;
					} );

					$et_slider_prev.click( function(){
						if ( $et_slider.et_animation_running )	return false;

						$et_slider.et_slider_move_to( 'previous' );

						return false;
					} );

					// swipe support requires et-jquery-touch-mobile
					$et_slider.find( settings.slide ).on( 'swipeleft', function() {
						$et_slider.et_slider_move_to( 'next' );
					});
					$et_slider.find( settings.slide ).on( 'swiperight', function() {
						$et_slider.et_slider_move_to( 'previous' );
					});
				}

				if ( settings.use_controls && et_slides_number > 1 ) {
					for ( var i = 1; i <= et_slides_number; i++ ) {
						controls_html += '<a href="#"' + ( i == 1 ? ' class="' + settings.control_active_class + '"' : '' ) + '>' + i + '</a>';
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

					$et_slider_controls.click( function(){
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
					$et_slider_carousel_controls.click( function(){
						if ( $et_slider.et_animation_running )	return false;

						var $this = $(this);
						$et_slider.et_slider_move_to( $this.data('slide-id') );

						return false;
					} );
				}

				if ( settings.slideshow && et_slides_number > 1 ) {
					$et_slider.hover( function() {
						if ( $et_slider.hasClass( 'et_slider_auto_ignore_hover' ) ) {
							return;
						}

						$et_slider.addClass( 'et_slider_hovered' );

						if ( typeof et_slider_timer != 'undefined' ) {
							clearInterval( et_slider_timer );
						}
					}, function() {
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

				function et_fix_slider_content_images() {
					var $this_slider           = $et_slider,
						$slide_image_container = $this_slider.find( '.et-pb-active-slide .et_pb_slide_image' );
						$slide_video_container = $this_slider.find( '.et-pb-active-slide .et_pb_slide_video' );
						$slide                 = $slide_image_container.closest( '.et_pb_slide' ),
						$slider                = $slide.closest( '.et_pb_slider' ),
						slide_height           = $slider.innerHeight(),
						image_height           = parseInt( slide_height * 0.8 ),
						$top_header 		   = $('#top-header'),
						$main_header		   = $('#main-header'),
						$et_transparent_nav    = $( '.et_transparent_nav' ),
						$et_vertical_nav 	   = $('.et_vertical_nav');

					$slide_image_container.find( 'img' ).css( 'maxHeight', image_height + 'px' );

					if ( $slide.hasClass( 'et_pb_media_alignment_center' ) ) {
						$slide_image_container.css( 'marginTop', '-' + parseInt( $slide_image_container.height() / 2 ) + 'px' );
					}

					$slide_video_container.css( 'marginTop', '-' + parseInt( $slide_video_container.height() / 2 ) + 'px' );

					$slide_image_container.find( 'img' ).addClass( 'active' );
				}

				function et_get_bg_layout_color( $slide ) {
					if ( $slide.hasClass( 'et_pb_bg_layout_dark' ) ) {
						return 'et_pb_bg_layout_dark';
					}

					return 'et_pb_bg_layout_light';
				}

				function et_maybe_set_controls_color( $slide ) {
					var next_slide_dot_color,
						$arrows,
						arrows_color;

					if ( typeof $et_slider_controls !== 'undefined' && $et_slider_controls.length ) {
						next_slide_dot_color = $slide.data( 'dots_color' ) || '';

						if ( next_slide_dot_color !== '' ) {
							$et_slider_controls.attr( 'style', 'background-color: ' + hex_to_rgba( next_slide_dot_color, '0.3' ) + ';' )
							$et_slider_controls.filter( '.et-pb-active-control' ).attr( 'style', 'background-color: ' + hex_to_rgba( next_slide_dot_color ) + '!important;' );
						} else {
							$et_slider_controls.removeAttr( 'style' );
						}
					}

					if ( typeof $et_slider_arrows !== 'undefined' && $et_slider_arrows.length ) {
						$arrows      = $et_slider_arrows.find( 'a' );
						arrows_color = $slide.data( 'arrows_color' ) || '';

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
							window.et_reinint_waypoint_modules();
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
					et_fix_slider_content_images();
				} else {
					$et_window.load( function() {
						et_fix_slider_content_images();
					} );
				}

				$et_window.resize( function() {
					et_fix_slider_content_images();
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

					$et_slider.trigger( 'simple_slider_before_move_to', { direction : direction, next_slide : $next_slide });

					$et_slide.each( function(){
						$(this).css( 'zIndex', 1 );
					} );
					$active_slide.css( 'zIndex', 2 ).removeClass( 'et-pb-active-slide' ).addClass('et-pb-moved-slide');
					$next_slide.css( { 'display' : 'block', opacity : 0 } ).addClass( 'et-pb-active-slide' );

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

					et_slider_auto_rotate();
				}
		}

		$.fn.et_pb_simple_slider = function( options ) {
			return this.each(function() {
				new $.et_pb_simple_slider(this, options);
			});
		}

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

			$et_carousel_next.click( function(){
				if ( $et_carousel.et_animation_running ) return false;

				$et_carousel.et_carousel_move_to( 'next' );

				return false;
			} );

			$et_carousel_prev.click( function(){
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
		}

		$.fn.et_pb_simple_carousel = function( options ) {
			return this.each(function() {
				new $.et_pb_simple_carousel(this, options);
			});
		}

		var $et_pb_slider  = $( '.et_pb_slider' ),
			$et_pb_tabs    = $( '.et_pb_tabs' ),
			$et_pb_tabs_li = $et_pb_tabs.find( '.et_pb_tabs_controls li' ),
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
			et_is_fixed_nav = $( 'body' ).hasClass( 'et_fixed_nav' ),
			et_is_vertical_nav = $( 'body' ).hasClass( 'et_vertical_nav' ),
			et_is_vertical_fixed_nav = $( 'body' ).hasClass( 'et_vertical_fixed' ),
			et_is_rtl = $( 'body' ).hasClass( 'rtl' ),
			et_hide_nav = $( 'body' ).hasClass( 'et_hide_nav' ),
			et_header_style_left = $( 'body' ).hasClass( 'et_header_style_left' ),
			et_vertical_navigation = $( 'body' ).hasClass( 'et_vertical_nav' ),
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
			$et_window = $(window),
			etRecalculateOffset = false,
			et_header_height,
			et_header_modifier,
			et_header_offset,
			et_primary_header_top,
			$et_vertical_nav = $('.et_vertical_nav'),
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

		$(document).ready( function(){
			var $et_top_menu = $et_menu_selector,
				et_parent_menu_longpress_limit = 300,
				et_parent_menu_longpress_start,
				et_parent_menu_click = true,
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
							column_type_clean = column_type.split( ' ', 1 )[0],
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

			if ( $et_pb_video_section.length ) {
				$et_pb_video_section.find( 'video' ).mediaelementplayer( {
					pauseOtherPlayers: false,
					success : function( mediaElement, domObject ) {
						mediaElement.addEventListener( 'loadeddata', function() {
							et_pb_resize_section_video_bg( $(domObject) );
							et_pb_center_video( $(domObject) );
						}, false );

						mediaElement.addEventListener( 'canplay', function() {
							$(domObject).closest( '.et_pb_preload' ).removeClass( 'et_pb_preload' );
						}, false );
					}
				} );
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
							enabled: true,
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

			if ( $et_lightbox_image.length ) {
				// prevent attaching of any further actions on click
				$et_lightbox_image.unbind( 'click' );
				$et_lightbox_image.bind( 'click' );

				$et_lightbox_image.magnificPopup( {
					type: 'image',
					removalDelay: 500,
					mainClass: 'mfp-fade',
					zoom: {
						enabled: true,
						duration: 500,
						opener: function(element) {
							return element.find('img');
						}
					}
				} );
			}

			if ( $et_pb_slider.length ) {
				$et_pb_slider.each( function() {
					var $this_slider = $(this),
						et_slider_settings = {
							fade_speed 		: 700,
							slide			: ! $this_slider.hasClass( 'et_pb_gallery' ) ? '.et_pb_slide' : '.et_pb_gallery_item'
						}

					if ( $this_slider.hasClass('et_pb_slider_no_arrows') )
						et_slider_settings.use_arrows = false;

					if ( $this_slider.hasClass('et_pb_slider_no_pagination') )
						et_slider_settings.use_controls = false;

					if ( $this_slider.hasClass('et_slider_auto') ) {
						var et_slider_autospeed_class_value = /et_slider_speed_(\d+)/g;

						et_slider_settings.slideshow = true;

						et_slider_autospeed = et_slider_autospeed_class_value.exec( $this_slider.attr('class') );

						et_slider_settings.slideshow_speed = et_slider_autospeed[1];
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

				} );
			}

			$et_pb_carousel  = $( '.et_pb_carousel' );
			if ( $et_pb_carousel.length ) {
				$et_pb_carousel.each( function() {
					var $this_carousel = $(this),
						et_carousel_settings = {
							fade_speed 		: 1000
						};

					$this_carousel.et_pb_simple_carousel( et_carousel_settings );
				} );
			}

			if ( $et_pb_fullwidth_portfolio.length ) {

				function set_fullwidth_portfolio_columns( $the_portfolio, carousel_mode ) {
					var columns,
						$portfolio_items = $the_portfolio.find('.et_pb_portfolio_items'),
						portfolio_items_width = $portfolio_items.width(),
						$the_portfolio_items = $portfolio_items.find('.et_pb_portfolio_item'),
						portfolio_item_count = $the_portfolio_items.length;

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
							$carousel.find('.et-pb-arrow-next').click();
						}, $carousel.data('auto-rotate-speed') );

						$carousel.data('et_carousel_timer', et_carousel_timer);
					}
				}

				$et_pb_fullwidth_portfolio.each(function(){
					var $the_portfolio = $(this),
						$portfolio_items = $the_portfolio.find('.et_pb_portfolio_items');

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
							var $the_portfolio = $(this).parents('.et_pb_fullwidth_portfolio'),
								$portfolio_items = $the_portfolio.find('.et_pb_portfolio_items'),
								$the_portfolio_items = $portfolio_items.find('.et_pb_portfolio_item'),
								$active_carousel_group = $portfolio_items.find('.et_pb_carousel_group.active'),
								slide_duration = 700,
								items = $portfolio_items.data('items'),
								columns = $portfolio_items.data('portfolio-columns'),
								item_width = $active_carousel_group.innerWidth() / columns, //$active_carousel_group.children().first().innerWidth(),
								original_item_width = ( 100 / columns ) + '%';

							e.preventDefault();

							if ( $the_portfolio.data('carouseling') ) {
								return;
							}

							$the_portfolio.data('carouseling', true);

							$active_carousel_group.children().each(function(){
								$(this).css({'width': $(this).innerWidth() + 1, 'position':'absolute', 'left': ( $(this).innerWidth() * ( $(this).data('position') - 1 ) ) });
							});

							if ( $(this).hasClass('et-pb-arrow-next') ) {
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

							return false;
						});

					} else {
						// setup fullwidth portfolio grid
						set_fullwidth_portfolio_columns( $the_portfolio, false );
					}

				});
			}

			function et_audio_module_set() {
				if ( $( '.et_pb_audio_module .mejs-audio' ).length || $( '.et_audio_content .mejs-audio' ).length ) {
					$( '.et_audio_container' ).each( function(){
						var $this_player = $( this ),
							$time_rail = $this_player.find( '.mejs-time-rail' ),
							$time_slider = $this_player.find( '.mejs-time-slider' );
						// remove previously added width and min-width attributes to calculate the new sizes accurately
						$time_rail.removeAttr( 'style' );
						$time_slider.removeAttr( 'style' );

						var $count_timer = $this_player.find( 'div.mejs-currenttime-container' ),
							player_width = $this_player.width(),
							controls_play_width = $this_player.find( '.mejs-play' ).outerWidth(),
							time_width = $this_player.find( '.mejs-currenttime-container' ).outerWidth(),
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
					});
				}
			}

			if ( $('.et_pb_section_video').length ) {
				window._wpmejsSettings.pauseOtherPlayers = false;
			}

			if ( $et_pb_filterable_portfolio.length ) {

				function et_pb_filterable_portfolio_init() {
					$et_pb_filterable_portfolio.each(function(){
						var $the_portfolio = $(this),
							$the_portfolio_items = $the_portfolio.find('.et_pb_portfolio_items'),
							$left_orientatation = true == $the_portfolio.data( 'rtl' ) ? false : true;

						$the_portfolio.show();

						set_filterable_grid_items( $the_portfolio );

						$the_portfolio.on('click', '.et_pb_portfolio_filter a', function(e){
							e.preventDefault();
							var category_slug = $(this).data('category-slug');
							$the_portfolio_items = $(this).parents('.et_pb_filterable_portfolio').find('.et_pb_portfolio_items');

							if ( 'all' == category_slug ) {
								$the_portfolio.find('.et_pb_portfolio_filter a').removeClass('active');
								$the_portfolio.find('.et_pb_portfolio_filter_all a').addClass('active');
								$the_portfolio.find('.et_pb_portfolio_item').removeClass('active inactive');
								$the_portfolio.find('.et_pb_portfolio_item').show();
								$the_portfolio.find('.et_pb_portfolio_item').addClass('active');
							} else {
								$the_portfolio.find('.et_pb_portfolio_filter_all').removeClass('active');
								$the_portfolio.find('.et_pb_portfolio_filter a').removeClass('active');
								$the_portfolio.find('.et_pb_portfolio_filter_all a').removeClass('active');
								$(this).addClass('active');

								$the_portfolio_items.find('.et_pb_portfolio_item').hide();
								$the_portfolio_items.find('.et_pb_portfolio_item').addClass( 'inactive' );
								$the_portfolio_items.find('.et_pb_portfolio_item').removeClass('active');
								$the_portfolio_items.find('.et_pb_portfolio_item.project_category_' + $(this).data('category-slug') ).show();
								$the_portfolio_items.find('.et_pb_portfolio_item.project_category_' + $(this).data('category-slug') ).addClass('active').removeClass( 'inactive' );
							}

							set_filterable_grid_items( $the_portfolio );
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
					var active_category = $the_portfolio.find('.et_pb_portfolio_filter > a.active').data('category-slug'),
						container_width = $the_portfolio.find( '.et_pb_portfolio_items' ).innerWidth(),
						item_width = $the_portfolio.find( '.et_pb_portfolio_item' ).outerWidth( true ),
						last_item_margin = item_width - $the_portfolio.find( '.et_pb_portfolio_item' ).outerWidth(),
						columns_count = Math.round( ( container_width + last_item_margin ) / item_width ),
						counter = 1,
						first_in_row = 1;

						$the_portfolio.find( '.et_pb_portfolio_item' ).removeClass( 'last_in_row first_in_row' );
						$the_portfolio.find( '.et_pb_portfolio_item' ).each( function() {
							var $this_el = $( this );

							if ( ! $this_el.hasClass( 'inactive' ) ) {
								if ( first_in_row === counter ) {
									$this_el.addClass( 'first_in_row' );
								}

								if ( 0 === counter % columns_count ) {
									$this_el.addClass( 'last_in_row' );
									first_in_row = counter + 1;
								}
								counter++;
							}
						});

					if ( 'all' === active_category ) {
						$the_portfolio_visible_items = $the_portfolio.find('.et_pb_portfolio_item');
					} else {
						$the_portfolio_visible_items = $the_portfolio.find('.et_pb_portfolio_item.project_category_' + active_category);
					}

					var visible_grid_items = $the_portfolio_visible_items.length,
						posts_number = $the_portfolio.data('posts-number'),
						pages = Math.ceil( visible_grid_items / posts_number );

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

				$et_pb_filterable_portfolio.on('click', '.et_pb_portofolio_pagination a', function(e){
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

					setTimeout(function(){
						set_filterable_portfolio_hash( $the_portfolio );
					}, 500 );
				});

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

			if ( $et_pb_gallery.length ) {

				function set_gallery_grid_items( $the_gallery ) {
					var $the_gallery_items_container = $the_gallery.find('.et_pb_gallery_items'),
						$the_gallery_items = $the_gallery_items_container.find('.et_pb_gallery_item');

					var total_grid_items = $the_gallery_items.length,
						posts_number = $the_gallery_items_container.data('per_page'),
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

				function set_gallery_grid_pages( $the_gallery, pages ) {
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

				function set_gallery_hash( $the_gallery ) {

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

				$et_pb_gallery.each(function(){
					var $the_gallery = $(this);

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

				});

				$et_pb_gallery.data('paginating', false );
				$et_pb_gallery.on('click', '.et_pb_gallery_pagination a', function(e){
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

					setTimeout(function(){
						set_gallery_hash( $the_gallery );
					}, 100 );

					$( 'html, body' ).animate( { scrollTop : $the_gallery.offset().top - 200 }, 200 );
				});

			} /*  end if ( $et_pb_gallery.length ) */

			if ( $et_pb_counter_amount.length ) {
				$et_pb_counter_amount.each(function(){
					var $bar_item           = $(this),
						bar_item_width      = $bar_item.attr( 'data-width' ),
						bar_item_padding    = Math.ceil( parseFloat( $bar_item.css('paddingLeft') ) ) + Math.ceil( parseFloat( $bar_item.css('paddingRight') ) ),
						$bar_item_text      = $bar_item.children( '.et_pb_counter_amount_number' ),
						bar_item_text_width = $bar_item_text.width() + bar_item_padding;

					$bar_item.css({
						'width' : bar_item_width,
						'min-width' : bar_item_text_width
					});
				});
			} /* $et_pb_counter_amount.length */

			function et_countdown_timer( timer ) {
				var end_date = parseInt( timer.data( 'end-timestamp') ),
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

				if ( days == 0 ) {
					if ( !timer.find('.days > .value').parent('.section').hasClass('zero') ) {
						timer.find('.days > .value').html( '000' ).parent('.section').addClass('zero').next().addClass('zero');
					}
				} else {
					days_slice = days.toString().length >= 3 ? days.toString().length : 3;
					timer.find('.days > .value').html( ('000' + days).slice(-days_slice) );
				}

				if ( days == 0 && hours == 0 ) {
					if ( !timer.find('.hours > .value').parent('.section').hasClass('zero') ) {
						timer.find('.hours > .value').html('00').parent('.section').addClass('zero').next().addClass('zero');
					}
				} else {
					timer.find('.hours > .value').html( ( '0' + hours ).slice(-2) );
				}

				if ( days == 0 && hours == 0 && minutes == 0 ) {
					if ( !timer.find('.minutes > .value').parent('.section').hasClass('zero') ) {
						timer.find('.minutes > .value').html('00').parent('.section').addClass('zero').next().addClass('zero');
					}
				} else {
					timer.find('.minutes > .value').html( ( '0' + minutes ).slice(-2) );
				}

				if ( days == 0 && hours == 0 && minutes == 0 && seconds == 0 ) {
					if ( !timer.find('.seconds > .value').parent('.section').hasClass('zero') ) {
						timer.find('.seconds > .value').html('00').parent('.section').addClass('zero');
					}
				} else {
					timer.find('.seconds > .value').html( ( '0' + seconds ).slice(-2) );
				}
			}

			function et_countdown_timer_labels( timer ) {
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

			if ( $et_pb_countdown_timer.length ) {
				$et_pb_countdown_timer.each(function(){
					var timer = $(this);
					et_countdown_timer_labels( timer );
					et_countdown_timer( timer );
					setInterval(function(){
						et_countdown_timer( timer );
					}, 1000);
				});
			}

			if ( $et_pb_tabs.length ) {
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
			}

			if ( $et_pb_map.length ) {
				function et_pb_init_maps() {
					$et_pb_map.each(function(){
						var $this_map_container = $(this),
							$this_map = $this_map_container.children('.et_pb_map'),
							this_map_grayscale = $this_map_container.data( 'grayscale' ) || 0,
							is_draggable = ( et_is_mobile_device && $this_map.data('mobile-dragging') !== 'off' ) || ! et_is_mobile_device,
							infowindow_active;

						if ( this_map_grayscale !== 0 ) {
							this_map_grayscale = '-' + this_map_grayscale.toString();
						}

						$this_map_container.data('map', new google.maps.Map( $this_map[0], {
							zoom: parseInt( $this_map.data('zoom') ),
							center: new google.maps.LatLng( parseFloat( $this_map.data('center-lat') ) , parseFloat( $this_map.data('center-lng') )),
							mapTypeId: google.maps.MapTypeId.ROADMAP,
							scrollwheel: $this_map.data('mouse-wheel') == 'on' ? true : false,
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
								position: new google.maps.LatLng( parseFloat( $this_marker.data('lat') ) , parseFloat( $this_marker.data('lng') ) ),
								map: $this_map_container.data('map'),
								title: $this_marker.data('title'),
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
					});
				}

				if ( window.et_load_event_fired ) {
					et_pb_init_maps();
				} else {
					google.maps.event.addDomListener(window, 'load', function() {
						et_pb_init_maps();
					} );
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

			if ( $et_pb_circle_counter.length ) {

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

			if ( $et_pb_number_counter.length ) {
				window.et_pb_reinit_number_counters = function( $et_pb_number_counter ) {
					if ( $.fn.fitText ) {
						$et_pb_number_counter.find( '.percent p' ).fitText( 0.3 );
					}

					$et_pb_number_counter.each(function(){
						var $this_counter = $(this);
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
									$(this.el).find('.percent-value').text( Math.round( parseInt( percent ) ) );
							},
							onStop: function(from, to) {
								$(this.el).find('.percent-value').text( $(this.el).data('number-value') );
							}
						});
					});
				}
				window.et_pb_reinit_number_counters( $et_pb_number_counter );
			}

			function et_apply_parallax() {
				var $this = $(this),
					element_top = $this.offset().top,
					window_top = $et_window.scrollTop(),
					y_pos = ( ( ( window_top + $et_window.height() ) - element_top ) * 0.3 ),
					main_position;

				main_position = 'translate(0, ' + y_pos + 'px)';

				$this.find('.et_parallax_bg').css( {
					'-webkit-transform' : main_position,
					'-moz-transform'    : main_position,
					'-ms-transform'     : main_position,
					'transform'         : main_position
				} );
			}

			function et_parallax_set_height() {
				var $this = $(this),
					bg_height;

				bg_height = ( $et_window.height() * 0.3 + $this.innerHeight() );

				$this.find('.et_parallax_bg').css( { 'height' : bg_height } );
			}

			$('.et_pb_toggle_title').click( function(){
				var $this_heading         = $(this),
					$module               = $this_heading.closest('.et_pb_toggle'),
					$section              = $module.parents( '.et_pb_section' ),
					$content              = $module.find('.et_pb_toggle_content'),
					$accordion            = $module.closest( '.et_pb_accordion' ),
					is_accordion          = $accordion.length,
					is_accordion_toggling = $accordion.hasClass( 'et_pb_accordion_toggling' ),
					window_offset_top     = $(window).scrollTop(),
					fixed_header_height   = 0,
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

				$content.slideToggle( 700, function() {
					if ( $module.hasClass('et_pb_toggle_close') ) {
						$module.removeClass('et_pb_toggle_close').addClass('et_pb_toggle_open');
					} else {
						$module.removeClass('et_pb_toggle_open').addClass('et_pb_toggle_close');
					}

					if ( $section.hasClass( 'et_pb_section_parallax' ) && !$section.children().hasClass( 'et_pb_parallax_css') ) {
						$.proxy( et_parallax_set_height, $section )();
					}
				} );

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

						if ( $('#main-header').length && ! et_is_vertical_nav ) {
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
						$et_inputs = $et_contact_form.find( 'input[type=text],textarea' ),
						et_email_reg = /^[\w-]+(\.[\w-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)*?\.[a-z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/,
						redirect_url = typeof $this_contact_container.data( 'redirect_url' ) !== 'undefined' ? $this_contact_container.data( 'redirect_url' ) : '';

					$et_inputs.live( 'focus', function() {
						if ( $( this ).val() === $( this ).siblings('label').text() ) {
							$( this ).val('');
						}
					}).live( 'blur', function() {
						if ( '' === $( this ).val() ) {
							$( this ).val( $( this ).siblings( 'label' ).text() );
						}
					});

					$et_contact_form.on( 'submit', function( event ) {
						var $this_contact_form = $( this ),
							$this_inputs = $this_contact_form.find( 'input[type=text],textarea' ),
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
							var $this_el = $( this ),
								this_val = $this_el.val(),
								this_label = $this_el.siblings( 'label' ).text(),
								field_type = typeof $this_el.data( 'field_type' ) !== 'undefined' ? $this_el.data( 'field_type' ) : 'text',
								required_mark = typeof $this_el.data( 'required_mark' ) !== 'undefined' ? $this_el.data( 'required_mark' ) : 'not_required',
								original_id = typeof $this_el.data( 'original_id' ) !== 'undefined' ? $this_el.data( 'original_id' ) : '',
								default_value;

							// add current field data into array of inputs
							if ( typeof $this_el.attr( 'id' ) !== 'undefined' ) {
								inputs_list.push( { 'field_id' : $this_el.attr( 'id' ), 'original_id' : original_id, 'required_mark' : required_mark, 'field_type' : field_type, 'field_label' : this_label } );
							}

							// add error message for the field if it is required and empty
							if ( 'required' === required_mark && ( '' === this_val || this_label === this_val ) ) {
								$this_el.addClass( 'et_contact_error' );
								this_et_contact_error = true;

								default_value = this_label;

								if ( '' === default_value ) {
									default_value = et_pb_custom.captcha;
								}

								et_fields_message += '<li>' + default_value + '</li>';
							}

							// add error message if email field is not empty and fails the email validation
							if ( 'email' === field_type && '' !== this_val && this_label !== this_val && ! et_email_reg.test( this_val ) ) {
								$this_el.addClass( 'et_contact_error' );
								this_et_contact_error = true;

								if ( ! et_email_reg.test( this_val ) ) {
									et_message += '<li>' + et_pb_custom.invalid + '</li>';
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

			$( '.et_pb_video .et_pb_video_overlay, .et_pb_video_wrap .et_pb_video_overlay' ).click( function() {
				var $this        = $(this),
					$video_image = $this.closest( '.et_pb_video_overlay' );

				$video_image.fadeTo( 500, 0, function() {
					var $image = $(this);

					$image.css( 'display', 'none' );
				} );

				return false;
			} );

			function et_pb_resize_section_video_bg( $video ) {
				$element = typeof $video !== 'undefined' ? $video.closest( '.et_pb_section_video_bg' ) : $( '.et_pb_section_video_bg' );

				$element.each( function() {
					var $this_el = $(this),
						ratio = ( typeof $this_el.attr( 'data-ratio' ) !== 'undefined' )
							? $this_el.attr( 'data-ratio' )
							: $this_el.find('video').attr( 'width' ) / $this_el.find('video').attr( 'height' ),
						$video_elements = $this_el.find( '.mejs-video, video, object' ).css( 'margin', 0 ),
						$container = $this_el.closest( '.et_pb_section_video' ).length
							? $this_el.closest( '.et_pb_section_video' )
							: $this_el.closest( '.et_pb_slides' ),
						body_width = $container.width(),
						container_height = $container.innerHeight(),
						width, height;

					if ( typeof $this_el.attr( 'data-ratio' ) == 'undefined' )
						$this_el.attr( 'data-ratio', ratio );

					if ( body_width / container_height < ratio ) {
						width = container_height * ratio;
						height = container_height;
					} else {
						width = body_width;
						height = body_width / ratio;
					}

					$video_elements.width( width ).height( height );
				} );
			}

			function et_pb_center_video( $video ) {
				$element = typeof $video !== 'undefined' ? $video : $( '.et_pb_section_video_bg .mejs-video' );

				$element.each( function() {
					var $video_width = $(this).width() / 2;
					var $video_width_negative = 0 - $video_width;
					$(this).css("margin-left",$video_width_negative );

					if ( typeof $video !== 'undefined' ) {
						if ( $video.closest( '.et_pb_slider' ).length && ! $video.closest( '.et_pb_first_video' ).length )
							return false;
					}
				} );
			}

			function et_fix_slider_height() {
				if ( ! $et_pb_slider.length ) return;

				$et_pb_slider.each( function() {
					var $slide_section = $(this).parent( '.et_pb_section' ),
						$slide = $(this).find( '.et_pb_slide' ),
						$slide_container = $slide.find( '.et_pb_container' ),
						max_height = 0;

					// If this is appears at the first section benath transparent nav, skip it
					// leave it to et_fix_page_container_position()
					if ( $slide_section.is( '.et_pb_section_first' ) ){
						return true;
					}

					$slide_container.css( 'min-height', 0 );

					$slide.each( function() {
						var $this_el = $(this),
							height = $this_el.innerHeight();

						if ( max_height < height )
							max_height = height;
					} );

					$slide_container.css( 'min-height', max_height );
				} );
			}

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

			et_pb_form_placeholders_init( $( '.et_pb_newsletter_form' ) );

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
				if ( $(this).closest( '.et_pb_login_form' ).length || $(this).closest( '.et_pb_feedburner_form' ).length ) {
					et_pb_maybe_log_event( $(this).closest( '.et_pb_newsletter' ), 'con_goal' );
					return;
				}

				event.preventDefault();

				var $newsletter_container = $(this).closest( '.et_pb_newsletter' ),
					$firstname = $newsletter_container.find( 'input[name="et_pb_signup_firstname"]' ),
					$lastname = $newsletter_container.find( 'input[name="et_pb_signup_lastname"]' ),
					$email = $newsletter_container.find( 'input[name="et_pb_signup_email"]' ),
					list_id = $newsletter_container.find( 'input[name="et_pb_signup_list_id"]' ).val(),
					$result = $newsletter_container.find( '.et_pb_newsletter_result' ).hide(),
					service = $(this).closest( '.et_pb_newsletter_form' ).data( 'service' ) || 'mailchimp';

				$firstname.removeClass( 'et_pb_signup_error' );
				$lastname.removeClass( 'et_pb_signup_error' );
				$email.removeClass( 'et_pb_signup_error' );

				et_pb_remove_placeholder_text( $(this).closest( '.et_pb_newsletter_form' ) );

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
						et_service : service
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
			} );

			window.et_fix_testimonial_inner_width = function() {
				var window_width = $( window ).width();

				if( window_width > 767 ){
					$( '.et_pb_testimonial' ).each( function() {
						if ( ! $(this).is(':visible') ) {
							return;
						}

						var $testimonial      = $(this),
							testimonial_width = $testimonial.width(),
							$portrait         = $testimonial.find( '.et_pb_testimonial_portrait' ),
							portrait_width    = $portrait.width(),
							$testimonial_inner= $testimonial.find( '.et_pb_testimonial_description_inner' ),
							$outer_column     = $testimonial.closest( '.et_pb_column' ),
							testimonial_inner_width = testimonial_width,
							subtract = ! ( $outer_column.hasClass( 'et_pb_column_1_3' ) || $outer_column.hasClass( 'et_pb_column_1_4' ) || $outer_column.hasClass( 'et_pb_column_3_8' ) ) ? portrait_width + 31 : 0;

							$testimonial_inner.width( testimonial_inner_width - subtract );
					} );
				} else {
					$( '.et_pb_testimonial_description_inner' ).removeAttr( 'style' );
				}
			}
			window.et_fix_testimonial_inner_width();

			window.et_reinint_waypoint_modules = function() {
				if ( $.fn.waypoint ) {
					var $et_pb_circle_counter = $( '.et_pb_circle_counter' ),
						$et_pb_number_counter = $( '.et_pb_number_counter' ),
						$et_pb_video_background = $( '.et_pb_section_video_bg video' );

					$( '.et_pb_counter_container, .et-waypoint' ).waypoint( {
						offset: '75%',
						handler: function() {
							$(this.element).addClass( 'et-animated' );
						}
					} );

					// fallback to 'bottom-in-view' offset, to make sure element become visible when it's on the bottom of page and other offsets are not triggered
					$( '.et_pb_counter_container, .et-waypoint' ).waypoint( {
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
							$this_counter.waypoint({
								offset: '65%',
								handler: function() {
									if ( $this_counter.data( 'PieChartHasLoaded' ) ) {
										return;
									}

									$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );

									$this_counter.data( 'PieChartHasLoaded', true );
								}
							});

							// fallback to 'bottom-in-view' offset, to make sure animation applied when element is on the bottom of page and other offsets are not triggered
							$this_counter.waypoint({
								offset: 'bottom-in-view',
								handler: function() {
									if ( $this_counter.data( 'PieChartHasLoaded' ) ) {
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
							$this_counter.waypoint({
								offset: '75%',
								handler: function() {
									$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );
								}
							});

							// fallback to 'bottom-in-view' offset, to make sure animation applied when element is on the bottom of page and other offsets are not triggered
							$this_counter.waypoint({
								offset: 'bottom-in-view',
								handler: function() {
									$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );
								}
							});
						});
					}

					if ( $( '.et_pb_ab_goal' ).length ) {
						var $et_pb_ab_goal = $( '.et_pb_ab_goal' );

						$et_pb_ab_goal.waypoint({
							offset: '80%',
							handler: function() {
								if ( et_pb_ab_logged_status['read_goal'] || ! $et_pb_ab_goal.visible( true ) ) {
									return;
								}

								// log the goal_read if goal is still visible after 3 seconds.
								setTimeout( function() {
									if ( $et_pb_ab_goal.visible( true ) && ! et_pb_ab_logged_status['read_goal'] ) {
										et_pb_ab_update_stats( 'read_goal' );
									}
								}, 3000 );

								et_pb_maybe_log_event( $et_pb_ab_goal, 'view_goal' );
							}
						});
					}

					if ( $et_pb_video_background.length ) {
						$et_pb_video_background.each( function(){
							var $this_video_background = $(this),
								$video_background_wrapper = $this_video_background.closest( '.et_pb_section_video_bg' ),
								this_video_player = this.player;

							// Entering video's top viewport
							$video_background_wrapper.waypoint({
								offset: '100%',
								handler : function( direction ) {
									if ( $this_video_background.is(':visible') && direction === 'down' ) {
										this_video_player.play();
									} else if ( $this_video_background.is(':visible') && direction === 'up' ) {
										this_video_player.pause();
									}
								}
							});

							// Entering video's bottom viewport
							$video_background_wrapper.waypoint({
								offset: '-50%',
								handler : function( direction ) {
									if ( $this_video_background.is(':visible') && direction === 'up' ) {
										this_video_player.play();
									} else if ( $this_video_background.is(':visible') && direction === 'down' ) {
										this_video_player.pause();
									}
								}
							});
						});
					}
				}
			}

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
				var subject_id_raw = $( '.et_pb_ab_subject' ).attr( 'class' ).split( 'et_pb_ab_subject_id-' )[1],
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

			window.et_calc_fullscreen_section = function() {
				var $et_window = $(window),
					$body = $( 'body' ),
					$wpadminbar = $( '#wpadminbar' ),
					et_is_vertical_nav = $body.hasClass( 'et_vertical_nav' ),
					$this_section = $(this),
					this_section_index = $this_section.index('.et_pb_fullwidth_header'),
					$header = $this_section.children('.et_pb_fullwidth_header_container'),
					$header_content = $header.children('.header-content-container'),
					$header_image = $header.children('.header-image-container'),
					sectionHeight = $et_window.height(),
					$wpadminbar = $('#wpadminbar'),
					$top_header = $('#top-header'),
					$main_header = $('#main-header'),
					et_header_height,
					secondary_nav_height;

					secondary_nav_height = $top_header.length && $top_header.is( ':visible' ) ? $top_header.innerHeight() : 0;
					et_header_height = $main_header.length ? $main_header.innerHeight() + secondary_nav_height : 0;

				var calc_header_offset = ( $wpadminbar.length ) ? et_header_height + $wpadminbar.innerHeight() - 1 : et_header_height - 1;

				// Section height adjustment differs in vertical and horizontal nav
				if ( $body.hasClass('et_vertical_nav') ) {
					if ( $et_window.width() >= 980 && $top_header.length ) {
						sectionHeight -= $top_header.height();
					}

					if ( $wpadminbar.length ) {
						sectionHeight -= $wpadminbar.height();
					}
				} else {
					if ( $body.hasClass('et_hide_nav' ) ) {
						// If user is logged in and hide navigation is in use, adjust the section height
						if ( $wpadminbar.length ) {
							sectionHeight -= $wpadminbar.height();
						}

						// In mobile, header always appears. Adjust the section height
						if ( $et_window.width() < 981 && ! $body.hasClass('et_transparent_nav') ) {
							sectionHeight -= $('#main-header').height();
						}
					} else {
						if ( $this_section.offset().top <= calc_header_offset + 3 ) {
							if ( et_is_vertical_nav ) {
								var $top_header = $('#top-header'),
									top_header_height = ( $top_header.length && 0 === $this_section.index( '.et_pb_fullscreen' ) ) ? $top_header.height() : 0,
									wpadminbar_height = ( $wpadminbar.length && 0 === $this_section.index( '.et_pb_fullscreen' ) ) ? $wpadminbar.height() : 0,
									calc_header_offset_vertical = wpadminbar_height + top_header_height;

								sectionHeight -= calc_header_offset_vertical;
							} else {
								sectionHeight -= calc_header_offset;
							}
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

			function et_pb_window_load_scripts() {
				et_fix_fullscreen_section();

				// recalculate fullscreen section sizes on load
				$( 'section.et_pb_fullscreen' ).each( function(){
					var $this_section = $( this );

					$.proxy( et_calc_fullscreen_section, $this_section )();
				});

				$( '.et_pb_fullwidth_header_scroll a' ).click( function( event ) {
					event.preventDefault();

					var $this_section      = $(this).parents( 'section' ),
						is_next_fullscreen = $this_section.next().hasClass( 'et_pb_fullscreen' ),
						$wpadminbar        = $('#wpadminbar'),
						wpadminbar_height  = ( $wpadminbar.length && ! is_next_fullscreen ) ? $wpadminbar.height() : 0,
						main_header_height = is_next_fullscreen || ! et_is_fixed_nav ? 0 : $main_header.height(),
						top_header_height  = is_next_fullscreen || ! et_is_fixed_nav ? 0 : $top_header.height(),
						section_bottom     = $this_section.offset().top + $this_section.outerHeight( true ) - ( wpadminbar_height + top_header_height + main_header_height );

					// Fixed menu is turned off on 980px below screen
					if ( 980 > $et_window.width() ) {
						section_bottom += main_header_height;
						section_bottom += top_header_height;
					}

					// Admin bar is sticked to the top of page on 600px screen below
					if ( 600 > $et_window.width() ) {
						section_bottom += wpadminbar_height;
					}

					if ( $this_section.length ) {
						$( 'html, body' ).animate( { scrollTop : section_bottom }, 800 );

						if ( ! $( '#main-header' ).hasClass( 'et-fixed-header' ) && $( 'body' ).hasClass( 'et_fixed_nav' ) && $( window ).width() > 980 ) {
							setTimeout(function(){
								var section_offset_top = $this_section.offset().top,
									section_height     = $this_section.outerHeight( true ),
									main_header_height = is_next_fullscreen ? 0 : $main_header.height(),
									section_bottom     = section_offset_top + section_height - ( main_header_height + top_header_height + wpadminbar_height);

								$( 'html, body' ).animate( { scrollTop : section_bottom }, 280, 'linear' );
							}, 780 );
						}
					}
				});

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
						if ( $(this).hasClass('et_pb_parallax_css') ) {
							return;
						}

						var $this_parent = $(this).parent();

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
					});
				}

				et_audio_module_set();

				window.et_reinint_waypoint_modules();

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

			if ( $( '.et_pb_search' ).length ) {
				$( '.et_pb_search' ).each( function() {
					var $this_module = $( this ),
						$input_field = $this_module.find( '.et_pb_s' ),
						$button = $this_module.find( '.et_pb_searchsubmit' ),
						input_padding = $this_module.hasClass( 'et_pb_text_align_right' ) ? 'paddingLeft' : 'paddingRight',
						disabled_button = $this_module.hasClass( 'et_pb_hide_search_button' );

					if ( $button.innerHeight() > $input_field.innerHeight() ) {
						$input_field.height( $button.innerHeight() );
					}

					if ( ! disabled_button ) {
						$input_field.css( input_padding, $button.innerWidth() + 10 );
					}
				});
			}

			// apply required classes for the Reply buttons in Comments Module
			if ( $( '.et_pb_comments_module' ).length ) {
				$( '.et_pb_comments_module' ).each( function() {
					var $comments_module = $( this ),
						$comments_module_button = $comments_module.find( '.comment-reply-link' );

					if ( $comments_module_button.length ) {
						$comments_module_button.addClass( 'et_pb_button' );

						if ( typeof $comments_module.data( 'icon' ) !== 'undefined' ) {
							$comments_module_button.attr( 'data-icon', $comments_module.data( 'icon' ) );
							$comments_module_button.addClass( 'et_pb_custom_button_icon' );
						}
					}
				});
			}

		});
	}
})(jQuery)
