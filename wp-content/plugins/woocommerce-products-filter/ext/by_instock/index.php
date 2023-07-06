<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

final class WOOF_EXT_BY_INSTOCK extends WOOF_EXT {

    public $type = 'by_html_type';
    public $html_type = 'by_instock'; //your custom key here
    public $index = 'stock';
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

        self::$includes['js']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'js/' . $this->html_type . '.js';
        self::$includes['css']['woof_' . $this->html_type . '_html_items'] = $this->get_ext_link() . 'css/' . $this->html_type . '.css';
        self::$includes['js_init_functions'][$this->html_type] = 'woof_init_instock';
        self::$includes['js_lang_custom'][$this->index] = esc_html__('In stock', 'woocommerce-products-filter');
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

        if (isset($request['stock'])) {
            if ($request['stock'] == 'instock') {
                $meta_query[] = array(
                    'key' => '_stock_status',
                    'value' => 'outofstock', //instock,outofstock
                    'compare' => 'NOT IN'
                );
            }

            if ($request['stock'] == 'outofstock') {
                $meta_query[] = array(
                    array(
                        'key' => '_stock_status',
                        'value' => 'outofstock', //instock,outofstock
                        'compare' => 'IN'
                    )
                );
            }
        }

//			$product_visibility_term_ids = wc_get_product_visibility_term_ids();
//			$query_args['tax_query'][] = array(
//				array(
//					'taxonomy' => 'product_visibility',
//					'field'    => 'term_taxonomy_id',
//					'terms'    => $product_visibility_term_ids['outofstock'],
//					'operator' => 'NOT IN',
//				),
//			); // WPCS: slow query ok.
        //+++


        $use_for = isset(woof()->settings['by_instock']['use_for']) ? woof()->settings['by_instock']['use_for'] : 'simple';
        if ($use_for == 'both') {
            add_filter('posts_where', array($this, 'posts_where'), 9999);
        }

        //***

