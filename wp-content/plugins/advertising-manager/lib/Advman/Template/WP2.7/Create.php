<?php
class Advman_Template_Create
{
	function display($target = null)
	{
?><div class="wrap">
	<div id="icon-edit" class="icon32"><br /></div>
	<h2><?php _e('Create Ad', 'advman'); ?></h2>
	<form method="post" id="advman-form">
	<input type="hidden" name="advman-mode" id="advman-mode" value="edit_ad">
	<input type="hidden" name="advman-action" id="advman-action">
	<input type="hidden" name="advman-target" id="advman-target">
<!--
	<div id="side-info-column" class="inner-sidebar">
		<p><h3>Or, select an ad network below:</p></h3>
	</div>
-->
	<div id="post-body" class="has-sidebar">
		<div id="post-body-content" class="has-sidebar-content" style="width:520px">
			
		<p><h3><?php _e('Step 1: Import Your Ad Code', 'advman'); ?></h3></p>
		<p><?php _e('Simply <strong>paste your Ad Code below</strong> and Import!', 'advman'); ?></p>

		<label class="hidden" for="excerpt"><?php _e('Code'); ?></label>
		<textarea rows="8" cols="60" name="advman-code" tabindex="6"></textarea>
		<p><span style="font-size:x-small;color:gray;"><?php _e('Advertising Manager will automatically detect many ad network tags.', 'advman'); ?> <?php _e('You can paste your existing ads or ad networks, or sign up to new ad networks to try them out!', 'advman'); ?></span></p>
				<div id="publishing-action">
					<a class="submitdelete deletion" href="javascript:submit();" onclick="document.getElementById('advman-action').value='cancel'; document.getElementById('advman-form').submit();"><?php _e('Cancel', 'advmgr') ?></a>&nbsp;&nbsp;&nbsp;
					<input type="submit" class="button-primary" id="advman_save" tabindex="5" accesskey="p" value="<?php _e('Import', 'advman'); ?>" onclick="document.getElementById('advman-action').value='import';" />
				</div>
				<div class="clear"></div>
	</div>
	</form>
</div><!-- wpwrap -->
<?php 
	}
}
?>