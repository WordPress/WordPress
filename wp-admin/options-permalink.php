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

if (isset($_POST['submit'])) {
	update_option('permalink_structure', $_POST['permalink_structure']);
	$permalink_structure = $_POST['permalink_structure'];

	update_option('category_base', $_POST['category_base']);
	$category_base = $_POST['category_base'];
} else {
	$permalink_structure = get_settings('permalink_structure');
	$category_base = get_settings('category_base');
}



	require_once('admin-header.php');
	if ($user_level <= 6) {
		die(__("You have do not have sufficient permissions to edit the options for this blog."));
	}
	require('./options-head.php');
?>
<?php if (isset($_POST['submit'])) : ?>
<div class="updated"><p><?php _e('Permalink structure updated.'); ?></p></div>
<?php endif; ?>
<div class="wrap"> 
  <h2><?php _e('Edit Permalink Structure') ?></h2> 
  <?php _e('<p>WordPress offers you the ability to create a custom URI structure for your permalinks and archives. The following &#8220;tags&#8221; are available:</p>')?> 

<dl>
	<dt><code>%year%</code></dt>
	<dd>
		<?php _e('The year of the post, 4 digits, for example <code>2004</code>') ?>
	</dd>
	<dt><code>%monthnum%</code></dt>
	<dd>
		<?php _e('Month of the year, for example <code>05</code>') ?>
	</dd>
	<dt><code>%day%</code></dt>
	<dd>
		<?php _e('Day of the month, for example <code>28</code>') ?>
	</dd>
	<dt><code>%hour%</code></dt>
	<dd>
		<?php _e('Hour of the day, for example <code>15</code>') ?>
	</dd>
	<dt><code>%minute%</code></dt>
	<dd>
		<?php _e('Minute of the hour, for example <code>43</code>') ?>
	</dd>
	<dt><code>%second%</code></dt>
	<dd>
		<?php _e('Second of the minute, for example <code>33</code>') ?>
	</dd>
	<dt><code>%postname%</code></dt>
	<dd>
		<?php _e('A sanitized version of the title of the post. So &#8220;This Is A Great Post!&#8221; becomes &#8220;<code>this-is-a-great-post</code>&#8221; in the URI') ?>
	</dd>
	<dt><code>%post_id%</code></dt>
	<dd>
		<?php _e('The unique ID # of the post, for example <code>423</code>') ?>
	</dd>
</dl>

  <?php _e('<p>So for example a value like:</p>
  <p><code>/archives/%year%/%monthnum%/%day%/%postname%/</code> </p>
  <p>would give you a permalink like:</p>
  <p><code>/archives/2003/05/23/my-cheese-sandwich/</code></p>
  <p> In general for this you must use mod_rewrite, however if you put a filename at the beginning WordPress will attempt to use that to pass the arguments, for example:</p>
  <p><code>/index.php/archives/%year%/%monthnum%/%day%/%postname%/</code> </p>
  <p>If you use this option you can ignore the mod_rewrite rules. </p>') ?>
  <form name="form" action="options-permalink.php" method="post"> 
    <p><?php _e('Use the template tags above to create a virtual site structure:') ?></p>
    <p> 
      <input name="permalink_structure" type="text" style="width: 98%;" value="<?php echo $permalink_structure; ?>" /> 
    </p> 
	<p><?php _e('If you like, you may enter a custom prefix for your category URIs here. For example, <code>/taxonomy/categorias</code> would make your category links like <code>http://example.org/taxonomy/categorias/general/</code>. If you leave this blank the default will be used.') ?></p>
	<p> 
  <input name="category_base" type="text" style="width: 98%;" value="<?php echo $category_base; ?>" /> 
     </p> 
    <p class="submit"> 
      <input type="submit" name="submit" value="<?php _e('Update Permalink Structure &raquo;') ?>"> 
    </p> 
  </form> 
<?php
 if ($permalink_structure) {
?>
  <p><?php printf(__('Using the permalink structure value you currently have, <code>%s</code>, these are the mod_rewrite rules you should have in your <code>.htaccess</code> file. Click in the field and press <kbd>CTRL + a</kbd> to select all.'), $permalink_structure) ?></p>
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
    	<textarea rows="5" style="width: 98%;">RewriteEngine On
RewriteBase <?php echo $home_root; ?> 
<?php
$rewrite = rewrite_rules('', $permalink_structure);
$rules = '';
foreach ($rewrite as $match => $query) {
	if (strstr($query, 'index.php')) {
        $rules .= 'RewriteRule ^' . $match . ' ' . $home_root . $query . " [QSA]\n";
    } else {
        $rules .= 'RewriteRule ^' . $match . ' ' . $site_root . $query . " [QSA]\n";
    }
}
echo apply_filters('rewrite_rules', $rules);
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
<?php } ?>
</div>

<?php
require('./admin-footer.php');
?>