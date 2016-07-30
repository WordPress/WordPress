(function($){
	window.et_pb_align_vertical_modal = function( $prompt_modal, prompt_buttons_class ) {
		var $window = $(window),
			prompt_buttons_class = typeof prompt_buttons_class !== 'undefined' ? prompt_buttons_class : '.et_pb_prompt_buttons',
			$prompt_buttons = $prompt_modal.find( prompt_buttons_class ),
			$wpadminbar = $('#wpadminbar'),
			window_height = $window.height(),
			prompt_modal_height = $prompt_modal.outerHeight(),
			prompt_buttons_height = $prompt_buttons.outerHeight();
			wpadminbar_height = $wpadminbar.outerHeight(),
			prompt_modal_adjustment = 0 - ( prompt_modal_height / 2 ) + ( wpadminbar_height / 2 );

		if ( prompt_modal_height > ( window_height - wpadminbar_height ) ) {
			$prompt_modal.css({
				top : ( wpadminbar_height + 15 ),
				bottom : 15,
				marginTop : 0,
				minHeight : 0
			});
		} else {
			$prompt_modal.css({
				top : '50%',
				marginTop : prompt_modal_adjustment
			});
		}

		$prompt_modal.addClass( 'et_pb_auto_centerize_modal' );
	}
})(jQuery)