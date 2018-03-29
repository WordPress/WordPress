<div class="um <?php echo $this->get_class( $mode ); ?> um-<?php echo $form_id; ?>">

	<div class="um-form">
	
		<form method="post" action="">
			
			<?php do_action('um_account_page_hidden_fields', $args ); ?>
			
			<?php do_action('um_account_user_photo_hook__mobile', $args ); ?>
			
			<div class="um-account-side uimob340-hide uimob500-hide">
			
				<?php do_action('um_account_user_photo_hook', $args ); ?>
				
				<?php do_action('um_account_display_tabs_hook', $args ); ?>

			</div>
			
			<div class="um-account-main" data-current_tab="<?php echo $ultimatemember->account->current_tab; ?>">
			
				<?php
				
				do_action('um_before_form', $args);
				
				foreach( $ultimatemember->account->tabs as $k => $arr ) {

					foreach( $arr as $id => $info ) { extract( $info );
					
						$current_tab = $ultimatemember->account->current_tab;

						if ( isset($info['custom']) || um_get_option('account_tab_'.$id ) == 1 || $id == 'general' ) {

							?>
							
							<div class="um-account-nav uimob340-show uimob500-show"><a href="#" data-tab="<?php echo $id; ?>" class="<?php if ( $id == $current_tab ) echo 'current'; ?>"><?php echo $title; ?>
								<span class="ico"><i class="<?php echo $icon; ?>"></i></span>
								<span class="arr"><i class="um-faicon-angle-down"></i></span>
							</a></div>
							
							<?php
							
							echo '<div class="um-account-tab um-account-tab-'.$id.'" data-tab="'.$id.'">';

								do_action("um_account_tab__{$id}", $info );
							
							echo '</div>';
						
						}
						
					}
					
				}
				
				?>
				
			</div><div class="um-clear"></div>
			
		</form>
		
		<?php do_action('um_after_account_page_load'); ?>
	
	</div>
	
</div>