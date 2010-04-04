<?php
/**
 * Tools Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Tools');

require_once('admin-header.php');

?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<?php if ( current_user_can('edit_posts') ) : ?>
<div class="tool-box">
	<h3 class="title"><?php _e('Press This') ?></h3>
	<p><?php _e('Press This is a bookmarklet: a little app that runs in your browser and lets you grab bits of the web.');?></p>

	<p><?php _e('Use Press This to clip text, images and videos from any web page. Then edit and add more straight from Press This before you save or publish it in a post on your blog.'); ?></p>
	<p><?php _e('Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.') ?></p>
	<p class="pressthis"><a href="<?php echo htmlspecialchars( get_shortcut_link() ); ?>" title="<?php echo esc_attr(__('Press This')) ?>"><?php _e('Press This') ?></a></p>
</div>
<?php
endif;

$cats = get_taxonomy('category');
$tags = get_taxonomy('post_tag');

if ( current_user_can($cats->manage_cap) || current_user_can($tags->manage_cap) ) : ?>
<div class="tool-box">
    <h3 class="title"><?php _e('Category&#47;Tag Conversion') ?></h3>
    <p><?php printf(__('Use this to convert <a href="%s">categories to tags</a>, or <a href="%s">tags to categories</a>.'), 'admin.php?import=wp-cat2tag', 'admin.php?import=wp-cat2tag&amp;step=3'); ?></p>
</div>
<?php
endif;

do_action( 'tool_box' );
?>
</div>
<?php
include('admin-footer.php');
?>
