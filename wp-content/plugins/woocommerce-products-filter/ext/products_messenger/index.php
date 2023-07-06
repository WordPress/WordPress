<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_PRODS_MESSENGER extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'products_messenger'; //your custom key here
    public $index = '';
    public $html_type_dynamic_recount_behavior = 'none';
    protected $user_meta_key = 'woof_user_messenger';

    public $subscribe_lang = "";
    public $date_expire = "";
    public $count_message = -1;
    public $subscr_count = 2; //options
    public $priority_limit = 'both';
    public $subscr_period_option = "twicemonthly"; //options
    public $header_email = '';
    public $subject_email = '';
    public $text_email = '';
    public $cron = NULL;
    public $cron_hook = 'woof_products_messenger';
    public $wp_cron_period = WEEK_IN_SECONDS;

    public function __construct() {
	parent::__construct();
	//lang variables
	$this->header_email = esc_html__("New Products by your request", 'woocommerce-products-filter');
	$this->subject_email = esc_html__("New products", 'woocommerce-products-filter');
	$this->text_email = esc_html__("Dear [DISPLAY_NAME], we increased the range of our products. Number of new products: [PRODUCT_COUNT]", 'woocommerce-products-filter');

	//***

	if (isset($this->woof_settings["products_messenger"]['subscr_count']) AND ! empty($this->woof_settings["products_messenger"]['subscr_count'])) {
	    $this->subscr_count = (int) $this->woof_settings["products_messenger"]['subscr_count'];
	}
	if (isset($this->woof_settings["products_messenger"]['wp_cron_period'])AND ! empty($this->woof_settings["products_messenger"]['wp_cron_period'])) {
	    $this->subscr_period_option = $this->woof_settings["products_messenger"]['wp_cron_period'];
	}
	if (isset($this->woof_settings["products_messenger"]['header_email'])AND ! empty($this->woof_settings["products_messenger"]['header_email'])) {
	    $this->header_email = $this->woof_settings["products_messenger"]['header_email'];
	}
	if (isset($this->woof_settings["products_messenger"]['subject_email'])AND ! empty($this->woof_settings["products_messenger"]['subject_email'])) {
	    $this->subject_email = $this->woof_settings["products_messenger"]['subject_email'];
	}
	if (isset($this->woof_settings["products_messenger"]['text_email'])AND ! empty($this->woof_settings["products_messenger"]['text_email'])) {
	    $this->text_email = $this->woof_settings["products_messenger"]['text_email'];
	}
	if (isset($this->woof_settings["products_messenger"]['date_expire'])AND !empty($this->woof_settings["products_messenger"]['date_expire'])) {
	    $this->date_expire = $this->woof_settings["products_messenger"]['date_expire'];
	}
	if (isset($this->woof_settings["products_messenger"]['count_message'])AND ! empty($this->woof_settings["products_messenger"]['count_message'])) {
	    $this->count_message = $this->woof_settings["products_messenger"]['count_message'];
	}
	if (isset($this->woof_settings["products_messenger"]['priority_limit'])AND ! empty($this->woof_settings["products_messenger"]['priority_limit'])) {
	    $this->priority_limit = $this->woof_settings["products_messenger"]['priority_limit'];
	}

	$this->subscribe_lang = esc_html__('Subscription', 'woocommerce-products-filter');

	add_action('woof_products_messenger', array($this, 'woof_products_messenger'), 10);
	$this->cron = new PN_WP_CRON_WOOF('woof_messenger_wpcron');
	$this->wp_cron_period = (int) $this->get_woof_cron_schedules($this->subscr_period_option);
	$this->make_send_emails();
	$this->init();
    }

    public function get_ext_path() {
	return plugin_dir_path(__FILE__);
    }
    public function get_ext_override_path()
    {
        return get_stylesheet_directory(). DIRECTORY_SEPARATOR ."woof". DIRECTORY_SEPARATOR ."ext". DIRECTORY_SEPARATOR .$this->html_type. DIRECTORY_SEPARATOR;
    }
    public function get_ext_link() {
	return plugin_dir_url(__FILE__);
    }

    public function woof_add_items_keys($keys) {
	$keys[] = $this->html_type;
	return $keys;
    }

    public function init() {
	add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
	add_action('woof_print_html_type_options_' . $this->html_type, array($this, 'woof_print_html_type_options'), 10, 1);
	add_action('woof_print_html_type_' . $this->html_type, array($this, 'print_html_type'), 10, 1);
	add_action('wp_enqueue_scripts', array($this, 'wp_head'), 9);
	// Ajax  action
	add_action('wp_ajax_woof_messenger_add_subscr', array($this, 'woof_add_subscr'));
	add_action('wp_ajax_nopriv_woof_messenger_add_subscr', array($this, 'woof_add_subscr'));
	add_action('wp_ajax_woof_messenger_remove_subscr', array($this, 'woof_remove_subscr'));
	add_action('wp_ajax_nopriv_woof_messenger_remove_subscr', array($this, 'woof_remove_subscr'));
	//+++
	// Check if user want to unsubscribe without auth  AND check external cron  Plugin shuld be init on  current page
	add_action('init', array($this, 'woof_unsubscr'));
        // add shortcode
        add_shortcode('woof_products_messenger',array($this,'woof_products_messenger_shortcode'));
   
       
	self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
	self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
	self::$includes['js_init_functions'][$this->html_type] = 'woof_init_products_messenger';
	
    }

    public function woof_unsubscr() {
	
	$this->woof_external_cron_init();
	if (!isset($_GET['id_user']) OR ! isset($_GET['key']) OR ! isset($_GET['woof_skey'])) {
	    return;
	}

	$sanit_get = $this->sanitaz_array_r($_GET);
	$subscr = get_user_meta($sanit_get['id_user'], $this->user_meta_key, true);
	$text = "";
	if ($subscr[$sanit_get['key']]['secret_key'] == $sanit_get['woof_skey']) {
	    unset($subscr[$sanit_get['key']]);
	    update_user_meta($sanit_get['id_user'], $this->user_meta_key, $subscr);

	} 
	$data['text'] = esc_html__('You are unsubscribed from the future products newsletters.', 'woocommerce-products-filter');
	woof()->render_html_e($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'unsubscr_template.php', $data);
	die();
    }

    public function woof_external_cron_init() {
	//check secret key  ( min  16 symbol )  
	if (!isset($_GET['woof_pm_cron_key']) OR empty($_GET['woof_pm_cron_key']) OR strlen($_GET['woof_pm_cron_key']) < 16) {
	    return false;
	}

	$sanitazed_key = esc_attr(sanitize_key($_GET['woof_pm_cron_key']));
	if ($sanitazed_key AND isset(woof()->settings['products_messenger']['use_external_cron']) AND woof()->settings['products_messenger']['use_external_cron'] === $sanitazed_key) {
	    $this->woof_do_mesenger_action();
	} else {
	    return false;
	}
    }

    public function woof_do_mesenger_action() { 
	global $wpdb;
	// get all users
	$users = get_users(array('count_total' => false, 'fields' => array('ID', 'display_name', 'user_login', 'user_nicename', 'user_email'),));
        
	foreach ($users as $user) {
	    $data_user = get_user_meta($user->ID, $this->user_meta_key, true); // get subscribtion of user 
	    if (empty($data_user) OR count($data_user) <= 0 OR ! is_array($data_user)) {
		continue;
	    }
	    foreach ($data_user as $key => $data_subscr) {    // check subcr
		$data_email = array();
		$data_email['products'] = array();
		$products = $wpdb->get_results($data_subscr['request']);
		foreach ($products as $p) {      // if it has new products
		    if (!in_array($p->ID, $data_subscr['product_ids'])) {
			$data_email['products'][] = $p->ID;
		    }
		}
               
		if (count($data_email['products']) > 0) {
		    $last_email = $by_date = $by_count = false;
		    (int) $data_user[$key]['count'] --;
		    if (((int) $data_user[$key]['count']) <= 0) {
			$by_count = true;
		    }
		    if (((int) $data_subscr['date']) < time()) {
			$by_date = true;
		    }
		    if ($this->priority_limit == 'by_date' AND $by_date) {   //priority by date  
			$last_email = true;
		    } elseif ($this->priority_limit == 'by_count' AND $by_count) { //priority by count 
			$last_email = true;
		    } elseif ($this->priority_limit == 'both' AND ( $by_count OR $by_date)) {   //both
			$last_email = true;
		    }
		    $data_email['user'] = $user;
		    $data_email['text_email'] = $this->text_email;
		    $data_email['subscr'] = $data_subscr;
		    $data_email['last_email'] = $last_email;

               
		    if ($last_email) {
			unset($data_user[$key]);
		    } else {
			$data_user[$key]['product_ids'] = array_merge($data_subscr['product_ids'], $data_email['products']);
		    }
                    $successful_sending=$this->create_new_email($data_email);
                    //safe all info. If the wp_email does not work, the data is not updated
                   
                    if($successful_sending){
                        update_user_meta($user->ID, $this->user_meta_key, $data_user);
                    }

		}
	    }
	}
    }

    public function create_new_email($data) {
	
	$mailer = $GLOBALS['woocommerce']->mailer();
	// get the preview email subject
	$email_heading = $this->header_email;
	$subject = $this->subject_email;
	// get the preview email content

	$message = woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'email_template.php', $data);


	// create a new email
	$email = new WC_Email();

	// wrap the content with the email template and then add styles
	$message = apply_filters('woocommerce_mail_content', $email->style_inline($mailer->wrap_message($email_heading, $message)));
	
	$headers=array();
	$headers[] = 'content-type: text/html';
        
        //create new  email name
        $home_url=array();
        $home_url=explode("//",home_url());
        $site_name="messenger.woof";
        if(isset($home_url[1])){
            $site_name=explode("/",$home_url[1] );
            $site_name= $site_name[0];
        }else{
            $site_name=explode("/",$home_url[0] );
            $site_name= $site_name[0];
        }
        $headers[]= 'From: '.get_bloginfo('name').' <no-reply@'.$site_name .'>';
	//+++
	// send email
	return wp_mail($data['user']->user_email, $subject, $message, $headers);
    }

    //settings page hook
    public function woof_print_html_type_options() {
	
	woof()->render_html_e($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'options.php', array(
	    'key' => $this->html_type,
	    "woof_settings" => get_option('woof_settings', array())
		)
	);
    }

    public function woof_add_subscr() {
	global $WOOF,$wpdb,$wp_query;

	if (! isset($_POST['link']) OR ! isset($_POST['user_id'])) {
	    die();
	}

	//***

	$data = array();
	$sanit_user_id = sanitize_key($_POST['user_id']);
	if ($sanit_user_id < 1) {
	    die(); //if user id - wrong!!!
	}

	$key = uniqid('woofms_'); // Create   key for this subscr
	$data['key'] = $key;
        
	$data['secret_key'] = bin2hex(random_bytes(9)); //Key for check link from email
        
	$data['user_id'] = $sanit_user_id;
	$data['link'] = sanitize_text_field($_POST['link']);
	$data['get'] =$this->woof_get_html_terms($this->sanitaz_array_r(isset($_POST['get_var'])?$_POST['get_var']: array()));
	
	$subscr = get_user_meta($data['user_id'], $this->user_meta_key, true);
        $data['request'] =$this->sanitazed_sql_query(base64_decode(woof()->storage->get_val("woof_pm_request_".$data['user_id'])));
        // If the request has banned operators or is empty
        if(!$data['request'] OR empty($data['request'])){
             die();
        }
        //+++
        //Remove limit frim request
        $pos = stripos($data['request'], "LIMIT");
        if($pos){
            $data['request']=substr($data['request'],0,$pos);
        }
        if(!is_array($subscr)){
            $subscr=array();
        }
	if (count($subscr) >= $this->subscr_count) {
	     die('<li class="woof_pm_max_count" >'.__('Сount is max', 'woocommerce-products-filter').'</li>'); // Check limit count on backend
	}
        //+++
	$data['subscr_lang'] = apply_filters('woof_subscribe_lang', $this->subscribe_lang); //Text of  the subscriptions

        
	$data['count'] = ((int) $this->count_message != -1) ? (int) $this->count_message : PHP_INT_MAX; // not limit* million times
	$data['date'] = time();
	if ($this->get_woof_cron_schedules($this->date_expire)) {
	    $data['date'] = $data['date'] + $this->get_woof_cron_schedules($this->date_expire); // date of expire
	} else {
	    $data['date'] = $data['date'] + YEAR_IN_SECONDS*10; // not limit*  10 years
	}

	$data['product_ids'] = array();
        $products=$wpdb->get_results($data['request']);
	foreach ($products as $product) {
	    $data['product_ids'][] = $product->ID; //Get current product ids
	}

	$subscr[$key] = $data;
	update_user_meta($data['user_id'], $this->user_meta_key, $subscr);
	//for Ajax redraw
	$cont = woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'item_list_subscr.php', $data);
	die($cont);
    }

    public function woof_remove_subscr() {
	if (!isset($_POST['key']) OR ! isset($_POST['user_id'])) {
	    die('No data!');
	}

	$user_id = sanitize_key($_POST['user_id']);
	$key = sanitize_key($_POST['key']);
	$subscr = get_user_meta($user_id, $this->user_meta_key, true);
	unset($subscr[$key]);
	update_user_meta($user_id, $this->user_meta_key, $subscr);
	$arg = array('key' => $key);
	die(json_encode($arg));
    }

    public function make_send_emails($reset = false) {
	if ($this->subscr_period_option != 'no' AND ! empty($this->subscr_period_option)) {
	    if ($this->wp_cron_period) {


		$this->woocs_wpcron_init();
	    }
	}
    }

    public function woocs_wpcron_init($remove = false) {
	if ($remove) {
	    $this->cron->remove($this->cron_hook);
	    return;
	}

	if ($this->wp_cron_period) {
	    if (!$this->cron->is_attached($this->cron_hook, $this->wp_cron_period)) {
		$this->cron->attach($this->cron_hook, time(), $this->wp_cron_period);
	    }

	    $this->cron->process();
	}
    }

    public function get_woof_cron_schedules($key = '') {
	$schedules = array(
	    'hourly' => HOUR_IN_SECONDS,
	    'twicedaily' => HOUR_IN_SECONDS * 12,
	    'daily' => DAY_IN_SECONDS,
	    'week' => WEEK_IN_SECONDS,
	    'twicemonthly' => WEEK_IN_SECONDS * 2,
	    'month' => WEEK_IN_SECONDS * 4,
	    'twomonth' => WEEK_IN_SECONDS * 9,
	    'min1' => MINUTE_IN_SECONDS, // only for test
	);
	if (!empty($key) AND isset($schedules[$key])) {
	    return (int) $schedules[$key];
	} else {
	    return NULL;
	}

	return $schedules;
    }

    public function woof_products_messenger() {
	add_action('init', array($this, 'woof_do_mesenger_action'), 999); // init messeng function
    }

    //it create  html for tooltip and list of the terms in email
    public function woof_get_html_terms($args) {
	$html = "";
       
	$not_show = array( 'swoof','paged','orderby', 'min_price', 'max_price', 'woof_author','page');
	if (isset($args['min_price'])) {
	    $price_text = sprintf(__('Price - from %s to %s', 'woocommerce-products-filter'), $args['min_price'], $args['max_price']);
	    $price_text .= '<br />';
	    $html .= '<span class="woof_subscr_price">' . $price_text . '</span>';
	}
	if (isset($args['woof_author'])) {
	    $ids = explode(',', $args['woof_author']);
	    $auths = "";
	    foreach ($ids as $auth) {
		$auths .= " " . get_userdata((int) $auth)->display_name;
	    }
	    $html .= "<span class='woof_author_name'>" . $auths . "</span><br />";
	}
 
	foreach ($args as $key => $val) {
            
	    if (in_array($key, $not_show)) {
		continue;
	    }
            
            if(class_exists('WOOF_META_FILTER')){
                $meta_title=WOOF_META_FILTER::get_meta_title_messenger($val, $key);
                //var_dump($meta_title);
                if(!empty($meta_title) AND $meta_title){
                    $html .= $meta_title; 
                    
                    continue;
                }
            }
			 if (class_exists('WOOF_ACF_FILTER')) {
                $acf_title = WOOF_ACF_FILTER::get_meta_title_messenger($val, $key);
                if (!empty($acf_title) AND $acf_title) {
                    $html .= $acf_title;

                    continue;
                }
            }	
            $tax=get_taxonomy($key);
            if(is_object($tax)){
                $name = $tax->labels->name;
                if (!empty($name)) {
                    $name .= ": ";
                }
                $name .= $val;
                $html .= "<span class='woof_terms'>" . $name . "</span><br />";                
            }

	}
	if (empty($html)) {
	   $html = esc_html__('None', 'woocommerce-products-filter');
	}

	return $html;
    }

    // Recursive sanitaze arrais
    public function sanitaz_array_r($arr) {
	$newArr = array();
	foreach ($arr as $key => $value) {
	    $newArr[WOOF_HELPER::escape($key)] = ( is_array($value) ) ? $this->sanitaz_array_r($value) : WOOF_HELPER::escape($value);
	}
	return $newArr;
    }

    public function wp_head() {
		$txt_js = "";
		ob_start();
		?>
			var woof_confirm_lang = "<?php esc_html_e('Are you sure?', 'woocommerce-products-filter') ?>";
		<?php
		$txt_js = ob_get_clean();
		self::$includes['js_code_custom'][$this->html_type] = $txt_js;

    }
    public function  woof_products_messenger_shortcode($args){
        $data=shortcode_atts(array(
	    'in_filter' => 0
			), $args);
        
        if(file_exists($this->get_ext_override_path(). 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_products_messenger.php')){
            return woof()->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_products_messenger.php', $data);
        }
        return woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes'. DIRECTORY_SEPARATOR.'woof_products_messenger.php',$data);
    }

    public function  sanitazed_sql_query($sql){
        $conditional_operator=array('TRUNCATE','DELETE','UPDATE','INSERT','REPLACE','CREATE');
        foreach($conditional_operator as $operator){
            $result=stripos($sql,$operator);
            if($result!==false){
                return false;
                break;
            }
        }
        return $sql;
    }
}

WOOF_EXT::$includes['html_type_objects']['products_messenger'] = new WOOF_EXT_PRODS_MESSENGER();
