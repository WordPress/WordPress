<?php
/**
 * EVO_generator class.
 *
 * @class 		EVO_generator
 * @version		2.2.13.2
 * @package		EventON/Classes
 * @category	Class
 * @author 		AJDE
 */

class EVO_generator {
	
	public $google_maps_load, 
		$is_eventcard_open,				
		$evopt1, 
		$evopt2, 
		$evcal_hide_sort;
	
	public $is_upcoming_list = false;
	public $is_eventcard_hide_forcer = false;
	public $_sc_hide_past = false; // shortcode hide past
		
	public $wp_arguments='';
	public $shortcode_args;
	public $filters;
	
	private $lang_array=array();
	
	public $current_event_ids = array();
	
	private $_hide_mult_occur = false;
	private	$events_processed = array();
	
	private $__apply_scheme_SEO = false;
	private $_featured_events = array();

	public $__calendar_type ='default';

	private $event_types = 3;
	
	
	/**	Construction function	 */
		public function __construct(){
			
			
			/** set class wide variables **/
			$options_1 = get_option('evcal_options_evcal_1');
			$this->evopt1= (!empty($options_1))? $options_1:null;
			$this->evopt2= get_option('evcal_options_evcal_2');		
			
			$this->is_eventcard_open = (!empty($this->evopt1['evo_opencard']) && $this->evopt1['evo_opencard']=='yes')? true:false;
			
			// set reused values
			$this->evcal_hide_sort = (!empty($this->evopt1['evcal_hide_sort']))? $this->evopt1['evcal_hide_sort']:null;
			
			// load google maps api only on frontend
			add_action( 'init', array( $this, 'init' ) );		
			
			//$this->google_maps_load = get_option('evcal_gmap_load');
			//add_action('wp_enqueue_scripts', array($this, 'load_evo_styles'));

			
		}
	
		function init(){	
			add_action( 'init', array( $this, 'load_google_maps_api' ) );

			$this->verify_eventtypes();
			$this->reused();
			
		}
	

	// the reused variables and other things within the calendar
		function reused(){
			$lang = (!empty($this->shortcode_args['lang']))? $this->shortcode_args['lang']: 'L1';


			// for each event type category
			$ett_i18n_names = evo_get_localized_ettNames( $lang, $this->evopt1, $this->evopt2);

			for($x = 1; $x< $this->event_types ; $x++){
				$ab = ($x==1)? '':$x;			
				
				$this->lang_array['et'.$ab] = $ett_i18n_names[$x];
			}
			 		
			$this->lang_array['no_event'] = eventon_get_custom_language($this->evopt2, 'evcal_lang_noeve','No Events',$lang);

		}

	// load scripts
		function load_evo_files(){
			global $eventon; 
			$eventon->load_default_evo_scripts();
			$this->load_google_maps_api();
		}
	
	// GOOGLE MAP
		function load_google_maps_api(){
			// google maps loading conditional statement
			if( !empty($this->evopt1['evcal_cal_gmap_api']) && ($this->evopt1['evcal_cal_gmap_api']=='yes') 	){
				if(!empty($this->evopt1['evcal_gmap_disable_section']) && $this->evopt1['evcal_gmap_disable_section']=='complete'){
					
					
					//update_option('evcal_gmap_load',false);
					$this->google_maps_load = false;
					
					wp_enqueue_script( 'eventon_init_gmaps_blank');
					wp_enqueue_script( 'eventon_init_gmaps');
				}else{
					
					
					//update_option('evcal_gmap_load',true);
					$this->google_maps_load = true;
					
					wp_enqueue_script( 'eventon_init_gmaps');
				}
				
			}else {
				
				
				//update_option('evcal_gmap_load',true);
				$this->google_maps_load = true;

				wp_enqueue_script( 'evcal_gmaps');
				wp_enqueue_script('eventon_init_gmaps');
				
				// load map files only to frontend
				if ( !is_admin() ){
					wp_enqueue_script( 'evcal_gmaps');
					wp_enqueue_script( 'eventon_init_gmaps');
				}
			}
		}
	
	// GET event type category array
		function get_event_types(){
			$output;
			for($x = 1; $x< $this->event_types ; $x++){
				$ab = ($x==1)? '':'_'.$x;
				$event_type = 'event_type'.$ab;

				$output[$x] = $event_type;
			}
			return $output;
		}
		function verify_eventtypes(){

			for($x= 3; $x<6; $x++){
				if( !empty($this->evopt1['evcal_ett_'.$x]) && $this->evopt1['evcal_ett_'.$x]=='yes'){
					$this->event_types = $x+1;
				}else{
					break;
				}
			}
		}
	
	// SHORT CODE variables
		function get_supported_shortcode_atts(){

			$args = array(			
				'cal_id'=>'1',
				'event_count'=>0,
				'month_incre'=>0,			
				'number_of_events'=>5,
				'focus_start_date_range'=>'',
				'focus_end_date_range'=>'',
				'sort_by'=>'sort_date',
				'filters'=>'',
				'fixed_month'=>0,
				'fixed_year'=>0,
				'hide_past'=>'no',
				'hide_past_by'=>'ee',			
				'show_et_ft_img'=>'no',
				'event_order'=>'ASC',
				'ft_event_priority'=>'no',
				'number_of_months'=>1,
				'hide_mult_occur'=>'no',
				'show_upcoming'=>0,
				'lang'=>'L1',
				'pec'=>'',				// past event cut-off
				'etop_month'=>'no',
				'evc_open'=>'no',		// open eventCard by default
				'ux_val'=>'0', 			// user interaction to override default user interaction values
				'etc_override'=>'no',	// even type color override the event colors
				'jumper'=>'no'	,		// month jumper
				'accord'=>'no',			// accordion
				'only_ft'=> 'no',		// only featured events
				'exp_so'=>'no',		// expand sort options by default
				'rtl'=>'no',		// right to left text
				'wpml_l1'=>'',		// WPML lanuage L1 = en
				'wpml_l2'=>'',		// WPML lanuage L2 = nl
				'wpml_l3'=>'',		// WPML lanuage L3 = es
			);

			// each event type category
			foreach($this->get_event_types() as $ety=>$ett){			
				$args[$ett] ='all';
			}
			
			return apply_filters('eventon_shortcode_defaults', $args);
		}
	
	/*	Process the eventON variable arguments	*/
		function process_arguments($args='', $own_defaults=false, $type=''){
			
			$this->load_evo_files();
			
			$default_arguments = $this->get_supported_shortcode_atts();
			
			//print_r($args);
			
			if(!empty($args)){
			
				// merge default values of shortcode
				if(!$own_defaults)
					$args = shortcode_atts($default_arguments, $args);


				// Foreach even type
				foreach($this->get_event_types() as $ety=>$event_type){	

					if(!empty($args[$event_type]) && $args[$event_type]!='all'){
						$filters['filters'][]=array(
							'filter_type'=>'tax',
							'filter_name'=>$event_type,
							'filter_val'=>$args[$event_type]
						);
						$args = array_merge($args,$filters);
					}
				}		

					
				$this->shortcode_args=$args; // set global arguments
			
			// empty args
			}else{
				
				if($type=='usedefault'){
					$args = (!empty($this->shortcode_args))? $this->shortcode_args:null;
					
				}else{
					$this->shortcode_args=$default_arguments; // set global arguments
					$args = $default_arguments;
				}
			}
			
			
			// Set hide past value for shortcode hide past event variation
			$this->_sc_hide_past = (!empty($args['hide_past']) && $args['hide_past']=='yes')? true:false;
			
			// check for possible filters
			$this->filters = (!empty($args['filters']))? 'true':'false';
			
			
			//print_r($args);

			return $args;
		}
	
	function update_shortcode_arguments($new_args){
		return array_merge($this->shortcode_args, $new_args);
	}
	

