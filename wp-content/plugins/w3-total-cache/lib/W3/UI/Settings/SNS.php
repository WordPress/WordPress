<?php
if (!defined('W3TC')) { die(); }

class W3_UI_Settings_SNS extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'cluster.messagebus.enabled' => __('Manage the cache purge queue via <acronym title="Simple Notification Service">SNS</acronym>', 'w3-total-cache'),
                'cluster.messagebus.sns.region' => __('SNS region:', 'w3-total-cache'),
                'cluster.messagebus.sns.api_key' => __('<acronym title="Application Programming Interface">API</acronym> key:', 'w3-total-cache'),
                'cluster.messagebus.sns.api_secret' => __('<acronym title="Application Programming Interface">API</acronym> secret:', 'w3-total-cache'),
                'cluster.messagebus.sns.topic_arn' => __('Topic <acronym title="Identification">ID</acronym>:', 'w3-total-cache'),
                'cluster.messagebus.debug' =>  __('Amazon <acronym title="Simple Notification Service">SNS</acronym>', 'w3-total-cache')
            ),
            'settings' => array(

            )
        );
    }
}