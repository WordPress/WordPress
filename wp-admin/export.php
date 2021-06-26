<?php
/**
 * WordPress Export Administration Screen
 *
 * @package WordPress
 * @subpackage Administration
 */

/** Load WordPress Bootstrap */
require_once __DIR__ . '/admin.php';

if ( ! current_user_can( 'export' ) ) {
	wp_die( __( 'Sorry, you are not allowed to export the content of this site.' ) );
}

/** Load WordPress export API */
require_once ABSPATH . 'wp-admin/includes/export.php';
$title = __( 'Export' );

/**
 * Display JavaScript on the page.
 *
 * @since 3.5.0
 */
function export_add_js() {
	?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		var form = $('#export-filters'),
			filters = form.find('.export-filters');
		filters.hide();
		form.find('input:radio').on( 'change', function() {
			filters.slideUp('fast');
			switch ( $(this).val() ) {
				case 'attachment': $('#attachment-filters').slideDown(); break;
				case 'posts': $('#post-filters').slideDown(); break;
				case 'pages': $('#page-filters').slideDown(); break;
			}
		});
	});
</script>
	<?php
}
add_action( 'admin_head', 'export_add_js' );

get_current_screen()->add_help_tab(
	array(
		'id'      => 'overview',
		'title'   => __( 'Overview' ),
		'content' => '<p>' . __( 'You can export a file of your site&#8217;s content in order to import it into another installation or platform. The export file will be an XML file format called WXR. Posts, pages, comments, custom fields, categories, and tags can be included. You can choose for the WXR file to include only certain posts or pages by setting the dropdown filters to limit the export by category, author, date range by month, or publishing status.' ) . '</p>' .
			'<p>' . __( 'Once generated, your WXR file can be imported by another WordPress site or by another blogging platform able to access this format.' ) . '</p>',
	)
);

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __( 'For more information:' ) . '</strong></p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/article/tools-export-screen/">Documentation on Export</a>' ) . '</p>' .
	'<p>' . __( '<a href="https://wordpress.org/support/">Support</a>' ) . '</p>'
);

// If the 'download' URL parameter is set, a WXR export file is baked and returned.
if ( isset( $_GET['download'] ) ) {
	$args = array();

	if ( ! isset( $_GET['content'] ) || 'all' === $_GET['content'] ) {
		$args['content'] = 'all';
	} elseif ( 'posts' === $_GET['content'] ) {
		$args['content'] = 'post';

		if ( $_GET['cat'] ) {
			$args['category'] = (int) $_GET['cat'];
		}

		if ( $_GET['post_author'] ) {
			$args['author'] = (int) $_GET['post_author'];
		}

		if ( $_GET['post_start_date'] || $_GET['post_end_date'] ) {
			$args['start_date'] = $_GET['post_start_date'];
			$args['end_date']   = $_GET['post_end_date'];
		}

		if ( $_GET['post_status'] ) {
			$args['status'] = $_GET['post_status'];
		}
	} elseif ( 'pages' === $_GET['content'] ) {
		$args['content'] = 'page';

		if ( $_GET['page_author'] ) {
			$args['author'] = (int) $_GET['page_author'];
		}

		if ( $_GET['page_start_date'] || $_GET['page_end_date'] ) {
			$args['start_date'] = $_GET['page_start_date'];
			$args['end_date']   = $_GET['page_end_date'];
		}

		if ( $_GET['page_status'] ) {
			$args['status'] = $_GET['page_status'];
		}
	} elseif ( 'attachment' === $_GET['content'] ) {
		$args['content'] = 'attachment';

		if ( $_GET['attachment_start_date'] || $_GET['attachment_end_date'] ) {
			$args['start_date'] = $_GET['attachment_start_date'];
			$args['end_date']   = $_GET['attachment_end_date'];
		}
	} else {
		$args['content'] = $_GET['content'];
	}

	/**
	 * Filters the export args.
	 *
	 * @since 3.5.0
	 *
	 * @param array $args The arguments to send to the exporter.
	 */
	$args = apply_filters( 'export_args', $args );

	export_wp( $args );
	die();
}

