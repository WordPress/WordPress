/* global wpseoMediaL10n */
/* global ajaxurl */
/* global wp */
/* jshint -W097 */
/* jshint -W003 */
/* jshint unused:false */
'use strict';
// Taken and adapted from http://www.webmaster-source.com/2013/02/06/using-the-wordpress-3-5-media-uploader-in-your-plugin-or-theme/
jQuery( document ).ready( function( $ ) {
		var wpseo_custom_uploader;
		$( '.wpseo_image_upload_button' ).click( function( e ) {
				var wpseo_target_id = $( this ).attr( 'id' ).replace( /_button$/, '' );
				e.preventDefault();
				if ( wpseo_custom_uploader ) {
					wpseo_custom_uploader.open();
					return;
				}
				wpseo_custom_uploader = wp.media.frames.file_frame = wp.media( {
						title: wpseoMediaL10n.choose_image,
						button: { text: wpseoMediaL10n.choose_image },
						multiple: false
					}
				);
				wpseo_custom_uploader.on( 'select', function() {
						var attachment = wpseo_custom_uploader.state().get( 'selection' ).first().toJSON();
						$( '#' + wpseo_target_id ).val( attachment.url );
					}
				);
				wpseo_custom_uploader.open();
			}
		);
	}
);
