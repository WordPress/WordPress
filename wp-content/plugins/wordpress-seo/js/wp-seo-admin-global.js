/* global ajaxurl */
/* jshint -W097 */
/* jshint unused:false */
'use strict';
/**
 * Used to dismiss the after-update admin notice for a specific user until the next update.
 *
 * @param {string} nonce
 */
function wpseoDismissAbout( nonce ) {
	jQuery.post( ajaxurl, {
			action: 'wpseo_dismiss_about',
			_wpnonce: nonce
		}
	);
}

/**
 * Used to dismiss the tagline notice for a specific user.
 *
 * @param {string} nonce
 */
function wpseoDismissTaglineNotice( nonce ) {
	jQuery.post( ajaxurl, {
			action: 'wpseo_dismiss_tagline_notice',
			_wpnonce: nonce
		}
	)
}

/**
 * Used to remove the admin notices for several purposes, dies on exit.
 *
 * @param {string} option
 * @param {string} hide
 * @param {string} nonce
 */
function wpseoSetIgnore( option, hide, nonce ) {
	jQuery.post( ajaxurl, {
			action: 'wpseo_set_ignore',
			option: option,
			_wpnonce: nonce
		}, function( data ) {
			if ( data ) {
				jQuery( '#' + hide ).hide();
				jQuery( '#hidden_ignore_' + option ).val( 'ignore' );
			}
		}
	);
}

/**
 * Make the notices dismissible (again)
 */
function wpseoMakeDismissible() {
	jQuery( '.notice.is-dismissible' ).each( function() {
		var $notice = jQuery( this );
		if ( $notice.find( '.notice-dismiss').empty() ) {
			var	$button = jQuery( '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>' );

			$notice.append( $button );

			$button.on( 'click.wp-dismiss-notice', function( event ) {
				event.preventDefault();
				$notice.fadeTo( 100 , 0, function() {
					jQuery(this).slideUp( 100, function() {
						jQuery(this).remove();
					});
				});
			});
		}
	});
}

jQuery( document ).ready( function() {
	jQuery( '#wpseo-dismiss-about > .notice-dismiss' ).click( function() {
		wpseoDismissAbout( jQuery( '#wpseo-dismiss-about' ).data( 'nonce' ) );
	});

	jQuery( '#wpseo-dismiss-tagline-notice > .notice-dismiss').click( function() {
		wpseoDismissTaglineNotice( jQuery( '#wpseo-dismiss-tagline-notice').data( 'nonce' ) );
	})
});
