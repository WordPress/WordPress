<?php

/*
	AJDE Backender 
	version: 2.2
	Description: print out back end customization form set up for the plugin settings
	Date: 2014-3-11
*/

/** Store settings in this array */
global $print_ajde_customization_form;


if ( ! function_exists( 'print_ajde_customization_form' ) ) {
function print_ajde_customization_form($cutomization_pg_array, $evOPT, $extra_tabs=''){
	
	global $eventon;
	
	$font_sizes = array('10px','11px','12px','13px','14px','16px','18px','20px', '22px', '24px','28px','30px','36px','42px','48px','54px','60px');
	$font_styles = array('normal','bold','italic','bold-italic');
	
	$__no_hr_types = array('begin_afterstatement','end_afterstatement','hiddensection_open','hiddensection_close');
	
	//define variables
	$leftside=$rightside='';
	$count=1;
	

	



	foreach($cutomization_pg_array as $cpa=>$cpav){								
		// left side tabs with different level colors
		$ls_level_code = (isset($cpav['level']))? 'class="'.$cpav['level'].'"': null;
		
		$leftside .= "<li ".$ls_level_code."><a class='".( ($count==1)?'focused':null)."' data-c_id='".$cpav['id']."' title='".$cpav['tab_name']."'>".__($cpav['tab_name'],'eventon')."</a></li>";								
		$tab_type = (isset($cpav['tab_type'] ) )? $cpav['tab_type']:'';
		if( $tab_type !='empty'){ // to not show the right side

			
			// RIGHT SIDE
			$display_default = (!empty($cpav['display']) && $cpav['display']=='show')?'':'display:none';
			
			$rightside.= "<div id='".$cpav['id']."' style='".$display_default."' class='nfer'>
				<h3 style='margin-bottom:10px' >".__($cpav['name'],'eventon')."</h3>
				<em class='hr_line'></em>";

				if($cpav['id'] == 'evcal_002'){
					// color selector guide box
					$rightside.= "<div style='display:none' id='evo_color_guide'>Testing</div>";
				}else{	
					// font awesome
					require_once('includes/settings_fa_fonts.php');
	
					$rightside.= "<div style='display:none' class='fa_icons_selection'><div class='fai_in'><ul class='faicon_ul'>";
					foreach($font_ as $fa){
						$rightside.= "<li><i data-name='".$fa."' class='fa ".$fa."' title='{$fa}'></i></li>";
					}

					$rightside.= "</ul>";
					$rightside.= "</div></div>";
				}

				





			// EACH field
			foreach($cpav['fields'] as $field){
				
				// LEGEND
				$legend_code = (!empty($field['legend']) )? 
						$eventon->throw_guide($field['legend'], 'L', false):null;
				
				
				switch ($field['type']){
					// notices
					case 'notice':
						$rightside.= "<div class='evos_notice'>".__($field['name'],'eventon')."</div>";
					break;

					//IMAGE
					case 'image':
						$image = ''; 
						$meta = $evOPT[$field['id']];
						
						$preview_img_size = (empty($field['preview_img_size']))?'medium'
							: $field['preview_img_size'];
						
						$rightside.= "<div id='pa_".$field['id']."'><p class='nylon_img'>".$field['name']."</p>";
						$rightside.= '<span class="custom_default_image" style="display:none">'.$image.'</span>';  
						
						if ($meta) { $image = wp_get_attachment_image_src($meta, $preview_img_size); $image = $image[0]; } 
						
						$img_code = (empty($image))? "<p class='custom_no_preview_img'><i>No Image Selected</i></p><img id='ev_".$field['id']."' src='' style='display:none' class='custom_preview_image' />"
							: '<p class="custom_no_preview_img" style="display:none"><i>No Image Selected</i></p><img src="'.$image.'" class="custom_preview_image" alt="" />';
						
						$rightside.= '<input name="'.$field['id'].'" type="hidden" class="custom_upload_image" value="'.$meta.'" />'.$img_code.'<br />';
							
						$display_choose = (empty($image))?'block':'none';
						$display_remove = (empty($image))?'none':'block';
						
						$rightside.='<input style="display:'.$display_choose.'" parent="pa_'.$field['id'].'" class="custom_upload_image_button button" type="button" value="Choose Image" />
							<small > <a href="#" style="display:'.$display_remove.'" class="custom_clear_image_button">Remove Image</a></small> 
							<br clear="all" /></div>';
					break;
					
					case 'icon':

						$field_value = (!empty($evOPT[ $field['id']]) )? 
							$evOPT[ $field['id']]:$field['default'];

						$rightside.= "<div class='row_faicons'><p class='fieldname'>".__($field['name'],'foodpress')."</p>";
						// code
						$rightside.= "<p class='acus_line faicon'>
							<i class='fa ".$field_value."'></i>
							<input name='".$field['id']."' class='backender_colorpicker' type='hidden' value='".$field_value."' /></p>";
						$rightside.= "<div class='clear'></div></div>";

					break;

					case 'subheader':
						$rightside.= "<h4 class='acus_subheader'>".__($field['name'],'eventon')."</h4>";
					break;
					case 'note':
						$rightside.= "<p class='nylon_note'><i>".__($field['name'],'eventon')."</i></p>";
					break;
					case 'hr': $rightside.= "<em class='hr_line'></em>"; break;
					case 'checkbox':

						$this_value= (!empty($evOPT[ $field['id']]))? $evOPT[ $field['id']]: null;
						
						$rightside.= "<p><input type='checkbox' name='".$field['id']."' value='yes' ".(($this_value=='yes')?'checked="/checked"/':'')."/> ".$field['name']."</p>";
					break;
					case 'text':
						$this_value= (!empty($evOPT[ $field['id']]))? $evOPT[ $field['id']]: null;
						
						$default_value = (!empty($field['default']) )? 'placeholder="'.$field['default'].'"':null;
						
						$rightside.= "<p>".__($field['name'],'eventon').$legend_code."</p><p><span class='nfe_f_width'><input type='text' name='".$field['id']."' value='".$this_value."' ".$default_value."/></span></p>";
					break;
					case 'textarea':
						
						$textarea_value= (!empty($evOPT[ $field['id']]))?$evOPT[ $field['id']]:null;
						
						$rightside.= "<p>".__($field['name'],'eventon').$legend_code."</p><p><span class='nfe_f_width'><textarea name='".$field['id']."'>".$textarea_value."</textarea></span></p>";
					break;
					case 'font_size':
						$rightside.= "<p>".__($field['name'],'eventon')." <select name='".$field['id']."'>";
								$evo_fval = $evOPT[ $field['id'] ];
								
								foreach($font_sizes as $fs){
									$selected = ($evo_fval == $fs)?"selected='selected'":null;	
									$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
								}
						$rightside.= "</select></p>";
					break;
					case 'font_style':
						$rightside.= "<p>".__($field['name'],'eventon')." <select name='".$field['id']."'>";
								$evo_fval = $evOPT[ $field['id'] ];
								foreach($font_styles as $fs){
									$selected = ($evo_fval == $fs)?"selected='selected'":null;	
									$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
								}
						$rightside.= "</select></p>";
					break;
					case 'border_radius':
						$rightside.= "<p>".__($field['name'],'eventon')." <select name='".$field['id']."'>";
								$evo_fval = $evOPT[ $field['id'] ];
								$border_radius = array('0px','2px','3px','4px','5px','6px','8px','10px');
								foreach($border_radius as $br){
									$selected = ($evo_fval == $br)?"selected='selected'":null;	
									$rightside.=  "<option value='$br' ".$selected.">$br</option>";
								}
						$rightside.= "</select></p>";
					break;
					case 'color':

						// default hex color
						$hex_color = (!empty($evOPT[ $field['id']]) )? 
							$evOPT[ $field['id']]:$field['default'];
						$hex_color_val = (!empty($evOPT[ $field['id'] ]))? $evOPT[ $field['id'] ]: null;

						// RGB Color for the color box
						$rgb_color_val = (!empty($field['rgbid']) && !empty($evOPT[ $field['rgbid'] ]))? $evOPT[ $field['rgbid'] ]: null;
						$__em_class = (!empty($field['rgbid']))? ' rgb': null;

						$rightside.= "<p class='acus_line color'>
							<em><span class='colorselector{$__em_class}' style='background-color:#".$hex_color."' hex='".$hex_color."' title='".$hex_color."'></span>
							<input name='".$field['id']."' class='backender_colorpicker' type='hidden' value='".$hex_color_val."' default='".$field['default']."'/>";
						if(!empty($field['rgbid'])){
							$rightside .= "<input name='".$field['rgbid']."' class='rgb' type='hidden' value='".$rgb_color_val."' />";
						}
						$rightside .= "</em>".__($field['name'],'foodpress')." </p>";

						
					break;
					

					case 'fontation':

						$variations = $field['variations'];
						$rightside.= "<div class='row_fontation'><p class='fieldname'>".__($field['name'],'foodpress')."</p>";

						foreach($variations as $variation){
							switch($variation['type']){
								case 'color':
									// default hex color
									$hex_color = (!empty($evOPT[ $variation['id']]) )? 
										$evOPT[ $variation['id']]:$variation['default'];
									$hex_color_val = (!empty($evOPT[ $variation['id'] ]))? $evOPT[ $variation['id'] ]: null;
									
									$title = (!empty($variation['name']))? $variation['name']:$hex_color;
									$_has_title = (!empty($variation['name']))? true:false;

									// code
									$rightside.= "<p class='acus_line color'>
										<em><span class='colorselector ".( ($_has_title)? 'hastitle': '')."' style='background-color:#".$hex_color."' hex='".$hex_color."' title='".$hex_color."' alt='".$title."'></span>
										<input name='".$variation['id']."' class='backender_colorpicker' type='hidden' value='".$hex_color_val."' default='".$variation['default']."'/></em></p>";

								break;

								case 'font_style':
									$rightside.= "<p><select title='".__('Font Style','foodpress')."' name='".$variation['id']."'>";
											$f1_fs = (!empty($evOPT[ $variation['id'] ]))?
												$evOPT[ $variation['id'] ]:$variation['default'] ;
											foreach($font_styles as $fs){
												$selected = ($f1_fs == $fs)?"selected='selected'":null;	
												$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
											}
									$rightside.= "</select></p>";
								break;

								case 'font_size':
									$rightside.= "<p><select title='".__('Font Size','foodpress')."' name='".$variation['id']."'>";
											
											$f1_fs = (!empty($evOPT[ $variation['id'] ]))?
												$evOPT[ $variation['id'] ]:$variation['default'] ;
											
											foreach($font_sizes as $fs){
												$selected = ($f1_fs == $fs)?"selected='selected'":null;	
												$rightside.= "<option value='$fs' ".$selected.">$fs</option>";
											}
									$rightside.= "</select></p>";
								break;
							}

							
						}

						$rightside.= "<div class='clear'></div></div>";

					break;


					case 'multicolor':

						$variations = $field['variations'];

						$rightside.= "<div class='row_multicolor'>";

						foreach($variations as $variation){
							// default hex color
							$hex_color = (!empty($evOPT[ $variation['id']]) )? 
								$evOPT[ $variation['id']]:$variation['default'];
							$hex_color_val = (!empty($evOPT[ $variation['id'] ]))? $evOPT[ $variation['id'] ]: null;

							$rightside.= "<p class='acus_line color'>
							<em data-name='".__($variation['name'],'foodpress')."'><span class='colorselector' style='background-color:#".$hex_color."' hex='".$hex_color."' title='".$hex_color."'></span>
							<input name='".$variation['id']."' class='backender_colorpicker' type='hidden' value='".$hex_color_val."' default='".$variation['default']."'/></em></p>";
						}

						$rightside.= "<div class='clear'></div><p class='multicolor_alt'></p></div>";

					break;

					case 'radio':
						$rightside.= "<p class='acus_line acus_radio'>".__($field['name'],'eventon')."</br></br>";
						$cnt =0;
						foreach($field['options'] as $option=>$option_val){
							$this_value = (!empty($evOPT[ $field['id'] ]))? $evOPT[ $field['id'] ]:null;
							
							$checked_or_not = ((!empty($this_value) && ($option == $this_value) ) || (empty($this_value) && $cnt==0) )?
								'checked=\"checked\"':null;
							
							$rightside.="<em><input id='".$field['id'].$option_val."' type='radio' name='".$field['id']."' value='".$option."' "
							.  $checked_or_not  ."/><label for='".$field['id'].$option_val."'><span></span>".__($option_val,'eventon')."</label></em>";
							
							$cnt++;
						}						
						$rightside.= "</p>";
						
					break;
					case 'dropdown':
						
						$dropdown_opt = (!empty($evOPT[ $field['id'] ]))? $evOPT[ $field['id'] ]:null;
						
						$rightside.= "<p class='acus_line'>".__($field['name'],'eventon')." <select name='".$field['id']."'>";
						
						foreach($field['options'] as $option=>$option_val){
							$rightside.="<option type='radio' name='".$field['id']."' value='".$option."' "
							.  ( ($option == $dropdown_opt)? 'selected=\"selected\"':null)  ."/> ".$option_val."</option>";
						}						
						$rightside.= "</select></p>";						
					break;
					case 'checkboxes':
						
						$meta_arr= (!empty($evOPT[ $field['id'] ]) )? $evOPT[ $field['id'] ]: null;
						$default_arr= (!empty($field['default'] ) )? $field['default']: null;

						ob_start();
						
						echo "<p class='acus_line acus_checks'><span style='padding-bottom:10px;'>".__($field['name'],'eventon')."</span>";

						
						// foreach checkbox
						foreach($field['options'] as $option=>$option_val){

							$checked='';
							if(!empty($meta_arr) && is_array($meta_arr)){
								$checked = (in_array($option, $meta_arr))?'checked':'';
							}elseif(!empty($default_arr)){
								$checked = (in_array($option, $default_arr))?'checked':'';
							}
							
							echo "<span><input id='".$field['id'].$option_val."' type='checkbox' 
							name='".$field['id']."[]' value='".$option."' ".$checked."/>
							<label for='".$field['id'].$option_val."'><span></span>".$option_val."</label></span>";							
						}						
						echo  "</p>";

						$rightside.= ob_get_clean();

					break;
					
					case 'yesno':
						
						$yesno_value = (!empty( $evOPT[$field['id'] ]) )? 
							$evOPT[$field['id']]:'no';
						
						$after_statement = (isset($field['afterstatement']) )?$field['afterstatement']:'';

						$__default = (!empty( $field['default'] ) && $evOPT[$field['id'] ]!='yes' )? 
							$field['default']
							:$yesno_value;



						$rightside.= "<p class='yesno_row'>".eventon_html_yesnobtn(array('var'=>$__default,'attr'=>array('afterstatement'=>$after_statement) ))."<input type='hidden' name='".$field['id']."' value='".(($__default=='yes')?'yes':'no')."'/><span class='field_name'>".__($field['name'],'eventon')."{$legend_code}</span></p>";
					break;
					case 'begin_afterstatement': 
						
						$yesno_val = (!empty($evOPT[$field['id']]))? $evOPT[$field['id']]:'no';
						
						$rightside.= "<div class='backender_yn_sec' id='".$field['id']."' style='display:".(($yesno_val=='yes')?'block':'none')."'>";
					break;
					case 'end_afterstatement': $rightside.= "</div>"; break;
					
					// hidden section open
					case 'hiddensection_open':
						
						$__display = (!empty($field['display']) && $field['display']=='none')? 'style="display:none"':null;
						$__diclass = (!empty($field['display']) && $field['display']=='none')? '':'open';
						
						$rightside.="<div class='evoSET_hidden_open {$__diclass}'><h4>{$field['name']}{$legend_code}</h4></div>
						<div class='evoSET_hidden_body' {$__display}>";
						
					break;					
					case 'hiddensection_close':	$rightside.="</div>";	break;
					
					// custom code
					case 'customcode':
						
						$rightside.=$field['code'];
						
					break;
				}
				if(!empty($field['type']) && !in_array($field['type'], $__no_hr_types) ){ $rightside.= "<em class='hr_line'></em>";}
				
			}		
			$rightside.= "</div>";
		}
		$count++;
	}
	
	//built out the backender section
	ob_start();
	?>
	<table id='ajde_customization'>
			<tr><td class='backender_left' valign='top'>
				<div id='acus_left'>
					<ul><?php echo $leftside ?></ul>								
				</div>
				<div class="evo-collapse-menu"><div id="collapse-button" class='evo_collpase_btn'><div></div></div><span>Collpase Menu</span></div>
				</td><td width='100%'  valign='top'>
					<div id='acus_right' class='evo_backender_uix'>
						<p id='acus_arrow' style='top:4px'></p>
						<div class='customization_right_in'><?php echo $rightside.$extra_tabs;?></div>
					</div>
				</td></tr>
			</table>
	
	<?php
	echo ob_get_clean();
	
}
}
?>