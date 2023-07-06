<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<ul class="woobe_fields_tmp">
    <?php if (!empty($products)): ?>
        <?php
        foreach ($products as $prod_id) :
            if (has_post_thumbnail($prod_id)) {
                $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($prod_id), 'thumbnail');
                //$img_src = woobe_aq_resize($img_src[0], 100, 100, true);
                $img_src = $img_src[0];
            } else {
                $img_src = WOOBE_ASSETS_LINK . 'images/not-found.jpg';
            }
            ?>
            <li class="woobe_options_li">
                <a href="#" class="help_tip woobe_drag_and_drope" title="<?php echo esc_html__('drag and drop', 'woocommerce-bulk-editor') ?>"><img src="<?php echo WOOBE_ASSETS_LINK ?>images/move.png" alt="<?php echo esc_html__('move', 'woocommerce-bulk-editor') ?>" /></a>
                <img src="<?php echo $img_src ?>" alt="" class="woobe_gal_img_block" />&nbsp;
                <a href="<?php echo get_post_permalink($prod_id) ?>" target="_blank"><label><?php echo get_post_field('post_title', $prod_id) ?> (#<?php echo $prod_id ?>)</label></a>
                <a href="#" class="woobe_prod_delete"><span class="icon-trash button"></span></a>
                <input type="hidden" name="woobe_prod_ids[]" value="<?php echo intval($prod_id); ?>" />
            </li>
        <?php endforeach; ?>
    <?php endif; ?>
</ul>
