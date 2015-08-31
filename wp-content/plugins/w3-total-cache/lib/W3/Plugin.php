<?php

/**
 * W3 Plugin base class
 */

/**
 * Class W3_Plugin
 */
class W3_Plugin {
    /**
     * Config
     *
     * @var W3_Config
     */
    var $_config = null;

    /**
     * PHP5 Constructor
     */
    function __construct() {
        $this->_config = w3_instance('W3_Config');
    }

    /**
     * Runs plugin
     */
    function run() {
    }
}
