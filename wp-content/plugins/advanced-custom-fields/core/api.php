<?php

/*
*  get_field_reference()
*
*  This function will find the $field_key that is related to the $field_name.
*  This is know as the field value reference
*
*  @type	function
*  @since	3.6
*  @date	29/01/13
*
*  @param	mixed	$field_name: the name of the field - 'sub_heading'
*  @param	int		$post_id: the post_id of which the value is saved against
*
*  @return	string	$return:  a string containing the field_key
*/

function get_field_reference( $field_name, $post_id ) {
	
	// cache
	$found = false;
	$cache = wp_cache_get( 'field_reference/post_id=' .  $post_id . '/name=' .  $field_name, 'acf', false, $found );

	if( $found )
	{
		return $cache;
	}
	
	
	// vars
	$return = '';

	
	// get field key
	if( is_numeric($post_id) )
	{
		$return = get_post_meta($post_id, '_' . $field_name, true); 
	}
	elseif( strpos($post_id, 'user_') !== false )
	{
		$temp_post_id = str_replace('user_', '', $post_id);
		$return = get_user_meta($temp_post_id, '_' . $field_name, true); 
	}
	else
	{
		$return = get_option('_' . $post_id . '_' . $field_name); 
	}
	
	
	// set cache
	wp_cache_set( 'field_reference/post_id=' .  $post_id . '/name=' .  $field_name, $return, 'acf' );
		
	
	// return	
	return $return;
}


/*
*  get_field_objects()
*
*  This function will return an array containing all the custom field objects for a specific post_id.
*  The function is not very elegant and wastes a lot of PHP memory / SQL queries if you are not using all the fields / values.
*
*  @type	function
*  @since	3.6
*  @date	29/01/13
*
*  @param	mixed	$post_id: the post_id of which the value is saved against
*
*  @return	array	$return: an array containin the field groups
*/

function get_field_objects( $post_id = false, $options = array() ) {
	
	// global
	global $wpdb;
	
	
	// filter post_id
	$post_id = apply_filters('acf/get_post_id', $post_id );


	// vars
	$field_key = '';
	$value = array();
	
	
	// get field_names
	if( is_numeric($post_id) )
	{
		$keys = $wpdb->get_col($wpdb->prepare(
			"SELECT meta_value FROM $wpdb->postmeta WHERE post_id = %d and meta_key LIKE %s AND meta_value LIKE %s",
			$post_id,
			'_%',
			'field_%'
		));
	}
	elseif( strpos($post_id, 'user_') !== false )
	{
		$user_id = str_replace('user_', '', $post_id);
		
		$keys = $wpdb->get_col($wpdb->prepare(
			"SELECT meta_value FROM $wpdb->usermeta WHERE user_id = %d and meta_key LIKE %s AND meta_value LIKE %s",
			$user_id,
			'_%',
			'field_%'
		));
	}
	else
	{
		$keys = $wpdb->get_col($wpdb->prepare(
			"SELECT option_value FROM $wpdb->options WHERE option_name LIKE %s",
			'_' . $post_id . '_%' 
		));
	}


	if( is_array($keys) )
	{
		foreach( $keys as $key )
		{
			$field = get_field_object( $key, $post_id, $options );
			
			if( !is_array($field) )
			{
				continue;
			}
			
			$value[ $field['name'] ] = $field;
		}
 	}
 	
 	
	// no value
	if( empty($value) )
	{
		return false;
	}
	
	
	// return
	return $value;
}


/*
*  get_fields()
*
*  This function will return an array containing all the custom field values for a specific post_id.
*  The function is not very elegant and wastes a lot of PHP memory / SQL queries if you are not using all the values.
*
*  @type	function
*  @since	3.6
*  @date	29/01/13
*
*  @param	mixed	$post_id: the post_id of which the value is saved against
*
*  @return	array	$return: an array containin the field values
*/

function get_fields( $post_id = false, $format_value = true ) {
	
	// vars
	$options = array(
		'load_value' => true,
		'format_value' => $format_value
	);
	
	
	$fields = get_field_objects( $post_id, $options );
	
	if( is_array($fields) )
	{
		foreach( $fields as $k => $field )
		{
			$fields[ $k ] = $field['value'];
		}
	}
	
	return $fields;	
}


/*
*  get_field()
*
*  This function will return a custom field value for a specific field name/key + post_id.
*  There is a 3rd parameter to turn on/off formating. This means that an Image field will not use 
*  its 'return option' to format the value but return only what was saved in the database
*
*  @type	function
*  @since	3.6
*  @date	29/01/13
*
*  @param	string		$field_key: string containing the name of teh field name / key ('sub_field' / 'field_1')
*  @param	mixed		$post_id: the post_id of which the value is saved against
*  @param	boolean		$format_value: whether or not to format the value as described above
*
*  @return	mixed		$value: the value found
*/
 
