<?php
/**
 * EventON Widget
 *
 * @author 		AJDE
 * @category 	Widget
 * @package 	EventON/Classes
 * @version     1.3
 */
class EvcalWidget extends WP_Widget{
	
	function EvcalWidget(){
		$widget_ops = array('classname' => 'EvcalWidget', 
			'description' => 'EventON basic or upcoming list Event Calendar widget.' );
		$this->WP_Widget('EvcalWidget', 'eventON Calendar', $widget_ops);
	}
	
	function widget_default(){
		return $defaults = array(
			'ev_cal_id'=>'',
			'ev_count'=>'0',
			'ev_type'=>'all',
			'ev_type_2'=>'all',
			'ev_title'=>'',
			'show_upcoming'=>'0',
			'ev_hidepastev'=>'no',
			'hide_mult_occur'=>'no',
			'_is_fixed_time'=>'no',
			'fixed_month'=>'0',
			'fixed_year'=>'0',
			'hide_empty_months'=>'no',
			'number_of_months'=>'1',
			'lang'=>'L1'
		);
	}
	function widget_values($instance){
		$defaults = $this->widget_default();
		
		return wp_parse_args( (array) $instance, $defaults);
	}
	
	function process_values($inst){
		$defaults = $this->widget_default();
		
		$send_values = array();
		
		foreach($defaults as $f=>$v){
			
			if($f == 'show_upcoming'){
				$send_values[$f] =	(!empty($inst[$f]) && $inst[$f]=='yes')? 1:'0';
			}else{
				$send_values[$f] = (!empty($inst[$f])) ?$inst[$f] : $v;
			}
		}
		
		return $send_values;
	}
	
