<?php
/**
 * Turbo Administration Panel.
 *
 * @package WordPress
 * @subpackage Administration
 */

/** WordPress Administration Bootstrap */
require_once('admin.php');

$title = __('Turbo');

require_once('admin-header.php');

if ( ! $is_opera ) {
?>
	<div id="gears-info-box" class="info-box" style="display:none;">
	<img src="images/gear.png" title="Gear" alt="" class="gears-img" />
	<div id="gears-msg1">
	<h3 class="info-box-title"><?php _e('Speed up WordPress'); ?></h3>
	<p><?php _e('WordPress now has support for Gears, which adds new features to your web browser.'); ?><br />
	<a href="http://gears.google.com/" target="_blank" style="font-weight:normal;"><?php _e('More information...'); ?></a></p>
	<p><?php _e('After you install and enable Gears, most of WordPress&#8217; images, scripts, and CSS files will be stored locally on your computer. This speeds up page load time.'); ?></p>
	<p><strong><?php _e('Don&#8217;t install on a public or shared computer.'); ?></strong></p>	<div class="submit"><button onclick="window.location = 'http://gears.google.com/?action=install&amp;return=<?php echo urlencode( admin_url() ); ?>';" class="button"><?php _e('Install Now'); ?></button>
	<button class="button" style="margin-left:10px;" onclick="document.getElementById('gears-info-box').style.display='none';"><?php _e('Cancel'); ?></button></div>
	</div>

	<div id="gears-msg2" style="display:none;">
	<h3 class="info-box-title"><?php _e('Gears Status'); ?></h3>
	<p><?php _e('Gears is installed on this computer, but is not enabled for use with WordPress.'); ?></p>
	<p><?php _e('To enable it click the button below.'); ?></p>
	<p><strong><?php _e('However, Gears should not be enabled if this is a public or shared computer.'); ?></strong></p>
	<div class="submit"><button class="button" onclick="wpGears.getPermission();"><?php _e('Enable Gears'); ?></button>
	<button class="button" style="margin-left:10px;" onclick="document.getElementById('gears-info-box').style.display='none';"><?php _e('Cancel'); ?></button></div>
	</div>

	<div id="gears-msg3" style="display:none;">
	<h3 class="info-box-title"><?php _e('Gears Status'); ?></h3>
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
	<h3 class="info-box-title"><?php _e('Gears Status'); ?></h3>
	<p><?php _e('This web site is denied to use Gears.'); ?></p>
	<p><?php

	if ( $is_chrome )
	 	_e('To allow it, change the Gears settings from your browser&#8217;s Options, Under the Hood menu and reload this page.');
	elseif ( $is_safari )
	 	_e('To allow it, change the Gears settings from the Safari menu and reload this page.');
	else
		_e('To allow it, change the Gears settings from your browser&#8217;s Tools menu and reload this page.');

	?></p>
	<p><strong><?php _e('However, Gears should not be enabled if this is a public or shared computer.'); ?></strong></p>
	</div>
	
	</div>

	<script type="text/javascript">
		wpGears.message(1);			
	</script>
<?php }

?>