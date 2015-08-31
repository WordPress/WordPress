<?php

class acf_field_relationship extends acf_field
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
		$this->name = 'relationship';
		$this->label = __("Relationship",'acf');
		$this->category = __("Relational",'acf');
		$this->defaults = array(
			'post_type'			=>	array('all'),
			'max' 				=>	'',
			'taxonomy' 			=>	array('all'),
			'filters'			=>	array('search'),
			'result_elements' 	=>	array('post_title', 'post_type'),
			'return_format'		=>	'object'
		);
		$this->l10n = array(
			'max'		=> __("Maximum values reached ( {max} values )",'acf'),
			'tmpl_li'	=> '
							<li>
								<a href="#" data-post_id="<%= post_id %>"><%= title %><span class="acf-button-remove"></span></a>
								<input type="hidden" name="<%= name %>[]" value="<%= post_id %>" />
							</li>
							'
		);
		
		
		// do not delete!
    	parent::__construct();
    	
    	
    	// extra
		add_action('wp_ajax_acf/fields/relationship/query_posts', array($this, 'query_posts'));
		add_action('wp_ajax_nopriv_acf/fields/relationship/query_posts', array($this, 'query_posts'));
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
		
		
		// validate result_elements
		if( !is_array( $field['result_elements'] ) )
		{
			$field['result_elements'] = array();
		}
		
		if( !in_array('post_title', $field['result_elements']) )
		{
			$field['result_elements'][] = 'post_title';
		}
		
		
		// filters
		if( !is_array( $field['filters'] ) )
		{
			$field['filters'] = array();
		}
		
		
		// return
		return $field;
	}
	
	
	/*
	*  get_result
	*
	*  description
	*
	*  @type	function
	*  @date	5/02/2015
	*  @since	5.1.5
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function get_result( $post, $field, $the_post, $options = array() ) {
		
		// right aligned info
		$title = '<span class="relationship-item-info">';
			
			if( in_array('post_type', $field['result_elements']) ) {
				
				$post_type_object = get_post_type_object( $post->post_type );
				$title .= $post_type_object->labels->singular_name;
				
			}
			
			
			// WPML
			if( !empty($options['lang']) ) {
				
				$title .= ' (' . $options['lang'] . ')';
				
			} elseif( defined('ICL_LANGUAGE_CODE') ) {
				
				$title .= ' (' . ICL_LANGUAGE_CODE . ')';
				
			}
			
		$title .= '</span>';
		
		
		// featured_image
		if( in_array('featured_image', $field['result_elements']) ) {
			
			$image = '';
			
			if( $post->post_type == 'attachment' ) {
				
				$image = wp_get_attachment_image( $post->ID, array(21, 21) );
				
			} else {
				
				$image = get_the_post_thumbnail( $post->ID, array(21, 21) );
				
			}
			
			$title .= '<div class="result-thumbnail">' . $image . '</div>';
			
		}
		
		
		// title
		$post_title = get_the_title( $post->ID );
		
		
		// empty
		if( $post_title === '' ) {
			
			$post_title = __('(no title)', 'acf');
			
		}
		
		
		$title .= $post_title;
		
		
		// status
		if( get_post_status( $post->ID ) != "publish" ) {
			
			$title .= ' (' . get_post_status( $post->ID ) . ')';
			
		}
			
		
		// filters
		$title = apply_filters('acf/fields/relationship/result', $title, $post, $field, $the_post);
		$title = apply_filters('acf/fields/relationship/result/name=' . $field['_name'] , $title, $post, $field, $the_post);
		$title = apply_filters('acf/fields/relationship/result/key=' . $field['key'], $title, $post, $field, $the_post);
		
		
		// return
		return $title;
		
	}
	
	
	/*
	*  query_posts
	*
	*  @description: 
	*  @since: 3.6
	*  @created: 27/01/13
	*/
	
	function query_posts()
   	{
   		// vars
   		$r = array(
   			'next_page_exists' => 1,
   			'html' => ''
   		);
   		
   		
   		// options
		$options = array(
			'post_type'					=>	'all',
			'taxonomy'					=>	'all',
			'posts_per_page'			=>	10,
			'paged'						=>	1,
			'orderby'					=>	'title',
			'order'						=>	'ASC',
			'post_status'				=>	'any',
			'suppress_filters'			=>	false,
			's'							=>	'',
			'lang'						=>	false,
			'update_post_meta_cache'	=>	false,
			'field_key'					=>	'',
			'nonce'						=>	'',
			'ancestor'					=>	false,
		);
		
		$options = array_merge( $options, $_POST );
		
		
		// validate
		if( ! wp_verify_nonce($options['nonce'], 'acf_nonce') )
		{
			die();
		}
		
		
		// WPML
		if( $options['lang'] )
		{
			global $sitepress;
			
			if( !empty($sitepress) )
			{
				$sitepress->switch_lang( $options['lang'] );
			}
		}
		
		
		// convert types
		$options['post_type'] = explode(',', $options['post_type']);
		$options['taxonomy'] = explode(',', $options['taxonomy']);
		
		
		// load all post types by default
		if( in_array('all', $options['post_type']) )
		{
			$options['post_type'] = apply_filters('acf/get_post_types', array());
		}
		
		
		// attachment doesn't work if it is the only item in an array???
		if( is_array($options['post_type']) && count($options['post_type']) == 1 )
		{
			$options['post_type'] = $options['post_type'][0];
		}
		
		
		// create tax queries
		if( ! in_array('all', $options['taxonomy']) )
		{
			// vars
			$taxonomies = array();
			$options['tax_query'] = array();
			
			foreach( $options['taxonomy'] as $v )
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
				$options['tax_query'][] = array(
					'taxonomy' => $k,
					'field' => 'id',
					'terms' => $v,
				);
			}
		}
		
		unset( $options['taxonomy'] );
		
		
		// load field
		$field = array();
		if( $options['ancestor'] )
		{
			$ancestor = apply_filters('acf/load_field', array(), $options['ancestor'] );
			$field = acf_get_child_field_from_parent_field( $options['field_key'], $ancestor );
		}
		else
		{
			$field = apply_filters('acf/load_field', array(), $options['field_key'] );
		}
		
		
		// get the post from which this field is rendered on
		$the_post = get_post( $options['post_id'] );
		
		
		// filters
		$options = apply_filters('acf/fields/relationship/query', $options, $field, $the_post);
		$options = apply_filters('acf/fields/relationship/query/name=' . $field['_name'], $options, $field, $the_post );
		$options = apply_filters('acf/fields/relationship/query/key=' . $field['key'], $options, $field, $the_post );
		
		
		// query
		$wp_query = new WP_Query( $options );

		
		// global
		global $post;
		
		
		// loop
		while( $wp_query->have_posts() ) {
			
			$wp_query->the_post();
			
			
			// get title
			$title = $this->get_result($post, $field, $the_post, $options);
			
			
			// update html
			$r['html'] .= '<li><a href="' . get_permalink($post->ID) . '" data-post_id="' . $post->ID . '">' . $title .  '<span class="acf-button-add"></span></a></li>';
				
		}
		
		
		// next page
		if( (int)$options['paged'] >= $wp_query->max_num_pages ) {
			
			$r['next_page_exists'] = 0;
			
		}
		
		
		// reset
		wp_reset_postdata();
		
		
		// return JSON
		echo json_encode( $r );
		
		die();
			
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

		
		// no row limit?
		if( !$field['max'] || $field['max'] < 1 )
		{
			$field['max'] = 9999;
		}
		
		
		// class
		$class = '';
		if( $field['filters'] )
		{
			foreach( $field['filters'] as $filter )
			{
				$class .= ' has-' . $filter;
			}
		}
		
		$attributes = array(
			'max' => $field['max'],
			's' => '',
			'paged' => 1,
			'post_type' => implode(',', $field['post_type']),
			'taxonomy' => implode(',', $field['taxonomy']),
			'field_key' => $field['key']
		);
		
		
		// Lang
		if( defined('ICL_LANGUAGE_CODE') )
		{
			$attributes['lang'] = ICL_LANGUAGE_CODE;
		}
		
		
		// parent
		preg_match('/\[(field_.*?)\]/', $field['name'], $ancestor);
		if( isset($ancestor[1]) && $ancestor[1] != $field['key'])
		{
			$attributes['ancestor'] = $ancestor[1];
		}
				
		?>
