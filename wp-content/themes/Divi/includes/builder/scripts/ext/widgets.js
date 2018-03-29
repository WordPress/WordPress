( function($) {

	$( document ).ready( function() {
		$( '.widget-liquid-right' ).append( et_pb_options.widget_info );

		var $create_box = $( '#et_pb_widget_area_create' ),
			$widget_name_input = $create_box.find( '#et_pb_new_widget_area_name' ),
			$et_pb_sidebars = $( 'div[id^=et_pb_widget_area_]' );

		$create_box.find( '.et_pb_create_widget_area' ).click( function( event ) {
			var $this_el = $(this);

			event.preventDefault();

			if ( $widget_name_input.val() === '' ) return;

			$.ajax( {
				type: "POST",
				url: et_pb_options.ajaxurl,
				data:
				{
					action : 'et_pb_add_widget_area',
					et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
					et_widget_area_name : $widget_name_input.val()
				},
				success: function( data ){
					$this_el.closest( '#et_pb_widget_area_create' ).find( '.et_pb_widget_area_result' ).hide().html( data ).slideToggle();
				}
			} );
		} );

		$et_pb_sidebars.each( function() {
			if ( $(this).is( '#et_pb_widget_area_create' ) || $(this).closest( '.inactive-sidebar' ).length ) return true;

			$(this).closest('.widgets-holder-wrap').find('.sidebar-name h2, .sidebar-name h3').before( '<a href="#" class="et_pb_widget_area_remove">' + et_pb_options.delete_string + '</a>' );

			$( '.et_pb_widget_area_remove' ).click( function( event ) {
				var $this_el = $(this);

				event.preventDefault();

				$.ajax( {
					type: "POST",
					url: et_pb_options.ajaxurl,
					data:
					{
						action : 'et_pb_remove_widget_area',
						et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
						et_widget_area_name : $this_el.closest( '.widgets-holder-wrap' ).find( 'div[id^=et_pb_widget_area_]' ).attr( 'id' )
					},
					success: function( data ){
						$( '#' + data ).closest( '.widgets-holder-wrap' ).remove();
					}
				} );

				return false;
			} );
		} );
	} );

} )(jQuery);