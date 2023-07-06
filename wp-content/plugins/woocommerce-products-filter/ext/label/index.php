<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_LABEL extends WOOF_EXT
{

    public $type = 'html_type';
    public $html_type = 'label'; //your custom key here
    public $html_type_dynamic_recount_behavior = 'multi';

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

    public function woof_add_html_types($types)
    {
        $types[$this->html_type] = esc_html__('Label', 'woocommerce-products-filter');
        return $types;
    }

    public function init()
    {
        add_filter('woof_add_html_types', array($this, 'woof_add_html_types'));
        self::$includes['js']['woof_label_html_items'] = $this->get_ext_link() . 'js/html_types/label.js';
        self::$includes['css']['woof_label_html_items'] = $this->get_ext_link() . 'css/html_types/label.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_labels';
    }



}

WOOF_EXT::$includes['taxonomy_type_objects']['label'] = new WOOF_EXT_LABEL();
