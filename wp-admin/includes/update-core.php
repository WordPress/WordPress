<?php
/**
 * WordPress core upgrade functionality.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 2.7.0
 */

/**
 * Stores files to be deleted.
 *
 * @since 2.7.0
 * @global array $_old_files
 * @var array
 * @name $_old_files
 */
global $_old_files;

$_old_files = array(
// 2.0
'wp-admin/import-b2.php',
'wp-admin/import-blogger.php',
'wp-admin/import-greymatter.php',
'wp-admin/import-livejournal.php',
'wp-admin/import-mt.php',
'wp-admin/import-rss.php',
'wp-admin/import-textpattern.php',
'wp-admin/quicktags.js',
'wp-images/fade-butt.png',
'wp-images/get-firefox.png',
'wp-images/header-shadow.png',
'wp-images/smilies',
'wp-images/wp-small.png',
'wp-images/wpminilogo.png',
'wp.php',
// 2.0.8
'wp-includes/js/tinymce/plugins/inlinepopups/readme.txt',
// 2.1
'wp-admin/edit-form-ajax-cat.php',
'wp-admin/execute-pings.php',
'wp-admin/inline-uploading.php',
'wp-admin/link-categories.php',
'wp-admin/list-manipulation.js',
'wp-admin/list-manipulation.php',
'wp-includes/comment-functions.php',
'wp-includes/feed-functions.php',
'wp-includes/functions-compat.php',
'wp-includes/functions-formatting.php',
'wp-includes/functions-post.php',
'wp-includes/js/dbx-key.js',
'wp-includes/js/tinymce/plugins/autosave/langs/cs.js',
'wp-includes/js/tinymce/plugins/autosave/langs/sv.js',
'wp-includes/links.php',
'wp-includes/pluggable-functions.php',
'wp-includes/template-functions-author.php',
'wp-includes/template-functions-category.php',
'wp-includes/template-functions-general.php',
'wp-includes/template-functions-links.php',
'wp-includes/template-functions-post.php',
'wp-includes/wp-l10n.php',
// 2.2
'wp-admin/cat-js.php',
'wp-admin/import/b2.php',
'wp-includes/js/autosave-js.php',
'wp-includes/js/list-manipulation-js.php',
'wp-includes/js/wp-ajax-js.php',
// 2.3
'wp-admin/admin-db.php',
'wp-admin/cat.js',
'wp-admin/categories.js',
'wp-admin/custom-fields.js',
'wp-admin/dbx-admin-key.js',
'wp-admin/edit-comments.js',
'wp-admin/install-rtl.css',
'wp-admin/install.css',
'wp-admin/upgrade-schema.php',
'wp-admin/upload-functions.php',
'wp-admin/upload-rtl.css',
'wp-admin/upload.css',
'wp-admin/upload.js',
'wp-admin/users.js',
'wp-admin/widgets-rtl.css',
'wp-admin/widgets.css',
'wp-admin/xfn.js',
'wp-includes/js/tinymce/license.html',
// 2.5
'wp-admin/css/upload.css',
'wp-admin/images/box-bg-left.gif',
'wp-admin/images/box-bg-right.gif',
'wp-admin/images/box-bg.gif',
'wp-admin/images/box-butt-left.gif',
'wp-admin/images/box-butt-right.gif',
'wp-admin/images/box-butt.gif',
'wp-admin/images/box-head-left.gif',
'wp-admin/images/box-head-right.gif',
'wp-admin/images/box-head.gif',
'wp-admin/images/heading-bg.gif',
'wp-admin/images/login-bkg-bottom.gif',
'wp-admin/images/login-bkg-tile.gif',
'wp-admin/images/notice.gif',
'wp-admin/images/toggle.gif',
'wp-admin/includes/upload.php',
'wp-admin/js/dbx-admin-key.js',
'wp-admin/js/link-cat.js',
'wp-admin/profile-update.php',
'wp-admin/templates.php',
'wp-includes/images/wlw/WpComments.png',
'wp-includes/images/wlw/WpIcon.png',
'wp-includes/images/wlw/WpWatermark.png',
'wp-includes/js/dbx.js',
'wp-includes/js/fat.js',
'wp-includes/js/list-manipulation.js',
'wp-includes/js/tinymce/langs/en.js',
'wp-includes/js/tinymce/plugins/autosave/editor_plugin_src.js',
'wp-includes/js/tinymce/plugins/autosave/langs',
'wp-includes/js/tinymce/plugins/directionality/images',
'wp-includes/js/tinymce/plugins/directionality/langs',
'wp-includes/js/tinymce/plugins/inlinepopups/css',
'wp-includes/js/tinymce/plugins/inlinepopups/images',
'wp-includes/js/tinymce/plugins/inlinepopups/jscripts',
'wp-includes/js/tinymce/plugins/paste/images',
'wp-includes/js/tinymce/plugins/paste/jscripts',
'wp-includes/js/tinymce/plugins/paste/langs',
'wp-includes/js/tinymce/plugins/spellchecker/classes/HttpClient.class.php',
'wp-includes/js/tinymce/plugins/spellchecker/classes/TinyGoogleSpell.class.php',
'wp-includes/js/tinymce/plugins/spellchecker/classes/TinyPspell.class.php',
'wp-includes/js/tinymce/plugins/spellchecker/classes/TinyPspellShell.class.php',
'wp-includes/js/tinymce/plugins/spellchecker/css/spellchecker.css',
'wp-includes/js/tinymce/plugins/spellchecker/images',
'wp-includes/js/tinymce/plugins/spellchecker/langs',
'wp-includes/js/tinymce/plugins/spellchecker/tinyspell.php',
'wp-includes/js/tinymce/plugins/wordpress/images',
'wp-includes/js/tinymce/plugins/wordpress/langs',
'wp-includes/js/tinymce/plugins/wordpress/wordpress.css',
'wp-includes/js/tinymce/plugins/wphelp',
'wp-includes/js/tinymce/themes/advanced/css',
'wp-includes/js/tinymce/themes/advanced/images',
'wp-includes/js/tinymce/themes/advanced/jscripts',
'wp-includes/js/tinymce/themes/advanced/langs',
// 2.5.1
'wp-includes/js/tinymce/tiny_mce_gzip.php',
// 2.6
'wp-admin/bookmarklet.php',
'wp-includes/js/jquery/jquery.dimensions.min.js',
'wp-includes/js/tinymce/plugins/wordpress/popups.css',
'wp-includes/js/wp-ajax.js',
// 2.7
'wp-admin/css/press-this-ie-rtl.css',
'wp-admin/css/press-this-ie.css',
'wp-admin/css/upload-rtl.css',
'wp-admin/edit-form.php',
'wp-admin/images/comment-pill.gif',
'wp-admin/images/comment-stalk-classic.gif',
'wp-admin/images/comment-stalk-fresh.gif',
'wp-admin/images/comment-stalk-rtl.gif',
'wp-admin/images/del.png',
'wp-admin/images/gear.png',
'wp-admin/images/media-button-gallery.gif',
'wp-admin/images/media-buttons.gif',
'wp-admin/images/postbox-bg.gif',
'wp-admin/images/tab.png',
'wp-admin/images/tail.gif',
'wp-admin/js/forms.js',
'wp-admin/js/upload.js',
'wp-admin/link-import.php',
'wp-includes/images/audio.png',
'wp-includes/images/css.png',
'wp-includes/images/default.png',
'wp-includes/images/doc.png',
'wp-includes/images/exe.png',
'wp-includes/images/html.png',
'wp-includes/images/js.png',
'wp-includes/images/pdf.png',
'wp-includes/images/swf.png',
'wp-includes/images/tar.png',
'wp-includes/images/text.png',
'wp-includes/images/video.png',
'wp-includes/images/zip.png',
'wp-includes/js/tinymce/tiny_mce_config.php',
'wp-includes/js/tinymce/tiny_mce_ext.js',
// 2.8
'wp-admin/js/users.js',
'wp-includes/js/swfupload/plugins/swfupload.documentready.js',
'wp-includes/js/swfupload/plugins/swfupload.graceful_degradation.js',
'wp-includes/js/swfupload/swfupload_f9.swf',
'wp-includes/js/tinymce/plugins/autosave',
'wp-includes/js/tinymce/plugins/paste/css',
'wp-includes/js/tinymce/utils/mclayer.js',
'wp-includes/js/tinymce/wordpress.css',
// 2.8.5
'wp-admin/import/btt.php',
'wp-admin/import/jkw.php',
// 2.9
'wp-admin/js/page.dev.js',
'wp-admin/js/page.js',
'wp-admin/js/set-post-thumbnail-handler.dev.js',
'wp-admin/js/set-post-thumbnail-handler.js',
'wp-admin/js/slug.dev.js',
'wp-admin/js/slug.js',
'wp-includes/gettext.php',
'wp-includes/js/tinymce/plugins/wordpress/js',
'wp-includes/streams.php',
// MU
'README.txt',
'htaccess.dist',
'index-install.php',
'wp-admin/css/mu-rtl.css',
'wp-admin/css/mu.css',
'wp-admin/images/site-admin.png',
'wp-admin/includes/mu.php',
'wp-admin/wpmu-admin.php',
'wp-admin/wpmu-blogs.php',
'wp-admin/wpmu-edit.php',
'wp-admin/wpmu-options.php',
'wp-admin/wpmu-themes.php',
'wp-admin/wpmu-upgrade-site.php',
'wp-admin/wpmu-users.php',
'wp-includes/images/wordpress-mu.png',
'wp-includes/wpmu-default-filters.php',
'wp-includes/wpmu-functions.php',
'wpmu-settings.php',
// 3.0
'wp-admin/categories.php',
'wp-admin/edit-category-form.php',
'wp-admin/edit-page-form.php',
'wp-admin/edit-pages.php',
'wp-admin/images/admin-header-footer.png',
'wp-admin/images/browse-happy.gif',
'wp-admin/images/ico-add.png',
'wp-admin/images/ico-close.png',
'wp-admin/images/ico-edit.png',
'wp-admin/images/ico-viewpage.png',
'wp-admin/images/fav-top.png',
'wp-admin/images/screen-options-left.gif',
'wp-admin/images/wp-logo-vs.gif',
'wp-admin/images/wp-logo.gif',
'wp-admin/import',
'wp-admin/js/wp-gears.dev.js',
'wp-admin/js/wp-gears.js',
'wp-admin/options-misc.php',
'wp-admin/page-new.php',
'wp-admin/page.php',
'wp-admin/rtl.css',
'wp-admin/rtl.dev.css',
'wp-admin/update-links.php',
'wp-admin/wp-admin.css',
'wp-admin/wp-admin.dev.css',
'wp-includes/js/codepress',
'wp-includes/js/codepress/engines/khtml.js',
'wp-includes/js/codepress/engines/older.js',
'wp-includes/js/jquery/autocomplete.dev.js',
'wp-includes/js/jquery/autocomplete.js',
'wp-includes/js/jquery/interface.js',
'wp-includes/js/scriptaculous/prototype.js',
'wp-includes/js/tinymce/wp-tinymce.js',
// 3.1
'wp-admin/edit-attachment-rows.php',
'wp-admin/edit-link-categories.php',
'wp-admin/edit-link-category-form.php',
'wp-admin/edit-post-rows.php',
'wp-admin/images/button-grad-active-vs.png',
'wp-admin/images/button-grad-vs.png',
'wp-admin/images/fav-arrow-vs-rtl.gif',
'wp-admin/images/fav-arrow-vs.gif',
'wp-admin/images/fav-top-vs.gif',
'wp-admin/images/list-vs.png',
'wp-admin/images/screen-options-right-up.gif',
'wp-admin/images/screen-options-right.gif',
'wp-admin/images/visit-site-button-grad-vs.gif',
'wp-admin/images/visit-site-button-grad.gif',
'wp-admin/link-category.php',
'wp-admin/sidebar.php',
'wp-includes/classes.php',
'wp-includes/js/tinymce/blank.htm',
'wp-includes/js/tinymce/plugins/media/css/content.css',
'wp-includes/js/tinymce/plugins/media/img',
'wp-includes/js/tinymce/plugins/safari',
// 3.2
'wp-admin/images/logo-login.gif',
'wp-admin/images/star.gif',
'wp-admin/js/list-table.dev.js',
'wp-admin/js/list-table.js',
'wp-includes/default-embeds.php',
'wp-includes/js/tinymce/plugins/wordpress/img/help.gif',
'wp-includes/js/tinymce/plugins/wordpress/img/more.gif',
'wp-includes/js/tinymce/plugins/wordpress/img/toolbars.gif',
'wp-includes/js/tinymce/themes/advanced/img/fm.gif',
'wp-includes/js/tinymce/themes/advanced/img/sflogo.png',
// 3.3
'wp-admin/css/colors-classic-rtl.css',
'wp-admin/css/colors-classic-rtl.dev.css',
'wp-admin/css/colors-fresh-rtl.css',
'wp-admin/css/colors-fresh-rtl.dev.css',
'wp-admin/css/dashboard-rtl.css',
'wp-admin/css/dashboard-rtl.dev.css',
'wp-admin/css/dashboard.css',
'wp-admin/css/dashboard.dev.css',
'wp-admin/css/farbtastic-rtl.css',
'wp-admin/css/global-rtl.css',
'wp-admin/css/global-rtl.dev.css',
'wp-admin/css/global.css',
'wp-admin/css/global.dev.css',
'wp-admin/css/install-rtl.css',
'wp-admin/css/install-rtl.dev.css',
'wp-admin/css/login-rtl.css',
'wp-admin/css/login-rtl.dev.css',
'wp-admin/css/login.css',
'wp-admin/css/login.dev.css',
'wp-admin/css/ms.css',
'wp-admin/css/ms.dev.css',
'wp-admin/css/nav-menu-rtl.css',
'wp-admin/css/nav-menu-rtl.dev.css',
'wp-admin/css/nav-menu.css',
'wp-admin/css/nav-menu.dev.css',
'wp-admin/css/plugin-install-rtl.css',
'wp-admin/css/plugin-install-rtl.dev.css',
'wp-admin/css/plugin-install.css',
'wp-admin/css/plugin-install.dev.css',
'wp-admin/css/press-this-rtl.css',
'wp-admin/css/press-this-rtl.dev.css',
'wp-admin/css/press-this.css',
'wp-admin/css/press-this.dev.css',
'wp-admin/css/theme-editor-rtl.css',
'wp-admin/css/theme-editor-rtl.dev.css',
'wp-admin/css/theme-editor.css',
'wp-admin/css/theme-editor.dev.css',
'wp-admin/css/theme-install-rtl.css',
'wp-admin/css/theme-install-rtl.dev.css',
'wp-admin/css/theme-install.css',
'wp-admin/css/theme-install.dev.css',
'wp-admin/css/widgets-rtl.css',
'wp-admin/css/widgets-rtl.dev.css',
'wp-admin/css/widgets.css',
'wp-admin/css/widgets.dev.css',
'wp-admin/includes/internal-linking.php',
'wp-includes/images/admin-bar-sprite-rtl.png',
'wp-includes/js/jquery/ui.button.js',
'wp-includes/js/jquery/ui.core.js',
'wp-includes/js/jquery/ui.dialog.js',
'wp-includes/js/jquery/ui.draggable.js',
'wp-includes/js/jquery/ui.droppable.js',
'wp-includes/js/jquery/ui.mouse.js',
'wp-includes/js/jquery/ui.position.js',
'wp-includes/js/jquery/ui.resizable.js',
'wp-includes/js/jquery/ui.selectable.js',
'wp-includes/js/jquery/ui.sortable.js',
'wp-includes/js/jquery/ui.tabs.js',
'wp-includes/js/jquery/ui.widget.js',
'wp-includes/js/l10n.dev.js',
'wp-includes/js/l10n.js',
'wp-includes/js/tinymce/plugins/wplink/css',
'wp-includes/js/tinymce/plugins/wplink/img',
'wp-includes/js/tinymce/plugins/wplink/js',
'wp-includes/js/tinymce/themes/advanced/img/wpicons.png',
'wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/butt2.png',
'wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/button_bg.png',
'wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/down_arrow.gif',
'wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/fade-butt.png',
'wp-includes/js/tinymce/themes/advanced/skins/wp_theme/img/separator.gif',
// Don't delete, yet: 'wp-rss.php',
// Don't delete, yet: 'wp-rdf.php',
// Don't delete, yet: 'wp-rss2.php',
// Don't delete, yet: 'wp-commentsrss2.php',
// Don't delete, yet: 'wp-atom.php',
// Don't delete, yet: 'wp-feed.php',
// 3.4
'wp-admin/images/gray-star.png',
'wp-admin/images/logo-login.png',
'wp-admin/images/star.png',
'wp-admin/index-extra.php',
'wp-admin/network/index-extra.php',
'wp-admin/user/index-extra.php',
'wp-admin/images/screenshots/admin-flyouts.png',
'wp-admin/images/screenshots/coediting.png',
'wp-admin/images/screenshots/drag-and-drop.png',
'wp-admin/images/screenshots/help-screen.png',
'wp-admin/images/screenshots/media-icon.png',
'wp-admin/images/screenshots/new-feature-pointer.png',
'wp-admin/images/screenshots/welcome-screen.png',
'wp-includes/css/editor-buttons.css',
'wp-includes/css/editor-buttons.dev.css',
'wp-includes/js/tinymce/plugins/paste/blank.htm',
'wp-includes/js/tinymce/plugins/wordpress/css',
'wp-includes/js/tinymce/plugins/wordpress/editor_plugin.dev.js',
'wp-includes/js/tinymce/plugins/wordpress/img/embedded.png',
'wp-includes/js/tinymce/plugins/wordpress/img/more_bug.gif',
'wp-includes/js/tinymce/plugins/wordpress/img/page_bug.gif',
'wp-includes/js/tinymce/plugins/wpdialogs/editor_plugin.dev.js',
'wp-includes/js/tinymce/plugins/wpeditimage/css/editimage-rtl.css',
'wp-includes/js/tinymce/plugins/wpeditimage/editor_plugin.dev.js',
'wp-includes/js/tinymce/plugins/wpfullscreen/editor_plugin.dev.js',
'wp-includes/js/tinymce/plugins/wpgallery/editor_plugin.dev.js',
'wp-includes/js/tinymce/plugins/wpgallery/img/gallery.png',
'wp-includes/js/tinymce/plugins/wplink/editor_plugin.dev.js',
// Don't delete, yet: 'wp-pass.php',
// Don't delete, yet: 'wp-register.php',
);

