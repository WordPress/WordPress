<?php
    /**
     * W3 Forum Widget
     */
    if (!defined('W3TC')) {
        die();
    }

    w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');
    w3_require_once(W3TC_INC_DIR . '/functions/widgets.php');

    /**
     * Class W3_Widget_Forum
     */
class W3_Widget_SpreadTheWord extends W3_Plugin {

    function run() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        if(w3tc_get_current_wp_page() == 'w3tc_dashboard')
            add_action('admin_enqueue_scripts', array($this,'enqueue'));

        add_action('w3tc_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));
        add_action('w3tc_network_dashboard_setup', array(
            &$this,
            'wp_dashboard_setup'
        ));

        if (is_admin()) {
            add_action('wp_ajax_w3tc_link_support', array($this, 'action_widget_link_support'));
        }
    }

    /**
     * Dashboard setup action
     *
     * @return void
     */
    function wp_dashboard_setup() {
        w3tc_add_dashboard_widget('w3tc_spreadtheword', __('Spread The Word', 'w3-total-cache'), array(
            &$this,
            'widget_form'
        ),null, 'normal',
        'div'
        );
    }

    function widget_form() {
        $support = $this->_config->get_string('common.support');
        $supports = $this->get_supports();

        include W3TC_INC_WIDGET_DIR . '/spreadtheword.php';
    }

    /**
     * Returns list of support types
     *
     * @return array
     */
    function get_supports() {
        $supports = array(
            'footer' => 'page footer'
        );

        $link_categories = get_terms('link_category', array(
            'hide_empty' => 0
        ));

        foreach ($link_categories as $link_category) {
            $supports['link_category_' . $link_category->term_id] = strtolower($link_category->name);
        }

        return $supports;
    }

    function action_widget_link_support() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        $value = W3_Request::get_string('w3tc_common_support_us');
        $this->_config->set('common.support', $value);
        $this->_config->save();
        if ($value) {
            _e('Thank you for linking to us!', 'w3-total-cache');
        } else {
            _e('You are no longer linking to us. Please support us in other ways instead.', 'w3-total-cache');
        }
        die();
    }

    public function enqueue() {
        wp_enqueue_style('w3tc-widget');
        wp_enqueue_script('w3tc-metadata');
        wp_enqueue_script('w3tc-widget');
    }
}
