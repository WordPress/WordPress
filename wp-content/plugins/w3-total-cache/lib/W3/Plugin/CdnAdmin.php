<?php

/**
 * W3 Total Cache CDN Plugin
 */
if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_INC_DIR . '/functions/http.php');
w3_require_once(W3TC_LIB_W3_DIR . '/Plugin.php');

/**
 * Class W3_Plugin_CdnAdmin
 */
class W3_Plugin_CdnAdmin extends W3_Plugin {
    /**
     * Instantiates worker with common functionality on demand
     *
     * @return W3_Plugin_CdnCommon
     */
    function _get_common() {
        return w3_instance('W3_Plugin_CdnCommon');
    }

    /**
     * Purge attachment
     *
     * Upload _wp_attached_file, _wp_attachment_metadata, _wp_attachment_backup_sizes
     *
     * @param integer $attachment_id
     * @param array $results
     * @return boolean
     */
    function purge_attachment($attachment_id, &$results) {
        $files = $this->_get_common()->get_attachment_files($attachment_id);

        return $this->_get_common()->purge($files, false, $results);
    }

    /**
     * Updates file date in the queue
     *
     * @param integer $queue_id
     * @param string $last_error
     * @return integer
     */
    function queue_update($queue_id, $last_error) {
        global $wpdb;

        $sql = sprintf('UPDATE %s SET last_error = "%s", date = NOW() WHERE id = %d', $wpdb->prefix . W3TC_CDN_TABLE_QUEUE, esc_sql($last_error), $queue_id);

        return $wpdb->query($sql);
    }

    /**
     * Removes from queue
     *
     * @param integer $queue_id
     * @return integer
     */
    function queue_delete($queue_id) {
        global $wpdb;

        $sql = sprintf('DELETE FROM %s WHERE id = %d', $wpdb->prefix . W3TC_CDN_TABLE_QUEUE, $queue_id);

        return $wpdb->query($sql);
    }

    /**
     * Empties queue
     *
     * @param integer $command
     * @return integer
     */
    function queue_empty($command) {
        global $wpdb;

        $sql = sprintf('DELETE FROM %s WHERE command = %d', $wpdb->prefix . W3TC_CDN_TABLE_QUEUE, $command);

        return $wpdb->query($sql);
    }

    /**
     * Returns queue
     *
     * @param integer $limit
     * @return array
     */
    function queue_get($limit = null) {
        global $wpdb;

        $sql = sprintf('SELECT * FROM %s%s ORDER BY date', $wpdb->prefix, W3TC_CDN_TABLE_QUEUE);

        if ($limit) {
            $sql .= sprintf(' LIMIT %d', $limit);
        }

        $results = $wpdb->get_results($sql);
        $queue = array();

        if ($results) {
            foreach ((array) $results as $result) {
                $queue[$result->command][] = $result;
            }
        }

        return $queue;
    }

    /**
     * Process queue
     *
     * @param integer $limit
	 * @return integer
     */
    function queue_process($limit) {
        $items = 0;
        
        $commands = $this->queue_get($limit);
        $force_rewrite = $this->_config->get_boolean('cdn.force.rewrite');

        if (count($commands)) {
            $cdn = $this->_get_common()->get_cdn();

            foreach ($commands as $command => $queue) {
                $files = array();
                $results = array();
                $map = array();

                foreach ($queue as $result) {
                    $files[] = $this->_get_common()->build_file_descriptor($result->local_path, $result->remote_path);
                    $map[$result->local_path] = $result->id;
                    $items++;
                }

                switch ($command) {
                    case W3TC_CDN_COMMAND_UPLOAD:
                        $dispatcher = w3_instance('W3_Dispatcher');
                        foreach ($files as $file) {
                            $local_file_name = $file['local_path'];
                            $remote_file_name = $file['remote_path'];
                            if (!file_exists($local_file_name)) {
                                $dispatcher->create_file_for_cdn($local_file_name);
                            }
                        }

                        $cdn->upload($files, $results, $force_rewrite);
                        
                        foreach ($results as $result) {
                            if ($result['result'] == W3TC_CDN_RESULT_OK) {
                                $dispatcher->on_cdn_file_upload($result['local_path']);
                            }
                        }
                        break;

                    case W3TC_CDN_COMMAND_DELETE:
                        $cdn->delete($files, $results);
                        break;

                    case W3TC_CDN_COMMAND_PURGE:
                        $cdn->purge($files, $results);
                        break;
                }

                foreach ($results as $result) {
                    if ($result['result'] == W3TC_CDN_RESULT_OK) {
                        $this->queue_delete($map[$result['local_path']]);
                    } else {
                        $this->queue_update($map[$result['local_path']], $result['error']);
                    }
                }
            }
        }
        
        return $items;
    }

