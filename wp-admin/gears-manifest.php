<?php
@ require('../wp-config.php');

if ( ! defined('WP_ADMIN') )
	define('WP_ADMIN', true);

if ( ! is_a($wp_scripts, 'WP_Scripts') ) 
	$wp_scripts = new WP_Scripts();

$default_js = $version = '';
foreach ( $wp_scripts->scripts as $script ) {
	if ( empty($script->src) || strpos($script->src, 'tiny_mce_config.php') ) continue;
	$src = str_replace( '/wp-admin/', '', $script->src );
	$src = str_replace( '/wp-includes/', '../wp-includes/', $src );
	$default_js .= '{ "url" : "' . $src . '?ver=' . $script->ver . '" },' . "\n";
	$version .= $script->ver;
}

$version = md5($version);

nocache_headers();
header( 'Content-Type: application/x-javascript; charset=UTF-8' ); // application/json text/plain ?
?>
{
"betaManifestVersion" : 1,
"version" : "<?php echo $version; ?>_20080511",
"entries" : [
<?php echo $default_js; ?>

{ "url" : "wp-admin.css?version=2.6-bleeding" },
{ "url" : "rtl.css?version=2.6-bleeding" },
{ "url" : "../wp-includes/js/thickbox/thickbox.css?ver=20080430" },
{ "url" : "css/colors-classic-rtl.css?version=2.6-bleeding" },
{ "url" : "css/colors-classic.css?version=2.6-bleeding" },
{ "url" : "css/colors-fresh-rtl.css?version=2.6-bleeding" },
{ "url" : "css/colors-fresh.css?version=2.6-bleeding" },
{ "url" : "css/dashboard-rtl.css?version=2.6-bleeding" },
{ "url" : "css/dashboard.css?version=2.6-bleeding" },
{ "url" : "css/global.css?version=2.6-bleeding" },
{ "url" : "css/global-rtl.css?version=2.6-bleeding" },
{ "url" : "css/ie-rtl.css?version=2.6-bleeding" },
{ "url" : "css/ie.css?version=2.6-bleeding" },
{ "url" : "css/install-rtl.css?version=2.6-bleeding" },
{ "url" : "css/install.css?version=2.6-bleeding" },
{ "url" : "css/login-rtl.css?version=2.6-bleeding" },
{ "url" : "css/login.css?version=2.6-bleeding" },
{ "url" : "css/media-rtl.css?version=2.6-bleeding" },
{ "url" : "css/media.css?version=2.6-bleeding" },
{ "url" : "css/theme-editor-rtl.css?version=2.6-bleeding" },
{ "url" : "css/theme-editor.css?version=2.6-bleeding" },
{ "url" : "css/upload-rtl.css?version=2.6-bleeding" },
{ "url" : "css/widgets-rtl.css?version=2.6-bleeding" },
{ "url" : "css/widgets.css?version=2.6-bleeding" },

{ "url" : "images/align-center.png" },
{ "url" : "images/align-left.png" },
{ "url" : "images/align-none.png" },
{ "url" : "images/align-right.png" },
{ "url" : "images/bubble_bg.gif" },
{ "url" : "images/comment-grey-bubble.png" },
{ "url" : "images/comment-pill.gif" },
{ "url" : "images/comment-stalk-classic.gif" },
{ "url" : "images/comment-stalk-fresh.gif" },
{ "url" : "images/comment-stalk-rtl.gif" },
{ "url" : "images/date-button.gif" },
{ "url" : "images/fade-butt.png" },
{ "url" : "images/logo-ghost.png" },
{ "url" : "images/logo-login.gif" },
{ "url" : "images/media-button-gallery.gif" },
{ "url" : "images/media-button-image.gif" },
{ "url" : "images/media-button-music.gif" },
{ "url" : "images/media-button-other.gif" },
{ "url" : "images/media-button-video.gif" },
{ "url" : "images/media-buttons.gif" },
{ "url" : "images/tail.gif" },
{ "url" : "images/toggle-arrow-rtl.gif" },
{ "url" : "images/toggle-arrow.gif" },
{ "url" : "images/wordpress-logo.png" },
{ "url" : "images/xit.gif" },
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
{ "url" : "../wp-includes/js/thickbox/tb-close.png" },
{ "url" : "../wp-includes/js/swfupload/swfupload_f9.swf" },

{ "url" : "../wp-includes/js/tinymce/tiny_mce_popup.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/utils/mctabs.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/utils/validate.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/utils/form_utils.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/utils/editable_selects.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/js/pasteword.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/js/pastetext.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/js/media.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/color_picker.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/charmap.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/image.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/link.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/source_editor.js?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/js/anchor.js?v=307" },

{ "url" : "../wp-includes/js/tinymce/themes/advanced/source_editor.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/anchor.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/image.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/link.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/color_picker.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/charmap.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/media.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/pasteword.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/blank.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/pastetext.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/fullscreen/fullscreen.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/template.htm?v=307" },
{ "url" : "../wp-includes/js/tinymce/wp-mce-help.php?v=307" },

{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/ui.css?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/content.css?v=307" },
{ "url" : "../wp-includes/js/tinymce/themes/advanced/skins/wp_theme/dialog.css?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/inlinepopups/skins/clearlooks2/window.css?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/spellchecker/css/content.css?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/css/content.css?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/css/content.css?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/media/css/media.css?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/css/pasteword.css?v=307" },
{ "url" : "../wp-includes/js/tinymce/plugins/paste/css/blank.css?v=307" },
{ "url" : "../wp-includes/js/tinymce/wordpress.css?v=307" },

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
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/more.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/more_bug.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/page.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/page_bug.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/toolbars.gif" },
{ "url" : "../wp-includes/js/tinymce/plugins/wordpress/img/help.gif" }
]}
