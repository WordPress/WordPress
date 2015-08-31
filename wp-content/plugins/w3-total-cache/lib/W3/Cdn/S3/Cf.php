<?php

/**
 * Amazon CloudFront CDN engine
 */
if (!defined('ABSPATH')) {
    die();
}

define('W3TC_CDN_CF_TYPE_S3', 's3');
define('W3TC_CDN_CF_TYPE_CUSTOM', 'custom');

w3_require_once(W3TC_LIB_W3_DIR . '/Cdn/S3.php');

/**
 * Class W3_Cdn_S3_Cf
 */
class W3_Cdn_S3_Cf extends W3_Cdn_S3 {
    /**
     * Type
     *
     * @var string
     */
    var $type = '';

    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $config = array_merge(array(
            'id' => ''
        ), $config);

        parent::__construct($config);
    }

    /**
     * Initializes S3 object
     *
     * @param string $error
     * @return bool
     */
    function _init(&$error) {
        if (empty($this->type)) {
            $error = 'Empty type.';

            return false;
        } elseif (!in_array($this->type, array(W3TC_CDN_CF_TYPE_S3, W3TC_CDN_CF_TYPE_CUSTOM))) {
            $error = 'Invalid type.';

            return false;
        }

        if (empty($this->_config['key'])) {
            $error = 'Empty access key.';

            return false;
        }

        if (empty($this->_config['secret'])) {
            $error = 'Empty secret key.';

            return false;
        }

        if ($this->type == W3TC_CDN_CF_TYPE_S3 && empty($this->_config['bucket'])) {
            $error = 'Empty bucket.';

            return false;
        }

        $this->_s3 = new S3($this->_config['key'], $this->_config['secret'], false);

        return true;
    }

    /**
     * Returns origin
     *
     * @return string
     */
    function _get_origin() {
        if ($this->type == W3TC_CDN_CF_TYPE_S3) {
            $origin = sprintf('%s.s3.amazonaws.com', $this->_config['bucket']);
        } else {
            $origin = w3_get_host();
        }

        return $origin;
    }

    /**
     * Upload files
     *
     * @param array $files
     * @param array $results
     * @param boolean $force_rewrite
     * @return boolean
     */
    function upload($files, &$results, $force_rewrite = false) {
        if ($this->type == W3TC_CDN_CF_TYPE_S3) {
            return parent::upload($files, $results, $force_rewrite);
        } else {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_OK, 'OK');

            return true;
        }
    }

    /**
     * Delete files from CDN
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function delete($files, &$results) {
        if ($this->type == W3TC_CDN_CF_TYPE_S3) {
            return parent::delete($files, $results);
        } else {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_OK, 'OK');

            return true;
        }
    }

    /**
     * Purge files from CDN
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function purge($files, &$results) {
        if (parent::purge($files, $results)) {
            return $this->invalidate($files, $results);
        }

        return false;
    }

    /**
     * Invalidates files
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function invalidate($files, &$results) {
        if (!$this->_init($error)) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, $error);

            return false;
        }

        $this->_set_error_handler();
        $dists = @$this->_s3->listDistributions();
        $this->_restore_error_handler();

        if ($dists === false) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, sprintf('Unable to list distributions (%s).', $this->_get_last_error()));

            return false;
        }

        if (!count($dists)) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, 'No distributions found.');

            return false;
        }

        $dist = false;
        $origin = $this->_get_origin();

        foreach ((array) $dists as $_dist) {
            if (isset($_dist['origin']) && $_dist['origin'] == $origin) {
                $dist = $_dist;
                break;
            }
        }

        if (!$dist) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, sprintf('Distribution for origin "%s" not found.', $origin));

            return false;
        }

        $paths = array();

        foreach ($files as $file) {
            $remote_file = $file['remote_path'];
            $paths[] = '/' . $remote_file;
        }

        $this->_set_error_handler();
        $invalidation = @$this->_s3->createInvalidation($dist['id'], $paths);
        $this->_restore_error_handler();

        if (!$invalidation) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, sprintf('Unable to create invalidation bath (%s).', $this->_get_last_error()));

            return false;
        }

        $results = $this->_get_results($files, W3TC_CDN_RESULT_OK, 'OK');

        return true;
    }

    /**
     * Returns array of CDN domains
     *
     * @return array
     */
    function get_domains() {
        if (!empty($this->_config['cname'])) {
            return (array) $this->_config['cname'];
        } elseif (!empty($this->_config['id'])) {
            $domain = sprintf('%s.cloudfront.net', $this->_config['id']);

            return array(
                $domain
            );
        }

        return array();
    }

    /**
     * Tests CF
     *
     * @param string $error
     * @return boolean
     */
    function test(&$error) {
        if ($this->type == W3TC_CDN_CF_TYPE_S3) {
            if (!parent::test($error)) {
                return false;
            }
        } elseif ($this->type == W3TC_CDN_CF_TYPE_CUSTOM) {
            if (!$this->_init($error)) {
                return false;
            }
        }

        /**
         * Search active CF distribution
         */
        $this->_set_error_handler();
        $dists = @$this->_s3->listDistributions();
        $this->_restore_error_handler();

        if ($dists === false) {
            $error = sprintf('Unable to list distributions (%s).', $this->_get_last_error());

            return false;
        }

        if (!count($dists)) {
            $error = 'No distributions found.';

            return false;
        }

        $dist = false;
        $origin = $this->_get_origin();

        foreach ((array) $dists as $_dist) {
            if (isset($_dist['origin']) && $_dist['origin'] == $origin) {
                $dist = $_dist;
                break;
            }
        }

        if (!$dist) {
            $error = sprintf('Distribution for origin "%s" not found.', $origin);

            return false;
        }

        if (!$dist['enabled']) {
            $error = sprintf('Distribution for origin "%s" is disabled.', $origin);

            return false;
        }

        if (!empty($this->_config['cname'])) {
            $domains = (array) $this->_config['cname'];
            $cnames = (isset($dist['cnames']) ? (array) $dist['cnames'] : array());

            foreach ($domains as $domain) {
                $_domains = array_map('trim', explode(',', $domain));

                foreach ($_domains as $_domain) {
                    if (!in_array($_domain, $cnames)) {
                        $error = sprintf('Domain name %s is not in distribution CNAME list.', $_domain);

                        return false;
                    }
                }
            }
        } elseif (!empty($this->_config['id'])) {
            $domain = $this->get_domain();

            if ($domain != $dist['domain']) {
                $error = sprintf('Distribution domain name mismatch (%s != %s).', $domain, $dist['domain']);

                return false;
            }
        }

        return true;
    }

    /**
     * Create bucket
     *
     * @param string $container_id
     * @param string $error
     * @return boolean
     */
    function create_container(&$container_id, &$error) {
        if ($this->type == W3TC_CDN_CF_TYPE_S3) {
            if (!parent::create_container($container_id, $error)) {
                return false;
            }
        } elseif ($this->type == W3TC_CDN_CF_TYPE_CUSTOM) {
            if (!$this->_init($error)) {
                return false;
            }
        }

        $cnames = array();

        if (!empty($this->_config['cname'])) {
            $domains = (array) $this->_config['cname'];

            foreach ($domains as $domain) {
                $_domains = array_map('trim', explode(',', $domain));

                foreach ($_domains as $_domain) {
                    $cnames[] = $_domain;
                }
            }
        }

        $origin = $this->_get_origin();

        $this->_set_error_handler();
        $dist = @$this->_s3->createDistribution($origin, $this->type, true, $cnames);
        $this->_restore_error_handler();

        if (!$dist) {
            $error = sprintf('Unable to create distribution for origin %s (%s).', $origin, $this->_get_last_error());

            return false;
        }

        $matches = null;

        if (preg_match('~^(.+)\.cloudfront\.net$~', $dist['domain'], $matches)) {
            $container_id = $matches[1];
        }

        return true;
    }

    /**
     * Returns via string
     *
     * @return string
     */
    function get_via() {
        $domain = $this->get_domain();

        $via = ($domain ? $domain : 'N/A');

        return sprintf('Amazon Web Services: CloudFront: %s', $via);
    }

    /**
     * Update distribution CNAMEs
     *
     * @param string $error
     * @return boolean
     */
    function update_cnames(&$error) {
        if (!$this->_init($error)) {
            return false;
        }

        $this->_set_error_handler();
        $dists = @$this->_s3->listDistributions();
        $this->_restore_error_handler();

        if ($dists === false) {
            $error = sprintf('Unable to list distributions (%s).', $this->_get_last_error());

            return false;
        }

        $dist_id = false;
        $origin = $this->_get_origin();

        foreach ((array) $dists as $dist) {
            if (isset($dist['origin']) && $dist['origin'] == $origin) {
                $dist_id = $dist['id'];
                break;
            }
        }

        if (!$dist_id) {
            $error = sprintf('Distribution ID for origin "%s" not found.', $origin);

            return false;
        }

        $this->_set_error_handler();
        $dist = @$this->_s3->getDistribution($dist_id);
        $this->_restore_error_handler();

        if (!$dist) {
            $error = sprintf('Unable to get distribution by ID: %s (%s).', $dist_id, $this->_get_last_error());
        }

        $dist['cnames'] = (isset($this->_config['cname']) ? (array) $this->_config['cname'] : array());

        $this->_set_error_handler();
        $dist = @$this->_s3->updateDistribution($dist);
        $this->_restore_error_handler();

        if (!$dist) {
            $error = sprintf('Unable to update distribution: %s (%s).', json_encode($dist), $this->_get_last_error());

            return false;
        }

        return true;
    }
}
