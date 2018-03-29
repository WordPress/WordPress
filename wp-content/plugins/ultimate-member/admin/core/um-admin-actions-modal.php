<?php

	/***
	***	@Put status handler in modal
	***/
	add_action('um_admin_field_modal_header', 'um_admin_add_message_handlers');
	function um_admin_add_message_handlers(){ ?><div class="um-admin-error-block"></div><div class="um-admin-success-block"></div> <?php }
	
	/***
	***	@Footer of modal
	***/
	add_action('um_admin_field_modal_footer', 'um_admin_add_conditional_support', 10, 4);
	function um_admin_add_conditional_support( $form_id, $field_args, $in_edit, $edit_array ){
		$metabox = new UM_Admin_Metabox();
		
		if ( isset($field_args['conditional_support'])  && $field_args['conditional_support'] == 0 ) return;
		
		?>
		
		<div class="um-admin-btn-toggle">
		
			<?php if ( $in_edit ) { $metabox->in_edit = true;  $metabox->edit_array = $edit_array; ?>
			<a href="#"><i class="um-icon-plus"></i><?php _e('Manage conditional fields support'); ?></a> <?php $metabox->tooltip('Here you can setup conditional logic to show/hide this field based on specific fields value or conditions'); ?>
			<?php } else { ?>
			<a href="#"><i class="um-icon-plus"></i><?php _e('Add conditional fields support'); ?></a> <?php $metabox->tooltip('Here you can setup conditional logic to show/hide this field based on specific fields value or conditions'); ?>
			<?php } ?>
			
			<div class="um-admin-btn-content">
			
				<p class="um-admin-reset-conditions"><a href="#" class="button button-primary"><?php _e('Reset all rules','ultimate-member'); ?></a></p>
				<div class="um-admin-clear"></div>
				
				<?php
				
				if ( isset( $edit_array['conditions'] ) ){
					
					foreach( $edit_array['conditions'] as $k => $arr ) {

						if ( $k == 0 ) $k = '';
				?>
				
				<div class="um-admin-cur-condition">
				
				<?php $metabox->field_input( '_conditional_action' . $k, $form_id ); ?>
				<?php $metabox->field_input( '_conditional_field' . $k , $form_id ); ?>
				<?php $metabox->field_input( '_conditional_operator' . $k, $form_id ); ?>
				<?php $metabox->field_input( '_conditional_value' . $k, $form_id ); ?>
				
				<?php if ( $k == '' ) { ?>
				<p><a href="#" class="um-admin-new-condition button um-admin-tipsy-n" title="Add new condition"><i class="um-icon-plus" style="margin-right:0!important"></i></a></p>
				<?php } else { ?>
				<p><a href="#" class="um-admin-remove-condition button um-admin-tipsy-n" title="Remove condition"><i class="um-icon-close" style="margin-right:0!important"></i></a></p>
				<?php } ?>
				
				<div class="um-admin-clear"></div>
				</div>
				
				<?php
				
					}
					
				} else {
				
				?>
			
				<div class="um-admin-cur-condition">
				
				<?php $metabox->field_input( '_conditional_action', $form_id ); ?>
				<?php $metabox->field_input( '_conditional_field', $form_id ); ?>
				<?php $metabox->field_input( '_conditional_operator', $form_id ); ?>
				<?php $metabox->field_input( '_conditional_value', $form_id ); ?>
				
				<p><a href="#" class="um-admin-new-condition button um-admin-tipsy-n" title="Add new condition"><i class="um-icon-plus" style="margin-right:0!important"></i></a></p>
				
				<div class="um-admin-clear"></div>
				</div>
				
				<?php } ?>

			</div>
			
		</div>

		<?php
		
	}
	
	/***
	***	@Dynamic modal content
	***/
	add_action('wp_ajax_nopriv_ultimatemember_dynamic_modal_content', 'ultimatemember_dynamic_modal_content');
	add_action('wp_ajax_ultimatemember_dynamic_modal_content', 'ultimatemember_dynamic_modal_content');
	function ultimatemember_dynamic_modal_content(){
		global $ultimatemember;
		
		$metabox = new UM_Admin_Metabox();
		
		if ( !is_user_logged_in() || !current_user_can('manage_options') ) die( __('Please login as administrator','ultimate-member') );
		
		extract($_POST);
		
		switch ( $act_id ) {
		
			default:
				
				ob_start();
				
				do_action('um_admin_ajax_modal_content__hook', $act_id );
				do_action("um_admin_ajax_modal_content__hook_{$act_id}");
				
				$output = ob_get_contents();
				ob_end_clean();
				
				break;
			
			case 'um_admin_fonticon_selector':
			
				ob_start();
				
				?>
				
					<div class="um-admin-metabox">
						<p class="_icon_search"><input type="text" name="_icon_search" id="_icon_search" value="" placeholder="<?php _e('Search Icons...','ultimate-member'); ?>" /></p>
					</div>
				
					<div class="um-admin-icons">
						<?php foreach($ultimatemember->icons->all as $icon){ ?>
						<span data-code="<?php echo $icon; ?>" title="<?php echo $icon; ?>" class="um-admin-tipsy-n"><i class="<?php echo $icon; ?>"></i></span>
						<?php } ?>
					</div><div class="um-admin-clear"></div>
			
					<?php
					
					$output = ob_get_contents();
					ob_end_clean();
					break;

			case 'um_admin_show_fields':
				
				ob_start();
				$form_fields = $ultimatemember->query->get_attr( 'custom_fields', $arg2 );
				$form_fields = array_values( array_filter( array_keys( $form_fields ) ) );
				?>
					
					<h4><?php _e('Setup New Field','ultimate-member'); ?></h4>
					<div class="um-admin-btns">
						
						<?php
						if ( $ultimatemember->builtin->core_fields ) {
							foreach ($ultimatemember->builtin->core_fields as $field_type => $array) {
							
								if ( isset( $array['in_fields'] ) && $array['in_fields'] == false ) { } else {
						?>
						
						<a href="#" class="button" data-modal="UM_add_field" data-modal-size="normal" data-dynamic-content="um_admin_new_field_popup" data-arg1="<?php echo $field_type; ?>" data-arg2="<?php echo $arg2 ?>"><?php echo $array['name']; ?></a>
						
						<?php } } } ?>
						
					</div>
					
					<h4><?php _e('Predefined Fields','ultimate-member'); ?></h4>
					<div class="um-admin-btns">
						
						<?php
						if ( $ultimatemember->builtin->predefined_fields ) {
							foreach ($ultimatemember->builtin->predefined_fields as $field_key => $array) {
								
								if ( !isset( $array['account_only'] ) && !isset( $array['private_use'] ) ) {
						?>
						
						<a href="#" class="button" <?php disabled( in_array( $field_key,  $form_fields  ) ) ?> data-silent_action="um_admin_add_field_from_predefined" data-arg1="<?php echo $field_key; ?>" data-arg2="<?php echo $arg2; ?>"><?php echo um_trim_string( stripslashes( $array['title'] ), 20 ); ?></a>

						<?php } } } else { echo '<p>' . __('None','ultimate-member') . '</p>'; } ?>
						
					</div>
					
					<h4><?php _e('Custom Fields','ultimate-member'); ?></h4>
					<div class="um-admin-btns">
						
						<?php
						if ( $ultimatemember->builtin->custom_fields ) {
							foreach ($ultimatemember->builtin->custom_fields as $field_key => $array) {
						?>
						
						<a href="#" class="button with-icon" data-silent_action="um_admin_add_field_from_list" data-arg1="<?php echo $field_key; ?>" data-arg2="<?php echo $arg2; ?>"><?php echo um_trim_string( stripslashes( $array['title'] ), 20 ); ?> <small>(<?php echo ucfirst( $array['type']); ?>)</small><span class="remove"></span></a>
						
						<?php } } else { echo '<p>' . __('You did not create any custom fields', 'ultimate-member') . '</p>'; } ?>
						
					</div>
					
				<?php
					
				$output = ob_get_contents();
				ob_end_clean();
				break;

			case 'um_admin_edit_field_popup':
			
				ob_start();
				
				$args = $ultimatemember->builtin->get_core_field_attrs( $arg1 );
				
				$form_fields = $ultimatemember->query->get_attr( 'custom_fields', $arg2 );
				
				$metabox->set_field_type = $arg1;
				$metabox->in_edit = true;
				$metabox->edit_array = $form_fields[ $arg3 ];
				
				if ( !isset( $metabox->edit_array['metakey'] ) ){
					$metabox->edit_array['metakey'] = $metabox->edit_array['id'];
				}
			
				if ( !isset( $metabox->edit_array['position'] ) ){
					$metabox->edit_array['position'] = $metabox->edit_array['id'];
				}
				
				extract( $args );
				
				if ( !isset( $col1 ) ) {
				
					echo '<p>'. __('This field type is not setup correcty.', 'ultimate-member') . '</p>';
					
				} else {

				?>
				
				<?php if ( isset( $metabox->edit_array['in_group'] ) ) { ?>
				<input type="hidden" name="_in_row" id="_in_row" value="<?php echo $metabox->edit_array['in_row']; ?>" />
				<input type="hidden" name="_in_sub_row" id="_in_sub_row" value="<?php echo $metabox->edit_array['in_sub_row']; ?>" />
				<input type="hidden" name="_in_column" id="_in_column" value="<?php echo $metabox->edit_array['in_column']; ?>" />
				<input type="hidden" name="_in_group" id="_in_group" value="<?php echo $metabox->edit_array['in_group']; ?>" />
				<?php } ?>
				
				<input type="hidden" name="_type" id="_type" value="<?php echo $arg1; ?>" />
				
				<input type="hidden" name="post_id" id="post_id" value="<?php echo $arg2; ?>" />
				
				<input type="hidden" name="action" id="action" value="ultimatemember_admin_update_field" />
				
				<input type="hidden" name="edit_mode" id="edit_mode" value="true" />
				
				<input type="hidden" name="_metakey" id="_metakey" value="<?php echo $metabox->edit_array['metakey']; ?>" />

				<input type="hidden" name="_position" id="_position" value="<?php echo $metabox->edit_array['position']; ?>" />
				
				<?php if ( isset( $args['mce_content'] ) ) { ?><div class="dynamic-mce-content"><?php echo $metabox->edit_array['content']; ?></div><?php } ?>

				<?php do_action('um_admin_field_modal_header'); ?>

				<div class="um-admin-half">
				
					<?php if ( isset( $col1 ) ) {  foreach( $col1 as $opt ) $metabox->field_input ( $opt, null, $metabox->edit_array ); } ?>
					
				</div>
				
				<div class="um-admin-half um-admin-right">
				
					<?php if ( isset( $col2 ) ) {  foreach( $col2 as $opt ) $metabox->field_input ( $opt, null, $metabox->edit_array ); } ?>
					
				</div><div class="um-admin-clear"></div>
				
				<?php if ( isset( $col3 ) ) { foreach( $col3 as $opt ) $metabox->field_input ( $opt, null, $metabox->edit_array ); } ?>
				
				<div class="um-admin-clear"></div>
				
				<?php if ( isset( $col_full ) ) {foreach( $col_full as $opt ) $metabox->field_input ( $opt, null, $metabox->edit_array ); } ?>
				
				<?php do_action('um_admin_field_modal_footer', $arg2, $args, $metabox->in_edit, (isset( $metabox->edit_array ) ) ? $metabox->edit_array : '' ); ?>
	
				<?php
				
				}
				
				$output = ob_get_contents();
				ob_end_clean();
				
				break;

			case 'um_admin_new_field_popup':
			
				ob_start();
				
				$args = $ultimatemember->builtin->get_core_field_attrs( $arg1 );
				
				$metabox->set_field_type = $arg1;

				extract( $args );
				
				if ( !isset( $col1 ) ) {
				
					echo '<p>'. __('This field type is not setup correcty.', 'ultimate-member') . '</p>';
					
				} else {
				
				?>
				
				<?php if ( $in_column ) { ?>
				<input type="hidden" name="_in_row" id="_in_row" value="_um_row_<?php echo $in_row + 1; ?>" />
				<input type="hidden" name="_in_sub_row" id="_in_sub_row" value="<?php echo $in_sub_row; ?>" />
				<input type="hidden" name="_in_column" id="_in_column" value="<?php echo $in_column; ?>" />
				<input type="hidden" name="_in_group" id="_in_group" value="<?php echo $in_group; ?>" />
				<?php } ?>
				
				<input type="hidden" name="_type" id="_type" value="<?php echo $arg1; ?>" />
				
				<input type="hidden" name="post_id" id="post_id" value="<?php echo $arg2; ?>" />
				
				<input type="hidden" name="action" id="action" value="ultimatemember_admin_update_field" />

				<?php do_action('um_admin_field_modal_header'); ?>

				<div class="um-admin-half">
				
					<?php if ( isset( $col1 ) ) {  foreach( $col1 as $opt ) $metabox->field_input ( $opt ); } ?>
					
				</div>
				
				<div class="um-admin-half um-admin-right">
				
					<?php if ( isset( $col2 ) ) {  foreach( $col2 as $opt ) $metabox->field_input ( $opt ); } ?>
					
				</div><div class="um-admin-clear"></div>
				
				<?php if ( isset( $col3 ) ) { foreach( $col3 as $opt ) $metabox->field_input ( $opt ); } ?>
				
				<div class="um-admin-clear"></div>
				
				<?php if ( isset( $col_full ) ) {foreach( $col_full as $opt ) $metabox->field_input ( $opt ); } ?>
				
				<?php do_action('um_admin_field_modal_footer', $arg2, $args, $metabox->in_edit, (isset( $metabox->edit_array ) ) ? $metabox->edit_array : '' ); ?>
	
				<?php
				
				}
				
				$output = ob_get_contents();
				ob_end_clean();
				
				break;

			case 'um_admin_preview_form':
				
				$mode = $ultimatemember->query->get_attr('mode', $arg1 );
				
				if ( $mode == 'profile' ) {
					$ultimatemember->fields->editing = true;
				}
				
				$output = do_shortcode('[ultimatemember form_id='.$arg1.']');
				
				break;

			case 'um_admin_review_registration':
				
				um_fetch_user( $arg1 );
				
				$ultimatemember->user->preview = true;
				
				$submitted = um_user('submitted');
				
				$output = um_user_submitted_registration( true );
				
				break;
				
		}
		
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
		
	}
