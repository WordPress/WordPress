<?php

class W3_ModuleStatus {

    private $_opcode_engines = array(
                                    'apc',
                                    'eaccelerator',
                                    'xcache',
                                    'wincache'
                                    );
    private $_file_engines = array(
                                    'file',
                                    'file_generic'
                                    );
    /**
     * @var W3_Config
     */
    private $_config;
    public function __construct() {
        $this->_config = w3_instance('W3_Config');
    }

    /**
     * @return bool
     */
    public function plugin_is_enabled() {
        return $this->is_enabled('pgcache')
               || $this->is_enabled('minify')
               || $this->is_enabled('dbcache')
               || $this->is_enabled('objectcache')
               || $this->is_enabled('browsercache')
               || $this->is_enabled('cdn')
               || $this->is_enabled('varnish')
               || $this->is_enabled('newrelic')
               || $this->is_enabled('fragmentcache');
    }

    /**
     * @param $module
     * @return bool
     */
    public function is_enabled($module) {
        return $this->_config->get_boolean("$module.enabled");
    }

    /**
     * Verifies that the module is actually running and not only enabled.
     * @param $module
     * @return mixed|void
     */
    public function is_running($module) {
        return apply_filters("w3tc_module_is_running-{$module}", $this->is_enabled($module));
    }

    /**
     * @return bool
     */
    public function all_modules_enabled() {
        return $this->is_enabled('pgcache')
            && $this->is_enabled('minify')
            && $this->is_enabled('dbcache')
            && $this->is_enabled('objectcache')
            && $this->is_enabled('browsercache')
            && $this->is_enabled('cdn')
            && $this->is_enabled('varnish')
            && $this->is_enabled('fragmentcache');
    }

    /**
     * @return bool
     */
    public function can_empty_memcache() {
        return $this->_enabled_module_uses_engine('pgcache', 'memcached')
                || $this->_enabled_module_uses_engine('dbcache', 'memcached')
                || $this->_enabled_module_uses_engine('objectcache', 'memcached')
                || $this->_enabled_module_uses_engine('minify', 'memcached')
                || $this->_enabled_module_uses_engine('fragmentcache', 'memcached');
    }

    /**
     * @return bool
     */
    public function can_empty_opcode() {
        return $this->_enabled_module_uses_engine('pgcache', $this->_opcode_engines)
                || $this->_enabled_module_uses_engine('dbcache', $this->_opcode_engines)
                || $this->_enabled_module_uses_engine('objectcache', $this->_opcode_engines)
                || $this->_enabled_module_uses_engine('minify', $this->_opcode_engines)
                || $this->_enabled_module_uses_engine('fragmentcache', $this->_opcode_engines);
    }

    /**
     * @return bool
     */
    public function can_empty_file() {
        return $this->_enabled_module_uses_engine('pgcache', $this->_file_engines)
                || $this->_enabled_module_uses_engine('dbcache', $this->_file_engines)
                || $this->_enabled_module_uses_engine('objectcache', $this->_file_engines)
                || $this->_enabled_module_uses_engine('minify', $this->_file_engines)
                || $this->_enabled_module_uses_engine('fragmentcache', $this->_file_engines);
    }

    /**
     * @return bool
     */
    public function can_empty_apc_system() {
        return function_exists('apc_clear_cache') && ini_get('apc.stat') == '0';
    }

    /**
     * @return mixed
     */
    public function can_empty_varnish() {
        return $this->_config->get_boolean('varnish.enabled');
    }

    /**
     * @param $module
     * @return mixed
     */
    public function get_module_engine($module) {
        return $this->_config->get_string("$module.engine");
    }

    private function _enabled_module_uses_engine($module, $engine) {
        if (is_array($engine))
            return $this->is_enabled($module) && in_array($this->get_module_engine($module), $engine);
        else
            return $this->is_enabled($module) && $this->get_module_engine($module) == $engine;
    }
}
