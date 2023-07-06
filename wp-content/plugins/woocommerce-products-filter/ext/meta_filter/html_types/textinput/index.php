<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

class WOOF_META_FILTER_TEXTINPUT extends WOOF_META_FILTER_TYPE {

    public $type = 'textinput';
    protected $js_func_name = "woof_init_meta_text_input";

    public function __construct($key, $options, $woof_settings) {
        parent::__construct($key, $options, $woof_settings);
        $this->value_type = (isset($this->woof_settings['meta_filter'][$this->meta_key]['type'])) ? $this->woof_settings['meta_filter'][$this->meta_key]['type'] : 'string';
        $this->init();
    }

    public function init() {
        if (!isset($this->woof_settings[$this->meta_key]['text_conditional'])) {
            $this->woof_settings[$this->meta_key]['text_conditional'] = "LIKE";
        }
        add_action('woof_print_html_type_options_' . $this->meta_key, array($this, 'draw_meta_filter_structure'));
        add_action('woof_print_html_type_' . $this->meta_key, array($this, 'woof_print_html_type_meta'));
        add_action('wp_footer', array($this, 'wp_footer'));
        add_action('wp_head', array($this, 'wp_head'), 9);
        add_filter('woof_extensions_type_index', array($this, 'add_type_index'));
    }

    public function wp_head() {
        WOOF_EXT::$includes['js_lang_custom'][$this->type . "_" . $this->meta_key] = WOOF_HELPER::wpml_translate(null, $this->woof_settings['meta_filter'][$this->meta_key]['title']);
    }

    public function add_type_index($indexes) {
        $indexes[] = '"' . $this->type . "_" . $this->meta_key . '"';
        return $indexes;
    }

    public function wp_footer() {
        wp_enqueue_script('meta-textinput-js', $this->get_meta_filter_link() . 'js/textinput.js', array('jquery'), WOOF_VERSION, true);
        wp_enqueue_style('meta-textinput-css', $this->get_meta_filter_link() . 'css/textinput.css', array(), WOOF_VERSION);
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

    public function woof_print_html_type_meta() {
        $data['meta_key'] = $this->meta_key;
        $data['options'] = $this->type_options;
        $data['loader_img'] = WOOF_LINK . 'img/eye-icon1.png';
        if (isset($this->woof_settings[$this->meta_key]) AND isset($this->woof_settings[$this->meta_key]["show"]) AND $this->woof_settings[$this->meta_key]["show"]) {
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
        $curr_text = $this->check_current_request();

		$curr_text = htmlspecialchars_decode($curr_text);
		$curr_text = str_replace("&#039;", "'", $curr_text);

        if ($curr_text) {
            $compare = 'LIKE';
            if (isset($this->woof_settings[$this->meta_key]["text_conditional"]) AND $this->woof_settings[$this->meta_key]["text_conditional"] == '=') {
                $compare = '=';
            }
            $meta = array(
                'key' => $this->meta_key,
                'value' => $curr_text,
                'compare' => $compare,
                'type' => $this->value_type,
            );
            return $meta;
        } else {
            return false;
        }
    }

    public function woof_get_meta_query($meta_query) {
        $meta = $this->create_meta_query();
        if ($meta) {
            $meta_query = array_merge($meta_query, array($meta));
        }
        return $meta_query;
    }

    public function get_js_func_name() {
        return $this->js_func_name;
    }

    public static function get_option_name($value, $key = NULL) {
        return '"' . $value . '"';
    }

}