/**
 * Stores new files in wp-content to copy
 *
 * The contents of this array indicate any new bundled plugins/themes which
 * should be installed with the WordPress Upgrade. These items will not be
 * re-installed in future upgrades, this behaviour is controlled by the
 * introduced version present here being older than the current installed version.
 *
 * The content of this array should follow the following format:
 * Filename (relative to wp-content) => Introduced version
 * Directories should be noted by suffixing it with a trailing slash (/)
 *
 * @since 3.2.0
 * @global array $_new_bundled_files
 * @var array
 * @name $_new_bundled_files
 */
global $_new_bundled_files;

$_new_bundled_files = array(
'plugins/akismet/' => '2.0',
'themes/twentyten/' => '3.0',
'themes/twentyeleven/' => '3.2'
);

/**
 * Upgrade the core of WordPress.
 *
 * This will create a .maintenance file at the base of the WordPress directory
 * to ensure that people can not access the web site, when the files are being
 * copied to their locations.
 *
 * The files in the {@link $_old_files} list will be removed and the new files
 * copied from the zip file after the database is upgraded.
 *
 * The files in the {@link $_new_bundled_files} list will be added to the installation
 * if the version is greater than or equal to the old version being upgraded.
 *
 * The steps for the upgrader for after the new release is downloaded and
 * unzipped is:
 *   1. Test unzipped location for select files to ensure that unzipped worked.
 *   2. Create the .maintenance file in current WordPress base.
 *   3. Copy new WordPress directory over old WordPress files.
 *   4. Upgrade WordPress to new version.
 *     4.1. Copy all files/folders other than wp-content
 *     4.2. Copy any language files to WP_LANG_DIR (which may differ from WP_CONTENT_DIR
 *     4.3. Copy any new bundled themes/plugins to their respective locations
 *   5. Delete new WordPress directory path.
 *   6. Delete .maintenance file.
 *   7. Remove old files.
 *   8. Delete 'update_core' option.
 *
 * There are several areas of failure. For instance if PHP times out before step
 * 6, then you will not be able to access any portion of your site. Also, since
 * the upgrade will not continue where it left off, you will not be able to
 * automatically remove old files and remove the 'update_core' option. This
 * isn't that bad.
 *
 * If the copy of the new WordPress over the old fails, then the worse is that
 * the new WordPress directory will remain.
 *
 * If it is assumed that every file will be copied over, including plugins and
 * themes, then if you edit the default theme, you should rename it, so that
 * your changes remain.
 *
 * @since 2.7.0
 *
 * @param string $from New release unzipped path.
 * @param string $to Path to old WordPress installation.
 * @return WP_Error|null WP_Error on failure, null on success.
 */
