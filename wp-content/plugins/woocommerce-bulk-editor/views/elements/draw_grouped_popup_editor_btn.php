<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $WOOBE;

$title = '';
if ($product_id > 0) {
    $product = $WOOBE->products->get_product($product_id);
    $ids = $product->get_children('edit');
    $title = $product->get_title();
}

$files_count = count($ids);

if (empty($ids)) {
    ?>
    <div class="woobe-button" onclick="woobe_act_grouped_editor(this)" id="grouped_ids_<?php echo $field_key ?>_<?php echo $product_id ?>" data-count="0" data-product_id="<?php echo $product_id ?>" data-key="<?php echo $field_key ?>" data-terms_ids="" data-name="<?php echo sprintf(esc_html__('Product: %s', 'woocommerce-bulk-editor'), $title) ?>">
        <?php printf(esc_html__('Products (%s)', 'woocommerce-bulk-editor'), $files_count) ?>
    </div>
    <?php
} else {
    ?>
    <div class="popup_val_in_tbl woobe-button" onclick="woobe_act_grouped_editor(this)" id="grouped_ids_<?php echo $field_key ?>_<?php echo $product_id ?>" data-count="<?php echo $files_count ?>" data-product_id="<?php echo $product_id ?>" data-key="<?php echo $field_key ?>" data-terms_ids="" data-name="<?php echo sprintf(esc_html__('Product: %s', 'woocommerce-bulk-editor'), $title) ?>">
        <ul>
            <?php foreach ($ids as $prod_id): ?>

                <?php
                $p = $WOOBE->products->get_product($prod_id);
                
                if(!is_object($p)){
                    continue;
                }

                $li_data = array(
                    'id' => $prod_id,
                    'title' => $p->get_title(),
                    'link' => $p->get_permalink()
                );

                if (has_post_thumbnail($prod_id)) {
                    $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($prod_id), 'thumbnail');
                    $li_data['thumb'] = $img_src[0];
                } else {
                    $li_data['thumb'] = WOOBE_ASSETS_LINK . 'images/not-found.jpg';
                }
                ?>

                <li class="woobe_li_tag" data-product='<?php echo json_encode($li_data) ?>'>#<?php echo $prod_id ?>.<?php echo $p->get_title() ?></li>
                <?php endforeach; ?>
        </ul>
    </div>
    <?php
}


