<?php
/*
 * Plugin Name: EventON - Single Event 
 * Plugin URI: http://www.myeventon.com/single-event
 * Description: Add more power to a single event in EventON
 * Version: 0.12
 * Author: AshanJay
 * Author URI: http://www.ashanjay.com
 * Requires at least: 3.7
 * Tested up to: 3.8.1
 */

define('EVO_SIN_EV',true);
	
 
class EventON_sin_event{
	
	public $version='0.12';
	public $eventon_version = '2.2.12';
	public $name = 'SingleEvent';
	
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	
	public $is_single_event = false;

	private $evo_opt='';

	
	
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

				$this->evo_opt = get_option('evcal_options_evcal_1');
				
				// template loading
				add_filter('eventon_template_paths', array( $this, 'add_new_template_load_path' ) ,10,1);
				
				add_filter('eventon_settings_tab1_arr_content', array( $this, 'single_event_settings' ) ,10,1 );		
				add_filter('eventon_register_post_type_ajde_events', array( $this, 'activate_comments' ) ,10,1 );	
					
				
				add_filter('eventon_eventcard_additions', array( $this, 'add_social_media_to_eventcard' ) ,10,5 );
				
				//add_action('eventon_metab1_end', array( $this, 'event_meta_settings' ) );		
				add_action('evcal_ui_click_additions', array( $this, 'event_meta_settings' ) );		
				add_action('eventon_admin_post_script', array( $this, 'event_meta_post_script' ) );
						
				// backend
				add_action( 'admin_enqueue_scripts', array( $this, 'backend_scripts' ) );	

				// Installation
				register_activation_hook( __FILE__, array( $this, 'activate' ) );		
				
				add_action( 'init', array( $this, 'register_scripts' ) ,16);
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
		
				
		// Re-activate the permalinks on events
		remove_filter('get_sample_permalink_html','eventon_perm',10,4);
		
		$this->register_se_sidebar();
		
		
		// Deactivation
		register_deactivation_hook( __FILE__, array($this,'deactivate'));


		// RUN addon updater only in dedicated pages
		if ( is_admin() ){
			$this->addon->updater();			
		}

