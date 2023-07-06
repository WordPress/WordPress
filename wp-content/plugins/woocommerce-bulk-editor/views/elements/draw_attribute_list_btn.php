<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//global $WOOBE;
?>
<div class="popup_val_in_tbl woobe-button" onclick="woobe_multi_select_cell(this)">
    <ul>
        <?php if (!empty($selected_terms_ids)): ?>
            <?php foreach ($selected_terms_ids as $k => $term_id): ?>
                <li class="woobe_li_tag"><?php echo $terms[$term_id] ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="woobe_li_tag"><?php echo esc_html__('no items', 'woocommerce-bulk-editor') ?></li>
            <?php endif; ?>
    </ul>
</div>
