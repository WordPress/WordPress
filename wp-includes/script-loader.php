<?php
/**
 * WordPress scripts and styles default loader.
 *
 * Most of the functionality that existed here was moved to
 * {@link http://backpress.automattic.com/ BackPress}. WordPress themes and
 * plugins will only be concerned about the filters and actions set in this
 * file.
 *
 * Several constants are used to manage the loading, concatenating and compression of scripts and CSS:
 * define('SCRIPT_DEBUG', true); loads the development (non-minified) versions of all scripts and CSS, and disables compression and concatenation,
 * define('CONCATENATE_SCRIPTS', false); disables compression and concatenation of scripts and CSS,
 * define('COMPRESS_SCRIPTS', false); disables compression of scripts,
 * define('COMPRESS_CSS', false); disables compression of CSS,
 * define('ENFORCE_GZIP', true); forces gzip for compression (default is deflate).
 *
 * The globals $concatenate_scripts, $compress_scripts and $compress_css can be set by plugins
 * to temporarily override the above settings. Also a compression test is run once and the result is saved
 * as option 'can_compress_scripts' (0/1). The test will run again if that option is deleted.
 *
 * @package WordPress
 */

/** BackPress: WordPress Dependencies Class */
require( ABSPATH . WPINC . '/class.wp-dependencies.php' );

/** BackPress: WordPress Scripts Class */
require( ABSPATH . WPINC . '/class.wp-scripts.php' );

/** BackPress: WordPress Scripts Functions */
require( ABSPATH . WPINC . '/functions.wp-scripts.php' );

/** BackPress: WordPress Styles Class */
require( ABSPATH . WPINC . '/class.wp-styles.php' );

/** BackPress: WordPress Styles Functions */
require( ABSPATH . WPINC . '/functions.wp-styles.php' );

/**
 * Set up WordPress scripts to load by default for Administration Screen.
 *
 * Localizes a few of the scripts.
 * $scripts->add_data( 'script-handle', 'group', 1 ); queues the script for the footer
 *
 * @since 2.6.0
 *
 * @param object $scripts WP_Scripts object.
 */
