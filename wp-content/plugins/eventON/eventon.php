<?php
/**
 * Plugin Name: EventON
 * Plugin URI: http://www.myeventon.com/
 * Description: A beautifully crafted minimal calendar experience
 * Version: 2.2.14
 * Author: AshanJay
 * Author URI: http://www.ashanjay.com
 * Requires at least: 3.7
 * Tested up to: 3.8.1
 *
 * Text Domain: eventon
 * Domain Path: /lang/languages/
 *
 * @package EventON
 * @category Core
 * @author AJDE
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



// main eventon class
if ( ! class_exists( 'EventON' ) ) {

class EventON {
	public $version = '2.2.14';
	/**
	 * @var evo_generator
	 */
	public $evo_generator;	
	
	public $template_url;
	
	private $content;
	/**
	 * Constructor.
	 */
	public function __construct() {

		// Define constants
		$this->define_constants();	
		
		// Installation
		register_activation_hook( __FILE__, array( $this, 'activate' ) );


		// Updates
		add_action( 'admin_init', array( $this, 'verify_plugin_version' ), 5 );
		
		// Include required files
		$this->includes();
				
		// Hooks
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'widgets_init', array( $this, 'register_widgets' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		
		// Deactivation
		register_deactivation_hook( AJDE_EVCAL_FILE, array($this,'deactivate'));
		
		
	}

	/**
	 * Define EVO Constants
	 */
	public function define_constants() {
		define( "AJDE_EVCAL_DIR", WP_PLUGIN_DIR );
		define( "AJDE_EVCAL_PATH", dirname( __FILE__ ) );
		define( "AJDE_EVCAL_FILE", ( __FILE__ ) );
		define( "AJDE_EVCAL_URL", path_join(plugins_url(), basename(dirname(__FILE__))) );
		define( "AJDE_EVCAL_BASENAME", plugin_basename(__FILE__) );
		define( "EVENTON_BASE", basename(dirname(__FILE__)) );
		define( "BACKEND_URL", get_bloginfo('url').'/wp-admin/' ); 

	}
	
	
	/**
	 * Include required core files used in admin and on the frontend.
	 */
	function includes() {

		// post types
		include_once( 'includes/class-evo-post-types.php' );

		if ( is_admin() )
			$this->admin_includes();
		if ( ! is_admin() || defined('DOING_AJAX') )
			$this->frontend_includes();
		if ( defined('DOING_AJAX') )
			$this->ajax_includes();
		
		// Functions
		include_once( 'eventon-core-functions.php' );					// Contains core functions for the front/back end
		include_once( 'classes/class-calendar_generator.php' );		// Main class to generate calendar

		
	}
	
	/**
	 * Include required admin files.
	 */
	public function admin_includes() {
		include_once( 'admin/eventon-admin-init.php' );			// Admin section
		
		//admin only classes
		include_once( 'classes/class-evo-event.php' );
		$this->evo_event = new evo_event();
	}
	/**
	 * Include required ajax files.
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_includes() {
		include_once( 'eventon-ajax.php' );						// Ajax functions for admin and the front-end
	}
	
	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		// Functions
		include_once( 'eventon-functions.php' );// Contains functions for various front-end events
		// Shortcodes
		include_once( 'classes/shortcodes/class-evo-shortcodes.php' );

		// Template loader
		include_once( 'includes/class-evo-template-loader.php' );
	}
	/**
	 * register_widgets function.
	 */
	function register_widgets() {
		// Include - no need to use autoload as WP loads them anyway
		include_once( 'classes/widgets/class-evo-widget-main.php' );
		
		// Register widgets
		register_widget( 'EvcalWidget' );
		register_widget( 'EvcalWidget_SC' );
	}
	
	/**
	 * Init eventON when WordPress Initialises.
	 */
	public function init() {
		
		// Set up localisation
		$this->load_plugin_textdomain();
		
		$this->template_url = apply_filters('eventon_template_url','eventon/');
		
		$this->evo_generator	= new EVO_generator();			// Query class handles generating calendar
				
		// Classes/actions loaded for the frontend and for ajax requests
		if ( ! is_admin() || defined('DOING_AJAX') ) {
			// Class instances			
			$this->shortcodes		= new EVO_Shortcodes();		
					
			// Hooks
			add_action( 'init', array( $this, 'register_scripts' ), 10 );

			add_action( 'wp_enqueue_scripts', array( $this, 'load_default_evo_styles' ), 10 );
			add_action( 'wp_head', array( $this, 'load_dynamic_evo_styles' ), 50 );
			add_action( 'wp_head', array( $this, 'generator' ) );			
		}
		
		
		// roles and capabilities
		eventon_init_caps();
		
		global $pagenow;
		$__needed_pages = array('update-core.php','plugins.php', 'admin.php','admin-ajax.php', 'plugin-install.php');

		//print_r($pagenow);
		// only for admin
		if(is_admin() && !empty($pagenow) && in_array($pagenow, $__needed_pages) ){

			// Initiate eventon update checker		
			require_once( 'classes/class-evo-updater.php' );		
			$api_url = 'http://update.myeventon.com/';
			//$api_url = 'http://update.myeventon.com/index-temp.php';
			$this->evo_updater = new evo_updater ( $this->version, $api_url, AJDE_EVCAL_BASENAME);
			//$GLOBALS['evo_updater'] = new evo_updater( $this->version, $api_url, AJDE_EVCAL_BASENAME);

			
		}
		
		// Init action
		do_action( 'eventon_init' );
		
		
	}
	
	
	/*** output the inpage popup window for eventon	 */
		public function output_eventon_pop_window($arg){
		
			$defaults = array(
				'content'=>'',
				'class'=>'regular',
				'attr'=>'',
				'title'=>'',
				'subtitle'=>'',
				'type'=>'normal',
				'hidden_content'=>'',
				'width'=>'',
			);
			$args = (!empty($arg) && is_array($arg) && count($arg)>0) ? array_merge($defaults, $arg) : $defaults;
			
			
			
			$_padding_class = (!empty($args['type']) && $args['type']=='padded')? ' padd':null;

			//print_r($args);
			$content='';
			$content .= 
			"<div class='eventon_popup {$args['class']}{$_padding_class}' {$args['attr']} style='display:none; ". ( (!empty($args['width']))? 'width:'.$args['width'].'px;':null )."'>				
					<div class='evoPOP_header'>
						<a class='evopop_backbtn' style='display:none'><i class='fa fa-angle-left'></i></a>
						<p id='evoPOP_title'>{$args['title']}</p>
						". ( (!empty($args['subtitle']))? "<p id='evoPOP_subtitle'>{$args['subtitle']}</p>":null) ."
						<a class='eventon_close_pop_btn'>X</a>
					</div>
							
					<div id='eventon_loading'></div>";

				$content .= (!empty($args['max_height']))? "<div class='evo_lightbox_outter maxbox' style='max-height:{$args['max_height']}px'>":null;
				$content .= "<div class='eventon_popup_text'>{$args['content']}</div>";
				$content .= (!empty($args['max_height']))? "</div>":null;
				$content .= "	<p class='message'></p>
					
				</div>
			";
			
			$this->content .= $content;
			add_action('admin_footer', array($this, 'actual_output_popup'));
		}
		
		function actual_output_popup($content){
			
			echo "<div id='eventon_popup_outter'>";
			echo $this->content;
			echo "</div><div id='evo_popup_bg'></div>";
		}


	
	/*	Legend popup box across wp-admin	*/
		public function throw_guide($content, $position='', $echo=true){
			
			$L = (!empty($position) && $position=='L')? ' L':null;
			
			$content = "<span class='evoGuideCall{$L}'>?<em>{$content}</em></span>";
			
			if($echo){ echo $content;  }else{ return $content; }
		}
	
		
	// deprecatin function after 2.2.12
		public function addon_has_new_version($values){
			// return nothing
		}
	

	/* EMAIL functions */
		public function get_email_part($part){

			$file_name = 'email_'.$part.'.php';

			$paths = array(
				0=> TEMPLATEPATH.'/'.$this->template_url.'templates/email/',
				1=> AJDE_EVCAL_PATH.'/templates/email/',
			);

			foreach($paths as $path){				
				if(file_exists($path.$file_name) ){	
					$template = $path.$file_name;	
					break;
				}

				//echo($path.$file_name.'<br/>');
			}


			ob_start();

			include($template);

			return ob_get_clean();
		}

		// body part of the email template loading
		public function get_email_body($part, $def_location, $args=''){
			$file_name = $part.'.php';

			$paths = array(
				0=> TEMPLATEPATH.'/'.$this->template_url.'templates/email/',
				1=> $def_location,
			);

			foreach($paths as $path){				
				if(file_exists($path.$file_name) ){	
					$template = $path.$file_name;	
					break;
				}

				//echo($path.$file_name.'<br/>');
			}


			ob_start();

			include($template);

			return ob_get_clean();
		}

	/** Register/queue frontend scripts. */
		public function register_scripts() {
			global $post;
			
			$evo_opt= get_option('evcal_options_evcal_1');
			
			
			// Google gmap API script -- loadded from class-calendar_generator.php		

			wp_register_script('add_to_cal', AJDE_EVCAL_URL. '/assets/js/add_to_calendar.js', array('jquery'),'1.0',true );

			wp_register_script('evcal_ajax_handle', AJDE_EVCAL_URL. '/assets/js/eventon_script.js', array('jquery'),'1.0',true );
			wp_localize_script( 
				'evcal_ajax_handle', 
				'the_ajax_script', 
				array( 
					'ajaxurl' => admin_url( 'admin-ajax.php' ) , 
					'postnonce' => wp_create_nonce( 'eventon_nonce' )
				)
			);



			// google maps	
			wp_register_script('eventon_gmaps', AJDE_EVCAL_URL. '/assets/js/eventon_gen_maps.js', array('jquery'),'1.0',true );	
			wp_register_script('eventon_init_gmaps', AJDE_EVCAL_URL. '/assets/js/eventon_init_gmap.js', array('jquery'),'1.0',true );
			wp_register_script( 'eventon_init_gmaps_blank', AJDE_EVCAL_URL. '/assets/js/eventon_init_gmap_blank.js', array('jquery'),'1.0',true ); // load a blank initiate gmap javascript

			wp_register_script( 'evcal_gmaps', apply_filters('eventon_google_map_url', 'https://maps.googleapis.com/maps/api/js?sensor=false'), array('jquery'),'1.0',true);



			// STYLES
			wp_register_style('evo_font_icons',AJDE_EVCAL_URL.'/assets/fonts/font-awesome.css');		
			
			// select the current skin
			$skin  = (!empty($evo_opt['evcal_skin']))? $evo_opt['evcal_skin'] : 'slick';		
					
			// Defaults styles and dynamic styles
			wp_register_style('evcal_cal_default',AJDE_EVCAL_URL.'/assets/css/eventon_styles.css');	
			//wp_register_style('evo_dynamic_css', admin_url('admin-ajax.php').'?action=evo_dynamic_css');


			global $is_IE;
			if ( $is_IE ) {
				wp_register_style( 'ieStyle', AJDE_EVCAL_URL.'/assets/css/ie.css', array(), '1.0' );
				wp_enqueue_style( 'ieStyle' );
			}


			// LOAD custom google fonts for skins		
			$gfont="http://fonts.googleapis.com/css?family=Oswald:400,300|Open+Sans:400,300";
			wp_register_style( 'evcal_google_fonts', $gfont, '', '', 'screen' );
			
			$this->register_evo_dynamic_styles();

		}	

		public function register_evo_dynamic_styles(){
			$opt= get_option('evcal_options_evcal_1');
			if(!empty($opt['evcal_css_head']) && $opt['evcal_css_head'] =='no' || empty($opt['evcal_css_head'])){
				if(is_multisite()) {
					$uploads = wp_upload_dir();
					wp_register_style('eventon_dynamic_styles', $uploads['baseurl'] . '/eventon_dynamic_styles.css', 'style');
				} else {
					wp_register_style('eventon_dynamic_styles', 
						AJDE_EVCAL_URL. '/assets/css/eventon_dynamic_styles.css', 'style');
				}
			}

		}
		
		public function load_dynamic_evo_styles(){
			$opt= get_option('evcal_options_evcal_1');
			if(!empty($opt['evcal_css_head']) && $opt['evcal_css_head'] =='yes'){
				
				$dynamic_css = get_option('evo_dyn_css');
				if(!empty($dynamic_css)){
					echo '<style type ="text/css">'.$dynamic_css.'</style>';
				}
				
			}else{
				wp_enqueue_style( 'eventon_dynamic_styles');
			}
		}
		public function load_default_evo_scripts(){
			//wp_enqueue_script('add_to_cal');
			wp_enqueue_script('evcal_ajax_handle');
			wp_enqueue_script('eventon_gmaps');

			if(has_action('eventon_enqueue_scripts'))
				do_action('eventon_enqueue_scripts');
			
		}
		public function load_default_evo_styles(){
			$opt= get_option('evcal_options_evcal_1');
			if(empty($opt['evo_googlefonts']) || $opt['evo_googlefonts'] =='no')
				wp_enqueue_style( 'evcal_google_fonts' );

			wp_enqueue_style( 'evcal_cal_default');		
			wp_enqueue_style( 'evo_font_icons' );

			

		}
		public function evo_styles(){
			add_action('wp_head', array($this, 'load_default_evo_scripts'));
		}
	
	/**
	 * Activate function to store version.
	 */
	public function activate(){
		set_transient( '_evo_activation_redirect', 1, 60 * 60 );
		
		do_action('eventon_activate');
	}
	
	// update function
	public function update(){
		//set_transient( '_evo_activation_redirect', 1, 60 * 60 );
		
	}
	
	
	/** check plugin version **/
	public function verify_plugin_version(){
		
		$plugin_version = $this->version;
			
		// check installed version
		$installed_version = get_option('eventon_plugin_version');
		
		if($installed_version != $plugin_version){
			if (  isset( $_GET['page'] ) && 'eventon' == $_GET['page']   ){
				

				update_option('eventon_plugin_version', $plugin_version);
				wp_safe_redirect( admin_url( 'index.php?page=evo-about&evo-updated=true' ) );
			}
			//set_transient( '_evo_update_redirect', 1, 60 * 60 );
			//update_option( '_evo_updated', 'true');
			
		}else if(!$installed_version ){
			add_option('eventon_plugin_version', $plugin_version);			
		}else{
			update_option('eventon_plugin_version', $plugin_version);			
		}
		
		// delete options saved on previous version
		delete_option('evcal_plugin_version');
	}
	
	public function is_eventon_activated(){
		$licenses =get_option('_evo_licenses');

		if(!empty($licenses)){
			$status = $licenses['eventon']['status'];
			return ($status=='active')? true:false;
		}else{
			return false;
		}
		
	}
	
	public function deactivate(){
		//delete_option('evcal_options');		
		do_action('eventon_deactivate');
	}
	

	
	/**
	 * Ensure theme and server variable compatibility and setup image sizes..
	 */
	public function setup_environment() {
		// Post thumbnail support
		if ( ! current_theme_supports( 'post-thumbnails', 'ajde_events' ) ) {
			add_theme_support( 'post-thumbnails' );
			remove_post_type_support( 'post', 'thumbnail' );
			remove_post_type_support( 'page', 'thumbnail' );
		} else {
			add_post_type_support( 'ajde_events', 'thumbnail' );
		}

		// IIS
		if ( ! isset($_SERVER['REQUEST_URI'] ) ) {
			$_SERVER['REQUEST_URI'] = substr( $_SERVER['PHP_SELF'], 1 );
			if ( isset( $_SERVER['QUERY_STRING'] ) ) {
				$_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING'];
			}
		}

		// NGINX Proxy
		if ( ! isset( $_SERVER['REMOTE_ADDR'] ) && isset( $_SERVER['HTTP_REMOTE_ADDR'] ) ) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_REMOTE_ADDR'];
		}

		if ( ! isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTP_HTTPS'] ) ) {
			$_SERVER['HTTPS'] = $_SERVER['HTTP_HTTPS'];
		}

		// Support for hosts which don't use HTTPS, and use HTTP_X_FORWARDED_PROTO
		if ( ! isset( $_SERVER['HTTPS'] ) && ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
			$_SERVER['HTTPS'] = '1';
		}
	}
	
	
	/** LOAD Backender UI and functionalities for settings. */
		public function load_ajde_backender(){
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');

			wp_enqueue_script('backender_colorpicker');
			wp_enqueue_script('ajde_backender_script');

			include_once('admin/ajde_backender.php');
			
		}
	
		public function enqueue_backender_styles(){
			wp_enqueue_style( 'ajde_backender_styles',AJDE_EVCAL_URL.'/assets/css/ajde_backender_style.css');
			wp_enqueue_style( 'colorpicker_styles',AJDE_EVCAL_URL.'/assets/css/colorpicker_styles.css');
			
		}
		public function register_backender_scripts(){
			wp_register_script('backender_colorpicker',AJDE_EVCAL_URL.'/assets/js/colorpicker.js' ,array('jquery'),'1.0', true);
			wp_register_script('ajde_backender_script',AJDE_EVCAL_URL.'/assets/js/ajde_backender_script.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable'), '1.0', true );
			
		}
	
	
	/** Output generator to aid debugging. */
		public function generator() {
			echo "\n\n" . '<!-- EventON Version -->' . "\n" . '<meta name="generator" content="EventON ' . esc_attr( $this->version ) . '" />' . "\n\n";
		}
	
	


	
	
	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'eventon' );
		$formal = 'yes' == get_option( 'eventon_informal_localisation_type' ) ? 'informal' : 'formal';

		load_textdomain( 'eventon', WP_LANG_DIR . "/eventon/eventon-$locale.mo" );

		// Load admin specific MO files
		if ( is_admin() ) {
			load_textdomain( 'eventon', WP_LANG_DIR . "/eventon/eventon-admin-$locale.mo" );
			load_textdomain( 'eventon', AJDE_EVCAL_PATH . "/lang/languages/eventon-admin-$locale.mo" );
		}

		load_plugin_textdomain( 'eventon', false, AJDE_EVCAL_URL . "/lang/languages/$formal" );
		load_plugin_textdomain( 'eventon', false, AJDE_EVCAL_URL . "/lang/languages" );
	}
	
	public function get_current_version(){
		return $this->version;
	}	
	
	/** return eventon option settings values **/
	public function evo_get_options($field, $array_field=''){
		if(!empty($array_field)){
			$options = get_option($field);
			$options = $options[$array_field];
		}else{
			$options = get_option($field);
		}		
		return !empty($options)?$options:null;
	}

}

}// class exists

/**
 * Init eventon class
 */
$GLOBALS['eventon'] = new EventON();

//include_once('admin/update-notifier.php');	
?>