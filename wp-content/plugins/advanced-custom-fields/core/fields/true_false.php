<?php

class acf_field_true_false extends acf_field
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
		$this->name = 'true_false';
		$this->label = __("True / False",'acf');
		$this->category = __("Choice",'acf');
		$this->defaults = array(
			'default_value'	=>	0,
			'message'	=>	'',
		);
		
		
		// do not delete!
    	parent::__construct();
  
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
		// html
		echo '<ul class="acf-checkbox-list ' . $field['class'] . '">';
			echo '<input type="hidden" name="'.$field['name'].'" value="0" />';
			$selected = ($field['value'] == 1) ? 'checked="yes"' : '';
			echo '<li><label><input id="' . $field['id'] . '-1"  type="checkbox" name="'.$field['name'].'" value="1" ' . $selected . ' />' . $field['message'] . '</label></li>';
		
		echo '</ul>';
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
		<label><?php _e("Message",'acf'); ?></label>
		<p class="description"><?php _e("eg. Show extra content",'acf'); ?></a></p>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'text',
			'name'	=>	'fields['.$key.'][message]',
			'value'	=>	$field['message'],
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Default Value",'acf'); ?></label>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'	=>	'true_false',
			'name'	=>	'fields['.$key.'][default_value]',
			'value'	=>	$field['default_value'],
		));
		
		?>
	</td>
</tr>
		<?php
		
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
		$value = ($value == 1) ? true : false;
		
		return $value;
	}
	
}

new acf_field_true_false();

?>