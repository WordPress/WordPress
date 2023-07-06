<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//CRUD - filters sets profiles
class WOOBE_FILTER_PROFILES extends WOOBE_PROFILES {

    protected $option_key = 'woobe_filter_profiles_';
    protected $non_deletable_profiles = [];
    protected $create_profile_ajax_action = 'woobe_create_filter_profile';
    protected $load_profile_ajax_action = 'woobe_load_filter_profile';
    protected $delete_profile_ajax_action = 'woobe_delete_filter_profile';

    public function __construct($settings) {
        parent::__construct(new WOOBE_SETTINGS());
        add_action('wp_ajax_woobe_get_filter_profile_data', array($this, 'get_filter_profile_data'), 1);
        add_action('wp_ajax_woobe_fprofile_saved_cencel', array($this, 'fprofile_saved_cencel'), 1);
    }

    protected function init_constructor_data() {
        //$this->update(array());//for tests
        if (!$this->get()) {
            //lets create default profile after first plugin init after its intstallation
            $this->create(array(
                'product_type' => 'variable'
                    ), esc_html__('Variable products', 'woocommerce-bulk-editor'), 'default');
        }
    }

    //++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    //ajax
    public function load_profile() {

        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }
        //die('1');//for tests
        $profile_key=sanitize_text_field($_REQUEST['profile_key']);
        $profile = $this->get($profile_key);
        if (isset($_REQUEST['saved_fprofile']) AND boolval($_REQUEST['saved_fprofile'])) {
            update_user_meta(get_current_user_id(), "woobe_fprofile_saved", $profile_key);
        } else {
            update_user_meta(get_current_user_id(), "woobe_fprofile_saved", 0);
        }
        if (!empty($profile)) {
            if (isset($profile['data']) AND ! empty($profile['data'])) {
                $this->storage->set_val('woobe_filter_' . $profile_key, $profile['data']);
            } else {
                die('-1');
            }
        } else {
            die('-1');
        }

        die('1');
    }

    //ajax
    public function create_profile() {

        if (!current_user_can('manage_woocommerce')) {
            die('0');
        }

        $profile_title = sanitize_text_field(trim(htmlentities($_REQUEST['profile_title'], ENT_NOQUOTES)));

        if (!empty($profile_title)) {

            $filter_current_key = sanitize_text_field($_REQUEST['filter_current_key']);

            if (!empty($profile_title) AND ! empty($filter_current_key)) {
                echo $this->create($this->storage->get_val('woobe_filter_' . $filter_current_key), $profile_title, $filter_current_key);
            }
        }

        exit;
    }

    //ajax
    public function fprofile_saved_cencel() {
        update_user_meta(get_current_user_id(), "woobe_fprofile_saved", 0);
    }

    public function get_filter_profile_data() {
        $res = array();
        $res['taxonomies'] = array();
        $res['taxonomies_operators'] = array();
        $res['taxonomies_terms_titles'] = array();
        $profile = $this->get(sanitize_text_field($_REQUEST['profile_key']));

        if (!empty($profile['data'])) {
            foreach ($profile['data'] as $key => $value) {

                if (in_array($key, array('taxonomies_operators', 'tax_query', 'meta_query'))) {
                    continue;
                }

                //***

                if ($key == 'taxonomies') {

                    if (!empty($value)) {
                        foreach ($value as $tax_key => $terms) {
                            $res['taxonomies'][$tax_key] = $terms;
                            if (!empty($terms)) {
                                foreach ($terms as $term_id) {
                                    $term = get_term_by('id', $term_id, $tax_key);
                                    $res['taxonomies_terms_titles'][$term_id] = $term->name;
                                }
                            }
                            $res['taxonomies_operators'][$tax_key] = $profile['data']['taxonomies_operators'][$tax_key];
                        }
                    }

                    continue;
                }

                //***

                if (is_array($value)) {
                    if (isset($value['value']) AND ! empty($value['value'])) {
                        $res[$key]['value'] = $value['value'];
                        $res[$key]['behavior'] = $value['behavior'];
                    }

                    if (isset($value['from']) AND ! empty($value['from'])) {
                        $res[$key]['from'] = $value['from'];
                    }

                    if (isset($value['to']) AND ! empty($value['to'])) {
                        $res[$key]['to'] = $value['to'];
                    }
                } else {
                    if (!empty($value) AND intval($value) !== -1) {
                        /*
                          if ($this->settings->get_fields(false)[$key]['edit_view'] == 'calendar') {
                          $products = new WOOBE_PRODUCTS($this->settings, $this->storage);
                          $value = $products->normalize_calendar_date($value, $key);
                          }
                         */

                        $res[$key] = $value;
                    }
                }
            }
        }

        //***

        $html = '';

        if (!empty($res)) {
            foreach ($res as $key => $value) {

                if (in_array($key, array('taxonomies_operators', 'taxonomies_terms_titles'))) {
                    continue;
                }

                //***

                if ($key == 'taxonomies') {
                    if (!empty($value)) {
                        foreach ($value as $tax_key => $terms) {
                            if (!empty($terms)) {
                                foreach ($terms as $term_id) {
                                    $html .= '<li>' . $res['taxonomies_terms_titles'][$term_id] . ' (<i>' . $res['taxonomies_operators'][$tax_key] . '</i>)' . '</li>';
                                }
                            }
                        }
                    }

                    continue;
                }


                //***

                if (is_array($value)) {
                    if (isset($value['value'])) {
                        $html .= '<li><b>' . $key . '</b>: <i>' . $value['value'] . '</i> (' . $value['behavior'] . ')</li>';
                    } else {
                        $tmp = array(
                            'from' => '-',
                            'to' => '-'
                        );
                        if (isset($value['from'])) {
                            $tmp['from'] = $value['from'];
                        }

                        if (isset($value['to'])) {
                            $tmp['to'] = $value['to'];
                        }

                        $html .= '<li><b>' . $key . '</b>: <i>' . $tmp['from'] . ' - ' . $tmp['to'] . '</i></li>';
                    }
                } else {
                    $html .= '<li><b>' . $key . '</b>: <i>' . $value . '</i></li>';
                }
            }
        }

        //***

        $answer = array(
            'res' => $res,
            'html' => $html
        );

        die(json_encode($answer));
    }

}
