<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_QUERY_SAVE extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'query_save'; //your custom key here
    public $index = '';
    public $html_type_dynamic_recount_behavior = 'none';
    protected $user_meta_key = 'woof_user_search_query';
    public $search_count = 2;
    public $show_notise_product = 0;
    public $show_notise = 0;

    public function __construct() {
        parent::__construct();
        //***
        if (isset($this->woof_settings["query_save"]['search_count']) AND!empty($this->woof_settings["query_save"]['search_count'])) {
            $this->search_count = (int) $this->woof_settings["query_save"]['search_count'];
        }

        if (isset($this->woof_settings["query_save"]['show_notice_product']) AND!empty($this->woof_settings["query_save"]['show_notice_product'])) {
            $this->show_notise_product = (int) $this->woof_settings["query_save"]['show_notice_product'];
        }
        if (isset($this->woof_settings["query_save"]['show_notice']) AND!empty($this->woof_settings["query_save"]['show_notice'])) {
            $this->show_notise = (int) $this->woof_settings["query_save"]['show_notice'];
        }

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
        add_action('wp_enqueue_scripts', array($this, 'wp_head'), 9);
        // Ajax  action
        add_action('wp_ajax_woof_save_query_add_query', array($this, 'woof_add_query'));
        add_action('wp_ajax_nopriv_woof_save_query_add_query', array($this, 'woof_add_query'));
        add_action('wp_ajax_woof_save_query_remove_query', array($this, 'woof_remove_query'));
        add_action('wp_ajax_nopriv_woof_save_query_remove_query', array($this, 'woof_remove_query'));

        add_action('wp_ajax_nopriv_woof_save_query_check_query', array($this, 'check_query'));
        add_action('wp_ajax_woof_save_query_check_query', array($this, 'check_query'));

        //+++
        // add shortcode
        add_shortcode('woof_save_query', array($this, 'woof_save_query'));

        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_save_query';

        add_action('woocommerce_single_product_summary', array($this, 'show_notice_on_product'));
    }

    //settings page hook
    public function woof_print_html_type_options() {

        woof()->render_html_e($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'options.php', array(
            'key' => $this->html_type,
            "woof_settings" => get_option('woof_settings', array())
                )
        );
    }

    public function woof_add_query() {
        global $WOOF, $wpdb, $wp_query;

        if (!isset($_POST['link']) OR!isset($_POST['user_id'])) {
            die();
        }

        //***

        $data = array();
        $sanit_user_id = sanitize_key($_POST['user_id']);
        if ($sanit_user_id < 1) {
            die(); //if user id - wrong!!!
        }


        $key = uniqid('woofms_'); // Create key for this subscriber
        $data['key'] = $key;

        $data['user_id'] = $sanit_user_id;
        $data['link'] = esc_url_raw($_POST['link']);
        if (!isset($_POST['get_var'])) {
            $_POST['get_var'] = [];
        }
        $data['get'] = $this->woof_get_html_terms($this->sanitaz_array_r($_POST['get_var']));
        $saved_q = get_user_meta($data['user_id'], $this->user_meta_key, true);
        if (!is_array($saved_q)) {
            $saved_q = array();
        }
        $data['request'] = $this->sanitazed_sql_query(base64_decode(woof()->storage->get_val("woof_pm_request_" . $data['user_id'])));
        // If the request has banned operators or is empty
        if (!$data['request'] OR empty($data['request'])) {
            die();
        }
        //+++
        //Remove limit frim request
        $pos = stripos($data['request'], "LIMIT");
        if ($pos) {
            $data['request'] = substr($data['request'], 0, $pos);
        }
        if (!is_array($saved_q)) {
            $saved_q = array();
        }
        if (count($saved_q) >= $this->search_count) {
            die('<li class="woof_sq_max_count" >' . esc_html__('Сount is max', 'woocommerce-products-filter') . '</li>'); // Check limit count on backend
        }
        //+++
        $data['date'] = time();

        $data['title'] = esc_html__('My query', 'woocommerce-products-filter');
        if (isset($_POST['query_title']) AND $_POST['query_title']) {
            $data['title'] = sanitize_text_field($_POST['query_title']);
        }

        $saved_q[$key] = $data;
        update_user_meta($data['user_id'], $this->user_meta_key, $saved_q);
        //for Ajax redraw
        $cont = woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'item_list_query.php', $data);

        die($cont);
    }

    public function woof_remove_query() {
        if (!isset($_POST['key']) OR!isset($_POST['user_id'])) {
            die('No data!');
        }

        $user_id = sanitize_key($_POST['user_id']);
        $key = sanitize_key($_POST['key']);
        $subscr = get_user_meta($user_id, $this->user_meta_key, true);
        unset($subscr[$key]);
        update_user_meta($user_id, $this->user_meta_key, $subscr);
        $arg = array('key' => $key);
        die(json_encode($arg));
    }

    //it create  html for tooltip and list of the terms in email
    public function woof_get_html_terms($args) {
        $html = "";

        $not_show = array('swoof', 'paged', 'orderby', 'min_price', 'max_price', 'woof_author', 'page');
        if (isset($args['min_price'])) {
            $price_text = sprintf(__('Price - from %s to %s', 'woocommerce-products-filter'), $args['min_price'], $args['max_price']);
            $price_text .= '<br />';
            $html .= '<span class="woof_subscr_price">' . $price_text . '</span>';
        }
        if (isset($args['woof_author'])) {
            $ids = explode(',', $args['woof_author']);
            $auths = "";
            foreach ($ids as $auth) {
                $auths .= " " . get_userdata((int) $auth)->display_name;
            }
            $html .= "<span class='woof_author_name'>" . $auths . "</span><br />";
        }

        foreach ($args as $key => $val) {

            if (in_array($key, $not_show)) {
                continue;
            }

            if (class_exists('WOOF_META_FILTER')) {
                $meta_title = WOOF_META_FILTER::get_meta_title_messenger($val, $key);
                if (!empty($meta_title) AND $meta_title) {
                    $html .= $meta_title;

                    continue;
                }
            }
            if (class_exists('WOOF_ACF_FILTER')) {
                $acf_title = WOOF_ACF_FILTER::get_meta_title_messenger($val, $key);
                if (!empty($acf_title) AND $acf_title) {
                    $html .= $acf_title;

                    continue;
                }
            }			
            $tax = get_taxonomy($key);
            if (is_object($tax)) {
                $name = $tax->labels->name;
                if (!empty($name)) {
                    $name .= ": ";
                }

                $arr_val = explode(',', $val);
                $result = array();

                foreach ($arr_val as $slug) {
                    $term = get_term_by('slug', $slug, $key);
                    if (is_object($term)) {
                        $result[] = $term->name;
                    } else {
                        $result[] = $val;
                    }
                }

                $name .= implode(',', $result);

                $html .= "<span class='woof_terms'>" . $name . "</span><br />";
            }
        }
        if (empty($html)) {
            $html = esc_html__('None', 'woocommerce-products-filter');
        }

        return $html;
    }

    // Recursive sanitaze arrais
    public function sanitaz_array_r($arr) {
        $newArr = array();
        foreach ($arr as $key => $value) {
            $newArr[WOOF_HELPER::escape($key)] = ( is_array($value) ) ? $this->sanitaz_array_r($value) : WOOF_HELPER::escape($value);
        }
        return $newArr;
    }

    public function wp_head() {
        $txt_js = "";
        ob_start();
        ?>
        var woof_confirm_lang = "<?php esc_html_e('Are you sure?', 'woocommerce-products-filter') ?>";
        <?php
        $txt_js = ob_get_clean();
        self::$includes['js_code_custom'][$this->html_type] = $txt_js;
    }

    public function woof_save_query($args) {
        $data = shortcode_atts(array(
            'in_filter' => 0
                ), $args);

        if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_save_query.php')) {
            return woof()->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_save_query.php', $data);
        }
        return woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_save_query.php', $data);
    }

    public function sanitazed_sql_query($sql) {
        $conditional_operator = array('TRUNCATE', 'DELETE', 'UPDATE', 'INSERT', 'REPLACE', 'CREATE');
        foreach ($conditional_operator as $operator) {
            $result = stripos($sql, $operator);
            if ($result !== false) {
                return false;
                break;
            }
        }
        return $sql;
    }

    public function show_notice_on_product() {

        if ($this->show_notise_product AND is_user_logged_in()) {
            global $product;
            $id = $product->get_id();
            if ($id) {
                ?>
                <div class="woof_query_save_notice_product woof_query_save_notice_product_<?php echo esc_attr($id) ?>" data-id="<?php echo esc_attr($id) ?>" ></div>
                <?php
            }
        }
    }

    public function check_query() {

        if (!isset($_POST['product_ids'])) {
            die();
        }
        $type = "woof";
        if (isset($_POST['type'])) {
            $type = sanitize_textarea_field($_POST['type']);
        }
        $user_id = get_current_user_id();
        if (!$user_id) {
            die();
        }
        $data = get_user_meta($user_id, $this->user_meta_key, true);
        $result = array();
        if (!is_array($data)) {
            $data = array();
        }
        $show_notice = ($type == "woof") ? $this->show_notise : $this->show_notise_product;

        if ($show_notice == 0) {
            die();
        }
        foreach ($_POST['product_ids'] as $id) {
            $result[$id] = array();

            foreach ($data as $key => $item) {
                if (!isset($item['link'])) {
                    continue;
                }
                $link = parse_url(html_entity_decode($item['link']), PHP_URL_QUERY);
                $query_array = WOOF_HELPER::safe_parse_str($link);
                $_GET = array_merge($query_array, wc_clean($_GET));

                woof()->woof_products_ids_prediction(array('post__in' => $id));
                if (is_array(WOOF_REQUEST::get('woof_wp_query_ids')) AND in_array($id, WOOF_REQUEST::get('woof_wp_query_ids'))) {
                    $data['match'] = true;
                    $data['notice'] = str_replace("%title%", $item['title'], $this->woof_settings["query_save"]["show_notice_text"]);
                } else {
                    $data['match'] = false;
                    $data['notice'] = str_replace("%title%", $item['title'], $this->woof_settings["query_save"]["show_notice_text_not"]);
                }
                if ($show_notice == 1 AND $data['match'] == false) {
                    continue;
                }

                if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'notice.php')) {
                    $text = woof()->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'notice.php', $data);
                } else {
                    $text = woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'notice.php', $data);
                }
                $result[$id][$key] = $text;
            }
        }
        die(json_encode($result));
    }

}

WOOF_EXT::$includes['html_type_objects']['query_save'] = new WOOF_EXT_QUERY_SAVE();