function wp_default_scripts( &$scripts ) {

	if ( !$guessurl = site_url() )
		$guessurl = wp_guess_url();

	$scripts->base_url = $guessurl;
	$scripts->content_url = defined('WP_CONTENT_URL')? WP_CONTENT_URL : '';
	$scripts->default_version = get_bloginfo( 'version' );
	$scripts->default_dirs = array('/wp-admin/js/', '/wp-includes/js/');

	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';

	// Always ensure that we have the convertEntities function
	$scripts->add( 'l10n', "/wp-includes/js/l10n$suffix.js", false, '20101110' );
	$scripts->enqueue( 'l10n' );

	$scripts->add( 'utils', "/wp-admin/js/utils$suffix.js", false, '20101110' );

	$scripts->add( 'common', "/wp-admin/js/common$suffix.js", array('jquery', 'hoverIntent', 'utils'), '20110505' );
	$scripts->add_data( 'common', 'group', 1 );
	$scripts->localize( 'common', 'commonL10n', array(
		'warnDelete' => __("You are about to permanently delete the selected items.\n  'Cancel' to stop, 'OK' to delete."),
		'l10n_print_after' => 'try{convertEntities(commonL10n);}catch(e){};'
	) );

	$scripts->add( 'sack', "/wp-includes/js/tw-sack$suffix.js", false, '1.6.1' );
	$scripts->add_data( 'sack', 'group', 1 );

	$scripts->add( 'quicktags', "/wp-includes/js/quicktags$suffix.js", false, '20090307' );
	$scripts->add_data( 'quicktags', 'group', 1 );
	$scripts->localize( 'quicktags', 'quicktagsL10n', array(
		'quickLinks' => __('(Quick Links)'),
		'wordLookup' => __('Enter a word to look up:'),
		'dictionaryLookup' => esc_attr(__('Dictionary lookup')),
		'lookup' => esc_attr(__('lookup')),
		'closeAllOpenTags' => esc_attr(__('Close all open tags')),
		'closeTags' => esc_attr(__('close tags')),
		'enterURL' => __('Enter the URL'),
		'enterImageURL' => __('Enter the URL of the image'),
		'enterImageDescription' => __('Enter a description of the image'),
		'fullscreen' => __('fullscreen'),
		'toggleFullscreen' => esc_attr( __('Toggle fullscreen mode') ),
		'l10n_print_after' => 'try{convertEntities(quicktagsL10n);}catch(e){};'
	) );

	$scripts->add( 'colorpicker', "/wp-includes/js/colorpicker$suffix.js", array('prototype'), '3517m' );

	$scripts->add( 'editor', "/wp-admin/js/editor$suffix.js", array('utils','jquery'), '20110411' );
	$scripts->add_data( 'editor', 'group', 1 );

	$scripts->add( 'wp-fullscreen', "/wp-admin/js/wp-fullscreen$suffix.js", array('jquery'), '20110501' );
	$scripts->add_data( 'wp-fullscreen', 'group', 1 );

	$scripts->add( 'prototype', '/wp-includes/js/prototype.js', false, '1.6.1');

	$scripts->add( 'wp-ajax-response', "/wp-includes/js/wp-ajax-response$suffix.js", array('jquery'), '20091119' );
	$scripts->add_data( 'wp-ajax-response', 'group', 1 );
	$scripts->localize( 'wp-ajax-response', 'wpAjax', array(
		'noPerm' => __('You do not have permission to do that.'),
		'broken' => __('An unidentified error has occurred.'),
		'l10n_print_after' => 'try{convertEntities(wpAjax);}catch(e){};'
	) );

	$scripts->add( 'autosave', "/wp-includes/js/autosave$suffix.js", array('schedule', 'wp-ajax-response'), '20110425' );
	$scripts->add_data( 'autosave', 'group', 1 );

	$scripts->add( 'wp-lists', "/wp-includes/js/wp-lists$suffix.js", array('wp-ajax-response'), '20110504' );
	$scripts->add_data( 'wp-lists', 'group', 1 );

	$scripts->add( 'scriptaculous-root', '/wp-includes/js/scriptaculous/wp-scriptaculous.js', array('prototype'), '1.8.3');
	$scripts->add( 'scriptaculous-builder', '/wp-includes/js/scriptaculous/builder.js', array('scriptaculous-root'), '1.8.3');
	$scripts->add( 'scriptaculous-dragdrop', '/wp-includes/js/scriptaculous/dragdrop.js', array('scriptaculous-builder', 'scriptaculous-effects'), '1.8.3');
	$scripts->add( 'scriptaculous-effects', '/wp-includes/js/scriptaculous/effects.js', array('scriptaculous-root'), '1.8.3');
	$scripts->add( 'scriptaculous-slider', '/wp-includes/js/scriptaculous/slider.js', array('scriptaculous-effects'), '1.8.3');
	$scripts->add( 'scriptaculous-sound', '/wp-includes/js/scriptaculous/sound.js', array( 'scriptaculous-root' ), '1.8.3' );
	$scripts->add( 'scriptaculous-controls', '/wp-includes/js/scriptaculous/controls.js', array('scriptaculous-root'), '1.8.3');
	$scripts->add( 'scriptaculous', '', array('scriptaculous-dragdrop', 'scriptaculous-slider', 'scriptaculous-controls'), '1.8.3');

	// not used in core, replaced by Jcrop.js
	$scripts->add( 'cropper', '/wp-includes/js/crop/cropper.js', array('scriptaculous-dragdrop'), '20070118');

	$scripts->add( 'jquery', '/wp-includes/js/jquery/jquery.js', false, '1.5.2');

	$scripts->add( 'jquery-ui-core', '/wp-includes/js/jquery/ui.core.js', array('jquery'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-core', 'group', 1 );

	$scripts->add( 'jquery-ui-position', '/wp-includes/js/jquery/ui.position.js', array('jquery-ui-core'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-position', 'group', 1 );

	$scripts->add( 'jquery-ui-widget', '/wp-includes/js/jquery/ui.widget.js', array('jquery-ui-core'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-widget', 'group', 1 );

	$scripts->add( 'jquery-ui-mouse', '/wp-includes/js/jquery/ui.mouse.js', array('jquery-ui-widget'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-mouse', 'group', 1 );

	$scripts->add( 'jquery-ui-button', '/wp-includes/js/jquery/ui.button.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-button', 'group', 1 );

	$scripts->add( 'jquery-ui-tabs', '/wp-includes/js/jquery/ui.tabs.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-tabs', 'group', 1 );

	$scripts->add( 'jquery-ui-sortable', '/wp-includes/js/jquery/ui.sortable.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-sortable', 'group', 1 );

	$scripts->add( 'jquery-ui-draggable', '/wp-includes/js/jquery/ui.draggable.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-draggable', 'group', 1 );

	$scripts->add( 'jquery-ui-droppable', '/wp-includes/js/jquery/ui.droppable.js', array('jquery-ui-core', 'jquery-ui-mouse', 'jquery-ui-draggable'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-droppable', 'group', 1 );

	$scripts->add( 'jquery-ui-selectable', '/wp-includes/js/jquery/ui.selectable.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-selectable', 'group', 1 );

	$scripts->add( 'jquery-ui-resizable', '/wp-includes/js/jquery/ui.resizable.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-resizable', 'group', 1 );

	$scripts->add( 'jquery-ui-dialog', '/wp-includes/js/jquery/ui.dialog.js', array('jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position'), '1.8.9' );
	$scripts->add_data( 'jquery-ui-dialog', 'group', 1 );

	// deprecated, not used in core, most functionality is included in jQuery 1.3
	$scripts->add( 'jquery-form', "/wp-includes/js/jquery/jquery.form$suffix.js", array('jquery'), '2.02m');
	$scripts->add_data( 'jquery-form', 'group', 1 );

	$scripts->add( 'jquery-color', "/wp-includes/js/jquery/jquery.color$suffix.js", array('jquery'), '2.0-4561m');
	$scripts->add_data( 'jquery-color', 'group', 1 );

	$scripts->add( 'suggest', "/wp-includes/js/jquery/suggest$suffix.js", array('jquery'), '1.1-20110113');
	$scripts->add_data( 'suggest', 'group', 1 );

	$scripts->add( 'schedule', '/wp-includes/js/jquery/jquery.schedule.js', array('jquery'), '20m');
	$scripts->add_data( 'schedule', 'group', 1 );

	$scripts->add( 'jquery-query', "/wp-includes/js/jquery/jquery.query.js", array('jquery'), '2.1.7' );
	$scripts->add_data( 'jquery-query', 'group', 1 );

	$scripts->add( 'jquery-serialize-object', "/wp-includes/js/jquery/jquery.serialize-object.js", array('jquery'), '0.2' );
	$scripts->add_data( 'jquery-serialize-object', 'group', 1 );

	$scripts->add( 'jquery-hotkeys', "/wp-includes/js/jquery/jquery.hotkeys$suffix.js", array('jquery'), '0.0.2m' );
	$scripts->add_data( 'jquery-hotkeys', 'group', 1 );

	$scripts->add( 'jquery-table-hotkeys', "/wp-includes/js/jquery/jquery.table-hotkeys$suffix.js", array('jquery', 'jquery-hotkeys'), '20090102' );
	$scripts->add_data( 'jquery-table-hotkeys', 'group', 1 );

	$scripts->add( 'thickbox', "/wp-includes/js/thickbox/thickbox.js", array('jquery'), '3.1-20110405');
	$scripts->add_data( 'thickbox', 'group', 1 );
	$scripts->localize( 'thickbox', 'thickboxL10n', array(
			'next' => __('Next &gt;'),
			'prev' => __('&lt; Prev'),
			'image' => __('Image'),
			'of' => __('of'),
			'close' => __('Close'),
			'noiframes' => __('This feature requires inline frames. You have iframes disabled or your browser does not support them.'),
			'loadingAnimation' => includes_url('js/thickbox/loadingAnimation.gif'),
			'closeImage' => includes_url('js/thickbox/tb-close.png'),
			'l10n_print_after' => 'try{convertEntities(thickboxL10n);}catch(e){};'
	) );


	$scripts->add( 'jcrop', "/wp-includes/js/jcrop/jquery.Jcrop$suffix.js", array('jquery'), '0.9.8-20110113');

	$scripts->add( 'swfobject', "/wp-includes/js/swfobject.js", false, '2.2');

	$scripts->add( 'swfupload', '/wp-includes/js/swfupload/swfupload.js', false, '2201-20110113');
	$scripts->add( 'swfupload-swfobject', '/wp-includes/js/swfupload/plugins/swfupload.swfobject.js', array('swfupload', 'swfobject'), '2201');
	$scripts->add( 'swfupload-queue', '/wp-includes/js/swfupload/plugins/swfupload.queue.js', array('swfupload'), '2201');
	$scripts->add( 'swfupload-speed', '/wp-includes/js/swfupload/plugins/swfupload.speed.js', array('swfupload'), '2201');

	if ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) {
		// queue all SWFUpload scripts that are used by default
		$scripts->add( 'swfupload-all', false, array('swfupload', 'swfupload-swfobject', 'swfupload-queue'), '2201');
	} else {
		$scripts->add( 'swfupload-all', '/wp-includes/js/swfupload/swfupload-all.js', array(), '2201');
	}

	$scripts->add( 'swfupload-handlers', "/wp-includes/js/swfupload/handlers$suffix.js", array('swfupload-all', 'jquery'), '2201-20100523');
	$max_upload_size = ( (int) ( $max_up = @ini_get('upload_max_filesize') ) < (int) ( $max_post = @ini_get('post_max_size') ) ) ? $max_up : $max_post;
	if ( empty($max_upload_size) )
		$max_upload_size = __('not configured');
	// these error messages came from the sample swfupload js, they might need changing.
	$scripts->localize( 'swfupload-handlers', 'swfuploadL10n', array(
			'queue_limit_exceeded' => __('You have attempted to queue too many files.'),
			'file_exceeds_size_limit' => __('This file exceeds the maximum upload size for this site.'),
			'zero_byte_file' => __('This file is empty. Please try another.'),
			'invalid_filetype' => __('This file type is not allowed. Please try another.'),
			'default_error' => __('An error occurred in the upload. Please try again later.'),
			'missing_upload_url' => __('There was a configuration error. Please contact the server administrator.'),
			'upload_limit_exceeded' => __('You may only upload 1 file.'),
			'http_error' => __('HTTP error.'),
			'upload_failed' => __('Upload failed.'),
			'io_error' => __('IO error.'),
			'security_error' => __('Security error.'),
			'file_cancelled' => __('File canceled.'),
			'upload_stopped' => __('Upload stopped.'),
			'dismiss' => __('Dismiss'),
			'crunching' => __('Crunching&hellip;'),
			'deleted' => __('moved to the trash.'),
			'error_uploading' => __('&#8220;%s&#8221; has failed to upload due to an error'),
			'l10n_print_after' => 'try{convertEntities(swfuploadL10n);}catch(e){};',
	) );

	$scripts->add( 'comment-reply', "/wp-includes/js/comment-reply$suffix.js", false, '20090102');

	$scripts->add( 'json2', "/wp-includes/js/json2$suffix.js", false, '2011-02-23');

	$scripts->add( 'imgareaselect', "/wp-includes/js/imgareaselect/jquery.imgareaselect$suffix.js", array('jquery'), '0.9.1-20110113' );
	$scripts->add_data( 'imgareaselect', 'group', 1 );

	$scripts->add( 'password-strength-meter', "/wp-admin/js/password-strength-meter$suffix.js", array('jquery'), '20101027' );
	$scripts->add_data( 'password-strength-meter', 'group', 1 );
	$scripts->localize( 'password-strength-meter', 'pwsL10n', array(
		'empty' => __('Strength indicator'),
		'short' => __('Very weak'),
		'bad' => __('Weak'),
		/* translators: password strength */
		'good' => _x('Medium', 'password strength'),
		'strong' => __('Strong'),
		'mismatch' => __('Mismatch'),
		'l10n_print_after' => 'try{convertEntities(pwsL10n);}catch(e){};'
	) );

	$scripts->add( 'user-profile', "/wp-admin/js/user-profile$suffix.js", array( 'jquery', 'password-strength-meter' ), '20110429' );
	$scripts->add_data( 'user-profile', 'group', 1 );

	$scripts->add( 'admin-bar', "/wp-includes/js/admin-bar$suffix.js", false, '20110131' );
	$scripts->add_data( 'admin-bar', 'group', 1 );

	$scripts->add( 'wplink', "/wp-includes/js/tinymce/plugins/wplink/js/wplink$suffix.js", array( 'jquery', 'wpdialogs' ), '20110501' );
	$scripts->add_data( 'wplink', 'group', 1 );
	$scripts->localize( 'wplink', 'wpLinkL10n', array(
		'title' => __('Insert/edit link'),
		'update' => __('Update'),
		'save' => __('Add Link'),
		'noTitle' => __('(no title)'),
		'noMatchesFound' => __('No matches found.'),
		'l10n_print_after' => 'try{convertEntities(wpLinkL10n);}catch(e){};',
	) );

	$scripts->add( 'wpdialogs', "/wp-includes/js/tinymce/plugins/wpdialogs/js/wpdialog$suffix.js", array( 'jquery-ui-dialog' ), '20110421' );
	$scripts->add_data( 'wpdialogs', 'group', 1 );

	$scripts->add( 'wpdialogs-popup', "/wp-includes/js/tinymce/plugins/wpdialogs/js/popup$suffix.js", array( 'wpdialogs' ), '20110421' );
	$scripts->add_data( 'wpdialogs-popup', 'group', 1 );

	if ( is_admin() ) {
		$scripts->add( 'ajaxcat', "/wp-admin/js/cat$suffix.js", array( 'wp-lists' ), '20090102' );
		$scripts->add_data( 'ajaxcat', 'group', 1 );
		$scripts->localize( 'ajaxcat', 'catL10n', array(
			'add' => esc_attr(__('Add')),
			'how' => __('Separate multiple categories with commas.'),
			'l10n_print_after' => 'try{convertEntities(catL10n);}catch(e){};'
		) );

		$scripts->add( 'admin-categories', "/wp-admin/js/categories$suffix.js", array('wp-lists'), '20091201' );
		$scripts->add_data( 'admin-categories', 'group', 1 );

		$scripts->add( 'admin-tags', "/wp-admin/js/tags$suffix.js", array('jquery', 'wp-ajax-response'), '20110429' );
		$scripts->add_data( 'admin-tags', 'group', 1 );
		$scripts->localize( 'admin-tags', 'tagsl10n', array(
			'noPerm' => __('You do not have permission to do that.'),
			'broken' => __('An unidentified error has occurred.'),
			'l10n_print_after' => 'try{convertEntities(tagsl10n);}catch(e){};'
		));

		$scripts->add( 'admin-custom-fields', "/wp-admin/js/custom-fields$suffix.js", array('wp-lists'), '20110429' );
		$scripts->add_data( 'admin-custom-fields', 'group', 1 );

		$scripts->add( 'admin-comments', "/wp-admin/js/edit-comments$suffix.js", array('wp-lists', 'jquery-ui-resizable', 'quicktags', 'jquery-query'), '20110506' );
		$scripts->add_data( 'admin-comments', 'group', 1 );
		$scripts->localize( 'admin-comments', 'adminCommentsL10n', array(
			'hotkeys_highlight_first' => isset($_GET['hotkeys_highlight_first']),
			'hotkeys_highlight_last' => isset($_GET['hotkeys_highlight_last']),
			'replyApprove' => __( 'Approve and Reply' ),
			'reply' => __( 'Reply' )
		) );

		$scripts->add( 'xfn', "/wp-admin/js/xfn$suffix.js", array('jquery'), '20100403' );
		$scripts->add_data( 'xfn', 'group', 1 );

		$scripts->add( 'postbox', "/wp-admin/js/postbox$suffix.js", array('jquery-ui-sortable'), '20091012' );
		$scripts->add_data( 'postbox', 'group', 1 );

		$scripts->add( 'post', "/wp-admin/js/post$suffix.js", array('suggest', 'wp-lists', 'postbox'), '20110429' );
		$scripts->add_data( 'post', 'group', 1 );
		$scripts->localize( 'post', 'postL10n', array(
			'tagsUsed' =>  __('Tags used on this post:'),
			'add' => esc_attr(__('Add')),
			'addTag' => esc_attr(__('Add new Tag')),
			'separate' => __('Separate tags with commas'),
			'ok' => __('OK'),
			'cancel' => __('Cancel'),
			'edit' => __('Edit'),
			'publishOn' => __('Publish on:'),
			'publishOnFuture' =>  __('Schedule for:'),
			'publishOnPast' => __('Published on:'),
			'showcomm' => __('Show more comments'),
			'endcomm' => __('No more comments found.'),
			'publish' => __('Publish'),
			'schedule' => __('Schedule'),
			'update' => __('Update'),
			'savePending' => __('Save as Pending'),
			'saveDraft' => __('Save Draft'),
			'private' => __('Private'),
			'public' => __('Public'),
			'publicSticky' => __('Public, Sticky'),
			'password' => __('Password Protected'),
			'privatelyPublished' => __('Privately Published'),
			'published' => __('Published'),
			'l10n_print_after' => 'try{convertEntities(postL10n);}catch(e){};'
		) );

		$scripts->add( 'link', "/wp-admin/js/link$suffix.js", array('wp-lists', 'postbox'), '20090526' );
		$scripts->add_data( 'link', 'group', 1 );

		$scripts->add( 'comment', "/wp-admin/js/comment$suffix.js", array('jquery'), '20110429' );
		$scripts->add_data( 'comment', 'group', 1 );
		$scripts->localize( 'comment', 'commentL10n', array(
			'cancel' => __('Cancel'),
			'edit' => __('Edit'),
			'submittedOn' => __('Submitted on:'),
			'l10n_print_after' => 'try{convertEntities(commentL10n);}catch(e){};'
		) );

		$scripts->add( 'admin-gallery', "/wp-admin/js/gallery$suffix.js", array( 'jquery-ui-sortable' ), '20110414' );

		$scripts->add( 'media-upload', "/wp-admin/js/media-upload$suffix.js", array( 'thickbox' ), '20110425' );
		$scripts->add_data( 'media-upload', 'group', 1 );

		$scripts->add( 'admin-widgets', "/wp-admin/js/widgets$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ), '20110506' );
		$scripts->add_data( 'admin-widgets', 'group', 1 );

		$scripts->add( 'word-count', "/wp-admin/js/word-count$suffix.js", array( 'jquery' ), '20110424' );
		$scripts->add_data( 'word-count', 'group', 1 );
		$scripts->localize( 'word-count', 'wordCountL10n', array(
			'count' => __('Word count: %d'),
			'l10n_print_after' => 'try{convertEntities(wordCountL10n);}catch(e){};'
		));

		$scripts->add( 'theme', "/wp-admin/js/theme$suffix.js", array( 'thickbox' ), '20110118' );
		$scripts->add_data( 'theme', 'group', 1 );

		$scripts->add( 'theme-preview', "/wp-admin/js/theme-preview$suffix.js", array( 'thickbox', 'jquery' ), '20100407' );
		$scripts->add_data( 'theme-preview', 'group', 1 );

		$scripts->add( 'inline-edit-post', "/wp-admin/js/inline-edit-post$suffix.js", array( 'jquery', 'suggest' ), '20110429' );
		$scripts->add_data( 'inline-edit-post', 'group', 1 );
		$scripts->localize( 'inline-edit-post', 'inlineEditL10n', array(
			'error' => __('Error while saving the changes.'),
			'ntdeltitle' => __('Remove From Bulk Edit'),
			'notitle' => __('(no title)'),
			'l10n_print_after' => 'try{convertEntities(inlineEditL10n);}catch(e){};'
		) );

		$scripts->add( 'inline-edit-tax', "/wp-admin/js/inline-edit-tax$suffix.js", array( 'jquery' ), '20100615' );
		$scripts->add_data( 'inline-edit-tax', 'group', 1 );
		$scripts->localize( 'inline-edit-tax', 'inlineEditL10n', array(
			'error' => __('Error while saving the changes.'),
			'l10n_print_after' => 'try{convertEntities(inlineEditL10n);}catch(e){};'
		) );

		$scripts->add( 'plugin-install', "/wp-admin/js/plugin-install$suffix.js", array( 'jquery', 'thickbox' ), '20110113' );
		$scripts->add_data( 'plugin-install', 'group', 1 );
		$scripts->localize( 'plugin-install', 'plugininstallL10n', array(
			'plugin_information' => __('Plugin Information:'),
			'ays' => __('Are you sure you want to install this plugin?'),
			'l10n_print_after' => 'try{convertEntities(plugininstallL10n);}catch(e){};'
		) );

		$scripts->add( 'farbtastic', '/wp-admin/js/farbtastic.js', array('jquery'), '1.2' );

		$scripts->add( 'dashboard', "/wp-admin/js/dashboard$suffix.js", array( 'jquery', 'admin-comments', 'postbox' ), '20110430a' );
		$scripts->add_data( 'dashboard', 'group', 1 );

		$scripts->add( 'hoverIntent', "/wp-includes/js/hoverIntent$suffix.js", array('jquery'), '20090102' );
		$scripts->add_data( 'hoverIntent', 'group', 1 );

		$scripts->add( 'list-revisions', "/wp-includes/js/wp-list-revisions$suffix.js", null, '20091223' );

		$scripts->add( 'media', "/wp-admin/js/media$suffix.js", array( 'jquery-ui-draggable' ), '20101022' );
		$scripts->add_data( 'media', 'group', 1 );

		$scripts->add( 'image-edit', "/wp-admin/js/image-edit$suffix.js", array('jquery', 'json2', 'imgareaselect'), '20110429' );
		$scripts->add_data( 'image-edit', 'group', 1 );

		$scripts->add( 'set-post-thumbnail', "/wp-admin/js/set-post-thumbnail$suffix.js", array( 'jquery' ), '20100518' );
		$scripts->add_data( 'set-post-thumbnail', 'group', 1 );
		$scripts->localize( 'set-post-thumbnail', 'setPostThumbnailL10n', array(
			'setThumbnail' => __( 'Use as featured image' ),
			'saving' => __( 'Saving...' ),
			'error' => __( 'Could not set that as the thumbnail image. Try a different attachment.' ),
			'done' => __( 'Done' ),
			'l10n_print_after' => 'try{convertEntities(setPostThumbnailL10n);}catch(e){};'
		) );

		// Navigation Menus
		$scripts->add( 'nav-menu', "/wp-admin/js/nav-menu$suffix.js", array('jquery-ui-sortable'), '20110429' );
		$scripts->localize( 'nav-menu', 'navMenuL10n', array(
			'noResultsFound' => _x('No results found.', 'search results'),
			'warnDeleteMenu' => __( "You are about to permanently delete this menu. \n 'Cancel' to stop, 'OK' to delete." ),
			'saveAlert' => __('The changes you made will be lost if you navigate away from this page.'),
			'l10n_print_after' => 'try{convertEntities(navMenuL10n);}catch(e){};'
		) );

		$scripts->add( 'custom-background', "/wp-admin/js/custom-background$suffix.js", array('farbtastic'), '20101025' );
		$scripts->add_data( 'custom-background', 'group', 1 );
	}
}

/**
 * Assign default styles to $styles object.
 *
 * Nothing is returned, because the $styles parameter is passed by reference.
 * Meaning that whatever object is passed will be updated without having to
 * reassign the variable that was passed back to the same value. This saves
 * memory.
 *
 * Adding default styles is not the only task, it also assigns the base_url
 * property, the default version, and text direction for the object.
 *
 * @since 2.6.0
 *
 * @param object $styles
 */
function wp_default_styles( &$styles ) {
	// This checks to see if site_url() returns something and if it does not
	// then it assigns $guess_url to wp_guess_url(). Strange format, but it works.
	if ( ! $guessurl = site_url() )
		$guessurl = wp_guess_url();

	$styles->base_url = $guessurl;
	$styles->content_url = defined('WP_CONTENT_URL')? WP_CONTENT_URL : '';
	$styles->default_version = get_bloginfo( 'version' );
	$styles->text_direction = function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr';
	$styles->default_dirs = array('/wp-admin/');

	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';

	$rtl_styles = array( 'wp-admin', 'global', 'colors', 'colors-fresh', 'colors-classic', 'dashboard', 'ie', 'install', 'login', 'media', 'theme-editor', 'upload', 'widgets', 'press-this', 'plugin-install', 'nav-menu', 'farbtastic', 'admin-bar', 'wplink', 'theme-install' );
	// Any rtl stylesheets that don't have a .dev version for ltr
	$no_suffix = array( 'farbtastic' );

	$styles->add( 'wp-admin', "/wp-admin/css/wp-admin$suffix.css", array(), '20110507' );

	$styles->add( 'ie', "/wp-admin/css/ie$suffix.css", array(), '20101102' );
	$styles->add_data( 'ie', 'conditional', 'lte IE 7' );

	// all colors stylesheets need to have the same query strings (cache manifest compat)
	$colors_version = '20110507';

	// Register "meta" stylesheet for admin colors. All colors-* style sheets should have the same version string.
	$styles->add( 'colors', true, array(), $colors_version );

	// do not refer to these directly, the right one is queued by the above "meta" colors handle
	$styles->add( 'colors-fresh', "/wp-admin/css/colors-fresh$suffix.css", array(), $colors_version );
	$styles->add( 'colors-classic', "/wp-admin/css/colors-classic$suffix.css", array(), $colors_version );

	$styles->add( 'ms', "/wp-admin/css/ms$suffix.css", array(), '20101213' );
	$styles->add( 'global', "/wp-admin/css/global$suffix.css", array(), '20110507' );
	$styles->add( 'media', "/wp-admin/css/media$suffix.css", array(), '20110121' );
	$styles->add( 'widgets', "/wp-admin/css/widgets$suffix.css", array(), '20110504' );
	$styles->add( 'dashboard', "/wp-admin/css/dashboard$suffix.css", array(), '20110506' );
	$styles->add( 'install', "/wp-admin/css/install$suffix.css", array(), '20110506' ); // Readme as well
	$styles->add( 'theme-editor', "/wp-admin/css/theme-editor$suffix.css", array(), '20110506' );
	$styles->add( 'press-this', "/wp-admin/css/press-this$suffix.css", array(), '20110506' );
	$styles->add( 'thickbox', '/wp-includes/js/thickbox/thickbox.css', array(), '20090514' );
	$styles->add( 'login', "/wp-admin/css/login$suffix.css", array(), '20110121' );
	$styles->add( 'plugin-install', "/wp-admin/css/plugin-install$suffix.css", array(), '20101230' );
	$styles->add( 'theme-install', "/wp-admin/css/theme-install$suffix.css", array(), '20110506' );
	$styles->add( 'farbtastic', '/wp-admin/css/farbtastic.css', array(), '1.3u' );
	$styles->add( 'jcrop', '/wp-includes/js/jcrop/jquery.Jcrop.css', array(), '0.9.8' );
	$styles->add( 'imgareaselect', '/wp-includes/js/imgareaselect/imgareaselect.css', array(), '0.9.1' );
	$styles->add( 'nav-menu', "/wp-admin/css/nav-menu$suffix.css", array(), '20110506' );
	$styles->add( 'admin-bar', "/wp-includes/css/admin-bar$suffix.css", array(), '20110419' );
	$styles->add( 'wp-jquery-ui-dialog', "/wp-includes/css/jquery-ui-dialog$suffix.css", array(), '20101224' );
	$styles->add( 'wplink', "/wp-includes/js/tinymce/plugins/wplink/css/wplink$suffix.css", array(), '20101224' );

	foreach ( $rtl_styles as $rtl_style ) {
		$styles->add_data( $rtl_style, 'rtl', true );
		if ( $suffix && ! in_array( $rtl_style, $no_suffix ) )
			$styles->add_data( $rtl_style, 'suffix', $suffix );
	}
}

/**
 * Reorder JavaScript scripts array to place prototype before jQuery.
 *
 * @since 2.3.1
 *
 * @param array $js_array JavaScript scripst array
 * @return array Reordered array, if needed.
 */
function wp_prototype_before_jquery( $js_array ) {
	if ( false === $jquery = array_search( 'jquery', $js_array, true ) )
		return $js_array;

	if ( false === $prototype = array_search( 'prototype', $js_array, true ) )
		return $js_array;

	if ( $prototype < $jquery )
		return $js_array;

	unset($js_array[$prototype]);

	array_splice( $js_array, $jquery, 0, 'prototype' );

	return $js_array;
}

/**
 * Load localized data on print rather than initialization.
 *
 * These localizations require information that may not be loaded even by init.
 *
 * @since 2.5.0
 */
function wp_just_in_time_script_localization() {

	wp_localize_script( 'autosave', 'autosaveL10n', array(
		'autosaveInterval' => AUTOSAVE_INTERVAL,
		'previewPageText' => __('Preview this Page'),
		'previewPostText' => __('Preview this Post'),
		'requestFile' => admin_url('admin-ajax.php'),
		'savingText' => __('Saving Draft&#8230;'),
		'saveAlert' => __('The changes you made will be lost if you navigate away from this page.'),
		'l10n_print_after' => 'try{convertEntities(autosaveL10n);}catch(e){};'
	) );

}

/**
 * Administration Screen CSS for changing the styles.
 *
 * If installing the 'wp-admin/' directory will be replaced with './'.
 *
 * The $_wp_admin_css_colors global manages the Administration Screens CSS
 * stylesheet that is loaded. The option that is set is 'admin_color' and is the
 * color and key for the array. The value for the color key is an object with
 * a 'url' parameter that has the URL path to the CSS file.
 *
 * The query from $src parameter will be appended to the URL that is given from
 * the $_wp_admin_css_colors array value URL.
 *
 * @since 2.6.0
 * @uses $_wp_admin_css_colors
 *
 * @param string $src Source URL.
 * @param string $handle Either 'colors' or 'colors-rtl'.
 * @return string URL path to CSS stylesheet for Administration Screens.
 */
function wp_style_loader_src( $src, $handle ) {
	if ( defined('WP_INSTALLING') )
		return preg_replace( '#^wp-admin/#', './', $src );

	if ( 'colors' == $handle || 'colors-rtl' == $handle ) {
		global $_wp_admin_css_colors;
		$color = get_user_option('admin_color');

		if ( empty($color) || !isset($_wp_admin_css_colors[$color]) )
			$color = 'fresh';

		$color = $_wp_admin_css_colors[$color];
		$parsed = parse_url( $src );
		$url = $color->url;

		if ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG )
			$url = preg_replace('/.css$|.css(?=\?)/', '.dev.css', $url);

		if ( isset($parsed['query']) && $parsed['query'] ) {
			wp_parse_str( $parsed['query'], $qv );
			$url = add_query_arg( $qv, $url );
		}

		return $url;
	}

	return $src;
}

/**
 * Prints the script queue in the HTML head on admin pages.
 *
 * Postpones the scripts that were queued for the footer.
 * print_footer_scripts() is called in the footer to print these scripts.
 *
 * @since 2.8
 * @see wp_print_scripts()
 */
function print_head_scripts() {
	global $wp_scripts, $concatenate_scripts;

	if ( ! did_action('wp_print_scripts') )
		do_action('wp_print_scripts');

	if ( !is_a($wp_scripts, 'WP_Scripts') )
		$wp_scripts = new WP_Scripts();

	script_concat_settings();
	$wp_scripts->do_items( 'l10n' );
	$wp_scripts->do_concat = $concatenate_scripts;
	$wp_scripts->do_head_items();

	if ( apply_filters('print_head_scripts', true) )
		_print_scripts();

	$wp_scripts->reset();
	return $wp_scripts->done;
}

/**
 * Prints the scripts that were queued for the footer on admin pages.
 *
 * @since 2.8
 */
function print_footer_scripts() {
	global $wp_scripts, $concatenate_scripts;

	if ( ! did_action('wp_print_footer_scripts') )
		do_action('wp_print_footer_scripts');

	if ( !is_a($wp_scripts, 'WP_Scripts') )
		return array(); // No need to run if not instantiated.

	script_concat_settings();
	$wp_scripts->do_concat = $concatenate_scripts;
	$wp_scripts->do_footer_items();

	if ( apply_filters('print_footer_scripts', true) )
		_print_scripts();

	$wp_scripts->reset();
	return $wp_scripts->done;
}

function _print_scripts() {
	global $wp_scripts, $compress_scripts;

	$zip = $compress_scripts ? 1 : 0;
	if ( $zip && defined('ENFORCE_GZIP') && ENFORCE_GZIP )
		$zip = 'gzip';

	if ( !empty($wp_scripts->concat) ) {

		if ( !empty($wp_scripts->print_code) ) {
			echo "<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n";
			echo $wp_scripts->print_code;
			echo "/* ]]> */\n";
			echo "</script>\n";
		}

		$ver = md5("$wp_scripts->concat_version");
		$src = $wp_scripts->base_url . "/wp-admin/load-scripts.php?c={$zip}&load=" . trim($wp_scripts->concat, ', ') . "&ver=$ver";
		echo "<script type='text/javascript' src='" . esc_attr($src) . "'></script>\n";
	}

	if ( !empty($wp_scripts->print_html) )
		echo $wp_scripts->print_html;
}

/**
 * Prints the script queue in the HTML head on the front end.
 *
 * Postpones the scripts that were queued for the footer.
 * wp_print_footer_scripts() is called in the footer to print these scripts.
 *
 * @since 2.8
 */
function wp_print_head_scripts() {
	if ( ! did_action('wp_print_scripts') )
		do_action('wp_print_scripts');

	global $wp_scripts;

	if ( !is_a($wp_scripts, 'WP_Scripts') )
		return array(); // no need to run if nothing is queued

	return print_head_scripts();
}

/**
 * Prints the scripts that were queued for the footer on the front end.
 *
 * @since 2.8
 */
function wp_print_footer_scripts() {
	return print_footer_scripts();
}

/**
 * Wrapper for do_action('wp_enqueue_scripts')
 *
 * Allows plugins to queue scripts for the front end using wp_enqueue_script().
 * Runs first in wp_head() where all is_home(), is_page(), etc. functions are available.
 *
 * @since 2.8
 */
function wp_enqueue_scripts() {
	do_action('wp_enqueue_scripts');
}

function print_admin_styles() {
	global $wp_styles, $concatenate_scripts, $compress_css;

	if ( !is_a($wp_styles, 'WP_Styles') )
		$wp_styles = new WP_Styles();

	script_concat_settings();
	$wp_styles->do_concat = $concatenate_scripts;
	$zip = $compress_css ? 1 : 0;
	if ( $zip && defined('ENFORCE_GZIP') && ENFORCE_GZIP )
		$zip = 'gzip';

	$wp_styles->do_items(false);

	if ( apply_filters('print_admin_styles', true) ) {
		if ( !empty($wp_styles->concat) ) {
			$dir = $wp_styles->text_direction;
			$ver = md5("$wp_styles->concat_version{$dir}");
			$href = $wp_styles->base_url . "/wp-admin/load-styles.php?c={$zip}&dir={$dir}&load=" . trim($wp_styles->concat, ', ') . "&ver=$ver";
			echo "<link rel='stylesheet' href='" . esc_attr($href) . "' type='text/css' media='all' />\n";
		}

		if ( !empty($wp_styles->print_html) )
			echo $wp_styles->print_html;
	}

	$wp_styles->do_concat = false;
	$wp_styles->concat = $wp_styles->concat_version = $wp_styles->print_html = '';
	return $wp_styles->done;
}

function script_concat_settings() {
	global $concatenate_scripts, $compress_scripts, $compress_css;

	$compressed_output = ( ini_get('zlib.output_compression') || 'ob_gzhandler' == ini_get('output_handler') );

	if ( ! isset($concatenate_scripts) ) {
		$concatenate_scripts = defined('CONCATENATE_SCRIPTS') ? CONCATENATE_SCRIPTS : true;
		if ( ! is_admin() || ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) )
			$concatenate_scripts = false;
	}

	if ( ! isset($compress_scripts) ) {
		$compress_scripts = defined('COMPRESS_SCRIPTS') ? COMPRESS_SCRIPTS : true;
		if ( $compress_scripts && ( ! get_site_option('can_compress_scripts') || $compressed_output ) )
			$compress_scripts = false;
	}

	if ( ! isset($compress_css) ) {
		$compress_css = defined('COMPRESS_CSS') ? COMPRESS_CSS : true;
		if ( $compress_css && ( ! get_site_option('can_compress_scripts') || $compressed_output ) )
			$compress_css = false;
	}
}

add_action( 'wp_default_scripts', 'wp_default_scripts' );
add_filter( 'wp_print_scripts', 'wp_just_in_time_script_localization' );
add_filter( 'print_scripts_array', 'wp_prototype_before_jquery' );

add_action( 'wp_default_styles', 'wp_default_styles' );
add_filter( 'style_loader_src', 'wp_style_loader_src', 10, 2 );
