<?php
$title = "Template &amp; file editing";

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

$wpvarstoreset = array('action','standalone','redirect','profile','error','warning','a','file');
for ($i=0; $i<count($wpvarstoreset); $i += 1) {
	$wpvar = $wpvarstoreset[$i];
	if (!isset($$wpvar)) {
		if (empty($HTTP_POST_VARS["$wpvar"])) {
			if (empty($HTTP_GET_VARS["$wpvar"])) {
				$$wpvar = '';
			} else {
				$$wpvar = $HTTP_GET_VARS["$wpvar"];
			}
		} else {
			$$wpvar = $HTTP_POST_VARS["$wpvar"];
		}
	}
}

switch($action) {

case 'update':

	$standalone = 1;
	require_once("admin-header.php");

	if ($user_level < 3) {
		die('<p>You have no right to edit the template for this blog.<br />Ask for a promotion to your <a href="mailto:$admin_email">blog admin</a>. :)</p>');
	}

	$newcontent = stripslashes($HTTP_POST_VARS['newcontent']);
	$file = $HTTP_POST_VARS['file'];
	$f = fopen($file, 'w+');
	fwrite($f, $newcontent);
	fclose($f);

	$file = str_replace('../', '', $file);
	header("Location: templates.php?file=$file&a=te");
	exit();

break;

default:

	require_once('admin-header.php');

	if ($user_level <= 3) {
		die('<p>You have no right to edit the template for this blog.<br>Ask for a promotion to your <a href="mailto:$admin_email">blog admin</a>. :)</p>');
	}

	if ('' == $file) {
		if ('' != $blogfilename) {
			$file = $blogfilename;
		} else {
			$file = 'index.php';
		}
	}
	
	if ('..' == substr($file,0,2))
		die ('Sorry, can&#8217;t edit files with ".." in the name. If you are trying to edit a file in your WordPress home directory, you can just type the name of the file in.');
	
	if (':' == substr($file,1,1))
		die ('Sorry, can&#8217;t call files with their real path.');

	if ('/' == substr($file,0,1))
		$file = '.' . $file;
	
	$file = stripslashes($file);
	$file = '../' . $file;
	
	if (!is_file($file))
		$error = 1;

	if ((substr($file,0,2) == 'wp') and (substr($file,-4,4) == '.php') and ($file != 'wp.php'))
		$warning = ' &#8212; this is a WordPress file, be careful when editing it!';
	
	if (!$error) {
		$f = fopen($file, 'r');
		$content = fread($f, filesize($file));
		$content = htmlspecialchars($content);
//		$content = str_replace("</textarea","&lt;/textarea",$content);
	}

	?>
 <div class="wrap"> 
  <?php
	echo "Editing <strong>$file</strong> $warning";
	if ('te' == $a)
		echo "<em>File edited successfully.</em>";
	
	if (!$error) {
	?> 
  <form name="template" action="templates.php" method="post"> 
     <textarea cols="80" rows="20" style="width:100%; font-family: 'Courier New', Courier, monopace; font-size:small;" name="newcontent" tabindex="1"><?php echo $content ?></textarea> 
     <input type="hidden" name="action" value="update" /> 
     <input type="hidden" name="file" value="<?php echo $file ?>" /> 
     <br /> 
     <?php
		if (is_writeable($file)) {
			echo "<input type='submit' name='submit' value='Update File' tabindex='2' />";
		} else {
			echo "<input type='button' name='oops' value='(You cannot update that file/template: must make it writable, e.g. CHMOD 666)' tabindex='2' />";
		}
		?> 
   </form> 
  <?php
	} else {
		echo '<p>Oops, no such file exists! Double check the name and try again, merci.</p>';
	}
	?> 
</div> 
<div class="wrap">
  <p>To edit a file, type its name here. You can edit any file writable by the server, e.g. CHMOD 766.</p> 
  <form name="file" action="templates.php" method="get"> 
    <input type="text" name="file" /> 
    <input type="submit" name="submit"  class="search" value="go" /> 
  </form> 
  <p>Common files:</p>
  <ul>
    <li><a href="templates.php?file=index.php">Main Index </a></li>
    <li><a href="templates.php?file=wp-comments.php">Comments</a></li>
    <li><a href="templates.php?file=wp-comments-popup.php">Popup comments </a></li>
    <li><a href="templates.php?file=.htaccess">.htaccess (for rewrite rules)</a></li>
    <li><a href="templates.php?file=my-hacks.php">my-hacks.php</a></li>
  </ul>
  <p>Note: of course, you can also edit the files/templates in your text editor of choice and upload them. This online editor is only meant to be used when you don't have access to a text editor or FTP client.</p>
</div> 
<?php

break;
}

include("admin-footer.php") ?> 