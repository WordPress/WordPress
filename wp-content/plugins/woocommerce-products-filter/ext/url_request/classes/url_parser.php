<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WOOF_URL_PARSER {

    private $current_request = array();
    private $filter_prefix = '';
    public $special_filters = [];

    public function __construct() {

        $this->init();
    }

    public function get_filter_prefix() {
        if (empty($this->filter_prefix)) {

            $this->filter_prefix = woof()->get_swoof_search_slug();
        }

        return $this->filter_prefix;
    }

    public function init() {

        $this->special_filters = array(
            'instock' => array('stock' => 'instock'),
            'onsale' => array('onsales' => 'salesonly'),
            'featured' => array('product_visibility' => 'featured'),
            'backorder_not_in' => array('backorder' => 'onbackorder'),
        );

        if (!is_admin()) {
            add_filter('do_parse_request', array($this, 'url_process'), 10, 3);
        }
        add_filter('woof_get_request_data', array($this, 'add_request'), 10);
        add_filter('woof_draw_products_get_args', array($this, 'ajax_draw_products'), 10, 2);
    }

    public function url_process($do, $WP, $extra_query_vars) {

        if (!$this->get_url_request()) {
            return $do;
        }

        global $wp_rewrite;
        $post_data = $this->get_post();
        $get_data = $this->get_get();

        $self = '';
        if (isset($_SERVER['PHP_SELF'])) {
            $self = WOOF_HELPER::get_server_var('PHP_SELF');
        }

        $WP->query_vars = [];
        $post_type_query_vars = [];

        if (is_array($extra_query_vars)) {
            $WP->extra_query_vars = & $extra_query_vars;
        } elseif (!empty($extra_query_vars)) {
            $WP->extra_query_vars = WOOF_HELPER::safe_parse_str($extra_query_vars);
        }
        //Source wp-includes/class-wp.php
        $rewrite_rules = $wp_rewrite->wp_rewrite_rules();

        if (!empty($rewrite_rules)) {
            $path_info = '';
            if (isset($_SERVER['PATH_INFO'])) {
                $path_info = WOOF_HELPER::get_server_var('PATH_INFO');
            }
            $error = '404';
            $WP->did_permalink = true;

            list( $path_info ) = explode('?', $path_info);
            $path_info = str_replace('%', '%25', $path_info);

            $cleared_url = $this->get_cleared_url($this->get_request_uri());

            list( $req_uri ) = explode('?', $cleared_url);
            $req_uri = str_replace($path_info, '', $req_uri);

            $req_uri = $this->check_url($req_uri);
            $path_info = $this->check_url($path_info);
            $self = $this->check_url($self);

            if (!empty($path_info) && !preg_match('|^.*' . $wp_rewrite->index . '$|', $path_info)) {
                $initial_path = $path_info;
            } else {

                if ($req_uri == $wp_rewrite->index) {
                    $req_uri = '';
                }
                $initial_path = $req_uri;
            }
            $requested_file = $req_uri;

            $WP->request = $initial_path;
            // Look for matches.
            $request_match = $initial_path;

            if (empty($request_match)) {

                if (isset($rewrite_rules['$'])) {
                    $WP->matched_rule = '$';
                    $query_var = $rewrite_rules['$'];
                    $matches = array('');
                } else {
                    $query_var = '';
                    $matches = array('');
                }
            } else {

                foreach ((array) $rewrite_rules as $match => $query_var) {
                    // If the requesting file is the anchor of the match, prepend it
                    // to the path info.
                    if (!empty($requested_file) && strpos($match, $requested_file) === 0 && $requested_file != $initial_path) {
                        $request_match = $requested_file . '/' . $initial_path;
                    }

                    if (preg_match("#^$match#", $request_match, $matches) ||
                            preg_match("#^$match#", urldecode($request_match), $matches)) {

                        if ($wp_rewrite->use_verbose_page_rules && preg_match('/pagename=\$matches\[([0-9]+)\]/', $query_var, $varmatch)) {
                            // This is a verbose page match, let's check to be sure about it.
                            $page = get_page_by_path($matches[$varmatch[1]]);
                            if (!$page) {
                                continue;
                            }

                            $post_status_obj = get_post_status_object($page->post_status);
                            if (!$post_status_obj->public && !$post_status_obj->protected && !$post_status_obj->private && $post_status_obj->exclude_from_search) {
                                continue;
                            }
                        }
                        $WP->matched_rule = $match;
                        break;
                    }
                }
            }

            if (isset($WP->matched_rule)) {

                // Got a match.
                // Trim the query of everything up to the '?'.				
                $query_var = preg_replace('!^.+\?!', '', $query_var);

                $query_var = addslashes(\WP_MatchesMapRegex::apply($query_var, $matches));

                $WP->matched_query = $query_var;
                // Filter out non-public query vars
                $perma_query_vars = WOOF_HELPER::safe_parse_str($query_var);
                // If we're processing a 404 request, clear the error var since we found something.
                if ('404' == $error) {
                    unset($error, $get_data['error']);
                }
            }
            // If req_uri is empty or if it is a request for ourself, unset error.
            if (empty($initial_path) || $requested_file == $self || strpos($self, 'wp-admin/') !== false) {
                unset($error, $get_data['error']);

                if (isset($perma_query_vars) && strpos($self, 'wp-admin/') !== false && (!isset($post_data['link']))) {
                    unset($perma_query_vars);
                }

                $WP->did_permalink = false;
            }
        }



        $WP->public_query_vars = apply_filters('query_vars', $WP->public_query_vars);

        foreach (get_post_types([], 'objects') as $post_type => $t) {
            if (is_post_type_viewable($t) && $t->query_var) {
                $post_type_query_vars[$t->query_var] = $post_type;
            }
        }

        foreach ($WP->public_query_vars as $wpvar) {
            if (isset($WP->extra_query_vars[$wpvar])) {
                $WP->query_vars[$wpvar] = $WP->extra_query_vars[$wpvar];
            } elseif (isset($get_data[$wpvar]) && isset($post_data[$wpvar]) && $get_data[$wpvar] !== $post_data[$wpvar]) {
                wp_die(esc_html__('Forbidden', 'woocommerce-products-filter'), 400);
            } elseif (isset($post_data[$wpvar])) {
                $WP->query_vars[$wpvar] = $post_data[$wpvar];
            } elseif (isset($get_data[$wpvar])) {
                $WP->query_vars[$wpvar] = $get_data[$wpvar];
            } elseif (isset($perma_query_vars[$wpvar])) {
                $WP->query_vars[$wpvar] = $perma_query_vars[$wpvar];
            }

            if (!empty($WP->query_vars[$wpvar])) {
                if (!is_array($WP->query_vars[$wpvar])) {
                    $WP->query_vars[$wpvar] = (string) $WP->query_vars[$wpvar];
                } else {
                    foreach ($WP->query_vars[$wpvar] as $vkey => $v) {
                        if (is_scalar($v)) {
                            $WP->query_vars[$wpvar][$vkey] = (string) $v;
                        }
                    }
                }

                if (isset($post_type_query_vars[$wpvar])) {
                    $WP->query_vars['post_type'] = $post_type_query_vars[$wpvar];
                    $WP->query_vars['name'] = $WP->query_vars[$wpvar];
                }
            }
        }
        // Convert urldecoded spaces back into '+'.
        foreach (get_taxonomies(['object_type' => 'product'], 'objects') as $taxonomy => $t) {

            if ($t->query_var && isset($WP->query_vars[$t->query_var])) {
                $WP->query_vars[$t->query_var] = str_replace(' ', '+', $WP->query_vars[$t->query_var]);
            }
        }
        // Don't allow non-publicly queryable taxonomies to be queried from the front end.
        if (!is_admin()) {
            foreach (get_taxonomies(array('object_type' => 'product', 'publicly_queryable' => false), 'objects') as $taxonomy => $t) {

                if (isset($WP->query_vars['taxonomy']) && $taxonomy === $WP->query_vars['taxonomy']) {
                    unset($WP->query_vars['taxonomy'], $WP->query_vars['term']);
                }
            }
        }
        // Limit publicly queried post_types to those that are 'publicly_queryable'.
        if (isset($WP->query_vars['post_type'])) {
            $queryable_post_types = get_post_types(array('publicly_queryable' => true));
            if (!is_array($WP->query_vars['post_type'])) {
                if (!in_array($WP->query_vars['post_type'], $queryable_post_types)) {
                    unset($WP->query_vars['post_type']);
                }
            } else {
                $WP->query_vars['post_type'] = array_intersect($WP->query_vars['post_type'], $queryable_post_types);
            }
        }

        $WP->query_vars = wp_resolve_numeric_slug_conflicts($WP->query_vars);

        foreach ((array) $WP->private_query_vars as $var) {
            if (isset($WP->extra_query_vars[$var])) {
                $WP->query_vars[$var] = $WP->extra_query_vars[$var];
            }
        }

        if (isset($error)) {
            $WP->query_vars['error'] = $error;
        }


        $WP->query_vars = apply_filters('request', $WP->query_vars);

        global $wp_version;

        if (version_compare($wp_version, '6.0') >= 0) {
            $WP->query_posts();
            $WP->handle_404();
            $WP->register_globals();
        } else {
            do_action_ref_array('parse_request', array(&$WP));
        }


        return false;
    }

    public function get_cleared_url($url) {
        $request_url = $this->get_url_request($url);

        $url = str_replace($request_url, '/', $url);
        return $url;
    }

    public function get_url_request($url = null) {
        if (!$url) {
            $url = $this->get_request_uri();
        }

        if (!$this->current_request) {
            $this->current_request = $this->get_search_request($url);
        }
        return $this->current_request;
    }

    public function check_url($url) {
        $home = parse_url(home_url(), PHP_URL_PATH);
        if ($home == null) {
            $home = '';
        }

        $home = trim($home, '/');
        $home_regex = sprintf('|^%s|i', preg_quote($home, '|'));

        $url = trim($url, '/');
        $url = preg_replace($home_regex, '', $url);
        $url = trim($url, '/');
        return $url;
    }

    public function get_search_request($url) {

        $cleared_url = $url;
        $clear_array = array('/page/', '?', '#');
        $request_url = "";
        foreach ($clear_array as $sign) {
            $tmp_url = explode($sign, $cleared_url, 2);
            $cleared_url = $tmp_url[0];
        }
        $filter_data = array();

        if ('/' != substr($cleared_url, -1)) {
            $cleared_url = $cleared_url . '/';
        }


        $pos = stripos($cleared_url, '/' . $this->get_filter_prefix() . '/');

        if (false !== $pos) {
            $request_url = substr($cleared_url, $pos);
        }


        return $request_url;
    }

    public function create_request($request) {
        $url = $this->get_url_request();
        if ($url) {
            $request = $request + $this->parse_url_query($url);
        }
        return $request;
    }

    public function get_all_items() {

        $settings = woof()->settings;
        $items_order = array();
        $all_items = array();
        $taxonomies = woof()->get_taxonomies();
        $taxonomies_keys = array_keys($taxonomies);
        if (isset($settings['items_order']) AND !empty($settings['items_order'])) {
            $items_order = explode(',', $settings['items_order']);
        } else {
            $items_order = array_merge(woof()->items_keys, $taxonomies_keys);
        }
        foreach (array_merge(woof()->items_keys, $taxonomies_keys) as $key) {
            if (!in_array($key, $items_order)) {
                $items_order[] = $key;
            }
        }

        $items_order = array_merge($items_order, array_keys($this->special_filters));
        $tax_relations = apply_filters('woof_main_query_tax_relations', array());

        foreach ($items_order as $f_key) {
            $f_real_keys = $f_key;
            if (isset($tax_relations[$f_real_keys]) AND $tax_relations[$f_real_keys] == 'NOT IN') {
                $f_real_keys = 'rev_' . $f_real_keys;
            }
            $f_key = preg_replace('/^pa_/', '', $f_key);

            switch ($f_key) {
                case 'by_price':
                    $f_key = 'price';
                    break;
                case 'by_rating':
                    unset($all_items[$f_key]);
                    $all_items['min_rating'] = 'min_rating';
                    break;
                case 'by_author':
                    unset($all_items[$f_key]);
                    $all_items['author'] = 'woof_author';
                    break;
                case 'by_sku':
                    unset($all_items[$f_key]);
                    $all_items['sku'] = 'woof_sku';
                    break;
                case 'by_text':
                case 'by_text_2':
                    unset($all_items[$f_key]);
                    $all_items['name'] = 'woof_text';
                    break;
            }
            if ($f_key == 'by_price') {
                $f_key = 'price';
            } elseif ($f_key == 'by_rating') {
                unset($all_items[$f_key]);
                $all_items['min_rating'] = 'min_rating';
            } elseif ($f_key == 'by_author') {
                unset($all_items[$f_key]);
                $all_items['author'] = 'woof_author';
            }
            $all_items[$f_key] = $f_real_keys; //to  do
        }

        return $all_items;
    }

    public function parse_url_query($url_query) {

        $filter_data = array();
        $all_filter_data = array();
        $request_array = explode('/', trim($url_query, '/'));

        if ($request_array[0] !== $this->get_filter_prefix()) {
            return $filter_data;
        }

        $filter_data[$this->get_filter_prefix()] = 1;

        $settings = woof()->settings;
        $taxonomies = woof()->get_taxonomies();
        $taxonomies_keys = array_keys($taxonomies);
        $all_items = $this->get_all_items();

        foreach ($request_array as $string) {

            foreach ($all_items as $f_key => $f_real_key) {

                $s_match = preg_replace('/^' . $f_key . '(-|$)/', '', $string);

                if ($s_match != $string) {


                    if (isset($taxonomies_keys[$f_real_key])) {
                        $separator = '-or-';
                        if (isset($settings['comparison_logic'][$f_real_key]) AND 'AND' == $settings['comparison_logic'][$f_real_key]) {
                            $separator = '-and-';
                        }
                        $separator = '-and-';
                        $terms_slug = explode($separator, $s_match);
                        $filter_data[$f_real_key] = implode(',', $terms_slug);
                    } elseif ($f_key == $string AND isset($this->special_filters[$f_key])) {

                        $filter_data = array_merge($filter_data, $this->special_filters[$f_key]);
                    } elseif ('price' == $f_key) {

                        $prices = explode('-to-', $s_match);

                        if (isset($prices[1])) {
                            $filter_data['min_price'] = $prices[0];
                            $filter_data['max_price'] = $prices[1];
                        }
                    } else {

                        $needle = array('-and-', '-or-', '-to-', '+');
                        $replase = array(',', ',', '^', ' ');
                        $s_match = urldecode($s_match);
                        if (isset($settings['meta_filter']) AND isset($settings['meta_filter'][$f_real_key])) {
                            $f_real_key = $settings['meta_filter'][$f_real_key]['search_view'] . '_' . $f_real_key;
                        }
                        $filter_data[$f_real_key] = str_replace($needle, $replase, $s_match);
                    }
                }
            }
        }

        return $filter_data;
    }

    public function get_request_uri() {
        $uri = false;
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = WOOF_HELPER::get_server_var('REQUEST_URI');
        }

        return apply_filters('woof_override_seo_request_uri', $uri);
    }

    public function get_post() {
        return WOOF_HELPER::sanitize_array($_POST);
    }

    public function get_get() {
        return WOOF_HELPER::sanitize_array($_GET);
    }

    public function add_request($data) {


        if (!is_array($data)) {
            $data = array();
        }
        $url = $this->get_url_request();
        $request = array();
        if ($url) {
            $request = $this->parse_url_query($url);
        }
        $data = array_merge($data, $request);

        if (isset($data['min_price'])) {
            $_GET['min_price'] = floatval($data['min_price']);
        }
        if (isset($data['max_price'])) {
            $_GET['max_price'] = floatval($data['max_price']);
        }

        return $data;
    }

    public function ajax_draw_products($get, $link) {
        $url = $this->get_url_request($link);
        $request = array();
        if ($url) {
            $request = $this->parse_url_query($url);
            $request[$this->get_filter_prefix()] = 1;
        }
        $get = array_merge($get, $request);
        $get[$this->get_filter_prefix()] = 1;
        return $get;
    }

}
