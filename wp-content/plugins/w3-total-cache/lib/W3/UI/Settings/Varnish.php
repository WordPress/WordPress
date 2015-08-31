<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_Varnish extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'varnish.enabled' => __('Enable varnish cache purging', 'w3-total-cache'),
                'varnish.debug' => __('Reverse Proxy', 'w3-total-cache'),
                'varnish.servers' => __('Varnish servers:', 'w3-total-cache')
            ),
            'settings' => array());
    }
}