/* global ajaxurl */
(function($){

	$(document).ready( function() {
		var $colorpicker, $stylesheet;
	
		$('.color-palette').click( function() {
			$(this).siblings('input[name="admin_color"]').prop('checked', true);
		});
	
		$colorpicker = $( '#color-picker' );
		$stylesheet = $( '#colors-css' );
	
		$colorpicker.on( 'click.colorpicker', '.color-option', function() {
			var colors, css_url,
				$this = $(this);
	
			if ( $this.hasClass( 'selected' ) ) {
				return;
			}
	
			$this.siblings( '.selected' ).removeClass( 'selected' );
			$this.addClass( 'selected' ).find( 'input[type="radio"]' ).prop( 'checked', true );
	
			// Set color scheme
			// Load the colors stylesheet
			css_url = $this.children( '.css_url' ).val();
			$stylesheet.attr( 'href', css_url );

			// repaint icons
			if ( typeof wp !== 'undefined' && wp.svgPainter ) {
				try {
					colors = $.parseJSON( $this.children( '.icon_colors' ).val() );
				} catch ( error ) {}

				if ( colors ) {
					wp.svgPainter.setColors( colors );
					wp.svgPainter.paint();
				}
			}
			
			// update user option
			$.post( ajaxurl, {
				action:       'save-user-color-scheme',
				color_scheme: $this.children( 'input[name="admin_color"]' ).val(),
				nonce:        $('#_wpnonce').val()
			});
		});
	});

})(jQuery);
