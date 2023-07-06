<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>
<input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_title_label]" value="<?php echo intval(isset($settings[$key]['show_title_label']) ? $settings[$key]['show_title_label'] : 1) ?>" /> 
<input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_toggle_button]" value="<?php echo intval(isset($settings[$key]['show_toggle_button']) ? $settings[$key]['show_toggle_button'] : 0) ?>" /> 
<input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][tooltip_text]" value="<?php echo wp_kses_post(wp_unslash(isset($settings[$key]['tooltip_text']) ? stripcslashes($settings[$key]['tooltip_text']) : "")) ?>" />
<input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][format]" value="<?php echo wp_kses_post(wp_unslash(isset($settings[$key]['format']) ? stripcslashes($settings[$key]['format']) : 'mm/dd/yy')) ?>" />

<div id="woof-modal-content-<?php echo esc_attr($key) ?>" style="display: none;">
    <div class="woof-form-element-container">
        <div class="woof-name-description">
            <strong><?php esc_html_e('Show title label', 'woocommerce-products-filter') ?></strong>
            <span><?php esc_html_e('Show/Hide meta block title on the front', 'woocommerce-products-filter') ?></span>
        </div>

        <div class="woof-form-element">
            <?php
            $show_title = array(
                0 => esc_html__('No', 'woocommerce-products-filter'),
                1 => esc_html__('Yes', 'woocommerce-products-filter')
            );
            ?>

            <div class="select-wrap">
                <select class="woof_popup_option" data-option="show_title_label">
                    <?php foreach ($show_title as $id => $value) : ?>
                        <option value="<?php echo esc_attr($id) ?>"><?php echo esc_html($value) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

    </div> 
    <div class="woof-form-element-container">
        <div class="woof-name-description">
            <strong><?php esc_html_e('Show toggle button', 'woocommerce-products-filter') ?></strong>
            <span><?php esc_html_e('Show toggle button near the title on the front above the block of html-items', 'woocommerce-products-filter') ?></span>
        </div>

        <div class="woof-form-element">
            <?php
            $show_toogle = array(
                0 => esc_html__('No', 'woocommerce-products-filter'),
                1 => esc_html__('Yes, show as closed', 'woocommerce-products-filter'),
                2 => esc_html__('Yes, show as opened', 'woocommerce-products-filter')
            );
            ?>

            <div class="select-wrap">
                <select class="woof_popup_option" data-option="show_toggle_button">
                    <?php foreach ($show_toogle as $id => $value) : ?>
                        <option value="<?php echo esc_attr($id) ?>"><?php echo esc_html($value) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

    </div>    
    <div class="woof-form-element-container">

        <div class="woof-name-description">
            <strong><?php esc_html_e('Tooltip', 'woocommerce-products-filter') ?></strong>
            <span><?php esc_html_e('Show tooltip', 'woocommerce-products-filter') ?></span>
        </div>

        <div class="woof-form-element">

            <div class="select-wrap">
                <textarea class="woof_popup_option" data-option="tooltip_text" ></textarea>
            </div>

        </div>

    </div>
    <div class="woof-form-element-container">
        <div class="woof-name-description">
            <strong><?php esc_html_e('jQuery-ui calendar date format', 'woocommerce-products-filter') ?></strong>
            <span>&nbsp;</span>
        </div>

        <div class="woof-form-element">
            <?php
            $calendar_date_formats = array(
                'mm/dd/yy' => esc_html__("Default - mm/dd/yy", 'woocommerce-products-filter'),
                'dd-mm-yy' => esc_html__("Europe - dd-mm-yy", 'woocommerce-products-filter'),
                'yy-mm-dd' => esc_html__("ISO 8601 - yy-mm-dd", 'woocommerce-products-filter'),
                'd M, y' => esc_html__("Short - d M, y", 'woocommerce-products-filter'),
                'd MM, y' => esc_html__("Medium - d MM, y", 'woocommerce-products-filter'),
                'D, d M, yy' => esc_html__("Full - DD, d MM, yy", 'woocommerce-products-filter')
            );
            ?>

            <div class="select-wrap">
                <select class="woof_popup_option" data-option="format">
                    <?php foreach ($calendar_date_formats as $id => $value) : ?>
                        <option value="<?php echo esc_attr($id) ?>"><?php echo esc_html($value) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

    </div> 


</div>

