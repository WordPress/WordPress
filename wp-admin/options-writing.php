<?php
/**
 * Writing settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Writing Settings');
$parent_file = 'options-general.php';

include('admin-header.php');
?>

<div class="wrap">
<h2><?php echo wp_specialchars( $title ); ?></h2> 

<form method="post" action="options.php">
<?php wp_nonce_field('writing-options') ?>
<input type='hidden' name='option_page' value='writing' />
<input type="hidden" name="action" value="update" />

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="default_post_edit_rows"> <?php _e('Size of the post box') ?></label></th>
<td><input name="default_post_edit_rows" type="text" id="default_post_edit_rows" value="<?php form_option('default_post_edit_rows'); ?>" class="small-text" />
<?php _e('lines') ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Formatting') ?></th>
<td><fieldset><legend class="hidden"><?php _e('Formatting') ?></legend>
<label for="use_smilies">
<input name="use_smilies" type="checkbox" id="use_smilies" value="1" <?php checked('1', get_option('use_smilies')); ?> />
<?php _e('Convert emoticons like <code>:-)</code> and <code>:-P</code> to graphics on display') ?></label><br />
<label for="use_balanceTags"><input name="use_balanceTags" type="checkbox" id="use_balanceTags" value="1" <?php checked('1', get_option('use_balanceTags')); ?> /> <?php _e('WordPress should correct invalidly nested XHTML automatically') ?></label>
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><label for="default_category"><?php _e('Default Post Category') ?></label></th>
<td><select name="default_category" id="default_category">
<?php
$categories = get_categories('get=all');
foreach ($categories as $category) :
$category = sanitize_category($category);
if ($category->term_id == get_option('default_category')) $selected = " selected='selected'";
else $selected = '';
echo "\n\t<option value='$category->term_id' $selected>$category->name</option>";
endforeach;
?>
</select></td>
</tr>
<tr valign="top">
<th scope="row"><label for="default_link_category"><?php _e('Default Link Category') ?></label></th>
<td><select name="default_link_category" id="default_link_category">
<?php
$link_categories = get_terms('link_category', 'get=all');
foreach ($link_categories as $category) :
$category = sanitize_term($category, 'link_category');
if ($category->term_id == get_option('default_link_category')) $selected = " selected='selected'";
else $selected = '';
echo "\n\t<option value='$category->term_id' $selected>$category->name</option>";
endforeach;
?>
</select></td>
</tr>
<?php do_settings_fields('writing', 'default'); ?>
</table>

<h3><?php _e('Remote Publishing') ?></h3>
<p><?php printf(__('To post to WordPress from a desktop blogging client or remote website that uses the Atom Publishing Protocol or one of the XML-RPC publishing interfaces you must enable them below.')) ?></p>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Atom Publishing Protocol') ?></th>
<td><fieldset><legend class="hidden"><?php _e('Atom Publishing Protocol') ?></legend>
<label for="enable_app">
<input name="enable_app" type="checkbox" id="enable_app" value="1" <?php checked('1', get_option('enable_app')); ?> />
<?php _e('Enable the Atom Publishing Protocol.') ?></label><br />
</fieldset></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('XML-RPC') ?></th>
<td><fieldset><legend class="hidden"><?php _e('XML-RPC') ?></legend>
<label for="enable_xmlrpc">
<input name="enable_xmlrpc" type="checkbox" id="enable_xmlrpc" value="1" <?php checked('1', get_option('enable_xmlrpc')); ?> />
<?php _e('Enable the WordPress, Movable Type, MetaWeblog and Blogger XML-RPC publishing protocols.') ?></label><br />
</fieldset></td>
</tr>
<?php do_settings_fields('writing', 'remote_publishing'); ?>
</table>

<h3><?php _e('Post via e-mail') ?></h3>
<p><?php printf(__('To post to WordPress by e-mail you must set up a secret e-mail account with POP3 access. Any mail received at this address will be posted, so it&#8217;s a good idea to keep this address very secret. Here are three random strings you could use: <code>%s</code>, <code>%s</code>, <code>%s</code>.'), wp_generate_password(8, false), wp_generate_password(8, false), wp_generate_password(8, false)) ?></p>

<table class="form-table">
<tr valign="top">
<th scope="row"><label for="mailserver_url"><?php _e('Mail Server') ?></label></th>
<td><input name="mailserver_url" type="text" id="mailserver_url" value="<?php form_option('mailserver_url'); ?>" class="regular-text" />
<label for="mailserver_port"><?php _e('Port') ?></label>
<input name="mailserver_port" type="text" id="mailserver_port" value="<?php form_option('mailserver_port'); ?>" class="small-text" />
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="mailserver_login"><?php _e('Login Name') ?></label></th>
<td><input name="mailserver_login" type="text" id="mailserver_login" value="<?php form_option('mailserver_login'); ?>" class="regular-text" /></td>
</tr>
<tr valign="top">
<th scope="row"><label for="mailserver_pass"><?php _e('Password') ?></label></th>
<td>
<input name="mailserver_pass" type="text" id="mailserver_pass" value="<?php form_option('mailserver_pass'); ?>" class="regular-text" />
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="default_email_category"><?php _e('Default Mail Category') ?></label></th>
<td><select name="default_email_category" id="default_email_category">
<?php
//Alreay have $categories from default_category
foreach ($categories as $category) :
$category = sanitize_category($category);
if ($category->cat_ID == get_option('default_email_category')) $selected = " selected='selected'";
else $selected = '';
echo "\n\t<option value='$category->cat_ID' $selected>$category->cat_name</option>";
endforeach;
?>
</select></td>
</tr>
<?php do_settings_fields('writing', 'post_via_email'); ?>
</table>

<h3><?php _e('Update Services') ?></h3>

<?php if ( get_option('blog_public') ) : ?>

<p><label for="ping_sites"><?php _e('When you publish a new post, WordPress automatically notifies the following site update services. For more about this, see <a href="http://codex.wordpress.org/Update_Services">Update Services</a> on the Codex. Separate multiple service <abbr title="Universal Resource Locator">URL</abbr>s with line breaks.') ?></label></p>

<textarea name="ping_sites" id="ping_sites" class="large-text" rows="3"><?php form_option('ping_sites'); ?></textarea>

<?php else : ?>

	<p><?php printf(__('WordPress is not notifying any <a href="http://codex.wordpress.org/Update_Services">Update Services</a> because of your blog\'s <a href="%s">privacy settings</a>.'), 'options-privacy.php'); ?></p>

<?php endif; ?>

<h3><?php _e('Press This') ?></h3>
<p><?php _e('Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.') ?>  <a href="<?php echo htmlspecialchars( get_shortcut_link() ); ?>" title="<?php echo attribute_escape(__('Press This')) ?>"><?php _e('Press This') ?></a></p>

<?php do_settings_sections('writing'); ?>

<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
</p>
</form>
</div>

<?php include('./admin-footer.php') ?>
