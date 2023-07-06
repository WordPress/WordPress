<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
WOOF_REQUEST::set('additional_taxes', $additional_taxes);

if (!$sd_template) {
    echo esc_html__('Selected SD element is not correct!', 'woocommerce-products-filter');
    return;
}

//+++

if (!function_exists('woof_sd_assemble_terms')) {

    function woof_sd_assemble_terms($tax_slug, $terms, $hidden_terms, $hide_childs, $sd_data,
            $sd_template, $sd_template_num, $current_request, $show_count, $show_count_dynamic,
            $hide_dynamic_empty_pos, $not_toggled_terms_count, $sd_type, $level = 0) {

        $result = "";
        $hide_next_term_li = false;
        $terms_count_printed = 0;

        foreach ($terms as $term) {
            $inique_id = uniqid('sd-');
            $count_string = "";
            $count = 0;

            if (!in_array($term['slug'], $current_request)) {
                if ($show_count) {

                    if ($show_count_dynamic) {
                        $count = woof()->dynamic_count($term, 'multi', WOOF_REQUEST::get('additional_taxes'));
                    } else {
                        $count = $term['count'];
                    }

                    $count_string = $count;
                }
                //+++
                if ($hide_dynamic_empty_pos AND $count == 0) {
                    continue;
                }
            }

            if (WOOF_REQUEST::get('hide_terms_count_txt')) {
                $count_string = "";
            }

            //excluding hidden terms
            $inreverse = true;
            if (isset(woof()->settings['excluded_terms_reverse'][$tax_slug]) AND woof()->settings['excluded_terms_reverse'][$tax_slug]) {
                $inreverse = !$inreverse;
            }

            if (in_array($term['term_id'], $hidden_terms) == $inreverse) {
                continue;
            }

            //***
            if ($level === 0) {
                if ($not_toggled_terms_count > 0 AND $terms_count_printed === $not_toggled_terms_count) {
                    $hide_next_term_li = true;
                }
            }

            $html = $sd_template;
            $html = str_replace('__ID__', esc_attr('woof_' . $term['term_id'] . '_' . $inique_id), $html);
            $html = str_replace('__CONTENT__', has_filter('woof_before_term_name') ? apply_filters('woof_before_term_name', $term, $taxonomy_info) : $term['name'], $html);
            $html = str_replace('__DISABLED__', !$count AND!in_array($term['slug'], $current_request) AND $show_count ? 'disabled=""' : '', $html);
            $html = str_replace('__DATA_TAX__', esc_attr(woof()->check_slug($tax_slug)), $html);
            $html = str_replace('__SLUG__', esc_attr($term['slug']), $html);
            $html = str_replace('__TERM_ID__', esc_attr($term['term_id']), $html);
            $html = str_replace('__VALUE__', esc_attr($term['term_id']), $html);
            $html = str_replace('__CHECKED__', in_array($term['slug'], $current_request) ? 'checked=""' : '', $html);
            $html = str_replace('__COUNT__', wp_kses_post(wp_unslash($count_string)), $html);
            $html = str_replace('__TERM_NAME__', esc_attr($term['name']), $html);
            $html = str_replace('__DATA_ANCHOR__', esc_attr(woof()->check_slug($tax_slug)) . '_' . esc_attr($term['slug']), $html);

            if ($hide_next_term_li AND $level === 0) {
                $html = str_replace('__CLASS__', 'woof_hidden_term', $html);
            } else {
                $html = str_replace('__CLASS__', '', $html);
            }

            //+++

            if (str_contains($html, '__RESET_RADIO_BTN__')) {
                $html = str_replace('__RESET_RADIO_BTN__', WOOF_HELPER::generate_html_item('a', [
                            'href' => '#',
                            'data-name' => esc_attr(woof()->check_slug($tax_slug)),
                            'data-term-id' => esc_attr($term['term_id']),
                            'style' => !in_array($term['slug'], $current_request) ? 'display: none;' : '',
                            'class' => 'woof_radio_term_reset woof_radio_term_reset_' . esc_attr($term['term_id']) . ' ' . (in_array($term['slug'], $current_request) ? 'woof_radio_term_reset_visible' : '')
                                ], WOOF_HELPER::generate_html_item('img', [
                                    'src' => woof()->settings['delete_image'],
                                    'height' => 12,
                                    'width' => 12,
                                    'alt' => esc_html__("Delete", 'woocommerce-products-filter')
                                ])), $html);
            } else {
                $html = str_replace('__RESET_RADIO_BTN__', '', $html);
            }

            //+++

            if ($sd_type === 'color') {
                $html = str_replace('__COLOR__', apply_filters('get_woof_sd_term_color', intval($term['term_id'])), $html);
                $image = apply_filters('get_woof_sd_term_color_image', intval($term['term_id']));
                $html = str_replace('__IMAGE__', $image, $html);
                if (str_contains($image, 'http')) {
                    $html = str_replace('__CLASS_HAS_IMAGE__', 'woof-sd-color-has-image', $html);
                } else {
                    $html = str_replace('__CLASS_HAS_IMAGE__', '', $html);
                }

                if (!empty($count_string)) {
                    $html = str_replace('__TOOLTIP_TEXT__', "<span class='woof-sd-tooltiptext'>{$term['name']} <b>({$count_string})</b></span>", $html);
                } else {
                    $html = str_replace('__TOOLTIP_TEXT__', "<span class='woof-sd-tooltiptext'>{$term['name']}</span>", $html);
                }
            }

            //+++

            $use_subterms = intval($sd_data['templates'][$sd_template_num]['use_subterms']);

            if (!empty($term['childs']) AND $use_subterms) {
                $html = str_replace('__OPENER__', '<woof-sd-list-opener></woof-sd-list-opener>', $html);
                $style = '';
                if ($hide_childs === 1) {
                    $style = 'display: none;';
                }
                $html .= '<div class="woof-sd-ie-childs" style="' . $style . '">' . woof_sd_assemble_terms($tax_slug, $term['childs'], $hidden_terms, $hide_childs, $sd_data, $sd_template, $sd_template_num, $current_request, $show_count, $show_count_dynamic, $hide_dynamic_empty_pos, $not_toggled_terms_count, $sd_type, $level + 1) . '</div>';
            } else {
                $html = str_replace('__OPENER__', '', $html);
            }

            $terms_count_printed++;
            $result .= $html;
        }

        return $result;
    }

}

