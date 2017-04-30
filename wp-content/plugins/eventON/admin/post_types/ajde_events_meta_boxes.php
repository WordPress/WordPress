<?php
/**
 * Meta boxes for ajde_events
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin/ajde_events
 * @version     1.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Init the meta boxes.
 */
function eventon_meta_boxes(){

	$evcal_opt1= get_option('evcal_options_evcal_1');

	// ajde_events meta boxes
	add_meta_box('ajdeevcal_mb2','Event Color', 'ajde_evcal_show_box_2','ajde_events', 'side', 'core');
	add_meta_box('ajdeevcal_mb1','Event Settings', 'ajde_evcal_show_box','ajde_events', 'normal', 'high');	
	
	// if third party is enabled
	if(( $evcal_opt1['evcal_evb_events']=='yes' && !empty($evcal_opt1['evcal_evb_api']) ) || ($evcal_opt1['evcal_api_meetup']=='yes' 
				&& !empty($evcal_opt1['evcal_api_mu_key']) ) || ($evcal_opt1['evcal_paypal_pay']=='yes') )
		add_meta_box('ajdeevcal_mb3','Third Party Settings', 'ajde_evcal_show_box_3','ajde_events', 'normal', 'core');	
	
	
	do_action('eventon_add_meta_boxes');
}
add_action( 'add_meta_boxes', 'eventon_meta_boxes' );
add_action( 'save_post', 'eventon_save_meta_data', 1, 2 );
	

// EXTRA event settings for the page
	function ajde_events_settings_per_post(){
		global $post, $eventon;

		
		if ( ! is_object( $post ) ) return;

		if ( $post->post_type != 'ajde_events' ) return;

		if ( isset( $_GET['post'] ) ) {

			$evo_exclude_ev = get_post_meta($post->ID, 'evo_exclude_ev', true);
		?>
			<div class="misc-pub-section" >
			<div class='evo_event_opts'>
				<p class='yesno_leg_line'>
					<?php 	echo eventon_html_yesnobtn(array('id'=>'evo_exclude_ev', 'var'=>$evo_exclude_ev, 'attr'=>array('allday_switch'=>'1')));?>			
					<input type='hidden' name='evo_exclude_ev' value="<?php echo ($evo_exclude_ev=='yes')?'yes':'no';?>"/>
					<label for='evcal_allday_yn_btn'><?php _e('Exclude from calendar')?><?php $eventon->throw_guide('Set this to Yes to hide event from showing in all calendars','L');?></label>
				</p>
			</div>
			</div>

		<?php

		}
	}
	add_action( 'post_submitbox_misc_actions', 'ajde_events_settings_per_post' );




/** Event Color Meta box. */	
function ajde_evcal_show_box_2(){
		
		
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'evo_noncename_2' );
		$p_id = get_the_ID();
		$ev_vals = get_post_custom($p_id);
		
		
?>		
		<table id="meta_tb2" class="form-table meta_tb" >
		<tr>
			<td>
			<?php
				// Hex value cleaning
				$hexcolor = (!empty($ev_vals["evcal_event_color"]) )? 
					( ($ev_vals["evcal_event_color"][0][0] == '#')?
						substr( $ev_vals["evcal_event_color"][0], 1 ):
						$ev_vals["evcal_event_color"][0] )
					:'64b0bd';
			?>			
			<div id='color_selector' >
				<em id='evColor' style='background-color:<?php echo (!empty($hexcolor) )? '#'.$hexcolor: 'na'; ?>'></em>
				<p class='evselectedColor'>
					<span class='evcal_color_hex evcal_chex'  ><?php echo (!empty($hexcolor) )? $hexcolor: 'Hex code'; ?></span>
					<span class='evcal_color_selector_text evcal_chex'><?php _e('Click here to pick a color');?></span>
				</p>
			</div>
			<p style='margin-bottom:0; padding-bottom:0'><i>OR Select from other colors</i></p>
			
			<div id='evcal_colors'>
				<?php 
				
					$other_events = get_posts(array(
						'posts_per_page'=>-1,
						'post_type'=>'ajde_events',
						'meta_key' => 'evcal_event_color'
					));
					
					$other_colors='';
					
					foreach($other_events as $ev){ setup_postdata($ev);
						$this_id = $ev->ID;
						
						$hexval = get_post_meta($this_id,'evcal_event_color',true);
						$hexval_num = get_post_meta($this_id,'evcal_event_color_n',true);
						
						
						// hex color cleaning
						$hexval = ($hexval[0]=='#')? substr($hexval,1):$hexval;
						
						
						if(!empty( $hexval) && (empty($other_colors) || (is_array($other_colors) && !in_array($hexval, $other_colors)	)	)	){
							echo "<div class='evcal_color_box' style='background-color:#".$hexval."'color_n='".$hexval_num."' color='".$hexval."'></div>";
							
							$other_colors[]=$hexval;
						}				
					}
					
				?>				
			</div>
			<div class='clear'></div>
			
			
			
			<input id='evcal_event_color' type='hidden' name='evcal_event_color' 
				value='<?php echo (!empty($ev_vals["evcal_event_color"]) )? $ev_vals["evcal_event_color"][0]: null; ?>'/>
			<input id='evcal_event_color_n' type='hidden' name='evcal_event_color_n' 
				value='<?php echo (!empty($ev_vals["evcal_event_color_n"]) )? $ev_vals["evcal_event_color_n"][0]: null ?>'/>
			</td>
		</tr>
		<?php do_action('eventon_metab2_end'); ?>
		</table>
<?php }
	
	
	
/**
 * Main meta box.
 */
	function ajde_evcal_show_box(){
		global $eventon;
		
				
		
		$evcal_opt1= get_option('evcal_options_evcal_1');
		$evcal_opt2= get_option('evcal_options_evcal_2');
		
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'evo_noncename' );
		
		// The actual fields for data entry
		$p_id = get_the_ID();
		$ev_vals = get_post_custom($p_id);
		
		
		$evcal_allday = (!empty($ev_vals["evcal_allday"]))? $ev_vals["evcal_allday"][0]:null;		
		$show_style_code = ($evcal_allday=='yes') ? "style='display:none'":null;

		$select_a_arr= array('AM','PM');
		
		
		// --- TIME variations
		$evcal_date_format = eventon_get_timeNdate_format($evcal_opt1);
		$time_hour_span= ($evcal_date_format[2])?25:13;
		
		
		// GET DATE and TIME values
		$_START=(!empty($ev_vals['evcal_srow'][0]))?
			eventon_get_editevent_kaalaya($ev_vals['evcal_srow'][0],$evcal_date_format[1], $evcal_date_format[2]):false;
		$_END=(!empty($ev_vals['evcal_erow'][0]))?
			eventon_get_editevent_kaalaya($ev_vals['evcal_erow'][0],$evcal_date_format[1], $evcal_date_format[2]):false;
		
		//print_r($_START);
		//print_r($ev_vals);
	?>
	
	