function get_field( $field_key, $post_id = false, $format_value = true ) {
	
	// vars
	$return = false;
	$options = array(
		'load_value' => true,
		'format_value' => $format_value
	);

	
	$field = get_field_object( $field_key, $post_id, $options);
	
	
	if( is_array($field) )
	{
		$return = $field['value'];
	}
	
	
	return $return;
	 
}


/*
*  get_field_object()
*
*  This function will return an array containing all the field data for a given field_name
*
*  @type	function
*  @since	3.6
*  @date	3/02/13
*
*  @param	string		$field_key: string containing the name of teh field name / key ('sub_field' / 'field_1')
*  @param	mixed		$post_id: the post_id of which the value is saved against
*  @param	array		$options: an array containing options
*			boolean		+ load_value: load the field value or not. Defaults to true
*			boolean		+ format_value: format the field value or not. Defaults to true
*
*  @return	array		$return: an array containin the field groups
*/

function get_field_object( $field_key, $post_id = false, $options = array() ) {
	
	// make sure add-ons are included
	acf()->include_3rd_party();
		
		
	// filter post_id
	$post_id = apply_filters('acf/get_post_id', $post_id );
	$field = false;
	$orig_field_key = $field_key;
	
	
	// defaults for options
	$defaults = array(
		'load_value'	=>	true,
		'format_value'	=>	true,
	);
	
	$options = array_merge($defaults, $options);
	
	
	// is $field_name a name? pre 3.4.0
	if( substr($field_key, 0, 6) !== 'field_' )
	{
		// get field key
		$field_key = get_field_reference( $field_key, $post_id );
	}
	
	
	// get field
	if( substr($field_key, 0, 6) === 'field_' )
	{
		$field = apply_filters('acf/load_field', false, $field_key );
	}
	
	
	// validate field
	if( !$field )
	{
		// treat as text field
		$field = array(
			'type' => 'text',
			'name' => $orig_field_key,
			'key' => 'field_' . $orig_field_key,
		);
		$field = apply_filters('acf/load_field', $field, $field['key'] );
	}


	// load value
	if( $options['load_value'] )
	{
		$field['value'] = apply_filters('acf/load_value', false, $post_id, $field);
		
		
		// format value
		if( $options['format_value'] )
		{
			$field['value'] = apply_filters('acf/format_value_for_api', $field['value'], $post_id, $field);
		}
	}


	return $field;

}


/*
*  the_field()
*
*  This function is the same as echo get_field().
*
*  @type	function
*  @since	1.0.3
*  @date	29/01/13
*
*  @param	string		$field_name: the name of the field - 'sub_heading'
*  @param	mixed		$post_id: the post_id of which the value is saved against
*
*  @return	string		$value
*/

function the_field( $field_name, $post_id = false ) {
	
	$value = get_field($field_name, $post_id);
	
	if( is_array($value) )
	{
		$value = @implode(', ',$value);
	}
	
	echo $value;
}


/*
*  have_rows
*
*  This function will instantiate a global variable containing the rows of a repeater or flexible content field,
*  afterwhich, it will determin if another row exists to loop through
*
*  @type	function
*  @date	2/09/13
*  @since	4.3.0
*
*  @param	$field_name (string) the name of the field - 'images'
*  @return	$post_id (mixed) the post_id of which the value is saved against
*/

