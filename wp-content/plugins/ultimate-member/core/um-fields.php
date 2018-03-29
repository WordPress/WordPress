<?php

class UM_Fields {

	function __construct() {

		$this->editing = false;
		$this->viewing = false;
		$this->timestamp = current_time('timestamp');

	}

	/**
	 * Standard checkbox field
	 * @param  integer $id   
	 * @param  string $title
	 */
	function checkbox( $id, $title ) {
		?>

		<div class="um-field um-field-c">
			<div class="um-field-area">
				<label class="um-field-checkbox active">
					<input type="checkbox" name="<?php echo $id; ?>" value="1" checked /><span class="um-field-checkbox-state"><i class="um-icon-android-checkbox-outline"></i></span>
					<span class="um-field-checkbox-option"> <?php echo $title; ?></span>
				</label>
			</div>
		</div>

		<?php
	}


	/**
	 * Shows social links
	 */
	function show_social_urls(){
		global $ultimatemember;
		$fields = $ultimatemember->builtin->all_user_fields;
		foreach( $fields as $field => $args ) {
			if ( isset( $args['advanced'] ) && $args['advanced'] == 'social' ) {
				$social[$field] = $args;
			}
		}
		foreach( $social as $k => $arr ) {
			if ( um_profile( $k ) ) { ?>

				<a href="<?php echo um_filtered_social_link( $k , $arr['match'] ); ?>" style="background: <?php echo $arr['color']; ?>;" target="_blank" class="um-tip-n" title="<?php echo $arr['title']; ?>"><i class="<?php echo $arr['icon']; ?>"></i></a>

			<?php
			}
		}
	}

	
	/**
	 * Hidden field inside a shortcode
	 * @param string $field 
	 */
	function add_hidden_field( $field ) {
		global $ultimatemember;
		echo '<div style="display: none !important;">';

			$fields = $ultimatemember->builtin->get_specific_fields( $field );

			$output = null;

			foreach( $fields as $key => $data ) {
				$output .= $ultimatemember->fields->edit_field( $key, $data );
			}

			echo $output;

		echo '</div>';
	}

	/**
	 * Get hidden field
	 * @param  string $key  
	 * @param  string $value
	 * @return string      
	 */
	function disabled_hidden_field( $key, $value ){

			return '<input type="hidden" name="'.$key.'" value="'.esc_attr( $value ).'"/>';
	}

	
	/**
	 * Updates a field globally
	 * @param  integer $id  
	 * @param  array $args
	 */
	function globally_update_field( $id, $args ){
		global $ultimatemember;
		$fields = $ultimatemember->builtin->saved_fields;

		$fields[$id] = $args;

		unset( $fields[ $id ]['in_row'] );
		unset( $fields[ $id ]['in_sub_row'] );
		unset( $fields[ $id ]['in_column'] );
		unset( $fields[ $id ]['in_group'] );
		unset( $fields[ $id ]['position'] );

		update_option('um_fields', $fields );
	}

	
	/**
	 * Updates a field in form only
	 * @param  integer $id     
	 * @param  array $args   
	 * @param  integer $form_id
	 */
	function update_field($id, $args, $form_id){
		global $ultimatemember;
		$fields = $ultimatemember->query->get_attr( 'custom_fields', $form_id );

		if ( $args['type'] == 'row' ) {
			if ( isset( $fields[$id] ) ){
				$old_args = $fields[$id];
				foreach( $old_args as $k => $v ) {
					if (!in_array($k, array('sub_rows','cols')) ) {
						unset($old_args[$k]);
					}
				}
				$args = array_merge( $old_args, $args );
			}
		}

		// custom fields support
		if ( isset( $ultimatemember->builtin->predefined_fields[$id] ) && isset( $ultimatemember->builtin->predefined_fields[$id]['custom'] ) ) {
			$args = array_merge( $ultimatemember->builtin->predefined_fields[$id], $args);
		}

		$fields[$id] = $args;

		// for group field only
		if ( $args['type'] == 'group' ){
			$fields[$id]['in_group'] = '';
		}

		$ultimatemember->query->update_attr( 'custom_fields', $form_id, $fields );
	}

	
	/**
	 * Deletes a field in form only
	 * @param  integer $id      
	 * @param  integer $form_id 
	 */
	function delete_field_from_form( $id, $form_id ) {
		global $ultimatemember;
		$fields = $ultimatemember->query->get_attr( 'custom_fields', $form_id );
		if ( isset( $fields[ $id ] ) ) {
			unset( $fields[ $id ] );
			$ultimatemember->query->update_attr( 'custom_fields', $form_id, $fields );
		}
	}

	
	/**
	 * Deletes a field from custom fields
	 * @param  integer $id 
	 */
	function delete_field_from_db( $id ) {
		global $ultimatemember;
		$fields = $ultimatemember->builtin->saved_fields;
		if ( isset( $fields[$id] ) ){
			unset( $fields[$id] );
			update_option('um_fields', $fields );
		}
	}

	/**
	 * Quickly adds a field from custom fields
	 * @param integer $global_id 
	 * @param integer $form_id   
	 * @param array  $position  
	 */
	function add_field_from_list( $global_id, $form_id, $position = array() ) {
		global $ultimatemember;
		$fields = $ultimatemember->query->get_attr( 'custom_fields', $form_id );
		$field_scope = $ultimatemember->builtin->saved_fields;
		
		if ( !isset( $fields[ $global_id ] ) ) {

			$count = 1;
			if ( isset( $fields ) && !empty( $fields ) ) $count = count( $fields ) + 1;

			$fields[ $global_id ] = $field_scope[ $global_id ];
			$fields[ $global_id ]['position'] = $count;

			// set position
			if ( $position ) {
				foreach( $position as $key => $val ) {
					$fields[ $global_id ][ $key ] = $val;
				}
			}

			// add field to form
			$ultimatemember->query->update_attr( 'custom_fields', $form_id, $fields );

		}
	}

	/**
	 * Quickly adds a field from pre-defined fields
	 * @param integer $global_id 
	 * @param integer $form_id   
	 * @param array  $position  
	 */
	function add_field_from_predefined( $global_id, $form_id, $position = array() ) {
		global $ultimatemember;

		$fields = $ultimatemember->query->get_attr( 'custom_fields', $form_id );
		$field_scope = $ultimatemember->builtin->predefined_fields;
		
		if ( !isset( $fields[ $global_id ] ) ) {

			$count = 1;
			if ( isset( $fields ) && !empty( $fields) ) $count = count( $fields ) + 1;

			$fields[ $global_id ] = $field_scope[ $global_id ];
			$fields[ $global_id ]['position'] = $count;

			// set position
			if ( $position ) {
				foreach( $position as $key => $val ) {
					$fields[ $global_id ][ $key ] = $val;
				}
			}

			// add field to form
			$ultimatemember->query->update_attr( 'custom_fields', $form_id, $fields );

			// add field to db
			//$this->globally_update_field( $global_id, $fields[$global_id] );

		}
	}

	/**
	 * Duplicates a frield by meta key
	 * @param  integer $id      
	 * @param  integer $form_id 
	 */
	function duplicate_field( $id, $form_id ) {
		global $ultimatemember;
		$fields = $ultimatemember->query->get_attr( 'custom_fields', $form_id );
		$all_fields = $ultimatemember->builtin->saved_fields;

		$inc = count( $fields ) + 1;

		$duplicate = $fields[ $id ];

		$new_metakey = $id . "_" . $inc;
		$new_title = $fields[ $id ]['title'] . " #" . $inc;
		$new_position = $inc;

		$duplicate['title'] = $new_title;
		$duplicate['metakey'] = $new_metakey;
		$duplicate['position'] = $new_position;

		$fields[ $new_metakey ] = $duplicate;
		$all_fields[ $new_metakey ] = $duplicate;

		// not global attributes
		unset( $all_fields[ $new_metakey ]['in_row'] );
		unset( $all_fields[ $new_metakey ]['in_sub_row'] );
		unset( $all_fields[ $new_metakey ]['in_column'] );
		unset( $all_fields[ $new_metakey ]['in_group'] );
		unset( $all_fields[ $new_metakey ]['position'] );

		$ultimatemember->query->update_attr( 'custom_fields', $form_id, $fields );
		update_option('um_fields', $all_fields );

	}

	/**
	 *  Print field error
	 * @param  string $text       
	 * @param  boolean $force_show 
	 */
	function field_error($text, $force_show = false ) {
		global $ultimatemember;
		if ( $force_show ) {
			$output = '<div class="um-field-error"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'.$text.'</div>';
			return $output;
		}
		if ( isset( $this->set_id ) && $ultimatemember->form->processing == $this->set_id ) {
			$output = '<div class="um-field-error"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'.$text.'</div>';
		} else {
			$output = '';
		}

		if ( !$ultimatemember->form->processing ) {
			$output = '<div class="um-field-error"><span class="um-field-arrow"><i class="um-faicon-caret-up"></i></span>'.$text.'</div>';
		}
		return $output;
	}

	/**
	 * Checks if field has a server-side error
	 * @param  string  $key 
	 * @return boolean      
	 */
	function is_error($key) {
		global $ultimatemember;
		return $ultimatemember->form->has_error($key);
	}

	/**
	 * Returns field error
	 * @param  string $key 
	 * @return string
	 */
	function show_error($key) {
		global $ultimatemember;
		return $ultimatemember->form->errors[$key];
	}

