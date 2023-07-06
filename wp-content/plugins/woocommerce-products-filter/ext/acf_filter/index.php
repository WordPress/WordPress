<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_ACF_FILTER extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'acf_filter'; //should be defined!!
    public $allowed_meta_type = array('select', 'radio', 'true_false');
    public static $acf_fields = array();
    public $meta_filters_obj = array();

    //***
    public function __construct() {
        parent::__construct();
        self::$acf_fields = $this->get_all_acf_meta();
        require_once $this->get_ext_path() . 'classes/woof_type_acf_filter.php';
        $this->init();
    }

    public function get_ext_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . $this->html_type . DIRECTORY_SEPARATOR;
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {
        foreach (self::$acf_fields as $key => $data) {
            $this->conect_activate_meta_filter($key, $data);
        }
        add_filter('woof_get_all_filter_titles', array($this, 'add_titles'));
        add_filter('woof_add_items_keys', array($this, 'add_items_keys'));

        // Create meta query
        add_filter('woof_get_meta_query', array($this, 'woof_get_meta_query'));
    }

    public function wp_head() {
        
    }

    public function add_items_keys($arr_keys) {
        foreach (self::$acf_fields as $key => $data) {
            $arr_keys[] = $data['meta_key'];
        }
        return $arr_keys;
    }

    public function get_all_acf_meta() {

        if (!function_exists('acf_get_field_groups')) {
            return array();
        }

        $acf = acf_get_field_groups();

        $fields = array();
        $meta = array();

        foreach ($acf as $item) {

            $fields = acf_get_fields($item);
            $group_name = $item['title'];
            foreach ($fields as $field) {
                $type = $field['type'];
                if (!in_array($type, $this->allowed_meta_type)) {
                    continue;
                }
                
                $options = array();

                if (isset($field['choices'])) {
                    foreach ($field['choices'] as $o_key => $o_val) {
                        $r_key = $o_key;
                        $r_key = explode(':', $r_key);
                        $o_key = $r_key[0];

                        $options[$o_key] = $o_val;
                        $options[$o_key] = str_replace("'", '&prime;', $options[$o_key]);
                        $options[$o_key] = str_replace('"', '&Prime;', $options[$o_key]);
                    }
                }
                
                if ('true_false' == $field['type']) {
                    $options = array(
                        1 => 'Yes'
                    );
                }
                
                $t_m = $field['label'];
                $t_m = str_replace("'", '&prime;', $t_m);
                $t_m = str_replace('"', '&Prime;', $t_m);

                $meta[$field['name']] = array(
                    'meta_key' => $field['name'],
                    'title' => $t_m,
                    'meta_type' => $type,
                    'meta_options' => $options,
                    'group_name' => $group_name
                );
            }
        }

        return $meta;
    }

    public function conect_activate_meta_filter($key, $options) {
        $class_name = 'WOOF_ACF_FILTER_' . strtoupper($options['meta_type']);
        require_once $this->get_ext_path() . 'html_types/' . $options['meta_type'] . '/index.php';
        if (class_exists($class_name)) {
            $this->meta_filters_obj[$key] = new $class_name($key, $options, $this->woof_settings);
            if ($this->meta_filters_obj[$key]->get_js_func_name()) {
                self::$includes['js_init_functions']["acf_" . $options['meta_type']] = $this->meta_filters_obj[$key]->get_js_func_name();
            }
        }
    }

    public function woof_get_meta_query($meta_query) {
        $meta_filter_query = array();
        foreach ($this->meta_filters_obj as $obj) {
            $meta = $obj->create_meta_query();
            if ($meta) {
                $meta_filter_query[] = $meta;
            }
        }
        if (!empty($meta_filter_query)) {
            $meta_filter_query['relation'] = 'AND';
            $meta_query = array_merge($meta_query, $meta_filter_query);
        }

        return $meta_query;
    }

    public function add_titles($option) {

        foreach (self::$acf_fields as $key => $data) {
            if (isset($option[$key])) {
                $option[$key] = self::get_meta_filter_name($key);
            }
        }

        return $option;
    }

    //compatibility with other extensions
    public static function get_meta_filter_name($request_key) {
        if (isset(self::$acf_fields [$request_key])) {
            return WOOF_HELPER::wpml_translate(null, self::$acf_fields[$request_key]['title']);
        }
        return false;
    }

    //compatibility with other extensions
    public static function get_meta_filter_option_name($request_key, $request_val) {
        if (isset(self::$acf_fields [$request_key]) && isset(self::$acf_fields[$request_key]['meta_options'][$request_val])) {
            return WOOF_HELPER::wpml_translate(null, self::$acf_fields[$request_key]['meta_options'][$request_val]);
        }

        return false;
    }

    // get html for  messenger
    public static function get_meta_title_messenger($request_val, $request_key) {
        $html = "";
        $title = self::get_meta_filter_name($request_key);
        $option = self::get_meta_filter_option_name($request_key, $request_val);

        $option = explode(':', $option);
        $option_title = $option[0];
        if (isset($option[1])) {
            $option_title = $option[1];
        }

        if (!$title) {
            return $html;
        }
        
        $html = $title;
        if ($option) {
            $html .= ":" . $option_title;
        }
        
        return "<span class='woof_terms'>" . $html . "</span><br />";
    }

}

WOOF_EXT::$includes['applications']['acf_filter'] = new WOOF_ACF_FILTER();
