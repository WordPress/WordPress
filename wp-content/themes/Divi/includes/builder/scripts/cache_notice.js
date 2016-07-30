(function($){
	window.et_error_modal_shown = false;
	window.et_builder_version = window.et_builder_version || '';

	var $et_cache_notice_template = $( '#et-builder-cache-notice-template' );

	if ( et_pb_notice_options.product_version !== window.et_builder_version ) {
		$( 'body' ).addClass( 'et_pb_stop_scroll' ).append( $et_cache_notice_template.html() );

		window.et_pb_align_vertical_modal( $( '.et_pb_prompt_modal' ) );

		window.et_error_modal_shown = true;
	}
})(jQuery)