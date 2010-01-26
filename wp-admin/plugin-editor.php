<?php
/**
 * Edit plugin editor administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

if ( !current_user_can('edit_plugins') )
	wp_die('<p>'.__('You do not have sufficient permissions to edit plugins for this blog.').'</p>');

$title = __("Edit Plugins");
$parent_file = 'plugins.php';

wp_reset_vars(array('action', 'redirect', 'profile', 'error', 'warning', 'a', 'file', 'plugin'));

wp_admin_css( 'theme-editor' );

$plugins = get_plugins();

if ( isset($_REQUEST['file']) )
	$plugin = stripslashes($_REQUEST['file']);

if ( empty($plugin) ) {
	$plugin = array_keys($plugins);
	$plugin = $plugin[0];
}

$plugin_files = get_plugin_files($plugin);

if ( empty($file) )
	$file = $plugin_files[0];
else
	$file = stripslashes($file);

$file = validate_file_to_edit($file, $plugin_files);
$real_file = WP_PLUGIN_DIR . '/' . $file;
$scrollto = isset($_REQUEST['scrollto']) ? (int) $_REQUEST['scrollto'] : 0;

switch ( $action ) {

case 'update':

	check_admin_referer('edit-plugin_' . $file);

	$newcontent = stripslashes($_POST['newcontent']);
	if ( is_writeable($real_file) ) {
		$f = fopen($real_file, 'w+');
		fwrite($f, $newcontent);
		fclose($f);

		// Deactivate so we can test it.
		if ( is_plugin_active($file) || isset($_POST['phperror']) ) {
			if ( is_plugin_active($file) )
				deactivate_plugins($file, true);
			wp_redirect(add_query_arg('_wpnonce', wp_create_nonce('edit-plugin-test_' . $file), "plugin-editor.php?file=$file&liveupdate=1&scrollto=$scrollto"));
			exit;
		}
		wp_redirect("plugin-editor.php?file=$file&a=te&scrollto=$scrollto");
	} else {
		wp_redirect("plugin-editor.php?file=$file&scrollto=$scrollto");
	}
	exit;

break;

default:

	if ( isset($_GET['liveupdate']) ) {
		check_admin_referer('edit-plugin-test_' . $file);

		$error = validate_plugin($file);
		if ( is_wp_error($error) )
			wp_die( $error );

		if ( ! is_plugin_active($file) )
			activate_plugin($file, "plugin-editor.php?file=$file&phperror=1"); // we'll override this later if the plugin can be included without fatal error

		wp_redirect("plugin-editor.php?file=$file&a=te&scrollto=$scrollto");
		exit;
	}

	// List of allowable extensions
	$editable_extensions = array('php', 'txt', 'text', 'js', 'css', 'html', 'htm', 'xml', 'inc', 'include');
	$editable_extensions = (array) apply_filters('editable_extensions', $editable_extensions);

	if ( ! is_file($real_file) ) {
		wp_die(sprintf('<p>%s</p>', __('No such file exists! Double check the name and try again.')));
	} else {
		// Get the extension of the file
		if ( preg_match('/\.([^.]+)$/', $real_file, $matches) ) {
			$ext = strtolower($matches[1]);
			// If extension is not in the acceptable list, skip it
			if ( !in_array( $ext, $editable_extensions) )
				wp_die(sprintf('<p>%s</p>', __('Files of this type are not editable.')));
		}
	}

	require_once('admin-header.php');

	update_recently_edited(WP_PLUGIN_DIR . '/' . $file);

	$content = file_get_contents( $real_file );

	if ( '.php' == substr( $real_file, strrpos( $real_file, '.' ) ) ) {
		$functions = wp_doc_link_parse( $content );

		if ( !empty($functions) ) {
			$docs_select = '<select name="docs-list" id="docs-list">';
			$docs_select .= '<option value="">' . __( 'Function Name...' ) . '</option>';
			foreach ( $functions as $function) {
				$docs_select .= '<option value="' . esc_attr( $function ) . '">' . htmlspecialchars( $function ) . '()</option>';
			}
			$docs_select .= '</select>';
		}
	}

	$content = htmlspecialchars( $content );
	$codepress_lang = codepress_get_lang($real_file);

	?>
<?php if (isset($_GET['a'])) : ?>
 <div id="message" class="updated"><p><?php _e('File edited successfully.') ?></p></div>
<?php elseif (isset($_GET['phperror'])) : ?>
 <div id="message" class="updated"><p><?php _e('This plugin has been deactivated because your changes resulted in a <strong>fatal error</strong>.') ?></p>
	<?php
		if ( wp_verify_nonce($_GET['_error_nonce'], 'plugin-activation-error_' . $file) ) { ?>
	<iframe style="border:0" width="100%" height="70px" src="<?php bloginfo('wpurl'); ?>/wp-admin/plugins.php?action=error_scrape&amp;plugin=<?php echo esc_attr($file); ?>&amp;_wpnonce=<?php echo esc_attr($_GET['_error_nonce']); ?>"></iframe>
	<?php } ?>
</div>
<?php endif; ?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<div class="fileedit-sub">
<div class="alignleft">
<big><?php
	if ( is_plugin_active($plugin) ) {
		if ( is_writeable($real_file) )
			echo sprintf(__('Editing <strong>%s</strong> (active)'), $file);
		else
			echo sprintf(__('Browsing <strong>%s</strong> (active)'), $file);
	} else {
		if ( is_writeable($real_file) )
			echo sprintf(__('Editing <strong>%s</strong> (inactive)'), $file);
		else
			echo sprintf(__('Browsing <strong>%s</strong> (inactive)'), $file);
	}
	?></big>
</div>
<div class="alignright">
	<form action="plugin-editor.php" method="post">
		<strong><label for="plugin"><?php _e('Select plugin to edit:'); ?> </label></strong>
		<select name="plugin" id="plugin">
<?php
	foreach ( $plugins as $plugin_key => $a_plugin ) {
		$plugin_name = $a_plugin['Name'];
		if ( $plugin_key == $plugin )
			$selected = " selected='selected'";
		else
			$selected = '';
		$plugin_name = esc_attr($plugin_name);
		$plugin_key = esc_attr($plugin_key);
		echo "\n\t<option value=\"$plugin_key\" $selected>$plugin_name</option>";
	}
?>
		</select>
		<input type="submit" name="Submit" value="<?php esc_attr_e('Select') ?>" class="button" />
	</form>
</div>
<br class="clear" />
</div>

<div id="templateside">
	<h3><?php _e('Plugin Files'); ?></h3>

	<ul>
<?php
foreach ( $plugin_files as $plugin_file ) :
	// Get the extension of the file
	if ( preg_match('/\.([^.]+)$/', $plugin_file, $matches) ) {
		$ext = strtolower($matches[1]);
		// If extension is not in the acceptable list, skip it
		if ( !in_array( $ext, $editable_extensions ) )
			continue;
	} else {
		// No extension found
		continue;
	}
?>
		<li<?php echo $file == $plugin_file ? ' class="highlight"' : ''; ?>><a href="plugin-editor.php?file=<?php echo $plugin_file; ?>&amp;plugin=<?php echo $plugin; ?>"><?php echo $plugin_file ?></a></li>
<?php endforeach; ?>
	</ul>
</div>
<form name="template" id="template" action="plugin-editor.php" method="post">
	<?php wp_nonce_field('edit-plugin_' . $file) ?>
		<div><textarea cols="70" rows="25" name="newcontent" id="newcontent" tabindex="1" class="codepress <?php echo $codepress_lang ?>"><?php echo $content ?></textarea>
		<input type="hidden" name="action" value="update" />
		<input type="hidden" name="file" value="<?php echo esc_attr($file) ?>" />
		<input type="hidden" name="plugin" value="<?php echo esc_attr($plugin) ?>" />
		<input type="hidden" name="scrollto" id="scrollto" value="<?php echo $scrollto; ?>" />
		</div>
		<?php if ( !empty( $docs_select ) ) : ?>
		<div id="documentation"><label for="docs-list"><?php _e('Documentation:') ?></label> <?php echo $docs_select ?> <input type="button" class="button" value="<?php esc_attr_e( 'Lookup' ) ?> " onclick="if ( '' != jQuery('#docs-list').val() ) { window.open( 'http://api.wordpress.org/core/handbook/1.0/?function=' + escape( jQuery( '#docs-list' ).val() ) + '&amp;locale=<?php echo urlencode( get_locale() ) ?>&amp;version=<?php echo urlencode( $wp_version ) ?>&amp;redirect=true'); }" /></div>
		<?php endif; ?>
<?php if ( is_writeable($real_file) ) : ?>
	<?php if ( in_array( $file, (array) get_option( 'active_plugins', array() ) ) ) { ?>
		<p><?php _e('<strong>Warning:</strong> Making changes to active plugins is not recommended.  If your changes cause a fatal error, the plugin will be automatically deactivated.'); ?></p>
	<?php } ?>
	<p class="submit">
	<?php
		if ( isset($_GET['phperror']) )
			echo "<input type='hidden' name='phperror' value='1' /><input type='submit' name='submit' class='button-primary' value='" . esc_attr__('Update File and Attempt to Reactivate') . "' tabindex='2' />";
		else
			echo "<input type='submit' name='submit' class='button-primary' value='" . esc_attr__('Update File') . "' tabindex='2' />";
	?>
	</p>
<?php else : ?>
	<p><em><?php _e('You need to make this file writable before you can save your changes. See <a href="http://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.'); ?></em></p>
<?php endif; ?>
</form>
<br class="clear" />
</div>
<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($){
	$('#template').submit(function(){ $('#scrollto').val( $('#newcontent').scrollTop() ); });
	$('#newcontent').scrollTop( $('#scrollto').val() );
});
/* ]]> */
</script>
<?php
	break;
}
include("admin-footer.php");
