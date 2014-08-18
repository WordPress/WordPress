<?php
/*
 Plugin Name: EventON - Event Lists
 Plugin URI: http://www.myeventon.com/
 Description: Create past and upcoming event lists for eventON
 Author: Ashan Jay
 Version: 0.3
 Author URI: http://www.ashanjay.com/
 Requires at least: 3.7
 Tested up to: 3.8.1

 */


 
class eventon_event_lists{
	
	public $version='0.3';
	public $eventon_version = '2.2.12';
	public $name = 'EventLists';
		
	public $is_running_el =false;
	
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	public $template_url ;
	
	public $shortcode_args;
	private $shortcode_atts = array();
	
	/*
	 * Construct
	 */
	public function __construct(){
		
		$this->super_init();

		// get eventon addon class file url if exists
		$this->urls = get_option('eventon_addon_urls');	
		$url = (!empty($this->urls))? $this->urls['addons']: AJDE_EVCAL_PATH.'/classes/class-evo-addons.php';
		if(file_exists($url)){
				
			include_once( $url);
			$this->addon = new evo_addon($this->addon_data);

			// if addon class exists
			if($this->addon->requirment_check()){
		
				add_action( 'init', array( $this, 'init' ), 0 );				
		
				// scripts and styles 
			
				// HOOKs		
			
				$this->includes();
			}
		}else{
			// if we cant find eventon addon class file show admin notice
			add_action('admin_notices', array($this, '_no_eventon_warning'));
		}
	}
	
	
	// SUPER init
		function super_init(){
			// PLUGIN SLUGS			
			$this->addon_data['plugin_url'] = path_join(WP_PLUGIN_URL, basename(dirname(__FILE__)));
			$this->addon_data['plugin_slug'] = plugin_basename(__FILE__);
			list ($t1, $t2) = explode('/', $this->addon_data['plugin_slug'] );
	        $this->addon_data['slug'] = $t1;
	        $this->addon_data['plugin_path'] = dirname( __FILE__ );
	        $this->addon_data['evo_version'] = $this->eventon_version;
	        $this->addon_data['version'] = $this->version;
	        $this->addon_data['name'] = $this->name;

	        $this->plugin_url = $this->addon_data['plugin_url'];
	        $this->plugin_slug = $this->addon_data['plugin_slug'];
	        $this->slug = $this->addon_data['slug'];
	        $this->plugin_path = $this->addon_data['plugin_path'];
		}

	// INITIATE please
	function init(){
				
		// Activation
		$this->activate();		
		
		// Deactivation
		register_deactivation_hook( __FILE__, array($this,'deactivate'));


		// RUN addon updater only in dedicated pages
		if ( is_admin() ){
			$this->addon->updater();			
		}

		$this->shortcodes = new evo_el_shortcode();
		
	}
	


	/**
	 * Include required core files.
	 */
	function includes(){
		include_once( 'admin/eventonEL_shortcode.php' );
		
		
		if ( defined('DOING_AJAX') ){
			//include_once( 'admin/eventonEL_ajax.php' );
		}
	}
	

	

			
	/**
	 *	MAIN Function to generate the calendar outter shell
	 *	for event lists
	 */
	public function generate_eventon_el_calendar($args, $type=''){
		
		global $eventon;

		
		$this->only_el_actions();
			
		// CUT OFF time calculation
			//fixed time list
			if(!empty($args['pec']) && $args['pec']=='ft'){
				$__D = (!empty($args['fixed_date']))? $args['fixed_date']:date("j", current_time('timestamp'));
				$__M = (!empty($args['fixed_month']))? $args['fixed_month']:date("m", current_time('timestamp'));
				$__Y = (!empty($args['fixed_year']))? $args['fixed_year']:date("Y", current_time('timestamp'));

				$current_timestamp = mktime(0,0,0,$__M,$__D,$__Y);

			// current date cd
			}else if(!empty($args['pec']) && $args['pec']=='cd'){
				$current_timestamp = strtotime( date("m/j/Y", current_time('timestamp')) );
			}else{// current time ct
				$current_timestamp = current_time('timestamp');
			}
			// reset arguments
			$args['fixed_date']= $args['fixed_month']= $args['fixed_year']='';

		
		// restrained time unix
			$number_of_months = (!empty($args['number_of_months']))? (int)($args['number_of_months']):0;
			$month_dif = ($args['el_type']=='ue')? '+':'-';
			$unix_dif = strtotime($month_dif.($number_of_months-1).' months', $current_timestamp);

			$restrain_monthN = ($number_of_months>0)?				
				date('n',  $unix_dif):
				date('n',$current_timestamp);


			$restrain_year = ($number_of_months>0)?				
				date('Y', $unix_dif):
				date('Y',$current_timestamp);
			

		// upcoming events list 
			if($args['el_type']=='ue'){
				$restrain_day = date('t', mktime(0, 0, 0, $restrain_monthN+1, 0, $restrain_year));

				$__focus_start_date_range = $current_timestamp;
				$__focus_end_date_range =  mktime(23,59,59,($restrain_monthN),$restrain_day, ($restrain_year));
				
				//echo 'tt'.$restrain_monthN.' '.$restrain_day.' '.$number_of_months;
			// past events list
			}else{

				if(!empty($args['event_order']))
					$args['event_order']='DESC';

				$args['hide_past']='no';
				
				$__focus_start_date_range =  mktime(0,0,0,($restrain_monthN),1, ($restrain_year));
				$__focus_end_date_range = $current_timestamp;

				//echo 'tt'.$restrain_monthN.' '.$restrain_day.' '.$number_of_months;
			}

		
		
		// Add extra arguments to shortcode arguments
		$new_arguments = array(
			'focus_start_date_range'=>$__focus_start_date_range,
			'focus_end_date_range'=>$__focus_end_date_range,
		);

		//echo $args['eTop_month'];
		//print_r($args);
		$args = (!empty($args) && is_array($args))? 
			wp_parse_args($new_arguments, $args): $new_arguments;
		
		
		// PROCESS variables
		$args__ = $eventon->evo_generator->process_arguments($args, true);
		$this->shortcode_args=$args__;
		
		//print_r($args__);
		$this->add_shortcode_atts_to_header(array(
			'etop_month'=>$args__['etop_month']
		));

		// ==================
		$content =$eventon->evo_generator->calendar_shell_header(
			array(
				'month'=>$restrain_monthN,'year'=>$restrain_year, 
				'date_header'=>false,
				'sort_bar'=>true,
				'date_range_start'=>$__focus_start_date_range,
				'date_range_end'=>$__focus_end_date_range,
				'title'=>$args['el_title'],
				'send_unix'=>true
			)
		);
		$content .=$eventon->evo_generator->eventon_generate_events($args__);
		$content .=$eventon->evo_generator->calendar_shell_footer();

		$this->remove_only_el_actions();
		
		return  $content;	

		
	}


	

