<?php

	/***
	***	@modify field args just before it is saved into form
	***/
	add_filter('um_admin_pre_save_field_to_form', 'um_admin_pre_save_field_to_form', 1 );
	function um_admin_pre_save_field_to_form( $array ){
		unset( $array['conditions'] );
		if ( isset($array['conditional_field']) && !empty( $array['conditional_action'] ) && !empty( $array['conditional_operator'] ) ) {
			$array['conditions'][] = array( $array['conditional_action'], $array['conditional_field'], $array['conditional_operator'], $array['conditional_value'] );
		}

		if ( isset($array['conditional_field1']) && !empty( $array['conditional_action1'] ) && !empty( $array['conditional_operator1'] ) ) {
			$array['conditions'][] = array( $array['conditional_action1'], $array['conditional_field1'], $array['conditional_operator1'], $array['conditional_value1'] );
		}

		if ( isset($array['conditional_field2']) && !empty( $array['conditional_action2'] ) && !empty( $array['conditional_operator2'] ) ) {
			$array['conditions'][] = array( $array['conditional_action2'], $array['conditional_field2'], $array['conditional_operator2'], $array['conditional_value2'] );
		}

		if ( isset($array['conditional_field3']) && !empty( $array['conditional_action3'] ) && !empty( $array['conditional_operator3'] ) ) {
			$array['conditions'][] = array( $array['conditional_action3'], $array['conditional_field3'], $array['conditional_operator3'], $array['conditional_value3'] );
		}

		if ( isset($array['conditional_field4']) && !empty( $array['conditional_action4'] ) && !empty( $array['conditional_operator4'] ) ) {
			$array['conditions'][] = array( $array['conditional_action4'], $array['conditional_field4'], $array['conditional_operator4'], $array['conditional_value4'] );
		}

		return $array;
	}

	/***
	***	@Some fields may require extra fields before saving
	***/
	add_filter('um_admin_pre_save_fields_hook', 'um_admin_pre_save_fields_hook', 1 );
	function um_admin_pre_save_fields_hook( $array ){
		global $ultimatemember;
		extract( $array );

		$metabox = new UM_Admin_Metabox();

		$fields_without_metakey = array('block','shortcode','spacing','divider','group');
		$fields_without_metakey = apply_filters('um_fields_without_metakey', $fields_without_metakey );

		$fields = $ultimatemember->query->get_attr('custom_fields', $form_id);
		$count = 1;
		if ( isset( $fields ) && !empty( $fields) ) $count = count($fields)+1;

		// set unique meta key
		if ( in_array( $field_type, $fields_without_metakey ) && !isset($array['post']['_metakey']) ) {
			$array['post']['_metakey'] = "um_{$field_type}_{$form_id}_{$count}";
		}

		// set position
		if ( !isset( $array['post']['_position'] ) ) {
			$array['post']['_position'] = $count;
		}

		return $array;
	}

	/***
	***	@Apply a filter to handle errors for field updating in backend
	***/
	add_filter('um_admin_field_update_error_handling', 'um_admin_field_update_error_handling', 1, 2 );
	function um_admin_field_update_error_handling( $errors, $array ){
		global $ultimatemember;
		extract( $array );

		$field_attr = $ultimatemember->builtin->get_core_field_attrs( $field_type );

		if ( isset( $field_attr['validate'] ) ) {

			$validate = $field_attr['validate'];
			foreach ( $validate as $post_input => $arr ) {

				$mode = $arr['mode'];

				switch ( $mode ) {

					case 'numeric':
						if ( !empty( $array['post'][$post_input] ) && !is_numeric( $array['post'][$post_input] ) ){
							$errors[$post_input] = $validate[$post_input]['error'];
						}
						break;

					case 'unique':
						if ( !isset( $array['post']['edit_mode'] ) ) {
							if ( $ultimatemember->builtin->unique_field_err( $array['post'][$post_input] ) ) {
								$errors[$post_input] = $ultimatemember->builtin->unique_field_err( $array['post'][$post_input] );
							}
						}
						break;

					case 'required':
						if (  $array['post'][$post_input] == '' )
							$errors[$post_input] = $validate[$post_input]['error'];
						break;

					case 'range-start':
						if ( $ultimatemember->builtin->date_range_start_err( $array['post'][$post_input] ) && $array['post']['_range'] == 'date_range' )
							$errors[$post_input] = $ultimatemember->builtin->date_range_start_err( $array['post'][$post_input] );
							break;

					case 'range-end':
						if ( $ultimatemember->builtin->date_range_end_err( $array['post'][$post_input], $array['post']['_range_start'] ) && $array['post']['_range'] == 'date_range' )
							$errors[$post_input] = $ultimatemember->builtin->date_range_end_err( $array['post'][$post_input], $array['post']['_range_start'] );
							break;

				}

			}

		}

		return $errors;

	}

    /***
    *** @Filter validation types on loop
    ****/
	add_filter('um_builtin_validation_types_continue_loop', 'um_builtin_validation_types_continue_loop', 1, 4);
    function um_builtin_validation_types_continue_loop( $break, $key, $form_id, $field_array ){

        // show unique username validation only for user_login field
        if ( isset( $field_array['metakey'] ) && $field_array['metakey'] == 'user_login' && $key !== 'unique_username' ){
            return false;
        }

        return $break;
    }
