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
if (!function_exists('woof_draw_radio_childs')) {

    function woof_draw_radio_childs($taxonomy_info, $tax_slug, $term_id, $childs, $show_count, $show_count_dynamic, $hide_dynamic_empty_pos, $parent_is_selected) {

        $do_not_show_childs = (int) apply_filters('woof_terms_where_hidden_childs', $term_id);

        if ($do_not_show_childs == 1) {
            return "";
        }

        //***

        
        $request = woof()->get_request_data();
        $current_request = array();
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

        $childs = apply_filters('woof_sort_terms_before_out', $childs, 'radio');
        ?>
        <?php if (!empty($childs)): ?>
            <ul class="woof_childs_list woof_childs_list_<?php echo esc_attr($term_id) ?>" <?php if ($hide_childs == 1 AND!$parent_is_selected): ?>style="display: none;"<?php endif; ?>>
            <?php foreach ($childs as $term) : $inique_id = uniqid(); ?>
                <?php
                $count_string = "";
                $count = 0;
                if (!in_array($term['slug'], $current_request)) {
                    if ($show_count) {
                        if ($show_count_dynamic) {
                            $count = woof()->dynamic_count($term, 'single', WOOF_REQUEST::get('additional_taxes'));
                        } else {
                            $count = $term['count'];
                        }
                        $count_string = '<span class="woof_radio_count">(' . $count . ')</span>';
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
                        <input type="radio" <?php if (!$count AND!in_array($term['slug'], $current_request) AND $show_count): ?>disabled=""<?php endif; ?> id="<?php echo esc_attr('woof_' . $term['term_id'] . '_' . $inique_id) ?>" class="woof_radio_term woof_radio_term_<?php echo esc_attr($term['term_id']) ?>" data-slug="<?php echo esc_attr($term['slug']) ?>" data-term-id="<?php echo esc_attr($term['term_id']) ?>" name="<?php echo esc_attr(woof()->check_slug($tax_slug)) ?>" value="<?php echo esc_attr($term['term_id']) ?>" <?php checked(in_array($term['slug'], $current_request)) ?> /><label class="woof_radio_label woof_radio_label_<?php echo esc_attr($term['slug']) ?> <?php if (in_array($term['slug'], $current_request)): ?>woof_radio_label_selected<?php endif; ?>" for="<?php echo esc_attr('woof_' . $term['term_id'] . '_' . $inique_id) ?>"><?php
                    if (has_filter('woof_before_term_name'))
                        esc_html_e(apply_filters('woof_before_term_name', $term, $taxonomy_info));
                    else
                        echo esc_html($term['name']);
                    ?><?php echo wp_kses_post(wp_unslash($count_string)) ?></label>
                        <a href="#" data-name="<?php echo esc_attr(woof()->check_slug($tax_slug)) ?>" data-term-id="<?php echo esc_attr($term['term_id']) ?>" style="<?php if (!in_array($term['slug'], $current_request)): ?>display: none;<?php endif; ?>" class="woof_radio_term_reset <?php if (in_array($term['slug'], $current_request)): ?>woof_radio_term_reset_visible<?php endif; ?> woof_radio_term_reset_<?php echo esc_attr($term['term_id']) ?>">
                            <img src="<?php echo esc_url(woof()->settings['delete_image']) ?>" height="12" width="12" alt="<?php esc_html_e("Delete", 'woocommerce-products-filter') ?>" />
                        </a>
                    <?php
                    if (!empty($term['childs'])) {
                        woof_draw_radio_childs($taxonomy_info, $tax_slug, $term['term_id'], $term['childs'], $show_count, $show_count_dynamic, $hide_dynamic_empty_pos, in_array($term['slug'], $current_request));
                    }
                    ?>
                        <input type="hidden" value="<?php echo esc_attr($term['name']) ?>" data-anchor="woof_n_<?php echo esc_attr(woof()->check_slug($tax_slug)) ?>_<?php echo esc_attr($term['slug']) ?>" />

                    </li>
                        <?php endforeach; ?>
            </ul>
                    <?php endif; ?>
                    <?php
                }

            }
            ?>

<ul class="woof_list woof_list_radio">
        <?php
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

        $terms = apply_filters('woof_sort_terms_before_out', $terms, 'radio');
        $terms_count_printed = 0;
        $hide_next_term_li = false;
        ?>
    <?php if (!empty($terms)): ?>
        <?php foreach ($terms as $term) : $inique_id = uniqid(); ?>
            <?php
            $count_string = "";
            $count = 0;
            if (!in_array($term['slug'], $current_request)) {
                if ($show_count) {
                    if ($show_count_dynamic) {
                        $count = $this->dynamic_count($term, 'single', WOOF_REQUEST::get('additional_taxes'));
                    } else {
                        $count = $term['count'];
                    }
                    $count_string = '<span class="woof_radio_count">(' . $count . ')</span>';
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

            if ($not_toggled_terms_count > 0 AND $terms_count_printed === $not_toggled_terms_count) {
                $hide_next_term_li = true;
            }
            ?>
            <li class="woof_term_<?php echo esc_attr($term['term_id']) ?> <?php if ($hide_next_term_li): ?>woof_hidden_term<?php endif; ?>">
                <input type="radio" <?php if (!$count AND!in_array($term['slug'], $current_request) AND $show_count): ?>disabled=""<?php endif; ?> id="<?php echo esc_attr('woof_' . $term['term_id'] . '_' . $inique_id) ?>" class="woof_radio_term woof_radio_term_<?php echo esc_attr($term['term_id']) ?>" data-slug="<?php echo esc_attr($term['slug']) ?>" data-term-id="<?php echo esc_attr($term['term_id']) ?>" name="<?php echo esc_attr($this->check_slug($tax_slug)) ?>" value="<?php echo esc_attr($term['term_id']) ?>" <?php checked(in_array($term['slug'], $current_request)) ?> />
                <label class="woof_radio_label <?php if (in_array($term['slug'], $current_request)): ?>woof_radio_label_selected<?php endif; ?>" for="<?php echo esc_attr('woof_' . $term['term_id'] . '_' . $inique_id) ?>"><?php
            if (has_filter('woof_before_term_name'))
                esc_html_e(apply_filters('woof_before_term_name', $term, $taxonomy_info));
            else
                echo esc_html($term['name']);
            ?><?php echo wp_kses_post(wp_unslash($count_string)) ?></label>

                <a href="#" data-name="<?php echo esc_attr($this->check_slug($tax_slug)) ?>" data-term-id="<?php echo esc_attr($term['term_id']) ?>" style="<?php if (!in_array($term['slug'], $current_request)): ?>display: none;<?php endif; ?>" class="woof_radio_term_reset  <?php if (in_array($term['slug'], $current_request)): ?>woof_radio_term_reset_visible<?php endif; ?> woof_radio_term_reset_<?php echo esc_attr($term['term_id']) ?>">
                    <img src="<?php echo esc_url($this->settings['delete_image']) ?>" height="12" width="12" alt="<?php esc_html_e("Delete", 'woocommerce-products-filter') ?>" />
                </a>

            <?php
            if (!empty($term['childs'])) {
                woof_draw_radio_childs($taxonomy_info, $tax_slug, $term['term_id'], $term['childs'], $show_count, $show_count_dynamic, $hide_dynamic_empty_pos, in_array($term['slug'], $current_request));
            }
            ?>
                <input type="hidden" value="<?php echo esc_attr($term['name']) ?>" data-anchor="woof_n_<?php echo esc_attr($this->check_slug($tax_slug)) ?>_<?php echo esc_attr($term['slug']) ?>" />

            </li>
            <?php
            $terms_count_printed++;
        endforeach;
        ?>

        <?php
        if ($not_toggled_terms_count > 0 AND $terms_count_printed > $not_toggled_terms_count):
            ?>
            <li class="woof_open_hidden_li"><?php WOOF_HELPER::draw_more_less_button('radio') ?></li>
        <?php endif; ?>
<?php endif; ?>
</ul>
            <?php
//we need it only here, and keep it in WOOF_REQUEST for using in function for child items
            WOOF_REQUEST::del('additional_taxes');

            