	// shortcode arguments as attrs for the calendar header
		function shortcode_args_for_cal(){
			
			$arg = $this->shortcode_args;
			$_cd='';

			//print_r($arg);
			
			$cdata = apply_filters('eventon_calhead_shortcode_args', array(
				'hide_past'=>$arg['hide_past'],
				'show_et_ft_img'=>$arg['show_et_ft_img'],
				'event_order'=>$arg['event_order'],
				'ft_event_priority'=>((!empty($arg['ft_event_priority']))? $arg['ft_event_priority']: null),
				'lang'=>$arg['lang'],
				'evc_open'=>((!empty($arg['evc_open']))? $arg['evc_open']:'no'),
				'etc_override'=>((!empty($arg['etc_override']))? $arg['etc_override']:'no'),
			));

			foreach ($cdata as $f=>$v){
				$_cd .=''.$f.'="'.$v.'" ';
			}

			return "<div class='cal_arguments' style='display:none' {$_cd}></div>";
			
		}
	
	
	// GET: Calendar top Header
		function get_calendar_header($arguments){
			
			// SHORTCODE
			$args = $this->shortcode_args;
			extract($args);

			
			
			// FUNCTION
			$defaults = array(
				'focused_month_num'=>1,
				'focused_year'=>2014,			
				'range_start'=>0,
				'range_end'=>0,
				'send_unix'=>false,
				'header_title'=>'',
				'_html_evcal_list'=>true,
				'sortbar'=>true,
				'_html_sort_section'=>true,
				'date_header'=>true,
			);
			$arg_x = array_merge($defaults, $arguments);
			extract($arg_x);


			$cal_version =  get_option('eventon_plugin_version');			
			
			//BASE settings to pass to calendar
			
			$eventcard_open = ($this->is_eventcard_open)? 'eventcard="1"':null;	

			// right to left 
				$rtl = (!empty($args['rtl']) && $args['rtl'] =='yes')? 'evortl':null;
				$upcoming_list = ($this->is_upcoming_list)?'ul':null;
			
			$__cal_classes=apply_filters('eventon_cal_class',array('ajde_evcal_calendar', $rtl, $upcoming_list));
			
			$cal_header_title = get_eventon_cal_title_month($focused_month_num, $focused_year, $args['lang']);


			// calendar data variables
			$_cd='';
			$cdata = apply_filters('eventon_cal_jqdata', array(
				'cyear'=>$focused_year,
				'cmonth'=>$focused_month_num,
				'runajax'=>'1',
				'evc_open'=>((!empty($args['evc_open']) && $args['evc_open']=='yes')? '1':'0'),
				'cal_ver'=>$cal_version,
				'mapscroll'=> ((!empty($this->evopt1['evcal_gmap_scroll']) && $this->evopt1['evcal_gmap_scroll']=='yes')?'false':'true'),
				'mapformat'=> (($this->evopt1['evcal_gmap_format']!='')?$this->evopt1['evcal_gmap_format']:'roadmap'),
				'mapzoom'=>(($this->evopt1['evcal_gmap_zoomlevel']!='')?$this->evopt1['evcal_gmap_zoomlevel']:'12'),
				'ev_cnt'=>$args['event_count'],
				'sort_by'=>$args['sort_by'],
				'filters_on'=>$this->filters,
				'range_start'=>$range_start,
				'range_end'=>$range_end,
				'send_unix'=>( ($send_unix)?'1':'0'),
				'ux_val'=>( (!empty($ux_val) && $ux_val!='0' )? $ux_val: '0'.$ux_val),
				'accord'=>( (!empty($accord) && $accord== 'yes' )? '1': '0'),
			));
			foreach ($cdata as $f=>$v){
				$_cd .='data-'.$f.'="'.$v.'" ';
			}

			$content='';
			// Calendar SHELL
			$content .= "<div id='evcal_calendar_".$cal_id."' class='".( implode(' ', $__cal_classes))."' >
				<div class='evo-data' {$_cd} ></div>";

					$sort_class = ($this->evcal_hide_sort=='yes')?'evcal_nosort':null;
			
			// HTML 
			$content.="<div id='evcal_head' class='calendar_header ".$sort_class."' >";

			// if the calendar arrows and headers are to show 
			if($date_header){
				$hide_arrows_check = ($this->evopt1['evcal_arrow_hide']=='yes')?"style='display:none'":null;
				$focused_year = (int)$focused_year;

				if($jumper=='yes'){
					$content.= "<div class='evo-jumper'><span class='evo-jumper-btn'>".eventon_get_custom_language($this->evopt2, 'evcal_lang_jumpmonths','Jump Months')."</span>
						<div class='evo_j_container' style='display:none' m='{$focused_month_num}' y='{$focused_year}'>
							<div class='evo_j_months evo_j_dates' val='m'>
								<p class='legend'>".eventon_get_custom_language($this->evopt2, 'evcal_lang_jumpmonthsM','Month').": ";

						// months list
						$__months = eventon_get_oneL_months($this->evopt2['L1']);				
						$count = 1;
						foreach($__months as $m){
							$_current = ($focused_month_num == $count)? 'class="current set"':null;
							$content.= "<a val='{$count}' {$_current} title='". eventon_return_timely_names_('month_num_to_name',$count,'full',$lang)."' >{$m}</a>";
							$count ++;
						}
							$content.= "</p><div class='clear'></div></div>
							
							<div class='evo_j_years evo_j_dates' val='y'>
								<p class='legend'>".eventon_get_custom_language($this->evopt2, 'evcal_lang_jumpmonthsY','Year').": <a>".($focused_year-2)."</a><a>".($focused_year-1)."</a><a class='current set'>".$focused_year."</a><a>".($focused_year+1)."</a><a>".($focused_year+2)."</a></p><div class='clear'></div>
							</div>
						</div>
					</div>";
				}

				$content.="<p id='evcal_cur'> ".$cal_header_title."</p>
					<span id='evcal_prev' class='evcal_arrows evcal_btn_prev' ".$hide_arrows_check."><i class='fa fa-angle-left'></i></span><span id='evcal_next' class='evcal_arrows evcal_btn_next' ".$hide_arrows_check."><i class='fa fa-angle-right'></i></span>";	
			}else if(!empty($header_title)){
				$content.="<p>". $header_title ."</p>";
			}
			
			// (---) Hook for addon
				if(has_action('eventon_calendar_header_content')){
					ob_start();
					do_action('eventon_calendar_header_content', $content);
					$content.= ob_get_clean();
				}
			
			// Shortcode arguments
			$content.= $this->shortcode_args_for_cal();
			$content.="<div class='clear'></div></div>";
			
							
			// SORT BAR
			$content.= ($_html_sort_section)? $this->eventon_get_cal_sortbar($args, $sortbar):null;
			
			$rtl = (!empty($args['rtl']) && $args['rtl'] =='yes')? 'evortl':null;
			$content .= ($_html_evcal_list)? "<div id='evcal_list' class='eventon_events_list {$rtl}'>":null;


			// RESET calendar stuff
				if($this->is_upcoming_list){ $this->is_upcoming_list=false;}

			return $content;
		}
		

	

	// GET: single calendar month body content
	function get_calendar_month_body( $get_new_monthyear, $focus_start_date_range='', $focus_end_date_range=''){
		
		// CHECK if start and end day ranges are provided for this function
		$defined_date_ranges = ( empty($focus_start_date_range) && empty($focus_end_date_range) )?false: true;
		
		$args = $this->shortcode_args;
		extract($args);

		// update the languages array
		$this->reused();
		
		//print_r($args);
		
		// check if date ranges present
		if( !$defined_date_ranges){	
		
			// default start end date range -- for month view
			$get_new_monthyear = $get_new_monthyear;
			
			$focus_start_date_range = mktime( 0,0,0,$get_new_monthyear['month'],1,$get_new_monthyear['year'] );
			$time_string = $get_new_monthyear['year'].'-'.$get_new_monthyear['month'].'-1';		
			
			$focus_end_date_range = mktime(23,59,59,($get_new_monthyear['month']),(date('t',(strtotime($time_string) ))), ($get_new_monthyear['year']));
			
		}
				
				
		// generate events within the focused date range
		$eve_args = array(
			'focus_start_date_range'=>$focus_start_date_range,
			'focus_end_date_range'=>$focus_end_date_range,
			'sort_by'=>$sort_by, // by default sort events by start date					
			'event_count'=>$event_count,
			'filters'=>$filters,
			'number_months'=>$number_of_months, // to determine empty label 			
		);
		
		// add event type arguments		
		for($x=1; $x< $this->event_types; $x++){
			$ab = ($x==1)? '':'_'.$x;
			$eve_args['ev_type'.$ab] = !empty($args['ev_type'.$ab])? $args['ev_type'.$ab]:null;
		}

		$eve_args =$this->update_shortcode_arguments($eve_args);
		$content_li = $this->eventon_generate_events($eve_args);	
		
		
		
		ob_start();
		if($content_li != 'empty'){
			// Eventon Calendar events list
			//echo "<div id='evcal_list' class='eventon_events_list'>";
			echo $content_li;
			//echo "</div>"; 
		}else{
			// ONLY UPCOMING LIST empty months
			if( $this->is_upcoming_list && !empty($hide_empty_months) && $hide_empty_months=='yes'){
				echo 'false';
			}else{
				//echo "<div id='evcal_list' class='eventon_events_list'>";
				echo "<div class='eventon_list_event'><p class='no_events'>".$this->lang_array['no_event']."</p></div>";
				//echo "</div>";
			}
		}
		
		return ob_get_clean();
		
	}
	



	/* INDEPENDENCY */

		
		// HEADER
		public function calendar_shell_header($arg){

			$defaults = array(
				'sort_bar'=> true,
				'title'=>'none',
				'date_header'=>true,
				'month'=>'1',
				'year'=>2014,
				'date_range_start'=>0,
				'date_range_end'=>0,
				'send_unix'=>false
			);

			$args = array_merge($defaults, $arg);

			$date_range_start =($args['date_range_start']!=0)? $args['date_range_start']: '0';
			$date_range_end =($args['date_range_end']!=0)? $args['date_range_end']: '0';

			$content ='';

			$content .= $this->get_calendar_header(
				array(
					'focused_month_num'=>$args['month'], 
					'focused_year'=>$args['year'], 
					'sortbar'=>$args['sort_bar'], 
					'date_header'=>$args['date_header'],
					'range_start'=>$date_range_start, 
					'range_end'=>$date_range_end , 
					'send_unix'=>$args['send_unix'],
					'header_title'=>$args['title']
				)
			);

			return $content;
		}

		// FOOTER
		public function calendar_shell_footer(){


			ob_start();
			do_action('evo_cal_footer');

			?>
			</div><!-- #evcal_list-->
			<div class='clear'></div>
			</div><!-- .ajde_evcal_calendar-->

			<?php

			return ob_get_clean();
		}



	// GET: calendar starting month and year data 
	function get_starting_monthYear(){
		$args = $this->shortcode_args;
		extract($args);
		
		// current focus month calculation
		//$current_timestamp =  current_time('timestamp');
			
		
		// *** GET STARTING month and year 
		if($fixed_month!=0 && $fixed_year!=0){
			$focused_month_num = $fixed_month;
			$focused_year = $fixed_year;
		}else{
		// GET offset month/year values
			$this_month_num = date('n');
			$this_year_num = date('Y');

			
			if($month_incre !=0){

				$mi_int = (int)$month_incre;

				$new_month_num = $this_month_num +$mi_int;
				
				//month
				$focused_month_num = ($new_month_num>12)? 
					$new_month_num-12:
					( ($new_month_num<1)?$new_month_num+12:$new_month_num );
				
				// year		
				$focused_year = ($new_month_num>12)? 
					$this_year_num+1:
					( ($new_month_num<1)?$this_year_num-1:$this_year_num );

				
			}else{
				$focused_month_num = $this_month_num;
				$focused_year = $this_year_num;
			}

		}
		

		//echo strtotime($month_incre.' month', $current_timestamp);

		return array('focused_month_num'=>$focused_month_num, 'focused_year'=>$focused_year);
	}



