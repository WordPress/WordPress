<?php

/**
 * Amazon S3 CDN engine
 */
if (!defined('ABSPATH')) {
    die();
}

if (!class_exists('S3')) {
    w3_require_once(W3TC_LIB_DIR . '/S3.php');
}

w3_require_once(W3TC_LIB_W3_DIR . '/Cdn/Base.php');

/**
 * Class W3_Cdn_S3
 */
class W3_Cdn_S3 extends W3_Cdn_Base {
    /**
     * S3 object
     *
     * @var S3
     */
    var $_s3 = null;

    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $config = array_merge(array(
            'key' => '',
            'secret' => '',
            'bucket' => '',
            'cname' => array(),
        ), $config);

        parent::__construct($config);
    }

    /**
     * Formats URL
     *
     * @param string $path
     * @return string
     */
    function _format_url($path) {
        $domain = $this->get_domain($path);

        if ($domain) {
            $scheme = $this->_get_scheme();

            // it does not support '+', requires '%2B'
            $path = str_replace('+', '%2B', $path);
            $url = sprintf('%s://%s/%s', $scheme, $domain, $path);

            return $url;
        }

        return false;
    }

    /**
     * Inits S3 object
     *
     * @param string $error
     * @return boolean
     */
    function _init(&$error) {
        if (empty($this->_config['key'])) {
            $error = 'Empty access key.';

            return false;
        }

        if (empty($this->_config['secret'])) {
            $error = 'Empty secret key.';

            return false;
        }

        if (empty($this->_config['bucket'])) {
            $error = 'Empty bucket.';

            return false;
        }

        $this->_s3 = new S3($this->_config['key'], $this->_config['secret'], false);

        return true;
    }

    /**
     * Uploads files to S3
     *
     * @param array $files
     * @param array $results
     * @param boolean $force_rewrite
     * @return boolean
     */
    function upload($files, &$results, $force_rewrite = false) {
        $error = null;

        if (!$this->_init($error)) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, $error);

            return false;
        }

        foreach ($files as $file) {
            $local_path = $file['local_path'];
            $remote_path = $file['remote_path'];

            $results[] = $this->_upload($file, $force_rewrite);

            if ($this->_config['compression'] && $this->_may_gzip($remote_path)) {
                $file['remote_path_gzip'] = $remote_path . $this->_gzip_extension;
                $results[] = $this->_upload_gzip($file, $force_rewrite);
            }
        }

        return !$this->_is_error($results);
    }

    /**
     * Uploads single file to S3
     *
     * @param array CDN file array
     * @param boolean $force_rewrite
     * @return array
     */
    function _upload($file, $force_rewrite = false) {
        $local_path = $file['local_path'];
        $remote_path = $file['remote_path'];

        if (!file_exists($local_path)) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Source file not found.');
        }

        if (!$force_rewrite) {
            $this->_set_error_handler();
            $info = @$this->_s3->getObjectInfo($this->_config['bucket'], $remote_path);
            $this->_restore_error_handler();

            if ($info) {
                $hash = @md5_file($local_path);
                $s3_hash = (isset($info['hash']) ? $info['hash'] : '');

                if ($hash === $s3_hash) {
                    return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'Object up-to-date.');
                }
            }
        }

        $headers = $this->_get_headers($file);

        $this->_set_error_handler();
        $result = @$this->_s3->putObjectFile($local_path, $this->_config['bucket'], $remote_path, S3::ACL_PUBLIC_READ, array(), $headers);
        $this->_restore_error_handler();

        if ($result) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
        }

        return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to put object (%s).', $this->_get_last_error()));
    }

    /**
     * Uploads gzip version of file
     *
     * @param string $local_path
     * @param string $remote_path
     * @param boolean $force_rewrite
     * @return array
     */
    function _upload_gzip($file, $force_rewrite = false) {
        $local_path = $file['local_path'];
        $remote_path = $file['remote_path_gzip'];

        if (!function_exists('gzencode')) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, "GZIP library doesn't exist.");
        }

        if (!file_exists($local_path)) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Source file not found.');
        }

        $contents = @file_get_contents($local_path);

        if ($contents === false) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Unable to read file.');
        }

        $data = gzencode($contents);

        if (!$force_rewrite) {
            $this->_set_error_handler();
            $info = @$this->_s3->getObjectInfo($this->_config['bucket'], $remote_path);
            $this->_restore_error_handler();

            if ($info) {
                $hash = md5($data);
                $s3_hash = (isset($info['hash']) ? $info['hash'] : '');

                if ($hash === $s3_hash) {
                    return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'Object up-to-date.');
                }
            }
        }

        $headers = $this->_get_headers($file);
        $headers = array_merge($headers, array(
            'Vary' => 'Accept-Encoding',
            'Content-Encoding' => 'gzip'
        ));

        $this->_set_error_handler();
        $result = @$this->_s3->putObjectString($data, $this->_config['bucket'], $remote_path, S3::ACL_PUBLIC_READ, array(), $headers);
        $this->_restore_error_handler();

        if ($result) {
            return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
        }

        return $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to put object (%s).', $this->_get_last_error()));
    }

    /**
     * Deletes files from S3
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function delete($files, &$results) {
        $error = null;

        if (!$this->_init($error)) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, $error);

            return false;
        }

        foreach ($files as $file) {
            $local_path = $file['local_path'];
            $remote_path = $file['remote_path'];

            $this->_set_error_handler();
            $result = @$this->_s3->deleteObject($this->_config['bucket'], $remote_path);
            $this->_restore_error_handler();

            if ($result) {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
            } else {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to delete object (%s).', $this->_get_last_error()));
            }

            if ($this->_config['compression']) {
                $remote_path_gzip = $remote_path . $this->_gzip_extension;

                $this->_set_error_handler();
                $result = @$this->_s3->deleteObject($this->_config['bucket'], $remote_path_gzip);
                $this->_restore_error_handler();

                if ($result) {
                    $results[] = $this->_get_result($local_path, $remote_path_gzip, W3TC_CDN_RESULT_OK, 'OK');
                } else {
                    $results[] = $this->_get_result($local_path, $remote_path_gzip, W3TC_CDN_RESULT_ERROR, sprintf('Unable to delete object (%s).', $this->_get_last_error()));
                }
            }
        }

        return !$this->_is_error($results);
    }

    /**
     * Tests S3
     *
     * @param string $error
     * @return boolean
     */
    function test(&$error) {
        if (!parent::test($error)) {
            return false;
        }

        $string = 'test_s3_' . md5(time());

        if (!$this->_init($error)) {
            return false;
        }

        $this->_set_error_handler();

        $buckets = @$this->_s3->listBuckets();

        if ($buckets === false) {
            $error = sprintf('Unable to list buckets (%s).', $this->_get_last_error());

            $this->_restore_error_handler();

            return false;
        }

        if (!in_array($this->_config['bucket'], (array) $buckets)) {
            $error = sprintf('Bucket doesn\'t exist: %s.', $this->_config['bucket']);

            $this->_restore_error_handler();

            return false;
        }

        if (!@$this->_s3->putObjectString($string, $this->_config['bucket'], $string, S3::ACL_PUBLIC_READ)) {
            $error = sprintf('Unable to put object (%s).', $this->_get_last_error());

            $this->_restore_error_handler();

            return false;
        }

        if (!($object = @$this->_s3->getObject($this->_config['bucket'], $string))) {
            $error = sprintf('Unable to get object (%s).', $this->_get_last_error());

            $this->_restore_error_handler();

            return false;
        }

        if ($object->body != $string) {
            $error = 'Objects are not equal.';

            @$this->_s3->deleteObject($this->_config['bucket'], $string);
            $this->_restore_error_handler();

            return false;
        }

        if (!@$this->_s3->deleteObject($this->_config['bucket'], $string)) {
            $error = sprintf('Unable to delete object (%s).', $this->_get_last_error());

            $this->_restore_error_handler();

            return false;
        }

        $this->_restore_error_handler();

        return true;
    }

    /**
     * Returns CDN domain
     *
     * @return array
     */
    function get_domains() {
        if (!empty($this->_config['cname'])) {
            return (array) $this->_config['cname'];
        } elseif (!empty($this->_config['bucket'])) {
            $domain = sprintf('%s.s3.amazonaws.com', $this->_config['bucket']);

            return array(
                $domain
            );
        }

        return array();
    }

    /**
     * Returns via string
     *
     * @return string
     */
    function get_via() {
        return sprintf('Amazon Web Services: S3: %s', parent::get_via());
    }

    /**
     * Creates bucket
     *
     * @param string $container_id
     * @param string $error
     * @return boolean
     */
    function create_container(&$container_id, &$error) {
        if (!$this->_init($error)) {
            return false;
        }

        $this->_set_error_handler();

        $buckets = @$this->_s3->listBuckets();

        if ($buckets === false) {
            $error = sprintf('Unable to list buckets (%s).', $this->_get_last_error());

            $this->_restore_error_handler();

            return false;
        }

        if (in_array($this->_config['bucket'], (array) $buckets)) {
            $error = sprintf('Bucket already exists: %s.', $this->_config['bucket']);

            $this->_restore_error_handler();

            return false;
        }

        if (empty($this->_config['bucket_acl'])) {
            $this->_config['bucket_acl'] = S3::ACL_PRIVATE;
        }

        if (!isset($this->_config['bucket_location'])) {
            $this->_config['bucket_location'] = S3::LOCATION_US;
        }

        if (!@$this->_s3->putBucket($this->_config['bucket'], $this->_config['bucket_acl'], $this->_config['bucket_location'])) {
            $error = sprintf('Unable to create bucket: %s (%s).', $this->_config['bucket'], $this->_get_last_error());

            $this->_restore_error_handler();

            return false;
        }

        $this->_restore_error_handler();

        return true;
    }

    /**
     * How and if headers should be set
     * @return string W3TC_CDN_HEADER_NONE, W3TC_CDN_HEADER_UPLOADABLE, W3TC_CDN_HEADER_MIRRORING
     */
    function headers_support() {
        return W3TC_CDN_HEADER_UPLOADABLE;
    }
}
