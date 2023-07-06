<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOBE_PRODUCTS {

    private $fields_keys = NULL;
    private $settings = NULL;
    private $storage = NULL;
    public $suppress_filters = false; //for example while do woobe_title_autocomplete we not need to apply another filters
    public $cached_products = array(); //products caching - for 400 percents quicker!!

    public function __construct($settings, $storage) {
        $this->settings = $settings;
        $this->storage = $storage;
        $this->fields_keys = $this->settings->get_fields_keys();
    }

    public function gets($args) {

        if (!isset($args['get_variations'])) {

            if (isset($args['order_by'])) {
                $order_by = sanitize_key($args['order_by']);
                if ($order_by === 'id') {
                    $order_by = 'ID';
                }
            } else {
                $order_by = 'ID';
            }

//***
//fix to avoid notice when seacrhing goig for crossels, upsells, etc ...
            if (!isset($args['offset'])) {
                $args['offset'] = 0;
            }

            $pr = array(
                'post_type' => isset($args['post_type']) ? $args['post_type'] : array('product'),
                'post_status' => isset($args['post_status']) ? $args['post_status'] : array_keys(apply_filters('woobe_product_statuses', get_post_statuses())),
                'orderby' => $order_by,
                'order' => isset($args['order']) ? sanitize_key($args['order']) : 'asc',
                'posts_per_page' => isset($args['per_page']) ? intval($args['per_page']) : 10,
                'paged' => isset($args['per_page']) ? intval(($args['offset'] / $args['per_page']) + 1) : 1
            );

//get one product data
            if (isset($args['p'])) {
                $pr['p'] = $args['p'];
            }

//***

            if (isset($args['nopaging'])) {
                $pr['nopaging'] = $args['nopaging'];
            }

            if (isset($args['max_num_pages'])) {
                $pr['max_num_pages'] = $args['max_num_pages'];
            }

            if (isset($args['post__not_in'])) {
                $pr['post__not_in'] = $args['post__not_in'];
            }

//***
//for bulk get product count
            if (isset($args['fields'])) {
                $pr['fields'] = $args['fields'];
            }

            if (isset($args['no_found_rows'])) {
                $pr['no_found_rows'] = $args['no_found_rows'];
                $pr['posts_per_page'] = -1;
                unset($pr['paged']);
            }

//***

            if (isset($this->settings->get_fields()[$order_by]) AND $this->settings->get_fields()[$order_by]['field_type'] === 'meta') {
                $pr['meta_key'] = $order_by;
                if (in_array($this->settings->get_fields()[$order_by]['type'], array('number', 'timestamp', 'unix'))) {
                    $pr['orderby'] = 'meta_value_num';
                } else {
                    $pr['orderby'] = 'meta_value';
                }
            }

            //WPML compatibility
            if (class_exists('SitePress') && isset($args['lang'])) {
                //https://wpml.org/forums/topic/passing-language-to-wp_query-2/
                global $sitepress;
                $sitepress->switch_lang($args['lang']);
            }
        } else {
            //get variations only
            $pr = array(
                'post_type' => array('product_variation'),
                'post_status' => array_keys(apply_filters('woobe_product_statuses', get_post_statuses())),
                'posts_per_page' => -1,
                'post_parent' => $args['get_variations'], //product parent id
                'orderby' => 'menu_order',
                'order' => 'ASC'
            );

            return new WP_Query($pr);
        }


        if (!$this->suppress_filters) {
			$pr = apply_filters('woobe_apply_query_filter_data', $pr);

            return new WP_Query($pr);
        } else {
            return new WP_Query($pr);
        }
    }

    public function update_page_field($product_id, $field_key, $value, $field_type = '', $additional_attr = array()) {

        if (!$product_id) {
            return FALSE;
        }

        $fields = $this->settings->get_fields();

        //***
        //lets check if current user is not administrator and is he can edit this field
        if (!$this->is_current_user_can_edit_field($field_key)) {
            return esc_html__('forbidden', 'woocommerce-bulk-editor');
        }

        //***

        $value = apply_filters('woobe_before_update_product_field', $value, $product_id, $field_key);

        $product = $this->get_product($product_id);

        $answer = '';
        if (empty($field_type)) {
            $field_type = $fields[$field_key]['field_type'];
        }

//***

        do_action('woobe_before_update_page_field', $field_key, $product_id, $product->get_parent_id());

//***

        if ($field_key == 'post_content') {
//fix for description of a variation
            if ($product->is_type('variation')) {
                $field_key = '_variation_description';
                $field_type = 'meta';
            }
        }
        if ($field_key == "attribute_visibility") {
            $field_type = 'meta';
        }


		
//***
        if (isset($fields[$field_key]['sanitize']) AND $fields[$field_key]['sanitize'] == 'array' AND!empty($value)) {

            $value = WOOBE_HELPER::string_to_array($value); //in db its keeps as array, so lets conver it
        }


        if (isset($_REQUEST['num_rounding']) AND $_REQUEST['num_rounding'] > 0 AND $fields[$field_key]['type'] === 'number') {
            if (isset($fields[$field_key]['sanitize']) AND $fields[$field_key]['sanitize'] === 'floatval') {
                $round_to = intval($_REQUEST['num_rounding']);
                $decimals = wc_get_price_decimals();
                $div = intval('1' . str_repeat('0', $decimals));

                if ($decimals > 0) {
                    switch ($round_to) {
                        case 100:
                            $value = round($value,0);
                            break;
                        case 5:
							$value = ceil($value / 0.05) * 0.05;
							break;
                        case 10:

							$value = ceil($value * 10) / 10;
							break;

                        case 9:
                        case 19:
                        case 29:
                        case 39:
                        case 49:
                        case 59:
                        case 69:
                        case 79:
                        case 89:
                        case 99:

                            $value = intval($value) + floatval($round_to / $div);

                        default:
                            break;
                    }
                }
            }
        }

//***

        if ($fields[$field_key]['type'] === 'number' AND isset($fields[$field_key]['sanitize']) AND $fields[$field_key]['sanitize'] === 'floatval') {
            $value = apply_filters('woobe_number_field_manipulation', $value, $field_key, $product_id);
        }


//***
//echo $field_type . ' + ' . $value . ' + ';
        switch ($field_type) {
            case 'meta':

                if ($field_key == "attribute_visibility") {

                    $meta = get_post_meta($product_id, '_product_attributes', true);
                    if (!is_array($meta)) {
                        $meta = array();
                    }
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    foreach ($meta as $pa_key => $item) {
                        if (in_array($item['name'], $value)) {
                            $meta[$pa_key]['is_visible'] = 1;
                        } else {
                            $meta[$pa_key]['is_visible'] = 0;
                        }
                    }

                    update_post_meta($product_id, '_product_attributes', $meta);
                    break;
                }
//				if ($field_key == "_thumbnail_id") {
//					set_post_thumbnail( $product_id, intval($value) );
//					break;
//				}
				if ($fields[$field_key]['edit_view'] == 'switcher') { //do switcher
					$value = WOOBE_HELPER::over_switcher_swicher_to_val($value, $field_key);
				}
                if (isset($_REQUEST['is_serialized']) AND $_REQUEST['is_serialized'] AND is_string($value)) {
                    parse_str($value, $value); //for serialized meta data saving
                }

                if ($fields[$field_key]['edit_view'] == 'meta_popup_editor') {
                    $value = $this->process_jsoned_meta_data($value);
                    $answer = json_encode($value, JSON_HEX_QUOT | JSON_HEX_TAG);
                } else {
                    $answer = $value;
                }
				if ($fields[$field_key]['edit_view'] == 'gallery_popup_editor') {

					$value = isset($value['woobe_gallery_images']) ? $value['woobe_gallery_images'] : array();
				}		
				
                update_post_meta($product_id, $field_key, $value);			
                $this->_call_hooks_after_product_update($product);
				
				if ($fields[$field_key]['edit_view'] == 'gallery_popup_editor') {
					$answer = WOOBE_HELPER::draw_gallery_popup_editor_btn($field_key, $product_id);
				}				
                break;

            case 'prop':
                //fix for stock_quantity + manage_stock
                if ($field_key == 'stock_quantity' AND $value > 0) {
                    if (apply_filters('woobe_stock_quantity_dependency', true)) {
                        $product->set_props(array(
                            'manage_stock' => 1
                        ));
                    }
                }


//fix IF sale_price > regular_price
                if ($field_key == 'sale_price' AND $value > 0) {
                    $rp = $product->get_regular_price();
                    if (floatval($value) >= floatval($rp)) {
                        $div = intval('1' . str_repeat('0', wc_get_price_decimals()));
                        $value = $rp - floatval(1 / $div);
                    }
                }

                if ($field_key == 'sale_price' OR $field_key == 'regular_price' AND $value > 0) {
                    $value = number_format(floatval($value), wc_get_price_decimals(), ".", "");
                    if ($field_key == 'sale_price' AND $value <= 0) {
                        $value = false;
                    }
                    // wpml price  sync
                    if (apply_filters('woobe_wpml_sync_prices', false) && function_exists('icl_object_id')) {
                        $icl_langs = icl_get_languages();
                        foreach ($icl_langs as $ln => $data) {
                            if ($icl_langs == $ln) {
                                continue;
                            }

                            $product_lcl_id = icl_object_id($product_id, 'post', false, $ln);
                            $product_lcl = $this->get_product($product_lcl_id);

                            if ($product_lcl) {
                                $product_lcl->set_props(array(
                                    $field_key => $value
                                ));
                                $product_lcl->save();
                            }
                        }
                    }
                }


//***
//$field_key - catalog_visibility for example
                if ($field_key == "purchase_note") {
                    $allowed_html = wp_kses_allowed_html('post');
                    $product->set_props(array(
                        $field_key => wp_kses($value, $allowed_html)
                    ));
				}elseif($field_key == "sku"){
					update_post_meta($product_id, '_sku', $value);
					$product->set_sku( $value );
				}else {					
					
					
                    $product->set_props(array(
                        $field_key => wc_clean($value)
                    ));								
					
//					if (function_exists('icl_object_id') && 'stock_quantity' == $field_key) {
//                        $icl_langs = icl_get_languages();
//                        foreach ($icl_langs as $ln => $data) {
//                            if ($icl_langs == $ln) {
//                                continue;
//                            }
//
//                            $product_lcl_id = icl_object_id($product_id, 'post', false, $ln);
//                            $product_lcl = $this->get_product($product_lcl_id);
//
//                            if ($product_lcl) {
//                                $product_lcl->set_props(array(
//									$field_key => wc_clean($value)
//								));
//                                $product_lcl->save();
//                            }
//                        }
//                    }
					
					
                }
				

                $product->save();

                $func_name = 'get_' . $field_key;
                if (method_exists($product, $func_name)) {
                    $answer = $product->$func_name();
                }

                break;

            case 'taxonomy':

                if ($fields[$field_key]['type'] === 'array') {
                    if (!is_array($value)) {
                        $value = array(intval($value));
                    } else {
                        foreach ($value as $k => $tid) {
                            $value[$k] = intval($tid);
                        }
                    }
                } else {
//string, do nothing
                }

                wp_set_post_terms($product_id, $value, $fields[$field_key]['taxonomy'], false);
                $this->_call_hooks_after_product_update($product);

                break;

            case 'attribute':
                $this->set_product_attributes($product_id, $field_key, $value, $additional_attr);

                $this->_call_hooks_after_product_update($product);
                break;

            case 'downloads':

                if (!is_array($value)) {
                    //from Product Editor
                    parse_str($value, $value);
                }

                $downloads = $this->prepare_downloads(
                        isset($value['_wc_file_names']) ? $value['_wc_file_names'] : array(), isset($value['_wc_file_urls']) ? $value['_wc_file_urls'] : array(), isset($value['_wc_file_hashes']) ? $value['_wc_file_hashes'] : array()
                );

//***
//for bulk editing operation
                if (isset($_REQUEST['action']) AND $_REQUEST['action'] === 'woobe_bulk_products') {

                    $woobe_bulk = $this->storage->get_val('woobe_bulk_' . WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']));
                    $behavior = $woobe_bulk['download_files']['behavior'];

                    if ($behavior !== 'new') {

                        $prev_downloads = $product->get_downloads();

//+++

                        switch ($behavior) {
                            case 'delete':
                                if (!empty($downloads)) {
                                    foreach ($downloads as $d) {
                                        foreach ($prev_downloads as $ked => $ed) {
                                            if ($ed->get_file() === $d['file']) {
                                                unset($prev_downloads[$ked]);
                                            }
                                        }
                                    }
                                }

                                $downloads = $prev_downloads;

                                break;
                            case 'add':
                                if (!empty($downloads)) {
                                    foreach ($downloads as $d) {
                                        $prev_downloads[] = $d;
                                    }

                                    $downloads = $prev_downloads;
                                }
                                break;
                            default:
//new
//do nothing
                                break;
                        }
                    }
                }

//***

                $product->set_downloads($downloads);
                $product->save();

                $answer = WOOBE_HELPER::draw_downloads_popup_editor_btn($field_key, $product->get_id());

                break;

            case 'gallery':

                if (!is_array($value)) {
//from Product Editor
                    parse_str($value, $value);
                }

                $value = isset($value['woobe_gallery_images']) ? $value['woobe_gallery_images'] : array();

//***
//for bulk editing operation
                if (isset($_REQUEST['action']) AND $_REQUEST['action'] === 'woobe_bulk_products') {

                    $woobe_bulk = $this->storage->get_val('woobe_bulk_' . WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']));
                    $behavior = $woobe_bulk['gallery']['behavior'];

                    if ($behavior !== 'new') {

                        $prev_ids = $product->get_gallery_image_ids();

                        switch ($behavior) {
                            case 'delete_forever':

                                if (!empty($value)) {
                                    foreach ($value as $attachment_id) {
                                        wp_delete_attachment($attachment_id, true);
                                    }
                                }

                                $value = array_diff($prev_ids, $value);

                                break;
                            case 'delete':

                                $value = array_diff($prev_ids, $value);

                                break;
                            case 'add':

                                $value = array_merge($prev_ids, $value);

                                break;

                            default:
//new
//do nothing
                                break;
                        }
                    }
                }

//***

                $product->set_gallery_image_ids($value);
                $product->save();

                $answer = WOOBE_HELPER::draw_gallery_popup_editor_btn($field_key, $product->get_id());

                break;

            case 'upsells':



                if (!is_array($value)) {
                    $value = str_replace("&amp;", "&", $value);
//from Product Editor
                    parse_str($value, $value);
                }

                $value = (isset($value['woobe_prod_ids'])) ? $value['woobe_prod_ids'] : array();

//***
//for bulk editing operation
                if (isset($_REQUEST['action']) AND $_REQUEST['action'] === 'woobe_bulk_products') {

                    $woobe_bulk = $this->storage->get_val('woobe_bulk_' . WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']));
                    $behavior = $woobe_bulk['upsell_ids']['behavior'];

                    if ($behavior !== 'new') {

                        $prev_ids = $product->get_upsell_ids();

                        switch ($behavior) {
                            case 'delete':
                                $value = array_diff($prev_ids, $value);
                                break;
                            case 'add':
                                $value = array_merge($prev_ids, $value);
                                break;
                            default:
//new
//do nothing
                                break;
                        }
                    }
                }

//***


                $product->set_upsell_ids($value);
                $product->save();

                $answer = WOOBE_HELPER::draw_upsells_popup_editor_btn($field_key, $product->get_id());

                break;

            case 'cross_sells':

                if (!is_array($value)) {
                    $value = str_replace("&amp;", "&", $value);
//from Product Editor
                    parse_str($value, $value);
                }

                $value = (isset($value['woobe_prod_ids'])) ? $value['woobe_prod_ids'] : array();

//***
//for bulk editing operation
                if (isset($_REQUEST['action']) AND $_REQUEST['action'] === 'woobe_bulk_products') {

                    $woobe_bulk = $this->storage->get_val('woobe_bulk_' . WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']));
                    $behavior = $woobe_bulk['cross_sell_ids']['behavior'];

                    if ($behavior !== 'new') {

                        $prev_ids = $product->get_cross_sell_ids();

                        switch ($behavior) {
                            case 'delete':
                                $value = array_diff($prev_ids, $value);
                                break;
                            case 'add':
                                $value = array_merge($prev_ids, $value);
                                break;
                            default:
//new
//do nothing
                                break;
                        }
                    }
                }

//***

                $product->set_cross_sell_ids($value);
                $product->save();

                $answer = WOOBE_HELPER::draw_cross_sells_popup_editor_btn($field_key, $product->get_id());

                break;

            case 'grouped':

                if ($product->get_type() === 'grouped') {

                    if (!is_array($value)) {
//from Product Editor
                        parse_str($value, $value);
                    }

                    $value = (isset($value['woobe_prod_ids'])) ? $value['woobe_prod_ids'] : array();
//***
//for bulk editing operation
                    if (isset($_REQUEST['action']) AND $_REQUEST['action'] === 'woobe_bulk_products') {

                        $woobe_bulk = $this->storage->get_val('woobe_bulk_' . WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']));
                        $behavior = $woobe_bulk['grouped_ids']['behavior'];

                        if ($behavior !== 'new') {

                            $prev_ids = $product->get_children();

                            switch ($behavior) {
                                case 'delete':
                                    $value = array_diff($prev_ids, $value);
                                    break;
                                case 'add':
                                    $value = array_merge($prev_ids, $value);
                                    break;
                                default:
//new
//do nothing
                                    break;
                            }
                        }
                    }

//***

                    $product->set_children($value);
                    $product->save();

                    $answer = WOOBE_HELPER::draw_grouped_popup_editor_btn($field_key, $product->get_id());
                }

                break;

            default:

//fix for product variations statuses
                if ($field_key === 'post_status') {
                    if ($product->is_type('variation')) {
                        if ($value != 'publish') {
                            $value = 'private';
                        }
                    }
                }
                if ('post_date' == $field_key) {
                    $new_key = 'post_date_gmt';
                    $new_value = gmdate('Y-m-d H:i:s', strtotime($value));
                    wp_update_post(array(
                        'ID' => $product_id,
                        $new_key => $new_value
                    ));
                }

//				if ($field_key === 'post_content') {
//					if(is_array($value)){
//$value=implode(" ", $value);
//
//$value=strval ($value);
//
//					}
//				}
                // fix for variation
                if ($field_key === 'post_author') {
                    $children = $product->get_children();
                    if (!empty($children)) {
                        foreach ($children as $var_id) {
                            wp_update_post(array(
                                'ID' => $var_id,
                                $field_key => $value
                            ));
                        }
                    }
                }
//***
//table field
                wp_update_post(array(
                    'ID' => $product_id,
                    $field_key => $value
                ));

                $answer = get_post_field($field_key, $product_id);

                $this->_call_hooks_after_product_update($product);

                break;
        }

//***
//speedfix comment line -0.5sek
//wc_delete_product_transients($product_id);
//***

        if ($fields[$field_key]['edit_view'] == 'textinput') {
            $answer = $this->sanitize_answer_value($field_key, (isset($fields[$field_key]['sanitize']) ? $fields[$field_key]['sanitize'] : ''), $answer);
        }

//***
//FOR ANY FLEXIBLE COMPATIBILITY
        do_action('woobe_after_update_page_field', $product_id, $product, $field_key, $value, $field_type);

        //update products cache
        $this->cached_products[$product_id] = $product;
        return $answer;
        return stripcslashes($answer);
    }

//util
    private function _call_hooks_after_product_update(&$product) {
        $product_id = $product->get_id();
        $pp = get_post($product_id);
        do_action('save_post', $product_id, $pp, true);
        do_action("save_post_product", $product_id, $pp, true);
        do_action('edit_post', $product_id, $pp);
        $this->clear_caches($product);
        flush_rewrite_rules();

//***

        if ($product->get_type() === 'variation') {
            do_action('woocommerce_update_product_variation', $product_id, $product_id);
        } else {
            do_action('woocommerce_update_product', $product_id, $product);
        }
    }

//service
    public function sanitize_answer_value($field_key, $sanitize, $val) {

        $res = $val;

        switch ($sanitize) {
            case 'sanitize_key':
                $res = sanitize_key($val);
                break;
            case 'esc_url':
                $res = esc_url($val);
                break;
            case 'urldecode':
                $res = urldecode($val);
                break;
            case 'floatval':
                $val = str_replace(',', '.', $val);
                $val = str_replace(' ', '', $val);

                if (in_array($field_key, array('regular_price', 'sale_price'))) {
                    $res = number_format(floatval($val), wc_get_price_decimals());
                } else {
                    $res = floatval($val);
                }

                break;
            case 'intval':
                $res = intval($val);
                break;

            case 'array':
                if (is_array($val) AND!empty($val)) {
                    $res = WOOBE_HELPER::array_to_string($val);
                }
                break;
        }

        return $res;
    }

    public function set_product_attributes_visible($product_id, $attributes, $visible) {

        $meta = get_post_meta($product_id, '_product_attributes', true);
        if (!is_array($meta)) {
            $meta = array();
        }
        if (!is_array($attributes)) {
            $attributes = array($attributes);
        }
        foreach ($meta as $pa_key => $item) {
            if (in_array($item['name'], $attributes)) {
                $meta[$pa_key]['is_visible'] = $visible;
            }
        }

        update_post_meta($product_id, '_product_attributes', $meta);
    }

    public function set_product_attributes($product_id, $field_key, $value, $mode = 'replace', $attr_data = array()) {
        if (!is_array($value)) {
            $value = array(intval($value));
        } else {
            foreach ($value as $k => $tid) {
                $value[$k] = intval($tid);
            }
        }

//***

        $product = $this->get_product($product_id);

        $attributes = array();
        $product_attributes = $product->get_attributes();

//*** fix for empty value
        if (count($value) === 1) {
            if (intval($value[0]) === 0) {
                $value = array();
            }
        }

//***

        if (!empty($product_attributes)) {
//wp-content\plugins\woocommerce\includes\admin\meta-boxes\class-wc-meta-box-product-data.php
//public static function prepare_attributes
            foreach ($product_attributes as $pa_key => $a) {

                if (is_object($a)) {
                    $attribute = new WC_Product_Attribute();
                    $attribute->set_id($a->get_id());
                    $attribute->set_name($a->get_name());

                    if ($a->get_name() == $field_key) {

//detach attributes if there is no selected terms!!
                        if (empty($value)) {
                            continue;
                        }
//***
                        switch ($mode) {
                            case 'append':
//using in bulk
                                $attribute->set_options(array_unique(array_merge($a->get_options(), $value)));
                                break;
                            case 'replace':
                            case 'new':
//using in bulk AND in editor
                                $attribute->set_options($value);
                                break;
                            case 'remove':
//using in bulk
                                $attribute->set_options(array_values(array_diff($a->get_options(), $value)));

                                break;
                            case 'visible':
//using in bulk
                                $a->set_visible($value);
                                break;
                            default :
                                $attribute->set_options($value);
                        }
                    } else {
                        $attribute->set_options($a->get_options());
                    }
                    $attribute->set_position($a->get_position());
                    $attribute->set_visible($a->get_visible());
                    $attribute->set_variation($a->get_variation());

                    $attributes[] = $attribute;
                }
            }
        }

//***
//if such attribute not applied in the product
        if (!isset($product_attributes[$field_key]) AND!isset($product_attributes[strtolower(urlencode($field_key))])) {
            $attribute = new WC_Product_Attribute();
            $attribute_taxonomies = wc_get_attribute_taxonomies();
            foreach ($attribute_taxonomies as $a) {
                if ('pa_' . $a->attribute_name == $field_key) {

                    if (!empty($value)) {
                        $attribute->set_id($a->attribute_id);
                        $attribute->set_name('pa_' . $a->attribute_name);
                        $attribute->set_options($value);
                        $attribute->set_position(count($attributes));
                        $attribute->set_visible(1);
                        if (in_array('set_variation', $attr_data)) {
                            $attribute->set_variation(true);
                        } else {
                            $attribute->set_variation(false);
                        }
                        $attributes[] = $attribute;
                    }

                    break;
                }
            }
        }

        $product->set_attributes($attributes);
        $product->save();
    }

//for saving downloads
    private function prepare_downloads($file_names, $file_urls, $file_hashes) {
        $downloads = array();

        if (!empty($file_urls)) {
            $file_url_size = sizeof($file_urls);

            for ($i = 0; $i < $file_url_size; $i++) {
                if (!empty($file_urls[$i])) {
                    $downloads[] = array(
                        'name' => wc_clean($file_names[$i]),
                        'file' => wp_unslash(trim($file_urls[$i])),
                        'previous_hash' => wc_clean($file_hashes[$i]),
                    );
                }
            }
        }
        return $downloads;
    }

//++++++++++++++++++++++++++++++++++++++++

    public function get_post_field($product_id, $field_key, $post_parent = 0) {
        if (!$product_id) {
            return FALSE;
        }

//***
        $res = '';
        $field_type = $this->settings->get_fields()[$field_key]['field_type'];

//fix for description of one variation
        if ($field_key == 'post_content' AND $post_parent > 0) {
            $field_type = 'meta';
            $field_key = '_variation_description';
        }

//***

        switch ($field_type) {
            case 'meta':
                $res = get_post_meta($product_id, $field_key, true);
                break;

            case 'field':
                $res = get_post_field($field_key, $product_id);
                break;

            case 'prop':
                $product = $this->get_product($product_id);

//$field_key - for example: catalog_visibility
                $func_name = 'get_' . $field_key;

//for example get_product_url() and get_button_text() exists only for external products
                if (method_exists($product, $func_name)) {
                    $res = $product->$func_name();
                }

                break;

            case 'taxonomy':
                $res = wp_get_post_terms($product_id, $this->settings->get_fields()[$field_key]['taxonomy'], array(
//'fields' => 'ids',
                    'hide_empty' => false,
                ));
                break;

            case 'attribute':
                $product = $this->get_product($product_id);
                $attributes = $product->get_attributes();

                if (!empty($attributes)) {

                    if (!$product->is_type('variation')) {
                        $res = array();

                        foreach ($attributes as $a) {
                            if (is_object($a)) {
                                if ($a->get_name() == $field_key) {
                                    $res = $a->get_options();
                                    break;
                                }
                            }
                        }
                    } else {
                        //a variation has NOT attributes as it is an attribute self
                        //echo get_class($product);
                        foreach ($attributes as $a) {
                            if (is_object($a) AND method_exists($a, 'get_name')) {
                                if ($a->get_name() == $field_key) {
                                    $t = get_term_by('slug', $a, $field_key);
                                    if (!empty($t)) {
                                        $res = array($t->term_id);
                                    }
                                    break;
                                }
                            }
                        }
                    }
                }

                break;
            case 'attribute_visibile':
                $product = $this->get_product($product_id);
                $attributes = $product->get_attributes();

                if (!empty($attributes)) {

                    if (!$product->is_type('variation')) {
                        $res = array();

                        foreach ($attributes as $a) {
                            if (is_object($a)) {
                                if ($a->get_visible()) {
                                    $res[] = $a->get_visible();
                                }
                            }
                        }
                    }
//                    else {
//                        //a variation has NOT attributes as it is an attribute self
//                        //echo get_class($product);
//                        foreach ($attributes as $a) {
//                            if (is_object($a) AND method_exists($a, 'get_name')) {
//                                if ($a->get_name() == $field_key) {
//                                    $t = get_term_by('slug', $a, $field_key);
//                                    if (!empty($t)) {
//                                        $res = array($t->term_id);
//                                    }
//                                    break;
//                                }
//                            }
//                        }
//                    }
                }
                break;
            case 'gallery':
                $product = $this->get_product($product_id);
                try {
                    $res = $product->get_gallery_image_ids();
                } catch (Exception $e) {
                    $res = array();
                }
                break;

            case 'downloads':
                try {
                    $product = $this->get_product($product_id);
                    $res = $product->get_downloads('edit');
                } catch (Exception $e) {
                    $res = array();
                }
                break;

            case 'upsells':
                try {
                    $product = $this->get_product($product_id);
                    $res = $product->get_upsell_ids();
                } catch (Exception $e) {
                    $res = array();
                }
                break;

            case 'cross_sells':
                try {
                    $product = $this->get_product($product_id);
                    $res = $product->get_cross_sell_ids();
                } catch (Exception $e) {
                    $res = array();
                }
                break;

            case 'grouped':
                try {
                    $product = $this->get_product($product_id);
                    $res = $product->get_children('edit');
                } catch (Exception $e) {
                    $res = array();
                }
                break;
        }

        return $res;
    }

    public function get_product($product_id, $use_cache = true) {

        $product_id = intval($product_id);
 return wc_get_product($product_id);
        if ($use_cache) {
            if (!isset($this->cached_products[$product_id])) {
                $this->cached_products[$product_id] = wc_get_product($product_id);
            }

            return $this->cached_products[$product_id];
        } else {
            return wc_get_product($product_id);
        }

        /*
          $product_type = WC_Product_Factory::get_product_type($product_id);
          $classname = WC_Product_Factory::get_product_classname($product_id, $product_type);
          return new $classname($product_id);
         *
         */
    }

    public function normalize_calendar_date($value, $field_key) {
        if ($field_key == 'post_date') {
            $date = new DateTime();
            $date->setTimestamp(strtotime($value));
            $value = $date->format('Y-m-d H:i:s');
            return $value;
        }

        if ($value != 0) {//if not clearing
            //  $value = explode('-', $value);
            if (isset($this->settings->active_fields[$field_key]['set_day_end']) AND $this->settings->active_fields[$field_key]['set_day_end']) {
                //$value = mktime(23, 59, 59, intval($value['1']), intval($value['2']), intval($value['0']));
                $value = date('Y-m-d 23:59:59', intval(strtotime($value)));
            } else {
                //$value = mktime(0, 0, 0, intval($value['1']), intval($value['2']), intval($value['0']));
                $value = date('Y-m-d 00:00:00', intval(strtotime($value)));
            }
            $value = strtotime($value) - intval(get_option('gmt_offset')) * 3600;

//***
            //$gmt_offset = intval(get_option('gmt_offset')) * 3600;
            //$value += $gmt_offset;
//***

            if ($this->settings->active_fields[$field_key]['type'] === 'timestamp'
                    AND $this->settings->active_fields[$field_key]['field_type'] === 'field') {
                $date = new DateTime();
                $date->setTimestamp($value);
                $value = $date->format('Y-m-d H:i:s');
            }
        }

        return $value;
    }

    public function get_attributes($product_id, $type = "all") {
        $product = $this->get_product($product_id);
        $attributes = $product->get_attributes();
        $res = array();
        if (!empty($attributes)) {

            if ($type == "visible") {
                $res = [];
                foreach ($attributes as $a) {
                    if (is_object($a)) {
                        if ($a->get_visible()) {
                            $res[] = $a;
                        }
                    }
                }
            } else {
                $res = $attributes;
            }
        }
        return $res;
    }

//service
    public function string_replacer($val, $product_id) {

        if (is_string($val)) {
            if (stripos($val, '{TITLE}') !== false) {
                $val = str_ireplace('{TITLE}', $this->get_post_field($product_id, 'post_title'), $val);
            }

            if (stripos($val, '{ID}') !== false) {
                $val = str_ireplace('{ID}', $product_id, $val);
            }

            if (stripos($val, '{SKU}') !== false) {
                $val = str_ireplace('{SKU}', $this->get_post_field($product_id, 'sku'), $val);
            }


//***

            if (stripos($val, '{PARENT_TITLE}') !== false) {
                $p = $this->get_product($product_id);
                if ($p->is_type('variation')) {
                    $val = str_ireplace('{PARENT_TITLE}', $this->get_post_field($p->get_parent_id(), 'post_title'), $val);
                } else {
                    $val = str_ireplace('{PARENT_TITLE}', $this->get_post_field($product_id, 'post_title'), $val);
                }
            }

            if (stripos($val, '{PARENT_SKU}') !== false) {
                $p = $this->get_product($product_id);
                if ($p->is_type('variation')) {
                    $val = str_ireplace('{PARENT_SKU}', $this->get_post_field($p->get_parent_id(), 'sku'), $val);
                } else {
                    $val = str_ireplace('{PARENT_SKU}', $this->get_post_field($product_id, 'sku'), $val);
                }
            }

            if (stripos($val, '{PARENT_ID}') !== false) {
                $p = $this->get_product($product_id);
                if ($p->is_type('variation')) {
                    $val = str_ireplace('{PARENT_ID}', $p->get_parent_id(), $val);
                } else {
                    $val = str_ireplace('{PARENT_ID}', $product_id, $val);
                }
            }


            if (stripos($val, '{MENU_ORDER}') !== false) {
                $val = str_ireplace('{MENU_ORDER}', $this->get_post_field($product_id, 'menu_order'), $val);
            }

            if (stripos($val, '{REGULAR_PRICE}') !== false) {
                $val = str_ireplace('{REGULAR_PRICE}', $this->get_post_field($product_id, 'regular_price'), $val);
            }

            if (stripos($val, '{SALE_PRICE}') !== false) {
                $val = str_ireplace('{SALE_PRICE}', $this->get_post_field($product_id, 'sale_price'), $val);
            }

//***

            if (stripos($val, '{meta:') !== false) {
//{meta:_woocs_regular_price_USD} - lets take data from metafield
                $val = explode(':', $val);
                $val = $this->get_post_field($product_id, trim($val[1], ' }'));
            }

            if (stripos($val, '{attribute:') !== false) {

                //{attribute:pa_size}
                $matches = array();
                preg_match("/(?<={attribute:)(.*)(?=})/", $val, $matches);

                if (isset($matches[1])) {
                    $prod = $this->get_product($product_id);
                    $attr = $prod->get_attribute(trim($matches[1]));

                    if (!empty($attr)) {
                        $val = str_ireplace('{attribute:' . $matches[1] . "}", $attr, $val);
                    } else {
                        $val = str_ireplace('{attribute:' . $matches[1] . "}", "", $val);
                    }
                }
            }
        }

        return apply_filters('woobe_apply_string_replacer', $val);
    }
	public function string_macros($val, $field_key, $product_id) {
		if (!is_string($val)) {
			return $val;
		}		
		$original_val = $this->get_post_field($product_id, $field_key);
		
		if (is_string($original_val)) {
            if (stripos($val, '{DO_STRING_UP}') !== false) {
                $val = str_ireplace('{DO_STRING_UP}', mb_strtoupper($original_val), $val);
            }
            if (stripos($val, '{DO_STRING_DOWN}') !== false) {
                $val = str_ireplace('{DO_STRING_DOWN}', mb_strtolower($original_val), $val);
            }
            if (stripos($val, '{DO_STRING_TITLE}') !== false) {
                $val = str_ireplace('{DO_STRING_TITLE}', mb_convert_case($original_val, MB_CASE_TITLE, "UTF-8"), $val);
            }
            if (stripos($val, '{DO_STRING_UP_FIRST}') !== false) {
				$fc = mb_strtoupper(mb_substr($original_val, 0, 1));
				$original_val = $fc . mb_substr($original_val, 1);				
                $val = str_ireplace('{DO_STRING_UP_FIRST}', $original_val, $val);
            }			
			
			 
		}
		return $val;
	}

    /**
     * Generates a title with attribute information for a variation.
     * Products will get a title of the form "Name - Value, Value" or just "Name".
     *
     * @since 3.0.0
     * @param WC_Product
     * @return string
     */
    public function generate_product_title($product) {
        $attributes = (array) $product->get_attributes();

// Do not include attributes if the product has 10+ attributes.
        $should_include_attributes = count($attributes) < 10;

// Do not include attributes if an attribute name has 2+ words and the
// product has multiple attributes.
        if ($should_include_attributes && 1 < count($attributes)) {
            foreach ($attributes as $name => $value) {
                if (false !== strpos($name, '-')) {
                    $should_include_attributes = false;
                    break;
                }
            }
        }

        $should_include_attributes = apply_filters('woocommerce_product_variation_title_include_attributes', $should_include_attributes, $product);
        $separator = apply_filters('woocommerce_product_variation_title_attributes_separator', ' - ', $product);

//$parent_product = $this->get_product($product->get_parent_id());
        $title_base = get_post_field('post_title', $product->get_parent_id());
        if ($should_include_attributes) {
            $title_suffix = wc_get_formatted_variation($product, true, false);

            if (!empty($attributes)) {
                $title_suffix .= ' <small>[';
                $title_suffix .= implode(', ', array_map(
                                function ($str) {
                                    return str_replace('pa_', '', $str);
                                }, array_map('urldecode', array_keys($attributes))
                ));
                $title_suffix .= ']</small>';
            }
        } else {
            $title_suffix = '';
        }


//$title_suffix = $should_include_attributes ? wc_get_formatted_variation($product, true, false) : '';

        return apply_filters('woocommerce_product_variation_title', $title_suffix ? $title_base . $separator . $title_suffix : $title_base, $product, $title_base, $title_suffix);
    }

    public function is_current_user_can_edit_field($field_key) {

        if (!in_array($this->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
            $shop_manager_visibility = $this->settings->get_shop_manager_visibility();

            $user_can = apply_filters('woobe_user_can_edit', $shop_manager_visibility[$field_key], $field_key, $shop_manager_visibility);
            if (!intval($user_can)) {
                return FALSE;
            }
        }

        return TRUE;
    }

    //service function to process serialized meta data
    public function process_jsoned_meta_data($raw_data) {
        $result = array();
        //*** //for js arrays
        if (isset($raw_data['keys']) AND!empty($raw_data['keys'])) {
            $tmp = array();
            foreach ($raw_data['keys'] as $kk => $kv) {
                if (!is_null($kv)) {
                    if (!is_array($kv) AND isset($raw_data['keys'][$kv]) AND is_array($raw_data['keys'][$kv])) {
                        if (!empty($raw_data['keys'][$kv])) {
                            $tmp[WOOBE_HELPER::prepare_meta_keys($kv)] = array();
                            foreach ($raw_data['keys'][$kv] as $kkk => $vvv) {
                                if (isset($raw_data['values'][$kv][$kkk])) {
                                    $tmp[WOOBE_HELPER::prepare_meta_keys($kv)][WOOBE_HELPER::prepare_meta_keys($vvv)] = $raw_data['values'][$kv][$kkk];
                                }
                            }
                        }
                    } else {
                        if (!is_array($kv)) {
                            if (isset($raw_data['values'][WOOBE_HELPER::prepare_meta_keys($kk)])) {
                                $tmp[WOOBE_HELPER::prepare_meta_keys($kv)] = $raw_data['values'][WOOBE_HELPER::prepare_meta_keys($kk)];
                            }
                        }
                    }
                }
            } $result = $tmp;
        }
//*** //for js objects
        if (isset($raw_data['keys2']) AND!empty($raw_data['keys2'])) {
            $tmp = array();
            foreach ($raw_data['keys2'] as $k => $keys) {
                if (!empty($keys)) {
                    $o = array();
                    foreach ($keys as $kk => $key) {
                        if ($this->is_json($raw_data['values2'][$k][$kk])) {
                            $o[$key] = json_decode($raw_data['values2'][$k][$kk], ARRAY_A);
                        } else {
                            $o[$key] = $raw_data['values2'][$k][$kk];
                        }
                    } $tmp[$k] = $o;
                }
            } $result = array_merge($result, $tmp);
        }
        //*** //if meta value is just string or number
        if (empty($result)) {
            $result = $raw_data;
        }

        return $result;
    }

    protected function clear_caches(&$product) {
        wc_delete_product_transients($product->get_id());
        if ($product->get_parent_id('edit')) {
            wc_delete_product_transients($product->get_parent_id('edit'));
            WC_Cache_Helper::invalidate_cache_group('product_' . $product->get_parent_id('edit'));
        }
        WC_Cache_Helper::invalidate_attribute_count(array_keys($product->get_attributes()));
        WC_Cache_Helper::invalidate_cache_group('product_' . $product->get_id());
    }

    function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
	public function get_product_type($pr) {
		
		if (is_object($pr)) {
			$pr = $pr->get_id();
		} 
		$type = 'simple';
        $terms = wp_get_object_terms( $pr, 'product_type' );
		if (is_array($terms)) {
			$type = $terms[0]->slug;
		}
		
		return $type;
	}

}
