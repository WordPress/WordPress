<?php
require_once('admin.php');
$title = __('Template &amp; File Editing');
$parent_file = 	'edit.php';

wp_reset_vars(array('action', 'redirect', 'profile', 'error', 'warning', 'a', 'file'));

$recents = get_option('recently_edited');

if (empty($file)) {
	if ($recents) {
		$file = $recents[0];
	} else {
		$file = 'index.php';
	}
}

$file = validate_file_to_edit($file);
$real_file = get_real_file_to_edit($file);

switch($action) {

case 'update':

	check_admin_referer('edit-file_' . $file);

	if ( ! current_user_can('edit_files') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit templates for this blog.').'</p>');

	$newcontent = stripslashes($_POST['newcontent']);
	if (is_writeable($real_file)) {
		$f = @ fopen($real_file, 'w+');
		if ( $f ) {
			fwrite($f, $newcontent);
			fclose($f);
			wp_redirect("templates.php?file=$file&a=te");
		} else {
			wp_redirect("templates.php?file=$file&a=err");
		}
	} else {
		wp_redirect("templates.php?file=$file&a=err");
	}

	exit();

break;

default:

	require_once('./admin-header.php');

	if ( ! current_user_can('edit_files') )
		wp_die('<p>'.__('You do not have sufficient permissions to edit templates for this blog.').'</p>');

	if ( strstr( $file, 'wp-config.php' ) )
	wp_die('<p>'.__('The config file cannot be edited or viewed through the web interface. Sorry!').'</p>');

	update_recently_edited($file);

	if (!is_file($real_file))
		$error = true;

	if (!$error) {
		$f = @ fopen($real_file, 'r');
		if ( $f ) {
			$content = fread($f, filesize($real_file));
			$content = htmlspecialchars($content);
		} else {
			$error = true;
		}
	}

	?>
<?php if (isset($_GET['a'])) : ?>
	<?php if ( 'err' == $_GET['a'] ) : ?>
 <div id="message" class="error"><p><?php _e('Could not save file.') ?></p></div>
	<?php else: ?>
 <div id="message" class="updated fade"><p><?php _e('File edited successfully.') ?></p></div>
	<?php endif; ?>
<?php endif; ?>
 <div class="wrap"> 
<?php
if (is_writeable($real_file)) {
	echo '<h2>' . sprintf(__('Editing <strong>%s</strong>'), wp_specialchars($file) ) . '</h2>';
} else {
	echo '<h2>' . sprintf(__('Browsing <strong>%s</strong>'), wp_specialchars($file) ) . '</h2>';
}
?>
<div id="templateside">
<?php 
if ( $recents ) : 
?>
<h3><?php _e('Recent'); ?></h3>
<?php
echo '<ol>';
foreach ($recents as $recent) :
	echo "<li><a href='templates.php?file=$recent'>" . get_file_description(basename($recent)) . "</a></li>";
endforeach;
echo '</ol>';
endif;
?>
<h3><?php _e('Common'); ?></h3>
	<?php $common_files = array('index.php', '.htaccess', 'my-hacks.php');
 $old_files = array('wp-layout.css', 'wp-comments.php', 'wp-comments-popup.php');
 foreach ($old_files as $old_file) {
	 if (file_exists(ABSPATH . $old_file))
		 $common_files[] = $old_file;
 } ?>
  <ul>
	 <?php foreach ($common_files as $common_file) : ?>
	  <li><a href="templates.php?file=<?php echo $common_file?>"><?php echo get_file_description($common_file); ?></a></li>
	 <?php endforeach; ?>
  </ul>
</div>
<?php if (!$error) { ?>
  <form name="template" id="template" action="templates.php" method="post"> 
  <?php wp_nonce_field('edit-file_' . $file) ?>
     <div><textarea cols="70" rows="25" name="newcontent" id='newcontent' tabindex="1"><?php echo $content ?></textarea> 
     <input type="hidden" name="action" value="update" /> 
     <input type="hidden" name="file" value="<?php echo $file ?>" /> 
</div>
<?php if ( is_writeable($real_file) ) : ?>
     <p class="submit">
<?php
	echo "<input type='submit' name='submit' value='	" . __('Update File &raquo;') . "' tabindex='2' />";
?>
</p>
<?php else : ?>
<p><em><?php _e('If this file were writable you could edit it.'); ?></em></p>
<?php endif; ?>
   </form> 
  <?php
	} else {
		echo '<div class="error"><p>' . __('Oops, no such file exists! Double check the name and try again, merci.') . '</p></div>';
	}
	?>
<div class="clear"> &nbsp; </div>
</div>
<div class="wrap">
<h2><?php _e('Other Files') ?></h2>

  <p><?php _e('To edit a file, type its name here. You can edit any file <a href="http://codex.wordpress.org/Changing_File_Permissions" title="Read more about making files writable">writable by the server</a>, e.g. CHMOD 666.') ?></p> 
  <form name="file" action="templates.php" method="get"> 
    <input type="text" name="file" /> 
    <input type="submit" name="submit"  value="<?php _e('Edit file &raquo;') ?>" /> 
  </form> 

  <p><?php _e('Note: of course, you can also edit the files/templates in your text editor of choice and upload them. This online editor is only meant to be used when you don&#8217;t have access to a text editor or FTP client.') ?></p>
</div> 
<?php

break;
}

include("admin-footer.php");
?>
