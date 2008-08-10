<?php

require( ABSPATH . WPINC . '/class.wp-dependencies.php' );
require( ABSPATH . WPINC . '/class.wp-scripts.php' );
require( ABSPATH . WPINC . '/functions.wp-scripts.php' );
require( ABSPATH . WPINC . '/class.wp-styles.php' );
require( ABSPATH . WPINC . '/functions.wp-styles.php' );

function wp_default_scripts( &$scripts ) {
	if (!$guessurl = site_url())
		$guessurl = wp_guess_url();
	$scripts->base_url = $guessurl;
	$scripts->default_version = get_bloginfo( 'version' );

	$scripts->add( 'common', '/wp-admin/js/common.js', array('jquery'), '20080318' );
	$scripts->add( 'sack', '/wp-includes/js/tw-sack.js', false, '1.6.1' );

	$scripts->add( 'quicktags', '/wp-includes/js/quicktags.js', false, '3958' );
	$scripts->localize( 'quicktags', 'quicktagsL10n', array(
		'quickLinks' => __('(Quick Links)'),
		'wordLookup' => __('Enter a word to look up:'),
		'dictionaryLookup' => attribute_escape(__('Dictionary lookup')),
		'lookup' => attribute_escape(__('lookup')),
		'closeAllOpenTags' => attribute_escape(__('Close all open tags')),
		'closeTags' => attribute_escape(__('close tags')),
		'enterURL' => __('Enter the URL'),
		'enterImageURL' => __('Enter the URL of the image'),
		'enterImageDescription' => __('Enter a description of the image')
	) );

	$scripts->add( 'colorpicker', '/wp-includes/js/colorpicker.js', array('prototype'), '3517' );

	// Let a plugin replace the visual editor
	$visual_editor = apply_filters('visual_editor', array('tiny_mce'));
	$scripts->add( 'editor', false, $visual_editor, '20080321' );

	$scripts->add( 'editor_functions', '/wp-admin/js/editor.js', false, '20080710' );

	// Modify this version when tinyMCE plugins are changed.
	$mce_version = apply_filters('tiny_mce_version', '20080810');
	$scripts->add( 'tiny_mce', '/wp-includes/js/tinymce/tiny_mce_config.php', array('editor_functions'), $mce_version );

	$scripts->add( 'prototype', '/wp-includes/js/prototype.js', false, '1.6');

	$scripts->add( 'wp-ajax-response', '/wp-includes/js/wp-ajax-response.js', array('jquery'), '20080316' );
	$scripts->localize( 'wp-ajax-response', 'wpAjax', array(
		'noPerm' => __('You do not have permission to do that.'),
		'broken' => __('An unidentified error has occurred.')
	) );

	$scripts->add( 'autosave', '/wp-includes/js/autosave.js', array('schedule', 'wp-ajax-response'), '20080622' );

	$scripts->add( 'wp-lists', '/wp-includes/js/wp-lists.js', array('wp-ajax-response'), '20080729' );
	$scripts->localize( 'wp-lists', 'wpListL10n', array(
		'url' => admin_url('admin-ajax.php')
	) );

	$scripts->add( 'scriptaculous-root', '/wp-includes/js/scriptaculous/scriptaculous.js', array('prototype'), '1.8.0');
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
	$scripts->add( 'thickbox', '/wp-includes/js/thickbox/thickbox.js', array('jquery'), '3.1-20080430');
	$scripts->add( 'swfupload', '/wp-includes/js/swfupload/swfupload.js', false, '2.0.2-20080430');
	$scripts->add( 'swfupload-degrade', '/wp-includes/js/swfupload/plugins/swfupload.graceful_degradation.js', array('swfupload'), '2.0.2');
	$scripts->localize( 'swfupload-degrade', 'uploadDegradeOptions', array(
		'is_lighttpd_before_150' => is_lighttpd_before_150(),
	) );
	$scripts->add( 'swfupload-queue', '/wp-includes/js/swfupload/plugins/swfupload.queue.js', array('swfupload'), '2.0.2');
	$scripts->add( 'swfupload-handlers', '/wp-includes/js/swfupload/handlers.js', array('swfupload'), '2.0.2-20080407');
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
	) );

	$scripts->add( 'jquery-ui-core', '/wp-includes/js/jquery/ui.core.js', array('jquery'), '1.5.1' );
	$scripts->add( 'jquery-ui-tabs', '/wp-includes/js/jquery/ui.tabs.js', array('jquery-ui-core'), '1.5.1' );
	$scripts->add( 'jquery-ui-sortable', '/wp-includes/js/jquery/ui.sortable.js', array('jquery-ui-core'), '1.5.1' );

	if ( is_admin() ) {
		$scripts->add( 'ajaxcat', '/wp-admin/js/cat.js', array( 'wp-lists' ), '20071101' );
		$scripts->localize( 'ajaxcat', 'catL10n', array(
			'add' => attribute_escape(__('Add')),
			'how' => __('Separate multiple categories with commas.')
		) );
		$scripts->add( 'admin-categories', '/wp-admin/js/categories.js', array('wp-lists'), '20071031' );
		$scripts->add( 'admin-tags', '/wp-admin/js/tags.js', array('wp-lists'), '20071031' );
		$scripts->add( 'admin-custom-fields', '/wp-admin/js/custom-fields.js', array('wp-lists'), '20070823' );
		$scripts->add( 'password-strength-meter', '/wp-admin/js/password-strength-meter.js', array('jquery'), '20070405' );
		$scripts->localize( 'password-strength-meter', 'pwsL10n', array(
			'short' => __('Too short'),
			'bad' => __('Bad'),
			'good' => __('Good'),
			'strong' => __('Strong')
		) );
		$scripts->add( 'admin-comments', '/wp-admin/js/edit-comments.js', array('wp-lists'), '20080311' );
		$scripts->localize( 'admin-comments', 'adminCommentsL10n', array(
			'pending' => __('%i% pending') // must look like: "# blah blah"
		) );
		$scripts->add( 'admin-users', '/wp-admin/js/users.js', array('wp-lists'), '20070823' );
		$scripts->add( 'admin-forms', '/wp-admin/js/forms.js', array('jquery'), '20080729');
		$scripts->add( 'xfn', '/wp-admin/js/xfn.js', false, '3517' );
		$scripts->add( 'upload', '/wp-admin/js/upload.js', array('jquery'), '20070518' );
		$scripts->add( 'postbox', '/wp-admin/js/postbox.js', array('jquery'), '20080128' );
		$scripts->localize( 'postbox', 'postboxL10n', array(
			'requestFile' => admin_url('admin-ajax.php'),
		) );
		$scripts->add( 'slug', '/wp-admin/js/slug.js', array('jquery'), '20080208' );
		$scripts->localize( 'slug', 'slugL10n', array(
			'requestFile' => admin_url('admin-ajax.php'),
			'save' => __('Save'),
			'cancel' => __('Cancel'),
		) );
		$scripts->add( 'post', '/wp-admin/js/post.js', array('suggest', 'jquery-ui-tabs', 'wp-lists', 'postbox', 'slug'), '20080629' );
		$scripts->localize( 'post', 'postL10n', array(
			'tagsUsed' =>  __('Tags used on this post:'),
			'add' => attribute_escape(__('Add')),
			'addTag' => attribute_escape(__('Add new tag')),
			'separate' => __('Separate tags with commas'),
			'cancel' => __('Cancel'),
			'edit' => __('Edit'),
		) );
		$scripts->add( 'page', '/wp-admin/js/page.js', array('jquery', 'slug', 'postbox'), '20080318' );
		$scripts->localize( 'page', 'postL10n', array(
			'cancel' => __('Cancel'),
			'edit' => __('Edit'),
		) );
		$scripts->add( 'link', '/wp-admin/js/link.js', array('jquery-ui-tabs', 'wp-lists', 'postbox'), '20080131' );
		$scripts->add( 'comment', '/wp-admin/js/comment.js', array('postbox'), '20080219' );
		$scripts->localize( 'comment', 'commentL10n', array(
			'cancel' => __('Cancel'),
			'edit' => __('Edit'),
		) );
		$scripts->add( 'admin-gallery', '/wp-admin/js/gallery.js', array( 'jquery-ui-sortable' ), '20080709' );
		$scripts->add( 'media-upload', '/wp-admin/js/media-upload.js', array( 'thickbox' ), '20080710' );
		$scripts->localize( 'upload', 'uploadL10n', array(
			'browseTitle' => attribute_escape(__('Browse your files')),
			'back' => __('&laquo; Back'),
			'directTitle' => attribute_escape(__('Direct link to file')),
			'edit' => __('Edit'),
			'thumb' => __('Thumbnail'),
			'full' => __('Full size'),
			'icon' => __('Icon'),
			'title' => __('Title'),
			'show' => __('Show:'),
			'link' => __('Link to:'),
			'file' => __('File'),
			'page' => __('Page'),
			'none' => __('None'),
			'editorText' => attribute_escape(__('Send to editor &raquo;')),
			'insert' => __('Insert'),
			'urlText' => __('URL'),
			'desc' => __('Description'),
			'deleteText' => attribute_escape(__('Delete File')),
			'saveText' => attribute_escape(__('Save &raquo;')),
			'confirmText' => __("Are you sure you want to delete the file '%title%'?\nClick ok to delete or cancel to go back.")
		) );
		$scripts->add( 'admin-widgets', '/wp-admin/js/widgets.js', array( 'interface' ), '20080503' );
		$scripts->localize( 'admin-widgets', 'widgetsL10n', array(
			'add' => __('Add'),
			'edit' => __('Edit'),
			'cancel' => __('Cancel'),
		));

		$scripts->add( 'word-count', '/wp-admin/js/word-count.js', array( 'jquery' ), '20080423' );
		$scripts->localize( 'word-count', 'wordCountL10n', array(
			'count' => __('Word count: %d')
		));
		
		$scripts->add( 'wp-gears', '/wp-admin/js/wp-gears.js', false, '20080721' );
		$scripts->localize( 'wp-gears', 'wpGearsL10n', array(
			'updateCompleted' => __('Update completed.'),
			'error' => __('Error:')
		));
		
		$scripts->add( 'theme-preview', '/wp-admin/js/theme-preview.js', array( 'thickbox', 'jquery' ), '20080625' );
	}
}

