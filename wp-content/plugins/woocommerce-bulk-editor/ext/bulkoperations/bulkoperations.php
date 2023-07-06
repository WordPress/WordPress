<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//any operations with variations only
final class WOOBE_BULKOPERATIONS extends WOOBE_EXT {

    protected $slug = 'bulkoperations'; //unique

    //protected $is = 'external';

    public function __construct() {
        add_action('woobe_ext_scripts', array($this, 'woobe_ext_scripts'), 1);
        add_action('woobe_tools_panel_buttons', array($this, 'woobe_tools_panel_buttons'), 1);
        add_action('woobe_page_end', array($this, 'woobe_page_end'), 1);

        //ajax
        add_action('wp_ajax_woobe_bulkoperations_get_att_terms', array($this, 'woobe_bulkoperations_get_att_terms'), 1);
        add_action('wp_ajax_woobe_bulkoperations_get_possible_combos', array($this, 'woobe_bulkoperations_get_possible_combos'), 1);
        add_action('wp_ajax_woobe_bulkoperations_get_prod_count', array($this, 'woobe_bulkoperations_get_prod_count'), 1);
        add_action('wp_ajax_woobe_bulkoperations_apply_combinations', array($this, 'woobe_bulkoperations_apply_combinations'), 1);
        add_action('wp_ajax_woobe_bulkoperations_apply_default_combination', array($this, 'woobe_bulkoperations_apply_default_combination'), 1);
        add_action('wp_ajax_woobe_bulkoperations_get_product_variations', array($this, 'woobe_bulkoperations_get_product_variations'), 1);
        add_action('wp_ajax_woobe_bulkoperations_delete', array($this, 'woobe_bulkoperations_delete'), 1);
        add_action('wp_ajax_woobe_bulkoperations_ordering', array($this, 'woobe_bulkoperations_ordering'), 1);
        add_action('wp_ajax_woobe_bulkoperations_swap', array($this, 'woobe_bulkoperations_swap'), 1);
        add_action('wp_ajax_woobe_bulkoperations_attaching', array($this, 'woobe_bulkoperations_attaching'), 1);
        add_action('wp_ajax_woobe_bulkoperations_visibility', array($this, 'woobe_bulkoperations_visibility'), 1);
    }

