<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_Mobile extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
            ),
            'settings' => array(
                'mobile.enabled' => __('User Agents:', 'w3-total-cache'),
                'mobile.rgroups' => __('User Agent groups', 'w3-total-cache')
            ));
    }
}