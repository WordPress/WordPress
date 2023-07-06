<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<li data-key="<?php echo esc_attr($key) ?>" class="woof_options_li">

    <?php
    $show = 0;
    if (isset($woof_settings[$key]['show'])) {
        $show = (int) $woof_settings[$key]['show'];
    }
    ?>

    <span class="icon-arrow-combo help_tip woof_drag_and_drope" data-tip="<?php esc_html_e("drag and drope", 'woocommerce-products-filter'); ?>"></span>

    <strong class="woof_fix1"><?php esc_html_e("Search by SKU", 'woocommerce-products-filter'); ?>:</strong>
    
    <span class="icon-question help_tip" data-tip="<?php esc_html_e('Show textinput for searching by products sku', 'woocommerce-products-filter') ?>"></span>

    <div class="select-wrap">
        <select name="woof_settings[<?php echo esc_attr($key) ?>][show]" class="woof_setting_select">
            <option value="0" <?php selected($show, 0) ?>><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
            <option value="1" <?php selected($show, 1) ?>><?php esc_html_e('Yes', 'woocommerce-products-filter') ?></option>
        </select>
    </div>

    <a href="#" data-key="<?php echo esc_attr($key) ?>" data-name="<?php esc_html_e("Search by SKU", 'woocommerce-products-filter'); ?>" class="woof-button js_woof_options js_woof_options_<?php echo esc_attr($key) ?> help_tip" data-tip="<?php esc_html_e('additional options', 'woocommerce-products-filter') ?>"><span class="icon-cog-outline"></span></a>

    <?php
    if (!isset($woof_settings[$key]['logic']) OR empty($woof_settings[$key]['logic'])) {
        $woof_settings[$key]['logic'] = 'LIKE';
    }

    if (!isset($woof_settings[$key]['autocomplete']) OR empty($woof_settings[$key]['autocomplete'])) {
        $woof_settings[$key]['autocomplete'] = 0;
    }

    if (!isset($woof_settings[$key]['autocomplete_items']) OR empty($woof_settings[$key]['autocomplete_items'])) {
        $woof_settings[$key]['autocomplete_items'] = 10;
    }

    if (!isset($woof_settings[$key]['use_for']) OR empty($woof_settings[$key]['use_for'])) {
        $woof_settings[$key]['use_for'] = 'simple';
    }


    if (!isset($woof_settings[$key]['title'])) {
        $woof_settings[$key]['title'] = '';
    }

    if (!isset($woof_settings[$key]['placeholder'])) {
        $woof_settings[$key]['placeholder'] = '';
    }

    if (!isset($woof_settings[$key]['image'])) {
        $woof_settings[$key]['image'] = '';
    }

    if (!isset($woof_settings[$key]['notes_for_customer'])) {
        $woof_settings[$key]['notes_for_customer'] = '';
    }

    if (!isset($woof_settings[$key]['reset_behavior'])) {
        $woof_settings[$key]['reset_behavior'] = 1;
    }
    ?>

    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][logic]" value="<?php echo esc_html($woof_settings[$key]['logic']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][autocomplete]" value="<?php echo esc_html($woof_settings[$key]['autocomplete']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][autocomplete_items]" value="<?php echo esc_html($woof_settings[$key]['autocomplete_items']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][use_for]" value="<?php echo esc_html($woof_settings[$key]['use_for']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][title]" value="<?php echo esc_html($woof_settings[$key]['title']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][placeholder]" value="<?php echo esc_html($woof_settings[$key]['placeholder']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][image]" value="<?php echo esc_url($woof_settings[$key]['image']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][notes_for_customer]" value="<?php echo stripcslashes(wp_kses_post(wp_unslash($woof_settings[$key]['notes_for_customer']))) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][reset_behavior]" value="<?php echo esc_html($woof_settings[$key]['reset_behavior']) ?>" />
    <div id="woof-modal-content-<?php echo esc_attr($key) ?>" style="display: none;">

        <div style="display: none;">
            <div class="woof-form-element-container">

                <div class="woof-name-description">
                    <strong><?php esc_html_e('Title text', 'woocommerce-products-filter') ?></strong>
                    <span><?php esc_html_e('Leave it empty if you not need this', 'woocommerce-products-filter') ?></span>
                </div>

                <div class="woof-form-element">
                    <input type="text" class="woof_popup_option" data-option="title" placeholder="" value="" />
                </div>

            </div>
        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Placeholder text', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Leave it empty if you not need this', 'woocommerce-products-filter') ?></span>
                <span><?php esc_html_e('SKU textinput placeholder. Set "none" if you want leave it empty on the front.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="placeholder" placeholder="" value="" />
            </div>

        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Conditions logic', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('LIKE or Equally', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $logic = array(
                    '=' => esc_html__('Exact match', 'woocommerce-products-filter'),
                    'LIKE' => esc_html__('LIKE', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="logic">
                        <?php foreach ($logic as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>


        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Autocomplete', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Autocomplete relevant variants in SKU textinput', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $autocomplete = array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="autocomplete">
                        <?php foreach ($autocomplete as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Behavior of reset button', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Make filtering after clearing the SKU field or just clear the text input.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $reset_behavior = array(
                    1 => esc_html__('Make filtering', 'woocommerce-products-filter'),
                    0 => esc_html__('Clear text input', 'woocommerce-products-filter'),
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="reset_behavior">
                        <?php foreach ($reset_behavior as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>


        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Autocomplete products count', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('How many show products in the autocomplete list', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="autocomplete_items" placeholder="" value="" />
            </div>

        </div>


        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Use for', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('For which type of products will be realized searching by SKU. Request for variables products creates more mysql queries in database ...', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $use_for = array(
                    'simple' => esc_html__('For simple products only', 'woocommerce-products-filter'),
                    'both' => esc_html__('For simple and for variables products', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="use_for">
                        <?php foreach ($use_for as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>


        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Notes for customer', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Any notes for customer. Example: use comma for searching by more than 1 SKU!', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <textarea class="woof_popup_option" data-option="notes_for_customer"></textarea>
            </div>

        </div>

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Image', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Image for sku search button which appears near input when users typing there any symbols. Better use png. Size is: 20x20 px.', 'woocommerce-products-filter') ?></span>
                <span><?php esc_html_e('Example', 'woocommerce-products-filter') ?>: <?php echo esc_url(WOOF_LINK) ?>img/eye-icon1.png</span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="image" placeholder="" value="" />
                <a href="#" class="woof-button woof_select_image woof_select_image_last"><?php esc_html_e('Select Image', 'woocommerce-products-filter') ?></a>
            </div>

        </div>

    </div>

</li>