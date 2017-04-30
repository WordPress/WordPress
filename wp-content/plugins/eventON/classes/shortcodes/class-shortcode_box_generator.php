<?php
/**
 * EventON Admin Include
 *
 * Include for EventON related events in admin.
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Admin
 * @version     0.1
 */



class eventon_admin_shortcode_box{
	
	private $_in_select_step=false;
	private $evopt;

	function __construct(){
		$this->evopt =  get_option('evcal_options_evcal_1');
	}
	
	public function shortcode_default_field($key){

		$options_1 = $this->evopt;

		// event type 3
			 if(!empty($options_1['evcal_ett_3']) && $options_1['evcal_ett_3']=='yes' && !empty($options_1['evcal_eventt3'])){
			 	$__event_tt3 = array(
					'name'=>'Event Type 3',
					'type'=>'eventtype',
					'guide'=>'Event Type 3 category IDs - seperate by commas (eg. 3,12)',
					'placeholder'=>'eg. 3, 12',
					'var'=>'event_type_3',
					'default'=>'0'
				);
			 }else{ $__event_tt3 = array(); }

		
		$SC_defaults = array(
			'cal_id'=>array(
				'name'=>'Calendar ID',
				'type'=>'text',
				'var'=>'cal_id',
				'guide'=>'Unique ID to differentiate this calendar from others',
				'default'=>'0',
				'placeholder'=>'eg. 1'
			),
			'number_of_months'=>array(
				'name'=>'Number of Months',
				'type'=>'text',
				'var'=>'number_of_months',
				'default'=>'0',
				'placeholder'=>'eg. 5'
			),		
			'show_et_ft_img'=>array(
				'name'=>'Show Featured Image',
				'type'=>'YN',
				'var'=>'show_et_ft_img',
				'default'=>'no'
			),
			'hide_past'=>array(
				'name'=>'Hide Past Events',
				'type'=>'YN',
				'var'=>'hide_past',
				'default'=>'no'
			),'hide_past_by'=>array(
				'name'=>'Hide Past Events by',
				'guide'=>'You can choose which date (start or end) to use to decide when to clasify them as past events.',
				'type'=>'select',
				'var'=>'hide_past_by',
				'default'=>'ee',
				'options'=>array( 
					'ss'=>'Start Date/time',
					'ee'=>'End Date/Time',
				)
			),
			'ft_event_priority'=>array(
				'name'=>'Feature event priority',
				'type'=>'YN',
				'guide'=>'Move featured events above others',
				'var'=>'ft_event_priority',
				'default'=>'no',
			),
			'event_count'=>array(
				'name'=>'Event count limit',
				'placeholder'=>'eg. 3',
				'type'=>'text',
				'guide'=>'Limit number of events per month (integer) eg. 3',
				'var'=>'event_count',
				'default'=>'0'
			),
			'month_incre'=>array(
				'name'=>'Month Increment',
				'type'=>'text',
				'placeholder'=>'eg. +1',
				'guide'=>'Change starting month (eg. +1)',
				'var'=>'month_incre',
				'default'=>'0'
			),
			'event_type'=>array(
				'name'=>'Event Type',
				'type'=>'eventtype',
				'guide'=>'Event Type category IDs - seperate by commas (eg. 3,12)',
				'placeholder'=>'eg. 3, 12',
				'var'=>'event_type',
				'default'=>'0'
			),'event_type_2'=>array(
				'name'=>'Event Type 2',
				'type'=>'eventtype',
				'guide'=>'Event Type 2 category IDs - seperate by commas (eg. 3,12)',
				'placeholder'=>'eg. 3, 12',
				'var'=>'event_type_2',
				'default'=>'0'
			),'event_type_3'=>$__event_tt3,
			'fixed_month'=>array(
				'name'=>'Fixed Month',
				'type'=>'text',
				'guide'=>'Set fixed month for calendar start (integer)',
				'var'=>'fixed_month',
				'default'=>'0',
				'placeholder'=>'eg. 10'
			),
			'fixed_year'=>array(
				'name'=>'Fixed Year',
				'type'=>'text',
				'guide'=>'Set fixed year for calendar start (integer)',
				'var'=>'fixed_year',
				'default'=>'0',
				'placeholder'=>'eg. 2013'
			),
			'event_order'=>array(
				'name'=>'Event Order',
				'type'=>'select',
				'guide'=>'Select ascending or descending order for event. By default it will be Ascending order.',
				'var'=>'event_order',
				'default'=>'ASC',
				'options'=>array('ASC'=>'ASC','DESC'=>'DESC')
			),
			'pec'=>array(
				'name'=>'Event Cut-off',
				'type'=>'select',
				'guide'=>'Past or upcoming events cut-off time. This will allow you to override past event cut-off settings for calendar events. Current date = today at 12:00am',
				'var'=>'pec',
				'default'=>'Current Time',
				'options'=>array( 
					'ct'=>'Current Time: '.date('m/j/Y g:i a', current_time('timestamp')),
					'cd'=>'Current Date: '.date('m/j/Y', current_time('timestamp')),
				)
			),
			'lang'=>array(
				'name'=>'Language Variation (<a href="'.get_admin_url().'admin.php?page=eventon&tab=evcal_2">Update Language Text</a>)',
				'type'=>'select',
				'guide'=>'Select which language variation text to use',
				'var'=>'lang',
				'default'=>'L1',
				'options'=>array('L1'=>'L1','L2'=>'L2','L3'=>'L3')
			),'hide_mult_occur'=>array(
				'name'=>'Hide multiple occurence',
				'type'=>'YN',
				'guide'=>'Hide events from showing more than once between months',
				'var'=>'hide_mult_occur',
				'default'=>'no',
			),'fixed_mo_yr'=>array(
				'name'=>'Fixed Month/Year',
				'type'=>'fmy',
				'guide'=>'Set fixed month and year value (Both values required)(integer)',
				'var'=>'fixed_my',
			),'fixed_d_m_y'=>array(
				'name'=>'Fixed Date/Month/Year',
				'type'=>'fdmy',
				'guide'=>'Set fixed date, month and year value (All values required)(integer)',
				'var'=>'fixed_my',
			),'evc_open'=>array(
				'name'=>'Open eventCards on load',
				'type'=>'YN',
				'guide'=>'Open eventCards when the calendar first load on the page by default. This will override the settings saved for default calendar.',
				'var'=>'evc_open',
				'default'=>'no',
			),'UIX'=>array(
				'name'=>'User Interaction',
				'type'=>'select',
				'guide'=>'Select the user interaction option to override individual event user interactions',
				'var'=>'ux_val',
				'default'=>'0',
				'options'=>apply_filters('eventon_uix_shortcode_opts', array('0'=>'None','X'=>'Do not interact','1'=>'Slide Down EventCard','3'=>'Lightbox popup window'))
			),'etc_override'=>array(
				'name'=>'Event type color override',
				'type'=>'YN',
				'guide'=>'Select this option to override event colors with event type colors, if they exists',
				'var'=>'etc_override',
				'default'=>'no',
			),'only_ft'=>array(
				'name'=>'Show only featured events',
				'type'=>'YN',
				'guide'=>'Display only featured events in the calendar',
				'var'=>'only_ft',
				'default'=>'no',
			),'jumper'=>array(
				'name'=>'Show jump months option',
				'type'=>'YN',
				'guide'=>'Display month jumper on the calendar',
				'var'=>'jumper',
				'default'=>'no',
			),'accord'=>array(
				'name'=>'Accordion effect on eventcards','type'=>'YN',
				'guide'=>'This will close open events when new one clicked','var'=>'accord','default'=>'no',
			),'sort_by'=>array(
				'name'=>'Default Sort by',
				'type'=>'select',
				'guide'=>'Sort calendar events by on load',
				'var'=>'sort_by',
				'default'=>'sort_date',
				'options'=>array( 
					'sort_date'=>'Date','sort_title'=>'Title',
				)
			),'expand_sortO'=>array(
				'name'=>'Expand sort options by default',
				'type'=>'YN',
				'guide'=>'This will expand sort options section on load for calendar.',
				'var'=>'exp_so',
				'default'=>'no',
			),'rtl'=>array(
				'name'=>'Right-to-left text on calendar',
				'type'=>'YN',
				'guide'=>'This will change text alignment to Right-to-left for languages like Arabic.',
				'var'=>'rtl',
				'default'=>'no',
			)

		);
		
		return $SC_defaults[$key];
	
	}
	
