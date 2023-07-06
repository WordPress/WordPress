<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php

$collector = array();
WOOF_REQUEST::set('additional_taxes', $additional_taxes);
WOOF_REQUEST::set('hide_terms_count_txt', isset($this->settings['hide_terms_count_txt']) ? $this->settings['hide_terms_count_txt'] : 0);
$woof_hide_dynamic_empty_pos = (intval(WOOF_VERSION) === 1) ? 0 : get_option('woof_hide_dynamic_empty_pos');
//***
if (WOOF_REQUEST::isset('hide_terms_count_txt_short') AND intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) !== -1) {
    if (intval(WOOF_REQUEST::get('hide_terms_count_txt_short')) === 1) {
        WOOF_REQUEST::set('hide_terms_count_txt', 1);
    } else {
        WOOF_REQUEST::set('hide_terms_count_txt', 0);
    }
}
//***
//get all terms from parent relative to the current selected term
$selected_chain = array();

//show all child and parent drop-downs on the same time as disabled if not selected.
$show_chain_always = $this->settings['show_chain_always'][$tax_slug];

//how many drop-downs to show if $show_chain_always is true
$deep = 0;

if ($show_chain_always) {
    $custom_title_txt = $this->settings['custom_tax_label'][$tax_slug];
    if (stripos($custom_title_txt, '+')) {
        $tmp = explode('+', $custom_title_txt);
        $deep = count($tmp);
    }
}
//***
$selected_chain['chain'] = array();

//+++
$hide_empty = (bool) get_option('woof_hide_dynamic_empty_pos', 0);
//rewrite terms because here is no nessesary look into for $this->is_really_current_term_exists() in public function woof_shortcode()
$terms = apply_filters('woof_sort_terms_before_out', WOOF_HELPER::get_terms($tax_slug, $hide_empty), 'select');
$really_current_term = NULL;
$really_current_term_id = 0;

if (woof()->is_really_current_term_exists()) {
    $really_current_term = woof()->get_really_current_term();
}

if (!empty($really_current_term) AND is_object($really_current_term)) {
    if ($really_current_term->taxonomy == $tax_slug) {
        $really_current_term_id = $really_current_term->term_id;
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $request = $this->get_request_data();
            $is = $this->is_isset_in_request_data($tax_slug);
            add_filter('woof_get_request_data', function () use ($tax_slug, $really_current_term, $request, $is) {
                if (!$is) {
                    $request[$tax_slug] = $really_current_term->slug;
                }
                return $request;
            });
        }
    }
}
//+++

$request = $this->get_request_data();

if ($this->is_isset_in_request_data($tax_slug)) {
    $tmp = explode(',', urldecode($request[$tax_slug]));
    $selected_chain['current'] = get_term_by('slug', $tmp[0], $tax_slug, ARRAY_A);
}

if (isset($selected_chain['current'])) {
    if ($selected_chain['current']['parent'] > 0) {
        //lets get terms chain
        $tmp = array();
        $parent_id = $selected_chain['current']['parent'];
        $selected_chain['chain'][0] = $selected_chain['current']['term_id'];
        $selected_chain['chain'][1] = $parent_id;
        $i = 2;
        while (true) {
            $t = get_term_by('term_id', $parent_id, $tax_slug, ARRAY_A);
            if ($t['parent'] > 0) {
                $selected_chain['chain'][$i] = $parent_id = $t['parent'];
                $i++;
            } else {
                break;
            }
        }
    } else {
        $selected_chain['chain'][0] = $selected_chain['current']['term_id'];
    }
}

//reverse to start from the top parent
$selected_chain['chain'] = array_reverse($selected_chain['chain']);
//***

