<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $WOOBE;

//***

$title = '';
$downloads = array();
if ($product_id > 0) {
    $product = $WOOBE->products->get_product($product_id, FALSE);
    if (!is_object($product)) {
        return;
    }
    $downloadable_files = $product->get_downloads();
    $files_count = count($downloadable_files);
    $title = $product->get_title();
}

if (!empty($downloadable_files)) {
    foreach ($downloadable_files as $file) {
        $file['file'] = esc_attr($file['file']);
        $downloads[] = (array) $file['data'];
    }
}
?>

<div class="woobe-button" onclick="woobe_act_downloads_editor(this)" data-downloads='<?php echo json_encode($downloads,JSON_HEX_APOS) ?>' data-count="<?php echo $files_count ?>" data-product_id="<?php echo $product_id ?>" id="popup_val_<?php echo $field_key ?>_<?php echo $product_id ?>" data-key="<?php echo $field_key ?>" data-terms_ids="" data-name="<?php echo sprintf(esc_html__('Product: %s', 'woocommerce-bulk-editor'), $title) ?>">
    <?php printf(esc_html__('Files (%s)', 'woocommerce-bulk-editor'), $files_count) ?>
</div>


