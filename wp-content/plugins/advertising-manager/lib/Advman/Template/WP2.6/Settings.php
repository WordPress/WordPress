<?php
require_once(OX_LIB . '/Tools.php');
class Advman_Template_Settings
{
	function display($target = null)
	{
		global $advman_engine;

		$action = isset($_POST['advman-action']) ? OX_Tools::sanitize($_POST['advman-action'], 'key') : '';
		
		$oxEnablePhp = $advman_engine->getSetting('enable-php');
		if (is_null($oxEnablePhp)) {
			$oxEnablePhp = false;
		}
		$oxStats = $advman_engine->getSetting('stats');
		if (is_null($oxStats)) {
			$oxStats = true;
		}
		$oxPurgeStatsDays = $advman_engine->getSetting('purge-stats-days');
		if (is_null($oxPurgeStatsDays)) {
			$oxPurgeStatsDays = 30;
		}
		
?><?php if ($action == 'save') : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Settings saved.') ?></strong></p></div>
<?php endif; ?>

<div class="wrap">
<h2><?php _e('Ad Settings', 'advman'); ?></h2>

<form action="" method="post" id="advman-form" enctype="multipart/form-data">
<input type="hidden" name="advman-mode" id="advman-mode" value="settings" />
<input type="hidden" name="advman-action" id="advman-action" value="save" />
<input type="hidden" name="advman-target" id="advman-target" />

<table class="form-table">
<tr valign="top">
	<th scope="row">
		<label for="advman-stats"><?php _e('Statistics', 'advman'); ?></label>
	</th>
	<td>
		<fieldset>
			<legend class="hidden"><?php _e('Statistics', 'advman'); ?></legend>
			<label for="advman-stats"><input name="advman-stats" type="checkbox" id="advman-stats" value="1"<?php echo $oxStats ? ' checked="checked"' : ''; ?> /> <?php _e('Collect statistics about the number of ads served', 'advman'); ?></label>
		</fieldset>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Purge after:', 'advman'); ?> <input type="text" name="advman-purge-stats-days" value="<?php echo $oxStatsPurgeDays; ?>" class="small-text" /> <?php _e('days', 'advman'); ?><br />
		<span class="setting-description"><?php _e('Collecting statistics about your ad serving will give you insight on how many ads have been viewed by your users.  It is a good idea to purge these stats after 30 days so that your database does not get too full.', 'advman'); ?></span>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Other Settings', 'advman'); ?></th>
	<td>
		<fieldset>
			<legend class="hidden"><?php _e('Other Settings', 'advman'); ?></legend>
			<label for="advman-enable-php"><input name="advman-enable-php" type="checkbox" id="advman-enable-php" value="1"<?php echo $oxEnablePhp ? ' checked="checked"' : ''; ?> /> <?php _e('Allow PHP Code in Ads', 'advman'); ?></label>
		</fieldset>
		<span class="setting-description"><?php _e('Allowing PHP code in ads will execute any PHP code when delivering an ad.  Be careful - only enable if you know what you are doing.', 'advman'); ?></span>
	</td>
</tr>
</table>


<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Changes', 'advman'); ?>" />
</p>
</form>

</div>

<?php
	}
}
?>