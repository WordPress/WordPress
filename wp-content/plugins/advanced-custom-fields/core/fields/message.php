<?php

class acf_field_message extends acf_field
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
		$this->name = 'message';
		$this->label = __("Message",'acf');
		$this->category = __("Layout",'acf');
		$this->defaults = array(
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
		echo wpautop( $field['message'] );
	}
	
	
	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @param	$field	- an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function create_options( $field )
	{
		// vars
		$key = $field['name'];
		
		?>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label for=""><?php _e("Message",'acf'); ?></label>
		<p class="description"><?php _e("Text &amp; HTML entered here will appear inline with the fields",'acf'); ?><br /><br />
		<?php _e("Please note that all text will first be passed through the wp function ",'acf'); ?><a href="http://codex.wordpress.org/Function_Reference/wpautop" target="_blank">wpautop</a></p>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'textarea',
			'class' => 	'textarea',
			'name'	=>	'fields['.$key.'][message]',
			'value'	=>	$field['message'],
		));
		?>
	</td>
</tr>
		<?php
		
	}
	
}

new acf_field_message();

?>