<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $WOOBE;

$title = '';
if ($product_id > 0) {
    $product = $WOOBE->products->get_product($product_id);
    if (!is_object($product)) {
        return;
    }
    $images = (array) $WOOBE->products->get_post_field($product_id, $field_key);

	//delete  empty values 
	$images = array_filter($images, function($value) { return !is_null($value) && $value !== ''; });
 
    $title = $product->get_title();
}

//***

$files_count = count($images);

$images_data = array();
if ($files_count > 0) {
    foreach ($images as $attachment_id) {
        $img = wp_get_attachment_image_src($attachment_id);
        if (isset($img[0])) {
            $images_data[] = array(
                'id' => $attachment_id,
                'url' => $img[0]
            );
        }
    }
}

//***
if (empty($images)) {
    ?>
    <div class="woobe-button" onclick="woobe_act_gallery_editor(this)" data-count="0" data-product_id="<?php echo $product_id ?>" id="popup_val_<?php echo $field_key ?>_<?php echo $product_id ?>" data-key="<?php echo $field_key ?>" data-terms_ids="" data-name="<?php echo sprintf(esc_html__('Product: %s', 'woocommerce-bulk-editor'), $title) ?>">
        <?php printf(esc_html__('Images (%s)', 'woocommerce-bulk-editor'), $files_count) ?>
    </div>
    <?php
} else {
    ?>
    <a href="javascript: void(0);" class="gallery_popup_editor_btn" data-images='<?php echo json_encode($images_data) ?>' onclick="woobe_act_gallery_editor(this)" data-count="<?php echo $files_count ?>" data-product_id="<?php echo $product_id ?>" id="popup_val_<?php echo $field_key ?>_<?php echo $product_id ?>" data-key="<?php echo $field_key ?>" data-terms_ids="" data-name="<?php echo sprintf(esc_html__('Product: %s', 'woocommerce-bulk-editor'), $title) ?>">
        <?php
        foreach ($images_data as $c => $d) {
            if ($c > 2) {
                break;
            }
            ?><img src="<?php echo $d['url'] ?>" alt="" class="woobe_btn_gal_block" /><?php
        }
        ?>
        <?php if ($files_count > 2): ?>
            <span class="woobe_btn_gal_block"><?php echo $files_count ?></span>
        <?php endif; ?>
    </a>
    <?php
}