<?php
	
	// --------------------------
	// HTML - date
	ob_start();
	?>
	<!-- date and time formats to use -->
	<input type='hidden' name='_evo_date_format' value='<?php echo $evcal_date_format[1];?>'/>
	<input type='hidden' name='_evo_time_format' value='<?php echo ($evcal_date_format[2])?'24h':'12h';?>'/>	
	<div id='evcal_dates' date_format='<?php echo $evcal_date_format[0];?>'>
		
		
		<p class='yesno_leg_line fcw'>
			<?php 	echo eventon_html_yesnobtn(array('id'=>'evcal_allday_yn_btn', 'var'=>$evcal_allday, 'attr'=>array('allday_switch'=>'1')));?>			
			<input type='hidden' name='evcal_allday' value="<?php echo ($evcal_allday=='yes')?'yes':'no';?>"/>
			<label for='evcal_allday_yn_btn'><?php _e('All Day Event')?></label>
		</p>
		<p style='clear:both'></p>
		
		<!-- START TIME-->
		<div class='evo_start_event evo_datetimes'>
			<div class='evo_date'>
				<p id='evcal_start_date_label'><?php _e('Event Start Date')?></p>
				<input id='evo_dp_from' class='evcal_data_picker datapicker_on' type='text' id='evcal_start_date' name='evcal_start_date' value='<?php echo ($_START)?$_START[0]:null?>' placeholder='<?php echo $evcal_date_format[1];?>'/>					
				<span>Select a Date</span>
			</div>					
			<div class='evcal_date_time switch_for_evsdate evcal_time_selector' <?php echo $show_style_code?>>
				<div class='evcal_select'>
					<select id='evcal_start_time_hour' class='evcal_date_select' name='evcal_start_time_hour'>
						<?php
							//echo "<option value=''>--</option>";
							$start_time_h = ($_START)?$_START[1]:null;						
						for($x=1; $x<$time_hour_span;$x++){									
							echo "<option value='$x'".(($start_time_h==$x)?'selected="selected"':'').">$x</option>";
						}?>
					</select>
				</div><p style='display:inline; font-size:24px;padding:4px 2px'>:</p>
				<div class='evcal_select'>
					
					<select id='evcal_start_time_min' class='evcal_date_select' name='evcal_start_time_min'>
						<?php	
							//echo "<option value=''>--</option>";
							$start_time_m = ($_START)?	$_START[2]: null;
						for($x=0; $x<12;$x++){
							$min = ($x<2)?('0'.$x*5):$x*5;
							echo "<option value='$min'".(($start_time_m==$min)?'selected="selected"':'').">$min</option>";
						}?>
					</select>
				</div>
				
				<?php if(!$evcal_date_format[2]):?>
				<div class='evcal_select evcal_ampm_sel'>
					<select name='evcal_st_ampm' id='evcal_st_ampm' >
						<?php
							$evcal_st_ampm = ($_START)?$_START[3]:null;
							foreach($select_a_arr as $sar){
								echo "<option value='".$sar."' ".(($evcal_st_ampm==$sar)?'selected="selected"':'').">".$sar."</option>";
							}
						?>								
					</select>
				</div>	
				<?php endif;?>
				<br/>
				<span><?php _e('Select a Time')?></span>
			</div><div class='clear'></div>
		</div>
		
		
		
		<!-- END TIME -->
		<?php 
			$evo_hide_endtime = (!empty($ev_vals["evo_hide_endtime"]) )? $ev_vals["evo_hide_endtime"][0]:null;
		?>
		<div class='evo_end_event evo_datetimes switch_for_evsdate'>
			<div class='evo_enddate_selection' style='<?php echo ($evo_hide_endtime=='yes')?'display:none':null;?>'>
			<div class='evo_date'>
				<p><?php _e('Event End Date','eventon')?></p>
				<input id='evo_dp_to' class='evcal_data_picker datapicker_on' type='text' id='evcal_end_date' name='evcal_end_date' value='<?php echo ($_END)? $_END[0]:null; ?>'/>					
				<span><?php _e('Select a Date','eventon')?></span>
				
			</div>
			<div class='evcal_date_time evcal_time_selector' <?php echo $show_style_code?>>
				<div class='evcal_select'>
					<select class='evcal_date_select' name='evcal_end_time_hour'>
						<?php	
							//echo "<option value=''>--</option>";
							$end_time_h = ($_END)?$_END[1]:null;
							for($x=1; $x<$time_hour_span;$x++){
								echo "<option value='$x'".(($end_time_h==$x)?'selected="selected"':'').">$x</option>";
							}
						?>
					</select>
				</div><p style='display:inline; font-size:24px;padding:4px'>:</p>
				<div class='evcal_select'>
					<select class='evcal_date_select' name='evcal_end_time_min'>
						<?php	
							//echo "<option value=''>--</option>";
							$end_time_m = ($_END[2])?$_END[2]:null;
							for($x=0; $x<12;$x++){
								$min = ($x<2)?('0'.$x*5):$x*5;
								echo "<option value='$min'".(($end_time_m==$min)?'selected="selected"':'').">$min</option>";
							}
						?>
					</select>
				</div>
				
				<?php if(!$evcal_date_format[2]):?>
				<div class='evcal_select evcal_ampm_sel'>
					<select name='evcal_et_ampm'>
						<?php
							$evcal_et_ampm = ($_END)?$_END[3]:null;
							
							foreach($select_a_arr as $sar){
								echo "<option value='".$sar."' ".(($evcal_et_ampm==$sar)?'selected="selected"':'').">".$sar."</option>";
							}
						?>								
					</select>
				</div>
				<?php endif;?>
				<br/>
				<span><?php _e('Select the Time','eventon')?></span>
			</div><div class='clear'></div>
			</div>
			
			
			<!-- end time yes/no option -->					
			<p class='yesno_leg_line '>
				<?php 	echo eventon_html_yesnobtn(array('id'=>'evo_endtime', 'var'=>$evo_hide_endtime));?>
				
				<input type='hidden' name='evo_hide_endtime' value="<?php echo ($evo_hide_endtime=='yes')?'yes':'no';?>"/>
				<label for='evo_hide_endtime'><?php _e('Hide End Time from calendar')?></label>
			</p>
			<p style='clear:both'></p>
		</div>
		<div style='clear:both'></div>
		
		<?php 

			// Recurring events 
			$evcal_repeat = (!empty($ev_vals["evcal_repeat"]) )? $ev_vals["evcal_repeat"][0]:null;
		?>
		<div id='evcal_rep' class='evd'>



			<div class='evcalr_1'>
				<p class='yesno_leg_line '>
					<?php 	echo eventon_html_yesnobtn(array('id'=>'evd_repeat', 'var'=>$evcal_repeat));?>
					
					<input type='hidden' name='evcal_repeat' value="<?php echo ($evcal_repeat=='yes')?'yes':'no';?>"/>
					<label for='evcal_repeat'><?php _e('Repeating event')?></label>
				</p>
				<p style='clear:both'></p>
			</div>
			<p class='eventon_ev_post_set_line'></p>
			<?php
				$display = (!empty($ev_vals["evcal_repeat"]) && $evcal_repeat=='yes')? '':'none';
			?>
			<div class='evcalr_2' style='display:<?php echo $display ?>'>
				
				<?php
					// repeat frequency array
					$repeat_freq= apply_filters('evo_repeat_intervals', array('weekly'=>'weeks','monthly'=>'months','yearly'=>'years') );
		
				?>

				<p class='evcalr_2_freq evcalr_2_p'><span class='evo_form_label'>Frequency:</span> <select id='evcal_rep_freq' name='evcal_rep_freq'>
				<?php
					$evcal_rep_freq = (!empty($ev_vals['evcal_rep_freq']))?$ev_vals['evcal_rep_freq'][0]:null;
					foreach($repeat_freq as $refv=>$ref){
						echo "<option field='".$ref."' value='".$refv."' ".(($evcal_rep_freq==$refv)?'selected="selected"':'').">".$refv."</option>";
					}
					
				?></select></p>
				
				<?php
					$evcal_rep_gap = (!empty($ev_vals['evcal_rep_gap']) )? 
							$ev_vals['evcal_rep_gap'][0]:1;
					$freq = (!empty($ev_vals["evcal_rep_freq"]) )?
						 ($repeat_freq[ $ev_vals["evcal_rep_freq"][0] ])
						: null;

					if(!empty($ev_vals['repeat_intervals'])){
						//print_r(unserialize($ev_vals['repeat_intervals'][0]));
					}
				?>						
				<p class='evcalr_2_rep evcalr_2_p'><span class='evo_form_label'>Gap between repeats:</span>
					<input type='number' name='evcal_rep_gap' min='1' max='100' value='<?php echo $evcal_rep_gap;?>' placeholder='1'/>	 <span id='evcal_re'><?php echo $freq;?></span></p>
				
				
				
				<?php
					
					// repeat number
						$evcal_rep_num = (!empty($ev_vals['evcal_rep_num']) )?  $ev_vals['evcal_rep_num'][0]:1;
					
					// repeat by
						$evp_repeat_rb = (!empty($ev_vals['evp_repeat_rb']) )? $ev_vals['evp_repeat_rb'][0]: null;	
						$evo_rep_WK = (!empty($ev_vals['evo_rep_WK']) )? unserialize($ev_vals['evo_rep_WK'][0]): array();
						$evo_repeat_wom = (!empty($ev_vals['evo_repeat_wom']) )? $ev_vals['evo_repeat_wom'][0]: null;

						
					// display none section
						$__display_none_1 =  (!empty($ev_vals['evcal_rep_freq']) && $ev_vals['evcal_rep_freq'][0] =='monthly')? 'block': 'none';
						$__display_none_2 =  ($__display_none_1=='block' && !empty($ev_vals['evp_repeat_rb']) && $ev_vals['evp_repeat_rb'][0] =='dow')? 'block': 'none';
				?>
					
				<?php // monthly only ?>
					<p class='evcalr_2_p evo_rep_month' style='display:<?php echo $__display_none_1;?>'>
						<span class='evo_form_label'>Repeat by:</span>
						<select id='evo_rep_by' name='evp_repeat_rb'>
							<option value='dom' <?php echo ('dom'==$evp_repeat_rb)? 'selected="selected"':null;?>>Day of the month</option>
							<option value='dow' <?php echo ('dow'==$evp_repeat_rb)? 'selected="selected"':null;?>>Day of the week</option>
						</select>
					</p>
					<p class='evo_days_list evo_rep_month_2'  style='display:<?php echo $__display_none_2;?>'>
						<span class='evo_form_label'>Repeat on selected days: </span>
						<em><input type='checkbox' name='evo_rep_WK[]' value='0' <?php echo (in_array('0', $evo_rep_WK))? 'checked="checked"':null;?>><label>S</label></em>
						<em><input type='checkbox' name='evo_rep_WK[]' value='1' <?php echo (in_array('1', $evo_rep_WK))? 'checked="checked"':null;?>><label>M</label></em>
						<em><input type='checkbox' name='evo_rep_WK[]' value='2' <?php echo (in_array('2', $evo_rep_WK))? 'checked="checked"':null;?>><label>T</label></em>
						<em><input type='checkbox' name='evo_rep_WK[]' value='3' <?php echo (in_array('3', $evo_rep_WK))? 'checked="checked"':null;?>><label>W</label></em>
						<em><input type='checkbox' name='evo_rep_WK[]' value='4' <?php echo (in_array('4', $evo_rep_WK))? 'checked="checked"':null;?>><label>T</label></em>
						<em><input type='checkbox' name='evo_rep_WK[]' value='5' <?php echo (in_array('5', $evo_rep_WK))? 'checked="checked"':null;?>><label>F</label></em>
						<em><input type='checkbox' name='evo_rep_WK[]' value='6' <?php echo (in_array('6', $evo_rep_WK))? 'checked="checked"':null;?>><label>S</label></em>
					</p>
					<p class='evcalr_2_p evo_rep_month_2'  style='display:<?php echo $__display_none_2;?>'>
						<span class='evo_form_label'>Week of month to repeat: </span>
						<select id='evo_wom' name='evo_repeat_wom'>
							<option value='none' <?php echo ('none'== $evo_repeat_wom)? 'checked="checked"':null;?>>None</option>
							<option value='1' <?php echo ('1'== $evo_repeat_wom)? 'selected="selected"':null;?>>First</option>
							<option value='2' <?php echo ('2'== $evo_repeat_wom)? 'selected="selected"':null;?>>Second</option>
							<option value='3' <?php echo ('3'== $evo_repeat_wom)? 'selected="selected"':null;?>>Third</option>
							<option value='4' <?php echo ('4'== $evo_repeat_wom)? 'selected="selected"':null;?>>Fourth</option>
							<option value='-1' <?php echo ('-1'== $evo_repeat_wom)? 'selected="selected"':null;?>>Last</option>
						</select>
					</p>
				
					<p class='evo_month_rep_value evo_rep_month_2' style='display:none'></p>
					
					<p class='evcalr_2_numr evcalr_2_p'><span class='evo_form_label'>Number of repeats:</span>
						<input type='number' name='evcal_rep_num' min='1' max='100' value='<?php echo $evcal_rep_num;?>' placeholder='1'/>						
					</p>



			</div>
		</div>	
		
	</div>
	
	<?php 
	$_html_TD = ob_get_clean();
	$__hiddenVAL_TD = '';
	
	
	// --------------------------
	// HTML - location
		ob_start();
		?>
			<div class='evcal_data_block_style1'>
				<p class='edb_icon evcal_edb_map'></p>
				<div class='evcal_db_data'>			
					<p>
					<?php
						$terms = get_terms('event_location', array('hide_empty'=>false));
						//print_r($terms);
						if(count($terms)>0){

							echo "<select id='evcal_location_field' name='evcal_location_name'>
								<option value=''>Select a saved location</option>";
						    foreach ( $terms as $term ) {

						    	$t_id = $term->term_id;
						    	$term_meta = get_option( "taxonomy_$t_id" );

						       	echo "<option value='". $term->name ."' data-address='".((!empty( $term_meta['location_address'] )) ? esc_attr( $term_meta['location_address'] ) : '')  ."' data-lat='". ( (!empty( $term_meta['location_lat'] )) ? esc_attr( $term_meta['location_lat'] ) : '' ) ."' data-lon='". ( (!empty( $term_meta['location_lon'] )) ? esc_attr( $term_meta['location_lon'] ) : '') ."'>" . $term->name . "</option>";
						        
						    }
						    echo "</select> <label for='evcal_location_field'>".__('Choose already saved location or type new one below','eventon')."</label>";
						}

					?>

					<input type='text' id='evcal_location_name' name='evcal_location_name' value="<?php echo (!empty($ev_vals["evcal_location_name"]) )? $ev_vals["evcal_location_name"][0]:null?>" style='width:100%' placeholder='eg. Irving City Park'/><label for='evcal_location_name'><?php _e('Event Location Name')?></label></p>
					<p><input type='text' id='evcal_location' name='evcal_location' value="<?php echo (!empty($ev_vals["evcal_location"]) )? $ev_vals["evcal_location"][0]:null?>" style='width:100%' placeholder='eg. 12 Rue de Rivoli, Paris'/><label for='evcal_location'><?php _e('Event Location Address')?></label></p>
					
					
					<p><input type='text' id='evcal_lat' class='evcal_latlon' name='evcal_lat' value='<?php echo (!empty($ev_vals["evcal_lat"]) )? $ev_vals["evcal_lat"][0]:null?>' placeholder='<?php _e('Latitude');?>'/>
					<input type='text' id='evcal_lon' class='evcal_latlon' name='evcal_lon' value='<?php echo (!empty($ev_vals["evcal_lon"]) )? $ev_vals["evcal_lon"][0]:null?>' placeholder='<?php _e('Longitude')?>'/></p>
					<p><i>(NOTE: If Latlon provided, Latlon will be used for generating google maps while location address will be shown as text address. Location address field is required for this to work. <a href='http://itouchmap.com/latlong.html' target='_blank'>Find LanLat for address</a>)</i></p>
					
					
					<p class='yesno_leg_line'>
						<?php 	
						$location_val = (!empty($ev_vals["evcal_gmap_gen"]))? $ev_vals["evcal_gmap_gen"][0]: null;
						echo eventon_html_yesnobtn(array('id'=>'evo_endtime', 'var'=>$location_val));?>
						
						<input type='hidden' name='evcal_gmap_gen' value="<?php echo (!empty($ev_vals["evcal_gmap_gen"]) && $ev_vals["evcal_gmap_gen"][0]=='yes')?'yes':'no';?>"/>
						<label for='evcal_gmap_gen'><?php _e('Generate Google Map from the address','eventon')?></label>
					</p>
					<p style='clear:both'></p>
				</div>
			</div>
		<?php
		
		$_html_LOC = ob_get_clean();
		$__hiddenVAL_LOC = '';
			
	// --------------------------
	// HTML - Organizer
		ob_start();
		?>
			<div class='evcal_data_block_style1'>
				<div class='evcal_db_data'>		
					<input type='text' id='evcal_organizer' name='evcal_organizer' value="<?php echo (!empty($ev_vals["evcal_organizer"]) )? $ev_vals["evcal_organizer"][0]:null?>" style='width:100%' placeholder='<?php _e('Event Organizer','eventon');?>'/><br/>						
				</div>
			</div>
		<?php
		$_html_OR = ob_get_clean();
		$__hiddenVAL_OR = '';
		
	// --------------------------
	// HTML - User Interaction
		ob_start();
		?>
			<div class='evcal_data_block_style1'>
				<div class='evcal_db_data'>
					
					<?php
						$exlink_option = (!empty($ev_vals["_evcal_exlink_option"]))? $ev_vals["_evcal_exlink_option"][0]:1;
						$exlink_target = (!empty($ev_vals["_evcal_exlink_target"]) && $ev_vals["_evcal_exlink_target"][0]=='yes')?
							$ev_vals["_evcal_exlink_target"][0]:null;
					?>
					
					<input id='evcal_exlink_option' type='hidden' name='_evcal_exlink_option' value='<?php echo $exlink_option; ?>'/>
					
					<input id='evcal_exlink_target' type='hidden' name='_evcal_exlink_target' value='<?php echo ($exlink_target) ?>'/>
					
					<?php
						$display_link_input = (!empty($ev_vals["_evcal_exlink_option"]) && $ev_vals["_evcal_exlink_option"][0]!='1')? 'display:block':'display:none';
				
					?>
					<p <?php echo ($exlink_option=='1' || $exlink_option=='3')?"style='display:none'":null;?> id='evo_new_window_io' class='<?php echo ($exlink_target=='yes')?'selected':null;?>'><span></span> <?php _e('Open in new window','eventon');?></p>
					
					<!-- external link field-->
					<input id='evcal_exlink' placeholder='<?php _e('Type the URL address','eventon');?>' type='text' name='evcal_exlink' value='<?php echo (!empty($ev_vals["evcal_exlink"]) )? $ev_vals["evcal_exlink"][0]:null?>' style='width:100%; <?php echo $display_link_input;?>'/>
					
					<div class='evcal_db_uis'>
						<a link='no'  class='evcal_db_ui evcal_db_ui_1 <?php echo ($exlink_option=='1')?'selected':null;?>' title='Slide Down Event Card' value='1'></a>
						
						<!-- open as link-->
						<a link='yes' class='evcal_db_ui evcal_db_ui_2 <?php echo ($exlink_option=='2')?'selected':null;?>' title='External Link' value='2'></a>	
						
						<!-- open as popup -->
						<a link='yes' class='evcal_db_ui evcal_db_ui_3 <?php echo ($exlink_option=='3')?'selected':null;?>' title='Popup Window' value='3'></a>
						
						<?php
							// (-- addon --)
							if(has_action('evcal_ui_click_additions')){
								do_action('evcal_ui_click_additions');
							}
						?>							
						<div class='clear'></div>
					</div>
				</div>
			</div>
		<?php
		$_html_UIN = ob_get_clean();
		$__hiddenVAL_UIN = '';
		
	// --------------------------
	// HTML - Learn More
		ob_start();
		?>
			<div class='evcal_data_block_style1'>
				<div class='evcal_db_data'>
					<input type='text' id='evcal_lmlink' name='evcal_lmlink' value='<?php echo (!empty($ev_vals["evcal_lmlink"]) )? $ev_vals["evcal_lmlink"][0]:null?>' style='width:100%'/><br/>
					<input type='checkbox' name='evcal_lmlink_target' value='yes' <?php echo (!empty($ev_vals["evcal_lmlink_target"]) && $ev_vals["evcal_lmlink_target"][0]=='yes')? 'checked="checked"':null?>/> <?php _e('Open in New window','eventon'); ?>
				</div>
			</div>
		<?php
		$_html_LM = ob_get_clean();
		$__hiddenVAL_LM = '';		
		
	
	/** custom fields **/
		$evMB_custom = array();
		for($x =1; $x<4; $x++){	
			
			if(eventon_is_custom_meta_field_good($x)){
				
				$fa_icon_class = $evcal_opt1['evcal__fai_00c'.$x];
				
				ob_start();
				
				echo "<div class='evcal_data_block_style1'>
						<div class='evcal_db_data'>";

					// FIELD
					$__saved_field_value = (!empty($ev_vals["_evcal_ec_f".$x."a1_cus"]) )? $ev_vals["_evcal_ec_f".$x."a1_cus"][0]:null ;
					$__field_id = '_evcal_ec_f'.$x.'a1_cus';

					// wysiwyg editor
					if(!empty($evcal_opt1['evcal_ec_f'.$x.'a2']) && 
						$evcal_opt1['evcal_ec_f'.$x.'a2']=='textarea'){
						
						wp_editor($__saved_field_value, $__field_id);
						
					// button
					}elseif(!empty($evcal_opt1['evcal_ec_f'.$x.'a2']) && 
						$evcal_opt1['evcal_ec_f'.$x.'a2']=='button'){
						
						$__saved_field_link = (!empty($ev_vals["_evcal_ec_f".$x."a1_cusL"]) )? $ev_vals["_evcal_ec_f".$x."a1_cusL"][0]:null ;


						echo "<input type='text' id='".$__field_id."' name='_evcal_ec_f".$x."a1_cus' ";
						echo 'value="'. $__saved_field_value.'"';						
						echo "style='width:100%' placeholder='Button Text' title='Button Text'/>";

						echo "<input type='text' id='".$__field_id."' name='_evcal_ec_f".$x."a1_cusL' ";
						echo 'value="'. $__saved_field_link.'"';						
						echo "style='width:100%' placeholder='Button Link' title='Button Link'/>";

							$onw = (!empty($ev_vals["_evcal_ec_f".$x."_onw"]) )? $ev_vals["_evcal_ec_f".$x."_onw"][0]:null ;
						?>
						<input type='checkbox' name='_evcal_ec_f<?php echo $x;?>_onw' value='yes' <?php echo (!empty($onw) && $onw=='yes')? 'checked="checked"':null?>/> <?php _e('Open in New window','eventon'); ?>
						<?php
					
					// text	
					}else{
						echo "<input type='text' id='".$__field_id."' name='_evcal_ec_f".$x."a1_cus' ";
								
						echo 'value="'. $__saved_field_value.'"';						
						echo "style='width:100%'/>";
						
					}

				echo "</div></div>";


				$__html = ob_get_clean();
				
				$evMB_custom[]= array(
					'id'=>'evcal_ec_f'.$x.'a1',
					'variation'=>'customfield',
					'name'=>$evcal_opt1['evcal_ec_f'.$x.'a1'],		
					'iconURL'=>$fa_icon_class,
					'iconPOS'=>'',
					'type'=>'code',
					'content'=>$__html,
					'slug'=>'evcal_ec_f'.$x.'a1'
				);
			}
		}
			
	
	// array of all meta boxes
		$metabox_array = apply_filters('eventon_event_metaboxs', array(
			array(
				'id'=>'ev_timedate',
				'name'=>__('Time and Date','eventon'),	
				'hiddenVal'=>$__hiddenVAL_TD,	
				'iconURL'=>'',
				'iconPOS'=>'0 -190px',
				'type'=>'code',
				'content'=>$_html_TD,
				'slug'=>'ev_timedate'
			),array(
				'id'=>'ev_location',
				'name'=>__('Location and Venue','eventon'),	
				'hiddenVal'=>$__hiddenVAL_LOC,	
				'iconURL'=>'',
				'iconPOS'=>'0 -225px',
				'type'=>'code',
				'content'=>$_html_LOC,
				'slug'=>'ev_location',
				'guide'=>''
			),array(
				'id'=>'ev_organizer',
				'name'=>__('Organizer','eventon'),	
				'hiddenVal'=>$__hiddenVAL_OR,	
				'iconURL'=>'',
				'iconPOS'=>'0 -31px',
				'type'=>'code',
				'content'=>$_html_OR,
				'slug'=>'ev_organizer'
			),array(
				'id'=>'ev_uint',
				'name'=>__('User Interaction for event click','eventon'),	
				'hiddenVal'=>$__hiddenVAL_UIN,	
				'iconURL'=>'',
				'iconPOS'=>'0 -262px',
				'type'=>'code',
				'content'=>$_html_UIN,
				'slug'=>'ev_uint',
				'guide'=>'This define how you want the events to expand following a click on the eventTop by a user'
			),array(
				'id'=>'ev_learnmore',
				'name'=>__('Learn more about event link','eventon'),	
				'hiddenVal'=>$__hiddenVAL_LM,	
				'iconURL'=>'',
				'iconPOS'=>'0 -96px',
				'type'=>'code',
				'content'=>$_html_LM,
				'slug'=>'ev_learnmore',
				'guide'=>'This will create a learn more link in the event card'
			)
		));
	
	// combine array with custom fields
	$metabox_array = (!empty($evMB_custom) && count($evMB_custom)>0)? array_merge($metabox_array , $evMB_custom): $metabox_array;
	
	$closedmeta = eventon_get_collapse_metaboxes($p_id);
	
	//print_r($closedmeta);
