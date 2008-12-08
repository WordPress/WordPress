<?php
/**
 * Defines the Gears manifest file for Google Gears offline storage.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Set ABSPATH for execution */
define( 'ABSPATH', dirname(dirname(__FILE__)) );
define( 'WPINC', '/wp-includes' );

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

$wp_scripts = new WP_Scripts();
wp_default_scripts($wp_scripts);

$wp_styles = new WP_Styles();
wp_default_styles($wp_styles);

$defaults = $man_version = '';
foreach ( $wp_scripts->registered as $script ) {
	if ( empty($script->src) ) continue;
	$ver = empty($script->ver) ? $wp_version : $script->ver;
	if ( 'editor' == $script->handle ) $mce_ver = $script->ver;
	$src = str_replace( array( '/wp-admin/', '/wp-includes/' ), array( '', '../wp-includes/' ), $script->src );
	$defaults .= '{ "url" : "' . $src . '?ver=' . $ver . '" },' . "\n";
	$man_version .= $ver;
}

foreach ( $wp_styles->registered as $style ) {
	if ( empty($style->src) ) continue;

	$ver = empty($style->ver) ? $wp_version : $style->ver;
	$src = str_replace( array( '/wp-admin/', '/wp-includes/' ), array( '', '../wp-includes/' ), $style->src );
	if ( 'colors' == $style->handle ) $src = 'css/colors-classic.css';
	$defaults .= '{ "url" : "' . $src . '?ver=' . $ver . '" },' . "\n";

	if ( isset($style->extra['rtl']) && $style->extra['rtl'] ) {
		if ( is_bool( $style->extra['rtl'] ) )
			$rtl_href = str_replace( '.css', '-rtl.css', $src );
		else
			$rtl_href = str_replace( array( '/wp-admin/', '/wp-includes/' ), array( '', '../wp-includes/' ), $style->extra['rtl'] );

		$defaults .= '{ "url" : "' . $rtl_href . '?ver=' . $ver . '" },' . "\n";
	}
	$man_version .= $ver;
}

$man_version = md5($man_version);

