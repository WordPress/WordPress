<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

class WOOF_ACF_FILTER_RADIO extends WOOF_ACF_FILTER_TYPE {

    public $type = 'radio';
    protected $js_func_name = "";

    public function __construct($key, $options, $woof_settings) {
        parent::__construct($key, $options, $woof_settings);
       // $this->value_type = (isset($this->woof_settings['meta_filter'][$this->meta_key]['title'])) ? $this->woof_settings['meta_filter'][$this->meta_key]['title'] : 'string';
        $this->init();
    }

    public function init() {
        if (!isset($this->woof_settings[$this->meta_key]['search_option'])) {
            $this->woof_settings[$this->meta_key]['search_option'] = 0;
        }
        if (!isset($this->woof_settings[$this->meta_key]['search_value'])) {
            $this->woof_settings[$this->meta_key]['search_value'] = "";
        }

        add_action('woof_print_html_type_options_' . $this->meta_key, array($this, 'draw_meta_filter_structure'));
        add_action('woof_print_html_type_' . $this->meta_key, array($this, 'woof_print_html_type_meta'));
        add_action('wp_footer', array($this, 'wp_footer'));
        add_filter('woof_extensions_type_index', array($this, 'add_type_index'));
    }

    public function add_type_index($indexes) {
        $indexes[] = '"' . $this->meta_key . '"';
        return $indexes;
    }

    public function wp_footer() {
        //wp_enqueue_script('acf-radio-js', $this->get_meta_filter_link() . 'js/radio.js', array('jquery'), WOOF_VERSION, true);
    }

    public function get_meta_filter_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_meta_filter_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . 'acf_filter' . DIRECTORY_SEPARATOR . "html_types" . DIRECTORY_SEPARATOR . $this->type . DIRECTORY_SEPARATOR;
    }

    public function get_meta_filter_link() {
        return plugin_dir_url(__FILE__);
    }

    public function woof_print_html_type_meta() {
        $data['meta_key'] = $this->meta_key;
        $data['options'] = $this->type_options['meta_options'];
		$data['meta_title'] = $this->type_options['title'];

        if (isset($this->woof_settings[$this->meta_key]["show"]) AND $this->woof_settings[$this->meta_key]["show"]) {

            if (file_exists($this->get_meta_filter_override_path() . 'views' . DIRECTORY_SEPARATOR . 'woof.php')) {
                $this->render_html_e($this->get_meta_filter_override_path() . 'views' . DIRECTORY_SEPARATOR . 'woof.php', $data);
            } else {
                $this->render_html_e($this->get_meta_filter_path() . '/views/woof.php', $data);
            }
        }
    }

    protected function draw_additional_options() {
        $data = array();
        $data['key'] = $this->meta_key;
        $data['settings'] = $this->woof_settings;
        $this->render_html_e($this->get_meta_filter_path() . '/views/additional_options.php', $data);
    }

    public function create_meta_query() {
        $curr_text = $this->check_current_request();
        if ($curr_text) {		
			if (isset($this->type_options['meta_options'][$curr_text])) {
                $meta = array(
                    'key' => $this->meta_key,
                    'value' => $this->type_options['meta_options'][$curr_text],
                    'compare' => '=',

                );				
		
				return $meta;
			} 
		}
		return false;
    }

    protected function check_current_request() {

        $request = woof()->get_request_data();
        if (isset($request[$this->meta_key]) AND $request[$this->meta_key]) {
            return $request[$this->meta_key];
        }
        return false;
    }

    public function get_js_func_name() {
        return $this->js_func_name;
    }

    public static function get_option_name($value, $key = NULL) {
        return false;
    }

}

