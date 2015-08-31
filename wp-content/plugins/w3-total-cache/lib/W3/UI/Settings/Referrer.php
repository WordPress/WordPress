<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_Referrer extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
            ),
            'settings' => array(
                'referrer.enabled' => __('Referrers:', 'w3-total-cache'),
                'referrer.rgroups' => __('Referrer groups', 'w3-total-cache')
            ));
    }
}