function update_core($from, $to) {
	global $wp_filesystem, $_old_files, $_new_bundled_files, $wpdb;

	@set_time_limit( 300 );

	$php_version    = phpversion();
	$mysql_version  = $wpdb->db_version();
	$required_php_version = '5.2.4';
	$required_mysql_version = '5.0';
	$wp_version = '3.4.1';
	$php_compat     = version_compare( $php_version, $required_php_version, '>=' );
	if ( file_exists( WP_CONTENT_DIR . '/db.php' ) && empty( $wpdb->is_mysql ) )
		$mysql_compat = true;
	else
		$mysql_compat = version_compare( $mysql_version, $required_mysql_version, '>=' );

	if ( !$mysql_compat || !$php_compat )
		$wp_filesystem->delete($from, true);

	if ( !$mysql_compat && !$php_compat )
		return new WP_Error( 'php_mysql_not_compatible', sprintf( __('The update cannot be installed because WordPress %1$s requires PHP version %2$s or higher and MySQL version %3$s or higher. You are running PHP version %4$s and MySQL version %5$s.'), $wp_version, $required_php_version, $required_mysql_version, $php_version, $mysql_version ) );
	elseif ( !$php_compat )
		return new WP_Error( 'php_not_compatible', sprintf( __('The update cannot be installed because WordPress %1$s requires PHP version %2$s or higher. You are running version %3$s.'), $wp_version, $required_php_version, $php_version ) );
	elseif ( !$mysql_compat )
		return new WP_Error( 'mysql_not_compatible', sprintf( __('The update cannot be installed because WordPress %1$s requires MySQL version %2$s or higher. You are running version %3$s.'), $wp_version, $required_mysql_version, $mysql_version ) );

	// Sanity check the unzipped distribution
	apply_filters('update_feedback', __('Verifying the unpacked files&#8230;'));
	$distro = '';
	$roots = array( '/wordpress/', '/wordpress-mu/' );
	foreach( $roots as $root ) {
		if ( $wp_filesystem->exists($from . $root . 'readme.html') && $wp_filesystem->exists($from . $root . 'wp-includes/version.php') ) {
			$distro = $root;
			break;
		}
	}
	if ( !$distro ) {
		$wp_filesystem->delete($from, true);
		return new WP_Error('insane_distro', __('The update could not be unpacked') );
	}

	apply_filters('update_feedback', __('Installing the latest version&#8230;'));

	// Create maintenance file to signal that we are upgrading
	$maintenance_string = '<?php $upgrading = ' . time() . '; ?>';
	$maintenance_file = $to . '.maintenance';
	$wp_filesystem->delete($maintenance_file);
	$wp_filesystem->put_contents($maintenance_file, $maintenance_string, FS_CHMOD_FILE);

	// Copy new versions of WP files into place.
	$result = _copy_dir($from . $distro, $to, array('wp-content') );

	// Custom Content Directory needs updating now.
	// Copy Languages
	if ( !is_wp_error($result) && $wp_filesystem->is_dir($from . $distro . 'wp-content/languages') ) {
		if ( WP_LANG_DIR != ABSPATH . WPINC . '/languages' || @is_dir(WP_LANG_DIR) )
			$lang_dir = WP_LANG_DIR;
		else
			$lang_dir = WP_CONTENT_DIR . '/languages';

		if ( !@is_dir($lang_dir) && 0 === strpos($lang_dir, ABSPATH) ) { // Check the language directory exists first
			$wp_filesystem->mkdir($to . str_replace(ABSPATH, '', $lang_dir), FS_CHMOD_DIR); // If it's within the ABSPATH we can handle it here, otherwise they're out of luck.
			clearstatcache(); // for FTP, Need to clear the stat cache
		}

		if ( @is_dir($lang_dir) ) {
			$wp_lang_dir = $wp_filesystem->find_folder($lang_dir);
			if ( $wp_lang_dir )
				$result = copy_dir($from . $distro . 'wp-content/languages/', $wp_lang_dir);
		}
	}

	// Copy New bundled plugins & themes
	// This gives us the ability to install new plugins & themes bundled with future versions of WordPress whilst avoiding the re-install upon upgrade issue.
	if ( !is_wp_error($result) && ( ! defined('CORE_UPGRADE_SKIP_NEW_BUNDLED') || ! CORE_UPGRADE_SKIP_NEW_BUNDLED ) ) {
		$old_version = $GLOBALS['wp_version']; // $wp_version in local scope == new version
		foreach ( (array) $_new_bundled_files as $file => $introduced_version ) {
			// If $introduced version is greater than what the site was previously running
			if ( version_compare($introduced_version, $old_version, '>') ) {
				$directory = ('/' == $file[ strlen($file)-1 ]);
				list($type, $filename) = explode('/', $file, 2);

				if ( 'plugins' == $type )
					$dest = $wp_filesystem->wp_plugins_dir();
				elseif ( 'themes' == $type )
					$dest = trailingslashit($wp_filesystem->wp_themes_dir()); // Back-compat, ::wp_themes_dir() did not return trailingslash'd pre-3.2
				else
					continue;

				if ( ! $directory ) {
					if ( $wp_filesystem->exists($dest . $filename) )
						continue;

					if ( ! $wp_filesystem->copy($from . $distro . 'wp-content/' . $file, $dest . $filename, FS_CHMOD_FILE) )
						$result = new WP_Error('copy_failed', __('Could not copy file.'), $dest . $filename);
				} else {
					if ( $wp_filesystem->is_dir($dest . $filename) )
						continue;

					$wp_filesystem->mkdir($dest . $filename, FS_CHMOD_DIR);
					$_result = copy_dir( $from . $distro . 'wp-content/' . $file, $dest . $filename);
					if ( is_wp_error($_result) ) //If a error occurs partway through this final step, keep the error flowing through, but keep process going.
						$result = $_result;
				}
			}
		} //end foreach
	}

	// Handle $result error from the above blocks
	if ( is_wp_error($result) ) {
		$wp_filesystem->delete($maintenance_file);
		$wp_filesystem->delete($from, true);
		return $result;
	}

	// Remove old files
	foreach ( $_old_files as $old_file ) {
		$old_file = $to . $old_file;
		if ( !$wp_filesystem->exists($old_file) )
			continue;
		$wp_filesystem->delete($old_file, true);
	}

	// Upgrade DB with separate request
	apply_filters('update_feedback', __('Upgrading database&#8230;'));
	$db_upgrade_url = admin_url('upgrade.php?step=upgrade_db');
	wp_remote_post($db_upgrade_url, array('timeout' => 60));

	// Remove working directory
	$wp_filesystem->delete($from, true);

	// Force refresh of update information
	if ( function_exists('delete_site_transient') )
		delete_site_transient('update_core');
	else
		delete_option('update_core');

	// Remove maintenance file, we're done.
	$wp_filesystem->delete($maintenance_file);

	// If we made it this far:
	do_action( '_core_updated_successfully', $wp_version );

	return $wp_version;
}

