<?php

w3_require_once(W3TC_LIB_W3_DIR . '/ConfigBase.php');

/**
 * Class W3_Config
 * Provides configuration data using cache
 */
class W3_Config extends W3_ConfigBase {
    /*
     * blog id of loaded config
     * @var integer
     */
    private $_blog_id;
    
    /*
     * Is this preview config
     * @var boolean
     */
    private $_preview;

    private $_md5 = null;

    /**
     * Constructor
     */
    function __construct($master = false, $blog_id = null) {
        $preview = w3_is_preview_mode();
        if (defined('WP_ADMIN')) {
            $config_admin = w3_instance('W3_ConfigAdmin');
            $preview = $config_admin->get_boolean('previewmode.enabled');
        }
        if ($master)
            $this->_blog_id = 0;
        elseif ($blog_id == null)
            $this->_blog_id = w3_get_blog_id();
        else
            $this->_blog_id = $blog_id;
        $this->_preview = $preview;
        $this->load();
    }

    /**
     * Check if we are in preview mode
     */
    function is_preview() {
        return $this->_preview;
    }

    /**
     * Sets config value
     *
     * @param string $key
     * @param string $value
     * @return mixed value set
     */
    function set($key, $value) {
        $key = $this->_get_writer()->resolve_http_key($key);
        $value = $this->_get_writer()->set($key, $value);
        return parent::set($key, $value);
    }

    /**
     * Sets default values
     */
    function set_defaults() {
        $this->_get_writer()->set_defaults();
        $this->_flush_cache();
    }

    /**
     * Saves modified config
     */
    function save($deprecated = false) {
        $this->_get_writer()->save();
        $this->_flush_cache();
    }

    /**
     * Deploys the config file from a preview config file
     *
     * @param integer $direction +1: preview->production
     *                           -1: production->preview
     * @param boolean $remove_source remove source file
     */
    function preview_production_copy($direction = 1, $remove_source = false) {
        $this->_get_writer()->preview_production_copy($direction, $remove_source);

        $this->_flush_cache(
            ($direction > 0 ? false /* del production */: true /* del preview */));
    }

    /**
     * Checks if own configuration file exists
     *
     * @return bool
     */
    function own_config_exists() {
        return $this->_get_writer()->own_config_exists();
    }
    
    /**
     * Loads config
     * @param bool $generate_admin_cache generates a cached config even in admin
     */
    function load($generate_admin_cache = false) {
        $filename = $this->_get_config_filename();
        // dont use cache in admin - may load some old cache when real config
        // contains different state (even manually edited)
        if (!defined('WP_ADMIN')) {
            $data = $this->_read($filename);
            if (!is_null($data)) {
                $this->_data = $data;
                return;
            }
        }

        $this->_data = $this->_get_writer()->create_compiled_config($filename, $generate_admin_cache || !defined('WP_ADMIN'));
    }

    /**
     * Exports config content
     * 
     * @return string
     */
    function export() {
        $this->refresh_cache(true);
        w3_require_once(W3TC_INC_DIR . '/functions/file.php');
        $content = file_get_contents($this->_get_config_filename());
        $content = substr($content,13);
        $data = unserialize($content);
        $settings = w3tc_format_data_as_settings_file($data);
        return $settings;
    }

