<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

//ask favour
add_action('init', function() {

    if (intval(get_option('woof_manage_rate_alert', 0)) === -2) {
        return; //old rate system mark for already set review users
    }

    $slug = 'woof';

    add_action("wp_ajax_{$slug}_dismiss_rate_alert", function() use($slug) {
        update_option("{$slug}_dismiss_rate_alert", 2);
    });

    add_action("wp_ajax_{$slug}_later_rate_alert", function() use($slug) {
        update_option("{$slug}_later_rate_alert", time() + 4 * 7 * 24 * 60 * 60); //4 weeks
    });

    //+++

    add_action('admin_notices', function() use($slug) {

        if (!current_user_can('manage_options')) {
            return; //show to admin only
        }

        if (intval(get_option("{$slug}_dismiss_rate_alert", 0)) === 2) {
            return;
        }

        if (intval(get_option("{$slug}_later_rate_alert", 0)) === 0) {
            update_option("{$slug}_later_rate_alert", time() + 2 * 7 * 24 * 60 * 60); //14 days after install
            return;
        }

        if (intval(get_option("{$slug}_later_rate_alert", 0)) > time()) {
            return;
        }

        wp_enqueue_script('woof_alert-js', WOOF_LINK . 'lib/alert/js/alert.js', array('jquery'), WOOF_VERSION);

        $link = 'https://codecanyon.net/downloads#item-11498469';
        $on = 'CodeCanyon';
        if (version_compare(WOOF_VERSION, '2.0.0', '<')) {
            $link = 'https://wordpress.org/support/plugin/woocommerce-products-filter/reviews/#new-post';
            $on = 'WordPress';
        }
        ?>
        <div class="notice notice-info" id="pn_<?php echo esc_attr($slug) ?>_ask_favour" style="position: relative;">
            <button onclick="javascript: return pn_<?php echo esc_attr($slug) ?>_dismiss_review(1); void(0);" title="<?php esc_html_e('Later', 'woocommerce-products-filter'); ?>" class="notice-dismiss"></button>
            <div id="pn_<?php echo esc_attr($slug) ?>_review_suggestion">
                <p><?php esc_html_e('Hi! Are you enjoying using HUSKY - Products Filter for WooCommerce?', 'woocommerce-products-filter'); ?></p>
                <p><a href="javascript: pn_<?php echo esc_attr($slug) ?>_set_review(1); void(0);"><?php esc_html_e('Yes, I love it', 'woocommerce-products-filter'); ?></a> 🙂 | <a href="javascript: pn_<?php echo esc_attr($slug) ?>_set_review(0); void(0);"><?php esc_html_e('Not really...', 'woocommerce-products-filter'); ?></a></p>
            </div>

            <div id="pn_<?php echo esc_attr($slug) ?>_review_yes" style="display: none;">
                <p><?php printf(__('That\'s awesome! Could you please do us a BIG favor and give it a 5-star rating on %s to help us spread the word and boost our motivation?', 'woocommerce-products-filter'), $on) ?></p>
                <p><b>~ PluginUs.NET developers team</b></p>
                <p>

                    <a href="<?php echo esc_url_raw($link) ?>" onclick="pn_<?php echo esc_attr($slug) ?>_dismiss_review(2)" target="_blank" style="display: inline-block; margin-right: 10px; color: #2eca8b;"><?php esc_html_e('Okay, you deserve it', 'woocommerce-products-filter'); ?></a>
                    <!----------------- inline css as it total for the system ----------------------->
                    <a href="javascript: pn_<?php echo esc_attr($slug) ?>_dismiss_review(1); void(0);" style="display: inline-block; margin-right: 10px;"><?php esc_html_e('Nope, maybe later', 'woocommerce-products-filter'); ?></a>
                    <a href="javascript: pn_<?php echo esc_attr($slug) ?>_dismiss_review(2); void(0);"><?php esc_html_e('I already did', 'woocommerce-products-filter'); ?></a>
                </p>
            </div>

            <div id="pn_<?php echo esc_attr($slug) ?>_review_no" style="display: none;">
                <p><?php esc_html_e('We are sorry to hear you aren\'t enjoying HUSKY. We would love a chance to improve it. Could you take a minute and let us know what we can do better?', 'woocommerce-products-filter'); ?></p>
                <p>
                    <a href="https://pluginus.net/contact-us/" onclick="pn_<?php echo esc_attr($slug) ?>_dismiss_review(2)" target="_blank"><?php esc_html_e('Give Feedback', 'woocommerce-products-filter'); ?></a>&nbsp;
                    |&nbsp;<a href="javascript: pn_<?php echo esc_attr($slug) ?>_dismiss_review(2); void(0);"><?php esc_html_e('No thanks', 'woocommerce-products-filter'); ?></a>
                </p>
            </div>

        </div>
        <?php
    });
}, 1);
