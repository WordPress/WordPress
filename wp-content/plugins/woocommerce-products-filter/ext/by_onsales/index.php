<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_BY_ONSALES extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'by_onsales'; //your custom key here
    public $index = 'onsales';
    public $html_type_dynamic_recount_behavior = 'none';

    public function __construct()
    {
        parent::__construct();
        $this->init();
    }

    public function get_ext_path()
    {
        return plugin_dir_path(__FILE__);
    }
    public function get_ext_override_path()
    {
        return get_stylesheet_directory(). DIRECTORY_SEPARATOR ."woof". DIRECTORY_SEPARATOR ."ext". DIRECTORY_SEPARATOR .$this->html_type. DIRECTORY_SEPARATOR;
    }
    public function get_ext_link()
    {
        return plugin_dir_url(__FILE__);
    }

    public function woof_add_items_keys($keys)
    {
        $keys[] = $this->html_type;
        return $keys;
    }

    public function init()
    {
        add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
        add_action('woof_print_html_type_options_' . $this->html_type, array($this, 'woof_print_html_type_options'), 10, 1);
        add_action('woof_print_html_type_' . $this->html_type, array($this, 'print_html_type'), 10, 1);

        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_onsales';
		self::$includes['js_lang_custom'][$this->index] = esc_html__('On sale', 'woocommerce-products-filter');
		
		add_filter('woof_dynamic_count_attr', array($this, 'dynamic_recount'), 30, 2);
    }

    //settings page hook
    public function woof_print_html_type_options()
    {        
        woof()->render_html_e($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'options.php', array(
            'key' => $this->html_type,
            "woof_settings" => get_option('woof_settings', array())
                )
        );
    }
    public function dynamic_recount($args, $type) {
		if( 'onsale' == $type){
			$all_ids = wc_get_product_ids_on_sale() ;
			if(!count($all_ids)){
				$all_ids = array(0);
			}
			if(!isset($args['post__in'])){
				$args['post__in'] = $all_ids ;
			}else{
				$args['post__in'] = array_map($args['post__in'], $all_ids);
			}
		}		
		return $args;
	}
	public function assemble_query_params(&$meta_query, &$query = NULL)
    {
        
        $request = woof()->get_request_data();
        //http://stackoverflow.com/questions/20990199/woocommerce-display-only-on-sale-products-in-shop

        if (isset($request['onsales']) AND $request['onsales'] == 'salesonly')
        {


            if (is_object($query))
            {
                $query->set('post__in', array_merge(array(0), wc_get_product_ids_on_sale() ));
            }

            if (is_array($query))
            {
                $query['post__in'] = array_merge(array(0), wc_get_product_ids_on_sale() );
            }

            add_filter('woof_products_query', array($this, 'woof_products_query'), 9999);
        }

        return $meta_query;
    }

    public function woof_products_query($args)
    {
        $args['post__in'] = array_merge(array(0), wc_get_product_ids_on_sale());
        return $args;
    }

}

WOOF_EXT::$includes['html_type_objects']['by_onsales'] = new WOOF_EXT_BY_ONSALES();