function have_rows( $field_name, $post_id = false ) {
	
	// vars
	$depth = 0;
	$row = array();
	$new_parent_loop = false;
	$new_child_loop = false;
	
	
	// reference
	$_post_id = $post_id;
	
	
	// filter post_id
	$post_id = apply_filters('acf/get_post_id', $post_id );
	
	
	// empty?
	if( empty($GLOBALS['acf_field']) )
	{
		// reset
		reset_rows( true );
		
		
		// create a new loop
		$new_parent_loop = true;
	}
	else
	{
		// vars
		$row = end( $GLOBALS['acf_field'] );
		$prev = prev( $GLOBALS['acf_field'] );
		
		
		// If post_id has changed, this is most likely an archive loop
		if( $post_id != $row['post_id'] )
		{
			if( $prev && $prev['post_id'] == $post_id )
			{
				// case: Change in $post_id was due to a nested loop ending
				// action: move up one level through the loops
				reset_rows();
			}
			elseif( empty($_post_id) && isset($row['value'][ $row['i'] ][ $field_name ]) )
			{
				// case: Change in $post_id was due to this being a nested loop and not specifying the $post_id
				// action: move down one level into a new loop
				$new_child_loop = true;
			}
			else
			{
				// case: Chang in $post_id is the most obvious, used in an WP_Query loop with multiple $post objects
				// action: leave this current loop alone and create a new parent loop
				$new_parent_loop = true;
			}
		}
		elseif( $field_name != $row['name'] )
		{
			if( $prev && $prev['name'] == $field_name && $prev['post_id'] == $post_id )
			{
				// case: Change in $field_name was due to a nested loop ending
				// action: move up one level through the loops
				reset_rows();
			}
			elseif( isset($row['value'][ $row['i'] ][ $field_name ]) )
			{
				// case: Change in $field_name was due to this being a nested loop
				// action: move down one level into a new loop
				$new_child_loop = true;
				
			}
			else
			{
				// case: Chang in $field_name is the most obvious, this is a new loop for a different field within the $post
				// action: leave this current loop alone and create a new parent loop
				$new_parent_loop = true;
			}
			
			
		}
	}
	
	
	if( $new_parent_loop )
	{
		// vars
		$f = get_field_object( $field_name, $post_id );
		$v = $f['value'];
		unset( $f['value'] );
		
		
		// add row
		$GLOBALS['acf_field'][] = array(
			'name'		=> $field_name,
			'value'		=> $v,
			'field'		=> $f,
			'i'			=> -1,
			'post_id'	=> $post_id,
		);
		
	}
	elseif( $new_child_loop )
	{
		// vars
		$f = acf_get_child_field_from_parent_field( $field_name, $row['field'] );
		$v = $row['value'][ $row['i'] ][ $field_name ];
		
		$GLOBALS['acf_field'][] = array(
			'name'		=> $field_name,
			'value'		=> $v,
			'field'		=> $f,
			'i'			=> -1,
			'post_id'	=> $post_id,
		);

	}	
	
	
	// update vars
	$row = end( $GLOBALS['acf_field'] );
	
	
	if( is_array($row['value']) && array_key_exists( $row['i']+1, $row['value'] ) )
	{
		// next row exists
		return true;
	}
	
	
	// no next row!
	reset_rows();
	
	
	// return
	return false;
  
}


/*
*  the_row
*
*  This function will progress the global repeater or flexible content value 1 row
*
*  @type	function
*  @date	2/09/13
*  @since	4.3.0
*
*  @param	N/A
*  @return	N/A
*/

function the_row() {
	
	// vars
	$depth = count( $GLOBALS['acf_field'] ) - 1;

	
	
	// increase row
	$GLOBALS['acf_field'][ $depth ]['i']++;
	
	
	// get row
	$value = $GLOBALS['acf_field'][ $depth ]['value'];
	$i = $GLOBALS['acf_field'][ $depth ]['i'];

	
	// return
	return $value[ $i ];
}


/*
*  reset_rows
*
*  This function will find the current loop and unset it from the global array.
*  To bo used when loop finishes or a break is used
*
*  @type	function
*  @date	26/10/13
*  @since	5.0.0
*
*  @param	$post_id (int)
*  @return	$post_id (int)
*/

function reset_rows( $hard_reset = false ) {
	
	// completely destroy?
	if( $hard_reset )
	{
		$GLOBALS['acf_field'] = array();
	}
	else
	{
		// vars
		$depth = count( $GLOBALS['acf_field'] ) - 1;
		
		
		// remove
		unset( $GLOBALS['acf_field'][$depth] );
		
		
		// refresh index
		$GLOBALS['acf_field'] = array_values($GLOBALS['acf_field']);
	}
	
	
	// return
	return true;
	
	
}


/*
*  has_sub_field()
*
*  This function is used inside a while loop to return either true or false (loop again or stop).
*  When using a repeater or flexible content field, it will loop through the rows until 
*  there are none left or a break is detected
*
*  @type	function
*  @since	1.0.3
*  @date	29/01/13
*
*  @param	string	$field_name: the name of the field - 'sub_heading'
*  @param	mixed	$post_id: the post_id of which the value is saved against
*
*  @return	bool
*/

function has_sub_field( $field_name, $post_id = false ) {
	
	// vars
	$r = have_rows( $field_name, $post_id );
	
	
	// if has rows, progress through 1 row for the while loop to work
	if( $r )
	{
		the_row();
	}
	
	
	// return
	return $r;
}