	/**
	 *  Display field label
	 * @param  string $label 
	 * @param  string $key   
	 * @param  data $data  
	 * @return  string    
	 */
	function field_label( $label, $key, $data ) {
		global $ultimatemember;
		$output = null;
		$output .= '<div class="um-field-label">';

		if ( isset($data['icon']) && $data['icon'] != '' && isset( $this->field_icons ) && $this->field_icons != 'off' && ( $this->field_icons == 'label' || $this->viewing == true ) ) {
			$output .= '<div class="um-field-label-icon"><i class="'.$data['icon'].'"></i></div>';
		}

		if ( $this->viewing == true ) {
			$label = apply_filters("um_view_label_{$key}", $label );
		} else {
			$label = apply_filters("um_edit_label_{$key}", $label );
			$label = apply_filters("um_edit_label_all_fields", $label, $data );
		}

		$output .= '<label for="'.$key.$ultimatemember->form->form_suffix.'">'.__( $label, UM_TEXTDOMAIN ).'</label>';

		if ( isset( $data['help'] ) && !empty( $data['help'] ) && $this->viewing == false && !strstr($key, 'confirm_user_pass') ) {

			if ( !$ultimatemember->mobile->isMobile() ) {
				if ( !isset( $this->disable_tooltips ) ) {
					$output .= '<span class="um-tip um-tip-w" title="'.__( $data['help'], UM_TEXTDOMAIN ).'"><i class="um-icon-help-circled"></i></span>';
				}
			}

			if ( $ultimatemember->mobile->isMobile() || isset( $this->disable_tooltips ) ) {
				$output .= '<span class="um-tip-text">'.__( $data['help'], UM_TEXTDOMAIN ). '</span>';
			}

		}

		$output .= '<div class="um-clear"></div></div>';

		return $output;
	}

	
	/**
	 * Output field classes
	 * @param  string $key  
	 * @param  array $data 
	 * @param  string $add  
	 * @return string
	 */
	function get_class($key, $data, $add = null) {
		$classes = null;

		$classes .= 'um-form-field ';

		if ( $this->is_error($key) ) {
			$classes .= 'um-error ';
		} else {
			$classes .= 'valid ';
		}

		if ( !isset($data['required']) ) {
			$classes .= 'not-required ';
		}

		if ( $data['type'] == 'date' ) {
			$classes .= 'um-datepicker ';
		}

		if ( $data['type'] == 'time' ) {
			$classes .= 'um-timepicker ';
		}

		if ( isset($data['icon']) && $data['icon'] && isset( $this->field_icons ) && $this->field_icons == 'field' ) {
			$classes .= 'um-iconed ';
		}

		if ($add) {
			$classes .= $add . ' ';
		}

		return $classes;
	}

	
	/**
	 * Gets field value
	 * @param  string  $key     
	 * @param  boolean $default 
	 * @param  array $data    
	 * @return mixed
	 */
	function field_value( $key, $default = false, $data = null ) {
		global $ultimatemember;
 		

		if ( isset( $_SESSION ) && isset( $_SESSION['um_social_profile'][ $key ] ) && isset( $this->set_mode ) && $this->set_mode == 'register' )
			return $_SESSION['um_social_profile'][ $key ];

		$type = ( isset( $data['type'] ) ) ? $data['type'] : '';

		// preview in backend
		if ( isset( $ultimatemember->user->preview ) && $ultimatemember->user->preview ) {
			$submitted = um_user('submitted');
			if ( isset( $submitted[ $key ] ) && ! empty( $submitted[ $key ] ) ) {
				return $submitted[ $key ];
			} else {
				return 'Undefined';
			}
		}

		// normal state
		if ( isset( $ultimatemember->form->post_form[ $key ] ) ) {

			if ( strstr( $key, 'user_pass' ) && $this->set_mode != 'password' ) return '';

			return stripslashes_deep( $ultimatemember->form->post_form[ $key ] );

		} else if ( um_user( $key ) && $this->editing == true ) {

			if ( strstr( $key, 'user_pass' ) ) return '';
			$value = um_user( $key );
			$value = apply_filters( "um_edit_{$key}_field_value",  $value,  $key );
			$value = apply_filters( "um_edit_{$type}_field_value", $value,  $key );

			return $value;

		} else if ( ( um_user( $key ) || isset( $data['show_anyway'] ) ) && $this->viewing == true ) {

			$value = um_filtered_value( $key, $data );
			return $value;

		} else if ( $default ) {

			$default = apply_filters( "um_field_default_value", $default, $data, $type );
			$default = apply_filters( "um_field_{$key}_default_value", $default, $data );
			$default = apply_filters( "um_field_{$type}_default_value", $default, $data );

			return $default;

		} else if ( $this->editing == true ) {

			return apply_filters( "um_edit_{$key}_field_value", '', $key);

		}

		return '';
	}

	
	/**
	 * Checks if an option is selected
	 * @param  string  $key   
	 * @param  string  $value 
	 * @param  array  $data  
	 * @return boolean        
	 */
	function is_selected($key, $value, $data){
		global $ultimatemember;

		$key = apply_filters('um_is_selected_filter_key', $key );

		if ( isset( $ultimatemember->form->post_form[ $key ] ) && is_array( $ultimatemember->form->post_form[ $key ] ) ) {
           
            if ( in_array( $value, $ultimatemember->form->post_form[ $key ] ) ){
				return true;
			}

			if ( in_array( html_entity_decode( $value ), $ultimatemember->form->post_form[ $key ] ) ){
				return true;
			}

		} else {

			if ( !isset( $ultimatemember->form->post_form ) ) {

				$field_value 	= um_user( $key );
				$field_value 	= apply_filters('um_is_selected_filter_value', $field_value, $key );
					   $data	= apply_filters('um_is_selected_filter_data', $data, $key, $field_value );

				if ( $field_value && $this->editing == true && is_array( $field_value ) && ( in_array( $value, $field_value ) || in_array( html_entity_decode( $value ), $field_value ) )  ) {
					return true;
				}

				if ( $field_value && $this->editing == true && !is_array( $field_value ) && $field_value == $value ) {
					return true;
				}

				if ( $field_value && $this->editing == true && !is_array( $field_value ) && html_entity_decode( $field_value ) == html_entity_decode( $value ) ) {
					return true;
				}

				if ( strstr( $data['default'], ', ') ) {
					$data['default'] = explode(', ', $data['default']);
				}

				if ( isset( $data['default'] ) && !is_array( $data['default'] ) && $data['default'] == $value ) {
					return true;
				}

				if ( isset( $data['default'] ) && is_array( $data['default'] ) && in_array( $value, $data['default'] ) ){
					return true;
				}

			} else {

				if ( isset( $ultimatemember->form->post_form[ $key ] ) && $value == $ultimatemember->form->post_form[ $key ] ) {
					return true;
				}



			}

		}

		return false;
	}

	
	/**
	 * Checks if a radio button is selected
	 * @param  string  $key   
	 * @param  string $value 
	 * @param  array $data  
	 * @return boolean        
	 */
	function is_radio_checked($key, $value, $data){
		global $ultimatemember;

		if ( isset( $ultimatemember->form->post_form[$key] ) && is_array( $ultimatemember->form->post_form[$key] ) ) {

			if ( in_array( $value, $ultimatemember->form->post_form[$key] ) ){
				return true;
			}

		} else {

			if ( !isset( $ultimatemember->form->post_form ) ) {
				
				if ( um_user( $key ) && $this->editing == true ) {

					if ( strstr($key, 'role_') ) {
						$key = 'role';
					}

					$um_user_value = um_user( $key );
					
					if( $key == 'role' ){
						$um_user_value = strtolower( $um_user_value );
					}

					if ( $um_user_value == $value ) {
						return true;
					}

					if ( is_array( $um_user_value ) && in_array( $value, $um_user_value ) ) {
						return true;
					}

					if ( is_array( $um_user_value ) ){
					    foreach( $um_user_value as $u) {
							if( $u == html_entity_decode( $value ) ){
								return true;
							}
						}
					}


				} else {

					if ( isset($data['default']) && $data['default'] == $value ) {
						return true;
					}

				}

			} else {

				if ( isset( $ultimatemember->form->post_form[$key] ) && $value == $ultimatemember->form->post_form[$key] ) {
					return true;
				}

			}

		}

		return false;
	}

	
	/**
	 * Get field icon
	 * @param  string $key 
	 * @return string
	 */
	function get_field_icon( $key ) {
		global $ultimatemember;
		$fields = $ultimatemember->builtin->all_user_fields;
		if ( isset( $fields[$key]['icon'] ) )
			return $fields[$key]['icon'];
		return '';
	}

	
	/**
	 * Gets selected option value from a callback function
	 * @param  string $value 
	 * @param  array $data  
	 * @param  string $type  
	 * @return json
	 */
	function get_option_value_from_callback( $value, $data, $type ){
        
        global $ultimatemember;

    	if( in_array( $type , array('select','multiselect') ) && isset( $data['custom_dropdown_options_source'] ) && ! empty( $data['custom_dropdown_options_source'] ) ){
             
             if( function_exists( $data['custom_dropdown_options_source'] ) ){
                
                $arr_options = call_user_func( $data['custom_dropdown_options_source'] );
               
                if( $type == 'select' ){
                	if( isset( $arr_options[ $value ] ) && ! empty( $arr_options[ $value ] ) ) {
                	   return $arr_options[ $value ];
                	}else if( isset( $data['default'] ) && ! empty( $data['default'] ) && empty( $arr_options[ $value ] ) ) {
                		return $arr_options[ $data['default'] ];
                	}else{
                		return '';
                	}
                }

                if( $type == 'multiselect' ){
                	
                	if( is_array( $value ) ){
                		$values = $value;
                	}else{
	                	$values = explode(', ', $value );
	                }

                    $arr_paired_options = array();
                    
                    foreach ( $values as $option ) {
                    	if( isset( $arr_options[ $option ] ) ){
	                       $arr_paired_options[] = $arr_options[ $option ];
	                    }
                    } 

                    return implode( ', ' , $arr_paired_options );
                }

             }

            
    	}

    	return $value;
	}

	/**
	 * Get select options from a callback function
	 * @param  array $data 
	 * @param  string $type 
	 * @return array $arr_options
	 */
	function get_options_from_callback( $data, $type ){


    	if( in_array( $type , array('select','multiselect') ) && isset( $data['custom_dropdown_options_source'] ) && ! empty( $data['custom_dropdown_options_source'] ) ){
             
            if( function_exists( $data['custom_dropdown_options_source'] ) ){
                
                $arr_options = call_user_func( $data['custom_dropdown_options_source'] );
                 
			}

            
    	}

    	return $arr_options;
	}

	/**
	 * Get field type
	 * @param  string $key 
	 * @return string
	 */
	function get_field_type( $key ) {
		global $ultimatemember;
		$fields = $ultimatemember->builtin->all_user_fields;
		if ( isset( $fields[$key]['type'] ) )
			return $fields[$key]['type'];
		return '';
	}

	/**
	 * Get field label
	 * @param  string $key 
	 * @return string      
	 */
	function get_label( $key ) {
		global $ultimatemember;
		$fields = $ultimatemember->builtin->all_user_fields;
		if ( isset( $fields[$key]['label'] ) )
			return $fields[$key]['label'];
		if ( isset( $fields[$key]['title'] ) )
			return $fields[$key]['title'];
		return '';
	}

	
	/**
	 * Get field title
	 * @param  string $key 
	 * @return string     
	 */
	function get_field_title( $key ) {
		global $ultimatemember;
		$fields = $ultimatemember->builtin->all_user_fields;
		if ( isset( $fields[$key]['title'] ) )
			return $fields[$key]['title'];
		if ( isset( $fields[$key]['label'] ) )
			return $fields[$key]['label'];
		return __('Custom Field','ultimate-member');
	}

