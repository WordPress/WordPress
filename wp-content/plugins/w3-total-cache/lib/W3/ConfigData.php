<?php

/**
 * Class W3_ConfigData
 */
class W3_ConfigData {
    /*
     * Normalized data
     * @var array
     */
    public $data = array();

    /*
     * Array of config keys descriptors. In a format of
     * <key> => array('type' => <key type>, 'default' => <default value>)
     * 
     * @var array
     */
    private $_keys;

    /*
     * Maps http key to options key.
     * Fixes problem when php replaces 'my.super_option' to 'my_super_option'
     * <http name> => <config name>
     * 
     * @var array
     */
    private $_http_keys_map;
    
    /**
     * Constructor
     */
    function __construct($keys) {
        $this->data = array('version' => W3TC_VERSION);
        $this->_keys = $keys;
        
        $this->_http_keys_map = array();
        foreach (array_keys($keys) as $key) {
            $http_key = str_replace('.', '_', $key);
            $this->_http_keys_map[$http_key] = $key;
            // add also non-escaped key
            $this->_http_keys_map[$key] = $key;
        }
    }
    
    /*
     * Converts configuration key returned in http _GET/_POST
     * to configuration key
     * 
     * @param $http_key string
     * @return string
     */
    function resolve_http_key($http_key) {
        if (!isset($this->_http_keys_map[$http_key]))
            return null;
        
        return $this->_http_keys_map[$http_key];
    }
    
    /*
     * Removes data
     */
    function clear() {
      $this->data = array('version' => W3TC_VERSION);
    }

    /**
     * Sets config value
     *
     * @param string $key
     * @param string $value
     * @return mixed value set
     */
    function set($key, $value) {
        if (!array_key_exists($key, $this->_keys))
            return null;
        
        $type = $this->_keys[$key]['type'];
        if (!($type == 'array' && is_string($value)))
            settype($value, $type);
        else {
            $value = str_replace("\r\n", "\n", $value);
            $value = explode("\n", $value);
        }

        
        $this->data[$key] = $value;

        return $value;
    }
    
    /**
     * Sets default values
     */
    function set_defaults() {
        foreach ($this->_keys as $key => $value)
            $this->data[$key] = $value['default'];
    }

    /**
     * Sets group of keys
     * 
     * @param $data array
     */
    function set_group($data) {
        foreach ($data as $key => $value)
            $this->set($key, $value);
    }

    /**
     * Reads config from file and returns it's content as array (or null)
     *
     * @param string $filename
     * @param bool $unserialize
     * @return array or null
     */
    static function get_array_from_file($filename, $unserialize = false) {

        if (file_exists($filename) && is_readable($filename)) {
            // include errors not hidden by @ since they still terminate
            // process (code not functional), but hides reason why
            if ($unserialize) {
                $content = file_get_contents($filename);
                $content = substr($content, 13);
                $config = @unserialize($content);
                if (!$config)
                    return null;
            } else {
                /** @var $filename array */
                // including file directly instead of read+eval causes constant
                // problems with APC, ZendCache, and WSOD in a case of
                // broken config file, still doesnt affect runtime since 
                // config cache is used
                $content = @file_get_contents($filename);
                $config = @eval(substr($content, 5));
            }

            if (is_array($config)) {
                return $config;
            }
        }

        return null;
    }

    /**
     * Reads config from file using "set" method to fill object with data.
     *
     * @param string $filename
     * @param bool $unserialize
     * @return boolean
     */
    function read($filename, $unserialize = false) {
        $config = W3_ConfigData::get_array_from_file($filename, $unserialize);
        if (is_null($config))
            return false;
        
        foreach ($config as $key => $value)
            $this->set($key, $value);

        return true;
    }

    /**
     * Saves modified config
     */
    function write($filename, $serialize = false) {
        w3_require_once(W3TC_INC_DIR . '/functions/file.php');
        if ($serialize) {
            $config = '<?php exit;?>' . serialize($this->data);
        } else {
            $config = w3tc_format_data_as_settings_file($this->data);
        }
        w3_file_put_contents_atomic($filename, $config);
    }
}
