<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_SLIDER extends WOOF_EXT {

    public $type = 'html_type';
    public $html_type = 'slider'; //your custom key here
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

    public function woof_add_html_types($types) {
        $types[$this->html_type] = esc_html__('Slider', 'woocommerce-products-filter');
        return $types;
    }

    public function init() {
        add_filter('woof_add_html_types', array($this, 'woof_add_html_types'));
        add_action('wp_head', array($this, 'wp_head'), 999);
        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/html_types/slider.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/html_types/slider.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_sliders';

        $this->taxonomy_type_additional_options = array(
            'slider_dynamic_recount' => array(
                'title' => esc_html__('Enable dynamic recount', 'woocommerce-products-filter'),
                'tip' => esc_html__('Activates dynamic recount for each term in the the range. If you have very long range (a lot of terms) this feature will generate additional MySQL queries which will create additional loading on the server!', 'woocommerce-products-filter'),
                'type' => 'select',
                'options' => array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                )
            ),
            'slider_grid_step' => array(
                'title' => esc_html__('Grid step', 'woocommerce-products-filter'),
                'tip' => esc_html__('Hide each Nth grid label. -1 hide all labels, 1 show all labels, 2n show each second label, 3n show each third label, etc...', 'woocommerce-products-filter'),
                'type' => 'text',
            ),
            'tax_slider_skin' => array(
                'title' => esc_html__('Slider skin', 'woocommerce-products-filter'),
                'tip' => esc_html__('For each taxonomy it is possible to select a unique slider design', 'woocommerce-products-filter'),
                'type' => 'select',
                'options' => array(
                    0 => esc_html__('Default', 'woocommerce-products-filter'),
                    'round' => 'Round',
                    'flat' => 'skinFlat',
                    'big' => 'skinHTML5',
                    'modern' => 'skinModern',
                    'sharp' => 'Sharp',
                    'square' => 'Square',
                )
            )
        );
    }

    public function wp_head() {
        wp_enqueue_script('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/js/ion.rangeSlider.min.js', array('jquery'), WOOF_VERSION);
        wp_enqueue_style('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/css/ion.rangeSlider.css', array(), WOOF_VERSION);
    }

}

WOOF_EXT::$includes['taxonomy_type_objects']['slider'] = new WOOF_EXT_SLIDER();
