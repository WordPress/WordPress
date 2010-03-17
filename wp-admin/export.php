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
		$category = isset($_GET['category']) ? $_GET['category'] : 'all';
		$post_type = isset($_GET['post_type']) ? stripslashes_deep($_GET['post_type']) : 'all';
		$status = isset($_GET['status']) ? stripslashes_deep($_GET['status']) : 'all';
		$mm_start = isset($_GET['mm_start']) ? $_GET['mm_start'] : 'all';
		$mm_end = isset($_GET['mm_end']) ? $_GET['mm_end'] : 'all';
		$aa_start = isset($_GET['aa_start']) ? intval($_GET['aa_start']) : 0;
		$aa_end = isset($_GET['aa_end']) ? intval($_GET['aa_end']) : 0;
		if($mm_start != 'all' && $aa_start > 0) {
			$start_date = sprintf( "%04d-%02d-%02d", $aa_start, $mm_start, 1 );
		} else {
			$start_date = 'all';
		}
		if($mm_end != 'all' && $aa_end > 0) {
			if($mm_end == 12) {
				$mm_end = 1;
				$aa_end++;
			} else {
				$mm_end++;
			}
			$end_date = sprintf( "%04d-%02d-%02d", $aa_end, $mm_end, 1 );
		} else {
			$end_date = 'all';
		}
	export_wp( $author, $category, $post_type, $status, $start_date, $end_date );
	die();
}

require_once ('admin-header.php');

$months = "";
for ( $i = 1; $i < 13; $i++ ) {
	$months .= "\t\t\t<option value=\"" . zeroise($i, 2) . '">' .
		$wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
} ?>

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
<th><label for="mm_start"><?php _e('Restrict Date'); ?></label></th>
<td><strong><?php _e('Start:'); ?></strong> <?php _e('Month'); ?>&nbsp;
<select name="mm_start" id="mm_start">
<option value="all" selected="selected"><?php _e('All Dates'); ?></option>
<?php echo $months; ?>
</select>&nbsp;<?php _e('Year'); ?>&nbsp;
<input type="text" id="aa_start" name="aa_start" value="" size="4" maxlength="5" />
</td>
<td><strong><?php _e('End:'); ?></strong> <?php _e('Month'); ?>&nbsp;
<select name="mm_end" id="mm_end">
<option value="all" selected="selected"><?php _e('All Dates'); ?></option>
<?php echo $months; ?>
</select>&nbsp;<?php _e('Year'); ?>&nbsp;
<input type="text" id="aa_end" name="aa_end" value="" size="4" maxlength="5" />
</td>
</tr>
<tr>
<th><label for="author"><?php _e('Restrict Author'); ?></label></th>
<td>
<select name="author" id="author">
<option value="all" selected="selected"><?php _e('All Authors'); ?></option>
<?php
$authors = $wpdb->get_col( "SELECT post_author FROM $wpdb->posts GROUP BY post_author" );
foreach ( $authors as $id ) {
	$o = get_userdata( $id );
	echo "<option value='{$o->ID}'>{$o->display_name}</option>\n";
}
?>
</select>
</td>
</tr>
<tr>
<th><label for="category"><?php _e('Restrict Category'); ?></label></th>
<td>
<select name="category" id="category">
<option value="all" selected="selected"><?php _e('All Categories'); ?></option>
<?php
$categories = (array) get_categories('get=all');
if($categories) {
	foreach ( $categories as $cat ) {
		echo "<option value='{$cat->term_taxonomy_id}'>{$cat->name}</option>\n";
	}
}
?>
</select>
</td>
</tr>
<tr>
<th><label for="post_type"><?php _e('Restrict Content'); ?></label></th>
<td>
<select name="post_type" id="post_type">
<option value="all" selected="selected"><?php _e('All Content'); ?></option>
<option value="page"><?php _e('Pages'); ?></option>
<option value="post"><?php _e('Posts'); ?></option>
</select>
</td>
</tr>
<tr>
<th><label for="status"><?php _e('Restrict Status'); ?></label></th>
<td>
<select name="status" id="status">
<option value="all" selected="selected"><?php _e('All Statuses'); ?></option>
<option value="draft"><?php _e('Draft'); ?></option>
<option value="private"><?php _e('Privately published'); ?></option>
<option value="publish"><?php _e('Published'); ?></option>
<option value="future"><?php _e('Scheduled'); ?></option>
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