?>
	
	
	<div id='evo_mb' class='eventon_mb'>
		<input type='hidden' id='evo_collapse_meta_boxes' name='evo_collapse_meta_boxes' value=''/>
	<?php
		foreach($metabox_array as $mBOX):
			
			// ICONS
			$icon_style = (!empty($mBOX['iconURL']))?
				'background-image:url('.$mBOX['iconURL'].')'
				:'background-position:'.$mBOX['iconPOS'];
			$icon_class = (!empty($mBOX['iconPOS']))? 'evIcons':'evII';
			
			$guide = (!empty($mBOX['guide']))? 
				$eventon->throw_guide($mBOX['guide'], '',false):null;
			
			$hiddenVal = (!empty($mBOX['hiddenVal']))?
				'<span class="hiddenVal">'.$mBOX['hiddenVal'].'</span>':null;
			
			$closed = (!empty($closedmeta) && in_array($mBOX['id'], $closedmeta))? 'closed':null;
	?>	
	
		<div class='evomb_section' id='<?php echo $mBOX['id'];?>'>			
			<div class='evomb_header'>
				<?php // custom field with icons
					if(!empty($mBOX['variation']) && $mBOX['variation']	=='customfield'):?>	
					<span class='evomb_icon <?php echo $icon_class;?>'><i class='fa <?php echo $mBOX['iconURL']; ?>'></i></span>
					
				<?php else:
					
				?>
					<span class='evomb_icon <?php echo $icon_class;?>' style='<?php echo $icon_style?>'></span>
				<?php endif; ?>
				<p><?php echo $mBOX['name'];?><?php echo $hiddenVal;?><?php echo $guide;?></p>
			</div>
			<div class='evomb_body <?php echo $closed;?>' box_id='<?php echo $mBOX['id'];?>'>
				<?php echo $mBOX['content'];?>
			</div>
		</div>
	<?php
		endforeach;
	?>
		<div class='evMB_end'></div>
	</div>

	
	
	
	

