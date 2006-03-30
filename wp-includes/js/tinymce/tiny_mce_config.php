<?php
	@ require('../../../wp-config.php');

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
	$valid_elements = 'p/-div[*],-b[*],-font[*],-ul[*],-ol[*],-li[*],*[*]';
	$valid_elements = apply_filters('mce_valid_elements', $valid_elements);

	$plugins = array('inlinepopups', 'autosave', 'spellchecker', 'paste', 'wordpress');
	$plugins = apply_filters('mce_plugins', $plugins);
	$plugins = implode($plugins, ',');

	$mce_buttons = apply_filters('mce_buttons', array('bold', 'italic', 'strikethrough', 'separator', 'bullist', 'numlist', 'outdent', 'indent', 'separator', 'justifyleft', 'justifycenter', 'justifyright', 'separator', 'link', 'unlink', 'image', 'wp_more', 'separator', 'spellchecker', 'separator', 'code', 'wp_help', 'wp_adv_start', 'wp_adv', 'separator', 'formatselect', 'underline', 'justifyfull', 'forecolor', 'separator', 'pastetext', 'pasteword', 'separator', 'removeformat', 'cleanup', 'separator', 'charmap', 'separator', 'undo', 'redo', 'wp_adv_end'));
	$mce_buttons = implode($mce_buttons, ',');

	$mce_buttons_2 = apply_filters('mce_buttons_2', array());
	$mce_buttons_2 = implode($mce_buttons_2, ',');

	$mce_buttons_3 = apply_filters('mce_buttons_3', array());
	$mce_buttons_3 = implode($mce_buttons_3, ',');

	$mce_browsers = apply_filters('mce_browsers', array('msie', 'gecko', 'opera', 'safari'));
	$mce_browsers = implode($mce_browsers, ',');
	
	$mce_popups_css = get_option('siteurl') . '/wp-includes/js/tinymce/plugins/wordpress/popups.css';
	$mce_css = get_option('siteurl') . '/wp-includes/js/tinymce/plugins/wordpress/wordpress.css';
	$mce_css = apply_filters('mce_css', $mce_css);
?>

initArray = {
	mode : "specific_textareas",
	editor_selector : "mceEditor",
	width : "100%",
	theme : "advanced",
	theme_advanced_buttons1 : "<?php echo $mce_buttons; ?>",
	theme_advanced_buttons2 : "<?php echo $mce_buttons_2; ?>",
	theme_advanced_buttons3 : "<?php echo $mce_buttons_3; ?>",
	language : "<?php echo strtolower(get_locale()); ?>",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
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
	content_css : "<?php echo $mce_css; ?>",
	valid_elements : "<?php echo $valid_elements; ?>",
	save_callback : 'TinyMCE_wordpressPlugin.saveCallback',
<?php do_action('mce_options'); ?>
	plugins : "<?php echo $plugins; ?>"
};

<?php
	// For people who really REALLY know what they're doing with TinyMCE
	// You can modify initArray to add, remove, change elements of the config before tinyMCE.init
	do_action('tinymce_before_init');
?>

tinyMCE.init(initArray);
