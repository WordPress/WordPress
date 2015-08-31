<?php

/**
 * W3 PgCache plugin - administrative interface
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_INC_DIR . '/functions/rule.php');

/**
 * Class W3_BrowserCacheAdminEnvironment
 */
class W3_BrowserCacheAdminEnvironment {
    /**
     * Fixes environment in each wp-admin request
     *
     * @param W3_Config $config
     * @param bool $force_all_checks
     * @throws SelfTestExceptions
     **/
    public function fix_on_wpadmin_request($config, $force_all_checks) {
        $exs = new SelfTestExceptions();

        if ($config->get_boolean('config.check') || $force_all_checks) {
            if ($config->get_boolean('browsercache.enabled')) {
                $this->rules_cache_add($config, $exs);
            } else {
                $this->rules_cache_remove($exs);
            }

            if ($config->get_boolean('browsercache.enabled') &&
                    $config->get_boolean('browsercache.no404wp')) {
                $this->rules_no404wp_add($config, $exs);
            } else {
                $this->rules_no404wp_remove($exs);
            }
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment once event occurs
     * @throws SelfTestExceptions
     **/
    public function fix_on_event($config, $event, $old_config = null) {
    }

    /**
     * Fixes environment after plugin deactivation
     * @throws SelfTestExceptions
     */
    public function fix_after_deactivation() {
        $exs = new SelfTestExceptions();

        $this->rules_cache_remove($exs);
        $this->rules_no404wp_remove($exs);

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Returns required rules for module
     *
     * @param W3_Config $config
     * @return array
     */
    public function get_required_rules($config) {
        if (!$config->get_boolean('browsercache.enabled'))
            return null;

        $rewrite_rules = array();
        $dispatcher = w3_instance('W3_Dispatcher');
        if ($dispatcher->should_browsercache_generate_rules_for_cdn($config)) {
            $domain = $dispatcher->get_cdn_domain();
            $cdn_rules_path = sprintf('ftp://%s/%s', $domain, 
                w3_get_cdn_rules_path());
            $rewrite_rules[] = array(
                'filename' => $cdn_rules_path, 
                'content' => $this->rules_cache_generate($config, true)
            );
        }

        $browsercache_rules_cache_path = w3_get_browsercache_rules_cache_path();
        $rewrite_rules[] = array(
            'filename' => $browsercache_rules_cache_path, 
            'content' => $this->rules_cache_generate($config)
        );

        if ($config->get_boolean('browsercache.no404wp')) {
            $browsercache_rules_no404wp_path = 
                w3_get_browsercache_rules_no404wp_path();
            $rewrite_rules[] = array(
                'filename' => $browsercache_rules_no404wp_path, 
                'content' => $this->rules_no404wp_generate($config)
            );
        }
        return $rewrite_rules;
    }

    /**
     * Returns mime types
     *
     * @return array
     */
    public function get_mime_types() {
        return array(
            'cssjs' => include W3TC_INC_DIR . '/mime/cssjs.php',
            'html' => include W3TC_INC_DIR . '/mime/html.php',
            'other' => include W3TC_INC_DIR . '/mime/other.php'
        );
    }

    /**
     * Generate rules for FTP upload
     *
     * @param W3_Config $config
     * @return string
     **/
    public function rules_cache_generate_for_ftp($config) {
        return $this->rules_cache_generate($config, true);
    }



    /**
     * rules cache
     **/

    /**
     * Writes cache rules
     *
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_cache_add($config, $exs) {
        w3_add_rules($exs,
            w3_get_browsercache_rules_cache_path(),
            $this->rules_cache_generate($config),
            W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE,
            W3TC_MARKER_END_BROWSERCACHE_CACHE,
            array(
                W3TC_MARKER_BEGIN_MINIFY_CORE => 0,
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
                W3TC_MARKER_END_PGCACHE_CACHE => strlen(W3TC_MARKER_END_PGCACHE_CACHE) + 1,
                W3TC_MARKER_END_MINIFY_CACHE => strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1
            )
        );
    }

    /**
     * Removes cache directives
     *
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_cache_remove($exs) {
        w3_remove_rules($exs,
            w3_get_browsercache_rules_cache_path(),
            W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE,
            W3TC_MARKER_END_BROWSERCACHE_CACHE);
    }

    /**
     * Returns cache rules
     *
     * @param W3_Config $config
     * @param bool $cdnftp
     * @return string
     */
    public function rules_cache_generate($config, $cdnftp = false) {
        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                return $this->rules_cache_generate_apache($config);

            case w3_is_nginx():
                return $this->rules_cache_generate_nginx($config, $cdnftp);
        }
        return '';
    }

    /**
     * Returns cache rules
     *
     * @param W3_Config $config
     * @return string
     */
    private function rules_cache_generate_apache($config) {
        $mime_types2 = $this->get_mime_types();
        $cssjs_types = $mime_types2['cssjs'];
        $cssjs_types = array_unique($cssjs_types);
        $html_types = $mime_types2['html'];
        $other_types = $mime_types2['other'];

        $cssjs_expires = $config->get_boolean('browsercache.cssjs.expires');
        $html_expires = $config->get_boolean('browsercache.html.expires');
        $other_expires = $config->get_boolean('browsercache.other.expires');

        $cssjs_lifetime = $config->get_integer('browsercache.cssjs.lifetime');
        $html_lifetime = $config->get_integer('browsercache.html.lifetime');
        $other_lifetime = $config->get_integer('browsercache.other.lifetime');
        $compatibility = $config->get_boolean('pgcache.compatibility');

        $mime_types = array();

        if ($cssjs_expires && $cssjs_lifetime) {
            $mime_types = array_merge($mime_types, $cssjs_types);
        }

        if ($html_expires && $html_lifetime) {
            $mime_types = array_merge($mime_types, $html_types);
        }

        if ($other_expires && $other_lifetime) {
            $mime_types = array_merge($mime_types, $other_types);
        }

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE . "\n";

        if (count($mime_types)) {
            $rules .= "<IfModule mod_mime.c>\n";

            foreach ($mime_types as $ext => $mime_type) {
                $extensions = explode('|', $ext);

                if (!is_array($mime_type))
                    $mime_type = (array)$mime_type;
                foreach($mime_type as $mime_type2) {
                    $rules .= "    AddType " . $mime_type2;
                    foreach ($extensions as $extension) {
                        $rules .= " ." . $extension;
                    }
                    $rules .= "\n";
                }
            }

            $rules .= "</IfModule>\n";

            $rules .= "<IfModule mod_expires.c>\n";
            $rules .= "    ExpiresActive On\n";

            if ($cssjs_expires && $cssjs_lifetime) {
                foreach ($cssjs_types as $mime_type) {
                    $rules .= "    ExpiresByType " . $mime_type . " A" . $cssjs_lifetime . "\n";
                }
            }

            if ($html_expires && $html_lifetime) {
                foreach ($html_types as $mime_type) {
                    $rules .= "    ExpiresByType " . $mime_type . " A" . $html_lifetime . "\n";
                }
            }

            if ($other_expires && $other_lifetime) {
                foreach ($other_types as $mime_type) {
                    if (is_array($mime_type))
                        foreach ($mime_type as $mime_type2)
                            $rules .= "    ExpiresByType " . $mime_type2 . " A" . $other_lifetime . "\n";
                    else
                    $rules .= "    ExpiresByType " . $mime_type . " A" . $other_lifetime . "\n";
                }
            }

            $rules .= "</IfModule>\n";
        }

        $cssjs_compression = $config->get_boolean('browsercache.cssjs.compression');
        $html_compression = $config->get_boolean('browsercache.html.compression');
        $other_compression = $config->get_boolean('browsercache.other.compression');

        if ($cssjs_compression || $html_compression || $other_compression) {
            $compression_types = array();

            if ($cssjs_compression) {
                $compression_types = array_merge($compression_types, $cssjs_types);
            }

            if ($html_compression) {
                $compression_types = array_merge($compression_types, $html_types);
            }

            if ($other_compression) {
                $compression_types = array_merge($compression_types, array(
                    //for some reason the 'other' types are not loaded from the 'other' file
                    'ico' => 'image/x-icon',
                    'json' => 'application/json'
                ));
            }

            $rules .= "<IfModule mod_deflate.c>\n";
            if ($compatibility) {
                $rules .= "    <IfModule mod_setenvif.c>\n";
                $rules .= "        BrowserMatch ^Mozilla/4 gzip-only-text/html\n";
                $rules .= "        BrowserMatch ^Mozilla/4\\.0[678] no-gzip\n";
                $rules .= "        BrowserMatch \\bMSIE !no-gzip !gzip-only-text/html\n";
                $rules .= "        BrowserMatch \\bMSI[E] !no-gzip !gzip-only-text/html\n";
                $rules .= "    </IfModule>\n";
            }
            $rules .= "    <IfModule mod_headers.c>\n";
            $rules .= "        Header append Vary User-Agent env=!dont-vary\n";
            $rules .= "    </IfModule>\n";
            if (version_compare($this->_get_server_version(), '2.3.7', '>=')) {
                $rules .= "    <IfModule mod_filter.c>\n";
            }
            $rules .= "        AddOutputFilterByType DEFLATE " . implode(' ', $compression_types) . "\n";
            $rules .= "    <IfModule mod_mime.c>\n";
            $rules .= "        # DEFLATE by extension\n";
            $rules .= "        AddOutputFilter DEFLATE js css htm html xml\n";
            $rules .= "    </IfModule>\n";

            if (version_compare($this->_get_server_version(), '2.3.7', '>=')) {
                $rules .= "    </IfModule>\n";
            }
            $rules .= "</IfModule>\n";
        }

        foreach ($mime_types2 as $type => $extensions)
            $rules .= $this->_rules_cache_generate_apache_for_type($config, 
                $extensions, $type);

        $rules .= W3TC_MARKER_END_BROWSERCACHE_CACHE . "\n";

        return $rules;
    }

    /**
     * Writes cache rules
     *
     * @param W3_Config $config
     * @param array $mime_types
     * @param string $section
     * @return string
     */
    private function _rules_cache_generate_apache_for_type($config, $mime_types, 
            $section) {
        $is_disc_enhanced = $config->get_boolean('pgcache.enabled') &&
                            $config->get_string('pgcache.engine') == 'file_generic';
        $cache_control = $config->get_boolean('browsercache.' . $section . '.cache.control');
        $etag = $config->get_boolean('browsercache.' . $section . '.etag');
        $w3tc = $config->get_boolean('browsercache.' . $section . '.w3tc');
        $unset_setcookie = $config->get_boolean('browsercache.' . $section . '.nocookies');
        $set_last_modified = $config->get_boolean('browsercache.' . $section . '.last_modified');
        $compatibility = $config->get_boolean('pgcache.compatibility');

        $extensions = array_keys($mime_types);

        // Remove ext from filesmatch if its the same as permalink extension
        $pext = strtolower(pathinfo(get_option('permalink_structure'), PATHINFO_EXTENSION));
        if ($pext) {
            $extensions = $this->_remove_extension_from_list($extensions, $pext);
        }

        $extensions_lowercase = array_map('strtolower', $extensions);
        $extensions_uppercase = array_map('strtoupper', $extensions);

        $rules = '';
        $headers_rules = '';

        if ($cache_control) {
            $cache_policy = $config->get_string('browsercache.' . $section . '.cache.policy');

            switch ($cache_policy) {
                case 'cache':
                    $headers_rules .= "        Header set Pragma \"public\"\n";
                    $headers_rules .= "        Header set Cache-Control \"public\"\n";
                    break;

                case 'cache_public_maxage':
                    $expires = $config->get_boolean('browsercache.' . $section . '.expires');
                    $lifetime = $config->get_integer('browsercache.' . $section . '.lifetime');

                    $headers_rules .= "        Header set Pragma \"public\"\n";

                    if ($expires)
                        $headers_rules .= "        Header append Cache-Control \"public\"\n";
                    else
                        $headers_rules .= "        Header set Cache-Control \"max-age=" . $lifetime . ", public\"\n";

                    break;

                case 'cache_validation':
                    $headers_rules .= "        Header set Pragma \"public\"\n";
                    $headers_rules .= "        Header set Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                    break;

                case 'cache_noproxy':
                    $headers_rules .= "        Header set Pragma \"public\"\n";
                    $headers_rules .= "        Header set Cache-Control \"private, must-revalidate\"\n";
                    break;

                case 'cache_maxage':
                    $expires = $config->get_boolean('browsercache.' . $section . '.expires');
                    $lifetime = $config->get_integer('browsercache.' . $section . '.lifetime');

                    $headers_rules .= "        Header set Pragma \"public\"\n";

                    if ($expires)
                        $headers_rules .= "        Header append Cache-Control \"public, must-revalidate, proxy-revalidate\"\n";
                    else
                        $headers_rules .= "        Header set Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\"\n";

                    break;

                case 'no_cache':
                    $headers_rules .= "        Header set Pragma \"no-cache\"\n";
                    $headers_rules .= "        Header set Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\"\n";
                    break;
            }
        }

        if ($etag) {
            $rules .= "    FileETag MTime Size\n";
        } else {
            if ($compatibility) {
                $rules .= "    FileETag None\n";
                $headers_rules .= "         Header unset ETag\n";
            }
        }

        if ($unset_setcookie)
            $headers_rules .= "         Header unset Set-Cookie\n";

        if (!$set_last_modified)
            $headers_rules .= "         Header unset Last-Modified\n";

        if ($w3tc)
            $headers_rules .= "         Header set X-Powered-By \"" . W3TC_POWERED_BY . "\"\n";

        if (strlen($headers_rules) > 0) {
            $rules .= "    <IfModule mod_headers.c>\n";
            $rules .= $headers_rules;
            $rules .= "    </IfModule>\n";
        }

        if (strlen($rules) > 0) {
            $rules = "<FilesMatch \"\\.(" . implode('|', 
                array_merge($extensions_lowercase, $extensions_uppercase)) . 
                ")$\">\n" . $rules;
            $rules .= "</FilesMatch>\n";
        }

        return $rules;
    }

    /**
     * Returns cache rules
     *
     * @param W3_Config $config
     * @param bool $cdnftp
     * @return string
     */
    private function rules_cache_generate_nginx($config, $cdnftp = false) {
        $mime_types = $this->get_mime_types();
        $cssjs_types = $mime_types['cssjs'];
        $cssjs_types = array_unique($cssjs_types);
        $html_types = $mime_types['html'];
        $other_types = $mime_types['other'];

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE . "\n";

        $cssjs_compression = $config->get_boolean('browsercache.cssjs.compression');
        $html_compression = $config->get_boolean('browsercache.html.compression');
        $other_compression = $config->get_boolean('browsercache.other.compression');

        if ($cssjs_compression || $html_compression || $other_compression) {
            $compression_types = array();

            if ($cssjs_compression) {
                $compression_types = array_merge($compression_types, $cssjs_types);
            }

            if ($html_compression) {
                $compression_types = array_merge($compression_types, $html_types);
            }

            if ($other_compression) {
                $compression_types = array_merge($compression_types, array(
                    'ico' => 'image/x-icon'
                ));
            }

            unset($compression_types['html|htm']);

            $rules .= "gzip on;\n";
            $rules .= "gzip_types " . implode(' ', $compression_types) . ";\n";
        }

        foreach ($mime_types as $type => $extensions)
            $this->_rules_cache_generate_nginx_for_type($config, $rules, 
                $extensions, $type);

        $rules .= W3TC_MARKER_END_BROWSERCACHE_CACHE . "\n";

        return $rules;
    }

    /**
     * Adds cache rules for type to &$rules
     *
     * @param W3_Config $config
     * @param string $rules
     * @param array $mime_types
     * @param string $section
     * @param boolean $write_location
     * @param boolean $cdnftp
     * @return void
     */
    private function _rules_cache_generate_nginx_for_type($config, &$rules, 
            $mime_types, $section, $write_location = false, $cdnftp = false) {
        $expires = $config->get_boolean('browsercache.' . $section . '.expires');
        $cache_control = $config->get_boolean('browsercache.' . $section . '.cache.control');
        $w3tc = $config->get_boolean('browsercache.' . $section . '.w3tc');

        if ($expires || $cache_control || $w3tc) {
            $lifetime = $config->get_integer('browsercache.' . $section . '.lifetime');

            $extensions = array_keys($mime_types);

            // Remove ext from filesmatch if its the same as permalink extension
            $pext = strtolower(pathinfo(get_option('permalink_structure'), PATHINFO_EXTENSION));
            if ($pext) {
                $extensions = $this->_remove_extension_from_list($extensions, $pext);
            }

            $rules .= "location ~ \\.(" . implode('|', $extensions) . ")$ {\n";

            if ($expires) {
                $rules .= "    expires " . $lifetime . "s;\n";
            }

            if ($cache_control) {
                $cache_policy = $config->get_string('browsercache.cssjs.cache.policy');

                switch ($cache_policy) {
                    case 'cache':
                        $rules .= "    add_header Pragma \"public\";\n";
                        $rules .= "    add_header Cache-Control \"public\";\n";
                        break;

                    case 'cache_public_maxage':
                        $rules .= "    add_header Pragma \"public\";\n";
                        $rules .= "    add_header Cache-Control \"max-age=" . $lifetime . ", public\";\n";
                        break;

                    case 'cache_validation':
                        $rules .= "    add_header Pragma \"public\";\n";
                        $rules .= "    add_header Cache-Control \"public, must-revalidate, proxy-revalidate\";\n";
                        break;

                    case 'cache_noproxy':
                        $rules .= "    add_header Pragma \"public\";\n";
                        $rules .= "    add_header Cache-Control \"private, must-revalidate\";\n";
                        break;

                    case 'cache_maxage':
                        $rules .= "    add_header Pragma \"public\";\n";
                        $rules .= "    add_header Cache-Control \"max-age=" . $lifetime . ", public, must-revalidate, proxy-revalidate\";\n";
                        break;

                    case 'no_cache':
                        $rules .= "    add_header Pragma \"no-cache\";\n";
                        $rules .= "    add_header Cache-Control \"max-age=0, private, no-store, no-cache, must-revalidate\";\n";
                        break;
                }
            }

            $w3_dispatcher = w3_instance('W3_Dispatcher');
            $rules .= $w3_dispatcher->on_browsercache_rules_generation_for_section(
                $config, $cdnftp, $section);

            if ($w3tc) {
                $rules .= "    add_header X-Powered-By \"" . W3TC_POWERED_BY . "\";\n";
            }
            if ($write_location) {
                $rules .= '    try_files $uri $uri/ $uri.html /index.php?$args;' . "\n";
            }
            $rules .= "}\n";
        }
    }



    /**
     * rules_no404wp
     **/

    /**
     * Writes no 404 by WP rules
     *
     * @param W3_Config $config
     * @param SelfTestExceptions $exs
     * @throws FilesystemOperationException with S/FTP form
     * @throws FileOperationException
     */
    private function rules_no404wp_add($config, $exs) {
        w3_add_rules($exs, w3_get_browsercache_rules_no404wp_path(),
            $this->rules_no404wp_generate($config),
            W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP,
            W3TC_MARKER_END_BROWSERCACHE_NO404WP,
            array(
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
                W3TC_MARKER_END_PGCACHE_CORE =>
                strlen(W3TC_MARKER_END_PGCACHE_CORE) + 1,
                W3TC_MARKER_END_MINIFY_CORE =>
                strlen(W3TC_MARKER_END_MINIFY_CORE) + 1,
                W3TC_MARKER_END_BROWSERCACHE_CACHE =>
                strlen(W3TC_MARKER_END_BROWSERCACHE_CACHE) + 1,
                W3TC_MARKER_END_PGCACHE_CACHE =>
                strlen(W3TC_MARKER_END_PGCACHE_CACHE) + 1,
                W3TC_MARKER_END_MINIFY_CACHE =>
                strlen(W3TC_MARKER_END_MINIFY_CACHE) + 1
            )
        );
    }

    /**
     * Removes 404 directives
     *
     * @throws FilesystemOperationException with S/FTP form
     * @throws FileOperationException
     */
    private function rules_no404wp_remove($exs) {
        w3_remove_rules($exs,
                w3_get_browsercache_rules_no404wp_path(),
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP,
                W3TC_MARKER_END_BROWSERCACHE_NO404WP
            );
    }

    /**
     * Generate rules related to prevent for media 404 error by WP
     *
     * @param W3_Config $config
     * @return string
     */
    private function rules_no404wp_generate($config) {
        switch (true) {
            case w3_is_apache():
            case w3_is_litespeed():
                return $this->rules_no404wp_generate_apache($config);

            case w3_is_nginx():
                return $this->rules_no404wp_generate_nginx($config);
        }

        return false;
    }

    /**
     * Generate rules related to prevent for media 404 error by WP
     *
     * @param W3_Config $config
     * @return string
     */
    private function rules_no404wp_generate_apache($config) {
        $a = $this->get_mime_types();
        $cssjs_types = $a['cssjs'];
        $html_types = $a['html'];
        $other_types = $a['other'];

        $extensions = array_merge(array_keys($cssjs_types), 
            array_keys($html_types), array_keys($other_types));

        $permalink_structure = get_option('permalink_structure');
        $permalink_structure_ext = ltrim(strrchr($permalink_structure, '.'), 
            '.');

        if ($permalink_structure_ext != '') {
            foreach ($extensions as $index => $extension) {
                if (strstr($extension, $permalink_structure_ext) !== false) {
                    $extensions[$index] = preg_replace('~\|?' . 
                        w3_preg_quote($permalink_structure_ext) . 
                        '\|?~', '', $extension);
                }
            }
        }

        $exceptions = $config->get_array('browsercache.no404wp.exceptions');

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP . "\n";
        $rules .= "<IfModule mod_rewrite.c>\n";
        $rules .= "    RewriteEngine On\n";
        $rules .= "    RewriteCond %{REQUEST_FILENAME} !-f\n";
        $rules .= "    RewriteCond %{REQUEST_FILENAME} !-d\n";

        if (count($exceptions)) {
            $rules .= "    RewriteCond %{REQUEST_URI} !(" . 
                implode('|', $exceptions) . ")\n";
        }

        $rules .= "    RewriteCond %{REQUEST_FILENAME} \\.(" . 
            implode('|', $extensions) . ")$ [NC]\n";
        $rules .= "    RewriteRule .* - [L]\n";
        $rules .= "</IfModule>\n";
        $rules .= W3TC_MARKER_END_BROWSERCACHE_NO404WP . "\n";

        return $rules;
    }

    /**
     * Generate rules related to prevent for media 404 error by WP
     *
     * @param W3_Config $config
     * @return string
     */
    private function rules_no404wp_generate_nginx($config) {
        $a = $this->get_mime_types();
        $cssjs_types = $a['cssjs'];
        $html_types = $a['html'];
        $other_types = $a['other'];

        $extensions = array_merge(array_keys($cssjs_types), 
            array_keys($html_types), array_keys($other_types));

        $permalink_structure = get_option('permalink_structure');
        $permalink_structure_ext = 
            ltrim(strrchr($permalink_structure, '.'), '.');

        if ($permalink_structure_ext != '') {
            foreach ($extensions as $index => $extension) {
                if (strstr($extension, $permalink_structure_ext) !== false) {
                    $extensions[$index] = preg_replace('~\|?' . 
                        w3_preg_quote($permalink_structure_ext) . '\|?~', '', 
                        $extension);
                }
            }
        }

        $exceptions = $config->get_array('browsercache.no404wp.exceptions');

        $rules = '';
        $rules .= W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP . "\n";
        $rules .= "if (-f \$request_filename) {\n";
        $rules .= "    break;\n";
        $rules .= "}\n";
        $rules .= "if (-d \$request_filename) {\n";
        $rules .= "    break;\n";
        $rules .= "}\n";

        if (count($exceptions)) {
            $rules .= "if (\$request_uri ~ \"(" . implode('|', $exceptions) . 
                ")\") {\n";
            $rules .= "    break;\n";
            $rules .= "}\n";
        }

        $rules .= "if (\$request_uri ~* \\.(" . implode('|', $extensions) . 
            ")$) {\n";
        $rules .= "    return 404;\n";
        $rules .= "}\n";
        $rules .= W3TC_MARKER_END_BROWSERCACHE_NO404WP . "\n";

        return $rules;
    }



    /**
     * Returns the apache, nginx version
     * @return string
     */
    private function _get_server_version() {
        $sig= explode('/', $_SERVER['SERVER_SOFTWARE']);
        $temp = isset($sig[1]) ? explode(' ', $sig[1]) : array('0');
        $version = $temp[0];
        return $version;
    }

    /**
     * Takes an array of extensions single per row and/or extensions delimited by |
     * @param $extensions
     * @param $ext
     * @return array
     */
    private function _remove_extension_from_list($extensions, $ext) {
        for ($i = 0; $i < sizeof($extensions); $i++) {
            if ($extensions[$i] == $ext) {
                unset($extensions[$i]);
                return $extensions;
            } elseif (strpos($extensions[$i], $ext) !== false && 
                    strpos($extensions[$i], '|') !== false) {
                $exts = explode('|', $extensions[$i]);
                $key = array_search($ext, $exts);
                unset($exts[$key]);
                $extensions[$i] = implode('|', $exts);
                return $extensions;
            }
        }
        return $extensions;
    }
}