		$this->shortcodes = new evo_se_shortcode();
		
	}
	
	

	/**
	 * Include required core files.
	 */
	function includes(){
		include_once( 'admin/eventonSE_shortcode.php' );

		if ( is_admin() )
			include_once( 'admin/admin-init.php' );

		if ( defined('DOING_AJAX') ){
			include_once( 'admin/eventonSE_ajax.php' );
		}
	}
	
	
	
	
	
	// ADD SOcial media to event card
	function add_social_media_to_eventcard($event_id, $cal_type, $title, $event_full_description, $img){
		global $eventon;

		$__calendar_type = $eventon->evo_generator->__calendar_type;
		$evo_opt = $this->evo_opt;

		// check if social media to show or not
		if( (!empty($evo_opt['evosm_som']) && $evo_opt['evosm_som']=='yes' && $__calendar_type=='single') || ( empty($evo_opt['evosm_som']) ) || ( !empty($evo_opt['evosm_som']) && $evo_opt['evosm_som']=='no' ) ){
		
			$post_title = $title;
					
			$permalink 	= urlencode(get_permalink($event_id));
			$permalinkCOUNT 	= get_permalink($event_id);
			$title 		= str_replace('+','%20',urlencode($post_title));
			$titleCOUNT = $post_title;
			$summary = (!empty($event_full_description)? urlencode(eventon_get_normal_excerpt($event_full_description, 16)): '--');
			$imgurl = (!empty($img))? urlencode($img[0]):'';
			
			//$app_id = '486365788092310';
			// social media array

			$fb_js = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;";
			$tw_js = "javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');return false;";

			$social_sites = apply_filters('evo_se_social_media', array(
				
				
				//<div class="fb-like" data-href="PERMALINKCOUNT" data-width="450" data-show-faces="true" data-send="true"></div>
				
				
				'FacebookShare'    => array(
					'key'=>'eventonsm_fbs',
					'counter' =>1,
					'favicon' => 'likecounter.png',
					'url' => '<a class="fb evo_ss" target="_blank" 
						onclick="'.$fb_js.'"
						href="http://www.facebook.com/sharer.php?s=100&p[url]=PERMALINK&p[title]=TITLE&p[summary]=SUMMARY&display=popup" ><i class="fa fa-facebook"></i></a>',
				),
				'Twitter'    => array(
					'key'=>'eventonsm_tw',
					'counter' =>1,
					'favicon' => 'twitter.png',
					'url' => '<a class="tw evo_ss" onclick="'.$tw_js.'" href="http://twitter.com/share?original_referer=PERMALINK&text=TITLECOUNT" title="Share on Twitter" rel="nofollow" target="_blank"><i class="fa fa-twitter"></i></a>',
				),
				'LinkedIn'=> array(
					'key'=>'eventonsm_ln',
					'counter'=>1,'favicon' => 'linkedin.png',
					'url' => '<a class="li evo_ss" href="http://www.linkedin.com/shareArticle?mini=true&url=PERMALINKCOUNT&title=TITLE&summary=SUMMARY" target="_blank"><i class="fa fa-linkedin"></i></a>',
				),
				'Google' => Array (
					'key'=>'eventonsm_gp',
					'counter' =>1,'favicon' => 'google.png',
					'url' => '<a class="gp evo_ss" href="https://plus.google.com/share?url=PERMALINKCOUNT" target="_blank"><i class="fa fa-google-plus"></i></a>'
				),
				'Pinterest' => Array (
					'key'=>'eventonsm_pn',
					'counter' =>1,'favicon' => 'pinterest.png',
					'url' => '<a class="pn evo_ss" href="http://www.pinterest.com/pin/create/button/?url=PERMALINK&media=IMAGEURL&description=SUMMARY"
				        data-pin-do="buttonPin" data-pin-config="above" target="_blank"><i class="fa fa-pinterest"></i></a>'
				)
				
			));
			
			$sm_count = 0;
			$output_sm='';
			
			foreach($social_sites as $sm_site=>$sm_site_val){
				
				if(!empty($evo_opt[$sm_site_val['key']]) && $evo_opt[$sm_site_val['key']]=='yes'){
					
					if( ($sm_site=='Pinterest' && !empty($imgurl) ) || $sm_site!='Pinterest') {
						$site = $sm_site;
						$url = $sm_site_val['url'];
						
						$url = str_replace('TITLECOUNT', $titleCOUNT, $url);
						$url = str_replace('TITLE', $title, $url);			
						$url = str_replace('PERMALINKCOUNT', $permalinkCOUNT, $url);
						$url = str_replace('PERMALINK', $permalink, $url);
						$url = str_replace('SUMMARY', $summary, $url);
						$url = str_replace('IMAGEURL', $imgurl, $url);
						
						$linkitem = 'f';
						
						$style='';
						$target='';
						$href = $url;
						
						
						$link= "<div class='evo_sm ".$sm_site."'>".$href."</div>";
						
						$output_sm.=$link;
						$sm_count++;
					}
				}
			}
			
			if($sm_count>0){
				echo 
					"<div class='bordb evo_metarow_socialmedia evcal_evdata_row'>
						".$output_sm."<div class='clear'></div>
					</div>";
			}
		}
	
		$eventon->evo_generator->__calendar_type ='default';
		
		//return $row;
	}
	
	// Eventon Settings Page Additions
	function single_event_settings($array){
		
		$new_array = $array;
		
		$new_array[]= array(
			'id'=>'eventon_social',
			'name'=>'Settings for Single Events',
			'display'=>'none',
			'tab_name'=>'Single Events',
			'top'=>'4',
			'fields'=> apply_filters('evo_se_setting_fields', array(
				array('id'=>'evosm','type'=>'subheader','name'=>'Single Event Page',),
				array('id'=>'evosm_1','type'=>'yesno','name'=>'Create Single Events Page Sidebar',
						'legend'=>'This will create a sidebar for single event page, to which you can add widgets from Appearance > Widget'
					),

				array('id'=>'evosm','type'=>'note','name'=>'Need help with single events page?',),

				array('id'=>'evosm','type'=>'subheader','name'=>'Social Media',),

				array('id'=>'evosm_som','type'=>'yesno','name'=>'Show social media share icons only on single events', 'legend'=>'Setting this to Yes will only add social media share link buttons to single event page and single event box you created'),

				array('id'=>'eventonsm_fbs','type'=>'yesno','name'=>'Facebook Share',),
				array('id'=>'eventonsm_tw','type'=>'yesno','name'=>'Twitter'),
				array('id'=>'eventonsm_ln','type'=>'yesno','name'=>'LinkedIn'),
				array('id'=>'eventonsm_gp','type'=>'yesno','name'=>'GooglePlus'),
				array('id'=>'eventonsm_pn','type'=>'yesno','name'=>'Pinterest'),
			)
		));
		
		return $new_array;
	}
	

	// SUPPORT FUNCTIONS

		// create a single event sidebar
		function register_se_sidebar(){
			$opt = $this->evo_opt;

			if(!empty($opt['evosm_1']) && $opt['evosm_1'] =='yes'){
				register_sidebar(array(
				  'name' => __( 'Single Event Sidebar' ),
				  'id' => 'evose_sidebar',
				  'description' => __( 'Widgets in this area will be shown on the right-hand side of single events page.' ),
				  'before_title' => '<h3 class="widget-title">',
				  'after_title' => '</h3>'
				));
			}
		}

	

		/*
			SINGLE EVENT page template functions
		*/
			// HEADER
			public function eventon_header(){
				$this->page_frontend_scripts();	
				//add_action('wp_head', array( $this, 'remove_script') );			
				
				global $post;
				
				get_header();
			}	
			// GET : month and year for an event
			function get_single_event_header($event_id){
				
				$row_start = get_post_meta($event_id, 'evcal_srow',true);
				
				$formatted_time = eventon_get_formatted_time($row_start);
				
				return get_eventon_cal_title_month($formatted_time['n'], $formatted_time['Y']);
			}
		
		/**
			ADD single events template paths
		 */
		public function add_new_template_load_path( $paths ) {
			
			$paths[] = $this->plugin_path . '/templates/';
			
			return $paths;
		}
	
		/** Save event meta values **/
		function event_meta_settings(){
			
			$this_id = get_the_ID();
			
			$exlink_option = get_post_meta($this_id, '_evcal_exlink_option',true);
			
			
			$code ="<a link='yes' linkval='".get_permalink($this_id)."' class='evcal_db_ui evcal_db_ui_4 ".(($exlink_option=='4')?'selected':null)."' title='Open Event Page' value='4'></a>";
			
			echo $code;
		}
		
		/**
		 *	Remove eventon frontend javascript to stop event click actions on single page
		 */
		public function remove_script(){
			//wp_dequeue_script('evcal_ajax_handle');
		}
		
		
		
		// front end styles for single event ::PAGE
		public function page_frontend_scripts(){
			global $typenow, $post, $wp_scripts;
			
			wp_enqueue_script('eventon_single_events',$this->plugin_url.'/assets/se_page_script.js', array('jquery'), '1.0', true );
			wp_enqueue_style( 'evcal_single_event');
				
			$this->is_single_event= true;
			
		}
		
		// FRONT end styles and scripts for single event.
		function register_scripts(){
			$this->is_single_event= true;
			
			wp_register_style('evcal_single_event',$this->plugin_url.'/assets/style.css');		
			wp_register_style('evcal_single_event_one_style',$this->plugin_url.'/assets/style_single.css');		

			wp_register_script('evcal_single_event_one',$this->plugin_url.'/assets/single_event_box.js', array('jquery'),'1.0',true );
		}


	
	// SECONDARY FUNCTIONS

		/** backend pages **/
		function backend_scripts(){
			wp_enqueue_style( 'evo_sin_wpadmin',$this->plugin_url.'/assets/style-wp-admin.css');
		}
		
		
		/** Javascript for event post page */
		function event_meta_post_script(){
			wp_enqueue_script('evo_sin_post_script',$this->plugin_url.'/assets/post_script.js',array('jquery'),1.0,true);
		}
		

		// ACTIVATION
			function activate(){
				global $wp_rewrite;
				//Call flush_rules() as a method of the $wp_rewrite object
				$wp_rewrite->flush_rules();

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
		
		
		
		// ADD : Comments and event excerpt box
		function activate_comments($array){		
			$vals = array('supports'=> array('title','excerpt','editor','custom-fields','comments','thumbnail') );
			return array_merge($array,$vals);	
		}
	
		public function has_evo_se_sidebar(){
			$opt = $this->evo_opt;
			return (!empty($opt['evosm_1']) && $opt['evosm_1'] =='yes')? true: false;
		}
	
}
// Initiate this addon within the plugin
$GLOBALS['eventon_sin_event'] = new EventON_sin_event();



/*
	Universally available functions
*/
// main content body
function eventon_se_page_content(){
	global $eventon_sin_event;

	$eventon_sin_event->page_frontend_scripts();

	eventon_get_template_part( 'content', 'single-event' , $eventon_sin_event->plugin_path.'/templates');	
}

// sidebar 
function eventon_se_sidebar(){
	// sidebar
	$opt = get_option('evcal_options_evcal_1');
	if(!empty($opt['evosm_1']) && $opt['evosm_1'] =='yes'){
		
		if ( is_active_sidebar( 'evose_sidebar' ) ){

			?>
			<?php //get_sidebar('evose_sidebar'); ?>
			<div class='evo_page_sidebar'>
				<ul id="sidebar">
					<?php dynamic_sidebar( 'evose_sidebar' ); ?>
				</ul>
			</div>
			<?php
		}
	}
}