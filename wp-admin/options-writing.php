<?php
/**
 * Writing settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

if ( ! current_user_can( 'manage_options' ) )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

$title = __('Writing Settings');
$parent_file = 'options-general.php';

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => '<p>' . __('You can submit content in several different ways; this screen holds the settings for all of them. The top section controls the editor within the dashboard, while the rest control external publishing methods. For more information on any of these methods, use the documentation links.') . '</p>' .
		'<p>' . __('You must click the Save Changes button at the bottom of the screen for new settings to take effect.') . '</p>',
) );

get_current_screen()->add_help_tab( array(
	'id'      => 'options-press',
	'title'   => __('Press This'),
	'content' => '<p>' . __('Press This is a bookmarklet that makes it easy to blog about something you come across on the web. You can use it to just grab a link, or to post an excerpt. Press This will even allow you to choose from images included on the page and use them in your post. Just drag the Press This link on this screen to your bookmarks bar in your browser, and you&#8217;ll be on your way to easier content creation. Clicking on it while on another website opens a popup window with all these options.') . '</p>',
) );

if ( is_multisite() ) {
	$post_email_help = '<p>' . __('Due to security issues, you cannot use Post By Email on Multisite Installs.') . '</p>';
} else {
	$post_email_help = '<p>' . __('Post via email settings allow you to send your WordPress install an email with the content of your post. You must set up a secret e-mail account with POP3 access to use this, and any mail received at this address will be posted, so it&#8217;s a good idea to keep this address very secret.') . '</p>';
}

get_current_screen()->add_help_tab( array(
	'id'      => 'options-postemail',
	'title'   => __('Post Via Email'),
	'content' => $post_email_help,
) );

get_current_screen()->add_help_tab( array(
	'id'      => 'options-remote',
	'title'   => __('Remote Publishing'),
	'content' => '<p>' . __('Remote Publishing allows you to use an external editor (like the iOS or Android app) to write your posts.') . '</p>',
) );

get_current_screen()->add_help_tab( array(
	'id'      => 'options-services',
	'title'   => __('Update Services'),
	'content' => '<p>' . __('If desired, WordPress will automatically alert various services of your new posts.') . '</p>',
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Settings_Writing_Screen" target="_blank">Documentation on Writing Settings</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

include('./admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<form method="post" action="options.php">
<?php settings_fields('writing'); ?>

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="default_post_edit_rows"> <?php _e('Size of the post box') ?></label></th>
<td><input name="default_post_edit_rows" type="text" id="default_post_edit_rows" value="<?php form_option('default_post_edit_rows'); ?>" class="small-text" />
<?php _e('lines') ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Formatting') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Formatting') ?></span></legend>
<label for="use_smilies">
<input name="use_smilies" type="checkbox" id="use_smilies" value="1" <?php checked('1', get_option('use_smilies')); ?> />
<?php _e('Convert emoticons like <code>:-)</code> and <code>:-P</code> to graphics on display') ?></label><br />
<label for="use_balanceTags"><input name="use_balanceTags" type="checkbox" id="use_balanceTags" value="1" <?php checked('1', get_option('use_balanceTags')); ?> /> <?php _e('WordPress should correct invalidly nested XHTML automatically') ?></label>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><label for="default_category"><?php _e('Default Post Category') ?></label></th>
<td>
<?php
wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'default_category', 'orderby' => 'name', 'selected' => get_option('default_category'), 'hierarchical' => true));
?>
</td>
</tr>
<?php
if ( current_theme_supports( 'post-formats' ) ) :
	$post_formats = get_theme_support( 'post-formats' );
	if ( is_array( $post_formats[0] ) ) :
?>
<tr valign="top">
<th scope="row"><label for="default_post_format"><?php _e('Default Post Format') ?></label></th>
<td>
	<select name="default_post_format" id="default_post_format">
		<option value="0"><?php _e('Standard'); ?></option>
<?php foreach ( $post_formats[0] as $format ): ?>
		<option<?php selected( get_option('default_post_format'), $format ); ?> value="<?php echo esc_attr( $format ); ?>"><?php echo esc_html( get_post_format_string( $format ) ); ?></option>
<?php endforeach; ?>
	</select>
</td>
</tr>
<?php endif; endif; ?>
<tr valign="top">
<th scope="row"><label for="default_link_category"><?php _e('Default Link Category') ?></label></th>
<td>
<?php
wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'default_link_category', 'orderby' => 'name', 'selected' => get_option('default_link_category'), 'hierarchical' => true, 'taxonomy' => 'link_category'));
?>
</td>
</tr>
<?php do_settings_fields('writing', 'default'); ?>
</table>

<h3 class="title"><?php _e('Press This') ?></h3>
<p><?php _e('Press This is a bookmarklet: a little app that runs in your browser and lets you grab bits of the web.');?></p>
<p><?php _e('Use Press This to clip text, images and videos from any web page. Then edit and add more straight from Press This before you save or publish it in a post on your site.'); ?></p>
<p><?php _e('Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.') ?></p>
<p class="pressthis"><a onclick="return false;" oncontextmenu="if(window.navigator.userAgent.indexOf('WebKit')!=-1||window.navigator.userAgent.indexOf('MSIE')!=-1)jQuery('.pressthis-code').show().find('textarea').focus().select();return false;" href="<?php echo htmlspecialchars( get_shortcut_link() ); ?>"><span><?php _e('Press This') ?></span></a></p>
<div class="pressthis-code" style="display:none;">
	<p class="description"><?php _e('If your bookmarks toolbar is hidden: copy the code below, open your Bookmarks manager, create new bookmark, type Press This into the name field and paste the code into the URL field.') ?></p>
	<p><textarea rows="5" cols="120" readonly="readonly"><?php echo htmlspecialchars( get_shortcut_link() ); ?></textarea></p>
</div>

<?php if ( apply_filters( 'enable_post_by_email_configuration', true ) ) { ?>
<h3><?php _e('Post via e-mail') ?></h3>
<p><?php printf(__('To post to WordPress by e-mail you must set up a secret e-mail account with POP3 access. Any mail received at this address will be posted, so it&#8217;s a good idea to keep this address very secret. Here are three random strings you could use: <kbd>%s</kbd>, <kbd>%s</kbd>, <kbd>%s</kbd>.'), wp_generate_password(8, false), wp_generate_password(8, false), wp_generate_password(8, false)) ?></p>

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="mailserver_url"><?php _e('Mail Server') ?></label></th>
<td><input name="mailserver_url" type="text" id="mailserver_url" value="<?php form_option('mailserver_url'); ?>" class="regular-text code" />
<label for="mailserver_port"><?php _e('Port') ?></label>
<input name="mailserver_port" type="text" id="mailserver_port" value="<?php form_option('mailserver_port'); ?>" class="small-text" />
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="mailserver_login"><?php _e('Login Name') ?></label></th>
<td><input name="mailserver_login" type="text" id="mailserver_login" value="<?php form_option('mailserver_login'); ?>" class="regular-text ltr" /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="mailserver_pass"><?php _e('Password') ?></label></th>
<td>
<input name="mailserver_pass" type="text" id="mailserver_pass" value="<?php form_option('mailserver_pass'); ?>" class="regular-text ltr" />
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="default_email_category"><?php _e('Default Mail Category') ?></label></th>
<td>
<?php
wp_dropdown_categories(array('hide_empty' => 0, 'name' => 'default_email_category', 'orderby' => 'name', 'selected' => get_option('default_email_category'), 'hierarchical' => true));
?>
</td>
</tr>
<?php do_settings_fields('writing', 'post_via_email'); ?>
</table>
<?php } ?>

<h3><?php _e('Remote Publishing') ?></h3>
<p><?php printf(__('To post to WordPress from a desktop blogging client or remote website that uses the Atom Publishing Protocol or one of the XML-RPC publishing interfaces you must enable them below.')) ?></p>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Atom Publishing Protocol') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('Atom Publishing Protocol') ?></span></legend>
<label for="enable_app">
<input name="enable_app" type="checkbox" id="enable_app" value="1" <?php checked('1', get_option('enable_app')); ?> />
<?php _e('Enable the Atom Publishing Protocol.') ?></label><br />
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('XML-RPC') ?></th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e('XML-RPC') ?></span></legend>
<label for="enable_xmlrpc">
<input name="enable_xmlrpc" type="checkbox" id="enable_xmlrpc" value="1" <?php checked('1', get_option('enable_xmlrpc')); ?> />
<?php _e('Enable the WordPress, Movable Type, MetaWeblog and Blogger XML-RPC publishing protocols.') ?></label><br />
</fieldset></td>
</tr>
<?php do_settings_fields('writing', 'remote_publishing'); ?>
</table>

<?php if ( apply_filters( 'enable_update_services_configuration', true ) ) { ?>
<h3><?php _e('Update Services') ?></h3>

<?php if ( 1 == get_option('blog_public') ) : ?>

<p><label for="ping_sites"><?php _e('When you publish a new post, WordPress automatically notifies the following site update services. For more about this, see <a href="http://codex.wordpress.org/Update_Services">Update Services</a> on the Codex. Separate multiple service <abbr title="Universal Resource Locator">URL</abbr>s with line breaks.') ?></label></p>

<textarea name="ping_sites" id="ping_sites" class="large-text code" rows="3"><?php echo esc_textarea( get_option('ping_sites') ); ?></textarea>

<?php else : ?>

	<p><?php printf(__('WordPress is not notifying any <a href="http://codex.wordpress.org/Update_Services">Update Services</a> because of your site&#8217;s <a href="%s">privacy settings</a>.'), 'options-privacy.php'); ?></p>

<?php endif; ?>
<?php } // multisite ?>

<?php do_settings_sections('writing'); ?>

<?php submit_button(); ?>
</form>
</div>

<?php include('./admin-footer.php') ?>
