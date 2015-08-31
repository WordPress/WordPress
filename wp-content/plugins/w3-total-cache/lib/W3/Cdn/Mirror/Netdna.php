<?php

/**
 * W3 CDN Netdna Class
 */
if (!defined('ABSPATH')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Cdn/Mirror.php');

define('W3TC_CDN_NETDNA_URL', 'netdna-cdn.com');

/**
 * Class W3_Cdn_Mirror_Netdna
 */
class W3_Cdn_Mirror_Netdna extends W3_Cdn_Mirror {
    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $config = array_merge(array(
            'authorization_key' => '',
            'alias' => '',
            'consumerkey' => '',
            'consumersecret' => '',
            'zone_id' => 0
        ), $config);
        $split_keys = explode('+', $config['authorization_key']);
        if (sizeof($split_keys)==3)
            list($config['alias'], $config['consumerkey'], $config['consumersecret']) = $split_keys;
        parent::__construct($config);
    }

    /**
     * Purges remote files
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function purge($files, &$results) {
        if (empty($this->_config['authorization_key'])) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, __('Empty Authorization Key.', 'w3-total-cache'));

            return false;
        }

        if (empty($this->_config['alias']) || empty($this->_config['consumerkey']) || empty($this->_config['consumersecret'])) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, __('Malformed Authorization Key.', 'w3-total-cache'));

            return false;
        }


        if (!class_exists('NetDNA')) {
            w3_require_once(W3TC_LIB_NETDNA_DIR . '/NetDNA.php');
        }

        $api = new NetDNA($this->_config['alias'], $this->_config['consumerkey'], $this->_config['consumersecret']);

        $results = array();
        $local_path = $remote_path = '';
        $domain_is_valid = 0;
        $found_domain = false;
        try {
            if ($this->_config['zone_id'] != 0)
                $zone_id = $this->_config['zone_id'];
            else {
                $zone_id = $api->get_zone_id(w3_get_home_url());
            }

            if ($zone_id == 0) {
                $zone_id = $api->get_zone_id(w3_get_domain_url());
            }

            if ($zone_id == 0) {
                $zone_id = $api->get_zone_id(str_replace('://', '://www.', w3_get_domain_url()));
            }

            if ($zone_id == 0 || is_null($zone_id)) {
                if (w3_get_domain_url() == w3_get_home_url())
                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('No zones match site: %s.', 'w3-total-cache'), trim(w3_get_home_url(), '/')));
                else
                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('No zones match site: %s or %s.', 'w3-total-cache'), trim(w3_get_home_url(), '/'), trim(w3_get_domain_url(), '/')));
                return !$this->_is_error($results);
            }

            $pullzone =  json_decode($api->get('/zones/pull.json/' . $zone_id));

            try {
                if (preg_match("(200|201)", $pullzone->code)) {
                    $custom_domains_full = json_decode($api->get('/zones/pull/' . $pullzone->data->pullzone->id . '/customdomains.json'));
                    $custom_domains = array();
                    foreach ($custom_domains_full->data->customdomains as $custom_domain_full) {
                        $custom_domains[]= $custom_domain_full->custom_domain;
                    }

                    foreach ($files as $file) {
                        $local_path = $file['local_path'];
                        $remote_path = $file['remote_path'];

                        $domain_is_valid = 0;
                        $found_domain = false;
                        if ($pullzone->data->pullzone->name . '.' . $this->_config['alias'] . '.' . W3TC_CDN_NETDNA_URL === $this->get_domain($local_path)
                            || in_array($this->get_domain($local_path), $custom_domains)) {
                            try {
                                $params = array('file' => '/' . $local_path);

                                $file_purge = json_decode($api->delete('/zones/pull.json/' . $pullzone->data->pullzone->id . '/cache', $params));

                                if(preg_match("(200|201)", $file_purge->code)) {
                                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
                                } else {
                                    if(preg_match("(401|500)", $file_purge->code)) {
                                        $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('Failed with error code %s Please check your alias, consumer key, and private key.', 'w3-total-cache'), $file_purge->code));
                                    } else {
                                        $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, __('Failed with error code ', 'w3-total-cache') . $file_purge->code);
                                    }
                                }

                                $found_domain = true;
                            } catch (W3tcWpHttpException $e) {
                                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_HALT, sprintf(__('Unable to purge (%s).', 'w3-total-cache'), $e->getMessage()));
                            }
                        } else {
                            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('No registered CNAMEs match %s.', 'w3-total-cache'), $this->get_domain($local_path)));
                            $domain_is_valid++;
                            break;
                        }
                    }
                } else {
                    if (preg_match("(401|500)", $pullzone->code)) {
                        $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('Failed with error code %s. Please check your alias, consumer key, and private key.', 'w3-total-cache'), $pullzone->code));
                    } else {
                        $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, __('Failed with error code ', 'w3-total-cache') . $pullzone->code);
                    }
                }
            } catch (W3tcWpHttpException $e) {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_HALT, sprintf(__('Unable to purge (%s).', 'w3-total-cache'), $e->getMessage()));
            }

        } catch (W3tcWpHttpException $e) {
            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_HALT, __('Failure to pull zone: ', 'w3-total-cache') . $e->getMessage());
        } 

        if ($domain_is_valid > 0 && !$found_domain) {
            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, __('No zones match custom domain.', 'w3-total-cache'));
        } elseif (!$found_domain) {
            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('No zones match site: %s.', 'w3-total-cache'), trim(w3_get_home_url(), '/')));
        }

        return !$this->_is_error($results);
    }

    /**
     * Purge CDN completely
     * @param $results
     * @return bool
     */
    function purge_all(&$results) {
        if (empty($this->_config['authorization_key'])) {
            $results = $this->_get_results(array(), W3TC_CDN_RESULT_HALT,  __('Empty Authorization Key.', 'w3-total-cache'));

            return false;
        }

        if (empty($this->_config['alias']) || empty($this->_config['consumerkey']) || empty($this->_config['consumersecret'])) {
            $results = $this->_get_results(array(), W3TC_CDN_RESULT_HALT,  __('Malformed Authorization Key.', 'w3-total-cache'));

            return false;
        }

        if (!class_exists('NetDNA')) {
            w3_require_once(W3TC_LIB_NETDNA_DIR . '/NetDNA.php');
        }

        $api = new NetDNA($this->_config['alias'], $this->_config['consumerkey'], $this->_config['consumersecret']);

        $results = array();
        $local_path = $remote_path = '';
        $domain_is_valid = 0;
        $found_domain = false;

        try {
            if ($this->_config['zone_id'] != 0)
                $zone_id = $this->_config['zone_id'];
            else {
                $zone_id = $api->get_zone_id(w3_get_home_url());
            }

            if ($zone_id == 0) {
                $zone_id = $api->get_zone_id(w3_get_domain_url());
            }


            if ($zone_id == 0) {
                $zone_id = $api->get_zone_id(str_replace('://', '://www.', w3_get_domain_url()));
            }

            if ($zone_id == 0 || is_null($zone_id)) {
                if (w3_get_domain_url() == w3_get_home_url())
                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('No zones match site: %s.', 'w3-total-cache'), trim(w3_get_home_url(), '/')));
                else
                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('No zones match site: %s or %s.', 'w3-total-cache'), trim(w3_get_home_url(), '/'), trim(w3_get_domain_url(), '/')));
                return !$this->_is_error($results);
            }

            $pullzone =  json_decode($api->get('/zones/pull.json/' . $zone_id));

            try {
                if (preg_match("(200|201)", $pullzone->code)) {
                    $custom_domains_full = json_decode($api->get('/zones/pull/' . $pullzone->data->pullzone->id . '/customdomains.json'));
                    $custom_domains = array();
                    foreach ($custom_domains_full->data->customdomains as $custom_domain_full) {
                        $custom_domains[]= $custom_domain_full->custom_domain;
                    }

                    $local_path = 'all';
                    $remote_path = 'all';

                    $domain_is_valid = 0;
                    $found_domain = false;
                    if ($pullzone->data->pullzone->name . '.' . $this->_config['alias'] . '.' . W3TC_CDN_NETDNA_URL === $this->get_domain($local_path)
                        || in_array($this->get_domain($local_path), $custom_domains)) {
                        try {
                            $params = array('file' => '/' . $local_path);

                            $file_purge = json_decode($api->delete('/zones/pull.json/' . $pullzone->data->pullzone->id . '/cache'));

                            if(preg_match("(200|201)", $file_purge->code)) {
                                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, __('OK', 'w3-total-cache'));
                            } else {
                                if(preg_match("(401|500)", $file_purge->code)) {
                                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('Failed with error code %s. Please check your alias, consumer key, and private key.', 'w3-total-cache'), $file_purge->code));
                                } else {
                                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, __('Failed with error code ', 'w3-total-cache') . $file_purge->code);
                                }
                            }

                            $found_domain = true;
                        } catch (W3tcWpHttpException $e) {
                            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_HALT, sprintf(__('Unable to purge (%s).', 'w3-total-cache'), $e->getMessage()));
                        }
                    } else {
                        $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('No registered CNAMEs match %s.', 'w3-total-cache'), $this->get_domain($local_path)));
                        return !$this->_is_error($results);
                    }
                } else {
                    if (preg_match("(401|500)", $pullzone->code)) {
                        $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('Failed with error code %s. Please check your alias, consumer key, and private key.', 'w3-total-cache'), $pullzone->code));
                    } else {
                        $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, __('Failed with error code ', 'w3-total-cache') . $pullzone->code);
                    }
                }
            } catch (W3tcWpHttpException $e) {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_HALT, sprintf(__('Unable to purge (%s).', 'w3-total-cache'), $e->getMessage()));
            }

        } catch (W3tcWpHttpException $e) {
            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_HALT, __('Failure to pull zone: ', 'w3-total-cache') . $e->getMessage());
        }

        if ($domain_is_valid > 0 && !$found_domain) {
            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, __('No zones match custom domain.', 'w3-total-cache'));
        } elseif (!$found_domain) {
            $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf(__('No zones match site: %s.', 'w3-total-cache'), trim(w3_get_home_url(), '/')));
        }

        return !$this->_is_error($results);
    }

    /**
     * If the CDN supports fullpage mirroring
     * @return bool
     */
    function supports_full_page_mirroring() {
        return false;
    }
}