//+++

$sd_style = '';
if (!empty($sd_options)) {
    foreach ($sd_options as $key => $value) {
        //check option for template num
        foreach ($sd_data['sections'] as $kk1 => $section) {
            if (isset($section['table']['rows'][$key][0]['value']['conditions']['templates'])) {
                if (!in_array($sd_template_num, $section['table']['rows'][$key][0]['value']['conditions']['templates'])) {
                    continue 2; //!!
                }
            }
        }

        //+++

        $measure = '';
        if (isset($element_additional_data[$key]['measure'])) {
            $measure = $element_additional_data[$key]['measure'];
        }

        $before = '';
        if (isset($element_additional_data[$key]['before'])) {
            $before = $element_additional_data[$key]['before'];
        }

        $after = '';
        if (isset($element_additional_data[$key]['after'])) {
            $after = $element_additional_data[$key]['after'];
        }

        //check for conditions of relations
        foreach ($sd_data['sections'] as $kk1 => $section) {
            if (isset($section['table']['rows'][$key][0]['value']['conditions']['forced_change'])) {
                $forced_change = $section['table']['rows'][$key][0]['value']['conditions']['forced_change'];
                if (!empty($forced_change)) {
                    foreach ($forced_change as $kk => $rule) {
                        if ($sd_data['sections'][$kk1]['table']['rows'][$kk][0]['value']['value'] == $rule['value']) {

                            //if rule from key which is not current template do nothing
                            if (isset($sd_data['sections'][$kk1]['table']['rows'][$kk]['value']['conditions']['templates'])) {
                                if (!in_array($sd_template_num, $sd_data['sections'][$kk1]['table']['rows'][$kk]['value']['conditions']['templates'])) {
                                    continue;
                                }
                            }

                            if (isset($rule['exclude_in_template'])) {
                                if (in_array($sd_template_num, $rule['exclude_in_template'])) {
                                    continue;
                                }
                            }

                            $value = $rule['set_to'];
                            $measure = $rule['measure'];
                        }
                    }
                }
            }
        }


        $sd_style .= "--woof-sd-ie-{$sd_data_prefix}{$key}: {$before}{$value}{$measure}{$after}; ";
    }
}
?>
<div class="woof_list woof_list_sd woof_list_<?php echo esc_attr($sd_type) ?>_sd woof_list_<?php echo esc_attr($sd_type) ?>_sd_<?php echo esc_attr($sd_template_num) ?>" style="<?php echo esc_attr(trim($sd_style)) ?>">
    <?php
    $woof_tax_values = [];
    $current_request = [];
    $request = $this->get_request_data();
    if ($this->is_isset_in_request_data($this->check_slug($tax_slug))) {
        $current_request = $request[$this->check_slug($tax_slug)];
        $current_request = explode(',', urldecode($current_request));
    }

    static $hide_childs = -1;
    if ($hide_childs === -1) {
        $hide_childs = intval(get_option('woof_checkboxes_slide'));
    }


    //excluding hidden terms
    $hidden_terms = [];
    if (!WOOF_REQUEST::isset('woof_shortcode_excluded_terms')) {
        if (isset(woof()->settings['excluded_terms'][$tax_slug])) {
            $hidden_terms = explode(',', woof()->settings['excluded_terms'][$tax_slug]);
        }
    } else {
        $hidden_terms = explode(',', WOOF_REQUEST::get('woof_shortcode_excluded_terms'));
    }

//***

    $not_toggled_terms_count = 0;
    if (isset(woof()->settings['not_toggled_terms_count'][$tax_slug])) {
        $not_toggled_terms_count = intval(woof()->settings['not_toggled_terms_count'][$tax_slug]);
    }

//***

    $terms = apply_filters('woof_sort_terms_before_out', $terms, $sd_type);
    $terms_count_printed = 0;

    if (!empty($terms) AND is_array($terms)) {
        echo woof_sd_assemble_terms($tax_slug, $terms, $hidden_terms, $hide_childs, $sd_data, $sd_template, $sd_template_num, $current_request, $show_count, $show_count_dynamic, $hide_dynamic_empty_pos, $not_toggled_terms_count, $sd_type, 0);

        if ($not_toggled_terms_count > 0 AND count($terms) > $not_toggled_terms_count) {
            ?>
            <div class="woof_open_hidden_li"><?php WOOF_HELPER::draw_more_less_button($sd_type) ?></div>
            <?php
        }
    }
    ?>
</div>
<?php
//we need it only here, and keep it in WOOF_REQUEST for using in function for child items
WOOF_REQUEST::del('additional_taxes');