	/** GENERATE: function to build the entire event calendar */
		public function eventon_generate_calendar($args){
			global $EventON, $wpdb;		
					
			// extract the variable values 
			$args__ = $this->process_arguments($args);
			extract($args__);

			$this->_hide_mult_occur= ($hide_mult_occur=='yes')?true:false;
			
			//echo get_template_directory();
			//echo AJDE_EVCAL_PATH;
			//print_r($args__);
			//print_r($args);
			
			// Before beginning the eventON calendar Action
			if(has_action('eventon_cal_variable_action'))		
				do_action('eventon_cal_variable_action', $args);			
			
			// If settings set to hide calendar
			if( $show_upcoming!=1 && ( !empty($this->evopt1['evcal_cal_hide']) && $this->evopt1['evcal_cal_hide']=='no') ||  empty($this->evopt1['evcal_cal_hide'])):		
				
				
				$evcal_plugin_url= AJDE_EVCAL_URL;			
				$content = $content_li='';	
				
				// Check for empty month_incre values
				$month_incre = (!empty($month_incre))? $month_incre:0;
				
				
				// *** GET STARTING month and year 
				extract( $this->get_starting_monthYear() );
				
				// ========================================
				// HEADER with month and year name	- for NONE upcoming list events
				$content.= $this->get_calendar_header(array(
					'focused_month_num'=>$focused_month_num, 
					'focused_year'=>$focused_year
					)
				);
							
				
				// Calendar month body
				$get_new_monthyear = eventon_get_new_monthyear($focused_month_num, $focused_year,0);
				$content.= $this->get_calendar_month_body($get_new_monthyear, $focus_start_date_range, $focus_end_date_range);
				
				$content.=$this->calendar_shell_footer();
					
				// action to perform at the end of the calendar
				do_action('eventon_cal_end');
				
				return  $content;	

				
			
			// support for show_upcoming shortcode -- deprecated in the future
			elseif($show_upcoming==1 && $number_of_months>0):	
				
				return $this->generate_events_list($args);	
			endif;
			
			
		}

	/* GENERATE: upcoming list events*/
		function generate_events_list($args=''){
			
			$type = (empty($args))? 'usedefault':null;
			
			$args__ = $this->process_arguments($args, '', $type);
			extract($args__);
			$content='';
					
			// HIDE or show multiple occurance of events in upcoming list
			$this->_hide_mult_occur= ($hide_mult_occur=='yes')?true:false;
			
			
			// check if upcoming list calendar view
			if($number_of_months>0){
				$this->is_upcoming_list= true;
				$this->is_eventcard_open = false;			
			}
			
			// *** GET STARTING month and year 
			extract( $this->get_starting_monthYear() );
			
			// Calendar SHELL
			$content.=$this->get_calendar_header(array(
				'focused_month_num'=>$focused_month_num, 
				'focused_year'=>$focused_year,
				'sortbar'=>false,
				'date_header'=>false,
				'_html_evcal_list'=>false,
				'_html_sort_section'=>false
				)
			);

			
			
			// generate each month
			for($x=0; $x<$number_of_months; $x++){

				//echo $number_of_months;

				$month_body='';

				$__mo_cnt = ($event_order=='DESC')? $number_of_months-$x-1: $x;
				
				$get_new_monthyear = eventon_get_new_monthyear($focused_month_num, $focused_year,$__mo_cnt);
				
				$active_month_name = eventon_returnmonth_name_by_num($get_new_monthyear['month']);
				
				// check settings to see if year should be shown or not
				$active_year = (!empty($show_year) && $show_year=='yes')?
					$get_new_monthyear['year']:null;
					
				// body content of the month
				$month_body= $this->get_calendar_month_body($get_new_monthyear);
				
				
				if($month_body=='false' && !empty($hide_empty_months) && $hide_empty_months=='yes' ){
					//$content.= "<div class='evcal_month_line'><p>".$active_month_name.' '.$active_year."</p></div>";
				}else{
					// Construct months exterior 				
					$content.= "<div class='evcal_month_line'><p>".$active_month_name.' '.$active_year."</p></div>";

					$content.= "<div id='evcal_list' class='eventon_events_list'>";
					$content.= $month_body;
					$content.= "</div>";
				
				}
			}
			
			
			$content.="<div class='clear'></div></div>";
			
			return $content;
				
		}
	
	
	/** MAIN function to generate individual events.	*/	 
		public function eventon_generate_events($args){
			
			global $EventON;
					
			// get required shortcode based argument values
			if(empty($this->shortcode_args)){
				$ecv = $this->process_arguments($args);
			}else{
				$args =array_merge($this->shortcode_args,$args );
				$ecv =$this->process_arguments($args);
			}
			
			$this->reused();
			//print_r($this->shortcode_args);
			//print_r($args);
			//print_r($ecv);
			
			// ===========================
			// WPQUery Arguments
			$wp_arguments = array (
				'post_type' 		=> 'ajde_events' ,
				'post_status'		=>'publish',
				'posts_per_page'	=>-1 ,
				'order'				=>'ASC',					
			);
			
			// apply other filters to wp argument
			$wp_arguments = $this->apply_evo_filters_to_wp_argument($wp_arguments, $ecv);
			
			
			//print_r($wp_arguments);
					
			// -----------------------------
			// hook for addons
			if(has_filter('eventon_wp_query_args')){
				$wp_arguments = apply_filters('eventon_wp_query_args',$wp_arguments);
			}
			
			$this->wp_arguments = $wp_arguments;
			
			//print_r($wp_arguments);
			
			
			// ========================	
			// GET: list of events for wp argument
			$event_list_array = $this->wp_query_event_cycle(
				$wp_arguments,				
				$ecv['focus_start_date_range'], 
				$ecv['focus_end_date_range'],
				$ecv
			);
			
			
			//print_r($event_list_array);		
			
			//print_r($this->_featured_events);
			
			// SORT: events array
			if(is_array($event_list_array)){			
				switch($ecv['sort_by']){
					case has_action("eventon_event_sorting_{$ecv['sort_by']}"):
						do_action("eventon_event_sorting_{$ecv['sort_by']}", $event_list_array);
						
					break;
					case 'sort_date':
						usort($event_list_array, 'cmp_esort_startdate' );
					break;case 'sort_title':
						usort($event_list_array, 'cmp_esort_title' );
					break; case 'sort_color':
						usort($event_list_array, 'cmp_esort_color' );
					break;
					
				}
			}
			//print_r($event_list_array);
			
			// ALT: reverse events order if set
			$event_list_array = ($this->shortcode_args['event_order']=='DESC')? 
				array_reverse($event_list_array) : $event_list_array;
			
			if(has_filter('eventon_sorted_dates'))
				apply_filters('eventon_sorted_dates', $event_list_array);
			
			
			// GET: eventTop and eventCard for each event in order
			$months_event_array = $this->generate_event_data( 
				$event_list_array, 
				$ecv['focus_start_date_range']
			);
			//print_r($months_event_array);
			
			
			// MOVE: featured events to top if set
			if($this->shortcode_args['ft_event_priority']=='yes' && !empty($this->_featured_events) && count($this->_featured_events)>0){
				
				$ft_events = $events = array();
				
				foreach($months_event_array as $event){
					//print_r($event_list_array);
					
					if(in_array($event['event_id'], $this->_featured_events)){
						$ft_events[]=$event;
					}else{
						$events[]=$event;
					}
				}
				
				// move featured events to top
				$months_event_array =array_merge($ft_events,$events);
			}
			
			
			// ========================
			// RETURN VALUES
			$content_li='';
			// month array with events
			if( is_array($months_event_array) && count($months_event_array)>0){
				if($ecv['event_count']==0 ){
					foreach($months_event_array as $event){
						$content_li.= $event['content'];
					}
					
				}else if($ecv['event_count']>0){
					
					// make sure we take lesser value of count
					$lesser_of_count = (count($months_event_array)<$ecv['event_count'])?
						count($months_event_array): $ecv['event_count'];
					
					// for each event until count
					for($x=0; $x<$lesser_of_count; $x++){
						$content_li.= $months_event_array[$x]['content'];
					}
				}
			}else{	
				// EMPTY month array
				if($this->is_upcoming_list && !empty($ecv['hide_empty_months']) && $ecv['hide_empty_months']=='yes'){
					$content_li = "empty";				
				}else{
					$content_li = "<div class='eventon_list_event'><p class='no_events' >".$this->lang_array['no_event']."</p></div>";
				}
				
			}
			return $content_li;
			
		}// END evcal_generate_events()
		
	
	
