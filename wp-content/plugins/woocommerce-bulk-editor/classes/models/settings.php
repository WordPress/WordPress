<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOBE_SETTINGS {

    public $active_fields = array();
    public $per_page = 10;
    public $editable = array();
    public $default_sort_by = 'ID';
    public $default_sort = 'desc';
    public $show_admin_bar_menu_btn = 1;
    public $show_thumbnail_preview = 1;
    public $add_vars_to_var_title = 1; //append variations to varitation title - this option is hidden and not optional else
    //public $price_round_decimals = 2; - use wc_get_price_decimals()
    //instead of checkboxes will be beauty switchers, but its will take more time for the table redrawing
    public $load_switchers = 1;
    public $autocomplete_max_elem_count = 10;
    public $quick_search_fieds = "";
	public $override_switcher_fieds = "";
	public $vendor_roles = "";
    public $sync_profiles = 0;
    public $show_text_editor = 0;
    public $no_order = array();
    private $options_key = 'woobe_options_';
	private $global_option_keys = array('vendor_roles');
    public $current_user_role = 'administrator';

    public function __construct() {
        global $WOOBE;
		$this->options_key_global = $this->options_key . "global";
        $this->options_key .= get_current_user_id(); //we need do this to divide different users options set
        $user = wp_get_current_user();
        $role = (array) $user->roles;
        if (!isset($role[0]) && (current_user_can('administrator') || array_intersect(apply_filters('woobe_permit_special_roles', ['administrator']), $role))) {
            $role[0] = 'administrator';
        }

        $this->current_user_role = $role[0];
        $this->init_fields();

        //***

        $counter = 0;
        foreach ($this->active_fields as $f) {
            if ($f['editable']) {
                $this->editable[] = $counter;
            }

            if (!$f['order']) {
                $this->no_order[] = $counter;
            }

            $counter++;
        }

        $this->no_order[] = count($this->active_fields);

        //***
        //init options values
        $options = $this->get_options();
		
        if (!empty($options)) {
            if (!empty($options['options']) AND is_array($options['options'])) {
                foreach ($options['options'] as $key => $v) {
                    if (!is_null($v)) {
                        $this->$key = $v;
                    }
                }
            }
        }

        //max per page to avoid 500 error on weak servers
        if (intval($this->per_page) > 100) {
            $this->per_page = 100;
        }

        if (intval($this->per_page) < 1 || $WOOBE->show_notes) {
            $this->per_page = 10;
        }
    }

    public function get_options() {
		$settings = get_option($this->options_key);
		 if (!empty($settings)) {
			 if (!isset($settings['options']) || !is_array($settings['options'])) {
				 $settings['options'] = array();
			 }
			 $global_settings = get_option($this->options_key_global);

			 if (!is_array($global_settings)) {
				 $global_settings = array();
			 }	
			 $settings['options'] = array_merge($global_settings, $settings['options']);
		 }
		
        return $settings;
    }

    public function update_options($options) {
		if (isset($options['options']) && is_array($options['options'])) {
			$global_options = array();
			foreach($options['options'] as $key => $val) {
				if (in_array($key, $this->global_option_keys)) {
					$global_options[$key] = $val;
					unset($options['options'][$key]); 
					
				}
			}	
			if ( current_user_can('administrator') || in_array($this->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
				update_option($this->options_key_global, $global_options);
			}			
		}
		
        update_option($this->options_key, $options);
		
    }

    public function get_fields($use_roles = true) {
        global $WOOBE;
        static $res = array(); //lets cache it as it uses many times

        if (empty($res)/* AND $use_cache */) {
            $fields = woobe_get_fields();

            //get all woocommerce attributes
            $attribute_taxonomies = wc_get_attribute_taxonomies();
            static $att_fileds = array(); //static is for caching data
            if (!empty($attribute_taxonomies)) {

                if (empty($att_fileds)) {
                    $counter = 0;
                    foreach ($attribute_taxonomies as $a) {

                        $terms_arr = array();

                        $terms = get_terms(array(
                            'taxonomy' => 'pa_' . $a->attribute_name,
                            'hide_empty' => false,
                        ));

                        if (!empty($terms)) {
                            foreach ($terms as $term) {
                                if (is_object($term)) {
                                    $terms_arr[intval($term->term_id)] = $term->name;
                                }
                            }
                        }

                        $direct = TRUE;

                        if ($WOOBE->show_notes) {
                            $direct = FALSE;
                        }

                        if ($counter === 0) {
                            $direct = TRUE;
                        }

                        $att_fileds['pa_' . $a->attribute_name] = array(
                            'show' => 0,
                            'title' => $a->attribute_label,
                            'name' => $a->attribute_name,
                            'field_type' => 'attribute',
                            'type' => 'string',
                            'editable' => TRUE,
                            'edit_view' => 'multi_select',
                            //'edit_view' => 'popup',
                            'direct' => $direct,
                            'select_options' => $terms_arr,
                            'order' => FALSE,
                            'prohibit_product_types' => array('variation'),
                            'shop_manager_visibility' => 1
                        );

                        $counter++;
                    }
                }

                $fields = array_merge($fields, $att_fileds);
            }

            //***
            //get all products taxonomies
            $taxonomy_objects = get_object_taxonomies('product', 'objects');
            unset($taxonomy_objects['product_type']);
            unset($taxonomy_objects['product_visibility']);
            unset($taxonomy_objects['product_shipping_class']);
            static $tax_fileds = array(); //static is for caching data
            if (!empty($taxonomy_objects)) {

                if (empty($tax_fileds)) {
                    $counter = 0;
                    foreach ($taxonomy_objects as $t) {
                        if (substr($t->name, 0, 3) === 'pa_') {
                            continue; //attributes load above as field_type 'attribute'
                        }

                        //***

                        if ($WOOBE->show_notes) {
                            $direct = FALSE;
                            if ($t->name === 'product_cat') {
                                $direct = TRUE;
                            }
                        } else {
                            $direct = TRUE;
                        }

                        //***

                        if ($counter === 0) {
                            $direct = TRUE;
                        }

                        $tax_fileds[$t->name] = array(
                            'show' => 0,
                            'title' => ucfirst(trim(str_replace('Product ', '', $t->label))),
                            'field_type' => 'taxonomy',
                            'taxonomy' => $t->name,
                            'type' => 'array',
                            'editable' => TRUE,
                            'edit_view' => 'popup',
                            'order' => FALSE,
                            'direct' => $direct,
                            'prohibit_product_types' => array('variation'),
                            'shop_manager_visibility' => 1
                        );

                        $counter++;
                    }
                }

                $fields = array_merge($fields, $tax_fileds);
            }

            //***

            $options = $this->get_options();

            //apply saved options
            if (!empty($options)) {
                if (isset($options['fields']) AND!empty($options['fields']) AND is_array($options['fields'])) {

                    foreach ($options['fields'] as $key => $v) {

                        if (!isset($fields[$key])) {
                            continue; //key was removed or renamed
                        }

                        //***
                        if (!isset($v['show'])) {
                            $v['show'] = 0;
                        }
                        $fields[$key]['show'] = intval($v['show']);

                        if (isset($v['shop_manager_visibility'])) {//because for shop manager its doesn exists
                            if (!in_array($key, array('__checker', 'ID'))) {//this fields must be always visible!!
                                if (in_array($this->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
                                    $fields[$key]['shop_manager_visibility'] = intval($v['shop_manager_visibility']);
                                }
                            }
                        }

                        if (isset($v['title'])) {
                            $title = strip_tags(trim($v['title']));
                            if (!empty($title)) {
                                if (!isset($fields[$key]['title_static'])) {
                                    $fields[$key]['title'] = $title;
                                }
                            }
                        } else {
                            $fields[$key]['title'] = '_';
                        }
                    }


                    //***

                    foreach ($options['fields'] as $key => $v) {

                        if (!isset($fields[$key])) {
                            continue; //key was removed or renamed
                        }

                        //***

                        $res[$key] = $fields[$key];
                    }

                    //if in the future will be added new fields
                    $diff = array_diff(array_keys($fields), array_keys($res));
                    if (!empty($diff)) {
                        foreach ($diff as $fk) {
                            $res[$fk] = $fields[$fk];
                        }
                    }
                }
            } else {
                $res = $fields;
                //lets init options
                $options = array();
                $options['fields'] = array();

                foreach ($fields as $key => $f) {
                    $options['fields'][$key]['show'] = $f['show'];
                    $options['fields'][$key]['title'] = $f['title'];
                }


                $this->update_options($options);
            }

            //***
        }


        //lets check restricions
        if (!in_array($this->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator'])) AND $use_roles) {

            static $ff = array();

            if (empty($ff)) {
                $ff = $res;
                //for correct filtering products manager needs all fields
                $shop_manager_visibility = $this->get_shop_manager_visibility();
                if (is_array($shop_manager_visibility) AND!empty($shop_manager_visibility)) {
                    foreach ($shop_manager_visibility as $key => $is) {
                        if (intval($is) === 0) {
                            unset($ff[$key]);
                        }
                    }
                }
            }

            return $ff;
        }


        //****
        return $res;
    }

    public function get_shop_manager_visibility() {
        static $shop_manager_visibility = array();

        if (empty($shop_manager_visibility)) {
            $shop_manager_visibility = get_option('woobe_shop_manager_visibility', true);
        }

        return $shop_manager_visibility;
    }

    private function init_fields() {

        $fields = $this->get_fields();

        foreach ($fields as $key => $f) {
            if ($f['show']) {
                $this->active_fields[$key] = $f;
            }
        }

        //fix for stock_quantity + manage_stock
        if (isset($this->active_fields['stock_quantity'])) {
            // $this->active_fields['manage_stock'] = $fields['manage_stock'];
            //$this->active_fields['stock_status'] = $fields['stock_status'];
        }
    }

    public function get_fields_keys() {
        return array_keys($this->active_fields);
    }

    //by which column are products sorted after page loading
    public function get_default_sortby_col_num() {

        $col_num = 0;
        if (empty($this->default_sort_by)) {
            $this->default_sort_by = 'ID';
        }
        $keys = $this->get_fields_keys();

        if (!empty($keys)) {
            foreach ($keys as $counter => $key) {
                if ($key == $this->default_sort_by) {
                    $col_num = $counter;
                    break;
                }
            }
        }

        return $col_num;
    }

    public function get_total_settings() {

        $default_sort_by = $this->active_fields;
        if (!empty($default_sort_by)) {
            foreach ($default_sort_by as $key => $f) {
                if (!$f['order']) {
                    unset($default_sort_by[$key]);
                }
            }
        } else {
            $default_sort_by = array();
        }

        //***

        $data = array();
        $data['default_sort_by'] = $default_sort_by;
        $settings = woobe_get_total_settings($data);
        foreach ($settings as $key => $sett) {
            $settings[$key]['value'] = $this->$key;
        }

        return $settings;
    }

}
