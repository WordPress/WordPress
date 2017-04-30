<?php

/**
 * Password Protected
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<div id="bbpress-forums">
	<fieldset class="bbp-form" id="bbp-protected">
		<Legend><?php _e( 'Protected', 'bbpress' ); ?></legend>

		<?php echo get_the_password_form(); ?>

	</fieldset>
</div>