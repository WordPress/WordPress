<?php 


// validate
$field['row_limit'] = intval( $field['row_limit'] );
$field['row_min'] = intval( $field['row_min'] );


// value may be false
if( !is_array($field['value']) )
{
	$field['value'] = array();
}


// row limit = 0?
if( $field['row_limit'] < 1 )
{
	$field['row_limit'] = 999;
}


// min rows
if( $field['row_min'] > count($field['value']) )
{
	for( $i = 0; $i < $field['row_min']; $i++ )
	{
		// already have a value? continue...
		if( isset($field['value'][$i]) )
		{
			continue;
		}
		
		
		// populate values
		$field['value'][ $i ] = array();
		
		
		foreach( $field['sub_fields'] as $sub_field)
		{
			$sub_value = false;
			
			if( !empty($sub_field['default_value']) )
			{
				$sub_value = $sub_field['default_value'];
			}
			
			$field['value'][ $i ][ $sub_field['key'] ] = $sub_value;
		}
		
	}
}


// max rows
$row_count = count($field['value']);
if( $row_count > $field['row_limit'] )
{
	for( $i = 0; $i < $row_count; $i++ )
	{
		if( $i >= $field['row_limit'] )
		{
			unset( $field['value'][ $i ] );
		}
	}
}


// setup values for row clone
$field['value']['acfcloneindex'] = array();
foreach( $field['sub_fields'] as $sub_field )
{
	$sub_value = false;
			
	if( isset($sub_field['default_value']) )
	{
		$sub_value = $sub_field['default_value'];
	}
	
	
	$field['value']['acfcloneindex'][ $sub_field['key'] ] = $sub_value;
}


// helper function which does not exist yet in acf
if( !function_exists('acf_get_join_attr') ):

function acf_get_join_attr( $attributes = false )
{
	// validate
	if( empty($attributes) )
	{
		return '';
	}
	
	
	// vars
	$e = array();
	
	
	// loop through and render
	foreach( $attributes as $k => $v )
	{
		$e[] = $k . '="' . esc_attr( $v ) . '"';
	}
	
	
	// echo
	return implode(' ', $e);
}

endif;

if( !function_exists('acf_join_attr') ):

function acf_join_attr( $attributes = false )
{
	echo acf_get_join_attr( $attributes );
}

endif;

?>
<div class="repeater" data-min_rows="<?php echo $field['row_min']; ?>" data-max_rows="<?php echo $field['row_limit']; ?>">
	<table class="widefat acf-input-table <?php if( $field['layout'] == 'row' ): ?>row_layout<?php endif; ?>">
	<?php if( $field['layout'] == 'table' ): ?>
		<thead>
			<tr>
				<?php 
				
				// order th
				
				if( $field['row_limit'] > 1 ): ?>
					<th class="order"></th>
				<?php endif; ?>
				
				<?php foreach( $field['sub_fields'] as $sub_field ): 
					
					// add width attr
					$attr = "";
					
					if( count($field['sub_fields']) > 1 && isset($sub_field['column_width']) && $sub_field['column_width'] )
					{
						$attr = 'width="' . $sub_field['column_width'] . '%"';
					}
					
					// required
					$required_label = "";
					
					if( $sub_field['required'] )
					{
						$required_label = ' <span class="required">*</span>';
					}
					
					?>
					<th class="acf-th-<?php echo $sub_field['name']; ?> field_key-<?php echo $sub_field['key']; ?>" <?php echo $attr; ?>>
						<span><?php echo $sub_field['label'] . $required_label; ?></span>
						<?php if( isset($sub_field['instructions']) ): ?>
							<span class="sub-field-instructions"><?php echo $sub_field['instructions']; ?></span>
						<?php endif; ?>
					</th><?php
				endforeach; ?>
							
				<?php
				
				// remove th
							
				if( $field['row_min'] < $field['row_limit'] ):  ?>
					<th class="remove"></th>
				<?php endif; ?>
			</tr>
		</thead>
	<?php endif; ?>
	<tbody>
	<?php if( $field['value'] ): foreach( $field['value'] as $i => $value ): ?>
		
		<tr class="<?php echo ( (string) $i == 'acfcloneindex') ? "row-clone" : "row"; ?>">
		
		<?php 
		
		// row number
		
		if( $field['row_limit'] > 1 ): ?>
			<td class="order"><?php echo $i+1; ?></td>
		<?php endif; ?>
		
		<?php
		
		// layout: Row
		
		if( $field['layout'] == 'row' ): ?>
			<td class="acf_input-wrap">
				<table class="widefat acf_input">
		<?php endif; ?>
		
		
		<?php
		
		// loop though sub fields
		
		foreach( $field['sub_fields'] as $sub_field ): ?>
		
			<?php
			
			// attributes (can appear on tr or td depending on $field['layout'])
			$attributes = array(
				'class'				=> "field sub_field field_type-{$sub_field['type']} field_key-{$sub_field['key']}",
				'data-field_type'	=> $sub_field['type'],
				'data-field_key'	=> $sub_field['key'],
				'data-field_name'	=> $sub_field['name']
			);
			
			
			// required
			if( $sub_field['required'] )
			{
				$attributes['class'] .= ' required';
			}

						
			// layout: Row
			
			if( $field['layout'] == 'row' ): ?>
				<tr <?php acf_join_attr( $attributes ); ?>>
					<td class="label">
						<label>
							<?php echo $sub_field['label']; ?>
							<?php if( $sub_field['required'] ): ?><span class="required">*</span><?php endif; ?>
						</label>
						<?php if( isset($sub_field['instructions']) ): ?>
							<span class="sub-field-instructions"><?php echo $sub_field['instructions']; ?></span>
						<?php endif; ?>
					</td>
			<?php endif; ?>
			
			<td <?php if( $field['layout'] != 'row' ){ acf_join_attr( $attributes ); } ?>>
				<div class="inner">
				<?php
				
				// prevent repeater field from creating multiple conditional logic items for each row
				if( $i !== 'acfcloneindex' )
				{
					$sub_field['conditional_logic']['status'] = 0;
					$sub_field['conditional_logic']['rules'] = array();
				}
				
				// add value
				$sub_field['value'] = isset($value[$sub_field['key']]) ? $value[$sub_field['key']] : '';
					
				// add name
				$sub_field['name'] = $field['name'] . '[' . $i . '][' . $sub_field['key'] . ']';
				
				// clear ID (needed for sub fields to work!)
				unset( $sub_field['id'] );
				
				// create field
				do_action('acf/create_field', $sub_field);
				
				?>
				</div>
			</td>
			
			<?php
		
			// layout: Row
			
			if( $field['layout'] == 'row' ): ?>
				</tr>				
			<?php endif; ?>
			
		<?php endforeach; ?>
			
		<?php
		
		// layout: Row
		
		if( $field['layout'] == 'row' ): ?>
				</table>
			</td>
		<?php endif; ?>
		
		<?php 
		
		// delete row
		
		if( $field['row_min'] < $field['row_limit'] ): ?>
			<td class="remove">
				<a class="acf-button-add add-row-before" href="#"></a>
				<a class="acf-button-remove" href="#"></a>
			</td>
		<?php endif; ?>
		
		</tr>
	<?php endforeach; endif; ?>
	</tbody>
	</table>
	<?php if( $field['row_min'] < $field['row_limit'] ): ?>

	<ul class="hl clearfix repeater-footer">
		<li class="right">
			<a href="#" class="add-row-end acf-button"><?php echo $field['button_label']; ?></a>
		</li>
	</ul>

	<?php endif; ?>	
</div>
