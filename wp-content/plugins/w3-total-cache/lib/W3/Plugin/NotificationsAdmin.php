<?php

class W3_Plugin_NotificationsAdmin {

    /**
     * @var W3_Config
     */
    private $_config;

    /**
     * @var W3_ConfigAdmin
     */
    private $_config_admin;

    /**
     * @var string
     */
    private $_page;

    public function run() {
        $this->_config = w3_instance('W3_Config');
        $this->_config_admin = w3_instance('W3_ConfigAdmin');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');
        $this->_page = w3tc_get_current_page();

        if (is_network_admin() || !w3_is_multisite())
            $this->edge_notification();

        if (is_w3tc_admin_page()) {
            add_action('admin_head', array($this, 'admin_head'));
            add_action('w3tc_hide_button_custom-edge_mode', array($this, 'hide_edge_mode_notification'));
            $support_reminder = ($this->_config->get_boolean('notes.support_us') && $this->_config_admin->get_integer('common.install') < (time() - W3TC_SUPPORT_US_TIMEOUT) && $this->_config->get_string('common.support') == '' && !$this->_config->get_boolean('common.tweeted'));
            if($support_reminder || w3tc_show_notification('support_us_popup'))
                add_action('w3tc-dashboard-head', array($this, 'support_us_nag'));
            add_action('w3tc-dashboard-head', array($this, 'edge_nag'));
        }
    }

    /**
     * Print JS required by the support nag.
     */
    function admin_head() { ?>
        <?php
    }

    /**
     * Queue edge mode notification
     */
    public function edge_notification() {
       if ((!w3tc_edge_mode() &&
            $this->_config_admin->get_integer('evaluation.reminder', 0) == 0) || w3tc_show_notification('edge_mode_note')) {
            add_action('admin_notices', array($this, 'notify_edge_mode'));
       }
    }

    /**
     * Hide initial edge mode notification
     */
    public function hide_edge_mode_notification() {
        try{
            $this->_config_admin->set("evaluation.reminder", time() + (7*24*60*60));
            $this->_config_admin->save();
        } catch(Exception $ex) {}
    }

    /**
     * Edge Mode popup notification
     */
    public function edge_nag() {
        $edge_reminder = ($this->_config_admin->get_integer('evaluation.reminder') != 0 && $this->_config_admin->get_integer('evaluation.reminder') < time()) && !w3tc_edge_mode();
        if ($edge_reminder  || w3tc_show_notification('edge_mode_popup')) { ?>
            <script type="text/javascript">/*<![CDATA[*/
                jQuery(function() {
                    w3tc_lightbox_use_edge_mode('<?php echo wp_create_nonce('w3tc'); ?>');
                });
                /*]]>*/</script>
            <?php
            $delay = get_option('w3tc_edge_remainder_period');
            if ($delay <= 7 * 24 * 60 * 60)
                $delay = 30 * 24 * 60 * 60;
            else if ($delay <= 30 * 24 * 60 * 60)
                $delay = 60 * 24 * 60 * 60;
            else
                $delay = 90 * 24 * 60 * 60;
            update_option('w3tc_edge_remainder_period', $delay);

            try {
                $this->_config_admin->set('evaluation.reminder', time() + $delay);
                $this->_config_admin->save();
            }catch (Exception $ex) {

            }
        }
    }

    /**
     *  Display edge mode notification
     */
    public function notify_edge_mode() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');
        $message = sprintf(__('<p>You can now keep W3 Total Cache up-to-date without having to worry about new features breaking your website. There will be more releases with bug fixes, security fixes and settings updates. </p>
        <p>Also, you can now try out our new features as soon as they\'re ready. %s to enable "edge mode" and unlock pre-release features. %s</p>', 'w3-total-cache')
            ,'<a href="' . w3_admin_url(wp_nonce_url('admin.php', 'w3tc') . '&page='. $this->_page .'&w3tc_edge_mode_enable').'" class="button">' . __('Click Here', 'w3-total-cache') . '</a>'
            , w3_button_hide_note(__('Hide this message', 'w3-total-cache'), 'edge_mode', '', true,'','w3tc_default_hide_note_custom')
        );
        w3_e_notification_box($message, 'edge-mode');
    }

    /**
     * Display the support us nag
     */
    public function support_us_nag() { ?>
    <script type="text/javascript">/*<![CDATA[*/
        jQuery(function() {
            w3tc_lightbox_support_us('<?php echo wp_create_nonce('w3tc'); ?>');
        });
        /*]]>*/</script>
    <?php
    }
}