    /**
     * Export library to CDN
     *
     * @param integer $limit
     * @param integer $offset
     * @param integer $count
     * @param integer $total
     * @param array $results
     * @return void
     */
    function export_library($limit, $offset, &$count, &$total, &$results) {
        global $wpdb;

        $count = 0;
        $total = 0;

        $upload_info = w3_upload_info();

        if ($upload_info) {
            $sql = sprintf('SELECT
        		pm.meta_value AS file,
                pm2.meta_value AS metadata
            FROM
                %sposts AS p
            LEFT JOIN
                %spostmeta AS pm ON p.ID = pm.post_ID AND pm.meta_key = "_wp_attached_file"
            LEFT JOIN
            	%spostmeta AS pm2 ON p.ID = pm2.post_ID AND pm2.meta_key = "_wp_attachment_metadata"
            WHERE
                p.post_type = "attachment"  AND (pm.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)
            GROUP BY
            	p.ID', $wpdb->prefix, $wpdb->prefix, $wpdb->prefix);

            if ($limit) {
                $sql .= sprintf(' LIMIT %d', $limit);

                if ($offset) {
                    $sql .= sprintf(' OFFSET %d', $offset);
                }
            }

            $posts = $wpdb->get_results($sql);

            if ($posts) {
                $count = count($posts);
                $total = $this->get_attachments_count();
                $files = array();

                foreach ($posts as $post) {
                    $post_files = array();

                    if ($post->file) {
                        $file = $this->_get_common()->normalize_attachment_file($post->file);

                        $local_file = $upload_info['basedir'] . '/' . $file;
                        $remote_file = ltrim($upload_info['baseurlpath'] . $file, '/');

                        $post_files[] = $this->_get_common()->build_file_descriptor($local_file, $remote_file);
                    }

                    if ($post->metadata) {
                        $metadata = @unserialize($post->metadata);

                        $post_files = array_merge($post_files, $this->_get_common()->get_metadata_files($metadata));
                    }

                    $post_files = apply_filters('w3tc_cdn_add_attachment', $post_files);

                    $files = array_merge($files, $post_files);
                }

                $this->_get_common()->upload($files, false, $results);
            }
        }
    }

    /**
     * Imports library
     *
     * @param integer $limit
     * @param integer $offset
     * @param integer $count
     * @param integer $total
     * @param array $results
     * @return boolean
     */
    function import_library($limit, $offset, &$count, &$total, &$results) {
        global $wpdb;

        $count = 0;
        $total = 0;
        $results = array();

        $upload_info = w3_upload_info();
        $uploads_use_yearmonth_folders = get_option('uploads_use_yearmonth_folders');
        $document_root = w3_get_document_root();

        @set_time_limit($this->_config->get_integer('timelimit.cdn_import'));

        if ($upload_info) {
            /**
             * Search for posts with links or images
             */
            $sql = sprintf('SELECT
        		ID,
        		post_content,
        		post_date
            FROM
                %sposts
            WHERE
                post_status = "publish"
                AND (post_type = "post" OR post_type = "page")
                AND (post_content LIKE "%%src=%%"
                	OR post_content LIKE "%%href=%%")
       		', $wpdb->prefix);

            if ($limit) {
                $sql .= sprintf(' LIMIT %d', $limit);

                if ($offset) {
                    $sql .= sprintf(' OFFSET %d', $offset);
                }
            }

            $posts = $wpdb->get_results($sql);

            if ($posts) {
                $count = count($posts);
                $total = $this->get_import_posts_count();
                $regexp = '~(' . $this->get_regexp_by_mask($this->_config->get_string('cdn.import.files')) . ')$~';
                $import_external = $this->_config->get_boolean('cdn.import.external');

                foreach ($posts as $post) {
                    $matches = null;
                    $replaced = array();
                    $attachments = array();
                    $post_content = $post->post_content;

                    /**
                     * Search for all link and image sources
                     */
                    if (preg_match_all('~(href|src)=[\'"]?([^\'"<>\s]+)[\'"]?~', $post_content, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            list($search, $attribute, $origin) = $match;

                            /**
                             * Check if $search is already replaced
                             */
                            if (isset($replaced[$search])) {
                                continue;
                            }

                            $error = '';
                            $result = false;

                            $src = w3_normalize_file_minify($origin);
                            $dst = '';

                            /**
                             * Check if file exists in the library
                             */
                            if (stristr($origin, $upload_info['baseurl']) === false) {
                                /**
                                 * Check file extension
                                 */
                                $check_src = $src;

                                if (w3_is_url($check_src)) {
                                    $qpos = strpos($check_src, '?');

                                    if ($qpos !== false) {
                                        $check_src = substr($check_src, 0, $qpos);
                                    }
                                }

                                if (preg_match($regexp, $check_src)) {
                                    /**
                                     * Check for already uploaded attachment
                                     */
                                    if (isset($attachments[$src])) {
                                        list($dst, $dst_url) = $attachments[$src];
                                        $result = true;
                                    } else {
                                        if ($uploads_use_yearmonth_folders) {
                                            $upload_subdir = date('Y/m', strtotime($post->post_date));
                                            $upload_dir = sprintf('%s/%s', $upload_info['basedir'], $upload_subdir);
                                            $upload_url = sprintf('%s/%s', $upload_info['baseurl'], $upload_subdir);
                                        } else {
                                            $upload_subdir = '';
                                            $upload_dir = $upload_info['basedir'];
                                            $upload_url = $upload_info['baseurl'];
                                        }

                                        $src_filename = pathinfo($src, PATHINFO_FILENAME);
                                        $src_extension = pathinfo($src, PATHINFO_EXTENSION);

                                        /**
                                         * Get available filename
                                         */
                                        for ($i = 0; ; $i++) {
                                            $dst = sprintf('%s/%s%s%s', $upload_dir, $src_filename, ($i ? $i : ''), ($src_extension ? '.' . $src_extension : ''));

                                            if (!file_exists($dst)) {
                                                break;
                                            }
                                        }

                                        $dst_basename = basename($dst);
                                        $dst_url = sprintf('%s/%s', $upload_url, $dst_basename);
                                        $dst_path = ltrim(str_replace($document_root, '', w3_path($dst)), '/');

                                        if ($upload_subdir) {
                                            w3_mkdir($upload_subdir, 0777, $upload_info['basedir']);
                                        }

                                        $download_result = false;

                                        /**
                                         * Check if file is remote URL
                                         */
                                        if (w3_is_url($src)) {
                                            /**
                                             * Download file
                                             */
                                            if ($import_external) {
                                                $download_result = w3_download($src, $dst);

                                                if (!$download_result) {
                                                    $error = 'Unable to download file';
                                                }
                                            } else {
                                                $error = 'External file import is disabled';
                                            }
                                        } else {
                                            /**
                                             * Otherwise copy file from local path
                                             */
                                            $src_path = $document_root . '/' . urldecode($src);

                                            if (file_exists($src_path)) {
                                                $download_result = @copy($src_path, $dst);

                                                if (!$download_result) {
                                                    $error = 'Unable to copy file';
                                                }
                                            } else {
                                                $error = 'Source file doesn\'t exists';
                                            }
                                        }

                                        /**
                                         * Check if download or copy was successful
                                         */
                                        if ($download_result) {
                                            w3_require_once(W3TC_INC_DIR . '/functions/mime.php');

                                            $title = $dst_basename;
                                            $guid = ltrim($upload_info['baseurlpath'] . $title, ',');
                                            $mime_type = w3_get_mime_type($dst);

                                            $GLOBALS['wp_rewrite'] = new WP_Rewrite();

                                            /**
                                             * Insert attachment
                                             */
                                            $id = wp_insert_attachment(array(
                                                'post_mime_type' => $mime_type,
                                                'guid' => $guid,
                                                'post_title' => $title,
                                                'post_content' => '',
                                                'post_parent' => $post->ID
                                            ), $dst);

                                            if (!is_wp_error($id)) {
                                                /**
                                                 * Generate attachment metadata and upload to CDN
                                                 */
                                                require_once ABSPATH . 'wp-admin/includes/image.php';
                                                wp_update_attachment_metadata($id, wp_generate_attachment_metadata($id, $dst));

                                                $attachments[$src] = array(
                                                    $dst,
                                                    $dst_url
                                                );

                                                $result = true;
                                            } else {
                                                $error = 'Unable to insert attachment';
                                            }
                                        }
                                    }

                                    /**
                                     * If attachment was successfully created then replace links
                                     */
                                    if ($result) {
                                        $replace = sprintf('%s="%s"', $attribute, $dst_url);

                                        // replace $search with $replace
                                        $post_content = str_replace($search, $replace, $post_content);

                                        $replaced[$search] = $replace;
                                        $error = 'OK';
                                    }
                                } else {
                                    $error = 'File type rejected';
                                }
                            } else

                            {
                                $error = 'File already exists in the media library';
                            }

                            /**
                             * Add new entry to the log file
                             */

                            $results[] = array(
                                'src' => $src,
                                'dst' => $dst_path,
                                'result' => $result,
                                'error' => $error
                            );
                        }
                    }

                    /**
                     * If post content was chenged then update DB
                     */
                    if ($post_content != $post->post_content) {
                        wp_update_post(array(
                            'ID' => $post->ID,
                            'post_content' => $post_content
                        ));
                    }
                }
            }
        }
    }

    /**
     * Rename domain
     *
     * @param array $names
     * @param integer $limit
     * @param integer $offset
     * @param integer $count
     * @param integer $total
     * @param integer $results
     * @return void
     */
    function rename_domain($names, $limit, $offset, &$count, &$total, &$results) {
        global $wpdb;

        @set_time_limit($this->_config->get_integer('timelimit.domain_rename'));

        $count = 0;
        $total = 0;
        $results = array();

        $upload_info = w3_upload_info();

        foreach ($names as $index => $name) {
            $names[$index] = str_ireplace('www.', '', $name);
        }

        if ($upload_info) {
            $sql = sprintf('SELECT
        		ID,
        		post_content,
        		post_date
            FROM
                %sposts
            WHERE
                post_status = "publish"
                AND (post_type = "post" OR post_type = "page")
                AND (post_content LIKE "%%src=%%"
                	OR post_content LIKE "%%href=%%")
       		', $wpdb->prefix);

            if ($limit) {
                $sql .= sprintf(' LIMIT %d', $limit);

                if ($offset) {
                    $sql .= sprintf(' OFFSET %d', $offset);
                }
            }

            $posts = $wpdb->get_results($sql);

            if ($posts) {
                $count = count($posts);
                $total = $this->get_rename_posts_count();
                $names_quoted = array_map('w3_preg_quote', $names);

                foreach ($posts as $post) {
                    $matches = null;
                    $post_content = $post->post_content;
                    $regexp = '~(href|src)=[\'"]?(https?://(www\.)?(' . implode('|', $names_quoted) . ')' . w3_preg_quote($upload_info['baseurlpath']) . '([^\'"<>\s]+))[\'"]~';

                    if (preg_match_all($regexp, $post_content, $matches, PREG_SET_ORDER)) {
                        foreach ($matches as $match) {
                            $old_url = $match[2];
                            $new_url = sprintf('%s/%s', $upload_info['baseurl'], $match[5]);
                            $post_content = str_replace($old_url, $new_url, $post_content);

                            $results[] = array(
                                'old' => $old_url,
                                'new' => $new_url,
                                'result' => true,
                                'error' => 'OK'
                            );
                        }
                    }

                    if ($post_content != $post->post_content) {
                        wp_update_post(array(
                            'ID' => $post->ID,
                            'post_content' => $post_content
                        ));
                    }
                }
            }
        }
    }

    /**
     * Returns attachments count
     *
     * @return integer
     */
    function get_attachments_count() {
        global $wpdb;

        $sql = sprintf('SELECT COUNT(DISTINCT p.ID)
FROM %sposts AS p
LEFT JOIN %spostmeta AS pm ON p.ID = pm.post_ID
AND pm.meta_key =  "_wp_attached_file"
LEFT JOIN %spostmeta AS pm2 ON p.ID = pm2.post_ID
AND pm2.meta_key =  "_wp_attachment_metadata"
WHERE p.post_type = "attachment" AND (pm.meta_value IS NOT NULL OR pm2.meta_value IS NOT NULL)', $wpdb->prefix, $wpdb->prefix, $wpdb->prefix);

        return $wpdb->get_var($sql);
    }

    /**
     * Returns import posts count
     *
     * @return integer
     */
    function get_import_posts_count() {
        global $wpdb;

        $sql = sprintf('SELECT
        		COUNT(*)
            FROM
                %sposts
            WHERE
                post_status = "publish"
                AND (post_type = "post" OR post_type = "page")
                AND (post_content LIKE "%%src=%%"
                	OR post_content LIKE "%%href=%%")
                ', $wpdb->prefix);

        return $wpdb->get_var($sql);
    }

    /**
     * Returns rename posts count
     *
     * @return integer
     */
    function get_rename_posts_count() {
        return $this->get_import_posts_count();
    }

    /**
     * Returns regexp by mask
     *
     * @param string $mask
     * @return string
     */
    function get_regexp_by_mask($mask) {
        $mask = trim($mask);
        $mask = w3_preg_quote($mask);

        $mask = str_replace(array(
            '\*',
            '\?',
            ';'
        ), array(
            '@ASTERISK@',
            '@QUESTION@',
            '|'
        ), $mask);

        $regexp = str_replace(array(
            '@ASTERISK@',
            '@QUESTION@'
        ), array(
            '[^\\?\\*:\\|"<>]*',
            '[^\\?\\*:\\|"<>]'
        ), $mask);

        return $regexp;
    }

    /**
     * @param $error
     */
    function update_cnames(&$error) {
        $cdn = $this->_get_common()->get_cdn();
        $cdn->update_cnames($error);
    }


    /**
     * media_row_actions filter
     *
     * @param array $actions
     * @param object $post
     * @return array
     */
    function media_row_actions($actions, $post) {
        $actions = array_merge($actions, array(
            'cdn_purge' => sprintf('<a href="%s">' . __('Purge from CDN', 'w3-total-cache') . '</a>', wp_nonce_url(sprintf('admin.php?page=w3tc_dashboard&w3tc_cdn_purge_attachment&attachment_id=%d', $post->ID), 'w3tc'))
        ));

        return $actions;
    }

    /**
     * Changes settings on MaxCDN/NetDNA site
     */
    function change_canonical_header() {
        if (in_array($cdn_engine = $this->_config->get_string('cdn.engine'), array('maxcdn', 'netdna'))) {
            w3_require_once(W3TC_LIB_NETDNA_DIR . '/NetDNA.php');
            $authorization_key = $this->_config->get_string("cdn.$cdn_engine.authorization_key");
            if ($authorization_key) {
                $keys = explode('+', $authorization_key);
                if (sizeof($keys) == 3) {
                    list($alias, $consumer_key, $consumer_secret) =  $keys;
                    $api = new NetDNA($alias, $consumer_key, $consumer_secret);
                    $zone = array();
                    $zone_id = $this->_config->get_string("cdn.$cdn_engine.zone_id");
                    $zone['canonical_link_headers'] = $this->_config->get_boolean('cdn.canonical_header') ? 1 : 0;
                    try {
                        $api->update_pull_zone($zone_id, $zone);
                    } catch (Exception $ex) {}
                }
            }
        }
    }

    function is_running() {
        /**
         * CDN
         */
        $running = true;

        /**
         * Check CDN settings
         */
        $cdn_engine = $this->_config->get_string('cdn.engine');

        switch (true) {
            case ($cdn_engine == 'ftp' && !count($this->_config->get_array('cdn.ftp.domain'))):
                $running = false;
                break;

            case ($cdn_engine == 's3' && ($this->_config->get_string('cdn.s3.key') == '' || $this->_config->get_string('cdn.s3.secret') == '' || $this->_config->get_string('cdn.s3.bucket') == '')):
                $running = false;                    break;

            case ($cdn_engine == 'cf' && ($this->_config->get_string('cdn.cf.key') == '' || $this->_config->get_string('cdn.cf.secret') == '' || $this->_config->get_string('cdn.cf.bucket') == '' || ($this->_config->get_string('cdn.cf.id') == '' && !count($this->_config->get_array('cdn.cf.cname'))))):
                $running = false;
                break;

            case ($cdn_engine == 'cf2' && ($this->_config->get_string('cdn.cf2.key') == '' || $this->_config->get_string('cdn.cf2.secret') == '' || ($this->_config->get_string('cdn.cf2.id') == '' && !count($this->_config->get_array('cdn.cf2.cname'))))):
                $running = false;
                break;

            case ($cdn_engine == 'rscf' && ($this->_config->get_string('cdn.rscf.user') == '' || $this->_config->get_string('cdn.rscf.key') == '' || $this->_config->get_string('cdn.rscf.container') == '' || !count($this->_config->get_array('cdn.rscf.cname')))):
                $running = false;
                break;

            case ($cdn_engine == 'azure' && ($this->_config->get_string('cdn.azure.user') == '' || $this->_config->get_string('cdn.azure.key') == '' || $this->_config->get_string('cdn.azure.container') == '')):
                $running = false;
                break;

            case ($cdn_engine == 'mirror' && !count($this->_config->get_array('cdn.mirror.domain'))):
                $running = false;
                break;

            case ($cdn_engine == 'netdna'):
                $running = false;
                break;

            case ($cdn_engine == 'maxcdn'):
                $running = false;
                break;

            case ($cdn_engine == 'cotendo' && !count($this->_config->get_array('cdn.cotendo.domain'))):
                $running = false;
                break;

            case ($cdn_engine == 'edgecast' && !count($this->_config->get_array('cdn.edgecast.domain'))):
                $running = false;
                break;

            case ($cdn_engine == 'att' && !count($this->_config->get_array('cdn.att.domain'))):
                $running = false;
                break;

            case ($cdn_engine == 'akamai' && !count($this->_config->get_array('cdn.akamai.domain'))):
                $running = false;
                break;
        }
        return $running;
    }
}