header( 'Expires: Wed, 11 Jan 1984 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-cache, must-revalidate, max-age=0' );
header( 'Pragma: no-cache' );
header( 'Content-Type: application/x-javascript; charset=UTF-8' );
?>
{
"betaManifestVersion" : 1,
"version" : "<?php echo $man_version; ?>_20081201",
"entries" : [
<?php echo $defaults; ?>

{ "url" : "images/align-center.png" },
{ "url" : "images/align-left.png" },
{ "url" : "images/align-none.png" },
{ "url" : "images/align-right.png" },
{ "url" : "images/archive-link.png" },
{ "url" : "images/blue-grad.png" },
{ "url" : "images/browse-happy.gif" },
{ "url" : "images/bubble_bg.gif" },
{ "url" : "images/bubble_bg-rtl.gif" },
{ "url" : "images/button-grad.png" },
{ "url" : "images/button-grad-active.png" },
{ "url" : "images/comment-grey-bubble.png" },
{ "url" : "images/date-button.gif" },
{ "url" : "images/ed-bg.gif" },
{ "url" : "images/fade-butt.png" },
{ "url" : "images/fav.png" },
{ "url" : "images/fav-arrow.gif" },
{ "url" : "images/fav-arrow-rtl.gif" },
{ "url" : "images/fav-top.png" },
{ "url" : "images/generic.png" },
{ "url" : "images/gray-grad.png" },
{ "url" : "images/icons32.png" },
{ "url" : "images/icons32-vs.png" },
{ "url" : "images/list.png" },
{ "url" : "images/list-vs.png" },
{ "url" : "images/loading.gif" },
{ "url" : "images/loading-publish.gif" },
{ "url" : "images/logo.gif" },
{ "url" : "images/logo-ghost.png" },
{ "url" : "images/logo-login.gif" },
{ "url" : "images/media-button-image.gif" },
{ "url" : "images/media-button-music.gif" },
{ "url" : "images/media-button-other.gif" },
{ "url" : "images/media-button-video.gif" },
{ "url" : "images/menu.png" },
{ "url" : "images/menu-vs.png" },
{ "url" : "images/menu-arrows.gif" },
{ "url" : "images/menu-bits.gif" },
{ "url" : "images/menu-bits-rtl.gif" },
{ "url" : "images/menu-dark.gif" },
{ "url" : "images/menu-dark-rtl.gif" },
{ "url" : "images/no.png" },
{ "url" : "images/required.gif" },
{ "url" : "images/resize.gif" },
{ "url" : "images/screen-options-left.gif" },
{ "url" : "images/screen-options-right.gif" },
{ "url" : "images/screen-options-right-up.gif" },
{ "url" : "images/se.png" },
{ "url" : "images/star.gif" },
{ "url" : "images/toggle-arrow.gif" },
{ "url" : "images/toggle-arrow-rtl.gif" },
{ "url" : "images/white-grad.png" },
{ "url" : "images/white-grad-active.png" },
{ "url" : "images/wordpress-logo.png" },
{ "url" : "images/wp-logo.gif" },
{ "url" : "images/xit.gif" },
{ "url" : "images/yes.png" },

<?php if ( is_file('../wp-includes/js/tinymce/tiny_mce.js') ) { ?>
{ "url" : "../wp-includes/js/tinymce/tiny_mce.js", "src" : "../wp-includes/js/tinymce/tiny_mce.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/langs/wp-langs-en.js", "src" : "../wp-includes/js/tinymce/langs/wp-langs-en.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/wordpress.css", "src" : "../wp-includes/js/tinymce/wordpress.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/utils/mctabs.js", "src" : "../wp-includes/js/tinymce/utils/mctabs.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/utils/validate.js", "src" : "../wp-includes/js/tinymce/utils/validate.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/utils/form_utils.js", "src" : "../wp-includes/js/tinymce/utils/form_utils.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/utils/editable_selects.js", "src" : "../wp-includes/js/tinymce/utils/editable_selects.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/tiny_mce_popup.js", "src" : "../wp-includes/js/tinymce/tiny_mce_popup.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/themes/advanced/editor_template.js", "src" : "../wp-includes/js/tinymce/themes/advanced/editor_template.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/source_editor.htm", "src" : "../wp-includes/js/tinymce/themes/advanced/source_editor.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/anchor.htm", "src" : "../wp-includes/js/tinymce/themes/advanced/anchor.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/image.htm", "src" : "../wp-includes/js/tinymce/themes/advanced/image.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/link.htm", "src" : "../wp-includes/js/tinymce/themes/advanced/link.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/color_picker.htm", "src" : "../wp-includes/js/tinymce/themes/advanced/color_picker.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/charmap.htm", "src" : "../wp-includes/js/tinymce/themes/advanced/charmap.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/color_picker.js", "src" : "../wp-includes/js/tinymce/themes/advanced/js/color_picker.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/charmap.js", "src" : "../wp-includes/js/tinymce/themes/advanced/js/charmap.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/image.js", "src" : "../wp-includes/js/tinymce/themes/advanced/js/image.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/link.js", "src" : "../wp-includes/js/tinymce/themes/advanced/js/link.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/source_editor.js", "src" : "../wp-includes/js/tinymce/themes/advanced/js/source_editor.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/anchor.js", "src" : "../wp-includes/js/tinymce/themes/advanced/js/anchor.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/ui.css", "src" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/ui.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/content.css", "src" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/content.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/dialog.css", "src" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/dialog.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/plugins/autosave/editor_plugin.js", "src" : "../wp-includes/js/tinymce/plugins/autosave/editor_plugin.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/plugins/fullscreen/editor_plugin.js", "src" : "../wp-includes/js/tinymce/plugins/fullscreen/editor_plugin.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/fullscreen/fullscreen.htm", "src" : "../wp-includes/js/tinymce/plugins/fullscreen/fullscreen.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/editor_plugin.js", "src" : "../wp-includes/js/tinymce/plugins/inlinepopups/editor_plugin.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/template.htm", "src" : "../wp-includes/js/tinymce/plugins/inlinepopups/template.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/window.css", "src" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/window.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/plugins/media/editor_plugin.js", "src" : "../wp-includes/js/tinymce/plugins/media/editor_plugin.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/media/js/media.js", "src" : "../wp-includes/js/tinymce/plugins/media/js/media.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/media/media.htm", "src" : "../wp-includes/js/tinymce/plugins/media/media.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/media/css/content.css", "src" : "../wp-includes/js/tinymce/plugins/media/css/content.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/media/css/media.css", "src" : "../wp-includes/js/tinymce/plugins/media/css/media.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/plugins/paste/editor_plugin.js", "src" : "../wp-includes/js/tinymce/plugins/paste/editor_plugin.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/js/pasteword.js", "src" : "../wp-includes/js/tinymce/plugins/paste/js/pasteword.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/js/pastetext.js", "src" : "../wp-includes/js/tinymce/plugins/paste/js/pastetext.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/pasteword.htm", "src" : "../wp-includes/js/tinymce/plugins/paste/pasteword.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/blank.htm", "src" : "../wp-includes/js/tinymce/plugins/paste/blank.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/pastetext.htm", "src" : "../wp-includes/js/tinymce/plugins/paste/pastetext.htm?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/css/pasteword.css", "src" : "../wp-includes/js/tinymce/plugins/paste/css/pasteword.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/css/blank.css", "src" : "../wp-includes/js/tinymce/plugins/paste/css/blank.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/plugins/safari/editor_plugin.js", "src" : "../wp-includes/js/tinymce/plugins/safari/editor_plugin.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/plugins/spellchecker/editor_plugin.js", "src" : "../wp-includes/js/tinymce/plugins/spellchecker/editor_plugin.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/spellchecker/css/content.css", "src" : "../wp-includes/js/tinymce/plugins/spellchecker/css/content.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/editor_plugin.js", "src" : "../wp-includes/js/tinymce/plugins/wordpress/editor_plugin.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/css/content.css", "src" : "../wp-includes/js/tinymce/plugins/wordpress/css/content.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/plugins/wpeditimage/editor_plugin.js", "src" : "../wp-includes/js/tinymce/plugins/wpeditimage/editor_plugin.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/wpeditimage/editimage.html", "src" : "../wp-includes/js/tinymce/plugins/wpeditimage/editimage.html?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/wpeditimage/js/editimage.js", "src" : "../wp-includes/js/tinymce/plugins/wpeditimage/js/editimage.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/wpeditimage/css/editimage.css", "src" : "../wp-includes/js/tinymce/plugins/wpeditimage/css/editimage.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },
{ "url" : "../wp-includes/js/tinymce/plugins/wpeditimage/css/editimage-rtl.css", "src" : "../wp-includes/js/tinymce/plugins/wpeditimage/css/editimage-rtl.css?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/plugins/wpgallery/editor_plugin.js", "src" : "../wp-includes/js/tinymce/plugins/wpgallery/editor_plugin.js?ver=<?php echo $mce_ver; ?>", "ignoreQuery" : true },

{ "url" : "../wp-includes/js/tinymce/themes/advanced/img/icons.gif" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/img/colorpicker.jpg" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/img/fm.gif" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/img/gotmoxie.png" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/img/sflogo.png" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/butt2.png" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/fade-butt.png" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/tabs.gif" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/down_arrow.gif" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/default/img/progress.gif" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/default/img/menu_check.gif" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/default/img/menu_arrow.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/drag.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/corners.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/buttons.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/horizontal.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/alert.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/button.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/confirm.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/img/vertical.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/img/flash.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/img/flv_player.swf" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/img/quicktime.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/img/realmedia.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/img/shockwave.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/img/windowsmedia.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/img/trans.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/spellchecker/img/wline.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/more.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/more_bug.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/page.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/page_bug.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/toolbars.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/help.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/image.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/media.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/video.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/audio.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wpeditimage/img/image.png" },
{ "url" : "../wp-includes/js/tinymce/plugins/wpeditimage/img/delete.png" },
{ "url" : "../wp-includes/js/tinymce/plugins/wpgallery/img/delete.png" },
{ "url" : "../wp-includes/js/tinymce/plugins/wpgallery/img/edit.png" },
{ "url" : "../wp-includes/js/tinymce/plugins/wpgallery/img/gallery.png" },
<?php } ?>

{ "url" : "../wp-includes/images/crystal/archive.png" },
{ "url" : "../wp-includes/images/crystal/audio.png" },
{ "url" : "../wp-includes/images/crystal/code.png" },
{ "url" : "../wp-includes/images/crystal/default.png" },
{ "url" : "../wp-includes/images/crystal/document.png" },
{ "url" : "../wp-includes/images/crystal/interactive.png" },
{ "url" : "../wp-includes/images/crystal/text.png" },
{ "url" : "../wp-includes/images/crystal/video.png" },
{ "url" : "../wp-includes/images/crystal/spreadsheet.png" },
{ "url" : "../wp-includes/images/rss.png" },
{ "url" : "../wp-includes/js/thickbox/loadingAnimation.gif" },
{ "url" : "../wp-includes/js/thickbox/tb-close.png" }
]}
