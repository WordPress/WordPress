<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

class WOOF_META_FILTER_MSELECT extends WOOF_META_FILTER_TYPE {

    public $type = 'mselect';
    public $js_func_name = 'woof_init_meta_mselects';

    public function __construct($key, $options, $woof_settings) {
        parent::__construct($key, $options, $woof_settings);
        $this->value_type = (isset($this->woof_settings['meta_filter'][$this->meta_key]['type'])) ? $this->woof_settings['meta_filter'][$this->meta_key]['type'] : 'string';
        $this->init();
    }

    public function init() {
        if (!isset($this->woof_settings[$this->meta_key]['search_logic'])) {
            $this->woof_settings[$this->meta_key]['search_logic'] = "OR";
        }

        add_action('woof_print_html_type_options_' . $this->meta_key, array($this, 'draw_meta_filter_structure'));
        add_action('woof_print_html_type_' . $this->meta_key, array($this, 'woof_print_html_type_meta'));
        add_action('wp_footer', array($this, 'wp_footer'));
        add_filter('woof_extensions_type_index', array($this, 'add_type_index'));
    }

    public function wp_footer() {
        wp_enqueue_script('meta-mselect-js', $this->get_meta_filter_link() . 'js/mselect.js', array('jquery'), WOOF_VERSION, true);
        wp_enqueue_style('meta-mselect-css', $this->get_meta_filter_link() . 'css/mselect.css', array(), WOOF_VERSION);
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

    protected function draw_additional_options() {
        $this->render_html_e($this->get_meta_filter_path() . '/views/additional_options.php', [
            'key' => $this->meta_key,
            'settings' => $this->woof_settings
        ]);
    }

    public function woof_print_html_type_meta() {
        $data['meta_key'] = $this->meta_key;
        $data['options'] = $this->type_options;
        $data['relation'] = (isset($this->woof_settings[$this->meta_key]["search_logic"])) ? $this->woof_settings[$this->meta_key]["search_logic"] : "OR";
        $data['meta_options'] = (isset($this->type_options["options"])) ? $this->type_options["options"] : "";
        $data['meta_settings'] = (isset($this->woof_settings[$this->meta_key])) ? $this->woof_settings[$this->meta_key] : "";
        $data['options_separator'] = $this->options_separator;
        if (isset($this->woof_settings[$this->meta_key]["show"]) AND $this->woof_settings[$this->meta_key]["show"]) {
            if (file_exists($this->get_meta_filter_override_path() . 'views' . DIRECTORY_SEPARATOR . 'woof.php')) {
                $this->render_html_e($this->get_meta_filter_override_path() . 'views' . DIRECTORY_SEPARATOR . 'woof.php', $data);
            } else {
                $this->render_html_e($this->get_meta_filter_path() . '/views/woof.php', $data);
            }
        }
    }

    protected function check_current_request() {

        $request = woof()->get_request_data();
        if (isset($request[$this->type . "_" . $this->meta_key]) AND $request[$this->type . "_" . $this->meta_key]) {
            return explode($this->options_separator, $request[$this->type . "_" . $this->meta_key]);
        }
        return array();
    }

    public function create_meta_query() {
        $curr_text = "";
        $arr_index = $this->check_current_request();
        $options = (isset($this->type_options["options"])) ? $this->type_options["options"] : "";
        $options = explode($this->options_separator, $options);
        $meta = array();
        foreach ($arr_index as $curr_index) {
            if (isset($options[intval($curr_index) - 1])) {
                $curr_text = $options[intval($curr_index) - 1];
                $custom_title = explode('^', $options[intval($curr_index) - 1], 2);
                if (count($custom_title) > 1) {
                    $curr_text = $custom_title[1];
                }
            }
            if ($curr_text) {
                $meta[] = array(
                    'key' => $this->meta_key,
                    'value' => $curr_text,
                    'compare' => '=',
                    'type' => $this->value_type,
                );
            } else {
                continue;
            }
        }
        if (!empty($meta)) {
            $meta['relation'] = (isset($this->woof_settings[$this->meta_key]["search_logic"])) ? $this->woof_settings[$this->meta_key]["search_logic"] : "OR";
            return $meta;
        } else {
            return false;
        }
    }

    public function get_js_func_name() {
        return $this->js_func_name;
    }

    public static function get_option_name($value, $key = NULL) {
        $option_txt = "";

        if ($key) {
            $meta_key = str_replace("mselect_", "", $key);
            $options = explode($this->options_separator, (isset(woof()->settings['meta_filter'][$meta_key]["options"])) ? woof()->settings['meta_filter'][$meta_key]["options"] : "");
            $value_arr = explode($this->options_separator, $value);
            $opt = array();
            if (!empty($options)) {
                foreach ($value_arr as $val) {
                    if (isset($options[intval($val) - 1])) {
                        $op_title = explode('^', $options[intval($val) - 1], 2);
                        if (count($op_title) > 1) {
                            $curr_title = $op_title[0];
                        } else {
                            $curr_title = $options[intval($val) - 1];
                        }
                        $opt[] = WOOF_HELPER::wpml_translate(null, $curr_title);
                    }
                }
                $option_txt = implode(',', $opt);
            }
        }
        return $option_txt;
    }

}
