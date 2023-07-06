<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
WOOF_REQUEST::set('additional_taxes', $additional_taxes);
WOOF_REQUEST::set('hide_terms_count_txt', isset($this->settings['hide_terms_count_txt']) ? $this->settings['hide_terms_count_txt'] : 0);
//***
if (WOOF_REQUEST::isset('hide_terms_count_txt_short') AND intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) !== -1) {
    if (intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) === 1) {
        WOOF_REQUEST::set('hide_terms_count_txt', 1);
    } else {
        WOOF_REQUEST::set('hide_terms_count_txt', 0);
    }
}
//***
if (!function_exists('woof_draw_label_childs')) {

    function woof_draw_label_childs($taxonomy_info, $tax_slug, $childs, $show_count, $show_count_dynamic, $hide_dynamic_empty_pos) {
        $do_not_show_childs = (int) apply_filters('woof_terms_where_hidden_childs', $term_id);

        if ($do_not_show_childs == 1) {
            return "";
        }

        //***

        $current_request = array();

        $request = woof()->get_request_data();
        if (woof()->is_isset_in_request_data(woof()->check_slug($tax_slug))) {
            $current_request = $request[woof()->check_slug($tax_slug)];
            $current_request = explode(',', urldecode($current_request));
        }
        //***
        static $hide_childs = -1;
        if ($hide_childs == -1) {
            $hide_childs = (int) get_option('woof_checkboxes_slide');
        }


        //excluding hidden terms
        $hidden_terms = array();
        if (!WOOF_REQUEST::isset('woof_shortcode_excluded_terms')) {
            if (isset(woof()->settings['excluded_terms'][$tax_slug])) {
                $hidden_terms = explode(',', woof()->settings['excluded_terms'][$tax_slug]);
            }
        } else {
            $hidden_terms = explode(',', WOOF_REQUEST::get('woof_shortcode_excluded_terms'));
        }

        $childs = apply_filters('woof_sort_terms_before_out', $childs, 'label');
        ?>
        <?php if (!empty($childs)): ?>
            <ul class="woof_childs_list" <?php if ($hide_childs == 1): ?>style="display: none;"<?php endif; ?>>
                <?php foreach ($childs as $term) : $inique_id = uniqid(); ?>
                    <?php
                    $count_string = "";
                    $count = 0;
                    if (!in_array($term['slug'], $current_request)) {
                        if ($show_count) {
                            if ($show_count_dynamic) {
                                $count = woof()->dynamic_count($term, 'multi', WOOF_REQUEST::get('additional_taxes'));
                            } else {
                                $count = $term['count'];
                            }
                            $count_string = '<span>(' . $count . ')</span>';
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
                    ?>
                    <li <?php if (woof()->settings['dispay_in_row'][$tax_slug] AND empty($term['childs'])): ?>style="display: inline-block !important;"<?php endif; ?>>
                        <input type="checkbox" <?php if (!$count AND!in_array($term['slug'], $current_request) AND $show_count): ?>disabled=""<?php endif; ?> id="<?php echo esc_attr('woof_' . $term['term_id'] . '_' . $inique_id) ?>" class="woof_label_term" data-tax="<?php echo esc_attr(woof()->check_slug($tax_slug)) ?>" name="<?php echo esc_html($term['slug']) ?>" value="<?php echo esc_html($term['term_id']) ?>" <?php checked(in_array($term['slug'], $current_request)) ?> />&nbsp;<label for="<?php echo esc_attr('woof_' . $term['term_id'] . '_' . $inique_id) ?>" <?php if (checked(in_array($term['slug'], $current_request))): ?>style="font-weight: bold;"<?php endif; ?>><?php
                            if (has_filter('woof_before_term_name'))
                                echo wp_kses_post(wp_unslash(apply_filters('woof_before_term_name', $term, $taxonomy_info)));
                            else
                                echo esc_html($term['name']);
                            ?> <?php echo wp_kses_post($count_string) ?></label>
                        <?php
                        if (!empty($term['childs'])) {
                            woof_draw_label_childs($taxonomy_info, $tax_slug, $term['childs'], $show_count, $show_count_dynamic, $hide_dynamic_empty_pos);
                        }
                        ?>
                        <input type="hidden" value="<?php echo esc_html($term['name']) ?>" data-anchor="woof_n_<?php echo esc_attr(woof()->check_slug($tax_slug)) ?>_<?php echo esc_attr($term['slug']) ?>" />

                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <?php
    }

}
?>
<ul class="woof_list woof_list_label">
    <?php
    $woof_tax_values = array();
    $current_request = array();
    $request = $this->get_request_data();
    if ($this->is_isset_in_request_data($this->check_slug($tax_slug))) {
        $current_request = $request[$this->check_slug($tax_slug)];
        $current_request = explode(',', urldecode($current_request));
    }


    //excluding hidden terms
    $hidden_terms = array();
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

    $terms = apply_filters('woof_sort_terms_before_out', $terms, 'label');
    $terms_count_printed = 0;
    $hide_next_term_li = false;
    ?>
    <?php if (!empty($terms)): ?>
        <?php foreach ($terms as $term) : $inique_id = uniqid(); ?>
            <?php
            $count_string = "";
            $count = 0;
            $term_slug = $term['slug'];
            if (!in_array($term_slug, $current_request)) {
                if ($show_count) {
                    if ($show_count_dynamic) {
                        $count = $this->dynamic_count($term, 'multi', WOOF_REQUEST::get('additional_taxes'));
                    } else {
                        $count = $term['count'];
                    }
                    $count_string = '<span class="woof_label_count">' . $count . '</span>';
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

            if ($not_toggled_terms_count > 0 AND $terms_count_printed === $not_toggled_terms_count) {
                $hide_next_term_li = true;
            }

            $checked = in_array($term_slug, $current_request);
            ?>
            <li class="woof_term_<?php echo esc_attr($term['term_id']) ?> <?php if ($hide_next_term_li): ?>woof_hidden_term<?php endif; ?>" style="<?php if ($this->settings['dispay_in_row'][$tax_slug] AND empty($term['childs'])): ?>display: inline-block !important;<?php endif; ?>">
                <?php echo wp_kses_post($count_string) ?>
                <span class="woof_label_term <?php echo(boolval($checked) ? 'checked' : '') ?>">
                    <?php echo esc_html($term['name']); ?>
                    <?php $label_id = 'woof_tax_label_' . $term_slug; ?>
                    <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($label_id) ?>"><?php echo esc_html($term['name']); ?></label>
                    <input id="<?php echo esc_attr($label_id) ?>" style="display: none;" type="checkbox" <?php if (!$count AND!in_array($term_slug, $current_request) AND $show_count): ?>disabled=""<?php endif; ?> id="<?php echo esc_attr('woof_' . $term['term_id'] . '_' . $inique_id) ?>" class="woof_label_term woof_label_term_<?php echo esc_attr($term['term_id']) ?>" data-tax="<?php echo esc_attr($this->check_slug($tax_slug)) ?>" name="<?php echo esc_html($term_slug) ?>" data-name="<?php echo esc_html($term['name']) ?>" data-term-id="<?php echo esc_attr($term['term_id']) ?>" value="<?php echo esc_html($term['term_id']) ?>" <?php checked($checked) ?> />
                    <input type="hidden" value="<?php echo esc_html($term['name']) ?>" data-anchor="woof_n_<?php echo esc_attr($this->check_slug($tax_slug)) ?>_<?php echo esc_attr($term['slug']) ?>" />
                </span>
                <?php
                if (!empty($term['childs'])) {
                    //woof_draw_label_childs($taxonomy_info, $tax_slug, $term['childs'], $show_count, $show_count_dynamic, $hide_dynamic_empty_pos);
                }
                ?>

            </li>
            <?php
            $terms_count_printed++;
        endforeach;
        ?>


        <?php
        if ($not_toggled_terms_count > 0 AND $terms_count_printed > $not_toggled_terms_count):
            ?>
            <li class="woof_open_hidden_li"><?php WOOF_HELPER::draw_more_less_button('label') ?></li>
            <?php endif; ?>
        <?php endif; ?>
</ul>
<?php
//we need it only here, and keep it in WOOF_REQUEST for using in function for child items
WOOF_REQUEST::del('additional_taxes');