/*
*  has_sub_fields()
*
*  This function is a replica of 'has_sub_field'
*
*  @type	function
*  @since	4.0.0
*  @date	29/01/13
*
*  @param	string	$field_name: the name of the field - 'sub_heading'
*  @param	mixed	$post_id: the post_id of which the value is saved against
*
*  @return	bool
*/

function has_sub_fields( $field_name, $post_id = false )
{
	return has_sub_field( $field_name, $post_id );
}


/*
*  get_sub_field()
*
*  This function is used inside a 'has_sub_field' while loop to return a sub field value
*
*  @type	function
*  @since	1.0.3
*  @date	29/01/13
*
*  @param	string	$field_name: the name of the field - 'sub_heading'
*
*  @return	mixed	$value
*/

function get_sub_field( $field_name ) {
	
	// no field?
	if( empty($GLOBALS['acf_field']) )
	{
		return false;
	}
	
	
	// vars
	$row = end( $GLOBALS['acf_field'] );
	
	
	// return value
	if( isset($row['value'][ $row['i'] ][ $field_name ]) )
	{
		return $row['value'][ $row['i'] ][ $field_name ];
	}
	
	
	// return false
	return false;
}


/*
*  get_sub_field()
*
*  This function is the same as echo get_sub_field
*
*  @type	function
*  @since	1.0.3
*  @date	29/01/13
*
*  @param	string	$field_name: the name of the field - 'sub_heading'
*
*  @return	string	$value
*/

function the_sub_field($field_name)
{
	$value = get_sub_field($field_name);
	
	if(is_array($value))
	{
		$value = implode(', ',$value);
	}
	
	echo $value;
}


/*
*  get_sub_field_object()
*
*  This function is used inside a 'has_sub_field' while loop to return a sub field object
*
*  @type	function
*  @since	3.5.8.1
*  @date	29/01/13
*
*  @param	string	$field_name: the name of the field - 'sub_heading'
*
*  @return	array	$sub_field	
*/

function get_sub_field_object( $child_name )
{
	// no field?
	if( empty($GLOBALS['acf_field']) )
	{
		return false;
	}


	// vars
	$depth = count( $GLOBALS['acf_field'] ) - 1;
	$parent = $GLOBALS['acf_field'][$depth]['field'];


	// return
	return acf_get_child_field_from_parent_field( $child_name, $parent );
	
}


/*
*  acf_get_sub_field_from_parent_field()
*
*  This function is used by the get_sub_field_object to find a sub field within a parent field
*
*  @type	function
*  @since	3.5.8.1
*  @date	29/01/13
*
*  @param	string	$child_name: the name of the field - 'sub_heading'
*  @param	array	$parent: the parent field object
*
*  @return	array	$sub_field	
*/

function acf_get_child_field_from_parent_field( $child_name, $parent )
{
	// vars
	$return = false;
	
	
	// find child
	if( isset($parent['sub_fields']) && is_array($parent['sub_fields']) )
	{
		foreach( $parent['sub_fields'] as $child )
		{
			if( $child['name'] == $child_name || $child['key'] == $child_name )
			{
				$return = $child;
				break;
			}
			
			// perhaps child has grand children?
			$grand_child = acf_get_child_field_from_parent_field( $child_name, $child );
			if( $grand_child )
			{
				$return = $grand_child;
				break;
			}
		}
	}
	elseif( isset($parent['layouts']) && is_array($parent['layouts']) )
	{
		foreach( $parent['layouts'] as $layout )
		{
			$child = acf_get_child_field_from_parent_field( $child_name, $layout );
			if( $child )
			{
				$return = $child;
				break;
			}
		}
	}
	

	// return
	return $return;
	
}


/*
*  register_field_group()
*
*  This function is used to register a field group via code. It acceps 1 array containing
*  all the field group data. This data can be obtained by using teh export tool within ACF
*
*  @type	function
*  @since	3.0.6
*  @date	29/01/13
*
*  @param	array	$array: an array holding all the field group data
*
*  @return
*/

$GLOBALS['acf_register_field_group'] = array();