    public function woobe_ext_scripts() {
        wp_enqueue_script('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/js/' . $this->slug . '.js', array(), WOOBE_VERSION);
        wp_enqueue_script('woobe_ext_' . $this->slug . '-2', $this->get_ext_link() . 'assets/js/' . $this->slug . '-2.js', array(), WOOBE_VERSION);
        wp_enqueue_script('woobe_ext_' . $this->slug . '-3', $this->get_ext_link() . 'assets/js/' . $this->slug . '-3.js', array(), WOOBE_VERSION);
        wp_enqueue_script('woobe_ext_' . $this->slug . '-4', $this->get_ext_link() . 'assets/js/' . $this->slug . '-4.js', array(), WOOBE_VERSION);
        wp_enqueue_script('woobe_ext_' . $this->slug . '-5', $this->get_ext_link() . 'assets/js/' . $this->slug . '-5.js', array(), WOOBE_VERSION);
        wp_enqueue_script('woobe_ext_' . $this->slug . '-6', $this->get_ext_link() . 'assets/js/' . $this->slug . '-6.js', array(), WOOBE_VERSION);
        wp_enqueue_script('woobe_ext_' . $this->slug . '-7', $this->get_ext_link() . 'assets/js/' . $this->slug . '-7.js', array(), WOOBE_VERSION);
        wp_enqueue_style('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/css/' . $this->slug . '.css', array(), WOOBE_VERSION);
        ?>
        <script>
            lang.<?php echo $this->slug ?> = {};
            lang.<?php echo $this->slug ?>.going = '<?php esc_html_e('ATTENTION: Variations Advanced Bulk Operation is going', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.finished = '<?php esc_html_e('Variations Advanced Bulk Operation is finished', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.finished2 = '<?php esc_html_e('Attaching of the default combination for the products variations is finished!', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.finished3 = '<?php esc_html_e('Deleting of the products variations is finished', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.finished4 = '<?php esc_html_e('Ordering of the products variations is finished', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.finished5 = '<?php esc_html_e('Swap of variations is finished', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.finished6 = '<?php esc_html_e('Attaching of the products variations is finished', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.generating = '<?php esc_html_e('Generating possible combinations', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.generated = '<?php esc_html_e('Possible combinations been generated.', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.no_combinations = '<?php esc_html_e('Combination(s) not selected!', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.not_selected_var = '<?php esc_html_e('variation is not selected', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.no_vars = '<?php esc_html_e('the product has no variations', 'woocommerce-bulk-editor') ?>';
        </script>
        <?php
    }

    public function woobe_tools_panel_buttons() {
        ?>
        <a href="#" class="button button-secondary woobe_tools_panel_newvars_btn" title="<?php esc_html_e('Variations Advanced Bulk Operations', 'woocommerce-bulk-editor') ?>"></a>
        <?php
    }

    public function woobe_page_end() {
        $data = array();
        $data['attributes'] = wc_get_attribute_taxonomies();
        echo WOOBE_HELPER::render_html($this->get_ext_path() . 'views/panel.php', $data);
    }

    //ajax
    public function woobe_bulkoperations_get_att_terms() {
        die(json_encode(WOOBE_HELPER::get_taxonomies_terms_hierarchy(sanitize_text_field($_REQUEST['attribute']))));
    }

    //ajax
    public function woobe_bulkoperations_get_possible_combos() {
        try {
            $res = "";
            if (isset($_REQUEST['arrays'])) {
                //no db writing, ajax to DOM
                $res = json_encode($this->generate_combinations($_REQUEST['arrays']));
            }
        } catch (Exception $e) {
            print_r($e);
        }
        die($res);
    }

    //https://gist.github.com/fabiocicerchia/4556892
    private function generate_combinations($data, &$all = array(), array $group = array(), $value = null, $i = 0) {
        if (!is_array($data)) {
            $data = array();
        }
        $keys = array_keys($data);
        if (isset($value) === true) {
            array_push($group, intval($value));
        }

        if ($i >= count($data)) {
            array_push($all, $group);
        } else {
            $currentKey = $keys[$i];
            $currentElement = $data[$currentKey];
            foreach ($currentElement as $val) {
                $this->generate_combinations($data, $all, $group, $val, $i + 1);
            }
        }

        return $all;
    }

    //ajax
    public function woobe_bulkoperations_get_prod_count() {

        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }

        //***

        $products = $this->products->gets(array(
            'fields' => 'ids',
            'no_found_rows' => true
        ));
        echo json_encode($products->posts);

        exit;
    }

    //ajax
    public function woobe_bulkoperations_apply_combinations() {

        if (!isset($_REQUEST['products_ids'])) {
            die('0');
        }

        //***

        $products_ids = array_map(function($item) {
            return intval($item); //sanitize intval
        }, $_REQUEST['products_ids']);

        $combinations = WOOBE_HELPER::sanitize_array((array) $_REQUEST['combinations']);

        $possible_attributes = array();
        //for set_product_attributes
        $set_product_attributes = array();

        //***

        if (!empty($products_ids) AND!empty($combinations)) {

            //lets prepare combinations for applying
            $terms = array();
            foreach ($combinations as $comb) {
                if (!empty($comb)) {
                    foreach ($comb as $term_id) {
                        $terms[$term_id] = get_term($term_id, '', ARRAY_A);
                    }
                }
            }

            //***

            foreach ($terms as $t) {
                $set_product_attributes[$t['taxonomy']][] = $t['term_id'];
            }

            foreach ($combinations as $k => $comb) {
                if (!empty($comb)) {
                    foreach ($comb as $term_id) {
                        $possible_attributes[$k][strtolower(urlencode($terms[$term_id]['taxonomy']))] = $terms[$term_id]['slug'];
                    }
                }
            }

            //***
            //wp-content\plugins\woocommerce\includes\class-wc-ajax.php -> public static function link_all_variations
            wc_maybe_define_constant('WC_MAX_LINKED_VARIATIONS', 50);
            wc_set_time_limit(0);


            /*
             * Leaved as an example of data structure
              $possible_attributes = array(
              'pa_color' => array('black', 'blue', 'green'),
              'pa_size' => array('xl', '2xxl')
              );

              $set_product_attributes = array(
              'pa_color' => array(1, 2, 3),
              'pa_size' => array(4, 5)
              );
             */


            if (!empty($possible_attributes)) {

                foreach ($products_ids as $product_id) {

                    $product_id = intval($product_id);
                    $product = $this->products->get_product($product_id);

                    //if product is not variable - no variations!!
                    if (!$product->is_type('variable')) {
                        continue;
                    }

                    //attach attributes if they not been attached
                    foreach ($set_product_attributes as $field_key => $value) {
                        $this->products->set_product_attributes($product_id, $field_key, $value, 'append', array('set_variation'));

					}

                    //***
                    // Get existing variations so we don't create duplicates.
                    $existing_variations = array_map('wc_get_product', $product->get_children());
                    $parent_sku = $product->get_sku();
                    $existing_attributes = array();

                    foreach ($existing_variations as $existing_variation) {
                        if ($existing_variation) {
                            $existing_attributes[] = $existing_variation->get_attributes();
                        }
                    }


                    $regular_price = get_post_meta($product_id, '_regular_price', true);
                    $sale_price = get_post_meta($product_id, '_sale_price', true);
                    //***

                    foreach ($possible_attributes as $possible_attribute) {

                        if (in_array($possible_attribute, $existing_attributes)) {
                            continue;
                        }

                        $variation = new WC_Product_Variation();
                        $variation->set_parent_id($product_id);
                        $variation->set_attributes($possible_attribute);
						
						$variation->set_status(apply_filters('woobe_new_variation_product_status', 'publish'));
						

                        if ($regular_price) {
                            $variation->set_regular_price($regular_price);
                        }
                        if ($sale_price) {
                            $variation->set_sale_price($sale_price);
                        }


                        //do_action('product_variation_linked', $variation->save());
                        $variation->save();

                        //to avoid the same SKU as in the parent
                        if (empty($parent_sku)) {
                            $variation->set_sku('sku-' . $variation->get_id());
                        } else {
                            $variation->set_sku($parent_sku . '-' . $variation->get_id());
                        }
                        $variation->set_manage_stock(0);
                        $variation->set_stock_quantity(0);
                        $variation->save();

                        //clean_post_cache($variation->get_id());
                        //wp_cache_flush();
                    }


                    //***
                    //set order of variations
                    $data_store = $product->get_data_store();
                    $data_store->sort_all_product_variations($product->get_id());
                }
            }
        }

        die('done');
    }

    //************ TAB 2
    //ajax
    public function woobe_bulkoperations_apply_default_combination() {

        if (!isset($_REQUEST['products_ids'])) {
            die('0');
        }

        //***

        $products_ids = array_map(function($item) {
            return intval($item); //sanitize intval
        }, $_REQUEST['products_ids']);
        
        $combination = WOOBE_HELPER::sanitize_array((array) $_REQUEST['combination']);

        if (!empty($combination) AND!empty($products_ids)) {
            foreach ($products_ids as $product_id) {
                $product = $this->products->get_product($product_id);
                $product->set_props(array(
                    'default_attributes' => $combination
                ));
                $product->save();

                //*** also lets set order of attributes which customer will see on the product page
                $meta = get_post_meta($product_id, '_product_attributes', true);

                $new_meta = array();
                $counter = 0;
                foreach (array_keys($combination) as $meta_key) {
                    if (isset($meta[$meta_key])) {
                        $new_meta[$meta_key] = $meta[$meta_key];
                        $new_meta[$meta_key]['position'] = $counter;
                        unset($meta[$meta_key]);
                        $counter++;
                    }
                }

                //if we have more attributes than trying to save in order
                if (!empty($meta)) {
                    foreach ($meta as $meta_key => $value) {
                        $new_meta[$meta_key] = $value;
                        $new_meta[$meta_key]['position'] = $counter;
                        $counter++;
                    }
                }

                update_post_meta($product_id, '_product_attributes', $new_meta);

                //woocommerce\includes\admin\meta-boxes\class-wc-meta-box-product-data.php
                //public static function save($post_id, $post)
                do_action('woocommerce_process_product_meta_' . $product->get_type(), $product_id);
            }
        }

        exit;
    }

    //************ TAB 3
    //ajax
    public function woobe_bulkoperations_delete() {

        if (!isset($_REQUEST['products_ids'])) {
            die('0');
        }

        //***

        $removed_ids = array();
        $products_ids = array_map(function($item) {
            return intval($item); //sanitize intval
        }, $_REQUEST['products_ids']);

        $combination = array();
        if (isset($_REQUEST['combination'])) {
            $combination = WOOBE_HELPER::sanitize_array((array) $_REQUEST['combination']);
        }

        if (!empty($products_ids)) {
            foreach ($products_ids as $product_id) {
                $product = $this->products->get_product($product_id);

                if (method_exists($product, 'is_type') AND $product->is_type('variable')) {
                    $vars = $product->get_children();

                    if (!empty($vars)) {
                        foreach ($vars as $var_id) {

                            $var = $this->products->get_product($var_id);
                            $av = $product->get_available_variation($var);

                            if ($_REQUEST['delete_how'] == 'combo') {

                                if (!empty($combination)) {
                                    if (count($av['attributes']) === count($combination)) {
                                        $is = FALSE;
                                        $attributes = array();

                                        //fix for non-latin symbols
                                        if (!empty($av['attributes'])) {
                                            foreach ($av['attributes'] as $k => $v) {
                                                $attributes[urldecode($k)] = urldecode($v);
                                            }
                                        }


                                        foreach ($combination as $comb_key => $comb_value) {
                                            if (isset($attributes['attribute_' . urldecode($comb_key)]) AND $attributes['attribute_' . urldecode($comb_key)] == urldecode($comb_value)) {
                                                $is = TRUE;
                                            } else {
                                                $is = FALSE;
                                                break;
                                            }
                                        }

                                        //removing
                                        if ($is) {
                                            $var_prod = $this->products->get_product($av['variation_id']);
                                            $removed_ids[] = $av['variation_id'];
                                            $var_prod->delete(true);
                                        }
                                    }
                                }
                            } else {
                                //all
                                $var_prod = $this->products->get_product($av['variation_id']);
                                $removed_ids[] = $av['variation_id'];
								if ($var_prod){ 
									$var_prod->delete(true);
								}
                            }
                        }
                    }
                }

                //***
            }
        }

        //***
        $removed_ids = json_encode($removed_ids);
        die($removed_ids);
    }

    //************ TAB 4
    //ajax
    public function woobe_bulkoperations_get_product_variations() {
        $product_id = intval($_REQUEST['product_id']);
        $available_variations = array();

        if ($product_id > 0) {
            try {
                $product = $this->products->get_product($product_id);

                if (is_object($product) AND $product->is_type('variable')) {
                    $vars = $product->get_children();

                    if (!empty($vars)) {
                        foreach ($vars as $var_id) {
                            $var = $this->products->get_product($var_id);
                            $av = $product->get_available_variation($var);
                            $available_variations[$var_id]['title'] = str_replace($product->get_title(), '', $this->products->generate_product_title($var));
                            $available_variations[$var_id]['attributes'] = $av['attributes'];
                        }
                    }

                    //print_r($available_variations);
                }
            } catch (Exception $e) {
                //***
            }
        }

        die(json_encode($available_variations));
    }

    //ajax
    public function woobe_bulkoperations_ordering() {

        $products_ids = array_map(function($item) {
            return intval($item); //sanitize intval
        }, $_REQUEST['products_ids']);

        $combination = WOOBE_HELPER::sanitize_array((array) $_REQUEST['combination']);

        //***

        if (!empty($products_ids) AND!empty($combination)) {
            foreach ($products_ids as $product_id) {
                $available_variations = array();
                $product = $this->products->get_product($product_id);
                $childrens = $product->get_children();

                if (!empty($childrens)) {
                    foreach ($childrens as $child_id) {
                        $variation = $this->products->get_product($child_id);
                        $available_variations[] = $product->get_available_variation($variation);
                    }

                    //***

                    if (!empty($available_variations)) {
                        foreach ($available_variations as $var) {
                            $att = $var['attributes'];
                            $num = -1;
                            foreach ($combination as $n => $comb_var) {
                                //lets look is it the same set of attributes as in $var
                                $ak_att = array_keys($att);
                                $ak_cv = array_keys($comb_var);
                                sort($ak_att);
                                sort($ak_cv);
                                if ($ak_att === $ak_cv) {
                                    $av_att = array_values($att);
                                    $av_cv = array_values($comb_var);
                                    sort($av_att);
                                    sort($av_cv);
                                    if ($av_att === $av_cv) {
                                        $num = $n;
                                        break;
                                    }
                                }
                            }

                            //***
                            if ($num > -1) {
                                $this->products->update_page_field(intval($var['variation_id']), 'menu_order', $num);
                            }
                        }
                    }
                }
            }
        }


        exit;
    }

    //************ TAB 5 swap
    //ajax
    public function woobe_bulkoperations_swap() {

        $do = true;
        if (!empty($_REQUEST['from']) AND!empty($_REQUEST['to'])) {
            if ($_REQUEST['from']['attribute'] == $_REQUEST['to']['attribute']) {
                if ($_REQUEST['from']['term'] == $_REQUEST['to']['term']) {
                    $do = false;
                }
            }
        } else {
            $do = false;
        }

        //***

        if ($do) {
            $from_att = sanitize_text_field($_REQUEST['from']['attribute']);
            $to_att = sanitize_text_field($_REQUEST['to']['attribute']);

            //***

            if (!empty($_REQUEST['products_ids'])) {
                foreach ($_REQUEST['products_ids'] as $product_id) {
                    $product_id = intval($product_id);
                    $product = $this->products->get_product($product_id);

                    //***

                    if (!in_array($product->get_type(), array('variable', 'variation'))) {
                        continue;
                    }

                    //***

                    if ($product->is_type('variable')) {
                        $childrens = $product->get_children();
                        $parent_id = $product_id;
                        $parent_product = $product;
                    } else {
                        $childrens = array($product_id);
                        $parent_id = $product->get_parent_id();
                        $parent_product = $this->products->get_product($parent_id);
                    }


                    $parent_terms = wc_get_product_terms($parent_id, $to_att, array('fields' => 'slugs'));

                    if (!empty($childrens)) {
                        foreach ($childrens as $child_id) {
                            $variation = $this->products->get_product($child_id);
                            $available_variations = $parent_product->get_available_variation($variation);

                            //***

                            if (isset($available_variations['attributes']) AND!empty($available_variations['attributes'])) {
                                if (isset($available_variations['attributes']['attribute_' . $from_att])) {
                                    if ($available_variations['attributes']['attribute_' . $from_att] === sanitize_text_field($_REQUEST['from']['term'])) {

                                        $possible_attributes = $available_variations['attributes'];

                                        if ($from_att !== $to_att) {
                                            unset($possible_attributes['attribute_' . $from_att]);
                                        }

                                        $possible_attributes['attribute_' . $to_att] = sanitize_text_field($_REQUEST['to']['term']);

                                        //if such attribute not selected in the parent product Attributes tab lets attach it here
                                        if (!in_array($_REQUEST['to']['term'], $parent_terms)) {
                                            $p_terms = [];
											foreach($parent_terms as $t_slug){
												$t_term= get_term_by('slug', sanitize_text_field($t_slug), $to_att);
												if($t_term){
													$p_terms[] = $t_term->term_id;
												}
												
											}
                                            $t = get_term_by('slug', sanitize_text_field($_REQUEST['to']['term']), $to_att);
                                            $p_terms[] =  $t->term_id;

                                            $this->products->update_page_field($parent_id, $to_att, $p_terms);
                                        }

                                        $variation->set_attributes($possible_attributes);
                                        $variation->save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        die('done');
    }

    //************ TAB 6 attaching
    //ajax
    public function woobe_bulkoperations_attaching() {

        $selected_attribute = sanitize_text_field($_REQUEST['selected_attribute']);
        $attaching_att = $_REQUEST['attaching_att']; //sanitizing in cycle
        $products_ids = $_REQUEST['products_ids']; //sanitizing in cycle
        //***

        if (!empty($products_ids)) {
            if (!empty($selected_attribute) AND $attaching_att) {
                foreach ($products_ids as $product_id) {
                    $product_id = intval($product_id); //sanitizing

                    $product = $this->products->get_product($product_id);

                    if ($product->is_type('variable')) {
                        $childrens = $product->get_children();

                        //***
                        if (!empty($childrens)) {
                            foreach ($childrens as $child_id) {
                                $variation = $this->products->get_product($child_id);
                                $available_variations = $product->get_available_variation($variation);

                                //***

                                if (isset($available_variations['attributes']) AND!empty($available_variations['attributes'])) {

                                    //if its empty - will be filled up, if not empty will be replaced if not selected 'ignore'
                                    $possible_attributes = $available_variations['attributes'];

                                    //***

                                    foreach ($attaching_att as $set) {

                                        if (isset($possible_attributes['attribute_' . $selected_attribute])) {
                                            unset($possible_attributes['attribute_' . $selected_attribute]);
                                        }

                                        if (isset($set['attributes']['attribute_' . $selected_attribute])) {
                                            unset($set['attributes']['attribute_' . $selected_attribute]);
                                        }

                                        //***

                                        $att_set_now_keys = array_keys($possible_attributes);
                                        $set_keys = array_keys($set['attributes']);
                                        sort($att_set_now_keys);
                                        sort($set_keys);

                                        if ($att_set_now_keys === $set_keys) {
                                            $att_set_now_vals = array_values($possible_attributes);
                                            $set_vals = array_values($set['attributes']);
                                            sort($att_set_now_vals);
                                            sort($set_vals);
                                            if ($att_set_now_vals === $set_vals AND $set['value'] !== 'woobe-ignore') {
                                                $possible_attributes['attribute_' . $selected_attribute] = sanitize_text_field($set['value']);

												//if such attribute not selected in the parent product Attributes tab lets attach it here
												$parent_terms = wc_get_product_terms($product_id, $selected_attribute, array('fields' => 'ids'));
												if (!in_array($set['value'], $parent_terms)) {
													$t = get_term_by('slug', sanitize_text_field($set['value']), $selected_attribute);
													$parent_terms[] = $t->term_id;
													$this->products->update_page_field($product_id, $selected_attribute, $parent_terms, '', array('set_variation'));

												}


                                                $variation->set_attributes($possible_attributes);
                                                $variation->save();
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                        }
						
						//fix  visibility
						$meta = get_post_meta($product_id, '_product_attributes', true);
						if (is_array($meta)) {
							foreach ($meta as $pa_key => $item) {
								if ($item['name'] == 'pa_size') {
									$meta[$pa_key]['is_visible'] = 1;
									$meta[$pa_key]['is_variation'] = 1;
								}
							}
						}
                        update_post_meta($product_id, '_product_attributes', $meta);						
                    }
                }
            }
        }

        //***

        die('done');
    }

    //************ TAB 7 visibility
    //ajax
    public function woobe_bulkoperations_visibility() {

        if (isset($_REQUEST['products_ids'])) {
            $products_ids = $_REQUEST['products_ids']; //sanitizing in cycle
            $vis_data = $_REQUEST['vis_data']; //sanitizing in cycle

            if (!empty($products_ids) AND!empty($vis_data)) {
                foreach ($products_ids as $product_id) {
                    $product_id = intval($product_id);

                    $product = $this->products->get_product($product_id);
                    if ($product->is_type('variable')) {
                        $meta = get_post_meta($product_id, '_product_attributes', true);


                        foreach ($vis_data as $vis) {
                            if (is_array($meta)) {
                                foreach ($meta as $pa_key => $item) {
                                    if ($item['name'] == $vis['attribute']) {
                                        $meta[$pa_key]['is_visible'] = intval($vis['is_visible']);
                                        $meta[$pa_key]['is_variation'] = intval($vis['is_variation']);
                                    }
                                }
                            }
                        }
                        update_post_meta($product_id, '_product_attributes', $meta);
                    }
                }
            }
        }

        die('done');
    }

}
