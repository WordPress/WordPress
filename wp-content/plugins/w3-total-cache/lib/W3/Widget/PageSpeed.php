<?php
if (!defined('W3TC')) die();

/**
 * Setups Google PageSpeed dashboard widget
 */
class W3_Widget_PageSpeed extends W3_Plugin{
    function run() {
        add_action('w3tc_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));
        add_action('w3tc_network_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));
    }


    /**
     * Dashboard setup action
     *
     * @return void
     */
    function wp_dashboard_setup() {
        w3tc_add_dashboard_widget('w3tc_pagespeed', __('Page Speed Report', 'w3-total-cache'), array(
            &$this,
            'widget_pagespeed'
        ), array(
            &$this,
            'widget_pagespeed_control'
        ),
        'normal',
        'div'
        );
    }

    /**
     * PageSpeed widget
     *
     * @return void
     */
    function widget_pagespeed() {
        w3_require_once(W3TC_LIB_W3_DIR . '/PageSpeed.php');
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $key = $this->_config->get_string('widget.pagespeed.key');
        $force = W3_Request::get_boolean('w3tc_widget_pagespeed_force');
        $results = null;

        if ($key) {
            $w3_pagespeed = new W3_PageSpeed();
            $results = $w3_pagespeed->analyze(w3_get_home_url(), $force);
        }

        include W3TC_INC_DIR . '/widget/pagespeed.php';
    }

    /**
     * Latest widget control
     *
     * @param integer $widget_id
     * @param array $form_inputs
     * @return void
     */
    function widget_pagespeed_control($widget_id, $form_inputs = array()) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

            $this->_config->set('widget.pagespeed.key', W3_Request::get_string('w3tc_widget_pagespeed_key'));
            $this->_config->save();
        }
        include W3TC_INC_DIR . '/widget/pagespeed_control.php';
    }
}
