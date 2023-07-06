<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $WOOBE;
$btn_text = esc_html__('Content[empty]', 'woocommerce-bulk-editor');
if($val){
	$btn_text = esc_html__('Content', 'woocommerce-bulk-editor');
	if ($WOOBE->settings->show_text_editor) {
		$btn_text = wp_trim_words($val , 15);
		if(!$btn_text){
			$btn_text = esc_html__('Content', 'woocommerce-bulk-editor');
		}
	}	
}

?>

<div class="woobe-button text-editor-standart" data-text-title="<?php echo $WOOBE->settings->show_text_editor ?>" onclick="woobe_act_popupeditor(this, <?php echo intval($post['post_parent']) ?>)" data-product_id="<?php echo $post['ID'] ?>" id="popup_val_<?php echo $field_key ?>_<?php echo $post['ID'] ?>" data-key="<?php echo $field_key ?>" data-terms_ids="" data-name="<?php echo sprintf(esc_html__('Product: %s', 'woocommerce-bulk-editor'), $post['post_title']) ?>">
    <?php echo $btn_text ?>
</div>