    /**
     * Imports config content
     *
     * @param string $filename
     * @return boolean
     */
    function import($filename) {
        if (file_exists($filename) && is_readable($filename)) {
            $data = file_get_contents($filename);
            if (substr($data, 0, 5) == '<?php')
                $data = substr($data, 5);

            $config = eval($data);

            if (is_array($config)) {
                foreach ($config as $key => $value)
                  $this->set($key, $value);

                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if we edit master config
     * @return boolean
     */
    function is_master() {
        return ($this->_blog_id <= 0);
    }

    /**
     * Checks if cache file is actual
     * @throws Exception
     */
    function validate_cache_actual() {
        $filename = $this->_get_config_filename();

        if (!file_exists($filename))
            return;

        $data = $this->_read($filename);
        if (is_null($data))
            throw new Exception('Can\'t read file <strong>' .
                $filename . '</strong>. Remove it manually.');

        $stale_data = array();
        if (false !== $data) {
            foreach ($this->_data as $key => $value) {
                if (!isset($data[$key]) || $value != $data[$key]) {
                    $stale_data[$key] = $value;
                }
            }
        }
        if ($stale_data) {
            set_transient('w3tc_new_settings', $stale_data);
            throw new SelfTestExceptions('Cache file <strong>' .
            $filename . '</strong> can\'t be actualized. ' .
            'Remove it manually.');
        }
    }

    /**
     * Flushes the cache and rebuilds it from scratch
     *
     * @param bool $force_refresh
     * @return void
     */
    function refresh_cache($force_refresh = false) {
        $this->_flush_cache();
        $this->load($force_refresh);
    }

    /**
     * Tries to get cache options which are not filled yet
     * and saves cache
     */
    function fill_missing_cache_options_and_save() {
      if (isset($this->_data['wordpress.home']) || w3_force_master())
          return false;

      $this->refresh_cache();
      return true;
    }

    /**
     * Will get a value from the config cache.
     * Will rebuild the cache in case the option doesn't exist.
     *
     * @param string $option 
     * @return mixed value of the option if it can be found in the (regenerated) cache, null if otherwise
     */
    function get_cache_option($option) {
        $value = null;
        
        // Attempt to get the value
        if (isset($this->_data[$option])) {
            $value = $this->_data[$option];
        } else {
            // Rebuild the cache
            $this->refresh_cache();
            
            // Try again
            $value = $this->_data[$option];

            if (!isset($this->_data[$option])) {
              // true value is a sign to just generate config cache
              $GLOBALS['w3tc_blogmap_register_new_item'] = true;
            }
        }
        
        return $value;
    }

    function get_md5() {
        if (is_null($this->_md5))
            $this->_md5 = substr(md5(serialize($this->_data)), 20);
        return $this->_md5;
    }

    /**
     * Reads config from file
     *
     * @param string $filename
     * @return boolean
     */
    private function _read($filename) {
        if (file_exists($filename) && is_readable($filename)) {
            $content = file_get_contents($filename);
            $content = substr($content,13);
            $config = @unserialize($content);
            if (is_array($config)) {
                if (isset($config['version'])
                        && $config['version'] == W3TC_VERSION) {
                    return $config;
                }
            }
        }
        return null;
    }
    
    private function _flush_cache($forced_preview = null) {
        $this->_md5 = null;
        if ($this->_blog_id > 0)
            @unlink($this->_get_config_filename($forced_preview));
        else {
            // clear whole cache if we change master config
            w3_require_once(W3TC_INC_DIR . '/functions/file.php');
            w3_emptydir(W3TC_CACHE_CONFIG_DIR);
        }
    }

    /*
     * Returns config filename
     * 
     * @return string
     */
    private function _get_config_filename($forced_preview = null, $master = false) {
        $preview = (is_null($forced_preview) ? $this->_preview : $forced_preview);
        $postfix = ($preview ? '-preview' : '') . '.php';

        if ($this->_blog_id <= 0 || w3_force_master() || $master)
            return W3TC_CACHE_CONFIG_DIR . '/master' . $postfix;

        return W3TC_CACHE_CONFIG_DIR . '/' . 
            sprintf('%06d', $this->_blog_id) . $postfix;
    }
    
    /*
     * Returns object able to write config files
     * 
     * @return string
     */
    private function _get_writer() {
        if (!isset($this->_writer)) {
            w3_require_once(W3TC_LIB_W3_DIR . '/ConfigWriter.php');
            $this->_writer = new W3_ConfigWriter($this->_blog_id, $this->_preview);
        }
        
        return $this->_writer;
    }
}