	function form($instance) {
		global $eventon;
				
		$instance = $this->widget_values($instance); 
		extract($instance);
		// HTML
		
		?>
		<div id='eventon_widget_settings'>
			<div class='eventon_widget_top'><p></p></div>
			
			<div class='evo_widget_outter evowig'>
				<div class='evo_wig_item'>
					<?php $eventon->throw_guide('Set a custom ID for widget calendar to separate it from other eventON calendar widgets. Specially if you have more than one eventON calendar widgets. <a href="http://www.myeventon.com/documentation/shortcode-guide/" target="_blank">What should be the ID</a>','L');?>
					<input id="<?php echo $this->get_field_id('ev_cal_id'); ?>" name="<?php echo $this->get_field_name('ev_cal_id'); ?>" type="text" 
					value="<?php echo esc_attr($ev_cal_id); ?>" placeholder="Widget ID" title="Widget ID"/>					
				</div>
				<div class='evo_wig_item'>					
					<input id="<?php echo $this->get_field_id('ev_title'); ?>" name="<?php echo $this->get_field_name('ev_title'); ?>" type="text" 
					value="<?php echo esc_attr($ev_title); ?>" placeholder='Widget Title' title='Widget Title'/>					
				</div>
				<div class='evo_wig_item'>
					<?php $eventon->throw_guide('If left blank - will display all events for that month.','L');?>
										
					<input id="<?php echo $this->get_field_id('ev_count'); ?>" 
					name="<?php echo $this->get_field_name('ev_count'); ?>" type="text" 
					value="<?php echo esc_attr($ev_count); ?>" placeholder='Event Count' title='Event Count'/>
					
				</div>
				<div class='evo_wig_item' connection=''>
					<input id="<?php echo $this->get_field_id('ev_hidepastev'); ?>" type='hidden' name='<?php echo $this->get_field_name('ev_hidepastev'); ?>' value='<?php echo esc_attr($ev_hidepastev); ?>'/>
					<p class='evowig_chbx <?php echo ($ev_hidepastev=='yes')?'selected':null; ?>'></p>
					<p>Hide past events</p>
					<div class='clear'></div>
				</div>				
			</div>
			
			<p class='divider'></p>
			<div class='evo_widget_outter evowig'>
				<div class='evo_wig_item' connection=''>

					<input id="<?php echo $this->get_field_id('show_upcoming'); ?>" type='hidden' name='<?php echo $this->get_field_name('show_upcoming'); ?>' value='<?php echo esc_attr($show_upcoming); ?>'/>
					<p class='evowig_chbx <?php echo ($show_upcoming=='yes')?'selected':null; ?>'></p>
					<p>Show upcoming events</p>
					<div class='clear'></div>
				</div>
				
				<div class='evo_wug_hid' <?php echo ($show_upcoming=='yes')?'style="display:block"':null; ?>>
					<div class='evo_wig_item'>
						<?php $eventon->throw_guide('Use this field to set the number of upcoming months to show','L');?>
												
						<input id="<?php echo $this->get_field_id('number_of_months'); ?>" name="<?php echo $this->get_field_name('number_of_months'); ?>" type="text" 
						value="<?php echo esc_attr($number_of_months); ?>" placeholder='Number of Months' title='Number of Months'/>
						
					</div>
					<div class='evo_wig_item' connection=''>
						<input id="<?php echo $this->get_field_id('hide_mult_occur'); ?>" type='hidden' name='<?php echo $this->get_field_name('hide_mult_occur'); ?>' value='<?php echo esc_attr($hide_mult_occur); ?>'/>
						<p class='evowig_chbx <?php echo ($hide_mult_occur=='yes')?'selected':null; ?>'></p>
						<p>Hide Multiple Occurance</p>
						<div class='clear'></div>
					</div>
					<div class='evo_wig_item' connection=''>
						<input id="<?php echo $this->get_field_id('hide_empty_months'); ?>" type='hidden' name='<?php echo $this->get_field_name('hide_empty_months'); ?>' value='<?php echo esc_attr($hide_empty_months); ?>'/>
						<p class='evowig_chbx <?php echo ($hide_empty_months=='yes')?'selected':null; ?>'></p>
						<p>Hide Empty Months</p>
						<div class='clear'></div>
					</div>
				</div>
			</div>	
			
			
			<p class='divider'></p>
			<div class='evo_widget_outter evowig'>
				<div class='evo_wig_item' connection=''>					
					
					<input id="<?php echo $this->get_field_id('_is_fixed_time'); ?>" type='hidden' name='<?php echo $this->get_field_name('_is_fixed_time'); ?>' value='<?php echo esc_attr($_is_fixed_time); ?>'/>
					<p class='evowig_chbx <?php echo ($_is_fixed_time=='yes')?'selected':null; ?>'></p>
					<p>Set fixed month/year</p>
					<div class='clear'></div>
				</div>
				
				<div class='evo_wug_hid' <?php echo ($_is_fixed_time=='yes')?'style="display:block"':null; ?>>
					<div class='evo_wig_item'>
						<input id="<?php echo $this->get_field_id('fixed_month'); ?>" name="<?php echo $this->get_field_name('fixed_month'); ?>" type="text" 
						value="<?php echo esc_attr($fixed_month); ?>" placeholder='Fixed month number' title='Fixed month number'/>					
					</div><div class='evo_wig_item'>
						<input id="<?php echo $this->get_field_id('fixed_year'); ?>" name="<?php echo $this->get_field_name('fixed_year'); ?>" type="text" 
						value="<?php echo esc_attr($fixed_year); ?>" placeholder='Fixed year number' title='Fixed year number'/>					
					</div>
				</div>
			</div>
			 
			<p class='divider'></p>
			 
			<div class='evo_widget_outter evowig'>
				<div class='evo_wig_item'>
					<input id="<?php echo $this->get_field_id('ev_type'); ?>" name="<?php echo $this->get_field_name('ev_type'); ?>" type="text" 
					value="<?php echo esc_attr($ev_type); ?>" placeholder='Event Types' title='Event Types'/>
					<?php $eventon->throw_guide('Leave blank for all event types, else type <a href="edit-tags.php?taxonomy=event_type&post_type=ajde_events">event type ID</a> separated by commas)','L');?>
					
				</div>						
			</div>
			<div class='evo_widget_outter evowig'>
				<div class='evo_wig_item select_row'>
					<label>Language</label>
					<select name="<?php echo $this->get_field_name('lang'); ?>">
					<?php 

						for($x=1; $x<4; $x++){
							echo "<option value='L{$x}' ". (($lang=='L'.$x)? 'selected="selected"':null ). ">L{$x}</option>";
						}
					?>		
					</select>					
					
				</div>						
			</div>
			
			
		</div>
		<?php
	}
	
	// update the new values for widget
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		foreach($this->widget_default() as $defv=>$def){
			if($defv!='ev_type_2')
				$instance[$defv] = strip_tags($new_instance[$defv]);
		}
		