<?php }
	
/*	THIRD PARTY event related settings */
	function ajde_evcal_show_box_3(){	
		
		global $eventon;
		
		$evcal_opt1= get_option('evcal_options_evcal_1');
			$evcal_opt2= get_option('evcal_options_evcal_2');
			
			// Use nonce for verification
			wp_nonce_field( plugin_basename( __FILE__ ), 'evo_noncename' );
			
			// The actual fields for data entry
			$p_id = get_the_ID();
			$ev_vals = get_post_custom($p_id);
		
		?>
		<table id="meta_tb" class="form-table meta_tb evoThirdparty_meta" >
			<?php
				// (---) hook for addons
				if(has_action('eventon_post_settings_metabox_table'))
					do_action('eventon_post_settings_metabox_table');
			?>
			
			<?php
				// (---) hook for addons
				if(has_action('eventon_post_time_settings'))
					do_action('eventon_post_time_settings');
			?>
			
			<?php 
			// Event brite
			if($evcal_opt1['evcal_evb_events']=='yes'
				&& !empty($evcal_opt1['evcal_evb_api']) ):?>
				
				<tr>
					<td colspan='2'>
					<div class='evcal_data_block_style1'>
						<p class='edb_icon'><img src='<?php echo AJDE_EVCAL_URL ?>/assets/images/backend_post/eventbrite_icon.png'/></p>
						
						<p class='evcal_db_data'>
						<?php
							if(!empty($ev_vals["evcal_evb_id"]) ){
								echo "<span id='evcal_eb5'>Currently Connected to <b id='evcal_eb2'>".$ev_vals["evcal_evb_id"][0]."</b><br/></span>";
								$html_eb2 = "  <input type='button' class='evo_admin_btn btn_prime' id='evcal_eventb_btn_dis' value='Disconnect this'/>";
							}else{
								$html_eb2='';
							}
							$html_eb1 = 'Connect to Eventbrite Event';
						?>	
						<input type='button' class='evo_admin_btn btn_prime' id='evcal_eventb_btn' value='<?php echo $html_eb1?>'/><?php echo $html_eb2?></p>
						
						<input type='hidden' name='evcal_evb_id' id='evcal_eventb_ev_d2' value='<?php echo (!empty($ev_vals["evcal_evb_id"]))? $ev_vals["evcal_evb_id"][0]: null; ?>'/>
						<input type='hidden' name='evcal_eventb_data_set' id='evcal_eventb_ev_d1' value='<?php echo (!empty($ev_vals["evcal_eventb_data_set"]))? $ev_vals["evcal_eventb_data_set"][0]: null; ?>'/>
					</div>	
					</td>
				</tr>
				<?php
					// URL
					$display = (!empty($ev_vals["evcal_eventb_url"]) )? '':'none';
				?>
				<tr class='divide evcal_eb_url evcal_eb_r' style='display:<?php echo $display ?>'>
					<td colspan='2'><p class='div_bar div_bar_sm'></p></td></tr>
				<tr class='evcal_eb_url evcal_eb_r' style='display:<?php echo $display?>'>
					<td colspan='2'>
						<p style='margin-bottom:2px'>Eventbrite Buy Ticket URL</p>
						<input style='width:100%' id='evcal_ebv_url' type='text' name='evcal_eventb_url' value='<?php echo (!empty($ev_vals["evcal_eventb_url"]))? $ev_vals["evcal_eventb_url"][0]: null; ?>' />
					</td>
				</tr>
				<?php
					// CAPACITY
					$display = (!empty($ev_vals["evcal_eventb_capacity"]) )? '':'none';
				?>
				<tr class='divide evcal_eb_capacity evcal_eb_r' style='display:<?php echo $display ?>'>
					<td colspan='2'><p class='div_bar div_bar_sm'></p></td></tr>
				<tr class='evcal_eb_capacity evcal_eb_r' style='display:<?php echo $display?>'>
					<td colspan='2'>
						<?php $evcal_eventb_capacity = (!empty($ev_vals["evcal_eventb_capacity"]))? $ev_vals["evcal_eventb_capacity"][0]: null; ?>
						<p style='margin-bottom:2px'>Eventbrite Event Capacity: <b id='evcal_eb3'><?php echo $evcal_eventb_capacity?></b></p>
						<input id='evcal_ebv_capacity' type='hidden' name='evcal_eventb_capacity' value='<?php echo $evcal_eventb_capacity?>' />
					</td>
				</tr>
				<?php
					// TICKET PRICE
					$display = (!empty($ev_vals["evcal_eventb_tprice"]) )? '':'none';
				?>
				<tr class='divide evcal_eb_price evcal_eb_r' style='display:<?php echo $display ?>'>
					<td colspan='2'><p class='div_bar div_bar_sm'></p></td></tr>
				<tr class='evcal_eb_price evcal_eb_r' style='display:<?php echo $display?>'>
					<td colspan='2'>
						<?php $evcal_eventb_tprice = (!empty($ev_vals["evcal_eventb_tprice"]))? $ev_vals["evcal_eventb_tprice"][0]: null; ?>
						<p style='margin-bottom:2px'>Eventbrite Ticket Price: <b id='evcal_eb4'><?php echo $evcal_eventb_tprice?></b></p>
						<input id='evcal_ebv_price' type='hidden' name='evcal_eventb_tprice' value='<?php echo $evcal_eventb_tprice?>' />
					</td>
				</tr>
				
				<tr id='evcal_eventb_data' style='display:none'><td colspan='2'>
					<div class='evcal_row_dark' >
						<p id='evcal_eventb_msg' class='event_api_msg' style='display:none'>Message</p>
						<div class='col50'>
							<p><input type='text' id='evcal_eventb_ev_id' value='' style='width:100%'/></p>
							<p class='legend_mf'>Enter Eventbrite Event ID</p>
						</div>
						<div class='col50'>
							<div class='padl20'>
								<p><input id='evcal_eventb_btn_2' style='margin-left:10px'type='button' class='evo_admin_btn btn_prime' value='Get Event Data from Eventbrite'/></p>
							</div>
						</div>			
						
						<p class='clear'></p>					
						<p class='divider'></p>					
						<div id='evcal_eventb_s1' style='display:none'>
							<h5 class='mu_ev_id'>Retrived Event Data for: <b id='evcal_eb1'>321786</b></h5>
							<p class='legend_mf'>Click on each eventbrite event data section to connect to this event.</p>
							
							<div id='evcal_eventb_data_tb'></div>
						</div>
					</div>
				</td></tr>
			
			
			<?php endif;?>
			
			<?php 
				// MEETUP
				
				if($evcal_opt1['evcal_api_meetup']=='yes' 
					&& !empty($evcal_opt1['evcal_api_mu_key']) ):
			?>
				<tr class='divide'><td colspan='2'><p class='div_bar div_bar_sm '></p></td></tr>
				<tr>
					<td colspan='2'>
					<div class='evcal_data_block_style1'>
						<p class='edb_icon'><img src='<?php echo AJDE_EVCAL_URL ?>/assets/images/backend_post/meetup_icon.png'/></p>
						
						<p class='evcal_db_data'>
						<?php
							if(!empty($ev_vals["evcal_meetup_ev_id"]) ){
								echo "<span id='evcal_mu2'>Currently Connected to <b id='evcal_002'>".$ev_vals["evcal_meetup_ev_id"][0]."</b><br/></span>";
								$html_mu2 = "  <input type='button' class='button' id='evcal_meetup_btn_dis' value='Disconnect this'/>";
							}else{
								$html_mu2 ='';
							}
							$html_mu1 = 'Connect to Meetup Event';
						?>	
						<input type='button' class='button' id='evcal_meetup_btn' value='<?php echo $html_mu1?>'/><?php echo $html_mu2?></p>
						
						<input type='hidden' name='evcal_meetup_data_set' id='evcal_meetup_ev_d1' value='<?php echo (!empty($ev_vals["evcal_meetup_data_set"]))? $ev_vals["evcal_meetup_data_set"][0]: null; ?>'/>
						<input type='hidden' name='evcal_meetup_ev_id' id='evcal_meetup_ev_d2' value='<?php echo (!empty($ev_vals["evcal_meetup_ev_id"]))? $ev_vals["evcal_meetup_ev_id"][0]: null; ?>'/>
					</div>	
					</td>
				</tr>
				
				
				<tr id='evcal_meetup_data' style='display:none'><td colspan='2'>
					<div class='evcal_row_dark' >
						<p id='evcal_meetup_msg' class='event_api_msg' style='display:none'>Message</p>
						<div class='col50'>
							<p><input type='text' id='evcal_meetup_ev_id' value='' style='width:100%'/></p>
							<p class='legend_mf'>Enter Meetup Event ID</p>
						</div>
						<div class='col50'>
							<div class='padl20'>
								<p><input id='evcal_meetup_btn_2' style='margin-left:10px'type='button' class='button' value='Get Event Data from Meetup'/></p>
							</div>
						</div>			
						
						<p class='clear'></p>					
						<p class='divider'></p>					
						<div id='evcal_meetup_s1' style='display:none'>
							<h5 class='mu_ev_id'>Retrived Event Data for: <b id='evcal_001'>321786</b></h5>
							<p class='legend_mf'>Click on each meetup event data section to populate this event with meetup event information.</p>						
							<div id='evcal_meetup_data_tb'></div>
						</div>
					</div>
				</td></tr>
			<?php endif; ?>
			
			<?php
				// PAYPAL
				if($evcal_opt1['evcal_paypal_pay']=='yes'):
			?>
			<tr>
				<td colspan='2' class='evo_thirdparty_table_td'>
					<div class='evo_thirdparty_section_header evcal_data_block_style1'>
						<p class='edb_icon'><img src='<?php echo AJDE_EVCAL_URL ?>/assets/images/backend_post/evcal_pp.png'/></p>
						<p class='evcal_db_data'>Paypal Buy Now button</p>
					</div>	
					<p class='evo_thirdparty'><label for='evcal_paypal_text'><?php _e('Text to show above buy now button')?></label><br/>			
						<input type='text' id='evcal_paypal_text' name='evcal_paypal_text' value='<?php echo (!empty($ev_vals["evcal_paypal_text"]) )? $ev_vals["evcal_paypal_text"][0]:null?>' style='width:100%'/>
					</p>
					<p class='evo_thirdparty'><label for='evcal_paypal_item_price'><?php _e('Enter the price for paypal buy now button <i>eg. 23.99</i>')?><?php $eventon->throw_guide('Type the price without currency symbol to create a buy now button for this event. This will show on front-end calendar for this event');?></label><br/>			
						<input placeholder='eg. 29.99' type='text' id='evcal_paypal_item_price' name='evcal_paypal_item_price' value='<?php echo (!empty($ev_vals["evcal_paypal_item_price"]) )? $ev_vals["evcal_paypal_item_price"][0]:null?>' style='width:100%'/>
					</p>			
				</td>			
			</tr>
			<?php endif;?>
			</table>
		<?php

	}
	
	
