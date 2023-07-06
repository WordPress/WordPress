<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_IMAGE extends WOOF_EXT {

    public $type = 'html_type';
    public $html_type = 'image'; //your custom key here
    public $html_type_dynamic_recount_behavior = 'multi';

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

    public function init() {
        
    }

}

WOOF_EXT::$includes['taxonomy_type_objects']['image'] = new WOOF_EXT_IMAGE();
