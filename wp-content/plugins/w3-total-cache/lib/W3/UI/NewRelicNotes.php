<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cyonite
 * Date: 6/24/13
 * Time: 11:30 AM
 * To change this template use File | Settings | File Templates.
 */

class W3_UI_NewRelicNotes {
    /**
     * @param W3_Config $config
     * @param W3_ConfigAdmin $config_admin
     * @return string
     */
    function notifications($config) {
        /**
         * @var $nerser W3_NewRelicService
         */
        $nerser = w3_instance('W3_NewRelicService');

        try {
            $pl = $nerser->get_frontend_response_time();

            if ($pl>0.3) {
                $nr_recommends = array();
                if (!$config->get_boolean('pgcache.enabled'))
                    $nr_recommends[] = __('Page Cache', 'w3-total-cache');
                if (!$config->get_boolean('minify.enabled'))
                    $nr_recommends[] = __('Minify', 'w3-total-cache');
                if (!$config->get_boolean('cdn.enabled'))
                    $nr_recommends[] = __('CDN', 'w3-total-cache');
                if (!$config->get_boolean('browsercache.enabled'))
                    $nr_recommends[] = __('Browser Cache and use compression', 'w3-total-cache');
                if ($nr_recommends) {
                    $message =  sprintf(__('Application monitoring has detected that your page load time is
                                                       higher than 300ms. It is recommended that you enable the following
                                                       features: %s %s', 'w3-total-cache')
                        , implode(', ', $nr_recommends)
                        , w3_button_hide_note('Hide this message', 'new_relic_page_load_notification', '', true)
                    );
                    return $message;
                }
            }
        }catch(Exception $ex){}
        return '';
    }
}