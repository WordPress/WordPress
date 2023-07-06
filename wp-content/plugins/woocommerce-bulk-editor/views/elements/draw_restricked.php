<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

//global $WOOBE;
if (empty($text)) {
    $text = esc_html__('Not allowed!', 'woocommerce-bulk-editor');
}
?>

<a class="info_helper info_restricked" data-balloon-length="medium" data-balloon-pos="<?= $direction ?>" data-balloon="<?= $text ?>"><img src="<?php echo WOOBE_ASSETS_LINK . 'images/restricted.png' ?>" width="25" alt="" /></a>

