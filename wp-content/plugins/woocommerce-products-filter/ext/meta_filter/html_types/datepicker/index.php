<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

class WOOF_META_FILTER_DATEPICKER extends WOOF_META_FILTER_TYPE {

    public $type = 'datepicker';
    public $js_func_name = "woof_init_meta_datepicker";
    public $format = "unix";

    public function __construct($key, $options, $woof_settings) {
        parent::__construct($key, $options, $woof_settings);
        $this->value_type = (isset($this->woof_settings['meta_filter'][$this->meta_key]['type'])) ? $this->woof_settings['meta_filter'][$this->meta_key]['type'] : 'NUMERIC';
        $this->init();
    }

    public function init() {
        add_action('woof_print_html_type_options_' . $this->meta_key, array($this, 'draw_meta_filter_structure'));
        add_action('woof_print_html_type_' . $this->meta_key, array($this, 'woof_print_html_type_meta'));
        add_action('wp_footer', array($this, 'wp_footer'));

        add_filter('woof_extensions_type_index', array($this, 'add_type_index'));
    }

    public function get_meta_filter_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_meta_filter_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . 'meta_filter' . DIRECTORY_SEPARATOR . "html_types" . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function get_meta_filter_link() {
        return plugin_dir_url(__FILE__);
    }

    public function add_type_index($indexes) {
        $indexes[] = '"' . $this->type . "_" . $this->meta_key . '"';
        return $indexes;
    }

    public function wp_footer() {
        wp_enqueue_script('jquery-ui-datepicker', array('jquery'));
        wp_enqueue_script('meta-datepicker-js', $this->get_meta_filter_link() . 'js/datepicker.js', array('jquery'), WOOF_VERSION, true);
        wp_enqueue_style('meta-datepicker-css', $this->get_meta_filter_link() . 'css/datepicker.css', array(), WOOF_VERSION);
    }

    public function woof_print_html_type_meta() {
        $data['meta_key'] = $this->meta_key;
        $data['options'] = $this->type_options;
        $data['meta_settings'] = $data['meta_options'] = (isset($this->woof_settings[$this->meta_key])) ? $this->woof_settings[$this->meta_key] : "";
        if (isset($this->woof_settings[$this->meta_key]["show"]) AND $this->woof_settings[$this->meta_key]["show"]) {
            if (file_exists($this->get_meta_filter_override_path() . 'views' . DIRECTORY_SEPARATOR . 'woof.php')) {
                $this->render_html_e($this->get_meta_filter_override_path() . 'views' . DIRECTORY_SEPARATOR . 'woof.php', $data);
            } else {
                $this->render_html_e($this->get_meta_filter_path() . '/views/woof.php', $data);
            }
        }
    }

    protected function draw_additional_options() {
        $this->render_html_e($this->get_meta_filter_path() . '/views/additional_options.php', [
            'key' => $this->meta_key,
            'settings' => $this->woof_settings
        ]);
    }

    protected function check_current_request() {

        $request = woof()->get_request_data();
        if (isset($request[$this->type . "_" . $this->meta_key]) AND $request[$this->type . "_" . $this->meta_key]) {
            return $request[$this->type . "_" . $this->meta_key];
        }
        return false;
    }

    public function create_meta_query() {
        $curr_request = $this->check_current_request();
        if ($curr_request) {
            $curr_range = array();
            $curr_range = explode("-", $curr_request);
            $from = "";
            $to = "";

            $type = 'numeric';

            if ($this->value_type != 'NUMERIC') {
                $type = 'DATE';
            }
            if ($from != "i") {
                $from = $curr_range[0];
                if ($type == 'DATE') {
                    $from = date('Y-M-D', $from);
                    $to = date('Y-M-D', 5396281199);
                }
            }

            if (count($curr_range) > 1 AND $curr_range[1] != "i") {
                $to = $curr_range[1];
                if ($type == 'DATE') {
                    $from = date('Y-M-D', 0);
                    $to = date('Y-M-D', $to);
                }
            }
            $meta = array();

            //acf datepicker

            if (apply_filters('woof_meta_use_acf_dateformat', false)) {
                if (!$to) {
                    $to = PHP_INT_MAX;
                }
                $from = date('Ymd', intval($from)); //int must be another way fatal error because from DB it cames as string
                $to = date('Ymd', intval($to)); //int must be another way fatal error because from DB it cames as string
            }

            if ($from AND $to) {
                $meta = array(
                    'key' => $this->meta_key,
                    'value' => array($from, $to),
                    'type' => $type,
                    'compare' => 'BETWEEN',
                );
            } elseif ((!$from AND $to) OR ($from AND !$to)) {
                $compare = ">";
                if ($from AND !$to) {
                    $val = $from;
                } else {
                    $val = $to;
                    $compare = "<";
                }
                $meta = array(
                    'key' => $this->meta_key,
                    'value' => $val,
                    'type' => $type,
                    'compare' => $compare,
                );
            }
            return apply_filters('woof_meta_data_datapicker', $meta, $this->meta_key);
        } else {
            return false;
        }
    }

    public function get_js_func_name() {
        return $this->js_func_name;
    }

    public static function get_option_name($value, $key = NULL) {

        $value_txt = "";

        $arr_val = explode("-", $value, 2);
        if (count($arr_val) > 1) {

            $meta_key = str_replace("datepicker_", "", $key);
            $format = (isset(woof()->settings[$meta_key]['format'])) ? woof()->settings[$meta_key]['format'] : "m/d/y";
            $format_compatibility = array(
                'mm/dd/yy' => "m/d/y",
                'dd-mm-yy' => 'd-m-y',
                'yy-mm-dd' => 'y-m-d',
                'D, d M, yy' => 'D, d M, Y',
                'd MM, y' => 'd M, y',
            );

            if (isset($format_compatibility[$format])) {
                $format = $format_compatibility[$format];
            }
            if ($arr_val[0] AND $arr_val[0] != "i") {
                $value_txt .= " ";
                $value_txt .= sprintf(esc_html__("from: %s", 'woocommerce-products-filter'), date($format, $arr_val[0]));
            }
            if ($arr_val[1] AND $arr_val[1] != "i") {
                $value_txt .= " ";
                $value_txt .= sprintf(esc_html__("to: %s", 'woocommerce-products-filter'), date($format, $arr_val[1]));
            }
        }

        return $value_txt;
    }

}
