<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_HELPER {

    static $notices = array();

    //log test data while makes debbuging
    public static function log($string) {
        $handle = fopen(WOOF_PATH . 'log.txt', 'a+');
        $string .= PHP_EOL;
        fwrite($handle, $string);
        fclose($handle);
    }

    public static function get_var_size(&$var) {
        if (is_string($var)) {
            return strlen($var);
        } elseif (is_array($var)) {
            $size = 0;
            foreach ($var as $k => $v) {
                $size += strlen($k) + getsize($v);
            }
            return $size;
        } else {
            return 1;
        }
    }

    public static function escape($value, $echo = false) {
        if (is_string($value)) {
            if ($echo) {
                echo sanitize_text_field(esc_html($value));
            } else {
                return sanitize_text_field(esc_html($value));
            }
        }
    }

    public static function get_server_var($var) {
        return self::escape($_SERVER[$var]);
    }

    public static function safe_parse_str($link) {
        $query_array = [];
		if ($link) {
			parse_str($link, $query_array);
		}
        return self::sanitize_array($query_array);
    }

    public static function sanitize_array($array) {
        $is_html = array('override_no_products');
        $is_textarea = array('init_only_on', 'custom_css_code', 'js_after_ajax_done');
        $is_js = array();

        if (is_array($array) && !empty($array)) {
            foreach ($array as $key => $data) {
                if (is_array($data)) {
                    self::sanitize_array($data);
                } else {
                    $key = sanitize_text_field($key);
                    if (in_array($key, $is_html)) {
                        $array[$key] = wp_kses_post(wp_unslash($data));
                    } elseif (in_array($key, $is_textarea)) {
                        $array[$key] = sanitize_textarea_field($data);
                    } else {
                        $array[$key] = sanitize_text_field($data);
                    }
                }
            }
        }

        return $array;
    }

    public static function sanitize_html_fields_array($fiels) {
        $array = array();
        foreach ($fiels as $key => $data) {
            $key = sanitize_text_field($key);
            $array[$key] = wp_kses($data, wp_kses_allowed_html('post'));
        }
        return $array;
    }

    public static function parse_ext_data($file_path) {
        $info = array();
        if (is_file($file_path)) {
            if (function_exists('parse_ini_file')) {
                $info = parse_ini_file($file_path);
            }

            if (empty($info)) {
                $array = file($file_path);
                foreach ($array as $val) {
                    if (preg_match("#^([^=]*)=([^=]*)$#isU", $val)) {
                        list($key, $value) = explode("=", trim($val));
                        $info[trim($key)] = trim($value, ' "');
                    }
                }
            }
        }
        return $info;
    }

    //for multi language strtolower
    //http://stackoverflow.com/questions/13288785/mb-strtolower-and-utf8-strings
    public static function strtolower($string) {
        if (function_exists('mb_strtolower')) {
            $string = mb_strtolower($string, 'UTF-8');
        } else {
            $string = strtolower($string);
        }

        return $string;
    }

    public static function get_terms($taxonomy, $hide_empty = true, $get_childs = true, $selected = 0, $category_parent = 0) {
        static $collector = array();

        $lang_key = ""; //WPML compatibility
        if (class_exists('SitePress')) {
            // $lang_key = ICL_LANGUAGE_CODE;
            $lang_key = apply_filters('wpml_current_language', NULL);
        }

        if (isset($collector[$taxonomy])) {
            return $collector[$taxonomy];
        }

        //***
        if (isset(woof()->settings['cache_terms']) AND woof()->settings['cache_terms'] == 1) {
            $cache_key = 'woof_terms_cache_' . md5($taxonomy . '-' . (int) $hide_empty . '-' . (int) $get_childs . '-' . (int) $selected . '-' . (int) $category_parent . '-' . $lang_key);
            if (false !== ( $cats = get_transient($cache_key) )) {
                return $cats;
            }
        }
        //***    

        $args = array(
            'style' => 'list',
            'show_count' => 0,
            'hide_empty' => $hide_empty,
            'use_desc_for_title' => 1,
            'child_of' => 0,
            'hierarchical' => true,
            'title_li' => '',
            'show_option_none' => '',
            'number' => '',
            'echo' => 0,
            'depth' => 0,
            'current_category' => $selected,
            'pad_counts' => 0,
            'taxonomy' => $taxonomy,
            'walker' => 'Walker_Category');

        //***

        $orderby = apply_filters('woof_get_terms_orderby', $taxonomy);
        if (!empty($orderby)) {
            if ($orderby != $taxonomy) {
                $args['orderby'] = $orderby;
            }
        }
        $order = apply_filters('woof_get_terms_order', $taxonomy, $orderby);
        if (!empty($order)) {
            if ($order != $taxonomy) {
                $args['order'] = $order;
            }
        }

        //WPML compatibility
        if (class_exists('SitePress')) {
            //$args['lang'] = ICL_LANGUAGE_CODE;
            $args['lang'] = apply_filters('wpml_current_language', NULL);
        }

        $args = apply_filters('woof_get_terms_args', $args);

        $cats_objects = get_categories($args);

        $cats = array();
        if (!empty($cats_objects)) {
            foreach ($cats_objects as $value) {
                if (is_object($value) AND $value->category_parent == $category_parent) {
                    $cats[$value->term_id] = array();
                    $cats[$value->term_id]['term_id'] = $value->term_id;

                    //non_latin_mode
                    $cats[$value->term_id]['slug'] = urldecode($value->slug);
                    $cats[$value->term_id]['taxonomy'] = urldecode($value->taxonomy);

                    $cats[$value->term_id]['name'] = $value->name;
                    $cats[$value->term_id]['count'] = $value->count;
                    $cats[$value->term_id]['parent'] = $value->parent;
                    if ($get_childs) {
                        $cats[$value->term_id]['childs'] = self::assemble_terms_childs($cats_objects, $value->term_id);
                    }
                }
            }
        }

        //***
        if (isset(woof()->settings['cache_terms']) AND woof()->settings['cache_terms'] == 1) {
            $period = 0;

            $periods = array(
                0 => 0,
                'hourly' => HOUR_IN_SECONDS,
                'twicedaily' => 12 * HOUR_IN_SECONDS,
                'daily' => DAY_IN_SECONDS,
                'days2' => 2 * DAY_IN_SECONDS,
                'days3' => 3 * DAY_IN_SECONDS,
                'days4' => 4 * DAY_IN_SECONDS,
                'days5' => 5 * DAY_IN_SECONDS,
                'days6' => 6 * DAY_IN_SECONDS,
                'days7' => 7 * DAY_IN_SECONDS
            );

            if (isset(woof()->settings['cache_terms_auto_clean'])) {
                $period = woof()->settings['cache_terms_auto_clean'];

                if (!$period) {
                    $period = 0;
                }
            }

            set_transient($cache_key, $cats, $periods[$period]);
        }
        //***

        $collector[$taxonomy] = $cats;
        return $cats;
    }

    //just for get_terms
    private static function assemble_terms_childs($cats_objects, $parent_id) {
        $res = array();
        foreach ($cats_objects as $value) {
            if ($value->category_parent == $parent_id) {
                $res[$value->term_id]['term_id'] = $value->term_id;
                $res[$value->term_id]['name'] = $value->name;

                //non_latin_mode
                $res[$value->term_id]['slug'] = urldecode($value->slug);
                $res[$value->term_id]['taxonomy'] = urldecode($value->taxonomy);

                $res[$value->term_id]['count'] = $value->count;
                $res[$value->term_id]['parent'] = $value->parent;
                $res[$value->term_id]['childs'] = self::assemble_terms_childs($cats_objects, $value->term_id);
            }
        }

        return $res;
    }

    //https://wordpress.org/support/topic/translated-label-with-wpml
    //for taxonomies labels translations
    public static function wpml_translate($taxonomy_info, $string = '', $index = -1) {
        if (empty($string)) {
            if (is_object($taxonomy_info) && isset(woof()->settings['custom_tax_label'])) {
                $string = stripcslashes(woof()->settings['custom_tax_label'][$taxonomy_info->name]);
            }
        }


        if (empty($string)) {
            if (is_object($taxonomy_info)) {
                //fix for WPML
                if (isset($taxonomy_info->labels->name) AND!empty($taxonomy_info->labels->name)) {
                    $string = $taxonomy_info->labels->name;
                } else {
                    $string = $taxonomy_info->label;
                }
            }
        }


        //***
        $check_for_custom_label = false;
        if (class_exists('SitePress') OR class_exists("Polylang")) {
            if (class_exists('SitePress')) {
                //$lang = ICL_LANGUAGE_CODE;
                $lang = apply_filters('wpml_current_language', NULL);
            }
            if (class_exists('Polylang')) {
                $lang = get_locale();
            }
            $woof_settings = get_option('woof_settings');
            if (isset($woof_settings['wpml_tax_labels']) AND!empty($woof_settings['wpml_tax_labels'])) {
                $translations = $woof_settings['wpml_tax_labels'];

                if (isset($translations[$lang])) {
                    foreach ($translations[$lang] as $k => $data) {
                        $translations[$lang][self::strtolower($k)] = $data;
                    }
                    if (isset($translations[$lang][self::strtolower(trim($string))])) {
                        $string = $translations[$lang][self::strtolower(trim($string))];
                    } else {
                        $check_for_custom_label = TRUE;
                    }
                } else {
                    $check_for_custom_label = TRUE;
                }
            } else {
                $check_for_custom_label = TRUE;
            }
        }

        //+++
        if (empty($string)) {
            $check_for_custom_label = FALSE;
        }


        //for hierarchy titles type of: name1+name2+name3^Title
        if ($index != -1) {
            if (stripos($string, '+')) {
                $tmp = explode('+', $string);
                if (isset($tmp[$index])) {
                    $string = explode('^', $tmp[$index]);
                    $string = $string[0];
                }
            }
        }

        return $string;
    }

    public static function price_filter_e($additional_taxes = "", $min = null, $max = null) {
        global $_chosen_attributes, $wpdb, $wp, $WOOF;
        $request = woof()->get_request_data();
        if (!isset($additional_taxes)) {
            $additional_taxes = "";
        }
        if ($min === null && $max === null) {
            $min = self::get_min_price($additional_taxes);
            $max = self::get_max_price($additional_taxes);
        }

        $min_price = woof()->is_isset_in_request_data('min_price') ? esc_attr($request['min_price']) : '';
        $max_price = woof()->is_isset_in_request_data('max_price') ? esc_attr($request['max_price']) : '';
        if (wc_tax_enabled() && 'incl' === get_option('woocommerce_tax_display_shop') && !wc_prices_include_tax()) {
            $tax_classes = array_merge(array(''), WC_Tax::get_tax_classes());
            $class_max = $max;
            $class_min = $min;
            foreach ($tax_classes as $tax_class) {
                if ($tax_rates = WC_Tax::get_rates($tax_class)) {
                    $class_max = ceil($max + WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($max, $tax_rates)));
                    $class_min = floor($min + WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($min, $tax_rates)));
                }
            }
            $min = $class_min;
            $max = $class_max;
        }

        //***
        $min_price = ($min_price) ?: $min;
        $max_price = ($max_price) ?: $max;

        if ($min == $max) {
            echo esc_attr("");
            return;
        }
        if ('' == get_option('permalink_structure')) {
            $form_action = remove_query_arg(array('page', 'paged'), add_query_arg($wp->query_string, '', home_url($wp->request)));
        } else {
            $form_action = preg_replace('%\/page/[0-9]+%', '', home_url(trailingslashit($wp->request)));
        }
        ?>

        <form method="get" action="<?php echo esc_attr($form_action); ?>">
            <div class="price_slider_wrapper">
                <div class="price_slider" style="display:none;"></div>
                <div class="price_slider_amount">
                    <input type="text" id="min_price" name="min_price" value="<?php echo esc_attr(apply_filters('woocommerce_price_filter_widget_amount', $min_price)) ?>" data-min="<?php echo esc_attr(apply_filters('woocommerce_price_filter_widget_amount', $min)) ?>" placeholder="<?php esc_html_e('Min price', 'woocommerce-products-filter'); ?>" />
                    <input type="text" id="max_price" name="max_price" value="<?php echo esc_attr(apply_filters('woocommerce_price_filter_widget_amount', $max_price)) ?>" data-max="<?php echo esc_attr(apply_filters('woocommerce_price_filter_widget_amount', $max)) ?>" placeholder="<?php esc_html_e('Max price', 'woocommerce-products-filter'); ?>" />
                    <button type="submit" class="button"><?php esc_html_e('Filter', 'woocommerce-products-filter') ?></button>
                    <div class="price_label" style="display:none;">
                        <?php esc_html_e('Price:', 'woocommerce-products-filter') ?> <span class="from"></span> &mdash; <span class="to"></span>
                    </div>
                    <?php if (get_search_query()) { ?>
                        <input type="hidden" name="s" value="<?php echo esc_attr(get_search_query()) ?>" />
                    <?php } ?>
                    <?php if (isset($_GET['post_type']) && !empty($_GET['post_type'])) { ?>
                        <input type="hidden" name="post_type" value="<?php echo esc_attr(sanitize_key($_GET['post_type'])); ?>" />
                    <?php } ?>	

                    <?php if (isset($_GET['product_cat']) && !empty($_GET['product_cat'])) { ?>
                        <input type="hidden" name="product_cat" value="<?php echo esc_attr(sanitize_key($_GET['product_cat'])); ?>" />
                    <?php } ?>

                    <?php if (isset($_GET['product_tag']) && !empty($_GET['product_tag'])) { ?>
                        <input type="hidden" name="product_tag" value="<?php echo esc_attr(sanitize_key($_GET['product_tag'])); ?>" />
                    <?php } ?>	

                    <?php if (isset($_GET['orderby']) && !empty($_GET['orderby'])) { ?>
                        <input type="hidden" name="orderby" value="<?php echo esc_attr(sanitize_key($_GET['orderby'])); ?>" />
                    <?php } ?>
                    <?php
                    if ($_chosen_attributes) {
                        foreach ($_chosen_attributes as $attribute => $data) {
                            $taxonomy_filter = 'filter_' . str_replace('pa_', '', $attribute);
                            ?>
                            <input type="hidden" name="<?php echo esc_attr($taxonomy_filter); ?>" value="<?php echo esc_attr(implode(',', $data['terms'])); ?>" />
                            <?php if ('or' == $data['query_type']) { ?>
                                <input type="hidden" name="<?php echo esc_attr(str_replace('pa_', 'query_type_', $attribute)); ?>" value="or" />
                                <?php
                            }
                        }
                    }
                    ?>

                    <div class="clear"></div>
                </div>
            </div>	
        </form>	
        <?php
    }

    //drawing of native woo price filter
    //don't use it
    public static function price_filter($additional_taxes = "") {
        global $_chosen_attributes, $wpdb, $wp, $WOOF;
        $request = woof()->get_request_data();

        $min_price = woof()->is_isset_in_request_data('min_price') ? esc_attr($request['min_price']) : '';
        $max_price = woof()->is_isset_in_request_data('max_price') ? esc_attr($request['max_price']) : '';

        // Remember current filters/search
        $fields = '';

        if (get_search_query()) {
            $fields .= '<input type="hidden" name="s" value="' . get_search_query() . '" />';
        }

        if (!empty($_GET['post_type'])) {
            $fields .= '<input type="hidden" name="post_type" value="' . esc_attr(sanitize_key($_GET['post_type'])) . '" />';
        }

        if (!empty($_GET['product_cat'])) {
            $fields .= '<input type="hidden" name="product_cat" value="' . esc_attr(sanitize_key($_GET['product_cat'])) . '" />';
        }

        if (!empty($_GET['product_tag'])) {
            $fields .= '<input type="hidden" name="product_tag" value="' . esc_attr(sanitize_key($_GET['product_tag'])) . '" />';
        }

        if (!empty($_GET['orderby'])) {
            $fields .= '<input type="hidden" name="orderby" value="' . esc_attr(sanitize_key($_GET['orderby'])) . '" />';
        }

        if ($_chosen_attributes) {
            foreach ($_chosen_attributes as $attribute => $data) {
                $taxonomy_filter = 'filter_' . str_replace('pa_', '', $attribute);

                $fields .= '<input type="hidden" name="' . sanitize_text_field($taxonomy_filter) . '" value="' . sanitize_text_field(implode(',', $data['terms'])) . '" />';

                if ('or' == $data['query_type']) {
                    $fields .= '<input type="hidden" name="' . sanitize_text_field(str_replace('pa_', 'query_type_', $attribute)) . '" value="or" />';
                }
            }
        }
        if (!isset($additional_taxes)) {
            $additional_taxes = "";
        }

        //***
        $min = self::get_min_price($additional_taxes);
        $max = self::get_max_price($additional_taxes);

        if (wc_tax_enabled() && 'incl' === get_option('woocommerce_tax_display_shop') && !wc_prices_include_tax()) {
            $tax_classes = array_merge(array(''), WC_Tax::get_tax_classes());
            $class_max = $max;
            $class_min = $min;
            foreach ($tax_classes as $tax_class) {
                if ($tax_rates = WC_Tax::get_rates($tax_class)) {
                    $class_max = ceil($max + WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($max, $tax_rates)));
                    $class_min = floor($min + WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($min, $tax_rates)));
                }
            }
            $min = $class_min;
            $max = $class_max;
        }

        //***
        $min_price = ($min_price) ?: $min;
        $max_price = ($max_price) ?: $max;

        if ($min == $max) {
            echo esc_attr("");
            return;
        }


        if ('' == get_option('permalink_structure')) {
            $form_action = remove_query_arg(array('page', 'paged'), add_query_arg($wp->query_string, '', home_url($wp->request)));
        } else {
            $form_action = preg_replace('%\/page/[0-9]+%', '', home_url(trailingslashit($wp->request)));
        }

        $price_slider_html = '<form method="get" action="' . sanitize_text_field($form_action) . '">
			<div class="price_slider_wrapper">
				<div class="price_slider" style="display:none;"></div>
				<div class="price_slider_amount">
					<input type="text" id="min_price" name="min_price" value="' . sanitize_text_field(apply_filters('woocommerce_price_filter_widget_amount', $min_price)) . '" data-min="' . sanitize_text_field(apply_filters('woocommerce_price_filter_widget_amount', $min)) . '" placeholder="' . esc_html__('Min price', 'woocommerce-products-filter') . '" />
					<input type="text" id="max_price" name="max_price" value="' . sanitize_text_field(apply_filters('woocommerce_price_filter_widget_amount', $max_price)) . '" data-max="' . sanitize_text_field(apply_filters('woocommerce_price_filter_widget_amount', $max)) . '" placeholder="' . esc_html__('Max price', 'woocommerce-products-filter') . '" />
					<button type="submit" class="button">' . esc_html__('Filter', 'woocommerce-products-filter') . '</button>
					<div class="price_label" style="display:none;">
						' . esc_html__('Price:', 'woocommerce-products-filter') . ' <span class="from"></span> &mdash; <span class="to"></span>
					</div>
					' . $fields . '
					<div class="clear"></div>
				</div>
			</div>
		</form>';

        $price_slider_data = array(
            'form_action' => sanitize_text_field($form_action),
            'min_price' => sanitize_text_field($min_price),
            'max_price' => sanitize_text_field($max_price),
            'fields' => $fields
        );

        echo wp_kses_post(apply_filters('woof_price_slider_html', $price_slider_html, $price_slider_data));
    }

    //for drop-down price filter
    public static function get_price2_filter_data($additional_taxes = '') {
        $woof_settings = get_option('woof_settings', array());
        if (isset($woof_settings['by_price']['ranges']) AND!empty($woof_settings['by_price']['ranges'])) {

            $res = array();
            $request = woof()->get_request_data();
            $res['selected'] = '';
            if (isset($request['min_price']) AND isset($request['max_price'])) {
                $res['selected'] = $request['min_price'] . '-' . $request['max_price'];
            }

            //+++
            $r = array(); //drop-doen options
            $rc = array(); //count of items
            $ranges = explode(',', trim($woof_settings['by_price']['ranges']));
            $get = $request;
            $max = self::get_max_price();
            $show_count = get_option('woof_show_count', 0);
            foreach ($ranges as $value) {
                $key = str_replace('i', $max, $value);
                //+++
                $tmp = explode('-', trim($value));
                $wc_price_args = array();

                if (class_exists('WOOCS')) {
                    $tmp[0] = apply_filters('woocs_exchange_value', $tmp[0]);
                }

                $tmp[0] = wc_price(floatval($tmp[0]), $wc_price_args);

                if ($tmp[1] != 'i') {
                    if (class_exists('WOOCS')) {
                        $tmp[1] = apply_filters('woocs_exchange_value', $tmp[1]);
                    }
                    $tmp[1] = wc_price(floatval($tmp[1]), $wc_price_args);
                } else {
                    $tmp[1] = '&#8734;';
                }
                //just note to avoid
                $value = $tmp[0] . ' - ' . $tmp[1];
                //***
                $v = explode('-', $key);
                $_GET['min_price'] = floatval($v[0]);
                $_GET['max_price'] = floatval($v[1]);
                //***
                if ($v[0] >= $v[1]) {
                    continue;
                }
                //***
                $r[$key] = $value;
                if ($show_count) {
                    $rc[$key] = woof()->dynamic_count(NULL, 'none', $additional_taxes);
                } else {
                    $rc[$key] = 0;
                }
            }
            $_GET = WOOF_HELPER::sanitize_array($get);
            $ranges['options'] = $r;
            $ranges['count'] = $rc;
            //+++
            $res['ranges'] = $ranges;
            return $res;
        }


        return array();
    }

    public static function set_layered_nav_product_ids() {
        //after update to woo 2.6.x this function can be removed
        if (WOOF_REQUEST::isset('woof_wp_query_ids')) {
            if ((defined('DOING_AJAX') && DOING_AJAX) AND WOOF_REQUEST::isset('predict_ids_and_continue')) {
                //for relevant recounting of price range on cat page in AJAX mode
                WC()->query->layered_nav_product_ids = WOOF_REQUEST::get('woof_wp_query_ids');
            }
        }
    }

    /**
     * Get filtered min price for current products.
     * @return int
     * woocommerce native function from 2.6.0 version - added by HUSKY author
     */
    //wp-content\plugins\woocommerce\includes\widgets\class-wc-widget-price-filter.php
    public static function get_filtered_price($additional_taxes = "") {
        global $wpdb, $wp_the_query, $WOOF;

        $args = $wp_the_query->query_vars;
        $tax_query = isset($args['tax_query']) ? $args['tax_query'] : array();

        if (is_object($wp_the_query->tax_query)) {
            $tax_query = $wp_the_query->tax_query->queries; //fix for cat page
        }
        $meta_query = isset($args['meta_query']) ? $args['meta_query'] : array();
        //++++    
        // fix  for  adapt slider in shortcode 
       // $tax_query = self::expand_additional_taxes_string($additional_taxes, $tax_query);
		$tax_query = woof()->get_tax_query($additional_taxes);
		
        $current_term = woof()->get_really_current_term();
        if (!empty($current_term)) {
            $tax_query[] = array(
                'taxonomy' => $current_term->taxonomy,
                'field' => 'slug', //id
                'terms' => $current_term->slug
            );
        }

        //  fix  if current query has more them  one taxonomy 
        $temp_arr = array();
        if (isset($args['taxonomy']) AND isset($args[$args['taxonomy']]) AND!empty($args[$args['taxonomy']])) {
            $temp_arr = explode(',', $args[$args['taxonomy']]);
            if (!$temp_arr OR count($temp_arr) < 1) {
                $temp_arr = array();
            }
        }

        if (!empty($args['taxonomy']) && !empty($args['term'])) {
            $tax_query[] = array(
                'taxonomy' => $args['taxonomy'],
                'terms' => (empty($temp_arr)) ? array($args['term']) : $temp_arr,
                'field' => 'slug',
            );
        }

        if (!empty($meta_query) AND is_array($meta_query)) {
            foreach ($meta_query as $key => $query) {
                if (!empty($query['price_filter']) || !empty($query['rating_filter'])) {
                    unset($meta_query[$key]);
                }
            }
        }
		//serialize($tax_query)();
        $meta_query = new WP_Meta_Query($meta_query);
        $tax_query = new WP_Tax_Query($tax_query);

        $meta_query_sql = $meta_query->get_sql('post', $wpdb->posts, 'ID');
        $tax_query_sql = $tax_query->get_sql($wpdb->posts, 'ID');

        $sql = "SELECT min( FLOOR( price_meta.meta_value + 0.0)  ) as min_price, max( CEILING( price_meta.meta_value + 0.0)  )as max_price FROM {$wpdb->posts} ";
        $sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'] . $meta_query_sql['join'];
        $sql .= " WHERE {$wpdb->posts}.post_type = 'product'
					AND {$wpdb->posts}.post_status = 'publish'
					AND price_meta.meta_key IN ('" . implode("','", array_map('esc_sql', apply_filters('woocommerce_price_filter_meta_keys', array('_price')))) . "')
					AND price_meta.meta_value > '' ";
        $sql .= $tax_query_sql['where'] . $meta_query_sql['where'];
        $sql = apply_filters('woof_get_filtered_price_query', $sql);

        if (isset(woof()->settings['price_transient']) AND woof()->settings['price_transient']) {
            $data_key = md5($sql . 'woof');
            $data = get_transient('woof_min_max_prices');

            if (!is_array($data)) {
                $data = array();
            }

            if (isset($data[$data_key])) {

                $value = $data[$data_key];
                return $value;
            }
            $prices = $wpdb->get_row($sql);
            $data[$data_key] = $prices;
            set_transient('woof_min_max_prices', $data, 1 * 24 * 3600); //1 day
        }

        $prices = $wpdb->get_row($sql);
        return $prices;
    }

    public static function get_max_price($additional_taxes = "") {
        global $wpdb;
        if (version_compare(WOOCOMMERCE_VERSION, '2.6', '>')) {
            $prices = self::get_filtered_price($additional_taxes);
            $max = ceil($prices->max_price?:0);
        } else {
            self::set_layered_nav_product_ids();
            if (0 === sizeof(WC()->query->layered_nav_product_ids)) {

                $sql_data = array(
                    array(
                        'val' => $wpdb->posts,
                        'type' => 'string',
                    ),
                    array(
                        'val' => $wpdb->postmeta,
                        'type' => 'string',
                    ),
                    array(
                        'val' => '_price',
                        'type' => 'string',
                    ),
                );
                $query_txt = self::woof_prepare('SELECT max(meta_value + 0) FROM %1$s LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE meta_key IN ("' . implode('","', apply_filters('woocommerce_price_filter_meta_keys', array('_price'))) . '")
				', $sql_data);
                $max = ceil($wpdb->get_var($query_txt));
            } else {
                $sql_data = array(
                    array(
                        'val' => $wpdb->posts,
                        'type' => 'string',
                    ),
                    array(
                        'val' => $wpdb->postmeta,
                        'type' => 'string',
                    ),
                );
                $max = ceil($wpdb->get_var(
                                self::woof_prepare('
					SELECT max(meta_value + 0)
					FROM %1$s
					LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE meta_key IN ("' . implode('","', apply_filters('woocommerce_price_filter_meta_keys', array('_price'))) . '")
					AND (
						%1$s.ID IN (' . implode(',', array_map('absint', WC()->query->layered_nav_product_ids)) . ')
						OR (
							%1$s.post_parent IN (' . implode(',', array_map('absint', WC()->query->layered_nav_product_ids)) . ')
							AND %1$s.post_parent != 0
						)
					)
				', $sql_data
                )));
            }
        }


        return $max;
    }

    public static function get_min_price($additional_taxes = "") {
        global $wpdb;

        if (version_compare(WOOCOMMERCE_VERSION, '2.6', '>')) {
            $prices = self::get_filtered_price($additional_taxes);
            $min = floor($prices->min_price?:0);
        } else {
            self::set_layered_nav_product_ids();
            if (0 === sizeof(WC()->query->layered_nav_product_ids)) {
                $sql_data = array(
                    array(
                        'val' => $wpdb->posts,
                        'type' => 'string',
                    ),
                    array(
                        'val' => $wpdb->postmeta,
                        'type' => 'string',
                    ),
                );
                $min = floor($wpdb->get_var(
                                self::woof_prepare('
					SELECT min(meta_value + 0)
					FROM %1$s
					LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE meta_key IN ("' . implode('","', apply_filters('woocommerce_price_filter_meta_keys', array('_price', '_min_variation_price'))) . '")
					AND meta_value != ""
				', $sql_data)
                ));
            } else {
                $sql_data = array(
                    array(
                        'val' => $wpdb->posts,
                        'type' => 'string',
                    ),
                    array(
                        'val' => $wpdb->postmeta,
                        'type' => 'string',
                    ),
                );
                $min = floor($wpdb->get_var(
                                self::woof_prepare('
					SELECT min(meta_value + 0)
					FROM %1$s
					LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
					WHERE meta_key IN ("' . implode('","', apply_filters('woocommerce_price_filter_meta_keys', array('_price', '_min_variation_price'))) . '")
					AND meta_value != ""
					AND (
						%1$s.ID IN (' . implode(',', array_map('absint', WC()->query->layered_nav_product_ids)) . ')
						OR (
							%1$s.post_parent IN (' . implode(',', array_map('absint', WC()->query->layered_nav_product_ids)) . ')
							AND %1$s.post_parent != 0
						)
					)
				', $sql_data
                )));
            }
        }


        return $min;
    }

    //is customer look the site from mobile device
    public static function is_mobile_device() {
        return wp_is_mobile();
    }

    public static function show_admin_notice($key) {

        woof()->render_html_e(WOOF_PATH . 'views/notices/' . $key . '.php');
    }

    public static function add_notice($key) {
        //update_option('woof_notices', array());
        $notices = get_option('woof_notices', array());
        $is_hidden = false;
        if (isset($notices[$key]) AND $notices[$key] == 'hidden') {
            $is_hidden = true;
        }

        if (!$is_hidden) {
            self::$notices[] = $key;
            $notices[] = $key;
            update_option('woof_notices', $notices);
        }
    }

    public static function hide_admin_notices() {
        if (isset($_GET['woof_hide_notice']) && isset($_GET['_wpnonce'])) {
            if (!wp_verify_nonce($_GET['_wpnonce'])) {
                wp_die(__('Action failed. Please refresh the page and retry.', 'woocommerce-products-filter'));
            }

            if (!current_user_can('manage_woocommerce')) {
                wp_die(__('Cheatin&#8217; huh?', 'woocommerce-products-filter'));
            }

            $key = self::escape($_GET['woof_hide_notice']);
            unset(self::$notices[$key]);
            $notices = get_option('woof_notices', array());
            $notices[$key] = 'hidden';
            update_option('woof_notices', $notices);
        }
    }

    public static function draw_tooltipe($title, $tooltip_text) {
        if (!$tooltip_text OR empty($tooltip_text) OR $tooltip_text == 'none') {
            return"";
        }

        if (!isset(woof()->settings['use_tooltip'])) {
            $show_tooltip = 1;
        } else {
            $show_tooltip = woof()->settings['use_tooltip'];
        }
        if (!$show_tooltip) {
            return"";
        }

        $tooltip_text = self::wpml_translate(null, stripcslashes(wp_strip_all_tags($tooltip_text)));
        $toggle_image = ((isset(woof()->settings['woof_tooltip_img']) AND!empty(woof()->settings['woof_tooltip_img'])) ? woof()->settings['woof_tooltip_img'] : WOOF_LINK . 'img/woof_info_icon.svg');
        $current_id = uniqid("woof_tooltip_content");
        ?>
        <img src="<?php echo esc_url($toggle_image) ?>" class="woof_tooltip_header" data-tooltip-content="#<?php echo esc_attr($current_id) ?>" alt="<?php esc_html_e('info', 'woocommerce-products-filter'); ?>">
        <span class="woof_tooltip_templates">
            <span id="<?php echo esc_attr($current_id) ?>">
                <span class="woof_tooltip_title"><?php echo wp_kses_post(wp_unslash($title)) ?></span>
                <span class="woof_tooltip_text"><?php echo wp_kses_post(wp_unslash($tooltip_text)) ?></span>
            </span>
        </span>          
        <?php
    }

    public static function draw_title_toggle($show, $block_is_closed) {
        if (!$show) {
            return "";
        }

        $condition = 'closed';
        $toggle_type = ((isset(woof()->settings['toggle_type']) AND!empty(woof()->settings['toggle_type'])) ? woof()->settings['toggle_type'] : 'text');

        if ($block_is_closed) {
            $toggle_text = ((isset(woof()->settings['toggle_closed_text']) AND!empty(woof()->settings['toggle_closed_text'])) ? self::wpml_translate(null, woof()->settings['toggle_closed_text']) : '+');
            $toggle_image = ((isset(woof()->settings['toggle_closed_image']) AND!empty(woof()->settings['toggle_closed_image'])) ? woof()->settings['toggle_closed_image'] : WOOF_LINK . 'img/plus.svg');
        } else {
            $toggle_text = ((isset(woof()->settings['toggle_opened_text']) AND!empty(woof()->settings['toggle_opened_text'])) ? self::wpml_translate(null, woof()->settings['toggle_opened_text']) : '-');
            $toggle_image = ((isset(woof()->settings['toggle_opened_image']) AND!empty(woof()->settings['toggle_opened_image'])) ? woof()->settings['toggle_opened_image'] : WOOF_LINK . 'img/minus.svg');
            $condition = 'opened';
        }

        if ($toggle_type == 'text' OR empty($toggle_image)) {
            ?>
            <a href="javascript: void(0);" title="<?php esc_html_e('toggle', 'woocommerce-products-filter') ?>" class="woof_front_toggle woof_front_toggle_<?php echo esc_attr($condition) ?>" data-condition="<?php echo esc_attr($condition) ?>"><?php esc_html_e($toggle_text) ?></a>
            <?php
        } else {
            ?>
            <a href="javascript: void(0);" title="<?php esc_html_e('toggle', 'woocommerce-products-filter') ?>" class="woof_front_toggle woof_front_toggle_<?php echo esc_attr($condition) ?>" data-condition="<?php echo esc_attr($condition) ?>">
                <img src="<?php echo esc_url($toggle_image) ?>" alt="<?php esc_html_e('toggle', 'woocommerce-products-filter') ?>" />
            </a>
            <?php
        }
    }

    //for checkboxes,radio,colors,labels,images
    public static function draw_more_less_button($type) {
        //$type - color,radio,checkbox,label,image
		
		$args = array(
			'type' => isset(woof()->settings['more_less_type'])? woof()->settings['more_less_type']: 'text',
		);
		if ('image' == $args['type']) {
			$args['closed'] = isset(woof()->settings['more_less_opened_image'])? woof()->settings['more_less_opened_image']: '';
            $args['opened'] = isset(woof()->settings['more_less_closed_image'])? woof()->settings['more_less_closed_image']: '';
			
			if (empty($args['closed']) || empty($args['opened'])) {
				$args['type'] = 'text';
			}
		}
		
		if ('text' == $args['type']) {
            $args['closed'] = isset(woof()->settings['more_less_opened_text']) && !empty(woof()->settings['more_less_opened_text'])? woof()->settings['more_less_opened_text']: esc_html__('Show more', 'woocommerce-products-filter');
            $args['opened'] = isset(woof()->settings['more_less_closed_text']) && !empty(woof()->settings['more_less_closed_text'])? woof()->settings['more_less_closed_text']: esc_html__('Show less', 'woocommerce-products-filter');
		
			//translate
			$args['closed'] = self::wpml_translate(null, $args['closed']);
			$args['opened'] = self::wpml_translate(null, $args['opened']);
		} 
		 
        $args = apply_filters('woof_get_more_less_button_' . $type, $args);

        if ($args['type'] == 'image') {
            ?>
            <a href="javascript:void(0);" class="woof_open_hidden_li_btn" data-type="<?php echo esc_attr($args['type']) ?>" data-state="closed" data-closed="<?php echo esc_attr($args['closed']) ?>" data-opened="<?php echo esc_attr($args['opened']) ?>"><img src="<?php echo esc_url($args['closed']) ?>" alt="" /></a>
            <?php
        } else {
            ?>
            <a href="javascript:void(0);" class="woof_open_hidden_li_btn" data-type="<?php echo esc_attr($args['type']) ?>" data-state="closed" data-closed="<?php echo esc_attr($args['closed']) ?>" data-opened="<?php echo esc_attr($args['opened']) ?>"><?php esc_html_e($args['closed']) ?></a>
            <?php
        }
    }

    public static function recurse_dirsize($directory, $exclude = '') {
        $size = 0;
        $directory = untrailingslashit($directory);
        //***
        if (!file_exists($directory) || !is_dir($directory) || !is_readable($directory) || $directory === $exclude) {
            return false;
        }
        //***
        if ($handle = opendir($directory)) {
            while (($file = readdir($handle)) !== false) {
                $path = $directory . '/' . $file;
                if ($file != '.' && $file != '..') {
                    if (is_file($path)) {
                        $size += filesize($path);
                    } elseif (is_dir($path)) {
                        $handlesize = self::recurse_dirsize($path, $exclude);
                        if ($handlesize > 0) {
                            $size += $handlesize;
                        }
                    }
                }
            }
            closedir($handle);
        }
        return $size;
    }

    public static function expand_additional_taxes_string($additional_taxes, $res = array()) {
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
                        if (is_object($term) AND!is_wp_error($term)) {
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

    public static function woof_prepare($query, $args) {
        if (is_null($query)) {
            return;
        }
        $sql_val = array();

        $query = str_replace("'%s'", '%s', $query); // in case someone mistakenly already singlequoted it
        $query = str_replace('"%s"', '%s', $query); // doublequote unquoting
        $query = preg_replace('|(?<!%)%f|', '%F', $query); // Force floats to be locale unaware
        $query = preg_replace('|(?<!%)%s|', "'%s'", $query); // quote the strings, avoiding escaped strings like %%s
        if (!is_array($args)) {
            $args = array('val' => $args, 'type' => 'string');
        }
        foreach ($args as $item) {

            if (!is_array($item) OR!isset($item['val'])) {
                continue;
            }
            if (!isset($item['type'])) {
                $item['type'] = 'string';
            }
            $sql_val[] = self::woof_escape_sql($item['type'], $item['val']);
        }
        return @vsprintf($query, $sql_val);
    }

    public static function woof_escape_sql($type, $value) {
        switch ($type) {
            case'string':
                global $wpdb;
                return $wpdb->_real_escape($value);
                break;
            case'int':
                return intval($value);
                break;
            case'float':
                return floatval($value);
                break;
            default :
                global $wpdb;
                return $wpdb->_real_escape($value);
        }
    }

    public static function recursiveRemoval(&$array, $val) {
        if (is_array($array)) {
            foreach ($array as $key => &$arrayElement) {
                if (isset($arrayElement['key']) AND $arrayElement['key'] == $val) {
                    unset($array[$key]);
                }
                if (is_array($arrayElement)) {
                    self::recursiveRemoval($arrayElement, $val);
                }
            }
        }
    }

    public static function check_new_ion_skin($skin) {
        $skins = array(
            'round' => 'Round',
            'flat' => 'skinFlat',
            'big' => 'skinHTML5',
            'modern' => 'skinModern',
            // 'skinSimple' => 'skinSimple',
            'sharp' => 'Sharp',
            'square' => 'Square',
        );

        //comp  with old  ion slider
        if (!isset($skins[$skin])) {

            if (array_search($skin, $skins) !== false) {
                $skin = array_search($skin, $skins);
            } else {
                $skin = 'round';
            }
        }

        return $skin;
    }

    public static function generate_html_item($type, $data, $content = '') {
        $item = '<' . $type;
        foreach ($data as $key => $value) {
            $item .= " {$key}='{$value}'";
        }

        if (!empty($content) OR in_array($type, array('textarea'))) {
            $item .= '>' . $content . "</{$type}>";
        } else {
            $item .= ' />';
        }

        return $item;
    }

}