function register_field_group( $array )
{
	// add id
	if( !isset($array['id']) )
	{
		$array['id'] = uniqid();
	}
	

	// 3.2.5 - changed show_on_page option
	if( !isset($array['options']['hide_on_screen']) && isset($array['options']['show_on_page']) )
	{
		$show_all = array('the_content', 'discussion', 'custom_fields', 'comments', 'slug', 'author');
		$array['options']['hide_on_screen'] = array_diff($show_all, $array['options']['show_on_page']);
		unset( $array['options']['show_on_page'] );
	}

	
	// 4.0.4 - changed location rules architecture
	if( isset($array['location']['rules']) )
	{
		// vars
		$groups = array();
		$group_no = 0;
		
		
		if( is_array($array['location']['rules']) )
	 	{
		 	foreach( $array['location']['rules'] as $rule )
		 	{
			 	$rule['group_no'] = $group_no;
			 	
			 	// sperate groups?
			 	if( $array['location']['allorany'] == 'any' )
			 	{
				 	$group_no++;
			 	}
			 	
			 	
			 	// add to group
			 	$groups[ $rule['group_no'] ][ $rule['order_no'] ] = $rule;
			 	
			 	
			 	// sort rules
			 	ksort( $groups[ $rule['group_no'] ] );
	 	
		 	}
		 	
		 	// sort groups
			ksort( $groups );
	 	}
	 	
	 	$array['location'] = $groups;
	}
	
	
	$GLOBALS['acf_register_field_group'][] = $array;
}


add_filter('acf/get_field_groups', 'api_acf_get_field_groups', 2, 1);
function api_acf_get_field_groups( $return )
{
	// validate
	if( empty($GLOBALS['acf_register_field_group']) )
	{
		return $return;
	}
	
	
	foreach( $GLOBALS['acf_register_field_group'] as $acf )
	{
		$return[] = array(
			'id' => $acf['id'],
			'title' => $acf['title'],
			'menu_order' => $acf['menu_order'],
		);
	}

	
	// order field groups based on menu_order, title
	// Obtain a list of columns
	foreach( $return as $key => $row )
	{
	    $menu_order[ $key ] = $row['menu_order'];
	    $title[ $key ] = $row['title'];
	}
	
	// Sort the array with menu_order ascending
	// Add $array as the last parameter, to sort by the common key
	if(isset($menu_order))
	{
		array_multisort($menu_order, SORT_ASC, $title, SORT_ASC, $return);
	}
	
	return $return;
}


add_filter('acf/field_group/get_fields', 'api_acf_field_group_get_fields', 1, 2);
function api_acf_field_group_get_fields( $fields, $post_id )
{
	// validate
	if( !empty($GLOBALS['acf_register_field_group']) )
	{
		foreach( $GLOBALS['acf_register_field_group'] as $acf )
		{
			if( $acf['id'] == $post_id )
			{
				foreach( $acf['fields'] as $f )
				{
					$fields[] = apply_filters('acf/load_field', $f, $f['key']);
				}
				
				break;
			}
		}
	}

	return $fields;

}


add_filter('acf/load_field', 'api_acf_load_field', 1, 2);
function api_acf_load_field( $field, $field_key )
{
	// validate
	if( !empty($GLOBALS['acf_register_field_group']) )
	{
		foreach( $GLOBALS['acf_register_field_group'] as $acf )
		{
			if( !empty($acf['fields']) )
			{
				foreach( $acf['fields'] as $f )
				{
					if( $f['key'] == $field_key )
					{
						$field = $f;
						break;
					}
				}
			}
		}
	}

	return $field;
}


add_filter('acf/field_group/get_location', 'api_acf_field_group_get_location', 1, 2);
function api_acf_field_group_get_location( $location, $post_id )
{
	// validate
	if( !empty($GLOBALS['acf_register_field_group']) )
	{
		foreach( $GLOBALS['acf_register_field_group'] as $acf )
		{
			if( $acf['id'] == $post_id )
			{
				$location = $acf['location'];
				break;
			}
		}
	}

	return $location;
}



add_filter('acf/field_group/get_options', 'api_acf_field_group_get_options', 1, 2);
function api_acf_field_group_get_options( $options, $post_id )
{
	// validate
	if( !empty($GLOBALS['acf_register_field_group']) )
	{
		foreach( $GLOBALS['acf_register_field_group'] as $acf )
		{
			if( $acf['id'] == $post_id )
			{
				$options = $acf['options'];
				break;
			}
		}
	}

	return $options;
}


/*
*  get_row_layout()
*
*  This function will return a string representation of the current row layout within a 'has_sub_field' loop
*
*  @type	function
*  @since	3.0.6
*  @date	29/01/13
*
*  @return	$value - string containing the layout
*/

function get_row_layout()
{
	// vars
	$value = get_sub_field('acf_fc_layout');
	
	
	return $value;
}


/*
*  acf_shortcode()
*
*  This function is used to add basic shortcode support for the ACF plugin
*
*  @type	function
*  @since	1.1.1
*  @date	29/01/13
*
*  @param	array	$atts: an array holding the shortcode options
*			string	+ field: the field name
*			mixed	+ post_id: the post_id to load from
*
*  @return	string	$value: the value found by get_field
*/

