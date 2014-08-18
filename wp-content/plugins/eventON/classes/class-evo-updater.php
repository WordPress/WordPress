<?php
/**
 * 
 * eventon update and licensing class
 *
 * @author 		AJDE
 * @category 	Admin
 * @package 	EventON/Classes
 * @version     2.2.12
 */
 
class evo_updater{
   
	/** The plugin current version*/
    public $current_version;
	
    /** The plugin remote update path */
    public $api_url;

    /** Plugin Slug (plugin_directory/plugin_file.php) */
    public $plugin_slug;
    public $remote_version;

    /** Plugin name (plugin_file) */
    public $slug;
	
	public $transient;

	public $test = 49904;
		
    /**
     * Initialize a new instance of the WordPress Auto-Update class
     */
    function __construct($current_version, $api_url, $plugin_slug){
        // Set the class public variables
        $this->current_version = $current_version;
        $this->api_url = $api_url;
        $this->plugin_slug = $plugin_slug;
        list ($t1, $t2) = explode('/', $plugin_slug);
        $this->slug = str_replace('.php', '', $t2);

        // define the alternative API for updating checking
        add_filter('pre_set_site_transient_update_plugins', array(&$this, 'check_update'));

        // Define the alternative response for information checking
        add_filter('plugins_api', array(&$this, 'evo_check_info'), 10, 3);
		
		// update to current version
		$this->save_new_license_field_values('current_version',$this->current_version,$this->slug);

		// Get saved remote version and store in class variables
		$this->sync_remote_version();

		// show new update notices		
		$this->new_update_notices();
				
    }

    /** Add our self-hosted autoupdate plugin to the filter transient   */
	    public function check_update($transient){  

	        // Get the remote version
	        $this->remote_version = $this->getRemote_version();

	        // If a newer version is available, add the update
	        if (version_compare($this->current_version, $this->remote_version, '<')) {
	            $obj = new stdClass();
	            $obj->slug = $this->slug;
	            $obj->new_version = $this->remote_version;
	            $obj->url = $this->api_url;
	            $obj->package = $this->get_package_download_url();
	            $transient->response[$this->plugin_slug] = $obj;
							
	        }
			
			// compare versions remote to local
			if(version_compare($this->current_version, $this->remote_version, '<')){
				//$this->have_new_version();
				$this->save_new_update_details($this->remote_version, true, $this->current_version);
			}

		
			return $transient;
			
	    }

	// CHECK for new update and if there are any show custom update notice message
	    public function new_update_notices(){
	    	$remot_version = $this->remote_version;
	    	if(version_compare($this->current_version, $remot_version, '<')){
				global $pagenow;

			    if( $pagenow == 'plugins.php' ){	       
			        add_action( 'in_plugin_update_message-' . $this->plugin_slug, array($this, 'in_plugin_update_message'), 10, 2 );
			       
			    }				
			}
	    }

	// sync remote version 
	    private function sync_remote_version(){
	    	if(empty( $this->remote_version)){
	    		$licenses =get_option('_evo_licenses');
	    		if(!empty($licenses) && count($licenses)>0 && !empty($licenses[$this->slug]) 
	    			&& !empty($licenses[$this->slug]['remote_version']) ){
	    			$this->remote_version = $licenses[$this->slug]['remote_version'];
	    		}else{
	    			return false;
	    		}
						
	    	}else{ return $this->remote_version; }
	    }
	
	// custom update notificatoin message		
		function in_plugin_update_message($plugin_data, $r ){
		    
		    ob_start();

		    // main eventon plugin
		    if($this->slug=='eventon'):
		    	?>
				<div class="evo-plugin-update-info">
					<p><strong>NOTE:</strong> You can activate your copy to get auto updates. <a href='http://www.myeventon.com/documentation/how-to-find-eventon-license-key/' target='_blank'>How to find eventON license key</a><br/>When you update eventON please be sure to clear all your website and browser cache to reflect style and javascript changes we have made.</p>
				</div>
		    <?php
		    	// addon
		    	else:
		   	?>
				<div class="evo-plugin-update-info">
					<p><strong>NOTE:</strong> You can activate your copy to get auto updates or you can grab the new update from <a href='http://www.myeventon.com/my-account' target='_blank'>myeventon.com</a></p>
				</div>
		   	<?php
		   	endif;

		    echo ob_get_clean();
		}

    // ADDONS ------------------------