        return $meta_query;
    }

    public function posts_where($where = '') {
		
        global $WOOF, $wpdb;
        $request = woof()->get_request_data();
		if(!is_array($request)){
			$request = array();		
		}
        static $where_instock = "";

        //cache on the fly
        if (!empty($where_instock)) {
            return $where . $where_instock;
        }

        //+++

        if (WOOF_REQUEST::isset('woof_current_recount')) {
            $dynamic_request = WOOF_REQUEST::get('woof_current_recount');
            if (isset($dynamic_request["slug"]) AND isset($dynamic_request["taxonomy"])) {
                if (isset($request[$dynamic_request["taxonomy"]])) {
                    $request[$dynamic_request["taxonomy"]] = $request[$dynamic_request["taxonomy"]] . "," . $dynamic_request["slug"];
                } else {

                    $request[$dynamic_request["taxonomy"]] = $dynamic_request["slug"];
                }
            }
        }

        if (isset($request['stock'])) {
            if ($request['stock'] == 'instock') {

                $taxonomies = woof()->get_taxonomies();
                $prod_attributes = array();
                foreach ($taxonomies as $key => $value) {
                    if (substr($key, 0, 3) == 'pa_') {
                        $prod_attributes[] = $key;
                    }
                }

                $prod_attributes_in_request = array();
                if (!empty($prod_attributes)) {
                    foreach ($prod_attributes as $value) {
                        if (in_array($value, array_keys($request))) {
                            $prod_attributes_in_request[] = $value;
                        }
                    }
					if (!empty($prod_attributes_in_request)) {
						

						$new_db = isset(woof()->settings['by_instock']['new_db']) ? woof()->settings['by_instock']['new_db'] : 1;
						if ($new_db) {

							$attr_in_search = array();
							foreach ($prod_attributes_in_request as $attr_slug) {
								$term_slugs = explode(',', $request[$attr_slug]);
								foreach ($term_slugs as $slug) {
									$term = get_term_by('slug',$slug, $attr_slug );
									if($term  && !is_wp_error( $term  )){
										$attr_in_search[urlencode($attr_slug)][] = $term->term_id;
									}
								}
							}

							//generate SQL
							$attr_sql = array();
							$attr_sql2 = array();
							foreach ($attr_in_search as $tax=>$ids) {
								$attr_sql[] = "(a.taxonomy = '".$tax."' AND a.term_id IN (".implode(', ',$ids)."))";
								$attr_sql2[] = "(a.taxonomy = '".$tax."' AND a.term_id IN (".implode(', ',$ids).") AND a.in_stock=1) ";
							}

							$parent_ids = $wpdb->get_col("SELECT a.product_or_parent_id "
									. "FROM ".$wpdb->prefix ."wc_product_attributes_lookup a "
									. "JOIN  ".$wpdb->prefix ."wc_product_attributes_lookup b on b.product_id=a.product_id "
									. "WHERE a.is_variation_attribute = 1"
									//taxonomy
									. " AND ( "
									. "( ". implode(' OR ', $attr_sql) ." )"
									. " AND a.in_stock=0 ) "

									. " GROUP BY a.product_or_parent_id");		

							$instock_parent_ids = $wpdb->get_col("SELECT a.product_or_parent_id "
									. "FROM ".$wpdb->prefix ."wc_product_attributes_lookup a "
									. "JOIN  ".$wpdb->prefix ."wc_product_attributes_lookup b on b.product_id=a.product_id "
									. "WHERE a.is_variation_attribute = 1"
									//taxonomy
									. " AND "
									. "( ". implode(' OR ', $attr_sql2) ." )"
									. " AND a.in_stock=1 "																
									. " GROUP BY a.product_id");								

							$parent_ids = array_diff($parent_ids, $instock_parent_ids);


							if(empty($parent_ids)){
								return $where;
							}

							$where .= " AND $wpdb->posts.ID NOT IN(". implode(', ', $parent_ids).")";								
						} else {
							$meta_query = array('relation' => 'AND');
							$meta_query[] = array(
								'key' => '_stock_status',
								'value' => 'outofstock'
							);
							$sub_meta_query = array('relation' => 'OR');
							$term_in_cycle = array();

							foreach ($prod_attributes_in_request as $attr_slug) {
								$terms = explode(',', $request[$attr_slug]);
								for ($i = 0; $i < count($terms); $i++) {

									if (isset($term_in_cycle[$terms[$i]])) {
										$t_name = $term_in_cycle[$terms[$i]];
									} else {
										$t_name = $term_in_cycle[$terms[$i]] = $wpdb->get_var("SELECT name FROM $wpdb->terms WHERE slug = '{$terms[$i]}'");
									}
									$sub_meta_query[] = array(
										'key' => 'attribute_' . $attr_slug,
										'value' => $terms[$i]
									);
								}
							}
							$meta_query[] = array($sub_meta_query);

							//if there is price range?
							//if there is more than 2 meta terms in pa_*

							$args = array(
								'nopaging' => true,
								'suppress_filters' => true,
								'post_status' => 'any',
								'post_type' => array('product_variation'),
								'meta_query' => $meta_query
							);

							$query = new WP_Query($args);
							$products = array();
							if ($query->have_posts()) {
								foreach ($query->posts as $p) {
									$products[$p->post_parent] = $p->post_parent;
								}
							}

							if (apply_filters('woof_exclude_existing_variations', false)) {
								foreach ($args['meta_query'] as $key => $data) {
									if (isset($data['key']) AND $data['key'] == '_stock_status') {
										$args['meta_query'][$key]['value'] = 'instock';
									}
								}
								$query_excl = new WP_Query($args);
								if ($query_excl->have_posts()) {
									foreach ($query_excl->posts as $p) {
										if (isset($products[$p->post_parent])) {
											unset($products[$p->post_parent]);
										}
									}
								}
							}


							$product_ids = implode(',', $products);

							//exit;

							if (!empty($product_ids)) {
								$where .= " AND $wpdb->posts.ID NOT IN($product_ids)";
							}							
						}
					
					}

                }
            }
        }

        return $where;
    }

}

WOOF_EXT::$includes['html_type_objects']['by_instock'] = new WOOF_EXT_BY_INSTOCK();