function acf_shortcode( $atts )
{
	// extract attributs
	extract( shortcode_atts( array(
		'field' => "",
		'post_id' => false,
	), $atts ) );
	
	
	// $field is requird
	if( !$field || $field == "" )
	{
		return "";
	}
	
	
	// get value and return it
	$value = get_field( $field, $post_id );
	
	
	if( is_array($value) )
	{
		$value = @implode( ', ',$value );
	}
	
	return $value;
}
add_shortcode( 'acf', 'acf_shortcode' );


/*
*  acf_form_head()
*
*  This function is placed at the very top of a template (before any HTML is rendered) and saves the $_POST data sent by acf_form.
*
*  @type	function
*  @since	1.1.4
*  @date	29/01/13
*
*  @param	N/A
*
*  @return	N/A
*/

function acf_form_head()
{
	// global vars
	global $post_id;
	
	
	// verify nonce
	if( isset($_POST['acf_nonce']) && wp_verify_nonce($_POST['acf_nonce'], 'input') )
	{
		// $post_id to save against
		$post_id = $_POST['post_id'];
		
		
		// allow for custom save
		$post_id = apply_filters('acf/pre_save_post', $post_id);
		
		
		// save the data
		do_action('acf/save_post', $post_id);	


		// redirect
		if(isset($_POST['return']))
		{
			wp_redirect($_POST['return']);
			exit;
		}
	}
	
	
	// need wp styling
	wp_enqueue_style(array(
		'colors-fresh'
	));
	
		
	// actions
	do_action('acf/input/admin_enqueue_scripts');

	add_action('wp_head', 'acf_form_wp_head');
	
}

function acf_form_wp_head()
{
	do_action('acf/input/admin_head');
}


/*
*  acf_form()
*
*  This function is used to create an ACF form.
*
*  @type	function
*  @since	1.1.4
*  @date	29/01/13
*
*  @param	array		$options: an array containing many options to customize the form
*			string		+ post_id: post id to get field groups from and save data to. Default is false
*			array		+ field_groups: an array containing field group ID's. If this option is set, 
*						  the post_id will not be used to dynamically find the field groups
*			boolean		+ form: display the form tag or not. Defaults to true
*			array		+ form_attributes: an array containg attributes which will be added into the form tag
*			string		+ return: the return URL
*			string		+ html_before_fields: html inside form before fields
*			string		+ html_after_fields: html inside form after fields
*			string		+ submit_value: value of submit button
*			string		+ updated_message: default updated message. Can be false					 
*
*  @return	N/A
*/

