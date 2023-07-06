<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php
wp_enqueue_script('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/js/ion.rangeSlider.min.js', array('jquery'), WOOF_VERSION);
wp_enqueue_style('ion.range-slider', WOOF_LINK . 'js/ion.range-slider/css/ion.rangeSlider.css', array(), WOOF_VERSION);
$skin = 'round';
if (isset($this->settings['ion_slider_skin'])) {
	$skin = $this->settings['ion_slider_skin'];
}
$skin = WOOF_HELPER::check_new_ion_skin($skin);
if (isset($this->settings['by_price']['price_slider_skin']) && $this->settings['by_price']['price_slider_skin']) {
	$skin = $this->settings['by_price']['price_slider_skin'];
}

$request = $this->get_request_data();
$uniqid = uniqid();
if (!isset($additional_taxes)) {
    $additional_taxes = "";
}
if ($range_min === null) {
	$preset_min = WOOF_HELPER::get_min_price($additional_taxes);
} else {
	$preset_min = $range_min;
}

if ($range_max === null) {
	$preset_max = WOOF_HELPER::get_max_price($additional_taxes);
} else {
	$preset_max = $range_max;
}

if (wc_tax_enabled() && 'incl' === get_option('woocommerce_tax_display_shop') && !wc_prices_include_tax()) {
    $tax_classes = array_merge(array(''), WC_Tax::get_tax_classes());
    $class_max = $preset_max;
	$class_min = $preset_min;
    foreach ($tax_classes as $tax_class) {
        if ($tax_rates = WC_Tax::get_rates($tax_class)) {
            $class_max = ceil($preset_max + WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($preset_max, $tax_rates)));
			$class_min = floor($preset_min + WC_Tax::get_tax_total(WC_Tax::calc_exclusive_tax($preset_min, $tax_rates)));
        }
    }
	$preset_min = $class_min;
    $preset_max = $class_max;
}

$min_price = $this->is_isset_in_request_data('min_price') ? esc_attr($request['min_price']) : $preset_min;
$max_price = $this->is_isset_in_request_data('max_price') ? esc_attr($request['max_price']) : $preset_max;
//***
if (class_exists('WOOCS')) {
    $preset_min = apply_filters('woocs_exchange_value', $preset_min);
    $preset_max = apply_filters('woocs_exchange_value', $preset_max);
    $min_price = apply_filters('woocs_exchange_value', $min_price);
    $max_price = apply_filters('woocs_exchange_value', $max_price);
}
//***
$slider_step = 1;
if (isset($this->settings['by_price']['ion_slider_step'])) {
    $slider_step = $this->settings['by_price']['ion_slider_step'];
    if (!$slider_step) {
        $slider_step = 1;
    }
}
//***
$slider_prefix = '';
$slider_postfix = '';
if (class_exists('WOOCS')) {
    global $WOOCS;
    $currencies = $WOOCS->get_currencies();
    $currency_pos = 'left';
    if (isset($currencies[$WOOCS->current_currency])) {
        $currency_pos = $currencies[$WOOCS->current_currency]['position'];
    }
} else {
    $currency_pos = get_option('woocommerce_currency_pos');
}
switch ($currency_pos) {
    case 'left':
        $slider_prefix = get_woocommerce_currency_symbol();
        break;
    case 'left_space':
        $slider_prefix = get_woocommerce_currency_symbol() . ' ';
        break;
    case 'right':
        $slider_postfix = get_woocommerce_currency_symbol();
        break;
    case 'right_space':
        $slider_postfix = ' ' . get_woocommerce_currency_symbol();
        break;

    default:
        break;
}

//***
//https://wordpress.org/support/topic/results-found/
if ($preset_max < $max_price) {
    $max = $max_price;
} else {
    $max = $preset_max;
}
if ($preset_min > $min_price) {
    $min = $min_price;
} else {
    $min = $preset_min;
}
$tax = 1.0;
if (isset($this->settings['by_price']['price_tax']) AND $this->settings['by_price']['price_tax'] != 0) {
    $tax = $tax + floatval($this->settings['by_price']['price_tax']) / 100.00;
    $min_tax = floor($min * $tax);
    $max_tax = ceil($max * $tax);

    if ($min != $min_price) {
        $min_price = ($min_price * $tax);
    } else {
        $min_price = $min_tax;
    }
    if ($max != $max_price) {
        $max_price = ($max_price * $tax);
    } else {
        $max_price = $max_tax;
    }
    $min = $min_tax;
    $max = $max_tax;
}

if ($min == $max) {
    return false;
}

if (isset($this->settings['by_price']['show_text_input']) AND $this->settings['by_price']['show_text_input']) {
    ?>
    <div class="woof_price_filter_txt_slider">
		<label class="woof_wcga_label_hide"  for="<?php echo esc_attr($uniqid) ?>_from"><?php esc_html_e('Price from', 'woocommerce-products-filter') ?></label>
        <input id="<?php echo esc_attr($uniqid) ?>_from" type="number" class="woof_price_filter_txt woof_price_filter_txt_from" placeholder="<?php echo esc_attr($min_price) ?>" data-value="<?php echo esc_attr($min) ?>" value="<?php echo esc_attr($min_price) ?>" />&nbsp;
        <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($uniqid) ?>_to"><?php esc_html_e('Price to', 'woocommerce-products-filter') ?></label>
		<input id="<?php echo esc_attr($uniqid) ?>_to" type="number" class="woof_price_filter_txt woof_price_filter_txt_to" placeholder="<?php echo esc_attr($max_price) ?>" name="max_price" data-value="<?php echo esc_attr($max) ?>" value="<?php echo esc_attr($max_price) ?>" />
        <?php if (class_exists('WOOCS')): ?>
            &nbsp;(<?php esc_html_e(get_woocommerce_currency_symbol()) ?>)
        <?php endif; ?>
        <div class="woof_float_none"></div>
    </div>
<?php } ?>
<label class="woof_wcga_label_hide"  for="<?php echo esc_attr($uniqid) ?>"><?php esc_html_e('Price filter', 'woocommerce-products-filter') ?></label>
<input class="woof_range_slider" id="<?php echo esc_attr($uniqid) ?>" data-skin="<?php echo esc_attr($skin) ?>" data-taxes="<?php echo esc_attr($tax) ?>" data-min="<?php echo esc_attr($min) ?>" data-max="<?php echo esc_attr($max) ?>" data-min-now="<?php echo esc_attr($min_price) ?>" data-max-now="<?php echo esc_attr($max_price) ?>" data-step="<?php echo esc_attr($slider_step) ?>" data-slider-prefix="<?php echo esc_html($slider_prefix) ?>" data-slider-postfix="<?php echo esc_html($slider_postfix) ?>" value="" />
