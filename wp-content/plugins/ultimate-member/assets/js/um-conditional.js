
jQuery(document).ready(function() {

	var arr_fields = [];
	var arr_field_keys = [];
	var arr_all_conditions = []; //raw 
	var um_field_conditions = {}; // filtered
	var um_field_default_values = {};
	
	/**
	 * Get all conditional fields
	 * @param  object $dom 
	 * @return object field condition settings
	 */
	function um_get_field_conditional_attrs( $dom ) {
	    var attrs = [];
	    var field_atts = ['action','field','operator','value'];
	    var key =  $dom.data('key');
	    
	    arr_field_keys.push( key );

	    jQuery.each( $dom[0].attributes, function ( index, attribute ) {
	    	if( attribute.name.indexOf('data-cond') != -1 ){
	    		attrs.push( attribute.value );
	    	}
	    });

	    var i = attrs.length / 4;

	    var splitlength = ( i <= 0 ) ? 4: i;
	    var attr = {}
	    attr[ key ] = um_splitup_array( attrs, splitlength );

	    jQuery.each( attr[ key ], function( ii,dd ){
	    	var obj = {}
	    	obj['field'] = {
	    	 	owner: 		 key,
	    	 	action: 	 dd.action,
	    	 	parent: 	 dd.if_field,
	    	 	operator: 	 dd.operator,
	    	 	value: 		 dd.value,
	    	 	condition: {
	    	 		owner: 		 key,
		    	 	action: 	 dd.action,
		    	 	operator: 	 dd.operator,
		    	 	value: 		 dd.value
		    	 }
	    	 };

	    	 arr_all_conditions.push( obj );
	    });

	    return attr;
	}

	/**
	 * UM Conditional fields Init
	 */
	function um_init_field_conditions(){

		jQuery('.um-field[data-key]').each(function(){
			var me = jQuery(this);
			arr_fields.push( um_get_field_conditional_attrs( me ) );
			um_field_default_values[ me.data('key') ] = um_get_field_default_value( me );
		});

		jQuery.each( arr_field_keys, function( i,field_key ){
			um_field_conditions[ field_key ] = um_get_field_children( field_key );
		});

		jQuery('.um-field[data-key]:visible').each(function(){
			var $wrap_dom = jQuery(this);
			var me = um_get_field_element( $wrap_dom );
			if( typeof me.trigger !== 'undefined' ){
				me.trigger('change');
			}
		});

	}

	/**
	 * Get field default value
	 * @param object $dom 
	 * @return string
	 */
	function um_get_field_default_value( $dom ){
		var default_value = '';
		var type = um_get_field_type( $dom );

		switch( type ){

		 	case 'text':
		 	case 'number':
		 	case 'date':
		 	case 'textarea':
		 	case 'select':
		 		default_value = $dom.find('input:text,input[type=number],textarea,select').val();
		 	break;

		 	case 'multiselect':
					default_value = $dom.find('select').val();
		 	
			break;

		 	case 'radio':
		 	case 'checkbox':

		 		if ( $dom.find('input[type=checkbox]:checked').length >= 1 ) {

						if ( $dom.find('input[type=checkbox]:checked').length > 1 ) {
							$dom.find('input[type=checkbox]:checked').each(function(){
								default_value = default_value + jQuery(this).val() + ' ';
							});
						} else {
							default_value = $dom.find('input[type=checkbox]:checked').val();
						}

				}

				if ( $dom.find('input[type=radio]:checked').length >= 1 ) {
						default_value =  $dom.find('input[type=radio]:checked').val();
				}

		 	break;

		 	
		}
    		
	    return {type: type, value: default_value};
	}

	/**
	 * Get field element by field wrapper
	 * @param  object $dom 
	 * @return object
	 */
	function um_get_field_element( $dom ){
		var default_value = '';
		var type = um_get_field_type( $dom );

		switch( type ){

		 	case 'text':
		 	case 'number':
		 	case 'date':
		 	case 'textarea':
		 	case 'select':
		 	case 'multiselect':
			case 'radio':
		 	case 'checkbox':
				return $dom.find('input,textarea,select');
		 	break;


		 	
		}
    		
	    return '';
	}

	/**
	 * Get field type 
	 * @param  object $dom 
	 * @return string
	 */
	function um_get_field_type( $dom ){
		var type = '';
		var classes = $dom.attr('class');
		jQuery.each( classes.split(' '), function(i,d){
		 	if( d.indexOf('um-field-type') != -1 ){
		 		type = d.split('_')[1];
			}
		});

		return type;

	}

	/**
	 * Get field siblings/chidren conditions
	 * @param  string field_key 
	 * @return array
	 */
	function um_get_field_children( field_key ){
		var arr_conditions = [];
		jQuery.each( arr_all_conditions, function( ii, condition ){
			if( condition.field.parent == field_key ){
				arr_conditions.push( condition.field.condition );
			}
		});

		return arr_conditions;

	}
	
	/**
	 * Split single array to multi-dimensional array
	 * @param  array arr 
	 * @param  integer n   
	 * @return array
	 */
	function um_splitup_array(arr, n) {
	    var rest = arr.length % n,
	        restUsed = rest,
	        partLength = Math.floor(arr.length / n),
	        result = [];
	    
	    for(var i = 0; i < arr.length; i += partLength) {
	        var end = partLength + i,
	            add = false;
	        
	        if(rest !== 0 && restUsed) {
	            end++;
	            restUsed--;
	            add = true;
	        }
	        
	        result.push(arr.slice(i, end));
	        
	        if(add) {
	            i++;
	        }
	    }

	    var obj_result = [];
	    jQuery.each( result, function(ii,dd ){
	    	 obj_result.push({
	    	 	action: 	dd[0],
	    	 	if_field: 	dd[1],
	    	 	operator: 	dd[2],
	    	 	value: 		dd[3]
	    	 })
	    });

	    return obj_result;
	}

	/**
	 * Get field live value
	 * @param  object $dom 
	 * @return mixed
	 */
	function um_get_field_data( $dom ){
		um_live_field = $dom.parents('.um-field').data('key');
		um_live_value = $dom.val();

		if ( $dom.is(':checkbox') ) {

				um_live_value = '';
					
				if ( $dom.parents('.um-field').find('input:checked').length > 1 ) {
					$dom.parents('.um-field').find('input:checked').each(function(){
						um_live_value = um_live_value + jQuery(this).val() + ' ';
					});
				} else {
					if( $dom.parents('.um-field').find('input:checked').length >= 1 ){
						um_live_value = $dom.parents('.um-field').find('input:checked').val();
					}
				}

		}

		if ( $dom.is(':radio') ) {
				um_live_value = $dom.parents('.um-field').find('input[type=radio]:checked').val();
		}

		return um_live_value;
		
	}

	/**
	 * Apply field conditions
	 * @param  object  $dom             
	 * @param  boolean is_single_update 
	 */
	function um_apply_conditions( $dom, is_single_update ){
		
		var operators = ['empty','not empty','equals to','not equals','greater than','less than','contains'];
		
		var key = $dom.parents('.um-field[data-key]').data('key');
		
		var conditions = um_field_conditions[ key ];
		
		var live_field_value = um_get_field_data( $dom );
		
		jQuery.each( conditions, function( index, condition ){
			
			if ( condition.operator == 'empty' ) {
				if ( !live_field_value || live_field_value == '' ) {
					 	um_field_apply_action( $dom, condition, true );
				}else{
					 	um_field_apply_action( $dom, condition, false );
				}
			}

			if ( condition.operator == 'not empty' ) {
				if ( live_field_value && live_field_value != '' ) {
					 	um_field_apply_action( $dom, condition, true );
				}else{
					 	um_field_apply_action( $dom, condition, false );
				} 
			}

			if ( condition.operator == 'equals to' ) {
				if ( condition.value == live_field_value  ) {
					 	um_field_apply_action( $dom, condition, true );
				}else{
					 	um_field_apply_action( $dom, condition, false );
				} 
			}

			if ( condition.operator == 'not equals' ) {
				if ( jQuery.isNumeric( condition.value ) && parseInt( live_field_value ) != parseInt( condition.value ) && live_field_value  ) {
					 	um_field_apply_action( $dom, condition, true );
				} else if ( !jQuery.isNumeric( condition.value ) && condition.value != live_field_value  ) {
					 	um_field_apply_action( $dom, condition, true );
				}else{
					 	um_field_apply_action( $dom, condition, false );
				}
			}

			if ( condition.operator == 'greater than' ) {
				if ( jQuery.isNumeric( condition.value ) && parseInt( live_field_value ) > parseInt( condition.value )  ) {
					 	um_field_apply_action( $dom, condition, true );
				}else{
					 	um_field_apply_action( $dom, condition, false );
				}
			}

			if ( condition.operator == 'less than' ) {
				if ( jQuery.isNumeric( condition.value ) && parseInt( live_field_value ) < parseInt( condition.value ) && live_field_value  ) {
					 	um_field_apply_action( $dom, condition, true );
				}else{
					 	um_field_apply_action( $dom, condition, false );
				} 
			}

			if ( condition.operator == 'contains' ) {
				if ( live_field_value && live_field_value.indexOf( condition.value ) >= 0  ) {
					 	um_field_apply_action( $dom, condition, true );
				}else{
					 	um_field_apply_action( $dom, condition, false );
				} 
			}

		}); // end foreach `conditions`

		$dom.trigger('um_fields_change');
	
	}

	/**
	 * Apply condition's action
	 * @param  object  $dom      
	 * @param  string  condition 
	 * @param  boolean is_true   
	 */
	function um_field_apply_action( $dom, condition, is_true ){
		var child_dom = jQuery('div.um-field[data-key="'+condition.owner+'"]');

		if( condition.action == 'show' && is_true /*&& child_dom.is(':hidden')*/ ){
			child_dom.show();
			um_field_restore_default_value( child_dom );
		}

		if( condition.action == 'show' && ! is_true /*&& child_dom.is(':visible') */  ){
			child_dom.hide();
		}

		if( condition.action == 'hide' && is_true  /*&& child_dom.is(':visible')*/  ){
			child_dom.hide();
		}

		if( condition.action == 'hide' && !is_true /*&& child_dom.is(':hidden')*/   ){
			child_dom.show();
			um_field_restore_default_value( child_dom );
			
		}
		$dom.removeClass('um-field-has-changed');
	}

	/**
	 * Restores default field value
	 * @param  object $dom 
	 */
	function um_field_restore_default_value( $dom ){
		um_field_default_values

		var type = um_get_field_type( $dom );
		var key = $dom.data('key');
		var field = um_field_default_values[ key ];
		switch( type ){

		 	case 'text':
		 	case 'number':
		 	case 'date':
		 	case 'textarea':
		 		$dom.find('input:text,input[type=number],textarea,select').val( field.value );
		 	break;

		 	case 'select':
		 		$dom.find('select').find('option').prop('selected',false);
				$dom.find('select').val( field.value );
				$dom.find('select').trigger('change');
			break;
		 	
		 	case 'multiselect':
				$dom.find('select').find('option').prop('selected',false);
				jQuery.each( field.value, function(i,value){
					$dom.find('select').find('option[value="'+value+'"]').attr('selected',true);
				});
				$dom.find('select').trigger('change');
			break;

		 	case 'radio':
		 	case 'checkbox':
		 				
		 		if ( $dom.find('input[type=checkbox]:checked').length >= 1 ) {
		 				
		 				$dom.find('input[type=checkbox]:checked').removeAttr('checked');
						$dom.find('span.um-field-checkbox-state i').removeClass('um-icon-android-checkbox-outline');
				 		$dom.find('span.um-field-checkbox-state i').addClass('um-icon-android-checkbox-outline-blank');
				 		$dom.find('.um-field-checkbox.active').removeClass('active');		
				 		
				 		if ( jQuery.isArray( field.value ) ) {
							jQuery.each(field.value, function(i, value ){
								var cbox_elem = $dom.find('input[type=checkbox][value="'+value+'"]');
								cbox_elem.attr('checked', true);
								cbox_elem.closest('.um-field-checkbox').find('i').removeClass('um-icon-android-checkbox-outline-blank');
							 	cbox_elem.closest('.um-field-checkbox').find('i').addClass('um-icon-android-checkbox-outline');
							 	cbox_elem.closest('.um-field-checkbox').addClass('active');
							});
						}else{
							var cbox_elem = $dom.find('input[type=checkbox][value="'+field.value+'"]');
								cbox_elem.attr('checked', true);
								cbox_elem.closest('.um-field-checkbox').find('i').removeClass('um-icon-android-checkbox-outline-blank');
							 	cbox_elem.closest('.um-field-checkbox').find('i').addClass('um-icon-android-checkbox-outline');
							 	cbox_elem.closest('.um-field-checkbox').addClass('active');
						}

				}

				if ( $dom.find('input[type=radio]:checked').length >= 1 ) {

					setTimeout(function(){
					
						$dom.find('input[type=radio]:checked').removeAttr('checked');
					 		
						$dom.find('span.um-field-radio-state i').removeClass('um-icon-android-radio-button-on');
					 	$dom.find('span.um-field-radio-state i').addClass('um-icon-android-radio-button-off');
					 	$dom.find('.um-field-radio.active').removeClass('active');
					 	
					 	var radio_elem = $dom.find("input[type=radio][value='"+field.value+"']");	
						radio_elem.attr('checked', true);
						radio_elem.closest('.um-field-radio').find('i').removeClass('um-icon-android-radio-button-off');
					 	radio_elem.closest('.um-field-radio').find('i').addClass('um-icon-android-radio-button-on');
					 	radio_elem.closest('.um-field-radio').addClass('active');

				 	},100);
				}

				

		 	break;

		 	
		} // end switch type

		if( ! $dom.hasClass('um-field-has-changed') ){
			var me = um_get_field_element( $dom );
			if( me ){
				me.trigger('change');
				$dom.addClass('um-field-has-changed');
			}
		}
	}

	/**
	 * Hides sibling/child field when parent field is hidden
	 */
	function um_field_hide_siblings(){
		
		jQuery.each( um_field_conditions, function(index, conditions ){
			if( jQuery('.um-field[data-key="'+index+'"]:hidden').length >= 1 || jQuery('.um-field[data-key="'+index+'"]').css('display') == 'none' ){
				jQuery.each( conditions,function( key,condition ){
					jQuery('.um-field[data-key="'+condition.owner+'"]').hide();

				});
			}

		});
		
	}

	jQuery(document).on('change','.um-field select, .um-field input[type=radio], .um-field input[type=checkbox]', function(){
		var me = jQuery(this);
		um_apply_conditions( me, false );
	});

	jQuery(document).on('input change', '.um-field input[type=text]', function(){
		var me = jQuery(this);
		um_apply_conditions( me, false );
	});
	
	jQuery(document).on('um_fields_change',function(){
		um_field_hide_siblings();
		um_field_hide_siblings(); // dupes, issue with false field wrapper's visiblity validations. requires optimization.
	});

	um_init_field_conditions();

	
});