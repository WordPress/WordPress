(function($){

	$(document).ready( function(){
		var $save_message = $("#epanel-ajax-saving"),
			$save_message_spinner = $save_message.children("img"),
			$save_message_description = $save_message.children("span");

		$(".et_disable_memory_limit_increase").on( "click", function( event ) {
			event.preventDefault();

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: {
					action : "et_reset_memory_limit_increase",
					et_builder_reset_memory_limit_nonce : et_reset_memory_limit_increase.et_builder_reset_memory_limit_nonce
				},
				beforeSend: function ( xhr ){
					$save_message.addClass( 'et_loading' ).removeClass( 'success-animation' );
					$save_message.fadeIn('fast');
				},
				success: function( response ){
					$save_message.removeClass( 'et_loading' ).removeClass( 'success-animation' );

					setTimeout( function() {
						$save_message.fadeOut( "slow" );
					}, 500 );

					if ( response === 'success' ) {
						$( '.et_disable_memory_limit_increase' ).closest( '.epanel-box' ).hide();

						$save_message.addClass( 'success-animation' );
					}
				}
			});
		});

	});

})(jQuery)