	// SUPPROT FUNCTIONS

		function add_shortcode_atts_to_header($array_n){
			$this->shortcode_atts = $array_n;
			add_filter('eventon_calhead_shortcode_args',array($this, 'process_shortcode_atts'), 10, 1);
		}
		function process_shortcode_atts($array){
			$new_array = $this->shortcode_atts;

			$array = wp_parse_args($new_array, $array);
			return $array;
		}

		// ONLY for el calendar actions 
		public function only_el_actions(){
			add_filter('eventon_cal_class', array($this, 'eventon_cal_class'), 10, 1);	
		}
		public function remove_only_el_actions(){
			//add_filter('eventon_cal_class', array($this, 'remove_eventon_cal_class'), 10, 1);
			remove_filter('eventon_cal_class', array($this, 'eventon_cal_class'));	
			
		}
		// add class name to calendar header for DV
		function eventon_cal_class($name){
			$name[]='evoEL';
			return $name;
		}
		// add class name to calendar header for DV
		function remove_eventon_cal_class($name){
			if(($key = array_search('evoEL', $name)) !== false) {
			    unset($name[$key]);
			}
			return $name;
		}

		/**	Styles for the tab page	 */	
			public function register_styles_scripts(){		
				
				wp_register_style( 'evo_el_styles',$this->plugin_url.'/assets/el_styles.css');
				wp_register_script('evo_el_script',$this->plugin_url.'/assets/el_script.js', array('jquery'), 1.0, true );	

				if(has_eventon_shortcode('add_eventon_el')){
					// LOAD JS files
					$this->print_scripts();
						
				}
				add_action( 'wp_enqueue_scripts', array($this,'print_styles' ));
					
			}
			public function print_scripts(){					
				wp_enqueue_script('evo_el_script');	
			}

			function print_styles(){
				wp_enqueue_style( 'evo_el_styles');	
			}

		

	// SECONDARY FUNCTIONS	
		

		// ACTIVATION
			function activate(){
				// add actionUser addon to eventon addons list
				$this->addon->activate();
			}
			

			// DISPLAY Warning
			function _no_eventon_warning(){
		        ?>
		        <div class="message error"><p><?php printf(__('EventON %s is enabled but not effective. It requires <a href="%s">EventON</a> in order to work.', 'eventon'), $this->name, 
		            'http://www.myeventon.com/'); ?></p></div>
		        <?php
		    }
		   
		
			// Deactivate addon
			function deactivate(){
				$this->addon->remove_addon();
			}
	
}

// Initiate this addon within the plugin
$GLOBALS['eventon_el'] = new eventon_event_lists();


// php tag
function add_eventon_el($args='') {
	global $eventon_el, $eventon;
	
	/*
	// connect to support arguments
	$supported_defaults = $eventon->evo_generator->get_supported_shortcode_atts();
	
	$args = shortcode_atts( $supported_defaults, $args ) ;
	*/
	
	$content = $eventon_el->generate_eventon_el_calendar($args, 'php');
	
	echo $content;
}


?>