function acf_form( $options = array() )
{
	global $post;
	
	
	// defaults
	$defaults = array(
		'post_id' => false,
		'field_groups' => array(),
		'form' => true,
		'form_attributes' => array(
			'id' => 'post',
			'class' => '',
			'action' => '',
			'method' => 'post',
		),
		'return' => add_query_arg( 'updated', 'true', get_permalink() ),
		'html_before_fields' => '',
		'html_after_fields' => '',
		'submit_value' => __("Update", 'acf'),
		'updated_message' => __("Post updated", 'acf'), 
	);
	
	
	// merge defaults with options
	$options = array_merge($defaults, $options);
	
	
	// merge sub arrays
	foreach( $options as $k => $v )
	{
		if( is_array($v) )
		{
			$options[ $k ] = array_merge($defaults[ $k ], $options[ $k ]);
		}
	}
	
	
	// filter post_id
	$options['post_id'] = apply_filters('acf/get_post_id', $options['post_id'] );
	
	
	// attributes
	$options['form_attributes']['class'] .= 'acf-form';
	
	
	
	// register post box
	if( empty($options['field_groups']) )
	{
		// get field groups
		$filter = array(
			'post_id' => $options['post_id']
		);
		
		
		if( strpos($options['post_id'], 'user_') !== false )
		{
			$user_id = str_replace('user_', '', $options['post_id']);
			$filter = array(
				'ef_user' => $user_id
			);
		}
		elseif( strpos($options['post_id'], 'taxonomy_') !== false )
		{
			$taxonomy_id = str_replace('taxonomy_', '', $options['post_id']);
			$filter = array(
				'ef_taxonomy' => $taxonomy_id
			);
		}
		
		
		$options['field_groups'] = array();
		$options['field_groups'] = apply_filters( 'acf/location/match_field_groups', $options['field_groups'], $filter );
	}


	// updated message
	if(isset($_GET['updated']) && $_GET['updated'] == 'true' && $options['updated_message'])
	{
		echo '<div id="message" class="updated"><p>' . $options['updated_message'] . '</p></div>';
	}
	
	
	// display form
	if( $options['form'] ): ?>
	<form <?php if($options['form_attributes']){foreach($options['form_attributes'] as $k => $v){echo $k . '="' . $v .'" '; }} ?>>
	<?php endif; ?>
	
	<div style="display:none">
		<script type="text/javascript">
			acf.o.post_id = <?php echo is_numeric($options['post_id']) ? $options['post_id'] : '"' . $options['post_id'] . '"'; ?>;
		</script>
		<input type="hidden" name="acf_nonce" value="<?php echo wp_create_nonce( 'input' ); ?>" />
		<input type="hidden" name="post_id" value="<?php echo $options['post_id']; ?>" />
		<input type="hidden" name="return" value="<?php echo $options['return']; ?>" />
		<?php wp_editor('', 'acf_settings'); ?>
	</div>
	
	<div id="poststuff">
	<?php
	
	// html before fields
	echo $options['html_before_fields'];
	
	
	$acfs = apply_filters('acf/get_field_groups', array());
	
	if( is_array($acfs) ){ foreach( $acfs as $acf ){
		
		// only add the chosen field groups
		if( !in_array( $acf['id'], $options['field_groups'] ) )
		{
			continue;
		}
		
		
		// load options
		$acf['options'] = apply_filters('acf/field_group/get_options', array(), $acf['id']);
		
		
		// load fields
		$fields = apply_filters('acf/field_group/get_fields', array(), $acf['id']);
		
		
		echo '<div id="acf_' . $acf['id'] . '" class="postbox acf_postbox ' . $acf['options']['layout'] . '">';
		echo '<h3 class="hndle"><span>' . $acf['title'] . '</span></h3>';
		echo '<div class="inside">';
							
		do_action('acf/create_fields', $fields, $options['post_id']);
		
		echo '</div></div>';
		
	}}
	
	
	// html after fields
	echo $options['html_after_fields'];
	
	?>
	
	<?php if( $options['form'] ): ?>
	<!-- Submit -->
	<div class="field">
		<input type="submit" value="<?php echo $options['submit_value']; ?>" />
	</div>
	<!-- / Submit -->
	<?php endif; ?>
	
	</div><!-- <div id="poststuff"> -->
	
	<?php if( $options['form'] ): ?>
	</form>
	<?php endif;
}


/*
*  update_field()
*
*  This function will update a value in the database
*
*  @type	function
*  @since	3.1.9
*  @date	29/01/13
*
*  @param	mixed	$field_name: the name of the field - 'sub_heading'
*  @param	mixed	$value: the value to save in the database. The variable type is dependant on the field type
*  @param	mixed	$post_id: the post_id of which the value is saved against
*
*  @return	N/A
*/

function update_field( $field_key, $value, $post_id = false )
{
	// filter post_id
	$post_id = apply_filters('acf/get_post_id', $post_id );
	
	
	// vars
	$options = array(
		'load_value' => false,
		'format_value' => false
	);
	
	$field = get_field_object( $field_key, $post_id, $options);

	
	// sub fields? They need formatted data
	if( $field['type'] == 'repeater' )
	{
		$value = acf_convert_field_names_to_keys( $value, $field );
	}
	elseif( $field['type'] == 'flexible_content' )
	{
		if( $field['layouts'] )
		{
			foreach( $field['layouts'] as $layout )
			{
				$value = acf_convert_field_names_to_keys( $value, $layout );
			}
		}
	}
	
	
	// save
	do_action('acf/update_value', $value, $post_id, $field );
	
	
	return true;
	
}


/*
*  delete_field()
*
*  This function will remove a value from the database
*
*  @type	function
*  @since	3.1.9
*  @date	29/01/13
*
*  @param	mixed	$field_name: the name of the field - 'sub_heading'
*  @param	mixed	$post_id: the post_id of which the value is saved against
*
*  @return	N/A
*/

function delete_field( $field_name, $post_id )
{
	do_action('acf/delete_value', $post_id, $field_name );
}


/*
*  create_field()
*
*  This function will creat the HTML for a field
*
*  @type	function
*  @since	4.0.0
*  @date	17/03/13
*
*  @param	array	$field - an array containing all the field attributes
*
*  @return	N/A
*/

function create_field( $field )
{
	do_action('acf/create_field', $field );
}


/*
*  acf_convert_field_names_to_keys()
*
*  Helper for the update_field function
*
*  @type	function
*  @since	4.0.0
*  @date	17/03/13
*
*  @param	array	$value: the value returned via get_field
*  @param	array	$field: the field or layout to find sub fields from
*
*  @return	N/A
*/

