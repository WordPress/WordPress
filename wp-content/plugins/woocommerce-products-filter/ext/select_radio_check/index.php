<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_SEL_RADIO_CHECK extends WOOF_EXT {

    public $type = 'html_type';
    public $html_type = 'select_radio_check'; //your custom key here
    public $html_type_dynamic_recount_behavior = 'multi'; //doesnt matter in this extension

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

    public function woof_add_html_types($types) {
        $types[$this->html_type] = esc_html__('Radio/Checkbox in drop-down', 'woocommerce-products-filter');
        return $types;
    }

    public function init() {
        add_filter('woof_add_html_types', array($this, 'woof_add_html_types'));
        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/html_types/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/html_types/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_' . $this->html_type;

        $this->taxonomy_type_additional_options = array(
            'select_radio_check_type' => array(
                'title' => esc_html__('Type', 'woocommerce-products-filter'),
                'tip' => esc_html__('How to display this filter-element on the site frontend', 'woocommerce-products-filter'),
                'type' => 'select',
                'options' => array(
                    0 => esc_html__('Radio', 'woocommerce-products-filter'),
                    1 => esc_html__('Checkbox', 'woocommerce-products-filter')
                )
            ),
            'select_radio_check_height' => array(
                'title' => esc_html__('Height', 'woocommerce-products-filter'),
                'tip' => esc_html__('Height of the drop-down of the opened active panel.', 'woocommerce-products-filter'),
                'type' => 'text',
                'placeholder' => esc_html__('enter value in px', 'woocommerce-products-filter'),
            )
        );

        if (intval(WOOF_VERSION) === 1) {
            unset($this->taxonomy_type_additional_options['select_radio_check_type']['options'][1]);
        }
    }

}

WOOF_EXT::$includes['taxonomy_type_objects']['select_radio_check'] = new WOOF_EXT_SEL_RADIO_CHECK();