if (!function_exists('woof_draw_select_childs_h')) {

    function woof_draw_select_childs_h(&$collector, $selected_chain, $parent_data, $show_chain_always) {

        extract($parent_data);
        
        $request = woof()->get_request_data();

        //***

        if (empty($parent_data['childs']) AND $show_chain_always) {
            ?>
            <?php $select_id = 'woof_hh_slider_' . $tax_slug . $level; ?>
            <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($select_id) ?>"><?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomy_info, '', 0)) ?> <?php esc_html_e($level) ?></label>			
            <select id="<?php echo esc_attr($select_id) ?>" class="woof_select woof_select_<?php echo esc_attr($tax_slug) ?> woof_select_<?php echo esc_attr($tax_slug) ?>_<?php echo esc_attr($level) ?>" name="<?php echo esc_attr($tax_slug) ?>" disabled="">
                <option value="0"><?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomy_info, '', $level)) ?></option>
            </select>
            <?php
            if ($level < $deep - 1) {
                $parent_data['level'] += 1;
                woof_draw_select_childs_h($collector, $selected_chain, $parent_data, $show_chain_always);
            }
        } else {

            $woof_hide_dynamic_empty_pos = (intval(WOOF_VERSION) === 1) ? 0 : get_option('woof_hide_dynamic_empty_pos');
            //***
            $current_request = array();
            if (woof()->is_isset_in_request_data($tax_slug)) {
                $current_request = $request[$tax_slug];
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

            $childs = apply_filters('woof_sort_terms_before_out', $childs, 'select');
            $parent_data = array();
            ?>
            <?php if (!empty($childs)): ?>
                <?php $select_id = 'woof_hh_slider_' . $tax_slug . $level; ?>
                <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($select_id) ?>"><?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomy_info, '', 0)) ?> <?php esc_html_e($level) ?></label>
                <select id="<?php echo esc_attr($select_id) ?>" class="woof_select woof_select_<?php echo esc_attr($tax_slug) ?> woof_select_<?php echo esc_attr($tax_slug) ?>_<?php echo esc_attr($level) ?>" name="<?php echo esc_attr($tax_slug) ?>">
                    <option value="0"><?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomy_info, '', $level)) ?></option>
                    <?php foreach ($childs as $term) : ?>
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
                        <option <?php if ($show_count AND $count == 0 AND!in_array($term['slug'], $current_request)): ?>disabled=""<?php endif; ?> value="<?php echo esc_attr($term['slug']) ?>" <?php selected(isset($selected_chain['chain'][$level]) AND ( $selected_chain['chain'][$level] == $term['term_id'])) ?>><?php
                            if (has_filter('woof_before_term_name'))
                                echo esc_html(apply_filters('woof_before_term_name', $term, $taxonomy_info));
                            else
                                esc_html_e($term['name']);
                            ?> <?php echo wp_kses_post(wp_unslash($count_string)) ?></option>
                        <?php
                        if (!isset($collector[$tax_slug])) {
                            $collector[$tax_slug] = array();
                        }

                        $collector[$tax_slug][] = array('name' => $term['name'], 'slug' => $term['slug'], 'term_id' => $term['term_id']);

                        if (isset($selected_chain['chain'][$level]) AND ( $selected_chain['chain'][$level] == $term['term_id'])) {
                            $parent_data['taxonomy_info'] = $taxonomy_info;
                            $parent_data['tax_slug'] = $tax_slug;
                            $parent_data['childs'] = $term['childs'];
                            $parent_data['level'] = $level + 1; //this IS the index for $selected_chain on child drop-down
                            $parent_data['deep'] = $deep;
                            $parent_data['show_count'] = $show_count;
                            $parent_data['show_count_dynamic'] = $show_count_dynamic;
                            $parent_data['hide_dynamic_empty_pos'] = $hide_dynamic_empty_pos;
                        }
                        //+++
                        ?>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <?php
            if (!empty($parent_data)) {
                if (!empty($parent_data['childs'])) {
                    woof_draw_select_childs_h($collector, $selected_chain, $parent_data, $show_chain_always);
                }
            } else {
                if (empty($parent_data) AND $show_chain_always) {
                    $parent_data['taxonomy_info'] = $taxonomy_info;
                    $parent_data['tax_slug'] = $tax_slug;
                    $parent_data['childs'] = array();
                    $parent_data['level'] = $level + 1; //this IS the index for $selected_chain on child drop-down
                    $parent_data['deep'] = $deep;
                    $parent_data['show_count'] = $show_count;
                    $parent_data['show_count_dynamic'] = $show_count_dynamic;
                    $parent_data['hide_dynamic_empty_pos'] = $hide_dynamic_empty_pos;
                    //***
                    if ($level < $deep - 1) {
                        woof_draw_select_childs_h($collector, $selected_chain, $parent_data, $show_chain_always);
                    }
                }
            }
        }
    }

}
$select_id = 'woof_hh_slider_' . $tax_slug;
?>
<label class="woof_wcga_label_hide"  for="<?php echo esc_attr($select_id) ?>"><?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomy_info, '', 0)) ?></label>
<select id="<?php echo esc_attr($select_id) ?>" <?php if ($really_current_term_id > 0 AND array_key_exists($really_current_term_id, $terms)): ?>disabled=""<?php endif; ?> class="woof_select woof_select_<?php echo esc_attr($tax_slug) ?> woof_select_<?php echo esc_attr($tax_slug) ?>_0" name="<?php echo esc_attr($tax_slug) ?>">
    <option value="0"><?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomy_info, '', 0)) ?></option>
    <?php
    $woof_tax_values = array();
    $current_request = array();

    if ($this->is_isset_in_request_data($tax_slug)) {
        $current_request = $request[$tax_slug];
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

    $shown_options_tags = 0;
    $parent_data = array();
    ?>
    <?php if (!empty($terms)): ?>
        <?php foreach ($terms as $term) : ?>
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
            <option <?php if ($show_count AND $count == 0 AND!in_array($term['slug'], $current_request)): ?>disabled=""<?php endif; ?> value="<?php echo esc_attr($term['slug']) ?>" <?php selected(isset($selected_chain['chain'][0]) AND ( $selected_chain['chain'][0] == $term['term_id'])) ?>><?php
                if (has_filter('woof_before_term_name'))
                    echo esc_html(apply_filters('woof_before_term_name', $term, $taxonomy_info));
                else
                    esc_html_e($term['name']);
                ?> <?php echo wp_kses_post(wp_unslash($count_string)) ?></option>
            <?php
            if (!isset($collector[$tax_slug])) {
                $collector[$tax_slug] = array();
            }

            $collector[$tax_slug][] = array('name' => $term['name'], 'slug' => $term['slug'], 'term_id' => $term['term_id']);

            //+++
            //if the current term is selected - lets prepare data for child
            if (isset($selected_chain['chain'][0]) AND ( $selected_chain['chain'][0] == $term['term_id'])) {
                $parent_data['taxonomy_info'] = $taxonomy_info;
                $parent_data['tax_slug'] = $tax_slug;
                $parent_data['childs'] = (isset($term['childs'])) ? $term['childs'] : array();
                $parent_data['level'] = 1; //this IS the index for $selected_chain on child drop-down
                $parent_data['deep'] = $deep;
                $parent_data['show_count'] = $show_count;
                $parent_data['show_count_dynamic'] = $show_count_dynamic;
                $parent_data['hide_dynamic_empty_pos'] = $hide_dynamic_empty_pos;
            }

            $shown_options_tags++;
            ?>
        <?php endforeach; ?>
    <?php endif; ?>
</select>

<?php
if (!empty($parent_data) OR $show_chain_always) {
    if ((isset($parent_data['childs']) AND!empty($parent_data['childs'])) OR $show_chain_always) {
        if (empty($parent_data)) {
            $parent_data['taxonomy_info'] = $taxonomy_info;
            $parent_data['tax_slug'] = $tax_slug;
            $parent_data['childs'] = array();
            $parent_data['level'] = 1; //this IS the index for $selected_chain on child drop-down
            $parent_data['deep'] = $deep;
            $parent_data['show_count'] = $show_count;
            $parent_data['show_count_dynamic'] = $show_count_dynamic;
            $parent_data['hide_dynamic_empty_pos'] = $hide_dynamic_empty_pos;
        }

        if (!empty($parent_data['childs']) OR!isset($request[$tax_slug])) {
            woof_draw_select_childs_h($collector, $selected_chain, $parent_data, $show_chain_always);
        }
    }
}
?>

<?php if ($shown_options_tags == 0): ?>
    <input type="hidden" class="woof_hide_empty_container" value=".woof_container_<?php echo esc_attr($tax_slug) ?>">
<?php endif; ?>

<?php
//this is for woof_products_top_panel
if (!empty($collector)) {
    foreach ($collector as $ts => $values) {
        if (!empty($values)) {
            foreach ($values as $value) {
                ?>
                <input type="hidden" value="<?php echo esc_html($value['name']) ?>" data-anchor="woof_n_<?php echo esc_attr($ts) ?>_<?php echo esc_attr($value['slug']) ?>" />
                <?php
            }
        }
    }
}

//we need it only here, and keep it in WOOF_REQUEST for using in function for child items
WOOF_REQUEST::del('additional_taxes');
