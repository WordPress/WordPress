<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_BY_RATING extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'by_rating'; //your custom key here
    public $index = 'byrating';
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
		
		self::$includes['js_lang_custom'][$this->index] = esc_html__('By rating', 'woocommerce-products-filter');

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

    public function assemble_query_params(&$meta_query, $wp_query = NULL)
    {
        
        $request = woof()->get_request_data();
        if (isset($request['min_rating']))
        {
            $min = $request['min_rating'];
            if ($min == 4)
            {
                $max = 5;
            } else
            {
                $max = $min + 1 - 0.001;
            }
            $meta_query[] = array(
                'key' => '_wc_average_rating',
                'value' => array($min, $max),
                'type' => 'DECIMAL',
                'compare' => 'BETWEEN'
            );
        }


        return $meta_query;
    }

}

WOOF_EXT::$includes['html_type_objects']['by_rating'] = new WOOF_EXT_BY_RATING();
