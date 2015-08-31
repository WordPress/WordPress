<?php
if (!defined('W3TC')) { die(); }

class CloudFlareSettings extends W3_UI_Settings_SettingsBase{
    protected  function strings() {
        return array(
            'general' => array(
                'cloudflare.enabled' => __('CloudFlare:', 'w3-total-cache'),
                'cloudflare.email' => __('CloudFlare account email:', 'w3-total-cache'),
                'cloudflare.key' => __('<abbr title="Application Programming Interface">API</abbr> key:', 'w3-total-cache'),
                'cloudflare.zone' => __('Domain:', 'w3-total-cache'),

            ),
            'settings' => array(
                'cloudflare.ips.ip4' => __('Cloudflare <abbr title="Internet Protocol">IPs</abbr> <abbr title="Internet Protocol version 4">IP4</abbr> addresses', 'w3-total-cache'),
                'cloudflare.ips.ip6' => __('Cloudflare <abbr title="Internet Protocol">IPs</abbr> <abbr title="Internet Protocol version 6">IP6</abbr> addresses', 'w3-total-cache')
            )
        );
    }
}
