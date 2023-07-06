<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$selected=array();
$options=array();

foreach($attributes as $attr){  
   $slug=$attr->get_name();
   $tax=get_taxonomy($slug);
   if(is_object($tax)){
        $name=$tax->labels->name;
        if($name){
           $options[$slug] =$name;
           if($attr->get_visible()){
              $selected[]= $slug;
           }
        }       
   }        
}
?>

<div class="woobe_multi_select_cell">  
    <div class="woobe_multi_select_cell_list">    
        <div class="popup_val_in_tbl woobe-button" onclick="woobe_multi_select_cell_attr_visible(this)">
            <ul>
                <?php if (!empty($selected)): ?>
                    <?php foreach ($selected as $k => $tax_slug): ?>
                        <li class="woobe_li_tag"><?php echo $options[$tax_slug] ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="woobe_li_tag"><?php echo esc_html__('no items', 'woocommerce-bulk-editor') ?></li>
                    <?php endif; ?>
            </ul>
        </div>
    </div>
    <div class="woobe_multi_select_cell_dropdown" style="display: none;" >
        <?php

        echo WOOBE_HELPER::draw_select(array(
            'field' => $field_key,
            'product_id' => $product_id,
            'class' => 'woobe_data_select chosen-select',
            'options' => $options,
            'selected' => $selected,
                ), true);
        ?>
        <br /><br /> 
        <div class="fl">
            <a href="#" class="page-title-action woobe_multi_select_cell_select"><?php esc_html_e('Select all', 'woocommerce-bulk-editor') ?></a>
            <a href="#" class="page-title-action woobe_multi_select_cell_deselect"><?php esc_html_e('Deselect all', 'woocommerce-bulk-editor') ?></a>
        </div>

        <br /><br />         
        <div class="fr">
            <a href="#" class="page-title-action woobe_multi_select_cell_cancel"><?php esc_html_e('cancel', 'woocommerce-bulk-editor') ?></a>
        </div>


        <div class="fl">
            <a href="#" class="page-title-action woobe_multi_select_cell_save"><?php esc_html_e('save', 'woocommerce-bulk-editor') ?></a>
        </div>

        <div class="clear"></div>

    </div>
</div>