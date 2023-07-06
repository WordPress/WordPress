<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<?php
$request_data = $this->get_request_data();
$min_price = 0;
$max_price = WOOF_HELPER::get_max_price();

$min_price_txt = esc_html__('min price', 'woocommerce-products-filter');
$max_price_txt = esc_html__('max price', 'woocommerce-products-filter');

if (isset($request_data['min_price'])) {
    $min_price = $request_data['min_price'];
}

if (isset($request_data['max_price'])) {
    $max_price = $request_data['max_price'];
}

//+++
$min_price_data = $min_price;
$max_price_data = $max_price;
//WOOCS compatibility
if (class_exists('WOOCS')) {
    $min_price_data = apply_filters('woocs_exchange_value', $min_price_data);
    $max_price_data = apply_filters('woocs_exchange_value', $max_price_data);
}
$price_id = uniqid('woof_txt_price');
?>


<div class="woof_price_filter_txt_container">
    <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($price_id) ?>_from"><?php esc_html_e($min_price_txt) ?></label>
    <input id="<?php echo esc_attr($price_id) ?>_from" type="text" class="woof_price_filter_txt woof_price_filter_txt_from" placeholder="<?php echo esc_html($min_price_txt) ?>" data-value="<?php echo esc_attr($min_price_data) ?>" value="<?php echo esc_attr($min_price) ?>" />&nbsp;
    <label class="woof_wcga_label_hide"  for="<?php echo esc_attr($price_id) ?>_to"><?php esc_html_e($max_price_txt) ?></label>
    <input id="<?php echo esc_attr($price_id) ?>_to" type="text" class="woof_price_filter_txt woof_price_filter_txt_to" placeholder="<?php echo esc_html($max_price_txt) ?>" name="max_price" data-value="<?php echo esc_attr($max_price_data) ?>" value="<?php echo esc_attr($max_price) ?>" />
<?php if (class_exists('WOOCS')): ?>
        &nbsp;(<?php echo esc_html(get_woocommerce_currency_symbol()) ?>)
    <?php endif; ?>

</div>


<?php
