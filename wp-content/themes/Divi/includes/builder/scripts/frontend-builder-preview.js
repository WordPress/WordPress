(function($){
	// Turn of all hrefs which point to another page
	$('body').on( 'click', 'a', function( event ){
		var href  = $(this).attr( 'href'),
			start = href.substr( 0, 1 );

		// Stop the link if it points to another URL
		if ( start !== '#' && start !== '' ) {
			event.preventDefault();

			// Display notification
			$('.link-disabled').addClass('active');
		}
	});

	// Prompt closing mechanism
	$('body').on( 'click', '.et_pb_prompt_proceed', function() {
		$('.link-disabled').removeClass('active');
	});

	// Build preview screen
	ET_PageBuilder_Preview = function( e ) {
		// Create form on the fly
		var $form = $('<form id="preview-data-submission" method="POST" style="display: none;"></form>'),
			value,
			data = e.data,
			msie = document.documentMode;

		// Origins should be matched
		if ( e.origin !== et_preview_params.preview_origin ) {
			$('.et-pb-preview-loading').replaceWith( $('<h4 />', { 'style' : 'text-align: center;' } ).html( et_preview_params.alert_origin_not_matched ) );
			return;
		}

		// IE9 below fix. They have postMessage, but it has to be in string
		if ( typeof msie !== 'undefined' && msie < 10 ) {
			data = JSON.parse( data );
		}

		// Loop postMessage data and append it to $form
		for ( name in data ) {
			$textarea = $('<textarea />', { name : name, style : "display: none; " }).val( data[name] );
			$textarea.appendTo( $form );
		}

		$form.append( '<input type="submit" value="submit" style="display: none;" />' );

		$form.appendTo( '.container' );

		// Submit the form
		$('#preview-data-submission').submit();
	}

	// listen to data passed from builder
	window.addEventListener( 'message', ET_PageBuilder_Preview, false );

	/**
	 * Notify parent of parsed shortcode
	 */
	var previewLoaded = $('#content > .content .et-pb-preview-loading').length !== 1;

	if (previewLoaded && typeof parent !== 'undefined') {
		/**
		 * There's no way to determine when the shortcode's JS has done parsing, so
		 * assume a safe fixed timeout before contacting parent's window
		 */
		setTimeout(function(){
			var output = {
				html: $('#content > .content').html(),
				stylesheets: [],
			};

			jQuery.each(window.location.search.substr(1).split('&'), function(index, param) {
				var paramArray = param.split('=');

				if (typeof paramArray[0] !== 'undefined' && typeof paramArray[1] !== 'undefined' && paramArray[0] === 'iframe_id') {
					output.iframe_id = paramArray[1];
				}
			});

			/**
			 * Pass parsed shortcode's style hrefs to parent
			 */
			$('link[rel="stylesheet"]').each(function(index, style){
				output.stylesheets.push($(style).attr('href'));
			})

			parent.postMessage(output, window.location.origin);
		}, 2000);
	}
})(jQuery)
