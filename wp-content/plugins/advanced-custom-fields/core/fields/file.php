<?php

class acf_field_file extends acf_field
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
		$this->name = 'file';
		$this->label = __("File",'acf');
		$this->category = __("Content",'acf');
		$this->defaults = array(
			'save_format'	=>	'object',
			'library' 		=>	'all'
		);
		$this->l10n = array(
			'select'		=>	__("Select File",'acf'),
			'edit'			=>	__("Edit File",'acf'),
			'update'		=>	__("Update File",'acf'),
			'uploadedTo'	=>	__("uploaded to this post",'acf'),
		);
		
		
		// do not delete!
    	parent::__construct();
    	
    	
    	// filters
		add_filter('get_media_item_args', array($this, 'get_media_item_args'));
		add_filter('wp_prepare_attachment_for_js', array($this, 'wp_prepare_attachment_for_js'), 10, 3);
		
		
		// JSON
		add_action('wp_ajax_acf/fields/file/get_files', array($this, 'ajax_get_files'));
		add_action('wp_ajax_nopriv_acf/fields/file/get_files', array($this, 'ajax_get_files'), 10, 1);
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
		// vars
		$o = array(
			'class'		=>	'',
			'icon'		=>	'',
			'title'		=>	'',
			'size'		=>	'',
			'url'		=>	'',
			'name'		=>	'',
		);
		
		if( $field['value'] && is_numeric($field['value']) )
		{
			$file = get_post( $field['value'] );
			
			if( $file )
			{
				$o['class'] = 'active';
				$o['icon'] = wp_mime_type_icon( $file->ID );
				$o['title']	= $file->post_title;
				$o['size'] = size_format(filesize( get_attached_file( $file->ID ) ));
				$o['url'] = wp_get_attachment_url( $file->ID );
				
				$explode = explode('/', $o['url']);
				$o['name'] = end( $explode );				
			}
		}
		
		
		?>
<div class="acf-file-uploader clearfix <?php echo $o['class']; ?>" data-library="<?php echo $field['library']; ?>">
	<input class="acf-file-value" type="hidden" name="<?php echo $field['name']; ?>" value="<?php echo $field['value']; ?>" />
	<div class="has-file">
		<ul class="hl clearfix">
			<li>
				<img class="acf-file-icon" src="<?php echo $o['icon']; ?>" alt=""/>
				<div class="hover">
					<ul class="bl">
						<li><a href="#" class="acf-button-delete ir">Remove</a></li>
						<li><a href="#" class="acf-button-edit ir">Edit</a></li>
					</ul>
				</div>
			</li>
			<li>
				<p>
					<strong class="acf-file-title"><?php echo $o['title']; ?></strong>
				</p>
				<p>
					<strong><?php _e('Name', 'acf'); ?>:</strong>
					<a class="acf-file-name" href="<?php echo $o['url']; ?>" target="_blank"><?php echo $o['name']; ?></a>
				</p>
				<p>
					<strong><?php _e('Size', 'acf'); ?>:</strong>
					<span class="acf-file-size"><?php echo $o['size']; ?></span>
				</p>
				
			</li>
		</ul>
	</div>
	<div class="no-file">
		<ul class="hl clearfix">
			<li>
				<span><?php _e('No File Selected','acf'); ?></span>. <a href="#" class="button add-file"><?php _e('Add File','acf'); ?></a>
			</li>
		</ul>
	</div>
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
		<label><?php _e("Return Value",'acf'); ?></label>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][save_format]',
			'value'		=>	$field['save_format'],
			'layout'	=>	'horizontal',
			'choices' 	=>	array(
				'object'	=>	__("File Object",'acf'),
				'url'		=>	__("File URL",'acf'),
				'id'		=>	__("File ID",'acf')
			)
		));
		
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Library",'acf'); ?></label>
	</td>
	<td>
		<?php
		
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][library]',
			'value'		=>	$field['library'],
			'layout'	=>	'horizontal',
			'choices' 	=>	array(
				'all'			=>	__('All', 'acf'),
				'uploadedTo'	=>	__('Uploaded to post', 'acf')
			)
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

		// validate
		if( !$value )
		{
			return false;
		}
		
		
		// format
		if( $field['save_format'] == 'url' )
		{
			$value = wp_get_attachment_url($value);
		}
		elseif( $field['save_format'] == 'object' )
		{
			$attachment = get_post( $value );
			
			
			// validate
			if( !$attachment )
			{
				return false;	
			}
			
			
			// create array to hold value data
			$value = array(
				'id' => $attachment->ID,
				'alt' => get_post_meta($attachment->ID, '_wp_attachment_image_alt', true),
				'title' => $attachment->post_title,
				'caption' => $attachment->post_excerpt,
				'description' => $attachment->post_content,
				'mime_type'	=> $attachment->post_mime_type,
				'url' => wp_get_attachment_url( $attachment->ID ),
			);
		}
		
		return $value;
	}
	
	
	/*
	*  get_media_item_args
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 27/01/13
	*/
	
	function get_media_item_args( $vars )
	{
	    $vars['send'] = true;
	    return($vars);
	}
		
	
	/*
   	*  ajax_get_files
   	*
   	*  @description: 
   	*  @since: 3.5.7
   	*  @created: 13/01/13
   	*/
	
   	function ajax_get_files()
   	{
   		// vars
		$options = array(
			'nonce' => '',
			'files' => array()
		);
		$return = array();
		
		
		// load post options
		$options = array_merge($options, $_POST);
		
		
		// verify nonce
		if( ! wp_verify_nonce($options['nonce'], 'acf_nonce') )
		{
			die(0);
		}
		
		
		if( $options['files'] )
		{
			foreach( $options['files'] as $id )
			{
				$o = array();
				$file = get_post( $id );
					
				$o['id'] = $file->ID;
				$o['icon'] = wp_mime_type_icon( $file->ID );
				$o['title']	= $file->post_title;
				$o['size'] = size_format(filesize( get_attached_file( $file->ID ) ));
				$o['url'] = wp_get_attachment_url( $file->ID );
				$o['name'] = end(explode('/', $o['url']));				
				
				$return[] = $o;
			}
		}
		
		
		// return json
		echo json_encode( $return );
		die;
		
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
		if( is_array($value) && isset($value['id']) )
		{
			$value = $value['id'];	
		}
		
		// object?
		if( is_object($value) && isset($value->ID) )
		{
			$value = $value->ID;
		}
		
		return $value;
	}
	
	
	/*
	*  wp_prepare_attachment_for_js
	*
	*  this filter allows ACF to add in extra data to an attachment JS object
	*
	*  @type	function
	*  @date	1/06/13
	*
	*  @param	{int}	$post_id
	*  @return	{int}	$post_id
	*/
	
	function wp_prepare_attachment_for_js( $response, $attachment, $meta )
	{
		// default
		$fs = '0 kb';
		
		
		// supress PHP warnings caused by corrupt images
		if( $i = @filesize( get_attached_file( $attachment->ID ) ) )
		{
			$fs = size_format( $i );
		}
		
		
		// update JSON
		$response['filesize'] = $fs;
		
		
		// return
		return $response;
	}
	
}

new acf_field_file();

?>