	// INTERPRET shortcode from array
	public function shortcode_interpret($var){
		global $eventon;
		$line_class = array('fieldline');

		ob_start();		
		
		// GUIDE popup
		$guide = (!empty($var['guide']))? $eventon->throw_guide($var['guide'], 'L',false):null;

		// afterstatemnt class
		if(!empty($var['afterstatement'])){	$line_class[]='trig_afterst'; }

		// select step class
		if($this->_in_select_step){ $line_class[]='ss_in'; }


		if(!empty($var['type'])):

		switch($var['type']){
			// custom type and its html pluggability
			case has_action("eventon_shortcode_box_interpret_{$var['type']}"):
				do_action("eventon_shortcode_box_interpret_{$var['type']}");
			
			case 'YN':
				$line_class[]='evoYN_row';
				echo 
				"<div class='".implode(' ', $line_class)."'>
					<p class='label'><a class='evo_YN_btn ".( ($var['default']=='no')? 'NO':null )."' codevar='".$var['var']."'></a>
					<span >".$var['name'].
					"</span>".$guide."</p>							
				</div>";
			break;
			
			case 'text':
				echo 
				"<div class='".implode(' ', $line_class)."'>
					<p class='label'><input class='evoPOSH_input' type='text' codevar='".$var['var']."' placeholder='".( (!empty($var['placeholder']))?$var['placeholder']:null) ."'/> ".$var['name']."".$guide."</p>
				</div>";
			break;

			case 'fmy':
				$line_class[]='fmy';
				echo 
				"<div class='".implode(' ', $line_class)."'>
					<p class='label'>
						<input class='evoPOSH_input short' type='text' codevar='fixed_month' placeholder='eg. 11' title='Month'/><input class='evoPOSH_input short' type='text' codevar='fixed_year' placeholder='eg. 2014' title='Year'/> ".$var['name']."".$guide."</p>
				</div>";
			break;
			case 'fdmy':
				$line_class[]='fdmy';
				echo 
				"<div class='".implode(' ', $line_class)."'>
					<p class='label'>
						<input class='evoPOSH_input short shorter' type='text' codevar='fixed_date' placeholder='eg. 31' title='Date'/><input class='evoPOSH_input short shorter' type='text' codevar='fixed_month' placeholder='eg. 11' title='Month'/><input class='evoPOSH_input short shorter' type='text' codevar='fixed_year' placeholder='eg. 2014' title='Year'/> ".$var['name']."".$guide."</p>
				</div>";
			break;
			
			case 'eventtype':
				
				$terms = get_terms($var['var']);	
				
				$view ='';
				if(!empty($terms) && count($terms)>0){
					foreach($terms as $term){
						$view.= '<em>'.$term->name .' ('.$term->term_id.')</em>';
					}
				}
				
				$view_html = (!empty($view))? '<span class="evoPOSH_tax">Possible Values <span >'. $view .'</span></span>': null;
				
				
				echo 
				"<div class='".implode(' ', $line_class)."'>
					<p class='label'><input class='evoPOSH_input' type='text' codevar='".$var['var']."' placeholder='".( (!empty($var['placeholder']))?$var['placeholder']:null) ."'/> ".$var['name']." {$view_html}</p>
				</div>";
			break;
			
			case 'select':
				echo 
				"<div class='".implode(' ', $line_class)."'>
					<p class='label'>
						<select class='evoPOSH_select' codevar='".$var['var']."'>";
						$default = (!empty($var['default']))? $var['default']: null;
						foreach($var['options'] as $valf=>$val){
							echo "<option value='".$valf."' ".( $default==$valf? 'selected="selected"':null).">".$val."</option>";
						}		
						
						echo 
						"</select> ".$var['name']."".$guide."</p>
				</div>";
			break;

			// select steps
			case 'select_step':
				$line_class[]='select_step_line';
				echo 
				"<div class='".implode(' ', $line_class)."'>
					<p class='label '>
						<select class='evoPOSH_select_step' data-codevar='".$var['var']."'>";
						
						foreach($var['options'] as $f=>$val){
							echo (!empty($val))? "<option value='".$f."'>".$val."</option>":null;
						}		
						echo 
						"</select> ".__($var['name'],'eventon')."".$guide."</p>
				</div>";
			break;

			case 'open_select_steps':
				echo "<div id='".$var['id']."' class='evo_open_ss' style='display:none' data-step='".$var['id']."' >";
				$this->_in_select_step=true;	// set select step section to on
			break;

			case 'close_select_step':	echo "</div>";	$this->_in_select_step=false; break;
			
		}// end switch

		endif;

		// afterstatement
		if(!empty($var['afterstatement'])){
			echo "<div class='evo_afterst ".$var['afterstatement']."' style='display:none'>";
		}

		// closestatement
		if(!empty($var['closestatement'])){
			echo "</div>";
		}
		
		return ob_get_clean();
	}
	
