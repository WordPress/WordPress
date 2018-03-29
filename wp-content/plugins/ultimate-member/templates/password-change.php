<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo $form_id; ?>">

	<div class="um-form">
	
		<form method="post" action="">
		
			<?php
			
			if ( !isset( $ultimatemember->password->reset_request ) ) {
			
				do_action('um_change_password_page_hidden_fields', $args );
				
				do_action('um_change_password_form', $args );
					
				do_action("um_after_form_fields", $args);
			
			}
			
			?>

		</form>
	
	</div>
	
</div>