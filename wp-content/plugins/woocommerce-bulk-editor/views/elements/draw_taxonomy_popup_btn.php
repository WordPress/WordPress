<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//global $WOOBE;
?>

<div class="popup_val_in_tbl woobe-button js_woobe_tax_popup" onclick="woobe_act_tax_popup(this)" 
	 data-product-id="<?php echo $post['ID'] ?>" 
	 id="popup_val_ids_<?php echo $tax_key ?>_<?php echo $post['ID'] ?>" 
	 data-terms-ids="<?php echo implode(',', $data['terms_ids']) ?>" 
	 data-key="<?php echo $tax_key ?>" 
	 data-name="<?php esc_attr_e($post['post_title']) ?>">
    <ul>
        <?php if (!empty($data['terms_ids'])): ?>
            <?php foreach ($data['terms_ids'] as $k => $term_id): ?>
                <li class="woobe_li_tag"><?php echo $data['terms_titles'][$k] ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="woobe_li_tag"><?php echo esc_html__('no items', 'woocommerce-bulk-editor') ?></li>
            <?php endif; ?>
    </ul>
</div>
