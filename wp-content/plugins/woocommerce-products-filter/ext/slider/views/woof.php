<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php

WOOF_REQUEST::set('additional_taxes', $additional_taxes);
//***
$request = $this->get_request_data();
//excluding hidden terms
$hidden_terms = array();
if (!WOOF_REQUEST::isset('woof_shortcode_excluded_terms')) {
    if (isset(woof()->settings['excluded_terms'][$tax_slug])) {
        $hidden_terms = explode(',', woof()->settings['excluded_terms'][$tax_slug]);
    }
} else {
    $hidden_terms = explode(',', WOOF_REQUEST::get('woof_shortcode_excluded_terms'));
}

$request = woof()->get_request_data();
if (woof()->is_isset_in_request_data($tax_slug)) {
    $current_request = $request[$tax_slug];
    $current_request = explode(',', urldecode($current_request));
} else {
    $current_request = array();
}
$from = '';
$to = '';
if(count($current_request)){
	$from = $current_request[0];
	$to = $current_request[array_key_last($current_request)];
}

//***
$terms = apply_filters('woof_sort_terms_before_out', $terms, 'slider');

$use_text_range = true;

$values_js = array();
$titles_js = array();
$max = 0;
$all = array();
$sum_count = 0;
$grid_step = 0;

if (!empty($terms)) {
	
    foreach ($terms as $term) {
        //excluding hidden terms
        $inreverse = true;
        if (isset(woof()->settings['excluded_terms_reverse'][$tax_slug]) AND woof()->settings['excluded_terms_reverse'][$tax_slug]) {
            $inreverse = !$inreverse;
        }
        if (in_array($term['term_id'], $hidden_terms) == $inreverse) {
            continue;
        }
        //***
        //hiding empty marks in the range-slider, not in production
        if (isset($this->settings['slider_dynamic_recount'][$tax_slug]) AND $this->settings['slider_dynamic_recount'][$tax_slug]) {
            $count = (int) $this->dynamic_count($term, 'multi', WOOF_REQUEST::get('additional_taxes'));
            if ($count <= 0 AND!in_array($term['slug'], $current_request)) {
                continue;
            }
        }

        $sum_count++;
        //***
        $values_js[] = $term['slug'];
        $titles_js[] = $term['name'];
		
		if ($from == $term['slug']) {
			$from = $term['name'];
		}
		if ($to == $term['slug']) {
			$to = $term['name'];
		}		
		if (!$use_text_range) {
			?> 
			<input type="hidden" value="<?php echo esc_attr($term['name']) ?>" data-anchor="woof_n_<?php echo esc_attr($tax_slug) ?>_<?php echo esc_attr($term['slug']) ?>" />
			<?php
		}
    }
	
	if ($use_text_range) {
		if ( $from == $to) {
			$top_panel_txt = $from;
		} else {
			$top_panel_txt = sprintf(esc_html__("from %s to %s", 'woocommerce-products-filter'), $from, $to);
		}
		?>	
		<input type="hidden"  value="<?php echo esc_html($top_panel_txt); ?>" data-anchor="woof_n_<?php echo esc_attr($tax_slug) ?>_all_range" />	
		<?php
	}
}
if (isset($this->settings['slider_grid_step'][$tax_slug]) AND!empty($this->settings['slider_grid_step'][$tax_slug])) {
    $grid_step = $this->settings['slider_grid_step'][$tax_slug];
}
//***
$max = count($values_js);

$values_js = implode(',', $values_js);

$titles_js = implode(',', $titles_js);

$current = isset($request[$tax_slug]) ? $request[$tax_slug] : '';

$skin = 'round';
if (isset(woof()->settings['ion_slider_skin'])) {
    $skin = woof()->settings['ion_slider_skin'];
}
$skin = WOOF_HELPER::check_new_ion_skin($skin);
if (isset($this->settings['tax_slider_skin'][$tax_slug]) AND $this->settings['tax_slider_skin'][$tax_slug]) {
    $skin = $this->settings['tax_slider_skin'][$tax_slug];
}

$slider_id = "woof_slider_" . $tax_slug;
?>

<?php if ($sum_count > 1): ?>
    <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($slider_id) ?>"><?php echo esc_html(WOOF_HELPER::wpml_translate($taxonomies_info[$tax_slug])); ?></label>		
    <input class="woof_taxrange_slider" value='' 
           data-skin="<?php echo esc_attr($skin) ?>" 
           data-grid_step="<?php echo esc_attr($grid_step) ?>" 
           data-current="<?php echo esc_attr($current) ?>" 
           data-max='<?php echo esc_attr($max) ?>' 
           data-slags='<?php echo esc_attr($values_js) ?>' 
           data-values='<?php echo esc_attr($titles_js) ?>' 
           data-tax="<?php echo esc_attr($tax_slug) ?>" 
           id="<?php echo esc_attr($slider_id) ?>"/>
           <?php else:
           ?> 
    <div class="woof_hide_slider"></div>
<?php endif; ?>

<?php
//we need it only here, and keep it in WOOF_REQUEST for using in function for child items
WOOF_REQUEST::del('additional_taxes');
