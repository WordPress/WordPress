<?php
/**
 * WordPress Export Administration Panel
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once ('admin.php');

if ( !current_user_can('export') )
	wp_die(__('You do not have sufficient permissions to export the content of this site.'));

/** Load WordPress export API */
require_once('./includes/export.php');
$title = __('Export');

add_contextual_help($current_screen,
	'<p>' . __('You can export a file of your site&#8217;s content in order to import it into another installation or platform. The export file will be an XML file format called WXR. Posts, pages, comments, custom fields, categories, and tags can be included. You can set filters to have the WXR file only include a certain date, author, category, tag, all posts or all pages, certain publishing statuses.') . '</p>' .
	'<p>' . __('Once generated, your WXR file can be imported by another WordPress site or by another blogging platform able to access this format.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Tools_Export_SubPanel" target="_blank">Export Documentation</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

if ( isset( $_GET['download'] ) ) {
	export_wp();
	die();
}

require_once ('admin-header.php');

$dateoptions = $edateoptions = '';
$types = "'" . implode("', '", get_post_types( array( 'public' => true, 'can_export' => true ), 'names' )) . "'";
$stati = "'" . implode("', '", get_post_stati( array( 'internal' => false ), 'names' )) . "'";
if ( $monthyears = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, YEAR(DATE_ADD(post_date, INTERVAL 1 MONTH)) AS `eyear`, MONTH(DATE_ADD(post_date, INTERVAL 1 MONTH)) AS `emonth` FROM $wpdb->posts WHERE post_type IN ($types) AND post_status IN ($stati) ORDER BY post_date ASC ") ) {
	foreach ( $monthyears as $k => $monthyear )
		$monthyears[$k]->lmonth = $wp_locale->get_month( $monthyear->month, 2 );
	for( $s = 0, $e = count( $monthyears ) - 1; $e >= 0; $s++, $e-- ) {
		$dateoptions .= "\t<option value=\"" . $monthyears[$s]->year . '-' . zeroise( $monthyears[$s]->month, 2 ) . '">' . $monthyears[$s]->lmonth . ' ' . $monthyears[$s]->year . "</option>\n";
		$edateoptions .= "\t<option value=\"" . $monthyears[$e]->eyear . '-' . zeroise( $monthyears[$e]->emonth, 2 ) . '">' . $monthyears[$e]->lmonth . ' ' . $monthyears[$e]->year . "</option>\n";
	}
}

?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<p><?php _e('When you click the button below WordPress will create an XML file for you to save to your computer.'); ?></p>
<p><?php _e('This format, which we call WordPress eXtended RSS or WXR, will contain your posts, pages, comments, custom fields, categories, and tags.'); ?></p>
<p><?php _e('Once you&#8217;ve saved the download file, you can use the Import function on another WordPress site to import this site.'); ?></p>
<form action="" method="get">
<?php submit_button( __('Download Export File'), 'secondary' ); ?>
<input type="hidden" name="download" value="true" />
</p>
</form>
</div>

<?php


include ('admin-footer.php');
?>
