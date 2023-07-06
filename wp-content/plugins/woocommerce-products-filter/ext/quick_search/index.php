<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_QUICK_TEXT extends WOOF_EXT {

    public $type = 'application';
    public $folder_name = 'quick_search'; //should be defined!!
    //***
    public $cron = NULL;
    public $cron_system = 0;
    public $wp_cron_period = 'daily';
    public $tax_conditional = '';
    public $tax_serch_data = array();

    //***

    public function __construct() {
        parent::__construct();

        //***
        if (isset($this->woof_settings['woof_quick_search']['wp_cron_period']) AND!empty($this->woof_settings['woof_quick_search']['wp_cron_period'])) {
            $this->wp_cron_period = $this->woof_settings['woof_quick_search']['wp_cron_period'];
        }
        if (isset($this->woof_settings['woof_quick_search']['quick_search_tax_conditionals']) AND!empty($this->woof_settings['woof_quick_search']['quick_search_tax_conditionals'])) {
            $this->tax_conditional = $this->woof_settings['woof_quick_search']['quick_search_tax_conditionals'];
        }
        if (isset($this->woof_settings['woof_quick_search']['items_for_text_search']) AND!empty($this->woof_settings['woof_quick_search']['items_for_text_search'])) {
            $this->tax_serch_data = $this->woof_settings['woof_quick_search']['items_for_text_search'];
        }
        $this->cron_system = 0;
        //***
        if (false) {
            $this->cron = new PN_WP_CRON_WOOF('woof_quick_search_cron');
            //***
            if ($this->cron_system === 1) {
                $this->woof_stat_wpcron_init(true);
                if (isset($_GET['woof_quick_search_collection'])) {
                    $cron_secret_key = 'woof_quick_search_updating';
                    if (isset($this->woof_settings['woof_quick_search']['cron_secret_key']) AND!empty($this->woof_settings['woof_quick_search']['cron_secret_key'])) {
                        $cron_secret_key = sanitize_title($this->woof_settings['woof_quick_search']['cron_secret_key']);
                    }
                    if ($_GET['woof_quick_search_collection'] === $cron_secret_key) {
                        $this->create_data_search_files();
                        die('woof quick text assemble done!');
                    }
                }
            } else {
                add_action('woof_quick_search_wpcron', array($this, 'create_data_search_files_when_init'), 10);
                $this->woof_quick_wpcron_init();
            }
        }

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
        add_shortcode('woof_quick_search', array($this, 'quick_search'));
        add_shortcode('woof_quick_search_results', array($this, 'quick_search_result'));
        add_action('woof_print_applications_tabs_' . $this->folder_name, array($this, 'woof_print_applications_tabs'), 10, 1);
        add_action('woof_print_applications_tabs_content_' . $this->folder_name, array($this, 'woof_print_applications_tabs_content'), 10, 1);

        self::$includes['css']['woof_' . $this->folder_name . '_html_items'] = $this->get_ext_link() . 'css/' . $this->folder_name . '.css';
        add_action('wp_footer', array($this, 'wp_footer'), 12);
        //ajax
        add_action('wp_ajax_woof_qt_update_file', array($this, 'create_data_search_files'));
        add_action('wp_ajax_nopriv_woof_qt_update_file', array($this, 'create_data_search_files'));
    }

    public function quick_search($atts) {
        
        $data = (shortcode_atts(array(
                    'short_id' => '0000', /* this is needed when using several data files now it is not used */
                    'preload' => 1, /* download a file  with the page = 1    file download by click =0 */
                    'placeholder' => esc_html__('Text search', 'woocommerce-products-filter'),
                    'price_filter' => 0, /* show price filter  1 or 0 ( only for extended filter ) */
                    'extended_filter' => 0, /* use  extend filter(alaSQL) with special template  1 or 0 */
                    'target' => '_blank', /* link behavior */
                    'reset_btn' => 1, /* show reset btn   1 or 0 ( only for extended filter ) */
                    'add_filters' => '', /* [drop-down|multi-drop-down|checkbox|radio] example  drop-down:product_cat,multi-drop-down:pa_size ( only for extended filter ) */
                    'filter_title' => '', /* Example "product_cat:Type,pa_size:Color of the item" ( only for extended filter ) */
                    'term_logic' => '', /* example  pa_color:AND,product_cat:OR ( only for extended filter ) */
                    'tax_logic' => 'AND', /*  logic  beetwin taxonomies  AND or OR ( only for extended filter ) */
                    'text_group_logic' => 'AND', /* logic for a few words, all words must be found=AND   [AND | OR} ( only for extended filter ) */
                    'exclude_terms' => '', /* ids any taxonomies exclude_terms="56,5288,8,1299"  ( only for extended filter ) */
                    'reset_text' => esc_html__('Reset', 'woocommerce-products-filter'),
                    'class' => 'woof_qs_3_col',
                        ), $atts));

        $curr_lang = 'xxx';
        if (class_exists('SitePress')) {
            //$curr_lang = ICL_LANGUAGE_CODE;
            $curr_lang = apply_filters('wpml_current_language', NULL);
        }
		$data['short_id'] = $this->check_id_file($data['short_id']);
        WOOF_REQUEST::set('woof_qt', true);
        WOOF_REQUEST::set('woof_quick_search_link', $this->get_ext_link() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'data_' . $data['short_id'] . '_' . $curr_lang . '.json');

        if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_quick_search.php')) {
            return woof()->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_quick_search.php', $data);
        }
        return woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_quick_search.php', $data);
    }

    public function create_data_search_files_when_init() {
        add_action('init', array($this, 'create_data_search_files'));
    }

    public function quick_search_result($atts) {
        
        $data = (shortcode_atts(array(
                    'per_page' => 12,
                    'template_result' => 'list_1',
                    'always_show_products' => 1,
                    'template_structure' => 'img,title,price,sku,key_words',
                    'orderby' => 'title-asc',
                    'header_text' => "",
                        ), $atts));
        $data = apply_filters('woof_qs_shortcode_data', $data); // possibility to change a template for example for a mobile
        WOOF_REQUEST::set('woof_qt_extended', $data['template_result']);
        $all_data = array();
        $all_data['data'] = $data;
        if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_quick_search_results.php')) {
            return woof()->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_quick_search_results.php', $all_data);
        }
        return woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_quick_search_results.php', $all_data);
    }

    public function wp_footer() {
        if (!WOOF_REQUEST::isset('woof_qt')) {
            return;
        }
        if (WOOF_REQUEST::isset('woof_qt_extended')) {
            wp_enqueue_script('woof_alasql', $this->get_ext_link() . "/js/alasql/alasql.min.js", array(), WOOF_VERSION);
            $parse_tpl = explode("/", WOOF_REQUEST::get('woof_qt_extended'));
            if (count($parse_tpl) > 1 AND $parse_tpl[0] == 'custom') {
                wp_enqueue_style('woof_qs_style', get_stylesheet_directory_uri() . "/woof_qs_templates/" . $parse_tpl[1] . "/css/" . $parse_tpl[1] . ".css", array(), WOOF_VERSION);
                wp_enqueue_script('woof_qs_script', get_stylesheet_directory_uri() . "/woof_qs_templates/" . $parse_tpl[1] . "/js/" . $parse_tpl[1] . ".js", array(), WOOF_VERSION);
            } else {
                wp_enqueue_style('woof_qs_style', $this->get_ext_link() . "/views/templates/" . $parse_tpl[0] . "/css/" . $parse_tpl[0] . ".css", array(), WOOF_VERSION);
                wp_enqueue_script('woof_qs_script', $this->get_ext_link() . "/views/templates/" . $parse_tpl[0] . "/js/" . $parse_tpl[0] . ".js", array(), WOOF_VERSION);
            }
        } else {
            wp_enqueue_script('easy-autocomplete', WOOF_LINK . 'js/easy-autocomplete/jquery.easy-autocomplete.min.js', array(), WOOF_VERSION);
            wp_enqueue_style('easy-autocomplete', WOOF_LINK . 'js/easy-autocomplete/easy-autocomplete.min.css', array(), WOOF_VERSION);
            wp_enqueue_style('easy-autocomplete-theme', WOOF_LINK . 'js/easy-autocomplete/easy-autocomplete.themes.min.css', array(), WOOF_VERSION);
        }
        wp_enqueue_script('woof_quick_search', $this->get_ext_link() . 'js/quick_search.js', array(), WOOF_VERSION);
        $link = '';
        if (WOOF_REQUEST::isset('woof_quick_search_link')) {
            $link = WOOF_REQUEST::get('woof_quick_search_link');
        } else {
            $link = $this->get_ext_link() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'data_0000_xxx.json';
        }
        $currency_data = array();
        if (class_exists('WOOCS')) {
            global $WOOCS;
            $currencies = $WOOCS->get_currencies();
            $currency_data['symbol'] = $currencies[$WOOCS->default_currency]['symbol'];
            $currency_data['decimal'] = $currencies[$WOOCS->default_currency]['decimals'];
            $currency_data['position'] = $currencies[$WOOCS->default_currency]['position'];
        } else {
            $currency_data['symbol'] = get_woocommerce_currency_symbol();
            $currency_data['decimal'] = wc_get_price_decimals();
            $currency_data['position'] = get_option('woocommerce_currency_pos');
        }
        $currency_data['t_separ'] = wc_get_price_thousand_separator();
        $currency_data['d_separ'] = wc_get_price_decimal_separator();
        $text_data = array(
            'link' => $link,
            'site_url' => get_site_url(),
            'no_image' => '/wp-content/plugins/woocommerce/assets/images/placeholder.png',
            'currency_data' => $currency_data,
        );
        wp_localize_script('woof_quick_search', 'wooftextfilelink', $text_data);
    }

    public function woof_print_applications_tabs() {
        ?>
        <li>
            <a href="#tabs-quick-text">
                <span class="icon-flash"></span>
                <span><?php esc_html_e("Quick search", 'woocommerce-products-filter') ?></span>
            </a>
        </li>
        <?php
    }

    public function woof_print_applications_tabs_content() {
        wp_enqueue_script('woof_qs_admin_', $this->get_ext_link() . 'js/admin.js', array(), WOOF_VERSION);
        wp_enqueue_style('woof_qs_admin_css', $this->get_ext_link() . 'css/admin.css', [], WOOF_VERSION);

        
        $data = array();
        $data['woof_settings'] = $this->woof_settings;
        echo woof()->render_html($this->get_ext_path() . 'views/tabs_content.php', $data);
    }

    public function get_woof_cron_schedules($key = '') {
        $schedules = array(
            'daily' => DAY_IN_SECONDS,
            'week' => WEEK_IN_SECONDS,
            'twicemonthly' => WEEK_IN_SECONDS * 2,
            'month' => WEEK_IN_SECONDS * 4,
            'min1' => MINUTE_IN_SECONDS,
        );

        if (!empty($key)) {
            return $schedules[$key];
        }

        return $schedules;
    }

    public function create_data_search_files() {
        
        $tax_query = array();
        $start = 0;
        $step = 10;
        $do = true;
        $id = "0000";
		$id = $this->check_id_file($id);
        if (isset($_POST['qs_start'])) {
            $start = intval($_POST['qs_start']);
        }


        if ($start == 0) {
           // $this->delete_all_files('temp');
        }
        $tax_query = $this->_expand_additional_taxes_string($this->tax_conditional, $tax_query);
        if (version_compare(WOOCOMMERCE_VERSION, '3.0', '>=')) {
            $tax_query[] = array(
                'taxonomy' => 'product_visibility',
                'field' => 'name',
                'terms' => 'exclude-from-catalog',
                'operator' => 'NOT IN',
            );
        }
        $args = array(
            'post_type' => array('product'/* ,'product_variation' */),
            'post_status' => 'publish',
            'tax_query' => $tax_query,
            'offset' => $start,
            'posts_per_page' => $step,
            'fields' => 'ids',
            'cache_results' => false,
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
        $limit = apply_filters("woof_quick_search_products_limit", 10000);
        if ($start > $limit) { // limiting the number of products
            $do = false;
        }
        $product_ids = new WP_Query($args);
        if ($product_ids->found_posts < 1) {
            $do = false;
        }
        if ($do) {
            if ($product_ids->found_posts < $step) {
                $start -= $step;
                $start += $product_ids->found_posts;
            }

            $this->push_products_data($product_ids, $id);
        } else {
            $start = -1;
        }
		if ($start < 0){
			//$this->delete_all_files();
			$this->rename_files($id);
			
		}
        $result = array('total' => $start);
        exit(json_encode($result));
    }

    public function push_products_data($product_ids, $id) {
        $langs = $this->get_all_lang_wpml();
        $posts = array();
        $posts_data = array();

        if ($langs) {
            foreach ($langs as $lang) {
                $posts_data = array();
                foreach ($product_ids->posts as $post) {
                    $posts_lang = $this->get_post_id_by_lang($post, $lang['language_code']);
                    if ($posts_lang) {
                        $posts_data[] = $this->get_data_by_id($posts_lang, $lang['language_code']);
                    }
                }
                $this->push_data_into_file($posts_data, $id, $lang['language_code']);
            }
        } else {

            foreach ($product_ids->posts as $post) {
                $posts_data[] = $this->get_data_by_id($post);
            }
            $this->push_data_into_file($posts_data, $id);
        }
    }

    public function get_data_by_id($id, $lang = 'xxx') {
        
        if (!$id OR $id < 1) {
            return array();
        }
        $product = wc_get_product($id);
        if (!$product) {
            return array();
        }
        $data = array();
        $data['id'] = $id;
        $data['title'] = $product->get_name();
        $data['url'] = $product->get_permalink();
        $data['url'] = apply_filters('wpml_permalink', $data['url'], $lang);
        $img = wp_get_attachment_image_src($product->get_image_id($id), 'shop_single');

        if (isset($img[0])) {
            $data['img'] = $this->optimize_url($img[0]);
        }

        $data['sku'] = $product->get_sku();
        $data['price'] = $this->get_all_prices($product);
        $data['key_words'] = "";
        $data['term_ids'] = " ";
        $data['meta_data'] = array();
        $term_ids = array();
        $all_taxonomies = woof()->get_taxonomies();
        foreach ($this->tax_serch_data as $tax) {
            unset($all_taxonomies[$tax]); //not to do double work  (get all id terms)
            $terms = get_the_terms($id, $tax);
            if (!is_array($terms)) {
                continue;
            }
            $term_name = array();
            $term_ids = array();
            foreach ($terms as $term) {
                $term_ids[] = $term->term_id;
                $term_name[] = html_entity_decode($term->name);
            }
            if (!empty($term_name)) {
                if (!empty($data['key_words'])) {
                    $data['key_words'] .= "; ";
                }
                $data['key_words'] .= implode(', ', $term_name);
            }
            if (!empty($term_ids)) {
                $data['term_ids'] .= " ";
                $data['term_ids'] .= implode(' ', $term_ids);
            }
        }
        $data['term_ids'] .= " ";
        if (true) {   // if you want search in all taxonomies by additional filters( ignore $this->tax_serch_data )
            foreach ($all_taxonomies as $key_slug => $val_tax) {
                $_terms = get_the_terms($id, $key_slug);
                if (!is_array($_terms)) {
                    continue;
                }
                $term_ids = array();
                foreach ($_terms as $term) {
                    $term_ids[] = $term->term_id;
                }
                if (!empty($term_ids)) {

                    $data['term_ids'] .= implode(' ', $term_ids);
                }
                $data['term_ids'] .= " ";
            }
        }
        $data['meta_data'] = $this->get_meta_data_by_id($id);

        return $data;
    }

    public function get_meta_data_by_id($id) {
        $meta_data = array();
        if (class_exists('WOOF_META_FILTER')) {
            $meta_fields = $this->woof_settings['meta_filter'];
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

        if ($this->cron_system === 0) {//wp cron
            if (!$this->cron->is_attached($hook, $this->get_woof_cron_schedules($this->wp_cron_period))) {
                $this->cron->attach($hook, time(), $this->get_woof_cron_schedules($this->wp_cron_period));
            }

            $this->cron->process();
        }
    }

    //  File works
    private function delete_all_files($folder = '') {
        $path = $this->get_ext_path() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR;
		if ($folder) {
			$path .= $folder . DIRECTORY_SEPARATOR;
		}
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle)))
                if ($file != "." && $file != "..")
                    unlink($path . $file);
            closedir($handle);
        }
    }
	private function rename_files($id) {
            $dir_path = $file = $this->get_ext_path() . '/data/';

            $langs = $this->get_all_lang_wpml();
            if ($langs) {
                foreach ($langs as $lang) {
                    if (file_exists($dir_path . 'temp/' . 'data_' . $id . "_" . $lang['language_code'] . ".json")) {
                        rename($dir_path . 'temp/' . 'data_' . $id . "_" . $lang['language_code'] . ".json", $dir_path . 'data_' . $id . "_" . $lang['language_code'] . ".json");
                    }
                }
            } else {

                if (file_exists($dir_path . 'temp/' . 'data_' . $id . "_xxx.json")) {
                    rename($dir_path . 'temp/' . 'data_' . $id . "_xxx.json", $dir_path . 'data_' . $id . "_xxx.json");
                }
            }		
	}

    private function push_data_into_file($data, $id = '0000', $lang = 'xxx') {

        $file = $this->get_ext_path() . '/data/temp/data_' . $id . '_' . $lang . '.json';
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

    //themplate helper
    public static function show_sort_html_by_title() {
        return self::show_sort_html('title-asc', 'title-desc');
    }

    public static function show_sort_html_by_price() {
        return self::show_sort_html('price-asc', 'price-desc');
    }

    public static function show_sort_html_select() {
        $data = array(
            'price-asc' => sprintf(__('Price- %s ', 'woocommerce-products-filter'), '&uarr;'),
            'price-desc' => sprintf(__('Price- %s ', 'woocommerce-products-filter'), '&darr;'),
            'title-asc' => sprintf(__('Title- %s ', 'woocommerce-products-filter'), '&uarr;'),
            'title-desc' => sprintf(__('Title- %s ', 'woocommerce-products-filter'), '&darr;'),
        );
        return self::show_sort_select_html(apply_filters('woof_qs_sort_select_data', $data));
    }

    public static function show_sort_html($asc = 'title-asc', $desc = 'title-desc') {
        
        $data = array(
            'asc' => $asc,
            'desc' => $desc,
        );
        return woof()->render_html(WOOF_EXT_PATH . 'quick_search' . DIRECTORY_SEPARATOR . 'views/sort_html.php', $data);
    }

    public static function show_sort_select_html($args) {
        $data = array(
            'sort' => $args,
        );
        
        return woof()->render_html(WOOF_EXT_PATH . 'quick_search' . DIRECTORY_SEPARATOR . 'views/sort_select_html.php', $data);
    }

    //+++
    public static function parse_template_structure($str) {
        $tpl_str = array(); //array('key'=>'title',title=>'Title',class=>'woof_qs_title','alias'=>'__TITLE__')
        //img,title,price,sku,key_words
        $default_titles = array(
            'title' => esc_html__('Title', 'woocommerce-products-filter'),
            'img' => '',
            'price' => esc_html__('Price', 'woocommerce-products-filter'),
            'sku' => esc_html__('SKU', 'woocommerce-products-filter'),
            'key_words' => esc_html__('Tags', 'woocommerce-products-filter'),
        );
        $temp_arr = explode(',', $str);
        foreach ($temp_arr as $item) {
            $item = trim($item);
            if (!isset($default_titles[$item])) {
                continue;
            }
            $tpl_str[$item] = array(
                'key' => $item,
                'title' => $default_titles[$item],
                'class' => 'woof_qs_' . $item,
                'alias' => sprintf("__%s__", strtoupper($item))
            );
        }

        return apply_filters('woof_qs_get_template_structure', $tpl_str);
    }
	public function check_id_file($id="0000") {
		if(is_multisite()) {
			$id.= get_current_blog_id();
		}
		return $id;
	}
    public function optimize_url($url) {
        $site_url = get_site_url();
        return str_replace($site_url, "", $url);
    }

}

WOOF_EXT::$includes['applications']['quick_search'] = new WOOF_EXT_QUICK_TEXT();
