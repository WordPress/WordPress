<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

define('HUSKY_INIT', true);

final class WOOF_EXT_BY_TEXT extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'by_text'; //your custom key here
    public $index = 'woof_text';
    public $html_type_dynamic_recount_behavior = 'none';
    public $options = [];
    private $cache = null;
    private $use_post__in = true;
    private $data_fields = ['title', 'placeholder', 'behavior', 'search_by_full_word',
        'autocomplete', 'how_to_open_links', 'taxonomy_compatibility', 'sku_compatibility',
        'custom_fields', 'search_desc_variant', 'view_text_length', 'min_symbols', 'max_posts', 'min_symbols',
        'image', 'notes_for_customer', 'template', 'max_open_height', 'page'];

    public function __construct() {
        parent::__construct();
        include_once $this->get_ext_path() . 'classes/cache.php';
        $this->cache = new WoofTextCache();
        $this->use_post__in = apply_filters('woof_husky_query_post__in', true);

        //default data fields
        $this->options = $this->data_fields();
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

        add_action('wp_ajax_woof_text_search', array($this, 'ajax_search'));
        add_action('wp_ajax_nopriv_woof_text_search', array($this, 'ajax_search'));

        add_filter('woof_add_items_keys', array($this, 'woof_add_items_keys'));
        add_filter('woof_get_request_data', array($this, 'woof_get_request_data'));

        add_action('wp_enqueue_scripts', array($this, 'wp_head'), 9);

        add_action('woof_print_html_type_' . $this->html_type, array($this, 'print_html_type'), 10, 1);
        add_action('woof_print_html_type_options_' . $this->html_type, array($this, 'woof_print_html_type_options'), 10, 1);

        //search
        add_filter('posts_join', array($this, 'posts_join'), 20, 2);
        add_filter('posts_where', array($this, 'posts_where'), 20, 2);
        add_filter('posts_groupby', array($this, 'posts_groupby'), 10, 2);

        add_action('woocommerce_product_query', array($this, 'woo_product_query'), 9999);
        add_filter('woocommerce_shortcode_products_query', array($this, 'woo_shortcode_products_query'), 99, 3);
        add_filter('woof_products_query', array($this, 'woof_products_query'));

        add_filter('woof_dynamic_count_attr', array($this, 'woof_dynamic_count_attr'), 9999, 2);
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'assets/css/front.css';
        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'assets/js/front.js';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_text'; //we have no init function in this case


        add_shortcode('woof_text_filter', array($this, 'woof_text_filter'));

        add_action('init', array($this, 'init_data'), 99999);
        add_action('wp_enqueue_scripts', array($this, 'add_additional_js'), 9);
		
		//add_filter('woof_get_filtered_price_query', array($this, 'add_to_filtered_price'));
    }

    public function init_data() {
        $this->options = $this->data_fields();
    }

    public function add_additional_js() {

        $request = woof()->get_request_data();
        $search_text = "";
        if (isset($request['woof_text']) AND $request['woof_text']) {
            $search_text = ":<i>" . $request['woof_text']. '</i>';
        }
        self::$includes['js_lang_custom'][$this->index] = esc_html__('By text', 'woocommerce-products-filter') . $search_text;
    }
	public function add_to_filtered_price($sql) {
        $request = woof()->get_request_data();

        if (isset($request['woof_text']) AND $request['woof_text']) {
            $search_text = $request['woof_text'];
			global $wpdb;
			$ids = $this->get_all_ids($search_text);
			if (empty($ids)) {
				$ids = array(-1);
			}	
			$product_ids = implode(',', $ids);
			$sql.= " AND ( $wpdb->posts.ID IN($product_ids))";
		}
		return $sql;
	}
	public function woof_products_query($query_args) {

        $request = woof()->get_request_data();

        if (isset($request['woof_text']) AND $request['woof_text']) {
            $search_text = $request['woof_text'];

            if ($this->use_post__in) {
                $ids = $this->get_all_ids($search_text);
                if (!empty($ids)) {
                    if (isset($query_args['post__in']) && !empty($query_args['post__in'])) {
                        $ids = array_intersect($ids, $query_args['post__in']);
                    }
                } else {
                    $ids = array(-1);
                }

                $query_args['post__in'] = $ids;
            } else {
                $query_args['woof_text_filter'] = $search_text;
            }
        }

        return $query_args;
    }

    public function woof_dynamic_count_attr($query_args, $custom_type) {

        $request = woof()->get_request_data();

        if (isset($request['woof_text']) AND $request['woof_text']) {
            $search_text = $request['woof_text'];

            if ($this->use_post__in) {
                $ids = $this->get_all_ids($search_text);
                if (!empty($ids)) {
                    if (isset($query_args['post__in']) && !empty($query_args['post__in'])) {
                        $ids = array_intersect($ids, $query_args['post__in']);
                    }
                } else {
                    $ids = array(-1);
                }

                $query_args['post__in'] = $ids;
            } else {
                $query_args['woof_text_filter'] = $search_text;
            }
        }

        return $query_args;
    }

    public function woo_product_query($q) {

        $request = woof()->get_request_data();

        if (isset($request['woof_text']) AND $request['woof_text']) {
            $search_text = $request['woof_text'];
            if ($this->use_post__in) {
                $post__in = $q->get('post__in');
                $ids = $this->get_all_ids($search_text);
                if (!empty($ids)) {
                    if (!empty($post__in)) {
                        $ids = array_intersect($ids, $post__in);
                    }
                } else {
                    $ids = array(-1);
                }

                $q->set('post__in', $ids);
            } else {
                $q->set('woof_text_filter', $search_text);
            }
        }
    }

    public function woo_shortcode_products_query($query_args, $attr, $type = "") {
        if (WOOF_REQUEST::get('override_no_products')) {
            return $query_args;
        }

        $request = woof()->get_request_data();

        if (isset($request['woof_text']) AND $request['woof_text']) {
            $search_text = $request['woof_text'];
            if ($this->use_post__in) {
                $ids = $this->get_all_ids($search_text);
                if (!empty($ids)) {
                    if (isset($query_args['post__in']) && !empty($query_args['post__in'])) {
                        $ids = array_intersect($ids, $query_args['post__in']);
                    }
                } else {
                    $ids = array(-1);
                }

                $query_args['post__in'] = $ids;
            } else {
                $query_args['woof_text_filter'] = $search_text;
            }
        }

        return $query_args;
    }

    public function woof_get_request_data($request) {
        if (isset($request['s'])) {
            $request['woof_text'] = $request['s'];
        }

        return $request;
    }

    public function cache_compatibility($args, $type) {

        $request = woof()->get_request_data();
        if (isset($request['woof_text']) AND $request['woof_text']) {
            $args['woof_text'] = $request['woof_text'];
        }

        return $args;
    }

    public function wp_head() {


        //self::$includes['js_code_custom']['woof_' . $this->html_type . '_html_items'] = $this->get_js();
        $request = woof()->get_request_data();
        $search_text = "";
        if (isset($request['woof_text']) AND $request['woof_text']) {
            $search_text = ":" . $request['woof_text'];
        }
        self::$includes['js_lang_custom'][$this->index] = esc_html__('By text', 'woocommerce-products-filter') . $search_text;

        wp_enqueue_script('woof-husky', $this->get_ext_link() . 'assets/js/husky.js', [], WOOF_VERSION);
        //wp_enqueue_script('woof_husky_txt-front', $this->get_ext_link() . 'assets/js/front.js', ['woof_husky_txt'], WOOF_VERSION);
        wp_localize_script('woof-husky', 'woof_husky_txt', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'plugin_uri' => $this->get_ext_link(),
            'loader' => $this->get_ext_link() . 'assets/img/ajax-loader.gif',
            'not_found' => __('Nothing found!', 'woocommerce-products-filter'),
            'prev' => __('Prev', 'woocommerce-products-filter'),
            'next' => __('Next', 'woocommerce-products-filter'),
            'site_link' => site_url(),
            'default_data' => $this->data_fields()
        ));

        wp_enqueue_script('woof_husky_txt');
    }

    //settings page hook
    public function woof_print_html_type_options() {

        woof()->control_extension_by_key('by_text_2', false);
        woof()->render_html_e($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'options.php', array(
            'key' => $this->html_type,
            "woof_settings" => get_option('woof_settings', array())
                )
        );
    }

    public function assemble_query_params(&$meta_query, $wp_query = NULL) {
        //add_filter('posts_where', array($this, 'woof_post_text_filter'), 9999, 2); //for searching by text
        return $meta_query;
    }

    private function data_fields($args = []) {
        $data = [];

        foreach ($this->data_fields as $field) {

            switch ($field) {
                case 'text':
                    $data['text'] = isset($args['text']) ? sanitize_text_field($args['text']) : 'title';
                    break;
                case 'placeholder':
                    if (isset($args['placeholder'])) {
                        $data['placeholder'] = $args['placeholder'];
                    } elseif (isset(woof()->settings[$this->html_type]['placeholder'])) {
                        $data['placeholder'] = woof()->settings[$this->html_type]['placeholder'];
                    } else {
                        $data['placeholder'] = '';
                    }
                    $data['placeholder'] = WOOF_HELPER::wpml_translate(null, $data['placeholder']);
                    break;
                case 'behavior':
                    if (isset($args['behavior'])) {
                        $data['behavior'] = $args['behavior'];
                    } elseif (isset(woof()->settings[$this->html_type]['behavior'])) {
                        $data['behavior'] = woof()->settings[$this->html_type]['behavior'];
                    } else {
                        $data['behavior'] = 'title';
                    }
                    break;
                case 'search_by_full_word':
                    if (isset($args['search_by_full_word'])) {
                        $data['search_by_full_word'] = $args['search_by_full_word'];
                    } elseif (isset(woof()->settings[$this->html_type]['search_by_full_word'])) {
                        $data['search_by_full_word'] = woof()->settings[$this->html_type]['search_by_full_word'];
                    } else {
                        $data['search_by_full_word'] = 0;
                    }
                    break;
                case 'autocomplete':
                    if (isset($args['autocomplete'])) {
                        $data['autocomplete'] = $args['autocomplete'];
                    } elseif (isset(woof()->settings[$this->html_type]['autocomplete'])) {
                        $data['autocomplete'] = woof()->settings[$this->html_type]['autocomplete'];
                    } else {
                        $data['autocomplete'] = 0;
                    }
                    break;

                case 'how_to_open_links':
                    if (isset($args['how_to_open_links'])) {
                        $data['how_to_open_links'] = $args['how_to_open_links'];
                    } elseif (isset(woof()->settings[$this->html_type]['how_to_open_links'])) {
                        $data['how_to_open_links'] = woof()->settings[$this->html_type]['how_to_open_links'];
                    } else {
                        $data['how_to_open_links'] = 0;
                    }

                    break;

                case 'taxonomy_compatibility':

                    if (isset($args['taxonomy_compatibility'])) {
                        $data['taxonomy_compatibility'] = $args['taxonomy_compatibility'];
                    } elseif (isset(woof()->settings[$this->html_type]['taxonomy_compatibility'])) {
                        $data['taxonomy_compatibility'] = woof()->settings[$this->html_type]['taxonomy_compatibility'];
                    } else {
                        $data['taxonomy_compatibility'] = 0;
                    }
                    break;
                case 'sku_compatibility':
                    if (isset($args['sku_compatibility'])) {
                        $data['sku_compatibility'] = $args['sku_compatibility'];
                    } elseif (isset(woof()->settings[$this->html_type]['sku_compatibility'])) {
                        $data['sku_compatibility'] = woof()->settings[$this->html_type]['sku_compatibility'];
                    } else {
                        $data['sku_compatibility'] = 0;
                    }
                    break;
                case 'custom_fields':
                    if (isset($args['custom_fields'])) {
                        $data['custom_fields'] = $args['custom_fields'];
                    } elseif (isset(woof()->settings[$this->html_type]['custom_fields'])) {
                        $data['custom_fields'] = woof()->settings[$this->html_type]['custom_fields'];
                    } else {
                        $data['custom_fields'] = '';
                    }
                    break;
                case 'search_desc_variant':
                    if (isset($args['search_desc_variant'])) {
                        $data['search_desc_variant'] = $args['search_desc_variant'];
                    } elseif (isset(woof()->settings[$this->html_type]['search_desc_variant'])) {
                        $data['search_desc_variant'] = woof()->settings[$this->html_type]['search_desc_variant'];
                    } else {
                        $data['search_desc_variant'] = 0;
                    }
                    break;

                case 'view_text_length':
                    if (isset($args['view_text_length'])) {
                        $data['view_text_length'] = $args['view_text_length'];
                    } elseif (isset(woof()->settings[$this->html_type]['view_text_length'])) {
                        $data['view_text_length'] = woof()->settings[$this->html_type]['view_text_length'];
                    } else {
                        $data['view_text_length'] = 10;
                    }
                    break;

                case 'min_symbols':
                    if (isset($args['min_symbols'])) {
                        $data['min_symbols'] = $args['min_symbols'];
                    } elseif (isset(woof()->settings[$this->html_type]['min_symbols'])) {
                        $data['min_symbols'] = woof()->settings[$this->html_type]['min_symbols'];
                    } else {
                        $data['min_symbols'] = 3;
                    }
                    break;

                case 'max_posts':
                    if (isset($args['max_posts'])) {
                        $data['max_posts'] = $args['max_posts'];
                    } elseif (isset(woof()->settings[$this->html_type]['max_posts'])) {
                        $data['max_posts'] = woof()->settings[$this->html_type]['max_posts'];
                    } else {
                        $data['max_posts'] = 10;
                    }
                    break;

                case 'max_open_height':
                    if (isset($args['max_open_height'])) {
                        $data['max_open_height'] = $args['max_open_height'];
                    } elseif (isset(woof()->settings[$this->html_type]['max_open_height'])) {
                        $data['max_open_height'] = woof()->settings[$this->html_type]['max_open_height'];
                    } else {
                        $data['max_open_height'] = 300;
                    }

                    break;

                case 'use_cache':
                    if (isset($args['use_cache'])) {
                        $data['use_cache'] = $args['use_cache'];
                    } elseif (isset(woof()->settings[$this->html_type]['use_cache'])) {
                        $data['use_cache'] = woof()->settings[$this->html_type]['use_cache'];
                    } else {
                        $data['use_cache'] = 0;
                    }

                    break;

                case 'page':
                    $data['page'] = isset($args['page']) ? intval($args['page']) : 0;
                    break;

                case 'title_light':
                    $data['title_light'] = isset($args['title_light']) ? intval($args['title_light']) : 1;
                    break;

                case 'click_on_option':
                    $data['click_on_option'] = isset($args['click_on_option']) ? intval($args['click_on_option']) : 0;
                    break;

                case 'template':
                    if (isset($args['template'])) {
                        $data['template'] = $args['template'];
                    } elseif (isset(woof()->settings[$this->html_type]['template'])) {
                        $data['template'] = woof()->settings[$this->html_type]['template'];
                    } else {
                        $data['template'] = 'default';
                    }
                    break;
                case 'image':
                    if (isset($args['image'])) {
                        $data['image'] = $args['image'];
                    } elseif (isset(woof()->settings[$this->html_type]['image'])) {
                        $data['image'] = woof()->settings[$this->html_type]['image'];
                    } else {
                        $data['image'] = '';
                    }
                    break;
                case 'notes_for_customer':
                    if (isset($args['notes_for_customer'])) {
                        $data['notes_for_customer'] = $args['notes_for_customer'];
                    } elseif (isset(woof()->settings[$this->html_type]['notes_for_customer'])) {
                        $data['notes_for_customer'] = woof()->settings[$this->html_type]['notes_for_customer'];
                    } else {
                        $data['notes_for_customer'] = '';
                    }
                    break;
            }
        }

        return $data;
    }

    private function get_thumbnail($post_id, $size = 'thumbnail') {
        $img = '';

        $thumbnail_id = get_post_thumbnail_id($post_id);
        if ($thumbnail_id > 0) {
            $img = wp_get_attachment_image_src($thumbnail_id, $size);
            if (!isset($img[0])) {
                $img = wp_get_attachment_url($thumbnail_id);
            } else {
                $img = $img[0];
            }
        }

        return $img;
    }

    private function get_breadcrumb($post_id) {
        $terms = get_the_terms($post_id, 'product_cat');
        $div = '&gt;';
        $breadcrumb = '<div class="woof_husky_txt-option-breadcrumb">';
        if (!empty($terms)) {
            $max_parent_count = 0;
            $max_count = 0;
            $head_id = 0;
            $tail_id = 0;
            $head = '';
            $tail = '';
            foreach ($terms as $term) {
                if ($term->parent === 0) {
                    if ($term->count > $max_parent_count) {
                        $max_parent_count = $term->count;
                        $head_id = $term->term_id;
                    }
                } else {
                    if ($term->count > $max_count) {
                        $max_count = $term->count;
                        $tail_id = $term->term_id;
                    }
                }
            }

            //+++
            foreach ($terms as $term) {
                if ($term->term_id === $head_id) {
                    $plink = get_term_link($term);
                    $head = "<a href='{$plink}' target='_blank'>{$term->name}</a>";
                }

                if ($term->term_id === $tail_id) {
                    $plink = get_term_link($term);
                    $tail = "<a href='{$plink}' target='_blank'>{$term->name}</a>";
                }
            }

            if ($tail) {
                $tail = $div . ' ' . $tail;
            }

            $breadcrumb .= "<a href='/' target='_blank'>" . __('Home', 'woocommerce-products-filter') . "</a> {$div} {$head} {$tail}";
        }
        $breadcrumb .= '</div>';

        return $breadcrumb;
    }

    private function normalize_ids_array($array) {
        $res = [];

        if (!empty($array)) {
            foreach ($array as $value) {
                $res[] = $value[0];
            }
        }

        return $res;
    }

    private function render_html($pagepath, $data = []) {
        if (isset($data['pagepath'])) {
            unset($data['pagepath']);
        }
        if (is_array($data) AND!empty($data)) {
            extract($data);
        }
        $pagepath = realpath($pagepath);
        if (!$pagepath) {
            return "";
        }
        $pagepath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pagepath);
        ob_start();
        include($pagepath);
        return ob_get_clean();
    }

    private function render_html_e($pagepath, $data = []) {
        if (isset($data['pagepath'])) {
            unset($data['pagepath']);
        }
        if (is_array($data) AND!empty($data)) {
            extract($data);
        }
        $pagepath = realpath($pagepath);
        if (!$pagepath) {
            return;
        }
        $pagepath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pagepath);
        include($pagepath);
    }

    //shortcode
    public function woof_text_filter($args = array()) {

        if (!is_array($args)) {
            $args = array();
        }

        $args['loader_img'] = $this->get_ext_link() . 'img/loader.gif';

        if (file_exists($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_text_filter.php')) {
            return woof()->render_html($this->get_ext_override_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_text_filter.php', ['data' => $this->data_fields($args)]);
        }

        return woof()->render_html($this->get_ext_path() . 'views' . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . 'woof_text_filter.php', ['data' => $this->data_fields($args)]);
    }

    public function posts_groupby($groupby, $wp_query) {

        global $wpdb;

        if (isset($wp_query->query_vars['woof_text_filter']) && $wp_query->query_vars['woof_text_filter']) {
            $groupby = "{$wpdb->posts}.ID";
        }

        return $groupby;
    }

    public function posts_join($join, $wp_query) {
        if (!isset($wp_query->query_vars['woof_text_filter']) && !isset($wp_query->query_vars['woof_text_filter'])) {
            return $join;
        }
        $tax_search = 0;
        $meta_search = 0;
        if (isset($this->options['taxonomy_compatibility']) && $this->options['taxonomy_compatibility']) {
            $tax_search = 1;
        }

        if ((isset($this->options['sku_compatibility']) && $this->options['sku_compatibility']) || (isset($this->options['custom_fields']) && !empty($this->options['custom_fields']))) {
            $meta_search = 1;
        }

        global $wpdb;
        if ($tax_search) {
            $join .= " LEFT JOIN {$wpdb->term_relationships} as trm_r ON {$wpdb->posts}.ID = trm_r.object_id INNER JOIN {$wpdb->term_taxonomy} trm_t ON trm_t.term_taxonomy_id=trm_r.term_taxonomy_id INNER JOIN {$wpdb->terms} trm ON trm.term_id = trm_t.term_id";
        }
        if ($meta_search) {
            $join .= " LEFT JOIN $wpdb->postmeta AS postmeta ON ( {$wpdb->posts}.ID = postmeta.post_id )";
        }

        return $join;
    }

    public function posts_where($where, $wp_query) {

        if (!isset($wp_query->query_vars['woof_text_filter']) && !isset($wp_query->query_vars['woof_text_filter'])) {
            return $where;
        }

        $search_terms = $wp_query->query_vars['woof_text_filter'];

        $sql = $this->create_where_query($search_terms);

        if ($sql) {
            $where .= ' AND ' . $sql;
        }
        return $where;
    }

    public function create_where_query($search_terms) {
        $tax_search = 0;
        $meta_search = 0;
        $search_on_vardesc = 0;
        $meta_search_terms = array();
        $general_search_terms = array();

        if (isset($this->options['taxonomy_compatibility']) && $this->options['taxonomy_compatibility']) {
            $tax_search = 1;
        }

        if (isset($this->options['custom_fields']) && !empty($this->options['custom_fields'])) {
            $meta_search_terms = explode(',', $this->options['custom_fields']);
            $meta_search_terms = array_map('trim', $meta_search_terms);
        }

        if (isset($this->options['sku_compatibility']) && $this->options['sku_compatibility']) {
            $meta_search_terms[] = '_sku';
        }
        if (!empty($meta_search_terms)) {
            $meta_search = 1;
        }
        if (isset($this->options['search_desc_variant']) && $this->options['search_desc_variant']) {
            $search_on_vardesc = 1;
        }



        switch ($this->options['behavior']) {
            case'content':
                $general_search_terms = array('post_content');
                break;
            case'excerpt':
                $general_search_terms = array('post_excerpt');
                break;
            case'content_or_excerpt':
                $general_search_terms = array('post_excerpt', 'post_content');
                break;
            case'title_or_content_or_excerpt':
                $general_search_terms = array('post_title', 'post_excerpt', 'post_content');
                break;
            case'title_or_content':
                $general_search_terms = array('post_title', 'post_content');
                break;
            default :
                $general_search_terms = array('post_title');
        }

        global $wpdb;

        if (!is_array($search_terms)) {	
			$search_terms = htmlspecialchars_decode($search_terms);
	        $search_terms = explode(' ', $search_terms);
        }

        $sql = array();

        foreach ($search_terms as $term) {
            $sub_sql = array();
            $relation = ' OR ';
            $search_type = ' LIKE ';

            if ('-' == substr($term, 0, 1)) {
                $relation = ' AND ';
                $search_type = ' NOT LIKE ';
            }
            $word = $wpdb->esc_like($term);
            if (isset($this->options['search_by_full_word']) && $this->options['search_by_full_word'] == 1) {
                $search_type = ' RLIKE ';
                $like = '[[:<:]]' . $wpdb->esc_like($term) . '[[:>:]]';
            } else {
                $like = '%' . $wpdb->esc_like($term) . '%';
            }

            foreach ($general_search_terms as $terms) {
                //$sub_sql[] = $wpdb->prepare("({$terms} $search_type %s)",  $like);
                $sub_sql[] = '(' . $terms . ' ' . $search_type . ' "'.$like . '" )';
            }
            if ($tax_search) {
                $sub_sql[] = $wpdb->prepare("( trm.name $search_type %s)", $word);
            }
            if ($meta_search && !empty($meta_search_terms)) {
                foreach ($meta_search_terms as $meta_term) {
                    $sub_sql[] = $wpdb->prepare("( postmeta.meta_key = '%s' AND  postmeta.meta_value $search_type %s)", $meta_term, $like);
                }
            }

            $sql[] = '(' . implode($relation, $sub_sql) . ') ';
        }
        // prepare search on variations

        $res_sql = '(' . implode(' AND ', $sql) . ') ';

        if (1 == $search_on_vardesc) {

            if (isset($this->options['use_cache']) && (int) $this->options['use_cache'] == 1) {
                $use_cache = true;
            } else {
                $use_cache = false;
            }
            //cache
            $product_ids = false;
            if ($use_cache) {
                $key = $this->cache->create_key($search_terms);
                $product_ids = $this->cache->get($key);
            }
            if (!$product_ids) {
                $sub_sql_var = array();
                foreach ($search_terms as $term) {
                    $sub_sql = array();
                    $search_type = ' LIKE ';
                    if ('-' == substr($term, 0, 1)) {
                        $search_type = ' NOT LIKE ';
                    }
                    $word = $wpdb->esc_like($term);
                    if (isset($this->options['search_by_full_word']) && $this->options['search_by_full_word'] == 1) {
                        $search_type = ' RLIKE ';
                        $like = '[[:<:]]' . $wpdb->esc_like($term) . '[[:>:]]';
                    } else {
                        $like = '%' . $wpdb->esc_like($term) . '%';
                    }
                    $sub_sql_var[] = $wpdb->prepare(" (postmeta.meta_value $search_type %s )", $like);
                }
                $condtion_string = implode(' AND ', $sub_sql_var);
                $product_variations = $wpdb->get_results("
							SELECT posts.ID
							FROM $wpdb->posts AS posts
							LEFT JOIN $wpdb->postmeta AS postmeta ON ( posts.ID = postmeta.post_id )
							WHERE posts.post_type IN ('product_variation')
							AND postmeta.meta_key = '_variation_description'
							AND ($condtion_string)", ARRAY_N);

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
                            $product_ids[] = $v[0];
                        }
                    }

                    $product_ids = implode(',', $product_ids);
                    if ($use_cache) {
                        $this->cache->set($key, $product_ids);
                    }
                }
            }

            if ($product_ids) {
                $res_sql = " (  $res_sql OR $wpdb->posts.ID IN($product_ids))";
            }
        }
        //+++

        return $res_sql;
    }

    public function get_all_ids($search_text, $options = array()) {
        if (isset($options['page'])) {
            unset($options['page']);
        }
        $options['max_posts'] = -1;
        $products = $this->init_text_search($search_text, $options);
        $ids = $products->posts;
        return $ids;
    }

    public function init_text_search($search_text, $options = array()) {

        $search_text = str_replace("\&#039;", "'", $search_text);
        $search_text = str_replace("\&quot;", "\"", $search_text);
        $search_text = str_replace("\(", "\\\(", $search_text);
        $search_text = str_replace("\)", "\\\)", $search_text);

        if (empty($options)) {
            $options = $this->data_fields();
        }

        if (isset($options['use_cache']) && (int) $options['use_cache'] == 1) {
            $options['use_cache'] = true;
        } else {
            $options['use_cache'] = false;
        }

        $taxonomies = '';
        $opposition_terms = array();
        $tax_query = array();

        $data = woof()->get_request_data();

        $search = false;

        if (woof()->is_isset_in_request_data(woof()->get_swoof_search_slug()) && count($data) > 1) {
            $search = true;
        }
        $tax_query = woof()->get_tax_query($taxonomies);
        $tax_query = woof()->product_visibility_not_in($tax_query, woof()->generate_visibility_keys($search));

        //current taxonomy
        if (woof()->is_really_current_term_exists()) {
            //we need this when for dynamic recount on taxonomy page
            $o = woof()->get_really_current_term();
            $opposition_terms[$o->taxonomy] = array($o->slug);
        }
        if (!empty($opposition_terms)) {
            foreach ($opposition_terms as $tax_slug => $terms) {
                if (!empty($terms)) {
                    $tax_query[] = array(
                        'taxonomy' => $tax_slug,
                        'terms' => $terms,
                        'field' => 'slug',
                        'operator' => 'IN',
                        'include_children' => true
                    );
                }
            }
        }


        $args = array(
            'fields' => 'ids',
            'post_type' => array('product'),
            'post_status' => 'publish',
            'tax_query' => $tax_query,
            'posts_per_page' => -1
        );

        if (isset($options['page']) && isset($options['max_posts'])) {
            $args['posts_per_page'] = (int) $options['max_posts'];
            $args['paged'] = (int) $options['page'];
        }


        $args['meta_query'] = woof()->get_meta_query();
        $tax_relations = apply_filters('woof_main_query_tax_relations', array());
        if (!empty($tax_relations)) {
            $tax_query = $args['tax_query'];
            foreach ($tax_query as $key => $value) {
                if (isset($value['taxonomy'])) {
                    if (in_array($value['taxonomy'], array_keys($tax_relations))) {
                        if (count($tax_query[$key]['terms'])) {
                            $tax_query[$key]['operator'] = $tax_relations[$value['taxonomy']];
                            $tax_query[$key]['include_children'] = 0;
                        }
                    }
                }
            }

            $args['tax_query'] = $tax_query;
        }
        $args['woof_text_filter'] = $search_text;

        $products = false;
        //cache
        if ($options['use_cache']) {
            $key = $this->cache->create_key($args);
            $products = $this->cache->get($key);
        }


        if (!$products) {
            $products = new WP_Query($args);
            if ($options['use_cache']) {
                $this->cache->set($key, $products);
            }
        }

        return $products;
    }

    //ajax
    public function ajax_search() {
        if (WOOF_REQUEST::isset('link')) {
            $link = parse_url(WOOF_REQUEST::get('link'), PHP_URL_QUERY);
            $query_array = WOOF_HELPER::safe_parse_str($link);
            $_GET = array_merge($query_array, wc_clean($_GET));
            $_GET = apply_filters('woof_draw_products_get_args', WOOF_HELPER::sanitize_array($_GET), WOOF_REQUEST::get('link'));
        }

        if (WOOF_REQUEST::isset('cur_tax')) {
            $_GET['really_curr_tax'] = WOOF_REQUEST::get('cur_tax');
        }

        $options = [];
        $search_text = sanitize_text_field(WOOF_REQUEST::get('value'));
        $this->options = array_merge($this->options, $this->data_fields(WOOF_REQUEST::get())); //sanitizing is inside

        if (!isset($this->options['page']) || !$this->options['page']) {
            $this->options['page'] = 0;
        }
        $this->options['page'] += 1;

        if (strlen($search_text) < $this->options['min_symbols']) {
            die(json_encode([]));
        }

        if ($this->options['max_posts'] < 0) {
            $this->options['max_posts'] = 10;
        }
        //+++

        $cache_key = null;
        $res = false;

        $query = $this->init_text_search($search_text, $this->options);
        $products = $query->posts;
        $found = $query->found_posts;
        $res = $products;

        $template = 'default';
        if (isset($this->options['template']) && !empty($this->options['template'])) {
            $template = $this->options['template'];
        }

        $path = $this->get_ext_path() . "views/templates/{$template}.php"; //templates inside the plugin

        if (!file_exists($path)) {
            //templates outside the plugin
            $path = apply_filters('woof_husky_txt_templates', $template);
            if (!file_exists($path)) {
                $path = '';

                $path = get_stylesheet_directory() . DIRECTORY_SEPARATOR . "woof" . DIRECTORY_SEPARATOR . "ext" .
                        DIRECTORY_SEPARATOR . $this->html_type . DIRECTORY_SEPARATOR .
                        "views" . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $template . ".php";
                $T = $path;
                if (!file_exists($path)) {
                    $path = '';
                }
            }
        }

        //+++

        if (!empty($res)) {
            foreach ($res as $post_id) {
                $excerpt = get_the_excerpt($post_id);

                $data = [
                    'options' => $this->options,
                    'id' => $post_id,
                    'title' => get_the_title($post_id),
                    'permalink' => get_permalink($post_id),
                    'thumbnail' => $this->get_thumbnail($post_id),
                    'excerpt' => $this->options['view_text_length'] > 0 ? wp_trim_words($excerpt, $this->options['view_text_length']) : wp_trim_words($excerpt, 5),
                    'breadcrumb' => $this->get_breadcrumb($post_id),
                    'labels' => $this->get_labels($post_id)
                ];

                $data['options']['click_target'] = (isset($data['options']['how_to_open_links']) && (int) $data['options']['how_to_open_links']) ? '_self' : '_blank';
                if (!empty($path)) {
                    $options[] = apply_filters('woof_husky_txt_option', $this->render_html($path, $data), $data);
                } else {
                    //if no template lets anyway show something
                    $options[] = '<div class="woof_husky_txt-option-title">'
                            . '<a href="' . $data['permalink'] . '" target="' . $data['options']['click_target'] . '">' . $data['title'] . '</a></div>'
                            . '<div class="woof_husky_txt-option-text">&nbsp;' . __('Wrong path to template', 'woocommerce-products-filter') . $this->get_ext_path() . "views/templates/{$template}.php" . '</div>';
                }
            }
        }

        $result = [
            'options' => $options,
            'pagination' => [
                'pages' => ceil($found / $this->options['max_posts']),
                'page' => $this->options['page']
            ]
        ];

        $result['test'] = WOOF_HELPER::sanitize_array($_GET);

        die(json_encode($result));
    }

    private function get_labels($post_id) {
        $labels = [];

        if (!empty($this->options['for_labels'])) {
            foreach ($this->options['for_labels'] as $label => $ids) {
                if (in_array($post_id, $ids)) {
                    $labels[] = $label;
                }
            }
        }

        return apply_filters('woof_text_filter_labels', $labels);
    }

}

WOOF_EXT::$includes['html_type_objects']['by_text'] = new WOOF_EXT_BY_TEXT();
