<?php

if ( !defined('ABSPATH') )
	exit;
/**
 * @ignore
 */
function __() {}

/**
 * @ignore
 */
function _c() {}

/**
 * @ignore
 */
function _x() {}


/**
 * @ignore
 */
function add_filter() {}

/**
 * @ignore
 */
function attribute_escape() {}

/**
 * @ignore
 */
function apply_filters() {}

/**
 * @ignore
 */
function get_option() {}

/**
 * @ignore
 */
function is_lighttpd_before_150() {}

/**
 * @ignore
 */
function add_action() {}

/**
 * @ignore
 */
function do_action_ref_array() {}

/**
 * @ignore
 */
function get_bloginfo() {}

/**
 * @ignore
 */
function is_admin() {return true;}

/**
 * @ignore
 */
function site_url() {}

/**
 * @ignore
 */
function admin_url() {}

/**
 * @ignore
 */
function wp_guess_url() {}

require(ABSPATH . '/wp-includes/script-loader.php');
require(ABSPATH . '/wp-includes/version.php');

/**
 * Retrieve list of all cacheable WP files
 *
 * Array format: file, version (optional), bool (whether to use src and set ignoreQuery)
 */

$wp_scripts = new WP_Scripts();
wp_default_scripts($wp_scripts);

$wp_styles = new WP_Styles();
wp_default_styles($wp_styles);

