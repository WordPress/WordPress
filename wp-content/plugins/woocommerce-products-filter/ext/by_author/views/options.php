<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');
?>

<li data-key="<?php echo esc_attr($key) ?>" class="woof_options_li">

    <?php
    $show = 0;
    if (isset($woof_settings[$key]['show'])) {
        $show = $woof_settings[$key]['show'];
    } else {
        $show = get_option('woof_show_author_search', 0);
    }
    ?>


    <span class="icon-arrow-combo help_tip woof_drag_and_drope" data-tip="<?php esc_html_e("drag and drope", 'woocommerce-products-filter'); ?>"></span>

    <strong class="woof_fix1"><?php esc_html_e("Search by Author", 'woocommerce-products-filter'); ?>:</strong>
    
    <span class="icon-question help_tip" data-tip="<?php esc_html_e('Show Search by author drop-down inside woof search form', 'woocommerce-products-filter') ?>"></span>

    <div class="select-wrap">
        <select name="woof_settings[<?php echo esc_attr($key) ?>][show]" class="woof_setting_select">
            <option value="0" <?php selected($show, 0) ?>><?php esc_html_e('No', 'woocommerce-products-filter') ?></option>
            <option value="1" <?php selected($show, 1) ?>><?php esc_html_e('Yes', 'woocommerce-products-filter') ?></option>
        </select>
    </div>

    <a href="#" data-key="<?php echo esc_attr($key) ?>" data-name="<?php esc_html_e("Search by Author", 'woocommerce-products-filter'); ?>" class="woof-button js_woof_options js_woof_options_<?php echo esc_attr($key) ?> help_tip" data-tip="<?php esc_html_e('additional options', 'woocommerce-products-filter') ?>"><span class="icon-cog-outline"></span></a>


    <?php
    if (!isset($woof_settings[$key]['placeholder'])) {
        $woof_settings[$key]['placeholder'] = '';
    }

    if (!isset($woof_settings[$key]['role'])) {
        $woof_settings[$key]['role'] = 0;
    }
    if (!isset($woof_settings[$key]['view'])) {
        $woof_settings[$key]['view'] = 'drop-down';
    }
    ?>

    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][placeholder]" value="<?php echo wp_kses_post(wp_unslash($woof_settings[$key]['placeholder'])) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][role]" value="<?php echo esc_html($woof_settings[$key]['role']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][view]" value="<?php echo esc_html($woof_settings[$key]['view']) ?>" />

    <div id="woof-modal-content-<?php echo esc_html($key) ?>" style="display: none;">


        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Placeholder text', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('First drop-down option placeholder text OR title for checkboxes', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="placeholder" placeholder="" value="" />
            </div>

        </div>


        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Role', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('You can define whith which role show users in the drop down', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php global $wp_roles; ?>
                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="role">
                        <option value="0"><?php esc_html_e('all', 'woocommerce-products-filter') ?></option>
                        <?php foreach ($wp_roles->get_names() as $key => $value) : ?>
                            <option value="<?php echo esc_html($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('View', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('View of the search by author ', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $view = array(
                    'drop-down' => esc_html__('Drop-down', 'woocommerce-products-filter'),
                    'checkbox' => esc_html__('Checkbox', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="view">
                        <?php foreach ($view as $key => $value) : ?>
                            <option value="<?php echo esc_html($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>


    </div>

</li>
