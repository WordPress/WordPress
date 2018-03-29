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
		$form.find('input:text, input[type="email"], input[type="url"], textarea').each(function(index,domEle){
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
				$(this).parents( '.et_mobile_menu' ).siblings( '.mobile_menu_bar' ).trigger( 'click' );
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

	window.et_bar_counters_init = function( $bar_item ) {
		if ( ! $bar_item.length ) {
			return;
		}

		var $bar_container      = $bar_item.closest( '.et_pb_counter_container' ),
			bar_item_width      = $bar_item.attr( 'data-width' ),
			bar_item_padding    = Math.ceil( parseFloat( $bar_item.css('paddingLeft') ) ) + Math.ceil( parseFloat( $bar_item.css('paddingRight') ) ),
			$bar_item_text      = $bar_item.children( '.et_pb_counter_amount_number' ),
			calculated_width    = ( $bar_container.width() - $bar_item_text.innerWidth() ) / 100 * parseFloat( bar_item_width ),
			bar_item_text_width = calculated_width + $bar_item_text.innerWidth();

		$bar_item.css({
			'width' : bar_item_text_width
		});
	}

	window.et_fix_pricing_currency_position = function( $pricing_table ) {
		var $all_pricing_tables = typeof $pricing_table !== 'undefined' ? $pricing_table : $( '.et_pb_pricing_table' );

		if ( ! $all_pricing_tables.length ) {
			return;
		}

		$all_pricing_tables.each( function() {
			var $this_table = $( this ),
				$price_container = $this_table.find( '.et_pb_et_price' ),
				$currency = $price_container.length ? $price_container.find( '.et_pb_dollar_sign' ) : false,
				$price = $price_container.length ? $price_container.find( '.et_pb_sum' ) : false;

			if ( ! $currency || ! $price ) {
				return;
			}

			// adjust the margin of currency sign to make sure it doesn't overflow the price
			$currency.css( { 'marginLeft' : - $currency.width() + 'px' } );
		});
	}

	window.et_pb_set_responsive_grid = function( $grid_items_container, single_item_selector ) {
		setTimeout( function() {
			var container_width = $grid_items_container.innerWidth(),
				$grid_items = $grid_items_container.find( single_item_selector ),
				item_width = $grid_items.outerWidth( true ),
				last_item_margin = item_width - $grid_items.outerWidth(),
				columns_count = Math.round( ( container_width + last_item_margin ) / item_width ),
				counter = 1,
				first_in_row = 1;

			$grid_items.removeClass( 'last_in_row first_in_row' );
			$grid_items.filter(':visible').each( function() {
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
		}, 1 ); // need this timeout to make sure all the css applied before calculating sizes
	};

	window.et_pb_set_tabs_height = function( $tabs_module ) {
		if ( typeof $tabs_module === 'undefined' ) {
			$tabs_module = $( '.et_pb_tabs' );
		}

		if ( ! $tabs_module.length ) {
			return;
		}

		$tabs_module.each( function() {
			var $tab_controls = $( this ).find( '.et_pb_tabs_controls' );
			var $all_tabs = $tab_controls.find( 'li' );
			var max_height = 0;
			var small_columns      = '.et_pb_column_1_3, .et_pb_column_1_4, .et_pb_column_3_8';
			var in_small_column    = $( this ).parents( small_columns ).length > 0;
			var on_small_screen    = parseFloat( $( window ).width() ) < 768;
			var vertically_stacked = in_small_column || on_small_screen;

			if ( vertically_stacked ) {
				$( this ).addClass( 'et_pb_tabs_vertically_stacked' );
			}

			// determine the height of the tallest tab
			if ( $all_tabs.length ) {
				// remove the height attribute if it was added to calculate the height correctly
				$tab_controls.removeAttr( 'style' );

				$all_tabs.each( function() {
					var tab_height = $( this ).outerHeight();

					if ( vertically_stacked ) {
						return;
					}

					if ( tab_height > max_height ) {
						max_height = tab_height;
					}
				});
			}

			if ( 0 !== max_height ) {
				// set the height of tabs container based on the height of the tallest tab
				$tab_controls.css( 'min-height', max_height );
			}
		});
	}
})(jQuery);
