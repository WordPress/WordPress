<?php
/**
 * Reading settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once( './admin.php' );

if ( ! current_user_can( 'manage_options' ) )
	wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );

$title = __( 'Reading Settings' );
$parent_file = 'options-general.php';

wp_enqueue_script( 'sample-permalink' );

/**
 * Display JavaScript on the page.
 *
 * @since 3.5.0
 */
function options_reading_add_js() {
?>
<script>
jQuery(document).ready( function($) {
	var section = $('#front-static-pages');
	$('#show_on_front').change( function() {
		var checked = $(this).prop('checked');
		section.toggleClass('page-on-front', checked);
		if ( checked )
			$('#page_for_posts').prop('checked', true).change();
		else
			section.removeClass('page-for-posts');
	});
	$('#page_for_posts').change( function() {
		section.toggleClass('page-for-posts', $(this).prop('checked'));
	});
	$('#page_on_front').change( function() {
		section.toggleClass('new-front-page', 'new' === $(this).val());
	});
});
</script>
<?php
}
add_action( 'admin_head', 'options_reading_add_js' );

/**
 * Render the blog charset setting.
 *
 * @since 3.5.0
 */
function options_reading_blog_charset() {
	echo '<input name="blog_charset" type="text" id="blog_charset" value="' . esc_attr( get_option( 'blog_charset' ) ) . '" class="regular-text" />';
	echo '<p class="description">' . __( 'The <a href="http://codex.wordpress.org/Glossary#Character_set">character encoding</a> of your site (UTF-8 is recommended)' ) . '</p>';
}

get_current_screen()->add_help_tab( array(
	'id'      => 'overview',
	'title'   => __('Overview'),
	'content' => '<p>' . __('This screen contains the settings that affect the display of your content.') . '</p>' .
		'<p>' . sprintf(__('You can choose what&#8217;s displayed on the front page of your site. It can be posts in reverse chronological order (classic blog), or a fixed/static page. To set a static home page, you first need to create two <a href="%s">Pages</a>. One will become the front page, and the other will be where your posts are displayed.'), 'post-new.php?post_type=page') . '</p>' .
		'<p>' . __('You can also control the display of your content in RSS feeds, including the maximum numbers of posts to display and whether to show full text or a summary.') . '</p>' .
		'<p>' . __('You must click the Save Changes button at the bottom of the screen for new settings to take effect.') . '</p>',
) );

get_current_screen()->add_help_tab( array(
	'id'      => 'site-visibility',
	'title'   => has_action( 'blog_privacy_selector' ) ? __( 'Site Visibility' ) : __( 'Search Engine Visibility' ),
	'content' => '<p>' . __( 'You can choose whether or not your site will be crawled by robots, ping services, and spiders. If you want those services to ignore your site, click the checkbox next to &#8220;Discourage search engines from indexing this site&#8221; and click the Save Changes button at the bottom of the screen. Note that your privacy is not complete; your site is still visible on the web.' ) . '</p>' .
		'<p>' . __( 'When this setting is in effect, a reminder is shown in the Right Now box of the Dashboard that says, &#8220;Search Engines Discouraged,&#8221; to remind you that your site is not being crawled.' ) . '</p>',
) );

get_current_screen()->set_help_sidebar(
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Settings_Reading_Screen" target="_blank">Documentation on Reading Settings</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/" target="_blank">Support Forums</a>') . '</p>'
);

include( './admin-header.php' );
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<form method="post" action="options.php">
<?php
settings_fields( 'reading' );
wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );
?>
<table class="form-table">
<?php
if ( ! in_array( get_option( 'blog_charset' ), array( 'utf8', 'utf-8', 'UTF8', 'UTF-8' ) ) )
	add_settings_field( 'blog_charset', __( 'Encoding for pages and feeds' ), 'options_reading_blog_charset', 'reading', 'default', array( 'label_for' => 'blog_charset' ) );

$classes = '';
if ( 'page' == get_option( 'show_on_front' ) ) {
	if ( ! get_pages() || ! get_option( 'page_on_front' ) && ! get_option( 'page_for_posts' ) ) {
		update_option( 'show_on_front', 'posts' );
	} else {
		$classes = 'page-on-front';
		if ( get_option( 'page_for_posts' ) )
			$classes .= ' page-for-posts';
	}
}

