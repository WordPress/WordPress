<?php
$title = 'Permalink Options';
$parent_file = 'options-general.php';

function add_magic_quotes($array) {
	foreach ($array as $k => $v) {
		if (is_array($v)) {
			$array[$k] = add_magic_quotes($v);
		} else {
			$array[$k] = addslashes($v);
		}
	}
	return $array;
}

if (!get_magic_quotes_gpc()) {
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

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

require_once('optionhandler.php');

if ($_POST['Submit']) {
	update_option('permalink_structure', $_POST['permalink_structure']);
	$permalink_structure = $_POST['permalink_structure'];
} else {
	$permalink_structure = get_settings('permalink_structure');
}


switch($action) {

default:
	$standalone = 0;
	include_once('admin-header.php');
	if ($user_level <= 6) {
		die("You have do not have sufficient permissions to edit the options for this blog.");
	}
	include('options-head.php');
?>
<div class="wrap"> 
  <h2>Edit Permalink Structure</h2> 
  <p>WordPress offers you the ability to create a custom URI structure for your permalinks and archives. The following &#8220;tags&#8221; are available:</p> 
  <ul> 
    <li><code>%year%</code> --- The year of the post, 4 digits, for example <code>2004</code> </li> 
    <li><code>%monthnum%</code> --- Month of the year, for example <code>05</code></li> 
    <li><code>%day% </code>--- Day of the month, for example <code>28</code></li> 
    <li><code>%postname%</code> --- A sanitized version of the title of the post. So &quot;This Is A Great Post!&quot; becomes &quot;<code>this-is-a-great-post</code>&quot; in the URI </li> 
    <li><code>%post_id%</code> --- The unique ID # of the post, for example <code>423</code> <strong></strong></li> 
  </ul> 
  <p>So for example a value like:</p>
  <p><code>/archives/%year%/%monthnum%/%day%/%postname%/</code> </p>
  <p>would give you a permalink like:</p>
  <p><code>/archives/2003/05/23/my-cheese-sandwich/</code></p>
  <p> In general for this you must use mod_rewrite, however if you put a filename at the beginning WordPress will attempt to use that to pass the arguments, for example:</p>
  <p><code>/index.php/archives/%year%/%monthnum%/%day%/%postname%/</code> </p>
  <p>If you use this option you can ignore the mod_rewrite rules. </p>
  <form name="form" action="options-permalink.php" method="post"> 
    <p>Use the template tags above to create a virtual site structure:</p> 
    <p> 
      <input name="permalink_structure" type="text" style="width: 100%;" value="<?php echo $permalink_structure; ?>" /> 
    </p> 
    <p class="submit"> 
      <input type="submit" name="Submit" value="Update Permalink Structure"> 
    </p> 
  </form> 
<?php
 if ($permalink_structure) {
?>
  <p>Using the permalink structure value you currently have, <code><?php echo $permalink_structure; ?></code>, these are the mod_rewrite rules you should have in your <code>.htaccess</code> file.</p> 
  <?php
$site_root = str_replace('http://', '', trim(get_settings('siteurl')));
$site_root = preg_replace('|([^/]*)(.*)|i', '$2', $site_root);
if ('/' != substr($site_root, -1)) $site_root = $site_root . '/';

?> 
<form action="">
    <p>
    	<textarea rows="5" style="width: 100%;">RewriteEngine On
RewriteBase <?php echo $site_root; ?> 
<?php
$rewrite = rewrite_rules('', $permalink_structure);
foreach ($rewrite as $match => $query) {
    echo 'RewriteRule ^' . $match . ' ' . $site_root . $query . " [QSA]\n";
}
?>
    </textarea>
    </p>
    <p>If your <code>.htaccess</code> file is writable by WordPress, you can <a href="templates.php?file=.htaccess">edit it through your template interface</a>.</p>
</form>
</div> 
<?php
} else {
?>
<p>
You are not currently using customized permalinks. No special mod_rewrite
rules are needed.
</p>
<?php
}
echo "</div>\n";

break;
}

include("admin-footer.php") 
?>