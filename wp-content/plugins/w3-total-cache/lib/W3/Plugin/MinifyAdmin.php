<?php

/**
 * W3 Minify plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_MinifyAdmin
 */
class W3_Plugin_MinifyAdmin extends W3_Plugin {

    function run() {

        if (!$this->_config->get_boolean('minify.auto.disable_filename_length_test', false)) {
            if (!is_network_admin() && $this->_config->get_boolean('minify.auto') && false === get_transient('w3tc_minify_tested_filename_length')) {
                add_action('wp_ajax_w3tc_minify_disable_filename_test', array($this, 'disable_filename_test'));
                add_action('wp_ajax_w3tc_minify_change_filename_length', array($this, 'change_filename_length'));
                w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');

                $page = W3_Request::get_string('page');
                if (strpos($page, 'w3tc_') === 0) {
                    add_action( 'admin_print_scripts', array($this, 'print_script'),10000);
                    add_action( 'admin_print_scripts', array($this, 'print_test_once_script'),10000);

                    add_action('admin_notices', array(
                        &$this,
                        'admin_notices_minify_auto_test'
                    ));
                }
            }
        }
    }
    
    /**
     * Does disk cache cleanup
     *
     * @return void
     */
    function cleanup() {
        w3_require_once(W3TC_LIB_W3_DIR . '/Cache/File/Cleaner/Generic.php');

        $w3_cache_file_cleaner_generic = new W3_Cache_File_Cleaner_Generic(array(
            'exclude' => array(
                '*.files',
                '.htaccess',
                'index.php'
            ),
            'cache_dir' => w3_cache_blog_dir('minify'),
            'expire' => $this->_config->get_integer('minify.file.gc'),
            'clean_timelimit' => $this->_config->get_integer('timelimit.cache_gc')
        ));

        $w3_cache_file_cleaner_generic->clean();
    }

    function change_filename_length() {
        try {
            w3_require_once(W3TC_LIB_W3_DIR . '/Request.php');
            $new = W3_Request::get_integer('maxlength');
            $this->_config->set('minify.auto.filename_length',$new);
            set_transient('w3tc_minify_tested_filename_length',true, 3600*24);
            $this->_config->save();
            w3_require_once(W3TC_LIB_MINIFY_DIR . '/Minify/Cache/File.php');

            $cache = new Minify_Cache_File(
                w3_cache_blog_dir('minify'),
                array(
                    '.htaccess',
                    'index.php',
                    '*.old'
                ),
                $this->_config->get_boolean('minify.file.locking'),
                $this->_config->get_integer('timelimit.cache_flush'),
                (w3_get_blog_id() == 0 ? W3TC_CACHE_MINIFY_DIR : null)
            );
            $cache->flush();
            echo 1;
        } catch(Exception $ex) {
            echo $ex->getMessage();
        }
        exit;
    }

    function print_test_once_script() { ?>
    <script type="text/javascript">/*<![CDATA[*/
        jQuery(function() {
            var filename = new Array(<?php echo $this->_config->get_integer('minify.auto.filename_length', 246) ?>+1).join('X');
            var url = '<?php echo w3_filename_to_url(w3_cache_blog_dir('minify').'/', w3_get_domain(w3_get_home_url()) != w3_get_domain(w3_get_site_url())) ?>';

            w3tc_minify_filename_test_once(url, filename);
        });
    /*]]>*/</script>
    <?php
    }

    function print_script() { ?>
        <script type="text/javascript">
            var w3_use_network_link = <?php echo is_network_admin() || (w3_is_multisite() && w3_force_master()) ? 'true' : 'false' ?>;
            function w3tc_start_minify_try_solve() {
                var testUrl = '<?php echo w3_filename_to_url(w3_cache_blog_dir('minify').'/', w3_get_domain(w3_get_home_url()) != w3_get_domain(w3_get_site_url())) ?>';
                w3tc_filename_auto_solve(testUrl);
            }
        </script>
        <?php
    }

    function admin_notices_minify_auto_test() {
        $error = sprintf(__('Minify Auto encountered an error. The filename length value is most likely too high for
                            your host. It is currently %d. The plugin is trying to solve the issue for you:
                            <span id="minify_auto_test_loading">(solving ...)</span>.', 'w3-total-cache')
            , $this->_config->get_integer('minify.auto.filename_length')
        );
        echo sprintf('<div id="minify_auto_error" class="error" style="display: none"><p>%s</p></div>', $error);
    }

    function disable_filename_test() {
        set_transient('w3tc_minify_tested_filename_length',true, 3600*24);
        echo 1;
        exit;
    }
}
