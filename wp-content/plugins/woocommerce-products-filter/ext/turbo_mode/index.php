<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_TURBO_MODE extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'turbo_mode'; //should be defined!!
    //+++
    public $cron = array();
    public $cron_obj = null;
    protected $crone_hook = "woof_turbo_do_recreate_file";
    protected $crone_filter = "woof_turbo_get_cron_ids";
    protected $enable = 0;
    public $wp_cron_period = 'weekly';
    //+++
    public $tax_conditional = '';
    public $tax_serch_data = array();
    protected $dir_path = "";
    public $dir_link = "";
    protected $file = null;
    protected $storing = 0;
    protected $attr_taxonomies = array();

    //+++

    public function __construct() {
        parent::__construct();
        require_once $this->get_ext_path() . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "file_helper.php";
        require_once $this->get_ext_path() . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "cron_helper.php";

        $path = "";
        $path = $this->get_ext_path() . 'data' . DIRECTORY_SEPARATOR;
        $link = "";
        $link = $this->get_ext_link() . 'data' . DIRECTORY_SEPARATOR;

        if (isset($this->woof_settings['woof_turbo_mode']['storing']) AND !empty($this->woof_settings['woof_turbo_mode']['storing'])) {
            $this->storing = $this->woof_settings['woof_turbo_mode']['storing'];
        }
        if ($this->storing == 0) {
            $path = WOOF_FILE_GENERATOR_HELPER::get_full_path_dir();
            $link = WOOF_FILE_GENERATOR_HELPER::get_full_link_dir();
        }
        $this->dir_path = $path;
        $this->dir_link = $link;
        //***
        if (isset($this->woof_settings['woof_turbo_mode']['wp_cron_period']) AND !empty($this->woof_settings['woof_turbo_mode']['wp_cron_period'])) {
            $this->wp_cron_period = $this->woof_settings['woof_turbo_mode']['wp_cron_period'];
        }
        if (isset($this->woof_settings['woof_turbo_mode']['enable'])) {
            $this->enable = $this->woof_settings['woof_turbo_mode']['enable'];
        }

        if ($this->enable) {
            // reset standart  woof setting  To not show the counter twice
            update_option('woof_show_count', 0);
            update_option('woof_hide_dynamic_empty_pos', 0);
            update_option('woof_show_count_dynamic', 0);

            //activate  ajax  mode
            update_option('woof_try_ajax', 1);
        }

        add_action($this->crone_hook, array($this, 'do_recreate_file'), 10, 3);
        add_filter($this->crone_filter, array($this, 'get_cron_ids'), 10, 2);
        $this->cron_obj = new PN_WP_CRON_WOOF_TURBO_MODE('woof_turbo_init_wpcrone_', $this->crone_hook, $this->crone_filter);
        add_action('init', array($this, 'init_crone'), 100000);

        add_filter('woof_set_shortcode_taxonomyattr_behaviour', array($this, 'check_shortcode_att_taxonomies'));
        $this->init();
    }

    public function get_ext_path() {
        return plugin_dir_path(__FILE__);
    }

    public function get_ext_override_path() {
        return get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" . DIRECTORY_SEPARATOR . $this->folder_name . DIRECTORY_SEPARATOR;
    }

    public function get_ext_link() {
        return plugin_dir_url(__FILE__);
    }

    public function init() {

        add_action('woof_print_applications_tabs_anvanced', array($this, 'woof_print_applications_tabs'), 10, 1);
        add_action('woof_print_applications_tabs_content_advanced', array($this, 'woof_print_applications_tabs_content'), 10, 1);
        if ($this->enable) {
            self::$includes['css']['woof_' . $this->folder_name . '_html_items'] = $this->get_ext_link() . 'css/' . $this->folder_name . '.css';
            add_action('wp_footer', array($this, 'wp_footer'), 12);
            add_filter('woof_print_content_before_search_form', array($this, 'add_overlay_buffer'));
            add_filter('wc_settings_tab_woof_settings', array($this, 'change_options'));
        }

        //ajax
        add_action('wp_ajax_woof_turbo_mode_update_file', array($this, 'create_data_search_files'));
        add_action('wp_ajax_nopriv_woof_turbo_mode_update_file', array($this, 'create_data_search_files'));
    }

    public function create_data_search_files_when_init() {
        add_action('init', array($this, 'create_data_search_files'));
    }

    public function add_overlay_buffer($txt) {
        $txt = "<div class='woof_turbo_mode_overlay'></div>" . $txt;
        return $txt;
    }

    public function wp_footer() {

        wp_enqueue_script('woof_alasql', WOOF_LINK . "ext/quick_search/js/alasql/alasql.min.js", array(), WOOF_VERSION);
        wp_enqueue_script('woof_turbo_mode', $this->get_ext_link() . 'js/turbo_mode.js', array(), WOOF_VERSION);
        $link = '';
        $curr_lang = 'xxx';
        if (class_exists('SitePress')) {
            //$curr_lang = ICL_LANGUAGE_CODE;
            $curr_lang = apply_filters('wpml_current_language', NULL);
        }
        $curr_tax = array();
        $tax = woof()->get_really_current_term();
        if ($tax) {
            $curr_tax = array(
                "tax" => $tax->taxonomy,
                "slug" => $tax->slug
            );
        }
        $id = '0000';
        $id = $this->check_id_file($id);
        $link = $this->dir_link . 'data_' . $id . '_' . $curr_lang . '.json';

        $dynamic_recount = get_option('woof_show_count_dynamic_turbo_mode', 0);
        if ($dynamic_recount == 2 AND wp_is_mobile()) {
            $dynamic_recount = 0;
        } elseif ($dynamic_recount == 2 AND !wp_is_mobile()) {
            $dynamic_recount = 1;
        }

        $text_data = array(
            'link' => $link,
            'pre_load' => 1,
            'sale_ids' => wc_get_product_ids_on_sale(),
            'settings' => $this->woof_settings,
            'current_tax' => $curr_tax,
            'additional_tax' => $this->attr_taxonomies,
            'show_count' => get_option('woof_show_count_turbo_mode', 0),
            'hide_count' => isset($this->woof_settings['hide_terms_count_txt']) ? $this->woof_settings['hide_terms_count_txt'] : 0,
            'hide_empty_term' => get_option('woof_hide_dynamic_empty_pos_turbo_mode', 0),
            'dynamic_recount' => $dynamic_recount,
        );

        wp_localize_script('woof_turbo_mode', 'woof_tm_data', $text_data);
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-turbo-mode">
                <span class="icon-gauge"></span>
                <span><?php esc_html_e("Turbo mode", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {

        $txt_js = "";
        ob_start();
        ?>
        var woof_turbo_creating = "<?php esc_html_e('Creating', 'woocommerce-products-filter') ?>";
        var woof_turbo_products = "<?php esc_html_e('Products and Variants', 'woocommerce-products-filter') ?>";		
        <?php
        $txt_js = ob_get_clean();
        wp_enqueue_script('woof_turbo_mode_admin_', $this->get_ext_link() . 'js/admin.js', array(), WOOF_VERSION);
        wp_enqueue_style('woof_turbo_mode_admin_css', $this->get_ext_link() . 'css/admin.css', array(), WOOF_VERSION);
        wp_add_inline_script('woof_turbo_mode_admin_', $txt_js, 'before');
        //***

        $data = array();

        $data['woof_settings'] = $this->woof_settings;

        woof()->render_html_e($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function create_data_search_files() {

        $tax_query = array();
        $start = 0;
        $step = 10;
        $do = true;
        $id = "0000";
        $id = $this->check_id_file($id);

        if (isset($_POST['turbo_mode_start'])) {
            $start = intval($_POST['turbo_mode_start']);
        }

        if ($start == 0 OR $start == null) {
            if ($this->storing) {
                $this->delete_all_files();
            } else {
                WOOF_FILE_GENERATOR_HELPER::delete_file_all_files();
            }
        }
        $tax_query = $this->_expand_additional_taxes_string($this->tax_conditional, $tax_query);
        $tax_query = woof()->product_visibility_not_in($tax_query, woof()->generate_visibility_keys(true));
        $args = array(
            'post_type' => array('product', 'product_variation'),
            'post_status' => 'publish',
            'tax_query' => $tax_query,
            'offset' => $start,
            'posts_per_page' => $step,
            'fields' => 'ids',
            'cache_results' => false,
            //'no_found_rows'=>false,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'orderby' => 'ID',
            'order' => 'DESC',
        );

        if (class_exists('SitePress')) {
            global $sitepress;
            $default_lang = $sitepress->get_default_language(); // Get WPML default language
            $current_lang = $sitepress->get_current_language(); //save current language
            $sitepress->switch_lang($default_lang);
        }

        $start += $step;
        $limit = apply_filters("woof_turbo_mode_products_limit", 15000);
        if ($start > $limit) { // limiting the number of products
            $do = false;
        }
        $product_ids = new WP_Query($args);
        if ($product_ids->found_posts < 1) {
            $do = false;
        }
        if ($do) {


            $this->push_products_data($product_ids, $id);
        } else {
            $start = -1;
        }
        $result = array('total' => $start);

        exit(json_encode($result));
    }

    public function push_products_data($product_ids, $id) {
        $langs = $this->get_all_lang_wpml();
        $posts = array();
        $posts_data = array();
        $ids = array();
        if (is_object($product_ids)) {
            $ids = $product_ids->posts;
        } elseif (is_array($product_ids)) {
            $ids = $product_ids;
        }

        if ($langs) {
            foreach ($langs as $lang) {
                $posts_data = array();
                foreach ($ids as $post) {
                    $posts_lang = $this->get_post_id_by_lang($post, $lang['language_code']);
                    if ($posts_lang) {
                        $posts_data[] = $this->get_data_by_id($posts_lang, $lang['language_code']);
                    }
                }
                $this->push_data_into_file($posts_data, $id, $lang['language_code']);
            }
        } else {

            foreach ($ids as $post) {
                //$posts[]=$post->ID;
                $posts_data[] = $this->get_data_by_id($post);
            }
            $this->push_data_into_file($posts_data, $id);
        }
    }

    public function get_data_by_id($id, $lang = 'xxx') {

        if (!$id OR $id < 1) {
            return array();
        }
        if (class_exists('SitePress') AND $lang != 'xxx') {
            global $sitepress;
            $sitepress->switch_lang($lang);
        }

        $product = wc_get_product($id);

        if (!$product) {
            return array();
        }
        $author = 0;
        $post_data = get_post($id);
        if ($post_data) {
            $author = $post_data->post_author;
        }
        $data = array();
        $data['id'] = $id;
        $data['title'] = $product->get_name();
        $data['sku'] = $product->get_sku();
        $data['price'] = $this->get_all_prices($product);
        $data['author'] = $author;
        $data['meta_data'] = array();
        $data['taxonomies'] = array();
        $data['parent'] = "";
        $data['stock'] = $product->get_stock_status();
        if ($product->get_type() == "variation") {
            $data['parent'] = $product->get_parent_id();
        } else {
            $data['parent'] = -1;
        }


        $all_taxonomies = woof()->get_taxonomies();
        if ($product->get_type() == "variation") {
            $var_attributes = $product->get_variation_attributes();
            foreach ($var_attributes as $key_attr => $val) {
                $key = str_replace("attribute_", "", $key_attr);

                $data['taxonomies'][$key][] = html_entity_decode($val);
            }
        } else {
            if (true) {   // if you want search in all taxonomies by additional filters( ignore $this->tax_serch_data )
                foreach ($all_taxonomies as $key_slug => $val_tax) {
                    $_terms = get_the_terms($id, $key_slug);
                    if (!is_array($_terms)) {
                        continue;
                    }

                    foreach ($_terms as $term) {
                        $data['taxonomies'][$key_slug][] = html_entity_decode($term->slug);
                    }
                }
            }
        }

        $data['meta_data'] = $this->get_meta_data_by_id($id);

        return $data;
    }

    public function get_meta_data_by_id($id) {
        $meta_data = array();
        if (class_exists('WOOF_META_FILTER')) {
            $meta_fields = [];

            if (isset($this->woof_settings['meta_filter'])) {
                $meta_fields = $this->woof_settings['meta_filter'];
            }

            $meta_fields['_stock_status'] = array('meta_key' => '_stock_status');
            $meta_fields['_wc_average_rating'] = array('meta_key' => '_wc_average_rating');

            if (!empty($meta_fields)) {
                foreach ($meta_fields as $key => $meta) {
                    if ($meta['meta_key'] == "__META_KEY__") {
                        continue;
                    }

                    $meta = get_post_meta($id, $meta['meta_key'], true);

                    if ($meta) {
                        $meta_data[$key] = $meta;
                    }
                }
            }
        }
        return $meta_data;
    }

    public function get_all_prices($product) {
        if (!$product) {
            return array();
        }
        $include_tax = false;
        if (wc_tax_enabled() && 'incl' === get_option('woocommerce_tax_display_shop') && !wc_prices_include_tax()) {
            $include_tax = true;
        }
        if (class_exists('WOOCS')) {
            global $WOOCS;
            $def_currency = $WOOCS->default_currency;
            if ($def_currency != $WOOCS->current_currency) {
                $WOOCS->current_currency = $def_currency;
                $WOOCS->storage->set_val('woocs_current_currency', $def_currency);
            }
        }
        //$product = wc_get_product($id);
        $prices = array();
        if ($product->is_type('variable')) {
            $available_variations = $product->get_children();
            foreach ($available_variations as $var_id) {
                $var_product = wc_get_product($var_id);
                $regular_v = $var_product->get_regular_price();
                $sale_v = $var_product->get_sale_price();
                if ($include_tax) {
                    $regular_v = wc_get_price_including_tax($var_product, array('price' => $regular_v));
                    $sale_v = wc_get_price_including_tax($var_product, array('price' => $sale_v));
                }
                $prices[] = array(
                    'regular' => $regular_v,
                    'sale' => $sale_v,
                );
            }
        } else {
            $regular = $product->get_regular_price();
            $sale = $product->get_sale_price();
            if ($include_tax) {
                $regular = wc_get_price_including_tax($product, array('price' => $regular));
                $sale = wc_get_price_including_tax($product, array('price' => $sale));
            }
            $prices[] = array(
                'regular' => $regular,
                'sale' => $sale,
            );
        }
        return $prices;
    }

    // wpml compatibility
    public function get_all_lang_wpml() {
        if (class_exists('SitePress')) {
            return $langs = icl_get_languages('skip_missing=0&orderby=KEY&order=DIR&link_empty_to=str');
        }
        return false;
    }

    public function check_shortcode_att_taxonomies($taxonomies) {
        $this->attr_taxonomies = array();
        if (!empty($taxonomies)) {
            $t = explode('+', $taxonomies);
            if (!empty($t) AND is_array($t)) {
                foreach ($t as $string) {
                    $tmp = explode(':', $string);
                    $tax_slug = $tmp[0];
                    $tax_terms = explode(',', $tmp[1]);
                    $slugs = array();

                    foreach ($tax_terms as $term_id) {
                        $term = get_term(intval($term_id), $tax_slug);

                        if (is_object($term)) {
                            $slugs[] = $term->slug;
                        }
                    }

                    //***

                    if (!empty($slugs)) {
                        $this->attr_taxonomies[] = array(
                            'tax' => $tax_slug,
                            'terms' => implode(',', $slugs)
                        );
                    }
                }
            }
        }
        return $taxonomies;
    }

    public function get_post_id_by_lang($post_id, $lang_code, $type = 'product') {
        if (class_exists('SitePress')) {
            return icl_object_id($post_id, $type, false, $lang_code);
        }
        return false;
    }

    // +++

    public function woof_quick_wpcron_init($reset = false) {
        $hook = 'woof_quick_search_wpcron';

        if ($reset) {
            $this->cron->remove($hook);
            return;
        }

        if ($this->cron_system === 0 AND $this->wp_cron_period != -1) {//wp cron
            if (!$this->cron->is_attached($hook, $this->get_woof_cron_schedules($this->wp_cron_period))) {
                $this->cron->attach($hook, time(), $this->get_woof_cron_schedules($this->wp_cron_period));
            }

            $this->cron->process();
        }
    }

    //  File works
    private function delete_all_files() {
        $path = $this->dir_path . DIRECTORY_SEPARATOR;
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle)))
                if ($file != "." && $file != "..")
                    unlink($path . $file);
            closedir($handle);
        }
    }

    private function push_data_into_file($data, $id = '0000', $lang = 'xxx') {

        $file = $this->dir_path . '/data_' . $id . '_' . $lang . '.json';
        try {
            clearstatcache(true, $file);
        } catch (Exception $e) {
            
        }
        //***
        if ($fh = fopen($file, 'a+')) {

            if ($fh) {
                $contents = '';
                $file_size = filesize($file);

                if ($file_size > 0) {
                    $contents = fread($fh, $file_size);
                }
                //***
                if (!empty($contents)) {
                    $contents = json_decode(trim($contents), true);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        $data = array_merge($contents, $data);
                    }
                }
                //***
                ftruncate($fh, 0);
                fwrite($fh, json_encode($data));
                fclose($fh);

                return true;
            }
        }

        return false;
    }

    private function _expand_additional_taxes_string($additional_taxes, $res = array()) {
        if (!empty($additional_taxes)) {
            $t = explode('+', $additional_taxes);
            if (!empty($t) AND is_array($t)) {
                foreach ($t as $string) {
                    $tmp = explode(':', $string);
                    $tax_slug = $tmp[0];
                    $tax_terms = explode(',', $tmp[1]);
                    $slugs = array();
                    foreach ($tax_terms as $term_id) {
                        $term = get_term(intval($term_id), $tax_slug);
                        if (is_object($term)) {
                            $slugs[] = $term->slug;
                        }
                    }

                    //***
                    if (!empty($slugs)) {
                        $res[] = array(
                            'taxonomy' => $tax_slug,
                            'field' => 'slug', //id
                            'terms' => $slugs
                        );
                    }
                }
            }
        }

        return $res;
    }

    public function optimize_url($url) {
        $site_url = get_site_url();
        return str_replace($site_url, "", $url);
    }

    public function change_options($options) {

        foreach ($options as &$item) {
            if (isset($item['id']) AND ( $item['id'] == "woof_show_count_dynamic" OR $item['id'] == "woof_show_count" OR $item['id'] == "woof_hide_dynamic_empty_pos")) {
                if ($item['id'] == "woof_show_count_dynamic") {
                    $item["options"] = array(
                        0 => esc_html__('No', 'woocommerce-products-filter'),
                        1 => esc_html__('Yes, for all devices', 'woocommerce-products-filter'),
                        2 => esc_html__('Yes, only for PC', 'woocommerce-products-filter')
                    );
                }
                $item['id'] .= "_turbo_mode";
            }
        }
        return $options;
    }

    // CRON

    public function get_woof_cron_schedules($key = '') {
        $schedules = array(
            'daily' => DAY_IN_SECONDS,
            'weekly' => WEEK_IN_SECONDS,
            'twicemonthly' => WEEK_IN_SECONDS * 2,
            'month' => WEEK_IN_SECONDS * 4,
            'min1' => MINUTE_IN_SECONDS,
            'no' => -1
        );

        if (empty($key)) {
            $key = 'weekly';
        }

        if (isset($schedules[$key])) {
            return $schedules[$key];
        }

        return -1;
    }

    public function init_crone() {

        $id = "0000";
        $id = $this->check_id_file($id);
        $cron = $this->get_woof_cron_schedules($this->wp_cron_period);

        if ($cron != -1 AND $cron) {
            if (!$this->cron_obj->is_attached($id, $cron)) {
                $this->cron_obj->attach($id, time(), $cron);
            }
        } else {
            $this->cron_obj->remove($id);
        }
        $this->cron_obj->process();
    }

    public function get_cron_ids($ids, $id) {

        $tax_query = array();
        $tax_query = $this->_expand_additional_taxes_string($this->tax_conditional, $tax_query);
        $tax_query = woof()->product_visibility_not_in($tax_query, woof()->generate_visibility_keys(true));
        $args = array(
            'post_type' => array('product', 'product_variation'),
            'post_status' => 'publish',
            'tax_query' => $tax_query,
            'posts_per_page' => -1,
            //'posts_per_page' => 12,
            'fields' => 'ids',
            'cache_results' => false,
            //'no_found_rows'=>false,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'orderby' => 'ID',
            'order' => 'DESC',
        );

        if (class_exists('SitePress')) {
            global $sitepress;
            $default_lang = $sitepress->get_default_language(); // Get WPML default language
            $current_lang = $sitepress->get_current_language(); //save current language
            $sitepress->switch_lang($default_lang);
        }
        $product_ids = new WP_Query($args);
        if ($product_ids->found_posts > 0) {
            return array_unique($product_ids->posts);
        } else {
            return array();
        }
    }

    public function do_recreate_file($id, $ids, $is_end) {

        if (count($ids) < 1 OR $is_end) {
            $dir_path = $this->dir_path;

            $langs = $this->get_all_lang_wpml();
            if ($langs) {
                foreach ($langs as $lang) {
                    if (file_exists($dir_path . 'data_' . $id . "_temp_" . $lang['language_code'] . ".json")) {
                        rename($dir_path . 'data_' . $id . "_temp_" . $lang['language_code'] . ".json", $dir_path . 'data_' . $id . "_" . $lang['language_code'] . ".json");
                    }
                }
            } else {

                if (file_exists($dir_path . 'data_' . $id . "_temp_xxx.json")) {
                    rename($dir_path . 'data_' . $id . "_temp_xxx.json", $dir_path . 'data_' . $id . "_xxx.json");
                }
            }
        } else {
            $this->push_products_data($ids, $id . "_temp");
        }
    }

    public function check_id_file($id = "0000") {
        if (is_multisite()) {
            $id .= get_current_blog_id();
        }
        return $id;
    }

}

WOOF_EXT::$includes['applications']['turbo_mode'] = new WOOF_EXT_TURBO_MODE();