	/**
	 * WP_Query function to generate relavent events for a given month
	 * return events list within start - end date range for WP_Query arg.
	 * return array
	 */
		public function wp_query_event_cycle($wp_arguments, $focus_month_beg_range, $focus_month_end_range, $ecv=''){
			
			
			$event_list_array= $featured_events = array();
			$wp_arguments= (!empty($wp_arguments))?$wp_arguments: $this->wp_arguments;
			//print_r($wp_arguments);
			
			
			// check if multiple occurance of events b/w months allowed
			$__run_occurance_check = (($this->is_upcoming_list && $this->_hide_mult_occur) || (!empty($this->shortcode_args['hide_mult_occur']) && $this->shortcode_args['hide_mult_occur']=='yes'))? true:false;
			
			/** RUN through all events **/
			$events = new WP_Query( $wp_arguments);
			if ( $events->have_posts() ) :
				
				date_default_timezone_set('UTC');	
				// override past event cut-off
					if(!empty($this->shortcode_args['pec'])){

						//shortcode driven hide_past value
						$evcal_cal_hide_past= ($this->_sc_hide_past)? 'yes': 
							( (!empty($this->evopt1['evcal_cal_hide_past']))? $this->evopt1['evcal_cal_hide_past']: 'no');

						if( $this->shortcode_args['pec']=='cd'){
							// this is based on local time
							$current_time = strtotime( date("m/j/Y", current_time('timestamp')) );	
						}else{
							// this is based on UTC time zone
							$current_time = current_time('timestamp');		
						}

					}else{
						// Define option values for the front-end
						$cur_time_basis = (!empty($this->evopt1['evcal_past_ev']) )? $this->evopt1['evcal_past_ev'] : null;
						//shortcode driven hide_past value
						$evcal_cal_hide_past= ($this->_sc_hide_past)? 'yes': 
							( (!empty($this->evopt1['evcal_cal_hide_past']))? $this->evopt1['evcal_cal_hide_past']: 'no');
						
						//date_default_timezone_set($tzstring);	
						if($evcal_cal_hide_past=='yes' && $cur_time_basis=='today_date'){
							// this is based on local time
							$current_time = strtotime( date("m/j/Y", current_time('timestamp')) );	
						}else{
							// this is based on UTC time zone
							$current_time = current_time('timestamp');		
						}
					}//pec not present


					// hide past by variable
						$hide_past_by = (!empty($this->shortcode_args['hide_past_by']))? $this->shortcode_args['hide_past_by']: null;

				// each event
				while( $events->have_posts()): $events->the_post();

					$p_id = get_the_ID();
					$ev_vals = get_post_custom($p_id);

					// if event set to exclude from calendars
					if(!empty($ev_vals['evo_exclude_ev']) && $ev_vals['evo_exclude_ev'][0]=='yes')
						continue;


					
					$is_recurring_event = (!empty($ev_vals['evcal_repeat']) )? $ev_vals['evcal_repeat'][0]: null;
					//$__is_all_day_event = (!empty($ev_vals['evcal_allday']) && $ev_vals['evcal_allday'][0]=='yes')?true:false;
					
					// initial event start and end UNIX
					$row_start = (!empty($ev_vals['evcal_srow']))? 
						$ev_vals['evcal_srow'][0] :null;
					$row_end = ( !empty($ev_vals['evcal_erow']) )? 
						$ev_vals['evcal_erow'][0]:$row_start;
					
					$evcal_event_color_n= (!empty($ev_vals['evcal_event_color_n']))?$ev_vals['evcal_event_color_n'][0]:'0';
					
					$_is_featured = (!empty($ev_vals['_featured']))? 
						$ev_vals['_featured'][0] :'no';


					
					// check for recurring event 
					if($is_recurring_event=='yes'){

						// get saved repeat intervals for repeating events
						$repeat_intervals = (!empty($ev_vals['repeat_intervals']))? unserialize($ev_vals['repeat_intervals'][0]) :null;

						

						$frequency = $ev_vals['evcal_rep_freq'][0];
						$repeat_gap_num = $ev_vals['evcal_rep_gap'][0];
						$repeat_num = (int)$ev_vals['evcal_rep_num'][0];
						

						// if repeat intervals are saved
						if(!empty($repeat_intervals) && is_array($repeat_intervals)){

							$virtual_dates=array();
							foreach($repeat_intervals as $interval){

								
								$E_start_unix = $interval[0];
								$E_end_unix = $interval[1];
								$term_ar = 'rm';

								
									// is future event
									$fe = ( (!empty($this->shortcode_args['el_type']))? true: eventon_is_future_event($current_time, $E_start_unix, $E_end_unix, $evcal_cal_hide_past, $hide_past_by) );

									// in date range
									$me = eventon_is_event_in_daterange($E_start_unix,$E_end_unix, $focus_month_beg_range,$focus_month_end_range, $this->shortcode_args);

									if($fe && $me){
										if($__run_occurance_check && !in_array($p_id, $this->events_processed) ||!$__run_occurance_check){
											
											if(!in_array($E_start_unix, $virtual_dates)){
												$virtual_dates[] = $E_start_unix;
												$event_list_array[] = array(
													'event_id' => $p_id,
													'event_start_unix'=>$E_start_unix,
													'event_end_unix'=>$E_end_unix,
													'event_title'=>get_the_title(),
													'event_color'=>$evcal_event_color_n,
													'event_type'=>$term_ar,
													'event_pmv'=>$ev_vals
												);
											
											}

											
											if($feature!='no'){
												$featured_events[]=$p_id;
											}
										}
										$this->events_processed[]=$p_id;	
									}
								
							}

						// does not have repeat intervals saved
						}else{

							// each repeating instance	OLD WAY
							for($x=0; $x<=($repeat_num); $x++){
								
								$feature='no';
														
								$repeat_multiplier = ((int)$repeat_gap_num) * $x;
								
								// Get repeat terms for different frequencies
								switch($frequency){
									// Additional frequency filters
									case has_filter("eventon_event_frequency_{$frequency}"):
										$terms = apply_filters("eventon_event_frequency_{$frequency}", $repeat_multiplier);								
										$term = $terms['term'];
										$term_ar = $terms['term_ar'];
									break;
									case 'yearly':
										$term = 'year';	$term_ar = 'ry';
										$feature = ($_is_featured!='no')?'yes':'no';
									break;

									// MONTHLY
									case 'monthly':
										
										$term = 'month';	$term_ar = 'rm';
										$feature = ($_is_featured!='no')?'yes':'no';
										
									break; 
									case 'weekly':
										$term = 'week';	$term_ar = 'rw';
										
									break;							
									default: $term = $term_ar = ''; break;
								}
								
								$E_start_unix = strtotime('+'.$repeat_multiplier.' '.$term, $row_start);
								$E_end_unix = strtotime('+'.$repeat_multiplier.' '.$term, $row_end);
								
										

								// check if only featured events to show
								if( (!empty($ecv['only_ft']) && $ecv['only_ft']=='yes' && $_is_featured=='yes') || 
									(!empty($ecv['only_ft']) && $ecv['only_ft']=='no' ) ||
									empty($ecv['only_ft'])
								){

									$fe = ( (!empty($this->shortcode_args['el_type']))? true: eventon_is_future_event($current_time, $E_start_unix, $E_end_unix, $evcal_cal_hide_past, $hide_past_by) );

									$me = eventon_is_event_in_daterange($E_start_unix,$E_end_unix, $focus_month_beg_range,$focus_month_end_range, $this->shortcode_args);
									

									if($fe && $me){
										if($__run_occurance_check && !in_array($p_id, $this->events_processed) ||!$__run_occurance_check){
										
											$event_list_array[] = array(
												'event_id' => $p_id,
												'event_start_unix'=>$E_start_unix,
												'event_end_unix'=>$E_end_unix,
												'event_title'=>get_the_title(),
												'event_color'=>$evcal_event_color_n,
												'event_type'=>$term_ar,
												'event_pmv'=>$ev_vals
											);
											
											if($feature!='no'){
												$featured_events[]=$p_id;
											}
										}
										$this->events_processed[]=$p_id;	
									}
								}				
							} // end for statement

						} // end if statemtn	
						
					}else{
					// Non recurring event

						// check if only featured events to show
						if( (!empty($ecv['only_ft']) && $ecv['only_ft']=='yes' && $_is_featured=='yes') || 
							(!empty($ecv['only_ft']) && $ecv['only_ft']=='no' ) ||
							empty($ecv['only_ft'])
						){

							$fe = ( (!empty($this->shortcode_args['el_type']))? true: eventon_is_future_event($current_time, $row_start, $row_end, $evcal_cal_hide_past, $hide_past_by));
							$me = eventon_is_event_in_daterange($row_start,$row_end, $focus_month_beg_range,$focus_month_end_range, $this->shortcode_args);

							//echo $_is_featured.'tt';
							
							//echo get_the_title().$row_end.' v '.$current_time.'-</br>';

							if($fe && $me){
								
								if($__run_occurance_check && !in_array($p_id, $this->events_processed) ||!$__run_occurance_check){
									
									$feature = ($_is_featured!='no')?'yes':'no';
									
									$event_list_array[] = array(
										'event_id' => $p_id,
										'event_start_unix'=>$row_start,
										'event_end_unix'=>$row_end,
										'event_title'=>get_the_title(),
										'event_color'=>$evcal_event_color_n,
										'event_type'=>'nr',
										'event_pmv'=>$ev_vals
									);	
									
									if($feature!='no'){
										$featured_events[]=$p_id;
									}
									
									$this->events_processed[]=$p_id;
								}
							}
						}	
					}
					
					
				endwhile;
				
				$this->_featured_events=$featured_events;
				
			endif;
			wp_reset_postdata();
			
			return $event_list_array;
		}
	
	
	// UPDATE or change shortcode argument values after its processed on global
		private function update_shortcode_args($field, $new_val){
			$sca = $this->shortcode_args;
			if(!empty($sca) && !empty($sca[$field])){
				$new_sca = $sca;
				$new_sca[$field]= $new_val;

				$this->shortcode_args = $new_sca;
			}

			if($field=='lang' && empty($sca)){
				$this->shortcode_args = array('lang'=>$new_val);
			}
		}

	/**	output single event data	 */
		public function get_single_event_data($event_id, $lang=''){

			$this->__calendar_type = 'single';

			if(!empty($lang)){
				$this->update_shortcode_args('lang', $lang);
			}
			
			// GET Eventon files to load for single event
			$this->load_evo_files();
			
			$this->is_eventcard_open= ($this->is_eventcard_hide_forcer)?false:true;
			
			$emv = get_post_custom($event_id);
			
			$event_array[] = array(
				'event_id' => $event_id,
				'event_start_unix'=>$emv['evcal_srow'][0],
				'event_end_unix'=>$emv['evcal_erow'][0],
				'event_title'=>get_the_title($event_id),
				'event_color'=>$emv['evcal_event_color_n'][0],
				'event_type'=>'nr',
				'event_pmv'=>$emv
			);
			
			$month_int = date('n', time() );

			return $this->generate_event_data($event_array, '', $month_int);
			
		}
	
