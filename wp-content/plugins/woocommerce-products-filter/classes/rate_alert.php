<?php
if (!defined('ABSPATH'))
    die('No direct access allowed');

//delete_option('woof_manage_rate_alert');//for tests
class WOOF_RATE_ALERT {

    protected $notes_for_free = true;
    private $show_after_time = 86400 * 2;
    private $meta_key = 'woof_manage_rate_alert';

    public function __construct($for_free) {
        $this->notes_for_free = $for_free;
        add_action('wp_ajax_woof_manage_alert', array($this, 'manage_alert'));
    }

    private function get_time() {
        $time = intval(get_option($this->meta_key, -1));

        if ($time === -1) {
            add_option($this->meta_key, time());
            $time = time();
        }

        if ($time === -2) {
            $time = time(); //user already set review
        }

        return $time;
    }

    public function show_alert() {

        $show = false;

        if (($this->get_time() + $this->show_after_time) <= time()) {
            $show = true;
        }

        //***

        if ($show) {
            if (isset($_GET['tab']) AND $_GET['tab'] == 'woof') {
                $support_link = 'https://pluginus.net/support/forum/woof-woocommerce-products-filter/';
                ?>
                <div style="background: #fff; padding: 15px; border-radius: 4px;" id="woof_rate_alert">
                    <p class="plugin-card-woocommerce-products-filter">
                        <?php printf("Hi, looks like you using <b>HUSKY - Products Filter for WooCommerce</b> for some time and I hope this software helped you with your business. If you satisfied with the plugin functionality, could you please give us BIG favor and give it a 5-star rating to help us spread the word and boost our motivation?<br /><br /><strong>~ PluginUs.NET developers team</strong>", "<a href='{$support_link}' target='_blank'>" . __('support', 'woocommerce-products-filter') . "</a>") ?>
                    </p>

                    <hr />

                    <?php
                    $link = 'https://codecanyon.net/downloads#item-11498469';
                    if ($this->notes_for_free) {
                        $link = 'https://wordpress.org/support/plugin/woocommerce-products-filter/reviews/#new-post';
                    }
                    ?>

                    <table style="width: 100%; margin-bottom: 7px;">
                        <tr>
                            <td style="width: 33%; text-align: left;">
                                <a href="javascript: woof_manage_alert(0);void(0);" class="button button-large dashicons-before dashicons-clock">&nbsp;<?php echo __('Nope, maybe later!', 'woocommerce-products-filter') ?></a>
                            </td>

                            <td style="width: 33%; text-align: center;">
                                <a href="<?= $link ?>" target="_blank" class="woof-panel-button dashicons-before dashicons-star-filled">&nbsp;<?php echo __('Ok, you deserve it', 'woocommerce-products-filter') ?></a>
                            </td>

                            <td style="width: 33%; text-align: right;">
                                <a href="javascript: woof_manage_alert(1);void(0);" class="button button-large dashicons-before dashicons-thumbs-up">&nbsp;<?php echo __('Thank you, I did it!', 'woocommerce-products-filter') ?></a>
                            </td>
                        </tr>
                    </table>


                </div>
                <script>
                    function woof_manage_alert(value) {
                        //1 - did it, 0 - later
                        jQuery('#woof_rate_alert').hide(333);
                        jQuery.post(ajaxurl, {
                            action: "woof_manage_alert",
                            value: value
                        }, function (data) {
                            console.log(data);
                        });
                    }
                </script>

                <?php
            }
        }
    }

    public function manage_alert() {

        if (intval($_REQUEST['value'])) {
            update_option($this->meta_key, -2);
        } else {
            update_option($this->meta_key, time());
        }

        die('Thank you!');
    }

}
