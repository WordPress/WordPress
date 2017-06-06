<?php


function ca_register_settings(){
	/* Example
		register_setting('Section Group Name', 'Setting Field Name')
	*/
	register_setting( 'ca_header_options', 'header_ca_logo');
	
}

function menu_option_setup(){
	
?>
<form action="options.php" method="post" target="" name=""> 
		<h2>CA Theme Options</h2>
		
		<?php
			settings_fields('ca_header_options');
			do_settings_sections( 'ca_header_options' );
		?>
		<label style="padding-right: 2.8em;" >Logo Image </label>
		<input type="text" class="media-input" />
		<button class="media-button" >Browse</button>
		

		<?php submit_button();?>
		
</form>
<?php


}


?>