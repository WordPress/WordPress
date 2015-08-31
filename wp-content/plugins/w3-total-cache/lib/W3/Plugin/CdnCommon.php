<?php

/**
 * W3 Total Cache CDN Plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_CdnCommon
 */
class W3_Plugin_CdnCommon extends W3_Plugin {
    /**
     * If background uploading already scheduled
     * 
     * @var boolean
     */
    var $_upload_scheduled = false;
    
    /**
     * Adds file to queue
     *
     * @param string $local_path
     * @param string $remote_path
     * @param integer $command
     * @param string $last_error
     * @return integer
     */
    function queue_add($local_path, $remote_path, $command, $last_error) {
        global $wpdb;

        $table = $wpdb->prefix . W3TC_CDN_TABLE_QUEUE;
        $sql = sprintf('SELECT id FROM %s WHERE local_path = "%s" AND remote_path = "%s" AND command != %d', $table, esc_sql($local_path), esc_sql($remote_path), $command);

        $row = $wpdb->get_row($sql);

        if ($row) {
            $sql = sprintf('DELETE FROM %s WHERE id = %d', $table, $row->id);
        } else {
            $sql = sprintf('REPLACE INTO %s (local_path, remote_path, command, last_error, date) VALUES ("%s", "%s", %d, "%s", NOW())', $table, esc_sql($local_path), esc_sql($remote_path), $command, esc_sql($last_error));
        }

        return $wpdb->query($sql);
    }

    /**
     * Returns array of array('local_path' => '', 'remote_path' => '') for specified file
     *
     * @param string $file
     * @return array
     */
    function get_files_for_upload($file) {
        w3_require_once(W3TC_INC_DIR . '/functions/http.php');

        $files = array();
        $upload_info = w3_upload_info();

        if ($upload_info) {
            $file = $this->normalize_attachment_file($file);

            $local_file = $upload_info['basedir'] . '/' . $file;
            $remote_file = ltrim($upload_info['baseurlpath'] . $file, '/');

            $files[] = $this->build_file_descriptor($local_file, $remote_file);
        }

        return $files;
    }

    /**
     * Returns array of files from sizes array
     *
     * @param string $attached_file
     * @param array $sizes
     * @return array
     */
    function _get_sizes_files($attached_file, $sizes) {
        $files = array();
        $base_dir = w3_dirname($attached_file);

        foreach ((array) $sizes as $size) {
            if (isset($size['file'])) {
                if ($base_dir) {
                    $file = $base_dir . '/' . $size['file'];
                } else {
                    $file = $size['file'];
                }

                $files = array_merge($files, $this->get_files_for_upload($file));
            }
        }

        return $files;
    }

    /**
     * Returns attachment files by metadata
     *
     * @param array $metadata
     * @return array
     */
    function get_metadata_files($metadata) {
        $files = array();

        if (isset($metadata['file']) && isset($metadata['sizes'])) {
            $files = array_merge($files, $this->_get_sizes_files($metadata['file'], $metadata['sizes']));
        }

        return $files;
    }

    /**
     * Returns attachment files by attachment ID
     *
     * @param integer $attachment_id
     * @return array
     */
    function get_attachment_files($attachment_id) {
        $files = array();

        /**
         * Get attached file
         */
        $attached_file = get_post_meta($attachment_id, '_wp_attached_file', true);

        if ($attached_file != '') {
            $files = array_merge($files, $this->get_files_for_upload($attached_file));

            /**
             * Get backup sizes files
             */
            $attachment_backup_sizes = get_post_meta($attachment_id, '_wp_attachment_backup_sizes', true);

            if (is_array($attachment_backup_sizes)) {
                $files = array_merge($files, $this->_get_sizes_files($attached_file, $attachment_backup_sizes));
            }
        }

        /**
         * Get files from metadata
         */
        $attachment_metadata = get_post_meta($attachment_id, '_wp_attachment_metadata', true);

        if (is_array($attachment_metadata)) {
            $files = array_merge($files, $this->get_metadata_files($attachment_metadata));
        }

        return $files;
    }