	public function get_shortcode_field_array(){
		
		$shortcode_guide_array = apply_filters('eventon_shortcode_popup', array(
			array(
				'id'=>'s1',
				'name'=>'Main Calendar',
				'code'=>'add_eventon',
				'variables'=>apply_filters('eventon_basiccal_shortcodebox', array(
					$this->shortcode_default_field('show_et_ft_img')
					,$this->shortcode_default_field('ft_event_priority')
					,$this->shortcode_default_field('only_ft')
					,$this->shortcode_default_field('hide_past')	
					,$this->shortcode_default_field('hide_past_by')	
					,$this->shortcode_default_field('sort_by')
					,$this->shortcode_default_field('event_count')
					,$this->shortcode_default_field('month_incre')
					,$this->shortcode_default_field('event_type')
					,$this->shortcode_default_field('event_type_2')
					,$this->shortcode_default_field('event_type_3')
					,$this->shortcode_default_field('etc_override')
					,$this->shortcode_default_field('fixed_mo_yr')
					,$this->shortcode_default_field('cal_id')
					,$this->shortcode_default_field('event_order')
					,$this->shortcode_default_field('lang')
					,$this->shortcode_default_field('UIX')
					,$this->shortcode_default_field('evc_open')					
					,$this->shortcode_default_field('jumper')
					,$this->shortcode_default_field('expand_sortO')
					,$this->shortcode_default_field('accord')
					,$this->shortcode_default_field('rtl')
				))
			),
			array(
				'id'=>'s2',
				'name'=>'Events List',
				'code'=>'add_eventon_list',
				'variables'=>array(
					$this->shortcode_default_field('number_of_months')
					,array(
						'name'=>'Event count limit',
						'placeholder'=>'eg. 3',
						'type'=>'text',
						'guide'=>'Limit number of events per month (integer)',
						'var'=>'event_count',
						'default'=>'0'
					),$this->shortcode_default_field('month_incre')
					,$this->shortcode_default_field('fixed_mo_yr')
					,$this->shortcode_default_field('cal_id')
					,$this->shortcode_default_field('event_order')
					,$this->shortcode_default_field('hide_past')
					,$this->shortcode_default_field('hide_mult_occur'),
					array(
						'name'=>'Hide empty months',
						'type'=>'YN',
						'guide'=>'Hide months without any events on the events list',
						'var'=>'hide_empty_months',
						'default'=>'no',
					),array(
						'name'=>'Show year',
						'type'=>'YN',
						'guide'=>'Show year next to month name on the events list',
						'var'=>'show_year',
						'default'=>'no',
					),$this->shortcode_default_field('ft_event_priority'),
					$this->shortcode_default_field('only_ft'),
					$this->shortcode_default_field('etc_override'),
					$this->shortcode_default_field('accord'),
					
				)
			)
		));
		
		return $shortcode_guide_array;
	}
	
