<?php
if (!defined('W3TC')) {
    die();
}
w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');

class W3_AdminActions_TestActionsAdmin {

    /**
     * @var W3_Config $_config
     */
    private $_config = null;

    /**
     * Current page
     * @var null|string
     */
    private $_page = null;

    function __construct() {
        $this->_config = w3_instance('W3_Config');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin.php');
        $this->_page = w3tc_get_current_page();
    }

    /**
     * Evaluation mode
     */
    function action_test_use_edge_mode() {
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/admin_ui.php');
        w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/ui.php');
        $page = $this->_page;
        include W3TC_INC_LIGHTBOX_DIR . '/edge.php';
    }

    /**
     * Test memcached
     *
     * @return void
     */
    function action_test_memcached() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $servers = W3_Request::get_array('servers');

        if ($this->is_memcache_available($servers)) {
            $result = true;
            $error = __('Test passed.', 'w3-total-cache');
        } else {
            $result = false;
            $error = __('Test failed.', 'w3-total-cache');
        }

        $response = array(
            'result' => $result,
            'error' => $error
        );

        echo json_encode($response);
    }

    /**
     * Test minifier action
     *
     * @return void
     */
    function action_test_minifier() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

        $engine = W3_Request::get_string('engine');
        $path_java = W3_Request::get_string('path_java');
        $path_jar = W3_Request::get_string('path_jar');

        $result = false;
        $error = '';

        if (!$path_java) {
            $error = __('Empty JAVA executable path.', 'w3-total-cache');
        } elseif (!$path_jar) {
            $error = __('Empty JAR file path.', 'w3-total-cache');
        } else {
            switch ($engine) {
                case 'yuijs':
                    w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/YUICompressor.php');

                    Minify_YUICompressor::setPathJava($path_java);
                    Minify_YUICompressor::setPathJar($path_jar);

                    $result = Minify_YUICompressor::testJs($error);
                    break;

                case 'yuicss':
                    w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/YUICompressor.php');

                    Minify_YUICompressor::setPathJava($path_java);
                    Minify_YUICompressor::setPathJar($path_jar);

                    $result = Minify_YUICompressor::testCss($error);
                    break;

                case 'ccjs':
                    w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/ClosureCompiler.php');

                    Minify_ClosureCompiler::setPathJava($path_java);
                    Minify_ClosureCompiler::setPathJar($path_jar);

                    $result = Minify_ClosureCompiler::test($error);
                    break;

                default:
                    $error = __('Invalid engine.', 'w3-total-cache');
                    break;
            }
        }

        $response = array(
            'result' => $result,
            'error' => $error
        );

        echo json_encode($response);
    }


    /**
     * Check if memcache is available
     *
     * @param array $servers
     * @return boolean
     */
    function is_memcache_available($servers) {
        static $results = array();

        $key = md5(implode('', $servers));

        if (!isset($results[$key])) {
            w3_require_once(W3TC_LIB_W3_DIR . '/Cache/Memcached.php');

            @$memcached = new W3_Cache_Memcached(array(
                'servers' => $servers,
                'persistant' => false
            ));

            $test_string = sprintf('test_' . md5(time()));
            $test_value = array('content' => $test_string);
            $memcached->set($test_string, $test_value, 60);
            $test_value = $memcached->get($test_string);
            $results[$key] = ( $test_value['content'] == $test_string);
        }

        return $results[$key];
    }

    /**
     * Self test action
     */
    function action_test_self() {
        include W3TC_INC_LIGHTBOX_DIR . '/self_test.php';
    }

    /**
     * Minify recommendations action
     *
     * @return void
     */
    function action_test_minify_recommendations() {
        $options_minify = w3_instance('W3_UI_MinifyAdminView');
        $options_minify->recommendations();
    }


    /**
     * Page Speed results action
     *
     * @return void
     */
    function action_test_pagespeed_results() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
        w3_require_once(W3TC_LIB_W3_DIR . '/PageSpeed.php');

        $force = W3_Request::get_boolean('force');
        $title = 'Google Page Speed';

        $w3_pagespeed = new W3_PageSpeed();
        $results = $w3_pagespeed->analyze(w3_get_home_url(), $force);

        if ($force) {
            w3_admin_redirect(array(
                'w3tc_pagespeed_results' => 1,
                '_wpnonce' => wp_create_nonce('w3tc')
            ));
        }

        include W3TC_INC_POPUP_DIR . '/pagespeed_results.php';
    }
}