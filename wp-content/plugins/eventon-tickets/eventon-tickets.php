<?php

/*
 Plugin Name: EventON - Event Tickets
 Plugin URI: http://www.myeventon.com/
 Description: Sell Event Tickets - powered by Woocommerce
 Author: Ashan Jay
 Version: 0.1
 Author URI: http://www.ashanjay.com/
 Requires at least: 3.7
 Tested up to: 3.8.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;


//Event tickets main class
if ( ! class_exists( 'evotx' ) ):

class evotx{
	
	public $version='0.1';
	public $eventon_version = '2.2.13';
	public $name = 'Tickets';
			
	public $addon_data = array();
	public $slug, $plugin_slug , $plugin_url , $plugin_path ;
	private $urls;
	public $template_url ;

	private $evotx_opt;
	
	public $evotx_args;
	
	/*
	 * Construct
	 */
	public function __construct(){
		
		$this->super_init();

		// get eventon addon class file url if exists
		$url = str_replace($this->addon_data['slug'], 'eventON', $this->addon_data['plugin_path']);
		$url .= '/classes/class-evo-addons.php';

		if(file_exists($url)){
				
			include_once( $url);
			$this->addon = new evo_addon($this->addon_data);

			// if addon class exists
			if($this->addon->requirment_check($this->eventon_version, $this->name)){

				if($this->woocommerce_exists()){
					add_action( 'init', array( $this, 'init' ), 0 );

					// send email when order is complete w/ tix
					add_action('woocommerce_order_status_completed', array(&$this, 'send_ticket_email'), 10, 1);
			

					$this->includes();
				}else{
					add_action('admin_notices', array($this, '_wc_eventon_warning'));
				}
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
			
			$this->evotx_opt = get_option('evcal_options_evcal_tx');

			// RUN addon updater only in dedicated pages
			if ( is_admin() ){
				$this->addon->updater();
			}	

			$this->register_tix_post_type();			
			
		}
	

	/** Include required core files. */
		function includes(){
			
			// both front and admin
			include_once( 'includes/class-ticket.php' );

			if ( is_admin() ){
				include_once( 'admin/admin-init.php' );
			}
			//frontend includes
			if ( ! is_admin() || defined('DOING_AJAX') ){
				include_once( 'includes/class-frontend.php' );
			}

			if ( defined('DOING_AJAX') ){
				include_once( 'admin/eventon_tx_ajax.php' );
			}
		}

	// create new post type
		function register_tix_post_type(){

			$labels = eventon_get_proper_labels('Event Ticket','Event Tickets');
			register_post_type('evo-tix', 
				apply_filters( 'eventon_register_post_type_tix',
					array(
						'labels' => $labels,
						'public' 				=> true,
						'show_ui' 				=> true,
						'capability_type' 		=> 'eventon',
						'exclude_from_search'	=> true,
						'publicly_queryable' 	=> true,
						'hierarchical' 			=> false,
						'rewrite' 				=> false,
						'query_var'		 		=> true,
						'supports' 				=> array('title','custom-fields'),					
						'menu_position' 		=> 5, 
						'show_in_menu'			=>'edit.php?post_type=ajde_events',
						'has_archive' 			=> true
					)
				)
			);
		}
	

	// send out ticket email
		public function send_ticket_email($order_id){
			global $woocommerce, $evotx;
			//$order_id = 402;

			$evotx_opt = $this->evotx_opt;
			$order = new WC_Order( $order_id );
			$tickets = $order->get_items();

			// if there are tickets ordered in this order
			if($tickets && count($tickets)>0){
				
				add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));	

				$customer = get_post_meta($order_id, '_customer_user');
				$usermeta = get_user_meta($customer[0]);

				$email_body_arguments = array(
					'orderid'=>$order_id,
					'tickets'=>$tickets, 
					'customer'=>$usermeta['first_name'][0].' '.$usermeta['last_name'][0],
					'email'=>'yes'
				);

				$to_email = $usermeta['billing_email'][0];
				$__from_email = (!empty($evotx_opt['evotx_notfiemailfrom']) )?
							htmlspecialchars_decode ($evotx_opt['evotx_notfiemailfrom'])
							:get_bloginfo('admin_email');
				$__from_email_name = (!empty($evotx_opt['evotx_notfiemailfromN']) )?
							($evotx_opt['evotx_notfiemailfromN'])
							:get_bloginfo('name');

					$from_email = (!empty($__from_email_name))? 
								$__from_email_name.' <'.$__from_email.'>' : $__from_email;

				$subject = '[#'.$order_id.'] '.((!empty($evotx_opt['evotx_notfiesubjest']))? 
							$evotx_opt['evotx_notfiesubjest']: __('Event Ticket','eventon'));
				$headers = 'From: '.$from_email;	

				// get email body
				//$body = $this->get_ticket_email_body($email_body_arguments);

				$tix = new evotx_ticket();
				$body = $tix->get_ticket_email_body($email_body_arguments);

				$send_wp_mail = wp_mail($to_email, $subject, $body, $headers);	

				return $send_wp_mail;

				//print_r($message);

			}
		}



	// SECONDARY FUNCTIONS		

		function woocommerce_exists(){
			return (in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) )? true:false;
		}	
	
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


	    function _wc_eventon_warning(){

	        ?>
	        <div class="message error"><p><?php _e('Eventon Tickets need woocommerce plugin to function properly. Please install woocommerce', 'eventon'); ?></p></div>
	        <?php
	    }
	   
	
		// Deactivate addon
		function deactivate(){
			$this->addon->remove_addon();
		}
	    
	
}

// Initiate this addon within the plugin
$GLOBALS['evotx'] = new evotx();

endif;


?>