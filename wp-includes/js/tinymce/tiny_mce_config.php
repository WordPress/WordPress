<?php
	@ require('../../../wp-config.php');
	cache_javascript_headers();

	function wp_translate_tinymce_lang($text) {
		if ( ! function_exists('__') ) {
			return $text;
		} else {
			$search1 = "/^tinyMCELang\\[(['\"])(.*)\\1\]( ?= ?)(['\"])(.*)\\4/Uem";
			$replace1 = "'tinyMCELang[\\1\\2\\1]\\3'.stripslashes('\\4').__('\\5').stripslashes('\\4')";

			$search2 = "/ : (['\"])(.*)\\1/Uem";
			$replace2 = "' : '.stripslashes('\\1').__('\\2').stripslashes('\\1')";

			$search = array($search1, $search2);
			$replace = array($replace1, $replace2);

			$text = preg_replace($search, $replace, $text);

			return $text;
		}
	}

	// Set up init variables
	$valid_elements = '*[*]';
	$valid_elements = apply_filters('mce_valid_elements', $valid_elements);

    $invalid_elements = apply_filters('mce_invalid_elements', '');

	$plugins = array( 'safari', 'inlinepopups', 'autosave', 'spellchecker', 'paste', 'wordpress', 'media', 'fullscreen' );
	$plugins = apply_filters('mce_plugins', $plugins);
	$plugins = implode($plugins, ',');

	$mce_buttons = apply_filters('mce_buttons', array('bold', 'italic', 'strikethrough', '|', 'bullist', 'numlist', 'outdent', 'indent', '|', 'justifyleft', 'justifycenter', 'justifyright', '|', 'link', 'unlink', 'image', 'wp_more', '|', 'spellchecker', '|', 'wp_help', 'wp_adv' ));
	$mce_buttons = implode($mce_buttons, ',');


	$mce_buttons_2 = apply_filters('mce_buttons_2', array('formatselect', 'underline', 'justifyfull', 'forecolor', '|', 'pastetext', 'pasteword', '|', 'removeformat', 'cleanup', '|', 'media', 'charmap', 'blockquote', '|', 'undo', 'redo', 'fullscreen' ));
	$mce_buttons_2 = implode($mce_buttons_2, ',');

	$mce_buttons_3 = apply_filters('mce_buttons_3', array());
	$mce_buttons_3 = implode($mce_buttons_3, ',');

	$mce_buttons_4 = apply_filters('mce_buttons_4', array());
	$mce_buttons_4 = implode($mce_buttons_4, ',');

	$mce_browsers = apply_filters('mce_browsers', array('msie', 'gecko', 'opera', 'safari'));
	$mce_browsers = implode($mce_browsers, ',');

	$mce_css = get_option('siteurl') . '/wp-includes/js/tinymce/wordpress.css';
	$mce_css = apply_filters('mce_css', $mce_css);
	if ( $_SERVER['HTTPS'] == 'on' )
		$mce_css = str_replace('http://', 'https://', $mce_css);

	$mce_locale = ( '' == get_locale() ) ? 'en' : strtolower(get_locale());
?>

initArray = {
	mode : "none",
	onpageload : "wpEditorInit",
    width : "100%",
	theme : "advanced",
	skin : "wp_theme",
	theme_advanced_buttons1 : "<?php echo $mce_buttons; ?>",
	theme_advanced_buttons2 : "<?php echo $mce_buttons_2; ?>",
	theme_advanced_buttons3 : "<?php echo $mce_buttons_3; ?>",
	theme_advanced_buttons4 : "<?php echo $mce_buttons_4; ?>",
	language : "<?php echo $mce_locale; ?>",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	browsers : "<?php echo $mce_browsers; ?>",
	dialog_type : "modal",
	theme_advanced_resize_horizontal : false,
	convert_urls : false,
	relative_urls : false,
	remove_script_host : false,
	force_p_newlines : true,
	force_br_newlines : false,
	convert_newlines_to_brs : false,
	remove_linebreaks : false,
	fix_list_elements : true,
	fix_table_elements : true,
	gecko_spellcheck : true,
	entities : "38,amp,60,lt,62,gt",
	accessibility_focus : false,
	tab_focus : ":next",
	content_css : "<?php echo $mce_css; ?>",
	<?php if ( $valid_elements ) echo 'valid_elements : "' . $valid_elements . '",' . "\n"; ?>
	<?php if ( $invalid_elements ) echo 'invalid_elements : "' . $invalid_elements . '",' . "\n"; ?>
    save_callback : "switchEditors.saveCallback",
<?php do_action('mce_options'); ?>
	plugins : "<?php echo $plugins; ?>"
};

<?php
	// For people who really REALLY know what they're doing with TinyMCE
	// You can modify initArray to add, remove, change elements of the config before tinyMCE.init
	do_action('tinymce_before_init');
?>

tinyMCE_GZ.init(initArray);
