<?php
require_once('../wp-includes/wp-l10n.php');

$title = __('Permalink Options');
$parent_file = 'options-general.php';

$wpvarstoreset = array('action','standalone', 'option_group_id');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($_POST["$wpvar"])) {
			if (empty($_GET["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $_GET["$wpvar"];
			}
		} else {
			$$wpvar = $_POST["$wpvar"];
		}
	}
}

require_once('./optionhandler.php');

if ($_POST['submit']) {
	update_option('permalink_structure', $_POST['permalink_structure']);
	$permalink_structure = $_POST['permalink_structure'];
} else {
	$permalink_structure = get_settings('permalink_structure');
}



	require_once('admin-header.php');
	if ($user_level <= 6) {
		die(__("You have do not have sufficient permissions to edit the options for this blog."));
	}
	require('./options-head.php');
?>
<?php if ($_POST['submit']) : ?>
<div class="updated"><p><?php _e('Permalink structure updated.'); ?></p></div>
<?php endif; ?>
<div class="wrap"> 
  <h2><?php _e('Edit Permalink Structure') ?></h2> 
  <?php _e('<p>WordPress offers you the ability to create a custom URI structure for your permalinks and archives. The following &#8220;tags&#8221; are available:</p>')?> 
  <ul> 
    <li><code>%year%</code> --- <?php _e('The year of the post, 4 digits, for example <code>2004</code>') ?> </li> 
    <li><code>%monthnum%</code> --- <?php _e('Month of the year, for example <code>05</code>') ?></li> 
    <li><code>%day% </code>--- <?php _e('Day of the month, for example <code>28</code>') ?></li> 
    <li><code>%postname%</code> --- <?php _e('A sanitized version of the title of the post. So &#8220;This Is A Great Post!&#8221; becomes &#8220;<code>this-is-a-great-post</code>&#8221; in the URI') ?> </li> 
    <li><code>%post_id%</code> --- <?php _e('The unique ID # of the post, for example <code>423</code>') ?></li> 
  </ul> 
  <?php _e('<p>So for example a value like:</p>
  <p><code>/archives/%year%/%monthnum%/%day%/%postname%/</code> </p>
  <p>would give you a permalink like:</p>
  <p><code>/archives/2003/05/23/my-cheese-sandwich/</code></p>
  <p> In general for this you must use mod_rewrite, however if you put a filename at the beginning WordPress will attempt to use that to pass the arguments, for example:</p>
  <p><code>/index.php/archives/%year%/%monthnum%/%day%/%postname%/</code> </p>
  <p>If you use this option you can ignore the mod_rewrite rules. </p>') ?>
  <form name="form" action="options-permalink.php" method="post"> 
    <?php _e('<p>Use the template tags above to create a virtual site structure:</p>') ?> 
    <p> 
      <input name="permalink_structure" type="text" style="width: 98%;" value="<?php echo $permalink_structure; ?>" /> 
    </p> 
    <p class="submit"> 
      <input type="submit" name="submit" value="<?php _e('Update Permalink Structure &raquo;') ?>"> 
    </p> 
  </form> 
<?php
 if ($permalink_structure) {
?>
  <?php printf(__('<p>Using the permalink structure value you currently have, <code>%s</code>, these are the mod_rewrite rules you should have in your <code>.htaccess</code> file.</p>'), $permalink_structure) ?> 
  <?php
$site_root = str_replace('http://', '', trim(get_settings('siteurl')));
$site_root = preg_replace('|([^/]*)(.*)|i', '$2', $site_root);
if ('/' != substr($site_root, -1)) $site_root = $site_root . '/';

$home_root = str_replace('http://', '', trim(get_settings('home')));
$home_root = preg_replace('|([^/]*)(.*)|i', '$2', $home_root);
if ('/' != substr($home_root, -1)) $home_root = $home_root . '/';

?> 
<form action="">
    <p>
    	<textarea rows="5" style="width: 100%;">RewriteEngine On
RewriteBase <?php echo $home_root; ?> 
<?php
$rewrite = rewrite_rules('', $permalink_structure);
foreach ($rewrite as $match => $query) {
	if (strstr($query, 'index.php')) echo 'RewriteRule ^' . $match . ' ' . $home_root . $query . " [QSA]\n";
    echo 'RewriteRule ^' . $match . ' ' . $site_root . $query . " [QSA]\n";
}
?>
    </textarea>
    </p>
    <?php printf(__('<p>If your <code>.htaccess</code> file is writable by WordPress, you can <a href="%s">edit it through your template interface</a>.</p>'), 'templates.php?file=.htaccess') ?>
</form>
</div> 
<?php
} else {
?>
<p>
<?php _e('You are not currently using customized permalinks. No special mod_rewrite rules are needed.') ?>
</p>
<?php
}
echo "</div>\n";

require('./admin-footer.php');
?>