	// RETURN event times
		private function generate_time($args){
			// start date is past enddate = focus day
			if($args['eventstart']['j'] < $args['cdate'] && $args['eventend']['j'] == $args['cdate']){
				return "<em class='evo_day'>".$args['eventstart']['M'].' '.$args['eventstart']['j']."</em><span class='start'>".$args['stime']."</span><span class='end'>- ".$args['etime']."</span>";
			
			// start day = focus day and end day in future
			}elseif($args['eventend']['j'] > $args['cdate'] && $args['eventstart']['j'] == $args['cdate']){
				return "<span class='start'>".$args['stime']."</span><em class='evo_day end'>".$args['eventend']['M'].' '.$args['eventend']['j']."</em><span class='end'>- ".$args['etime']."</span>";
			
			// both start day and end days are not focus day
			}elseif($args['eventend']['j'] != $args['cdate'] && $args['eventstart']['j'] != $args['cdate']){
				return "<em class='evo_day'>".$args['eventstart']['M'].' '.$args['eventstart']['j']."</em><span class='start'>".$args['stime']."</span><em class='evo_day end'>".$args['eventend']['M'].' '.$args['eventend']['j']."</em><span class='end'>- ".$args['etime']."</span>";
			
			// same start day as focus day
			}elseif($args['eventstart']['j'] == $args['cdate']){
				return "<span class='start'>".$args['stime']."</span><em class='evo_day end'>".$args['eventend']['M'].' '.$args['eventend']['j']."</em><span class='end'>- ".$args['etime']."</span>";
			// same end day as focus day
			}elseif($args['eventend']['j'] == $args['cdate']){
				return "<em class='evo_day'>".$args['eventstart']['M'].' '.$args['eventstart']['j']."</em><span class='start'>".$args['stime']."</span><em class='evo_day end'>".$args['eventend']['M'].' '.$args['eventend']['j']."</em><span class='end'>- ".$args['etime']."</span>";
			}
		}

	// GENERATE TIME for event
		public function generate_time_(
			$DATE_start_val='', 
			$DATE_end_val='', 
			$pmv, 
			$evcal_lang_allday, 
			$focus_month_beg_range='', 
			$FOCUS_month_int='', 
			$event_start_unix='', 
			$event_end_unix=''
		){
			global $eventon;

			// INITIAL variables
				$event_start_unix = (!empty($event_start_unix))? $event_start_unix: $pmv['evcal_srow'][0];
				$event_end_unix = (!empty($event_end_unix))? $event_end_unix: $pmv['evcal_erow'][0];
				$wp_time_format = get_option('time_format');
				$_is_allday = (!empty($pmv['evcal_allday']) && $pmv['evcal_allday'][0]=='yes')? true:false;
				$_hide_endtime = (!empty($pmv['evo_hide_endtime']) && $pmv['evo_hide_endtime'][0]=='yes')? true:false;

				$DATE_start_val= (!empty($DATE_start_val))? $DATE_start_val: eventon_get_formatted_time($event_start_unix);
				if(empty($event_end_unix)){
					$DATE_end_val= $DATE_start_val;
				}else{
					$DATE_end_val=(!empty($DATE_end_val))? $DATE_end_val: eventon_get_formatted_time($event_end_unix);
				}

				// FOCUSED values
				$CURRENT_month_INT = (!empty($FOCUS_month_int))?
					$FOCUS_month_int: (!empty($focus_month_beg_range)? 
						date('n', $focus_month_beg_range ): date('n')); // 
				$_current_date = (!empty($focus_month_beg_range))? date('j', $focus_month_beg_range ): 1;

				$time_format = (!empty($this->evopt1['evcal_tdate_format']))? $this->evopt1['evcal_tdate_format']: 'F j(l) T';
				// M F j S l D


				// Universal time format
				// if activated get time values
				$__univ_time = false;
				if( !empty($this->evopt1['evo_timeF_v']) && !empty($this->evopt1['evo_timeF']) && $this->evopt1['evo_timeF'] =='yes' ){
					$__univ_time_s = eventon_get_langed_pretty_time($event_start_unix, $this->evopt1['evo_timeF_v']);

					$__univ_time = ($_hide_endtime)? $__univ_time_s:  $__univ_time_s .' - '. eventon_get_langed_pretty_time($event_end_unix, $this->evopt1['evo_timeF_v']);
				}

				$formatted_start = date($wp_time_format,($event_start_unix));
				$formatted_end = date($wp_time_format,($event_end_unix));


			$date_args = array(
				'cdate'=>$_current_date,
				'eventstart'=>$DATE_start_val,
				'eventend'=>$DATE_end_val,
				'stime'=>$formatted_start,
				'etime'=>$formatted_end,
			);
			// same start and end months
			if($DATE_start_val['n'] == $DATE_end_val['n']){
							
				/** EVENT TYPE = start and end in SAME DAY **/
				if($DATE_start_val['j'] == $DATE_end_val['j']){
					
					// check all days event
					if($_is_allday){					
						$__from_to ="<em class='evcal_alldayevent_text'>(".$evcal_lang_allday.": ".$DATE_start_val['l'].")</em>";
						$__prettytime = $evcal_lang_allday.' ('. ucfirst($DATE_start_val['l']).')';
						$__time = "<span class='start'>".$evcal_lang_allday."</span>";

					}else{

						$__from_to = ($_hide_endtime)?
							$formatted_start:
							$formatted_start.' - '. $formatted_end;
						
						$__prettytime = '('. ucfirst($DATE_start_val['l']).') '.$__from_to;
						$__time = "<span class='start'>".$formatted_start."</span><span class='end'>- ".$formatted_end."</span>";
					}
					
					
					$_event_date_HTML = array(
						'html_date'=> '<span class="start">'.$DATE_start_val['j'].'<em>'.$DATE_start_val['M'].'</em></span>',
						'html_time'=>$__time,
						'html_fromto'=> apply_filters('eventon_evt_fe_time', $__from_to),
						'html_prettytime'=> ($__univ_time)? $__univ_time_s: apply_filters('eventon_evt_fe_ptime', $__prettytime),
						'class_daylength'=>"sin_val",
						'start_month'=>$DATE_start_val['M']
					);	
					
				}else{
					// different start and end date
					
					// check all days event
					if($_is_allday){
						$__from_to ="<em class='evcal_alldayevent_text'>(".$evcal_lang_allday.")</em>";
						$__prettytime = $DATE_start_val['j'].' ('. ucfirst($DATE_start_val['l']) .') - '.$DATE_end_val['j'].' ('. ucfirst($DATE_end_val['l']).')';
						$__time = "<span class='start'>".$evcal_lang_allday."</span>";
					}else{
						

						$__from_to = $formatted_start.' - '.$formatted_end. ' ('.$DATE_end_val['j'].')';
						$__prettytime =$DATE_start_val['j'].' ('. ucfirst($DATE_start_val['l']).') '.$formatted_start.' - '.$DATE_end_val['j'].' ('. ucfirst($DATE_end_val['l']).') '.$formatted_end;

						// for daily view check if start day is same as focused day
						$__time = $this->generate_time($date_args);
						
					}
					
					$_event_date_HTML = array(							
						'html_date'=> '<span class="start">'.$DATE_start_val['j'].'<em>'.$DATE_start_val['M'].'</em></span><span class="end"> - '.$DATE_end_val['j'].'</span>',
						'html_time'=>$__time,
						'html_fromto'=> apply_filters('eventon_evt_fe_time', $__from_to),
						'html_prettytime'=> ($__univ_time)? $__univ_time: apply_filters('eventon_evt_fe_ptime', $__prettytime),
						'class_daylength'=>"mul_val",
						'start_month'=>$DATE_start_val['M']
					);	
				}					
			}else{
				/** EVENT TYPE = different start and end months **/
				
				/** EVENT TYPE = start month is before current month **/
				if($CURRENT_month_INT != $DATE_start_val['n']){
					// check all days event
					if($_is_allday){
						$__from_to ="<em class='evcal_alldayevent_text'>(".$evcal_lang_allday.")</em>";
						$__time = "<span class='start'>".$evcal_lang_allday."</span>";						
					}else{
						$__from_to = 
							'('.$DATE_start_val['F'].' '.$DATE_start_val['j'].') '.date($wp_time_format,($event_start_unix)).' - ('.$DATE_end_val['F'].' '.$DATE_end_val['j'].') '.date($wp_time_format,($event_end_unix));
						
						// for daily view check if start day is same as focused day
						$__time = $this->generate_time($date_args);
					}
										
											
				}else{
					/** EVENT TYPE = start month is current month **/
					// check all days event
					if($_is_allday){
						$__from_to ="<em class='evcal_alldayevent_text'>(".$evcal_lang_allday.")</em>";
						$__time = "<span class='start'>".$evcal_lang_allday."</span>";								
					}else{
						$__from_to =
							date($wp_time_format,($event_start_unix)).' - ('.$DATE_end_val['F'].' '.$DATE_end_val['j'].') '.date($wp_time_format,($event_end_unix));
						// for daily view check if start day is same as focused day
						$__time = $this->generate_time($date_args);
					}
				}
				
				
				// check all days event
				if($_is_allday){
					$__prettytime = ucfirst($DATE_start_val['F']) .' '.$DATE_start_val['j'].' ('. ucfirst($DATE_start_val['l']).') - '. ucfirst($DATE_end_val['F']).' '.$DATE_end_val['j'].' ('. ucfirst($DATE_end_val['l']).')';
				}else{
					$__prettytime = 
						ucfirst($DATE_start_val['F']) .' '.$DATE_start_val['j'].' ('. ucfirst($DATE_start_val['l']).') '.date($wp_time_format,($event_start_unix)).' - '. ucfirst($DATE_end_val['F']).' '.$DATE_end_val['j'].' ('.ucfirst($DATE_end_val['l']).') '.date($wp_time_format,($event_end_unix));	
				}
				
				
				$_event_date_HTML = apply_filters('evo_eventcard_dif_SEM', array(
					'html_date'=> '<span class="start">'.$DATE_start_val['j'].'<em>'.$DATE_start_val['M'].'</em></span><span class="end"> - '.$DATE_end_val['j'].'<em>'.$DATE_end_val['M'].'</em></span>',
					'html_time'=>$__time,
					'html_fromto'=> apply_filters('eventon_evt_fe_time', $__from_to),
					'html_prettytime'=> ($__univ_time)? $__univ_time: apply_filters('eventon_evt_fe_ptime', $__prettytime),
					'class_daylength'=>"mul_val",
					'start_month'=>$DATE_start_val['M']
				));
			}



			return $_event_date_HTML;
		}
	
