<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_FragmentCache extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'fragmentcache.engine' =>  __('Fragment Cache Method:', 'w3-total-cache'),
                'fragmentcache.enabled' => __('Fragment Cache:', 'w3-total-cache'),
                'fragmentcache.debug' =>  __('Fragment Cache', 'w3-total-cache')
            ),
            'settings' => array(
                'fragmentcache.memcached.servers' => __('Memcached hostname:port / <acronym title="Internet Protocol">IP</acronym>:port:', 'w3-total-cache'),
                'fragmentcache.lifetime' => __('Default lifetime of cached fragments:', 'w3-total-cache'),
                'fragmentcache.file.gc' => __('Garbage collection interval:', 'w3-total-cache'),
                'fragmentcache.groups' => __('Manual fragment groups:', 'w3-total-cache')
        ));
    }
}