	public function get_content(){
		
	$shortcode_guide_array = $this->get_shortcode_field_array();
	
	$__text_a = __('Select option below to customize shortcode variable values', 'eventon');

	ob_start();

	?>
		
		<div id='evoPOSH_outter'>
			<h3 class='notifications '><em id='evoPOSH_back'></em><span id='evoPOSH_subtitle' data-section='' data-bf='<?php echo $__text_a;?>'><?php echo $__text_a;?></span></h3>
			<div class='evoPOSH_inner'>
				<div class='step1 steps'>
				<?php					
					foreach($shortcode_guide_array as $options){
						$__step_2 = (empty($options['variables']))? ' nostep':null;
						
						echo "<div class='evoPOSH_btn{$__step_2}' step2='".$options['id']."' code='".$options['code']."'>".$options['name']."</div>";
					}	
				?>				
				</div>
				<div class='step2 steps' >
					<?php
						foreach($shortcode_guide_array as $options){
							
							if(!empty($options['variables']) ) {
							
								echo "<div id='".$options['id']."' class='step2_in' style='display:none'>";
								
								// each shortcode option variable row
								foreach($options['variables'] as $var){

									if($var == 'event_type_3' && is_array($var) && count($var)>0){
										$options_1 = $this->evopt;

										// event type 3
										if(!empty($options_1['evcal_ett_3']) && $options_1['evcal_ett_3']=='yes' && !empty($options_1['evcal_eventt3'])){
											echo $this->shortcode_interpret($var);
										}
									}else{
										echo $this->shortcode_interpret($var);
									}
								}									
								
								echo "</div>";
							}
						}
						
					?>
					
				</div>
				<div class='clear'></div>
			</div>
			<div class='evoPOSH_footer'>
				<p id='evoPOSH_var_'></p>
				<p id='evoPOSH_code' data-defsc='add_eventon' data-curcode='add_eventon' code='add_eventon' >[add_eventon]</p>
				<span class='evoPOSH_insert' title='Click to insert shortcode'></span>
			</div>
		</div>
	
	<?php
	return ob_get_clean();
	
	}

}

$GLOBALS['evo_shortcode_box'] = new eventon_admin_shortcode_box();


?>