	/**
	 * GENERATE individual event data
	 */
	public function generate_event_data(
		$event_list_array, 
		$focus_month_beg_range='', 
		$FOCUS_month_int='', 
		$FOCUS_year_int=''
	){
		
		
		$months_event_array='';
		
		// Initial variables
		$wp_time_format = get_option('time_format');
		$default_event_color = (!empty($this->evopt1['evcal_hexcode']))? '#'.$this->evopt1['evcal_hexcode']:'#ffa800';
		$__shortC_arg = $this->shortcode_args;

		//echo $__shortC_arg['lang'].'tt';

		// print_r($event_list_array);
		//echo $this->is_eventcard_open;
				
		// EVENT CARD open by default variables		
		$eventcard_styles = ($this->is_eventcard_open || (!empty($__shortC_arg['evc_open']) && $__shortC_arg['evc_open']=='yes' ))? null:"style='display:none'";
		$eventcard_script_class = ($this->is_eventcard_open)? "gmaponload":null;
		
			
		
		// check featured events are prioritized
		$__feature_events = (!empty($__shortC_arg['ft_event_priority']) && $__shortC_arg['ft_event_priority']!='no')?true:false;
		
		
		// GET EventTop fields - v2.1.17
		$eventop_fields = (!empty($this->evopt1['evcal_top_fields']))?$this->evopt1['evcal_top_fields']:null;
		
		
		// eventCARD HTML
		require_once(AJDE_EVCAL_PATH.'/admin/includes/eventon_eventCard.php');

		// check if single event exist
		$_sin_ev_ex  = (in_array( 'eventon-single-event/eventon-single-event.php', get_option( 'active_plugins' ) ) )? true:false;
		
		
		// EACH EVENT
		if(is_array($event_list_array) ){
		foreach($event_list_array as $event):
			//print_r($event);
			$event_id = $event['event_id'];
			$event_start_unix = $event['event_start_unix'];
			$event_end_unix = $event['event_end_unix'];
			$event_type = $event['event_type'];
			$ev_vals = $event['event_pmv'];

			$event = get_post($event_id);
			
			
			
			// define variables
			$ev_other_data = $ev_other_data_top = $html_event_type_info= $_event_date_HTML=$_eventcard='';	
			$_is_end_date=true;
			
			$DATE_start_val=eventon_get_formatted_time($event_start_unix);
			if(empty($event_end_unix)){
				$_is_end_date=false;
				$DATE_end_val= $DATE_start_val;
			}else{
				$DATE_end_val=eventon_get_formatted_time($event_end_unix);
			}

			// if this event featured
			$__featured = (!empty($ev_vals['_featured']) && $ev_vals['_featured'][0]=='yes')? true:false;

			
			// Unique ID generation
			$unique_varied_id = 'evc'.$event_start_unix.(uniqid()).$event_id;
			$unique_id = 'evc_'.$event_start_unix.$event_id;
			
			// All day event variables
			$_is_allday = (!empty($ev_vals['evcal_allday']) && $ev_vals['evcal_allday'][0]=='yes')? true:false;
			$_hide_endtime = (!empty($ev_vals['evo_hide_endtime']) && $ev_vals['evo_hide_endtime'][0]=='yes')? true:false;
			$evcal_lang_allday = eventon_get_custom_language( $this->evopt2,'evcal_lang_allday', 'All Day');
			
			
			/*
				evo_hide_endtime
				NOTE: if its set to hide end time, meaning end time and date would be empty on wp-admin, which will fall into same start end month category.
			*/

				

			$_event_date_HTML = $this->generate_time_($DATE_start_val, $DATE_end_val, $ev_vals, $evcal_lang_allday, $focus_month_beg_range, $FOCUS_month_int, $event_start_unix, $event_end_unix);
			
				
			
		
			// (---) hook for addons
			if(has_filter('eventon_eventcard_date_html'))
				$_event_date_HTML= apply_filters('eventon_eventcard_date_html', $_event_date_HTML, $event_id);
		
			
			
			// EVENT FEATURES IMAGE
				$img_id =get_post_thumbnail_id($event_id);
				if($img_id!=''){				
					$img_src = wp_get_attachment_image_src($img_id,'full');
					$img_thumb_src = wp_get_attachment_image_src($img_id,'thumbnail');
									
					// append to eventcard array
					$_eventcard['ftimage'] = array(
						'img'=>$img_src,
						'hovereffect'=> $this->evopt1['evo_ftimghover'],
						'fullheight'=> (!empty($this->evopt1['evo_ftimg_fullheight'])? $this->evopt1['evo_ftimg_fullheight']: 400),
					);	
									
				}else{		$img_thumb_src='';		}
				
			// EVENT DESCRIPTION
				$evcal_event_content =$event->post_content;
				
				if(!empty($evcal_event_content) ){
					$event_full_description = $evcal_event_content;
				}else{
					// event description compatibility from older versions.
					$event_full_description =(!empty($ev_vals['evcal_description']))?$ev_vals['evcal_description'][0]:null;
				}			
				if(!empty($event_full_description) ){				
					
					$except = $event->post_excerpt;
					$event_excerpt = eventon_get_event_excerpt($event_full_description, 30, $except);
					
					$_eventcard['eventdetails'] = array(
						'fulltext'=>$event_full_description,
						'excerpt'=>$event_excerpt,
					);				
					
				}
			
									
			// EVENT LOCATION
				$lonlat = (!empty($ev_vals['evcal_lat']) && !empty($ev_vals['evcal_lon']) )?
						'latlon="1" latlng="'.$ev_vals['evcal_lat'][0].','.$ev_vals['evcal_lon'][0].'" ': null;
							
				
				$__location = (!empty($ev_vals['evcal_location']))?
					$ev_vals['evcal_location'][0]:null;
				
				// location name
					$__location_name = (!empty($ev_vals['evcal_location_name']))?
						$ev_vals['evcal_location_name'][0]:null;
				
				$_eventcard['timelocation'] = array(
					'timetext'=>$_event_date_HTML['html_prettytime'],
					'location'=>$__location,
					'location_name'=>$__location_name
				);
			
						
			// GOOGLE maps			
				if( ($this->google_maps_load) && !empty($ev_vals['evcal_location']) && (!empty($ev_vals['evcal_gmap_gen']) && $ev_vals['evcal_gmap_gen'][0]=='yes') ){
					
					$gmap_api_status='';				
					$_eventcard['gmap'] = array(
						'id'=>$unique_varied_id,
					);
					
					
					// GET directions
					if($this->evopt1['evo_getdir']=='yes'){
						$_eventcard['getdirection'] = array(
							'fromaddress'=>$ev_vals['evcal_location'][0],
						);
					}
									
				}else{	$gmap_api_status = 'data-gmap_status="null"';	}
				
			
			// EVENT BRITE
			// check if eventbrite actually used in this event
				if(!empty($ev_vals['evcal_eventb_data_set'] ) && $ev_vals['evcal_eventb_data_set'][0]=='yes'){			
					// Event brite capacity
					if( 
						!empty($ev_vals['evcal_eventb_tprice'] ) &&				
						!empty($ev_vals['evcal_eventb_url'] ) )
					{					
						
						$_eventcard['eventbrite'] = array(
							'capacity'=>(( !empty($ev_vals['evcal_eventb_capacity']))?$ev_vals['evcal_eventb_capacity'][0]:null),
							'tix_price'=>$ev_vals['evcal_eventb_tprice'][0],
							'url'=>$ev_vals['evcal_eventb_url'][0]
						);
						
					}				
				}
			
			
			// PAYPAL Code
				if(!empty($ev_vals['evcal_paypal_item_price'][0]) && $this->evopt1['evcal_paypal_pay']=='yes'){
					
					$_eventcard['paypal'] = array(
						'title'=>$event->post_title,
						'price'=>$ev_vals['evcal_paypal_item_price'][0],
						'text'=> (!empty($ev_vals['evcal_paypal_text'])? $ev_vals['evcal_paypal_text'][0]: null),
					);
					
				}			
			
			// Event Organizer
				if(!empty($ev_vals['evcal_organizer'] )){
					
					$_eventcard['organizer'] = array(
						'value'=>$ev_vals['evcal_organizer'][0]
					);			
					
				}
						
			// Custom fields
				for($x =1; $x<4; $x++){
					if( !empty($this->evopt1['evcal_ec_f'.$x.'a1']) && !empty($this->evopt1['evcal__fai_00c'.$x])	&& !empty($ev_vals["_evcal_ec_f".$x."a1_cus"])	){
						
						// check if hide this from eventCard set to yes
						if(empty($this->evopt1['evcal_ec_f'.$x.'a3']) || $this->evopt1['evcal_ec_f'.$x.'a3']=='no'){

							$faicon = $this->evopt1['evcal__fai_00c'.$x];
							
							$_eventcard['customfield'.$x] = array(
								'imgurl'=>$faicon,
								'x'=>$x,
								'value'=>$ev_vals["_evcal_ec_f".$x."a1_cus"][0],
								'valueL'=>( (!empty($ev_vals["_evcal_ec_f".$x."a1_cusL"]))?
									$ev_vals["_evcal_ec_f".$x."a1_cusL"][0]:null ),
								'_target'=>( (!empty($ev_vals["_evcal_ec_f".$x."_onw"]))?
									$ev_vals["_evcal_ec_f".$x."_onw"][0]:null ),
								'type'=>$this->evopt1['evcal_ec_f'.$x.'a2']
							);
						}
					}
				}
						
			// LEARN MORE and ICS
				if(!empty($ev_vals['evcal_lmlink']) || !empty($this->evopt1['evo_ics']) && $this->evopt1['evo_ics']=='yes'){
					$_eventcard['learnmoreICS'] = array(						
						'event_id'=>$event_id,
						'learnmorelink'=>( (!empty($ev_vals['evcal_lmlink']))? $ev_vals['evcal_lmlink'][0]: null),
						'learnmore_target'=> ((!empty($ev_vals['evcal_lmlink_target'])  && $ev_vals['evcal_lmlink_target'][0]=='yes')? 'target="_blank"':null),
						'estart'=> ($event_start_unix),
						'eend'=>($event_end_unix),
						'etitle'=>$event->post_title,
						'evals'=>$ev_vals,
					);
				}
			
			//print_r($_eventcard);
			
			// =======================
			/** CONSTRUCT the EVENT CARD	 **/		
				if(!empty($_eventcard) && count($_eventcard)>0){
					
					// filter hook for eventcard content array
					$_eventcard = apply_filters('eventon_eventcard_array', $_eventcard, $ev_vals, $event_id);

					// if an order is set reorder things
					$_eventcard = eventon_EVC_sort($_eventcard, $this->evopt1['evoCard_order']);
					
					ob_start();
				
					echo "<div class='event_description evcal_eventcard' ".$eventcard_styles.">";
					
					echo  eventon_eventcard_print($_eventcard, $this->evopt1, $this->evopt2);
					
					
					// (---) hook for addons
					if(has_action('eventon_eventcard_additions')){
						do_action('eventon_eventcard_additions', $event_id, $this->__calendar_type, $event->post_title, $event_full_description, $img_thumb_src);
					}
				
					echo "</div>";
					
					$html_event_detail_card = ob_get_clean();				
					
				}else{
					$html_event_detail_card=null;
				}
			
			
			
			/** Trigger attributes **/
			$event_description_trigger = (!empty($html_event_detail_card))? "desc_trig":null;
			$gmap_trigger = (!empty($ev_vals['evcal_gmap_gen']) && $ev_vals['evcal_gmap_gen'][0]=='yes')? 'data-gmtrig="1"':'data-gmtrig="0"';

				
			//event type taxonomies #1
			$evcal_terms = wp_get_post_terms($event_id,'event_type');
				$term_class ='';
				if($evcal_terms){
					
					$html_event_type_info .="<span class='evcal_event_types'><em><i>".$this->lang_array['et'].":</i></em>";
					foreach($evcal_terms as $termA):
						$term_class = ' evo_'.$termA->slug;
						$html_event_type_info .="<em>".$termA->name."</em>";
					endforeach; 
					$html_event_type_info .="<i class='clear'></i></span>";
				}
			
			//event color	
				$event_color = (!empty($ev_vals['evcal_event_color']) )?
				(( strpos($ev_vals["evcal_event_color"][0] ,'#'))?
						$ev_vals["evcal_event_color"][0]:
						'#'.$ev_vals["evcal_event_color"][0] )
					: $default_event_color;

				// override event colors
				if(!empty($__shortC_arg['etc_override']) && $__shortC_arg['etc_override']=='yes' && $evcal_terms){
					$ev_id = $evcal_terms[0]->term_id;
					$ev_color = get_option( "evo_et_taxonomy_$ev_id" );

					$event_color = (!empty($ev_color['et_color']))? '#'.$ev_color['et_color']:$event_color;
				}		


			
			// event ex link
			$exlink_option = (!empty($ev_vals['_evcal_exlink_option']) )?$ev_vals['_evcal_exlink_option'][0]:1;
			$event_permalink = get_permalink($event_id);
			
			// if UX to be open in new window then use link to single event or that link
			$_lang_ext = (!empty($__shortC_arg['lang']) && $__shortC_arg['lang']!='L1')?
				'?l='.$__shortC_arg['lang']:null;

			$href = (!empty($ev_vals['evcal_exlink']) && $exlink_option!='1' )? 
				'data-exlk="1" href="'.$ev_vals['evcal_exlink'][0].$_lang_ext.'"'
				:'data-exlk="0"';

			// target
			$target_ex = (!empty($ev_vals['_evcal_exlink_target'])  && $ev_vals['_evcal_exlink_target'][0]=='yes')?
				'target="_blank"':null;
			
			
			
			// EVENT LOCATION
				if(!empty($ev_vals['evcal_location'])){
					$event_location_variables = ((!empty($lonlat))? $lonlat:null ). ' add_str="'.$ev_vals['evcal_location'][0].'" ';
					
					// conditional schema data for event
					if(!empty($this->evopt1['evo_schema']) && $this->evopt1['evo_schema']=='yes'){
						$__scheme_data_location ='';
					}else{
						$__scheme_data_location = '
							<item style="display:none" itemprop="location" itemscope itemtype="http://schema.org/Place">
								<span itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
									<item itemprop="streetAddress">'.$ev_vals['evcal_location'][0].'</item>
								</span>
							</item>';
					}
						
					$ev_location =				
						'<em class="evcal_location" '.( (!empty($lonlat))? $lonlat:null ).' add_str="'.$ev_vals['evcal_location'][0].'">'.$ev_vals['evcal_location'][0].'</em>';
				}else{
					$ev_location = $event_location_variables= $__scheme_data_location= null;
				}
				// location name 
				$event_location_variables .= (!empty($ev_vals['evcal_location_name']))? 'data-location_name="'.$ev_vals['evcal_location_name'][0].'"':null;

			
			/* -------------------
			// 	HTML		
			// 	EventTop - building of the eventTop section
			-------------*/
			$eventtop_html=$eventop_fields_='';
				
				// featured image
				$eventtop_html[] = (!empty($img_thumb_src) && !empty($__shortC_arg['show_et_ft_img']) && $__shortC_arg['show_et_ft_img']=='yes')? 
				"<span class='ev_ftImg' style='background-image:url(".$img_thumb_src[0].")'></span>":null;
				
				// CHECK for event top fields array
				$eventop_fields_ = (is_array($eventop_fields) )? true:false;
				
				
				// date number 
				$___day_name = ($eventop_fields_ && in_array('dayname',$eventop_fields))?
						"<em class='evo_day' >".$DATE_start_val['D']."</em>": null;

				$eventtop_html[]="<span class='evcal_cblock' bgcolor='".$event_color."' smon='".$DATE_start_val['F']."' syr='".$DATE_start_val['Y']."'><em class='evo_date' >".$___day_name.$_event_date_HTML['html_date'].'</em>';
				

				//print_r($DATE_start_val);
				// month name
				/*
				if($eventop_fields_ && in_array('monthname',$eventop_fields) || ( !empty($this->shortcode_args['etop_month']) && $this->shortcode_args['etop_month']=='yes') )
					$eventtop_html[]="<em class='evo_month' mo='".$DATE_start_val['M']."'>".$DATE_start_val['M']."</em>";
				*/
				
				// time for events
					$eventtop_html[]= "<em class='evo_time'>".$_event_date_HTML['html_time']."</em>";
				
				$eventtop_html[]="<em class='clear'></em></span>";
				
				// event title
				$eventtop_html[]= "<span class='evcal_desc' {$event_location_variables}><span class='evcal_desc2 evcal_event_title' itemprop='name'>".$event->post_title."</span>";
				
				$eventtop_html[]= "<span class='evcal_desc_info' >";
				
				// time
				if($eventop_fields_ && in_array('time',$eventop_fields))
					$eventtop_html[]= "<em class='evcal_time'>".$_event_date_HTML['html_fromto']."</em> ";
				
				// location
				if($eventop_fields_ && in_array('location',$eventop_fields))
					$eventtop_html[]= $ev_location;

				// location Name
				if($eventop_fields_ && in_array('locationame',$eventop_fields)){
					$__location_name = (!empty($ev_vals['evcal_location_name']))?
						$ev_vals['evcal_location_name'][0]:null;
					$eventtop_html[]= !empty($__location_name)?'<em class="evcal_location event_location_name">'.$__location_name.'</em>':null;
				}
				
				$eventtop_html[]= "</span><span class='evcal_desc3'>";
				
					// organizer
					if($eventop_fields_ && in_array('organizer',$eventop_fields) && !empty($ev_vals['evcal_organizer']))
						$eventtop_html[]= "<em class='evcal_oganizer'><i>".( eventon_get_custom_language( $this->evopt2,'evcal_evcard_org', 'Event Organized By')  ).':</i> '.$ev_vals['evcal_organizer'][0]."</em>";
					
					// event type
					if($eventop_fields_ && in_array('eventtype',$eventop_fields))
						$eventtop_html[]= $html_event_type_info;

					// custom meta fields
					$num = evo_retrieve_cmd_count($this->evopt1);
					for($x=1; $x<$num+1; $x++){
						if($eventop_fields_ && in_array('cmd'.$x,$eventop_fields) 
							&& !empty($ev_vals['_evcal_ec_f'.$x.'a1_cus'])){

							$def = $this->evopt1['evcal_ec_f'.$x.'a1']; // default custom meta field name
							$i18n_nam = eventon_get_custom_language( $this->evopt2,'evcal_cmd_'.$x, $def);

							$eventtop_html[]= ( ($x==1)? "<b class='clear'></b>":null )."<em class='evcal_cmd'><i>".$i18n_nam.':</i> '.$ev_vals['_evcal_ec_f'.$x.'a1_cus'][0]."</em> ";
						}
					}
				
				$eventtop_html[]= "</span>";
				$eventtop_html[]= "</span><em class='clear'></em>";
			
				$eventtop_html = apply_filters('eventon_eventtop_html',$eventtop_html);
			// --
			
			
			// Combine the event top individual sections
			$html_info_line = implode('', $eventtop_html);
			
			
			
			// (---) hook for addons
			if(has_filter('eventon_event_cal_short_info_line') ){
				$html_info_line = apply_filters('eventon_event_cal_short_info_line', $html_info_line);
			}
			
			
			// SCHEME SEO
				// conditional schema data
				if(!empty($this->evopt1['evo_schema']) && $this->evopt1['evo_schema']=='yes'){
					$__scheme_data ='<div class="evo_event_schema" style="display:none" >
						<a href="'.$event_permalink.'"></a></div>';
					$__scheme_attributes = '';
				}else{
					$event_permalink = ($_sin_ev_ex)? $event_permalink: "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];
					$__scheme_data = 
						'<div class="evo_event_schema" style="display:none" >
						<a href="'.$event_permalink.'" itemprop="url"></a>				
						<time itemprop="startDate" datetime="'.$DATE_start_val['Y'].'-'.$DATE_start_val['n'].'-'.$DATE_start_val['j'].'"></time>
						<time itemprop="endDate" datetime="'.$DATE_end_val['Y'].'-'.$DATE_end_val['n'].'-'.$DATE_end_val['j'].'"></time>'.
						$__scheme_data_location.
						'</div>';
					$__scheme_attributes = "itemscope itemtype='http://schema.org/Event'";
				}
			
			
			
			// ## Eventon Calendar events list -- single event
			
			// CLASES - attribute
			$_ft_imgClass = (!empty($img_thumb_src) && !empty($__shortC_arg['show_et_ft_img']) && $__shortC_arg['show_et_ft_img']=='yes')? 'hasFtIMG':null;
			$__attr_class = "evcal_list_a ".$event_description_trigger." "
				.	$_event_date_HTML['class_daylength']." ".(($event_type!='nr')?'event_repeat ':null). $eventcard_script_class.$_ft_imgClass;
			$_ft_event = ($__feature_events && !empty($ev_vals['_featured']) && $ev_vals['_featured'][0]=='yes')?' ft_event ':null;
			
			// class attribute for event
			$__a_class = $__attr_class.$_ft_event.$term_class. ( ($__featured)? ' featured_event':null) ;
			
			
			// div or an e tag
			$html_tag = ($exlink_option=='1')? 'div':'a';

			$event_html_code="<div id='event_{$event_id}' class='eventon_list_event' event_id='{$event_id}' {$__scheme_attributes}>{$__scheme_data}
			<{$html_tag} id='".$unique_id."' class='".$__a_class."' ".$href." ".$target_ex." style='border-color: ".$event_color."' ".$gmap_trigger." ".(!empty($gmap_api_status)?$gmap_api_status:null)." data-ux_val='{$exlink_option}'>{$html_info_line}</{$html_tag}>".$html_event_detail_card."<div class='clear'></div></div>";	
			
			//evc_open
			
			// prepare output
			$months_event_array[]=array(
				'event_id'=>$event_id,
				'srow'=>$event_start_unix,
				'erow'=>$event_end_unix,
				'content'=>$event_html_code
			);
			
			
		endforeach;
		
		}else{
			$months_event_array;
		}
		
		return $months_event_array;
	}
	
