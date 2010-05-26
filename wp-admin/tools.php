<?php
/**
 * Tools Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('./admin.php');

$title = __('Tools');

add_contextual_help($current_screen, '<p>' . __('Press This is a bookmarklet that makes it easy to blog about something you come across on the web. You can use it to just grab a link, or to post an excerpt. Press This will even allow you to choose from images included on the page and use them in your post. Just drag the Press This link on this screen to your bookmarks bar in your browser, and you&#8217;ll be you&#8217;ll be on your way to easier content creation.') . '</p>' .
	'<p>' . __('Note: Turbo/Gears is no longer promoted on this screen as it was in previous versions due to the fact that Google has discontinued support for it.') . '</p>' .
	'<p>' . __('<strong>For more information:</strong>') . '</p>' .
	'<p>' . sprintf(__('<a href="%s">Tools Documentation</a>'), 'http://codex.wordpress.org/Tools_Tools_SubPanel') . '</p>' .
	'<p>' . sprintf(__('<a href="%s">Support Forums</a>'), 'http://wordpress.org/support/') . '</p>'
);

require_once('./admin-header.php');

?>
<div class="wrap">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<?php if ( current_user_can('edit_posts') ) : ?>
<div class="tool-box">
	<h3 class="title"><?php _e('Press This') ?></h3>
	<p><?php _e('Press This is a bookmarklet: a little app that runs in your browser and lets you grab bits of the web.');?></p>

	<p><?php _e('Use Press This to clip text, images and videos from any web page. Then edit and add more straight from Press This before you save or publish it in a post on your site.'); ?></p>
	<p><?php _e('Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.') ?></p>
	<p class="pressthis"><a href="<?php echo htmlspecialchars( get_shortcut_link() ); ?>" title="<?php echo esc_attr(__('Press This')) ?>"><?php _e('Press This') ?></a></p>
</div>
<?php
endif;

do_action( 'tool_box' );
?>
</div>
<?php
include('./admin-footer.php');
?>
