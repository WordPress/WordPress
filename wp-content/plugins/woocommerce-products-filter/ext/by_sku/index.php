<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_BY_SKU extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'by_sku'; //your custom key here
    public $index = 'woof_sku'; //index in the search query
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
        
    }

}

WOOF_EXT::$includes['html_type_objects']['by_sku'] = new WOOF_EXT_BY_SKU();