	/**	 Add other filters to wp_query argument	 */
		public function apply_evo_filters_to_wp_argument($wp_arguments, $ecv){
			// -----------------------------
			// FILTERING events	
			
			// values from filtering events
			if(!empty($ecv['filters'])){			
				
				// build out the proper format for filtering with WP_Query
				$cnt =0;
				$filter_tax['relation']='AND';
				foreach($ecv['filters'] as $filter){
					if($filter['filter_type']=='tax'){					
						
						$filter_val = explode(',', $filter['filter_val']);
						$filter_tax[] = array(
							'taxonomy'=>$filter['filter_name'],
							'field'=>'id',
							'terms'=>$filter_val						
						);
						$cnt++;
					}else{				
						$filter_meta[] = array(
							'key'=>$filter['filter_name'],				
							'value'=>$filter['filter_val'],				
						);
					}				
				}
				
				
				if(!empty($filter_tax)){
					
					// for multiple taxonomy filtering
					if($cnt>1){					
						$filters_tax_wp_argument = array(
							'tax_query'=>$filter_tax
						);
					}else{
						$filters_tax_wp_argument = array(
							'tax_query'=>$filter_tax
						);
					}
					$wp_arguments = array_merge($wp_arguments, $filters_tax_wp_argument);
				}
				if(!empty($filter_meta)){
					$filters_meta_wp_argument = array(
						'meta_query'=>$filter_meta
					);
					$wp_arguments = array_merge($wp_arguments, $filters_meta_wp_argument);
				}		
			}else{

				
				// each event type category 
				foreach($this->get_event_types() as $ety=>$event_type){	
					
					if(!empty($ecv[$event_type]) && $ecv[$event_type] !='all'){
						$ev_type = explode(',', $ecv['event_type'.$x]);
						$ev_type_ar = array(
								'tax_query'=>array( 
								array('taxonomy'=>'event_type'.$x,'field'=>'id','terms'=>$ev_type) )	
							);
						
						$wp_arguments = array_merge($wp_arguments, $ev_type_ar);
					}
					
				}

				
				
			}
			
			//print_r($wp_arguments);
			return $wp_arguments;
		}
	
