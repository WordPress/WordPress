<?php

/**
 * Rackspace Cloud Files CDN engine
 */
if (!defined('ABSPATH')) {
    die();
}

w3_require_once(W3TC_LIB_W3_DIR . '/Cdn/Base.php');
w3_require_once(W3TC_LIB_CF_DIR . '/cloudfiles.php');

/**
 * Class W3_Cdn_Rscf
 */
class W3_Cdn_Rscf extends W3_Cdn_Base {
    /**
     * Auth object
     *
     * @var CF_Authentication
     */
    var $_auth = null;

    /**
     * Connection object
     *
     * @var CF_Connection
     */
    var $_connection = null;

    /**
     * Container object
     *
     * @var CF_Container
     */
    var $_container = null;

    /**
     * PHP5 Constructor
     *
     * @param array $config
     */
    function __construct($config = array()) {
        $config = array_merge(array(
            'user' => '',
            'key' => '',
            'location' => 'us',
            'container' => '',
            'cname' => array(),
        ), $config);

        parent::__construct($config);
    }

    /**
     * Init connection object
     *
     * @param string $error
     * @return boolean
     */
    function _init(&$error) {
        if (empty($this->_config['user'])) {
            $error = 'Empty username.';

            return false;
        }

        if (empty($this->_config['key'])) {
            $error = 'Empty API key.';

            return false;
        }

        if (empty($this->_config['location'])) {
            $error = 'Empty API key.';

            return false;
        }

        switch ($this->_config['location']) {
            default:
            case 'us':
                $host = US_AUTHURL;
                break;

            case 'uk':
                $host = UK_AUTHURL;
                break;
        }

        try {
            $this->_auth = new CF_Authentication($this->_config['user'], $this->_config['key'], null, $host);
            $this->_auth->ssl_use_cabundle();
            $this->_auth->authenticate();

            $this->_connection = new CF_Connection($this->_auth);
            $this->_connection->ssl_use_cabundle();
        } catch (Exception $exception) {
            $error = $exception->getMessage();

            return false;
        }

        return true;
    }

    /**
     * Init container object
     *
     * @param string $error
     * @return boolean
     */
    function _init_container(&$error) {
        if (empty($this->_config['container'])) {
            $error = 'Empty container.';

            return false;
        }

        try {
            $this->_container = $this->_connection->get_container($this->_config['container']);
        } catch (Exception $exception) {
            $error = $exception->getMessage();

            return false;
        }

        return true;
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
     * Uploads files to CDN
     *
     * @param array $files
     * @param array $results
     * @param boolean $force_rewrite
     * @return boolean
     */
    function upload($files, &$results, $force_rewrite = false) {
        $error = null;

        if (!$this->_init($error) || !$this->_init_container($error)) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, $error);

            return false;
        }

        foreach ($files as $file) {
            $local_path = $file['local_path'];
            $remote_path = $file['remote_path'];

            if (!file_exists($local_path)) {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, 'Source file not found.');

                continue;
            }

            try {
                $object = new CF_Object($this->_container, $remote_path, false, false);
            } catch (Exception $exception) {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to create object (%s).', $exception->getMessage()));

                continue;
            }

            if (!$force_rewrite) {
                try {
                    list($status, $reason, $etag, $last_modified, $content_type, $content_length, $metadata) = $this->_container->cfs_http->head_object($object);
                } catch (Exception $exception) {
                    $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to get object info (%s).', $exception->getMessage()));

                    continue;
                }

                if ($status >= 200 && $status < 300) {
                    $hash = @md5_file($local_path);

                    if ($hash === $etag) {
                        $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'Object up-to-date.');

                        continue;
                    }
                }
            }

            try {
                $object->load_from_filename($local_path);
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
            } catch (Exception $exception) {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to write object (%s).', $exception->getMessage()));
            }
        }

        return !$this->_is_error($results);
    }

    /**
     * Deletes files from CDN
     *
     * @param array $files
     * @param array $results
     * @return boolean
     */
    function delete($files, &$results) {
        $error = null;

        if (!$this->_init($error) || !$this->_init_container($error)) {
            $results = $this->_get_results($files, W3TC_CDN_RESULT_HALT, $error);

            return false;
        }

        foreach ($files as $file) {
            $local_path = $file['local_path'];
            $remote_path = $file['remote_path'];

            try {
                $this->_container->delete_object($remote_path);
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_OK, 'OK');
            } catch (Exception $exception) {
                $results[] = $this->_get_result($local_path, $remote_path, W3TC_CDN_RESULT_ERROR, sprintf('Unable to delete object (%s).', $exception->getMessage()));
            }
        }

        return !$this->_is_error($results);
    }

    /**
     * Test CDN connection
     *
     * @param string $error
     * @return boolean
     */
    function test(&$error) {
        if (!parent::test($error)) {
            return false;
        }

        if (!$this->_init($error) || !$this->_init_container($error)) {
            return false;
        }

        $string = 'test_rscf_' . md5(time());

        try {
            $object = $this->_container->create_object($string);
            $object->content_type = 'text/plain';
            $object->write($string, strlen($string));
        } catch (Exception $exception) {
            $error = sprintf('Unable to write object (%s).', $exception->getMessage());

            return false;
        }

        try {
            $object = $this->_container->get_object($string);
            $data = $object->read();
        } catch (Exception $exception) {
            $error = sprintf('Unable to read object (%s).', $exception->getMessage());

            try {
                $this->_container->delete_object($string);
            } catch (Exception $exception) {
            }

            return false;
        }

        if ($data != $string) {
            $error = 'Objects are not equal.';

            try {
                $this->_container->delete_object($string);
            } catch (Exception $exception) {
            }

            return false;
        }

        try {
            $this->_container->delete_object($string);
        } catch (Exception $exception) {
            $error = sprintf('Unable to delete object (%s).', $exception->getMessage());

            return false;
        }

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
        }

        return array();
    }

    /**
     * Returns VIA string
     *
     * @return string
     */
    function get_via() {
        return sprintf('Rackspace Cloud Files: %s', parent::get_via());
    }

    /**
     * Creates container
     *
     * @param string $container_id
     * @param string $error
     * @return boolean
     */
    function create_container(&$container_id, &$error) {
        if (!$this->_init($error)) {
            return false;
        }

        try {
            $containers = $this->_connection->list_containers();
        } catch (Exception $exception) {
            $error = sprintf('Unable to list containers (%s).', $exception->getMessage());

            return false;
        }

        if (in_array($this->_config['container'], (array) $containers)) {
            $error = sprintf('Container already exists: %s.', $this->_config['container']);

            return false;
        }

        try {
            $container = $this->_connection->create_container($this->_config['container']);
            $container->make_public();
        } catch (Exception $exception) {
            $error = sprintf('Unable to create container: %s (%s).', $this->_config['container'], $exception->getMessage());

            return false;
        }

        $matches = null;

        if (preg_match('~^https?://(.+)$~', $container->cdn_uri, $matches)) {
            $container_id = $matches[1];
        }

        return true;
    }
}
