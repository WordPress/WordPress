<?php
/**
 * WordPress scripts and styles default loader.
 *
 * Most of the functionality that existed here was moved to
 * {@link http://backpress.automattic.com/ BackPress}. WordPress themes and
 * plugins will only be concerned about the filters and actions set in this
 * file.
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
 * Setup WordPress scripts to load by default for Administration Panels.
 *
 * Localizes a few of the scripts.
 *
 * @since 2.6.0
 *
 * @param object $scripts WP_Scripts object.
 */
function wp_default_scripts( &$scripts ) {
	if (!$guessurl = site_url())
		$guessurl = wp_guess_url();

	$scripts->base_url = $guessurl;
	$scripts->default_version = get_bloginfo( 'version' );

	$scripts->add( 'common', '/wp-admin/js/common.js', array('jquery', 'hoverIntent'), '20081210' );
	$scripts->add( 'sack', '/wp-includes/js/tw-sack.js', false, '1.6.1' );

	$scripts->add( 'quicktags', '/wp-includes/js/quicktags.js', false, '20081210' );
	$scripts->localize( 'quicktags', 'quicktagsL10n', array(
		'quickLinks' => __('(Quick Links)'),
		'wordLookup' => __('Enter a word to look up:'),
		'dictionaryLookup' => attribute_escape(__('Dictionary lookup')),
		'lookup' => attribute_escape(__('lookup')),
		'closeAllOpenTags' => attribute_escape(__('Close all open tags')),
		'closeTags' => attribute_escape(__('close tags')),
		'enterURL' => __('Enter the URL'),
		'enterImageURL' => __('Enter the URL of the image'),
		'enterImageDescription' => __('Enter a description of the image'),
		'l10n_print_after' => 'try{convertEntities(quicktagsL10n);}catch(e){};'
	) );

	$scripts->add( 'colorpicker', '/wp-includes/js/colorpicker.js', array('prototype'), '3517' );

	// Modify this version when tinyMCE plugins are changed.
	function mce_version() {
		return '20081129';
	}
	add_filter( 'tiny_mce_version', 'mce_version' );

	$scripts->add( 'editor', '/wp-admin/js/editor.js', false, mce_version() );

	$scripts->add( 'prototype', '/wp-includes/js/prototype.js', false, '1.6');

	$scripts->add( 'wp-ajax-response', '/wp-includes/js/wp-ajax-response.js', array('jquery'), '20081210' );
	$scripts->localize( 'wp-ajax-response', 'wpAjax', array(
		'noPerm' => __('You do not have permission to do that.'),
		'broken' => __('An unidentified error has occurred.'),
		'l10n_print_after' => 'try{convertEntities(wpAjax);}catch(e){};'
	) );

	$scripts->add( 'autosave', '/wp-includes/js/autosave.js', array('schedule', 'wp-ajax-response'), '20081210' );

	$scripts->add( 'wp-lists', '/wp-includes/js/wp-lists.js', array('wp-ajax-response'), '20081210' );
	$scripts->localize( 'wp-lists', 'wpListL10n', array(
		'url' => admin_url('admin-ajax.php')
	) );

	$scripts->add( 'scriptaculous-root', '/wp-includes/js/scriptaculous/wp-scriptaculous.js', array('prototype'), '1.8.0');
	$scripts->add( 'scriptaculous-builder', '/wp-includes/js/scriptaculous/builder.js', array('scriptaculous-root'), '1.8.0');
	$scripts->add( 'scriptaculous-dragdrop', '/wp-includes/js/scriptaculous/dragdrop.js', array('scriptaculous-builder', 'scriptaculous-effects'), '1.8.0');
	$scripts->add( 'scriptaculous-effects', '/wp-includes/js/scriptaculous/effects.js', array('scriptaculous-root'), '1.8.0');
	$scripts->add( 'scriptaculous-slider', '/wp-includes/js/scriptaculous/slider.js', array('scriptaculous-effects'), '1.8.0');
	$scripts->add( 'scriptaculous-sound', '/wp-includes/js/scriptaculous/sound.js', array( 'scriptaculous-root' ), '1.8.0' );
	$scripts->add( 'scriptaculous-controls', '/wp-includes/js/scriptaculous/controls.js', array('scriptaculous-root'), '1.8.0');
	$scripts->add( 'scriptaculous', '', array('scriptaculous-dragdrop', 'scriptaculous-slider', 'scriptaculous-controls'), '1.8.0');

	$scripts->add( 'cropper', '/wp-includes/js/crop/cropper.js', array('scriptaculous-dragdrop'), '20070118');

	$scripts->add( 'jquery', '/wp-includes/js/jquery/jquery.js', false, '1.2.6');
	$scripts->add( 'jquery-form', '/wp-includes/js/jquery/jquery.form.js', array('jquery'), '2.02');
	$scripts->add( 'jquery-color', '/wp-includes/js/jquery/jquery.color.js', array('jquery'), '2.0-4561');
	$scripts->add( 'interface', '/wp-includes/js/jquery/interface.js', array('jquery'), '1.2' );
	$scripts->add( 'suggest', '/wp-includes/js/jquery/suggest.js', array('jquery'), '1.1b');
	$scripts->add( 'schedule', '/wp-includes/js/jquery/jquery.schedule.js', array('jquery'), '20');
	$scripts->add( 'jquery-hotkeys', '/wp-includes/js/jquery/jquery.hotkeys.js', array('jquery'), '0.0.2' );
	$scripts->add( 'jquery-table-hotkeys', '/wp-includes/js/jquery/jquery.table-hotkeys.js', array('jquery', 'jquery-hotkeys'), '20081128' );
	$scripts->add( 'thickbox', '/wp-includes/js/thickbox/thickbox.js', array('jquery'), '3.1-20080430');
	$scripts->add( 'swfupload', '/wp-includes/js/swfupload/swfupload.js', false, '2.2.0-20081031');
	$scripts->add( 'swfupload-degrade', '/wp-includes/js/swfupload/plugins/swfupload.graceful_degradation.js', array('swfupload'), '2.2.0-20081031');
	$scripts->add( 'swfupload-swfobject', '/wp-includes/js/swfupload/plugins/swfupload.swfobject.js', array('swfupload'), '2.2.0-20081031');
	$scripts->localize( 'swfupload-degrade', 'uploadDegradeOptions', array(
		'is_lighttpd_before_150' => is_lighttpd_before_150(),
	) );
	$scripts->add( 'swfupload-queue', '/wp-includes/js/swfupload/plugins/swfupload.queue.js', array('swfupload'), '2.2.0-20081031');
	$scripts->add( 'swfupload-handlers', '/wp-includes/js/swfupload/handlers.js', array('swfupload'), '2.2.0-20081201');
	// these error messages came from the sample swfupload js, they might need changing.
	$scripts->localize( 'swfupload-handlers', 'swfuploadL10n', array(
			'queue_limit_exceeded' => __('You have attempted to queue too many files.'),
			'file_exceeds_size_limit' => sprintf(__('This file is too big. Your php.ini upload_max_filesize is %s.'), @ini_get('upload_max_filesize')),
			'zero_byte_file' => __('This file is empty. Please try another.'),
			'invalid_filetype' => __('This file type is not allowed. Please try another.'),
			'default_error' => __('An error occurred in the upload. Please try again later.'),
			'missing_upload_url' => __('There was a configuration error. Please contact the server administrator.'),
			'upload_limit_exceeded' => __('You may only upload 1 file.'),
			'http_error' => __('HTTP error.'),
			'upload_failed' => __('Upload failed.'),
			'io_error' => __('IO error.'),
			'security_error' => __('Security error.'),
			'file_cancelled' => __('File cancelled.'),
			'upload_stopped' => __('Upload stopped.'),
			'dismiss' => __('Dismiss'),
			'crunching' => __('Crunching&hellip;'),
			'deleted' => __('Deleted'),
			'l10n_print_after' => 'try{convertEntities(swfuploadL10n);}catch(e){};'
	) );

	$scripts->add( 'jquery-ui-core', '/wp-includes/js/jquery/ui.core.js', array('jquery'), '1.5.2' );
	$scripts->add( 'jquery-ui-tabs', '/wp-includes/js/jquery/ui.tabs.js', array('jquery-ui-core'), '1.5.2' );
	$scripts->add( 'jquery-ui-sortable', '/wp-includes/js/jquery/ui.sortable.js', array('jquery-ui-core'), '1.5.2c' );
	$scripts->add( 'jquery-ui-draggable', '/wp-includes/js/jquery/ui.draggable.js', array('jquery-ui-core'), '1.5.2' );
	$scripts->add( 'jquery-ui-resizable', '/wp-includes/js/jquery/ui.resizable.js', array('jquery-ui-core'), '1.5.2' );
	$scripts->add( 'jquery-ui-dialog', '/wp-includes/js/jquery/ui.dialog.js', array('jquery-ui-resizable', 'jquery-ui-draggable'), '1.5.2' );

	$scripts->add( 'comment-reply', '/wp-includes/js/comment-reply.js', false, '20081210');

	if ( is_admin() ) {
		$scripts->add( 'ajaxcat', '/wp-admin/js/cat.js', array( 'wp-lists' ), '20081210' );
		$scripts->localize( 'ajaxcat', 'catL10n', array(
			'add' => attribute_escape(__('Add')),
			'how' => __('Separate multiple categories with commas.'),
			'l10n_print_after' => 'try{convertEntities(catL10n);}catch(e){};'
		) );
		$scripts->add( 'admin-categories', '/wp-admin/js/categories.js', array('wp-lists'), '20081210' );
		$scripts->add( 'admin-tags', '/wp-admin/js/tags.js', array('wp-lists'), '20081210' );
		$scripts->add( 'admin-custom-fields', '/wp-admin/js/custom-fields.js', array('wp-lists'), '20081210' );
		$scripts->add( 'password-strength-meter', '/wp-admin/js/password-strength-meter.js', array('jquery'), '20081210' );
		$scripts->localize( 'password-strength-meter', 'pwsL10n', array(
			'empty' => __('Strength indicator'),
			'short' => __('Very weak'),
			'bad' => __('Weak'),
			'good' => _c('Medium|password strength'),
			'strong' => __('Strong'),
			'l10n_print_after' => 'try{convertEntities(pwsL10n);}catch(e){};'
		) );
		$scripts->add( 'admin-comments', '/wp-admin/js/edit-comments.js', array('wp-lists', 'jquery-ui-resizable', 'quicktags'), '20081210' );
		$scripts->localize( 'admin-comments', 'adminCommentsL10n', array(
			'hotkeys_highlight_first' => isset($_GET['hotkeys_highlight_first']),
			'hotkeys_highlight_last' => isset($_GET['hotkeys_highlight_last'])
		) );
		$scripts->add( 'admin-users', '/wp-admin/js/users.js', array('wp-lists'), '20081210' );
		$scripts->add( 'xfn', '/wp-admin/js/xfn.js', false, '3517' );
		$scripts->add( 'postbox', '/wp-admin/js/postbox.js', array('jquery-ui-sortable'), '20081210' );
		$scripts->localize( 'postbox', 'postboxL10n', array(
			'requestFile' => admin_url('admin-ajax.php')
		) );
		$scripts->add( 'slug', '/wp-admin/js/slug.js', array('jquery'), '20081210' );
		$scripts->localize( 'slug', 'slugL10n', array(
			'requestFile' => admin_url('admin-ajax.php'),
			'save' => __('Save'),
			'cancel' => __('Cancel'),
			'l10n_print_after' => 'try{convertEntities(slugL10n);}catch(e){};'
		) );
		$scripts->add( 'post', '/wp-admin/js/post.js', array('suggest', 'jquery-ui-tabs', 'wp-lists', 'postbox', 'slug'), '20081210' );
		$scripts->localize( 'post', 'postL10n', array(
			'tagsUsed' =>  __('Tags used on this post:'),
			'add' => attribute_escape(__('Add')),
			'addTag' => attribute_escape(__('Add new tag')),
			'separate' => __('Separate tags with commas'),
			'cancel' => __('Cancel'),
			'edit' => __('Edit'),
			'publishOn' => __('Publish on:'),
			'publishOnFuture' =>  __('Schedule for:'),
			'publishOnPast' => __('Published on:'),
			'showcomm' => __('Show more comments'),
			'endcomm' => __('No more comments found.'),
			'publish' => __('Publish'),
			'schedule' => __('Schedule'),
			'update' => __('Update Post'),
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
		$scripts->add( 'page', '/wp-admin/js/page.js', array('jquery', 'slug', 'wp-lists', 'postbox'), '20081210' );
		$scripts->localize( 'page', 'postL10n', array(
			'cancel' => __('Cancel'),
			'edit' => __('Edit'),
			'publishOn' => __('Publish on:'),
			'publishOnFuture' =>  __('Schedule for:'),
			'publishOnPast' => __('Published on:'),
			'showcomm' => __('Show more comments'),
			'endcomm' => __('No more comments found.'),
			'publish' => __('Publish'),
			'schedule' => __('Schedule'),
			'update' => __('Update Page'),
			'savePending' => __('Save as Pending'),
			'saveDraft' => __('Save Draft'),
			'private' => __('Private'),
			'public' => __('Public'),
			'password' => __('Password Protected'),
			'privatelyPublished' => __('Privately Published'),
			'published' => __('Published'),
			'l10n_print_after' => 'try{convertEntities(postL10n);}catch(e){};'
		) );
		$scripts->add( 'link', '/wp-admin/js/link.js', array('jquery-ui-tabs', 'wp-lists', 'postbox'), '20081210' );
		$scripts->add( 'comment', '/wp-admin/js/comment.js', array('jquery'), '20081210' );
		$scripts->localize( 'comment', 'commentL10n', array(
			'cancel' => __('Cancel'),
			'edit' => __('Edit'),
			'submittedOn' => __('Submitted on:'),
			'l10n_print_after' => 'try{convertEntities(commentL10n);}catch(e){};'
		) );
		$scripts->add( 'admin-gallery', '/wp-admin/js/gallery.js', array( 'jquery-ui-sortable' ), '20081210' );
		$scripts->add( 'media-upload', '/wp-admin/js/media-upload.js', array( 'thickbox' ), '20081210' );

		$scripts->add( 'admin-widgets', '/wp-admin/js/widgets.js', array( 'interface' ), '20081210' );
		$scripts->localize( 'admin-widgets', 'widgetsL10n', array(
			'add' => __('Add'),
			'edit' => __('Edit'),
			'cancel' => __('Cancel'),
			'lameReminder' => __('Remember to click the "Save Changes" button at the bottom of the Current Widgets column after you\'re all done!'),
			'lamerReminder' => __("You're about to leave without having saved your changes!"),
			'l10n_print_after' => 'try{convertEntities(widgetsL10n);}catch(e){};'
		));

		$scripts->add( 'word-count', '/wp-admin/js/word-count.js', array( 'jquery' ), '20081210' );
		$scripts->localize( 'word-count', 'wordCountL10n', array(
			'count' => __('Word count: %d'),
			'l10n_print_after' => 'try{convertEntities(wordCountL10n);}catch(e){};'
		));

		$scripts->add( 'wp-gears', '/wp-admin/js/wp-gears.js', false, '20081210' );
		$scripts->localize( 'wp-gears', 'wpGearsL10n', array(
			'updateCompleted' => __('Update completed.'),
			'error' => __('Error:'),
			'l10n_print_after' => 'try{convertEntities(wpGearsL10n);}catch(e){};'
		));

		$scripts->add( 'theme-preview', '/wp-admin/js/theme-preview.js', array( 'thickbox', 'jquery' ), '20081210' );

		$scripts->add( 'inline-edit-post', '/wp-admin/js/inline-edit-post.js', array( 'jquery', 'jquery-form', 'suggest' ), '20081210' );
		$scripts->localize( 'inline-edit-post', 'inlineEditL10n', array(
			'error' => __('Error while saving the changes.'),
			'ntdeltitle' => __('Remove From Bulk Edit'),
			'notitle' => __('(no title)'),
			'l10n_print_after' => 'try{convertEntities(inlineEditL10n);}catch(e){};'
		) );

		$scripts->add( 'inline-edit-tax', '/wp-admin/js/inline-edit-tax.js', array( 'jquery', 'jquery-form' ), '20081210' );
		$scripts->localize( 'inline-edit-tax', 'inlineEditL10n', array(
			'error' => __('Error while saving the changes.'),
			'l10n_print_after' => 'try{convertEntities(inlineEditL10n);}catch(e){};'
		) );

		$scripts->add( 'plugin-install', '/wp-admin/js/plugin-install.js', array( 'thickbox', 'jquery' ), '20081210' );
		$scripts->localize( 'plugin-install', 'plugininstallL10n', array(
			'plugin_information' => __('Plugin Information:'),
			'l10n_print_after' => 'try{convertEntities(plugininstallL10n);}catch(e){};'
		) );

		$scripts->add( 'farbtastic', '/wp-admin/js/farbtastic.js', array('jquery'), '1.2' );

		$scripts->add( 'dashboard', '/wp-admin/js/dashboard.js', array( 'jquery', 'admin-comments', 'postbox' ), '20081210' );

		$scripts->add( 'hoverIntent', '/wp-includes/js/hoverIntent.js', array('jquery'), '20081210' );

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
	$styles->default_version = get_bloginfo( 'version' );
	$styles->text_direction = 'rtl' == get_bloginfo( 'text_direction' ) ? 'rtl' : 'ltr';

	$rtl_styles = array( 'global', 'colors', 'dashboard', 'ie', 'install', 'login', 'media', 'theme-editor', 'upload', 'widgets', 'press-this', 'plugin-install', 'farbtastic' );

	$styles->add( 'wp-admin', '/wp-admin/wp-admin.css', array(), '20081210' );
	$styles->add_data( 'wp-admin', 'rtl', '/wp-admin/rtl.css' );

	$styles->add( 'ie', '/wp-admin/css/ie.css', array(), '20081210' );
	$styles->add_data( 'ie', 'conditional', 'gte IE 6' );

	$styles->add( 'colors', true, array(), '20081210' ); // Register "meta" stylesheet for admin colors
	$styles->add( 'colors-fresh', '/wp-admin/css/colors-fresh.css', array(), '20081210'); // for login.php.  Is there a better way?
	$styles->add_data( 'colors-fresh', 'rtl', true );
	$styles->add( 'colors-classic', '/wp-admin/css/colors-classic.css', array(), '20081210');
	$styles->add_data( 'colors-classic', 'rtl', true );

	$styles->add( 'global', '/wp-admin/css/global.css', array(), '20081210' );
	$styles->add( 'media', '/wp-admin/css/media.css', array(), '20081210' );
	$styles->add( 'widgets', '/wp-admin/css/widgets.css', array(), '20081210' );
	$styles->add( 'dashboard', '/wp-admin/css/dashboard.css', array(), '20081210' );
	$styles->add( 'install', '/wp-admin/css/install.css', array(), '20081210' );
	$styles->add( 'theme-editor', '/wp-admin/css/theme-editor.css', array(), '20081210' );
	$styles->add( 'press-this', '/wp-admin/css/press-this.css', array(), '20081210' );
	$styles->add( 'thickbox', '/wp-includes/js/thickbox/thickbox.css', array(), '20081210' );
	$styles->add( 'login', '/wp-admin/css/login.css', array(), '20081210' );
	$styles->add( 'plugin-install', '/wp-admin/css/plugin-install.css', array(), '20081210' );
	$styles->add( 'farbtastic', '/wp-admin/css/farbtastic.css', array(), '1.2' );

	foreach ( $rtl_styles as $rtl_style )
		$styles->add_data( $rtl_style, 'rtl', true );
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
	if ( false === $jquery = array_search( 'jquery', $js_array ) )
		return $js_array;

	if ( false === $prototype = array_search( 'prototype', $js_array ) )
		return $js_array;

	if ( $prototype < $jquery )
		return $js_array;

	unset($js_array[$prototype]);

	array_splice( $js_array, $jquery, 0, 'prototype' );

	return $js_array;
}

/**
 * Load localized script just in time for MCE.
 *
 * These localizations require information that may not be loaded even by init.
 *
 * @since 2.5.0
 */
function wp_just_in_time_script_localization() {
	global $current_user;

	wp_localize_script( 'autosave', 'autosaveL10n', array(
		'autosaveInterval' => AUTOSAVE_INTERVAL,
		'previewPageText' => __('Preview this Page'),
		'previewPostText' => __('Preview this Post'),
		'requestFile' => admin_url('admin-ajax.php'),
		'savingText' => __('Saving Draft&#8230;'),
		'l10n_print_after' => 'try{convertEntities(autosaveL10n);}catch(e){};'
	) );

	$userid = isset($current_user) ? $current_user->ID : 0;
	wp_localize_script( 'common', 'userSettings', array(
		'url' => SITECOOKIEPATH,
		'uid' => $userid,
		'time' => time()
	) );
}

/**
 * Administration Panel CSS for changing the styles.
 *
 * If installing the 'wp-admin/' directory will be replaced with './'.
 *
 * The $_wp_admin_css_colors global manages the Administration Panels CSS
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
 * @return string URL path to CSS stylesheet for Administration Panels.
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
		if ( isset($parsed['query']) && $parsed['query'] ) {
			wp_parse_str( $parsed['query'], $qv );
			$url = add_query_arg( $qv, $url );
		}
		return $url;
	}

	return $src;
}

add_action( 'wp_default_scripts', 'wp_default_scripts' );
add_filter( 'wp_print_scripts', 'wp_just_in_time_script_localization' );
add_filter( 'print_scripts_array', 'wp_prototype_before_jquery' );

add_action( 'wp_default_styles', 'wp_default_styles' );
add_filter( 'style_loader_src', 'wp_style_loader_src', 10, 2 );