    /**
     * Uploads files to CDN
     *
     * @param array $files
     * @param boolean $queue_failed
     * @param array $results
     * @return boolean
     */
    function upload($files, $queue_failed, &$results) {
        $cdn = $this->get_cdn();
        $force_rewrite = $this->_config->get_boolean('cdn.force.rewrite');

        @set_time_limit($this->_config->get_integer('timelimit.cdn_upload'));

        $return = $cdn->upload($files, $results, $force_rewrite);

        if (!$return && $queue_failed) {
            foreach ($results as $result) {
                if ($result['result'] != W3TC_CDN_RESULT_OK) {
                    $this->queue_add($result['local_path'], $result['remote_path'], W3TC_CDN_COMMAND_UPLOAD, $result['error']);
                }
            }
        }

        return $return;
    }

    /**
     * Deletes files frrom CDN
     *
     * @param array $files
     * @param boolean $queue_failed
     * @param array $results
     * @return boolean
     */
    function delete($files, $queue_failed, &$results) {
        $cdn = $this->get_cdn();

        @set_time_limit($this->_config->get_integer('timelimit.cdn_delete'));

        $return = $cdn->delete($files, $results);

        if (!$return && $queue_failed) {
            foreach ($results as $result) {
                if ($result['result'] != W3TC_CDN_RESULT_OK) {
                    $this->queue_add($result['local_path'], $result['remote_path'], W3TC_CDN_COMMAND_DELETE, $result['error']);
                }
            }
        }

        return $return;
    }

    /**
     * Purges files from CDN
     *
     * @param array $files consisting of array('local_path'=>'', 'remote_path'=>'')
     * @param boolean $queue_failed
     * @param array $results
     * @return boolean
     */
    function purge($files, $queue_failed, &$results) {
        /**
         * Purge varnish servers before mirror purging
         */
        if (w3_is_cdn_mirror($this->_config->get_string('cdn.engine')) && $this->_config->get_boolean('varnish.enabled')) {
            $varnish = w3_instance('W3_VarnishFlush');

            foreach ($files as $file) {
                $remote_path = $file['remote_path'];
                $varnish->flush_url(network_site_url($remote_path));
            }
        }

        /**
         * Purge CDN
         */
        $cdn = $this->get_cdn();

        @set_time_limit($this->_config->get_integer('timelimit.cdn_purge'));

        $return = $cdn->purge($files, $results);

        if (!$return && $queue_failed) {
            foreach ($results as $result) {
                if ($result['result'] != W3TC_CDN_RESULT_OK) {
                    $this->queue_add($result['local_path'], $result['remote_path'], W3TC_CDN_COMMAND_PURGE, $result['error']);
                }
            }
        }

        return $return;
    }

    /**
     * Purge CDN completely
     * @param $results
     * @return mixed
     */
    function purge_all(&$results) {
        /**
         * Purge CDN
         */
        $cdn = $this->get_cdn();

        @set_time_limit($this->_config->get_integer('timelimit.cdn_purge'));

        $return = $cdn->purge_all($results);
        return $return;
    }
    
    /**
     * Queues file upload.
     * Links wp_cron call to do that by the end of request processing
     *
     * @param string $url
     * @return void
     */
    function queue_upload_url($url) {

        /**
         * Get filesystem file name by url
         */
        $home_url_ssl =  w3_get_home_url_ssl();
        if (substr($url, 0, strlen($home_url_ssl)) == $home_url_ssl) {
            if (!w3_is_multisite())
                $home_url_ssl = str_replace(trim(w3_get_home_path(),'/'), '', $home_url_ssl);
            $path = str_replace($home_url_ssl, '', $url);
            $file_name = w3_get_document_root() . '/' . trim($path, '/');

        } else {
            // unknown url for uploading
            return;
        }

        $document_root = w3_get_document_root() . '/';
        
        /*
         * Define remote filename
         */
        if (!substr($file_name, 0, strlen($document_root)) == $document_root) {
            // unexpected file name
            return;
        }

        $remote_file_name = $this->uri_to_cdn_uri($this->docroot_filename_to_uri((substr($file_name, strlen($document_root)))));
        
        /*
         * Queue uploading
         */
        $this->queue_add($file_name, $remote_file_name, W3TC_CDN_COMMAND_UPLOAD, 'Pending');
        
        if (!$this->_upload_scheduled) {
            wp_schedule_single_event(time() - 100, 'w3_cdn_cron_queue_process');
            add_action('shutdown', 'wp_cron');
            $this->_upload_scheduled = true;
        }
    }

