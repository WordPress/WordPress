/**
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {
	var $et_pb_section 		         = $( '.et_pb_section' ),
		$et_transparent_nav          = $( '.et_transparent_nav' ),
		$et_footer_info              = $('#footer-info'),
		et_footer_info_original_html = et_main_customizer_data.original_footer_credits;

	if ( ! $et_footer_info.length ) {
		$( '#footer-bottom .container' ).prepend( '<p id="footer-info"></p>' );

		$et_footer_info = $('#footer-info');
	}

	function et_remove_element_class( prefix, el ) {
		var $element = typeof el === 'undefined' ? $( 'body' ) : $( el ),
			el_classes = $element.attr( 'class' ),
			el_class;

		regex = new RegExp( prefix + '[^\\s]+', 'g' );

		el_class = el_classes.replace( regex, '' );

		$element.attr( 'class', $.trim( el_class ) );
	}

	function et_fix_page_top_padding() {
		setTimeout( function() {
			var $body = $( 'body' ),
				$pagecontainer = $( '#page-container' ),
				$top_header = $( '#top-header' ),
				$main_header = $( '#main-header' ),
				secondary_nav_height = $top_header.length && $top_header.is(':visible') ? $top_header.innerHeight() : 0;

			if ( !$body.hasClass('et_hide_nav') && ! window.et_is_vertical_nav && $body.hasClass( 'et_fixed_nav' ) ) {
				$pagecontainer.css( 'paddingTop', $main_header.innerHeight() + secondary_nav_height );
				$main_header.css( 'top', secondary_nav_height );
			} else if ( window.et_is_vertical_nav ) {
				$pagecontainer.css( 'paddingTop', 0 );
				$main_header.css( 'top', 0 );
			} else {
				$pagecontainer.css( 'paddingTop', 0 );
			}

			et_fix_page_container_position();

		}, 550 );
	}

	// Fixing logo height size
	function et_fix_logo_height() {
		var header_style       = typeof wp.customize.value( 'et_divi[header_style]' )() === 'undefined' ? 'left' : wp.customize.value( 'et_divi[header_style]' )(),
			menu_height        = typeof wp.customize.value( 'et_divi[menu_height]' )() === 'undefined' ? 66 : parseInt( wp.customize.value( 'et_divi[menu_height]' )() ),
			fixed_menu_height  = ! $('#fixed_menu_height').length || typeof wp.customize.value( 'et_divi[minimized_menu_height]' )() === 'undefined' ? 40 : parseInt( wp.customize.value( 'et_divi[minimized_menu_height]' )() ),
			logo_height        = typeof wp.customize.value( 'et_divi[logo_height]' )() === 'undefined' ? 54 : parseInt( wp.customize.value( 'et_divi[logo_height]' )() ),
			$body              = $('body'),
			is_rtl             = $body.hasClass( 'rtl' ),
			$et_top_navigation = $('#et-top-navigation'),
			et_top_nav_padding = is_rtl ? 'paddingRight' : 'paddingLeft',
			logo_width         = 30;
			style_id           = "style#logo_height_style",
			style_content      = '<style id="logo_height_style">',
			top_nav_padding_value = '';

			if ( header_style === 'left' || header_style === 'slide' || header_style === 'fullscreen' ) {
				style_content += "#logo { max-height: " + logo_height + "%; }\ ";
				style_content += ".et_pb_svg_logo #logo { height: " + logo_height + "%; }\ ";
			}

			if ( header_style === 'centered' ) {
				style_content += ".et_header_style_centered #logo { max-height: " + logo_height + "%; }\ ";
				style_content += ".et_pb_svg_logo.et_header_style_centered #logo { height: " + logo_height + "%; }\ ";
			}

			if ( header_style === 'split' ) {
				style_content += "body.et_header_style_split .centered-inline-logo-wrap { width: auto; height: " + ( ( ( menu_height / 100 ) * logo_height ) + 14 ) + "px; }\ ";
				style_content += "body.et_header_style_split .et-fixed-header .centered-inline-logo-wrap { width: auto; height: " + ( ( ( fixed_menu_height / 100 ) * logo_height ) + 14 ) + "px; }\ ";
				style_content += "body.et_header_style_split .centered-inline-logo-wrap #logo { height: auto; max-height: 100%; }\ ";
				style_content += "body.et_header_style_split .et-fixed-header .centered-inline-logo-wrap #logo { height: auto; max-height: 100%; }\ ";

				// Removes inline width styling
				$( '.et_header_style_split .centered-inline-logo-wrap' ).css({ 'width' : '' });
			}

			if ( window.et_is_vertical_nav ) {
				style_content += "#main-header .logo_container { width: " + logo_height + "%; }\ ";
				style_content += ".et_header_style_centered #main-header .logo_container, .et_header_style_split #main-header .logo_container { margin: 0 auto; }\ ";
			}

			style_content += '</style>';

			// Append / refresh logo height
			et_customizer_update_styles( style_id, style_content );

			setTimeout( function() {
				// Update inline styling
				if ( header_style === 'left' && ! window.et_is_vertical_nav || header_style === 'slide' || header_style === 'fullscreen' ) {
					// Update logo height
					logo_width += $( '#logo' ).width();

					top_nav_padding_value = logo_width;
				}

				$et_top_navigation.css( et_top_nav_padding, top_nav_padding_value );
			}, 700 );
	}

	// Retrieving padding/margin value based on formatted saved padding/margin strings
	function et_get_saved_padding_margin_value( saved_value, order ) {
		if ( typeof saved_value === 'undefined' ) {
			return false;
		}

		var values = saved_value.split('|');

		return typeof values[order] !== 'undefined' ? values[order] : false;
	}

	// Calculate fixed header height by cloning, emulating, and calculating its height
	function et_fix_saved_main_header_height( state ) {
		var is_desktop_view = $(window).width() > 980,
			main_header_height = 0,
			$main_header = $('#main-header'),
			data_attribute = state === 'fixed' ? 'data-fixed-height-onload' : 'data-height-onload',
			main_header_clone_classname = state === 'fixed' ? 'main-header-clone et-fixed-header' : 'main-header-clone',
			$main_header_clone = $main_header.clone().addClass( main_header_clone_classname );

		if ( is_desktop_view ) {
			if ( state === 'fixed' ) {
				$main_header_clone.css({
					opacity: 0,
					position: 'fixed',
					top: 'auto',
					right: 0,
					bottom: 0,
					left: 0
				}).appendTo( $('body') );
			} else {
				$main_header_clone.css({
					opacity: 0,
					position: 'absolute',
					top: 0,
					right: 0,
					bottom: 'auto',
					left: 0
				}).prependTo( $('body') );
			}

			main_header_height = $main_header_clone.height();

			$main_header_clone.remove();

			$main_header.attr( data_attribute, main_header_height );
		}
	}

	// Fixing main header's alpha to fixed background color transition
	function et_fix_page_container_position(){
		var $et_pb_slider  					= $( '.et_pb_slider' ),
			$top_header 					= $('#top-header'),
			$main_header 					= $('#main-header'),
			$main_header_innerheight 		= $main_header.innerHeight(),
			$main_container_wrapper 		= $( '#page-container' ),
			$et_transparent_nav 			= $( '.et_transparent_nav' ),
			$et_transparent_nav_length 		= $et_transparent_nav.length,
			$et_pb_first_row 				= $( 'body.et_pb_pagebuilder_layout .et_pb_section:first-child' ),
			$et_pb_first_row_length 		= $et_pb_first_row.length,
			$et_pb_first_row_first_module 	= $et_pb_first_row.children( '.et_pb_module:first' ),
			$et_main_content_first_row 		= $( '#main-content .container:first-child' ),
			$et_main_content_first_row_length = $et_main_content_first_row.length,
			$et_main_content_first_row_meta_wrapper = $et_main_content_first_row.find('.et_post_meta_wrapper:first'),
			$et_main_content_first_row_meta_wrapper_title = $et_main_content_first_row_meta_wrapper.find( 'h1' ),
			$et_main_content_first_row_content = $et_main_content_first_row.find('.entry-content:first'),
			$et_single_post 				= $( 'body.single-post' ),
			$et_window 						= $(window),
			et_window_width 				= $et_window.width(),
			secondary_nav_height 			= $top_header.length && $top_header.is( ':visible' ) ? $top_header.innerHeight() : 0,
			inline_style 					= "<style id='et_fix_page_container_position'>",
			$inline_style 					= $('#et_fix_page_container_position'),
			$inline_style_length 			= $inline_style.length,
			$et_pb_post_fullwidth           = $( '.single.et_pb_pagebuilder_layout.et_full_width_page' ),
			is_fixed_nav                    = $('body').hasClass('et_fixed_nav'),
			is_nav_vertical_to_horizontal   = $('body').hasClass( 'nav-vertical-to-horizontal' );


		// Set data-height-onload for header if the page is loaded on large screen
		// If the page is loaded from small screen, rely on data-height-onload printed on the markup,
		// prevent window resizing issue from small to large
		if ( et_window_width > 980 && ! $main_header.attr( 'data-height-loaded' ) ) {
			$main_header.attr({ 'data-height-onload' : $main_header_innerheight, 'data-height-loaded' : true });
		}

		// Use on page load calculation for large screen. Use on the fly calculation for small screen (980px below)
		if ( et_window_width <= 980 ) {
			var header_height = $main_header_innerheight + secondary_nav_height - 1;

			// If transparent is detected, #main-content .container's padding-top needs to be added to header_height
			// And NOT a pagebuilder page
			if ( $et_transparent_nav_length && ! $et_pb_first_row_length ) {
				header_height += 58;
			}
		} else {

			// Get header height from header attribute
			var header_height = is_nav_vertical_to_horizontal ? $main_header.height() : parseInt( $main_header.attr( 'data-height-onload' ) );

			header_height += secondary_nav_height;

			// Non page builder page needs to be added by #main-content .container's fixed height
			if ( $et_transparent_nav_length && ! window.et_is_vertical_nav && $et_main_content_first_row_length ) {
				header_height += 58;
			}

			// If this is horizontal to vertical switching, update main header's data-height-onload
			if ( is_nav_vertical_to_horizontal ) {
				$main_header.attr({ 'data-height-onload' : $main_header.height() });
			}

			// Calculate fixed header height by cloning, emulating, and calculating its height
		}

		// Calculate fixed header height by cloning, emulating, and calculating its height
		et_fix_saved_main_header_height( 'fixed' );

		// Specific adjustment required for transparent nav + not vertical nav
		if ( $et_transparent_nav_length && ! window.et_is_vertical_nav ) {

			// Add class for first row for custom section padding purpose
			$et_pb_first_row.addClass( 'et_pb_section_first' );

			// List of conditionals
			var is_pb                            = $et_pb_first_row.length,
				is_post_pb                       = is_pb && $et_single_post.length,
				is_post_pb_full_layout_has_title = $et_pb_post_fullwidth.length && $et_main_content_first_row_meta_wrapper_title.length,
				is_post_pb_full_layout_no_title  = $et_pb_post_fullwidth.length && 0 === $et_main_content_first_row_meta_wrapper_title.length,
				is_pb_fullwidth_section_first    = $et_pb_first_row.is( '.et_pb_fullwidth_section' ),
				is_no_pb_mobile                  = $et_window.width() <= 980 && $et_main_content_first_row.length;

			if ( is_post_pb && ! ( is_post_pb_full_layout_no_title && is_pb_fullwidth_section_first ) ) {

				/* Desktop / Mobile + Single Post */

				/*
				 * EXCEPT for fullwidth layout + fullwidth section ( at the first row ).
				 * It is basically the same as page + fullwidth section with few quirk.
				 * Instead of duplicating the conditional for each module, it'll be simpler to negate
				 * fullwidth layout + fullwidth section in is_post_pb and rely it to is_pb_fullwidth_section_first
				 */

				// Remove main content's inline padding to styling to prevent looping padding-top calculation
				$et_main_content_first_row.css({ 'paddingTop' : '' });

				if ( $et_window.width() < 980 ) {
					header_height += 40;
				}

				if ( is_pb_fullwidth_section_first ) {
					// If the first section is fullwidth, restore the padding-top modified area at first section
					$et_pb_first_row.css({
						'paddingTop' : '0',
					});
				}

				if ( is_post_pb_full_layout_has_title ) {

					// Add header height to post meta wrapper as padding top
					$et_main_content_first_row_meta_wrapper.css({
						'paddingTop' : header_height
					});

				} else if ( is_post_pb_full_layout_no_title ) {

					$et_pb_first_row.css({
						'paddingTop' : header_height
					});

				} else {

					// Add header height to first row content as padding top
					$et_main_content_first_row.css({
						'paddingTop' : header_height
					});

				}

			} else if ( is_pb_fullwidth_section_first ){

				/* Desktop / Mobile + Pagebuilder + Fullwidth Section */

				var $et_pb_first_row_first_module = $et_pb_first_row.children( '.et_pb_module:first' );

				// Quirks: If this is post with fullwidth layout + no title + fullwidth section at first row,
				// Remove the added height at line 2656
				if ( is_post_pb_full_layout_no_title && is_pb_fullwidth_section_first && $et_window.width() > 980 ) {
					header_height = header_height - 58;
				}

				if ( $et_pb_first_row_first_module.is( '.et_pb_slider' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth slider */

					var $et_pb_first_row_first_module_slide_image 		= $et_pb_first_row_first_module.find( '.et_pb_slide_image' ),
						$et_pb_first_row_first_module_slide 				= $et_pb_first_row_first_module.find( '.et_pb_slide' ),
						$et_pb_first_row_first_module_slide_container 	= $et_pb_first_row_first_module.find( '.et_pb_slide .et_pb_container' ),
						et_pb_slide_image_margin_top 		= 0 - ( parseInt( $et_pb_first_row_first_module_slide_image.height() ) / 2 ),
						et_pb_slide_container_height 		= 0,
						$et_pb_first_row_first_module_slider_arrow 		= $et_pb_first_row_first_module.find( '.et-pb-slider-arrows a'),
						et_pb_first_row_slider_arrow_height = $et_pb_first_row_first_module_slider_arrow.height();

					// Adding padding top to each slide so the transparency become useful
					$et_pb_first_row_first_module_slide.css({
						'paddingTop' : header_height,
					});

					// delete container's min-height
					$et_pb_first_row_first_module_slide_container.css({
						'min-height' : ''
					});

					// Adjusting slider's image, considering additional top padding of slideshow
					$et_pb_first_row_first_module_slide_image.css({
						'marginTop' : et_pb_slide_image_margin_top
					});

					// Adjusting slider's arrow, considering additional top padding of slideshow
					$et_pb_first_row_first_module_slider_arrow.css({
						'marginTop' : ( ( header_height / 2 ) - ( et_pb_first_row_slider_arrow_height / 2 ) )
					});

					// Looping the slide and get the highest height of slide
					et_pb_first_row_slide_container_height_new = 0

					$et_pb_first_row_first_module.find( '.et_pb_slide' ).each( function(){
						var $et_pb_first_row_first_module_slide_item = $(this),
							$et_pb_first_row_first_module_slide_container = $et_pb_first_row_first_module_slide_item.find( '.et_pb_container' );

						// Make sure that the slide is visible to calculate correct height
						$et_pb_first_row_first_module_slide_item.show();

						// Remove existing inline css to make sure that it calculates the height
						$et_pb_first_row_first_module_slide_container.css({ 'min-height' : '' });

						var et_pb_first_row_slide_container_height = $et_pb_first_row_first_module_slide_container.innerHeight();

						if ( et_pb_first_row_slide_container_height_new < et_pb_first_row_slide_container_height ){
							et_pb_first_row_slide_container_height_new = et_pb_first_row_slide_container_height;
						}

						// Hide the slide back if it isn't active slide
						if ( $et_pb_first_row_first_module_slide_item.is( ':not(".et-pb-active-slide")' ) ){
							$et_pb_first_row_first_module_slide_item.hide();
						}
					});

					// Setting appropriate min-height, considering additional top padding of slideshow
					$et_pb_first_row_first_module_slide_container.css({
						'min-height' : et_pb_first_row_slide_container_height_new
					});

				} else if ( $et_pb_first_row_first_module.is( '.et_pb_fullwidth_header' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth header */

					// Remove existing inline stylesheet to prevent looping padding
					$et_pb_first_row_first_module.removeAttr( 'style' );

					// Get paddingTop from stylesheet
					var et_pb_first_row_first_module_fullwidth_header_padding_top = parseInt( $et_pb_first_row_first_module.css( 'paddingTop' ) );

					// Implement stylesheet's padding-top + header_height
					$et_pb_first_row_first_module.css({
						'paddingTop' : ( header_height + et_pb_first_row_first_module_fullwidth_header_padding_top )
					} );

				} else if ( $et_pb_first_row_first_module.is( '.et_pb_fullwidth_portfolio' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth Portfolio */

					$et_pb_first_row_first_module.css({ 'paddingTop' : header_height });

				} else if ( $et_pb_first_row_first_module.is( '.et_pb_map_container' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth Map */

					var $et_pb_first_row_map = $et_pb_first_row_first_module.find( '.et_pb_map' );

					// Remove existing inline height to prevent looping height calculation
					$et_pb_first_row_map.css({ 'height' : '' });

					// Implement map height + header height
					$et_pb_first_row_first_module.find('.et_pb_map').css({
						'height' : header_height + parseInt( $et_pb_first_row_map.css( 'height' ) )
					});

					// Adding specific class to mark the map as first row section element
					$et_pb_first_row_first_module.addClass( 'et_beneath_transparent_nav' );

				} else if ( $et_pb_first_row_first_module.is( '.et_pb_fullwidth_menu' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth Menu */
					$et_pb_first_row_first_module.css({ 'marginTop' : header_height });

				} else if ( $et_pb_first_row_first_module.is( '.et_pb_fullwidth_code' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth code */

					var $et_pb_first_row_first_module_code = $et_pb_first_row_first_module;

					$et_pb_first_row_first_module_code.css({ 'paddingTop' : '' });

					var et_pb_first_row_first_module_code_padding_top = parseInt( $et_pb_first_row_first_module_code.css( 'paddingTop' ) );

					$et_pb_first_row_first_module_code.css({
						'paddingTop' : header_height + et_pb_first_row_first_module_code_padding_top
					});

				} else if ( $et_pb_first_row_first_module.is( '.et_pb_post_title' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth Post Title */

					var $et_pb_first_row_first_module_title = $et_pb_first_row_first_module;

					$et_pb_first_row_first_module_title.css({
						'paddingTop' : header_height + 50
					});
				}

			} else if ( is_pb ) {

				/* Desktop / Mobile + Pagebuilder + Regular section */

				// Remove first row's inline padding top styling to prevent looping padding-top calculation
				$et_pb_first_row.css({ 'paddingTop' : '' });

				// Get saved custom padding from data-* attributes. Builder automatically adds
				// saved custom paddings to data-* attributes on first section
				var et_window_width                 = $et_window.width(),
					saved_custom_padding            = $et_pb_first_row.attr('data-padding'),
					saved_custom_padding_top        = et_get_saved_padding_margin_value( saved_custom_padding, 0 ),
					saved_custom_padding_tablet     = $et_pb_first_row.attr('data-padding-tablet'),
					saved_custom_padding_tablet_top = et_get_saved_padding_margin_value( saved_custom_padding_tablet, 0 ),
					saved_custom_padding_phone      = $et_pb_first_row.attr('data-padding-phone'),
					saved_custom_padding_phone_top  = et_get_saved_padding_margin_value( saved_custom_padding_phone, 0 ),
					applied_saved_custom_padding;

				if ( saved_custom_padding_top || saved_custom_padding_tablet_top || saved_custom_padding_phone_top ) {
					// Applies padding top to first section to automatically convert saved unit into px
					if ( et_window_width > 980 && saved_custom_padding_top ) {
						$et_pb_first_row.css({
							paddingTop: saved_custom_padding_top
						});
					} else if ( et_window_width > 767 && saved_custom_padding_tablet_top ) {
						$et_pb_first_row.css({
							paddingTop: saved_custom_padding_tablet_top
						});
					} else if ( saved_custom_padding_phone_top ) {
						$et_pb_first_row.css({
							paddingTop: saved_custom_padding_phone_top
						});
					}

					// Get converted custom padding top value
					applied_saved_custom_padding = parseInt( $et_pb_first_row.css( 'paddingTop' ) );

					// Implemented saved & converted padding top + header height
					$et_pb_first_row.css({
						paddingTop: ( header_height + applied_saved_custom_padding )
					});
				} else {
					// Pagebuilder ignores #main-content .container's fixed height and uses its row's padding
					// Anticipate the use of custom section padding.
					et_pb_first_row_padding_top = header_height + parseInt( $et_pb_first_row.css( 'paddingBottom' ) );

					// Implementing padding-top + header_height
					$et_pb_first_row.css({
						'paddingTop' : et_pb_first_row_padding_top
					});
				}

			} else if ( is_no_pb_mobile ) {

				// Mobile + not pagebuilder
				$et_main_content_first_row.css({
					'paddingTop' : header_height
				});

			} else if ( is_fixed_nav ) {

				$('#main-content .container:first-child').css({
					'paddingTop' : header_height
				});

			}

			// Append inline style
			inline_style += "#page-container{padding-top: 0 !important; }";

		} else {

			// Remove class for first row for custom section padding purpose
			$et_pb_first_row.removeClass( 'et_pb_section_first' );

			// Add padding-top for #page-container if fixed nav is used
			if ( is_fixed_nav ) {
				$main_container_wrapper.css( 'paddingTop', header_height );
			}

			// Cleanup mechanism from transparent nav into fixed color nav for edge cases (e.g. fullwidth section)

			// List of conditionals
			var is_pb                            = $et_pb_first_row.length,
				is_post_pb                       = is_pb && $et_single_post.length,
				is_post_pb_full_layout_has_title = $et_pb_post_fullwidth.length && $et_main_content_first_row_meta_wrapper_title.length,
				is_post_pb_full_layout_no_title  = $et_pb_post_fullwidth.length && 0 === $et_main_content_first_row_meta_wrapper_title.length,
				is_pb_fullwidth_section_first    = $et_pb_first_row.is( '.et_pb_fullwidth_section' ),
				is_no_pb_mobile                  = $et_window.width() <= 980 && $et_main_content_first_row.length;

			if ( is_post_pb && ! ( is_post_pb_full_layout_no_title && is_pb_fullwidth_section_first ) ) {

				/* Desktop / Mobile + Single Post */

				/*
				 * EXCEPT for fullwidth layout + fullwidth section ( at the first row ).
				 * It is basically the same as page + fullwidth section with few quirk.
				 * Instead of duplicating the conditional for each module, it'll be simpler to negate
				 * fullwidth layout + fullwidth section in is_post_pb and rely it to is_pb_fullwidth_section_first
				 */

				// Remove main content's inline padding to styling to prevent looping padding-top calculation
				$et_main_content_first_row.css({ 'paddingTop' : '' });

				if ( is_pb_fullwidth_section_first ) {
					$et_pb_first_row.css({
						'paddingTop' : '',
					});
				}

				if ( is_post_pb_full_layout_has_title ) {

					// Add header height to post meta wrapper as padding top
					$et_main_content_first_row_meta_wrapper.css({
						'paddingTop' : ''
					});

				} else if ( is_post_pb_full_layout_no_title ) {

					$et_pb_first_row.css({
						'paddingTop' : ''
					});

				} else {

					// Add header height to first row content as padding top
					$et_main_content_first_row.css({
						'paddingTop' : ''
					});

				}

			} else if ( is_pb_fullwidth_section_first ){

				/* Desktop / Mobile + Pagebuilder + Fullwidth Section */

				var $et_pb_first_row_first_module = $et_pb_first_row.children( '.et_pb_module:first' );

				if ( $et_pb_first_row.children( '.et_pb_module:first' ).is( '.et_pb_slider' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth slider */

					var $et_pb_first_row_slide_image 		= $et_pb_first_row.find( '.et_pb_slide_image' ),
						$et_pb_first_row_slide 				= $et_pb_first_row.find( '.et_pb_slide' ),
						$et_pb_first_row_slide_container 	= $et_pb_first_row.find( '.et_pb_slide .et_pb_container' ),
						et_pb_slide_image_margin_top 		= 0 - ( parseInt( $et_pb_first_row_slide_image.height() ) / 2 ),
						et_pb_slide_container_height 		= 0,
						$et_pb_first_row_slider_arrow 		= $et_pb_first_row.find( '.et-pb-slider-arrows a'),
						et_pb_first_row_slider_arrow_height = $et_pb_first_row_slider_arrow.height();

					// Adding padding top to each slide so the transparency become useful
					$et_pb_first_row_slide.css({
						'paddingTop' : '',
					});

					// delete container's min-height
					$et_pb_first_row_slide_container.css({
						'min-height' : ''
					});

					// Adjusting slider's image, considering additional top padding of slideshow
					$et_pb_first_row_slide_image.css({
						'marginTop' : ''
					});

					// Adjusting slider's arrow, considering additional top padding of slideshow
					$et_pb_first_row_slider_arrow.css({
						'marginTop' : ''
					});

					// Setting appropriate min-height, considering additional top padding of slideshow
					$et_pb_first_row_slide_container.css({
						'min-height' : ''
					});

				} else if ( $et_pb_first_row.children( '.et_pb_module:first' ).is( '.et_pb_fullwidth_header' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth header */

					// Implement stylesheet's padding-top + header_height
					$et_pb_first_row_first_module.css({
						'paddingTop' : ''
					} );

				} else if ( $et_pb_first_row.children( '.et_pb_module:first' ).is( '.et_pb_fullwidth_portfolio' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth Portfolio */

					$et_pb_first_row.find( '.et_pb_fullwidth_portfolio' ).css({ 'paddingTop' : '' });

				} else if ( $et_pb_first_row_first_module.is( '.et_pb_map_container' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth Map */

					var $et_pb_first_row_map = $et_pb_first_row_first_module.find( '.et_pb_map' );

					// Remove existing inline height to prevent looping height calculation
					$et_pb_first_row_map.css({ 'height' : '' });

					// Implement map height + header height
					$et_pb_first_row_first_module.find('.et_pb_map').css({
						'height' : ''
					});

					// Adding specific class to mark the map as first row section element
					$et_pb_first_row_first_module.removeClass( 'et_beneath_transparent_nav' );

				} else if ( $et_pb_first_row_first_module.is( '.et_pb_fullwidth_menu' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth Menu */
					$et_pb_first_row_first_module.css({ 'marginTop' : '' });

				} else if ( $et_pb_first_row_first_module.is( '.et_pb_fullwidth_code' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth code */

					var $et_pb_first_row_code = $et_pb_first_row_first_module;

					$et_pb_first_row_code.css({ 'paddingTop' : '' });

					$et_pb_first_row_code.css({
						'paddingTop' : ''
					});

				} else if ( $et_pb_first_row_first_module.is( '.et_pb_post_title' ) ) {

					/* Desktop / Mobile + Pagebuilder + Fullwidth Post Title */

					var $et_pb_first_row_title = $et_pb_first_row_first_module;

					$et_pb_first_row_title.css({
						'paddingTop' : ''
					});
				}

			} else if ( is_pb ) {

				/* Desktop / Mobile + Pagebuilder + Regular section */

				// Remove first row's inline padding top styling to prevent looping padding-top calculation
				$et_pb_first_row.css({ 'paddingTop' : '' });

				// Implementing padding-top + header_height
				$et_pb_first_row.css({
					'paddingTop' : ''
				});

			} else if ( is_no_pb_mobile ) {

				// Mobile + not pagebuilder
				$et_main_content_first_row.css({
					'paddingTop' : ''
				});

			} else {

				$('#main-content .container:first-child').css({
					'paddingTop' : ''
				});

			}

		}

		// Print or update inline style on <head>
		inline_style += '</style>';

		if ( $inline_style_length ) {
			$inline_style.replaceWith( inline_style );
		} else {
			$( 'head' ).append( inline_style );
		}

		// Remove nav transition marking, if there's any. It should only used once during first transition
		$('body').removeClass( 'nav-vertical-to-horizontal nav-horizontal-to-vertical' );

		// et_change_primary_nav_position( 0 );
	}

	// Fixing main header's box-shadow based on primary_nav_bg and fixed_primary_nav_bg
	function et_fix_main_header_box_shadow() {
		var primary_nav_bg 					= wp.customize( 'et_divi[primary_nav_bg]' )(),
			fixed_primary_nav_bg 			= $( 'body' ).hasClass( 'et_fixed_nav' ) ?  wp.customize( 'et_divi[fixed_primary_nav_bg]' )() : '',
			et_custom_header_shadow_style 	= $( '<style />', { id: 'et_custom_header_shadow_style' }),
			$et_custom_header_shadow_style 	= $( '#et_custom_header_shadow_style' ),
			$et_custom_header_shadow_style_length = $et_custom_header_shadow_style.length;

		// Append styling

		// main-header's rule: if it's transparent, remove box-shadow
		if ( 'string' === typeof primary_nav_bg && 'rgba' === primary_nav_bg.substr( 0, 4 ) ) {
			et_custom_header_shadow_style.append( '#main-header{ background: '+primary_nav_bg+' !important;\n box-shadow: none; }' );
		} else {
			et_custom_header_shadow_style.append( '#main-header{ background: '+primary_nav_bg+' !important;\n box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);\n -moz-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);\n -webkit-box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1); }' );
		}

		// fixed main-header's rule: 1) remove box-shadow if transparent. 2) if it uses default (#ffffff) derive color from primary_nav_bg
		if ( 'string' === typeof fixed_primary_nav_bg && ( 'rgba' === fixed_primary_nav_bg.substr( 0, 4 ) || ( 'rgba' === primary_nav_bg.substr( 0, 4 ) && '#ffffff' === fixed_primary_nav_bg ) ) ) {
			et_custom_header_shadow_style.append( '.et-fixed-header#main-header{ box-shadow: none !important; }' );
		} else {
			et_custom_header_shadow_style.append( '.et-fixed-header#main-header{ box-shadow: 0 0 7px rgba(0, 0, 0, 0.1) !important; \n  -moz-box-shadow: 0 0 7px rgba(0, 0, 0, 0.1) !important; \n -webkit-box-shadow: 0 0 7px rgba(0, 0, 0, 0.1) !important; }' );
		}

		if ( '#ffffff' === fixed_primary_nav_bg ) {
			et_custom_header_shadow_style.append( '.et-fixed-header#main-header{ background: '+primary_nav_bg+' !important; }' );
		} else {
			et_custom_header_shadow_style.append( '.et-fixed-header#main-header{ background: '+fixed_primary_nav_bg+' !important; }' );
		}

		// Print / replace the custom styling
		if ( $et_custom_header_shadow_style_length ) {
			$et_custom_header_shadow_style.replaceWith( et_custom_header_shadow_style );
		} else {
			$( 'head' ).append( et_custom_header_shadow_style );
		}
	}

	function et_maybe_fix_header_style( mode ) {
		var $et_window 					= $(window),
			$et_window_width 			= $et_window.width(),
			$main_header 				= $('#main-header'),
			$main_header_height 		= $main_header.height(),
			$et_top_navigation 			= $('#et-top-navigation'),
			$logo_container 			= $('#main-header > .container > .logo_container'),
			$logo_container_length 		= $logo_container.length,
			$logo_container_splitted 	= $('.centered-inline-logo-wrap > .logo_container'),
			et_top_navigation_li_size 	= $et_top_navigation.children('nav').children('ul').children('li').size(),
			et_top_navigation_li_break_index = Math.round( et_top_navigation_li_size / 2 ) - 1;

		if ( $et_window_width > 980 && $logo_container_length && mode == 'split' && ! window.et_is_vertical_nav ) {
			$('<li class="centered-inline-logo-wrap"></li>').insertAfter($et_top_navigation.find('nav > ul >li:nth('+et_top_navigation_li_break_index+')') );
			$logo_container.appendTo( $et_top_navigation.find('.centered-inline-logo-wrap') );
		}

		if ( $et_window_width <= 980 && $logo_container_splitted.length || mode != 'split' ) {
			$logo_container_splitted.prependTo('#main-header > .container');
			$('#main-header .centered-inline-logo-wrap').remove();
		}

		// Update initial header height attribute
		if ( $et_window_width > 980 ) {
			$main_header.attr({ 'data-height-onload' : $main_header_height });
		}
	}

	function et_fix_slider_height() {
		var $et_pb_slider  = $( '.et_pb_slider' );

		if ( ! $et_pb_slider.length ) {
			return;
		}

		$et_pb_slider.each( function() {
			var $slide = $(this).find( '.et_pb_slide' ),
				$slide_container = $slide.find( '.et_pb_container' ),
				max_height = 0;

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

	function et_maybe_create_secondary_nav() {
		if ( $( '#top-header' ).length ) {
			return;
		}

		$( 'body' )
			.addClass( 'et_secondary_nav_enabled' )
			.prepend( '<div id="top-header" class="et_nav_text_color_light"><div class="container"></div></div>' );

		et_fix_page_top_padding();
	}

	function et_maybe_remove_secondary_nav() {
		if ( ! $( '#top-header' ).length ) {
			return;
		}

		setTimeout( function() {
			if ( $( '#top-header .container' ).children().filter( ':visible' ).length ) {
				return;
			}

			$( 'body' )
				.removeClass( 'et_secondary_nav_enabled' )
				.removeClass( 'et_secondary_nav_two_panels' )
				.find( '#top-header' )
				.remove();

			et_fix_page_top_padding();
		}, 500 );
	}
	function add_menu_styles( to, style_id, is_fixed ) {
		var $full_style_id = $( 'style#et_menu_preview_' + style_id ),
			fixed_class = 'fixed' === is_fixed ? '.et-fixed-header' : '',
			menu_styles = "<style id='et_menu_preview_" + style_id + "'>\  @media all and ( min-width: 981px ) {\ ";

			// Append menu styles
			menu_styles += ".et_header_style_left " + fixed_class + " #et-top-navigation nav > ul > li > a { padding-bottom: " + to / 2 + "px; }\ ";
			menu_styles += ".et_header_style_left " + fixed_class + " #et-top-navigation { padding: " + to / 2 + "px 0 0 0; }\ ";
			menu_styles += ".et_header_style_split " + fixed_class + " #et-top-navigation nav > ul > li > a { padding-bottom: " + to / 2 + "px; }\ ";
			menu_styles += ".et_header_style_split " + fixed_class + " .centered-inline-logo-wrap { width: "+to+"px; margin: -"+to+"px 0; }\ ";
			menu_styles += ".et_header_style_split " + fixed_class + " .centered-inline-logo-wrap #logo { max-height: "+to+"px; }\ ";
			menu_styles += ".et_header_style_split " + fixed_class + " #et-top-navigation { padding: " + to / 2 + "px 0 0 0; }\ ";
			menu_styles += ".et_header_style_centered header#main-header" + fixed_class + " .logo_container { height: " + to + "px; }\ ";
			menu_styles += ".et_header_style_centered header#main-header" + fixed_class + " #top-menu > li > a { padding-bottom: " + to * .18 + "px; }\ ";
			menu_styles += ".et_header_style_slide " + fixed_class + " #et-top-navigation, .et_header_style_fullscreen " + fixed_class + " #et-top-navigation { padding: " + ( to - 18 ) / 2 + "px 0 " + ( to - 18 ) / 2 + "px 0 !important; }\ ";

			menu_styles += '}\ </style>';

		if ( $full_style_id.length ) {
			$( $full_style_id ).replaceWith( menu_styles );
		} else {
			$( 'head' ).append( menu_styles );
		}
	}

	function et_slide_to_top() {
		$('html, body').animate({
			scrollTop : 0
		}, 100, function() {
			setTimeout( function() {
				et_fix_saved_main_header_height( 'initial' );

				$(window).trigger('resize');
			}, 300 );
		});
	}

	function add_content_sidebar_style( sidebar_width ) {
		var content_width          = 100 - parseInt( sidebar_width ),
			content_sidebar_style  = $( '<style />', { id : 'theme-customizer-sidebar-width-css' } ),
			$content_sidebar_style = $('#theme-customizer-sidebar-width-css');

		content_sidebar_style.text( 'body #page-container #sidebar{ width:' + sidebar_width + '%; }\
			body #page-container #left-area{ width:' + content_width + '%; }\
			.et_right_sidebar #main-content .container:before{ right:' + sidebar_width+'% !important }\
			.et_left_sidebar #main-content .container:before{ left:'+sidebar_width+'% !important }\
		');

		if ( $content_sidebar_style.length ) {
			$( $content_sidebar_style ).replaceWith( content_sidebar_style );
		} else {
			$( 'head' ).append( content_sidebar_style );
		}
	}

	/**
	* Basically mimics et_pb_print_module_styles_css() on functions.php
	* Append to <head> instead of adding inline styling. Module's individual styling > Customizer's module styles
	*/
	function et_print_module_styles_css( id, type, selector, value, important ){
		// sanitize id into safe style's ID
		var style_id 		= id.replace(/[ +\/\[\]]/g,'_').toLowerCase(),
			$style 			= $('#' + style_id),
			$style_length 	= $style.length;

		// create DOM
		var style = $( '<style />', {
			id : style_id
		} );

		// Determine important tag
		if ( typeof important !== 'undefined' ){
			var important_tag = '!important';
		} else {
			var important_tag = '';
		}

		// append style into DOM
		switch( type ){
			case 'font-size':
				style.text( selector + "{ font-size: " + value + "px " + important_tag + ";}" );

				// Option with specific adjustment for smaller columns
				var smaller_title_sections = [
					'et_divi[et_pb_audio-header_font_size]',
					'et_divi[et_pb_blog-title_font_size]',
					'et_divi[et_pb_cta-header_font_size]',
					'et_divi[et_pb_contact_form-header_font_size]',
					'et_divi[et_pb_login-header_font_size]',
					'et_divi[et_pb_signup-header_font_size]',
					'et_divi[et_pb_slider-header_font_size]',
					'et_divi[et_pb_slider-body_font_size]',
					'et_divi[et_pb_countdown_timer-header_font_size]'
				];

				if( $.inArray( id, smaller_title_sections ) ){

					// font size coefficient
					switch ( id ) {
						case 'et_divi[et_pb_slider-header_font_size]':
							var font_size = parseInt( value ) * .565217391; // 26/46
							break;

						case 'et_divi[et_pb_slider-body_font_size]':
							var font_size = parseInt( value ) * .777777778; // 14/16
							break;

						default:
							var font_size = parseInt( value ) * .846153846; // 22/26
							break;
					}

					style.append( ".et_pb_column_1_3 " + selector + ", .et_pb_column_1_4 " + selector + " { font-size: " + font_size + "px " + important_tag + "; }" );
				}
				break;

			case 'font-styles':
				style.text( selector + " { " + et_set_font_styles( value, important_tag ) + " }" );
				break;

			case 'letter-spacing':
				style.text( selector + "{ letter-spacing: " + value + "px " + important_tag + ";}" );
				break;

			case 'line-height':
				style.text( selector + "{ line-height: " + value + "em " + important_tag + ";}" );
				break;

			case 'color':
				style.text( selector + "{ color: " + value + " " + important_tag + ";}" );
				break;

			case 'background-color':
				style.text( selector + "{ background-color: " + value + " " + important_tag + ";}" );
				break;

			case 'border-radius':
				style.text( selector + " { -moz-border-radius: " + value + "px; -webkit-border-radius: " + value + "px; border-radius: " + value + "px; }" );
				break;

			case 'width':
				style.text( selector + "{ width: " + value + "px " + important_tag + ";}" );
				break;

			case 'height':
				style.text( selector + "{ height: " + value + "px " + important_tag + ";}" );
				break;

			case 'padding':
				style.text( selector + "{ padding: " + value + "px " + important_tag + ";}" );
				break;

			case 'padding-top-bottom':
				style.text( selector + "{ padding: " + value + "px 0 " + important_tag + ";}" );
				break;

			case 'padding-tabs':
				var padding_tab_top_bottom = parseInt( value ) * 0.133333333,
					padding_tab_active_top = padding_tab_top_bottom + 1,
					padding_tab_active_bottom = padding_tab_top_bottom - 1,
					padding_tab_content = parseInt( value ) * 0.8;

				// negative result will cause layout issue
				if ( padding_tab_active_bottom < 0 ) {
					padding_tab_active_bottom = 0;
				}

				style.text( ".et_pb_tabs_controls li{ padding: " + padding_tab_active_top + "px " + value + "px " + padding_tab_active_bottom + "px; } .et_pb_tabs_controls li.et_pb_tab_active{ padding: " + padding_tab_top_bottom + "px " + value + "px; }  .et_pb_all_tabs { padding: " + padding_tab_content + "px " + value + "px " + important_tag + ";}" );
				break;

			case 'padding-slider':
				style.text( selector + "{ padding-top: " + value + "%; padding-bottom: " + value + "%; }" );

				if ( 'et_pagebuilder_slider_padding' === id ) {
					style.append( '@media only screen and ( max-width: 767px ) { ' + selector + '{ padding-top: 16%; padding-bottom: 16%; } }' );
				}

				break;

			case 'padding-call-to-action':
				value = parseInt( value );

				style.text( ".et_pb_promo { padding: " + value + "px " + ( value * ( 60 / 40 ) ) + "px; }" );
				style.append( ".et_pb_column_1_2 .et_pb_promo, .et_pb_column_1_3 .et_pb_promo, .et_pb_column_1_4 .et_pb_promo { padding: " + value + "px; }" );
				break;

			case 'social-icon-size':
				var icon_margin 	= parseInt( value ) * 0.57;
				var icon_dimension = parseInt( value ) * 2;

				style.text( ".et_pb_social_media_follow li a.icon{ margin-right: " + icon_margin + "px; width: " + icon_dimension + "px; height: " + icon_dimension + "px; } .et_pb_social_media_follow li a.icon::before{ width: " + icon_dimension + "px; height: " + icon_dimension + "px; font-size: " + value + "px; line-height: " + icon_dimension + "px; } .et_pb_social_media_follow li a.follow_button{ font-size:" + value + "px; }" );

				break;

			case 'border-top-style':
				style.text( selector + "{ border-top-style: " + value  + " " + important_tag +  "; }" );
				break;

			case 'border-top-width':
				style.text( selector + "{ border-top-width: " + value  + "px " + important_tag +  "; }" );
				break;
		}

		// Insert custom styling
		if ( $style_length ) {
			$style.replaceWith( style );
		} else {
			$( 'head' ).append( style );
		}
	}

	function et_set_font_styles( value, important_tag ) {
		var font_styles = value.split( '|' ),
			style = '';

		if ( $.inArray( 'bold', font_styles ) >= 0 ) {
			style += "font-weight: bold " + important_tag + ";";
		} else {
			style += "font-weight: inherit " + important_tag + ";";
		}

		if ( $.inArray( 'italic', font_styles ) >= 0 ) {
			style += "font-style: italic " + important_tag + ";";
		} else {
			style += "font-style: inherit " + important_tag + ";";
		}

		if ( $.inArray( 'underline', font_styles ) >= 0 ) {
			style += "text-decoration: underline " + important_tag + ";";
		} else {
			style += "text-decoration: inherit " + important_tag + ";";
		}

		if ( $.inArray( 'uppercase', font_styles ) >= 0 ) {
			style += "text-transform: uppercase " + important_tag + ";";
		} else {
			style += "text-transform: inherit " + important_tag + ";";
		}

		return style;
	}

	function et_fix_footer_widget_bullet_top(){

		var style = $( '<style />', { id : 'footer-widget-bullet-style' }),
			$style = $( '#footer-widget-bullet-style' ),
			line_height = parseFloat( $('.footer-widget .et_pb_widget div').css( 'line-height' ) ),
			footer_widget_bullet_top = ( line_height / 2 ) - 3;

		style.text( "#footer-widgets .footer-widget li:before { top: " + footer_widget_bullet_top + "px; }" );

		// Insert custom styling
		if ( $style.length ) {
			$style.replaceWith( style );
		} else {
			$( 'head' ).append( style );
		}
	}

	function et_customizer_update_styles( style_id, $style_content ) {
		if ( $( style_id ).length ) {
			if ( '' !== $style_content ) {
				$( style_id ).replaceWith( $style_content );
			} else {
				$( style_id ).remove();
			}
		} else {
			$( 'head' ).append( $style_content );
		}
	}

	function et_calculate_header_values() {
		var $top_header = $( '#top-header' ),
			secondary_nav_height = $top_header.length && $top_header.is( ':visible' ) ? $top_header.innerHeight() : 0,
			admin_bar_height     = $( '#wpadminbar' ).length ? $( '#wpadminbar' ).innerHeight() : 0,
			$slide_menu_container = $( '.et_header_style_slide .et_slide_in_menu_container' );

		et_header_height      = $( '#main-header' ).innerHeight() + secondary_nav_height,
		et_header_modifier    = et_header_height <= 90 ? et_header_height - 29 : et_header_height - 56,
		et_header_offset      = et_header_modifier + admin_bar_height;

		et_primary_header_top = secondary_nav_height + admin_bar_height;

		if ( $slide_menu_container.length && ! $( 'body' ).hasClass( 'et_pb_slide_menu_active' ) ) {
			$slide_menu_container.css( { right: '-' + $slide_menu_container.innerWidth() + 'px' } );

			if ( $( 'body' ).hasClass( 'et_boxed_layout' ) ) {
				var page_container_margin = $( '#page-container' ).css( 'margin-left' );
				$( '#main-header' ).css( { left : page_container_margin } );
			}
		}
	}

	function et_fix_fullscreen_section() {
		var $et_window = $(window);

		$( 'section.et_pb_fullscreen' ).each( function(){
			var $this_section = $( this );

			$.proxy( window.et_calc_fullscreen_section, $this_section )();

			$et_window.on( 'resize', $.proxy( window.et_calc_fullscreen_section, $this_section ) );

		});
	}

	function et_fix_slide_in_top_bar() {
		var $body = $( 'body' );

		if ( 0 === $body.find( '.et_slide_menu_top' ).height() ) {
			$body.find( '.et_slide_menu_top' ).css( { 'display' : 'none' } );
		} else {
			$body.find( '.et_slide_menu_top' ).css( { 'display' : 'block' } );
		}
	}

	et_fix_slide_in_top_bar();

	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			$('#logo').attr({ 'alt' : to });
		} );
	} );

	wp.customize( 'et_divi[link_color]', function( value ) {
		value.bind( function( to ) {
			$( 'article p:not(.post-meta) a, .comment-edit-link, .pinglist a, .pagination a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[body_font_size]', function( value ) {
		value.bind( function( to ) {
			var widget = $( '.footer-widget li' ).css( 'font-size' );
			$( '#main-content, .et_pb_column_1_2 .et_quote_content blockquote cite, .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_pb_column_1_3 .et_quote_content blockquote cite, .et_pb_column_3_8 .et_quote_content blockquote cite, .et_pb_column_1_4 .et_quote_content blockquote cite, .et_pb_blog_grid .et_quote_content blockquote cite, .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_pb_blog_grid .et_link_content a.et_link_main_url' ).css( 'font-size', to + 'px' );
			$( '.et_pb_slide_content, .et_pb_best_value' ).css( 'font-size', to * 1.14 + 'px' );
			if ( to == widget ) {
				$( '#main-footer li, #main-footer a, #main-footer p, #main-footer' ).css( 'font-size', to + 'px' );
			}
		} );
	} );

	wp.customize( 'et_divi[phone_body_font_size]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#phone_body_font_size' ).remove(),
			custom_style = "<style id='phone_body_font_size'>\
								@media only screen and ( max-width: 767px ) {\
									#main-content, .et_pb_column_1_2 .et_quote_content blockquote cite, .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_pb_column_1_3 .et_quote_content blockquote cite, .et_pb_column_3_8 .et_quote_content blockquote cite, .et_pb_column_1_4 .et_quote_content blockquote cite, .et_pb_blog_grid .et_quote_content blockquote cite, .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_pb_blog_grid .et_link_content a.et_link_main_url, #main-footer li, #main-footer a, #main-footer p, #main-footer  { font-size:" + to + "px !important; }\
									.et_pb_slide_content, .et_pb_best_value { font-size:" + to * 1.14 + "px !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[tablet_body_font_size]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#tablet_body_font_size' ).remove(),
			custom_style = "<style id='tablet_body_font_size'>\
								@media only screen and ( max-width: 980px ) {\
									#main-content, .et_pb_column_1_2 .et_quote_content blockquote cite, .et_pb_column_1_2 .et_link_content a.et_link_main_url, .et_pb_column_1_3 .et_quote_content blockquote cite, .et_pb_column_3_8 .et_quote_content blockquote cite, .et_pb_column_1_4 .et_quote_content blockquote cite, .et_pb_blog_grid .et_quote_content blockquote cite, .et_pb_column_1_3 .et_link_content a.et_link_main_url, .et_pb_column_3_8 .et_link_content a.et_link_main_url, .et_pb_column_1_4 .et_link_content a.et_link_main_url, .et_pb_blog_grid .et_link_content a.et_link_main_url, #main-footer li, #main-footer a, #main-footer p, #main-footer  { font-size:" + to + "px !important; }\
									.et_pb_slide_content, .et_pb_best_value { font-size:" + to * 1.14 + "px !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[section_padding]', function( value ) {
		value.bind( function( to ) {

		// Don't use cache selector as it might be modified by other controls
		var $et_transparent_nav = $( '.et_transparent_nav' );

			// Detect transparent nav & non vertical nav
			if ( $et_transparent_nav.length && ! window.et_is_vertical_nav ) {
				$( '.et_pb_section:nth-child(1)' ).css({
					'paddingBottom' : to + '%'
				});

				$( '.et_pb_section:gt(0)' ).css({
					'padding' : to + '% 0'
				});

				// first section's paddingTop has to be done after the other paddings have been added
				$( '.et_pb_section:nth-child(1)' ).css({
					'paddingTop' : (  parseInt( $( '#main-header' ).innerHeight() ) + parseInt( $( '#top-header' ).innerHeight() ) + parseInt( $( '.et_pb_section:nth-child(1)' ).css( 'paddingBottom' ) ) - 8 )
				});
			} else {
				$( '.et_pb_section' ).css( 'padding', to + '% 0' );
			}
		} );
	} );

	wp.customize( 'et_divi[row_padding]', function( value ) {
		value.bind( function( to ) {
			$( '.et_pb_row' ).css( 'padding', to + '% 0' );
		} );
	} );

	wp.customize( 'et_divi[phone_row_height]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#phone_row_height' ).remove(),
			custom_style = "<style id='phone_row_height'>\
								@media only screen and ( max-width: 767px ) {\
									.et_pb_row, .et_pb_column .et_pb_row_inner { padding: " + to + "px 0 !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[tablet_row_height]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#tablet_row_height' ).remove(),
			custom_style = "<style id='tablet_row_height'>\
								@media only screen and ( max-width: 980px ) {\
									.et_pb_row, .et_pb_column .et_pb_row_inner { padding: " + to + "px 0 !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[phone_section_height]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#phone_section_height' ).remove(),
			custom_style = "<style id='phone_section_height'>\
								@media only screen and ( max-width: 767px ) {\
									.et_pb_section { padding: " + to + "px 0; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[tablet_section_height]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#tablet_section_height' ).remove(),
			custom_style = "<style id='tablet_section_height'>\
								@media only screen and ( max-width: 980px ) {\
									.et_pb_section { padding: " + to + "px 0; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[body_header_size]', function( value ) {
		value.bind( function( to ) {
			var widget = $( '.footer-widget h4' ).css( 'font-size' );
			$( 'h1' ).css( 'font-size', to + 'px' );
			$( 'h2, .product .related h2, .et_pb_column_1_2 .et_quote_content blockquote p' ).css( 'font-size', to * 0.86 + 'px' );
			$( 'h3' ).css( 'font-size', to * 0.73 + 'px' );
			$( 'h5' ).css( 'font-size', to * 0.53 + 'px' );
			$( 'h6' ).css( 'font-size', to * 0.47 + 'px' );
			$( '.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_column_1_4 .et_pb_audio_module_content h2 ' ).css( 'font-size', to * 0.53 + 'px' );
			$( '#main-content h4, .et_pb_column_1_3 .et_pb_post h2, .et_pb_column_1_4 .et_pb_post h2, .et_pb_blog_grid h2, .et_pb_column_1_3 .et_quote_content blockquote p, .et_pb_column_3_8 .et_quote_content blockquote p, .et_pb_column_1_4 .et_quote_content blockquote p, .et_pb_blog_grid .et_quote_content blockquote p, .et_pb_column_1_3 .et_link_content h2, .et_pb_column_3_8 .et_link_content h2, .et_pb_column_1_4 .et_link_content h2, .et_pb_blog_grid .et_link_content h2, .et_pb_column_1_3 .et_audio_content h2, .et_pb_column_3_8 .et_audio_content h2, .et_pb_column_1_4 .et_audio_content h2, .et_pb_blog_grid .et_audio_content h2, .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_circle_counter h3, .et_pb_number_counter h3' ).css( 'font-size', to * 0.6 + 'px' );
			$( '.et_pb_slide_description .et_pb_slide_title' ).css( 'font-size', to * 1.53 + 'px' );
			if ( to == widget ) {
				$( '.footer-widget h4' ).css( 'font-size', to * 0.6 + 'px' );
			}
		} );
	} );

	wp.customize( 'et_divi[body_header_height]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#body_header_height' ).remove(),
			custom_style = "<style id='body_header_height'>\
									h1, h2, h3, h4, h5, h6, .et_quote_content blockquote p, .et_pb_slide_description .et_pb_slide_title { line-height: " + to + "em; }\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[body_font_height]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#body_font_height' ).remove(),
			custom_style = "<style id='body_font_height'>\
									body { line-height: " + to + "em; }\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[body_header_spacing]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#body_header_spacing' ).remove(),
			custom_style = "<style id='body_header_spacing'>\
									h1, h2, h3, h4, h5, h6, .et_quote_content blockquote p, .et_pb_slide_description .et_pb_slide_title { letter-spacing: " + to + "px; }\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[primary_nav_font_spacing]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#primary_nav_font_spacing' ).remove(),
			custom_style = "<style id='primary_nav_font_spacing'>\
									#top-menu li a, .et_search_form_container input { letter-spacing: " + to + "px; }\
									.et_search_form_container input::-moz-placeholder { letter-spacing: " + to + "px; }\
									.et_search_form_container input::-webkit-input-placeholder { letter-spacing: " + to + "px; }\
									.et_search_form_container input:-ms-input-placeholder { letter-spacing: " + to + "px; }\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_fullwidth]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.addClass('et_fullwidth_secondary_nav');
			} else {
				$body.removeClass('et_fullwidth_secondary_nav');
			}
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_font_spacing]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#secondary_nav_font_spacing' ).remove(),
			custom_style = "<style id='secondary_nav_font_spacing'>\
									#top-header, #top-header a, #et-secondary-nav li li a, #top-header .et-social-icon a:before { letter-spacing: " + to + "px; }\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[phone_header_font_size]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#phone_header_font_size' ).remove(),
			custom_style = "<style id='phone_header_font_size'>\
								@media only screen and ( max-width: 767px ) {\
									h1 { font-size: " + to + "px !important; }\
									h2 { font-size: " + to * 0.86 + "px !important; }\
									h3 { font-size: " + to * 0.73 + "px !important; }\
									.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: " + to * 0.53 + "px !important; }\
									#main-content h4, .et_pb_column_1_3 .et_pb_post h2, .et_pb_column_1_4 .et_pb_post h2, .et_pb_blog_grid h2, .et_pb_column_1_3 .et_quote_content blockquote p, .et_pb_column_3_8 .et_quote_content blockquote p, .et_pb_column_1_4 .et_quote_content blockquote p, .et_pb_blog_grid .et_quote_content blockquote p, .et_pb_column_1_3 .et_link_content h2, .et_pb_column_3_8 .et_link_content h2, .et_pb_column_1_4 .et_link_content h2, .et_pb_blog_grid .et_link_content h2, .et_pb_column_1_3 .et_audio_content h2, .et_pb_column_3_8 .et_audio_content h2, .et_pb_column_1_4 .et_audio_content h2, .et_pb_blog_grid .et_audio_content h2, .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_circle_counter h3, .et_pb_number_counter h3 { font-size: " + to * 0.6 + "px !important; }\
									.et_pb_slide_description .et_pb_slide_title { font-size: " + to * 1.53 + "px !important; }\
									.footer-widget h4 { font-size: " + to * 0.6 + "px !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[tablet_header_font_size]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#tablet_header_font_size' ).remove(),
			custom_style = "<style id='tablet_header_font_size'>\
								@media only screen and ( max-width: 980px ) {\
									h1 { font-size: " + to + "px !important; }\
									h2 { font-size: " + to * 0.86 + "px !important; }\
									h3 { font-size: " + to * 0.73 + "px !important; }\
									.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_column_1_4 .et_pb_audio_module_content h2 { font-size: " + to * 0.53 + "px !important; }\
									#main-content h4, .et_pb_column_1_3 .et_pb_post h2, .et_pb_column_1_4 .et_pb_post h2, .et_pb_blog_grid h2, .et_pb_column_1_3 .et_quote_content blockquote p, .et_pb_column_3_8 .et_quote_content blockquote p, .et_pb_column_1_4 .et_quote_content blockquote p, .et_pb_blog_grid .et_quote_content blockquote p, .et_pb_column_1_3 .et_link_content h2, .et_pb_column_3_8 .et_link_content h2, .et_pb_column_1_4 .et_link_content h2, .et_pb_blog_grid .et_link_content h2, .et_pb_column_1_3 .et_audio_content h2, .et_pb_column_3_8 .et_audio_content h2, .et_pb_column_1_4 .et_audio_content h2, .et_pb_blog_grid .et_audio_content h2, .et_pb_column_3_8 .et_pb_audio_module_content h2, .et_pb_column_1_3 .et_pb_audio_module_content h2, .et_pb_gallery_grid .et_pb_gallery_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2, .et_pb_filterable_portfolio_grid .et_pb_portfolio_item h2, .et_pb_circle_counter h3, .et_pb_number_counter h3 { font-size: " + to * 0.6 + "px !important; }\
									.et_pb_slide_description .et_pb_slide_title { font-size: " + to * 1.53 + "px !important; }\
									.footer-widget h4 { font-size: " + to * 0.6 + "px !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[font_color]', function( value ) {
		value.bind( function( to ) {
			$( 'body' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[accent_color]', function( value ) {
		value.bind( function( to ) {
			var	$accent_style = "<style id='accent_color'>.et_pb_counter_amount, .et_pb_featured_table .et_pb_pricing_heading, .et_pb_pricing_table_button, .comment-reply-link, .form-submit .et_pb_button, .et_quote_content, .et_link_content, .et_audio_content, .et_pb_post_slider.et_pb_bg_layout_dark, #page-container .et_slide_in_menu_container, .et_pb_contact p input[type='radio']:checked + label i:before { background-color: " + to + "; }\
								#et_search_icon:hover, .mobile_menu_bar:before, .footer-widget h4, .et-social-icon a:hover, .et_pb_sum, .et_pb_pricing li a, .et_overlay:before, .et_pb_member_social_links a:hover, .et_pb_widget li a:hover, .et_pb_bg_layout_light .et_pb_promo_button, .et_pb_bg_layout_light .et_pb_more_button, .et_pb_filterable_portfolio .et_pb_portfolio_filters li a.active, .et_pb_filterable_portfolio .et_pb_portofolio_pagination ul li a.active, .et_pb_gallery .et_pb_gallery_pagination ul li a.active, .wp-pagenavi span.current, .wp-pagenavi a:hover, .et_pb_contact_submit, .et_password_protected_form .et_submit_button, .et_pb_bg_layout_light .et_pb_newsletter_button, .nav-single a, .posted_in a, .et_pb_contact p input[type='checkbox']:checked + label i:before { color:" + to + "; }\
								.et-search-form, .nav li ul, .et_mobile_menu, .footer-widget li:before, .et_pb_pricing li:before { border-color " + to + "; }\
								</style>",
				style_id = 'style#accent_color';

			et_customizer_update_styles( style_id, $accent_style );
		} );
	} );

	wp.customize( 'et_divi[primary_nav_bg]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body'),
				$body_has_et_transparent_nav = $body.hasClass( 'et_transparent_nav' );

			$( '#main-header, #main-header .nav li ul, .et-search-form, #main-header .et_mobile_menu' ).css( 'background-color', to );

			// Transition from fixed color to transparent color
			if ( ! $body_has_et_transparent_nav && to.substr( 0, 4 ) === 'rgba' ) {
				$body.addClass( 'et_transparent_nav' );
				$( 'head' ).append( "<style id='remove_transparent_margin'>\
										body #page-container { margin-top: 0 !important; }\
									</style>" );
				et_fix_page_container_position();
				window.et_is_transparent_nav = true;
			}

			// Transition from transparent to fixed color
			if ( $body_has_et_transparent_nav && to.substr( 0, 4 ) !== 'rgba' ) {
				$body.removeClass( 'et_transparent_nav' );
				et_fix_page_container_position();
				$( '#remove_transparent_margin' ).remove();
				window.et_is_transparent_nav = false;
			}

			// Always fix main header's background and box-shadow on change
			et_fix_main_header_box_shadow();

			// Fix full width header in fullscreen mode when transitioning from/to alpha color
			et_fix_fullscreen_section();
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_bg]', function( value ) {
		value.bind( function( to ) {
			$( '#top-header, #et-secondary-nav li ul' ).css( 'background-color', to );
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_dropdown_bg]', function( value ) {
		value.bind( function( to ) {
			$( '#et-secondary-nav li ul' ).css( 'background-color', to );
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_dropdown_link_color]', function( value ) {
		value.bind( function( to ) {
			$( '#et-secondary-nav li ul a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[primary_nav_dropdown_bg]', function( value ) {
		value.bind( function( to ) {
			$( '#et-top-navigation li ul' ).css( 'background-color', to );
		} );
	} );

	wp.customize( 'et_divi[primary_nav_dropdown_line_color]', function( value ) {
		value.bind( function( to ) {
			$( '.nav li ul' ).css( 'border-color', to );
		} );
	} );

	wp.customize( 'et_divi[primary_nav_dropdown_link_color]', function( value ) {
		value.bind( function( to ) {
			$( '#et-top-navigation li ul a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[fixed_secondary_nav_bg]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#fixed_secondary_nav_bg' ).remove(),
			custom_style = "<style id='fixed_secondary_nav_bg'>\
								@media only screen and ( min-width: 981px ) {\
									.et-fixed-header#top-header, .et-fixed-header#top-header #et-secondary-nav li ul { background-color: " + to + " !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[fixed_primary_nav_bg]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#fixed_primary_nav_bg' ).remove(),
			custom_style = "<style id='fixed_primary_nav_bg'>\
								@media only screen and ( min-width: 981px ) {\
									.et-fixed-header#main-header .nav li ul, .et-fixed-header .et-search-form { background-color: " + to + " !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
			et_fix_main_header_box_shadow();
		} );
	} );

	wp.customize( 'et_divi[mobile_primary_nav_bg]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#mobile_primary_nav_bg' ).remove(),
			custom_style = "<style id='mobile_primary_nav_bg'>\
								@media only screen and ( max-width: 980px ) {\
									body #main-header, #main-header .nav li ul, .et-search-form, #main-header .et_mobile_menu { background-color: " + to + " !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
			et_fix_main_header_box_shadow();
		} );
	} );

	wp.customize( 'et_divi[fixed_primary_nav_font_size]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#fixed_primary_nav_font_size' ).remove(),
			custom_style = "<style id='fixed_primary_nav_font_size'>\
								@media only screen and ( min-width: 981px ) {\
									.et-fixed-header #top-menu li a { font-size: " + to + "px !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[fixed_menu_link]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#fixed_menu_link' ).remove(),
			custom_style = "<style id='fixed_menu_link'>\
								@media only screen and ( min-width: 981px ) {\
									.et-fixed-header #top-menu a, .et-fixed-header #et_search_icon:before, .et-fixed-header #et_top_search .et-search-form input, .et-fixed-header .et_search_form_container input,.et-fixed-header .et_close_search_field:after { color: " + to + " !important; }\
									.et-fixed-header .et_search_form_container input::-moz-placeholder { color: " + to + " !important; }\
									.et-fixed-header .et_search_form_container input::-webkit-input-placeholder { color: " + to + " !important; }\
									.et-fixed-header .et_search_form_container input:-ms-input-placeholder { color: " + to + " !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[fixed_menu_link_active]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#fixed_menu_link_active' ).remove(),
			custom_style = "<style id='fixed_menu_link_active'>\
								@media only screen and ( min-width: 981px ) {\
									.et-fixed-header #top-menu li.current-menu-ancestor > a, .et-fixed-header #top-menu li.current-menu-item > a { color: " + to + " !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[fixed_secondary_menu_link]', function( value ) {
		value.bind( function( to ) {
			var	$style = "<style id='fixed_secondary_menu_link'>.et-fixed-header#top-header, .et-fixed-header#top-header a { color: " + to + " !important; }\
						  </style>",
				style_id = 'style#fixed_secondary_menu_link';

			et_customizer_update_styles( style_id, $style );
		} );
	} );

	wp.customize( 'et_divi[header_color]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#header_color' ).remove(),
			custom_style = "<style id='header_color'>\
								h1,h2,h3,h4,h5,h6 { color: " + to + "; }\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[disable_custom_footer_credits]', function( value ) {
		value.bind( function( to ) {
			var footer_info_html = '';

			if ( to === false ) {
				var custom_footer_credits = wp.customize.value('et_divi[custom_footer_credits]')();

				footer_info_html = $.trim( custom_footer_credits ) !== '' ? custom_footer_credits : et_footer_info_original_html;
			}

			$et_footer_info.html( footer_info_html );
		} );
	} );

	wp.customize( 'et_divi[custom_footer_credits]', function( value ) {
		value.bind( function( to ) {
			if ( $.trim( to ) === '' ) {
				to = et_footer_info_original_html;
			}

			$et_footer_info.html( to );
		} );
	} );

	wp.customize( 'et_divi[footer_bg]', function( value ) {
		value.bind( function( to ) {
			$( '#main-footer' ).css( 'background-color', to );
		} );
	} );

	wp.customize( 'et_divi[footer_columns]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');
			if ( to ) {
				$body.removeClass(function(index, css){
					return (css.match (/\bet_pb_footer_columns\S+/g) || []).join(' ')
				});
				$body.addClass( 'et_pb_footer_columns' + to );
			}
		} );
	} );

	wp.customize( 'et_divi[footer_widget_link_color]', function( value ) {
		value.bind( function( to ) {
			$( '#footer-widgets .footer-widget a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[footer_widget_text_color]', function( value ) {
		value.bind( function( to ) {
			$( '.footer-widget' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[footer_widget_header_color]', function( value ) {
		value.bind( function( to ) {
			$( '#main-footer .footer-widget h4' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[footer_widget_bullet_color]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#footer_widget_bullet_color' ).remove(),
			custom_style = "<style id='footer_widget_bullet_color'>\
							.footer-widget li:before { border-color: " + to + "; }\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[widget_header_font_size]', function( value ) {
		value.bind( function( to ) {
				$( '.footer-widget h4' ).css( 'font-size', to + 'px' );
		} );
	} );

	wp.customize( 'et_divi[widget_header_font_style]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[widget_header_font_style]',
				'font-styles',
				'.footer-widget h4',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[widget_body_font_size]', function( value ) {
		value.bind( function( to ) {
			$( '.footer-widget li, .footer-widget li a, .footer-widget div, .footer-widget, #footer-info' ).css( 'font-size', to + 'px' );

			et_fix_footer_widget_bullet_top();
		} );
	} );


	wp.customize( 'et_divi[footer_menu_background_color]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[footer_menu_background_color]',
				'background-color',
				'#et-footer-nav',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[footer_menu_text_color]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[footer_menu_text_color]',
				'color',
				'.bottom-nav, .bottom-nav a, .bottom-nav li.current-menu-item a',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[footer_menu_active_link_color]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[footer_menu_active_link_color]',
				'color',
				'#et-footer-nav .bottom-nav li.current-menu-item a',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[footer_menu_letter_spacing]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[footer_menu_letter_spacing]',
				'letter-spacing',
				'.bottom-nav',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[footer_menu_font_style]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[footer_menu_font_style]',
				'font-styles',
				'.bottom-nav a',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[footer_menu_font_size]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[footer_menu_font_size]',
				'font-size',
				'.bottom-nav, .bottom-nav a',
				to
			);
		} );
	} );


	wp.customize( 'et_divi[bottom_bar_background_color]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[bottom_bar_background_color]',
				'background-color',
				'#footer-bottom',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[bottom_bar_text_color]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[bottom_bar_text_color]',
				'color',
				'#footer-info, #footer-info a',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[bottom_bar_font_style]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[bottom_bar_font_style]',
				'font-styles',
				'#footer-info, #footer-info a',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[bottom_bar_font_size]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[bottom_bar_font_size]',
				'font-size',
				'#footer-info',
				to,
				true
			);
		} );
	} );

	wp.customize( 'et_divi[bottom_bar_social_icon_size]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[bottom_bar_social_icon_size]',
				'font-size',
				'#footer-bottom .et-social-icon a',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[bottom_bar_social_icon_color]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[bottom_bar_social_icon_color]',
				'color',
				'#footer-bottom .et-social-icon a',
				to
			);
		} );
	} );


	wp.customize( 'et_divi[widget_body_font_style]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[widget_body_font_style]',
				'font-styles',
				'.footer-widget .et_pb_widget div, .footer-widget .et_pb_widget ul, .footer-widget .et_pb_widget ol, .footer-widget .et_pb_widget > label',
				to
			);
		} );
	} );

	wp.customize( 'et_divi[widget_body_line_height]', function( value ) {
		value.bind( function( to ) {
			et_print_module_styles_css(
				'et_divi[widget_body_line_height]',
				'line-height',
				'.footer-widget .et_pb_widget div, .footer-widget .et_pb_widget ul, .footer-widget .et_pb_widget ol, .footer-widget .et_pb_widget label',
				to
			);

			et_fix_footer_widget_bullet_top();
		} );
	} );

	wp.customize( 'et_divi[menu_link]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#menu_link' ).remove(),
			custom_style = "<style id='menu_link'>\
								.et_nav_text_color_light #top-menu > li > a, .et_nav_text_color_dark #top-menu > li > a, #top-menu a, #et_search_icon:before, #et_top_search .et-search-form input, .et_search_form_container input, span.et_close_search_field:after, #et-top-navigation .et-cart-info { color: " + to + " !important; }\
								.et_search_form_container input::-moz-placeholder { color: " + to + "; }\
								.et_search_form_container input::-webkit-input-placeholder { color: " + to + "; }\
								.et_search_form_container input:-ms-input-placeholder { color: " + to + "; }\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_text_color_new]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#secondary_nav_text_color_new' ).remove(),
			custom_style = "<style id='secondary_nav_text_color_new'>\
								#top-header, #top-header a { color: " + to + " !important; }\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[mobile_menu_link]', function( value ) {
		value.bind( function( to ) {
			$( 'head style#mobile_menu_link' ).remove(),
			custom_style = "<style id='mobile_menu_link'>\
								@media only screen and ( max-width: 980px ) {\
									.et_header_style_centered .mobile_nav .select_page, .et_header_style_split .mobile_nav .select_page, .et_mobile_menu li a, .mobile_menu_bar:before, .et_nav_text_color_light #top-menu > li > a, .et_nav_text_color_dark #top-menu > li > a, #top-menu a, #et_search_icon:before, #et_top_search .et-search-form input, .et_search_form_container input, .et_close_search_field:after, #et-top-navigation .et-cart-info  { color: " + to + " !important; }\
									.et_search_form_container input::-moz-placeholder { color: " + to + " !important; }\
									.et_search_form_container input::-webkit-input-placeholder { color: " + to + " !important; }\
									.et_search_form_container input:-ms-input-placeholder { color: " + to + " !important; }\
								}\
							</style>",
			$( 'head' ).append( custom_style );
		} );
	} );

	wp.customize( 'et_divi[menu_link_active]', function( value ) {
		value.bind( function( to ) {
			$( '#top-menu li.current-menu-ancestor > a, #top-menu li.current-menu-item > a, .bottom-nav li.current-menu-item > a' ).css( 'color', to );
		} );
	} );

	wp.customize( 'et_divi[content_width]', function( value ) {
		value.bind( function( to ) {
			$( '.container, .et_pb_row, .et_pb_slider .et_pb_container, .et_pb_fullwidth_section .et_pb_title_container, .et_pb_fullwidth_section .et_pb_title_featured_container' ).css( 'max-width', to + 'px' );
		} );
		value.bind( function( to ) {
			$( '.et_boxed_layout #page-container, .et_boxed_layout #page-container #top-header, .et_boxed_layout #page-container #main-header, .et_boxed_layout #page-container .container, .et_boxed_layout #page-container .et_pb_row' ).css( 'max-width', parseInt(to) + 160 + 'px' );
		} );
	} );

	wp.customize( 'et_divi[gutter_width]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');
			if ( to ) {
				$body.removeClass( 'et_pb_gutters1 et_pb_gutters2 et_pb_gutters3 et_pb_gutters4' );
				$body.addClass( 'et_pb_gutters' + to );
			}
		} );
	} );

	wp.customize( 'et_divi[sidebar_width]', function( value ) {
		value.bind( function( to ) {
			add_content_sidebar_style( to );
		} );
	} );

	wp.customize( 'et_divi[use_sidebar_width]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.removeClass( 'et_pb_gutter ' );
				add_content_sidebar_style( wp.customize.value( 'et_divi[sidebar_width]' )() );
			} else {
				$body.addClass( 'et_pb_gutter' );
				$( 'style#theme-customizer-sidebar-width-css').remove();
			}
		} );
	} );

	wp.customize( 'et_divi[boxed_layout]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');
			if ( to ) {
				$body.addClass( 'et_boxed_layout' );
			} else {
				$body.removeClass( 'et_boxed_layout' );
				$( '#main-header, #page-container, #top-header' ).css( 'max-width', 'none' )
			}
		} );
	} );

	wp.customize( 'et_divi[cover_background]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.addClass( 'et_cover_background' );
			} else {
				$body.removeClass( 'et_cover_background' );
			}
		} );
	} );

	wp.customize( 'et_divi[show_header_social_icons]', function( value ) {
		value.bind( function( to ) {
			var $social_icons = $( '#top-header ul.et-social-icons, .et_slide_menu_top ul.et-social-icons' ),
				$social_icons_container = $( '#et-info' ),
				$social_icons_container_slide = $( '.et_slide_menu_top' ),
				is_slide_header = $( 'body' ).hasClass( 'et_header_style_slide' ) || $( 'body' ).hasClass( 'et_header_style_fullscreen' );

			if ( ! is_slide_header ) {
				et_maybe_create_secondary_nav();
			}

			if ( to ) {
				if ( $social_icons.length ) {
					$social_icons.show();
				} else {
					var $social_icons_ul = $( 'body' ).find( '.et_customizer_social_icons .et-social-icons' ).clone();

					$social_icons_container.append( $social_icons_ul );
					$social_icons_container_slide.prepend( $social_icons_ul );
				}
			} else {
				$social_icons.hide();
			}

			et_fix_slide_in_top_bar();
		} );
	} );

	wp.customize( 'et_divi[show_search_icon]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.removeClass( 'et_hide_search_icon' );
			} else {
				$body.addClass( 'et_hide_search_icon' );
			}

			et_fix_slide_in_top_bar();
		} );
	} );

	wp.customize( 'et_divi[show_footer_social_icons]', function( value ) {
		value.bind( function( to ) {
			var $social_icons = $('#main-footer ul.et-social-icons');

			if ( to ) {
				if ( $social_icons.length ) {
					$social_icons.show();
				} else {
					var $social_icons_ul = $( 'body' ).find( '.et_customizer_social_icons .et-social-icons' ).clone();
					$( '#footer-bottom .container' ).append( $social_icons_ul );
				}
			} else {
				$social_icons.hide();
			}
		} );
	} );

	wp.customize( 'et_divi[header_style]', function( value ) {
		value.bind( function( to ) {
			var header_style_prefix = 'et_header_style_',
				header_default_left = $( 'body' ).hasClass( 'et_boxed_layout' ) ? $( '#page-container' ).css( 'margin-left' ) : '0',
				$header_toggle = $( '#et-top-navigation .et_pb_header_toggle' );

			// Transitioning logo causes incorrect header height calculation. Hide logo to begin
			$('#logo').hide();

			et_remove_element_class( header_style_prefix );

			$( 'body' ).addClass( header_style_prefix + to );

			if ( 'slide' === to || 'fullscreen' === to ) {
				$( 'body' ).addClass( header_style_prefix + 'left' );
				if ( 'slide' === to ) {
					$header_toggle.addClass( 'et_toggle_slide_menu' ).removeClass( 'et_toggle_fullscreen_menu' );
				} else {
					$header_toggle.addClass( 'et_toggle_fullscreen_menu' ).removeClass( 'et_toggle_slide_menu' );
				}

				// fix the unwanted appearance of slide in and fullscreen menus right after the header style switch
				$( '.et_slide_in_menu_container' ).toggle();
				$( '.et_slide_in_menu_container' ).toggle();
			}

			// close the slide in and fullscreen menus if they were opened
			$( '.et_slide_in_menu_container' ).animate( { right : '-100%' } ).removeClass( 'et_pb_slide_menu_opened' );
			$( '#page-container' ).animate( { left : '0' } );
			$( '#main-header' ).animate( { left : header_default_left } );
			$( 'body' ).removeClass( 'et_pb_slide_menu_active' );
			$( 'body' ).removeClass( 'et_pb_fullscreen_menu_active' );
			$( '.et_toggle_slide_menu' ).removeClass( 'et_pb_fullscreen_menu_opened' );

			et_maybe_fix_header_style( to );

			et_slide_to_top();

			et_fix_logo_height();

			et_fix_page_container_position();

			et_fix_page_top_padding();

			et_fix_fullscreen_section();

			// Display the logo back
			$('#logo').fadeIn();
		} );
	} );

	wp.customize( 'et_divi[primary_nav_dropdown_animation]', function( value ) {
		value.bind( function( to ) {
			var primary_dropdown_animation_prefix = 'et_primary_nav_dropdown_animation_';

			et_remove_element_class( primary_dropdown_animation_prefix );

			$( 'body' ).addClass( primary_dropdown_animation_prefix + to );
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_dropdown_animation]', function( value ) {
		value.bind( function( to ) {
			var secondary_dropdown_animation_prefix = 'et_secondary_nav_dropdown_animation_';

			et_remove_element_class( secondary_dropdown_animation_prefix );

			$( 'body' ).addClass( secondary_dropdown_animation_prefix + to );
		} );
	} );


	wp.customize( 'et_divi[phone_number]', function( value ) {
		value.bind( function( to ) {
			et_maybe_create_secondary_nav();

			var $phone_number = $( '#et-info-phone' );

			if ( ! $phone_number.length ) {
				if ( ! $( '#et-info' ).length ) {
					$( '#top-header .container' ).prepend( '<div id="et-info"></div>' );
				}

				$( '#et-info' ).prepend( '<span id="et-info-phone"></span>' );

				$phone_number = $( '#et-info-phone' );
			}

			if ( to !== '' ) {
				$phone_number.show().html( to );
			} else {
				$phone_number.hide();
				et_maybe_remove_secondary_nav();
			}
		} );
	} );

	wp.customize( 'et_divi[header_email]', function( value ) {
		value.bind( function( to ) {
			et_maybe_create_secondary_nav();

			var $email = $( '#et-info-email' );

			if ( ! $email.length ) {
				if ( ! $( '#et-info' ).length ) {
					$( '#top-header .container' ).append( '<div id="et-info"></div>' );
				}

				$( '#et-info' ).append( '<span id="et-info-email"></span>' );

				$email = $( '#et-info-email' );
			}

			if ( to !== '' ) {
				$email.show().text( to );
			} else {
				$email.hide();
			}
		} );
	} );

	wp.customize( 'et_divi[primary_nav_text_color]', function( value ) {
		value.bind( function( to ) {
			var nav_color_prefix = 'et_nav_text_color_',
				element = '#main-header';

			et_remove_element_class( nav_color_prefix, element );

			$( element ).addClass( nav_color_prefix + to );
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_text_color]', function( value ) {
		value.bind( function( to ) {
			var nav_color_prefix = 'et_nav_text_color_',
				element = '#top-header';

			et_remove_element_class( nav_color_prefix, element );

			$( element ).addClass( nav_color_prefix + to );
		} );
	} );

	wp.customize( 'et_divi[vertical_nav]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body'),
				$top_navigation = $( '#et-top-navigation' ),
				main_header_bg  = $( '#main-header').css( 'background-color' ),
				menu_height     = typeof wp.customize.value( 'et_divi[menu_height]' )() === 'undefined' ? 66 : parseInt( wp.customize.value( 'et_divi[menu_height]' )() );

			$top_navigation.css( 'padding-left', 0 );

			var header_style = ( $('.et_header_style_split').length ) ? 'split' : 'not-split';

			if ( header_style == 'split' ) {
				var $et_window = $(window),
					$et_top_navigation = $('#et-top-navigation'),
					$logo_container = $('#main-header > .container > .logo_container'),
					$logo_container_splitted = $('.centered-inline-logo-wrap > .logo_container'),
					et_top_navigation_li_size = $et_top_navigation.children('nav').children('ul').children('li').size(),
					et_top_navigation_li_break_index = Math.round( et_top_navigation_li_size / 2 ) - 1;

				if ( ! to && $et_window.width() >= 980 ) {
					$('<li class="centered-inline-logo-wrap"></li>').insertAfter($et_top_navigation.find('nav > ul >li:nth('+et_top_navigation_li_break_index+')') );
					$logo_container.appendTo( $et_top_navigation.find('.centered-inline-logo-wrap') );
				} else {
					$logo_container_splitted.prependTo('#main-header > .container');
					$('#main-header .centered-inline-logo-wrap').remove();
				}
			}

			if ( to ) {
				$body.addClass( 'et_vertical_nav' );

				window.et_is_vertical_nav = true;

				if ( $body.hasClass( 'et_fixed_nav' ) ) {
					$body.removeClass( 'et_fixed_nav' ).addClass( 'et_fixed_nav_temp' );
					window.et_is_fixed_nav = false;

					$('#main-header').css( { 'transform': 'translateY(0)', 'top': '0' } );
					$('#top-header').css( { 'transform': 'translateY(0)', 'top': '0' } );
				}
			} else {
				$body.find( '#main-header' ).removeClass( '.et-fixed-header' );

				$body.removeClass( 'et_vertical_nav' );

				window.et_is_vertical_nav = false;

				if ( $body.hasClass( 'et_fixed_nav_temp' ) || $body.hasClass( 'et_vertical_fixed' ) ) {
					$body.removeClass( 'et_fixed_nav_temp et_vertical_fixed' ).addClass( 'et_fixed_nav' );

					window.et_is_fixed_nav = true;
				} else {
					window.et_is_fixed_nav = false;
				}

				et_fix_page_top_padding();
			}

			// .et_transparent_nav should only be present at <body> on this condition: horizontal nav + transparent #main-header background
			if ( ! window.et_is_vertical_nav && 'rgba' === main_header_bg.substr( 0, 4 ) ) {
				$body.addClass( 'et_transparent_nav' );
			} else {
				$body.removeClass( 'et_transparent_nav' );
			}

			// .et_fullwidth_nav should not be added on vertical_nav enabled.
			// Add et_fullwidth_nav_temp to anticipate fullwidth nav to vertical to fullwidth nav switching
			if ( to && $body.is( '.et_fullwidth_nav' ) ){
				$body.removeClass( 'et_fullwidth_nav' ).addClass( 'et_fullwidth_nav_temp' );
			} else if ( ! to && $body.is( '.et_fullwidth_nav_temp' ) ) {
				$body.removeClass( 'et_fullwidth_nav_temp' ).addClass( 'et_fullwidth_nav' );
			}

			if ( to && $body.hasClass( 'et_hide_nav') ) {
				$body.removeClass( 'et_hide_nav' ).addClass( 'et_hide_nav_temp' );
			} else if ( ! to && $body.hasClass( 'et_hide_nav_temp' ) ) {
				$body.removeClass( 'et_hide_nav_temp' ).addClass( 'et_hide_nav' );
			}

			// Add body class for navigation transition marking
			if ( to ) {
				$body.removeClass( 'nav-vertical-to-horizontal' ).addClass( 'nav-horizontal-to-vertical' );
			} else {
				$body.removeClass( 'nav-horizontal-to-vertical' ).addClass( 'nav-vertical-to-horizontal' );
			}

			// Fix menu styles
			add_menu_styles( menu_height, 'full_menu', 'not-fixed' );

			et_fix_logo_height();

			et_fix_page_container_position();

			et_fix_fullscreen_section();
		} );
	} );

	wp.customize( 'et_divi[vertical_nav_orientation]', function( value ) {
		value.bind( function( to ) {
			var $body = $( 'body' );

			if ( 'right' === to ) {
				$body.addClass( 'et_vertical_right' );
			} else {
				$body.removeClass( 'et_vertical_right' );
			}
		} );
	} );

	wp.customize( 'et_divi[hide_nav]', function( value ) {
		value.bind( function( to ) {
			var $window = $(window),
				$body = $('body'),
				$secondary_nav_height = $body.find( '#top-header' ).length ? $body.find( '#top-header' ).innerHeight() : 0,
				$pagecontainer = $body.find( '#page-container' ),
				$mainheader = $body.find( '#main-header' ),
				$topheader = $body.find( '#top-header' ),
				$hiddenheaderheight = $body.find( '#main-header' ).innerHeight() + $secondary_nav_height;

			if ( $window.width() < 981 ) {
				return;
			}

			if ( !$body.hasClass('et_hide_nav') ) {
				$body.addClass( 'et_hide_nav' );
				$pagecontainer.css( 'paddingTop', 0 );
				$mainheader.css( 'transform', 'translateY(-' + $hiddenheaderheight + 'px)' );
				$topheader.css( 'transform', 'translateY(-' + $hiddenheaderheight + 'px)' );

			} else {
				$body.removeClass( 'et_hide_nav' );
				$mainheader.css( 'transform', 'translateY(0)' );
				$topheader.css( 'transform', 'translateY(0)' );

				et_fix_page_top_padding();
			}

			et_fix_fullscreen_section();
		} );
	} );

	wp.customize( 'et_divi[hide_primary_logo]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.addClass( 'et_hide_primary_logo' );
			} else {
				$body.removeClass( 'et_hide_primary_logo' );
			}
		} );
	} );

	wp.customize( 'et_divi[hide_fixed_logo]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.addClass( 'et_hide_fixed_logo' );
			} else {
				$body.removeClass( 'et_hide_fixed_logo' );
			}
		} );
	} );

	wp.customize( 'et_divi[hide_mobile_logo]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.addClass( 'et_hide_mobile_logo' );
			} else {
				$body.removeClass( 'et_hide_mobile_logo' );
			}
		} );
	} );

	wp.customize( 'et_divi[color_schemes]', function( value ) {
		value.bind( function( to ) {
			var $body = $( 'body' ),
				body_classes = $body.attr( 'class' ),
				et_customizer_color_scheme_prefix = 'et_color_scheme_',
				body_class;

			body_class = body_classes.replace( /et_color_scheme_[^\s]+/, '' );
			$body.attr( 'class', $.trim( body_class ) );

			if ( 'none' !== to  )
				$body.addClass( et_customizer_color_scheme_prefix + to );
		} );
	} );

	wp.customize( 'et_divi[nav_fullwidth]', function( value ) {
		value.bind( function( to ) {
			var $body = $('body');

			if ( to ) {
				$body.addClass('et_fullwidth_nav');
			} else {
				$body.removeClass('et_fullwidth_nav');
			}
		} );
	} );

	wp.customize( 'et_divi[menu_height]', function( value ) {
		value.bind( function( to ) {
			// Update height data
			$('#et-top-navigation').attr( 'data-height', to );

			// Update main-header data-height-onload: it's critical for page et_fix_page_top_padding()
			et_fix_saved_main_header_height( 'initial' );

			add_menu_styles( to, 'full_menu', 'not-fixed' );

			et_slide_to_top();

			et_fix_logo_height();

			et_fix_page_top_padding();
		} );
	} );

	wp.customize( 'et_divi[logo_height]', function( value ) {
		value.bind( function( to ) {
			var header_style = typeof wp.customize.value( 'et_divi[header_style]' )() === 'undefined' ? 'left' : wp.customize.value( 'et_divi[header_style]' )();

			// Update logo height data
			$('#logo').attr( 'data-height-percentage', to );

			if ( header_style === 'split' ) {
				$('#logo').hide();
			}

			et_fix_logo_height();

			if ( header_style === 'split' ) {
				setTimeout( function() {
					$('#logo').fadeIn();
				}, 500 );
			}
		} );
	} );

	wp.customize( 'et_divi[menu_margin_top]', function( value ) {
		value.bind( function( to ) {
			var style_id = 'style#menu_margin_top',
				$style_content = '<style id="menu_margin_top">@media only screen and ( min-width: 981px ) { .et_vertical_nav #et-top-navigation { margin-top: ' + to + 'px } }</style>';

			et_customizer_update_styles( style_id, $style_content );
		} );
	} );

	wp.customize( 'et_divi[minimized_menu_height]', function( value ) {
		value.bind( function( to ) {
			// Update height data
			$('#et-top-navigation').attr( 'data-fixed-height', to );

			add_menu_styles( to, 'fixed_menu', 'fixed' );

			et_fix_logo_height();

			et_fix_page_container_position();

			et_fix_fullscreen_section();

			$(window).trigger('resize');
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_font_size]', function( value ) {
		value.bind( function( to ) {
			var social_icons_style = '<style id="header_social_icons">#top-header .et-social-icon a:before { font-size:' + to + 'px }</style>',
				$style_id = $( 'style#header_social_icons' );

			$( '#top-header, #top-header a' ).css( 'font-size', to + 'px' );
			if ( $( 'body' ).hasClass( 'et_fixed_nav' ) ) {
				$( '#main-header' ).css( 'top', $( '#top-header' ).innerHeight() );
			}

			if ( $style_id.length ) {
				$( $style_id ).replaceWith( social_icons_style );
			} else {
				$( 'head' ).append( social_icons_style );
			}
			et_slide_to_top();
			et_fix_page_top_padding();
		} );
	} );

	wp.customize( 'et_divi[primary_nav_font_size]', function( value ) {
		value.bind( function( to ) {
			var $style_content = "<style id='search_font_size'> body.et_vertical_nav .container.et_search_form_container .et-search-form input { font-size:" + to + "px !important; }\
								</style>",
				style_id = 'style#search_font_size';

			et_customizer_update_styles( style_id, $style_content );

			$( '#top-menu li a' ).css( 'font-size', to + 'px' );

		} );
	} );

	/* Module Styles Panel */

		/* Gallery */
			wp.customize( 'et_divi[et_pb_gallery-zoom_icon_color]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_gallery-zoom_icon_color]',
						'color',
						'.et_pb_gallery_image .et_overlay:before',
						to,
						'use_important'
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_gallery-hover_overlay_color]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_gallery-hover_overlay_color]',
						'background-color',
						'.et_pb_gallery_image .et_overlay',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_gallery-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_gallery-title_font_size]',
						'font-size',
						'.et_pb_gallery_grid .et_pb_gallery_item .et_pb_gallery_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_gallery-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_gallery-title_font_style]',
						'font-styles',
						'.et_pb_gallery_grid .et_pb_gallery_item .et_pb_gallery_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_gallery-caption_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_gallery-caption_font_size]',
						'font-size',
						'.et_pb_gallery .et_pb_gallery_item .et_pb_gallery_caption',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_gallery-caption_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_gallery-caption_font_style]',
						'font-styles',
						'.et_pb_gallery .et_pb_gallery_item .et_pb_gallery_caption',
						to
					);
				} );
			} );

		/* Blurb */
			wp.customize( 'et_divi[et_pb_blurb-header_font_size]', function( value ) {
				value.bind( function( to ) {
					// Print style
					et_print_module_styles_css(
						'et_divi[et_pb_blurb-header_font_size]',
						'font-size',
						'.et_pb_blurb h4',
						to
					);
				} );
			} );

		/* Tabs */
			wp.customize( 'et_divi[et_pb_tabs-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_tabs-title_font_size]',
						'font-size',
						'.et_pb_tabs_controls li',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_tabs-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_tabs-title_font_style]',
						'font-styles',
						'.et_pb_tabs_controls li',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_tabs-padding]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_tabs-padding]',
						'padding-tabs',
						'',
						to
					);
				} );
			} );

		/* Slider */
			wp.customize( 'et_divi[et_pb_slider-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_slider-header_font_size]',
						'font-size',
						'.et_pb_slider_fullwidth_off .et_pb_slide_description .et_pb_slide_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_slider-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_slider-header_font_style]',
						'font-styles',
						'.et_pb_slider_fullwidth_off .et_pb_slide_description .et_pb_slide_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_slider-body_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_slider-body_font_size]',
						'font-size',
						'.et_pb_slider_fullwidth_off .et_pb_slide_content',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_slider-body_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_slider-body_font_style]',
						'font-styles',
						'.et_pb_slider_fullwidth_off .et_pb_slide_content',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_slider-padding]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_slider-padding]',
						'padding-slider',
						'.et_pb_slider_fullwidth_off .et_pb_slide_description',
						to
					);

					et_fix_slider_height();
				} );
			} );

		/* Testimonial */
			wp.customize( 'et_divi[et_pb_testimonial-portrait_border_radius]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_testimonial-portrait_border_radius]',
						'border-radius',
						'.et_pb_testimonial_portrait, .et_pb_testimonial_portrait:before',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_testimonial-portrait_width]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_testimonial-portrait_width]',
						'width',
						'.et_pb_testimonial_portrait',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_testimonial-portrait_height]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_testimonial-portrait_height]',
						'height',
						'.et_pb_testimonial_portrait',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_testimonial-author_name_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_testimonial-author_name_font_style]',
						'font-styles',
						'.et_pb_testimonial_author',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_testimonial-author_details_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_testimonial-author_details_font_style]',
						'font-styles',
						'p.et_pb_testimonial_meta',
						to
					);
				} );
			} );

		/* Pricing Table */
			wp.customize( 'et_divi[et_pb_pricing_tables-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_pricing_tables-header_font_size]',
						'font-size',
						'.et_pb_pricing_heading h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_pricing_tables-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_pricing_tables-header_font_style]',
						'font-styles',
						'.et_pb_pricing_heading h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_pricing_tables-subheader_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_pricing_tables-subheader_font_size]',
						'font-size',
						'.et_pb_best_value',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_pricing_tables-subheader_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_pricing_tables-subheader_font_style]',
						'font-styles',
						'.et_pb_best_value',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_pricing_tables-price_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_pricing_tables-price_font_size]',
						'font-size',
						'.et_pb_sum',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_pricing_tables-price_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_pricing_tables-price_font_style]',
						'font-styles',
						'.et_pb_sum',
						to
					);
				} );
			} );

		/* Call to Action */
			wp.customize( 'et_divi[et_pb_cta-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_cta-header_font_size]',
						'font-size',
						'.et_pb_promo h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_cta-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_cta-header_font_style]',
						'font-styles',
						'.et_pb_promo h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_cta-custom_padding]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_cta-custom_padding]',
						'padding-call-to-action',
						'',
						to
					);
				} );
			} );

		/* Audio */
			wp.customize( 'et_divi[et_pb_audio-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_audio-title_font_size]',
						'font-size',
						'.et_pb_audio_module_content h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_audio-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_audio-title_font_style]',
						'font-styles',
						'.et_pb_audio_module_content h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_audio-caption_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_audio-caption_font_size]',
						'font-size',
						'.et_pb_audio_module p',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_audio-caption_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_audio-caption_font_style]',
						'font-styles',
						'.et_pb_audio_module p',
						to
					);
				} );
			} );

		/* Email Optin */
			wp.customize( 'et_divi[et_pb_signup-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_signup-header_font_size]',
						'font-size',
						'.et_pb_subscribe h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_signup-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_signup-header_font_style]',
						'font-styles',
						'.et_pb_subscribe h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_signup-padding]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_signup-padding]',
						'padding',
						'.et_pb_subscribe',
						to
					);
				} );
			} );

		/* Login */
			wp.customize( 'et_divi[et_pb_login-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_login-header_font_size]',
						'font-size',
						'.et_pb_login h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_login-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_login-header_font_style]',
						'font-styles',
						'.et_pb_login h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_login-custom_padding]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_login-custom_padding]',
						'padding',
						'.et_pb_login',
						to
					);
				} );
			} );

		/* Portfolio */
			wp.customize( 'et_divi[et_pb_portfolio-zoom_icon_color]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_portfolio-zoom_icon_color]',
						'color',
						'.et_pb_portfolio .et_overlay:before, .et_pb_fullwidth_portfolio .et_overlay:before, .et_pb_portfolio_grid .et_overlay:before',
						to,
						'use_important'
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_portfolio-hover_overlay_color]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_portfolio-hover_overlay_color]',
						'background-color',
						'.et_pb_portfolio .et_overlay, .et_pb_fullwidth_portfolio .et_overlay, .et_pb_portfolio_grid .et_overlay',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_portfolio-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_portfolio-title_font_size]',
						'font-size',
						'.et_pb_portfolio .et_pb_portfolio_item h2, .et_pb_fullwidth_portfolio .et_pb_portfolio_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_portfolio-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_portfolio-title_font_style]',
						'font-styles',
						'.et_pb_portfolio .et_pb_portfolio_item h2, .et_pb_fullwidth_portfolio .et_pb_portfolio_item h3, .et_pb_portfolio_grid .et_pb_portfolio_item h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_portfolio-caption_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_portfolio-caption_font_size]',
						'font-size',
						'.et_pb_portfolio .et_pb_portfolio_item .post-meta, .et_pb_fullwidth_portfolio .et_pb_portfolio_item .post-meta, .et_pb_portfolio_grid .et_pb_portfolio_item .post-meta',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_portfolio-caption_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_portfolio-caption_font_style]',
						'font-styles',
						'.et_pb_portfolio .et_pb_portfolio_item .post-meta, .et_pb_fullwidth_portfolio .et_pb_portfolio_item .post-meta, .et_pb_portfolio_grid .et_pb_portfolio_item .post-meta',
						to
					);
				} );
			} );

		/* Filterable Portfolio */
			wp.customize( 'et_divi[et_pb_filterable_portfolio-zoom_icon_color]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_filterable_portfolio-zoom_icon_color]',
						'color',
						'.et_pb_filterable_portfolio .et_overlay:before',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_filterable_portfolio-hover_overlay_color]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_filterable_portfolio-hover_overlay_color]',
						'background-color',
						'.et_pb_filterable_portfolio .et_overlay',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_filterable_portfolio-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_filterable_portfolio-title_font_size]',
						'font-size',
						'.et_pb_filterable_portfolio .et_pb_portfolio_item h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_filterable_portfolio-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_filterable_portfolio-title_font_style]',
						'font-styles',
						'.et_pb_filterable_portfolio .et_pb_portfolio_item h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_filterable_portfolio-caption_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_filterable_portfolio-caption_font_size]',
						'font-size',
						'.et_pb_filterable_portfolio .et_pb_portfolio_item .post-meta',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_filterable_portfolio-caption_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_filterable_portfolio-caption_font_style]',
						'font-styles',
						'.et_pb_filterable_portfolio .et_pb_portfolio_item .post-meta',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_filterable_portfolio-filter_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_filterable_portfolio-filter_font_size]',
						'font-size',
						'.et_pb_filterable_portfolio .et_pb_portfolio_filters li',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_filterable_portfolio-filter_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_filterable_portfolio-filter_font_style]',
						'font-styles',
						'.et_pb_filterable_portfolio .et_pb_portfolio_filters li',
						to
					);
				} );
			} );

		/* Bar Counter */
			wp.customize( 'et_divi[et_pb_counters-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_counters-title_font_size]',
						'font-size',
						'.et_pb_counters .et_pb_counter_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_counters-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_counters-title_font_style]',
						'font-styles',
						'.et_pb_counters .et_pb_counter_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_counters-percent_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_counters-percent_font_size]',
						'font-size',
						'.et_pb_counters .et_pb_counter_amount',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_counters-percent_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_counters-percent_font_style]',
						'font-styles',
						'.et_pb_counters .et_pb_counter_amount',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_counters-border_radius]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_counters-border_radius]',
						'border-radius',
						'.et_pb_counters .et_pb_counter_amount, .et_pb_counters .et_pb_counter_container',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_counters-padding]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_counters-padding]',
						'padding',
						'.et_pb_counter_amount',
						to
					);
				} );
			} );

		/* Circle Counter */
			wp.customize( 'et_divi[et_pb_circle_counter-number_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_circle_counter-number_font_size]',
						'font-size',
						'.et_pb_circle_counter .percent p',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_circle_counter-number_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_circle_counter-number_font_style]',
						'font-styles',
						'.et_pb_circle_counter .percent p',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_circle_counter-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_circle_counter-title_font_size]',
						'font-size',
						'.et_pb_circle_counter h3',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_circle_counter-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_circle_counter-title_font_style]',
						'font-styles',
						'.et_pb_circle_counter h3',
						to
					);
				} );
			} );

		/* Number Counter */
			wp.customize( 'et_divi[et_pb_number_counter-number_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_number_counter-number_font_size]',
						'font-size',
						'.et_pb_number_counter .percent p',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_number_counter-number_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_number_counter-number_font_style]',
						'font-styles',
						'.et_pb_number_counter .percent p',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_number_counter-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_number_counter-title_font_size]',
						'font-size',
						'.et_pb_number_counter h3',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_number_counter-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_number_counter-title_font_style]',
						'font-styles',
						'.et_pb_number_counter h3',
						to
					);
				} );
			} );

		/* Accordion */
			wp.customize( 'et_divi[et_pb_accordion-toggle_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_accordion-toggle_font_size]',
						'font-size',
						'.et_pb_accordion .et_pb_toggle_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_accordion-toggle_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_accordion-toggle_font_style]',
						'font-styles',
						'.et_pb_accordion .et_pb_toggle.et_pb_toggle_open .et_pb_toggle_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_accordion-inactive_toggle_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_accordion-inactive_toggle_font_style]',
						'font-styles',
						'.et_pb_accordion .et_pb_toggle.et_pb_toggle_close .et_pb_toggle_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_accordion-toggle_icon_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_accordion-toggle_icon_size]',
						'font-size',
						'.et_pb_accordion .et_pb_toggle_title:before',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_accordion-custom_padding]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_accordion-custom_padding]',
						'padding',
						'.et_pb_accordion .et_pb_toggle_open, .et_pb_accordion .et_pb_toggle_close',
						to
					);
				} );
			} );

		/* Toggle */
			wp.customize( 'et_divi[et_pb_toggle-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_toggle-title_font_size]',
						'font-size',
						'.et_pb_toggle.et_pb_toggle_item h5',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_toggle-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_toggle-title_font_style]',
						'font-styles',
						'.et_pb_toggle.et_pb_toggle_item.et_pb_toggle_open h5',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_toggle-inactive_title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_toggle-inactive_title_font_style]',
						'font-styles',
						'.et_pb_toggle.et_pb_toggle_item.et_pb_toggle_close h5',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_toggle-toggle_icon_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_toggle-toggle_icon_size]',
						'font-size',
						'.et_pb_toggle.et_pb_toggle_item .et_pb_toggle_title:before',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_toggle-custom_padding]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_toggle-custom_padding]',
						'padding',
						'.et_pb_toggle.et_pb_toggle_item',
						to
					);
				} );
			} );

		/* Contact Form */
			wp.customize( 'et_divi[et_pb_contact_form-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_contact_form-title_font_size]',
						'font-size',
						'.et_pb_contact_form_container .et_pb_contact_main_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_contact_form-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_contact_form-title_font_style]',
						'font-styles',
						'.et_pb_contact_form_container .et_pb_contact_main_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_contact_form-form_field_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_contact_form-form_field_font_size]',
						'font-size',
						'.et_pb_contact_form_container .et_pb_contact p input, .et_pb_contact_form_container .et_pb_contact p textarea',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_contact_form-form_field_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_contact_form-form_field_font_style]',
						'font-styles',
						'.et_pb_contact_form_container .et_pb_contact p input, .et_pb_contact_form_container .et_pb_contact p textarea',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_contact_form-captcha_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_contact_form-captcha_font_size]',
						'font-size',
						'.et_pb_contact_captcha_question',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_contact_form-captcha_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_contact_form-captcha_font_style]',
						'font-styles',
						'.et_pb_contact_captcha_question',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_contact_form-padding]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_contact_form-padding]',
						'padding',
						'.et_pb_contact p input, .et_pb_contact p textarea',
						to
					);
				} );
			} );

		/* Sidebar */
			wp.customize( 'et_divi[et_pb_sidebar-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_sidebar-header_font_style]',
						'font-styles',
						'.et_pb_widget_area h4',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_sidebar-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_sidebar-header_font_size]',
						'font-size',
						'.et_pb_widget_area h4',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_sidebar-remove_border]', function( value ) {
				value.bind( function( to ) {
					if ( to ){
						$('body').addClass( 'et_pb_no_sidebar_vertical_divider' );
					} else {
						$('body').removeClass( 'et_pb_no_sidebar_vertical_divider' );
					}
				} );
			} );

		/* Person */
			wp.customize( 'et_divi[et_pb_team_member-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_team_member-header_font_size]',
						'font-size',
						'.et_pb_team_member h4',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_team_member-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_team_member-header_font_style]',
						'font-styles',
						'.et_pb_team_member h4',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_team_member-subheader_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_team_member-subheader_font_size]',
						'font-size',
						'.et_pb_team_member .et_pb_member_position',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_team_member-subheader_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_team_member-subheader_font_style]',
						'font-styles',
						'.et_pb_team_member .et_pb_member_position',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_team_member-social_network_icon_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_team_member-social_network_icon_size]',
						'font-size',
						'.et_pb_member_social_links a',
						to
					);
				} );
			} );

		/* Divider */
			wp.customize( 'et_divi[et_pb_divider-divider_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_divider-divider_style]',
						'border-top-style',
						'.et_pb_space:before',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_divider-divider_weight]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_divider-divider_weight]',
						'border-top-width',
						'.et_pb_space:before',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_divider-height]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_divider-height]',
						'height',
						'.et_pb_space',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_divider-divider_position]', function( value ) {
				value.bind( function( to ) {
					$('.customized_et_pb_divider_position').removeClass( function( index, css ){
						return ( css.match(/\bet_pb_divider_position_\S+/g) || [] ).join(' ');
					} ).addClass( "et_pb_divider_position_" + to );
				} );
			} );

		/* Blog */
			wp.customize( 'et_divi[et_pb_blog-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_blog-header_font_size]',
						'font-size',
						'.et_pb_posts .et_pb_post h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_blog-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_blog-header_font_style]',
						'font-styles',
						'.et_pb_posts .et_pb_post h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_blog-meta_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_blog-meta_font_size]',
						'font-size',
						'.et_pb_posts .et_pb_post .post-meta',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_blog-meta_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_blog-meta_font_style]',
						'font-styles',
						'.et_pb_posts .et_pb_post .post-meta',
						to
					);
				} );
			} );

		/* Blog Grid */
			wp.customize( 'et_divi[et_pb_blog_masonry-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_blog_masonry-header_font_size]',
						'font-size',
						'.et_pb_blog_grid .et_pb_post h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_blog_masonry-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_blog_masonry-header_font_style]',
						'font-styles',
						'.et_pb_blog_grid .et_pb_post h2',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_blog_masonry-meta_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_blog_masonry-meta_font_size]',
						'font-size',
						'.et_pb_blog_grid .et_pb_post .post-meta',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_blog_masonry-meta_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_blog_masonry-meta_font_style]',
						'font-styles',
						'.et_pb_blog_grid .et_pb_post .post-meta',
						to
					);
				} );
			} );

		/* Shop */
			wp.customize( 'et_divi[et_pb_shop-title_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_shop-title_font_size]',
						'font-size',
						'.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_shop-title_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_shop-title_font_style]',
						'font-styles',
						'.woocommerce ul.products li.product h3, .woocommerce-page ul.products li.product h3',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_shop-sale_badge_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_shop-sale_badge_font_size]',
						'font-size',
						'.woocommerce span.onsale, .woocommerce-page span.onsale',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_shop-sale_badge_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_shop-sale_badge_font_style]',
						'font-styles',
						'.woocommerce span.onsale, .woocommerce-page span.onsale',
						to,
						'use_important'
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_shop-price_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_shop-price_font_size]',
						'font-size',
						'.woocommerce ul.products li.product .price .amount, .woocommerce-page ul.products li.product .price .amount',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_shop-price_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_shop-price_font_style]',
						'font-styles',
						'.woocommerce ul.products li.product .price .amount, .woocommerce-page ul.products li.product .price .amount',
						to,
						'use_important'
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_shop-sale_price_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_shop-sale_price_font_size]',
						'font-size',
						'.woocommerce ul.products li.product .price ins .amount, .woocommerce-page ul.products li.product .price ins .amount',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_shop-sale_price_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_shop-sale_price_font_style]',
						'font-styles',
						'.woocommerce ul.products li.product .price ins .amount, .woocommerce-page ul.products li.product .price ins .amount',
						to,
						'use_important'
					);
				} );
			} );

		/* Countdown */
			wp.customize( 'et_divi[et_pb_countdown_timer-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_countdown_timer-header_font_size]',
						'font-size',
						'.et_pb_countdown_timer .title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_countdown_timer-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_countdown_timer-header_font_style]',
						'font-styles',
						'.et_pb_countdown_timer .title',
						to
					);
				} );
			} );

		/* Social */
			wp.customize( 'et_divi[et_pb_social_media_follow-button_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_social_media_follow-button_font_style]',
						'font-styles',
						'.et_pb_social_media_follow li a.follow_button',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_social_media_follow-icon_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_social_media_follow-icon_size]',
						'social-icon-size',
						'',
						to
					);
				} );
			} );

		/* Fullwidth Slider */
			wp.customize( 'et_divi[et_pb_fullwidth_slider-header_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_fullwidth_slider-header_font_size]',
						'font-size',
						'.et_pb_fullwidth_section .et_pb_slide_description .et_pb_slide_title',
						to
					);

					et_fix_slider_height();
				} );
			} );

			wp.customize( 'et_divi[et_pb_fullwidth_slider-header_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_fullwidth_slider-header_font_style]',
						'font-styles',
						'.et_pb_fullwidth_section .et_pb_slide_description .et_pb_slide_title',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_fullwidth_slider-body_font_size]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_fullwidth_slider-body_font_size]',
						'font-size',
						'.et_pb_fullwidth_section .et_pb_slide_content',
						to
					);

					et_fix_slider_height();
				} );
			} );

			wp.customize( 'et_divi[et_pb_fullwidth_slider-body_font_style]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_fullwidth_slider-body_font_style]',
						'font-styles',
						'.et_pb_fullwidth_section .et_pb_slide_content',
						to
					);
				} );
			} );

			wp.customize( 'et_divi[et_pb_fullwidth_slider-padding]', function( value ) {
				value.bind( function( to ) {
					et_print_module_styles_css(
						'et_divi[et_pb_fullwidth_slider-padding]',
						'padding-slider',
						'.et_pb_fullwidth_section .et_pb_slide_description',
						to
					);

					et_fix_slider_height();
				} );
			} );

	/* Blog Panel */
		wp.customize( 'et_divi[post_meta_font_size]', function( value ) {
			value.bind( function( to ) {
				var $style_content = "<style id='post_meta_font_size'>@media only screen and ( min-width: 981px ) { \
									body.home-posts #left-area .et_pb_post .post-meta,\
									body.archive #left-area .et_pb_post .post-meta,\
									body.search #left-area .et_pb_post .post-meta,\
									body.single #left-area .et_pb_post .post-meta { font-size:" + to + "px; }\
									}</style>",
					style_id = 'style#post_meta_font_size';

				et_customizer_update_styles( style_id, $style_content );
			} );
		} );

		wp.customize( 'et_divi[post_meta_height]', function( value ) {
			value.bind( function( to ) {
				var $style_content = "<style id='post_meta_height'> \
									body.home-posts #left-area .et_pb_post .post-meta,\
									body.archive #left-area .et_pb_post .post-meta,\
									body.search #left-area .et_pb_post .post-meta,\
									body.single #left-area .et_pb_post .post-meta { line-height: " + to + "em; }\
									</style>",
					style_id = 'style#post_meta_height';

				et_customizer_update_styles( style_id, $style_content );
			} );
		} );

		wp.customize( 'et_divi[post_meta_spacing]', function( value ) {
			value.bind( function( to ) {
				var $style_content = "<style id='post_meta_spacing'> \
									body.home-posts #left-area .et_pb_post .post-meta,\
									body.archive #left-area .et_pb_post .post-meta,\
									body.search #left-area .et_pb_post .post-meta,\
									body.single #left-area .et_pb_post .post-meta { letter-spacing: " + to + "px; }\
									</style>",
					style_id = 'style#post_meta_spacing';

				et_customizer_update_styles( style_id, $style_content );
			} );
		} );

		wp.customize( 'et_divi[post_meta_style]', function( value ) {
			value.bind( function( to ) {
				var styles = et_set_font_styles( to, '' ),
					$button_style = '<style id="post_meta_style"> \
									body.home-posts #left-area .et_pb_post .post-meta,\
									body.archive #left-area .et_pb_post .post-meta,\
									body.search #left-area .et_pb_post .post-meta,\
									body.single #left-area .et_pb_post .post-meta {' + styles + '}</style>',
					style_id = 'style#post_meta_style';

				et_customizer_update_styles( style_id, $button_style );
			} );
		} );

		wp.customize( 'et_divi[post_header_font_size]', function( value ) {
			value.bind( function( to ) {
				var $style_content = "<style id='post_header_font_size'>@media only screen and ( min-width: 981px ) {\
									body.home-posts #left-area .et_pb_post h2,\
									body.archive #left-area .et_pb_post h2,\
									body.search #left-area .et_pb_post h2 { font-size:" + parseInt( to ) * ( 26 / 30 ) + "px }\
									body.single .et_post_meta_wrapper h1 { font-size:" + to + "px; }\
									}</style>",
					style_id = 'style#post_header_font_size';

				et_customizer_update_styles( style_id, $style_content );
			} );
		} );

		wp.customize( 'et_divi[post_header_height]', function( value ) {
			value.bind( function( to ) {
				var $style_content = "<style id='post_header_height'>\
									body.home-posts #left-area .et_pb_post h2,\
									body.archive #left-area .et_pb_post h2,\
									body.search #left-area .et_pb_post h2,\
									body.single .et_post_meta_wrapper h1 { line-height: " + to + "em; }\
									</style>",
					style_id = 'style#post_header_height';

				et_customizer_update_styles( style_id, $style_content );
			} );
		} );

		wp.customize( 'et_divi[post_header_spacing]', function( value ) {
			value.bind( function( to ) {
				var $style_content = "<style id='post_header_spacing'>\
									body.home-posts #left-area .et_pb_post h2,\
									body.archive #left-area .et_pb_post h2,\
									body.search #left-area .et_pb_post h2,\
									body.single .et_post_meta_wrapper h1 { letter-spacing: " + to + "px; }\
									</style>",
					style_id = 'style#post_header_spacing';

				et_customizer_update_styles( style_id, $style_content );
			} );
		} );

		wp.customize( 'et_divi[post_header_style]', function( value ) {
			value.bind( function( to ) {
				var styles = et_set_font_styles( to, '' ),
					$button_style = '<style id="post_header_style">\
									body.home-posts #left-area .et_pb_post h2,\
									body.archive #left-area .et_pb_post h2,\
									body.search #left-area .et_pb_post h2,\
									body.single .et_post_meta_wrapper h1 {' + styles + '}\
									</style>',
					style_id = 'style#post_header_style';

				et_customizer_update_styles( style_id, $button_style );
			} );
		} );

	wp.customize( 'et_divi[all_buttons_font_size]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_icon_font_size">body #page-container .et_pb_button{ font-size: ' + to + 'px; } body #page-container .et_pb_button:after, .woocommerce a.button.alt:after, .woocommerce-page a.button.alt:after, .woocommerce button.button.alt:after, .woocommerce-page button.button.alt:after, .woocommerce input.button.alt:after, .woocommerce-page input.button.alt:after, .woocommerce #respond input#submit.alt:after, .woocommerce-page #respond input#submit.alt:after, .woocommerce #content input.button.alt:after, .woocommerce-page #content input.button.alt:after, .woocommerce a.button:after, .woocommerce-page a.button:after, .woocommerce button.button:after, .woocommerce-page button.button:after, .woocommerce input.button:after, .woocommerce-page input.button:after, .woocommerce #respond input#submit:after, .woocommerce-page #respond input#submit:after, .woocommerce #content input.button:after, .woocommerce-page #content input.button:after { font-size:' + parseInt( to ) * 1.6 + 'px; } body.et_button_custom_icon #page-container .et_pb_button:after{ font-size:' + to + 'px; } </style>',
				style_id = 'style#buttons_icon_font_size';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_text_color]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = "<style id='buttons_text_color'> body.et_pb_button_helper_class #page-container .et_pb_button,\
									.woocommerce.et_pb_button_helper_class a.button.alt, .woocommerce-page.et_pb_button_helper_class a.button.alt, .woocommerce.et_pb_button_helper_class button.button.alt, .woocommerce-page.et_pb_button_helper_class button.button.alt, .woocommerce.et_pb_button_helper_class input.button.alt, .woocommerce-page.et_pb_button_helper_class input.button.alt, .woocommerce.et_pb_button_helper_class #respond input#submit.alt, .woocommerce-page.et_pb_button_helper_class #respond input#submit.alt, .woocommerce.et_pb_button_helper_class #content input.button.alt, .woocommerce-page.et_pb_button_helper_class #content input.button.alt,\
									.woocommerce.et_pb_button_helper_class a.button, .woocommerce-page.et_pb_button_helper_class a.button, .woocommerce.et_pb_button_helper_class button.button, .woocommerce-page.et_pb_button_helper_class button.button, .woocommerce.et_pb_button_helper_class input.button, .woocommerce-page.et_pb_button_helper_class input.button, .woocommerce.et_pb_button_helper_class #respond input#submit, .woocommerce-page.et_pb_button_helper_class #respond input#submit, .woocommerce.et_pb_button_helper_class #content input.button, .woocommerce-page.et_pb_button_helper_class #content input.button { color:" + to + ";}\
								</style>",
				style_id = 'style#buttons_text_color';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_bg_color]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_bg_color">body #page-container .et_pb_button, .woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button { background:' + to + ';}</style>',
				style_id = 'style#buttons_bg_color';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_border_width]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_border_width">body #page-container .et_pb_button, .woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button { border-width:' + to + 'px !important; }</style>',
				style_id = 'style#buttons_border_width';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_border_color]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_border_color">body #page-container .et_pb_button, .woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button { border-color:' + to + ';}</style>',
				style_id = 'style#buttons_border_color';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_border_radius]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_border_radius">body #page-container .et_pb_button, .woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button { border-radius:' + to + 'px;}</style>',
				style_id = 'style#buttons_border_radius';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_font_style]', function( value ) {
		value.bind( function( to ) {
			var styles = et_set_font_styles( to, '' ),
				$button_style = '<style id="buttons_font_style">body #page-container .et_pb_button, .woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button {' + styles + '}</style>',
				style_id = 'style#buttons_font_style';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[primary_nav_font_style]', function( value ) {
		value.bind( function( to ) {
			var styles = et_set_font_styles( to, '' ),
				$button_style = "<style id='primary_nav_font_style'> #top-menu li a, .et_search_form_container input {" + styles + "}\
									.et_search_form_container input::-moz-placeholder { " + styles + " }\
									.et_search_form_container input::-webkit-input-placeholder { " + styles + " }\
									.et_search_form_container input:-ms-input-placeholder { " + styles + " }\
								</style>",
				style_id = 'style#primary_nav_font_style';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[secondary_nav_font_style]', function( value ) {
		value.bind( function( to ) {
			var styles = et_set_font_styles( to, '' ),
				$button_style = '<style id="secondary_nav_font_style"> #top-header, #top-header a, #et-secondary-nav li li a, #top-header .et-social-icon a:before {' + styles + '}</style>',
				style_id = 'style#secondary_nav_font_style';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[body_header_style]', function( value ) {
		value.bind( function( to ) {
			var styles = et_set_font_styles( to, '' ),
				$button_style = '<style id="body_header_style"> h1, h2, h3, h4, h5, h6, .et_quote_content blockquote p, .et_pb_slide_description .et_pb_slide_title {' + styles + '}</style>',
				style_id = 'style#body_header_style';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_selected_icon]', function( value ) {
		value.bind( function( to ) {
			var	button_font_size = $( '.et_pb_button' ).css( 'font-size' ),
				$button_style = "<style id='buttons_icon'>body #page-container .et_pb_button:after, .woocommerce a.button.alt:after, .woocommerce-page a.button.alt:after, .woocommerce button.button.alt:after, .woocommerce-page button.button.alt:after, .woocommerce input.button.alt:after, .woocommerce-page input.button.alt:after, .woocommerce #respond input#submit.alt:after, .woocommerce-page #respond input#submit.alt:after, .woocommerce #content input.button.alt:after, .woocommerce-page #content input.button.alt:after, .woocommerce a.button:after, .woocommerce-page a.button:after, .woocommerce button.button:after, .woocommerce-page button.button:after, .woocommerce input.button:after, .woocommerce-page input.button:after, .woocommerce #respond input#submit:after, .woocommerce-page #respond input#submit:after, .woocommerce #content input.button:after, .woocommerce-page #content input.button:after { font-size:" + button_font_size + ";",
				style_id = 'style#buttons_icon';

			if ( "'" === to ) {
				$button_style += 'content:"' + to + '";'
			} else {
				$button_style += "content:'" + to + "';"
			}

			$button_style += "}</style>";


			et_customizer_update_styles( style_id, $button_style );

			if ( '5' !== to ) {
				$( 'body' ).addClass( 'et_button_custom_icon' );
			} else {
				$( 'body' ).removeClass( 'et_button_custom_icon' );
			}

		} );
	} );

	wp.customize( 'et_divi[all_buttons_icon_color]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_icon_color">body #page-container .et_pb_button:after, .woocommerce a.button.alt:after, .woocommerce-page a.button.alt:after, .woocommerce button.button.alt:after, .woocommerce-page button.button.alt:after, .woocommerce input.button.alt:after, .woocommerce-page input.button.alt:after, .woocommerce #respond input#submit.alt:after, .woocommerce-page #respond input#submit.alt:after, .woocommerce #content input.button.alt:after, .woocommerce-page #content input.button.alt:after, .woocommerce a.button:after, .woocommerce-page a.button:after, .woocommerce button.button:after, .woocommerce-page button.button:after, .woocommerce input.button:after, .woocommerce-page input.button:after, .woocommerce #respond input#submit:after, .woocommerce-page #respond input#submit:after, .woocommerce #content input.button:after, .woocommerce-page #content input.button:after { color:' + to + ';}</style>',
				style_id = 'style#buttons_icon_color';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_icon_placement]', function( value ) {
		value.bind( function( to ) {
			if ( 'left' === to ) {
				$( 'body' ).addClass( 'et_button_left' );
			} else {
				$( 'body' ).removeClass( 'et_button_left' );
			}
		} );
	} );

	wp.customize( 'et_divi[all_buttons_icon_hover]', function( value ) {
		value.bind( function( to ) {
			if ( 'no' === to ) {
				$( 'body' ).addClass( 'et_button_icon_visible' );
			} else {
				$( 'body' ).removeClass( 'et_button_icon_visible' );
			}
		} );
	} );

	wp.customize( 'et_divi[all_buttons_icon]', function( value ) {
		value.bind( function( to ) {
			if ( 'no' === to ) {
				$( 'body' ).addClass( 'et_button_no_icon' );
			} else {
				$( 'body' ).removeClass( 'et_button_no_icon' );
			}
		} );
	} );

	wp.customize( 'et_divi[all_buttons_text_color_hover]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_text_color_hover">body #page-container .et_pb_button:hover, .woocommerce a.button.alt:hover, .woocommerce-page a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce-page button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce-page input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce #content input.button.alt:hover, .woocommerce-page #content input.button.alt:hover, .woocommerce a.button:hover, .woocommerce-page a.button:hover, .woocommerce button.button:hover, .woocommerce-page button.button:hover, .woocommerce input.button:hover, .woocommerce-page input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce-page #respond input#submit:hover, .woocommerce #content input.button:hover, .woocommerce-page #content input.button:hover { color: ' + to + ' !important; } </style>',
				style_id = 'style#buttons_text_color_hover';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_bg_color_hover]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_bg_color_hover">body #page-container .et_pb_button:hover, .woocommerce a.button.alt:hover, .woocommerce-page a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce-page button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce-page input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce #content input.button.alt:hover, .woocommerce-page #content input.button.alt:hover, .woocommerce a.button:hover, .woocommerce-page a.button:hover, .woocommerce button.button, .woocommerce-page button.button:hover, .woocommerce input.button:hover, .woocommerce-page input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce-page #respond input#submit:hover, .woocommerce #content input.button:hover, .woocommerce-page #content input.button:hover { background: ' + to + ' !important; } </style>',
				style_id = 'style#buttons_bg_color_hover';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_border_color_hover]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_border_color_hover">body #page-container .et_pb_button:hover, .woocommerce a.button.alt:hover, .woocommerce-page a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce-page button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce-page input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce #content input.button.alt:hover, .woocommerce-page #content input.button.alt:hover, .woocommerce a.button:hover, .woocommerce-page a.button:hover, .woocommerce button.button, .woocommerce-page button.button:hover, .woocommerce input.button:hover, .woocommerce-page input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce-page #respond input#submit:hover, .woocommerce #content input.button:hover, .woocommerce-page #content input.button:hover { border-color: ' + to + ' !important; } </style>',
				style_id = 'style#buttons_border_color_hover';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_border_radius_hover]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_border_radius_hover">body #page-container .et_pb_button:hover, .woocommerce a.button.alt:hover, .woocommerce-page a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce-page button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce-page input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce #content input.button.alt:hover, .woocommerce-page #content input.button.alt:hover, .woocommerce a.button:hover, .woocommerce-page a.button:hover, .woocommerce button.button, .woocommerce-page button.button:hover, .woocommerce input.button:hover, .woocommerce-page input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce-page #respond input#submit:hover, .woocommerce #content input.button:hover, .woocommerce-page #content input.button:hover { border-radius: ' + to + 'px !important; } </style>',
				style_id = 'style#buttons_border_radius_hover';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_spacing]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_spacing">body #page-container .et_pb_button, .woocommerce a.button.alt, .woocommerce-page a.button.alt, .woocommerce button.button.alt, .woocommerce-page button.button.alt, .woocommerce input.button.alt, .woocommerce-page input.button.alt, .woocommerce #respond input#submit.alt, .woocommerce-page #respond input#submit.alt, .woocommerce #content input.button.alt, .woocommerce-page #content input.button.alt, .woocommerce a.button, .woocommerce-page a.button, .woocommerce button.button, .woocommerce-page button.button, .woocommerce input.button, .woocommerce-page input.button, .woocommerce #respond input#submit, .woocommerce-page #respond input#submit, .woocommerce #content input.button, .woocommerce-page #content input.button { letter-spacing: ' + to + 'px; } </style>',
				style_id = 'style#buttons_spacing';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[all_buttons_spacing_hover]', function( value ) {
		value.bind( function( to ) {
			var	$button_style = '<style id="buttons_spacing_hover">body #page-container .et_pb_button:hover, .woocommerce a.button.alt:hover, .woocommerce-page a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce-page button.button.alt:hover, .woocommerce input.button.alt:hover, .woocommerce-page input.button.alt:hover, .woocommerce #respond input#submit.alt:hover, .woocommerce-page #respond input#submit.alt:hover, .woocommerce #content input.button.alt:hover, .woocommerce-page #content input.button.alt:hover, .woocommerce a.button:hover, .woocommerce-page a.button:hover, .woocommerce button.button, .woocommerce-page button.button:hover, .woocommerce input.button:hover, .woocommerce-page input.button:hover, .woocommerce #respond input#submit:hover, .woocommerce-page #respond input#submit:hover, .woocommerce #content input.button:hover, .woocommerce-page #content input.button:hover { letter-spacing: ' + to + 'px; } </style>',
				style_id = 'style#buttons_spacing_hover';

			et_customizer_update_styles( style_id, $button_style );
		} );
	} );

	wp.customize( 'et_divi[slide_nav_show_top_bar]', function( value ) {
		value.bind( function( to ) {
			var $style_content,
				style_id = 'style#slide_nav_show_top_bar';

			if ( to ) {
				$style_content = '<style id="slide_nav_show_top_bar"></style>';
				$( 'body' ).removeClass( 'et_pb_no_top_bar_fullscreen' );
			} else {
				$style_content = '<style id="slide_nav_show_top_bar">.et_slide_menu_top{ display: none; }</style>';
				$( 'body' ).addClass( 'et_pb_no_top_bar_fullscreen' );
			}

			et_customizer_update_styles( style_id, $style_content );
		} );
	} );

	wp.customize( 'et_divi[slide_nav_bg]', function( value ) {
		value.bind( function( to ) {
			var $style_content = '<style id="slide_nav_bg">body #page-container .et_slide_in_menu_container{ background: ' + to + '; } </style>',
				style_id = 'style#slide_nav_bg';

			et_customizer_update_styles( style_id, $style_content );
		} );
	} );

	wp.customize( 'et_divi[slide_nav_links_color]', function( value ) {
		value.bind( function( to ) {
			var $style_content = '<style id="slide_nav_links_color">.et_slide_in_menu_container #mobile_menu_slide li span.et_mobile_menu_arrow:before, .et_slide_in_menu_container #mobile_menu_slide li a { color: ' + to + '; } </style>',
				style_id = 'style#slide_nav_links_color';

			et_customizer_update_styles( style_id, $style_content );
		} );
	} );

	wp.customize( 'et_divi[slide_nav_links_color_active]', function( value ) {
		value.bind( function( to ) {
			var $style_content = '<style id="slide_nav_links_color_active">.et_slide_in_menu_container #mobile_menu_slide li.current-menu-item span.et_mobile_menu_arrow:before, .et_slide_in_menu_container #mobile_menu_slide li.current-menu-item a { color: ' + to + '; } </style>',
				style_id = 'style#slide_nav_links_color_active';

			et_customizer_update_styles( style_id, $style_content );
		} );
	} );

	wp.customize( 'et_divi[slide_nav_top_color]', function( value ) {
		value.bind( function( to ) {
			var $style_content = "<style id='slide_nav_top_color'>.et_slide_in_menu_container .et_slide_menu_top, .et_slide_in_menu_container .et_slide_menu_top a, .et_slide_in_menu_container .et_slide_menu_top input { color: " + to + "; } \
					.et_slide_in_menu_container .et_slide_menu_top .et-search-form input, .et_slide_in_menu_container .et_slide_menu_top .et-search-form button#searchsubmit_header:before { color: " + to + "; } \
					.et_slide_in_menu_container .et_slide_menu_top .et-search-form input::-webkit-input-placeholder { color: " + to + "; } \
					.et_slide_in_menu_container .et_slide_menu_top .et-search-form input::-moz-placeholder { color: " + to + "; } \
					.et_slide_in_menu_container .et_slide_menu_top .et-search-form input:-ms-input-placeholder { color: " + to + "; } \
					.et_header_style_fullscreen .et_slide_in_menu_container span.mobile_menu_bar.et_toggle_fullscreen_menu:before { color: " + to + "; } \
					.et_header_style_fullscreen .et_slide_menu_top .et-search-form { border-color: " + to + "; } \
				</style>",
				style_id = 'style#slide_nav_top_color';

			et_customizer_update_styles( style_id, $style_content );
		} );
	} );

	wp.customize( 'et_divi[slide_nav_search]', function( value ) {
		value.bind( function( to ) {
			var $style_content = "<style id='slide_nav_search'>.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form input,.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form button#searchsubmit_header:before { color: " + to + "; } \
				.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form input::-webkit-input-placeholder { color: " + to + "; } \
				.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form input::-moz-placeholder { color: " + to + "; } \
				.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form input:-ms-input-placeholder { color: " + to + "; } \
				</style>",
				style_id = 'style#slide_nav_search';

			if ( 'rgba(255,255,255,0.6)' === to ) {
				$style_content = '';
			}

			et_customizer_update_styles( style_id, $style_content );
		} );
	} );

	wp.customize( 'et_divi[slide_nav_search_bg]', function( value ) {
		value.bind( function( to ) {
			var $style_content = '<style id="slide_nav_search_bg">.et_header_style_slide .et_slide_in_menu_container .et_slide_menu_top .et-search-form { background: ' + to + ' !important; } </style>',
				style_id = 'style#slide_nav_search_bg';

			et_customizer_update_styles( style_id, $style_content );
		} );
	} );

	wp.customize( 'et_divi[slide_nav_width]', function( value ) {
		value.bind( function( to ) {
			var $style_content = '<style id="slide_nav_width">.et_header_style_slide .et_slide_in_menu_container { width: ' + to + 'px; } </style>',
				style_id = 'style#slide_nav_width',
				$slide_menu_container = $( '.et_slide_in_menu_container' ),
				$page_container = $( '#page-container, .et_fixed_nav #main-header' ),
				is_menu_opened = $( 'body' ).hasClass( 'et_pb_slide_menu_active' );

			et_customizer_update_styles( style_id, $style_content );

			if ( is_menu_opened ) {
				$page_container.css( { left: '-' + $slide_menu_container.innerWidth() + 'px' } );
			} else {
				$slide_menu_container.css( { right: '-' + $slide_menu_container.innerWidth() + 'px' } );
			}

		} );
	} );

	wp.customize( 'et_divi[slide_nav_font_style]', function( value ) {
		value.bind( function( to ) {
			var styles = et_set_font_styles( to, '' ),
				$style_content = '<style id="slide_nav_font_style"> .et_slide_in_menu_container, .et_slide_in_menu_container .et-search-field, .et_slide_in_menu_container a, .et_slide_in_menu_container #et-info span {' + styles + '}</style>',
				style_id = 'style#slide_nav_font_style';

			et_customizer_update_styles( style_id, $style_content );
		} );
	} );

	wp.customize( 'et_divi[slide_nav_font_size]', function( value ) {
		value.bind( function( to ) {
			var $style_content = "<style id='slide_nav_font_size'> .et_header_style_slide .et_slide_in_menu_container .et_mobile_menu li a { font-size:" + to + "px; }\
								</style>",
				style_id = 'style#slide_nav_font_size';

			et_customizer_update_styles( style_id, $style_content );

		} );
	} );

	wp.customize( 'et_divi[slide_nav_top_font_size]', function( value ) {
		value.bind( function( to ) {
			var $style_content = "<style id='slide_nav_top_font_size'> .et_header_style_slide .et_slide_in_menu_container,.et_header_style_slide .et_slide_in_menu_container input.et-search-field,.et_header_style_slide .et_slide_in_menu_container a,.et_header_style_slide .et_slide_in_menu_container #et-info span,.et_header_style_slide .et_slide_menu_top ul.et-social-icons a,.et_header_style_slide .et_slide_menu_top span { font-size: " + to + "px; }\
								.et_header_style_slide .et_slide_in_menu_container .et-search-field::-moz-placeholder { font-size: " + to + "px; }\
								.et_header_style_slide .et_slide_in_menu_container .et-search-field::-webkit-input-placeholder { font-size: " + to + "px; }\
								.et_header_style_slide .et_slide_in_menu_container .et-search-field:-ms-input-placeholder { font-size: " + to + "px; }\
								</style>",
				style_id = 'style#slide_nav_top_font_size';

			et_customizer_update_styles( style_id, $style_content );

		} );
	} );

	wp.customize( 'et_divi[fullscreen_nav_font_size]', function( value ) {
		value.bind( function( to ) {
			var $style_content = "<style id='fullscreen_nav_font_size'> .et_header_style_fullscreen .et_slide_in_menu_container .et_mobile_menu li a { font-size:" + to + "px; }\
								</style>",
				style_id = 'style#fullscreen_nav_font_size';

			et_customizer_update_styles( style_id, $style_content );

		} );
	} );

	wp.customize( 'et_divi[fullscreen_nav_top_font_size]', function( value ) {
		value.bind( function( to ) {
			var $style_content = "<style id='fullscreen_nav_top_font_size'> .et_header_style_fullscreen .et_slide_in_menu_container,.et_header_style_fullscreen .et_slide_in_menu_container input.et-search-field,.et_header_style_fullscreen .et_slide_in_menu_container a,.et_header_style_fullscreen .et_slide_in_menu_container #et-info span,.et_header_style_fullscreen .et_slide_menu_top ul.et-social-icons a,.et_header_style_fullscreen .et_slide_menu_top span { font-size: " + to + "px; }\
								.et_header_style_fullscreen .et_slide_in_menu_container .et-search-field::-moz-placeholder { font-size: " + to + "px; }\
								.et_header_style_fullscreen .et_slide_in_menu_container .et-search-field::-webkit-input-placeholder { font-size: " + to + "px; }\
								.et_header_style_fullscreen .et_slide_in_menu_container .et-search-field:-ms-input-placeholder { font-size: " + to + "px; }\
								</style>",
				style_id = 'style#fullscreen_nav_top_font_size';

			et_customizer_update_styles( style_id, $style_content );

		} );
	} );

	wp.customize( 'et_divi[slide_nav_font_spacing]', function( value ) {
		value.bind( function( to ) {
			var $style_content = "<style id='slide_nav_font_spacing'>\
									.et_slide_in_menu_container, .et_slide_in_menu_container .et-search-field { letter-spacing: " + to + "px; }\
									.et_slide_in_menu_container .et-search-field::-moz-placeholder { letter-spacing: " + to + "px; }\
									.et_slide_in_menu_container .et-search-field::-webkit-input-placeholder { letter-spacing: " + to + "px; }\
									.et_slide_in_menu_container .et-search-field:-ms-input-placeholder { letter-spacing: " + to + "px; }\
								</style>",
				style_id = 'style#slide_nav_font_spacing';

			et_customizer_update_styles( style_id, $style_content );
		} );
	} );

} )( jQuery );
