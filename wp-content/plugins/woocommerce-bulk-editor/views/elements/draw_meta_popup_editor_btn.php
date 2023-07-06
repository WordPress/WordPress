<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $WOOBE;

$title = '';
$meta_data = array();
if ($product_id > 0) {
    $product = $WOOBE->products->get_product($product_id);
    $title = $product->get_title();

    $meta_data = $WOOBE->products->get_post_field($product_id, $field_key, $product->get_parent_id());
}

$meta_data = json_encode($meta_data, JSON_HEX_QUOT | JSON_HEX_TAG);

if (empty($btn_title)) {
    $btn_title = esc_html__('Array', 'woocommerce-bulk-editor');
}
?>

<div class="woobe-button" onclick="woobe_act_meta_popup_editor(this)" id="meta_popup_<?php echo $field_key ?>_<?php echo $product_id ?>" data-count="0" data-product_id="<?php echo $product_id ?>" data-key="<?php echo $field_key ?>" data-terms_ids="" data-name="<?php echo sprintf(esc_html__('Product: %s', 'woocommerce-bulk-editor'), $title) ?>">
    <div style="display: none;" class="meta_popup_btn_data"><?php echo $meta_data ?></div>
    <?php echo $btn_title ?>
</div>




