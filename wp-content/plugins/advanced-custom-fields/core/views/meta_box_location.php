<?php

/*
*  Meta box - locations
*
*  This template file is used when editing a field group and creates the interface for editing location rules.
*
*  @type	template
*  @date	23/06/12
*/


// global
global $post;
		
		
// vars
$groups = apply_filters('acf/field_group/get_location', array(), $post->ID);


// at lease 1 location rule
if( empty($groups) )
{
	$groups = array(
		
		// group_0
		array(
			
			// rule_0
			array(
				'param'		=>	'post_type',
				'operator'	=>	'==',
				'value'		=>	'post',
				'order_no'	=>	0,
				'group_no'	=>	0
			)
		)
		
	);
}


?>
<table class="acf_input widefat" id="acf_location">
	<tbody>
	<tr>
		<td class="label">
			<label for="post_type"><?php _e("Rules",'acf'); ?></label>
			<p class="description"><?php _e("Create a set of rules to determine which edit screens will use these advanced custom fields",'acf'); ?></p>
		</td>
		<td>
			<div class="location-groups">
				
<?php if( is_array($groups) ): ?>
	<?php foreach( $groups as $group_id => $group ): 
		$group_id = 'group_' . $group_id;
		?>
		<div class="location-group" data-id="<?php echo $group_id; ?>">
			<?php if( $group_id == 'group_0' ): ?>
				<h4><?php _e("Show this field group if",'acf'); ?></h4>
			<?php else: ?>
				<h4><?php _e("or",'acf'); ?></h4>
			<?php endif; ?>
			<?php if( is_array($group) ): ?>
			<table class="acf_input widefat">
				<tbody>
					<?php foreach( $group as $rule_id => $rule ): 
						$rule_id = 'rule_' . $rule_id;
					?>
					<tr data-id="<?php echo $rule_id; ?>">
					<td class="param"><?php 
						
						$choices = array(
							__("Basic",'acf') => array(
								'post_type'		=>	__("Post Type",'acf'),
								'user_type'		=>	__("Logged in User Type",'acf'),
							),
							__("Post",'acf') => array(
								'post'			=>	__("Post",'acf'),
								'post_category'	=>	__("Post Category",'acf'),
								'post_format'	=>	__("Post Format",'acf'),
								'post_status'	=>	__("Post Status",'acf'),
								'taxonomy'		=>	__("Post Taxonomy",'acf'),
							),
							__("Page",'acf') => array(
								'page'			=>	__("Page",'acf'),
								'page_type'		=>	__("Page Type",'acf'),
								'page_parent'	=>	__("Page Parent",'acf'),
								'page_template'	=>	__("Page Template",'acf'),
							),
							__("Other",'acf') => array(
								'ef_media'		=>	__("Attachment",'acf'),
								'ef_taxonomy'	=>	__("Taxonomy Term",'acf'),
								'ef_user'		=>	__("User",'acf'),
							)
						);
								
						
						// allow custom location rules
						$choices = apply_filters( 'acf/location/rule_types', $choices );
						
						
						// create field
						$args = array(
							'type'	=>	'select',
							'name'	=>	'location[' . $group_id . '][' . $rule_id . '][param]',
							'value'	=>	$rule['param'],
							'choices' => $choices,
						);
						
						do_action('acf/create_field', $args);							
						
					?></td>
					<td class="operator"><?php 	
						
						$choices = array(
							'=='	=>	__("is equal to",'acf'),
							'!='	=>	__("is not equal to",'acf'),
						);
						
						
						// allow custom location rules
						$choices = apply_filters( 'acf/location/rule_operators', $choices );
						
						
						// create field
						do_action('acf/create_field', array(
							'type'	=>	'select',
							'name'	=>	'location[' . $group_id . '][' . $rule_id . '][operator]',
							'value'	=>	$rule['operator'],
							'choices' => $choices
						)); 	
						
					?></td>
					<td class="value"><?php 
						
						$this->ajax_render_location(array(
							'group_id' => $group_id,
							'rule_id' => $rule_id,
							'value' => $rule['value'],
							'param' => $rule['param'],
						)); 
						
					?></td>
					<td class="add">
						<a href="#" class="location-add-rule button"><?php _e("and",'acf'); ?></a>
					</td>
					<td class="remove">
						<a href="#" class="location-remove-rule acf-button-remove"></a>
					</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
	
	<h4><?php _e("or",'acf'); ?></h4>
	
	<a class="button location-add-group" href="#"><?php _e("Add rule group",'acf'); ?></a>
	
<?php endif; ?>
				
			</div>
		</td>
	</tr>
	</tbody>
</table>