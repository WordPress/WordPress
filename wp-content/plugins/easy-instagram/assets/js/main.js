/*jshint -W061 */
videojs.options.flash.swf = Easy_Instagram_Settings.videojs_flash_swf_url;

jQuery( document ).ready( function() {
	jQuery( '.easy-instagram-container' ).each( function() {
		var f = jQuery( this ).children( 'form' ).first();
		var elem_id = jQuery( this ).attr( 'id' );

		jQuery.ajax({
			url: Easy_Instagram_Settings.ajaxurl,
			data: f.serialize(),
			success: function( data ) {
				var obj = jQuery.parseJSON( data );
				if( 'SUCCESS' == obj.status ) {
					jQuery( '#' + elem_id ).replaceWith( obj.output );
					console.log(Easy_Instagram_Settings.after_ajax_content_load);
					if('' !== Easy_Instagram_Settings.after_ajax_content_load) {
						eval(Easy_Instagram_Settings.after_ajax_content_load);
					}
					
					jQuery.event.trigger({
						type: 'afterEasyInstagramLoad',
					});
				}
				else {
					jQuery('#'+elem_id).html(jQuery('<span/>', {
						text: obj.error,
						class: 'easy-instagram-error'
					}));
				}
			}
		});
	});
});

jQuery(document).on( 'afterEasyInstagramLoad', function() {
	// Reload Masonry layout if present
	var layout = jQuery( '.masonry' );
	if ( 'undefined' != typeof layout ) {
		var elements = layout.find( '.easy-instagram-thumbnail-wrapper' );
		if ( elements.size() > 0 ) {
			layout.imagesLoaded( function() {
				layout.masonry();
			});
		}
	}
	
	jQuery('.colorbox').colorbox();
		jQuery("a.gallery").colorbox({rel:"group"})
	
	jQuery( '.thickbox.video' ).each(function() {
		var hid_video = jQuery(this).prev('.easy-instagram-hid-video-wrapper').first().find('video').first();
		
		if( 0 === hid_video.length ) return;
		
		var id = jQuery( hid_video ).attr( 'id' );
		var href = '#' + id;

		videojs(href, { controls: true }, function() {
			
		});
	});
	
	jQuery( '.colorbox-video' ).each(function() {
		var hid_video = jQuery(this).prev('.easy-instagram-hid-video-wrapper').first().find('video').first();
		
		if( 0 === hid_video.length ) return;
		
		var id = jQuery( hid_video ).attr( 'id' );
		var href = '#' + id;

		jQuery(this).colorbox({
			inline: true,
			href: href
		});
		
		videojs(href, { controls: true }, function() {
			
		});
	});
	
});
