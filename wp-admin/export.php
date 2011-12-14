<?php
/**
 * WordPress Export Administration Screen
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

function add_js() {
?>
<script type="text/javascript">
//<![CDATA[
	jQuery(document).ready(function($){
 		var form = $('#export-filters'),
 			filters = form.find('.export-filters');
 		filters.hide();
 		form.find('input:radio').change(function() {
			filters.slideUp('fast');
			switch ( $(this).val() ) {
				case 'posts': $('#post-filters').slideDown(); break;
				case 'pages': $('#page-filters').slideDown(); break;
			}
 		});
	});
//]]>
</script>
<?php
}
add_action( 'admin_head', 'add_js' );

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => '<p>' . __('You can export a file of your site&#8217;s content in order to import it into another installation or platform. The export file will be an XML file format called WXR. Posts, pages, comments, custom fields, categories, and tags can be included. You can choose for the WXR file to include only certain posts or pages by setting the dropdown filters to limit the export by category, author, date range by month, or publishing status.') . '</p>' .
		'<p>' . __('Once generated, your WXR file can be imported by another WordPress site or by another blogging platform able to access this format.') . '</p>',
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Tools_Export_Screen" target="_blank">Documentation on Export</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

if ( isset( $_GET['download'] ) ) {
	$args = array();

	if ( ! isset( $_GET['content'] ) || 'all' == $_GET['content'] ) {
		$args['content'] = 'all';
	} else if ( 'posts' == $_GET['content'] ) {
		$args['content'] = 'post';

		if ( $_GET['cat'] )
			$args['category'] = (int) $_GET['cat'];

		if ( $_GET['post_author'] )
			$args['author'] = (int) $_GET['post_author'];

		if ( $_GET['post_start_date'] || $_GET['post_end_date'] ) {
			$args['start_date'] = $_GET['post_start_date'];
			$args['end_date'] = $_GET['post_end_date'];
		}

		if ( $_GET['post_status'] )
			$args['status'] = $_GET['post_status'];
	} else if ( 'pages' == $_GET['content'] ) {
		$args['content'] = 'page';

		if ( $_GET['page_author'] )
			$args['author'] = (int) $_GET['page_author'];

		if ( $_GET['page_start_date'] || $_GET['page_end_date'] ) {
			$args['start_date'] = $_GET['page_start_date'];
			$args['end_date'] = $_GET['page_end_date'];
		}

		if ( $_GET['page_status'] )
			$args['status'] = $_GET['page_status'];
	} else {
		$args['content'] = $_GET['content'];
	}

	export_wp( $args );
	die();
}

require_once ('admin-header.php');

function export_date_options() {
	global $wpdb, $wp_locale;

	$months = $wpdb->get_results( "
		SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
		FROM $wpdb->posts
		WHERE post_type = 'post' AND post_status != 'auto-draft'
		ORDER BY post_date DESC
	" );

	$month_count = count( $months );
	if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
		return;

	foreach ( $months as $date ) {
		if ( 0 == $date->year )
			continue;

		$month = zeroise( $date->month, 2 );
		echo '<option value="' . $date->year . '-' . $month . '">' . $wp_locale->get_month( $month ) . ' ' . $date->year . '</option>';
	}
}
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<p><?php _e('When you click the button below WordPress will create an XML file for you to save to your computer.'); ?></p>
<p><?php _e('This format, which we call WordPress eXtended RSS or WXR, will contain your posts, pages, comments, custom fields, categories, and tags.'); ?></p>
<p><?php _e('Once you&#8217;ve saved the download file, you can use the Import function in another WordPress installation to import the content from this site.'); ?></p>

<h3><?php _e( 'Choose what to export' ); ?></h3>
<form action="" method="get" id="export-filters">
<input type="hidden" name="download" value="true" />
<p><label><input type="radio" name="content" value="all" checked="checked" /> <?php _e( 'All content' ); ?></label>
<span class="description"><?php _e( 'This will contain all of your posts, pages, comments, custom fields, terms, navigation menus and custom posts.' ); ?></span></p>

<p><label><input type="radio" name="content" value="posts" /> <?php _e( 'Posts' ); ?></label></p>
<ul id="post-filters" class="export-filters">
	<li>
		<label><?php _e( 'Categories:' ); ?></label>
		<?php wp_dropdown_categories( array( 'show_option_all' => __('All') ) ); ?>
	</li>
	<li>
		<label><?php _e( 'Authors:' ); ?></label>
<?php
		$authors = $wpdb->get_col( "SELECT DISTINCT post_author FROM {$wpdb->posts} WHERE post_type = 'post'" );
		wp_dropdown_users( array( 'include' => $authors, 'name' => 'post_author', 'multi' => true, 'show_option_all' => __('All') ) );
?>
	</li>
	<li>
		<label><?php _e( 'Date range:' ); ?></label>
		<select name="post_start_date">
			<option value="0"><?php _e( 'Start Date' ); ?></option>
			<?php export_date_options(); ?>
		</select>
		<select name="post_end_date">
			<option value="0"><?php _e( 'End Date' ); ?></option>
			<?php export_date_options(); ?>
		</select>
	</li>
	<li>
		<label><?php _e( 'Status:' ); ?></label>
		<select name="post_status">
			<option value="0"><?php _e( 'All' ); ?></option>
			<?php $post_stati = get_post_stati( array( 'internal' => false ), 'objects' );
			foreach ( $post_stati as $status ) : ?>
			<option value="<?php echo esc_attr( $status->name ); ?>"><?php echo esc_html( $status->label ); ?></option>
			<?php endforeach; ?>
		</select>
	</li>
</ul>

<p><label><input type="radio" name="content" value="pages" /> <?php _e( 'Pages' ); ?></label></p>
<ul id="page-filters" class="export-filters">
	<li>
		<label><?php _e( 'Authors:' ); ?></label>
<?php
		$authors = $wpdb->get_col( "SELECT DISTINCT post_author FROM {$wpdb->posts} WHERE post_type = 'page'" );
		wp_dropdown_users( array( 'include' => $authors, 'name' => 'page_author', 'multi' => true, 'show_option_all' => __('All') ) );
?>
	</li>
	<li>
		<label><?php _e( 'Date range:' ); ?></label>
		<select name="page_start_date">
			<option value="0"><?php _e( 'Start Date' ); ?></option>
			<?php export_date_options(); ?>
		</select>
		<select name="page_end_date">
			<option value="0"><?php _e( 'End Date' ); ?></option>
			<?php export_date_options(); ?>
		</select>
	</li>
	<li>
		<label><?php _e( 'Status:' ); ?></label>
		<select name="page_status">
			<option value="0"><?php _e( 'All' ); ?></option>
			<?php foreach ( $post_stati as $status ) : ?>
			<option value="<?php echo esc_attr( $status->name ); ?>"><?php echo esc_html( $status->label ); ?></option>
			<?php endforeach; ?>
		</select>
	</li>
</ul>

<?php foreach ( get_post_types( array( '_builtin' => false, 'can_export' => true ), 'objects' ) as $post_type ) : ?>
<p><label><input type="radio" name="content" value="<?php echo esc_attr( $post_type->name ); ?>" /> <?php echo esc_html( $post_type->label ); ?></label></p>
<?php endforeach; ?>

<?php submit_button( __('Download Export File'), 'secondary' ); ?>
</form>
</div>

<?php include('admin-footer.php'); ?>
