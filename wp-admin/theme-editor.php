<?php
require_once('admin.php');

$title = __("Template &amp; file editing");
$parent_file = 'themes.php';

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

$wpvarstoreset = array('action','redirect','profile','error','warning','a','file', 'theme');
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
        header("Location: theme-editor.php?file=$file&a=te");
    } else {
        header("Location: theme-editor.php?file=$file");
    }

	exit();

break;

default:
	
	require_once('admin-header.php');
	if ($user_level <= 5) {
		die(__('<p>You have do not have sufficient permissions to edit themes for this blog.</p>'));
	}
	
	$themes = get_themes();

	if (! isset($theme)  || empty($theme)) {
		$theme = get_current_theme();
	}

	$stylesheet_files = $themes[$theme]['Stylesheet Files'];
	$template_files = $themes[$theme]['Template Files'];
	
	if ('' == $file) {
		$file = $stylesheet_files[0];
	}
	
	$home = get_settings('home');
	if (($home != '')
			&& ($home != get_settings('siteurl')) &&
			('index.php' == $file || get_settings('blogfilename') == $file ||
			 '.htaccess' == $file)) {
		$home_root = parse_url($home);
		$home_root = $home_root['path'];
		$root = str_replace($_SERVER["PHP_SELF"], '', $_SERVER["PATH_TRANSLATED"]);
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
  <form name="theme" action="theme-editor.php" method="post"> 
		<?php _e('Select theme to edit:') ?>
		<select name="theme" id="theme">
	<?php
		foreach ($themes as $a_theme) {
		$theme_name = $a_theme['Name'];
		if ($theme_name == $theme) $selected = " selected='selected'";
		else $selected = '';
		echo "\n\t<option value='$theme_name' $selected>$theme_name</option>";
	}
?>
 </select>
 <input type="submit" name="Submit" value="<?php _e('Select') ?> &raquo;" />
 </form>
 </div>

 <div class="wrap"> 
  <?php
	echo "<p>" . sprintf(__('Editing <strong>%s</strong>'), $file) . "</p>";
	
	if (!$error) {
	?> 
  <form name="template" action="theme-editor.php" method="post"> 
     <textarea cols="80" rows="21" style="width:95%; margin-right: 10em; font-family: 'Courier New', Courier, monopace; font-size:small;" name="newcontent" tabindex="1"><?php echo $content ?></textarea> 
     <input type="hidden" name="action" value="update" /> 
     <input type="hidden" name="file" value="<?php echo $file ?>" /> 
     <input type="hidden" name="theme" value="<?php echo $theme ?>" /> 
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
<?php

if ($template_files || $stylesheet_files) :
?>
  <p><?php printf(__('<strong>%s</strong> theme files:'), $theme) ?></p>
  <ul>
<?php foreach($stylesheet_files as $stylesheet_file) : ?>
		 <li><a href="theme-editor.php?file=<?php echo "$stylesheet_file"; ?>&amp;theme=<?php echo $theme; ?>"><?php echo basename($stylesheet_file); ?></a></li>
<?php endforeach; ?>
<?php foreach($template_files as $template_file) : ?>
		<li><a href="theme-editor.php?file=<?php echo "$template_file"; ?>&amp;theme=<?php echo $theme; ?>"><?php echo basename($template_file); ?></a></li>
<?php endforeach; ?>
  </ul>
<?php endif; ?>
  <p><?php _e('Note: of course, you can also edit the files/templates in your text editor of choice and upload them. This online editor is only meant to be used when you don&#8217;t have access to a text editor or FTP client.') ?></p>
</div> 
<?php

break;
}

include("admin-footer.php") ?> 