$all_pages = get_pages();
$new_front_page_only = ! get_option( 'page_on_front' ) && ( ! $all_pages || ( 1 == count( $all_pages ) && __( 'sample-page' ) == $all_pages[0]->post_name ) );

if ( current_user_can( 'create_posts', 'page' ) && ! ( get_option( 'page_for_posts' ) && $page_for_posts = get_post( get_option( 'page_for_posts' ) ) ) ) {
	$title = _x( 'Blog', 'default page for posts title' );
	// @todo What if the found page is post_type = attachment or post_status != publish?
	//       We could go ahead and create a new one, but we would not be able to take over
	//       the slug from another page. (We could for an attachment.)
	//       We must also check that the user can edit this page and publish a page.
	//       Otherwise, we must assume they cannot create pages (throughout), and thus
	//       should fall back to the dropdown.
	$page_for_posts = get_page_by_path( sanitize_title( $title ) );
	if ( ! $page_for_posts || $page_for_posts->ID == get_option( 'page_on_front' ) ) {
		$page_for_posts = get_default_post_to_edit( 'page', true );
		$page_for_posts->post_title = $title;
		$page_for_posts->post_name = sanitize_title( $title );
	}
}

if ( ! $new_front_page_only || current_user_can( 'create_posts', 'page' ) ) : ?>
<tr valign="top">
<th scope="row"><?php _e( 'Enable a static front page' ); ?></th>
<td id="front-static-pages" class="<?php echo $classes; ?>">
	<fieldset><legend class="screen-reader-text"><span><?php _e( 'Enable a static front page' ); ?></span></legend>
	<p><label for="show_on_front">
		<input id="show_on_front" name="show_on_front" type="checkbox" value="page" <?php checked( 'page', get_option( 'show_on_front' ) ); ?> />
		<?php printf( __( 'Show a <a href="%s">page</a> instead of your latest posts' ), 'edit.php?post_type=page' ); ?>
	</label></p>
	<p class="if-page-on-front sub-option">
	<?php if ( $new_front_page_only ) : // If no pages, or only sample page, only allow a new page to be added ?>
		<label for="page_on_front_title"><?php _e( 'Add new page titled:' ); ?>
	<?php else : ?>
		<label for="page_on_front">
			<select name="page_on_front" id="page_on_front">
				<option value="0"><?php _e( '&mdash; Select &mdash;' ); ?></option>
				<?php if ( current_user_can( 'create_posts', 'page' ) ) : ?>
				<option value="new" id="new-page"><?php _e( '&mdash; Add new page &mdash;' ); ?></option>
				<?php endif; ?>
				<?php echo walk_page_dropdown_tree( $all_pages, 0, array( 'selected' => get_option( 'page_on_front' ) ) ); ?>
			</select>
		</label>
		<?php if ( current_user_can( 'create_posts', 'page' ) ) : ?>
		<label for="page_on_front_title" class="if-new-front-page"><?php _e( 'titled:' ); ?>
		<?php endif; ?>
	<?php endif; ?>
	<?php if ( current_user_can( 'create_posts', 'page' ) ) : ?>
			<input name="page_on_front_title" type="text" id="page_on_front_title" value="<?php echo esc_attr_x( 'Home', 'default page on front title' ); ?>" />
		</label>
	<?php endif; ?>
	</p>
	<p class="if-page-on-front"><label for="page_for_posts">
		<input id="page_for_posts" name="page_for_posts" type="checkbox" value="<?php echo $page_for_posts->ID; ?>" <?php checked( (bool) get_option( 'page_for_posts' ) ); ?> />
		<?php _e( 'Show latest posts on a separate page' ); ?>
	</label></p>
	<?php if ( current_user_can( 'create_posts', 'page' ) ) : ?>
	<p class="if-page-for-posts sub-option"><label for="page_for_posts_title"><?php _e( 'Page title:' ); ?>
		<input name="page_for_posts_title" type="text" id="page_for_posts_title" value="<?php echo esc_attr( htmlspecialchars( $page_for_posts->post_title ) ); ?>" />
	</label></p>
	<p class="if-page-for-posts sub-option" id="edit-slug-box">
		<?php echo get_sample_permalink_html( $page_for_posts->ID, $page_for_posts->post_title, $page_for_posts->post_name ); ?>
	</p>
	<input name="post_name" type="hidden" id="post_name" value="<?php echo esc_attr( apply_filters( 'editable_slug', $page_for_posts->post_name ) ); ?>" />
	<?php if ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) == get_option( 'page_on_front' ) ) : ?>
	<div class="error inline"><p><strong><?php _e( 'ERROR:' ); ?></strong> <?php _e( 'These pages should not be the same!' ); ?></p></div>
	<?php endif; ?>
	</fieldset>
	<?php else : // cannot create pages, so fall back to a selector of existing pages ?>
	<p class="if-page-for-posts sub-option"><label for="page_for_posts">
		<?php wp_dropdown_pages( array(
			'name' => 'page_for_posts', 'show_option_none' => __( '&mdash; Select &mdash;' ),
			'option_none_value' => '0', 'selected' => get_option( 'page_for_posts' )
		) ); ?>
	<?php endif; // create pages ?>
