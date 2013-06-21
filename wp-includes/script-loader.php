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
 * Register all WordPress scripts.
 *
 * Localizes some of them.
 * args order: $scripts->add( 'handle', 'url', 'dependencies', 'query-string', 1 );
 * when last arg === 1 queues the script for the footer
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

	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

	$scripts->add( 'utils', "/wp-includes/js/utils$suffix.js" );
	did_action( 'init' ) && $scripts->localize( 'utils', 'userSettings', array(
		'url' => (string) SITECOOKIEPATH,
		'uid' => (string) get_current_user_id(),
		'time' => (string) time(),
	) );

	$scripts->add( 'common', "/wp-admin/js/common$suffix.js", array('jquery', 'hoverIntent', 'utils'), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'common', 'commonL10n', array(
		'warnDelete' => __("You are about to permanently delete the selected items.\n  'Cancel' to stop, 'OK' to delete.")
	) );

	$scripts->add( 'sack', "/wp-includes/js/tw-sack$suffix.js", array(), '1.6.1', 1 );

	$scripts->add( 'quicktags', "/wp-includes/js/quicktags$suffix.js", array(), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'quicktags', 'quicktagsL10n', array(
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
		'textdirection' => esc_attr( __('text direction') ),
		'toggleTextdirection' => esc_attr( __('Toggle Editor Text Direction') )
	) );

	$scripts->add( 'colorpicker', "/wp-includes/js/colorpicker$suffix.js", array('prototype'), '3517m' );

	$scripts->add( 'editor', "/wp-admin/js/editor$suffix.js", array('utils','jquery'), false, 1 );

	$scripts->add( 'wp-fullscreen', "/wp-admin/js/wp-fullscreen$suffix.js", array('jquery'), false, 1 );

	$scripts->add( 'wp-ajax-response', "/wp-includes/js/wp-ajax-response$suffix.js", array('jquery'), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'wp-ajax-response', 'wpAjax', array(
		'noPerm' => __('You do not have permission to do that.'),
		'broken' => __('An unidentified error has occurred.')
	) );

	$scripts->add( 'wp-pointer', "/wp-includes/js/wp-pointer$suffix.js", array( 'jquery-ui-widget', 'jquery-ui-position' ), '20111129a', 1 );
	did_action( 'init' ) && $scripts->localize( 'wp-pointer', 'wpPointerL10n', array(
		'dismiss' => __('Dismiss'),
	) );

	$scripts->add( 'autosave', "/wp-includes/js/autosave$suffix.js", array('schedule', 'wp-ajax-response'), false, 1 );

	$scripts->add( 'wp-lists', "/wp-includes/js/wp-lists$suffix.js", array( 'wp-ajax-response', 'jquery-color' ), false, 1 );

	// WordPress no longer uses or bundles Prototype or script.aculo.us. These are now pulled from an external source.
	$scripts->add( 'prototype', '//ajax.googleapis.com/ajax/libs/prototype/1.7.1.0/prototype.js', array(), '1.7.1');
	$scripts->add( 'scriptaculous-root', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/scriptaculous.js', array('prototype'), '1.9.0');
	$scripts->add( 'scriptaculous-builder', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/builder.js', array('scriptaculous-root'), '1.9.0');
	$scripts->add( 'scriptaculous-dragdrop', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/dragdrop.js', array('scriptaculous-builder', 'scriptaculous-effects'), '1.9.0');
	$scripts->add( 'scriptaculous-effects', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/effects.js', array('scriptaculous-root'), '1.9.0');
	$scripts->add( 'scriptaculous-slider', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/slider.js', array('scriptaculous-effects'), '1.9.0');
	$scripts->add( 'scriptaculous-sound', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/sound.js', array( 'scriptaculous-root' ), '1.9.0' );
	$scripts->add( 'scriptaculous-controls', '//ajax.googleapis.com/ajax/libs/scriptaculous/1.9.0/controls.js', array('scriptaculous-root'), '1.9.0');
	$scripts->add( 'scriptaculous', false, array('scriptaculous-dragdrop', 'scriptaculous-slider', 'scriptaculous-controls') );

	// not used in core, replaced by Jcrop.js
	$scripts->add( 'cropper', '/wp-includes/js/crop/cropper.js', array('scriptaculous-dragdrop') );

	$scripts->add( 'jquery', '/wp-includes/js/jquery/jquery.js', array(), '1.8.3' );

	// full jQuery UI
	$scripts->add( 'jquery-ui-core', '/wp-includes/js/jquery/ui/jquery.ui.core.min.js', array('jquery'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-core', '/wp-includes/js/jquery/ui/jquery.ui.effect.min.js', array('jquery'), '1.9.2', 1 );

	$scripts->add( 'jquery-effects-blind', '/wp-includes/js/jquery/ui/jquery.ui.effect-blind.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-bounce', '/wp-includes/js/jquery/ui/jquery.ui.effect-bounce.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-clip', '/wp-includes/js/jquery/ui/jquery.ui.effect-clip.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-drop', '/wp-includes/js/jquery/ui/jquery.ui.effect-drop.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-explode', '/wp-includes/js/jquery/ui/jquery.ui.effect-explode.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-fade', '/wp-includes/js/jquery/ui/jquery.ui.effect-fade.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-fold', '/wp-includes/js/jquery/ui/jquery.ui.effect-fold.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-highlight', '/wp-includes/js/jquery/ui/jquery.ui.effect-highlight.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-pulsate', '/wp-includes/js/jquery/ui/jquery.ui.effect-pulsate.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-scale', '/wp-includes/js/jquery/ui/jquery.ui.effect-scale.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-shake', '/wp-includes/js/jquery/ui/jquery.ui.effect-shake.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-slide', '/wp-includes/js/jquery/ui/jquery.ui.effect-slide.min.js', array('jquery-effects-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-effects-transfer', '/wp-includes/js/jquery/ui/jquery.ui.effect-transfer.min.js', array('jquery-effects-core'), '1.9.2', 1 );

	$scripts->add( 'jquery-ui-accordion', '/wp-includes/js/jquery/ui/jquery.ui.accordion.min.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-autocomplete', '/wp-includes/js/jquery/ui/jquery.ui.autocomplete.min.js', array('jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-menu'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-button', '/wp-includes/js/jquery/ui/jquery.ui.button.min.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-datepicker', '/wp-includes/js/jquery/ui/jquery.ui.datepicker.min.js', array('jquery-ui-core'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-dialog', '/wp-includes/js/jquery/ui/jquery.ui.dialog.min.js', array('jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-draggable', '/wp-includes/js/jquery/ui/jquery.ui.draggable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-droppable', '/wp-includes/js/jquery/ui/jquery.ui.droppable.min.js', array('jquery-ui-draggable'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-menu', '/wp-includes/js/jquery/ui/jquery.ui.menu.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-mouse', '/wp-includes/js/jquery/ui/jquery.ui.mouse.min.js', array('jquery-ui-widget'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-position', '/wp-includes/js/jquery/ui/jquery.ui.position.min.js', array('jquery'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-progressbar', '/wp-includes/js/jquery/ui/jquery.ui.progressbar.min.js', array('jquery-ui-widget'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-resizable', '/wp-includes/js/jquery/ui/jquery.ui.resizable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-selectable', '/wp-includes/js/jquery/ui/jquery.ui.selectable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-slider', '/wp-includes/js/jquery/ui/jquery.ui.slider.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-sortable', '/wp-includes/js/jquery/ui/jquery.ui.sortable.min.js', array('jquery-ui-core', 'jquery-ui-mouse'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-spinner', '/wp-includes/js/jquery/ui/jquery.ui.spinner.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-button' ), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-tabs', '/wp-includes/js/jquery/ui/jquery.ui.tabs.min.js', array('jquery-ui-core', 'jquery-ui-widget'), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-tooltip', '/wp-includes/js/jquery/ui/jquery.ui.tooltip.min.js', array( 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position' ), '1.9.2', 1 );
	$scripts->add( 'jquery-ui-widget', '/wp-includes/js/jquery/ui/jquery.ui.widget.min.js', array('jquery'), '1.9.2', 1 );

	// deprecated, not used in core, most functionality is included in jQuery 1.3
	$scripts->add( 'jquery-form', "/wp-includes/js/jquery/jquery.form$suffix.js", array('jquery'), '2.73', 1 );

	// jQuery plugins
	$scripts->add( 'jquery-color', "/wp-includes/js/jquery/jquery.color.min.js", array('jquery'), '2.1.0', 1 );
	$scripts->add( 'suggest', "/wp-includes/js/jquery/suggest$suffix.js", array('jquery'), '1.1-20110113', 1 );
	$scripts->add( 'schedule', '/wp-includes/js/jquery/jquery.schedule.js', array('jquery'), '20m', 1 );
	$scripts->add( 'jquery-query', "/wp-includes/js/jquery/jquery.query.js", array('jquery'), '2.1.7', 1 );
	$scripts->add( 'jquery-serialize-object', "/wp-includes/js/jquery/jquery.serialize-object.js", array('jquery'), '0.2', 1 );
	$scripts->add( 'jquery-hotkeys', "/wp-includes/js/jquery/jquery.hotkeys$suffix.js", array('jquery'), '0.0.2m', 1 );
	$scripts->add( 'jquery-table-hotkeys', "/wp-includes/js/jquery/jquery.table-hotkeys$suffix.js", array('jquery', 'jquery-hotkeys'), false, 1 );
	$scripts->add( 'jquery-touch-punch', "/wp-includes/js/jquery/jquery.ui.touch-punch.js", array('jquery-ui-widget', 'jquery-ui-mouse'), '0.2.2', 1 );
	$scripts->add( 'jquery-masonry', "/wp-includes/js/jquery/jquery.masonry.min.js", array('jquery'), '2.1.05', 1 );

	$scripts->add( 'thickbox', "/wp-includes/js/thickbox/thickbox.js", array('jquery'), '3.1-20121105', 1 );
	did_action( 'init' ) && $scripts->localize( 'thickbox', 'thickboxL10n', array(
			'next' => __('Next &gt;'),
			'prev' => __('&lt; Prev'),
			'image' => __('Image'),
			'of' => __('of'),
			'close' => __('Close'),
			'noiframes' => __('This feature requires inline frames. You have iframes disabled or your browser does not support them.'),
			'loadingAnimation' => includes_url('js/thickbox/loadingAnimation.gif'),
			'closeImage' => includes_url('js/thickbox/tb-close.png')
	) );

	$scripts->add( 'jcrop', "/wp-includes/js/jcrop/jquery.Jcrop.min.js", array('jquery'), '0.9.10');

	$scripts->add( 'swfobject', "/wp-includes/js/swfobject.js", array(), '2.2-20120417');

	// common bits for both uploaders
	$max_upload_size = ( (int) ( $max_up = @ini_get('upload_max_filesize') ) < (int) ( $max_post = @ini_get('post_max_size') ) ) ? $max_up : $max_post;

	if ( empty($max_upload_size) )
		$max_upload_size = __('not configured');

	// error message for both plupload and swfupload
	$uploader_l10n = array(
		'queue_limit_exceeded' => __('You have attempted to queue too many files.'),
		'file_exceeds_size_limit' => __('%s exceeds the maximum upload size for this site.'),
		'zero_byte_file' => __('This file is empty. Please try another.'),
		'invalid_filetype' => __('This file type is not allowed. Please try another.'),
		'not_an_image' => __('This file is not an image. Please try another.'),
		'image_memory_exceeded' => __('Memory exceeded. Please try another smaller file.'),
		'image_dimensions_exceeded' => __('This is larger than the maximum size. Please try another.'),
		'default_error' => __('An error occurred in the upload. Please try again later.'),
		'missing_upload_url' => __('There was a configuration error. Please contact the server administrator.'),
		'upload_limit_exceeded' => __('You may only upload 1 file.'),
		'http_error' => __('HTTP error.'),
		'upload_failed' => __('Upload failed.'),
		'big_upload_failed' => __('Please try uploading this file with the %1$sbrowser uploader%2$s.'),
		'big_upload_queued' => __('%s exceeds the maximum upload size for the multi-file uploader when used in your browser.'),
		'io_error' => __('IO error.'),
		'security_error' => __('Security error.'),
		'file_cancelled' => __('File canceled.'),
		'upload_stopped' => __('Upload stopped.'),
		'dismiss' => __('Dismiss'),
		'crunching' => __('Crunching&hellip;'),
		'deleted' => __('moved to the trash.'),
		'error_uploading' => __('&#8220;%s&#8221; has failed to upload.')
	);

	$scripts->add( 'plupload', '/wp-includes/js/plupload/plupload.js', array(), '1.5.5' );
	$scripts->add( 'plupload-html5', '/wp-includes/js/plupload/plupload.html5.js', array('plupload'), '1.5.5' );
	$scripts->add( 'plupload-flash', '/wp-includes/js/plupload/plupload.flash.js', array('plupload'), '1.5.5' );
	$scripts->add( 'plupload-silverlight', '/wp-includes/js/plupload/plupload.silverlight.js', array('plupload'), '1.5.5' );
	$scripts->add( 'plupload-html4', '/wp-includes/js/plupload/plupload.html4.js', array('plupload'), '1.5.5' );

	// cannot use the plupload.full.js, as it loads browserplus init JS from Yahoo
	$scripts->add( 'plupload-all', false, array('plupload', 'plupload-html5', 'plupload-flash', 'plupload-silverlight', 'plupload-html4'), '1.5.5' );

	$scripts->add( 'plupload-handlers', "/wp-includes/js/plupload/handlers$suffix.js", array('plupload-all', 'jquery') );
	did_action( 'init' ) && $scripts->localize( 'plupload-handlers', 'pluploadL10n', $uploader_l10n );

	$scripts->add( 'wp-plupload', "/wp-includes/js/plupload/wp-plupload$suffix.js", array('plupload-all', 'jquery', 'json2', 'media-models'), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'wp-plupload', 'pluploadL10n', $uploader_l10n );

	// keep 'swfupload' for back-compat.
	$scripts->add( 'swfupload', '/wp-includes/js/swfupload/swfupload.js', array(), '2201-20110113');
	$scripts->add( 'swfupload-swfobject', '/wp-includes/js/swfupload/plugins/swfupload.swfobject.js', array('swfupload', 'swfobject'), '2201a');
	$scripts->add( 'swfupload-queue', '/wp-includes/js/swfupload/plugins/swfupload.queue.js', array('swfupload'), '2201');
	$scripts->add( 'swfupload-speed', '/wp-includes/js/swfupload/plugins/swfupload.speed.js', array('swfupload'), '2201');
	$scripts->add( 'swfupload-all', false, array('swfupload', 'swfupload-swfobject', 'swfupload-queue'), '2201');
	$scripts->add( 'swfupload-handlers', "/wp-includes/js/swfupload/handlers$suffix.js", array('swfupload-all', 'jquery'), '2201-20110524');
	did_action( 'init' ) && $scripts->localize( 'swfupload-handlers', 'swfuploadL10n', $uploader_l10n );

	$scripts->add( 'comment-reply', "/wp-includes/js/comment-reply$suffix.js" );

	$scripts->add( 'json2', "/wp-includes/js/json2$suffix.js", array(), '2011-02-23');

	$scripts->add( 'underscore', '/wp-includes/js/underscore.min.js', array(), '1.4.0', 1 );
	$scripts->add( 'backbone', '/wp-includes/js/backbone.min.js', array('underscore','jquery'), '0.9.2', 1 );

	$scripts->add( 'imgareaselect', "/wp-includes/js/imgareaselect/jquery.imgareaselect$suffix.js", array('jquery'), '0.9.8', 1 );

	$scripts->add( 'password-strength-meter', "/wp-admin/js/password-strength-meter$suffix.js", array('jquery'), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'password-strength-meter', 'pwsL10n', array(
		'empty' => __('Strength indicator'),
		'short' => __('Very weak'),
		'bad' => __('Weak'),
		/* translators: password strength */
		'good' => _x('Medium', 'password strength'),
		'strong' => __('Strong'),
		'mismatch' => __('Mismatch')
	) );

	$scripts->add( 'user-profile', "/wp-admin/js/user-profile$suffix.js", array( 'jquery', 'password-strength-meter' ), false, 1 );

	$scripts->add( 'user-suggest', "/wp-admin/js/user-suggest$suffix.js", array( 'jquery-ui-autocomplete' ), false, 1 );

	$scripts->add( 'admin-bar', "/wp-includes/js/admin-bar$suffix.js", array(), false, 1 );

	$scripts->add( 'wplink', "/wp-includes/js/wplink$suffix.js", array( 'jquery', 'wpdialogs' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'wplink', 'wpLinkL10n', array(
		'title' => __('Insert/edit link'),
		'update' => __('Update'),
		'save' => __('Add Link'),
		'noTitle' => __('(no title)'),
		'noMatchesFound' => __('No matches found.')
	) );

	$scripts->add( 'wpdialogs', "/wp-includes/js/tinymce/plugins/wpdialogs/js/wpdialog$suffix.js", array( 'jquery-ui-dialog' ), false, 1 );

	$scripts->add( 'wpdialogs-popup', "/wp-includes/js/tinymce/plugins/wpdialogs/js/popup$suffix.js", array( 'wpdialogs' ), false, 1 );

	$scripts->add( 'word-count', "/wp-admin/js/word-count$suffix.js", array( 'jquery' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'word-count', 'wordCountL10n', array(
		/* translators: If your word count is based on single characters (East Asian characters),
		   enter 'characters'. Otherwise, enter 'words'. Do not translate into your own language. */
		'type' => 'characters' == _x( 'words', 'word count: words or characters?' ) ? 'c' : 'w',
	) );

	$scripts->add( 'media-upload', "/wp-admin/js/media-upload$suffix.js", array( 'thickbox', 'shortcode' ), false, 1 );

	$scripts->add( 'hoverIntent', "/wp-includes/js/hoverIntent$suffix.js", array('jquery'), 'r6', 1 );

	$scripts->add( 'customize-base',     "/wp-includes/js/customize-base$suffix.js",     array( 'jquery', 'json2' ), false, 1 );
	$scripts->add( 'customize-loader',   "/wp-includes/js/customize-loader$suffix.js",   array( 'customize-base' ), false, 1 );
	$scripts->add( 'customize-preview',  "/wp-includes/js/customize-preview$suffix.js",  array( 'customize-base' ), false, 1 );
	$scripts->add( 'customize-controls', "/wp-admin/js/customize-controls$suffix.js", array( 'customize-base' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'customize-controls', '_wpCustomizeControlsL10n', array(
		'activate'  => __( 'Save &amp; Activate' ),
		'save'      => __( 'Save &amp; Publish' ),
		'saved'     => __( 'Saved' ),
		'cancel'    => __( 'Cancel' ),
		'close'     => __( 'Close' ),
		'cheatin'   => __( 'Cheatin&#8217; uh?' ),

		// Used for overriding the file types allowed in plupload.
		'allowedFiles' => __( 'Allowed Files' ),
	) );

	$scripts->add( 'shortcode', "/wp-includes/js/shortcode$suffix.js", array( 'underscore' ), false, 1 );
	$scripts->add( 'media-models', "/wp-includes/js/media-models$suffix.js", array( 'backbone', 'jquery' ), false, 1 );
	did_action( 'init' ) && $scripts->localize( 'media-models', '_wpMediaModelsL10n', array(
		'settings' => array(
			'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
			'post' => array( 'id' => 0 ),
		),
	) );

	// To enqueue media-views or media-editor, call wp_enqueue_media().
	// Both rely on numerous settings, styles, and templates to operate correctly.
	$scripts->add( 'media-views',  "/wp-includes/js/media-views$suffix.js",  array( 'utils', 'media-models', 'wp-plupload', 'jquery-ui-sortable' ), false, 1 );
	$scripts->add( 'media-editor', "/wp-includes/js/media-editor$suffix.js", array( 'shortcode', 'media-views' ), false, 1 );
	$scripts->add( 'mce-view', "/wp-includes/js/mce-view$suffix.js", array( 'shortcode', 'media-models' ), false, 1 );

	if ( is_admin() ) {
		$scripts->add( 'ajaxcat', "/wp-admin/js/cat$suffix.js", array( 'wp-lists' ) );
		$scripts->add_data( 'ajaxcat', 'group', 1 );
		did_action( 'init' ) && $scripts->localize( 'ajaxcat', 'catL10n', array(
			'add' => esc_attr(__('Add')),
			'how' => __('Separate multiple categories with commas.')
		) );

		$scripts->add( 'admin-categories', "/wp-admin/js/categories$suffix.js", array('wp-lists'), false, 1 );

		$scripts->add( 'admin-tags', "/wp-admin/js/tags$suffix.js", array('jquery', 'wp-ajax-response'), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'admin-tags', 'tagsl10n', array(
			'noPerm' => __('You do not have permission to do that.'),
			'broken' => __('An unidentified error has occurred.')
		));

		$scripts->add( 'admin-custom-fields', "/wp-admin/js/custom-fields$suffix.js", array('wp-lists'), false, 1 );

		$scripts->add( 'admin-comments', "/wp-admin/js/edit-comments$suffix.js", array('wp-lists', 'quicktags', 'jquery-query'), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'admin-comments', 'adminCommentsL10n', array(
			'hotkeys_highlight_first' => isset($_GET['hotkeys_highlight_first']),
			'hotkeys_highlight_last' => isset($_GET['hotkeys_highlight_last']),
			'replyApprove' => __( 'Approve and Reply' ),
			'reply' => __( 'Reply' )
		) );

		$scripts->add( 'xfn', "/wp-admin/js/xfn$suffix.js", array('jquery'), false, 1 );

		$scripts->add( 'postbox', "/wp-admin/js/postbox$suffix.js", array('jquery-ui-sortable'), false, 1 );

		$scripts->add( 'post', "/wp-admin/js/post$suffix.js", array('suggest', 'wp-lists', 'postbox'), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'post', 'postL10n', array(
			'ok' => __('OK'),
			'cancel' => __('Cancel'),
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
			'comma' => _x( ',', 'tag delimiter' ),
		) );

		$scripts->add( 'link', "/wp-admin/js/link$suffix.js", array( 'wp-lists', 'postbox' ), false, 1 );

		$scripts->add( 'comment', "/wp-admin/js/comment$suffix.js", array( 'jquery', 'postbox' ) );
		$scripts->add_data( 'comment', 'group', 1 );
		did_action( 'init' ) && $scripts->localize( 'comment', 'commentL10n', array(
			'submittedOn' => __('Submitted on:')
		) );

		$scripts->add( 'admin-gallery', "/wp-admin/js/gallery$suffix.js", array( 'jquery-ui-sortable' ) );

		$scripts->add( 'admin-widgets', "/wp-admin/js/widgets$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable' ), false, 1 );

		$scripts->add( 'theme', "/wp-admin/js/theme$suffix.js", array( 'jquery' ), false, 1 );

		// @todo: Core no longer uses theme-preview.js. Remove?
		$scripts->add( 'theme-preview', "/wp-admin/js/theme-preview$suffix.js", array( 'thickbox', 'jquery' ), false, 1 );

		$scripts->add( 'inline-edit-post', "/wp-admin/js/inline-edit-post$suffix.js", array( 'jquery', 'suggest' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'inline-edit-post', 'inlineEditL10n', array(
			'error' => __('Error while saving the changes.'),
			'ntdeltitle' => __('Remove From Bulk Edit'),
			'notitle' => __('(no title)'),
			'comma' => _x( ',', 'tag delimiter' ),
		) );

		$scripts->add( 'inline-edit-tax', "/wp-admin/js/inline-edit-tax$suffix.js", array( 'jquery' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'inline-edit-tax', 'inlineEditL10n', array(
			'error' => __('Error while saving the changes.')
		) );

		$scripts->add( 'plugin-install', "/wp-admin/js/plugin-install$suffix.js", array( 'jquery', 'thickbox' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'plugin-install', 'plugininstallL10n', array(
			'plugin_information' => __('Plugin Information:'),
			'ays' => __('Are you sure you want to install this plugin?')
		) );

		$scripts->add( 'farbtastic', '/wp-admin/js/farbtastic.js', array('jquery'), '1.2' );

		$scripts->add( 'iris', '/wp-admin/js/iris.min.js', array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
		$scripts->add( 'wp-color-picker', "/wp-admin/js/color-picker$suffix.js", array( 'iris' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'wp-color-picker', 'wpColorPickerL10n', array(
			'clear' => __( 'Clear' ),
			'defaultString' => __( 'Default' ),
			'pick' => __( 'Select Color' ),
			'current' => __( 'Current Color' ),
		) );

		$scripts->add( 'dashboard', "/wp-admin/js/dashboard$suffix.js", array( 'jquery', 'admin-comments', 'postbox' ), false, 1 );

		$scripts->add( 'list-revisions', "/wp-includes/js/wp-list-revisions$suffix.js" );

		$scripts->add( 'media', "/wp-admin/js/media$suffix.js", array( 'jquery-ui-draggable' ), false, 1 );

		$scripts->add( 'image-edit', "/wp-admin/js/image-edit$suffix.js", array('jquery', 'json2', 'imgareaselect'), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'image-edit', 'imageEditL10n', array(
			'error' => __( 'Could not load the preview image. Please reload the page and try again.' )
		));

		$scripts->add( 'set-post-thumbnail', "/wp-admin/js/set-post-thumbnail$suffix.js", array( 'jquery' ), false, 1 );
		did_action( 'init' ) && $scripts->localize( 'set-post-thumbnail', 'setPostThumbnailL10n', array(
			'setThumbnail' => __( 'Use as featured image' ),
			'saving' => __( 'Saving...' ),
			'error' => __( 'Could not set that as the thumbnail image. Try a different attachment.' ),
			'done' => __( 'Done' )
		) );

		// Navigation Menus
		$scripts->add( 'nav-menu', "/wp-admin/js/nav-menu$suffix.js", array( 'jquery-ui-sortable', 'jquery-ui-draggable', 'jquery-ui-droppable', 'wp-lists', 'postbox' ) );
		did_action( 'init' ) && $scripts->localize( 'nav-menu', 'navMenuL10n', array(
			'noResultsFound' => _x('No results found.', 'search results'),
			'warnDeleteMenu' => __( "You are about to permanently delete this menu. \n 'Cancel' to stop, 'OK' to delete." ),
			'saveAlert' => __('The changes you made will be lost if you navigate away from this page.')
		) );

		$scripts->add( 'custom-header', "/wp-admin/js/custom-header.js", array( 'jquery-masonry' ), false, 1 );
		$scripts->add( 'custom-background', "/wp-admin/js/custom-background$suffix.js", array( 'wp-color-picker', 'media-views' ), false, 1 );
		$scripts->add( 'media-gallery', "/wp-admin/js/media-gallery$suffix.js", array('jquery'), false, 1 );
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

	if ( ! $guessurl = site_url() )
		$guessurl = wp_guess_url();

	$styles->base_url = $guessurl;
	$styles->content_url = defined('WP_CONTENT_URL')? WP_CONTENT_URL : '';
	$styles->default_version = get_bloginfo( 'version' );
	$styles->text_direction = function_exists( 'is_rtl' ) && is_rtl() ? 'rtl' : 'ltr';
	$styles->default_dirs = array('/wp-admin/', '/wp-includes/css/');

	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

	$rtl_styles = array( 'wp-admin', 'ie', 'media', 'admin-bar', 'customize-controls', 'media-views', 'wp-color-picker' );
	// Any rtl stylesheets that don't have a .min version
	$no_suffix = array( 'farbtastic' );

	$styles->add( 'wp-admin', "/wp-admin/css/wp-admin$suffix.css" );

	$styles->add( 'ie', "/wp-admin/css/ie$suffix.css" );
	$styles->add_data( 'ie', 'conditional', 'lte IE 7' );

	// Register "meta" stylesheet for admin colors. All colors-* style sheets should have the same version string.
	$styles->add( 'colors', true, array('wp-admin', 'buttons') );

	// do not refer to these directly, the right one is queued by the above "meta" colors handle
	$styles->add( 'colors-fresh', "/wp-admin/css/colors-fresh$suffix.css", array('wp-admin', 'buttons') );
	$styles->add( 'colors-classic', "/wp-admin/css/colors-classic$suffix.css", array('wp-admin', 'buttons') );

	$styles->add( 'media', "/wp-admin/css/media$suffix.css" );
	$styles->add( 'install', "/wp-admin/css/install$suffix.css", array('buttons') );
	$styles->add( 'thickbox', '/wp-includes/js/thickbox/thickbox.css', array(), '20121105' );
	$styles->add( 'farbtastic', '/wp-admin/css/farbtastic.css', array(), '1.3u1' );
	$styles->add( 'wp-color-picker', "/wp-admin/css/color-picker$suffix.css" );
	$styles->add( 'jcrop', "/wp-includes/js/jcrop/jquery.Jcrop.min.css", array(), '0.9.10' );
	$styles->add( 'imgareaselect', '/wp-includes/js/imgareaselect/imgareaselect.css', array(), '0.9.8' );
	$styles->add( 'admin-bar', "/wp-includes/css/admin-bar$suffix.css" );
	$styles->add( 'wp-jquery-ui-dialog', "/wp-includes/css/jquery-ui-dialog$suffix.css" );
	$styles->add( 'editor-buttons', "/wp-includes/css/editor$suffix.css" );
	$styles->add( 'wp-pointer', "/wp-includes/css/wp-pointer$suffix.css" );
	$styles->add( 'customize-controls', "/wp-admin/css/customize-controls$suffix.css", array( 'wp-admin', 'colors', 'ie' ) );
	$styles->add( 'media-views', "/wp-includes/css/media-views$suffix.css", array( 'buttons' ) );
	$styles->add( 'buttons', "/wp-includes/css/buttons$suffix.css" );

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
 * @param array $js_array JavaScript scripts array
 * @return array Reordered array, if needed.
 */
function wp_prototype_before_jquery( $js_array ) {
	if ( false === $prototype = array_search( 'prototype', $js_array, true ) )
		return $js_array;

	if ( false === $jquery = array_search( 'jquery', $js_array, true ) )
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
		'savingText' => __('Saving Draft&#8230;'),
		'saveAlert' => __('The changes you made will be lost if you navigate away from this page.')
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
			$url = preg_replace( '/.min.css$|.min.css(?=\?)/', '.css', $url );

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
	$wp_scripts->do_concat = $concatenate_scripts;
	$wp_scripts->do_head_items();

	if ( apply_filters('print_head_scripts', true) )
		_print_scripts();

	$wp_scripts->reset();
	return $wp_scripts->done;
}

/**
 * Prints the scripts that were queued for the footer or too late for the HTML head.
 *
 * @since 2.8
 */
function print_footer_scripts() {
	global $wp_scripts, $concatenate_scripts;

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

/**
 * @internal use
 */
function _print_scripts() {
	global $wp_scripts, $compress_scripts;

	$zip = $compress_scripts ? 1 : 0;
	if ( $zip && defined('ENFORCE_GZIP') && ENFORCE_GZIP )
		$zip = 'gzip';

	if ( $concat = trim( $wp_scripts->concat, ', ' ) ) {

		if ( !empty($wp_scripts->print_code) ) {
			echo "\n<script type='text/javascript'>\n";
			echo "/* <![CDATA[ */\n"; // not needed in HTML 5
			echo $wp_scripts->print_code;
			echo "/* ]]> */\n";
			echo "</script>\n";
		}

		$concat = str_split( $concat, 128 );
		$concat = 'load%5B%5D=' . implode( '&load%5B%5D=', $concat );

		$src = $wp_scripts->base_url . "/wp-admin/load-scripts.php?c={$zip}&" . $concat . '&ver=' . $wp_scripts->default_version;
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
 * Private, for use in *_footer_scripts hooks
 *
 * @since 3.3.0
 */
function _wp_footer_scripts() {
	print_late_styles();
	print_footer_scripts();
}

/**
 * Hooks to print the scripts and styles in the footer.
 *
 * @since 2.8
 */
function wp_print_footer_scripts() {
	do_action('wp_print_footer_scripts');
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

/**
 * Prints the styles queue in the HTML head on admin pages.
 *
 * @since 2.8
 */
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

	if ( apply_filters('print_admin_styles', true) )
		_print_styles();

	$wp_styles->reset();
	return $wp_styles->done;
}

/**
 * Prints the styles that were queued too late for the HTML head.
 *
 * @since 3.3.0
 */
function print_late_styles() {
	global $wp_styles, $concatenate_scripts;

	if ( !is_a($wp_styles, 'WP_Styles') )
		return;

	$wp_styles->do_concat = $concatenate_scripts;
	$wp_styles->do_footer_items();

	if ( apply_filters('print_late_styles', true) )
		_print_styles();

	$wp_styles->reset();
	return $wp_styles->done;
}

/**
 * @internal use
 */
function _print_styles() {
	global $wp_styles, $compress_css;

	$zip = $compress_css ? 1 : 0;
	if ( $zip && defined('ENFORCE_GZIP') && ENFORCE_GZIP )
		$zip = 'gzip';

	if ( !empty($wp_styles->concat) ) {
		$dir = $wp_styles->text_direction;
		$ver = $wp_styles->default_version;
		$href = $wp_styles->base_url . "/wp-admin/load-styles.php?c={$zip}&dir={$dir}&load=" . trim($wp_styles->concat, ', ') . '&ver=' . $ver;
		echo "<link rel='stylesheet' href='" . esc_attr($href) . "' type='text/css' media='all' />\n";

		if ( !empty($wp_styles->print_code) ) {
			echo "<style type='text/css'>\n";
			echo $wp_styles->print_code;
			echo "\n</style>\n";
		}
	}

	if ( !empty($wp_styles->print_html) )
		echo $wp_styles->print_html;
}

/**
 * Determine the concatenation and compression settings for scripts and styles.
 *
 * @since 2.8
 */
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
