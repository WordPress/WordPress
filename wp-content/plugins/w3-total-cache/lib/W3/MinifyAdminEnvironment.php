<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_INC_DIR . '/functions/rule.php');

/**
 * Class W3_MinifyAdminEnvironment
 */
class W3_MinifyAdminEnvironment {
    /*
     * Fixes environment in each wp-admin request
     * @param W3_Config $config
     * @param bool $force_all_checks
     *
     * @throws SelfTestExceptions
     **/
    public function fix_on_wpadmin_request($config, $force_all_checks) {
        $exs = new SelfTestExceptions();

        $this->fix_folders($config, $exs);
        /**
         * @var W3_Config $config
         */
        if ($config->get_boolean('config.check') || $force_all_checks) {
            if ($config->get_boolean('minify.enabled') && 
                    $config->get_boolean('minify.rewrite')) {
                $this->rules_core_add($config, $exs);
            } else {
                $this->rules_core_remove($exs);
            }

            if ((w3_is_apache() || w3_is_litespeed()) &&
                    w3_is_network() && !w3_is_subdomain_install()) {
                if ($config->get_boolean('minify.enabled') && 
                        $config->get_boolean('minify.rewrite')) {
                    $this->rules_wpmu_subfolder_add($config, $exs);
                } else {
                    $this->rules_wpmu_subfolder_remove($exs);
                }
            }

            if ($config->get_boolean('minify.enabled') && 
                    $config->get_string('minify.engine') == 'file') {
                $this->rules_cache_add($config, $exs);
            } else {
                $this->rules_cache_remove($exs);
            }
        }

        // if no errors so far - check if rewrite actually works
        if (count($exs->exceptions()) <= 0 || true) {
            try {
                if ($config->get_boolean('minify.enabled') && 
                        $config->get_string('minify.engine') == 'file' &&
                        $config->get_boolean('minify.debug'))
                    $this->verify_rewrite_working();
            } catch (Exception $ex) {
                $exs->push($ex);
            }

            if ($config->get_boolean('minify.enabled'))
                $this->verify_engine_working($config, $exs);
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment once event occurs
     *
     * @param W3_Config $config
     * @param string $event
     * @param null|W3_Config $old_config
     * @throws SelfTestExceptions
     */
    public function fix_on_event($config, $event, $old_config = null) {
        // Schedules events
        if ($config->get_boolean('minify.enabled') && 
                $config->get_string('minify.engine') == 'file') {
            if ($old_config != null && 
                    $config->get_integer('minify.file.gc') != 
                    $old_config->get_integer('minify.file.gc')) {
                $this->unschedule();
            }

            if (!wp_next_scheduled('w3_minify_cleanup')) {
                wp_schedule_event(time(), 
                    'w3_minify_cleanup', 'w3_minify_cleanup');
            }
        } else {
            $this->unschedule();
        }
    }

    /**
     * Fixes environment after plugin deactivation
     * @throws SelfTestExceptions
     */
    public function fix_after_deactivation() {
        $exs = new SelfTestExceptions();

        $this->rules_core_remove($exs);
        $this->rules_wpmu_subfolder_remove($exs);
        $this->rules_cache_remove($exs);

        $this->unschedule();

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     *
     * @param W3_Config $config
     * @return array
     */
    function get_required_rules($config) {
        if (!$config->get_boolean('minify.enabled'))
            return null;
        
        $rewrite_rules = array();
        if ($config->get_string('minify.engine') == 'file') {
            $minify_rules_cache_path = w3_get_minify_rules_cache_path();
            $rewrite_rules[] = array(
                'filename' => $minify_rules_cache_path, 
                'content'  => $this->rules_cache_generate($config)
            );
        }
        $minify_rules_core_path = w3_get_minify_rules_core_path();
        $rewrite_rules[] = array(
            'filename' => $minify_rules_core_path, 
            'content'  => $this->rules_core_generate($config),
            'last' => true
        );

        return $rewrite_rules;
    }



    /**
     * Fixes folders
     * @param W3_Config $config
     * @param SelfTestExceptions $exs
     **/
    private function fix_folders($config, $exs) {
        // folder that we delete if exists and not writeable
        if ($config->get_boolean('minify.enabled') && 
            $config->get_string('minify.engine') == 'file') {
            $dir = W3TC_CACHE_MINIFY_DIR;
        
            try{
                if (file_exists($dir) && !is_writeable($dir))
                    w3_wp_delete_folder($dir, '', $_SERVER['REQUEST_URI']);
            } catch (FilesystemRmdirException $ex) {
                $exs->push($ex);
            }
        }
    }

    /**
     * Minifiers availability error handling
     *
     * @param W3_Config $config
     * @param SelfTestExceptions $exs
     */
    private function verify_engine_working($config, $exs) {
        $minifiers_errors = array();

        if ($config->get_string('minify.js.engine') == 'yuijs') {
            $path_java = $config->get_string('minify.yuijs.path.java');
            $path_jar = $config->get_string('minify.yuijs.path.jar');

            if (!file_exists($path_java)) {
                $minifiers_errors[] = sprintf('YUI Compressor (JS): JAVA executable path was not found. The default minifier JSMin will be used instead.');
            } elseif (!file_exists($path_jar)) {
                $minifiers_errors[] = sprintf('YUI Compressor (JS): JAR file path was not found. The default minifier JSMin will be used instead.');
            }
        }

        if ($config->get_string('minify.css.engine') == 'yuicss') {
            $path_java = $config->get_string('minify.yuicss.path.java');
            $path_jar = $config->get_string('minify.yuicss.path.jar');

            if (!file_exists($path_java)) {
                $minifiers_errors[] = sprintf('YUI Compressor (CSS): JAVA executable path was not found. The default CSS minifier will be used instead.');
            } elseif (!file_exists($path_jar)) {
                $minifiers_errors[] = sprintf('YUI Compressor (CSS): JAR file path was not found. The default CSS minifier will be used instead.');
            }
        }

        if ($config->get_string('minify.js.engine') == 'ccjs') {
            $path_java = $config->get_string('minify.ccjs.path.java');
            $path_jar = $config->get_string('minify.ccjs.path.jar');

            if (!file_exists($path_java)) {
                $minifiers_errors[] = sprintf('Closure Compiler: JAVA executable path was not found. The default minifier JSMin will be used instead.');
            } elseif (!file_exists($path_jar)) {
                $minifiers_errors[] = sprintf('Closure Compiler: JAR file path was not found. The default minifier JSMin will be used instead.');
            }
        }

        if (count($minifiers_errors)) {
            $minify_error = 'The following minifiers cannot be found or are no longer working:</p><ul>';

            foreach ($minifiers_errors as $minifiers_error) {
                $minify_error .= '<li>' . $minifiers_error . '</li>';
            }

            $minify_error .= '</ul><p>This message will automatically disappear once the issue is resolved.';

            $exs->push(new SelfTestFailedException($minify_error));
        }
    }

    /*
     * Checks rewrite
     * @throws SelfTestExceptions
     **/
    private function verify_rewrite_working() {
        $url = w3_filename_to_url(w3_cache_blog_dir('minify') . '/w3tc_rewrite_test');

        if (!$this->test_rewrite($url)) {
            $key = sprintf('w3tc_rewrite_test_result_%s', substr(md5($url), 0, 16));
            $result = get_transient($key);

            $home_url = w3_get_home_url();

            $tech_message = 
                (w3_is_nginx() ? 'nginx configuration file' : '.htaccess file') .
                ' contains rules to rewrite url ' . 
                $url . '. If handled by ' .
                'plugin, it returns "OK" message.<br/>';
            $tech_message .= 'The plugin made a request to ' . 
                $url . ' but received: <br />' . 
                $result . '<br />';
            $tech_message .= 'instead of "OK" response. <br />';

            $error = '<strong>W3 Total Cache error:</strong>It appears Minify ' . 
                '<acronym title="Uniform Resource Locator">URL</acronym> ' .
                'rewriting is not working. ';

            if (w3_is_nginx()) {
                $error .= 'Please verify that all configuration files are ' .
                'included in the configuration file ' .
                '(and that you have reloaded / restarted nginx).';
            } else {
                $error .= 'Please verify that the server configuration ' .
                'allows .htaccess'; 
            }

            $error .= '<br />Unfortunately minification will ' .
                'not function without custom rewrite rules. ' .
                'Please ask your server administrator for assistance. ' .
                'Also refer to <a href="' . 
                admin_url('admin.php?page=w3tc_install') . 
                '">the install page</a>  for the rules for your server.';


            throw new SelfTestFailedException($error, $tech_message);
        }
    }

    /**
     * Perform rewrite test
     *
     * @param string $url
     * @return boolean
     */
    private function test_rewrite($url) {
        $key = sprintf('w3tc_rewrite_test_%s', substr(md5($url), 0, 16));
        $result = get_transient($key);

        if ($result === false) {
            w3_require_once(W3TC_INC_FUNCTIONS_DIR . '/http.php');

            $response = w3_http_get($url);

            $result = (!is_wp_error($response) && $response['response']['code'] == 200 && trim($response['body']) == 'OK');

            if ($result) {
                set_transient($key, $result, 30);
            } else {
                $key_result = sprintf('w3tc_rewrite_test_result_%s', substr(md5($url), 0, 16));
                set_transient($key_result, is_wp_error($response)? $response->get_error_message(): implode(' ', $response['response']), 30);
            }
        }

        return $result;
    }



    /**
     * scheduling stuff
     **/
    private function unschedule() {
        if (wp_next_scheduled('w3_minify_cleanup')) {
            wp_clear_scheduled_hook('w3_minify_cleanup');
        }
    }



    /**
     * rules core modification
     **/

    /**
     * Writes directives to WP .htaccess
     *
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_core_add($config, $exs) {

        w3_add_rules($exs, w3_get_minify_rules_core_path(),
            $this->rules_core_generate($config),
            W3TC_MARKER_BEGIN_MINIFY_CORE,
            W3TC_MARKER_END_MINIFY_CORE,
            array(
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
                W3TC_MARKER_END_BROWSERCACHE_CACHE => strlen(W3TC_MARKER_END_BROWSERCACHE_CACHE) + 1,
                W3TC_MARKER_END_PGCACHE_CACHE => strlen(W3TC_MARKER_END_PGCACHE_CACHE) + 1,
                W3TC_MARKER_END_MINIFY_CACHE => strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1
            )
        );
    }

    /**
     * Removes Page Cache core directives
     *
     * @param SelfTestExceptions $exs
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_core_remove($exs) {
        // no need to remove rules for apache - its in cache .htaccess file
        if (!w3_is_nginx())
            return;

        w3_remove_rules($exs,
            w3_get_minify_rules_core_path(),
            W3TC_MARKER_BEGIN_MINIFY_CORE ,
            W3TC_MARKER_END_MINIFY_CORE);
    }

    /**
     * Generates rules for WP dir
     *
     * @param W3_Config $config
     * @return string
     */
    public function rules_core_generate($config) {
        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                return $this->rules_core_generate_apache($config);

            case w3_is_nginx():
                return $this->rules_core_generate_nginx($config);
        }

        return '';
    }

    /**
     * Generates rules
     *
     * @param W3_Config $config
     * @return string
     */
    function rules_core_generate_apache($config) {
        $cache_dir = w3_filename_to_uri(W3TC_CACHE_MINIFY_DIR);
        $minify_filename = w3_make_relative_path(W3TC_DIR . '/pub/minify.php',
            W3TC_CACHE_MINIFY_DIR);

        $engine = $config->get_string('minify.engine');
        $browsercache = $config->get_boolean('browsercache.enabled');
        $compression = ($browsercache && $config->get_boolean('browsercache.cssjs.compression'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CORE . "\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "    RewriteEngine On\n";
        $rules .= "    RewriteBase " . $cache_dir . "/\n";
        $rules .= "    RewriteRule /w3tc_rewrite_test$ $minify_filename?w3tc_rewrite_test=1 [L]\n";

        if ($engine == 'file') {
            if ($compression) {
                $rules .= "    RewriteCond %{HTTP:Accept-Encoding} gzip\n";
                $rules .= "    RewriteRule .* - [E=APPEND_EXT:.gzip]\n";
            }

            $rules .= "    RewriteCond %{REQUEST_FILENAME}%{ENV:APPEND_EXT} -" . ($config->get_boolean('minify.file.nfs') ? 'F' : 'f') . "\n";
            $rules .= "    RewriteRule (.*) $1%{ENV:APPEND_EXT} [L]\n";
        }
        $rules .= "    RewriteRule ^(.+/[X]+\\.css)$ $minify_filename?test_file=$1 [L]\n";
        $rules .= "    RewriteRule ^(.+\\.(css|js))$ $minify_filename?file=$1 [L]\n";

        $rules .= "</IfModule>\n";
        $rules .= W3TC_MARKER_END_MINIFY_CORE . "\n";

        return $rules;
    }

    /**
     * Generates rules
     *
     * @param W3_Config $config
     * @return string
     */
    function rules_core_generate_nginx($config) {
        $cache_dir = w3_filename_to_uri(W3TC_CACHE_MINIFY_DIR);
        $minify_filename = w3_filename_to_uri(W3TC_DIR . '/pub/minify.php');

        $engine = $config->get_string('minify.engine');
        $browsercache = $config->get_boolean('browsercache.enabled');
        $compression = ($browsercache && $config->get_boolean('browsercache.cssjs.compression'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CORE . "\n";
        $rules .= "rewrite ^$cache_dir.*/w3tc_rewrite_test$ $minify_filename?w3tc_rewrite_test=1 last;\n";

        if ($engine == 'file') {
            $rules .= "set \$w3tc_enc \"\";\n";

            if ($compression) {
                $rules .= "if (\$http_accept_encoding ~ gzip) {\n";
                $rules .= "    set \$w3tc_enc .gzip;\n";
                $rules .= "}\n";
            }

            $rules .= "if (-f \$request_filename\$w3tc_enc) {\n";
            $rules .= "    rewrite (.*) $1\$w3tc_enc break;\n";
            $rules .= "}\n";
        }
        $rules .= "rewrite ^$cache_dir/(.+/[X]+\\.css)$ $minify_filename?test_file=$1 last;\n";
        $rules .= "rewrite ^$cache_dir/(.+\\.(css|js))$ $minify_filename?file=$1 last;\n";
        $rules .= W3TC_MARKER_END_MINIFY_CORE . "\n";

        return $rules;
    }



    /**
     * cache rules
     **/

    /**
     * Writes directives to file cache .htaccess
     * Throws exception on error
     *
     * @param W3_Config $config
     * @param SelfTestExceptions $exs
     */
    private function rules_cache_add($config, $exs) {
        w3_add_rules($exs,
            w3_get_minify_rules_cache_path(),
            $this->rules_cache_generate($config),
            W3TC_MARKER_BEGIN_MINIFY_CACHE,
            W3TC_MARKER_END_MINIFY_CACHE,
            array(
                W3TC_MARKER_BEGIN_PGCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_MINIFY_CORE => 0,
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0
            )
        );
    }

    /**
     * Removes Page Cache core directives
     *
     * @param SelfTestExceptions $exs
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_cache_remove($exs) {
        // apache's cache files are not used when core rules disabled
        if (!w3_is_nginx())
            return;

        w3_remove_rules($exs,
                w3_get_minify_rules_cache_path(),
                W3TC_MARKER_BEGIN_MINIFY_CACHE,
                W3TC_MARKER_END_MINIFY_CACHE);

    }

    /**
     * Generates directives for file cache dir
     *
     * @param W3_Config $config
     * @return string
     */
    private function rules_cache_generate($config) {
        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                return $this->rules_cache_generate_apache($config);

            case w3_is_nginx():
                return $this->rules_cache_generate_nginx($config);
        }

        return '';
    }


    /**
     * Generates directives for file cache dir
     *
     * @param W3_Config $config
     * @return string
     */
    private function rules_cache_generate_apache($config) {
        $browsercache = $config->get_boolean('browsercache.enabled');
        $compression = ($browsercache && $config->get_boolean('browsercache.cssjs.compression'));
        $expires = ($browsercache && $config->get_boolean('browsercache.cssjs.expires'));
        $lifetime = ($browsercache ? $config->get_integer('browsercache.cssjs.lifetime') : 0);
        $cache_control = ($browsercache && $config->get_boolean('browsercache.cssjs.cache.control'));
        $etag = ($browsercache && $config->get_integer('browsercache.html.etag'));
        $w3tc = ($browsercache && $config->get_integer('browsercache.cssjs.w3tc'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CACHE . "\n";
        $rules .= "Options -MultiViews\n";

        if ($etag) {
            $rules .= "FileETag MTime Size\n";
        }

        if ($compression) {
            $rules .= "<IfModule mod_mime.c>\n";
            $rules .= "    AddEncoding gzip .gzip\n";
            $rules .= "    <Files *.css.gzip>\n";
            $rules .= "        ForceType text/css\n";
            $rules .= "    </Files>\n";
            $rules .= "    <Files *.js.gzip>\n";
            $rules .= "        ForceType application/x-javascript\n";
            $rules .= "    </Files>\n";
            $rules .= "</IfModule>\n";
            $rules .= "<IfModule mod_deflate.c>\n";
            $rules .= "    <IfModule mod_setenvif.c>\n";
            $rules .= "        SetEnvIfNoCase Request_URI \\.gzip$ no-gzip\n";
            $rules .= "    </IfModule>\n";
            $rules .= "</IfModule>\n";
        }

        if ($expires) {
            $rules .= "<IfModule mod_expires.c>\n";
            $rules .= "    ExpiresActive On\n";
            $rules .= "    ExpiresByType text/css M" . $lifetime . "\n";
            $rules .= "    ExpiresByType application/x-javascript M" . $lifetime . "\n";
            $rules .= "</IfModule>\n";
        }

        if ($w3tc || $compression || $cache_control) {
            $rules .= "<IfModule mod_headers.c>\n";

            if ($w3tc) {
                $rules .= "    Header set X-Powered-By \"" . W3TC_POWERED_BY . "\"\n";
            }

            if ($compression) {
                $rules .= "    Header set Vary \"Accept-Encoding\"\n";
            }

            if ($cache_control) {
                $cache_policy = $config->get_string('browsercache.cssjs.cache.policy');

                switch ($cache_policy) {
                    case 'cache':
                        $rules .= "    Header set Pragma \"public\"\n";
                        $rules .= "    Header set Cache-Control \"public\"\n";
                        break;

                    case 'cache_public_maxage':
                        $rules .= "    Header set Pragma \"public\"\n";

                        if ($expires) {
                            $rules .= "    Header append Cache-Control \"public\"\n";
                        } else {
                            $rules .= "    Header set Cache-Control \"max-age=" . $lifetime . ", public\"\n";
                        }
                        break;

                    case 'cache_validation':
                        $rules .= "    Header set Pragma \"public\"\n";
                        $rules .= "    Header set Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                        break;

                    case 'cache_noproxy':
                        $rules .= "    Header set Pragma \"public\"\n";
                        $rules .= "    Header set Cache-Control \"private, must-revalidate\"\n";
                        break;

                    case 'cache_maxage':
                        $rules .= "    Header set Pragma \"public\"\n";

                        if ($expires) {
                            $rules .= "    Header append Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                        } else {
                            $rules .= "    Header set Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\"\n";
                        }
                        break;

                    case 'no_cache':
                        $rules .= "    Header set Pragma \"no-cache\"\n";
                        $rules .= "    Header set Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\"\n";
                        break;
                }
            }

            $rules .= "</IfModule>\n";
        }

        $rules .= W3TC_MARKER_END_MINIFY_CACHE . "\n";

        return $rules;
    }

    /**
     * Generates directives for file cache dir
     *
     * @param W3_Config $config
     * @return string
     */
    private function rules_cache_generate_nginx($config) {
        $cache_dir = w3_filename_to_uri(W3TC_CACHE_MINIFY_DIR);

        $browsercache = $config->get_boolean('browsercache.enabled');
        $compression = ($browsercache && $config->get_boolean('browsercache.cssjs.compression'));
        $expires = ($browsercache && $config->get_boolean('browsercache.cssjs.expires'));
        $lifetime = ($browsercache ? $config->get_integer('browsercache.cssjs.lifetime') : 0);
        $cache_control = ($browsercache && $config->get_boolean('browsercache.cssjs.cache.control'));
        $w3tc = ($browsercache && $config->get_integer('browsercache.cssjs.w3tc'));

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_MINIFY_CACHE . "\n";

        $common_rules = '';

        if ($expires) {
            $common_rules .= "    expires modified " . $lifetime . "s;\n";
        }

        if ($w3tc) {
            $common_rules .= "    add_header X-Powered-By \"" . W3TC_POWERED_BY . "\";\n";
        }

        if ($compression) {
            $common_rules .= "    add_header Vary \"Accept-Encoding\";\n";
        }

        if ($cache_control) {
            $cache_policy = $config->get_string('browsercache.cssjs.cache.policy');

            switch ($cache_policy) {
                case 'cache':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"public\";\n";
                    break;

                case 'cache_public_maxage':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"max-age=" . $lifetime . ", public\";\n";
                    break;

                case 'cache_validation':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"public, must-revalidate, proxy-revalidate\";\n";
                    break;

                case 'cache_noproxy':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"private, must-revalidate\";\n";
                    break;

                case 'cache_maxage':
                    $common_rules .= "    add_header Pragma \"public\";\n";
                    $common_rules .= "    add_header Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\";\n";
                    break;

                case 'no_cache':
                    $common_rules .= "    add_header Pragma \"no-cache\";\n";
                    $common_rules .= "    add_header Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\";\n";
                    break;
            }
        }

        $rules .= "location ~ " . $cache_dir . ".*\\.js$ {\n";
        $rules .= "    types {}\n";
        $rules .= "    default_type application/x-javascript;\n";
        $rules .= $common_rules;
        $rules .= "}\n";

        $rules .= "location ~ " . $cache_dir . ".*\\.css$ {\n";
        $rules .= "    types {}\n";
        $rules .= "    default_type text/css;\n";
        $rules .= $common_rules;
        $rules .= "}\n";

        if ($compression) {
            $rules .= "location ~ " . $cache_dir . ".*js\\.gzip$ {\n";
            $rules .= "    gzip off;\n";
            $rules .= "    types {}\n";
            $rules .= "    default_type application/x-javascript;\n";
            $rules .= $common_rules;
            $rules .= "    add_header Content-Encoding gzip;\n";
            $rules .= "}\n";

            $rules .= "location ~ " . $cache_dir . ".*css\\.gzip$ {\n";
            $rules .= "    gzip off;\n";
            $rules .= "    types {}\n";
            $rules .= "    default_type text/css;\n";
            $rules .= $common_rules;
            $rules .= "    add_header Content-Encoding gzip;\n";
            $rules .= "}\n";
        }

        $rules .= W3TC_MARKER_END_MINIFY_CACHE . "\n";

        return $rules;
    }



    /**
     * rules wpmu subfolder
     **/

     /**
     * Writes directives to WP .htaccess
     *
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_wpmu_subfolder_add($config, $exs) {
        w3_add_rules($exs,
            w3_get_browsercache_rules_cache_path(),
            $this->rules_wpmu_subfolder_generate($config),
            W3TC_MARKER_BEGIN_MINIFY_CACHE,
            W3TC_MARKER_END_MINIFY_CACHE,
            array(
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
                W3TC_MARKER_END_BROWSERCACHE_CACHE => strlen(W3TC_MARKER_END_BROWSERCACHE_CACHE) + 1,
                W3TC_MARKER_END_PGCACHE_CACHE => strlen(W3TC_MARKER_END_PGCACHE_CACHE) + 1,
                W3TC_MARKER_END_MINIFY_CACHE => strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1
            )
        );
    }

    /**
     * Removes Page Cache core directives
     *
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_wpmu_subfolder_remove($exs) {
        w3_remove_rules($exs, w3_get_browsercache_rules_cache_path(),
            W3TC_MARKER_BEGIN_MINIFY_CACHE,
            W3TC_MARKER_END_MINIFY_CACHE);
    }

    /**
     * Generates rules for WP dir
     *
     * @param W3_Config $config
     * @return string
     */
    private function rules_wpmu_subfolder_generate($config) {
        $cache_dir = w3_filename_to_uri(W3TC_CACHE_MINIFY_DIR);
        $minify_filename = w3_filename_to_uri(W3TC_DIR . '/pub/minify.php');

        $rule  = W3TC_MARKER_BEGIN_MINIFY_CACHE . "\n";
        $rule .= "<IfModule mod_rewrite.c> \n";
        $rule .= "    RewriteEngine On\n";
        $rule .= "    RewriteBase /\n";
        $rule .= "    RewriteRule ^[_0-9a-zA-Z-]+$cache_dir/[0-9]+/w3tc_rewrite_test$ $minify_filename?w3tc_rewrite_test=1 [L]\n";
        $rule .= "</IfModule>\n";
        $rule .= W3TC_MARKER_END_MINIFY_CACHE . "\n";

        return $rule;
    }
}