    	//  verify addon license
    		public function ADD_verify_lic($arr){

				$url='http://www.myeventon.com/woocommerce/?wc-api=software-api&request=activation&email='.$arr['email'].'&licence_key='.$arr['key'].'&product_id='.$arr['product_id'].'';

				$request = wp_remote_get($url);

				if (!is_wp_error($request) && $request['response']['code']===200) { 

					$result = (!empty($request['body']))? json_decode($request['body']): $request; 
					//update_option('test1', json_decode($result));
					return $result;
				}else{	
					return false;
				}
    		}

    	// save addon license status
    		public function ADD_save_lic($arr){
    			$licenses =get_option('_evo_licenses');
			
				if(!empty($licenses) && count($licenses)>0 && !empty($licenses[$arr['slug']]) && !empty( $arr['key']) ){	

					$new_lic = $licenses;	

					$new_lic[$arr['slug']]['key']= $arr['key'];	
					$new_lic[$arr['slug']]['email']= $arr['email'];	
					$new_lic[$arr['slug']]['product_id']= $arr['product_id'];	
					$new_lic[$arr['slug']]['status']= 'active';
					
					update_option('_evo_licenses',$new_lic);
					
					return $new_lic;
				}elseif( empty($licenses[$arr['slug']]) ){
				// dont have the addon license on the options

					$new_lic = $licenses;

					$new_lic[$arr['slug']]['key']= $arr['key'];	
					$new_lic[$arr['slug']]['email']= $arr['email'];	
					$new_lic[$arr['slug']]['product_id']= $arr['product_id'];	
					$new_lic[$arr['slug']]['status']= 'active';
					
					update_option('_evo_licenses',$new_lic);
					return $new_lic;

				}else{
					return false;
				}
    		}

    	// deactivate addon
    		public function ADD_deactivate_lic($slug){
    			$licenses =get_option('_evo_licenses');
			
				if(!empty($licenses) && count($licenses)>0 && !empty($slug) ){	

					$new_lic = $licenses;	
					$new_lic[$slug]['status']='inactive';

					update_option('_evo_licenses',$new_lic);
					return $new_lic;
				}else{ return false;}
    		}

    	// update addons existance
    		public function ADD_update_addons(){    			

				$evo_addons = get_option('eventon_addons');    							

				// site have eventon addons and its an array
				if(!empty($evo_addons) && is_array($evo_addons)){
					$active_plugins = get_option( 'active_plugins' );    	

					$new_addons = $evo_addons;
					foreach($evo_addons as $addon=>$some){
						// addon actually doesn not exist in plugins
						if(!in_array($addon.'/'.$addon.'.php', $active_plugins)){
							unset($new_addons[$addon]);
						}
					}

					update_option('eventon_addons',$new_addons);
				}
    		}

	// get version information
		public function evo_check_info($false, $action, $args){
			if ($args->slug === $this->slug) {  
	            $information = $this->getRemote_information($args);  
	            return $information;  
	        }  
	        return $false;
		}
	
	
    /** Add our self-hosted description to the filter    */
	    public function getRemote_information( $args){
			global $wp_version; 
			
			/*
			$plugin_info = get_site_transient('update_plugins');
			$current_version = $plugin_info->checked[$this->plugin_slug];
			*/
			$args->version = $this->current_version;
			
			$request_string = array(
					'body' => array(
						'action' => 'plugin_information', 
						'request' => serialize($args),
						'api-key' => md5(get_bloginfo('url'))
					),
					'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
				);
			
			$request = wp_remote_post($this->api_url, $request_string);
			
			 
	        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {  
	            
				$result = unserialize($request['body']);
				$result->download_link = $this->get_package_download_url();
				
				return  $result;
	        }  
	        return false;  
				
			
	    }
	
	
	/**	Update field values to licenses */
		function save_new_update_details($remote_version, $has_new_update, $current_version){
			$licenses =get_option('_evo_licenses');
			
			if(!empty($licenses) && count($licenses)>0 && !empty($licenses[$this->slug]) ){
				

				$new_lic = $licenses;
				$new_lic[$this->slug]['remote_version']= $remote_version;	
				$new_lic[$this->slug]['has_new_update']= $has_new_update;	

				update_option('_evo_licenses',$new_lic);
				
				return $new_lic;
			}else{
				return false;
			}
		}
	
	// save license fields to wp options
		function save_new_license_field_values($license_field, $new_value, $license_slug){
			$licenses =get_option('_evo_licenses');
			
			if(!empty($licenses) && count($licenses)>0 && !empty($licenses[$license_slug]) ){
				
				
				$new_lic = $licenses;
				$new_lic[$license_slug][$license_field]= $new_value;	

				update_option('_evo_licenses',$new_lic);
			}
		}
	
