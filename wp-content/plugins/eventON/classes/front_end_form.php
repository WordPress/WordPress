<?php
	
	/* OUTPUT the form */
	// version: 0.2
	
	$evoopt= $this->evoau_opt;
	$evoopt_1= get_option('evcal_options_evcal_1');
	
	$lang = 'L1';
	
?>


<div id='eventon_form'>
	<form method="POST" action="" enctype="multipart/form-data" id='evoau_form'>
	<?php 	wp_nonce_field( AJDE_EVCAL_BASENAME, 'evoau_noncename' );	?>
		<h2><?php echo (!empty($evoopt['evo_au_title']))? $evoopt['evo_au_title']:'Submit your event';?></h2>
		<?php echo (!empty($evoopt['evo_au_stitle']))? '<h3>'.$evoopt['evo_au_stitle'].'</h3>':null;?>
		
		<?php
		
		
		//access control to form
		if($evoopt['evoau_access']=='yes' && !is_user_logged_in() ){
			$this->log['msg'] = sprintf(__('You must login to to submit events. <a title="%1$s" href="%2$s">%1$s</a>','eventon'), __('Login','eventon'), wp_login_url(get_permalink()) );
			
			echo "<p class='eventon_form_message'><span>".$this->log['msg']."</span></p>";
						
		}else{
		
			// Good Message
			if(isset($this->log['msg']))
				echo "<p class='eventon_form_message'><span>".$this->log['msg']."</span></p>";
			
			// Bad Message
			if(isset($this->log['error']))
				echo "<p class='eventon_form_message error_message'><span>".$this->log['error']."</span></p>";
			
		
		?>
		<div class='evoau_table'>
		<?php
			
			$evcal_date_format = eventon_get_timeNdate_format();
			$opt_2 = get_option('evcal_options_evcal_2');

			$fields = $this->au_form_fields('additional');
			
			$saved_fields = (!empty($evoopt['evoau_fields']) && is_array($evoopt['evoau_fields']) && count($evoopt['evoau_fields'])>0)? $evoopt['evoau_fields']: false;
			

			// form messages
				echo "<div class='form_msg' m1='". ((!empty($this->evoau_opt['evoaun_msg_f']))?
							($this->evoau_opt['evoaun_msg_f'])
							:__('Required fields missing','eventon'))."' style='display:none'></div>";
			
			// DEFAULT name and date feilds
				echo "<div class='row'>
						<p class='label'>
						<input id='_evo_date_format' type='hidden' name='_evo_date_format' jq='".$evcal_date_format[0]."' value='".$evcal_date_format[1]."'/>
						<input id='_evo_time_format' type='hidden' name='_evo_time_format' value='".(($evcal_date_format[2])?'24h':'12h')."'/>
						<label for='event_name'>".eventon_get_custom_language($opt_2, 'evoAUL_evn', 'Event Name', $lang)." *</label></p>
						<p><input type='text' class='fullwidth' name='event_name' value='".( (!empty($_POST['event_name']))? $_POST['event_name']:null)."' placeholder='".eventon_get_custom_language($opt_2, 'evoAUL_evn', 'Event Name', $lang)."'/></p>
					</div>";
				echo "<div class='row'>
						<p class='label'><label for='event_start_date'>".eventon_get_custom_language($opt_2, 'evoAUL_esdt', 'Event Start Date/Time', $lang)." *</label></p>
						<p><input id='evoAU_start_date' type='text' class='evoau_dpicker req' name='event_start_date' placeholder='".eventon_get_custom_language($opt_2, 'evoAUL_phsd', 'Start Date', $lang)."'/><input class='evoau_tpicker req' type='text' name='event_start_time' placeholder='".eventon_get_custom_language($opt_2, 'evoAUL_phst', 'Start Time', $lang)."'/></p>
					</div>";
				echo "<div class='row' id='evoAU_endtime_row'>
						<p class='label'><label for='event_end_date'>".eventon_get_custom_language($opt_2, 'evoAUL_eedt', 'Event End Date/Time', $lang)." *</label></p>
						<p><input id='evoAU_end_date' class='evoau_dpicker req' type='text' name='event_end_date' placeholder='".eventon_get_custom_language($opt_2, 'evoAUL_phed', 'End Date', $lang)."'/><input class='evoau_tpicker' type='text' name='event_end_time req' placeholder='".eventon_get_custom_language($opt_2, 'evoAUL_phet', 'End Time', $lang)."'/></p>
					</div>";

				echo "<div class='row'>
						<p class='label'><input id='evoAU_all_day' name='event_all_day' type='checkbox' value='yes'/> <label>".eventon_get_custom_language($opt_2, 'evoAUL_001', 'All Day Event', $lang)."</label></p>
						<p class='label'><input id='evoAU_hide_ee' name='evo_hide_endtime' type='checkbox' value='yes'/> <label>".eventon_get_custom_language($opt_2, 'evoAUL_002', 'No end time', $lang)."</label></p>
					</div>";
			
			// make sure field names are saved in settings
			if($saved_fields!=false){
				//print_r($fields);

				
				// EACH field array from $eventon_au->au_form_fields()
				foreach($fields as $__index=>$field):
					
					if(in_array($__index, $saved_fields)){

						$__field_name = (!empty($field[4]))?  eventon_get_custom_language($opt_2, $field[4], $field[0], $lang) :$field[0];
						$__field_type = $field[2];
						$_placeholder = (!empty($field[3]))? "placeholder='".__($field[3],'eventon')."'":null;
						$__field_id =$field[1];
						$__req = (!empty($field[5]) && $field[5]=='req')? ' *':null;

						//echo $__field_name;
						$default_val = (!empty($_POST[$__field_id]))? $_POST[$__field_id]: null;

						switch($__field_type){

							case 'text':
								echo "<div class='row'>
									<p class='label'><label for='".$__field_id."'>".$__field_name.$__req."</label></p>
									<p><input type='text' class='fullwidth' name='".$__field_id."' ".$_placeholder." value='{$default_val}'/></p>
								</div>";
							break;
							case 'textarea':
								echo "<div class='row ta'>
									<p class='label'><label for='".$__field_id."'>".$__field_name."</label></p>
									<p><textarea type='text' class='fullwidth' name='".$__field_id."' ".$_placeholder.">{$default_val}</textarea></p>
								</div>";
							break;
							case 'color':
								echo "<div class='row'>
									<p class='color_circle' data-hex='8c8c8c'></p>
									<p class='evoau_color_picker'>
										<input type='hidden' class='evcal_event_color' name='evcal_event_color'/>
										<input type='hidden' name='evcal_event_color_n' class='evcal_event_color_n' value='0'/>
										<label for='".$__field_id."'>".$__field_name."</label>
									</p>									
								</div>";
							break;
							case 'tax':
								$terms = get_terms($field[1], array(
									'hide_empty'=>false,
								));

								if(count($terms)>0){
									echo "<div class='row'>
										<p class='label'><label for='".$__field_id."'>".$__field_name."</label></p><p class='checkbox_row'>";
	
										foreach($terms as $term){
											echo "<span><input type='checkbox' name='".$__field_id."[]' value='".$term->term_id."'/> ".$term->name."</span>";
										}
									echo "</p>
									</div>";
								}

							break;
							case 'image':
								echo "<div class='row'>
									<p class='label'><label for='".$__field_id."'>".$__field_name."</label></p>
									<p><input type='file' id='".$__field_id."' name='".$__field_id."' /></p>
								</div>";
							break;
						}

					}
				endforeach;




				
			}
			
			// Submit button
			echo "<div class='submit_row row'><p><input id='evoau_submit' type='submit' value='".eventon_get_custom_language($opt_2, 'evoAUL_se', 'Submit Event', $lang)."'/></p></div>";
	
		?>
			
		</div>
		
		<?php }?>
	</form>
</div>
<?php


?>