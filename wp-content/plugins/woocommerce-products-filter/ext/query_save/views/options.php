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

    <strong class="woof_fix1"><?php esc_html_e("Save search query", 'woocommerce-products-filter'); ?>:</strong>

    
    <span class="icon-question help_tip" data-tip="<?php esc_html_e('User can save the search query', 'woocommerce-products-filter') ?>"></span>

    <div class="select-wrap">
        <select name="woof_settings[<?php echo esc_attr($key) ?>][show]" class="woof_setting_select">
            <option value="0" <?php selected($show, 0) ?>><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
            <option value="1" <?php selected($show, 1) ?>><?php esc_html_e('Yes', 'woocommerce-products-filter') ?></option>
        </select>
    </div>


    <a href="#" data-key="<?php echo esc_attr($key) ?>" data-name="<?php esc_html_e("Products Messenger", 'woocommerce-products-filter'); ?>" class="woof-button js_woof_options js_woof_options_<?php echo esc_attr($key) ?> help_tip" data-tip="<?php esc_html_e('additional options', 'woocommerce-products-filter') ?>"><span class="icon-cog-outline"></span></a>



    <?php
    $cron_key = "";
    if (!isset($woof_settings[$key]['show_label'])) {
        $woof_settings[$key]['show_label'] = 1;
    }
    if (!isset($woof_settings[$key]['label'])) {
        $woof_settings[$key]['label'] = esc_html__('Save current search query', 'woocommerce-products-filter');
    }
    if (!isset($woof_settings[$key]['placeholder'])) {
        $woof_settings[$key]['placeholder'] = esc_html__('Title of the Query*', 'woocommerce-products-filter');
    }
    if (!isset($woof_settings[$key]['btn_label'])) {
        $woof_settings[$key]['btn_label'] = esc_html__('Add this query', 'woocommerce-products-filter');
    }
    if (!isset($woof_settings[$key]['search_count'])) {
        $woof_settings[$key]['search_count'] = 2;
    }
    if (!isset($woof_settings[$key]['show_notice'])) {
        $woof_settings[$key]['show_notice'] = 0;
    }
    if (!isset($woof_settings[$key]['notes_for_customer'])) {
        $woof_settings[$key]['notes_for_customer'] = "";
    }
    if (!isset($woof_settings[$key]['show_notice_product'])) {
        $woof_settings[$key]['show_notice_product'] = 0;
    }
    if (!isset($woof_settings[$key]['show_notice_text'])) {
        $woof_settings[$key]['show_notice_text'] = esc_html__('This product matches your search %title%.', 'woocommerce-products-filter');
    }
    if (!isset($woof_settings[$key]['show_notice_tex_not'])) {
        $woof_settings[$key]['show_notice_text_not'] = esc_html__('Sorry! This product is not suitable for your search %title%.', 'woocommerce-products-filter');
        ;
    }
    ?>
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_label]" value="<?php echo intval($woof_settings[$key]['show_label']) ?>" /> 
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][label]" value="<?php echo esc_html($woof_settings[$key]['label']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][placeholder]" value="<?php echo esc_html($woof_settings[$key]['placeholder']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][btn_label]" value="<?php echo esc_html($woof_settings[$key]['btn_label']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][notes_for_customer]" value="<?php echo stripcslashes(wp_kses_post(wp_unslash($woof_settings[$key]['notes_for_customer']))); ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][search_count]" value="<?php echo intval($woof_settings[$key]['search_count']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_notice]" value="<?php echo intval($woof_settings[$key]['show_notice']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_notice_product]" value="<?php echo intval($woof_settings[$key]['show_notice_product']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_notice_text]" value="<?php echo wp_kses_post(wp_unslash($woof_settings[$key]['show_notice_text'])) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_notice_text_not]" value="<?php echo wp_kses_post(wp_unslash($woof_settings[$key]['show_notice_text_not'])) ?>" />
    <div id="woof-modal-content-<?php echo esc_attr($key) ?>" style="display: none;">

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Label', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('The text before the block of subscription block', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="label" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Placeholder', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('The placeholder  in title field', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="placeholder" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Button label', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('The text in the button', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="btn_label" placeholder="" value="" />
            </div>

        </div>        

        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Max saved queries per user', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Maximum number of subscriptions for a single registered user.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="search_count" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Notes for customer', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Any text notes for customer under subscription form.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <textarea class="woof_popup_option" data-option="notes_for_customer"></textarea>
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Show notice', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Display message if current product is suitable for saved search', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $show_notice = array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes(only if the product exists)', 'woocommerce-products-filter'),
                    2 => esc_html__('Yes', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="show_notice">
                        <?php foreach ($show_notice as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>  
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Show notice on product page', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Display message if current product is suitable for saved search', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="show_notice_product">
                        <?php foreach ($show_notice as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>   
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Text if current product is suitable for saved searches', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Any text notes for customer. Example: This product matches your search: %title%.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <textarea class="woof_popup_option" data-option="show_notice_text"></textarea>
            </div>

        </div>        
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Text if current product is not suitable for saved searches', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Any text notes for customer. Example: Sorry! This product is not suitable for your search %title%.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <textarea class="woof_popup_option" data-option="show_notice_text_not"></textarea>
            </div>

        </div> 
    </div>


</li>
