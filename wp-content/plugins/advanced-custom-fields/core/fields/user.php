<?php

class acf_field_user extends acf_field
{
	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function __construct()
	{
		// vars
		$this->name = 'user';
		$this->label = __("User",'acf');
		$this->category = __("Relational",'acf');
		$this->defaults = array(
			'role' 			=> 'all',
			'field_type' 	=> 'select',
			'allow_null' 	=> 0,
		);
		
		
		// do not delete!
    	parent::__construct();
    	
	}

	
	/*
	*  format_value_for_api()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{

		// format value
		if( !$value || $value == 'null' )
		{
			return false;
		}
		
		
		// temp convert to array
		$is_array = true;
		
		if( !is_array($value) )
		{
			$is_array = false;
			$value = array( $value );
		}

		
		foreach( $value as $k => $v )
		{
			$user_data = get_userdata( $v );
			
			//cope with deleted users by @adampope
			if( !is_object($user_data) )
			{
				unset( $value[$k] );
				continue;
			}

			
			$value[ $k ] = array();
			$value[ $k ]['ID'] = $v;
			$value[ $k ]['user_firstname'] = $user_data->user_firstname;
			$value[ $k ]['user_lastname'] = $user_data->user_lastname;
			$value[ $k ]['nickname'] = $user_data->nickname;
			$value[ $k ]['user_nicename'] = $user_data->user_nicename;
			$value[ $k ]['display_name'] = $user_data->display_name;
			$value[ $k ]['user_email'] = $user_data->user_email;
			$value[ $k ]['user_url'] = $user_data->user_url;
			$value[ $k ]['user_registered'] = $user_data->user_registered;
			$value[ $k ]['user_description'] = $user_data->user_description;
			$value[ $k ]['user_avatar'] = get_avatar( $v );
			
		}
		
		
		// de-convert from array
		if( !$is_array && isset($value[0]) )
		{
			$value = $value[0];
		}
		

		// return value
		return $value;
		
	}
	
	
	/*
	*  input_admin_head()
	*
	*  This action is called in the admin_head action on the edit screen where your field is created.
	*  Use this action to add css and javascript to assist your create_field() action.
	*
	*  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_head()
	{
		if( ! function_exists( 'get_editable_roles' ) )
		{ 
			// if using front-end forms then we need to add this core file
			require_once( ABSPATH . '/wp-admin/includes/user.php' ); 
		}
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - an array holding all the field's data
	*/
	
	function create_field( $field )
	{
		if( ! function_exists( 'get_editable_roles' ) )
		{ 
			// if using front-end forms then we need to add this core file
			require_once( ABSPATH . '/wp-admin/includes/user.php' ); 
		}
		
		// options
   		$options = array(
			'post_id' => get_the_ID(),
		);
		
		
		// vars
		$args = array();
		
		
		// editable roles
		$editable_roles = get_editable_roles();
		
		if( !empty($field['role']) )
		{
			if( ! in_array('all', $field['role']) )
			{
				foreach( $editable_roles as $role => $role_info )
				{
					if( !in_array($role, $field['role']) )
					{
						unset( $editable_roles[ $role ] );
					}
				}
			}
			
		}
		
		// filters
		$args = apply_filters('acf/fields/user/query', $args, $field, $options['post_id']);
		$args = apply_filters('acf/fields/user/query/name=' . $field['_name'], $args, $field, $options['post_id'] );
		$args = apply_filters('acf/fields/user/query/key=' . $field['key'], $args, $field, $options['post_id'] );
		
		
		// get users
		$users = get_users( $args );
		
		
		if( !empty($users) && !empty($editable_roles) )
		{
			$field['choices'] = array();
			
			foreach( $editable_roles as $role => $role_info )
			{
				// vars
				$this_users = array();
				$this_json = array();
				
				
				// loop over users
				$keys = array_keys($users);
				foreach( $keys as $key )
				{
					if( in_array($role, $users[ $key ]->roles) )
					{
						$this_users[] = $users[ $key ];
						unset( $users[ $key ] );
					}
				}
				
				
				// bail early if no users for this role
				if( empty($this_users) )
				{
					continue;
				}
				
				
				// label
				$label = translate_user_role( $role_info['name'] );
				
				
				// append to choices
				$field['choices'][ $label ] = array();
				
				foreach( $this_users as $user )
				{
					$field['choices'][ $label ][ $user->ID ] = ucfirst( $user->display_name );
				}
				
			}
		}
		
		
		// modify field
		if( $field['field_type'] == 'multi_select' )
		{
			$field['multiple'] = 1;
		}
		
		
		$field['type'] = 'select';
		
		
		do_action('acf/create_field', $field);			
		
	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function create_options( $field )
	{
		// vars
		$key = $field['name'];
		
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e( "Filter by role", 'acf' ); ?></label>
	</td>
	<td>
		<?php 
		
		$choices = array('all' => __('All', 'acf'));
		$editable_roles = get_editable_roles();

		foreach( $editable_roles as $role => $details )
		{			
			// only translate the output not the value
			$choices[$role] = translate_user_role( $details['name'] );
		}

		do_action('acf/create_field', array(
			'type' => 'select',
			'name' => 'fields[' . $key . '][role]',
			'value'	=> $field['role'],
			'choices' => $choices,
			'multiple' => '1',
		));
		
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Field Type",'acf'); ?></label>
	</td>
	<td>
		<?php	
		do_action('acf/create_field', array(
			'type'	=>	'select',
			'name'	=>	'fields['.$key.'][field_type]',
			'value'	=>	$field['field_type'],
			'choices' => array(
				__("Multiple Values",'acf') => array(
					//'checkbox' => __('Checkbox', 'acf'),
					'multi_select' => __('Multi Select', 'acf')
				),
				__("Single Value",'acf') => array(
					//'radio' => __('Radio Buttons', 'acf'),
					'select' => __('Select', 'acf')
				)
			)
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Allow Null?",'acf'); ?></label>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'radio',
			'name'	=>	'fields['.$key.'][allow_null]',
			'value'	=>	$field['allow_null'],
			'choices'	=>	array(
				1	=>	__("Yes",'acf'),
				0	=>	__("No",'acf'),
			),
			'layout'	=>	'horizontal',
		));
		?>
	</td>
</tr>
		<?php
		
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field )
	{
		// array?
		if( is_array($value) && isset($value['ID']) )
		{
			$value = $value['ID'];	
		}
		
		// object?
		if( is_object($value) && isset($value->ID) )
		{
			$value = $value->ID;
		}
		
		return $value;
	}
	
		
}

new acf_field_user();

?>