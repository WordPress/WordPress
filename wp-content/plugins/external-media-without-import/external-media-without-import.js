jQuery(function ( $ ) {
	var isAdding = false;

	function clear() {
		$( '#emwi-url' ).val( '' );
		$( '#emwi-hidden' ).hide();
		$( '#emwi-error' ).text( '' );
		$( '#emwi-width' ).val( '' );
		$( '#emwi-height' ).val( '' );
		$( '#emwi-mime-type' ).val( '' );
	}

	$( 'body' ).on( 'click', '#emwi-clear', function ( e ) {
		clear();
	});

	$( 'body' ).on( 'click', '#emwi-show', function ( e ) {
		$( '#emwi-media-new-panel' ).show();
		e.preventDefault();
	});

	$( 'body' ).on( 'click', '#emwi-in-upload-ui #emwi-add', function ( e ) {
		if ( isAdding ) {
			return;
		}
		isAdding = true;

		$('#emwi-in-upload-ui #emwi-add').prop('disabled', true);

		var postData = {
			'url': $( '#emwi-url' ).val(),
			'width': $( '#emwi-width' ).val(),
			'height': $( '#emwi-height' ).val(),
			'mime-type': $( '#emwi-mime-type' ).val()
		};
		wp.media.post( 'add_external_media_without_import', postData )
			.done(function ( response ) {
				var attachment = wp.media.model.Attachment.create( response );
				attachment.fetch();

				// Update the attachment list in browser.
				var frame = wp.media.frame || wp.media.library;
				if ( frame ) {
					frame.content.mode( 'browse' );

					// The frame variable may be MediaFrame.Manage or MediaFrame.EditAttachments.
					// In the later case, library = frame.library.
					var library = frame.state().get( 'library' ) || frame.library;
					library.add( attachment ? [ attachment ] : [] );

					if ( wp.media.frame._state != 'library' ) {
						var selection = frame.state().get( 'selection' );
						if ( selection ) {
							selection.add( attachment );
						}
					}
				}

				// Reset the input.
				clear();
				$( '#emwi-hidden' ).hide();
				$( '#emwi-buttons-row .spinner' ).css( 'visibility', 'hidden' );
				$( '#emwi-in-upload-ui #emwi-add').prop('disabled', false);
				isAdding = false;
			}).fail(function (response ) {
				$( '#emwi-error' ).text( response['error'] );
				$( '#emwi-width' ).val( response['width'] );
				$( '#emwi-height' ).val( response['height'] );
				$( '#emwi-mime-type' ).val( response['mime-type'] );
				$( '#emwi-hidden' ).show();
				$( '#emwi-buttons-row .spinner' ).css( 'visibility', 'hidden' );
				$( '#emwi-in-upload-ui #emwi-add' ).prop('disabled', false);
				isAdding = false;
			});
		e.preventDefault();
		$( '#emwi-buttons-row .spinner' ).css( 'visibility', 'visible' );
	});

	$( 'body' ).on( 'click', '#emwi-in-upload-ui #emwi-cancel', function (e ) {
		clear();
		$( '#emwi-media-new-panel' ).hide();
		$( '#emwi-buttons-row .spinner' ).css( 'visibility', 'hidden' );
		$( '#emwi-in-upload-ui #emwi-add' ).prop('disabled', false);
		isAdding = false;
		e.preventDefault();
	});
});
