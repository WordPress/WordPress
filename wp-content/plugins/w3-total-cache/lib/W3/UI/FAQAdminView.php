<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/UI/PluginView.php');

class W3_UI_FAQAdminView extends W3_UI_PluginView {
    /**
     * Current page
     *
     * @var string
     */
    var $_page = 'w3tc_faq';

    /**
     * FAQ tab
     *
     * @return void
     */
    function view() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/other.php');
        $faq = w3_parse_faq();

        include W3TC_INC_DIR . '/options/faq.php';
    }
}
