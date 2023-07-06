<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_BY_SKU extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'by_sku'; //your custom key here
    public $index = 'woof_sku'; //index in the search query
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
        add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
        add_action('woof_print_html_type_options_' . $this->html_type, array($this, 'woof_print_html_type_options'), 10, 1);
        add_action('woof_print_html_type_' . $this->html_type, array($this, 'print_html_type'), 10, 1);
        add_action('wp_enqueue_scripts', array($this, 'wp_head'), 2);

        add_action('wp_ajax_woof_sku_autocomplete', array($this, 'woof_sku_autocomplete'));
        add_action('wp_ajax_nopriv_woof_sku_autocomplete', array($this, 'woof_sku_autocomplete'));

        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_sku';
        self::$includes['js_lang_custom'][$this->index] = esc_html__('by SKU', 'woocommerce-products-filter');

        //***
        add_shortcode('woof_sku_filter', array($this, 'woof_sku_filter'));
    }

    public function wp_head() {
        
        self::$includes['js_code_custom']['woof_' . $this->html_type . '_html_items'] = $this->get_js();
        self::$includes['css_code_custom'][$this->index] = $this->get_style();
        //***
        if (isset(woof()->settings['by_sku']['autocomplete']) AND woof()->settings['by_sku']['autocomplete']) {
            wp_enqueue_script('easy-autocomplete', WOOF_LINK . 'js/easy-autocomplete/jquery.easy-autocomplete.min.js', array('jquery'), WOOF_VERSION);
            wp_enqueue_style('easy-autocomplete', WOOF_LINK . 'js/easy-autocomplete/easy-autocomplete.min.css', array(), WOOF_VERSION);
            wp_enqueue_style('easy-autocomplete-theme', WOOF_LINK . 'js/easy-autocomplete/easy-autocomplete.themes.min.css', array(), WOOF_VERSION);
        }
    }

    public function get_style() {
        
        ob_start();
        if (isset(woof()->settings['by_sku']['image'])) {
            if (!empty(woof()->settings['by_sku']['image'])) {
                ?>
                .woof_sku_search_container .woof_sku_search_go{
                background: url(<?php echo esc_url(woof()->settings['by_sku']['image']) ?>) !important;
                }
                <?php
            }
        }
        return ob_get_clean();
    }

    public function get_js() {
        
        ob_start();
        //dynamic js
        ?>
        var woof_sku_autocomplete = 0;
        var woof_sku_autocomplete_items = 10;
        var woof_sku_reset_behavior = 1;
        <?php if (isset(woof()->settings['by_sku']['reset_behavior'])): ?>
            woof_sku_reset_behavior =<?php echo intval(woof()->settings['by_sku']['reset_behavior']) ?>;
        <?php endif; ?>			

        <?php if (isset(woof()->settings['by_sku']['autocomplete'])): ?>
            woof_sku_autocomplete =<?php echo intval(woof()->settings['by_sku']['autocomplete']) ?>;
            woof_sku_autocomplete_items =<?php echo intval(woof()->settings['by_sku']['autocomplete_items'] ? woof()->settings['by_sku']['autocomplete_items'] : 10) ?>;
            <?php
        endif;
        return ob_get_clean();
    }

    //shortcode
    public function woof_sku_filter($args = array()) {
        
        if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_sku_filter.php')) {
            return woof()->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_sku_filter.php', $args);
        }
        return woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_sku_filter.php', $args);
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
        
        $request = woof()->get_request_data();
        if (isset($request['woof_sku'])) {
            if (!empty($request['woof_sku'])) {
                $woof_sku_request = explode(',', $request['woof_sku']);
                $woof_sku_request = array_map('urldecode', $woof_sku_request);
                $woof_sku_request = array_map('trim', $woof_sku_request);
                //***
                $use_for = isset(woof()->settings['by_sku']['use_for']) ? woof()->settings['by_sku']['use_for'] : 'simple';
                if ($use_for == 'simple') {
                    if (!empty($woof_sku_request)) {
                        $res = array();
                        $res['relation'] = 'OR';
                        foreach ($woof_sku_request as $sku) {
                            $res[] = array(
                                'key' => '_sku',
                                'value' => $sku,
                                'compare' => woof()->settings['by_sku']['logic']
                            );
                        }
                        $meta_query[] = $res;
                    }
                } else {
                    add_filter('posts_where', array($this, 'posts_where'), 9999);
                }
            }
        }

        return $meta_query;
    }

    public function posts_where($where = '') {
        
        static $where_sku = "";

        //cache on the fly
        if (!empty($where_sku)) {
            return $where . $where_sku;
        }

        $request = woof()->get_request_data();
        if (isset($request['woof_sku'])) {
            if (!empty($request['woof_sku'])) {
                global $wpdb;

                $woof_sku_request = explode(',', $request['woof_sku']);
                $woof_sku_request = array_map('urldecode', $woof_sku_request);
                $woof_sku_request = array_map('trim', $woof_sku_request);
                //***
                $condtion_string = "";
                if (!empty($woof_sku_request)) {
                    foreach ($woof_sku_request as $k => $sku) {
                        if ($k > 0) {
                            $condtion_string .= " OR ";
                        }
						$logic = woof()->settings['by_sku']['logic'];
                        if ($logic  == '=') {
                            $condtion_string .= "postmeta.meta_value {$logic} '$sku'";
                        } else {
                            $condtion_string .= "postmeta.meta_value {$logic} '%$sku%'";
                        }
                    }
                }

                //***

                $product_variations = $wpdb->get_results("
                    SELECT posts.ID
                    FROM $wpdb->posts AS posts
                    LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id )
                    WHERE posts.post_type IN ('product_variation','product')
                    AND postmeta.meta_key = '_sku'
                    AND ($condtion_string)", ARRAY_N);
                //+++
                $product_variations_ids = array();
                if (!empty($product_variations)) {
                    foreach ($product_variations as $v) {
                        $product_variations_ids[] = $v[0];
                    }

                    //+++
                    $product_variations_ids_string = implode(',', $product_variations_ids);

                    $products = $wpdb->get_results("
                        SELECT posts.post_parent
                        FROM $wpdb->posts AS posts
                        WHERE posts.ID IN ($product_variations_ids_string) AND posts.post_parent > 0", ARRAY_N);
                    //+++
                    $product_ids = array();
                    if (!empty($products)) {
                        foreach ($products as $v) {
                            $product_ids[] = intval($v[0]);
                        }
                    }
					$result_ids = array_merge($product_ids, $product_variations_ids);
					if(empty($result_ids)){
						$result_ids = array(-1);
					}

                    $product_ids = implode(',', $result_ids);
                    $where .= " AND $wpdb->posts.ID IN($product_ids)";
                    $where_sku = " AND $wpdb->posts.ID IN($product_ids)";
				}else{
					$where .= " AND $wpdb->posts.ID IN(-1)";
				}
            }
        }

        return $where;
    }

    //ajax
    public function woof_sku_autocomplete() {
        
        $results = array();
        $args = array(
            'nopaging' => true,
            'post_type' => 'product',
            'post_status' => array('publish'),
            'orderby' => 'title',
            'order' => 'ASC',
            'max_num_pages' => (int) woof()->settings['by_sku']['autocomplete_items']
        );

        if (class_exists('SitePress')) {
            // $args['lang'] = ICL_LANGUAGE_CODE;
            $args['lang'] = apply_filters('wpml_current_language', NULL);
        }


        //***

        $_GET['woof_sku'] = WOOF_REQUEST::get('phrase');
        add_filter('posts_where', array($this, 'posts_where'), 10);
        $query = new WP_Query($args);
        //+++
        //http://easyautocomplete.com/guide
        if ($query->have_posts()) {

            foreach ($query->posts as $p) {
                $product = new WC_Product($p->ID);
                $data = array(
                    "name" => $product->get_sku(),
                    "type" => $p->post_title,
                    "link" => get_post_permalink($p->ID)
                );

                $results[] = $data;
            }
        } else {
            $results[] = array(
                "name" => esc_html__("Products not found!", 'woocommerce-products-filter'),
                "type" => "",
                "link" => "#",
                "icon" => WOOF_LINK . 'img/not-found.jpg'
            );
        }

        die(json_encode($results));
    }

}

WOOF_EXT::$includes['html_type_objects']['by_sku'] = new WOOF_EXT_BY_SKU();
