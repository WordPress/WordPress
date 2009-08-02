<?php
/**
 * WordPress Export Administration Panel
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once ('admin.php');

if ( !current_user_can('edit_files') )
	wp_die(__('You do not have sufficient permissions to export the content of this blog.'));

/** Load WordPress export API */
require_once('includes/export.php');
$title = __('Export');

if ( isset( $_GET['download'] ) ) {
	$author = isset($_GET['author']) ? $_GET['author'] : 'all';
	export_wp( $author );
	die();
}

require_once ('admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<p><?php _e('When you click the button below WordPress will create an XML file for you to save to your computer.'); ?></p>
<p><?php _e('This format, which we call WordPress eXtended RSS or WXR, will contain your posts, pages, comments, custom fields, categories, and tags.'); ?></p>
<p><?php _e('Once you&#8217;ve saved the download file, you can use the Import function on another WordPress blog to import this blog.'); ?></p>
<form action="" method="get">
<h3><?php _e('Options'); ?></h3>

<table class="form-table">
<tr>
<th><label for="author"><?php _e('Restrict Author'); ?></label></th>
<td>
<select name="author" id="author">
<option value="all" selected="selected"><?php _e('All Authors'); ?></option>
<?php
$authors = $wpdb->get_col( "SELECT post_author FROM $wpdb->posts GROUP BY post_author" );
foreach ( $authors as $id ) {
	$o = get_userdata( $id );
	echo "<option value='" . esc_attr($o->ID) . "'>$o->display_name</option>";
}
?>
</select>
</td>
</tr>
</table>
<p class="submit"><input type="submit" name="submit" class="button" value="<?php esc_attr_e('Download Export File'); ?>" />
<input type="hidden" name="download" value="true" />
</p>
</form>
</div>

<?php


include ('admin-footer.php');
?>
