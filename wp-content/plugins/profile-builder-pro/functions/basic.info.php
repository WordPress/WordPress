<?php
function wppb_basic_info(){
?>

	<h2><?php _e('Profile Builder', 'profilebuilder');?></h2>
	<h3><?php _e('Welcome to Profile Builder!', 'profilebuilder');?></h3>
	<p>
	<strong><?php _e('Profile Builder', 'profilebuilder');?></strong><?php _e(' lets you customize your website by adding a front-end menu for all your users, giving them a more flexible way to modify their user-information or to register new users.', 'profilebuilder');?><br/><br/>
	<?php _e('Also, grants users with administrator rights to customize basic fields or to add new ones.', 'profilebuilder');?><br/><br/>
	<?php _e('To achieve this, just create a new page, and give it an intuitive name(e.g. Edit Profile).', 'profilebuilder');?><br/>
	<?php _e('Now all you need to do is add the following shortcode(for the previous example): ', 'profilebuilder');?>[wppb-edit-profile].<br/>
	<?php _e('Publish your page and you are ready to go!', 'profilebuilder');?><br/><br/>
	<?php _e('You can use the following shortcodes:', 'profilebuilder');?><br/>
	&rarr; <strong>[wppb-login]</strong> - <?php _e('for a log-in form.', 'profilebuilder');?><br/>
	&rarr; <strong>[wppb-register]</strong> - <?php _e('to add a registration form.', 'profilebuilder');?><br/>
	&rarr; <strong>[wppb-edit-profile]</strong> - <?php _e('to grant users a front-end acces to their personal information(requires user to be logged in).', 'profilebuilder');?><br/>
	&rarr; <strong>[wppb-recover-password]</strong> - <?php _e('to add a password recovery form.', 'profilebuilder');?><br/><br/>
	
	<?php _e('With the <strong>Pro</strong> version, users with administrator rights have access to the following features:', 'profilebuilder');?><br/>
	&rarr; <?php _e('add a custom stylesheet/inherit values from the current theme or use one of the following built into this plugin: default, white or black.', 'profilebuilder');?><br/>
	&rarr; <?php _e('select whether to display or not the admin bar in the front end for a specific user-group registered to the site.', 'profilebuilder');?><br/>
	&rarr; <?php _e('select which information-field can users see/modify. The hidden fields\' values remain unmodified.', 'profilebuilder');?><br/>
	&rarr; <?php _e('add custom fields to the existing ones, with several types to choose from: heading, text, textarea, select, checkbox, radio, and/or upload.', 'profilebuilder');?><br/>
	&rarr; <?php _e('add an avatar field.', 'profilebuilder');?><br/>
	&rarr; <?php _e('create custom redirects.', 'profilebuilder');?><br/>
	&rarr; <?php echo $echoString = __('front-end userlisting using the', 'profilebuilder').' <strong>[wppb-list-users]</strong> '. __('shortcode.', 'profilebuilder');?><br/>
	<br/>

	<strong><?php _e('NOTE:', 'profilebuilder');?></strong>
	<?php _e('this plugin only adds/removes fields in the front-end.', 'profilebuilder');?><br/>
	<?php _e('The default information-fields will still be visible(and thus modifiable)', 'profilebuilder');?> 
	<?php _e('from the back-end, while custom fields will only be visible in the front-end.', 'profilebuilder');?>
	</p>
	
<?php
}