function acf_convert_field_names_to_keys( $value, $field )
{
	// only if $field has sub fields
	if( !isset($field['sub_fields']) )
	{
		return $value;
	}
	

	// define sub field keys
	$sub_fields = array();
	if( $field['sub_fields'] )
	{
		foreach( $field['sub_fields'] as $sub_field )
		{
			$sub_fields[ $sub_field['name'] ] = $sub_field;
		}
	}
	
	
	// loop through the values and format the array to use sub field keys
	if( is_array($value) )
	{
		foreach( $value as $row_i => $row)
		{
			if( $row )
			{
				foreach( $row as $sub_field_name => $sub_field_value )
				{
					// sub field must exist!
					if( !isset($sub_fields[ $sub_field_name ]) )
					{
						continue;
					}
					
					
					// vars
					$sub_field = $sub_fields[ $sub_field_name ];
					$sub_field_value = acf_convert_field_names_to_keys( $sub_field_value, $sub_field );
					
					
					// set new value
					$value[$row_i][ $sub_field['key'] ] = $sub_field_value;
					
					
					// unset old value
					unset( $value[$row_i][$sub_field_name] );
						
					
				}
				// foreach( $row as $sub_field_name => $sub_field_value )
			}
			// if( $row )
		}
		// foreach( $value as $row_i => $row)
	}
	// if( $value )
	
	
	return $value;

}


/*
*  acf_force_type_array
*
*  This function will force a variable to become an array
*
*  @type	function
*  @date	4/02/2014
*  @since	5.0.0
*
*  @param	$var (mixed)
*  @return	(array)
*/

function acf_force_type_array( $var ) {
	
	// is array?
	if( is_array($var) ) {
	
		return $var;
	
	}
	
	
	// bail early if empty
	if( empty($var) && !is_numeric($var) ) {
		
		return array();
		
	}
	
	
	// string 
	if( is_string($var) ) {
		
		return explode(',', $var);
		
	}
	
	
	// place in array
	return array( $var );
} 


/*
*  acf_get_valid_terms
*
*  This function will replace old terms with new split term ids
*
*  @type	function
*  @date	27/02/2015
*  @since	5.1.5
*
*  @param	$terms (int|array)
*  @param	$taxonomy (string)
*  @return	$terms
*/

function acf_get_valid_terms( $terms = false, $taxonomy = 'category' ) {
	
	// bail early if function does not yet exist or
	if( !function_exists('wp_get_split_term') || empty($terms) ) {
		
		return $terms;
		
	}
	
	
	// vars
	$is_array = is_array($terms);
	
	
	// force into array
	$terms = acf_force_type_array( $terms );
	
	
	// force ints
	$terms = array_map('intval', $terms);
	
	
	// attempt to find new terms
	foreach( $terms as $i => $term_id ) {
		
		$new_term_id = wp_get_split_term($term_id, $taxonomy);
		
		if( $new_term_id ) {
			
			$terms[ $i ] = $new_term_id;
			
		}
		
	}
	
	
	// revert array if needed
	if( !$is_array ) {
		
		$terms = $terms[0];
		
	}
	
	
	// return
	return $terms;
	
}


/*
*  Depreceated Functions
*
*  @description: 
*  @created: 23/07/12
*/


/*--------------------------------------------------------------------------------------
*
*	reset_the_repeater_field
*
*	@author Elliot Condon
*	@depreciated: 3.3.4 - now use has_sub_field
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function reset_the_repeater_field()
{
	// do nothing
}


/*--------------------------------------------------------------------------------------
*
*	the_repeater_field
*
*	@author Elliot Condon
*	@depreciated: 3.3.4 - now use has_sub_field
*	@since 1.0.3
* 
*-------------------------------------------------------------------------------------*/

function the_repeater_field($field_name, $post_id = false)
{
	return has_sub_field($field_name, $post_id);
}


/*--------------------------------------------------------------------------------------
*
*	the_flexible_field
*
*	@author Elliot Condon
*	@depreciated: 3.3.4 - now use has_sub_field
*	@since 3.?.?
* 
*-------------------------------------------------------------------------------------*/

function the_flexible_field($field_name, $post_id = false)
{
	return has_sub_field($field_name, $post_id);
}

/*
*  acf_filter_post_id()
*
*  This is a deprecated function which is now run through a filter
*
*  @type	function
*  @since	3.6
*  @date	29/01/13
*
*  @param	mixed	$post_id
*
*  @return	mixed	$post_id
*/

function acf_filter_post_id( $post_id )
{
	return apply_filters('acf/get_post_id', $post_id );
}

?>