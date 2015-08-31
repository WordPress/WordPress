<?php

class acf_field_post_object extends acf_field
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
		$this->name = 'post_object';
		$this->label = __("Post Object",'acf');
		$this->category = __("Relational",'acf');
		$this->defaults = array(
			'post_type' => array('all'),
			'taxonomy' => array('all'),
			'multiple' => 0,
			'allow_null' => 0,
		);
		
		
		// do not delete!
    	parent::__construct();
  
	}
	
	
	/*
	*  load_field()
	*  
	*  This filter is appied to the $field after it is loaded from the database
	*  
	*  @type filter
	*  @since 3.6
	*  @date 23/01/13
	*  
	*  @param $field - the field array holding all the field options
	*  
	*  @return $field - the field array holding all the field options
	*/
	
	function load_field( $field )
	{
		// validate post_type
		if( !$field['post_type'] || !is_array($field['post_type']) || in_array('', $field['post_type']) )
		{
			$field['post_type'] = array( 'all' );
		}

		
		// validate taxonomy
		if( !$field['taxonomy'] || !is_array($field['taxonomy']) || in_array('', $field['taxonomy']) )
		{
			$field['taxonomy'] = array( 'all' );
		}
		
		
		// return
		return $field;
	}
	
	
	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_field( $field )
	{
		// global
		global $post;
		
		
		// vars
		$args = array(
			'numberposts' => -1,
			'post_type' => null,
			'orderby' => 'title',
			'order' => 'ASC',
			'post_status' => array('publish', 'private', 'draft', 'inherit', 'future'),
			'suppress_filters' => false,
		);
		
		
		// load all post types by default
		if( in_array('all', $field['post_type']) )
		{
			$field['post_type'] = apply_filters('acf/get_post_types', array());
		}
		
		
		// create tax queries
		if( ! in_array('all', $field['taxonomy']) )
		{
			// vars
			$taxonomies = array();
			$args['tax_query'] = array();
			
			foreach( $field['taxonomy'] as $v )
			{
				
				// find term (find taxonomy!)
				// $term = array( 0 => $taxonomy, 1 => $term_id )
				$term = explode(':', $v); 
				
				
				// validate
				if( !is_array($term) || !isset($term[1]) )
				{
					continue;
				}
				
				
				// add to tax array
				$taxonomies[ $term[0] ][] = $term[1];
				
			}
			
			
			// now create the tax queries
			foreach( $taxonomies as $k => $v )
			{
				$args['tax_query'][] = array(
					'taxonomy' => $k,
					'field' => 'id',
					'terms' => $v,
				);
			}
		}
		
		
		// Change Field into a select
		$field['type'] = 'select';
		$field['choices'] = array();
		
		
		foreach( $field['post_type'] as $post_type )
		{
			// set post_type
			$args['post_type'] = $post_type;
			
			
			// set order
			$get_pages = false;
			if( is_post_type_hierarchical($post_type) && !isset($args['tax_query']) )
			{
				$args['sort_column'] = 'menu_order, post_title';
				$args['sort_order'] = 'ASC';
				
				$get_pages = true;
			}
			
			
			// filters
			$args = apply_filters('acf/fields/post_object/query', $args, $field, $post);
			$args = apply_filters('acf/fields/post_object/query/name=' . $field['_name'], $args, $field, $post );
			$args = apply_filters('acf/fields/post_object/query/key=' . $field['key'], $args, $field, $post );
			
			
			if( $get_pages )
			{
				$posts = get_pages( $args );
			}
			else
			{
				$posts = get_posts( $args );
			}
			
			
			if($posts) {
				
				foreach( $posts as $p ) {
					
					// title
					$title = get_the_title( $p->ID );
					
					
					// empty
					if( $title === '' ) {
						
						$title = __('(no title)', 'acf');
						
					}
					
					
					// ancestors
					if( $p->post_type != 'attachment' ) {
						
						$ancestors = get_ancestors( $p->ID, $p->post_type );
						
						$title = str_repeat('- ', count($ancestors)) . $title;
						
					}
					
					
					// status
					if( get_post_status( $p->ID ) != "publish" ) {
						
						$title .= ' (' . get_post_status( $p->ID ) . ')';
						
					}
					
					
					// WPML
					if( defined('ICL_LANGUAGE_CODE') ) {
						
						$title .= ' (' . ICL_LANGUAGE_CODE . ')';
						
					}
					
					
					// filters
					$title = apply_filters('acf/fields/post_object/result', $title, $p, $field, $post);
					$title = apply_filters('acf/fields/post_object/result/name=' . $field['_name'] , $title, $p, $field, $post);
					$title = apply_filters('acf/fields/post_object/result/key=' . $field['key'], $title, $p, $field, $post);
					
					
					// add to choices
					if( count($field['post_type']) == 1 )
					{
						$field['choices'][ $p->ID ] = $title;
					}
					else
					{
						// group by post type
						$post_type_object = get_post_type_object( $p->post_type );
						$post_type_name = $post_type_object->labels->name;
					
						$field['choices'][ $post_type_name ][ $p->ID ] = $title;
					}
					
					
				}
				// foreach( $posts as $post )
			}
			// if($posts)
		}
		// foreach( $field['post_type'] as $post_type )
		
		
		// create field
		do_action('acf/create_field', $field );
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
		<label for=""><?php _e("Post Type",'acf'); ?></label>
	</td>
	<td>
		<?php 
		
		$choices = array(
			'all'	=>	__("All",'acf')
		);
		$choices = apply_filters('acf/get_post_types', $choices);
		
		
		do_action('acf/create_field', array(
			'type'	=>	'select',
			'name'	=>	'fields['.$key.'][post_type]',
			'value'	=>	$field['post_type'],
			'choices'	=>	$choices,
			'multiple'	=>	1,
		));
		
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Filter from Taxonomy",'acf'); ?></label>
	</td>
	<td>
		<?php 
		$choices = array(
			'' => array(
				'all' => __("All",'acf')
			)
		);
		$simple_value = false;
		$choices = apply_filters('acf/get_taxonomies_for_select', $choices, $simple_value);
		
		do_action('acf/create_field', array(
			'type'	=>	'select',
			'name'	=>	'fields['.$key.'][taxonomy]',
			'value'	=>	$field['taxonomy'],
			'choices' => $choices,
			'multiple'	=>	1,
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
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Select multiple values?",'acf'); ?></label>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'	=>	'radio',
			'name'	=>	'fields['.$key.'][multiple]',
			'value'	=>	$field['multiple'],
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
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is passed to the create_field action
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value	- the value which was loaded from the database
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value( $value, $post_id, $field )
	{
		// empty?
		if( !empty($value) )
		{
			// convert to integers
			if( is_array($value) )
			{
				$value = array_map('intval', $value);
			}
			else
			{
				$value = intval($value);
			}
		}
			
		
		// return value
		return $value;	
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
	*  @param	$post_id - the $post_id from which the value was loaded
	*  @param	$field	- the field array holding all the field options
	*
	*  @return	$value	- the modified value
	*/
	
	function format_value_for_api( $value, $post_id, $field )
	{
		// no value?
		if( !$value )
		{
			return false;
		}
		
		
		// null?
		if( $value == 'null' )
		{
			return false;
		}
		
		
		// multiple / single
		if( is_array($value) )
		{
			// find posts (DISTINCT POSTS)
			$posts = get_posts(array(
				'numberposts' => -1,
				'post__in' => $value,
				'post_type'	=>	apply_filters('acf/get_post_types', array()),
				'post_status' => array('publish', 'private', 'draft', 'inherit', 'future'),
			));
	
			
			$ordered_posts = array();
			foreach( $posts as $post )
			{
				// create array to hold value data
				$ordered_posts[ $post->ID ] = $post;
			}
			
			
			// override value array with attachments
			foreach( $value as $k => $v)
			{
				// check that post exists (my have been trashed)
				if( !isset($ordered_posts[ $v ]) )
				{
					unset( $value[ $k ] );
				}
				else
				{
					$value[ $k ] = $ordered_posts[ $v ];
				}
			}
			
		}
		else
		{
			$value = get_post($value);
		}
		
		
		// return the value
		return $value;
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
		// validate
		if( empty($value) )
		{
			return $value;
		}
		
		
		if( is_object($value) && isset($value->ID) )
		{
			// object
			$value = $value->ID;
			
		}
		elseif( is_array($value) )
		{
			// array
			foreach( $value as $k => $v ){
			
				// object?
				if( is_object($v) && isset($v->ID) )
				{
					$value[ $k ] = $v->ID;
				}
			}
			
			// save value as strings, so we can clearly search for them in SQL LIKE statements
			$value = array_map('strval', $value);
			
		}
		
		return $value;
	}
	
}

new acf_field_post_object();

?>