function &get_manifest(&$man_ver) {
	global $wp_scripts, $wp_styles, $wp_version;

	$files = array();
	foreach ( $wp_scripts->registered as $script ) {
		if ( empty($script->src) ) continue;
		$ver = empty($script->ver) ? $wp_version : $script->ver;
		if ( 'editor' == $script->handle ) $mce_ver = $script->ver;
		$src = str_replace( array( '/wp-admin/', '/wp-includes/' ), array( '', '../wp-includes/' ), $script->src );
		$files[] = array($src, $ver);
		$man_ver .= $ver;
	}

	foreach ( $wp_styles->registered as $style ) {
		if ( empty($style->src) ) continue;

		$ver = empty($style->ver) ? $wp_version : $style->ver;
		$src = str_replace( array( '/wp-admin/', '/wp-includes/' ), array( '', '../wp-includes/' ), $style->src );
		if ( 'colors' == $style->handle ) $src = 'css/colors-classic.css';
		$files[] = array($src, $ver);
		 $man_ver .= $ver;

		if ( isset($style->extra['rtl']) && $style->extra['rtl'] ) {
			if ( is_bool( $style->extra['rtl'] ) )
				$rtl_href = str_replace( '.css', '-rtl.css', $src );
			else
				$rtl_href = str_replace( array( '/wp-admin/', '/wp-includes/' ), array( '', '../wp-includes/' ), $style->extra['rtl'] );

			$files[] = array($rtl_href, $ver);
			 $man_ver .= $ver;
		}
	}

	$images = array(
		array('images/align-center.png'),
		array('images/align-left.png'),
		array('images/align-none.png'),
		array('images/align-right.png'),
		array('images/archive-link.png'),
		array('images/blue-grad.png'),
		array('images/browse-happy.gif'),
		array('images/bubble_bg.gif'),
		array('images/bubble_bg-rtl.gif'),
		array('images/button-grad.png'),
		array('images/button-grad-active.png'),
		array('images/comment-grey-bubble.png'),
		array('images/date-button.gif'),
		array('images/ed-bg.gif'),
		array('images/fade-butt.png'),
		array('images/fav.png'),
		array('images/fav-arrow.gif'),
		array('images/fav-arrow-rtl.gif'),
		array('images/fav-top.png'),
		array('images/generic.png'),
		array('images/gray-grad.png'),
		array('images/icons32.png'),
		array('images/icons32-vs.png'),
		array('images/list.png'),
		array('images/list-vs.png'),
		array('images/loading.gif'),
		array('images/loading-publish.gif'),
		array('images/logo.gif'),
		array('images/logo-ghost.png'),
		array('images/logo-login.gif'),
		array('images/media-button-image.gif'),
		array('images/media-button-music.gif'),
		array('images/media-button-other.gif'),
		array('images/media-button-video.gif'),
		array('images/menu.png'),
		array('images/menu-vs.png'),
		array('images/menu-arrows.gif'),
		array('images/menu-bits.gif'),
		array('images/menu-bits-rtl.gif'),
		array('images/menu-dark.gif'),
		array('images/menu-dark-rtl.gif'),
		array('images/no.png'),
		array('images/required.gif'),
		array('images/resize.gif'),
		array('images/screen-options-left.gif'),
		array('images/screen-options-right.gif'),
		array('images/screen-options-right-up.gif'),
		array('images/se.png'),
		array('images/star.gif'),
		array('images/toggle-arrow.gif'),
		array('images/toggle-arrow-rtl.gif'),
		array('images/white-grad.png'),
		array('images/white-grad-active.png'),
		array('images/wordpress-logo.png'),
		array('images/wp-logo.gif'),
		array('images/xit.gif'),
		array('images/yes.png'),
		array('../wp-includes/images/crystal/archive.png'),
		array('../wp-includes/images/crystal/audio.png'),
		array('../wp-includes/images/crystal/code.png'),
		array('../wp-includes/images/crystal/default.png'),
		array('../wp-includes/images/crystal/document.png'),
		array('../wp-includes/images/crystal/interactive.png'),
		array('../wp-includes/images/crystal/text.png'),
		array('../wp-includes/images/crystal/video.png'),
		array('../wp-includes/images/crystal/spreadsheet.png'),
		array('../wp-includes/images/rss.png'),
		array('../wp-includes/js/thickbox/loadingAnimation.gif'),
		array('../wp-includes/js/thickbox/tb-close.png'),
	);

	$files = array_merge($files, $images);

	if ( @is_file('../wp-includes/js/tinymce/tiny_mce.js') ) :
	$mce = array(
		array('../wp-includes/js/tinymce/wp-tinymce.php', $mce_ver, true),
		array('../wp-includes/js/tinymce/tiny_mce.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/langs/wp-langs-en.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/utils/mctabs.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/utils/validate.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/utils/form_utils.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/utils/editable_selects.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/tiny_mce_popup.js', $mce_ver, true),

		array('../wp-includes/js/tinymce/themes/advanced/editor_template.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/source_editor.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/anchor.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/image.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/link.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/color_picker.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/charmap.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/js/color_picker.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/js/charmap.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/js/image.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/js/link.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/js/source_editor.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/js/anchor.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/ui.css', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/content.css', $mce_ver, true),
		array('../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/dialog.css', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/autosave/editor_plugin.js', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/fullscreen/editor_plugin.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/fullscreen/fullscreen.htm', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/inlinepopups/editor_plugin.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/inlinepopups/template.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/window.css', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/media/editor_plugin.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/media/js/media.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/media/media.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/media/css/content.css', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/media/css/media.css', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/paste/editor_plugin.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/paste/js/pasteword.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/paste/js/pastetext.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/paste/pasteword.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/paste/blank.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/paste/pastetext.htm', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/paste/css/pasteword.css', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/paste/css/blank.css', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/safari/editor_plugin.js', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/spellchecker/editor_plugin.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/spellchecker/css/content.css', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/tabfocus/editor_plugin.js', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/wordpress/editor_plugin.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/wordpress/css/content.css', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/wpeditimage/editor_plugin.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/wpeditimage/editimage.html', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/wpeditimage/js/editimage.js', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/wpeditimage/css/editimage.css', $mce_ver, true),
		array('../wp-includes/js/tinymce/plugins/wpeditimage/css/editimage-rtl.css', $mce_ver, true),

		array('../wp-includes/js/tinymce/plugins/wpgallery/editor_plugin.js', $mce_ver, true),

		array('../wp-includes/js/tinymce/themes/advanced/img/icons.gif'),
		array('../wp-includes/js/tinymce/themes/advanced/img/colorpicker.jpg'),
		array('../wp-includes/js/tinymce/themes/advanced/img/fm.gif'),
		array('../wp-includes/js/tinymce/themes/advanced/img/gotmoxie.png'),
		array('../wp-includes/js/tinymce/themes/advanced/img/sflogo.png'),
		array('../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/butt2.png'),
		array('../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/fade-butt.png'),
		array('../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/tabs.gif'),
		array('../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/down_arrow.gif'),
		array('../wp-includes/js/tinymce/themes/advanced/skins/default/img/progress.gif'),
		array('../wp-includes/js/tinymce/themes/advanced/skins/default/img/menu_check.gif'),
		array('../wp-includes/js/tinymce/themes/advanced/skins/default/img/menu_arrow.gif'),
		array('../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/drag.gif'),
		array('../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/corners.gif'),
		array('../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/buttons.gif'),
		array('../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/horizontal.gif'),
		array('../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/alert.gif'),
		array('../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/button.gif'),
		array('../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/confirm.gif'),
		array('../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/vertical.gif'),
		array('../wp-includes/js/tinymce/plugins/media/img/flash.gif'),
		array('../wp-includes/js/tinymce/plugins/media/img/flv_player.swf'),
		array('../wp-includes/js/tinymce/plugins/media/img/quicktime.gif'),
		array('../wp-includes/js/tinymce/plugins/media/img/realmedia.gif'),
		array('../wp-includes/js/tinymce/plugins/media/img/shockwave.gif'),
		array('../wp-includes/js/tinymce/plugins/media/img/windowsmedia.gif'),
		array('../wp-includes/js/tinymce/plugins/media/img/trans.gif'),
		array('../wp-includes/js/tinymce/plugins/spellchecker/img/wline.gif'),
		array('../wp-includes/js/tinymce/plugins/wordpress/img/more.gif'),
		array('../wp-includes/js/tinymce/plugins/wordpress/img/more_bug.gif'),
		array('../wp-includes/js/tinymce/plugins/wordpress/img/page.gif'),
		array('../wp-includes/js/tinymce/plugins/wordpress/img/page_bug.gif'),
		array('../wp-includes/js/tinymce/plugins/wordpress/img/toolbars.gif'),
		array('../wp-includes/js/tinymce/plugins/wordpress/img/help.gif'),
		array('../wp-includes/js/tinymce/plugins/wordpress/img/image.gif'),
		array('../wp-includes/js/tinymce/plugins/wordpress/img/media.gif'),
		array('../wp-includes/js/tinymce/plugins/wordpress/img/video.gif'),
		array('../wp-includes/js/tinymce/plugins/wordpress/img/audio.gif'),
		array('../wp-includes/js/tinymce/plugins/wpeditimage/img/image.png'),
		array('../wp-includes/js/tinymce/plugins/wpeditimage/img/delete.png'),
		array('../wp-includes/js/tinymce/plugins/wpgallery/img/delete.png'),
		array('../wp-includes/js/tinymce/plugins/wpgallery/img/edit.png'),
		array('../wp-includes/js/tinymce/plugins/wpgallery/img/gallery.png')
	);
	$files = array_merge($files, $mce);
	endif;

	return $files;
}
