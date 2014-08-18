<?php
class Advman_Template_Notice
{
	function display($notices = null)
	{
		if (is_array($notices)) {
			foreach ($notices as $action => $notice) {
?>				<div id='update-nag'>
				<form action="admin.php?page=advman-list" method="post" id="advman-config-manage" enctype="multipart/form-data">
				<input type="hidden" name="advman-mode" value="notice">		
				<input type="hidden" name="advman-action" value="<?php echo $action; ?>">												
<?php
				echo $notice['text'];
				if ($notice['confirm'] == 'yn') {
?>				<input name="advman-notice-confirm-yes" type="submit" value="Yes">
				<input name="advman-notice-confirm-no" type="submit" value="No">
<?php
				} elseif ($notice['confirm'] == 'ok') {
?>				<input name="advman-notice-confirm-ok" type="submit" value="OK">
<?php
				} elseif ($notice['confirm'] == 'x') {
?>				<input name="advman-notice-confirm-x" type="submit" value="x">
<?php
				}
?>				</form>
				</div>
<?php
			}
		}
	}
}
?>