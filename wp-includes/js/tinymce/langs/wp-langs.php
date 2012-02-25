<?php

// deprecated, not used
function mce_escape($text) {
	return esc_js($text);
}

if ( ! class_exists( '_WP_Editors' ) )
	require( ABSPATH . WPINC . '/class-wp-editor.php' );

function wp_mce_translation() {

	$default = array(
		'common' => array(
			'edit_confirm' => __('Do you want to use the WYSIWYG mode for this textarea?'),
			'apply' => __('Apply'),
			'insert' => __('Insert'),
			'update' => __('Update'),
			'cancel' => __('Cancel'),
			'close' => __('Close'),
			'browse' => __('Browse'),
			'class_name' => __('Class'),
			'not_set' => __('-- Not set --'),
			'clipboard_msg' => __('Copy/Cut/Paste is not available in Mozilla and Firefox.'),
			'clipboard_no_support' => __('Currently not supported by your browser, use keyboard shortcuts instead.'),
			'popup_blocked' => __('Sorry, but we have noticed that your popup-blocker has disabled a window that provides application functionality. You will need to disable popup blocking on this site in order to fully utilize this tool.'),
			'invalid_data' => __('ERROR: Invalid values entered, these are marked in red.'),
			'invalid_data_number' => __('{#field} must be a number'),
			'invalid_data_min' => __('{#field} must be a number greater than {#min}'),
			'invalid_data_size' => __('{#field} must be a number or percentage'),
			'more_colors' => __('More colors')
		),

		'colors' => array(
			'000000' => __('Black'),
			'993300' => __('Burnt orange'),
			'333300' => __('Dark olive'),
			'003300' => __('Dark green'),
			'003366' => __('Dark azure'),
			'000080' => __('Navy Blue'),
			'333399' => __('Indigo'),
			'333333' => __('Very dark gray'),
			'800000' => __('Maroon'),
			'FF6600' => __('Orange'),
			'808000' => __('Olive'),
			'008000' => __('Green'),
			'008080' => __('Teal'),
			'0000FF' => __('Blue'),
			'666699' => __('Grayish blue'),
			'808080' => __('Gray'),
			'FF0000' => __('Red'),
			'FF9900' => __('Amber'),
			'99CC00' => __('Yellow green'),
			'339966' => __('Sea green'),
			'33CCCC' => __('Turquoise'),
			'3366FF' => __('Royal blue'),
			'800080' => __('Purple'),
			'999999' => __('Medium gray'),
			'FF00FF' => __('Magenta'),
			'FFCC00' => __('Gold'),
			'FFFF00' => __('Yellow'),
			'00FF00' => __('Lime'),
			'00FFFF' => __('Aqua'),
			'00CCFF' => __('Sky blue'),
			'993366' => __('Brown'),
			'C0C0C0' => __('Silver'),
			'FF99CC' => __('Pink'),
			'FFCC99' => __('Peach'),
			'FFFF99' => __('Light yellow'),
			'CCFFCC' => __('Pale green'),
			'CCFFFF' => __('Pale cyan'),
			'99CCFF' => __('Light sky blue'),
			'CC99FF' => __('Plum'),
			'FFFFFF' => __('White')
		),

		'contextmenu' => array(
			'align' => __('Alignment'), /* translators: alignment */
			'left' => __('Left'), /* translators: alignment */
			'center' => __('Center'), /* translators: alignment */
			'right' => __('Right'), /* translators: alignment */
			'full' => __('Full') /* translators: alignment */
		),

		'insertdatetime' => array(
			'date_fmt' => __('%Y-%m-%d'), /* translators: year, month, date */
			'time_fmt' => __('%H:%M:%S'), /* translators: hours, minutes, seconds */
			'insertdate_desc' => __('Insert date'),
			'inserttime_desc' => __('Insert time'),
			'months_long' => __('January').','.__('February').','.__('March').','.__('April').','.__('May').','.__('June').','.__('July').','.__('August').','.__('September').','.__('October').','.__('November').','.__('December'),
			'months_short' => __('Jan_January_abbreviation').','.__('Feb_February_abbreviation').','.__('Mar_March_abbreviation').','.__('Apr_April_abbreviation').','.__('May_May_abbreviation').','.__('Jun_June_abbreviation').','.__('Jul_July_abbreviation').','.__('Aug_August_abbreviation').','.__('Sep_September_abbreviation').','.__('Oct_October_abbreviation').','.__('Nov_November_abbreviation').','.__('Dec_December_abbreviation'),
			'day_long' => __('Sunday').','.__('Monday').','.__('Tuesday').','.__('Wednesday').','.__('Thursday').','.__('Friday').','.__('Saturday'),
			'day_short' => __('Sun').','.__('Mon').','.__('Tue').','.__('Wed').','.__('Thu').','.__('Fri').','.__('Sat')
		),

		'print' => array(
			'print_desc' => __('Print')
		),

		'preview' => array(
			'preview_desc' => __('Preview')
		),

		'directionality' => array(
			'ltr_desc' => __('Direction left to right'),
			'rtl_desc' => __('Direction right to left')
		),

		'layer' => array(
			'insertlayer_desc' => __('Insert new layer'),
			'forward_desc' => __('Move forward'),
			'backward_desc' => __('Move backward'),
			'absolute_desc' => __('Toggle absolute positioning'),
			'content' => __('New layer...')
		),

		'save' => array(
			'save_desc' => __('Save'),
			'cancel_desc' => __('Cancel all changes')
		),

		'nonbreaking' => array(
			'nonbreaking_desc' => __('Insert non-breaking space character')
		),

		'iespell' => array(
			'iespell_desc' => __('Run spell checking'),
			'download' => __('ieSpell not detected. Do you want to install it now?')
		),

		'advhr' => array(
			'advhr_desc' => __('Horizontale rule')
		),

		'emotions' => array(
			'emotions_desc' => __('Emotions')
		),

		'searchreplace' => array(
			'search_desc' => __('Find'),
			'replace_desc' => __('Find/Replace')
		),

		'advimage' => array(
			'image_desc' => __('Insert/edit image')
		),

		'advlink' => array(
			'link_desc' => __('Insert/edit link')
		),

		'xhtmlxtras' => array(
			'cite_desc' => __('Citation'),
			'abbr_desc' => __('Abbreviation'),
			'acronym_desc' => __('Acronym'),
			'del_desc' => __('Deletion'),
			'ins_desc' => __('Insertion'),
			'attribs_desc' => __('Insert/Edit Attributes')
		),

		'style' => array(
			'desc' => __('Edit CSS Style')
		),

		'paste' => array(
			'paste_text_desc' => __('Paste as Plain Text'),
			'paste_word_desc' => __('Paste from Word'),
			'selectall_desc' => __('Select All'),
			'plaintext_mode_sticky' => __('Paste is now in plain text mode. Click again to toggle back to regular paste mode. After you paste something you will be returned to regular paste mode.'),
			'plaintext_mode' => __('Paste is now in plain text mode. Click again to toggle back to regular paste mode.')
		),

		'paste_dlg' => array(
			'text_title' => __('Use CTRL+V on your keyboard to paste the text into the window.'),
			'text_linebreaks' => __('Keep linebreaks'),
			'word_title' => __('Use CTRL+V on your keyboard to paste the text into the window.')
		),

		'table' => array(
			'desc' => __('Inserts a new table'),
			'row_before_desc' => __('Insert row before'),
			'row_after_desc' => __('Insert row after'),
			'delete_row_desc' => __('Delete row'),
			'col_before_desc' => __('Insert column before'),
			'col_after_desc' => __('Insert column after'),
			'delete_col_desc' => __('Remove column'),
			'split_cells_desc' => __('Split merged table cells'),
			'merge_cells_desc' => __('Merge table cells'),
			'row_desc' => __('Table row properties'),
			'cell_desc' => __('Table cell properties'),
			'props_desc' => __('Table properties'),
			'paste_row_before_desc' => __('Paste table row before'),
			'paste_row_after_desc' => __('Paste table row after'),
			'cut_row_desc' => __('Cut table row'),
			'copy_row_desc' => __('Copy table row'),
			'del' => __('Delete table'),
			'row' => __('Row'),
			'col' => __('Column'),
			'cell' => __('Cell')
		),

		'autosave' => array(
			'unload_msg' => __('The changes you made will be lost if you navigate away from this page.')
		),

		'fullscreen' => array(
			'desc' => __('Toggle fullscreen mode (Alt + Shift + G)')
		),

		'media' => array(
			'desc' => __('Insert / edit embedded media'),
			'edit' => __('Edit embedded media')
		),

		'fullpage' => array(
			'desc' => __('Document properties')
		),

		'template' => array(
			'desc' => __('Insert predefined template content')
		),

		'visualchars' => array(
			'desc' => __('Visual control characters on/off.')
		),

		'spellchecker' => array(
			'desc' => __('Toggle spellchecker (Alt + Shift + N)'),
			'menu' => __('Spellchecker settings'),
			'ignore_word' => __('Ignore word'),
			'ignore_words' => __('Ignore all'),
			'langs' => __('Languages'),
			'wait' => __('Please wait...'),
			'sug' => __('Suggestions'),
			'no_sug' => __('No suggestions'),
			'no_mpell' => __('No misspellings found.'),
			'learn_word' => __('Learn word')
		),

		'pagebreak' => array(
			'desc' => __('Insert Page Break')
		),

		'advlist' => array(
			'types' => __('Types'),
			'def' => __('Default'),
			'lower_alpha' => __('Lower alpha'),
			'lower_greek' => __('Lower greek'),
			'lower_roman' => __('Lower roman'),
			'upper_alpha' => __('Upper alpha'),
			'upper_roman' => __('Upper roman'),
			'circle' => __('Circle'),
			'disc' => __('Disc'),
			'square' => __('Square')
		),

		'aria' => array(
			'rich_text_area' => __('Rich Text Area')
		),

		'wordcount' => array(
			'words' => __('Words:')
		)
	);

	$advanced = array(
		'style_select' => __('Styles'), /* translators: TinyMCE inline styles */
		'font_size' => __('Font size'),
		'fontdefault' => __('Font family'),
		'block' => __('Format'),
		'paragraph' => __('Paragraph'),
		'div' => __('Div'),
		'address' => __('Address'),
		'pre' => __('Preformatted'),
		'h1' => __('Heading 1'),
		'h2' => __('Heading 2'),
		'h3' => __('Heading 3'),
		'h4' => __('Heading 4'),
		'h5' => __('Heading 5'),
		'h6' => __('Heading 6'),
		'blockquote' => __('Blockquote'),
		'code' => __('Code'),
		'samp' => __('Code sample'),
		'dt' => __('Definition term '),
		'dd' => __('Definition description'),
		'bold_desc' => __('Bold (Ctrl + B)'),
		'italic_desc' => __('Italic (Ctrl + I)'),
		'underline_desc' => __('Underline'),
		'striketrough_desc' => __('Strikethrough (Alt + Shift + D)'),
		'justifyleft_desc' => __('Align Left (Alt + Shift + L)'),
		'justifycenter_desc' => __('Align Center (Alt + Shift + C)'),
		'justifyright_desc' => __('Align Right (Alt + Shift + R)'),
		'justifyfull_desc' => __('Align Full (Alt + Shift + J)'),
		'bullist_desc' => __('Unordered list (Alt + Shift + U)'),
		'numlist_desc' => __('Ordered list (Alt + Shift + O)'),
		'outdent_desc' => __('Outdent'),
		'indent_desc' => __('Indent'),
		'undo_desc' => __('Undo (Ctrl + Z)'),
		'redo_desc' => __('Redo (Ctrl + Y)'),
		'link_desc' => __('Insert/edit link (Alt + Shift + A)'),
		'unlink_desc' => __('Unlink (Alt + Shift + S)'),
		'image_desc' => __('Insert/edit image (Alt + Shift + M)'),
		'cleanup_desc' => __('Cleanup messy code'),
		'code_desc' => __('Edit HTML Source'),
		'sub_desc' => __('Subscript'),
		'sup_desc' => __('Superscript'),
		'hr_desc' => __('Insert horizontal ruler'),
		'removeformat_desc' => __('Remove formatting'),
		'forecolor_desc' => __('Select text color'),
		'backcolor_desc' => __('Select background color'),
		'charmap_desc' => __('Insert custom character'),
		'visualaid_desc' => __('Toggle guidelines/invisible elements'),
		'anchor_desc' => __('Insert/edit anchor'),
		'cut_desc' => __('Cut'),
		'copy_desc' => __('Copy'),
		'paste_desc' => __('Paste'),
		'image_props_desc' => __('Image properties'),
		'newdocument_desc' => __('New document'),
		'help_desc' => __('Help'),
		'blockquote_desc' => __('Blockquote (Alt + Shift + Q)'),
		'clipboard_msg' => __('Copy/Cut/Paste is not available in Mozilla and Firefox.'),
		'path' => __('Path'),
		'newdocument' => __('Are you sure you want to clear all contents?'),
		'toolbar_focus' => __('Jump to tool buttons - Alt+Q, Jump to editor - Alt-Z, Jump to element path - Alt-X'),
		'more_colors' => __('More colors'),
		'shortcuts_desc' => __('Accessibility Help'),
		'help_shortcut' => __('Press ALT F10 for toolbar. Press ALT 0 for help.'),
		'rich_text_area' => __('Rich Text Area'),
		'toolbar' => __('Toolbar')
	);

	$advanced_dlg = array(
		'about_title' => __('About TinyMCE'),
		'about_general' => __('About'),
		'about_help' => __('Help'),
		'about_license' => __('License'),
		'about_plugins' => __('Plugins'),
		'about_plugin' => __('Plugin'),
		'about_author' => __('Author'),
		'about_version' => __('Version'),
		'about_loaded' => __('Loaded plugins'),
		'anchor_title' => __('Insert/edit anchor'),
		'anchor_name' => __('Anchor name'),
		'code_title' => __('HTML Source Editor'),
		'code_wordwrap' => __('Word wrap'),
		'colorpicker_title' => __('Select a color'),
		'colorpicker_picker_tab' => __('Picker'),
		'colorpicker_picker_title' => __('Color picker'),
		'colorpicker_palette_tab' => __('Palette'),
		'colorpicker_palette_title' => __('Palette colors'),
		'colorpicker_named_tab' => __('Named'),
		'colorpicker_named_title' => __('Named colors'),
		'colorpicker_color' => __('Color:'),
		'colorpicker_name' => _x('Name:', 'html attribute'),
		'charmap_title' => __('Select custom character'),
		'charmap_usage' => __('Use left and right arrows to navigate.'),
		'image_title' => __('Insert/edit image'),
		'image_src' => __('Image URL'),
		'image_alt' => __('Image description'),
		'image_list' => __('Image list'),
		'image_border' => __('Border'),
		'image_dimensions' => __('Dimensions'),
		'image_vspace' => __('Vertical space'),
		'image_hspace' => __('Horizontal space'),
		'image_align' => __('Alignment'),
		'image_align_baseline' => __('Baseline'),
		'image_align_top' => __('Top'),
		'image_align_middle' => __('Middle'),
		'image_align_bottom' => __('Bottom'),
		'image_align_texttop' => __('Text top'),
		'image_align_textbottom' => __('Text bottom'),
		'image_align_left' => __('Left'),
		'image_align_right' => __('Right'),
		'link_title' => __('Insert/edit link'),
		'link_url' => __('Link URL'),
		'link_target' => __('Target'),
		'link_target_same' => __('Open link in the same window'),
		'link_target_blank' => __('Open link in a new window'),
		'link_titlefield' => __('Title'),
		'link_is_email' => __('The URL you entered seems to be an email address, do you want to add the required mailto: prefix?'),
		'link_is_external' => __('The URL you entered seems to external link, do you want to add the required http:// prefix?'),
		'link_list' => __('Link list'),
		'accessibility_help' => __('Accessibility Help'),
		'accessibility_usage_title' => __('General Usage')
	);

	$media_dlg = array(
		'title' => __('Insert / edit embedded media'),
		'general' => __('General'),
		'advanced' => __('Advanced'),
		'file' => __('File/URL'),
		'list' => __('List'),
		'size' => __('Dimensions'),
		'preview' => __('Preview'),
		'constrain_proportions' => __('Constrain proportions'),
		'type' => __('Type'),
		'id' => __('Id'),
		'name' => _x('Name', 'html attribute'),
		'class_name' => __('Class'),
		'vspace' => __('V-Space'),
		'hspace' => __('H-Space'),
		'play' => __('Auto play'),
		'loop' => __('Loop'),
		'menu' => __('Show menu'),
		'quality' => __('Quality'),
		'scale' => __('Scale'),
		'align' => __('Align'),
		'salign' => __('SAlign'),
		'wmode' => __('WMode'),
		'bgcolor' => __('Background'),
		'base' => __('Base'),
		'flashvars' => __('Flashvars'),
		'liveconnect' => __('SWLiveConnect'),
		'autohref' => __('AutoHREF'),
		'cache' => __('Cache'),
		'hidden' => __('Hidden'),
		'controller' => __('Controller'),
		'kioskmode' => __('Kiosk mode'),
		'playeveryframe' => __('Play every frame'),
		'targetcache' => __('Target cache'),
		'correction' => __('No correction'),
		'enablejavascript' => __('Enable JavaScript'),
		'starttime' => __('Start time'),
		'endtime' => __('End time'),
		'href' => __('href'),
		'qtsrcchokespeed' => __('Choke speed'),
		'target' => __('Target'),
		'volume' => __('Volume'),
		'autostart' => __('Auto start'),
		'enabled' => __('Enabled'),
		'fullscreen' => __('Fullscreen'),
		'invokeurls' => __('Invoke URLs'),
		'mute' => __('Mute'),
		'stretchtofit' => __('Stretch to fit'),
		'windowlessvideo' => __('Windowless video'),
		'balance' => __('Balance'),
		'baseurl' => __('Base URL'),
		'captioningid' => __('Captioning id'),
		'currentmarker' => __('Current marker'),
		'currentposition' => __('Current position'),
		'defaultframe' => __('Default frame'),
		'playcount' => __('Play count'),
		'rate' => __('Rate'),
		'uimode' => __('UI Mode'),
		'flash_options' => __('Flash options'),
		'qt_options' => __('Quicktime options'),
		'wmp_options' => __('Windows media player options'),
		'rmp_options' => __('Real media player options'),
		'shockwave_options' => __('Shockwave options'),
		'autogotourl' => __('Auto goto URL'),
		'center' => __('Center'),
		'imagestatus' => __('Image status'),
		'maintainaspect' => __('Maintain aspect'),
		'nojava' => __('No java'),
		'prefetch' => __('Prefetch'),
		'shuffle' => __('Shuffle'),
		'console' => __('Console'),
		'numloop' => __('Num loops'),
		'controls' => __('Controls'),
		'scriptcallbacks' => __('Script callbacks'),
		'swstretchstyle' => __('Stretch style'),
		'swstretchhalign' => __('Stretch H-Align'),
		'swstretchvalign' => __('Stretch V-Align'),
		'sound' => __('Sound'),
		'progress' => __('Progress'),
		'qtsrc' => __('QT Src'),
		'qt_stream_warn' => __('Streamed rtsp resources should be added to the QT Src field under the advanced tab.'),
		'align_top' => __('Top'),
		'align_right' => __('Right'),
		'align_bottom' => __('Bottom'),
		'align_left' => __('Left'),
		'align_center' => __('Center'),
		'align_top_left' => __('Top left'),
		'align_top_right' => __('Top right'),
		'align_bottom_left' => __('Bottom left'),
		'align_bottom_right' => __('Bottom right'),
		'flv_options' => __('Flash video options'),
		'flv_scalemode' => __('Scale mode'),
		'flv_buffer' => __('Buffer'),
		'flv_startimage' => __('Start image'),
		'flv_starttime' => __('Start time'),
		'flv_defaultvolume' => __('Default volume'),
		'flv_hiddengui' => __('Hidden GUI'),
		'flv_autostart' => __('Auto start'),
		'flv_loop' => __('Loop'),
		'flv_showscalemodes' => __('Show scale modes'),
		'flv_smoothvideo' => __('Smooth video'),
		'flv_jscallback' => __('JS Callback'),
		'html5_video_options' => __('HTML5 Video Options'),
		'altsource1' => __('Alternative source 1'),
		'altsource2' => __('Alternative source 2'),
		'preload' => __('Preload'),
		'poster' => __('Poster'),
		'source' => __('Source')
	);

	$wordpress = array(
		'wp_adv_desc' => __('Show/Hide Kitchen Sink (Alt + Shift + Z)'),
		'wp_more_desc' => __('Insert More Tag (Alt + Shift + T)'),
		'wp_page_desc' => __('Insert Page break (Alt + Shift + P)'),
		'wp_help_desc' => __('Help (Alt + Shift + H)'),
		'wp_more_alt' => __('More...'),
		'wp_page_alt' => __('Next page...'),
		'add_media' => __('Add Media'),
		'add_image' => __('Add an Image'),
		'add_video' => __('Add Video'),
		'add_audio' => __('Add Audio'),
		'editgallery' => __('Edit Gallery'),
		'delgallery' => __('Delete Gallery')
	);

	$wpeditimage = array(
		'edit_img' => __('Edit Image'),
		'del_img' => __('Delete Image'),
		'adv_settings' => __('Advanced Settings'),
		'none' => __('None'),
		'size' => __('Size'),
		'thumbnail' => __('Thumbnail'),
		'medium' => __('Medium'),
		'full_size' => __('Full Size'),
		'current_link' => __('Current Link'),
		'link_to_img' => __('Link to Image'),
		'link_help' => __('Enter a link URL or click above for presets.'),
		'adv_img_settings' => __('Advanced Image Settings'),
		'source' => __('Source'),
		'width' => __('Width'),
		'height' => __('Height'),
		'orig_size' => __('Original Size'),
		'css' => __('CSS Class'),
		'adv_link_settings' => __('Advanced Link Settings'),
		'link_rel' => __('Link Rel'),
		'height' => __('Height'),
		'orig_size' => __('Original Size'),
		'css' => __('CSS Class'),
		's60' => __('60%'),
		's70' => __('70%'),
		's80' => __('80%'),
		's90' => __('90%'),
		's100' => __('100%'),
		's110' => __('110%'),
		's120' => __('120%'),
		's130' => __('130%'),
		'img_title' => __('Title'),
		'caption' => __('Caption'),
		'insert_link' => __('Insert link'),
		'linked_text' => __('Linked text'),
		'alt' => __('Alternate Text')
	);

	$locale = _WP_Editors::$mce_locale;

	$translated = 'tinyMCE.addI18n({' . $locale . ':' . json_encode( $default ) . "});\n";
	$translated .= 'tinyMCE.addI18n("' . $locale . '.advanced", ' . json_encode( $advanced ) . ");\n";
	$translated .= 'tinyMCE.addI18n("' . $locale . '.advanced_dlg", ' . json_encode( $advanced_dlg ) . ");\n";
	$translated .= 'tinyMCE.addI18n("' . $locale . '.media_dlg", ' . json_encode( $media_dlg ) . ");\n";
	$translated .= 'tinyMCE.addI18n("' . $locale . '.wordpress", ' . json_encode( $wordpress ) . ");\n";
	$translated .= 'tinyMCE.addI18n("' . $locale . '.wpeditimage", ' . json_encode( $wpeditimage ) . ');';

	return $translated;
}

$lang = wp_mce_translation();