<div class="acf_relationship<?php echo $class; ?>"<?php foreach( $attributes as $k => $v ): ?> data-<?php echo $k; ?>="<?php echo $v; ?>"<?php endforeach; ?>>
	
	
	<!-- Hidden Blank default value -->
	<input type="hidden" name="<?php echo $field['name']; ?>" value="" />
	
	
	<!-- Left List -->
	<div class="relationship_left">
		<table class="widefat">
			<thead>
				<?php if(in_array( 'search', $field['filters']) ): ?>
				<tr>
					<th>
						<input class="relationship_search" placeholder="<?php _e("Search...",'acf'); ?>" type="text" id="relationship_<?php echo $field['name']; ?>" />
					</th>
				</tr>
				<?php endif; ?>
				<?php if(in_array( 'post_type', $field['filters']) ): ?>
				<tr>
					<th>
						<?php 
						
						// vars
						$choices = array(
							'all' => __("Filter by post type",'acf')
						);
						
						
						if( in_array('all', $field['post_type']) )
						{
							$post_types = apply_filters( 'acf/get_post_types', array() );
							$choices = array_merge( $choices, $post_types);
						}
						else
						{
							foreach( $field['post_type'] as $post_type )
							{
								$choices[ $post_type ] = $post_type;
							}
						}
						
						
						// create field
						do_action('acf/create_field', array(
							'type'	=>	'select',
							'name'	=>	'',
							'class'	=>	'select-post_type',
							'value'	=>	'',
							'choices' => $choices,
						));
						
						?>
					</th>
				</tr>
				<?php endif; ?>
			</thead>
		</table>
		<ul class="bl relationship_list">
			<li class="load-more">
				<div class="acf-loading"></div>
			</li>
		</ul>
	</div>
	<!-- /Left List -->
	
	<!-- Right List -->
	<div class="relationship_right">
		<ul class="bl relationship_list">
		<?php

		if( $field['value'] )
		{
			foreach( $field['value'] as $p )
			{
				$title = $this->get_result($p, $field, $post);
				
				
				echo '<li>
					<a href="' . get_permalink($p->ID) . '" class="" data-post_id="' . $p->ID . '">' . $title . '<span class="acf-button-remove"></span></a>
					<input type="hidden" name="' . $field['name'] . '[]" value="' . $p->ID . '" />
				</li>';
				
					
			}
		}
			
		?>
		</ul>
	</div>
	<!-- / Right List -->
	
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
		<label><?php _e("Return Format",'acf'); ?></label>
		<p><?php _e("Specify the returned value on front end",'acf') ?></p>
	</td>
	<td>
		<?php
		do_action('acf/create_field', array(
			'type'		=>	'radio',
			'name'		=>	'fields['.$key.'][return_format]',
			'value'		=>	$field['return_format'],
			'layout'	=>	'horizontal',
			'choices'	=> array(
				'object'	=>	__("Post Objects",'acf'),
				'id'		=>	__("Post IDs",'acf')
			)
		));
		?>
	</td>