/**
 * Copies a directory from one location to another via the WordPress Filesystem Abstraction.
 * Assumes that WP_Filesystem() has already been called and setup.
 *
 * This is a temporary function for the 3.1 -> 3.2 upgrade only and will be removed in 3.3
 *
 * @ignore
 * @since 3.2.0
 * @see copy_dir()
 *
 * @param string $from source directory
 * @param string $to destination directory
 * @param array $skip_list a list of files/folders to skip copying
 * @return mixed WP_Error on failure, True on success.
 */
function _copy_dir($from, $to, $skip_list = array() ) {
	global $wp_filesystem;

	$dirlist = $wp_filesystem->dirlist($from);

	$from = trailingslashit($from);
	$to = trailingslashit($to);

	$skip_regex = '';
	foreach ( (array)$skip_list as $key => $skip_file )
		$skip_regex .= preg_quote($skip_file, '!') . '|';

	if ( !empty($skip_regex) )
		$skip_regex = '!(' . rtrim($skip_regex, '|') . ')$!i';

	foreach ( (array) $dirlist as $filename => $fileinfo ) {
		if ( !empty($skip_regex) )
			if ( preg_match($skip_regex, $from . $filename) )
				continue;

		if ( 'f' == $fileinfo['type'] ) {
			if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true, FS_CHMOD_FILE) ) {
				// If copy failed, chmod file to 0644 and try again.
				$wp_filesystem->chmod($to . $filename, 0644);
				if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true, FS_CHMOD_FILE) )
					return new WP_Error('copy_failed', __('Could not copy file.'), $to . $filename);
			}
		} elseif ( 'd' == $fileinfo['type'] ) {
			if ( !$wp_filesystem->is_dir($to . $filename) ) {
				if ( !$wp_filesystem->mkdir($to . $filename, FS_CHMOD_DIR) )
					return new WP_Error('mkdir_failed', __('Could not create directory.'), $to . $filename);
			}
			$result = _copy_dir($from . $filename, $to . $filename, $skip_list);
			if ( is_wp_error($result) )
				return $result;
		}
	}
	return true;
}