require_once ABSPATH . 'wp-admin/admin-header.php';

/**
 * Create the date options fields for exporting a given post type.
 *
 * @global wpdb      $wpdb      WordPress database abstraction object.
 * @global WP_Locale $wp_locale WordPress date and time locale object.
 *
 * @since 3.1.0
 *
 * @param string $post_type The post type. Default 'post'.
 */
function export_date_options( $post_type = 'post' ) {
	global $wpdb, $wp_locale;

	$months = $wpdb->get_results(
		$wpdb->prepare(
			"
		SELECT DISTINCT YEAR( post_date ) AS year, MONTH( post_date ) AS month
		FROM $wpdb->posts
		WHERE post_type = %s AND post_status != 'auto-draft'
		ORDER BY post_date DESC
			",
			$post_type
		)
	);

	$month_count = count( $months );
	if ( ! $month_count || ( 1 === $month_count && 0 === (int) $months[0]->month ) ) {
		return;
	}

	foreach ( $months as $date ) {
		if ( 0 === (int) $date->year ) {
			continue;
		}

		$month = zeroise( $date->month, 2 );
		echo '<option value="' . $date->year . '-' . $month . '">' . $wp_locale->get_month( $month ) . ' ' . $date->year . '</option>';
	}
}
?>

<div class="wrap">
<h1><?php echo esc_html( $title ); ?></h1>

<p><?php _e( 'When you click the button below WordPress will create an XML file for you to save to your computer.' ); ?></p>
<p><?php _e( 'This format, which we call WordPress eXtended RSS or WXR, will contain your posts, pages, comments, custom fields, categories, and tags.' ); ?></p>
<p><?php _e( 'Once you&#8217;ve saved the download file, you can use the Import function in another WordPress installation to import the content from this site.' ); ?></p>

<h2><?php _e( 'Choose what to export' ); ?></h2>
<form method="get" id="export-filters">
<fieldset>
<legend class="screen-reader-text"><?php _e( 'Content to export' ); ?></legend>
<input type="hidden" name="download" value="true" />
<p><label><input type="radio" name="content" value="all" checked="checked" aria-describedby="all-content-desc" /> <?php _e( 'All content' ); ?></label></p>
<p class="description" id="all-content-desc"><?php _e( 'This will contain all of your posts, pages, comments, custom fields, terms, navigation menus, and custom posts.' ); ?></p>

<p><label><input type="radio" name="content" value="posts" /> <?php _ex( 'Posts', 'post type general name' ); ?></label></p>
<ul id="post-filters" class="export-filters">
	<li>
		<label><span class="label-responsive"><?php _e( 'Categories:' ); ?></span>
		<?php wp_dropdown_categories( array( 'show_option_all' => __( 'All' ) ) ); ?>
		</label>
	</li>
	<li>
		<label><span class="label-responsive"><?php _e( 'Authors:' ); ?></span>
		<?php
		$authors = $wpdb->get_col( "SELECT DISTINCT post_author FROM {$wpdb->posts} WHERE post_type = 'post'" );
		wp_dropdown_users(
			array(
				'include'         => $authors,
				'name'            => 'post_author',
				'multi'           => true,
				'show_option_all' => __( 'All' ),
				'show'            => 'display_name_with_login',
			)
		);
		?>
		</label>
	</li>
	<li>
		<fieldset>
		<legend class="screen-reader-text"><?php _e( 'Date range:' ); ?></legend>
		<label for="post-start-date" class="label-responsive"><?php _e( 'Start date:' ); ?></label>
		<select name="post_start_date" id="post-start-date">
			<option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
			<?php export_date_options(); ?>
		</select>
		<label for="post-end-date" class="label-responsive"><?php _e( 'End date:' ); ?></label>
		<select name="post_end_date" id="post-end-date">
			<option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
			<?php export_date_options(); ?>
		</select>
		</fieldset>
	</li>
	<li>
		<label for="post-status" class="label-responsive"><?php _e( 'Status:' ); ?></label>
		<select name="post_status" id="post-status">
			<option value="0"><?php _e( 'All' ); ?></option>
			<?php
			$post_stati = get_post_stati( array( 'internal' => false ), 'objects' );
			foreach ( $post_stati as $status ) :
				?>
			<option value="<?php echo esc_attr( $status->name ); ?>"><?php echo esc_html( $status->label ); ?></option>
			<?php endforeach; ?>
		</select>
	</li>
