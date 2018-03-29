<?php

class UM_Admin_Builder {

	function __construct() {
	
		add_action('wp_ajax_nopriv_update_builder', array(&$this, 'update_builder') );
		add_action('wp_ajax_update_builder', array(&$this, 'update_builder') );

	}
	
	/***
	***	@update the builder area
	***/
	function update_builder(){
	
		global $ultimatemember;
		
		if ( !is_user_logged_in() || !current_user_can('manage_options') ) die('Please login as administrator');
		
		extract($_POST);

		ob_start();
		
		$this->form_id = $_POST['form_id'];
		
		$this->show_builder();
		
		$output = ob_get_contents();
		ob_end_clean();
		
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	
	}
	
	/***
	***	@sort array function
	***/
	function array_sort_by_column($arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}

		array_multisort($sort_col, $dir, $arr);
		return $arr;
	}
	
	/***
	***	@get fields in row
	***/
	function get_fields_by_row( $row_id ) {

		if( empty( $this->global_fields) || ! is_array( $this->global_fields ) ){
			$this->global_fields = array();
		}
		
		foreach( $this->global_fields as $key => $array ) {
			if ( !isset( $array['in_row'] ) || ( isset( $array['in_row'] ) && $array['in_row'] == $row_id ) ) {
				$results[$key] = $array;
				unset( $this->global_fields[$key] );
			}
		}
		return ( isset ( $results ) ) ? $results : '';
	}
	
	/***
	***	@get fields by sub row
	***/
	function get_fields_in_subrow( $row_fields, $subrow_id ) {
		if ( !is_array( $row_fields ) ) return '';
		foreach( $row_fields as $key => $array ) {
			if ( !isset( $array['in_sub_row'] ) || ( isset( $array['in_sub_row'] ) && $array['in_sub_row'] == $subrow_id ) ) {
				$results[$key] = $array;
				unset( $this->global_fields[$key] );
			}
		}
		return ( isset ( $results ) ) ? $results : '';
	}
	
	/***
	***	@Display the builder
	***/
	function show_builder(){
		global $ultimatemember;

		//print_r( get_option('um_form_rowdata_' . $this->form_id ) );
		
		$fields = $ultimatemember->query->get_attr('custom_fields', $this->form_id );

		if ( !isset( $fields ) || empty( $fields ) ) { ?>
	
		<div class="um-admin-drag-row">
		
			<!-- Master Row Actions -->
			<div class="um-admin-drag-row-icons">
					<a href="#" class="um-admin-drag-rowsub-add um-admin-tipsy-n" title="<?php _e('Add Row','ultimate-member'); ?>" data-row_action="add_subrow"><i class="um-icon-plus"></i></a>
					<a href="#" class="um-admin-drag-row-edit um-admin-tipsy-n" title="<?php _e('Edit Row','ultimate-member'); ?>" data-modal="UM_edit_row" data-modal-size="normal" data-dynamic-content="um_admin_edit_field_popup" data-arg1="row" data-arg2="<?php echo $this->form_id; ?>" data-arg3="_um_row_1"><i class="um-faicon-pencil"></i></a>
					<span class="um-admin-drag-row-start"><i class="um-icon-arrow-move"></i></span>
			</div><div class="um-admin-clear"></div>
			
			<div class="um-admin-drag-rowsubs">
			<div class="um-admin-drag-rowsub">
		
				<!-- Column Layout -->
				<div class="um-admin-drag-ctrls columns">
					<a href="#" class="active" data-cols="1"></a>
					<a href="#" data-cols="2"></a>
					<a href="#" data-cols="3"></a>
				</div>
				
				<!-- Sub Row Actions -->
				<div class="um-admin-drag-rowsub-icons">
						<span class="um-admin-drag-rowsub-start"><i class="um-icon-arrow-move"></i></span>
				</div><div class="um-admin-clear"></div>
				
				<!-- Columns -->
				<div class="um-admin-drag-col">
				
				</div>
				
				<div class="um-admin-drag-col-dynamic"></div>
				
				<div class="um-admin-clear"></div>
			
			</div>
			</div>

		</div>
		
		<?php
		
		} else {
		
		if( empty( $fields) || ! is_array( $fields ) ){
			$this->global_fields = array();
		}else{
		 	$this->global_fields = $fields;
		}
		
		foreach( $this->global_fields as $key => $array ) {
			if ( $array['type'] == 'row' ) {
				$rows[$key] = $array;
				unset( $this->global_fields[ $key ] ); // not needed now
			}
			
		}
		
		if ( !isset( $rows ) ){
			$rows = array( '_um_row_1' => array(
					'type' => 'row', 
					'id' => '_um_row_1',
					'sub_rows' => 1,
					'cols' => 1
				)
			);
		}
		
		foreach ( $rows as $row_id => $array ) {
		
		?>
		
		<div class="um-admin-drag-row" data-original="<?php echo $row_id; ?>">
		
			<!-- Master Row Actions -->
			<div class="um-admin-drag-row-icons">
					<a href="#" class="um-admin-drag-rowsub-add um-admin-tipsy-n" title="<?php _e('Add Row','ultimate-member'); ?>" data-row_action="add_subrow"><i class="um-icon-plus"></i></a>
					<a href="#" class="um-admin-drag-row-edit um-admin-tipsy-n" title="<?php _e('Edit Row','ultimate-member'); ?>" data-modal="UM_edit_row" data-modal-size="normal" data-dynamic-content="um_admin_edit_field_popup" data-arg1="row" data-arg2="<?php echo $this->form_id; ?>" data-arg3="<?php echo $row_id; ?>"><i class="um-faicon-pencil"></i></a>
					<span class="um-admin-drag-row-start"><i class="um-icon-arrow-move"></i></span>
					<?php if ( $row_id != '_um_row_1' ) {?>
					<a href="#" class="um-admin-tipsy-n" title="<?php _e('Delete Row','ultimate-member'); ?>" data-remove_element="um-admin-drag-row"><i class="um-faicon-trash-o"></i></a>
					<?php } ?>
			</div><div class="um-admin-clear"></div>
			
			<div class="um-admin-drag-rowsubs">
			
			<?php
			
			$row_fields = $this->get_fields_by_row( $row_id );

			$sub_rows = ( isset( $array['sub_rows'] ) ) ? $array['sub_rows'] : 1;
			for( $c = 0; $c < $sub_rows; $c++  ) {
			
			$subrow_fields = $this->get_fields_in_subrow( $row_fields, $c );

			?>
			
			<div class="um-admin-drag-rowsub">
		
				<!-- Column Layout -->
				<div class="um-admin-drag-ctrls columns">
				
					<?php
					
					if ( !isset( $array['cols'] ) ){
						$col_num = 1;
					} else {
					
					$col_split = explode(':', $array['cols'] );
					$col_num = $col_split[$c];
					
					}
					
					for ( $i = 1; $i <= 3; $i++ ) {
						echo '<a href="#" data-cols="'.$i.'" ';
						if ( $col_num == $i ) echo 'class="active"';
						echo '></a>';
					}
					
					?>
					
				</div>
				
				<!-- Sub Row Actions -->
				<div class="um-admin-drag-rowsub-icons">
						<span class="um-admin-drag-rowsub-start"><i class="um-icon-arrow-move"></i></span>
						<?php if ( $c > 0 ) { ?><a href="#" class="um-admin-tipsy-n" title="Delete Row" data-remove_element="um-admin-drag-rowsub"><i class="um-faicon-trash-o"></i></a><?php } ?>
				</div><div class="um-admin-clear"></div>
				
				<!-- Columns -->
				<div class="um-admin-drag-col">
				
					<?php 

					if ( is_array( $subrow_fields ) ) {
					
					$subrow_fields = $this->array_sort_by_column( $subrow_fields, 'position');

					foreach( $subrow_fields as $key => $keyarray ) {
					extract( $keyarray );
					
					?>
					
					<div class="um-admin-drag-fld um-admin-delete-area um-field-type-<?php echo $type; ?> <?php echo $key; ?>" data-group="<?php echo (isset($keyarray['in_group'])) ? $keyarray['in_group'] : ''; ?>" data-key="<?php echo $key; ?>" data-column="<?php echo ( isset($keyarray['in_column']) ) ? $keyarray['in_column'] : 1; ?>">
						
						<div class="um-admin-drag-fld-title um-field-type-<?php echo $type; ?>">
							<?php if ( $type == 'group' ) { ?>
								<i class="um-icon-plus"></i>
							<?php } else if ( isset($keyarray['icon']) && !empty( $keyarray['icon'] ) ) { ?>
								<i class="<?php echo $keyarray['icon']; ?>"></i>
							<?php } ?><?php echo $title; ?></div>
							<?php $field_name = isset( $ultimatemember->builtin->core_fields[$type]['name'] )?$ultimatemember->builtin->core_fields[$type]['name']:''; ?>
						<div class="um-admin-drag-fld-type um-field-type-<?php echo $type; ?>"><?php echo $field_name; ?></div>
						<div class="um-admin-drag-fld-icons um-field-type-<?php echo $type; ?>">
						
							<a href="#" class="um-admin-tipsy-n" title="Edit" data-modal="UM_edit_field" data-modal-size="normal" data-dynamic-content="um_admin_edit_field_popup" data-arg1="<?php echo $type; ?>" data-arg2="<?php echo $this->form_id; ?>" data-arg3="<?php echo $key; ?>"><i class="um-faicon-pencil"></i></a>
							
							<a href="#" class="um-admin-tipsy-n um_admin_duplicate_field" title="Duplicate" data-silent_action="um_admin_duplicate_field" data-arg1="<?php echo $key; ?>" data-arg2="<?php echo $this->form_id; ?>"><i class="um-faicon-files-o"></i></a>
							
							<?php if ( $type == 'group' ) { ?>
						
							<a href="#" class="um-admin-tipsy-n" title="Delete Group" data-remove_element="um-admin-drag-fld.um-field-type-group" data-silent_action="um_admin_remove_field" data-arg1="<?php echo $key; ?>" data-arg2="<?php echo $this->form_id; ?>"><i class="um-faicon-trash-o"></i></a>
							<?php } else { ?>
							
							<a href="#" class="um-admin-tipsy-n" title="Delete" data-silent_action="um_admin_remove_field" data-arg1="<?php echo $key; ?>" data-arg2="<?php echo $this->form_id; ?>"><i class="um-faicon-trash-o"></i></a>
							
							<?php } ?>
						
						</div><div class="um-admin-clear"></div>
						
						<?php if ( $type == 'group' ) { ?>
						<div class="um-admin-drag-group">
						
						</div>
						<?php } ?>
						
					</div>
					
					<?php 
					
					} // end foreach
					
					} // end if
					
					?>
				
				</div>
				
				<div class="um-admin-drag-col-dynamic"></div>
				
				<div class="um-admin-clear"></div>
			
			</div>
			
			<?php } ?>
			
			</div>

		</div>
		
		<?php
		
		} // rows loop
		
		} // if fields exist
		
	}
	
}