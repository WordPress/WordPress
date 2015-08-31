<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_ObjectCache extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'objectcache.engine' =>  __('Object Cache Method:', 'w3-total-cache'),
                'objectcache.enabled' => __('Object Cache:', 'w3-total-cache'),
                'objectcache.debug' =>  __('Object Cache', 'w3-total-cache')
            ),
            'settings' => array(
                'objectcache.memcached.servers' => __('Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:', 'w3-total-cache'),
                'objectcache.lifetime' => __('Default lifetime of cache objects:', 'w3-total-cache'),
                'objectcache.file.gc' => __('Garbage collection interval:', 'w3-total-cache'),
                'objectcache.groups.global' => __('Global groups:', 'w3-total-cache'),
                'objectcache.groups.nonpersistent' => __('Non-persistent groups:', 'w3-total-cache'),
                'objectcache.purge.all' => __('Flush all cache on post, comment etc changes.', 'w3-total-cache')
    ));
    }
}