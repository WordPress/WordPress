<?php
/**
 * Turbo Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Tools');
wp_enqueue_script( 'wp-gears' );

require_once('admin-header.php');

screen_icon();
?>
<div class="wrap">
<h2><?php echo wp_specialchars( $title ); ?></h2>

<div class="tool-box">
<?php
if ( ! $is_opera ) {
?>
	<div id="gears-msg1">
	<h3 class="title"><?php _e('Turbo:'); ?> <?php _e('Speed up WordPress'); ?></h3>
	<p><?php _e('WordPress now has support for Gears, which adds new features to your web browser.'); ?><br />
	<a href="http://gears.google.com/" target="_blank" style="font-weight:normal;"><?php _e('More information...'); ?></a></p>
	<p><?php _e('After you install and enable Gears, most of WordPress&#8217; images, scripts, and CSS files will be stored locally on your computer. This speeds up page load time.'); ?></p>
	<p><strong><?php _e('Don&#8217;t install on a public or shared computer.'); ?></strong></p>
	<div class="buttons"><button onclick="window.location = 'http://gears.google.com/?action=install&amp;return=<?php echo urlencode( admin_url() ); ?>';" class="button"><?php _e('Install Now'); ?></button></div>
	</div>

	<div id="gears-msg2" style="display:none;">
	<h3 class="title"><?php _e('Turbo:'); ?> <?php _e('Gears Status'); ?></h3>
	<p><?php _e('Gears is installed on this computer, but is not enabled for use with WordPress.'); ?></p>
	<p><?php _e('To enable it click the button below.'); ?></p>
	<p><strong><?php _e('Note: Do not enable Gears if this is a public or shared computer!'); ?></strong></p>
	<div class="buttons"><button class="button" onclick="wpGears.getPermission();"><?php _e('Enable Gears'); ?></button></div>
	</div>

	<div id="gears-msg3" style="display:none;">
	<h3 class="title"><?php _e('Turbo:'); ?> <?php _e('Gears Status'); ?></h3>
	<p><?php

	if ( $is_chrome )
		_e('Gears is installed and enabled on this computer. You can disable it from your browser&#8217;s Options, Under the Hood menu.');
	elseif ( $is_safari )
		_e('Gears is installed and enabled on this computer. You can disable it from the Safari menu.');
	else
		_e('Gears is installed and enabled on this computer. You can disable it from your browser&#8217;s Tools menu.');

	?></p>
	<p><?php _e('If there are any errors try disabling Gears, reloading the page, and re-enabling Gears.'); ?></p>
	<p><?php _e('Local storage status:'); ?> <span id="gears-wait"><span style="color:#f00;"><?php _e('Updating files:'); ?></span> <span id="gears-upd-number"></span></span></p>
	</div>

	<div id="gears-msg4" style="display:none;">
	<h3 class="title"><?php _e('Turbo:'); ?> <?php _e('Gears Status'); ?></h3>
	<p><?php _e('Your browser&#8217;s settings do not permit this website to use Google Gears.'); ?></p>
	<p><?php

	if ( $is_chrome )
	 	_e('To allow it, change the Gears settings in your browser&#8217;s Options, Under the Hood menu and reload this page.');
	elseif ( $is_safari )
	 	_e('To allow it, change the Gears settings in the Safari menu and reload this page.');
	else
		_e('To allow it, change the Gears settings in your browser&#8217;s Tools menu and reload this page.');

	?></p>
	<p><strong><?php _e('Note: Do not enable Gears if this is a public or shared computer!'); ?></strong></p>
	</div>
	<script type="text/javascript">wpGears.message();</script>
<?php } else {
	_e('Turbo is not available for your browser.');
} ?>
</div>

<?php if ( current_user_can('publish_posts') ) : ?>
<div class="tool-box">
	<h3 class="title"><?php _e('Press This') ?></h3>
	<p><?php _e('Press This is a bookmarklet: a little app that runs in your browser and lets you grab bits of the web.');?></p>

	<p><?php _e('Use Press This to clip text, images and videos from any web page. Then edit and add more straight from Press This before you save or publish it in a post on your blog.'); ?></p>
	<p><?php _e('Drag-and-drop the following link to your bookmarks bar or right click it and add it to your favorites for a posting shortcut.') ?></p>
	<p class="pressthis"><a href="<?php echo htmlspecialchars( get_shortcut_link() ); ?>" title="<?php echo attribute_escape(__('Press This')) ?>"><?php _e('Press This') ?></a></p>
</div>
<?php endif; ?>
</div>
<?php
include('admin-footer.php');
?>
