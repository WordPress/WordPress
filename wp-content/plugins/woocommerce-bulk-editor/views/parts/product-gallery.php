<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<br />
<!-- <div class="woobe_gallery_popup_operations">
    <div class="col-lg-9">
        <input type="checkbox" value="0" id="woobe_remove_gall_attach" />&nbsp;<label for="woobe_remove_gall_attach"><?php esc_html_e('Remove images also from medialibrary if detached of the product', 'woocommerce-bulk-editor') ?></label>
    </div>
    <div class="col-lg-3 tar">
        <a href="#" class="button button-primary woobe_gall_file_delete_all" title="<?php esc_html_e('Remove all images', 'woocommerce-bulk-editor') ?>"><?php esc_html_e('Clean', 'woocommerce-bulk-editor') ?></a>
    </div>
    <div class="clear"></div>
</div> -->


<ul class="woobe_fields_tmp">

    <?php if (!empty($images)): ?>
        <?php
        foreach ($images as $attachment_id) :
            $img = wp_get_attachment_image_src($attachment_id);
            ?>
            <li>
                <img src="<?php echo $img[0] ?>" alt="" class="woobe_gal_img_block" />
                <a href="#" class="woobe_gall_file_delete" title="<?php esc_html_e('Detach image of the product', 'woocommerce-bulk-editor') ?>"><span class="icon-trash button"></span></a>
                <input type="hidden" name="woobe_gallery_images[]" value="<?php echo intval($attachment_id); ?>" />
            </li>
        <?php endforeach; ?>
    <?php endif; ?>

</ul>


