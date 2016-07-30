(function($){
	$( document ).ready( function() {
		var $container             = $( '.et_pb_roles_options_container' ),
			$yes_no_button_wrapper = $container.find( '.et_pb_yes_no_button_wrapper' ),
			$yes_no_button         = $container.find( '.et_pb_yes_no_button' ),
			$yes_no_select         = $container.find( 'select' ),
			$body                  = $( 'body' );

		$yes_no_button_wrapper.each( function() {
			var $this_el = $( this ),
				$this_switcher = $this_el.find( '.et_pb_yes_no_button' ),
				selected_value = $this_el.find( 'select' ).val();

			if ( 'on' === selected_value ) {
				$this_switcher.removeClass( 'et_pb_off_state' );
				$this_switcher.addClass( 'et_pb_on_state' );
			} else {
				$this_switcher.removeClass( 'et_pb_on_state' );
				$this_switcher.addClass( 'et_pb_off_state' );
			}
		});

		$yes_no_button.click( function() {
			var $this_el = $( this ),
				$this_select = $this_el.closest( '.et_pb_yes_no_button_wrapper' ).find( 'select' );

			if ( $this_el.hasClass( 'et_pb_off_state') ) {
				$this_el.removeClass( 'et_pb_off_state' );
				$this_el.addClass( 'et_pb_on_state' );
				$this_select.val( 'on' );
			} else {
				$this_el.removeClass( 'et_pb_on_state' );
				$this_el.addClass( 'et_pb_off_state' );
				$this_select.val( 'off' );
			}

			$this_select.trigger( 'change' );
		});

		$yes_no_select.change( function() {
			var $this_el = $( this ),
				$this_switcher = $this_el.closest( '.et_pb_yes_no_button_wrapper' ).find( '.et_pb_yes_no_button' ),
				new_value = $this_el.val();

			if ( 'on' === new_value ) {
				$this_switcher.removeClass( 'et_pb_off_state' );
				$this_switcher.addClass( 'et_pb_on_state' );
			} else {
				$this_switcher.removeClass( 'et_pb_on_state' );
				$this_switcher.addClass( 'et_pb_off_state' );
			}

		});

		$( '.et-pb-layout-buttons:not(.et-pb-layout-buttons-reset):not(.et-pb-portability-button)' ).click( function() {
			var $clicked_tab = $( this ),
				open_tab = $clicked_tab.data( 'open_tab' );

			$( '.et_pb_roles_options_container.active-container' ).css( { 'display' : 'block', 'opacity' : 1 } ).stop( true, true ).animate( { opacity : 0 }, 300, function() {
				var $this_container = $( this );
				$this_container.css( 'display', 'none' );
				$this_container.removeClass( 'active-container' );
				$( '.' + open_tab ).addClass( 'active-container' ).css( { 'display' : 'block', 'opacity' : 0 } ).stop( true, true ).animate( { opacity : 1 }, 300 );
			});

			$( '.et-pb-layout-buttons' ).removeClass( 'et_pb_roles_active_menu' );

			$clicked_tab.addClass( 'et_pb_roles_active_menu' );
		});

		$( '#et_pb_save_roles' ).click( function() {
			et_pb_save_roles( false, true );
			return false;
		} );

		function et_pb_save_roles( callback, message ) {
			var $all_options = $( '.et_pb_roles_container_all' ).find( 'form' ),
				all_options_array = {},
				options_combined = '';

			$all_options.each( function() {
				var this_form = $( this ),
					form_id = this_form.data( 'role_id' ),
					form_settings = this_form.serialize();

				all_options_array[form_id] = form_settings;
			});

			options_combined = JSON.stringify( all_options_array );

			$.ajax({
				type: 'POST',
				url: et_pb_roles_options.ajaxurl,
				dataType: 'json',
				data: {
					action : 'et_pb_save_role_settings',
					et_pb_options_all : options_combined,
					et_pb_save_roles_nonce : et_pb_roles_options.et_roles_nonce
				},
				beforeSend: function ( xhr ){
					if ( message ) {
						$( '#et_pb_loading_animation' ).removeClass( 'et_pb_hide_loading' );
						$( '#et_pb_success_animation' ).removeClass( 'et_pb_active_success' );
						$( '#et_pb_loading_animation' ).show();
					}
				},
				success: function( data ){
					if ( message ) {
						$( '#et_pb_loading_animation' ).addClass( 'et_pb_hide_loading' );
						$( '#et_pb_success_animation' ).addClass( 'et_pb_active_success' ).show();

						setTimeout( function(){
							$( '#et_pb_success_animation' ).fadeToggle();
							$( '#et_pb_loading_animation' ).fadeToggle();
						}, 1000 );
					}

					if ( $.isFunction( callback ) ) {
						callback();
					}
				}
			});
		}


		$( '.et_pb_toggle_all' ).click( function() {
			var $options_section = $( this ).closest( '.et_pb_roles_section_container' ),
				$toggles = $options_section.find( '.et-pb-main-setting' ),
				on_buttons_count = 0,
				off_buttons_count = 0;

			$toggles.each( function() {
				if ( 'on' === $( this ).val() ) {
					on_buttons_count++;
				} else {
					off_buttons_count++;
				}
			});

			if ( on_buttons_count >= off_buttons_count ) {
				$toggles.val( 'off' );
			} else {
				$toggles.val( 'on' );
			}

			$toggles.change();
		});

		$( '.et-pb-layout-buttons-reset' ).click( function() {
			var $confirm_modal =
				"<div class='et_pb_modal_overlay' data-action='reset_roles'>\
					<div class='et_pb_prompt_modal'>\
					<h3>" + et_pb_roles_options.modal_title + "</h3>\
					<p>" + et_pb_roles_options.modal_message + "</p>\
						<a href='#' class='et_pb_prompt_dont_proceed et-pb-modal-close'>\
							<span>" + et_pb_roles_options.modal_no + "<span>\
						</span></span></a>\
						<div class='et_pb_prompt_buttons'>\
							<a href='#' class='et_pb_prompt_proceed'>" + et_pb_roles_options.modal_yes + "</a>\
						</div>\
					</div>\
				</div>";

			$( 'body' ).append( $confirm_modal );
			window.et_pb_align_vertical_modal( $( '.et_pb_prompt_modal' ) );

			return false;
		});

		$( 'body' ).on( 'click', '.et-pb-modal-close', function() {
			et_pb_close_modal( $( this ) );
		});

		$( 'body' ).on( 'click', '.et_pb_prompt_proceed', function() {
			var $all_toggles = $( '.et-pb-main-setting' );

			$all_toggles.val( 'on' );
			$all_toggles.change();

			et_pb_close_modal( $( this ) );
		});

		$body.append( '<div id="et_pb_loading_animation"></div>' );
		$body.append( '<div id="et_pb_success_animation"></div>' );

		$( '#et_pb_loading_animation' ).hide();
		$( '#et_pb_success_animation' ).hide();

		function et_pb_close_modal( $button ) {
			var $modal_overlay = $button.closest( '.et_pb_modal_overlay' );

			// add class to apply the closing animation to modal
			$modal_overlay.addClass( 'et_pb_modal_closing' );

			//remove the modal with overlay when animation complete
			setTimeout( function() {
				$modal_overlay.remove();
			}, 600 );
		}

		// Portability integration.
		etCore.portability.save = function( callback ) {
			et_pb_save_roles( callback, false );
		}
	});
})(jQuery)