/**
 * Redirect to the About WordPress page after a successful upgrade.
 *
 * This function is only needed when the existing install is older than 3.4.0.
 *
 * @since 3.3.0
 *
 */
function _redirect_to_about_wordpress( $new_version ) {
	global $wp_version, $pagenow, $action;

	if ( version_compare( $wp_version, '3.4-RC1', '>=' ) )
		return;

	// Ensure we only run this on the update-core.php page. wp_update_core() could be called in other contexts.
	if ( 'update-core.php' != $pagenow )
		return;

 	if ( 'do-core-upgrade' != $action && 'do-core-reinstall' != $action )
 		return;

	// Load the updated default text localization domain for new strings
	load_default_textdomain();

	// See do_core_upgrade()
	show_message( __('WordPress updated successfully') );
	show_message( '<span class="hide-if-no-js">' . sprintf( __( 'Welcome to WordPress %1$s. You will be redirected to the About WordPress screen. If not, click <a href="%s">here</a>.' ), $new_version, esc_url( self_admin_url( 'about.php?updated' ) ) ) . '</span>' );
	show_message( '<span class="hide-if-js">' . sprintf( __( 'Welcome to WordPress %1$s. <a href="%2$s">Learn more</a>.' ), $new_version, esc_url( self_admin_url( 'about.php?updated' ) ) ) . '</span>' );
	echo '</div>';
	?>
<script type="text/javascript">
window.location = '<?php echo self_admin_url( 'about.php?updated' ); ?>';
</script>
	<?php

	// Include admin-footer.php and exit
	include(ABSPATH . 'wp-admin/admin-footer.php');
	exit();
}
add_action( '_core_updated_successfully', '_redirect_to_about_wordpress' );