    /**
     * Normalizes attachment file
     *
     * @param string $file
     * @return string
     */
    function normalize_attachment_file($file) {
        w3_require_once(W3TC_INC_DIR . '/functions/http.php');

        $upload_info = w3_upload_info();
        if ($upload_info) {
            $file = ltrim(str_replace($upload_info['basedir'], '', $file), '/\\');
            $matches = null;

            if (preg_match('~(\d{4}/\d{2}/)?[^/]+$~', $file, $matches)) {
                $file = $matches[0];
            }
        }

        return $file;
    }

    /**
     * Returns CDN object
     *
     * @return W3_Cdn_Base
     */
    function get_cdn() {
        static $cdn = array();

        if (!isset($cdn[0])) {
            $engine = $this->_config->get_string('cdn.engine');
            $compression = ($this->_config->get_boolean('browsercache.enabled') && $this->_config->get_boolean('browsercache.html.compression'));

            switch ($engine) {
                case 'ftp':
                    $engine_config = array(
                        'host' => $this->_config->get_string('cdn.ftp.host'),
                        'user' => $this->_config->get_string('cdn.ftp.user'),
                        'pass' => $this->_config->get_string('cdn.ftp.pass'),
                        'path' => $this->_config->get_string('cdn.ftp.path'),
                        'pasv' => $this->_config->get_boolean('cdn.ftp.pasv'),
                        'domain' => $this->_config->get_array('cdn.ftp.domain'),
                        'ssl' => $this->_config->get_string('cdn.ftp.ssl'),
                        'compression' => false,
                        'docroot' => w3_get_document_root()
                    );
                    break;

                case 's3':
                    $engine_config = array(
                        'key' => $this->_config->get_string('cdn.s3.key'),
                        'secret' => $this->_config->get_string('cdn.s3.secret'),
                        'bucket' => $this->_config->get_string('cdn.s3.bucket'),
                        'cname' => $this->_config->get_array('cdn.s3.cname'),
                        'ssl' => $this->_config->get_string('cdn.s3.ssl'),
                        'compression' => $compression
                    );
                    break;

                case 'cf':
                    $engine_config = array(
                        'key' => $this->_config->get_string('cdn.cf.key'),
                        'secret' => $this->_config->get_string('cdn.cf.secret'),
                        'bucket' => $this->_config->get_string('cdn.cf.bucket'),
                        'id' => $this->_config->get_string('cdn.cf.id'),
                        'cname' => $this->_config->get_array('cdn.cf.cname'),
                        'ssl' => $this->_config->get_string('cdn.cf.ssl'),
                        'compression' => $compression
                    );
                    break;

                case 'cf2':
                    $engine_config = array(
                        'key' => $this->_config->get_string('cdn.cf2.key'),
                        'secret' => $this->_config->get_string('cdn.cf2.secret'),
                        'id' => $this->_config->get_string('cdn.cf2.id'),
                        'cname' => $this->_config->get_array('cdn.cf2.cname'),
                        'ssl' => $this->_config->get_string('cdn.cf2.ssl'),
                        'compression' => false
                    );
                    break;

                case 'rscf':
                    $engine_config = array(
                        'user' => $this->_config->get_string('cdn.rscf.user'),
                        'key' => $this->_config->get_string('cdn.rscf.key'),
                        'location' => $this->_config->get_string('cdn.rscf.location'),
                        'container' => $this->_config->get_string('cdn.rscf.container'),
                        'cname' => $this->_config->get_array('cdn.rscf.cname'),
                        'ssl' => $this->_config->get_string('cdn.rscf.ssl'),
                        'compression' => false
                    );
                    break;

                case 'azure':
                    $engine_config = array(
                        'user' => $this->_config->get_string('cdn.azure.user'),
                        'key' => $this->_config->get_string('cdn.azure.key'),
                        'container' => $this->_config->get_string('cdn.azure.container'),
                        'cname' => $this->_config->get_array('cdn.azure.cname'),
                        'ssl' => $this->_config->get_string('cdn.azure.ssl'),
                        'compression' => $compression
                    );
                    break;

                case 'mirror':
                    $engine_config = array(
                        'domain' => $this->_config->get_array('cdn.mirror.domain'),
                        'ssl' => $this->_config->get_string('cdn.mirror.ssl'),
                        'compression' => false
                    );
                    break;

                case 'maxcdn':
                    $engine_config = array(
                        'authorization_key' => $this->_config->get_string('cdn.maxcdn.authorization_key'),
                        'zone_id' => $this->_config->get_integer('cdn.maxcdn.zone_id'),
                        'domain' => $this->_config->get_array('cdn.maxcdn.domain'),
                        'ssl' => $this->_config->get_string('cdn.maxcdn.ssl'),
                        'compression' => false
                    );
                    break;

                case 'netdna':
                    $engine_config = array(
                        'authorization_key' => $this->_config->get_string('cdn.netdna.authorization_key'),
                        'zone_id' => $this->_config->get_integer('cdn.netdna.zone_id'),
                        'domain' => $this->_config->get_array('cdn.netdna.domain'),
                        'ssl' => $this->_config->get_string('cdn.netdna.ssl'),
                        'compression' => false
                    );
                    break;

                case 'cotendo':
                    $engine_config = array(
                        'username' => $this->_config->get_string('cdn.cotendo.username'),
                        'password' => $this->_config->get_string('cdn.cotendo.password'),
                        'zones' => $this->_config->get_array('cdn.cotendo.zones'),
                        'domain' => $this->_config->get_array('cdn.cotendo.domain'),
                        'ssl' => $this->_config->get_string('cdn.cotendo.ssl'),
                        'compression' => false
                    );
                    break;

                case 'edgecast':
                    $engine_config = array(
                        'account' => $this->_config->get_string('cdn.edgecast.account'),
                        'token' => $this->_config->get_string('cdn.edgecast.token'),
                        'domain' => $this->_config->get_array('cdn.edgecast.domain'),
                        'ssl' => $this->_config->get_string('cdn.edgecast.ssl'),
                        'compression' => false
                    );
                    break;

                case 'att':
                    $engine_config = array(
                        'account' => $this->_config->get_string('cdn.att.account'),
                        'token' => $this->_config->get_string('cdn.att.token'),
                        'domain' => $this->_config->get_array('cdn.att.domain'),
                        'ssl' => $this->_config->get_string('cdn.att.ssl'),
                        'compression' => false
                    );
                    break;
                
                case 'akamai':
                    $engine_config = array(
                        'username' => $this->_config->get_string('cdn.akamai.username'),
                        'password' => $this->_config->get_string('cdn.akamai.password'),
                        'zone' => $this->_config->get_string('cdn.akamai.zone'),
                        'domain' => $this->_config->get_array('cdn.akamai.domain'),
                        'ssl' => $this->_config->get_string('cdn.akamai.ssl'),
                        'email_notification' => $this->_config->get_array('cdn.akamai.email_notification'),
                        'compression' => false
                    );
                    break;
                
            }

            $engine_config = array_merge($engine_config, array(
                'debug' => $this->_config->get_boolean('cdn.debug')
            ));

            w3_require_once(W3TC_LIB_W3_DIR . '/Cdn.php');
            $cdn[0] = W3_Cdn::instance($engine, $engine_config);

            /**
             * Set cache config for CDN
             */
            if ($this->_config->get_boolean('browsercache.enabled')) {
                $w3_plugin_browsercache = w3_instance('W3_Plugin_BrowserCache');
                $cdn[0]->cache_config = $w3_plugin_browsercache->get_cache_config();
            }
        }

        return $cdn[0];
    }

