<?php
require_once('../wp-includes/wp-l10n.php');

$title = __("Template &amp; file editing");

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

function validate_file($file) {
	if ('..' == substr($file,0,2))
		die (__('Sorry, can&#8217;t edit files with ".." in the name. If you are trying to edit a file in your WordPress home directory, you can just type the name of the file in.'));
	
	if (':' == substr($file,1,1))
		die (__('Sorry, can&#8217;t call files with their real path.'));

	if ('/' == substr($file,0,1))
		$file = '.' . $file;
	
	$file = stripslashes($file);
	$file = str_replace('../', '', $file);

    return $file;
}

if (!get_magic_quotes_gpc()) {
	$_GET    = add_magic_quotes($_GET);
	$_POST   = add_magic_quotes($_POST);
	$_COOKIE = add_magic_quotes($_COOKIE);
}

$wpvarstoreset = array('action','standalone','redirect','profile','error','warning','a','file');
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

switch($action) {

case 'update':

	$standalone = 1;
	require_once("admin-header.php");

	if ($user_level < 5) {
		die(__('<p>You have do not have sufficient permissions to edit templates for this blog.</p>'));
	}

	$newcontent = stripslashes($_POST['newcontent']);
	$file = $_POST['file'];
    $file = validate_file($file);
	$real_file = '../' . $file;
    if (is_writeable($real_file)) {
        $f = fopen($real_file, 'w+');
        fwrite($f, $newcontent);
        fclose($f);
        header("Location: templates.php?file=$file&a=te");
    } else {
        header("Location: templates.php?file=$file");
    }

	exit();

break;

default:

	require_once('admin-header.php');

	if ($user_level <= 5) {
		die(__('<p>You have do not have sufficient permissions to edit templates for this blog.</p>'));
	}

	if ('' == $file) {
		if ('' != get_settings('blogfilename')) {
			$file = get_settings('blogfilename');
		} else {
			$file = 'index.php';
		}
	}

    $file = validate_file($file);	
	$real_file = '../' . $file;
	
	if (!is_file($real_file))
		$error = 1;

	if ((substr($file,0,2) == 'wp') and (substr($file,-4,4) == '.php') and ($file != 'wp.php'))
		$warning = __(' &#8212; this is a WordPress file, be careful when editing it!');
	
	if (!$error) {
		$f = fopen($real_file, 'r');
		$content = fread($f, filesize($real_file));
		$content = htmlspecialchars($content);
//		$content = str_replace("</textarea","&lt;/textarea",$content);
	}

	?>
<?php if ('te' == $_GET['a']) : ?>
 <div class="updated"><p><?php _e('File edited successfully.') ?></p></div>
<?php endif; ?>
 <div class="wrap"> 
  <?php
	echo "<p>" . sprintf(__('Editing <strong>%s</strong>'), $file) . " $warning</p>";
	
	if (!$error) {
	?> 
  <form name="template" action="templates.php" method="post"> 
     <textarea cols="80" rows="21" style="width:98%; font-family: 'Courier New', Courier, monopace; font-size:small;" name="newcontent" tabindex="1"><?php echo $content ?></textarea> 
     <input type="hidden" name="action" value="update" /> 
     <input type="hidden" name="file" value="<?php echo $file ?>" /> 
     <p class="submit">
     <?php
		if (is_writeable($real_file)) {
			echo "<input type='submit' name='submit' value='Update File &raquo;' tabindex='2' />";
		} else {
			echo "<input type='button' name='oops' value='" . __('(You cannot update that file/template: must make it writable, e.g. CHMOD 666)') ."' tabindex='2' />";
		}
		?> 
</p>
   </form> 
  <?php
	} else {
		echo '<div class="error"><p>' . __('Oops, no such file exists! Double check the name and try again, merci.') . '</p></div>';
	}
	?> 
</div> 
<div class="wrap">
  <p><?php _e('To edit a file, type its name here. You can edit any file <a href="http://wiki.wordpress.org/index.php/MakeWritable" title="Read more about making files writable">writable by the server</a>, e.g. CHMOD 666.') ?></p> 
  <form name="file" action="templates.php" method="get"> 
    <input type="text" name="file" /> 
    <input type="submit" name="submit"  value="<?php _e('Edit file &raquo;') ?>" /> 
  </form> 
  <p><?php _e('Common files: (click to edit)') ?></p>
  <ul>
    <li><a href="templates.php?file=index.php"><?php _e('Main Index') ?> </a></li>
    <li><a href="templates.php?file=wp-comments.php">Comments</a></li>
    <li><a href="templates.php?file=wp-comments-popup.php"><?php _e('Popup comments') ?> </a></li>
    <li><a href="templates.php?file=.htaccess"><?php _e('.htaccess (for rewrite rules)') ?></a></li>
    <li><a href="templates.php?file=my-hacks.php"><?php _e('my-hacks.php (legacy hacks support)') ?></a></li>
    </ul>
<?php
$plugins_dir = @ dir(ABSPATH . 'wp-content/plugins');
if ($plugins_dir) {
	while(($file = $plugins_dir->read()) !== false) {
	  if ( !preg_match('|^\.+$|', $file) && preg_match('|\.php$|', $file) ) 
		$plugin_files[] = $file;
	}
}
if ($plugins_dir || $plugin_files) :
?>
  <p>Plugin files:</p>
  <ul>
<?php foreach($plugin_files as $plugin_file) : ?>
	<li><a href="templates.php?file=wp-content/plugins/<?php echo $plugin_file; ?>"><?php echo $plugin_file; ?></a></li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>
  <p><?php _e('Note: of course, you can also edit the files/templates in your text editor of choice and upload them. This online editor is only meant to be used when you don&#8217;t have access to a text editor or FTP client.') ?></p>
</div> 
<?php

break;
}

include("admin-footer.php") ?> 
