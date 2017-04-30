<?php
class Advman_Template_Create
{
	function display($target = null)
	{
		// Get our options and see if we're handling a form submission.
		global $_advman;
		global $_advman_networks;
?>	<div class="wrap">
		<form method="post" id="advman-form">
			<input type="hidden" name="advman-mode" id="advman-mode" value="create_ad">	
			<input type="hidden" name="advman-action" id="advman-action">
			<input type="hidden" name="advman-target" id="advman-target">
			<h2><?php _e('Create Ad', 'advman'); ?></h2>
			
			<table>
			<tr>
				<td style="width:50%;vertical-align:top;">
					<h3><?php _e('Step 1: Import Your Ad Code', 'advman'); ?></h3>
					<p><?php _e('Simply <strong>paste your Ad Code below</strong> and Import!', 'advman'); ?></p>
					<div>
						<textarea rows="5" cols="65" name="advman-code" id="advman-code"></textarea>
						<p class="submit" style="text-align:right;vertical-align:bottom;">
							<input type="button" value="<?php _e('Cancel', 'advman'); ?>" onclick="document.getElementById('advman-action').value='cancel'; this.form.submit();">		
							<input type="button" value="<?php _e('Clear', 'advman'); ?>" onclick="document.getElementById('advman-code').value='';">		
							<input style="font-weight:bold;" type="submit" value="<?php _e('Import to New Ad Unit&raquo;', 'advman'); ?>" onclick="document.getElementById('advman-action').value='import';">
						</p>
					</div>		
				</td>
				<td style="width:10%";>&nbsp;</td>
				<td style="width:40%";>
					<p><?php _e('Advertising Manager supports most Ad networks.', 'advman'); ?></p>
					<p><?php _e('Any networks not supported directly can still be managed as HTML Code units.', 'advman'); ?></p>
				</td>
			</tr>
			</table>
		</form>
	</div>
<?php 
	}
}
?>