</ul>

<p><label><input type="radio" name="content" value="pages" /> <?php _e( 'Pages' ); ?></label></p>
<ul id="page-filters" class="export-filters">
	<li>
		<label><span class="label-responsive"><?php _e( 'Authors:' ); ?></span>
		<?php
		$authors = $wpdb->get_col( "SELECT DISTINCT post_author FROM {$wpdb->posts} WHERE post_type = 'page'" );
		wp_dropdown_users(
			array(
				'include'         => $authors,
				'name'            => 'page_author',
				'multi'           => true,
				'show_option_all' => __( 'All' ),
				'show'            => 'display_name_with_login',
			)
		);
		?>
		</label>
	</li>
	<li>
		<fieldset>
		<legend class="screen-reader-text"><?php _e( 'Date range:' ); ?></legend>
		<label for="page-start-date" class="label-responsive"><?php _e( 'Start date:' ); ?></label>
		<select name="page_start_date" id="page-start-date">
			<option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
			<?php export_date_options( 'page' ); ?>
		</select>
		<label for="page-end-date" class="label-responsive"><?php _e( 'End date:' ); ?></label>
		<select name="page_end_date" id="page-end-date">
			<option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
			<?php export_date_options( 'page' ); ?>
		</select>
		</fieldset>
	</li>
	<li>
		<label for="page-status" class="label-responsive"><?php _e( 'Status:' ); ?></label>
		<select name="page_status" id="page-status">
			<option value="0"><?php _e( 'All' ); ?></option>
			<?php foreach ( $post_stati as $status ) : ?>
			<option value="<?php echo esc_attr( $status->name ); ?>"><?php echo esc_html( $status->label ); ?></option>
			<?php endforeach; ?>
		</select>
	</li>
</ul>

<?php
foreach ( get_post_types(
	array(
		'_builtin'   => false,
		'can_export' => true,
	),
	'objects'
) as $post_type ) :
	?>
<p><label><input type="radio" name="content" value="<?php echo esc_attr( $post_type->name ); ?>" /> <?php echo esc_html( $post_type->label ); ?></label></p>
<?php endforeach; ?>

<p><label><input type="radio" name="content" value="attachment" /> <?php _e( 'Media' ); ?></label></p>
<ul id="attachment-filters" class="export-filters">
	<li>
		<fieldset>
		<legend class="screen-reader-text"><?php _e( 'Date range:' ); ?></legend>
		<label for="attachment-start-date" class="label-responsive"><?php _e( 'Start date:' ); ?></label>
		<select name="attachment_start_date" id="attachment-start-date">
			<option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
			<?php export_date_options( 'attachment' ); ?>
		</select>
		<label for="attachment-end-date" class="label-responsive"><?php _e( 'End date:' ); ?></label>
		<select name="attachment_end_date" id="attachment-end-date">
			<option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
			<?php export_date_options( 'attachment' ); ?>
		</select>
		</fieldset>
	</li>
</ul>

</fieldset>
<?php
/**
 * Fires at the end of the export filters form.
 *
 * @since 3.5.0
 */
do_action( 'export_filters' );
?>

<?php submit_button( __( 'Download Export File' ) ); ?>
</form>
</div>

<?php require_once ABSPATH . 'wp-admin/admin-footer.php'; ?>
