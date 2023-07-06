<?php

if (!defined('ABSPATH'))
    die('No direct access allowed');

//16-11-2022
final class WOOF_SD_COLOR {

    private $meta_key = 'woof_sd_color';
    private $meta_key_image = 'woof_sd_color_image';

    public function __construct() {
        add_action('wp_ajax_woof_sd_load_color_terms', function () {
            $term_id = 0;

            if (isset($_REQUEST['term_id'])) {
                $term_id = intval($_REQUEST['term_id']);
            }

            echo json_encode($this->gets(esc_html($_REQUEST['taxonomy']), true, $term_id));
            exit;
        });

        add_action('wp_ajax_woof_sd_change_term_color', function () {
            $this->set(intval($_REQUEST['term_id']), esc_html($_REQUEST['value']));
            die(json_encode([]));
        });

        add_action('wp_ajax_woof_sd_change_term_color_image', function () {
            $this->set_image(intval($_REQUEST['term_id']), esc_html($_REQUEST['value']));
            die(json_encode([]));
        });

        add_filter('get_woof_sd_term_color', function ($term_id) {
            $color = get_term_meta($term_id, $this->meta_key, true);
            return $color ? $color : 'inherit';
        });

        add_filter('get_woof_sd_term_color_image', function ($term_id) {
            $img = get_term_meta($term_id, $this->meta_key_image, true);
            return $img ? $img : '';
        });
    }

    private function gets($tax_slug, $use_subterms = true, $parent_term_id = 0) {
        $terms = $this->assemble_terms(WOOF_HELPER::get_terms($tax_slug, false, $use_subterms, 0, $parent_term_id), $use_subterms);

        if (!empty($terms)) {
            foreach ($terms as $term_id => $term) {

                $title = ['value' => $term['title'], 'css_classes' => ['woof-sd-flex']];
                if ($term['has_childs']) {
                    $title = ['value' => ['element' => 'link', 'value' => $term['title'], 'data-id' => $term_id, 'action' => 'woof_sd_childs_term_color'], 'css_classes' => ['woof-sd-flex']];
                }

                $rows[(string) $term_id] = [
                    'title' => $title,
                    'color' => ['value' => ['element' => 'color', 'value' => $term['color'], 'action' => 'woof_sd_change_term_color'], 'css_classes' => ['woof-sd-flex']],
                    'image' => ['value' => ['element' => 'image', 'value' => $term['image'], 'action' => 'woof_sd_change_term_color_image']],
                ];
            }
        }

        //list of elements
        $data = [
            'header' => [
                ['value' => esc_html__('Title', 'woocommerce-products-filter'), 'width' => '30%', 'key' => 'title'],
                ['value' => esc_html__('Color', 'woocommerce-products-filter'), 'width' => '35%', 'key' => 'color'],
                ['value' => esc_html__('Image', 'woocommerce-products-filter'), 'width' => '35%', 'key' => 'image']
            ],
            'rows' => $rows
        ];

        return $data;
    }

    private function assemble_terms($data, $use_subterms) {
        $terms = [];

        if (!empty($data)) {
            foreach ($data as $term_id => $term) {
                $has_childs = intval(isset($term['childs']) AND count($term['childs']));

                $terms[$term_id] = [
                    'title' => $term['name'],
                    'color' => $this->get($term_id),
                    'image' => $this->get_image($term_id),
                    'has_childs' => $has_childs
                ];

                if ($has_childs AND $use_subterms) {
                    $terms[$term_id]['childs'] = $this->assemble_terms($term['childs'], $use_subterms);
                }
            }
        }

        return $terms;
    }

    private function get($term_id) {
        //return get_option("_woof_term_color_{$taxonomy}_{$term_id}", '#000000');
        $meta_key = apply_filters('get_woof_sd_term_color_key', $this->meta_key);
        $color = apply_filters('get_woof_sd_term_color', $term_id);

        if (!$color) {
            $color = '#000000';
        }

        return $color;
    }

    private function set($term_id, $value) {
        $meta_key = apply_filters('get_woof_sd_term_color_image_key', $this->meta_key);
        update_term_meta($term_id, $meta_key, $value);
        apply_filters('set_woof_sd_term_color', $value, $term_id); //for anoter plugins or serialize data somewhere
    }

    //+++

    private function get_image($term_id) {
        $meta_key = apply_filters('get_woof_sd_term_color_image_key', $this->meta_key_image);
        $img = apply_filters('get_woof_sd_term_color_image', $term_id);

        if (!$img) {
            $img = '';
        }

        return $img;
    }

    private function set_image($term_id, $value) {
        $meta_key = apply_filters('get_woof_sd_term_color_image_key', $this->meta_key_image);
        update_term_meta($term_id, $this->meta_key_image, $value);
        apply_filters('set_woof_sd_term_color_image', $value, $term_id); //for anoter plugins or serialize data somewhere
    }

}

new WOOF_SD_COLOR();

