<?php
require_once('admin.php');

$title = __('Writing Settings');
$parent_file = 'options-general.php';

include('admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Writing Settings') ?></h2>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options') ?>

<table class="form-table">
<tr valign="top">
<th scope="row"> <?php _e('Size of the post box') ?></th>
<td><input name="default_post_edit_rows" type="text" id="default_post_edit_rows" value="<?php form_option('default_post_edit_rows'); ?>" size="2" style="width: 1.5em;" />
<?php _e('lines') ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Formatting') ?></th>
<td>
<label for="use_smilies">
<input name="use_smilies" type="checkbox" id="use_smilies" value="1" <?php checked('1', get_option('use_smilies')); ?> />
<?php _e('Convert emoticons like <code>:-)</code> and <code>:-P</code> to graphics on display') ?></label><br />
<label for="use_balanceTags"><input name="use_balanceTags" type="checkbox" id="use_balanceTags" value="1" <?php checked('1', get_option('use_balanceTags')); ?> /> <?php _e('WordPress should correct invalidly nested XHTML automatically') ?></label>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Default Post Category') ?></th>
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
<th scope="row"><?php _e('Default Link Category') ?></th>
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
</table>

<h3><?php _e('Post via e-mail') ?></h3>
<p><?php printf(__('To post to WordPress by e-mail you must set up a secret e-mail account with POP3 access. Any mail received at this address will be posted, so it&#8217;s a good idea to keep this address very secret. Here are three random strings you could use: <code>%s</code>, <code>%s</code>, <code>%s</code>.'), wp_generate_password(), wp_generate_password(), wp_generate_password()) ?></p>

<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Mail Server') ?></th>
<td><input name="mailserver_url" type="text" id="mailserver_url" value="<?php form_option('mailserver_url'); ?>" size="40" />
<label for="mailserver_port"><?php _e('Port') ?></label>
<input name="mailserver_port" type="text" id="mailserver_port" value="<?php form_option('mailserver_port'); ?>" size="6" />
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Login Name') ?></th>
<td><input name="mailserver_login" type="text" id="mailserver_login" value="<?php form_option('mailserver_login'); ?>" size="40" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Password') ?></th>
<td>
<input name="mailserver_pass" type="text" id="mailserver_pass" value="<?php form_option('mailserver_pass'); ?>" size="40" />
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Default Mail Category') ?></th>
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
</table>

<h3><?php _e('Update Services') ?></h3>

<?php if ( get_option('blog_public') ) : ?>

<p><?php _e('When you publish a new post, WordPress automatically notifies the following site update services. For more about this, see <a href="http://codex.wordpress.org/Update_Services">Update Services</a> on the Codex. Separate multiple service <abbr title="Universal Resource Locator">URL</abbr>s with line breaks.') ?></p>

<textarea name="ping_sites" id="ping_sites" style="width: 98%;" rows="3" cols="50"><?php form_option('ping_sites'); ?></textarea>

<?php else : ?>

	<p><?php printf(__('WordPress is not notifying any <a href="http://codex.wordpress.org/Update_Services">Update Services</a> because of your blog\'s <a href="%s">privacy settings</a>.'), 'options-privacy.php'); ?></p>

<?php endif; ?>

<p class="submit">
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="default_post_edit_rows,use_smilies,ping_sites,mailserver_url,mailserver_port,mailserver_login,mailserver_pass,default_category,default_email_category,use_balanceTags,default_link_category" />
<input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
</p>
</form>
</div>

<?php include('./admin-footer.php') ?>
