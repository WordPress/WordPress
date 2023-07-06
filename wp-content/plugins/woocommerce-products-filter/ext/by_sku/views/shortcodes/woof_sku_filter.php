<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>
<div data-css-class="woof_sku_search_container" class="woof_sku_search_container woof_container woof_container_woof_sku">
    <div class="woof_container_overlay_item"></div>
    <div class="woof_container_inner">
        <?php
        $woof_sku = '';
        $request = $this->get_request_data();

        if (isset($request['woof_sku'])) {
            $woof_sku = $request['woof_sku'];
        }
        //+++
        if (!isset($placeholder)) {
            $p = esc_html__('enter a product sku here ...', 'woocommerce-products-filter');
        }


        if (isset($this->settings['by_sku']['placeholder']) AND!isset($placeholder)) {
            if (!empty($this->settings['by_sku']['placeholder'])) {
                $p = $this->settings['by_sku']['placeholder'];
                $p = WOOF_HELPER::wpml_translate(null, $p);
                $p = esc_html__($p, 'woocommerce-products-filter');
            }


            if ($this->settings['by_sku']['placeholder'] == 'none') {
                $p = '';
            }
        }
        //***
        $unique_id = uniqid('woof_sku_search_');
        ?>

        <div class="woof_show_sku_search_container">
            <a href="javascript:void(0);" data-uid="<?php esc_attr_e($unique_id) ?>" class="woof_sku_search_go <?php esc_attr_e($unique_id) ?>"></a>
            <a href="javascript:void(0);" data-uid="<?php esc_attr_e($unique_id) ?>" class="woof_sku_search_reset <?php esc_attr_e($unique_id) ?>"><span class="dashicons dashicons-dismiss"></span></a>
            <input type="search" class="woof_show_sku_search <?php esc_attr_e($unique_id) ?>" id="<?php esc_attr_e($unique_id) ?>" data-uid="<?php esc_attr_e($unique_id) ?>" placeholder="<?php esc_attr_e(isset($placeholder) ? $placeholder : $p) ?>" name="woof_sku" value="<?php esc_attr_e($woof_sku) ?>" /><br />
            <?php if (isset($this->settings['by_sku']['notes_for_customer']) AND!empty($this->settings['by_sku']['notes_for_customer'])): ?>
                <span class="woof_sku_notes_for_customer"><?php echo stripcslashes(wp_kses_post(wp_unslash($this->settings['by_sku']['notes_for_customer']))); ?></span>
            <?php endif; ?>
        </div>

    </div>
</div>