</tr>
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
		<label><?php _e("Filters",'acf'); ?></label>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'checkbox',
			'name'	=>	'fields['.$key.'][filters]',
			'value'	=>	$field['filters'],
			'choices'	=>	array(
				'search'	=>	__("Search",'acf'),
				'post_type'	=>	__("Post Type Select",'acf'),
			)
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Elements",'acf'); ?></label>
		<p><?php _e("Selected elements will be displayed in each result",'acf') ?></p>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'checkbox',
			'name'	=>	'fields['.$key.'][result_elements]',
			'value'	=>	$field['result_elements'],
			'choices' => array(
				'featured_image' => __("Featured Image",'acf'),
				'post_title' => __("Post Title",'acf'),
				'post_type' => __("Post Type",'acf'),
			),
			'disabled' => array(
				'post_title'
			)
		));
		?>
	</td>
</tr>
<tr class="field_option field_option_<?php echo $this->name; ?>">
	<td class="label">
		<label><?php _e("Maximum posts",'acf'); ?></label>
	</td>
	<td>
		<?php 
		do_action('acf/create_field', array(
			'type'	=>	'number',
			'name'	=>	'fields['.$key.'][max]',
			'value'	=>	$field['max'],
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
			// Pre 3.3.3, the value is a string coma seperated
			if( is_string($value) )
			{
				$value = explode(',', $value);
			}
			
			
			// convert to integers
			if( is_array($value) )
			{
				$value = array_map('intval', $value);
				
				// convert into post objects
				$value = $this->get_posts( $value );
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
		// empty?
		if( !$value )
		{
			return $value;
		}
		
		
		// Pre 3.3.3, the value is a string coma seperated
		if( is_string($value) )
		{
			$value = explode(',', $value);
		}
		
		
		// empty?
		if( !is_array($value) || empty($value) )
		{
			return $value;
		}
		
		
		// convert to integers
		$value = array_map('intval', $value);
		
		
		// return format
		if( $field['return_format'] == 'object' )
		{
			$value = $this->get_posts( $value );	
		}
		
		
		// return
		return $value;
		
	}
	
	
	/*
	*  get_posts
	*
	*  This function will take an array of post_id's ($value) and return an array of post_objects
	*
	*  @type	function
	*  @date	7/08/13
	*
	*  @param	$post_ids (array) the array of post ID's
	*  @return	(array) an array of post objects
	*/
	
	function get_posts( $post_ids )
	{
		// validate
		if( empty($post_ids) )
		{
			return $post_ids;
		}
		
		
		// vars
		$r = array();
		
		
		// find posts (DISTINCT POSTS)
		$posts = get_posts(array(
			'numberposts'	=>	-1,
			'post__in'		=>	$post_ids,
			'post_type'		=>	apply_filters('acf/get_post_types', array()),
			'post_status'	=>	'any',
		));

		
		$ordered_posts = array();
		foreach( $posts as $p )
		{
			// create array to hold value data
			$ordered_posts[ $p->ID ] = $p;
		}
		
		
		// override value array with attachments
		foreach( $post_ids as $k => $v)
		{
			// check that post exists (my have been trashed)
			if( isset($ordered_posts[ $v ]) )
			{
				$r[] = $ordered_posts[ $v ];
			}
		}
		
		
		// return
		return $r;
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
		
		
		if( is_string($value) )
		{
			// string
			$value = explode(',', $value);
			
		}
		elseif( is_object($value) && isset($value->ID) )
		{
			// object
			$value = array( $value->ID );
			
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
			
		}
		
		
		// save value as strings, so we can clearly search for them in SQL LIKE statements
		$value = array_map('strval', $value);
						
		
		return $value;
	}
	
}

new acf_field_relationship();

?>