/** Save the Event data meta box. **/
	function eventon_save_meta_data($post_id, $post){
		if($post->post_type!='ajde_events')
			return;
			
		// Stop WP from clearing custom fields on autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
			return;

		// Prevent quick edit from clearing custom fields
		if (defined('DOING_AJAX') && DOING_AJAX)
			return;

		
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if( isset($_POST['evo_noncename']) ){
			if ( !wp_verify_nonce( $_POST['evo_noncename'], plugin_basename( __FILE__ ) ) ){
				return;
			}
		}
		// Check permissions
		if ( !current_user_can( 'edit_post', $post_id ) )
			return;	

		global $pagenow;
		$_allowed = array( 'post-new.php', 'post.php' );
		if(!in_array($pagenow, $_allowed)) return;
		
		
		//save the post meta values
		$fields_ar =apply_filters('eventon_event_metafields', array(
			'evcal_allday','evcal_event_color','evcal_event_color_n',
			'evcal_location','evcal_location_name','evcal_organizer','evcal_exlink','evcal_lmlink',
			'evcal_gmap_gen','evcal_mu_id','evcal_paypal_item_price','evcal_paypal_text',
			'evcal_eventb_data_set','evcal_evb_id','evcal_eventb_url','evcal_eventb_capacity','evcal_eventb_tprice',
			'evcal_meetup_data_set','evcal_meetup_url','evcal_meetup_ev_id',
			'evcal_repeat','evcal_rep_freq','evcal_rep_gap','evcal_rep_num',
			'evp_repeat_rb','evo_repeat_wom','evo_rep_WK',
			'evcal_lmlink_target','_evcal_exlink_target','_evcal_exlink_option',
			'evo_hide_endtime',

			'evo_exclude_ev',
			
			'_evcal_ec_f1a1_cus','_evcal_ec_f2a1_cus','_evcal_ec_f3a1_cus',
			'_evcal_ec_f1a1_cusL','_evcal_ec_f2a1_cusL','_evcal_ec_f3a1_cusL',
			'_evcal_ec_f1_onw','_evcal_ec_f2_onw','_evcal_ec_f3_onw',
			'evcal_lat','evcal_lon',
		));
		
		
		
		// field names that pertains only to event date information
		$fields_sub_ar = apply_filters('eventon_event_date_metafields', array(
			'evcal_start_date','evcal_end_date', 'evcal_start_time_hour','evcal_start_time_min','evcal_st_ampm',
			'evcal_end_time_hour','evcal_end_time_min','evcal_et_ampm'
			)
		);
		

		
		// DATE and TIME data
		$date_POST_values='';
		foreach($fields_sub_ar as $ff){
			
			// end date value fix for -- hide end date
			if($ff=='evcal_end_date' && !empty($_POST['evo_hide_endtime']) && $_POST['evo_hide_endtime']=='yes'){
				$date_POST_values['evcal_end_date']=$_POST['evcal_start_date'];
			}else{
				if(!empty($_POST[$ff]))
					$date_POST_values[$ff]=$_POST[$ff];
			}
				
			
			// remove these values from previously saved
			delete_post_meta($post_id, $ff);
		}
		
		// convert the post times into proper unix time stamps
		if(!empty($_POST['_evo_date_format']) && !empty($_POST['_evo_time_format']))
			$proper_time = eventon_get_unix_time($date_POST_values, $_POST['_evo_date_format'], $_POST['_evo_time_format']);
		

		// if Repeating event save repeating intervals
			if( eventon_is_good_repeat_data() && !empty($proper_time['unix_start']) ){

				$unix_E = (!empty($proper_time['unix_end']))? $proper_time['unix_end']: $proper_time['unix_start'];
				$repeat_intervals = eventon_get_repeat_intervals($proper_time['unix_start'], $unix_E);

				// save repeat interval array as post meta
				if ( !empty($repeat_intervals) )
					update_post_meta( $post_id, 'repeat_intervals', $repeat_intervals);

			}


		// run through all the custom meta fields
			foreach($fields_ar as $f_val){
				
				if(!empty ($_POST[$f_val])){
					
					$post_value = ( $_POST[$f_val]);
					update_post_meta( $post_id, $f_val,$post_value);		
					
				}else{
					if(defined('DOING_AUTOSAVE') && !DOING_AUTOSAVE){
						// if the meta value is set to empty, then delete that meta value
						delete_post_meta($post_id, $f_val);
					}
					delete_post_meta($post_id, $f_val);
				}
				
			}
			
		
		// full time converted to unix time stamp
		if ( !empty($proper_time['unix_start']) )
			update_post_meta( $post_id, 'evcal_srow', $proper_time['unix_start']);
		
		if ( !empty($proper_time['unix_end']) )
			update_post_meta( $post_id, 'evcal_erow', $proper_time['unix_end']);


		
		
		//set event color code to 1 for none select colors
		if ( !isset( $_POST['evcal_event_color_n'] ) )
			update_post_meta( $post_id, 'evcal_event_color_n',1);
			
			
		// save featured event data default value no
		$_featured = get_post_meta($post_id, '_featured',true);
		if(empty( $_featured) )
			update_post_meta( $post_id, '_featured','no');
		
		
		
		// save event location as taxonomy
		if( isset($_POST['evcal_location']) && isset($_POST['evcal_location_name'])){

			$term_name = esc_attr($_POST['evcal_location_name']);

			$term_exist = term_exists($term_name, 'event_location');

			
			$location_address = esc_attr($_POST['evcal_location']);
			$term_slug = str_replace(" ", "-", $_POST['evcal_location_name']);
			
			wp_insert_term( $term_name, 'event_location', array(
				'slug'=>$term_slug
			) );

			//$t_id = get_cat_id($term_name);
			$new_term = get_term_by('slug',$term_slug, 'event_location' );

			if($new_term){
				// save location address
				$term_meta = array();
				$term_meta['location_address'] = $location_address;
				
				if(isset($_POST['evcal_lon']) )
					$term_meta['location_lon'] = $_POST['evcal_lon'];
				if(isset($_POST['evcal_lat']) )
					$term_meta['location_lat'] = $_POST['evcal_lat'];

				update_option( "taxonomy_".$new_term->term_id, $term_meta );
			}

		}

		
		// (---) hook for addons
		do_action('eventon_save_meta', $fields_ar, $post_id);


		// save user closed meta field boxes
		if(!empty($_POST['evo_collapse_meta_boxes']))
			eventon_save_collapse_metaboxes($post_id, $_POST['evo_collapse_meta_boxes'],true );
			
	}
	
	
	

?>