	/**
	 * Get form fields
	 * @return array
	 */
	function get_fields() {
		$this->fields = array();
		$this->fields = apply_filters("um_get_form_fields", $this->fields );
		return $this->fields;
	}

	
	/**
	 * Get specific field
	 * @param  string $key 
	 * @return array
	 */
	function get_field( $key ) {
		global $ultimatemember;

		$fields = $this->get_fields();

		if ( isset( $fields ) && is_array( $fields ) && isset( $fields[$key] ) ) {
			$array = $fields[$key];
		} else {
			if ( !isset( $ultimatemember->builtin->predefined_fields[$key] ) && !isset( $ultimatemember->builtin->all_user_fields[$key] ) ) {
				return '';
			}
			$array = (isset( $ultimatemember->builtin->predefined_fields[$key] ) ) ? $ultimatemember->builtin->predefined_fields[$key] :  $ultimatemember->builtin->all_user_fields[$key];
		}

		$array['classes'] = null;

		if (!isset($array['placeholder'])) $array['placeholder'] = null;
		if (!isset($array['required'])) $array['required'] = null;
		if (!isset($array['validate'])) $array['validate'] = null;
		if (!isset($array['default'])) $array['default'] = null;

		if ( isset( $array['conditions'] ) && is_array( $array['conditions'] ) && !$this->viewing ) {
			$array['conditional'] = '';

			foreach( $array['conditions'] as $cond_id => $cond ) {
				$array['conditional'] .= ' data-cond-'.$cond_id.'-action="'. $cond[0] . '" data-cond-'.$cond_id.'-field="'. $cond[1] . '" data-cond-'.$cond_id.'-operator="'. $cond[2] . '" data-cond-'.$cond_id.'-value="'. $cond[3] . '"';
			}

			$array['classes'] .= ' um-is-conditional';

		} else {
			$array['conditional'] = null;
		}

		$array['classes'] .= ' um-field-' . $key;
		$array['classes'] .= ' um-field-' . $array['type'];
		$array['classes'] .= ' um-field-type_' . $array['type'];

		switch( $array['type'] ) {

			case 'googlemap':
			case 'youtube_video':
			case 'vimeo_video':
			case 'soundcloud_track':
				$array['disabled'] = '';
				$array['input'] = 'text';
				break;

			case 'text':

				$array['disabled'] = '';

				if ( $key == 'user_login' && isset(  $this->set_mode ) && $this->set_mode == 'account' ) {
					$array['disabled'] = 'disabled="disabled"';
				}

				$array['input'] = 'text';

				break;

			case 'password':

				$array['input'] = 'password';

				break;

			case 'number':

				$array['disabled'] = '';

				break;

			case 'url':

				$array['input'] = 'text';

				break;

			case 'date':

				$array['input'] = 'text';

				if ( !isset( $array['format'] ) ) $array['format'] = 'j M Y';

				switch( $array['format'] ) {
					case 'j M Y':
						$js_format = 'd mmm yyyy';
						break;
					case 'j F Y':
						$js_format = 'd mmmm yyyy';
						break;
					case 'M j Y':
						$js_format = 'mmm d yyyy';
						break;
					case 'F j Y':
						$js_format = 'mmmm d yyyy';
						break;
				}

				$array['js_format'] = $js_format;

				if ( !isset( $array['range'] ) ) $array['range'] = 'years';
				if ( !isset( $array['years'] ) ) $array['years'] = 100;
				if ( !isset( $array['years_x'] ) ) $array['years_x'] = 'past';
				if ( !isset( $array['disabled_weekdays'] ) ) $array['disabled_weekdays'] = '';

				if ( !empty( $array['disabled_weekdays'] ) ) {
					$array['disabled_weekdays'] = '[' . implode(',',$array['disabled_weekdays']) . ']';
				}

				// When date range is strictly defined
				if ( $array['range'] == 'date_range' ) {

					$array['date_min'] = str_replace('/',',',$array['range_start']);
					$array['date_max'] = str_replace('/',',',$array['range_end']);

				} else {

					if ( $array['years_x'] == 'past' ) {

						$date = new DateTime( date('Y-n-d') );
						$past = $date->modify('-'.$array['years'].' years');
						$past = $date->format('Y,n,d');

						$array['date_min'] = $past;
						$array['date_max'] = date('Y,n,d');

					} else if ( $array['years_x'] == 'future' ) {

						$date = new DateTime( date('Y-n-d') );
						$future = $date->modify('+'.$array['years'].' years');
						$future = $date->format('Y,n,d');

						$array['date_min'] = date('Y,n,d');
						$array['date_max'] = $future;

					} else {

						$date = new DateTime( date('Y-n-d') );
						$date_f = new DateTime( date('Y-n-d') );
						$past = $date->modify('-'. ( $array['years'] / 2 ).' years');
						$past = $date->format('Y,n,d');
						$future = $date_f->modify('+'. ( $array['years'] / 2 ).' years');
						$future = $date_f->format('Y,n,d');

						$array['date_min'] = $past;
						$array['date_max'] = $future;

					}

				}

				break;

			case 'time':

				$array['input'] = 'text';

				if ( !isset( $array['format'] ) ) $array['format'] = 'g:i a';

				switch( $array['format'] ) {
					case 'g:i a':
						$js_format = 'h:i a';
						break;
					case 'g:i A':
						$js_format = 'h:i A';
						break;
					case 'H:i':
						$js_format = 'HH:i';
						break;
				}

				$array['js_format'] = $js_format;

				if ( !isset( $array['intervals'] ) ) $array['intervals'] = 60;

				break;

			case 'textarea':

				if (!isset($array['height'])) $array['height'] = '100px';

				break;

			case 'rating':

				if (!isset($array['number'])) $array['number'] = 5;

				break;

			case 'spacing':

				if ( !isset($array['spacing'])){
					$array['spacing'] = '20px';
				}

				break;

			case 'divider':

				if (isset($array['width'])){
					$array['borderwidth'] = $array['width'];
				} else {
					$array['borderwidth'] = 4;
				}

				if (isset($array['color'])){
					$array['bordercolor'] = $array['color'];
				} else {
					$array['bordercolor'] = '#eee';
				}

				if (isset($array['style'])){
					$array['borderstyle'] = $array['style'];
				} else {
					$array['borderstyle'] = 'solid';
				}

				if ( !isset( $array['divider_text'] ) ) {
					$array['divider_text'] = '';
				}

				break;

			case 'image':

				if ( !isset( $array['crop'] ) ) $array['crop'] = 0;

				if ( $array['crop'] == 0 ) {
					$array['crop_data'] = 0;
				} else if ( $array['crop'] == 1 ) {
					$array['crop_data'] = 'square';
				} else if ( $array['crop'] == 2 ) {
					$array['crop_data'] = 'cover';
				} else {
					$array['crop_data'] = 'user';
				}

				if ( !isset( $array['modal_size'] ) ) $array['modal_size'] = 'normal';

				if ( $array['crop'] > 0 ) {
					$array['crop_class'] = 'crop';
				} else {
					$array['crop_class'] = '';
				}

				if ( !isset( $array['ratio'] ) ) $array['ratio'] = 1.0;

				if ( !isset( $array['min_width'] ) ) $array['min_width'] = '';
				if ( !isset( $array['min_height'] ) ) $array['min_height'] = '';

				if ( $array['min_width'] == '' && $array['crop'] == 1 ) $array['min_width'] = 600;
				if ( $array['min_height'] == '' && $array['crop'] == 1 ) $array['min_height'] = 600;

				if ( $array['min_width'] == '' && $array['crop'] == 3 ) $array['min_width'] = 600;
				if ( $array['min_height'] == '' && $array['crop'] == 3 ) $array['min_height'] = 600;

				if (!isset($array['invalid_image'])) $array['invalid_image'] = __("Please upload a valid image!",'ultimate-member');
				if (!isset($array['allowed_types'])) {
					$array['allowed_types'] = "gif,jpg,jpeg,png";
				} else {
					$array['allowed_types'] = implode(',',$array['allowed_types']);
				}
				if (!isset($array['upload_text'])) $array['upload_text'] = '';
				if (!isset($array['button_text'])) $array['button_text'] = __('Upload','ultimate-member');
				if (!isset($array['extension_error'])) $array['extension_error'] =  __("Sorry this is not a valid image.",'ultimate-member');
				if (!isset($array['max_size_error'])) $array['max_size_error'] = __("This image is too large!",'ultimate-member');
				if (!isset($array['min_size_error'])) $array['min_size_error'] = __("This image is too small!",'ultimate-member');
				if (!isset($array['max_files_error'])) $array['max_files_error'] = __("You can only upload one image",'ultimate-member');
				if (!isset($array['max_size'])) $array['max_size'] = 999999999;
				if (!isset($array['upload_help_text'])) $array['upload_help_text'] = '';
				if (!isset($array['icon']) ) $array['icon'] = '';

				break;

			case 'file':

				if ( !isset( $array['modal_size'] ) ) $array['modal_size'] = 'normal';

				if (!isset($array['allowed_types'])) {
					$array['allowed_types'] = "pdf,txt";
				} else {
					$array['allowed_types'] = implode(',',$array['allowed_types']);
				}
				if (!isset($array['upload_text'])) $array['upload_text'] = '';
				if (!isset($array['button_text'])) $array['button_text'] = __('Upload','ultimate-member');
				if (!isset($array['extension_error'])) $array['extension_error'] =  __("Sorry this is not a valid file.",'ultimate-member');
				if (!isset($array['max_size_error'])) $array['max_size_error'] = __("This file is too large!",'ultimate-member');
				if (!isset($array['min_size_error'])) $array['min_size_error'] = __("This file is too small!",'ultimate-member');
				if (!isset($array['max_files_error'])) $array['max_files_error'] = __("You can only upload one file",'ultimate-member');
				if (!isset($array['max_size'])) $array['max_size'] = 999999999;
				if (!isset($array['upload_help_text'])) $array['upload_help_text'] = '';
				if (!isset($array['icon']) ) $array['icon'] = '';

				break;

			case 'select':

				break;

			case 'multiselect':

				break;

			case 'group':

				if ( !isset( $array['max_entries'] ) ) $array['max_entries'] = 0;

				break;

		}

		if ( !isset( $array['visibility'] ) ) $array['visibility'] = 'all';

		$array = apply_filters("um_get_field__{$key}", $array );
		

		return $array;
	}