</td>
</tr>
<?php endif; // if no pages to choose from and can't create pages ?>

<tr valign="top">
<th scope="row"><label for="posts_per_page"><?php _e( 'Blog pages show at most' ); ?></label></th>
<td>
<input name="posts_per_page" type="number" step="1" min="1" id="posts_per_page" value="<?php form_option( 'posts_per_page' ); ?>" class="small-text" /> <?php _e( 'posts' ); ?>
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="posts_per_rss"><?php _e( 'Syndication feeds show the most recent' ); ?></label></th>
<td><input name="posts_per_rss" type="number" step="1" min="1" id="posts_per_rss" value="<?php form_option( 'posts_per_rss' ); ?>" class="small-text" /> <?php _e( 'items' ); ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e( 'For each article in a feed, show' ); ?> </th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'For each article in a feed, show' ); ?> </span></legend>
<p><label><input name="rss_use_excerpt" type="radio" value="0" <?php checked( 0, get_option( 'rss_use_excerpt' ) ); ?>	/> <?php _e( 'Full text' ); ?></label><br />
<label><input name="rss_use_excerpt" type="radio" value="1" <?php checked( 1, get_option( 'rss_use_excerpt' ) ); ?> /> <?php _e( 'Summary' ); ?></label></p>
</fieldset></td>
</tr>

<tr valign="top" class="option-site-visibility">
<th scope="row"><?php has_action( 'blog_privacy_selector' ) ? _e( 'Site Visibility' ) : _e( 'Search Engine Visibility' ); ?> </th>
<td><fieldset><legend class="screen-reader-text"><span><?php has_action( 'blog_privacy_selector' ) ? _e( 'Site Visibility' ) : _e( 'Search Engine Visibility' ); ?> </span></legend>
<?php if ( has_action( 'blog_privacy_selector' ) ) : ?>
	<input id="blog-public" type="radio" name="blog_public" value="1" <?php checked('1', get_option('blog_public')); ?> />
	<label for="blog-public"><?php _e( 'Allow search engines to index this site' );?></label><br/>
	<input id="blog-norobots" type="radio" name="blog_public" value="0" <?php checked('0', get_option('blog_public')); ?> />
	<label for="blog-norobots"><?php _e( 'Discourage search engines from indexing this site' ); ?></label>
	<p class="description"><?php _e( 'Note: Neither of these options blocks access to your site &mdash; it is up to search engines to honor your request.' ); ?></p>
	<?php do_action('blog_privacy_selector'); ?>
<?php else : ?>
	<label for="blog_public"><input name="blog_public" type="checkbox" id="blog_public" value="0" <?php checked( '0', get_option( 'blog_public' ) ); ?> />
	<?php _e( 'Discourage search engines from indexing this site' ); ?></label>
	<p class="description"><?php _e( 'It is up to search engines to honor this request.' ); ?></p>
<?php endif; ?>
</fieldset></td>
</tr>

<?php do_settings_fields( 'reading', 'default' ); ?>
</table>

<?php do_settings_sections( 'reading' ); ?>

<?php submit_button(); ?>
</form>
</div>
<?php include( './admin-footer.php' ); ?>
