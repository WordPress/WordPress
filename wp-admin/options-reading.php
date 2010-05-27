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

add_contextual_help($current_screen, 
	'<p>' . __('This screen contains the settings that affect the display of your content.') . '</p>' .
	'<p>' . __('You can choose what&#8217;s displayed on the front page of your site. It can be posts in reverse chronological order (classic blog), or a fixed/static page. To set a static home page, you first need to create two <a href="post-new.php?post_type=page">Pages</a>. One will become the front page, and the other will be where your posts are displayed.') . '</p>' .
	'<p>' . __('You can also control the display of your content in RSS feeds, including the maximum numbers of posts to display, whether to show full text or a summary, and the character set encoding.') . '</p>' .
	'<p>' . __('You must click the Save Changes button at the bottom of the screen for new settings to take effect.') . '</p>' .
	'<p><strong>' . __('For more information:') . '</strong></p>' .
	'<p>' . __('<a href="http://codex.wordpress.org/Settings_Reading_SubPanel">Reading Settings Documentation</a>') . '</p>' .
	'<p>' . __('<a href="http://wordpress.org/support/">Support Forums</a>') . '</p>'
);

include( './admin-header.php' );
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<form name="form1" method="post" action="options.php">
<?php settings_fields( 'reading' ); ?>

<?php if ( ! get_pages() ) : ?>
<input name="show_on_front" type="hidden" value="posts" />
<table class="form-table">
<?php else : ?>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e( 'Front page displays' ); ?></th>
<td id="front-static-pages"><fieldset><legend class="screen-reader-text"><span><?php _e( 'Front page displays' ); ?></span></legend>
	<p><label>
		<input name="show_on_front" type="radio" value="posts" class="tog" <?php checked( 'posts', get_option( 'show_on_front' ) ); ?> />
		<?php _e( 'Your latest posts' ); ?>
	</label>
	</p>
	<p><label>
		<input name="show_on_front" type="radio" value="page" class="tog" <?php checked( 'page', get_option( 'show_on_front' ) ); ?> />
		<?php printf( __( 'A <a href="%s">static page</a> (select below)' ), 'edit.php?post_type=page' ); ?>
	</label>
	</p>
<ul>
	<li><label for="page_on_front"><?php printf( __( 'Front page: %s' ), wp_dropdown_pages( array( 'name' => 'page_on_front', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'selected' => get_option( 'page_on_front' ) ) ) ); ?></label></li>
	<li><label for="page_for_posts"><?php printf( __( 'Posts page: %s' ), wp_dropdown_pages( array( 'name' => 'page_for_posts', 'echo' => 0, 'show_option_none' => __( '&mdash; Select &mdash;' ), 'selected' => get_option( 'page_for_posts' ) ) ) ); ?></label></li>
</ul>
<?php if ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_for_posts' ) == get_option( 'page_on_front' ) ) : ?>
<div id="front-page-warning" class="error inline"><p><?php _e( '<strong>Warning:</strong> these pages should not be the same!' ); ?></p></div>
<?php endif; ?>
</fieldset></td>
</tr>
<?php endif; ?>
<tr valign="top">
<th scope="row"><label for="posts_per_page"><?php _e( 'Blog pages show at most' ); ?></label></th>
<td>
<input name="posts_per_page" type="text" id="posts_per_page" value="<?php form_option( 'posts_per_page' ); ?>" class="small-text" /> <?php _e( 'posts' ); ?>
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="posts_per_rss"><?php _e( 'Syndication feeds show the most recent' ); ?></label></th>
<td><input name="posts_per_rss" type="text" id="posts_per_rss" value="<?php form_option( 'posts_per_rss' ); ?>" class="small-text" /> <?php _e( 'items' ); ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e( 'For each article in a feed, show' ); ?> </th>
<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'For each article in a feed, show' ); ?> </span></legend>
<p><label><input name="rss_use_excerpt"  type="radio" value="0" <?php checked( 0, get_option( 'rss_use_excerpt' ) ); ?>	/> <?php _e( 'Full text' ); ?></label><br />
<label><input name="rss_use_excerpt" type="radio" value="1" <?php checked( 1, get_option( 'rss_use_excerpt' ) ); ?> /> <?php _e( 'Summary' ); ?></label></p>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><label for="blog_charset"><?php _e( 'Encoding for pages and feeds' ); ?></label></th>
<td><input name="blog_charset" type="text" id="blog_charset" value="<?php form_option( 'blog_charset' ); ?>" class="regular-text" />
<span class="description"><?php _e( 'The <a href="http://codex.wordpress.org/Glossary#Character_set">character encoding</a> of your site (UTF-8 is recommended, if you are adventurous there are some <a href="http://en.wikipedia.org/wiki/Character_set">other encodings</a>)' ); ?></span></td>
</tr>
<?php do_settings_fields( 'reading', 'default' ); ?>
</table>

<?php do_settings_sections( 'reading' ); ?>

<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
</p>
</form>
</div>
<?php include( './admin-footer.php' ); ?>