    /**
     * Convert relative file which is relative to ABSPATH (wp folder on disc) to path uri
     *
     * @param $file
     * @return string
     */
    function docroot_filename_to_uri($file) {
        $file = ltrim($file, '/');
        // Translate multisite subsite uploads paths
        $file = str_replace(basename(WP_CONTENT_DIR) . '/blogs.dir/' . w3_get_blog_id() . '/', '', $file);
        if (strpos($file, basename(WP_CONTENT_DIR)) === 0 && !w3_is_multisite())
            $remote_path = '';
        else
            $remote_path = ltrim(w3_get_site_path(), "/");

        if ($remote_path == substr($file, 0, strlen($remote_path)))
            return $file;

        return $remote_path . $file;

    }

    /**
     * Convert a relative path (relative to ABSPATH (wp folder on disc) into a absolute path
     *
     * @param $file
     * @return string
     */
    function docroot_filename_to_absolute_path($file) {
        if(is_file($file))
            return $file;

        return  rtrim(w3_get_document_root(), "/") . '/' . ltrim($file, "/");
    }

    /**
     * Convert local uri path to CDN type specific path
     * @param $local_uri_path
     * @return string
     */
    function uri_to_cdn_uri($local_uri_path) {
        if (w3_is_network() && defined('DOMAIN_MAPPING') && DOMAIN_MAPPING)
            $local_uri_path = str_replace(w3_get_site_url(), '', $local_uri_path);
        $engine = $this->_config->get_string('cdn.engine');
        if (w3_is_cdn_mirror($engine)) {
            if (w3_is_network() && strpos($local_uri_path, 'files') === 0) {
                $upload_dir = wp_upload_dir();
                return ltrim($this->abspath_to_relative_path(dirname($upload_dir['basedir'])) . '/' . $local_uri_path, '/');
            }
        }

        $remote_path = $local_uri_path;

        return ltrim($remote_path, "/");
    }

