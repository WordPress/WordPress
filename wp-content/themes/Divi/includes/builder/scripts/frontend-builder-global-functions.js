(function($){
	window.et_pb_smooth_scroll = function( $target, $top_section, speed, easing ) {
		var $window_width = $( window ).width();

		if ( $( 'body' ).hasClass( 'et_fixed_nav' ) && $window_width > 980 ) {
			$menu_offset = $( '#top-header' ).outerHeight() + $( '#main-header' ).outerHeight() - 1;
		} else {
			$menu_offset = -1;
		}

		if ( $ ('#wpadminbar').length && $window_width > 600 ) {
			$menu_offset += $( '#wpadminbar' ).outerHeight();
		}

		//fix sidenav scroll to top
		if ( $top_section ) {
			$scroll_position = 0;
		} else {
			$scroll_position = $target.offset().top - $menu_offset;
		}

		// set swing (animate's scrollTop default) as default value
		if( typeof easing === 'undefined' ){
			easing = 'swing';
		}

		$( 'html, body' ).animate( { scrollTop :  $scroll_position }, speed, easing );
	}

	window.et_fix_video_wmode = function( video_wrapper ) {
		$( video_wrapper ).each( function() {
			if ( $(this).find( 'iframe' ).length ) {
				var $this_el = $(this).find( 'iframe' ),
					src_attr = $this_el.attr('src'),
					wmode_character = src_attr.indexOf( '?' ) == -1 ? '?' : '&amp;',
					this_src = src_attr + wmode_character + 'wmode=opaque';

				$this_el.attr('src', this_src);
			}
		} );
	}

	window.et_pb_form_placeholders_init = function( $form ) {
		$form.find('input:text, textarea').each(function(index,domEle){
			var $et_current_input = jQuery(domEle),
				$et_comment_label = $et_current_input.siblings('label'),
				et_comment_label_value = $et_current_input.siblings('label').text();
			if ( $et_comment_label.length ) {
				$et_comment_label.hide();
				if ( $et_current_input.siblings('span.required') ) {
					et_comment_label_value += $et_current_input.siblings('span.required').text();
					$et_current_input.siblings('span.required').hide();
				}
				$et_current_input.val(et_comment_label_value);
			}
		}).bind('focus',function(){
			var et_label_text = jQuery(this).siblings('label').text();
			if ( jQuery(this).siblings('span.required').length ) et_label_text += jQuery(this).siblings('span.required').text();
			if (jQuery(this).val() === et_label_text) jQuery(this).val("");
		}).bind('blur',function(){
			var et_label_text = jQuery(this).siblings('label').text();
			if ( jQuery(this).siblings('span.required').length ) et_label_text += jQuery(this).siblings('span.required').text();
			if (jQuery(this).val() === "") jQuery(this).val( et_label_text );
		});
	}

	window.et_duplicate_menu = function( menu, append_to, menu_id, menu_class, menu_click_event ){
		append_to.each( function() {
			var $this_menu = $(this),
				$cloned_nav;

			// make this function work with existing menus, without cloning
			if ( '' !== menu ) {
				menu.clone().attr('id',menu_id).removeClass().attr('class',menu_class).appendTo( $this_menu );
			}

			$cloned_nav = $this_menu.find('> ul');
			$cloned_nav.find('.menu_slide').remove();
			$cloned_nav.find('li:first').addClass('et_first_mobile_item');

			$cloned_nav.find( 'a' ).on( 'click', function(){
				$( '#et_mobile_nav_menu .mobile_menu_bar' ).trigger( 'click' );
			} );

			if ( 'no_click_event' !== menu_click_event ) {
				$this_menu.on( 'click', '.mobile_menu_bar', function(){
					if ( $this_menu.hasClass('closed') ){
						$this_menu.removeClass( 'closed' ).addClass( 'opened' );
						$cloned_nav.stop().slideDown( 500 );
					} else {
						$this_menu.removeClass( 'opened' ).addClass( 'closed' );
						$cloned_nav.stop().slideUp( 500 );
					}
					return false;
				} );
			}
		} );

		$('#mobile_menu .centered-inline-logo-wrap').remove();
	}

	// remove placeholder text before form submission
	window.et_pb_remove_placeholder_text = function( $form ) {
		$form.find('input:text, textarea').each(function(index,domEle){
			var $et_current_input = jQuery(domEle),
				$et_label = $et_current_input.siblings('label'),
				et_label_value = $et_current_input.siblings('label').text();

			if ( $et_label.length && $et_label.is(':hidden') ) {
				if ( $et_label.text() == $et_current_input.val() )
					$et_current_input.val( '' );
			}
		});
	}

	window.et_fix_fullscreen_section = function() {
		var $et_window = $(window);

		$( 'section.et_pb_fullscreen' ).each( function(){
			var $this_section = $( this );

			$.proxy( et_calc_fullscreen_section, $this_section )();

			$et_window.on( 'resize', $.proxy( et_calc_fullscreen_section, $this_section ) );
		});
	}
})(jQuery)
