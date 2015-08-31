<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_DatabaseCache extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'dbcache.engine' =>  __('Database Cache Method:', 'w3-total-cache'),
                'dbcache.enabled' => __('Database Cache:', 'w3-total-cache'),
                'dbcache.debug' =>  __('Database Cache', 'w3-total-cache')
            ),
            'settings' => array(
                'dbcache.reject.logged' => __('Don\'t cache queries for logged in users', 'w3-total-cache'),
                'dbcache.memcached.servers' => __('Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:', 'w3-total-cache'),
                'dbcache.lifetime' => __('Maximum lifetime of cache objects:', 'w3-total-cache'),
                'dbcache.file.gc' => __('Garbage collection interval:', 'w3-total-cache'),
                'dbcache.reject.uri' => __('Never cache the following pages:', 'w3-total-cache'),
                'dbcache.reject.sql' => __('Ignored query stems:', 'w3-total-cache'),
                'dbcache.reject.words' => __('Reject query words:', 'w3-total-cache')
    ));
    }
}