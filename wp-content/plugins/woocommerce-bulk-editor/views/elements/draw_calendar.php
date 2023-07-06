<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
//echo date('d-m-Y H:i:s',$val);
//global $WOOBE;
 
$format = 'd/m/Y';
if($time){
    $format = 'd/m/Y H:i';
}
?>

<input type="text" onmouseover="woobe_init_calendar(this)" data-time="<?php echo ($time)?"true":"false" ?>" data-title="<?php echo str_replace('"', '', strip_tags($product_title)) ?>" data-val-id="calendar_<?php echo $field_key ?>_<?php echo $product_id ?>" value="<?php if ($val) echo date($format, $val) ?>" class="woobe_calendar" placeholder="<?php echo ($print_placeholder ? $product_title : '') ?>" />
<input type="hidden" data-key="<?php echo $field_key ?>" data-product-id="<?php echo $product_id ?>" id="calendar_<?php echo $field_key ?>_<?php echo $product_id ?>" value="<?php echo $val ?>" name="<?php echo $name ?>" />
<a href="javascript: void(0);" class="woobe_calendar_cell_clear"><?php echo esc_html__('clear', 'woocommerce-bulk-editor') ?></a>
