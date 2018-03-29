<?php

	/***
	***	@Process a new field
	***/
	add_action('wp_ajax_nopriv_ultimatemember_admin_update_field', 'ultimatemember_admin_update_field');
	add_action('wp_ajax_ultimatemember_admin_update_field', 'ultimatemember_admin_update_field');
	function ultimatemember_admin_update_field(){
		global $ultimatemember;
		
		if ( !is_user_logged_in() || !current_user_can('manage_options') ) die( __('Please login as administrator','ultimate-member') );
		
		$output['error'] = null;

		$array = array( 
			'field_type' => $_POST['_type'],
			'form_id' =>  $_POST['post_id'],
			'args' => $ultimatemember->builtin->get_core_field_attrs(  $_POST['_type'] ),
			'post' => $_POST
		);
		
		$array = apply_filters("um_admin_pre_save_fields_hook", $array );
		$output['error'] = apply_filters( 'um_admin_field_update_error_handling', $output['error'], $array );
		
		extract( $array['post'] );
		if ( empty( $output['error'] ) ){
		
			$save = array();
			$save[ $_metakey ] = null;
			foreach( $array['post'] as $key => $val){
				
				if ( substr( $key, 0, 1) === '_' && $val != '' ) { // field attribute
					$new_key = ltrim ($key,'_');
					
					if ( $new_key == 'options' ) {
						//$save[ $_metakey ][$new_key] = explode(PHP_EOL, $val);
						$save[ $_metakey ][$new_key] = preg_split('/[\r\n]+/', $val, -1, PREG_SPLIT_NO_EMPTY);
					} else {
						$save[ $_metakey ][$new_key] = $val;
					}
					
				} else if ( strstr( $key, 'um_editor' ) ) {
					$save[ $_metakey ]['content'] = $val;
				}
				
			}

			$field_ID = $_metakey;
			$field_args = $save[ $_metakey ];
			
			$field_args = apply_filters("um_admin_pre_save_field_to_form", $field_args );

			$ultimatemember->fields->update_field( $field_ID, $field_args, $post_id );
			
			$field_args = apply_filters("um_admin_pre_save_field_to_db", $field_args );
			
			if ( !isset( $array['args']['form_only'] ) ) {
				if ( !isset( $ultimatemember->builtin->predefined_fields[ $field_ID ] ) ) {
					$ultimatemember->fields->globally_update_field( $field_ID, $field_args );
				}
			}

		}
		
		$output=json_encode($output);
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
	}