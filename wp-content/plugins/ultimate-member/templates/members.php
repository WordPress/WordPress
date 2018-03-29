<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo $form_id; ?>">

	<div class="um-form">

			<?php do_action('um_members_directory_search', $args ); ?>
			
			<?php do_action('um_members_directory_head', $args ); ?>
			
			<?php do_action('um_members_directory_display', $args ); ?>
			
			<?php do_action('um_members_directory_footer', $args ); ?>

	</div>
	
</div>