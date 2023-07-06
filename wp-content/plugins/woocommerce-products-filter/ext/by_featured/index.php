<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_BY_FEATURED extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'by_featured'; //your custom key here
    public $index = 'featured';
    public $html_type_dynamic_recount_behavior = 'none';

    public function __construct() {
        parent::__construct();
        $this->init();
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . $this->html_type . DIRECTORY_SEPARATOR;
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
        add_action('woocommerce_product_query', array($this, 'parse_query'));
        add_action('woof_get_tax_query', array($this, "woof_get_tax_query"), 9999);


        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_featured';
        self::$includes['js_lang_custom'][$this->index] = esc_html__('Featured product', 'woocommerce-products-filter');
    }

    //settings page hook
    public function woof_print_html_type_options() {
        
        woof()->render_html_e($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'options.php', array(
            'key' => $this->html_type,
            "woof_settings" => get_option('woof_settings', array())
                )
        );
    }

    public function parse_query($wp_query) {
        if (!isset($wp_query->query['post_type']) OR $wp_query->query['post_type'] != 'product') {
            //return $wp_query;
        }

        if (!empty($wp_query->tax_query) AND isset($wp_query->tax_query->queries)) {
            
            $request = woof()->get_request_data();
            if (isset($request["product_visibility"]) AND $request["product_visibility"] == 'featured') {
                $tax_query = $wp_query->tax_query->queries;
                $tax_query = $this->add_to_tax_query($tax_query);
                $wp_query->set('tax_query', $tax_query);
            }
        }
    }

    public function add_to_tax_query($tax_query) {
        $tax_query[] = array(
            'taxonomy' => 'product_visibility',
            'field' => 'name',
            'terms' => 'featured',
        );
        return $tax_query;
    }

    public function woof_get_tax_query($tax_query) {

        
        $request = woof()->get_request_data();
        if (isset($request["product_visibility"]) AND $request["product_visibility"] == 'featured') {
            $tax_query = $this->add_to_tax_query($tax_query);
        }
        return $tax_query;
    }

}

WOOF_EXT::$includes['html_type_objects']['by_featured'] = new WOOF_EXT_BY_FEATURED();