    /**
     * Returns the sitepath for multisite subfolder or subdomain path for multisite subdomain
     * @return string
     */
    private function _get_multisite_url_identifier() {
        if (defined('DOMAIN_MAPPING') && DOMAIN_MAPPING) {
            $parsedUrl = parse_url(w3_get_site_url());
            return $parsedUrl['host'];
        } elseif (w3_is_subdomain_install()) {
            $parsedUrl = parse_url(w3_get_domain_url());
            $urlparts = explode('.', $parsedUrl['host']);

            if (sizeof($urlparts) > 2) {
                $subdomain = array_shift($urlparts);
                return trim($subdomain, '/');
            }
        }
        return trim(w3_get_site_path(), '/');
    }

    /**
     * Taks an absolute path and converts to a relative path to root
     * @param $path
     * @return mixed
     */
    function abspath_to_relative_path($path) {
        return str_replace(w3_get_document_root(), '', $path);
    }

    /**
     * Takes a root relative path and converts to a full uri
     * @param $path
     * @return string
     */
    function relative_path_to_url($path) {
        $cdnuri = $this->docroot_filename_to_uri(ltrim($path, "/"));
        return rtrim(w3_get_domain_url(), "/") . '/' . $cdnuri;
    }

    /**
     * Constructs a CDN file descriptor
     * @param $local_path
     * @param $remote_path
     * @return array
     */
    function build_file_descriptor($local_path, $remote_path) {
        $file = array('local_path' => $local_path,
                      'remote_path' => $remote_path,
                      'original_url' => $this->relative_path_to_url($local_path));

        $file = apply_filters('w3tc_build_cdn_file_array', $file);
        return $file;
    }
}

