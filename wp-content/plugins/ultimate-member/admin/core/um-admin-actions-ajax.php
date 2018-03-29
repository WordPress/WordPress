<?php

	/***
	***	@for ajax actions
	***/
	add_action('wp_ajax_nopriv_ultimatemember_do_ajax_action', 'ultimatemember_do_ajax_action');
	add_action('wp_ajax_ultimatemember_do_ajax_action', 'ultimatemember_do_ajax_action');
	function ultimatemember_do_ajax_action(){
		global $ultimatemember;

		if ( !is_user_logged_in() || !current_user_can('manage_options') ) die( __('Please login as administrator','ultimate-member') );
		
		extract($_POST);
		
		$output = null;
		
		$position = array();
		if ( $in_column ) {
			$position['in_row'] = '_um_row_' . ( (int)$in_row+1 );
			$position['in_sub_row'] = $in_sub_row;
			$position['in_column'] = $in_column;
			$position['in_group'] = $in_group;
		}
		
		switch ( $act_id ) {

			case 'um_admin_duplicate_field':
				$ultimatemember->fields->duplicate_field( $arg1, $arg2 );
				break;
				
			case 'um_admin_remove_field_global':
				$ultimatemember->fields->delete_field_from_db( $arg1 );
				break;
				
			case 'um_admin_remove_field':
				$ultimatemember->fields->delete_field_from_form( $arg1, $arg2 );
				break;
				
			case 'um_admin_add_field_from_predefined':
				$ultimatemember->fields->add_field_from_predefined( $arg1, $arg2, $position );
				break;
				
			case 'um_admin_add_field_from_list':
				$ultimatemember->fields->add_field_from_list( $arg1, $arg2, $position );
				break;

		}
		
		if(is_array($output)){ print_r($output); }else{ echo $output; } die;
		
	}

	/**
	 *  Retrieves dropdown/multi-select options from a callback function
     */
	add_action('wp_ajax_nopriv_ultimatemember_populate_dropdown_options', 'ultimatemember_populate_dropdown_options');
	add_action('wp_ajax_ultimatemember_populate_dropdown_options', 'ultimatemember_populate_dropdown_options');
	function ultimatemember_populate_dropdown_options(){

		$arr_options = array();

		if( ! current_user_can('manage_options') ){
			wp_die( __( 'This is not possible for security reasons.','ultimate-member') );
		}
		
        $um_callback_func = $_POST['um_option_callback'];
        if( empty( $um_callback_func ) ){
        	$arr_options['status'] = 'empty';
        	$arr_options['function_name'] = $um_callback_func;
        	$arr_options['function_exists'] = function_exists( $um_callback_func );
        }
        
        $arr_options['data'] = array();

		if( function_exists( $um_callback_func ) ){
			$arr_options['data'] = call_user_func( $um_callback_func );
		}

		wp_send_json( $arr_options );
	}

	