function wp_default_styles( &$styles ) {
	if (!$guessurl = site_url())
		$guessurl = wp_guess_url();
	$styles->base_url = $guessurl;
	$styles->default_version = get_bloginfo( 'version' );
	$styles->text_direction = 'rtl' == get_bloginfo( 'text_direction' ) ? 'rtl' : 'ltr';

	$rtl_styles = array( 'global', 'colors', 'dashboard', 'ie', 'install', 'login', 'media', 'theme-editor', 'upload', 'widgets', 'press-this', 'press-this-ie' );

	$styles->add( 'wp-admin', '/wp-admin/wp-admin.css' );
	$styles->add_data( 'wp-admin', 'rtl', '/wp-admin/rtl.css' );

	$styles->add( 'ie', '/wp-admin/css/ie.css' );
	$styles->add_data( 'ie', 'conditional', 'gte IE 6' );

	$styles->add( 'colors', true ); // Register "meta" stylesheet for admin colors
	$styles->add( 'colors-fresh', '/wp-admin/css/colors-fresh.css' ); // for login.php.  Is there a better way?
	$styles->add_data( 'colors-fresh', 'rtl', true );

	$styles->add( 'global', '/wp-admin/css/global.css' );
	$styles->add( 'media', '/wp-admin/css/media.css', array(), '20080709' );
	$styles->add( 'widgets', '/wp-admin/css/widgets.css' );
	$styles->add( 'dashboard', '/wp-admin/css/dashboard.css' );
	$styles->add( 'install', '/wp-admin/css/install.css', array(), '20080708' );
	$styles->add( 'theme-editor', '/wp-admin/css/theme-editor.css' );
	$styles->add( 'press-this', '/wp-admin/css/press-this.css', array(), '20080710' );
	$styles->add( 'press-this-ie', '/wp-admin/css/press-this-ie.css', array(), '20080710' );
	$styles->add_data( 'press-this-ie', 'conditional', 'gte IE 6' );
	$styles->add( 'thickbox', '/wp-includes/js/thickbox/thickbox.css', array(), '20080613' );
	$styles->add( 'login', '/wp-admin/css/login.css' );

	foreach ( $rtl_styles as $rtl_style )
		$styles->add_data( $rtl_style, 'rtl', true );
}

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

// These localizations require information that may not be loaded even by init
function wp_just_in_time_script_localization() {
	wp_localize_script( 'tiny_mce', 'wpTinyMCEConfig', array( 'defaultEditor' => wp_default_editor() ) );
	wp_localize_script( 'autosave', 'autosaveL10n', array(
		'autosaveInterval' => AUTOSAVE_INTERVAL,
		'previewPageText' => __('Preview this Page'),
		'previewPostText' => __('Preview this Post'),
		'requestFile' => admin_url('admin-ajax.php'),
		'savingText' => __('Saving Draft&#8230;')
	) );
}

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