	/**	 out put just the sort bar for the calendar	 */
		public function eventon_get_cal_sortbar($args, $sortbar=true){
			
			// define variable values	
			$sorting_options = (!empty($this->evopt1['evcal_sort_options']))?$this->evopt1['evcal_sort_options']:null;
			$filtering_options = (!empty($this->evopt1['evcal_filter_options']))?$this->evopt1['evcal_filter_options']:array();
			$content='';
				

			// START the magic	
			ob_start();
			
			// IF sortbar is set to be shown
			if($sortbar){
				echo ( $this->evcal_hide_sort!='yes' )? "<a class='evo_sort_btn'>".eventon_get_custom_language($this->evopt2, 'evcal_lang_sopt','Sort Options')."</a>":null;
			}

			// expand sort section by default or not
				$SO_display = (!empty($args['exp_so']) && $args['exp_so'] =='yes')? 'block': 'none';
			
			echo "<div class='eventon_sorting_section' style='display:{$SO_display}'>";
			if( $this->evcal_hide_sort!='yes' ){ // if sort bar is set to show	
			
			// sorting section
				$evsa1 = array(	'date'=>'Date', 'title'=>'Title','color'=>'Color');
				$sort_options = array(	1=>'sort_date', 'sort_title','sort_color');

					$__sort_key = substr($args['sort_by'], 5);

				echo "
				<div class='eventon_sort_line evo_sortOpt' >
					<div class='evo_sortby'><p>".eventon_get_custom_language($this->evopt2, 'evcal_lang_sort','Sort By').":</p></div>
					<div class='evo_srt_sel'><p class='fa'>".eventon_get_custom_language($this->evopt2, 'evcal_lang_s'.$__sort_key,$__sort_key)."</p>
						<div class='evo_srt_options'>";
					$cnt =1;
					if(is_array($sorting_options) ){
						foreach($evsa1 as $so=>$sov){
							if(in_array($so, $sorting_options) || $so=='date' ){
							echo "<p val='sort_".$so."' type='".$so."' class='evs_btn ".( ($args['sort_by'] == $sort_options[$cnt])? 'evs_hide':null)."' >"
									.eventon_get_custom_language($this->evopt2, 'evcal_lang_s'.$so,$sov)
									."</p>";						
							}
							$cnt++;
						}
					}
						echo "</div>
					</div>";
				

				echo "<div class='clear'></div>
				</div>";

			
			}
			
			$__text_all = eventon_get_custom_language($this->evopt2, 'evcal_lang_all', 'All');
			// filtering section
			echo "
				<div class='eventon_filter_line'>";
				

				// foreach event type
				foreach($this->get_event_types() as $ety=>$event_type){	
					$ety_ = ($ety==1)? '':$ety;
					if( !empty($args[$event_type])){
						// event_type line
						if(in_array($event_type, $filtering_options) && $args[$event_type]=='all'){

							echo "
							<div class='eventon_filter evo_sortOpt' filter_field='{$event_type}' filter_val='all' filter_type='tax' >
								<div class='eventon_sf_field'><p>".$this->lang_array['et'.$ety_].":</p></div>				
							
								<div class='eventon_filter_selection'>
									<p class='filtering_set_val' opts='evs4_in'>{$__text_all}</p>
									<div class='eventon_filter_dropdown' style='display:none'>";
								
									$cats = get_categories(array( 'taxonomy'=>$event_type));
									
									echo "<p class='evf_hide' filter_val='all'>{$__text_all}</p>";
									foreach($cats as $ct){
										echo "<p  filter_val='".$ct->term_id."' filter_slug='".$ct->slug."'>".$ct->name."</p>";
									}				
								echo "</div>
								</div><div class='clear'></div>
							</div>";
						}else if($args[$event_type]!='all'){
							echo "<div class='eventon_filter' filter_field='{$event_type}' filter_val='{$args[$event_type]}' filter_type='tax'></div>";
						}
					}

				}


				
				// (---) Hook for addon
				if(has_action('eventon_sorting_filters')){
					echo  do_action('eventon_sorting_filters', $content);
				}
					
				echo "</div>"; // #eventon_filter_line

				echo "<div class='clear'></div>"; // clear
			
			echo "</div>"; // #eventon_sorting_section
			
			// (---) Hook for addon
			if(has_action('eventon_below_sorts')){
				echo  do_action('eventon_below_sorts', $content);
			}
			
			// load bar for calendar
			echo "<div id='eventon_loadbar_section'><div id='eventon_loadbar'></div></div>";		

			// (---) Hook for addon
			if(has_action('eventon_after_loadbar')){
				echo  do_action('eventon_after_loadbar', $content);
			}	
			
			
			return ob_get_clean();
		}
		

	
	
} // class EVO_generator


?>