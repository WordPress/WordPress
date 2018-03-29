(function($){
	$( document ).ready( function() {
		var ET_Select_Image = function(element, options){
			this.element = element;
			this.custom_select_link = null;
			this.custom_dropdown = null;
			this.frontend_customizer = $('body').hasClass( 'et_frontend_customizer' ) ? true : false;

			this.options = jQuery.extend({}, this.defaults, options);

			this.create_dropdown();
		},
		$iframe_preview = 'old' === et_divi_customizer_data.is_old_wp ? $( '#customize-preview' ) : $( '.wp-full-overlay' ),
		all_device_classes = 'old' === et_divi_customizer_data.is_old_wp ? 'et_divi_phone et_divi_tablet' : 'preview-tablet preview-mobile preview-desktop',
		tablet_class = 'old' === et_divi_customizer_data.is_old_wp ? 'et_divi_tablet' : 'preview-tablet';
		phone_class = 'old' === et_divi_customizer_data.is_old_wp ? 'et_divi_phone' : 'preview-mobile';
		desktop_class = 'old' === et_divi_customizer_data.is_old_wp ? '' : 'preview-desktop';

		if ( typeof window.location.search !== 'undefined' && window.location.search.search( 'et_customizer_option_set=module' ) !== -1 ) {
			$( 'body' ).addClass( 'et_modules_customizer_option_set' );
		} else {
			$( 'body' ).addClass( 'et_theme_customizer_option_set' );
		}

		ET_Select_Image.prototype = {
			defaults: {
				apply_value_to	: 'body'
			},

			create_dropdown: function(){
				var $et_select_image_main_select = this.element,
					et_filter_options_html = '',
					$selected_option,
					$dropdown_selected_option,
					self = this;

				if ( $et_select_image_main_select.length ) {
					$et_select_image_main_select.hide().addClass( 'et_select_image_main_select' );

					$et_select_image_main_select.change( $.proxy( self.change_option, self ) );

					$et_select_image_main_select.find( 'option' ).each( function() {
						var $this_option = $(this),
							selected = $(this).is( ':selected' ) ? ' class="et_select_image_active"' : '',
							option_class = 0 === $this_option.attr( 'value' ).indexOf( '_' ) ? $this_option.attr( 'value' ) : '_' + $this_option.attr( 'value' );

						et_filter_options_html += '<li class="et_si' + option_class + '_column" data-value="' + $this_option.attr( 'value' ) + '"' + selected +'>' + $this_option.text() + '</li>';
					} );

					$et_select_image_main_select.after( '<a href="#" class="et_select_image_custom_select">' + '<span class="et_filter_text"></span>' + '</a>' + '<ul class="et_select_image_options '+ self.esc_classname( $et_select_image_main_select.attr('data-customize-setting-link') ) +'">' + et_filter_options_html + '</ul>' );
				}

				this.custom_select_link = $et_select_image_main_select.next( '.et_select_image_custom_select' );

				this.custom_dropdown = this.custom_select_link.next('.et_select_image_options');

				$selected_option = $et_select_image_main_select.find( ':selected' );

				if ( $selected_option.length ) {
					var selected_option_class = 0 === $selected_option.attr( 'value' ).indexOf( '_' ) ? $selected_option.attr( 'value' ) : '_' + $selected_option.attr( 'value' );
					this.custom_select_link.find('.et_filter_text').text( $selected_option.text() ).addClass( 'et_si' + selected_option_class + '_column' );

					$dropdown_selected_option = ( $selected_option.val() == 'none' ) ? this.custom_dropdown.find('li').eq(0) : this.custom_dropdown.find('li[data-value="' + $selected_option.text() + '"]');

					this.custom_select_link.find('.et_filter_text').addClass( $dropdown_selected_option.attr('class') ).attr( 'data-si-class', $dropdown_selected_option.attr('class') );

					$dropdown_selected_option.addClass( 'et_select_image_active' );
				}

				this.custom_select_link.click( $.proxy( self.open_dropdown, self ) );

				this.custom_dropdown.find('li').click( $.proxy( self.select_option, self ) );
			},

			open_dropdown: function(event) {
				var self = this,
					$this_link = $(event.target);

				if ( self.custom_dropdown.hasClass( 'et_select_image_open' ) ) return false;

				self.custom_dropdown.show().addClass( 'et_select_image_open' );

				$this_link.hide();

				return false;
			},

			select_option: function(event) {
				var self = this,
					$this_option = $(event.target),
					this_option_value = $this_option.attr('data-value'),
					$main_text = self.custom_select_link.find( '.et_filter_text' ),
					$main_select_active_element = self.element.find( 'option[value="' + this_option_value + '"]' );

				if ( $this_option.hasClass( 'et_select_image_active' ) ) {
					self.close_dropdown();

					return false;
				}

				$this_option.siblings().removeClass( 'et_select_image_active' );

				$main_text.removeClass(function(index, css){
					return (css.match(/\bet_si_\S+/g) || []).join(' ');
				});

				$main_text.addClass( $this_option.attr( 'class' ) ).attr( 'data-si-class', $this_option.attr( 'class' ) );

				$this_option.addClass('et_select_image_active');

				self.close_dropdown();

				if ( ! $main_select_active_element.length )
					self.element.val( 'none' ).trigger( 'change' );
				else
					self.element.val( this_option_value ).trigger( 'change' );

				return false;
			},

			close_dropdown: function() {
				this.custom_select_link.find( '.et_filter_text' ).show();
				this.custom_dropdown.hide().removeClass( 'et_select_image_open' );
			},

			change_option: function() {
				var self = this,
					$active_option = self.element.find('option:selected'),
					active_option_value = $active_option.val(),
					$this_option = this.custom_dropdown.find('li[data-value="' + active_option_value + '"]'),
					$main_text = self.custom_select_link.find( '.et_filter_text' ),
					main_text_si_class = $main_text.attr( 'data-si-class' );

				// set correct custom dropdown values on first load
				if ( this.custom_dropdown.find('li.et_select_image_active').data( 'value' ) !== active_option_value ) {
					this.custom_dropdown.find('li').removeClass( 'et_select_image_active' );
					$main_text.removeClass( main_text_si_class ).addClass( $this_option.attr( 'class' ) ).attr( 'data-si-class', $this_option.attr( 'class' ) );

					$this_option.addClass('et_select_image_active');
				}
			},

			esc_classname: function( option_value ) {
				return 'et_si_' + option_value.replace(/[ +\/\[\]]/g,'_').toLowerCase();
			}
		};

		$.fn.et_select_image = function(options){
			new ET_Select_Image(this, options);
			return this;
		};

		$('select[data-customize-setting-link="et_divi[footer_columns]"]').et_select_image({ apply_value_to: 'body' });

		$( '.et_divi_reset_slider' ).click( function () {
			var $this_input = $( this ).closest( 'label' ).find( 'input' ),
				input_name = $this_input.data( 'customize-setting-link' ),
				input_default = $this_input.data( 'reset_value' );

			$this_input.val( input_default );
			$this_input.change();
		});

		$( '#accordion-section-et_divi_mobile_tablet h3, #accordion-panel-et_divi_mobile h3' ).click( function () {
			$iframe_preview.removeClass( all_device_classes ).addClass( tablet_class );

			if ( 'old' !== et_divi_customizer_data.is_old_wp ) {
				$( '#customize-footer-actions .devices' ).css( { display: 'none' } );
			}
		});

		$( '#accordion-section-et_divi_mobile_phone h3, #accordion-section-et_divi_mobile_menu h3' ).click( function () {
			$iframe_preview.removeClass( all_device_classes ).addClass( phone_class );

			if ( 'old' !== et_divi_customizer_data.is_old_wp ) {
				$( '#customize-footer-actions .devices' ).css( { display: 'none' } );
			}
		});

		$( '.control-panel-back, .customize-panel-back' ).click( function () {
			$iframe_preview.removeClass( all_device_classes ).addClass( desktop_class );

			if ( 'old' !== et_divi_customizer_data.is_old_wp ) {
				$( '#customize-footer-actions .devices' ).css( { display: 'block' } );
			}
		});

		$( 'input[type=range]' ).on( 'mousedown', function() {
			var $range = $(this),
				$range_input = $range.parent().children( '.et-pb-range-input' );

			value = $( this ).attr( 'value' );
			$range_input.val( value );

			$( this ).mousemove(function() {
				value = $( this ).attr( 'value' );
				$range_input.val( value );
			});
		});

		var et_range_input_number_timeout;

		function et_autocorrect_range_input_number( input_number, timeout ) {
			var $range_input = input_number,
				$range       = $range_input.parent().find('input[type="range"]'),
				value        = parseFloat( $range_input.val() ),
				reset        = parseFloat( $range.attr('data-reset_value') ),
				step         = parseFloat( $range_input.attr('step') ),
				min          = parseFloat( $range_input.attr('min') ),
				max          = parseFloat( $range_input.attr('max') );

			clearTimeout( et_range_input_number_timeout );

			et_range_input_number_timeout = setTimeout( function() {
				if ( isNaN( value ) ) {
					$range_input.val( reset );
					$range.val( reset ).trigger( 'change' );
					return;
				}

				if ( step >= 1 && value % 1 !== 0 ) {
					value = Math.round( value );
					$range_input.val( value );
					$range.val( value );
				}

				if ( value > max ) {
					$range_input.val( max );
					$range.val( max ).trigger( 'change' );
				}

				if ( value < min ) {
					$range_input.val( min );
					$range.val( min ).trigger( 'change' );
				}
			}, timeout );

			$range.val( value ).trigger( 'change' );
		}

		$('input.et-pb-range-input').on( 'change keyup', function() {
			et_autocorrect_range_input_number( $(this), 1000 );
		}).on( 'focusout', function() {
			et_autocorrect_range_input_number( $(this), 0 );
		});

		$('input.et_font_style_checkbox[type=checkbox]').on('change', function(){
			var $this_el      = $(this),
				$main_option  = $this_el.closest( 'span' ).siblings( 'input.et_font_styles' ),
				value         = $this_el.val(),
				current_value = $main_option.val(),
				values        = ( current_value != 'false' ) ? current_value.split( '|' ) : [],
				query         = $.inArray( value, values ),
				result        = '';

			if ( $this_el.prop('checked' ) === true ) {

				if ( current_value.length ) {

					if ( query < 0 ) {
						values.push( value );

						result = values.join( '|' );
					}
				} else {
					result = value;
				}
			} else {

				if ( current_value.length !== 0 ) {

					if ( query >= 0 ) {
						values.splice( query, 1 );

						result = values.join( '|' );
					} else {
						result = current_value;
					}
				}
			}

			$main_option.val( result ).trigger( 'change' );
		});

		$( 'span.et_font_style' ).click( function() {
			var style_checkbox = $( this ).find( 'input' );

			$( this ).toggleClass( 'et_font_style_checked' );

			if ( style_checkbox.is( ':checked' ) ) {
				style_checkbox.prop( 'checked', false );
			} else {
				style_checkbox.prop( 'checked', true );
			}

			style_checkbox.change();
		});

		var $vertical_nav_option                  = $( '#customize-control-et_divi-vertical_nav' ),
			$vertical_nav_input                   = $vertical_nav_option.find( 'input[type=checkbox]' ),
			$nav_fullwidth_control                = $( '#customize-control-et_divi-nav_fullwidth' ),
			$hide_navigation_until_scroll_control = $('#customize-control-et_divi-hide_nav'),
			$header_style_option                  = $( '#customize-control-et_divi-header_style select' ),
			$secondary_menu_options               = $( '#accordion-section-et_divi_header_secondary' ),
			$slide_header_section                 = $( '#accordion-section-et_divi_header_slide' ),
			$show_top_bar_input                   = $( '#customize-control-et_divi-slide_nav_show_top_bar input[type=checkbox]' ),
			$top_bar_related_options              = $( '#customize-control-et_divi-slide_nav_bg_top, #customize-control-et_divi-slide_nav_top_color, #customize-control-et_divi-slide_nav_search, #customize-control-et_divi-slide_nav_search_bg'),
			$primary_header_options               = $( '#customize-control-et_divi-primary_nav_font_size, #customize-control-et_divi-primary_nav_font_spacing, #customize-control-et_divi-primary_nav_font, #customize-control-et_divi-primary_nav_font_style, #customize-control-et_divi-menu_link_active, #customize-control-et_divi-primary_nav_dropdown_bg, #customize-control-et_divi-primary_nav_dropdown_line_color, #customize-control-et_divi-primary_nav_dropdown_link_color, #customize-control-et_divi-primary_nav_dropdown_animation, #customize-control-et_divi-fixed_primary_nav_font_size, #customize-control-et_divi-fixed_secondary_nav_bg, #customize-control-et_divi-fixed_menu_link, #customize-control-et_divi-fixed_secondary_menu_link, #customize-control-et_divi-fixed_menu_link_active' ),
			$slide_only_options                   = $( '#customize-control-et_divi-slide_nav_width, #customize-control-et_divi-slide_nav_search, #customize-control-et_divi-slide_nav_search_bg, #customize-control-et_divi-slide_nav_font_size, #customize-control-et_divi-slide_nav_top_font_size' ),
			$fullscreen_only_options              = $( '#customize-control-et_divi-fullscreen_nav_font_size, #customize-control-et_divi-fullscreen_nav_top_font_size' ),
			$vertical_orientation                 = $( '#customize-control-et_divi-vertical_nav_orientation' ),
			$menu_height                          = $( '#customize-control-et_divi-menu_height' ),
			$menu_margin_top                      = $( '#customize-control-et_divi-menu_margin_top' );

		if ( $vertical_nav_input.is( ':checked') ) {
			$nav_fullwidth_control.hide();
			$hide_navigation_until_scroll_control.hide();
			$vertical_orientation.show();
			$menu_height.hide();
			$menu_margin_top.show();
		} else {
			$nav_fullwidth_control.show();
			$hide_navigation_until_scroll_control.show();
			$vertical_orientation.hide();
			$menu_height.show();
			$menu_margin_top.hide();
		}

		if ( 'slide' === $header_style_option.val() || 'fullscreen' === $header_style_option.val() ) {
			$vertical_nav_option.hide();
			$vertical_nav_input.attr( 'checked', false );
			$vertical_nav_input.change();
			$secondary_menu_options.addClass( 'et_hidden_section' );
			$primary_header_options.hide();
			$slide_header_section.removeClass( 'et_hidden_section' );

			if ( 'slide' === $header_style_option.val() ) {
				$slide_only_options.removeClass( 'et_hidden_section' );
				$fullscreen_only_options.addClass( 'et_hidden_section' );
			} else {
				$slide_only_options.addClass( 'et_hidden_section' );
				$fullscreen_only_options.removeClass( 'et_hidden_section' );
			}
		} else {
			$vertical_nav_option.show();
			$secondary_menu_options.removeClass( 'et_hidden_section' );
			$primary_header_options.show();
			$slide_header_section.addClass( 'et_hidden_section' );
		}

		if ( $show_top_bar_input.is( ':checked' ) ) {
			$top_bar_related_options.show();
		} else {
			$top_bar_related_options.hide();
		}

		$('#customize-theme-controls').on( 'change', '#customize-control-et_divi-vertical_nav input[type=checkbox]', function(){
			var is_checked = $(this).is(':checked');

			if ( is_checked ) {
				$menu_height.hide();
				$menu_margin_top.show();
			} else {
				$menu_height.show();
				$menu_margin_top.hide();
			}
		});

		$('#customize-theme-controls').on( 'change', '#customize-control-et_divi-vertical_nav input[type=checkbox]', function(){
			$input = $(this);

			if ( $input.is(':checked') ) {
				$nav_fullwidth_control.hide();
				$hide_navigation_until_scroll_control.hide();
				$vertical_orientation.show();
			} else {
				$nav_fullwidth_control.show();
				$hide_navigation_until_scroll_control.show();
				$vertical_orientation.hide();
			}
		});

		$( '#customize-theme-controls' ).on( 'change', '#customize-control-et_divi-header_style select', function(){
			$input = $(this);

			if ( 'slide' === $input.val() || 'fullscreen' === $input.val() ) {
				$vertical_nav_option.hide();
				$vertical_nav_input.attr( 'checked', false );
				$vertical_nav_input.change();
				$secondary_menu_options.addClass( 'et_hidden_section' );
				$primary_header_options.hide();
				$slide_header_section.removeClass( 'et_hidden_section' );

				if ( 'slide' === $header_style_option.val() ) {
					$slide_only_options.removeClass( 'et_hidden_section' );
					$fullscreen_only_options.addClass( 'et_hidden_section' );
				} else {
					$slide_only_options.addClass( 'et_hidden_section' );
					$fullscreen_only_options.removeClass( 'et_hidden_section' );
				}
			} else {
				$vertical_nav_option.show();
				$secondary_menu_options.removeClass( 'et_hidden_section' );
				$primary_header_options.show();
				$slide_header_section.addClass( 'et_hidden_section' );
			}
		});

		$( '#customize-theme-controls' ).on( 'change', '#customize-control-et_divi-slide_nav_show_top_bar input[type=checkbox]', function(){
			$input = $(this);

			if ( $input.is( ':checked' ) ) {
				$top_bar_related_options.show();
			} else {
				$top_bar_related_options.hide();
			}
		});

		function toggle_sidebar_width_control() {
			var $checkbox          = $('#customize-control-et_divi-use_sidebar_width input[type="checkbox"]'),
			$sidebar_width_control = $( '#customize-control-et_divi-sidebar_width' );

			if ( $checkbox.is( ':checked' ) ) {
				$sidebar_width_control.fadeIn();
			} else {
				$sidebar_width_control.fadeOut();
			}
		}

		toggle_sidebar_width_control();

		$('#customize-theme-controls').on( 'change', '#customize-control-et_divi-use_sidebar_width input[type=checkbox]', function(){
			toggle_sidebar_width_control();
		});

	});

	var api = wp.customize;

	api.ET_ColorAlphaControl = api.Control.extend({
		ready: function() {
			var control = this,
				picker = control.container.find('.color-picker-hex');

			picker.val( control.setting() ).wpColorPicker({
				change: function() {
					var et_color_picker_value = picker.wpColorPicker('color');

					if ( '' === et_color_picker_value || 'string' !== typeof et_color_picker_value ) {
						return;
					}

					try {
						control.setting.set( et_color_picker_value.toLowerCase() );
					} catch( err ) {
						// Value is not a properly formatted color, let's see if we can fix it.

						if ( /^[\da-z]{3}([\da-z]{3})?$/i.test(et_color_picker_value) ) {
							// Value looks like a hex color but is missing hash character.
							et_color_picker_value = '#' + et_color_picker_value.toLowerCase();

							control.setting.set( et_color_picker_value );
						}
					}
				},
				clear: function() {
					control.setting.set( false );
				}
			});

			control.setting.bind( function( value ) {
				picker.val( value );
				picker.wpColorPicker( 'color', value );
			});

			/**
			* Adding following event whenever footer_menu_text_color is changed, due to its relationship with footer_menu_active_link_color.
			*/
			if ( 'et_divi[footer_menu_text_color]' === this.id ) {

				// Whenever user change footer_menu_text_color, do the following
				this.setting.bind( 'change', function( value ){

					// Set footer_menu_active_link_color equal to the newly changed footer_menu_text_color
					api( 'et_divi[footer_menu_active_link_color]' ).set( value );

					// Update default color of footer_menu_active_link_color equal to the newly changed footer_menu_text_color.
					// If afterward user change the color and not happy with it, they can click reset and back to footer_menu_text_color color
					api.control( 'et_divi[footer_menu_active_link_color]' ).container.find( '.color-picker-hex' )
						.data( 'data-default-color', value )
						.wpColorPicker({ 'defaultColor' : value, 'color' : value });
				});
			}
		}
	});

	$( 'body' ).on( 'click', '.et_font_icon li', function() {
		var $this_el = $( this ),
			$this_input;

		if ( ! $this_el.hasClass( 'active' ) ) {
			$( '.et_font_icon li' ).removeClass( 'et_active' );
			$this_el.addClass( 'et_active' );

			$this_input = $this_el.closest( 'label' ).find( '.et_selected_icon' );
			$this_input.val( $this_el.data( 'icon' ) );
			$this_input.change();
		}
	});

	api.controlConstructor.et_coloralpha = api.ET_ColorAlphaControl;

	wp.customize.bind('ready', function() {
		// Unbind built-in & sanitized control and replaced it with straightforward control
		// to ensure compatibility with Divi option's data type
		var normalizedBackgroundImageOptions = ['background_repeat', 'background_attachment'];

		_.each(normalizedBackgroundImageOptions, function(option) {
			// Unbind WordPress' built-in option js control
			var defaultControl = api.control(option);

			if ( _.isUndefined( defaultControl ) ) {
				return;
			}

			defaultControl.container.find('input').unbind();

			// Re-bind background_repeat option which is compatible with Divi's option
			var	defaultControlNewInputs = new api.Element( defaultControl.container.find('input') );

			defaultControlNewInputs.bind(function( to ) {
				defaultControl.setting.set( to );
			} );

			defaultControl.setting.bind( function( to ) {
				defaultControlNewInputs.set( to );
			} );
		});

		// Toggle customizer control visibility based on other control's value
		function et_toggle_control_visibility( controls, visibility ) {
			_.each( controls, function( controlId ) {
				api.control( controlId, function( controlChild ) {
					// Use inline CSS + !important styling combo to overwrite WordPress'
					// built-in customizer appearance dependency
					if ( visibility ) {
						$( controlChild.container ).show().removeClass('et_hidden_section');
					} else {
						$( controlChild.container ).hide().addClass('et_hidden_section');
					}
				});
			});
		}

		// Stretch Background Image
		api.control( 'et_divi[cover_background]', function( control ) {
			var coverBackgroundValue = control.setting.get(),
				backgroundImageValue = api.control( 'background_image' ).setting.get() !== '',
				affectedControls     = ['background_repeat', 'background_position_x' ],
				affectedControlsVisibility = ! coverBackgroundValue && backgroundImageValue;

			// Toggle visibility on page load
			et_toggle_control_visibility( affectedControls, affectedControlsVisibility );

			// Toggle visibility on checkbox change
			control.setting.bind( 'change', function( coverBackgroundValueChanged ) {
				var backgroundImageValueChanged = api.control( 'background_image' ).setting.get() !== '',
					affectedControlsVisibilityChanged = ! coverBackgroundValueChanged && backgroundImageValueChanged;

				et_toggle_control_visibility( affectedControls, affectedControlsVisibilityChanged );
			});
		});

		// Get background image's dynamic affected controls
		function et_background_image_affected_controls() {
			var coverBackgroundValue = api.control( 'et_divi[cover_background]' ).setting.get(),
				affectedControls = [ 'et_divi[cover_background]' ];

			if ( ! coverBackgroundValue ) {
				affectedControls = $.merge( affectedControls, [ 'background_repeat', 'background_position_x' ] );
			}

			return affectedControls;
		}

		// Background Image
		api.control('background_image', function( control ) {
			var backgroundImageValue = control.setting.get(),
				hasBackgroundImage = backgroundImageValue !== '';

			// Toggle visibility on page load
			et_toggle_control_visibility( et_background_image_affected_controls(), hasBackgroundImage );

			// Toggle visibility on background image change
			control.setting.bind( 'change', function( changedBackgroundImageValue ) {
				var hasChangedBackgroundImage = changedBackgroundImageValue !== '';

				et_toggle_control_visibility( et_background_image_affected_controls(), hasChangedBackgroundImage );
			});
		});
	});

	$( window ).load( function() {
		var $et_custom_footer_credits_disable_control = $('#customize-control-et_divi-disable_custom_footer_credits input'),
			$et_custom_footer_credits_control         = $('#customize-control-et_divi-custom_footer_credits');

		if ( $et_custom_footer_credits_disable_control.is(':checked') ) {
			$et_custom_footer_credits_control.hide();
		}

		$et_custom_footer_credits_disable_control.change( function() {
			if ( $(this).is(':checked') ) {
				$et_custom_footer_credits_control.hide();
			} else {
				$et_custom_footer_credits_control.show();
			}
		} );

		if ( $( '#accordion-section-et_divi_buttons' ).length ) {
			var $icon_options_trigger = $( '#customize-control-et_divi-all_buttons_icon select' ),
				icon_options_trigger_val = $icon_options_trigger.val();

			trigger_button_options( icon_options_trigger_val );

			$icon_options_trigger.change( function() {
				icon_options_trigger_val = $( this ).val();
				trigger_button_options( icon_options_trigger_val );
			});
		}

		function trigger_button_options( trigger_val ) {
			var icon_options_set = [ 'all_buttons_icon_color', 'all_buttons_icon_placement', 'all_buttons_icon_hover', 'all_buttons_selected_icon' ];

			$.each( icon_options_set, function( i, option_name ) {
				if ( 'yes' === trigger_val ) {
					$( '#customize-control-et_divi-' + option_name ).show();
				} else {
					$( '#customize-control-et_divi-' + option_name ).hide();
				}
			} );
		}

		if ( $( '.et_font_icon' ).length ) {
			$( '.et_font_icon' ).each( function(){
				var $this_el = $( this ),
					this_input_val = $this_el.closest( 'label' ).find( '.et_selected_icon' ).val();
				$this_el.find( 'li[data-icon="' + this_input_val + '"]').addClass( 'et_active' );
			});
		}

	} );

})(jQuery);
