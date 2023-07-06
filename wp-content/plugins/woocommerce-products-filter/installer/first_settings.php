<?php

class WoofFirstSettings {

    public $woof = null;
    public $activate_ext = array();

    public function __construct($woof) {
        $this->woof = $woof;
        $this->activate_ext = $this->get_ext_list_to_activate();
    }

    public function init_first_settings() {
        //activate  ext
        foreach ($this->activate_ext as $key) {
            $this->control_extension_by_key($key, true);
        }
        $first_options = $this->get_settings_list();
        $first_options = array_merge($first_options, $this->woof->settings);

        update_option('woof_settings', $first_options);
        $this->woof->settings = $first_options;
    }

    public function control_extension_by_key($key, $activate) {
        $directories = $this->woof->get_ext_directories();
        if (isset($this->woof->settings['activated_extensions'])) {
            $activated = $this->woof->settings['activated_extensions'];
        } else {
            $activated = array();
        }
        //*** default exts

        if (!empty($directories['default']) AND is_array($directories['default'])) {

            foreach ($directories['default'] as $path) {

                if (strrpos($path . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR) !== false) {
                    $idx = WOOF_EXT::get_ext_idx_new($path);
                    $key = array_search($idx, $activated);

                    if ($activate && $key === false) {
                        //add
                        $activated[] = $idx;
                        $this->woof->settings['activated_extensions'] = $activated;
                        update_option('woof_settings', $this->woof->settings);
                    }
                    if (!$activate && $key !== false) {
                        //delete
                        unset($activated[$key]);
                        $this->woof->settings['activated_extensions'] = $activated;
                        update_option('woof_settings', $this->woof->settings);
                    }
                }
            }
        }
    }

    public function get_ext_list_to_activate() {

        $activate = array('label', 'in_stock', 'by_onsales', 'meta_filter', 'url_request', 'by_text', 'smart_designer', 'acf_filter');
        if (intval(WOOF_VERSION) === 3) {
            $activate = array_merge($activate, array('color', 'image', 'select_hierarchy', 'slider', 'by_featured'));
        }

        return $activate;
    }

    public function get_settings_list() {
        $settings = array(
            'by_price' => array(
                'show' => 3
            ),
            'by_instock' => array(
                'show' => 1,
                'use_for' => 'both'
            ),
            'by_onsales' => array(
                'show' => 1
            ),
            'by_text' => array(
                'show' => 1,
                'behavior' => 'title_or_content_or_excerpt',
                'autocomplete' => 1,
                'sku_compatibility' => 1
            ),
            'listen_catalog_visibility' => 1,
            //taxonomies
            'tax_type' => array(
                'product_cat' => 'select',
                'product_tag' => 'mselect',
                'pa_color' => 'checkbox',
                'pa_size' => 'label'
            ),
            'show_title' => array(
                'product_cat' => 1,
                'product_tag' => 1,
                'pa_color' => 1,
                'pa_size' => 1
            ),
            'show_title_label' => array(
                'product_cat' => 1,
                'product_tag' => 1,
                'pa_color' => 1,
                'pa_size' => 1
            ),
            "dispay_in_row" => array(
                'product_cat' => 0,
                'product_tag' => 0,
                'pa_color' => 0,
                'pa_size' => 0
            ),
            "show_tooltip" => array(
                'product_cat' => 0,
                'product_tag' => 0,
                'pa_color' => 0,
                'pa_size' => 0
            ),
            'tax' => array(
                'product_cat' => 1,
                'product_tag' => 1,
                'pa_color' => 1,
                'pa_size' => 1
            )
        );

        if (intval(WOOF_VERSION) === 3) {
            $settings['by_featured'] = array(
                'show' => 1
            );
            $settings['tax_type']['pa_color'] = 'color';

            $settings['color'] = array(
                'pa_color' => array(
                    "yellow" => "#eeee22",
                    "pink" => "#ea31a6",
                    "purple" => "#cc2828",
                    "gray" => "#d8d8d8",
                    "blue" => "#1e73be",
                    "orange" => "#e87c35",
                    "green" => "#81d742",
                    "black" => "#000000",
                    "red" => "#dd3333",
                    "white" => "#ffffff"
                )
            );
        }

        return $settings;
    }

}
