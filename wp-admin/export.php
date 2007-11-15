<?php
require_once ('admin.php');
require_once('includes/export.php');
$title = __('Export');
$parent_file = 'edit.php';

if ( isset( $_GET['download'] ) ) {	
	export_wp( $_GET['author'] );
	die();
}

require_once ('admin-header.php');
?>

<div class="wrap">
<h2><?php _e('Export'); ?></h2>
<div class="narrow">
<p><?php _e('When you click the button below WordPress will create an XML file for you to save to your computer.'); ?></p>
<p><?php _e('This format, which we call WordPress eXtended RSS or WXR, will contain your posts, comments, custom fields, and categories.'); ?></p>
<p><?php _e('Once you&#8217;ve saved the download file, you can use the Import function on another WordPress blog to import this blog.'); ?></p>
<form action="" method="get">
<h3><?php _e('Optional options'); ?></h3>

<table>
<tr>
<th><?php _e('Restrict Author:'); ?></th>
<td>
<select name="author">
<option value="all" selected="selected"><?php _e('All'); ?></option>
<?php
$authors = $wpdb->get_col( "SELECT post_author FROM $wpdb->posts GROUP BY post_author" );
foreach ( $authors as $id ) {
	$o = get_userdata( $id );
	echo "<option value='$o->ID'>$o->display_name</option>";
}
?>
</select>
</td>
</tr>
</table>
<p class="submit"><input type="submit" name="submit" value="<?php _e('Download Export File'); ?> &raquo;" />
<input type="hidden" name="download" value="true" />
</p>
</form>
</div>
</div>

<?php


include ('admin-footer.php');
?>
