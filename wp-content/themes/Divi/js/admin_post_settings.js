(function($){
	$(document).ready(function() {
		var $post_format          = $('input[name="post_format"]'),
			$settings             = $('.et_divi_format_setting'),
			$use_bg_color_setting = $('#et_post_use_bg_color');

		$('.color-picker-hex').wpColorPicker();

		$post_format.change( function() {
			var $this = $(this);

			$settings.hide();

			$( '.et_divi_format_setting' + '.et_divi_' + $this.val() + '_settings' ).show();

			$use_bg_color_setting.trigger( 'change' );
		} );

		$use_bg_color_setting.change( function() {
			var $this = $(this);

			$( '.et_post_bg_color_setting' ).toggle( $this.is(':checked') );
		} );

		$post_format.filter(':checked').trigger( 'change' );
	});
})(jQuery);
