<?php
/**
 * Reading settings administration panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Reading Settings');
$parent_file = 'options-general.php';

include('admin-header.php');
?>

<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo wp_specialchars( $title ); ?></h2>

<form name="form1" method="post" action="options.php">
<?php settings_fields('reading'); ?>

<table class="form-table">
<?php if ( get_pages() ): ?>
<tr valign="top">
<th scope="row"><?php _e('Front page displays')?></th>
<td><fieldset><legend class="invisible"><span><?php _e('Front page displays')?></span></legend>
	<p><label>
		<input name="show_on_front" type="radio" value="posts" class="tog" <?php checked('posts', get_option('show_on_front')); ?> />
		<?php _e('Your latest posts'); ?>
	</label>
	</p>
	<p><label>
		<input name="show_on_front" type="radio" value="page" class="tog" <?php checked('page', get_option('show_on_front')); ?> />
		<?php printf(__('A <a href="%s">static page</a> (select below)'), 'edit-pages.php'); ?>
	</label>
	</p>
<ul>
	<li><?php printf("<label for='page_on_front'>".__('Front page: %s')."</label>", wp_dropdown_pages("name=page_on_front&echo=0&show_option_none=".__('- Select -')."&selected=" . get_option('page_on_front'))); ?></li>
	<li><?php printf("<label for='page_for_posts'>".__('Posts page: %s')."</label>", wp_dropdown_pages("name=page_for_posts&echo=0&show_option_none=".__('- Select -')."&selected=" . get_option('page_for_posts'))); ?></li>
</ul>
<?php if ( 'page' == get_option('show_on_front') && get_option('page_for_posts') == get_option('page_on_front') ) : ?>
<div id="front-page-warning" class="updated fade-ff0000">
	<p>
		<?php _e('<strong>Warning:</strong> these pages should not be the same!'); ?>
	</p>
</div>
<?php endif; ?>
</fieldset></td>
</tr>
<?php endif; ?>
<tr valign="top">
<th scope="row"><label for="posts_per_page"><?php _e('Blog pages show at most') ?></label></th>
<td>
<input name="posts_per_page" type="text" id="posts_per_page" value="<?php form_option('posts_per_page'); ?>" class="small-text" /> <?php _e('posts') ?>
</td>
</tr>
<tr valign="top">
<th scope="row"><label for="posts_per_rss"><?php _e('Syndication feeds show the most recent') ?></label></th>
<td><input name="posts_per_rss" type="text" id="posts_per_rss" value="<?php form_option('posts_per_rss'); ?>" class="small-text" /> <?php _e('posts') ?></td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('For each article in a feed, show') ?> </th>
<td><fieldset><legend class="invisible"><span><?php _e('For each article in a feed, show') ?> </span></legend>
<p><label><input name="rss_use_excerpt"  type="radio" value="0" <?php checked(0, get_option('rss_use_excerpt')); ?>	/> <?php _e('Full text') ?></label><br />
<label><input name="rss_use_excerpt" type="radio" value="1" <?php checked(1, get_option('rss_use_excerpt')); ?> /> <?php _e('Summary') ?></label></p>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><label for="blog_charset"><?php _e('Encoding for pages and feeds') ?></label></th>
<td><input name="blog_charset" type="text" id="blog_charset" value="<?php form_option('blog_charset'); ?>" class="regular-text" />
<span class="description"><?php _e('The character encoding you write your blog in (UTF-8 is <a href="http://developer.apple.com/documentation/macos8/TextIntlSvcs/TextEncodingConversionManager/TEC1.5/TEC.b0.html">recommended</a>)') ?></span></td>
</tr>
<?php do_settings_fields('reading', 'default'); ?>
</table>

<?php do_settings_sections('reading'); ?>

<p class="submit">
	<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>
</form>
</div>
<?php include('./admin-footer.php'); ?>
