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

    <span class="icon-arrow-combo help_tip woof_drag_and_drope" data-tip="<?php esc_html_e("drag and drop", 'woocommerce-products-filter'); ?>"></span>

    <strong class="woof_fix1"><?php esc_html_e("Products Messenger", 'woocommerce-products-filter'); ?>:</strong>


    <span class="icon-question help_tip" data-tip="<?php esc_html_e('Show product mesenger box inside woof search form', 'woocommerce-products-filter') ?>"></span>

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
        $woof_settings[$key]['label'] = esc_html__('Products Messenger', 'woocommerce-products-filter');
    }
    if (!isset($woof_settings[$key]['show_btn_subscr'])) {
        $woof_settings[$key]['show_btn_subscr'] = 0;
    }
    if (!isset($woof_settings[$key]['use_external_cron']) OR empty($woof_settings[$key]['use_external_cron'])) {
        $woof_settings[$key]['use_external_cron'] = bin2hex(random_bytes(12));
    }
    if (!isset($woof_settings[$key]['wp_cron_period'])) {
        $woof_settings[$key]['wp_cron_period'] = 'twicemonthly';
    }
    if (!isset($woof_settings[$key]['subscr_count'])) {
        $woof_settings[$key]['subscr_count'] = 2;
    }
    if (!isset($woof_settings[$key]['header_email'])) {
        $woof_settings[$key]['header_email'] = "New Products by your request";
    }
    if (!isset($woof_settings[$key]['subject_email'])) {
        $woof_settings[$key]['subject_email'] = "New products";
    }
    if (!isset($woof_settings[$key]['text_email'])) {
        $woof_settings[$key]['text_email'] = 'Dear [DISPLAY_NAME], we increased the range of our products. Number of new products: [PRODUCT_COUNT] ';
    }
    if (!isset($woof_settings[$key]['date_expire'])) {
        $woof_settings[$key]['date_expire'] = 'no';
    }
    if (!isset($woof_settings[$key]['count_message'])) {
        $woof_settings[$key]['count_message'] = -1;
    }
    if (!isset($woof_settings[$key]['priority_limit'])) {
        $woof_settings[$key]['priority_limit'] = 'both';
    }
    if (!isset($woof_settings[$key]['notes_for_customer'])) {
        $woof_settings[$key]['notes_for_customer'] = '';
    }
    ?>

    <?php $cron_key = $woof_settings[$key]['use_external_cron'] ?>
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_label]" value="<?php echo intval($woof_settings[$key]['show_label']) ?>" /> 
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][label]" value="<?php echo esc_html($woof_settings[$key]['label']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][notes_for_customer]" value="<?php echo stripcslashes(wp_kses_post(wp_unslash($woof_settings[$key]['notes_for_customer']))); ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][show_btn_subscr]" value="<?php echo intval($woof_settings[$key]['show_btn_subscr']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][wp_cron_period]" value="<?php echo esc_html($woof_settings[$key]['wp_cron_period']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][use_external_cron]" value="<?php echo esc_html($woof_settings[$key]['use_external_cron']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][subscr_count]" value="<?php echo intval($woof_settings[$key]['subscr_count']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][header_email]" value="<?php echo esc_html($woof_settings[$key]['header_email']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][subject_email]" value="<?php echo esc_html($woof_settings[$key]['subject_email']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][text_email]" value="<?php echo wp_kses_post(wp_unslash($woof_settings[$key]['text_email'])) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][date_expire]" value="<?php echo esc_html($woof_settings[$key]['date_expire']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][count_message]" value="<?php echo intval($woof_settings[$key]['count_message']) ?>" />
    <input type="hidden" name="woof_settings[<?php echo esc_attr($key) ?>][priority_limit]" value="<?php echo esc_html($woof_settings[$key]['priority_limit']) ?>" />
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
                <strong><?php esc_html_e('Show subscription button', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Show subscription button without search query. For example, user will be able to subscribe for new products in any products category.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $autocomplete = array(
                    0 => esc_html__('No', 'woocommerce-products-filter'),
                    1 => esc_html__('Yes', 'woocommerce-products-filter')
                );
                ?>

                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="show_btn_subscr">
                        <?php foreach ($autocomplete as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('WordPress cron period', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Period of emailing to subscribed users. Set it to "No" if you going to use external cron.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $wp_cron_periods = array(
                    'no' => esc_html__('No', 'woocommerce-products-filter'),
                    'hourly' => esc_html__('hourly', 'woocommerce-products-filter'),
                    'twicedaily' => esc_html__('twicedaily', 'woocommerce-products-filter'),
                    'daily' => esc_html__('daily', 'woocommerce-products-filter'),
                    'week' => esc_html__('weekly', 'woocommerce-products-filter'),
                    'twicemonthly' => esc_html__('twicemonthly', 'woocommerce-products-filter'),
                    'month' => esc_html__('monthly', 'woocommerce-products-filter'),
                );
                ?>
                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="wp_cron_period">
                        <?php foreach ($wp_cron_periods as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php echo esc_html($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('External cron key (is recommended as flexible for timetable)', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('For external cron use the next link', 'woocommerce-products-filter'); ?>: <i class="woof_cron_link" ><b><?php echo get_home_url() . "?woof_pm_cron_key=" . esc_html($cron_key); ?></b></i><br />
                    <?php esc_html_e('To update the key, just delete it and save the plugin settings. Key should be min 16 symbols.', 'woocommerce-products-filter'); ?> 
                </span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="use_external_cron" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Max subscriptions per user', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Maximum number of subscriptions for a single registered user.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="subscr_count" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Title of the email', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Text in header of the email.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="header_email" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Subject of the email', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Subject of the email.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="subject_email" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Text of the email', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Text in the body of the email. You can use next variables: [DISPLAY_NAME], [USER_NICENAME], [PRODUCT_COUNT].', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <textarea class="woof_popup_option" data-option="text_email" placeholder="" value=""></textarea>
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Subscription time', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('How long user will get emails after subscription', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $expire_periods = array(
                    'no' => esc_html__('No limit', 'woocommerce-products-filter'),
                    'week' => esc_html__('One week', 'woocommerce-products-filter'),
                    'twicemonthly' => esc_html__('Two weeks', 'woocommerce-products-filter'),
                    'month' => esc_html__('One month', 'woocommerce-products-filter'),
                    'twomonth' => esc_html__('Two months', 'woocommerce-products-filter'),
                    'min1' => esc_html__('min1', 'woocommerce-products-filter')
                );
                ?>
                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="date_expire">
                        <?php foreach ($expire_periods as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Emails count', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Maximum number of emails per one subscribed user. -1 means no limit.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <input type="text" class="woof_popup_option" data-option="count_message" placeholder="" value="" />
            </div>

        </div>
        <div class="woof-form-element-container">

            <div class="woof-name-description">
                <strong><?php esc_html_e('Priority of limitations', 'woocommerce-products-filter') ?></strong>
                <span><?php esc_html_e('Which limitation has priority. Event after which user stop getting the emails. Both - means that any first event of two ones, will reset user subscription.', 'woocommerce-products-filter') ?></span>
            </div>

            <div class="woof-form-element">
                <?php
                $priority_limit = array(
                    'by_date' => esc_html__('By date', 'woocommerce-products-filter'),
                    'by_count' => esc_html__('By emails count', 'woocommerce-products-filter'),
                    'both' => esc_html__('Both', 'woocommerce-products-filter'),
                );
                ?>
                <div class="select-wrap">
                    <select class="woof_popup_option" data-option="priority_limit">
                        <?php foreach ($priority_limit as $key => $value) : ?>
                            <option value="<?php echo esc_attr($key) ?>"><?php esc_html_e($value) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

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

    </div>


</li>
