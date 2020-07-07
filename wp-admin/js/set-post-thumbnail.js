/**
 * @output wp-admin/js/set-post-thumbnail.js
 */

/* global ajaxurl, post_id, alert */
/* exported WPSetAsThumbnail */

window.WPSetAsThumbnail = function( id, nonce ) {
	var $link = jQuery('a#wp-post-thumbnail-' + id);

	$link.text( wp.i18n.__( 'Saving…' ) );
	jQuery.post(ajaxurl, {
		action: 'set-post-thumbnail', post_id: post_id, thumbnail_id: id, _ajax_nonce: nonce, cookie: encodeURIComponent( document.cookie )
	}, function(str){
		var win = window.dialogArguments || opener || parent || top;
		$link.text( wp.i18n.__( 'Use as featured image' ) );
		if ( str == '0' ) {
			alert( wp.i18n.__( 'Could not set that as the thumbnail image. Try a different attachment.' ) );
		} else {
			jQuery('a.wp-post-thumbnail').show();
			$link.text( wp.i18n.__( 'Done' ) );
			$link.fadeOut( 2000 );
			win.WPSetThumbnailID(id);
			win.WPSetThumbnailHTML(str);
		}
	}
	);
};
