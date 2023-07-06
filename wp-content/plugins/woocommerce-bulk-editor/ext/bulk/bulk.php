<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOBE_BULK extends WOOBE_EXT {

    protected $slug = 'bulk'; //unique
    public $text_keys = array();
    public $num_keys = array();
    public $other_keys = array();

    public function __construct() {

        $this->init_bulk_keys();

        add_action('woobe_ext_scripts', array($this, 'woobe_ext_scripts'), 1);
        add_action('woobe_tools_panel_buttons_end', array($this, 'woobe_tools_panel_buttons_end'), 1);

        //ajax
        add_action('wp_ajax_woobe_bulk_products_count', array($this, 'woobe_bulk_products_count'), 1);
        add_action('wp_ajax_woobe_bulk_products', array($this, 'woobe_bulk_products'), 1);
        add_action('wp_ajax_woobe_bulk_finish', array($this, 'woobe_bulk_finish'), 1);
        add_action('wp_ajax_woobe_bulk_draw_gallery_btn', array($this, 'woobe_bulk_draw_gallery_btn'), 1);
        add_action('wp_ajax_woobe_bulk_draw_download_files_btn', array($this, 'woobe_bulk_draw_download_files_btn'), 1);
        add_action('wp_ajax_woobe_bulk_draw_cross_sells_btn', array($this, 'woobe_bulk_draw_cross_sells_btn'), 1);
        add_action('wp_ajax_woobe_bulk_draw_upsell_ids_btn', array($this, 'woobe_bulk_draw_upsell_ids_btn'), 1);
        add_action('wp_ajax_woobe_bulk_draw_grouped_ids_btn', array($this, 'woobe_bulk_draw_grouped_ids_btn'), 1);
        add_action('wp_ajax_woobe_bulk_get_att_terms', array($this, 'woobe_bulk_get_att_terms'), 1);

        add_action('wp_ajax_woobe_bulk_delete_products_count', array($this, 'woobe_bulk_delete_products_count'), 1);
        add_action('wp_ajax_woobe_bulk_delete_products', array($this, 'woobe_bulk_delete_products'), 1);

        add_action('woobe_bulk_going', array($this, 'woobe_bulk_going'), 10, 2);

        //tabs
        $this->add_tab($this->slug, 'top_panel', esc_html__('Bulk Edit', 'woocommerce-bulk-editor'), 'pencil');
        add_action('woobe_ext_top_panel_' . $this->slug, array($this, 'woobe_ext_panel'), 1);
    }

    public function woobe_ext_scripts() {
        wp_enqueue_script('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/js/' . $this->slug . '.js', array(), WOOBE_VERSION);
        wp_enqueue_style('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/css/' . $this->slug . '.css', array(), WOOBE_VERSION);
        ?>
        <script>
            lang.<?php echo $this->slug ?> = {};
            lang.<?php echo $this->slug ?>.want_to_bulk = "<?php echo esc_html__('Will be edited next:', 'woocommerce-bulk-editor') ?>";
            lang.<?php echo $this->slug ?>.want_to_delete = "<?php echo esc_html__('Sure? Delete products?', 'woocommerce-bulk-editor') ?>";
            lang.<?php echo $this->slug ?>.deleting = "<?php echo esc_html__('Bulk deleting', 'woocommerce-bulk-editor') ?>";
            lang.<?php echo $this->slug ?>.deleted = "<?php echo esc_html__('Product(s) deleted!', 'woocommerce-bulk-editor') ?>";
            lang.<?php echo $this->slug ?>.bulking = "<?php echo esc_html__('Bulk editing', 'woocommerce-bulk-editor') ?> ...";
            lang.<?php echo $this->slug ?>.bulked = "<?php echo esc_html__('Product(s) edited! Table redrawing ...', 'woocommerce-bulk-editor') ?>";
            lang.<?php echo $this->slug ?>.bulked2 = "<?php echo esc_html__('Product(s) edited!', 'woocommerce-bulk-editor') ?>";
            lang.<?php echo $this->slug ?>.bulk_is_going = "<?php echo esc_html__('ATTENTION: Bulk operation is going!', 'woocommerce-bulk-editor') ?>";
        </script>
        <?php
    }

    public function woobe_tools_panel_buttons_end() {
        global $WOOBE;
        ?>
        &nbsp;|&nbsp;<span>
            <?php echo WOOBE_HELPER::draw_advanced_switcher(0, 'woobe_bind_editing', '', array('true' => esc_html__('binded editing', 'woocommerce-bulk-editor'), 'false' => esc_html__('binded editing', 'woocommerce-bulk-editor')), array('true' => 1, 'false' => 0), 'js_check_woobe_bind_editing', 'woobe_bind_editing'); ?>

            <?php
            $bind_tooltip = '';
            if ($WOOBE->show_notes) {
                $fields = $WOOBE->settings->get_fields();
                if (!empty($fields)) {
                    $bind_tooltip = [];
                    foreach ($fields as $field_key => $f) {
                        if ($f['direct']) {
                            $t = strip_tags($f['title']);
                            if (!empty($t) AND $field_key != 'ID') {
                                $bind_tooltip[] = $t;
                            }
                        }
                    }

                    $bind_tooltip = sprintf(esc_html__('In FREE version of the plugin you can change only next fields: %s', 'woocommerce-bulk-editor'), implode(', ', $bind_tooltip));
                }
            }
            ?>

            <?php echo WOOBE_HELPER::draw_tooltip(esc_html__('In this mode to the all selected products will be set the value of a product field which been edited', 'woocommerce-bulk-editor') . '. ' . $bind_tooltip) ?>

        </span>
        <?php
    }

    public function woobe_ext_panel() {
        $data = array();
        $data['shop_manager_visibility'] = $this->settings->get_shop_manager_visibility();
        $data['text_keys'] = $this->text_keys;
        $data['num_keys'] = $this->num_keys;
        $data['other_keys'] = $this->other_keys;
        $data['settings_fields'] = $this->settings->get_fields();
        echo WOOBE_HELPER::render_html($this->get_ext_path() . 'views/panel.php', $data);
    }

    //ajax
    public function woobe_bulk_products_count() {
        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }

        //***

        $bulk_data = array();

        if (!isset($_REQUEST['woobe_bind_editing'])) {
            parse_str($_REQUEST['bulk_data'], $bulk_data);

            $bulk_data = WOOBE_HELPER::sanitize_array($bulk_data);
        } else {
            //binded editing operation works
            if (is_array($_REQUEST['val'])) {
                $value = WOOBE_HELPER::sanitize_array($_REQUEST['val']);
            } else {
                $value = wp_kses($_REQUEST['val'], wp_kses_allowed_html('post'));
                $value = str_replace("&amp;", "&", $value);
            }


            $field_key = sanitize_text_field($_REQUEST['field']);

            //***

            $bulk_data['woobe_bulk'] = array(
                'is' => array(
                    $field_key => 1
                ),
                $field_key => array(
                    'value' => $value,
                    'behavior' => sanitize_text_field($_REQUEST['behavior'])
                )
            );
        }


        $this->storage->set_val('woobe_bulk_' . WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']), $bulk_data['woobe_bulk']);

        if (!isset($_REQUEST['no_filter'])) {
            //get count of filtered - doesn work if bulk for checked products
            $products = $this->products->gets(array(
                'fields' => 'ids',
                'no_found_rows' => true
            ));
            echo json_encode($products->posts);
        }

        //***

        do_action('woobe_bulk_started', WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']));

        exit;
    }

    public function woobe_bulk_delete_products_count() {
        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }

        $bulk_data = array();

        if (!isset($_REQUEST['woobe_bind_editing'])) {
            parse_str($_REQUEST['bulk_data'], $bulk_data);
            $bulk_data = WOOBE_HELPER::sanitize_array($bulk_data);
        } else {
            //binded editing operation works
            if (is_array($_REQUEST['val'])) {
                $value = WOOBE_HELPER::sanitize_array($_REQUEST['val']);
            } else {
                $value = wp_kses($_REQUEST['val'], wp_kses_allowed_html('post'));
            }

            $field_key = sanitize_text_field($_REQUEST['field']);

            //***

            $bulk_data['woobe_bulk'] = array(
                'is' => array(
                    $field_key => 1
                ),
                $field_key => array(
                    'value' => $value,
                    'behavior' => sanitize_text_field($_REQUEST['behavior'])
                )
            );
        }


        $this->storage->set_val('woobe_bulk_' . WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']), $bulk_data['woobe_bulk']);

        if (!isset($_REQUEST['no_filter'])) {
            //get count of filtered - doesn work if bulk for checked products
            $products = $this->products->gets(array(
                'fields' => 'ids',
                'no_found_rows' => true
            ));
            echo json_encode($products->posts);
        }

        exit;
    }

    public function woobe_bulk_delete_products() {
        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }
        if (!isset($_REQUEST['products_ids'])) {
            die('0');
        }

        if (is_array($_REQUEST['products_ids'])) {
            $is_variations_solo = intval($_REQUEST['woobe_show_variations']);

            $products_ids = array_map(function ($item) {
                return intval($item); //sanitize intval
            }, $_REQUEST['products_ids']);

            //as we want to change variations only but have ids of parents - lets get variations ids
            if ($is_variations_solo AND!empty($products_ids)) {
                $vars_ids = array();
                foreach ($products_ids as $product_id) {
                    $product = $this->products->get_product($product_id);
                    if ($product->is_type('variable')) {
                        $children = $product->get_children();
                        if (!empty($children)) {
                            $vars_ids = array_merge($vars_ids, $children);
                        }
                    }
                }
                $products_ids = array_unique(array_merge($products_ids, $vars_ids));
            }
            $woobe_bulk = $this->storage->get_val('woobe_bulk_' . WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']));

            foreach ($products_ids as $id) {
                if ($is_variations_solo) {

                    //lets check that currenct variation has the same attributes combination
                    if (isset($woobe_bulk['combination_attributes']) AND!empty($woobe_bulk['combination_attributes'])) {

                        $variation = $this->products->get_product($id);
                        $attributes = $variation->get_attributes();

                        //***

                        $go = FALSE;

                        //***

                        if (!empty($attributes)) {
                            foreach ($woobe_bulk['combination_attributes'] as $comb) {
                                //lets look is $attributes the same set of attributes as in $comb
                                $ak_att = array_keys($attributes);
                                $ak_cv = array_keys($comb);

                                //fix for non-latin symbols
                                if (!empty($ak_att)) {
                                    $ak_att = array_map('urldecode', $ak_att);
                                }

                                //fix for non-latin symbols
                                if (!empty($ak_cv)) {
                                    $ak_cv = array_map('urldecode', $ak_cv);
                                }

                                sort($ak_att);
                                sort($ak_cv);

                                if ($ak_att === $ak_cv) {
                                    $av_att = array_values($attributes);
                                    $av_cv = array_values($comb);

                                    //fix for non-latin symbols
                                    if (!empty($ak_att)) {
                                        $av_att = array_map('urldecode', $av_att);
                                    }


                                    if (!empty($av_cv)) {
                                        $av_cv = array_map('urldecode', $av_cv);
                                    }

                                    sort($av_att);
                                    sort($av_cv);
                                    if ($av_att === $av_cv) {
                                        $go = TRUE;
                                        break;
                                    }
                                }
                            }
                        }

                        //***

                        if (!$go) {
                            continue;
                        }
                    }
                }
                //wp_delete_post($id,false);
                wp_trash_post(intval($id));
            }
        } else {
            die('0');
        }

        die(json_encode($_REQUEST['products_ids']));
        exit;
    }

    //ajax
    public function woobe_bulk_products() {
        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }

        if (!isset($_REQUEST['products_ids'])) {
            die('0');
        }

        //***

        $fields = $this->settings->get_fields();
        $woobe_bulk = $this->storage->get_val('woobe_bulk_' . WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']));
        //key for history bulk opearation, not related to products keys
        $_REQUEST['woobe_bulk_key'] = WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']);

        $is_variations_solo = intval($_REQUEST['woobe_show_variations']);
        $products_ids = $_REQUEST['products_ids']; //sanitize in cycle below
        //***
        //as we want to change variations only but have ids of parents - lets get variations ids
        if ($is_variations_solo AND!empty($products_ids)) {
            $vars_ids = array();
            foreach ($products_ids as $product_id) {
                $product_id = intval($product_id); //sanitize

                $product = $this->products->get_product($product_id);
                if ($product->is_type('variable')) {
                    $children = $product->get_children();
                    if (!empty($children)) {
                        $vars_ids = array_merge($vars_ids, $children);
                    }
                }
            }

            $products_ids = array_unique(array_merge($products_ids, $vars_ids));
        }

        //***

        if (isset($woobe_bulk['is']) AND!empty($woobe_bulk['is']) AND!empty($products_ids)) {

            //***

            foreach ($woobe_bulk['is'] as $field_key => $is) {

                if ($fields[$field_key]['edit_view'] === 'calendar') {


                    if (!is_int($woobe_bulk[$field_key]['value']) AND (isset($fields[$field_key]["field_type"]) AND $fields[$field_key]["field_type"] == "meta")) {
                        $woobe_bulk[$field_key]['value'] = strtotime($woobe_bulk[$field_key]['value']); // - wrong way
                    } else {
                        $woobe_bulk[$field_key]['value'] = $this->products->normalize_calendar_date($woobe_bulk[$field_key]['value'], $field_key);
                    }
                }

                //***
                //speedfix
                wp_defer_term_counting(false);
                wp_defer_comment_counting(true);

                //***

                if (intval($is) === 1) {

                    foreach ($products_ids as $product_id) {

                        if ($is_variations_solo) {
                            //if enabled editing of variations only parent-products are ignored
                            $product = $this->products->get_product($product_id);
                            if (!$product->is_type('variation')) {
                                continue;
                            }

                            //lets check that currenct variation has the same attributes combination
                            if (isset($woobe_bulk['combination_attributes']) AND!empty($woobe_bulk['combination_attributes'])) {

                                $variation = $this->products->get_product($product_id);
                                $attributes = $variation->get_attributes();

                                //***

                                $go = FALSE;

                                //***

                                if (!empty($attributes)) {
                                    foreach ($woobe_bulk['combination_attributes'] as $comb) {
                                        //lets look is $attributes the same set of attributes as in $comb
                                        $ak_att = array_keys($attributes);
                                        $ak_cv = array_keys($comb);
										
                                        //fix for non-latin symbols
                                        if (!empty($ak_att)) {
                                            $ak_att = array_map('urldecode', $ak_att);
                                        }

                                        //fix for non-latin symbols
                                        if (!empty($ak_cv)) {
                                            $ak_cv = array_map('urldecode', $ak_cv);
                                        }

                                        sort($ak_att);
                                        sort($ak_cv);

                                        if ($ak_att === $ak_cv) {
                                            $new_attributes = $attributes;
                                            $new_comb = $comb;											
											if (in_array('-1',$comb)){
												$delete_keys = array_keys($comb, '-1',);
												
												foreach ($delete_keys as $d_key) {
													if (isset($new_attributes[$d_key])) {
														unset($new_attributes[$d_key]);
														unset($new_comb[$d_key]);
													}
												}
												
											}
											
                                            $av_att = array_values($new_attributes);
                                            $av_cv = array_values($new_comb);

                                            //fix for non-latin symbols
                                            if (!empty($ak_att)) {
                                                $av_att = array_map('urldecode', $av_att);
                                            }

                                            if (!empty($av_cv)) {
                                                $av_cv = array_map('urldecode', $av_cv);
                                            }

                                            sort($av_att);
                                            sort($av_cv);
                                            if ($av_att === $av_cv) {
                                                $go = TRUE;
                                                break;
                                            }
                                        }
                                    }
                                }

                                //***

                                if (!$go) {
                                    continue;
                                }
                            }
                        }

                        //***


                        switch ($field_key) {
                            case 'post_title':
                            case 'post_content':
                            case 'post_excerpt':
                            case 'post_name':
                            case 'purchase_note':
                            case 'sku':
                            case 'tax_class':
                            case 'backorders':
                            case 'sold_individually':
                            case 'reviews_allowed':
                            case 'product_url':
                            case 'button_text':
                                $this->_process_text_data($woobe_bulk, $field_key, $product_id);
                                break;

                            case 'post_status':
                            case 'stock_status':
                            case 'manage_stock':
                            case 'tax_status':
                            case 'catalog_visibility':
                            case 'product_type':
                            case 'featured':
                            case 'virtual':
                            case 'downloadable':
                            case 'download_files':
                            case 'gallery':
                            case 'upsell_ids':
                            case 'cross_sell_ids':
                            case 'grouped_ids':
                            case 'post_date':
                                if (intval($woobe_bulk[$field_key]['value']) !== -1) {
                                    $this->products->update_page_field($product_id, $field_key, $woobe_bulk[$field_key]['value']);
                                }
                                break;
                            case 'attribute_visibility':

                                if (!isset($woobe_bulk[$field_key]['value']) OR!is_array($woobe_bulk[$field_key]['value'])) {

                                    $woobe_bulk[$field_key]['value'] = array();
                                }
                                if (!isset($woobe_bulk[$field_key]['visible'])) {
                                    $woobe_bulk[$field_key]['visible'] = 1;
                                }
                                $this->products->set_product_attributes_visible($product_id, $woobe_bulk[$field_key]['value'], $woobe_bulk[$field_key]['visible']);

                                break;
                            case 'regular_price':
                            case 'sale_price':
                            case 'stock_quantity':
                            case 'download_expiry':
                            case 'download_limit':
                            case 'date_on_sale_from':
                            case 'date_on_sale_to':
                            case 'weight':
                            case 'length':
                            case 'width':
                            case 'height':
                            case 'product_shipping_class':
                            case 'menu_order':
                            case 'post_author':
                            case 'total_sales':
                            case 'review_count':
                            case 'average_rating':
                                $this->_process_number_data($woobe_bulk, $field_key, $product_id);
                                break;

                            default:
                                break;
                        }

                        //***

                        if (isset($fields[$field_key]) AND $fields[$field_key]['field_type'] === 'taxonomy') {
                            //if (!empty($woobe_bulk[$field_key]['value'])) {
                            do_action('woobe_before_update_page_field', $field_key, $product_id, 0); //for the History
                            if (!isset($woobe_bulk[$field_key]['behavior'])) {
                                $woobe_bulk[$field_key]['behavior'] = "";
                            }
                            switch ($woobe_bulk[$field_key]['behavior']) {
                                case 'append':
                                    if (is_taxonomy_hierarchical($field_key)) {
                                        wp_set_post_terms($product_id, $woobe_bulk[$field_key]['value'], $field_key, true);
                                    } else {
                                        //product_tag for example
                                        foreach ($woobe_bulk[$field_key]['value'] as $term_id) {
                                            $t = get_term_by('id', $term_id, $field_key);
                                            wp_set_post_terms($product_id, $t->slug, $field_key, true);
                                        }
                                    }
                                    break;
                                case 'replace':
                                case 'new':
                                    if (is_taxonomy_hierarchical($field_key)) {
                                        wp_set_post_terms($product_id, $woobe_bulk[$field_key]['value'], $field_key, false);
                                    } else {
                                        //product_tag for example
                                        $append = false; //clean previous by first one then append
                                        if (!empty($woobe_bulk[$field_key]['value']) AND is_array($woobe_bulk[$field_key]['value'])) {
                                            foreach ($woobe_bulk[$field_key]['value'] as $term_id) {
                                                $t = get_term_by('id', $term_id, $field_key);
                                                wp_set_post_terms($product_id, $t->slug, $field_key, $append);
                                                $append = true;
                                            }
                                        }
                                    }
                                    break;
                                case 'remove':
                                    foreach ($woobe_bulk[$field_key]['value'] as $term_id) {
                                        $t = get_term_by('id', $term_id, $field_key);
                                        wp_remove_object_terms($product_id, $t->slug, $field_key);
                                    }
                                    break;
                            }
                            //}
                        }

                        //***

                        if (isset($fields[$field_key]) AND $fields[$field_key]['field_type'] === 'attribute') {
                            if (!isset($woobe_bulk[$field_key]['value'])) {
                                $woobe_bulk[$field_key]['value'] = '';
                            }
                            do_action('woobe_before_update_page_field', $field_key, $product_id, 0); //for the History
                            $this->products->set_product_attributes($product_id, $field_key, $woobe_bulk[$field_key]['value'], $woobe_bulk[$field_key]['behavior']);
                        }

                        //***

                        if (isset($fields[$field_key]) AND $fields[$field_key]['field_type'] === 'meta') {
                            switch ($fields[$field_key]['type']) {
                                case 'string':

                                    //if data is serialized in ine string
                                    if ($fields[$field_key]['edit_view'] == 'meta_popup_editor') {
                                        if (!is_array($woobe_bulk[$field_key]['value'])) {

                                            //if not else parsed
                                            parse_str($woobe_bulk[$field_key]['value'], $meta_val);

                                            $woobe_bulk[$field_key]['value'] = $this->products->process_jsoned_meta_data($meta_val);
                                        }
                                    }
									//***
                                    if ($fields[$field_key]['edit_view'] == 'gallery_popup_editor') {
					
                                        if (!is_array($woobe_bulk[$field_key]['value'])) {
                                            //if not else parsed
                                            parse_str($woobe_bulk[$field_key]['value'], $meta_val);
                                            if (!empty($meta_val['woobe_gallery_images'])) {
                                                $woobe_bulk[$field_key]['value'] = $meta_val;
											} 
                                        }
                                        
                                    }
                                    //***

                                    if ($fields[$field_key]['edit_view'] !== 'switcher') {
                                        $this->_process_text_data($woobe_bulk, $field_key, $product_id);
                                    } else {
                                        if (intval($woobe_bulk[$field_key]['value']) !== -1) {
                                            $this->products->update_page_field($product_id, $field_key, intval($woobe_bulk[$field_key]['value']));
                                        }
                                    }
                                    break;

                                case 'number':
                                    $this->_process_number_data($woobe_bulk, $field_key, $product_id);
                                    break;

                                default:
                                    break;
                            }
                        }
                    }
                }
            }

            do_action('woobe_bulk_going', sanitize_text_field($_REQUEST['woobe_bulk_key']), count($products_ids));
        }



        die('done');
    }

    public function woobe_bulk_going($bulk_key, $products_count) {
        $count_key = 'woobe_bulk_' . strtolower($bulk_key) . '_count';
        $count_now = intval($this->storage->get_val($count_key));
        $this->storage->set_val($count_key, $products_count + $count_now);
    }

    private function _process_text_data($woobe_bulk, $field_key, $product_id) {
        //if (!empty($woobe_bulk[$field_key]['value'])) {
        $val = $this->products->get_post_field($product_id, $field_key);
		$woobe_bulk[$field_key]['value'] = $this->products->string_macros($woobe_bulk[$field_key]['value'], $field_key, $product_id);
        switch ($woobe_bulk[$field_key]['behavior']) {
            case 'append':
                $val = $this->products->string_replacer($val . $woobe_bulk[$field_key]['value'], $product_id);
                break;
            case 'prepend':
                $val = $this->products->string_replacer($woobe_bulk[$field_key]['value'] . $val, $product_id);
                break;
            case 'new':
                $val = $this->products->string_replacer($woobe_bulk[$field_key]['value'], $product_id);
                break;
            case 'replace':
                $replace_to = $this->products->string_replacer($woobe_bulk[$field_key]['replace_to'], $product_id);
                $replace_from = $this->products->string_replacer($woobe_bulk[$field_key]['value'], $product_id);

                //fix  for  apostrophe
                $replace_from = str_replace("\'", "'", $replace_from);

                if ($woobe_bulk[$field_key]['case'] == 'ignore') {
                    $val = str_ireplace($replace_from, $replace_to, $val);
                } else {
                    $val = str_replace($replace_from, $replace_to, $val);
                    /*
                     * https://stackoverflow.com/questions/19317493/php-preg-replace-case-insensitive-match-with-case-sensitive-replacement
                      $val = preg_replace_callback('/\b' . $replace_from . '\b/i', function($matches) use ($replace_to) {
                      $i = 0;
                      return join('', array_map(function($char) use ($matches, &$i) {
                      return ctype_lower($matches[0][$i++]) ? strtolower($char) : strtoupper($char);
                      }, str_split($replace_to)));
                      }, $val);
                     *
                     */
                }

                break;
        }

        //***
        $empty_exceptions = array('tax_class'); //setting empty values is possible with this fields

        $can = true; //!empty($val);

        if (in_array($field_key, $empty_exceptions)) {
            $can = true;
        }

        if ($can) {
			
            $val = $this->products->update_page_field($product_id, $field_key, $val);
        }
        //}
    }

    private function _process_number_data($woobe_bulk, $field_key, $product_id) {
        if ($woobe_bulk[$field_key]['behavior'] != 'new') {
            $val = floatval($this->products->get_post_field($product_id, $field_key));
        }

        //***

        switch ($woobe_bulk[$field_key]['behavior']) {
            case 'new':
                $val = floatval($woobe_bulk[$field_key]['value']);
                break;

            case 'invalue':
                $val += floatval($woobe_bulk[$field_key]['value']);
                break;

            case 'devalue':
                $val -= floatval($woobe_bulk[$field_key]['value']);
                break;

            case 'inpercent':
                $val = $val + $val * floatval($woobe_bulk[$field_key]['value']) / 100;
                break;

            case 'depercent':
                $val = $val - $val * floatval($woobe_bulk[$field_key]['value']) / 100;
                break;

            case 'devalue_regular_price':
                //for sale_price only
                $val = $this->products->get_post_field($product_id, 'regular_price') - floatval($woobe_bulk[$field_key]['value']);
                break;

            case 'depercent_regular_price':
                //for sale_price only
                $val = floatval($this->products->get_post_field($product_id, 'regular_price'));
                $val = $val - $val * floatval($woobe_bulk[$field_key]['value']) / 100;
                break;

            case 'invalue_sale_price':
                //for regular_price only
                $val = floatval($this->products->get_post_field($product_id, 'sale_price') + floatval($woobe_bulk[$field_key]['value']));
                break;

            case 'inpercent_sale_price':
                //for regular_price only
                $val =  floatval($this->products->get_post_field($product_id, 'sale_price'));
                $val = $val + $val * floatval($woobe_bulk[$field_key]['value']) / 100;
                break;
        }

        if (isset($_REQUEST['num_formula_action']) AND isset($_REQUEST['num_formula_value']) AND $_REQUEST['num_formula_value'] != '-1') {
            $v_key = esc_textarea($_REQUEST['num_formula_value']);
            $action = esc_textarea($_REQUEST['num_formula_action']);
            $v_data = floatval(get_post_meta($product_id, $v_key, true));

            switch ($action) {
                case '-':
                    $val = $val - $v_data;
                    break;
                case '*':
                    $val = $val * $v_data;
                    break;
                case '/':
                    if ($v_data == 0) {
                        $v_data = 1;
                    }
                    $val = $val / $v_data;
                    break;

                default:
                    $val = $val + $v_data;
                    break;
            }
        }
		
		if (isset($_REQUEST['num_rand_data']) && is_array($_REQUEST['num_rand_data'])) {
			$rand_data = wc_clean($_REQUEST['num_rand_data']);
			if (isset($rand_data['from']) && isset($rand_data['to']) && ($rand_data['from'] != $rand_data['to']) && ($rand_data['from'] < $rand_data['to'])) {
				$from = (float)$rand_data['from'];
				$to = (float)$rand_data['to'];
				$decimal = 1;
				if (isset($rand_data['decimal'])) {
					$decimal = (int)$rand_data['decimal'];
				}
				$action = '+';
				if (isset($rand_data['action'])) {
					$action = $rand_data['action'];
				}
				
				$rand_val = rand($from * $decimal, $to * $decimal)/$decimal;
				switch ($action) {
					case '-':
						$val = $val - $rand_val;
						break;
					case '*':
						$val = $val * $rand_val;
						break;
					case '/':
						if ($rand_val == 0) {
							$rand_val = 1;
						}
						$val = $val / $rand_val;
						break;

					default:
						$val = $val + $rand_val;
						break;
				}				
				
			}
		}

        //***

        $convert = TRUE;
        if ($field_key == 'sale_price') {
            //sale price CAN NOT be more OR even equal to the regular price
            if ($val >= $this->products->get_post_field($product_id, 'regular_price')) {
                $convert = FALSE;
            }
            //to delete sale price
            if ($val <= 0) {
                $val = -1;
                $val = $this->products->update_page_field($product_id, $field_key, $val);
                $convert = FALSE;
            }
        }

        //***

        if ($convert) {
            $val = $this->products->update_page_field($product_id, $field_key, floatval($val));
        }
    }

    public function woobe_bulk_finish() {
        do_action('woobe_bulk_finished', WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']));
        $count_key = 'woobe_bulk_' . WOOBE_HELPER::sanitize_bulk_key($_REQUEST['bulk_key']) . '_count';
        die($this->storage->get_val($count_key) . '');
    }

    private function init_bulk_keys() {

        $fields = woobe_get_fields();

        $this->text_keys = array(
            'post_title' => array(
                'title' => esc_html__('title', 'woocommerce-bulk-editor'),
                'css_classes' => isset($fields['post_title']['css_classes']) ? $fields['post_title']['css_classes'] : ''
            ),
            'post_content' => array(
                'title' => esc_html__('description', 'woocommerce-bulk-editor'),
                'css_classes' => isset($fields['post_content']['css_classes']) ? $fields['post_content']['css_classes'] : ''
            ),
            'post_excerpt' => array(
                'title' => esc_html__('short description', 'woocommerce-bulk-editor'),
                'css_classes' => isset($fields['post_excerpt']['css_classes']) ? $fields['post_excerpt']['css_classes'] : ''
            ),
            'post_name' => array(
                'title' => esc_html__('product slug', 'woocommerce-bulk-editor'),
                'css_classes' => isset($fields['post_name']['css_classes']) ? $fields['post_name']['css_classes'] : ''
            ),
            'sku' => array(
                'title' => esc_html__('SKU', 'woocommerce-bulk-editor'),
                'css_classes' => isset($fields['sku']['css_classes']) ? $fields['sku']['css_classes'] : ''
            )
        );

        $this->other_keys = array(
            'post_status' => array(
                'title' => esc_html__('post status', 'woocommerce-bulk-editor'),
                'options' => $fields['post_status']['select_options'],
                'direct' => $fields['post_status']['direct'],
                'css_classes' => isset($fields['post_status']['css_classes']) ? $fields['post_status']['css_classes'] : ''
            ),
            'stock_status' => array(
                'title' => esc_html__('stock status', 'woocommerce-bulk-editor'),
                'options' => $fields['stock_status']['select_options'],
                'direct' => $fields['stock_status']['direct'],
                'css_classes' => isset($fields['stock_status']['css_classes']) ? $fields['stock_status']['css_classes'] : ''
            ),
            'tax_status' => array(
                'title' => esc_html__('tax status', 'woocommerce-bulk-editor'),
                'options' => $fields['tax_status']['select_options'],
                'direct' => $fields['tax_status']['direct'],
                'css_classes' => isset($fields['tax_status']['css_classes']) ? $fields['tax_status']['css_classes'] : ''
            ),
            'catalog_visibility' => array(
                'title' => esc_html__('catalog visibility', 'woocommerce-bulk-editor'),
                'options' => $fields['catalog_visibility']['select_options'],
                'direct' => $fields['catalog_visibility']['direct'],
                'css_classes' => isset($fields['catalog_visibility']['css_classes']) ? $fields['catalog_visibility']['css_classes'] : ''
            ),
            'product_type' => array(
                'title' => esc_html__('product type', 'woocommerce-bulk-editor'),
                'options' => $fields['product_type']['select_options'],
                'direct' => $fields['product_type']['direct'],
                'css_classes' => isset($fields['product_type']['css_classes']) ? $fields['product_type']['css_classes'] : ''
            ),
            'featured' => array(
                'title' => esc_html__('featured', 'woocommerce-bulk-editor'),
                'options' => $fields['featured']['select_options'],
                'direct' => $fields['featured']['direct'],
                'css_classes' => isset($fields['featured']['css_classes']) ? $fields['featured']['css_classes'] : ''
            ),
        );
        //***

        $options1 = array(
            'invalue' => esc_html__('increase by value', 'woocommerce-bulk-editor'),
            'devalue' => esc_html__('decrease by value', 'woocommerce-bulk-editor'),
            'inpercent' => esc_html__('increase by %', 'woocommerce-bulk-editor'),
            'depercent' => esc_html__('decrease by %', 'woocommerce-bulk-editor'),
            'new' => esc_html__('set new', 'woocommerce-bulk-editor')
        );

        $options2 = array(
            'invalue' => esc_html__('increase by value', 'woocommerce-bulk-editor'),
            'devalue' => esc_html__('decrease by value', 'woocommerce-bulk-editor'),
            'delete' => esc_html__('delete', 'woocommerce-bulk-editor'),
            'new' => esc_html__('set new', 'woocommerce-bulk-editor')
        );

        //***

        $this->num_keys = array(
            'regular_price' => array(
                'title' => esc_html__('regular price', 'woocommerce-bulk-editor'),
                'direct' => $fields['regular_price']['direct'],
                'options' => array(
                    'invalue' => esc_html__('increase by value', 'woocommerce-bulk-editor'),
                    'devalue' => esc_html__('decrease by value', 'woocommerce-bulk-editor'),
                    'inpercent' => esc_html__('increase by %', 'woocommerce-bulk-editor'),
                    'depercent' => esc_html__('decrease by %', 'woocommerce-bulk-editor'),
                    'inpercent_sale_price' => esc_html__('sale price plus %', 'woocommerce-bulk-editor'),
                    'invalue_sale_price' => esc_html__('sale price plus value', 'woocommerce-bulk-editor'),
                    'new' => esc_html__('set new', 'woocommerce-bulk-editor')
                ),
                'css_classes' => isset($fields['regular_price']['css_classes']) ? $fields['regular_price']['css_classes'] : ''
            ),
            'sale_price' => array(
                'title' => esc_html__('sale price', 'woocommerce-bulk-editor'),
                'direct' => $fields['sale_price']['direct'],
                'options' => array(
                    'invalue' => esc_html__('increase by value', 'woocommerce-bulk-editor'),
                    'devalue' => esc_html__('decrease by value', 'woocommerce-bulk-editor'),
                    'inpercent' => esc_html__('increase by %', 'woocommerce-bulk-editor'),
                    'depercent' => esc_html__('decrease by %', 'woocommerce-bulk-editor'),
                    'depercent_regular_price' => esc_html__('regular price minus %', 'woocommerce-bulk-editor'),
                    'devalue_regular_price' => esc_html__('regular price minus value', 'woocommerce-bulk-editor'),
                    'new' => esc_html__('set new', 'woocommerce-bulk-editor')
                ),
                'css_classes' => isset($fields['sale_price']['css_classes']) ? $fields['sale_price']['css_classes'] : ''
            ),
            'stock_quantity' => array(
                'title' => esc_html__('in stock quantity', 'woocommerce-bulk-editor'),
                'direct' => $fields['stock_quantity']['direct'],
                'options' => $options2,
                'css_classes' => isset($fields['stock_quantity']['css_classes']) ? $fields['stock_quantity']['css_classes'] : ''
            ),
            'download_expiry' => array(
                'title' => esc_html__('download expiry', 'woocommerce-bulk-editor'),
                'direct' => $fields['download_expiry']['direct'],
                'options' => $options2,
                'css_classes' => isset($fields['download_expiry']['css_classes']) ? $fields['download_expiry']['css_classes'] : ''
            ),
            'download_limit' => array(
                'title' => esc_html__('download limit', 'woocommerce-bulk-editor'),
                'direct' => $fields['download_limit']['direct'],
                'options' => $options2,
                'css_classes' => isset($fields['download_limit']['css_classes']) ? $fields['download_limit']['css_classes'] : ''
            )
        );
    }

    //ajax
    public function woobe_bulk_draw_gallery_btn() {
        $images = [];
        parse_str($_REQUEST['images'], $images); //sanitize below in array_map
        $data = array();
        $woobe_gallery_images = isset($images['woobe_gallery_images']) ? $images['woobe_gallery_images'] : array();
        //sanitizing to intval
        $woobe_gallery_images = array_map(function ($item) {
            return intval($item); //sanitize intval
        }, $woobe_gallery_images);

        $data['html'] = WOOBE_HELPER::draw_gallery_popup_editor_btn(sanitize_text_field($_REQUEST['field']), 0, $woobe_gallery_images);
        $data['images_ids'] = implode(',', $woobe_gallery_images); //for any case, but now we not need it because updating of products applies by serialized data


        die(json_encode($data));
    }

    //ajax
    public function woobe_bulk_draw_download_files_btn() {
        $files = [];
        parse_str($_REQUEST['files'], $files);
        $files = WOOBE_HELPER::sanitize_array($files);
        $count = 0;

        if (isset($files['_wc_file_names'])) {
            $count = count($files['_wc_file_names']);
        }

        echo WOOBE_HELPER::draw_downloads_popup_editor_btn(sanitize_text_field($_REQUEST['field']), 0, $count);

        exit;
    }

    //ajax
    public function woobe_bulk_draw_cross_sells_btn() {
        $products = [];
        parse_str($_REQUEST['products'], $products);

        $ids = array();
        if (isset($products['woobe_prod_ids'])) {
            $ids = $products['woobe_prod_ids'];
        }

        $ids = array_map(function ($item) {
            return intval($item); //sanitize intval
        }, $ids);

        echo WOOBE_HELPER::draw_cross_sells_popup_editor_btn(sanitize_text_field($_REQUEST['field']), 0, $ids);

        exit;
    }

    //ajax
    public function woobe_bulk_draw_upsell_ids_btn() {
        $products = [];
        parse_str($_REQUEST['products'], $products);

        $ids = array();
        if (isset($products['woobe_prod_ids'])) {
            $ids = array_map(function ($item) {
                return intval($item); //sanitize intval
            }, $products['woobe_prod_ids']);
        }

        echo WOOBE_HELPER::draw_upsells_popup_editor_btn($_REQUEST['field'], 0, $ids);

        exit;
    }

    //ajax
    public function woobe_bulk_draw_grouped_ids_btn() {
        $products = [];
        parse_str($_REQUEST['products'], $products); //sanitize below

        $ids = array();
        if (isset($products['woobe_prod_ids'])) {
            $ids = array_map(function ($item) {
                return intval($item); //sanitize intval
            }, $products['woobe_prod_ids']);
        }

        echo WOOBE_HELPER::draw_grouped_popup_editor_btn(sanitize_text_field($_REQUEST['field']), 0, $ids);

        exit;
    }

    //ajax
    public function woobe_bulk_get_att_terms() {

        $drop_downs = '';
        if (!empty($_REQUEST['attributes'])) {
            foreach ($_REQUEST['attributes'] as $pa) {
                $pa = sanitize_text_field($pa); //sanitize attribute name

                $terms = WOOBE_HELPER::get_taxonomies_terms_hierarchy($pa);
                if (!empty($terms)) {
                    $options = array();
                    $options[''] = esc_html__('not selected', 'woocommerce-bulk-editor');
					$options['-1'] = esc_html__('Any', 'woocommerce-bulk-editor');
                    foreach ($terms as $t) {
                        $options[$t['slug']] = $t['name'];
                    }

                    $drop_downs .= WOOBE_HELPER::draw_select(array(
                                'field' => 0,
                                'product_id' => 0,
                                'class' => '',
                                'options' => $options,
                                'name' => 'woobe_bulk[combination_attributes][' . sanitize_text_field($_REQUEST['hash_key']) . '][' . $pa . ']'
                    ));
                }
            }
        }

        die($drop_downs);
    }

}
