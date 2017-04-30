<?php
function wppb_custom_settings(){
		$wppb_customFields = get_option('wppb_custom_fields');
?>
		<h2><?php _e('Extra Profile Fields', 'profilebuilder');?></h2>
		<h3><?php _e('Extra Profile Fields', 'profilebuilder');?></h3>
		<p><?php _e('You can create as many extra fields as your project requires. To break your custom fields into sections (on the front-end), add a "', 'profilebuilder');?><strong><?php _e('heading', 'profilebuilder');?></strong><?php _e('" custom field.', 'profilebuilder');?></p>
		<p><?php _e('All of the fields can be sorted and rearranged to your liking with', 'profilebuilder');?> <strong><?php _e('Drag', 'profilebuilder');?> &amp; <?php _e('Drop', 'profilebuilder');?></strong>. <?php _e('Don\'t worry about the order in which you create your custom fields, you can always reorder them.', 'profilebuilder');?></p>
		<table cellspacing="0">
		  <thead>
			<tr>
			  <th class="col-title"><?php _e('Title', 'profilebuilder');?></th>
			  <th class="col-type"><?php _e('Type', 'profilebuilder');?></th>
			  <th class="col-metaName"><?php _e('Meta-Key', 'profilebuilder');?></th>
			  <th class="col-internal_id"><?php _e('ID', 'profilebuilder');?></th>
			  <th class="col-required"><?php _e('Req\'d', 'profilebuilder');?></th>
			  <th class="col-edit"><a href="javascript:;" class="add-option"><?php _e('Add Option', 'profilebuilder');?></a></th>
			</tr>
		  </thead>
		  <tfoot>
			<tr>
			  <th class="col-title"><?php _e('Title', 'profilebuilder');?></th>
			  <th class="col-type"><?php _e('Type', 'profilebuilder');?></th>
			  <th class="col-metaName"><?php _e('Meta-Key', 'profilebuilder');?></th>
			  <th class="col-internal_id"><?php _e('ID', 'profilebuilder');?></th>
			  <th class="col-required"><?php _e('Req\'d', 'profilebuilder');?></th>
			  <th class="col-edit"><a href="javascript:;" class="add-option"><?php _e('Add Option', 'profilebuilder');?></a></th>
			</tr>
		  </tfoot>
		  <tbody id="framework-settings" class="dragable">
				<!-- we need this party when there are no fields added yet -->
				<tr id="option-0" class="col-heading" >
					<td class="col-title">Hidden Title</td>
					<td class="col-type">heading</td>
					<td class="col-metaName">hidden_title_metaname</td>
					<td class="col-internal_id">0</td>
					<td class="col-item_required">No</td>
					<td class="col-edit">
					  <a href="javascript:;" class="edit-inline"><?php _e('Edit', 'profilebuilder');?></a>
					  <a href="javascript:;" class="delete-inline"><?php _e('Delete', 'profilebuilder');?></a>
						<div class="hidden item-data" id="inline_0">
							  <div class="item_title">Hidden Title</div>
							  <div class="item_type">heading</div>
							  <div class="item_metaName">hidden_title_metaname</div>
							  <div class="internal_id">0</div>
							  <div class="item_required">no</div>
							  <div class="item_desc">Hidden Description</div>
							  <div class="item_options">Hidden Options</div>
						</div>
					</td>
				</tr>
				<!-- END needed part -->
		  <?php 

			$count = 0;
			foreach ( $wppb_customFields as $value ){
			$count++;
			$heading = ($value['item_type'] == 'heading') ? true : false; ?>
				<tr id="option-<?php echo $value['id']; ?>" class="<?php echo ($heading) ? 'col-heading ' : ''; ?><?php /* echo ($count==1) ? 'nodrag nodrop' : ''; */ ?>">
					<td class="col-title"<?php echo ($heading) ? ' colspan="5"' : ''; ?>><?php echo (!$heading) ? '&ndash; ' : ''; ?><?php echo htmlspecialchars_decode( $value['item_title'] ); ?></td>
					<td class="col-type<?php echo ($heading) ? ' hide' : ''; ?>"><?php echo $value['item_type']; ?></td>
					<?php
						if ($value['item_type'] != 'heading'){
							$req = 'No';
							if  ($value['item_required'] == 'yes')
								$req = 'Yes';
							echo '<td class="col-metaName">'.$value['item_metaName'].'</td>';
							echo '<td class="col-internal_id">'.$value['id'].'</td>';
							echo '<td class="col-item_required">'.$req.'</td>';
						}
					?>
					<td class="col-edit">
						<a href="javascript:;" class="edit-inline"><?php _e('Edit', 'profilebuilder');?></a>
						<a href="javascript:;" class="delete-inline"><?php _e('Delete', 'profilebuilder');?></a>
						<div class="hidden item-data" id="inline_<?php echo $value['id']; ?>">
							<div class="item_title"><?php echo htmlspecialchars_decode( $value['item_title'] ); ?></div>
							<div class="item_type"><?php echo $value['item_type']; ?></div>
							<div class="item_metaName"><?php echo $value['item_metaName']; ?></div>
							<div class="internal_id"><?php echo $value['id']; ?></div>
							<div class="item_required"><?php echo esc_html(stripslashes($value['item_required'])); ?></div>
							<div class="item_desc"><?php echo esc_html(stripslashes($value['item_desc'])); ?></div>
							<div class="item_options"><?php echo esc_html(stripslashes($value['item_options'])); ?></div>
						</div>
					</td>
				</tr>
	  <?php } ?>
		  </tbody>
		</table>
		
		
		<table>
		  <tbody id="framework-settings-edit">
			<tr id="inline-edit" class="inline-edit-option nodrop nodrag">
				<td colspan="6">
					<div class="option option-title">
						<div class="section">
							<div class="element">
								<input type="text" name="item_title" class="item_title" value="" />
							</div>
							<div class="description">
								<strong><?php _e('Title:', 'profilebuilder');?></strong> <?php _e('The title of the item.', 'profilebuilder');?>
							</div>
						</div>
					</div>
					<div class="option option-metaName">
						<div class="section">
							<div class="element">
								<input type="text" name="item_metaName" class="item_metaName" title="A valid format will include: letters, numbers, &quot_&quot and &quot-&quot. Any spaces used will be converted to &quot_&quot." value=""/>
							</div>
							<div class="description">
								<strong><?php _e('Meta-Key:', 'profilebuilder');?></strong> <?php _e('Use this in conjuction with WordPress functions to display the value in the page of your choosing. Auto-completed but editable - in this case it must be uniqe.<br/>Changing this might take long in case of a very big user-count.', 'profilebuilder');?>
							</div>
						</div>
					</div>
					<div class="option option-type">
						<div class="section">
							<div class="element">
								<div class="select_wrapper">
									<select name="item_type" class="select item_type">
										<?php							
										$types = array(
										  'heading'       => 'Heading',
										  'input'         => 'Input',
										  'hiddenInput'	  => 'Input (Hidden)',
										  'checkbox'      => 'Checkbox',
										  'agreeToTerms'  => 'Checkbox ("I agree to terms and conditions")',
										  'radio'         => 'Radio',
										  'select'        => 'Select',
										  'countrySelect' => 'Select (Country)',
										  'timeZone'	  => 'Select (Timezone)',
										  'datepicker'	  => 'Datepicker',
										  'textarea'      => 'Textarea',
										  'upload'        => 'Upload',
										  'avatar'		  => 'Avatar'	  
										);
										foreach ( $types as $key => $value ){
										  echo '<option value="'.$key.'">'.$value.'</option>';
										} 
										?>
									</select>
								</div>
							</div>
							<div class="description">
								<strong><?php _e('Option Type:', 'profilebuilder');?></strong> <?php _e('Choose one of the supported option types.', 'profilebuilder');?>
							</div>
						</div>
					</div>
					<div class="option option-desc">
						<div class="section">
							<div class="element">
								<textarea name="item_desc" class="item_desc" rows="8"></textarea>
							</div>
							<div class="description">
								<strong><?php _e('Description:', 'profilebuilder');?></strong> <?php _e('Enter a detailed description of the option for end users to read(optional).', 'profilebuilder');?>
								<span class="alternative3" style="display:none;"><br/><?php echo $text = __('You can only insert links using standard HTML syntax:', 'profilebuilder') .'<br/>&lt;a href="'. __('address', 'profilebuilder') .'"&gt;'. __('name', 'profilebuilder') .'&lt;/a&gt;'; ?></span>
							</div>
						</div>
					</div>
					<div class="option option-options">
						<div class="section">
							<div class="element">
								<input type="text" name="item_options" class="item_options" value="" />
							</div>
							<div class="description">
								<span class="regular"><strong><?php _e('Options:', 'profilebuilder');?></strong> <?php _e('Enter a comma separated list of options. For example, you could have "One,Two,Three" or just a single value like "Yes" for a checkbox.', 'profilebuilder');?></span>
								<span class="alternative" style="display:none;">&nbsp;</span>
							</div>
						</div>
					</div>			
					<div class="option option-internal_id">
						<div class="section">
							<div class="element">
								<input type="text" name="internal_id" class="internal_id" id="internal_id" value="" disabled=""/>
							</div>
							<div class="description">
								<span class="regular3"><strong><?php _e('ID:', 'profilebuilder');?></strong> <?php _e('This is the internal ID for this input. You can use this in conjuction with filters to target this element if needed.<br/>Can\'t be edited.', 'profilebuilder');?></span>
								<span class="alternative3" style="display:none;">&nbsp;</span>
							</div>
						</div>
					</div>						
					<div class="option option-required">
						<div class="section">
							<div class="element">
								<input type="checkbox" class="item_required" name="item_required" value="yes"/>
							</div>
							<div class="description">
								<span class="regular2"><strong><?php _e('Required:', 'profilebuilder');?></strong> <?php _e('Check this box to make this field required.', 'profilebuilder');?></span>
								<span class="alternative2" style="display:none;">&nbsp;</span>
							</div>
						</div>
					</div>
					<?php wp_nonce_field( 'inlineeditnonce', '_ajax_nonce', false ); ?>
					<div class="inline-edit-save">
						<a href="#" class="cancel button-framework reset"><?php _e('Cancel', 'profilebuilder');?></a> 
						<a href="#" class="save button-framework"><?php _e('Save', 'profilebuilder');?></a>
					</div>
				</td>
			</tr>
			<tr id="inline-add">
				<td class="col-title"></td>
				<td class="col-type"></td>
				<td class="col-metaName"></td>
				<td class="col-internal_id"></td>
				<td class="col-item_required"></td>
				<td class="col-edit">
					<a href="#" class="edit-inline"><?php _e('Edit', 'profilebuilder');?></a>
					<a href="#" class="delete-inline"><?php _e('Delete', 'profilebuilder');?></a>
					<div class="hidden item-data">
						<div class="item_title"></div>
						<div class="item_type"></div>
						<div class="item_metaName"></div>
						<div class="internal_id"></div>
						<div class="item_required"></div>
						<div class="item_desc"></div>
						<div class="item_options"></div>
					</div>
				</td>
			</tr>
		  </tbody>
		</table>
<?php
}