(function($){
	$( document ).ready( function() {
		var url = window.location.href,
			tab_link = url.split( 'edit.php' )[1];

		if ( typeof tab_link !== 'undefined' ) {
			var $menu_items = $( '#toplevel_page_et_divi_library' ).find( '.wp-submenu li' );
			$menu_items.removeClass( 'current' );
			$menu_items.find( 'a' ).each( function() {
				var $this_el = $( this ),
					this_href = $this_el.attr( 'href' ),
					full_tab_link = 'edit.php' + tab_link;
				if ( -1 !== full_tab_link.indexOf( this_href ) ) {
					$this_el.closest( 'li' ).addClass( 'current' );
				}
			});
			$( '#toplevel_page_et_divi_library' ).removeClass( 'wp-not-current-submenu' ).addClass( 'wp-has-current-submenu' );
			$( 'a.toplevel_page_et_divi_library' ).removeClass( 'wp-not-current-submenu' ).addClass( 'wp-has-current-submenu wp-menu-open' );
		}

		$( 'body' ).on( 'click', '.add-new-h2, a.page-title-action', function() {
			$( 'body' ).addClass( 'et-core-nbfc' ).append( et_pb_new_template_options.modal_output );

			return false;
		} );

		$( 'body' ).on( 'click', '.et_pb_prompt_dont_proceed', function() {
			var $modal_overlay = $( this ).closest( '.et_pb_modal_overlay' );

			// add class to apply the closing animation to modal
			$modal_overlay.addClass( 'et_pb_modal_closing' );

			//remove the modal with overlay when animation complete
			setTimeout( function() {
				$( 'body' ).removeClass( 'et-core-nbfc' );
				$modal_overlay.remove();
			}, 600 );
		} );

		$( 'body' ).on( 'change', '#new_template_type', function() {
			var selected_type = $( this ).val();
			var $module_tabs_option = $( '.et_module_tabs_options' );
			var $global_option = $( '#et_pb_template_global' ).closest( 'label' );

			if ( 'module' === selected_type || 'fullwidth_module' === selected_type ) {
				$module_tabs_option.css( 'display', 'block' );
			} else {
				$module_tabs_option.css( 'display', 'none' );
			}

			if ( 'layout' === selected_type ) {
				$global_option.css( 'display', 'none' );
			} else {
				$global_option.css( 'display', 'block' );
			}
		} );

		$( 'body' ).on( 'click', '.et_pb_create_template:not(.clicked_button)', function() {
			var $this_button = $( this ),
				$this_form = $this_button.closest( '.et_pb_prompt_modal' ),
				template_name = $this_form.find( '#et_pb_new_template_name' ).val();

			if ( '' === template_name ) {
				$this_form.find( '#et_pb_new_template_name' ).focus();
			} else {
				var	template_shortcode = '',
					layout_type = $this_form.find( '#new_template_type' ).val(),
					selected_cats = '',
					fields_data = [];

				// push all the data from inputs into array
				$this_form.find('input, select').each( function() {
					var $this_input = $( this );

					if ( typeof $this_input.attr('id') !== 'undefined' && '' !== $this_input.val()) {
						// add only values from checked checkboxes
						if ( 'checkbox' === $this_input.attr('type') && !$this_input.is( ':checked' ) ) {
							return;
						}
						fields_data.push({
							'field_id': $this_input.attr('id'),
							'field_val': $this_input.val()
						});
					}
				});

				if ( $( '.layout_cats_container input' ).is( ':checked' ) ) {

					$( '.layout_cats_container input' ).each( function() {
						var this_input = $( this );

						if ( this_input.is( ':checked' ) ) {
							selected_cats += '' !== selected_cats ? ',' + this_input.val() : this_input.val();
						}
					});

				}

				// add processed data into array of values
				fields_data.push(
					{
						'field_id': 'selected_cats',
						'field_val': selected_cats
					}
				);

				$this_button.addClass( 'clicked_button' );
				$this_button.closest( '.et_pb_prompt_buttons' ).find( '.spinner' ).addClass( 'et_pb_visible_spinner' );

				$.ajax( {
					type: "POST",
					url: et_pb_new_template_options.ajaxurl,
					dataType: 'json',
					data:
					{
						action : 'et_pb_add_new_layout',
						et_admin_load_nonce : et_pb_new_template_options.et_admin_load_nonce,
						et_layout_options : JSON.stringify(fields_data),
					},
					success: function( data ) {
						if ( typeof data !== 'undefined' && '' !== data ) {
							window.location.href = decodeURIComponent( unescape( data.edit_link ) );
						}
					}
				} );
			}
		} );
	});
})(jQuery)