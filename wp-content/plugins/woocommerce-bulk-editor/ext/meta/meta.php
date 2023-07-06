<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WOOBE_META extends WOOBE_EXT {

    protected $slug = 'meta'; //unique
    private $storage_key = 'woobe_meta_fields';

    public function __construct() {

        load_plugin_textdomain('woocommerce-bulk-editor', false, 'woocommerce-bulk-editor/languages');

        add_action('woobe_ext_scripts', array($this, 'woobe_ext_scripts'), 1);

        //ajax
        add_action('wp_ajax_woobe_save_meta', array($this, 'woobe_save_meta'), 1);
        add_action('wp_ajax_woobe_meta_get_keys', array($this, 'woobe_meta_get_keys'), 1);

        //tabs

        $user = wp_get_current_user();
        $role = (array) $user->roles;

        if (current_user_can('administrator') || array_intersect(apply_filters('woobe_permit_special_roles', ['administrator']), $role)) {
            $this->add_tab($this->slug, 'panel', esc_html__('Meta Fields', 'woocommerce-bulk-editor'), 'attach');
            add_action('woobe_ext_panel_' . $this->slug, array($this, 'woobe_ext_panel'), 1);
        }


        //hooks
        add_filter('woobe_extend_fields', array($this, 'woobe_extend_fields'), 1);
        add_filter('woobe_filter_text', array($this, 'woobe_filter_text'), 1);
        add_filter('woobe_filter_numbers', array($this, 'woobe_filter_numbers'), 1);
        add_filter('woobe_filter_other', array($this, 'woobe_filter_other'), 1);

        add_filter('woobe_bulk_text', array($this, 'woobe_bulk_text'), 1);
        add_filter('woobe_bulk_number', array($this, 'woobe_bulk_number'), 1);
        add_filter('woobe_bulk_other', array($this, 'woobe_bulk_other'), 1);
    }

    public function woobe_ext_scripts() {
        wp_enqueue_script('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/js/' . $this->slug . '.js', array(), WOOBE_VERSION);
        wp_enqueue_style('woobe_ext_' . $this->slug, $this->get_ext_link() . 'assets/css/' . $this->slug . '.css', array(), WOOBE_VERSION);
        ?>
        <script>
            lang.<?php echo $this->slug ?> = {};
            lang.<?php echo $this->slug ?>.enter_key = '<?php esc_html_e('Meta key cannot be empty!', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.enter_prod_id = '<?php esc_html_e('Enter a product ID!', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.no_keys_found = '<?php esc_html_e('No meta keys found!', 'woocommerce-bulk-editor') ?>';
            lang.<?php echo $this->slug ?>.new_key = '<?php esc_html_e('New meta key', 'woocommerce-bulk-editor') ?>';
        </script>
        <?php
    }

    public function woobe_ext_panel() {
        $data = array();
        $data['metas'] = $this->get_fields();
        echo WOOBE_HELPER::render_html($this->get_ext_path() . 'views/panel.php', $data);
    }

    //***
    //ajax
    public function woobe_save_meta() {
        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }

        if (!in_array($this->settings->current_user_role, apply_filters('woobe_permit_special_roles', ['administrator']))) {
            return;
        }

        //***

        $data = array();
        parse_str($_REQUEST['formdata'], $data);
        $data = WOOBE_HELPER::sanitize_array($data);

        if (isset($data['woobe_meta_fields'])) {
            if (is_array($data['woobe_meta_fields'])) {
                $data['woobe_meta_fields'] = array_map(function ($item) {
                    if (isset($item['meta_key'])) {
                        // $item['meta_key'] = sanitize_text_field($item['meta_key']);
                        // $item['title'] = sanitize_text_field($item['title']);
                    }

                    return $item;
                }, $data['woobe_meta_fields']);
                $this->update_fields($data['woobe_meta_fields']);
            }
        }

        exit;
    }

    private function update_fields($data) {
        if (!empty($data)) {
            foreach ($data as $k => $m) {
                if (!isset($data[$k]['meta_key'])) {
                    continue;
                }
                $data[$k]['meta_key'] = /* sanitize_key */trim($m['meta_key']);
                //do not sanitize as exists such meta keys as for example _woocs_sale_price_USD and if to make lowerstring key will be invalid!
                if ($m['edit_view'] == 'textarea') {
                    $data[$k]['type'] = 'string'; //important
                }
            }
        }

        update_option($this->storage_key, $data);
    }

    private function get_fields() {
        $metas = get_option($this->storage_key);

        if (!empty($metas) AND is_array($metas)) {
            foreach ($metas as $k => $m) {
                if (empty($m['meta_key'])) {
                    unset($metas[$k]);
                }
            }
        } else {
            $metas = array();
        }

        global $WOOBE;
        if ($WOOBE->show_notes) {
            if (count($metas) > 2) {
                $metas = array_slice($metas, 0, 2);
            }
        }

        return $metas;
    }

    //hook woobe_extend_fields - add columns into editor
    public function woobe_extend_fields($fields) {
        $metas = $this->get_fields();

        if (!empty($metas)) {
            foreach ($metas as $m) {
                $f = array(
                    'show' => 0,
                    'title' => $m['title'],
                    'title_static' => true, //will not be possible to change title in columns settings
                    'field_type' => 'meta',
                    'meta_key' => $m['meta_key'],
                    'type' => $m['type'],
                    'editable' => TRUE,
                    'direct' => TRUE,
                    'edit_view' => $m['edit_view'],
                    'order' => FALSE,
                    //'prohibit_product_types' => array('variation'),
                    'shop_manager_visibility' => 1
                );

                if ($m['type'] == 'number') {
                    $f['sanitize'] = 'floatval';
                    $f['order'] = TRUE;
                } elseif ($m['type'] == 'string') {
                    $f['order'] = TRUE;
                }

                if ($m['edit_view'] == 'switcher') {
                    $f['select_options'] = array(
                        '1' => esc_html__('Yes', 'woocommerce-bulk-editor'), //true                        
                        '0' => esc_html__('No', 'woocommerce-bulk-editor'), //false
                    );
                    $f['type'] = 'string'; //matter
                }

                //$f['css_classes'] = 'not-for-variations';
                $f['css_classes'] = '';

                $fields[$m['meta_key']] = $f;
            }
        }

        return $fields;
    }

    //hook woobe_filter_text
    public function woobe_filter_text($data) {
        $metas = $this->get_fields();
        if (!empty($metas)) {
            foreach ($metas as $m) {
				if ($m['edit_view'] === 'gallery_popup_editor') {
                    continue;
                }
                if (in_array($m['meta_key'], $this->settings->get_fields_keys())) {
                    if ($m['type'] == 'string') {
                        $data[$m['meta_key']] = array(
                            'placeholder' => $m['title'],
                            'direct' => TRUE,
                            'behavior_options' => array(
                                'LIKE' => esc_html__('LIKE', 'woocommerce-bulk-editor'),
                                '=' => esc_html__('EXACT (=)', 'woocommerce-bulk-editor'),
                                '!=' => esc_html__('NOT EXACT (!=)', 'woocommerce-bulk-editor'),
                                'NOT LIKE' => esc_html__('NOT LIKE', 'woocommerce-bulk-editor'),
                                'empty' => esc_html__('Empty', 'woocommerce-bulk-editor'),
                                'not_empty' => esc_html__('NOT Empty', 'woocommerce-bulk-editor'),
                            ),
                            'css_classes' => 'not-for-variations'
                        );
                    }
                }
            }
        }

        return $data;
    }

    //hook woobe_filter_numbers
    public function woobe_filter_numbers($data) {
        $metas = $this->get_fields();
        if (!empty($metas)) {
            foreach ($metas as $m) {
                if (in_array($m['meta_key'], $this->settings->get_fields_keys())) {
                    if ($m['type'] == 'number' AND ($m['edit_view'] != 'switcher' AND $m['edit_view'] != 'calendar')) {
                        $data[$m['meta_key']] = array(
                            'placeholder_from' => sprintf(esc_html__('%s from', 'woocommerce-bulk-editor'), $m['title']),
                            'placeholder_to' => sprintf(esc_html__('%s to', 'woocommerce-bulk-editor'), $m['title']),
                            //'css_classes' => 'not-for-variations'
                            'direct' => TRUE,
                            'css_classes' => ''
                        );
                    }
                }
            }
        }

        return $data;
    }

    //hook woobe_filter_other
    public function woobe_filter_other($data) {
        $metas = $this->get_fields();
        if (!empty($metas)) {
            foreach ($metas as $m) {
                if (in_array($m['meta_key'], $this->settings->get_fields_keys())) {
                    if ($m['edit_view'] == 'switcher') {
                        $data[$m['meta_key']] = array(
                            'title' => $m['title'],
                            'direct' => TRUE,
                            'css_classes' => 'not-for-variations'
                        );
                    }
                }
            }
        }

        return $data;
    }

    //ajax
    public function woobe_meta_get_keys() {
        $res = '';

        $product_id = intval($_REQUEST['product_id']);
        if ($product_id > 0) {
            $a1 = array_keys(get_post_meta($product_id, '', false));
            if (!$a1) {
                $a1 = array_keys(get_post_meta($product_id, '', false));
            }
            $a2 = (new WOOBE_PDS_CPT())->get_internal_meta_keys();
            $res = array_diff($a1, $a2);
        }

        die(json_encode(array_values($res)));
    }

    //hook woobe_bulk_text
    public function woobe_bulk_text($data) {
        $metas = $this->get_fields();
        if (!empty($metas)) {
            foreach ($metas as $m) {
                if (in_array($m['meta_key'], $this->settings->get_fields_keys())) {
                    if ($m['type'] == 'string') {
                        $data[$m['meta_key']] = array(
                            'title' => $m['title'],
                            'direct' => TRUE,
                            //'css_classes' => 'not-for-variations'
                            'css_classes' => ''
                        );
                    }
                }
            }
        }

        return $data;
    }

    //hook woobe_bulk_number
    public function woobe_bulk_number($data) {
        $metas = $this->get_fields();
        if (!empty($metas)) {
            foreach ($metas as $m) {
                if ($m['type'] == 'number' AND $m['edit_view'] != 'switcher') {
                    if (in_array($m['meta_key'], $this->settings->get_fields_keys())) {
                        $data[$m['meta_key']] = array(
                            'title' => $m['title'],
                            'direct' => TRUE,
                            'options' => array(
                                'new' => esc_html__('set new', 'woocommerce-bulk-editor'),
                                'invalue' => esc_html__('increase by value', 'woocommerce-bulk-editor'),
                                'devalue' => esc_html__('decrease by value', 'woocommerce-bulk-editor'),
                                'inpercent' => esc_html__('increase by %', 'woocommerce-bulk-editor'),
                                'depercent' => esc_html__('decrease by %', 'woocommerce-bulk-editor')
                            ),
                            //'css_classes' => 'not-for-variations'
                            'css_classes' => ''
                        );
                    }
                }
            }
        }

        return $data;
    }

    //hook woobe_bulk_other
    public function woobe_bulk_other($data) {

        $metas = $this->get_fields();
        if (!empty($metas)) {
            foreach ($metas as $m) {
                if (in_array($m['meta_key'], $this->settings->get_fields_keys())) {
                    if ($m['edit_view'] == 'switcher') {
                        $data[$m['meta_key']] = array(
                            'title' => $m['title'],
                            'direct' => TRUE,
                            'options' => array(
                                '1' => esc_html__('Yes', 'woocommerce-bulk-editor'), //true                        
                                '0' => esc_html__('No', 'woocommerce-bulk-editor'), //false
                            ),
                            'css_classes' => 'not-for-variations'
                        );
                    }
                }
            }
        }

        return $data;
    }

}

//for woobe_meta_get_keys method
//another way is stupid copy/paste!
class WOOBE_PDS_CPT extends WC_Product_Data_Store_CPT {

    public function get_internal_meta_keys() {
        $this->internal_meta_keys[] = '_button_text';
        $this->internal_meta_keys[] = '_product_url';
        return $this->internal_meta_keys;
    }

}