    /** Return the remote version   */
	    public function getRemote_version(){
			global $wp_version;
			
			$args = array('slug' => $this->slug);
			$request_string = array(
				'body' => array(
					'action' => 'evo_latest_version', 
					'request' => serialize($args),
					'api-key' => md5(get_bloginfo('url'))
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
			);
			
		
	        $request = wp_remote_post($this->api_url, $request_string);
	        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
	            return $request['body'];
	        }
	        return false;
	    }
	
	
	/** get download url **/
		function get_package_download_url(){
			$license = $this->get_saved_license_key();
			
			if(empty($license) || !$license) {
				return false;
			}else{
				global $wp_version;
				$status = $this->get_lic_status();
				
				// if not activated and doesnt have key then dont waste remote trying
				if($status && $status=='active'){
					$args = array(
						'slug' => $this->slug,
						'key'=>$license,
						'type'=> ( ($this->slug=='eventon')? 'main':'addon'),
					);
					$request_string = array(
						'body' => array(
							'action' => 'get_download_link', 
							'request' => serialize($args),
							'api-key' => md5(get_bloginfo('url'))
						),
						'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
					);
					
				
					$request = wp_remote_post($this->api_url, $request_string);
					if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
						return $request['body'];
					}
					return false;
				}else{
					// inactive status
					return false;
				}
			
			}
		}
	
	/** get license key **/
		public function _verify_license_key($slug='', $key=''){
			
			$slug = (!empty($slug))? $slug: $this->slug;
			$saved_key = (!empty($key) )? $key: $this->get_saved_license_key($slug);
			
			if($saved_key!=false ){		
							
				global $wp_version;
			
				$args = array(
					'slug' => $this->slug,
					'key'=>$saved_key,
					'server'=>$_SERVER['SERVER_NAME']
				);
				$request_string = array(
					'body' => array(
						'action' => 'verify_envato_purchase', 
						'request' => serialize($args),
						'api-key' => md5(get_bloginfo('url'))
					),
					'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url')
				);
				
			
				$request = wp_remote_post($this->api_url, $request_string);
				if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
					$license_check_status =  $request['body'];
					
					// if validation return 1 or if error code returned
					return ($license_check_status==1)? true:$license_check_status;
						
				}			
			}	
		}
	
	// get saved license key from wp options
		public function get_saved_license_key($slug=''){
			$licenses =get_option('_evo_licenses');
			
			$slug = (!empty($slug))? $slug: $this->slug;
			
			if(is_array($licenses)&&count($licenses)>0 && !empty($licenses[$slug]) && !empty($licenses[$slug]['key'] )){	
				return $licenses[$slug]['key'];
			}else{
				return false;
			}
		}
	// get item license status
		public function get_lic_status(){
			$licenses =get_option('_evo_licenses');
			
			if(!empty($licenses) && count($licenses)>0 && !empty($licenses[$this->slug]) ){
				return $licenses[$this->slug]['status'];
			}else{
				return false;
			}
		}
	
	// save to wp options
		public function save_license_key($slug, $key){
			$licenses =get_option('_evo_licenses');
			
			if(!empty($licenses) && count($licenses)>0 && !empty($licenses[$slug]) && !empty($key) ){	

				$new_lic = $licenses;	

				$new_lic[$slug]['key']= $key;	
				$new_lic[$slug]['status']= 'active';
				
				update_option('_evo_licenses',$new_lic);
				
				return $new_lic;
			}else{
				return false;
			}
			
		}

	// remove license
		public function remove_license(){
			$licenses =get_option('_evo_licenses');
			
			if(!empty($licenses) && count($licenses)>0 && !empty($licenses[$this->slug])){

				$new_lic = $licenses;
				unset($new_lic[$this->slug]['key']);
				$new_lic[$this->slug]['status']='inactive';
				
				update_option('_evo_licenses',$new_lic);

				return $new_lic;

			}else{
				return false;
			}
		}
	
	// compare and return true or false for has newset version;
		public function has_newest_version($remote_version=''){
				
			if(empty($remote_version)){
				$evoOpt = get_option('_evo_licenses');			
				if(!empty($evoOpt)){
					$remote_version = $evoOpt['eventon']['remote_version'];
				}else{
					$remote_version = $this->getRemote_version;
				}			
			}
			
			
			return ( version_compare($remote_version, $this->current_version ) >=0)? true:false;
			
		}
	
}

