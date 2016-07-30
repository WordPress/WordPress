/* <![CDATA[ */
	var clearpath = ePanelSettings.clearpath;

	jQuery(document).ready(function($){
		var $palette_inputs = $( '.et_color_palette_main_input' );

		$('#epanel-content,#epanel-content > div').tabs({
			fx: {
				opacity: 'toggle',
				duration:'fast'
			},
			selected: 0,
			activate: function( event, ui ) {
				$epanel = $('#epanel');

				if ( $epanel.hasClass('onload') ) {
					$epanel.removeClass('onload');
				}
			}
		});

		$(".box-description").click(function(){
			var descheading = $(this).parent('.epanel-box').find(".box-title h3").html();
			var desctext = $(this).parent('.epanel-box').find(".box-title .box-descr").html();

			$('body').append("<div id='custom-lbox'><div class='box-desc'><div class='box-desc-top'>"+ ePanelSettings.help_label +"</div><div class='box-desc-content'><h3>"+descheading+"</h3>"+desctext+"<div class='lightboxclose'></div> </div> <div class='box-desc-bottom'></div>	</div></div>");

			et_pb_center_modal( $( '.box-desc' ) );

			$( '.lightboxclose' ).click( function() {
				et_pb_close_modal( $( '#custom-lbox' ) );
			});
		});

		$(".defaults-button.epanel-reset").click(function(e) {
			e.preventDefault();
			$(".reset-popup-overlay, .defaults-hover").addClass('active');

			et_pb_center_modal( $( '.defaults-hover' ) );
		});

		$( '.no' ).click( function() {
			et_pb_close_modal( $( '.reset-popup-overlay' ), 'no_remove' );

			//clean the modal classes when animation complete
			setTimeout( function() {
				$( '.reset-popup-overlay, .defaults-hover' ).removeClass( 'active et_pb_modal_closing' );
			}, 600 );
		});

		// ":not([safari])" is desirable but not necessary selector
		// ":not([safari])" is desirable but not necessary selector
		$('#epanel input:checkbox:not([safari]):not(.yes_no_button)').checkbox();
		$('#epanel input[safari]:checkbox:not(.yes_no_button)').checkbox({cls:'jquery-safari-checkbox'});
		$('#epanel input:radio:not(.yes_no_button)').checkbox();

		// Yes - No button UI
		$('.yes_no_button').each(function() {
			$checkbox = $(this),
			value     = $checkbox.is(':checked'),
			state     = value ? 'et_pb_on_state' : 'et_pb_off_state',
			$template = $($('#epanel-yes-no-button-template').html()).find('.et_pb_yes_no_button').addClass(state);

			$checkbox.hide().after($template);
		});

		$('.box-content').on( 'click', '.et_pb_yes_no_button', function(e){
			e.preventDefault();

			var $click_area = $(this),
				$box_content = $click_area.parents('.box-content'),
				$checkbox    = $box_content.find('input[type="checkbox"]'),
				$state       = $box_content.find('.et_pb_yes_no_button');

			$state.toggleClass('et_pb_on_state et_pb_off_state');

			if ( $checkbox.is(':checked' ) ) {
				$checkbox.prop('checked', false);
			} else {
				$checkbox.prop('checked', true);
			}

		});

		var $save_message = $("#epanel-ajax-saving");

		$('#epanel-save-top').click(function(e){
			e.preventDefault();

			$('#epanel-save').trigger('click');
		})

		$('#epanel-save').click(function(){
			epanel_save( false, true );
			return false;
		});

		function epanel_save( callback, message ) {
			var options_fromform = $('#main_options_form').formSerialize(),
				add_nonce = '&_ajax_nonce='+ePanelSettings.epanel_nonce;

			options_fromform += add_nonce;

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: options_fromform,
				beforeSend: function ( xhr ){
					if ( message ) {
						$save_message.removeAttr('class').fadeIn('fast');
					}
				},
				success: function(response){
					if ( message ) {
						$save_message.addClass('success-animation');

						setTimeout(function(){
							$save_message.fadeOut();
						},500);
					}

					if ( $.isFunction( callback ) ) {
						callback();
					}
				}
			});
		}

		function et_pb_close_modal( $overlay, no_overlay_remove ) {
			var $modal_container = $overlay;

			// add class to apply the closing animation to modal
			$modal_container.addClass( 'et_pb_modal_closing' );

			//remove the modal with overlay when animation complete
			setTimeout( function() {
				if ( 'no_remove' !== no_overlay_remove ) {
					$modal_container.remove();
				}
			}, 600 );
		}

		if ( $palette_inputs.length ) {
			$palette_inputs.each( function() {
				var	$this_input                    = $( this ),
					$palette_wrapper               = $this_input.closest( '.box-content' ),
					$colorpalette_colorpickers     = $palette_wrapper.find( '.input-colorpalette-colorpicker' ),
					colorpalette_colorpicker_index = 0,
					saved_palette                  = $this_input.val().split('|');

				$colorpalette_colorpickers.each( function(){
					var $colorpalette_colorpicker      = $(this),
						colorpalette_colorpicker_color = saved_palette[ colorpalette_colorpicker_index ];

					$colorpalette_colorpicker.val( colorpalette_colorpicker_color ).wpColorPicker({
						hide : false,
						default : $(this).data( 'default-color' ),
						width: 313,
						palettes : false,
						change : function( event, ui ) {
							var $input     = $(this),
								data_index = $input.attr( 'data-index'),
								$preview   = $palette_wrapper.find( '.colorpalette-item-' + data_index ),
								color      = ui.color.toString();

							$input.val( color );
							$preview.css({ 'backgroundColor' : color });
							saved_palette[ data_index - 1 ] = color;
							$this_input.val( saved_palette.join( '|' ) );
						}
					});

					$colorpalette_colorpicker.trigger( 'change' );

					colorpalette_colorpicker_index++;
				} );

				$palette_wrapper.on( 'click', '.colorpalette-item', function(e){
					e.preventDefault();

					var $colorpalette_item = $(this),
						data_index         = $colorpalette_item.attr('data-index');

					// Hide other colorpalette colorpicker
					$palette_wrapper.find( '.colorpalette-colorpicker' ).removeClass( 'active' );

					// Display selected colorpalette colorpicker
					$palette_wrapper.find( '.colorpalette-colorpicker[data-index="' + data_index + '"]' ).addClass( 'active' );
				});
			});
		}

		if ( typeof etCore !== 'undefined' ) {
			// Portability integration.
			etCore.portability.save = function( callback ) {
				epanel_save( callback, false );
			}
		}

		function et_pb_center_modal( $modal ) {
			var modal_height = $modal.outerHeight(),
				modal_height_adjustment = 0 - ( modal_height / 2 );

			$modal.css({
				top : '50%',
				bottom : 'auto',
				marginTop : modal_height_adjustment
			});
		}
	});
/* ]]> */