<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_BY_AUTHOR extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'by_author'; //your custom key here
    public $index = 'woof_author';
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
        if ((int) get_option('woof_first_init', 0) != 1) {
            update_option('woof_show_author_search', 0);
        }

        add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
        add_action('woof_print_html_type_options_' . $this->html_type, array($this, 'woof_print_html_type_options'), 10, 1);
        add_action('woof_print_html_type_' . $this->html_type, array($this, 'print_html_type'), 10, 1);


        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_author'; //we have no init function in this case
        self::$includes['js_lang_custom'][$this->index] = esc_html__('By author', 'woocommerce-products-filter');

        //***
        add_shortcode('woof_author_filter', array($this, 'woof_author_filter'));
    }

    //shortcode
    public function woof_author_filter($args = array()) {
        
        if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_author_filter.php')) {
            return woof()->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_author_filter.php', $args);
        }
        return woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_author_filter.php', $args);
    }

    //settings page hook
    public function woof_print_html_type_options() {        
        woof()->render_html_e($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'options.php', array(
            'key' => $this->html_type,
            "woof_settings" => get_option('woof_settings', array())
                )
        );
    }

    public function assemble_query_params(&$meta_query, $wp_query = NULL) {
        add_filter('posts_where', array($this, 'woof_post_author_filter'), 9999, 2); //for searching by author
        return $meta_query;
    }

    public function woof_post_author_filter($where = '', $query = null) {

        global $wp_query;
        
        $request = woof()->get_request_data();

        if (defined('DOING_AJAX')) {
            $conditions = (isset($wp_query->query_vars['post_type']) AND $wp_query->query_vars['post_type'] == 'product') OR WOOF_REQUEST::isset('woof_products_doing');
        } else {
            $conditions = WOOF_REQUEST::isset('woof_products_doing');
        }
        //***
		if (isset($query->query_vars['wc_query']) && $query->query_vars['wc_query'] == "product_query"){
			$conditions = true;		
		}		
		
        if ($conditions) {
            if (woof()->is_isset_in_request_data('woof_author')) {
                $request['woof_author'] = explode(",", $request['woof_author']);
                $where .= "AND ( ";
                for ($i = 0; $i < count($request['woof_author']); $i++) {
                    $logic = " OR ";
                    if ((count($request['woof_author']) - 1) <= $i) {
                        $logic = "";
                    }
                    $where .= "post_author={$request['woof_author'][$i]} {$logic}";
                }
                $where .= " )";
            }
        }
        //***
        return $where;
    }

}

WOOF_EXT::$includes['html_type_objects']['by_author'] = new WOOF_EXT_BY_AUTHOR();
