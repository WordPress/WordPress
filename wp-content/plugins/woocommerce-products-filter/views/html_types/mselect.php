<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php

$collector = array();

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
$woof_hide_dynamic_empty_pos = get_option('woof_hide_dynamic_empty_pos');
if (!function_exists('woof_draw_mselect_childs')) {

    function woof_draw_mselect_childs(&$collector, $taxonomy_info, $term_id, $tax_slug, $childs, $level, $show_count, $show_count_dynamic, $hide_dynamic_empty_pos) {
        $do_not_show_childs = (int) apply_filters('woof_terms_where_hidden_childs', $term_id);

        if ($do_not_show_childs == 1) {
            return "";
        }

        //***

        
        $request = woof()->get_request_data();
        $woof_hide_dynamic_empty_pos = get_option('woof_hide_dynamic_empty_pos');

        $current_request = array();
        if (woof()->is_isset_in_request_data(woof()->check_slug($tax_slug))) {
            $current_request = $request[woof()->check_slug($tax_slug)];
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

        $childs = apply_filters('woof_sort_terms_before_out', $childs, 'mselect');
        ?>
        <?php if (!empty($childs)): ?>
            <?php foreach ($childs as $term) : ?>
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
                        $count_string = '(' . $count . ')';
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
                <option <?php if ($show_count AND $count == 0 AND!in_array($term['slug'], $current_request)): ?>disabled=""<?php endif; ?> value="<?php echo esc_attr($term['slug']) ?>" <?php selected(in_array($term['slug'], $current_request)) ?> class="woof-padding-<?php echo esc_attr($level) ?>"><?php /* echo str_repeat('&nbsp;&nbsp;&nbsp;', esc_attr($level)) */ ?><?php
                if (has_filter('woof_before_term_name'))
                    esc_html_e(apply_filters('woof_before_term_name', $term, $taxonomy_info));
                else
                    echo esc_html($term['name']);
                ?> <?php echo wp_kses_post(wp_unslash($count_string)) ?></option>
                <?php
                if (!isset($collector[$tax_slug])) {
                    $collector[$tax_slug] = array();
                }
                $collector[$tax_slug][] = array('name' => $term['name'], 'slug' => $term['slug'], 'term_id' => $term['term_id']);

                if (!empty($term['childs'])) {
                    woof_draw_mselect_childs($collector, $taxonomy_info, $term['term_id'], $tax_slug, $term['childs'], $level + 1, $show_count, $show_count_dynamic, $hide_dynamic_empty_pos);
                }
                ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php
        }

    }
    $select_id = "woof_tax_mselect_" . $tax_slug;
    ?>
<label class="woof_wcga_label_hide"  for="<?php echo esc_attr($select_id) ?>"><?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomy_info)); ?></label>
<select id="<?php echo esc_attr($select_id) ?>" class="woof_mselect woof_mselect_<?php echo esc_attr($tax_slug) ?>" data-placeholder="<?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomy_info)) ?>" multiple="" size="<?php echo esc_attr('chosen' == $this->get_select_type() ? 1 : '') ?>" name="<?php echo esc_attr($this->check_slug($tax_slug)) ?>">
    <option value="0"></option>
<?php
$woof_tax_values = array();
$current_request = array();
$request = $this->get_request_data();
$shown_options_tags = 0;
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

$terms = apply_filters('woof_sort_terms_before_out', $terms, 'mselect');
?>
    <?php if (!empty($terms)): ?>
        <?php foreach ($terms as $term) : ?>
            <?php
            $count_string = "";
            $count = 0;
            if (!in_array($term['slug'], $current_request)) {
                if ($show_count) {
                    if ($show_count_dynamic) {
                        $count = $this->dynamic_count($term, 'multi', WOOF_REQUEST::get('additional_taxes'));
                    } else {
                        $count = $term['count'];
                    }
                    $count_string = '(' . $count . ')';
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
            <option <?php if ($show_count AND $count == 0 AND!in_array($term['slug'], $current_request)): ?>disabled=""<?php endif; ?> value="<?php echo esc_attr($term['slug']) ?>" <?php selected(in_array($term['slug'], $current_request)) ?>><?php
            if (has_filter('woof_before_term_name'))
                esc_html_e(apply_filters('woof_before_term_name', $term, $taxonomy_info));
            else
                echo esc_html($term['name']);
            ?> <?php echo wp_kses_post(wp_unslash($count_string)) ?></option>
            <?php
            if (!isset($collector[$tax_slug])) {
                $collector[$tax_slug] = array();
            }

            $collector[$tax_slug][] = array('name' => $term['name'], 'slug' => $term['slug'], 'term_id' => $term['term_id']);

            //+++

            if (!empty($term['childs'])) {
                woof_draw_mselect_childs($collector, $taxonomy_info, $term['term_id'], $tax_slug, $term['childs'], 1, $show_count, $show_count_dynamic, $hide_dynamic_empty_pos);
            }

            $shown_options_tags++;
            ?>
        <?php endforeach; ?>
    <?php endif; ?>
</select>
    <?php if ($shown_options_tags == 0): ?>
    <input type="hidden" class="woof_hide_empty_container_ms" value=".woof_container_<?php echo esc_attr($tax_slug) ?>">

    <?php endif; ?>

        <?php
//this is for woof_products_top_panel
        if (!empty($collector)) {
            foreach ($collector as $ts => $values) {
                if (!empty($values)) {
                    foreach ($values as $value) {
                        ?>
                <input type="hidden" value="<?php echo esc_html($value['name']) ?>" data-anchor="woof_n_<?php echo esc_attr($this->check_slug($ts)) ?>_<?php echo esc_attr($value['slug']) ?>" />
                    <?php
                }
            }
        }
    }

//we need it only here, and keep it in WOOF_REQUEST for using in function for child items
    WOOF_REQUEST::del('additional_taxes');
    