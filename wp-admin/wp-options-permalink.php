<?php
$title = 'Permalink Options';
$this_file = 'wp-options.php';

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
	$HTTP_GET_VARS    = add_magic_quotes($HTTP_GET_VARS);
	$HTTP_POST_VARS   = add_magic_quotes($HTTP_POST_VARS);
	$HTTP_COOKIE_VARS = add_magic_quotes($HTTP_COOKIE_VARS);
}

$b2varstoreset = array('action','standalone', 'option_group_id');
for ($i=0; $i<count($b2varstoreset); $i += 1) {
	$b2var = $b2varstoreset[$i];
	if (!isset($$b2var)) {
		if (empty($HTTP_POST_VARS["$b2var"])) {
			if (empty($HTTP_GET_VARS["$b2var"])) {
				$$b2var = '';
			} else {
				$$b2var = $HTTP_GET_VARS["$b2var"];
			}
		} else {
			$$b2var = $HTTP_POST_VARS["$b2var"];
		}
	}
}

require_once('optionhandler.php');

if ($HTTP_POST_VARS['permalink_structure'] != '') {
	update_option('permalink_structure', $HTTP_POST_VARS['permalink_structure']);
	$permalink_structure = $HTTP_POST_VARS['permalink_structure'];
} else {
	$permalink_structure = get_settings('permalink_structure');
}


switch($action) {

default:
	$standalone = 0;
	include_once('b2header.php');
	if ($user_level <= 3) {
		die("You have no right to edit the options for this blog.<br>Ask for a promotion to your <a href=\"mailto:$admin_email\">blog admin</a> :)");
	}
?>
 <ul id="adminmenu2"> 
  <?php
    //we need to iterate through the available option groups.
    $option_groups = $wpdb->get_results("SELECT group_id, group_name, group_desc, group_longdesc FROM $tableoptiongroups ORDER BY group_id");
    foreach ($option_groups as $option_group) {
        if ($option_group->group_id == $option_group_id) {
            $current_desc=$option_group->group_desc;
            $current_long_desc = $option_group->group_longdesc;
            echo("  <li><a id=\"current2\" href=\"$this_file?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></li>\n");
        } else {
            echo("  <li><a href=\"$this_file?option_group_id={$option_group->group_id}\" title=\"{$option_group->group_desc}\">{$option_group->group_name}</a></li>\n");
        }
    } // end for each group
?> 
  <li class="last"><a href="wp-options-permalink.php">Permalinks</a></li> 
</ul> 
<br clear="all" /> 
<div class="wrap"> 
  <h2>Edit Permalink Structure</h2> 
  <p>WordPress offers you the ability to create a custom URI structure for your permalinks and archives. The following &#8220;tags&#8221; are available:</p> 
  <ul> 
    <li><code>%year%</code> --- The year of the post, 4 digits, for example 2003 </li> 
    <li><code>%monthnum%</code> --- Two digit month, for example 05</li> 
    <li><code>%day% </code>--- Day of the month, for example 28</li> 
    <li><code>%postname%</code> --- A sanitized version of the title of the post. So &quot;This Is A Great Post!&quot; becomes &quot;this-is-a-great-post&quot; </li> 
    <li><code>%category%</code> --- Category name of the post. For example &quot;general&quot;. <strong>Not done yet. </strong></li> 
  </ul> 
  <p>So for example a value like <code>/%year%/%monthnum%/%day%/%postname%</code> could give you a permalink like <code>/2003/05/23/my-cheese-sandwich</code> . For this to work you'll need mod_rewrite installed on your server for the rule generation rule to work below. In the future there may be other options. </p> 
  <form name="form" action="wp-options-permalink.php" method="post"> 
    <p>Use the template tags above to create a virtual site structure:</p> 
    <p> 
      <input name="permalink_structure" type="text" style="width: 100%;" value="<?php echo $permalink_structure; ?>" /> 
    </p> 
    <p> 
      <input type="submit" name="Submit" value="Update"> 
    </p> 
  </form> 
  <p>Using the permalink structure value you currently have, <code><?php echo $permalink_structure; ?></code>, these are the mod_rewrite rules you should have in your <code>.htaccess</code> file.</p> 
  <?php
$site_root = str_replace('http://', '', trim(get_settings('siteurl')));
$site_root = preg_replace('|([^/]*)(.*)|i', '$2', $site_root);
if ('/' != substr($site_root, -1)) $site_root = $site_root . '/';

$rewritecode = array(
	'%year%',
	'%monthnum%',
	'%day%',
	'%postname%'
);
$rewritereplace = array(
	'([0-9]{4})?',
	'([0-9]{1,2})?',
	'([0-9]{1,2})?',
	'([0-9a-z-]+)?'
);
$queryreplace = array (
	'year=',
	'monthnum=',
	'day=',
	'name='
);



$match = str_replace('/', '/?', $permalink_structure);
$match = str_replace($rewritecode, $rewritereplace, $match);

preg_match_all('/%.+?%/', $permalink_structure, $tokens);

$query = '/index.php?';
for ($i = 0; $i < count($tokens[0]); ++$i) {
	if (0 < $i) $query .= '&';
	$query .= str_replace($rewritecode, $queryreplace, $tokens[0][$i]) . '$'. ($i + 1);
	
}

?> 
  <p><code>RewriteEngine On<br /> 
    RewriteBase <?php echo $site_root; ?><br /> 
    RewriteRule ^<?php echo $match; echo '$ ' . $query ?> [QSA]</code></p> 
</div> 
<?php

break;
}

include("b2footer.php") ?> 
