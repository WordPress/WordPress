<?php
require_once('admin.php');

$title = __("Edit Plugins");
$parent_file = 'plugins.php';

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

$plugins_dir = @ dir(ABSPATH . 'wp-content/plugins');
if ($plugins_dir) {
	while(($plug_file = $plugins_dir->read()) !== false) {
		if ( !preg_match('|^\.+$|', $plug_file) && preg_match('|\.php$|', $plug_file) ) 
			$plugin_files[] = "wp-content/plugins/$plug_file";
	}
}

if (count($plugin_files)) {
	natcasesort($plugin_files);
}

if (file_exists(ABSPATH . 'my-hacks.php')) {
	$plugin_files[] = 'my-hacks.php';
}


if (empty($file)) {
	$file = $plugin_files[0];
}

$file = validate_file_to_edit($file, $plugin_files);
$real_file = get_real_file_to_edit($file);

switch($action) {

case 'update':

	if ($user_level < 5) {
		die(__('<p>You have do not have sufficient permissions to edit templates for this blog.</p>'));
	}

	$newcontent = stripslashes($_POST['newcontent']);
	if (is_writeable($real_file)) {
		$f = fopen($real_file, 'w+');
		fwrite($f, $newcontent);
		fclose($f);
		header("Location: plugin-editor.php?file=$file&a=te");
	} else {
		header("Location: plugin-editor.php?file=$file");
	}

	exit();

break;

default:
	
	require_once('admin-header.php');
	if ($user_level <= 5) {
		die(__('<p>You have do not have sufficient permissions to edit plugins for this blog.</p>'));
	}

	update_recently_edited($file);
	
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
<h3><?php _e('Plugin files') ?></h3>

<?php
if ($plugin_files) :
?>
  <ul>
<?php foreach($plugin_files as $plugin_file) : ?>
		 <li><a href="plugin-editor.php?file=<?php echo "$plugin_file"; ?>"><?php echo get_file_description(basename($plugin_file)); ?></a></li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>
</div>
	<?php	if (!$error) { ?> 
  <form name="template" id="template" action="plugin-editor.php" method="post">
		 <div><textarea cols="70" rows="25" name="newcontent" id="newcontent" tabindex="1"><?php echo $content ?></textarea> 
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
<?php
break;
}

include("admin-footer.php") ?> 
