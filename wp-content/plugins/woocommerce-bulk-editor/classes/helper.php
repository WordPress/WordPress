<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOBE_HELPER {

    static $users = array();

    public static function draw_link($data) {
        $link = "<a href='{$data['href']}'";

        if (isset($data['class'])) {
            $link .= " class='{$data['class']}'";
        }

        if (isset($data['style'])) {
            $link .= " style='{$data['style']}'";
        }

        if (isset($data['id'])) {
            $link .= " id='{$data['id']}'";
        }

        if (isset($data['target'])) {
            $link .= " target='{$data['target']}'";
        }

        if (isset($data['title_attr'])) {
            $link .= " title='{$data['title_attr']}'";
        }

        if (isset($data['more'])) {
            $link .= " {$data['more']} ";
        }

        $link .= '>' . $data['title'] . '</a>';
        return $link;
    }

    public static function get_users() {

        if (empty(self::$users)) {
			
			$roles__in = [];
			foreach( wp_roles()->roles as $role_slug => $role )
			{
				if( ! empty( $role['capabilities']['publish_posts'] ) )
					$roles__in[] = $role_slug;
			}


            $users_arg = apply_filters('woobe_users_args', array(
                'fields' => array('ID', 'display_name'),
               // 'who' => 'authors',
				'role__in' => $roles__in 
				)
            );

            $users = get_users($users_arg);

            foreach ($users as $user) {
                self::$users[$user->ID] = $user->display_name;
            }
        }

        return self::$users;
    }

    public static function draw_select($data, $is_multi = false) {
        $multiple = '';
        if ($is_multi) {
            $multiple = 'multiple size=2';
        }

        //for filters
        $name = '';
        if (isset($data['name'])) {
            $name = "name='{$data['name']}'";
        }


        $disabled = '';
        if (isset($data['disabled']) AND $data['disabled']) {
            $disabled = "disabled=''";
        }

        //***

        $onchange = '';
        if (isset($data['onchange'])) {
            $onchange = "onchange='{$data['onchange']};'";
        }

        $onmouseover = '';
        if (isset($data['onmouseover'])) {
            $onmouseover = "onmouseover='{$data['onmouseover']};'";
        }

        //***
        $selected = '';
        if (isset($data['selected'])) {
            if (is_array($data['selected'])) {
                $selected = implode(',', $data['selected']);
            } else {
                $selected = $data['selected'];
            }
        }

        $select = "<div class='select-wrap'><select {$multiple} {$name} {$disabled} {$onchange} {$onmouseover} id='mselect_{$data['field']}_{$data['product_id']}' data-field='{$data['field']}' data-product-id='{$data['product_id']}' data-placeholder=' ' data-selected='{$selected}' class='{$data['class']}'>";

        //***        

        if (isset($data['options'])) {
            $in_selected = array();

            //***

            if (isset($data['selected'])) {
                if (is_array($data['selected'])) {
                    $in_selected = $data['selected'];
                } else {
                    $in_selected[] = $data['selected'];
                }
            }

            //***

            foreach ($data['options'] as $key => $title) {

                $selected = false;
                if (in_array($key, $in_selected)) {
                    $selected = TRUE;
                }
                $select .= '<option ' . selected($selected, TRUE, false) . " value='{$key}'>" . $title . '</option>';
            }
        }

        $select .= '</select></div>';
        return $select;
    }

    public static function draw_advanced_switcher($is, $numcheck, $name, $labels, $vals, $trigger_target = '', $css_classes = '') {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_advanced_switcher.php', array(
                    'is' => $is,
                    'numcheck' => $numcheck,
                    'name' => $name,
                    'labels' => $labels,
                    'vals' => $vals,
                    'trigger_target' => $trigger_target,
                    'css_classes' => $css_classes
        ));
    }

    public static function draw_calendar($product_id, $product_title, $field_key, $val, $name = '', $print_placeholder = false, $time = false) {

        return self::render_html(WOOBE_PATH . 'views/elements/draw_calendar.php', array(
                    'product_id' => $product_id,
                    'product_title' => $product_title,
                    'field_key' => $field_key,
                    'val' => $val,
                    'name' => $name,
                    'print_placeholder' => $print_placeholder,
                    'time' => $time
        ));
    }

    public static function draw_taxonomy_popup_btn($data, $tax_key, $post) {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_taxonomy_popup_btn.php', array(
                    'data' => $data,
                    'tax_key' => $tax_key,
                    'post' => $post
        ));
    }

    public static function draw_attribute_list_btn($terms, $selected_terms_ids, $tax_key, $post) {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_attribute_list_btn.php', array(
                    'terms' => $terms,
                    'selected_terms_ids' => $selected_terms_ids,
                    'tax_key' => $tax_key,
                    'post' => $post
        ));
    }

    public static function draw_popup_editor_btn($val, $field_key, $post) {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_popup_editor_btn.php', array(
                    'val' => $val,
                    'field_key' => $field_key,
                    'post' => $post
        ));
    }

    public static function draw_downloads_popup_editor_btn($field_key, $product_id, $files_count = 0) {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_downloads_popup_editor_btn.php', array(
                    'field_key' => $field_key,
                    'product_id' => $product_id,
                    'files_count' => $files_count
        ));
    }

    public static function draw_gallery_popup_editor_btn($field_key, $product_id, $images = array()) {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_gallery_popup_editor_btn.php', array(
                    'field_key' => $field_key,
                    'product_id' => $product_id,
                    'images' => $images
        ));
    }

    public static function draw_upsells_popup_editor_btn($field_key, $product_id, $ids = array()) {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_upsells_popup_editor_btn.php', array(
                    'field_key' => $field_key,
                    'product_id' => $product_id,
                    'ids' => $ids
        ));
    }

    public static function draw_cross_sells_popup_editor_btn($field_key, $product_id, $ids = array()) {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_cross_sells_popup_editor_btn.php', array(
                    'field_key' => $field_key,
                    'product_id' => $product_id,
                    'ids' => $ids
        ));
    }

    public static function draw_meta_popup_editor_btn($field_key, $product_id, $btn_title = '') {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_meta_popup_editor_btn.php', array(
                    'field_key' => $field_key,
                    'product_id' => $product_id,
                    'btn_title' => $btn_title
        ));
    }

    public static function draw_grouped_popup_editor_btn($field_key, $product_id, $ids = array()) {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_grouped_popup_editor_btn.php', array(
                    'field_key' => $field_key,
                    'product_id' => $product_id,
                    'ids' => $ids
        ));
    }

    public static function draw_tooltip($text, $direction = 'down') {
        ?>
        <a class="info_helper zebra_tips1" title="<?= $text ?>"><span class="icon-info"></span></a>
        <?php
    }

    public static function draw_restricked($text = '', $direction = 'right') {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_restricked.php', array(
                    'text' => $text,
                    'direction' => $direction
        ));
    }

    public static function draw_image($src, $alt = '', $class = '', $width = '') {
        return self::render_html(WOOBE_PATH . 'views/elements/draw_image.php', array(
                    'src' => $src,
                    'alt' => $alt,
                    'class' => $class,
                    'width' => $width
        ));
    }

    public static function draw_checkbox($attributes = array(), $is_checked = false) {
        $ch = '<input type="checkbox" ';
        if (!empty($attributes)) {
            foreach ($attributes as $key => $value) {
                $ch .= $key . '=' . '"' . $value . '" ';
            }
        }

        if ($is_checked) {
            $ch .= 'checked ';
        }

        $ch .= '/>';
        return $ch;
    }

    public static function strtolower($string) {
        if (function_exists('mb_strtolower')) {
            $string = mb_strtolower($string, 'UTF-8');
        } else {
            $string = strtolower($string);
        }

        return $string;
    }

    public static function array_to_string($array) {
        $string = '';
        foreach ($array as $key => $value) {
            $string .= $key . ':' . $value . ',';
        }
        return trim($string, ',');
    }

    public static function string_to_array($string) {
        $res = array();
        $tmp = explode(',', $string);
        if (substr_count($string, ':') > 0) {
            //if indexes of array matter: 3:1,4:2,5:1 - index:value
            if (!empty($tmp)) {
                $vv = array();
                foreach ($tmp as $v) {
                    $v = explode(':', $v);
                    $vv[$v[0]] = $v[1];
                }
                $res = $vv;
            }
        } else {
            //1,2,5,7,12
            $res = $tmp;
        }

        return $res;
    }

    public static function get_taxonomies_terms_hierarchy($taxonomy) {

        $res = array();

        $object_terms = get_terms(array(
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ));

        $data = array();

        if (!empty($object_terms)) {
            foreach ($object_terms as $term) {
                if (is_object($term)) {
                    $data[$term->parent][] = array(
                        'term_id' => $term->term_id,
                        'name' => $term->name,
                        'slug' => $term->slug,
                        'parent' => $term->parent,
						'description' => $term->description,
                        'childs' => array()
                    );
                }
            }

            //***

            $res = self::sort_taxonomies_by_parents($data);
        }

        return $res;
    }

    private static function sort_taxonomies_by_parents($data, $parent_id = 0) {
        if (isset($data[$parent_id])) {
            if (!empty($data[$parent_id])) {
                foreach ($data[$parent_id] as $key => $o) {
                    if (isset($data[$o['term_id']])) {
                        $data[$parent_id][$key]['childs'] = self::sort_taxonomies_by_parents($data, $o['term_id']);
                    }
                }

                return $data[$parent_id];
            }
        }

        return array();
    }

    public static function prepare_meta_keys($key) {
        //return sanitize_title(trim($key));
        return trim($key);
    }

    public static function draw_rounding_drop_down() {
        ?>
        <select class="woobe_num_rounding">
            <option value="0"><?php esc_html_e('no rounding', 'woocommerce-bulk-editor') ?></option>
			<option value="100">00</option>
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="9">9</option>
            <option value="19">19</option>
            <option value="29">29</option>
            <option value="39">39</option>
            <option value="49">49</option>
            <option value="59">59</option>
            <option value="69">69</option>
            <option value="79">79</option>
            <option value="89">89</option>
            <option value="99">99</option>
        </select>  
        <?php
    }

    public static function render_html($pagepath, $data = array()) {

        if (is_array($data) AND!empty($data)) {
            if (isset($data['pagepath'])) {
                unset($data['pagepath']);
            }
            extract($data);
        }

        //***

        ob_start();
        include(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pagepath));
        return ob_get_clean();
    }

    public static function sanitize_bulk_key($bulk_key) {
        return strtolower(sanitize_text_field($bulk_key));
    }

    public static function sanitize_array($array) {
        return $array;
        if (!empty($array) AND is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = self::sanitize_array($value);
                } else {
                    $array[$key] = wp_kses($value, wp_kses_allowed_html('post'));
                }
            }
        }

        return $array;
    }
	public static function over_switcher_swicher_to_val($val, $key) {
		global $WOOBE;
		$switcher_values = $WOOBE->settings->override_switcher_fieds;
		//$switcher_values = 'dist:yes,stest:true';
		$sw_array = explode(',', $switcher_values);
		foreach ($sw_array as $rule) {
			$rule_array = explode(':', $rule);
			if (count($rule_array) > 1 && $rule_array[0] == $key) {
				$values_array = explode('^', $rule_array[1]);
				if ($val == 1) {
					return $values_array[0];
				} elseif (count($values_array) > 1 && !$val) {
					return $values_array[1];
				}
			}
		}
		return $val;
	}	
	public static function over_switcher_val_to_swicher($val, $key) {
		global $WOOBE;
		$switcher_values = $WOOBE->settings->override_switcher_fieds;
		//$switcher_values = 'dist:yes,stest:true';
		$sw_array = explode(',', $switcher_values);
		foreach ($sw_array as $rule) {
			$rule_array = explode(':', $rule);
			if (count($rule_array) > 1 && $rule_array[0] == $key) {
				$values_array = explode('^', $rule_array[1]);
				if ($values_array[0] == $val ) {
					return 1;
				} else {
					return '';
				}
			}
		}
		return $val;
	}
	public static function write_log($message){
		$path = WOOBE_PATH . 'woobe.log';
		$data_log = date("Y-m-d H:i:s") . " - " . $message . PHP_EOL;
		file_put_contents($path, $data_log, FILE_APPEND);
	}	

}
