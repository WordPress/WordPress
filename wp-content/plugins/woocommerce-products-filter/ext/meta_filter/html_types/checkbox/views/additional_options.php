<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][search_option]" value="<?php echo esc_html((isset($settings[$key]['search_option'])) ? $settings[$key]['search_option'] : 0); ?>" /> 
<input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][search_value]" value="<?php echo esc_html((isset($settings[$key]['search_value'])) ? $settings[$key]['search_value'] : ""); ?>" /> 
<div id="woof-modal-content-<?php echo esc_attr($key) ?>" style="display: none;">
    <div class="woof-form-element-container">
        <div class="woof-name-description">
            <strong><?php esc_html_e('Search option', 'woocommerce-products-filter') ?></strong>
            <span><?php esc_html_e('Search by exact value OR if meta key exists', 'woocommerce-products-filter') ?></span>
        </div>

        <div class="woof-form-element">
            <?php
            $show_title = array(
                0 => esc_html__('Exact value', 'woocommerce-products-filter'),
                1 => esc_html__('Value exists', 'woocommerce-products-filter')
            );
            ?>

            <div class="select-wrap">
                <select class="woof_popup_option" data-option="search_option">
                    <?php foreach ($show_title as $id => $value) : ?>
                        <option value="<?php echo esc_attr($id) ?>"><?php echo esc_html($value) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

        </div>

    </div> 
    <?php if ($type != 'numeric'): ?>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Search value', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('TRUE value, all another are FALSE. Example: yes or true or 1. By default if this textinput empty 1 is true and 0 is false', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="search_value" placeholder="" value="" />
            </div>
        </div>
    <?php endif; ?>
</div>