	/**
	 * Gets a field in 'input mode'
	 * @param  string $key  
	 * @param  array  $data 
	 * @param  boolean $rule 
	 * @return string
	 */
	function edit_field( $key, $data, $rule=false ) {
		global $ultimatemember;

		$output = null;
		$disabled = '';

		// get whole field data
		if ( isset( $data ) && is_array( $data ) ) {
			$data = $this->get_field($key);
			if( is_array( $data ) ){
				extract($data);
			}
		}

		if ( !isset( $data['type'] ) ) return;

		if ( isset( $data['in_group'] ) && $data['in_group'] != '' && $rule != 'group' ) return;
		
		if ( $visibility == 'view' && $this->set_mode != 'register' ) return;

		if ( ( $visibility == 'view' && $this->set_mode == 'register' ) || 
			( isset( $data['editable'] ) && $data['editable'] == 0 && $this->set_mode == 'profile' ) ){
				
				if( ! um_user_can('can_edit_everyone') ){
					$disabled = ' disabled="disabled" ';
				}

				if ( isset( $data['public'] ) && $data['public'] == '-2' && $data['roles'] ){
					if ( in_array( $ultimatemember->query->get_role_by_userid( get_current_user_id() ), $data['roles'] ) ){
						$disabled = '';
					}
				}

		}

		if( ! isset( $data['autocomplete'] ) ){
			$autocomplete = 'off';
		}


		if ( !um_can_view_field( $data ) ) return;
		if ( !um_can_edit_field( $data ) ) return;


		// fields that need to be disabled in edit mode (profile)
		$arr_restricted_fields = array('user_email','username','user_login','user_password');
		
		if( um_get_option('editable_primary_email_in_profile') == 1 ){
			unset( $arr_restricted_fields[0] ); // remove user_email
		}

		if ( in_array( $key, $arr_restricted_fields ) && $this->editing == true && $this->set_mode == 'profile' ) {
			return;
		}

		// forbidden in edit mode?
		if ( isset( $data['edit_forbidden'] ) ) return;

		// required option
		if ( isset( $data['required_opt'] ) ) {
			$opt = $data['required_opt'];
			if ( um_get_option( $opt[0] ) != $opt[1] ) {
				return;
			}
		}

		// required user permission
		if ( isset( $data['required_perm'] ) ) {
			if ( !um_user( $data['required_perm'] ) ) {
				return;
			}
		}

		// do not show passwords
		if ( isset( $ultimatemember->user->preview ) && $ultimatemember->user->preview ) {
			if ( $data['type'] == 'password' ){
				return;
			}
		}

		$type = apply_filters("um_hook_for_field_{$type}", $type );

		/* Begin by field type */
		switch( $type ) {

			/* Default: Integration */
			default:
				$mode = (isset($this->set_mode))?$this->set_mode:'no_mode';
				$output .= apply_filters("um_edit_field_{$mode}_{$type}", $output, $data);
				break;

			/* Other fields */
			case 'googlemap':
			case 'youtube_video':
			case 'vimeo_video':
			case 'soundcloud_track':

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';

						if ( isset($icon) && $icon && isset( $this->field_icons ) && $this->field_icons == 'field' ) {

						$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';

						}

						$field_name = $key.$ultimatemember->form->form_suffix;
						$field_value = htmlspecialchars( $this->field_value( $key, $default, $data ) );

						$output .= '<input '.$disabled.' class="'.$this->get_class($key, $data).'" type="'.$input.'" name="'.$field_name.'" id="'.$field_name.'" value="'. $field_value .'" placeholder="'.$placeholder.'" data-validate="'.$validate.'" data-key="'.$key.'" />

						</div>';
                        
                        if( ! empty( $disabled ) ){
	                        $output .= $this->disabled_hidden_field( $field_name, $field_value ); 
	                    }

						if ( $this->is_error($key) ) {
							$output .= $this->field_error( $this->show_error($key) );
						}

						$output .= '</div>';
				break;

			/* Text */
			case 'text':

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';

						if ( isset($icon) && $icon && isset( $this->field_icons ) && $this->field_icons == 'field' ) {

						$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';

						}

						$field_name = $key.$ultimatemember->form->form_suffix;
						$field_value = htmlspecialchars( $this->field_value( $key, $default, $data ) );

						$output .= '<input '.$disabled.' autocomplete="'.$autocomplete.'" class="'.$this->get_class($key, $data).'" type="'.$input.'" name="'.$field_name.'" id="'.$field_name.'" value="'. $field_value .'" placeholder="'.$placeholder.'" data-validate="'.$validate.'" data-key="'.$key.'" />

						</div>';
                 		
                 		if( ! empty( $disabled ) ){
	                        $output .= $this->disabled_hidden_field( $field_name, $field_value ); 
	                    }

						if ( $this->is_error($key) ) {
							$output .= $this->field_error( $this->show_error($key) );
						}

						$output .= '</div>';
				break;

			/* Number */
			case 'number':

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';

						if ( isset($icon) && $icon && isset( $this->field_icons ) && $this->field_icons == 'field' ) {

						$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';

						}

						$number_limit = '';

						if( isset( $min ) ){
							$number_limit .= " min=\"{$min}\" ";
						}

						if( isset( $max ) ){
							$number_limit .= " max=\"{$max}\" ";
						}

						$output .= '<input '.$disabled.' class="'.$this->get_class($key, $data).'" type="number" name="'.$key.$ultimatemember->form->form_suffix.'" id="'.$key.$ultimatemember->form->form_suffix.'" value="'. htmlspecialchars( $this->field_value( $key, $default, $data ) ) .'" placeholder="'.$placeholder.'" data-validate="'.$validate.'" data-key="'.$key.'" {$number_limit} />

						</div>';

						if ( $this->is_error($key) ) {
							$output .= $this->field_error( $this->show_error($key) );
						}

						$output .= '</div>';
				break;

			/* Password */
			case 'password':

					$original_key = $key;

					if ( $key == 'single_user_password' ) {

							$key = $original_key;

							$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

							if ( isset( $data['label'] ) ) {
								$output .= $this->field_label($label, $key, $data);
							}

							$output .= '<div class="um-field-area">';

							if ( isset($icon) && $icon && $this->field_icons == 'field' ) {

							$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';

							}

							$output .= '<input class="'.$this->get_class($key, $data).'" type="'.$input.'" name="'.$key.$ultimatemember->form->form_suffix.'" id="'.$key.$ultimatemember->form->form_suffix.'" value="'. $this->field_value( $key, $default, $data ) .'" placeholder="'.$placeholder.'" data-validate="'.$validate.'" data-key="'.$key.'" />

							</div>';

							if ( $this->is_error($key) ) {
								$output .= $this->field_error( $this->show_error($key) );
							}

							$output .= '</div>';

					} else {

					if ( $this->set_mode == 'account' && um_is_core_page('account') ) {

						$key = 'current_' . $original_key;
						$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

								if ( isset( $data['label'] ) ) {
								$output .= $this->field_label( __('Current Password','ultimate-member'), $key, $data);
								}

								$output .= '<div class="um-field-area">';

								if ( isset($icon) && $icon && $this->field_icons == 'field' ) {

								$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';

								}

								$output .= '<input class="'.$this->get_class($key, $data).'" type="'.$input.'" name="'.$key.$ultimatemember->form->form_suffix.'" id="'.$key.$ultimatemember->form->form_suffix.'" value="' . $this->field_value( $key, $default, $data ) .'" placeholder="'.$placeholder.'" data-validate="'.$validate.'" data-key="'.$key.'" />

								</div>';

								if ( $this->is_error($key) ) {
									$output .= $this->field_error( $this->show_error($key) );
								}

								$output .= '</div>';

					}

						$key = $original_key;

						$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( $this->set_mode == 'account' && um_is_core_page('account') || $this->set_mode == 'password' && um_is_core_page('password-reset') ) {

							$output .= $this->field_label( __('New Password','ultimate-member'), $key, $data);

						} else if ( isset( $data['label'] ) ) {

							$output .= $this->field_label($label, $key, $data);

						}

						$output .= '<div class="um-field-area">';

						if ( isset($icon) && $icon && $this->field_icons == 'field' ) {

						$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';

						}

						$output .= '<input class="'.$this->get_class($key, $data).'" type="'.$input.'" name="'.$key.$ultimatemember->form->form_suffix.'" id="'.$key.$ultimatemember->form->form_suffix.'" value="'. $this->field_value( $key, $default, $data ) .'" placeholder="'.$placeholder.'" data-validate="'.$validate.'" data-key="'.$key.'" />

						</div>';

						if ( $this->is_error($key) ) {
							$output .= $this->field_error( $this->show_error($key) );
						}

						$output .= '</div>';

					if ( $this->set_mode != 'login' && isset( $data['force_confirm_pass'] ) && $data['force_confirm_pass'] == 1 ) {

						$key = 'confirm_' . $original_key;
						$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

								if ( isset( $data['label'] ) ) {
								$output .= $this->field_label( sprintf(__('Confirm %s','ultimate-member'), $data['label'] ), $key, $data);
								}

								$output .= '<div class="um-field-area">';

								if ( isset($icon) && $icon && $this->field_icons == 'field' ) {

								$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';

								}

								$output .= '<input class="'.$this->get_class($key, $data).'" type="'.$input.'" name="'.$key.$ultimatemember->form->form_suffix.'" id="'.$key.$ultimatemember->form->form_suffix.'" value="' . $this->field_value( $key, $default, $data ) .'" placeholder="'.$placeholder.'" data-validate="'.$validate.'" data-key="'.$key.'" />

								</div>';

								if ( $this->is_error($key) ) {
									$output .= $this->field_error( $this->show_error($key) );
								}

								$output .= '</div>';

					}

					}

					break;

			/* URL */
			case 'url':

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';

						if ( isset($icon) && $icon && isset($this->field_icons) && $this->field_icons == 'field' ) {

						$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';

						}

						$output .= '<input  '.$disabled.'  class="'.$this->get_class($key, $data).'" type="'.$input.'" name="'.$key.$ultimatemember->form->form_suffix.'" id="'.$key.$ultimatemember->form->form_suffix.'" value="'. $this->field_value( $key, $default, $data ) .'" placeholder="'.$placeholder.'" data-validate="'.$validate.'" data-key="'.$key.'" />

						</div>';

						if ( $this->is_error($key) ) {
							$output .= $this->field_error( $this->show_error($key) );
						}

						$output .= '</div>';
				break;

			/* Date */
			case 'date':

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';

						if ( isset($icon) && $icon && isset( $this->field_icons ) && $this->field_icons == 'field' ) {

						$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';

						}

						$output .= '<input  '.$disabled.'  class="'.$this->get_class($key, $data).'" type="'.$input.'" name="'.$key.$ultimatemember->form->form_suffix.'" id="'.$key.$ultimatemember->form->form_suffix.'" value="'. $this->field_value( $key, $default, $data ) .'" placeholder="'.$placeholder.'" data-validate="'.$validate.'" data-key="'.$key.'"    data-range="'.$range.'" data-years="'.$years.'" data-years_x="'.$years_x.'" data-disabled_weekdays="'.$disabled_weekdays.'" data-date_min="'.$date_min.'" data-date_max="'.$date_max.'" data-format="'.$js_format.'" data-value="'. $this->field_value( $key, $default, $data ) .'" />

						</div>';

						if ( $this->is_error($key) ) {
							$output .= $this->field_error( $this->show_error($key) );
						}

						$output .= '</div>';
				break;

			/* Time */
			case 'time':

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';

						if ( isset($icon) && $icon && $this->field_icons == 'field' ) {

						$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';

						}

						$output .= '<input  '.$disabled.'  class="'.$this->get_class($key, $data).'" type="'.$input.'" name="'.$key.$ultimatemember->form->form_suffix.'" id="'.$key.$ultimatemember->form->form_suffix.'" value="'. $this->field_value( $key, $default, $data ) .'" placeholder="'.$placeholder.'" data-validate="'.$validate.'" data-key="'.$key.'"  data-format="'.$js_format.'" data-intervals="'.$intervals.'" data-value="'. $this->field_value( $key, $default, $data ) .'" />

						</div>';

						if ( $this->is_error($key) ) {
							$output .= $this->field_error( $this->show_error($key) );
						}

						$output .= '</div>';
				break;

			/* Row */
			case 'row':
				$output .= '';
				break;

			/* Textarea */
			case 'textarea':
				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';
						$field_name = $key;
						$field_value = $this->field_value( $key, $default, $data );

						if ( isset( $data['html'] ) && $data['html'] != 0 && $key != "description" ) {


							$textarea_settings = array(
								'media_buttons' => false,
								'wpautop' => false,
								'editor_class' => $this->get_class($key, $data),
								'editor_height' => $height,
								'tinymce'=> array(
									'toolbar1' => 'formatselect,bullist,numlist,bold,italic,underline,forecolor,blockquote,hr,removeformat,link,unlink,undo,redo',
									'toolbar2' => '',
								)
							);

							if( ! empty( $disabled ) ){
								$textarea_settings['tinymce']['readonly'] = true;
							}
							
							$textarea_settings = apply_filters('um_form_fields_textarea_settings', $textarea_settings );

							// turn on the output buffer
							ob_start();

							// echo the editor to the buffer
							wp_editor( $field_value , $key, $textarea_settings );

							// add the contents of the buffer to the output variable
							$output .=  ob_get_clean();

						}
						else $output .= '<textarea  '.$disabled.'  style="height: '.$height.';" class="'.$this->get_class($key, $data).'" name="'.$key.'" id="'.$key.'" placeholder="'.$placeholder.'">'.$field_value.'</textarea>';

						$output .= '
						</div>';

						if( ! empty( $disabled ) ){
	                        $output .= $this->disabled_hidden_field( $field_name, $field_value ); 
	                    }
						
						if ( $this->is_error($key) ) {
							$output .= $this->field_error( $this->show_error($key) );
						}

						$output .= '</div>';
				break;

			/* Rating */
			case 'rating':
				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';

						$output .= '<div class="um-rating um-raty" id="'.$key.'" data-key="'.$key.'" data-number="'.$data['number'].'" data-score="' . $this->field_value( $key, $default, $data ) . '"></div>';

						$output .= '</div>';

						$output .= '</div>';

				break;

			/* Gap/Space */
			case 'spacing':
				$output .= '<div class="um-field um-field-spacing' . $classes . '"' . $conditional . ' style="height: '.$spacing.'"></div>';
				break;

			/* A line divider */
			case 'divider':
				$output .= '<div class="um-field um-field-divider' . $classes . '"' . $conditional . ' style="border-bottom: '.$borderwidth.'px '.$borderstyle.' '.$bordercolor.'">';
				if ( $divider_text ) {
					$output .= '<div class="um-field-divider-text"><span>' . $divider_text . '</span></div>';
				}
				$output .= '</div>';
				break;

			/* Single Image Upload */
			case 'image':
				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

					if ( in_array( $key, array('profile_photo','cover_photo') )  ) {
						$field_value = '';
					} else {
						$field_value = $this->field_value( $key, $default, $data );
					}

					$output .= '<input type="hidden" name="'.$key.$ultimatemember->form->form_suffix.'" id="'.$key.$ultimatemember->form->form_suffix.'" value="'. $field_value . '" />';

					if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
					}

					$modal_label = ( isset( $data['label'] ) ) ? $data['label'] : __('Upload Photo','ultimate-member');

					$output .= '<div class="um-field-area" style="text-align: center">';

					if ( $this->field_value( $key, $default, $data ) ) {

						if ( !in_array( $key, array('profile_photo','cover_photo') ) ) {
							if ( isset( $this->set_mode ) && $this->set_mode == 'register' ) {
								$imgValue = $this->field_value( $key, $default, $data );
							} else {
								$imgValue = um_user_uploads_uri() . $this->field_value( $key, $default, $data );
							}
							$img = '<img src="' . $imgValue . '" alt="" />';
						} else {
							$img = '';
						}

						$output .= '<div class="um-single-image-preview show '. $crop_class .'" data-crop="'.$crop_data.'" data-key="'.$key.'">
								<a href="#" class="cancel"><i class="um-icon-close"></i></a>' . $img . '
							</div><a href="#" data-modal="um_upload_single" data-modal-size="'.$modal_size.'" data-modal-copy="1" class="um-button um-btn-auto-width">'. __('Change photo','ultimate-member') . '</a>';

					} else {

						$output .= '<div class="um-single-image-preview '. $crop_class .'" data-crop="'.$crop_data.'" data-key="'.$key.'">
								<a href="#" class="cancel"><i class="um-icon-close"></i></a>
								<img src="" alt="" />
							<div class="um-clear"></div></div><a href="#" data-modal="um_upload_single" data-modal-size="'.$modal_size.'" data-modal-copy="1" class="um-button um-btn-auto-width">'. $button_text . '</a>';

					}

					$output .= '</div>';

					/* modal hidden */
					$output .= '<div class="um-modal-hidden-content">';

					$output .= '<div class="um-modal-header"> ' . $modal_label . '</div>';

					$output .= '<div class="um-modal-body">';

					if ( isset( $this->set_id ) ) {
						$set_id = $this->set_id;
						$set_mode = $this->set_mode;
					} else {
						$set_id = 0;
						$set_mode = '';
					}

					$nonce = wp_create_nonce( 'um_upload_nonce-'.$this->timestamp );

					$output .= '<div class="um-single-image-preview '. $crop_class .'"  data-crop="'.$crop_data.'" data-ratio="'.$ratio.'" data-min_width="'.$min_width.'" data-min_height="'.$min_height.'" data-coord=""><a href="#" class="cancel"><i class="um-icon-close"></i></a><img src="" alt="" /><div class="um-clear"></div></div><div class="um-clear"></div>';
					$output .= '<div class="um-single-image-upload" data-nonce="'.$nonce.'" data-timestamp="'.$this->timestamp.'" data-icon="'.$icon.'" data-set_id="'.$set_id.'" data-set_mode="'.$set_mode.'" data-type="'.$type.'" data-key="'.$key.'" data-max_size="'.$max_size.'" data-max_size_error="'.$max_size_error.'" data-min_size_error="'.$min_size_error.'" data-extension_error="'.$extension_error.'"  data-allowed_types="'.$allowed_types.'" data-upload_text="'.$upload_text.'" data-max_files_error="'.$max_files_error.'" data-upload_help_text="'.$upload_help_text.'">'.$button_text.'</div>';

					$output .= '<div class="um-modal-footer">
									<div class="um-modal-right">
										<a href="#" class="um-modal-btn um-finish-upload image disabled" data-key="'.$key.'" data-change="'.__('Change photo','ultimate-member').'" data-processing="'.__('Processing...','ultimate-member').'"> ' . __('Apply','ultimate-member') . '</a>
										<a href="#" class="um-modal-btn alt" data-action="um_remove_modal"> ' . __('Cancel','ultimate-member') . '</a>
									</div>
									<div class="um-clear"></div>
								</div>';

					$output .= '</div>';

					$output .= '</div>';

					/* end */

					if ( $this->is_error($key) ) {
						$output .= $this->field_error( $this->show_error($key) );
					}

					$output .= '</div>';

				break;

			/* Single File Upload */
			case 'file':
				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

					$output .= '<input type="hidden" name="'.$key.$ultimatemember->form->form_suffix.'" id="'.$key.$ultimatemember->form->form_suffix.'" value="'. $this->field_value( $key, $default, $data ) . '" />';

					if ( isset( $data['label'] ) ) {
					$output .= $this->field_label($label, $key, $data);
					}

					$modal_label = ( isset( $data['label'] ) ) ? $data['label'] : __('Upload Photo','ultimate-member');

					$output .= '<div class="um-field-area" style="text-align: center">';

					if ( $this->field_value( $key, $default, $data ) ) {

						$extension = pathinfo( $this->field_value( $key, $default, $data ), PATHINFO_EXTENSION);

						$output .= '<div class="um-single-file-preview show" data-key="'.$key.'">
										<a href="#" class="cancel"><i class="um-icon-close"></i></a>
										<div class="um-single-fileinfo">
											<a href="' . um_user_uploads_uri() . $this->field_value( $key, $default, $data )  . '" target="_blank">
												<span class="icon" style="background:'. $ultimatemember->files->get_fonticon_bg_by_ext( $extension ) . '"><i class="'. $ultimatemember->files->get_fonticon_by_ext( $extension ) .'"></i></span>
												<span class="filename">' . $this->field_value( $key, $default, $data ) . '</span>
											</a>
										</div>
							</div><a href="#" data-modal="um_upload_single" data-modal-size="'.$modal_size.'" data-modal-copy="1" class="um-button um-btn-auto-width">'. __('Change file','ultimate-member') . '</a>';

					} else {

						$output .= '<div class="um-single-file-preview" data-key="'.$key.'">
							</div><a href="#" data-modal="um_upload_single" data-modal-size="'.$modal_size.'" data-modal-copy="1" class="um-button um-btn-auto-width">'. $button_text . '</a>';

					}

					$output .= '</div>';

					/* modal hidden */
					$output .= '<div class="um-modal-hidden-content">';

					$output .= '<div class="um-modal-header"> ' . $modal_label . '</div>';

					$output .= '<div class="um-modal-body">';

					if ( isset( $this->set_id ) ) {
						$set_id = $this->set_id;
						$set_mode = $this->set_mode;
					} else {
						$set_id = 0;
						$set_mode = '';
					}

					$output .= '<div class="um-single-file-preview">
										<a href="#" class="cancel"><i class="um-icon-close"></i></a>
										<div class="um-single-fileinfo">
											<a href="" target="_blank">
												<span class="icon"><i></i></span>
												<span class="filename"></span>
											</a>
										</div>
								</div>';

					$nonce = wp_create_nonce( 'um_upload_nonce-'.$this->timestamp );

					$output .= '<div class="um-single-file-upload" data-timestamp="'.$this->timestamp.'" data-nonce="'.$nonce.'" data-icon="'.$icon.'" data-set_id="'.$set_id.'" data-set_mode="'.$set_mode.'" data-type="'.$type.'" data-key="'.$key.'" data-max_size="'.$max_size.'" data-max_size_error="'.$max_size_error.'" data-min_size_error="'.$min_size_error.'" data-extension_error="'.$extension_error.'"  data-allowed_types="'.$allowed_types.'" data-upload_text="'.$upload_text.'" data-max_files_error="'.$max_files_error.'" data-upload_help_text="'.$upload_help_text.'">'.$button_text.'</div>';

					$output .= '<div class="um-modal-footer">
									<div class="um-modal-right">
										<a href="#" class="um-modal-btn um-finish-upload file disabled" data-key="'.$key.'" data-change="'.__('Change file').'" data-processing="'.__('Processing...','ultimate-member').'"> ' . __('Save','ultimate-member') . '</a>
										<a href="#" class="um-modal-btn alt" data-action="um_remove_modal"> ' . __('Cancel','ultimate-member') . '</a>
									</div>
									<div class="um-clear"></div>
								</div>';

					$output .= '</div>';

					$output .= '</div>';

					/* end */

					if ( $this->is_error($key) ) {
						$output .= $this->field_error( $this->show_error($key) );
					}

					$output .= '</div>';

				break;

			/* Select dropdown */
			case 'select':

				$form_key = str_replace('role_select','role',$key);

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';


						if ( isset( $data['allowclear'] ) && $data['allowclear'] == 0 ) {
							$class = 'um-s2';
						} else {
							$class = 'um-s1';
						}

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area '.( isset(  $this->field_icons ) && $this->field_icons == 'field' ? 'um-field-area-has-icon':'' ).' ">';
						if ( isset( $icon ) && $icon && isset( $this->field_icons ) && $this->field_icons == 'field' ) {
							$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';
						}
						
						$has_parent_option = false;
						$disabled_by_parent_option = '';
						$atts_ajax = '';
						$select_original_option_value = '';
						
						if( isset( $data['parent_dropdown_relationship'] ) && ! empty( $data['parent_dropdown_relationship'] ) && ! $ultimatemember->user->preview ){
								
								$disabled_by_parent_option = 'disabled = disabled';
								
								$has_parent_option = true;
								
								$parent_dropdown_relationship = apply_filters("um_custom_dropdown_options_parent__{$form_key}", $data['parent_dropdown_relationship'], $data );
								$atts_ajax .= " data-um-parent='{$parent_dropdown_relationship}' ";

								if( isset( $data['custom_dropdown_options_source'] ) && ! empty( $data['custom_dropdown_options_source'] ) &&
									$has_parent_option &&  function_exists( $data['custom_dropdown_options_source'] ) &&
									um_user( $data['parent_dropdown_relationship'] ) ){
									$options = call_user_func( $data['custom_dropdown_options_source'] );
									$disabled_by_parent_option = '';
									if( um_user( $form_key ) ){
										$select_original_option_value = " data-um-original-value='".um_user( $form_key )."' ";
									}
								}

						}

						if( isset( $data['custom_dropdown_options_source'] ) && ! empty( $data['custom_dropdown_options_source'] ) ){
							
							$ajax_source = apply_filters("um_custom_dropdown_options_source__{$form_key}", $data['custom_dropdown_options_source'], $data );
							$atts_ajax .= " data-um-ajax-source='{$ajax_source}' ";

							$ajax_source_url = apply_filters("um_custom_dropdown_options_source_url__{$form_key}", admin_url('admin-ajax.php'), $data );
							$atts_ajax .= " data-um-ajax-url='{$ajax_source_url}' ";


						}

						$output .= '<select  '.$disabled.' '.$select_original_option_value.' '.$disabled_by_parent_option.'  name="'.$form_key.'" id="'.$form_key.'" data-validate="'.$validate.'" data-key="'.$key.'" class="'.$this->get_class($key, $data, $class).'" style="width: 100%" data-placeholder="'.$placeholder.'" '.$atts_ajax.'>';

						if( ! $has_parent_option ){
							if ( isset($options) && $options == 'builtin'){
								$options = $ultimatemember->builtin->get ( $filter );
							}

							if (!isset($options)){
								$options = $ultimatemember->builtin->get ( 'countries' );
							}

							if ( isset( $options ) ) {
								$options = apply_filters('um_select_dropdown_dynamic_options', $options, $data );
								$options = apply_filters("um_select_dropdown_dynamic_options_{$key}", $options );
							}
						}

						// role field
						if ( $form_key == 'role' ) {
							global $wpdb;
							foreach($options as $key => $val ) {
								$val = (string) $val;
								$val = trim( $val );
								$post_id = $wpdb->get_var( 
									$wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'um_role' AND ( post_name = %s OR post_title = %s )", $key, $val )
								);
								$_role = get_post( $post_id );
								if( isset( $_role->post_title ) ){
									$new_roles[ $_role->post_name ] = $_role->post_title;
								}
								wp_reset_postdata();
							}

							$options = $new_roles;
						}

						// add an empty option!
						$output .= '<option value=""></option>';
                        
                        $field_value = '';
                        
                        // switch options pair for custom options from a callback function
                        if( isset( $data['custom_dropdown_options_source'] ) && ! empty( $data['custom_dropdown_options_source'] ) ){
							$options_pair = true;
						}

                       // add options
						foreach($options as $k => $v) {

							$v = rtrim($v);

							$option_value = $v;
							$um_field_checkbox_item_title = $v;
							

							if ( ! is_numeric( $k ) && in_array($form_key, array('role') ) ) {
								$option_value = $k;
								$um_field_checkbox_item_title = $v;
							}
							
							if ( isset( $options_pair ) ) {
								$option_value = $k;
								$um_field_checkbox_item_title = $v;
							}
							
							$option_value = apply_filters('um_field_non_utf8_value',$option_value );
    
							$output .= '<option value="' . $option_value . '" ';
							
							if ( $this->is_selected( $form_key, $option_value, $data ) ) {
								$output.= 'selected';
								$field_value = $option_value;
							}else if( ! isset( $options_pair ) && $this->is_selected( $form_key, $v, $data ) ){
								$output.= 'selected';
								$field_value = $v;
							}
							
							$output .= '>'.__( $um_field_checkbox_item_title, UM_TEXTDOMAIN).'</option>';

							
						}

						if( ! empty( $disabled ) ){
		                        $output .= $this->disabled_hidden_field( $form_key, $field_value ); 
		                 }

						$output .= '</select>';

						$output .= '</div>';


						if ( $this->is_error($form_key) ) {
							$output .= $this->field_error( $this->show_error($form_key) );
						}

						$output .= '</div>';
				break;

			/* Multi-Select dropdown */
			case 'multiselect':

				$max_selections = ( isset( $max_selections ) ) ? absint( $max_selections ) : 0;

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['allowclear'] ) && $data['allowclear'] == 0 ) {
							$class = 'um-s2';
						} else {
							$class = 'um-s1';
						}

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$field_icon = false;
						$field_icon_output = '';

						$use_keyword = apply_filters('um_multiselect_option_value', 0, $data['type'] );

						$output .= '<div class="um-field-area '.( isset(  $this->field_icons ) && $this->field_icons == 'field' ? 'um-field-area-has-icon':'' ).' ">';
						if ( isset( $icon ) && $icon && isset( $this->field_icons ) && $this->field_icons == 'field' ) {
							$output .= '<div class="um-field-icon"><i class="'.$icon.'"></i></div>';
						}
						
						$output .= '<select  '.$disabled.' multiple="multiple" name="'.$key.'[]" id="'.$key.'" data-maxsize="'. $max_selections . '" data-validate="'.$validate.'" data-key="'.$key.'" class="'.$this->get_class($key, $data, $class).' um-user-keyword_'.$use_keyword.'" style="width: 100%" data-placeholder="'.$placeholder.'">';

						
						if ( isset($options) && $options == 'builtin'){
							$options = $ultimatemember->builtin->get ( $filter );
						}

						if (!isset($options)){
							$options = $ultimatemember->builtin->get ( 'countries' );
						}

						if ( isset( $options ) ) {
							$options = apply_filters('um_multiselect_options', $options, $data );
							$options = apply_filters("um_multiselect_options_{$key}", $options );
							$options = apply_filters("um_multiselect_options_{$data['type']}", $options, $data );
						}
						
						// switch options pair for custom options from a callback function
                        if( isset( $data['custom_dropdown_options_source'] ) && ! empty( $data['custom_dropdown_options_source'] ) ){
							$use_keyword = true;
						}

						// add an empty option!
						$output .= '<option value=""></option>';
						
						$arr_selected = array();
						// add options
						if( ! empty( $options ) && is_array( $options ) ){
                        	foreach( $options as $k => $v ) {

								$v = rtrim( $v );

								$um_field_checkbox_item_title = $v;
								$opt_value = $v;
								
								if ( $use_keyword  ) {
									$um_field_checkbox_item_title = $v;
									$opt_value = $k;
								}

								$opt_value = apply_filters('um_field_non_utf8_value',$opt_value );
	    
								$output .= '<option value="'.$opt_value.'" ';
								if ( $this->is_selected( $key, $opt_value, $data ) ) {
									
									$output .= 'selected';
									$arr_selected[ $opt_value ] = $opt_value;	
								}

								$output .= '>'.__( $um_field_checkbox_item_title ,UM_TEXTDOMAIN).'</option>';
								
							}
						}

						$output .= '</select>';

						if( ! empty( $disabled ) && ! empty( $arr_selected )  ){
							foreach( $arr_selected as $item ){
				               $output .= $this->disabled_hidden_field( $key.'[]',  $item  );
							}
			            }

						$output .= '</div>';

						
						if ( $this->is_error($key) ) {
							$output .= $this->field_error( $this->show_error( $key ) );
						}

						$output .= '</div>';
				break;

			/* Radio */
			case 'radio':

				$form_key = str_replace('role_radio','role',$key);

				if ( isset( $options ) ) {
					$options = apply_filters('um_radio_field_options', $options, $data );
					$options = apply_filters("um_radio_field_options_{$key}", $options );
				}

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';

						// role field
						if ( $form_key == 'role' ) {

							global $wpdb;
							foreach($options as $rkey => $val ) {
								$val = (string) $val;
								$val = trim( $val );
								$post_id = $wpdb->get_var(
									$wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'um_role' AND ( post_name = %s OR post_title = %s )", $rkey, $val )
								);
								$_role = get_post($post_id);
								$new_roles[$_role->post_name] = $_role->post_title;
								wp_reset_postdata();
							}

							$options = $new_roles;
						}

						// add options
						$i = 0;
						$field_value = array();

						foreach($options as $k => $v) {

							$v = rtrim($v);

							$um_field_checkbox_item_title = $v;
							$option_value = $v;

							if ( !is_numeric( $k ) && in_array($form_key, array('role') ) ) {
								$um_field_checkbox_item_title = $v;
								$option_value = $k;
							}

							$i++;
							if ($i % 2 == 0 ) {
								$col_class = 'right';
							} else {
								$col_class = '';
							}

							if ( $this->is_radio_checked($key, $option_value, $data) ) {
								$active = 'active';
								$class = "um-icon-android-radio-button-on";
							} else {
								$active = '';
								$class = "um-icon-android-radio-button-off";
							}

							if( isset( $data['editable'] ) &&  $data['editable'] == 0 ){
								$col_class .= " um-field-radio-state-disabled";
							}


				
							$output .= '<label class="um-field-radio '.$active.' um-field-half '.$col_class.'">';

							$option_value = apply_filters('um_field_non_utf8_value',$option_value );
    
							$output .= '<input  '.$disabled.' type="radio" name="'.$form_key.'[]" value="'.$option_value.'" ';

							if ( $this->is_radio_checked($key, $option_value, $data) ) {
								$output.= 'checked';
								$field_value[ $key ] = $option_value;
							}

							$output .= ' />';
						
							$output .= '<span class="um-field-radio-state"><i class="'.$class.'"></i></span>';
							$output .= '<span class="um-field-radio-option">'.__( $um_field_checkbox_item_title,UM_TEXTDOMAIN).'</span>';
							$output .= '</label>';

							if ($i % 2 == 0) {
								$output .= '<div class="um-clear"></div>';
							}

						}

						if( ! empty( $disabled ) ){
							foreach( $field_value as $item ){
			                    $output .= $this->disabled_hidden_field( $form_key , $item );
			                }
			            }
			            
						$output .= '<div class="um-clear"></div>';

						$output .= '</div>';

						if ( $this->is_error($form_key) ) {
							$output .= $this->field_error( $this->show_error($form_key) );
						}

						$output .= '</div>';
				break;

			/* Checkbox */
			case 'checkbox':

				if ( isset( $options ) ) {
					$options = apply_filters('um_checkbox_field_options', $options, $data );
					$options = apply_filters("um_checkbox_field_options_{$key}", $options );
				}

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) ) {
						$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';

						// add options
						$i = 0;

						foreach($options as $k => $v) {

							$v = rtrim($v);

							$i++;
							if ($i % 2 == 0 ) {
								$col_class = 'right';
							} else {
								$col_class = '';
							}

							if ( $this->is_selected($key, $v, $data) ) {
								$active = 'active';
								$class = "um-icon-android-checkbox-outline";
							} else {
								$active = '';
								$class = "um-icon-android-checkbox-outline-blank";
							}

							if( isset( $data['editable'] ) &&  $data['editable'] == 0 ){
								$col_class .= " um-field-radio-state-disabled";
							}

							$output .= '<label class="um-field-checkbox '.$active.' um-field-half '.$col_class.'">';
                            
                            $um_field_checkbox_item_title = $v;

							$v = apply_filters('um_field_non_utf8_value', $v );
    
							$output .= '<input  '.$disabled.' type="checkbox" name="'.$key.'[]" value="'.strip_tags( $v ).'" ';

							if ( $this->is_selected($key, $v, $data) ) {
								$output.= 'checked';
							}

							$output .= ' />';

							if( ! empty( $disabled ) &&  $this->is_selected($key, $v, $data) ){
			                        $output .= $this->disabled_hidden_field( $key.'[]', strip_tags( $v ) ); 
			                }

							
							$output .= '<span class="um-field-checkbox-state"><i class="'.$class.'"></i></span>';
							$um_field_checkbox_item_title = apply_filters("um_field_checkbox_item_title", $um_field_checkbox_item_title , $key, $v, $data );
							$output .= '<span class="um-field-checkbox-option">'. __( $um_field_checkbox_item_title ,UM_TEXTDOMAIN) .'</span>';
							$output .= '</label>';

							if ($i % 2 == 0) {
								$output .= '<div class="um-clear"></div>';
							}

						}

						$output .= '<div class="um-clear"></div>';

						$output .= '</div>';


						if ( $this->is_error($key) ) {
							$output .= $this->field_error( $this->show_error($key) );
						}

						$output .= '</div>';
				break;

			/* HTML */
			case 'block':
				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">
								<div class="um-field-block">'.$content.'</div>
							</div>';
				break;

			/* Shortcode */
			case 'shortcode':

				$content = str_replace('{profile_id}', um_profile_id(), $content );

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">
								<div class="um-field-shortcode">' . do_shortcode($content) . '</div>
							</div>';
				break;

			/* Unlimited Group */
			case 'group':

				$fields = $this->get_fields_in_group( $key );
				if ( !empty( $fields ) ) {

				$output .= '<div class="um-field-group" data-max_entries="'.$max_entries.'">
								<div class="um-field-group-head"><i class="um-icon-plus"></i>'.__($label,UM_TEXTDOMAIN).'</div>';
					$output .= '<div class="um-field-group-body"><a href="#" class="um-field-group-cancel"><i class="um-icon-close"></i></a>';

									foreach($fields as $subkey => $subdata) {
										$output .= $this->edit_field( $subkey, $subdata, 'group' );
									}

					$output .= '</div>';
				$output .= '</div>';

				}

				break;

		}

		// Custom filter for field output
		if ( isset( $this->set_mode ) ) {
			$output = apply_filters("um_{$key}_form_edit_field", $output, $this->set_mode);
		}

		return $output;
	}

	/**
	 * Sorts columns array
	 * @param  array $arr 
	 * @param  string $col 
	 * @param  string $dir 
	 * @return array $arr
	 */
	function array_sort_by_column($arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			if ( isset( $row[$col] ) ) {
				$sort_col[$key] = $row[$col];
			}
		}

		array_multisort($sort_col, $dir, $arr);
		return $arr;
	}

	
	/**
	 * Get fields in row
	 * @param  integer $row_id 
	 * @return string     
	 */
	function get_fields_by_row( $row_id ) {
		foreach( $this->get_fields as $key => $array ) {
			if ( !isset( $array['in_row'] ) || ( isset( $array['in_row'] ) && $array['in_row'] == $row_id ) ) {
				$results[$key] = $array;
			}
		}
		return ( isset ( $results ) ) ? $results : '';
	}

	
	/**
	 * Get fields by sub row
	 * @param  string $row_fields 
	 * @param  integer $subrow_id  
	 * @return mixed
	 */
	function get_fields_in_subrow( $row_fields, $subrow_id ) {
		if ( !is_array( $row_fields ) ) return '';
		foreach( $row_fields as $key => $array ) {
			if ( !isset( $array['in_sub_row'] ) || ( isset( $array['in_sub_row'] ) && $array['in_sub_row'] == $subrow_id ) ) {
				$results[$key] = $array;
			}
		}
		return ( isset ( $results ) ) ? $results : '';
	}

	/**
	 * Get fields in group
	 * @param  integer $group_id 
	 * @return mixed         
	 */
	function get_fields_in_group( $group_id ) {
		foreach( $this->get_fields as $key => $array ) {
			if ( isset( $array['in_group'] ) && $array['in_group'] == $group_id ) {
				$results[$key] = $array;
			}
		}
		return ( isset ( $results ) ) ? $results : '';
	}

	
	/**
	 * Get fields in column
	 * @param  array $fields     
	 * @param  integer $col_number 
	 * @return mixed           
	 */
	function get_fields_in_column( $fields, $col_number ) {
		foreach( $fields as $key => $array ) {
			if ( isset( $array['in_column'] ) && $array['in_column'] == $col_number ) {
				$results[$key] = $array;
			}
		}
		return ( isset ( $results ) ) ? $results : '';
	}

	
	/**
	 * Display fields
	 * @param  string $mode 
	 * @param  array $args 
	 * @return string      
	 */
	function display( $mode, $args ) {
		global $ultimatemember;
		$output = null;

		$this->global_args = $args;

		$ultimatemember->form->form_suffix = '-' . $this->global_args['form_id'];

		$this->set_mode = $mode;
		$this->set_id = $this->global_args['form_id'];

		$this->field_icons = ( isset(  $this->global_args['icons'] ) ) ? $this->global_args['icons'] : 'label';

		// start output here
		$this->get_fields = $this->get_fields();

		if ( !empty( $this->get_fields ) ) {

			// find rows
			foreach( $this->get_fields as $key => $array ) {
				if ( $array['type'] == 'row' ) {
					$this->rows[$key] = $array;
					unset( $this->get_fields[ $key ] ); // not needed anymore
				}
			}

			// rows fallback
			if ( !isset( $this->rows ) ){
				$this->rows = array( '_um_row_1' => array(
						'type' => 'row',
						'id' => '_um_row_1',
						'sub_rows' => 1,
						'cols' => 1
					)
				);
			}

			// master rows
			foreach ( $this->rows as $row_id => $row_array ) {

				$row_fields = $this->get_fields_by_row( $row_id );
				if ( $row_fields ) {

				$output .= $this->new_row_output( $row_id, $row_array );

				$sub_rows = ( isset( $row_array['sub_rows'] ) ) ? $row_array['sub_rows'] : 1;
				for( $c = 0; $c < $sub_rows; $c++  ) {

					// cols
					$cols = ( isset(  $row_array['cols'] ) ) ? $row_array['cols'] : 1;
					if ( strstr( $cols, ':' ) ) {
						$col_split = explode( ':', $cols );
					} else {
						$col_split = array( $cols );
					}
					$cols_num = $col_split[$c];

					// sub row fields
					$subrow_fields = null;
					$subrow_fields = $this->get_fields_in_subrow( $row_fields, $c );

					if ( is_array( $subrow_fields ) ) {

						$subrow_fields = $this->array_sort_by_column( $subrow_fields, 'position');

						if ( $cols_num == 1 ) {

							$output .= '<div class="um-col-1">';
							$col1_fields = $this->get_fields_in_column( $subrow_fields, 1 );
							if ( $col1_fields ) {
							foreach( $col1_fields as $key => $data ) {$output .= $this->edit_field( $key, $data );}
							}
							$output .= '</div>';

						} else if ( $cols_num == 2 ) {

							$output .= '<div class="um-col-121">';
							$col1_fields = $this->get_fields_in_column( $subrow_fields, 1 );
							if ( $col1_fields ) {
							foreach( $col1_fields as $key => $data ) {$output .= $this->edit_field( $key, $data );}
							}
							$output .= '</div>';

							$output .= '<div class="um-col-122">';
							$col2_fields = $this->get_fields_in_column( $subrow_fields, 2 );
							if ( $col2_fields ) {
							foreach( $col2_fields as $key => $data ) {$output .= $this->edit_field( $key, $data );}
							}
							$output .= '</div><div class="um-clear"></div>';

						} else {

							$output .= '<div class="um-col-131">';
							$col1_fields = $this->get_fields_in_column( $subrow_fields, 1 );
							if ( $col1_fields ) {
							foreach( $col1_fields as $key => $data ) {$output .= $this->edit_field( $key, $data );}
							}
							$output .= '</div>';

							$output .= '<div class="um-col-132">';
							$col2_fields = $this->get_fields_in_column( $subrow_fields, 2 );
							if ( $col2_fields ) {
							foreach( $col2_fields as $key => $data ) {$output .= $this->edit_field( $key, $data );}
							}
							$output .= '</div>';

							$output .= '<div class="um-col-133">';
							$col3_fields = $this->get_fields_in_column( $subrow_fields, 3 );
							if ( $col3_fields ) {
							foreach( $col3_fields as $key => $data ) {$output .= $this->edit_field( $key, $data );}
							}
							$output .= '</div><div class="um-clear"></div>';

						}

					}

				}

				$output .= '</div>';

				}

			}

		}

		return $output;
	}

	/**
	 * Gets a field in `view mode`
	 * @param  string  $key  
	 * @param  array  $data 
	 * @param  boolean $rule 
	 * @return string       
	 */
	function view_field( $key, $data, $rule=false ) {
		global $ultimatemember;

		$output = null;

		// get whole field data
		if (is_array($data)) {
			$data = $this->get_field($key);
			extract($data);
		}

		if ( !isset( $data['type'] ) ) return;

		if ( isset( $data['in_group'] ) && $data['in_group'] != '' && $rule != 'group' ) return;

		if ( $visibility == 'edit' ) return;

		if ( in_array( $type, array('block','shortcode','spacing','divider','group') ) ) {

		} else {
			if ( ! $this->field_value( $key, $default, $data ) ) return;
		}

		if ( !um_can_view_field( $data ) ) return;

		// disable these fields in profile view only
		if ( in_array( $key, array('user_password') ) && $this->set_mode == 'profile' ) {
			return;
		}

		if ( !um_field_conditions_are_met( $data ) ) return;

		switch( $type ) {

			/* Default */
			default:

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';
					
						if ( isset( $data['label'] ) || isset( $data['icon'] ) && ! empty( $data['icon'] ) ) {

							if( ! isset( $data['label'] ) ) $data['label'] = '';

							$output .= $this->field_label( $data['label'], $key, $data);
						}
						
						$res = $this->field_value( $key, $default, $data );

						if( ! empty( $res ) ){
							$res = stripslashes( $res );
						}

						$data['is_view_field'] = true;
						$res = apply_filters("um_view_field", $res, $data, $type );
						$res = apply_filters("um_view_field_value_{$type}", $res, $data );

						$output .= '<div class="um-field-area">';
						$output .= '<div class="um-field-value">' . $res . '</div>';
						$output .= '</div>';

						$output .= '</div>';

				break;

			/* HTML */
			case 'block':
				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">
								<div class="um-field-block">'.$content.'</div>
							</div>';
				break;

			/* Shortcode */
			case 'shortcode':

				$content = str_replace('{profile_id}', um_profile_id(), $content );

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">
								<div class="um-field-shortcode">' . do_shortcode($content) . '</div>
							</div>';
				break;

			/* Gap/Space */
			case 'spacing':
				$output .= '<div class="um-field um-field-spacing' . $classes . '"' . $conditional . ' style="height: '.$spacing.'"></div>';
				break;

			/* A line divider */
			case 'divider':
				$output .= '<div class="um-field um-field-divider' . $classes . '"' . $conditional . ' style="border-bottom: '.$borderwidth.'px '.$borderstyle.' '.$bordercolor.'">';
				if ( $divider_text ) {
					$output .= '<div class="um-field-divider-text"><span>' . $divider_text . '</span></div>';
				}
				$output .= '</div>';
				break;

			/* Rating */
			case 'rating':

				$output .= '<div class="um-field' . $classes . '"' . $conditional . ' data-key="'.$key.'">';

						if ( isset( $data['label'] ) || isset( $data['icon'] ) && ! empty( $data['icon'] ) ) {
							$output .= $this->field_label($label, $key, $data);
						}

						$output .= '<div class="um-field-area">';
						$output .= '<div class="um-field-value">
										<div class="um-rating-readonly um-raty" id="'.$key.'" data-key="'.$key.'" data-number="'.$data['number'].'" data-score="' .  $this->field_value( $key, $default, $data ) . '"></div>
									</div>';
						$output .= '</div>';

						$output .= '</div>';

				break;

		}

		// Custom filter for field output
		if ( isset( $this->set_mode ) ) {
			$output = apply_filters("um_{$key}_form_show_field", $output, $this->set_mode);
			$output = apply_filters("um_{$type}_form_show_field", $output, $this->set_mode);

		}

		return $output;
	}

	/**
	 * Display fields ( view mode )
	 * @param  string $mode 
	 * @param  array $args 
	 * @return string       
	 */
	function display_view( $mode, $args ) {
		global $ultimatemember;
		$output = null;

		$this->global_args = $args;

		$ultimatemember->form->form_suffix = '-' . $this->global_args['form_id'];

		$this->set_mode = $mode;
		$this->set_id = $this->global_args['form_id'];

		$this->field_icons = ( isset(  $this->global_args['icons'] ) ) ? $this->global_args['icons'] : 'label';

		// start output here
		$this->get_fields = $this->get_fields();

		if ( um_get_option('profile_empty_text') ) {

			$emo = um_get_option('profile_empty_text_emo');
			if ( $emo ) {
				$emo = '<i class="um-faicon-frown-o"></i>';
			} else {
				$emo = false;
			}

			if ( um_is_myprofile() ) {
				$output .= '<p class="um-profile-note">' . $emo .'<span>' . sprintf(__('Your profile is looking a little empty. Why not <a href="%s">add</a> some information!','ultimate-member'), um_edit_profile_url() ) . '</span></p>';
			} else {
				$output .= '<p class="um-profile-note">'. $emo . '<span>' . __('This user has not added any information to their profile yet.','ultimate-member') . '</span></p>';
			}
		}

		if ( !empty( $this->get_fields ) ) {

			// find rows
			foreach( $this->get_fields as $key => $array ) {
				if ( $array['type'] == 'row' ) {
					$this->rows[$key] = $array;
					unset( $this->get_fields[ $key ] ); // not needed anymore
				}
			}

			// rows fallback
			if ( !isset( $this->rows ) ){
				$this->rows = array( '_um_row_1' => array(
						'type' => 'row',
						'id' => '_um_row_1',
						'sub_rows' => 1,
						'cols' => 1
					)
				);
			}

			// master rows
			foreach ( $this->rows as $row_id => $row_array ) {

				$row_fields = $this->get_fields_by_row( $row_id );
				if ( $row_fields ) {

				$output .= $this->new_row_output( $row_id, $row_array );

				$sub_rows = ( isset( $row_array['sub_rows'] ) ) ? $row_array['sub_rows'] : 1;
				for( $c = 0; $c < $sub_rows; $c++  ) {

					// cols
					$cols = ( isset(  $row_array['cols'] ) ) ? $row_array['cols'] : 1;
					if ( strstr( $cols, ':' ) ) {
						$col_split = explode( ':', $cols );
					} else {
						$col_split = array( $cols );
					}
					$cols_num = $col_split[$c];

					// sub row fields
					$subrow_fields = null;
					$subrow_fields = $this->get_fields_in_subrow( $row_fields, $c );

					if ( is_array( $subrow_fields ) ) {

						$subrow_fields = $this->array_sort_by_column( $subrow_fields, 'position');

						if ( $cols_num == 1 ) {

							$output .= '<div class="um-col-1">';
							$col1_fields = $this->get_fields_in_column( $subrow_fields, 1 );
							if ( $col1_fields ) {
								foreach( $col1_fields as $key => $data ) {

									$data  = apply_filters("um_view_field_output_".$data['type'],  $data);

									$output .= $this->view_field( $key, $data );


								}
							}
							$output .= '</div>';

						} else if ( $cols_num == 2 ) {

							$output .= '<div class="um-col-121">';
							$col1_fields = $this->get_fields_in_column( $subrow_fields, 1 );
							if ( $col1_fields ) {
								foreach( $col1_fields as $key => $data ) {

									$data  = apply_filters("um_view_field_output_".$data['type'],  $data);

									$output .= $this->view_field( $key, $data );

								}
							}
							$output .= '</div>';

							$output .= '<div class="um-col-122">';
							$col2_fields = $this->get_fields_in_column( $subrow_fields, 2 );
							if ( $col2_fields ) {
								foreach( $col2_fields as $key => $data ) {

									$data  = apply_filters("um_view_field_output_".$data['type'],  $data);

									$output .= $this->view_field( $key, $data );

								}
							}
							$output .= '</div><div class="um-clear"></div>';

						} else {

							$output .= '<div class="um-col-131">';
							$col1_fields = $this->get_fields_in_column( $subrow_fields, 1 );
							if ( $col1_fields ) {
								foreach( $col1_fields as $key => $data ) {

									$data  = apply_filters("um_view_field_output_".$data['type'],  $data);

									$output .= $this->view_field( $key, $data );

								}
							}
							$output .= '</div>';

							$output .= '<div class="um-col-132">';
							$col2_fields = $this->get_fields_in_column( $subrow_fields, 2 );
							if ( $col2_fields ) {
								foreach( $col2_fields as $key => $data ) {

									$data  = apply_filters("um_view_field_output_".$data['type'],  $data);

									$output .= $this->view_field( $key, $data );

								}
							}
							$output .= '</div>';

							$output .= '<div class="um-col-133">';
							$col3_fields = $this->get_fields_in_column( $subrow_fields, 3 );
							if ( $col3_fields ) {
								foreach( $col3_fields as $key => $data ) {

									$data  = apply_filters("um_view_field_output_".$data['type'],  $data);

									$output .= $this->view_field( $key, $data );

								}
							}
							$output .= '</div><div class="um-clear"></div>';

						}

					}

				}

				$output .= '</div>';

				}

			}

		}

		return $output;
	}

	/**
	 * Get new row in form
	 * @param  string $row_id    
	 * @param  array $row_array 
	 * @return array         
	 */
	function new_row_output( $row_id, $row_array ) {
		$output = null;
		extract($row_array);

		$padding = (isset($padding))?$padding:'';
		$margin = (isset($margin))?$margin:'';
		$background = (isset($background))?$background:'';
		$text_color = (isset($text_color))?$text_color:'';
		$borderradius = (isset($borderradius))?$borderradius:'';
		$border = (isset($border))?$border:'';
		$bordercolor = (isset($bordercolor))?$bordercolor:'';
		$borderstyle = (isset($borderstyle))?$borderstyle:'';
		$heading = (isset($heading))?$heading:'';
		$css_class = (isset($css_class))?$css_class:'';

		$css_padding = '';
		$css_margin = '';
		$css_background = '';
		$css_borderradius = '';
		$css_border = '';
		$css_bordercolor = '';
		$css_borderstyle = '';
		$css_heading_background_color = '';
		$css_heading_padding = '';
		$css_heading_text_color = '';
		$css_heading_borderradius = '';
		$css_text_color = '';

		// row css rules
		if ( $padding ) $css_padding = 'padding: ' . $padding .';';
		if ( $margin ) {
			$css_margin = 'margin: ' . $margin .';';
		} else {
			$css_margin = 'margin: 0 0 30px 0;';
		}

		if ( $background ) $css_background = 'background-color: ' . $background .';';
		if ( $borderradius ) $css_borderradius = 'border-radius: 0px 0px ' . $borderradius . ' ' . $borderradius . ';';
		if ( $border ) $css_border = 'border-width: ' . $border . ';';
		if ( $bordercolor ) $css_bordercolor = 'border-color: ' . $bordercolor . ';';
		if ( $borderstyle ) $css_borderstyle = 'border-style: ' . $borderstyle . ';';
		if ( $text_color ) $css_text_color = 'color: ' . $text_color . ' !important;';

		// show the heading
		if ( $heading ) {

			$heading_background_color = (isset($heading_background_color))?$heading_background_color:'';
			$heading_text_color = (isset($heading_text_color))?$heading_text_color:'';

			if ( $heading_background_color ) {
				$css_heading_background_color = 'background-color: ' . $heading_background_color .';';
				$css_heading_padding = 'padding: 10px 15px;';
			}

			if ( $heading_text_color ) $css_heading_text_color = 'color: ' . $heading_text_color .';';
			if ( $borderradius ) $css_heading_borderradius = 'border-radius: ' . $borderradius . ' ' . $borderradius . ' 0px 0px;';

			$output .= '<div class="um-row-heading" style="' . $css_heading_background_color . $css_heading_padding . $css_heading_text_color . $css_heading_borderradius . '">';
			
			if ( isset( $icon ) ) {
				$output .= '<span class="um-row-heading-icon"><i class="' . $icon . '"></i></span>';
			}
			
			$output .= ( ! empty( $heading_text ) ? $heading_text: '') .'</div>';

		} else {

			// no heading
			if ( $borderradius ) $css_borderradius = 'border-radius: ' . $borderradius . ';';

		}

		$output .= '<div class="um-row ' . $row_id . ' ' . $css_class . '" style="'. $css_padding . $css_background . $css_margin . $css_border . $css_borderstyle . $css_bordercolor . $css_borderradius . $css_text_color.'">';

		return $output;
	}

	

}