		return $instance;
	}
	
	// add new default shortcode arguments
	public function event_list_shortcode_defaults($arr){
		
		return array_merge($arr, array(
			'hide_empty_months'=>'no',
		));
		
	}
	
	/**
	 * The actuval widget
	 */
	public function widget($args, $instance) {
		global $eventon;
		
		// DEFAULTS
		$fixed_month = $fixed_year = 0;
		
		// extract widget specific variables
		extract($args, EXTR_SKIP);		
		
		
		$values = $this->process_values($instance);		
		extract($values);
		
		
		// HIDE EMPTY months
		if($hide_empty_months =='yes')
			add_filter('eventon_shortcode_defaults', array($this,'event_list_shortcode_defaults'), 10, 1);
		
		// CALENDAR ARGUMENTS
		$args = array(
			'cal_id'=>$ev_cal_id,
			'event_count'=>$ev_count,
			'show_upcoming'=>$show_upcoming,
			'number_of_months'=>$number_of_months,
			'event_type'=> $ev_type,
			'lang'=> $lang,
			'event_type_2'=> 'all',
			'fixed_month'=>$fixed_month,
			'fixed_year'=>$fixed_year,
			'hide_past'=>$ev_hidepastev,
			'hide_mult_occur'=>$hide_mult_occur,
			'hide_empty_months'=>$hide_empty_months,
		);
		
		
		
		
		//print_r($args);
		
		// Check for event type filterings called for from widget settings
		if($ev_type!='all'){
			$filters['filters'][]=array(
				'filter_type'=>'tax',
				'filter_name'=>'event_type',
				'filter_val'=>$args['event_type']
			);
			$args = array_merge($args,$filters);
		}
		if($ev_type_2!='all'){
			$filters['filters'][]=array(
				'filter_type'=>'tax',
				'filter_name'=>'event_type_2',
				'filter_val'=>$args['event_type_2']
			);
			$args = array_merge($args,$filters);
		}
		
		
		/*
			 WIDGET
		*/	
		if(has_action('eventon_before_widget')){
			do_action('eventon_before_widget');
		}else{
			echo $before_widget;
		}
			
			
		
		// widget title
		if(!empty($instance['ev_title']) ){
			echo "<h3 class='widget-title'>".$instance['ev_title']."</h3>";
		}

		//print_r($args);
		
		$content =$eventon->evo_generator->eventon_generate_calendar($args);
		echo "<div id='evcal_widget'>".$content."</div>";
		
		
		if(has_action('eventon_after_widget')){
			do_action('eventon_after_widget');
		}else{
			echo $after_widget;
		}
		
	}
}





// EventON Second widget
class EvcalWidget_SC extends WP_Widget{
	
	function EvcalWidget_SC(){
		$widget_ops = array('classname' => 'EvcalWidget_SC', 
			'description' => 'EventON shortcode executor in the widget.' );
		$this->WP_Widget('EvcalWidget_SC', 'eventON Shortcode executor (ESE)', $widget_ops);
	}


	function form($instance) {
		global $eventon;

		extract($instance);

		$evo_title = (!empty($evo_title))? $evo_title: null;
		$evo_shortcodeW = (!empty($evo_shortcodeW))? $evo_shortcodeW: null;
		// HTML
		?>
		<div id='eventon_widget_settings' class='eventon_widget_settings'>
			<div class='eventon_widget_top'><p></p></div>
			
			<div class='evo_widget_outter evowig'>				
				<div class='evo_wig_item'>					
					<input id="<?php echo $this->get_field_id('evo_title'); ?>" name="<?php echo $this->get_field_name('evo_title'); ?>" type="text" 
					value="<?php echo esc_attr($evo_title); ?>" placeholder='Widget Title' title='Widget Title'/>					
				</div>
			</div>
			<p><label><?php _e('Paste the eventON shortcode to execute','eventon');?><?php $eventon->throw_guide('Paste eventON Shortcodes with [ ] in here to execute them for this widget.','L');?></label>
			<p class='evo_widget_textarea'><textarea name="<?php echo $this->get_field_name('evo_shortcodeW'); ?>" id="<?php echo $this->get_field_id('evo_shortcodeW'); ?>"><?php echo esc_attr($evo_shortcodeW); ?></textarea></p>
		
		</div>
		<?php
	}

	// update the new values for widget
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$instance['evo_shortcodeW'] = strip_tags($new_instance['evo_shortcodeW']);
		$instance['evo_title'] = strip_tags($new_instance['evo_title']);
		
		return $instance;
	}

	/**
	 * The actuval widget
	 */
	public function widget($args, $instance) {
		global $eventon;
				
		// extract widget specific variables
		extract($args, EXTR_SKIP);		
		
		
		/*
			 WIDGET
		*/	
		if(has_action('eventon_before_widget_SC')){
			do_action('eventon_before_widget_SC');
		}else{
			echo $before_widget;
		}			
		

		$title = apply_filters('widget_title', $instance['evo_title'] );  

		// widget title
		if(!empty($instance['evo_title']) ){
			echo $before_title. $title .$after_title;
		}

		// shortcode
		if(!empty($instance['evo_shortcodeW'])){
			echo "<div id='evcal_widget' >";
			echo do_shortcode( $instance['evo_shortcodeW']) ;	
			echo "</div>";		
		}


		if(has_action('eventon_after_widget_SC')){
			do_action('eventon_after_widget_SC');
		}else{
			echo $after_widget;
		}
		
	}
}