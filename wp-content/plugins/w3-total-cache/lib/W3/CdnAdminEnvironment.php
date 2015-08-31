<?php

if (!defined('W3TC')) {
    die();
}

w3_require_once(W3TC_INC_DIR . '/functions/activation.php');
w3_require_once(W3TC_INC_DIR . '/functions/file.php');
w3_require_once(W3TC_INC_DIR . '/functions/rule.php');

/**
 * Class W3_CdnAdminEnvironment
 */
class W3_CdnAdminEnvironment {

    /**
     * Fixes environment in each wp-admin request
     *
     * @param W3_Config $config
     * @param bool $force_all_checks
     * @throws SelfTestExceptions
     */
    public function fix_on_wpadmin_request($config, $force_all_checks) {
        $exs = new SelfTestExceptions();

        if ($config->get_boolean('config.check') || $force_all_checks) {
            if ($config->get_boolean('cdn.enabled')) {
                $this->rules_add($config, $exs);
            } else {
                $this->rules_remove($exs);
            }
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment once event occurs
     *
     * @param W3_Config $config
     * @param string $event
     * @param W3_Config|null $old_config
     * @throws SelfTestExceptions
     **/
    public function fix_on_event($config, $event, $old_config = null) {
        if ($config->get_boolean('cdn.enabled') &&
                !w3_is_cdn_mirror($config->get_string('cdn.engine'))) {
            if ($old_config != null && 
                    $config->get_integer('cdn.queue.interval') != 
                    $old_config->get_integer('cdn.queue.interval')) {
                $this->unschedule_queue_process();
            }

            if (!wp_next_scheduled('w3_cdn_cron_queue_process')) {
                wp_schedule_event(time(), 
                    'w3_cdn_cron_queue_process', 'w3_cdn_cron_queue_process');
            }
        } else {
            $this->unschedule_queue_process();
        }

        if ($config->get_boolean('cdn.enabled') && 
                $config->get_boolean('cdn.autoupload.enabled') && 
                !w3_is_cdn_mirror($config->get_string('cdn.engine'))) {
            if ($old_config != null && 
                    $config->get_integer('cdn.autoupload.interval') != 
                    $old_config->get_integer('cdn.autoupload.interval')) {
                $this->unschedule_upload();
            }

            if (!wp_next_scheduled('w3_cdn_cron_upload')) {
                wp_schedule_event(time(), 
                    'w3_cdn_cron_upload', 'w3_cdn_cron_upload');
            }
        } else {
            $this->unschedule_upload();
        }

        $exs = new SelfTestExceptions();

        if ($config->get_boolean('cdn.enabled')) {
            try {
                $this->table_create($event == 'activate');
            } catch (Exception $ex) {
                $exs->push($ex);
            }
        }

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Fixes environment after plugin deactivation
     */
    public function fix_after_deactivation() {
        $exs = new SelfTestExceptions();

        $this->rules_remove($exs);
        $this->table_delete();

        if (count($exs->exceptions()) > 0)
            throw $exs;
    }

    /**
     * Returns required rules for module
     * @param W3_Config $config
     * @return array|null
     */
    function get_required_rules($config) {
        if (!$config->get_boolean('cdn.enabled'))
            return null;

        $rewrite_rules = array();
        $rules = $this->rules_generate($config);

        if (strlen($rules) > 0) {
            if ($config->get_string('cdn.engine') == 'ftp') {
                /**
                 * @var W3_Plugin_CdnCommon $w3_plugin_cdncommon
                 */
                $w3_plugin_cdncommon = w3_instance('W3_Plugin_CdnCommon');
                $domain = $w3_plugin_cdncommon->get_cdn()->get_domain();
                $cdn_rules_path = sprintf('ftp://%s/%s', $domain, 
                    w3_get_cdn_rules_path());
                $rewrite_rules[] = array(
                    'filename' => $cdn_rules_path, 
                    'content' => $rules
                );
            }

            $path = w3_get_browsercache_rules_cache_path();
            $rewrite_rules[] = array(
                'filename' => $path, 
                'content' => $rules
            );
        }
        return $rewrite_rules;
    }

    /**
     * @param W3_Config $config
     * @return array|null
     */
    function get_instructions($config) {
        if (!$config->get_boolean('cdn.enabled'))
            return null;

        $instructions = array();
        $instructions[] = array('title'=>__('CDN module: Required Database SQL', 'w3-total-cache'),
            'content' => $this->generate_table_sql(), 'area' => 'database');

        return $instructions;
    }

    /**
     * Generate rules for FTP
     **/
    public function rules_generate_for_ftp($config) {
        return $this->rules_generate($config, true);
    }



    /**
     * table operations
     **/

    /**
     * Create queue table
     *
     * @param bool $drop
     * @throws SelfTestFailedException
     */
    private function table_create($drop = false) {
        global $wpdb;

        if ($drop) {
            $sql = sprintf('DROP TABLE IF EXISTS `%s%s`;', $wpdb->prefix, W3TC_CDN_TABLE_QUEUE);

            $wpdb->query($sql);
        }

        $charset_collate = '';

        if ( ! empty($wpdb->charset) )
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if ( ! empty($wpdb->collate) )
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = sprintf("CREATE TABLE IF NOT EXISTS `%s%s` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `local_path` varchar(500) NOT NULL DEFAULT '',
            `remote_path` varchar(500) NOT NULL DEFAULT '',
            `command` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - Upload, 2 - Delete, 3 - Purge',
            `last_error` varchar(150) NOT NULL DEFAULT '',
            `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`id`),
            KEY `date` (`date`)
        ) $charset_collate;", $wpdb->prefix, W3TC_CDN_TABLE_QUEUE);

        $wpdb->query($sql);

        if (!$wpdb->result)
            throw new SelfTestFailedException('Can\'t create table ' .
                $wpdb->prefix . W3TC_CDN_TABLE_QUEUE);
    }

    /**
     * Delete queue table
     *
     * @return void
     */
    private function table_delete() {
        global $wpdb;

        $sql = sprintf('DROP TABLE IF EXISTS `%s%s`', $wpdb->prefix, W3TC_CDN_TABLE_QUEUE);
        $wpdb->query($sql);
    }

    private function generate_table_sql() {
        global $wpdb;
        $charset_collate = '';

        if ( ! empty($wpdb->charset) )
            $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
        if ( ! empty($wpdb->collate) )
            $charset_collate .= " COLLATE $wpdb->collate";

        $sql = sprintf('DROP TABLE IF EXISTS `%s%s`;', $wpdb->prefix, W3TC_CDN_TABLE_QUEUE);
        $sql .= "\n" . sprintf("CREATE TABLE IF NOT EXISTS `%s%s` (
            `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `local_path` varchar(500) NOT NULL DEFAULT '',
            `remote_path` varchar(500) NOT NULL DEFAULT '',
            `command` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 - Upload, 2 - Delete, 3 - Purge',
            `last_error` varchar(150) NOT NULL DEFAULT '',
            `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            PRIMARY KEY (`id`),
            KEY `date` (`date`)
        ) $charset_collate;", $wpdb->prefix, W3TC_CDN_TABLE_QUEUE);

        return $sql;
    }

    /**
     * schedules
     **/

    /**
     * Unschedules cron events
     */
    private function unschedule_queue_process() {
        if (wp_next_scheduled('w3_cdn_cron_queue_process')) {
            wp_clear_scheduled_hook('w3_cdn_cron_queue_process');
        }
    }

    /**
     * Unschedule upload event
     */
    private function unschedule_upload() {
        if (wp_next_scheduled('w3_cdn_cron_upload')) {
            wp_clear_scheduled_hook('w3_cdn_cron_upload');
        }
    }



    /**
     * rules core modification
     **/

    /**
     * Writes directives to WP .htaccess
     *
     * @param W3_Config $config
     * @param SelfTestExceptions $exs
     * @throws FilesystemOperationException with S/FTP form if it can't get the required filesystem credentials
     * @throws FileOperationException
     */
    private function rules_add($config, $exs) {
        w3_add_rules($exs, w3_get_browsercache_rules_cache_path(),
            $this->rules_generate($config),
            W3TC_MARKER_BEGIN_CDN,
            W3TC_MARKER_END_CDN,
            array(
                W3TC_MARKER_BEGIN_MINIFY_CORE => 0,
                W3TC_MARKER_BEGIN_PGCACHE_CORE => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_NO404WP => 0,
                W3TC_MARKER_BEGIN_BROWSERCACHE_CACHE => 0,
                W3TC_MARKER_BEGIN_WORDPRESS => 0,
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
    private function rules_remove($exs) {
        w3_remove_rules($exs,
            w3_get_browsercache_rules_cache_path(),
            W3TC_MARKER_BEGIN_CDN,
            W3TC_MARKER_END_CDN);
    }

    /**
     * Generates rules for WP dir
     *
     * @param W3_Config $config
     * @param bool $cdnftp
     * @return string
     */
    private function rules_generate($config, $cdnftp = false) {
        $w3_dispatcher = w3_instance('W3_Dispatcher');
        $w3_sharedRules = w3_instance('W3_SharedRules');

        $rules = '';
        if ($w3_dispatcher->canonical_generated_by($config, $cdnftp) == 'cdn')
            $rules .= $w3_sharedRules->canonical($config, $cdnftp);
        if ($w3_dispatcher->allow_origin_generated_by($config) == 'cdn')
            $rules .= $w3_sharedRules->allow_origin($config, $cdnftp);

        if (strlen($rules) > 0)
            $rules = 
                W3TC_MARKER_BEGIN_CDN . "\n" .
                $rules . 
                W3TC_MARKER_END_CDN . "\n";

        return $rules;
    }
}
