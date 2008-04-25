<?php
class WP_Scripts {
	var $scripts = array();
	var $queue = array();
	var $to_print = array();
	var $printed = array();
	var $args = array();

	function WP_Scripts() {
		$this->default_scripts();
	}

	function default_scripts() {
		$this->add( 'common', '/wp-admin/js/common.js', array('jquery'), '20080318' );
		$this->add( 'sack', '/wp-includes/js/tw-sack.js', false, '1.6.1' );

		$this->add( 'quicktags', '/wp-includes/js/quicktags.js', false, '3958' );
		$this->localize( 'quicktags', 'quicktagsL10n', array(
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

		$this->add( 'colorpicker', '/wp-includes/js/colorpicker.js', array('prototype'), '3517' );

		// Let a plugin replace the visual editor
		$visual_editor = apply_filters('visual_editor', array('tiny_mce'));
		$this->add( 'editor', false, $visual_editor, '20080321' );

		$this->add( 'editor_functions', '/wp-admin/js/editor.js', false, '20080325' );

		// Modify this version when tinyMCE plugins are changed.
		$mce_version = apply_filters('tiny_mce_version', '20080414');
		$this->add( 'tiny_mce', '/wp-includes/js/tinymce/tiny_mce_config.php', array('editor_functions'), $mce_version );

		$this->add( 'prototype', '/wp-includes/js/prototype.js', false, '1.6');

		$this->add( 'wp-ajax-response', '/wp-includes/js/wp-ajax-response.js', array('jquery'), '20080316' );
		$this->localize( 'wp-ajax-response', 'wpAjax', array(
			'noPerm' => __('You do not have permission to do that.'),
			'broken' => __('An unidentified error has occurred.')
		) );

		$this->add( 'autosave', '/wp-includes/js/autosave.js', array('schedule', 'wp-ajax-response'), '20080424' );

		$this->add( 'wp-ajax', '/wp-includes/js/wp-ajax.js', array('prototype'), '20070306');
		$this->localize( 'wp-ajax', 'WPAjaxL10n', array(
			'defaultUrl' => get_option( 'siteurl' ) . '/wp-admin/admin-ajax.php',
			'permText' => __("You do not have permission to do that."),
			'strangeText' => __("Something strange happened.  Try refreshing the page."),
			'whoaText' => __("Slow down, I'm still sending your data!")
		) );

		$this->add( 'wp-lists', '/wp-includes/js/wp-lists.js', array('wp-ajax-response'), '20080411' );
		$this->localize( 'wp-lists', 'wpListL10n', array(
			'url' => get_option( 'siteurl' ) . '/wp-admin/admin-ajax.php'
		) );

		$this->add( 'scriptaculous-root', '/wp-includes/js/scriptaculous/scriptaculous.js', array('prototype'), '1.8.0');
		$this->add( 'scriptaculous-builder', '/wp-includes/js/scriptaculous/builder.js', array('scriptaculous-root'), '1.8.0');
		$this->add( 'scriptaculous-dragdrop', '/wp-includes/js/scriptaculous/dragdrop.js', array('scriptaculous-builder', 'scriptaculous-effects'), '1.8.0');
		$this->add( 'scriptaculous-effects', '/wp-includes/js/scriptaculous/effects.js', array('scriptaculous-root'), '1.8.0');
		$this->add( 'scriptaculous-slider', '/wp-includes/js/scriptaculous/slider.js', array('scriptaculous-effects'), '1.8.0');
		$this->add( 'scriptaculous-sound', '/wp-includes/js/scriptaculous/sound.js', array( 'scriptaculous-root' ), '1.8.0' );
		$this->add( 'scriptaculous-controls', '/wp-includes/js/scriptaculous/controls.js', array('scriptaculous-root'), '1.8.0');
		$this->add( 'scriptaculous', '', array('scriptaculous-dragdrop', 'scriptaculous-slider', 'scriptaculous-controls'), '1.8.0');

		$this->add( 'cropper', '/wp-includes/js/crop/cropper.js', array('scriptaculous-dragdrop'), '20070118');

		$this->add( 'jquery', '/wp-includes/js/jquery/jquery.js', false, '1.2.3');
		$this->add( 'jquery-form', '/wp-includes/js/jquery/jquery.form.js', array('jquery'), '2.02');
		$this->add( 'jquery-color', '/wp-includes/js/jquery/jquery.color.js', array('jquery'), '2.0-4561');
		$this->add( 'interface', '/wp-includes/js/jquery/interface.js', array('jquery'), '1.2' );
		$this->add( 'dimensions', '/wp-includes/js/jquery/jquery.dimensions.min.js', array('jquery'), '1.1.2');
		$this->add( 'suggest', '/wp-includes/js/jquery/suggest.js', array('dimensions'), '1.1');
		$this->add( 'schedule', '/wp-includes/js/jquery/jquery.schedule.js', array('jquery'), '20');
		$this->add( 'thickbox', '/wp-includes/js/thickbox/thickbox.js', array('jquery'), '3.1');
		$this->add( 'swfupload', '/wp-includes/js/swfupload/swfupload.js', false, '2.0.2');
		$this->add( 'swfupload-degrade', '/wp-includes/js/swfupload/plugins/swfupload.graceful_degradation.js', array('swfupload'), '2.0.2');
		$this->localize( 'swfupload-degrade', 'uploadDegradeOptions', array(
			'is_lighttpd_before_150' => is_lighttpd_before_150(),
		) );
		$this->add( 'swfupload-queue', '/wp-includes/js/swfupload/plugins/swfupload.queue.js', array('swfupload'), '2.0.2');
		$this->add( 'swfupload-handlers', '/wp-includes/js/swfupload/handlers.js', array('swfupload'), '2.0.2-20080407');
		// these error messages came from the sample swfupload js, they might need changing.
		$this->localize( 'swfupload-handlers', 'swfuploadL10n', array(
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

		$this->add( 'jquery-ui-tabs', '/wp-includes/js/jquery/ui.tabs.js', array('jquery'), '3' );

		if ( is_admin() ) {
			$this->add( 'ajaxcat', '/wp-admin/js/cat.js', array( 'wp-lists' ), '20071101' );
			$this->localize( 'ajaxcat', 'catL10n', array(
				'add' => attribute_escape(__('Add')),
				'how' => __('Separate multiple categories with commas.')
			) );
			$this->add( 'admin-categories', '/wp-admin/js/categories.js', array('wp-lists'), '20071031' );
			$this->add( 'admin-tags', '/wp-admin/js/tags.js', array('wp-lists'), '20071031' );
			$this->add( 'admin-custom-fields', '/wp-admin/js/custom-fields.js', array('wp-lists'), '20070823' );
			$this->add( 'password-strength-meter', '/wp-admin/js/password-strength-meter.js', array('jquery'), '20070405' );
			$this->localize( 'password-strength-meter', 'pwsL10n', array(
				'short' => __('Too short'),
				'bad' => __('Bad'),
				'good' => __('Good'),
				'strong' => __('Strong')
			) );
			$this->add( 'admin-comments', '/wp-admin/js/edit-comments.js', array('wp-lists'), '20080311' );
			$this->localize( 'admin-comments', 'adminCommentsL10n', array(
				'pending' => __('%i% pending') // must look like: "# blah blah"
			) );
			$this->add( 'admin-users', '/wp-admin/js/users.js', array('wp-lists'), '20070823' );
			$this->add( 'admin-forms', '/wp-admin/js/forms.js', false, '20080317' );
			$this->add( 'xfn', '/wp-admin/js/xfn.js', false, '3517' );
			$this->add( 'upload', '/wp-admin/js/upload.js', array('jquery'), '20070518' );
			$this->add( 'postbox', '/wp-admin/js/postbox.js', array('jquery'), '20080128' );
			$this->localize( 'postbox', 'postboxL10n', array(
				'requestFile' => get_option( 'siteurl' ) . '/wp-admin/admin-ajax.php',
			) );
			$this->add( 'slug', '/wp-admin/js/slug.js', array('jquery'), '20080208' );
			$this->localize( 'slug', 'slugL10n', array(
				'requestFile' => get_option( 'siteurl' ) . '/wp-admin/admin-ajax.php',
				'save' => __('Save'),
				'cancel' => __('Cancel'),
			) );
			$this->add( 'post', '/wp-admin/js/post.js', array('suggest', 'jquery-ui-tabs', 'wp-lists', 'postbox', 'slug'), '20080422' );
			$this->localize( 'post', 'postL10n', array(
				'tagsUsed' =>  __('Tags used on this post:'),
				'add' => attribute_escape(__('Add')),
				'addTag' => attribute_escape(__('Add new tag')),
				'separate' => __('Separate tags with commas'),
				'cancel' => __('Cancel'),
				'edit' => __('Edit'),
			) );
			$this->add( 'page', '/wp-admin/js/page.js', array('jquery', 'slug', 'postbox'), '20080318' );
			$this->localize( 'page', 'postL10n', array(
				'cancel' => __('Cancel'),
				'edit' => __('Edit'),
			) );
			$this->add( 'link', '/wp-admin/js/link.js', array('jquery-ui-tabs', 'wp-lists', 'postbox'), '20080131' );
			$this->add( 'comment', '/wp-admin/js/comment.js', array('postbox'), '20080219' );
			$this->localize( 'comment', 'commentL10n', array(
					'cancel' => __('Cancel'),
					'edit' => __('Edit'),
				) );
			$this->add( 'media-upload', '/wp-admin/js/media-upload.js', false, '20080109' );
			$this->localize( 'upload', 'uploadL10n', array(
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
			$this->add( 'admin-widgets', '/wp-admin/js/widgets.js', array( 'interface' ), '20080407c' );
			$this->localize( 'admin-widgets', 'widgetsL10n', array(
				'add' => __('Add'),
				'edit' => __('Edit'),
				'cancel' => __('Cancel'),
			));
			$this->add( 'editor', '/wp-admin/js/editor.js', array('tiny_mce'), '20080221' );
		}
	}

	/**
	 * Prints script tags
	 *
	 * Prints the scripts passed to it or the print queue.  Also prints all necessary dependencies.
	 *
	 * @param mixed handles (optional) Scripts to be printed.  (void) prints queue, (string) prints that script, (array of strings) prints those scripts.
	 * @return array Scripts that have been printed
	 */
	function print_scripts( $handles = false ) {
		global $wp_db_version;

		// Print the queue if nothing is passed.  If a string is passed, print that script.  If an array is passed, print those scripts.
		$handles = false === $handles ? $this->queue : (array) $handles;
		$this->all_deps( $handles );

		$to_print = apply_filters( 'print_scripts_array', array_keys($this->to_print) );

		foreach( $to_print as $handle ) {
			if ( !in_array($handle, $this->printed) && isset($this->scripts[$handle]) ) {
				if ( $this->scripts[$handle]->src ) { // Else it defines a group.
					$ver = $this->scripts[$handle]->ver ? $this->scripts[$handle]->ver : $wp_db_version;
					if ( isset($this->args[$handle]) )
						$ver .= '&amp;' . $this->args[$handle];
					$src = 0 === strpos($this->scripts[$handle]->src, 'http://') ? $this->scripts[$handle]->src : get_option( 'siteurl' ) . $this->scripts[$handle]->src;
					$src = $this->scripts[$handle]->src;

					if (!preg_match('|^https?://|', $src)) {
						$src = get_option('siteurl') . $src;
					}

					$src = add_query_arg('ver', $ver, $src);
					$src = clean_url(apply_filters( 'script_loader_src', $src ));
					$this->print_scripts_l10n( $handle );
					echo "<script type='text/javascript' src='$src'></script>\n";
				}
				$this->printed[] = $handle;
			}
		}

		$this->to_print = array();
		return $this->printed;
	}

	function print_scripts_l10n( $handle ) {
		if ( empty($this->scripts[$handle]->l10n_object) || empty($this->scripts[$handle]->l10n) || !is_array($this->scripts[$handle]->l10n) )
			return;

		$object_name = $this->scripts[$handle]->l10n_object;

		echo "<script type='text/javascript'>\n";
		echo "/* <![CDATA[ */\n";
		echo "\t$object_name = {\n";
		$eol = '';
		foreach ( $this->scripts[$handle]->l10n as $var => $val ) {
			echo "$eol\t\t$var: \"" . js_escape( $val ) . '"';
			$eol = ",\n";
		}
		echo "\n\t}\n";
		echo "/* ]]> */\n";
		echo "</script>\n";
	}

	/**
	 * Determines dependencies of scripts
	 *
	 * Recursively builds array of scripts to print taking dependencies into account.  Does NOT catch infinite loops.
	 *
	 * @param mixed handles Accepts (string) script name or (array of strings) script names
	 * @param bool recursion Used internally when function calls itself
	 */
	function all_deps( $handles, $recursion = false ) {
		if ( !$handles = (array) $handles )
			return false;

		foreach ( $handles as $handle ) {
			$handle = explode('?', $handle);
			if ( isset($handle[1]) )
				$this->args[$handle[0]] = $handle[1];
			$handle = $handle[0];

			if ( isset($this->to_print[$handle]) ) // Already grobbed it and its deps
				continue;

			$keep_going = true;
			if ( !isset($this->scripts[$handle]) )
				$keep_going = false; // Script doesn't exist
			elseif ( $this->scripts[$handle]->deps && array_diff($this->scripts[$handle]->deps, array_keys($this->scripts)) )
				$keep_going = false; // Script requires deps which don't exist (not a necessary check.  efficiency?)
			elseif ( $this->scripts[$handle]->deps && !$this->all_deps( $this->scripts[$handle]->deps, true ) )
				$keep_going = false; // Script requires deps which don't exist

			if ( !$keep_going ) { // Either script or its deps don't exist.
				if ( $recursion )
					return false; // Abort this branch.
				else
					continue; // We're at the top level.  Move on to the next one.
			}

			$this->to_print[$handle] = true;
		}

		return true;
	}

	/**
	 * Adds script
	 *
	 * Adds the script only if no script of that name already exists
	 *
	 * @param string handle Script name
	 * @param string src Script url
	 * @param array deps (optional) Array of script names on which this script depends
	 * @param string ver (optional) Script version (used for cache busting)
	 * @return array Hierarchical array of dependencies
	 */
	function add( $handle, $src, $deps = array(), $ver = false ) {
		if ( isset($this->scripts[$handle]) )
			return false;
		$this->scripts[$handle] = new _WP_Script( $handle, $src, $deps, $ver );
		return true;
	}

	/**
	 * Localizes a script
	 *
	 * Localizes only if script has already been added
	 *
	 * @param string handle Script name
	 * @param string object_name Name of JS object to hold l10n info
	 * @param array l10n Array of JS var name => localized string
	 * @return bool Successful localization
	 */
	function localize( $handle, $object_name, $l10n ) {
		if ( !isset($this->scripts[$handle]) )
			return false;
		return $this->scripts[$handle]->localize( $object_name, $l10n );
	}

	function remove( $handles ) {
		foreach ( (array) $handles as $handle )
			unset($this->scripts[$handle]);
	}

	function enqueue( $handles ) {
		foreach ( (array) $handles as $handle ) {
			$handle = explode('?', $handle);
			if ( !in_array($handle[0], $this->queue) && isset($this->scripts[$handle[0]]) ) {
				$this->queue[] = $handle[0];
				if ( isset($handle[1]) )
					$this->args[$handle[0]] = $handle[1];
			}
		}
	}

	function dequeue( $handles ) {
		foreach ( (array) $handles as $handle )
			unset( $this->queue[$handle] );
	}

	function query( $handle, $list = 'scripts' ) { // scripts, queue, or printed
		switch ( $list ) :
		case 'scripts':
			if ( isset($this->scripts[$handle]) )
				return $this->scripts[$handle];
			break;
		default:
			if ( in_array($handle, $this->$list) )
				return true;
			break;
		endswitch;
		return false;
	}

}

class _WP_Script {
	var $handle;
	var $src;
	var $deps = array();
	var $ver = false;
	var $l10n_object = '';
	var $l10n = array();

	function _WP_Script() {
		@list($this->handle, $this->src, $this->deps, $this->ver) = func_get_args();
		if ( !is_array($this->deps) )
			$this->deps = array();
		if ( !$this->ver )
			$this->ver = false;
	}

	function localize( $object_name, $l10n ) {
		if ( !$object_name || !is_array($l10n) )
			return false;
		$this->l10n_object = $object_name;
		$this->l10n = $l10n;
		return true;
	}
}

/**
 * Prints script tags in document head
 *
 * Called by admin-header.php and by wp_head hook. Since it is called by wp_head on every page load,
 * the function does not instantiate the WP_Scripts object unless script names are explicitly passed.
 * Does make use of already instantiated $wp_scripts if present.
 * Use provided wp_print_scripts hook to register/enqueue new scripts.
 *
 * @see WP_Scripts::print_scripts()
 */
function wp_print_scripts( $handles = false ) {
	do_action( 'wp_print_scripts' );
	if ( '' === $handles ) // for wp_head
		$handles = false;

	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') ) {
		if ( !$handles )
			return array(); // No need to instantiate if nothing's there.
		else
			$wp_scripts = new WP_Scripts();
	}

	return $wp_scripts->print_scripts( $handles );
}

function wp_register_script( $handle, $src, $deps = array(), $ver = false ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		$wp_scripts = new WP_Scripts();

	$wp_scripts->add( $handle, $src, $deps, $ver );
}

/**
 * Localizes a script
 *
 * Localizes only if script has already been added
 *
 * @see WP_Script::localize()
 */
function wp_localize_script( $handle, $object_name, $l10n ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		return false;

	return $wp_scripts->localize( $handle, $object_name, $l10n );
}

function wp_deregister_script( $handle ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		$wp_scripts = new WP_Scripts();

	$wp_scripts->remove( $handle );
}

/**
 * Equeues script
 *
 * Registers the script if src provided (does NOT overwrite) and enqueues.
 *
 * @see WP_Script::add(), WP_Script::enqueue()
*/
function wp_enqueue_script( $handle, $src = false, $deps = array(), $ver = false ) {
	global $wp_scripts;
	if ( !is_a($wp_scripts, 'WP_Scripts') )
		$wp_scripts = new WP_Scripts();

	if ( $src ) {
		$_handle = explode('?', $handle);
		$wp_scripts->add( $_handle[0], $src, $deps, $ver );
	}
	$wp_scripts->enqueue( $handle );
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
		'requestFile' => get_option( 'siteurl' ) . '/wp-admin/admin-ajax.php',
		'savingText' => __('Saving Draft&#8230;')
	) );
}

add_filter( 'wp_print_scripts', 'wp_just_in_time_script_localization' );
add_filter( 'print_scripts_array', 'wp_prototype_before_jquery' );

?>
