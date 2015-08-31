<?php

w3_require_once(W3TC_LIB_W3_DIR . '/ConfigBase.php');
w3_require_once(W3TC_LIB_W3_DIR . '/ConfigData.php');

/**
 * Class W3_ConfigAdmin
 * Provides administration configuration data
 */
class W3_ConfigAdmin extends W3_ConfigBase {
    /*
     * blog id of loaded config
     * @var integer
     */
    private $_blog_id;

    /*
     * configuration data as object.
     * Contains data only of current blog, while ConfigAdmin object contains 
     * aggregated data
     * 
     * @var object
     */
    private $_own_config_data_object;

    /*
     * configuration data as object.
     * Contains aggregated data
     * 
     * @var object
     */
    private $_aggregated_data_object;

    /**
     * keys descriptiors
     * 
     * @var array
     */
    private $_keys;
    
    /**
     * Constructor
     */
    function __construct() {
        $this->_blog_id = w3_get_blog_id();
        
        // defines $keys_admin with descriptors
        include W3TC_LIB_W3_DIR . '/ConfigKeys.php';
        $this->_keys = $keys_admin;
        
        $this->_aggregated_data_object = new W3_ConfigData($keys_admin);
        $this->_aggregated_data_object->set_defaults();

        // load master-config data
        $filename1 = $this->_get_config_filename(true);
        $this->_aggregated_data_object->read($filename1);

        // load blog-config data (or master if we are master)
        $filename2 = $this->_get_config_filename();
        $this->_data_object = new W3_ConfigData($keys_admin);
        
        // set defaults 
        $this->_data_object->set_group($this->_aggregated_data_object->data);
        
        $data = W3_ConfigData::get_array_from_file($filename2);
        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                if ($this->_writable_key($key))
                    $this->_data_object->set($key, $value);
            }
        }

        // merge blog-specific config data to aggregated data
        if ($this->_blog_id > 0)
            $this->_aggregated_data_object->set_group($this->_data_object->data);
        
        $this->_data = & $this->_aggregated_data_object->data;
    }

    /**
     * Sets config value
     *
     * @param string $key
     * @param string $value
     * @return value set
     */
    function set($key, $value) {
        $key = $this->_data_object->resolve_http_key($key);
        
        if (!$this->_writable_key($key))
            return null;
        
        $value = $this->_data_object->set($key, $value);
        $this->_aggregated_data_object->set($key, $value);
        
        return $value;
    }
    
    /**
     * Checks if own configuration file exists
     *
     * @return bool
     */
    function own_config_exists() {
        return @file_exists($this->_get_config_filename());
    }

    /**
     * Saves modified config
     */
    function save() {
        $filename = $this->_get_config_filename();

        if (!is_dir(dirname($filename))) {
            w3_require_once(W3TC_INC_DIR . '/functions/file.php');
            w3_mkdir_from(dirname($filename), WP_CONTENT_DIR);
        }

        $this->_data_object->write($filename);
    }

    /**
     * returns if that key can be set for this config
     * 
     * @param string $key
     * @return bool
     */
    private function _writable_key($key) {
        if ($this->_blog_id > 0) {
            if (!isset($this->_keys[$key]))
                return false;
            if (isset($this->_keys[$key]['master_only']) && 
                    $this->_keys[$key]['master_only'])
                return false;
        }

        return true;
    }

    /*
     * Returns config filename
     * 
     * @return string
     */
    private function _get_config_filename($force_master = false) {
        if ($this->_blog_id <= 0 || $force_master || w3_force_master())
            return W3TC_CONFIG_DIR . '/master-admin.php';

        return W3TC_CONFIG_DIR . '/' . 
            sprintf('%06d', $this->_blog_id) . '-admin.php';
    }
}
