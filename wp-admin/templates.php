<?php
require_once('admin.php');
$title = __('Template &amp; file editing');
$parent_file = 	'themes.php';

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

$wpvarstoreset = array('action','redirect','profile','error','warning','a','file');
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

	require_once('./admin-header.php');
	if ($user_level <= 5) {
		die(__('<p>You have do not have sufficient permissions to edit templates for this blog.</p>'));
	}

	if ('' == $file) {
		$file = 'index.php';
	} else {
		$oldfiles = (array) get_option('recently_edited');
		if ($oldfiles) {
			$oldfiles = array_reverse($oldfiles);
			$oldfiles[] = $file;
			$oldfiles = array_reverse($oldfiles);
			$oldfiles = array_unique($oldfiles);
			if ( 5 < count($oldfiles) )
				array_pop($oldfiles);
		} else {
			$oldfiles[] = $file;
		}
		update_option('recently_edited', $oldfiles);
	}

    $home = get_settings('home');
    if (($home != '' && $home != get_settings('siteurl')) &&
      ('index.php' == $file || get_settings('blogfilename') == $file ||
       '.htaccess' == $file)) {
        $home_root = parse_url($home);
	$home_root = $home_root['path'];
	$root = str_replace($_SERVER['PHP_SELF'], '', $_SERVER['PATH_TRANSLATED']);
	$home_root = $root . $home_root;
        $real_file = $home_root . '/' . $file;
    } else {
        $file = validate_file($file);
        $real_file = '../' . $file;
    }
	
	if (!is_file($real_file))
		$error = 1;
	
	if (!$error) {
		$f = fopen($real_file, 'r');
		$content = fread($f, filesize($real_file));
		$content = htmlspecialchars($content);
	}

	?>
<?php if (isset($_GET['a'])) : ?>
 <div class="updated"><p><?php _e('File edited successfully.') ?></p></div>
<?php endif; ?>
 <div class="wrap"> 
<?php
if (is_writeable($real_file)) {
	echo '<h2>' . sprintf(__('Editing <strong>%s</strong>'), $file) . '</h2>';
} else {
	echo '<h2>' . sprintf(__('Browsing <strong>%s</strong>'), $file) . '</h2>';
}
?>
<div id="templateside">
<?php 
if ( $recents = get_option('recently_edited') ) : 
?>
<h3><?php _e('Recent'); ?></h3>
<?php
echo '<ol>';
foreach ($recents as $recent) :
	$display = preg_replace('|.*/(.*)$|', '$1', $recent);
	echo "<li><a href='templates.php?file=$recent'>$display</a>";
endforeach;
echo '</ol>';
endif;
?>
<h3><?php _e('Common'); ?></h3>
  <ul>
    <li><a href="templates.php?file=index.php"><?php _e('Main Index') ?></a></li>
    <li><a href="templates.php?file=wp-layout.css"><?php _e('Main Stylesheet') ?></a></li>
    <li><a href="templates.php?file=wp-comments.php"><?php _e('Comments') ?></a></li>
    <li><a href="templates.php?file=wp-comments-popup.php"><?php _e('Popup comments') ?></a></li>
    <li><a href="templates.php?file=.htaccess"><?php _e('.htaccess (for rewrite rules)') ?></a></li>
    <li><a href="templates.php?file=my-hacks.php"><?php _e('my-hacks.php (legacy hacks support)') ?></a></li>
    </ul>
</div>
<?php if (!$error) { ?>
  <form name="template" id="template" action="templates.php" method="post"> 
     <div><textarea cols="70" rows="25" name="newcontent" id='newcontent' tabindex="1"><?php echo $content ?></textarea> 
     <input type="hidden" name="action" value="update" /> 
     <input type="hidden" name="file" value="<?php echo $file ?>" /> 
</div>
<?php if ( is_writeable($real_file) ) : ?>
     <p class="submit">
<?php
	echo "<input type='submit' name='submit' value='	" . __('Update File') . " &raquo;' tabindex='2' />";
?>
</p>
<?php else : ?>
<p><em><?php _e('If this file was writable you could edit it.'); ?></em></p>
<?php endif; ?>
   </form> 
  <?php
	} else {
		echo '<div class="error"><p>' . __('Oops, no such file exists! Double check the name and try again, merci.') . '</p></div>';
	}
	?>
</div>
<div class="wrap">
<h2>Other Files</h2>

  <p><?php _e('To edit a file, type its name here. You can edit any file <a href="http://wiki.wordpress.org/index.php/MakeWritable" title="Read more about making files writable">writable by the server</a>, e.g. CHMOD 666.') ?></p> 
  <form name="file" action="templates.php" method="get"> 
    <input type="text" name="file" /> 
    <input type="submit" name="submit"  value="<?php _e('Edit file &raquo;') ?>" /> 
  </form> 

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
