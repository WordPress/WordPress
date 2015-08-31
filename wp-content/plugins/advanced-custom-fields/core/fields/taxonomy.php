<?php

class acf_field_taxonomy extends acf_field
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
		$this->name = 'taxonomy';
		$this->label = __("Taxonomy",'acf');
		$this->category = __("Relational",'acf');
		$this->defaults = array(
			'taxonomy' 			=> 'category',
			'field_type' 		=> 'checkbox',
			'allow_null' 		=> 0,
			'load_save_terms' 	=> 0,
			'multiple'			=> 0,
			'return_format'		=> 'id'
		);
		
		
		// do not delete!
    	parent::__construct();
    	
	}
	
	
	/*
	*  get_terms
	*
	*  This function will return an array of terms for a given field value
	*
	*  @type	function
	*  @date	13/06/2014
	*  @since	5.0.0
	*
	*  @param	$value (array)
	*  @return	$value
	*/
	
	function get_terms( $value, $taxonomy = 'category' ) {
		
		// load terms in 1 query to save multiple DB calls from following code
		if( count($value) > 1 ) {
			
			$terms = get_terms($taxonomy, array(
				'hide_empty'	=> false,
				'include'		=> $value,
			));
			
		}
		
		
		// update value to include $post
		foreach( array_keys($value) as $i ) {
			
			$value[ $i ] = get_term( $value[ $i ], $taxonomy );
			
		}
		
		
		// filter out null values
		$value = array_filter($value);
		
		
		// return
		return $value;
	}
	
	
	/*
	*  load_value()
	*
	*  This filter is appied to the $value after it is loaded from the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value found in the database
	*  @param	$post_id - the $post_id from which the value was loaded from
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the value to be saved in te database
	*/
	
	function load_value( $value, $post_id, $field ) {
		
		// get valid terms
		$value = acf_get_valid_terms($value, $field['taxonomy']);
		
		
		// load/save
		if( $field['load_save_terms'] ) {
			
			// bail early if no value
			if( empty($value) ) {
				
				return $value;
				
			}
			
			
			// get current ID's
			$term_ids = wp_get_object_terms($post_id, $field['taxonomy'], array('fields' => 'ids', 'orderby' => 'none'));
			
			
			// case
			if( empty($term_ids) ) {
				
				// 1. no terms for this post
				return null;
				
			} elseif( is_array($value) ) {
				
				// 2. remove metadata terms which are no longer for this post
				$value = array_map('intval', $value);
				$value = array_intersect( $value, $term_ids );
				
			} elseif( !in_array($value, $term_ids)) {
				
				// 3. term is no longer for this post
				return null;
				
			}
			
		}
		
		
		// return
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
	*  @param	$field - the field array holding all the field options
	*  @param	$post_id - the $post_id of which the value will be saved
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// vars
		if( is_array($value) ) {
		
			$value = array_filter($value);
			
		}
		
		
		// load_save_terms
		if( $field['load_save_terms'] ) {
			
			// vars
			$taxonomy = $field['taxonomy'];
			
			
			// force value to array
			$term_ids = acf_force_type_array( $value );
			
			
			// convert to int
			$term_ids = array_map('intval', $term_ids);
			
			
			// bypass $this->set_terms if called directly from update_field
			if( !did_action('acf/save_post') ) {
				
				wp_set_object_terms( $post_id, $term_ids, $taxonomy, false );
				
				return $value;
				
			}
			
			
			// initialize
			if( empty($this->set_terms) ) {
				
				// create holder
				$this->set_terms = array();
				
				
				// add action
				add_action('acf/save_post', array($this, 'set_terms'), 15, 1);
				
			}
			
			
			// append
			if( empty($this->set_terms[ $taxonomy ]) ) {
				
				$this->set_terms[ $taxonomy ] = array();
				
			}
			
			$this->set_terms[ $taxonomy ] = array_merge($this->set_terms[ $taxonomy ], $term_ids);
			
		}
		
		
		// return
		return $value;
		
	}
	
	
	/*
	*  set_terms
	*
	*  description
	*
	*  @type	function
	*  @date	26/11/2014
	*  @since	5.0.9
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function set_terms( $post_id ) {
		
		// bail ealry if no terms
		if( empty($this->set_terms) ) {
			
			return;
			
		}
		
		
		// loop over terms
		foreach( $this->set_terms as $taxonomy => $term_ids ){
			
			wp_set_object_terms( $post_id, $term_ids, $taxonomy, false );
			
		}
		
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
	
	function format_value_for_api( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) ) {
			
			return $value;
		
		}
		
		
		// force value to array
		$value = acf_force_type_array( $value );
		
		
		// convert values to int
		$value = array_map('intval', $value);
		
		
		// load posts if needed
		if( $field['return_format'] == 'object' ) {
			
			
			// get posts
			$value = $this->get_terms( $value, $field["taxonomy"] );
		
		}
		
		
		// convert back from array if neccessary
		if( $field['field_type'] == 'select' || $field['field_type'] == 'radio' ) {
			
			$value = array_shift($value);
			
		}
		

		// return
		return $value;
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
		// vars
		$single_name = $field['name'];
			
			
		// multi select?
		if( $field['field_type'] == 'multi_select' )
		{
			$field['multiple'] = 1;
			$field['field_type'] = 'select';
			$field['name'] .= '[]';
		}
		elseif( $field['field_type'] == 'checkbox' )
		{
			$field['name'] .= '[]';
		}
		
		// value must be array!
		if( !is_array($field['value']) )
		{
			$field['value'] = array( $field['value'] );
		}
		
		
		// vars
		$args = array(
			'taxonomy'     => $field['taxonomy'],
			'hide_empty'   => false,
			'style'        => 'none',
			'walker'       => new acf_taxonomy_field_walker( $field ),
		);
		
		$args = apply_filters('acf/fields/taxonomy/wp_list_categories', $args, $field );
		
		?>
<div class="acf-taxonomy-field" data-load_save="<?php echo $field['load_save_terms']; ?>">
	<input type="hidden" name="<?php echo $single_name; ?>" value="" />
	
	<?php if( $field['field_type'] == 'select' ): ?>
		
		<select id="<?php echo $field['id']; ?>" name="<?php echo $field['name']; ?>" <?php if( $field['multiple'] ): ?>multiple="multiple" size="5"<?php endif; ?>>
			<?php if( $field['allow_null'] ): ?>
				<option value=""><?php _e("None", 'acf'); ?></option>
			<?php endif; ?>
	
	<?php else: ?>
		<div class="categorychecklist-holder">
		<ul class="acf-checkbox-list">
			<?php if( $field['allow_null'] ): ?>
				<li>
					<label class="selectit">
						<input type="<?php echo $field['field_type']; ?>" name="<?php echo $field['name']; ?>" value="" /> <?php _e("None", 'acf'); ?>
					</label>
				</li>
			<?php endif; ?>
	
	<?php endif; ?>
			
			<?php wp_list_categories( $args ); ?>
	
	<?php if( $field['field_type'] == 'select' ): ?>
	
		</select>
	
	<?php else: ?>
	
		</ul>
		</div>
		
	<?php endif; ?>

</div>
	<?php
	
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
		<label><?php _e("Taxonomy",'acf'); ?></label>
	</td>
	<td>
		<?php
		
		// vars
		$choices = array();
		$taxonomies = get_taxonomies( array(), 'objects' );
		$ignore = array( 'post_format', 'nav_menu', 'link_category' );
		
		
		foreach( $taxonomies as $taxonomy )
		{
			if( in_array($taxonomy->name, $ignore) )
			{
				continue;
			}
			
			$choices[ $taxonomy->name ] = $taxonomy->name;
		}
		
				
		do_action('acf/create_field', array(
			'type'	=>	'select',
			'name'	=>	'fields['.$key.'][taxonomy]',
			'value'	=>	$field['taxonomy'],
			'choices' => $choices,
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
			'optgroup' => true,
			'choices' => array(
				__("Multiple Values",'acf') => array(
					'checkbox' => __('Checkbox', 'acf'),
					'multi_select' => __('Multi Select', 'acf')
				),
				__("Single Value",'acf') => array(
					'radio' => __('Radio Buttons', 'acf'),
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
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Load & Save Terms to Post",'acf'); ?></label>
	</td>
	<td>
		<?php	
		do_action('acf/create_field', array(
			'type'	=>	'true_false',
			'name'	=>	'fields['.$key.'][load_save_terms]',
			'value'	=>	$field['load_save_terms'],
			'message' => __("Load value based on the post's terms and update the post's terms on save",'acf')
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Return Value",'acf'); ?></label>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][return_format]',
			'value'		=>	$field['return_format'],
			'layout'	=>	'horizontal',
			'choices'	=> array(
				'object'	=>	__("Term Object",'acf'),
				'id'		=>	__("Term ID",'acf')
			)
		));
		?>
	</td>
</tr>
		<?php
		
	}
	
		
}

new acf_field_taxonomy();


class acf_taxonomy_field_walker extends Walker
{
	// vars
	var $field = null,
		$tree_type = 'category',
		$db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' );


	// construct
	function __construct( $field )
	{
		$this->field = $field;
	}

	
	// start_el
	function start_el( &$output, $term, $depth = 0, $args = array(), $current_object_id = 0)
	{
		// vars
		$selected = in_array( $term->term_id, $this->field['value'] );
		
		if( $this->field['field_type'] == 'checkbox' )
		{
			$output .= '<li><label class="selectit"><input type="checkbox" name="' . $this->field['name'] . '" value="' . $term->term_id . '" ' . ($selected ? 'checked="checked"' : '') . ' /> ' . $term->name . '</label>';
		}
		elseif( $this->field['field_type'] == 'radio' )
		{
			$output .= '<li><label class="selectit"><input type="radio" name="' . $this->field['name'] . '" value="' . $term->term_id . '" ' . ($selected ? 'checked="checkbox"' : '') . ' /> ' . $term->name . '</label>';
		}
		elseif( $this->field['field_type'] == 'select' )
		{
			$indent = str_repeat("&mdash; ", $depth);
			$output .= '<option value="' . $term->term_id . '" ' . ($selected ? 'selected="selected"' : '') . '>' . $indent . $term->name . '</option>';
		}
		
	}
	
	
	//end_el
	function end_el( &$output, $term, $depth = 0, $args = array() )
	{
		if( in_array($this->field['field_type'], array('checkbox', 'radio')) )
		{
			$output .= '</li>';
		}
		
		$output .= "\n";
	}
	
	
	// start_lvl
	function start_lvl( &$output, $depth = 0, $args = array() )
	{
		// indent
		//$output .= str_repeat( "\t", $depth);
		
		
		// wrap element
		if( in_array($this->field['field_type'], array('checkbox', 'radio')) )
		{
			$output .= '<ul class="children">' . "\n";
		}
	}

	
	// end_lvl
	function end_lvl( &$output, $depth = 0, $args = array() )
	{
		// indent
		//$output .= str_repeat( "\t", $depth);
		
		
		// wrap element
		if( in_array($this->field['field_type'], array('checkbox', 'radio')) )
		{
			$output .= '</ul>' . "\n";
		}
	}
	
}

?>